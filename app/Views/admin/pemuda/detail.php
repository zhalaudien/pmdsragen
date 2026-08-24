<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<!-- BREADCRUMB / BACK -->
<div class="mb-3">
    <a href="<?= base_url('admin/pemuda') ?>" class="btn btn-outline-secondary btn-sm rounded-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Pemuda
    </a>
</div>

<!-- PROFILE HERO CARD -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="row align-items-center g-4">
            <div class="col-auto">
                <div class="user-avatar user-avatar-lg">
                    <?= strtoupper(substr($pemuda['name'], 0, 1)) ?>
                </div>
            </div>
            <div class="col">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <h3 class="fw-bold text-dark mb-0"><?= esc($pemuda['name']) ?></h3>
                    
                    <!-- Verif badge -->
                    <?php if ($pemuda['status_verifikasi'] === 'verified'): ?>
                        <span class="badge badge-verif-verified rounded-pill px-3 py-1 fw-semibold"><i class="bi bi-check-circle-fill me-1"></i> Terverifikasi</span>
                    <?php elseif ($pemuda['status_verifikasi'] === 'rejected'): ?>
                        <span class="badge badge-verif-rejected rounded-pill px-3 py-1 fw-semibold"><i class="bi bi-x-circle-fill me-1"></i> Ditolak</span>
                    <?php else: ?>
                        <span class="badge badge-verif-pending rounded-pill px-3 py-1 fw-semibold"><i class="bi bi-clock-history me-1"></i> Menunggu Verifikasi</span>
                    <?php endif; ?>

                    <!-- Active / Archived badge -->
                    <?php if ($pemuda['status_data'] === 'archived'): ?>
                        <span class="badge bg-secondary rounded-pill px-3 py-1 fw-semibold"><i class="bi bi-archive-fill me-1"></i> Data Arsip</span>
                    <?php else: ?>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 fw-semibold">Aktif</span>
                    <?php endif; ?>
                </div>

                <div class="text-muted d-flex flex-wrap align-items-center gap-3 small mt-2">
                    <div><i class="bi bi-card-text text-primary me-1"></i> No. Reg: <strong class="text-dark"><?= esc($pemuda['registration_number']) ?></strong></div>
                    <div><i class="bi bi-diagram-3 text-success me-1"></i> Cabang: <strong class="text-dark"><?= esc($pemuda['cabang_name']) ?></strong> (<?= esc($pemuda['wilayah_name']) ?>)</div>
                    <div><i class="bi bi-calendar-event text-secondary me-1"></i> Terdaftar: <?= date('d F Y, H:i', strtotime($pemuda['created_at'])) ?> WIB</div>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="col-12 col-xl-auto text-xl-end">
                <div class="d-flex flex-wrap gap-2">
                    <!-- Cetak button -->
                    <a href="<?= base_url('admin/pemuda/cetak/' . $pemuda['id']) ?>" target="_blank" class="btn btn-outline-secondary rounded-3">
                        <i class="bi bi-printer me-1"></i> Cetak Dokumen
                    </a>

                    <!-- Edit button -->
                    <a href="<?= base_url('admin/pemuda/edit/' . $pemuda['id']) ?>" class="btn btn-primary rounded-3">
                        <i class="bi bi-pencil-square me-1"></i> Edit Data
                    </a>

                    <!-- Verifikasi dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-outline-success dropdown-toggle rounded-3" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-patch-check me-1"></i> Ubah Status
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 p-1">
                            <li>
                                <form action="<?= base_url('admin/pemuda/verifikasi/' . $pemuda['id']) ?>" method="POST">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="status" value="verified">
                                    <button type="submit" class="dropdown-item py-2 text-success small rounded-2">
                                        <i class="bi bi-check-circle-fill me-2"></i> Verifikasi (Setujui)
                                    </button>
                                </form>
                            </li>
                            <li>
                                <form action="<?= base_url('admin/pemuda/verifikasi/' . $pemuda['id']) ?>" method="POST">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="dropdown-item py-2 text-danger small rounded-2">
                                        <i class="bi bi-x-circle-fill me-2"></i> Tolak Pendaftaran
                                    </button>
                                </form>
                            </li>
                            <li>
                                <form action="<?= base_url('admin/pemuda/verifikasi/' . $pemuda['id']) ?>" method="POST">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="status" value="pending">
                                    <button type="submit" class="dropdown-item py-2 text-warning small rounded-2">
                                        <i class="bi bi-clock-history me-2"></i> Kembalikan ke Menunggu
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- KOLOM KIRI: DATA PRIBADI & ALAMAT -->
    <div class="col-12 col-lg-6">
        <!-- 1. DATA PRIBADI -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header-clean">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-person-badge-fill text-primary"></i>
                    <span>Data Pribadi & Kontak</span>
                </div>
            </div>
            <div class="card-body p-4">
                <table class="table table-borderless align-middle mb-0" style="font-size: 0.92rem;">
                    <tbody>
                        <tr>
                            <td class="text-muted" style="width: 170px;">Nama Lengkap</td>
                            <td class="fw-bold text-dark">: <?= esc($pemuda['name']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jenis Kelamin</td>
                            <td>: <?= $pemuda['gender'] === 'L' ? '<span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1">Laki-laki</span>' : '<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1">Perempuan</span>' ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status Pernikahan</td>
                            <td class="fw-semibold text-dark">: <?= ucwords(str_replace('_', ' ', $pemuda['marital_status'] ?? 'belum_menikah')) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Golongan Darah</td>
                            <td class="fw-semibold text-dark">: <?= esc($pemuda['blood_type'] ?: 'Tidak Tahu / Belum Cek') ?></td>
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
                                    echo " <span class='text-muted small'>({$age} Tahun)</span>";
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
                                       class="btn btn-sm btn-outline-success rounded-pill px-3 py-0">
                                        <i class="bi bi-whatsapp"></i> <?= esc($pemuda['phone']) ?>
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
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header-clean">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-geo-alt-fill text-danger"></i>
                    <span>Domisili & Alamat Lengkap</span>
                </div>
            </div>
            <div class="card-body p-4">
                <table class="table table-borderless align-middle mb-0" style="font-size: 0.92rem;">
                    <tbody>
                        <tr>
                            <td class="text-muted" style="width: 170px;">Cabang Pemuda</td>
                            <td class="fw-bold text-primary">: <?= esc($pemuda['cabang_name']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Wilayah Organisasi</td>
                            <td class="fw-semibold text-dark">: <?= esc($pemuda['wilayah_name']) ?></td>
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
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header-clean">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-briefcase-fill text-warning"></i>
                    <span>Pendidikan & Pekerjaan</span>
                </div>
            </div>
            <div class="card-body p-4">
                <h6 class="fw-bold text-primary mb-3"><i class="bi bi-mortarboard-fill me-1"></i> Riwayat Pendidikan</h6>
                <table class="table table-borderless align-middle mb-4" style="font-size: 0.9rem;">
                    <tbody>
                        <tr>
                            <td class="text-muted" style="width: 170px;">Jenjang Terakhir</td>
                            <td class="fw-bold text-dark">: <?= esc($pemuda['education_level_name'] ?: '-') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nama Sekolah / Kampus</td>
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

                <hr class="my-3 text-muted opacity-25">

                <h6 class="fw-bold text-warning mb-3"><i class="bi bi-building me-1"></i> Status Pekerjaan & Profesi</h6>
                <table class="table table-borderless align-middle mb-0" style="font-size: 0.9rem;">
                    <tbody>
                        <tr>
                            <td class="text-muted" style="width: 170px;">Status Pekerjaan</td>
                            <td class="fw-bold text-dark">: <?= esc($pemuda['job_status_name'] ?: '-') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Profesi / Jabatan</td>
                            <td class="text-dark">: <?= esc($pemuda['job_title'] ?: '-') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nama Instansi / Usaha</td>
                            <td class="text-dark">: <?= esc($pemuda['company_name'] ?: '-') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Bidang Usaha / Sektor</td>
                            <td class="text-dark">: <?= esc($pemuda['business_field'] ?: '-') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 4. ORGANISASI -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header-clean">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-people-fill text-success"></i>
                    <span>Keikutsertaan Organisasi</span>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (empty($pemuda['organisasi'])): ?>
                    <div class="p-4 text-center text-muted small">Tidak ada riwayat organisasi yang tercatat.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Nama Organisasi</th>
                                    <th>Jabatan</th>
                                    <th>Masa Keanggotaan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pemuda['organisasi'] as $org): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-dark"><?= esc($org['organization_name']) ?></td>
                                        <td><span class="badge bg-light text-dark border"><?= esc($org['position'] ?: 'Anggota') ?></span></td>
                                        <td class="text-muted small">
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
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header-clean">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-stars text-info"></i>
                    <span>Keahlian & Minat</span>
                </div>
            </div>
            <div class="card-body p-4">
                <!-- Skills -->
                <div class="mb-4">
                    <div class="fw-semibold text-dark small mb-2"><i class="bi bi-tools text-primary me-1"></i> Keahlian / Keterampilan:</div>
                    <?php if (empty($pemuda['skills'])): ?>
                        <div class="text-muted small fst-italic">Belum ada keahlian yang ditambahkan.</div>
                    <?php else: ?>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($pemuda['skills'] as $sk): ?>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2">
                                    <?= esc($sk['skill_name']) ?>
                                    <span class="badge bg-primary text-white rounded-pill ms-1" style="font-size: 0.68rem;"><?= esc($sk['level']) ?></span>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <hr class="my-3 text-muted opacity-25">

                <!-- Interests -->
                <div>
                    <div class="fw-semibold text-dark small mb-2"><i class="bi bi-heart-fill text-danger me-1"></i> Minat / Hobi:</div>
                    <?php if (empty($pemuda['interests'])): ?>
                        <div class="text-muted small fst-italic">Belum ada minat yang ditambahkan.</div>
                    <?php else: ?>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($pemuda['interests'] as $in): ?>
                                <span class="badge bg-light text-dark border rounded-pill px-3 py-2">
                                    <i class="bi bi-check2 text-success me-1"></i> <?= esc($in['interest_name']) ?>
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
