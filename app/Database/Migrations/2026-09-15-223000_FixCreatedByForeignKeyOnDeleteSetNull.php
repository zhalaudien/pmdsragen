<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixCreatedByForeignKeyOnDeleteSetNull extends Migration
{
    public function up()
    {
        // Perbaikan relasi created_by ke tabel users:
        // Sebelumnya keliru terdefinisi ON DELETE CASCADE, sehingga ketika user/admin dihapus,
        // seluruh data pemuda yang dibuat/diimpor oleh user tersebut ikut terhapus otomatis oleh MySQL.
        // Seharusnya ON DELETE SET NULL dan ON UPDATE CASCADE.

        $tables = [
            'pemuda'         => 'pemuda_created_by_foreign',
            'forms'          => 'forms_created_by_foreign',
            'mta_sync_logs'  => 'mta_sync_logs_created_by_foreign',
            'mta_sync_queue' => 'mta_sync_queue_created_by_foreign',
        ];

        if ($this->db->DBDriver === 'MySQLi') {
            foreach ($tables as $table => $constraintName) {
                if ($this->db->tableExists($table)) {
                    // Cari nama constraint foreign key aktual untuk kolom created_by yang mengarah ke tabel users
                    $fkQuery = $this->db->query("
                        SELECT CONSTRAINT_NAME 
                        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                        WHERE TABLE_SCHEMA = '" . $this->db->getDatabase() . "' 
                          AND TABLE_NAME = '{$table}' 
                          AND COLUMN_NAME = 'created_by' 
                          AND REFERENCED_TABLE_NAME = 'users'
                    ");
                    $rows = $fkQuery->getResultArray();

                    foreach ($rows as $row) {
                        $actualConstraint = $row['CONSTRAINT_NAME'];
                        $this->db->query("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$actualConstraint}`");
                    }

                    // Tambahkan constraint yang benar: ON UPDATE CASCADE ON DELETE SET NULL
                    $this->db->query("
                        ALTER TABLE `{$table}` 
                        ADD CONSTRAINT `{$constraintName}` 
                        FOREIGN KEY (`created_by`) 
                        REFERENCES `users` (`id`) 
                        ON UPDATE CASCADE 
                        ON DELETE SET NULL
                    ");
                }
            }
        }
    }

    public function down()
    {
        // Revert ke konfigurasi sebelumnya jika di-rollback
        $tables = [
            'pemuda'         => 'pemuda_created_by_foreign',
            'forms'          => 'forms_created_by_foreign',
            'mta_sync_logs'  => 'mta_sync_logs_created_by_foreign',
            'mta_sync_queue' => 'mta_sync_queue_created_by_foreign',
        ];

        if ($this->db->DBDriver === 'MySQLi') {
            foreach ($tables as $table => $constraintName) {
                if ($this->db->tableExists($table)) {
                    $fkQuery = $this->db->query("
                        SELECT CONSTRAINT_NAME 
                        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                        WHERE TABLE_SCHEMA = '" . $this->db->getDatabase() . "' 
                          AND TABLE_NAME = '{$table}' 
                          AND COLUMN_NAME = 'created_by' 
                          AND REFERENCED_TABLE_NAME = 'users'
                    ");
                    $rows = $fkQuery->getResultArray();

                    foreach ($rows as $row) {
                        $actualConstraint = $row['CONSTRAINT_NAME'];
                        $this->db->query("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$actualConstraint}`");
                    }

                    $this->db->query("
                        ALTER TABLE `{$table}` 
                        ADD CONSTRAINT `{$constraintName}` 
                        FOREIGN KEY (`created_by`) 
                        REFERENCES `users` (`id`) 
                        ON UPDATE SET NULL 
                        ON DELETE CASCADE
                    ");
                }
            }
        }
    }
}
