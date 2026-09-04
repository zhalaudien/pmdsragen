<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Unit Test for Mobile Version & PWA Features
 */
class MobileVersionTest extends CIUnitTestCase
{
    /**
     * Test manifest.json presence and validity
     */
    public function testManifestJsonIsValid(): void
    {
        $manifestPath = FCPATH . 'manifest.json';
        $this->assertFileExists($manifestPath, 'manifest.json must exist in public root.');

        $content = file_get_contents($manifestPath);
        $json = json_decode($content, true);

        $this->assertIsArray($json, 'manifest.json must be valid JSON.');
        $this->assertSame('Pemuda MTA Perwakilan Sragen', $json['name']);
        $this->assertSame('Pemuda MTA', $json['short_name']);
        $this->assertSame('standalone', $json['display']);
        $this->assertSame('#dc2626', $json['theme_color']);
        $this->assertNotEmpty($json['icons'], 'manifest.json must define icons.');
        $this->assertNotEmpty($json['shortcuts'], 'manifest.json must define shortcuts.');
    }

    /**
     * Test PWA app icons exist in public/icons
     */
    public function testPwaIconsExist(): void
    {
        $icons = [
            'icon-72x72.png',
            'icon-96x96.png',
            'icon-128x128.png',
            'icon-144x144.png',
            'icon-152x152.png',
            'icon-192x192.png',
            'icon-384x384.png',
            'icon-512x512.png',
            'apple-touch-icon.png',
            'maskable-icon-512x512.png'
        ];

        foreach ($icons as $icon) {
            $path = FCPATH . 'icons/' . $icon;
            $this->assertFileExists($path, "Icon {$icon} must exist in public/icons/");
            $this->assertGreaterThan(0, filesize($path), "Icon {$icon} must not be empty.");
        }
    }

    /**
     * Test Service Worker file exists and contains core events
     */
    public function testServiceWorkerFile(): void
    {
        $swPath = FCPATH . 'sw.js';
        $this->assertFileExists($swPath, 'sw.js must exist in public root.');

        $content = file_get_contents($swPath);
        $this->assertStringContainsString('install', $content);
        $this->assertStringContainsString('activate', $content);
        $this->assertStringContainsString('fetch', $content);
        $this->assertStringContainsString('offline.html', $content);
    }

    /**
     * Test offline fallback page exists
     */
    public function testOfflineFallbackHtml(): void
    {
        $offlinePath = FCPATH . 'offline.html';
        $this->assertFileExists($offlinePath, 'offline.html must exist.');

        $content = file_get_contents($offlinePath);
        $this->assertStringContainsString('Offline', $content);
        $this->assertStringContainsString('Koneksi Internet Terputus', $content);
    }

    /**
     * Test PWA install script exists
     */
    public function testPwaInstallJs(): void
    {
        $jsPath = FCPATH . 'js/pwa-install.js';
        $this->assertFileExists($jsPath, 'pwa-install.js must exist.');

        $content = file_get_contents($jsPath);
        $this->assertStringContainsString('serviceWorker.register', $content);
        $this->assertStringContainsString('beforeinstallprompt', $content);
        $this->assertStringContainsString('triggerPwaInstall', $content);
    }

    /**
     * Test Public Layout contains PWA meta tags and Mobile Bottom Nav
     */
    public function testPublicLayoutIncludesMobileVersion(): void
    {
        $layoutPath = APPPATH . 'Views/layouts/main.php';
        $content = file_get_contents($layoutPath);

        $this->assertStringContainsString('manifest.json', $content);
        $this->assertStringContainsString('apple-touch-icon', $content);
        $this->assertStringContainsString('theme-color', $content);
        $this->assertStringContainsString('mobile-bottom-nav', $content);
        $this->assertStringContainsString('pwa-install.js', $content);
    }

    /**
     * Test Admin Layout contains PWA meta tags and Admin Mobile Bottom Nav
     */
    public function testAdminLayoutIncludesMobileVersion(): void
    {
        $layoutPath = APPPATH . 'Views/admin/layouts/main.php';
        $content = file_get_contents($layoutPath);

        $this->assertStringContainsString('manifest.json', $content);
        $this->assertStringContainsString('admin-mobile-bottom-nav', $content);
        $this->assertStringContainsString('pwa-install.js', $content);
    }

    /**
     * Test Auth Login contains PWA meta tags
     */
    public function testAuthLoginIncludesPwaTags(): void
    {
        $loginPath = APPPATH . 'Views/auth/login.php';
        $content = file_get_contents($loginPath);

        $this->assertStringContainsString('manifest.json', $content);
        $this->assertStringContainsString('apple-touch-icon', $content);
        $this->assertStringContainsString('theme-color', $content);
    }

    /**
     * Test Mobile Cards exist in Admin Pemuda & Cabang views
     */
    public function testAdminViewsHaveMobileCards(): void
    {
        $pemudaView = file_get_contents(APPPATH . 'Views/admin/pemuda/index.php');
        $this->assertStringContainsString('pemuda-mobile-cards', $pemudaView);
        $this->assertStringContainsString('pemuda-card-item', $pemudaView);

        $cabangView = file_get_contents(APPPATH . 'Views/admin/cabang/index.php');
        $this->assertStringContainsString('cabang-mobile-cards', $cabangView);
    }

    /**
     * Test CSS files have mobile bottom navigation rules
     */
    public function testCssHasMobileNavRules(): void
    {
        $mainCss = file_get_contents(FCPATH . 'css/main.css');
        $this->assertStringContainsString('.mobile-bottom-nav', $mainCss);
        $this->assertStringContainsString('safe-area-inset-bottom', $mainCss);

        $adminCss = file_get_contents(FCPATH . 'css/admin.css');
        $this->assertStringContainsString('.admin-mobile-bottom-nav', $adminCss);
        $this->assertStringContainsString('.pemuda-card-item', $adminCss);
    }
}
