<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<!-- HEADER -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
    <div>
        <div class="d-flex align-items-center mb-1">
            <a href="<?= base_url('admin/pemuda') ?>" class="btn btn-default btn-xs mr-2">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
            </a>
            <span class="badge badge-danger px-2 py-1 font-weight-bold">
                <i class="fas fa-shield-alt mr-1"></i> Khusus Super Administrator
            </span>
        </div>
        <h4 class="font-weight-bold text-dark mb-1">Import Data Pemuda dari Excel</h4>
        <p class="text-muted text-xs mb-0">Unggah berkas spreadsheet (.xlsx, .xls, atau .csv) untuk memasukkan data pemuda secara massal ke dalam sistem.</p>
    </div>
    <div class="mt-2 mt-md-0">
        <a href="<?= base_url('admin/pemuda/template-import') ?>" class="btn btn-success btn-sm">
            <i class="fas fa-download mr-1"></i> Unduh Format Template Excel (.xlsx)
        </a>
    </div>
</div>

<!-- IMPORT ERROR / WARNING DETAILS (IF ANY) -->
<?php if (session()->getFlashdata('import_errors')): ?>
    <div class="card card-danger card-outline shadow-sm mb-3">
        <div class="card-header bg-danger text-white py-2">
            <h3 class="card-title font-weight-bold text-sm">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                Detail Kesalahan Validasi Baris Excel (<?= count(session()->getFlashdata('import_errors')) ?> Isu)
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool text-white" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body p-3 bg-light">
            <p class="text-xs text-danger mb-2 font-weight-bold">
                Data di bawah ini tidak dapat diproses. Silakan perbaiki baris terkait pada file Excel Anda atau aktifkan opsi "Lewati Baris Error" saat mengunggah ulang.
            </p>
            <div style="max-height: 250px; overflow-y: auto;">
                <ul class="list-group list-group-flush rounded border">
                    <?php foreach (session()->getFlashdata('import_errors') as $err): ?>
                        <li class="list-group-item list-group-item-danger py-1 text-xs d-flex align-items-center">
                            <i class="fas fa-times-circle text-danger mr-2"></i>
                            <div><?= esc($err) ?></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="row">
    <!-- KOLOM KIRI: FORM UPLOAD FILE -->
    <div class="col-12 col-lg-7">
        <div class="card card-primary card-outline shadow-sm mb-3">
            <div class="card-header border-0">
                <h3 class="card-title font-weight-bold text-sm">
                    <i class="fas fa-cloud-upload-alt text-primary mr-1"></i>
                    Unggah File Spreadsheet
                </h3>
            </div>
            <div class="card-body p-3">
                <form action="<?= base_url('admin/pemuda/import') ?>" method="POST" enctype="multipart/form-data" id="formImport">
                    <?= csrf_field() ?>

                    <!-- STEP 1: DOWNLOAD TEMPLATE BANNER -->
                    <div class="callout callout-success py-2 px-3 mb-3 d-flex align-items-center justify-content-between flex-wrap">
                        <div>
                            <h6 class="font-weight-bold text-dark mb-1 text-sm">
                                <i class="fas fa-file-excel text-success mr-1"></i> 1. Gunakan Template Standar Import
                            </h6>
                            <p class="text-muted text-xs mb-0">
                                <strong>Prioritas Utama:</strong> Cukup isi <strong>Nama Lengkap</strong>, <strong>Cabang</strong>, <strong>Jenis Kelamin</strong>, <strong>Status Pernikahan</strong>, dan <strong>Tanggal Lahir</strong>. Data lainnya bersifat opsional dan dapat menyusul.
                            </p>
                        </div>
                        <a href="<?= base_url('admin/pemuda/template-import') ?>" class="btn btn-success btn-xs font-weight-bold mt-2 mt-sm-0 shadow-sm">
                            <i class="fas fa-download mr-1"></i> Unduh Template Excel
                        </a>
                    </div>

                    <!-- STEP 2: FILE INPUT -->
                    <div class="form-group mb-3">
                        <label for="file_excel" class="text-xs font-weight-bold text-dark">
                            2. Pilih Berkas Excel / CSV <span class="text-danger">*</span>
                        </label>
                        <div class="custom-file">
                            <input type="file" 
                                   class="custom-file-input <?= session()->getFlashdata('errors.file_excel') ? 'is-invalid' : '' ?>" 
                                   id="file_excel" 
                                   name="file_excel" 
                                   accept=".xlsx, .xls, .csv" 
                                   required>
                            <label class="custom-file-label text-xs" for="file_excel">Pilih file spreadsheet...</label>
                        </div>
                        <div class="text-muted text-xs mt-1">
                            <i class="fas fa-info-circle mr-1"></i> Format yang didukung: <strong>.xlsx</strong>, <strong>.xls</strong>, <strong>.csv</strong> (Maksimal 10 MB).
                        </div>
                        <?php if (session()->getFlashdata('errors.file_excel')): ?>
                            <div class="invalid-feedback d-block text-xs">
                                <?= session()->getFlashdata('errors.file_excel') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- STEP 3: IMPORT OPTIONS -->
                    <div class="card card-default shadow-none border mb-3 bg-light">
                        <div class="card-body p-3">
                            <h6 class="font-weight-bold text-dark mb-2 text-xs text-uppercase">
                                <i class="fas fa-sliders-h mr-1 text-primary"></i> 3. Pengaturan Opsi Import
                            </h6>

                            <!-- Default Status Verifikasi -->
                            <div class="form-group mb-2">
                                <label class="text-xs text-muted font-weight-bold">Status Verifikasi Default</label>
                                <select name="default_verifikasi" class="form-control form-control-sm">
                                    <option value="verified" selected>Langsung Terverifikasi (Valid)</option>
                                    <option value="pending">Menunggu Verifikasi (Pending)</option>
                                </select>
                                <div class="text-muted text-xs mt-1">Jika kolom status verifikasi pada file Excel dikosongkan, status ini yang akan digunakan.</div>
                            </div>

                            <!-- Skip Errors Checkbox -->
                            <div class="custom-control custom-checkbox mt-2">
                                <input class="custom-control-input" type="checkbox" id="skip_errors" name="skip_errors" value="1" checked>
                                <label class="custom-control-label font-weight-bold text-dark text-xs" for="skip_errors">
                                    Lewati baris data yang error (Import baris yang valid saja)
                                </label>
                                <div class="text-muted text-xs">
                                    Jika dicentang, data baris yang valid akan tetap disimpan ke database, dan baris yang bermasalah akan dilaporkan pada ringkasan hasil.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SUBMIT BUTTONS -->
                    <div class="d-flex justify-content-between align-items-center pt-2">
                        <a href="<?= base_url('admin/pemuda') ?>" class="btn btn-default btn-sm">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm px-3 shadow-sm font-weight-bold" id="btnSubmitImport">
                            <i class="fas fa-cloud-upload-alt mr-1"></i> Mulai Proses Import Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- KOLOM KANAN: PANDUAN & PETUNJUK KOLOM -->
    <div class="col-12 col-lg-5">
        <div class="card card-success card-outline shadow-sm mb-3">
            <div class="card-header border-0">
                <h3 class="card-title font-weight-bold text-sm">
                    <i class="fas fa-check-double text-success mr-1"></i>
                    Prioritas Format Kolom Excel
                </h3>
            </div>
            <div class="card-body p-3">
                <!-- 5 Data Wajib Utama -->
                <div class="mb-3">
                    <h6 class="font-weight-bold text-success text-xs text-uppercase mb-2">
                        <i class="fas fa-star text-warning mr-1"></i> 5 Data Inti Wajib Diisi (*)
                    </h6>
                    <div class="bg-light p-2 rounded border mb-2">
                        <ul class="list-unstyled text-xs text-muted mb-0 pl-1">
                            <li class="mb-1">
                                <strong class="text-dark"><i class="fas fa-user text-primary mr-1"></i> 1. Nama Lengkap</strong> — Nama pemuda (min. 3 karakter).
                            </li>
                            <li class="mb-1">
                                <strong class="text-dark"><i class="fas fa-sitemap text-warning mr-1"></i> 2. Cabang</strong> — Nama Cabang (contoh: <code>Sragen 1</code>, <code>Gemolong 1</code>) atau Kode (contoh: <code>86.1</code>, <code>86.47</code>).
                            </li>
                            <li class="mb-1">
                                <strong class="text-dark"><i class="fas fa-venus-mars text-info mr-1"></i> 3. Jenis Kelamin</strong> — Isi <span class="badge badge-primary">L</span> (Laki-laki) atau <span class="badge badge-danger">P</span> (Perempuan).
                            </li>
                            <li class="mb-1">
                                <strong class="text-dark"><i class="fas fa-heart text-danger mr-1"></i> 4. Status Pernikahan</strong> — <code>belum_menikah</code>, <code>sudah_menikah</code>, <code>janda</code>, <code>duda</code> (default: <code>belum_menikah</code>).
                            </li>
                            <li class="mb-0">
                                <strong class="text-dark"><i class="fas fa-calendar-alt text-success mr-1"></i> 5. Tanggal Lahir</strong> — Format <code>YYYY-MM-DD</code> atau <code>DD/MM/YYYY</code>.
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Kolom Pelengkap (Bisa Menyusul) -->
                <div class="mb-3">
                    <h6 class="font-weight-bold text-secondary text-xs text-uppercase mb-2">
                        <i class="fas fa-info-circle text-info mr-1"></i> Data Pelengkap (Opsional / Bisa Menyusul)
                    </h6>
                    <p class="text-muted text-xs mb-1">
                        Seluruh kolom di bawah ini <strong>boleh dikosongkan</strong> dan dapat dilengkapi kemudian hari melalui menu edit data:
                    </p>
                    <div class="text-xs text-muted">
                        <span class="badge badge-light border mb-1">No. Telepon / WA</span>
                        <span class="badge badge-light border mb-1">Tempat Lahir</span>
                        <span class="badge badge-light border mb-1">Email</span>
                        <span class="badge badge-light border mb-1">Golongan Darah</span>
                        <span class="badge badge-light border mb-1">Kecamatan &amp; Desa</span>
                        <span class="badge badge-light border mb-1">Alamat Lengkap / RT / RW</span>
                        <span class="badge badge-light border mb-1">Jenjang &amp; Nama Sekolah</span>
                        <span class="badge badge-light border mb-1">Status Pekerjaan</span>
                        <span class="badge badge-light border mb-1">Organisasi</span>
                        <span class="badge badge-light border mb-1">Keahlian</span>
                        <span class="badge badge-light border mb-1">Minat</span>
                    </div>
                </div>

                <hr class="my-2">

                <!-- Statistik Master Database -->
                <div>
                    <h6 class="font-weight-bold text-info text-xs text-uppercase mb-2">
                        <i class="fas fa-database mr-1"></i> Master Data Tersedia
                    </h6>
                    <div class="text-xs text-muted">
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span>Total Wilayah:</span>
                            <strong class="text-dark"><?= esc($wilayahCount) ?> Wilayah</strong>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span>Total Cabang:</span>
                            <strong class="text-dark"><?= esc($cabangCount) ?> Cabang</strong>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span>Kabupaten:</span>
                            <strong class="text-dark">Kabupaten Sragen (3314)</strong>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span>Total Kecamatan:</span>
                            <strong class="text-dark">20 Kecamatan</strong>
                        </div>
                        <p class="text-muted mt-2 mb-0" style="font-size: 0.72rem;">
                            * Daftar lengkap kode &amp; nama cabang dapat dilihat pada lembar (sheet) ke-2 di dalam file template Excel yang diunduh.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Custom file input label update
        $('#file_excel').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName || 'Pilih file spreadsheet...');
        });

        $('#formImport').on('submit', function() {
            const $btn = $('#btnSubmitImport');
            $btn.prop('disabled', true);
            $btn.html('<span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span> Sedang Mengimport Data...');
        });
    });
</script>
<?= $this->endSection() ?>
