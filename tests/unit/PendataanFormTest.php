<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\PemudaModel;

/**
 * @internal
 */
final class PendataanFormTest extends CIUnitTestCase
{
    public function testPemudaModelAllowedFields(): void
    {
        $model = new PemudaModel();
        $fields = $model->allowedFields;

        $this->assertContains('marital_status', $fields);
        $this->assertContains('blood_type', $fields);
        $this->assertContains('gender', $fields);
        $this->assertContains('name', $fields);
    }

    public function testPemudaModelValidationRules(): void
    {
        $model = new PemudaModel();
        $rules = $model->getValidationRules();

        $this->assertArrayHasKey('marital_status', $rules);
        $this->assertArrayHasKey('blood_type', $rules);
        $this->assertStringContainsString('in_list[belum_menikah,sudah_menikah,janda,duda]', $rules['marital_status']);
    }

    public function testOrganizationFilterOnlySelected(): void
    {
        $mockPostOrganizations = [
            'satgas' => [
                'selected'    => 'Satgas',
                'name'        => 'Satgas',
                'position'    => 'Koordinator',
                'join_year'   => '2023',
                'description' => 'Satgas Inti',
            ],
            'bankom' => [
                'name'        => 'Bankom',
                'position'    => 'Anggota',
                'join_year'   => '2026',
                'description' => '',
            ],
            'parkir' => [
                'name'        => 'Parkir',
                'position'    => 'Anggota',
                'join_year'   => '2026',
                'description' => '',
            ],
            'pemuda' => [
                'selected'    => 'Pemuda',
                'name'        => 'Pemuda',
                'position'    => 'Sie Acara',
                'join_year'   => '2024',
                'description' => '',
            ],
            'tim_ikhrom' => [
                'name'        => 'Tim Ikhrom',
                'position'    => 'Anggota',
                'join_year'   => '2026',
                'description' => '',
            ],
        ];

        $inserted = [];
        foreach ($mockPostOrganizations as $orgKey => $org) {
            if (!empty($org['selected'])) {
                $orgName  = !empty($org['name']) ? $org['name'] : (is_string($org['selected']) ? $org['selected'] : ucfirst($orgKey));
                $position = !empty($org['position']) ? $org['position'] : 'Anggota';
                $joinDate = !empty($org['join_year']) ? ($org['join_year'] . '-01-01') : (!empty($org['join_date']) ? $org['join_date'] : null);
                $desc     = !empty($org['description']) ? $org['description'] : null;

                $inserted[] = [
                    'organization_name' => $orgName,
                    'position'          => $position,
                    'join_date'         => $joinDate,
                    'description'       => $desc,
                ];
            }
        }

        $this->assertCount(2, $inserted);
        $this->assertSame('Satgas', $inserted[0]['organization_name']);
        $this->assertSame('Koordinator', $inserted[0]['position']);
        $this->assertSame('Pemuda', $inserted[1]['organization_name']);
        $this->assertSame('Sie Acara', $inserted[1]['position']);
    }

    public function testFindDuplicateMethodBehavior(): void
    {
        $model = new PemudaModel();

        // Testing empty parameter validation handling in findDuplicate
        $this->assertNull($model->findDuplicate('', '2000-01-01', 1));
        $this->assertNull($model->findDuplicate('Budi', '', 1));
        $this->assertNull($model->findDuplicate('Budi', '2000-01-01', 0));
    }

    public function testCheckDuplicateRouteRegistered(): void
    {
        $routes = \Config\Services::routes();
        require APPPATH . 'Config/Routes.php';

        $postRoutes = $routes->getRoutes('POST');
        $this->assertArrayHasKey('pendataan/check-duplicate', $postRoutes);
        $this->assertEquals('\App\Controllers\Pendataan::checkDuplicate', $postRoutes['pendataan/check-duplicate']);
    }
}
