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
                'description' => 'Super Administrator dengan akses penuh seluruh sistem',
            ],
            [
                'id'          => 2,
                'name'        => 'admin_wilayah',
                'description' => 'Administrator tingkat Wilayah',
            ],
            [
                'id'          => 3,
                'name'        => 'admin_cabang',
                'description' => 'Administrator tingkat Cabang',
            ],
        ];

        $this->db->table('user_roles')->insertBatch($data);
        $this->db->enableForeignKeyChecks();
    }
}
