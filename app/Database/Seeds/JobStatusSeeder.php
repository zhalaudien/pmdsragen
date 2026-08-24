<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class JobStatusSeeder extends Seeder
{
    public function run()
    {
        $this->db->disableForeignKeyChecks();
        $this->db->table('job_statuses')->truncate();

        $data = [
            [
                'id'          => 1,
                'name'        => 'Belum / Tidak Bekerja',
                'description' => 'Belum atau sedang tidak bekerja',
            ],
            [
                'id'          => 2,
                'name'        => 'Pelajar / Mahasiswa',
                'description' => 'Sedang menempuh pendidikan sekolah atau perkuliahan',
            ],
            [
                'id'          => 3,
                'name'        => 'Karyawan Swasta',
                'description' => 'Bekerja sebagai pegawai di perusahaan swasta',
            ],
            [
                'id'          => 4,
                'name'        => 'Pegawai Negeri / ASN / PPPK',
                'description' => 'Aparatur Sipil Negara, PNS, PPPK, atau instansi pemerintahan',
            ],
            [
                'id'          => 5,
                'name'        => 'Wirausaha / Pemilik Usaha',
                'description' => 'Memiliki atau mengelola bisnis/usaha mandiri',
            ],
            [
                'id'          => 6,
                'name'        => 'Freelancer / Pekerja Lepas',
                'description' => 'Pekerja profesional mandiri/lepas',
            ],
            [
                'id'          => 7,
                'name'        => 'Petani / Peternak',
                'description' => 'Bekerja di sektor pertanian, perkebunan, atau peternakan',
            ],
            [
                'id'          => 8,
                'name'        => 'Lainnya',
                'description' => 'Bidang pekerjaan atau profesi lainnya',
            ],
        ];

        $this->db->table('job_statuses')->insertBatch($data);
        $this->db->enableForeignKeyChecks();
    }
}
