<?php

namespace App\Models;

use CodeIgniter\Model;

class PemudaModel extends Model
{
    protected $table            = 'pemuda';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'cabang_id',
        'registration_number',
        'name',
        'gender',
        'birth_place',
        'birth_date',
        'phone',
        'email',
        'status_verifikasi',
        'status_data',
        'created_by',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation Rules
    protected $validationRules = [
        'cabang_id' => 'required|is_natural_no_zero',
        'name'      => 'required|min_length[3]|max_length[150]',
        'gender'    => 'required|in_list[L,P]',
        'phone'     => 'permit_empty|max_length[20]',
        'email'     => 'permit_empty|valid_email|max_length[100]',
    ];

    protected $validationMessages = [
        'cabang_id' => [
            'required' => 'Cabang wajib dipilih.',
        ],
        'name' => [
            'required' => 'Nama pemuda wajib diisi.',
            'min_length' => 'Nama minimal 3 karakter.',
        ],
        'gender' => [
            'required' => 'Jenis kelamin wajib dipilih.',
            'in_list'  => 'Jenis kelamin harus L atau P.',
        ],
    ];

    /**
     * Generate unique registration number
     * Format: PMD-YYYYMMDD-XXXX
     */
    public function generateRegistrationNumber(): string
    {
        $prefix = 'PMD-' . date('Ymd') . '-';
        $latest = $this->like('registration_number', $prefix, 'after')
                       ->orderBy('id', 'DESC')
                       ->first();

        if ($latest) {
            $lastNumber = (int) substr($latest['registration_number'], -4);
            $newNumber  = str_pad((string) ($lastNumber + 1), 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }
}
