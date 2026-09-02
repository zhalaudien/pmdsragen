<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Controllers\Pendataan;
use App\Services\MtaApiService;
use Config\Services;

/**
 * @internal
 */
final class WargaMtaAutocompleteTest extends CIUnitTestCase
{
    public function testControllerMethodsExist(): void
    {
        $reflection = new \ReflectionClass(Pendataan::class);
        $this->assertTrue($reflection->hasMethod('searchWarga'));
        $this->assertTrue($reflection->hasMethod('wargaDetail'));
        $this->assertTrue($reflection->hasMethod('pemudaDetail'));

        $detailMethod = $reflection->getMethod('wargaDetail');
        $this->assertCount(1, $detailMethod->getParameters());
        $this->assertEquals('uuid', $detailMethod->getParameters()[0]->getName());

        $pemudaDetailMethod = $reflection->getMethod('pemudaDetail');
        $this->assertCount(1, $pemudaDetailMethod->getParameters());
        $this->assertEquals('id', $pemudaDetailMethod->getParameters()[0]->getName());
    }

    public function testRoutesAreDefined(): void
    {
        $routes = Services::routes();
        require APPPATH . 'Config/Routes.php';

        $getRoutes = $routes->getRoutes('GET');

        $this->assertArrayHasKey('pendataan/search-warga', $getRoutes);
        $this->assertArrayHasKey('pendataan/warga-detail/([^/]+)', $getRoutes);
        $this->assertArrayHasKey('pendataan/pemuda-detail/([0-9]+)', $getRoutes);

        $this->assertEquals('\App\Controllers\Pendataan::searchWarga', $getRoutes['pendataan/search-warga']);
        $this->assertEquals('\App\Controllers\Pendataan::wargaDetail/$1', $getRoutes['pendataan/warga-detail/([^/]+)']);
        $this->assertEquals('\App\Controllers\Pendataan::pemudaDetail/$1', $getRoutes['pendataan/pemuda-detail/([0-9]+)']);
    }

    public function testFormViewContainsAutocompleteElements(): void
    {
        $viewFile = APPPATH . 'Views/pendataan/form.php';
        $this->assertFileExists($viewFile);

        $content = file_get_contents($viewFile);

        // Hidden input for MTA warga UUID
        $this->assertStringContainsString('id="mta_warga_uuid"', $content);
        $this->assertStringContainsString('name="mta_warga_uuid"', $content);

        // Autocomplete suggestions dropdown and spinner
        $this->assertStringContainsString('id="warga-suggestions-dropdown"', $content);
        $this->assertStringContainsString('id="warga-suggestions-list"', $content);
        $this->assertStringContainsString('id="name-search-spinner"', $content);

        // Selected warga MTA info banner
        $this->assertStringContainsString('id="warga-mta-selected-banner"', $content);
        $this->assertStringContainsString('id="warga-selected-name"', $content);
        $this->assertStringContainsString('id="warga-selected-nomor"', $content);
        $this->assertStringContainsString('resetWargaMtaSelection()', $content);

        // PENDATAAN_CONFIG URLs
        $this->assertStringContainsString("searchWargaUrl:", $content);
        $this->assertStringContainsString("wargaDetailUrl:", $content);
        $this->assertStringContainsString("pemudaDetailUrl:", $content);
    }

    public function testJsContainsAutocompleteFunctions(): void
    {
        $jsFile = ROOTPATH . 'public/js/pendataan.js';
        $this->assertFileExists($jsFile);

        $content = file_get_contents($jsFile);

        $this->assertStringContainsString('initWargaMtaAutocomplete', $content);
        $this->assertStringContainsString('renderWargaSuggestions', $content);
        $this->assertStringContainsString('selectWargaMta', $content);
        $this->assertStringContainsString('selectLocalPemuda', $content);
        $this->assertStringContainsString('selectNewPemudaInput', $content);
        $this->assertStringContainsString('handleSelectSuggestion', $content);
        $this->assertStringContainsString('resetWargaMtaSelection', $content);
        $this->assertStringContainsString('warga-suggestions-dropdown', $content);
    }

    public function testPemudaDetailValidationHandling(): void
    {
        $controller = new Pendataan();
        $controller->initController(Services::request(), Services::response(), Services::logger());

        // Test with invalid ID (0 or negative)
        $response = $controller->pemudaDetail(0);
        $data = json_decode($response->getBody(), true);
        $this->assertFalse($data['success']);
        $this->assertEquals('ID Pemuda tidak valid.', $data['message']);

        $responseNeg = $controller->pemudaDetail(-5);
        $dataNeg = json_decode($responseNeg->getBody(), true);
        $this->assertFalse($dataNeg['success']);
    }
}
