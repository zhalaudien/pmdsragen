<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\PemudaExportService;
use App\Models\PemudaModel;
use Config\Services;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use Config\Database;

/**
 * @internal
 */
final class PemudaExportTest extends CIUnitTestCase
{
    protected PemudaExportService $exportService;
    protected PemudaModel $pemudaModel;

    protected function setUp(): void
    {
        parent::setUp();
        $db = Database::connect('default');
        $this->pemudaModel   = new PemudaModel($db);
        $this->exportService = new PemudaExportService($this->pemudaModel);
    }

    public function testExportRoutesAreRegistered(): void
    {
        $routes = Services::routes();
        require APPPATH . 'Config/Routes.php';

        $getRoutes  = $routes->getRoutes('GET');
        $postRoutes = $routes->getRoutes('POST');

        $this->assertArrayHasKey('admin/pemuda/export', $getRoutes);
        $this->assertEquals('\App\Controllers\Admin\Pemuda::export', $getRoutes['admin/pemuda/export']);

        $this->assertArrayHasKey('admin/pemuda/export', $postRoutes);
        $this->assertEquals('\App\Controllers\Admin\Pemuda::exportDownload', $postRoutes['admin/pemuda/export']);

        $this->assertArrayHasKey('admin/pemuda/export/count', $getRoutes);
        $this->assertEquals('\App\Controllers\Admin\Pemuda::exportCount', $getRoutes['admin/pemuda/export/count']);
    }

    public function testCategorizedColumnsAndPresetsStructure(): void
    {
        $categories = PemudaExportService::CATEGORIZED_COLUMNS;
        $this->assertIsArray($categories);
        $this->assertArrayHasKey('personal', $categories);
        $this->assertArrayHasKey('wilayah_cabang', $categories);
        $this->assertArrayHasKey('alamat', $categories);
        $this->assertArrayHasKey('pendidikan', $categories);
        $this->assertArrayHasKey('pekerjaan', $categories);
        $this->assertArrayHasKey('organisasi_potensi', $categories);
        $this->assertEquals('Element Dakwah, Bakat & Minat', $categories['organisasi_potensi']['category_name']);
        $this->assertArrayHasKey('status_sistem', $categories);

        $allLabels = $this->exportService->getAllColumnLabels();
        $this->assertNotEmpty($allLabels);
        $this->assertArrayHasKey('registration_number', $allLabels);
        $this->assertArrayHasKey('name', $allLabels);
        $this->assertArrayHasKey('organizations', $allLabels);
        $this->assertEquals('Element Dakwah Yang Diikuti', $allLabels['organizations']);
        $this->assertArrayHasKey('skills', $allLabels);
        $this->assertArrayHasKey('interests', $allLabels);

        // Verify all preset columns exist in available columns
        foreach (PemudaExportService::PRESETS as $presetKey => $presetCols) {
            $this->assertIsArray($presetCols, "Preset {$presetKey} must be an array");
            $this->assertNotEmpty($presetCols, "Preset {$presetKey} must not be empty");
            foreach ($presetCols as $col) {
                $this->assertArrayHasKey($col, $allLabels, "Column {$col} in preset {$presetKey} must be defined");
            }
        }
    }

    public function testGenerateXlsxCreatesValidSpreadsheet(): void
    {
        $filters = ['gender' => 'L'];
        $selectedColumns = ['name', 'gender', 'phone', 'cabang_name', 'skills', 'interests'];
        $scope = ['role' => 'superadmin'];

        $spreadsheet = $this->exportService->generateXlsx($filters, $selectedColumns, $scope, [
            'user_name' => 'Test Admin',
        ]);

        $this->assertInstanceOf(Spreadsheet::class, $spreadsheet);
        $sheet = $spreadsheet->getActiveSheet();
        $this->assertEquals('Data Pemuda', $sheet->getTitle());

        // Row 1 contains main title
        $this->assertStringContainsString('DATA PEMUDA', (string) $sheet->getCell('A1')->getValue());

        // Header row starts at A4
        $this->assertEquals('No', $sheet->getCell('A4')->getValue());
        $this->assertEquals('Nama Lengkap', $sheet->getCell('B4')->getValue());
        $this->assertEquals('Jenis Kelamin', $sheet->getCell('C4')->getValue());
        $this->assertEquals('No. WhatsApp / HP', $sheet->getCell('D4')->getValue());
        $this->assertEquals('Cabang Pemuda MTA', $sheet->getCell('E4')->getValue());
        $this->assertEquals('Bakat / Keahlian', $sheet->getCell('F4')->getValue());
        $this->assertEquals('Minat', $sheet->getCell('G4')->getValue());
    }

    public function testModelFiltersSupportSkillsAndInterests(): void
    {
        $scope = ['role' => 'superadmin'];

        // Single skill filter
        $querySkill = $this->pemudaModel->getFilteredQuery(['skill_id' => 1], $scope);
        $this->assertNotNull($querySkill);

        // Multiple skill IDs filter
        $querySkills = $this->pemudaModel->getFilteredQuery(['skill_id' => [1, 2, 3]], $scope);
        $this->assertNotNull($querySkills);

        // Single interest filter
        $queryInterest = $this->pemudaModel->getFilteredQuery(['interest_id' => 1], $scope);
        $this->assertNotNull($queryInterest);

        // Multiple interest IDs filter
        $queryInterests = $this->pemudaModel->getFilteredQuery(['interest_id' => [1, 2]], $scope);
        $this->assertNotNull($queryInterests);

        // Combined skills, interests, age range, and organization
        $queryCombined = $this->pemudaModel->getFilteredQuery([
            'skill_id'          => [1],
            'interest_id'       => [2],
            'organization_name' => 'Pemuda',
            'min_age'           => 17,
            'max_age'           => 30,
        ], $scope);
        $this->assertNotNull($queryCombined);
    }

    public function testScopeRestrictionsEnforced(): void
    {
        // Admin Wilayah scope
        $wilayahScope = [
            'role'       => 'admin_wilayah',
            'wilayah_id' => 2,
            'cabang_id'  => null,
        ];
        $countWilayah = $this->exportService->countFiltered([], $wilayahScope);
        $this->assertIsInt($countWilayah);

        // Admin Cabang scope
        $cabangScope = [
            'role'       => 'admin_cabang',
            'wilayah_id' => 2,
            'cabang_id'  => 5,
        ];
        $countCabang = $this->exportService->countFiltered([], $cabangScope);
        $this->assertIsInt($countCabang);
    }

    public function testReferenceDataQueriesUsedInExportView(): void
    {
        $db = Database::connect('default');

        $skillModel          = new \App\Models\SkillModel($db);
        $skills              = $skillModel->orderBy('name', 'ASC')->findAll();
        $this->assertIsArray($skills);

        $interestModel       = new \App\Models\InterestModel($db);
        $interests           = $interestModel->orderBy('name', 'ASC')->findAll();
        $this->assertIsArray($interests);

        $educationLevelModel = new \App\Models\EducationLevelModel($db);
        $educationLevels     = $educationLevelModel->orderBy('id', 'ASC')->findAll();
        $this->assertIsArray($educationLevels);

        $jobStatusModel      = new \App\Models\JobStatusModel($db);
        $jobStatuses         = $jobStatusModel->orderBy('id', 'ASC')->findAll();
        $this->assertIsArray($jobStatuses);

        $wilayahModel        = new \App\Models\WilayahModel($db);
        $wilayahList         = $wilayahModel->orderBy('code', 'ASC')->findAll();
        $this->assertIsArray($wilayahList);

        $cabangModel         = new \App\Models\CabangModel($db);
        $cabangList          = $cabangModel->orderBy('name', 'ASC')->findAll();
        $this->assertIsArray($cabangList);
    }
}
