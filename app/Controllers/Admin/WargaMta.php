<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\MtaApiService;
use App\Services\MtaSyncService;
use App\Models\PemudaModel;
use App\Models\CabangModel;
use App\Models\WilayahModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class WargaMta extends BaseController
{
    protected MtaApiService $apiService;
    protected MtaSyncService $syncService;
    protected PemudaModel $pemudaModel;
    protected CabangModel $cabangModel;
    protected WilayahModel $wilayahModel;

    public function __construct()
    {
        $this->apiService   = new MtaApiService();
        $this->syncService  = new MtaSyncService($this->apiService);
        $this->pemudaModel  = new PemudaModel();
        $this->cabangModel  = new CabangModel();
        $this->wilayahModel = new WilayahModel();
    }

    /**
     * Pastikan hanya Superadmin yang dapat mengakses fitur ini
     */
    protected function ensureSuperadmin(): void
    {
        if (session()->get('role') !== 'superadmin') {
            throw PageNotFoundException::forPageNotFound('Halaman ini hanya dapat diakses oleh Superadmin.');
        }
    }

    /**
     * Halaman Utama: Daftar & Pencarian Data Warga MTA (Khusus Perwakilan Sragen)
     */
    public function index(): string
    {
        $this->ensureSuperadmin();

        // 1. Ambil Parameter Filter & Pagination dari URL GET
        $page      = max(1, (int) ($this->request->getGet('page') ?: 1));
        $perPage   = (int) ($this->request->getGet('per_page') ?: 20);
        $perPage   = in_array($perPage, [10, 15, 20, 25, 50, 100], true) ? $perPage : 20;
        $search    = trim((string) $this->request->getGet('search'));
        $cabang    = trim((string) $this->request->getGet('cabang'));
        $kelamin   = strtoupper(trim((string) $this->request->getGet('kelamin')));
        $status    = trim((string) $this->request->getGet('status'));
        $statusPmd = trim((string) $this->request->getGet('status_pmd')); // 'all', 'registered', 'unregistered'

        // 2. Siapkan parameter request ke API MTA (dikunci ke Perwakilan Sragen)
        $apiParams = [
            'perwakilan' => $this->apiService->getSragenUuid(),
            'page'       => $page,
            'per_page'   => $perPage,
        ];

        if ($search !== '') {
            $apiParams['search'] = $search;
        }
        if ($cabang !== '') {
            $apiParams['cabang'] = $cabang;
        }
        if (in_array($kelamin, ['L', 'P'], true)) {
            $apiParams['kelamin'] = $kelamin;
        }
        if ($status !== '') {
            $apiParams['status'] = $status;
        }

        // 3. Panggil API MTA untuk data warga
        $wargaResponse = $this->apiService->getWargaList($apiParams);
        $apiError      = null;
        $wargaList     = [];
        $meta          = [
            'page'        => $page,
            'per_page'    => $perPage,
            'total'       => 0,
            'total_pages' => 1,
        ];

        if (!($wargaResponse['success'] ?? false)) {
            $apiError = $wargaResponse['message'] ?? 'Gagal menghubungi server API MTA (api.mta.or.id).';
        } else {
            $wargaList = $wargaResponse['data'] ?? [];
            if (isset($wargaResponse['meta']) && is_array($wargaResponse['meta'])) {
                $meta = $wargaResponse['meta'];
            }
        }

        // 4. Sinkronkan status lokal PMD Sragen (Cek apakah warga sudah terdaftar di PMD)
        $wargaUuids = array_filter(array_column($wargaList, 'uuid'));
        $localPemudaMap = [];

        if (!empty($wargaUuids)) {
            $matchedPemuda = $this->pemudaModel
                ->select('pemuda.id, pemuda.registration_number, pemuda.name, pemuda.status_verifikasi, pemuda.mta_warga_uuid, cabang.name as cabang_name')
                ->join('cabang', 'cabang.id = pemuda.cabang_id', 'left')
                ->whereIn('pemuda.mta_warga_uuid', $wargaUuids)
                ->findAll();

            foreach ($matchedPemuda as $mp) {
                if (!empty($mp['mta_warga_uuid'])) {
                    $localPemudaMap[$mp['mta_warga_uuid']] = $mp;
                }
            }
        }

        // Enrich setiap warga dengan status PMD lokal
        foreach ($wargaList as &$w) {
            $uuid = $w['uuid'] ?? '';
            if (!empty($uuid) && isset($localPemudaMap[$uuid])) {
                $lp = $localPemudaMap[$uuid];
                $w['is_local_registered']     = true;
                $w['local_pemuda_id']         = (int) $lp['id'];
                $w['local_reg_number']        = $lp['registration_number'];
                $w['local_cabang_name']       = $lp['cabang_name'] ?? '-';
                $w['local_status_verifikasi'] = $lp['status_verifikasi'];
            } else {
                $w['is_local_registered']     = false;
                $w['local_pemuda_id']         = null;
                $w['local_reg_number']        = null;
                $w['local_cabang_name']       = null;
                $w['local_status_verifikasi'] = null;
            }
        }
        unset($w);

        // Filter status_pmd jika dipilih oleh admin
        if ($statusPmd === 'registered') {
            $wargaList = array_values(array_filter($wargaList, static fn($item) => ($item['is_local_registered'] ?? false) === true));
        } elseif ($statusPmd === 'unregistered') {
            $wargaList = array_values(array_filter($wargaList, static fn($item) => ($item['is_local_registered'] ?? false) === false));
        }

        // 5. Ambil data pendukung: Daftar Cabang MTA Sragen untuk dropdown filter
        $mtaSragenCabangList = [];
        $sragenDetail = $this->apiService->getPerwakilanSragenDetail();
        if (($sragenDetail['success'] ?? false) && isset($sragenDetail['data']['cabang'])) {
            $mtaSragenCabangList = $sragenDetail['data']['cabang'];
        }

        // 6. Ambil data Cabang PMD Lokal untuk form modal import
        $localCabangList = $this->cabangModel->getWithWilayah();

        // 7. Hitung statistik ringkas untuk header
        $totalWargaSragenMta  = $sragenDetail['data']['total_warga'] ?? ($meta['total'] ?? 0);
        $totalCabangSragenMta = count($mtaSragenCabangList);
        $totalPemudaSyncedMta = $this->pemudaModel->where('mta_warga_uuid IS NOT NULL')->countAllResults();
        $totalPemudaLokal     = $this->pemudaModel->countAll();

        $data = [
            'title'                => 'Data Warga MTA — Perwakilan Sragen',
            'wargaList'            => $wargaList,
            'meta'                 => $meta,
            'apiError'             => $apiError,
            'mtaCabangList'        => $mtaSragenCabangList,
            'localCabangList'      => $localCabangList,
            'filters'              => [
                'search'     => $search,
                'cabang'     => $cabang,
                'kelamin'    => $kelamin,
                'status'     => $status,
                'status_pmd' => $statusPmd,
                'per_page'   => $perPage,
                'page'       => $page,
            ],
            'stats'                => [
                'totalWargaSragenMta'  => $totalWargaSragenMta,
                'totalCabangSragenMta' => $totalCabangSragenMta,
                'totalPemudaSyncedMta' => $totalPemudaSyncedMta,
                'totalPemudaLokal'     => $totalPemudaLokal,
            ],
            'user'                 => session()->get(),
        ];

        return view('admin/warga_mta/index', $data);
    }

    /**
     * Detail Data Warga MTA (Bisa via AJAX Modal atau Direct Page)
     */
    public function detail(string $uuid)
    {
        $this->ensureSuperadmin();

        $uuid = trim($uuid);
        if (empty($uuid)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'UUID Warga MTA tidak valid.']);
            }
            return redirect()->to(base_url('admin/warga-mta'))->with('error', 'UUID Warga MTA tidak valid.');
        }

        $res = $this->apiService->getWargaDetail($uuid);

        if (!($res['success'] ?? false) || empty($res['data'])) {
            $msg = $res['message'] ?? 'Gagal mengambil detail data warga dari API MTA.';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $msg]);
            }
            return redirect()->to(base_url('admin/warga-mta'))->with('error', $msg);
        }

        $wargaData = $res['data'];

        // Cek apakah sudah terdaftar di PMD Lokal
        $localPemuda = $this->pemudaModel->findByMtaWargaUuid($uuid);
        if (!$localPemuda && !empty($wargaData['nama']) && !empty($wargaData['lahir'])) {
            $localPemuda = $this->pemudaModel->where('birth_date', date('Y-m-d', strtotime($wargaData['lahir'])))
                                             ->where('LOWER(TRIM(name))', strtolower(trim($wargaData['nama'])))
                                             ->first();
        }

        $wargaData['is_local_registered']     = (bool) $localPemuda;
        $wargaData['local_pemuda_id']         = $localPemuda['id'] ?? null;
        $wargaData['local_reg_number']        = $localPemuda['registration_number'] ?? null;
        $wargaData['local_status_verifikasi'] = $localPemuda['status_verifikasi'] ?? null;

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'data'    => $wargaData,
            ]);
        }

        $data = [
            'title'           => 'Detail Warga MTA: ' . ($wargaData['nama'] ?? '-'),
            'warga'           => $wargaData,
            'localCabangList' => $this->cabangModel->getWithWilayah(),
            'user'            => session()->get(),
        ];

        return view('admin/warga_mta/detail', $data);
    }

    /**
     * Impor Warga MTA Menjadi Pemuda PMD Sragen
     */
    public function import()
    {
        $this->ensureSuperadmin();

        $wargaUuid = trim((string) $this->request->getPost('warga_uuid'));
        $cabangId  = (int) $this->request->getPost('cabang_id');

        if (empty($wargaUuid) || $cabangId <= 0) {
            $msg = 'Warga MTA dan Cabang PMD tujuan wajib dipilih.';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success'  => false,
                    'message'  => $msg,
                    'csrfHash' => csrf_hash(),
                ]);
            }
            return redirect()->back()->with('error', $msg);
        }

        // Ambil detail lengkap warga dari API MTA
        $detailRes = $this->apiService->getWargaDetail($wargaUuid);
        if (!($detailRes['success'] ?? false) || empty($detailRes['data'])) {
            $msg = $detailRes['message'] ?? 'Gagal mengambil data warga dari server API MTA.';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success'  => false,
                    'message'  => $msg,
                    'csrfHash' => csrf_hash(),
                ]);
            }
            return redirect()->back()->with('error', $msg);
        }

        // Jalankan proses sinkronisasi / pendaftaran ke database PMD
        $result = $this->syncService->syncWargaToPemuda(
            $detailRes['data'],
            $cabangId,
            (int) session()->get('user_id')
        );

        $result['csrfHash'] = csrf_hash();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($result);
        }

        if ($result['success']) {
            return redirect()->to(base_url('admin/pemuda/detail/' . $result['pemuda_id']))
                             ->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }
}
