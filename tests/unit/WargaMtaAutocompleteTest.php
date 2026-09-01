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

        $detailMethod = $reflection->getMethod('wargaDetail');
        $this->assertCount(1, $detailMethod->getParameters());
        $this->assertEquals('uuid', $detailMethod->getParameters()[0]->getName());
    }

    public function testRoutesAreDefined(): void
    {
        $routes = Services::routes();
        require APPPATH . 'Config/Routes.php';

        $getRoutes = $routes->getRoutes('GET');

        $this->assertArrayHasKey('pendataan/search-warga', $getRoutes);
        $this->assertArrayHasKey('pendataan/warga-detail/([^/]+)', $getRoutes);

        $this->assertEquals('\App\Controllers\Pendataan::searchWarga', $getRoutes['pendataan/search-warga']);
        $this->assertEquals('\App\Controllers\Pendataan::wargaDetail/$1', $getRoutes['pendataan/warga-detail/([^/]+)']);
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
    }

    public function testJsContainsAutocompleteFunctions(): void
    {
        $jsFile = ROOTPATH . 'public/js/pendataan.js';
        $this->assertFileExists($jsFile);

        $content = file_get_contents($jsFile);

        $this->assertStringContainsString('initWargaMtaAutocomplete', $content);
        $this->assertStringContainsString('renderWargaSuggestions', $content);
        $this->assertStringContainsString('selectWargaMta', $content);
        $this->assertStringContainsString('resetWargaMtaSelection', $content);
        $this->assertStringContainsString('warga-suggestions-dropdown', $content);
    }
}
