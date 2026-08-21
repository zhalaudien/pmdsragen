<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?? 'Sistem Pendataan Pemuda' ?> - PMD Sragen</title>
    <meta name="description" content="Sistem Informasi dan Pendataan Pemuda Daerah Sragen">
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- App Global Stylesheet -->
    <link rel="stylesheet" href="<?= base_url('css/main.css') ?>">
    
    <?= $this->renderSection('styles') ?>
</head>
<body>

    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-pmd sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= base_url('pendataan') ?>">
                <div class="rounded-circle bg-white text-danger p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 38px; height: 38px;">
                    <i class="bi bi-people-fill fs-5 text-pmd-red"></i>
                </div>
                <div>
                    <span class="navbar-brand-title">Sistem Pendataan Pemuda</span>
                    <span class="navbar-brand-subtitle">Kabupaten Sragen &bull; Form Pendataan Resmi</span>
                </div>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navMenu">
                <ul class="navbar-nav align-items-lg-center gap-lg-2">
                    <li class="nav-item">
                        <a class="nav-link text-white fw-medium active" href="<?= base_url('pendataan') ?>">
                            <i class="bi bi-ui-checks me-1"></i> Form Pendataan
                        </a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-outline-light btn-sm rounded-pill px-3 py-1 fw-semibold" href="<?= base_url('admin/dashboard') ?>">
                            <i class="bi bi-shield-lock me-1"></i> Portal Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="py-4 py-md-5">
        <div class="container">
            <?= $this->renderSection('content') ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer-pmd">
        <div class="container">
            <div class="row align-items-center gy-2">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0">&copy; <?= date('Y') ?> <strong>Sistem Pendataan Pemuda</strong>. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <span class="badge bg-light text-secondary border px-3 py-2">
                        <i class="bi bi-shield-check text-success me-1"></i> Data Terenkripsi & Terverifikasi
                    </span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5.3 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
