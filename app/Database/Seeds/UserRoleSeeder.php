<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    public function run()
    {
        $this->db->disableForeignKeyChecks();
        $this->db->table('user_roles')->truncate();

        $data = [
            [
                'id'          => 1,
                'name'        => 'superadmin',
                'description' => 'Super Administrator yang mengelola seluruh sistem dan data',
            ],
            [
                'id'          => 2,
                'name'        => 'admin_wilayah',
                'description' => 'Administrator tingkat Wilayah',
            ],
            [
                'id'          => 3,
                'name'        => 'admin_cabang',
                'description' => 'Administrator tingkat Cabang untuk manajemen data pada cabang tersebut',
            ],
            [
                'id'          => 4,
                'name'        => 'admin_pemuda',
                'description' => 'Administrator seluruh Sragen yang mengelola data pemuda (Laki-laki)',
            ],
            [
                'id'          => 5,
                'name'        => 'admin_pemudi',
                'description' => 'Administrator seluruh Sragen yang mengelola data pemudi (Perempuan)',
            ],
            [
                'id'          => 6,
                'name'        => 'admin_wilayah_pemuda',
                'description' => 'Administrator tingkat Wilayah yang mengelola data pemuda (Laki-laki)',
            ],
        ];

        $this->db->table('user_roles')->insertBatch($data);
        $this->db->enableForeignKeyChecks();
    }
}
