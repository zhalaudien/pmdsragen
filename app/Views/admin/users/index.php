<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Manajemen Pengguna & Admin</h4>
        <p class="text-muted small mb-0">Kelola akun administrator dengan hak akses bertingkat (Superadmin, Admin Wilayah, Admin Cabang).</p>
    </div>
    <div>
        <button class="btn btn-primary rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-person-plus-fill me-1"></i> Tambah Pengguna
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="<?= base_url('admin/users') ?>" method="GET" class="row g-2 align-items-center">
            <div class="col-12 col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama, username, email..." value="<?= esc($search ?? '') ?>">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <select name="role_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Semua Role --</option>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= $r['id'] ?>" <?= ($selectedRole == $r['id']) ? 'selected' : '' ?>>
                            <?= esc(ucwords(str_replace('_', ' ', $r['name']))) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1 rounded-3">Filter</button>
                <?php if (!empty($search) || !empty($selectedRole)): ?>
                    <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary rounded-3">Reset</a>
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
                        <th class="ps-3" style="width: 50px;">ID</th>
                        <th>Nama Pengguna</th>
                        <th>Username & Email</th>
                        <th>Role / Hak Akses</th>
                        <th>Scope Wilayah & Cabang</th>
                        <th>Status</th>
                        <th>Login Terakhir</th>
                        <th class="text-end pe-3" style="width: 110px;">Aksi</th>
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
                                <td class="ps-3 text-muted"><?= $u['id'] ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= esc($u['name']) ?></div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><i class="bi bi-person me-1 text-muted"></i><?= esc($u['username']) ?></div>
                                    <div class="text-muted small"><?= esc($u['email']) ?></div>
                                </td>
                                <td>
                                    <?php if ($u['role_name'] === 'superadmin'): ?>
                                        <span class="badge badge-role-superadmin rounded-pill px-3 py-1">Superadmin</span>
                                    <?php elseif ($u['role_name'] === 'admin_wilayah'): ?>
                                        <span class="badge badge-role-wilayah rounded-pill px-3 py-1">Admin Wilayah</span>
                                    <?php else: ?>
                                        <span class="badge badge-role-cabang rounded-pill px-3 py-1">Admin Cabang</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($u['role_name'] === 'superadmin'): ?>
                                        <span class="text-muted small"><i class="bi bi-globe2 text-purple me-1"></i> Akses Penuh (Global)</span>
                                    <?php elseif ($u['role_name'] === 'admin_wilayah'): ?>
                                        <span class="fw-semibold text-primary small"><i class="bi bi-geo-alt me-1"></i> <?= esc($u['wilayah_name'] ?: 'Wilayah ' . $u['wilayah_id']) ?></span>
                                    <?php else: ?>
                                        <span class="fw-semibold text-success small"><i class="bi bi-diagram-3 me-1"></i> <?= esc($u['cabang_name'] ?: 'Cabang ' . $u['cabang_id']) ?> (<?= esc($u['wilayah_name'] ?? '') ?>)</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $u['status'] == 1 ? '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Aktif</span>' : '<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">Nonaktif</span>' ?>
                                </td>
                                <td class="text-muted small">
                                    <?= $u['last_login'] ? date('d/m/Y H:i', strtotime($u['last_login'])) : '-' ?>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" 
                                                class="btn btn-outline-primary btn-edit-user" 
                                                data-id="<?= $u['id'] ?>"
                                                data-name="<?= esc($u['name']) ?>"
                                                data-username="<?= esc($u['username']) ?>"
                                                data-email="<?= esc($u['email']) ?>"
                                                data-role="<?= $u['role_id'] ?>"
                                                data-wilayah="<?= $u['wilayah_id'] ?? '' ?>"
                                                data-cabang="<?= $u['cabang_id'] ?? '' ?>"
                                                data-status="<?= $u['status'] ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <?php if ($u['id'] !== (int) session()->get('user_id')): ?>
                                            <button type="button" 
                                                    class="btn btn-outline-danger btn-delete-user" 
                                                    data-id="<?= $u['id'] ?>"
                                                    data-name="<?= esc($u['name']) ?>">
                                                <i class="bi bi-trash"></i>
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
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-dark mb-0">Tambah Administrator Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/users/simpan') ?>" method="POST" autocomplete="off">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Nama Admin" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-muted">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" placeholder="username" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-muted">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="admin@email.com" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Role / Hak Akses <span class="text-danger">*</span></label>
                        <select name="role_id" id="addRoleSelect" class="form-select" required>
                            <option value="">-- Pilih Role --</option>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?= $r['id'] ?>"><?= esc(ucwords(str_replace('_', ' ', $r['name']))) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3 d-none" id="addWilayahGroup">
                        <label class="form-label small fw-semibold text-muted">Wilayah Scope</label>
                        <select name="wilayah_id" id="addWilayahSelect" class="form-select">
                            <option value="">-- Pilih Wilayah --</option>
                            <?php foreach ($wilayahList as $w): ?>
                                <option value="<?= $w['id'] ?>"><?= esc($w['name']) ?> (<?= esc($w['code']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3 d-none" id="addCabangGroup">
                        <label class="form-label small fw-semibold text-muted">Cabang Scope</label>
                        <select name="cabang_id" id="addCabangSelect" class="form-select">
                            <option value="">-- Pilih Cabang --</option>
                            <?php foreach ($cabangList as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= esc($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Status Akun</label>
                        <select name="status" class="form-select">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 shadow-sm">Simpan Pengguna</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT USER -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-dark mb-0">Edit Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUserForm" action="" method="POST" autocomplete="off">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editUserName" class="form-control" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-muted">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" id="editUserUsername" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-muted">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="editUserEmail" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Ubah Password (Kosongkan jika tidak diubah)</label>
                        <input type="password" name="password" class="form-control" placeholder="Password baru">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Role / Hak Akses <span class="text-danger">*</span></label>
                        <select name="role_id" id="editRoleSelect" class="form-select" required>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?= $r['id'] ?>"><?= esc(ucwords(str_replace('_', ' ', $r['name']))) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3" id="editWilayahGroup">
                        <label class="form-label small fw-semibold text-muted">Wilayah Scope</label>
                        <select name="wilayah_id" id="editWilayahSelect" class="form-select">
                            <option value="">-- Pilih Wilayah --</option>
                            <?php foreach ($wilayahList as $w): ?>
                                <option value="<?= $w['id'] ?>"><?= esc($w['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3" id="editCabangGroup">
                        <label class="form-label small fw-semibold text-muted">Cabang Scope</label>
                        <select name="cabang_id" id="editCabangSelect" class="form-select">
                            <option value="">-- Pilih Cabang --</option>
                            <?php foreach ($cabangList as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= esc($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Status</label>
                        <select name="status" id="editUserStatus" class="form-select">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
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

<!-- MODAL DELETE USER -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-body p-4 text-center">
                <i class="bi bi-exclamation-triangle text-danger fs-1 mb-2 d-block"></i>
                <h5 class="fw-bold text-dark mb-2">Hapus Pengguna</h5>
                <p class="text-muted small mb-4">Apakah Anda yakin ingin menghapus akun <strong id="deleteUserName"></strong>?</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteUserForm" method="POST" action="">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-danger rounded-3 px-4 shadow-sm">Hapus Pengguna</button>
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
        // Add Role toggle scope fields
        const addRoleSelect    = document.getElementById('addRoleSelect');
        const addWilayahGroup  = document.getElementById('addWilayahGroup');
        const addCabangGroup   = document.getElementById('addCabangGroup');
        const addWilayahSelect = document.getElementById('addWilayahSelect');
        const addCabangSelect  = document.getElementById('addCabangSelect');

        function toggleAddScope(roleVal) {
            if (roleVal === '1' || !roleVal) { // superadmin
                addWilayahGroup.classList.add('d-none');
                addCabangGroup.classList.add('d-none');
            } else if (roleVal === '2') { // admin_wilayah
                addWilayahGroup.classList.remove('d-none');
                addCabangGroup.classList.add('d-none');
            } else if (roleVal === '3') { // admin_cabang
                addWilayahGroup.classList.remove('d-none');
                addCabangGroup.classList.remove('d-none');
            }
        }

        if (addRoleSelect) {
            addRoleSelect.addEventListener('change', function () {
                toggleAddScope(this.value);
            });
        }

        // Dynamic cabang in Add User
        if (addWilayahSelect && addCabangSelect) {
            addWilayahSelect.addEventListener('change', function () {
                const wid = this.value;
                if (!wid) {
                    addCabangSelect.innerHTML = '<option value="">-- Pilih Cabang --</option>';
                    return;
                }
                fetch('<?= base_url('admin/ajax/cabang/') ?>/' + wid)
                    .then(res => res.json())
                    .then(data => {
                        let opts = '<option value="">-- Pilih Cabang --</option>';
                        data.forEach(c => {
                            opts += `<option value="${c.id}">${c.name}</option>`;
                        });
                        addCabangSelect.innerHTML = opts;
                    });
            });
        }

        // Edit user modal
        const editButtons   = document.querySelectorAll('.btn-edit-user');
        const editModalEl   = document.getElementById('editUserModal');
        const editForm      = document.getElementById('editUserForm');
        const editName      = document.getElementById('editUserName');
        const editUsername  = document.getElementById('editUserUsername');
        const editEmail     = document.getElementById('editUserEmail');
        const editRole      = document.getElementById('editRoleSelect');
        const editWilayah   = document.getElementById('editWilayahSelect');
        const editCabang    = document.getElementById('editCabangSelect');
        const editStatus    = document.getElementById('editUserStatus');

        if (editButtons.length && editModalEl) {
            const editModal = new bootstrap.Modal(editModalEl);
            editButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    const id       = this.getAttribute('data-id');
                    const name     = this.getAttribute('data-name');
                    const username = this.getAttribute('data-username');
                    const email    = this.getAttribute('data-email');
                    const role     = this.getAttribute('data-role');
                    const wilayah  = this.getAttribute('data-wilayah');
                    const cabang   = this.getAttribute('data-cabang');
                    const status   = this.getAttribute('data-status');

                    editName.value     = name;
                    editUsername.value = username;
                    editEmail.value    = email;
                    editRole.value     = role;
                    editWilayah.value  = wilayah;
                    editStatus.value   = status;
                    editForm.action    = '<?= base_url('admin/users/update/') ?>/' + id;

                    if (wilayah) {
                        fetch('<?= base_url('admin/ajax/cabang/') ?>/' + wilayah)
                            .then(res => res.json())
                            .then(data => {
                                let opts = '<option value="">-- Pilih Cabang --</option>';
                                data.forEach(c => {
                                    opts += `<option value="${c.id}" ${cabang == c.id ? 'selected' : ''}>${c.name}</option>`;
                                });
                                editCabang.innerHTML = opts;
                            });
                    }

                    editModal.show();
                });
            });
        }

        // Delete user modal
        const delButtons = document.querySelectorAll('.btn-delete-user');
        const delModalEl = document.getElementById('deleteUserModal');
        const delForm    = document.getElementById('deleteUserForm');
        const delNameEl  = document.getElementById('deleteUserName');

        if (delButtons.length && delModalEl) {
            const delModal = new bootstrap.Modal(delModalEl);
            delButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    const id   = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    delNameEl.textContent = name;
                    delForm.action = '<?= base_url('admin/users/delete/') ?>/' + id;
                    delModal.show();
                });
            });
        }
    });
</script>
<?= $this->endSection() ?>
