<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<!-- BREADCRUMB / BACK -->
<div class="mb-3">
    <a href="<?= base_url('admin/pemuda') ?>" class="btn btn-default btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Pemuda
    </a>
</div>

<?php
    $isMale   = ($pemuda['gender'] ?? 'L') === 'L';
    $photoUrl = null;
    if (!empty($pemuda['foto']) && file_exists(FCPATH . 'uploads/pemuda/' . $pemuda['foto'])) {
        $photoUrl = base_url('uploads/pemuda/' . $pemuda['foto']);
    } elseif (!empty($pemuda['mta_foto_url'])) {
        $photoUrl = esc($pemuda['mta_foto_url']);
    }
?>

<!-- PROFILE HERO CARD -->
<div class="card card-primary card-outline shadow-sm mb-3">
    <div class="card-body p-3 p-md-4">
        <div class="row align-items-center">
            <!-- FOTO PROFILE DIPERBESAR -->
            <div class="col-12 col-md-auto text-center mb-3 mb-md-0">
                <div class="detail-avatar-container">
                    <?php if (!empty($photoUrl)): ?>
                        <a href="javascript:void(0);" data-toggle="modal" data-target="#modalFotoPreview" class="d-block position-relative" title="Klik untuk memperbesar foto profil">
                            <img src="<?= $photoUrl ?>" 
                                 alt="<?= esc($pemuda['name']) ?>" 
                                 class="rounded-circle shadow detail-profile-img border border-3 <?= $isMale ? 'border-primary' : 'border-danger' ?>" 
                                 style="width: 130px; height: 130px; object-fit: cover;">
                            <span class="detail-avatar-zoom-badge shadow-sm" title="Perbesar Foto">
                                <i class="fas fa-search-plus"></i>
                            </span>
                        </a>
                        <div class="mt-2">
                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 shadow-2xs" data-toggle="modal" data-target="#modalFotoPreview">
                                <i class="fas fa-expand-alt mr-1 text-primary"></i> Perbesar Foto
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="rounded-circle shadow d-inline-flex align-items-center justify-content-center text-white font-weight-bold border border-3 border-light" 
                             style="width: 130px; height: 130px; font-size: 3.2rem; background: <?= $isMale ? 'linear-gradient(135deg, #007bff, #0056b3)' : 'linear-gradient(135deg, #e83e8c, #c2185b)' ?>;">
                            <?= strtoupper(substr($pemuda['name'], 0, 1)) ?>
                        </div>
                        <div class="mt-2 text-muted text-xs">
                            <i class="fas fa-user mr-1"></i> Tanpa Foto Profil
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- INFO UTAMA -->
            <div class="col-12 col-md text-center text-md-left">
                <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start mb-2">
                    <h3 class="font-weight-bold text-dark mb-1 mr-2"><?= esc($pemuda['name']) ?></h3>
                    
                    <!-- Verif badge -->
                    <?php if ($pemuda['status_verifikasi'] === 'verified'): ?>
                        <span class="badge badge-success px-2 py-1 font-weight-bold mr-1 mb-1" title="Data Sinkron dengan MTA Pusat"><i class="fas fa-check-circle mr-1"></i> Terverifikasi</span>
                    <?php else: ?>
                        <span class="badge badge-secondary px-2 py-1 font-weight-bold mr-1 mb-1" title="Data Belum Sinkron dengan MTA Pusat"><i class="fas fa-clock mr-1"></i> Belum Terverifikasi</span>
                    <?php endif; ?>

                    <!-- Active / Archived badge -->
                    <?php if ($pemuda['status_data'] === 'archived'): ?>
                        <span class="badge badge-secondary px-2 py-1 font-weight-bold mr-1 mb-1"><i class="fas fa-archive mr-1"></i> Data Arsip</span>
                    <?php else: ?>
                        <span class="badge badge-primary px-2 py-1 font-weight-bold mr-1 mb-1">Aktif</span>
                    <?php endif; ?>

                    <!-- Gender badge -->
                    <span class="badge <?= $isMale ? 'badge-info' : 'badge-danger' ?> px-2 py-1 font-weight-bold mb-1">
                        <i class="fas <?= $isMale ? 'fa-mars' : 'fa-venus' ?> mr-1"></i> <?= $isMale ? 'Laki-laki' : 'Perempuan' ?>
                    </span>
                </div>

                <div class="text-muted d-flex flex-wrap align-items-center justify-content-center justify-content-md-start text-xs mt-2">
                    <div class="mr-3 mb-1"><i class="fas fa-id-card text-primary mr-1"></i> No. Reg: <strong class="text-dark"><?= esc($pemuda['registration_number']) ?></strong></div>
                    <div class="mr-3 mb-1"><i class="fas fa-sitemap text-success mr-1"></i> Cabang: <strong class="text-dark"><?= esc($pemuda['cabang_name']) ?></strong> (<?= esc($pemuda['wilayah_name']) ?>)</div>
                    <div class="mb-1"><i class="fas fa-calendar-alt text-secondary mr-1"></i> Terdaftar: <?= date('d F Y, H:i', strtotime($pemuda['created_at'])) ?> WIB</div>
                </div>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="col-12 col-xl-auto text-center text-xl-right mt-3 mt-xl-0">
                <button type="button" class="btn btn-outline-info btn-sm mb-1 shadow-sm" id="btnLiveSyncMta" title="Sinkronkan dengan Database Warga MTA">
                    <i class="fas fa-sync-alt mr-1" id="iconSyncMta"></i> Sinkronkan MTA
                </button>

                <a href="<?= base_url('admin/pemuda/cetak/' . $pemuda['id']) ?>" target="_blank" class="btn btn-default btn-sm mb-1 shadow-sm">
                    <i class="fas fa-print mr-1"></i> Cetak Dokumen
                </a>

                <a href="<?= base_url('admin/pemuda/edit/' . $pemuda['id']) ?>" class="btn btn-primary btn-sm mb-1 shadow-sm">
                    <i class="fas fa-edit mr-1"></i> Edit Data
                </a>
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

    <!-- KOLOM KANAN: PENDIDIKAN, PEKERJAAN, ELEMENT DAKWAH, SKILLS, INTERESTS -->
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
                        <?php if (!empty($pemuda['business_name']) || (int)($pemuda['job_status_id'] ?? 0) === 5): ?>
                            <tr>
                                <td class="text-muted">Nama Usaha</td>
                                <td class="font-weight-bold text-primary">: <?= esc($pemuda['business_name'] ?: ($pemuda['company_name'] ?: '-')) ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Alamat Usaha</td>
                                <td class="text-dark">: <?= esc($pemuda['business_address'] ?: '-') ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Kontak / CP Usaha</td>
                                <td class="text-dark">: 
                                    <?php if (!empty($pemuda['business_contact'])): ?>
                                        <a href="<?= esc(formatWaNumber($pemuda['business_contact'])) ?>" target="_blank" class="text-success font-weight-bold">
                                            <i class="fab fa-whatsapp mr-1"></i><?= esc($pemuda['business_contact']) ?>
                                        </a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Sosmed / Web Usaha</td>
                                <td class="text-dark">: 
                                    <?php if (!empty($pemuda['business_social'])): ?>
                                        <span class="text-primary"><i class="fas fa-globe mr-1"></i><?= esc($pemuda['business_social']) ?></span>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td class="text-muted">Instansi / Perusahaan</td>
                                <td class="text-dark">: <?= esc($pemuda['company_name'] ?: '-') ?></td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td class="text-muted">Sektor / Bidang Usaha</td>
                            <td class="text-dark">: <?= esc($pemuda['business_field'] ?: '-') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 4. ELEMENT DAKWAH -->
        <div class="card card-success card-outline shadow-sm mb-3">
            <div class="card-header border-0">
                <h3 class="card-title font-weight-bold text-sm">
                    <i class="fas fa-users text-success mr-1"></i> Keikutsertaan Element Dakwah
                </h3>
            </div>
            <div class="card-body p-3">
                <?php if (empty($pemuda['organisasi'])): ?>
                    <div class="text-center text-muted text-xs">Tidak ada keikutsertaan element dakwah yang tercatat.</div>
                <?php else: ?>
                    <div class="d-flex flex-wrap" style="gap: 8px;">
                        <?php foreach ($pemuda['organisasi'] as $org): ?>
                            <span class="badge badge-light border px-3 py-2 text-xs font-weight-bold text-dark shadow-none">
                                <i class="fas fa-check-circle text-success mr-1"></i> <?= esc(ucwords($org['organization_name'])) ?>
                            </span>
                        <?php endforeach; ?>
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

        <!-- 6. INTEGRASI DATABASE WARGA MTA -->
        <div class="card card-outline <?= !empty($pemuda['mta_warga_uuid']) ? 'card-success' : 'card-secondary' ?> shadow-sm mb-3">
            <div class="card-header border-0 d-flex align-items-center justify-content-between">
                <h3 class="card-title font-weight-bold text-sm">
                    <i class="fas fa-network-wired text-primary mr-1"></i> Status Database Warga MTA
                </h3>
                <?php if (!empty($pemuda['mta_warga_uuid'])): ?>
                    <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> Terhubung</span>
                <?php else: ?>
                    <span class="badge badge-secondary">Belum Terhubung</span>
                <?php endif; ?>
            </div>
            <div class="card-body p-3 text-sm">
                <?php if (!empty($pemuda['mta_warga_uuid'])): ?>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width: 140px;">Status di MTA</td>
                            <td>: <span class="badge badge-light border font-weight-bold"><?= esc($pemuda['mta_status_warga'] ?? 'Warga') ?></span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">UUID Warga MTA</td>
                            <td>: <code class="text-xs"><?= esc($pemuda['mta_warga_uuid']) ?></code></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Terakhir Sinkron</td>
                            <td class="text-dark">: <?= !empty($pemuda['mta_synced_at']) ? date('d/m/Y H:i:s', strtotime($pemuda['mta_synced_at'])) : '-' ?> WIB</td>
                        </tr>
                    </table>
                <?php else: ?>
                    <p class="text-muted text-xs mb-2">
                        Data pemuda ini belum terhubung dengan UUID di Database Warga MTA. Anda dapat menekan tombol <strong>Sinkronkan MTA</strong> untuk mencari dan menghubungkan data warga secara otomatis.
                    </p>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<!-- MODAL PREVIEW FOTO PROFIL -->
<?php if (!empty($photoUrl)): ?>
<div class="modal fade" id="modalFotoPreview" tabindex="-1" role="dialog" aria-labelledby="modalFotoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header <?= $isMale ? 'bg-primary' : 'bg-danger' ?> text-white py-2">
                <h6 class="modal-title font-weight-bold" id="modalFotoLabel">
                    <i class="fas fa-image mr-1"></i> Foto Profil &bull; <?= esc($pemuda['name']) ?>
                </h6>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-2 text-center bg-dark">
                <img src="<?= $photoUrl ?>" alt="<?= esc($pemuda['name']) ?>" class="img-fluid rounded shadow" style="max-height: 75vh; width: auto; object-fit: contain;">
            </div>
            <div class="modal-footer py-2 justify-content-between bg-light">
                <span class="text-xs text-muted">
                    <i class="fas fa-id-badge mr-1"></i> No. Reg: <strong><?= esc($pemuda['registration_number']) ?></strong>
                </span>
                <div>
                    <a href="<?= $photoUrl ?>" target="_blank" class="btn btn-sm btn-outline-primary shadow-sm" download>
                        <i class="fas fa-download mr-1"></i> Unduh Foto
                    </a>
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
.detail-avatar-container {
    position: relative;
    display: inline-block;
}
.detail-profile-img {
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    cursor: pointer;
}
.detail-profile-img:hover {
    transform: scale(1.04);
    box-shadow: 0 8px 22px rgba(0, 123, 255, 0.28) !important;
}
.detail-avatar-zoom-badge {
    position: absolute;
    bottom: 30px;
    right: 4px;
    width: 32px;
    height: 32px;
    background: #007bff;
    color: #ffffff;
    border: 2px solid #ffffff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.82rem;
    cursor: pointer;
    transition: background 0.2s ease, transform 0.2s ease;
}
.detail-avatar-zoom-badge:hover {
    background: #0056b3;
    transform: scale(1.12);
}
.shadow-2xs {
    box-shadow: 0 1px 2px rgba(0,0,0,0.06);
}
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#btnLiveSyncMta').on('click', function() {
        if (!confirm('Apakah Anda ingin menyinkronkan data pemuda ini dengan Database Warga MTA?')) {
            return;
        }

        const btn = $(this);
        const icon = $('#iconSyncMta');
        btn.prop('disabled', true);
        icon.addClass('fa-spin');

        $.ajax({
            url: '<?= base_url('admin/mta-sync/sync-pemuda/' . $pemuda['id']) ?>',
            type: 'POST',
            dataType: 'json',
            success: function(res) {
                btn.prop('disabled', false);
                icon.removeClass('fa-spin');
                if (res.success) {
                    alert('Sukses: ' + res.message);
                    location.reload();
                } else {
                    alert('Informasi Sinkronisasi: ' + res.message);
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false);
                icon.removeClass('fa-spin');
                alert('Gagal melakukan sinkronisasi: ' + (xhr.responseJSON ? xhr.responseJSON.message : xhr.statusText));
            }
        });
    });
});
</script>
<?= $this->endSection() ?>

