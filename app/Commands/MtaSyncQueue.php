<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\MtaSyncService;

class MtaSyncQueue extends BaseCommand
{
    protected $group       = 'MTA';
    protected $name        = 'mta:sync-queue';
    protected $description = 'Proses antrian sinkronisasi data pemuda dengan API MTA Pusat (Laju: 40 data / menit)';
    protected $usage       = 'mta:sync-queue [options]';
    protected $options     = [
        '--cabang'       => 'ID Cabang tertentu untuk disinkronkan',
        '--only-pending' => 'Hanya sinkronkan data yang masih berstatus pending (1/0, default 1)',
        '--init'         => 'Inisialisasi antrian baru sebelum memproses',
    ];

    public function run(array $params)
    {
        $syncService = new MtaSyncService();

        $cabangId    = CLI::getOption('cabang') ? (int) CLI::getOption('cabang') : null;
        $onlyPending = CLI::getOption('only-pending') !== '0';
        $shouldInit  = (bool) CLI::getOption('init');

        CLI::write("==================================================", 'yellow');
        CLI::write(" ANTRIAN SINKRONISASI PEMUDA DENGAN API MTA PUSAT ", 'black', 'light_gray');
        CLI::write(" Batas Laju Aman: 40 data / menit (1.5 detik/item)", 'cyan');
        CLI::write("==================================================", 'yellow');

        if ($shouldInit) {
            CLI::write("Menginisialisasi antrian baru...", 'white');
            $initRes = $syncService->initSyncQueue($cabangId, $onlyPending, null, true);
            if (!$initRes['success']) {
                CLI::error($initRes['message']);
                return;
            }
            CLI::write("Antrian berhasil disiapkan: {$initRes['total']} data. Estimasi: {$initRes['estimated_time']}", 'green');
        }

        $status = $syncService->getQueueStatus();
        $summary = $status['summary'];

        if ($summary['remaining'] === 0) {
            CLI::write("Tidak ada antrian pending. Gunakan opsi --init untuk membuat antrian baru.", 'yellow');
            return;
        }

        CLI::write("Memulai pemrosesan antrian tersisa ({$summary['remaining']} data)...", 'cyan');

        $processedInRun = 0;

        while (true) {
            $result = $syncService->processNextQueueItem();

            if ($result['finished'] ?? false) {
                CLI::newLine();
                CLI::write("SELESAI! " . ($result['message'] ?? ''), 'green');
                $final = $result['summary'];
                CLI::write("Total: {$final['total']} | Terverifikasi: {$final['verified']} | Belum Terverifikasi: {$final['pending_unverified']} | Gagal: {$final['failed']}", 'yellow');
                break;
            }

            if (!empty($result['rate_limited'])) {
                $retryAfter = $result['retry_after'] ?? 10;
                CLI::write("[RATELIMIT 429] Batas kuota tercapai. Menunggu {$retryAfter} detik...", 'light_red');
                sleep($retryAfter);
                continue;
            }

            $item = $result['item'];
            $statusLabel = $item['result'] === 'verified' ? 'TERVERIFIKASI' : ($item['result'] === 'pending' ? 'BELUM TERDATA' : 'GAGAL');
            $color = $item['result'] === 'verified' ? 'green' : ($item['result'] === 'pending' ? 'yellow' : 'red');

            $processedInRun++;
            $sum = $result['summary'];
            CLI::write(sprintf(
                "[%s/%s - %s%%] %s (#%s) [%s]: %s",
                $sum['processed'],
                $sum['total'],
                $sum['percent'],
                $item['name'],
                $item['pemuda_id'],
                $statusLabel,
                $item['message']
            ), $color);

            // Jeda aman: 1.5 detik (40 data / menit)
            usleep(1500000);
        }
    }
}
