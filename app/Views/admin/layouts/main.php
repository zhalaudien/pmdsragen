<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token-name" content="<?= csrf_token() ?>">
    <meta name="csrf-token-hash" content="<?= csrf_hash() ?>">
    <title><?= esc($title ?? 'Dashboard') ?> | Pemuda MTA Perwakilan Sragen</title>

    <!-- Google Font: Source Sans Pro & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,600,700&display=fallback">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Bootstrap Icons (for backward compatibility) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- AdminLTE 3.2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

    <!-- Custom Admin Stylesheet -->
    <link rel="stylesheet" href="<?= base_url('css/admin.css') ?>">

    <!-- Chart.js 4.4 -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed text-sm">
<div class="wrapper">

    <!-- NAVBAR (HEADER) -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom-0 shadow-sm">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button" title="Toggle Sidebar">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="<?= base_url('admin/dashboard') ?>" class="nav-link">
                    <i class="fas fa-home mr-1"></i> Dashboard
                </a>
            </li>
            <li class="nav-item d-none d-md-inline-block">
                <a href="<?= base_url('admin/pemuda') ?>" class="nav-link">
                    <i class="fas fa-users mr-1"></i> Data Pemuda
                </a>
            </li>
            <li class="nav-item d-none d-lg-inline-block">
                <a href="<?= base_url('pendataan') ?>" target="_blank" class="nav-link text-info">
                    <i class="fas fa-external-link-alt mr-1"></i> Form Publik
                </a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto align-items-center">
            <!-- Scope badge -->
            <li class="nav-item mr-2 d-none d-md-block">
                <?php 
                    $currRole    = session()->get('role');
                    $currWilayah = session()->get('wilayah_name') ?? ('Wilayah ' . session()->get('wilayah_id'));
                    $currCabang  = session()->get('cabang_name') ?? ('Cabang ' . session()->get('cabang_id'));
                ?>
                <?php if ($currRole === 'superadmin'): ?>
                    <span class="badge badge-dark px-3 py-2 font-weight-normal" style="font-size: 0.8rem;">
                        <i class="fas fa-globe text-info mr-1"></i> Scope: <strong>Seluruh Sistem</strong>
                    </span>
                <?php elseif ($currRole === 'admin_wilayah'): ?>
                    <span class="badge badge-primary px-3 py-2 font-weight-normal" style="font-size: 0.8rem;">
                        <i class="fas fa-map-marker-alt mr-1"></i> Scope: <strong><?= esc($currWilayah) ?></strong>
                    </span>
                <?php else: ?>
                    <span class="badge badge-success px-3 py-2 font-weight-normal" style="font-size: 0.8rem;">
                        <i class="fas fa-sitemap mr-1"></i> Scope: <strong><?= esc($currCabang) ?></strong>
                    </span>
                <?php endif; ?>
            </li>

            <!-- Fullscreen Toggle -->
            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button" title="Fullscreen Mode">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>

            <!-- User Dropdown Menu -->
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" data-toggle="dropdown">
                    <div class="user-avatar-circle mr-2">
                        <?= strtoupper(substr(session()->get('name') ?? 'A', 0, 1)) ?>
                    </div>
                    <span class="d-none d-md-inline font-weight-bold text-dark">
                        <?= esc(session()->get('name') ?? session()->get('username')) ?>
                    </span>
                    <i class="fas fa-angle-down ml-1 text-muted text-xs"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right shadow-lg border-0">
                    <!-- User image -->
                    <li class="user-header bg-primary">
                        <div class="user-avatar-large mx-auto mb-2 elevation-2">
                            <?= strtoupper(substr(session()->get('name') ?? 'A', 0, 1)) ?>
                        </div>
                        <p class="font-weight-bold mb-0">
                            <?= esc(session()->get('name')) ?>
                            <small class="d-block text-white-50"><?= esc(session()->get('email')) ?></small>
                        </p>
                        <span class="badge badge-light text-primary mt-1 font-weight-bold">
                            <?= ($currRole === 'superadmin') ? 'Super Administrator' : (($currRole === 'admin_wilayah') ? 'Admin Wilayah' : 'Admin Cabang') ?>
                        </span>
                    </li>
                    <!-- Menu Body -->
                    <li class="user-body bg-light py-2">
                        <div class="row text-center text-xs">
                            <div class="col-6">
                                <a href="<?= base_url('admin/pemuda') ?>" class="text-dark font-weight-semibold">
                                    <i class="fas fa-users text-primary"></i> Data Pemuda
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="<?= base_url('pendataan') ?>" target="_blank" class="text-dark font-weight-semibold">
                                    <i class="fas fa-edit text-success"></i> Form Publik
                                </a>
                            </div>
                        </div>
                    </li>
                    <!-- Menu Footer-->
                    <li class="user-footer d-flex justify-content-between">
                        <a href="<?= base_url('/') ?>" target="_blank" class="btn btn-default btn-flat btn-sm">
                            <i class="fas fa-globe mr-1"></i> Web Depan
                        </a>
                        <a href="<?= base_url('admin/logout') ?>" class="btn btn-danger btn-flat btn-sm">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- MAIN SIDEBAR CONTAINER -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="<?= base_url('admin/dashboard') ?>" class="brand-link">
            <div class="brand-image img-circle elevation-2 bg-primary d-flex align-items-center justify-content-center text-white font-weight-bold" style="width: 33px; height: 33px; font-size: 15px; opacity: .95;">
                <i class="fas fa-users"></i>
            </div>
            <span class="brand-text font-weight-bold pl-1" style="letter-spacing: 0.3px;">Pemuda MTA</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
                <div class="image">
                    <div class="user-avatar-circle elevation-1">
                        <?= strtoupper(substr(session()->get('name') ?? 'A', 0, 1)) ?>
                    </div>
                </div>
                <div class="info">
                    <a href="<?= base_url('admin/dashboard') ?>" class="d-block font-weight-bold text-truncate" style="max-width: 150px;">
                        <?= esc(session()->get('name')) ?>
                    </a>
                    <span class="badge badge-pill <?= ($currRole === 'superadmin') ? 'badge-danger' : (($currRole === 'admin_wilayah') ? 'badge-info' : 'badge-success') ?>" style="font-size: 0.68rem;">
                        <?= ($currRole === 'superadmin') ? 'Super Admin' : (($currRole === 'admin_wilayah') ? 'Admin Wilayah' : 'Admin Cabang') ?>
                    </span>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
                    
                    <li class="nav-header">MENU UTAMA</li>
                    
                    <li class="nav-item">
                        <a href="<?= base_url('admin/dashboard') ?>" 
                           class="nav-link <?= (url_is('admin/dashboard') || url_is('admin')) ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= base_url('admin/pemuda') ?>" 
                           class="nav-link <?= (url_is('admin/pemuda') || url_is('admin/pemuda/detail*') || url_is('admin/pemuda/edit*')) ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Data Pemuda</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= base_url('admin/pemuda/tambah') ?>" 
                           class="nav-link <?= url_is('admin/pemuda/tambah') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-user-plus"></i>
                            <p>Tambah Pemuda</p>
                        </a>
                    </li>

                    <?php if (session()->get('role') === 'superadmin'): ?>
                        <li class="nav-header mt-2">MASTER &amp; PENGATURAN</li>

                        <li class="nav-item">
                            <a href="<?= base_url('admin/pemuda/import') ?>" 
                               class="nav-link <?= url_is('admin/pemuda/import*') ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-file-excel text-success"></i>
                                <p>Import Data Excel</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="<?= base_url('admin/wilayah') ?>" 
                               class="nav-link <?= url_is('admin/wilayah*') ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-map-marked-alt text-warning"></i>
                                <p>Master Wilayah</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="<?= base_url('admin/cabang') ?>" 
                               class="nav-link <?= url_is('admin/cabang*') ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-sitemap text-info"></i>
                                <p>Master Cabang</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="<?= base_url('admin/users') ?>" 
                               class="nav-link <?= url_is('admin/users*') ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-user-shield text-danger"></i>
                                <p>Pengguna &amp; Akses</p>
                            </a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-header mt-2">TAUTAN SISTEM</li>

                    <li class="nav-item">
                        <a href="<?= base_url('/') ?>" target="_blank" class="nav-link">
                            <i class="nav-icon fas fa-globe text-primary"></i>
                            <p>Beranda Web <i class="fas fa-external-link-alt right text-xs"></i></p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= base_url('pendataan') ?>" target="_blank" class="nav-link">
                            <i class="nav-icon fas fa-clipboard-list text-info"></i>
                            <p>Formulir Pendataan <i class="fas fa-external-link-alt right text-xs"></i></p>
                        </a>
                    </li>

                    <li class="nav-item mt-3">
                        <a href="<?= base_url('admin/logout') ?>" class="nav-link bg-danger text-white">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p class="font-weight-bold">Logout / Keluar</p>
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>

    <!-- CONTENT WRAPPER. CONTAINS PAGE CONTENT -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header pb-2">
            <div class="container-fluid">
                <div class="row mb-2 align-items-center">
                    <div class="col-sm-6">
                        <h1 class="m-0 font-weight-bold text-dark" style="font-size: 1.5rem;">
                            <?= esc($title ?? 'Dashboard') ?>
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right bg-transparent p-0 mb-0" style="font-size: 0.82rem;">
                            <li class="breadcrumb-item">
                                <a href="<?= base_url('admin/dashboard') ?>" class="text-primary font-weight-semibold">
                                    <i class="fas fa-home mr-1"></i> Admin
                                </a>
                            </li>
                            <li class="breadcrumb-item active text-muted"><?= esc($title ?? 'Dashboard') ?></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">

                <!-- FLASH ALERTS -->
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h5><i class="icon fas fa-check"></i> Berhasil!</h5>
                        <?= esc(session()->getFlashdata('success')) ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h5><i class="icon fas fa-ban"></i> Terjadi Kesalahan!</h5>
                        <?= esc(session()->getFlashdata('error')) ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h5><i class="icon fas fa-exclamation-triangle"></i> Validasi Gagal:</h5>
                        <ul class="mb-0 pl-3">
                            <?php foreach (session()->getFlashdata('errors') as $err): ?>
                                <li><?= esc($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- RENDER CHILD VIEW CONTENT -->
                <?= $this->renderSection('content') ?>

            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- MAIN FOOTER -->
    <footer class="main-footer">
        <div class="float-right d-none d-sm-inline">
            Sistem Pendataan Pemuda &bull; CI4 Native MVC
        </div>
        <strong>&copy; <?= date('Y') ?> <a href="<?= base_url('/') ?>" class="text-primary font-weight-bold">Pemuda MTA Perwakilan Sragen</a>.</strong> All rights reserved.
    </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<!-- Bootstrap 4 Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE 3.2 App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<!-- Compatibility Shim for Bootstrap 5 data-bs-* attributes & Modals -->
<script>
    $(document).ready(function() {
        // Global CSRF configuration for AJAX requests
        const csrfHeader = '<?= config('Security')->headerName ?>';
        const csrfHash = $('meta[name="csrf-token-hash"]').attr('content');
        
        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                if (!/^(GET|HEAD|OPTIONS|TRACE)$/i.test(settings.type) && !this.crossDomain) {
                    xhr.setRequestHeader(csrfHeader, $('meta[name="csrf-token-hash"]').attr('content') || csrfHash);
                }
            }
        });

        // Auto-bridge data-bs-toggle="modal" & data-bs-target
        $(document).on('click', '[data-bs-toggle="modal"]', function(e) {
            e.preventDefault();
            var target = $(this).attr('data-bs-target');
            if (target) {
                $(target).modal('show');
            }
        });
        
        // Auto-bridge data-bs-dismiss="modal"
        $(document).on('click', '[data-bs-dismiss="modal"]', function(e) {
            e.preventDefault();
            $(this).closest('.modal').modal('hide');
        });

        // Auto-bridge data-bs-toggle="collapse"
        $(document).on('click', '[data-bs-toggle="collapse"]', function(e) {
            var target = $(this).attr('data-bs-target');
            if (target) {
                $(target).collapse('toggle');
            }
        });
    });
</script>

<?= $this->renderSection('scripts') ?>
</body>
</html>
