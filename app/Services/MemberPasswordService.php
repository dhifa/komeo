<?php

namespace App\Services;

use CodeIgniter\Shield\Entities\User;
use Config\Services;
use Throwable;

class MemberPasswordService
{
    /**
     * Generate a secure and readable random password
     */
    public static function generatePassword(int $length = 10): string
    {
        $numbers = '23456789';
        $uppers  = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lowers  = 'abcdefghjkmnpqrstuvwxyz';
        $specials = '@#$%';

        $pwd = '';
        $pwd .= $uppers[random_int(0, strlen($uppers) - 1)];
        $pwd .= $lowers[random_int(0, strlen($lowers) - 1)];
        $pwd .= $lowers[random_int(0, strlen($lowers) - 1)];
        $pwd .= $specials[random_int(0, strlen($specials) - 1)];

        for ($i = 0; $i < 4; $i++) {
            $pwd .= $numbers[random_int(0, strlen($numbers) - 1)];
        }

        // Add remaining random characters
        $all = $uppers . $lowers . $numbers;
        while (strlen($pwd) < $length) {
            $pwd .= $all[random_int(0, strlen($all) - 1)];
        }

        return str_shuffle($pwd);
    }

    /**
     * Reset password in Shield user provider
     *
     * @param int $userId
     * @param string|null $customPassword
     * @return array [success => bool, message => string, password => ?string, user => ?User]
     */
    public static function resetPasswordByAdmin(int $userId, ?string $customPassword = null): array
    {
        $userProvider = auth()->getProvider();
        /** @var User|null $user */
        $user = $userProvider->findById($userId);

        if (! $user) {
            return [
                'success'  => false,
                'message'  => 'Pengguna tidak ditemukan dalam sistem autentikasi.',
                'password' => null,
                'user'     => null,
            ];
        }

        $newPassword = ! empty($customPassword) ? trim($customPassword) : self::generatePassword(10);

        if (strlen($newPassword) < 8) {
            return [
                'success'  => false,
                'message'  => 'Password minimal harus 8 karakter.',
                'password' => null,
                'user'     => null,
            ];
        }

        try {
            $user->password = $newPassword;
            $userProvider->save($user);

            return [
                'success'  => true,
                'message'  => 'Password pengguna berhasil diperbarui di sistem.',
                'password' => $newPassword,
                'user'     => $user,
            ];
        } catch (Throwable $e) {
            return [
                'success'  => false,
                'message'  => 'Gagal memperbarui password pengguna: ' . $e->getMessage(),
                'password' => null,
                'user'     => null,
            ];
        }
    }

    /**
     * Send email notification containing the new password to member
     */
    public static function sendResetEmail(string $recipientEmail, string $recipientName, string $newPassword): array
    {
        try {
            $email = Services::email();

            $fromEmail = config('Email')->fromEmail ?? 'no-reply@komeo.id';
            $fromName  = config('Email')->fromName ?? 'KOMEO.ID Support';

            $email->setFrom($fromEmail, $fromName);
            $email->setTo($recipientEmail);
            $email->setMailType('html');
            $email->setSubject('[KOMEO.ID] Password Akun Anda Telah Direset');

            $loginUrl = base_url('login');
            $profileUrl = base_url('dashboard/profil');

            $htmlMessage = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>Pembaruan Password Akun KOMEO.ID</title>
                <style>
                    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 20px; color: #1e293b; }
                    .card { max-width: 540px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 32px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
                    .header { text-align: center; margin-bottom: 24px; }
                    .brand { font-size: 22px; font-weight: 900; color: #0f172a; letter-spacing: -0.5px; }
                    .brand span { color: #f59e0b; }
                    .badge { display: inline-block; padding: 4px 12px; background: #fef3c7; color: #92400e; font-size: 11px; font-weight: bold; border-radius: 999px; margin-top: 8px; }
                    .content { font-size: 14px; line-height: 1.6; color: #334155; }
                    .box { background: #f1f5f9; border-radius: 12px; padding: 16px; margin: 20px 0; border: 1px dashed #cbd5e1; }
                    .pwd { font-size: 20px; font-weight: 800; font-family: monospace; color: #0f172a; letter-spacing: 2px; }
                    .btn { display: inline-block; background: #0f172a; color: #ffffff !important; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-weight: bold; font-size: 13px; margin-top: 10px; }
                    .footer { font-size: 11px; color: #94a3b8; text-align: center; margin-top: 24px; border-top: 1px solid #f1f5f9; padding-top: 16px; }
                </style>
            </head>
            <body>
                <div class="card">
                    <div class="header">
                        <div class="brand">KOMEO<span>.ID</span></div>
                        <div class="badge">RESET PASSWORD OLEH ADMINISTRATOR</div>
                    </div>
                    <div class="content">
                        <p>Halo <strong>' . esc($recipientName) . '</strong>,</p>
                        <p>Password akun keanggotaan Anda di <strong>KOMEO.ID</strong> telah direset oleh Administrator.</p>
                        
                        <div class="box">
                            <div style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Password Baru Anda:</div>
                            <div class="pwd">' . esc($newPassword) . '</div>
                            <div style="font-size: 11px; color: #64748b; margin-top: 4px;">Akun: ' . esc($recipientEmail) . '</div>
                        </div>

                        <p>Silakan klik tombol di bawah untuk masuk ke akun Anda:</p>
                        <p style="text-align: center;"><a href="' . $loginUrl . '" class="btn">Masuk ke KOMEO.ID</a></p>

                        <p style="font-size: 12px; color: #dc2626; margin-top: 20px;">
                            <strong>Perhatian Keamanan:</strong> Demi keamanan akun Anda, mohon segera ganti password ini dengan password pribadi Anda setelah berhasil masuk melalui menu Profil Member.
                        </p>
                    </div>
                    <div class="footer">
                        Email ini dikirim secara otomatis oleh Sistem KOMEO.ID.<br>
                        Satu Komunitas, Ribuan Peluang Kolaborasi.
                    </div>
                </div>
            </body>
            </html>';

            $email->setMessage($htmlMessage);

            if ($email->send()) {
                return ['success' => true, 'message' => "Email notifikasi berhasil dikirim ke {$recipientEmail}."];
            }

            // If send fails (e.g. SMTP config not set locally), grab debugger info
            $debug = $email->printDebugger(['headers']);
            return ['success' => false, 'message' => 'Gagal mengirim email: ' . strip_tags($debug)];
        } catch (Throwable $e) {
            return ['success' => false, 'message' => 'Layanan email gagal diproses: ' . $e->getMessage()];
        }
    }

    /**
     * Format phone number and build a direct WhatsApp click-to-chat URL
     */
    public static function buildWhatsAppUrl(
        ?string $rawPhone,
        string $memberName,
        string $accountIdentifier,
        string $newPassword
    ): ?string {
        if (empty($rawPhone)) {
            return null;
        }

        // Sanitize phone number to international format without + (Indonesia 62...)
        $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        } elseif (str_starts_with($cleanPhone, '8')) {
            $cleanPhone = '62' . $cleanPhone;
        }

        if (strlen($cleanPhone) < 9) {
            return null;
        }

        $loginUrl = base_url('login');

        $message = "Halo *{$memberName}*,\n\n"
            . "Password akun keanggotaan Anda di *KOMEO.ID* telah di-reset oleh Administrator.\n\n"
            . "Berikut detail login baru Anda:\n"
            . "• Akun / Email: {$accountIdentifier}\n"
            . "• Password Baru: *{$newPassword}*\n"
            . "• Halaman Login: {$loginUrl}\n\n"
            . "Demi keamanan, silakan segera login dan perbarui password Anda di menu profil.\n\n"
            . "Salam hangat,\n"
            . "_Tim KOMEO.ID_";

        return 'https://wa.me/' . $cleanPhone . '?text=' . rawurlencode($message);
    }
}
