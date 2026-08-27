<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PemudaModel;
use App\Models\WilayahModel;
use App\Models\CabangModel;
use App\Models\AlamatModel;
use App\Models\PendidikanModel;
use App\Models\PekerjaanModel;
use App\Models\OrganisasiModel;
use App\Models\EducationLevelModel;
use App\Models\JobStatusModel;
use App\Models\SkillModel;
use App\Models\InterestModel;
use App\Models\PemudaSkillModel;
use App\Models\PemudaInterestModel;
use App\Models\DistrictModel;
use App\Models\VillageModel;
use App\Services\PemudaImportService;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Config\Database;

class Pemuda extends BaseController
{
    protected PemudaModel $pemudaModel;
    protected WilayahModel $wilayahModel;
    protected CabangModel $cabangModel;
    protected AlamatModel $alamatModel;
    protected PendidikanModel $pendidikanModel;
    protected PekerjaanModel $pekerjaanModel;
    protected OrganisasiModel $organisasiModel;

    public function __construct()
    {
        $this->pemudaModel     = new PemudaModel();
        $this->wilayahModel    = new WilayahModel();
        $this->cabangModel     = new CabangModel();
        $this->alamatModel     = new AlamatModel();
        $this->pendidikanModel = new PendidikanModel();
        $this->pekerjaanModel  = new PekerjaanModel();
        $this->organisasiModel = new OrganisasiModel();
    }

    /**
     * Dapatkan scope user yang aktif
     */
    protected function getScope(): array
    {
        return [
            'role'       => session()->get('role'),
            'wilayah_id' => session()->get('wilayah_id'),
            'cabang_id'  => session()->get('cabang_id'),
        ];
    }

    /**
     * Manajemen Daftar Data Pemuda (List, Filter, Search, Pagination)
     */
    public function index(): string
    {
        $scope = $this->getScope();

        // 1. Ambil filter dari request & sesuaikan dengan scope role
        $filters = [
            'search'             => $this->request->getGet('search'),
            'wilayah_id'         => $this->request->getGet('wilayah_id'),
            'cabang_id'          => $this->request->getGet('cabang_id'),
            'gender'             => $this->request->getGet('gender'),
            'marital_status'     => $this->request->getGet('marital_status'),
            'blood_type'         => $this->request->getGet('blood_type'),
            'status_verifikasi'  => $this->request->getGet('status_verifikasi'),
            'status_data'        => $this->request->getGet('status_data') ?? 'active',
            'education_level_id' => $this->request->getGet('education_level_id'),
            'job_status_id'      => $this->request->getGet('job_status_id'),
            'start_date'         => $this->request->getGet('start_date'),
            'end_date'           => $this->request->getGet('end_date'),
        ];

        // Enforce scope on filters
        if ($scope['role'] === 'admin_wilayah') {
            $filters['wilayah_id'] = $scope['wilayah_id'];
        } elseif ($scope['role'] === 'admin_cabang') {
            $filters['wilayah_id'] = $scope['wilayah_id'];
            $filters['cabang_id']  = $scope['cabang_id'];
        }

        // Jika status_data kosong atau 'all', jangan filter status_data
        if ($filters['status_data'] === 'all') {
            unset($filters['status_data']);
        }

        $perPage = (int) ($this->request->getGet('per_page') ?: 15);
        if ($perPage < 5 || $perPage > 100) {
            $perPage = 15;
        }

        // 2. Query Builder dengan scope & filter
        $query = $this->pemudaModel->getFilteredQuery($filters, $scope)
                                   ->orderBy('pemuda.created_at', 'DESC');

        $pemudaList = $query->paginate($perPage, 'pemuda');
        $pager      = $this->pemudaModel->pager;

        // 3. Status Summary Counters
        $summary = $this->pemudaModel->getCountsSummary($scope);

        // 4. Reference Data untuk dropdown filter (disesuaikan scope)
        $wilayahBuilder = $this->wilayahModel->orderBy('id', 'ASC');
        if ($scope['role'] === 'admin_wilayah' && !empty($scope['wilayah_id'])) {
            $wilayahBuilder->where('id', (int) $scope['wilayah_id']);
        } elseif ($scope['role'] === 'admin_cabang' && !empty($scope['wilayah_id'])) {
            $wilayahBuilder->where('id', (int) $scope['wilayah_id']);
        }
        $wilayahList = $wilayahBuilder->findAll();
        
        $cabangBuilder = $this->cabangModel->orderBy('name', 'ASC');
        if ($scope['role'] === 'admin_cabang' && !empty($scope['cabang_id'])) {
            $cabangBuilder->where('id', (int) $scope['cabang_id']);
        } elseif ($scope['role'] === 'admin_wilayah' && !empty($scope['wilayah_id'])) {
            $cabangBuilder->where('wilayah_id', (int) $scope['wilayah_id']);
        } elseif (!empty($filters['wilayah_id'])) {
            $cabangBuilder->where('wilayah_id', (int) $filters['wilayah_id']);
        }
        $cabangList = $cabangBuilder->findAll();

        $educationLevelModel = new EducationLevelModel();
        $jobStatusModel      = new JobStatusModel();

        $educationLevels = $educationLevelModel->findAll();
        $jobStatuses     = $jobStatusModel->findAll();

        $data = [
            'title'           => 'Manajemen Data Pemuda',
            'pemudaList'      => $pemudaList,
            'pager'           => $pager,
            'filters'         => $filters,
            'perPage'         => $perPage,
            'summary'         => $summary,
            'wilayahList'     => $wilayahList,
            'cabangList'      => $cabangList,
            'educationLevels' => $educationLevels,
            'jobStatuses'     => $jobStatuses,
            'user'            => session()->get(),
        ];

        return view('admin/pemuda/index', $data);
    }

    /**
     * Detail Data Pemuda
     */
    public function detail(int $id)
    {
        $scope = $this->getScope();
        $pemuda = $this->pemudaModel->getPemudaDetail($id, $scope);

        if (!$pemuda) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Data pemuda tidak ditemukan atau Anda tidak memiliki akses.'])->setStatusCode(404);
            }
            return redirect()->to(base_url('admin/pemuda'))
                             ->with('error', 'Data pemuda tidak ditemukan atau Anda tidak memiliki akses.');
        }

        if ($this->request->isAJAX()) {
            return view('admin/pemuda/_modal_detail', ['pemuda' => $pemuda]);
        }

        $data = [
            'title'  => 'Detail Pemuda - ' . $pemuda['name'],
            'pemuda' => $pemuda,
            'user'   => session()->get(),
        ];

        return view('admin/pemuda/detail', $data);
    }

    /**
     * Form Tambah Data Pemuda oleh Admin
     */
    public function tambah(): string
    {
        $scope = $this->getScope();
        $educationLevelModel = new EducationLevelModel();
        $jobStatusModel      = new JobStatusModel();
        $skillModel          = new SkillModel();
        $interestModel       = new InterestModel();
        $districtModel       = new DistrictModel();

        $filterWilayahId = ($scope['role'] === 'admin_wilayah' || $scope['role'] === 'admin_cabang') ? (int) $scope['wilayah_id'] : null;
        $filterCabangId  = ($scope['role'] === 'admin_cabang') ? (int) $scope['cabang_id'] : null;

        $wilayahWithCabang = $this->wilayahModel->getWithCabang($filterWilayahId, $filterCabangId);
        $districts         = $districtModel->where('regency_id', 3314)->orderBy('name', 'ASC')->findAll();

        $availableOrganizations = [
            [
                'key'         => 'satgas',
                'name'        => 'Satgas',
                'title'       => 'Satgas',
                'badge'       => 'Satuan Tugas',
                'description' => 'Satuan Tugas Pengamanan, Penertiban, dan Pengawalan Kegiatan',
                'icon'        => 'fas fa-shield-alt text-danger',
            ],
            [
                'key'         => 'bankom',
                'name'        => 'Bankom',
                'title'       => 'Bankom',
                'badge'       => 'Bantuan Komunikasi',
                'description' => 'Unit Bantuan Komunikasi, Radio Pancar Ulang, dan Informasi Lapangan',
                'icon'        => 'fas fa-broadcast-tower text-primary',
            ],
            [
                'key'         => 'parkir',
                'name'        => 'Parkir',
                'title'       => 'Parkir',
                'badge'       => 'Tim Parkir',
                'description' => 'Tim Pengaturan Parkir, Kelancaran Arus, dan Kerapian Kendaraan',
                'icon'        => 'fas fa-parking text-warning',
            ],
            [
                'key'         => 'pemuda',
                'name'        => 'Pemuda',
                'title'       => 'Pemuda',
                'badge'       => 'Kepengurusan Pemuda',
                'description' => 'Keaktifan Struktural / Anggota dalam Kegiatan Kepemudaan Cabang/Wilayah',
                'icon'        => 'fas fa-users text-success',
            ],
            [
                'key'         => 'tim_ikhrom',
                'name'        => 'Tim Ikhrom',
                'title'       => 'Tim Ikhrom',
                'badge'       => 'Perawatan Jenazah',
                'description' => 'Tim Khusus Pelayanan, Perawatan, dan Pengurusan Jenazah',
                'icon'        => 'fas fa-heart text-info',
            ],
        ];

        $data = [
            'title'                  => 'Tambah Data Pemuda',
            'wilayahWithCabang'      => $wilayahWithCabang,
            'educationLevels'        => $educationLevelModel->findAll(),
            'jobStatuses'            => $jobStatusModel->findAll(),
            'skills'                 => $skillModel->findAll(),
            'interests'              => $interestModel->findAll(),
            'districts'              => $districts,
            'availableOrganizations' => $availableOrganizations,
            'activeOrganizations'    => [],
            'otherOrganizations'     => '',
            'user'                   => session()->get(),
            'mode'                   => 'create',
        ];

        return view('admin/pemuda/form', $data);
    }

    /**
     * Simpan Data Pemuda Baru
     */
    public function simpan()
    {
        $scope = $this->getScope();

        // 1. Validation Rules
        $rules = [
            'cabang_id'          => 'required|is_natural_no_zero',
            'name'               => 'required|min_length[3]|max_length[150]',
            'gender'             => 'required|in_list[L,P]',
            'marital_status'     => 'required|in_list[belum_menikah,sudah_menikah,janda,duda]',
            'blood_type'         => 'permit_empty|in_list[A,B,AB,O,tidak_tahu,-]',
            'birth_place'        => 'required|max_length[100]',
            'birth_date'         => 'required|valid_date',
            'phone'              => 'required|min_length[9]|max_length[20]',
            'email'              => 'permit_empty|valid_email|max_length[100]',
            'district_id'        => 'required',
            'village_id'         => 'required',
            'address_detail'     => 'required|min_length[5]',
            'education_level_id' => 'required',
            'school_name'        => 'required|min_length[3]|max_length[150]',
            'education_status'   => 'required|in_list[sedang_sekolah,lulus,putus_sekolah]',
            'job_status_id'      => 'required',
        ];

        // Status verifikasi input validation (only required if permitted)
        if (in_array($scope['role'], ['superadmin', 'admin_cabang'], true)) {
            $rules['status_verifikasi'] = 'permit_empty|in_list[pending,verified,rejected]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->validator->getErrors())
                             ->with('error', 'Silakan lengkapi kolom yang wajib diisi.');
        }

        $cabangId = (int) $this->request->getPost('cabang_id');

        // Scope check for admin_cabang or admin_wilayah
        if ($scope['role'] === 'admin_cabang' && $cabangId !== (int) $scope['cabang_id']) {
            return redirect()->back()->withInput()->with('error', 'Anda hanya dapat mendaftarkan pemuda pada cabang Anda sendiri.');
        }

        if ($scope['role'] === 'admin_wilayah') {
            $targetCabang = $this->cabangModel->find($cabangId);
            if (!$targetCabang || (int) $targetCabang['wilayah_id'] !== (int) $scope['wilayah_id']) {
                return redirect()->back()->withInput()->with('error', 'Anda hanya dapat mendaftarkan pemuda pada cabang dalam wilayah Anda.');
            }
        }

        // Verification permission check: admin_wilayah cannot set verified/rejected, always 'pending'
        if ($scope['role'] === 'admin_wilayah') {
            $statusVerifikasi = 'pending';
        } else {
            $statusVerifikasi = $this->request->getPost('status_verifikasi') ?: 'verified';
        }

        $name      = (string) $this->request->getPost('name');
        $birthDate = (string) $this->request->getPost('birth_date');

        // Pengecekan data ganda (nama, tanggal lahir, dan cabang)
        $duplicate = $this->pemudaModel->findDuplicate($name, $birthDate, $cabangId);
        if ($duplicate) {
            $formattedBirth = date('d/m/Y', strtotime($birthDate));
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Gagal menyimpan: Data pemuda dengan nama "' . esc($name) . '", tanggal lahir (' . $formattedBirth . '), dan cabang tersebut sudah terdaftar di sistem (No. Registrasi: ' . esc($duplicate['registration_number']) . '). Input data ganda ditolak.');
        }

        $db = Database::connect();
        $db->transStart();

        try {
            $regNumber = $this->pemudaModel->generateRegistrationNumber();

            // 1. Insert Pemuda
            $pemudaData = [
                'cabang_id'           => $cabangId,
                'registration_number' => $regNumber,
                'name'                => $this->request->getPost('name'),
                'gender'              => $this->request->getPost('gender'),
                'marital_status'      => $this->request->getPost('marital_status') ?: 'belum_menikah',
                'blood_type'          => $this->request->getPost('blood_type') ?: null,
                'birth_place'         => $this->request->getPost('birth_place'),
                'birth_date'          => $this->request->getPost('birth_date'),
                'phone'               => $this->request->getPost('phone'),
                'email'               => $this->request->getPost('email') ?: null,
                'status_verifikasi'   => $statusVerifikasi,
                'status_data'         => $this->request->getPost('status_data') ?: 'active',
                'created_by'          => session()->get('user_id'),
            ];

            $this->pemudaModel->insert($pemudaData);
            $pemudaId = $this->pemudaModel->getInsertID();

            // 2. Insert Alamat
            $this->alamatModel->insert([
                'pemuda_id'      => $pemudaId,
                'province_id'    => 33, // Jawa Tengah
                'regency_id'     => 3314, // Sragen
                'district_id'    => (int) $this->request->getPost('district_id'),
                'village_id'     => (int) $this->request->getPost('village_id'),
                'dusun'          => $this->request->getPost('dusun') ?: null,
                'rt'             => $this->request->getPost('rt') ?: null,
                'rw'             => $this->request->getPost('rw') ?: null,
                'address_detail' => $this->request->getPost('address_detail'),
            ]);

            // 3. Insert Pendidikan
            $this->pendidikanModel->insert([
                'pemuda_id'          => $pemudaId,
                'education_level_id' => (int) $this->request->getPost('education_level_id'),
                'school_name'        => $this->request->getPost('school_name'),
                'major'              => $this->request->getPost('major') ?: null,
                'education_status'   => $this->request->getPost('education_status'),
                'graduation_year'    => $this->request->getPost('graduation_year') ? (int) $this->request->getPost('graduation_year') : null,
            ]);

            // 4. Insert Pekerjaan
            $this->pekerjaanModel->insert([
                'pemuda_id'      => $pemudaId,
                'job_status_id'  => (int) $this->request->getPost('job_status_id'),
                'job_title'      => $this->request->getPost('job_title') ?: null,
                'company_name'   => $this->request->getPost('company_name') ?: null,
                'business_field' => $this->request->getPost('business_field') ?: null,
            ]);

            // 5. Insert Organisasi
            $organizations = $this->request->getPost('organizations');
            if (!empty($organizations) && is_array($organizations)) {
                foreach ($organizations as $orgKey => $org) {
                    if (!empty($org['selected'])) {
                        $orgName  = !empty($org['name']) ? $org['name'] : (is_string($org['selected']) ? $org['selected'] : ucfirst($orgKey));
                        $position = !empty($org['position']) ? $org['position'] : 'Anggota';
                        $joinDate = !empty($org['join_year']) ? ($org['join_year'] . '-01-01') : (!empty($org['join_date']) ? $org['join_date'] : null);
                        $desc     = !empty($org['description']) ? $org['description'] : null;

                        $this->organisasiModel->insert([
                            'pemuda_id'         => $pemudaId,
                            'organization_name' => $orgName,
                            'position'          => $position,
                            'join_date'         => $joinDate,
                            'description'       => $desc,
                        ]);
                    }
                }
            }

            // Insert other organizations if provided
            $otherOrg = trim((string) $this->request->getPost('other_organization'));
            if (!empty($otherOrg)) {
                $otherOrgs = array_map('trim', explode(',', $otherOrg));
                foreach ($otherOrgs as $name) {
                    if (!empty($name)) {
                        $this->organisasiModel->insert([
                            'pemuda_id'         => $pemudaId,
                            'organization_name' => $name,
                            'position'          => 'Anggota',
                        ]);
                    }
                }
            }

            // 6. Insert Skills
            $skillsPost = $this->request->getPost('skills');
            if (!empty($skillsPost) && is_array($skillsPost)) {
                $pemudaSkillModel = new PemudaSkillModel();
                foreach ($skillsPost as $skillId => $skillData) {
                    if (!empty($skillData['selected'])) {
                        $pemudaSkillModel->insert([
                            'pemuda_id' => $pemudaId,
                            'skill_id'  => (int) $skillId,
                            'level'     => $skillData['level'] ?? 'pemula',
                        ]);
                    }
                }
            }

            // 7. Insert Interests
            $interestsPost = $this->request->getPost('interests');
            if (!empty($interestsPost) && is_array($interestsPost)) {
                $pemudaInterestModel = new PemudaInterestModel();
                foreach ($interestsPost as $interestId) {
                    $pemudaInterestModel->insert([
                        'pemuda_id'   => $pemudaId,
                        'interest_id' => (int) $interestId,
                    ]);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem saat menyimpan data.');
            }

            return redirect()->to(base_url('admin/pemuda/detail/' . $pemudaId))
                             ->with('success', 'Data pemuda berhasil ditambahkan dengan nomor registrasi: ' . $regNumber);

        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Form Edit Data Pemuda
     */
    public function edit(int $id)
    {
        $scope = $this->getScope();
        $pemuda = $this->pemudaModel->getPemudaDetail($id, $scope);

        if (!$pemuda) {
            return redirect()->to(base_url('admin/pemuda'))
                             ->with('error', 'Data pemuda tidak ditemukan atau Anda tidak memiliki akses.');
        }

        $educationLevelModel = new EducationLevelModel();
        $jobStatusModel      = new JobStatusModel();
        $skillModel          = new SkillModel();
        $interestModel       = new InterestModel();
        $districtModel       = new DistrictModel();
        $villageModel        = new VillageModel();

        $filterWilayahId = ($scope['role'] === 'admin_wilayah' || $scope['role'] === 'admin_cabang') ? (int) $scope['wilayah_id'] : null;
        $filterCabangId  = ($scope['role'] === 'admin_cabang') ? (int) $scope['cabang_id'] : null;

        $wilayahWithCabang = $this->wilayahModel->getWithCabang($filterWilayahId, $filterCabangId);
        $districts         = $districtModel->where('regency_id', 3314)->orderBy('name', 'ASC')->findAll();
        
        $villages = [];
        if (!empty($pemuda['district_id'])) {
            $villages = $villageModel->where('district_id', $pemuda['district_id'])->orderBy('name', 'ASC')->findAll();
        }

        // Map existing skills and interests for form check
        $activeSkillIds = [];
        $activeSkillLevels = [];
        foreach ($pemuda['skills'] as $s) {
            $activeSkillIds[] = $s['skill_id'];
            $activeSkillLevels[$s['skill_id']] = $s['level'];
        }

        $activeInterestIds = [];
        foreach ($pemuda['interests'] as $i) {
            $activeInterestIds[] = $i['interest_id'];
        }

        // Map existing organizations
        $availableOrganizations = [
            [
                'key'         => 'satgas',
                'name'        => 'Satgas',
                'title'       => 'Satgas',
                'badge'       => 'Satuan Tugas',
                'description' => 'Satuan Tugas Pengamanan, Penertiban, dan Pengawalan Kegiatan',
                'icon'        => 'fas fa-shield-alt text-danger',
            ],
            [
                'key'         => 'bankom',
                'name'        => 'Bankom',
                'title'       => 'Bankom',
                'badge'       => 'Bantuan Komunikasi',
                'description' => 'Unit Bantuan Komunikasi, Radio Pancar Ulang, dan Informasi Lapangan',
                'icon'        => 'fas fa-broadcast-tower text-primary',
            ],
            [
                'key'         => 'parkir',
                'name'        => 'Parkir',
                'title'       => 'Parkir',
                'badge'       => 'Tim Parkir',
                'description' => 'Tim Pengaturan Parkir, Kelancaran Arus, dan Kerapian Kendaraan',
                'icon'        => 'fas fa-parking text-warning',
            ],
            [
                'key'         => 'pemuda',
                'name'        => 'Pemuda',
                'title'       => 'Pemuda',
                'badge'       => 'Kepengurusan Pemuda',
                'description' => 'Keaktifan Struktural / Anggota dalam Kegiatan Kepemudaan Cabang/Wilayah',
                'icon'        => 'fas fa-users text-success',
            ],
            [
                'key'         => 'tim_ikhrom',
                'name'        => 'Tim Ikhrom',
                'title'       => 'Tim Ikhrom',
                'badge'       => 'Perawatan Jenazah',
                'description' => 'Tim Khusus Pelayanan, Perawatan, dan Pengurusan Jenazah',
                'icon'        => 'fas fa-heart text-info',
            ],
        ];

        $activeOrgs = [];
        $otherOrgsList = [];
        $knownOrgKeys = [
            'satgas'     => 'Satgas',
            'bankom'     => 'Bankom',
            'parkir'     => 'Parkir',
            'pemuda'     => 'Pemuda',
            'tim_ikhrom' => 'Tim Ikhrom',
        ];

        if (!empty($pemuda['organisasi'])) {
            foreach ($pemuda['organisasi'] as $o) {
                $matchedKey = null;
                foreach ($knownOrgKeys as $k => $name) {
                    if (strcasecmp($o['organization_name'], $name) === 0 || strcasecmp(str_replace([' ', '_', '-'], '', $o['organization_name']), str_replace([' ', '_', '-'], '', $name)) === 0) {
                        $matchedKey = $k;
                        break;
                    }
                }
                if ($matchedKey) {
                    $activeOrgs[$matchedKey] = [
                        'selected'    => true,
                        'name'        => $o['organization_name'],
                        'position'    => $o['position'] ?? 'Anggota',
                        'join_year'   => !empty($o['join_date']) ? date('Y', strtotime($o['join_date'])) : date('Y'),
                        'description' => $o['description'] ?? '',
                    ];
                } else {
                    $otherOrgsList[] = $o['organization_name'];
                }
            }
        }

        $data = [
            'title'                  => 'Edit Data Pemuda - ' . $pemuda['name'],
            'pemuda'                 => $pemuda,
            'wilayahWithCabang'      => $wilayahWithCabang,
            'educationLevels'        => $educationLevelModel->findAll(),
            'jobStatuses'            => $jobStatusModel->findAll(),
            'skills'                 => $skillModel->findAll(),
            'interests'              => $interestModel->findAll(),
            'districts'              => $districts,
            'villages'               => $villages,
            'activeSkillIds'         => $activeSkillIds,
            'activeSkillLevels'      => $activeSkillLevels,
            'activeInterestIds'      => $activeInterestIds,
            'availableOrganizations' => $availableOrganizations,
            'activeOrganizations'    => $activeOrgs,
            'otherOrganizations'     => implode(', ', $otherOrgsList),
            'user'                   => session()->get(),
            'mode'                   => 'edit',
        ];

        return view('admin/pemuda/form', $data);
    }

    /**
     * Update Data Pemuda
     */
    public function update(int $id)
    {
        $scope = $this->getScope();
        $existing = $this->pemudaModel->getPemudaDetail($id, $scope);

        if (!$existing) {
            return redirect()->to(base_url('admin/pemuda'))
                             ->with('error', 'Data pemuda tidak ditemukan atau Anda tidak memiliki akses.');
        }

        $rules = [
            'cabang_id'          => 'required|is_natural_no_zero',
            'name'               => 'required|min_length[3]|max_length[150]',
            'gender'             => 'required|in_list[L,P]',
            'marital_status'     => 'required|in_list[belum_menikah,sudah_menikah,janda,duda]',
            'blood_type'         => 'permit_empty|in_list[A,B,AB,O,tidak_tahu,-]',
            'birth_place'        => 'required|max_length[100]',
            'birth_date'         => 'required|valid_date',
            'phone'              => 'required|min_length[9]|max_length[20]',
            'email'              => 'permit_empty|valid_email|max_length[100]',
            'district_id'        => 'required',
            'village_id'         => 'required',
            'address_detail'     => 'required|min_length[5]',
            'education_level_id' => 'required',
            'school_name'        => 'required|min_length[3]|max_length[150]',
            'education_status'   => 'required|in_list[sedang_sekolah,lulus,putus_sekolah]',
            'job_status_id'      => 'required',
            'status_data'        => 'required|in_list[active,archived]',
        ];

        if (in_array($scope['role'], ['superadmin', 'admin_cabang'], true)) {
            $rules['status_verifikasi'] = 'permit_empty|in_list[pending,verified,rejected]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->validator->getErrors())
                             ->with('error', 'Silakan periksa input form Anda.');
        }

        $cabangId = (int) $this->request->getPost('cabang_id');

        if ($scope['role'] === 'admin_cabang' && $cabangId !== (int) $scope['cabang_id']) {
            return redirect()->back()->withInput()->with('error', 'Anda hanya dapat mengelola pemuda pada cabang Anda sendiri.');
        }

        if ($scope['role'] === 'admin_wilayah') {
            $targetCabang = $this->cabangModel->find($cabangId);
            if (!$targetCabang || (int) $targetCabang['wilayah_id'] !== (int) $scope['wilayah_id']) {
                return redirect()->back()->withInput()->with('error', 'Anda hanya dapat mengelola pemuda pada cabang dalam wilayah Anda.');
            }
        }

        // Verification status handling
        if ($scope['role'] === 'admin_wilayah') {
            $statusVerifikasi = $existing['status_verifikasi'];
        } else {
            $statusVerifikasi = $this->request->getPost('status_verifikasi') ?: $existing['status_verifikasi'];
        }

        $name      = (string) $this->request->getPost('name');
        $birthDate = (string) $this->request->getPost('birth_date');

        // Pengecekan data ganda (nama, tanggal lahir, dan cabang) mengecualikan pemuda yang sedang diedit
        $duplicate = $this->pemudaModel->findDuplicate($name, $birthDate, $cabangId, $id);
        if ($duplicate) {
            $formattedBirth = date('d/m/Y', strtotime($birthDate));
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Gagal memperbarui: Data pemuda dengan nama "' . esc($name) . '", tanggal lahir (' . $formattedBirth . '), dan cabang tersebut sudah digunakan oleh pemuda lain (No. Registrasi: ' . esc($duplicate['registration_number']) . ').');
        }

        $db = Database::connect();
        $db->transStart();

        try {
            // 1. Update Pemuda
            $pemudaData = [
                'cabang_id'         => $cabangId,
                'name'              => $this->request->getPost('name'),
                'gender'            => $this->request->getPost('gender'),
                'marital_status'    => $this->request->getPost('marital_status') ?: 'belum_menikah',
                'blood_type'        => $this->request->getPost('blood_type') ?: null,
                'birth_place'       => $this->request->getPost('birth_place'),
                'birth_date'        => $this->request->getPost('birth_date'),
                'phone'             => $this->request->getPost('phone'),
                'email'             => $this->request->getPost('email') ?: null,
                'status_verifikasi' => $statusVerifikasi,
                'status_data'       => $this->request->getPost('status_data'),
            ];

            $this->pemudaModel->update($id, $pemudaData);

            // 2. Update/Insert Alamat
            $alamat = $this->alamatModel->where('pemuda_id', $id)->first();
            $alamatData = [
                'pemuda_id'      => $id,
                'province_id'    => 33,
                'regency_id'     => 3314,
                'district_id'    => (int) $this->request->getPost('district_id'),
                'village_id'     => (int) $this->request->getPost('village_id'),
                'dusun'          => $this->request->getPost('dusun') ?: null,
                'rt'             => $this->request->getPost('rt') ?: null,
                'rw'             => $this->request->getPost('rw') ?: null,
                'address_detail' => $this->request->getPost('address_detail'),
            ];
            if ($alamat) {
                $this->alamatModel->update($alamat['id'], $alamatData);
            } else {
                $this->alamatModel->insert($alamatData);
            }

            // 3. Update/Insert Pendidikan
            $pendidikan = $this->pendidikanModel->where('pemuda_id', $id)->first();
            $pendidikanData = [
                'pemuda_id'          => $id,
                'education_level_id' => (int) $this->request->getPost('education_level_id'),
                'school_name'        => $this->request->getPost('school_name'),
                'major'              => $this->request->getPost('major') ?: null,
                'education_status'   => $this->request->getPost('education_status'),
                'graduation_year'    => $this->request->getPost('graduation_year') ? (int) $this->request->getPost('graduation_year') : null,
            ];
            if ($pendidikan) {
                $this->pendidikanModel->update($pendidikan['id'], $pendidikanData);
            } else {
                $this->pendidikanModel->insert($pendidikanData);
            }

            // 4. Update/Insert Pekerjaan
            $pekerjaan = $this->pekerjaanModel->where('pemuda_id', $id)->first();
            $pekerjaanData = [
                'pemuda_id'      => $id,
                'job_status_id'  => (int) $this->request->getPost('job_status_id'),
                'job_title'      => $this->request->getPost('job_title') ?: null,
                'company_name'   => $this->request->getPost('company_name') ?: null,
                'business_field' => $this->request->getPost('business_field') ?: null,
            ];
            if ($pekerjaan) {
                $this->pekerjaanModel->update($pekerjaan['id'], $pekerjaanData);
            } else {
                $this->pekerjaanModel->insert($pekerjaanData);
            }

            // 5. Update Organisasi (Replace existing entries)
            $this->organisasiModel->where('pemuda_id', $id)->delete();
            $organizations = $this->request->getPost('organizations');
            if (!empty($organizations) && is_array($organizations)) {
                foreach ($organizations as $orgKey => $org) {
                    if (!empty($org['selected'])) {
                        $orgName  = !empty($org['name']) ? $org['name'] : (is_string($org['selected']) ? $org['selected'] : ucfirst($orgKey));
                        $position = !empty($org['position']) ? $org['position'] : 'Anggota';
                        $joinDate = !empty($org['join_year']) ? ($org['join_year'] . '-01-01') : (!empty($org['join_date']) ? $org['join_date'] : null);
                        $desc     = !empty($org['description']) ? $org['description'] : null;

                        $this->organisasiModel->insert([
                            'pemuda_id'         => $id,
                            'organization_name' => $orgName,
                            'position'          => $position,
                            'join_date'         => $joinDate,
                            'description'       => $desc,
                        ]);
                    }
                }
            }

            $otherOrg = trim((string) $this->request->getPost('other_organization'));
            if (!empty($otherOrg)) {
                $otherOrgs = array_map('trim', explode(',', $otherOrg));
                foreach ($otherOrgs as $name) {
                    if (!empty($name)) {
                        $this->organisasiModel->insert([
                            'pemuda_id'         => $id,
                            'organization_name' => $name,
                            'position'          => 'Anggota',
                        ]);
                    }
                }
            }

            // 6. Update Skills
            $pemudaSkillModel = new PemudaSkillModel();
            $pemudaSkillModel->where('pemuda_id', $id)->delete();
            $skillsPost = $this->request->getPost('skills');
            if (!empty($skillsPost) && is_array($skillsPost)) {
                foreach ($skillsPost as $skillId => $skillData) {
                    if (!empty($skillData['selected'])) {
                        $pemudaSkillModel->insert([
                            'pemuda_id' => $id,
                            'skill_id'  => (int) $skillId,
                            'level'     => $skillData['level'] ?? 'pemula',
                        ]);
                    }
                }
            }

            // 7. Update Interests
            $pemudaInterestModel = new PemudaInterestModel();
            $pemudaInterestModel->where('pemuda_id', $id)->delete();
            $interestsPost = $this->request->getPost('interests');
            if (!empty($interestsPost) && is_array($interestsPost)) {
                foreach ($interestsPost as $interestId) {
                    $pemudaInterestModel->insert([
                        'pemuda_id'   => $id,
                        'interest_id' => (int) $interestId,
                    ]);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem saat memperbarui data.');
            }

            return redirect()->to(base_url('admin/pemuda/detail/' . $id))
                             ->with('success', 'Data pemuda berhasil diperbarui.');

        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Verifikasi Status Pemuda (Pending / Verified / Rejected)
     */
    public function verifikasi(int $id)
    {
        $scope = $this->getScope();

        // Verification permission check: only superadmin and admin_cabang are allowed
        if (!in_array($scope['role'], ['superadmin', 'admin_cabang'], true)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status'  => 'error', 
                    'message' => 'Akses ditolak. Admin Wilayah tidak memiliki izin untuk melakukan verifikasi data pemuda.'
                ])->setStatusCode(403);
            }
            return redirect()->back()->with('error', 'Akses ditolak. Admin Wilayah tidak memiliki izin untuk melakukan verifikasi data pemuda.');
        }

        $status = $this->request->getPost('status');

        if (!in_array($status, ['pending', 'verified', 'rejected'], true)) {
            return redirect()->back()->with('error', 'Status verifikasi tidak valid.');
        }

        $pemuda = $this->pemudaModel->getPemudaDetail($id, $scope);
        if (!$pemuda) {
            return redirect()->back()->with('error', 'Data pemuda tidak ditemukan atau Anda tidak memiliki akses.');
        }

        $this->pemudaModel->update($id, [
            'status_verifikasi' => $status,
        ]);

        $statusLabels = [
            'verified' => 'Diverifikasi',
            'rejected' => 'Ditolak',
            'pending'  => 'Menunggu Verifikasi',
        ];

        $msg = 'Status pemuda ' . esc($pemuda['name']) . ' berhasil diubah menjadi ' . ($statusLabels[$status] ?? $status) . '.';

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'message' => $msg, 'new_status' => $status]);
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Toggle Archive / Active Status
     */
    public function archive(int $id)
    {
        $scope = $this->getScope();
        $pemuda = $this->pemudaModel->getPemudaDetail($id, $scope);

        if (!$pemuda) {
            return redirect()->back()->with('error', 'Data pemuda tidak ditemukan atau Anda tidak memiliki akses.');
        }

        $newStatus = ($pemuda['status_data'] === 'archived') ? 'active' : 'archived';
        $this->pemudaModel->update($id, ['status_data' => $newStatus]);

        $msg = ($newStatus === 'archived') 
            ? 'Data pemuda ' . esc($pemuda['name']) . ' berhasil diarsipkan.' 
            : 'Data pemuda ' . esc($pemuda['name']) . ' berhasil diaktifkan kembali.';

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Hapus Data Pemuda Permanen (Super Admin Only)
     */
    public function delete(int $id)
    {
        $scope = $this->getScope();

        // Check if user is superadmin
        if ($scope['role'] !== 'superadmin') {
            return redirect()->back()->with('error', 'Hanya Super Administrator yang berhak menghapus data permanen.');
        }

        $pemuda = $this->pemudaModel->find($id);
        if (!$pemuda) {
            return redirect()->back()->with('error', 'Data pemuda tidak ditemukan.');
        }

        $this->pemudaModel->delete($id);

        return redirect()->to(base_url('admin/pemuda'))
                         ->with('success', 'Data pemuda ' . esc($pemuda['name']) . ' telah dihapus permanen dari sistem.');
    }

    /**
     * Cetak Profil / Formulir Pemuda
     */
    public function cetak(int $id)
    {
        $scope = $this->getScope();
        $pemuda = $this->pemudaModel->getPemudaDetail($id, $scope);

        if (!$pemuda) {
            return redirect()->to(base_url('admin/pemuda'))->with('error', 'Data pemuda tidak ditemukan.');
        }

        return view('admin/pemuda/cetak', [
            'pemuda' => $pemuda,
            'title'  => 'Biodata Pemuda - ' . $pemuda['registration_number'],
        ]);
    }

    /**
     * Export Data Pemuda ke File Excel / CSV Berdasarkan Filter Aktif
     */
    public function export()
    {
        $scope = $this->getScope();

        $filters = [
            'search'             => $this->request->getGet('search'),
            'wilayah_id'         => $this->request->getGet('wilayah_id'),
            'cabang_id'          => $this->request->getGet('cabang_id'),
            'gender'             => $this->request->getGet('gender'),
            'marital_status'     => $this->request->getGet('marital_status'),
            'blood_type'         => $this->request->getGet('blood_type'),
            'status_verifikasi'  => $this->request->getGet('status_verifikasi'),
            'status_data'        => $this->request->getGet('status_data'),
            'education_level_id' => $this->request->getGet('education_level_id'),
            'job_status_id'      => $this->request->getGet('job_status_id'),
            'start_date'         => $this->request->getGet('start_date'),
            'end_date'           => $this->request->getGet('end_date'),
        ];

        // Enforce scope on export filters
        if ($scope['role'] === 'admin_wilayah') {
            $filters['wilayah_id'] = $scope['wilayah_id'];
        } elseif ($scope['role'] === 'admin_cabang') {
            $filters['wilayah_id'] = $scope['wilayah_id'];
            $filters['cabang_id']  = $scope['cabang_id'];
        }

        if ($filters['status_data'] === 'all') {
            unset($filters['status_data']);
        }

        $query = $this->pemudaModel->getFilteredQuery($filters, $scope)
                                   ->orderBy('pemuda.created_at', 'DESC');
        $rows  = $query->findAll();

        $filename = 'Export_Data_Pemuda_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // UTF-8 BOM for Microsoft Excel compatibility
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header columns
        fputcsv($output, [
            'No',
            'No Registrasi',
            'Nama Lengkap',
            'Jenis Kelamin',
            'Status Pernikahan',
            'Golongan Darah',
            'Tempat Lahir',
            'Tanggal Lahir',
            'No Telepon / WhatsApp',
            'Email',
            'Wilayah',
            'Cabang',
            'Alamat Detail',
            'Dusun / Dukuh',
            'RT',
            'RW',
            'Desa / Kelurahan',
            'Kecamatan',
            'Jenjang Pendidikan',
            'Nama Sekolah / Kampus',
            'Jurusan',
            'Status Pendidikan',
            'Status Pekerjaan',
            'Profesi / Jabatan',
            'Nama Perusahaan / Usaha',
            'Status Verifikasi',
            'Status Data',
            'Tanggal Registrasi',
        ]);

        $no = 1;
        $maritalLabels = [
            'belum_menikah' => 'Belum Menikah',
            'sudah_menikah' => 'Sudah Menikah',
            'janda'         => 'Janda',
            'duda'          => 'Duda',
        ];

        $verifLabels = [
            'pending'  => 'Menunggu',
            'verified' => 'Terverifikasi',
            'rejected' => 'Ditolak',
        ];

        foreach ($rows as $row) {
            fputcsv($output, [
                $no++,
                $row['registration_number'],
                $row['name'],
                $row['gender'] === 'L' ? 'Laki-laki' : 'Perempuan',
                $maritalLabels[$row['marital_status']] ?? $row['marital_status'],
                $row['blood_type'] ?: '-',
                $row['birth_place'] ?: '-',
                $row['birth_date'] ? date('d/m/Y', strtotime($row['birth_date'])) : '-',
                $row['phone'] ? ("'" . $row['phone']) : '-', // leading quote for excel phone numbers
                $row['email'] ?: '-',
                $row['wilayah_name'] ?: '-',
                $row['cabang_name'] ?: '-',
                $row['address_detail'] ?: '-',
                $row['dusun'] ?: '-',
                $row['rt'] ?: '-',
                $row['rw'] ?: '-',
                $row['village_name'] ?: '-',
                $row['district_name'] ?: '-',
                $row['education_level_name'] ?: '-',
                $row['school_name'] ?: '-',
                $row['major'] ?: '-',
                $row['education_status'] ?: '-',
                $row['job_status_name'] ?: '-',
                $row['job_title'] ?: '-',
                $row['company_name'] ?: '-',
                $verifLabels[$row['status_verifikasi']] ?? $row['status_verifikasi'],
                $row['status_data'] === 'active' ? 'Aktif' : 'Arsip',
                $row['created_at'] ? date('d/m/Y H:i', strtotime($row['created_at'])) : '-',
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Halaman Formulir Import Data Pemuda (Khusus Superadmin)
     */
    public function import()
    {
        $scope = $this->getScope();
        if ($scope['role'] !== 'superadmin') {
            return redirect()->to(base_url('admin/pemuda'))
                             ->with('error', 'Fitur import data hanya dapat diakses oleh Super Administrator.');
        }

        $cabangCount  = $this->cabangModel->countAllResults();
        $wilayahCount = $this->wilayahModel->countAllResults();

        $data = [
            'title'        => 'Import Data Pemuda dari Excel',
            'user'         => session()->get(),
            'cabangCount'  => $cabangCount,
            'wilayahCount' => $wilayahCount,
        ];

        return view('admin/pemuda/import', $data);
    }

    /**
     * Unduh Template Excel Standar Import Pemuda
     */
    public function templateImport()
    {
        $scope = $this->getScope();
        if ($scope['role'] !== 'superadmin') {
            return redirect()->to(base_url('admin/pemuda'))
                             ->with('error', 'Akses ditolak. Fitur ini hanya untuk Super Administrator.');
        }

        $service     = new PemudaImportService();
        $spreadsheet = $service->generateTemplate();

        $filename = 'Template_Import_Pemuda_MTA_Sragen_' . date('Ymd') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Proses Upload dan Import File Excel / CSV
     */
    public function prosesImport()
    {
        $scope = $this->getScope();
        if ($scope['role'] !== 'superadmin') {
            return redirect()->to(base_url('admin/pemuda'))
                             ->with('error', 'Fitur import data hanya dapat diakses oleh Super Administrator.');
        }

        $validationRules = [
            'file_excel' => [
                'label' => 'File Excel / CSV',
                'rules' => 'uploaded[file_excel]|max_size[file_excel,10240]|ext_in[file_excel,xlsx,xls,csv]',
                'errors' => [
                    'uploaded' => 'Silakan pilih file Excel (.xlsx, .xls) atau CSV terlebih dahulu.',
                    'max_size' => 'Ukuran file maksimal adalah 10MB.',
                    'ext_in'   => 'Format file harus berupa .xlsx, .xls, atau .csv.',
                ],
            ],
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->validator->getErrors())
                             ->with('error', 'Gagal memproses file unggahan. Silakan periksa format file.');
        }

        $file = $this->request->getFile('file_excel');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid atau terjadi kesalahan saat mengunggah.');
        }

        $tempPath = $file->getTempName();

        $options = [
            'default_verifikasi' => $this->request->getPost('default_verifikasi') ?: 'verified',
            'skip_errors'        => (bool) $this->request->getPost('skip_errors'),
        ];

        $service = new PemudaImportService();
        $result  = $service->importFile($tempPath, (int) session()->get('user_id'), $options);

        if (!$result['success']) {
            $redirect = redirect()->back()->withInput()->with('error', $result['message']);
            if (!empty($result['errors'])) {
                $redirect->with('import_errors', $result['errors']);
            }
            return $redirect;
        }

        $msg = "Import data pemuda selesai: {$result['imported']} data pemuda berhasil diimport ke sistem.";
        if (!empty($result['skipped'])) {
            $msg .= " ({$result['skipped']} baris dilewati karena terdapat kesalahan data).";
        }

        $redirect = redirect()->to(base_url('admin/pemuda'))->with('success', $msg);
        if (!empty($result['errors'])) {
            $redirect->with('import_warnings', $result['errors']);
        }
        return $redirect;
    }
}
