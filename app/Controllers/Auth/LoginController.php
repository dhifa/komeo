<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RedirectResponse;

class LoginController extends BaseController
{
    /**
     * Show login page
     */
    public function loginView(): RedirectResponse|string
    {
        if (auth()->loggedIn()) {
            $user = auth()->user();
            if ($user && $user->inGroup('admin', 'superadmin')) {
                return redirect()->to('admin');
            }
            return redirect()->to('dashboard');
        }

        return view('auth/login', [
            'title' => 'Masuk ke KOMEO.ID',
        ]);
    }

    /**
     * Handle login submission
     */
    public function loginAction(): RedirectResponse
    {
        if (auth()->loggedIn()) {
            return redirect()->to('dashboard');
        }

        $rules = [
            'login' => [
                'label'  => 'Email atau Username',
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Email atau Nama Pengguna wajib diisi.',
                ],
            ],
            'password' => [
                'label'  => 'Kata Sandi',
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Kata sandi wajib diisi.',
                ],
            ],
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $loginInput = trim((string) $this->request->getPost('login'));
        $password   = (string) $this->request->getPost('password');
        $remember   = (bool) $this->request->getPost('remember');

        // Determine if input is email or username
        $credentials = [
            'password' => $password,
        ];
        if (str_contains($loginInput, '@')) {
            $credentials['email'] = strtolower($loginInput);
        } else {
            $credentials['username'] = strtolower($loginInput);
        }

        /** @var \CodeIgniter\Shield\Authentication\Authenticators\Session $authenticator */
        $authenticator = auth('session')->getAuthenticator();
        $result = $authenticator->attempt($credentials, $remember);

        if (! $result->isOK()) {
            return redirect()->back()->withInput()->with('error', 'Kredensial tidak valid. Silakan periksa kembali email/nama pengguna dan kata sandi Anda.');
        }

        $user = auth()->user();

        // Redirect admin directly to admin area, members to member dashboard
        if ($user && $user->inGroup('admin', 'superadmin')) {
            return redirect()->to('admin')->with('message', 'Selamat datang kembali, Administrator!');
        }

        return redirect()->to('dashboard')->with('message', 'Selamat datang kembali di KOMEO.ID!');
    }

    /**
     * Log the user out
     */
    public function logoutAction(): RedirectResponse
    {
        auth('session')->logout();

        return redirect()->to('login')->with('message', 'Anda telah berhasil keluar.');
    }
}
