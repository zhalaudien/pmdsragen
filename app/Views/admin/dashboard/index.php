<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<!-- WELCOME BANNER -->
<?php 
    $userRole    = session()->get('role');
    $wilayahName = session()->get('wilayah_name') ?? ('Wilayah ' . session()->get('wilayah_id'));
    $cabangName  = session()->get('cabang_name') ?? ('Cabang ' . session()->get('cabang_id'));
?>
<div class="card bg-gradient-navy mb-4 shadow-sm">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-inline-flex align-items-center mb-2 px-3 py-1 bg-white bg-opacity-10 rounded-pill border border-light text-xs font-weight-bold">
                    <i class="fas fa-shield-alt text-info mr-1"></i>
                    <?php if ($userRole === 'superadmin'): ?>
                        Dashboard Super Administrator
                    <?php elseif ($userRole === 'admin_wilayah'): ?>
                        Dashboard Admin Wilayah: <?= esc($wilayahName) ?>
                    <?php else: ?>
                        Dashboard Admin Cabang: <?= esc($cabangName) ?>
                    <?php endif; ?>
                </div>
                <h2 class="font-weight-bold mb-2">Selamat Datang, <?= esc($user['name'] ?? 'Administrator') ?>! 👋</h2>
                <p class="text-white-50 mb-0 font-weight-normal">
                    <?php if ($userRole === 'superadmin'): ?>
                        Pantau seluruh data pemuda, distribusi wilayah &amp; cabang, jenjang pendidikan, pekerjaan, serta verifikasi pendaftaran di Kabupaten Sragen.
                    <?php elseif ($userRole === 'admin_wilayah'): ?>
                        Pantau dan analisis sebaran data pemuda pada seluruh cabang dalam lingkup <strong><?= esc($wilayahName) ?></strong>.
                    <?php else: ?>
                        Kelola, input, dan verifikasi data pemuda khusus pada lingkup <strong><?= esc($cabangName) ?></strong>.
                    <?php endif; ?>
                </p>
            </div>
            <div class="col-lg-4 text-lg-right mt-3 mt-lg-0">
                <div class="btn-group">
                    <a href="<?= base_url('admin/pemuda/tambah') ?>" class="btn btn-primary btn-sm shadow-sm">
                        <i class="fas fa-user-plus mr-1"></i> Tambah Pemuda
                    </a>
                    <a href="<?= base_url('admin/pemuda') ?>" class="btn btn-light btn-sm shadow-sm">
                        <i class="fas fa-table mr-1"></i> Kelola Data
                    </a>
                    <a href="<?= base_url('admin/pemuda/export') ?>" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- STATS ROW 1: SMALL-BOXES -->
<div class="row">
    <!-- Total Pemuda -->
    <div class="col-lg-4 col-12 col-sm-6">
        <div class="small-box bg-info elevation-2">
            <div class="inner">
                <h3><?= number_format($stats['summary']['total'] ?? 0) ?></h3>
                <p>Total Pemuda Terdaftar</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
            <a href="<?= base_url('admin/pemuda') ?>" class="small-box-footer">
                Lihat Seluruh Data <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Terverifikasi -->
    <div class="col-lg-4 col-6 col-sm-6">
        <div class="small-box bg-success elevation-2">
            <div class="inner">
                <h3><?= number_format($stats['summary']['verified'] ?? 0) ?></h3>
                <p>Terverifikasi (Sinkron Pusat)</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <a href="<?= base_url('admin/pemuda?status_verifikasi=verified') ?>" class="small-box-footer">
                Data Terverifikasi <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Belum Terverifikasi -->
    <div class="col-lg-4 col-6 col-sm-12">
        <div class="small-box bg-secondary elevation-2">
            <div class="inner">
                <h3><?= number_format($stats['summary']['pending'] ?? 0) ?></h3>
                <p>Belum Terverifikasi</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
            <a href="<?= base_url('admin/pemuda?status_verifikasi=pending') ?>" class="small-box-footer">
                Data Belum Terverifikasi <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- STATS ROW 2: INFO-BOXES -->
<div class="row">
    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm mb-3">
            <span class="info-box-icon bg-purple elevation-1"><i class="fas fa-map-marked-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted text-xs text-uppercase font-weight-bold">Wilayah</span>
                <span class="info-box-number text-lg font-weight-bold"><?= number_format($stats['totalWilayah'] ?? 4) ?> <small>Wilayah</small></span>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm mb-3">
            <span class="info-box-icon bg-teal elevation-1"><i class="fas fa-sitemap"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted text-xs text-uppercase font-weight-bold">Cabang</span>
                <span class="info-box-number text-lg font-weight-bold"><?= number_format($stats['totalCabang'] ?? 0) ?> <small>Cabang</small></span>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm mb-3">
            <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-user-shield"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted text-xs text-uppercase font-weight-bold">Pengguna / Admin</span>
                <span class="info-box-number text-lg font-weight-bold"><?= number_format($stats['totalUsers'] ?? 0) ?> <small>User</small></span>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm mb-3">
            <span class="info-box-icon bg-secondary elevation-1"><i class="fas fa-archive"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted text-xs text-uppercase font-weight-bold">Data Arsip</span>
                <span class="info-box-number text-lg font-weight-bold"><?= number_format($stats['summary']['archived'] ?? 0) ?> <small>Data</small></span>
            </div>
        </div>
    </div>
</div>

<!-- CHARTS ROW 1 -->
<div class="row">
    <!-- Chart Wilayah -->
    <div class="col-lg-7 col-12">
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header border-0">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-chart-bar mr-1 text-primary"></i> Sebaran Data Pemuda per Wilayah
                </h3>
                <div class="card-tools">
                    <span class="badge badge-light border">Kab. Sragen</span>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body pt-0">
                <div style="position: relative; height: 260px;">
                    <canvas id="chartWilayah"></canvas>
                </div>
                <div class="row text-center mt-3">
                    <?php 
                    $colors = ['#007bff', '#28a745', '#ffc107', '#6f42c1'];
                    $i = 0;
                    foreach ($stats['wilayahStats'] as $w): 
                    ?>
                        <div class="col-6 col-md-3 mb-2">
                            <div class="p-2 border rounded bg-light">
                                <div class="text-xs font-weight-bold" style="color: <?= $colors[$i % 4] ?>;"><?= esc($w['name']) ?></div>
                                <div class="h5 font-weight-bold text-dark mb-0"><?= number_format($w['total']) ?></div>
                                <div class="text-muted text-xs"><?= esc($w['code']) ?></div>
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

    <!-- Chart Gender & Marital -->
    <div class="col-lg-5 col-12">
        <div class="card card-info card-outline shadow-sm">
            <div class="card-header border-0">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-chart-pie mr-1 text-info"></i> Demografi Gender &amp; Status Nikah
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="row align-items-center">
                    <div class="col-6 text-center">
                        <div style="position: relative; height: 160px;">
                            <canvas id="chartGender"></canvas>
                        </div>
                        <div class="mt-2 font-weight-bold text-xs">Jenis Kelamin</div>
                        <div class="d-flex justify-content-center text-xs mt-1">
                            <span class="text-primary font-weight-bold mr-2"><i class="fas fa-mars"></i> L: <?= $stats['genderData']['L'] ?></span>
                            <span class="text-danger font-weight-bold"><i class="fas fa-venus"></i> P: <?= $stats['genderData']['P'] ?></span>
                        </div>
                    </div>
                    <div class="col-6 text-center border-left">
                        <div style="position: relative; height: 160px;">
                            <canvas id="chartMarital"></canvas>
                        </div>
                        <div class="mt-2 font-weight-bold text-xs">Status Nikah</div>
                        <div class="text-muted text-xs">Belum / Sudah / J / D</div>
                    </div>
                </div>

                <hr class="my-3">

                <div class="row text-center text-xs">
                    <div class="col-3">
                        <div class="p-1 bg-light rounded border">
                            <div class="text-muted text-xs">Belum Nikah</div>
                            <div class="font-weight-bold text-primary"><?= $stats['maritalData']['belum_menikah'] ?></div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-1 bg-light rounded border">
                            <div class="text-muted text-xs">Menikah</div>
                            <div class="font-weight-bold text-success"><?= $stats['maritalData']['sudah_menikah'] ?></div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-1 bg-light rounded border">
                            <div class="text-muted text-xs">Janda</div>
                            <div class="font-weight-bold text-warning"><?= $stats['maritalData']['janda'] ?></div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-1 bg-light rounded border">
                            <div class="text-muted text-xs">Duda</div>
                            <div class="font-weight-bold text-secondary"><?= $stats['maritalData']['duda'] ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CHARTS ROW 2: PENDIDIKAN & PEKERJAAN -->
<div class="row">
    <!-- Chart Pendidikan -->
    <div class="col-lg-6 col-12">
        <div class="card card-success card-outline shadow-sm">
            <div class="card-header border-0">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-graduation-cap mr-1 text-success"></i> Statistik Jenjang Pendidikan
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body pt-0">
                <div style="position: relative; height: 250px;">
                    <canvas id="chartPendidikan"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Pekerjaan -->
    <div class="col-lg-6 col-12">
        <div class="card card-warning card-outline shadow-sm">
            <div class="card-header border-0">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-briefcase mr-1 text-warning"></i> Statistik Status Pekerjaan
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body pt-0">
                <div style="position: relative; height: 250px;">
                    <canvas id="chartPekerjaan"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SECTION 3: TOP CABANG & RECENT REGISTRATIONS -->
<div class="row">
    <!-- 10 Cabang Terbanyak -->
    <div class="col-xl-4 col-12">
        <div class="card card-navy card-outline shadow-sm h-100">
            <div class="card-header border-0">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-trophy mr-1 text-warning"></i>
                    <?php if ($userRole === 'superadmin'): ?>
                        10 Cabang Terbanyak
                    <?php elseif ($userRole === 'admin_wilayah'): ?>
                        Cabang Terbanyak di <?= esc($wilayahName) ?>
                    <?php else: ?>
                        Statistik Cabang <?= esc($cabangName) ?>
                    <?php endif; ?>
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body p-0">
                <ul class="products-list product-list-in-card pl-2 pr-2">
                    <?php if (empty($stats['topCabangStats'])): ?>
                        <li class="item text-center py-3 text-muted">Belum ada data pemuda terdaftar.</li>
                    <?php else: ?>
                        <?php 
                        $maxTotal = max(array_column($stats['topCabangStats'], 'total')) ?: 1;
                        foreach ($stats['topCabangStats'] as $idx => $c): 
                            $percent = round(($c['total'] / $maxTotal) * 100);
                        ?>
                            <li class="item py-2 border-bottom">
                                <div class="product-info ml-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="font-weight-bold text-dark">
                                            <span class="badge badge-light border mr-1"><?= $idx + 1 ?></span>
                                            <?= esc($c['name']) ?>
                                        </span>
                                        <span class="badge badge-primary font-weight-bold"><?= number_format($c['total']) ?> pemuda</span>
                                    </div>
                                    <div class="progress progress-xs">
                                        <div class="progress-bar bg-primary" style="width: <?= $percent ?>%"></div>
                                    </div>
                                    <small class="text-muted"><?= esc($c['wilayah_name']) ?></small>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
            <?php if ($userRole === 'superadmin'): ?>
                <div class="card-footer text-center bg-white border-0 py-2">
                    <a href="<?= base_url('admin/cabang') ?>" class="text-xs font-weight-bold">
                        Kelola Semua Cabang <i class="fas fa-chevron-right ml-1"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pendaftaran Pemuda Terbaru -->
    <div class="col-xl-8 col-12">
        <div class="card card-primary card-outline shadow-sm h-100">
            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-history mr-1 text-primary"></i> Pendaftaran Pemuda Terbaru
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('admin/pemuda') ?>" class="btn btn-xs btn-outline-primary">
                        Lihat Semua Data
                    </a>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-vcenter mb-0" style="font-size: 0.85rem;">
                        <thead class="thead-light">
                            <tr>
                                <th class="pl-3">No. Reg</th>
                                <th>Nama Lengkap</th>
                                <th>Cabang / Wilayah</th>
                                <th>Status Verifikasi</th>
                                <th>Tgl Daftar</th>
                                <th class="text-right pr-3">Aksi</th>
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
                                        <td class="pl-3">
                                            <a href="<?= base_url('admin/pemuda/detail/' . $p['id']) ?>" class="font-weight-bold text-primary">
                                                <?= esc($p['registration_number']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold text-dark"><?= esc($p['name']) ?></div>
                                            <div class="text-muted text-xs">
                                                <?= $p['gender'] === 'L' ? '<i class="fas fa-mars text-primary"></i> L' : '<i class="fas fa-venus text-danger"></i> P' ?>
                                                <?php if (!empty($p['phone'])): ?>
                                                    &bull; <i class="fab fa-whatsapp text-success"></i> <?= esc($p['phone']) ?>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="font-weight-semibold text-dark"><?= esc($p['cabang_name']) ?></div>
                                            <div class="text-muted text-xs"><?= esc($p['wilayah_name']) ?></div>
                                        </td>
                                        <td>
                                            <?php if ($p['status_verifikasi'] === 'verified'): ?>
                                                <span class="badge badge-success px-2 py-1" title="Sinkron Pusat"><i class="fas fa-check-circle mr-1"></i> Terverifikasi</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary px-2 py-1" title="Belum Sinkron Pusat"><i class="fas fa-clock mr-1"></i> Belum Terverifikasi</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-muted text-xs">
                                            <?= date('d/m/Y H:i', strtotime($p['created_at'])) ?>
                                        </td>
                                        <td class="text-right pr-3">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= base_url('admin/pemuda/detail/' . $p['id']) ?>" 
                                                   class="btn btn-default btn-xs" 
                                                   title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?= base_url('admin/pemuda/edit/' . $p['id']) ?>" 
                                                   class="btn btn-primary btn-xs" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
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
    $(document).ready(function () {
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
                    backgroundColor: ['#007bff', '#28a745', '#ffc107', '#6f42c1'],
                    borderRadius: 6,
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
                    backgroundColor: ['#007bff', '#e83e8c'],
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
                    backgroundColor: ['#007bff', '#28a745', '#ffc107', '#6c757d'],
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
                    backgroundColor: '#17a2b8',
                    borderRadius: 5,
                    maxBarThickness: 26
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
                    backgroundColor: '#ffc107',
                    borderRadius: 5,
                    maxBarThickness: 30
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
