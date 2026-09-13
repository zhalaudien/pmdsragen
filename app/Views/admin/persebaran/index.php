<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<?php
    $userRole    = session()->get('role');
    $wilayahName = session()->get('wilayah_name') ?? ('Wilayah ' . session()->get('wilayah_id'));
    $cabangName  = session()->get('cabang_name') ?? ('Cabang ' . session()->get('cabang_id'));
    
    $totalYouth       = $stats['totalYouth'] ?? 0;
    $totalWithOrg     = $stats['totalWithOrg'] ?? 0;
    $percentOrg       = $totalYouth > 0 ? round(($totalWithOrg / $totalYouth) * 100, 1) : 0;
    
    $eduStatusActive  = $stats['eduStatusData']['sedang_menempuh']['total'] ?? 0;
    $percentActiveEdu = $totalYouth > 0 ? round(($eduStatusActive / $totalYouth) * 100, 1) : 0;
    
    $totalWithSkill   = $stats['totalWithSkill'] ?? 0;
    $percentSkill     = $totalYouth > 0 ? round(($totalWithSkill / $totalYouth) * 100, 1) : 0;
    
    $totalWirausaha   = $stats['totalWirausaha'] ?? 0;
    $percentWirausaha = $totalYouth > 0 ? round(($totalWirausaha / $totalYouth) * 100, 1) : 0;
?>

<!-- BANNER HEADER -->
<div class="card bg-gradient-navy mb-4 shadow-sm">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-inline-flex align-items-center mb-2 px-3 py-1 bg-white bg-opacity-10 rounded-pill border border-light text-xs font-weight-bold">
                    <i class="fas fa-chart-pie text-warning mr-1"></i> Dashboard Persebaran Data &bull;
                    <?php if ($userRole === 'superadmin'): ?>
                        Akses Superadmin (Seluruh Sragen)
                    <?php elseif ($userRole === 'admin_pemuda'): ?>
                        Admin Pemuda Sragen (Khusus Laki-laki)
                    <?php elseif ($userRole === 'admin_pemudi'): ?>
                        Admin Pemudi Sragen (Khusus Perempuan)
                    <?php elseif ($userRole === 'admin_wilayah' || $userRole === 'admin_wilayah_pemuda'): ?>
                        <?= esc($wilayahName) ?><?= $userRole === 'admin_wilayah_pemuda' ? ' (L)' : '' ?>
                    <?php else: ?>
                        <?= esc($cabangName) ?>
                    <?php endif; ?>
                </div>
                <h2 class="font-weight-bold mb-2">Persebaran Data Pemuda MTA Sragen 📊</h2>
                <p class="text-white-50 mb-0 font-weight-normal">
                    Analisis mendalam sebaran keikutsertaan <strong>Element Dakwah</strong> (Satgas, Bankom, Parkir, dll), <strong>Pendidikan &amp; Sekolah</strong>, <strong>Bakat &amp; Keahlian</strong>, <strong>Minat</strong>, <strong>Ketenagakerjaan &amp; Wirausaha</strong>, <strong>Demografi Usia</strong>, serta <strong>Golongan Darah</strong>.
                </p>
            </div>
            <div class="col-lg-4 text-lg-right mt-3 mt-lg-0">
                <div class="btn-group">
                    <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-tachometer-alt mr-1"></i> Dashboard Utama
                    </a>
                    <a href="<?= base_url('admin/pemuda') ?>" class="btn btn-light btn-sm shadow-sm">
                        <i class="fas fa-table mr-1"></i> Kelola Data
                    </a>
                    <a href="<?= base_url('admin/pemuda/export') ?>" class="btn btn-warning btn-sm shadow-sm font-weight-bold text-dark">
                        <i class="fas fa-file-excel mr-1"></i> Export Data
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- INTERACTIVE FILTER PANEL -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-2 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="font-weight-bold text-dark mb-0 text-xs text-uppercase">
            <i class="fas fa-filter mr-1 text-primary"></i> Filter Interaktif Sebaran Data
        </h6>
        <?php if (!empty($filters['wilayah_id']) || !empty($filters['cabang_id']) || !empty($filters['blood_type']) || (!empty($filters['gender']) && !in_array($userRole, ['admin_pemuda', 'admin_pemudi', 'admin_wilayah_pemuda'], true))): ?>
            <span class="badge badge-primary px-2 py-1">Filter Aktif Diterapkan</span>
        <?php endif; ?>
    </div>
    <div class="card-body p-3 bg-light">
        <form method="GET" action="<?= base_url('admin/persebaran') ?>" id="filterForm">
            <div class="row align-items-end">
                <!-- Wilayah Filter -->
                <div class="col-12 col-sm-6 col-md-2 mb-2">
                    <label class="text-xs text-muted font-weight-bold mb-1">Wilayah</label>
                    <?php if (in_array($userRole, ['superadmin', 'admin_pemuda', 'admin_pemudi'], true)): ?>
                        <select name="wilayah_id" id="filterWilayah" class="form-control form-control-sm">
                            <option value="">-- Semua Wilayah --</option>
                            <?php foreach ($wilayahList as $w): ?>
                                <option value="<?= $w['id'] ?>" <?= (!empty($filters['wilayah_id']) && (int)$filters['wilayah_id'] === (int)$w['id']) ? 'selected' : '' ?>>
                                    <?= esc($w['code'] . ' - ' . $w['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <input type="text" class="form-control form-control-sm bg-white" value="<?= esc($wilayahName) ?>" readonly>
                    <?php endif; ?>
                </div>

                <!-- Cabang Filter -->
                <div class="col-12 col-sm-6 col-md-2 mb-2">
                    <label class="text-xs text-muted font-weight-bold mb-1">Cabang</label>
                    <?php if ($userRole === 'admin_cabang'): ?>
                        <input type="text" class="form-control form-control-sm bg-white" value="<?= esc($cabangName) ?>" readonly>
                    <?php else: ?>
                        <select name="cabang_id" id="filterCabang" class="form-control form-control-sm">
                            <option value="">-- Semua Cabang --</option>
                            <?php foreach ($cabangList as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= (!empty($filters['cabang_id']) && (int)$filters['cabang_id'] === (int)$c['id']) ? 'selected' : '' ?>>
                                    <?= esc($c['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>

                <!-- Gender Filter -->
                <div class="col-6 col-md-2 mb-2">
                    <label class="text-xs text-muted font-weight-bold mb-1">Jenis Kelamin</label>
                    <?php if (in_array($userRole, ['admin_wilayah_pemuda', 'admin_pemuda'], true)): ?>
                        <input type="text" class="form-control form-control-sm bg-white text-primary font-weight-bold" value="Laki-laki (L)" readonly>
                    <?php elseif ($userRole === 'admin_pemudi'): ?>
                        <input type="text" class="form-control form-control-sm bg-white text-danger font-weight-bold" value="Perempuan (P)" readonly>
                    <?php else: ?>
                        <select name="gender" class="form-control form-control-sm">
                            <option value="">-- Semua Gender --</option>
                            <option value="L" <?= (($filters['gender'] ?? '') === 'L') ? 'selected' : '' ?>>Laki-laki (L)</option>
                            <option value="P" <?= (($filters['gender'] ?? '') === 'P') ? 'selected' : '' ?>>Perempuan (P)</option>
                        </select>
                    <?php endif; ?>
                </div>

                <!-- Golongan Darah Filter -->
                <div class="col-6 col-md-2 mb-2">
                    <label class="text-xs text-muted font-weight-bold mb-1">Golongan Darah</label>
                    <select name="blood_type" class="form-control form-control-sm">
                        <option value="">-- Semua Gol. Darah --</option>
                        <option value="A" <?= (($filters['blood_type'] ?? '') === 'A') ? 'selected' : '' ?>>Golongan A</option>
                        <option value="B" <?= (($filters['blood_type'] ?? '') === 'B') ? 'selected' : '' ?>>Golongan B</option>
                        <option value="AB" <?= (($filters['blood_type'] ?? '') === 'AB') ? 'selected' : '' ?>>Golongan AB</option>
                        <option value="O" <?= (($filters['blood_type'] ?? '') === 'O') ? 'selected' : '' ?>>Golongan O</option>
                        <option value="unknown" <?= (($filters['blood_type'] ?? '') === 'unknown') ? 'selected' : '' ?>>Belum Tercatat</option>
                    </select>
                </div>

                <!-- Status Data Filter -->
                <div class="col-6 col-md-2 mb-2">
                    <label class="text-xs text-muted font-weight-bold mb-1">Status Data</label>
                    <select name="status_data" class="form-control form-control-sm">
                        <option value="active" <?= (($filters['status_data'] ?? 'active') === 'active') ? 'selected' : '' ?>>Data Aktif</option>
                        <option value="all" <?= (($filters['status_data'] ?? '') === 'all') ? 'selected' : '' ?>>Semua Data</option>
                        <option value="archived" <?= (($filters['status_data'] ?? '') === 'archived') ? 'selected' : '' ?>>Diarsipkan</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="col-6 col-md-2 mb-2">
                    <div class="btn-group w-100">
                        <button type="submit" class="btn btn-primary btn-sm font-weight-bold">
                            <i class="fas fa-search mr-1"></i> Filter
                        </button>
                        <a href="<?= base_url('admin/persebaran') ?>" class="btn btn-secondary btn-sm" title="Reset Filter">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- TOP KPI SUMMARY CARDS -->
<div class="row">
    <!-- Total Pemuda -->
    <div class="col-xl-3 col-md-6 col-12 mb-3">
        <div class="card shadow-sm border-0 h-100 border-left-primary">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-muted text-uppercase mb-1">Total Pemuda Terdata</div>
                        <div class="h3 mb-0 font-weight-bold text-dark"><?= number_format($totalYouth) ?></div>
                        <div class="text-xs text-muted mt-1">
                            <span class="text-primary font-weight-bold"><i class="fas fa-mars mr-1"></i><?= number_format($stats['genderData']['L'] ?? 0) ?> L</span> &bull;
                            <span class="text-danger font-weight-bold"><i class="fas fa-venus mr-1"></i><?= number_format($stats['genderData']['P'] ?? 0) ?> P</span>
                        </div>
                    </div>
                    <div class="icon-circle bg-primary text-white elevation-1">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Aktif Element Dakwah -->
    <div class="col-xl-3 col-md-6 col-12 mb-3">
        <div class="card shadow-sm border-0 h-100 border-left-danger">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-muted text-uppercase mb-1">Aktif Element Dakwah</div>
                        <div class="h3 mb-0 font-weight-bold text-danger"><?= number_format($totalWithOrg) ?></div>
                        <div class="text-xs text-muted mt-1">
                            <span class="badge badge-danger"><?= $percentOrg ?>%</span> dari total pemuda
                        </div>
                    </div>
                    <div class="icon-circle bg-danger text-white elevation-1">
                        <i class="fas fa-shield-alt fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pelajar & Mahasiswa Aktif -->
    <div class="col-xl-3 col-md-6 col-12 mb-3">
        <div class="card shadow-sm border-0 h-100 border-left-info">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-muted text-uppercase mb-1">Pelajar / Mahasiswa</div>
                        <div class="h3 mb-0 font-weight-bold text-info"><?= number_format($eduStatusActive) ?></div>
                        <div class="text-xs text-muted mt-1">
                            <span class="badge badge-info"><?= $percentActiveEdu ?>%</span> sedang studi aktif
                        </div>
                    </div>
                    <div class="icon-circle bg-info text-white elevation-1">
                        <i class="fas fa-graduation-cap fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Memiliki Bakat / Keahlian -->
    <div class="col-xl-3 col-md-6 col-12 mb-3">
        <div class="card shadow-sm border-0 h-100 border-left-success">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-muted text-uppercase mb-1">Memiliki Keahlian / Bakat</div>
                        <div class="h3 mb-0 font-weight-bold text-success"><?= number_format($totalWithSkill) ?></div>
                        <div class="text-xs text-muted mt-1">
                            <span class="badge badge-success"><?= $percentSkill ?>%</span> potensi SDM terdata
                        </div>
                    </div>
                    <div class="icon-circle bg-success text-white elevation-1">
                        <i class="fas fa-lightbulb fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================================================================= -->
<!-- SECTION 1: PERSEBARAN KEIKUTSERTAAN ELEMENT DAKWAH (ORGANISASI) -->
<!-- ================================================================= -->
<div class="row">
    <div class="col-12 mb-2">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="font-weight-bold text-dark mb-0">
                <i class="fas fa-shield-alt text-danger mr-2"></i> 1. Persebaran Keikutsertaan Element Dakwah
            </h5>
            <span class="badge badge-danger px-3 py-1 text-xs">Satgas, Bankom, Tim Parkir, Pengurus Pemuda, Tim Ikhrom</span>
        </div>
        <p class="text-muted text-xs mt-1 mb-3">Sebaran pemuda yang telah bergabung dan bertugas dalam unit-unit keorganisasian dakwah.</p>
    </div>

    <!-- Donut Chart Keikutsertaan -->
    <div class="col-lg-5 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="font-weight-bold mb-0 text-dark">
                    <i class="fas fa-chart-pie text-danger mr-1"></i> Komposisi Unit Element Dakwah
                </h6>
            </div>
            <div class="card-body d-flex flex-column justify-content-center p-3">
                <?php if (empty($stats['orgStats'])): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-info-circle fa-2x mb-2 text-muted"></i>
                        <p class="mb-0">Belum ada data keikutsertaan element dakwah tercatat pada filter ini.</p>
                    </div>
                <?php else: ?>
                    <div style="position: relative; height: 260px;">
                        <canvas id="chartElementDakwah"></canvas>
                    </div>
                    <div class="mt-3 pt-3 border-top text-center text-xs text-muted">
                        <span class="font-weight-bold text-dark"><?= number_format($totalWithOrg) ?></span> dari <?= number_format($totalYouth) ?> pemuda telah aktif terdaftar dalam element dakwah.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- List & Progress Rincian Element Dakwah -->
    <div class="col-lg-7 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="font-weight-bold mb-0 text-dark">
                    <i class="fas fa-list-ul text-primary mr-1"></i> Rincian Personel per Element Dakwah
                </h6>
                <span class="badge badge-light border text-xs"><?= count($stats['orgStats']) ?> Unit Tugas Terdata</span>
            </div>
            <div class="card-body p-3">
                <?php if (empty($stats['orgStats'])): ?>
                    <div class="text-center py-5 text-muted">
                        <p class="mb-0">Belum ada data element dakwah tercatat.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm align-middle mb-0" style="font-size: 0.85rem;">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 40px;">No</th>
                                    <th>Element Dakwah</th>
                                    <th class="text-center" style="width: 140px;">Sebaran Partisipasi</th>
                                    <th class="text-right" style="width: 100px;">Jumlah</th>
                                    <th class="text-right" style="width: 90px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                foreach ($stats['orgStats'] as $idx => $org): 
                                    $pct = $totalYouth > 0 ? round(($org['total'] / $totalYouth) * 100, 1) : 0;
                                ?>
                                    <tr>
                                        <td class="text-muted text-center"><?= $idx + 1 ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="mr-2" style="width: 22px; text-align: center;">
                                                    <i class="<?= esc($org['icon']) ?>"></i>
                                                </div>
                                                <div>
                                                    <span class="font-weight-bold text-dark"><?= esc($org['name']) ?></span>
                                                    <span class="badge badge-light border ml-1 text-xs"><?= esc($org['badge']) ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 mr-2" style="height: 6px;">
                                                    <div class="progress-bar" role="progressbar" style="width: <?= $pct ?>%; background-color: <?= esc($org['color']) ?>;" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <small class="font-weight-bold text-muted" style="min-width: 35px;"><?= $pct ?>%</small>
                                            </div>
                                        </td>
                                        <td class="text-right font-weight-bold text-dark">
                                            <?= number_format($org['total']) ?> <small class="text-muted">Orang</small>
                                        </td>
                                        <td class="text-right">
                                            <a href="<?= base_url('admin/pemuda?organization_name=' . urlencode($org['name'])) ?>" class="btn btn-xs btn-outline-primary" title="Lihat Daftar Pemuda">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ================================================================= -->
<!-- SECTION 2: PERSEBARAN SEKOLAH & PENDIDIKAN -->
<!-- ================================================================= -->
<div class="row mt-2">
    <div class="col-12 mb-2">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="font-weight-bold text-dark mb-0">
                <i class="fas fa-graduation-cap text-info mr-2"></i> 2. Persebaran Sekolah, Jenjang &amp; Pendidikan
            </h5>
            <span class="badge badge-info px-3 py-1 text-xs">Jenjang, Status Studi, Top Kampus &amp; Jurusan</span>
        </div>
        <p class="text-muted text-xs mt-1 mb-3">Distribusi tingkat pendidikan, status kelulusan, dan institusi pendidikan asal pemuda.</p>
    </div>

    <!-- Chart Jenjang Pendidikan -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="font-weight-bold mb-0 text-dark">
                    <i class="fas fa-chart-bar text-info mr-1"></i> Sebaran Jenjang Pendidikan
                </h6>
                <span class="badge badge-light border text-xs">Tingkat Pendidikan</span>
            </div>
            <div class="card-body p-3">
                <div style="position: relative; height: 260px;">
                    <canvas id="chartJenjangPendidikan"></canvas>
                </div>
                <!-- Status Pendidikan Pills -->
                <div class="row mt-3 pt-3 border-top text-center">
                    <?php foreach ($stats['eduStatusData'] as $k => $st): ?>
                        <div class="col-6 col-sm-3 mb-1">
                            <div class="text-xs text-muted"><?= esc($st['label']) ?></div>
                            <div class="font-weight-bold" style="color: <?= esc($st['color']) ?>; font-size: 1rem;">
                                <?= number_format($st['total']) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Top 10 Sekolah / Perguruan Tinggi -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="font-weight-bold mb-0 text-dark">
                    <i class="fas fa-university text-primary mr-1"></i> Top 10 Sekolah &amp; Perguruan Tinggi
                </h6>
                <span class="badge badge-light border text-xs">Sekolah / Kampus Terbanyak</span>
            </div>
            <div class="card-body p-3">
                <?php if (empty($stats['topSchools'])): ?>
                    <div class="text-center py-5 text-muted">
                        <p class="mb-0">Belum ada nama sekolah / universitas yang terdata.</p>
                    </div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php 
                        $maxSchool = !empty($stats['topSchools']) ? max(array_column($stats['topSchools'], 'total')) : 1;
                        foreach ($stats['topSchools'] as $idx => $sch): 
                            $pctSch = round(($sch['total'] / ($maxSchool ?: 1)) * 100);
                        ?>
                            <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center border-bottom-dashed">
                                <div class="d-flex align-items-center flex-grow-1 mr-3 text-truncate">
                                    <span class="badge badge-light border mr-2 font-weight-bold" style="width: 24px;"><?= $idx + 1 ?></span>
                                    <div class="text-truncate">
                                        <div class="font-weight-bold text-dark text-truncate" style="font-size: 0.85rem;" title="<?= esc($sch['school_name']) ?>">
                                            <?= esc($sch['school_name']) ?>
                                        </div>
                                        <div class="progress mt-1" style="height: 4px; width: 140px;">
                                            <div class="progress-bar bg-info" style="width: <?= $pctSch ?>%;"></div>
                                        </div>
                                    </div>
                                </div>
                                <span class="badge badge-pill badge-info px-2 py-1 font-weight-bold text-xs">
                                    <?= number_format($sch['total']) ?> Pemuda
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Top 10 Jurusan / Program Studi -->
    <?php if (!empty($stats['topMajors'])): ?>
        <div class="col-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="font-weight-bold mb-0 text-dark">
                        <i class="fas fa-book-open text-purple mr-1"></i> Top 10 Jurusan &amp; Program Studi Terpopuler
                    </h6>
                    <span class="badge badge-light border text-xs">Keahlian Akademik</span>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <?php foreach ($stats['topMajors'] as $idx => $mj): ?>
                            <div class="col-md-6 col-lg-4 mb-2">
                                <div class="p-2 border rounded bg-white shadow-2xs d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center text-truncate mr-2">
                                        <span class="badge badge-secondary mr-2" style="font-size: 0.7rem;"><?= $idx + 1 ?></span>
                                        <span class="font-weight-bold text-dark text-xs text-truncate" title="<?= esc($mj['major_name']) ?>">
                                            <?= esc($mj['major_name']) ?>
                                        </span>
                                    </div>
                                    <span class="badge badge-light border font-weight-bold text-xs"><?= number_format($mj['total']) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- ================================================================= -->
<!-- SECTION 3: PERSEBARAN BAKAT & KEAHLIAN (SKILLS) -->
<!-- ================================================================= -->
<div class="row mt-2">
    <div class="col-12 mb-2">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="font-weight-bold text-dark mb-0">
                <i class="fas fa-tools text-success mr-2"></i> 3. Persebaran Bakat &amp; Keahlian (Skills)
            </h5>
            <span class="badge badge-success px-3 py-1 text-xs">Pemetaan Potensi SDM Pemuda</span>
        </div>
        <p class="text-muted text-xs mt-1 mb-3">Inventarisasi keahlian teknis, kreatif, dan manajerial pemuda untuk penugasan dakwah dan kegiatan.</p>
    </div>

    <!-- Chart Horizontal Top Bakat & Keahlian -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="font-weight-bold mb-0 text-dark">
                    <i class="fas fa-chart-bar text-success mr-1"></i> 10 Keahlian &amp; Bakat Terbanyak
                </h6>
                <span class="badge badge-light border text-xs">Paling Banyak Dikuasai</span>
            </div>
            <div class="card-body p-3">
                <?php if (empty($stats['topSkills'])): ?>
                    <div class="text-center py-5 text-muted">
                        <p class="mb-0">Belum ada data keahlian pemuda tercatat.</p>
                    </div>
                <?php else: ?>
                    <div style="position: relative; height: 320px;">
                        <canvas id="chartSkills"></canvas>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Tingkat Kemahiran (Levels) & Analisis -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="font-weight-bold mb-0 text-dark">
                    <i class="fas fa-layer-group text-warning mr-1"></i> Tingkat Kemahiran
                </h6>
            </div>
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div style="position: relative; height: 210px;">
                    <canvas id="chartSkillLevels"></canvas>
                </div>
                <div class="mt-3 pt-3 border-top">
                    <div class="d-flex justify-content-between text-xs mb-2">
                        <span class="text-muted"><i class="fas fa-star text-warning mr-1"></i> Pemula (Basic):</span>
                        <span class="font-weight-bold"><?= number_format($stats['skillLevelData']['pemula']['total'] ?? 0) ?></span>
                    </div>
                    <div class="d-flex justify-content-between text-xs mb-2">
                        <span class="text-muted"><i class="fas fa-award text-info mr-1"></i> Menengah (Interm.):</span>
                        <span class="font-weight-bold"><?= number_format($stats['skillLevelData']['menengah']['total'] ?? 0) ?></span>
                    </div>
                    <div class="d-flex justify-content-between text-xs mb-2">
                        <span class="text-muted"><i class="fas fa-medal text-success mr-1"></i> Mahir (Advanced):</span>
                        <span class="font-weight-bold"><?= number_format($stats['skillLevelData']['mahir']['total'] ?? 0) ?></span>
                    </div>
                    <div class="alert alert-light border text-xs text-muted mb-0 mt-3 p-2 text-center">
                        <i class="fas fa-info-circle text-primary mr-1"></i> Total <strong><?= number_format($totalWithSkill) ?></strong> pemuda (<?= $percentSkill ?>%) memiliki keahlian yang siap diberdayakan.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================================================================= -->
<!-- SECTION 4: PERSEBARAN MINAT KEGIATAN (INTERESTS) -->
<!-- ================================================================= -->
<div class="row mt-2">
    <div class="col-12 mb-2">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="font-weight-bold text-dark mb-0">
                <i class="fas fa-heart text-danger mr-2"></i> 4. Persebaran Minat Kegiatan (Interests)
            </h5>
            <span class="badge badge-danger px-3 py-1 text-xs">Orientasi Minat Pemuda</span>
        </div>
        <p class="text-muted text-xs mt-1 mb-3">Ketertarikan aktivitas pemuda sebagai acuan perumusan program kerja dan kegiatan kepemudaan.</p>
    </div>

    <!-- Chart Minat -->
    <div class="col-lg-7 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="font-weight-bold mb-0 text-dark">
                    <i class="fas fa-chart-bar text-danger mr-1"></i> 10 Kategori Minat Tertinggi
                </h6>
                <span class="badge badge-light border text-xs">Prioritas Minat</span>
            </div>
            <div class="card-body p-3">
                <?php if (empty($stats['topInterests'])): ?>
                    <div class="text-center py-5 text-muted">
                        <p class="mb-0">Belum ada data minat pemuda tercatat.</p>
                    </div>
                <?php else: ?>
                    <div style="position: relative; height: 300px;">
                        <canvas id="chartInterests"></canvas>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Rincian Minat Cards -->
    <div class="col-lg-5 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="font-weight-bold mb-0 text-dark">
                    <i class="fas fa-list mr-1 text-danger"></i> Rincian Minat Terbanyak
                </h6>
            </div>
            <div class="card-body p-3">
                <?php if (empty($stats['topInterests'])): ?>
                    <div class="text-center py-5 text-muted">
                        <p class="mb-0">Belum ada data minat pemuda.</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($stats['topInterests'] as $idx => $int): ?>
                            <div class="list-group-item px-0 py-2 border-bottom-dashed d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center text-truncate mr-2">
                                    <span class="badge badge-light border mr-2 font-weight-bold text-xs" style="width: 24px;"><?= $idx + 1 ?></span>
                                    <span class="font-weight-bold text-dark text-xs text-truncate" title="<?= esc($int['name']) ?>">
                                        <?= esc($int['name']) ?>
                                    </span>
                                </div>
                                <span class="badge badge-pill badge-danger px-2 py-1 font-weight-bold text-xs">
                                    <?= number_format($int['total']) ?> Peminat
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ================================================================= -->
<!-- SECTION 5: KETENAGAKERJAAN & KEMANDIRIAN WIRAUSAHA -->
<!-- ================================================================= -->
<div class="row mt-2">
    <div class="col-12 mb-2">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="font-weight-bold text-dark mb-0">
                <i class="fas fa-briefcase text-purple mr-2"></i> 5. Ketenagakerjaan &amp; Potensi Wirausaha
            </h5>
            <span class="badge badge-purple px-3 py-1 text-xs">Status Kerja &amp; Pelaku UMKM</span>
        </div>
        <p class="text-muted text-xs mt-1 mb-3">Distribusi status profesi pemuda dan sektor wirausaha yang digeluti.</p>
    </div>

    <!-- Chart Status Pekerjaan -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="font-weight-bold mb-0 text-dark">
                    <i class="fas fa-chart-pie text-purple mr-1"></i> Status Ketenagakerjaan
                </h6>
                <span class="badge badge-light border text-xs">Profesi &amp; Karir</span>
            </div>
            <div class="card-body p-3">
                <div style="position: relative; height: 260px;">
                    <canvas id="chartPekerjaan"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Sektor Wirausaha Pemuda -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="font-weight-bold mb-0 text-dark">
                    <i class="fas fa-store text-warning mr-1"></i> Sektor &amp; Bidang Wirausaha Pemuda
                </h6>
                <span class="badge badge-warning text-dark font-weight-bold text-xs"><?= number_format($totalWirausaha) ?> Pelaku Usaha</span>
            </div>
            <div class="card-body p-3">
                <?php if (empty($stats['topBizFields'])): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-store-slash fa-2x mb-2 text-muted"></i>
                        <p class="mb-0">Belum ada rincian bidang wirausaha pemuda yang tercatat pada filter ini.</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php 
                        $maxBiz = max(array_column($stats['topBizFields'], 'total')) ?: 1;
                        foreach ($stats['topBizFields'] as $idx => $biz): 
                            $pctBiz = round(($biz['total'] / $maxBiz) * 100);
                        ?>
                            <div class="list-group-item px-0 py-2 border-bottom-dashed d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center flex-grow-1 mr-3 text-truncate">
                                    <span class="badge badge-light border mr-2 font-weight-bold" style="width: 24px;"><?= $idx + 1 ?></span>
                                    <div class="text-truncate">
                                        <span class="font-weight-bold text-dark text-xs text-truncate">
                                            <?= esc($biz['name']) ?>
                                        </span>
                                        <div class="progress mt-1" style="height: 4px; width: 130px;">
                                            <div class="progress-bar bg-warning" style="width: <?= $pctBiz ?>%;"></div>
                                        </div>
                                    </div>
                                </div>
                                <span class="badge badge-pill badge-warning text-dark font-weight-bold text-xs">
                                    <?= number_format($biz['total']) ?> Usaha
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ================================================================= -->
<!-- SECTION 6: DEMOGRAFI USIA & SEBARAN GEOGRAFIS -->
<!-- ================================================================= -->
<div class="row mt-2">
    <div class="col-12 mb-2">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="font-weight-bold text-dark mb-0">
                <i class="fas fa-map-marked-alt text-primary mr-2"></i> 6. Demografi Usia &amp; Persebaran Wilayah / Domisili
            </h5>
            <span class="badge badge-primary px-3 py-1 text-xs">Kelompok Umur &amp; Kecamatan</span>
        </div>
        <p class="text-muted text-xs mt-1 mb-3">Distribusi piramida umur pemuda dan sebaran domisili kecamatan di Sragen.</p>
    </div>

    <!-- Chart Kelompok Usia -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="font-weight-bold mb-0 text-dark">
                    <i class="fas fa-birthday-cake text-warning mr-1"></i> Sebaran Kelompok Usia
                </h6>
                <span class="badge badge-light border text-xs">
                    Rata-rata Usia: <strong><?= $stats['avgAge'] > 0 ? $stats['avgAge'] . ' Th' : '-' ?></strong>
                </span>
            </div>
            <div class="card-body p-3">
                <div style="position: relative; height: 260px;">
                    <canvas id="chartUsia"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Kecamatan Domisili di Sragen -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="font-weight-bold mb-0 text-dark">
                    <i class="fas fa-map-marker-alt text-danger mr-1"></i> Top 10 Kecamatan Domisili di Sragen
                </h6>
                <span class="badge badge-light border text-xs">Alamat Tinggal</span>
            </div>
            <div class="card-body p-3">
                <?php if (empty($stats['districtStats'])): ?>
                    <div class="text-center py-5 text-muted">
                        <p class="mb-0">Belum ada data kecamatan domisili tercatat.</p>
                    </div>
                <?php else: ?>
                    <div style="position: relative; height: 260px;">
                        <canvas id="chartKecamatan"></canvas>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ================================================================= -->
<!-- SECTION 7: PERSEBARAN GOLONGAN DARAH & KESIAPSIAGAAN MEDIS -->
<!-- ================================================================= -->
<div class="row mt-4">
    <div class="col-12 mb-2">
        <div class="d-flex align-items-center justify-content-between flex-wrap">
            <h5 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-tint text-danger mr-2"></i> 7. Persebaran Golongan Darah &amp; Kesiapsiagaan Donor
            </h5>
            <span class="badge badge-danger px-3 py-1 text-xs mb-1">
                <i class="fas fa-heartbeat mr-1"></i> Kesiapsiagaan Kemanusiaan &amp; Medis
            </span>
        </div>
        <p class="text-muted text-xs mt-1 mb-3">
            Pemetaan golongan darah pemuda untuk kebutuhan bank data kemanusiaan, aksi donor darah berkala PMI, dan respon cepat darurat medis di lingkungan jamaah.
        </p>
    </div>

    <!-- Chart Visualisasi Golongan Darah -->
    <div class="col-lg-5 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="font-weight-bold mb-0 text-dark">
                    <i class="fas fa-chart-pie text-danger mr-1"></i> Komposisi Golongan Darah
                </h6>
                <span class="badge badge-light border text-xs">
                    Terdata: <strong><?= number_format($stats['totalWithBlood'] ?? 0) ?></strong> (<?= $stats['percentWithBlood'] ?? 0 ?>%)
                </span>
            </div>
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div style="position: relative; height: 260px;">
                    <canvas id="chartGolDarah"></canvas>
                </div>
                <div class="mt-3 pt-3 border-top">
                    <div class="d-flex justify-content-between text-xs mb-1">
                        <span class="text-muted font-weight-bold">Tingkat Kelengkapan Data:</span>
                        <span class="font-weight-bold text-dark"><?= $stats['percentWithBlood'] ?? 0 ?>%</span>
                    </div>
                    <div class="progress mb-1" style="height: 8px;">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: <?= $stats['percentWithBlood'] ?? 0 ?>%" aria-valuenow="<?= $stats['percentWithBlood'] ?? 0 ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex justify-content-between text-xs text-muted">
                        <span><i class="fas fa-check-circle text-success mr-1"></i> Sudah Tahu: <strong><?= number_format($stats['totalWithBlood'] ?? 0) ?></strong></span>
                        <span><i class="fas fa-question-circle text-secondary mr-1"></i> Belum Tercatat: <strong><?= number_format($stats['totalUnknownBlood'] ?? 0) ?></strong></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rincian Tiap Golongan Darah & Kompatibilitas Donor -->
    <div class="col-lg-7 mb-4">
        <div class="row">
            <!-- Golongan A -->
            <div class="col-sm-6 mb-3">
                <div class="card shadow-sm border-0 h-100 border-left-danger">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge badge-danger font-weight-bold px-2 py-1">Golongan Darah A</span>
                                <div class="h3 font-weight-bold text-dark mb-0 mt-1">
                                    <?= number_format($stats['bloodData']['A']['total'] ?? 0) ?>
                                    <span class="text-xs font-weight-normal text-muted">orang</span>
                                </div>
                            </div>
                            <div class="rounded-circle text-white d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px; font-weight: 900; font-size: 1.25rem; background-color: #dc3545;">
                                A
                            </div>
                        </div>
                        <div class="text-xs text-muted border-top pt-2">
                            <div><i class="fas fa-arrow-circle-right text-success mr-1"></i> Donor untuk: <strong>A, AB</strong></div>
                            <div><i class="fas fa-arrow-circle-left text-primary mr-1"></i> Menerima dari: <strong>A, O</strong></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Golongan B -->
            <div class="col-sm-6 mb-3">
                <div class="card shadow-sm border-0 h-100 border-left-primary">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge badge-primary font-weight-bold px-2 py-1">Golongan Darah B</span>
                                <div class="h3 font-weight-bold text-dark mb-0 mt-1">
                                    <?= number_format($stats['bloodData']['B']['total'] ?? 0) ?>
                                    <span class="text-xs font-weight-normal text-muted">orang</span>
                                </div>
                            </div>
                            <div class="rounded-circle text-white d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px; font-weight: 900; font-size: 1.25rem; background-color: #007bff;">
                                B
                            </div>
                        </div>
                        <div class="text-xs text-muted border-top pt-2">
                            <div><i class="fas fa-arrow-circle-right text-success mr-1"></i> Donor untuk: <strong>B, AB</strong></div>
                            <div><i class="fas fa-arrow-circle-left text-primary mr-1"></i> Menerima dari: <strong>B, O</strong></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Golongan AB -->
            <div class="col-sm-6 mb-3">
                <div class="card shadow-sm border-0 h-100" style="border-left: 4px solid #6f42c1 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge badge-purple font-weight-bold px-2 py-1">Golongan Darah AB</span>
                                <div class="h3 font-weight-bold text-dark mb-0 mt-1">
                                    <?= number_format($stats['bloodData']['AB']['total'] ?? 0) ?>
                                    <span class="text-xs font-weight-normal text-muted">orang</span>
                                </div>
                            </div>
                            <div class="rounded-circle text-white d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px; font-weight: 900; font-size: 1.15rem; background-color: #6f42c1;">
                                AB
                            </div>
                        </div>
                        <div class="text-xs text-muted border-top pt-2">
                            <div><i class="fas fa-arrow-circle-right text-success mr-1"></i> Donor untuk: <strong>AB</strong></div>
                            <div><i class="fas fa-star text-warning mr-1"></i> <strong>Resipien Universal</strong> (Semua gol.)</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Golongan O -->
            <div class="col-sm-6 mb-3">
                <div class="card shadow-sm border-0 h-100 border-left-success">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge badge-success font-weight-bold px-2 py-1">Golongan Darah O</span>
                                <div class="h3 font-weight-bold text-dark mb-0 mt-1">
                                    <?= number_format($stats['bloodData']['O']['total'] ?? 0) ?>
                                    <span class="text-xs font-weight-normal text-muted">orang</span>
                                </div>
                            </div>
                            <div class="rounded-circle text-white d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px; font-weight: 900; font-size: 1.25rem; background-color: #28a745;">
                                O
                            </div>
                        </div>
                        <div class="text-xs text-muted border-top pt-2">
                            <div><i class="fas fa-star text-warning mr-1"></i> <strong>Donor Universal</strong> (Ke semua gol.)</div>
                            <div><i class="fas fa-arrow-circle-left text-primary mr-1"></i> Menerima dari: <strong>O</strong></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Belum Tercatat / Info Action Card -->
            <div class="col-12">
                <div class="card shadow-sm border-0 bg-light">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap">
                        <div class="mb-2 mb-sm-0">
                            <h6 class="font-weight-bold mb-1 text-dark text-sm">
                                <i class="fas fa-info-circle text-info mr-1"></i> Belum Tercatat: <strong><?= number_format($stats['totalUnknownBlood'] ?? 0) ?></strong> Pemuda
                            </h6>
                            <p class="text-muted text-xs mb-0">
                                Disarankan menyelenggarakan cek golongan darah bersamaan dengan kegiatan gelombang pemuda atau donor darah PMI.
                            </p>
                        </div>
                        <a href="<?= base_url('admin/pemuda?blood_type=unknown') ?>" class="btn btn-sm btn-outline-secondary font-weight-bold">
                            <i class="fas fa-filter mr-1"></i> Lihat Data Belum Cek
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Dynamic dependent dropdown: Cabang by Wilayah
    $('#filterWilayah').on('change', function() {
        const wid = $(this).val();
        const $cabangSelect = $('#filterCabang');
        if (!wid) {
            $cabangSelect.html('<option value="">-- Semua Cabang --</option>');
            <?php foreach ($cabangList as $c): ?>
                $cabangSelect.append('<option value="<?= $c['id'] ?>"><?= esc($c['name']) ?></option>');
            <?php endforeach; ?>
            return;
        }

        $.getJSON('<?= base_url('api/cabang/') ?>/' + wid, function(data) {
            let opts = '<option value="">-- Semua Cabang --</option>';
            $.each(data, function(i, c) {
                opts += '<option value="' + c.id + '">' + c.name + '</option>';
            });
            $cabangSelect.html(opts);
        });
    });

    // Color palette constants
    const colors = {
        primary: '#007bff',
        success: '#28a745',
        info: '#17a2b8',
        warning: '#ffc107',
        danger: '#dc3545',
        purple: '#6f42c1',
        teal: '#20c997',
        indigo: '#6610f2',
        pink: '#e83e8c',
        gray: '#6c757d',
    };

    // ==========================================
    // 1. Chart Element Dakwah (Doughnut)
    // ==========================================
    <?php if (!empty($stats['orgStats'])): ?>
    const ctxOrg = document.getElementById('chartElementDakwah');
    if (ctxOrg) {
        new Chart(ctxOrg, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_column($stats['orgStats'], 'name')) ?>,
                datasets: [{
                    data: <?= json_encode(array_column($stats['orgStats'], 'total')) ?>,
                    backgroundColor: <?= json_encode(array_column($stats['orgStats'], 'color')) ?>,
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            font: { size: 11 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const val = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                return ' ' + context.label + ': ' + val.toLocaleString() + ' orang (' + pct + '%)';
                            }
                        }
                    }
                },
                cutout: '62%'
            }
        });
    }
    <?php endif; ?>

    // ==========================================
    // 2. Chart Jenjang Pendidikan (Bar)
    // ==========================================
    const ctxEdu = document.getElementById('chartJenjangPendidikan');
    if (ctxEdu) {
        new Chart(ctxEdu, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($stats['eduLevelStats'], 'name')) ?>,
                datasets: [{
                    label: 'Jumlah Pemuda',
                    data: <?= json_encode(array_map('intval', array_column($stats['eduLevelStats'], 'total'))) ?>,
                    backgroundColor: '#17a2b8',
                    borderRadius: 4,
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
                    },
                    x: {
                        ticks: { font: { size: 10 } }
                    }
                }
            }
        });
    }

    // ==========================================
    // 3. Chart Top Skills (Horizontal Bar)
    // ==========================================
    <?php if (!empty($stats['topSkills'])): ?>
    const ctxSkills = document.getElementById('chartSkills');
    if (ctxSkills) {
        new Chart(ctxSkills, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($stats['topSkills'], 'name')) ?>,
                datasets: [{
                    label: 'Jumlah Pemuda',
                    data: <?= json_encode(array_map('intval', array_column($stats['topSkills'], 'total'))) ?>,
                    backgroundColor: '#28a745',
                    borderRadius: 4,
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
                    },
                    y: {
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });
    }
    <?php endif; ?>

    // ==========================================
    // 4. Chart Skill Levels (Doughnut)
    // ==========================================
    const ctxSkillLvl = document.getElementById('chartSkillLevels');
    if (ctxSkillLvl) {
        new Chart(ctxSkillLvl, {
            type: 'doughnut',
            data: {
                labels: ['Pemula (Basic)', 'Menengah (Intermediate)', 'Mahir (Advanced)'],
                datasets: [{
                    data: [
                        <?= (int) ($stats['skillLevelData']['pemula']['total'] ?? 0) ?>,
                        <?= (int) ($stats['skillLevelData']['menengah']['total'] ?? 0) ?>,
                        <?= (int) ($stats['skillLevelData']['mahir']['total'] ?? 0) ?>
                    ],
                    backgroundColor: ['#ffc107', '#17a2b8', '#28a745'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, font: { size: 10 } }
                    }
                },
                cutout: '60%'
            }
        });
    }

    // ==========================================
    // 5. Chart Minat (Horizontal Bar)
    // ==========================================
    <?php if (!empty($stats['topInterests'])): ?>
    const ctxInt = document.getElementById('chartInterests');
    if (ctxInt) {
        new Chart(ctxInt, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($stats['topInterests'], 'name')) ?>,
                datasets: [{
                    label: 'Peminat',
                    data: <?= json_encode(array_map('intval', array_column($stats['topInterests'], 'total'))) ?>,
                    backgroundColor: '#dc3545',
                    borderRadius: 4,
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
                    },
                    y: {
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });
    }
    <?php endif; ?>

    // ==========================================
    // 6. Chart Pekerjaan (Doughnut)
    // ==========================================
    const ctxJob = document.getElementById('chartPekerjaan');
    if (ctxJob) {
        new Chart(ctxJob, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_column($stats['jobStats'], 'name')) ?>,
                datasets: [{
                    data: <?= json_encode(array_map('intval', array_column($stats['jobStats'], 'total'))) ?>,
                    backgroundColor: ['#007bff', '#28a745', '#ffc107', '#17a2b8', '#6f42c1', '#dc3545', '#6c757d'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, font: { size: 10 } }
                    }
                },
                cutout: '58%'
            }
        });
    }

    // ==========================================
    // 7. Chart Kelompok Usia (Bar)
    // ==========================================
    const ctxAge = document.getElementById('chartUsia');
    if (ctxAge) {
        new Chart(ctxAge, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($stats['ageData'], 'label')) ?>,
                datasets: [{
                    label: 'Jumlah Pemuda',
                    data: <?= json_encode(array_column($stats['ageData'], 'total')) ?>,
                    backgroundColor: <?= json_encode(array_column($stats['ageData'], 'color')) ?>,
                    borderRadius: 4,
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
                    },
                    x: {
                        ticks: { font: { size: 10 } }
                    }
                }
            }
        });
    }

    // ==========================================
    // 8. Chart Top Kecamatan (Horizontal Bar)
    // ==========================================
    <?php if (!empty($stats['districtStats'])): ?>
    const ctxKec = document.getElementById('chartKecamatan');
    if (ctxKec) {
        new Chart(ctxKec, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($stats['districtStats'], 'name')) ?>,
                datasets: [{
                    label: 'Domisili Pemuda',
                    data: <?= json_encode(array_map('intval', array_column($stats['districtStats'], 'total'))) ?>,
                    backgroundColor: '#6f42c1',
                    borderRadius: 4,
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
                    },
                    y: {
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });
    }
    <?php endif; ?>

    // ==========================================
    // 9. Chart Golongan Darah (Doughnut)
    // ==========================================
    const ctxBlood = document.getElementById('chartGolDarah');
    if (ctxBlood) {
        new Chart(ctxBlood, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_column($stats['bloodData'], 'label')) ?>,
                datasets: [{
                    data: <?= json_encode(array_map('intval', array_column($stats['bloodData'], 'total'))) ?>,
                    backgroundColor: <?= json_encode(array_column($stats['bloodData'], 'color')) ?>,
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            font: { size: 11 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const val = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                return ' ' + context.label + ': ' + val.toLocaleString() + ' orang (' + pct + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
<style>
.icon-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.border-left-primary { border-left: 4px solid #007bff !important; }
.border-left-danger  { border-left: 4px solid #dc3545 !important; }
.border-left-info    { border-left: 4px solid #17a2b8 !important; }
.border-left-success { border-left: 4px solid #28a745 !important; }
.border-bottom-dashed { border-bottom: 1px dashed #e9ecef; }
.shadow-2xs { box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
.badge-purple { background-color: #6f42c1; color: #fff; }
</style>
<?= $this->endSection() ?>
