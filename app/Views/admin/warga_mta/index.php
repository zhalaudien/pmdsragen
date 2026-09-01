<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<?php
// Helper untuk URL pagination yang mempertahankan semua parameter filter
$buildPageUrl = function(int $targetPage) use ($filters): string {
    $params = array_merge($filters, ['page' => $targetPage]);
    $cleanParams = [];
    foreach ($params as $k => $v) {
        if ($v !== null && $v !== '') {
            $cleanParams[$k] = $v;
        }
    }
    return base_url('admin/warga-mta') . '?' . http_build_query($cleanParams);
};

$currPage   = (int) ($meta['page'] ?? 1);
$perPage    = (int) ($meta['per_page'] ?? 20);
$totalRows  = (int) ($meta['total'] ?? count($wargaList));
$totalPages = (int) ($meta['total_pages'] ?? max(1, (int) ceil($totalRows / max(1, $perPage))));
$startRow   = $totalRows > 0 ? (($currPage - 1) * $perPage) + 1 : 0;
$endRow     = min($currPage * $perPage, $totalRows);
?>

<!-- HEADER & ACTION -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
    <div>
        <h4 class="font-weight-bold text-dark mb-1">
            <i class="fas fa-id-card text-success mr-2"></i>Data Warga MTA — <span class="text-primary">Perwakilan Sragen</span>
        </h4>
        <p class="text-muted text-sm mb-0">
            Data resmi warga MTA Kabupaten Sragen yang diambil secara langsung melalui REST API <code>api.mta.or.id</code>.
        </p>
    </div>
    <div class="mt-2 mt-md-0 d-flex align-items-center">
        <a href="<?= base_url('admin/mta-sync') ?>" class="btn btn-outline-primary btn-sm mr-2 shadow-sm" title="Pengaturan Integrasi & Sinkronisasi Cabang">
            <i class="fas fa-sync-alt mr-1"></i> Integrasi MTA
        </a>
        <a href="<?= base_url('admin/pemuda') ?>" class="btn btn-outline-secondary btn-sm shadow-sm" title="Lihat Data Pemuda Lokal PMD">
            <i class="fas fa-users mr-1"></i> Data Pemuda
        </a>
    </div>
</div>

<!-- ALERT API ERROR JIKA ADA -->
<?php if (!empty($apiError)): ?>
    <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
        <h5><i class="icon fas fa-exclamation-triangle"></i> Peringatan Koneksi API MTA</h5>
        <p class="mb-1"><?= esc($apiError) ?></p>
        <small class="text-muted">Pastikan token API pada file <code>.env</code> sudah benar dan server <code>api.mta.or.id</code> dapat diakses.</small>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- 1. STATS SUMMARY CARDS -->
<div class="row mb-3">
    <div class="col-12 col-sm-6 col-md-3 mb-2 mb-md-0">
        <div class="info-box shadow-sm mb-0">
            <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-xs text-muted">Total Warga MTA Sragen</span>
                <span class="info-box-number text-lg font-weight-bold text-primary">
                    <?= number_format($stats['totalWargaSragenMta'] ?? $totalRows, 0, ',', '.') ?>
                </span>
                <small class="text-muted">Kode Perwakilan: 86</small>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3 mb-2 mb-md-0">
        <div class="info-box shadow-sm mb-0">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-sitemap"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-xs text-muted">Cabang MTA di Sragen</span>
                <span class="info-box-number text-lg font-weight-bold text-info">
                    <?= number_format($stats['totalCabangSragenMta'] ?? count($mtaCabangList), 0, ',', '.') ?> Cabang
                </span>
                <small class="text-muted">Database Pusat MTA</small>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3 mb-2 mb-md-0">
        <div class="info-box shadow-sm mb-0">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-user-check"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-xs text-muted">Tersinkron di PMD</span>
                <span class="info-box-number text-lg font-weight-bold text-success">
                    <?= number_format($stats['totalPemudaSyncedMta'] ?? 0, 0, ',', '.') ?>
                    <small class="text-muted text-xs font-weight-normal">
                        (<?= ($stats['totalPemudaLokal'] ?? 0) > 0 ? round(($stats['totalPemudaSyncedMta'] / $stats['totalPemudaLokal']) * 100, 1) : 0 ?>%)
                    </small>
                </span>
                <small class="text-muted">Dari <?= number_format($stats['totalPemudaLokal'] ?? 0) ?> Pemuda</small>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box shadow-sm mb-0">
            <span class="info-box-icon bg-olive elevation-1"><i class="fas fa-satellite-dish"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-xs text-muted">Status Sumber API</span>
                <span class="info-box-number text-sm font-weight-bold <?= empty($apiError) ? 'text-success' : 'text-danger' ?>">
                    <i class="fas <?= empty($apiError) ? 'fa-check-circle' : 'fa-times-circle' ?> mr-1"></i>
                    <?= empty($apiError) ? 'Terhubung Normal' : 'Ada Gangguan' ?>
                </span>
                <small class="text-muted">api.mta.or.id/api/v1</small>
            </div>
        </div>
    </div>
</div>

<!-- 2. FILTER & SEARCH CARD -->
<div class="card card-default card-outline shadow-sm mb-3">
    <div class="card-header border-0 py-2">
        <h3 class="card-title font-weight-bold text-sm">
            <i class="fas fa-filter mr-1 text-primary"></i> Filter &amp; Pencarian Warga MTA Sragen
        </h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body pt-1 pb-3">
        <form action="<?= base_url('admin/warga-mta') ?>" method="GET" id="formFilterWargaMta">
            <div class="row">
                <!-- Keyword Cari -->
                <div class="col-12 col-md-3 mb-2">
                    <label class="text-xs font-weight-semibold text-muted mb-1">Cari Warga</label>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text" name="search" class="form-control" placeholder="Nama / No. HP / Alamat..." value="<?= esc($filters['search'] ?? '') ?>">
                    </div>
                </div>

                <!-- Filter Cabang MTA -->
                <div class="col-12 col-md-3 mb-2">
                    <label class="text-xs font-weight-semibold text-muted mb-1">Cabang MTA (Sragen)</label>
                    <select name="cabang" class="form-control form-control-sm select2">
                        <option value="">-- Semua Cabang Sragen --</option>
                        <?php foreach ($mtaCabangList as $mc): ?>
                            <option value="<?= esc($mc['uuid']) ?>" <?= ($filters['cabang'] === $mc['uuid']) ? 'selected' : '' ?>>
                                <?= esc($mc['nama']) ?> <?= !empty($mc['total_warga']) ? '(' . $mc['total_warga'] . ')' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Filter Kelamin -->
                <div class="col-6 col-md-2 mb-2">
                    <label class="text-xs font-weight-semibold text-muted mb-1">Jenis Kelamin</label>
                    <select name="kelamin" class="form-control form-control-sm">
                        <option value="">-- Semua --</option>
                        <option value="L" <?= ($filters['kelamin'] === 'L') ? 'selected' : '' ?>>Laki-laki (Putra)</option>
                        <option value="P" <?= ($filters['kelamin'] === 'P') ? 'selected' : '' ?>>Perempuan (Putri)</option>
                    </select>
                </div>

                <!-- Filter Status PMD Lokal -->
                <div class="col-6 col-md-2 mb-2">
                    <label class="text-xs font-weight-semibold text-muted mb-1">Status PMD Sragen</label>
                    <select name="status_pmd" class="form-control form-control-sm">
                        <option value="">-- Semua Status --</option>
                        <option value="registered" <?= ($filters['status_pmd'] === 'registered') ? 'selected' : '' ?>>Sudah di PMD</option>
                        <option value="unregistered" <?= ($filters['status_pmd'] === 'unregistered') ? 'selected' : '' ?>>Belum di PMD</option>
                    </select>
                </div>

                <!-- Per Page Selector -->
                <div class="col-6 col-md-1 mb-2">
                    <label class="text-xs font-weight-semibold text-muted mb-1">Per Hal</label>
                    <select name="per_page" class="form-control form-control-sm">
                        <option value="15" <?= ($perPage === 15) ? 'selected' : '' ?>>15</option>
                        <option value="20" <?= ($perPage === 20) ? 'selected' : '' ?>>20</option>
                        <option value="25" <?= ($perPage === 25) ? 'selected' : '' ?>>25</option>
                        <option value="50" <?= ($perPage === 50) ? 'selected' : '' ?>>50</option>
                        <option value="100" <?= ($perPage === 100) ? 'selected' : '' ?>>100</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="col-6 col-md-1 mb-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm btn-block shadow-sm" title="Terapkan Filter">
                        <i class="fas fa-filter"></i>
                    </button>
                    <?php if (!empty($filters['search']) || !empty($filters['cabang']) || !empty($filters['kelamin']) || !empty($filters['status_pmd'])): ?>
                        <a href="<?= base_url('admin/warga-mta') ?>" class="btn btn-default btn-sm ml-1 shadow-sm" title="Reset Filter">
                            <i class="fas fa-undo text-secondary"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 3. DATA TABLE CARD -->
<div class="card card-outline card-success shadow-sm">
    <div class="card-header border-0 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center py-2">
        <h3 class="card-title font-weight-bold text-sm">
            <i class="fas fa-table mr-1 text-success"></i> Data Warga MTA Sragen (Halaman <?= $currPage ?> dari <?= $totalPages ?>)
        </h3>
        <div class="card-tools text-muted text-xs mt-1 mt-sm-0">
            <?php if ($totalRows > 0): ?>
                Menampilkan <strong><?= number_format($startRow) ?></strong> - <strong><?= number_format($endRow) ?></strong> dari <strong><?= number_format($totalRows) ?></strong> warga
            <?php else: ?>
                Tidak ada data
            <?php endif; ?>
        </div>
    </div>

    <div class="card-body p-0 table-responsive">
        <table class="table table-hover table-striped table-bordered text-sm align-middle mb-0">
            <thead class="thead-light">
                <tr>
                    <th style="width: 50px;" class="text-center">No.</th>
                    <th style="width: 55px;" class="text-center">Foto</th>
                    <th>Nama &amp; Identitas Warga</th>
                    <th style="width: 110px;" class="text-center">Gender / Usia</th>
                    <th>Cabang MTA (Sragen)</th>
                    <th>Kontak / No. HP</th>
                    <th>Alamat</th>
                    <th style="width: 140px;" class="text-center">Status di PMD</th>
                    <th style="width: 120px;" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($wargaList)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <div class="mb-2"><i class="fas fa-user-slash fa-3x text-secondary opacity-50"></i></div>
                            <h6 class="font-weight-bold mb-1">Data Warga Tidak Ditemukan</h6>
                            <p class="text-xs mb-0">
                                <?= !empty($apiError) ? esc($apiError) : 'Tidak ada data warga MTA Sragen yang sesuai dengan filter pencarian.' ?>
                            </p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php 
                    $no = $startRow;
                    foreach ($wargaList as $w): 
                        $isMale = strtoupper($w['kelamin'] ?? 'L') === 'L';
                        $isLocal = !empty($w['is_local_registered']);
                        $waUrl = !empty($w['nohp']) ? formatWaNumber($w['nohp']) : null;
                    ?>
                        <tr>
                            <!-- 1. Nomor Baris -->
                            <td class="text-center text-muted font-weight-bold"><?= $no++ ?></td>

                            <!-- 2. Avatar / Foto Profil -->
                            <td class="text-center p-2">
                                <div class="rounded-circle bg-light border d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 1.1rem; color: <?= $isMale ? '#007bff' : '#e83e8c' ?>;">
                                    <i class="fas <?= $isMale ? 'fa-user' : 'fa-user' ?>"></i>
                                </div>
                            </td>

                            <!-- 3. Nama & Info Warga -->
                            <td>
                                <div class="font-weight-bold text-dark mb-1" style="font-size: 0.95rem;">
                                    <?= esc($w['nama'] ?? '-') ?>
                                </div>
                                <div class="d-flex flex-wrap gap-1 align-items-center text-xs">
                                    <?php if (!empty($w['nomor'])): ?>
                                        <span class="badge badge-light border text-monospace mr-1" title="Nomor Warga MTA">
                                            <i class="fas fa-barcode mr-1 text-muted"></i><?= esc($w['nomor']) ?>
                                        </span>
                                    <?php endif; ?>
                                    <span class="badge <?= (($w['status'] ?? '') === 'Warga') ? 'badge-info' : 'badge-secondary' ?>">
                                        <?= esc($w['status'] ?? 'Warga') ?>
                                    </span>
                                    <?php if (!empty($w['menikah'])): ?>
                                        <span class="text-muted ml-1"><i class="fas fa-ring text-warning mr-1"></i><?= esc($w['menikah']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- 4. Gender & Usia -->
                            <td class="text-center">
                                <span class="badge <?= $isMale ? 'badge-primary' : 'badge-pink' ?>" style="font-size: 0.75rem; <?= !$isMale ? 'background-color:#e83e8c;color:#fff;' : '' ?>">
                                    <i class="fas <?= $isMale ? 'fa-mars' : 'fa-venus' ?> mr-1"></i>
                                    <?= $isMale ? 'Putra' : 'Putri' ?>
                                </span>
                                <?php if (!empty($w['usia'])): ?>
                                    <small class="d-block text-muted font-weight-bold mt-1"><?= esc($w['usia']) ?> Tahun</small>
                                <?php endif; ?>
                            </td>

                            <!-- 5. Cabang MTA Sragen -->
                            <td>
                                <span class="font-weight-semibold text-dark d-block">
                                    <i class="fas fa-map-marker-alt text-danger mr-1"></i><?= esc($w['cabang'] ?? '-') ?>
                                </span>
                                <small class="text-muted"><?= esc($w['perwakilan'] ?? 'Sragen') ?></small>
                            </td>

                            <!-- 6. Kontak -->
                            <td>
                                <?php if (!empty($w['nohp'])): ?>
                                    <div class="d-flex align-items-center">
                                        <span class="font-weight-mono text-dark text-xs mr-2"><?= esc($w['nohp']) ?></span>
                                        <?php if ($waUrl): ?>
                                            <a href="<?= esc($waUrl) ?>" target="_blank" class="btn btn-xs btn-success text-white py-0 px-1" title="Kirim Pesan WhatsApp">
                                                <i class="fab fa-whatsapp"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted text-xs">-</span>
                                <?php endif; ?>
                            </td>

                            <!-- 7. Alamat -->
                            <td>
                                <span class="text-xs text-secondary d-block text-truncate" style="max-width: 220px;" title="<?= esc($w['alamat'] ?? '-') ?>">
                                    <?= esc($w['alamat'] ?? '-') ?>
                                </span>
                            </td>

                            <!-- 8. Status di PMD Sragen -->
                            <td class="text-center">
                                <?php if ($isLocal): ?>
                                    <span class="badge badge-success px-2 py-1 mb-1 d-inline-block">
                                        <i class="fas fa-check-circle mr-1"></i> Terdaftar PMD
                                    </span>
                                    <code class="d-block text-xs"><?= esc($w['local_reg_number']) ?></code>
                                    <small class="text-muted d-block text-truncate" style="max-width: 120px;" title="<?= esc($w['local_cabang_name']) ?>">
                                        <?= esc($w['local_cabang_name']) ?>
                                    </small>
                                <?php else: ?>
                                    <span class="badge badge-light border text-muted px-2 py-1">
                                        <i class="fas fa-minus-circle mr-1"></i> Belum di PMD
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- 9. Aksi -->
                            <td class="text-center text-nowrap">
                                <!-- Tombol Detail Warga -->
                                <button type="button" 
                                        class="btn btn-xs btn-info btn-detail-warga mr-1" 
                                        data-uuid="<?= esc($w['uuid']) ?>" 
                                        title="Lihat Detail Warga MTA">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </button>

                                <?php if ($isLocal): ?>
                                    <!-- Jika sudah ada di PMD: Tombol Lihat Data Pemuda -->
                                    <a href="<?= base_url('admin/pemuda/detail/' . $w['local_pemuda_id']) ?>" 
                                       class="btn btn-xs btn-outline-success" 
                                       title="Buka Data Pemuda Lokal">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                <?php else: ?>
                                    <!-- Jika belum ada di PMD: Tombol Impor Jadi Pemuda -->
                                    <button type="button" 
                                            class="btn btn-xs btn-success btn-open-import" 
                                            data-uuid="<?= esc($w['uuid']) ?>"
                                            data-nama="<?= esc($w['nama']) ?>"
                                            data-gender="<?= esc($w['kelamin'] ?? 'L') ?>"
                                            data-cabang="<?= esc($w['cabang'] ?? '') ?>"
                                            title="Jadikan Pemuda PMD Sragen">
                                        <i class="fas fa-user-plus mr-1"></i> Impor
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- 4. PAGINATION FOOTER -->
    <?php if ($totalPages > 1): ?>
        <div class="card-footer bg-white d-flex flex-column flex-md-row justify-content-between align-items-center py-2">
            <div class="text-muted text-xs mb-2 mb-md-0">
                Halaman <strong><?= $currPage ?></strong> dari <strong><?= number_format($totalPages) ?></strong> (Total <?= number_format($totalRows) ?> data)
            </div>
            
            <nav aria-label="Navigasi Halaman Warga MTA">
                <ul class="pagination pagination-sm m-0 shadow-xs">
                    <!-- Tombol First & Previous -->
                    <?php if ($currPage > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= $buildPageUrl(1) ?>" title="Halaman Pertama">
                                <i class="fas fa-angle-double-left"></i>
                            </a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="<?= $buildPageUrl($currPage - 1) ?>" title="Sebelumnya">
                                <i class="fas fa-angle-left"></i>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled"><span class="page-link"><i class="fas fa-angle-double-left"></i></span></li>
                        <li class="page-item disabled"><span class="page-link"><i class="fas fa-angle-left"></i></span></li>
                    <?php endif; ?>

                    <!-- Nomor Halaman Sekitar -->
                    <?php
                    $startP = max(1, $currPage - 2);
                    $endP   = min($totalPages, $currPage + 2);

                    if ($startP > 1) {
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }

                    for ($p = $startP; $p <= $endP; $p++):
                    ?>
                        <li class="page-item <?= ($p === $currPage) ? 'active' : '' ?>">
                            <?php if ($p === $currPage): ?>
                                <span class="page-link font-weight-bold"><?= $p ?></span>
                            <?php else: ?>
                                <a class="page-link" href="<?= $buildPageUrl($p) ?>"><?= $p ?></a>
                            <?php endif; ?>
                        </li>
                    <?php endfor; ?>

                    <?php if ($endP < $totalPages): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>

                    <!-- Tombol Next & Last -->
                    <?php if ($currPage < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= $buildPageUrl($currPage + 1) ?>" title="Berikutnya">
                                <i class="fas fa-angle-right"></i>
                            </a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="<?= $buildPageUrl($totalPages) ?>" title="Halaman Terakhir">
                                <i class="fas fa-angle-double-right"></i>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled"><span class="page-link"><i class="fas fa-angle-right"></i></span></li>
                        <li class="page-item disabled"><span class="page-link"><i class="fas fa-angle-double-right"></i></span></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<!-- ============================================================== -->
<!-- MODAL DETAIL WARGA MTA (AJAX) -->
<!-- ============================================================== -->
<div class="modal fade" id="modalDetailWargaMta" tabindex="-1" role="dialog" aria-labelledby="modalDetailWargaMtaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title font-weight-bold" id="modalDetailWargaMtaLabel">
                    <i class="fas fa-id-card mr-1"></i> Detail Warga MTA Sragen
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalDetailBody">
                <!-- Konten akan dimuat via AJAX -->
                <div class="text-center py-5">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary mb-2"></i>
                    <p class="text-muted text-sm mb-0">Memuat data dari API MTA...</p>
                </div>
            </div>
            <div class="modal-footer bg-light d-flex justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                <div id="modalDetailActionContainer"></div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL IMPOR WARGA KE PMD SRAGEN -->
<!-- ============================================================== -->
<div class="modal fade" id="modalImportWargaMta" tabindex="-1" role="dialog" aria-labelledby="modalImportWargaMtaLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formImportWargaMta" action="<?= base_url('admin/warga-mta/import') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="warga_uuid" id="importModalWargaUuid">

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title font-weight-bold" id="modalImportWargaMtaLabel">
                        <i class="fas fa-user-plus mr-1"></i> Impor Warga MTA Menjadi Pemuda PMD
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Ringkasan Data Warga -->
                    <div class="card bg-light border mb-3">
                        <div class="card-body py-2 px-3">
                            <div class="mb-1">
                                <span class="text-muted text-xs d-block">Nama Warga MTA:</span>
                                <strong id="importModalWargaNama" class="text-dark" style="font-size: 1.1rem;">-</strong>
                            </div>
                            <div class="row text-xs">
                                <div class="col-6">
                                    <span class="text-muted d-block">Jenis Kelamin:</span>
                                    <span id="importModalWargaGender" class="font-weight-bold">-</span>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted d-block">Cabang MTA (Sragen):</span>
                                    <span id="importModalWargaCabang" class="font-weight-bold">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pilih Cabang Tujuan di PMD Sragen -->
                    <div class="form-group">
                        <label class="font-weight-semibold text-dark">
                            Pilih Cabang PMD Sragen Tujuan <span class="text-danger">*</span>
                        </label>
                        <select name="cabang_id" id="importModalCabangId" class="form-control select2" required style="width: 100%;">
                            <option value="">-- Pilih Cabang PMD Lokal --</option>
                            <?php 
                            $currentWilayah = '';
                            foreach ($localCabangList as $c): 
                                if ($currentWilayah !== ($c['wilayah_name'] ?? '')):
                                    if ($currentWilayah !== '') echo '</optgroup>';
                                    $currentWilayah = $c['wilayah_name'] ?? 'Wilayah';
                                    echo '<optgroup label="' . esc($currentWilayah) . '">';
                                endif;
                            ?>
                                <option value="<?= $c['id'] ?>" data-name="<?= strtolower(trim($c['name'])) ?>">
                                    <?= esc($c['name']) ?> <?= !empty($c['code']) ? '(' . esc($c['code']) . ')' : '' ?>
                                </option>
                            <?php 
                            endforeach; 
                            if ($currentWilayah !== '') echo '</optgroup>';
                            ?>
                        </select>
                        <small class="form-text text-muted">
                            Data pribadi, alamat, dan status verifikasi warga akan otomatis disinkronkan ke dalam sistem PMD Sragen.
                        </small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm font-weight-bold shadow-sm" id="btnSubmitImport">
                        <i class="fas fa-check-circle mr-1"></i> Daftarkan Sebagai Pemuda
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Inisialisasi Select2
    if ($('.select2').length) {
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
    }

    // Handle Klik Tombol Detail Warga via AJAX
    $(document).on('click', '.btn-detail-warga', function() {
        const uuid = $(this).data('uuid');
        if (!uuid) return;

        $('#modalDetailBody').html(`
            <div class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-2x text-info mb-2"></i>
                <p class="text-muted text-sm mb-0">Mengambil detail data dari server api.mta.or.id...</p>
            </div>
        `);
        $('#modalDetailActionContainer').html('');
        $('#modalDetailWargaMta').modal('show');

        $.ajax({
            url: `<?= base_url('admin/warga-mta/detail') ?>/${uuid}`,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (!res.success || !res.data) {
                    $('#modalDetailBody').html(`
                        <div class="alert alert-danger mb-0">
                            <i class="fas fa-exclamation-triangle mr-1"></i> ${res.message || 'Gagal memuat detail warga.'}
                        </div>
                    `);
                    return;
                }

                const d = res.data;
                const isMale = (d.kelamin || 'L').toUpperCase() === 'L';
                const waUrl = d.nohp ? `https://wa.me/62${d.nohp.replace(/^0+/, '').replace(/[^0-9]/g, '')}` : null;

                let html = `
                    <div class="row align-items-center mb-3 pb-3 border-bottom">
                        <div class="col-md-3 text-center">
                            <div class="rounded-circle bg-light border d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 80px; height: 80px; font-size: 2.2rem; color: ${isMale ? '#007bff' : '#e83e8c'};">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="mt-2">
                                <span class="badge ${isMale ? 'badge-primary' : 'badge-pink'}" style="${!isMale ? 'background-color:#e83e8c;color:#fff;' : ''}">
                                    ${isMale ? 'Putra (Laki-laki)' : 'Putri (Perempuan)'}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-9 mt-2 mt-md-0">
                            <h4 class="font-weight-bold text-dark mb-1">${d.nama || '-'}</h4>
                            <div class="d-flex flex-wrap gap-2 align-items-center text-sm mb-2">
                                <span class="badge badge-light border text-monospace mr-1">
                                    <i class="fas fa-barcode mr-1 text-muted"></i>Nomor: ${d.nomor || '-'}
                                </span>
                                <span class="badge ${d.status === 'Warga' ? 'badge-info' : 'badge-secondary'} mr-1">
                                    ${d.status || 'Warga'}
                                </span>
                                ${d.usia ? `<span class="badge badge-light border"><i class="fas fa-birthday-cake text-warning mr-1"></i>${d.usia} Tahun</span>` : ''}
                            </div>
                            <div class="text-xs text-muted">
                                <strong>UUID API:</strong> <code>${d.uuid}</code>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 border-right">
                            <h6 class="font-weight-bold text-primary mb-2"><i class="fas fa-info-circle mr-1"></i> Data Pribadi</h6>
                            <table class="table table-sm table-borderless text-sm mb-3">
                                <tr>
                                    <td class="text-muted" style="width: 40%;">Tempat, Tgl Lahir:</td>
                                    <td class="font-weight-bold text-dark">${d.kabupaten || 'Sragen'}, ${d.lahir || '-'}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Status Pernikahan:</td>
                                    <td class="font-weight-bold text-dark">${d.menikah || '-'}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Pekerjaan:</td>
                                    <td class="font-weight-bold text-dark">${d.pekerjaan || '-'}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Golongan Darah:</td>
                                    <td class="font-weight-bold text-dark">${d.goldar || '-'}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Mulai Mengaji:</td>
                                    <td class="font-weight-bold text-dark">${d.mengaji || '-'}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Nama Orang Tua:</td>
                                    <td class="text-dark">
                                        <div><small class="text-muted">Ayah:</small> <strong>${d.ayah || '-'}</strong></div>
                                        <div><small class="text-muted">Ibu:</small> <strong>${d.ibu || '-'}</strong></div>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-success mb-2"><i class="fas fa-map-marked-alt mr-1"></i> Cabang &amp; Alamat</h6>
                            <table class="table table-sm table-borderless text-sm mb-3">
                                <tr>
                                    <td class="text-muted" style="width: 35%;">Perwakilan:</td>
                                    <td class="font-weight-bold text-dark">${d.perwakilan || 'Sragen'}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Cabang MTA:</td>
                                    <td class="font-weight-bold text-primary">${d.cabang || '-'}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Kontak / HP:</td>
                                    <td>
                                        ${d.nohp ? `
                                            <strong>${d.nohp}</strong>
                                            ${waUrl ? `<a href="${waUrl}" target="_blank" class="btn btn-xs btn-success ml-1"><i class="fab fa-whatsapp"></i> Chat</a>` : ''}
                                        ` : '<span class="text-muted">-</span>'}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Alamat RT/RW:</td>
                                    <td class="text-dark">${d.alamat_rtrw || '-'}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Desa / Kelurahan:</td>
                                    <td class="text-dark">${d.desa || '-'}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Kecamatan:</td>
                                    <td class="text-dark">${d.kecamatan || '-'}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Alamat Lengkap:</td>
                                    <td class="text-dark">${d.alamat || '-'}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="alert ${d.is_local_registered ? 'alert-success' : 'alert-light border'} mt-2 mb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="font-weight-bold mb-0">
                                    <i class="fas ${d.is_local_registered ? 'fa-check-circle text-success' : 'fa-info-circle text-info'} mr-1"></i>
                                    Status di Sistem PMD Sragen
                                </h6>
                                <small class="${d.is_local_registered ? 'text-success' : 'text-muted'}">
                                    ${d.is_local_registered 
                                        ? `Warga ini SUDAH terdaftar di PMD dengan No. Registrasi <strong>${d.local_reg_number}</strong>` 
                                        : 'Warga ini BELUM terdaftar sebagai pemuda di sistem PMD Sragen.'}
                                </small>
                            </div>
                            <div>
                                ${d.is_local_registered ? `
                                    <a href="<?= base_url('admin/pemuda/detail') ?>/${d.local_pemuda_id}" class="btn btn-sm btn-outline-success font-weight-bold">
                                        <i class="fas fa-external-link-alt mr-1"></i> Buka Data Pemuda
                                    </a>
                                ` : `
                                    <button type="button" class="btn btn-sm btn-success font-weight-bold btn-open-import" 
                                            data-uuid="${d.uuid}" 
                                            data-nama="${d.nama}" 
                                            data-gender="${d.kelamin || 'L'}" 
                                            data-cabang="${d.cabang || ''}">
                                        <i class="fas fa-user-plus mr-1"></i> Impor Jadi Pemuda
                                    </button>
                                `}
                            </div>
                        </div>
                    </div>
                `;

                $('#modalDetailBody').html(html);
            },
            error: function(xhr) {
                $('#modalDetailBody').html(`
                    <div class="alert alert-danger mb-0">
                        <i class="fas fa-times-circle mr-1"></i> Terjadi kesalahan saat memuat data: ${xhr.statusText || 'Error'}
                    </div>
                `);
            }
        });
    });

    // Handle Buka Modal Impor Warga ke PMD
    $(document).on('click', '.btn-open-import', function() {
        const uuid   = $(this).data('uuid');
        const nama   = $(this).data('nama');
        const gender = $(this).data('gender') || 'L';
        const cabang = $(this).data('cabang') || '';

        $('#importModalWargaUuid').val(uuid);
        $('#importModalWargaNama').text(nama);
        $('#importModalWargaGender').text(gender.toUpperCase() === 'L' ? 'Laki-laki (Putra)' : 'Perempuan (Putri)');
        $('#importModalWargaCabang').text(cabang || '-');

        // Otomatis pilih cabang lokal jika nama cabangnya mirip
        $('#importModalCabangId option').prop('selected', false);
        if (cabang) {
            const cleanMtaCabang = cabang.toLowerCase().trim();
            $('#importModalCabangId option').each(function() {
                const optName = ($(this).data('name') || '').toLowerCase().trim();
                if (optName && (cleanMtaCabang.includes(optName) || optName.includes(cleanMtaCabang))) {
                    $(this).prop('selected', true);
                    return false;
                }
            });
        }
        $('#importModalCabangId').trigger('change');

        // Tutup modal detail jika sedang terbuka, lalu buka modal impor
        $('#modalDetailWargaMta').modal('hide');
        $('#modalImportWargaMta').modal('show');
    });

    // Handle Submit Form Impor Warga
    $('#formImportWargaMta').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const submitBtn = $('#btnSubmitImport');
        const originalBtnHtml = submitBtn.html();

        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(res) {
                // Update CSRF token
                if (res.csrfHash) {
                    $('input[name="<?= csrf_token() ?>"]').val(res.csrfHash);
                    $('meta[name="csrf-token"]').attr('content', res.csrfHash);
                }

                if (res.success) {
                    $('#modalImportWargaMta').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil Diimpor!',
                        text: res.message || 'Data warga MTA berhasil didaftarkan ke PMD Sragen.',
                        showCancelButton: true,
                        confirmButtonText: 'Buka Data Pemuda',
                        cancelButtonText: 'Tetap di Halaman Ini'
                    }).then((result) => {
                        if (result.isConfirmed && res.pemuda_id) {
                            window.location.href = `<?= base_url('admin/pemuda/detail') ?>/${res.pemuda_id}`;
                        } else {
                            window.location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Mengimpor',
                        text: res.message || 'Terjadi kesalahan saat memproses data.'
                    });
                    submitBtn.prop('disabled', false).html(originalBtnHtml);
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Sistem',
                    text: 'Gagal menghubungi server: ' + (xhr.statusText || 'Error')
                });
                submitBtn.prop('disabled', false).html(originalBtnHtml);
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
