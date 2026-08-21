<?php

namespace App\Models;

use CodeIgniter\Model;

class JobStatusModel extends Model
{
    protected $table            = 'job_statuses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'description',
    ];
}
