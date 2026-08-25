<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Pemuda MTA Perwakilan Sragen | Pusat Informasi & Pendataan Pemuda<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/landing.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- ===================================================
     1. HERO SECTION
     =================================================== -->
<section class="hero-landing">
    <div class="container position-relative" style="z-index: 2;">
        <div class="row align-items-center gy-4">
            <div class="col-lg-8 text-center text-lg-start">
                <div class="hero-badge-pill mb-3">
                    <i class="bi bi-patch-check-fill text-warning"></i>
                    <span>Majelis Tafsir Al-Qur'an (MTA) Perwakilan Sragen</span>
                </div>
                <h1 class="hero-title mb-3">
                    Generasi Muda Berilmu, Berakhlak Mulia &amp; Berjiwa Pengabdian
                </h1>
                <p class="hero-subtitle mb-4 pe-lg-4">
                    Pusat Informasi &amp; Basis Data Resmi <strong>Pemuda MTA Perwakilan Sragen</strong>. Wadah pembinaan akidah, pengembangan kompetensi, kesiapsiagaan pengabdian, dan pemetaan potensi pemuda di 4 Wilayah dan 61 Cabang se-Kabupaten Sragen.
                </p>
                <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start mb-4">
                    <a href="<?= base_url('pendataan') ?>" class="hero-btn-primary">
                        <i class="bi bi-ui-checks fs-5"></i>
                        <span>Isi Form Pendataan Pemuda</span>
                    </a>
                    <a href="#wilayah" class="hero-btn-outline">
                        <i class="bi bi-diagram-3 fs-5"></i>
                        <span>Struktur 4 Wilayah</span>
                    </a>
                    <a href="#program" class="hero-btn-outline">
                        <i class="bi bi-grid fs-5"></i>
                        <span>Program Kerja</span>
                    </a>
                </div>

                <!-- Chips Highlight -->
                <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-lg-start pt-2">
                    <span class="hero-feature-chip"><i class="bi bi-shield-check text-warning"></i> Satgas &amp; Kesiapsiagaan</span>
                    <span class="hero-feature-chip"><i class="bi bi-broadcast text-warning"></i> Bankom Radio</span>
                    <span class="hero-feature-chip"><i class="bi bi-heart-pulse-fill text-warning"></i> Tim Ikhrom &amp; Parkir</span>
                    <span class="hero-feature-chip"><i class="bi bi-laptop text-warning"></i> Skill &amp; Wirausaha</span>
                    <span class="hero-feature-chip"><i class="bi bi-book-half text-warning"></i> Kajian &amp; Tarbiyah</span>
                </div>
            </div>

            <div class="col-lg-4 text-center d-none d-lg-block">
                <div class="position-relative p-4">
                    <div class="card border-0 shadow-lg text-start rounded-4 overflow-hidden" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);">
                        <div class="p-3 bg-pmd-red text-white d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #991b1b 0%, #dc2626 100%);">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-people-fill fs-5"></i>
                                <span class="fw-bold small">Portal Pendataan Resmi</span>
                            </div>
                            <span class="badge bg-white text-danger fw-semibold px-2 py-1" style="font-size: 0.7rem;">Online</span>
                        </div>
                        <div class="p-4 text-dark">
                            <h6 class="fw-bold text-slate-900 mb-2">Mengapa Harus Mengisi Form?</h6>
                            <p class="small text-muted mb-3">
                                Pendataan bertujuan memetakan potensi keilmuan, profesi, keterampilan, dan kesiapan pengabdian pemuda MTA di seluruh cabang.
                            </p>
                            <ul class="list-unstyled small mb-4 d-flex flex-column gap-2 text-secondary">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> Nomor registrasi resmi pemuda</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> Penyaluran bidang pengabdian</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> Rekomendasi pelatihan skill</li>
                            </ul>
                            <a href="<?= base_url('pendataan') ?>" class="btn btn-danger w-100 py-2 fw-semibold rounded-3 shadow-sm" style="background-color: #dc2626;">
                                <i class="bi bi-pencil-square me-1"></i> Mulai Pengisian Data
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================================================
     2. STATS COUNTER STRIP
     =================================================== -->
<section class="stats-strip">
    <div class="container">
        <div class="row g-3">
            <div class="col-6 col-lg-3">
                <div class="stats-card">
                    <div class="stats-icon-wrapper bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-diagram-3-fill"></i>
                    </div>
                    <div class="stats-num"><?= esc($totalWilayah) ?></div>
                    <div class="stats-label">Wilayah Koordinasi</div>
                    <small class="text-muted d-block mt-1">Wilayah 1 s.d 4 se-Sragen</small>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="stats-card">
                    <div class="stats-icon-wrapper bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <div class="stats-num"><?= esc($totalCabang) ?>+</div>
                    <div class="stats-label">Cabang Binaan</div>
                    <small class="text-muted d-block mt-1">Tersebar di 20 Kecamatan</small>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="stats-card">
                    <div class="stats-icon-wrapper bg-success bg-opacity-10 text-success">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="stats-num"><?= number_format($totalPemuda) ?></div>
                    <div class="stats-label">Pemuda Terdata</div>
                    <small class="text-muted d-block mt-1"><?= number_format($totalVerified) ?> Terverifikasi Admin</small>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="stats-card">
                    <div class="stats-icon-wrapper bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-shield-shaded"></i>
                    </div>
                    <div class="stats-num">5+</div>
                    <div class="stats-label">Bidang Pengabdian</div>
                    <small class="text-muted d-block mt-1">Satgas, Bankom, Ikhrom, dll</small>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================================================
     3. PROFIL & VISI MISI SECTION
     =================================================== -->
<section id="tentang" class="landing-section bg-white">
    <div class="container">
        <div class="row align-items-center gy-4 mb-5">
            <div class="col-lg-6">
                <span class="section-tag"><i class="bi bi-info-circle-fill"></i> Tentang Organisasi</span>
                <h2 class="section-heading mb-3">Mengenal Pemuda MTA Perwakilan Sragen</h2>
                <p class="section-desc mb-3">
                    <strong>Pemuda MTA Perwakilan Sragen</strong> adalah wadah pembinaan, pengkaderan, dan penggerak kegiatan generasi muda Majelis Tafsir Al-Qur'an di tingkat Kabupaten Sragen.
                </p>
                <p class="section-desc mb-4">
                    Dengan berlandaskan Al-Qur'an dan As-Sunnah, pemuda MTA berperan aktif dalam dakwah Islam, kegiatan sosial kemanusiaan, kesiapsiagaan kebencanaan melalui Satgas, pelayanan pengajian melalui Tim Ikhrom &amp; Parkir, serta pengembangan ekonomi dan wirausaha generasi muda.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-light text-dark border px-3 py-2 fw-semibold"><i class="bi bi-check2 text-danger me-1"></i> Ikhlas Beramal</span>
                    <span class="badge bg-light text-dark border px-3 py-2 fw-semibold"><i class="bi bi-check2 text-danger me-1"></i> Disiplin &amp; Tangguh</span>
                    <span class="badge bg-light text-dark border px-3 py-2 fw-semibold"><i class="bi bi-check2 text-danger me-1"></i> Ukhuwah Islamiyah</span>
                    <span class="badge bg-light text-dark border px-3 py-2 fw-semibold"><i class="bi bi-check2 text-danger me-1"></i> Mandiri &amp; Berdaya</span>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="visi-card shadow-sm">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-compass-fill fs-4 text-warning"></i>
                        <h4 class="fw-bold mb-0 text-white">Visi Organisasi</h4>
                    </div>
                    <p class="fs-6 mb-0 text-white-50" style="line-height: 1.7; color: rgba(255, 255, 255, 0.95) !important;">
                        "Terwujudnya generasi muda muslim yang kokoh dalam akidah tauhid, istiqomah mengamalkan Al-Qur'an dan As-Sunnah, cerdas berilmu, mandiri berwirausaha, berakhlak mulia, serta siap berkhidmah untuk dakwah dan kemaslahatan umat."
                    </p>
                </div>
            </div>
        </div>

        <!-- 4 Misi Strategis -->
        <div class="row g-3">
            <div class="col-md-6 col-lg-3">
                <div class="misi-item h-100">
                    <div class="misi-number">1</div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Dakwah &amp; Tarbiyah</h6>
                        <p class="small text-muted mb-0">Menanamkan pemahaman Al-Qur'an dan As-Sunnah serta adab Islami pada generasi muda.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="misi-item h-100">
                    <div class="misi-number">2</div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Pengabdian &amp; Khidmah</h6>
                        <p class="small text-muted mb-0">Membangun kesiapsiagaan sosial, Satgas pengamanan dakwah, Bankom, dan Tim Ikhrom.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="misi-item h-100">
                    <div class="misi-number">3</div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Skill &amp; Kemandirian</h6>
                        <p class="small text-muted mb-0">Mengembangkan keterampilan vokasi, wirausaha muda, IPTEK digital, dan potensi profesi.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="misi-item h-100">
                    <div class="misi-number">4</div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Tata Kelola Modern</h6>
                        <p class="small text-muted mb-0">Mewujudkan pendataan pemuda berbasis digital yang terstruktur, akurat, dan terintegrasi.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================================================
     4. STRUKTUR 4 WILAYAH & 61 CABANG SECTION
     =================================================== -->
<section id="wilayah" class="landing-section bg-light">
    <div class="container">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="section-tag"><i class="bi bi-diagram-3-fill"></i> Struktur Wilayah Koordinasi</span>
            <h2 class="section-heading mb-2">4 Wilayah &amp; 61 Cabang Binaan</h2>
            <p class="section-desc">
                Struktur organisasi Pemuda MTA Perwakilan Sragen dibagi ke dalam 4 Wilayah koordinasi untuk memastikan pembinaan dan komunikasi berjalan efektif di seluruh cabang.
            </p>
        </div>

        <div class="row g-4">
            <!-- Wilayah Nav Tabs -->
            <div class="col-lg-4">
                <div class="nav flex-column wilayah-nav-pills gap-2" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <?php if (!empty($wilayahList)): ?>
                        <?php foreach ($wilayahList as $idx => $wil): ?>
                            <button class="nav-link <?= $idx === 0 ? 'active' : '' ?>" 
                                    id="v-pills-wil-<?= $wil['id'] ?>-tab" 
                                    data-bs-toggle="pill" 
                                    data-bs-target="#v-pills-wil-<?= $wil['id'] ?>" 
                                    type="button" 
                                    role="tab" 
                                    aria-selected="<?= $idx === 0 ? 'true' : 'false' ?>">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-geo-alt-fill text-danger"></i>
                                    <div class="text-start">
                                        <div class="fw-bold"><?= esc($wil['name']) ?></div>
                                        <small class="opacity-75"><?= esc($wil['code']) ?></small>
                                    </div>
                                </div>
                                <span class="badge bg-danger rounded-pill"><?= count($wil['cabang'] ?? []) ?> Cabang</span>
                            </button>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Fallback 4 Wilayah Standard -->
                        <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-w1" type="button">
                            <span><i class="bi bi-geo-alt-fill text-danger me-2"></i> Wilayah 1 (Utara Bengawan)</span>
                            <span class="badge bg-danger rounded-pill">12 Cabang</span>
                        </button>
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-w2" type="button">
                            <span><i class="bi bi-geo-alt-fill text-danger me-2"></i> Wilayah 2 (Barat)</span>
                            <span class="badge bg-danger rounded-pill">16 Cabang</span>
                        </button>
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-w3" type="button">
                            <span><i class="bi bi-geo-alt-fill text-danger me-2"></i> Wilayah 3 (Pusat)</span>
                            <span class="badge bg-danger rounded-pill">21 Cabang</span>
                        </button>
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-w4" type="button">
                            <span><i class="bi bi-geo-alt-fill text-danger me-2"></i> Wilayah 4 (Timur)</span>
                            <span class="badge bg-danger rounded-pill">12 Cabang</span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Wilayah Tab Content -->
            <div class="col-lg-8">
                <div class="tab-content" id="v-pills-tabContent">
                    <?php if (!empty($wilayahList)): ?>
                        <?php foreach ($wilayahList as $idx => $wil): ?>
                            <div class="tab-pane fade <?= $idx === 0 ? 'show active' : '' ?>" 
                                 id="v-pills-wil-<?= $wil['id'] ?>" 
                                 role="tabpanel" 
                                 tabindex="0">
                                <div class="card card-custom p-4">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between pb-3 mb-3 border-bottom gap-2">
                                        <div>
                                            <h5 class="fw-bold text-slate-900 mb-1"><?= esc($wil['name']) ?> (<?= esc($wil['code']) ?>)</h5>
                                            <p class="text-muted small mb-0"><?= esc($wil['description'] ?: 'Koordinasi Cabang Pemuda MTA') ?></p>
                                        </div>
                                        <span class="badge bg-pmd-badge px-3 py-2 fw-semibold">
                                            <i class="bi bi-diagram-2 me-1"></i> Total <?= count($wil['cabang'] ?? []) ?> Cabang Terdaftar
                                        </span>
                                    </div>

                                    <div class="small fw-semibold text-muted mb-2">Daftar Cabang Binaan:</div>
                                    <div class="d-flex flex-wrap gap-2">
                                        <?php if (!empty($wil['cabang'])): ?>
                                            <?php foreach ($wil['cabang'] as $cab): ?>
                                                <div class="cabang-chip">
                                                    <i class="bi bi-check-circle-fill text-success small"></i>
                                                    <span><?= esc($cab['name']) ?></span>
                                                    <small class="text-muted">(<?= esc($cab['code']) ?>)</small>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p class="text-muted small mb-0">Belum ada data cabang pada wilayah ini.</p>
                                        <?php endif; ?>
                                    </div>

                                    <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                                        <small class="text-muted">Apakah Anda pemuda di wilayah ini?</small>
                                        <a href="<?= base_url('pendataan') ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                            <i class="bi bi-pencil-fill me-1"></i> Isi Data Cabang Anda
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Fallback static view if list is empty -->
                        <div class="tab-pane fade show active" id="tab-w1">
                            <div class="card card-custom p-4">
                                <h5 class="fw-bold text-dark mb-2">Wilayah 1 (Utara Bengawan Solo)</h5>
                                <p class="text-muted small mb-3">Mencakup wilayah Gesi, Jenar, Mondokan, Sukodono, Tangen dan sekitarnya.</p>
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="cabang-chip">Gesi 1</span>
                                    <span class="cabang-chip">Gesi 2</span>
                                    <span class="cabang-chip">Jenar 1</span>
                                    <span class="cabang-chip">Mondokan 1</span>
                                    <span class="cabang-chip">Sukodono 1</span>
                                    <span class="cabang-chip">Tangen 1</span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================================================
     5. BIDANG & PROGRAM KERJA KEPEMUDAAN
     =================================================== -->
<section id="program" class="landing-section bg-white">
    <div class="container">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="section-tag"><i class="bi bi-grid-fill"></i> Bidang &amp; Program Kerja</span>
            <h2 class="section-heading mb-2">Ruang Aktualisasi &amp; Pengabdian Pemuda</h2>
            <p class="section-desc">
                Berbagai divisi dan program dirancang untuk mewadahi minat, bakat, serta semangat juang pemuda dalam mengabdi kepada agama, bangsa, dan masyarakat.
            </p>
        </div>

        <div class="row g-4">
            <!-- Program 1: Dakwah & Tarbiyah -->
            <div class="col-md-6 col-lg-4">
                <div class="program-card">
                    <div class="program-icon-box bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-book-half"></i>
                    </div>
                    <h5 class="fw-bold text-slate-900 mb-2">Kajian &amp; Tarbiyah Pemuda</h5>
                    <p class="text-muted small mb-3 flex-grow-1">
                        Kajian rutin pemuda tematik, pendalaman tafsir Al-Qur'an, tahsin tilawah, pembahasan hadits shahih, dan pembentukan karakter akhlaqul karimah.
                    </p>
                    <div class="pt-2 border-top">
                        <span class="badge bg-light text-secondary border small"><i class="bi bi-calendar-event me-1"></i> Rutin Bulanan &amp; Wilayah</span>
                    </div>
                </div>
            </div>

            <!-- Program 2: Satgas & Kesiapsiagaan -->
            <div class="col-md-6 col-lg-4">
                <div class="program-card">
                    <div class="program-icon-box bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h5 class="fw-bold text-slate-900 mb-2">Satgas Kesiapsiagaan</h5>
                    <p class="text-muted small mb-3 flex-grow-1">
                        Pasukan pengamanan kegiatan dakwah dan pengajian akbar, tanggap darurat bencana (SAR), bakti sosial kemanusiaan, dan ketertiban acara perwakilan.
                    </p>
                    <div class="pt-2 border-top">
                        <span class="badge bg-light text-secondary border small"><i class="bi bi-shield-shaded me-1"></i> Disiplin &amp; Siaga 24/7</span>
                    </div>
                </div>
            </div>

            <!-- Program 3: Bankom Radio -->
            <div class="col-md-6 col-lg-4">
                <div class="program-card">
                    <div class="program-icon-box bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-broadcast-pin"></i>
                    </div>
                    <h5 class="fw-bold text-slate-900 mb-2">Bankom (Bantuan Komunikasi)</h5>
                    <p class="text-muted small mb-3 flex-grow-1">
                        Jaringan komunikasi radio terpadu untuk koordinasi acara besar, pemantauan arus lalu lintas jamaah, serta koordinasi cepat tanggap situasi darurat.
                    </p>
                    <div class="pt-2 border-top">
                        <span class="badge bg-light text-secondary border small"><i class="bi bi-reception-4 me-1"></i> Frekuensi Radio Resmi</span>
                    </div>
                </div>
            </div>

            <!-- Program 4: Tim Ikhrom & Parkir -->
            <div class="col-md-6 col-lg-4">
                <div class="program-card">
                    <div class="program-icon-box bg-success bg-opacity-10 text-success">
                        <i class="bi bi-heart-pulse-fill"></i>
                    </div>
                    <h5 class="fw-bold text-slate-900 mb-2">Tim Ikhrom &amp; Parkir</h5>
                    <p class="text-muted small mb-3 flex-grow-1">
                        Khidmah melayani jamaah pengajian Ahad pagi, pengaturan saf dan fasilitas jamaah, penataan kantong parkir tertib, dan kelancaran sirkulasi kendaraan.
                    </p>
                    <div class="pt-2 border-top">
                        <span class="badge bg-light text-secondary border small"><i class="bi bi-clock-history me-1"></i> Pengajian Rutin Ahad</span>
                    </div>
                </div>
            </div>

            <!-- Program 5: Skill & Wirausaha -->
            <div class="col-md-6 col-lg-4">
                <div class="program-card">
                    <div class="program-icon-box bg-info bg-opacity-10 text-info">
                        <i class="bi bi-laptop"></i>
                    </div>
                    <h5 class="fw-bold text-slate-900 mb-2">Pelatihan Skill &amp; Wirausaha</h5>
                    <p class="text-muted small mb-3 flex-grow-1">
                        Workshop keahlian digital, desain grafis, coding, pelatihan mekanik/teknik, bimbingan wirausaha muda mandiri, dan jejaring ekonomi pemuda.
                    </p>
                    <div class="pt-2 border-top">
                        <span class="badge bg-light text-secondary border small"><i class="bi bi-graph-up-arrow me-1"></i> Pemberdayaan Ekonomi</span>
                    </div>
                </div>
            </div>

            <!-- Program 6: Olahraga & Rihlah -->
            <div class="col-md-6 col-lg-4">
                <div class="program-card">
                    <div class="program-icon-box bg-secondary bg-opacity-10 text-secondary">
                        <i class="bi bi-trophy-fill"></i>
                    </div>
                    <h5 class="fw-bold text-slate-900 mb-2">Olahraga, Seni &amp; Rihlah</h5>
                    <p class="text-muted small mb-3 flex-grow-1">
                        Menjalin ukhuwah dan kebugaran jasmani melalui turnamen futsal, bulutangkis, panahan, outbound / tadabbur alam, dan donor darah sukarela.
                    </p>
                    <div class="pt-2 border-top">
                        <span class="badge bg-light text-secondary border small"><i class="bi bi-people-fill me-1"></i> Solidaritas Ukhuwah</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================================================
     6. ALUR PENDATAAN & MANFAAT SECTION
     =================================================== -->
<section id="manfaat" class="landing-section bg-light">
    <div class="container">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="section-tag"><i class="bi bi-ui-checks-grid"></i> Alur Pendataan Pemuda</span>
            <h2 class="section-heading mb-2">4 Langkah Mudah Pengisian Data</h2>
            <p class="section-desc">
                Proses pendataan dilakukan secara online, cepat, dan transparan. Ikuti 4 tahapan berikut untuk melengkapi profil Anda.
            </p>
        </div>

        <div class="row g-4 mb-5">
            <!-- Step 1 -->
            <div class="col-md-6 col-lg-3">
                <div class="step-card">
                    <div class="step-badge">1</div>
                    <h6 class="fw-bold text-slate-900 mb-2">Akses Formulir</h6>
                    <p class="small text-muted mb-0">
                        Klik tombol "Form Pendataan" pada menu navigasi atau halaman ini untuk membuka form registrasi.
                    </p>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="col-md-6 col-lg-3">
                <div class="step-card">
                    <div class="step-badge">2</div>
                    <h6 class="fw-bold text-slate-900 mb-2">Pilih Wilayah &amp; Cabang</h6>
                    <p class="small text-muted mb-0">
                        Tentukan cabang asal Anda (terintegrasi otomatis dengan 4 Wilayah di Sragen) dan isi identitas pribadi.
                    </p>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="col-md-6 col-lg-3">
                <div class="step-card">
                    <div class="step-badge">3</div>
                    <h6 class="fw-bold text-slate-900 mb-2">Lengkapi Profil &amp; Minat</h6>
                    <p class="small text-muted mb-0">
                        Isi riwayat pendidikan, status pekerjaan, keahlian yang dikuasai, serta pilihan organisasi (Satgas, Bankom, dll).
                    </p>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="col-md-6 col-lg-3">
                <div class="step-card">
                    <div class="step-badge">4</div>
                    <h6 class="fw-bold text-slate-900 mb-2">Terima No. Registrasi</h6>
                    <p class="small text-muted mb-0">
                        Dapatkan Nomor Registrasi resmi pemuda yang dapat dicetak sebagai bukti telah terdaftar di database perwakilan.
                    </p>
                </div>
            </div>
        </div>

        <!-- Big CTA Banner Strip -->
        <div class="cta-banner">
            <div class="row align-items-center gy-4 position-relative" style="z-index: 2;">
                <div class="col-lg-8 text-center text-lg-start">
                    <h3 class="fw-bold text-white mb-2">Sudahkah Anda Terdata di Sistem Pemuda MTA Sragen?</h3>
                    <p class="text-white-50 mb-0 fs-6">
                        Mari berkontribusi aktif dalam barisan dakwah dan kemaslahatan umat. Satu data pemuda untuk kemajuan bersama se-Kabupaten Sragen.
                    </p>
                </div>
                <div class="col-lg-4 text-center text-lg-end">
                    <a href="<?= base_url('pendataan') ?>" class="btn btn-light text-danger fw-bold px-4 py-3 rounded-pill shadow-lg" style="color: #991b1b !important;">
                        <i class="bi bi-pencil-square me-2"></i> Isi Formulir Sekarang
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================================================
     7. FAQ SECTION
     =================================================== -->
<section id="faq" class="landing-section bg-white">
    <div class="container">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="section-tag"><i class="bi bi-question-circle-fill"></i> Tanya Jawab</span>
            <h2 class="section-heading mb-2">Pertanyaan yang Sering Diajukan (FAQ)</h2>
            <p class="section-desc">
                Informasi seputar sistem pendataan, validitas data, dan keanggotaan Pemuda MTA Perwakilan Sragen.
            </p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="accordion accordion-custom" id="accordionFaq">
                    <!-- FAQ 1 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                <i class="bi bi-patch-question text-danger me-2"></i> Siapa saja yang wajib mengisi formulir pendataan ini?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#accordionFaq">
                            <div class="accordion-body">
                                Seluruh pemuda dan pemudi warga binaan Majelis Tafsir Al-Qur'an (MTA) yang berdomisili atau beraktivitas di seluruh cabang se-Kabupaten Sragen diharapkan mengisi form pendataan ini.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                <i class="bi bi-patch-question text-danger me-2"></i> Bagaimana jika saya belum mengetahui cabang MTA saya?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#accordionFaq">
                            <div class="accordion-body">
                                Anda dapat memilih cabang terdekat dengan domisili tempat tinggal atau pengajian yang biasa Anda hadiri. Pada bagian Wilayah, sistem akan otomatis mengarahkan daftar cabang yang tersedia di kecamatan Anda.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                <i class="bi bi-patch-question text-danger me-2"></i> Apakah data pribadi yang saya masukkan aman?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#accordionFaq">
                            <div class="accordion-body">
                                Ya, sistem kami dirancang dengan standar keamanan tinggi dan akses bertingkat (Role Based Access Control). Data Anda hanya dapat diakses oleh pengurus cabang, admin wilayah, dan pengurus perwakilan yang berwenang untuk kepentingan pembinaan organisasi.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 4 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                <i class="bi bi-patch-question text-danger me-2"></i> Apa fungsi Nomor Registrasi Pemuda?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#accordionFaq">
                            <div class="accordion-body">
                                Nomor registrasi (contoh: <code>PMD-20260825-0001</code>) adalah tanda bukti resmi bahwa profil Anda telah terdaftar dalam sistem induk Pemuda MTA Perwakilan Sragen dan dapat digunakan untuk verifikasi keikutsertaan kegiatan atau pelatihan.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 5 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                <i class="bi bi-patch-question text-danger me-2"></i> Bisakah saya memperbarui data setelah mengirimkan formulir?
                            </button>
                        </h2>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#accordionFaq">
                            <div class="accordion-body">
                                Jika ada perubahan data penting (alamat, nomor HP, pekerjaan), Anda dapat menghubungi admin cabang atau admin wilayah setempat untuk dilakukan pembaruan data pada sistem dashboard.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================================================
     8. KONTAK & SEKRETARIAT SECTION
     =================================================== -->
<section id="kontak" class="landing-section bg-light">
    <div class="container">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="section-tag"><i class="bi bi-telephone-fill"></i> Layanan &amp; Kontak</span>
            <h2 class="section-heading mb-2">Sekretariat Pemuda MTA Sragen</h2>
            <p class="section-desc">
                Butuh bantuan terkait pengisian data atau informasi kegiatan kepemudaan? Silakan hubungi kami.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="contact-card text-center">
                    <div class="rounded-circle bg-danger bg-opacity-10 text-danger p-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-geo-alt-fill fs-4"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Kantor Sekretariat</h6>
                    <p class="small text-muted mb-0">
                        Gedung Perwakilan MTA Sragen<br>
                        Jl. Raya Sukowati, Kabupaten Sragen, Jawa Tengah
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="contact-card text-center">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-whatsapp fs-4"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">WhatsApp Center</h6>
                    <p class="small text-muted mb-2">Layanan Informasi &amp; Helpdesk</p>
                    <a href="https://wa.me/6281234567890" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3">
                        <i class="bi bi-chat-dots-fill me-1"></i> Chat WhatsApp
                    </a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="contact-card text-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-shield-lock-fill fs-4"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Portal Pengurus &amp; Admin</h6>
                    <p class="small text-muted mb-2">Akses Verifikasi &amp; Manajemen Data</p>
                    <a href="<?= base_url('admin/login') ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Login Dashboard Admin
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
