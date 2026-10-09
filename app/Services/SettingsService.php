<?php

namespace App\Services;

class SettingsService
{
    protected const CACHE_KEY = 'komeo_site_settings';
    protected const CACHE_TTL = 3600; // 1 hour

    /**
     * In-memory cache for request lifecycle
     */
    protected static ?array $memoryCache = null;

    /**
     * Default application settings
     */
    protected static array $defaults = [
        // 1. Identitas Website
        'App.site_name'           => 'KOMEO.ID',
        'App.site_subtitle'       => 'KOMUNITAS EVENT INDONESIA',
        'App.site_tagline'        => 'Satu Komunitas, Ribuan Peluang Kolaborasi.',
        'App.site_description'    => 'Tempat berkumpulnya para pelaku industri event Indonesia. Bangun koneksi, temukan kolaborasi, dan kembangkan peluang bersama KOMEO.',
        'App.footer_description'  => '"Satu Komunitas, Ribuan Peluang Kolaborasi." Wadah resmi kolaborasi profesional ekosistem industri event di Indonesia: Event Organizer, Wedding Organizer, Vendor Teknis, Kreatif, dan Talenta.',
        'App.copyright_text'      => 'KOMEO.ID. Hak Cipta Dilindungi Undang-Undang.',

        // 2. Logo & Favicon
        'App.logo_header'         => '',
        'App.logo_footer'         => '',
        'App.favicon'             => '',
        'App.og_image'            => '',

        // 3. Warna & Tampilan
        'App.color_primary'       => '#4F46E5', // indigo-600
        'App.color_secondary'     => '#4338CA', // indigo-700
        'App.color_accent'        => '#818CF8', // indigo-400
        'App.color_background'    => '#FFFFFF',
        'App.color_text'          => '#0F172A', // slate-900

        // 4. Media Sosial
        'App.social_instagram'    => 'https://instagram.com/komeo.id',
        'App.social_linkedin'     => 'https://linkedin.com/company/komeo-id',
        'App.social_tiktok'       => 'https://tiktok.com/@komeo.id',
        'App.social_youtube'      => '',
        'App.social_facebook'     => '',
        'App.social_whatsapp'     => 'https://wa.me/6281234567890',

        // 5. Kontak
        'App.contact_email'       => 'contact@komeo.id',
        'App.contact_phone'       => '+62 812-3456-7890',
        'App.contact_whatsapp'    => '081234567890',
        'App.contact_city'        => 'Jakarta & Seluruh Indonesia',
        'App.contact_address'     => 'Jakarta, Indonesia',
        'App.contact_maps_url'    => '',

        // 6. SEO & Metadata
        'App.meta_title'          => 'KOMEO.ID - Komunitas Pelaku Industri Event Indonesia',
        'App.meta_description'    => 'KOMEO.ID adalah wadah komunitas para pelaku industri event di Indonesia: Event Organizer, Wedding Organizer, Vendor, Talent, dan Freelancer.',
        'App.meta_keywords'       => 'event organizer, wedding organizer, vendor event, audio visual, lighting, talent event, freelancer event indonesia, komeo',
        'App.social_share_title'  => 'KOMEO.ID - Satu Komunitas, Ribuan Peluang Kolaborasi',
        'App.social_share_description' => 'Tempat berkumpulnya para pelaku industri event Indonesia. Bangun koneksi, temukan kolaborasi, dan kembangkan peluang bersama.',
        'App.robots_indexing'     => 'index, follow',

        // 7. Konten Homepage - Hero
        'Homepage.hero_badge'               => 'Komunitas Resmi Industri Event Indonesia',
        'Homepage.hero_headline'            => 'Satu Komunitas,',
        'Homepage.hero_headline_highlight'  => 'Ribuan Peluang Kolaborasi.',
        'Homepage.hero_description'         => 'Tempat berkumpulnya para pelaku industri event Indonesia. Bangun koneksi, temukan kolaborasi, dan kembangkan peluang bersama KOMEO.',
        'Homepage.hero_btn_primary_label'   => 'Gabung KOMEO',
        'Homepage.hero_btn_primary_url'     => '/register',
        'Homepage.hero_btn_secondary_label' => 'Tentang Kami',
        'Homepage.hero_btn_secondary_url'   => '#tentang',
        'Homepage.hero_background_image'    => '',

        // Konten Homepage - About
        'Homepage.about_badge'              => 'Mengenal Lebih Dekat',
        'Homepage.about_title'              => 'Wadah Sinergi Terbesar bagi Para Pelaku Industri Event Indonesia',
        'Homepage.about_subtitle'           => 'Terbuka Untuk Perorangan & Bisnis',
        'Homepage.about_description'        => 'Industri event adalah ekosistem yang dinamis dan saling bergantung. Dari konseptualisasi oleh Event Organizer, keindahan momen oleh Wedding Organizer, ketepatan tata panggung & multimedia, hingga aksi panggung para talenta. KOMEO.ID hadir sebagai jembatan profesional yang menyatukan perorangan maupun entitas bisnis. Kami meruntuhkan sekat birokrasi dan membuka akses langsung antar penyedia jasa untuk saling bermitra dalam menyukseskan ribuan event di seluruh Nusantara.',
        'Homepage.about_image'              => '',

        // Konten Homepage - Benefits
        'Homepage.benefits_badge'           => 'Keuntungan Eksklusif',
        'Homepage.benefits_title'           => 'Mengapa Bergabung di KOMEO?',
        'Homepage.benefits_description'     => 'Platform komunitas yang dirancang khusus untuk memenuhi kebutuhan nyata industri event.',

        // Konten Homepage - Statistics
        'Homepage.stats_use_real_members'      => '1',
        'Homepage.stats_use_real_cities'       => '1',
        'Homepage.stats_custom_metric1_number' => '10+',
        'Homepage.stats_custom_metric1_label'  => 'Sektor Event Tergabung',
        'Homepage.stats_custom_metric2_number' => '100%',
        'Homepage.stats_custom_metric2_label'  => 'Kolaborasi Terbuka',

        // Konten Homepage - Visibility
        'Homepage.section_hero_visible'        => '1',
        'Homepage.section_about_visible'       => '1',
        'Homepage.section_benefits_visible'    => '1',
        'Homepage.section_categories_visible'  => '1',
        'Homepage.section_statistics_visible'  => '1',
        'Homepage.section_members_visible'     => '1',
        'Homepage.section_cta_visible'         => '1',

        // 9. Pengaturan Direktori Member & Vendor (Phase 5)
        'Directory.title'                 => 'Temukan Profesional Event Terbaik di KOMEO',
        'Directory.subtitle'              => 'Jelajahi jaringan Event Organizer, vendor, freelancer, dan talent dari berbagai daerah di Indonesia.',
        'Directory.featured_count'        => '6',
        'Directory.per_page'              => '12',
        'Directory.default_sort'          => 'newest',
        'Directory.show_city'             => '1',
        'Directory.show_specializations'  => '1',
        'Directory.enable_public'         => '1',
        'Directory.enable_vendor'         => '1',
        'Directory.enable_crew'           => '1',

        // 10. Pengaturan Aktivasi Keanggotaan & Kontak Admin (Phase 5 Extension)
        'Membership.activation_whatsapp'     => '081234567890',
        'Membership.activation_email'        => 'admin@komeo.id',
        'Membership.activation_instructions' => 'Akun Anda berhasil dibuat. Untuk mengaktifkan keanggotaan KOMEO.ID dan mendapatkan KTA resmi, silakan hubungi Admin KOMEO untuk proses verifikasi dan persetujuan.',
        'Membership.activation_btn_label'    => 'Hubungi Admin untuk Aktivasi',
        'Membership.enable_whatsapp'         => '1',
        'Membership.enable_email'            => '1',

        // 8. Pengaturan KTA (Kartu Tanda Anggota)
        'Kta.card_title'               => 'KARTU TANDA ANGGOTA',
        'Kta.front_bg_color'           => '#0F172A', // Dark Navy
        'Kta.back_bg_color'            => '#0B1120',
        'Kta.primary_color'            => '#6366F1', // Indigo / Purple
        'Kta.accent_color'             => '#818CF8',
        'Kta.text_color'               => '#FFFFFF',
        'Kta.text_secondary_color'     => '#94A3B8',
        'Kta.logo_path'                => '',
        'Kta.front_bg_image'           => '',
        'Kta.back_bg_image'            => '',
        'Kta.hide_default_shapes'      => '0', // 1 to hide default shapes when custom artwork is uploaded
        'Kta.number_style'             => 'gold_badge', // gold_badge, glass_pill, neon_cyan, modern_slate, minimal_outline
        'Kta.photo_shape'              => 'rounded', // rounded, circle, square
        'Kta.show_meta'                => '1', // Bergabung & Domisili
        'Kta.show_status_badge'        => '1', // Status Pill
        // Live Editor Positioning Coordinates (Card dimension: 1011 x 638 px)
        'Kta.layout_mode'              => 'default', // default or custom
        'Kta.pos_photo_x'              => 45,
        'Kta.pos_photo_y'              => 125,
        'Kta.pos_photo_w'              => 210,
        'Kta.pos_photo_h'              => 270,
        'Kta.pos_name_x'               => 285,
        'Kta.pos_name_y'               => 179,
        'Kta.pos_name_size'            => 22,
        'Kta.pos_number_x'             => 285,
        'Kta.pos_number_y'             => 295,
        'Kta.pos_category_x'           => 285,
        'Kta.pos_category_y'           => 245,
        'Kta.pos_meta_x'               => 285,
        'Kta.pos_meta_y'               => 375,
        'Kta.pos_qr_x'                 => 756,
        'Kta.pos_qr_y'                 => 373,
        'Kta.pos_qr_size'              => 190,
        'Kta.pos_title_x'              => 680,
        'Kta.pos_title_y'              => 44,
        'Kta.pos_logo_x'               => 45,
        'Kta.pos_logo_y'               => 22,
        // Back side customizable fields
        'Kta.back_header_title'        => 'KOMEO.ID',
        'Kta.back_header_subtitle'     => 'KOMUNITAS EVENT ORGANIZER INDONESIA',
        'Kta.back_badge_text'          => 'IDENTITAS RESMI KEANGGOTAAN',
        'Kta.tagline'                  => 'Satu Komunitas, Ribuan Peluang Kolaborasi.',
        'Kta.back_message'             => 'Kartu ini merupakan tanda keanggotaan resmi KOMEO.ID. Keabsahan keanggotaan dapat diverifikasi secara langsung melalui pemindaian QR Code pada bagian depan kartu.',
        'Kta.back_rules_title'         => 'KETENTUAN PENGGUNAAN KARTU:',
        'Kta.back_rules_text'          => "1. Kartu ini hanya berlaku bagi anggota yang terdaftar resmi dan berstatus aktif di KOMEO.ID.\n2. Kartu ini tidak dapat dipindahtangankan, digandakan, atau dipinjamkan kepada pihak mana pun.\n3. Anggota wajib menjunjung tinggi etika profesi dan integritas industri event Indonesia.\n4. Apabila keanggotaan ditangguhkan atau dicabut, hak kepemilikan dan verifikasi otomatis gugur.",
        'Kta.back_office_address'      => 'Sekretariat Pusat KOMEO.ID | Hak Cipta Dilindungi Undang-Undang',
        'Kta.back_sign_title'          => 'Dewan Pengurus KOMEO.ID',
        'Kta.back_sign_name'           => 'Ketua Umum',
        'Kta.back_show_sign'           => '0',
        'Kta.website_url'              => 'https://komeo.id',
        'Kta.qr_position'              => 'bottom-right',
        'Kta.qr_size_mm'               => 16,
        'Kta.safe_margin_mm'           => 3.5,
        'Kta.default_dpi'              => 300,
        'Kta.print_bleed_mm'           => 0,
        'Kta.sheet_size'               => 'A4',
        'Kta.card_spacing_mm'          => 4,
        'Kta.sheet_margin_mm'          => 8,
        'Kta.back_orientation'         => 'normal',
        'Kta.duplex_flip'              => 'short-edge',
    ];

    /**
     * Get all KTA settings as clean key => value dictionary
     */
    public static function getKtaSettings(): array
    {
        $all = self::getAll();
        $kta = [];
        foreach ($all as $k => $v) {
            if (str_starts_with($k, 'Kta.')) {
                $cleanKey = substr($k, 4);
                $kta[$cleanKey] = $v;
            }
        }

        // Provide aliases for flexible naming across views and renderers
        $kta['bg_color_front']      = $kta['bg_color_front'] ?? $kta['front_bg_color'] ?? '#0F172A';
        $kta['bg_color_back']       = $kta['bg_color_back'] ?? $kta['back_bg_color'] ?? '#0B1120';
        $kta['text_color_front']    = $kta['text_color_front'] ?? $kta['text_color'] ?? '#FFFFFF';
        $kta['text_color_back']     = $kta['text_color_back'] ?? $kta['text_color'] ?? '#FFFFFF';
        $kta['accent_purple']       = $kta['accent_purple'] ?? $kta['primary_color'] ?? '#6366F1';
        $kta['accent_cyan']         = $kta['accent_cyan'] ?? $kta['accent_color'] ?? '#818CF8';
        $kta['logo']                = $kta['logo'] ?? $kta['logo_path'] ?? '';
        $kta['bg_pattern_front']    = $kta['bg_pattern_front'] ?? $kta['front_bg_image'] ?? '';
        $kta['bg_pattern_back']     = $kta['bg_pattern_back'] ?? $kta['back_bg_image'] ?? '';
        $kta['back_statement']      = $kta['back_statement'] ?? $kta['back_message'] ?? '';
        $kta['number_style']        = $kta['number_style'] ?? 'gold_badge';
        $kta['photo_shape']         = $kta['photo_shape'] ?? 'rounded';
        $kta['hide_default_shapes'] = (string) ($kta['hide_default_shapes'] ?? '0');
        $kta['qr_size']             = (int) ($kta['qr_size'] ?? $kta['pos_qr_size'] ?? 190);
        $kta['export_dpi']          = (int) ($kta['export_dpi'] ?? $kta['default_dpi'] ?? 300);
        $kta['bleed_mm']            = (float) ($kta['bleed_mm'] ?? $kta['print_bleed_mm'] ?? 0);
        $kta['print_profile']       = $kta['print_profile'] ?? 'pvc';
        $kta['back_rotation']       = (int) ($kta['back_rotation'] ?? (($kta['back_orientation'] ?? '') === 'rotate180' ? 180 : 0));

        // Coordinate defaults if empty
        $kta['pos_photo_x']    = (int) ($kta['pos_photo_x'] ?? 45);
        $kta['pos_photo_y']    = (int) ($kta['pos_photo_y'] ?? 125);
        $kta['pos_photo_w']    = (int) ($kta['pos_photo_w'] ?? 210);
        $kta['pos_photo_h']    = (int) ($kta['pos_photo_h'] ?? 270);
        $kta['pos_name_x']     = (int) ($kta['pos_name_x'] ?? 285);
        $kta['pos_name_y']     = (int) ($kta['pos_name_y'] ?? 179);
        $kta['pos_name_size']  = (int) ($kta['pos_name_size'] ?? 22);
        $kta['pos_number_x']   = (int) ($kta['pos_number_x'] ?? 285);
        $kta['pos_number_y']   = (int) ($kta['pos_number_y'] ?? 295);
        $kta['pos_category_x'] = (int) ($kta['pos_category_x'] ?? 285);
        $kta['pos_category_y'] = (int) ($kta['pos_category_y'] ?? 245);
        $kta['pos_meta_x']     = (int) ($kta['pos_meta_x'] ?? 285);
        $kta['pos_meta_y']     = (int) ($kta['pos_meta_y'] ?? 375);
        $kta['show_meta']      = (string) ($kta['show_meta'] ?? '1');
        $kta['pos_qr_x']       = (int) ($kta['pos_qr_x'] ?? 756);
        $kta['pos_qr_y']       = (int) ($kta['pos_qr_y'] ?? 373);
        $kta['pos_qr_size']    = (int) ($kta['pos_qr_size'] ?? 190);
        $kta['pos_title_x']    = (int) ($kta['pos_title_x'] ?? 680);
        $kta['pos_title_y']    = (int) ($kta['pos_title_y'] ?? 44);
        $kta['pos_logo_x']     = (int) ($kta['pos_logo_x'] ?? 45);
        $kta['pos_logo_y']     = (int) ($kta['pos_logo_y'] ?? 22);

        return $kta;
    }

    /**
     * Get a setting by dot-notated key (e.g. 'App.site_name')
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $all = self::getAll();

        if (array_key_exists($key, $all)) {
            $val = $all[$key];
            return $val !== null ? $val : ($default ?? self::$defaults[$key] ?? '');
        }

        return $default ?? self::$defaults[$key] ?? null;
    }

    /**
     * Get all settings merged with defaults
     */
    public static function getAll(): array
    {
        if (self::$memoryCache !== null) {
            return self::$memoryCache;
        }

        // Try reading from cache
        $cached = cache(self::CACHE_KEY);
        if (is_array($cached)) {
            self::$memoryCache = $cached;
            return self::$memoryCache;
        }

        // Fetch from database table `settings`
        $db = \Config\Database::connect();
        $builder = $db->table('settings');
        $rows = $builder->select('class, key, value')->get()->getResultArray();

        $dbSettings = [];
        foreach ($rows as $row) {
            $dotKey = $row['class'] . '.' . $row['key'];
            $dbSettings[$dotKey] = $row['value'];
        }

        // Merge defaults with DB values
        $merged = array_merge(self::$defaults, $dbSettings);

        // Cache result
        cache()->save(self::CACHE_KEY, $merged, self::CACHE_TTL);
        self::$memoryCache = $merged;

        return self::$memoryCache;
    }

    /**
     * Set a single setting
     */
    public static function set(string $dotKey, mixed $value): bool
    {
        return self::setMultiple([$dotKey => $value]);
    }

    /**
     * Set multiple settings at once
     */
    public static function setMultiple(array $settings): bool
    {
        $db = \Config\Database::connect();
        $builder = $db->table('settings');
        $now = date('Y-m-d H:i:s');

        $db->transBegin();
        try {
            foreach ($settings as $dotKey => $value) {
                $parts = explode('.', $dotKey, 2);
                if (count($parts) !== 2) {
                    continue;
                }
                [$class, $key] = $parts;
                $valueStr = is_bool($value) ? ($value ? '1' : '0') : (string) $value;

                $existing = $builder->where('class', $class)->where('key', $key)->get()->getRow();

                if ($existing) {
                    $builder->where('id', $existing->id)->update([
                        'value'      => $valueStr,
                        'updated_at' => $now,
                    ]);
                } else {
                    $builder->insert([
                        'class'      => $class,
                        'key'        => $key,
                        'value'      => $valueStr,
                        'type'       => 'string',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }

            $db->transCommit();
            self::invalidateCache();

            return true;
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'SettingsService error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Invalidate cached settings
     */
    public static function invalidateCache(): void
    {
        self::$memoryCache = null;
        cache()->delete(self::CACHE_KEY);
    }

    /**
     * Reset specific settings or all settings to defaults
     */
    public static function resetToDefaults(array $keys = []): bool
    {
        $db = \Config\Database::connect();
        $builder = $db->table('settings');

        if (empty($keys)) {
            $builder->truncate();
        } else {
            foreach ($keys as $dotKey) {
                $parts = explode('.', $dotKey, 2);
                if (count($parts) === 2) {
                    $builder->where('class', $parts[0])->where('key', $parts[1])->delete();
                }
            }
        }

        self::invalidateCache();
        return true;
    }

    /**
     * Get system defaults
     */
    public static function getDefaults(): array
    {
        return self::$defaults;
    }

    /**
     * Generate CSS variables block for brand colors
     */
    public static function getBrandColorsCss(): string
    {
        $primary    = self::get('App.color_primary', '#4F46E5');
        $secondary  = self::get('App.color_secondary', '#4338CA');
        $accent     = self::get('App.color_accent', '#818CF8');
        $background = self::get('App.color_background', '#FFFFFF');
        $text       = self::get('App.color_text', '#0F172A');

        return <<<CSS
<style id="dynamic-brand-colors">
    :root {
        --brand-primary: {$primary};
        --brand-secondary: {$secondary};
        --brand-accent: {$accent};
        --brand-background: {$background};
        --brand-text: {$text};
    }
    .bg-brand-custom { background-color: var(--brand-primary) !important; }
    .text-brand-custom { color: var(--brand-primary) !important; }
    .border-brand-custom { border-color: var(--brand-primary) !important; }
</style>
CSS;
    }

    /**
     * Validate HEX color
     */
    public static function isValidHexColor(string $hex): bool
    {
        return (bool) preg_match('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/', trim($hex));
    }
}
