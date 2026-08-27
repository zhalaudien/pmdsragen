<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-3">
    <div>
        <h4 class="font-weight-bold text-dark mb-1">Master Cabang</h4>
        <p class="text-muted text-xs mb-0">Kelola daftar cabang pemuda pada setiap wilayah se-Kabupaten Sragen.</p>
    </div>
    <div class="mt-2 mt-sm-0">
        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addCabangModal">
            <i class="fas fa-plus-circle mr-1"></i> Tambah Cabang
        </button>
    </div>
</div>

<!-- FILTER CARD -->
<div class="card card-default card-outline shadow-sm mb-3">
    <div class="card-body p-2">
        <form action="<?= base_url('admin/cabang') ?>" method="GET" class="row align-items-center">
            <div class="col-12 col-md-5 mb-2 mb-md-0">
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama atau kode cabang..." value="<?= esc($search ?? '') ?>">
                </div>
            </div>
            <div class="col-12 col-md-4 mb-2 mb-md-0">
                <select name="wilayah_id" class="form-control form-control-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Wilayah --</option>
                    <?php foreach ($wilayahList as $w): ?>
                        <option value="<?= $w['id'] ?>" <?= ($selectedW == $w['id']) ? 'selected' : '' ?>>
                            <?= esc($w['name']) ?> (<?= esc($w['code']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-3 d-flex">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1 mr-1">Cari</button>
                <?php if (!empty($search) || !empty($selectedW)): ?>
                    <a href="<?= base_url('admin/cabang') ?>" class="btn btn-default btn-sm">Reset</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="card card-primary card-outline shadow-sm">
    <div class="card-header border-0 d-flex justify-content-between align-items-center">
        <h3 class="card-title font-weight-bold text-sm">
            <i class="fas fa-sitemap mr-1 text-primary"></i> Daftar Cabang Pemuda
        </h3>
        <div class="card-tools text-muted text-xs">
            Menampilkan <?= count($cabangList) ?> cabang
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped table-vcenter mb-0" style="font-size: 0.88rem;">
                <thead class="thead-light">
                    <tr>
                        <th class="pl-3" style="width: 50px;">ID</th>
                        <th>Kode</th>
                        <th>Nama Cabang</th>
                        <th>Wilayah</th>
                        <th>Keterangan</th>
                        <th class="text-center">Total Pemuda</th>
                        <th class="text-right pr-3" style="width: 110px;">Aksi</th>
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
                                <td class="pl-3 text-muted"><?= $c['id'] ?></td>
                                <td><span class="badge badge-light border"><?= esc($c['code'] ?: '-') ?></span></td>
                                <td class="font-weight-bold text-dark"><?= esc($c['name']) ?></td>
                                <td><span class="badge badge-primary px-2 py-1"><?= esc($c['wilayah_name']) ?></span></td>
                                <td class="text-muted text-xs"><?= esc($c['description'] ?: '-') ?></td>
                                <td class="text-center">
                                    <a href="<?= base_url('admin/pemuda?cabang_id=' . $c['id']) ?>" class="badge badge-success px-2 py-1 text-decoration-none">
                                        <i class="fas fa-users mr-1"></i> <?= number_format($c['total_pemuda'] ?? 0) ?> Pemuda
                                    </a>
                                </td>
                                <td class="text-right pr-3">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" 
                                                class="btn btn-primary btn-xs btn-edit-cabang" 
                                                data-id="<?= $c['id'] ?>"
                                                data-wid="<?= $c['wilayah_id'] ?>"
                                                data-code="<?= esc($c['code'] ?? '') ?>"
                                                data-name="<?= esc($c['name']) ?>"
                                                data-desc="<?= esc($c['description'] ?? '') ?>"
                                                title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-danger btn-xs btn-delete-cabang" 
                                                data-id="<?= $c['id'] ?>"
                                                data-name="<?= esc($c['name']) ?>"
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
    
    <?php if ($pager->getPageCount('cabang') > 1): ?>
        <div class="card-footer bg-white border-top py-2 d-flex justify-content-between align-items-center">
            <div class="text-xs text-muted">Halaman <?= $pager->getCurrentPage('cabang') ?> dari <?= $pager->getPageCount('cabang') ?></div>
            <div><?= $pager->links('cabang', 'default_full') ?></div>
        </div>
    <?php endif; ?>
</div>

<!-- MODAL ADD CABANG -->
<div class="modal fade" id="addCabangModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title font-weight-bold text-sm">
                    <i class="fas fa-plus-circle mr-1"></i> Tambah Cabang Baru
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('admin/cabang/simpan') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-3">
                    <div class="form-group mb-2">
                        <label class="text-xs text-muted font-weight-bold">Wilayah <span class="text-danger">*</span></label>
                        <select name="wilayah_id" class="form-control form-control-sm" required>
                            <option value="">-- Pilih Wilayah --</option>
                            <?php foreach ($wilayahList as $w): ?>
                                <option value="<?= $w['id'] ?>"><?= esc($w['name']) ?> (<?= esc($w['code']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs text-muted font-weight-bold">Kode Cabang</label>
                        <input type="text" name="code" class="form-control form-control-sm" placeholder="Contoh: CBG-001">
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs text-muted font-weight-bold">Nama Cabang <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-sm" placeholder="Contoh: Gesi / Mondokan 1" required>
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-xs text-muted font-weight-bold">Deskripsi</label>
                        <textarea name="description" class="form-control form-control-sm" rows="2"></textarea>
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

<!-- MODAL EDIT CABANG -->
<div class="modal fade" id="editCabangModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title font-weight-bold text-sm">
                    <i class="fas fa-edit mr-1"></i> Edit Cabang
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editCabangForm" action="" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-3">
                    <div class="form-group mb-2">
                        <label class="text-xs text-muted font-weight-bold">Wilayah <span class="text-danger">*</span></label>
                        <select name="wilayah_id" id="editCabangWid" class="form-control form-control-sm" required>
                            <option value="">-- Pilih Wilayah --</option>
                            <?php foreach ($wilayahList as $w): ?>
                                <option value="<?= $w['id'] ?>"><?= esc($w['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs text-muted font-weight-bold">Kode Cabang</label>
                        <input type="text" name="code" id="editCabangCode" class="form-control form-control-sm">
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs text-muted font-weight-bold">Nama Cabang <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editCabangName" class="form-control form-control-sm" required>
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-xs text-muted font-weight-bold">Deskripsi</label>
                        <textarea name="description" id="editCabangDesc" class="form-control form-control-sm" rows="2"></textarea>
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

<!-- MODAL DELETE CABANG -->
<div class="modal fade" id="deleteCabangModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white py-2">
                <h5 class="modal-title font-weight-bold text-sm">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Hapus Cabang
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="fas fa-trash-alt text-danger fa-3x mb-3 d-block"></i>
                <h6 class="font-weight-bold text-dark mb-2">Konfirmasi Hapus</h6>
                <p class="text-muted text-xs mb-4">Apakah Anda yakin ingin menghapus cabang <strong id="deleteCabangName" class="text-dark"></strong>?</p>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-default btn-sm px-3 mr-2" data-dismiss="modal">Batal</button>
                    <form id="deleteCabangForm" method="POST" action="">
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
        $(document).on('click', '.btn-edit-cabang', function () {
            const id   = $(this).data('id');
            const wid  = $(this).data('wid');
            const code = $(this).data('code');
            const name = $(this).data('name');
            const desc = $(this).data('desc');

            $('#editCabangWid').val(wid);
            $('#editCabangCode').val(code);
            $('#editCabangName').val(name);
            $('#editCabangDesc').val(desc);
            $('#editCabangForm').attr('action', '<?= base_url('admin/cabang/update/') ?>/' + id);
            $('#editCabangModal').modal('show');
        });

        // Delete modal handler
        $(document).on('click', '.btn-delete-cabang', function () {
            const id   = $(this).data('id');
            const name = $(this).data('name');
            $('#deleteCabangName').text(name);
            $('#deleteCabangForm').attr('action', '<?= base_url('admin/cabang/delete/') ?>/' + id);
            $('#deleteCabangModal').modal('show');
        });
    });
</script>
<?= $this->endSection() ?>
