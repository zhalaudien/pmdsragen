<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name'        => 'super_admin',
                'description' => 'Administrator dengan akses penuh',
            ],
            [
                'name'        => 'operator',
                'description' => 'Operator pengelola data pemuda',
            ],
        ];

        $this->db->table('user_roles')->insertBatch($data);
    }
}
