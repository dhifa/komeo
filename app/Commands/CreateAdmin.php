<?php

namespace App\Commands;

use App\Models\MemberProfileModel;
use App\Models\MembershipModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Shield\Entities\User;
use Throwable;

class CreateAdmin extends BaseCommand
{
    /**
     * The Command's Group
     */
    protected $group = 'KOMEO';

    /**
     * The Command's Name
     */
    protected $name = 'admin:create';

    /**
     * The Command's Description
     */
    protected $description = 'Membuat akun administrator pertama untuk KOMEO.ID.';

    /**
     * The Command's Usage
     */
    protected $usage = 'admin:create [options]';

    /**
     * The Command's Options
     */
    protected $options = [
        '-u'         => 'Nama pengguna (username) admin',
        '--username' => 'Nama pengguna (username) admin',
        '-e'         => 'Alamat email admin',
        '--email'    => 'Alamat email admin',
        '-n'         => 'Nama lengkap admin',
        '--name'     => 'Nama lengkap admin',
        '-p'         => 'Kata sandi admin (minimal 8 karakter)',
        '--password' => 'Kata sandi admin (minimal 8 karakter)',
    ];

    public function run(array $params)
    {
        CLI::write("==================================================", 'cyan');
        CLI::write("       PEMBUATAN AKUN ADMINISTRATOR KOMEO.ID       ", 'yellow');
        CLI::write("==================================================", 'cyan');

        $username = $params['username'] ?? $params['u'] ?? CLI::getOption('username') ?? CLI::getOption('u');
        if (empty($username)) {
            $username = CLI::prompt('Masukkan Username Administrator', null, 'required|alpha_numeric_punct|min_length[3]|max_length[30]');
        }
        $username = strtolower(trim((string) $username));

        $email = $params['email'] ?? $params['e'] ?? CLI::getOption('email') ?? CLI::getOption('e');
        if (empty($email)) {
            $email = CLI::prompt('Masukkan Email Administrator', null, 'required|valid_email');
        }
        $email = strtolower(trim((string) $email));

        $name = $params['name'] ?? $params['n'] ?? CLI::getOption('name') ?? CLI::getOption('n');
        if (empty($name)) {
            $name = CLI::prompt('Masukkan Nama Lengkap Administrator', 'Administrator KOMEO', 'required|min_length[2]|max_length[150]');
        }
        $name = trim((string) $name);

        $password = $params['password'] ?? $params['p'] ?? CLI::getOption('password') ?? CLI::getOption('p');
        if (empty($password)) {
            $password = CLI::prompt('Masukkan Kata Sandi (min 8 karakter)', null, 'required|min_length[8]');
        }

        // Check if user already exists
        $userProvider = auth()->getProvider();
        $existingUser = $userProvider->findByCredentials(['email' => $email]) 
            ?: $userProvider->where('username', $username)->first();

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            if ($existingUser) {
                // Update existing user credentials and ensure admin role
                $existingUser->fill([
                    'password' => $password,
                    'active'   => 1,
                ]);
                $userProvider->save($existingUser);
                
                // Ensure admin and superadmin groups
                if (! $existingUser->inGroup('admin')) {
                    $existingUser->addGroup('admin');
                }
                if (! $existingUser->inGroup('superadmin')) {
                    $existingUser->addGroup('superadmin');
                }

                $userId = $existingUser->id;
                $username = $existingUser->username;
                $email = $existingUser->email;
                $isNew = false;
            } else {
                // 1. Create user in Shield
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
                    throw new \RuntimeException('Gagal mendapatkan ID pengguna baru.');
                }

                // 2. Add to admin & superadmin groups
                $user->addGroup('admin', 'superadmin');

                // 3. Create member profile
                $profileModel = model(MemberProfileModel::class);
                $profileModel->insert([
                    'user_id'       => $userId,
                    'full_name'     => $name,
                    'display_name'  => $name,
                    'username'      => $username,
                    'member_type'   => 'individual',
                    'business_name' => 'KOMEO.ID HQ',
                    'is_public'     => 0,
                ]);

                // 4. Create active membership
                $membershipModel = model(MembershipModel::class);
                $membershipModel->insert([
                    'user_id'       => $userId,
                    'member_number' => 'ADM-' . str_pad((string) $userId, 4, '0', STR_PAD_LEFT),
                    'status'        => 'active',
                    'approved_at'   => date('Y-m-d H:i:s'),
                    'joined_at'     => date('Y-m-d H:i:s'),
                ]);

                $isNew = true;
            }

            $db->transCommit();

            CLI::newLine();
            if ($isNew) {
                CLI::write("✓ Akun Administrator baru berhasil dibuat!", 'green');
            } else {
                CLI::write("✓ Kredensial Akun Administrator berhasil diperbarui!", 'green');
            }

            CLI::table([
                ['User ID', (string) $userId],
                ['Username', $username],
                ['Email', $email],
                ['Grup / Peran', 'admin, superadmin'],
                ['Status', 'Aktif'],
            ], ['Keterangan', 'Nilai']);

            CLI::newLine();
            CLI::write("Anda dapat masuk melalui http://localhost:8080/login lalu mengakses http://localhost:8080/admin", 'yellow');

            return EXIT_SUCCESS;

        } catch (Throwable $e) {
            $db->transRollback();
            CLI::error("Terjadi kegagalan: " . $e->getMessage());
            return EXIT_ERROR;
        }
    }
}
