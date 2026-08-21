<?php

namespace App\Models;

use CodeIgniter\Model;

class PemudaSkillModel extends Model
{
    protected $table            = 'pemuda_skills';
    protected $primaryKey       = 'pemuda_id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pemuda_id',
        'skill_id',
        'level',
    ];
}
