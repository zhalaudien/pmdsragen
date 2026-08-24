<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EducationLevelSeeder extends Seeder
{
    public function run()
    {
        $this->db->disableForeignKeyChecks();
        $this->db->table('education_levels')->truncate();

        $data = [
            [
                'id'          => 1,
                'name'        => 'SD / Sederajat',
                'description' => 'Sekolah Dasar / MI / Sederajat',
            ],
            [
                'id'          => 2,
                'name'        => 'SMP / MTs / Sederajat',
                'description' => 'Sekolah Menengah Pertama / MTs / Sederajat',
            ],
            [
                'id'          => 3,
                'name'        => 'SMA / SMK / MA',
                'description' => 'Sekolah Menengah Atas / Kejuruan / MA / Sederajat',
            ],
            [
                'id'          => 4,
                'name'        => 'Diploma (D1 / D2 / D3)',
                'description' => 'Program Diploma Satu, Dua, atau Tiga',
            ],
            [
                'id'          => 5,
                'name'        => 'Sarjana (S1 / D4)',
                'description' => 'Program Sarjana S1 atau Diploma 4 Terapan',
            ],
            [
                'id'          => 6,
                'name'        => 'Magister (S2)',
                'description' => 'Program Magister / Pascasarjana S2',
            ],
            [
                'id'          => 7,
                'name'        => 'Doktor (S3)',
                'description' => 'Program Doktoral S3',
            ],
        ];

        $this->db->table('education_levels')->insertBatch($data);
        $this->db->enableForeignKeyChecks();
    }
}
