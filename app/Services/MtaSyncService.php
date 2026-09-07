<?php

namespace App\Services;

use App\Models\PemudaModel;
use App\Models\WilayahModel;
use App\Models\CabangModel;
use App\Models\AlamatModel;
use App\Models\PendidikanModel;
use App\Models\PekerjaanModel;
use App\Models\OrganisasiModel;
use App\Models\DistrictModel;
use App\Models\VillageModel;
use App\Models\EducationLevelModel;
use App\Models\JobStatusModel;
use App\Models\MtaSyncLogModel;
use App\Models\MtaSyncQueueModel;
use Config\Database;

class MtaSyncService
{
    protected MtaApiService $apiService;
    protected PemudaModel $pemudaModel;
    protected WilayahModel $wilayahModel;
    protected CabangModel $cabangModel;
    protected AlamatModel $alamatModel;
    protected PendidikanModel $pendidikanModel;
    protected PekerjaanModel $pekerjaanModel;
    protected OrganisasiModel $organisasiModel;
    protected DistrictModel $districtModel;
    protected VillageModel $villageModel;
    protected MtaSyncLogModel $logModel;
    protected MtaSyncQueueModel $queueModel;

    public function __construct(?MtaApiService $apiService = null)
    {
        $this->apiService      = $apiService ?? new MtaApiService();
        $this->pemudaModel     = new PemudaModel();
        $this->wilayahModel    = new WilayahModel();
        $this->cabangModel     = new CabangModel();
        $this->alamatModel     = new AlamatModel();
        $this->pendidikanModel = new PendidikanModel();
        $this->pekerjaanModel  = new PekerjaanModel();
        $this->organisasiModel = new OrganisasiModel();
        $this->districtModel   = new DistrictModel();
        $this->villageModel    = new VillageModel();
        $this->logModel        = new MtaSyncLogModel();
        $this->queueModel      = new MtaSyncQueueModel();
    }

    /**
     * Sinkronisasi data Cabang dari API MTA ke Database Lokal (Khusus Perwakilan Sragen)
     *
     * @param string|null $perwakilanUuid UUID Perwakilan di MTA (opsional, default: Sragen)
     * @param int|null    $wilayahId      ID Wilayah default untuk cabang baru (opsional)
     * @param bool        $autoCreate     Buat cabang baru jika belum ada di database lokal
     * @return array Hasil sinkronisasi
     */
    public function syncCabang(?string $perwakilanUuid = null, ?int $wilayahId = null, bool $autoCreate = true): array
    {
        $perwakilanUuid = $perwakilanUuid ?: $this->apiService->getSragenUuid();
        $response = $this->apiService->getCabangSragenList();

        if (!($response['success'] ?? false) || !isset($response['data']) || !is_array($response['data'])) {
            $msg = $response['message'] ?? 'Gagal mengambil data cabang Perwakilan Sragen dari server API MTA.';
            $this->logModel->log('cabang', 'failed', 0, $msg);
            return [
                'success' => false,
                'message' => $msg,
            ];
        }

        $mtaCabangList = $response['data'];
        $syncedCount   = 0;
        $createdCount  = 0;
        $updatedCount  = 0;
        $unmatched     = [];

        // Ambil default wilayah ID jika tidak diberikan
        if (!$wilayahId) {
            $firstWilayah = $this->wilayahModel->first();
            $wilayahId = $firstWilayah ? (int) $firstWilayah['id'] : 1;
        }

        foreach ($mtaCabangList as $item) {
            $uuid   = $item['uuid'] ?? '';
            $nama   = trim($item['nama'] ?? '');
            $kode   = trim($item['kode'] ?? '');
            $alamat = trim($item['alamat'] ?? '');

            if (empty($uuid) || empty($nama)) {
                continue;
            }

            // 1. Cari cabang lokal berdasarkan mta_uuid atau nama yang mirip
            $existing = $this->cabangModel->where('mta_uuid', $uuid)->first();

            if (!$existing) {
                // Cari berdasarkan kesamaan nama
                $existing = $this->cabangModel->where('LOWER(TRIM(name))', strtolower($nama))->first();
            }

            if ($existing) {
                // Update mta_uuid & sync timestamp
                $updateData = [
                    'mta_uuid'           => $uuid,
                    'mta_last_synced_at' => date('Y-m-d H:i:s'),
                ];
                if (!empty($kode)) {
                    $updateData['code'] = $kode;
                }
                if (empty($existing['alamat']) && !empty($alamat)) {
                    $updateData['alamat'] = $alamat;
                }

                $this->cabangModel->update($existing['id'], $updateData);
                $updatedCount++;
                $syncedCount++;
            } elseif ($autoCreate) {
                // Insert cabang baru
                $insertData = [
                    'wilayah_id'         => $wilayahId,
                    'code'               => !empty($kode) ? $kode : null,
                    'name'               => $nama,
                    'alamat'             => !empty($alamat) ? $alamat : null,
                    'mta_uuid'           => $uuid,
                    'mta_last_synced_at' => date('Y-m-d H:i:s'),
                ];
                $this->cabangModel->insert($insertData);
                $createdCount++;
                $syncedCount++;
            } else {
                $unmatched[] = $nama;
            }
        }

        $logMsg = "Sinkronisasi Cabang MTA selesai. Total diproses: {$syncedCount} (Dibuat baru: {$createdCount}, Diperbarui: {$updatedCount}).";
        $this->logModel->log('cabang', 'success', $syncedCount, $logMsg);

        return [
            'success'      => true,
            'message'      => $logMsg,
            'total'        => count($mtaCabangList),
            'synced'       => $syncedCount,
            'created'      => $createdCount,
            'updated'      => $updatedCount,
            'unmatched'    => $unmatched,
        ];
    }

    /**
     * Impor atau Sinkronkan satu data Warga MTA ke dalam tabel Pemuda
     *
     * @param array    $wargaData Data warga (dari getWargaDetail atau elemen getWargaList)
     * @param int      $cabangId  ID cabang lokal
     * @param int|null $userId    ID user yang melakukan sinkronisasi
     * @return array Status dan data pemuda yang dihasilkan
     */
    public function syncWargaToPemuda(array $wargaData, int $cabangId, ?int $userId = null): array
    {
        $wargaUuid = $wargaData['uuid'] ?? '';
        $nama      = trim($wargaData['nama'] ?? '');

        if (empty($nama)) {
            return ['success' => false, 'message' => 'Nama warga MTA tidak valid.'];
        }

        // Jika detail data belum lengkap (misal dari pencarian ringkas), ambil detail lengkap
        if (!isset($wargaData['alamat_rtrw']) && !empty($wargaUuid)) {
            $detailRes = $this->apiService->getWargaDetail($wargaUuid);
            if (($detailRes['success'] ?? false) && !empty($detailRes['data'])) {
                $wargaData = array_merge($wargaData, $detailRes['data']);
            }
        }

        $gender       = in_array(strtoupper($wargaData['kelamin'] ?? 'L'), ['L', 'P'], true) ? strtoupper($wargaData['kelamin']) : 'L';
        $birthDate    = !empty($wargaData['lahir']) ? date('Y-m-d', strtotime($wargaData['lahir'])) : null;
        $phone        = !empty($wargaData['nohp']) ? preg_replace('/[^0-9+]/', '', $wargaData['nohp']) : null;
        $bloodType    = !empty($wargaData['goldar']) ? strtoupper(trim($wargaData['goldar'])) : null;
        $mtaStatus    = $wargaData['status'] ?? 'Warga';
        $maritalRaw   = strtolower($wargaData['menikah'] ?? '');
        $maritalStatus = 'belum_menikah';
        if (str_contains($maritalRaw, 'sudah') || str_contains($maritalRaw, 'menikah') && !str_contains($maritalRaw, 'belum')) {
            $maritalStatus = 'sudah_menikah';
        } elseif (str_contains($maritalRaw, 'janda')) {
            $maritalStatus = 'janda';
        } elseif (str_contains($maritalRaw, 'duda')) {
            $maritalStatus = 'duda';
        }

        // 1. Cek apakah sudah pernah tersinkronisasi berdasarkan mta_warga_uuid
        $existingPemuda = null;
        if (!empty($wargaUuid)) {
            $existingPemuda = $this->pemudaModel->findByMtaWargaUuid($wargaUuid);
        }

        // 2. Jika belum, cek duplikasi berdasarkan (nama + tanggal lahir + cabang)
        if (!$existingPemuda && $birthDate) {
            $existingPemuda = $this->pemudaModel->findDuplicate($nama, $birthDate, $cabangId);
        }

        $db = Database::connect();
        $db->transStart();

        try {
            $pemudaId = null;
            $action = 'created';

            if ($existingPemuda) {
                // Update data pemuda yang sudah ada
                $pemudaId = (int) $existingPemuda['id'];
                $action   = 'updated';

                $updatePemuda = [
                    'name'             => $nama,
                    'gender'           => $gender,
                    'marital_status'   => $maritalStatus,
                    'status_verifikasi'=> 'verified', // Karena data resmi dari Database Warga MTA
                    'mta_warga_uuid'   => $wargaUuid ?: ($existingPemuda['mta_warga_uuid'] ?? null),
                    'mta_status_warga' => $mtaStatus,
                    'mta_ayah_uuid'    => $wargaData['ayah_uuid'] ?? ($existingPemuda['mta_ayah_uuid'] ?? null),
                    'mta_ibu_uuid'     => $wargaData['ibu_uuid'] ?? ($existingPemuda['mta_ibu_uuid'] ?? null),
                    'mta_foto_url'     => $wargaData['foto'] ?? ($existingPemuda['mta_foto_url'] ?? null),
                    'mta_synced_at'    => date('Y-m-d H:i:s'),
                ];

                if (!empty($birthDate)) {
                    $updatePemuda['birth_date'] = $birthDate;
                }
                if (!empty($phone) && empty($existingPemuda['phone'])) {
                    $updatePemuda['phone'] = $phone;
                }
                if (!empty($bloodType) && empty($existingPemuda['blood_type'])) {
                    $updatePemuda['blood_type'] = $bloodType;
                }

                $this->pemudaModel->update($pemudaId, $updatePemuda);
            } else {
                // Insert Pemuda Baru
                $regNumber = $this->pemudaModel->generateRegistrationNumber((int) $cabangId, $birthDate);
                $insertPemuda = [
                    'cabang_id'           => $cabangId,
                    'registration_number' => $regNumber,
                    'name'                => $nama,
                    'gender'              => $gender,
                    'marital_status'      => $maritalStatus,
                    'blood_type'          => $bloodType,
                    'birth_place'         => $wargaData['kabupaten'] ?? 'Sragen',
                    'birth_date'          => $birthDate ?: date('Y-m-d', strtotime('-20 years')),
                    'phone'               => $phone ?: '081234567890',
                    'email'               => null,
                    'status_verifikasi'   => 'verified',
                    'status_data'         => 'active',
                    'mta_warga_uuid'      => $wargaUuid,
                    'mta_status_warga'    => $mtaStatus,
                    'mta_ayah_uuid'       => $wargaData['ayah_uuid'] ?? null,
                    'mta_ibu_uuid'        => $wargaData['ibu_uuid'] ?? null,
                    'mta_foto_url'        => $wargaData['foto'] ?? null,
                    'mta_synced_at'       => date('Y-m-d H:i:s'),
                    'created_by'          => $userId ?? (session()->get('user_id') ?? null),
                ];

                $this->pemudaModel->insert($insertPemuda);
                $pemudaId = $this->pemudaModel->getInsertID();
            }

            // 3. Sinkronisasi Alamat
            $this->syncAlamatPemuda($pemudaId, $wargaData);

            // 4. Sinkronisasi Pekerjaan
            if (!empty($wargaData['pekerjaan'])) {
                $this->syncPekerjaanPemuda($pemudaId, $wargaData['pekerjaan']);
            }

            // 5. Default Pendidikan jika belum ada
            $existingPendidikan = $this->pendidikanModel->where('pemuda_id', $pemudaId)->first();
            if (!$existingPendidikan) {
                $defaultEdu = (new EducationLevelModel())->first();
                $this->pendidikanModel->insert([
                    'pemuda_id'          => $pemudaId,
                    'education_level_id' => $defaultEdu ? (int) $defaultEdu['id'] : 4,
                    'school_name'        => 'Data dari Warga MTA',
                    'education_status'   => 'lulus',
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return ['success' => false, 'message' => 'Gagal melakukan transaksi database untuk Warga: ' . $nama];
            }

            return [
                'success'   => true,
                'action'    => $action,
                'pemuda_id' => $pemudaId,
                'name'      => $nama,
                'uuid'      => $wargaUuid,
                'message'   => "Data pemuda {$nama} berhasil di-{$action} dari Database Warga MTA.",
            ];

        } catch (\Throwable $e) {
            $db->transRollback();
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses data warga MTA: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Helper untuk sinkronisasi alamat dari data Warga MTA
     */
    protected function syncAlamatPemuda(int $pemudaId, array $wargaData): void
    {
        $existingAlamat = $this->alamatModel->where('pemuda_id', $pemudaId)->first();

        $alamatDetail = trim($wargaData['alamat'] ?? '');
        $rtrwRaw     = trim($wargaData['alamat_rtrw'] ?? '');
        $rt           = null;
        $rw           = null;

        if (preg_match('/rt\s*([0-9]+)/i', $rtrwRaw, $mRt)) {
            $rt = $mRt[1];
        }
        if (preg_match('/rw\s*([0-9]+)/i', $rtrwRaw, $mRw)) {
            $rw = $mRw[1];
        }

        // Resolusi Kecamatan di Sragen
        $kecamatanName = trim($wargaData['kecamatan'] ?? '');
        $districtId    = null;
        if (!empty($kecamatanName)) {
            $district = $this->districtModel->where('regency_id', 3314)
                                           ->where('LOWER(TRIM(name))', strtolower($kecamatanName))
                                           ->first();
            if ($district) {
                $districtId = (int) $district['id'];
            }
        }
        if (!$districtId) {
            // Default ke kecamatan pertama di Sragen jika tidak terdeteksi
            $firstDistrict = $this->districtModel->where('regency_id', 3314)->first();
            $districtId    = $firstDistrict ? (int) $firstDistrict['id'] : 331401;
        }

        // Resolusi Desa di Sragen
        $desaName  = trim($wargaData['desa'] ?? '');
        $villageId = null;
        if (!empty($desaName) && $districtId) {
            $village = $this->villageModel->where('district_id', $districtId)
                                         ->where('LOWER(TRIM(name))', strtolower($desaName))
                                         ->first();
            if ($village) {
                $villageId = (int) $village['id'];
            }
        }
        if (!$villageId && $districtId) {
            $firstVillage = $this->villageModel->where('district_id', $districtId)->first();
            $villageId    = $firstVillage ? (int) $firstVillage['id'] : 3314010001;
        }

        $alamatData = [
            'pemuda_id'      => $pemudaId,
            'province_id'    => 33, // Jawa Tengah
            'regency_id'     => 3314, // Sragen
            'district_id'    => $districtId,
            'village_id'     => $villageId,
            'dusun'          => !empty($wargaData['desa']) ? $wargaData['desa'] : null,
            'rt'             => $rt,
            'rw'             => $rw,
            'address_detail' => !empty($alamatDetail) ? $alamatDetail : ($desaName ? "Ds. {$desaName}, Kec. {$kecamatanName}" : 'Sragen'),
        ];

        if ($existingAlamat) {
            $this->alamatModel->update($existingAlamat['id'], $alamatData);
        } else {
            $this->alamatModel->insert($alamatData);
        }
    }

    /**
     * Helper untuk sinkronisasi data pekerjaan dari Warga MTA
     */
    protected function syncPekerjaanPemuda(int $pemudaId, string $jobTitle): void
    {
        $existing = $this->pekerjaanModel->where('pemuda_id', $pemudaId)->first();
        $jobStatuses = (new JobStatusModel())->findAll();
        $matchedStatusId = 1; // Default

        $jobLower = strtolower($jobTitle);
        foreach ($jobStatuses as $status) {
            if (str_contains($jobLower, strtolower($status['name']))) {
                $matchedStatusId = (int) $status['id'];
                break;
            }
        }

        $pekerjaanData = [
            'pemuda_id'     => $pemudaId,
            'job_status_id' => $matchedStatusId,
            'job_title'     => $jobTitle,
        ];

        if ($existing) {
            $this->pekerjaanModel->update($existing['id'], $pekerjaanData);
        } else {
            $this->pekerjaanModel->insert($pekerjaanData);
        }
    }

    /**
     * Live Sync Pemuda Tunggal dengan Server MTA berdasarkan UUID atau Nama/No HP
     */
    public function syncSinglePemuda(int $pemudaId): array
    {
        $pemuda = $this->pemudaModel->find($pemudaId);
        if (!$pemuda) {
            return ['success' => false, 'message' => 'Data pemuda tidak ditemukan.'];
        }

        $wargaData = null;

        // 1. Coba ambil berdasarkan mta_warga_uuid jika ada
        if (!empty($pemuda['mta_warga_uuid'])) {
            $res = $this->apiService->getWargaDetail($pemuda['mta_warga_uuid']);
            if (($res['success'] ?? false) && !empty($res['data'])) {
                $wargaData = $res['data'];
            }
        }

        // 2. Jika belum ada mta_warga_uuid, cari berdasarkan nama di API MTA
        if (!$wargaData) {
            $searchRes = $this->apiService->searchWarga($pemuda['name'], ['limit' => 5]);
            if (($searchRes['success'] ?? false) && !empty($searchRes['data']) && is_array($searchRes['data'])) {
                // Cari match yang paling sesuai (nama sama persis atau no HP sama)
                foreach ($searchRes['data'] as $candidate) {
                    if (!empty($pemuda['phone']) && !empty($candidate['nohp']) && $pemuda['phone'] === $candidate['nohp']) {
                        $wargaData = $candidate;
                        break;
                    }
                    if (strcasecmp(trim($candidate['nama']), trim($pemuda['name'])) === 0) {
                        $wargaData = $candidate;
                        break;
                    }
                }

                if (!$wargaData && count($searchRes['data']) === 1) {
                    $wargaData = $searchRes['data'][0];
                }
            }
        }

        if (!$wargaData) {
            return [
                'success' => false,
                'message' => 'Data warga dengan nama "' . $pemuda['name'] . '" tidak ditemukan di Database Warga MTA.',
            ];
        }

        // Jalankan sinkronisasi
        $result = $this->syncWargaToPemuda($wargaData, (int) $pemuda['cabang_id']);
        if ($result['success']) {
            $this->logModel->log('warga', 'success', 1, "Sinkronisasi live pemuda ID #{$pemudaId} ({$pemuda['name']}) berhasil.");
        }

        return $result;
    }

    /**
     * Verifikasi & Cek apakah data pemuda yang diinput sudah ada di Database Warga MTA Pusat (Perwakilan Sragen).
     * Jika ada -> kembalikan status 'verified' beserta data warga MTA.
     * Jika tidak ada -> kembalikan status 'pending'.
     *
     * @param array $inputData Data input pemuda [name, phone, birth_date, gender, cabang_id, mta_warga_uuid]
     * @return array
     */
    public function verifyYouthAgainstMta(array $inputData): array
    {
        $name         = trim($inputData['name'] ?? '');
        $phone        = !empty($inputData['phone']) ? preg_replace('/[^0-9]/', '', $inputData['phone']) : '';
        $birthDate    = !empty($inputData['birth_date']) ? date('Y-m-d', strtotime($inputData['birth_date'])) : '';
        $mtaWargaUuid = $inputData['mta_warga_uuid'] ?? '';

        // 1. Jika mta_warga_uuid sudah ada (misal dipilih dari modal lookup), verifikasi langsung
        if (!empty($mtaWargaUuid)) {
            $detailRes = $this->apiService->getWargaDetail($mtaWargaUuid);
            if (($detailRes['success'] ?? false) && !empty($detailRes['data'])) {
                return [
                    'verified'   => true,
                    'status'     => 'verified',
                    'match_type' => 'uuid',
                    'warga'      => $detailRes['data'],
                ];
            }
            if (($detailRes['statusCode'] ?? 0) === 429) {
                return [
                    'verified'     => false,
                    'status'       => 'pending',
                    'rate_limited' => true,
                    'error'        => $detailRes['message'] ?? 'Batas kuota request tercapai (Rate limit 60 req/menit).',
                    'warga'        => null,
                ];
            }
        }

        if (empty($name) || strlen($name) < 2) {
            return [
                'verified' => false,
                'status'   => 'pending',
                'warga'    => null,
            ];
        }

        // 2. Cari di MTA Pusat (khusus Perwakilan Sragen)
        $searchRes = $this->apiService->searchWarga($name, [
            'limit' => 10,
        ]);

        if (!($searchRes['success'] ?? false)) {
            if (($searchRes['statusCode'] ?? 0) === 429) {
                return [
                    'verified'     => false,
                    'status'       => 'pending',
                    'rate_limited' => true,
                    'error'        => $searchRes['message'] ?? 'Batas kuota request tercapai (Rate limit 60 req/menit).',
                    'warga'        => null,
                ];
            }
            return [
                'verified' => false,
                'status'   => 'pending',
                'error'    => $searchRes['message'] ?? 'Gagal mencari warga di API MTA',
                'warga'    => null,
            ];
        }

        if (empty($searchRes['data'])) {
            return [
                'verified' => false,
                'status'   => 'pending',
                'warga'    => null,
            ];
        }

        $cleanInputName = strtolower((string) preg_replace('/[^a-z0-9]/i', '', $name));

        // Cocokkan kandidat
        foreach ($searchRes['data'] as $candidate) {
            $candidateName  = strtolower((string) preg_replace('/[^a-z0-9]/i', '', $candidate['nama'] ?? ''));
            $candidatePhone = !empty($candidate['nohp']) ? preg_replace('/[^0-9]/', '', $candidate['nohp']) : '';
            $candidateBirth = !empty($candidate['lahir']) ? date('Y-m-d', strtotime($candidate['lahir'])) : '';

            // Jika nama memiliki kecocokan tinggi
            if ($cleanInputName === $candidateName || strcasecmp(trim($candidate['nama'] ?? ''), $name) === 0) {
                // Kasus 1: Nomor HP cocok (sub-string 8 digit terakhir untuk toleransi format 08xx vs +62xx)
                if (!empty($phone) && !empty($candidatePhone)) {
                    $p1 = substr($phone, -8);
                    $p2 = substr($candidatePhone, -8);
                    if ($p1 === $p2) {
                        return [
                            'verified'   => true,
                            'status'     => 'verified',
                            'match_type' => 'name_and_phone',
                            'warga'      => $candidate,
                        ];
                    }
                }

                // Kasus 2: Tanggal lahir cocok
                if (!empty($birthDate) && !empty($candidateBirth) && $birthDate === $candidateBirth) {
                    return [
                        'verified'   => true,
                        'status'     => 'verified',
                        'match_type' => 'name_and_birthdate',
                        'warga'      => $candidate,
                    ];
                }

                // Kasus 3: Jika kandidat hanya ada 1 orang di Sragen dengan nama yang sama persis
                if (count($searchRes['data']) === 1) {
                    return [
                        'verified'   => true,
                        'status'     => 'verified',
                        'match_type' => 'exact_name_single_match',
                        'warga'      => $candidate,
                    ];
                }
            }
        }

        return [
            'verified' => false,
            'status'   => 'pending',
            'warga'    => null,
        ];
    }

    /**
     * Inisialisasi Antrian Sinkronisasi Data Pemuda ke MTA Pusat
     * Mengisi tabel `mta_sync_queue` dengan pemuda yang akan disinkronkan.
     *
     * @param int|null $cabangId ID cabang (opsional)
     * @param bool     $onlyPending Hanya pemuda berstatus pending
     * @param int|null $userId User yang memulai antrian
     * @param bool     $clearExisting Hapus antrian lama jika ada
     * @return array
     */
    public function initSyncQueue(?int $cabangId = null, bool $onlyPending = true, ?int $userId = null, bool $clearExisting = true): array
    {
        if ($clearExisting) {
            $this->queueModel->emptyTable();
        }

        $builder = $this->pemudaModel->builder();
        $builder->select('id, cabang_id, name, registration_number');
        $builder->where('status_data', 'active');

        if ($cabangId) {
            $builder->where('cabang_id', $cabangId);
        }
        if ($onlyPending) {
            $builder->where('status_verifikasi', 'pending');
        }

        $pemudaList = $builder->get()->getResultArray();
        $total = count($pemudaList);

        if ($total === 0) {
            return [
                'success' => false,
                'message' => 'Tidak ditemukan data pemuda yang memenuhi kriteria untuk disinkronisasi.',
                'total'   => 0,
                'summary' => $this->queueModel->getQueueSummary(),
            ];
        }

        $now = date('Y-m-d H:i:s');
        $batchData = [];
        foreach ($pemudaList as $p) {
            $batchData[] = [
                'pemuda_id'  => (int) $p['id'],
                'cabang_id'  => (int) $p['cabang_id'],
                'status'     => 'pending',
                'result'     => 'pending',
                'created_by' => $userId ?? (session()->get('user_id') ?? null),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Insert batch secara bertahap (500 data per batch)
        foreach (array_chunk($batchData, 500) as $chunk) {
            $this->queueModel->insertBatch($chunk);
        }

        $summary = $this->queueModel->getQueueSummary();

        return [
            'success'        => true,
            'message'        => "Antrian sinkronisasi berhasil disiapkan sebanyak {$total} data pemuda.",
            'total'          => $total,
            'rate_limit'     => '40 data / menit (1 data per 1.5 detik)',
            'delay_ms'       => 1500,
            'estimated_time' => $summary['estimated_formatted'],
            'summary'        => $summary,
        ];
    }

    /**
     * Proses Satu Item Antrian Berikutnya (Laju: 40 data / menit = 1500ms delay)
     *
     * @param int|null $userId User ID pemroses
     * @return array Status dan hasil pemrosesan item
     */
    public function processNextQueueItem(?int $userId = null): array
    {
        $item = $this->queueModel->getNextPendingItem();

        if (!$item) {
            $summary = $this->queueModel->getQueueSummary();
            if ($summary['remaining'] === 0 && $summary['processed'] > 0) {
                $this->logModel->log(
                    'warga',
                    'success',
                    $summary['verified'],
                    "Antrian sinkronisasi 40 data/menit selesai. Total: {$summary['total']}, Terverifikasi: {$summary['verified']}, Belum Terverifikasi: {$summary['pending_unverified']}.",
                    $userId
                );
            }

            return [
                'success'  => true,
                'finished' => true,
                'message'  => 'Seluruh antrian sinkronisasi telah selesai diproses.',
                'summary'  => $summary,
            ];
        }

        // 1. Tandai item sedang diproses & tambah percobaan
        $this->queueModel->update($item['id'], [
            'status'   => 'processing',
            'attempts' => (int) $item['attempts'] + 1,
        ]);

        $pemuda = $this->pemudaModel->find($item['pemuda_id']);
        if (!$pemuda) {
            $this->queueModel->update($item['id'], [
                'status'       => 'failed',
                'result'       => 'error',
                'message'      => 'Data pemuda ID #' . $item['pemuda_id'] . ' tidak ditemukan.',
                'processed_at' => date('Y-m-d H:i:s'),
            ]);

            return [
                'success'      => false,
                'finished'     => false,
                'rate_limited' => false,
                'item'         => [
                    'id'          => $item['id'],
                    'pemuda_id'   => $item['pemuda_id'],
                    'name'        => $item['name'] ?? '-',
                    'cabang_name' => $item['cabang_name'] ?? '',
                    'status'      => 'failed',
                    'result'      => 'error',
                    'message'     => 'Data pemuda tidak ditemukan.',
                ],
                'summary'      => $this->queueModel->getQueueSummary(),
                'delay_ms'     => 1500,
            ];
        }

        // 2. Verifikasi terhadap database MTA Pusat
        $check = $this->verifyYouthAgainstMta($pemuda);

        // Kasus: Terkena Rate Limiting API (HTTP 429)
        if (!empty($check['rate_limited'])) {
            // Kembalikan ke antrian pending agar tidak gagal permanen
            $this->queueModel->update($item['id'], [
                'status'  => 'pending',
                'message' => 'Menunggu jeda kuota API MTA (HTTP 429)',
            ]);

            return [
                'success'      => false,
                'finished'     => false,
                'rate_limited' => true,
                'retry_after'  => 10,
                'message'      => 'Batas kuota API MTA Pusat terdeteksi (HTTP 429). Antrian dijeda otomatis 10 detik...',
                'item'         => [
                    'id'          => $item['id'],
                    'pemuda_id'   => $pemuda['id'],
                    'name'        => $pemuda['name'],
                    'cabang_name' => $item['cabang_name'] ?? '',
                    'status'      => 'pending',
                    'result'      => 'pending',
                    'message'     => 'Menunggu cooldown API Pusat',
                ],
                'summary'      => $this->queueModel->getQueueSummary(),
                'delay_ms'     => 10000,
            ];
        }

        if ($check['verified'] && !empty($check['warga'])) {
            $warga = $check['warga'];
            $updateData = [
                'status_verifikasi' => 'verified',
                'mta_warga_uuid'    => $warga['uuid'] ?? ($pemuda['mta_warga_uuid'] ?? null),
                'mta_status_warga'  => $warga['status'] ?? ($pemuda['mta_status_warga'] ?? 'Warga'),
                'mta_synced_at'     => date('Y-m-d H:i:s'),
            ];

            if (!empty($warga['ayah_uuid']) && empty($pemuda['mta_ayah_uuid'])) {
                $updateData['mta_ayah_uuid'] = $warga['ayah_uuid'];
            }
            if (!empty($warga['ibu_uuid']) && empty($pemuda['mta_ibu_uuid'])) {
                $updateData['mta_ibu_uuid'] = $warga['ibu_uuid'];
            }
            if (!empty($warga['foto']) && empty($pemuda['mta_foto_url'])) {
                $updateData['mta_foto_url'] = $warga['foto'];
            }

            $this->pemudaModel->update($pemuda['id'], $updateData);

            $this->queueModel->update($item['id'], [
                'status'         => 'completed',
                'result'         => 'verified',
                'mta_warga_uuid' => $warga['uuid'] ?? null,
                'message'        => 'Terverifikasi di MTA Pusat (' . ($check['match_type'] ?? 'Match') . ')',
                'processed_at'   => date('Y-m-d H:i:s'),
            ]);

            $resultItem = [
                'id'           => $item['id'],
                'pemuda_id'    => $pemuda['id'],
                'name'         => $pemuda['name'],
                'cabang_name'  => $item['cabang_name'] ?? '',
                'status'       => 'completed',
                'result'       => 'verified',
                'result_label' => 'Terverifikasi',
                'message'      => 'Terverifikasi di MTA Pusat (' . ($check['match_type'] ?? 'Match') . ')',
                'uuid'         => $warga['uuid'] ?? null,
                'processed_at' => date('H:i:s'),
            ];
        } else {
            // Sesuai aturan: jika belum/tidak terdata di MTA Pusat, status_verifikasi tetap 'pending'
            if (empty($pemuda['mta_warga_uuid'])) {
                $this->pemudaModel->update($pemuda['id'], ['status_verifikasi' => 'pending']);
            }

            $this->queueModel->update($item['id'], [
                'status'       => 'completed',
                'result'       => 'pending',
                'message'      => 'Belum terdata di Database Warga MTA Pusat',
                'processed_at' => date('Y-m-d H:i:s'),
            ]);

            $resultItem = [
                'id'           => $item['id'],
                'pemuda_id'    => $pemuda['id'],
                'name'         => $pemuda['name'],
                'cabang_name'  => $item['cabang_name'] ?? '',
                'status'       => 'completed',
                'result'       => 'pending',
                'result_label' => 'Menunggu Verifikasi',
                'message'      => 'Belum terdata di MTA Pusat',
                'uuid'         => null,
                'processed_at' => date('H:i:s'),
            ];
        }

        return [
            'success'      => true,
            'finished'     => false,
            'rate_limited' => false,
            'item'         => $resultItem,
            'summary'      => $this->queueModel->getQueueSummary(),
            'delay_ms'     => 1500, // 40 data / menit = 1500 ms delay aman
        ];
    }

    /**
     * Dapatkan Status dan Ringkasan Antrian Saat Ini
     */
    public function getQueueStatus(): array
    {
        return [
            'summary'          => $this->queueModel->getQueueSummary(),
            'recent_processed' => $this->queueModel->getRecentProcessed(20),
        ];
    }

    /**
     * Batalkan Antrian yang Tersisa
     */
    public function cancelQueue(?int $userId = null): array
    {
        $this->queueModel->clearPendingQueue();
        $summary = $this->queueModel->getQueueSummary();

        $this->logModel->log(
            'warga',
            'success',
            $summary['processed'],
            "Antrian sinkronisasi dibatalkan oleh pengguna. Total tersisa dibersihkan.",
            $userId
        );

        return [
            'success' => true,
            'message' => 'Antrian yang tersisa berhasil dibatalkan dan dibersihkan.',
            'summary' => $summary,
        ];
    }

    /**
     * Sinkronisasi dan Verifikasi Seluruh Pemuda Sragen dengan Laju Terkendali (40 data / menit)
     *
     * @param int|null $cabangId Batasi ke cabang tertentu (opsional)
     * @param bool     $onlyPending Hanya proses yang masih berstatus pending
     * @return array
     */
    public function syncAndVerifyAllPemudaSragen(?int $cabangId = null, bool $onlyPending = false): array
    {
        $builder = $this->pemudaModel->builder();
        if ($cabangId) {
            $builder->where('cabang_id', $cabangId);
        }
        if ($onlyPending) {
            $builder->where('status_verifikasi', 'pending');
        }

        $pemudaList = $builder->get()->getResultArray();
        $total = count($pemudaList);
        $verifiedCount = 0;
        $pendingCount = 0;
        $newlyVerified = 0;

        foreach ($pemudaList as $index => $pemuda) {
            // Laju antrian: jeda 1.5 detik per data (40 data / menit) untuk mencegah terlampauinya limit 60 req/menit
            if ($index > 0) {
                usleep(1500000); // 1.500.000 mikrodetik = 1.5 detik
            }

            $check = $this->verifyYouthAgainstMta($pemuda);

            // Jika terkena batas 429 pada eksekusi langsung, beri jeda pendinginan dan ulangi 1 kali
            if (!empty($check['rate_limited'])) {
                sleep(10);
                $check = $this->verifyYouthAgainstMta($pemuda);
            }

            if ($check['verified'] && !empty($check['warga'])) {
                $warga = $check['warga'];
                $updateData = [
                    'status_verifikasi' => 'verified',
                    'mta_warga_uuid'    => $warga['uuid'] ?? ($pemuda['mta_warga_uuid'] ?? null),
                    'mta_status_warga'  => $warga['status'] ?? ($pemuda['mta_status_warga'] ?? 'Warga'),
                    'mta_synced_at'     => date('Y-m-d H:i:s'),
                ];

                if (!empty($warga['ayah_uuid']) && empty($pemuda['mta_ayah_uuid'])) {
                    $updateData['mta_ayah_uuid'] = $warga['ayah_uuid'];
                }
                if (!empty($warga['ibu_uuid']) && empty($pemuda['mta_ibu_uuid'])) {
                    $updateData['mta_ibu_uuid'] = $warga['ibu_uuid'];
                }
                if (!empty($warga['foto']) && empty($pemuda['mta_foto_url'])) {
                    $updateData['mta_foto_url'] = $warga['foto'];
                }

                if ($pemuda['status_verifikasi'] !== 'verified') {
                    $newlyVerified++;
                }

                $this->pemudaModel->update($pemuda['id'], $updateData);
                $verifiedCount++;
            } else {
                // Jika tidak ditemukan di pusat dan tidak memiliki mta_warga_uuid
                if (empty($pemuda['mta_warga_uuid'])) {
                    $this->pemudaModel->update($pemuda['id'], ['status_verifikasi' => 'pending']);
                }
                $pendingCount++;
            }
        }

        $msg = "Sinkronisasi & Verifikasi selesai (Laju 40 data/menit). Total diperiksa: {$total}. Terverifikasi di MTA Pusat: {$verifiedCount} (Baru diverifikasi: {$newlyVerified}), Belum Terverifikasi: {$pendingCount}.";
        $this->logModel->log('warga', 'success', $verifiedCount, $msg);

        return [
            'success'        => true,
            'message'        => $msg,
            'total'          => $total,
            'verified_count' => $verifiedCount,
            'newly_verified' => $newlyVerified,
            'pending_count'  => $pendingCount,
            'rate_limit'     => '40 data / menit',
        ];
    }
}
