<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMaritalStatusAndBloodTypeToPemuda extends Migration
{
    public function up()
    {
        $fields = [
            'marital_status' => [
                'type'       => 'ENUM',
                'constraint' => ['belum_menikah', 'sudah_menikah', 'janda', 'duda'],
                'default'    => 'belum_menikah',
                'null'       => false,
                'after'      => 'gender',
            ],
            'blood_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'after'      => 'birth_date',
            ],
        ];

        $this->forge->addColumn('pemuda', $fields);

        // Add index for marital_status filter
        $this->db->query('ALTER TABLE `pemuda` ADD INDEX `idx_pemuda_marital_status` (`marital_status`)');
    }

    public function down()
    {
        $this->forge->dropColumn('pemuda', ['marital_status', 'blood_type']);
    }
}
