<?php

namespace App\Models;

use CodeIgniter\Model;

class OrganisasiModel extends Model
{
    protected $table            = 'organisasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pemuda_id',
        'organization_name',
        'position',
        'join_date',
        'end_date',
        'description',
    ];

    // Callbacks
    protected $beforeInsert = ['formatLowercaseFields'];
    protected $beforeUpdate = ['formatLowercaseFields'];

    protected function formatLowercaseFields(array $data): array
    {
        if (isset($data['data']) && is_array($data['data'])) {
            $fieldsToLower = ['organization_name', 'position', 'description'];
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
