<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDetailsToCabang extends Migration
{
    public function up()
    {
        $fields = [
            'alamat' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'description',
            ],
            'pimpinan_nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'alamat',
            ],
            'no_wa' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'pimpinan_nama',
            ],
            'has_gelombang' => [
                'type'       => 'ENUM',
                'constraint' => ['sudah', 'belum'],
                'default'    => 'belum',
                'null'       => false,
                'after'      => 'no_wa',
            ],
            'gelombang_hari' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'has_gelombang',
            ],
            'gelombang_jam' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'gelombang_hari',
            ],
            'gelombang_ustadz' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'gelombang_jam',
            ],
        ];

        $this->forge->addColumn('cabang', $fields);

        // Add index for has_gelombang
        $this->db->query('ALTER TABLE `cabang` ADD INDEX `idx_cabang_has_gelombang` (`has_gelombang`)');
    }

    public function down()
    {
        $this->forge->dropColumn('cabang', [
            'alamat',
            'pimpinan_nama',
            'no_wa',
            'has_gelombang',
            'gelombang_hari',
            'gelombang_jam',
            'gelombang_ustadz',
        ]);
    }
}
