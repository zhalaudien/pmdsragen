<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->db->disableForeignKeyChecks();

        $this->call('UserRoleSeeder');
        $this->call('RegionalSeeder');
        $this->call('WilayahSeeder');
        $this->call('CabangSeeder');
        $this->call('UserSeeder');
        $this->call('EducationLevelSeeder');
        $this->call('JobStatusSeeder');
        $this->call('SkillSeeder');
        $this->call('InterestSeeder');
        $this->call('HomepageSettingSeeder');

        $this->db->enableForeignKeyChecks();
    }
}
