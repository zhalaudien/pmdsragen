<?php

namespace App\Models;

use CodeIgniter\Model;

class MtaSyncQueueModel extends Model
{
    protected $table            = 'mta_sync_queue';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pemuda_id',
        'cabang_id',
        'status',
        'result',
        'message',
        'mta_warga_uuid',
        'attempts',
        'created_by',
        'created_at',
        'updated_at',
        'processed_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Dapatkan ringkasan statistik antrian saat ini
     */
    public function getQueueSummary(): array
    {
        $total = $this->countAllResults(false);
        $pending = (clone $this)->where('status', 'pending')->countAllResults(false);
        $processing = (clone $this)->where('status', 'processing')->countAllResults(false);
        $completed = (clone $this)->where('status', 'completed')->countAllResults(false);
        $failed = (clone $this)->where('status', 'failed')->countAllResults(false);

        $verified = (clone $this)->where('result', 'verified')->countAllResults(false);
        $pendingUnverified = (clone $this)->where('status', 'completed')->where('result', 'pending')->countAllResults(false);

        $remaining = $pending + $processing;
        $processed = $completed + $failed;
        $percent = $total > 0 ? round(($processed / $total) * 100, 1) : 0;

        // Estimasi sisa waktu dalam detik berdasarkan 40 data / menit (1.5 detik/data)
        $estimatedSeconds = ceil($remaining * 1.5);
        $minutes = floor($estimatedSeconds / 60);
        $seconds = $estimatedSeconds % 60;
        $estimatedFormatted = $minutes > 0 
            ? "{$minutes} mnt {$seconds} dtk" 
            : "{$seconds} dtk";

        return [
            'total'               => $total,
            'pending'             => $pending,
            'processing'          => $processing,
            'completed'           => $completed,
            'failed'              => $failed,
            'processed'           => $processed,
            'remaining'           => $remaining,
            'verified'            => $verified,
            'pending_unverified'  => $pendingUnverified,
            'percent'             => $percent,
            'rate_per_minute'     => 40,
            'delay_seconds'       => 1.5,
            'delay_ms'            => 1500,
            'estimated_seconds'   => $estimatedSeconds,
            'estimated_formatted' => $estimatedFormatted,
        ];
    }

    /**
     * Ambil item pending berikutnya dalam antrian beserta data pemuda
     */
    public function getNextPendingItem(): ?array
    {
        return $this->select('mta_sync_queue.*, pemuda.name, pemuda.phone, pemuda.birth_date, pemuda.gender, pemuda.mta_warga_uuid as pemuda_mta_uuid, pemuda.status_verifikasi, cabang.name as cabang_name')
                    ->join('pemuda', 'pemuda.id = mta_sync_queue.pemuda_id')
                    ->join('cabang', 'cabang.id = mta_sync_queue.cabang_id')
                    ->where('mta_sync_queue.status', 'pending')
                    ->orderBy('mta_sync_queue.id', 'ASC')
                    ->first();
    }

    /**
     * Ambil log hasil proses terbaru dari antrian
     */
    public function getRecentProcessed(int $limit = 20): array
    {
        return $this->select('mta_sync_queue.*, pemuda.name, pemuda.registration_number, cabang.name as cabang_name')
                    ->join('pemuda', 'pemuda.id = mta_sync_queue.pemuda_id')
                    ->join('cabang', 'cabang.id = mta_sync_queue.cabang_id')
                    ->whereIn('mta_sync_queue.status', ['completed', 'failed'])
                    ->orderBy('mta_sync_queue.processed_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Batalkan / bersihkan seluruh antrian yang masih pending atau processing
     */
    public function clearPendingQueue(): int
    {
        return $this->whereIn('status', ['pending', 'processing'])->delete();
    }

    /**
     * Reset semua item yang gagal/terhenti agar bisa diproses ulang
     */
    public function resetFailedQueue(): int
    {
        return $this->whereIn('status', ['failed', 'processing'])
                    ->set(['status' => 'pending', 'attempts' => 0])
                    ->update();
    }
}
