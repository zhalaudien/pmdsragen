<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-3">
    <div>
        <h4 class="font-weight-bold text-dark mb-1">Manajemen Pengguna &amp; Admin</h4>
        <p class="text-muted text-xs mb-0">Kelola akun administrator dengan hak akses bertingkat (Superadmin, Admin Wilayah, Admin Cabang).</p>
    </div>
    <div class="mt-2 mt-sm-0">
        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addUserModal">
            <i class="fas fa-user-plus mr-1"></i> Tambah Pengguna
        </button>
    </div>
</div>

<div class="card card-default card-outline shadow-sm mb-3">
    <div class="card-body p-2">
        <form action="<?= base_url('admin/users') ?>" method="GET" class="row align-items-center">
            <div class="col-12 col-md-6 mb-2 mb-md-0">
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama, username, email..." value="<?= esc($search ?? '') ?>">
                </div>
            </div>
            <div class="col-12 col-md-4 mb-2 mb-md-0">
                <select name="role_id" class="form-control form-control-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Role --</option>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= $r['id'] ?>" <?= ($selectedRole == $r['id']) ? 'selected' : '' ?>>
                            <?= esc(ucwords(str_replace('_', ' ', $r['name']))) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1 mr-1">Filter</button>
                <?php if (!empty($search) || !empty($selectedRole)): ?>
                    <a href="<?= base_url('admin/users') ?>" class="btn btn-default btn-sm">Reset</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="card card-primary card-outline shadow-sm">
    <div class="card-header border-0 d-flex justify-content-between align-items-center">
        <h3 class="card-title font-weight-bold text-sm">
            <i class="fas fa-user-shield mr-1 text-primary"></i> Daftar Pengguna Sistem
        </h3>
        <div class="card-tools text-muted text-xs">
            Total: <?= count($users) ?> Pengguna
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped table-vcenter mb-0" style="font-size: 0.88rem;">
                <thead class="thead-light">
                    <tr>
                        <th class="pl-3" style="width: 40px;">ID</th>
                        <th>Nama Pengguna</th>
                        <th>Username &amp; Email</th>
                        <th>Role / Hak Akses</th>
                        <th>Scope Wilayah &amp; Cabang</th>
                        <th>Status</th>
                        <th>Login Terakhir</th>
                        <th class="text-right pr-3" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Belum ada data pengguna.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td class="pl-3 text-muted"><?= $u['id'] ?></td>
                                <td>
                                    <div class="font-weight-bold text-dark"><?= esc($u['name']) ?></div>
                                </td>
                                <td>
                                    <div class="font-weight-bold text-dark"><i class="fas fa-user mr-1 text-muted text-xs"></i><?= esc($u['username']) ?></div>
                                    <div class="text-muted text-xs"><?= esc($u['email']) ?></div>
                                </td>
                                <td>
                                    <?php if ($u['role_name'] === 'superadmin'): ?>
                                        <span class="badge badge-danger px-2 py-1">Superadmin</span>
                                    <?php elseif ($u['role_name'] === 'admin_wilayah'): ?>
                                        <span class="badge badge-info px-2 py-1">Admin Wilayah</span>
                                    <?php else: ?>
                                        <span class="badge badge-success px-2 py-1">Admin Cabang</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($u['role_name'] === 'superadmin'): ?>
                                        <span class="text-muted text-xs"><i class="fas fa-globe text-purple mr-1"></i> Akses Global</span>
                                    <?php elseif ($u['role_name'] === 'admin_wilayah'): ?>
                                        <span class="font-weight-semibold text-primary text-xs"><i class="fas fa-map-marker-alt mr-1"></i> <?= esc($u['wilayah_name'] ?: 'Wilayah ' . $u['wilayah_id']) ?></span>
                                    <?php else: ?>
                                        <span class="font-weight-semibold text-success text-xs"><i class="fas fa-sitemap mr-1"></i> <?= esc($u['cabang_name'] ?: 'Cabang ' . $u['cabang_id']) ?> (<?= esc($u['wilayah_name'] ?? '') ?>)</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $u['status'] == 1 ? '<span class="badge badge-success px-2 py-1">Aktif</span>' : '<span class="badge badge-danger px-2 py-1">Nonaktif</span>' ?>
                                </td>
                                <td class="text-muted text-xs">
                                    <?= $u['last_login'] ? date('d/m/Y H:i', strtotime($u['last_login'])) : '-' ?>
                                </td>
                                <td class="text-right pr-3">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" 
                                                class="btn btn-primary btn-xs btn-edit-user" 
                                                data-id="<?= $u['id'] ?>"
                                                data-name="<?= esc($u['name']) ?>"
                                                data-username="<?= esc($u['username']) ?>"
                                                data-email="<?= esc($u['email']) ?>"
                                                data-role="<?= $u['role_id'] ?>"
                                                data-wilayah="<?= $u['wilayah_id'] ?? '' ?>"
                                                data-cabang="<?= $u['cabang_id'] ?? '' ?>"
                                                data-status="<?= $u['status'] ?>"
                                                title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php if ($u['id'] !== (int) session()->get('user_id')): ?>
                                            <button type="button" 
                                                    class="btn btn-danger btn-xs btn-delete-user" 
                                                    data-id="<?= $u['id'] ?>"
                                                    data-name="<?= esc($u['name']) ?>"
                                                    title="Hapus">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        <?php endif; ?>
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

<!-- MODAL ADD USER -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title font-weight-bold text-sm">
                    <i class="fas fa-user-plus mr-1"></i> Tambah Administrator Baru
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('admin/users/simpan') ?>" method="POST" autocomplete="off">
                <?= csrf_field() ?>
                <div class="modal-body p-3">
                    <div class="form-group mb-2">
                        <label class="text-xs text-muted font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-sm" placeholder="Nama Admin" required>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-2">
                                <label class="text-xs text-muted font-weight-bold">Username <span class="text-danger">*</span></label>
                                <input type="text" name="username" class="form-control form-control-sm" placeholder="username" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-2">
                                <label class="text-xs text-muted font-weight-bold">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control form-control-sm" placeholder="admin@email.com" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs text-muted font-weight-bold">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control form-control-sm" placeholder="Minimal 6 karakter" required>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs text-muted font-weight-bold">Role / Hak Akses <span class="text-danger">*</span></label>
                        <select name="role_id" id="addRoleSelect" class="form-control form-control-sm" required>
                            <option value="">-- Pilih Role --</option>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?= $r['id'] ?>"><?= esc(ucwords(str_replace('_', ' ', $r['name']))) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-2 d-none" id="addWilayahGroup">
                        <label class="text-xs text-muted font-weight-bold">Wilayah Scope</label>
                        <select name="wilayah_id" id="addWilayahSelect" class="form-control form-control-sm">
                            <option value="">-- Pilih Wilayah --</option>
                            <?php foreach ($wilayahList as $w): ?>
                                <option value="<?= $w['id'] ?>"><?= esc($w['name']) ?> (<?= esc($w['code']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-2 d-none" id="addCabangGroup">
                        <label class="text-xs text-muted font-weight-bold">Cabang Scope</label>
                        <select name="cabang_id" id="addCabangSelect" class="form-control form-control-sm">
                            <option value="">-- Pilih Cabang --</option>
                            <?php foreach ($cabangList as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= esc($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-xs text-muted font-weight-bold">Status Akun</label>
                        <select name="status" class="form-control form-control-sm">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer py-2 bg-light">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3">
                        <i class="fas fa-save mr-1"></i> Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT USER -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title font-weight-bold text-sm">
                    <i class="fas fa-user-edit mr-1"></i> Edit Pengguna
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editUserForm" action="" method="POST" autocomplete="off">
                <?= csrf_field() ?>
                <div class="modal-body p-3">
                    <div class="form-group mb-2">
                        <label class="text-xs text-muted font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editUserName" class="form-control form-control-sm" required>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-2">
                                <label class="text-xs text-muted font-weight-bold">Username <span class="text-danger">*</span></label>
                                <input type="text" name="username" id="editUserUsername" class="form-control form-control-sm" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-2">
                                <label class="text-xs text-muted font-weight-bold">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="editUserEmail" class="form-control form-control-sm" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs text-muted font-weight-bold">Ubah Password (Kosongkan jika tidak diubah)</label>
                        <input type="password" name="password" class="form-control form-control-sm" placeholder="Password baru">
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs text-muted font-weight-bold">Role / Hak Akses <span class="text-danger">*</span></label>
                        <select name="role_id" id="editRoleSelect" class="form-control form-control-sm" required>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?= $r['id'] ?>"><?= esc(ucwords(str_replace('_', ' ', $r['name']))) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-2" id="editWilayahGroup">
                        <label class="text-xs text-muted font-weight-bold">Wilayah Scope</label>
                        <select name="wilayah_id" id="editWilayahSelect" class="form-control form-control-sm">
                            <option value="">-- Pilih Wilayah --</option>
                            <?php foreach ($wilayahList as $w): ?>
                                <option value="<?= $w['id'] ?>"><?= esc($w['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-2" id="editCabangGroup">
                        <label class="text-xs text-muted font-weight-bold">Cabang Scope</label>
                        <select name="cabang_id" id="editCabangSelect" class="form-control form-control-sm">
                            <option value="">-- Pilih Cabang --</option>
                            <?php foreach ($cabangList as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= esc($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-xs text-muted font-weight-bold">Status</label>
                        <select name="status" id="editUserStatus" class="form-control form-control-sm">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
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

<!-- MODAL DELETE USER -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white py-2">
                <h5 class="modal-title font-weight-bold text-sm">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Hapus Pengguna
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="fas fa-trash-alt text-danger fa-3x mb-3 d-block"></i>
                <h6 class="font-weight-bold text-dark mb-2">Konfirmasi Hapus</h6>
                <p class="text-muted text-xs mb-4">Apakah Anda yakin ingin menghapus akun <strong id="deleteUserName" class="text-dark"></strong>?</p>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-default btn-sm px-3 mr-2" data-dismiss="modal">Batal</button>
                    <form id="deleteUserForm" method="POST" action="">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-danger btn-sm px-4">
                            <i class="fas fa-trash mr-1"></i> Hapus Pengguna
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
        function toggleAddScope(roleVal) {
            if (roleVal === '1' || !roleVal) { // superadmin
                $('#addWilayahGroup').addClass('d-none');
                $('#addCabangGroup').addClass('d-none');
            } else if (roleVal === '2') { // admin_wilayah
                $('#addWilayahGroup').removeClass('d-none');
                $('#addCabangGroup').addClass('d-none');
            } else if (roleVal === '3') { // admin_cabang
                $('#addWilayahGroup').removeClass('d-none');
                $('#addCabangGroup').removeClass('d-none');
            }
        }

        $('#addRoleSelect').on('change', function () {
            toggleAddScope($(this).val());
        });

        // Dynamic cabang in Add User
        $('#addWilayahSelect').on('change', function () {
            const wid = $(this).val();
            const $cabangSelect = $('#addCabangSelect');
            if (!wid) {
                $cabangSelect.html('<option value="">-- Pilih Cabang --</option>');
                return;
            }
            $.getJSON('<?= base_url('admin/ajax/cabang/') ?>/' + wid, function (data) {
                let opts = '<option value="">-- Pilih Cabang --</option>';
                $.each(data, function (i, c) {
                    opts += '<option value="' + c.id + '">' + c.name + '</option>';
                });
                $cabangSelect.html(opts);
            });
        });

        // Edit user modal
        $(document).on('click', '.btn-edit-user', function () {
            const id       = $(this).data('id');
            const name     = $(this).data('name');
            const username = $(this).data('username');
            const email    = $(this).data('email');
            const role     = $(this).data('role');
            const wilayah  = $(this).data('wilayah');
            const cabang   = $(this).data('cabang');
            const status   = $(this).data('status');

            $('#editUserName').val(name);
            $('#editUserUsername').val(username);
            $('#editUserEmail').val(email);
            $('#editRoleSelect').val(role);
            $('#editWilayahSelect').val(wilayah);
            $('#editUserStatus').val(status);
            $('#editUserForm').attr('action', '<?= base_url('admin/users/update/') ?>/' + id);

            if (wilayah) {
                $.getJSON('<?= base_url('admin/ajax/cabang/') ?>/' + wilayah, function (data) {
                    let opts = '<option value="">-- Pilih Cabang --</option>';
                    $.each(data, function (i, c) {
                        opts += '<option value="' + c.id + '" ' + (cabang == c.id ? 'selected' : '') + '>' + c.name + '</option>';
                    });
                    $('#editCabangSelect').html(opts);
                });
            }

            $('#editUserModal').modal('show');
        });

        // Delete user modal
        $(document).on('click', '.btn-delete-user', function () {
            const id   = $(this).data('id');
            const name = $(this).data('name');
            $('#deleteUserName').text(name);
            $('#deleteUserForm').attr('action', '<?= base_url('admin/users/delete/') ?>/' + id);
            $('#deleteUserModal').modal('show');
        });
    });
</script>
<?= $this->endSection() ?>
