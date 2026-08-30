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
            'role_id'  => 'required|is_natural_no_zero|is_not_unique[user_roles.id]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $roleId    = (int) $this->request->getPost('role_id');
        $wilayahId = $this->request->getPost('wilayah_id') ? (int) $this->request->getPost('wilayah_id') : null;
        $cabangId  = $this->request->getPost('cabang_id') ? (int) $this->request->getPost('cabang_id') : null;

        // Scope validation per role
        if ($roleId === 1) { // superadmin
            $wilayahId = null;
            $cabangId  = null;
        } elseif ($roleId === 2) { // admin_wilayah
            if (empty($wilayahId)) {
                return redirect()->back()->withInput()->with('error', 'Admin Wilayah wajib memilih Wilayah yang dikelola.');
            }
            $targetWilayah = $this->wilayahModel->find($wilayahId);
            if (!$targetWilayah) {
                return redirect()->back()->withInput()->with('error', 'Wilayah yang dipilih tidak valid.');
            }
            $cabangId = null;
        } elseif ($roleId === 3) { // admin_cabang
            if (empty($cabangId)) {
                return redirect()->back()->withInput()->with('error', 'Admin Cabang wajib memilih Cabang yang dikelola.');
            }
            $targetCabang = $this->cabangModel->find($cabangId);
            if (!$targetCabang) {
                return redirect()->back()->withInput()->with('error', 'Cabang yang dipilih tidak valid.');
            }
            $wilayahId = (int) $targetCabang['wilayah_id'];
        }

        $this->userModel->insert([
            'name'       => trim((string) $this->request->getPost('name')),
            'email'      => trim((string) $this->request->getPost('email')),
            'username'   => strtolower(trim((string) $this->request->getPost('username'))),
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
        $targetUser = $this->userModel->find($id);
        if (!$targetUser) {
            return redirect()->to(base_url('admin/users'))->with('error', 'Pengguna tidak ditemukan.');
        }

        $rules = [
            'name'     => 'required|min_length[3]|max_length[100]',
            'email'    => "required|valid_email|max_length[100]|is_unique[users.email,id,{$id}]",
            'username' => "required|alpha_dash|min_length[3]|max_length[50]|is_unique[users.username,id,{$id}]",
            'role_id'  => 'required|is_natural_no_zero|is_not_unique[user_roles.id]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $currentUserId = (int) session()->get('user_id');
        $roleId        = (int) $this->request->getPost('role_id');
        $status        = (int) ($this->request->getPost('status') ?? 1);
        $wilayahId     = $this->request->getPost('wilayah_id') ? (int) $this->request->getPost('wilayah_id') : null;
        $cabangId      = $this->request->getPost('cabang_id') ? (int) $this->request->getPost('cabang_id') : null;

        // Prevent self-lockout: logged in user cannot demote themselves or deactivate their own account
        if ($id === $currentUserId) {
            if ($roleId !== (int) $targetUser['role_id']) {
                return redirect()->back()->withInput()->with('error', 'Anda tidak dapat mengubah role akun yang sedang Anda gunakan.');
            }
            if ($status === 0) {
                return redirect()->back()->withInput()->with('error', 'Anda tidak dapat menonaktifkan akun yang sedang Anda gunakan.');
            }
        }

        // Prevent removing or deactivating the last active superadmin
        if ((int) $targetUser['role_id'] === 1 && ($roleId !== 1 || $status === 0)) {
            $otherSuperadmins = $this->userModel->where('role_id', 1)
                                                ->where('status', 1)
                                                ->where('id !=', $id)
                                                ->countAllResults();
            if ($otherSuperadmins === 0) {
                return redirect()->back()->withInput()->with('error', 'Gagal: Sistem harus memiliki setidaknya satu Super Administrator yang aktif.');
            }
        }

        // Scope validation per role
        if ($roleId === 1) { // superadmin
            $wilayahId = null;
            $cabangId  = null;
        } elseif ($roleId === 2) { // admin_wilayah
            if (empty($wilayahId)) {
                return redirect()->back()->withInput()->with('error', 'Admin Wilayah wajib memilih Wilayah yang dikelola.');
            }
            $targetWilayah = $this->wilayahModel->find($wilayahId);
            if (!$targetWilayah) {
                return redirect()->back()->withInput()->with('error', 'Wilayah yang dipilih tidak valid.');
            }
            $cabangId = null;
        } elseif ($roleId === 3) { // admin_cabang
            if (empty($cabangId)) {
                return redirect()->back()->withInput()->with('error', 'Admin Cabang wajib memilih Cabang yang dikelola.');
            }
            $targetCabang = $this->cabangModel->find($cabangId);
            if (!$targetCabang) {
                return redirect()->back()->withInput()->with('error', 'Cabang yang dipilih tidak valid.');
            }
            $wilayahId = (int) $targetCabang['wilayah_id'];
        }

        $data = [
            'name'       => trim((string) $this->request->getPost('name')),
            'email'      => trim((string) $this->request->getPost('email')),
            'username'   => strtolower(trim((string) $this->request->getPost('username'))),
            'role_id'    => $roleId,
            'wilayah_id' => $wilayahId,
            'cabang_id'  => $cabangId,
            'status'     => $status,
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
        $currentUserId = (int) session()->get('user_id');
        if ($id === $currentUserId) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $targetUser = $this->userModel->find($id);
        if (!$targetUser) {
            return redirect()->back()->with('error', 'Pengguna tidak ditemukan.');
        }

        // Prevent deleting the last active superadmin
        if ((int) $targetUser['role_id'] === 1) {
            $otherSuperadmins = $this->userModel->where('role_id', 1)
                                                ->where('status', 1)
                                                ->where('id !=', $id)
                                                ->countAllResults();
            if ($otherSuperadmins === 0) {
                return redirect()->back()->with('error', 'Gagal: Anda tidak dapat menghapus satu-satunya Super Administrator yang tersisa di sistem.');
            }
        }

        $this->userModel->delete($id);
        return redirect()->to(base_url('admin/users'))->with('success', 'Pengguna berhasil dihapus.');
    }
}
