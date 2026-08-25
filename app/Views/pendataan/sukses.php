<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Pendaftaran Berhasil<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container py-4 py-md-5">
    <!-- Breadcrumb & Back -->
    <div class="row justify-content-center mb-3">
        <div class="col-lg-8 d-flex justify-content-between align-items-center">
            <a href="<?= base_url('/') ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda
            </a>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>" class="text-decoration-none text-muted">Beranda</a></li>
                    <li class="breadcrumb-item active text-success fw-semibold" aria-current="page">Pendaftaran Sukses</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center py-2">
        <div class="col-lg-8 text-center">
            
            <!-- Success Animation & Badge -->
            <div class="card card-custom p-4 p-md-5">
                <div class="mb-4">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-check-circle-fill display-4"></i>
                    </div>
                    <h2 class="fw-bold text-slate-900 mb-2">Pendaftaran Pemuda Berhasil!</h2>
                    <p class="text-muted mx-auto" style="max-width: 540px;">
                        Data profil dan potensi diri Anda telah tersimpan dengan aman dalam sistem basis data <strong>Pemuda MTA Perwakilan Sragen</strong>.
                    </p>
                </div>

                <!-- Registration Number Box -->
                <div class="p-4 bg-light rounded-3 border mb-4 text-center">
                    <div class="text-muted small fw-semibold text-uppercase mb-1">Nomor Registrasi Pemuda Anda:</div>
                    <div class="display-6 fw-bold text-danger text-break" id="regNumberDisplay">
                        <?= esc($registration_number ?? (session()->getFlashdata('registration_number') ?? 'PMD-' . date('Ymd') . '-0001')) ?>
                    </div>
                    <div class="small text-muted mt-2">
                        <i class="bi bi-info-circle me-1"></i> Simpan nomor registrasi ini sebagai bukti pendaftaran resmi Anda.
                    </div>
                </div>

                <!-- Summary of Submitted Data (If Available) -->
                <?php if (isset($pemuda)): ?>
                    <div class="text-start bg-white p-3 rounded-3 border mb-4">
                        <h6 class="fw-bold text-danger border-bottom pb-2 mb-3"><i class="bi bi-person-check me-1"></i> Ringkasan Registrasi:</h6>
                        <div class="row g-2 small">
                            <div class="col-sm-4 text-muted">Nama Lengkap:</div>
                            <div class="col-sm-8 fw-semibold text-slate-900"><?= esc($pemuda['name'] ?? '-') ?></div>

                            <div class="col-sm-4 text-muted">Nomor WhatsApp:</div>
                            <div class="col-sm-8 fw-semibold text-slate-900"><?= esc($pemuda['phone'] ?? '-') ?></div>

                            <div class="col-sm-4 text-muted">Status Verifikasi:</div>
                            <div class="col-sm-8">
                                <span class="badge bg-warning bg-opacity-25 text-warning-emphasis fw-bold">Menunggu Verifikasi Admin</span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Action Buttons -->
                <div class="d-flex flex-column flex-sm-row justify-content-center gap-2 mt-2">
                    <button type="button" class="btn btn-outline-secondary px-4 py-2" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i> Cetak Bukti
                    </button>
                    <a href="<?= base_url('pendataan') ?>" class="btn btn-secondary-pmd px-4 py-2">
                        <i class="bi bi-plus-circle me-1"></i> Input Data Lain
                    </a>
                    <a href="<?= base_url('/') ?>" class="btn btn-primary-pmd px-4 py-2">
                        <i class="bi bi-house-door me-1"></i> Kembali ke Beranda
                    </a>
                </div>

            </div>

        </div>
    </div>
</div>
<?= $this->endSection() ?>

