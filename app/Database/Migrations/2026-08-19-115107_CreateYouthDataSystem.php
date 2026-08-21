<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateYouthDataSystem extends Migration
{
    public function up()
    {
        /*
        |--------------------------------------------------------------------------
        | 1. USER ROLES
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'description' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('user_roles', true);


        /*
        |--------------------------------------------------------------------------
        | 2. WILAYAH
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('code');
        $this->forge->createTable('wilayah', true);


        /*
        |--------------------------------------------------------------------------
        | 3. CABANG
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'wilayah_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('wilayah_id');
        $this->forge->addForeignKey(
            'wilayah_id',
            'wilayah',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->forge->createTable('cabang', true);


        /*
        |--------------------------------------------------------------------------
        | 4. USERS
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'role_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'wilayah_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'cabang_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'last_login' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'status' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('email');
        $this->forge->addUniqueKey('username');
        $this->forge->addKey('role_id');
        $this->forge->addKey('wilayah_id');
        $this->forge->addKey('cabang_id');

        $this->forge->addForeignKey(
            'role_id',
            'user_roles',
            'id',
            'RESTRICT',
            'CASCADE'
        );
        $this->forge->addForeignKey(
            'wilayah_id',
            'wilayah',
            'id',
            'SET NULL',
            'CASCADE'
        );
        $this->forge->addForeignKey(
            'cabang_id',
            'cabang',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->forge->createTable('users', true);


        /*
        |--------------------------------------------------------------------------
        | 3. PROVINCES
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('name');
        $this->forge->createTable('provinces', true);


        /*
        |--------------------------------------------------------------------------
        | 4. REGENCIES / KABUPATEN
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'province_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('province_id');
        $this->forge->addUniqueKey(['province_id', 'name']);

        $this->forge->addForeignKey(
            'province_id',
            'provinces',
            'id',
            'CASCADE',
            'RESTRICT'
        );

        $this->forge->createTable('regencies', true);


        /*
        |--------------------------------------------------------------------------
        | 5. DISTRICTS / KECAMATAN
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'regency_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('regency_id');
        $this->forge->addUniqueKey(['regency_id', 'name']);

        $this->forge->addForeignKey(
            'regency_id',
            'regencies',
            'id',
            'CASCADE',
            'RESTRICT'
        );

        $this->forge->createTable('districts', true);


        /*
        |--------------------------------------------------------------------------
        | 6. VILLAGES / DESA
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'district_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('district_id');
        $this->forge->addUniqueKey(['district_id', 'name']);

        $this->forge->addForeignKey(
            'district_id',
            'districts',
            'id',
            'CASCADE',
            'RESTRICT'
        );

        $this->forge->createTable('villages', true);


        /*
        |--------------------------------------------------------------------------
        | 7. EDUCATION LEVELS
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'description' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('name');
        $this->forge->createTable('education_levels', true);


        /*
        |--------------------------------------------------------------------------
        | 8. JOB STATUSES
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'description' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('name');
        $this->forge->createTable('job_statuses', true);


        /*
        |--------------------------------------------------------------------------
        | 9. SKILLS / KEAHLIAN
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('name');
        $this->forge->createTable('skills', true);


        /*
        |--------------------------------------------------------------------------
        | 10. INTERESTS / MINAT
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('name');
        $this->forge->createTable('interests', true);


        /*
        |--------------------------------------------------------------------------
        | 11. PEMUDA
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'cabang_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'registration_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'gender' => [
                'type'       => 'ENUM',
                'constraint' => ['L', 'P'],
            ],
            'birth_place' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'birth_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'status_verifikasi' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'verified', 'rejected'],
                'default'    => 'pending',
            ],
            'status_data' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'archived'],
                'default'    => 'active',
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
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('registration_number');
        $this->forge->addKey('cabang_id');
        $this->forge->addKey('name');
        $this->forge->addKey('gender');
        $this->forge->addKey('status_verifikasi');
        $this->forge->addKey('status_data');

        $this->forge->addForeignKey(
            'cabang_id',
            'cabang',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->forge->addForeignKey(
            'created_by',
            'users',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->forge->createTable('pemuda', true);


        /*
        |--------------------------------------------------------------------------
        | 12. ALAMAT
        |--------------------------------------------------------------------------
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
            'province_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'regency_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'district_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'village_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'dusun' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'rt' => [
                'type'       => 'VARCHAR',
                'constraint' => 5,
                'null'       => true,
            ],
            'rw' => [
                'type'       => 'VARCHAR',
                'constraint' => 5,
                'null'       => true,
            ],
            'address_detail' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('pemuda_id');

        $this->forge->addKey('province_id');
        $this->forge->addKey('regency_id');
        $this->forge->addKey('district_id');
        $this->forge->addKey('village_id');

        $this->forge->addForeignKey(
            'pemuda_id',
            'pemuda',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->addForeignKey(
            'province_id',
            'provinces',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->forge->addForeignKey(
            'regency_id',
            'regencies',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->forge->addForeignKey(
            'district_id',
            'districts',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->forge->addForeignKey(
            'village_id',
            'villages',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->forge->createTable('alamat', true);


        /*
        |--------------------------------------------------------------------------
        | 13. PENDIDIKAN
        |--------------------------------------------------------------------------
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
            'education_level_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'school_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'major' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'education_status' => [
                'type'       => 'ENUM',
                'constraint' => ['sedang_sekolah', 'lulus', 'putus_sekolah'],
                'default'    => 'lulus',
            ],
            'graduation_year' => [
                'type'       => 'YEAR',
                'null'       => true,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('pemuda_id');
        $this->forge->addKey('education_level_id');

        $this->forge->addForeignKey(
            'pemuda_id',
            'pemuda',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->addForeignKey(
            'education_level_id',
            'education_levels',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->forge->createTable('pendidikan', true);


        /*
        |--------------------------------------------------------------------------
        | 14. PEKERJAAN
        |--------------------------------------------------------------------------
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
            'job_status_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'job_title' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'company_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'business_field' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('pemuda_id');
        $this->forge->addKey('job_status_id');

        $this->forge->addForeignKey(
            'pemuda_id',
            'pemuda',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->addForeignKey(
            'job_status_id',
            'job_statuses',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->forge->createTable('pekerjaan', true);


        /*
        |--------------------------------------------------------------------------
        | 15. ORGANISASI
        |--------------------------------------------------------------------------
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
            'organization_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'position' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'join_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'end_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('pemuda_id');

        $this->forge->addForeignKey(
            'pemuda_id',
            'pemuda',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('organisasi', true);


        /*
        |--------------------------------------------------------------------------
        | 16. PEMUDA SKILLS
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'pemuda_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'skill_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'level' => [
                'type'       => 'ENUM',
                'constraint' => ['pemula', 'menengah', 'mahir'],
                'default'    => 'pemula',
            ],
        ]);

        $this->forge->addKey(['pemuda_id', 'skill_id'], true);
        $this->forge->addForeignKey(
            'pemuda_id',
            'pemuda',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->addForeignKey(
            'skill_id',
            'skills',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('pemuda_skills', true);


        /*
        |--------------------------------------------------------------------------
        | 17. PEMUDA INTERESTS
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'pemuda_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'interest_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
        ]);

        $this->forge->addKey(['pemuda_id', 'interest_id'], true);

        $this->forge->addForeignKey(
            'pemuda_id',
            'pemuda',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->addForeignKey(
            'interest_id',
            'interests',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('pemuda_interests', true);


        /*
        |--------------------------------------------------------------------------
        | 18. FORMS
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 180,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
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
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->addKey('created_by');

        $this->forge->addForeignKey(
            'created_by',
            'users',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->forge->createTable('forms', true);


        /*
        |--------------------------------------------------------------------------
        | 19. QUESTIONS
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'form_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'question_text' => [
                'type' => 'TEXT',
            ],
            'question_type' => [
                'type'       => 'ENUM',
                'constraint' => [
                    'text',
                    'textarea',
                    'number',
                    'date',
                    'radio',
                    'checkbox',
                    'select',
                    'file',
                ],
            ],
            'is_required' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'options' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'order_no' => [
                'type'       => 'INT',
                'default'    => 0,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('form_id');
        $this->forge->addKey(['form_id', 'order_no']);

        $this->forge->addForeignKey(
            'form_id',
            'forms',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('questions', true);


        /*
        |--------------------------------------------------------------------------
        | 20. RESPONSES
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'form_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'pemuda_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'submitted_at' => [
                'type'    => 'DATETIME',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('form_id');
        $this->forge->addKey('pemuda_id');

        $this->forge->addForeignKey(
            'form_id',
            'forms',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->addForeignKey(
            'pemuda_id',
            'pemuda',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->forge->createTable('responses', true);


        /*
        |--------------------------------------------------------------------------
        | 21. ANSWERS
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'response_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'question_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'answer_text' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('response_id');
        $this->forge->addKey('question_id');

        $this->forge->addUniqueKey([
            'response_id',
            'question_id',
        ]);

        $this->forge->addForeignKey(
            'response_id',
            'responses',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->addForeignKey(
            'question_id',
            'questions',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('answers', true);
    }


    public function down()
    {
        /*
        |--------------------------------------------------------------------------
        | Hapus dari tabel yang paling bergantung terlebih dahulu.
        |--------------------------------------------------------------------------
        */

        $tables = [
            'answers',
            'responses',
            'questions',
            'forms',
            'pemuda_interests',
            'pemuda_skills',
            'organisasi',
            'pekerjaan',
            'pendidikan',
            'alamat',
            'pemuda',
            'interests',
            'skills',
            'job_statuses',
            'education_levels',
            'villages',
            'districts',
            'regencies',
            'provinces',
            'users',
            'cabang',
            'wilayah',
            'user_roles',
        ];

        foreach ($tables as $table) {
            $this->forge->dropTable($table, true);
        }
    }
}
