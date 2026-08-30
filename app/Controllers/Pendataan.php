<?php

namespace App\Controllers;

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
use Config\Database;

class Pendataan extends BaseController
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
     * Tampilkan form input data pemuda (Public)
     */
    public function index(): string
    {
        $educationLevelModel = new EducationLevelModel();
        $jobStatusModel      = new JobStatusModel();
        $skillModel          = new SkillModel();
        $interestModel       = new InterestModel();

        // Safely fetch reference data if tables exist
        $wilayahWithCabang = [];
        $educationLevels   = [];
        $jobStatuses       = [];
        $skills            = [];
        $interests         = [];

        try {
            $wilayahWithCabang = $this->wilayahModel->getWithCabang();
            $educationLevels   = $educationLevelModel->findAll();
            $jobStatuses       = $jobStatusModel->findAll();
            $skills            = $skillModel->findAll();
            $interests         = $interestModel->findAll();
        } catch (\Throwable $e) {
            // Fallback gracefully if database table is not seeded yet
        }

        $data = [
            'wilayahList'     => $wilayahWithCabang,
            'educationLevels' => $educationLevels,
            'jobStatuses'     => $jobStatuses,
            'skills'          => $skills,
            'interests'       => $interests,
        ];

        return view('pendataan/form', $data);
    }

    /**
     * Simpan data pendataan pemuda dengan Database Transaction
     */
    public function simpan()
    {
        $throttler = \Config\Services::throttler();
        $ipAddress = $this->request->getIPAddress();
        // Allow max 10 submissions per minute per IP
        if ($throttler->check(md5($ipAddress . '_public_register'), 10, MINUTE) === false) {
            $seconds = $throttler->getTokentime();
            return redirect()->back()
                             ->withInput()
                             ->with('error', "Terlalu banyak permintaan pendaftaran. Demi keamanan, silakan tunggu {$seconds} detik sebelum mencoba kembali.");
        }

        // 1. Server-side Validation
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
                             ->with('error', 'Silakan periksa kembali formulir Anda. Masih ada data yang belum terisi dengan benar.');
        }

        $name      = (string) $this->request->getPost('name');
        $birthDate = (string) $this->request->getPost('birth_date');
        $cabangId  = (int) $this->request->getPost('cabang_id');

        // Autentikasi / Verifikasi Duplikasi Data (Nama, Tanggal Lahir, dan Cabang)
        $duplicate = $this->pemudaModel->findDuplicate($name, $birthDate, $cabangId);
        if ($duplicate) {
            $formattedBirth = date('d/m/Y', strtotime($birthDate));
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Pendaftaran Ditolak: Data pemuda dengan nama <strong>"' . esc($name) . '"</strong>, tanggal lahir <strong>(' . $formattedBirth . ')</strong>, dan cabang yang dipilih sudah terdaftar di sistem. Mohon tidak melakukan input data ganda.');
        }

        $db = Database::connect();
        $db->transStart();

        try {
            // 2. Generate Registration Number
            $regNumber = $this->pemudaModel->generateRegistrationNumber();

            // 3. Insert Pemuda (terikat pada cabang_id)
            $pemudaData = [
                'cabang_id'           => (int) $this->request->getPost('cabang_id'),
                'registration_number' => $regNumber,
                'name'                => $this->request->getPost('name'),
                'gender'              => $this->request->getPost('gender'),
                'marital_status'      => $this->request->getPost('marital_status') ?: 'belum_menikah',
                'blood_type'          => $this->request->getPost('blood_type') ?: null,
                'birth_place'         => $this->request->getPost('birth_place'),
                'birth_date'          => $this->request->getPost('birth_date'),
                'phone'               => $this->request->getPost('phone'),
                'email'               => $this->request->getPost('email') ?: null,
                'status_verifikasi'   => 'pending',
                'status_data'         => 'active',
            ];

            $this->pemudaModel->insert($pemudaData);
            $pemudaId = $this->pemudaModel->getInsertID();

            // 4. Insert Alamat
            $alamatData = [
                'pemuda_id'      => $pemudaId,
                'province_id'    => (int) ($this->request->getPost('province_id') ?: 33),
                'regency_id'     => (int) ($this->request->getPost('regency_id') ?: 3314),
                'district_id'    => (int) $this->request->getPost('district_id'),
                'village_id'     => (int) $this->request->getPost('village_id'),
                'dusun'          => $this->request->getPost('dusun') ?: null,
                'rt'             => $this->request->getPost('rt') ?: null,
                'rw'             => $this->request->getPost('rw') ?: null,
                'address_detail' => $this->request->getPost('address_detail'),
            ];
            $this->alamatModel->insert($alamatData);

            // 5. Insert Pendidikan
            $pendidikanData = [
                'pemuda_id'          => $pemudaId,
                'education_level_id' => (int) $this->request->getPost('education_level_id'),
                'school_name'        => $this->request->getPost('school_name'),
                'major'              => $this->request->getPost('major') ?: null,
                'education_status'   => $this->request->getPost('education_status'),
                'graduation_year'    => $this->request->getPost('graduation_year') ? (int) $this->request->getPost('graduation_year') : null,
            ];
            $this->pendidikanModel->insert($pendidikanData);

            // 6. Insert Pekerjaan
            $pekerjaanData = [
                'pemuda_id'      => $pemudaId,
                'job_status_id'  => (int) $this->request->getPost('job_status_id'),
                'job_title'      => $this->request->getPost('job_title') ?: null,
                'company_name'   => $this->request->getPost('company_name') ?: null,
                'business_field' => $this->request->getPost('business_field') ?: null,
            ];
            $this->pekerjaanModel->insert($pekerjaanData);

            // 7. Insert Organisasi (Pilihan: Satgas, Bankom, Parkir, Pemuda, Tim Ikhrom, & Lainnya)
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

            // 8. Insert Skills
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

            // 9. Insert Interests
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
                return redirect()->back()
                                 ->withInput()
                                 ->with('error', 'Terjadi kesalahan sistem saat menyimpan data pemuda. Silakan coba kembali.');
            }

            return redirect()->to(base_url('pendataan/sukses'))
                             ->with('registration_number', $regNumber)
                             ->with('pemuda_id', $pemudaId);

        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Halaman Sukses Pendataan
     */
    public function sukses(): string
    {
        $regNumber = session()->getFlashdata('registration_number');
        $pemudaId  = session()->getFlashdata('pemuda_id');
        $pemuda    = null;

        if ($pemudaId) {
            $pemuda = $this->pemudaModel->find($pemudaId);
        }

        $data = [
            'registration_number' => $regNumber,
            'pemuda'              => $pemuda,
        ];

        return view('pendataan/sukses', $data);
    }

    /**
     * Endpoint AJAX untuk memeriksa duplikasi data pemuda (Nama, Tanggal Lahir, Cabang)
     */
    public function checkDuplicate()
    {
        $throttler = \Config\Services::throttler();
        $ipAddress = $this->request->getIPAddress();
        // Allow max 30 duplicate checks per minute per IP
        if ($throttler->check(md5($ipAddress . '_check_duplicate'), 30, MINUTE) === false) {
            return $this->response->setJSON([
                'status'    => 'error',
                'duplicate' => false,
                'message'   => 'Terlalu banyak permintaan verifikasi. Silakan tunggu sebentar.',
                'csrfHash'  => csrf_hash(),
            ]);
        }

        $name      = trim((string) $this->request->getPost('name'));
        $birthDate = trim((string) $this->request->getPost('birth_date'));
        $cabangId  = (int) $this->request->getPost('cabang_id');

        if ($name === '' || $birthDate === '' || $cabangId <= 0) {
            return $this->response->setJSON([
                'status'    => 'error',
                'duplicate' => false,
                'message'   => 'Parameter nama, tanggal lahir, dan cabang wajib diisi.',
                'csrfHash'  => csrf_hash(),
            ]);
        }

        $duplicate = $this->pemudaModel->findDuplicate($name, $birthDate, $cabangId);

        if ($duplicate) {
            $formattedBirth = date('d/m/Y', strtotime($birthDate));
            return $this->response->setJSON([
                'status'    => 'duplicate',
                'duplicate' => true,
                'message'   => "Data pemuda dengan nama <strong>\"" . esc($name) . "\"</strong>, tanggal lahir <strong>({$formattedBirth})</strong>, dan cabang yang dipilih sudah terdaftar di sistem. Anda tidak dapat melanjutkan pengisian data untuk mencegah duplikasi.",
                'data'      => [
                    'registration_number' => $duplicate['registration_number'],
                    'name'                => $duplicate['name'],
                ],
                'csrfHash'  => csrf_hash(),
            ]);
        }

        return $this->response->setJSON([
            'status'    => 'success',
            'duplicate' => false,
            'message'   => 'Data belum terdaftar, silakan melanjutkan.',
            'csrfHash'  => csrf_hash(),
        ]);
    }
}
