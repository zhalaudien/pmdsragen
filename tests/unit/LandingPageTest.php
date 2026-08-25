<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Controllers\Home;
use App\Controllers\Pendataan;
use Config\Services;

/**
 * @internal
 */
final class LandingPageTest extends CIUnitTestCase
{
    public function testRoutesSeparation(): void
    {
        $routes = Services::routes();
        require APPPATH . 'Config/Routes.php';
        $routesList = $routes->getRoutes('GET');

        $this->assertArrayHasKey('/', $routesList);
        $this->assertEquals('\App\Controllers\Home::index', $routesList['/']);

        $this->assertArrayHasKey('pendataan', $routesList);
        $this->assertEquals('\App\Controllers\Pendataan::index', $routesList['pendataan']);
    }

    public function testLandingPageControllerRendersContent(): void
    {
        $controller = new Home();
        $controller->initController(Services::request(), Services::response(), Services::logger());

        $html = $controller->index();

        $this->assertIsString($html);
        $this->assertStringContainsString('Pemuda MTA Perwakilan Sragen', $html);
        $this->assertStringContainsString('4 Wilayah', $html);
        $this->assertStringContainsString('Form Pendataan', $html);
        $this->assertStringContainsString('Satgas', $html);
        $this->assertStringContainsString('Bankom', $html);
    }

    public function testPendataanControllerRendersForm(): void
    {
        $controller = new Pendataan();
        $controller->initController(Services::request(), Services::response(), Services::logger());

        $html = $controller->index();

        $this->assertIsString($html);
        $this->assertStringContainsString('Formulir Pendataan Pemuda', $html);
        $this->assertStringContainsString('Pemuda MTA Perwakilan Sragen', $html);
        $this->assertStringContainsString('Kembali ke Beranda', $html);
    }
}
