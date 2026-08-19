<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EducationLevelSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name'        => 'SD',
                'description' => 'Sekolah Dasar',
            ],
            [
                'name'        => 'SMP',
                'description' => 'Sekolah Menengah Pertama',
            ],
            [
                'name'        => 'SMA/SMK',
                'description' => 'Sekolah Menengah Atas/Kejuruan',
            ],
            [
                'name'        => 'D1',
                'description' => 'Diploma 1',
            ],
            [
                'name'        => 'D2',
                'description' => 'Diploma 2',
            ],
            [
                'name'        => 'D3',
                'description' => 'Diploma 3',
            ],
            [
                'name'        => 'D4/S1',
                'description' => 'Diploma 4/Sarjana',
            ],
            [
                'name'        => 'S2',
                'description' => 'Magister',
            ],
            [
                'name'        => 'S3',
                'description' => 'Doktor',
            ],
        ];

        $this->db->table('education_levels')->insertBatch($data);
    }
}
