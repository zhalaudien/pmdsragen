<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNewRolesAdminPemudaPemudi extends Migration
{
    public function up()
    {
        $roles = [
            [
                'id'          => 4,
                'name'        => 'admin_pemuda',
                'description' => 'Administrator tingkat Cabang khusus pemuda Laki-laki',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'id'          => 5,
                'name'        => 'admin_pemudi',
                'description' => 'Administrator tingkat Cabang khusus pemuda Perempuan',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'id'          => 6,
                'name'        => 'admin_wilayah_pemuda',
                'description' => 'Administrator tingkat Wilayah khusus pemuda Laki-laki',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($roles as $role) {
            $exists = $this->db->table('user_roles')->where('name', $role['name'])->countAllResults();
            if ($exists === 0) {
                $this->db->table('user_roles')->insert($role);
            }
        }
    }

    public function down()
    {
        $this->db->table('user_roles')->whereIn('name', [
            'admin_pemuda',
            'admin_pemudi',
            'admin_wilayah_pemuda',
        ])->delete();
    }
}
