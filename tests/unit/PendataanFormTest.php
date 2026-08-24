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
}
