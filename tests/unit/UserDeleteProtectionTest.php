<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\PemudaModel;
use App\Models\UserModel;
use App\Controllers\Admin\Users;
use Config\Database;
use Config\Services;

/**
 * @internal
 */
final class UserDeleteProtectionTest extends CIUnitTestCase
{
    public function testDeleteUserDoesNotDeletePemudaData(): void
    {
        $db = Database::connect('default');
        $userModel = new UserModel($db);
        $pemudaModel = new PemudaModel($db);

        // 1. Buat dummy user untuk pengujian via DB builder
        $testUsername = 'test_creator_' . time();
        $db->table('users')->insert([
            'name'       => 'Test Creator User',
            'email'      => $testUsername . '@example.com',
            'username'   => $testUsername,
            'password'   => password_hash('password123', PASSWORD_DEFAULT),
            'role_id'    => 2, // admin_wilayah
            'wilayah_id' => 1,
            'status'     => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        $dummyUserId = $db->insertID();

        $this->assertIsNumeric($dummyUserId);
        $this->assertGreaterThan(0, $dummyUserId);

        // 2. Buat dummy pemuda yang dibuat oleh dummy user tersebut
        $regNumber = 'TEST' . time() . rand(100, 999);
        $db->table('pemuda')->insert([
            'cabang_id'           => 1,
            'registration_number' => $regNumber,
            'name'                => 'Pemuda Terlindungi ' . time(),
            'gender'              => 'L',
            'birth_date'          => '2000-01-01',
            'phone'               => '081299998888',
            'status_verifikasi'   => 'pending',
            'status_data'         => 'active',
            'created_by'          => $dummyUserId,
            'created_at'          => date('Y-m-d H:i:s'),
        ]);
        $testPemudaId = $db->insertID();

        $this->assertIsNumeric($testPemudaId);
        $this->assertGreaterThan(0, $testPemudaId);

        // Pastikan pemuda tercatat dengan created_by = dummyUserId
        $pemudaBefore = $db->table('pemuda')->where('id', $testPemudaId)->get()->getRowArray();
        $this->assertNotNull($pemudaBefore);
        $this->assertEquals($dummyUserId, $pemudaBefore['created_by']);

        // 3. Hapus user langsung dari tabel users di database MySQL
        // Ini akan memicu foreign key pemuda_created_by_foreign pada MySQL
        $db->table('users')->where('id', $dummyUserId)->delete();

        // 4. Verifikasi user berhasil dihapus dari database
        $deletedUser = $db->table('users')->where('id', $dummyUserId)->get()->getRowArray();
        $this->assertNull($deletedUser, 'User harus terhapus dari tabel users.');

        // 5. CRITICAL CHECK: Pastikan data pemuda TIDAK ikut terhapus oleh MySQL!
        $pemudaAfter = $db->table('pemuda')->where('id', $testPemudaId)->get()->getRowArray();
        $this->assertNotNull($pemudaAfter, 'DATA PEMUDA TIDAK BOLEH TERHAPUS KETIKA USER DIHAPUS!');
        $this->assertStringStartsWith('Pemuda Terlindungi', $pemudaAfter['name']);

        // 6. Pastikan created_by berubah menjadi NULL di MySQL
        $this->assertNull($pemudaAfter['created_by'], 'Kolom created_by pada pemuda harus menjadi NULL (ON DELETE SET NULL).');

        // Clean up data pemuda dummy
        $db->table('pemuda')->where('id', $testPemudaId)->delete();
    }

    public function testDatabaseSchemaHasOnDeleteSetNull(): void
    {
        $db = Database::connect('default');
        if ($db->DBDriver === 'MySQLi') {
            $query = $db->query("
                SELECT 
                    CONSTRAINT_NAME, 
                    TABLE_NAME, 
                    UPDATE_RULE, 
                    DELETE_RULE 
                FROM 
                    INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS 
                WHERE 
                    CONSTRAINT_SCHEMA = '" . $db->getDatabase() . "' 
                    AND TABLE_NAME = 'pemuda'
                    AND REFERENCED_TABLE_NAME = 'users'
            ");
            $row = $query->getRowArray();

            $this->assertNotNull($row, 'Foreign key dari pemuda ke users harus ada.');
            $this->assertEquals('SET NULL', $row['DELETE_RULE'], 'DELETE_RULE pada pemuda_created_by_foreign WAJIB SET NULL!');
            $this->assertEquals('CASCADE', $row['UPDATE_RULE'], 'UPDATE_RULE pada pemuda_created_by_foreign harus CASCADE.');
        }
    }
}
