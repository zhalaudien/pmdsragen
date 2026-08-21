<?php

namespace App\Models;

use CodeIgniter\Model;

class InterestModel extends Model
{
    protected $table            = 'interests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'description',
    ];
}
