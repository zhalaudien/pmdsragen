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
        $cabangModel = new CabangModel();
        $cabang = $cabangModel->where('wilayah_id', $wilayahId)
                              ->orderBy('name', 'ASC')
                              ->findAll();

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
