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
        $this->assertSame('Ust. Ahmad Dahlan', $saved['pimpinan_nama']);
        $this->assertSame('081234567890', $saved['no_wa']);
        $this->assertSame('sudah', $saved['has_gelombang']);
        $this->assertSame('Ahad', $saved['gelombang_hari']);
        $this->assertSame('19:30 - 21:00 WIB', $saved['gelombang_jam']);
        $this->assertSame('Ust. Muhammad Ihsan', $saved['gelombang_ustadz']);

        // Test update
        $cabangModel->update($insertedId, [
            'pimpinan_nama' => 'Ust. Budi Santoso',
            'has_gelombang' => 'belum',
        ]);

        $updated = $cabangModel->find($insertedId);
        $this->assertSame('Ust. Budi Santoso', $updated['pimpinan_nama']);
        $this->assertSame('belum', $updated['has_gelombang']);

        // Clean up
        $cabangModel->delete($insertedId);
        $this->assertNull($cabangModel->find($insertedId));
    }
}
