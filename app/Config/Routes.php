<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ==========================================
// 1. PUBLIC ROUTES
// ==========================================
$routes->get('/', 'Home::index');
$routes->get('pendataan', 'Pendataan::index');
$routes->get('pendataan/search-warga', 'Pendataan::searchWarga');
$routes->get('pendataan/warga-detail/(:segment)', 'Pendataan::wargaDetail/$1');
$routes->post('pendataan/simpan', 'Pendataan::simpan');
$routes->post('pendataan/check-data', 'Pendataan::checkData');
$routes->post('pendataan/check-duplicate', 'Pendataan::checkData');
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
        $routes->get('detail/(:num)', 'Admin\Cabang::detail/$1');
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

    // Data Warga MTA Sragen dari api.mta.or.id (Superadmin)
    $routes->group('warga-mta', ['filter' => 'role:superadmin'], function ($routes) {
        $routes->get('/', 'Admin\WargaMta::index');
        $routes->get('detail/(:segment)', 'Admin\WargaMta::detail/$1');
        $routes->post('import', 'Admin\WargaMta::import');
    });

    // Integrasi & Sinkronisasi Database Warga MTA (Superadmin)
    $routes->group('mta-sync', ['filter' => 'role:superadmin'], function ($routes) {
        $routes->get('/', 'Admin\MtaSync::index');
        $routes->get('test-connection', 'Admin\MtaSync::testConnection');
        $routes->post('sync-cabang', 'Admin\MtaSync::syncCabang');
        $routes->get('search-warga', 'Admin\MtaSync::searchWarga');
        $routes->get('warga-detail/(:segment)', 'Admin\MtaSync::wargaDetail/$1');
        $routes->get('cabang-warga/(:segment)', 'Admin\MtaSync::cabangWarga/$1');
        $routes->post('import-warga', 'Admin\MtaSync::importWarga');
        $routes->post('sync-pemuda/(:num)', 'Admin\MtaSync::syncPemuda/$1');
        $routes->post('sync-verify-all', 'Admin\MtaSync::syncVerifyAll');
    });

    // Kelola Konten Beranda / Homepage (Superadmin)
    $routes->group('homepage', ['filter' => 'role:superadmin'], function ($routes) {
        $routes->get('/', 'Admin\HomepageSetting::index');
        $routes->post('update', 'Admin\HomepageSetting::update');
        $routes->post('reset', 'Admin\HomepageSetting::reset');
    });

    // Ajax Helpers
    $routes->get('ajax/cabang/(:num)', 'Admin\Ajax::getCabangByWilayah/$1');
    $routes->get('ajax/villages/(:num)', 'Admin\Ajax::getVillagesByDistrict/$1');
});
