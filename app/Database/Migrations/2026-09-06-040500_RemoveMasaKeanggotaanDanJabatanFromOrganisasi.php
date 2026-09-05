<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveMasaKeanggotaanDanJabatanFromOrganisasi extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('organisasi', [
            'position',
            'join_date',
            'end_date',
        ]);
    }

    public function down()
    {
        $fields = [
            'position' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'organization_name',
            ],
            'join_date' => [
                'type'  => 'DATE',
                'null'  => true,
                'after' => 'position',
            ],
            'end_date' => [
                'type'  => 'DATE',
                'null'  => true,
                'after' => 'join_date',
            ],
        ];

        $this->forge->addColumn('organisasi', $fields);
    }
}
