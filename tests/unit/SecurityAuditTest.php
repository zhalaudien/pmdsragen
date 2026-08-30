<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use Config\Filters;
use Config\Security;
use Config\Session;
use Config\Cookie;
use Config\Database;
use App\Models\PemudaModel;

class SecurityAuditTest extends CIUnitTestCase
{
    /**
     * Test global security filters configuration
     */
    public function testGlobalFiltersEnabled()
    {
        $filtersConfig = config(Filters::class);

        $this->assertContains('csrf', $filtersConfig->globals['before']);
        $this->assertContains('invalidchars', $filtersConfig->globals['before']);
        $this->assertContains('secureheaders', $filtersConfig->globals['after']);
    }

    /**
     * Test Session & Cookie security settings
     */
    public function testSessionAndCookieSecurity()
    {
        $sessionConfig = config(Session::class);
        $cookieConfig  = config(Cookie::class);

        $this->assertTrue($sessionConfig->regenerateDestroy, 'Session regenerateDestroy should be true');
        $this->assertTrue($cookieConfig->httponly, 'Cookie httponly must be true to prevent XSS cookie theft');
        $this->assertSame('Lax', $cookieConfig->samesite, 'Cookie SameSite should be Lax');
    }

    /**
     * Test CSV Formula Injection sanitizer
     */
    public function testCsvFormulaInjectionSanitization()
    {
        $this->assertTrue(function_exists('sanitizeCsvField'));

        // Normal inputs
        $this->assertSame('Ahmad Dahlan', sanitizeCsvField('Ahmad Dahlan'));
        $this->assertSame('12345', sanitizeCsvField('12345'));
        $this->assertSame('-', sanitizeCsvField(''));
        $this->assertSame('-', sanitizeCsvField(null));

        // Malicious Formula Injection inputs
        $this->assertSame("'=1+1", sanitizeCsvField('=1+1'));
        $this->assertSame("'+cmd|' /C calc'!A0", sanitizeCsvField("+cmd|' /C calc'!A0"));
        $this->assertSame("'-5+2", sanitizeCsvField('-5+2'));
        $this->assertSame("'@SUM(1,2)", sanitizeCsvField('@SUM(1,2)'));
        $this->assertSame("'\tpayload", sanitizeCsvField("\tpayload"));
        $this->assertSame("'\rpayload", sanitizeCsvField("\rpayload"));
    }

    /**
     * Test formatWaNumber helper safety
     */
    public function testFormatWaNumberSafety()
    {
        $this->assertTrue(function_exists('formatWaNumber'));

        $this->assertSame('https://wa.me/6281234567890', formatWaNumber('081234567890'));
        $this->assertSame('https://wa.me/6281234567890', formatWaNumber('+62 812-3456-7890'));
        $this->assertSame('https://wa.me/6281234567890', formatWaNumber('81234567890'));
        $this->assertNull(formatWaNumber(''));
        $this->assertNull(formatWaNumber('abc-xyz'));
    }

    /**
     * Test duplicate detection logic
     */
    public function testDuplicateDetectionLogic()
    {
        $db = Database::connect('default');
        $pemudaModel = new PemudaModel($db);

        // Testing duplicate finder with non-existent values
        $duplicate = $pemudaModel->findDuplicate('NonExistentName999XYZ', '2000-01-01', 1);
        $this->assertNull($duplicate);
    }
}
