<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<!-- WELCOME BANNER -->
<?php 
    $userRole    = session()->get('role');
    $wilayahName = session()->get('wilayah_name') ?? ('Wilayah ' . session()->get('wilayah_id'));
    $cabangName  = session()->get('cabang_name') ?? ('Cabang ' . session()->get('cabang_id'));
?>
<div class="card border-0 text-white mb-4 shadow-sm dashboard-hero-card">
    <div class="card-body p-4 p-lg-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 bg-white bg-opacity-10 rounded-pill mb-3 border border-white border-opacity-10">
                    <i class="bi bi-shield-check text-info"></i>
                    <span class="small fw-semibold">
                        <?php if ($userRole === 'superadmin'): ?>
                            Dashboard Super Administrator
                        <?php elseif ($userRole === 'admin_wilayah'): ?>
                            Dashboard Admin Wilayah: <?= esc($wilayahName) ?>
                        <?php else: ?>
                            Dashboard Admin Cabang: <?= esc($cabangName) ?>
                        <?php endif; ?>
                    </span>
                </div>
                <h2 class="fw-bold mb-2">Selamat Datang, <?= esc($user['name'] ?? 'Administrator') ?>! 👋</h2>
                <p class="text-white-50 mb-4 mb-lg-0">
                    <?php if ($userRole === 'superadmin'): ?>
                        Anda memiliki hak akses penuh untuk memantau data pemuda, distribusi per wilayah, cabang, jenjang pendidikan, pekerjaan, serta verifikasi pendaftaran di seluruh Kabupaten Sragen.
                    <?php elseif ($userRole === 'admin_wilayah'): ?>
                        Anda memantau data pemuda, statistik, dan sebaran seluruh cabang yang berada dalam lingkup <strong><?= esc($wilayahName) ?></strong>.
                    <?php else: ?>
                        Anda mengelola, memverifikasi, dan memantau perkembangan data pemuda khusus pada lingkup <strong><?= esc($cabangName) ?></strong>.
                    <?php endif; ?>
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                    <a href="<?= base_url('admin/pemuda/tambah') ?>" class="btn btn-primary px-3 py-2 rounded-3 shadow-sm">
                        <i class="bi bi-person-plus-fill me-1"></i> Tambah Pemuda
                    </a>
                    <a href="<?= base_url('admin/pemuda') ?>" class="btn btn-light px-3 py-2 rounded-3 shadow-sm">
                        <i class="bi bi-table me-1"></i> Kelola Data
                    </a>
                    <a href="<?= base_url('admin/pemuda/export') ?>" class="btn btn-outline-light px-3 py-2 rounded-3">
                        <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export Excel
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- STATS CARDS ROW 1 -->
<div class="row g-3 mb-4">
    <!-- Total Pemuda -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card stat-card-blue border-0 h-100 shadow-sm">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Total Pemuda</div>
                    <div class="fs-2 fw-bold text-dark mt-1"><?= number_format($stats['summary']['total'] ?? 0) ?></div>
                    <div class="small text-muted mt-1"><i class="bi bi-people-fill text-primary me-1"></i> Terdaftar di sistem</div>
                </div>
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Menunggu Verifikasi -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card stat-card-yellow border-0 h-100 shadow-sm">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Menunggu Verifikasi</div>
                    <div class="fs-2 fw-bold text-warning mt-1"><?= number_format($stats['summary']['pending'] ?? 0) ?></div>
                    <a href="<?= base_url('admin/pemuda?status_verifikasi=pending') ?>" class="small text-decoration-none text-warning fw-semibold mt-1 d-inline-block">
                        Proses sekarang <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="stat-icon bg-warning-subtle text-warning">
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Terverifikasi -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card stat-card-green border-0 h-100 shadow-sm">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Terverifikasi</div>
                    <div class="fs-2 fw-bold text-success mt-1"><?= number_format($stats['summary']['verified'] ?? 0) ?></div>
                    <div class="small text-muted mt-1"><i class="bi bi-check2-circle text-success me-1"></i> Data valid</div>
                </div>
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="bi bi-patch-check-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Ditolak / Tidak Valid -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card stat-card-red border-0 h-100 shadow-sm">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Ditolak</div>
                    <div class="fs-2 fw-bold text-danger mt-1"><?= number_format($stats['summary']['rejected'] ?? 0) ?></div>
                    <div class="small text-muted mt-1"><i class="bi bi-x-circle text-danger me-1"></i> Perlu perbaikan</div>
                </div>
                <div class="stat-icon bg-danger-subtle text-danger">
                    <i class="bi bi-x-octagon-fill"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- STATS CARDS ROW 2 (Master scope info) -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card p-3 border-0 shadow-sm h-100 d-flex flex-row align-items-center gap-3">
            <div class="stat-icon bg-purple-subtle text-purple rounded-3">
                <i class="bi bi-geo-alt-fill"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold">Wilayah</div>
                <div class="fs-4 fw-bold text-dark"><?= number_format($stats['totalWilayah'] ?? 4) ?> <span class="fs-6 fw-normal text-muted">Wilayah</span></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card p-3 border-0 shadow-sm h-100 d-flex flex-row align-items-center gap-3">
            <div class="stat-icon bg-info-subtle text-info rounded-3">
                <i class="bi bi-diagram-3-fill"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold">Cabang</div>
                <div class="fs-4 fw-bold text-dark"><?= number_format($stats['totalCabang'] ?? 0) ?> <span class="fs-6 fw-normal text-muted">Cabang</span></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card p-3 border-0 shadow-sm h-100 d-flex flex-row align-items-center gap-3">
            <div class="stat-icon bg-primary-subtle text-primary rounded-3">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold">Pengguna / Admin</div>
                <div class="fs-4 fw-bold text-dark"><?= number_format($stats['totalUsers'] ?? 0) ?> <span class="fs-6 fw-normal text-muted">User</span></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card p-3 border-0 shadow-sm h-100 d-flex flex-row align-items-center gap-3">
            <div class="stat-icon bg-secondary-subtle text-secondary rounded-3">
                <i class="bi bi-archive-fill"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold">Data Arsip</div>
                <div class="fs-4 fw-bold text-dark"><?= number_format($stats['summary']['archived'] ?? 0) ?> <span class="fs-6 fw-normal text-muted">Data</span></div>
            </div>
        </div>
    </div>
</div>

<!-- CHARTS ROW 1: Pemuda per Wilayah & Gender & Marital -->
<div class="row g-4 mb-4">
    <!-- Chart Wilayah -->
    <div class="col-12 col-xl-7">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header-clean">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-bar-chart-fill text-primary"></i>
                    <span>Sebaran Data Pemuda per Wilayah</span>
                </div>
                <span class="badge bg-light text-muted border">Kab. Sragen</span>
            </div>
            <div class="card-body p-4">
                <div style="position: relative; height: 280px;">
                    <canvas id="chartWilayah"></canvas>
                </div>
                <div class="row text-center mt-3 g-2">
                    <?php 
                    $colors = ['#0284c7', '#059669', '#f59e0b', '#7c3aed'];
                    $i = 0;
                    foreach ($stats['wilayahStats'] as $w): 
                    ?>
                        <div class="col-6 col-md-3">
                            <div class="p-2 border rounded-3 bg-light">
                                <div class="small fw-semibold" style="color: <?= $colors[$i % 4] ?>;"><?= esc($w['name']) ?></div>
                                <div class="fs-5 fw-bold text-dark"><?= number_format($w['total']) ?></div>
                                <div class="text-muted" style="font-size: 0.72rem;"><?= esc($w['code']) ?></div>
                            </div>
                        </div>
                    <?php 
                    $i++;
                    endforeach; 
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Gender & Status Pernikahan -->
    <div class="col-12 col-xl-5">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header-clean">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-pie-chart-fill text-primary"></i>
                    <span>Demografi Gender & Pernikahan</span>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row align-items-center g-3">
                    <div class="col-6 text-center">
                        <div style="position: relative; height: 170px;">
                            <canvas id="chartGender"></canvas>
                        </div>
                        <div class="mt-2 fw-semibold small text-dark">Jenis Kelamin</div>
                        <div class="d-flex justify-content-center gap-2 mt-1 small">
                            <span class="text-primary fw-semibold"><i class="bi bi-gender-male"></i> L: <?= $stats['genderData']['L'] ?></span>
                            <span class="text-danger fw-semibold"><i class="bi bi-gender-female"></i> P: <?= $stats['genderData']['P'] ?></span>
                        </div>
                    </div>
                    <div class="col-6 text-center border-start">
                        <div style="position: relative; height: 170px;">
                            <canvas id="chartMarital"></canvas>
                        </div>
                        <div class="mt-2 fw-semibold small text-dark">Status Pernikahan</div>
                        <div class="text-muted" style="font-size: 0.72rem;">Belum / Sudah / Janda / Duda</div>
                    </div>
                </div>

                <hr class="my-3 text-muted opacity-25">

                <div class="row g-2 text-center" style="font-size: 0.8rem;">
                    <div class="col-3">
                        <div class="p-1 bg-light rounded border">
                            <div class="text-muted" style="font-size: 0.68rem;">Belum Nikah</div>
                            <div class="fw-bold text-primary"><?= $stats['maritalData']['belum_menikah'] ?></div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-1 bg-light rounded border">
                            <div class="text-muted" style="font-size: 0.68rem;">Menikah</div>
                            <div class="fw-bold text-success"><?= $stats['maritalData']['sudah_menikah'] ?></div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-1 bg-light rounded border">
                            <div class="text-muted" style="font-size: 0.68rem;">Janda</div>
                            <div class="fw-bold text-warning"><?= $stats['maritalData']['janda'] ?></div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-1 bg-light rounded border">
                            <div class="text-muted" style="font-size: 0.68rem;">Duda</div>
                            <div class="fw-bold text-secondary"><?= $stats['maritalData']['duda'] ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CHARTS ROW 2: Pendidikan & Pekerjaan -->
<div class="row g-4 mb-4">
    <!-- Chart Pendidikan -->
    <div class="col-12 col-lg-6">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header-clean">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-mortarboard-fill text-info"></i>
                    <span>Statistik Jenjang Pendidikan</span>
                </div>
            </div>
            <div class="card-body p-4">
                <div style="position: relative; height: 260px;">
                    <canvas id="chartPendidikan"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Pekerjaan -->
    <div class="col-12 col-lg-6">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header-clean">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-briefcase-fill text-warning"></i>
                    <span>Statistik Status Pekerjaan</span>
                </div>
            </div>
            <div class="card-body p-4">
                <div style="position: relative; height: 260px;">
                    <canvas id="chartPekerjaan"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SECTION 3: TOP CABANG & RECENT REGISTRATIONS -->
<div class="row g-4">
    <!-- 10 Cabang Terbanyak -->
    <div class="col-12 col-xl-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header-clean">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-trophy-fill text-warning"></i>
                    <span>
                        <?php if ($userRole === 'superadmin'): ?>
                            10 Cabang Terbanyak
                        <?php elseif ($userRole === 'admin_wilayah'): ?>
                            Cabang Terbanyak di <?= esc($wilayahName) ?>
                        <?php else: ?>
                            Statistik Cabang <?= esc($cabangName) ?>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php if (empty($stats['topCabangStats'])): ?>
                        <div class="p-4 text-center text-muted small">Belum ada data pemuda terdaftar.</div>
                    <?php else: ?>
                        <?php 
                        $maxTotal = max(array_column($stats['topCabangStats'], 'total')) ?: 1;
                        foreach ($stats['topCabangStats'] as $idx => $c): 
                            $percent = round(($c['total'] / $maxTotal) * 100);
                        ?>
                            <div class="list-group-item px-3 py-2 border-bottom border-light">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-light text-dark rounded-circle badge-number-circle"><?= $idx + 1 ?></span>
                                        <span class="fw-semibold text-dark small"><?= esc($c['name']) ?></span>
                                    </div>
                                    <div class="text-end">
                                        <span class="fw-bold text-primary small"><?= number_format($c['total']) ?></span>
                                        <span class="text-muted" style="font-size: 0.72rem;">pemuda</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 5px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $percent ?>%"></div>
                                    </div>
                                    <span class="text-muted" style="font-size: 0.68rem;"><?= esc($c['wilayah_name']) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ($userRole === 'superadmin'): ?>
                <div class="card-footer bg-white border-0 text-center py-2">
                    <a href="<?= base_url('admin/cabang') ?>" class="small text-decoration-none fw-semibold">Kelola Semua Cabang <i class="bi bi-chevron-right"></i></a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pendaftaran Pemuda Terbaru -->
    <div class="col-12 col-xl-8">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header-clean">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history text-primary"></i>
                    <span>Pendaftaran Pemuda Terbaru</span>
                </div>
                <a href="<?= base_url('admin/pemuda') ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                    Lihat Semua Data
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">No. Registrasi</th>
                                <th>Nama Lengkap</th>
                                <th>Cabang / Wilayah</th>
                                <th>Status Verifikasi</th>
                                <th>Tgl Daftar</th>
                                <th class="text-end pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($stats['recentRegistrations'])): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada data pemuda terdaftar.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($stats['recentRegistrations'] as $p): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <a href="<?= base_url('admin/pemuda/detail/' . $p['id']) ?>" class="fw-bold text-decoration-none text-primary">
                                                <?= esc($p['registration_number']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= esc($p['name']) ?></div>
                                            <div class="text-muted" style="font-size: 0.75rem;">
                                                <?= $p['gender'] === 'L' ? '<i class="bi bi-gender-male text-primary"></i> Laki-laki' : '<i class="bi bi-gender-female text-danger"></i> Perempuan' ?>
                                                <?php if (!empty($p['phone'])): ?>
                                                    &bull; <i class="bi bi-whatsapp text-success"></i> <?= esc($p['phone']) ?>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-medium text-dark"><?= esc($p['cabang_name']) ?></div>
                                            <div class="text-muted small"><?= esc($p['wilayah_name']) ?></div>
                                        </td>
                                        <td>
                                            <?php if ($p['status_verifikasi'] === 'verified'): ?>
                                                <span class="badge badge-verif-verified rounded-pill px-2 py-1"><i class="bi bi-check-circle me-1"></i> Terverifikasi</span>
                                            <?php elseif ($p['status_verifikasi'] === 'rejected'): ?>
                                                <span class="badge badge-verif-rejected rounded-pill px-2 py-1"><i class="bi bi-x-circle me-1"></i> Ditolak</span>
                                            <?php else: ?>
                                                <span class="badge badge-verif-pending rounded-pill px-2 py-1"><i class="bi bi-clock me-1"></i> Menunggu</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-muted small">
                                            <?= date('d/m/Y H:i', strtotime($p['created_at'])) ?>
                                        </td>
                                        <td class="text-end pe-3">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= base_url('admin/pemuda/detail/' . $p['id']) ?>" 
                                                   class="btn btn-outline-secondary btn-sm" 
                                                   title="Lihat Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?= base_url('admin/pemuda/edit/' . $p['id']) ?>" 
                                                   class="btn btn-outline-primary btn-sm" 
                                                   title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
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
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // 1. Chart Wilayah
        const wilayahLabels = <?= json_encode(array_column($stats['wilayahStats'], 'name')) ?>;
        const wilayahData   = <?= json_encode(array_map('intval', array_column($stats['wilayahStats'], 'total'))) ?>;
        
        new Chart(document.getElementById('chartWilayah'), {
            type: 'bar',
            data: {
                labels: wilayahLabels,
                datasets: [{
                    label: 'Jumlah Pemuda',
                    data: wilayahData,
                    backgroundColor: ['#0284c7', '#059669', '#f59e0b', '#7c3aed'],
                    borderRadius: 8,
                    maxBarThickness: 45
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });

        // 2. Chart Gender
        new Chart(document.getElementById('chartGender'), {
            type: 'doughnut',
            data: {
                labels: ['Laki-laki', 'Perempuan'],
                datasets: [{
                    data: [<?= (int) $stats['genderData']['L'] ?>, <?= (int) $stats['genderData']['P'] ?>],
                    backgroundColor: ['#0284c7', '#ec4899'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                cutout: '68%'
            }
        });

        // 3. Chart Status Pernikahan
        new Chart(document.getElementById('chartMarital'), {
            type: 'doughnut',
            data: {
                labels: ['Belum Menikah', 'Sudah Menikah', 'Janda', 'Duda'],
                datasets: [{
                    data: [
                        <?= (int) $stats['maritalData']['belum_menikah'] ?>,
                        <?= (int) $stats['maritalData']['sudah_menikah'] ?>,
                        <?= (int) $stats['maritalData']['janda'] ?>,
                        <?= (int) $stats['maritalData']['duda'] ?>
                    ],
                    backgroundColor: ['#0284c7', '#10b981', '#f59e0b', '#64748b'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                cutout: '68%'
            }
        });

        // 4. Chart Pendidikan
        const eduLabels = <?= json_encode(array_column($stats['educationStats'], 'name')) ?>;
        const eduData   = <?= json_encode(array_map('intval', array_column($stats['educationStats'], 'total'))) ?>;
        
        new Chart(document.getElementById('chartPendidikan'), {
            type: 'bar',
            data: {
                labels: eduLabels,
                datasets: [{
                    label: 'Jumlah Pemuda',
                    data: eduData,
                    backgroundColor: '#0ea5e9',
                    borderRadius: 6,
                    maxBarThickness: 28
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });

        // 5. Chart Pekerjaan
        const jobLabels = <?= json_encode(array_column($stats['jobStats'], 'name')) ?>;
        const jobData   = <?= json_encode(array_map('intval', array_column($stats['jobStats'], 'total'))) ?>;
        
        new Chart(document.getElementById('chartPekerjaan'), {
            type: 'bar',
            data: {
                labels: jobLabels,
                datasets: [{
                    label: 'Jumlah Pemuda',
                    data: jobData,
                    backgroundColor: '#f59e0b',
                    borderRadius: 6,
                    maxBarThickness: 32
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>
