<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\PemudaBackupService;
use App\Models\PemudaModel;
use App\Models\UserModel;
use Config\Database;

/**
 * @internal
 */
final class PemudaBackupTest extends CIUnitTestCase
{
    protected PemudaBackupService $backupService;
    protected $db;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db            = Database::connect('default');
        $this->backupService = new PemudaBackupService();
    }

    public function testEnsureBackupDirectoryExists(): void
    {
        $dir = $this->backupService->getBackupDir();
        $this->assertDirectoryExists($dir);
        $this->assertFileExists($dir . 'index.html');
        $this->assertFileExists($dir . '.htaccess');
    }

    public function testGenerateSqlBackupStructure(): void
    {
        $backup = $this->backupService->generateSqlBackup(false);

        $this->assertIsArray($backup);
        $this->assertArrayHasKey('filename', $backup);
        $this->assertArrayHasKey('content', $backup);
        $this->assertArrayHasKey('size', $backup);
        $this->assertArrayHasKey('counts', $backup);

        $this->assertStringEndsWith('.sql', $backup['filename']);
        $this->assertStringStartsWith('backup_pemuda_', $backup['filename']);

        $content = $backup['content'];
        $this->assertStringContainsString('BACKUP DATA PEMUDA MTA KABUPATEN SRAGEN', $content);
        $this->assertStringContainsString('SET FOREIGN_KEY_CHECKS = 0;', $content);
        $this->assertStringContainsString('SET FOREIGN_KEY_CHECKS = 1;', $content);
    }

    public function testGenerateJsonBackupStructure(): void
    {
        $backup = $this->backupService->generateJsonBackup(false);

        $this->assertIsArray($backup);
        $this->assertArrayHasKey('filename', $backup);
        $this->assertArrayHasKey('content', $backup);
        $this->assertArrayHasKey('size', $backup);
        $this->assertArrayHasKey('counts', $backup);

        $this->assertStringEndsWith('.json', $backup['filename']);
        $this->assertStringStartsWith('backup_pemuda_', $backup['filename']);

        $decoded = json_decode($backup['content'], true);
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('metadata', $decoded);
        $this->assertArrayHasKey('pemuda_records', $decoded);
        $this->assertArrayHasKey('raw_tables', $decoded);
        $this->assertSame('Sistem Pendataan Pemuda MTA Kabupaten Sragen', $decoded['metadata']['app_name']);
    }

    public function testGenerateXlsxBackupStructure(): void
    {
        $backup = $this->backupService->generateXlsxBackup(false);

        $this->assertIsArray($backup);
        $this->assertArrayHasKey('filename', $backup);
        $this->assertArrayHasKey('spreadsheet', $backup);
        $this->assertArrayHasKey('counts', $backup);
        $this->assertStringEndsWith('.xlsx', $backup['filename']);
        $this->assertInstanceOf(\PhpOffice\PhpSpreadsheet\Spreadsheet::class, $backup['spreadsheet']);
    }

    public function testSaveAndListAndSafeDeleteBackupFile(): void
    {
        // 1. Simpan backup file ke server
        $backup = $this->backupService->generateSqlBackup(true, 'test_backup_');
        $filename = $backup['filename'];
        $this->assertNotNull($backup['filepath']);
        $this->assertFileExists($backup['filepath']);

        // 2. Ambil list backup dan pastikan file ada di list
        $list = $this->backupService->getBackupList();
        $this->assertNotEmpty($list);

        $found = false;
        foreach ($list as $item) {
            if ($item['filename'] === $filename) {
                $found = true;
                $this->assertSame('SQL', $item['extension']);
                $this->assertGreaterThan(0, $item['size_bytes']);
                break;
            }
        }
        $this->assertTrue($found, "File {$filename} harus ditemukan dalam daftar berkas backup.");

        // 3. Test path resolution aman dari directory traversal
        $this->assertNull($this->backupService->getBackupFilePath('../../etc/passwd'));
        $this->assertNull($this->backupService->getBackupFilePath('../malicious.php'));
        $this->assertNull($this->backupService->getBackupFilePath('non_existent.sql'));
        $this->assertNotNull($this->backupService->getBackupFilePath($filename));

        // 4. Hapus file backup
        $deleted = $this->backupService->deleteBackupFile($filename);
        $this->assertTrue($deleted);
        $this->assertFileDoesNotExist($backup['filepath']);
    }

    public function testDeleteAllYouthDataRequiresValidSuperadminPassword(): void
    {
        // Ambil salah satu user superadmin dari database
        $userModel = new UserModel($this->db);
        $superadmin = $userModel->where('role_id', 1)->first();

        if ($superadmin) {
            // Test 1: Password salah harus ditolak
            $resultFail = $this->backupService->deleteAllYouthData((int) $superadmin['id'], 'password_salah_12345');
            $this->assertFalse($resultFail['success']);
            $this->assertStringContainsString('Password konfirmasi salah', $resultFail['message']);

            // Test 2: User tidak ada harus ditolak
            $resultNoUser = $this->backupService->deleteAllYouthData(999999, 'any_password');
            $this->assertFalse($resultNoUser['success']);
            $this->assertStringContainsString('tidak ditemukan', $resultNoUser['message']);
        }
    }

    public function testDeleteAllYouthDataWorkflowWithDummyYouth(): void
    {
        $userModel = new UserModel($this->db);
        $superadmin = $userModel->where('role_id', 1)->first();

        if (!$superadmin) {
            $this->markTestSkipped('Tidak ada user superadmin di database untuk pengujian.');
        }

        // Set password yang diketahui untuk pengujian pada user superadmin sementara
        $testPassword = 'TestSuperAdminPass123!';
        $originalHash = $superadmin['password'];
        $this->db->table('users')->where('id', $superadmin['id'])->update([
            'password' => password_hash($testPassword, PASSWORD_DEFAULT),
        ]);

        try {
            // Masukkan 1 data pemuda dummy dan relasinya
            $cabang = $this->db->table('cabang')->get()->getRowArray();
            $cabangId = $cabang ? (int) $cabang['id'] : 1;

            $pemudaModel = new PemudaModel($this->db);
            $this->db->table('pemuda')->where('registration_number', '9999999999999999')->delete();
            $dummyRegNum = 'T' . time() . random_int(100, 999);

            $this->db->table('pemuda')->insert([
                'cabang_id'           => $cabangId,
                'registration_number' => $dummyRegNum,
                'name'                => 'pemuda uji coba reset',
                'gender'              => 'L',
                'marital_status'      => 'belum_menikah',
                'birth_date'          => '2000-01-01',
                'phone'               => '081234567899',
                'status_verifikasi'   => 'pending',
                'status_data'         => 'active',
            ]);
            $dummyId = $this->db->insertID();

            $district = $this->db->table('districts')->get()->getRowArray();
            $village  = $this->db->table('villages')->get()->getRowArray();

            $this->db->table('alamat')->insert([
                'pemuda_id'      => $dummyId,
                'province_id'    => 33,
                'regency_id'     => 3314,
                'district_id'    => $district ? (int) $district['id'] : 1,
                'village_id'     => $village ? (int) $village['id'] : 1,
                'address_detail' => 'alamat dummy test reset',
            ]);

            $this->db->table('organisasi')->insert([
                'pemuda_id'         => $dummyId,
                'organization_name' => 'satgas test',
            ]);

            $totalBefore = $this->db->table('pemuda')->countAllResults();
            $this->assertGreaterThan(0, $totalBefore);

            // Eksekusi penghapusan seluruh data pemuda
            $result = $this->backupService->deleteAllYouthData((int) $superadmin['id'], $testPassword);

            $this->assertTrue($result['success'], $result['message'] ?? 'Failed');
            $this->assertArrayHasKey('deleted_counts', $result);
            $this->assertArrayHasKey('auto_backup', $result);

            // Pastikan auto-backup dibuat di server
            if (!empty($result['auto_backup'])) {
                $autoBackupPath = $this->backupService->getBackupFilePath($result['auto_backup']);
                $this->assertNotNull($autoBackupPath);
                $this->assertFileExists($autoBackupPath);

                // Bersihkan auto backup pengujian
                $this->backupService->deleteBackupFile($result['auto_backup']);
            }

            // Pastikan tabel pemuda dan relasi kosong
            $totalAfter = $this->db->table('pemuda')->countAllResults();
            $this->assertSame(0, $totalAfter);

            $totalAlamatAfter = $this->db->table('alamat')->countAllResults();
            $this->assertSame(0, $totalAlamatAfter);

            $totalOrgAfter = $this->db->table('organisasi')->countAllResults();
            $this->assertSame(0, $totalOrgAfter);

            // Test saat database sudah kosong: tidak boleh error, menginfokan tidak ada data
            $emptyResult = $this->backupService->deleteAllYouthData((int) $superadmin['id'], $testPassword);
            $this->assertFalse($emptyResult['success']);
            $this->assertStringContainsString('Tidak ada data pemuda', $emptyResult['message']);

        } finally {
            // Pulihkan password asli superadmin
            $this->db->table('users')->where('id', $superadmin['id'])->update(['password' => $originalHash]);
        }
    }
}
