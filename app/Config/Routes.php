<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ==========================================
// 1. PUBLIC ROUTES
// ==========================================
$routes->get('/', 'Home::index');
$routes->get('pendataan', 'Pendataan::index');
$routes->post('pendataan/simpan', 'Pendataan::simpan');
$routes->get('pendataan/sukses', 'Pendataan::sukses');

// Public API for dropdowns
$routes->get('api/cabang/(:num)', 'Admin\Ajax::getCabangByWilayah/$1');
$routes->get('api/villages/(:num)', 'Admin\Ajax::getVillagesByDistrict/$1');

// ==========================================
// 2. AUTHENTICATION ROUTES
// ==========================================
$routes->get('admin/login', 'Auth::login');
$routes->post('admin/login', 'Auth::login');
$routes->get('admin/logout', 'Auth::logout');

// ==========================================
// 3. ADMIN PANEL (PROTECTED)
// ==========================================
$routes->group('admin', ['filter' => 'auth'], function ($routes) {
    // Dashboard
    $routes->get('/', 'Admin\Dashboard::index');
    $routes->get('dashboard', 'Admin\Dashboard::index');

    // Manajemen Data Pemuda
    $routes->group('pemuda', function ($routes) {
        $routes->get('/', 'Admin\Pemuda::index');
        $routes->get('detail/(:num)', 'Admin\Pemuda::detail/$1');
        $routes->get('tambah', 'Admin\Pemuda::tambah');
        $routes->post('simpan', 'Admin\Pemuda::simpan');
        $routes->get('edit/(:num)', 'Admin\Pemuda::edit/$1');
        $routes->post('update/(:num)', 'Admin\Pemuda::update/$1');
        $routes->post('verifikasi/(:num)', 'Admin\Pemuda::verifikasi/$1');
        $routes->post('archive/(:num)', 'Admin\Pemuda::archive/$1');
        $routes->post('delete/(:num)', 'Admin\Pemuda::delete/$1');
        $routes->get('export', 'Admin\Pemuda::export');
        $routes->get('cetak/(:num)', 'Admin\Pemuda::cetak/$1');
        
        // Import Data Pemuda (Khusus Superadmin)
        $routes->get('import', 'Admin\Pemuda::import', ['filter' => 'role:superadmin']);
        $routes->post('import', 'Admin\Pemuda::prosesImport', ['filter' => 'role:superadmin']);
        $routes->get('template-import', 'Admin\Pemuda::templateImport', ['filter' => 'role:superadmin']);
    });

    // Master Wilayah (Superadmin)
    $routes->group('wilayah', ['filter' => 'role:superadmin'], function ($routes) {
        $routes->get('/', 'Admin\Wilayah::index');
        $routes->post('simpan', 'Admin\Wilayah::simpan');
        $routes->post('update/(:num)', 'Admin\Wilayah::update/$1');
        $routes->post('delete/(:num)', 'Admin\Wilayah::delete/$1');
    });

    // Master Cabang (Superadmin)
    $routes->group('cabang', ['filter' => 'role:superadmin'], function ($routes) {
        $routes->get('/', 'Admin\Cabang::index');
        $routes->post('simpan', 'Admin\Cabang::simpan');
        $routes->post('update/(:num)', 'Admin\Cabang::update/$1');
        $routes->post('delete/(:num)', 'Admin\Cabang::delete/$1');
    });

    // Master Users & Roles (Superadmin)
    $routes->group('users', ['filter' => 'role:superadmin'], function ($routes) {
        $routes->get('/', 'Admin\Users::index');
        $routes->post('simpan', 'Admin\Users::simpan');
        $routes->post('update/(:num)', 'Admin\Users::update/$1');
        $routes->post('delete/(:num)', 'Admin\Users::delete/$1');
    });

    // Ajax Helpers
    $routes->get('ajax/cabang/(:num)', 'Admin\Ajax::getCabangByWilayah/$1');
    $routes->get('ajax/villages/(:num)', 'Admin\Ajax::getVillagesByDistrict/$1');
});
