<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Master Cabang</h4>
        <p class="text-muted small mb-0">Kelola daftar cabang pemuda pada setiap wilayah se-Kabupaten Sragen.</p>
    </div>
    <div>
        <button class="btn btn-primary rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addCabangModal">
            <i class="bi bi-plus-circle me-1"></i> Tambah Cabang
        </button>
    </div>
</div>

<!-- FILTER CARD -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="<?= base_url('admin/cabang') ?>" method="GET" class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama atau kode cabang..." value="<?= esc($search ?? '') ?>">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <select name="wilayah_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Semua Wilayah --</option>
                    <?php foreach ($wilayahList as $w): ?>
                        <option value="<?= $w['id'] ?>" <?= ($selectedW == $w['id']) ? 'selected' : '' ?>>
                            <?= esc($w['name']) ?> (<?= esc($w['code']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1 rounded-3">Cari</button>
                <?php if (!empty($search) || !empty($selectedW)): ?>
                    <a href="<?= base_url('admin/cabang') ?>" class="btn btn-outline-secondary rounded-3">Reset</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 60px;">ID</th>
                        <th>Kode</th>
                        <th>Nama Cabang</th>
                        <th>Wilayah</th>
                        <th>Keterangan</th>
                        <th class="text-center">Total Pemuda</th>
                        <th class="text-end pe-3" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($cabangList)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada data cabang.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($cabangList as $c): ?>
                            <tr>
                                <td class="ps-3 text-muted"><?= $c['id'] ?></td>
                                <td><span class="badge bg-light text-dark border"><?= esc($c['code'] ?: '-') ?></span></td>
                                <td class="fw-bold text-dark"><?= esc($c['name']) ?></td>
                                <td><span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1"><?= esc($c['wilayah_name']) ?></span></td>
                                <td class="text-muted small"><?= esc($c['description'] ?: '-') ?></td>
                                <td class="text-center">
                                    <a href="<?= base_url('admin/pemuda?cabang_id=' . $c['id']) ?>" class="badge bg-success-subtle text-success border border-success-subtle text-decoration-none px-3 py-1">
                                        <?= number_format($c['total_pemuda'] ?? 0) ?> Pemuda
                                    </a>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" 
                                                class="btn btn-outline-primary btn-edit-cabang" 
                                                data-id="<?= $c['id'] ?>"
                                                data-wid="<?= $c['wilayah_id'] ?>"
                                                data-code="<?= esc($c['code'] ?? '') ?>"
                                                data-name="<?= esc($c['name']) ?>"
                                                data-desc="<?= esc($c['description'] ?? '') ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-outline-danger btn-delete-cabang" 
                                                data-id="<?= $c['id'] ?>"
                                                data-name="<?= esc($c['name']) ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php if ($pager->getPageCount('cabang') > 1): ?>
        <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
            <div class="small text-muted">Halaman <?= $pager->getCurrentPage('cabang') ?> dari <?= $pager->getPageCount('cabang') ?></div>
            <div><?= $pager->links('cabang', 'default_full') ?></div>
        </div>
    <?php endif; ?>
</div>

<!-- MODAL ADD CABANG -->
<div class="modal fade" id="addCabangModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-dark mb-0">Tambah Cabang Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/cabang/simpan') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Wilayah <span class="text-danger">*</span></label>
                        <select name="wilayah_id" class="form-select" required>
                            <option value="">-- Pilih Wilayah --</option>
                            <?php foreach ($wilayahList as $w): ?>
                                <option value="<?= $w['id'] ?>"><?= esc($w['name']) ?> (<?= esc($w['code']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Kode Cabang</label>
                        <input type="text" name="code" class="form-control" placeholder="Contoh: CBG-001">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Nama Cabang <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Gesi / Mondokan 1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 shadow-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT CABANG -->
<div class="modal fade" id="editCabangModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-dark mb-0">Edit Cabang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCabangForm" action="" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Wilayah <span class="text-danger">*</span></label>
                        <select name="wilayah_id" id="editCabangWid" class="form-select" required>
                            <option value="">-- Pilih Wilayah --</option>
                            <?php foreach ($wilayahList as $w): ?>
                                <option value="<?= $w['id'] ?>"><?= esc($w['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Kode Cabang</label>
                        <input type="text" name="code" id="editCabangCode" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Nama Cabang <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editCabangName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Deskripsi</label>
                        <textarea name="description" id="editCabangDesc" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL DELETE CABANG -->
<div class="modal fade" id="deleteCabangModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-body p-4 text-center">
                <i class="bi bi-exclamation-triangle text-danger fs-1 mb-2 d-block"></i>
                <h5 class="fw-bold text-dark mb-2">Hapus Cabang</h5>
                <p class="text-muted small mb-4">Apakah Anda yakin ingin menghapus cabang <strong id="deleteCabangName"></strong>?</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteCabangForm" method="POST" action="">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-danger rounded-3 px-4 shadow-sm">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Edit modal handler
        const editButtons = document.querySelectorAll('.btn-edit-cabang');
        const editModalEl = document.getElementById('editCabangModal');
        const editForm    = document.getElementById('editCabangForm');
        const editWid     = document.getElementById('editCabangWid');
        const editCode    = document.getElementById('editCabangCode');
        const editName    = document.getElementById('editCabangName');
        const editDesc    = document.getElementById('editCabangDesc');

        if (editButtons.length && editModalEl) {
            const editModal = new bootstrap.Modal(editModalEl);
            editButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    const id   = this.getAttribute('data-id');
                    const wid  = this.getAttribute('data-wid');
                    const code = this.getAttribute('data-code');
                    const name = this.getAttribute('data-name');
                    const desc = this.getAttribute('data-desc');

                    editWid.value  = wid;
                    editCode.value = code;
                    editName.value = name;
                    editDesc.value = desc;
                    editForm.action = '<?= base_url('admin/cabang/update/') ?>/' + id;
                    editModal.show();
                });
            });
        }

        // Delete modal handler
        const delButtons = document.querySelectorAll('.btn-delete-cabang');
        const delModalEl = document.getElementById('deleteCabangModal');
        const delForm    = document.getElementById('deleteCabangForm');
        const delNameEl  = document.getElementById('deleteCabangName');

        if (delButtons.length && delModalEl) {
            const delModal = new bootstrap.Modal(delModalEl);
            delButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    const id   = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    delNameEl.textContent = name;
                    delForm.action = '<?= base_url('admin/cabang/delete/') ?>/' + id;
                    delModal.show();
                });
            });
        }
    });
</script>
<?= $this->endSection() ?>
