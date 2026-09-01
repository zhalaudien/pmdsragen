<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Controllers\Admin\WargaMta;
use App\Services\MtaApiService;
use App\Services\MtaSyncService;
use Config\Services;

/**
 * @internal
 */
final class WargaMtaTest extends CIUnitTestCase
{
    public function testControllerAndServicesExist(): void
    {
        $this->assertTrue(class_exists(WargaMta::class));
        $this->assertTrue(class_exists(MtaApiService::class));
        $this->assertTrue(class_exists(MtaSyncService::class));
    }

    public function testControllerMethods(): void
    {
        $reflection = new \ReflectionClass(WargaMta::class);
        $this->assertTrue($reflection->hasMethod('index'));
        $this->assertTrue($reflection->hasMethod('detail'));
        $this->assertTrue($reflection->hasMethod('import'));

        $detailMethod = $reflection->getMethod('detail');
        $this->assertCount(1, $detailMethod->getParameters());
        $this->assertEquals('uuid', $detailMethod->getParameters()[0]->getName());
    }

    public function testRoutesAreDefined(): void
    {
        $routes = Services::routes();
        require APPPATH . 'Config/Routes.php';

        $getRoutes = $routes->getRoutes('GET');
        $postRoutes = $routes->getRoutes('POST');

        $this->assertArrayHasKey('admin/warga-mta', $getRoutes);
        $this->assertArrayHasKey('admin/warga-mta/detail/([^/]+)', $getRoutes);
        $this->assertArrayHasKey('admin/warga-mta/import', $postRoutes);

        $this->assertEquals('\App\Controllers\Admin\WargaMta::index', $getRoutes['admin/warga-mta']);
        $this->assertEquals('\App\Controllers\Admin\WargaMta::detail/$1', $getRoutes['admin/warga-mta/detail/([^/]+)']);
        $this->assertEquals('\App\Controllers\Admin\WargaMta::import', $postRoutes['admin/warga-mta/import']);
    }

    public function testSidebarContainsWargaMtaMenu(): void
    {
        $viewFile = APPPATH . 'Views/admin/layouts/main.php';
        $this->assertFileExists($viewFile);

        $content = file_get_contents($viewFile);
        $this->assertStringContainsString("base_url('admin/warga-mta')", $content);
        $this->assertStringContainsString('Warga MTA', $content);
        $this->assertStringContainsString("url_is('admin/warga-mta*')", $content);
    }

    public function testViewsExist(): void
    {
        $indexView = APPPATH . 'Views/admin/warga_mta/index.php';
        $detailView = APPPATH . 'Views/admin/warga_mta/detail.php';

        $this->assertFileExists($indexView);
        $this->assertFileExists($detailView);

        $indexContent = file_get_contents($indexView);
        $this->assertStringContainsString('Data Warga MTA', $indexContent);
        $this->assertStringContainsString('modalDetailWargaMta', $indexContent);
        $this->assertStringContainsString('modalImportWargaMta', $indexContent);
    }

    public function testMtaApiSragenScope(): void
    {
        $apiService = new MtaApiService();
        $this->assertEquals('3246792b-f0a7-48ca-95fa-379e3bee777d', $apiService->getSragenUuid());

        if ($apiService->isEnabled()) {
            $test = $apiService->testConnection();
            $this->assertIsArray($test);
            $this->assertArrayHasKey('connected', $test);
        }
    }
}
