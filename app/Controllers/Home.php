<?php

namespace App\Controllers;

use App\Models\PemudaModel;
use App\Models\WilayahModel;
use App\Models\CabangModel;
use App\Models\HomepageSettingModel;

class Home extends BaseController
{
    protected PemudaModel $pemudaModel;
    protected WilayahModel $wilayahModel;
    protected CabangModel $cabangModel;
    protected HomepageSettingModel $settingModel;

    public function __construct()
    {
        $this->pemudaModel   = new PemudaModel();
        $this->wilayahModel  = new WilayahModel();
        $this->cabangModel   = new CabangModel();
        $this->settingModel  = new HomepageSettingModel();
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

        // Ambil pengaturan konten homepage
        $settings = $this->settingModel->getAllSettings();

        // Parse format JSON & multiline
        $heroChips = json_decode($settings['hero_chips'] ?? '[]', true) ?: [];
        $heroFeatures = array_filter(array_map('trim', explode("\n", (string) ($settings['hero_card_features'] ?? ''))));
        $misiList  = json_decode($settings['misi_list'] ?? '[]', true) ?: [];
        $programs  = json_decode($settings['program_list'] ?? '[]', true) ?: [];
        $alurSteps = json_decode($settings['alur_steps'] ?? '[]', true) ?: [];
        $faqs      = json_decode($settings['faq_list'] ?? '[]', true) ?: [];

        $data = [
            'title'         => 'Pemuda MTA Perwakilan Sragen | Pusat Informasi & Pendataan Pemuda',
            'totalPemuda'   => $totalPemuda,
            'totalVerified' => $totalVerified,
            'totalCabang'   => $totalCabang > 0 ? $totalCabang : 61,
            'totalWilayah'  => $totalWilayah,
            'wilayahList'   => $wilayahList,
            'settings'      => $settings,
            'heroChips'     => $heroChips,
            'heroFeatures'  => $heroFeatures,
            'misiList'      => $misiList,
            'programs'      => $programs,
            'alurSteps'     => $alurSteps,
            'faqs'          => $faqs,
        ];

        return view('landing', $data);
    }
}
