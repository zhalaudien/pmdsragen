<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'email',
        'username',
        'password',
        'role_id',
        'wilayah_id',
        'cabang_id',
        'last_login',
        'status',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name'     => 'required|min_length[3]|max_length[100]',
        'email'    => 'required|valid_email|max_length[100]|is_unique[users.email,id,{id}]',
        'username' => 'required|alpha_dash|min_length[3]|max_length[50]|is_unique[users.username,id,{id}]',
        'role_id'  => 'required|is_natural_no_zero',
    ];

    /**
     * Cari user berdasarkan username atau email beserta rolenya
     */
    public function findByUsernameOrEmail(string $login): ?array
    {
        return $this->select('users.*, user_roles.name as role_name, user_roles.description as role_description, wilayah.name as wilayah_name, cabang.name as cabang_name')
                    ->join('user_roles', 'user_roles.id = users.role_id')
                    ->join('wilayah', 'wilayah.id = users.wilayah_id', 'left')
                    ->join('cabang', 'cabang.id = users.cabang_id', 'left')
                    ->groupStart()
                        ->where('users.username', $login)
                        ->orWhere('users.email', $login)
                    ->groupEnd()
                    ->first();
    }

    /**
     * Ambil data user lengkap dengan relasi
     */
    public function getUserWithRelations(int $id): ?array
    {
        return $this->select('users.*, user_roles.name as role_name, user_roles.description as role_description, wilayah.name as wilayah_name, cabang.name as cabang_name')
                    ->join('user_roles', 'user_roles.id = users.role_id')
                    ->join('wilayah', 'wilayah.id = users.wilayah_id', 'left')
                    ->join('cabang', 'cabang.id = users.cabang_id', 'left')
                    ->where('users.id', $id)
                    ->first();
    }

    /**
     * Ambil seluruh user dengan data role dan wilayah
     */
    public function getUsersList(array $filters = []): array
    {
        $builder = $this->select('users.*, user_roles.name as role_name, wilayah.name as wilayah_name, cabang.name as cabang_name')
                        ->join('user_roles', 'user_roles.id = users.role_id')
                        ->join('wilayah', 'wilayah.id = users.wilayah_id', 'left')
                        ->join('cabang', 'cabang.id = users.cabang_id', 'left');

        if (!empty($filters['role_id'])) {
            $builder->where('users.role_id', $filters['role_id']);
        }

        if (!empty($filters['status']) !== '') {
            if (isset($filters['status']) && $filters['status'] !== '') {
                $builder->where('users.status', $filters['status']);
            }
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                    ->like('users.name', $filters['search'])
                    ->orLike('users.username', $filters['search'])
                    ->orLike('users.email', $filters['search'])
                    ->groupEnd();
        }

        return $builder->orderBy('users.id', 'ASC')->findAll();
    }
}
