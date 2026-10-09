<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\Kta\CardImageRenderer;
use App\Services\SettingsService;
use CodeIgniter\HTTP\RedirectResponse;

class KtaSettingsController extends BaseController
{
    protected CardImageRenderer $renderer;

    public function __construct()
    {
        $this->renderer = new CardImageRenderer();
    }

    /**
     * Display KTA settings with tabs and live interactive preview & layout editor
     */
    public function index(): string|RedirectResponse
    {
        $user = auth()->user();
        if (!$user || !$user->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $activeTab = $this->request->getGet('tab') ?? 'layout';
        $validTabs = ['layout', 'front', 'back', 'assets', 'colors', 'qr', 'print'];
        if (!in_array($activeTab, $validTabs, true)) {
            $activeTab = 'layout';
        }

        $settings = SettingsService::getKtaSettings();

        return view('admin/settings/kta', [
            'title'     => 'Pengaturan Desain & Editor Tata Letak KTA',
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
        if (!$user || !$user->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        switch ($tab) {
            case 'layout':
                return $this->updateLayout();
            case 'front':
                return $this->updateFront();
            case 'back':
                return $this->updateBack();
            case 'assets':
                return $this->updateAssets();
            case 'colors':
                return $this->updateColors();
            case 'qr':
                return $this->updateQr();
            case 'print':
                return $this->updatePrint();
            default:
                return redirect()->to('admin/settings/kta')->with('error', 'Tab pengaturan tidak dikenali.');
        }
    }

    /**
     * Update Live Visual Layout Coordinates and Styles
     */
    protected function updateLayout(): RedirectResponse
    {
        $data = [
            'Kta.layout_mode'         => $this->request->getPost('layout_mode') === 'custom' ? 'custom' : 'default',
            'Kta.number_style'        => trim((string) ($this->request->getPost('number_style') ?: 'gold_badge')),
            'Kta.photo_shape'         => trim((string) ($this->request->getPost('photo_shape') ?: 'rounded')),
            'Kta.hide_default_shapes' => $this->request->getPost('hide_default_shapes') ? '1' : '0',
            'Kta.show_meta'           => $this->request->getPost('show_meta') ? '1' : '0',
            'Kta.show_status_badge'   => $this->request->getPost('show_status_badge') ? '1' : '0',

            // Coordinates
            'Kta.pos_photo_x'         => max(0, min(1011, (int) $this->request->getPost('pos_photo_x'))),
            'Kta.pos_photo_y'         => max(0, min(638, (int) $this->request->getPost('pos_photo_y'))),
            'Kta.pos_photo_w'         => max(80, min(400, (int) ($this->request->getPost('pos_photo_w') ?: 210))),
            'Kta.pos_photo_h'         => max(80, min(500, (int) ($this->request->getPost('pos_photo_h') ?: 270))),

            'Kta.pos_name_x'          => max(0, min(1011, (int) $this->request->getPost('pos_name_x'))),
            'Kta.pos_name_y'          => max(0, min(638, (int) $this->request->getPost('pos_name_y'))),
            'Kta.pos_name_size'       => max(12, min(36, (int) ($this->request->getPost('pos_name_size') ?: 22))),

            'Kta.pos_number_x'        => max(0, min(1011, (int) $this->request->getPost('pos_number_x'))),
            'Kta.pos_number_y'        => max(0, min(638, (int) $this->request->getPost('pos_number_y'))),

            'Kta.pos_category_x'      => max(0, min(1011, (int) $this->request->getPost('pos_category_x'))),
            'Kta.pos_category_y'      => max(0, min(638, (int) $this->request->getPost('pos_category_y'))),

            'Kta.pos_meta_x'          => max(0, min(1011, (int) $this->request->getPost('pos_meta_x'))),
            'Kta.pos_meta_y'          => max(0, min(638, (int) $this->request->getPost('pos_meta_y'))),

            'Kta.pos_qr_x'            => max(0, min(1011, (int) $this->request->getPost('pos_qr_x'))),
            'Kta.pos_qr_y'            => max(0, min(638, (int) $this->request->getPost('pos_qr_y'))),
            'Kta.pos_qr_size'         => max(100, min(260, (int) ($this->request->getPost('pos_qr_size') ?: 190))),

            'Kta.pos_title_x'         => max(0, min(1011, (int) $this->request->getPost('pos_title_x'))),
            'Kta.pos_title_y'         => max(0, min(638, (int) $this->request->getPost('pos_title_y'))),
            'Kta.pos_logo_x'          => max(0, min(1011, (int) $this->request->getPost('pos_logo_x'))),
            'Kta.pos_logo_y'          => max(0, min(638, (int) $this->request->getPost('pos_logo_y'))),
        ];

        // Optional quick background upload directly from layout editor
        $bgFile = $this->request->getFile('quick_bg_front');
        if ($bgFile && $bgFile->isValid() && !$bgFile->hasMoved()) {
            $targetDir = FCPATH . 'uploads/kta/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $newName = 'kta_bg_front_' . time() . '.' . $bgFile->getExtension();
            $bgFile->move($targetDir, $newName);
            $data['Kta.bg_pattern_front'] = 'uploads/kta/' . $newName;
            $data['Kta.front_bg_image'] = 'uploads/kta/' . $newName;
        }

        SettingsService::setMultiple($data);

        return redirect()->to('admin/settings/kta?tab=layout')->with('message', 'Tata letak elemen KTA dan gaya nomor anggota berhasil diperbarui.');
    }

    protected function updateFront(): RedirectResponse
    {
        $rules = [
            'card_title'       => 'required|min_length[3]|max_length[60]',
            'bg_color_front'   => 'required|regex_match[/^#[a-fA-F0-9]{6}$/]',
            'text_color_front' => 'required|regex_match[/^#[a-fA-F0-9]{6}$/]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('admin/settings/kta?tab=front')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        SettingsService::setMultiple([
            'Kta.card_title'       => trim((string) $this->request->getPost('card_title')),
            'Kta.bg_color_front'   => trim((string) $this->request->getPost('bg_color_front')),
            'Kta.text_color_front' => trim((string) $this->request->getPost('text_color_front')),
            'Kta.front_bg_color'   => trim((string) $this->request->getPost('bg_color_front')),
        ]);

        return redirect()->to('admin/settings/kta?tab=front')->with('message', 'Desain depan KTA berhasil diperbarui.');
    }

    protected function updateBack(): RedirectResponse
    {
        $rules = [
            'tagline'              => 'required|min_length[3]|max_length[150]',
            'back_statement'       => 'required|min_length[10]|max_length[500]',
            'back_rules_title'     => 'permit_empty|max_length[100]',
            'back_rules_text'      => 'permit_empty',
            'back_header_title'    => 'permit_empty|max_length[100]',
            'back_header_subtitle' => 'permit_empty|max_length[150]',
            'back_badge_text'      => 'permit_empty|max_length[100]',
            'back_office_address'  => 'permit_empty|max_length[200]',
            'website_url'          => 'required|valid_url_strict',
            'bg_color_back'        => 'required|regex_match[/^#[a-fA-F0-9]{6}$/]',
            'text_color_back'      => 'required|regex_match[/^#[a-fA-F0-9]{6}$/]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('admin/settings/kta?tab=back')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        SettingsService::setMultiple([
            'Kta.back_header_title'    => trim((string) ($this->request->getPost('back_header_title') ?: 'KOMEO.ID')),
            'Kta.back_header_subtitle' => trim((string) ($this->request->getPost('back_header_subtitle') ?: 'KOMUNITAS EVENT ORGANIZER INDONESIA')),
            'Kta.back_badge_text'      => trim((string) ($this->request->getPost('back_badge_text') ?: 'IDENTITAS RESMI KEANGGOTAAN')),
            'Kta.tagline'              => trim((string) $this->request->getPost('tagline')),
            'Kta.back_statement'       => trim((string) $this->request->getPost('back_statement')),
            'Kta.back_message'         => trim((string) $this->request->getPost('back_statement')),
            'Kta.back_rules_title'     => trim((string) ($this->request->getPost('back_rules_title') ?: 'KETENTUAN PENGGUNAAN KARTU:')),
            'Kta.back_rules_text'      => trim((string) $this->request->getPost('back_rules_text')),
            'Kta.back_office_address'  => trim((string) $this->request->getPost('back_office_address')),
            'Kta.back_show_sign'       => $this->request->getPost('back_show_sign') ? '1' : '0',
            'Kta.back_sign_title'      => trim((string) $this->request->getPost('back_sign_title')),
            'Kta.back_sign_name'       => trim((string) $this->request->getPost('back_sign_name')),
            'Kta.website_url'          => trim((string) $this->request->getPost('website_url')),
            'Kta.bg_color_back'        => trim((string) $this->request->getPost('bg_color_back')),
            'Kta.back_bg_color'        => trim((string) $this->request->getPost('bg_color_back')),
            'Kta.text_color_back'      => trim((string) $this->request->getPost('text_color_back')),
        ]);

        return redirect()->to('admin/settings/kta?tab=back')->with('message', 'Seluruh konten dan tata letak bagian belakang KTA berhasil diperbarui.');
    }

    protected function updateAssets(): RedirectResponse
    {
        $rules = [
            'logo' => [
                'label' => 'Logo KTA',
                'rules' => 'permit_empty|uploaded[logo]|max_size[logo,2048]|ext_in[logo,png,jpg,jpeg,webp]|mime_in[logo,image/png,image/jpeg,image/webp]',
            ],
            'bg_pattern_front' => [
                'label' => 'Background Depan',
                'rules' => 'permit_empty|uploaded[bg_pattern_front]|max_size[bg_pattern_front,4096]|ext_in[bg_pattern_front,png,jpg,jpeg,webp]|mime_in[bg_pattern_front,image/png,image/jpeg,image/webp]',
            ],
            'bg_pattern_back' => [
                'label' => 'Background Belakang',
                'rules' => 'permit_empty|uploaded[bg_pattern_back]|max_size[bg_pattern_back,4096]|ext_in[bg_pattern_back,png,jpg,jpeg,webp]|mime_in[bg_pattern_back,image/png,image/jpeg,image/webp]',
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('admin/settings/kta?tab=assets')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $targetDir = FCPATH . 'uploads/kta/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $map = [
            'logo'             => 'Kta.logo',
            'bg_pattern_front' => 'Kta.bg_pattern_front',
            'bg_pattern_back'  => 'Kta.bg_pattern_back',
        ];

        foreach ($map as $inputName => $settingKey) {
            $file = $this->request->getFile($inputName);
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $newName = 'kta_' . $inputName . '_' . time() . '.' . $file->getExtension();
                $file->move($targetDir, $newName);
                SettingsService::set($settingKey, 'uploads/kta/' . $newName);
                if ($inputName === 'bg_pattern_front') {
                    SettingsService::set('Kta.front_bg_image', 'uploads/kta/' . $newName);
                }
                if ($inputName === 'bg_pattern_back') {
                    SettingsService::set('Kta.back_bg_image', 'uploads/kta/' . $newName);
                }
            }
        }

        return redirect()->to('admin/settings/kta?tab=assets')->with('message', 'Berkas visual KTA berhasil diunggah.');
    }

    protected function updateColors(): RedirectResponse
    {
        $rules = [
            'accent_purple' => 'required|regex_match[/^#[a-fA-F0-9]{6}$/]',
            'accent_cyan'   => 'required|regex_match[/^#[a-fA-F0-9]{6}$/]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('admin/settings/kta?tab=colors')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        SettingsService::setMultiple([
            'Kta.accent_purple' => trim((string) $this->request->getPost('accent_purple')),
            'Kta.primary_color' => trim((string) $this->request->getPost('accent_purple')),
            'Kta.accent_cyan'   => trim((string) $this->request->getPost('accent_cyan')),
            'Kta.accent_color'  => trim((string) $this->request->getPost('accent_cyan')),
        ]);

        return redirect()->to('admin/settings/kta?tab=colors')->with('message', 'Palet warna aksen KTA berhasil diperbarui.');
    }

    protected function updateQr(): RedirectResponse
    {
        $rules = [
            'qr_position' => 'required|in_list[right,left]',
            'qr_size'     => 'required|integer|greater_than_equal_to[140]|less_than_equal_to[240]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('admin/settings/kta?tab=qr')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        SettingsService::setMultiple([
            'Kta.qr_position' => trim((string) $this->request->getPost('qr_position')),
            'Kta.qr_size'     => (int) $this->request->getPost('qr_size'),
            'Kta.pos_qr_size' => (int) $this->request->getPost('qr_size'),
        ]);

        return redirect()->to('admin/settings/kta?tab=qr')->with('message', 'Pengaturan QR Code KTA berhasil disimpan.');
    }

    protected function updatePrint(): RedirectResponse
    {
        $rules = [
            'export_dpi'      => 'required|integer|in_list[300,600]',
            'safe_margin_mm'  => 'required|numeric|greater_than_equal_to[1]|less_than_equal_to[10]',
            'bleed_mm'        => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[10]',
            'print_profile'   => 'required|in_list[pvc,digital_sheet]',
            'sheet_size'      => 'required|in_list[A4,A3]',
            'card_spacing_mm' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[10]',
            'back_rotation'   => 'required|integer|in_list[0,180]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('admin/settings/kta?tab=print')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        SettingsService::setMultiple([
            'Kta.export_dpi'      => (int) $this->request->getPost('export_dpi'),
            'Kta.safe_margin_mm'  => (float) $this->request->getPost('safe_margin_mm'),
            'Kta.bleed_mm'        => (float) $this->request->getPost('bleed_mm'),
            'Kta.print_profile'   => trim((string) $this->request->getPost('print_profile')),
            'Kta.sheet_size'      => trim((string) $this->request->getPost('sheet_size')),
            'Kta.card_spacing_mm' => (float) $this->request->getPost('card_spacing_mm'),
            'Kta.back_rotation'   => (int) $this->request->getPost('back_rotation'),
        ]);

        return redirect()->to('admin/settings/kta?tab=print')->with('message', 'Pengaturan profil cetak berhasil disimpan.');
    }

    public function deleteImage(string $field): RedirectResponse
    {
        $allowed = ['logo', 'bg_pattern_front', 'bg_pattern_back'];
        if (!in_array($field, $allowed, true)) {
            return redirect()->to('admin/settings/kta?tab=assets')->with('error', 'Bidang gambar tidak sah.');
        }

        $key = 'Kta.' . $field;
        $current = SettingsService::get($key);
        if ($current && file_exists(FCPATH . $current)) {
            @unlink(FCPATH . $current);
        }

        SettingsService::set($key, '');
        if ($field === 'bg_pattern_front') {
            SettingsService::set('Kta.front_bg_image', '');
        }
        if ($field === 'bg_pattern_back') {
            SettingsService::set('Kta.back_bg_image', '');
        }

        return redirect()->to('admin/settings/kta?tab=assets')->with('message', 'Gambar berhasil dihapus.');
    }

    /**
     * Stream live sample preview with sample member data
     */
    public function previewSample(string $side = 'front')
    {
        $db = \Config\Database::connect();
        $sampleMember = $db->table('memberships')->where('status', 'active')->orderBy('id', 'ASC')->get()->getFirstRow('array');

        $membershipId = $sampleMember['id'] ?? 1;

        $stream = ($side === 'back')
            ? $this->renderer->renderBack($membershipId)
            : $this->renderer->renderFront($membershipId);

        return $this->response
            ->setContentType('image/png')
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->setBody($stream);
    }
}
