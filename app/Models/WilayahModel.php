<?php

namespace App\Models;

use CodeIgniter\Model;

class WilayahModel extends Model
{
    protected $table            = 'wilayah';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'code',
        'name',
        'description',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil wilayah lengkap beserta cabang-cabangnya
     */
    public function getWithCabang(): array
    {
        $wilayahList = $this->orderBy('id', 'ASC')->findAll();
        $cabangModel = new CabangModel();
        
        foreach ($wilayahList as &$w) {
            $w['cabang'] = $cabangModel->where('wilayah_id', $w['id'])->orderBy('name', 'ASC')->findAll();
        }

        return $wilayahList;
    }
}
