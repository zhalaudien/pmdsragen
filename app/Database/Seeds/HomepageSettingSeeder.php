<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\HomepageSettingModel;

class HomepageSettingSeeder extends Seeder
{
    public function run()
    {
        $defaults = HomepageSettingModel::getDefaults();
        $builder  = $this->db->table('homepage_settings');

        foreach ($defaults as $key => $item) {
            $existing = $builder->where('key', $key)->get()->getRow();
            if ($existing) {
                $builder->where('id', $existing->id)->update([
                    'value'      => $item['value'],
                    'group'      => $item['group'],
                    'type'       => $item['type'],
                    'label'      => $item['label'],
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
                $builder->insert([
                    'group'      => $item['group'],
                    'key'        => $key,
                    'value'      => $item['value'],
                    'type'       => $item['type'],
                    'label'      => $item['label'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }
}
