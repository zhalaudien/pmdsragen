<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\MtaApiService;
use App\Services\MtaSyncService;
use App\Models\CabangModel;
use App\Models\WilayahModel;
use App\Models\PemudaModel;
use App\Models\MtaSyncLogModel;

class MtaSync extends BaseController
{
    protected MtaApiService $apiService;
    protected MtaSyncService $syncService;
    protected CabangModel $cabangModel;
    protected WilayahModel $wilayahModel;
    protected PemudaModel $pemudaModel;
    protected MtaSyncLogModel $logModel;

    public function __construct()
    {
        $this->apiService   = new MtaApiService();
        $this->syncService  = new MtaSyncService($this->apiService);
        $this->cabangModel  = new CabangModel();
        $this->wilayahModel = new WilayahModel();
        $this->pemudaModel  = new PemudaModel();
        $this->logModel     = new MtaSyncLogModel();
    }

    /**
     * Dashboard Integrasi & Sinkronisasi API MTA (Khusus Perwakilan Sragen)
     */
    public function index(): string
    {
        $testConn        = $this->apiService->testConnection();
        $localCabang     = $this->cabangModel->getWithWilayah();
        $wilayahList     = $this->wilayahModel->orderBy('id', 'ASC')->findAll();
        $recentLogs      = $this->logModel->getRecentLogs(10);
        $sragenDetail    = $this->apiService->getPerwakilanSragenDetail();
        $mtaSragenCabang = ($sragenDetail['success'] ?? false) && isset($sragenDetail['data']['cabang']) ? $sragenDetail['data']['cabang'] : [];

        // Hitung total cabang lokal yang sudah terpetakan MTA
        $syncedCabangCount = 0;
        foreach ($localCabang as $c) {
            if (!empty($c['mta_uuid'])) {
                $syncedCabangCount++;
            }
        }

        // Hitung total pemuda lokal yang sudah tersinkronisasi MTA
        $syncedPemudaCount = $this->pemudaModel->where('mta_warga_uuid IS NOT NULL')->countAllResults();
        $totalPemudaCount  = $this->pemudaModel->countAll();

        $data = [
            'title'             => 'Integrasi Database Warga MTA (Perwakilan Sragen)',
            'testConn'          => $testConn,
            'sragenDetail'      => $sragenDetail['data'] ?? null,
            'mtaSragenCabang'   => $mtaSragenCabang,
            'localCabang'       => $localCabang,
            'wilayahList'       => $wilayahList,
            'recentLogs'        => $recentLogs,
            'syncedCabangCount' => $syncedCabangCount,
            'totalCabangCount'  => count($localCabang),
            'syncedPemudaCount' => $syncedPemudaCount,
            'totalPemudaCount'  => $totalPemudaCount,
            'user'              => session()->get(),
        ];

        return view('admin/mta_sync/index', $data);
    }

    /**
     * Test Koneksi API MTA via AJAX
     */
    public function testConnection()
    {
        $res = $this->apiService->testConnection();
        return $this->response->setJSON([
            'status'   => $res['connected'] ? 'success' : 'error',
            'data'     => $res,
            'csrfHash' => csrf_hash(),
        ]);
    }

    /**
     * Trigger Sinkronisasi Cabang dari API MTA (POST)
     */
    public function syncCabang()
    {
        $perwakilanUuid = $this->request->getPost('perwakilan_uuid') ?: null;
        $wilayahId      = $this->request->getPost('wilayah_id') ? (int) $this->request->getPost('wilayah_id') : null;
        $autoCreate     = (bool) $this->request->getPost('auto_create');

        $result = $this->syncService->syncCabang($perwakilanUuid, $wilayahId, $autoCreate);

        if ($result['success']) {
            return redirect()->to(base_url('admin/mta-sync'))
                             ->with('success', $result['message']);
        }

        return redirect()->to(base_url('admin/mta-sync'))
                         ->with('error', $result['message']);
    }

    /**
     * Pencarian Warga MTA via AJAX untuk form lookup / import
     */
    public function searchWarga()
    {
        $q          = trim((string) $this->request->getGet('q'));
        $cabangUuid = $this->request->getGet('cabang_uuid') ?: null;
        $kelamin    = $this->request->getGet('kelamin') ?: null;
        $limit      = (int) ($this->request->getGet('limit') ?: 20);

        if (mb_strlen($q) < 2) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Kata kunci pencarian minimal 2 karakter.',
                'data'    => [],
            ]);
        }

        $params = ['limit' => $limit];
        if ($cabangUuid) {
            $params['cabang'] = $cabangUuid;
        }
        if ($kelamin) {
            $params['kelamin'] = $kelamin;
        }

        $res = $this->apiService->searchWarga($q, $params);

        if (!($res['success'] ?? false) || empty($res['data'])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => $res['message'] ?? 'Tidak ada data warga yang sesuai.',
                'data'    => [],
            ]);
        }

        // Tandai apakah warga sudah terdaftar di PMD Sragen
        $wargaList = $res['data'];
        foreach ($wargaList as &$w) {
            $wUuid = $w['uuid'] ?? '';
            $isLocal = false;
            $localPemuda = null;

            if ($wUuid) {
                $localPemuda = $this->pemudaModel->findByMtaWargaUuid($wUuid);
            }
            if (!$localPemuda && !empty($w['nama']) && !empty($w['lahir'])) {
                // Fallback check nama + birth_date
                $localPemuda = $this->pemudaModel->where('birth_date', date('Y-m-d', strtotime($w['lahir'])))
                                                 ->where('LOWER(TRIM(name))', strtolower(trim($w['nama'])))
                                                 ->first();
            }

            $w['is_local_registered'] = (bool) $localPemuda;
            $w['local_pemuda_id']     = $localPemuda['id'] ?? null;
            $w['local_reg_number']    = $localPemuda['registration_number'] ?? null;
        }

        return $this->response->setJSON([
            'success' => true,
            'data'    => $wargaList,
        ]);
    }

    /**
     * Detail Warga MTA via AJAX
     */
    public function wargaDetail(string $uuid)
    {
        $res = $this->apiService->getWargaDetail($uuid);
        return $this->response->setJSON($res);
    }

    /**
     * Impor Warga MTA menjadi Pemuda (POST via AJAX atau Form)
     */
    public function importWarga()
    {
        $wargaUuid = trim((string) $this->request->getPost('warga_uuid'));
        $cabangId  = (int) $this->request->getPost('cabang_id');

        if (empty($wargaUuid) || $cabangId <= 0) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'UUID Warga dan Cabang Tujuan wajib dipilih.']);
            }
            return redirect()->back()->with('error', 'UUID Warga dan Cabang Tujuan wajib dipilih.');
        }

        // Ambil data warga dari MTA
        $detailRes = $this->apiService->getWargaDetail($wargaUuid);
        if (!($detailRes['success'] ?? false) || empty($detailRes['data'])) {
            $msg = $detailRes['message'] ?? 'Gagal mengambil detail warga dari API MTA.';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $msg]);
            }
            return redirect()->back()->with('error', $msg);
        }

        $result = $this->syncService->syncWargaToPemuda($detailRes['data'], $cabangId, session()->get('user_id'));

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($result);
        }

        if ($result['success']) {
            return redirect()->to(base_url('admin/pemuda/detail/' . $result['pemuda_id']))
                             ->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Sinkronkan Pemuda Tunggal dengan Server MTA (POST / AJAX)
     */
    public function syncPemuda(int $id)
    {
        $result = $this->syncService->syncSinglePemuda($id);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => $result['success'],
                'message' => $result['message'],
                'csrfHash'=> csrf_hash(),
            ]);
        }

        if ($result['success']) {
            return redirect()->to(base_url('admin/pemuda/detail/' . $id))
                             ->with('success', $result['message']);
        }

        return redirect()->to(base_url('admin/pemuda/detail/' . $id))
                         ->with('error', $result['message']);
    }

    /**
     * Ambil daftar warga per Cabang MTA Sragen via AJAX
     */
    public function cabangWarga(string $uuid)
    {
        $page    = (int) ($this->request->getGet('page') ?: 1);
        $perPage = (int) ($this->request->getGet('per_page') ?: 50);
        $gender  = $this->request->getGet('gender') ?: null;

        $res = $this->apiService->getCabangWarga($uuid, $page, $perPage, $gender);

        if (!($res['success'] ?? false) || empty($res['data'])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => $res['message'] ?? 'Tidak ada data warga di cabang ini.',
                'data'    => [],
                'meta'    => $res['meta'] ?? null,
            ]);
        }

        // Tandai status di PMD lokal
        $wargaList = $res['data'];
        foreach ($wargaList as &$w) {
            $wUuid = $w['uuid'] ?? '';
            $isLocal = false;
            $localPemuda = null;

            if ($wUuid) {
                $localPemuda = $this->pemudaModel->findByMtaWargaUuid($wUuid);
            }
            if (!$localPemuda && !empty($w['nama']) && !empty($w['lahir'])) {
                $localPemuda = $this->pemudaModel->where('birth_date', date('Y-m-d', strtotime($w['lahir'])))
                                                 ->where('LOWER(TRIM(name))', strtolower(trim($w['nama'])))
                                                 ->first();
            }

            $w['is_local_registered'] = (bool) $localPemuda;
            $w['local_pemuda_id']     = $localPemuda['id'] ?? null;
            $w['local_reg_number']    = $localPemuda['registration_number'] ?? null;
        }

        return $this->response->setJSON([
            'success' => true,
            'cabang'  => $res['cabang'] ?? '',
            'data'    => $wargaList,
            'meta'    => $res['meta'] ?? null,
        ]);
    }

    /**
     * Sinkronkan & Verifikasi Otomatis Seluruh Pemuda Sragen dengan MTA Pusat (POST / AJAX)
     */
    public function syncVerifyAll()
    {
        $cabangId    = $this->request->getPost('cabang_id') ? (int) $this->request->getPost('cabang_id') : null;
        $onlyPending = (bool) $this->request->getPost('only_pending');

        $result = $this->syncService->syncAndVerifyAllPemudaSragen($cabangId, $onlyPending);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => $result['success'],
                'message' => $result['message'],
                'data'    => $result,
                'csrfHash'=> csrf_hash(),
            ]);
        }

        if ($result['success']) {
            return redirect()->to(base_url('admin/mta-sync'))->with('success', $result['message']);
        }

        return redirect()->to(base_url('admin/mta-sync'))->with('error', $result['message']);
    }
}
