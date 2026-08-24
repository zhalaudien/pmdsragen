<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\UserRoleModel;
use App\Models\WilayahModel;
use App\Models\CabangModel;

class Users extends BaseController
{
    protected UserModel $userModel;
    protected UserRoleModel $userRoleModel;
    protected WilayahModel $wilayahModel;
    protected CabangModel $cabangModel;

    public function __construct()
    {
        $this->userModel     = new UserModel();
        $this->userRoleModel = new UserRoleModel();
        $this->wilayahModel  = new WilayahModel();
        $this->cabangModel   = new CabangModel();
    }

    public function index(): string
    {
        $search = $this->request->getGet('search');
        $roleId = $this->request->getGet('role_id');

        $users = $this->userModel->getUsersList([
            'search'  => $search,
            'role_id' => $roleId,
        ]);

        return view('admin/users/index', [
            'title'       => 'Manajemen Pengguna & Admin',
            'users'       => $users,
            'roles'       => $this->userRoleModel->findAll(),
            'wilayahList' => $this->wilayahModel->orderBy('id', 'ASC')->findAll(),
            'cabangList'  => $this->cabangModel->orderBy('name', 'ASC')->findAll(),
            'search'      => $search,
            'selectedRole'=> $roleId,
            'user'        => session()->get(),
        ]);
    }

    public function simpan()
    {
        $rules = [
            'name'     => 'required|min_length[3]|max_length[100]',
            'email'    => 'required|valid_email|max_length[100]|is_unique[users.email]',
            'username' => 'required|alpha_dash|min_length[3]|max_length[50]|is_unique[users.username]',
            'password' => 'required|min_length[6]',
            'role_id'  => 'required|is_natural_no_zero',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $roleId    = (int) $this->request->getPost('role_id');
        $wilayahId = $this->request->getPost('wilayah_id') ? (int) $this->request->getPost('wilayah_id') : null;
        $cabangId  = $this->request->getPost('cabang_id') ? (int) $this->request->getPost('cabang_id') : null;

        // Superadmin scope rule
        if ($roleId === 1) {
            $wilayahId = null;
            $cabangId  = null;
        } elseif ($roleId === 2) {
            $cabangId  = null;
        }

        $this->userModel->insert([
            'name'       => $this->request->getPost('name'),
            'email'      => $this->request->getPost('email'),
            'username'   => strtolower((string) $this->request->getPost('username')),
            'password'   => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'role_id'    => $roleId,
            'wilayah_id' => $wilayahId,
            'cabang_id'  => $cabangId,
            'status'     => (int) ($this->request->getPost('status') ?? 1),
        ]);

        return redirect()->to(base_url('admin/users'))->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        $rules = [
            'name'     => 'required|min_length[3]|max_length[100]',
            'email'    => "required|valid_email|max_length[100]|is_unique[users.email,id,{$id}]",
            'username' => "required|alpha_dash|min_length[3]|max_length[50]|is_unique[users.username,id,{$id}]",
            'role_id'  => 'required|is_natural_no_zero',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $roleId    = (int) $this->request->getPost('role_id');
        $wilayahId = $this->request->getPost('wilayah_id') ? (int) $this->request->getPost('wilayah_id') : null;
        $cabangId  = $this->request->getPost('cabang_id') ? (int) $this->request->getPost('cabang_id') : null;

        if ($roleId === 1) {
            $wilayahId = null;
            $cabangId  = null;
        } elseif ($roleId === 2) {
            $cabangId  = null;
        }

        $data = [
            'name'       => $this->request->getPost('name'),
            'email'      => $this->request->getPost('email'),
            'username'   => strtolower((string) $this->request->getPost('username')),
            'role_id'    => $roleId,
            'wilayah_id' => $wilayahId,
            'cabang_id'  => $cabangId,
            'status'     => (int) ($this->request->getPost('status') ?? 1),
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash((string) $password, PASSWORD_DEFAULT);
        }

        $this->userModel->update($id, $data);

        return redirect()->to(base_url('admin/users'))->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        if ($id === (int) session()->get('user_id')) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $this->userModel->delete($id);
        return redirect()->to(base_url('admin/users'))->with('success', 'Pengguna berhasil dihapus.');
    }
}
