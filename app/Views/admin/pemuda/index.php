<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<!-- HEADER ACTIONS -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
    <div>
        <h4 class="font-weight-bold text-dark mb-1">Manajemen Data Pemuda</h4>
        <p class="text-muted text-xs mb-0">Kelola, verifikasi, saring, dan export seluruh data pemuda se-Kabupaten Sragen.</p>
    </div>
    <div class="mt-2 mt-md-0">
        <div class="btn-group">
            <?php if (session()->get('role') === 'superadmin'): ?>
                <a href="<?= base_url('admin/mta-sync') ?>" class="btn btn-outline-info btn-sm font-weight-bold">
                    <i class="fas fa-sync-alt mr-1"></i> Sinkron &amp; Verifikasi MTA
                </a>
                <a href="<?= base_url('admin/pemuda/import') ?>" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-file-excel mr-1"></i> Import Excel
                </a>
            <?php endif; ?>
            <a href="<?= base_url('admin/pemuda/export?' . http_build_query($filters)) ?>" class="btn btn-outline-success btn-sm">
                <i class="fas fa-download mr-1"></i> Export Excel
            </a>
            <a href="<?= base_url('admin/pemuda/tambah') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-user-plus mr-1"></i> Tambah Pemuda
            </a>
        </div>
    </div>
</div>

<!-- STATUS TABS -->
<div class="card card-primary card-outline card-outline-tabs shadow-sm mb-3">
    <div class="card-header p-0 border-bottom-0">
        <?php 
            $currVerif = $filters['status_verifikasi'] ?? '';
            $currStatusData = $filters['status_data'] ?? 'active';
        ?>
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link <?= (empty($currVerif) && $currStatusData === 'active') ? 'active font-weight-bold' : '' ?>" 
                   href="<?= base_url('admin/pemuda?status_data=active') ?>">
                    <i class="fas fa-users mr-1"></i> Semua Aktif 
                    <span class="badge badge-primary ml-1"><?= number_format($summary['active'] ?? 0) ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($currVerif === 'verified') ? 'active font-weight-bold text-success' : 'text-success' ?>" 
                   href="<?= base_url('admin/pemuda?status_verifikasi=verified&status_data=active') ?>">
                    <i class="fas fa-check-circle mr-1"></i> Terverifikasi
                    <span class="badge badge-success ml-1"><?= number_format($summary['verified'] ?? 0) ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($currVerif === 'pending') ? 'active font-weight-bold text-secondary' : 'text-secondary' ?>" 
                   href="<?= base_url('admin/pemuda?status_verifikasi=pending&status_data=active') ?>">
                    <i class="fas fa-clock mr-1"></i> Belum Terverifikasi
                    <span class="badge badge-secondary ml-1"><?= number_format($summary['pending'] ?? 0) ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($currStatusData === 'archived') ? 'active font-weight-bold text-secondary' : 'text-secondary' ?>" 
                   href="<?= base_url('admin/pemuda?status_data=archived') ?>">
                    <i class="fas fa-archive mr-1"></i> Data Arsip
                    <span class="badge badge-secondary ml-1"><?= number_format($summary['archived'] ?? 0) ?></span>
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- FILTER CARD -->
<div class="card card-default card-outline shadow-sm mb-3">
    <div class="card-header border-0">
        <h3 class="card-title font-weight-bold text-sm">
            <i class="fas fa-filter text-primary mr-1"></i> Filter &amp; Pencarian Data
        </h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body pt-1">
        <form action="<?= base_url('admin/pemuda') ?>" method="GET" id="filterForm">
            <div class="row">
                <!-- Search Keyword -->
                <div class="col-12 col-md-4 mb-2">
                    <label class="text-xs text-muted font-weight-bold mb-1">Kata Kunci Pencarian</label>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               placeholder="Nama / No. Reg / Kontak / Sekolah..." 
                               value="<?= esc($filters['search'] ?? '') ?>">
                    </div>
                </div>

                <!-- Wilayah -->
                <div class="col-12 col-sm-6 col-md-4 mb-2">
                    <label class="text-xs text-muted font-weight-bold mb-1">Wilayah</label>
                    <?php if (session()->get('role') === 'superadmin'): ?>
                        <select name="wilayah_id" id="filterWilayah" class="form-control form-control-sm">
                            <option value="">-- Semua Wilayah --</option>
                            <?php foreach ($wilayahList as $w): ?>
                                <option value="<?= $w['id'] ?>" <?= ($filters['wilayah_id'] ?? '') == $w['id'] ? 'selected' : '' ?>>
                                    <?= esc($w['name']) ?> (<?= esc($w['code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <select class="form-control form-control-sm bg-light" disabled>
                            <?php foreach ($wilayahList as $w): ?>
                                <option selected><?= esc($w['name']) ?> (<?= esc($w['code']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="wilayah_id" value="<?= esc($filters['wilayah_id'] ?? session()->get('wilayah_id')) ?>">
                    <?php endif; ?>
                </div>

                <!-- Cabang -->
                <div class="col-12 col-sm-6 col-md-4 mb-2">
                    <label class="text-xs text-muted font-weight-bold mb-1">Cabang</label>
                    <?php if (session()->get('role') === 'admin_cabang'): ?>
                        <select class="form-control form-control-sm bg-light" disabled>
                            <?php foreach ($cabangList as $c): ?>
                                <option selected><?= esc($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="cabang_id" value="<?= esc(session()->get('cabang_id')) ?>">
                    <?php else: ?>
                        <select name="cabang_id" id="filterCabang" class="form-control form-control-sm">
                            <option value="">-- Semua Cabang --</option>
                            <?php foreach ($cabangList as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= ($filters['cabang_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                                    <?= esc($c['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>

                <!-- Gender -->
                <div class="col-6 col-md-2 mb-2">
                    <label class="text-xs text-muted font-weight-bold mb-1">Gender</label>
                    <select name="gender" class="form-control form-control-sm">
                        <option value="">-- Semua --</option>
                        <option value="L" <?= ($filters['gender'] ?? '') === 'L' ? 'selected' : '' ?>>Laki-laki (L)</option>
                        <option value="P" <?= ($filters['gender'] ?? '') === 'P' ? 'selected' : '' ?>>Perempuan (P)</option>
                    </select>
                </div>

                <!-- Status Pernikahan -->
                <div class="col-6 col-md-2 mb-2">
                    <label class="text-xs text-muted font-weight-bold mb-1">Status Nikah</label>
                    <select name="marital_status" class="form-control form-control-sm">
                        <option value="">-- Semua --</option>
                        <option value="belum_menikah" <?= ($filters['marital_status'] ?? '') === 'belum_menikah' ? 'selected' : '' ?>>Belum Menikah</option>
                        <option value="sudah_menikah" <?= ($filters['marital_status'] ?? '') === 'sudah_menikah' ? 'selected' : '' ?>>Sudah Menikah</option>
                        <option value="janda" <?= ($filters['marital_status'] ?? '') === 'janda' ? 'selected' : '' ?>>Janda</option>
                        <option value="duda" <?= ($filters['marital_status'] ?? '') === 'duda' ? 'selected' : '' ?>>Duda</option>
                    </select>
                </div>

                <!-- Golongan Darah -->
                <div class="col-6 col-md-2 mb-2">
                    <label class="text-xs text-muted font-weight-bold mb-1">Gol. Darah</label>
                    <select name="blood_type" class="form-control form-control-sm">
                        <option value="">-- Semua --</option>
                        <option value="A" <?= ($filters['blood_type'] ?? '') === 'A' ? 'selected' : '' ?>>A</option>
                        <option value="B" <?= ($filters['blood_type'] ?? '') === 'B' ? 'selected' : '' ?>>B</option>
                        <option value="AB" <?= ($filters['blood_type'] ?? '') === 'AB' ? 'selected' : '' ?>>AB</option>
                        <option value="O" <?= ($filters['blood_type'] ?? '') === 'O' ? 'selected' : '' ?>>O</option>
                    </select>
                </div>

                <!-- Pendidikan -->
                <div class="col-6 col-md-3 mb-2">
                    <label class="text-xs text-muted font-weight-bold mb-1">Pendidikan</label>
                    <select name="education_level_id" class="form-control form-control-sm">
                        <option value="">-- Semua Pendidikan --</option>
                        <?php foreach ($educationLevels as $edu): ?>
                            <option value="<?= $edu['id'] ?>" <?= ($filters['education_level_id'] ?? '') == $edu['id'] ? 'selected' : '' ?>>
                                <?= esc($edu['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Status Pekerjaan -->
                <div class="col-6 col-md-3 mb-2">
                    <label class="text-xs text-muted font-weight-bold mb-1">Status Pekerjaan</label>
                    <select name="job_status_id" class="form-control form-control-sm">
                        <option value="">-- Semua Pekerjaan --</option>
                        <?php foreach ($jobStatuses as $job): ?>
                            <option value="<?= $job['id'] ?>" <?= ($filters['job_status_id'] ?? '') == $job['id'] ? 'selected' : '' ?>>
                                <?= esc($job['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Hidden inputs -->
                <input type="hidden" name="status_verifikasi" value="<?= esc($filters['status_verifikasi'] ?? '') ?>">
                <input type="hidden" name="status_data" value="<?= esc($filters['status_data'] ?? 'active') ?>">

                <!-- Actions -->
                <div class="col-12 d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                    <a href="<?= base_url('admin/pemuda') ?>" class="btn btn-default btn-sm">
                        <i class="fas fa-undo mr-1"></i> Reset
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm px-3">
                        <i class="fas fa-filter mr-1"></i> Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- DATA TABLE CARD -->
<div class="card card-primary card-outline shadow-sm">
    <div class="card-header border-0 d-flex justify-content-between align-items-center">
        <h3 class="card-title font-weight-bold text-sm">
            <i class="fas fa-list mr-1 text-primary"></i> Daftar Pemuda
        </h3>
        <div class="card-tools text-muted text-xs">
            Menampilkan <?= count($pemudaList) ?> data pada halaman ini
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped table-vcenter mb-0" style="font-size: 0.85rem;">
                <thead class="thead-light">
                    <tr>
                        <th class="pl-3" style="width: 40px;">No</th>
                        <th>No. Registrasi</th>
                        <th>Nama &amp; Kontak</th>
                        <th>Wilayah &amp; Cabang</th>
                        <th>Demografi</th>
                        <th>Pendidikan &amp; Kerja</th>
                        <th>Status Verifikasi</th>
                        <th>Tgl Daftar</th>
                        <th class="text-right pr-3" style="min-width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pemudaList)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted mb-2"><i class="fas fa-search fa-2x"></i></div>
                                <h6 class="font-weight-bold text-dark">Tidak ada data pemuda yang cocok</h6>
                                <p class="text-muted text-xs mb-0">Coba ubah kata kunci atau atur ulang filter pencarian Anda.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $page = (int) ($pager->getCurrentPage('pemuda') ?? 1);
                        $no = (($page - 1) * $perPage) + 1;
                        foreach ($pemudaList as $p): 
                        ?>
                            <tr>
                                <td class="pl-3 text-muted"><?= $no++ ?></td>
                                <td>
                                    <a href="<?= base_url('admin/pemuda/detail/' . $p['id']) ?>" class="font-weight-bold text-primary">
                                        <?= esc($p['registration_number']) ?>
                                    </a>
                                    <?php if (!empty($p['mta_warga_uuid'])): ?>
                                        <span class="badge badge-info ml-1" title="Tersinkron dengan Database Warga MTA" style="font-size: 0.65rem;">
                                            <i class="fas fa-check-double mr-1"></i>MTA
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($p['status_data'] === 'archived'): ?>
                                        <span class="badge badge-secondary ml-1" style="font-size: 0.65rem;">Arsip</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="font-weight-bold text-dark"><?= esc($p['name']) ?></div>
                                    <div class="text-muted text-xs">
                                        <?php if (!empty($p['phone'])): ?>
                                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', (str_starts_with($p['phone'], '0') ? ('62' . substr($p['phone'], 1)) : $p['phone'])) ?>" 
                                               target="_blank" 
                                               class="text-success font-weight-semibold">
                                                <i class="fab fa-whatsapp"></i> <?= esc($p['phone']) ?>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($p['email'])): ?>
                                            &bull; <?= esc($p['email']) ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="font-weight-semibold text-dark"><?= esc($p['cabang_name']) ?></div>
                                    <div class="text-muted text-xs"><?= esc($p['wilayah_name']) ?></div>
                                </td>
                                <td>
                                    <div>
                                        <?= $p['gender'] === 'L' ? '<span class="badge badge-primary">L</span>' : '<span class="badge badge-danger">P</span>' ?>
                                        <?php if (!empty($p['blood_type'])): ?>
                                            <span class="badge badge-light border" style="font-size: 0.7rem;"><?= esc($p['blood_type']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-muted text-xs mt-1">
                                        <?= ucwords(str_replace('_', ' ', $p['marital_status'] ?? 'belum_menikah')) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="font-weight-bold text-dark text-xs"><?= esc($p['education_level_name'] ?? '-') ?></div>
                                    <div class="text-muted text-xs"><?= esc($p['job_status_name'] ?? '-') ?></div>
                                </td>
                                <td>
                                    <?php if ($p['status_verifikasi'] === 'verified'): ?>
                                        <span class="badge badge-success px-2 py-1 font-weight-bold" title="Data Sinkron dengan MTA Pusat">
                                            <i class="fas fa-check-circle mr-1"></i> Terverifikasi
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary px-2 py-1 font-weight-bold" title="Data Belum Sinkron dengan MTA Pusat">
                                            <i class="fas fa-clock mr-1"></i> Belum Terverifikasi
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted text-xs">
                                    <?= date('d/m/y H:i', strtotime($p['created_at'])) ?>
                                </td>
                                <td class="text-right pr-3">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('admin/pemuda/detail/' . $p['id']) ?>" 
                                           class="btn btn-default btn-xs" 
                                           title="Lihat Detail Profil">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <a href="<?= base_url('admin/pemuda/edit/' . $p['id']) ?>" 
                                           class="btn btn-primary btn-xs" 
                                           title="Edit Data">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <a href="<?= base_url('admin/pemuda/cetak/' . $p['id']) ?>" 
                                           target="_blank"
                                           class="btn btn-info btn-xs" 
                                           title="Cetak Biodata">
                                            <i class="fas fa-print"></i>
                                        </a>

                                        <button type="button" class="btn btn-default btn-xs dropdown-toggle dropdown-toggle-split" data-toggle="dropdown">
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right shadow border-0 p-1">
                                            <form action="<?= base_url('admin/pemuda/verifikasi/' . $p['id']) ?>" method="POST">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="dropdown-item py-1 text-xs text-info font-weight-bold" title="Cek &amp; Sinkronkan dengan MTA Pusat">
                                                    <i class="fas fa-sync-alt mr-2"></i> Sinkronkan MTA
                                                </button>
                                            </form>
                                            <div class="dropdown-divider"></div>
                                            <form action="<?= base_url('admin/pemuda/archive/' . $p['id']) ?>" method="POST">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="dropdown-item py-1 text-xs">
                                                    <i class="fas fa-archive mr-2 text-secondary"></i>
                                                    <?= $p['status_data'] === 'archived' ? 'Aktifkan Kembali' : 'Arsipkan Data' ?>
                                                </button>
                                            </form>
                                            <?php if (session()->get('role') === 'superadmin'): ?>
                                                <div class="dropdown-divider"></div>
                                                <button type="button" 
                                                        class="dropdown-item py-1 text-xs text-danger font-weight-bold btn-delete" 
                                                        data-id="<?= $p['id'] ?>" 
                                                        data-name="<?= esc($p['name']) ?>">
                                                    <i class="fas fa-trash-alt mr-2"></i> Hapus Permanen
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- PAGINATION FOOTER -->
    <?php if ($pager->getPageCount('pemuda') > 1): ?>
        <div class="card-footer bg-white border-top py-3 d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="text-sm text-muted mb-2 mb-md-0 font-weight-500">
                <i class="fas fa-file-alt mr-1 text-primary"></i> 
                Halaman <strong class="text-dark"><?= $pager->getCurrentPage('pemuda') ?></strong> dari <strong class="text-dark"><?= $pager->getPageCount('pemuda') ?></strong>
                <span class="mx-2 text-muted">|</span>
                Total <strong class="text-dark"><?= number_format($pager->getTotal('pemuda'), 0, ',', '.') ?></strong> data
            </div>
            <div>
                <?= $pager->links('pemuda', 'bootstrap_full') ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- DELETE MODAL (SUPERADMIN ONLY) -->
<?php if (session()->get('role') === 'superadmin'): ?>
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white py-2">
                <h5 class="modal-title font-weight-bold text-sm">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Konfirmasi Hapus Permanen
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="text-danger mb-3">
                    <i class="fas fa-trash-alt fa-3x"></i>
                </div>
                <h6 class="font-weight-bold text-dark mb-2">Hapus Data Pemuda?</h6>
                <p class="text-muted text-xs mb-4">
                    Apakah Anda yakin ingin menghapus data pemuda <strong id="deletePemudaName" class="text-dark"></strong> secara permanen? Seluruh data relasi akan terhapus dan tindakan ini tidak dapat dibatalkan.
                </p>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-default btn-sm px-3 mr-2" data-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST" action="">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-danger btn-sm px-4">
                            <i class="fas fa-trash mr-1"></i> Hapus Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function () {
        // Dynamic Cabang dropdown based on Wilayah
        $('#filterWilayah').on('change', function () {
            const wilayahId = $(this).val();
            const $filterCabang = $('#filterCabang');
            $filterCabang.html('<option value="">-- Memuat Cabang... --</option>');

            if (!wilayahId) {
                $filterCabang.html('<option value="">-- Semua Cabang --</option>');
                return;
            }

            $.getJSON('<?= base_url('admin/ajax/cabang/') ?>/' + wilayahId, function (data) {
                let options = '<option value="">-- Semua Cabang --</option>';
                $.each(data, function (i, c) {
                    options += '<option value="' + c.id + '">' + c.name + '</option>';
                });
                $filterCabang.html(options);
            }).fail(function () {
                $filterCabang.html('<option value="">-- Gagal memuat cabang --</option>');
            });
        });

        // Delete modal handler
        $(document).on('click', '.btn-delete', function () {
            const id   = $(this).data('id');
            const name = $(this).data('name');
            $('#deletePemudaName').text(name);
            $('#deleteForm').attr('action', '<?= base_url('admin/pemuda/delete/') ?>/' + id);
            $('#deleteModal').modal('show');
        });
    });
</script>
<?= $this->endSection() ?>
