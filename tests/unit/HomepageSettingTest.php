<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\HomepageSettingModel;
use App\Controllers\Admin\HomepageSetting;
use Config\Services;

/**
 * @internal
 */
final class HomepageSettingTest extends CIUnitTestCase
{
    public function testModelClassAndDefaults(): void
    {
        $this->assertTrue(class_exists(HomepageSettingModel::class));

        $defaults = HomepageSettingModel::getDefaults();
        $this->assertIsArray($defaults);
        $this->assertArrayHasKey('hero_title', $defaults);
        $this->assertArrayHasKey('hero_subtitle', $defaults);
        $this->assertArrayHasKey('visi_text', $defaults);
        $this->assertArrayHasKey('misi_list', $defaults);
        $this->assertArrayHasKey('program_list', $defaults);
        $this->assertArrayHasKey('alur_steps', $defaults);
        $this->assertArrayHasKey('faq_list', $defaults);
        $this->assertArrayHasKey('whatsapp_number', $defaults);
    }

    public function testControllerMethodsExist(): void
    {
        $this->assertTrue(class_exists(HomepageSetting::class));

        $reflection = new \ReflectionClass(HomepageSetting::class);
        $this->assertTrue($reflection->hasMethod('index'));
        $this->assertTrue($reflection->hasMethod('update'));
        $this->assertTrue($reflection->hasMethod('reset'));
    }

    public function testRoutesAreDefined(): void
    {
        $routes = Services::routes();
        require APPPATH . 'Config/Routes.php';

        $getRoutes  = $routes->getRoutes('GET');
        $postRoutes = $routes->getRoutes('POST');

        $this->assertArrayHasKey('admin/homepage', $getRoutes);
        $this->assertEquals('\App\Controllers\Admin\HomepageSetting::index', $getRoutes['admin/homepage']);

        $this->assertArrayHasKey('admin/homepage/update', $postRoutes);
        $this->assertEquals('\App\Controllers\Admin\HomepageSetting::update', $postRoutes['admin/homepage/update']);

        $this->assertArrayHasKey('admin/homepage/reset', $postRoutes);
        $this->assertEquals('\App\Controllers\Admin\HomepageSetting::reset', $postRoutes['admin/homepage/reset']);
    }

    public function testAdminSidebarContainsHomepageMenu(): void
    {
        $viewFile = APPPATH . 'Views/admin/layouts/main.php';
        $this->assertFileExists($viewFile);

        $content = file_get_contents($viewFile);
        $this->assertStringContainsString("base_url('admin/homepage')", $content);
        $this->assertStringContainsString('Kelola Homepage', $content);
        $this->assertStringContainsString("url_is('admin/homepage*')", $content);
    }

    public function testPortalViewExistsAndContainsTabs(): void
    {
        $viewFile = APPPATH . 'Views/admin/homepage/index.php';
        $this->assertFileExists($viewFile);

        $content = file_get_contents($viewFile);
        $this->assertStringContainsString('formHomepageSettings', $content);
        $this->assertStringContainsString('pane-hero', $content);
        $this->assertStringContainsString('pane-tentang', $content);
        $this->assertStringContainsString('pane-wilayah', $content);
        $this->assertStringContainsString('pane-program', $content);
        $this->assertStringContainsString('pane-alur', $content);
        $this->assertStringContainsString('pane-faq', $content);
        $this->assertStringContainsString('pane-kontak', $content);
        $this->assertStringContainsString('modalResetDefault', $content);
    }

    public function testLandingViewBindsDynamicSettings(): void
    {
        $viewFile = APPPATH . 'Views/landing.php';
        $this->assertFileExists($viewFile);

        $content = file_get_contents($viewFile);
        $this->assertStringContainsString("\$settings['hero_title']", $content);
        $this->assertStringContainsString("\$settings['visi_text']", $content);
        $this->assertStringContainsString("\$settings['whatsapp_number']", $content);
        $this->assertStringContainsString('$programs', $content);
        $this->assertStringContainsString('$faqs', $content);
    }
}
