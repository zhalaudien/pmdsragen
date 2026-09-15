<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateVillagePilangInMasaran extends Migration
{
    public function up()
    {
        // Koreksi nama desa pada Kecamatan Masaran (district_id = 7) dari Pilangsari menjadi Pilang
        $this->db->table('villages')
            ->where('district_id', 7)
            ->where('name', 'Pilangsari')
            ->update(['name' => 'Pilang']);
    }

    public function down()
    {
        $this->db->table('villages')
            ->where('district_id', 7)
            ->where('name', 'Pilang')
            ->update(['name' => 'Pilangsari']);
    }
}
