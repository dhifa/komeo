<?php

namespace App\Services\Kta;

use App\Models\EventCategoryModel;
use App\Models\MemberProfileModel;
use App\Models\MembershipModel;
use App\Services\MembershipVerificationService;
use App\Services\SettingsService;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class CardImageRenderer
{
    public const CARD_WIDTH  = 1011; // 300 DPI for 85.60 mm
    public const CARD_HEIGHT = 638;  // 300 DPI for 53.98 mm

    /**
     * Prepare complete member data dictionary for rendering
     */
    public static function prepareMemberCardData(int $membershipId): array
    {
        $membershipModel = model(MembershipModel::class);
        $membership = $membershipModel->find($membershipId);
        if (! $membership) {
            throw new \RuntimeException('Data keanggotaan tidak ditemukan.');
        }

        $profileModel  = model(MemberProfileModel::class);
        $categoryModel = model(EventCategoryModel::class);

        $profile  = $profileModel->findByUserId((int) $membership->user_id);
        $category = ($profile && ! empty($profile->category_id)) ? $categoryModel->find($profile->category_id) : null;

        $verificationData = MembershipVerificationService::getOrCreateToken($membershipId);

        // Resolve local avatar file path if available (robust search across relative & absolute paths)
        $avatarPath = null;
        $rawPhoto = $profile ? ($profile->photo_path ?? $profile->avatar ?? '') : '';
        if (! empty($rawPhoto)) {
            $rawClean = ltrim($rawPhoto, '/\\');
            $candidates = [
                FCPATH . $rawClean,
                FCPATH . 'uploads/avatars/' . basename($rawClean),
                FCPATH . 'uploads/' . basename($rawClean),
                $rawClean,
            ];
            foreach ($candidates as $cand) {
                if (file_exists($cand) && ! is_dir($cand)) {
                    $avatarPath = $cand;
                    break;
                }
            }
        }

        $fullName    = $profile ? ($profile->full_name ?: $profile->display_name) : 'Member KOMEO';
        $displayName = $profile ? ($profile->display_name ?: $fullName) : $fullName;
        $joinYear    = $membership->joined_at ? date('Y', strtotime((string) $membership->joined_at)) : date('Y');

        return [
            'membership_id'      => (int) $membership->id,
            'user_id'            => (int) $membership->user_id,
            'full_name'          => $fullName,
            'display_name'       => $displayName,
            'username'           => $profile ? $profile->username : '',
            'member_number'      => $membership->member_number ?: 'KMO-' . $joinYear . '-000000',
            'member_type'        => $profile ? $profile->member_type : 'individual',
            'business_name'      => ($profile && $profile->isBusiness()) ? $profile->business_name : null,
            'category_name'      => $category ? $category['name'] : 'Event Professional',
            'city'               => $profile ? ($profile->city ?: 'Indonesia') : 'Indonesia',
            'joined_year'        => $joinYear,
            'approved_date'      => $membership->getFormattedApprovalDate() ?: '-',
            'status'             => $membership->status,
            'avatar_path'        => $avatarPath,
            'verification_token' => $verificationData['token'],
            'verification_url'   => $verificationData['url'],
        ];
    }

    /**
     * Render the FRONT of the card as a GD Image
     */
    public static function renderFrontImage(array $data, array $customSettings = []): \GdImage
    {
        $settings = array_merge(SettingsService::getKtaSettings(), $customSettings);
        $w = self::CARD_WIDTH;
        $h = self::CARD_HEIGHT;

        $im = imagecreatetruecolor($w, $h);
        imagesavealpha($im, true);
        imagealphablending($im, true);

        // Colors
        $bgColor     = self::parseColor($settings['front_bg_color'] ?? '#0F172A');
        $primaryCol  = self::parseColor($settings['primary_color'] ?? '#6366F1');
        $accentCol   = self::parseColor($settings['accent_color'] ?? '#818CF8');
        $textCol     = self::parseColor($settings['text_color'] ?? '#FFFFFF');
        $textSecCol  = self::parseColor($settings['text_secondary_color'] ?? '#94A3B8');

        $cBg        = imagecolorallocate($im, $bgColor[0], $bgColor[1], $bgColor[2]);
        $cPrimary   = imagecolorallocate($im, $primaryCol[0], $primaryCol[1], $primaryCol[2]);
        $cAccent    = imagecolorallocate($im, $accentCol[0], $accentCol[1], $accentCol[2]);
        $cText      = imagecolorallocate($im, $textCol[0], $textCol[1], $textCol[2]);
        $cTextSec   = imagecolorallocate($im, $textSecCol[0], $textSecCol[1], $textSecCol[2]);
        $cWhite     = imagecolorallocate($im, 255, 255, 255);

        // Fill Base Background
        imagefilledrectangle($im, 0, 0, $w, $h, $cBg);

        // Check if custom front background artwork exists
        $customBgApplied = false;
        $frontBgName = $settings['bg_pattern_front'] ?? $settings['front_bg_image'] ?? '';
        if (! empty($frontBgName)) {
            $candidates = [
                FCPATH . ltrim($frontBgName, '/\\'),
                FCPATH . 'uploads/kta/' . basename($frontBgName),
                FCPATH . 'uploads/settings/' . basename($frontBgName),
            ];
            foreach ($candidates as $cand) {
                if (file_exists($cand) && ! is_dir($cand)) {
                    $bgRes = @imagecreatefromstring((string) file_get_contents($cand));
                    if ($bgRes) {
                        imagecopyresampled($im, $bgRes, 0, 0, 0, 0, $w, $h, imagesx($bgRes), imagesy($bgRes));
                        imagedestroy($bgRes);
                        $customBgApplied = true;
                        break;
                    }
                }
            }
        }

        $hideDefaultShapes = ($settings['hide_default_shapes'] ?? '0') === '1';

        // Draw default background design elements if no custom background OR if not hidden
        if (! $customBgApplied && ! $hideDefaultShapes) {
            // Gradient / Geometric overlay shapes
            for ($i = 0; $i < 6; $i++) {
                $radColor = imagecolorallocatealpha($im, $primaryCol[0], $primaryCol[1], $primaryCol[2], 120 - ($i * 12));
                imagefilledellipse($im, $w + 50, -50, 400 + ($i * 70), 400 + ($i * 70), $radColor);
            }
            for ($i = 0; $i < 4; $i++) {
                $arcColor = imagecolorallocatealpha($im, $accentCol[0], $accentCol[1], $accentCol[2], 122 - ($i * 6));
                imagearc($im, -50, $h + 50, 600 + ($i * 60), 600 + ($i * 60), 270, 360, $arcColor);
            }

            // Decorative top-right metallic gradient ribbon
            imagefilledpolygon($im, [
                $w - 320, 0,
                $w, 0,
                $w, 140,
                $w - 200, 140,
            ], imagecolorallocatealpha($im, $primaryCol[0], $primaryCol[1], $primaryCol[2], 115));
        }

        // --- 1. HEADER SECTION ---
        $logoX = (int) ($settings['pos_logo_x'] ?? 45);
        $logoY = (int) ($settings['pos_logo_y'] ?? 38);

        // Logo image or Shield Emblem
        $logoLoaded = false;
        $logoPath = $settings['logo'] ?? $settings['logo_path'] ?? '';
        if (! empty($logoPath)) {
            $logoCands = [
                FCPATH . ltrim($logoPath, '/\\'),
                FCPATH . 'uploads/kta/' . basename($logoPath),
                FCPATH . 'uploads/settings/' . basename($logoPath),
            ];
            foreach ($logoCands as $lc) {
                if (file_exists($lc) && ! is_dir($lc)) {
                    $logoImg = @imagecreatefromstring((string) file_get_contents($lc));
                    if ($logoImg) {
                        // Preserve logo aspect ratio — fit within 46×46 bounding box
                        $logoSrcW = imagesx($logoImg);
                        $logoSrcH = imagesy($logoImg);
                        $logoMaxSize = 46;
                        if ($logoSrcW >= $logoSrcH) {
                            $logoDstW = $logoMaxSize;
                            $logoDstH = (int) round($logoSrcH * ($logoMaxSize / $logoSrcW));
                        } else {
                            $logoDstH = $logoMaxSize;
                            $logoDstW = (int) round($logoSrcW * ($logoMaxSize / $logoSrcH));
                        }
                        // Center vertically within the 46px slot
                        $logoOffsetY = (int) (($logoMaxSize - $logoDstH) / 2);
                        $logoOffsetX = (int) (($logoMaxSize - $logoDstW) / 2);
                        imagecopyresampled($im, $logoImg, $logoX + $logoOffsetX, $logoY + $logoOffsetY, 0, 0, $logoDstW, $logoDstH, $logoSrcW, $logoSrcH);
                        imagedestroy($logoImg);
                        $logoLoaded = true;
                        break;
                    }
                }
            }
        }

        if (! $logoLoaded) {
            // Draw KOMEO Shield Emblem
            $emblemW = 46;
            $emblemH = 46;
            imagefilledellipse($im, $logoX + 23, $logoY + 23, $emblemW, $emblemH, $cPrimary);
            self::drawText($im, $logoX + 13, $logoY + 32, 'K', 20, $cWhite, true);
        }

        // Brand Text
        self::drawText($im, $logoX + 58, $logoY + 22, 'KOMEO.ID', 17, $cWhite, true);
        self::drawText($im, $logoX + 58, $logoY + 39, 'KOMUNITAS EVENT ORGANIZER INDONESIA', 8.5, $cTextSec, false);

        // Right Header: Card Title
        $cardTitle = strtoupper($settings['card_title'] ?? 'KARTU TANDA ANGGOTA');
        $titleX = isset($settings['pos_title_x']) && (int) $settings['pos_title_x'] > 0
            ? (int) $settings['pos_title_x']
            : ($w - 45 - self::textWidth($cardTitle, 13, true));
        $titleY = isset($settings['pos_title_y']) && (int) $settings['pos_title_y'] > 0
            ? (int) $settings['pos_title_y']
            : ($logoY + 22);

        self::drawText($im, $titleX, $titleY, $cardTitle, 13, $cAccent, true);

        // Status Badge Pill (if enabled)
        if (($settings['show_status_badge'] ?? '1') !== '0') {
            $statusText = ($data['status'] === 'active') ? 'ANGGOTA AKTIF' : strtoupper($data['status']);
            $badgeBg = ($data['status'] === 'active')
                ? imagecolorallocate($im, 16, 185, 129) // emerald-500
                : imagecolorallocate($im, 239, 68, 68);  // red-500
            $badgeW = 125;
            $badgeH = 26;
            $badgeX = $w - 45 - $badgeW;
            $badgeY = $logoY + 32;
            self::drawRoundedRect($im, $badgeX, $badgeY, $badgeW, $badgeH, 13, $badgeBg);
            self::drawText($im, $badgeX + 16, $badgeY + 18, $statusText, 8.5, $cWhite, true);
        }

        // Subtle Header Divider Line
        if (! $customBgApplied && ! $hideDefaultShapes) {
            imageline($im, 45, 98, $w - 45, 98, imagecolorallocatealpha($im, 255, 255, 255, 105));
        }

        // --- 2. MEMBER PHOTO SECTION ---
        $photoX      = (int) ($settings['pos_photo_x'] ?? 45);
        $photoY      = (int) ($settings['pos_photo_y'] ?? 125);
        $photoW      = (int) ($settings['pos_photo_w'] ?? 210);
        $photoH      = (int) ($settings['pos_photo_h'] ?? 270);
        $photoShape  = $settings['photo_shape'] ?? 'rounded';
        $photoRadius = ($photoShape === 'square') ? 0 : (($photoShape === 'circle' || $photoShape === 'oval') ? (int) ($photoW / 2) : 18);

        // Photo Frame Container Background
        if ($photoShape === 'circle' || $photoShape === 'oval') {
            imagefilledellipse($im, $photoX + (int) ($photoW / 2), $photoY + (int) ($photoH / 2), $photoW + 8, $photoH + 8, $cPrimary);
            imagefilledellipse($im, $photoX + (int) ($photoW / 2), $photoY + (int) ($photoH / 2), $photoW, $photoH, $cBg);
        } else {
            self::drawRoundedRect($im, $photoX - 4, $photoY - 4, $photoW + 8, $photoH + 8, $photoRadius + 4, $cPrimary);
            self::drawRoundedRect($im, $photoX, $photoY, $photoW, $photoH, $photoRadius, $cBg);
        }

        // Draw Member Photo or Stylized Fallback Avatar
        if (! empty($data['avatar_path']) && file_exists($data['avatar_path'])) {
            self::drawCroppedImage($im, $data['avatar_path'], $photoX, $photoY, $photoW, $photoH, $photoRadius, $photoShape);
        } else {
            // Stylized placeholder avatar
            imagefilledellipse($im, $photoX + ($photoW / 2), $photoY + ($photoH * 0.35), (int) ($photoW * 0.48), (int) ($photoW * 0.48), $cAccent);
            imagefilledarc($im, $photoX + ($photoW / 2), $photoY + ($photoH * 0.85), (int) ($photoW * 0.82), (int) ($photoH * 0.55), 180, 360, $cPrimary, IMG_ARC_PIE);
            // Member Monogram Initials
            $initials = strtoupper(substr($data['full_name'], 0, 1));
            self::drawText($im, $photoX + ($photoW / 2) - 16, $photoY + ($photoH * 0.42), $initials, 40, $cWhite, true);
        }

        // --- 3. MEMBER DETAILS SECTION ---
        $nameX        = (int) ($settings['pos_name_x'] ?? 285);
        $nameY        = (int) ($settings['pos_name_y'] ?? 179);
        $nameFontSize = (float) ($settings['pos_name_size'] ?? 22);

        // Field 1: Nama Anggota
        self::drawText($im, $nameX, $nameY - 34, 'NAMA ANGGOTA', 9.5, $cTextSec, false);
        $name = $data['full_name'];
        if (strlen($name) > 30 && $nameFontSize > 16) {
            $nameFontSize = 16;
        } elseif (strlen($name) > 22 && $nameFontSize > 18) {
            $nameFontSize = 18;
        }
        self::drawText($im, $nameX, $nameY, $name, $nameFontSize, $cWhite, true);

        // Business name if available
        if (! empty($data['business_name'])) {
            self::drawText($im, $nameX, $nameY + 26, strtoupper($data['business_name']), 11.5, $cAccent, true);
        }

        // Field 2: Kategori Spesialisasi
        $catX = (int) ($settings['pos_category_x'] ?? 285);
        $catY = (int) ($settings['pos_category_y'] ?? 245);
        self::drawText($im, $catX, $catY - 26, 'KATEGORI SPESIALISASI', 9.5, $cTextSec, false);
        $categoryName = strtoupper($data['category_name'] ?: 'EVENT PROFESSIONAL');
        self::drawText($im, $catX, $catY, $categoryName, 13, $cWhite, true);

        // Field 3: Nomor Anggota Shape / Badge
        $numX = (int) ($settings['pos_number_x'] ?? 285);
        $numY = (int) ($settings['pos_number_y'] ?? 325);
        $numberStyle = $settings['number_style'] ?? 'gold_badge';

        self::drawMemberNumberBadge($im, $numX, $numY, $data['member_number'], $numberStyle, $primaryCol, $accentCol, $cWhite, $cTextSec);

        // Field 4: Metadata Row (Tahun Bergabung & Domisili)
        if (($settings['show_meta'] ?? '1') !== '0') {
            $metaX = (int) ($settings['pos_meta_x'] ?? 285);
            $metaY = (int) ($settings['pos_meta_y'] ?? 395);
            self::drawText($im, $metaX, $metaY - 22, 'BERGABUNG SEJAK', 9, $cTextSec, false);
            self::drawText($im, $metaX + 160, $metaY - 22, 'DOMISILI', 9, $cTextSec, false);
            self::drawText($im, $metaX, $metaY, $data['joined_year'], 13, $cWhite, true);
            self::drawText($im, $metaX + 160, $metaY, strtoupper($data['city']), 12, $cWhite, true);
        }

        // --- 4. SECURE QR CODE ---
        $qrSize = (int) ($settings['pos_qr_size'] ?? $settings['qr_size'] ?? 190);
        $qrBoxW = $qrSize + 20;
        $qrBoxH = $qrSize + 20;
        $qrX    = (int) ($settings['pos_qr_x'] ?? ($w - 45 - $qrBoxW));
        $qrY    = (int) ($settings['pos_qr_y'] ?? ($h - 55 - $qrBoxH));

        // White card container with rounded corners and safe quiet zone
        self::drawRoundedRect($im, $qrX, $qrY, $qrBoxW, $qrBoxH, 16, $cWhite);

        // Render QR Code
        $qrData = $data['verification_url'] ?? base_url();
        $qrImage = self::generateQrImage($qrData, $qrSize);
        if ($qrImage) {
            imagecopyresampled($im, $qrImage, $qrX + 10, $qrY + 10, 0, 0, $qrSize, $qrSize, imagesx($qrImage), imagesy($qrImage));
            imagedestroy($qrImage);
        }

        // Caption below QR Box
        $qrCaption = 'PINDAI VERIFIKASI RESMI';
        self::drawText($im, $qrX + 16, $qrY + $qrBoxH + 22, $qrCaption, 8, $cTextSec, false);

        // --- 5. BOTTOM METALLIC ACCENT BAR (if default shapes not hidden) ---
        if (! $customBgApplied && ! $hideDefaultShapes) {
            imagefilledrectangle($im, 0, $h - 10, $w, $h, $cPrimary);
            imagefilledrectangle($im, 0, $h - 5, (int) ($w / 2), $h, $cAccent);
        }

        return $im;
    }

    /**
     * Draw Member Number Badge with Luxury / Modern Shapes
     */
    public static function drawMemberNumberBadge(
        \GdImage $im,
        int $x,
        int $y,
        string $number,
        string $style,
        array $primaryCol,
        array $accentCol,
        int $cWhite,
        int $cTextSec
    ): void {
        $w = 285;
        $h = 50;
        $boxY = $y - 34;

        if ($style === 'gold_badge') {
            // Metallic Gold Luxury Badge with Chip Icon
            $cGoldOuter  = imagecolorallocate($im, 217, 119, 6);   // amber-600
            $cGoldInner  = imagecolorallocate($im, 245, 158, 11);  // amber-500
            $cGoldBg     = imagecolorallocatealpha($im, 15, 23, 42, 35); // dark glass
            $cGoldText   = imagecolorallocate($im, 254, 243, 199); // amber-100
            $cGoldAccent = imagecolorallocate($im, 251, 191, 36);  // amber-400

            // Base glass box
            self::drawRoundedRect($im, $x, $boxY, $w, $h, 12, $cGoldBg);
            self::drawRoundedRectOutline($im, $x, $boxY, $w, $h, 12, $cGoldOuter, 2);
            self::drawRoundedRectOutline($im, $x + 2, $boxY + 2, $w - 4, $h - 4, 10, $cGoldInner, 1);

            // Left: Mini Luxury Security Chip / Shield Icon
            $chipX = $x + 12;
            $chipY = $boxY + 12;
            imagefilledrectangle($im, $chipX, $chipY, $chipX + 26, $chipY + 26, $cGoldOuter);
            imagefilledrectangle($im, $chipX + 2, $chipY + 2, $chipX + 24, $chipY + 24, $cGoldAccent);
            // Chip circuit lines
            imageline($im, $chipX + 6, $chipY + 6, $chipX + 20, $chipY + 6, $cGoldOuter);
            imageline($im, $chipX + 6, $chipY + 13, $chipX + 20, $chipY + 13, $cGoldOuter);
            imageline($im, $chipX + 6, $chipY + 20, $chipX + 20, $chipY + 20, $cGoldOuter);

            // Micro-label & Number
            self::drawText($im, $x + 48, $boxY + 17, 'ID RESMI ANGGOTA', 7.5, $cGoldAccent, true);
            self::drawText($im, $x + 48, $boxY + 38, $number, 14.5, $cGoldText, true);

        } elseif ($style === 'glass_pill') {
            // Frosted Translucent Pill Badge with Glowing Bullet Dot
            $cPillBg   = imagecolorallocatealpha($im, 255, 255, 255, 115);
            $cBorder   = imagecolorallocate($im, $accentCol[0], $accentCol[1], $accentCol[2]);
            $cDotGlow  = imagecolorallocate($im, 6, 182, 212); // cyan-500

            self::drawRoundedRect($im, $x, $boxY, $w, $h, 24, $cPillBg);
            self::drawRoundedRectOutline($im, $x, $boxY, $w, $h, 24, $cBorder, 2);

            // Glowing pulse dot
            imagefilledellipse($im, $x + 24, $boxY + 25, 14, 14, $cDotGlow);
            imagefilledellipse($im, $x + 24, $boxY + 25, 8, 8, $cWhite);

            self::drawText($im, $x + 44, $boxY + 18, 'NO. ANGGOTA RESMI', 7.5, $cBorder, true);
            self::drawText($im, $x + 44, $boxY + 38, $number, 14.5, $cWhite, true);

        } elseif ($style === 'neon_cyan') {
            // High-Tech Cyber Neon Cyan Badge
            $cCyan = imagecolorallocate($im, 6, 182, 212);
            $cDark = imagecolorallocatealpha($im, 8, 47, 73, 50);

            self::drawRoundedRect($im, $x, $boxY, $w, $h, 10, $cDark);
            self::drawRoundedRectOutline($im, $x, $boxY, $w, $h, 10, $cCyan, 2);

            // Tech brackets
            imageline($im, $x + 6, $boxY + 6, $x + 16, $boxY + 6, $cWhite);
            imageline($im, $x + 6, $boxY + 6, $x + 6, $boxY + 16, $cWhite);
            imageline($im, $x + $w - 6, $boxY + $h - 6, $x + $w - 16, $boxY + $h - 6, $cWhite);
            imageline($im, $x + $w - 6, $boxY + $h - 6, $x + $w - 6, $boxY + $h - 16, $cWhite);

            self::drawText($im, $x + 20, $boxY + 33, $number, 15, $cWhite, true);

        } elseif ($style === 'modern_slate') {
            // Deep Charcoal Slate with 1px border
            $cSlateBg     = imagecolorallocatealpha($im, 30, 41, 59, 30);
            $cSlateBorder = imagecolorallocatealpha($im, 148, 163, 184, 80);

            self::drawRoundedRect($im, $x, $boxY, $w, $h, 12, $cSlateBg);
            self::drawRoundedRectOutline($im, $x, $boxY, $w, $h, 12, $cSlateBorder, 1);

            self::drawText($im, $x + 18, $boxY + 17, 'NO. ANGGOTA', 7.5, $cTextSec, false);
            self::drawText($im, $x + 18, $boxY + 38, $number, 14.5, $cWhite, true);

        } else {
            // Minimal Outline
            $cBorder = imagecolorallocate($im, $primaryCol[0], $primaryCol[1], $primaryCol[2]);
            self::drawRoundedRectOutline($im, $x, $boxY, $w, $h, 10, $cBorder, 2);
            self::drawText($im, $x + 16, $boxY + 33, $number, 15, $cWhite, true);
        }
    }

    /**
     * Render the BACK of the card as a GD Image (Fully customizable)
     */
    public static function renderBackImage(array $data, array $customSettings = []): \GdImage
    {
        $settings = array_merge(SettingsService::getKtaSettings(), $customSettings);
        $w = self::CARD_WIDTH;
        $h = self::CARD_HEIGHT;

        $im = imagecreatetruecolor($w, $h);
        imagesavealpha($im, true);
        imagealphablending($im, true);

        $bgColor    = self::parseColor($settings['back_bg_color'] ?? '#0B1120');
        $primaryCol = self::parseColor($settings['primary_color'] ?? '#6366F1');
        $accentCol  = self::parseColor($settings['accent_color'] ?? '#818CF8');
        $textCol    = self::parseColor($settings['text_color_back'] ?? $settings['text_color'] ?? '#FFFFFF');
        $textSecCol = self::parseColor($settings['text_secondary_color'] ?? '#94A3B8');

        $cBg        = imagecolorallocate($im, $bgColor[0], $bgColor[1], $bgColor[2]);
        $cPrimary   = imagecolorallocate($im, $primaryCol[0], $primaryCol[1], $primaryCol[2]);
        $cAccent    = imagecolorallocate($im, $accentCol[0], $accentCol[1], $accentCol[2]);
        $cText      = imagecolorallocate($im, $textCol[0], $textCol[1], $textCol[2]);
        $cTextSec   = imagecolorallocate($im, $textSecCol[0], $textSecCol[1], $textSecCol[2]);
        $cWhite     = imagecolorallocate($im, 255, 255, 255);

        // Base background
        imagefilledrectangle($im, 0, 0, $w, $h, $cBg);

        // Custom back background image if provided
        $customBgApplied = false;
        $backBgName = $settings['bg_pattern_back'] ?? $settings['back_bg_image'] ?? '';
        if (! empty($backBgName)) {
            $candidates = [
                FCPATH . ltrim($backBgName, '/\\'),
                FCPATH . 'uploads/kta/' . basename($backBgName),
                FCPATH . 'uploads/settings/' . basename($backBgName),
            ];
            foreach ($candidates as $cand) {
                if (file_exists($cand) && ! is_dir($cand)) {
                    $bgRes = @imagecreatefromstring((string) file_get_contents($cand));
                    if ($bgRes) {
                        imagecopyresampled($im, $bgRes, 0, 0, 0, 0, $w, $h, imagesx($bgRes), imagesy($bgRes));
                        imagedestroy($bgRes);
                        $customBgApplied = true;
                        break;
                    }
                }
            }
        }

        $hideDefaultShapes = ($settings['hide_default_shapes'] ?? '0') === '1';

        if (! $customBgApplied && ! $hideDefaultShapes) {
            // Subtle watermark badge in background
            $watermarkColor = imagecolorallocatealpha($im, 255, 255, 255, 123);
            imagearc($im, (int) ($w / 2), (int) ($h / 2), 500, 500, 0, 360, $watermarkColor);
            imagearc($im, (int) ($w / 2), (int) ($h / 2), 450, 450, 0, 360, $watermarkColor);
        }

        // --- TOP HEADER (Customizable) ---
        $topY = 46;
        $backHeaderTitle    = $settings['back_header_title'] ?? 'KOMEO.ID';
        $backHeaderSubtitle = $settings['back_header_subtitle'] ?? 'KOMUNITAS EVENT ORGANIZER INDONESIA';
        $backBadgeText      = $settings['back_badge_text'] ?? 'IDENTITAS RESMI KEANGGOTAAN';

        self::drawText($im, 55, $topY, $backHeaderTitle, 20, $cWhite, true);
        self::drawText($im, 55, $topY + 24, $backHeaderSubtitle, 9, $cTextSec, false);

        // Official Card Tag
        self::drawText($im, $w - 55 - self::textWidth($backBadgeText, 10, true), $topY + 12, $backBadgeText, 10, $cAccent, true);

        // Divider
        if (! $customBgApplied && ! $hideDefaultShapes) {
            imageline($im, 55, $topY + 44, $w - 55, $topY + 44, imagecolorallocatealpha($im, 255, 255, 255, 110));
        }

        // --- TAGLINE QUOTE ---
        $tagline = '"' . ($settings['tagline'] ?? 'Satu Komunitas, Ribuan Peluang Kolaborasi.') . '"';
        self::drawText($im, 55, $topY + 84, $tagline, 15, $cAccent, true);

        // --- COMMUNITY STATEMENT / TERMS (Customizable) ---
        $cardBoxX = 55;
        $cardBoxY = $topY + 115;
        $cardBoxW = $w - 110;
        $cardBoxH = 265;

        // Card Container Box
        self::drawRoundedRect($im, $cardBoxX, $cardBoxY, $cardBoxW, $cardBoxH, 16, imagecolorallocatealpha($im, 255, 255, 255, 122));

        // Statement Text
        $message = $settings['back_statement'] ?? $settings['back_message'] ?? 'Kartu ini merupakan tanda keanggotaan resmi KOMEO.ID. Keabsahan keanggotaan dapat diverifikasi secara langsung melalui pemindaian QR Code pada bagian depan kartu.';
        self::drawWrappedText($im, $cardBoxX + 28, $cardBoxY + 36, $message, $cardBoxW - 56, 11, $cWhite, 22);

        // Rules & Guidelines Section
        $rulesTitle = $settings['back_rules_title'] ?? 'KETENTUAN PENGGUNAAN KARTU:';
        $rulesY = $cardBoxY + 115;
        self::drawText($im, $cardBoxX + 28, $rulesY, $rulesTitle, 10, $cAccent, true);

        $rulesRaw = $settings['back_rules_text'] ?? "1. Kartu ini hanya berlaku bagi anggota yang terdaftar resmi dan berstatus aktif di KOMEO.ID.\n2. Kartu ini tidak dapat dipindahtangankan, digandakan, atau dipinjamkan kepada pihak mana pun.\n3. Anggota wajib menjunjung tinggi etika profesi dan integritas industri event Indonesia.\n4. Apabila keanggotaan ditangguhkan atau dicabut, hak kepemilikan dan verifikasi otomatis gugur.";
        $ruleLines = array_filter(array_map('trim', explode("\n", (string) $rulesRaw)));

        $rOffY = $rulesY + 26;
        $lineCount = 0;
        foreach ($ruleLines as $rule) {
            if ($lineCount++ >= 4) {
                break; // keep within card box
            }
            self::drawText($im, $cardBoxX + 28, $rOffY, $rule, 9.5, $cTextSec, false);
            $rOffY += 24;
        }

        // Optional Signature Box (if enabled)
        if (($settings['back_show_sign'] ?? '0') === '1') {
            $signBoxW = 200;
            $signBoxX = $w - 55 - $signBoxW;
            $signBoxY = $cardBoxY + $cardBoxH - 85;
            $signTitle = $settings['back_sign_title'] ?? 'Dewan Pengurus';
            $signName  = $settings['back_sign_name'] ?? 'Ketua Umum';

            self::drawText($im, $signBoxX, $signBoxY, $signTitle, 8.5, $cTextSec, false);
            imageline($im, $signBoxX, $signBoxY + 45, $signBoxX + $signBoxW - 20, $signBoxY + 45, imagecolorallocatealpha($im, 255, 255, 255, 90));
            self::drawText($im, $signBoxX, $signBoxY + 62, $signName, 9.5, $cWhite, true);
        }

        // --- FOOTER INFORMATION (Customizable) ---
        $footY = $h - 85;
        $website = $settings['website_url'] ?? 'https://komeo.id';
        self::drawText($im, 55, $footY, 'PORTAL RESMI: ' . strtoupper($website), 10.5, $cWhite, true);

        $officeAddress = $settings['back_office_address'] ?? 'Sekretariat Pusat KOMEO.ID | Hak Cipta Dilindungi Undang-Undang';
        self::drawText($im, 55, $footY + 22, $officeAddress, 8.5, $cTextSec, false);

        // Member serial number indicator
        $serial = 'ID REG: ' . ($data['member_number'] ?? 'KMO-2026-000000');
        self::drawText($im, $w - 55 - self::textWidth($serial, 10, true), $footY, $serial, 10, $cAccent, true);

        // Bottom Accent line
        if (! $customBgApplied && ! $hideDefaultShapes) {
            imagefilledrectangle($im, 0, $h - 8, $w, $h, $cPrimary);
        }

        // Handle 180 degree rotation if requested (duplex flip setting)
        if (($settings['back_orientation'] ?? 'normal') === 'rotate180' || ($settings['back_rotation'] ?? 0) === 180) {
            $rotated = imagerotate($im, 180, 0);
            imagedestroy($im);
            return $rotated;
        }

        return $im;
    }

    /**
     * Render front as PNG binary string
     */
    public static function renderFrontPngString(array $data, array $customSettings = []): string
    {
        $im = self::renderFrontImage($data, $customSettings);
        ob_start();
        imagepng($im, null, 8); // PNG compression 8
        $png = ob_get_clean();
        imagedestroy($im);
        return $png;
    }

    /**
     * Render back as PNG binary string
     */
    public static function renderBackPngString(array $data, array $customSettings = []): string
    {
        $im = self::renderBackImage($data, $customSettings);
        ob_start();
        imagepng($im, null, 8);
        $png = ob_get_clean();
        imagedestroy($im);
        return $png;
    }

    /**
     * Render front directly to file path
     */
    public static function renderFrontFile(array $data, string $savePath, array $customSettings = []): bool
    {
        $dir = dirname($savePath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $im = self::renderFrontImage($data, $customSettings);
        $res = imagepng($im, $savePath, 8);
        imagedestroy($im);
        return $res;
    }

    /**
     * Render back directly to file path
     */
    public static function renderBackFile(array $data, string $savePath, array $customSettings = []): bool
    {
        $dir = dirname($savePath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $im = self::renderBackImage($data, $customSettings);
        $res = imagepng($im, $savePath, 8);
        imagedestroy($im);
        return $res;
    }

    /**
     * Convenience methods for both static and instance calls
     */
    public function getMemberCardData(int $membershipId): array
    {
        return self::prepareMemberCardData($membershipId);
    }

    public static function renderFront(int|array $membershipOrData, array $customSettings = []): string|\GdImage
    {
        if (is_int($membershipOrData)) {
            $data = self::prepareMemberCardData($membershipOrData);
            return self::renderFrontPngString($data, $customSettings);
        }
        return self::renderFrontImage($membershipOrData, $customSettings);
    }

    public static function renderBack(int|array $membershipOrData, array $customSettings = []): string|\GdImage
    {
        if (is_int($membershipOrData)) {
            $data = self::prepareMemberCardData($membershipOrData);
            return self::renderBackPngString($data, $customSettings);
        }
        return self::renderBackImage($membershipOrData, $customSettings);
    }

    // -------------------------------------------------------------
    // INTERNAL GRAPHICS HELPERS
    // -------------------------------------------------------------

    /**
     * Generate QR code as GD resource
     */
    private static function generateQrImage(string $url, int $targetSize): ?\GdImage
    {
        try {
            $options = new QROptions([
                'outputInterface' => QRGdImagePNG::class,
                'outputBase64'    => false,
                'scale'           => 6,
                'margin'          => 1,
            ]);
            $qr = new QRCode($options);
            $pngData = $qr->render($url);
            return imagecreatefromstring($pngData);
        } catch (\Throwable $e) {
            log_message('error', 'QR Code generation failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Draw text using TrueType font if available or imagestring fallback
     */
    private static function drawText(\GdImage $im, int $x, int $y, string $text, float $sizePt, int $color, bool $bold = false): void
    {
        $fontPath = self::getFontPath($bold);
        if ($fontPath && function_exists('imagettftext')) {
            @imagettftext($im, $sizePt, 0, $x, $y, $color, $fontPath, $text);
        } else {
            // Fallback to internal bitmap font
            $fontIndex = $sizePt >= 18 ? 5 : ($sizePt >= 13 ? 4 : ($sizePt >= 10 ? 3 : 2));
            imagestring($im, $fontIndex, $x, $y - (int)($sizePt * 1.1), $text, $color);
        }
    }

    /**
     * Approximate text width in pixels
     */
    private static function textWidth(string $text, float $sizePt, bool $bold = false): int
    {
        $fontPath = self::getFontPath($bold);
        if ($fontPath && function_exists('imagettfbbox')) {
            $box = @imagettfbbox($sizePt, 0, $fontPath, $text);
            if ($box) {
                return abs($box[2] - $box[0]);
            }
        }
        return (int) (strlen($text) * ($sizePt * 0.75));
    }

    /**
     * Draw wrapped multi-line text
     */
    private static function drawWrappedText(\GdImage $im, int $x, int $y, string $text, int $maxWidth, float $sizePt, int $color, int $lineHeight): void
    {
        $words = explode(' ', $text);
        $currentLine = '';
        $currY = $y;

        foreach ($words as $word) {
            $testLine = $currentLine === '' ? $word : $currentLine . ' ' . $word;
            if (self::textWidth($testLine, $sizePt, false) > $maxWidth && $currentLine !== '') {
                self::drawText($im, $x, $currY, $currentLine, $sizePt, $color, false);
                $currY += $lineHeight;
                $currentLine = $word;
            } else {
                $currentLine = $testLine;
            }
        }

        if ($currentLine !== '') {
            self::drawText($im, $x, $currY, $currentLine, $sizePt, $color, false);
        }
    }

    /**
     * Get path to TrueType font
     */
    private static function getFontPath(bool $bold): ?string
    {
        static $fontCache = [];
        $key = $bold ? 'bold' : 'regular';
        if (isset($fontCache[$key])) {
            return $fontCache[$key];
        }

        $candidates = $bold
            ? [
                'C:\Windows\Fonts\segoeuib.ttf',
                'C:\Windows\Fonts\arialbd.ttf',
                '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
            ]
            : [
                'C:\Windows\Fonts\segoeui.ttf',
                'C:\Windows\Fonts\arial.ttf',
                '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
            ];

        foreach ($candidates as $candidate) {
            if (file_exists($candidate)) {
                $fontCache[$key] = $candidate;
                return $candidate;
            }
        }

        $fontCache[$key] = null;
        return null;
    }

    /**
     * Draw rounded rectangle filled with color
     */
    private static function drawRoundedRect(\GdImage $im, int $x, int $y, int $w, int $h, int $radius, int $color): void
    {
        if ($radius <= 0) {
            imagefilledrectangle($im, $x, $y, $x + $w, $y + $h, $color);
            return;
        }

        $radius = min($radius, (int) ($w / 2), (int) ($h / 2));

        // Body rectangles
        imagefilledrectangle($im, $x + $radius, $y, $x + $w - $radius, $y + $h, $color);
        imagefilledrectangle($im, $x, $y + $radius, $x + $w, $y + $h - $radius, $color);

        // 4 Corner arcs
        imagefilledellipse($im, $x + $radius, $y + $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($im, $x + $w - $radius, $y + $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($im, $x + $radius, $y + $h - $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($im, $x + $w - $radius, $y + $h - $radius, $radius * 2, $radius * 2, $color);
    }

    /**
     * Draw outline of rounded rectangle
     */
    private static function drawRoundedRectOutline(\GdImage $im, int $x, int $y, int $w, int $h, int $radius, int $color, int $thickness = 1): void
    {
        for ($t = 0; $t < $thickness; $t++) {
            $curX = $x + $t;
            $curY = $y + $t;
            $curW = $w - ($t * 2);
            $curH = $h - ($t * 2);
            $curR = max(1, $radius - $t);

            if ($curW <= 0 || $curH <= 0) {
                break;
            }

            // 4 straight edges
            imageline($im, $curX + $curR, $curY, $curX + $curW - $curR, $curY, $color);
            imageline($im, $curX + $curR, $curY + $curH, $curX + $curW - $curR, $curY + $curH, $color);
            imageline($im, $curX, $curY + $curR, $curX, $curY + $curH - $curR, $color);
            imageline($im, $curX + $curW, $curY + $curR, $curX + $curW, $curY + $curH - $curR, $color);

            // 4 corner arcs
            imagearc($im, $curX + $curR, $curY + $curR, $curR * 2, $curR * 2, 180, 270, $color);
            imagearc($im, $curX + $curW - $curR, $curY + $curR, $curR * 2, $curR * 2, 270, 360, $color);
            imagearc($im, $curX + $curR, $curY + $curH - $curR, $curR * 2, $curR * 2, 90, 180, $color);
            imagearc($im, $curX + $curW - $curR, $curY + $curH - $curR, $curR * 2, $curR * 2, 0, 90, $color);
        }
    }

    /**
     * Draw cropped image with rounded corners or circle into bounding box
     */
    private static function drawCroppedImage(\GdImage $im, string $srcPath, int $dstX, int $dstY, int $dstW, int $dstH, int $radius, string $shape = 'rounded'): void
    {
        $srcData = @file_get_contents($srcPath);
        if (! $srcData) {
            return;
        }
        $src = @imagecreatefromstring($srcData);
        if (! $src) {
            return;
        }

        $srcW = imagesx($src);
        $srcH = imagesy($src);

        // Calculate aspect fill crop
        $targetRatio = $dstW / $dstH;
        $srcRatio    = $srcW / $srcH;

        if ($srcRatio > $targetRatio) {
            // Source is wider -> crop horizontal sides
            $cropW = (int) ($srcH * $targetRatio);
            $cropH = $srcH;
            $cropX = (int) (($srcW - $cropW) / 2);
            $cropY = 0;
        } else {
            // Source is taller -> crop top/bottom
            $cropW = $srcW;
            $cropH = (int) ($srcW / $targetRatio);
            $cropX = 0;
            $cropY = (int) (($srcH - $cropH) / 4); // bias towards upper face
        }

        // Create temporary thumbnail with aspect-fill crop
        $thumb = imagecreatetruecolor($dstW, $dstH);
        imagesavealpha($thumb, true);
        $trans = imagecolorallocatealpha($thumb, 0, 0, 0, 127);
        imagefill($thumb, 0, 0, $trans);
        imagealphablending($thumb, true);
        imagecopyresampled($thumb, $src, 0, 0, $cropX, $cropY, $dstW, $dstH, $cropW, $cropH);

        if ($shape === 'square' && $radius <= 0) {
            // No masking needed — straight rectangle
            imagecopy($im, $thumb, $dstX, $dstY, 0, 0, $dstW, $dstH);
        } else {
            // Build shape mask (circle, rounded rectangle, or oval)
            $mask = imagecreatetruecolor($dstW, $dstH);
            imagesavealpha($mask, true);
            $maskTrans = imagecolorallocatealpha($mask, 0, 0, 0, 127);
            imagefill($mask, 0, 0, $maskTrans);
            $maskWhite = imagecolorallocate($mask, 255, 255, 255);

            if ($shape === 'circle' || $shape === 'oval') {
                // Ellipse mask — circle when W==H, oval otherwise
                imagefilledellipse($mask, (int) ($dstW / 2), (int) ($dstH / 2), $dstW, $dstH, $maskWhite);
            } else {
                // Rounded rectangle mask
                $r = max(1, min($radius, (int) ($dstW / 2), (int) ($dstH / 2)));
                // Fill body
                imagefilledrectangle($mask, $r, 0, $dstW - $r - 1, $dstH - 1, $maskWhite);
                imagefilledrectangle($mask, 0, $r, $dstW - 1, $dstH - $r - 1, $maskWhite);
                // Fill 4 corner arcs
                imagefilledellipse($mask, $r, $r, $r * 2, $r * 2, $maskWhite);
                imagefilledellipse($mask, $dstW - $r - 1, $r, $r * 2, $r * 2, $maskWhite);
                imagefilledellipse($mask, $r, $dstH - $r - 1, $r * 2, $r * 2, $maskWhite);
                imagefilledellipse($mask, $dstW - $r - 1, $dstH - $r - 1, $r * 2, $r * 2, $maskWhite);
            }

            // Apply mask pixel by pixel
            for ($cx = 0; $cx < $dstW; $cx++) {
                for ($cy = 0; $cy < $dstH; $cy++) {
                    $maskAlpha = (imagecolorat($mask, $cx, $cy) >> 24) & 0x7F;
                    if ($maskAlpha === 0) {
                        // Pixel is inside the shape
                        $rgb = imagecolorat($thumb, $cx, $cy);
                        imagesetpixel($im, $dstX + $cx, $dstY + $cy, $rgb);
                    }
                }
            }
            imagedestroy($mask);
        }
        imagedestroy($thumb);

        imagedestroy($src);
    }

    /**
     * Parse hex color to RGB array
     */
    private static function parseColor(string $hex): array
    {
        $clean = ltrim($hex, '#');
        if (strlen($clean) === 3) {
            $clean = $clean[0].$clean[0].$clean[1].$clean[1].$clean[2].$clean[2];
        }
        if (strlen($clean) !== 6) {
            return [15, 23, 42]; // default navy
        }
        return [
            hexdec(substr($clean, 0, 2)),
            hexdec(substr($clean, 2, 2)),
            hexdec(substr($clean, 4, 2)),
        ];
    }
}
