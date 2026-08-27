<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<!-- BREADCRUMB / BACK -->
<div class="mb-3">
    <a href="<?= base_url('admin/pemuda') ?>" class="btn btn-default btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Pemuda
    </a>
</div>

<!-- PROFILE HERO CARD -->
<div class="card card-primary card-outline shadow-sm mb-3">
    <div class="card-body p-3">
        <div class="row align-items-center">
            <div class="col-auto">
                <div class="user-avatar user-avatar-lg">
                    <?= strtoupper(substr($pemuda['name'], 0, 1)) ?>
                </div>
            </div>
            <div class="col">
                <div class="d-flex flex-wrap align-items-center mb-1">
                    <h4 class="font-weight-bold text-dark mb-0 mr-2"><?= esc($pemuda['name']) ?></h4>
                    
                    <!-- Verif badge -->
                    <?php if ($pemuda['status_verifikasi'] === 'verified'): ?>
                        <span class="badge badge-success px-2 py-1 font-weight-bold mr-1"><i class="fas fa-check-circle mr-1"></i> Terverifikasi</span>
                    <?php elseif ($pemuda['status_verifikasi'] === 'rejected'): ?>
                        <span class="badge badge-danger px-2 py-1 font-weight-bold mr-1"><i class="fas fa-times-circle mr-1"></i> Ditolak</span>
                    <?php else: ?>
                        <span class="badge badge-warning px-2 py-1 font-weight-bold mr-1"><i class="fas fa-clock mr-1"></i> Menunggu Verifikasi</span>
                    <?php endif; ?>

                    <!-- Active / Archived badge -->
                    <?php if ($pemuda['status_data'] === 'archived'): ?>
                        <span class="badge badge-secondary px-2 py-1 font-weight-bold"><i class="fas fa-archive mr-1"></i> Data Arsip</span>
                    <?php else: ?>
                        <span class="badge badge-primary px-2 py-1 font-weight-bold">Aktif</span>
                    <?php endif; ?>
                </div>

                <div class="text-muted d-flex flex-wrap align-items-center text-xs mt-2">
                    <div class="mr-3"><i class="fas fa-id-card text-primary mr-1"></i> No. Reg: <strong class="text-dark"><?= esc($pemuda['registration_number']) ?></strong></div>
                    <div class="mr-3"><i class="fas fa-sitemap text-success mr-1"></i> Cabang: <strong class="text-dark"><?= esc($pemuda['cabang_name']) ?></strong> (<?= esc($pemuda['wilayah_name']) ?>)</div>
                    <div><i class="fas fa-calendar-alt text-secondary mr-1"></i> Terdaftar: <?= date('d F Y, H:i', strtotime($pemuda['created_at'])) ?> WIB</div>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="col-12 col-xl-auto text-xl-right mt-3 mt-xl-0">
                <div class="btn-group">
                    <a href="<?= base_url('admin/pemuda/cetak/' . $pemuda['id']) ?>" target="_blank" class="btn btn-default btn-sm">
                        <i class="fas fa-print mr-1"></i> Cetak Dokumen
                    </a>

                    <a href="<?= base_url('admin/pemuda/edit/' . $pemuda['id']) ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit mr-1"></i> Edit Data
                    </a>

                    <?php if (in_array(session()->get('role'), ['superadmin', 'admin_cabang'], true)): ?>
                        <div class="btn-group">
                            <button class="btn btn-success btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                                <i class="fas fa-shield-alt mr-1"></i> Status
                            </button>
                            <div class="dropdown-menu dropdown-menu-right shadow border-0 p-1">
                                <form action="<?= base_url('admin/pemuda/verifikasi/' . $pemuda['id']) ?>" method="POST">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="status" value="verified">
                                    <button type="submit" class="dropdown-item py-1 text-success text-xs font-weight-bold">
                                        <i class="fas fa-check-circle mr-1"></i> Setujui (Valid)
                                    </button>
                                </form>
                                <form action="<?= base_url('admin/pemuda/verifikasi/' . $pemuda['id']) ?>" method="POST">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="dropdown-item py-1 text-danger text-xs font-weight-bold">
                                        <i class="fas fa-times-circle mr-1"></i> Tolak Pendaftaran
                                    </button>
                                </form>
                                <form action="<?= base_url('admin/pemuda/verifikasi/' . $pemuda['id']) ?>" method="POST">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="status" value="pending">
                                    <button type="submit" class="dropdown-item py-1 text-warning text-xs font-weight-bold">
                                        <i class="fas fa-clock mr-1"></i> Set Menunggu
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- KOLOM KIRI: DATA PRIBADI & ALAMAT -->
    <div class="col-12 col-lg-6">
        <!-- 1. DATA PRIBADI -->
        <div class="card card-primary card-outline shadow-sm mb-3">
            <div class="card-header border-0">
                <h3 class="card-title font-weight-bold text-sm">
                    <i class="fas fa-user-circle text-primary mr-1"></i> Data Pribadi &amp; Kontak
                </h3>
            </div>
            <div class="card-body p-3">
                <table class="table table-sm table-borderless align-middle mb-0" style="font-size: 0.88rem;">
                    <tbody>
                        <tr>
                            <td class="text-muted" style="width: 160px;">Nama Lengkap</td>
                            <td class="font-weight-bold text-dark">: <?= esc($pemuda['name']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jenis Kelamin</td>
                            <td>: <?= $pemuda['gender'] === 'L' ? '<span class="badge badge-primary">Laki-laki</span>' : '<span class="badge badge-danger">Perempuan</span>' ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status Pernikahan</td>
                            <td class="font-weight-semibold text-dark">: <?= ucwords(str_replace('_', ' ', $pemuda['marital_status'] ?? 'belum_menikah')) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Golongan Darah</td>
                            <td class="font-weight-semibold text-dark">: <?= esc($pemuda['blood_type'] ?: 'Tidak Tahu / Belum Cek') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tempat, Tgl Lahir</td>
                            <td class="text-dark">
                                : <?= esc($pemuda['birth_place'] ?: '-') ?>, 
                                <?= $pemuda['birth_date'] ? date('d F Y', strtotime($pemuda['birth_date'])) : '-' ?>
                                <?php 
                                if ($pemuda['birth_date']) {
                                    $birth = new \DateTime($pemuda['birth_date']);
                                    $today = new \DateTime();
                                    $age = $today->diff($birth)->y;
                                    echo " <span class='text-muted text-xs'>({$age} Tahun)</span>";
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">No. Handphone / WA</td>
                            <td>
                                : <?php if (!empty($pemuda['phone'])): ?>
                                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', (str_starts_with($pemuda['phone'], '0') ? ('62' . substr($pemuda['phone'], 1)) : $pemuda['phone'])) ?>" 
                                       target="_blank" 
                                       class="badge badge-success px-2 py-1">
                                        <i class="fab fa-whatsapp mr-1"></i> <?= esc($pemuda['phone']) ?>
                                    </a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Email</td>
                            <td class="text-dark">: <?= esc($pemuda['email'] ?: '-') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. DATA ALAMAT & DOMISILI -->
        <div class="card card-danger card-outline shadow-sm mb-3">
            <div class="card-header border-0">
                <h3 class="card-title font-weight-bold text-sm">
                    <i class="fas fa-map-marker-alt text-danger mr-1"></i> Domisili &amp; Alamat Lengkap
                </h3>
            </div>
            <div class="card-body p-3">
                <table class="table table-sm table-borderless align-middle mb-0" style="font-size: 0.88rem;">
                    <tbody>
                        <tr>
                            <td class="text-muted" style="width: 160px;">Cabang Pemuda</td>
                            <td class="font-weight-bold text-primary">: <?= esc($pemuda['cabang_name']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Wilayah Organisasi</td>
                            <td class="font-weight-semibold text-dark">: <?= esc($pemuda['wilayah_name']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Desa / Kelurahan</td>
                            <td class="text-dark">: <?= esc($pemuda['village_name'] ?: '-') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Kecamatan</td>
                            <td class="text-dark">: <?= esc($pemuda['district_name'] ?: '-') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Kabupaten / Kota</td>
                            <td class="text-dark">: <?= esc($pemuda['regency_name'] ?: 'Kabupaten Sragen') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Provinsi</td>
                            <td class="text-dark">: <?= esc($pemuda['province_name'] ?: 'Jawa Tengah') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Dusun / RT / RW</td>
                            <td class="text-dark">: <?= esc($pemuda['dusun'] ?: '-') ?>, RT <?= esc($pemuda['rt'] ?: '-') ?> / RW <?= esc($pemuda['rw'] ?: '-') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Alamat Lengkap</td>
                            <td class="text-dark">: <?= nl2br(esc($pemuda['address_detail'] ?: '-')) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- KOLOM KANAN: PENDIDIKAN, PEKERJAAN, ORGANISASI, SKILLS, INTERESTS -->
    <div class="col-12 col-lg-6">
        <!-- 3. PENDIDIKAN & PEKERJAAN -->
        <div class="card card-warning card-outline shadow-sm mb-3">
            <div class="card-header border-0">
                <h3 class="card-title font-weight-bold text-sm">
                    <i class="fas fa-briefcase text-warning mr-1"></i> Pendidikan &amp; Pekerjaan
                </h3>
            </div>
            <div class="card-body p-3">
                <h6 class="font-weight-bold text-primary mb-2 text-xs text-uppercase"><i class="fas fa-graduation-cap mr-1"></i> Riwayat Pendidikan</h6>
                <table class="table table-sm table-borderless align-middle mb-3" style="font-size: 0.88rem;">
                    <tbody>
                        <tr>
                            <td class="text-muted" style="width: 160px;">Jenjang Terakhir</td>
                            <td class="font-weight-bold text-dark">: <?= esc($pemuda['education_level_name'] ?: '-') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Sekolah / Kampus</td>
                            <td class="text-dark">: <?= esc($pemuda['school_name'] ?: '-') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jurusan</td>
                            <td class="text-dark">: <?= esc($pemuda['major'] ?: '-') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status / Th. Lulus</td>
                            <td class="text-dark">: <?= ucwords(str_replace('_', ' ', $pemuda['education_status'] ?? '-')) ?> (<?= esc($pemuda['graduation_year'] ?: '-') ?>)</td>
                        </tr>
                    </tbody>
                </table>

                <hr class="my-2">

                <h6 class="font-weight-bold text-warning mb-2 text-xs text-uppercase"><i class="fas fa-building mr-1"></i> Status Pekerjaan &amp; Profesi</h6>
                <table class="table table-sm table-borderless align-middle mb-0" style="font-size: 0.88rem;">
                    <tbody>
                        <tr>
                            <td class="text-muted" style="width: 160px;">Status Pekerjaan</td>
                            <td class="font-weight-bold text-dark">: <?= esc($pemuda['job_status_name'] ?: '-') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Profesi / Jabatan</td>
                            <td class="text-dark">: <?= esc($pemuda['job_title'] ?: '-') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Instansi / Usaha</td>
                            <td class="text-dark">: <?= esc($pemuda['company_name'] ?: '-') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Sektor Usaha</td>
                            <td class="text-dark">: <?= esc($pemuda['business_field'] ?: '-') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 4. ORGANISASI -->
        <div class="card card-success card-outline shadow-sm mb-3">
            <div class="card-header border-0">
                <h3 class="card-title font-weight-bold text-sm">
                    <i class="fas fa-users text-success mr-1"></i> Keikutsertaan Organisasi
                </h3>
            </div>
            <div class="card-body p-0">
                <?php if (empty($pemuda['organisasi'])): ?>
                    <div class="p-3 text-center text-muted text-xs">Tidak ada riwayat organisasi yang tercatat.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0" style="font-size: 0.85rem;">
                            <thead class="thead-light">
                                <tr>
                                    <th class="pl-3">Nama Organisasi</th>
                                    <th>Jabatan</th>
                                    <th>Masa Keanggotaan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pemuda['organisasi'] as $org): ?>
                                    <tr>
                                        <td class="pl-3 font-weight-bold text-dark"><?= esc($org['organization_name']) ?></td>
                                        <td><span class="badge badge-light border"><?= esc($org['position'] ?: 'Anggota') ?></span></td>
                                        <td class="text-muted text-xs">
                                            <?= $org['join_date'] ? date('Y', strtotime($org['join_date'])) : 'Aktif' ?>
                                            <?= $org['end_date'] ? (' - ' . date('Y', strtotime($org['end_date']))) : '' ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- 5. KEAHLIAN & MINAT -->
        <div class="card card-info card-outline shadow-sm mb-3">
            <div class="card-header border-0">
                <h3 class="card-title font-weight-bold text-sm">
                    <i class="fas fa-star text-info mr-1"></i> Keahlian &amp; Minat
                </h3>
            </div>
            <div class="card-body p-3">
                <!-- Skills -->
                <div class="mb-3">
                    <div class="font-weight-bold text-dark text-xs mb-2"><i class="fas fa-tools text-primary mr-1"></i> Keahlian / Keterampilan:</div>
                    <?php if (empty($pemuda['skills'])): ?>
                        <div class="text-muted text-xs font-italic">Belum ada keahlian yang ditambahkan.</div>
                    <?php else: ?>
                        <div>
                            <?php foreach ($pemuda['skills'] as $sk): ?>
                                <span class="badge badge-primary px-2 py-1 mr-1 mb-1">
                                    <?= esc($sk['skill_name']) ?>
                                    <span class="badge badge-light text-dark ml-1"><?= esc($sk['level']) ?></span>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <hr class="my-2">

                <!-- Interests -->
                <div>
                    <div class="font-weight-bold text-dark text-xs mb-2"><i class="fas fa-heart text-danger mr-1"></i> Minat / Hobi:</div>
                    <?php if (empty($pemuda['interests'])): ?>
                        <div class="text-muted text-xs font-italic">Belum ada minat yang ditambahkan.</div>
                    <?php else: ?>
                        <div>
                            <?php foreach ($pemuda['interests'] as $in): ?>
                                <span class="badge badge-light border px-2 py-1 mr-1 mb-1">
                                    <i class="fas fa-check text-success mr-1"></i> <?= esc($in['interest_name']) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
