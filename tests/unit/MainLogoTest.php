<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Unit Test for Pemuda MTA Main Logo Verification
 */
class MainLogoTest extends CIUnitTestCase
{
    public function testMainLogoFileExistsAndIsValid(): void
    {
        $logoPath = FCPATH . 'icons/pemudamta.png';
        $this->assertFileExists($logoPath, 'Master logo pemudamta.png must exist in public/icons/');
        $this->assertGreaterThan(0, filesize($logoPath), 'Logo file must not be empty.');

        $imageInfo = getimagesize($logoPath);
        $this->assertIsArray($imageInfo, 'Logo must be a valid readable image.');
        $this->assertSame(IMAGETYPE_PNG, $imageInfo[2], 'Logo must be of PNG type.');
    }

    public function testPublicLayoutUsesPemudaMtaLogo(): void
    {
        $publicLayout = file_get_contents(APPPATH . 'Views/layouts/main.php');
        $this->assertStringContainsString("icons/pemudamta.png", $publicLayout, 'Public layout must reference icons/pemudamta.png');
        $this->assertStringContainsString('navbar-brand-img', $publicLayout, 'Navbar brand must include the logo image class');
    }

    public function testAdminLayoutUsesPemudaMtaLogo(): void
    {
        $adminLayout = file_get_contents(APPPATH . 'Views/admin/layouts/main.php');
        $this->assertStringContainsString("icons/pemudamta.png", $adminLayout, 'Admin layout must reference icons/pemudamta.png');
        $this->assertStringContainsString('brand-link', $adminLayout, 'Admin sidebar brand link must be present');
    }

    public function testAuthLoginUsesPemudaMtaLogo(): void
    {
        $loginView = file_get_contents(APPPATH . 'Views/auth/login.php');
        $this->assertStringContainsString("icons/pemudamta.png", $loginView, 'Login view must reference icons/pemudamta.png');
    }

    public function testCetakBiodataUsesPemudaMtaLogo(): void
    {
        $cetakView = file_get_contents(APPPATH . 'Views/admin/pemuda/cetak.php');
        $this->assertStringContainsString("icons/pemudamta.png", $cetakView, 'Cetak biodata view must reference icons/pemudamta.png in the header');
    }
}
