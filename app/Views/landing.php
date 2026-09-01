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
                    <span><?= esc($settings['hero_badge'] ?? "Majelis Tafsir Al-Qur'an (MTA) Perwakilan Sragen") ?></span>
                </div>
                <h1 class="hero-title mb-3">
                    <?= esc($settings['hero_title'] ?? 'Generasi Muda Berilmu, Berakhlak Mulia & Berjiwa Pengabdian') ?>
                </h1>
                <p class="hero-subtitle mb-4 pe-lg-4">
                    <?= nl2br(esc($settings['hero_subtitle'] ?? 'Pusat Informasi & Basis Data Resmi Pemuda MTA Perwakilan Sragen. Wadah pembinaan akidah, pengembangan kompetensi, kesiapsiagaan pengabdian, dan pemetaan potensi pemuda di 4 Wilayah dan 61 Cabang se-Kabupaten Sragen.')) ?>
                </p>
                <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start mb-4">
                    <a href="<?= base_url('pendataan') ?>" class="hero-btn-primary">
                        <i class="bi bi-ui-checks fs-5"></i>
                        <span><?= esc($settings['hero_btn_text'] ?? 'Isi Form Pendataan Pemuda') ?></span>
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
                    <?php if (!empty($heroChips)): ?>
                        <?php foreach ($heroChips as $chip): ?>
                            <span class="hero-feature-chip">
                                <i class="bi <?= esc($chip['icon'] ?? 'bi-check-circle') ?> text-warning"></i> <?= esc($chip['text'] ?? '') ?>
                            </span>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span class="hero-feature-chip"><i class="bi bi-shield-check text-warning"></i> Satgas &amp; Kesiapsiagaan</span>
                        <span class="hero-feature-chip"><i class="bi bi-broadcast text-warning"></i> Bankom Radio</span>
                        <span class="hero-feature-chip"><i class="bi bi-heart-pulse-fill text-warning"></i> Tim Ikhrom &amp; Parkir</span>
                        <span class="hero-feature-chip"><i class="bi bi-laptop text-warning"></i> Skill &amp; Wirausaha</span>
                        <span class="hero-feature-chip"><i class="bi bi-book-half text-warning"></i> Kajian &amp; Tarbiyah</span>
                    <?php endif; ?>
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
                            <h6 class="fw-bold text-slate-900 mb-2"><?= esc($settings['hero_card_title'] ?? 'Mengapa Harus Mengisi Form?') ?></h6>
                            <p class="small text-muted mb-3">
                                <?= nl2br(esc($settings['hero_card_desc'] ?? 'Pendataan bertujuan memetakan potensi keilmuan, profesi, keterampilan, dan kesiapan pengabdian pemuda MTA di seluruh cabang.')) ?>
                            </p>
                            <ul class="list-unstyled small mb-4 d-flex flex-column gap-2 text-secondary">
                                <?php if (!empty($heroFeatures)): ?>
                                    <?php foreach ($heroFeatures as $feat): ?>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> <?= esc($feat) ?></li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li><i class="bi bi-check-circle-fill text-success me-2"></i> Nomor registrasi resmi pemuda</li>
                                    <li><i class="bi bi-check-circle-fill text-success me-2"></i> Penyaluran bidang pengabdian</li>
                                    <li><i class="bi bi-check-circle-fill text-success me-2"></i> Rekomendasi pelatihan skill</li>
                                <?php endif; ?>
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
                    <div class="stats-num"><?= esc($settings['stats_bidang_num'] ?? '5+') ?></div>
                    <div class="stats-label"><?= esc($settings['stats_bidang_label'] ?? 'Bidang Pengabdian') ?></div>
                    <small class="text-muted d-block mt-1"><?= esc($settings['stats_bidang_desc'] ?? 'Satgas, Bankom, Ikhrom, dll') ?></small>
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
                <span class="section-tag"><i class="bi bi-info-circle-fill"></i> <?= esc($settings['tentang_tag'] ?? 'Tentang Organisasi') ?></span>
                <h2 class="section-heading mb-3"><?= esc($settings['tentang_title'] ?? 'Mengenal Pemuda MTA Perwakilan Sragen') ?></h2>
                <p class="section-desc mb-3">
                    <?= nl2br(esc($settings['tentang_desc_1'] ?? "Pemuda MTA Perwakilan Sragen adalah wadah pembinaan, pengkaderan, dan penggerak kegiatan generasi muda Majelis Tafsir Al-Qur'an di tingkat Kabupaten Sragen.")) ?>
                </p>
                <p class="section-desc mb-4">
                    <?= nl2br(esc($settings['tentang_desc_2'] ?? "Dengan berlandaskan Al-Qur'an dan As-Sunnah, pemuda MTA berperan aktif dalam dakwah Islam, kegiatan sosial kemanusiaan, kesiapsiagaan kebencanaan melalui Satgas, pelayanan pengajian melalui Tim Ikhrom & Parkir, serta pengembangan ekonomi dan wirausaha generasi muda.")) ?>
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
                        <?= nl2br(esc($settings['visi_text'] ?? '"Terwujudnya generasi muda muslim yang kokoh dalam akidah tauhid, istiqomah mengamalkan Al-Qur\'an dan As-Sunnah, cerdas berilmu, mandiri berwirausaha, berakhlak mulia, serta siap berkhidmah untuk dakwah dan kemaslahatan umat."')) ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Misi Strategis -->
        <div class="row g-3">
            <?php if (!empty($misiList)): ?>
                <?php foreach ($misiList as $m): ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="misi-item h-100">
                            <div class="misi-number"><?= esc($m['number'] ?? '') ?></div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1"><?= esc($m['title'] ?? '') ?></h6>
                                <p class="small text-muted mb-0"><?= esc($m['desc'] ?? '') ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
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
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ===================================================
     4. STRUKTUR 4 WILAYAH & CABANG SECTION
     =================================================== -->
<section id="wilayah" class="landing-section bg-light">
    <div class="container">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="section-tag"><i class="bi bi-diagram-3-fill"></i> <?= esc($settings['wilayah_tag'] ?? 'Struktur Wilayah Koordinasi') ?></span>
            <h2 class="section-heading mb-2"><?= esc($settings['wilayah_title'] ?? '4 Wilayah & 61 Cabang Binaan') ?></h2>
            <p class="section-desc">
                <?= nl2br(esc($settings['wilayah_desc'] ?? 'Struktur organisasi Pemuda MTA Perwakilan Sragen dibagi ke dalam 4 Wilayah koordinasi untuk memastikan pembinaan dan komunikasi berjalan efektif di seluruh cabang.')) ?>
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
            <span class="section-tag"><i class="bi bi-grid-fill"></i> <?= esc($settings['program_tag'] ?? 'Bidang & Program Kerja') ?></span>
            <h2 class="section-heading mb-2"><?= esc($settings['program_title'] ?? 'Ruang Aktualisasi & Pengabdian Pemuda') ?></h2>
            <p class="section-desc">
                <?= nl2br(esc($settings['program_desc'] ?? 'Berbagai divisi dan program dirancang untuk mewadahi minat, bakat, serta semangat juang pemuda dalam mengabdi kepada agama, bangsa, dan masyarakat.')) ?>
            </p>
        </div>

        <div class="row g-4">
            <?php if (!empty($programs)): ?>
                <?php foreach ($programs as $prog): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="program-card">
                            <div class="program-icon-box bg-<?= esc($prog['color'] ?? 'primary') ?> bg-opacity-10 text-<?= esc($prog['color'] ?? 'primary') ?>">
                                <i class="bi <?= esc($prog['icon'] ?? 'bi-grid') ?>"></i>
                            </div>
                            <h5 class="fw-bold text-slate-900 mb-2"><?= esc($prog['title'] ?? '') ?></h5>
                            <p class="text-muted small mb-3 flex-grow-1">
                                <?= nl2br(esc($prog['desc'] ?? '')) ?>
                            </p>
                            <?php if (!empty($prog['badge'])): ?>
                                <div class="pt-2 border-top">
                                    <span class="badge bg-light text-secondary border small"><i class="bi bi-calendar-event me-1"></i> <?= esc($prog['badge']) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback standard program cards -->
                <div class="col-md-6 col-lg-4">
                    <div class="program-card">
                        <div class="program-icon-box bg-danger bg-opacity-10 text-danger"><i class="bi bi-book-half"></i></div>
                        <h5 class="fw-bold text-slate-900 mb-2">Kajian &amp; Tarbiyah Pemuda</h5>
                        <p class="text-muted small mb-3 flex-grow-1">Kajian rutin pemuda tematik, pendalaman tafsir Al-Qur'an, tahsin tilawah, dan hadits shahih.</p>
                        <div class="pt-2 border-top"><span class="badge bg-light text-secondary border small"><i class="bi bi-calendar-event me-1"></i> Rutin Bulanan &amp; Wilayah</span></div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ===================================================
     6. ALUR PENDATAAN & MANFAAT SECTION
     =================================================== -->
<section id="manfaat" class="landing-section bg-light">
    <div class="container">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="section-tag"><i class="bi bi-ui-checks-grid"></i> <?= esc($settings['alur_tag'] ?? 'Alur Pendataan Pemuda') ?></span>
            <h2 class="section-heading mb-2"><?= esc($settings['alur_title'] ?? '4 Langkah Mudah Pengisian Data') ?></h2>
            <p class="section-desc">
                <?= nl2br(esc($settings['alur_desc'] ?? 'Proses pendataan dilakukan secara online, cepat, dan transparan. Ikuti 4 tahapan berikut untuk melengkapi profil Anda.')) ?>
            </p>
        </div>

        <div class="row g-4 mb-5">
            <?php if (!empty($alurSteps)): ?>
                <?php foreach ($alurSteps as $s): ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="step-card">
                            <div class="step-badge"><?= esc($s['step'] ?? '') ?></div>
                            <h6 class="fw-bold text-slate-900 mb-2"><?= esc($s['title'] ?? '') ?></h6>
                            <p class="small text-muted mb-0">
                                <?= nl2br(esc($s['desc'] ?? '')) ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-md-6 col-lg-3">
                    <div class="step-card">
                        <div class="step-badge">1</div>
                        <h6 class="fw-bold text-slate-900 mb-2">Akses Formulir</h6>
                        <p class="small text-muted mb-0">Klik tombol "Form Pendataan" pada menu navigasi atau halaman ini untuk membuka form registrasi.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Big CTA Banner Strip -->
        <div class="cta-banner">
            <div class="row align-items-center gy-4 position-relative" style="z-index: 2;">
                <div class="col-lg-8 text-center text-lg-start">
                    <h3 class="fw-bold text-white mb-2"><?= esc($settings['cta_title'] ?? 'Sudahkah Anda Terdata di Sistem Pemuda MTA Sragen?') ?></h3>
                    <p class="text-white-50 mb-0 fs-6">
                        <?= nl2br(esc($settings['cta_desc'] ?? 'Mari berkontribusi aktif dalam barisan dakwah dan kemaslahatan umat. Satu data pemuda untuk kemajuan bersama se-Kabupaten Sragen.')) ?>
                    </p>
                </div>
                <div class="col-lg-4 text-center text-lg-end">
                    <a href="<?= base_url('pendataan') ?>" class="btn btn-light text-danger fw-bold px-4 py-3 rounded-pill shadow-lg" style="color: #991b1b !important;">
                        <i class="bi bi-pencil-square me-2"></i> <?= esc($settings['cta_btn_text'] ?? 'Isi Formulir Sekarang') ?>
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
            <span class="section-tag"><i class="bi bi-question-circle-fill"></i> <?= esc($settings['faq_tag'] ?? 'Tanya Jawab') ?></span>
            <h2 class="section-heading mb-2"><?= esc($settings['faq_title'] ?? 'Pertanyaan yang Sering Diajukan (FAQ)') ?></h2>
            <p class="section-desc">
                <?= nl2br(esc($settings['faq_desc'] ?? 'Informasi seputar sistem pendataan, validitas data, dan keanggotaan Pemuda MTA Perwakilan Sragen.')) ?>
            </p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="accordion accordion-custom" id="accordionFaq">
                    <?php if (!empty($faqs)): ?>
                        <?php foreach ($faqs as $fIdx => $faq): ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button <?= $fIdx === 0 ? '' : 'collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?= $fIdx ?>">
                                        <i class="bi bi-patch-question text-danger me-2"></i> <?= esc($faq['q'] ?? '') ?>
                                    </button>
                                </h2>
                                <div id="faq<?= $fIdx ?>" class="accordion-collapse collapse <?= $fIdx === 0 ? 'show' : '' ?>" data-bs-parent="#accordionFaq">
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
     8. KONTAK & SEKRETARIAT SECTION
     =================================================== -->
<section id="kontak" class="landing-section bg-light">
    <div class="container">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="section-tag"><i class="bi bi-telephone-fill"></i> <?= esc($settings['kontak_tag'] ?? 'Layanan & Kontak') ?></span>
            <h2 class="section-heading mb-2"><?= esc($settings['kontak_title'] ?? 'Sekretariat Pemuda MTA Sragen') ?></h2>
            <p class="section-desc">
                <?= nl2br(esc($settings['kontak_desc'] ?? 'Butuh bantuan terkait pengisian data atau informasi kegiatan kepemudaan? Silakan hubungi kami.')) ?>
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
                        <?= nl2br(esc($settings['alamat_kantor'] ?? "Gedung Perwakilan MTA Sragen\nJl. Raya Sukowati, Kabupaten Sragen, Jawa Tengah")) ?>
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="contact-card text-center">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-whatsapp fs-4"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">WhatsApp Center</h6>
                    <p class="small text-muted mb-2"><?= esc($settings['whatsapp_label'] ?? 'Layanan Informasi & Helpdesk') ?></p>
                    <?php $waNum = preg_replace('/[^0-9]/', '', (string) ($settings['whatsapp_number'] ?? '6281234567890')); ?>
                    <a href="https://wa.me/<?= esc($waNum) ?>" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3">
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
