<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Pemuda MTA Perwakilan Sragen | Pusat Pendataan Pemuda<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/landing.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- ===================================================
     1. HERO SECTION (SEDERHANA, FOKUS & JELAS)
     =================================================== -->
<section class="hero-simple text-center text-lg-start">
    <div class="container position-relative" style="z-index: 2;">
        <div class="row align-items-center gy-4">
            <div class="col-lg-8">
                <div class="hero-badge-pill mb-3">
                    <i class="bi bi-patch-check-fill text-warning"></i>
                    <span><?= esc($settings['hero_badge'] ?? "Majlis Tafsir Al-Qur'an (MTA) Perwakilan Sragen") ?></span>
                </div>
                <h1 class="hero-title mb-3">
                    <?= esc($settings['hero_title'] ?? 'Sistem Pendataan Pemuda MTA Perwakilan Sragen') ?>
                </h1>
                <p class="hero-subtitle mb-4 pe-lg-4">
                    <?= nl2br(esc($settings['hero_subtitle'] ?? 'Pusat basis data resmi pemuda MTA se-Kabupaten Sragen. Wadah pemetaan potensi, kaderisasi dakwah, dan kesiapsiagaan pengabdian di 4 Wilayah dan 61 Cabang.')) ?>
                </p>
                <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start mb-4">
                    <a href="<?= base_url('pendataan') ?>" class="hero-btn-primary">
                        <i class="bi bi-ui-checks fs-5"></i>
                        <span><?= esc($settings['hero_btn_text'] ?? 'Isi Form Pendataan Pemuda') ?></span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                    <a href="<?= base_url('admin/login') ?>" class="hero-btn-outline">
                        <i class="bi bi-shield-lock fs-5"></i>
                        <span>Portal Admin</span>
                    </a>
                </div>

                <!-- Chips Highlight Ringkas -->
                <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-lg-start">
                    <span class="hero-feature-chip"><i class="bi bi-diagram-3 text-warning"></i> 4 Wilayah &amp; 61 Cabang</span>
                    <span class="hero-feature-chip"><i class="bi bi-shield-check text-warning"></i> Satgas Kesiapsiagaan</span>
                    <span class="hero-feature-chip"><i class="bi bi-broadcast text-warning"></i> Bankom Radio</span>
                    <span class="hero-feature-chip"><i class="bi bi-book-half text-warning"></i> Kajian &amp; Tarbiyah</span>
                </div>
            </div>

            <!-- Kartu Ajakan Sederhana -->
            <div class="col-lg-4 text-center d-none d-lg-block">
                <div class="card border-0 shadow-lg text-start rounded-4 p-4 bg-white text-dark">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <img src="<?= base_url('icons/pemudamta.png') ?>" alt="Logo Pemuda MTA" style="width: 38px; height: 38px; object-fit: contain;">
                        <div>
                            <h6 class="fw-bold text-dark mb-0">Pendataan Pemuda</h6>
                            <small class="text-success"><i class="bi bi-check-circle-fill me-1"></i> Form Online Dibuka</small>
                        </div>
                    </div>
                    <p class="small text-muted mb-3">
                        Khusus pemuda MTA di seluruh cabang se-Kabupaten Sragen. Lengkapi biodata untuk mendapatkan nomor registrasi resmi pemuda.
                    </p>
                    <a href="<?= base_url('pendataan') ?>" class="btn btn-danger w-100 py-2 fw-semibold rounded-3 shadow-sm" style="background-color: #dc2626; border-color: #dc2626;">
                        <i class="bi bi-pencil-square me-1"></i> Mulai Isi Form Pendataan
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================================================
     2. STATISTIK RINGKAS
     =================================================== -->
<section class="stats-strip-simple">
    <div class="container">
        <div class="row g-3 text-center">
            <div class="col-6 col-md-3">
                <div class="stats-card-simple">
                    <div class="stats-num"><?= esc($totalWilayah) ?></div>
                    <div class="stats-label">Wilayah Koordinasi</div>
                    <div class="stats-desc">Wilayah 1 s.d 4 se-Sragen</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stats-card-simple">
                    <div class="stats-num"><?= esc($totalCabang) ?>+</div>
                    <div class="stats-label">Cabang Binaan</div>
                    <div class="stats-desc">Di 20 Kecamatan</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stats-card-simple">
                    <div class="stats-num"><?= number_format($totalPemuda) ?></div>
                    <div class="stats-label">Pemuda Terdata</div>
                    <div class="stats-desc"><?= number_format($totalVerified) ?> Terverifikasi</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stats-card-simple">
                    <div class="stats-num"><?= esc($settings['stats_bidang_num'] ?? '5+') ?></div>
                    <div class="stats-label"><?= esc($settings['stats_bidang_label'] ?? 'Bidang Pengabdian') ?></div>
                    <div class="stats-desc">Satgas, Bankom, Ikhrom, dll</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================================================
     3. ALUR 3 LANGKAH MUDAH PENDATAAN
     =================================================== -->
<section id="alur" class="section-simple" style="background-color: #f8fafc;">
    <div class="container">
        <div class="text-center max-w-700 mx-auto mb-4">
            <span class="section-tag-simple"><i class="bi bi-ui-checks-grid"></i> Alur Pendataan</span>
            <h2 class="section-title-simple mb-2">3 Langkah Mudah Pengisian Data</h2>
            <p class="section-subtitle-simple mb-0">Pengisian dapat dilakukan langsung dari smartphone Anda tanpa perlu registrasi akun.</p>
        </div>

        <div class="row g-3 justify-content-center">
            <div class="col-md-4">
                <div class="step-card-simple">
                    <div class="step-icon-circle">1</div>
                    <h6 class="fw-bold text-dark mb-2">Buka Form Online</h6>
                    <p class="small text-muted mb-0">Klik tombol "Isi Form Pendataan" untuk mengakses formulir pendaftaran resmi.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="step-card-simple">
                    <div class="step-icon-circle">2</div>
                    <h6 class="fw-bold text-dark mb-2">Lengkapi Data Diri &amp; Cabang</h6>
                    <p class="small text-muted mb-0">Isi data pribadi, pilih cabang asal Anda, serta minat dan potensi keahlian.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="step-card-simple">
                    <div class="step-icon-circle">3</div>
                    <h6 class="fw-bold text-dark mb-2">Terima Nomor Registrasi</h6>
                    <p class="small text-muted mb-0">Simpan formulir dan sistem akan otomatis menerbitkan nomor registrasi resmi Anda.</p>
                </div>
            </div>
        </div>

        <div class="text-center mt-4 pt-2">
            <a href="<?= base_url('pendataan') ?>" class="btn btn-danger px-4 py-2 rounded-pill fw-semibold shadow-sm" style="background-color: #dc2626;">
                <i class="bi bi-pencil-square me-1"></i> Buka Formulir Sekarang
            </a>
        </div>
    </div>
</section>

<!-- ===================================================
     4. STRUKTUR 4 WILAYAH KOORDINASI (RINGKAS)
     =================================================== -->
<section id="wilayah" class="section-simple bg-white">
    <div class="container">
        <div class="text-center max-w-700 mx-auto mb-4">
            <span class="section-tag-simple"><i class="bi bi-diagram-3-fill"></i> Wilayah &amp; Cabang</span>
            <h2 class="section-title-simple mb-2">4 Wilayah Koordinasi di Sragen</h2>
            <p class="section-subtitle-simple mb-0">Pembagian wilayah koordinasi pembinaan pemuda MTA se-Kabupaten Sragen.</p>
        </div>

        <div class="row g-3">
            <?php if (!empty($wilayahList)): ?>
                <?php foreach ($wilayahList as $wil): ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="wilayah-card-simple">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-danger bg-opacity-10 text-danger fw-bold px-2 py-1 small"><?= esc($wil['code']) ?></span>
                                <span class="small text-muted"><i class="bi bi-geo-alt text-danger me-1"></i> <?= count($wil['cabang'] ?? []) ?> Cabang</span>
                            </div>
                            <h6 class="fw-bold text-dark mb-2"><?= esc($wil['name']) ?></h6>
                            <p class="small text-muted mb-3" style="min-height: 40px;">
                                <?= esc($wil['description'] ?: 'Koordinasi cabang pemuda MTA di wilayah ini.') ?>
                            </p>
                            <a href="<?= base_url('pendataan') ?>" class="btn btn-sm btn-outline-danger w-100 rounded-pill">
                                Pilih Cabang &amp; Daftar
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-md-3">
                    <div class="wilayah-card-simple">
                        <h6 class="fw-bold text-dark mb-1">Wilayah 1</h6>
                        <p class="small text-muted mb-2">Utara Bengawan Solo</p>
                        <a href="<?= base_url('pendataan') ?>" class="btn btn-sm btn-outline-danger w-100 rounded-pill">Daftar</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ===================================================
     5. PROGRAM & BIDANG KHIDMAH (RINGKAS)
     =================================================== -->
<section id="program" class="section-simple" style="background-color: #f8fafc;">
    <div class="container">
        <div class="text-center max-w-700 mx-auto mb-4">
            <span class="section-tag-simple"><i class="bi bi-grid-fill"></i> Program Kerja</span>
            <h2 class="section-title-simple mb-2">Bidang Pengabdian Pemuda</h2>
            <p class="section-subtitle-simple mb-0">Wadah aktualisasi minat, bakat, dan pengabdian generasi muda.</p>
        </div>

        <div class="row g-3">
            <?php if (!empty($programs)): ?>
                <?php foreach (array_slice($programs, 0, 6) as $prog): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="program-card-simple">
                            <div class="program-icon-simple">
                                <i class="bi <?= esc($prog['icon'] ?? 'bi-grid') ?>"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1"><?= esc($prog['title'] ?? '') ?></h6>
                                <p class="small text-muted mb-0"><?= esc($prog['desc'] ?? '') ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-md-4">
                    <div class="program-card-simple">
                        <div class="program-icon-simple"><i class="bi bi-book-half"></i></div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Kajian &amp; Tarbiyah</h6>
                            <p class="small text-muted mb-0">Pembinaan akidah, tahsin, dan adab Islami bagi pemuda.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ===================================================
     6. VISI SINGKAT & CALL TO ACTION
     =================================================== -->
<section class="section-simple bg-white">
    <div class="container">
        <div class="cta-banner-simple">
            <div class="max-w-700 mx-auto">
                <span class="badge bg-white text-danger fw-bold px-3 py-1 mb-3 rounded-pill shadow-sm" style="font-size: 0.75rem;">VISI PEMUDA MTA</span>
                <p class="fs-6 fst-italic text-white mb-4 lh-lg">
                    <?= nl2br(esc($settings['visi_text'] ?? '"Terwujudnya generasi muda muslim yang kokoh dalam akidah tauhid, istiqomah mengamalkan Al-Qur\'an dan As-Sunnah, cerdas berilmu, mandiri berwirausaha, berakhlak mulia, serta siap berkhidmah untuk dakwah dan kemaslahatan umat."')) ?>
                </p>
                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    <a href="<?= base_url('pendataan') ?>" class="btn btn-light text-danger fw-bold px-4 py-2 rounded-pill shadow-sm" style="color: #991b1b !important;">
                        <i class="bi bi-pencil-square me-1"></i> Isi Formulir Pendataan
                    </a>
                    <?php $waNum = preg_replace('/[^0-9]/', '', (string) ($settings['whatsapp_number'] ?? '6281234567890')); ?>
                    <a href="https://wa.me/<?= esc($waNum) ?>" target="_blank" class="btn btn-outline-light rounded-pill px-4 py-2 fw-semibold">
                        <i class="bi bi-whatsapp me-1 text-success"></i> Hubungi WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================================================
     7. TANYA JAWAB (FAQ RINGKAS)
     =================================================== -->
<section id="faq" class="section-simple" style="background-color: #f8fafc;">
    <div class="container">
        <div class="text-center max-w-700 mx-auto mb-4">
            <span class="section-tag-simple"><i class="bi bi-question-circle-fill"></i> FAQ</span>
            <h2 class="section-title-simple mb-2">Pertanyaan yang Sering Diajukan</h2>
            <p class="section-subtitle-simple mb-0">Informasi ringkas seputar pendataan pemuda.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion accordion-simple" id="accordionFaqSimple">
                    <?php if (!empty($faqs)): ?>
                        <?php foreach (array_slice($faqs, 0, 4) as $fIdx => $faq): ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button <?= $fIdx === 0 ? '' : 'collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#faqSimple<?= $fIdx ?>">
                                        <i class="bi bi-patch-question text-danger me-2"></i> <?= esc($faq['q'] ?? '') ?>
                                    </button>
                                </h2>
                                <div id="faqSimple<?= $fIdx ?>" class="accordion-collapse collapse <?= $fIdx === 0 ? 'show' : '' ?>" data-bs-parent="#accordionFaqSimple">
                                    <div class="accordion-body">
                                        <?= nl2br(esc($faq['a'] ?? '')) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================================================
     8. KONTAK & BANTUAN
     =================================================== -->
<section id="kontak" class="section-simple bg-white">
    <div class="container">
        <div class="row g-3 justify-content-center">
            <div class="col-md-5">
                <div class="contact-card-simple">
                    <div class="rounded-circle bg-danger bg-opacity-10 text-danger p-2 d-inline-flex align-items-center justify-content-center mb-2" style="width: 48px; height: 48px;">
                        <i class="bi bi-geo-alt-fill fs-5"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Sekretariat</h6>
                    <p class="small text-muted mb-0">Gedung Perwakilan MTA Sragen, Jl. Raya Sukowati, Sragen</p>
                </div>
            </div>
            <div class="col-md-5">
                <div class="contact-card-simple">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success p-2 d-inline-flex align-items-center justify-content-center mb-2" style="width: 48px; height: 48px;">
                        <i class="bi bi-whatsapp fs-5"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Helpdesk WhatsApp</h6>
                    <p class="small text-muted mb-2">Butuh bantuan pengisian data?</p>
                    <a href="https://wa.me/<?= esc($waNum) ?>" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3">
                        <i class="bi bi-chat-dots-fill me-1"></i> Chat Bantuan
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
