<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\UserModel;
use App\Models\UserRoleModel;
use App\Models\PemudaModel;
use Config\Database;

/**
 * @internal
 */
final class SuperAdminDashboardTest extends CIUnitTestCase
{
    public function testUserModelStructure(): void
    {
        $userModel = new UserModel();
        $this->assertNotEmpty($userModel->allowedFields);
        $this->assertContains('username', $userModel->allowedFields);
        $this->assertContains('role_id', $userModel->allowedFields);
    }

    public function testUserRoleModelStructure(): void
    {
        $roleModel = new UserRoleModel();
        $this->assertSame('user_roles', $roleModel->getTable());
    }

    public function testPemudaModelStatsMethod(): void
    {
        $db = Database::connect('default');
        $pemudaModel = new PemudaModel($db);
        $stats = $pemudaModel->getDashboardStats();

        $this->assertArrayHasKey('summary', $stats);
        $this->assertArrayHasKey('totalWilayah', $stats);
        $this->assertArrayHasKey('totalCabang', $stats);
        $this->assertArrayHasKey('totalUsers', $stats);
        $this->assertArrayHasKey('genderData', $stats);
        $this->assertArrayHasKey('maritalData', $stats);
        $this->assertArrayHasKey('wilayahStats', $stats);
        $this->assertArrayHasKey('topCabangStats', $stats);
        $this->assertArrayHasKey('educationStats', $stats);
        $this->assertArrayHasKey('jobStats', $stats);
    }
}
