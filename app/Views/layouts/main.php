<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? ($this->renderSection('title') ? $this->renderSection('title') . ' - Pemuda MTA Perwakilan Sragen' : 'Pemuda MTA Perwakilan Sragen | Pusat Informasi & Pendataan')) ?></title>
    <meta name="description" content="Pusat Informasi dan Sistem Pendataan Pemuda MTA Perwakilan Sragen. Menghimpun potensi dan karya pemuda di 4 Wilayah dan 61 Cabang se-Kabupaten Sragen.">

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
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= base_url('/') ?>">
                <div class="navbar-brand-icon">
                    <i class="bi bi-people-fill fs-5"></i>
                </div>
                <div>
                    <span class="navbar-brand-title">Pemuda MTA Perwakilan Sragen</span>
                    <span class="navbar-brand-subtitle">Sistem Informasi & Pendataan Pemuda</span>
                </div>
            </a>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navMenu">
                <ul class="navbar-nav align-items-lg-center gap-lg-1 my-2 my-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?= (url_is('/') || url_is('')) ? 'active fw-semibold' : '' ?>" href="<?= base_url('/') ?>">
                            <i class="bi bi-house-door me-1"></i> Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/#tentang') ?>">
                            <i class="bi bi-info-circle me-1"></i> Profil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/#wilayah') ?>">
                            <i class="bi bi-diagram-3 me-1"></i> 4 Wilayah & Cabang
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/#program') ?>">
                            <i class="bi bi-grid me-1"></i> Program
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/#faq') ?>">
                            <i class="bi bi-question-circle me-1"></i> FAQ
                        </a>
                    </li>
                    <li class="nav-item ms-lg-2 my-1 my-lg-0">
                        <a class="btn btn-warning btn-sm rounded-pill px-3 py-1 fw-bold shadow-sm <?= url_is('pendataan*') ? 'border border-2 border-white' : '' ?>" href="<?= base_url('pendataan') ?>">
                            <i class="bi bi-ui-checks me-1"></i> Form Pendataan
                        </a>
                    </li>
                    <li class="nav-item ms-lg-1 my-1 my-lg-0">
                        <a class="btn btn-outline-light btn-sm rounded-pill px-3 py-1 fw-semibold" href="<?= base_url('admin/login') ?>">
                            <i class="bi bi-shield-lock me-1"></i> Portal Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main>
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <footer class="footer-pmd pt-5 pb-3">
        <div class="container">
            <div class="row g-4 pb-4 border-bottom">
                <!-- Col 1: Profil Brand -->
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="rounded-circle bg-pmd-red text-white p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 36px; height: 36px; background-color: #dc2626;">
                            <i class="bi bi-people-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">Pemuda MTA Perwakilan Sragen</h6>
                            <small class="text-muted">Majelis Tafsir Al-Qur'an (MTA)</small>
                        </div>
                    </div>
                    <p class="text-muted small pe-lg-3 mb-3">
                        Wadah pengkaderan dan pengembangan generasi muda berlandaskan Al-Qur'an dan As-Sunnah. Menghimpun potensi pemuda di 4 Wilayah dan 61 Cabang di seluruh wilayah Kabupaten Sragen.
                    </p>
                    <div class="d-flex gap-2">
                        <a href="https://mta.or.id" target="_blank" class="btn btn-sm btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;" title="Website MTA Pusat">
                            <i class="bi bi-globe"></i>
                        </a>
                        <a href="https://instagram.com" target="_blank" class="btn btn-sm btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;" title="Instagram Pemuda">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="https://youtube.com" target="_blank" class="btn btn-sm btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;" title="YouTube MTA TV">
                            <i class="bi bi-youtube"></i>
                        </a>
                    </div>
                </div>

                <!-- Col 2: Navigasi Cepat -->
                <div class="col-lg-2 col-md-6 col-6">
                    <h6 class="fw-bold text-dark mb-3">Navigasi</h6>
                    <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                        <li><a href="<?= base_url('/') ?>" class="text-decoration-none text-muted hover-red"><i class="bi bi-chevron-right me-1 text-danger small"></i> Beranda</a></li>
                        <li><a href="<?= base_url('/#tentang') ?>" class="text-decoration-none text-muted hover-red"><i class="bi bi-chevron-right me-1 text-danger small"></i> Profil & Visi</a></li>
                        <li><a href="<?= base_url('/#wilayah') ?>" class="text-decoration-none text-muted hover-red"><i class="bi bi-chevron-right me-1 text-danger small"></i> 4 Wilayah & Cabang</a></li>
                        <li><a href="<?= base_url('/#program') ?>" class="text-decoration-none text-muted hover-red"><i class="bi bi-chevron-right me-1 text-danger small"></i> Program Kerja</a></li>
                        <li><a href="<?= base_url('/#faq') ?>" class="text-decoration-none text-muted hover-red"><i class="bi bi-chevron-right me-1 text-danger small"></i> FAQ Pendataan</a></li>
                    </ul>
                </div>

                <!-- Col 3: Layanan & Form -->
                <div class="col-lg-3 col-md-6 col-6">
                    <h6 class="fw-bold text-dark mb-3">Layanan & Akses</h6>
                    <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                        <li><a href="<?= base_url('pendataan') ?>" class="text-decoration-none text-danger fw-semibold"><i class="bi bi-ui-checks me-1"></i> Form Pendataan Pemuda</a></li>
                        <li><a href="<?= base_url('/#manfaat') ?>" class="text-decoration-none text-muted hover-red"><i class="bi bi-chevron-right me-1 text-danger small"></i> Manfaat Pendataan</a></li>
                        <li><a href="<?= base_url('/#alur') ?>" class="text-decoration-none text-muted hover-red"><i class="bi bi-chevron-right me-1 text-danger small"></i> Alur Pendaftaran</a></li>
                        <li><a href="<?= base_url('admin/login') ?>" class="text-decoration-none text-muted hover-red"><i class="bi bi-shield-lock me-1 text-danger small"></i> Login Pengurus / Admin</a></li>
                    </ul>
                </div>

                <!-- Col 4: Kontak & Sekretariat -->
                <div class="col-lg-3 col-md-6">
                    <h6 class="fw-bold text-dark mb-3">Sekretariat & Kontak</h6>
                    <ul class="list-unstyled small d-flex flex-column gap-2 text-muted mb-0">
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-geo-alt-fill text-danger mt-1"></i>
                            <span>Gedung Perwakilan MTA Sragen, Jl. Raya Sukowati, Sragen, Jawa Tengah</span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-whatsapp text-success"></i>
                            <span>Layanan Informasi: 0812-3456-7890</span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-envelope-fill text-danger"></i>
                            <span>pemudamta.sragen@gmail.com</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Copyright -->
            <div class="row align-items-center gy-2 pt-3">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0 small text-muted">&copy; <?= date('Y') ?> <strong>Pemuda MTA Perwakilan Sragen</strong>. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <span class="badge bg-light text-secondary border px-3 py-2 small">
                        <i class="bi bi-shield-check text-success me-1"></i> Sistem Basis Data Terenkripsi & Terintegrasi
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