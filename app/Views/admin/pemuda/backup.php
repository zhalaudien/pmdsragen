<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<!-- BREADCRUMB & HEADER -->
<div class="content-header px-0 pt-0 pb-3">
    <div class="container-fluid px-0">
        <div class="row align-items-center">
            <div class="col-sm-7">
                <h4 class="m-0 font-weight-bold text-dark">
                    <i class="fas fa-database text-primary mr-2"></i>Backup &amp; Pembersihan Data Pemuda
                </h4>
                <p class="text-muted text-sm mb-0">
                    Cadangkan seluruh data pemuda ke berbagai format atau lakukan pembersihan data secara menyeluruh (Khusus Super Administrator).
                </p>
            </div>
            <div class="col-sm-5 text-sm-right mt-2 mt-sm-0">
                <a href="<?= base_url('admin/pemuda') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Data Pemuda
                </a>
            </div>
        </div>
    </div>
</div>

<!-- ALERT FEEDBACK -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-check-circle fa-lg mr-3"></i>
            <div>
                <strong>Berhasil!</strong> <?= session()->getFlashdata('success') ?>
            </div>
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle fa-lg mr-3"></i>
            <div>
                <strong>Perhatian!</strong> <?= session()->getFlashdata('error') ?>
            </div>
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- STATISTIK RINGKASAN DATA SAAT INI -->
<div class="row mb-3">
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box shadow-sm mb-3">
            <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted text-xs font-weight-bold text-uppercase">Total Pemuda</span>
                <span class="info-box-number font-weight-bold text-lg"><?= number_format($countsSummary['pemuda'] ?? 0) ?></span>
                <span class="text-xs text-muted">Seluruh Cabang</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box shadow-sm mb-3">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check-double"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted text-xs font-weight-bold text-uppercase">Terverifikasi</span>
                <span class="info-box-number font-weight-bold text-lg"><?= number_format($statusSummary['verified'] ?? 0) ?></span>
                <span class="text-xs text-success">Sinkron Database MTA</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box shadow-sm mb-3">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-clock text-white"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted text-xs font-weight-bold text-uppercase">Belum Terverifikasi</span>
                <span class="info-box-number font-weight-bold text-lg"><?= number_format($statusSummary['pending'] ?? 0) ?></span>
                <span class="text-xs text-warning">Status Pending</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box shadow-sm mb-3">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-sitemap"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted text-xs font-weight-bold text-uppercase">Data Relasi Terkait</span>
                <span class="info-box-number font-weight-bold text-lg">
                    <?= number_format(($countsSummary['alamat'] ?? 0) + ($countsSummary['pendidikan'] ?? 0) + ($countsSummary['pekerjaan'] ?? 0) + ($countsSummary['organisasi'] ?? 0)) ?>
                </span>
                <span class="text-xs text-muted">Alamat, Edu, Kerja, Org</span>
            </div>
        </div>
    </div>
</div>

<!-- SECTION 1: CADANGKAN DATA PEMUDA (BACKUP) -->
<div class="card card-outline card-primary shadow-sm mb-4">
    <div class="card-header bg-white">
        <h3 class="card-title font-weight-bold text-dark mb-0">
            <i class="fas fa-download text-primary mr-2"></i>Cadangkan Data Pemuda (Backup)
        </h3>
    </div>
    <div class="card-body">
        <p class="text-muted mb-3">
            Pilih format cadangan yang Anda butuhkan. Setiap berkas memuat seluruh atribut data pemuda beserta seluruh data relasi (alamat, riwayat pendidikan, profil pekerjaan &amp; wirausaha, keaktifan organisasi, bakat keahlian, dan minat).
        </p>

        <div class="row">
            <!-- 1. BACKUP SQL -->
            <div class="col-12 col-md-4 mb-3">
                <div class="card border h-100 shadow-none">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center mb-2">
                            <div class="rounded bg-primary-light p-2 mr-3 text-primary" style="background-color: #e8f0fe;">
                                <i class="fas fa-database fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="font-weight-bold mb-0 text-dark">SQL Database Dump</h6>
                                <span class="badge badge-primary">Format .sql</span>
                            </div>
                        </div>
                        <p class="text-muted text-xs flex-grow-1 mt-2">
                            File dump SQL standar berisi perintah <code>INSERT</code> untuk seluruh tabel data pemuda (pemuda, alamat, pendidikan, pekerjaan, organisasi, keahlian, minat). Sangat ideal untuk restorasi cepat ke MySQL / phpMyAdmin.
                        </p>
                        <div class="d-flex flex-column gap-2 mt-2">
                            <form action="<?= base_url('admin/pemuda/backup/generate') ?>" method="POST" class="mb-2">
                                <?= csrf_field() ?>
                                <input type="hidden" name="format" value="sql">
                                <input type="hidden" name="action" value="download">
                                <button type="submit" class="btn btn-primary btn-sm btn-block font-weight-bold">
                                    <i class="fas fa-cloud-download-alt mr-1"></i> Download File SQL
                                </button>
                            </form>
                            <form action="<?= base_url('admin/pemuda/backup/generate') ?>" method="POST">
                                <?= csrf_field() ?>
                                <input type="hidden" name="format" value="sql">
                                <input type="hidden" name="action" value="save">
                                <button type="submit" class="btn btn-outline-primary btn-sm btn-block">
                                    <i class="fas fa-save mr-1"></i> Simpan di Server
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. BACKUP JSON -->
            <div class="col-12 col-md-4 mb-3">
                <div class="card border h-100 shadow-none">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center mb-2">
                            <div class="rounded p-2 mr-3 text-success" style="background-color: #e6f4ea;">
                                <i class="fas fa-file-code fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="font-weight-bold mb-0 text-dark">JSON Structured Data</h6>
                                <span class="badge badge-success">Format .json</span>
                            </div>
                        </div>
                        <p class="text-muted text-xs flex-grow-1 mt-2">
                            File JSON terstruktur rapi memuat data hierarkis relasi pemuda dan raw tables. Mudah dibaca, ideal untuk integrasi API, arsip portabel, maupun konversi sistem modern lainnya.
                        </p>
                        <div class="d-flex flex-column gap-2 mt-2">
                            <form action="<?= base_url('admin/pemuda/backup/generate') ?>" method="POST" class="mb-2">
                                <?= csrf_field() ?>
                                <input type="hidden" name="format" value="json">
                                <input type="hidden" name="action" value="download">
                                <button type="submit" class="btn btn-success btn-sm btn-block font-weight-bold">
                                    <i class="fas fa-cloud-download-alt mr-1"></i> Download File JSON
                                </button>
                            </form>
                            <form action="<?= base_url('admin/pemuda/backup/generate') ?>" method="POST">
                                <?= csrf_field() ?>
                                <input type="hidden" name="format" value="json">
                                <input type="hidden" name="action" value="save">
                                <button type="submit" class="btn btn-outline-success btn-sm btn-block">
                                    <i class="fas fa-save mr-1"></i> Simpan di Server
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. BACKUP EXCEL -->
            <div class="col-12 col-md-4 mb-3">
                <div class="card border h-100 shadow-none">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center mb-2">
                            <div class="rounded p-2 mr-3 text-info" style="background-color: #e8f4f8;">
                                <i class="fas fa-file-excel fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="font-weight-bold mb-0 text-dark">Excel Spreadsheet</h6>
                                <span class="badge badge-info">Format .xlsx</span>
                            </div>
                        </div>
                        <p class="text-muted text-xs flex-grow-1 mt-2">
                            Dokumen Microsoft Excel lengkap berisi seluruh kolom (biodata, alamat, sekolah, pekerjaan wirausaha, organisasi, keahlian, minat, dan status MTA) dengan layout rapi dan siap olah.
                        </p>
                        <div class="d-flex flex-column gap-2 mt-2">
                            <form action="<?= base_url('admin/pemuda/backup/generate') ?>" method="POST" class="mb-2">
                                <?= csrf_field() ?>
                                <input type="hidden" name="format" value="xlsx">
                                <input type="hidden" name="action" value="download">
                                <button type="submit" class="btn btn-info btn-sm btn-block font-weight-bold text-white">
                                    <i class="fas fa-cloud-download-alt mr-1"></i> Download File Excel
                                </button>
                            </form>
                            <form action="<?= base_url('admin/pemuda/backup/generate') ?>" method="POST">
                                <?= csrf_field() ?>
                                <input type="hidden" name="format" value="xlsx">
                                <input type="hidden" name="action" value="save">
                                <button type="submit" class="btn btn-outline-info btn-sm btn-block">
                                    <i class="fas fa-save mr-1"></i> Simpan di Server
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SECTION 2: RIWAYAT BERKAS BACKUP DI SERVER -->
<div class="card card-outline card-secondary shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h3 class="card-title font-weight-bold text-dark mb-0">
            <i class="fas fa-folder-open text-secondary mr-2"></i>Berkas Cadangan Tersimpan di Server
        </h3>
        <span class="badge badge-secondary"><?= count($backupList) ?> Berkas</span>
    </div>
    <div class="card-body p-0">
        <?php if (empty($backupList)): ?>
            <div class="text-center py-5">
                <i class="fas fa-archive fa-3x text-muted mb-2" style="opacity: 0.4;"></i>
                <p class="text-muted mb-1 font-weight-bold">Belum Ada Berkas Cadangan Tersimpan di Server</p>
                <p class="text-xs text-muted mb-0">Gunakan tombol "Simpan di Server" di atas untuk menyimpan arsip snapshot cadangan.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0 text-sm">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 50px;" class="text-center">No</th>
                            <th>Nama Berkas</th>
                            <th style="width: 110px;">Format</th>
                            <th style="width: 130px;">Ukuran File</th>
                            <th style="width: 170px;">Waktu Pembuatan</th>
                            <th style="width: 160px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($backupList as $idx => $item): ?>
                            <tr>
                                <td class="text-center font-weight-bold text-muted"><?= $idx + 1 ?></td>
                                <td>
                                    <div class="font-weight-bold text-dark font-monospace">
                                        <?= esc($item['filename']) ?>
                                    </div>
                                    <?php if ($item['is_auto_backup']): ?>
                                        <span class="badge badge-warning text-dark text-xs mt-1">
                                            <i class="fas fa-shield-alt mr-1"></i> Auto-Backup Keamanan
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($item['extension'] === 'SQL'): ?>
                                        <span class="badge badge-primary px-2 py-1">SQL</span>
                                    <?php elseif ($item['extension'] === 'JSON'): ?>
                                        <span class="badge badge-success px-2 py-1">JSON</span>
                                    <?php elseif ($item['extension'] === 'XLSX'): ?>
                                        <span class="badge badge-info px-2 py-1">EXCEL</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary px-2 py-1"><?= esc($item['extension']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="font-weight-bold text-dark"><?= esc($item['size_formatted']) ?></span>
                                </td>
                                <td>
                                    <span class="text-muted"><i class="far fa-clock mr-1"></i><?= esc($item['created_at']) ?></span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('admin/pemuda/backup/download/' . urlencode($item['filename'])) ?>" 
                                           class="btn btn-outline-success" 
                                           title="Unduh Berkas">
                                            <i class="fas fa-download"></i> Unduh
                                        </a>
                                        <button type="button" 
                                                class="btn btn-outline-danger btn-delete-backup" 
                                                data-filename="<?= esc($item['filename']) ?>"
                                                title="Hapus Berkas dari Server">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    <div class="card-footer bg-light text-xs text-muted">
        <i class="fas fa-info-circle mr-1"></i> Berkas disimpan di direktori internal yang terlindungi: <code>writable/backups/</code>
    </div>
</div>

<!-- SECTION 3: ZONA BERBAHAYA (DANGER ZONE) - HAPUS SEMUA DATA PEMUDA -->
<div class="card card-outline card-danger shadow-sm">
    <div class="card-header bg-white">
        <h3 class="card-title font-weight-bold text-danger mb-0">
            <i class="fas fa-exclamation-triangle text-danger mr-2"></i>Zona Bahaya: Hapus Semua Data Pemuda
        </h3>
    </div>
    <div class="card-body">
        <div class="alert alert-warning border-left-warning mb-3">
            <div class="d-flex">
                <i class="fas fa-exclamation-circle fa-2x text-warning mr-3 mt-1"></i>
                <div>
                    <h6 class="font-weight-bold text-dark mb-1">Perhatian Sangat Penting!</h6>
                    <p class="text-sm mb-0">
                        Fitur ini dirancang khusus untuk <strong>Super Administrator</strong> jika ingin melakukan pembersihan total data pemuda (misalnya saat pengujian selesai, sebelum peluncuran resmi sistem, atau reset data tahunan).
                    </p>
                </div>
            </div>
        </div>

        <p class="text-sm text-muted">
            Tindakan ini akan <strong>MENGHAPUS SELURUH DATA PEMUDA (<?= number_format($countsSummary['pemuda'] ?? 0) ?> PEMUDA)</strong> secara permanen dari basis data, termasuk seluruh data terkait:
        </p>
        <ul class="text-xs text-muted mb-3 pl-3">
            <li>Data identitas &amp; profil pemuda (<code>pemuda</code>)</li>
            <li>Alamat lengkap &amp; domisili (<code>alamat</code>)</li>
            <li>Riwayat dan jenjang pendidikan (<code>pendidikan</code>)</li>
            <li>Data profesi, pekerjaan, dan usaha wirausaha (<code>pekerjaan</code>)</li>
            <li>Data keikutsertaan organisasi (<code>organisasi</code>)</li>
            <li>Bakat &amp; keahlian pemuda (<code>pemuda_skills</code>)</li>
            <li>Minat kepemudaan (<code>pemuda_interests</code>)</li>
            <li>Antrian sinkronisasi MTA Pusat (<code>mta_sync_queue</code>)</li>
        </ul>

        <div class="d-flex align-items-center p-3 rounded mb-3" style="background-color: #fdf2f2; border: 1px solid #f8d7da;">
            <i class="fas fa-shield-alt fa-2x text-success mr-3"></i>
            <div class="text-xs text-dark">
                <strong>Sistem Proteksi Otomatis:</strong><br>
                Sebelum proses penghapusan dieksekusi, sistem secara otomatis akan membuat <strong>1 berkas snapshot cadangan darurat (SQL auto-backup)</strong> yang tersimpan di server. Namun demikian, kami tetap menyarankan Anda mengunduh cadangan secara manual terlebih dahulu.
            </div>
        </div>

        <?php if (($countsSummary['pemuda'] ?? 0) === 0): ?>
            <button type="button" class="btn btn-secondary btn-sm" disabled>
                <i class="fas fa-ban mr-1"></i> Database Pemuda Sudah Kosong (0 Data)
            </button>
        <?php else: ?>
            <button type="button" class="btn btn-danger btn-sm font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalHapusSemua">
                <i class="fas fa-trash-alt mr-1"></i> Hapus Seluruh Data Pemuda Sekarang...
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- MODAL KONFIRMASI HAPUS SEMUA DATA PEMUDA -->
<div class="modal fade" id="modalHapusSemua" tabindex="-1" role="dialog" aria-labelledby="modalHapusSemuaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-danger shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title font-weight-bold" id="modalHapusSemuaLabel">
                    <i class="fas fa-skull-crossbones mr-2"></i>Konfirmasi Hapus Semua Data Pemuda
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('admin/pemuda/hapus-semua') ?>" method="POST" id="formHapusSemua">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <div class="display-4 text-danger mb-2">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h5 class="font-weight-bold text-danger">Anda Akan Menghapus Seluruh Data Pemuda!</h5>
                        <p class="text-sm text-muted mb-0">
                            Sebanyak <strong class="text-danger"><?= number_format($countsSummary['pemuda'] ?? 0) ?> data pemuda</strong> beserta seluruh data relasi akan dihapus secara permanen.
                        </p>
                    </div>

                    <div class="alert alert-danger text-xs mb-3">
                        <i class="fas fa-info-circle mr-1"></i>
                        Untuk memastikan tindakan ini disengaja dan diotorisasi oleh Super Administrator, silakan lengkapi konfirmasi keamanan ganda berikut:
                    </div>

                    <!-- 1. KONFIRMASI TEKS -->
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-xs text-dark mb-1">
                            1. Ketikkan kalimat: <code class="text-danger font-weight-bold" style="user-select: all;">HAPUS SEMUA PEMUDA</code>
                        </label>
                        <input type="text" 
                               name="confirm_text" 
                               id="confirmTextInput" 
                               class="form-control form-control-sm font-weight-bold" 
                               placeholder="Ketik persis: HAPUS SEMUA PEMUDA" 
                               autocomplete="off" 
                               required>
                        <small class="form-text text-muted text-xs">Penulisan huruf besar dan spasi harus sama persis.</small>
                    </div>

                    <!-- 2. KONFIRMASI PASSWORD -->
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-xs text-dark mb-1">
                            2. Masukkan Password Akun Super Administrator Anda:
                        </label>
                        <div class="input-group input-group-sm">
                            <input type="password" 
                                   name="password" 
                                   id="passwordInput" 
                                   class="form-control form-control-sm" 
                                   placeholder="Password akun Anda saat ini" 
                                   required>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="btnTogglePassword">
                                    <i class="fas fa-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted text-xs">Diperlukan untuk memverifikasi hak otorisasi Anda.</small>
                    </div>

                    <!-- 3. CHECKBOX PERNYATAAN -->
                    <div class="custom-control custom-checkbox mb-2">
                        <input type="checkbox" class="custom-control-input" id="checkAgreement" required>
                        <label class="custom-control-label text-xs font-weight-semibold text-danger" for="checkAgreement">
                            Saya sadar sepenuhnya dan bertanggung jawab penuh atas tindakan penghapusan seluruh data pemuda ini.
                        </label>
                    </div>
                </div>
                <div class="modal-footer bg-light justify-content-between">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batalkan
                    </button>
                    <button type="submit" class="btn btn-danger btn-sm font-weight-bold" id="btnSubmitHapusSemua" disabled>
                        <i class="fas fa-trash-alt mr-1"></i> Ya, Hapus Semua Data Pemuda
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL KONFIRMASI HAPUS SATU BERKAS BACKUP -->
<div class="modal fade" id="modalDeleteBackupFile" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content shadow">
            <div class="modal-header bg-danger text-white py-2">
                <h6 class="modal-title font-weight-bold mb-0">Hapus Berkas Cadangan</h6>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formDeleteBackupFile" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body text-sm py-3">
                    <p class="mb-1">Apakah Anda yakin ingin menghapus berkas cadangan berikut dari server?</p>
                    <div class="p-2 bg-light rounded text-xs font-monospace font-weight-bold text-danger text-break" id="deleteTargetFilename"></div>
                </div>
                <div class="modal-footer py-2 justify-content-between">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm">Hapus Berkas</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Validasi interaktif modal konfirmasi Hapus Semua
    const confirmInput = $('#confirmTextInput');
    const passwordInput = $('#passwordInput');
    const agreementCheck = $('#checkAgreement');
    const submitBtn = $('#btnSubmitHapusSemua');

    function checkFormValidity() {
        const textMatched = confirmInput.val().trim() === 'HAPUS SEMUA PEMUDA';
        const passwordFilled = passwordInput.val().trim().length >= 4;
        const agreementAgreed = agreementCheck.is(':checked');

        if (textMatched && passwordFilled && agreementAgreed) {
            submitBtn.prop('disabled', false);
        } else {
            submitBtn.prop('disabled', true);
        }
    }

    confirmInput.on('input keyup change', checkFormValidity);
    passwordInput.on('input keyup change', checkFormValidity);
    agreementCheck.on('change', checkFormValidity);

    // Toggle lihat password
    $('#btnTogglePassword').on('click', function() {
        const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
        passwordInput.attr('type', type);
        $('#eyeIcon').toggleClass('fa-eye fa-eye-slash');
    });

    // Loading state saat submit hapus semua
    $('#formHapusSemua').on('submit', function() {
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Sedang Memproses...');
    });

    // Modal Hapus Berkas Backup
    $('.btn-delete-backup').on('click', function() {
        const filename = $(this).data('filename');
        $('#deleteTargetFilename').text(filename);
        $('#formDeleteBackupFile').attr('action', '<?= base_url('admin/pemuda/backup/delete-file') ?>/' + encodeURIComponent(filename));
        $('#modalDeleteBackupFile').modal('show');
    });
});
</script>
<?= $this->endSection() ?>
