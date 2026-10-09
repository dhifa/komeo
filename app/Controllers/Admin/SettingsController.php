<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\SettingsService;
use CodeIgniter\HTTP\RedirectResponse;

class SettingsController extends BaseController
{
    /**
     * Display settings page with tab navigation
     */
    public function index(): string|RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $activeTab = $this->request->getGet('tab') ?? 'identity';
        $validTabs = ['identity', 'logo', 'appearance', 'social', 'contact', 'seo', 'footer'];
        if (! in_array($activeTab, $validTabs, true)) {
            $activeTab = 'identity';
        }

        $settings = SettingsService::getAll();

        return view('admin/settings/index', [
            'title'     => 'Pengaturan Website - KOMEO.ID',
            'activeTab' => $activeTab,
            'settings'  => $settings,
        ]);
    }

    /**
     * Handle updating settings based on tab
     */
    public function update(string $tab): RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->inGroup('admin', 'superadmin', 'editor')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        switch ($tab) {
            case 'identity':
                return $this->updateIdentity();
            case 'logo':
                return $this->updateLogo();
            case 'appearance':
                return $this->updateAppearance();
            case 'social':
                return $this->updateSocial();
            case 'contact':
                return $this->updateContact();
            case 'seo':
                return $this->updateSeo();
            case 'footer':
                return $this->updateFooter();
            default:
                return redirect()->to('admin/settings')->with('error', 'Tab pengaturan tidak dikenali.');
        }
    }

    /**
     * Update Website Identity settings
     */
    protected function updateIdentity(): RedirectResponse
    {
        $rules = [
            'site_name'          => 'required|min_length[2]|max_length[100]',
            'site_subtitle'      => 'permit_empty|max_length[100]',
            'site_tagline'       => 'required|min_length[3]|max_length[200]',
            'site_description'   => 'permit_empty|max_length[500]',
            'footer_description' => 'permit_empty|max_length[500]',
            'copyright_text'     => 'required|max_length[150]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('admin/settings?tab=identity')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        SettingsService::setMultiple([
            'App.site_name'          => trim((string) $this->request->getPost('site_name')),
            'App.site_subtitle'      => trim((string) $this->request->getPost('site_subtitle')),
            'App.site_tagline'       => trim((string) $this->request->getPost('site_tagline')),
            'App.site_description'   => trim((string) $this->request->getPost('site_description')),
            'App.footer_description' => trim((string) $this->request->getPost('footer_description')),
            'App.copyright_text'     => trim((string) $this->request->getPost('copyright_text')),
        ]);

        return redirect()->to('admin/settings?tab=identity')
            ->with('message', 'Identitas website berhasil diperbarui.');
    }

    /**
     * Update Logo and Favicon uploads
     */
    protected function updateLogo(): RedirectResponse
    {
        $validationRules = [
            'logo_header' => [
                'label' => 'Logo Header',
                'rules' => 'permit_empty|uploaded[logo_header]|max_size[logo_header,2048]|ext_in[logo_header,png,jpg,jpeg,webp]|mime_in[logo_header,image/png,image/jpeg,image/webp]',
            ],
            'logo_footer' => [
                'label' => 'Logo Footer',
                'rules' => 'permit_empty|uploaded[logo_footer]|max_size[logo_footer,2048]|ext_in[logo_footer,png,jpg,jpeg,webp]|mime_in[logo_footer,image/png,image/jpeg,image/webp]',
            ],
            'favicon' => [
                'label' => 'Favicon',
                'rules' => 'permit_empty|uploaded[favicon]|max_size[favicon,1024]|ext_in[favicon,png,ico]|mime_in[favicon,image/png,image/x-icon,image/vnd.microsoft.icon]',
            ],
            'og_image' => [
                'label' => 'Social Share (OG Image)',
                'rules' => 'permit_empty|uploaded[og_image]|max_size[og_image,3072]|ext_in[og_image,png,jpg,jpeg,webp]|mime_in[og_image,image/png,image/jpeg,image/webp]',
            ],
        ];

        if (! $this->validate($validationRules)) {
            return redirect()->to('admin/settings?tab=logo')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $uploadTargetDir = FCPATH . 'uploads/settings/';
        if (! is_dir($uploadTargetDir)) {
            mkdir($uploadTargetDir, 0755, true);
        }

        $fields = [
            'logo_header' => 'App.logo_header',
            'logo_footer' => 'App.logo_footer',
            'favicon'     => 'App.favicon',
            'og_image'    => 'App.og_image',
        ];

        $updatedCount = 0;
        foreach ($fields as $fieldName => $settingKey) {
            $file = $this->request->getFile($fieldName);
            if ($file && $file->isValid() && ! $file->hasMoved()) {
                // Delete existing old file if present
                $oldPath = SettingsService::get($settingKey);
                if (! empty($oldPath) && file_exists(FCPATH . $oldPath) && str_starts_with($oldPath, 'uploads/settings/')) {
                    @unlink(FCPATH . $oldPath);
                }

                $ext = $file->guessExtension() ?: $file->getClientExtension();
                $newFileName = $fieldName . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
                $file->move($uploadTargetDir, $newFileName);

                SettingsService::set($settingKey, 'uploads/settings/' . $newFileName);
                $updatedCount++;
            }
        }

        return redirect()->to('admin/settings?tab=logo')
            ->with('message', $updatedCount > 0 ? 'Berkas logo/favicon berhasil diunggah dan disimpan.' : 'Tidak ada berkas baru yang diunggah.');
    }

    /**
     * Delete an uploaded image setting
     */
    public function deleteImage(string $type): RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $keyMap = [
            'header'      => 'App.logo_header',
            'logo_header' => 'App.logo_header',
            'footer'      => 'App.logo_footer',
            'logo_footer' => 'App.logo_footer',
            'favicon'     => 'App.favicon',
            'og'          => 'App.og_image',
            'og_image'    => 'App.og_image',
        ];

        if (! isset($keyMap[$type])) {
            return redirect()->to('admin/settings?tab=logo')->with('error', 'Tipe gambar tidak valid.');
        }

        $settingKey = $keyMap[$type];
        $oldPath = SettingsService::get($settingKey);
        if (! empty($oldPath) && file_exists(FCPATH . $oldPath) && str_starts_with($oldPath, 'uploads/settings/')) {
            @unlink(FCPATH . $oldPath);
        }

        SettingsService::set($settingKey, '');

        return redirect()->to('admin/settings?tab=logo')
            ->with('message', 'Gambar berhasil dihapus dan kembali ke tampilan standar.');
    }

    /**
     * Update Dynamic Brand Colors
     */
    protected function updateAppearance(): RedirectResponse
    {
        $primary    = trim((string) $this->request->getPost('color_primary'));
        $secondary  = trim((string) $this->request->getPost('color_secondary'));
        $accent     = trim((string) $this->request->getPost('color_accent'));
        $background = trim((string) $this->request->getPost('color_background'));
        $text       = trim((string) $this->request->getPost('color_text'));

        $colors = [
            'Warna Utama (Primary)'      => $primary,
            'Warna Sekunder (Secondary)' => $secondary,
            'Warna Aksen (Accent)'       => $accent,
            'Warna Latar (Background)'   => $background,
            'Warna Teks (Text)'          => $text,
        ];

        $errors = [];
        foreach ($colors as $label => $val) {
            if (! SettingsService::isValidHexColor($val)) {
                $errors[] = "Format {$label} tidak valid. Gunakan format HEX seperti #4F46E5.";
            }
        }

        if (! empty($errors)) {
            return redirect()->to('admin/settings?tab=appearance')
                ->withInput()
                ->with('errors', $errors);
        }

        SettingsService::setMultiple([
            'App.color_primary'    => strtoupper($primary),
            'App.color_secondary'  => strtoupper($secondary),
            'App.color_accent'     => strtoupper($accent),
            'App.color_background' => strtoupper($background),
            'App.color_text'       => strtoupper($text),
        ]);

        return redirect()->to('admin/settings?tab=appearance')
            ->with('message', 'Skema warna brand berhasil diperbarui.');
    }

    /**
     * Reset Brand Colors to Default
     */
    public function resetColors(): RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        SettingsService::resetToDefaults([
            'App.color_primary',
            'App.color_secondary',
            'App.color_accent',
            'App.color_background',
            'App.color_text',
        ]);

        return redirect()->to('admin/settings?tab=appearance')
            ->with('message', 'Warna brand berhasil direset ke standar KOMEO.ID.');
    }

    /**
     * Update Social Media settings
     */
    protected function updateSocial(): RedirectResponse
    {
        $rules = [
            'social_instagram' => 'permit_empty|valid_url',
            'social_linkedin'  => 'permit_empty|valid_url',
            'social_tiktok'    => 'permit_empty|valid_url',
            'social_youtube'   => 'permit_empty|valid_url',
            'social_facebook'  => 'permit_empty|valid_url',
            'social_whatsapp'  => 'permit_empty|valid_url',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('admin/settings?tab=social')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        SettingsService::setMultiple([
            'App.social_instagram' => trim((string) $this->request->getPost('social_instagram')),
            'App.social_linkedin'  => trim((string) $this->request->getPost('social_linkedin')),
            'App.social_tiktok'    => trim((string) $this->request->getPost('social_tiktok')),
            'App.social_youtube'   => trim((string) $this->request->getPost('social_youtube')),
            'App.social_facebook'  => trim((string) $this->request->getPost('social_facebook')),
            'App.social_whatsapp'  => trim((string) $this->request->getPost('social_whatsapp')),
        ]);

        return redirect()->to('admin/settings?tab=social')
            ->with('message', 'Tautan media sosial berhasil disimpan.');
    }

    /**
     * Update Contact settings
     */
    protected function updateContact(): RedirectResponse
    {
        $rules = [
            'contact_email'    => 'required|valid_email',
            'contact_phone'    => 'permit_empty|max_length[30]',
            'contact_whatsapp' => 'permit_empty|max_length[30]',
            'contact_city'     => 'permit_empty|max_length[100]',
            'contact_address'  => 'permit_empty|max_length[255]',
            'contact_maps_url' => 'permit_empty|valid_url',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('admin/settings?tab=contact')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        SettingsService::setMultiple([
            'App.contact_email'    => strtolower(trim((string) $this->request->getPost('contact_email'))),
            'App.contact_phone'    => trim((string) $this->request->getPost('contact_phone')),
            'App.contact_whatsapp' => trim((string) $this->request->getPost('contact_whatsapp')),
            'App.contact_city'     => trim((string) $this->request->getPost('contact_city')),
            'App.contact_address'  => trim((string) $this->request->getPost('contact_address')),
            'App.contact_maps_url' => trim((string) $this->request->getPost('contact_maps_url')),
        ]);

        return redirect()->to('admin/settings?tab=contact')
            ->with('message', 'Informasi kontak resmi berhasil disimpan.');
    }

    /**
     * Update SEO & Metadata settings
     */
    protected function updateSeo(): RedirectResponse
    {
        $rules = [
            'meta_title'          => 'required|min_length[3]|max_length[150]',
            'meta_description'    => 'required|min_length[10]|max_length[300]',
            'meta_keywords'       => 'permit_empty|max_length[300]',
            'social_share_title'  => 'permit_empty|max_length[150]',
            'social_share_description' => 'permit_empty|max_length[300]',
            'robots_indexing'     => 'required|max_length[50]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('admin/settings?tab=seo')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $robots = trim((string) $this->request->getPost('robots_indexing'));
        $validRobots = ['index, follow', 'noindex, follow', 'noindex, nofollow', 'index, nofollow'];
        if (! in_array($robots, $validRobots, true)) {
            $robots = 'index, follow';
        }

        SettingsService::setMultiple([
            'App.meta_title'          => trim((string) $this->request->getPost('meta_title')),
            'App.meta_description'    => trim((string) $this->request->getPost('meta_description')),
            'App.meta_keywords'       => trim((string) $this->request->getPost('meta_keywords')),
            'App.social_share_title'  => trim((string) $this->request->getPost('social_share_title')),
            'App.social_share_description' => trim((string) $this->request->getPost('social_share_description')),
            'App.robots_indexing'     => trim((string) $this->request->getPost('robots_indexing')),
        ]);

        return redirect()->to('admin/settings?tab=seo')
            ->with('message', 'Pengaturan SEO dan metadata berhasil disimpan.');
    }

    /**
     * Update Footer settings
     */
    protected function updateFooter(): RedirectResponse
    {
        $rules = [
            'footer_description' => 'required',
            'footer_subtext'     => 'permit_empty|max_length[255]',
            'copyright_text'     => 'required|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('admin/settings?tab=footer')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        SettingsService::setMultiple([
            'App.footer_description' => trim((string) $this->request->getPost('footer_description')),
            'App.footer_subtext'     => trim((string) $this->request->getPost('footer_subtext')),
            'App.copyright_text'     => trim((string) $this->request->getPost('copyright_text')),
        ]);

        return redirect()->to('admin/settings?tab=footer')
            ->with('message', 'Pengaturan footer website berhasil disimpan.');
    }
}
