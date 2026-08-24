<?php

namespace App\Models;

use CodeIgniter\Model;

class RegencyModel extends Model
{
    protected $table            = 'regencies';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['province_id', 'name'];
}
