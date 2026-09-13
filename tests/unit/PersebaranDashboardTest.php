<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\PemudaModel;
use App\Models\WilayahModel;
use App\Models\CabangModel;
use Config\Database;

/**
 * @internal
 */
final class PersebaranDashboardTest extends CIUnitTestCase
{
    public function testGetPersebaranStatsStructure(): void
    {
        $db = Database::connect('default');
        $pemudaModel = new PemudaModel($db);

        $stats = $pemudaModel->getPersebaranStats(['role' => 'superadmin']);

        $expectedKeys = [
            'totalYouth',
            'genderData',
            'totalWithOrg',
            'totalWithoutOrg',
            'orgStats',
            'eduLevelStats',
            'eduStatusData',
            'topSchools',
            'topMajors',
            'topSkills',
            'skillLevelData',
            'totalWithSkill',
            'totalWithoutSkill',
            'topInterests',
            'jobStats',
            'totalWirausaha',
            'topBizFields',
            'ageData',
            'avgAge',
            'districtStats',
            'wilayahStats',
            'topCabangStats',
        ];

        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $stats, "Missing expected key: {$key}");
        }

        $this->assertIsInt($stats['totalYouth']);
        $this->assertIsArray($stats['genderData']);
        $this->assertArrayHasKey('L', $stats['genderData']);
        $this->assertArrayHasKey('P', $stats['genderData']);
        $this->assertIsArray($stats['orgStats']);
        $this->assertIsArray($stats['eduLevelStats']);
        $this->assertIsArray($stats['topSchools']);
        $this->assertIsArray($stats['topSkills']);
        $this->assertIsArray($stats['topInterests']);
        $this->assertIsArray($stats['jobStats']);
        $this->assertIsArray($stats['ageData']);
        $this->assertIsFloat($stats['avgAge']);
    }

    public function testPersebaranStatsForAdminPemudaScope(): void
    {
        $db = Database::connect('default');
        $pemudaModel = new PemudaModel($db);

        $scope = [
            'role'       => 'admin_pemuda',
            'wilayah_id' => null,
            'cabang_id'  => null,
        ];

        $stats = $pemudaModel->getPersebaranStats($scope);

        // admin_pemuda must only see male data (gender P should be 0)
        $this->assertSame(0, $stats['genderData']['P']);
        $this->assertSame($stats['totalYouth'], $stats['genderData']['L']);
    }

    public function testPersebaranStatsForAdminPemudiScope(): void
    {
        $db = Database::connect('default');
        $pemudaModel = new PemudaModel($db);

        $scope = [
            'role'       => 'admin_pemudi',
            'wilayah_id' => null,
            'cabang_id'  => null,
        ];

        $stats = $pemudaModel->getPersebaranStats($scope);

        // admin_pemudi must only see female data (gender L should be 0)
        $this->assertSame(0, $stats['genderData']['L']);
        $this->assertSame($stats['totalYouth'], $stats['genderData']['P']);
    }

    public function testPersebaranStatsForAdminCabangScope(): void
    {
        $db = Database::connect('default');
        $pemudaModel = new PemudaModel($db);
        $cabangModel = new CabangModel($db);

        $cabang = $cabangModel->first();
        if (!$cabang) {
            $this->markTestSkipped('No cabang in test database');
        }

        $cabangId = (int) $cabang['id'];
        $scope = [
            'role'       => 'admin_cabang',
            'wilayah_id' => (int) $cabang['wilayah_id'],
            'cabang_id'  => $cabangId,
        ];

        $stats = $pemudaModel->getPersebaranStats($scope);

        // Total youth must match exact count in that specific cabang
        $actualCabangCount = $pemudaModel->where('cabang_id', $cabangId)->where('status_data', 'active')->countAllResults();
        $this->assertSame($actualCabangCount, $stats['totalYouth']);
    }

    public function testPersebaranStatsWithFilterParameters(): void
    {
        $db = Database::connect('default');
        $pemudaModel = new PemudaModel($db);

        $statsAll = $pemudaModel->getPersebaranStats(['role' => 'superadmin'], ['gender' => null]);
        $statsMale = $pemudaModel->getPersebaranStats(['role' => 'superadmin'], ['gender' => 'L']);
        $statsFemale = $pemudaModel->getPersebaranStats(['role' => 'superadmin'], ['gender' => 'P']);

        $this->assertSame(0, $statsMale['genderData']['P']);
        $this->assertSame(0, $statsFemale['genderData']['L']);
        $this->assertSame($statsAll['totalYouth'], $statsMale['totalYouth'] + $statsFemale['totalYouth']);
    }
}
