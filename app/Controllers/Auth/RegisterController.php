<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\MemberProfileModel;
use App\Models\MembershipModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Shield\Entities\User;
use Throwable;

class RegisterController extends BaseController
{
    /**
     * Show registration page
     */
    public function registerView(): RedirectResponse|string
    {
        if (auth()->loggedIn()) {
            return redirect()->to('dashboard');
        }

        return view('auth/register', [
            'title' => 'Daftar Anggota KOMEO.ID',
        ]);
    }

    /**
     * Process member registration
     */
    public function registerAction(): RedirectResponse
    {
        if (auth()->loggedIn()) {
            return redirect()->to('dashboard');
        }

        $rules = [
            'full_name' => [
                'label' => 'Nama Lengkap',
                'rules' => 'required|min_length[2]|max_length[150]',
                'errors' => [
                    'required'   => 'Nama lengkap wajib diisi.',
                    'min_length' => 'Nama lengkap minimal 2 karakter.',
                    'max_length' => 'Nama lengkap maksimal 150 karakter.',
                ],
            ],
            'username' => [
                'label' => 'Nama Pengguna',
                'rules' => 'required|min_length[3]|max_length[30]|regex_match[/^[a-zA-Z0-9_.]+$/]|is_unique[users.username]',
                'errors' => [
                    'required'    => 'Nama pengguna wajib diisi.',
                    'min_length'  => 'Nama pengguna minimal 3 karakter.',
                    'max_length'  => 'Nama pengguna maksimal 30 karakter.',
                    'regex_match' => 'Nama pengguna hanya boleh mengandung huruf, angka, titik, dan garis bawah.',
                    'is_unique'   => 'Nama pengguna ini sudah digunakan. Silakan pilih yang lain.',
                ],
            ],
            'email' => [
                'label' => 'Alamat Email',
                'rules' => 'required|max_length[254]|valid_email|is_unique[auth_identities.secret]',
                'errors' => [
                    'required'    => 'Alamat email wajib diisi.',
                    'valid_email' => 'Format email tidak valid.',
                    'is_unique'   => 'Alamat email ini sudah terdaftar. Silakan gunakan email lain atau masuk ke akun Anda.',
                ],
            ],
            'password' => [
                'label' => 'Kata Sandi',
                'rules' => 'required|min_length[8]',
                'errors' => [
                    'required'   => 'Kata sandi wajib diisi.',
                    'min_length' => 'Kata sandi minimal 8 karakter.',
                ],
            ],
            'password_confirm' => [
                'label' => 'Konfirmasi Kata Sandi',
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'Konfirmasi kata sandi wajib diisi.',
                    'matches'  => 'Konfirmasi kata sandi tidak cocok dengan kata sandi.',
                ],
            ],
            'member_type' => [
                'label' => 'Tipe Anggota',
                'rules' => 'required|in_list[individual,business]',
                'errors' => [
                    'required' => 'Silakan pilih tipe anggota (Perorangan atau Badan Usaha).',
                    'in_list'  => 'Pilihan tipe anggota tidak valid.',
                ],
            ],
            'business_name' => [
                'label' => 'Nama Usaha / Perusahaan',
                'rules' => 'permit_empty|max_length[150]',
            ],
            'whatsapp' => [
                'label' => 'Nomor WhatsApp',
                'rules' => 'permit_empty|min_length[9]|max_length[20]|regex_match[/^[0-9+\s\-]+$/]',
                'errors' => [
                    'regex_match' => 'Nomor WhatsApp hanya boleh angka, tanda +, spasi, atau strip.',
                ],
            ],
            'city' => [
                'label' => 'Kota / Kabupaten',
                'rules' => 'permit_empty|max_length[100]',
            ],
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $fullName     = trim((string) $this->request->getPost('full_name'));
        $username     = strtolower(trim((string) $this->request->getPost('username')));
        $email        = strtolower(trim((string) $this->request->getPost('email')));
        $password     = (string) $this->request->getPost('password');
        $memberType   = (string) $this->request->getPost('member_type');
        $businessName = trim((string) $this->request->getPost('business_name'));
        $whatsapp     = trim((string) $this->request->getPost('whatsapp'));
        $city         = trim((string) $this->request->getPost('city'));

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            // 1. Create Shield user
            $userProvider = auth()->getProvider();
            $user = new User([
                'username' => $username,
                'email'    => $email,
                'password' => $password,
                'active'   => 1,
            ]);

            $userProvider->save($user);
            $userId = (int) $userProvider->getInsertID();
            $user = $userProvider->findById($userId);

            if (! $user) {
                throw new DatabaseException('Gagal membuat akun pengguna.');
            }

            // 2. Add to default group 'user' (never allow public registration as admin)
            $user->addGroup('user');

            // 3. Create member profile
            $profileModel = model(MemberProfileModel::class);
            $profileData = [
                'user_id'       => $userId,
                'full_name'     => $fullName,
                'display_name'  => $fullName,
                'username'      => $username,
                'member_type'   => $memberType,
                'business_name' => ($memberType === 'business' && ! empty($businessName)) ? $businessName : null,
                'city'          => ! empty($city) ? $city : null,
                'whatsapp'      => ! empty($whatsapp) ? $whatsapp : null,
                'is_public'     => 1,
            ];
            $profileModel->insert($profileData);

            // 4. Create membership with "pending" status
            $membershipModel = model(MembershipModel::class);
            $membershipData = [
                'user_id'       => $userId,
                'member_number' => null,
                'status'        => 'pending',
                'joined_at'     => date('Y-m-d H:i:s'),
            ];
            $membershipModel->insert($membershipData);

            // 5. Create membership activation request tracking record (Phase 5 Extension)
            $activationModel = model(\App\Models\MembershipActivationRequestModel::class);
            $activationModel->getOrCreateForUser($userId);

            // Commit transaction
            $db->transCommit();

            // 5. Log in newly created member
            auth('session')->logIn($user);

            return redirect()->to('dashboard')->with('message', 'Pendaftaran berhasil! Selamat datang di KOMEO.ID.');

        } catch (Throwable $e) {
            $db->transRollback();
            log_message('error', 'Registration error: ' . $e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memproses pendaftaran. Silakan coba lagi.');
        }
    }
}
