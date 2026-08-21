<?php

namespace App\Models;

use CodeIgniter\Model;

class CabangModel extends Model
{
    protected $table            = 'cabang';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'wilayah_id',
        'code',
        'name',
        'description',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil cabang dengan join data wilayah
     */
    public function getWithWilayah(): array
    {
        return $this->select('cabang.*, wilayah.name as wilayah_name, wilayah.code as wilayah_code')
                    ->join('wilayah', 'wilayah.id = cabang.wilayah_id')
                    ->orderBy('cabang.wilayah_id', 'ASC')
                    ->orderBy('cabang.name', 'ASC')
                    ->findAll();
    }
}
