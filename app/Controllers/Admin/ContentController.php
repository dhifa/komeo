<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\HomepageBenefitModel;
use App\Services\SettingsService;
use CodeIgniter\HTTP\RedirectResponse;

class ContentController extends BaseController
{
    /**
     * Controlled list of icons available for benefits
     */
    public const AVAILABLE_ICONS = [
        'users'       => 'Pengguna / Komunitas (Users)',
        'briefcase'   => 'Portofolio / Bisnis (Briefcase)',
        'badge-check' => 'Lencana Terverifikasi (Badge Check)',
        'sparkles'    => 'Kreativitas / Bintang (Sparkles)',
        'globe'       => 'Jejaring Lintas Kota (Globe)',
        'camera'      => 'Dokumentasi / Multimedia (Camera)',
        'music'       => 'Pentas & Hiburan (Music)',
        'star'        => 'Kualitas Terbaik (Star)',
        'shield'      => 'Aman & Terpercaya (Shield)',
        'zap'         => 'Peluang Cepat (Lightning)',
    ];

    /**
     * Display Homepage Content Management view
     */
    public function homepage(): string|RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $benefitModel = model(HomepageBenefitModel::class);
        $benefits = $benefitModel->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC')->findAll();

        $settings = SettingsService::getAll();

        return view('admin/content/homepage', [
            'title'          => 'Manajemen Konten Beranda - KOMEO.ID',
            'settings'       => $settings,
            'benefits'       => $benefits,
            'availableIcons' => self::AVAILABLE_ICONS,
        ]);
    }

    /**
     * Update homepage sections (Hero, About, Stats, Visibility)
     */
    public function updateHomepage(): RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'hero_headline'            => 'required|max_length[100]',
            'hero_headline_highlight'  => 'required|max_length[100]',
            'hero_description'         => 'required|max_length[500]',
            'hero_btn_primary_label'   => 'required|max_length[50]',
            'hero_btn_primary_url'     => 'required|max_length[100]',
            'hero_btn_secondary_label' => 'permit_empty|max_length[50]',
            'hero_btn_secondary_url'   => 'permit_empty|max_length[100]',
            'about_title'              => 'required|max_length[150]',
            'about_description'        => 'required',
            'hero_background_image'    => 'permit_empty|uploaded[hero_background_image]|max_size[hero_background_image,3072]|ext_in[hero_background_image,png,jpg,jpeg,webp]|mime_in[hero_background_image,image/png,image/jpeg,image/webp]',
            'about_image'              => 'permit_empty|uploaded[about_image]|max_size[about_image,3072]|ext_in[about_image,png,jpg,jpeg,webp]|mime_in[about_image,image/png,image/jpeg,image/webp]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('admin/content/homepage')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $uploadTargetDir = FCPATH . 'uploads/settings/';
        if (! is_dir($uploadTargetDir)) {
            mkdir($uploadTargetDir, 0755, true);
        }

        $updates = [
            // Hero
            'Homepage.hero_badge'               => trim((string) $this->request->getPost('hero_badge')),
            'Homepage.hero_headline'            => trim((string) $this->request->getPost('hero_headline')),
            'Homepage.hero_headline_highlight'  => trim((string) $this->request->getPost('hero_headline_highlight')),
            'Homepage.hero_description'         => trim((string) $this->request->getPost('hero_description')),
            'Homepage.hero_btn_primary_label'   => trim((string) $this->request->getPost('hero_btn_primary_label')),
            'Homepage.hero_btn_primary_url'     => trim((string) $this->request->getPost('hero_btn_primary_url')),
            'Homepage.hero_btn_secondary_label' => trim((string) $this->request->getPost('hero_btn_secondary_label')),
            'Homepage.hero_btn_secondary_url'   => trim((string) $this->request->getPost('hero_btn_secondary_url')),

            // About
            'Homepage.about_badge'              => trim((string) $this->request->getPost('about_badge')),
            'Homepage.about_title'              => trim((string) $this->request->getPost('about_title')),
            'Homepage.about_subtitle'           => trim((string) $this->request->getPost('about_subtitle')),
            'Homepage.about_description'        => trim((string) $this->request->getPost('about_description')),

            // Benefits Title
            'Homepage.benefits_badge'           => trim((string) $this->request->getPost('benefits_badge')),
            'Homepage.benefits_title'           => trim((string) $this->request->getPost('benefits_title')),
            'Homepage.benefits_description'     => trim((string) $this->request->getPost('benefits_description')),

            // Statistics Settings
            'Homepage.stats_use_real_members'      => $this->request->getPost('stats_use_real_members') ? '1' : '0',
            'Homepage.stats_use_real_cities'       => $this->request->getPost('stats_use_real_cities') ? '1' : '0',
            'Homepage.stats_custom_metric1_number' => trim((string) $this->request->getPost('stats_custom_metric1_number')),
            'Homepage.stats_custom_metric1_label'  => trim((string) $this->request->getPost('stats_custom_metric1_label')),
            'Homepage.stats_custom_metric2_number' => trim((string) $this->request->getPost('stats_custom_metric2_number')),
            'Homepage.stats_custom_metric2_label'  => trim((string) $this->request->getPost('stats_custom_metric2_label')),

            // Section Visibility
            'Homepage.section_hero_visible'        => $this->request->getPost('section_hero_visible') ? '1' : '0',
            'Homepage.section_about_visible'       => $this->request->getPost('section_about_visible') ? '1' : '0',
            'Homepage.section_benefits_visible'    => $this->request->getPost('section_benefits_visible') ? '1' : '0',
            'Homepage.section_categories_visible'  => $this->request->getPost('section_categories_visible') ? '1' : '0',
            'Homepage.section_statistics_visible'  => $this->request->getPost('section_statistics_visible') ? '1' : '0',
            'Homepage.section_members_visible'     => $this->request->getPost('section_members_visible') ? '1' : '0',
            'Homepage.section_cta_visible'         => $this->request->getPost('section_cta_visible') ? '1' : '0',
        ];

        // Handle Hero Background Image
        $heroBgFile = $this->request->getFile('hero_background_image');
        if ($heroBgFile && $heroBgFile->isValid() && ! $heroBgFile->hasMoved()) {
            $ext = $heroBgFile->guessExtension() ?: $heroBgFile->getClientExtension();
            $newFileName = 'hero_bg_' . bin2hex(random_bytes(8)) . '.' . $ext;
            $heroBgFile->move($uploadTargetDir, $newFileName);
            $updates['Homepage.hero_background_image'] = 'uploads/settings/' . $newFileName;
        }

        // Handle About Image
        $aboutImgFile = $this->request->getFile('about_image');
        if ($aboutImgFile && $aboutImgFile->isValid() && ! $aboutImgFile->hasMoved()) {
            $ext = $aboutImgFile->guessExtension() ?: $aboutImgFile->getClientExtension();
            $newFileName = 'about_img_' . bin2hex(random_bytes(8)) . '.' . $ext;
            $aboutImgFile->move($uploadTargetDir, $newFileName);
            $updates['Homepage.about_image'] = 'uploads/settings/' . $newFileName;
        }

        SettingsService::setMultiple($updates);

        return redirect()->to('admin/content/homepage')
            ->with('message', 'Konten dan visibilitas seksi beranda berhasil diperbarui.');
    }

    /**
     * Save (create or update) a benefit item
     */
    public function benefitSave(): RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'id'          => 'permit_empty|is_natural_no_zero',
            'title'       => 'required|min_length[3]|max_length[150]',
            'description' => 'required',
            'icon'        => 'required|in_list[' . implode(',', array_keys(self::AVAILABLE_ICONS)) . ']',
            'sort_order'  => 'required|integer',
            'is_active'   => 'permit_empty|in_list[0,1]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('admin/content/homepage#benefits-section')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $benefitModel = model(HomepageBenefitModel::class);
        $id = (int) $this->request->getPost('id');

        $data = [
            'title'       => trim((string) $this->request->getPost('title')),
            'description' => trim((string) $this->request->getPost('description')),
            'icon'        => (string) $this->request->getPost('icon'),
            'sort_order'  => (int) $this->request->getPost('sort_order'),
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
        ];

        if ($id > 0) {
            $benefitModel->update($id, $data);
            $msg = 'Poin keunggulan berhasil diperbarui.';
        } else {
            $benefitModel->insert($data);
            $msg = 'Poin keunggulan baru berhasil ditambahkan.';
        }

        return redirect()->to('admin/content/homepage#benefits-section')->with('message', $msg);
    }

    /**
     * Delete a benefit item
     */
    public function benefitDelete(int $id): RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $benefitModel = model(HomepageBenefitModel::class);
        $benefitModel->delete($id);

        return redirect()->to('admin/content/homepage#benefits-section')
            ->with('message', 'Poin keunggulan berhasil dihapus.');
    }
}
