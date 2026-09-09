<?php

namespace App\Services;

use App\Models\PemudaModel;
use App\Models\UserModel;
use Config\Database;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PemudaBackupService
{
    protected $db;
    protected UserModel $userModel;
    protected string $backupDir;

    public function __construct($db = null)
    {
        $this->db        = $db ?? Database::connect('default');
        $this->userModel = new UserModel($this->db);
        $this->backupDir = rtrim(WRITEPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'backups' . DIRECTORY_SEPARATOR;

        $this->ensureBackupDirExists();
    }

    /**
     * Pastikan direktori backup ada dan aman dari akses publik
     */
    protected function ensureBackupDirExists(): void
    {
        if (!is_dir($this->backupDir)) {
            @mkdir($this->backupDir, 0755, true);
        }

        // Buat file index.html dan .htaccess jika belum ada untuk perlindungan privasi
        $htaccess = $this->backupDir . '.htaccess';
        if (!file_exists($htaccess)) {
            @file_put_contents($htaccess, "Deny from all\n");
        }

        $indexHtml = $this->backupDir . 'index.html';
        if (!file_exists($indexHtml)) {
            @file_put_contents($indexHtml, '<!DOCTYPE html><html><head><title>403 Forbidden</title></head><body>Directory access is forbidden.</body></html>');
        }
    }

    /**
     * Dapatkan path direktori backup
     */
    public function getBackupDir(): string
    {
        return $this->backupDir;
    }

    /**
     * Format nilai untuk pernyataan SQL INSERT
     */
    protected function formatSqlValue(mixed $val): string
    {
        if ($val === null) {
            return 'NULL';
        }
        if (is_int($val) || is_float($val)) {
            return (string) $val;
        }
        return $this->db->escape((string) $val);
    }

    /**
     * Daftar tabel yang terkait langsung dengan data pemuda
     */
    public function getRelatedTables(): array
    {
        $tables = [
            'pemuda',
            'alamat',
            'pendidikan',
            'pekerjaan',
            'organisasi',
            'pemuda_skills',
            'pemuda_interests',
        ];

        if ($this->db->tableExists('mta_sync_queue')) {
            $tables[] = 'mta_sync_queue';
        }

        return $tables;
    }

    /**
     * Hitung total baris pada setiap tabel data pemuda
     */
    public function getCountsSummary(): array
    {
        $summary = [];
        $tables = $this->getRelatedTables();

        foreach ($tables as $tbl) {
            if ($this->db->tableExists($tbl)) {
                $summary[$tbl] = $this->db->table($tbl)->countAllResults();
            } else {
                $summary[$tbl] = 0;
            }
        }

        return $summary;
    }

    /**
     * Generate Backup SQL Dump lengkap untuk seluruh data pemuda & relasinya
     *
     * @param bool   $saveToServer Jika true, file akan disimpan di direktori writable/backups/
     * @param string $prefix       Awalan nama file
     * @return array [filename, content, filepath, size, counts]
     */
    public function generateSqlBackup(bool $saveToServer = false, string $prefix = 'backup_pemuda_'): array
    {
        $timestamp = date('Ymd_His');
        $filename  = $prefix . $timestamp . '.sql';
        $counts    = $this->getCountsSummary();

        $sql = "-- ========================================================\n";
        $sql .= "-- BACKUP DATA PEMUDA MTA KABUPATEN SRAGEN\n";
        $sql .= "-- Waktu Pembuatan : " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Total Pemuda    : " . number_format($counts['pemuda'] ?? 0) . " data\n";
        $sql .= "-- Sistem          : Sistem Pendataan Pemuda MTA Sragen\n";
        $sql .= "-- ========================================================\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n";
        $sql .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
        $sql .= "SET NAMES utf8mb4;\n\n";

        $tables = $this->getRelatedTables();

        foreach ($tables as $table) {
            if (!$this->db->tableExists($table)) {
                continue;
            }

            $rows = $this->db->table($table)->get()->getResultArray();
            $totalRows = count($rows);

            $sql .= "-- --------------------------------------------------------\n";
            $sql .= "-- Data untuk tabel `{$table}` ({$totalRows} baris)\n";
            $sql .= "-- --------------------------------------------------------\n";

            if ($totalRows === 0) {
                $sql .= "-- Tidak ada data pada tabel `{$table}`\n\n";
                continue;
            }

            $fields = $this->db->getFieldNames($table);
            $fieldList = '`' . implode('`, `', $fields) . '`';

            // Batch per 50 rows per INSERT statement agar aman dari packet limit
            $chunks = array_chunk($rows, 50);
            foreach ($chunks as $chunk) {
                $valuesList = [];
                foreach ($chunk as $row) {
                    $rowVals = [];
                    foreach ($fields as $field) {
                        $rowVals[] = $this->formatSqlValue($row[$field] ?? null);
                    }
                    $valuesList[] = '(' . implode(', ', $rowVals) . ')';
                }

                $sql .= "INSERT INTO `{$table}` ({$fieldList}) VALUES\n" . implode(",\n", $valuesList) . ";\n";
            }

            $sql .= "\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";
        $sql .= "-- ========================================================\n";
        $sql .= "-- AKHIR DUMP DATA PEMUDA\n";
        $sql .= "-- ========================================================\n";

        $filepath = null;
        if ($saveToServer) {
            $filepath = $this->backupDir . $filename;
            file_put_contents($filepath, $sql);
        }

        return [
            'filename' => $filename,
            'content'  => $sql,
            'filepath' => $filepath,
            'size'     => strlen($sql),
            'counts'   => $counts,
        ];
    }

    /**
     * Generate Backup format JSON lengkap dengan data hierarkis & raw tables
     *
     * @param bool   $saveToServer Jika true, simpan di writable/backups/
     * @param string $prefix       Awalan nama file
     * @return array [filename, content, filepath, size, counts]
     */
    public function generateJsonBackup(bool $saveToServer = false, string $prefix = 'backup_pemuda_'): array
    {
        $timestamp = date('Ymd_His');
        $filename  = $prefix . $timestamp . '.json';
        $counts    = $this->getCountsSummary();

        // 1. Ambil seluruh data pemuda beserta relasi lengkap
        $pemudaModel = new PemudaModel($this->db);
        $allYouth = $pemudaModel->select('pemuda.*, 
                                          cabang.name as cabang_name, 
                                          cabang.code as cabang_code, 
                                          cabang.wilayah_id,
                                          wilayah.name as wilayah_name, 
                                          wilayah.code as wilayah_code')
                                ->join('cabang', 'cabang.id = pemuda.cabang_id', 'left')
                                ->join('wilayah', 'wilayah.id = cabang.wilayah_id', 'left')
                                ->orderBy('pemuda.id', 'ASC')
                                ->findAll();

        $hierarchical = [];
        foreach ($allYouth as $p) {
            $pId = (int) $p['id'];

            $alamat = $this->db->table('alamat')
                               ->select('alamat.*, provinces.name as province_name, regencies.name as regency_name, districts.name as district_name, villages.name as village_name')
                               ->join('provinces', 'provinces.id = alamat.province_id', 'left')
                               ->join('regencies', 'regencies.id = alamat.regency_id', 'left')
                               ->join('districts', 'districts.id = alamat.district_id', 'left')
                               ->join('villages', 'villages.id = alamat.village_id', 'left')
                               ->where('alamat.pemuda_id', $pId)
                               ->get()
                               ->getRowArray();

            $pendidikan = $this->db->table('pendidikan')
                                   ->select('pendidikan.*, education_levels.name as education_level_name')
                                   ->join('education_levels', 'education_levels.id = pendidikan.education_level_id', 'left')
                                   ->where('pendidikan.pemuda_id', $pId)
                                   ->get()
                                   ->getRowArray();

            $pekerjaan = $this->db->table('pekerjaan')
                                  ->select('pekerjaan.*, job_statuses.name as job_status_name')
                                  ->join('job_statuses', 'job_statuses.id = pekerjaan.job_status_id', 'left')
                                  ->where('pekerjaan.pemuda_id', $pId)
                                  ->get()
                                  ->getRowArray();

            $organisasi = $this->db->table('organisasi')
                                   ->where('pemuda_id', $pId)
                                   ->orderBy('id', 'ASC')
                                   ->get()
                                   ->getResultArray();

            $skills = $this->db->table('pemuda_skills')
                               ->select('pemuda_skills.*, skills.name as skill_name, skills.description as skill_description')
                               ->join('skills', 'skills.id = pemuda_skills.skill_id', 'left')
                               ->where('pemuda_skills.pemuda_id', $pId)
                               ->get()
                               ->getResultArray();

            $interests = $this->db->table('pemuda_interests')
                                  ->select('pemuda_interests.*, interests.name as interest_name, interests.description as interest_description')
                                  ->join('interests', 'interests.id = pemuda_interests.interest_id', 'left')
                                  ->where('pemuda_interests.pemuda_id', $pId)
                                  ->get()
                                  ->getResultArray();

            $hierarchical[] = [
                'pemuda'     => $p,
                'alamat'     => $alamat,
                'pendidikan' => $pendidikan,
                'pekerjaan'  => $pekerjaan,
                'organisasi' => $organisasi,
                'skills'     => $skills,
                'interests'  => $interests,
            ];
        }

        // 2. Ambil raw database tables untuk pemulihan mudah
        $rawTables = [];
        foreach ($this->getRelatedTables() as $table) {
            if ($this->db->tableExists($table)) {
                $rawTables[$table] = $this->db->table($table)->get()->getResultArray();
            }
        }

        $payload = [
            'metadata' => [
                'app_name'     => 'Sistem Pendataan Pemuda MTA Kabupaten Sragen',
                'generated_at' => date('Y-m-d H:i:s'),
                'total_pemuda' => count($allYouth),
                'table_counts' => $counts,
                'schema_ver'   => '1.0',
            ],
            'pemuda_records' => $hierarchical,
            'raw_tables'     => $rawTables,
        ];

        $jsonString = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($jsonString === false) {
            $jsonString = json_encode($payload);
        }

        $filepath = null;
        if ($saveToServer) {
            $filepath = $this->backupDir . $filename;
            file_put_contents($filepath, $jsonString);
        }

        return [
            'filename' => $filename,
            'content'  => $jsonString,
            'filepath' => $filepath,
            'size'     => strlen($jsonString),
            'counts'   => $counts,
        ];
    }

    /**
     * Generate Backup format Excel (.xlsx) komprehensif
     *
     * @param bool   $saveToServer Jika true, simpan di server
     * @param string $prefix       Awalan nama file
     * @return array [filename, filepath, size, counts, spreadsheet]
     */
    public function generateXlsxBackup(bool $saveToServer = false, string $prefix = 'backup_pemuda_'): array
    {
        $timestamp = date('Ymd_His');
        $filename  = $prefix . $timestamp . '.xlsx';
        $counts    = $this->getCountsSummary();

        $exportService = new PemudaExportService(new PemudaModel($this->db));
        $spreadsheet   = $exportService->generateXlsx([], PemudaExportService::PRESETS['all'], [
            'role' => 'superadmin',
        ], [
            'title'     => 'CADANGAN DATA PEMUDA MTA SRAGEN',
            'user_name' => session()->get('name') ?? 'Super Administrator',
        ]);

        $filepath = null;
        $size = 0;

        if ($saveToServer) {
            $filepath = $this->backupDir . $filename;
            $writer = new Xlsx($spreadsheet);
            $writer->save($filepath);
            $size = file_exists($filepath) ? filesize($filepath) : 0;
        }

        return [
            'filename'    => $filename,
            'filepath'    => $filepath,
            'size'        => $size,
            'counts'      => $counts,
            'spreadsheet' => $spreadsheet,
        ];
    }

    /**
     * Ambil daftar file backup yang tersimpan di writable/backups/
     */
    public function getBackupList(): array
    {
        $list = [];
        if (!is_dir($this->backupDir)) {
            return $list;
        }

        $files = scandir($this->backupDir);
        if (!$files) {
            return $list;
        }

        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || $file === '.htaccess' || $file === 'index.html') {
                continue;
            }

            $path = $this->backupDir . $file;
            if (!is_file($path)) {
                continue;
            }

            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (!in_array($ext, ['sql', 'json', 'xlsx', 'csv'], true)) {
                continue;
            }

            $sizeBytes = filesize($path);
            $mtime     = filemtime($path);

            $isAuto = str_starts_with($file, 'auto_backup_sebelum_reset_');

            $list[] = [
                'filename'       => $file,
                'extension'      => strtoupper($ext),
                'size_bytes'     => $sizeBytes,
                'size_formatted' => $this->formatFileSize($sizeBytes),
                'created_at'     => date('Y-m-d H:i:s', $mtime),
                'timestamp'      => $mtime,
                'is_auto_backup' => $isAuto,
            ];
        }

        // Urutkan dari yang terbaru
        usort($list, fn($a, $b) => $b['timestamp'] <=> $a['timestamp']);

        return $list;
    }

    /**
     * Dapatkan path lengkap file backup yang aman dari directory traversal
     */
    public function getBackupFilePath(string $filename): ?string
    {
        $clean = basename($filename);
        if ($clean === '' || $clean === '.' || $clean === '..') {
            return null;
        }

        $ext = strtolower(pathinfo($clean, PATHINFO_EXTENSION));
        if (!in_array($ext, ['sql', 'json', 'xlsx', 'csv'], true)) {
            return null;
        }

        $fullPath = $this->backupDir . $clean;
        if (!file_exists($fullPath) || !is_file($fullPath)) {
            return null;
        }

        return $fullPath;
    }

    /**
     * Hapus file backup dari server
     */
    public function deleteBackupFile(string $filename): bool
    {
        $path = $this->getBackupFilePath($filename);
        if (!$path) {
            return false;
        }

        return @unlink($path);
    }

    /**
     * Hapus seluruh data pemuda beserta seluruh data relasinya dengan aman
     * Dilengkapi auto-backup sebelum penghapusan dan verifikasi otentikasi password superadmin.
     *
     * @param int    $superadminUserId ID user superadmin yang melakukan tindakan
     * @param string $password         Password akun superadmin untuk verifikasi
     * @return array [success => bool, message => string, counts => array, auto_backup => string|null]
     */
    public function deleteAllYouthData(int $superadminUserId, string $password): array
    {
        // 1. Verifikasi akun superadmin & password
        $user = $this->userModel->find($superadminUserId);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Akun pengguna tidak ditemukan dalam sistem.',
            ];
        }

        if (!password_verify($password, $user['password'])) {
            return [
                'success' => false,
                'message' => 'Password konfirmasi salah. Tindakan penghapusan seluruh data dibatalkan demi keamanan.',
            ];
        }

        // 2. Cek apakah ada data pemuda yang tersedia
        $totalPemuda = $this->db->table('pemuda')->countAllResults();
        if ($totalPemuda === 0) {
            return [
                'success' => false,
                'message' => 'Tidak ada data pemuda di sistem untuk dihapus.',
            ];
        }

        // 3. Buat file AUTO-BACKUP darurat sebelum penghapusan data
        $autoBackupFilename = null;
        try {
            $autoBackup = $this->generateSqlBackup(true, 'auto_backup_sebelum_reset_');
            $autoBackupFilename = $autoBackup['filename'] ?? null;
        } catch (\Throwable $e) {
            log_message('error', 'Gagal membuat auto backup sebelum reset pemuda: ' . $e->getMessage());
        }

        // 4. Lakukan penghapusan secara aman menggunakan transaksi database
        $this->db->transStart();

        $deletedCounts = [];
        try {
            // Hitung baris yang akan dihapus pada setiap tabel
            $deletedCounts['pemuda_interests'] = $this->db->table('pemuda_interests')->countAllResults();
            $deletedCounts['pemuda_skills']    = $this->db->table('pemuda_skills')->countAllResults();
            $deletedCounts['organisasi']       = $this->db->table('organisasi')->countAllResults();
            $deletedCounts['pekerjaan']        = $this->db->table('pekerjaan')->countAllResults();
            $deletedCounts['pendidikan']       = $this->db->table('pendidikan')->countAllResults();
            $deletedCounts['alamat']           = $this->db->table('alamat')->countAllResults();
            $deletedCounts['pemuda']           = $totalPemuda;

            if ($this->db->tableExists('mta_sync_queue')) {
                $deletedCounts['mta_sync_queue'] = $this->db->table('mta_sync_queue')->countAllResults();
            }

            // Hapus tabel anak terlebih dahulu untuk menjaga integritas relasi
            $this->db->table('pemuda_interests')->where('1 = 1')->delete();
            $this->db->table('pemuda_skills')->where('1 = 1')->delete();
            $this->db->table('organisasi')->where('1 = 1')->delete();
            $this->db->table('pekerjaan')->where('1 = 1')->delete();
            $this->db->table('pendidikan')->where('1 = 1')->delete();
            $this->db->table('alamat')->where('1 = 1')->delete();

            if ($this->db->tableExists('mta_sync_queue')) {
                $this->db->table('mta_sync_queue')->where('1 = 1')->delete();
            }

            // Putuskan relasi pada tabel responses (SET NULL) jika ada
            if ($this->db->tableExists('responses')) {
                $this->db->table('responses')
                         ->where('pemuda_id IS NOT NULL')
                         ->update(['pemuda_id' => null]);
            }

            // Hapus data utama pemuda
            $this->db->table('pemuda')->where('1 = 1')->delete();

            // Reset AUTO_INCREMENT ke 1 jika didukung oleh database driver
            $this->resetAutoIncrements();

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return [
                    'success' => false,
                    'message' => 'Gagal menghapus data: Terjadi kegagalan transaksi database. Seluruh perubahan telah dibatalkan.',
                ];
            }

            // Catat log keamanan
            log_message('notice', "Superadmin [ID: {$superadminUserId}, {$user['username']}] menghapus SELURUH data pemuda ({$totalPemuda} data). Salinan auto-backup: {$autoBackupFilename}");

            return [
                'success'        => true,
                'message'        => "Seluruh data pemuda ({$totalPemuda} pemuda) berhasil dihapus permanen dari sistem.",
                'deleted_counts' => $deletedCounts,
                'auto_backup'    => $autoBackupFilename,
            ];

        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('critical', 'Kesalahan kritis saat menghapus semua pemuda: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat menghapus data: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Reset Auto Increment pada tabel-tabel data pemuda
     */
    protected function resetAutoIncrements(): void
    {
        $tablesWithAutoIncrement = ['pemuda', 'alamat', 'pendidikan', 'pekerjaan', 'organisasi'];
        if ($this->db->tableExists('mta_sync_queue')) {
            $tablesWithAutoIncrement[] = 'mta_sync_queue';
        }

        foreach ($tablesWithAutoIncrement as $tbl) {
            try {
                $this->db->query("ALTER TABLE `{$tbl}` AUTO_INCREMENT = 1");
            } catch (\Throwable $e) {
                // Abaikan jika driver/user tidak mendukung perintah ALTER TABLE
            }
        }
    }

    /**
     * Format ukuran file menjadi satuan yang manusiawi (B, KB, MB, GB)
     */
    protected function formatFileSize(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = (int) floor(log($bytes, 1024));
        return round($bytes / pow(1024, $i), 2) . ' ' . ($units[$i] ?? 'B');
    }
}
