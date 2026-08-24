<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<!-- HEADER ACTIONS -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Manajemen Data Pemuda</h4>
        <p class="text-muted small mb-0">Kelola, verifikasi, saring, dan export seluruh data pemuda se-Kabupaten Sragen.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="<?= base_url('admin/pemuda/export?' . http_build_query($filters)) ?>" class="btn btn-outline-success rounded-3 shadow-sm">
            <i class="bi bi-file-earmark-excel-fill me-1"></i> Export Excel / CSV
        </a>
        <a href="<?= base_url('admin/pemuda/tambah') ?>" class="btn btn-primary rounded-3 shadow-sm">
            <i class="bi bi-person-plus-fill me-1"></i> Tambah Pemuda
        </a>
    </div>
</div>

<!-- STATUS TABS -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-2">
        <ul class="nav nav-pills gap-1">
            <?php 
                $currVerif = $filters['status_verifikasi'] ?? '';
                $currStatusData = $filters['status_data'] ?? 'active';
            ?>
            <li class="nav-item">
                <a class="nav-link <?= (empty($currVerif) && $currStatusData === 'active') ? 'active' : '' ?>" 
                   href="<?= base_url('admin/pemuda?status_data=active') ?>">
                    <i class="bi bi-people-fill me-1"></i> Semua Aktif 
                    <span class="badge bg-white text-dark ms-1"><?= number_format($summary['active'] ?? 0) ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($currVerif === 'pending') ? 'active bg-warning text-dark' : 'text-warning' ?>" 
                   href="<?= base_url('admin/pemuda?status_verifikasi=pending&status_data=active') ?>">
                    <i class="bi bi-clock-history me-1"></i> Menunggu Verifikasi
                    <span class="badge <?= ($currVerif === 'pending') ? 'bg-dark text-white' : 'bg-warning text-dark' ?> ms-1"><?= number_format($summary['pending'] ?? 0) ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($currVerif === 'verified') ? 'active bg-success' : 'text-success' ?>" 
                   href="<?= base_url('admin/pemuda?status_verifikasi=verified&status_data=active') ?>">
                    <i class="bi bi-check-circle-fill me-1"></i> Terverifikasi
                    <span class="badge <?= ($currVerif === 'verified') ? 'bg-white text-success' : 'bg-success text-white' ?> ms-1"><?= number_format($summary['verified'] ?? 0) ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($currVerif === 'rejected') ? 'active bg-danger' : 'text-danger' ?>" 
                   href="<?= base_url('admin/pemuda?status_verifikasi=rejected&status_data=active') ?>">
                    <i class="bi bi-x-circle-fill me-1"></i> Ditolak
                    <span class="badge <?= ($currVerif === 'rejected') ? 'bg-white text-danger' : 'bg-danger text-white' ?> ms-1"><?= number_format($summary['rejected'] ?? 0) ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($currStatusData === 'archived') ? 'active bg-secondary' : 'text-secondary' ?>" 
                   href="<?= base_url('admin/pemuda?status_data=archived') ?>">
                    <i class="bi bi-archive-fill me-1"></i> Data Arsip
                    <span class="badge <?= ($currStatusData === 'archived') ? 'bg-white text-secondary' : 'bg-secondary text-white' ?> ms-1"><?= number_format($summary['archived'] ?? 0) ?></span>
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- FILTER CARD (COLLAPSIBLE / ACCORDION) -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2 fw-bold text-dark">
            <i class="bi bi-funnel-fill text-primary"></i>
            <span>Filter & Pencarian Data</span>
        </div>
        <button class="btn btn-sm btn-link text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFilter">
            <i class="bi bi-chevron-down"></i> Tampilkan / Sembunyikan Filter
        </button>
    </div>
    <div class="collapse show" id="collapseFilter">
        <div class="card-body p-4 bg-light bg-opacity-25">
            <form action="<?= base_url('admin/pemuda') ?>" method="GET" id="filterForm">
                <div class="row g-3">
                    <!-- Search Keyword -->
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-semibold text-muted">Cari Nama / No. Reg / Kontak / Sekolah</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                            <input type="text" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="Ketik kata kunci pencarian..." 
                                   value="<?= esc($filters['search'] ?? '') ?>">
                        </div>
                    </div>

                    <!-- Wilayah -->
                    <div class="col-12 col-sm-6 col-md-4">
                        <label class="form-label small fw-semibold text-muted">Wilayah</label>
                        <select name="wilayah_id" id="filterWilayah" class="form-select">
                            <option value="">-- Semua Wilayah --</option>
                            <?php foreach ($wilayahList as $w): ?>
                                <option value="<?= $w['id'] ?>" <?= ($filters['wilayah_id'] ?? '') == $w['id'] ? 'selected' : '' ?>>
                                    <?= esc($w['name']) ?> (<?= esc($w['code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Cabang -->
                    <div class="col-12 col-sm-6 col-md-4">
                        <label class="form-label small fw-semibold text-muted">Cabang</label>
                        <select name="cabang_id" id="filterCabang" class="form-select">
                            <option value="">-- Semua Cabang --</option>
                            <?php foreach ($cabangList as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= ($filters['cabang_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                                    <?= esc($c['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Gender -->
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-semibold text-muted">Jenis Kelamin</label>
                        <select name="gender" class="form-select">
                            <option value="">-- Semua --</option>
                            <option value="L" <?= ($filters['gender'] ?? '') === 'L' ? 'selected' : '' ?>>Laki-laki (L)</option>
                            <option value="P" <?= ($filters['gender'] ?? '') === 'P' ? 'selected' : '' ?>>Perempuan (P)</option>
                        </select>
                    </div>

                    <!-- Status Pernikahan -->
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-semibold text-muted">Status Nikah</label>
                        <select name="marital_status" class="form-select">
                            <option value="">-- Semua --</option>
                            <option value="belum_menikah" <?= ($filters['marital_status'] ?? '') === 'belum_menikah' ? 'selected' : '' ?>>Belum Menikah</option>
                            <option value="sudah_menikah" <?= ($filters['marital_status'] ?? '') === 'sudah_menikah' ? 'selected' : '' ?>>Sudah Menikah</option>
                            <option value="janda" <?= ($filters['marital_status'] ?? '') === 'janda' ? 'selected' : '' ?>>Janda</option>
                            <option value="duda" <?= ($filters['marital_status'] ?? '') === 'duda' ? 'selected' : '' ?>>Duda</option>
                        </select>
                    </div>

                    <!-- Golongan Darah -->
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-semibold text-muted">Gol. Darah</label>
                        <select name="blood_type" class="form-select">
                            <option value="">-- Semua --</option>
                            <option value="A" <?= ($filters['blood_type'] ?? '') === 'A' ? 'selected' : '' ?>>A</option>
                            <option value="B" <?= ($filters['blood_type'] ?? '') === 'B' ? 'selected' : '' ?>>B</option>
                            <option value="AB" <?= ($filters['blood_type'] ?? '') === 'AB' ? 'selected' : '' ?>>AB</option>
                            <option value="O" <?= ($filters['blood_type'] ?? '') === 'O' ? 'selected' : '' ?>>O</option>
                        </select>
                    </div>

                    <!-- Pendidikan -->
                    <div class="col-6 col-md-3">
                        <label class="form-label small fw-semibold text-muted">Pendidikan</label>
                        <select name="education_level_id" class="form-select">
                            <option value="">-- Semua Pendidikan --</option>
                            <?php foreach ($educationLevels as $edu): ?>
                                <option value="<?= $edu['id'] ?>" <?= ($filters['education_level_id'] ?? '') == $edu['id'] ? 'selected' : '' ?>>
                                    <?= esc($edu['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Status Pekerjaan -->
                    <div class="col-6 col-md-3">
                        <label class="form-label small fw-semibold text-muted">Status Pekerjaan</label>
                        <select name="job_status_id" class="form-select">
                            <option value="">-- Semua Pekerjaan --</option>
                            <?php foreach ($jobStatuses as $job): ?>
                                <option value="<?= $job['id'] ?>" <?= ($filters['job_status_id'] ?? '') == $job['id'] ? 'selected' : '' ?>>
                                    <?= esc($job['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Status Verifikasi (Hidden or preserved) -->
                    <input type="hidden" name="status_verifikasi" value="<?= esc($filters['status_verifikasi'] ?? '') ?>">
                    <input type="hidden" name="status_data" value="<?= esc($filters['status_data'] ?? 'active') ?>">

                    <!-- Action Buttons -->
                    <div class="col-12 d-flex justify-content-between align-items-center pt-2">
                        <a href="<?= base_url('admin/pemuda') ?>" class="btn btn-outline-secondary btn-sm px-3 rounded-3">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                        </a>
                        <button type="submit" class="btn btn-primary px-4 rounded-3 shadow-sm">
                            <i class="bi bi-filter me-1"></i> Terapkan Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- DATA TABLE CARD -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
        <div class="fw-bold text-dark">
            <i class="bi bi-table text-primary me-2"></i>Daftar Pemuda
        </div>
        <div class="text-muted small">
            Menampilkan <?= count($pemudaList) ?> data pada halaman ini
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 50px;">No</th>
                        <th>No. Registrasi</th>
                        <th>Nama & Kontak</th>
                        <th>Wilayah & Cabang</th>
                        <th>Demografi</th>
                        <th>Pendidikan & Kerja</th>
                        <th>Status Verifikasi</th>
                        <th>Tgl Daftar</th>
                        <th class="text-end pe-3" style="min-width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pemudaList)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted mb-2"><i class="bi bi-search fs-1"></i></div>
                                <h6 class="fw-bold text-dark">Tidak ada data pemuda yang cocok</h6>
                                <p class="text-muted small mb-0">Coba ubah kata kunci atau atur ulang filter pencarian Anda.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $page = (int) ($pager->getCurrentPage('pemuda') ?? 1);
                        $no = (($page - 1) * $perPage) + 1;
                        foreach ($pemudaList as $p): 
                        ?>
                            <tr>
                                <td class="ps-3 text-muted"><?= $no++ ?></td>
                                <td>
                                    <a href="<?= base_url('admin/pemuda/detail/' . $p['id']) ?>" class="fw-bold text-decoration-none text-primary">
                                        <?= esc($p['registration_number']) ?>
                                    </a>
                                    <?php if ($p['status_data'] === 'archived'): ?>
                                        <span class="badge bg-secondary" style="font-size: 0.65rem;">Arsip</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?= esc($p['name']) ?></div>
                                    <div class="text-muted" style="font-size: 0.76rem;">
                                        <?php if (!empty($p['phone'])): ?>
                                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', (str_starts_with($p['phone'], '0') ? ('62' . substr($p['phone'], 1)) : $p['phone'])) ?>" 
                                               target="_blank" 
                                               class="text-decoration-none text-success fw-medium">
                                                <i class="bi bi-whatsapp"></i> <?= esc($p['phone']) ?>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($p['email'])): ?>
                                            &bull; <?= esc($p['email']) ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= esc($p['cabang_name']) ?></div>
                                    <div class="text-muted" style="font-size: 0.76rem;"><?= esc($p['wilayah_name']) ?></div>
                                </td>
                                <td>
                                    <div>
                                        <?= $p['gender'] === 'L' ? '<span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">L</span>' : '<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">P</span>' ?>
                                        <?php if (!empty($p['blood_type'])): ?>
                                            <span class="badge bg-light text-dark border ms-1" style="font-size: 0.7rem;"><?= esc($p['blood_type']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-muted mt-1" style="font-size: 0.72rem;">
                                        <?= ucwords(str_replace('_', ' ', $p['marital_status'] ?? 'belum_menikah')) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark small"><?= esc($p['education_level_name'] ?? '-') ?></div>
                                    <div class="text-muted" style="font-size: 0.72rem;"><?= esc($p['job_status_name'] ?? '-') ?></div>
                                </td>
                                <td>
                                    <!-- Status Badge with dropdown change -->
                                    <div class="dropdown">
                                        <button class="btn btn-sm dropdown-toggle border-0 py-1 px-2 rounded-pill shadow-none <?php 
                                            if ($p['status_verifikasi'] === 'verified') echo 'badge-verif-verified';
                                            elseif ($p['status_verifikasi'] === 'rejected') echo 'badge-verif-rejected';
                                            else echo 'badge-verif-pending';
                                        ?>" type="button" data-bs-toggle="dropdown" style="font-size: 0.78rem; font-weight: 600;">
                                            <?php 
                                                if ($p['status_verifikasi'] === 'verified') echo '<i class="bi bi-check-circle me-1"></i> Terverifikasi';
                                                elseif ($p['status_verifikasi'] === 'rejected') echo '<i class="bi bi-x-circle me-1"></i> Ditolak';
                                                else echo '<i class="bi bi-clock me-1"></i> Menunggu';
                                            ?>
                                        </button>
                                        <ul class="dropdown-menu shadow border-0 rounded-3 p-1">
                                            <li>
                                                <form action="<?= base_url('admin/pemuda/verifikasi/' . $p['id']) ?>" method="POST">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="status" value="verified">
                                                    <button type="submit" class="dropdown-item py-1 text-success small rounded-2">
                                                        <i class="bi bi-check-circle-fill me-2"></i> Verifikasi (Valid)
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="<?= base_url('admin/pemuda/verifikasi/' . $p['id']) ?>" method="POST">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="dropdown-item py-1 text-danger small rounded-2">
                                                        <i class="bi bi-x-circle-fill me-2"></i> Tolak Data
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="<?= base_url('admin/pemuda/verifikasi/' . $p['id']) ?>" method="POST">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="status" value="pending">
                                                    <button type="submit" class="dropdown-item py-1 text-warning small rounded-2">
                                                        <i class="bi bi-clock-history me-2"></i> Set Menunggu
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                                <td class="text-muted small">
                                    <?= date('d/m/y H:i', strtotime($p['created_at'])) ?>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <!-- Detail Button -->
                                        <a href="<?= base_url('admin/pemuda/detail/' . $p['id']) ?>" 
                                           class="btn btn-outline-secondary btn-sm" 
                                           title="Lihat Detail Profil">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <!-- Edit Button -->
                                        <a href="<?= base_url('admin/pemuda/edit/' . $p['id']) ?>" 
                                           class="btn btn-outline-primary btn-sm" 
                                           title="Edit Data">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <!-- Print Button -->
                                        <a href="<?= base_url('admin/pemuda/cetak/' . $p['id']) ?>" 
                                           target="_blank"
                                           class="btn btn-outline-info btn-sm" 
                                           title="Cetak Biodata">
                                            <i class="bi bi-printer"></i>
                                        </a>

                                        <!-- More Dropdown -->
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                                                <span class="visually-hidden">Toggle Dropdown</span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 p-1">
                                                <li>
                                                    <form action="<?= base_url('admin/pemuda/archive/' . $p['id']) ?>" method="POST">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="dropdown-item py-2 small rounded-2">
                                                            <i class="bi bi-archive me-2 text-secondary"></i>
                                                            <?= $p['status_data'] === 'archived' ? 'Aktifkan Kembali' : 'Arsipkan Data' ?>
                                                        </button>
                                                    </form>
                                                </li>
                                                <?php if (session()->get('role') === 'superadmin'): ?>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button type="button" 
                                                                class="dropdown-item py-2 small text-danger rounded-2 btn-delete" 
                                                                data-id="<?= $p['id'] ?>" 
                                                                data-name="<?= esc($p['name']) ?>">
                                                            <i class="bi bi-trash me-2"></i> Hapus Permanen
                                                        </button>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>
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
        <div class="card-footer bg-white border-top py-3 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
            <div class="small text-muted">
                Halaman <?= $pager->getCurrentPage('pemuda') ?> dari <?= $pager->getPageCount('pemuda') ?>
            </div>
            <div>
                <?= $pager->links('pemuda', 'default_full') ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- DELETE MODAL (SUPERADMIN ONLY) -->
<?php if (session()->get('role') === 'superadmin'): ?>
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-body p-4 text-center">
                <div class="text-danger mb-3">
                    <i class="bi bi-exclamation-triangle fs-1"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Konfirmasi Hapus Permanen</h5>
                <p class="text-muted small mb-4">
                    Apakah Anda yakin ingin menghapus data pemuda <strong id="deletePemudaName" class="text-dark"></strong> secara permanen? Seluruh relasi alamat, pendidikan, pekerjaan, dan organisasi akan dihapus. Tindakan ini tidak dapat dibatalkan.
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST" action="">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-danger rounded-3 px-4 shadow-sm">
                            <i class="bi bi-trash me-1"></i> Hapus Sekarang
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
    document.addEventListener("DOMContentLoaded", function () {
        // Dynamic Cabang dropdown based on Wilayah
        const filterWilayah = document.getElementById('filterWilayah');
        const filterCabang  = document.getElementById('filterCabang');

        if (filterWilayah && filterCabang) {
            filterWilayah.addEventListener('change', function () {
                const wilayahId = this.value;
                filterCabang.innerHTML = '<option value="">-- Memuat Cabang... --</option>';

                if (!wilayahId) {
                    filterCabang.innerHTML = '<option value="">-- Semua Cabang --</option>';
                    return;
                }

                fetch('<?= base_url('admin/ajax/cabang/') ?>/' + wilayahId)
                    .then(res => res.json())
                    .then(data => {
                        let options = '<option value="">-- Semua Cabang --</option>';
                        data.forEach(c => {
                            options += `<option value="${c.id}">${c.name}</option>`;
                        });
                        filterCabang.innerHTML = options;
                    })
                    .catch(() => {
                        filterCabang.innerHTML = '<option value="">-- Gagal memuat cabang --</option>';
                    });
            });
        }

        // Delete confirmation modal handler
        const deleteButtons = document.querySelectorAll('.btn-delete');
        const deleteModalEl = document.getElementById('deleteModal');
        const deleteForm    = document.getElementById('deleteForm');
        const deleteNameEl  = document.getElementById('deletePemudaName');

        if (deleteButtons.length && deleteModalEl) {
            const deleteModal = new bootstrap.Modal(deleteModalEl);
            deleteButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    const id   = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    deleteNameEl.textContent = name;
                    deleteForm.action = '<?= base_url('admin/pemuda/delete/') ?>/' + id;
                    deleteModal.show();
                });
            });
        }
    });
</script>
<?= $this->endSection() ?>
