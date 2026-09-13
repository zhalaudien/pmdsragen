<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateRoleDefinitionsAndScopes extends Migration
{
    public function up()
    {
        // 1. Update descriptions for all roles in user_roles table
        $roleUpdates = [
            1 => [
                'description' => 'Super Administrator yang mengelola seluruh sistem dan data',
            ],
            2 => [
                'description' => 'Administrator tingkat Wilayah',
            ],
            3 => [
                'description' => 'Administrator tingkat Cabang untuk manajemen data pada cabang tersebut',
            ],
            4 => [
                'description' => 'Administrator seluruh Sragen yang mengelola data pemuda (Laki-laki)',
            ],
            5 => [
                'description' => 'Administrator seluruh Sragen yang mengelola data pemudi (Perempuan)',
            ],
            6 => [
                'description' => 'Administrator tingkat Wilayah yang mengelola data pemuda (Laki-laki)',
            ],
        ];

        foreach ($roleUpdates as $id => $data) {
            $this->db->table('user_roles')
                     ->where('id', $id)
                     ->update($data);
        }

        // 2. Normalize existing users with role admin_pemuda (4) and admin_pemudi (5):
        // Since they are now district-wide (seluruh Sragen), clear their wilayah_id and cabang_id
        $this->db->table('users')
                 ->whereIn('role_id', [4, 5])
                 ->update([
                     'wilayah_id' => null,
                     'cabang_id'  => null,
                 ]);
    }

    public function down()
    {
        $roleReverts = [
            1 => ['description' => 'Super Administrator dengan akses penuh seluruh sistem'],
            2 => ['description' => 'Administrator tingkat Wilayah'],
            3 => ['description' => 'Administrator tingkat Cabang'],
            4 => ['description' => 'Administrator tingkat Cabang khusus pemuda Laki-laki'],
            5 => ['description' => 'Administrator tingkat Cabang khusus pemuda Perempuan'],
            6 => ['description' => 'Administrator tingkat Wilayah khusus pemuda Laki-laki'],
        ];

        foreach ($roleReverts as $id => $data) {
            $this->db->table('user_roles')
                     ->where('id', $id)
                     ->update($data);
        }
    }
}
