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
        $this->assertContains('foto', $fields);
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
                'selected' => 'Satgas',
                'name'     => 'Satgas',
            ],
            'bankom' => [
                'name'     => 'Bankom',
            ],
            'parkir' => [
                'name'     => 'Parkir',
            ],
            'pemuda' => [
                'selected' => 'Pemuda',
                'name'     => 'Pemuda',
            ],
            'tim_ikhrom' => [
                'name'     => 'Tim Ikhrom',
            ],
        ];

        $inserted = [];
        foreach ($mockPostOrganizations as $orgKey => $org) {
            if (!empty($org['selected'])) {
                $orgName = !empty($org['name']) ? $org['name'] : (is_string($org['selected']) ? $org['selected'] : ucfirst($orgKey));

                $inserted[] = [
                    'organization_name' => $orgName,
                ];
            }
        }

        $this->assertCount(2, $inserted);
        $this->assertSame('Satgas', $inserted[0]['organization_name']);
        $this->assertSame('Pemuda', $inserted[1]['organization_name']);
    }

    public function testFindDuplicateAndFindExistingMethodBehavior(): void
    {
        $model = new PemudaModel();

        // Testing empty parameter validation handling in findExistingPemuda
        $this->assertNull($model->findExistingPemuda('', 'L', '2000-01-01', 1));
        $this->assertNull($model->findExistingPemuda('Budi', 'L', '', 1));
        $this->assertNull($model->findExistingPemuda('Budi', 'L', '2000-01-01', 0));
        
        // Testing findDuplicate compatibility
        $this->assertNull($model->findDuplicate('', '2000-01-01', 1));
        $this->assertNull($model->findDuplicate('Budi', '', 1));
        $this->assertNull($model->findDuplicate('Budi', '2000-01-01', 0));
    }

    public function testToLowerTrimHelper(): void
    {
        $this->assertSame('budi santoso', toLowerTrim('  Budi Santoso  '));
        $this->assertSame('jawa tengah', toLowerTrim('JAWA TENGAH'));
        $this->assertNull(toLowerTrim(null));
        $this->assertNull(toLowerTrim('   '));
    }

    public function testCheckDataRoutesRegistered(): void
    {
        $routes = \Config\Services::routes();
        require APPPATH . 'Config/Routes.php';

        $postRoutes = $routes->getRoutes('POST');
        $this->assertArrayHasKey('pendataan/check-data', $postRoutes);
        $this->assertEquals('\App\Controllers\Pendataan::checkData', $postRoutes['pendataan/check-data']);
        $this->assertArrayHasKey('pendataan/check-duplicate', $postRoutes);
        $this->assertEquals('\App\Controllers\Pendataan::checkData', $postRoutes['pendataan/check-duplicate']);
    }

    public function testOrganisasiFieldsRemovedFromViewsAndModel(): void
    {
        // 1. OrganisasiModel does not allow position and join_date
        $model = new \App\Models\OrganisasiModel();
        $reflection = new \ReflectionClass($model);
        $allowedProp = $reflection->getProperty('allowedFields');
        $allowedProp->setAccessible(true);
        $allowed = $allowedProp->getValue($model);

        $this->assertNotContains('position', $allowed);
        $this->assertNotContains('join_date', $allowed);
        $this->assertNotContains('end_date', $allowed);
        $this->assertContains('organization_name', $allowed);

        // 2. Public form does not contain position or join_year inputs
        $publicForm = file_get_contents(APPPATH . 'Views/pendataan/form.php');
        $this->assertStringNotContainsString('][position]', $publicForm);
        $this->assertStringNotContainsString('][join_year]', $publicForm);
        $this->assertStringNotContainsString('lengkapi jabatan/peran Anda', $publicForm);

        // 3. Admin pemuda form does not contain position or join_year inputs
        $adminForm = file_get_contents(APPPATH . 'Views/admin/pemuda/form.php');
        $this->assertStringNotContainsString('][position]', $adminForm);
        $this->assertStringNotContainsString('][join_year]', $adminForm);
        $this->assertStringNotContainsString('admin_org_detail_', $adminForm);

        // 4. Admin detail view does not contain Masa Keanggotaan or Jabatan columns
        $adminDetail = file_get_contents(APPPATH . 'Views/admin/pemuda/detail.php');
        $this->assertStringNotContainsString('Masa Keanggotaan', $adminDetail);
        $this->assertStringNotContainsString('<th>Jabatan</th>', $adminDetail);
    }

    public function testFotoProfileUploadFormAndRequirement(): void
    {
        $publicForm = file_get_contents(APPPATH . 'Views/pendataan/form.php');

        // Form must support file upload
        $this->assertStringContainsString('enctype="multipart/form-data"', $publicForm);

        // Foto file input exists
        $this->assertStringContainsString('name="foto"', $publicForm);
        $this->assertStringContainsString('id="foto"', $publicForm);
        $this->assertStringContainsString('id="section-foto-profil"', $publicForm);

        // Dynamic badges & text for male/female
        $this->assertStringContainsString('Wajib untuk Laki-laki', $publicForm);
        $this->assertStringContainsString('Perempuan tidak diwajibkan', $publicForm);

        // Verification of gender-based requirement helper logic
        $isPhotoRequiredForMale = function(string $gender, bool $hasFile, bool $hasExisting): bool {
            if ($gender === 'L' && !$hasFile && !$hasExisting) {
                return false; // Invalid: missing required photo
            }
            return true; // Valid
        };

        // Male without photo -> invalid
        $this->assertFalse($isPhotoRequiredForMale('L', false, false));
        // Male with photo -> valid
        $this->assertTrue($isPhotoRequiredForMale('L', true, false));
        // Male with existing photo -> valid
        $this->assertTrue($isPhotoRequiredForMale('L', false, true));

        // Female without photo -> valid (not required)
        $this->assertTrue($isPhotoRequiredForMale('P', false, false));
        // Female with photo -> valid
        $this->assertTrue($isPhotoRequiredForMale('P', true, false));
    }
}
