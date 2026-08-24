<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\WilayahModel;
use App\Models\CabangModel;

class Wilayah extends BaseController
{
    protected WilayahModel $wilayahModel;
    protected CabangModel $cabangModel;

    public function __construct()
    {
        $this->wilayahModel = new WilayahModel();
        $this->cabangModel  = new CabangModel();
    }

    public function index(): string
    {
        $wilayahList = $this->wilayahModel->orderBy('id', 'ASC')->findAll();
        
        // Count branches and youth per wilayah
        $db = \Config\Database::connect();
        foreach ($wilayahList as &$w) {
            $w['total_cabang'] = $db->table('cabang')->where('wilayah_id', $w['id'])->countAllResults();
            $w['total_pemuda'] = $db->table('pemuda')
                                    ->join('cabang', 'cabang.id = pemuda.cabang_id')
                                    ->where('cabang.wilayah_id', $w['id'])
                                    ->countAllResults();
        }

        return view('admin/wilayah/index', [
            'title'       => 'Manajemen Wilayah',
            'wilayahList' => $wilayahList,
            'user'        => session()->get(),
        ]);
    }

    public function simpan()
    {
        $rules = [
            'code' => 'required|min_length[2]|max_length[50]|is_unique[wilayah.code]',
            'name' => 'required|min_length[3]|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->wilayahModel->insert([
            'code'        => strtoupper((string) $this->request->getPost('code')),
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ]);

        return redirect()->to(base_url('admin/wilayah'))->with('success', 'Wilayah berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        $rules = [
            'code' => "required|min_length[2]|max_length[50]|is_unique[wilayah.code,id,{$id}]",
            'name' => 'required|min_length[3]|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->wilayahModel->update($id, [
            'code'        => strtoupper((string) $this->request->getPost('code')),
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ]);

        return redirect()->to(base_url('admin/wilayah'))->with('success', 'Wilayah berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $cabangCount = $this->cabangModel->where('wilayah_id', $id)->countAllResults();
        if ($cabangCount > 0) {
            return redirect()->back()->with('error', "Tidak dapat menghapus wilayah ini karena masih memiliki {$cabangCount} cabang aktif.");
        }

        $this->wilayahModel->delete($id);
        return redirect()->to(base_url('admin/wilayah'))->with('success', 'Wilayah berhasil dihapus.');
    }
}
