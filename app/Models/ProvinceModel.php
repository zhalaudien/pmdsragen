<?php

namespace App\Models;

use CodeIgniter\Model;

class ProvinceModel extends Model
{
    protected $table            = 'provinces';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['name'];
}
