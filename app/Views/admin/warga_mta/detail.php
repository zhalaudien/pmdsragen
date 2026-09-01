<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<?php
$isMale = strtoupper($warga['kelamin'] ?? 'L') === 'L';
$isLocal = !empty($warga['is_local_registered']);
$waUrl = !empty($warga['nohp']) ? formatWaNumber($warga['nohp']) : null;
?>

<!-- BREADCRUMB & BACK BUTTON -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <a href="<?= base_url('admin/warga-mta') ?>" class="btn btn-outline-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Warga MTA
        </a>
    </div>
    <div>
        <?php if ($isLocal): ?>
            <a href="<?= base_url('admin/pemuda/detail/' . $warga['local_pemuda_id']) ?>" class="btn btn-success btn-sm shadow-sm font-weight-bold">
                <i class="fas fa-external-link-alt mr-1"></i> Buka di Data Pemuda PMD
            </a>
        <?php else: ?>
            <button type="button" class="btn btn-success btn-sm shadow-sm font-weight-bold" data-toggle="modal" data-target="#modalImportSingle">
                <i class="fas fa-user-plus mr-1"></i> Daftarkan ke Pemuda PMD
            </button>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <!-- PROFIL CARD -->
    <div class="col-md-4 mb-3">
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-body box-profile text-center">
                <div class="text-center mb-3">
                    <div class="rounded-circle bg-light border d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 100px; height: 100px; font-size: 2.8rem; color: <?= $isMale ? '#007bff' : '#e83e8c' ?>;">
                        <i class="fas fa-user"></i>
                    </div>
                </div>

                <h4 class="profile-username text-center font-weight-bold mb-1"><?= esc($warga['nama'] ?? '-') ?></h4>
                <p class="text-muted text-center mb-2">
                    <span class="badge <?= $isMale ? 'badge-primary' : 'badge-pink' ?>" style="<?= !$isMale ? 'background-color:#e83e8c;color:#fff;' : '' ?>">
                        <i class="fas <?= $isMale ? 'fa-mars' : 'fa-venus' ?> mr-1"></i> <?= $isMale ? 'Putra (Laki-laki)' : 'Putri (Perempuan)' ?>
                    </span>
                    <span class="badge <?= (($warga['status'] ?? '') === 'Warga') ? 'badge-info' : 'badge-secondary' ?> ml-1">
                        <?= esc($warga['status'] ?? 'Warga') ?>
                    </span>
                </p>

                <ul class="list-group list-group-unbordered mb-3 text-left text-sm">
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Nomor Warga:</span>
                        <strong class="text-monospace"><?= esc($warga['nomor'] ?? '-') ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Usia:</span>
                        <strong><?= esc($warga['usia'] ?? '-') ?> Tahun</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Cabang MTA:</span>
                        <strong class="text-primary"><?= esc($warga['cabang'] ?? '-') ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Perwakilan:</span>
                        <strong><?= esc($warga['perwakilan'] ?? 'Sragen') ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Status di PMD:</span>
                        <?php if ($isLocal): ?>
                            <span class="badge badge-success">Terdaftar (<?= esc($warga['local_reg_number']) ?>)</span>
                        <?php else: ?>
                            <span class="badge badge-warning text-dark">Belum Terdaftar</span>
                        <?php endif; ?>
                    </li>
                </ul>

                <?php if ($waUrl): ?>
                    <a href="<?= esc($waUrl) ?>" target="_blank" class="btn btn-success btn-block shadow-sm">
                        <i class="fab fa-whatsapp mr-1"></i> Hubungi via WhatsApp
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- DETAIL INFORMASI CARD -->
    <div class="col-md-8">
        <div class="card card-default card-outline shadow-sm mb-3">
            <div class="card-header border-0">
                <h3 class="card-title font-weight-bold text-sm">
                    <i class="fas fa-info-circle mr-1 text-primary"></i> Data Pribadi &amp; Riwayat
                </h3>
            </div>
            <div class="card-body pt-0">
                <table class="table table-sm table-striped text-sm mb-0">
                    <tr>
                        <th style="width: 30%;">Tempat, Tanggal Lahir</th>
                        <td><?= esc($warga['kabupaten'] ?? 'Sragen') ?>, <?= esc($warga['lahir'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Status Pernikahan</th>
                        <td><?= esc($warga['menikah'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Pekerjaan</th>
                        <td><?= esc($warga['pekerjaan'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Golongan Darah</th>
                        <td><?= esc($warga['goldar'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Tahun Mulai Mengaji</th>
                        <td><?= esc($warga['mengaji'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Nama Ayah</th>
                        <td><strong><?= esc($warga['ayah'] ?? '-') ?></strong></td>
                    </tr>
                    <tr>
                        <th>Nama Ibu</th>
                        <td><strong><?= esc($warga['ibu'] ?? '-') ?></strong></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card card-default card-outline shadow-sm">
            <div class="card-header border-0">
                <h3 class="card-title font-weight-bold text-sm">
                    <i class="fas fa-map-marked-alt mr-1 text-success"></i> Domisili &amp; Alamat Lengkap
                </h3>
            </div>
            <div class="card-body pt-0">
                <table class="table table-sm table-striped text-sm mb-0">
                    <tr>
                        <th style="width: 30%;">Alamat RT/RW</th>
                        <td><?= esc($warga['alamat_rtrw'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Desa / Kelurahan</th>
                        <td><?= esc($warga['desa'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Kecamatan</th>
                        <td><?= esc($warga['kecamatan'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Kabupaten</th>
                        <td><?= esc($warga['kabupaten'] ?? 'Kab. Sragen') ?></td>
                    </tr>
                    <tr>
                        <th>Provinsi</th>
                        <td><?= esc($warga['provinsi'] ?? 'Jawa Tengah') ?></td>
                    </tr>
                    <tr>
                        <th>Alamat Lengkap</th>
                        <td><?= esc($warga['alamat'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>UUID API MTA</th>
                        <td><code><?= esc($warga['uuid'] ?? '-') ?></code></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL IMPOR KE PMD -->
<?php if (!$isLocal): ?>
<div class="modal fade" id="modalImportSingle" tabindex="-1" role="dialog" aria-labelledby="modalImportSingleLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= base_url('admin/warga-mta/import') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="warga_uuid" value="<?= esc($warga['uuid']) ?>">

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title font-weight-bold" id="modalImportSingleLabel">
                        <i class="fas fa-user-plus mr-1"></i> Daftarkan ke Pemuda PMD
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-sm text-muted">
                        Pilih cabang PMD Sragen tujuan untuk mendaftarkan <strong><?= esc($warga['nama']) ?></strong>:
                    </p>
                    <div class="form-group">
                        <label class="font-weight-semibold">Cabang PMD Sragen <span class="text-danger">*</span></label>
                        <select name="cabang_id" class="form-control select2" required style="width: 100%;">
                            <option value="">-- Pilih Cabang --</option>
                            <?php foreach ($localCabangList as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= esc($c['name']) ?> (<?= esc($c['wilayah_name'] ?? '') ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm font-weight-bold">
                        <i class="fas fa-check-circle mr-1"></i> Daftarkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
