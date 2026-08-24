<?php

namespace App\Models;

use CodeIgniter\Model;

class VillageModel extends Model
{
    protected $table            = 'villages';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['district_id', 'name'];
}
