<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('title') ?>Kelola Konten Beranda (Homepage)<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Header Action Bar -->
    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-body p-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="font-weight-bold text-dark mb-1">
                    <i class="fas fa-desktop text-warning mr-2"></i>Portal Pengaturan Konten Beranda
                </h5>
                <p class="text-muted small mb-0">
                    Ubah teks, judul, visi misi, program kerja, FAQ, kontak, dan tautan yang tampil pada halaman utama (landing page).
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?= base_url('/') ?>" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                    <i class="fas fa-external-link-alt mr-1"></i> Buka Web Depan
                </a>
                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3" data-toggle="modal" data-target="#modalResetDefault">
                    <i class="fas fa-undo-alt mr-1"></i> Reset ke Default
                </button>
            </div>
        </div>
    </div>

    <!-- Main Settings Form -->
    <form action="<?= base_url('admin/homepage/update') ?>" method="POST" id="formHomepageSettings">
        <?= csrf_field() ?>
        <input type="hidden" name="active_tab" id="active_tab" value="<?= esc($activeTab) ?>">

        <div class="row">
            <!-- Sidebar Nav Tabs -->
            <div class="col-lg-3 col-md-4 mb-3">
                <div class="card shadow-sm border-0 sticky-top" style="top: 80px; z-index: 10;">
                    <div class="card-header bg-dark py-3">
                        <span class="font-weight-bold text-white text-sm">
                            <i class="fas fa-layer-group mr-1"></i> Bagian Halaman
                        </span>
                    </div>
                    <div class="list-group list-group-flush" id="homepageTabs" role="tablist">
                        <a class="list-group-item list-group-item-action d-flex align-items-center <?= $activeTab === 'hero' ? 'active' : '' ?>" 
                           id="tab-hero-btn" data-toggle="list" href="#pane-hero" role="tab" onclick="setTab('hero')">
                            <i class="fas fa-star mr-2 text-warning"></i>
                            <div>
                                <div class="font-weight-bold">1. Header &amp; Hero</div>
                                <small class="text-muted">Judul utama &amp; ajakan</small>
                            </div>
                        </a>
                        <a class="list-group-item list-group-item-action d-flex align-items-center <?= $activeTab === 'tentang' ? 'active' : '' ?>" 
                           id="tab-tentang-btn" data-toggle="list" href="#pane-tentang" role="tab" onclick="setTab('tentang')">
                            <i class="fas fa-compass mr-2 text-danger"></i>
                            <div>
                                <div class="font-weight-bold">2. Tentang &amp; Visi Misi</div>
                                <small class="text-muted">Profil &amp; 4 pilar misi</small>
                            </div>
                        </a>
                        <a class="list-group-item list-group-item-action d-flex align-items-center <?= $activeTab === 'wilayah' ? 'active' : '' ?>" 
                           id="tab-wilayah-btn" data-toggle="list" href="#pane-wilayah" role="tab" onclick="setTab('wilayah')">
                            <i class="fas fa-map-marked-alt mr-2 text-info"></i>
                            <div>
                                <div class="font-weight-bold">3. Struktur Wilayah</div>
                                <small class="text-muted">Pengantar 4 wilayah</small>
                            </div>
                        </a>
                        <a class="list-group-item list-group-item-action d-flex align-items-center <?= $activeTab === 'program' ? 'active' : '' ?>" 
                           id="tab-program-btn" data-toggle="list" href="#pane-program" role="tab" onclick="setTab('program')">
                            <i class="fas fa-th-large mr-2 text-primary"></i>
                            <div>
                                <div class="font-weight-bold">4. Bidang &amp; Program</div>
                                <small class="text-muted">6 divisi pengabdian</small>
                            </div>
                        </a>
                        <a class="list-group-item list-group-item-action d-flex align-items-center <?= $activeTab === 'alur' ? 'active' : '' ?>" 
                           id="tab-alur-btn" data-toggle="list" href="#pane-alur" role="tab" onclick="setTab('alur')">
                            <i class="fas fa-tasks mr-2 text-success"></i>
                            <div>
                                <div class="font-weight-bold">5. Alur &amp; Banner CTA</div>
                                <small class="text-muted">4 langkah pendataan</small>
                            </div>
                        </a>
                        <a class="list-group-item list-group-item-action d-flex align-items-center <?= $activeTab === 'faq' ? 'active' : '' ?>" 
                           id="tab-faq-btn" data-toggle="list" href="#pane-faq" role="tab" onclick="setTab('faq')">
                            <i class="fas fa-question-circle mr-2 text-secondary"></i>
                            <div>
                                <div class="font-weight-bold">6. Tanya Jawab (FAQ)</div>
                                <small class="text-muted">Pertanyaan &amp; jawaban</small>
                            </div>
                        </a>
                        <a class="list-group-item list-group-item-action d-flex align-items-center <?= $activeTab === 'kontak' ? 'active' : '' ?>" 
                           id="tab-kontak-btn" data-toggle="list" href="#pane-kontak" role="tab" onclick="setTab('kontak')">
                            <i class="fas fa-phone-alt mr-2 text-success"></i>
                            <div>
                                <div class="font-weight-bold">7. Kontak &amp; Sekretariat</div>
                                <small class="text-muted">Alamat &amp; WhatsApp</small>
                            </div>
                        </a>
                    </div>

                    <div class="card-footer bg-light p-3">
                        <button type="submit" class="btn btn-success btn-block font-weight-bold shadow-sm">
                            <i class="fas fa-save mr-1"></i> Simpan Semua Perubahan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tab Content Pane -->
            <div class="col-lg-9 col-md-8">
                <div class="tab-content" id="nav-tabContent">

                    <!-- ==============================================
                         TAB 1: HEADER & HERO BANNER
                         ============================================== -->
                    <div class="tab-pane fade <?= $activeTab === 'hero' ? 'show active' : '' ?>" id="pane-hero" role="tabpanel">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                                <h6 class="font-weight-bold mb-0 text-dark">
                                    <i class="fas fa-star text-warning mr-2"></i>Header Utama &amp; Banner Hero
                                </h6>
                                <span class="badge badge-light border">Bagian Paling Atas</span>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="font-weight-bold">Badge Header (Pill Atas Judul)</label>
                                        <input type="text" name="hero_badge" class="form-control" 
                                               value="<?= esc($settings['hero_badge'] ?? '') ?>" required>
                                        <small class="text-muted">Contoh: Majelis Tafsir Al-Qur'an (MTA) Perwakilan Sragen</small>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="font-weight-bold">Judul Besar Banner Hero</label>
                                        <input type="text" name="hero_title" class="form-control" 
                                               value="<?= esc($settings['hero_title'] ?? '') ?>" required>
                                        <small class="text-muted">Headline utama yang langsung dibaca pengunjung web</small>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="font-weight-bold">Deskripsi / Subjudul Hero</label>
                                        <textarea name="hero_subtitle" class="form-control" rows="3" required><?= esc($settings['hero_subtitle'] ?? '') ?></textarea>
                                        <small class="text-muted">Ringkasan tujuan portal informasi dan pendataan pemuda</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold">Teks Tombol Aksi Utama</label>
                                        <input type="text" name="hero_btn_text" class="form-control" 
                                               value="<?= esc($settings['hero_btn_text'] ?? '') ?>" required>
                                        <small class="text-muted">Arah tombol menuju ke form pendataan</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold">Angka Counter Bidang Pengabdian (Stats Strip)</label>
                                        <input type="text" name="stats_bidang_num" class="form-control" 
                                               value="<?= esc($settings['stats_bidang_num'] ?? '5+') ?>" required>
                                        <small class="text-muted">Contoh: 5+</small>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <h6 class="font-weight-bold text-dark mb-3">
                                    <i class="fas fa-id-card text-primary mr-2"></i>Kartu Samping Banner Hero
                                </h6>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold">Judul Kartu Samping</label>
                                        <input type="text" name="hero_card_title" class="form-control" 
                                               value="<?= esc($settings['hero_card_title'] ?? '') ?>">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold">Deskripsi Kartu Samping</label>
                                        <textarea name="hero_card_desc" class="form-control" rows="2"><?= esc($settings['hero_card_desc'] ?? '') ?></textarea>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="font-weight-bold">Poin Keuntungan / Manfaat (Satu per baris)</label>
                                        <textarea name="hero_card_features" class="form-control" rows="3"><?= esc($settings['hero_card_features'] ?? '') ?></textarea>
                                        <small class="text-muted">Setiap baris akan ditampilkan dengan ikon centang hijau</small>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="font-weight-bold text-dark mb-0">
                                        <i class="fas fa-tags text-warning mr-2"></i>Highlight Chips (Pill Bawah Tombol)
                                    </h6>
                                    <button type="button" class="btn btn-xs btn-outline-primary" onclick="addHeroChip()">
                                        <i class="fas fa-plus mr-1"></i> Tambah Chip
                                    </button>
                                </div>

                                <div id="hero-chips-container">
                                    <?php foreach ($heroChips as $idx => $chip): ?>
                                        <div class="chip-item-row input-group mb-2" id="chip-row-<?= $idx ?>">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="bi <?= esc($chip['icon'] ?? 'bi-check-circle') ?>"></i></span>
                                            </div>
                                            <input type="text" name="hero_chips[<?= $idx ?>][icon]" class="form-control col-md-3" 
                                                   value="<?= esc($chip['icon'] ?? 'bi-check-circle') ?>" placeholder="Nama ikon bootstrap (bi-...)">
                                            <input type="text" name="hero_chips[<?= $idx ?>][text]" class="form-control" 
                                                   value="<?= esc($chip['text'] ?? '') ?>" placeholder="Teks chip">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-danger" onclick="removeRow('chip-row-<?= $idx ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==============================================
                         TAB 2: TENTANG & VISI MISI
                         ============================================== -->
                    <div class="tab-pane fade <?= $activeTab === 'tentang' ? 'show active' : '' ?>" id="pane-tentang" role="tabpanel">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                                <h6 class="font-weight-bold mb-0 text-dark">
                                    <i class="fas fa-compass text-danger mr-2"></i>Profil Organisasi, Visi &amp; 4 Misi
                                </h6>
                                <span class="badge badge-light border">Bagian Profil</span>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold">Tag Section (Label Atas)</label>
                                        <input type="text" name="tentang_tag" class="form-control" 
                                               value="<?= esc($settings['tentang_tag'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold">Judul Utama Profil</label>
                                        <input type="text" name="tentang_title" class="form-control" 
                                               value="<?= esc($settings['tentang_title'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="font-weight-bold">Paragraf 1 (Tentang Pemuda MTA)</label>
                                        <textarea name="tentang_desc_1" class="form-control" rows="3" required><?= esc($settings['tentang_desc_1'] ?? '') ?></textarea>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="font-weight-bold">Paragraf 2 (Aktivitas &amp; Nilai)</label>
                                        <textarea name="tentang_desc_2" class="form-control" rows="3" required><?= esc($settings['tentang_desc_2'] ?? '') ?></textarea>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="font-weight-bold text-danger">Teks Visi Organisasi</label>
                                        <textarea name="visi_text" class="form-control" rows="3" required><?= esc($settings['visi_text'] ?? '') ?></textarea>
                                        <small class="text-muted">Akan ditampilkan menonjol pada kotak merah visi</small>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <h6 class="font-weight-bold text-dark mb-3">
                                    <i class="fas fa-bullseye text-danger mr-2"></i>4 Misi Strategis Organisasi
                                </h6>

                                <div id="misi-list-container">
                                    <?php foreach ($misiList as $idx => $misi): ?>
                                        <div class="card border mb-3 bg-light" id="misi-row-<?= $idx ?>">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <span class="badge badge-danger font-weight-bold px-2 py-1">Misi <?= $idx + 1 ?></span>
                                                </div>
                                                <div class="row g-2">
                                                    <div class="col-md-4 mb-2">
                                                        <label class="small font-weight-bold">Judul Misi</label>
                                                        <input type="text" name="misi_list[<?= $idx ?>][title]" class="form-control form-control-sm" 
                                                               value="<?= esc($misi['title'] ?? '') ?>" required>
                                                    </div>
                                                    <div class="col-md-8 mb-2">
                                                        <label class="small font-weight-bold">Uraian / Deskripsi Misi</label>
                                                        <input type="text" name="misi_list[<?= $idx ?>][desc]" class="form-control form-control-sm" 
                                                               value="<?= esc($misi['desc'] ?? '') ?>" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==============================================
                         TAB 3: STRUKTUR WILAYAH
                         ============================================== -->
                    <div class="tab-pane fade <?= $activeTab === 'wilayah' ? 'show active' : '' ?>" id="pane-wilayah" role="tabpanel">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                                <h6 class="font-weight-bold mb-0 text-dark">
                                    <i class="fas fa-map-marked-alt text-info mr-2"></i>Pengantar Struktur 4 Wilayah &amp; Cabang
                                </h6>
                                <span class="badge badge-light border">Bagian Wilayah</span>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info py-2 px-3 small">
                                    <i class="fas fa-info-circle mr-1"></i> Data daftar cabang di bawahnya otomatis diambil dari Master Wilayah dan Master Cabang di database. Di sini Anda dapat merubah judul dan deskripsi pengantarnya.
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold">Tag Section Wilayah</label>
                                        <input type="text" name="wilayah_tag" class="form-control" 
                                               value="<?= esc($settings['wilayah_tag'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold">Judul Utama Wilayah</label>
                                        <input type="text" name="wilayah_title" class="form-control" 
                                               value="<?= esc($settings['wilayah_title'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="font-weight-bold">Deskripsi Pengantar Wilayah</label>
                                        <textarea name="wilayah_desc" class="form-control" rows="3" required><?= esc($settings['wilayah_desc'] ?? '') ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==============================================
                         TAB 4: BIDANG & PROGRAM KERJA
                         ============================================== -->
                    <div class="tab-pane fade <?= $activeTab === 'program' ? 'show active' : '' ?>" id="pane-program" role="tabpanel">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                                <h6 class="font-weight-bold mb-0 text-dark">
                                    <i class="fas fa-th-large text-primary mr-2"></i>Bidang &amp; Program Kerja Kepemudaan
                                </h6>
                                <span class="badge badge-light border">6 Divisi Program</span>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold">Tag Section Program</label>
                                        <input type="text" name="program_tag" class="form-control" 
                                               value="<?= esc($settings['program_tag'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold">Judul Utama Program</label>
                                        <input type="text" name="program_title" class="form-control" 
                                               value="<?= esc($settings['program_title'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="font-weight-bold">Deskripsi Pengantar Program</label>
                                        <textarea name="program_desc" class="form-control" rows="2" required><?= esc($settings['program_desc'] ?? '') ?></textarea>
                                    </div>
                                </div>

                                <hr class="my-3">

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="font-weight-bold text-dark mb-0">Daftar Kartu Program Kerja</h6>
                                    <button type="button" class="btn btn-xs btn-outline-primary" onclick="addProgramCard()">
                                        <i class="fas fa-plus mr-1"></i> Tambah Program
                                    </button>
                                </div>

                                <div id="programs-container" class="row">
                                    <?php foreach ($programs as $idx => $prog): ?>
                                        <div class="col-md-6 mb-3 program-card-item" id="prog-card-<?= $idx ?>">
                                            <div class="card border h-100 shadow-none bg-light">
                                                <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                                                    <span class="badge badge-<?= esc($prog['color'] ?? 'primary') ?> px-2 py-1">
                                                        <i class="bi <?= esc($prog['icon'] ?? 'bi-grid') ?> mr-1"></i> Program <?= $idx + 1 ?>
                                                    </span>
                                                    <button type="button" class="btn btn-xs btn-outline-danger" onclick="removeRow('prog-card-<?= $idx ?>')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                <div class="card-body p-3">
                                                    <div class="form-group mb-2">
                                                        <label class="small font-weight-bold">Judul Program</label>
                                                        <input type="text" name="program_list[<?= $idx ?>][title]" class="form-control form-control-sm" 
                                                               value="<?= esc($prog['title'] ?? '') ?>" required>
                                                    </div>
                                                    <div class="form-group mb-2">
                                                        <label class="small font-weight-bold">Deskripsi Program</label>
                                                        <textarea name="program_list[<?= $idx ?>][desc]" class="form-control form-control-sm" rows="2" required><?= esc($prog['desc'] ?? '') ?></textarea>
                                                    </div>
                                                    <div class="row g-2">
                                                        <div class="col-6 mb-2">
                                                            <label class="small font-weight-bold">Ikon (Bootstrap Icon)</label>
                                                            <input type="text" name="program_list[<?= $idx ?>][icon]" class="form-control form-control-sm" 
                                                                   value="<?= esc($prog['icon'] ?? 'bi-grid') ?>" placeholder="bi-book-half">
                                                        </div>
                                                        <div class="col-6 mb-2">
                                                            <label class="small font-weight-bold">Warna Tema</label>
                                                            <select name="program_list[<?= $idx ?>][color]" class="form-control form-control-sm">
                                                                <?php foreach (['primary' => 'Biru', 'danger' => 'Merah', 'warning' => 'Kuning', 'success' => 'Hijau', 'info' => 'Cyan', 'secondary' => 'Abu-abu'] as $colVal => $colLabel): ?>
                                                                    <option value="<?= $colVal ?>" <?= ($prog['color'] ?? '') === $colVal ? 'selected' : '' ?>><?= $colLabel ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="small font-weight-bold">Label Badge / Jadwal</label>
                                                            <input type="text" name="program_list[<?= $idx ?>][badge]" class="form-control form-control-sm" 
                                                                   value="<?= esc($prog['badge'] ?? '') ?>" placeholder="Misal: Rutin Bulanan">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==============================================
                         TAB 5: ALUR PENDATAAN & BANNER CTA
                         ============================================== -->
                    <div class="tab-pane fade <?= $activeTab === 'alur' ? 'show active' : '' ?>" id="pane-alur" role="tabpanel">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                                <h6 class="font-weight-bold mb-0 text-dark">
                                    <i class="fas fa-tasks text-success mr-2"></i>Alur 4 Langkah Pendataan &amp; Banner Ajakan (CTA)
                                </h6>
                                <span class="badge badge-light border">Bagian Alur &amp; CTA</span>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold">Tag Section Alur</label>
                                        <input type="text" name="alur_tag" class="form-control" 
                                               value="<?= esc($settings['alur_tag'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold">Judul Utama Alur</label>
                                        <input type="text" name="alur_title" class="form-control" 
                                               value="<?= esc($settings['alur_title'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="font-weight-bold">Deskripsi Pengantar Alur</label>
                                        <textarea name="alur_desc" class="form-control" rows="2" required><?= esc($settings['alur_desc'] ?? '') ?></textarea>
                                    </div>
                                </div>

                                <hr class="my-3">

                                <h6 class="font-weight-bold text-dark mb-3">4 Tahapan Langkah Pengisian Form</h6>
                                <div class="row">
                                    <?php foreach ($alurSteps as $idx => $step): ?>
                                        <div class="col-md-6 mb-3">
                                            <div class="card border h-100 bg-light">
                                                <div class="card-header bg-white py-2 font-weight-bold text-dark">
                                                    <span class="badge badge-success mr-1">Langkah <?= $idx + 1 ?></span>
                                                </div>
                                                <div class="card-body p-3">
                                                    <div class="form-group mb-2">
                                                        <label class="small font-weight-bold">Judul Langkah</label>
                                                        <input type="text" name="alur_steps[<?= $idx ?>][title]" class="form-control form-control-sm" 
                                                               value="<?= esc($step['title'] ?? '') ?>" required>
                                                    </div>
                                                    <div class="form-group mb-0">
                                                        <label class="small font-weight-bold">Deskripsi Langkah</label>
                                                        <textarea name="alur_steps[<?= $idx ?>][desc]" class="form-control form-control-sm" rows="2" required><?= esc($step['desc'] ?? '') ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <hr class="my-4">

                                <h6 class="font-weight-bold text-danger mb-3">
                                    <i class="fas fa-bullhorn text-danger mr-2"></i>Banner Ajakan Besar (CTA Strip)
                                </h6>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="font-weight-bold">Judul Banner Ajakan</label>
                                        <input type="text" name="cta_title" class="form-control" 
                                               value="<?= esc($settings['cta_title'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-8 mb-3">
                                        <label class="font-weight-bold">Deskripsi Banner Ajakan</label>
                                        <textarea name="cta_desc" class="form-control" rows="2" required><?= esc($settings['cta_desc'] ?? '') ?></textarea>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="font-weight-bold">Teks Tombol Banner</label>
                                        <input type="text" name="cta_btn_text" class="form-control" 
                                               value="<?= esc($settings['cta_btn_text'] ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==============================================
                         TAB 6: TANYA JAWAB (FAQ)
                         ============================================== -->
                    <div class="tab-pane fade <?= $activeTab === 'faq' ? 'show active' : '' ?>" id="pane-faq" role="tabpanel">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                                <h6 class="font-weight-bold mb-0 text-dark">
                                    <i class="fas fa-question-circle text-secondary mr-2"></i>Pertanyaan yang Sering Diajukan (FAQ)
                                </h6>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addFaqItem()">
                                    <i class="fas fa-plus mr-1"></i> Tambah Pertanyaan
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold">Tag Section FAQ</label>
                                        <input type="text" name="faq_tag" class="form-control" 
                                               value="<?= esc($settings['faq_tag'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold">Judul Utama FAQ</label>
                                        <input type="text" name="faq_title" class="form-control" 
                                               value="<?= esc($settings['faq_title'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="font-weight-bold">Deskripsi Pengantar FAQ</label>
                                        <textarea name="faq_desc" class="form-control" rows="2" required><?= esc($settings['faq_desc'] ?? '') ?></textarea>
                                    </div>
                                </div>

                                <hr class="my-3">

                                <div id="faq-list-container">
                                    <?php foreach ($faqs as $idx => $faq): ?>
                                        <div class="card border mb-3 bg-light faq-item-row" id="faq-row-<?= $idx ?>">
                                            <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                                                <span class="font-weight-bold text-dark small">
                                                    <i class="fas fa-question text-danger mr-1"></i> Pertanyaan <?= $idx + 1 ?>
                                                </span>
                                                <button type="button" class="btn btn-xs btn-outline-danger" onclick="removeRow('faq-row-<?= $idx ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            <div class="card-body p-3">
                                                <div class="form-group mb-2">
                                                    <label class="small font-weight-bold">Pertanyaan (Tanya)</label>
                                                    <input type="text" name="faq_list[<?= $idx ?>][q]" class="form-control form-control-sm" 
                                                           value="<?= esc($faq['q'] ?? '') ?>" required>
                                                </div>
                                                <div class="form-group mb-0">
                                                    <label class="small font-weight-bold">Jawaban</label>
                                                    <textarea name="faq_list[<?= $idx ?>][a]" class="form-control form-control-sm" rows="2" required><?= esc($faq['a'] ?? '') ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==============================================
                         TAB 7: KONTAK & SEKRETARIAT
                         ============================================== -->
                    <div class="tab-pane fade <?= $activeTab === 'kontak' ? 'show active' : '' ?>" id="pane-kontak" role="tabpanel">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                                <h6 class="font-weight-bold mb-0 text-dark">
                                    <i class="fas fa-phone-alt text-success mr-2"></i>Layanan, Alamat Sekretariat &amp; WhatsApp
                                </h6>
                                <span class="badge badge-light border">Bagian Footer</span>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold">Tag Section Kontak</label>
                                        <input type="text" name="kontak_tag" class="form-control" 
                                               value="<?= esc($settings['kontak_tag'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold">Judul Utama Kontak</label>
                                        <input type="text" name="kontak_title" class="form-control" 
                                               value="<?= esc($settings['kontak_title'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="font-weight-bold">Deskripsi Pengantar Kontak</label>
                                        <textarea name="kontak_desc" class="form-control" rows="2" required><?= esc($settings['kontak_desc'] ?? '') ?></textarea>
                                    </div>

                                    <hr class="col-12 my-3">

                                    <div class="col-md-12 mb-3">
                                        <label class="font-weight-bold">Alamat Lengkap Kantor Sekretariat</label>
                                        <textarea name="alamat_kantor" class="form-control" rows="3" required><?= esc($settings['alamat_kantor'] ?? '') ?></textarea>
                                        <small class="text-muted">Alamat fisik sekretariat perwakilan</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold">Nomor WhatsApp Helpdesk (Format: 628xxx)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fab fa-whatsapp text-success"></i></span>
                                            </div>
                                            <input type="text" name="whatsapp_number" class="form-control" 
                                                   value="<?= esc($settings['whatsapp_number'] ?? '') ?>" placeholder="6281234567890" required>
                                        </div>
                                        <small class="text-muted">Gunakan kode negara tanpa tanda plus atau strip (contoh: 6281234567890)</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold">Label Keterangan WhatsApp</label>
                                        <input type="text" name="whatsapp_label" class="form-control" 
                                               value="<?= esc($settings['whatsapp_label'] ?? '') ?>" required>
                                        <small class="text-muted">Contoh: Layanan Informasi &amp; Helpdesk</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Sticky Bottom Action Bar -->
                <div class="card shadow-sm border-0 bg-white p-3 mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">
                            <i class="fas fa-shield-alt text-success mr-1"></i> Perubahan akan langsung tampil di halaman depan.
                        </span>
                        <button type="submit" class="btn btn-success px-4 font-weight-bold shadow-sm">
                            <i class="fas fa-save mr-1"></i> Simpan Semua Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

</div>

<!-- Modal Konfirmasi Reset Default -->
<div class="modal fade" id="modalResetDefault" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Konfirmasi Reset ke Default
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('admin/homepage/reset') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body text-dark">
                    <p class="mb-2">Apakah Anda yakin ingin <strong>mengembalikan seluruh isi beranda ke teks default awal</strong>?</p>
                    <p class="small text-muted mb-0">Tindakan ini akan menimpa seluruh teks yang telah Anda ubah dengan teks standar bawaan sistem.</p>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm font-weight-bold">
                        <i class="fas fa-undo-alt mr-1"></i> Ya, Reset ke Default
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function setTab(tabName) {
        document.getElementById('active_tab').value = tabName;
    }

    function removeRow(rowId) {
        const el = document.getElementById(rowId);
        if (el) el.remove();
    }

    let chipCount = <?= count($heroChips) ?>;
    function addHeroChip() {
        const container = document.getElementById('hero-chips-container');
        const rowId = 'chip-row-' + chipCount;
        const html = `
            <div class="chip-item-row input-group mb-2" id="${rowId}">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="bi bi-star"></i></span>
                </div>
                <input type="text" name="hero_chips[${chipCount}][icon]" class="form-control col-md-3" 
                       value="bi-star" placeholder="Nama ikon bootstrap (bi-...)">
                <input type="text" name="hero_chips[${chipCount}][text]" class="form-control" 
                       value="" placeholder="Teks chip baru" required>
                <div class="input-group-append">
                    <button type="button" class="btn btn-outline-danger" onclick="removeRow('${rowId}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        chipCount++;
    }

    let progCount = <?= count($programs) ?>;
    function addProgramCard() {
        const container = document.getElementById('programs-container');
        const cardId = 'prog-card-' + progCount;
        const html = `
            <div class="col-md-6 mb-3 program-card-item" id="${cardId}">
                <div class="card border h-100 shadow-none bg-light">
                    <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                        <span class="badge badge-primary px-2 py-1">
                            <i class="bi bi-grid mr-1"></i> Program Baru
                        </span>
                        <button type="button" class="btn btn-xs btn-outline-danger" onclick="removeRow('${cardId}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="card-body p-3">
                        <div class="form-group mb-2">
                            <label class="small font-weight-bold">Judul Program</label>
                            <input type="text" name="program_list[${progCount}][title]" class="form-control form-control-sm" 
                                   value="" placeholder="Nama Program" required>
                        </div>
                        <div class="form-group mb-2">
                            <label class="small font-weight-bold">Deskripsi Program</label>
                            <textarea name="program_list[${progCount}][desc]" class="form-control form-control-sm" rows="2" placeholder="Uraian kegiatan program" required></textarea>
                        </div>
                        <div class="row g-2">
                            <div class="col-6 mb-2">
                                <label class="small font-weight-bold">Ikon (Bootstrap Icon)</label>
                                <input type="text" name="program_list[${progCount}][icon]" class="form-control form-control-sm" 
                                       value="bi-grid" placeholder="bi-book-half">
                            </div>
                            <div class="col-6 mb-2">
                                <label class="small font-weight-bold">Warna Tema</label>
                                <select name="program_list[${progCount}][color]" class="form-control form-control-sm">
                                    <option value="primary">Biru</option>
                                    <option value="danger">Merah</option>
                                    <option value="warning">Kuning</option>
                                    <option value="success">Hijau</option>
                                    <option value="info">Cyan</option>
                                    <option value="secondary">Abu-abu</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="small font-weight-bold">Label Badge / Jadwal</label>
                                <input type="text" name="program_list[${progCount}][badge]" class="form-control form-control-sm" 
                                       value="" placeholder="Misal: Rutin Bulanan">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        progCount++;
    }

    let faqCount = <?= count($faqs) ?>;
    function addFaqItem() {
        const container = document.getElementById('faq-list-container');
        const rowId = 'faq-row-' + faqCount;
        const html = `
            <div class="card border mb-3 bg-light faq-item-row" id="${rowId}">
                <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                    <span class="font-weight-bold text-dark small">
                        <i class="fas fa-question text-danger mr-1"></i> Pertanyaan Baru
                    </span>
                    <button type="button" class="btn btn-xs btn-outline-danger" onclick="removeRow('${rowId}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="card-body p-3">
                    <div class="form-group mb-2">
                        <label class="small font-weight-bold">Pertanyaan (Tanya)</label>
                        <input type="text" name="faq_list[${faqCount}][q]" class="form-control form-control-sm" 
                               value="" placeholder="Ketik pertanyaan yang sering diajukan" required>
                    </div>
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold">Jawaban</label>
                        <textarea name="faq_list[${faqCount}][a]" class="form-control form-control-sm" rows="2" placeholder="Ketik jawaban lengkap dan jelas" required></textarea>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        faqCount++;
    }
</script>
<?= $this->endSection() ?>
