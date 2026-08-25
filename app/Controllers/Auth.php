<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Halaman Login & Proses Autentikasi
     */
    public function login()
    {
        // Jika sudah login, langsung arahkan ke dashboard
        if (session()->get('is_logged_in')) {
            return redirect()->to(base_url('admin/dashboard'));
        }

        if ($this->request->is('post')) {
            $rules = [
                'login'    => 'required|min_length[3]',
                'password' => 'required|min_length[5]',
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()
                                 ->withInput()
                                 ->with('errors', $this->validator->getErrors());
            }

            $loginInput = trim((string) $this->request->getPost('login'));
            $password   = (string) $this->request->getPost('password');

            $user = $this->userModel->findByUsernameOrEmail($loginInput);

            if (!$user || !password_verify($password, $user['password'])) {
                return redirect()->back()
                                 ->withInput()
                                 ->with('error', 'Username / Email atau Password tidak sesuai.');
            }

            if ((int) $user['status'] !== 1) {
                return redirect()->back()
                                 ->withInput()
                                 ->with('error', 'Akun Anda dinonaktifkan. Silakan hubungi Administrator.');
            }

            // Regenerate session ID for security
            session()->regenerate();

            $wilayahId   = $user['wilayah_id'] ? (int) $user['wilayah_id'] : null;
            $wilayahName = $user['wilayah_name'] ?? null;
            $cabangId    = $user['cabang_id'] ? (int) $user['cabang_id'] : null;
            $cabangName  = $user['cabang_name'] ?? null;

            // If user is admin_cabang and wilayah is not directly linked in users table, lookup from cabang
            if ($user['role_name'] === 'admin_cabang' && $cabangId && !$wilayahId) {
                $cabangModel = new \App\Models\CabangModel();
                $cabangData  = $cabangModel->select('cabang.*, wilayah.name as wilayah_name')
                                          ->join('wilayah', 'wilayah.id = cabang.wilayah_id', 'left')
                                          ->find($cabangId);
                if ($cabangData) {
                    $wilayahId   = (int) $cabangData['wilayah_id'];
                    $wilayahName = $cabangData['wilayah_name'];
                }
            }

            // Set session data
            $sessionData = [
                'user_id'          => (int) $user['id'],
                'name'             => $user['name'],
                'email'            => $user['email'],
                'username'         => $user['username'],
                'role'             => $user['role_name'],
                'role_id'          => (int) $user['role_id'],
                'role_description' => $user['role_description'] ?? '',
                'wilayah_id'       => $wilayahId,
                'wilayah_name'     => $wilayahName,
                'cabang_id'        => $cabangId,
                'cabang_name'      => $cabangName,
                'is_logged_in'     => true,
            ];

            session()->set($sessionData);

            // Update last login
            $this->userModel->update($user['id'], [
                'last_login' => date('Y-m-d H:i:s'),
            ]);

            $redirectUrl = session()->getFlashdata('redirect_url') ?: base_url('admin/dashboard');

            return redirect()->to($redirectUrl)
                             ->with('success', 'Selamat datang kembali, ' . esc($user['name']) . '!');
        }

        return view('auth/login');
    }

    /**
     * Proses Logout
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('admin/login'))
                         ->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}
