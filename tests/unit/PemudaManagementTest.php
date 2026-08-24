<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\PemudaModel;
use App\Models\UserModel;
use Config\Database;

/**
 * @internal
 */
final class PemudaManagementTest extends CIUnitTestCase
{
    public function testRegistrationNumberFormat(): void
    {
        $db = Database::connect('default');
        $pemudaModel = new PemudaModel($db);
        $regNumber = $pemudaModel->generateRegistrationNumber();

        $this->assertStringStartsWith('PMD-' . date('Ymd') . '-', $regNumber);
        $this->assertSame(17, strlen($regNumber));
    }

    public function testUserPasswordHashingAndVerification(): void
    {
        $password = 'admin123';
        $hashed   = password_hash($password, PASSWORD_DEFAULT);

        $this->assertTrue(password_verify($password, $hashed));
        $this->assertFalse(password_verify('wrongpassword', $hashed));
    }

    public function testScopeFilteringForAdminWilayah(): void
    {
        $db = Database::connect('default');
        $pemudaModel = new PemudaModel($db);

        $scope = [
            'role'       => 'admin_wilayah',
            'wilayah_id' => 1,
        ];

        $pemudaModel->getFilteredQuery([], $scope);
        $sql = $pemudaModel->builder()->getCompiledSelect(false);

        $this->assertStringContainsString('cabang', $sql);
    }

    public function testScopeFilteringForAdminCabang(): void
    {
        $db = Database::connect('default');
        $pemudaModel = new PemudaModel($db);

        $scope = [
            'role'      => 'admin_cabang',
            'cabang_id' => 1,
        ];

        $pemudaModel->getFilteredQuery([], $scope);
        $sql = $pemudaModel->builder()->getCompiledSelect(false);

        $this->assertStringContainsString('pemuda', $sql);
    }
}
