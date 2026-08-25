<?php

namespace App\Controllers;

use App\Models\PemudaModel;
use App\Models\WilayahModel;
use App\Models\CabangModel;

class Home extends BaseController
{
    protected PemudaModel $pemudaModel;
    protected WilayahModel $wilayahModel;
    protected CabangModel $cabangModel;

    public function __construct()
    {
        $this->pemudaModel  = new PemudaModel();
        $this->wilayahModel = new WilayahModel();
        $this->cabangModel  = new CabangModel();
    }

    /**
     * Halaman Utama / Landing Page Informasi Pemuda MTA Perwakilan Sragen
     */
    public function index(): string
    {
        $totalPemuda   = 0;
        $totalVerified = 0;
        $totalCabang   = 0;
        $totalWilayah  = 4;
        $wilayahList   = [];

        try {
            $totalPemuda   = $this->pemudaModel->where('status_data', 'active')->countAllResults();
            $totalVerified = $this->pemudaModel->where('status_verifikasi', 'verified')->where('status_data', 'active')->countAllResults();
            $totalCabang   = $this->cabangModel->countAllResults();
            $wilayahList   = $this->wilayahModel->getWithCabang();
            $totalWilayah  = count($wilayahList) > 0 ? count($wilayahList) : 4;
        } catch (\Throwable $e) {
            // Fallback gracefully jika database belum ter-seed
        }

        $data = [
            'title'         => 'Pemuda MTA Perwakilan Sragen | Pusat Informasi & Pendataan Pemuda',
            'totalPemuda'   => $totalPemuda,
            'totalVerified' => $totalVerified,
            'totalCabang'   => $totalCabang > 0 ? $totalCabang : 61,
            'totalWilayah'  => $totalWilayah,
            'wilayahList'   => $wilayahList,
        ];

        return view('landing', $data);
    }
}
