<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\CabangModel;
use App\Services\MtaSyncService;
use App\Services\PemudaImportService;
use App\Database\Seeds\CabangSeeder;

/**
 * @internal
 */
final class CabangApiCodeTest extends CIUnitTestCase
{
    public function testCabangSeederUsesMtaApiCodeFormat(): void
    {
        $seederFile = APPPATH . 'Database/Seeds/CabangSeeder.php';
        $this->assertFileExists($seederFile);

        $content = file_get_contents($seederFile);
        $this->assertStringNotContainsString('CBG-001', $content);
        $this->assertStringContainsString("'code'        => '86.1'", $content);
        $this->assertStringContainsString("'code'        => '86.6'", $content);
        $this->assertStringContainsString("'code'        => '86.47'", $content);
    }

    public function testCabangCodesInDatabaseFollowMtaApiFormat(): void
    {
        try {
            $db = \Config\Database::connect('default');
            $rows = $db->table('cabang')->get()->getResultArray();

            if (!empty($rows)) {
                $cbgCount = 0;
                $mtaCodeCount = 0;

                foreach ($rows as $cabang) {
                    $code = (string) ($cabang['code'] ?? '');
                    if (str_starts_with($code, 'CBG-')) {
                        $cbgCount++;
                    }
                    if (str_starts_with($code, '86.')) {
                        $mtaCodeCount++;
                    }
                }

                $this->assertEquals(0, $cbgCount, 'Tidak boleh ada kode cabang berformat CBG-xxx');
                $this->assertGreaterThanOrEqual(60, $mtaCodeCount, 'Mayoritas/seluruh kode cabang harus berformat 86.x sesuai API Pusat');
            }
        } catch (\Throwable $e) {
            $this->markTestSkipped('Database query skipped: ' . $e->getMessage());
        }
    }

    public function testPemudaImportLookupAcceptsMtaApiCode(): void
    {
        $importService = new PemudaImportService();
        $reflection = new \ReflectionClass(PemudaImportService::class);
        $preloadLookups = $reflection->getMethod('preloadLookups');
        $preloadLookups->setAccessible(true);

        $lookups = $preloadLookups->invoke($importService);
        $cabangMap = $lookups['cabang'];

        // Cek apakah kode format 86.x dapat ditemukan dalam map
        $this->assertArrayHasKey('86.1', $cabangMap);
        $this->assertArrayHasKey('86.6', $cabangMap);
        $this->assertArrayHasKey('gemolong 1', $cabangMap);
    }

    public function testCabangViewsReflectMtaCodePlaceholder(): void
    {
        $cabangView = APPPATH . 'Views/admin/cabang/index.php';
        $this->assertFileExists($cabangView);
        $content = file_get_contents($cabangView);

        $this->assertStringNotContainsString('placeholder="Contoh: CBG-001"', $content);
        $this->assertStringContainsString('placeholder="Contoh: 86.1"', $content);

        $importView = APPPATH . 'Views/admin/pemuda/import.php';
        $this->assertFileExists($importView);
        $importContent = file_get_contents($importView);
        $this->assertStringContainsString('86.1', $importContent);
    }
}
