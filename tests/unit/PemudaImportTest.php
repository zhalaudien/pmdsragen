<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\PemudaImportService;
use Config\Services;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * @internal
 */
final class PemudaImportTest extends CIUnitTestCase
{
    protected PemudaImportService $importService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->importService = new PemudaImportService();
    }

    public function testImportRoutesAreRegisteredWithSuperadminFilter(): void
    {
        $routes = Services::routes();
        require APPPATH . 'Config/Routes.php';

        $getRoutes  = $routes->getRoutes('GET');
        $postRoutes = $routes->getRoutes('POST');

        $this->assertArrayHasKey('admin/pemuda/import', $getRoutes);
        $this->assertEquals('\App\Controllers\Admin\Pemuda::import', $getRoutes['admin/pemuda/import']);

        $this->assertArrayHasKey('admin/pemuda/template-import', $getRoutes);
        $this->assertEquals('\App\Controllers\Admin\Pemuda::templateImport', $getRoutes['admin/pemuda/template-import']);

        $this->assertArrayHasKey('admin/pemuda/import', $postRoutes);
        $this->assertEquals('\App\Controllers\Admin\Pemuda::prosesImport', $postRoutes['admin/pemuda/import']);

        // Check filter applied to import route
        $filterForImport = $routes->getFiltersForRoute('admin/pemuda/import', 'GET');
        $this->assertContains('auth', $filterForImport);
        $this->assertContains('role:superadmin', $filterForImport);
    }

    public function testTemplateGeneratorCreatesValidSpreadsheet(): void
    {
        $spreadsheet = $this->importService->generateTemplate();

        $this->assertInstanceOf(Spreadsheet::class, $spreadsheet);
        $this->assertEquals(2, $spreadsheet->getSheetCount());

        // Sheet 1: Format Import Pemuda
        $sheet1 = $spreadsheet->getSheet(0);
        $this->assertEquals('Format Import Pemuda', $sheet1->getTitle());

        // Verify Essential Headers
        $this->assertStringContainsString('Nama Lengkap', (string) $sheet1->getCell('A1')->getValue());
        $this->assertStringContainsString('Jenis Kelamin', (string) $sheet1->getCell('B1')->getValue());
        $this->assertStringContainsString('Cabang', (string) $sheet1->getCell('C1')->getValue());
        $this->assertStringContainsString('Tempat Lahir', (string) $sheet1->getCell('D1')->getValue());
        $this->assertStringContainsString('Tanggal Lahir', (string) $sheet1->getCell('E1')->getValue());
        $this->assertStringContainsString('Telepon', (string) $sheet1->getCell('F1')->getValue());
        $this->assertStringContainsString('Kecamatan', (string) $sheet1->getCell('J1')->getValue());
        $this->assertStringContainsString('Desa', (string) $sheet1->getCell('K1')->getValue());
        $this->assertStringContainsString('Pendidikan', (string) $sheet1->getCell('P1')->getValue());
        $this->assertStringContainsString('Pekerjaan', (string) $sheet1->getCell('U1')->getValue());

        // Verify Sample Rows are present
        $this->assertEquals('Muhammad Yusuf', $sheet1->getCell('A2')->getValue());
        $this->assertEquals('L', $sheet1->getCell('B2')->getValue());
        $this->assertEquals('Sragen 1', $sheet1->getCell('C2')->getValue());

        $this->assertEquals('Aisyah Nur Rahma', $sheet1->getCell('A3')->getValue());
        $this->assertEquals('P', $sheet1->getCell('B3')->getValue());

        // Sheet 2: Referensi & Petunjuk
        $sheet2 = $spreadsheet->getSheet(1);
        $this->assertEquals('Referensi & Petunjuk', $sheet2->getTitle());
        $this->assertStringContainsString('PANDUAN', (string) $sheet2->getCell('A1')->getValue());
        $this->assertStringContainsString('WILAYAH & CABANG', (string) $sheet2->getCell('A15')->getValue());
    }

    public function testHeaderMappingMethod(): void
    {
        $reflection = new \ReflectionClass(PemudaImportService::class);
        $method = $reflection->getMethod('mapHeaderColumns');
        $method->setAccessible(true);

        $headers = [
            'Nama Lengkap *',
            'Jenis Kelamin * (L/P)',
            'Cabang * (Nama/Kode)',
            'Tempat Lahir *',
            'Tanggal Lahir * (YYYY-MM-DD)',
            'No. Telepon / WA *',
            'Email',
            'Status Nikah',
            'Golongan Darah',
            'Kecamatan *',
            'Desa / Kelurahan *',
            'Dusun / Dukuh',
            'RT',
            'RW',
            'Alamat Detail *',
            'Jenjang Pendidikan *',
            'Nama Sekolah / Kampus *',
            'Jurusan',
            'Status Pendidikan *',
            'Tahun Lulus',
            'Status Pekerjaan *',
            'Profesi / Jabatan',
            'Nama Perusahaan',
            'Bidang Usaha',
            'Organisasi',
            'Keahlian',
            'Minat',
            'Status Verifikasi',
        ];

        $map = $method->invoke($this->importService, $headers);

        $this->assertEquals(0, $map['name']);
        $this->assertEquals(1, $map['gender']);
        $this->assertEquals(2, $map['cabang']);
        $this->assertEquals(3, $map['birth_place']);
        $this->assertEquals(4, $map['birth_date']);
        $this->assertEquals(5, $map['phone']);
        $this->assertEquals(6, $map['email']);
        $this->assertEquals(7, $map['marital_status']);
        $this->assertEquals(8, $map['blood_type']);
        $this->assertEquals(9, $map['district']);
        $this->assertEquals(10, $map['village']);
        $this->assertEquals(14, $map['address_detail']);
        $this->assertEquals(15, $map['education_level']);
        $this->assertEquals(16, $map['school_name']);
        $this->assertEquals(18, $map['education_status']);
        $this->assertEquals(20, $map['job_status']);
        $this->assertEquals(24, $map['organisasi']);
        $this->assertEquals(25, $map['skills']);
        $this->assertEquals(26, $map['interests']);
        $this->assertEquals(27, $map['status_verifikasi']);
    }

    public function testDateParsingFormats(): void
    {
        $reflection = new \ReflectionClass(PemudaImportService::class);
        $method = $reflection->getMethod('parseDateValue');
        $method->setAccessible(true);

        // ISO format YYYY-MM-DD
        $this->assertEquals('2001-05-14', $method->invoke($this->importService, '2001-05-14'));
        $this->assertEquals('1999-12-31', $method->invoke($this->importService, '1999-12-31'));

        // Indo format DD/MM/YYYY
        $this->assertEquals('2003-11-20', $method->invoke($this->importService, '20/11/2003'));
        $this->assertEquals('1998-08-17', $method->invoke($this->importService, '17-08-1998'));

        // Excel Numeric Date (e.g. 37025 = 2001-05-14)
        $excelDate = 37025;
        $this->assertEquals('2001-05-14', $method->invoke($this->importService, $excelDate));

        // Invalid / null dates
        $this->assertNull($method->invoke($this->importService, null));
        $this->assertNull($method->invoke($this->importService, ''));
        $this->assertNull($method->invoke($this->importService, 'invalid-date-string'));
    }

    public function testEmptyRowDetection(): void
    {
        $reflection = new \ReflectionClass(PemudaImportService::class);
        $method = $reflection->getMethod('isRowEmpty');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($this->importService, [null, '', '   ', null]));
        $this->assertFalse($method->invoke($this->importService, [null, 'Budi', '', null]));
    }

    public function testParseAndValidateValidRow(): void
    {
        $reflection = new \ReflectionClass(PemudaImportService::class);
        $mapMethod = $reflection->getMethod('mapHeaderColumns');
        $mapMethod->setAccessible(true);
        $preloadMethod = $reflection->getMethod('preloadLookups');
        $preloadMethod->setAccessible(true);
        $parseMethod = $reflection->getMethod('parseAndValidateRow');
        $parseMethod->setAccessible(true);

        $headers = [
            'Nama Lengkap *',
            'Jenis Kelamin * (L/P)',
            'Cabang * (Nama/Kode)',
            'Tempat Lahir *',
            'Tanggal Lahir * (YYYY-MM-DD)',
            'No. Telepon / WA *',
            'Email',
            'Status Nikah',
            'Golongan Darah',
            'Kecamatan *',
            'Desa / Kelurahan *',
            'Dusun / Dukuh',
            'RT',
            'RW',
            'Alamat Detail *',
            'Jenjang Pendidikan *',
            'Nama Sekolah / Kampus *',
            'Jurusan',
            'Status Pendidikan *',
            'Tahun Lulus',
            'Status Pekerjaan *',
            'Profesi / Jabatan',
            'Nama Perusahaan',
            'Bidang Usaha',
            'Organisasi',
            'Keahlian',
            'Minat',
            'Status Verifikasi',
        ];

        $map = $mapMethod->invoke($this->importService, $headers);
        $lookups = $preloadMethod->invoke($this->importService);

        $rowValues = [
            'Ahmad Fauzi',
            'L',
            'Sragen 1',
            'Sragen',
            '2000-01-15',
            "'081299887766",
            'fauzi@test.com',
            'belum_menikah',
            'B',
            'Sragen',
            'Sragen Wetan',
            'Krapyak',
            '03',
            '01',
            'Jl. Sukowati No. 10',
            'Sarjana (S1 / D4)',
            'UNS',
            'Teknik',
            'lulus',
            '2022',
            'Karyawan Swasta',
            'Staff IT',
            'PT Maju Bersama',
            'Teknologi',
            'Karang Taruna, Satgas',
            'Pemrograman & IT (Web/Mobile), Desain Grafis & Multimedia',
            'Teknologi & Robotika',
            'verified',
        ];

        $res = $parseMethod->invoke($this->importService, $rowValues, $map, 2, $lookups, 'verified');

        $this->assertEmpty($res['errors']);
        $this->assertEquals('Ahmad Fauzi', $res['data']['name']);
        $this->assertEquals('L', $res['data']['gender']);
        $this->assertEquals('2000-01-15', $res['data']['birth_date']);
        $this->assertEquals('081299887766', $res['data']['phone']);
        $this->assertEquals('belum_menikah', $res['data']['marital_status']);
        $this->assertEquals('B', $res['data']['blood_type']);
        $this->assertEquals('verified', $res['data']['status_verifikasi']);
        $this->assertCount(2, $res['data']['organisasi_list']);
        $this->assertContains('Karang Taruna', $res['data']['organisasi_list']);
    }

    public function testParseAndValidateInvalidRowDetectsErrors(): void
    {
        $reflection = new \ReflectionClass(PemudaImportService::class);
        $mapMethod = $reflection->getMethod('mapHeaderColumns');
        $mapMethod->setAccessible(true);
        $preloadMethod = $reflection->getMethod('preloadLookups');
        $preloadMethod->setAccessible(true);
        $parseMethod = $reflection->getMethod('parseAndValidateRow');
        $parseMethod->setAccessible(true);

        $headers = [
            'Nama Lengkap *',
            'Jenis Kelamin *',
            'Cabang *',
            'Tanggal Lahir *',
            'No. Telepon / WA *',
        ];

        $map = $mapMethod->invoke($this->importService, $headers);
        $lookups = $preloadMethod->invoke($this->importService);

        // Row with missing/invalid fields
        $rowValues = [
            '', // Empty name
            'X', // Invalid gender
            'Cabang Tidak Ada', // Invalid cabang
            'tanggal-salah', // Invalid date
            '123', // Phone too short
        ];

        $res = $parseMethod->invoke($this->importService, $rowValues, $map, 5, $lookups, 'verified');

        $this->assertNotEmpty($res['errors']);
        $errorStr = implode(' | ', $res['errors']);
        $this->assertStringContainsString('Nama Lengkap', $errorStr);
        $this->assertStringContainsString('Jenis Kelamin', $errorStr);
        $this->assertStringContainsString('Cabang', $errorStr);
        $this->assertStringContainsString('Tanggal Lahir', $errorStr);
        $this->assertStringContainsString('Telepon', $errorStr);
    }
}
