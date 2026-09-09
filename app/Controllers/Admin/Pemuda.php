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
use App\Services\PemudaExportService;
use App\Services\PemudaBackupService;
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
        if (in_array($scope['role'], ['admin_wilayah', 'admin_wilayah_pemuda'], true)) {
            $filters['wilayah_id'] = $scope['wilayah_id'];
        } elseif (in_array($scope['role'], ['admin_cabang', 'admin_pemuda', 'admin_pemudi'], true)) {
            $filters['wilayah_id'] = $scope['wilayah_id'];
            $filters['cabang_id']  = $scope['cabang_id'];
        }

        // Lock gender filter for gender-scoped roles
        if (in_array($scope['role'], ['admin_wilayah_pemuda', 'admin_pemuda'], true)) {
            $filters['gender'] = 'L';
        } elseif ($scope['role'] === 'admin_pemudi') {
            $filters['gender'] = 'P';
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
        if (in_array($scope['role'], ['admin_wilayah', 'admin_wilayah_pemuda', 'admin_cabang', 'admin_pemuda', 'admin_pemudi'], true) && !empty($scope['wilayah_id'])) {
            $wilayahBuilder->where('id', (int) $scope['wilayah_id']);
        }
        $wilayahList = $wilayahBuilder->findAll();
        
        $cabangBuilder = $this->cabangModel->orderBy('name', 'ASC');
        if (in_array($scope['role'], ['admin_cabang', 'admin_pemuda', 'admin_pemudi'], true) && !empty($scope['cabang_id'])) {
            $cabangBuilder->where('id', (int) $scope['cabang_id']);
        } elseif (in_array($scope['role'], ['admin_wilayah', 'admin_wilayah_pemuda'], true) && !empty($scope['wilayah_id'])) {
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

        $filterWilayahId = in_array($scope['role'], ['admin_wilayah', 'admin_wilayah_pemuda', 'admin_cabang', 'admin_pemuda', 'admin_pemudi'], true) ? (int) $scope['wilayah_id'] : null;
        $filterCabangId  = in_array($scope['role'], ['admin_cabang', 'admin_pemuda', 'admin_pemudi'], true) ? (int) $scope['cabang_id'] : null;

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

        if (!$this->validate($rules)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->validator->getErrors())
                             ->with('error', 'Silakan lengkapi kolom yang wajib diisi.');
        }

        $cabangId = (int) $this->request->getPost('cabang_id');

        // Scope check for cabang-level or wilayah-level roles
        if (in_array($scope['role'], ['admin_cabang', 'admin_pemuda', 'admin_pemudi'], true) && $cabangId !== (int) $scope['cabang_id']) {
            return redirect()->back()->withInput()->with('error', 'Anda hanya dapat mendaftarkan pemuda pada cabang Anda sendiri.');
        }

        if (in_array($scope['role'], ['admin_wilayah', 'admin_wilayah_pemuda'], true)) {
            $targetCabang = $this->cabangModel->find($cabangId);
            if (!$targetCabang || (int) $targetCabang['wilayah_id'] !== (int) $scope['wilayah_id']) {
                return redirect()->back()->withInput()->with('error', 'Anda hanya dapat mendaftarkan pemuda pada cabang dalam wilayah Anda.');
            }
        }

        $name         = (string) $this->request->getPost('name');
        $birthDate    = (string) $this->request->getPost('birth_date');
        $gender       = (string) $this->request->getPost('gender');

        // Gender check based on role
        if (in_array($scope['role'], ['admin_wilayah_pemuda', 'admin_pemuda'], true) && $gender !== 'L') {
            return redirect()->back()->withInput()->with('error', 'Role Anda hanya diizinkan mendaftarkan data pemuda berjenis kelamin Laki-laki.');
        }
        if ($scope['role'] === 'admin_pemudi' && $gender !== 'P') {
            return redirect()->back()->withInput()->with('error', 'Role Anda hanya diizinkan mendaftarkan data pemuda berjenis kelamin Perempuan.');
        }

        $phone        = trim((string) $this->request->getPost('phone'));
        $mtaWargaUuid = toLowerTrim($this->request->getPost('mta_warga_uuid'));

        // Status verifikasi hanya ada 2 (terverifikasi / pending), otomatis berdasarkan sinkronisasi pusat
        // Tidak dapat diubah manual oleh superadmin maupun admin cabang
        $syncService = new \App\Services\MtaSyncService();
        $verifyCheck = $syncService->verifyYouthAgainstMta([
            'name'           => $name,
            'phone'          => $phone,
            'birth_date'     => $birthDate,
            'gender'         => $gender,
            'cabang_id'      => $cabangId,
            'mta_warga_uuid' => $mtaWargaUuid,
        ]);

        $statusVerifikasi = $verifyCheck['verified'] ? 'verified' : 'pending';
        $mtaWargaUuid     = $verifyCheck['warga']['uuid'] ?? ($mtaWargaUuid ?: null);
        $mtaStatusWarga   = $verifyCheck['warga']['status'] ?? null;
        $mtaSyncedAt      = $verifyCheck['verified'] ? date('Y-m-d H:i:s') : null;
        $mtaFotoUrl       = $verifyCheck['warga']['foto'] ?? null;
        $mtaAyahUuid      = $verifyCheck['warga']['ayah_uuid'] ?? null;
        $mtaIbuUuid       = $verifyCheck['warga']['ibu_uuid'] ?? null;

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
            $regNumber = $this->pemudaModel->generateRegistrationNumber((int) $cabangId, $birthDate);

            // Upload Foto Profil jika diunggah
            $fotoFile    = $this->request->getFile('foto');
            $newFotoName = null;
            if ($fotoFile && $fotoFile->isValid() && !$fotoFile->hasMoved()) {
                $uploadDir = FCPATH . 'uploads/pemuda';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $newFotoName = $fotoFile->getRandomName();
                $fotoFile->move($uploadDir, $newFotoName);
            }

            // 1. Insert Pemuda
            $pemudaData = [
                'cabang_id'           => $cabangId,
                'registration_number' => $regNumber,
                'name'                => mb_strtolower(trim($name), 'UTF-8'),
                'gender'              => $gender,
                'marital_status'      => toLowerTrim($this->request->getPost('marital_status')) ?: 'belum_menikah',
                'blood_type'          => toLowerTrim($this->request->getPost('blood_type')),
                'birth_place'         => toLowerTrim($this->request->getPost('birth_place')) ?: 'sragen',
                'birth_date'          => $birthDate,
                'phone'               => $phone,
                'email'               => toLowerTrim($this->request->getPost('email')),
                'status_verifikasi'   => $statusVerifikasi,
                'status_data'         => toLowerTrim($this->request->getPost('status_data')) ?: 'active',
                'mta_warga_uuid'      => $mtaWargaUuid,
                'mta_status_warga'    => $mtaStatusWarga,
                'mta_synced_at'       => $mtaSyncedAt,
                'mta_foto_url'        => $mtaFotoUrl,
                'foto'                => $newFotoName,
                'mta_ayah_uuid'       => $mtaAyahUuid,
                'mta_ibu_uuid'        => $mtaIbuUuid,
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
                'dusun'          => toLowerTrim($this->request->getPost('dusun')),
                'rt'             => toLowerTrim($this->request->getPost('rt')),
                'rw'             => toLowerTrim($this->request->getPost('rw')),
                'address_detail' => mb_strtolower(trim((string) $this->request->getPost('address_detail')), 'UTF-8'),
            ]);

            // 3. Insert Pendidikan
            $this->pendidikanModel->insert([
                'pemuda_id'          => $pemudaId,
                'education_level_id' => (int) $this->request->getPost('education_level_id'),
                'school_name'        => mb_strtolower(trim((string) $this->request->getPost('school_name')), 'UTF-8'),
                'major'              => toLowerTrim($this->request->getPost('major')),
                'education_status'   => toLowerTrim($this->request->getPost('education_status')) ?: 'lulus',
                'graduation_year'    => $this->request->getPost('graduation_year') ? (int) $this->request->getPost('graduation_year') : null,
            ]);

            // 4. Insert Pekerjaan
            $adminJobStatusId = (int) $this->request->getPost('job_status_id');
            $adminBizName     = toLowerTrim($this->request->getPost('business_name'));
            $adminBizField    = toLowerTrim($this->request->getPost('business_field'));
            $adminBizAddress  = toLowerTrim($this->request->getPost('business_address'));
            $adminBizContact  = toLowerTrim($this->request->getPost('business_contact'));
            $adminBizSocial   = toLowerTrim($this->request->getPost('business_social'));
            $adminJobTitle    = toLowerTrim($this->request->getPost('job_title'));
            $adminCompName    = toLowerTrim($this->request->getPost('company_name'));

            if (($adminJobStatusId === 5 || !empty($adminBizName))) {
                if (empty($adminCompName) && !empty($adminBizName)) {
                    $adminCompName = $adminBizName;
                }
                if (empty($adminJobTitle)) {
                    $adminJobTitle = 'owner / pengelola usaha';
                }
            }

            $this->pekerjaanModel->insert([
                'pemuda_id'        => $pemudaId,
                'job_status_id'    => $adminJobStatusId,
                'job_title'        => $adminJobTitle,
                'company_name'     => $adminCompName,
                'business_field'   => $adminBizField,
                'business_name'    => $adminBizName,
                'business_address' => $adminBizAddress,
                'business_contact' => $adminBizContact,
                'business_social'  => $adminBizSocial,
            ]);

            // 5. Insert Organisasi
            $organizations = $this->request->getPost('organizations');
            if (!empty($organizations) && is_array($organizations)) {
                foreach ($organizations as $orgKey => $org) {
                    if (!empty($org['selected'])) {
                        $orgName = !empty($org['name']) ? $org['name'] : (is_string($org['selected']) ? $org['selected'] : ucfirst($orgKey));

                        $this->organisasiModel->insert([
                            'pemuda_id'         => $pemudaId,
                            'organization_name' => mb_strtolower(trim($orgName), 'UTF-8'),
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
                            'organization_name' => mb_strtolower(trim($name), 'UTF-8'),
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

        $filterWilayahId = in_array($scope['role'], ['admin_wilayah', 'admin_wilayah_pemuda', 'admin_cabang', 'admin_pemuda', 'admin_pemudi'], true) ? (int) $scope['wilayah_id'] : null;
        $filterCabangId  = in_array($scope['role'], ['admin_cabang', 'admin_pemuda', 'admin_pemudi'], true) ? (int) $scope['cabang_id'] : null;

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
                        'selected' => true,
                        'name'     => $o['organization_name'],
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

        if (!$this->validate($rules)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->validator->getErrors())
                             ->with('error', 'Silakan periksa input form Anda.');
        }

        $cabangId = (int) $this->request->getPost('cabang_id');

        if (in_array($scope['role'], ['admin_cabang', 'admin_pemuda', 'admin_pemudi'], true) && $cabangId !== (int) $scope['cabang_id']) {
            return redirect()->back()->withInput()->with('error', 'Anda hanya dapat mengelola pemuda pada cabang Anda sendiri.');
        }

        if (in_array($scope['role'], ['admin_wilayah', 'admin_wilayah_pemuda'], true)) {
            $targetCabang = $this->cabangModel->find($cabangId);
            if (!$targetCabang || (int) $targetCabang['wilayah_id'] !== (int) $scope['wilayah_id']) {
                return redirect()->back()->withInput()->with('error', 'Anda hanya dapat mengelola pemuda pada cabang dalam wilayah Anda.');
            }
        }

        $name         = (string) $this->request->getPost('name');
        $birthDate    = (string) $this->request->getPost('birth_date');
        $gender       = (string) $this->request->getPost('gender');

        // Gender check based on role
        if (in_array($scope['role'], ['admin_wilayah_pemuda', 'admin_pemuda'], true) && $gender !== 'L') {
            return redirect()->back()->withInput()->with('error', 'Role Anda hanya diizinkan mengelola data pemuda berjenis kelamin Laki-laki.');
        }
        if ($scope['role'] === 'admin_pemudi' && $gender !== 'P') {
            return redirect()->back()->withInput()->with('error', 'Role Anda hanya diizinkan mengelola data pemuda berjenis kelamin Perempuan.');
        }

        $phone        = trim((string) $this->request->getPost('phone'));
        $mtaWargaUuid = toLowerTrim($this->request->getPost('mta_warga_uuid')) ?: ($existing['mta_warga_uuid'] ?? null);

        // Status verifikasi hanya ada 2 (terverifikasi / pending), otomatis berdasarkan sinkronisasi pusat
        // Tidak dapat diubah manual oleh superadmin maupun admin cabang
        $syncService = new \App\Services\MtaSyncService();
        $verifyCheck = $syncService->verifyYouthAgainstMta([
            'name'           => $name,
            'phone'          => $phone,
            'birth_date'     => $birthDate,
            'gender'         => $gender,
            'cabang_id'      => $cabangId,
            'mta_warga_uuid' => $mtaWargaUuid,
        ]);

        $statusVerifikasi = $verifyCheck['verified'] ? 'verified' : 'pending';
        $mtaWargaUuid     = $verifyCheck['warga']['uuid'] ?? ($mtaWargaUuid ?: null);
        $mtaStatusWarga   = $verifyCheck['warga']['status'] ?? ($existing['mta_status_warga'] ?? null);
        $mtaSyncedAt      = $verifyCheck['verified'] ? date('Y-m-d H:i:s') : ($existing['mta_synced_at'] ?? null);
        $mtaFotoUrl       = $verifyCheck['warga']['foto'] ?? ($existing['mta_foto_url'] ?? null);
        $mtaAyahUuid      = $verifyCheck['warga']['ayah_uuid'] ?? ($existing['mta_ayah_uuid'] ?? null);
        $mtaIbuUuid       = $verifyCheck['warga']['ibu_uuid'] ?? ($existing['mta_ibu_uuid'] ?? null);

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
            // Upload Foto Profil jika diunggah
            $fotoFile    = $this->request->getFile('foto');
            $newFotoName = null;
            if ($fotoFile && $fotoFile->isValid() && !$fotoFile->hasMoved()) {
                $uploadDir = FCPATH . 'uploads/pemuda';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $newFotoName = $fotoFile->getRandomName();
                $fotoFile->move($uploadDir, $newFotoName);
            }

            // 1. Update Pemuda
            $pemudaData = [
                'cabang_id'         => $cabangId,
                'name'              => mb_strtolower(trim($name), 'UTF-8'),
                'gender'            => $gender,
                'marital_status'    => toLowerTrim($this->request->getPost('marital_status')) ?: 'belum_menikah',
                'blood_type'        => toLowerTrim($this->request->getPost('blood_type')),
                'birth_place'       => toLowerTrim($this->request->getPost('birth_place')) ?: 'sragen',
                'birth_date'        => $birthDate,
                'phone'             => $phone,
                'email'             => toLowerTrim($this->request->getPost('email')),
                'status_verifikasi' => $statusVerifikasi,
                'status_data'       => toLowerTrim($this->request->getPost('status_data')) ?: 'active',
                'mta_warga_uuid'    => $mtaWargaUuid,
                'mta_status_warga'  => $mtaStatusWarga,
                'mta_synced_at'     => $mtaSyncedAt,
                'mta_foto_url'      => $mtaFotoUrl,
                'mta_ayah_uuid'     => $mtaAyahUuid,
                'mta_ibu_uuid'      => $mtaIbuUuid,
            ];

            if ($newFotoName !== null) {
                $pemudaData['foto'] = $newFotoName;
                if (!empty($existing['foto']) && $existing['foto'] !== $newFotoName && file_exists(FCPATH . 'uploads/pemuda/' . $existing['foto'])) {
                    @unlink(FCPATH . 'uploads/pemuda/' . $existing['foto']);
                }
            }

            $this->pemudaModel->update($id, $pemudaData);

            // 2. Update/Insert Alamat
            $alamat = $this->alamatModel->where('pemuda_id', $id)->first();
            $alamatData = [
                'pemuda_id'      => $id,
                'province_id'    => 33,
                'regency_id'     => 3314,
                'district_id'    => (int) $this->request->getPost('district_id'),
                'village_id'     => (int) $this->request->getPost('village_id'),
                'dusun'          => toLowerTrim($this->request->getPost('dusun')),
                'rt'             => toLowerTrim($this->request->getPost('rt')),
                'rw'             => toLowerTrim($this->request->getPost('rw')),
                'address_detail' => mb_strtolower(trim((string) $this->request->getPost('address_detail')), 'UTF-8'),
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
                'school_name'        => mb_strtolower(trim((string) $this->request->getPost('school_name')), 'UTF-8'),
                'major'              => toLowerTrim($this->request->getPost('major')),
                'education_status'   => toLowerTrim($this->request->getPost('education_status')) ?: 'lulus',
                'graduation_year'    => $this->request->getPost('graduation_year') ? (int) $this->request->getPost('graduation_year') : null,
            ];
            if ($pendidikan) {
                $this->pendidikanModel->update($pendidikan['id'], $pendidikanData);
            } else {
                $this->pendidikanModel->insert($pendidikanData);
            }

            // 4. Update/Insert Pekerjaan
            $adminJobStatusId = (int) $this->request->getPost('job_status_id');
            $adminBizName     = toLowerTrim($this->request->getPost('business_name'));
            $adminBizField    = toLowerTrim($this->request->getPost('business_field'));
            $adminBizAddress  = toLowerTrim($this->request->getPost('business_address'));
            $adminBizContact  = toLowerTrim($this->request->getPost('business_contact'));
            $adminBizSocial   = toLowerTrim($this->request->getPost('business_social'));
            $adminJobTitle    = toLowerTrim($this->request->getPost('job_title'));
            $adminCompName    = toLowerTrim($this->request->getPost('company_name'));

            if (($adminJobStatusId === 5 || !empty($adminBizName))) {
                if (empty($adminCompName) && !empty($adminBizName)) {
                    $adminCompName = $adminBizName;
                }
                if (empty($adminJobTitle)) {
                    $adminJobTitle = 'owner / pengelola usaha';
                }
            }

            $pekerjaan = $this->pekerjaanModel->where('pemuda_id', $id)->first();
            $pekerjaanData = [
                'pemuda_id'        => $id,
                'job_status_id'    => $adminJobStatusId,
                'job_title'        => $adminJobTitle,
                'company_name'     => $adminCompName,
                'business_field'   => $adminBizField,
                'business_name'    => $adminBizName,
                'business_address' => $adminBizAddress,
                'business_contact' => $adminBizContact,
                'business_social'  => $adminBizSocial,
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
                        $orgName = !empty($org['name']) ? $org['name'] : (is_string($org['selected']) ? $org['selected'] : ucfirst($orgKey));

                        $this->organisasiModel->insert([
                            'pemuda_id'         => $id,
                            'organization_name' => mb_strtolower(trim($orgName), 'UTF-8'),
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
                            'organization_name' => mb_strtolower(trim($name), 'UTF-8'),
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
     * Cek & Sinkronkan Status Verifikasi Pemuda dengan Database MTA Pusat
     * Status verifikasi tidak dapat diubah secara manual, melainkan otomatis berdasarkan sinkronisasi API MTA Pusat.
     */
    public function verifikasi(int $id)
    {
        $scope = $this->getScope();
        $pemuda = $this->pemudaModel->getPemudaDetail($id, $scope);

        if (!$pemuda) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status'  => 'error', 
                    'message' => 'Data pemuda tidak ditemukan atau Anda tidak memiliki akses.'
                ])->setStatusCode(404);
            }
            return redirect()->back()->with('error', 'Data pemuda tidak ditemukan atau Anda tidak memiliki akses.');
        }

        // Lakukan sinkronisasi dan verifikasi otomatis terhadap database MTA Pusat
        $syncService = new \App\Services\MtaSyncService();
        $verifyCheck = $syncService->verifyYouthAgainstMta([
            'name'           => $pemuda['name'],
            'phone'          => $pemuda['phone'],
            'birth_date'     => $pemuda['birth_date'],
            'gender'         => $pemuda['gender'],
            'cabang_id'      => $pemuda['cabang_id'],
            'mta_warga_uuid' => $pemuda['mta_warga_uuid'] ?? null,
        ]);

        $newStatus    = $verifyCheck['verified'] ? 'verified' : 'pending';
        $mtaWargaUuid = $verifyCheck['warga']['uuid'] ?? ($pemuda['mta_warga_uuid'] ?? null);

        $updateData = [
            'status_verifikasi' => $newStatus,
            'mta_warga_uuid'    => $mtaWargaUuid,
            'mta_status_warga'  => $verifyCheck['warga']['status'] ?? ($pemuda['mta_status_warga'] ?? null),
            'mta_synced_at'     => $verifyCheck['verified'] ? date('Y-m-d H:i:s') : ($pemuda['mta_synced_at'] ?? null),
            'mta_foto_url'      => $verifyCheck['warga']['foto'] ?? ($pemuda['mta_foto_url'] ?? null),
        ];

        $this->pemudaModel->update($id, $updateData);

        if ($verifyCheck['verified']) {
            $msg = 'Data pemuda "' . esc($pemuda['name']) . '" berhasil disinkronkan dan Terverifikasi dengan Database MTA Pusat.';
        } else {
            $msg = 'Data pemuda "' . esc($pemuda['name']) . '" belum cocok / tidak ditemukan di Database MTA Pusat (Status: Belum Terverifikasi).';
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'     => 'success',
                'message'    => $msg,
                'new_status' => $newStatus,
                'is_verified'=> $verifyCheck['verified'],
            ]);
        }

        return redirect()->back()->with($verifyCheck['verified'] ? 'success' : 'warning', $msg);
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
     * Helper untuk mengekstrak dan membatasi filter ekspor sesuai role user
     */
    protected function extractExportFilters(): array
    {
        $scope = $this->getScope();

        $skillId = $this->request->getVar('skill_id');
        if (is_array($skillId)) {
            $skillId = array_values(array_filter(array_map('intval', $skillId)));
            if (empty($skillId)) {
                $skillId = null;
            }
        } elseif (!empty($skillId)) {
            $skillId = (int) $skillId;
        } else {
            $skillId = null;
        }

        $interestId = $this->request->getVar('interest_id');
        if (is_array($interestId)) {
            $interestId = array_values(array_filter(array_map('intval', $interestId)));
            if (empty($interestId)) {
                $interestId = null;
            }
        } elseif (!empty($interestId)) {
            $interestId = (int) $interestId;
        } else {
            $interestId = null;
        }

        $filters = [
            'search'             => trim((string) $this->request->getVar('search')),
            'wilayah_id'         => $this->request->getVar('wilayah_id') ? (int) $this->request->getVar('wilayah_id') : null,
            'cabang_id'          => $this->request->getVar('cabang_id') ? (int) $this->request->getVar('cabang_id') : null,
            'gender'             => $this->request->getVar('gender') ?: null,
            'marital_status'     => $this->request->getVar('marital_status') ?: null,
            'blood_type'         => $this->request->getVar('blood_type') ?: null,
            'status_verifikasi'  => $this->request->getVar('status_verifikasi') ?: null,
            'status_data'        => $this->request->getVar('status_data') ?: 'active',
            'education_level_id' => $this->request->getVar('education_level_id') ? (int) $this->request->getVar('education_level_id') : null,
            'job_status_id'      => $this->request->getVar('job_status_id') ? (int) $this->request->getVar('job_status_id') : null,
            'skill_id'           => $skillId,
            'interest_id'        => $interestId,
            'organization_name'  => trim((string) $this->request->getVar('organization_name')),
            'min_age'            => ($this->request->getVar('min_age') !== null && $this->request->getVar('min_age') !== '') ? (int) $this->request->getVar('min_age') : null,
            'max_age'            => ($this->request->getVar('max_age') !== null && $this->request->getVar('max_age') !== '') ? (int) $this->request->getVar('max_age') : null,
            'start_date'         => $this->request->getVar('start_date') ?: null,
            'end_date'           => $this->request->getVar('end_date') ?: null,
        ];

        // Enforce scope on export filters
        if (in_array($scope['role'], ['admin_wilayah', 'admin_wilayah_pemuda'], true)) {
            $filters['wilayah_id'] = (int) $scope['wilayah_id'];
        } elseif (in_array($scope['role'], ['admin_cabang', 'admin_pemuda', 'admin_pemudi'], true)) {
            $filters['wilayah_id'] = (int) $scope['wilayah_id'];
            $filters['cabang_id']  = (int) $scope['cabang_id'];
        }

        // Enforce gender lock for export
        if (in_array($scope['role'], ['admin_wilayah_pemuda', 'admin_pemuda'], true)) {
            $filters['gender'] = 'L';
        } elseif ($scope['role'] === 'admin_pemudi') {
            $filters['gender'] = 'P';
        }

        if (isset($filters['status_data']) && $filters['status_data'] === 'all') {
            unset($filters['status_data']);
        }

        return array_filter($filters, function ($v) {
            return $v !== null && $v !== '';
        });
    }

    /**
     * Halaman Menu Export Data Pemuda Kustom (Pilihan Elemen, Bakat, Minat, Filter)
     */
    public function export()
    {
        // Jika request meminta unduhan langsung via GET (download=1 atau action=download)
        if ($this->request->getGet('download') || $this->request->getGet('action') === 'download') {
            return $this->exportDownload();
        }

        $scope = $this->getScope();
        $filters = $this->extractExportFilters();

        // Wilayah list (disesuaikan dengan scope)
        $wilayahBuilder = $this->wilayahModel->orderBy('code', 'ASC');
        if (in_array($scope['role'], ['admin_wilayah', 'admin_wilayah_pemuda', 'admin_cabang', 'admin_pemuda', 'admin_pemudi'], true) && !empty($scope['wilayah_id'])) {
            $wilayahBuilder->where('id', (int) $scope['wilayah_id']);
        }
        $wilayahList = $wilayahBuilder->findAll();

        // Cabang list (disesuaikan dengan scope)
        $cabangBuilder = $this->cabangModel->orderBy('name', 'ASC');
        if (in_array($scope['role'], ['admin_cabang', 'admin_pemuda', 'admin_pemudi'], true) && !empty($scope['cabang_id'])) {
            $cabangBuilder->where('id', (int) $scope['cabang_id']);
        } elseif (in_array($scope['role'], ['admin_wilayah', 'admin_wilayah_pemuda'], true) && !empty($scope['wilayah_id'])) {
            $cabangBuilder->where('wilayah_id', (int) $scope['wilayah_id']);
        } elseif (!empty($filters['wilayah_id'])) {
            $cabangBuilder->where('wilayah_id', (int) $filters['wilayah_id']);
        }
        $cabangList = $cabangBuilder->findAll();

        // Master Bakat & Minat
        $skillModel = new SkillModel();
        $skills = $skillModel->orderBy('name', 'ASC')->findAll();

        $interestModel = new InterestModel();
        $interests = $interestModel->orderBy('name', 'ASC')->findAll();

        // Master Pendidikan & Pekerjaan
        $educationLevelModel = new EducationLevelModel();
        $educationLevels = $educationLevelModel->orderBy('id', 'ASC')->findAll();

        $jobStatusModel = new JobStatusModel();
        $jobStatuses = $jobStatusModel->orderBy('id', 'ASC')->findAll();

        // Hitung estimasi data awal
        $exportService = new PemudaExportService();
        $initialCount = $exportService->countFiltered($filters, $scope);

        return view('admin/pemuda/export', [
            'title'              => 'Export Kustom Data Pemuda',
            'scope'              => $scope,
            'wilayahList'        => $wilayahList,
            'cabangList'         => $cabangList,
            'skills'             => $skills,
            'interests'          => $interests,
            'educationLevels'    => $educationLevels,
            'jobStatuses'        => $jobStatuses,
            'categorizedColumns' => PemudaExportService::CATEGORIZED_COLUMNS,
            'presets'            => PemudaExportService::PRESETS,
            'filters'            => $filters,
            'initialCount'       => $initialCount,
        ]);
    }

    /**
     * Proses Download Berkas Export (Excel .xlsx / CSV .csv)
     */
    public function exportDownload()
    {
        $scope = $this->getScope();
        $filters = $this->extractExportFilters();

        $columns = $this->request->getVar('columns');
        $preset  = (string) $this->request->getVar('preset');

        if ((!is_array($columns) || empty($columns)) && isset(PemudaExportService::PRESETS[$preset])) {
            $columns = PemudaExportService::PRESETS[$preset];
        } elseif (!is_array($columns) || empty($columns)) {
            $columns = PemudaExportService::PRESETS['default'];
        }

        $format = strtolower((string) ($this->request->getVar('format') ?: 'xlsx'));
        $exportService = new PemudaExportService();

        $timestamp = date('Ymd_His');
        $userName = session()->get('name') ?? session()->get('username') ?? 'Admin';

        if ($format === 'csv') {
            $filename = 'Export_Pemuda_' . $timestamp . '.csv';
            $exportService->streamCsv($filters, $columns, $scope, $filename);
            return;
        }

        // Default: Microsoft Excel (.xlsx)
        $filename = 'Export_Pemuda_' . $timestamp . '.xlsx';
        $spreadsheet = $exportService->generateXlsx($filters, $columns, $scope, [
            'user_name' => $userName,
        ]);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * AJAX Endpoint: Hitung jumlah data yang sesuai filter secara real-time
     */
    public function exportCount()
    {
        $scope = $this->getScope();
        $filters = $this->extractExportFilters();

        $exportService = new PemudaExportService();
        $count = $exportService->countFiltered($filters, $scope);

        return $this->response->setJSON([
            'success' => true,
            'count'   => $count,
        ]);
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
                'rules' => 'uploaded[file_excel]|max_size[file_excel,10240]|ext_in[file_excel,xlsx,xls,csv]|mime_in[file_excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,text/csv,text/plain,application/csv,text/x-csv,application/x-csv,text/comma-separated-values,text/x-comma-separated-values,application/octet-stream]',
                'errors' => [
                    'uploaded' => 'Silakan pilih file Excel (.xlsx, .xls) atau CSV terlebih dahulu.',
                    'max_size' => 'Ukuran file maksimal adalah 10MB.',
                    'ext_in'   => 'Format file harus berupa .xlsx, .xls, atau .csv.',
                    'mime_in'  => 'Tipe konten file tidak valid untuk format spreadsheet.',
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
            'default_verifikasi' => 'pending',
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
        $skipNotes = [];
        if (!empty($result['skipped_duplicates'])) {
            $skipNotes[] = "{$result['skipped_duplicates']} data duplikat dilewati";
        }
        $otherSkipped = ($result['skipped'] ?? 0) - ($result['skipped_duplicates'] ?? 0);
        if ($otherSkipped > 0) {
            $skipNotes[] = "{$otherSkipped} baris kesalahan dilewati";
        }
        if (!empty($skipNotes)) {
            $msg .= " (" . implode(', ', $skipNotes) . ").";
        }

        $redirect = redirect()->to(base_url('admin/pemuda'))->with('success', $msg);
        if (!empty($result['errors'])) {
            $redirect->with('import_warnings', $result['errors']);
        }
        return $redirect;
    }

    /**
     * Halaman Backup & Pemeliharaan Data Pemuda (Khusus Superadmin)
     */
    public function backup()
    {
        $scope = $this->getScope();
        if ($scope['role'] !== 'superadmin') {
            return redirect()->to(base_url('admin/pemuda'))
                             ->with('error', 'Akses ditolak. Fitur backup dan pemeliharaan hanya dapat diakses oleh Super Administrator.');
        }

        $backupService = new PemudaBackupService();
        $countsSummary = $backupService->getCountsSummary();
        $statusSummary = $this->pemudaModel->getCountsSummary($scope);
        $backupList    = $backupService->getBackupList();

        $data = [
            'title'         => 'Backup & Pembersihan Data Pemuda',
            'user'          => session()->get(),
            'countsSummary' => $countsSummary,
            'statusSummary' => $statusSummary,
            'backupList'    => $backupList,
            'backupDir'     => $backupService->getBackupDir(),
        ];

        return view('admin/pemuda/backup', $data);
    }

    /**
     * Proses Pembuatan dan Pengunduhan/Penyimpanan Berkas Backup Data Pemuda
     */
    public function generateBackup()
    {
        $scope = $this->getScope();
        if ($scope['role'] !== 'superadmin') {
            return redirect()->to(base_url('admin/pemuda'))
                             ->with('error', 'Akses ditolak. Fitur backup data hanya dapat diakses oleh Super Administrator.');
        }

        $format = strtolower((string) ($this->request->getPost('format') ?: 'sql'));
        $action = strtolower((string) ($this->request->getPost('action') ?: 'download'));

        $backupService = new PemudaBackupService();

        if ($format === 'sql') {
            if ($action === 'save') {
                $result = $backupService->generateSqlBackup(true);
                return redirect()->to(base_url('admin/pemuda/backup'))
                                 ->with('success', "Berkas cadangan database SQL ({$result['filename']}) berhasil dibuat dan disimpan di server.");
            }

            // Langsung unduh ke browser
            $result = $backupService->generateSqlBackup(false);
            return $this->response
                        ->setHeader('Content-Type', 'application/sql')
                        ->setHeader('Content-Disposition', 'attachment; filename="' . $result['filename'] . '"')
                        ->setHeader('Cache-Control', 'max-age=0')
                        ->setBody($result['content']);
        }

        if ($format === 'json') {
            if ($action === 'save') {
                $result = $backupService->generateJsonBackup(true);
                return redirect()->to(base_url('admin/pemuda/backup'))
                                 ->with('success', "Berkas cadangan format JSON ({$result['filename']}) berhasil dibuat dan disimpan di server.");
            }

            // Langsung unduh ke browser
            $result = $backupService->generateJsonBackup(false);
            return $this->response
                        ->setHeader('Content-Type', 'application/json')
                        ->setHeader('Content-Disposition', 'attachment; filename="' . $result['filename'] . '"')
                        ->setHeader('Cache-Control', 'max-age=0')
                        ->setBody($result['content']);
        }

        if ($format === 'xlsx') {
            if ($action === 'save') {
                $result = $backupService->generateXlsxBackup(true);
                return redirect()->to(base_url('admin/pemuda/backup'))
                                 ->with('success', "Berkas cadangan format Excel ({$result['filename']}) berhasil dibuat dan disimpan di server.");
            }

            // Langsung unduh ke browser
            $result = $backupService->generateXlsxBackup(false);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $result['filename'] . '"');
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($result['spreadsheet']);
            $writer->save('php://output');
            exit;
        }

        return redirect()->to(base_url('admin/pemuda/backup'))->with('error', 'Format cadangan tidak valid.');
    }

    /**
     * Unduh Berkas Backup yang Tersimpan di Server
     */
    public function downloadBackup(string $filename)
    {
        $scope = $this->getScope();
        if ($scope['role'] !== 'superadmin') {
            return redirect()->to(base_url('admin/pemuda'))
                             ->with('error', 'Akses ditolak. Fitur ini hanya untuk Super Administrator.');
        }

        $backupService = new PemudaBackupService();
        $path = $backupService->getBackupFilePath($filename);

        if (!$path) {
            return redirect()->to(base_url('admin/pemuda/backup'))
                             ->with('error', 'Berkas cadangan tidak ditemukan atau nama berkas tidak valid.');
        }

        return $this->response->download($path, null);
    }

    /**
     * Hapus Berkas Backup yang Tersimpan di Server
     */
    public function deleteBackupFile(string $filename)
    {
        $scope = $this->getScope();
        if ($scope['role'] !== 'superadmin') {
            return redirect()->to(base_url('admin/pemuda'))
                             ->with('error', 'Akses ditolak. Fitur ini hanya untuk Super Administrator.');
        }

        $backupService = new PemudaBackupService();
        $deleted = $backupService->deleteBackupFile($filename);

        if (!$deleted) {
            return redirect()->to(base_url('admin/pemuda/backup'))
                             ->with('error', 'Gagal menghapus berkas cadangan atau berkas tidak ditemukan.');
        }

        return redirect()->to(base_url('admin/pemuda/backup'))
                         ->with('success', 'Berkas cadangan ' . esc($filename) . ' berhasil dihapus dari server.');
    }

    /**
     * Hapus Seluruh Data Pemuda (Reset Total Data Pemuda - Superadmin Only)
     */
    public function hapusSemua()
    {
        $scope = $this->getScope();
        if ($scope['role'] !== 'superadmin') {
            return redirect()->to(base_url('admin/pemuda'))
                             ->with('error', 'Akses ditolak. Hanya Super Administrator yang berhak menghapus seluruh data pemuda.');
        }

        $rules = [
            'confirm_text' => [
                'label' => 'Teks Konfirmasi',
                'rules' => 'required',
                'errors' => [
                    'required' => 'Kolom konfirmasi wajib diisi.',
                ],
            ],
            'password' => [
                'label' => 'Password Konfirmasi',
                'rules' => 'required',
                'errors' => [
                    'required' => 'Password akun Super Administrator wajib diisi untuk verifikasi keamanan.',
                ],
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->to(base_url('admin/pemuda/backup'))
                             ->with('error', 'Silakan lengkapi konfirmasi teks dan password.');
        }

        $confirmText = trim((string) $this->request->getPost('confirm_text'));
        if ($confirmText !== 'HAPUS SEMUA PEMUDA') {
            return redirect()->to(base_url('admin/pemuda/backup'))
                             ->with('error', 'Teks konfirmasi salah. Harap ketik teks "HAPUS SEMUA PEMUDA" secara persis untuk melanjutkan.');
        }

        $password = (string) $this->request->getPost('password');
        $userId   = (int) session()->get('user_id');

        $backupService = new PemudaBackupService();
        $result = $backupService->deleteAllYouthData($userId, $password);

        if (!$result['success']) {
            return redirect()->to(base_url('admin/pemuda/backup'))
                             ->with('error', $result['message']);
        }

        $deletedPemudaCount = $result['deleted_counts']['pemuda'] ?? 0;
        $msg = "Seluruh data pemuda ({$deletedPemudaCount} pemuda) berhasil dihapus permanen dari sistem.";

        if (!empty($result['auto_backup'])) {
            $msg .= " Salinan cadangan otomatis darurat telah disimpan di server: {$result['auto_backup']}.";
        }

        return redirect()->to(base_url('admin/pemuda/backup'))
                         ->with('success', $msg);
    }
}
