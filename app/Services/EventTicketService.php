<?php

namespace App\Services;

use App\Models\EventRegistrationModel;
use App\Services\SettingsService;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use TCPDF;

class EventTicketService
{
    /**
     * Generate QR code binary PNG stream for an event ticket token
     */
    public function generateQrCodeImage(string $ticketUrl): string
    {
        $options = new QROptions([
            'outputInterface' => QRGdImagePNG::class,
            'scale'           => 8,
            'outputBase64'    => false,
            'bgColor'         => [255, 255, 255],
            'imageTransparent'=> false,
        ]);

        return (new QRCode($options))->render($ticketUrl);
    }

    /**
     * Render high-resolution PNG ticket
     */
    public function renderTicketPng(array $registration): string
    {
        $width  = 900;
        $height = 540;

        $im = imagecreatetruecolor($width, $height);
        imagealphablending($im, true);
        imagesavealpha($im, false);

        // Color palette
        $bgLight     = imagecolorallocate($im, 248, 250, 252); // slate-50
        $cardBg      = imagecolorallocate($im, 255, 255, 255); // white
        $primary     = imagecolorallocate($im, 79, 70, 229);   // brand-600 #4f46e5
        $primaryDark = imagecolorallocate($im, 67, 56, 202);   // brand-700
        $textDark    = imagecolorallocate($im, 15, 23, 42);    // slate-900
        $textGray    = imagecolorallocate($im, 100, 116, 139); // slate-500
        $textMuted   = imagecolorallocate($im, 148, 163, 184); // slate-400
        $borderColor = imagecolorallocate($im, 226, 232, 240); // slate-200
        $accentGreen = imagecolorallocate($im, 16, 185, 129);  // emerald-500
        $white       = imagecolorallocate($im, 255, 255, 255);

        // Fill background
        imagefill($im, 0, 0, $bgLight);

        // Main ticket container
        imagefilledrectangle($im, 20, 20, $width - 20, $height - 20, $cardBg);
        imagerectangle($im, 20, 20, $width - 20, $height - 20, $borderColor);

        // Left banner / header strip
        imagefilledrectangle($im, 20, 20, 32, $height - 20, $primary);

        // Header Top: KOMEO.ID Logo & Badge
        $siteName = SettingsService::get('App.site_name', 'KOMEO.ID');
        imagestring($im, 5, 50, 35, strtoupper($siteName), $primary);
        imagestring($im, 2, 50, 55, 'KOMUNITAS EVENT ORGANIZER INDONESIA - TIKET ELEKTRONIK', $textGray);

        // Participant Type Badge
        $isMember = ($registration['participant_type'] ?? '') === 'member';
        $typeText = $isMember ? 'MEMBER KOMEO' : 'PESERTA UMUM';
        $badgeBg  = $isMember ? $primary : $accentGreen;
        imagefilledrectangle($im, 460, 35, 580, 58, $badgeBg);
        imagestring($im, 2, 470, 40, $typeText, $white);

        // Divider
        imageline($im, 50, 75, 580, 75, $borderColor);

        // Event Title & Details
        $eventTitle = substr($registration['event_title'] ?? 'Kegiatan KOMEO', 0, 48);
        imagestring($im, 5, 50, 95, $eventTitle, $textDark);

        // Event Date & Time
        $startDate = ! empty($registration['event_start_date']) 
            ? date('l, d F Y - H:i', strtotime($registration['event_start_date'])) . ' WIB'
            : '-';
        imagestring($im, 3, 50, 130, 'Waktu : ' . $startDate, $textDark);

        // Venue
        $venue = substr(($registration['venue_name'] ?? 'Online') . ', ' . ($registration['event_city'] ?? ''), 0, 50);
        imagestring($im, 3, 50, 155, 'Lokasi: ' . $venue, $textDark);

        // Inner Divider
        imageline($im, 50, 190, 580, 190, $borderColor);

        // Participant Information
        imagestring($im, 2, 50, 210, 'NAMA PESERTA', $textMuted);
        $pName = substr($registration['participant_name'] ?? 'Peserta', 0, 38);
        imagestring($im, 5, 50, 228, strtoupper($pName), $textDark);

        imagestring($im, 2, 50, 265, 'NOMOR REGISTRASI', $textMuted);
        imagestring($im, 4, 50, 283, $registration['registration_number'] ?? '-', $primaryDark);

        if ($isMember && ! empty($registration['membership_number'])) {
            imagestring($im, 2, 300, 265, 'NOMOR ANGGOTA', $textMuted);
            imagestring($im, 4, 300, 283, $registration['membership_number'], $textDark);
        } else {
            imagestring($im, 2, 300, 265, 'INSTANSI / PERUSAHAAN', $textMuted);
            imagestring($im, 4, 300, 283, substr($registration['company'] ?? '-', 0, 25), $textDark);
        }

        imagestring($im, 2, 50, 320, 'STATUS TIKET', $textMuted);
        $statusText = strtoupper($registration['status'] ?? 'CONFIRMED');
        imagestring($im, 4, 50, 338, $statusText, $accentGreen);

        // Terms / notice footer
        imagestring($im, 2, 50, 420, '* Tunjukkan kode QR tiket ini kepada panitia saat kedatangan.', $textGray);
        imagestring($im, 2, 50, 440, '* Tiket ini berlaku untuk 1 (satu) kali check-in pada pintu masuk.', $textGray);
        imagestring($im, 2, 50, 460, '* Diterbitkan secara resmi oleh sistem terpadu KOMEO.ID.', $textMuted);

        // Perforation dotted line separating QR Ticket Stub
        for ($y = 30; $y < $height - 30; $y += 10) {
            imagefilledellipse($im, 610, $y, 3, 3, $borderColor);
        }

        // RIGHT STUB: QR Code Container
        imagefilledrectangle($im, 630, 40, $width - 40, $height - 40, imagecolorallocate($im, 241, 245, 249));
        imagerectangle($im, 630, 40, $width - 40, $height - 40, $borderColor);

        imagestring($im, 3, 675, 55, 'SCAN DI SINI', $textDark);
        imagestring($im, 2, 655, 75, 'Validasi Check-in', $textGray);

        // Generate QR code and stamp it
        $tokenSelector = $registration['ticket_token_selector'] ?? '';
        $tokenUrl = site_url('kegiatan/tiket/' . $tokenSelector);
        $qrRawPng = $this->generateQrCodeImage($tokenUrl);
        $qrIm = imagecreatefromstring($qrRawPng);

        if ($qrIm) {
            $qrSize = 190;
            imagecopyresampled($im, $qrIm, 640, 110, 0, 0, $qrSize, $qrSize, imagesx($qrIm), imagesy($qrIm));
            imagedestroy($qrIm);
        }

        imagestring($im, 2, 650, 320, 'Kode: ' . substr($registration['registration_number'] ?? '', 0, 20), $textDark);
        imagestring($im, 2, 645, 345, 'Waktu Cetak: ' . date('d/m/Y H:i'), $textMuted);

        // Watermark verification note
        imagestring($im, 2, 640, 420, 'Verifikasi Resmi KOMEO', $primary);

        ob_start();
        imagepng($im);
        $pngData = ob_get_clean();
        imagedestroy($im);

        return $pngData;
    }

    /**
     * Render PDF ticket using TCPDF
     */
    public function renderTicketPdf(array $registration): string
    {
        $pdf = new TCPDF('L', 'mm', [210, 120], true, 'UTF-8', false);
        $pdf->SetCreator('KOMEO.ID');
        $pdf->SetAuthor('KOMEO.ID');
        $pdf->SetTitle('Tiket Elektronik - ' . ($registration['event_title'] ?? 'Kegiatan'));
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();

        // Render PNG and embed in PDF
        $pngStream = $this->renderTicketPng($registration);
        $tempPath = WRITEPATH . 'cache/ticket_' . ($registration['id'] ?? uniqid()) . '.png';
        file_put_contents($tempPath, $pngStream);

        $pdf->Image($tempPath, 5, 5, 200, 110, 'PNG');
        @unlink($tempPath);

        return $pdf->Output('tiket.pdf', 'S');
    }
}
