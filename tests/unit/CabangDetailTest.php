<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\CabangModel;
use Config\Database;

/**
 * @internal
 */
final class CabangDetailTest extends CIUnitTestCase
{
    public function testCabangModelFieldsAndCrud(): void
    {
        $db = Database::connect('default');
        $cabangModel = new CabangModel($db);

        // Test insert with new detail fields
        $testData = [
            'wilayah_id'       => 1,
            'code'             => 'TEST-001',
            'name'             => 'Cabang Uji Coba Unit Test',
            'alamat'           => 'Jl. Sukowati No. 99, Sragen',
            'maps_url'         => 'https://maps.app.goo.gl/example123',
            'pimpinan_nama'    => 'Ust. Ahmad Dahlan',
            'no_wa'            => '081234567890',
            'has_gelombang'    => 'sudah',
            'gelombang_hari'   => 'Ahad',
            'gelombang_jam'    => '19:30 - 21:00 WIB',
            'gelombang_ustadz' => 'Ust. Muhammad Ihsan',
            'description'      => 'Cabang untuk pengujian unit test sistem',
        ];

        $insertedId = $cabangModel->insert($testData, true);
        $this->assertIsInt($insertedId);
        $this->assertGreaterThan(0, $insertedId);

        // Test retrieve and check all fields
        $saved = $cabangModel->find($insertedId);
        $this->assertNotNull($saved);
        $this->assertSame('Cabang Uji Coba Unit Test', $saved['name']);
        $this->assertSame('Jl. Sukowati No. 99, Sragen', $saved['alamat']);
        $this->assertSame('https://maps.app.goo.gl/example123', $saved['maps_url']);
        $this->assertSame('Ust. Ahmad Dahlan', $saved['pimpinan_nama']);
        $this->assertSame('081234567890', $saved['no_wa']);
        $this->assertSame('sudah', $saved['has_gelombang']);
        $this->assertSame('Ahad', $saved['gelombang_hari']);
        $this->assertSame('19:30 - 21:00 WIB', $saved['gelombang_jam']);
        $this->assertSame('Ust. Muhammad Ihsan', $saved['gelombang_ustadz']);

        // Test update
        $cabangModel->update($insertedId, [
            'pimpinan_nama' => 'Ust. Budi Santoso',
            'maps_url'      => 'https://maps.google.com/?q=-7.42,111.02',
            'has_gelombang' => 'belum',
        ]);

        $updated = $cabangModel->find($insertedId);
        $this->assertSame('Ust. Budi Santoso', $updated['pimpinan_nama']);
        $this->assertSame('https://maps.google.com/?q=-7.42,111.02', $updated['maps_url']);
        $this->assertSame('belum', $updated['has_gelombang']);

        // Clean up
        $cabangModel->delete($insertedId);
        $this->assertNull($cabangModel->find($insertedId));
    }

    public function testFormatMapsUrlHelper(): void
    {
        $this->assertNull(formatMapsUrl(null));
        $this->assertNull(formatMapsUrl(''));
        $this->assertNull(formatMapsUrl('   '));

        // Standard https URL
        $this->assertSame(
            'https://maps.app.goo.gl/abcdef',
            formatMapsUrl('https://maps.app.goo.gl/abcdef')
        );

        // Domain without scheme
        $this->assertSame(
            'https://maps.app.goo.gl/abcdef',
            formatMapsUrl('maps.app.goo.gl/abcdef')
        );

        // Google domain without scheme
        $this->assertSame(
            'https://goo.gl/maps/12345',
            formatMapsUrl('goo.gl/maps/12345')
        );

        // Coordinates format
        $this->assertSame(
            'https://www.google.com/maps?q=-7.4244%2C111.0234',
            formatMapsUrl('-7.4244, 111.0234')
        );

        // Iframe embed code
        $this->assertSame(
            'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1000',
            formatMapsUrl('<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1000" width="600"></iframe>')
        );
    }

    public function testCabangViewContainsMapsUrl(): void
    {
        $cabangView = APPPATH . 'Views/admin/cabang/index.php';
        $this->assertFileExists($cabangView);
        $content = file_get_contents($cabangView);

        $this->assertStringContainsString('name="maps_url"', $content);
        $this->assertStringContainsString('id="editCabangMapsUrl"', $content);
        $this->assertStringContainsString('id="detailCabangMapsContainer"', $content);
        $this->assertStringContainsString('data-mapsurl=', $content);
        $this->assertStringContainsString('formatMapsUrl', $content);
    }
}
