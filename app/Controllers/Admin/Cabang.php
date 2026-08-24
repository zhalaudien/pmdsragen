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
        $this->cabangModel  = new CabangModel();
        $this->wilayahModel = new WilayahModel();
    }

    public function index(): string
    {
        $wilayahId = $this->request->getGet('wilayah_id');
        $search    = $this->request->getGet('search');

        $builder = $this->cabangModel->select('cabang.*, wilayah.name as wilayah_name, wilayah.code as wilayah_code')
                                     ->join('wilayah', 'wilayah.id = cabang.wilayah_id');

        if (!empty($wilayahId)) {
            $builder->where('cabang.wilayah_id', (int) $wilayahId);
        }

        if (!empty($search)) {
            $builder->groupStart()
                    ->like('cabang.name', $search)
                    ->orLike('cabang.code', $search)
                    ->groupEnd();
        }

        $cabangList = $builder->orderBy('cabang.wilayah_id', 'ASC')
                              ->orderBy('cabang.name', 'ASC')
                              ->paginate(20, 'cabang');

        $db = \Config\Database::connect();
        foreach ($cabangList as &$c) {
            $c['total_pemuda'] = $db->table('pemuda')->where('cabang_id', $c['id'])->countAllResults();
        }

        return view('admin/cabang/index', [
            'title'       => 'Manajemen Cabang',
            'cabangList'  => $cabangList,
            'pager'       => $this->cabangModel->pager,
            'wilayahList' => $this->wilayahModel->orderBy('id', 'ASC')->findAll(),
            'selectedW'   => $wilayahId,
            'search'      => $search,
            'user'        => session()->get(),
        ]);
    }

    public function simpan()
    {
        $rules = [
            'wilayah_id' => 'required|is_natural_no_zero',
            'name'       => 'required|min_length[3]|max_length[100]',
            'code'       => 'permit_empty|max_length[50]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->cabangModel->insert([
            'wilayah_id'  => (int) $this->request->getPost('wilayah_id'),
            'code'        => $this->request->getPost('code') ?: null,
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ]);

        return redirect()->to(base_url('admin/cabang'))->with('success', 'Cabang berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        $rules = [
            'wilayah_id' => 'required|is_natural_no_zero',
            'name'       => 'required|min_length[3]|max_length[100]',
            'code'       => 'permit_empty|max_length[50]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->cabangModel->update($id, [
            'wilayah_id'  => (int) $this->request->getPost('wilayah_id'),
            'code'        => $this->request->getPost('code') ?: null,
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
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

        $this->cabangModel->delete($id);
        return redirect()->to(base_url('admin/cabang'))->with('success', 'Cabang berhasil dihapus.');
    }
}
