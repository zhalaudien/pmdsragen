<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\PemudaModel;
use App\Controllers\Admin\Pemuda;
use Config\Database;

/**
 * @internal
 */
final class VerificationPolicyTest extends CIUnitTestCase
{
    public function testOnlyTwoVerificationStatusesInSummary(): void
    {
        $db = Database::connect('default');
        $model = new PemudaModel($db);
        $summary = $model->getCountsSummary();

        $this->assertArrayHasKey('verified', $summary);
        $this->assertArrayHasKey('pending', $summary);
        $this->assertEquals(0, $summary['rejected']);
    }

    public function testPemudaControllerDoesNotAllowArbitraryStatusOverride(): void
    {
        $reflection = new \ReflectionClass(Pemuda::class);
        $this->assertTrue($reflection->hasMethod('verifikasi'));

        $method = $reflection->getMethod('verifikasi');
        $this->assertCount(1, $method->getParameters());
        $this->assertEquals('id', $method->getParameters()[0]->getName());
    }

    public function testDatabaseContainsOnlyTwoStatuses(): void
    {
        $db = Database::connect('default');
        $rejectedCount = $db->table('pemuda')->where('status_verifikasi', 'rejected')->countAllResults();
        $this->assertSame(0, $rejectedCount);
    }
}
