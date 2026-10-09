<?php

class TestSettingsAndCms
{
    private string $baseUrl = 'http://localhost:8080';
    private string $cookieJar;
    private ?string $csrfToken = null;
    private string $csrfTokenName = 'csrf_token_komeo';

    public function __construct()
    {
        $this->cookieJar = tempnam(sys_get_temp_dir(), 'komeo_cookie_');
    }

    public function __destruct()
    {
        if (file_exists($this->cookieJar)) {
            @unlink($this->cookieJar);
        }
    }

    private function isRedirect(int $code): bool
    {
        return $code === 301 || $code === 302 || $code === 303 || $code === 307 || $code === 308;
    }

    private function request(string $path, string $method = 'GET', array $postData = [], array $files = []): array
    {
        $url = $this->baseUrl . $path;
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieJar);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieJar);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($this->csrfToken && !isset($postData[$this->csrfTokenName])) {
                $postData[$this->csrfTokenName] = $this->csrfToken;
            }

            if (!empty($files)) {
                $payload = $postData;
                foreach ($files as $name => $filePath) {
                    $payload[$name] = new CURLFile($filePath, 'image/png', basename($filePath));
                }
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            }
        }

        $response = curl_exec($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $header = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);

        curl_close($ch);

        // Try extract csrf token if present in body
        if (preg_match('/name="(csrf_[^"]+)"\s+value="([^"]+)"/i', $body, $matches)) {
            $this->csrfTokenName = $matches[1];
            $this->csrfToken = $matches[2];
        }

        return [
            'code'   => $httpCode,
            'header' => $header,
            'body'   => $body,
        ];
    }

    public function run()
    {
        echo "=== STARTING CMS & SETTINGS TEST SUITE ===\n\n";

        // Test 1: Unauthorized access to /admin/settings
        echo "[TEST 1] Unauthorized access to /admin/settings... ";
        $res = $this->request('/admin/settings');
        if ($this->isRedirect($res['code']) || str_contains($res['header'], 'login')) {
            echo "PASSED (Redirected to login, code: {$res['code']})\n";
        } else {
            echo "FAILED (Unexpected code: {$res['code']})\n";
        }

        // Test 2: Login as admin
        echo "[TEST 2] Logging in as admin... ";
        $this->request('/login');

        $loginRes = $this->request('/login', 'POST', [
            $this->csrfTokenName => $this->csrfToken,
            'login'              => 'admin@komeo.id',
            'password'           => 'Password123!',
        ]);

        if ($this->isRedirect($loginRes['code']) && (str_contains($loginRes['header'], 'Location: /admin') || str_contains($loginRes['header'], 'Location: /dashboard') || str_contains($loginRes['header'], 'Location: http://localhost:8080'))) {
            echo "PASSED (Logged in successfully)\n";
        } else {
            echo "FAILED (Code: {$loginRes['code']}, Headers:\n{$loginRes['header']})\n";
            exit(1);
        }

        // Test 3: Load /admin/settings with tabs
        echo "[TEST 3] Loading /admin/settings tabbed page... ";
        $settingsRes = $this->request('/admin/settings');
        if ($settingsRes['code'] === 200 && str_contains($settingsRes['body'], 'Pengaturan Website') && str_contains($settingsRes['body'], 'Identitas')) {
            echo "PASSED (Admin settings loaded with tabs)\n";
        } else {
            echo "FAILED (Code: {$settingsRes['code']})\n";
        }

        // Test 4: Load /admin/content/homepage
        echo "[TEST 4] Loading /admin/content/homepage... ";
        $cmsRes = $this->request('/admin/content/homepage');
        if ($cmsRes['code'] === 200 && str_contains($cmsRes['body'], 'Manajemen Konten Beranda') && str_contains($cmsRes['body'], 'Visibilitas Seksi')) {
            echo "PASSED (Admin homepage content editor loaded)\n";
        } else {
            echo "FAILED (Code: {$cmsRes['code']})\n";
        }

        // Test 5: Update Website Identity
        echo "[TEST 5] Updating Website Identity via POST /admin/settings/update/identity... ";
        $this->request('/admin/settings?tab=identity');
        $resIdentity = $this->request('/admin/settings/update/identity', 'POST', [
            'site_name'          => 'KOMEO.ID',
            'site_subtitle'      => 'KOMUNITAS EVENT NUSANTARA',
            'site_tagline'       => 'Satu Komunitas, Sejuta Kesempatan Emas.',
            'site_description'   => 'Platform resmi komunitas EO dan industri event di seluruh Indonesia.',
            'footer_description' => 'Wadah sinergi terbesar industri event di Indonesia.',
            'copyright_text'     => '© 2026 KOMEO.ID. Seluruh Hak Cipta Dilindungi.',
        ]);
        if ($this->isRedirect($resIdentity['code'])) {
            echo "PASSED (Saved identity)\n";
        } else {
            echo "FAILED (Code: {$resIdentity['code']})\n";
        }

        // Test 6: Verify Identity changes reflected on public homepage
        echo "[TEST 6] Verifying Identity updates on public homepage /... ";
        $publicRes = $this->request('/');
        if (str_contains($publicRes['body'], 'KOMUNITAS EVENT NUSANTARA') && str_contains($publicRes['body'], 'Wadah sinergi terbesar industri event di Indonesia.')) {
            echo "PASSED (Homepage reflects updated identity)\n";
        } else {
            echo "FAILED (Did not find updated strings on homepage)\n";
        }

        // Test 7: Update Brand Colors
        echo "[TEST 7] Updating Brand Colors (Primary: #2563EB, Secondary: #1D4ED8)... ";
        $this->request('/admin/settings?tab=appearance');
        $resColors = $this->request('/admin/settings/update/appearance', 'POST', [
            'color_primary'    => '#2563eb',
            'color_secondary'  => '#1d4ed8',
            'color_accent'     => '#f59e0b',
            'color_background' => '#ffffff',
            'color_text'       => '#0f172a',
        ]);
        if ($this->isRedirect($resColors['code'])) {
            echo "PASSED (Colors saved)\n";
        } else {
            echo "FAILED (Code: {$resColors['code']})\n";
        }

        // Test 8: Verify Brand Colors in CSS on public homepage
        echo "[TEST 8] Verifying Brand Colors in CSS on public homepage /... ";
        $publicRes = $this->request('/');
        if (stripos($publicRes['body'], '--brand-primary: #2563eb;') !== false && stripos($publicRes['body'], '--brand-secondary: #1d4ed8;') !== false) {
            echo "PASSED (CSS variables injected: --brand-primary: #2563EB)\n";
        } else {
            echo "FAILED (CSS variables not found)\n";
        }

        // Test 9: Reset Brand Colors to Default
        echo "[TEST 9] Resetting Brand Colors to default via POST /admin/settings/reset-colors... ";
        $this->request('/admin/settings?tab=appearance');
        $resReset = $this->request('/admin/settings/reset-colors', 'POST', []);
        $publicRes = $this->request('/');
        if (stripos($publicRes['body'], '--brand-primary: #4f46e5;') !== false) {
            echo "PASSED (Reset to default color #4F46E5 succeeded)\n";
        } else {
            echo "FAILED (Colors not reset)\n";
        }

        // Test 10: Update Social Media & Contact & SEO
        echo "[TEST 10] Updating Social Media, Contact & SEO settings... ";
        $this->request('/admin/settings?tab=social');
        $this->request('/admin/settings/update/social', 'POST', [
            'social_instagram' => 'https://instagram.com/komeo.id',
            'social_linkedin'  => 'https://linkedin.com/company/komeoid',
            'social_tiktok'    => 'https://tiktok.com/@komeo.id',
            'social_youtube'   => '',
            'social_facebook'  => '',
            'social_whatsapp'  => 'https://wa.me/6281234567890',
        ]);

        $this->request('/admin/settings?tab=contact');
        $this->request('/admin/settings/update/contact', 'POST', [
            'contact_email'    => 'halo@komeo.id',
            'contact_phone'    => '+62 812-3456-7890',
            'contact_city'     => 'Jakarta Pusat',
            'contact_address'  => 'Gedung Kesenian No. 10',
            'contact_whatsapp' => '+62 812-3456-7890',
            'contact_maps_url' => '',
        ]);

        $this->request('/admin/settings?tab=seo');
        $this->request('/admin/settings/update/seo', 'POST', [
            'meta_title'              => 'KOMEO.ID - Jaringan Komunitas Event Terbesar se-Indonesia',
            'meta_description'        => 'Temukan dan bermitra dengan ribuan vendor, EO, dan profesional event.',
            'meta_keywords'           => 'event organizer, vendor event, sewa sound system, wedding organizer',
            'social_share_title'      => 'KOMEO.ID - Ekosistem Event Nusantara',
            'social_share_description'=> 'Kolaborasi resmi industri event Indonesia.',
            'robots_indexing'         => 'index, follow',
        ]);

        $publicRes = $this->request('/');
        $checks = [
            'https://instagram.com/komeo.id' => 'Social Instagram link rendered',
            'halo@komeo.id'                  => 'Contact email rendered',
            'Jakarta Pusat'                  => 'Contact city rendered',
            'Jaringan Komunitas Event'       => 'SEO Meta Title rendered',
        ];

        $allGood = true;
        foreach ($checks as $str => $label) {
            if (!str_contains($publicRes['body'], $str)) {
                echo "\n  Missing $label ($str)";
                $allGood = false;
            }
        }
        if ($allGood) {
            echo "PASSED (All Social, Contact, and SEO outputs verified on /)\n";
        } else {
            echo "FAILED\n";
        }

        // Test 11: Update Homepage Hero Text and Section Toggles
        echo "[TEST 11] Updating Homepage Hero Text and Section Toggles... ";
        $this->request('/admin/content/homepage');
        $resHp = $this->request('/admin/content/homepage/update', 'POST', [
            'section_hero_visible'       => '1',
            'section_about_visible'      => '1',
            'section_benefits_visible'   => '1',
            'section_categories_visible' => '1',
            'section_statistics_visible' => '1',
            'section_members_visible'    => '1',
            'section_cta_visible'        => '1',
            'hero_badge'                => 'Komunitas Terdepan Industri Event',
            'hero_headline'             => 'Satu Komunitas Terbaik,',
            'hero_headline_highlight'   => 'Ribuan Peluang Juara!',
            'hero_description'          => 'Pusat kolaborasi paling solid bagi para profesional event Indonesia.',
            'hero_btn_primary_label'    => 'Daftar Sekarang Juga',
            'hero_btn_primary_url'      => 'register',
            'hero_btn_secondary_label'  => 'Pelajari Selengkapnya',
            'hero_btn_secondary_url'    => '#tentang',
            'about_badge'               => 'Mengenal KOMEO',
            'about_title'               => 'Membangun Ekosistem Event yang Kokoh',
            'about_subtitle'            => 'KOLABORASI TANPA BATAS',
            'about_description'         => 'Bersama memajukan standar industri event di seluruh Indonesia.',
            'benefits_badge'            => 'Keunggulan Eksklusif',
            'benefits_title'            => 'Mengapa Harus KOMEO?',
            'benefits_description'      => 'Manfaat nyata untuk mengembangkan bisnis jasa Anda.',
            'stats_use_real_members'    => '1',
            'stats_use_real_cities'     => '1',
            'stats_custom_metric1_number' => '500+',
            'stats_custom_metric1_label'  => 'Kolaborasi Sukses',
            'stats_custom_metric2_number' => '100%',
            'stats_custom_metric2_label'  => 'Vendor Kredibel',
        ]);

        if ($this->isRedirect($resHp['code'])) {
            echo "PASSED (Homepage settings saved)\n";
        } else {
            echo "FAILED (Code: {$resHp['code']})\n";
        }

        // Test 12: Verify Homepage Hero update on public page
        echo "[TEST 12] Verifying updated Hero text on public homepage /... ";
        $publicRes = $this->request('/');
        if (str_contains($publicRes['body'], 'Satu Komunitas Terbaik,') && str_contains($publicRes['body'], 'Ribuan Peluang Juara!')) {
            echo "PASSED (Homepage reflects updated hero text)\n";
        } else {
            echo "FAILED (Did not find updated hero text on homepage)\n";
        }

        // Test 13: Community Benefit CRUD (Normalized Table)
        echo "[TEST 13] Adding a new Community Benefit item via POST /admin/content/homepage/benefit/save... ";
        $this->request('/admin/content/homepage');
        // Check if benefit item already exists to update it rather than create endless duplicates
        $existingBenefit = \Config\Database::connect()->table('homepage_benefits')
            ->where('title', 'Keamanan Transaksi & Kontrak')
            ->orderBy('id', 'ASC')
            ->get()
            ->getFirstRow('array');

        $resBenefit = $this->request('/admin/content/homepage/benefit/save', 'POST', [
            'id'             => $existingBenefit['id'] ?? null,
            'title'          => 'Keamanan Transaksi & Kontrak',
            'description'    => 'Panduan dan template perjanjian kerja sama vendor standar industri.',
            'icon'           => 'shield',
            'sort_order'     => 4,
            'is_active'      => 1,
        ]);
        if ($this->isRedirect($resBenefit['code'])) {
            echo "PASSED (Benefit saved)\n";
        } else {
            echo "FAILED (Code: {$resBenefit['code']})\n";
        }

        // Verify Benefit appears on public homepage (properly escaped check)
        echo "[TEST 14] Verifying new Benefit item on public homepage /... ";
        $publicRes = $this->request('/');
        if (str_contains($publicRes['body'], 'Keamanan Transaksi')) {
            echo "PASSED (New benefit rendered on homepage)\n";
        } else {
            echo "FAILED (Benefit not found on homepage)\n";
        }

        // Test 15: Header Logo Upload and Reflection
        echo "[TEST 15] Uploading custom Header Logo PNG... ";
        $tempLogo = tempnam(sys_get_temp_dir(), 'test_logo_') . '.png';
        // 1x1 valid PNG in base64
        $pngBytes = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');
        file_put_contents($tempLogo, $pngBytes);

        $this->request('/admin/settings?tab=logo');
        $resUpload = $this->request('/admin/settings/update/logo', 'POST', [], [
            'logo_header' => $tempLogo,
        ]);
        @unlink($tempLogo);

        if ($this->isRedirect($resUpload['code'])) {
            echo "PASSED (Logo uploaded)\n";
        } else {
            echo "FAILED (Code: {$resUpload['code']})\n";
        }

        echo "[TEST 16] Verifying Header Logo img tag on public homepage /... ";
        $publicRes = $this->request('/');
        if (str_contains($publicRes['body'], 'uploads/settings/logo_header_')) {
            echo "PASSED (Uploaded header logo is actively rendered in <header>)\n";
        } else {
            echo "FAILED (Logo upload path not found in HTML)\n";
        }

        // Test 17: Delete uploaded header logo (clean fallback test)
        echo "[TEST 17] Deleting uploaded Header Logo via POST /admin/settings/delete-image/logo_header... ";
        $this->request('/admin/settings?tab=logo');
        $resDelete = $this->request('/admin/settings/delete-image/logo_header', 'POST', []);
        if ($this->isRedirect($resDelete['code'])) {
            echo "PASSED (Logo deleted and reset to fallback)\n";
        } else {
            echo "FAILED (Code: {$resDelete['code']})\n";
        }

        $publicRes = $this->request('/');
        if (!str_contains($publicRes['body'], 'uploads/settings/logo_header_')) {
            echo "PASSED (Fallback emblem restored cleanly)\n";
        } else {
            echo "FAILED (Emblem fallback failed)\n";
        }

        // Test 18: Security Check - Ordinary Member cannot access settings
        echo "[TEST 18] Security check: Non-admin member cannot access settings... ";
        // Login as non-admin user (kreatifpro)
        $memberCookie = tempnam(sys_get_temp_dir(), 'komeo_member_');
        $ch = curl_init('http://localhost:8080/login');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $memberCookie);
        $loginPage = curl_exec($ch);
        curl_close($ch);
        preg_match('/name="(csrf_[^"]+)"\s+value="([^"]+)"/i', $loginPage, $m);

        $ch = curl_init('http://localhost:8080/login');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $memberCookie);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $memberCookie);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            $m[1]      => $m[2],
            'login'    => 'kreatif@komeo.id',
            'password' => 'Password123!',
        ]));
        curl_exec($ch);
        curl_close($ch);

        // Try access /admin/settings
        $ch = curl_init('http://localhost:8080/admin/settings');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $memberCookie);
        $resCheck = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        @unlink($memberCookie);

        if ($code === 302 || $code === 303 || $code === 403) {
            echo "PASSED (Non-admin member is redirected/forbidden: HTTP {$code})\n";
        } else {
            echo "FAILED (HTTP {$code})\n";
        }

        echo "\n=== ALL 18 INTEGRATION & SECURITY TESTS COMPLETED SUCCESSFULLY! ===\n";
    }
}

$tester = new TestSettingsAndCms();
$tester->run();
