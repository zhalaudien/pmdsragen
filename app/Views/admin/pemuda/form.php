<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<div class="mb-3">
    <a href="<?= base_url('admin/pemuda') ?>" class="btn btn-default btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Pemuda
    </a>
</div>

<div class="card card-primary card-outline shadow-sm">
    <div class="card-header border-0">
        <h3 class="card-title font-weight-bold text-sm">
            <i class="fas <?= $mode === 'create' ? 'fa-user-plus text-primary' : 'fa-edit text-warning' ?> mr-1"></i>
            <?= $mode === 'create' ? 'Tambah Data Pemuda Baru' : 'Edit Data Pemuda: ' . esc($pemuda['name']) ?>
        </h3>
    </div>

    <div class="card-body p-4">
        <form action="<?= $mode === 'create' ? base_url('admin/pemuda/simpan') : base_url('admin/pemuda/update/' . $pemuda['id']) ?>" 
              method="POST" 
              id="adminPemudaForm"
              autocomplete="off">
            <?= csrf_field() ?>

            <!-- 1. DATA ORGANISASI (WILAYAH & CABANG) -->
            <div class="mb-4">
                <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                    <span class="badge badge-primary mr-2" style="font-size: 0.9rem; border-radius: 50%; width: 26px; height: 26px; line-height: 18px; text-align: center;">1</span>
                    <h6 class="font-weight-bold text-dark mb-0 text-uppercase text-xs">Wilayah &amp; Cabang</h6>
                </div>

                <div class="row">
                    <?php 
                        $userRole = session()->get('role');
                        $userWilayahId = session()->get('wilayah_id');
                        $userCabangId = session()->get('cabang_id');
                    ?>

                    <?php if ($userRole === 'admin_cabang'): ?>
                        <div class="col-12 col-md-6 form-group mb-2">
                            <label class="text-xs text-muted font-weight-bold">Wilayah</label>
                            <input type="text" class="form-control form-control-sm bg-light" value="<?= esc(session()->get('wilayah_name') ?? ('Wilayah ' . $userWilayahId)) ?>" readonly>
                        </div>
                        <div class="col-12 col-md-6 form-group mb-2">
                            <label class="text-xs text-muted font-weight-bold">Cabang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm bg-light" value="<?= esc(session()->get('cabang_name') ?? ('Cabang ' . $userCabangId)) ?>" readonly>
                            <input type="hidden" name="cabang_id" id="cabang_id" value="<?= esc($userCabangId) ?>">
                        </div>
                    <?php elseif ($userRole === 'admin_wilayah'): ?>
                        <div class="col-12 col-md-6 form-group mb-2">
                            <label class="text-xs text-muted font-weight-bold">Wilayah</label>
                            <input type="text" class="form-control form-control-sm bg-light" value="<?= esc(session()->get('wilayah_name') ?? ('Wilayah ' . $userWilayahId)) ?>" readonly>
                            <input type="hidden" id="wilayah_id" value="<?= esc($userWilayahId) ?>">
                        </div>
                        <div class="col-12 col-md-6 form-group mb-2">
                            <label for="cabang_id" class="text-xs text-muted font-weight-bold">Pilih Cabang (Wilayah Anda) <span class="text-danger">*</span></label>
                            <select name="cabang_id" id="cabang_id" class="form-control form-control-sm" required>
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
                        <div class="col-12 col-md-6 form-group mb-2">
                            <label for="wilayah_id" class="text-xs text-muted font-weight-bold">Pilih Wilayah <span class="text-danger">*</span></label>
                            <select id="wilayah_id" class="form-control form-control-sm" required>
                                <option value="">-- Pilih Wilayah --</option>
                                <?php foreach ($wilayahWithCabang as $w): ?>
                                    <option value="<?= $w['id'] ?>" <?= (isset($pemuda['wilayah_id']) && $pemuda['wilayah_id'] == $w['id']) ? 'selected' : '' ?>>
                                        <?= esc($w['name']) ?> (<?= esc($w['code']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-6 form-group mb-2">
                            <label for="cabang_id" class="text-xs text-muted font-weight-bold">Pilih Cabang <span class="text-danger">*</span></label>
                            <select name="cabang_id" id="cabang_id" class="form-control form-control-sm" required>
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
            <div class="mb-4">
                <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                    <span class="badge badge-primary mr-2" style="font-size: 0.9rem; border-radius: 50%; width: 26px; height: 26px; line-height: 18px; text-align: center;">2</span>
                    <h6 class="font-weight-bold text-dark mb-0 text-uppercase text-xs">Data Pribadi</h6>
                </div>

                <div class="row">
                    <div class="col-12 col-md-6 form-group mb-2">
                        <label for="name" class="text-xs text-muted font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control form-control-sm" 
                               id="name" 
                               name="name" 
                               value="<?= old('name', $pemuda['name'] ?? '') ?>" 
                               placeholder="Nama lengkap sesuai KTP" 
                               required>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3 form-group mb-2">
                        <label class="text-xs text-muted font-weight-bold">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select name="gender" class="form-control form-control-sm" required>
                            <option value="L" <?= old('gender', $pemuda['gender'] ?? '') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="P" <?= old('gender', $pemuda['gender'] ?? '') === 'P' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3 form-group mb-2">
                        <label class="text-xs text-muted font-weight-bold">Status Pernikahan <span class="text-danger">*</span></label>
                        <select name="marital_status" class="form-control form-control-sm" required>
                            <option value="belum_menikah" <?= old('marital_status', $pemuda['marital_status'] ?? '') === 'belum_menikah' ? 'selected' : '' ?>>Belum Menikah</option>
                            <option value="sudah_menikah" <?= old('marital_status', $pemuda['marital_status'] ?? '') === 'sudah_menikah' ? 'selected' : '' ?>>Sudah Menikah</option>
                            <option value="janda" <?= old('marital_status', $pemuda['marital_status'] ?? '') === 'janda' ? 'selected' : '' ?>>Janda</option>
                            <option value="duda" <?= old('marital_status', $pemuda['marital_status'] ?? '') === 'duda' ? 'selected' : '' ?>>Duda</option>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3 form-group mb-2">
                        <label class="text-xs text-muted font-weight-bold">Golongan Darah</label>
                        <select name="blood_type" class="form-control form-control-sm">
                            <option value="">-- Tidak Tahu / Belum Cek --</option>
                            <option value="A" <?= old('blood_type', $pemuda['blood_type'] ?? '') === 'A' ? 'selected' : '' ?>>A</option>
                            <option value="B" <?= old('blood_type', $pemuda['blood_type'] ?? '') === 'B' ? 'selected' : '' ?>>B</option>
                            <option value="AB" <?= old('blood_type', $pemuda['blood_type'] ?? '') === 'AB' ? 'selected' : '' ?>>AB</option>
                            <option value="O" <?= old('blood_type', $pemuda['blood_type'] ?? '') === 'O' ? 'selected' : '' ?>>O</option>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3 form-group mb-2">
                        <label for="birth_place" class="text-xs text-muted font-weight-bold">Tempat Lahir <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control form-control-sm" 
                               id="birth_place" 
                               name="birth_place" 
                               value="<?= old('birth_place', $pemuda['birth_place'] ?? 'Sragen') ?>" 
                               required>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3 form-group mb-2">
                        <label for="birth_date" class="text-xs text-muted font-weight-bold">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date" 
                               class="form-control form-control-sm" 
                               id="birth_date" 
                               name="birth_date" 
                               value="<?= old('birth_date', $pemuda['birth_date'] ?? '') ?>" 
                               required>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3 form-group mb-2">
                        <label for="phone" class="text-xs text-muted font-weight-bold">No. WhatsApp / HP <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control form-control-sm" 
                               id="phone" 
                               name="phone" 
                               value="<?= old('phone', $pemuda['phone'] ?? '') ?>" 
                               placeholder="Contoh: 08123456789" 
                               required>
                    </div>

                    <div class="col-12 col-md-6 form-group mb-2">
                        <label for="email" class="text-xs text-muted font-weight-bold">Email (Opsional)</label>
                        <input type="email" 
                               class="form-control form-control-sm" 
                               id="email" 
                               name="email" 
                               value="<?= old('email', $pemuda['email'] ?? '') ?>" 
                               placeholder="nama@email.com">
                    </div>
                </div>
            </div>

            <!-- 3. ALAMAT & DOMISILI -->
            <div class="mb-4">
                <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                    <span class="badge badge-primary mr-2" style="font-size: 0.9rem; border-radius: 50%; width: 26px; height: 26px; line-height: 18px; text-align: center;">3</span>
                    <h6 class="font-weight-bold text-dark mb-0 text-uppercase text-xs">Alamat &amp; Domisili</h6>
                </div>

                <div class="row">
                    <div class="col-12 col-md-6 form-group mb-2">
                        <label for="district_id" class="text-xs text-muted font-weight-bold">Kecamatan (Sragen) <span class="text-danger">*</span></label>
                        <select name="district_id" id="district_id" class="form-control form-control-sm" required>
                            <option value="">-- Pilih Kecamatan --</option>
                            <?php foreach ($districts as $d): ?>
                                <option value="<?= $d['id'] ?>" <?= (old('district_id', $pemuda['district_id'] ?? '') == $d['id']) ? 'selected' : '' ?>>
                                    <?= esc($d['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-md-6 form-group mb-2">
                        <label for="village_id" class="text-xs text-muted font-weight-bold">Desa / Kelurahan <span class="text-danger">*</span></label>
                        <select name="village_id" id="village_id" class="form-control form-control-sm" required>
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

                    <div class="col-12 col-md-4 form-group mb-2">
                        <label for="dusun" class="text-xs text-muted font-weight-bold">Dusun / Dukuh</label>
                        <input type="text" 
                               class="form-control form-control-sm" 
                               id="dusun" 
                               name="dusun" 
                               value="<?= old('dusun', $pemuda['dusun'] ?? '') ?>" 
                               placeholder="Nama dusun/kampung">
                    </div>

                    <div class="col-6 col-md-4 form-group mb-2">
                        <label for="rt" class="text-xs text-muted font-weight-bold">RT</label>
                        <input type="text" class="form-control form-control-sm" id="rt" name="rt" value="<?= old('rt', $pemuda['rt'] ?? '') ?>" placeholder="01">
                    </div>

                    <div class="col-6 col-md-4 form-group mb-2">
                        <label for="rw" class="text-xs text-muted font-weight-bold">RW</label>
                        <input type="text" class="form-control form-control-sm" id="rw" name="rw" value="<?= old('rw', $pemuda['rw'] ?? '') ?>" placeholder="01">
                    </div>

                    <div class="col-12 form-group mb-2">
                        <label for="address_detail" class="text-xs text-muted font-weight-bold">Alamat Lengkap / Jalan <span class="text-danger">*</span></label>
                        <textarea class="form-control form-control-sm" 
                                  id="address_detail" 
                                  name="address_detail" 
                                  rows="2" 
                                  required><?= old('address_detail', $pemuda['address_detail'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- 4. PENDIDIKAN & PEKERJAAN -->
            <div class="mb-4">
                <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                    <span class="badge badge-primary mr-2" style="font-size: 0.9rem; border-radius: 50%; width: 26px; height: 26px; line-height: 18px; text-align: center;">4</span>
                    <h6 class="font-weight-bold text-dark mb-0 text-uppercase text-xs">Pendidikan &amp; Pekerjaan</h6>
                </div>

                <div class="row mb-2">
                    <div class="col-12 col-md-4 form-group mb-2">
                        <label for="education_level_id" class="text-xs text-muted font-weight-bold">Jenjang Pendidikan Terakhir <span class="text-danger">*</span></label>
                        <select name="education_level_id" id="education_level_id" class="form-control form-control-sm" required>
                            <option value="">-- Pilih Jenjang --</option>
                            <?php foreach ($educationLevels as $edu): ?>
                                <option value="<?= $edu['id'] ?>" <?= (old('education_level_id', $pemuda['education_level_id'] ?? '') == $edu['id']) ? 'selected' : '' ?>>
                                    <?= esc($edu['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-md-4 form-group mb-2">
                        <label for="school_name" class="text-xs text-muted font-weight-bold">Nama Sekolah / Kampus <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control form-control-sm" 
                               id="school_name" 
                               name="school_name" 
                               value="<?= old('school_name', $pemuda['school_name'] ?? '') ?>" 
                               placeholder="Nama SMA / SMK / Universitas" 
                               required>
                    </div>

                    <div class="col-12 col-md-4 form-group mb-2">
                        <label for="major" class="text-xs text-muted font-weight-bold">Jurusan / Program Studi</label>
                        <input type="text" 
                               class="form-control form-control-sm" 
                               id="major" 
                               name="major" 
                               value="<?= old('major', $pemuda['major'] ?? '') ?>" 
                               placeholder="Contoh: Teknik Informatika / IPA">
                    </div>

                    <div class="col-12 col-sm-6 col-md-4 form-group mb-2">
                        <label class="text-xs text-muted font-weight-bold">Status Pendidikan <span class="text-danger">*</span></label>
                        <select name="education_status" class="form-control form-control-sm" required>
                            <option value="lulus" <?= old('education_status', $pemuda['education_status'] ?? '') === 'lulus' ? 'selected' : '' ?>>Lulus</option>
                            <option value="sedang_sekolah" <?= old('education_status', $pemuda['education_status'] ?? '') === 'sedang_sekolah' ? 'selected' : '' ?>>Sedang Menempuh</option>
                            <option value="putus_sekolah" <?= old('education_status', $pemuda['education_status'] ?? '') === 'putus_sekolah' ? 'selected' : '' ?>>Putus Sekolah</option>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-md-4 form-group mb-2">
                        <label for="graduation_year" class="text-xs text-muted font-weight-bold">Tahun Kelulusan</label>
                        <input type="number" 
                               class="form-control form-control-sm" 
                               id="graduation_year" 
                               name="graduation_year" 
                               value="<?= old('graduation_year', $pemuda['graduation_year'] ?? '') ?>" 
                               placeholder="Contoh: 2022">
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-md-4 form-group mb-2">
                        <label for="job_status_id" class="text-xs text-muted font-weight-bold">Status Pekerjaan <span class="text-danger">*</span></label>
                        <select name="job_status_id" id="job_status_id" class="form-control form-control-sm" required>
                            <option value="">-- Pilih Status Pekerjaan --</option>
                            <?php foreach ($jobStatuses as $job): ?>
                                <option value="<?= $job['id'] ?>" <?= (old('job_status_id', $pemuda['job_status_id'] ?? '') == $job['id']) ? 'selected' : '' ?>>
                                    <?= esc($job['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-md-4 form-group mb-2">
                        <label for="job_title" class="text-xs text-muted font-weight-bold">Profesi / Jabatan</label>
                        <input type="text" 
                               class="form-control form-control-sm" 
                               id="job_title" 
                               name="job_title" 
                               value="<?= old('job_title', $pemuda['job_title'] ?? '') ?>" 
                               placeholder="Contoh: Staf Administrasi / Wirausaha">
                    </div>

                    <div class="col-12 col-md-4 form-group mb-2">
                        <label for="company_name" class="text-xs text-muted font-weight-bold">Nama Instansi / Perusahaan</label>
                        <input type="text" 
                               class="form-control form-control-sm" 
                               id="company_name" 
                               name="company_name" 
                               value="<?= old('company_name', $pemuda['company_name'] ?? '') ?>" 
                               placeholder="Nama kantor atau unit usaha">
                    </div>
                </div>
            </div>

            <!-- 5. KEAHLIAN & MINAT -->
            <div class="mb-4">
                <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                    <span class="badge badge-primary mr-2" style="font-size: 0.9rem; border-radius: 50%; width: 26px; height: 26px; line-height: 18px; text-align: center;">5</span>
                    <h6 class="font-weight-bold text-dark mb-0 text-uppercase text-xs">Keahlian (Skills) &amp; Minat (Interests)</h6>
                </div>

                <div class="mb-3">
                    <label class="text-xs font-weight-bold text-dark mb-2">Pilih Keahlian:</label>
                    <div class="row">
                        <?php foreach ($skills as $sk): 
                            $isChecked = isset($activeSkillIds) && in_array($sk['id'], $activeSkillIds);
                            $levelVal  = isset($activeSkillLevels[$sk['id']]) ? $activeSkillLevels[$sk['id']] : 'pemula';
                        ?>
                            <div class="col-12 col-sm-6 col-lg-4 mb-2">
                                <div class="p-2 border rounded bg-light">
                                    <div class="custom-control custom-checkbox mb-1">
                                        <input class="custom-control-input" 
                                               type="checkbox" 
                                               name="skills[<?= $sk['id'] ?>][selected]" 
                                               value="1" 
                                               id="sk_<?= $sk['id'] ?>"
                                               <?= $isChecked ? 'checked' : '' ?>>
                                        <label class="custom-control-label font-weight-bold text-xs" for="sk_<?= $sk['id'] ?>">
                                            <?= esc($sk['name']) ?>
                                        </label>
                                    </div>
                                    <select name="skills[<?= $sk['id'] ?>][level]" class="form-control form-control-sm">
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
                    <label class="text-xs font-weight-bold text-dark mb-2">Pilih Minat / Hobi:</label>
                    <div class="row">
                        <?php foreach ($interests as $in): 
                            $isCheckedIn = isset($activeInterestIds) && in_array($in['id'], $activeInterestIds);
                        ?>
                            <div class="col-6 col-sm-4 col-lg-3 mb-2">
                                <div class="p-2 border rounded bg-light">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" 
                                               type="checkbox" 
                                               name="interests[]" 
                                               value="<?= $in['id'] ?>" 
                                               id="in_<?= $in['id'] ?>"
                                               <?= $isCheckedIn ? 'checked' : '' ?>>
                                        <label class="custom-control-label text-xs" for="in_<?= $in['id'] ?>">
                                            <?= esc($in['name']) ?>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- 6. STATUS DATA & VERIFIKASI -->
            <div class="mb-4">
                <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                    <span class="badge badge-primary mr-2" style="font-size: 0.9rem; border-radius: 50%; width: 26px; height: 26px; line-height: 18px; text-align: center;">6</span>
                    <h6 class="font-weight-bold text-dark mb-0 text-uppercase text-xs">Status Verifikasi &amp; Status Data</h6>
                </div>

                <div class="row">
                    <div class="col-12 col-sm-6 form-group mb-2">
                        <label class="text-xs text-muted font-weight-bold">Status Verifikasi</label>
                        <?php if (in_array(session()->get('role'), ['superadmin', 'admin_cabang'], true)): ?>
                            <select name="status_verifikasi" class="form-control form-control-sm" required>
                                <option value="verified" <?= old('status_verifikasi', $pemuda['status_verifikasi'] ?? 'verified') === 'verified' ? 'selected' : '' ?>>Terverifikasi (Valid)</option>
                                <option value="pending" <?= old('status_verifikasi', $pemuda['status_verifikasi'] ?? '') === 'pending' ? 'selected' : '' ?>>Menunggu Verifikasi</option>
                                <option value="rejected" <?= old('status_verifikasi', $pemuda['status_verifikasi'] ?? '') === 'rejected' ? 'selected' : '' ?>>Ditolak</option>
                            </select>
                        <?php else: ?>
                            <!-- Read-only for admin_wilayah -->
                            <div class="p-2 bg-light rounded border">
                                <?php 
                                    $currV = $pemuda['status_verifikasi'] ?? 'pending';
                                    if ($currV === 'verified') echo '<span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Terverifikasi</span>';
                                    elseif ($currV === 'rejected') echo '<span class="badge badge-danger px-2 py-1"><i class="fas fa-times-circle mr-1"></i> Ditolak</span>';
                                    else echo '<span class="badge badge-warning px-2 py-1"><i class="fas fa-clock mr-1"></i> Menunggu Verifikasi</span>';
                                ?>
                                <div class="text-muted text-xs mt-1">
                                    <i class="fas fa-info-circle mr-1"></i> Hak verifikasi dipegang oleh <strong>Admin Cabang</strong> &amp; <strong>Superadmin</strong>.
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-12 col-sm-6 form-group mb-2">
                        <label class="text-xs text-muted font-weight-bold">Status Data</label>
                        <select name="status_data" class="form-control form-control-sm" required>
                            <option value="active" <?= old('status_data', $pemuda['status_data'] ?? 'active') === 'active' ? 'selected' : '' ?>>Aktif</option>
                            <option value="archived" <?= old('status_data', $pemuda['status_data'] ?? '') === 'archived' ? 'selected' : '' ?>>Arsip</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- SUBMIT BUTTONS -->
            <div class="d-flex justify-content-end pt-3 border-top">
                <a href="<?= base_url('admin/pemuda') ?>" class="btn btn-default btn-sm mr-2">Batal</a>
                <button type="submit" class="btn btn-primary btn-sm px-3">
                    <i class="fas fa-save mr-1"></i> <?= $mode === 'create' ? 'Simpan Data Pemuda' : 'Perbarui Data Pemuda' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function () {
        const wilayahData = <?= json_encode($wilayahWithCabang) ?>;
        const currentCabangId = "<?= old('cabang_id', $pemuda['cabang_id'] ?? '') ?>";
        const currentVillageId = "<?= old('village_id', $pemuda['village_id'] ?? '') ?>";

        function updateCabangOptions(wilayahId, selectedCabangId = null) {
            const $cabang = $('#cabang_id');
            $cabang.html('<option value="">-- Pilih Cabang --</option>');
            if (!wilayahId) return;

            const selectedWilayah = wilayahData.find(w => w.id == wilayahId);
            if (selectedWilayah && selectedWilayah.cabang) {
                $.each(selectedWilayah.cabang, function (i, c) {
                    const isSel = (selectedCabangId && selectedCabangId == c.id) ? 'selected' : '';
                    $cabang.append('<option value="' + c.id + '" ' + isSel + '>' + c.name + '</option>');
                });
            }
        }

        $('#wilayah_id').on('change', function () {
            updateCabangOptions($(this).val());
        });

        if ($('#wilayah_id').val()) {
            updateCabangOptions($('#wilayah_id').val(), currentCabangId);
        }

        // District to Village cascade
        $('#district_id').on('change', function () {
            const districtId = $(this).val();
            const $village = $('#village_id');
            $village.html('<option value="">-- Memuat Desa... --</option>');

            if (!districtId) {
                $village.html('<option value="">-- Pilih Kecamatan Terlebih Dahulu --</option>');
                return;
            }

            $.getJSON('<?= base_url('admin/ajax/villages/') ?>/' + districtId, function (data) {
                let opts = '<option value="">-- Pilih Desa / Kelurahan --</option>';
                $.each(data, function (i, v) {
                    opts += '<option value="' + v.id + '" ' + (currentVillageId == v.id ? 'selected' : '') + '>' + v.name + '</option>';
                });
                $village.html(opts);
            }).fail(function () {
                $village.html('<option value="">-- Gagal memuat data desa --</option>');
            });
        });
    });
</script>
<?= $this->endSection() ?>
