<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWirausahaDetailFieldsToPekerjaan extends Migration
{
    public function up()
    {
        $fields = [
            'business_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'business_field',
            ],
            'business_address' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'business_name',
            ],
            'business_contact' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'business_address',
            ],
            'business_social' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'business_contact',
            ],
        ];

        $this->forge->addColumn('pekerjaan', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('pekerjaan', [
            'business_name',
            'business_address',
            'business_contact',
            'business_social',
        ]);
    }
}
