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
        'mta_uuid',
        'mta_code',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil wilayah lengkap beserta cabang-cabangnya
     */
    public function getWithCabang(?int $wilayahId = null, ?int $cabangId = null): array
    {
        $builder = $this->orderBy('id', 'ASC');
        if (!empty($wilayahId)) {
            $builder->where('id', $wilayahId);
        }
        $wilayahList = $builder->findAll();
        $cabangModel = new CabangModel();
        
        foreach ($wilayahList as &$w) {
            $cBuilder = $cabangModel->where('wilayah_id', $w['id'])->orderBy('name', 'ASC');
            if (!empty($cabangId)) {
                $cBuilder->where('id', $cabangId);
            }
            $w['cabang'] = $cBuilder->findAll();
        }

        return $wilayahList;
    }
}
