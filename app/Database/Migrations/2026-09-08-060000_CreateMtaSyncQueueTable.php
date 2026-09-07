<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMtaSyncQueueTable extends Migration
{
    public function up()
    {
        /*
        |--------------------------------------------------------------------------
        | Tabel ANTRIAN SINKRONISASI MTA (mta_sync_queue)
        |--------------------------------------------------------------------------
        | Digunakan untuk mengelola antrian sinkronisasi data pemuda ke API MTA Pusat
        | dengan laju aman 40 data / menit (1 data per 1.5 detik) untuk mencegah
        | terlampauinya batas kuota 60 request / menit dari server pusat MTA.
        */
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'pemuda_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'cabang_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'processing', 'completed', 'failed'],
                'default'    => 'pending',
            ],
            'result' => [
                'type'       => 'ENUM',
                'constraint' => ['verified', 'pending', 'error'],
                'default'    => 'pending',
            ],
            'message' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'mta_warga_uuid' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
            ],
            'attempts' => [
                'type'     => 'INT',
                'unsigned' => true,
                'default'  => 0,
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
            'updated_at' => [
                'type'    => 'DATETIME',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            ],
            'processed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('status');
        $this->forge->addKey('pemuda_id');
        $this->forge->addKey('cabang_id');
        $this->forge->addKey('created_at');

        $this->forge->addForeignKey('pemuda_id', 'pemuda', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('cabang_id', 'cabang', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'SET NULL', 'CASCADE');

        $this->forge->createTable('mta_sync_queue', true);
    }

    public function down()
    {
        $this->forge->dropTable('mta_sync_queue', true);
    }
}
