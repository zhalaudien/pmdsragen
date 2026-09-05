<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\PekerjaanModel;
use App\Models\PemudaModel;

/**
 * Unit Test for Wirausaha / Pemilik Usaha Detail Feature
 */
class WirausahaDetailTest extends CIUnitTestCase
{
    public function testPekerjaanModelAllowedFields(): void
    {
        $model = new PekerjaanModel();
        $fields = $model->allowedFields;

        $this->assertContains('business_name', $fields);
        $this->assertContains('business_address', $fields);
        $this->assertContains('business_contact', $fields);
        $this->assertContains('business_social', $fields);
        $this->assertContains('business_field', $fields);
        $this->assertContains('company_name', $fields);
        $this->assertContains('job_title', $fields);
    }

    public function testPublicFormContainsWirausahaDetailPanel(): void
    {
        $formView = file_get_contents(APPPATH . 'Views/pendataan/form.php');

        $this->assertStringContainsString('id="panel-detail-wirausaha"', $formView);
        $this->assertStringContainsString('name="business_name"', $formView);
        $this->assertStringContainsString('id="business_field_wirausaha"', $formView);
        $this->assertStringContainsString('name="business_address"', $formView);
        $this->assertStringContainsString('name="business_contact"', $formView);
        $this->assertStringContainsString('name="business_social"', $formView);
    }

    public function testAdminFormContainsWirausahaPanel(): void
    {
        $adminForm = file_get_contents(APPPATH . 'Views/admin/pemuda/form.php');

        $this->assertStringContainsString('id="admin_wirausaha_panel"', $adminForm);
        $this->assertStringContainsString('name="business_name"', $adminForm);
        $this->assertStringContainsString('name="business_address"', $adminForm);
        $this->assertStringContainsString('name="business_contact"', $adminForm);
        $this->assertStringContainsString('name="business_social"', $adminForm);
        $this->assertStringContainsString('toggleAdminWirausaha', $adminForm);
    }

    public function testAdminDetailAndCetakContainWirausahaSection(): void
    {
        $adminDetail = file_get_contents(APPPATH . 'Views/admin/pemuda/detail.php');
        $this->assertStringContainsString('business_name', $adminDetail);
        $this->assertStringContainsString('business_address', $adminDetail);
        $this->assertStringContainsString('business_contact', $adminDetail);
        $this->assertStringContainsString('business_social', $adminDetail);

        $cetakView = file_get_contents(APPPATH . 'Views/admin/pemuda/cetak.php');
        $this->assertStringContainsString('business_name', $cetakView);
        $this->assertStringContainsString('business_address', $cetakView);
        $this->assertStringContainsString('business_contact', $cetakView);
    }

    public function testPendataanJsContainsWirausahaToggleLogic(): void
    {
        $js = file_get_contents(FCPATH . 'js/pendataan.js');

        $this->assertStringContainsString('panel-detail-wirausaha', $js);
        $this->assertStringContainsString('isWirausaha', $js);
        $this->assertStringContainsString('handleJobStatusChange', $js);
        $this->assertStringContainsString('business_name', $js);
        $this->assertStringContainsString('business_contact', $js);
    }
}
