<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-3">
    <div>
        <h4 class="font-weight-bold text-dark mb-1">Master Wilayah</h4>
        <p class="text-muted text-xs mb-0">Kelola daftar wilayah koordinasi pendataan pemuda Kabupaten Sragen.</p>
    </div>
    <div class="mt-2 mt-sm-0">
        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addWilayahModal">
            <i class="fas fa-plus-circle mr-1"></i> Tambah Wilayah
        </button>
    </div>
</div>

<div class="card card-primary card-outline shadow-sm">
    <div class="card-header border-0 d-flex justify-content-between align-items-center">
        <h3 class="card-title font-weight-bold text-sm">
            <i class="fas fa-map-marked-alt mr-1 text-primary"></i> Daftar Wilayah Koordinasi
        </h3>
        <div class="card-tools text-muted text-xs">
            Total: <?= count($wilayahList) ?> Wilayah
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped table-vcenter mb-0" style="font-size: 0.88rem;">
                <thead class="thead-light">
                    <tr>
                        <th class="pl-3" style="width: 50px;">ID</th>
                        <th>Kode</th>
                        <th>Nama Wilayah</th>
                        <th>Deskripsi / Cakupan</th>
                        <th class="text-center">Total Cabang</th>
                        <th class="text-center">Total Pemuda</th>
                        <th class="text-right pr-3" style="width: 110px;">Aksi</th>
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
                                <td class="pl-3 text-muted"><?= $w['id'] ?></td>
                                <td><span class="badge badge-primary px-2 py-1 font-weight-bold"><?= esc($w['code']) ?></span></td>
                                <td class="font-weight-bold text-dark"><?= esc($w['name']) ?></td>
                                <td class="text-muted text-xs"><?= esc($w['description'] ?: '-') ?></td>
                                <td class="text-center">
                                    <a href="<?= base_url('admin/cabang?wilayah_id=' . $w['id']) ?>" class="badge badge-light border text-decoration-none px-2 py-1">
                                        <i class="fas fa-sitemap mr-1 text-info"></i> <?= number_format($w['total_cabang'] ?? 0) ?> Cabang
                                    </a>
                                </td>
                                <td class="text-center">
                                    <a href="<?= base_url('admin/pemuda?wilayah_id=' . $w['id']) ?>" class="badge badge-success text-decoration-none px-2 py-1">
                                        <i class="fas fa-users mr-1"></i> <?= number_format($w['total_pemuda'] ?? 0) ?> Pemuda
                                    </a>
                                </td>
                                <td class="text-right pr-3">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" 
                                                class="btn btn-primary btn-xs btn-edit-wilayah" 
                                                data-id="<?= $w['id'] ?>"
                                                data-code="<?= esc($w['code']) ?>"
                                                data-name="<?= esc($w['name']) ?>"
                                                data-desc="<?= esc($w['description'] ?? '') ?>"
                                                title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-danger btn-xs btn-delete-wilayah" 
                                                data-id="<?= $w['id'] ?>"
                                                data-name="<?= esc($w['name']) ?>"
                                                title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
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
<div class="modal fade" id="addWilayahModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title font-weight-bold text-sm">
                    <i class="fas fa-plus-circle mr-1"></i> Tambah Wilayah Baru
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('admin/wilayah/simpan') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-3">
                    <div class="form-group mb-2">
                        <label class="text-xs text-muted font-weight-bold">Kode Wilayah <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control form-control-sm text-uppercase" placeholder="Contoh: W01" required>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs text-muted font-weight-bold">Nama Wilayah <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-sm" placeholder="Contoh: Wilayah 1" required>
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-xs text-muted font-weight-bold">Deskripsi / Kecamatan Cakupan</label>
                        <textarea name="description" class="form-control form-control-sm" rows="3" placeholder="Contoh: Cakupan kecamatan Gesi, Jenar, Tanon..."></textarea>
                    </div>
                </div>
                <div class="modal-footer py-2 bg-light">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT WILAYAH -->
<div class="modal fade" id="editWilayahModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title font-weight-bold text-sm">
                    <i class="fas fa-edit mr-1"></i> Edit Wilayah
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editWilayahForm" action="" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-3">
                    <div class="form-group mb-2">
                        <label class="text-xs text-muted font-weight-bold">Kode Wilayah <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="editCode" class="form-control form-control-sm text-uppercase" required>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs text-muted font-weight-bold">Nama Wilayah <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editName" class="form-control form-control-sm" required>
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-xs text-muted font-weight-bold">Deskripsi / Cakupan</label>
                        <textarea name="description" id="editDesc" class="form-control form-control-sm" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer py-2 bg-light">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL DELETE WILAYAH -->
<div class="modal fade" id="deleteWilayahModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white py-2">
                <h5 class="modal-title font-weight-bold text-sm">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Hapus Wilayah
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="fas fa-trash-alt text-danger fa-3x mb-3 d-block"></i>
                <h6 class="font-weight-bold text-dark mb-2">Konfirmasi Hapus</h6>
                <p class="text-muted text-xs mb-4">Apakah Anda yakin ingin menghapus wilayah <strong id="deleteWilayahName" class="text-dark"></strong>?</p>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-default btn-sm px-3 mr-2" data-dismiss="modal">Batal</button>
                    <form id="deleteWilayahForm" method="POST" action="">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-danger btn-sm px-4">
                            <i class="fas fa-trash mr-1"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function () {
        // Edit modal handler
        $(document).on('click', '.btn-edit-wilayah', function () {
            const id   = $(this).data('id');
            const code = $(this).data('code');
            const name = $(this).data('name');
            const desc = $(this).data('desc');

            $('#editCode').val(code);
            $('#editName').val(name);
            $('#editDesc').val(desc);
            $('#editWilayahForm').attr('action', '<?= base_url('admin/wilayah/update/') ?>/' + id);
            $('#editWilayahModal').modal('show');
        });

        // Delete modal handler
        $(document).on('click', '.btn-delete-wilayah', function () {
            const id   = $(this).data('id');
            const name = $(this).data('name');
            $('#deleteWilayahName').text(name);
            $('#deleteWilayahForm').attr('action', '<?= base_url('admin/wilayah/delete/') ?>/' + id);
            $('#deleteWilayahModal').modal('show');
        });
    });
</script>
<?= $this->endSection() ?>
