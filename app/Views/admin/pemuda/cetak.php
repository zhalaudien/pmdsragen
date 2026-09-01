<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Biodata Pemuda') ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Cetak Stylesheet -->
    <link rel="stylesheet" href="<?= base_url('css/cetak.css') ?>">
</head>
<body class="cetak-body">
    <div class="no-print mb-4 d-flex justify-content-between align-items-center bg-light p-3 rounded border">
        <div>
            <strong>Mode Cetak Dokumen</strong> &bull; Gunakan kertas A4 untuk hasil terbaik.
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-primary btn-sm px-3">
                Cetak Sekarang
            </button>
            <button onclick="window.close()" class="btn btn-secondary btn-sm px-3 ms-2">
                Tutup
            </button>
        </div>
    </div>

    <!-- HEADER DOKUMEN -->
    <div class="cetak-header-box">
        <h4 class="fw-bold mb-1">MAJELIS TAFSIR AL-QUR'AN (MTA)</h4>
        <h5 class="fw-bold text-uppercase mb-1">PENGURUS PEMUDA MTA PERWAKILAN SRAGEN</h5>
        <p class="mb-0 cetak-header-subtitle">Sekretariat Perwakilan Kabupaten Sragen &bull; Dokumen Data Induk Pemuda</p>
    </div>

    <div class="row mb-3 align-items-center">
        <div class="col-8">
            <h5 class="fw-bold mb-1">FORMULIR BIODATA PEMUDA</h5>
            <div>Nomor Registrasi: <strong><?= esc($pemuda['registration_number']) ?></strong></div>
            <div class="text-muted" style="font-size: 11px;">Status: <?= ($pemuda['status_verifikasi'] === 'verified') ? 'TERVERIFIKASI (SINKRON PUSAT)' : 'BELUM TERVERIFIKASI' ?> &bull; Tanggal Daftar: <?= date('d/m/Y H:i', strtotime($pemuda['created_at'])) ?></div>
        </div>
        <div class="col-4 text-end">
            <div class="cetak-photo-box">
                Foto 3x4
            </div>
        </div>
    </div>

    <!-- 1. DATA PRIBADI -->
    <div class="cetak-section-title">I. DATA PRIBADI</div>
    <table class="w-100 table-cetak">
        <tr>
            <td style="width: 25%;">Nama Lengkap</td>
            <td style="width: 2%;">:</td>
            <td style="width: 73%;" class="fw-bold"><?= esc($pemuda['name']) ?></td>
        </tr>
        <tr>
            <td>Jenis Kelamin</td>
            <td>:</td>
            <td><?= $pemuda['gender'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
        </tr>
        <tr>
            <td>Status Pernikahan</td>
            <td>:</td>
            <td><?= ucwords(str_replace('_', ' ', $pemuda['marital_status'] ?? 'belum_menikah')) ?></td>
        </tr>
        <tr>
            <td>Golongan Darah</td>
            <td>:</td>
            <td><?= esc($pemuda['blood_type'] ?: '-') ?></td>
        </tr>
        <tr>
            <td>Tempat, Tanggal Lahir</td>
            <td>:</td>
            <td><?= esc($pemuda['birth_place'] ?: '-') ?>, <?= $pemuda['birth_date'] ? date('d F Y', strtotime($pemuda['birth_date'])) : '-' ?></td>
        </tr>
        <tr>
            <td>No. HP / WhatsApp</td>
            <td>:</td>
            <td><?= esc($pemuda['phone'] ?: '-') ?></td>
        </tr>
        <tr>
            <td>Email</td>
            <td>:</td>
            <td><?= esc($pemuda['email'] ?: '-') ?></td>
        </tr>
    </table>

    <!-- 2. DATA WILAYAH & DOMISILI -->
    <div class="cetak-section-title">II. DATA WILAYAH & ALAMAT DOMISILI</div>
    <table class="w-100 table-cetak">
        <tr>
            <td style="width: 25%;">Cabang Pemuda</td>
            <td style="width: 2%;">:</td>
            <td style="width: 73%;" class="fw-bold"><?= esc($pemuda['cabang_name']) ?> (<?= esc($pemuda['wilayah_name']) ?>)</td>
        </tr>
        <tr>
            <td>Desa / Kelurahan</td>
            <td>:</td>
            <td><?= esc($pemuda['village_name'] ?: '-') ?></td>
        </tr>
        <tr>
            <td>Kecamatan</td>
            <td>:</td>
            <td><?= esc($pemuda['district_name'] ?: '-') ?></td>
        </tr>
        <tr>
            <td>Kabupaten / Provinsi</td>
            <td>:</td>
            <td><?= esc($pemuda['regency_name'] ?: 'Kabupaten Sragen') ?>, <?= esc($pemuda['province_name'] ?: 'Jawa Tengah') ?></td>
        </tr>
        <tr>
            <td>Dusun, RT / RW</td>
            <td>:</td>
            <td><?= esc($pemuda['dusun'] ?: '-') ?>, RT <?= esc($pemuda['rt'] ?: '-') ?> / RW <?= esc($pemuda['rw'] ?: '-') ?></td>
        </tr>
        <tr>
            <td>Alamat Lengkap</td>
            <td>:</td>
            <td><?= esc($pemuda['address_detail'] ?: '-') ?></td>
        </tr>
    </table>

    <!-- 3. PENDIDIKAN & PEKERJAAN -->
    <div class="cetak-section-title">III. RIWAYAT PENDIDIKAN & PEKERJAAN</div>
    <table class="w-100 table-cetak">
        <tr>
            <td style="width: 25%;">Pendidikan Terakhir</td>
            <td style="width: 2%;">:</td>
            <td style="width: 73%;"><?= esc($pemuda['education_level_name'] ?: '-') ?> &bull; <?= esc($pemuda['school_name'] ?: '-') ?> (Jurusan: <?= esc($pemuda['major'] ?: '-') ?>)</td>
        </tr>
        <tr>
            <td>Status / Tahun Lulus</td>
            <td>:</td>
            <td><?= ucwords(str_replace('_', ' ', $pemuda['education_status'] ?? '-')) ?> (Tahun: <?= esc($pemuda['graduation_year'] ?: '-') ?>)</td>
        </tr>
        <tr>
            <td>Status Pekerjaan</td>
            <td>:</td>
            <td><?= esc($pemuda['job_status_name'] ?: '-') ?></td>
        </tr>
        <tr>
            <td>Profesi / Perusahaan</td>
            <td>:</td>
            <td><?= esc($pemuda['job_title'] ?: '-') ?> (<?= esc($pemuda['company_name'] ?: '-') ?>)</td>
        </tr>
    </table>

    <!-- 4. ORGANISASI & KEAHLIAN -->
    <div class="cetak-section-title">IV. ORGANISASI & KEAHLIAN</div>
    <table class="w-100 table-cetak">
        <tr>
            <td style="width: 25%;">Organisasi yang Diikuti</td>
            <td style="width: 2%;">:</td>
            <td style="width: 73%;">
                <?php if (empty($pemuda['organisasi'])): ?>
                    -
                <?php else: ?>
                    <?php 
                    $orgList = [];
                    foreach ($pemuda['organisasi'] as $o) {
                        $orgList[] = $o['organization_name'] . ' (' . ($o['position'] ?: 'Anggota') . ')';
                    }
                    echo implode(', ', $orgList);
                    ?>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td>Keahlian / Keterampilan</td>
            <td>:</td>
            <td>
                <?php if (empty($pemuda['skills'])): ?>
                    -
                <?php else: ?>
                    <?php 
                    $skList = [];
                    foreach ($pemuda['skills'] as $s) {
                        $skList[] = $s['skill_name'] . ' [' . $s['level'] . ']';
                    }
                    echo implode(', ', $skList);
                    ?>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td>Minat / Hobi</td>
            <td>:</td>
            <td>
                <?php if (empty($pemuda['interests'])): ?>
                    -
                <?php else: ?>
                    <?php 
                    $inList = [];
                    foreach ($pemuda['interests'] as $i) {
                        $inList[] = $i['interest_name'];
                    }
                    echo implode(', ', $inList);
                    ?>
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <!-- TANDA TANGAN -->
    <div class="row signature-box">
        <div class="col-6">
            <div>Mengetahui,</div>
            <div class="fw-bold signature-space">Administrator Cabang / Verifikator</div>
            <div>( .................................................... )</div>
        </div>
        <div class="col-6">
            <div>Sragen, <?= date('d F Y') ?></div>
            <div class="fw-bold signature-space">Pemuda yang Bersangkutan</div>
            <div class="fw-bold">( <?= esc($pemuda['name']) ?> )</div>
        </div>
    </div>
</body>
</html>
