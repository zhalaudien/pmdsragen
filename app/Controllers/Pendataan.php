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
        // 1. Server-side Validation
        $rules = [
            'cabang_id'          => 'required|is_natural_no_zero',
            'name'               => 'required|min_length[3]|max_length[150]',
            'gender'             => 'required|in_list[L,P]',
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

            // 7. Insert Organisasi (Multi rows)
            $organizations = $this->request->getPost('organizations');
            if (!empty($organizations) && is_array($organizations)) {
                foreach ($organizations as $org) {
                    if (!empty($org['name'])) {
                        $this->organisasiModel->insert([
                            'pemuda_id'         => $pemudaId,
                            'organization_name' => $org['name'],
                            'position'          => $org['position'] ?? null,
                            'join_date'         => !empty($org['join_date']) ? $org['join_date'] : null,
                            'end_date'          => !empty($org['end_date']) ? $org['end_date'] : null,
                            'description'       => $org['description'] ?? null,
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
}
