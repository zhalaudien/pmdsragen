<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (!$session->get('is_logged_in')) {
            return redirect()->to(base_url('admin/login'))
                             ->with('error', 'Silakan login terlebih dahulu.');
        }

        $userRole = $session->get('role');

        if (!empty($arguments)) {
            // Superadmin has universal access to all admin routes unless explicitly excluded
            if ($userRole === 'superadmin') {
                return;
            }

            if (!in_array($userRole, $arguments, true)) {
                return redirect()->to(base_url('admin/dashboard'))
                                 ->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman tersebut.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No post-processing needed
    }
}
