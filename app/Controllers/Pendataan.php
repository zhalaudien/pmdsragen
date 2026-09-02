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

        $name       = (string) $this->request->getPost('name');
        $gender     = (string) $this->request->getPost('gender');
        $birthDate  = (string) $this->request->getPost('birth_date');
        $cabangId   = (int) $this->request->getPost('cabang_id');
        $existingId = (int) $this->request->getPost('existing_pemuda_id');

        // Check if existing data is present in this cabang
        $existingPemuda = null;
        if ($existingId > 0) {
            $existingPemuda = $this->pemudaModel->find($existingId);
        }
        if (!$existingPemuda) {
            $existingPemuda = $this->pemudaModel->findExistingPemuda($name, $gender, $birthDate, $cabangId);
        }

        $isUpdate = ($existingPemuda !== null);

        $db = Database::connect();
        $db->transStart();

        try {
            // 2. Verifikasi Otomatis dengan Database Warga MTA Pusat (Perwakilan Sragen)
            $syncService  = new \App\Services\MtaSyncService();
            $verifyCheck  = $syncService->verifyYouthAgainstMta([
                'name'           => $name,
                'phone'          => $this->request->getPost('phone'),
                'birth_date'     => $birthDate,
                'gender'         => $gender,
                'cabang_id'      => $cabangId,
                'mta_warga_uuid' => $this->request->getPost('mta_warga_uuid') ?: ($existingPemuda['mta_warga_uuid'] ?? null),
            ]);

            $statusVerifikasi = $verifyCheck['verified'] ? 'verified' : 'pending';
            $mtaWargaUuid     = $verifyCheck['warga']['uuid'] ?? ($this->request->getPost('mta_warga_uuid') ?: ($existingPemuda['mta_warga_uuid'] ?? null));
            $mtaStatusWarga   = $verifyCheck['warga']['status'] ?? ($existingPemuda['mta_status_warga'] ?? null);
            $mtaSyncedAt      = $verifyCheck['verified'] ? date('Y-m-d H:i:s') : ($existingPemuda['mta_synced_at'] ?? null);
            $mtaFotoUrl       = $verifyCheck['warga']['foto'] ?? ($existingPemuda['mta_foto_url'] ?? null);
            $mtaAyahUuid      = $verifyCheck['warga']['ayah_uuid'] ?? ($existingPemuda['mta_ayah_uuid'] ?? null);
            $mtaIbuUuid       = $verifyCheck['warga']['ibu_uuid'] ?? ($existingPemuda['mta_ibu_uuid'] ?? null);

            $pemudaData = [
                'cabang_id'         => $cabangId,
                'name'              => mb_strtolower(trim($name), 'UTF-8'),
                'gender'            => $gender,
                'marital_status'    => toLowerTrim($this->request->getPost('marital_status')) ?: 'belum_menikah',
                'blood_type'        => toLowerTrim($this->request->getPost('blood_type')),
                'birth_place'       => toLowerTrim($this->request->getPost('birth_place')) ?: 'sragen',
                'birth_date'        => $birthDate,
                'phone'             => trim((string) $this->request->getPost('phone')),
                'email'             => toLowerTrim($this->request->getPost('email')),
                'status_verifikasi' => $statusVerifikasi,
                'status_data'       => 'active',
                'mta_warga_uuid'    => $mtaWargaUuid ? mb_strtolower(trim($mtaWargaUuid), 'UTF-8') : null,
                'mta_status_warga'  => $mtaStatusWarga ? mb_strtolower(trim($mtaStatusWarga), 'UTF-8') : null,
                'mta_ayah_uuid'     => $mtaAyahUuid ? mb_strtolower(trim($mtaAyahUuid), 'UTF-8') : null,
                'mta_ibu_uuid'      => $mtaIbuUuid ? mb_strtolower(trim($mtaIbuUuid), 'UTF-8') : null,
                'mta_foto_url'      => $mtaFotoUrl,
                'mta_synced_at'     => $mtaSyncedAt,
            ];

            if ($isUpdate) {
                // 3a. Update data pemuda yang sudah ada
                $pemudaId  = (int) $existingPemuda['id'];
                $regNumber = $existingPemuda['registration_number'];

                $this->pemudaModel->update($pemudaId, $pemudaData);
            } else {
                // 3b. Generate Registration Number baru dan insert pemuda
                $regNumber = $this->pemudaModel->generateRegistrationNumber((int) $cabangId, $birthDate);
                $pemudaData['registration_number'] = $regNumber;

                $this->pemudaModel->insert($pemudaData);
                $pemudaId = $this->pemudaModel->getInsertID();
            }

            // 4. Insert / Update Alamat
            $alamatData = [
                'pemuda_id'      => $pemudaId,
                'province_id'    => (int) ($this->request->getPost('province_id') ?: 33),
                'regency_id'     => (int) ($this->request->getPost('regency_id') ?: 3314),
                'district_id'    => (int) $this->request->getPost('district_id'),
                'village_id'     => (int) $this->request->getPost('village_id'),
                'dusun'          => toLowerTrim($this->request->getPost('dusun')),
                'rt'             => toLowerTrim($this->request->getPost('rt')),
                'rw'             => toLowerTrim($this->request->getPost('rw')),
                'address_detail' => mb_strtolower(trim((string) $this->request->getPost('address_detail')), 'UTF-8'),
            ];

            $existingAlamat = $this->alamatModel->where('pemuda_id', $pemudaId)->first();
            if ($existingAlamat) {
                $this->alamatModel->update($existingAlamat['id'], $alamatData);
            } else {
                $this->alamatModel->insert($alamatData);
            }

            // 5. Insert / Update Pendidikan
            $pendidikanData = [
                'pemuda_id'          => $pemudaId,
                'education_level_id' => (int) $this->request->getPost('education_level_id'),
                'school_name'        => mb_strtolower(trim((string) $this->request->getPost('school_name')), 'UTF-8'),
                'major'              => toLowerTrim($this->request->getPost('major')),
                'education_status'   => toLowerTrim($this->request->getPost('education_status')) ?: 'lulus',
                'graduation_year'    => $this->request->getPost('graduation_year') ? (int) $this->request->getPost('graduation_year') : null,
            ];

            $existingPendidikan = $this->pendidikanModel->where('pemuda_id', $pemudaId)->first();
            if ($existingPendidikan) {
                $this->pendidikanModel->update($existingPendidikan['id'], $pendidikanData);
            } else {
                $this->pendidikanModel->insert($pendidikanData);
            }

            // 6. Insert / Update Pekerjaan
            $pekerjaanData = [
                'pemuda_id'      => $pemudaId,
                'job_status_id'  => (int) $this->request->getPost('job_status_id'),
                'job_title'      => toLowerTrim($this->request->getPost('job_title')),
                'company_name'   => toLowerTrim($this->request->getPost('company_name')),
                'business_field' => toLowerTrim($this->request->getPost('business_field')),
            ];

            $existingPekerjaan = $this->pekerjaanModel->where('pemuda_id', $pemudaId)->first();
            if ($existingPekerjaan) {
                $this->pekerjaanModel->update($existingPekerjaan['id'], $pekerjaanData);
            } else {
                $this->pekerjaanModel->insert($pekerjaanData);
            }

            // 7. Organisasi (Reset & Re-insert)
            $this->organisasiModel->where('pemuda_id', $pemudaId)->delete();
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
                            'organization_name' => mb_strtolower(trim($orgName), 'UTF-8'),
                            'position'          => mb_strtolower(trim($position), 'UTF-8'),
                            'join_date'         => $joinDate,
                            'description'       => toLowerTrim($desc),
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
                            'position'          => 'anggota',
                        ]);
                    }
                }
            }

            // 8. Skills (Reset & Re-insert)
            $pemudaSkillModel = new PemudaSkillModel();
            $pemudaSkillModel->where('pemuda_id', $pemudaId)->delete();
            $skillsPost = $this->request->getPost('skills');
            if (!empty($skillsPost) && is_array($skillsPost)) {
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

            // 9. Interests (Reset & Re-insert)
            $pemudaInterestModel = new PemudaInterestModel();
            $pemudaInterestModel->where('pemuda_id', $pemudaId)->delete();
            $interestsPost = $this->request->getPost('interests');
            if (!empty($interestsPost) && is_array($interestsPost)) {
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
                             ->with('pemuda_id', $pemudaId)
                             ->with('is_update', $isUpdate);

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
        $isUpdate  = (bool) session()->getFlashdata('is_update');
        $pemuda    = null;

        if ($pemudaId) {
            $pemuda = $this->pemudaModel->find($pemudaId);
        }

        $data = [
            'registration_number' => $regNumber,
            'pemuda'              => $pemuda,
            'isUpdate'            => $isUpdate,
        ];

        return view('pendataan/sukses', $data);
    }

    /**
     * Endpoint AJAX untuk memeriksa data pemuda (Nama, Jenis Kelamin, Tanggal Lahir, Cabang)
     * Jika data sudah ada -> kembalikan 'status' => 'found' beserta data lengkap untuk dilengkapi
     * Jika data belum ada -> kembalikan 'status' => 'not_found' untuk lanjut mengisi form baru
     */
    public function checkData()
    {
        $throttler = \Config\Services::throttler();
        $ipAddress = $this->request->getIPAddress();
        // Allow max 60 checks per minute per IP
        if ($throttler->check(md5($ipAddress . '_check_youth_data'), 60, MINUTE) === false) {
            return $this->response->setJSON([
                'status'    => 'error',
                'found'     => false,
                'duplicate' => false,
                'message'   => 'Terlalu banyak permintaan pengecekan data. Silakan tunggu sebentar.',
                'csrfHash'  => csrf_hash(),
            ]);
        }

        $name      = trim((string) $this->request->getPost('name'));
        $gender    = trim((string) $this->request->getPost('gender'));
        $birthDate = trim((string) $this->request->getPost('birth_date'));
        $cabangId  = (int) $this->request->getPost('cabang_id');

        if ($name === '' || $birthDate === '' || $cabangId <= 0) {
            return $this->response->setJSON([
                'status'    => 'error',
                'found'     => false,
                'duplicate' => false,
                'message'   => 'Parameter nama lengkap, tanggal lahir, dan cabang wajib diisi untuk pengecekan data.',
                'csrfHash'  => csrf_hash(),
            ]);
        }

        $existing = $this->pemudaModel->findExistingPemuda($name, $gender, $birthDate, $cabangId);

        if ($existing) {
            $formattedBirth = date('d/m/Y', strtotime($existing['birth_date']));
            $fullData       = $this->pemudaModel->getPemudaDetail((int) $existing['id']);

            return $this->response->setJSON([
                'status'    => 'found',
                'found'     => true,
                'duplicate' => true,
                'message'   => "Data pemuda atas nama <strong>\"" . esc($existing['name']) . "\"</strong>, tanggal lahir <strong>({$formattedBirth})</strong> sudah terdaftar di cabang ini dengan No. Registrasi: <strong>" . esc($existing['registration_number']) . "</strong>. Formulir telah otomatis diisikan dengan data Anda. Silakan lengkapi / perbarui data Anda.",
                'data'      => $fullData,
                'csrfHash'  => csrf_hash(),
            ]);
        }

        return $this->response->setJSON([
            'status'    => 'not_found',
            'found'     => false,
            'duplicate' => false,
            'message'   => 'Data belum terdaftar di cabang ini. Silakan lanjutkan mengisi formulir pendataan pemuda baru.',
            'csrfHash'  => csrf_hash(),
        ]);
    }

    /**
     * Alias legacy untuk backwards compatibility
     */
    public function checkDuplicate()
    {
        return $this->checkData();
    }

    /**
     * Endpoint AJAX untuk mencari data Warga MTA dan Pemuda Lokal di Cabang terpilih
     * Menggabungkan fitur check data dengan search warga:
     * - Menampilkan data dari MTA Pusat maupun data lokal dari pemuda
     * - Mendeteksi apakah warga sudah terdaftar di PMD atau belum
     * - Memberikan opsi input data baru jika nama belum ada
     */
    public function searchWarga()
    {
        $throttler = \Config\Services::throttler();
        $ipAddress = $this->request->getIPAddress();
        // Allow max 120 searches per minute per IP for fast debounced typing
        if ($throttler->check(md5($ipAddress . '_search_warga_mta'), 120, MINUTE) === false) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terlalu banyak permintaan pencarian data. Silakan tunggu sebentar.',
                'data'    => [],
                'csrfHash'=> csrf_hash(),
            ]);
        }

        $cabangId = (int) $this->request->getGet('cabang_id');
        $query    = trim((string) $this->request->getGet('q'));

        if ($cabangId <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pilih cabang Pemuda MTA terlebih dahulu.',
                'data'    => [],
                'csrfHash'=> csrf_hash(),
            ]);
        }

        if (mb_strlen($query) < 2) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Ketik minimal 2 karakter untuk mencari data warga / pemuda.',
                'data'    => [],
                'csrfHash'=> csrf_hash(),
            ]);
        }

        $cabang = $this->cabangModel->find($cabangId);
        if (!$cabang) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cabang tidak ditemukan di sistem.',
                'data'    => [],
                'csrfHash'=> csrf_hash(),
            ]);
        }

        // 1. Cari data di database Lokal PMD untuk cabang ini
        $localPemudaList = $this->pemudaModel
            ->select('pemuda.id as id, pemuda.registration_number, pemuda.name, pemuda.gender, pemuda.birth_date, pemuda.birth_place, pemuda.phone, pemuda.status_verifikasi, pemuda.status_data, pemuda.mta_warga_uuid, pemuda.mta_status_warga, alamat.address_detail, alamat.dusun, alamat.rt, alamat.rw, alamat.district_id, alamat.village_id')
            ->join('alamat', 'alamat.pemuda_id = pemuda.id', 'left')
            ->where('pemuda.cabang_id', $cabangId)
            ->where('pemuda.status_data', 'active')
            ->groupStart()
                ->like('pemuda.name', $query)
                ->orLike('pemuda.registration_number', $query)
                ->orLike('pemuda.phone', $query)
            ->groupEnd()
            ->orderBy('pemuda.name', 'ASC')
            ->limit(20)
            ->findAll();

        // 2. Cari data di MTA Pusat via MtaApiService
        $mtaCabangUuid = $cabang['mta_uuid'] ?? null;
        $apiService = new \App\Services\MtaApiService();

        if (empty($mtaCabangUuid)) {
            try {
                $cabangListRes = $apiService->getCabangSragenList();
                if (($cabangListRes['success'] ?? false) && !empty($cabangListRes['data'])) {
                    $cleanLocalName = strtolower((string) preg_replace('/[^a-z0-9]/', '', $cabang['name']));
                    preg_match('/\d+$/', $cleanLocalName, $mLocalNum);
                    $localNum = $mLocalNum[0] ?? '';

                    foreach ($cabangListRes['data'] as $mc) {
                        $cleanMcName = strtolower((string) preg_replace('/[^a-z0-9]/', '', $mc['nama'] ?? ''));
                        preg_match('/\d+$/', $cleanMcName, $mMcNum);
                        $mcNum = $mMcNum[0] ?? '';

                        // Jika ada nomor cabang (misal 1, 2), nomor harus sama persis
                        if ($localNum !== '' || $mcNum !== '') {
                            if ($localNum !== $mcNum) {
                                continue;
                            }
                        }

                        if ($cleanLocalName === $cleanMcName || str_contains($cleanMcName, $cleanLocalName) || str_contains($cleanLocalName, $cleanMcName)) {
                            $mtaCabangUuid = $mc['uuid'];
                            $this->cabangModel->update($cabangId, ['mta_uuid' => $mtaCabangUuid]);
                            break;
                        }
                    }
                }
            } catch (\Throwable $e) {
                log_message('warning', 'MTA Cabang lookup failed in searchWarga: ' . $e->getMessage());
            }
        }

        $mtaWargaList = [];
        try {
            $searchParams = ['limit' => 50];
            if (!empty($mtaCabangUuid)) {
                $searchParams['cabang'] = $mtaCabangUuid;
                $searchParams['cabang_uuid'] = $mtaCabangUuid;
            } else {
                $searchParams['cabang'] = $cabang['name'];
            }

            $searchRes = $apiService->searchWarga($query, $searchParams);
            if (($searchRes['success'] ?? false) && !empty($searchRes['data'])) {
                $mtaWargaList = $searchRes['data'];
            }
        } catch (\Throwable $e) {
            log_message('warning', 'MTA searchWarga failed: ' . $e->getMessage());
        }

        // 3. Gabungkan (Merge) data dari MTA Pusat dan Lokal PMD secara cerdas
        $mergedList = [];
        $matchedLocalIds = [];

        // Buat index pencocokan untuk data lokal
        $localByUuid = [];
        $localByNameBirth = [];

        foreach ($localPemudaList as $lp) {
            if (!empty($lp['mta_warga_uuid'])) {
                $localByUuid[$lp['mta_warga_uuid']] = $lp;
            }
            $normKey = strtolower(trim($lp['name'])) . '|' . (!empty($lp['birth_date']) ? date('Y-m-d', strtotime($lp['birth_date'])) : '');
            $localByNameBirth[$normKey] = $lp;
        }

        // Filter ketat: Hanya izinkan data warga MTA yang memang berasal dari cabang terpilih
        $cleanTargetCabangName = strtolower((string) preg_replace('/[^a-z0-9]/', '', $cabang['name']));
        preg_match('/\d+$/', $cleanTargetCabangName, $mTargetNum);
        $targetNum = $mTargetNum[0] ?? '';

        $filteredMtaWargaList = [];
        foreach ($mtaWargaList as $w) {
            $wUuid = $w['uuid'] ?? '';
            $wCabangUuid = $w['cabang_uuid'] ?? ($w['cabang_id'] ?? '');
            $wCabangRaw  = trim($w['cabang'] ?? ($w['cabang_nama'] ?? ''));
            $cleanWCabang = strtolower((string) preg_replace('/[^a-z0-9]/', '', $wCabangRaw));
            preg_match('/\d+$/', $cleanWCabang, $mWCabangNum);
            $wCabangNum = $mWCabangNum[0] ?? '';

            // 1. Jika warga sudah terdaftar di PMD lokal cabang ini -> PASTI milik cabang ini
            if (!empty($wUuid) && isset($localByUuid[$wUuid])) {
                $filteredMtaWargaList[] = $w;
                continue;
            }
            $wNormKey = strtolower(trim($w['nama'] ?? '')) . '|' . (!empty($w['lahir']) ? date('Y-m-d', strtotime($w['lahir'])) : '');
            if (isset($localByNameBirth[$wNormKey])) {
                $filteredMtaWargaList[] = $w;
                continue;
            }

            // 2. Jika ada informasi nama cabang pada data warga MTA, validasi kecocokan cabang
            if (!empty($wCabangRaw)) {
                // Jika ada nomor cabang (misal Gemolong 1 vs Gemolong 2), nomor harus sama
                if ($targetNum !== '' || $wCabangNum !== '') {
                    if ($targetNum !== $wCabangNum) {
                        continue; // Beda nomor cabang (jangan tampilkan)
                    }
                }

                if ($cleanTargetCabangName === $cleanWCabang ||
                    str_contains($cleanWCabang, $cleanTargetCabangName) ||
                    str_contains($cleanTargetCabangName, $cleanWCabang)) {
                    $filteredMtaWargaList[] = $w;
                    continue;
                }

                // Nama cabang terisi namun tidak cocok -> lewati
                continue;
            }

            // 3. Jika ada cabang_uuid pada data warga MTA
            if (!empty($mtaCabangUuid) && !empty($wCabangUuid)) {
                if (strcasecmp($mtaCabangUuid, $wCabangUuid) === 0) {
                    $filteredMtaWargaList[] = $w;
                    continue;
                } else {
                    continue; // Beda cabang UUID
                }
            }

            // 4. Jika data warga dari MTA tidak memuat field nama cabang maupun cabang_uuid,
            // dan kita mencari menggunakan filter cabang UUID ke API
            if (!empty($mtaCabangUuid)) {
                $filteredMtaWargaList[] = $w;
            }
        }
        $mtaWargaList = $filteredMtaWargaList;

        // Proses data dari MTA Pusat
        foreach ($mtaWargaList as $w) {
            $uuid = $w['uuid'] ?? '';
            $matchedLocal = null;

            if (!empty($uuid) && isset($localByUuid[$uuid])) {
                $matchedLocal = $localByUuid[$uuid];
            } else {
                $wNormKey = strtolower(trim($w['nama'] ?? '')) . '|' . (!empty($w['lahir']) ? date('Y-m-d', strtotime($w['lahir'])) : '');
                if (isset($localByNameBirth[$wNormKey])) {
                    $matchedLocal = $localByNameBirth[$wNormKey];
                } elseif (!empty($uuid)) {
                    // Cek database langsung jika di batch lokal belum terambil
                    $dbCheck = $this->pemudaModel
                        ->select('id, registration_number, name, gender, birth_date, phone, status_verifikasi, mta_warga_uuid')
                        ->where('cabang_id', $cabangId)
                        ->where('mta_warga_uuid', $uuid)
                        ->first();
                    if ($dbCheck) {
                        $matchedLocal = $dbCheck;
                    }
                }
            }

            if ($matchedLocal) {
                $matchedLocalIds[] = (int) $matchedLocal['id'];
                $resolvedUuid = !empty($uuid) ? $uuid : (!empty($matchedLocal['mta_warga_uuid']) ? $matchedLocal['mta_warga_uuid'] : (string) $matchedLocal['id']);
                $mergedList[] = [
                    'source'            => 'both', // Ada di MTA Pusat dan terdaftar di PMD
                    'uuid'              => $resolvedUuid,
                    'nomor'             => $w['nomor'] ?? '',
                    'nama'              => $matchedLocal['name'] ?? ($w['nama'] ?? ''),
                    'kelamin'           => in_array(strtoupper($matchedLocal['gender'] ?? $w['kelamin'] ?? 'L'), ['L', 'P'], true) ? strtoupper($matchedLocal['gender'] ?? $w['kelamin']) : 'L',
                    'lahir'             => !empty($matchedLocal['birth_date']) ? $matchedLocal['birth_date'] : ($w['lahir'] ?? ''),
                    'usia'              => $w['usia'] ?? (!empty($matchedLocal['birth_date']) ? (date('Y') - date('Y', strtotime($matchedLocal['birth_date']))) : null),
                    'alamat'            => !empty($matchedLocal['address_detail']) ? $matchedLocal['address_detail'] : ($w['alamat'] ?? ''),
                    'cabang'            => $cabang['name'],
                    'nohp'              => !empty($matchedLocal['phone']) ? $matchedLocal['phone'] : ($w['nohp'] ?? ''),
                    'is_registered_pmd' => true,
                    'local_pemuda_id'   => (int) $matchedLocal['id'],
                    'local_reg_number'  => $matchedLocal['registration_number'] ?? null,
                    'status_verifikasi' => $matchedLocal['status_verifikasi'] ?? 'verified',
                    'status_label'      => 'Terdaftar di PMD & MTA Pusat',
                ];
            } else {
                $mergedList[] = [
                    'source'            => 'mta', // Warga MTA Pusat, belum terdaftar di PMD
                    'uuid'              => $uuid,
                    'nomor'             => $w['nomor'] ?? '',
                    'nama'              => $w['nama'] ?? '',
                    'kelamin'           => in_array(strtoupper($w['kelamin'] ?? 'L'), ['L', 'P'], true) ? strtoupper($w['kelamin']) : 'L',
                    'lahir'             => $w['lahir'] ?? '',
                    'usia'              => $w['usia'] ?? null,
                    'alamat'            => $w['alamat'] ?? '',
                    'cabang'            => $cabang['name'],
                    'nohp'              => $w['nohp'] ?? '',
                    'is_registered_pmd' => false,
                    'local_pemuda_id'   => null,
                    'local_reg_number'  => null,
                    'status_verifikasi' => null,
                    'status_label'      => 'Warga MTA Pusat (Belum Terdaftar PMD)',
                ];
            }
        }

        // Tambahkan data lokal PMD yang belum masuk (yang tidak tercantum di hasil MTA)
        foreach ($localPemudaList as $lp) {
            if (in_array((int) $lp['id'], $matchedLocalIds, true)) {
                continue;
            }

            $age = !empty($lp['birth_date']) ? (date('Y') - date('Y', strtotime($lp['birth_date']))) : null;
            $hasMta = !empty($lp['mta_warga_uuid']) || ($lp['status_verifikasi'] ?? '') === 'verified';
            $resolvedUuid = !empty($lp['mta_warga_uuid']) ? $lp['mta_warga_uuid'] : (string) $lp['id'];

            $mergedList[] = [
                'source'            => $hasMta ? 'both' : 'pmd',
                'uuid'              => $resolvedUuid,
                'nomor'             => null,
                'nama'              => $lp['name'],
                'kelamin'           => in_array(strtoupper($lp['gender'] ?? 'L'), ['L', 'P'], true) ? strtoupper($lp['gender']) : 'L',
                'lahir'             => $lp['birth_date'] ?? '',
                'usia'              => $age,
                'alamat'            => $lp['address_detail'] ?? ($lp['dusun'] ?? ''),
                'cabang'            => $cabang['name'],
                'nohp'              => $lp['phone'] ?? '',
                'is_registered_pmd' => true,
                'local_pemuda_id'   => (int) $lp['id'],
                'local_reg_number'  => $lp['registration_number'] ?? null,
                'status_verifikasi' => $lp['status_verifikasi'] ?? 'pending',
                'status_label'      => $hasMta ? 'Terdaftar di PMD & MTA Pusat' : 'Terdaftar di PMD (Lokal)',
            ];
        }

        // Urutkan: Pemuda yang sudah terdaftar di PMD diutamakan, lalu abjad
        usort($mergedList, function($a, $b) {
            if ($a['is_registered_pmd'] !== $b['is_registered_pmd']) {
                return $a['is_registered_pmd'] ? -1 : 1;
            }
            return strcasecmp($a['nama'], $b['nama']);
        });

        return $this->response->setJSON([
            'success'     => true,
            'cabang_id'   => $cabangId,
            'cabang_name' => $cabang['name'],
            'total'       => count($mergedList),
            'data'        => $mergedList,
            'csrfHash'    => csrf_hash(),
        ]);
    }

    /**
     * Endpoint AJAX untuk mengambil detail lengkap Pemuda Lokal PMD
     * (Untuk auto-populate seluruh data form saat pemuda terdaftar dipilih dari pencarian)
     */
    public function pemudaDetail(int $id)
    {
        if ($id <= 0) {
            return $this->response->setJSON([
                'success'  => false,
                'message'  => 'ID Pemuda tidak valid.',
                'csrfHash' => csrf_hash(),
            ]);
        }

        $fullData = $this->pemudaModel->getPemudaDetail($id);
        if (!$fullData) {
            return $this->response->setJSON([
                'success'  => false,
                'message'  => 'Data pemuda tidak ditemukan di sistem.',
                'csrfHash' => csrf_hash(),
            ]);
        }

        return $this->response->setJSON([
            'success'  => true,
            'source'   => 'pmd',
            'data'     => $fullData,
            'csrfHash' => csrf_hash(),
        ]);
    }

    /**
     * Endpoint AJAX untuk mengambil detail Warga MTA untuk melengkapi form pendataan
     */
    public function wargaDetail(string $uuid)
    {
        $uuid = trim($uuid);
        if (empty($uuid)) {
            return $this->response->setJSON([
                'success'  => false,
                'message'  => 'UUID Warga MTA tidak valid.',
                'csrfHash' => csrf_hash(),
            ]);
        }

        // 1. Cek terlebih dahulu apakah UUID atau ID ini adalah milik Pemuda Lokal PMD
        $localPemuda = $this->pemudaModel->findByMtaWargaUuid($uuid);
        if (!$localPemuda && (is_numeric($uuid) || preg_match('/^\d+$/', $uuid))) {
            $localPemuda = $this->pemudaModel->find((int) $uuid);
        }
        if (!$localPemuda) {
            $localPemuda = $this->pemudaModel->where('registration_number', $uuid)->first();
        }

        if ($localPemuda) {
            $fullPmdData = $this->pemudaModel->getPemudaDetail((int) $localPemuda['id']);
            if ($fullPmdData) {
                return $this->response->setJSON([
                    'success'  => true,
                    'source'   => 'pmd',
                    'data'     => [
                        'uuid'              => $localPemuda['mta_warga_uuid'] ?? (string) $localPemuda['id'],
                        'nomor'             => '',
                        'name'              => $localPemuda['name'],
                        'gender'            => in_array(strtoupper($localPemuda['gender'] ?? 'L'), ['L', 'P'], true) ? strtoupper($localPemuda['gender']) : 'L',
                        'birth_date'        => !empty($localPemuda['birth_date']) ? $localPemuda['birth_date'] : '',
                        'birth_place'       => !empty($localPemuda['birth_place']) ? $localPemuda['birth_place'] : 'Sragen',
                        'phone'             => !empty($localPemuda['phone']) ? $localPemuda['phone'] : '',
                        'marital_status'    => $localPemuda['marital_status'] ?? 'belum_menikah',
                        'blood_type'        => $localPemuda['blood_type'] ?? 'tidak_tahu',
                        'address_detail'    => $fullPmdData['address_detail'] ?? '',
                        'dusun'             => $fullPmdData['dusun'] ?? '',
                        'rt'                => $fullPmdData['rt'] ?? '',
                        'rw'                => $fullPmdData['rw'] ?? '',
                        'district_id'       => $fullPmdData['district_id'] ?? null,
                        'village_id'        => $fullPmdData['village_id'] ?? null,
                        'pekerjaan'         => $fullPmdData['job_title'] ?? '',
                        'ayah'              => '',
                        'ibu'               => '',
                        'is_registered_pmd' => true,
                        'local_pemuda_id'   => (int) $localPemuda['id'],
                        'local_reg_number'  => $localPemuda['registration_number'] ?? null,
                        'full_pmd_data'     => $fullPmdData,
                    ],
                    'csrfHash' => csrf_hash(),
                ]);
            }
        }

        $apiService = new \App\Services\MtaApiService();
        $detailRes  = $apiService->getWargaDetail($uuid);

        if (!($detailRes['success'] ?? false) || empty($detailRes['data'])) {
            return $this->response->setJSON([
                'success'  => false,
                'message'  => $detailRes['message'] ?? 'Data warga tidak ditemukan di database MTA Pusat.',
                'csrfHash' => csrf_hash(),
            ]);
        }

        $w = $detailRes['data'];

        // Cek apakah warga ini sudah tercatat di PMD Lokal
        $localPemuda = $this->pemudaModel->findByMtaWargaUuid($uuid);
        if (!$localPemuda && !empty($w['nama']) && !empty($w['lahir'])) {
            $localPemuda = $this->pemudaModel->where('birth_date', date('Y-m-d', strtotime($w['lahir'])))
                                             ->where('LOWER(TRIM(name))', strtolower(trim($w['nama'])))
                                             ->first();
        }

        $fullPmdData = null;
        if ($localPemuda) {
            $fullPmdData = $this->pemudaModel->getPemudaDetail((int) $localPemuda['id']);
        }

        // Parsing marital status
        $maritalRaw = strtolower($w['menikah'] ?? '');
        $maritalStatus = 'belum_menikah';
        if (str_contains($maritalRaw, 'sudah') || (str_contains($maritalRaw, 'menikah') && !str_contains($maritalRaw, 'belum'))) {
            $maritalStatus = 'sudah_menikah';
        } elseif (str_contains($maritalRaw, 'janda')) {
            $maritalStatus = 'janda';
        } elseif (str_contains($maritalRaw, 'duda')) {
            $maritalStatus = 'duda';
        }

        // Parsing blood type
        $bloodRaw = strtoupper(trim($w['goldar'] ?? ''));
        $bloodType = in_array($bloodRaw, ['A', 'B', 'AB', 'O'], true) ? $bloodRaw : 'tidak_tahu';

        // Parsing RT & RW jika ada di alamat_rtrw
        $rt = '';
        $rw = '';
        $rtrwStr = $w['alamat_rtrw'] ?? '';
        if (preg_match('/rt\s*(\d+)/i', $rtrwStr, $mRt)) {
            $rt = $mRt[1];
        }
        if (preg_match('/rw\s*(\d+)/i', $rtrwStr, $mRw)) {
            $rw = $mRw[1];
        }

        // Lookup kecamatan & desa jika cocok dengan database lokal
        $districtId = null;
        $villageId  = null;
        if (!empty($w['kecamatan'])) {
            $districtModel = new \App\Models\DistrictModel();
            $cleanKec = strtolower(trim(str_ireplace(['kecamatan', 'kec.'], '', $w['kecamatan'])));
            $district = $districtModel->where('LOWER(TRIM(name))', $cleanKec)->first();
            if ($district) {
                $districtId = (int) $district['id'];
                if (!empty($w['desa'])) {
                    $villageModel = new \App\Models\VillageModel();
                    $cleanDesa = strtolower(trim(str_ireplace(['desa', 'kelurahan', 'kel.'], '', $w['desa'])));
                    $village = $villageModel->where('district_id', $districtId)
                                            ->where('LOWER(TRIM(name))', $cleanDesa)
                                            ->first();
                    if (!$village) {
                        $village = $villageModel->where('district_id', $districtId)
                                                ->like('LOWER(name)', $cleanDesa)
                                                ->first();
                    }
                    if ($village) {
                        $villageId = (int) $village['id'];
                    }
                }
            }
        }

        $formattedData = [
            'uuid'              => $w['uuid'],
            'nomor'             => $w['nomor'] ?? '',
            'name'              => $w['nama'] ?? '',
            'gender'            => in_array(strtoupper($w['kelamin'] ?? 'L'), ['L', 'P'], true) ? strtoupper($w['kelamin']) : 'L',
            'birth_date'        => !empty($w['lahir']) ? date('Y-m-d', strtotime($w['lahir'])) : '',
            'birth_place'       => !empty($w['kabupaten']) ? $w['kabupaten'] : 'Sragen',
            'phone'             => !empty($w['nohp']) ? preg_replace('/[^0-9+]/', '', $w['nohp']) : '',
            'marital_status'    => $maritalStatus,
            'blood_type'        => $bloodType,
            'address_detail'    => !empty($w['alamat']) ? $w['alamat'] : ($w['alamat_rtrw'] ?? ''),
            'dusun'             => $w['desa'] ?? '',
            'rt'                => $rt,
            'rw'                => $rw,
            'district_id'       => $districtId,
            'village_id'        => $villageId,
            'pekerjaan'         => $w['pekerjaan'] ?? '',
            'ayah'              => $w['ayah'] ?? '',
            'ibu'               => $w['ibu'] ?? '',
            'is_registered_pmd' => (bool) $localPemuda,
            'local_pemuda_id'   => $localPemuda['id'] ?? null,
            'local_reg_number'  => $localPemuda['registration_number'] ?? null,
            'full_pmd_data'     => $fullPmdData,
        ];

        return $this->response->setJSON([
            'success'  => true,
            'data'     => $formattedData,
            'csrfHash' => csrf_hash(),
        ]);
    }
}
