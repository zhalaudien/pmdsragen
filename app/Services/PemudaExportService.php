<?php

namespace App\Services;

use App\Models\PemudaModel;
use App\Models\CabangModel;
use App\Models\WilayahModel;
use App\Models\SkillModel;
use App\Models\InterestModel;
use App\Models\EducationLevelModel;
use App\Models\JobStatusModel;
use Config\Database;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PemudaExportService
{
    protected PemudaModel $pemudaModel;

    public const CATEGORIZED_COLUMNS = [
        'personal' => [
            'category_name' => 'Data Pribadi',
            'category_icon' => 'fas fa-user',
            'columns' => [
                'registration_number' => 'No. Registrasi',
                'name'                => 'Nama Lengkap',
                'gender'              => 'Jenis Kelamin',
                'marital_status'      => 'Status Pernikahan',
                'blood_type'          => 'Golongan Darah',
                'birth_place'         => 'Tempat Lahir',
                'birth_date'          => 'Tanggal Lahir',
                'age'                 => 'Usia (Tahun)',
                'phone'               => 'No. WhatsApp / HP',
                'email'               => 'Email',
            ],
        ],
        'wilayah_cabang' => [
            'category_name' => 'Wilayah & Cabang',
            'category_icon' => 'fas fa-sitemap',
            'columns' => [
                'wilayah_name' => 'Wilayah',
                'cabang_name'  => 'Cabang Pemuda MTA',
            ],
        ],
        'alamat' => [
            'category_name' => 'Alamat & Domisili',
            'category_icon' => 'fas fa-map-marker-alt',
            'columns' => [
                'address_detail' => 'Alamat Detail',
                'dusun'          => 'Dusun / Dukuh',
                'rt'             => 'RT',
                'rw'             => 'RW',
                'village_name'   => 'Desa / Kelurahan',
                'district_name'  => 'Kecamatan',
            ],
        ],
        'pendidikan' => [
            'category_name' => 'Pendidikan',
            'category_icon' => 'fas fa-graduation-cap',
            'columns' => [
                'education_level_name' => 'Jenjang Pendidikan',
                'school_name'          => 'Nama Sekolah / Kampus',
                'major'                => 'Jurusan',
                'education_status'     => 'Status Pendidikan',
                'graduation_year'      => 'Tahun Lulus',
            ],
        ],
        'pekerjaan' => [
            'category_name' => 'Pekerjaan & Wirausaha',
            'category_icon' => 'fas fa-briefcase',
            'columns' => [
                'job_status_name'  => 'Status Pekerjaan',
                'job_title'        => 'Profesi / Jabatan',
                'company_name'     => 'Nama Perusahaan / Instansi',
                'business_field'   => 'Bidang Usaha',
                'business_name'    => 'Nama Usaha (Wirausaha)',
                'business_address' => 'Alamat Tempat Usaha',
                'business_contact' => 'Kontak Person Usaha',
                'business_social'  => 'Media Sosial Usaha',
            ],
        ],
        'organisasi_potensi' => [
            'category_name' => 'Organisasi, Bakat & Minat',
            'category_icon' => 'fas fa-star',
            'columns' => [
                'organizations' => 'Organisasi Yang Diikuti',
                'skills'        => 'Bakat / Keahlian',
                'interests'     => 'Minat',
            ],
        ],
        'status_sistem' => [
            'category_name' => 'Status & Sistem',
            'category_icon' => 'fas fa-info-circle',
            'columns' => [
                'status_verifikasi' => 'Status Verifikasi',
                'status_data'       => 'Status Data',
                'created_at'        => 'Tanggal Registrasi',
            ],
        ],
    ];

    public const PRESETS = [
        'default' => [
            'registration_number', 'name', 'gender', 'age', 'birth_date', 
            'phone', 'cabang_name', 'address_detail', 'education_level_name', 
            'job_status_name', 'skills', 'interests', 'status_verifikasi'
        ],
        'all' => [
            'registration_number', 'name', 'gender', 'marital_status', 'blood_type',
            'birth_place', 'birth_date', 'age', 'phone', 'email',
            'wilayah_name', 'cabang_name', 'address_detail', 'dusun', 'rt', 'rw',
            'village_name', 'district_name', 'education_level_name', 'school_name',
            'major', 'education_status', 'graduation_year', 'job_status_name',
            'job_title', 'company_name', 'business_field', 'business_name',
            'business_address', 'business_contact', 'business_social',
            'organizations', 'skills', 'interests', 'status_verifikasi',
            'status_data', 'created_at'
        ],
        'contact' => [
            'registration_number', 'name', 'gender', 'phone', 'email',
            'cabang_name', 'address_detail', 'dusun', 'rt', 'rw',
            'village_name', 'district_name'
        ],
        'potensi' => [
            'registration_number', 'name', 'gender', 'age', 'cabang_name',
            'education_level_name', 'school_name', 'major', 'job_status_name',
            'job_title', 'organizations', 'skills', 'interests'
        ],
        'business' => [
            'registration_number', 'name', 'gender', 'phone', 'cabang_name',
            'job_status_name', 'business_name', 'business_field', 'business_address',
            'business_contact', 'business_social', 'skills'
        ],
    ];

    public function __construct(?PemudaModel $pemudaModel = null)
    {
        $this->pemudaModel = $pemudaModel ?? new PemudaModel();
    }

    /**
     * Ambil daftar seluruh kolom yang tersedia dalam bentuk flat array [key => label]
     */
    public function getAllColumnLabels(): array
    {
        $flat = [];
        foreach (self::CATEGORIZED_COLUMNS as $group) {
            foreach ($group['columns'] as $key => $label) {
                $flat[$key] = $label;
            }
        }
        return $flat;
    }

    /**
     * Hitung jumlah data yang sesuai dengan filter dan scope
     */
    public function countFiltered(array $filters, array $scope): int
    {
        return $this->pemudaModel->getFilteredQuery($filters, $scope)->countAllResults();
    }

    /**
     * Ambil data pemuda lengkap beserta relasi organisasi, bakat, dan minat
     */
    public function buildExportData(array $filters, array $selectedColumns, array $scope): array
    {
        $allLabels = $this->getAllColumnLabels();

        // Validasi kolom terpilih, jika kosong gunakan default preset
        $validColumns = [];
        foreach ($selectedColumns as $col) {
            if (isset($allLabels[$col])) {
                $validColumns[] = $col;
            }
        }

        if (empty($validColumns)) {
            $validColumns = self::PRESETS['default'];
        }

        // Ambil data dari model
        $query = $this->pemudaModel->getFilteredQuery($filters, $scope)
                                   ->orderBy('pemuda.created_at', 'DESC');
        $rows  = $query->findAll();

        if (empty($rows)) {
            return [
                'headers' => array_map(fn($col) => $allLabels[$col], $validColumns),
                'columns' => $validColumns,
                'rows'    => [],
                'total'   => 0,
            ];
        }

        $pemudaIds = array_column($rows, 'id');

        // 1. Batch load organisasi jika kolom organisasi dipilih
        $orgMap = [];
        if (in_array('organizations', $validColumns, true) && !empty($pemudaIds)) {
            $db = $this->pemudaModel->db;
            $orgRows = $db->table('organisasi')
                          ->select('pemuda_id, organization_name')
                          ->whereIn('pemuda_id', $pemudaIds)
                          ->get()->getResultArray();
            foreach ($orgRows as $o) {
                $orgMap[$o['pemuda_id']][] = $o['organization_name'];
            }
        }

        // 2. Batch load bakat / keahlian jika kolom skills dipilih
        $skillsMap = [];
        if (in_array('skills', $validColumns, true) && !empty($pemudaIds)) {
            $db = $this->pemudaModel->db;
            $skillRows = $db->table('pemuda_skills')
                            ->select('pemuda_skills.pemuda_id, skills.name')
                            ->join('skills', 'skills.id = pemuda_skills.skill_id')
                            ->whereIn('pemuda_skills.pemuda_id', $pemudaIds)
                            ->get()->getResultArray();
            foreach ($skillRows as $s) {
                $skillsMap[$s['pemuda_id']][] = $s['name'];
            }
        }

        // 3. Batch load minat jika kolom interests dipilih
        $interestsMap = [];
        if (in_array('interests', $validColumns, true) && !empty($pemudaIds)) {
            $db = $this->pemudaModel->db;
            $interestRows = $db->table('pemuda_interests')
                               ->select('pemuda_interests.pemuda_id, interests.name')
                               ->join('interests', 'interests.id = pemuda_interests.interest_id')
                               ->whereIn('pemuda_interests.pemuda_id', $pemudaIds)
                               ->get()->getResultArray();
            foreach ($interestRows as $i) {
                $interestsMap[$i['pemuda_id']][] = $i['name'];
            }
        }

        // Map label penerjemah
        $maritalLabels = [
            'belum_menikah' => 'Belum Menikah',
            'sudah_menikah' => 'Sudah Menikah',
            'janda'         => 'Janda',
            'duda'          => 'Duda',
        ];

        $verifLabels = [
            'pending'  => 'Belum Terverifikasi',
            'verified' => 'Terverifikasi',
            'rejected' => 'Belum Terverifikasi',
        ];

        // Format data per baris
        $formattedRows = [];
        $no = 1;
        $curYear = (int) date('Y');

        foreach ($rows as $item) {
            $pemudaId = $item['id'];
            $birthDate = $item['birth_date'] ?? null;
            $age = null;
            if (!empty($birthDate)) {
                $bYear = (int) date('Y', strtotime($birthDate));
                if ($bYear > 1920) {
                    $age = $curYear - $bYear;
                }
            }

            $rowData = [];
            foreach ($validColumns as $col) {
                switch ($col) {
                    case 'registration_number':
                        $rowData[$col] = $item['registration_number'] ?? '-';
                        break;
                    case 'name':
                        $rowData[$col] = !empty($item['name']) ? ucwords(trim($item['name'])) : '-';
                        break;
                    case 'gender':
                        $rowData[$col] = ($item['gender'] === 'L') ? 'Laki-laki' : 'Perempuan';
                        break;
                    case 'marital_status':
                        $rowData[$col] = $maritalLabels[$item['marital_status'] ?? ''] ?? ($item['marital_status'] ?: '-');
                        break;
                    case 'blood_type':
                        $rowData[$col] = !empty($item['blood_type']) && $item['blood_type'] !== 'tidak_tahu' ? strtoupper($item['blood_type']) : '-';
                        break;
                    case 'birth_place':
                        $rowData[$col] = !empty($item['birth_place']) ? ucwords(trim($item['birth_place'])) : '-';
                        break;
                    case 'birth_date':
                        $rowData[$col] = !empty($birthDate) ? date('d/m/Y', strtotime($birthDate)) : '-';
                        break;
                    case 'age':
                        $rowData[$col] = $age !== null ? $age : '-';
                        break;
                    case 'phone':
                        $rowData[$col] = !empty($item['phone']) ? $item['phone'] : '-';
                        break;
                    case 'email':
                        $rowData[$col] = !empty($item['email']) ? $item['email'] : '-';
                        break;
                    case 'wilayah_name':
                        $rowData[$col] = $item['wilayah_name'] ?? '-';
                        break;
                    case 'cabang_name':
                        $rowData[$col] = $item['cabang_name'] ?? '-';
                        break;
                    case 'address_detail':
                        $rowData[$col] = $item['address_detail'] ?? '-';
                        break;
                    case 'dusun':
                        $rowData[$col] = $item['dusun'] ?? '-';
                        break;
                    case 'rt':
                        $rowData[$col] = $item['rt'] ?? '-';
                        break;
                    case 'rw':
                        $rowData[$col] = $item['rw'] ?? '-';
                        break;
                    case 'village_name':
                        $rowData[$col] = $item['village_name'] ?? '-';
                        break;
                    case 'district_name':
                        $rowData[$col] = $item['district_name'] ?? '-';
                        break;
                    case 'education_level_name':
                        $rowData[$col] = $item['education_level_name'] ?? '-';
                        break;
                    case 'school_name':
                        $rowData[$col] = $item['school_name'] ?? '-';
                        break;
                    case 'major':
                        $rowData[$col] = $item['major'] ?? '-';
                        break;
                    case 'education_status':
                        $rowData[$col] = !empty($item['education_status']) ? ucwords(str_replace('_', ' ', $item['education_status'])) : '-';
                        break;
                    case 'graduation_year':
                        $rowData[$col] = $item['graduation_year'] ?? '-';
                        break;
                    case 'job_status_name':
                        $rowData[$col] = $item['job_status_name'] ?? '-';
                        break;
                    case 'job_title':
                        $rowData[$col] = $item['job_title'] ?? '-';
                        break;
                    case 'company_name':
                        $rowData[$col] = $item['company_name'] ?? '-';
                        break;
                    case 'business_field':
                        $rowData[$col] = $item['business_field'] ?? '-';
                        break;
                    case 'business_name':
                        $rowData[$col] = $item['business_name'] ?? '-';
                        break;
                    case 'business_address':
                        $rowData[$col] = $item['business_address'] ?? '-';
                        break;
                    case 'business_contact':
                        $rowData[$col] = $item['business_contact'] ?? '-';
                        break;
                    case 'business_social':
                        $rowData[$col] = $item['business_social'] ?? '-';
                        break;
                    case 'organizations':
                        $rowData[$col] = !empty($orgMap[$pemudaId]) ? implode(', ', $orgMap[$pemudaId]) : '-';
                        break;
                    case 'skills':
                        $rowData[$col] = !empty($skillsMap[$pemudaId]) ? implode(', ', $skillsMap[$pemudaId]) : '-';
                        break;
                    case 'interests':
                        $rowData[$col] = !empty($interestsMap[$pemudaId]) ? implode(', ', $interestsMap[$pemudaId]) : '-';
                        break;
                    case 'status_verifikasi':
                        $rowData[$col] = $verifLabels[$item['status_verifikasi'] ?? ''] ?? ($item['status_verifikasi'] ?: '-');
                        break;
                    case 'status_data':
                        $rowData[$col] = ($item['status_data'] === 'active') ? 'Aktif' : 'Arsip';
                        break;
                    case 'created_at':
                        $rowData[$col] = !empty($item['created_at']) ? date('d/m/Y H:i', strtotime($item['created_at'])) : '-';
                        break;
                    default:
                        $rowData[$col] = $item[$col] ?? '-';
                        break;
                }
            }
            $formattedRows[] = $rowData;
        }

        return [
            'headers' => array_map(fn($col) => $allLabels[$col], $validColumns),
            'columns' => $validColumns,
            'rows'    => $formattedRows,
            'total'   => count($formattedRows),
        ];
    }

    /**
     * Generate Spreadsheet Excel (.xlsx) berdasarkan data dan kolom yang dipilih
     */
    public function generateXlsx(array $filters, array $selectedColumns, array $scope, array $metadata = []): Spreadsheet
    {
        $data = $this->buildExportData($filters, $selectedColumns, $scope);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Pemuda');

        // Judul Dokumen
        $sheet->setCellValue('A1', 'DATA PEMUDA MTA PERWAKILAN SRAGEN');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('198754'));

        $subInfo = 'Tanggal Ekspor: ' . date('d F Y, H:i') . ' WIB | Total: ' . number_format($data['total']) . ' Data';
        if (!empty($metadata['user_name'])) {
            $subInfo .= ' | Diekspor oleh: ' . $metadata['user_name'];
        }
        $sheet->setCellValue('A2', $subInfo);
        $sheet->getStyle('A2')->getFont()->setSize(10)->setItalic(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('6c757d'));

        // Baris Header Kolom
        $startRow = 4;
        $sheet->setCellValue('A' . $startRow, 'No');
        
        $colIdx = 2; // Mulai dari Kolom B
        foreach ($data['headers'] as $headerLabel) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
            $sheet->setCellValue($colLetter . $startRow, $headerLabel);
            $colIdx++;
        }
        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx - 1);

        // Styling Baris Header
        $headerRange = 'A' . $startRow . ':' . $lastColLetter . $startRow;
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '198754'], // Hijau Emerald
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => '146C43'],
                ],
            ],
        ]);
        $sheet->getRowDimension($startRow)->setRowHeight(28);

        // Isi Baris Data
        $currentRow = $startRow + 1;
        $no = 1;

        foreach ($data['rows'] as $row) {
            $sheet->setCellValue('A' . $currentRow, $no++);
            
            $cIdx = 2;
            foreach ($data['columns'] as $colKey) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($cIdx);
                $val = $row[$colKey] ?? '-';

                // Format nomor telepon sebagai string teks agar 0 tidak hilang
                if ($colKey === 'phone' || $colKey === 'business_contact') {
                    $sheet->setCellValueExplicit($colLetter . $currentRow, (string) $val, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                } elseif ($colKey === 'age' && is_numeric($val)) {
                    $sheet->setCellValue($colLetter . $currentRow, (int) $val);
                } else {
                    $sheet->setCellValue($colLetter . $currentRow, $val);
                }
                $cIdx++;
            }

            // Alternating row background
            if ($no % 2 === 1) {
                $rowRange = 'A' . $currentRow . ':' . $lastColLetter . $currentRow;
                $sheet->getStyle($rowRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8F9FA');
            }

            $currentRow++;
        }

        // Border untuk seluruh tabel
        $dataEndRow = max($startRow + 1, $currentRow - 1);
        $sheet->getStyle('A' . $startRow . ':' . $lastColLetter . $dataEndRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'DEE2E6'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Auto-fit kolom width
        for ($c = 1; $c < $colIdx; $c++) {
            $l = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
            $sheet->getColumnDimension($l)->setAutoSize(true);
        }

        // Center align kolom No
        $sheet->getStyle('A' . ($startRow + 1) . ':A' . $dataEndRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return $spreadsheet;
    }

    /**
     * Output stream CSV langsung ke browser
     */
    public function streamCsv(array $filters, array $selectedColumns, array $scope, string $filename): void
    {
        $data = $this->buildExportData($filters, $selectedColumns, $scope);

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // UTF-8 BOM untuk Microsoft Excel
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header
        $csvHeaders = array_merge(['No'], $data['headers']);
        fputcsv($output, $csvHeaders);

        // Data Rows
        $no = 1;
        foreach ($data['rows'] as $row) {
            $csvRow = [$no++];
            foreach ($data['columns'] as $colKey) {
                $val = $row[$colKey] ?? '-';
                if ($colKey === 'phone' || $colKey === 'business_contact') {
                    $val = sanitizeCsvField("'" . $val);
                } else {
                    $val = sanitizeCsvField((string) $val);
                }
                $csvRow[] = $val;
            }
            fputcsv($output, $csvRow);
        }

        fclose($output);
        exit;
    }
}
