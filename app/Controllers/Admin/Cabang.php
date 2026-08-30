<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CabangModel;
use App\Models\WilayahModel;

class Cabang extends BaseController
{
    protected CabangModel $cabangModel;
    protected WilayahModel $wilayahModel;

    public function __construct()
    {
        helper(['text', 'form', 'url']);
        $this->cabangModel  = new CabangModel();
        $this->wilayahModel = new WilayahModel();
    }

    public function index(): string
    {
        $wilayahId    = $this->request->getGet('wilayah_id');
        $hasGelombang = $this->request->getGet('has_gelombang');
        $search       = $this->request->getGet('search');

        $builder = $this->cabangModel->select('cabang.*, wilayah.name as wilayah_name, wilayah.code as wilayah_code')
                                     ->join('wilayah', 'wilayah.id = cabang.wilayah_id');

        if (!empty($wilayahId)) {
            $builder->where('cabang.wilayah_id', (int) $wilayahId);
        }

        if (!empty($hasGelombang) && in_array($hasGelombang, ['sudah', 'belum'], true)) {
            $builder->where('cabang.has_gelombang', $hasGelombang);
        }

        if (!empty($search)) {
            $builder->groupStart()
                    ->like('cabang.name', $search)
                    ->orLike('cabang.code', $search)
                    ->orLike('cabang.pimpinan_nama', $search)
                    ->orLike('cabang.alamat', $search)
                    ->orLike('cabang.gelombang_ustadz', $search)
                    ->groupEnd();
        }

        $cabangList = $builder->orderBy('cabang.wilayah_id', 'ASC')
                              ->orderBy('cabang.name', 'ASC')
                              ->paginate(20, 'cabang');

        $db = \Config\Database::connect();
        foreach ($cabangList as &$c) {
            $c['total_pemuda'] = $db->table('pemuda')->where('cabang_id', $c['id'])->countAllResults();
        }

        // Statistik ringkas
        $totalCabang         = $this->cabangModel->countAllResults();
        $totalSudahGelombang = $this->cabangModel->where('has_gelombang', 'sudah')->countAllResults();
        $totalBelumGelombang = $this->cabangModel->where('has_gelombang', 'belum')->countAllResults();

        return view('admin/cabang/index', [
            'title'               => 'Manajemen Cabang',
            'cabangList'          => $cabangList,
            'pager'               => $this->cabangModel->pager,
            'wilayahList'         => $this->wilayahModel->orderBy('id', 'ASC')->findAll(),
            'selectedW'           => $wilayahId,
            'selectedGelombang'   => $hasGelombang,
            'search'              => $search,
            'totalCabang'         => $totalCabang,
            'totalSudahGelombang' => $totalSudahGelombang,
            'totalBelumGelombang' => $totalBelumGelombang,
            'user'                => session()->get(),
        ]);
    }

    public function detail(int $id)
    {
        $cabang = $this->cabangModel->select('cabang.*, wilayah.name as wilayah_name, wilayah.code as wilayah_code')
                                    ->join('wilayah', 'wilayah.id = cabang.wilayah_id')
                                    ->find($id);

        if (!$cabang) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Data cabang tidak ditemukan.',
            ]);
        }

        $db = \Config\Database::connect();
        $cabang['total_pemuda'] = $db->table('pemuda')->where('cabang_id', $id)->countAllResults();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $cabang,
        ]);
    }

    public function simpan()
    {
        $rules = [
            'wilayah_id'       => 'required|is_natural_no_zero',
            'name'             => 'required|min_length[3]|max_length[100]',
            'code'             => 'permit_empty|max_length[50]',
            'alamat'           => 'permit_empty',
            'pimpinan_nama'    => 'permit_empty|max_length[100]',
            'no_wa'            => 'permit_empty|max_length[20]',
            'has_gelombang'    => 'required|in_list[sudah,belum]',
            'gelombang_hari'   => 'permit_empty|max_length[100]',
            'gelombang_jam'    => 'permit_empty|max_length[50]',
            'gelombang_ustadz' => 'permit_empty|max_length[150]',
            'description'      => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $hasGelombang = $this->request->getPost('has_gelombang') === 'sudah' ? 'sudah' : 'belum';

        $this->cabangModel->insert([
            'wilayah_id'       => (int) $this->request->getPost('wilayah_id'),
            'code'             => $this->request->getPost('code') ?: null,
            'name'             => trim($this->request->getPost('name')),
            'alamat'           => $this->request->getPost('alamat') ?: null,
            'pimpinan_nama'    => $this->request->getPost('pimpinan_nama') ?: null,
            'no_wa'            => $this->request->getPost('no_wa') ?: null,
            'has_gelombang'    => $hasGelombang,
            'gelombang_hari'   => $hasGelombang === 'sudah' ? ($this->request->getPost('gelombang_hari') ?: null) : null,
            'gelombang_jam'    => $hasGelombang === 'sudah' ? ($this->request->getPost('gelombang_jam') ?: null) : null,
            'gelombang_ustadz' => $hasGelombang === 'sudah' ? ($this->request->getPost('gelombang_ustadz') ?: null) : null,
            'description'      => $this->request->getPost('description') ?: null,
        ]);

        return redirect()->to(base_url('admin/cabang'))->with('success', 'Cabang berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        $rules = [
            'wilayah_id'       => 'required|is_natural_no_zero',
            'name'             => 'required|min_length[3]|max_length[100]',
            'code'             => 'permit_empty|max_length[50]',
            'alamat'           => 'permit_empty',
            'pimpinan_nama'    => 'permit_empty|max_length[100]',
            'no_wa'            => 'permit_empty|max_length[20]',
            'has_gelombang'    => 'required|in_list[sudah,belum]',
            'gelombang_hari'   => 'permit_empty|max_length[100]',
            'gelombang_jam'    => 'permit_empty|max_length[50]',
            'gelombang_ustadz' => 'permit_empty|max_length[150]',
            'description'      => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $hasGelombang = $this->request->getPost('has_gelombang') === 'sudah' ? 'sudah' : 'belum';

        $this->cabangModel->update($id, [
            'wilayah_id'       => (int) $this->request->getPost('wilayah_id'),
            'code'             => $this->request->getPost('code') ?: null,
            'name'             => trim($this->request->getPost('name')),
            'alamat'           => $this->request->getPost('alamat') ?: null,
            'pimpinan_nama'    => $this->request->getPost('pimpinan_nama') ?: null,
            'no_wa'            => $this->request->getPost('no_wa') ?: null,
            'has_gelombang'    => $hasGelombang,
            'gelombang_hari'   => $hasGelombang === 'sudah' ? ($this->request->getPost('gelombang_hari') ?: null) : null,
            'gelombang_jam'    => $hasGelombang === 'sudah' ? ($this->request->getPost('gelombang_jam') ?: null) : null,
            'gelombang_ustadz' => $hasGelombang === 'sudah' ? ($this->request->getPost('gelombang_ustadz') ?: null) : null,
            'description'      => $this->request->getPost('description') ?: null,
        ]);

        return redirect()->to(base_url('admin/cabang'))->with('success', 'Cabang berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $db = \Config\Database::connect();
        $pemudaCount = $db->table('pemuda')->where('cabang_id', $id)->countAllResults();
        if ($pemudaCount > 0) {
            return redirect()->back()->with('error', "Tidak dapat menghapus cabang ini karena masih terdapat {$pemudaCount} data pemuda terdaftar.");
        }

        $userCount = $db->table('users')->where('cabang_id', $id)->countAllResults();
        if ($userCount > 0) {
            return redirect()->back()->with('error', "Tidak dapat menghapus cabang ini karena masih terdapat {$userCount} akun admin cabang yang terhubung.");
        }

        $this->cabangModel->delete($id);
        return redirect()->to(base_url('admin/cabang'))->with('success', 'Cabang berhasil dihapus.');
    }
}
