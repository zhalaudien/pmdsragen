<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\HomepageSettingModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class HomepageSetting extends BaseController
{
    protected HomepageSettingModel $settingModel;

    public function __construct()
    {
        $this->settingModel = new HomepageSettingModel();
    }

    /**
     * Memastikan akses hanya untuk Superadmin
     */
    protected function ensureSuperadmin(): void
    {
        if (session()->get('role') !== 'superadmin') {
            throw PageNotFoundException::forPageNotFound('Halaman ini hanya dapat diakses oleh Superadmin.');
        }
    }

    /**
     * Halaman Portal Pengaturan Konten Homepage
     */
    public function index(): string
    {
        $this->ensureSuperadmin();

        $settings = $this->settingModel->getAllSettings();

        // Decode JSON fields untuk kemudahan rendering di form
        $heroChips = json_decode($settings['hero_chips'] ?? '[]', true) ?: [];
        $misiList  = json_decode($settings['misi_list'] ?? '[]', true) ?: [];
        $programs  = json_decode($settings['program_list'] ?? '[]', true) ?: [];
        $alurSteps = json_decode($settings['alur_steps'] ?? '[]', true) ?: [];
        $faqs      = json_decode($settings['faq_list'] ?? '[]', true) ?: [];

        $data = [
            'title'     => 'Kelola Konten Beranda (Homepage)',
            'settings'  => $settings,
            'heroChips' => $heroChips,
            'misiList'  => $misiList,
            'programs'  => $programs,
            'alurSteps' => $alurSteps,
            'faqs'      => $faqs,
            'activeTab' => session()->getFlashdata('active_tab') ?: 'hero',
        ];

        return view('admin/homepage/index', $data);
    }

    /**
     * Proses Simpan / Update Konten Homepage
     */
    public function update()
    {
        $this->ensureSuperadmin();

        $tab = $this->request->getPost('active_tab') ?: 'hero';

        // 1. Ambil field-field teks/textarea standar
        $fields = [
            // Hero
            'hero_badge',
            'hero_title',
            'hero_subtitle',
            'hero_btn_text',
            'hero_card_title',
            'hero_card_desc',
            'hero_card_features',
            // Stats
            'stats_bidang_num',
            'stats_bidang_label',
            'stats_bidang_desc',
            // Tentang & Visi
            'tentang_tag',
            'tentang_title',
            'tentang_desc_1',
            'tentang_desc_2',
            'visi_text',
            // Wilayah
            'wilayah_tag',
            'wilayah_title',
            'wilayah_desc',
            // Program
            'program_tag',
            'program_title',
            'program_desc',
            // Alur & CTA
            'alur_tag',
            'alur_title',
            'alur_desc',
            'cta_title',
            'cta_desc',
            'cta_btn_text',
            // FAQ
            'faq_tag',
            'faq_title',
            'faq_desc',
            // Kontak
            'kontak_tag',
            'kontak_title',
            'kontak_desc',
            'alamat_kantor',
            'whatsapp_number',
            'whatsapp_label',
        ];

        foreach ($fields as $field) {
            $val = $this->request->getPost($field);
            if ($val !== null) {
                $this->settingModel->setSetting($field, trim((string) $val));
            }
        }

        // 2. Simpan Hero Chips jika ada
        $chipsPost = $this->request->getPost('hero_chips');
        if (is_array($chipsPost)) {
            $chips = [];
            foreach ($chipsPost as $item) {
                if (!empty($item['text'])) {
                    $chips[] = [
                        'icon' => trim((string) ($item['icon'] ?? 'bi-check-circle')),
                        'text' => trim((string) $item['text']),
                    ];
                }
            }
            $this->settingModel->setSetting('hero_chips', json_encode($chips, JSON_UNESCAPED_UNICODE), 'hero', 'json', 'Highlight Chips');
        }

        // 3. Simpan Misi List jika ada
        $misiPost = $this->request->getPost('misi_list');
        if (is_array($misiPost)) {
            $misi = [];
            $no   = 1;
            foreach ($misiPost as $item) {
                if (!empty($item['title'])) {
                    $misi[] = [
                        'number' => $no++,
                        'title'  => trim((string) $item['title']),
                        'desc'   => trim((string) ($item['desc'] ?? '')),
                    ];
                }
            }
            $this->settingModel->setSetting('misi_list', json_encode($misi, JSON_UNESCAPED_UNICODE), 'tentang', 'json', 'Daftar Misi');
        }

        // 4. Simpan Program List jika ada
        $progPost = $this->request->getPost('program_list');
        if (is_array($progPost)) {
            $programs = [];
            foreach ($progPost as $item) {
                if (!empty($item['title'])) {
                    $programs[] = [
                        'icon'  => trim((string) ($item['icon'] ?? 'bi-grid')),
                        'color' => trim((string) ($item['color'] ?? 'primary')),
                        'title' => trim((string) $item['title']),
                        'desc'  => trim((string) ($item['desc'] ?? '')),
                        'badge' => trim((string) ($item['badge'] ?? '')),
                    ];
                }
            }
            $this->settingModel->setSetting('program_list', json_encode($programs, JSON_UNESCAPED_UNICODE), 'program', 'json', 'Daftar Program Kerja');
        }

        // 5. Simpan Alur Steps jika ada
        $alurPost = $this->request->getPost('alur_steps');
        if (is_array($alurPost)) {
            $steps = [];
            $no    = 1;
            foreach ($alurPost as $item) {
                if (!empty($item['title'])) {
                    $steps[] = [
                        'step'  => $no++,
                        'title' => trim((string) $item['title']),
                        'desc'  => trim((string) ($item['desc'] ?? '')),
                    ];
                }
            }
            $this->settingModel->setSetting('alur_steps', json_encode($steps, JSON_UNESCAPED_UNICODE), 'alur', 'json', 'Daftar 4 Tahapan Alur');
        }

        // 6. Simpan FAQ List jika ada
        $faqPost = $this->request->getPost('faq_list');
        if (is_array($faqPost)) {
            $faqs = [];
            foreach ($faqPost as $item) {
                if (!empty($item['q'])) {
                    $faqs[] = [
                        'q' => trim((string) $item['q']),
                        'a' => trim((string) ($item['a'] ?? '')),
                    ];
                }
            }
            $this->settingModel->setSetting('faq_list', json_encode($faqs, JSON_UNESCAPED_UNICODE), 'faq', 'json', 'Daftar Tanya Jawab FAQ');
        }

        return redirect()->to('admin/homepage')
            ->with('success', 'Konten beranda (homepage) berhasil diperbarui dan telah diterapkan ke web depan!')
            ->with('active_tab', $tab);
    }

    /**
     * Kembalikan semua konten homepage ke default bawaan
     */
    public function reset()
    {
        $this->ensureSuperadmin();

        $this->settingModel->resetToDefaults();

        return redirect()->to('admin/homepage')
            ->with('success', 'Seluruh konten beranda berhasil dikembalikan ke format default sistem.');
    }
}
