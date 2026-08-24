<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $this->db->disableForeignKeyChecks();
        $this->db->table('users')->truncate();

        $defaultPassword = password_hash('admin123', PASSWORD_DEFAULT);

        $users = [
            [
                'id'         => 1,
                'name'       => 'Super Administrator',
                'email'      => 'superadmin@pmdsragen.org',
                'username'   => 'superadmin',
                'password'   => $defaultPassword,
                'role_id'    => 1, // superadmin
                'wilayah_id' => null,
                'cabang_id'  => null,
                'status'     => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id'         => 2,
                'name'       => 'Admin Wilayah 1',
                'email'      => 'admin.w1@pmdsragen.org',
                'username'   => 'admin_w1',
                'password'   => $defaultPassword,
                'role_id'    => 2, // admin_wilayah
                'wilayah_id' => 1,
                'cabang_id'  => null,
                'status'     => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id'         => 3,
                'name'       => 'Admin Cabang Gesi',
                'email'      => 'admin.gesi@pmdsragen.org',
                'username'   => 'admin_gesi',
                'password'   => $defaultPassword,
                'role_id'    => 3, // admin_cabang
                'wilayah_id' => 1,
                'cabang_id'  => 1,
                'status'     => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('users')->insertBatch($users);
        $this->db->enableForeignKeyChecks();
    }
}
