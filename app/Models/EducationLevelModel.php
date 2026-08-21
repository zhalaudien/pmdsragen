<?php

namespace App\Models;

use CodeIgniter\Model;

class EducationLevelModel extends Model
{
    protected $table            = 'education_levels';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'description',
    ];
}
