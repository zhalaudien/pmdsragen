<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call('UserRoleSeeder');
        $this->call('EducationLevelSeeder');
        $this->call('JobStatusSeeder');
        $this->call('SkillSeeder');
        $this->call('InterestSeeder');
    }
}
