<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<!-- HEADER -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
    <div>
        <div class="d-flex align-items-center mb-1">
            <a href="<?= base_url('admin/pemuda') ?>" class="btn btn-default btn-xs mr-2">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
            </a>
            <span class="badge badge-info px-2 py-1">
                <i class="fas fa-file-export mr-1"></i> Modul Ekspor Data
            </span>
        </div>
        <h4 class="font-weight-bold text-dark mb-1">Export Kustom Data Pemuda</h4>
        <p class="text-muted text-xs mb-0">Pilih elemen kolom data, saring bakat / keahlian, minat, wilayah, dan kriteria pemuda sesuai kebutuhan laporan Anda.</p>
    </div>
    <div class="mt-2 mt-md-0 d-flex align-items-center">
        <a href="<?= base_url('admin/pemuda') ?>" class="btn btn-outline-secondary btn-sm mr-2">
            <i class="fas fa-times mr-1"></i> Batal
        </a>
        <button type="submit" form="exportForm" class="btn btn-success btn-sm shadow-sm font-weight-bold">
            <i class="fas fa-download mr-1"></i> Unduh Berkas Ekspor
        </button>
    </div>
</div>

<!-- LIVE SUMMARY BOX -->
<div class="card bg-gradient-success text-white shadow-sm mb-4 border-0">
    <div class="card-body p-3">
        <div class="row align-items-center">
            <div class="col-md-7 d-flex align-items-center mb-2 mb-md-0">
                <div class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center mr-3 shadow-sm" style="width: 48px; height: 48px; min-width: 48px;">
                    <i class="fas fa-file-excel fa-2x"></i>
                </div>
                <div>
                    <h6 class="text-uppercase font-weight-bold mb-0 text-white-50 text-xs letter-spacing-1">Ringkasan Ekspor</h6>
                    <div class="d-flex align-items-baseline">
                        <span class="font-weight-bold text-lg mr-2" id="liveRecordCount"><?= number_format($initialCount) ?></span>
                        <span class="text-sm">Pemuda memenuhi filter</span>
                        <span class="spinner-border spinner-border-sm ml-2 text-white d-none" id="countSpinner" role="status"></span>
                    </div>
                </div>
            </div>
            <div class="col-md-5 d-flex justify-content-md-end align-items-center">
                <div class="mr-3 text-md-right">
                    <div class="text-xs text-white-50">Elemen Kolom Terpilih:</div>
                    <span id="selectedColBadge" class="badge badge-light text-success font-weight-bold px-2 py-1">
                        13 Kolom Terpilih
                    </span>
                </div>
                <button type="submit" form="exportForm" class="btn btn-light text-success font-weight-bold shadow-sm">
                    <i class="fas fa-download mr-1"></i> Unduh Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MAIN FORM -->
<form id="exportForm" action="<?= base_url('admin/pemuda/export') ?>" method="POST" target="_blank">
    <?= csrf_field() ?>

    <!-- STEP 1: PILIHAN ELEMEN DATA (KOLOM EKSPOR) -->
    <div class="card card-outline card-success shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div>
                    <h5 class="card-title font-weight-bold text-dark mb-0">
                        <span class="badge badge-success mr-2">1</span> Pilihan Elemen Data (Kolom Spreadsheet)
                    </h5>
                    <p class="text-muted text-xs mb-0 mt-1">Tentukan kolom mana saja yang ingin dimunculkan dalam berkas hasil ekspor.</p>
                </div>
                <div class="mt-2 mt-md-0 d-flex flex-wrap gap-1">
                    <span class="text-xs text-muted mr-2 align-self-center font-weight-bold">Preset Cepat:</span>
                    <button type="button" class="btn btn-outline-success btn-xs preset-btn mr-1" data-preset="default">
                        <i class="fas fa-check-circle mr-1"></i> Standar (13)
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-xs preset-btn mr-1" data-preset="all">
                        <i class="fas fa-list-ul mr-1"></i> Lengkap (Semua)
                    </button>
                    <button type="button" class="btn btn-outline-info btn-xs preset-btn mr-1" data-preset="contact">
                        <i class="fas fa-address-book mr-1"></i> Kontak &amp; Alamat
                    </button>
                    <button type="button" class="btn btn-outline-warning btn-xs preset-btn mr-1" data-preset="potensi">
                        <i class="fas fa-star mr-1"></i> Bakat &amp; Potensi
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-xs preset-btn mr-1" data-preset="business">
                        <i class="fas fa-briefcase mr-1"></i> Wirausaha / Usaha
                    </button>
                    <button type="button" class="btn btn-default btn-xs" id="btnDeselectAllCols">
                        <i class="fas fa-times mr-1"></i> Kosongkan
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body p-3 bg-light">
            <div class="row">
                <?php foreach ($categorizedColumns as $catKey => $category): ?>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="card h-100 shadow-none border">
                            <div class="card-header bg-white py-2 px-3 d-flex justify-content-between align-items-center">
                                <h6 class="font-weight-bold text-dark text-xs mb-0">
                                    <i class="<?= $category['category_icon'] ?> text-success mr-1"></i>
                                    <?= esc($category['category_name']) ?>
                                    <span class="badge badge-secondary ml-1 category-count" data-cat="<?= $catKey ?>">0/<?= count($category['columns']) ?></span>
                                </h6>
                                <button type="button" class="btn btn-link btn-xs p-0 text-muted toggle-category-cols" data-cat="<?= $catKey ?>">
                                    Toggle Semua
                                </button>
                            </div>
                            <div class="card-body p-2" style="max-height: 240px; overflow-y: auto;">
                                <?php foreach ($category['columns'] as $colKey => $colLabel): ?>
                                    <div class="custom-control custom-checkbox mb-1">
                                        <input type="checkbox" 
                                               class="custom-control-input col-checkbox" 
                                               id="col_<?= $colKey ?>" 
                                               name="columns[]" 
                                               value="<?= $colKey ?>"
                                               data-category="<?= $catKey ?>"
                                               <?= in_array($colKey, $presets['default'], true) ? 'checked' : '' ?>>
                                        <label class="custom-control-label text-xs font-weight-normal cursor-pointer" for="col_<?= $colKey ?>">
                                            <?= esc($colLabel) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- STEP 2: FILTER BAKAT / KEAHLIAN & MINAT -->
    <div class="card card-outline card-warning shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div>
                    <h5 class="card-title font-weight-bold text-dark mb-0">
                        <span class="badge badge-warning text-dark mr-2">2</span> Saring Berdasarkan Bakat / Keahlian &amp; Minat
                    </h5>
                    <p class="text-muted text-xs mb-0 mt-1">Pilih bakat atau minat spesifik untuk memfilter pemuda yang memilikinya. Jika tidak ada yang dipilih, data tidak dibatasi bakat/minat.</p>
                </div>
                <div class="mt-2 mt-md-0">
                    <button type="button" class="btn btn-default btn-xs" id="btnClearSkillsInterests">
                        <i class="fas fa-undo mr-1"></i> Kosongkan Pilihan Bakat &amp; Minat
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body p-3">
            <div class="row">
                <!-- KOLOM KIRI: BAKAT / KEAHLIAN -->
                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="border rounded p-3 h-100 bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="font-weight-bold text-primary mb-0 text-sm">
                                <i class="fas fa-tools mr-1"></i> Bakat &amp; Keahlian (Skills)
                            </h6>
                            <span class="badge badge-primary" id="badgeSelectedSkills">0 Dipilih</span>
                        </div>
                        <p class="text-muted text-xs mb-2">Pilih keahlian untuk memfilter pemuda dengan keahlian tersebut.</p>
                        
                        <!-- Search Box for Skills -->
                        <div class="input-group input-group-sm mb-2">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light"><i class="fas fa-search text-muted"></i></span>
                            </div>
                            <input type="text" class="form-control" id="searchSkillsInput" placeholder="Cari keahlian...">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-default btn-xs" id="selectAllSkills">Pilih Semua</button>
                            </div>
                        </div>

                        <div class="border rounded p-2" style="max-height: 250px; overflow-y: auto; background-color: #fafafa;">
                            <?php if (empty($skills)): ?>
                                <p class="text-muted text-xs text-center my-3">Belum ada data keahlian di sistem.</p>
                            <?php else: ?>
                                <div class="row" id="skillsContainer">
                                    <?php foreach ($skills as $s): ?>
                                        <div class="col-sm-6 skill-item mb-1">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" 
                                                       class="custom-control-input filter-input skill-checkbox" 
                                                       id="skill_<?= $s['id'] ?>" 
                                                       name="skill_id[]" 
                                                       value="<?= $s['id'] ?>"
                                                       <?= (!empty($filters['skill_id']) && (is_array($filters['skill_id']) ? in_array($s['id'], $filters['skill_id']) : (int)$filters['skill_id'] === (int)$s['id'])) ? 'checked' : '' ?>>
                                                <label class="custom-control-label text-xs font-weight-normal cursor-pointer" for="skill_<?= $s['id'] ?>" title="<?= esc($s['description'] ?? '') ?>">
                                                    <?= esc($s['name']) ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- KOLOM KANAN: MINAT -->
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100 bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="font-weight-bold text-info mb-0 text-sm">
                                <i class="fas fa-heart mr-1"></i> Minat (Interests)
                            </h6>
                            <span class="badge badge-info" id="badgeSelectedInterests">0 Dipilih</span>
                        </div>
                        <p class="text-muted text-xs mb-2">Pilih bidang minat untuk memfilter pemuda yang memiliki minat tersebut.</p>

                        <!-- Search Box for Interests -->
                        <div class="input-group input-group-sm mb-2">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light"><i class="fas fa-search text-muted"></i></span>
                            </div>
                            <input type="text" class="form-control" id="searchInterestsInput" placeholder="Cari minat...">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-default btn-xs" id="selectAllInterests">Pilih Semua</button>
                            </div>
                        </div>

                        <div class="border rounded p-2" style="max-height: 250px; overflow-y: auto; background-color: #fafafa;">
                            <?php if (empty($interests)): ?>
                                <p class="text-muted text-xs text-center my-3">Belum ada data minat di sistem.</p>
                            <?php else: ?>
                                <div class="row" id="interestsContainer">
                                    <?php foreach ($interests as $i): ?>
                                        <div class="col-sm-6 interest-item mb-1">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" 
                                                       class="custom-control-input filter-input interest-checkbox" 
                                                       id="interest_<?= $i['id'] ?>" 
                                                       name="interest_id[]" 
                                                       value="<?= $i['id'] ?>"
                                                       <?= (!empty($filters['interest_id']) && (is_array($filters['interest_id']) ? in_array($i['id'], $filters['interest_id']) : (int)$filters['interest_id'] === (int)$i['id'])) ? 'checked' : '' ?>>
                                                <label class="custom-control-label text-xs font-weight-normal cursor-pointer" for="interest_<?= $i['id'] ?>" title="<?= esc($i['description'] ?? '') ?>">
                                                    <?= esc($i['name']) ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- STEP 3: FILTER DEMOGRAFI & WILAYAH / CABANG -->
    <div class="card card-outline card-primary shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title font-weight-bold text-dark mb-0">
                        <span class="badge badge-primary mr-2">3</span> Saring Demografi &amp; Wilayah / Cabang
                    </h5>
                    <p class="text-muted text-xs mb-0 mt-1">Batasi hasil ekspor berdasarkan wilayah, cabang, usia, gender, atau status pekerjaan.</p>
                </div>
                <button type="button" class="btn btn-default btn-xs" id="btnResetFilters">
                    <i class="fas fa-undo mr-1"></i> Reset Filter Kriteria
                </button>
            </div>
        </div>

        <div class="card-body p-3">
            <div class="row">
                <!-- WILAYAH -->
                <div class="col-md-3 col-sm-6 mb-3">
                    <label class="form-label text-xs font-weight-bold text-muted mb-1">Wilayah</label>
                    <?php if ($scope['role'] === 'superadmin'): ?>
                        <select class="form-control form-control-sm filter-input" id="filter_wilayah_id" name="wilayah_id">
                            <option value="">Semua Wilayah</option>
                            <?php foreach ($wilayahList as $w): ?>
                                <option value="<?= $w['id'] ?>" <?= (!empty($filters['wilayah_id']) && (int)$filters['wilayah_id'] === (int)$w['id']) ? 'selected' : '' ?>>
                                    <?= esc($w['code'] . ' - ' . $w['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <input type="text" class="form-control form-control-sm bg-light" value="<?= esc($wilayahList[0]['name'] ?? 'Wilayah Anda') ?>" readonly>
                        <input type="hidden" name="wilayah_id" value="<?= esc($scope['wilayah_id']) ?>">
                    <?php endif; ?>
                </div>

                <!-- CABANG -->
                <div class="col-md-3 col-sm-6 mb-3">
                    <label class="form-label text-xs font-weight-bold text-muted mb-1">Cabang Pemuda</label>
                    <?php if (in_array($scope['role'], ['admin_cabang', 'admin_pemuda', 'admin_pemudi'], true)): ?>
                        <input type="text" class="form-control form-control-sm bg-light" value="<?= esc($cabangList[0]['name'] ?? 'Cabang Anda') ?>" readonly>
                        <input type="hidden" name="cabang_id" value="<?= esc($scope['cabang_id']) ?>">
                    <?php else: ?>
                        <select class="form-control form-control-sm filter-input" id="filter_cabang_id" name="cabang_id">
                            <option value="">Semua Cabang</option>
                            <?php foreach ($cabangList as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= (!empty($filters['cabang_id']) && (int)$filters['cabang_id'] === (int)$c['id']) ? 'selected' : '' ?>>
                                    <?= esc($c['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>

                <!-- JENIS KELAMIN -->
                <div class="col-md-3 col-sm-6 mb-3">
                    <label class="form-label text-xs font-weight-bold text-muted mb-1">Jenis Kelamin</label>
                    <?php if (in_array($scope['role'], ['admin_wilayah_pemuda', 'admin_pemuda'], true)): ?>
                        <input type="text" class="form-control form-control-sm bg-light text-primary font-weight-bold" value="Laki-laki (Ikhwan)" readonly>
                        <input type="hidden" name="gender" value="L">
                    <?php elseif ($scope['role'] === 'admin_pemudi'): ?>
                        <input type="text" class="form-control form-control-sm bg-light text-danger font-weight-bold" value="Perempuan (Akhwat)" readonly>
                        <input type="hidden" name="gender" value="P">
                    <?php else: ?>
                        <select class="form-control form-control-sm filter-input" id="filter_gender" name="gender">
                            <option value="">Semua Gender</option>
                            <option value="L" <?= (!empty($filters['gender']) && $filters['gender'] === 'L') ? 'selected' : '' ?>>Laki-laki (Ikhwan)</option>
                            <option value="P" <?= (!empty($filters['gender']) && $filters['gender'] === 'P') ? 'selected' : '' ?>>Perempuan (Akhwat)</option>
                        </select>
                    <?php endif; ?>
                </div>

                <!-- STATUS VERIFIKASI -->
                <div class="col-md-3 col-sm-6 mb-3">
                    <label class="form-label text-xs font-weight-bold text-muted mb-1">Status Verifikasi MTA</label>
                    <select class="form-control form-control-sm filter-input" id="filter_status_verifikasi" name="status_verifikasi">
                        <option value="">Semua Status</option>
                        <option value="verified" <?= (!empty($filters['status_verifikasi']) && $filters['status_verifikasi'] === 'verified') ? 'selected' : '' ?>>Terverifikasi</option>
                        <option value="pending" <?= (!empty($filters['status_verifikasi']) && $filters['status_verifikasi'] === 'pending') ? 'selected' : '' ?>>Belum Terverifikasi</option>
                    </select>
                </div>

                <!-- STATUS PEKERJAAN -->
                <div class="col-md-3 col-sm-6 mb-3">
                    <label class="form-label text-xs font-weight-bold text-muted mb-1">Status Pekerjaan</label>
                    <select class="form-control form-control-sm filter-input" id="filter_job_status_id" name="job_status_id">
                        <option value="">Semua Pekerjaan</option>
                        <?php foreach ($jobStatuses as $js): ?>
                            <option value="<?= $js['id'] ?>" <?= (!empty($filters['job_status_id']) && (int)$filters['job_status_id'] === (int)$js['id']) ? 'selected' : '' ?>>
                                <?= esc($js['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- JENJANG PENDIDIKAN -->
                <div class="col-md-3 col-sm-6 mb-3">
                    <label class="form-label text-xs font-weight-bold text-muted mb-1">Jenjang Pendidikan Terakhir</label>
                    <select class="form-control form-control-sm filter-input" id="filter_education_level_id" name="education_level_id">
                        <option value="">Semua Pendidikan</option>
                        <?php foreach ($educationLevels as $el): ?>
                            <option value="<?= $el['id'] ?>" <?= (!empty($filters['education_level_id']) && (int)$filters['education_level_id'] === (int)$el['id']) ? 'selected' : '' ?>>
                                <?= esc($el['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- RENTANG USIA -->
                <div class="col-md-3 col-sm-6 mb-3">
                    <label class="form-label text-xs font-weight-bold text-muted mb-1">Rentang Usia (Tahun)</label>
                    <div class="input-group input-group-sm">
                        <input type="number" class="form-control filter-input" name="min_age" id="filter_min_age" placeholder="Min" min="10" max="60" value="<?= esc($filters['min_age'] ?? '') ?>">
                        <div class="input-group-prepend input-group-append">
                            <span class="input-group-text">s/d</span>
                        </div>
                        <input type="number" class="form-control filter-input" name="max_age" id="filter_max_age" placeholder="Max" min="10" max="60" value="<?= esc($filters['max_age'] ?? '') ?>">
                    </div>
                </div>

                <!-- STATUS DATA -->
                <div class="col-md-3 col-sm-6 mb-3">
                    <label class="form-label text-xs font-weight-bold text-muted mb-1">Status Data Sistem</label>
                    <select class="form-control form-control-sm filter-input" id="filter_status_data" name="status_data">
                        <option value="active" <?= (!isset($filters['status_data']) || $filters['status_data'] === 'active') ? 'selected' : '' ?>>Aktif Saja</option>
                        <option value="archived" <?= (isset($filters['status_data']) && $filters['status_data'] === 'archived') ? 'selected' : '' ?>>Arsip Saja</option>
                        <option value="all" <?= (!isset($filters['status_data']) && isset($_GET['status_data']) && $_GET['status_data'] === 'all') ? 'selected' : '' ?>>Semua Data (Aktif &amp; Arsip)</option>
                    </select>
                </div>

                <!-- ELEMENT DAKWAH -->
                <div class="col-md-4 col-sm-6 mb-3">
                    <label class="form-label text-xs font-weight-bold text-muted mb-1">Riwayat Element Dakwah Yang Diikuti</label>
                    <input type="text" class="form-control form-control-sm filter-input" name="organization_name" id="filter_organization_name" placeholder="Misal: Satgas, Bankom, Tim Parkir, Tim Ikhrom..." value="<?= esc($filters['organization_name'] ?? '') ?>">
                </div>

                <!-- PENCARIAN KATA KUNCI -->
                <div class="col-md-4 col-sm-6 mb-3">
                    <label class="form-label text-xs font-weight-bold text-muted mb-1">Pencarian Bebas (Nama / No. Reg / WA)</label>
                    <input type="text" class="form-control form-control-sm filter-input" name="search" id="filter_search" placeholder="Ketik kata kunci pencarian..." value="<?= esc($filters['search'] ?? '') ?>">
                </div>

                <!-- TANGGAL REGISTRASI -->
                <div class="col-md-4 col-sm-12 mb-3">
                    <label class="form-label text-xs font-weight-bold text-muted mb-1">Tanggal Registrasi (Periode)</label>
                    <div class="input-group input-group-sm">
                        <input type="date" class="form-control filter-input" name="start_date" id="filter_start_date" value="<?= esc($filters['start_date'] ?? '') ?>">
                        <div class="input-group-prepend input-group-append">
                            <span class="input-group-text">-</span>
                        </div>
                        <input type="date" class="form-control filter-input" name="end_date" id="filter_end_date" value="<?= esc($filters['end_date'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- STEP 4: PILIHAN FORMAT BERKAS & TOMBOL EKSEKUSI -->
    <div class="card shadow-sm mb-5 border">
        <div class="card-header bg-white py-3">
            <h5 class="card-title font-weight-bold text-dark mb-0">
                <span class="badge badge-secondary mr-2">4</span> Format Berkas &amp; Unduh
            </h5>
        </div>
        <div class="card-body p-3">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label class="form-label text-xs font-weight-bold text-muted d-block mb-2">Pilih Format Berkas Ekspor:</label>
                    <div class="d-flex align-items-center">
                        <div class="custom-control custom-radio custom-control-inline mr-4">
                            <input type="radio" id="format_xlsx" name="format" value="xlsx" class="custom-control-input" checked>
                            <label class="custom-control-label font-weight-bold cursor-pointer d-flex align-items-center" for="format_xlsx">
                                <i class="fas fa-file-excel text-success mr-2 fa-lg"></i>
                                <span>Microsoft Excel (.xlsx)</span>
                                <span class="badge badge-success ml-2 font-weight-normal text-xs">Direkomendasikan</span>
                            </label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="format_csv" name="format" value="csv" class="custom-control-input">
                            <label class="custom-control-label font-weight-bold cursor-pointer d-flex align-items-center" for="format_csv">
                                <i class="fas fa-file-csv text-info mr-2 fa-lg"></i>
                                <span>CSV (.csv)</span>
                            </label>
                        </div>
                    </div>
                    <small class="text-muted d-block mt-1">Berkas Excel dilengkapi format tabel rapi, penyesuaian lebar otomatis, dan format teks nomor telepon.</small>
                </div>

                <div class="col-md-6 text-md-right">
                    <button type="button" class="btn btn-outline-secondary btn-lg mr-2" id="btnResetAll">
                        <i class="fas fa-undo mr-1"></i> Reset Semua
                    </button>
                    <button type="submit" class="btn btn-success btn-lg px-4 shadow font-weight-bold">
                        <i class="fas fa-download mr-2"></i> Unduh Berkas Ekspor
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Preset Columns Data
    var presets = <?= json_encode($presets) ?>;
    var countTimeout = null;

    // 1. Update Category Counters and Total Selected Columns
    function updateColumnCounts() {
        var totalCols = $('.col-checkbox').length;
        var selectedCols = $('.col-checkbox:checked').length;

        $('#selectedColBadge').text(selectedCols + ' Kolom Terpilih');

        // Update each category badge
        $('.category-count').each(function() {
            var cat = $(this).data('cat');
            var catTotal = $('.col-checkbox[data-category="' + cat + '"]').length;
            var catChecked = $('.col-checkbox[data-category="' + cat + '"]:checked').length;
            $(this).text(catChecked + '/' + catTotal);
            if (catChecked > 0) {
                $(this).removeClass('badge-secondary').addClass('badge-success');
            } else {
                $(this).removeClass('badge-success').addClass('badge-secondary');
            }
        });
    }

    // 2. Update Skill & Interest badges
    function updateSkillInterestCounts() {
        var selectedSkills = $('.skill-checkbox:checked').length;
        var selectedInterests = $('.interest-checkbox:checked').length;

        $('#badgeSelectedSkills').text(selectedSkills + ' Dipilih');
        $('#badgeSelectedInterests').text(selectedInterests + ' Dipilih');

        if (selectedSkills > 0) {
            $('#badgeSelectedSkills').removeClass('badge-secondary').addClass('badge-primary');
        } else {
            $('#badgeSelectedSkills').removeClass('badge-primary').addClass('badge-secondary');
        }

        if (selectedInterests > 0) {
            $('#badgeSelectedInterests').removeClass('badge-secondary').addClass('badge-info');
        } else {
            $('#badgeSelectedInterests').removeClass('badge-info').addClass('badge-secondary');
        }
    }

    // 3. Apply Preset
    function applyPreset(presetKey) {
        if (!presets[presetKey]) return;
        var cols = presets[presetKey];
        
        $('.col-checkbox').prop('checked', false);
        cols.forEach(function(colName) {
            $('#col_' + colName).prop('checked', true);
        });

        updateColumnCounts();
    }

    $('.preset-btn').on('click', function() {
        var p = $(this).data('preset');
        applyPreset(p);
    });

    $('#btnDeselectAllCols').on('click', function() {
        $('.col-checkbox').prop('checked', false);
        updateColumnCounts();
    });

    // 4. Toggle Category Checkboxes
    $('.toggle-category-cols').on('click', function() {
        var cat = $(this).data('cat');
        var checkboxes = $('.col-checkbox[data-category="' + cat + '"]');
        var allChecked = checkboxes.filter(':checked').length === checkboxes.length;
        checkboxes.prop('checked', !allChecked);
        updateColumnCounts();
    });

    $('.col-checkbox').on('change', function() {
        updateColumnCounts();
    });

    // 5. Filter Input Changes -> Debounced Live Count AJAX
    function fetchLiveCount() {
        $('#countSpinner').removeClass('d-none');
        
        // Serialize all filter inputs
        var formData = $('#exportForm').serialize();

        $.ajax({
            url: '<?= base_url('admin/pemuda/export/count') ?>',
            type: 'GET',
            data: formData,
            dataType: 'json',
            success: function(res) {
                if (res && res.success) {
                    var formatted = new Intl.NumberFormat('id-ID').format(res.count);
                    $('#liveRecordCount').text(formatted);
                }
            },
            error: function(err) {
                console.error('Gagal menghitung data filter', err);
            },
            complete: function() {
                $('#countSpinner').addClass('d-none');
            }
        });
    }

    function triggerLiveCount() {
        clearTimeout(countTimeout);
        countTimeout = setTimeout(fetchLiveCount, 350);
    }

    $('.filter-input').on('change input', function() {
        triggerLiveCount();
        updateSkillInterestCounts();
    });

    // 6. Search within Skills List
    $('#searchSkillsInput').on('keyup input', function() {
        var q = $(this).val().toLowerCase().trim();
        $('.skill-item').each(function() {
            var text = $(this).find('label').text().toLowerCase();
            if (text.indexOf(q) !== -1 || q === '') {
                $(this).removeClass('d-none');
            } else {
                $(this).addClass('d-none');
            }
        });
    });

    // 7. Search within Interests List
    $('#searchInterestsInput').on('keyup input', function() {
        var q = $(this).val().toLowerCase().trim();
        $('.interest-item').each(function() {
            var text = $(this).find('label').text().toLowerCase();
            if (text.indexOf(q) !== -1 || q === '') {
                $(this).removeClass('d-none');
            } else {
                $(this).addClass('d-none');
            }
        });
    });

    // 8. Select All visible skills / interests
    $('#selectAllSkills').on('click', function() {
        var visibleCheckboxes = $('.skill-item:not(.d-none) .skill-checkbox');
        var allChecked = visibleCheckboxes.filter(':checked').length === visibleCheckboxes.length;
        visibleCheckboxes.prop('checked', !allChecked);
        updateSkillInterestCounts();
        triggerLiveCount();
    });

    $('#selectAllInterests').on('click', function() {
        var visibleCheckboxes = $('.interest-item:not(.d-none) .interest-checkbox');
        var allChecked = visibleCheckboxes.filter(':checked').length === visibleCheckboxes.length;
        visibleCheckboxes.prop('checked', !allChecked);
        updateSkillInterestCounts();
        triggerLiveCount();
    });

    $('#btnClearSkillsInterests').on('click', function() {
        $('.skill-checkbox, .interest-checkbox').prop('checked', false);
        $('#searchSkillsInput, #searchInterestsInput').val('').trigger('input');
        updateSkillInterestCounts();
        triggerLiveCount();
    });

    // 9. Dynamic Cabang by Wilayah (for Superadmin)
    $('#filter_wilayah_id').on('change', function() {
        var wilayahId = $(this).val();
        var $cabangSelect = $('#filter_cabang_id');

        if (!wilayahId) {
            $cabangSelect.html('<option value="">Semua Cabang</option>');
            triggerLiveCount();
            return;
        }

        $cabangSelect.prop('disabled', true).html('<option value="">Memuat cabang...</option>');

        $.getJSON('<?= base_url('admin/ajax/cabang/') ?>/' + wilayahId, function(data) {
            var options = '<option value="">Semua Cabang</option>';
            if (data && data.length > 0) {
                $.each(data, function(index, c) {
                    options += '<option value="' + c.id + '">' + (c.name || c.cabang_name) + '</option>';
                });
            }
            $cabangSelect.html(options).prop('disabled', false);
            triggerLiveCount();
        }).fail(function() {
            $cabangSelect.html('<option value="">Semua Cabang</option>').prop('disabled', false);
            triggerLiveCount();
        });
    });

    // 10. Reset Filter Kriteria Demografi
    $('#btnResetFilters').on('click', function() {
        $('#filter_gender').val('');
        $('#filter_status_verifikasi').val('');
        $('#filter_job_status_id').val('');
        $('#filter_education_level_id').val('');
        $('#filter_min_age').val('');
        $('#filter_max_age').val('');
        $('#filter_status_data').val('active');
        $('#filter_organization_name').val('');
        $('#filter_search').val('');
        $('#filter_start_date').val('');
        $('#filter_end_date').val('');
        if ($('#filter_wilayah_id').length) {
            $('#filter_wilayah_id').val('');
            $('#filter_cabang_id').html('<option value="">Semua Cabang</option>');
        } else if ($('#filter_cabang_id').length) {
            $('#filter_cabang_id').val('');
        }
        triggerLiveCount();
    });

    // 11. Reset All (Presets + Filters + Bakat/Minat)
    $('#btnResetAll').on('click', function() {
        applyPreset('default');
        $('#btnClearSkillsInterests').trigger('click');
        $('#btnResetFilters').trigger('click');
    });

    // 12. Form Validation before submit
    $('#exportForm').on('submit', function(e) {
        var checkedCols = $('.col-checkbox:checked').length;
        if (checkedCols === 0) {
            e.preventDefault();
            alert('Silakan pilih minimal 1 kolom elemen data untuk diekspor.');
            applyPreset('default');
            return false;
        }
    });

    // Initialize counts on page load
    updateColumnCounts();
    updateSkillInterestCounts();
});
</script>
<?= $this->endSection() ?>
