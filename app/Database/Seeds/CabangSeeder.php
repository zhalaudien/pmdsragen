<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CabangSeeder extends Seeder
{
    public function run()
    {
        $this->db->disableForeignKeyChecks();
        $this->db->table('cabang')->truncate();

        $cabangWilayah1 = [
            'Gesi',
            'Jenar',
            'Mondokan 1',
            'Mondokan 2',
            'Mondokan 3',
            'Sukodono 1',
            'Sukodono 2',
            'Sukodono 3',
            'Sukodono 4',
            'Sumberlawang 1',
            'Sumberlawang 2',
            'Sumberlawang 3',
            'Sumberlawang 4',
            'Tangen 1',
            'Tangen 2',
            'Tanon 1',
            'Tanon 2',
            'Tanon 3',
        ];

        $cabangWilayah2 = [
            'Gemolong 1',
            'Gemolong 2',
            'Gemolong 3',
            'Gemolong 4',
            'Gemolong 5',
            'Kalijambe 1',
            'Kalijambe 2',
            'Kalijambe 3',
            'Kalijambe 4',
            'Miri 1',
            'Miri 2',
            'Plupuh 1',
            'Plupuh 2',
            'Plupuh 3',
            'Plupuh 4',
            'Plupuh 5',
            'Plupuh 6',
        ];

        $cabangWilayah3 = [
            'Karangmalang 1',
            'Karangmalang 2',
            'Karangmalang 3',
            'Karangmalang 4',
            'Karangmalang 5',
            'Masaran 1',
            'Masaran 2',
            'Masaran 3',
            'Masaran 4',
            'Masaran 5',
            'Masaran 6',
            'Sambungmacan 1',
            'Sambungmacan 2',
            'Sambungmacan 3',
            'Sidoharjo 1',
            'Sidoharjo 2',
            'Sidoharjo 3',
            'Sidoharjo 4',
            'Sragen 1',
            'Sragen 2',
        ];

        $cabangWilayah4 = [
            'Gondang 1',
            'Gondang 2',
            'Gondang 3',
            'Gondang 4',
            'Kedawung 1',
            'Kedawung 2',
            'Kedawung 3',
            'Kedawung 4',
            'Kedawung 5',
            'Ngrampal 1',
            'Ngrampal 2',
            'Ngrampal 3',
            'Sambirejo 1',
            'Sambirejo 2',
        ];

        $data = [];
        $codeIndex = 1;
        $idIndex = 1;

        // Wilayah 1
        foreach ($cabangWilayah1 as $name) {
            $data[] = [
                'id'          => $idIndex++,
                'wilayah_id'  => 1,
                'code'        => 'CBG-' . str_pad((string) $codeIndex++, 3, '0', STR_PAD_LEFT),
                'name'        => $name,
                'description' => 'Cabang ' . $name . ' (Wilayah 1)',
            ];
        }

        // Wilayah 2
        foreach ($cabangWilayah2 as $name) {
            $data[] = [
                'id'          => $idIndex++,
                'wilayah_id'  => 2,
                'code'        => 'CBG-' . str_pad((string) $codeIndex++, 3, '0', STR_PAD_LEFT),
                'name'        => $name,
                'description' => 'Cabang ' . $name . ' (Wilayah 2)',
            ];
        }

        // Wilayah 3
        foreach ($cabangWilayah3 as $name) {
            $data[] = [
                'id'          => $idIndex++,
                'wilayah_id'  => 3,
                'code'        => 'CBG-' . str_pad((string) $codeIndex++, 3, '0', STR_PAD_LEFT),
                'name'        => $name,
                'description' => 'Cabang ' . $name . ' (Wilayah 3)',
            ];
        }

        // Wilayah 4
        foreach ($cabangWilayah4 as $name) {
            $data[] = [
                'id'          => $idIndex++,
                'wilayah_id'  => 4,
                'code'        => 'CBG-' . str_pad((string) $codeIndex++, 3, '0', STR_PAD_LEFT),
                'name'        => $name,
                'description' => 'Cabang ' . $name . ' (Wilayah 4)',
            ];
        }

        $this->db->table('cabang')->insertBatch($data);
        $this->db->enableForeignKeyChecks();
    }
}
