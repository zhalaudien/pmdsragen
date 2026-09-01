<?php

namespace App\Models;

use CodeIgniter\Model;

class PekerjaanModel extends Model
{
    protected $table            = 'pekerjaan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pemuda_id',
        'job_status_id',
        'job_title',
        'company_name',
        'business_field',
    ];

    // Callbacks
    protected $beforeInsert = ['formatLowercaseFields'];
    protected $beforeUpdate = ['formatLowercaseFields'];

    protected function formatLowercaseFields(array $data): array
    {
        if (isset($data['data']) && is_array($data['data'])) {
            $fieldsToLower = ['job_title', 'company_name', 'business_field'];
            foreach ($fieldsToLower as $f) {
                if (array_key_exists($f, $data['data']) && is_string($data['data'][$f])) {
                    $trimmed = trim($data['data'][$f]);
                    $data['data'][$f] = ($trimmed === '') ? null : mb_strtolower($trimmed, 'UTF-8');
                }
            }
        }
        return $data;
    }

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
