<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Dashboard') ?> | Pemuda MTA Perwakilan Sragen</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Admin Master Stylesheet -->
    <link rel="stylesheet" href="<?= base_url('css/admin.css') ?>">
    
    <!-- Chart.js 4.4 -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- SIDEBAR -->
    <aside id="sidebar">
        <a href="<?= base_url('admin/dashboard') ?>" class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <div>Pemuda MTA Sragen</div>
                <div style="font-size: 0.68rem; color: #64748b; font-weight: 500;">Perwakilan Sragen</div>
            </div>
        </a>

        <div class="sidebar-user-box">
            <div class="user-avatar">
                <?= strtoupper(substr(session()->get('name') ?? 'A', 0, 1)) ?>
            </div>
            <div class="overflow-hidden">
                <div class="text-white fw-semibold text-truncate small"><?= esc(session()->get('name')) ?></div>
                <div>
                    <?php 
                        $role = session()->get('role');
                        if ($role === 'superadmin') {
                            echo '<span class="badge badge-role-superadmin rounded-pill" style="font-size: 0.68rem;">Super Admin</span>';
                        } elseif ($role === 'admin_wilayah') {
                            echo '<span class="badge badge-role-wilayah rounded-pill" style="font-size: 0.68rem;">Admin Wilayah</span>';
                        } else {
                            echo '<span class="badge badge-role-cabang rounded-pill" style="font-size: 0.68rem;">Admin Cabang</span>';
                        }
                    ?>
                </div>
            </div>
        </div>

        <div class="sidebar-menu">
            <div class="menu-header">Menu Utama</div>
            
            <a href="<?= base_url('admin/dashboard') ?>" 
               class="nav-link-custom <?= (url_is('admin/dashboard') || url_is('admin')) ? 'active' : '' ?>">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>

            <a href="<?= base_url('admin/pemuda') ?>" 
               class="nav-link-custom <?= (url_is('admin/pemuda') || url_is('admin/pemuda/detail*') || url_is('admin/pemuda/edit*')) ? 'active' : '' ?>">
                <i class="bi bi-person-lines-fill"></i>
                <span>Data Pemuda</span>
            </a>

            <a href="<?= base_url('admin/pemuda/tambah') ?>" 
               class="nav-link-custom <?= url_is('admin/pemuda/tambah') ? 'active' : '' ?>">
                <i class="bi bi-person-plus-fill"></i>
                <span>Tambah Pemuda</span>
            </a>

            <?php if (session()->get('role') === 'superadmin'): ?>
                <div class="menu-header mt-3">Master &amp; Import (Superadmin)</div>

                <a href="<?= base_url('admin/pemuda/import') ?>" 
                   class="nav-link-custom <?= url_is('admin/pemuda/import*') ? 'active' : '' ?>">
                    <i class="bi bi-file-earmark-arrow-up-fill"></i>
                    <span>Import Data Pemuda</span>
                </a>

                <a href="<?= base_url('admin/wilayah') ?>" 
                   class="nav-link-custom <?= url_is('admin/wilayah*') ? 'active' : '' ?>">
                    <i class="bi bi-geo-alt-fill"></i>
                    <span>Master Wilayah</span>
                </a>

                <a href="<?= base_url('admin/cabang') ?>" 
                   class="nav-link-custom <?= url_is('admin/cabang*') ? 'active' : '' ?>">
                    <i class="bi bi-diagram-3-fill"></i>
                    <span>Master Cabang</span>
                </a>

                <a href="<?= base_url('admin/users') ?>" 
                   class="nav-link-custom <?= url_is('admin/users*') ? 'active' : '' ?>">
                    <i class="bi bi-shield-lock-fill"></i>
                    <span>Pengguna & Hak Akses</span>
                </a>
            <?php endif; ?>

            <div class="menu-header mt-3">Tautan Publik</div>

            <a href="<?= base_url('/') ?>" target="_blank" class="nav-link-custom">
                <i class="bi bi-house-door"></i>
                <span>Beranda Web</span>
            </a>

            <a href="<?= base_url('pendataan') ?>" target="_blank" class="nav-link-custom">
                <i class="bi bi-ui-checks"></i>
                <span>Formulir Pendataan</span>
            </a>

            <a href="<?= base_url('admin/logout') ?>" class="nav-link-custom text-danger mt-3">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout / Keluar</span>
            </a>
        </div>
    </aside>

    <!-- MAIN CONTENT WRAPPER -->
    <div id="main-wrapper">
        <!-- TOPBAR -->
        <header id="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light d-lg-none p-2 rounded-3 border" id="btnToggleSidebar">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div>
                    <h5 class="fw-bold text-dark mb-0"><?= esc($title ?? 'Dashboard') ?></h5>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0" style="font-size: 0.78rem;">
                            <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>" class="text-decoration-none text-muted">Admin</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?= esc($title ?? 'Dashboard') ?></li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- Scope pill -->
                <?php if (session()->get('role') === 'superadmin'): ?>
                    <div class="d-none d-md-flex align-items-center gap-2 px-3 py-1 scope-pill-superadmin rounded-pill">
                        <i class="bi bi-globe2"></i> Scope: Seluruh Sistem
                    </div>
                <?php elseif (session()->get('role') === 'admin_wilayah'): ?>
                    <div class="d-none d-md-flex align-items-center gap-2 px-3 py-1 bg-primary-subtle rounded-pill border" style="font-size: 0.8rem; font-weight: 600;">
                        <i class="bi bi-geo-alt"></i> Scope: Wilayah <?= esc(session()->get('wilayah_id')) ?>
                    </div>
                <?php else: ?>
                    <div class="d-none d-md-flex align-items-center gap-2 px-3 py-1 bg-success-subtle rounded-pill border" style="font-size: 0.8rem; font-weight: 600;">
                        <i class="bi bi-diagram-3"></i> Scope: Cabang <?= esc(session()->get('cabang_id')) ?>
                    </div>
                <?php endif; ?>

                <!-- User Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-light d-flex align-items-center gap-2 rounded-pill px-3 py-1 border shadow-sm" 
                            type="button" data-bs-toggle="dropdown">
                        <div class="user-avatar user-avatar-sm">
                            <?= strtoupper(substr(session()->get('name') ?? 'A', 0, 1)) ?>
                        </div>
                        <span class="small fw-semibold d-none d-sm-inline"><?= esc(session()->get('username')) ?></span>
                        <i class="bi bi-chevron-down small text-muted"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2 p-2" style="min-width: 220px;">
                        <li class="px-3 py-2 border-bottom">
                            <div class="fw-bold text-dark"><?= esc(session()->get('name')) ?></div>
                            <div class="small text-muted"><?= esc(session()->get('email')) ?></div>
                        </li>
                        <li><a class="dropdown-item py-2 mt-1 rounded-2" href="<?= base_url('admin/pemuda') ?>"><i class="bi bi-person-lines-fill me-2 text-primary"></i> Data Pemuda</a></li>
                        <li><a class="dropdown-item py-2 rounded-2" href="<?= base_url('/') ?>" target="_blank"><i class="bi bi-house-door me-2 text-primary"></i> Beranda Web</a></li>
                        <li><a class="dropdown-item py-2 rounded-2" href="<?= base_url('pendataan') ?>" target="_blank"><i class="bi bi-ui-checks me-2 text-info"></i> Formulir Publik</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item py-2 text-danger rounded-2" href="<?= base_url('admin/logout') ?>"><i class="bi bi-box-arrow-right me-2"></i> Keluar</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- FLASH ALERTS -->
        <div class="px-4 pt-3">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm rounded-3" role="alert">
                    <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                    <div><?= esc(session()->getFlashdata('success')) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-sm rounded-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                    <div><?= esc(session()->getFlashdata('error')) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3" role="alert">
                    <div class="fw-bold mb-1"><i class="bi bi-exclamation-octagon-fill me-1"></i> Terjadi kesalahan validasi:</div>
                    <ul class="mb-0 ps-3">
                        <?php foreach (session()->getFlashdata('errors') as $err): ?>
                            <li><?= esc($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        </div>

        <!-- CONTENT -->
        <main class="content-area">
            <?= $this->renderSection('content') ?>
        </main>

        <!-- FOOTER -->
        <footer class="bg-white border-top py-3 px-4 text-center text-md-between d-flex flex-column flex-md-row justify-content-between align-items-center small text-muted">
            <div>&copy; <?= date('Y') ?> <strong>Pemuda MTA Perwakilan Sragen</strong> — Sistem Pendataan &amp; Manajemen Pemuda.</div>
            <div class="mt-2 mt-md-0">CodeIgniter 4 Native MVC &bull; Hak Akses: <strong><?= esc(session()->get('role')) ?></strong></div>
        </footer>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sidebar Mobile Toggle Script -->
    <script>
        const btnToggleSidebar = document.getElementById('btnToggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');

        if (btnToggleSidebar) {
            btnToggleSidebar.addEventListener('click', function() {
                sidebar.classList.toggle('show');
                sidebarBackdrop.classList.toggle('show');
            });
        }

        if (sidebarBackdrop) {
            sidebarBackdrop.addEventListener('click', function() {
                sidebar.classList.remove('show');
                sidebarBackdrop.classList.remove('show');
            });
        }
    </script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
