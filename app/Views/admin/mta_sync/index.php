<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<!-- 1. STATUS KONEKSI & SCOPE KHUSUS PERWAKILAN SRAGEN -->
<div class="row">
    <div class="col-lg-12">
        <div class="card card-outline <?= ($testConn['connected'] ?? false) ? 'card-success' : 'card-danger' ?> shadow-sm">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-network-wired mr-1 text-primary"></i> Integrasi Database Warga MTA — <span class="text-primary">Khusus Perwakilan Sragen (Kode: 86)</span>
                </h3>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnTestConn">
                        <i class="fas fa-sync-alt mr-1" id="iconTestConn"></i> Uji Ulang Koneksi
                    </button>
                </div>
            </div>
            <div class="card-body py-3">
                <div class="row align-items-center">
                    <div class="col-md-3 border-right text-center">
                        <div class="mb-1">
                            <?php if ($testConn['connected'] ?? false): ?>
                                <span class="badge badge-success px-3 py-2" style="font-size: 0.9rem;">
                                    <i class="fas fa-check-circle mr-1"></i> API TERHUBUNG
                                </span>
                            <?php else: ?>
                                <span class="badge badge-danger px-3 py-2" style="font-size: 0.9rem;">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> BELUM TERHUBUNG
                                </span>
                            <?php endif; ?>
                        </div>
                        <small class="text-muted d-block mt-1" id="statusMessage"><?= esc($testConn['message'] ?? '') ?></small>
                    </div>
                    <div class="col-md-9 pl-md-4">
                        <div class="row text-sm">
                            <div class="col-sm-6 mb-2">
                                <span class="text-muted d-block">Scope Wilayah / Perwakilan:</span>
                                <strong class="text-dark"><i class="fas fa-map-marker-alt text-danger mr-1"></i> Perwakilan Sragen</strong> (Kode: <code>86</code>)
                            </div>
                            <div class="col-sm-6 mb-2">
                                <span class="text-muted d-block">UUID Perwakilan Sragen:</span>
                                <code class="text-xs">3246792b-f0a7-48ca-95fa-379e3bee777d</code>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Rate Limiting API:</span>
                                <span>60 req/menit <?= !empty($testConn['rateLimit']['remaining']) ? '(Sisa kuota: <strong>' . esc($testConn['rateLimit']['remaining']) . '</strong> req)' : '' ?></span>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Total Cabang di Perwakilan Sragen:</span>
                                <strong class="text-primary"><?= !empty($mtaSragenCabang) ? count($mtaSragenCabang) : (!empty($sragenDetail['total_cabang']) ? $sragenDetail['total_cabang'] : '-') ?> Cabang MTA</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 2. RINGKASAN STATISTIK INTEGRASI -->
<div class="row">
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Pemuda PMD</span>
                <span class="info-box-number font-weight-bold" style="font-size: 1.3rem;"><?= number_format($totalPemudaCount, 0, ',', '.') ?></span>
                <div class="progress progress-xs mt-1">
                    <div class="progress-bar bg-info" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-user-check"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Tersinkron Warga MTA</span>
                <span class="info-box-number font-weight-bold text-success" style="font-size: 1.3rem;">
                    <?= number_format($syncedPemudaCount, 0, ',', '.') ?>
                    <small class="text-muted font-weight-normal" style="font-size: 0.75rem;">
                        (<?= $totalPemudaCount > 0 ? round(($syncedPemudaCount / $totalPemudaCount) * 100, 1) : 0 ?>%)
                    </small>
                </span>
                <div class="progress progress-xs mt-1">
                    <div class="progress-bar bg-success" style="width: <?= $totalPemudaCount > 0 ? ($syncedPemudaCount / $totalPemudaCount) * 100 : 0 ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-sitemap text-white"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Cabang Terpetakan MTA</span>
                <span class="info-box-number font-weight-bold" style="font-size: 1.3rem;">
                    <?= $syncedCabangCount ?> <small class="text-muted">/ <?= $totalCabangCount ?> Cabang</small>
                </span>
                <div class="progress progress-xs mt-1">
                    <div class="progress-bar bg-warning" style="width: <?= $totalCabangCount > 0 ? ($syncedCabangCount / $totalCabangCount) * 100 : 0 ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-landmark"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Warga di Sragen (MTA)</span>
                <span class="info-box-number font-weight-bold" style="font-size: 1.3rem;">
                    <?= isset($sragenDetail['total_warga']) ? number_format($sragenDetail['total_warga'], 0, ',', '.') : '-' ?>
                </span>
                <small class="text-muted font-weight-normal"><?= count($mtaSragenCabang) ?> Cabang Sragen</small>
            </div>
        </div>
    </div>
</div>

<!-- 3. FITUR SINKRONISASI & VERIFIKASI OTOMATIS PEMUDA SRAGEN DENGAN MTA PUSAT -->
<div class="card bg-gradient-navy text-white shadow-sm mb-4">
    <div class="card-body p-3 p-md-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="font-weight-bold mb-1">
                    <i class="fas fa-user-check text-success mr-2"></i> Sinkronisasi &amp; Verifikasi Otomatis Pemuda Sragen
                </h5>
                <p class="text-sm mb-0 text-white-50">
                    Sistem akan memindai database pemuda di PMD Sragen dan mencocokkannya dengan Database Warga MTA Pusat.
                    Data yang <strong>ada di MTA Pusat</strong> otomatis diubah statusnya menjadi <span class="badge badge-success px-2 py-1">Terverifikasi</span>, sedangkan yang <strong>belum ada</strong> berstatus <span class="badge badge-warning px-2 py-1 text-dark">Menunggu Verifikasi</span>.
                </p>
            </div>
            <div class="col-md-4 text-md-right mt-3 mt-md-0">
                <button type="button" class="btn btn-success btn-lg font-weight-bold shadow" data-toggle="modal" data-target="#modalSyncVerifyAll">
                    <i class="fas fa-bolt mr-1"></i> Mulai Verifikasi Otomatis
                </button>
            </div>
        </div>
    </div>
</div>

<!-- 4. TAB NAVIGASI INTEGRASI KHUSUS SRAGEN -->
<div class="card card-primary card-outline card-tabs shadow-sm">
    <div class="card-header p-0 pt-1 border-bottom-0">
        <ul class="nav nav-tabs" id="mtaTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active font-weight-bold" id="tab-search-warga" data-toggle="pill" href="#content-search-warga" role="tab">
                    <i class="fas fa-search mr-1 text-primary"></i> Cari &amp; Tarik Warga (Perwakilan Sragen)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold" id="tab-cabang-sync" data-toggle="pill" href="#content-cabang-sync" role="tab">
                    <i class="fas fa-sitemap mr-1 text-warning"></i> Pemetaan Cabang Perwakilan Sragen
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold" id="tab-sync-logs" data-toggle="pill" href="#content-sync-logs" role="tab">
                    <i class="fas fa-history mr-1 text-info"></i> Riwayat Sinkronisasi
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body">
        <div class="tab-content" id="mtaTabsContent">
            
            <!-- TAB 1: PANCARIAN & TARIK DATA WARGA KHUSUS SRAGEN -->
            <div class="tab-pane fade show active" id="content-search-warga" role="tabpanel">
                <div class="callout callout-info mb-4">
                    <h5><i class="fas fa-shield-alt mr-1"></i> Data Terkunci Khusus Perwakilan Sragen</h5>
                    <p class="text-muted mb-0">
                        Seluruh pencarian dan pengambilan data warga di bawah ini <strong>hanya mengambil warga yang terdaftar pada Cabang di bawah Perwakilan Sragen</strong>.
                    </p>
                </div>

                <div class="card bg-light border">
                    <div class="card-body py-3">
                        <form id="formSearchWarga" class="row align-items-end">
                            <div class="col-md-4 mb-2 mb-md-0">
                                <label class="font-weight-semibold">Nama / No. HP Warga Sragen</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    </div>
                                    <input type="text" id="inputSearchWarga" class="form-control" placeholder="Ketik nama atau no. HP...">
                                </div>
                            </div>
                            <div class="col-md-4 mb-2 mb-md-0">
                                <label class="font-weight-semibold">Pilih Cabang Sragen (MTA)</label>
                                <select id="selectCabangMta" class="form-control select2">
                                    <option value="">-- Semua Cabang di Sragen --</option>
                                    <?php foreach ($mtaSragenCabang as $mc): ?>
                                        <option value="<?= esc($mc['uuid']) ?>" data-total="<?= esc($mc['total_warga'] ?? 0) ?>">
                                            <?= esc($mc['nama']) ?> (<?= esc($mc['kode']) ?>) — <?= esc($mc['total_warga'] ?? 0) ?> Warga
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2 mb-2 mb-md-0">
                                <label class="font-weight-semibold">Jenis Kelamin</label>
                                <select id="selectGenderWarga" class="form-control">
                                    <option value="">Semua (L &amp; P)</option>
                                    <option value="L">Laki-laki (L)</option>
                                    <option value="P">Perempuan (P)</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-block font-weight-bold" id="btnSubmitSearch">
                                    <i class="fas fa-search mr-1"></i> Cari Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Hasil Pencarian Warga -->
                <div id="searchResultContainer" class="mt-4" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="font-weight-bold mb-0 text-dark">
                            Hasil Data Warga Sragen (<span id="resultCountBadge">0</span> data)
                        </h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover bg-white" id="tableResultWarga">
                            <thead class="thead-light">
                                <tr>
                                    <th width="4%">No</th>
                                    <th>Nama Warga</th>
                                    <th width="8%">JK</th>
                                    <th>Usia</th>
                                    <th>No. HP</th>
                                    <th>Status Warga</th>
                                    <th>Cabang MTA</th>
                                    <th>Alamat / Domisili</th>
                                    <th width="15%" class="text-center">Status di PMD</th>
                                    <th width="12%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tableResultBody">
                                <!-- Data injected via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAB 2: PEMETAAN & SINKRONISASI CABANG SRAGEN -->
            <div class="tab-pane fade" id="content-cabang-sync" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="font-weight-bold mb-1">Daftar Cabang Perwakilan Sragen (API MTA &amp; PMD Lokal)</h5>
                        <p class="text-muted text-sm mb-0">Sinkronisasi cabang akan memetakan seluruh cabang di Perwakilan Sragen (Total: <?= count($mtaSragenCabang) ?> Cabang di MTA).</p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-warning font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalSyncCabang">
                            <i class="fas fa-sync-alt mr-1"></i> Sinkronkan Seluruh Cabang Sragen
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped bg-white text-sm">
                        <thead class="thead-light">
                            <tr>
                                <th width="4%">No</th>
                                <th width="12%">Kode Cabang</th>
                                <th width="22%">Nama Cabang</th>
                                <th>Alamat Cabang</th>
                                <th width="12%" class="text-center">Total Warga di MTA</th>
                                <th width="18%">Status di PMD Sragen</th>
                                <th width="12%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($mtaSragenCabang)): ?>
                                <tr><td colspan="7" class="text-center py-3 text-muted">Belum ada data cabang dari Perwakilan Sragen.</td></tr>
                            <?php else: ?>
                                <?php 
                                foreach ($mtaSragenCabang as $idx => $mc): 
                                    // Cari apakah sudah ada di cabang lokal
                                    $matchedLocal = null;
                                    foreach ($localCabang as $lc) {
                                        if ((!empty($lc['mta_uuid']) && $lc['mta_uuid'] === $mc['uuid']) || strcasecmp(trim($lc['name']), trim($mc['nama'])) === 0) {
                                            $matchedLocal = $lc;
                                            break;
                                        }
                                    }
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $idx + 1 ?></td>
                                        <td><code><?= esc($mc['kode'] ?? '-') ?></code></td>
                                        <td class="font-weight-bold text-dark"><?= esc($mc['nama']) ?></td>
                                        <td class="text-xs"><?= esc($mc['alamat'] ?? '-') ?></td>
                                        <td class="text-center font-weight-bold text-primary">
                                            <?= esc($mc['total_warga'] ?? 0) ?> Warga
                                        </td>
                                        <td>
                                            <?php if ($matchedLocal): ?>
                                                <span class="badge badge-success px-2 py-1 mb-1">
                                                    <i class="fas fa-check-circle mr-1"></i> Terhubung (<?= esc($matchedLocal['wilayah_name'] ?? 'Wilayah ' . $matchedLocal['wilayah_id']) ?>)
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary px-2 py-1">
                                                    <i class="fas fa-unlink mr-1"></i> Belum Dibuat di PMD
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-xs btn-outline-primary btn-load-cabang-warga" data-uuid="<?= esc($mc['uuid']) ?>" data-name="<?= esc($mc['nama']) ?>">
                                                <i class="fas fa-users mr-1"></i> Lihat Warga
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 3: RIWAYAT SINKRONISASI -->
            <div class="tab-pane fade" id="content-sync-logs" role="tabpanel">
                <h5 class="font-weight-bold mb-3">Log Riwayat Sinkronisasi API MTA</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover bg-white text-sm">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%">ID</th>
                                <th width="15%">Waktu</th>
                                <th width="12%">Tipe Sinkronisasi</th>
                                <th width="10%">Status</th>
                                <th width="10%" class="text-center">Jumlah Data</th>
                                <th>Keterangan / Pesan Log</th>
                                <th width="15%">Dilakukan Oleh</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentLogs)): ?>
                                <tr><td colspan="7" class="text-center py-3 text-muted">Belum ada riwayat aktivitas sinkronisasi.</td></tr>
                            <?php else: ?>
                                <?php foreach ($recentLogs as $log): ?>
                                    <tr>
                                        <td>#<?= $log['id'] ?></td>
                                        <td><?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?></td>
                                        <td>
                                            <span class="badge badge-light border text-capitalize font-weight-bold">
                                                <?= esc($log['sync_type']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($log['status'] === 'success'): ?>
                                                <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Sukses</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger px-2 py-1"><i class="fas fa-times-circle mr-1"></i> Gagal</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center font-weight-bold"><?= $log['total_records'] ?></td>
                                        <td><?= esc($log['message'] ?? '-') ?></td>
                                        <td><?= esc($log['user_name'] ?? ($log['username'] ?? 'Sistem')) ?></td>
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

<!-- MODAL SINKRONISASI CABANG SRAGEN -->
<div class="modal fade" id="modalSyncCabang" tabindex="-1" role="dialog" aria-labelledby="modalSyncCabangLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= base_url('admin/mta-sync/sync-cabang') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header bg-warning">
                    <h5 class="modal-title font-weight-bold text-dark" id="modalSyncCabangLabel">
                        <i class="fas fa-sync-alt mr-1"></i> Sinkronkan Cabang Perwakilan Sragen
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-sm text-muted">
                        Sistem akan menyinkronkan <strong><?= count($mtaSragenCabang) ?> cabang</strong> yang ada di Perwakilan Sragen dari API MTA ke database cabang lokal.
                    </p>

                    <div class="form-group">
                        <label class="font-weight-semibold">Wilayah PMD Sragen Default (untuk Cabang Baru):</label>
                        <select name="wilayah_id" class="form-control" required>
                            <?php foreach ($wilayahList as $w): ?>
                                <option value="<?= $w['id'] ?>"><?= esc($w['name']) ?> (<?= esc($w['code']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">Cabang baru yang belum ada di database lokal akan dialokasikan ke wilayah ini.</small>
                    </div>

                    <div class="form-group mb-0">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="autoCreateCabang" name="auto_create" value="1" checked>
                            <label class="custom-control-label font-weight-bold text-dark" for="autoCreateCabang">
                                Buat cabang baru otomatis jika belum ada di database PMD
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning font-weight-bold">
                        <i class="fas fa-play mr-1"></i> Mulai Sinkronisasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL IMPOR WARGA KE PEMUDA -->
<div class="modal fade" id="modalImportWarga" tabindex="-1" role="dialog" aria-labelledby="modalImportWargaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="formExecuteImport" action="<?= base_url('admin/mta-sync/import-warga') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="warga_uuid" id="importWargaUuid">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold" id="modalImportWargaLabel">
                        <i class="fas fa-user-plus mr-1"></i> Impor Warga MTA Menjadi Pemuda
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Ringkasan Data Warga MTA -->
                    <div class="card bg-light border mb-3">
                        <div class="card-body py-2">
                            <div class="row">
                                <div class="col-sm-6">
                                    <span class="text-muted d-block text-xs">Nama Warga:</span>
                                    <strong id="importWargaNama" class="text-dark" style="font-size: 1.1rem;">-</strong>
                                </div>
                                <div class="col-sm-3">
                                    <span class="text-muted d-block text-xs">Jenis Kelamin:</span>
                                    <span id="importWargaGender" class="badge badge-info">-</span>
                                </div>
                                <div class="col-sm-3">
                                    <span class="text-muted d-block text-xs">Status Warga:</span>
                                    <span id="importWargaStatus" class="badge badge-success">-</span>
                                </div>
                                <div class="col-sm-6 mt-2">
                                    <span class="text-muted d-block text-xs">Nomor HP:</span>
                                    <span id="importWargaHp" class="font-weight-bold text-dark">-</span>
                                </div>
                                <div class="col-sm-6 mt-2">
                                    <span class="text-muted d-block text-xs">Alamat Asal:</span>
                                    <span id="importWargaAlamat" class="text-dark">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Pilih Cabang Pemuda Tujuan <span class="text-danger">*</span></label>
                        <select name="cabang_id" id="importSelectCabang" class="form-control select2" required style="width: 100%;">
                            <option value="">-- Pilih Cabang --</option>
                            <?php foreach ($localCabang as $c): ?>
                                <option value="<?= $c['id'] ?>" data-mta-uuid="<?= esc($c['mta_uuid'] ?? '') ?>" data-name="<?= esc(strtolower($c['name'])) ?>">
                                    <?= esc($c['name']) ?> (<?= esc($c['wilayah_name'] ?? 'Wilayah ' . $c['wilayah_id']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">Data pemuda akan didaftarkan dan diverifikasi di cabang ini.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold" id="btnExecuteImport">
                        <i class="fas fa-check-circle mr-1"></i> Impor Sekarang
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

    function renderWargaRows(data) {
        if (!data || data.length === 0) {
            $('#resultCountBadge').text('0');
            $('#tableResultBody').html('<tr><td colspan="10" class="text-center py-4 text-muted"><i class="fas fa-info-circle mr-1"></i> Tidak ditemukan data warga di Perwakilan Sragen.</td></tr>');
            return;
        }

        $('#resultCountBadge').text(data.length);
        let html = '';

        data.forEach(function(item, idx) {
            const genderBadge = item.kelamin === 'L' ? '<span class="badge badge-primary">L (Putra)</span>' : '<span class="badge badge-danger">P (Putri)</span>';
            const usiaText = item.usia ? (item.usia + ' Th') : '-';
            let statusPMD = '';
            let actionBtn = '';

            if (item.is_local_registered) {
                statusPMD = '<span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i> Terdaftar di PMD</span><br><small class="text-muted">' + (item.local_reg_number || '') + '</small>';
                actionBtn = '<a href="<?= base_url('admin/pemuda/detail/') ?>/' + item.local_pemuda_id + '" class="btn btn-xs btn-outline-info" target="_blank"><i class="fas fa-external-link-alt mr-1"></i> Lihat Data</a>';
            } else {
                statusPMD = '<span class="badge badge-secondary px-2 py-1"><i class="fas fa-times mr-1"></i> Belum Terdaftar</span>';
                actionBtn = '<button type="button" class="btn btn-xs btn-primary font-weight-bold btn-open-import" data-warga=\'' + JSON.stringify(item) + '\'><i class="fas fa-download mr-1"></i> Impor</button>';
            }

            html += '<tr>' +
                '<td class="text-center">' + (idx + 1) + '</td>' +
                '<td><strong class="text-dark">' + (item.nama || '-') + '</strong></td>' +
                '<td class="text-center">' + genderBadge + '</td>' +
                '<td class="text-center">' + usiaText + '</td>' +
                '<td>' + (item.nohp || '-') + '</td>' +
                '<td><span class="badge badge-light border">' + (item.status || 'Warga') + '</span></td>' +
                '<td>' + (item.cabang || '-') + '</td>' +
                '<td class="text-sm">' + (item.alamat || '-') + '</td>' +
                '<td class="text-center">' + statusPMD + '</td>' +
                '<td class="text-center">' + actionBtn + '</td>' +
            '</tr>';
        });

        $('#tableResultBody').html(html);
    }

    // 1. Test Ulang Koneksi AJAX
    $('#btnTestConn').on('click', function() {
        const btn = $(this);
        const icon = $('#iconTestConn');
        btn.prop('disabled', true);
        icon.addClass('fa-spin');

        $.ajax({
            url: '<?= base_url('admin/mta-sync/test-connection') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                btn.prop('disabled', false);
                icon.removeClass('fa-spin');
                if (res.data && res.data.connected) {
                    alert('Koneksi Sukses: ' + res.data.message);
                    location.reload();
                } else {
                    alert('Koneksi Gagal: ' + (res.data ? res.data.message : 'Server tidak merespons'));
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false);
                icon.removeClass('fa-spin');
                alert('Gagal menghubungi server backend: ' + xhr.statusText);
            }
        });
    });

    // 2. Pencarian Warga MTA Sragen AJAX
    $('#formSearchWarga').on('submit', function(e) {
        e.preventDefault();
        const q = $('#inputSearchWarga').val().trim();
        const cabangUuid = $('#selectCabangMta').val();
        const gender = $('#selectGenderWarga').val();
        const btn = $('#btnSubmitSearch');

        if (!q && !cabangUuid) {
            alert('Silakan masukkan nama warga, nomor HP, atau pilih salah satu cabang Sragen.');
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Memuat...');
        $('#searchResultContainer').show();
        $('#tableResultBody').html('<tr><td colspan="10" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-2"></i> Mengambil data warga Perwakilan Sragen...</td></tr>');

        // Jika cabang dipilih dan q kosong, panggil endpoint cabang-warga
        if (cabangUuid && !q) {
            $.ajax({
                url: '<?= base_url('admin/mta-sync/cabang-warga/') ?>/' + cabangUuid,
                type: 'GET',
                data: { gender: gender, per_page: 50 },
                dataType: 'json',
                success: function(res) {
                    btn.prop('disabled', false).html('<i class="fas fa-search mr-1"></i> Cari Data');
                    renderWargaRows(res.data);
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('<i class="fas fa-search mr-1"></i> Cari Data');
                    $('#tableResultBody').html('<tr><td colspan="10" class="text-center py-4 text-danger"><i class="fas fa-exclamation-triangle mr-1"></i> Gagal memuat data cabang: ' + xhr.statusText + '</td></tr>');
                }
            });
        } else {
            // Pencarian nama / HP di Perwakilan Sragen
            $.ajax({
                url: '<?= base_url('admin/mta-sync/search-warga') ?>',
                type: 'GET',
                data: { q: q, cabang_uuid: cabangUuid, kelamin: gender, limit: 50 },
                dataType: 'json',
                success: function(res) {
                    btn.prop('disabled', false).html('<i class="fas fa-search mr-1"></i> Cari Data');
                    renderWargaRows(res.data);
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('<i class="fas fa-search mr-1"></i> Cari Data');
                    $('#tableResultBody').html('<tr><td colspan="10" class="text-center py-4 text-danger"><i class="fas fa-exclamation-triangle mr-1"></i> Gagal menghubungi API: ' + xhr.statusText + '</td></tr>');
                }
            });
        }
    });

    // 3. Klik "Lihat Warga" pada Tab Cabang
    $(document).on('click', '.btn-load-cabang-warga', function() {
        const uuid = $(this).data('uuid');
        const name = $(this).data('name');
        if (!uuid) return;

        // Pindah ke tab 1 dan pilih cabang tersebut
        $('#tab-search-warga').tab('show');
        $('#selectCabangMta').val(uuid);
        $('#inputSearchWarga').val('');
        $('#formSearchWarga').submit();
    });

    // 4. Buka Modal Impor Warga
    $(document).on('click', '.btn-open-import', function() {
        const data = $(this).data('warga');
        if (!data) return;

        $('#importWargaUuid').val(data.uuid);
        $('#importWargaNama').text(data.nama || '-');
        $('#importWargaGender').text(data.kelamin === 'L' ? 'Laki-laki' : 'Perempuan');
        $('#importWargaStatus').text(data.status || 'Warga');
        $('#importWargaHp').text(data.nohp || '-');
        $('#importWargaAlamat').text((data.alamat || '') + (data.desa ? ', Ds. ' + data.desa : '') + (data.cabang ? ' (Cabang: ' + data.cabang + ')' : ''));

        // Coba auto-select cabang lokal jika namanya cocok dengan cabang di MTA
        if (data.cabang) {
            const cabangLower = data.cabang.toLowerCase().trim();
            $('#importSelectCabang option').each(function() {
                const optName = $(this).data('name') || '';
                if (optName && (cabangLower.includes(optName) || optName.includes(cabangLower))) {
                    $(this).prop('selected', true);
                    return false;
                }
            });
        }

        $('#modalImportWarga').modal('show');
    });

    // 5. Submit Sinkronisasi & Verifikasi Otomatis Seluruh Pemuda
    $('#formExecuteSyncVerifyAll').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const btn = $('#btnRunSyncVerifyAll');
        const modal = $('#modalSyncVerifyAll');
        const cabangId = form.find('select[name="cabang_id"]').val();
        const onlyPending = form.find('input[name="only_pending"]').is(':checked') ? 1 : 0;

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Sedang Memverifikasi...');

        $.ajax({
            url: '<?= base_url('admin/mta-sync/sync-verify-all') ?>',
            type: 'POST',
            data: {
                cabang_id: cabangId,
                only_pending: onlyPending,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(res) {
                btn.prop('disabled', false).html('<i class="fas fa-bolt mr-1"></i> Mulai Verifikasi Sekarang');
                modal.modal('hide');

                if (res.success) {
                    const d = res.data;
                    const summaryHtml = 'Proses verifikasi otomatis selesai!\n\n' +
                        '• Total Pemuda Diperiksa: ' + d.total + '\n' +
                        '• Terverifikasi di MTA Pusat: ' + d.verified_count + ' (Baru diverifikasi: ' + d.newly_verified + ')\n' +
                        '• Menunggu Verifikasi: ' + d.pending_count;
                    alert(summaryHtml);
                    location.reload();
                } else {
                    alert('Gagal: ' + res.message);
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="fas fa-bolt mr-1"></i> Mulai Verifikasi Sekarang');
                alert('Terjadi kesalahan saat memproses verifikasi otomatis: ' + xhr.statusText);
            }
        });
    });

});
</script>

<!-- MODAL SINKRONISASI & VERIFIKASI MASSAL PEMUDA SRAGEN -->
<div class="modal fade" id="modalSyncVerifyAll" tabindex="-1" role="dialog" aria-labelledby="modalSyncVerifyAllLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <form id="formExecuteSyncVerifyAll">
                <?= csrf_field() ?>
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title font-weight-bold" id="modalSyncVerifyAllLabel">
                        <i class="fas fa-user-check mr-1"></i> Verifikasi Otomatis dengan MTA Pusat
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info text-sm mb-3">
                        <i class="fas fa-info-circle mr-1"></i>
                        Sistem akan memeriksa setiap pemuda terdaftar di PMD Sragen terhadap Database Warga MTA Pusat:
                        <ul class="mb-0 pl-3 mt-1">
                            <li><strong>Jika Ditemukan:</strong> Status diubah menjadi <span class="badge badge-success">Terverifikasi</span> dan ditautkan UUID Warga MTA.</li>
                            <li><strong>Jika Tidak Ditemukan:</strong> Status tetap <span class="badge badge-warning text-dark">Menunggu Verifikasi</span>.</li>
                        </ul>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Pilih Cakupan Cabang:</label>
                        <select name="cabang_id" class="form-control">
                            <option value="">-- Seluruh Cabang di PMD Sragen --</option>
                            <?php foreach ($localCabang as $c): ?>
                                <option value="<?= $c['id'] ?>">
                                    <?= esc($c['name']) ?> (<?= esc($c['wilayah_name'] ?? 'Wilayah ' . $c['wilayah_id']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mb-0">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="checkOnlyPending" name="only_pending" value="1" checked>
                            <label class="custom-control-label font-weight-bold text-dark" for="checkOnlyPending">
                                Hanya periksa pemuda yang saat ini berstatus 'Menunggu Verifikasi' (Pending)
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success font-weight-bold" id="btnRunSyncVerifyAll">
                        <i class="fas fa-bolt mr-1"></i> Mulai Verifikasi Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
