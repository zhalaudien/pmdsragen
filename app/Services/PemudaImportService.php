<?php

namespace App\Services;

use App\Models\PemudaModel;
use App\Models\AlamatModel;
use App\Models\PendidikanModel;
use App\Models\PekerjaanModel;
use App\Models\OrganisasiModel;
use App\Models\PemudaSkillModel;
use App\Models\PemudaInterestModel;
use App\Models\WilayahModel;
use App\Models\CabangModel;
use App\Models\DistrictModel;
use App\Models\VillageModel;
use App\Models\EducationLevelModel;
use App\Models\JobStatusModel;
use App\Models\SkillModel;
use App\Models\InterestModel;
use Config\Database;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PemudaImportService
{
    protected PemudaModel $pemudaModel;
    protected AlamatModel $alamatModel;
    protected PendidikanModel $pendidikanModel;
    protected PekerjaanModel $pekerjaanModel;
    protected OrganisasiModel $organisasiModel;
    protected PemudaSkillModel $pemudaSkillModel;
    protected PemudaInterestModel $pemudaInterestModel;

    public function __construct()
    {
        $this->pemudaModel         = new PemudaModel();
        $this->alamatModel         = new AlamatModel();
        $this->pendidikanModel     = new PendidikanModel();
        $this->pekerjaanModel      = new PekerjaanModel();
        $this->organisasiModel     = new OrganisasiModel();
        $this->pemudaSkillModel    = new PemudaSkillModel();
        $this->pemudaInterestModel = new PemudaInterestModel();
    }

    /**
     * Generate template Excel (.xlsx) dengan 2 sheet:
     * Sheet 1: Format Import Pemuda (Header dan contoh data)
     * Sheet 2: Referensi & Petunjuk (Daftar Cabang, Wilayah, Kecamatan, Desa, Pendidikan, Pekerjaan, Keahlian, Minat)
     */
    public function generateTemplate(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();

        // -------------------------------------------------------------
        // SHEET 1: Format Import Pemuda
        // -------------------------------------------------------------
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Format Import Pemuda');

        // Header Columns
        $headers = [
            'A1' => 'Nama Lengkap *',
            'B1' => 'Jenis Kelamin * (L/P)',
            'C1' => 'Cabang * (Nama/Kode)',
            'D1' => 'Tempat Lahir *',
            'E1' => 'Tanggal Lahir * (YYYY-MM-DD)',
            'F1' => 'No. Telepon / WA *',
            'G1' => 'Email',
            'H1' => 'Status Nikah (belum_menikah/sudah_menikah/janda/duda)',
            'I1' => 'Golongan Darah (A/B/AB/O)',
            'J1' => 'Kecamatan *',
            'K1' => 'Desa / Kelurahan *',
            'L1' => 'Dusun / Dukuh',
            'M1' => 'RT',
            'N1' => 'RW',
            'O1' => 'Alamat Detail *',
            'P1' => 'Jenjang Pendidikan * (SMA/S1/dsb)',
            'Q1' => 'Nama Sekolah / Kampus *',
            'R1' => 'Jurusan',
            'S1' => 'Status Pendidikan * (lulus/sedang_sekolah/putus_sekolah)',
            'T1' => 'Tahun Lulus',
            'U1' => 'Status Pekerjaan *',
            'V1' => 'Profesi / Jabatan',
            'W1' => 'Nama Perusahaan / Tempat Usaha',
            'X1' => 'Bidang Usaha',
            'Y1' => 'Organisasi',
            'Z1' => 'Keahlian (Pisahkan Koma)',
            'AA1' => 'Minat (Pisahkan Koma)',
            'AB1' => 'Status Verifikasi (verified/pending)',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        // Header Styling
        $headerRange = 'A1:AB1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0D6EFD'], // Bootstrap Primary
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'B0C4DE'],
                ],
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(36);

        // Contoh Data Baris 2 & 3
        $sampleData = [
            [
                'Muhammad Yusuf',
                'L',
                'Sragen 1',
                'Sragen',
                '2001-05-14',
                '081234567890',
                'yusuf@example.com',
                'belum_menikah',
                'O',
                'Sragen',
                'Sragen Tengah',
                'Magersari',
                '02',
                '04',
                'Jl. Raya Sukowati No. 45',
                'Sarjana (S1 / D4)',
                'Universitas Sebelas Maret',
                'Informatika',
                'lulus',
                '2023',
                'Karyawan Swasta',
                'Web Developer',
                'PT Solusi Digital',
                'Teknologi Informasi',
                'Karang Taruna',
                'Pemrograman & IT (Web/Mobile), Desain Grafis & Multimedia',
                'Teknologi & Robotika, Kewirausahaan & UMKM',
                'verified',
            ],
            [
                'Aisyah Nur Rahma',
                'P',
                'Gemolong 1',
                'Sragen',
                '2003-11-20',
                '085712345678',
                'aisyah@example.com',
                'belum_menikah',
                'A',
                'Gemolong',
                'Gemolong',
                'Kauman',
                '01',
                '02',
                'Jl. Gemolong-Sragen KM 2',
                'SMA / SMK / MA',
                'SMA Negeri 1 Gemolong',
                'MIPA',
                'lulus',
                '2021',
                'Pelajar / Mahasiswa',
                'Mahasiswi',
                'Universitas Muhammadiyah Surakarta',
                'Pendidikan',
                'IPPNU',
                'Public Speaking & Komunikasi, Administrasi & Pembukuan',
                'Kajian & Keagamaan, Literasi & Buku',
                'verified',
            ],
        ];

        // Isi Contoh Data Baris 2 & 3
        $sheet->fromArray($sampleData, null, 'A2');

        // Style Sample Rows
        $sampleRange = 'A2:AB3';
        $sheet->getStyle($sampleRange)->applyFromArray([
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'E0E0E0'],
                ],
            ],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(22);
        $sheet->getRowDimension(3)->setRowHeight(22);

        // Auto-fit column widths
        foreach (range('A', 'Z') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->getColumnDimension('AA')->setAutoSize(true);
        $sheet->getColumnDimension('AB')->setAutoSize(true);

        // -------------------------------------------------------------
        // SHEET 2: Referensi & Petunjuk
        // -------------------------------------------------------------
        $refSheet = $spreadsheet->createSheet();
        $refSheet->setTitle('Referensi & Petunjuk');

        // Title
        $refSheet->setCellValue('A1', 'PANDUAN & DAFTAR REFERENSI IMPORT DATA PEMUDA');
        $refSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // Section 1: Aturan Pengisian
        $refSheet->setCellValue('A3', '1. ATURAN PENGISIAN FORMAT:');
        $refSheet->getStyle('A3')->getFont()->setBold(true);

        $rules = [
            ['A4', 'Kolom dengan tanda bintang (*) wajib diisi. Kolom lainnya bersifat opsional.'],
            ['A5', 'Jenis Kelamin: Isi dengan "L" (Laki-laki) atau "P" (Perempuan).'],
            ['A6', 'Cabang: Dapat diisi dengan Nama Cabang (contoh: "Sragen 1", "Gemolong 1") atau Kode Cabang (contoh: "CBG-001").'],
            ['A7', 'Tanggal Lahir: Format YYYY-MM-DD (contoh: 2002-08-17) atau DD/MM/YYYY (contoh: 17/08/2002).'],
            ['A8', 'No. Telepon / WA: Nomor kontak aktif (contoh: 081234567890 atau 6281234567890).'],
            ['A9', 'Status Nikah: "belum_menikah", "sudah_menikah", "janda", atau "duda".'],
            ['A10', 'Golongan Darah: "A", "B", "AB", "O", atau "tidak_tahu".'],
            ['A11', 'Status Pendidikan: "lulus", "sedang_sekolah", atau "putus_sekolah".'],
            ['A12', 'Keahlian & Minat: Jika lebih dari satu, pisahkan dengan tanda koma (,).'],
            ['A13', 'Status Verifikasi: "verified" (langsung diverifikasi) atau "pending" (menunggu verifikasi). Default: "verified".'],
        ];

        foreach ($rules as $r) {
            $refSheet->setCellValue($r[0], $r[1]);
        }

        // Section 2: Daftar Wilayah & Cabang
        $refSheet->setCellValue('A15', '2. DAFTAR WILAYAH & CABANG');
        $refSheet->getStyle('A15')->getFont()->setBold(true);

        $refSheet->setCellValue('A16', 'Wilayah');
        $refSheet->setCellValue('B16', 'Kode Cabang');
        $refSheet->setCellValue('C16', 'Nama Cabang');

        $refSheet->getStyle('A16:C16')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '198754']],
        ]);

        $cabangModel = new CabangModel();
        $allCabang   = [];
        try {
            $allCabang = $cabangModel->select('cabang.*, wilayah.name as wilayah_name')
                                     ->join('wilayah', 'wilayah.id = cabang.wilayah_id')
                                     ->orderBy('cabang.wilayah_id', 'ASC')
                                     ->orderBy('cabang.name', 'ASC')
                                     ->findAll();
        } catch (\Throwable $e) {
            // Fallback reference data
            $allCabang = [
                ['wilayah_name' => 'Wilayah 1', 'code' => 'CBG-001', 'name' => 'Gesi'],
                ['wilayah_name' => 'Wilayah 1', 'code' => 'CBG-002', 'name' => 'Jenar'],
                ['wilayah_name' => 'Wilayah 1', 'code' => 'CBG-003', 'name' => 'Tangen 1'],
                ['wilayah_name' => 'Wilayah 1', 'code' => 'CBG-004', 'name' => 'Tanon 1'],
                ['wilayah_name' => 'Wilayah 2', 'code' => 'CBG-019', 'name' => 'Gemolong 1'],
                ['wilayah_name' => 'Wilayah 2', 'code' => 'CBG-020', 'name' => 'Kalijambe 1'],
                ['wilayah_name' => 'Wilayah 3', 'code' => 'CBG-038', 'name' => 'Sragen 1'],
                ['wilayah_name' => 'Wilayah 3', 'code' => 'CBG-039', 'name' => 'Karangmalang 1'],
                ['wilayah_name' => 'Wilayah 4', 'code' => 'CBG-058', 'name' => 'Gondang 1'],
            ];
        }

        $cRow = 17;
        foreach ($allCabang as $c) {
            $refSheet->setCellValue('A' . $cRow, $c['wilayah_name'] ?? '-');
            $refSheet->setCellValue('B' . $cRow, $c['code'] ?? '-');
            $refSheet->setCellValue('C' . $cRow, $c['name'] ?? '-');
            $cRow++;
        }

        // Section 3: Daftar Kecamatan & Desa
        $refSheet->setCellValue('E15', '3. DAFTAR KECAMATAN & DESA (KAB. SRAGEN)');
        $refSheet->getStyle('E15')->getFont()->setBold(true);

        $refSheet->setCellValue('E16', 'Kecamatan');
        $refSheet->setCellValue('F16', 'Desa / Kelurahan');

        $refSheet->getStyle('E16:F16')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0D6EFD']],
        ]);

        $villageModel = new VillageModel();
        $allVillages  = [];
        try {
            $allVillages = $villageModel->select('villages.name as village_name, districts.name as district_name')
                                        ->join('districts', 'districts.id = villages.district_id')
                                        ->orderBy('districts.name', 'ASC')
                                        ->orderBy('villages.name', 'ASC')
                                        ->findAll();
        } catch (\Throwable $e) {
            $allVillages = [
                ['district_name' => 'Sragen', 'village_name' => 'Sragen Wetan'],
                ['district_name' => 'Sragen', 'village_name' => 'Sragen Kulon'],
                ['district_name' => 'Sragen', 'village_name' => 'Sragen Tengah'],
                ['district_name' => 'Gemolong', 'village_name' => 'Gemolong'],
                ['district_name' => 'Gemolong', 'village_name' => 'Kwangen'],
                ['district_name' => 'Karangmalang', 'village_name' => 'Puro'],
                ['district_name' => 'Karangmalang', 'village_name' => 'Plumbungan'],
            ];
        }

        $vRow = 17;
        foreach ($allVillages as $v) {
            $refSheet->setCellValue('E' . $vRow, $v['district_name'] ?? '-');
            $refSheet->setCellValue('F' . $vRow, $v['village_name'] ?? '-');
            $vRow++;
        }

        // Section 4: Jenjang Pendidikan & Status Pekerjaan
        $refSheet->setCellValue('H15', '4. REFERENSI LAINNYA');
        $refSheet->getStyle('H15')->getFont()->setBold(true);

        $refSheet->setCellValue('H16', 'Jenjang Pendidikan');
        $refSheet->setCellValue('I16', 'Status Pekerjaan');

        $refSheet->getStyle('H16:I16')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '6C757D']],
        ]);

        $eduModel = new EducationLevelModel();
        $jobModel = new JobStatusModel();
        $edus     = [];
        $jobs     = [];
        try {
            $edus = $eduModel->orderBy('id', 'ASC')->findAll();
            $jobs = $jobModel->orderBy('id', 'ASC')->findAll();
        } catch (\Throwable $e) {
            $edus = [
                ['name' => 'SD / Sederajat'],
                ['name' => 'SMP / MTs / Sederajat'],
                ['name' => 'SMA / SMK / MA'],
                ['name' => 'Diploma (D1 / D2 / D3)'],
                ['name' => 'Sarjana (S1 / D4)'],
                ['name' => 'Magister (S2)'],
                ['name' => 'Doktor (S3)'],
            ];
            $jobs = [
                ['name' => 'Belum / Tidak Bekerja'],
                ['name' => 'Pelajar / Mahasiswa'],
                ['name' => 'Karyawan Swasta'],
                ['name' => 'Pegawai Negeri / ASN / PPPK'],
                ['name' => 'Wirausaha / Pemilik Usaha'],
                ['name' => 'Freelancer / Pekerja Lepas'],
                ['name' => 'Petani / Peternak'],
                ['name' => 'Lainnya'],
            ];
        }

        $maxRows = max(count($edus), count($jobs));
        for ($i = 0; $i < $maxRows; $i++) {
            $r = 17 + $i;
            if (isset($edus[$i])) {
                $refSheet->setCellValue('H' . $r, $edus[$i]['name']);
            }
            if (isset($jobs[$i])) {
                $refSheet->setCellValue('I' . $r, $jobs[$i]['name']);
            }
        }

        // Section 5: Daftar Keahlian & Minat
        $skillStartRow = 17 + $maxRows + 2;
        $refSheet->setCellValue('H' . ($skillStartRow - 1), 'Daftar Keahlian Standar');
        $refSheet->setCellValue('I' . ($skillStartRow - 1), 'Daftar Minat Standar');

        $refSheet->getStyle('H' . ($skillStartRow - 1) . ':I' . ($skillStartRow - 1))->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FD7E14']],
        ]);

        $skillModel = new SkillModel();
        $interestModel = new InterestModel();
        $skills = [];
        $interests = [];
        try {
            $skills    = $skillModel->orderBy('id', 'ASC')->findAll();
            $interests = $interestModel->orderBy('id', 'ASC')->findAll();
        } catch (\Throwable $e) {
            $skills = [
                ['name' => 'Desain Grafis & Multimedia'],
                ['name' => 'Pemrograman & IT (Web/Mobile)'],
                ['name' => 'Digital Marketing & Social Media'],
                ['name' => 'Public Speaking & Komunikasi'],
                ['name' => 'Fotografi & Videografi'],
                ['name' => 'Pertanian Modern & Hidroponik'],
                ['name' => 'Tata Boga & Kuliner'],
                ['name' => 'Menjahit & Tata Busana'],
                ['name' => 'Teknik Otomotif & Mesin'],
                ['name' => 'Administrasi & Pembukuan'],
                ['name' => 'Bahasa Asing (Inggris/Lainnya)'],
                ['name' => 'Kepemimpinan & Manajemen Tim'],
            ];
            $interests = [
                ['name' => 'Olahraga & Kebugaran'],
                ['name' => 'Seni Musik & Tari'],
                ['name' => 'Seni Rupa & Kriya'],
                ['name' => 'Teknologi & Robotika'],
                ['name' => 'Kewirausahaan & UMKM'],
                ['name' => 'Aksi Relawan & Sosial'],
                ['name' => 'Kelestarian Lingkungan'],
                ['name' => 'Literasi & Buku'],
                ['name' => 'Kajian & Keagamaan'],
                ['name' => 'Pariwisata & Budaya Lokal'],
                ['name' => 'E-Sport & Gaming'],
                ['name' => 'Kepemimpinan & Organisasi'],
            ];
        }

        $maxSkillInterest = max(count($skills), count($interests));
        for ($j = 0; $j < $maxSkillInterest; $j++) {
            $currR = $skillStartRow + $j;
            if (isset($skills[$j])) {
                $refSheet->setCellValue('H' . $currR, $skills[$j]['name']);
            }
            if (isset($interests[$j])) {
                $refSheet->setCellValue('I' . $currR, $interests[$j]['name']);
            }
        }

        foreach (range('A', 'I') as $col) {
            $refSheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set active sheet back to Sheet 1
        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    /**
     * Memproses file Excel / CSV yang diunggah
     *
     * @param string $filePath Path file di filesystem
     * @param int|null $userId ID user superadmin yang melakukan import
     * @param array $options Opsi tambahan (default_verifikasi, skip_errors, dll)
     * @return array Hasil import [success => bool, total_rows => int, imported => int, skipped => int, errors => array, rows => array]
     */
    public function importFile(string $filePath, ?int $userId = null, array $options = []): array
    {
        // 1. Load file dengan PhpSpreadsheet
        try {
            $spreadsheet = IOFactory::load($filePath);
        } catch (\Throwable $e) {
            return [
                'success'    => false,
                'message'    => 'Gagal membaca file Excel/CSV: ' . $e->getMessage(),
                'total_rows' => 0,
                'imported'   => 0,
                'skipped'    => 0,
                'errors'     => ['File tidak dapat dibaca sebagai format spreadsheet yang valid.'],
            ];
        }

        // Pilih sheet pertama atau sheet "Format Import Pemuda" / "Data Pemuda"
        $sheet = $spreadsheet->getSheet(0);
        $sheetNames = $spreadsheet->getSheetNames();
        foreach ($sheetNames as $name) {
            if (stripos($name, 'format') !== false || stripos($name, 'pemuda') !== false || stripos($name, 'data') !== false) {
                $sheet = $spreadsheet->getSheetByName($name);
                break;
            }
        }

        $highestRow    = $sheet->getHighestDataRow();
        $highestColumn = $sheet->getHighestDataColumn();

        if ($highestRow < 2) {
            return [
                'success'    => false,
                'message'    => 'File tidak memiliki baris data untuk diimport.',
                'total_rows' => 0,
                'imported'   => 0,
                'skipped'    => 0,
                'errors'     => ['File kosong atau hanya berisi baris header.'],
            ];
        }

        // 2. Baca baris header (Row 1) & buat pemetaan indeks kolom
        $headerRow = $sheet->rangeToArray('A1:' . $highestColumn . '1', null, true, true, false)[0] ?? [];
        $columnMap = $this->mapHeaderColumns($headerRow);

        // 3. Preload referensi database ke memory cache untuk performa maksimal
        $lookups = $this->preloadLookups();

        // 4. Parse dan validasi setiap baris
        $parsedRows  = [];
        $rowErrors   = [];
        $totalDataRows = 0;

        $defaultVerif = $options['default_verifikasi'] ?? 'verified';
        if (!in_array($defaultVerif, ['verified', 'pending', 'rejected'], true)) {
            $defaultVerif = 'verified';
        }
        $skipErrors = !empty($options['skip_errors']);

        for ($row = 2; $row <= $highestRow; $row++) {
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, true, false)[0] ?? [];

            // Cek apakah seluruh baris kosong
            if ($this->isRowEmpty($rowData)) {
                continue;
            }

            $totalDataRows++;
            $parsed = $this->parseAndValidateRow($rowData, $columnMap, $row, $lookups, $defaultVerif);

            if (!empty($parsed['errors'])) {
                foreach ($parsed['errors'] as $err) {
                    $rowErrors[] = "Baris {$row}: {$err}";
                }
            } else {
                $parsedRows[] = [
                    'row_num' => $row,
                    'data'    => $parsed['data'],
                ];
            }
        }

        if ($totalDataRows === 0) {
            return [
                'success'    => false,
                'message'    => 'Tidak ada data valid yang ditemukan pada file spreadsheet.',
                'total_rows' => 0,
                'imported'   => 0,
                'skipped'    => 0,
                'errors'     => ['Seluruh baris kosong.'],
            ];
        }

        // Jika terdapat error dan opsi skip_errors tidak diaktifkan, gagalkan proses
        if (!empty($rowErrors) && !$skipErrors) {
            return [
                'success'    => false,
                'message'    => 'Ditemukan ' . count($rowErrors) . ' kesalahan validasi pada data. Silakan perbaiki file Excel Anda atau aktifkan opsi "Lewati Baris Error".',
                'total_rows' => $totalDataRows,
                'imported'   => 0,
                'skipped'    => count($rowErrors),
                'errors'     => $rowErrors,
            ];
        }

        if (empty($parsedRows)) {
            return [
                'success'    => false,
                'message'    => 'Tidak ada baris yang valid untuk disimpan ke database.',
                'total_rows' => $totalDataRows,
                'imported'   => 0,
                'skipped'    => count($rowErrors),
                'errors'     => $rowErrors,
            ];
        }

        // 5. Simpan data dalam Database Transaction
        $db = Database::connect();
        $db->transStart();

        $importedCount = 0;
        $insertedIds   = [];

        try {
            foreach ($parsedRows as $item) {
                $d = $item['data'];

                // Generate No Registrasi Unik
                $regNumber = $this->pemudaModel->generateRegistrationNumber();

                // 5.1 Insert Pemuda
                $pemudaData = [
                    'cabang_id'           => $d['cabang_id'],
                    'registration_number' => $regNumber,
                    'name'                => $d['name'],
                    'gender'              => $d['gender'],
                    'marital_status'      => $d['marital_status'],
                    'blood_type'          => $d['blood_type'],
                    'birth_place'         => $d['birth_place'],
                    'birth_date'          => $d['birth_date'],
                    'phone'               => $d['phone'],
                    'email'               => $d['email'],
                    'status_verifikasi'   => $d['status_verifikasi'],
                    'status_data'         => 'active',
                    'created_by'          => $userId,
                ];

                $this->pemudaModel->insert($pemudaData);
                $pemudaId = $this->pemudaModel->getInsertID();
                $insertedIds[] = $pemudaId;

                // 5.2 Insert Alamat
                $this->alamatModel->insert([
                    'pemuda_id'      => $pemudaId,
                    'province_id'    => 33, // Jawa Tengah
                    'regency_id'     => 3314, // Kabupaten Sragen
                    'district_id'    => $d['district_id'],
                    'village_id'     => $d['village_id'],
                    'dusun'          => $d['dusun'],
                    'rt'             => $d['rt'],
                    'rw'             => $d['rw'],
                    'address_detail' => $d['address_detail'],
                ]);

                // 5.3 Insert Pendidikan
                $this->pendidikanModel->insert([
                    'pemuda_id'          => $pemudaId,
                    'education_level_id' => $d['education_level_id'],
                    'school_name'        => $d['school_name'],
                    'major'              => $d['major'],
                    'education_status'   => $d['education_status'],
                    'graduation_year'    => $d['graduation_year'],
                ]);

                // 5.4 Insert Pekerjaan
                $this->pekerjaanModel->insert([
                    'pemuda_id'      => $pemudaId,
                    'job_status_id'  => $d['job_status_id'],
                    'job_title'      => $d['job_title'],
                    'company_name'   => $d['company_name'],
                    'business_field' => $d['business_field'],
                ]);

                // 5.5 Insert Organisasi
                if (!empty($d['organisasi_list'])) {
                    foreach ($d['organisasi_list'] as $orgName) {
                        if (!empty($orgName)) {
                            $this->organisasiModel->insert([
                                'pemuda_id'         => $pemudaId,
                                'organization_name' => $orgName,
                                'position'          => 'Anggota',
                            ]);
                        }
                    }
                }

                // 5.6 Insert Skills
                if (!empty($d['skill_ids'])) {
                    foreach ($d['skill_ids'] as $skillId) {
                        $this->pemudaSkillModel->insert([
                            'pemuda_id' => $pemudaId,
                            'skill_id'  => $skillId,
                            'level'     => 'pemula',
                        ]);
                    }
                }

                // 5.7 Insert Interests
                if (!empty($d['interest_ids'])) {
                    foreach ($d['interest_ids'] as $interestId) {
                        $this->pemudaInterestModel->insert([
                            'pemuda_id'   => $pemudaId,
                            'interest_id' => $interestId,
                        ]);
                    }
                }

                $importedCount++;
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return [
                    'success'    => false,
                    'message'    => 'Terjadi kesalahan transaksi database saat mengimport data.',
                    'total_rows' => $totalDataRows,
                    'imported'   => 0,
                    'skipped'    => $totalDataRows,
                    'errors'     => ['Gagal melakukan commit database.'],
                ];
            }

            return [
                'success'      => true,
                'message'      => "Berhasil mengimport {$importedCount} data pemuda ke dalam sistem.",
                'total_rows'   => $totalDataRows,
                'imported'     => $importedCount,
                'skipped'      => count($rowErrors),
                'errors'       => $rowErrors,
                'imported_ids' => $insertedIds,
            ];

        } catch (\Throwable $e) {
            $db->transRollback();
            return [
                'success'    => false,
                'message'    => 'Gagal menyimpan data ke database: ' . $e->getMessage(),
                'total_rows' => $totalDataRows,
                'imported'   => 0,
                'skipped'    => $totalDataRows,
                'errors'     => [$e->getMessage()],
            ];
        }
    }

    /**
     * Map header text ke canonical keys
     */
    protected function mapHeaderColumns(array $headerRow): array
    {
        $map = [];
        foreach ($headerRow as $index => $rawHeader) {
            if ($rawHeader === null) {
                continue;
            }
            $clean = strtolower(trim((string) $rawHeader));
            // Bersihkan tanda bintang dan kurung petunjuk
            $clean = preg_replace('/[\*\(\)\/\-\_]/', ' ', $clean);
            $clean = preg_replace('/\s+/', ' ', $clean);
            $clean = trim($clean);

            // Specific compound checks first
            if (str_contains($clean, 'status pendidikan') || str_contains($clean, 'education status')) {
                $map['education_status'] = $index;
            } elseif (str_contains($clean, 'status pekerjaan') || str_contains($clean, 'job status')) {
                $map['job_status'] = $index;
            } elseif (str_contains($clean, 'status nikah') || str_contains($clean, 'status pernikahan') || str_contains($clean, 'marital status')) {
                $map['marital_status'] = $index;
            } elseif (str_contains($clean, 'status verifikasi') || str_contains($clean, 'verifikasi') || str_contains($clean, 'verification')) {
                $map['status_verifikasi'] = $index;
            } elseif (str_contains($clean, 'tahun lulus') || str_contains($clean, 'graduation year') || str_contains($clean, 'lulus tahun')) {
                $map['graduation_year'] = $index;
            } elseif (str_contains($clean, 'nama sekolah') || str_contains($clean, 'nama kampus') || str_contains($clean, 'universitas') || str_contains($clean, 'school')) {
                $map['school_name'] = $index;
            } elseif (str_contains($clean, 'nama lengkap') || $clean === 'nama' || $clean === 'name') {
                $map['name'] = $index;
            } elseif (str_contains($clean, 'jenis kelamin') || str_contains($clean, 'gender') || $clean === 'jk') {
                $map['gender'] = $index;
            } elseif (str_contains($clean, 'cabang') || $clean === 'cbg') {
                $map['cabang'] = $index;
            } elseif (str_contains($clean, 'tempat lahir') || $clean === 'tmp lahir' || $clean === 'birth place') {
                $map['birth_place'] = $index;
            } elseif (str_contains($clean, 'tanggal lahir') || str_contains($clean, 'tgl lahir') || $clean === 'birth date') {
                $map['birth_date'] = $index;
            } elseif (str_contains($clean, 'telepon') || str_contains($clean, 'no hp') || str_contains($clean, 'nomor hp') || str_contains($clean, 'wa') || str_contains($clean, 'phone')) {
                $map['phone'] = $index;
            } elseif (str_contains($clean, 'email') || str_contains($clean, 'surel')) {
                $map['email'] = $index;
            } elseif (str_contains($clean, 'nikah') || str_contains($clean, 'pernikahan') || str_contains($clean, 'marital')) {
                $map['marital_status'] = $index;
            } elseif (str_contains($clean, 'darah') || str_contains($clean, 'blood')) {
                $map['blood_type'] = $index;
            } elseif (str_contains($clean, 'kecamatan') || str_contains($clean, 'district')) {
                $map['district'] = $index;
            } elseif (str_contains($clean, 'desa') || str_contains($clean, 'kelurahan') || str_contains($clean, 'village')) {
                $map['village'] = $index;
            } elseif (str_contains($clean, 'dusun') || str_contains($clean, 'dukuh')) {
                $map['dusun'] = $index;
            } elseif ($clean === 'rt' || str_contains($clean, 'no rt')) {
                $map['rt'] = $index;
            } elseif ($clean === 'rw' || str_contains($clean, 'no rw')) {
                $map['rw'] = $index;
            } elseif (str_contains($clean, 'alamat') || str_contains($clean, 'address')) {
                $map['address_detail'] = $index;
            } elseif (str_contains($clean, 'jenjang') || str_contains($clean, 'pendidikan') || str_contains($clean, 'education level')) {
                $map['education_level'] = $index;
            } elseif (str_contains($clean, 'sekolah') || str_contains($clean, 'kampus')) {
                $map['school_name'] = $index;
            } elseif (str_contains($clean, 'jurusan') || str_contains($clean, 'major') || str_contains($clean, 'prodi')) {
                $map['major'] = $index;
            } elseif (str_contains($clean, 'pekerjaan')) {
                $map['job_status'] = $index;
            } elseif (str_contains($clean, 'profesi') || str_contains($clean, 'jabatan') || str_contains($clean, 'job title')) {
                $map['job_title'] = $index;
            } elseif (str_contains($clean, 'perusahaan') || str_contains($clean, 'kantor') || str_contains($clean, 'tempat usaha') || str_contains($clean, 'company')) {
                $map['company_name'] = $index;
            } elseif (str_contains($clean, 'bidang usaha') || str_contains($clean, 'business field')) {
                $map['business_field'] = $index;
            } elseif (str_contains($clean, 'organisasi') || str_contains($clean, 'organization')) {
                $map['organisasi'] = $index;
            } elseif (str_contains($clean, 'keahlian') || str_contains($clean, 'skill')) {
                $map['skills'] = $index;
            } elseif (str_contains($clean, 'minat') || str_contains($clean, 'interest')) {
                $map['interests'] = $index;
            }
        }

        return $map;
    }

    /**
     * Preload data master ke memory untuk fast matching
     */
    protected function preloadLookups(): array
    {
        $cabangMap            = [];
        $districtMap          = [];
        $villageMap           = [];
        $villageByDistrictMap = [];
        $eduMap               = [];
        $jobMap               = [];
        $skillMap             = [];
        $interestMap          = [];

        // 1. Cabang
        try {
            $cabangModel = new CabangModel();
            $allCabang   = $cabangModel->findAll();
            foreach ($allCabang as $c) {
                $cabangMap[(int) $c['id']] = $c;
                $cabangMap[strtolower(trim($c['name']))] = $c;
                if (!empty($c['code'])) {
                    $cabangMap[strtolower(trim($c['code']))] = $c;
                }
            }
        } catch (\Throwable $e) {
            // Standard fallback
            $defaultCabang = [
                ['id' => 1, 'code' => 'CBG-001', 'name' => 'Gesi'],
                ['id' => 2, 'code' => 'CBG-002', 'name' => 'Jenar'],
                ['id' => 19, 'code' => 'CBG-019', 'name' => 'Gemolong 1'],
                ['id' => 38, 'code' => 'CBG-038', 'name' => 'Sragen 1'],
                ['id' => 39, 'code' => 'CBG-039', 'name' => 'Sragen 2'],
            ];
            foreach ($defaultCabang as $c) {
                $cabangMap[(int) $c['id']] = $c;
                $cabangMap[strtolower(trim($c['name']))] = $c;
                $cabangMap[strtolower(trim($c['code']))] = $c;
            }
        }

        // 2. Districts
        try {
            $districtModel = new DistrictModel();
            $allDistricts  = $districtModel->where('regency_id', 3314)->findAll();
            foreach ($allDistricts as $d) {
                $districtMap[(int) $d['id']] = $d;
                $districtMap[strtolower(trim($d['name']))] = $d;
            }
        } catch (\Throwable $e) {
            $defaultDistricts = [
                ['id' => 1, 'name' => 'Sragen'],
                ['id' => 2, 'name' => 'Karangmalang'],
                ['id' => 3, 'name' => 'Sidoharjo'],
                ['id' => 4, 'name' => 'Gemolong'],
                ['id' => 5, 'name' => 'Kalijambe'],
                ['id' => 6, 'name' => 'Plupuh'],
                ['id' => 7, 'name' => 'Masaran'],
            ];
            foreach ($defaultDistricts as $d) {
                $districtMap[(int) $d['id']] = $d;
                $districtMap[strtolower(trim($d['name']))] = $d;
            }
        }

        // 3. Villages
        try {
            $villageModel = new VillageModel();
            $allVillages  = $villageModel->findAll();
            foreach ($allVillages as $v) {
                $vName = strtolower(trim($v['name']));
                $villageMap[(int) $v['id']] = $v;
                $villageMap[$vName] = $v;
                $villageByDistrictMap[$v['district_id'] . '_' . $vName] = $v;
            }
        } catch (\Throwable $e) {
            $defaultVillages = [
                ['id' => 1, 'district_id' => 1, 'name' => 'Sragen Wetan'],
                ['id' => 2, 'district_id' => 1, 'name' => 'Sragen Kulon'],
                ['id' => 3, 'district_id' => 1, 'name' => 'Sragen Tengah'],
                ['id' => 4, 'district_id' => 4, 'name' => 'Gemolong'],
                ['id' => 5, 'district_id' => 4, 'name' => 'Kwangen'],
            ];
            foreach ($defaultVillages as $v) {
                $vName = strtolower(trim($v['name']));
                $villageMap[(int) $v['id']] = $v;
                $villageMap[$vName] = $v;
                $villageByDistrictMap[$v['district_id'] . '_' . $vName] = $v;
            }
        }

        // 4. Education Levels
        try {
            $eduModel = new EducationLevelModel();
            $allEdu   = $eduModel->findAll();
            foreach ($allEdu as $e) {
                $eduMap[(int) $e['id']] = $e;
                $eduMap[strtolower(trim($e['name']))] = $e;
            }
        } catch (\Throwable $e) {
            $defaultEdu = [
                ['id' => 1, 'name' => 'SD / Sederajat'],
                ['id' => 2, 'name' => 'SMP / MTs / Sederajat'],
                ['id' => 3, 'name' => 'SMA / SMK / MA'],
                ['id' => 4, 'name' => 'Diploma (D1 / D2 / D3)'],
                ['id' => 5, 'name' => 'Sarjana (S1 / D4)'],
                ['id' => 6, 'name' => 'Magister (S2)'],
                ['id' => 7, 'name' => 'Doktor (S3)'],
            ];
            foreach ($defaultEdu as $e) {
                $eduMap[(int) $e['id']] = $e;
                $eduMap[strtolower(trim($e['name']))] = $e;
            }
        }

        // 5. Job Statuses
        try {
            $jobModel = new JobStatusModel();
            $allJob   = $jobModel->findAll();
            foreach ($allJob as $j) {
                $jobMap[(int) $j['id']] = $j;
                $jobMap[strtolower(trim($j['name']))] = $j;
            }
        } catch (\Throwable $e) {
            $defaultJobs = [
                ['id' => 1, 'name' => 'Belum / Tidak Bekerja'],
                ['id' => 2, 'name' => 'Pelajar / Mahasiswa'],
                ['id' => 3, 'name' => 'Karyawan Swasta'],
                ['id' => 4, 'name' => 'Pegawai Negeri / ASN / PPPK'],
                ['id' => 5, 'name' => 'Wirausaha / Pemilik Usaha'],
                ['id' => 6, 'name' => 'Freelancer / Pekerja Lepas'],
                ['id' => 7, 'name' => 'Petani / Peternak'],
                ['id' => 8, 'name' => 'Lainnya'],
            ];
            foreach ($defaultJobs as $j) {
                $jobMap[(int) $j['id']] = $j;
                $jobMap[strtolower(trim($j['name']))] = $j;
            }
        }

        // 6. Skills
        try {
            $skillModel = new SkillModel();
            $allSkills  = $skillModel->findAll();
            foreach ($allSkills as $s) {
                $skillMap[(int) $s['id']] = $s;
                $skillMap[strtolower(trim($s['name']))] = $s;
            }
        } catch (\Throwable $e) {
            $defaultSkills = [
                ['id' => 1, 'name' => 'Desain Grafis & Multimedia'],
                ['id' => 2, 'name' => 'Pemrograman & IT (Web/Mobile)'],
                ['id' => 3, 'name' => 'Digital Marketing & Social Media'],
                ['id' => 4, 'name' => 'Public Speaking & Komunikasi'],
                ['id' => 5, 'name' => 'Fotografi & Videografi'],
                ['id' => 6, 'name' => 'Pertanian Modern & Hidroponik'],
                ['id' => 7, 'name' => 'Tata Boga & Kuliner'],
                ['id' => 8, 'name' => 'Menjahit & Tata Busana'],
                ['id' => 9, 'name' => 'Teknik Otomotif & Mesin'],
                ['id' => 10, 'name' => 'Administrasi & Pembukuan'],
                ['id' => 11, 'name' => 'Bahasa Asing (Inggris/Lainnya)'],
                ['id' => 12, 'name' => 'Kepemimpinan & Manajemen Tim'],
            ];
            foreach ($defaultSkills as $s) {
                $skillMap[(int) $s['id']] = $s;
                $skillMap[strtolower(trim($s['name']))] = $s;
            }
        }

        // 7. Interests
        try {
            $interestModel = new InterestModel();
            $allInterests  = $interestModel->findAll();
            foreach ($allInterests as $i) {
                $interestMap[(int) $i['id']] = $i;
                $interestMap[strtolower(trim($i['name']))] = $i;
            }
        } catch (\Throwable $e) {
            $defaultInterests = [
                ['id' => 1, 'name' => 'Olahraga & Kebugaran'],
                ['id' => 2, 'name' => 'Seni Musik & Tari'],
                ['id' => 3, 'name' => 'Seni Rupa & Kriya'],
                ['id' => 4, 'name' => 'Teknologi & Robotika'],
                ['id' => 5, 'name' => 'Kewirausahaan & UMKM'],
                ['id' => 6, 'name' => 'Aksi Relawan & Sosial'],
                ['id' => 7, 'name' => 'Kelestarian Lingkungan'],
                ['id' => 8, 'name' => 'Literasi & Buku'],
                ['id' => 9, 'name' => 'Kajian & Keagamaan'],
                ['id' => 10, 'name' => 'Pariwisata & Budaya Lokal'],
                ['id' => 11, 'name' => 'E-Sport & Gaming'],
                ['id' => 12, 'name' => 'Kepemimpinan & Organisasi'],
            ];
            foreach ($defaultInterests as $i) {
                $interestMap[(int) $i['id']] = $i;
                $interestMap[strtolower(trim($i['name']))] = $i;
            }
        }

        return [
            'cabang'              => $cabangMap,
            'districts'           => $districtMap,
            'villages'            => $villageMap,
            'villages_by_district'=> $villageByDistrictMap,
            'education_levels'    => $eduMap,
            'job_statuses'        => $jobMap,
            'skills'              => $skillMap,
            'interests'           => $interestMap,
        ];
    }

    /**
     * Parse and validate individual row
     */
    protected function parseAndValidateRow(array $row, array $map, int $rowNum, array $lookups, string $defaultVerif): array
    {
        $errors = [];
        $data   = [];

        // 1. Nama Lengkap (Wajib)
        $rawName = isset($map['name']) ? trim((string) ($row[$map['name']] ?? '')) : '';
        if (empty($rawName) || mb_strlen($rawName) < 3) {
            $errors[] = 'Kolom "Nama Lengkap" wajib diisi (minimal 3 karakter).';
        }
        $data['name'] = $rawName;

        // 2. Jenis Kelamin (Wajib, L/P)
        $rawGender = isset($map['gender']) ? strtoupper(trim((string) ($row[$map['gender']] ?? ''))) : '';
        if ($rawGender === 'L' || str_starts_with($rawGender, 'LAKI') || str_starts_with($rawGender, 'PRIA') || $rawGender === 'M' || $rawGender === 'MALE') {
            $gender = 'L';
        } elseif ($rawGender === 'P' || str_starts_with($rawGender, 'PEREMPUAN') || str_starts_with($rawGender, 'WANITA') || $rawGender === 'F' || $rawGender === 'FEMALE') {
            $gender = 'P';
        } else {
            $errors[] = 'Kolom "Jenis Kelamin" wajib diisi dengan L (Laki-laki) atau P (Perempuan).';
            $gender = 'L';
        }
        $data['gender'] = $gender;

        // 3. Cabang (Wajib)
        $rawCabang = isset($map['cabang']) ? trim((string) ($row[$map['cabang']] ?? '')) : '';
        $matchedCabang = null;
        if (!empty($rawCabang)) {
            $cleanCabang = strtolower($rawCabang);
            if (isset($lookups['cabang'][$cleanCabang])) {
                $matchedCabang = $lookups['cabang'][$cleanCabang];
            } elseif (is_numeric($rawCabang) && isset($lookups['cabang'][(int) $rawCabang])) {
                $matchedCabang = $lookups['cabang'][(int) $rawCabang];
            } else {
                // Fuzzy search
                foreach ($lookups['cabang'] as $key => $val) {
                    if (is_string($key) && (str_contains($key, $cleanCabang) || str_contains($cleanCabang, $key))) {
                        $matchedCabang = $val;
                        break;
                    }
                }
            }
        }

        if (!$matchedCabang) {
            $errors[] = 'Kolom "Cabang" (' . ($rawCabang ?: 'kosong') . ') tidak valid atau tidak ditemukan dalam sistem.';
            $data['cabang_id'] = null;
        } else {
            $data['cabang_id'] = (int) $matchedCabang['id'];
        }

        // 4. Tempat Lahir
        $rawBirthPlace = isset($map['birth_place']) ? trim((string) ($row[$map['birth_place']] ?? '')) : '';
        $data['birth_place'] = $rawBirthPlace ?: 'Sragen';

        // 5. Tanggal Lahir (Wajib)
        $rawBirthDate = isset($map['birth_date']) ? $row[$map['birth_date']] ?? null : null;
        $birthDate = $this->parseDateValue($rawBirthDate);
        if (!$birthDate) {
            $errors[] = 'Kolom "Tanggal Lahir" (' . ($rawBirthDate ?? 'kosong') . ') tidak valid. Gunakan format YYYY-MM-DD atau DD/MM/YYYY.';
        }
        $data['birth_date'] = $birthDate;

        // 6. No. Telepon / WA (Wajib)
        $rawPhone = isset($map['phone']) ? trim((string) ($row[$map['phone']] ?? '')) : '';
        $rawPhone = ltrim($rawPhone, "'`\" ");
        $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);
        if (empty($cleanPhone) || strlen($cleanPhone) < 8) {
            $errors[] = 'Kolom "No. Telepon / WA" wajib diisi dengan nomor telepon yang valid.';
        }
        if (str_starts_with($cleanPhone, '62')) {
            $cleanPhone = '0' . substr($cleanPhone, 2);
        }
        $data['phone'] = $cleanPhone ?: $rawPhone;

        // 7. Email (Opsional)
        $rawEmail = isset($map['email']) ? trim((string) ($row[$map['email']] ?? '')) : '';
        if (!empty($rawEmail) && !filter_var($rawEmail, FILTER_VALIDATE_EMAIL)) {
            $rawEmail = null;
        }
        $data['email'] = $rawEmail ?: null;

        // 8. Status Pernikahan
        $rawMarital = isset($map['marital_status']) ? strtolower(trim((string) ($row[$map['marital_status']] ?? ''))) : '';
        if (str_contains($rawMarital, 'sudah') || str_contains($rawMarital, 'menikah') && !str_contains($rawMarital, 'belum')) {
            $marital = 'sudah_menikah';
        } elseif (str_contains($rawMarital, 'janda')) {
            $marital = 'janda';
        } elseif (str_contains($rawMarital, 'duda')) {
            $marital = 'duda';
        } else {
            $marital = 'belum_menikah';
        }
        $data['marital_status'] = $marital;

        // 9. Golongan Darah
        $rawBlood = isset($map['blood_type']) ? strtoupper(trim((string) ($row[$map['blood_type']] ?? ''))) : '';
        if (in_array($rawBlood, ['A', 'B', 'AB', 'O'], true)) {
            $blood = $rawBlood;
        } else {
            $blood = 'tidak_tahu';
        }
        $data['blood_type'] = $blood;

        // 10. Kecamatan (Wajib / Match)
        $rawDistrict = isset($map['district']) ? trim((string) ($row[$map['district']] ?? '')) : '';
        $matchedDistrict = null;
        if (!empty($rawDistrict)) {
            $cleanDist = strtolower($rawDistrict);
            if (isset($lookups['districts'][$cleanDist])) {
                $matchedDistrict = $lookups['districts'][$cleanDist];
            } elseif (is_numeric($rawDistrict) && isset($lookups['districts'][(int) $rawDistrict])) {
                $matchedDistrict = $lookups['districts'][(int) $rawDistrict];
            } else {
                foreach ($lookups['districts'] as $k => $v) {
                    if (is_string($k) && (str_contains($k, $cleanDist) || str_contains($cleanDist, $k))) {
                        $matchedDistrict = $v;
                        break;
                    }
                }
            }
        }
        // Fallback to Sragen (ID 1) if not provided
        if (!$matchedDistrict) {
            $matchedDistrict = $lookups['districts'][1] ?? ['id' => 1, 'name' => 'Sragen'];
        }
        $data['district_id'] = (int) $matchedDistrict['id'];

        // 11. Desa / Kelurahan (Wajib / Match)
        $rawVillage = isset($map['village']) ? trim((string) ($row[$map['village']] ?? '')) : '';
        $matchedVillage = null;
        if (!empty($rawVillage)) {
            $cleanVill = strtolower($rawVillage);
            $distVillKey = $data['district_id'] . '_' . $cleanVill;
            if (isset($lookups['villages_by_district'][$distVillKey])) {
                $matchedVillage = $lookups['villages_by_district'][$distVillKey];
            } elseif (isset($lookups['villages'][$cleanVill])) {
                $matchedVillage = $lookups['villages'][$cleanVill];
            } elseif (is_numeric($rawVillage) && isset($lookups['villages'][(int) $rawVillage])) {
                $matchedVillage = $lookups['villages'][(int) $rawVillage];
            } else {
                foreach ($lookups['villages'] as $k => $v) {
                    if (is_string($k) && (str_contains($k, $cleanVill) || str_contains($cleanVill, $k))) {
                        $matchedVillage = $v;
                        break;
                    }
                }
            }
        }
        // Fallback if not matched: find first village in district from lookups
        if (!$matchedVillage) {
            foreach ($lookups['villages_by_district'] as $k => $v) {
                if (str_starts_with((string) $k, $data['district_id'] . '_')) {
                    $matchedVillage = $v;
                    break;
                }
            }
            if (!$matchedVillage) {
                $matchedVillage = $lookups['villages'][1] ?? ['id' => 1, 'name' => 'Sragen Wetan'];
            }
        }
        $data['village_id'] = (int) $matchedVillage['id'];

        // 12. Dusun, RT, RW, Alamat Detail
        $data['dusun'] = isset($map['dusun']) ? (trim((string) ($row[$map['dusun']] ?? '')) ?: null) : null;
        $data['rt']    = isset($map['rt']) ? (substr(trim((string) ($row[$map['rt']] ?? '')), 0, 5) ?: null) : null;
        $data['rw']    = isset($map['rw']) ? (substr(trim((string) ($row[$map['rw']] ?? '')), 0, 5) ?: null) : null;
        
        $rawAddress = isset($map['address_detail']) ? trim((string) ($row[$map['address_detail']] ?? '')) : '';
        if (empty($rawAddress)) {
            $rawAddress = 'Dusun ' . ($data['dusun'] ?: '-') . ' RT ' . ($data['rt'] ?: '01') . '/RW ' . ($data['rw'] ?: '01');
        }
        $data['address_detail'] = $rawAddress;

        // 13. Jenjang Pendidikan
        $rawEdu = isset($map['education_level']) ? trim((string) ($row[$map['education_level']] ?? '')) : '';
        $matchedEdu = null;
        if (!empty($rawEdu)) {
            $cleanEdu = strtolower($rawEdu);
            if (isset($lookups['education_levels'][$cleanEdu])) {
                $matchedEdu = $lookups['education_levels'][$cleanEdu];
            } elseif (is_numeric($rawEdu) && isset($lookups['education_levels'][(int) $rawEdu])) {
                $matchedEdu = $lookups['education_levels'][(int) $rawEdu];
            } else {
                // Short aliases matching
                if (str_contains($cleanEdu, 's3') || str_contains($cleanEdu, 'doktor')) {
                    $matchedEdu = $lookups['education_levels'][7] ?? null;
                } elseif (str_contains($cleanEdu, 's2') || str_contains($cleanEdu, 'magister')) {
                    $matchedEdu = $lookups['education_levels'][6] ?? null;
                } elseif (str_contains($cleanEdu, 's1') || str_contains($cleanEdu, 'sarjana') || str_contains($cleanEdu, 'd4')) {
                    $matchedEdu = $lookups['education_levels'][5] ?? null;
                } elseif (str_contains($cleanEdu, 'diploma') || str_contains($cleanEdu, 'd3') || str_contains($cleanEdu, 'd2') || str_contains($cleanEdu, 'd1')) {
                    $matchedEdu = $lookups['education_levels'][4] ?? null;
                } elseif (str_contains($cleanEdu, 'sma') || str_contains($cleanEdu, 'smk') || str_contains($cleanEdu, 'ma') || str_contains($cleanEdu, 'slta')) {
                    $matchedEdu = $lookups['education_levels'][3] ?? null;
                } elseif (str_contains($cleanEdu, 'smp') || str_contains($cleanEdu, 'mts') || str_contains($cleanEdu, 'sltp')) {
                    $matchedEdu = $lookups['education_levels'][2] ?? null;
                } elseif (str_contains($cleanEdu, 'sd') || str_contains($cleanEdu, 'mi')) {
                    $matchedEdu = $lookups['education_levels'][1] ?? null;
                }
            }
        }
        if (!$matchedEdu) {
            $matchedEdu = $lookups['education_levels'][3] ?? ['id' => 3, 'name' => 'SMA / SMK / MA']; // Default SMA
        }
        $data['education_level_id'] = (int) $matchedEdu['id'];

        // 14. Nama Sekolah / Kampus
        $rawSchool = isset($map['school_name']) ? trim((string) ($row[$map['school_name']] ?? '')) : '';
        $data['school_name'] = $rawSchool ?: ($matchedEdu['name'] . ' Sragen');

        // 15. Jurusan
        $data['major'] = isset($map['major']) ? (trim((string) ($row[$map['major']] ?? '')) ?: null) : null;

        // 16. Status Pendidikan
        $rawEduStatus = isset($map['education_status']) ? strtolower(trim((string) ($row[$map['education_status']] ?? ''))) : '';
        if (str_contains($rawEduStatus, 'sedang') || str_contains($rawEduStatus, 'belum') || str_contains($rawEduStatus, 'menempuh')) {
            $eduStatus = 'sedang_sekolah';
        } elseif (str_contains($rawEduStatus, 'putus') || str_contains($rawEduStatus, 'berhenti') || str_contains($rawEduStatus, 'drop')) {
            $eduStatus = 'putus_sekolah';
        } else {
            $eduStatus = 'lulus';
        }
        $data['education_status'] = $eduStatus;

        // 17. Tahun Lulus
        $rawGradYear = isset($map['graduation_year']) ? trim((string) ($row[$map['graduation_year']] ?? '')) : '';
        $cleanGradYear = preg_replace('/[^0-9]/', '', $rawGradYear);
        if (!empty($cleanGradYear) && strlen($cleanGradYear) === 4) {
            $data['graduation_year'] = (int) $cleanGradYear;
        } else {
            $data['graduation_year'] = null;
        }

        // 18. Status Pekerjaan
        $rawJob = isset($map['job_status']) ? trim((string) ($row[$map['job_status']] ?? '')) : '';
        $matchedJob = null;
        if (!empty($rawJob)) {
            $cleanJob = strtolower($rawJob);
            if (isset($lookups['job_statuses'][$cleanJob])) {
                $matchedJob = $lookups['job_statuses'][$cleanJob];
            } elseif (is_numeric($rawJob) && isset($lookups['job_statuses'][(int) $rawJob])) {
                $matchedJob = $lookups['job_statuses'][(int) $rawJob];
            } else {
                if (str_contains($cleanJob, 'pelajar') || str_contains($cleanJob, 'mahasiswa') || str_contains($cleanJob, 'kuliah')) {
                    $matchedJob = $lookups['job_statuses'][2] ?? null;
                } elseif (str_contains($cleanJob, 'swasta') || str_contains($cleanJob, 'buruh') || str_contains($cleanJob, 'pabrik') || str_contains($cleanJob, 'karyawan')) {
                    $matchedJob = $lookups['job_statuses'][3] ?? null;
                } elseif (str_contains($cleanJob, 'pns') || str_contains($cleanJob, 'asn') || str_contains($cleanJob, 'pppk') || str_contains($cleanJob, 'negeri')) {
                    $matchedJob = $lookups['job_statuses'][4] ?? null;
                } elseif (str_contains($cleanJob, 'wirausaha') || str_contains($cleanJob, 'usaha') || str_contains($cleanJob, 'bisnis') || str_contains($cleanJob, 'dagang')) {
                    $matchedJob = $lookups['job_statuses'][5] ?? null;
                } elseif (str_contains($cleanJob, 'freelance') || str_contains($cleanJob, 'lepas')) {
                    $matchedJob = $lookups['job_statuses'][6] ?? null;
                } elseif (str_contains($cleanJob, 'tani') || str_contains($cleanJob, 'ternak') || str_contains($cleanJob, 'kebun')) {
                    $matchedJob = $lookups['job_statuses'][7] ?? null;
                } elseif (str_contains($cleanJob, 'belum') || str_contains($cleanJob, 'tidak')) {
                    $matchedJob = $lookups['job_statuses'][1] ?? null;
                }
            }
        }
        if (!$matchedJob) {
            $matchedJob = $lookups['job_statuses'][1] ?? ['id' => 1, 'name' => 'Belum / Tidak Bekerja'];
        }
        $data['job_status_id'] = (int) $matchedJob['id'];

        // 19. Profesi, Perusahaan, Bidang Usaha
        $data['job_title']      = isset($map['job_title']) ? (trim((string) ($row[$map['job_title']] ?? '')) ?: null) : null;
        $data['company_name']   = isset($map['company_name']) ? (trim((string) ($row[$map['company_name']] ?? '')) ?: null) : null;
        $data['business_field'] = isset($map['business_field']) ? (trim((string) ($row[$map['business_field']] ?? '')) ?: null) : null;

        // 20. Organisasi
        $rawOrg = isset($map['organisasi']) ? trim((string) ($row[$map['organisasi']] ?? '')) : '';
        $orgList = [];
        if (!empty($rawOrg)) {
            $parts = explode(',', $rawOrg);
            foreach ($parts as $p) {
                $p = trim($p);
                if (!empty($p)) {
                    $orgList[] = $p;
                }
            }
        }
        $data['organisasi_list'] = $orgList;

        // 21. Skills (Matching with standard skills)
        $rawSkills = isset($map['skills']) ? trim((string) ($row[$map['skills']] ?? '')) : '';
        $matchedSkillIds = [];
        if (!empty($rawSkills)) {
            $parts = explode(',', $rawSkills);
            foreach ($parts as $p) {
                $pClean = strtolower(trim($p));
                if (empty($pClean)) continue;

                if (isset($lookups['skills'][$pClean])) {
                    $matchedSkillIds[] = (int) $lookups['skills'][$pClean]['id'];
                } else {
                    foreach ($lookups['skills'] as $sKey => $sVal) {
                        if (is_string($sKey) && (str_contains($sKey, $pClean) || str_contains($pClean, $sKey))) {
                            $matchedSkillIds[] = (int) $sVal['id'];
                            break;
                        }
                    }
                }
            }
        }
        $data['skill_ids'] = array_unique($matchedSkillIds);

        // 22. Interests (Matching with standard interests)
        $rawInterests = isset($map['interests']) ? trim((string) ($row[$map['interests']] ?? '')) : '';
        $matchedInterestIds = [];
        if (!empty($rawInterests)) {
            $parts = explode(',', $rawInterests);
            foreach ($parts as $p) {
                $pClean = strtolower(trim($p));
                if (empty($pClean)) continue;

                if (isset($lookups['interests'][$pClean])) {
                    $matchedInterestIds[] = (int) $lookups['interests'][$pClean]['id'];
                } else {
                    foreach ($lookups['interests'] as $iKey => $iVal) {
                        if (is_string($iKey) && (str_contains($iKey, $pClean) || str_contains($pClean, $iKey))) {
                            $matchedInterestIds[] = (int) $iVal['id'];
                            break;
                        }
                    }
                }
            }
        }
        $data['interest_ids'] = array_unique($matchedInterestIds);

        // 23. Status Verifikasi
        $rawVerif = isset($map['status_verifikasi']) ? strtolower(trim((string) ($row[$map['status_verifikasi']] ?? ''))) : '';
        if (in_array($rawVerif, ['verified', 'pending', 'rejected'], true)) {
            $data['status_verifikasi'] = $rawVerif;
        } elseif (str_contains($rawVerif, 'terverifikasi') || str_contains($rawVerif, 'valid') || str_contains($rawVerif, 'ya') || str_contains($rawVerif, 'sukses')) {
            $data['status_verifikasi'] = 'verified';
        } elseif (str_contains($rawVerif, 'tolak')) {
            $data['status_verifikasi'] = 'rejected';
        } elseif (str_contains($rawVerif, 'tunggu') || str_contains($rawVerif, 'pending')) {
            $data['status_verifikasi'] = 'pending';
        } else {
            $data['status_verifikasi'] = $defaultVerif;
        }

        return [
            'errors' => $errors,
            'data'   => $data,
        ];
    }

    /**
     * Konversi berbagai format tanggal excel / string ke YYYY-MM-DD
     */
    protected function parseDateValue($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        // 1. Jika berupa numeric float/int serial date dari Excel
        if (is_numeric($value)) {
            try {
                $dt = ExcelDate::excelToDateTimeObject($value);
                return $dt->format('Y-m-d');
            } catch (\Throwable $e) {
                // fall through
            }
        }

        $str = trim((string) $value);

        // 2. Format YYYY-MM-DD
        if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $str, $m)) {
            return sprintf('%04d-%02d-%02d', (int) $m[1], (int) $m[2], (int) $m[3]);
        }

        // 3. Format DD/MM/YYYY atau DD-MM-YYYY
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $str, $m)) {
            return sprintf('%04d-%02d-%02d', (int) $m[3], (int) $m[2], (int) $m[1]);
        }

        // 4. Coba strtotime
        $timestamp = strtotime($str);
        if ($timestamp !== false && $timestamp > 0) {
            return date('Y-m-d', $timestamp);
        }

        return null;
    }

    /**
     * Check if entire row is empty
     */
    protected function isRowEmpty(array $row): bool
    {
        foreach ($row as $val) {
            if ($val !== null && trim((string) $val) !== '') {
                return false;
            }
        }
        return true;
    }
}
