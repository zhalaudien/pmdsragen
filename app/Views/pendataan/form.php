<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Form Pendataan Pemuda<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<!-- Tom Select CSS for Searchable Selects -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('css/pendataan.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container py-4 py-md-5">
    <!-- Breadcrumb & Navigation -->
    <div class="row justify-content-center mb-3">
        <div class="col-lg-10 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <a href="<?= base_url('/') ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda
            </a>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>" class="text-decoration-none text-muted"><i class="bi bi-house-door me-1"></i>Beranda</a></li>
                    <li class="breadcrumb-item active text-danger fw-semibold" aria-current="page">Form Pendataan Pemuda</li>
                </ol>
            </nav>
        </div>
    </div>

<!-- Page Hero Header -->
<div class="row justify-content-center mb-4">
    <div class="col-lg-10 text-center">
        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-pmd-badge fw-semibold small mb-2">
            <i class="bi bi-patch-check-fill text-pmd-red"></i> Database Pemuda MTA Perwakilan Sragen
        </div>
        <h1 class="h2 fw-bold text-slate-900 mb-2">Formulir Pendataan Pemuda</h1>
        <p class="text-muted mx-auto" style="max-width: 650px;">
            Lengkapi profil dan data diri Anda untuk pemetaan potensi, program pemberdayaan, dan pengembangan pemuda MTA Perwakilan Sragen.
        </p>
    </div>
</div>

<!-- Main Form Container -->
<div class="row justify-content-center">
    <div class="col-lg-10">

        <!-- Server-side Alert Feedback -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show card-custom border-danger" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div><?= session()->getFlashdata('error') ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show card-custom border-danger" role="alert">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-x-circle-fill fs-5"></i>
                    <strong>Mohon perbaiki kesalahan berikut:</strong>
                </div>
                <ul class="mb-0 ps-3">
                    <?php foreach (session()->getFlashdata('errors') as $err): ?>
                        <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Multi-step Navigation Stepper -->
        <div class="stepper-wrapper">
            <div class="stepper-progress-bar">
                <div class="stepper-progress-fill" id="stepperProgressFill"></div>
            </div>

            <div class="stepper-item active" data-step="1" onclick="goToStep(1)">
                <div class="stepper-circle"><i class="bi bi-person"></i></div>
                <span class="stepper-title">Data Pribadi</span>
            </div>
            <div class="stepper-item" data-step="2" onclick="goToStep(2)">
                <div class="stepper-circle"><i class="bi bi-geo-alt"></i></div>
                <span class="stepper-title">Alamat</span>
            </div>
            <div class="stepper-item" data-step="3" onclick="goToStep(3)">
                <div class="stepper-circle"><i class="bi bi-mortarboard"></i></div>
                <span class="stepper-title">Pendidikan</span>
            </div>
            <div class="stepper-item" data-step="4" onclick="goToStep(4)">
                <div class="stepper-circle"><i class="bi bi-briefcase"></i></div>
                <span class="stepper-title">Pekerjaan</span>
            </div>
            <div class="stepper-item" data-step="5" onclick="goToStep(5)">
                <div class="stepper-circle"><i class="bi bi-diagram-3"></i></div>
                <span class="stepper-title">Organisasi</span>
            </div>
            <div class="stepper-item" data-step="6" onclick="goToStep(6)">
                <div class="stepper-circle"><i class="bi bi-award"></i></div>
                <span class="stepper-title">Keahlian</span>
            </div>
            <div class="stepper-item" data-step="7" onclick="goToStep(7)">
                <div class="stepper-circle"><i class="bi bi-heart"></i></div>
                <span class="stepper-title">Minat</span>
            </div>
            <div class="stepper-item" data-step="8" onclick="goToStep(8)">
                <div class="stepper-circle"><i class="bi bi-check2-circle"></i></div>
                <span class="stepper-title">Konfirmasi</span>
            </div>
        </div>

        <!-- Form Element -->
        <form id="formPendataanPemuda" action="<?= base_url('pendataan/simpan') ?>" method="POST" class="needs-validation" novalidate>
            <?= csrf_field() ?>
            <input type="hidden" id="existing_pemuda_id" name="existing_pemuda_id" value="<?= old('existing_pemuda_id') ?>">
            <input type="hidden" id="mta_warga_uuid" name="mta_warga_uuid" value="<?= old('mta_warga_uuid') ?>">

            <!-- ================================================================= -->
            <!-- SECTION 1: DATA PRIBADI & PENGECEKAN DATA -->
            <!-- ================================================================= -->
            <div class="card card-custom mb-4 form-step-section active" id="step-1">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex align-items-center mb-4">
                        <div class="section-icon-badge">
                            <i class="bi bi-person-vcard"></i>
                        </div>
                        <div>
                            <h4 class="card-title fw-bold mb-0">1. Data Pribadi &amp; Pengecekan Data</h4>
                            <p class="text-muted small mb-0">Pilih cabang dan ketikkan nama Anda untuk memilih data dari Database Warga MTA</p>
                        </div>
                    </div>

                    <!-- Banner Info Warga MTA Terpilih -->
                    <div class="alert alert-success card-custom border-success mb-4" id="warga-mta-selected-banner" style="display: none;" role="alert">
                        <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-patch-check-fill text-success fs-4 flex-shrink-0 mt-1"></i>
                                <div>
                                    <strong class="text-success" id="warga-selected-title">Data Warga MTA Berhasil Dimuat!</strong>
                                    <div class="small text-dark mb-1" id="warga-selected-desc">
                                        Data warga atas nama <strong id="warga-selected-name">-</strong> (Nomor: <strong id="warga-selected-nomor">-</strong>) berhasil dimuat.
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle py-1 px-2 small">
                                            <i class="bi bi-shield-check me-1"></i> Terhubung Database MTA Pusat
                                        </span>
                                        <small class="text-muted">Silakan periksa kembali dan lengkapi data yang belum terisi pada formulir ini.</small>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="resetWargaMtaSelection()" title="Batal memilih data warga ini dan ketik manual">
                                <i class="bi bi-x-lg me-1"></i> Batal / Ketik Manual
                            </button>
                        </div>
                    </div>

                    <!-- Mode Indicator Banner (Updated Dynamically) -->
                    <div id="form-mode-banner" class="alert alert-success card-custom border-success mb-4" style="display: none;" role="alert">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-patch-check-fill fs-5 text-success"></i>
                                <div>
                                    <strong class="text-success">Mode Melengkapi &amp; Memperbarui Data Terdaftar</strong>
                                    <div class="small text-dark" id="form-mode-desc">Data Anda ditemukan di sistem. Anda dapat memperbarui data pada formulir ini.</div>
                                </div>
                            </div>
                            <span class="badge bg-success rounded-pill px-3 py-2" id="form-mode-reg">No. Reg: -</span>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Wilayah & Cabang Domisili Organisasi -->
                        <div class="col-md-6">
                            <label for="cabang_id" class="form-label">Cabang Pemuda MTA (Wilayah)<span class="required-star">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-diagram-2"></i></span>
                                <select class="form-select" id="cabang_id" name="cabang_id" required>
                                    <option value="" selected disabled>-- Pilih Cabang Pemuda MTA Terdaftar --</option>
                                    <?php if (!empty($wilayahList)): ?>
                                        <?php foreach ($wilayahList as $w): ?>
                                            <optgroup label="<?= esc($w['name']) ?> (<?= esc($w['code']) ?>)">
                                                <?php if (!empty($w['cabang'])): ?>
                                                    <?php foreach ($w['cabang'] as $c): ?>
                                                        <option value="<?= $c['id'] ?>" 
                                                                data-name="<?= esc($c['name']) ?>" 
                                                                data-code="<?= esc($c['code'] ?? '') ?>" 
                                                                data-mta-uuid="<?= esc($c['mta_uuid'] ?? '') ?>" 
                                                                <?= old('cabang_id') == $c['id'] ? 'selected' : '' ?>>
                                                            <?= !empty($c['code']) ? '[' . esc($c['code']) . '] ' : '' ?><?= esc($c['name']) ?> (<?= esc($w['name']) ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </optgroup>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="invalid-feedback">Pilih cabang Pemuda MTA yang sesuai.</div>
                            <small class="form-text text-muted">Pilih cabang terlebih dahulu agar pencarian nama warga dapat otomatis disesuaikan.</small>
                        </div>

                        <!-- Nama Lengkap dengan Autocomplete Warga MTA -->
                        <div class="col-md-6 position-relative">
                            <label for="name" class="form-label">Nama Lengkap<span class="required-star">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Ketik nama untuk mencari data warga MTA..."
                                    value="<?= old('name') ?>" required minlength="3" maxlength="150" autocomplete="off">
                                <span class="input-group-text bg-white" id="name-search-spinner" style="display: none;">
                                    <span class="spinner-border spinner-border-sm text-success" role="status"></span>
                                </span>
                            </div>
                            <div class="invalid-feedback">Nama lengkap wajib diisi (minimal 3 karakter).</div>
                            <small class="form-text text-muted" id="warga-search-hint">
                                <i class="bi bi-lightbulb text-warning me-1"></i>Pilih cabang di samping, lalu ketik nama Anda untuk memilih dari data Warga MTA.
                            </small>

                            <!-- AUTOCOMPLETE SUGGESTIONS DROPDOWN -->
                            <div id="warga-suggestions-dropdown" class="card shadow-lg border-success border-opacity-50 position-absolute w-100 mt-1" 
                                 style="display: none; z-index: 1060; max-height: 360px; overflow-y: auto; left: 0; top: 100%;">
                                <div id="warga-suggestions-list"></div>
                            </div>
                        </div>

                        <!-- Jenis Kelamin -->
                        <div class="col-md-6">
                            <label class="form-label d-block">Jenis Kelamin<span class="required-star">*</span></label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="gender" id="gender_l" value="L" <?= old('gender') === 'L' ? 'checked' : '' ?> required>
                                    <label class="gender-card-select d-block" for="gender_l">
                                        <i class="bi bi-gender-male text-pmd-red fs-4 d-block mb-1"></i>
                                        <span class="fw-semibold">Laki-laki</span>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="gender" id="gender_p" value="P" <?= old('gender') === 'P' ? 'checked' : '' ?> required>
                                    <label class="gender-card-select d-block" for="gender_p">
                                        <i class="bi bi-gender-female text-danger fs-4 d-block mb-1"></i>
                                        <span class="fw-semibold">Perempuan</span>
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback d-block" id="gender-error" style="display: none !important;">Pilih jenis kelamin.</div>
                        </div>

                        <!-- Tanggal Lahir -->
                        <div class="col-md-6">
                            <label for="birth_date" class="form-label">Tanggal Lahir<span class="required-star">*</span></label>
                            <input type="date" class="form-control" id="birth_date" name="birth_date"
                                value="<?= old('birth_date') ?>" required max="<?= date('Y-m-d') ?>">
                            <div class="invalid-feedback">Tanggal lahir wajib diisi.</div>
                        </div>

                        <!-- Kotak Aksi Pengecekan Data Pemuda -->
                        <div class="col-12">
                            <div class="p-3 bg-light rounded-3 border d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 my-1">
                                <div>
                                    <div class="fw-bold text-slate-900 small mb-1">
                                        <i class="bi bi-person-bounding-box me-1 text-danger"></i> Fitur Pengecekan Data Pemuda
                                    </div>
                                    <div class="text-muted small">
                                        Periksa apakah data nama, jenis kelamin, dan tanggal lahir Anda sudah terdaftar di cabang yang dipilih untuk <strong>melengkapi data</strong> atau melanjutkan pendataan baru.
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-danger fw-semibold px-4 py-2 text-nowrap flex-shrink-0" id="btnCekData" onclick="handleManualCheckData()">
                                    <i class="bi bi-search me-1"></i> Cek Data Pemuda
                                </button>
                            </div>
                        </div>

                        <!-- Hasil Pengecekan Data Interaktif -->
                        <div class="col-12" id="check-data-result-wrapper" style="display: none;">
                            <div id="check-data-result-content"></div>
                        </div>

                        <!-- Tempat Lahir -->
                        <div class="col-md-6">
                            <label for="birth_place" class="form-label">Tempat Lahir<span class="required-star">*</span></label>
                            <input type="text" class="form-control" id="birth_place" name="birth_place"
                                placeholder="Contoh: Sragen"
                                value="<?= old('birth_place') ?>" required maxlength="100">
                            <div class="invalid-feedback">Tempat lahir wajib diisi.</div>
                        </div>

                        <!-- Status Pernikahan -->
                        <div class="col-md-6">
                            <label for="marital_status" class="form-label">Status Pernikahan<span class="required-star">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-heart"></i></span>
                                <select class="form-select" id="marital_status" name="marital_status" required onchange="this.classList.remove('is-invalid')">
                                    <option value="" selected disabled>-- Pilih Status Pernikahan --</option>
                                    <option value="belum_menikah" <?= old('marital_status') === 'belum_menikah' ? 'selected' : '' ?>>Belum Menikah</option>
                                    <option value="sudah_menikah" <?= old('marital_status') === 'sudah_menikah' ? 'selected' : '' ?>>Sudah Menikah</option>
                                    <option value="janda" <?= old('marital_status') === 'janda' ? 'selected' : '' ?>>Janda</option>
                                    <option value="duda" <?= old('marital_status') === 'duda' ? 'selected' : '' ?>>Duda</option>
                                </select>
                            </div>
                            <div class="invalid-feedback">Pilih status pernikahan.</div>
                        </div>

                        <!-- Golongan Darah -->
                        <div class="col-md-6">
                            <label for="blood_type" class="form-label">Golongan Darah</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-droplet-half"></i></span>
                                <select class="form-select" id="blood_type" name="blood_type">
                                    <option value="" <?= old('blood_type') === '' || old('blood_type') === null ? 'selected' : '' ?>>-- Pilih Golongan Darah (Opsional) --</option>
                                    <option value="A" <?= old('blood_type') === 'A' ? 'selected' : '' ?>>A</option>
                                    <option value="B" <?= old('blood_type') === 'B' ? 'selected' : '' ?>>B</option>
                                    <option value="AB" <?= old('blood_type') === 'AB' ? 'selected' : '' ?>>AB</option>
                                    <option value="O" <?= old('blood_type') === 'O' ? 'selected' : '' ?>>O</option>
                                    <option value="tidak_tahu" <?= old('blood_type') === 'tidak_tahu' ? 'selected' : '' ?>>Tidak Tahu / Belum Tahu</option>
                                </select>
                            </div>
                        </div>

                        <!-- Nomor WhatsApp / HP -->
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Nomor WhatsApp / HP<span class="required-star">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-whatsapp"></i></span>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                    placeholder="Contoh: 081234567890"
                                    value="<?= old('phone') ?>" required maxlength="20">
                            </div>
                            <div class="invalid-feedback">Nomor WhatsApp wajib diisi untuk koordinasi.</div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label for="email" class="form-label">Alamat Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="Contoh: nama@domain.com"
                                    value="<?= old('email') ?>" maxlength="100">
                            </div>
                            <div class="invalid-feedback">Masukkan format email yang valid.</div>
                        </div>
                    </div>

                    <!-- Step 1 Actions -->
                    <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-primary-pmd" id="btnNextStep1" onclick="validateAndNext(1)">
                            Selanjutnya: Alamat <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ================================================================= -->
            <!-- SECTION 2: ALAMAT -->
            <!-- ================================================================= -->
            <div class="card card-custom mb-4 form-step-section" id="step-2">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex align-items-center mb-4">
                        <div class="section-icon-badge">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div>
                            <h4 class="card-title fw-bold mb-0">2. Alamat Domisili</h4>
                            <p class="text-muted small mb-0">Informasi alamat tempat tinggal saat ini</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Provinsi -->
                        <div class="col-md-6">
                            <label for="province_id" class="form-label">Provinsi<span class="required-star">*</span></label>
                            <select class="form-select" id="province_id" name="province_id" required>
                                <option value="33" selected>Jawa Tengah</option>
                            </select>
                            <div class="invalid-feedback">Pilih provinsi.</div>
                        </div>

                        <!-- Kabupaten / Kota -->
                        <div class="col-md-6">
                            <label for="regency_id" class="form-label">Kabupaten / Kota<span class="required-star">*</span></label>
                            <select class="form-select" id="regency_id" name="regency_id" required>
                                <option value="3314" selected>Kabupaten Sragen</option>
                            </select>
                            <div class="invalid-feedback">Pilih kabupaten/kota.</div>
                        </div>

                        <!-- Kecamatan -->
                        <div class="col-md-6">
                            <label for="district_id" class="form-label">Kecamatan<span class="required-star">*</span></label>
                            <select class="form-select" id="district_id" name="district_id" required onchange="handleDistrictChange(this.value)">
                                <option value="" selected disabled>-- Pilih Kecamatan --</option>
                                <option value="1">Sragen</option>
                                <option value="2">Karangmalang</option>
                                <option value="3">Sidoharjo</option>
                                <option value="4">Gemolong</option>
                                <option value="5">Kalijambe</option>
                                <option value="6">Plupuh</option>
                                <option value="7">Masaran</option>
                                <option value="8">Kedawung</option>
                                <option value="9">Sambirejo</option>
                                <option value="10">Gondang</option>
                                <option value="11">Sambungmacan</option>
                                <option value="12">Ngrampal</option>
                                <option value="13">Tanon</option>
                                <option value="14">Sumberlawang</option>
                                <option value="15">Mondokan</option>
                                <option value="16">Sukodono</option>
                                <option value="17">Gesi</option>
                                <option value="18">Tangen</option>
                                <option value="19">Jenar</option>
                                <option value="20">Miri</option>
                            </select>
                            <div class="invalid-feedback">Pilih kecamatan tempat tinggal Anda.</div>
                        </div>

                        <!-- Desa / Kelurahan -->
                        <div class="col-md-6">
                            <label for="village_id" class="form-label">Desa / Kelurahan<span class="required-star">*</span></label>
                            <select class="form-select" id="village_id" name="village_id" required>
                                <option value="" selected disabled>-- Pilih Kecamatan Terlebih Dahulu --</option>
                            </select>
                            <div class="invalid-feedback">Pilih desa/kelurahan.</div>
                        </div>

                        <!-- Dusun / Dukuh -->
                        <div class="col-md-6">
                            <label for="dusun" class="form-label">Dukuh / Dusun / Lingkungan</label>
                            <input type="text" class="form-control" id="dusun" name="dusun"
                                placeholder="Contoh: Dukuh Kebonromo"
                                value="<?= old('dusun') ?>" maxlength="100">
                        </div>

                        <!-- RT & RW -->
                        <div class="col-md-6">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label for="rt" class="form-label">RT</label>
                                    <input type="text" class="form-control" id="rt" name="rt"
                                        placeholder="01" maxlength="5" value="<?= old('rt') ?>">
                                </div>
                                <div class="col-6">
                                    <label for="rw" class="form-label">RW</label>
                                    <input type="text" class="form-control" id="rw" name="rw"
                                        placeholder="03" maxlength="5" value="<?= old('rw') ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Alamat Detail -->
                        <div class="col-12">
                            <label for="address_detail" class="form-label">Alamat Lengkap (Jalan / Nomor Rumah / Patokan)<span class="required-star">*</span></label>
                            <textarea class="form-control" id="address_detail" name="address_detail" rows="2"
                                placeholder="Contoh: Jl. Raya Sukowati No. 45, RT 02/RW 01, Dekat Masjid Al-Falah" required><?= old('address_detail') ?></textarea>
                            <div class="invalid-feedback">Alamat lengkap wajib diisi.</div>
                        </div>
                    </div>

                    <!-- Step 2 Actions -->
                    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-secondary-pmd" onclick="goToStep(1)">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </button>
                        <button type="button" class="btn btn-primary-pmd" onclick="validateAndNext(2)">
                            Selanjutnya: Pendidikan <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ================================================================= -->
            <!-- SECTION 3: PENDIDIKAN -->
            <!-- ================================================================= -->
            <div class="card card-custom mb-4 form-step-section" id="step-3">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex align-items-center mb-4">
                        <div class="section-icon-badge">
                            <i class="bi bi-mortarboard-fill"></i>
                        </div>
                        <div>
                            <h4 class="card-title fw-bold mb-0">3. Riwayat Pendidikan</h4>
                            <p class="text-muted small mb-0">Informasi latar belakang jenjang pendidikan terakhir</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Jenjang Pendidikan -->
                        <div class="col-md-6">
                            <label for="education_level_id" class="form-label">Jenjang Pendidikan Terakhir<span class="required-star">*</span></label>
                            <select class="form-select" id="education_level_id" name="education_level_id" required>
                                <option value="" selected disabled>-- Pilih Jenjang Pendidikan --</option>
                                <?php if (!empty($educationLevels)): ?>
                                    <?php foreach ($educationLevels as $edu): ?>
                                        <option value="<?= $edu['id'] ?>" <?= old('education_level_id') == $edu['id'] ? 'selected' : '' ?>>
                                            <?= esc($edu['name']) ?> (<?= esc($edu['description'] ?? '') ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="1">SD / Sederajat</option>
                                    <option value="2">SMP / MTs / Sederajat</option>
                                    <option value="3">SMA / SMK / MA</option>
                                    <option value="4">Diploma (D1 / D2 / D3)</option>
                                    <option value="5">Sarjana (S1 / D4)</option>
                                    <option value="6">Magister (S2)</option>
                                    <option value="7">Doktor (S3)</option>
                                <?php endif; ?>
                            </select>
                            <div class="invalid-feedback">Pilih jenjang pendidikan terakhir.</div>
                        </div>

                        <!-- Status Pendidikan -->
                        <div class="col-md-6">
                            <label for="education_status" class="form-label">Status Pendidikan<span class="required-star">*</span></label>
                            <select class="form-select" id="education_status" name="education_status" required>
                                <option value="lulus" <?= old('education_status') === 'lulus' ? 'selected' : '' ?>>Sudah Lulus / Tamat</option>
                                <option value="sedang_sekolah" <?= old('education_status') === 'sedang_sekolah' ? 'selected' : '' ?>>Sedang Menempuh Pendidikan</option>
                                <option value="putus_sekolah" <?= old('education_status') === 'putus_sekolah' ? 'selected' : '' ?>>Tidak Tamat / Putus Sekolah</option>
                            </select>
                            <div class="invalid-feedback">Pilih status pendidikan.</div>
                        </div>

                        <!-- Nama Sekolah / Universitas -->
                        <div class="col-md-6">
                            <label for="school_name" class="form-label">Nama Sekolah / Kampus / Lembaga<span class="required-star">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-building"></i></span>
                                <input type="text" class="form-control" id="school_name" name="school_name"
                                    placeholder="Contoh: SMAN 1 Sragen / Universitas Sebelas Maret"
                                    value="<?= old('school_name') ?>" required minlength="3" maxlength="150">
                            </div>
                            <div class="invalid-feedback">Nama sekolah/universitas wajib diisi.</div>
                        </div>

                        <!-- Jurusan / Program Studi -->
                        <div class="col-md-6">
                            <label for="major" class="form-label">Jurusan / Program Studi / Peminatan</label>
                            <input type="text" class="form-control" id="major" name="major"
                                placeholder="Contoh: Rekayasa Perangkat Lunak / Teknik Informatika / IPA"
                                value="<?= old('major') ?>" maxlength="150">
                        </div>

                        <!-- Tahun Kelulusan -->
                        <div class="col-md-6">
                            <label for="graduation_year" class="form-label">Tahun Kelulusan / Angkatan</label>
                            <input type="number" class="form-control" id="graduation_year" name="graduation_year"
                                placeholder="Contoh: <?= date('Y') ?>"
                                min="1980" max="<?= date('Y') + 5 ?>" value="<?= old('graduation_year') ?>">
                        </div>
                    </div>

                    <!-- Step 3 Actions -->
                    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-secondary-pmd" onclick="goToStep(2)">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </button>
                        <button type="button" class="btn btn-primary-pmd" onclick="validateAndNext(3)">
                            Selanjutnya: Pekerjaan <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ================================================================= -->
            <!-- SECTION 4: PEKERJAAN -->
            <!-- ================================================================= -->
            <div class="card card-custom mb-4 form-step-section" id="step-4">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex align-items-center mb-4">
                        <div class="section-icon-badge">
                            <i class="bi bi-briefcase-fill"></i>
                        </div>
                        <div>
                            <h4 class="card-title fw-bold mb-0">4. Status Pekerjaan & Karir</h4>
                            <p class="text-muted small mb-0">Informasi aktivitas profesi dan bidang kerja saat ini</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Status Pekerjaan -->
                        <div class="col-md-6">
                            <label for="job_status_id" class="form-label">Status Pekerjaan Saat Ini<span class="required-star">*</span></label>
                            <select class="form-select" id="job_status_id" name="job_status_id" required onchange="handleJobStatusChange(this.value)">
                                <option value="" selected disabled>-- Pilih Status Pekerjaan --</option>
                                <?php if (!empty($jobStatuses)): ?>
                                    <?php foreach ($jobStatuses as $job): ?>
                                        <option value="<?= $job['id'] ?>" <?= old('job_status_id') == $job['id'] ? 'selected' : '' ?>>
                                            <?= esc($job['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="1">Belum / Tidak Bekerja</option>
                                    <option value="2">Pelajar / Mahasiswa</option>
                                    <option value="3">Karyawan Swasta</option>
                                    <option value="4">Pegawai Negeri / ASN / PPPK</option>
                                    <option value="5">Wirausaha / Pemilik Usaha</option>
                                    <option value="6">Freelancer / Pekerja Lepas</option>
                                    <option value="7">Petani / Peternak</option>
                                    <option value="8">Lainnya</option>
                                <?php endif; ?>
                            </select>
                            <div class="invalid-feedback">Pilih status pekerjaan Anda.</div>
                        </div>

                        <!-- Profesi / Jabatan -->
                        <div class="col-md-6" id="wrapper-job-title">
                            <label for="job_title" class="form-label">Profesi / Jabatan / Posisi</label>
                            <input type="text" class="form-control" id="job_title" name="job_title"
                                placeholder="Contoh: Staff Keuangan / Web Developer / Barista"
                                value="<?= old('job_title') ?>" maxlength="150">
                        </div>

                        <!-- Nama Perusahaan / Instansi / Usaha -->
                        <div class="col-md-6" id="wrapper-company-name">
                            <label for="company_name" class="form-label">Nama Perusahaan / Tempat Usaha</label>
                            <input type="text" class="form-control" id="company_name" name="company_name"
                                placeholder="Contoh: PT Sumber Pangan / Usaha Sendiri"
                                value="<?= old('company_name') ?>" maxlength="150">
                        </div>

                        <!-- Bidang Usaha / Sektor Industri -->
                        <div class="col-md-6" id="wrapper-business-field">
                            <label for="business_field" class="form-label">Bidang Industri / Sektor Usaha</label>
                            <input type="text" class="form-control" id="business_field" name="business_field"
                                placeholder="Contoh: Teknologi Informasi / Kuliner / Pertanian"
                                value="<?= old('business_field') ?>" maxlength="150">
                        </div>
                    </div>

                    <!-- Step 4 Actions -->
                    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-secondary-pmd" onclick="goToStep(3)">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </button>
                        <button type="button" class="btn btn-primary-pmd" onclick="validateAndNext(4)">
                            Selanjutnya: Organisasi <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ================================================================= -->
            <!-- SECTION 5: ORGANISASI -->
            <!-- ================================================================= -->
            <div class="card card-custom mb-4 form-step-section" id="step-5">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex align-items-center mb-4">
                        <div class="section-icon-badge">
                            <i class="bi bi-diagram-3-fill"></i>
                        </div>
                        <div>
                            <h4 class="card-title fw-bold mb-0">5. Keikutsertaan Organisasi & Penugasan</h4>
                            <p class="text-muted small mb-0">Pilih unit tugas / bidang organisasi yang Anda ikuti (opsional, bisa pilih lebih dari satu)</p>
                        </div>
                    </div>

                    <p class="text-secondary small mb-3">Centang organisasi yang Anda ikuti dan lengkapi jabatan/peran Anda:</p>

                    <?php
                    $availableOrganizations = [
                        [
                            'key'         => 'satgas',
                            'name'        => 'Satgas',
                            'title'       => 'Satgas',
                            'badge'       => 'Satuan Tugas',
                            'description' => 'Satuan Tugas Pengamanan, Penertiban, dan Pengawalan Kegiatan',
                            'icon'        => 'shield-shaded',
                            'color'       => 'text-danger',
                        ],
                        [
                            'key'         => 'bankom',
                            'name'        => 'Bankom',
                            'title'       => 'Bankom',
                            'badge'       => 'Bantuan Komunikasi',
                            'description' => 'Unit Bantuan Komunikasi, Radio Pancar Ulang, dan Informasi Lapangan',
                            'icon'        => 'broadcast-pin',
                            'color'       => 'text-primary',
                        ],
                        [
                            'key'         => 'parkir',
                            'name'        => 'Parkir',
                            'title'       => 'Parkir',
                            'badge'       => 'Tim Parkir',
                            'description' => 'Tim Pengaturan Parkir, Kelancaran Arus, dan Kerapian Kendaraan',
                            'icon'        => 'p-circle-fill',
                            'color'       => 'text-warning',
                        ],
                        [
                            'key'         => 'pemuda',
                            'name'        => 'Pemuda',
                            'title'       => 'Pemuda',
                            'badge'       => 'Kepengurusan Pemuda',
                            'description' => 'Keaktifan Struktural / Anggota dalam Kegiatan Kepemudaan Cabang/Wilayah',
                            'icon'        => 'people-fill',
                            'color'       => 'text-success',
                        ],
                        [
                            'key'         => 'tim_ikhrom',
                            'name'        => 'Tim Ikhrom',
                            'title'       => 'Tim Ikhrom',
                            'badge'       => 'Perawatan Jenazah',
                            'description' => 'Tim Khusus Pelayanan, Perawatan, dan Pengurusan Jenazah',
                            'icon'        => 'heart-pulse-fill',
                            'color'       => 'text-info',
                        ],
                    ];
                    ?>

                    <div class="row g-3" id="organizationContainer">
                        <?php foreach ($availableOrganizations as $org): ?>
                            <div class="col-12">
                                <div class="org-card" id="org_card_box_<?= $org['key'] ?>">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div class="form-check w-100">
                                            <input class="form-check-input org-toggle-check" type="checkbox"
                                                name="organizations[<?= $org['key'] ?>][selected]"
                                                value="<?= $org['name'] ?>"
                                                id="org_<?= $org['key'] ?>"
                                                data-key="<?= $org['key'] ?>"
                                                data-title="<?= $org['name'] ?>"
                                                onchange="toggleOrgDetail('<?= $org['key'] ?>')">
                                            <label class="form-check-label fw-bold text-slate-900 fs-6 w-100 cursor-pointer" for="org_<?= $org['key'] ?>">
                                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                    <span>
                                                        <i class="bi bi-<?= $org['icon'] ?> <?= $org['color'] ?> me-2 fs-5 align-middle"></i>
                                                        <?= $org['title'] ?>
                                                    </span>
                                                    <span class="badge bg-light text-secondary border rounded-pill fw-medium small">
                                                        <?= $org['badge'] ?>
                                                    </span>
                                                </div>
                                                <div class="text-muted fw-normal small mt-1 ms-4"><?= $org['description'] ?></div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mt-3 pt-3 border-top org-detail-wrapper" id="org_detail_<?= $org['key'] ?>" style="display: none;">
                                        <input type="hidden" name="organizations[<?= $org['key'] ?>][name]" value="<?= $org['name'] ?>" disabled>
                                        <div class="row g-2">
                                            <div class="col-md-5">
                                                <label class="form-label small text-muted mb-1">Jabatan / Posisi / Peran:</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    name="organizations[<?= $org['key'] ?>][position]"
                                                    placeholder="Contoh: Anggota / Koordinator / Sie Lapangan"
                                                    value="Anggota" disabled>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small text-muted mb-1">Tahun Bergabung:</label>
                                                <input type="number" class="form-control form-control-sm"
                                                    name="organizations[<?= $org['key'] ?>][join_year]"
                                                    placeholder="Contoh: <?= date('Y') ?>"
                                                    min="1990" max="<?= date('Y') ?>"
                                                    value="<?= date('Y') ?>" disabled>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small text-muted mb-1">Keterangan Tambahan:</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    name="organizations[<?= $org['key'] ?>][description]"
                                                    placeholder="Opsional" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Organisasi Lainnya -->
                    <div class="mt-4 p-3 bg-light rounded-3">
                        <label class="form-label fw-semibold text-slate-800 small mb-1">
                            <i class="bi bi-plus-circle me-1"></i> Organisasi / Komunitas Lainnya (Jika ada):
                        </label>
                        <input type="text" class="form-control form-control-sm" name="other_organization"
                            placeholder="Contoh: Karang Taruna, OSIS, Relawan BPBD (pisahkan dengan koma jika lebih dari satu)">
                    </div>

                    <!-- Step 5 Actions -->
                    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-secondary-pmd" onclick="goToStep(4)">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </button>
                        <button type="button" class="btn btn-primary-pmd" onclick="validateAndNext(5)">
                            Selanjutnya: Keahlian <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ================================================================= -->
            <!-- SECTION 6: KEAHLIAN (SKILLS) -->
            <!-- ================================================================= -->
            <div class="card card-custom mb-4 form-step-section" id="step-6">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex align-items-center mb-4">
                        <div class="section-icon-badge">
                            <i class="bi bi-award-fill"></i>
                        </div>
                        <div>
                            <h4 class="card-title fw-bold mb-0">6. Keahlian & Keterampilan</h4>
                            <p class="text-muted small mb-0">Pilih keahlian yang Anda miliki beserta tingkatan kemampuannya</p>
                        </div>
                    </div>

                    <p class="text-secondary small mb-3">Centang keahlian yang relevan dan tentukan tingkat keahlian Anda (Pemula, Menengah, Mahir):</p>

                    <!-- Skills Matrix -->
                    <div class="row g-3" id="skillsContainer">
                        <?php
                        $availableSkills = [
                            ['id' => 1, 'name' => 'Desain Grafis & Multimedia', 'icon' => 'palette'],
                            ['id' => 2, 'name' => 'Pemrograman & IT (Web/Mobile)', 'icon' => 'code-slash'],
                            ['id' => 3, 'name' => 'Digital Marketing & Social Media', 'icon' => 'megaphone'],
                            ['id' => 4, 'name' => 'Public Speaking & Komunikasi', 'icon' => 'mic'],
                            ['id' => 5, 'name' => 'Fotografi & Videografi', 'icon' => 'camera-video'],
                            ['id' => 6, 'name' => 'Pertanian Modern & Hidroponik', 'icon' => 'flower1'],
                            ['id' => 7, 'name' => 'Tata Boga & Kuliner', 'icon' => 'cup-hot'],
                            ['id' => 8, 'name' => 'Menjahit & Tata Busana', 'icon' => 'scissors'],
                            ['id' => 9, 'name' => 'Teknik Otomotif & Mesin', 'icon' => 'tools'],
                            ['id' => 10, 'name' => 'Administrasi & Pembukuan', 'icon' => 'calculator'],
                            ['id' => 11, 'name' => 'Bahasa Asing (Inggris/Lainnya)', 'icon' => 'translate'],
                            ['id' => 12, 'name' => 'Kepemimpinan & Manajemen Tim', 'icon' => 'people']
                        ];
                        ?>

                        <?php foreach ($availableSkills as $skill): ?>
                            <div class="col-md-6">
                                <div class="skill-card h-100">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="form-check">
                                            <input class="form-check-input skill-toggle-check" type="checkbox"
                                                name="skills[<?= $skill['id'] ?>][selected]"
                                                value="<?= $skill['id'] ?>"
                                                id="skill_<?= $skill['id'] ?>"
                                                onchange="toggleSkillLevel('<?= $skill['id'] ?>')">
                                            <label class="form-check-label fw-semibold text-slate-800" for="skill_<?= $skill['id'] ?>">
                                                <i class="bi bi-<?= $skill['icon'] ?> text-primary me-1"></i> <?= $skill['name'] ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mt-2 pt-2 border-top skill-level-wrapper" id="skill_level_box_<?= $skill['id'] ?>" style="display: none;">
                                        <label class="form-label small text-muted mb-1">Tingkat Kemampuan:</label>
                                        <select class="form-select form-select-sm" name="skills[<?= $skill['id'] ?>][level]" disabled>
                                            <option value="pemula">Pemula (Beginner)</option>
                                            <option value="menengah" selected>Menengah (Intermediate)</option>
                                            <option value="mahir">Mahir (Advanced)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Custom Skill Input -->
                    <div class="mt-4 p-3 bg-light rounded-3">
                        <label class="form-label fw-semibold"><i class="bi bi-plus-circle me-1"></i> Keahlian Lainnya (Jika belum terdaftar):</label>
                        <input type="text" class="form-control" name="custom_skills" placeholder="Contoh: Barista Kopi, Servis HP, Operator Drone (pisahkan dengan koma)">
                    </div>

                    <!-- Step 6 Actions -->
                    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-secondary-pmd" onclick="goToStep(5)">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </button>
                        <button type="button" class="btn btn-primary-pmd" onclick="validateAndNext(6)">
                            Selanjutnya: Minat <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ================================================================= -->
            <!-- SECTION 7: MINAT (INTERESTS) -->
            <!-- ================================================================= -->
            <div class="card card-custom mb-4 form-step-section" id="step-7">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex align-items-center mb-4">
                        <div class="section-icon-badge">
                            <i class="bi bi-heart-fill"></i>
                        </div>
                        <div>
                            <h4 class="card-title fw-bold mb-0">7. Minat & Pengembangan Diri</h4>
                            <p class="text-muted small mb-0">Pilih bidang minat atau pelatihan yang ingin Anda kembangkan</p>
                        </div>
                    </div>

                    <p class="text-secondary small mb-3">Klik untuk memilih satu atau beberapa minat yang paling Anda sukai:</p>

                    <!-- Interest Tag Pills -->
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <?php
                        $availableInterests = [
                            ['id' => 1, 'name' => 'Olahraga & Kebugaran', 'icon' => 'trophy'],
                            ['id' => 2, 'name' => 'Seni Musik & Tari', 'icon' => 'music-note-beamed'],
                            ['id' => 3, 'name' => 'Seni Rupa & Kriya', 'icon' => 'brush'],
                            ['id' => 4, 'name' => 'Teknologi & Robotika', 'icon' => 'cpu'],
                            ['id' => 5, 'name' => 'Kewirausahaan & UMKM', 'icon' => 'shop'],
                            ['id' => 6, 'name' => 'Aksi Relawan & Sosial', 'icon' => 'heart-pulse'],
                            ['id' => 7, 'name' => 'Kelestarian Lingkungan', 'icon' => 'tree'],
                            ['id' => 8, 'name' => 'Literasi & Buku', 'icon' => 'book'],
                            ['id' => 9, 'name' => 'Kajian & Keagamaan', 'icon' => 'moon-stars'],
                            ['id' => 10, 'name' => 'Pariwisata & Budaya Lokal', 'icon' => 'compass'],
                            ['id' => 11, 'name' => 'E-Sport & Gaming', 'icon' => 'controller'],
                            ['id' => 12, 'name' => 'Kepemimpinan & Organisasi', 'icon' => 'diagram-3']
                        ];
                        ?>

                        <?php foreach ($availableInterests as $interest): ?>
                            <div>
                                <input type="checkbox" class="interest-tag-checkbox"
                                    name="interests[]"
                                    value="<?= $interest['id'] ?>"
                                    id="interest_<?= $interest['id'] ?>"
                                    data-name="<?= esc($interest['name']) ?>">
                                <label class="interest-tag-label" for="interest_<?= $interest['id'] ?>">
                                    <i class="bi bi-<?= $interest['icon'] ?>"></i> <?= $interest['name'] ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Custom Interests -->
                    <div class="p-3 bg-light rounded-3">
                        <label class="form-label fw-semibold"><i class="bi bi-plus-circle me-1"></i> Minat Lainnya:</label>
                        <input type="text" class="form-control" name="custom_interests" placeholder="Contoh: Astronomi, Podcast, Panahan (pisahkan dengan koma)">
                    </div>

                    <!-- Step 7 Actions -->
                    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-secondary-pmd" onclick="goToStep(6)">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </button>
                        <button type="button" class="btn btn-primary-pmd" onclick="prepareReview(); goToStep(8)">
                            Selanjutnya: Review & Konfirmasi <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ================================================================= -->
            <!-- SECTION 8: KONFIRMASI & PERSETUJUAN -->
            <!-- ================================================================= -->
            <div class="card card-custom mb-4 form-step-section" id="step-8">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex align-items-center mb-4">
                        <div class="section-icon-badge">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div>
                            <h4 class="card-title fw-bold mb-0">8. Konfirmasi & Ringkasan Data</h4>
                            <p class="text-muted small mb-0">Pastikan seluruh data yang Anda masukkan telah lengkap dan benar</p>
                        </div>
                    </div>

                    <!-- Review Cards Summary -->
                    <div class="p-3 p-md-4 rounded-3 border bg-white mb-4">
                        <div class="row g-4">
                            <!-- Review Pribadi -->
                            <div class="col-md-6">
                                <div class="review-group-title"><i class="bi bi-person me-1"></i> Data Pribadi</div>
                                <div class="mb-2">
                                    <div class="review-item-label">Nama Lengkap</div>
                                    <div class="review-item-value" id="rev_name">-</div>
                                </div>
                                <div class="mb-2">
                                    <div class="review-item-label">Jenis Kelamin & Tempat/Tgl Lahir</div>
                                    <div class="review-item-value" id="rev_ttl">-</div>
                                </div>
                                <div class="mb-2">
                                    <div class="review-item-label">Status Pernikahan & Golongan Darah</div>
                                    <div class="review-item-value" id="rev_marital_blood">-</div>
                                </div>
                                <div class="mb-2">
                                    <div class="review-item-label">Kontak (WhatsApp & Email)</div>
                                    <div class="review-item-value" id="rev_contact">-</div>
                                </div>
                                <div class="mb-2">
                                    <div class="review-item-label">Cabang Pemuda MTA Terpilih</div>
                                    <div class="review-item-value" id="rev_cabang">-</div>
                                </div>
                            </div>

                            <!-- Review Alamat & Pendidikan -->
                            <div class="col-md-6">
                                <div class="review-group-title"><i class="bi bi-geo-alt me-1"></i> Domisili & Pendidikan</div>
                                <div class="mb-2">
                                    <div class="review-item-label">Alamat Lengkap</div>
                                    <div class="review-item-value" id="rev_address">-</div>
                                </div>
                                <div class="mb-2">
                                    <div class="review-item-label">Pendidikan Terakhir</div>
                                    <div class="review-item-value" id="rev_education">-</div>
                                </div>
                                <div class="mb-2">
                                    <div class="review-item-label">Pekerjaan Saat Ini</div>
                                    <div class="review-item-value" id="rev_job">-</div>
                                </div>
                                <div class="mb-2">
                                    <div class="review-item-label">Keikutsertaan Organisasi / Unit Tugas</div>
                                    <div class="review-item-value" id="rev_organizations">-</div>
                                </div>
                                <div class="mb-2">
                                    <div class="review-item-label">Keahlian & Minat</div>
                                    <div class="review-item-value" id="rev_skills_interests">-</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mode Banner on Step 8 -->
                    <div id="review-mode-banner" class="alert alert-success card-custom border-success mb-4" style="display: none;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-patch-check-fill fs-5 text-success"></i>
                            <div>
                                <strong>Mode: Melengkapi &amp; Memperbarui Data Terdaftar</strong> (<span id="review-mode-reg">-</span>)
                                <div class="small text-muted">Data pemuda ini akan diperbarui dan dilengkapi di sistem basis data cabang.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Statement Checkbox -->
                    <div class="card bg-light border-0 p-3 mb-4 rounded-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="agreement_check" required>
                            <label class="form-check-label fw-semibold text-slate-800 small" for="agreement_check">
                                Saya menyatakan dengan sesungguhnya bahwa seluruh data yang saya isikan pada formulir pendataan pemuda ini adalah benar, akurat, dan dapat dipertanggungjawabkan sesuai ketentuan yang berlaku.
                            </label>
                            <div class="invalid-feedback">Anda wajib menyetujui pernyataan keabsahan data sebelum mengirim form.</div>
                        </div>
                    </div>

                    <!-- Step 8 Actions -->
                    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-secondary-pmd" onclick="goToStep(7)">
                            <i class="bi bi-arrow-left me-1"></i> Kembali Ubah Data
                        </button>
                        <button type="submit" class="btn btn-primary-pmd px-4 py-2" id="btnSubmitForm">
                            <i class="bi bi-send-fill me-1"></i> <span id="btnSubmitFormText">Kirim Data Pendataan</span>
                        </button>
                    </div>
                </div>
            </div>

        </form>

    </div>
</div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Tom Select JS for Searchable Selects -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    const PENDATAAN_CONFIG = {
        baseUrl: '<?= rtrim(base_url(), '/') ?>',
        checkDataUrl: '<?= base_url('pendataan/check-data') ?>',
        checkDuplicateUrl: '<?= base_url('pendataan/check-duplicate') ?>',
        searchWargaUrl: '<?= base_url('pendataan/search-warga') ?>',
        wargaDetailUrl: '<?= base_url('pendataan/warga-detail') ?>',
        csrfToken: '<?= csrf_token() ?>',
        csrfHash: '<?= csrf_hash() ?>'
    };
</script>
<script src="<?= base_url('js/pendataan.js') ?>"></script>
<?= $this->endSection() ?>