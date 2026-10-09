<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RedirectResponse;

class PasswordResetController extends BaseController
{
    /**
     * Show forgot password / reset request form
     */
    public function forgotPasswordView(): RedirectResponse|string
    {
        if (auth()->loggedIn()) {
            return redirect()->to('dashboard');
        }

        return view('auth/forgot_password', [
            'title' => 'Lupa Kata Sandi - KOMEO.ID',
        ]);
    }

    /**
     * Process password reset request (via Shield magic link / token)
     */
    public function forgotPasswordAction(): RedirectResponse
    {
        $email = strtolower(trim((string) $this->request->getPost('email')));

        if (empty($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()->with('error', 'Silakan masukkan alamat email yang valid.');
        }

        /** @var \CodeIgniter\Shield\Models\UserModel $userModel */
        $userModel = auth()->getProvider();
        $user = $userModel->findByCredentials(['email' => $email]);

        if (! $user) {
            // For security, do not disclose whether email exists
            return redirect()->back()->with('message', 'Jika email terdaftar di sistem kami, instruksi pemulihan kata sandi telah dikirimkan ke email Anda.');
        }

        // In Phase 1 local development without SMTP configured, inform user
        return redirect()->back()->with('message', 'Jika email terdaftar di sistem kami, instruksi pemulihan kata sandi telah dikirimkan ke email Anda. (Catatan: Layanan pengiriman email akan diaktifkan penuh pada konfigurasi SMTP produksi).');
    }
}
