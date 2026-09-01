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

        // 1. Test default (tanpa parameter)
        $regNumber = $pemudaModel->generateRegistrationNumber();
        $this->assertSame(16, strlen($regNumber));
        $this->assertMatchesRegularExpression('/^86\d{2}\d{8}\d{4}$/', $regNumber);
        $this->assertStringStartsWith('8601' . date('Ymd'), $regNumber);

        // 2. Test dengan Cabang Gemolong 1 (kode: 86.1 -> 01) dan tanggal lahir 2000-05-17
        $cabangModel = new \App\Models\CabangModel($db);
        $cbgGemolong = $cabangModel->where('code', '86.1')->first();
        if ($cbgGemolong) {
            $regGemolong = $pemudaModel->generateRegistrationNumber((int) $cbgGemolong['id'], '2000-05-17');
            $this->assertSame(16, strlen($regGemolong));
            $this->assertStringStartsWith('860120000517', $regGemolong);
            $this->assertMatchesRegularExpression('/^860120000517\d{4}$/', $regGemolong);
        }

        // 3. Test dengan Cabang Gesi (kode: 86.6 -> 06) dan tanggal lahir 1998-12-30
        $cbgGesi = $cabangModel->where('code', '86.6')->first();
        if ($cbgGesi) {
            $regGesi = $pemudaModel->generateRegistrationNumber((int) $cbgGesi['id'], '1998-12-30');
            $this->assertSame(16, strlen($regGesi));
            $this->assertStringStartsWith('860619981230', $regGesi);
            $this->assertMatchesRegularExpression('/^860619981230\d{4}$/', $regGesi);
        }
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
