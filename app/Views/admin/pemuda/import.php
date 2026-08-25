<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<!-- HEADER -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <a href="<?= base_url('admin/pemuda') ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
            </a>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 fw-semibold">
                <i class="bi bi-shield-lock-fill me-1"></i> Khusus Super Administrator
            </span>
        </div>
        <h4 class="fw-bold text-dark mb-1">Import Data Pemuda dari Excel</h4>
        <p class="text-muted small mb-0">Unggah berkas spreadsheet (.xlsx, .xls, atau .csv) untuk memasukkan data pemuda secara massal ke dalam sistem.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('admin/pemuda/template-import') ?>" class="btn btn-success rounded-3 shadow-sm px-3">
            <i class="bi bi-download me-1"></i> Unduh Format Template Excel (.xlsx)
        </a>
    </div>
</div>

<!-- IMPORT ERROR / WARNING DETAILS (IF ANY) -->
<?php if (session()->getFlashdata('import_errors')): ?>
    <div class="card border-danger border-opacity-50 shadow-sm mb-4">
        <div class="card-header bg-danger text-white py-3 d-flex align-items-center justify-content-between">
            <div class="fw-bold">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                Detail Kesalahan Validasi Baris Excel (<?= count(session()->getFlashdata('import_errors')) ?> Isu)
            </div>
            <button class="btn btn-sm btn-outline-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseErrors">
                <i class="bi bi-chevron-down"></i> Sembunyikan / Tampilkan
            </button>
        </div>
        <div class="collapse show" id="collapseErrors">
            <div class="card-body p-3 bg-danger bg-opacity-10">
                <p class="small text-danger mb-2 fw-medium">
                    Data di bawah ini tidak dapat diproses. Silakan perbaiki baris terkait pada file Excel Anda atau aktifkan opsi "Lewati Baris Error" saat mengunggah ulang.
                </p>
                <div class="table-responsive" style="max-height: 250px;">
                    <ul class="list-group list-group-flush rounded-3 border">
                        <?php foreach (session()->getFlashdata('import_errors') as $err): ?>
                            <li class="list-group-item list-group-item-danger py-2 small d-flex align-items-start gap-2">
                                <i class="bi bi-x-circle-fill text-danger mt-1"></i>
                                <div><?= esc($err) ?></div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- KOLOM KIRI: FORM UPLOAD FILE -->
    <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <div class="fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-cloud-arrow-up-fill text-primary fs-5"></i>
                    <span>Unggah File Spreadsheet</span>
                </div>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('admin/pemuda/import') ?>" method="POST" enctype="multipart/form-data" id="formImport">
                    <?= csrf_field() ?>

                    <!-- STEP 1: DOWNLOAD TEMPLATE BANNER -->
                    <div class="p-3 bg-light rounded-3 border mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <h6 class="fw-bold text-dark mb-1">
                                <i class="bi bi-file-earmark-spreadsheet-fill text-success me-1"></i> 1. Gunakan Template Standar
                            </h6>
                            <p class="text-muted small mb-0">Pastikan format kolom sesuai dengan template resmi agar data terbaca sempurna.</p>
                        </div>
                        <a href="<?= base_url('admin/pemuda/template-import') ?>" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-semibold">
                            <i class="bi bi-download me-1"></i> Unduh Template
                        </a>
                    </div>

                    <!-- STEP 2: FILE INPUT -->
                    <div class="mb-4">
                        <label for="file_excel" class="form-label fw-bold text-dark">
                            2. Pilih Berkas Excel / CSV <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="file" 
                                   class="form-control form-control-lg <?= session()->getFlashdata('errors.file_excel') ? 'is-invalid' : '' ?>" 
                                   id="file_excel" 
                                   name="file_excel" 
                                   accept=".xlsx, .xls, .csv" 
                                   required>
                        </div>
                        <div class="form-text small text-muted mt-2">
                            <i class="bi bi-info-circle me-1"></i> Format yang didukung: <strong>.xlsx</strong>, <strong>.xls</strong>, <strong>.csv</strong> (Maksimal 10 MB).
                        </div>
                        <?php if (session()->getFlashdata('errors.file_excel')): ?>
                            <div class="invalid-feedback d-block">
                                <?= session()->getFlashdata('errors.file_excel') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- STEP 3: IMPORT OPTIONS -->
                    <div class="card border rounded-3 p-3 mb-4 bg-light bg-opacity-50">
                        <h6 class="fw-bold text-dark mb-3">
                            <i class="bi bi-sliders me-1 text-primary"></i> 3. Pengaturan Opsi Import
                        </h6>

                        <!-- Default Status Verifikasi -->
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Status Verifikasi Default</label>
                            <select name="default_verifikasi" class="form-select">
                                <option value="verified" selected>Langsung Terverifikasi (Valid)</option>
                                <option value="pending">Menunggu Verifikasi (Pending)</option>
                            </select>
                            <div class="form-text small">Jika kolom status verifikasi pada file Excel dikosongkan, status ini yang akan digunakan.</div>
                        </div>

                        <!-- Skip Errors Checkbox -->
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="skip_errors" name="skip_errors" value="1" checked>
                            <label class="form-check-label fw-semibold text-dark small" for="skip_errors">
                                Lewati baris data yang error (Import baris yang valid saja)
                            </label>
                            <div class="form-text small text-muted">
                                Jika diaktifkan, data baris yang valid akan tetap disimpan ke database, dan baris yang tidak lengkap akan dilaporkan pada ringkasan hasil.
                            </div>
                        </div>
                    </div>

                    <!-- SUBMIT BUTTONS -->
                    <div class="d-flex justify-content-between align-items-center pt-2">
                        <a href="<?= base_url('admin/pemuda') ?>" class="btn btn-light rounded-3 px-3">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary rounded-3 px-4 shadow-sm fw-semibold" id="btnSubmitImport">
                            <i class="bi bi-cloud-arrow-up-fill me-1"></i> Mulai Proses Import Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- KOLOM KANAN: PANDUAN & PETUNJUK KOLOM -->
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <div class="fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-lightbulb-fill text-warning fs-5"></i>
                    <span>Panduan Format Kolom Excel</span>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="accordion accordion-flush" id="accordionGuide">
                    <!-- Item 1: Kolom Wajib -->
                    <div class="accordion-item border-0 mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button bg-light rounded-3 py-2 fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#guideMandatory">
                                <i class="bi bi-check2-square text-primary me-2"></i> Kolom Wajib Diisi (*)
                            </button>
                        </h2>
                        <div id="guideMandatory" class="accordion-collapse collapse show">
                            <div class="accordion-body px-2 py-3 small text-muted">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2"><strong class="text-dark">Nama Lengkap</strong> — Nama pemuda (min. 3 karakter).</li>
                                    <li class="mb-2"><strong class="text-dark">Jenis Kelamin</strong> — Isi <span class="badge bg-light text-dark border">L</span> untuk Laki-laki atau <span class="badge bg-light text-dark border">P</span> untuk Perempuan.</li>
                                    <li class="mb-2"><strong class="text-dark">Cabang</strong> — Nama Cabang (contoh: <code>Sragen 1</code>, <code>Gemolong 1</code>) atau Kode (contoh: <code>CBG-001</code>).</li>
                                    <li class="mb-2"><strong class="text-dark">Tanggal Lahir</strong> — Format <code>YYYY-MM-DD</code> (contoh: <code>2001-05-14</code>) atau <code>DD/MM/YYYY</code>.</li>
                                    <li class="mb-2"><strong class="text-dark">No. Telepon / WA</strong> — Nomor kontak aktif (contoh: <code>081234567890</code>).</li>
                                    <li class="mb-2"><strong class="text-dark">Kecamatan & Desa</strong> — Nama Kecamatan dan Desa di Kabupaten Sragen.</li>
                                    <li class="mb-2"><strong class="text-dark">Jenjang Pendidikan</strong> — Contoh: <code>SMA / SMK / MA</code>, <code>Sarjana (S1 / D4)</code>, <code>SMP</code>, <code>Diploma</code>.</li>
                                    <li class="mb-2"><strong class="text-dark">Status Pendidikan</strong> — <code>lulus</code>, <code>sedang_sekolah</code>, atau <code>putus_sekolah</code>.</li>
                                    <li class="mb-0"><strong class="text-dark">Status Pekerjaan</strong> — Contoh: <code>Karyawan Swasta</code>, <code>Pelajar / Mahasiswa</code>, <code>Wirausaha</code>, <code>Belum / Tidak Bekerja</code>.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Item 2: Kolom Opsional -->
                    <div class="accordion-item border-0 mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed bg-light rounded-3 py-2 fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#guideOptional">
                                <i class="bi bi-plus-circle text-success me-2"></i> Kolom Opsional &amp; Multi-Value
                            </button>
                        </h2>
                        <div id="guideOptional" class="accordion-collapse collapse">
                            <div class="accordion-body px-2 py-3 small text-muted">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2"><strong class="text-dark">Email</strong> — Alamat surel pemuda.</li>
                                    <li class="mb-2"><strong class="text-dark">Status Nikah</strong> — <code>belum_menikah</code>, <code>sudah_menikah</code>, <code>janda</code>, <code>duda</code>.</li>
                                    <li class="mb-2"><strong class="text-dark">Golongan Darah</strong> — <code>A</code>, <code>B</code>, <code>AB</code>, <code>O</code>, atau <code>tidak_tahu</code>.</li>
                                    <li class="mb-2"><strong class="text-dark">Organisasi</strong> — Nama organisasi, jika lebih dari satu pisahkan dengan koma (<code>,</code>).</li>
                                    <li class="mb-2"><strong class="text-dark">Keahlian</strong> — Pisahkan dengan koma (contoh: <code>Desain Grafis, Pemrograman, Public Speaking</code>).</li>
                                    <li class="mb-0"><strong class="text-dark">Minat</strong> — Pisahkan dengan koma (contoh: <code>Teknologi &amp; Robotika, Olahraga &amp; Kebugaran</code>).</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Item 3: Statistik Master Database -->
                    <div class="accordion-item border-0">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed bg-light rounded-3 py-2 fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#guideRef">
                                <i class="bi bi-database-check text-info me-2"></i> Master Data Tersedia di Sistem
                            </button>
                        </h2>
                        <div id="guideRef" class="accordion-collapse collapse">
                            <div class="accordion-body px-2 py-3 small text-muted">
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
                                <p class="text-muted mt-2 mb-0" style="font-size: 0.75rem;">
                                    * Daftar lengkap kode &amp; nama cabang dapat dilihat pada lembar (sheet) ke-2 di dalam file template Excel yang diunduh.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById('formImport');
        const btn = document.getElementById('btnSubmitImport');

        if (form && btn) {
            form.addEventListener('submit', function() {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Sedang Mengimport Data...';
            });
        }
    });
</script>
<?= $this->endSection() ?>
