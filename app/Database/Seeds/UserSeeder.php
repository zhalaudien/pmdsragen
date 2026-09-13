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
            [
                'id'         => 4,
                'name'       => 'Admin Pemuda Sragen',
                'email'      => 'admin.pemuda@pmdsragen.org',
                'username'   => 'admin_pemuda',
                'password'   => $defaultPassword,
                'role_id'    => 4, // admin_pemuda (seluruh Sragen, L)
                'wilayah_id' => null,
                'cabang_id'  => null,
                'status'     => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id'         => 5,
                'name'       => 'Admin Pemudi Sragen',
                'email'      => 'admin.pemudi@pmdsragen.org',
                'username'   => 'admin_pemudi',
                'password'   => $defaultPassword,
                'role_id'    => 5, // admin_pemudi (seluruh Sragen, P)
                'wilayah_id' => null,
                'cabang_id'  => null,
                'status'     => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id'         => 6,
                'name'       => 'Admin Wilayah 1 Pemuda',
                'email'      => 'admin.w1pemuda@pmdsragen.org',
                'username'   => 'admin_w1_pemuda',
                'password'   => $defaultPassword,
                'role_id'    => 6, // admin_wilayah_pemuda (wilayah 1, L)
                'wilayah_id' => 1,
                'cabang_id'  => null,
                'status'     => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('users')->insertBatch($users);
        $this->db->enableForeignKeyChecks();
    }
}
