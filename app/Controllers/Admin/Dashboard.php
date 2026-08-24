<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PemudaModel;
use App\Models\WilayahModel;
use App\Models\CabangModel;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    protected PemudaModel $pemudaModel;
    protected WilayahModel $wilayahModel;
    protected CabangModel $cabangModel;
    protected UserModel $userModel;

    public function __construct()
    {
        $this->pemudaModel  = new PemudaModel();
        $this->wilayahModel = new WilayahModel();
        $this->cabangModel  = new CabangModel();
        $this->userModel    = new UserModel();
    }

    /**
     * Dashboard Utama Super Admin
     */
    public function index(): string
    {
        $scope = [
            'role'       => session()->get('role'),
            'wilayah_id' => session()->get('wilayah_id'),
            'cabang_id'  => session()->get('cabang_id'),
        ];

        $stats = $this->pemudaModel->getDashboardStats($scope);
        $wilayahList = $this->wilayahModel->orderBy('id', 'ASC')->findAll();

        $data = [
            'title'       => 'Dashboard Super Admin',
            'stats'       => $stats,
            'wilayahList' => $wilayahList,
            'user'        => session()->get(),
        ];

        return view('admin/dashboard/index', $data);
    }
}
