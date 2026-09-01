<?php

namespace App\Models;

use CodeIgniter\Model;

class MtaSyncLogModel extends Model
{
    protected $table            = 'mta_sync_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'sync_type',
        'status',
        'total_records',
        'message',
        'created_by',
        'created_at',
    ];

    protected $useTimestamps = false;

    /**
     * Catat log aktivitas sinkronisasi MTA
     */
    public function log(string $type, string $status, int $totalRecords = 0, ?string $message = null, ?int $userId = null): int
    {
        return (int) $this->insert([
            'sync_type'     => $type,
            'status'        => $status,
            'total_records' => $totalRecords,
            'message'       => $message,
            'created_by'    => $userId ?? (session()->get('user_id') ?? null),
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Ambil log terbaru dengan nama pengguna
     */
    public function getRecentLogs(int $limit = 10): array
    {
        return $this->select('mta_sync_logs.*, users.name as user_name, users.username')
                    ->join('users', 'users.id = mta_sync_logs.created_by', 'left')
                    ->orderBy('mta_sync_logs.id', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
}
