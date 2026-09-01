<?php

namespace App\Models;

use CodeIgniter\Model;

class PemudaModel extends Model
{
    protected $table            = 'pemuda';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'cabang_id',
        'registration_number',
        'name',
        'gender',
        'marital_status',
        'blood_type',
        'birth_place',
        'birth_date',
        'phone',
        'email',
        'status_verifikasi',
        'status_data',
        'mta_warga_uuid',
        'mta_status_warga',
        'mta_ayah_uuid',
        'mta_ibu_uuid',
        'mta_foto_url',
        'mta_synced_at',
        'created_by',
    ];

    // Callbacks
    protected $beforeInsert = ['formatLowercaseFields'];
    protected $beforeUpdate = ['formatLowercaseFields'];

    protected function formatLowercaseFields(array $data): array
    {
        if (isset($data['data']) && is_array($data['data'])) {
            $fieldsToLower = [
                'name',
                'marital_status',
                'blood_type',
                'birth_place',
                'email',
                'status_verifikasi',
                'status_data',
                'mta_warga_uuid',
                'mta_status_warga',
                'mta_ayah_uuid',
                'mta_ibu_uuid',
            ];

            foreach ($fieldsToLower as $f) {
                if (array_key_exists($f, $data['data']) && is_string($data['data'][$f])) {
                    $trimmed = trim($data['data'][$f]);
                    $data['data'][$f] = ($trimmed === '') ? null : mb_strtolower($trimmed, 'UTF-8');
                }
            }
        }

        return $data;
    }

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation Rules
    protected $validationRules = [
        'cabang_id'      => 'required|is_natural_no_zero',
        'name'           => 'required|min_length[3]|max_length[150]',
        'gender'         => 'required|in_list[L,P]',
        'marital_status' => 'permit_empty|in_list[belum_menikah,sudah_menikah,janda,duda]',
        'blood_type'     => 'permit_empty|max_length[10]',
        'phone'          => 'permit_empty|max_length[20]',
        'email'          => 'permit_empty|valid_email|max_length[100]',
    ];

    protected $validationMessages = [
        'cabang_id' => [
            'required' => 'Cabang wajib dipilih.',
        ],
        'name' => [
            'required' => 'Nama pemuda wajib diisi.',
            'min_length' => 'Nama minimal 3 karakter.',
        ],
        'gender' => [
            'required' => 'Jenis kelamin wajib dipilih.',
            'in_list'  => 'Jenis kelamin harus L atau P.',
        ],
    ];

    /**
     * Generate unique registration number
     * Format: IdPerwakilanIdCabangtanggallahirRandomNomor
     * Contoh: 8601200005178234 (Perwakilan: 86, Cabang: 01, Tgl Lahir: 20000517, Random: 4 digit 8234)
     *
     * @param int|null    $cabangId  ID cabang dari tabel cabang
     * @param string|null $birthDate Tanggal lahir (format Y-m-d, d/m/Y, atau string tanggal valid)
     * @return string Nomor registrasi unik 16 digit
     */
    public function generateRegistrationNumber(?int $cabangId = null, ?string $birthDate = null): string
    {
        $perwakilanCode = '86';
        $cabangCode     = '01';

        if (!empty($cabangId)) {
            $cabang = $this->db->table('cabang')->where('id', $cabangId)->get()->getRowArray();
            if ($cabang) {
                if (!empty($cabang['code']) && preg_match('/^(\d+)\.(\d+)$/', trim($cabang['code']), $matches)) {
                    $perwakilanCode = $matches[1];
                    $cabangCode     = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
                } else {
                    $cabangCode = str_pad((string) ($cabang['id'] ?? 1), 2, '0', STR_PAD_LEFT);
                }
            }
        }

        if (!empty($birthDate)) {
            $birthDateStr = trim((string) $birthDate);
            if (preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $birthDateStr, $dm)) {
                $birthCode = $dm[1] . $dm[2] . $dm[3];
            } elseif (preg_match('/^(\d{2})[\/\-](\d{2})[\/\-](\d{4})/', $birthDateStr, $dm)) {
                $birthCode = $dm[3] . $dm[2] . $dm[1];
            } elseif (preg_match('/^\d{8}$/', $birthDateStr)) {
                $birthCode = $birthDateStr;
            } else {
                $ts = strtotime($birthDateStr);
                $birthCode = ($ts !== false) ? date('Ymd', $ts) : date('Ymd');
            }
        } else {
            $birthCode = date('Ymd');
        }

        $baseNumber = $perwakilanCode . $cabangCode . $birthCode;

        // Generate 4 digit random (0001 - 9999) dan pastikan unik di database
        do {
            $randomCode = str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
            $candidate  = $baseNumber . $randomCode;
            $count = $this->db->table($this->table)->where('registration_number', $candidate)->countAllResults();
        } while ($count > 0);

        return $candidate;
    }

    /**
     * Cari pemuda untuk verifikasi/pengecekan data berdasarkan nama, jenis kelamin, tanggal lahir, dan cabang.
     *
     * @param string      $name        Nama pemuda
     * @param string|null $gender      Jenis kelamin ('L' atau 'P', opsional)
     * @param string      $birthDate   Tanggal lahir (string tanggal Y-m-d atau format tanggal valid)
     * @param int         $cabangId    ID cabang
     * @param int|null    $excludeId   ID pemuda yang dikecualikan (misal saat edit)
     *
     * @return array|null Mengembalikan data pemuda atau null jika belum ada
     */
    public function findExistingPemuda(string $name, ?string $gender, string $birthDate, int $cabangId, ?int $excludeId = null): ?array
    {
        $cleanName      = trim($name);
        $cleanGender    = $gender ? trim(strtoupper($gender)) : null;
        $cleanBirthDate = trim($birthDate);

        if ($cleanName === '' || $cleanBirthDate === '' || $cabangId <= 0) {
            return null;
        }

        $timestamp = strtotime($cleanBirthDate);
        $formattedDate = ($timestamp !== false && $timestamp > 0) ? date('Y-m-d', $timestamp) : $cleanBirthDate;

        $builder = $this->builder();
        $builder->where('cabang_id', $cabangId)
                ->where('birth_date', $formattedDate)
                ->where('LOWER(TRIM(name))', strtolower($cleanName));

        if (!empty($cleanGender) && in_array($cleanGender, ['L', 'P'], true)) {
            $builder->where('gender', $cleanGender);
        }

        if ($excludeId !== null && $excludeId > 0) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->get()->getRowArray();
    }

    /**
     * Cari duplikat data pemuda berdasarkan nama, tanggal lahir, dan cabang.
     *
     * @param string      $name        Nama pemuda
     * @param string      $birthDate   Tanggal lahir (string tanggal Y-m-d atau format tanggal valid)
     * @param int         $cabangId    ID cabang
     * @param int|null    $excludeId   ID pemuda yang dikecualikan (misal saat edit)
     * @param string|null $gender      Jenis kelamin ('L' / 'P', opsional)
     *
     * @return array|null Mengembalikan data pemuda yang duplikat atau null jika belum ada
     */
    public function findDuplicate(string $name, string $birthDate, int $cabangId, ?int $excludeId = null, ?string $gender = null): ?array
    {
        return $this->findExistingPemuda($name, $gender, $birthDate, $cabangId, $excludeId);
    }

    /**
     * Cari pemuda berdasarkan MTA Warga UUID
     */
    public function findByMtaWargaUuid(string $uuid): ?array
    {
        $cleanUuid = trim($uuid);
        if ($cleanUuid === '') {
            return null;
        }
        return $this->where('mta_warga_uuid', $cleanUuid)->first();
    }

    /**
     * Apply Scope based on User Role (Superadmin, Admin Wilayah, Admin Cabang)
     */
    public function applyScope($builder, array $scope = [])
    {
        if (isset($scope['role'])) {
            if ($scope['role'] === 'admin_wilayah' && !empty($scope['wilayah_id'])) {
                $builder->where('cabang.wilayah_id', (int) $scope['wilayah_id']);
            } elseif ($scope['role'] === 'admin_cabang' && !empty($scope['cabang_id'])) {
                $builder->where('pemuda.cabang_id', (int) $scope['cabang_id']);
            }
        }
        return $builder;
    }

    /**
     * Base query for filtered youth list
     */
    public function getFilteredQuery(array $filters = [], array $scope = [])
    {
        $builder = $this->select('pemuda.*, 
                                  cabang.name as cabang_name, 
                                  cabang.code as cabang_code,
                                  cabang.wilayah_id,
                                  wilayah.name as wilayah_name, 
                                  wilayah.code as wilayah_code,
                                  alamat.address_detail,
                                  alamat.dusun,
                                  alamat.rt,
                                  alamat.rw,
                                  districts.name as district_name,
                                  villages.name as village_name,
                                  pendidikan.school_name,
                                  pendidikan.major,
                                  pendidikan.education_status,
                                  education_levels.name as education_level_name,
                                  pekerjaan.job_title,
                                  pekerjaan.company_name,
                                  job_statuses.name as job_status_name')
                        ->join('cabang', 'cabang.id = pemuda.cabang_id', 'left')
                        ->join('wilayah', 'wilayah.id = cabang.wilayah_id', 'left')
                        ->join('alamat', 'alamat.pemuda_id = pemuda.id', 'left')
                        ->join('districts', 'districts.id = alamat.district_id', 'left')
                        ->join('villages', 'villages.id = alamat.village_id', 'left')
                        ->join('pendidikan', 'pendidikan.pemuda_id = pemuda.id', 'left')
                        ->join('education_levels', 'education_levels.id = pendidikan.education_level_id', 'left')
                        ->join('pekerjaan', 'pekerjaan.pemuda_id = pemuda.id', 'left')
                        ->join('job_statuses', 'job_statuses.id = pekerjaan.job_status_id', 'left');

        // 1. Enforce Role Scope first
        $this->applyScope($builder, $scope);

        // 2. Apply filters (respecting user scope)
        if (!empty($filters['search'])) {
            $s = trim($filters['search']);
            $builder->groupStart()
                    ->like('pemuda.name', $s)
                    ->orLike('pemuda.registration_number', $s)
                    ->orLike('pemuda.phone', $s)
                    ->orLike('pemuda.email', $s)
                    ->orLike('pendidikan.school_name', $s)
                    ->orLike('pekerjaan.company_name', $s)
                    ->orLike('alamat.address_detail', $s)
                    ->groupEnd();
        }

        // Only superadmin can filter across different wilayah
        $role = $scope['role'] ?? 'superadmin';
        if ($role === 'superadmin' && !empty($filters['wilayah_id'])) {
            $builder->where('cabang.wilayah_id', (int) $filters['wilayah_id']);
        }

        // Admin cabang is strictly bound to their own cabang_id; superadmin/admin_wilayah can filter by cabang
        if ($role !== 'admin_cabang' && !empty($filters['cabang_id'])) {
            $builder->where('pemuda.cabang_id', (int) $filters['cabang_id']);
        }

        if (!empty($filters['gender'])) {
            $builder->where('pemuda.gender', $filters['gender']);
        }

        if (!empty($filters['marital_status'])) {
            $builder->where('pemuda.marital_status', $filters['marital_status']);
        }

        if (!empty($filters['blood_type'])) {
            $builder->where('pemuda.blood_type', $filters['blood_type']);
        }

        if (!empty($filters['status_verifikasi'])) {
            $builder->where('pemuda.status_verifikasi', $filters['status_verifikasi']);
        }

        if (!empty($filters['status_data'])) {
            $builder->where('pemuda.status_data', $filters['status_data']);
        }

        if (!empty($filters['education_level_id'])) {
            $builder->where('pendidikan.education_level_id', (int) $filters['education_level_id']);
        }

        if (!empty($filters['job_status_id'])) {
            $builder->where('pekerjaan.job_status_id', (int) $filters['job_status_id']);
        }

        if (!empty($filters['start_date'])) {
            $builder->where('pemuda.created_at >=', $filters['start_date'] . ' 00:00:00');
        }

        if (!empty($filters['end_date'])) {
            $builder->where('pemuda.created_at <=', $filters['end_date'] . ' 23:59:59');
        }

        return $builder;
    }

    /**
     * Ambil data pemuda lengkap dengan detail relasi untuk satu ID
     */
    public function getPemudaDetail(int $id, array $scope = []): ?array
    {
        $builder = $this->select('pemuda.*, 
                                  cabang.name as cabang_name, 
                                  cabang.code as cabang_code,
                                  cabang.wilayah_id,
                                  wilayah.name as wilayah_name, 
                                  wilayah.code as wilayah_code,
                                  alamat.province_id,
                                  alamat.regency_id,
                                  alamat.district_id,
                                  alamat.village_id,
                                  alamat.dusun,
                                  alamat.rt,
                                  alamat.rw,
                                  alamat.address_detail,
                                  provinces.name as province_name,
                                  regencies.name as regency_name,
                                  districts.name as district_name,
                                  villages.name as village_name,
                                  pendidikan.education_level_id,
                                  pendidikan.school_name,
                                  pendidikan.major,
                                  pendidikan.education_status,
                                  pendidikan.graduation_year,
                                  education_levels.name as education_level_name,
                                  pekerjaan.job_status_id,
                                  pekerjaan.job_title,
                                  pekerjaan.company_name,
                                  pekerjaan.business_field,
                                  job_statuses.name as job_status_name,
                                  creator.name as creator_name')
                        ->join('cabang', 'cabang.id = pemuda.cabang_id', 'left')
                        ->join('wilayah', 'wilayah.id = cabang.wilayah_id', 'left')
                        ->join('alamat', 'alamat.pemuda_id = pemuda.id', 'left')
                        ->join('provinces', 'provinces.id = alamat.province_id', 'left')
                        ->join('regencies', 'regencies.id = alamat.regency_id', 'left')
                        ->join('districts', 'districts.id = alamat.district_id', 'left')
                        ->join('villages', 'villages.id = alamat.village_id', 'left')
                        ->join('pendidikan', 'pendidikan.pemuda_id = pemuda.id', 'left')
                        ->join('education_levels', 'education_levels.id = pendidikan.education_level_id', 'left')
                        ->join('pekerjaan', 'pekerjaan.pemuda_id = pemuda.id', 'left')
                        ->join('job_statuses', 'job_statuses.id = pekerjaan.job_status_id', 'left')
                        ->join('users as creator', 'creator.id = pemuda.created_by', 'left')
                        ->where('pemuda.id', $id);

        $this->applyScope($builder, $scope);
        $pemuda = $builder->first();

        if (!$pemuda) {
            return null;
        }

        // Ambil relasi Organisasi
        $organisasiModel = new OrganisasiModel();
        $pemuda['organisasi'] = $organisasiModel->where('pemuda_id', $id)->orderBy('join_date', 'DESC')->findAll();

        // Ambil relasi Skills
        $pemudaSkillModel = new PemudaSkillModel();
        $pemuda['skills'] = $pemudaSkillModel->select('pemuda_skills.*, skills.name as skill_name, skills.description as skill_description')
                                             ->join('skills', 'skills.id = pemuda_skills.skill_id')
                                             ->where('pemuda_skills.pemuda_id', $id)
                                             ->findAll();

        // Ambil relasi Interests
        $pemudaInterestModel = new PemudaInterestModel();
        $pemuda['interests'] = $pemudaInterestModel->select('pemuda_interests.*, interests.name as interest_name, interests.description as interest_description')
                                                   ->join('interests', 'interests.id = pemuda_interests.interest_id')
                                                   ->where('pemuda_interests.pemuda_id', $id)
                                                   ->findAll();

        return $pemuda;
    }

    /**
     * Hitung total per status
     */
    public function getCountsSummary(array $scope = []): array
    {
        $builder = $this->builder();
        $builder->join('cabang', 'cabang.id = pemuda.cabang_id', 'left');
        $this->applyScope($builder, $scope);

        $totalAll = (clone $builder)->countAllResults(false);
        $verified = (clone $builder)->where('pemuda.status_verifikasi', 'verified')->countAllResults(false);
        $pending  = (clone $builder)->where('pemuda.status_verifikasi', 'pending')->countAllResults(false);
        $active   = (clone $builder)->where('pemuda.status_data', 'active')->countAllResults(false);
        $archived = (clone $builder)->where('pemuda.status_data', 'archived')->countAllResults(false);

        return [
            'total'    => $totalAll,
            'verified' => $verified,
            'pending'  => $pending,
            'rejected' => 0,
            'active'   => $active,
            'archived' => $archived,
        ];
    }

    /**
     * Data statistik komprehensif untuk dashboard Super Admin
     */
    public function getDashboardStats(array $scope = []): array
    {
        $db = $this->db;

        // 1. Summary Counts
        $summary = $this->getCountsSummary($scope);

        $role      = $scope['role'] ?? 'superadmin';
        $wilayahId = !empty($scope['wilayah_id']) ? (int) $scope['wilayah_id'] : null;
        $cabangId  = !empty($scope['cabang_id']) ? (int) $scope['cabang_id'] : null;

        // Total Wilayah, Cabang & Users based on Scope
        if ($role === 'superadmin') {
            $totalWilayah = $db->table('wilayah')->countAll();
            $totalCabang  = $db->table('cabang')->countAll();
            $totalUsers   = $db->table('users')->where('status', 1)->countAllResults();
        } elseif ($role === 'admin_wilayah') {
            $totalWilayah = 1;
            $totalCabang  = $db->table('cabang')->where('wilayah_id', $wilayahId)->countAllResults();
            $totalUsers   = $db->table('users')->where('status', 1)->where('wilayah_id', $wilayahId)->countAllResults();
        } else { // admin_cabang
            $totalWilayah = 1;
            $totalCabang  = 1;
            $totalUsers   = $db->table('users')->where('status', 1)->where('cabang_id', $cabangId)->countAllResults();
        }

        // 2. Gender Statistics
        $builderGender = $db->table('pemuda')
                            ->select('pemuda.gender, COUNT(pemuda.id) as total')
                            ->join('cabang', 'cabang.id = pemuda.cabang_id', 'left');
        $this->applyScope($builderGender, $scope);
        $genderStats = $builderGender->groupBy('pemuda.gender')->get()->getResultArray();

        $genderData = ['L' => 0, 'P' => 0];
        foreach ($genderStats as $row) {
            $genderData[$row['gender']] = (int) $row['total'];
        }

        // 3. Marital Status Statistics
        $builderMarital = $db->table('pemuda')
                             ->select('pemuda.marital_status, COUNT(pemuda.id) as total')
                             ->join('cabang', 'cabang.id = pemuda.cabang_id', 'left');
        $this->applyScope($builderMarital, $scope);
        $maritalStats = $builderMarital->groupBy('pemuda.marital_status')->get()->getResultArray();

        $maritalData = [
            'belum_menikah' => 0,
            'sudah_menikah' => 0,
            'janda'         => 0,
            'duda'          => 0,
        ];
        foreach ($maritalStats as $row) {
            $key = $row['marital_status'] ?: 'belum_menikah';
            $maritalData[$key] = (int) $row['total'];
        }

        // 4. Wilayah Statistics (Pemuda per Wilayah)
        $builderWilayah = $db->table('wilayah')
                             ->select('wilayah.id, wilayah.code, wilayah.name, COUNT(pemuda.id) as total')
                             ->join('cabang', 'cabang.wilayah_id = wilayah.id', 'left')
                             ->join('pemuda', 'pemuda.cabang_id = cabang.id', 'left')
                             ->groupBy('wilayah.id, wilayah.code, wilayah.name')
                             ->orderBy('wilayah.id', 'ASC');
        if ($role === 'admin_wilayah' && $wilayahId) {
            $builderWilayah->where('wilayah.id', $wilayahId);
        } elseif ($role === 'admin_cabang') {
            if ($wilayahId) {
                $builderWilayah->where('wilayah.id', $wilayahId);
            }
            if ($cabangId) {
                $builderWilayah->where('cabang.id', $cabangId);
            }
        }
        $wilayahStats = $builderWilayah->get()->getResultArray();

        // 5. Top Cabang Statistics (Top 10 Cabang with most youth)
        $builderCabang = $db->table('cabang')
                            ->select('cabang.id, cabang.name, wilayah.name as wilayah_name, COUNT(pemuda.id) as total')
                            ->join('wilayah', 'wilayah.id = cabang.wilayah_id', 'left')
                            ->join('pemuda', 'pemuda.cabang_id = cabang.id', 'left')
                            ->groupBy('cabang.id, cabang.name, wilayah.name')
                            ->orderBy('total', 'DESC')
                            ->limit(10);
        if ($role === 'admin_wilayah' && $wilayahId) {
            $builderCabang->where('cabang.wilayah_id', $wilayahId);
        } elseif ($role === 'admin_cabang' && $cabangId) {
            $builderCabang->where('cabang.id', $cabangId);
        }
        $topCabangStats = $builderCabang->get()->getResultArray();

        // 6. Education Level Statistics
        $builderEdu = $db->table('education_levels')
                         ->select('education_levels.id, education_levels.name, COUNT(pemuda.id) as total')
                         ->join('pendidikan', 'pendidikan.education_level_id = education_levels.id', 'left')
                         ->join('pemuda', 'pemuda.id = pendidikan.pemuda_id', 'left')
                         ->join('cabang', 'cabang.id = pemuda.cabang_id', 'left');
        $this->applyScope($builderEdu, $scope);
        $educationStats = $builderEdu->groupBy('education_levels.id, education_levels.name')
                                     ->orderBy('education_levels.id', 'ASC')
                                     ->get()->getResultArray();

        // 7. Job Status Statistics
        $builderJob = $db->table('job_statuses')
                         ->select('job_statuses.id, job_statuses.name, COUNT(pemuda.id) as total')
                         ->join('pekerjaan', 'pekerjaan.job_status_id = job_statuses.id', 'left')
                         ->join('pemuda', 'pemuda.id = pekerjaan.pemuda_id', 'left')
                         ->join('cabang', 'cabang.id = pemuda.cabang_id', 'left');
        $this->applyScope($builderJob, $scope);
        $jobStats = $builderJob->groupBy('job_statuses.id, job_statuses.name')
                               ->orderBy('job_statuses.id', 'ASC')
                               ->get()->getResultArray();

        // 8. Blood Type Statistics
        $builderBlood = $db->table('pemuda')
                           ->select('COALESCE(NULLIF(pemuda.blood_type, ""), "Tidak Tahu") as blood_type, COUNT(pemuda.id) as total')
                           ->join('cabang', 'cabang.id = pemuda.cabang_id', 'left');
        $this->applyScope($builderBlood, $scope);
        $bloodStats = $builderBlood->groupBy('blood_type')
                                   ->orderBy('total', 'DESC')
                                   ->get()->getResultArray();

        // 9. Recent Registrations (10 Terbaru)
        $builderRecent = $db->table('pemuda')
                            ->select('pemuda.*, cabang.name as cabang_name, wilayah.name as wilayah_name')
                            ->join('cabang', 'cabang.id = pemuda.cabang_id', 'left')
                            ->join('wilayah', 'wilayah.id = cabang.wilayah_id', 'left')
                            ->orderBy('pemuda.created_at', 'DESC')
                            ->limit(10);
        $this->applyScope($builderRecent, $scope);
        $recentRegistrations = $builderRecent->get()->getResultArray();

        return [
            'summary'             => $summary,
            'totalWilayah'        => $totalWilayah,
            'totalCabang'         => $totalCabang,
            'totalUsers'          => $totalUsers,
            'genderData'          => $genderData,
            'maritalData'         => $maritalData,
            'wilayahStats'        => $wilayahStats,
            'topCabangStats'      => $topCabangStats,
            'educationStats'      => $educationStats,
            'jobStats'            => $jobStats,
            'bloodStats'          => $bloodStats,
            'recentRegistrations' => $recentRegistrations,
        ];
    }
}
