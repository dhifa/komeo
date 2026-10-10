<?php

use App\Services\SettingsService;

if (! function_exists('site_setting')) {
    /**
     * Retrieve a website setting with fallback
     */
    function site_setting(string $key, mixed $default = null): mixed
    {
        return SettingsService::get($key, $default);
    }
}

if (! function_exists('brand_colors_css')) {
    /**
     * Generate dynamic brand colors CSS block
     */
    function brand_colors_css(): string
    {
        return SettingsService::getBrandColorsCss();
    }
}

if (! function_exists('site_favicon_url')) {
    /**
     * Get favicon URL or default SVG icon
     */
    function site_favicon_url(): string
    {
        $path = site_setting('App.favicon');
        if (! empty($path) && file_exists(FCPATH . $path)) {
            return base_url($path);
        }
        return '';
    }
}

if (! function_exists('komeo_pending_members_count')) {
    /**
     * Get total pending membership applications count (cached per request)
     */
    function komeo_pending_members_count(): int
    {
        static $count = null;
        if ($count === null) {
            try {
                $membershipModel = model(\App\Models\MembershipModel::class);
                $count = $membershipModel->countPending();
            } catch (\Throwable $e) {
                $count = 0;
            }
        }
        return $count;
    }
}

if (! function_exists('komeo_allowed_badge_icons')) {
    /**
     * Controlled list of supported Lucide badge icons
     */
    function komeo_allowed_badge_icons(): array
    {
        return [
            'award'        => 'Award (Penghargaan)',
            'shield-check' => 'Shield Check (Keamanan/Terverifikasi)',
            'star'         => 'Star (Bintang Unggulan)',
            'briefcase'    => 'Briefcase (Profesional/Bisnis)',
            'check-circle' => 'Check Circle (Validasi)',
            'zap'          => 'Zap (Tergercep/Cepat)',
            'flame'        => 'Flame (Populer/Trending)',
            'sparkles'     => 'Sparkles (Spesial/Istimewa)',
            'crown'        => 'Crown (Mahkota/Top Tier)',
            'building'     => 'Building (Perusahaan/Vendor)',
            'users'        => 'Users (Kontributor Komunitas)',
            'gem'          => 'Gem (Permata/Eksklusif)',
            'trophy'       => 'Trophy (Juara/Piala)',
            'bookmark'     => 'Bookmark (Rekomendasi)',
            'tag'          => 'Tag (Label Khusus)',
            'heart'        => 'Heart (Favorit Klien)',
        ];
    }
}

if (! function_exists('komeo_badge_icon')) {
    /**
     * Render SVG for supported Lucide icons
     */
    function komeo_badge_icon(string $icon, string $class = 'w-3.5 h-3.5'): string
    {
        $icon = strtolower(trim($icon));
        $paths = match ($icon) {
            'shield-check' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />',
            'star' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />',
            'briefcase' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />',
            'check-circle' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
            'zap' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />',
            'flame' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />',
            'sparkles' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.286L13 21l-2.286-6.857L5 12l5.714-2.286L13 3z" />',
            'crown' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 18h18M4 7l4 6 4-6 4 6 4-6v10H4V7z" />',
            'building' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />',
            'users' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />',
            'gem' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 3h12l4 7-10 11L2 10l4-7z" />',
            'trophy' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 21h8m-4-4v4m-5-8a5 5 0 0010 0V5H7v4zm-2-2H3a2 2 0 000 4h2m10-4h2a2 2 0 010 4h-2" />',
            'bookmark' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />',
            'tag' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />',
            'heart' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />',
            default => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />',
        };

        return '<svg class="' . esc($class) . '" fill="none" stroke="currentColor" viewBox="0 0 24 24">' . $paths . '</svg>';
    }
}

if (! function_exists('komeo_render_badge')) {
    /**
     * Render accessible badge pill HTML with custom hex colors and icon
     */
    function komeo_render_badge(array|object $badge, string $size = 'sm', bool $showTooltip = true): string
    {
        $badgeArr = (array) $badge;
        $name     = esc($badgeArr['name'] ?? $badgeArr['badge_name'] ?? '');
        $desc     = esc($badgeArr['description'] ?? '');
        $icon     = $badgeArr['icon'] ?? 'award';
        $bg       = esc($badgeArr['background_color'] ?? '#4F46E5');
        $color    = esc($badgeArr['text_color'] ?? '#FFFFFF');

        $padding = match ($size) {
            'xs' => 'px-2 py-0.5 text-[10px]',
            'lg' => 'px-3.5 py-1.5 text-sm',
            default => 'px-2.5 py-1 text-xs',
        };

        $iconSize = match ($size) {
            'xs' => 'w-3 h-3',
            'lg' => 'w-4 h-4',
            default => 'w-3.5 h-3.5',
        };

        $iconHtml = komeo_badge_icon($icon, $iconSize . ' shrink-0');
        $titleAttr = $showTooltip && ! empty($desc) ? ' title="' . $desc . '"' : '';

        return '<span class="inline-flex items-center gap-1.5 rounded-full font-bold shadow-2xs transition-transform hover:scale-102 ' . $padding . '" style="background-color: ' . $bg . '; color: ' . $color . ';"' . $titleAttr . '>'
             . $iconHtml . '<span>' . $name . '</span></span>';
    }
}

if (! function_exists('komeo_member_active_badges')) {
    /**
     * Retrieve active badges for a member
     */
    function komeo_member_active_badges(int $userId, int $limit = 0): array
    {
        try {
            $badgeModel = model(\App\Models\MemberBadgeModel::class);
            return $badgeModel->getActiveBadgesForUser($userId, $limit);
        } catch (\Throwable $e) {
            return [];
        }
    }
}

if (! function_exists('komeo_pending_verifications_count')) {
    /**
     * Get count of pending identity/business verification requests
     */
    function komeo_pending_verifications_count(): int
    {
        static $count = null;
        if ($count === null) {
            try {
                $vModel = model(\App\Models\MemberVerificationModel::class);
                $count = $vModel->countPending();
            } catch (\Throwable $e) {
                $count = 0;
            }
        }
        return (int) $count;
    }
}

if (! function_exists('komeo_get_user_verification')) {
    /**
     * Get verification record for a user
     */
    function komeo_get_user_verification(int $userId): ?array
    {
        static $cache = [];
        if (! array_key_exists($userId, $cache)) {
            try {
                $vModel = model(\App\Models\MemberVerificationModel::class);
                $cache[$userId] = $vModel->getByUserId($userId);
            } catch (\Throwable $e) {
                $cache[$userId] = null;
            }
        }
        return $cache[$userId];
    }
}

if (! function_exists('komeo_verification_badge')) {
    /**
     * Render official blue check or PIC verification indicator
     *
     * @param array|object|null $verification Verification record or array
     * @param string $memberType 'individual' or 'business'
     * @param string $membershipStatus 'active', 'pending', etc.
     * @param string $size 'sm', 'md', 'lg'
     * @return string
     */
    function komeo_verification_badge(
        array|object|null $verification,
        string $memberType = 'individual',
        string $membershipStatus = 'active',
        string $size = 'md'
    ): string {
        // Verification indicators strictly hide if membership is NOT active
        if ($membershipStatus !== 'active') {
            return '';
        }

        if (empty($verification)) {
            return '';
        }

        $vStatus = is_array($verification) ? ($verification['verification_status'] ?? '') : ($verification->verification_status ?? '');
        $vLevel  = is_array($verification) ? ($verification['verification_level'] ?? '') : ($verification->verification_level ?? '');

        if ($vStatus !== 'approved') {
            return '';
        }

        $iconSize = match ($size) {
            'sm' => 'w-3.5 h-3.5',
            'lg' => 'w-5 h-5',
            default => 'w-4 h-4',
        };

        // Official KOMEO Blue Check Icon SVG
        $blueCheckSvg = '<svg class="' . $iconSize . ' inline-block fill-current text-blue-500 shrink-0" viewBox="0 0 24 24">'
            . '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>'
            . '</svg>';

        // 1. Individual verification
        if ($memberType === 'individual') {
            if ($vLevel === 'identity_verified') {
                return '<span class="inline-flex items-center text-blue-600 align-middle ml-1" title="Identitas diverifikasi oleh KOMEO" data-tippy-content="Identitas diverifikasi oleh KOMEO">'
                    . $blueCheckSvg . '</span>';
            }
            return '';
        }

        // 2. Business verification
        if ($memberType === 'business') {
            // NIB-based Registered Business -> Official blue check beside business name
            if ($vLevel === 'business_verified') {
                return '<span class="inline-flex items-center text-blue-600 align-middle ml-1" title="Data usaha dan PIC telah diperiksa oleh KOMEO" data-tippy-content="Data usaha dan PIC telah diperiksa oleh KOMEO">'
                    . $blueCheckSvg . '</span>';
            }

            // Sole Proprietor / Bisnis Perorangan without NIB -> "PIC Terverifikasi" indicator pill
            if ($vLevel === 'pic_verified') {
                return '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-teal-50 text-teal-800 border border-teal-200/80 align-middle ml-1 shadow-2xs select-none" title="Identitas PIC (Penanggung Jawab Usaha) telah diperiksa dan diverifikasi oleh KOMEO. Usaha berstatus perorangan tanpa NIB." data-tippy-content="PIC Terverifikasi oleh KOMEO">'
                    . '<svg class="w-3 h-3 text-teal-600 fill-current shrink-0" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>'
                    . '<span>PIC Terverifikasi</span>'
                    . '</span>';
            }
        }

        return '';
    }
}

if (! function_exists('komeo_detect_mime_type')) {
    /**
     * Safely detect MIME type of a file without requiring ext-fileinfo
     */
    function komeo_detect_mime_type(string $path): string
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        try {
            $mime = \Config\Mimes::guessTypeFromExtension($ext);
            if (! empty($mime)) {
                return is_array($mime) ? $mime[0] : $mime;
            }
        } catch (\Throwable $e) {
            // Ignore
        }

        if (function_exists('finfo_open')) {
            $finfo = @finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $type = @finfo_file($finfo, $path);
                @finfo_close($finfo);
                if ($type) {
                    return $type;
                }
            }
        }

        if (function_exists('mime_content_type')) {
            $type = @mime_content_type($path);
            if ($type) {
                return $type;
            }
        }

        $map = [
            'pdf'  => 'application/pdf',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'webp' => 'image/webp',
            'gif'  => 'image/gif',
            'svg'  => 'image/svg+xml',
        ];

        return $map[$ext] ?? 'application/octet-stream';
    }
}

if (! function_exists('komeo_render_member_role')) {
    /**
     * Render Custom Member Role badge pill HTML
     */
    function komeo_render_member_role(array|object $role, string $size = 'sm', bool $showTooltip = true): string
    {
        $roleArr = (array) $role;
        $name    = esc($roleArr['name'] ?? $roleArr['role_name'] ?? '');
        $desc    = esc($roleArr['description'] ?? '');
        $bg      = esc($roleArr['background_color'] ?? '#4F46E5');
        $color   = esc($roleArr['text_color'] ?? '#FFFFFF');
        $isPrimary = ! empty($roleArr['is_primary']);

        $padding = match ($size) {
            'xs'    => 'px-2 py-0.5 text-[10px]',
            'lg'    => 'px-3.5 py-1.5 text-sm',
            default => 'px-2.5 py-0.5 text-xs',
        };

        $titleAttr = $showTooltip && ! empty($desc) ? ' title="' . $desc . '"' : '';

        // Distinct rounded pill with border accent for primary role
        $border = $isPrimary ? ' ring-1 ring-white/30 font-extrabold' : ' font-bold';

        return '<span class="inline-flex items-center gap-1.5 rounded-lg shadow-2xs ' . $padding . $border . '" style="background-color: ' . $bg . '; color: ' . $color . ';"' . $titleAttr . '>'
             . '<span>' . $name . '</span></span>';
    }
}

if (! function_exists('komeo_get_user_roles')) {
    /**
     * Get active custom roles for a member
     */
    function komeo_get_user_roles(int $userId, bool $publicOnly = false): array
    {
        static $cache = [];
        $key = "{$userId}_" . ($publicOnly ? 'pub' : 'all');
        if (! array_key_exists($key, $cache)) {
            try {
                $assignmentModel = model(\App\Models\MemberRoleAssignmentModel::class);
                $roles = $assignmentModel->getActiveRolesForUser($userId);
                if ($publicOnly) {
                    $roles = array_values(array_filter($roles, fn($r) => ! empty($r['is_public'])));
                }
                $cache[$key] = $roles;
            } catch (\Throwable $e) {
                $cache[$key] = [];
            }
        }
        return $cache[$key];
    }
}

if (! function_exists('komeo_format_rupiah')) {
    /**
     * Format number as Indonesian Rupiah currency string
     */
    function komeo_format_rupiah(float|int|string|null $amount, bool $withPrefix = true): string
    {
        if ($amount === null || $amount === '') {
            return '-';
        }
        $num = (float) $amount;
        $formatted = number_format($num, 0, ',', '.');
        return $withPrefix ? 'Rp ' . $formatted : $formatted;
    }
}

