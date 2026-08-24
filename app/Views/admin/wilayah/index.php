<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Master Wilayah</h4>
        <p class="text-muted small mb-0">Kelola daftar wilayah koordinasi pendataan pemuda Kabupaten Sragen.</p>
    </div>
    <div>
        <button class="btn btn-primary rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addWilayahModal">
            <i class="bi bi-plus-circle me-1"></i> Tambah Wilayah
        </button>
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
                        <th>Nama Wilayah</th>
                        <th>Deskripsi / Cakupan</th>
                        <th class="text-center">Total Cabang</th>
                        <th class="text-center">Total Pemuda</th>
                        <th class="text-end pe-3" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($wilayahList)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada data wilayah.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($wilayahList as $w): ?>
                            <tr>
                                <td class="ps-3 text-muted"><?= $w['id'] ?></td>
                                <td><span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 fw-bold"><?= esc($w['code']) ?></span></td>
                                <td class="fw-bold text-dark"><?= esc($w['name']) ?></td>
                                <td class="text-muted small"><?= esc($w['description'] ?: '-') ?></td>
                                <td class="text-center">
                                    <a href="<?= base_url('admin/cabang?wilayah_id=' . $w['id']) ?>" class="badge bg-light text-dark border text-decoration-none px-3 py-1">
                                        <?= number_format($w['total_cabang'] ?? 0) ?> Cabang
                                    </a>
                                </td>
                                <td class="text-center">
                                    <a href="<?= base_url('admin/pemuda?wilayah_id=' . $w['id']) ?>" class="badge bg-success-subtle text-success border border-success-subtle text-decoration-none px-3 py-1">
                                        <?= number_format($w['total_pemuda'] ?? 0) ?> Pemuda
                                    </a>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" 
                                                class="btn btn-outline-primary btn-edit-wilayah" 
                                                data-id="<?= $w['id'] ?>"
                                                data-code="<?= esc($w['code']) ?>"
                                                data-name="<?= esc($w['name']) ?>"
                                                data-desc="<?= esc($w['description'] ?? '') ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-outline-danger btn-delete-wilayah" 
                                                data-id="<?= $w['id'] ?>"
                                                data-name="<?= esc($w['name']) ?>">
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
</div>

<!-- MODAL ADD WILAYAH -->
<div class="modal fade" id="addWilayahModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-dark mb-0">Tambah Wilayah Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/wilayah/simpan') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Kode Wilayah <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control text-uppercase" placeholder="Contoh: W01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Nama Wilayah <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Wilayah 1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Deskripsi / Kecamatan Cakupan</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Contoh: Cakupan kecamatan Gesi, Jenar, Tanon..."></textarea>
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

<!-- MODAL EDIT WILAYAH -->
<div class="modal fade" id="editWilayahModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-dark mb-0">Edit Wilayah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editWilayahForm" action="" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Kode Wilayah <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="editCode" class="form-control text-uppercase" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Nama Wilayah <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Deskripsi / Cakupan</label>
                        <textarea name="description" id="editDesc" class="form-control" rows="3"></textarea>
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

<!-- MODAL DELETE WILAYAH -->
<div class="modal fade" id="deleteWilayahModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-body p-4 text-center">
                <i class="bi bi-exclamation-triangle text-danger fs-1 mb-2 d-block"></i>
                <h5 class="fw-bold text-dark mb-2">Hapus Wilayah</h5>
                <p class="text-muted small mb-4">Apakah Anda yakin ingin menghapus wilayah <strong id="deleteWilayahName"></strong>?</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteWilayahForm" method="POST" action="">
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
        const editButtons = document.querySelectorAll('.btn-edit-wilayah');
        const editModalEl = document.getElementById('editWilayahModal');
        const editForm    = document.getElementById('editWilayahForm');
        const editCode    = document.getElementById('editCode');
        const editName    = document.getElementById('editName');
        const editDesc    = document.getElementById('editDesc');

        if (editButtons.length && editModalEl) {
            const editModal = new bootstrap.Modal(editModalEl);
            editButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    const id   = this.getAttribute('data-id');
                    const code = this.getAttribute('data-code');
                    const name = this.getAttribute('data-name');
                    const desc = this.getAttribute('data-desc');

                    editCode.value = code;
                    editName.value = name;
                    editDesc.value = desc;
                    editForm.action = '<?= base_url('admin/wilayah/update/') ?>/' + id;
                    editModal.show();
                });
            });
        }

        // Delete modal handler
        const delButtons   = document.querySelectorAll('.btn-delete-wilayah');
        const delModalEl   = document.getElementById('deleteWilayahModal');
        const delForm      = document.getElementById('deleteWilayahForm');
        const delNameEl    = document.getElementById('deleteWilayahName');

        if (delButtons.length && delModalEl) {
            const delModal = new bootstrap.Modal(delModalEl);
            delButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    const id   = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    delNameEl.textContent = name;
                    delForm.action = '<?= base_url('admin/wilayah/delete/') ?>/' + id;
                    delModal.show();
                });
            });
        }
    });
</script>
<?= $this->endSection() ?>
