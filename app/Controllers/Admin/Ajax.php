<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CabangModel;
use App\Models\DistrictModel;
use App\Models\VillageModel;

class Ajax extends BaseController
{
    public function getCabangByWilayah(int $wilayahId)
    {
        $scopeRole      = session()->get('role');
        $scopeWilayahId = session()->get('wilayah_id');
        $scopeCabangId  = session()->get('cabang_id');

        $cabangModel = new CabangModel();
        $builder = $cabangModel->orderBy('name', 'ASC');

        if ($scopeRole === 'admin_wilayah' && !empty($scopeWilayahId)) {
            $builder->where('wilayah_id', (int) $scopeWilayahId);
        } elseif ($scopeRole === 'admin_cabang' && !empty($scopeCabangId)) {
            $builder->where('id', (int) $scopeCabangId);
        } else {
            $builder->where('wilayah_id', $wilayahId);
        }

        $cabang = $builder->findAll();

        return $this->response->setJSON($cabang);
    }

    public function getVillagesByDistrict(int $districtId)
    {
        $villageModel = new VillageModel();
        $villages = $villageModel->where('district_id', $districtId)
                                 ->orderBy('name', 'ASC')
                                 ->findAll();

        return $this->response->setJSON($villages);
    }
}
