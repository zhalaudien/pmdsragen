<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMtaSyncFields extends Migration
{
    public function up()
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Tambahkan kolom MTA pada tabel WILAYAH
        |--------------------------------------------------------------------------
        */
        $this->forge->addColumn('wilayah', [
            'mta_uuid' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
                'after'      => 'description',
            ],
            'mta_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'mta_uuid',
            ],
        ]);

        $this->db->query('ALTER TABLE `wilayah` ADD INDEX `idx_wilayah_mta_uuid` (`mta_uuid`)');

        /*
        |--------------------------------------------------------------------------
        | 2. Tambahkan kolom MTA pada tabel CABANG
        |--------------------------------------------------------------------------
        */
        $this->forge->addColumn('cabang', [
            'mta_uuid' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
                'after'      => 'gelombang_ustadz',
            ],
            'mta_last_synced_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'mta_uuid',
            ],
        ]);

        $this->db->query('ALTER TABLE `cabang` ADD INDEX `idx_cabang_mta_uuid` (`mta_uuid`)');

        /*
        |--------------------------------------------------------------------------
        | 3. Tambahkan kolom MTA pada tabel PEMUDA
        |--------------------------------------------------------------------------
        */
        $this->forge->addColumn('pemuda', [
            'mta_warga_uuid' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
                'after'      => 'status_data',
            ],
            'mta_status_warga' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'mta_warga_uuid',
            ],
            'mta_ayah_uuid' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
                'after'      => 'mta_status_warga',
            ],
            'mta_ibu_uuid' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
                'after'      => 'mta_ayah_uuid',
            ],
            'mta_foto_url' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'mta_ibu_uuid',
            ],
            'mta_synced_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'mta_foto_url',
            ],
        ]);

        $this->db->query('ALTER TABLE `pemuda` ADD INDEX `idx_pemuda_mta_warga_uuid` (`mta_warga_uuid`)');

        /*
        |--------------------------------------------------------------------------
        | 4. Tabel Audit Log SINKRONISASI MTA (mta_sync_logs)
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'sync_type' => [
                'type'       => 'ENUM',
                'constraint' => ['perwakilan', 'cabang', 'warga', 'check'],
                'default'    => 'warga',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['success', 'failed'],
                'default'    => 'success',
            ],
            'total_records' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'default'    => 0,
            ],
            'message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_by' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('sync_type');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'SET NULL', 'CASCADE');

        $this->forge->createTable('mta_sync_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('mta_sync_logs', true);

        $this->forge->dropColumn('pemuda', [
            'mta_warga_uuid',
            'mta_status_warga',
            'mta_ayah_uuid',
            'mta_ibu_uuid',
            'mta_foto_url',
            'mta_synced_at',
        ]);

        $this->forge->dropColumn('cabang', [
            'mta_uuid',
            'mta_last_synced_at',
        ]);

        $this->forge->dropColumn('wilayah', [
            'mta_uuid',
            'mta_code',
        ]);
    }
}
