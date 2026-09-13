<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PemudaModel;
use App\Models\WilayahModel;
use App\Models\CabangModel;

class Persebaran extends BaseController
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
     * Dashboard Persebaran Data Pemuda
     * Menampilkan analisis persebaran data: Element Dakwah, Sekolah/Pendidikan,
     * Bakat/Keahlian, Minat, Ketenagakerjaan/Wirausaha, Demografi Usia, dan Wilayah/Cabang.
     */
    public function index(): string
    {
        $scope = [
            'role'       => session()->get('role'),
            'wilayah_id' => session()->get('wilayah_id'),
            'cabang_id'  => session()->get('cabang_id'),
        ];

        // Filters dari query string
        $filters = [
            'wilayah_id'  => $this->request->getGet('wilayah_id'),
            'cabang_id'   => $this->request->getGet('cabang_id'),
            'gender'      => $this->request->getGet('gender'),
            'status_data' => $this->request->getGet('status_data') ?? 'active',
        ];

        // Enforce role scope pada filter
        if (in_array($scope['role'], ['admin_wilayah', 'admin_wilayah_pemuda'], true)) {
            $filters['wilayah_id'] = $scope['wilayah_id'];
        } elseif ($scope['role'] === 'admin_cabang') {
            $filters['wilayah_id'] = $scope['wilayah_id'];
            $filters['cabang_id']  = $scope['cabang_id'];
        }

        if (in_array($scope['role'], ['admin_wilayah_pemuda', 'admin_pemuda'], true)) {
            $filters['gender'] = 'L';
        } elseif ($scope['role'] === 'admin_pemudi') {
            $filters['gender'] = 'P';
        }

        // Ambil data statistik persebaran
        $stats = $this->pemudaModel->getPersebaranStats($scope, $filters);

        // Reference lists untuk dropdown filter (disesuaikan dengan scope)
        $wilayahBuilder = $this->wilayahModel->orderBy('id', 'ASC');
        if (in_array($scope['role'], ['admin_wilayah', 'admin_wilayah_pemuda', 'admin_cabang'], true) && !empty($scope['wilayah_id'])) {
            $wilayahBuilder->where('id', (int) $scope['wilayah_id']);
        }
        $wilayahList = $wilayahBuilder->findAll();

        $cabangBuilder = $this->cabangModel->orderBy('name', 'ASC');
        if ($scope['role'] === 'admin_cabang' && !empty($scope['cabang_id'])) {
            $cabangBuilder->where('id', (int) $scope['cabang_id']);
        } elseif (in_array($scope['role'], ['admin_wilayah', 'admin_wilayah_pemuda'], true) && !empty($scope['wilayah_id'])) {
            $cabangBuilder->where('wilayah_id', (int) $scope['wilayah_id']);
        } elseif (!empty($filters['wilayah_id'])) {
            $cabangBuilder->where('wilayah_id', (int) $filters['wilayah_id']);
        }
        $cabangList = $cabangBuilder->findAll();

        $data = [
            'title'       => 'Dashboard Persebaran Data Pemuda',
            'stats'       => $stats,
            'filters'     => $filters,
            'scope'       => $scope,
            'wilayahList' => $wilayahList,
            'cabangList'  => $cabangList,
            'user'        => session()->get(),
        ];

        return view('admin/persebaran/index', $data);
    }
}
