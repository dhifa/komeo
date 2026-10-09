<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\SettingsService;
use CodeIgniter\HTTP\RedirectResponse;

class MembershipSettingsController extends BaseController
{
    /**
     * Display Membership Activation Settings Page
     */
    public function index(): string|RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $settings = SettingsService::getAll();

        return view('admin/settings/membership', [
            'title'    => 'Pengaturan Aktivasi Keanggotaan - KOMEO.ID',
            'settings' => $settings,
        ]);
    }

    /**
     * Update Membership Activation Settings
     */
    public function update(): RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'activation_whatsapp'     => 'permit_empty|min_length[8]|max_length[25]|regex_match[/^[0-9+\s\-]+$/]',
            'activation_email'        => 'permit_empty|valid_email|max_length[100]',
            'activation_instructions' => 'required|min_length[10]|max_length[1000]',
            'activation_btn_label'    => 'required|min_length[2]|max_length[100]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'Membership.activation_whatsapp'     => trim((string) $this->request->getPost('activation_whatsapp')),
            'Membership.activation_email'        => strtolower(trim((string) $this->request->getPost('activation_email'))),
            'Membership.activation_instructions' => trim(strip_tags((string) $this->request->getPost('activation_instructions'))),
            'Membership.activation_btn_label'    => trim(strip_tags((string) $this->request->getPost('activation_btn_label'))),
            'Membership.enable_whatsapp'         => $this->request->getPost('enable_whatsapp') ? '1' : '0',
            'Membership.enable_email'            => $this->request->getPost('enable_email') ? '1' : '0',
        ];

        SettingsService::setMultiple($data);

        return redirect()->back()->with('message', 'Pengaturan aktivasi keanggotaan dan kontak admin berhasil diperbarui.');
    }
}
