<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMapsUrlToCabang extends Migration
{
    public function up()
    {
        $fields = [
            'maps_url' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
                'after'      => 'alamat',
            ],
        ];

        $this->forge->addColumn('cabang', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('cabang', ['maps_url']);
    }
}
