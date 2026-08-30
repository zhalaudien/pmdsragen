<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>


<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-3">
    <div>
        <h4 class="font-weight-bold text-dark mb-1">Master Cabang</h4>
        <p class="text-muted text-xs mb-0">Kelola daftar cabang pemuda, pimpinan, kontak, dan jadwal gelombang pemuda se-Kabupaten Sragen.</p>
    </div>
    <div class="mt-2 mt-sm-0">
        <button class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#addCabangModal">
            <i class="fas fa-plus-circle mr-1"></i> Tambah Cabang
        </button>
    </div>
</div>

<!-- STATS SUMMARY CARDS -->
<div class="row mb-3">
    <div class="col-12 col-sm-6 col-md-4 mb-2 mb-md-0">
        <div class="info-box shadow-sm mb-0">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-sitemap"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-xs text-muted">Total Cabang</span>
                <span class="info-box-number text-lg font-weight-bold text-dark"><?= number_format($totalCabang ?? count($cabangList)) ?></span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4 mb-2 mb-md-0">
        <div class="info-box shadow-sm mb-0">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-calendar-check"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-xs text-muted">Sudah Ada Gelombang</span>
                <span class="info-box-number text-lg font-weight-bold text-success"><?= number_format($totalSudahGelombang ?? 0) ?> Cabang</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4">
        <div class="info-box shadow-sm mb-0">
            <span class="info-box-icon bg-secondary elevation-1"><i class="fas fa-clock"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-xs text-muted">Belum Ada Gelombang</span>
                <span class="info-box-number text-lg font-weight-bold text-secondary"><?= number_format($totalBelumGelombang ?? 0) ?> Cabang</span>
            </div>
        </div>
    </div>
</div>

<!-- FILTER CARD -->
<div class="card card-default card-outline shadow-sm mb-3">
    <div class="card-body p-2">
        <form action="<?= base_url('admin/cabang') ?>" method="GET" class="row align-items-center">
            <div class="col-12 col-md-4 mb-2 mb-md-0">
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama cabang, pimpinan, ustadz..." value="<?= esc($search ?? '') ?>">
                </div>
            </div>
            <div class="col-12 col-md-3 mb-2 mb-md-0">
                <select name="wilayah_id" class="form-control form-control-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Wilayah --</option>
                    <?php foreach ($wilayahList as $w): ?>
                        <option value="<?= $w['id'] ?>" <?= ($selectedW == $w['id']) ? 'selected' : '' ?>>
                            <?= esc($w['name']) ?> (<?= esc($w['code']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-3 mb-2 mb-md-0">
                <select name="has_gelombang" class="form-control form-control-sm" onchange="this.form.submit()">
                    <option value="">-- Status Gelombang --</option>
                    <option value="sudah" <?= (($selectedGelombang ?? '') === 'sudah') ? 'selected' : '' ?>>Sudah Ada Gelombang</option>
                    <option value="belum" <?= (($selectedGelombang ?? '') === 'belum') ? 'selected' : '' ?>>Belum Ada Gelombang</option>
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1 mr-1">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                <?php if (!empty($search) || !empty($selectedW) || !empty($selectedGelombang)): ?>
                    <a href="<?= base_url('admin/cabang') ?>" class="btn btn-default btn-sm" title="Reset Filter">
                        <i class="fas fa-undo"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- DATA TABLE CARD -->
<div class="card card-primary card-outline shadow-sm">
    <div class="card-header border-0 d-flex justify-content-between align-items-center py-2">
        <h3 class="card-title font-weight-bold text-sm">
            <i class="fas fa-sitemap mr-1 text-primary"></i> Daftar Cabang Pemuda
        </h3>
        <div class="card-tools text-muted text-xs">
            Menampilkan <?= count($cabangList) ?> cabang
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped table-vcenter mb-0" style="font-size: 0.88rem;">
                <thead class="thead-light">
                    <tr>
                        <th class="pl-3" style="width: 45px;">ID</th>
                        <th>Kode</th>
                        <th>Nama Cabang & Wilayah</th>
                        <th>Pimpinan & Kontak</th>
                        <th>Gelombang Pemuda</th>
                        <th>Alamat</th>
                        <th class="text-center">Total Pemuda</th>
                        <th class="text-right pr-3" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($cabangList)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fas fa-folder-open fa-2x mb-2 d-block text-muted"></i>
                                Belum ada data cabang yang sesuai filter.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($cabangList as $c): ?>
                            <tr>
                                <td class="pl-3 text-muted"><?= $c['id'] ?></td>
                                <td>
                                    <span class="badge badge-light border font-monospace"><?= esc($c['code'] ?: '-') ?></span>
                                </td>
                                <td>
                                    <div class="font-weight-bold text-dark"><?= esc($c['name']) ?></div>
                                    <span class="badge badge-primary px-2 py-0" style="font-size: 0.75rem;">
                                        <?= esc($c['wilayah_name']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($c['pimpinan_nama'])): ?>
                                        <div class="font-weight-bold text-dark text-xs mb-1">
                                            <i class="fas fa-user-tie text-primary mr-1"></i><?= esc($c['pimpinan_nama']) ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted text-xs">-</span>
                                    <?php endif; ?>

                                    <?php if (!empty($c['no_wa'])): ?>
                                        <?php $waLink = formatWaNumber($c['no_wa']); ?>
                                        <?php if (!empty($waLink)): ?>
                                        <div>
                                            <a href="<?= esc($waLink) ?>" target="_blank" rel="noopener noreferrer" class="badge badge-success text-white font-weight-normal px-2 py-1 text-decoration-none">
                                                <i class="fab fa-whatsapp mr-1"></i><?= esc($c['no_wa']) ?>
                                            </a>
                                        </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (($c['has_gelombang'] ?? 'belum') === 'sudah'): ?>
                                        <span class="badge badge-success px-2 py-1 mb-1 d-inline-block">
                                            <i class="fas fa-check-circle mr-1"></i> Sudah Ada
                                        </span>
                                        <div class="text-xs text-muted">
                                            <?php if (!empty($c['gelombang_hari']) || !empty($c['gelombang_jam'])): ?>
                                                <div><i class="far fa-clock text-info mr-1"></i><?= esc($c['gelombang_hari'] ?? '') ?><?= (!empty($c['gelombang_hari']) && !empty($c['gelombang_jam'])) ? ' • ' : '' ?><?= esc($c['gelombang_jam'] ?? '') ?></div>
                                            <?php endif; ?>
                                            <?php if (!empty($c['gelombang_ustadz'])): ?>
                                                <div><i class="fas fa-chalkboard-teacher text-secondary mr-1"></i><?= esc($c['gelombang_ustadz']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge badge-secondary px-2 py-1">
                                            <i class="fas fa-times-circle mr-1"></i> Belum Ada
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($c['alamat'])): ?>
                                        <div class="text-xs text-dark" style="max-width: 200px;" title="<?= esc($c['alamat']) ?>">
                                            <i class="fas fa-map-marker-alt text-danger mr-1"></i><?= esc(character_limiter($c['alamat'], 60)) ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted text-xs">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= base_url('admin/pemuda?cabang_id=' . $c['id']) ?>" class="badge badge-info px-2 py-1 text-decoration-none shadow-xs" title="Lihat daftar pemuda cabang ini">
                                        <i class="fas fa-users mr-1"></i> <?= number_format($c['total_pemuda'] ?? 0) ?> Pemuda
                                    </a>
                                </td>
                                <td class="text-right pr-3">
                                    <div class="btn-group btn-group-sm shadow-xs">
                                        <button type="button" 
                                                class="btn btn-info btn-xs btn-detail-cabang" 
                                                data-id="<?= $c['id'] ?>"
                                                data-wid="<?= $c['wilayah_id'] ?>"
                                                data-wname="<?= esc($c['wilayah_name']) ?>"
                                                data-code="<?= esc($c['code'] ?? '') ?>"
                                                data-name="<?= esc($c['name']) ?>"
                                                data-alamat="<?= esc($c['alamat'] ?? '') ?>"
                                                data-pimpinan="<?= esc($c['pimpinan_nama'] ?? '') ?>"
                                                data-nowa="<?= esc($c['no_wa'] ?? '') ?>"
                                                data-hasgelombang="<?= esc($c['has_gelombang'] ?? 'belum') ?>"
                                                data-hari="<?= esc($c['gelombang_hari'] ?? '') ?>"
                                                data-jam="<?= esc($c['gelombang_jam'] ?? '') ?>"
                                                data-ustadz="<?= esc($c['gelombang_ustadz'] ?? '') ?>"
                                                data-desc="<?= esc($c['description'] ?? '') ?>"
                                                data-totalpemuda="<?= number_format($c['total_pemuda'] ?? 0) ?>"
                                                title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-primary btn-xs btn-edit-cabang" 
                                                data-id="<?= $c['id'] ?>"
                                                data-wid="<?= $c['wilayah_id'] ?>"
                                                data-code="<?= esc($c['code'] ?? '') ?>"
                                                data-name="<?= esc($c['name']) ?>"
                                                data-alamat="<?= esc($c['alamat'] ?? '') ?>"
                                                data-pimpinan="<?= esc($c['pimpinan_nama'] ?? '') ?>"
                                                data-nowa="<?= esc($c['no_wa'] ?? '') ?>"
                                                data-hasgelombang="<?= esc($c['has_gelombang'] ?? 'belum') ?>"
                                                data-hari="<?= esc($c['gelombang_hari'] ?? '') ?>"
                                                data-jam="<?= esc($c['gelombang_jam'] ?? '') ?>"
                                                data-ustadz="<?= esc($c['gelombang_ustadz'] ?? '') ?>"
                                                data-desc="<?= esc($c['description'] ?? '') ?>"
                                                title="Edit Cabang">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-danger btn-xs btn-delete-cabang" 
                                                data-id="<?= $c['id'] ?>"
                                                data-name="<?= esc($c['name']) ?>"
                                                title="Hapus Cabang">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php if ($pager->getPageCount('cabang') > 1): ?>
        <div class="card-footer bg-white border-top py-2 d-flex justify-content-between align-items-center">
            <div class="text-xs text-muted">Halaman <?= $pager->getCurrentPage('cabang') ?> dari <?= $pager->getPageCount('cabang') ?></div>
            <div><?= $pager->links('cabang', 'default_full') ?></div>
        </div>
    <?php endif; ?>
</div>

<!-- ========================================== -->
<!-- MODAL DETAIL CABANG                        -->
<!-- ========================================== -->
<div class="modal fade" id="detailCabangModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-info text-white py-2">
                <h5 class="modal-title font-weight-bold text-sm">
                    <i class="fas fa-info-circle mr-1"></i> Detail Cabang: <span id="detailCabangTitle"></span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3">
                <div class="row">
                    <!-- INFORMASI UTAMA & ALAMAT -->
                    <div class="col-md-6 mb-3 mb-md-0 border-right">
                        <h6 class="font-weight-bold text-primary text-xs text-uppercase mb-2">
                            <i class="fas fa-sitemap mr-1"></i> Informasi Cabang
                        </h6>
                        <table class="table table-sm table-borderless text-xs mb-3">
                            <tr>
                                <td class="text-muted" style="width: 35%;">Nama Cabang</td>
                                <td class="font-weight-bold text-dark" id="detailCabangName">-</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Kode Cabang</td>
                                <td><span class="badge badge-light border font-monospace" id="detailCabangCode">-</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Wilayah</td>
                                <td><span class="badge badge-primary px-2 py-0" id="detailCabangWilayah">-</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Total Pemuda</td>
                                <td>
                                    <span class="badge badge-success px-2 py-0" id="detailCabangTotalPemuda">0 Pemuda</span>
                                </td>
                            </tr>
                        </table>

                        <h6 class="font-weight-bold text-primary text-xs text-uppercase mb-2">
                            <i class="fas fa-user-tie mr-1"></i> Pimpinan & Kontak
                        </h6>
                        <table class="table table-sm table-borderless text-xs mb-3">
                            <tr>
                                <td class="text-muted" style="width: 35%;">Nama Pimpinan</td>
                                <td class="font-weight-bold text-dark" id="detailCabangPimpinan">-</td>
                            </tr>
                            <tr>
                                <td class="text-muted">No. WhatsApp</td>
                                <td id="detailCabangWaContainer">-</td>
                            </tr>
                        </table>

                        <h6 class="font-weight-bold text-primary text-xs text-uppercase mb-1">
                            <i class="fas fa-map-marker-alt mr-1"></i> Alamat Lengkap
                        </h6>
                        <p class="text-xs text-dark bg-light p-2 rounded mb-0" id="detailCabangAlamat">-</p>
                    </div>

                    <!-- GELOMBANG PEMUDA & DESKRIPSI -->
                    <div class="col-md-6">
                        <h6 class="font-weight-bold text-success text-xs text-uppercase mb-2">
                            <i class="fas fa-users-class mr-1"></i> Status Gelombang Pemuda
                        </h6>
                        <div class="p-3 bg-light rounded mb-3 border">
                            <div class="mb-2" id="detailCabangGelombangBadge">
                                <span class="badge badge-secondary px-2 py-1">Belum Ada</span>
                            </div>
                            <div id="detailCabangGelombangSection">
                                <table class="table table-sm table-borderless text-xs mb-0">
                                    <tr>
                                        <td class="text-muted" style="width: 38%;"><i class="far fa-calendar-alt text-info mr-1"></i> Hari Masuk</td>
                                        <td class="font-weight-bold text-dark" id="detailCabangHari">-</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><i class="far fa-clock text-info mr-1"></i> Jam Masuk</td>
                                        <td class="font-weight-bold text-dark" id="detailCabangJam">-</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><i class="fas fa-chalkboard-teacher text-info mr-1"></i> Ustadz Pengampu</td>
                                        <td class="font-weight-bold text-dark" id="detailCabangUstadz">-</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <h6 class="font-weight-bold text-muted text-xs text-uppercase mb-1">
                            <i class="fas fa-comment-alt mr-1"></i> Deskripsi / Catatan
                        </h6>
                        <p class="text-xs text-muted bg-light p-2 rounded mb-0" id="detailCabangDesc">-</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2 bg-light">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Tutup</button>
                <a href="#" id="detailCabangPemudaLink" class="btn btn-info btn-sm">
                    <i class="fas fa-users mr-1"></i> Lihat Data Pemuda
                </a>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL ADD CABANG                           -->
<!-- ========================================== -->
<div class="modal fade" id="addCabangModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title font-weight-bold text-sm">
                    <i class="fas fa-plus-circle mr-1"></i> Tambah Cabang Baru
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('admin/cabang/simpan') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-3">
                    <div class="row">
                        <!-- BAGIAN 1: INFORMASI CABANG -->
                        <div class="col-md-6 border-right">
                            <h6 class="font-weight-bold text-primary text-xs text-uppercase mb-2">
                                <i class="fas fa-sitemap mr-1"></i> Identitas Cabang
                            </h6>
                            <div class="form-group mb-2">
                                <label class="text-xs text-muted font-weight-bold mb-1">Wilayah <span class="text-danger">*</span></label>
                                <select name="wilayah_id" class="form-control form-control-sm" required>
                                    <option value="">-- Pilih Wilayah --</option>
                                    <?php foreach ($wilayahList as $w): ?>
                                        <option value="<?= $w['id'] ?>"><?= esc($w['name']) ?> (<?= esc($w['code']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group mb-2">
                                <label class="text-xs text-muted font-weight-bold mb-1">Kode Cabang</label>
                                <input type="text" name="code" class="form-control form-control-sm" placeholder="Contoh: CBG-001">
                            </div>
                            <div class="form-group mb-2">
                                <label class="text-xs text-muted font-weight-bold mb-1">Nama Cabang <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control form-control-sm" placeholder="Contoh: Gesi / Mondokan 1" required>
                            </div>
                            <div class="form-group mb-2">
                                <label class="text-xs text-muted font-weight-bold mb-1">Alamat Cabang / Sekretariat</label>
                                <textarea name="alamat" class="form-control form-control-sm" rows="2" placeholder="Contoh: Jl. Raya Gesi Km 2, RT 03/RW 01, Gesi"></textarea>
                            </div>
                            <div class="form-group mb-2">
                                <label class="text-xs text-muted font-weight-bold mb-1">Nama Pimpinan Cabang</label>
                                <input type="text" name="pimpinan_nama" class="form-control form-control-sm" placeholder="Contoh: Bp. H. Ahmad Fauzi">
                            </div>
                            <div class="form-group mb-2">
                                <label class="text-xs text-muted font-weight-bold mb-1">No. WhatsApp Pimpinan / Kontak</label>
                                <input type="text" name="no_wa" class="form-control form-control-sm" placeholder="Contoh: 081234567890">
                            </div>
                        </div>

                        <!-- BAGIAN 2: GELOMBANG PEMUDA & DESKRIPSI -->
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-success text-xs text-uppercase mb-2">
                                <i class="fas fa-users-class mr-1"></i> Gelombang Pemuda
                            </h6>
                            <div class="form-group mb-2">
                                <label class="text-xs text-muted font-weight-bold mb-1">Sudah Ada Gelombang Pemuda? <span class="text-danger">*</span></label>
                                <select name="has_gelombang" id="addHasGelombang" class="form-control form-control-sm" required>
                                    <option value="belum">Belum Ada</option>
                                    <option value="sudah">Sudah Ada</option>
                                </select>
                            </div>

                            <div id="addGelombangFields" class="p-2 bg-light rounded mb-2 border" style="display: none;">
                                <div class="form-group mb-2">
                                    <label class="text-xs text-dark font-weight-bold mb-1">Hari Masuk / Kegiatan</label>
                                    <input type="text" name="gelombang_hari" class="form-control form-control-sm" placeholder="Contoh: Ahad / Setiap Senin malam">
                                </div>
                                <div class="form-group mb-2">
                                    <label class="text-xs text-dark font-weight-bold mb-1">Jam Masuk / Waktu</label>
                                    <input type="text" name="gelombang_jam" class="form-control form-control-sm" placeholder="Contoh: 19:30 - 21:00 WIB">
                                </div>
                                <div class="form-group mb-0">
                                    <label class="text-xs text-dark font-weight-bold mb-1">Ustadz yang Mengampu</label>
                                    <input type="text" name="gelombang_ustadz" class="form-control form-control-sm" placeholder="Contoh: Ust. M. Ihsan, S.Pd.I">
                                </div>
                            </div>

                            <h6 class="font-weight-bold text-muted text-xs text-uppercase mt-3 mb-2">
                                <i class="fas fa-comment-alt mr-1"></i> Keterangan Tambahan
                            </h6>
                            <div class="form-group mb-0">
                                <textarea name="description" class="form-control form-control-sm" rows="3" placeholder="Catatan atau deskripsi cabang..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2 bg-light">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3">
                        <i class="fas fa-save mr-1"></i> Simpan Cabang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL EDIT CABANG                          -->
<!-- ========================================== -->
<div class="modal fade" id="editCabangModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title font-weight-bold text-sm">
                    <i class="fas fa-edit mr-1"></i> Edit Cabang
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editCabangForm" action="" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-3">
                    <div class="row">
                        <!-- BAGIAN 1: INFORMASI CABANG -->
                        <div class="col-md-6 border-right">
                            <h6 class="font-weight-bold text-primary text-xs text-uppercase mb-2">
                                <i class="fas fa-sitemap mr-1"></i> Identitas Cabang
                            </h6>
                            <div class="form-group mb-2">
                                <label class="text-xs text-muted font-weight-bold mb-1">Wilayah <span class="text-danger">*</span></label>
                                <select name="wilayah_id" id="editCabangWid" class="form-control form-control-sm" required>
                                    <option value="">-- Pilih Wilayah --</option>
                                    <?php foreach ($wilayahList as $w): ?>
                                        <option value="<?= $w['id'] ?>"><?= esc($w['name']) ?> (<?= esc($w['code']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group mb-2">
                                <label class="text-xs text-muted font-weight-bold mb-1">Kode Cabang</label>
                                <input type="text" name="code" id="editCabangCode" class="form-control form-control-sm" placeholder="Contoh: CBG-001">
                            </div>
                            <div class="form-group mb-2">
                                <label class="text-xs text-muted font-weight-bold mb-1">Nama Cabang <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="editCabangName" class="form-control form-control-sm" required>
                            </div>
                            <div class="form-group mb-2">
                                <label class="text-xs text-muted font-weight-bold mb-1">Alamat Cabang / Sekretariat</label>
                                <textarea name="alamat" id="editCabangAlamat" class="form-control form-control-sm" rows="2"></textarea>
                            </div>
                            <div class="form-group mb-2">
                                <label class="text-xs text-muted font-weight-bold mb-1">Nama Pimpinan Cabang</label>
                                <input type="text" name="pimpinan_nama" id="editCabangPimpinan" class="form-control form-control-sm" placeholder="Contoh: Bp. H. Ahmad Fauzi">
                            </div>
                            <div class="form-group mb-2">
                                <label class="text-xs text-muted font-weight-bold mb-1">No. WhatsApp Pimpinan / Kontak</label>
                                <input type="text" name="no_wa" id="editCabangNowa" class="form-control form-control-sm" placeholder="Contoh: 081234567890">
                            </div>
                        </div>

                        <!-- BAGIAN 2: GELOMBANG PEMUDA & DESKRIPSI -->
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-success text-xs text-uppercase mb-2">
                                <i class="fas fa-users-class mr-1"></i> Gelombang Pemuda
                            </h6>
                            <div class="form-group mb-2">
                                <label class="text-xs text-muted font-weight-bold mb-1">Sudah Ada Gelombang Pemuda? <span class="text-danger">*</span></label>
                                <select name="has_gelombang" id="editHasGelombang" class="form-control form-control-sm" required>
                                    <option value="belum">Belum Ada</option>
                                    <option value="sudah">Sudah Ada</option>
                                </select>
                            </div>

                            <div id="editGelombangFields" class="p-2 bg-light rounded mb-2 border">
                                <div class="form-group mb-2">
                                    <label class="text-xs text-dark font-weight-bold mb-1">Hari Masuk / Kegiatan</label>
                                    <input type="text" name="gelombang_hari" id="editCabangHari" class="form-control form-control-sm" placeholder="Contoh: Ahad / Setiap Senin malam">
                                </div>
                                <div class="form-group mb-2">
                                    <label class="text-xs text-dark font-weight-bold mb-1">Jam Masuk / Waktu</label>
                                    <input type="text" name="gelombang_jam" id="editCabangJam" class="form-control form-control-sm" placeholder="Contoh: 19:30 - 21:00 WIB">
                                </div>
                                <div class="form-group mb-0">
                                    <label class="text-xs text-dark font-weight-bold mb-1">Ustadz yang Mengampu</label>
                                    <input type="text" name="gelombang_ustadz" id="editCabangUstadz" class="form-control form-control-sm" placeholder="Contoh: Ust. M. Ihsan, S.Pd.I">
                                </div>
                            </div>

                            <h6 class="font-weight-bold text-muted text-xs text-uppercase mt-3 mb-2">
                                <i class="fas fa-comment-alt mr-1"></i> Keterangan Tambahan
                            </h6>
                            <div class="form-group mb-0">
                                <textarea name="description" id="editCabangDesc" class="form-control form-control-sm" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2 bg-light">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL DELETE CABANG                        -->
<!-- ========================================== -->
<div class="modal fade" id="deleteCabangModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white py-2">
                <h5 class="modal-title font-weight-bold text-sm">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Hapus Cabang
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="fas fa-trash-alt text-danger fa-3x mb-3 d-block"></i>
                <h6 class="font-weight-bold text-dark mb-2">Konfirmasi Hapus Cabang</h6>
                <p class="text-muted text-xs mb-4">Apakah Anda yakin ingin menghapus cabang <strong id="deleteCabangName" class="text-dark"></strong>?</p>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-default btn-sm px-3 mr-2" data-dismiss="modal">Batal</button>
                    <form id="deleteCabangForm" method="POST" action="">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-danger btn-sm px-4">
                            <i class="fas fa-trash mr-1"></i> Hapus Cabang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function () {
        // Toggle gelombang fields in Add Modal
        $('#addHasGelombang').on('change', function () {
            if ($(this).val() === 'sudah') {
                $('#addGelombangFields').slideDown(200);
            } else {
                $('#addGelombangFields').slideUp(200);
            }
        });

        // Toggle gelombang fields in Edit Modal
        $('#editHasGelombang').on('change', function () {
            if ($(this).val() === 'sudah') {
                $('#editGelombangFields').slideDown(200);
            } else {
                $('#editGelombangFields').slideUp(200);
            }
        });

        // Detail modal handler
        $(document).on('click', '.btn-detail-cabang', function () {
            const id           = $(this).data('id');
            const wid          = $(this).data('wid');
            const wname        = $(this).data('wname') || '-';
            const code         = $(this).data('code') || '-';
            const name         = $(this).data('name') || '-';
            const alamat       = $(this).data('alamat') || '-';
            const pimpinan     = $(this).data('pimpinan') || '-';
            const nowa         = $(this).data('nowa') || '';
            const hasGelombang = $(this).data('hasgelombang') || 'belum';
            const hari         = $(this).data('hari') || '-';
            const jam          = $(this).data('jam') || '-';
            const ustadz       = $(this).data('ustadz') || '-';
            const desc         = $(this).data('desc') || '-';
            const totalPemuda  = $(this).data('totalpemuda') || '0';

            $('#detailCabangTitle').text(name);
            $('#detailCabangName').text(name);
            $('#detailCabangCode').text(code);
            $('#detailCabangWilayah').text(wname);
            $('#detailCabangTotalPemuda').text(totalPemuda + ' Pemuda');
            $('#detailCabangPimpinan').text(pimpinan);
            $('#detailCabangAlamat').text(alamat);
            $('#detailCabangDesc').text(desc);

            if (nowa) {
                let clean = nowa.replace(/[^0-9]/g, '');
                if (clean.startsWith('0')) clean = '62' + clean.slice(1);
                else if (clean.startsWith('8')) clean = '62' + clean;
                $('#detailCabangWaContainer').html(`
                    <a href="https://wa.me/${clean}" target="_blank" class="badge badge-success text-white px-2 py-1 text-decoration-none">
                        <i class="fab fa-whatsapp mr-1"></i> ${nowa}
                    </a>
                `);
            } else {
                $('#detailCabangWaContainer').text('-');
            }

            if (hasGelombang === 'sudah') {
                $('#detailCabangGelombangBadge').html(`
                    <span class="badge badge-success px-2 py-1">
                        <i class="fas fa-check-circle mr-1"></i> Sudah Terselenggara Gelombang Pemuda
                    </span>
                `);
                $('#detailCabangHari').text(hari);
                $('#detailCabangJam').text(jam);
                $('#detailCabangUstadz').text(ustadz);
                $('#detailCabangGelombangSection').show();
            } else {
                $('#detailCabangGelombangBadge').html(`
                    <span class="badge badge-secondary px-2 py-1">
                        <i class="fas fa-times-circle mr-1"></i> Belum Terselenggara Gelombang Pemuda
                    </span>
                `);
                $('#detailCabangGelombangSection').hide();
            }

            $('#detailCabangPemudaLink').attr('href', '<?= base_url('admin/pemuda?cabang_id=') ?>' + id);
            $('#detailCabangModal').modal('show');
        });

        // Edit modal handler
        $(document).on('click', '.btn-edit-cabang', function () {
            const id           = $(this).data('id');
            const wid          = $(this).data('wid');
            const code         = $(this).data('code') || '';
            const name         = $(this).data('name') || '';
            const alamat       = $(this).data('alamat') || '';
            const pimpinan     = $(this).data('pimpinan') || '';
            const nowa         = $(this).data('nowa') || '';
            const hasGelombang = $(this).data('hasgelombang') || 'belum';
            const hari         = $(this).data('hari') || '';
            const jam          = $(this).data('jam') || '';
            const ustadz       = $(this).data('ustadz') || '';
            const desc         = $(this).data('desc') || '';

            $('#editCabangWid').val(wid);
            $('#editCabangCode').val(code);
            $('#editCabangName').val(name);
            $('#editCabangAlamat').val(alamat);
            $('#editCabangPimpinan').val(pimpinan);
            $('#editCabangNowa').val(nowa);
            $('#editHasGelombang').val(hasGelombang);
            $('#editCabangHari').val(hari);
            $('#editCabangJam').val(jam);
            $('#editCabangUstadz').val(ustadz);
            $('#editCabangDesc').val(desc);

            if (hasGelombang === 'sudah') {
                $('#editGelombangFields').show();
            } else {
                $('#editGelombangFields').hide();
            }

            $('#editCabangForm').attr('action', '<?= base_url('admin/cabang/update/') ?>/' + id);
            $('#editCabangModal').modal('show');
        });

        // Delete modal handler
        $(document).on('click', '.btn-delete-cabang', function () {
            const id   = $(this).data('id');
            const name = $(this).data('name');
            $('#deleteCabangName').text(name);
            $('#deleteCabangForm').attr('action', '<?= base_url('admin/cabang/delete/') ?>/' + id);
            $('#deleteCabangModal').modal('show');
        });
    });
</script>
<?= $this->endSection() ?>
