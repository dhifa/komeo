<?php

namespace App\Services;

class NotificationEmailService
{
    /**
     * Send email and record log
     *
     * @param string $to
     * @param string $subject
     * @param string $htmlBody
     * @param string $templateName
     * @return array [success => bool, message => string]
     */
    public static function sendMail(
        string $to,
        string $subject,
        string $htmlBody,
        string $templateName = 'general'
    ): array {
        $email = service('email');

        $siteName = function_exists('site_setting') ? site_setting('App.site_name', 'KOMEO.ID') : 'KOMEO.ID';
        $fromEmail = env('email.fromEmail', function_exists('site_setting') ? site_setting('App.contact_email', 'halo@komeo.id') : 'halo@komeo.id');
        $fromName  = env('email.fromName', $siteName);

        $email->setTo(trim($to));
        $email->setFrom($fromEmail, $fromName);
        $email->setSubject($subject);
        $email->setMessage($htmlBody);
        $email->setMailType('html');

        $success = false;
        $errorMsg = null;

        try {
            if ($email->send(false)) {
                $success = true;
            } else {
                $errorMsg = $email->printDebugger(['headers']);
            }
        } catch (\Throwable $e) {
            $errorMsg = $e->getMessage();
        }

        // Record to email_logs
        try {
            $db = \Config\Database::connect();
            $db->table('email_logs')->insert([
                'recipient_email' => trim($to),
                'subject'         => $subject,
                'template'        => $templateName,
                'status'          => $success ? 'sent' : 'failed',
                'error_message'   => $errorMsg ? substr($errorMsg, 0, 1000) : null,
                'created_at'      => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Failed recording email log: ' . $e->getMessage());
        }

        return [
            'success' => $success,
            'message' => $success ? 'Email berhasil dikirim.' : ($errorMsg ?: 'Pengiriman email gagal.'),
        ];
    }

    /**
     * Base HTML email wrapper template with KOMEO.ID branding
     */
    public static function wrapHtml(string $title, string $contentHtml): string
    {
        $siteName = function_exists('site_setting') ? site_setting('App.site_name', 'KOMEO.ID') : 'KOMEO.ID';
        $siteUrl  = base_url();

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 24px; line-height: 1.6; }
        .container { max-width: 580px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .header { background: #0f172a; padding: 24px 32px; text-align: left; border-bottom: 2px solid #6366f1; }
        .brand { font-size: 20px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px; text-decoration: none; }
        .brand span { color: #818cf8; }
        .body { padding: 32px; font-size: 14px; color: #334155; }
        .button { display: inline-block; background: #4f46e5; color: #ffffff !important; font-weight: bold; text-decoration: none; padding: 12px 24px; border-radius: 10px; margin: 20px 0; font-size: 14px; }
        .footer { padding: 20px 32px; background: #f8fafc; border-top: 1px solid #e2e8f0; font-size: 11px; color: #64748b; text-align: center; }
        .info-box { background: #f1f5f9; border-radius: 10px; padding: 16px; margin: 16px 0; font-size: 13px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="{$siteUrl}" class="brand">KOMEO<span>.ID</span></a>
        </div>
        <div class="body">
            {$contentHtml}
        </div>
        <div class="footer">
            <p style="margin: 0;">Email otomatis dari sistem {$siteName}. Mohon tidak membalas email ini secara langsung.</p>
            <p style="margin: 4px 0 0;">&copy; 2026 {$siteName} &bull; Komunitas Event Organizer Indonesia</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Send event registration guest verification email
     */
    public static function sendEventGuestVerification(
        string $toEmail,
        string $guestName,
        string $eventTitle,
        string $verifyUrl
    ): array {
        $subject = 'Verifikasi Pendaftaran Kegiatan: ' . $eventTitle;
        $content = <<<HTML
<h2 style="font-size: 18px; color: #0f172a; margin-top: 0;">Halo, {$guestName}!</h2>
<p>Terima kasih telah mendaftar pada kegiatan <strong>{$eventTitle}</strong> melalui portal KOMEO.ID.</p>
<p>Untuk memastikan alamat email Anda valid dan melanjutkan proses pendaftaran, silakan klik tombol verifikasi di bawah ini:</p>
<div style="text-align: center;">
    <a href="{$verifyUrl}" class="button">Verifikasi Pendaftaran Saya</a>
</div>
<div class="info-box">
    <strong>Catatan:</strong> Tautan verifikasi ini berlaku selama 24 jam. Jika Anda tidak pernah merasa mendaftar pada kegiatan ini, silakan abaikan email ini.
</div>
<p style="font-size: 12px; color: #64748b; word-break: break-all;">
    Jika tombol tidak dapat diklik, salin dan buka tautan ini di browser:<br>
    <a href="{$verifyUrl}" style="color: #4f46e5;">{$verifyUrl}</a>
</p>
HTML;

        return self::sendMail($toEmail, $subject, self::wrapHtml($subject, $content), 'event_guest_verification');
    }

    /**
     * Send event ticket confirmation email
     */
    public static function sendEventTicketConfirmation(
        string $toEmail,
        string $participantName,
        string $eventTitle,
        string $regNumber,
        string $ticketUrl
    ): array {
        $subject = 'E-Tiket Kegiatan Dikonfirmasi: ' . $eventTitle;
        $content = <<<HTML
<h2 style="font-size: 18px; color: #0f172a; margin-top: 0;">Selamat, {$participantName}!</h2>
<p>Pendaftaran Anda untuk kegiatan <strong>{$eventTitle}</strong> telah <strong>BERHASIL DIKONFIRMASI</strong>.</p>
<div class="info-box">
    <strong>Nomor Registrasi:</strong> <code style="font-weight: bold; color: #0f172a;">{$regNumber}</code><br>
    <strong>Nama Peserta:</strong> {$participantName}<br>
    <strong>Kegiatan:</strong> {$eventTitle}
</div>
<p>Silakan simpan dan unduh E-Tiket ber-QR Code resmi Anda melalui tautan di bawah ini untuk ditunjukkan pada saat check-in di lokasi acara:</p>
<div style="text-align: center;">
    <a href="{$ticketUrl}" class="button">Lihat & Unduh E-Tiket QR</a>
</div>
<p style="font-size: 12px; color: #64748b; word-break: break-all;">
    Tautan E-Tiket aman:<br>
    <a href="{$ticketUrl}" style="color: #4f46e5;">{$ticketUrl}</a>
</p>
HTML;

        return self::sendMail($toEmail, $subject, self::wrapHtml($subject, $content), 'event_ticket_confirmation');
    }

    /**
     * Send client inquiry verification email
     */
    public static function sendClientInquiryVerification(
        string $toEmail,
        string $clientName,
        string $memberName,
        string $verifyUrl
    ): array {
        $subject = 'Verifikasi Pesan / Penawaran untuk ' . $memberName . ' - KOMEO Connect';
        $content = <<<HTML
<h2 style="font-size: 18px; color: #0f172a; margin-top: 0;">Halo, {$clientName}!</h2>
<p>Anda baru saja mengirimkan pesan / permintaan kolaborasi kepada member <strong>{$memberName}</strong> melalui KOMEO Connect.</p>
<p>Untuk melindungi anggota kami dari spam, silakan klik tombol di bawah ini guna memverifikasi email Anda agar pesan dapat diteruskan langsung ke kotak masuk member:</p>
<div style="text-align: center;">
    <a href="{$verifyUrl}" class="button">Verifikasi & Teruskan Pesan</a>
</div>
<div class="info-box">
    Tautan ini sekaligus menjadi portal akses percakapan pribadi Anda dengan member tanpa perlu mendaftar akun.
</div>
HTML;

        return self::sendMail($toEmail, $subject, self::wrapHtml($subject, $content), 'client_inquiry_verification');
    }

    /**
     * Send new inquiry notification to member
     */
    public static function sendMemberNewInquiryNotification(
        string $toEmail,
        string $memberName,
        string $clientName,
        string $subjectText,
        string $inquiryType,
        string $inboxUrl
    ): array {
        $subject = 'Permintaan Baru di KOMEO Connect dari ' . $clientName;
        $content = <<<HTML
<h2 style="font-size: 18px; color: #0f172a; margin-top: 0;">Halo, {$memberName}!</h2>
<p>Anda menerima pesan / permintaan kolaborasi baru melalui profil publik Anda di KOMEO.ID.</p>
<div class="info-box">
    <strong>Pengirim:</strong> {$clientName}<br>
    <strong>Tipe Permintaan:</strong> {$inquiryType}<br>
    <strong>Subjek:</strong> {$subjectText}
</div>
<p>Klien telah terverifikasi email. Anda dapat melihat rincian pesan, membalas, atau membagikan CV & Portofolio secara aman melalui Dashboard Member Anda:</p>
<div style="text-align: center;">
    <a href="{$inboxUrl}" class="button">Buka Kotak Masuk Permintaan</a>
</div>
HTML;

        return self::sendMail($toEmail, $subject, self::wrapHtml($subject, $content), 'member_new_inquiry');
    }

    /**
     * Send member reply notification to client
     */
    public static function sendClientReplyNotification(
        string $toEmail,
        string $clientName,
        string $memberName,
        string $conversationUrl
    ): array {
        $subject = 'Balasan Baru dari ' . $memberName . ' - KOMEO Connect';
        $content = <<<HTML
<h2 style="font-size: 18px; color: #0f172a; margin-top: 0;">Halo, {$clientName}!</h2>
<p>Member <strong>{$memberName}</strong> telah membalas pesan / permintaan kolaborasi Anda di KOMEO Connect.</p>
<p>Silakan buka ruang percakapan aman Anda melalui tombol di bawah ini:</p>
<div style="text-align: center;">
    <a href="{$conversationUrl}" class="button">Lihat Balasan Percakapan</a>
</div>
HTML;

        return self::sendMail($toEmail, $subject, self::wrapHtml($subject, $content), 'client_reply_notification');
    }

    /**
     * Send secure document share notification to verified client
     */
    public static function sendDocumentShareNotification(
        string $toEmail,
        string $clientName,
        string $memberName,
        string $docTitle,
        string $shareUrl,
        string $expiresAt
    ): array {
        $subject = 'Dokumen Dibagikan: ' . $docTitle . ' oleh ' . $memberName;
        $content = <<<HTML
<h2 style="font-size: 18px; color: #0f172a; margin-top: 0;">Halo, {$clientName}!</h2>
<p>Member <strong>{$memberName}</strong> telah membagikan dokumen profesional kepada Anda:</p>
<div class="info-box">
    <strong>Dokumen:</strong> {$docTitle}<br>
    <strong>Pemilik Dokumen:</strong> {$memberName}<br>
    <strong>Berlaku Hingga:</strong> {$expiresAt}
</div>
<p>Dokumen ini dilindungi secara terenkripsi dan hanya dapat diakses melalui tautan khusus di bawah ini:</p>
<div style="text-align: center;">
    <a href="{$shareUrl}" class="button">Akses & Unduh Dokumen</a>
</div>
<div class="info-box" style="font-size: 12px; color: #64748b;">
    <strong>Perhatian:</strong> Tautan ini bersifat privat untuk Anda. Akses akan ditolak otomatis setelah tanggal kedaluwarsa atau jika dicabut oleh pemilik dokumen.
</div>
HTML;

        return self::sendMail($toEmail, $subject, self::wrapHtml($subject, $content), 'document_share_notification');
    }

    /**
     * Alias for sendEventGuestVerification
     */
    public static function sendGuestVerificationEmail(
        string $toEmail,
        string $guestName,
        string $eventTitle,
        string $verifyUrl
    ): array {
        return self::sendEventGuestVerification($toEmail, $guestName, $eventTitle, $verifyUrl);
    }

    /**
     * Alias for sendEventTicketConfirmation
     */
    public static function sendTicketConfirmation(
        string $toEmail,
        string $participantName,
        string $eventTitle,
        string $regNumber,
        string $ticketUrl
    ): array {
        return self::sendEventTicketConfirmation($toEmail, $participantName, $eventTitle, $regNumber, $ticketUrl);
    }

    /**
     * Alias for sendClientInquiryVerification
     */
    public static function sendInquiryVerificationEmail(
        string $toEmail,
        string $clientName,
        string $memberName,
        string $verifyUrl
    ): array {
        return self::sendClientInquiryVerification($toEmail, $clientName, $memberName, $verifyUrl);
    }

    /**
     * Alias for sendMemberNewInquiryNotification
     */
    public static function sendNewInquiryNotification(
        string $toEmail,
        string $memberName,
        string $clientName,
        string $subjectText,
        string $inquiryType,
        string $inboxUrl
    ): array {
        return self::sendMemberNewInquiryNotification($toEmail, $memberName, $clientName, $subjectText, $inquiryType, $inboxUrl);
    }

    /**
     * Alias for sendClientReplyNotification
     */
    public static function sendMemberReplyNotification(
        string $toEmail,
        string $clientName,
        string $memberName,
        string $conversationUrl
    ): array {
        return self::sendClientReplyNotification($toEmail, $clientName, $memberName, $conversationUrl);
    }
}
