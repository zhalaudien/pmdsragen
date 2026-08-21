<?php

namespace App\Models;

use CodeIgniter\Model;

class PemudaInterestModel extends Model
{
    protected $table            = 'pemuda_interests';
    protected $primaryKey       = 'pemuda_id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pemuda_id',
        'interest_id',
    ];
}
