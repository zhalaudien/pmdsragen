<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<div class="mb-3">
    <a href="<?= base_url('admin/pemuda') ?>" class="btn btn-outline-secondary btn-sm rounded-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Pemuda
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3">
        <div class="d-flex align-items-center gap-2 fw-bold text-dark fs-5">
            <i class="bi <?= $mode === 'create' ? 'bi-person-plus-fill text-primary' : 'bi-pencil-square text-warning' ?>"></i>
            <span><?= $mode === 'create' ? 'Tambah Data Pemuda Baru' : 'Edit Data Pemuda: ' . esc($pemuda['name']) ?></span>
        </div>
    </div>

    <div class="card-body p-4 p-lg-5">
        <form action="<?= $mode === 'create' ? base_url('admin/pemuda/simpan') : base_url('admin/pemuda/update/' . $pemuda['id']) ?>" 
              method="POST" 
              id="adminPemudaForm"
              autocomplete="off">
            <?= csrf_field() ?>

            <!-- 1. DATA ORGANISASI (WILAYAH & CABANG) -->
            <div class="mb-5">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <div class="badge bg-primary rounded-circle step-number-circle">1</div>
                    <h5 class="fw-bold text-dark mb-0">Wilayah & Cabang</h5>
                </div>

                <div class="row g-3">
                    <?php 
                        $userRole = session()->get('role');
                        $userWilayahId = session()->get('wilayah_id');
                        $userCabangId = session()->get('cabang_id');
                    ?>

                    <?php if ($userRole === 'admin_cabang'): ?>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-muted">Wilayah</label>
                            <input type="text" class="form-control bg-light" value="<?= esc(session()->get('wilayah_name') ?? ('Wilayah ' . $userWilayahId)) ?>" readonly>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-muted">Cabang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-light" value="<?= esc(session()->get('cabang_name') ?? ('Cabang ' . $userCabangId)) ?>" readonly>
                            <input type="hidden" name="cabang_id" id="cabang_id" value="<?= esc($userCabangId) ?>">
                        </div>
                    <?php elseif ($userRole === 'admin_wilayah'): ?>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-muted">Wilayah</label>
                            <input type="text" class="form-control bg-light" value="<?= esc(session()->get('wilayah_name') ?? ('Wilayah ' . $userWilayahId)) ?>" readonly>
                            <input type="hidden" id="wilayah_id" value="<?= esc($userWilayahId) ?>">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="cabang_id" class="form-label small fw-semibold text-muted">Pilih Cabang (Wilayah Anda) <span class="text-danger">*</span></label>
                            <select name="cabang_id" id="cabang_id" class="form-select" required>
                                <option value="">-- Pilih Cabang --</option>
                                <?php if (!empty($wilayahWithCabang[0]['cabang'])): ?>
                                    <?php foreach ($wilayahWithCabang[0]['cabang'] as $c): ?>
                                        <option value="<?= $c['id'] ?>" <?= (old('cabang_id', $pemuda['cabang_id'] ?? '') == $c['id']) ? 'selected' : '' ?>>
                                            <?= esc($c['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    <?php else: ?>
                        <div class="col-12 col-md-6">
                            <label for="wilayah_id" class="form-label small fw-semibold text-muted">Pilih Wilayah <span class="text-danger">*</span></label>
                            <select id="wilayah_id" class="form-select" required>
                                <option value="">-- Pilih Wilayah --</option>
                                <?php foreach ($wilayahWithCabang as $w): ?>
                                    <option value="<?= $w['id'] ?>" <?= (isset($pemuda['wilayah_id']) && $pemuda['wilayah_id'] == $w['id']) ? 'selected' : '' ?>>
                                        <?= esc($w['name']) ?> (<?= esc($w['code']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="cabang_id" class="form-label small fw-semibold text-muted">Pilih Cabang <span class="text-danger">*</span></label>
                            <select name="cabang_id" id="cabang_id" class="form-select" required>
                                <option value="">-- Pilih Wilayah Terlebih Dahulu --</option>
                                <?php if (!empty($pemuda['cabang_id'])): ?>
                                    <option value="<?= $pemuda['cabang_id'] ?>" selected><?= esc($pemuda['cabang_name']) ?></option>
                                <?php endif; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 2. DATA PRIBADI -->
            <div class="mb-5">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <div class="badge bg-primary rounded-circle step-number-circle">2</div>
                    <h5 class="fw-bold text-dark mb-0">Data Pribadi</h5>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="name" class="form-label small fw-semibold text-muted">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="name" 
                               name="name" 
                               value="<?= old('name', $pemuda['name'] ?? '') ?>" 
                               placeholder="Nama lengkap sesuai KTP" 
                               required>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <label class="form-label small fw-semibold text-muted">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select name="gender" class="form-select" required>
                            <option value="L" <?= old('gender', $pemuda['gender'] ?? '') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="P" <?= old('gender', $pemuda['gender'] ?? '') === 'P' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <label class="form-label small fw-semibold text-muted">Status Pernikahan <span class="text-danger">*</span></label>
                        <select name="marital_status" class="form-select" required>
                            <option value="belum_menikah" <?= old('marital_status', $pemuda['marital_status'] ?? '') === 'belum_menikah' ? 'selected' : '' ?>>Belum Menikah</option>
                            <option value="sudah_menikah" <?= old('marital_status', $pemuda['marital_status'] ?? '') === 'sudah_menikah' ? 'selected' : '' ?>>Sudah Menikah</option>
                            <option value="janda" <?= old('marital_status', $pemuda['marital_status'] ?? '') === 'janda' ? 'selected' : '' ?>>Janda</option>
                            <option value="duda" <?= old('marital_status', $pemuda['marital_status'] ?? '') === 'duda' ? 'selected' : '' ?>>Duda</option>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <label class="form-label small fw-semibold text-muted">Golongan Darah</label>
                        <select name="blood_type" class="form-select">
                            <option value="">-- Tidak Tahu / Belum Cek --</option>
                            <option value="A" <?= old('blood_type', $pemuda['blood_type'] ?? '') === 'A' ? 'selected' : '' ?>>A</option>
                            <option value="B" <?= old('blood_type', $pemuda['blood_type'] ?? '') === 'B' ? 'selected' : '' ?>>B</option>
                            <option value="AB" <?= old('blood_type', $pemuda['blood_type'] ?? '') === 'AB' ? 'selected' : '' ?>>AB</option>
                            <option value="O" <?= old('blood_type', $pemuda['blood_type'] ?? '') === 'O' ? 'selected' : '' ?>>O</option>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="birth_place" class="form-label small fw-semibold text-muted">Tempat Lahir <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="birth_place" 
                               name="birth_place" 
                               value="<?= old('birth_place', $pemuda['birth_place'] ?? 'Sragen') ?>" 
                               required>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="birth_date" class="form-label small fw-semibold text-muted">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date" 
                               class="form-control" 
                               id="birth_date" 
                               name="birth_date" 
                               value="<?= old('birth_date', $pemuda['birth_date'] ?? '') ?>" 
                               required>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="phone" class="form-label small fw-semibold text-muted">No. WhatsApp / HP <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="phone" 
                               name="phone" 
                               value="<?= old('phone', $pemuda['phone'] ?? '') ?>" 
                               placeholder="Contoh: 08123456789" 
                               required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="email" class="form-label small fw-semibold text-muted">Email (Opsional)</label>
                        <input type="email" 
                               class="form-control" 
                               id="email" 
                               name="email" 
                               value="<?= old('email', $pemuda['email'] ?? '') ?>" 
                               placeholder="nama@email.com">
                    </div>
                </div>
            </div>

            <!-- 3. ALAMAT & DOMISILI -->
            <div class="mb-5">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <div class="badge bg-primary rounded-circle step-number-circle">3</div>
                    <h5 class="fw-bold text-dark mb-0">Alamat & Domisili</h5>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="district_id" class="form-label small fw-semibold text-muted">Kecamatan (Sragen) <span class="text-danger">*</span></label>
                        <select name="district_id" id="district_id" class="form-select" required>
                            <option value="">-- Pilih Kecamatan --</option>
                            <?php foreach ($districts as $d): ?>
                                <option value="<?= $d['id'] ?>" <?= (old('district_id', $pemuda['district_id'] ?? '') == $d['id']) ? 'selected' : '' ?>>
                                    <?= esc($d['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="village_id" class="form-label small fw-semibold text-muted">Desa / Kelurahan <span class="text-danger">*</span></label>
                        <select name="village_id" id="village_id" class="form-select" required>
                            <option value="">-- Pilih Kecamatan Terlebih Dahulu --</option>
                            <?php if (!empty($villages)): ?>
                                <?php foreach ($villages as $v): ?>
                                    <option value="<?= $v['id'] ?>" <?= (old('village_id', $pemuda['village_id'] ?? '') == $v['id']) ? 'selected' : '' ?>>
                                        <?= esc($v['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="dusun" class="form-label small fw-semibold text-muted">Dusun / Dukuh</label>
                        <input type="text" 
                               class="form-control" 
                               id="dusun" 
                               name="dusun" 
                               value="<?= old('dusun', $pemuda['dusun'] ?? '') ?>" 
                               placeholder="Nama dusun/kampung">
                    </div>

                    <div class="col-6 col-md-4">
                        <label for="rt" class="form-label small fw-semibold text-muted">RT</label>
                        <input type="text" class="form-control" id="rt" name="rt" value="<?= old('rt', $pemuda['rt'] ?? '') ?>" placeholder="01">
                    </div>

                    <div class="col-6 col-md-4">
                        <label for="rw" class="form-label small fw-semibold text-muted">RW</label>
                        <input type="text" class="form-control" id="rw" name="rw" value="<?= old('rw', $pemuda['rw'] ?? '') ?>" placeholder="01">
                    </div>

                    <div class="col-12">
                        <label for="address_detail" class="form-label small fw-semibold text-muted">Alamat Lengkap / Jalan <span class="text-danger">*</span></label>
                        <textarea class="form-control" 
                                  id="address_detail" 
                                  name="address_detail" 
                                  rows="2" 
                                  required><?= old('address_detail', $pemuda['address_detail'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- 4. PENDIDIKAN & PEKERJAAN -->
            <div class="mb-5">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <div class="badge bg-primary rounded-circle step-number-circle">4</div>
                    <h5 class="fw-bold text-dark mb-0">Pendidikan & Pekerjaan</h5>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-4">
                        <label for="education_level_id" class="form-label small fw-semibold text-muted">Jenjang Pendidikan Terakhir <span class="text-danger">*</span></label>
                        <select name="education_level_id" id="education_level_id" class="form-select" required>
                            <option value="">-- Pilih Jenjang --</option>
                            <?php foreach ($educationLevels as $edu): ?>
                                <option value="<?= $edu['id'] ?>" <?= (old('education_level_id', $pemuda['education_level_id'] ?? '') == $edu['id']) ? 'selected' : '' ?>>
                                    <?= esc($edu['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="school_name" class="form-label small fw-semibold text-muted">Nama Sekolah / Kampus <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="school_name" 
                               name="school_name" 
                               value="<?= old('school_name', $pemuda['school_name'] ?? '') ?>" 
                               placeholder="Nama SMA / SMK / Universitas" 
                               required>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="major" class="form-label small fw-semibold text-muted">Jurusan / Program Studi</label>
                        <input type="text" 
                               class="form-control" 
                               id="major" 
                               name="major" 
                               value="<?= old('major', $pemuda['major'] ?? '') ?>" 
                               placeholder="Contoh: Teknik Informatika / IPA">
                    </div>

                    <div class="col-12 col-sm-6 col-md-4">
                        <label class="form-label small fw-semibold text-muted">Status Pendidikan <span class="text-danger">*</span></label>
                        <select name="education_status" class="form-select" required>
                            <option value="lulus" <?= old('education_status', $pemuda['education_status'] ?? '') === 'lulus' ? 'selected' : '' ?>>Lulus</option>
                            <option value="sedang_sekolah" <?= old('education_status', $pemuda['education_status'] ?? '') === 'sedang_sekolah' ? 'selected' : '' ?>>Sedang Menempuh</option>
                            <option value="putus_sekolah" <?= old('education_status', $pemuda['education_status'] ?? '') === 'putus_sekolah' ? 'selected' : '' ?>>Putus Sekolah</option>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-md-4">
                        <label for="graduation_year" class="form-label small fw-semibold text-muted">Tahun Kelulusan</label>
                        <input type="number" 
                               class="form-control" 
                               id="graduation_year" 
                               name="graduation_year" 
                               value="<?= old('graduation_year', $pemuda['graduation_year'] ?? '') ?>" 
                               placeholder="Contoh: 2022">
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label for="job_status_id" class="form-label small fw-semibold text-muted">Status Pekerjaan <span class="text-danger">*</span></label>
                        <select name="job_status_id" id="job_status_id" class="form-select" required>
                            <option value="">-- Pilih Status Pekerjaan --</option>
                            <?php foreach ($jobStatuses as $job): ?>
                                <option value="<?= $job['id'] ?>" <?= (old('job_status_id', $pemuda['job_status_id'] ?? '') == $job['id']) ? 'selected' : '' ?>>
                                    <?= esc($job['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="job_title" class="form-label small fw-semibold text-muted">Profesi / Jabatan</label>
                        <input type="text" 
                               class="form-control" 
                               id="job_title" 
                               name="job_title" 
                               value="<?= old('job_title', $pemuda['job_title'] ?? '') ?>" 
                               placeholder="Contoh: Staf Administrasi / Wirausaha">
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="company_name" class="form-label small fw-semibold text-muted">Nama Instansi / Perusahaan</label>
                        <input type="text" 
                               class="form-control" 
                               id="company_name" 
                               name="company_name" 
                               value="<?= old('company_name', $pemuda['company_name'] ?? '') ?>" 
                               placeholder="Nama kantor atau unit usaha">
                    </div>
                </div>
            </div>

            <!-- 5. KEAHLIAN & MINAT -->
            <div class="mb-5">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <div class="badge bg-primary rounded-circle step-number-circle">5</div>
                    <h5 class="fw-bold text-dark mb-0">Keahlian (Skills) & Minat (Interests)</h5>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-semibold text-dark mb-2">Pilih Keahlian:</label>
                    <div class="row g-2">
                        <?php foreach ($skills as $sk): 
                            $isChecked = isset($activeSkillIds) && in_array($sk['id'], $activeSkillIds);
                            $levelVal  = isset($activeSkillLevels[$sk['id']]) ? $activeSkillLevels[$sk['id']] : 'pemula';
                        ?>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <div class="p-2 border rounded-3 bg-light bg-opacity-50">
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="skills[<?= $sk['id'] ?>][selected]" 
                                               value="1" 
                                               id="sk_<?= $sk['id'] ?>"
                                               <?= $isChecked ? 'checked' : '' ?>>
                                        <label class="form-check-label fw-semibold small" for="sk_<?= $sk['id'] ?>">
                                            <?= esc($sk['name']) ?>
                                        </label>
                                    </div>
                                    <select name="skills[<?= $sk['id'] ?>][level]" class="form-select form-select-sm mt-1">
                                        <option value="pemula" <?= $levelVal === 'pemula' ? 'selected' : '' ?>>Pemula</option>
                                        <option value="menengah" <?= $levelVal === 'menengah' ? 'selected' : '' ?>>Menengah</option>
                                        <option value="mahir" <?= $levelVal === 'mahir' ? 'selected' : '' ?>>Mahir</option>
                                    </select>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div>
                    <label class="form-label small fw-semibold text-dark mb-2">Pilih Minat / Hobi:</label>
                    <div class="row g-2">
                        <?php foreach ($interests as $in): 
                            $isCheckedIn = isset($activeInterestIds) && in_array($in['id'], $activeInterestIds);
                        ?>
                            <div class="col-6 col-sm-4 col-lg-3">
                                <div class="form-check p-2 border rounded-3 bg-light bg-opacity-50 ps-4">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="interests[]" 
                                           value="<?= $in['id'] ?>" 
                                           id="in_<?= $in['id'] ?>"
                                           <?= $isCheckedIn ? 'checked' : '' ?>>
                                    <label class="form-check-label small" for="in_<?= $in['id'] ?>">
                                        <?= esc($in['name']) ?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- 6. STATUS DATA & VERIFIKASI -->
            <div class="mb-5">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <div class="badge bg-primary rounded-circle step-number-circle">6</div>
                    <h5 class="fw-bold text-dark mb-0">Status Verifikasi & Status Data</h5>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <label class="form-label small fw-semibold text-muted">Status Verifikasi</label>
                        <?php if (in_array(session()->get('role'), ['superadmin', 'admin_cabang'], true)): ?>
                            <select name="status_verifikasi" class="form-select" required>
                                <option value="verified" <?= old('status_verifikasi', $pemuda['status_verifikasi'] ?? 'verified') === 'verified' ? 'selected' : '' ?>>Terverifikasi (Valid)</option>
                                <option value="pending" <?= old('status_verifikasi', $pemuda['status_verifikasi'] ?? '') === 'pending' ? 'selected' : '' ?>>Menunggu Verifikasi</option>
                                <option value="rejected" <?= old('status_verifikasi', $pemuda['status_verifikasi'] ?? '') === 'rejected' ? 'selected' : '' ?>>Ditolak</option>
                            </select>
                        <?php else: ?>
                            <!-- Read-only for admin_wilayah -->
                            <div class="p-3 bg-light rounded-3 border">
                                <?php 
                                    $currV = $pemuda['status_verifikasi'] ?? 'pending';
                                    if ($currV === 'verified') echo '<span class="badge badge-verif-verified rounded-pill px-3 py-1"><i class="bi bi-check-circle-fill me-1"></i> Terverifikasi</span>';
                                    elseif ($currV === 'rejected') echo '<span class="badge badge-verif-rejected rounded-pill px-3 py-1"><i class="bi bi-x-circle-fill me-1"></i> Ditolak</span>';
                                    else echo '<span class="badge badge-verif-pending rounded-pill px-3 py-1"><i class="bi bi-clock-history me-1"></i> Menunggu Verifikasi</span>';
                                ?>
                                <div class="small text-muted mt-2">
                                    <i class="bi bi-info-circle me-1"></i> Hak verifikasi dipegang oleh <strong>Admin Cabang</strong> & <strong>Superadmin</strong>.
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-12 col-sm-6">
                        <label class="form-label small fw-semibold text-muted">Status Data</label>
                        <select name="status_data" class="form-select" required>
                            <option value="active" <?= old('status_data', $pemuda['status_data'] ?? 'active') === 'active' ? 'selected' : '' ?>>Aktif</option>
                            <option value="archived" <?= old('status_data', $pemuda['status_data'] ?? '') === 'archived' ? 'selected' : '' ?>>Arsip</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- SUBMIT BUTTONS -->
            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <a href="<?= base_url('admin/pemuda') ?>" class="btn btn-light rounded-3 px-4">Batal</a>
                <button type="submit" class="btn btn-primary rounded-3 px-4 shadow-sm">
                    <i class="bi bi-check2-circle me-1"></i> <?= $mode === 'create' ? 'Simpan Data Pemuda' : 'Perbarui Data Pemuda' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const wilayahData = <?= json_encode($wilayahWithCabang) ?>;
        const selectWilayah = document.getElementById('wilayah_id');
        const selectCabang  = document.getElementById('cabang_id');
        const selectDistrict = document.getElementById('district_id');
        const selectVillage  = document.getElementById('village_id');

        const currentCabangId = "<?= old('cabang_id', $pemuda['cabang_id'] ?? '') ?>";
        const currentVillageId = "<?= old('village_id', $pemuda['village_id'] ?? '') ?>";

        function updateCabangOptions(wilayahId, selectedCabangId = null) {
            selectCabang.innerHTML = '<option value="">-- Pilih Cabang --</option>';
            if (!wilayahId) return;

            const selectedWilayah = wilayahData.find(w => w.id == wilayahId);
            if (selectedWilayah && selectedWilayah.cabang) {
                selectedWilayah.cabang.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.id;
                    opt.textContent = c.name;
                    if (selectedCabangId && selectedCabangId == c.id) {
                        opt.selected = true;
                    }
                    selectCabang.appendChild(opt);
                });
            }
        }

        if (selectWilayah) {
            selectWilayah.addEventListener('change', function () {
                updateCabangOptions(this.value);
            });

            if (selectWilayah.value) {
                updateCabangOptions(selectWilayah.value, currentCabangId);
            }
        }

        // District to Village cascade
        if (selectDistrict && selectVillage) {
            selectDistrict.addEventListener('change', function () {
                const districtId = this.value;
                selectVillage.innerHTML = '<option value="">-- Memuat Desa... --</option>';

                if (!districtId) {
                    selectVillage.innerHTML = '<option value="">-- Pilih Kecamatan Terlebih Dahulu --</option>';
                    return;
                }

                fetch('<?= base_url('admin/ajax/villages/') ?>/' + districtId)
                    .then(res => res.json())
                    .then(data => {
                        let opts = '<option value="">-- Pilih Desa / Kelurahan --</option>';
                        data.forEach(v => {
                            opts += `<option value="${v.id}" ${currentVillageId == v.id ? 'selected' : ''}>${v.name}</option>`;
                        });
                        selectVillage.innerHTML = opts;
                    })
                    .catch(() => {
                        selectVillage.innerHTML = '<option value="">-- Gagal memuat data desa --</option>';
                    });
            });
        }
    });
</script>
<?= $this->endSection() ?>
