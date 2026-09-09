<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFotoToPemuda extends Migration
{
    public function up()
    {
        $fields = [
            'foto' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
                'after'      => 'mta_foto_url',
            ],
        ];

        $this->forge->addColumn('pemuda', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('pemuda', ['foto']);
    }
}
