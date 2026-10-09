<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? site_setting('App.meta_title', 'KOMEO.ID - Komunitas Event Indonesia')) ?></title>
    
    <!-- Meta SEO -->
    <meta name="description" content="<?= esc($metaDescription ?? site_setting('App.meta_description', 'KOMEO.ID adalah wadah komunitas para pelaku industri event di Indonesia: Event Organizer, Wedding Organizer, Vendor, Talent, dan Freelancer.')) ?>">
    <?php if ($keywords = site_setting('App.meta_keywords')): ?>
        <meta name="keywords" content="<?= esc($keywords) ?>">
    <?php endif; ?>
    <meta name="robots" content="<?= esc($metaRobots ?? site_setting('App.robots_indexing', 'index, follow')) ?>">
    <?php if (! empty($canonicalUrl)): ?>
        <link rel="canonical" href="<?= esc($canonicalUrl) ?>">
    <?php endif; ?>

    <!-- Open Graph / Social Sharing -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= esc($canonicalUrl ?? current_url()) ?>">
    <meta property="og:title" content="<?= esc($ogTitle ?? site_setting('App.social_share_title') ?: ($title ?? site_setting('App.site_name', 'KOMEO.ID'))) ?>">
    <meta property="og:description" content="<?= esc($ogDescription ?? $metaDescription ?? site_setting('App.social_share_description') ?: site_setting('App.meta_description', 'Wadah resmi komunitas industri event se-Indonesia.')) ?>">
    <?php if (! empty($ogImage)): ?>
        <meta property="og:image" content="<?= esc($ogImage) ?>">
    <?php elseif ($defaultOg = site_setting('App.og_image')): ?>
        <meta property="og:image" content="<?= base_url(esc($defaultOg)) ?>">
    <?php endif; ?>

    <!-- Favicon -->
    <link rel="icon" href="<?= site_favicon_url() ?>">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: 'var(--brand-primary, #6366f1)',
                            600: 'var(--brand-primary, #4f46e5)',
                            700: 'var(--brand-secondary, #4338ca)',
                            800: '#3730a3',
                            900: '#312e81',
                        },
                        accent: 'var(--brand-accent, #f59e0b)',
                    }
                }
            }
        }
    </script>

    <!-- Dynamic Brand Colors Custom Properties -->
    <?= brand_colors_css() ?>

    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--brand-background, #ffffff);
            color: var(--brand-text, #0f172a);
        }
    </style>
</head>
<body class="min-h-full flex flex-col bg-white text-slate-900 antialiased selection:bg-brand-500 selection:text-white">

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-100 transition-all duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand Logo -->
                <a href="<?= base_url() ?>" class="flex items-center gap-3 group">
                    <?php if ($headerLogo = site_setting('App.logo_header')): ?>
                        <img src="<?= base_url(esc($headerLogo)) ?>" alt="<?= esc(site_setting('App.site_name', 'KOMEO.ID')) ?>" class="h-10 w-auto object-contain max-w-[200px]">
                    <?php else: ?>
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-indigo-500 flex items-center justify-center text-white font-extrabold text-xl shadow-md shadow-brand-500/20 group-hover:scale-105 transition-transform duration-200">
                            K
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xl font-extrabold tracking-tight text-slate-900 group-hover:text-brand-600 transition-colors">
                                <?= esc(site_setting('App.site_name', 'KOMEO.ID')) ?>
                            </span>
                            <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400">
                                <?= esc(site_setting('App.site_subtitle', 'Komunitas Event Indonesia')) ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </a>

                <!-- Desktop Navigation Menu -->
                <nav class="hidden md:flex items-center gap-7">
                    <a href="<?= base_url() ?>" class="text-sm font-semibold text-slate-700 hover:text-brand-600 transition-colors">Beranda</a>
                    <a href="<?= base_url('member') ?>" class="text-sm font-semibold <?= url_is('member') ? 'text-brand-600 font-bold' : 'text-slate-700 hover:text-brand-600' ?> transition-colors">Direktori</a>
                    <a href="<?= base_url('cari-vendor') ?>" class="text-sm font-semibold <?= url_is('cari-vendor') ? 'text-brand-600 font-bold' : 'text-slate-700 hover:text-brand-600' ?> transition-colors">Vendor</a>
                    <a href="<?= base_url('cari-crew') ?>" class="text-sm font-semibold <?= url_is('cari-crew') ? 'text-brand-600 font-bold' : 'text-slate-700 hover:text-brand-600' ?> transition-colors">Crew & Talent</a>
                    <a href="<?= base_url('kegiatan') ?>" class="text-sm font-semibold <?= str_starts_with(uri_string(), 'kegiatan') ? 'text-brand-600 font-bold' : 'text-slate-700 hover:text-brand-600' ?> transition-colors">Kegiatan</a>
                    <a href="<?= base_url('#tentang') ?>" class="text-sm font-semibold text-slate-700 hover:text-brand-600 transition-colors">Tentang</a>
                </nav>

                <!-- Auth Buttons -->
                <div class="hidden md:flex items-center gap-3">
                    <?php if (auth()->loggedIn()): ?>
                        <?php if (auth()->user()->inGroup('admin', 'superadmin')): ?>
                            <a href="<?= base_url('admin') ?>" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                Panel Admin
                            </a>
                        <?php endif; ?>
                        <a href="<?= base_url('dashboard') ?>" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-slate-900 hover:bg-brand-600 rounded-lg transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Dashboard
                        </a>
                        <a href="<?= base_url('logout') ?>" class="p-2 text-slate-400 hover:text-rose-600 rounded-lg transition-colors" title="Keluar">
                            <svg class="w-5 h-5 fill-none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </a>
                    <?php else: ?>
                        <a href="<?= base_url('login') ?>" class="px-4 py-2 text-sm font-semibold text-slate-700 hover:text-brand-600 transition-colors">
                            Masuk
                        </a>
                        <a href="<?= base_url('register') ?>" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-lg shadow-sm shadow-brand-500/20 hover:shadow-brand-500/30 transition-all">
                            Daftar Sekarang
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Mobile menu button -->
                <div class="flex md:hidden">
                    <button type="button" id="mobile-menu-btn" class="p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 focus:outline-none" aria-label="Toggle Navigation">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer -->
        <div id="mobile-menu" class="hidden md:hidden border-b border-slate-100 bg-white px-4 pt-2 pb-6 space-y-2">
            <a href="<?= base_url() ?>" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50">Beranda</a>
            <a href="<?= base_url('member') ?>" class="block px-3 py-2 rounded-lg text-sm font-semibold <?= url_is('member') ? 'text-brand-600 font-bold bg-brand-50' : 'text-slate-700 hover:bg-slate-50' ?>">Direktori Member</a>
            <a href="<?= base_url('cari-vendor') ?>" class="block px-3 py-2 rounded-lg text-sm font-semibold <?= url_is('cari-vendor') ? 'text-brand-600 font-bold bg-brand-50' : 'text-slate-700 hover:bg-slate-50' ?>">Cari Vendor</a>
            <a href="<?= base_url('cari-crew') ?>" class="block px-3 py-2 rounded-lg text-sm font-semibold <?= url_is('cari-crew') ? 'text-brand-600 font-bold bg-brand-50' : 'text-slate-700 hover:bg-slate-50' ?>">Cari Crew & Freelancer</a>
            <a href="<?= base_url('kegiatan') ?>" class="block px-3 py-2 rounded-lg text-sm font-semibold <?= str_starts_with(uri_string(), 'kegiatan') ? 'text-brand-600 font-bold bg-brand-50' : 'text-slate-700 hover:bg-slate-50' ?>">Kegiatan</a>
            <a href="<?= base_url('#tentang') ?>" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50">Tentang KOMEO</a>
            <div class="pt-4 border-t border-slate-100 flex flex-col gap-2">
                <?php if (auth()->loggedIn()): ?>
                    <a href="<?= base_url('dashboard') ?>" class="w-full text-center px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800">Buka Dashboard</a>
                    <a href="<?= base_url('logout') ?>" class="w-full text-center px-4 py-2 rounded-lg text-sm font-semibold text-rose-600 hover:bg-rose-50">Keluar</a>
                <?php else: ?>
                    <a href="<?= base_url('login') ?>" class="w-full text-center px-4 py-2.5 rounded-lg text-sm font-semibold text-slate-700 border border-slate-200 hover:bg-slate-50">Masuk</a>
                    <a href="<?= base_url('register') ?>" class="w-full text-center px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700">Daftar Sekarang</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 pt-16 pb-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">
                <!-- Col 1: Brand Info -->
                <div class="md:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        <?php if ($footerLogo = site_setting('App.logo_footer')): ?>
                            <img src="<?= base_url(esc($footerLogo)) ?>" alt="<?= esc(site_setting('App.site_name', 'KOMEO.ID')) ?>" class="h-9 w-auto object-contain">
                        <?php else: ?>
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-brand-600 to-indigo-500 flex items-center justify-center text-white font-extrabold text-lg">
                                K
                            </div>
                            <span class="text-xl font-bold tracking-tight text-white">
                                <?= esc(site_setting('App.site_name', 'KOMEO.ID')) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <p class="text-sm text-slate-400 max-w-md leading-relaxed whitespace-pre-line"><?= esc(site_setting('App.footer_description', "Satu Komunitas, Ribuan Peluang Kolaborasi.\nWadah resmi kolaborasi profesional ekosistem industri event di Indonesia: Event Organizer, Wedding Organizer, Vendor Teknis, Kreatif, dan Talenta.")) ?></p>
                    
                    <!-- Contact Info -->
                    <div class="text-xs text-slate-400 space-y-1">
                        <?php if ($city = site_setting('App.contact_city')): ?>
                            <p class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <?= esc($city) ?><?= ($addr = site_setting('App.contact_address')) ? ' &bull; ' . esc($addr) : '' ?>
                            </p>
                        <?php endif; ?>
                        
                        <?php if ($email = site_setting('App.contact_email')): ?>
                            <p class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <a href="mailto:<?= esc($email) ?>" class="hover:text-white transition-colors"><?= esc($email) ?></a>
                            </p>
                        <?php endif; ?>

                        <?php if ($phone = site_setting('App.contact_phone')): ?>
                            <p class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <span><?= esc($phone) ?></span>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Social Media Links (Only rendered when configured) -->
                    <div class="flex items-center gap-3 pt-2">
                        <?php if ($ig = site_setting('App.social_instagram')): ?>
                            <a href="<?= esc($ig) ?>" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-brand-600 text-slate-300 hover:text-white flex items-center justify-center transition-colors" title="Instagram">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </a>
                        <?php endif; ?>

                        <?php if ($linkedin = site_setting('App.social_linkedin')): ?>
                            <a href="<?= esc($linkedin) ?>" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-brand-600 text-slate-300 hover:text-white flex items-center justify-center transition-colors" title="LinkedIn">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M4.98 3.5c0 1.381-1.11 2.5-2.48 2.5s-2.48-1.119-2.48-2.5c0-1.38 1.11-2.5 2.48-2.5s2.48 1.12 2.48 2.5zm.02 4.5h-5v16h5v-16zm7.982 0h-4.968v16h4.969v-8.399c0-4.67 6.029-5.052 6.029 0v8.399h4.988v-10.131c0-7.88-8.922-7.593-11.018-3.714v-2.155z"/></svg>
                            </a>
                        <?php endif; ?>

                        <?php if ($tiktok = site_setting('App.social_tiktok')): ?>
                            <a href="<?= esc($tiktok) ?>" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-brand-600 text-slate-300 hover:text-white flex items-center justify-center transition-colors" title="TikTok">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 3 15.68 6.34 6.34 0 0 0 9.33 22a6.33 6.33 0 0 0 6.34-6.32V8.92a8.28 8.28 0 0 0 4.82 1.55v-3.48a4.84 4.84 0 0 1-.9-.3z"/></svg>
                            </a>
                        <?php endif; ?>

                        <?php if ($yt = site_setting('App.social_youtube')): ?>
                            <a href="<?= esc($yt) ?>" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-brand-600 text-slate-300 hover:text-white flex items-center justify-center transition-colors" title="YouTube">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                            </a>
                        <?php endif; ?>

                        <?php if ($fb = site_setting('App.social_facebook')): ?>
                            <a href="<?= esc($fb) ?>" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-brand-600 text-slate-300 hover:text-white flex items-center justify-center transition-colors" title="Facebook">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                        <?php endif; ?>

                        <?php if ($wa = site_setting('App.social_whatsapp')): ?>
                            <a href="<?= esc($wa) ?>" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-emerald-600 text-slate-300 hover:text-white flex items-center justify-center transition-colors" title="WhatsApp">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Col 2: Navigasi -->
                <div>
                    <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Navigasi</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="<?= base_url() ?>" class="hover:text-white transition-colors">Beranda</a></li>
                        <li><a href="<?= base_url('#tentang') ?>" class="hover:text-white transition-colors">Tentang KOMEO</a></li>
                        <li><a href="<?= base_url('#mengapa-komeo') ?>" class="hover:text-white transition-colors">Keunggulan Komunitas</a></li>
                        <li><a href="<?= base_url('#kategori') ?>" class="hover:text-white transition-colors">Kategori Industri</a></li>
                        <li><a href="<?= base_url('#member') ?>" class="hover:text-white transition-colors">Direktori Member</a></li>
                    </ul>
                </div>

                <!-- Col 3: Keanggotaan -->
                <div>
                    <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Keanggotaan</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="<?= base_url('register') ?>" class="hover:text-white transition-colors">Daftar Anggota</a></li>
                        <li><a href="<?= base_url('login') ?>" class="hover:text-white transition-colors">Masuk Akun</a></li>
                        <li><a href="<?= base_url('dashboard') ?>" class="hover:text-white transition-colors">Portal Anggota</a></li>
                        <li><a href="<?= base_url('dashboard/kta') ?>" class="hover:text-white transition-colors">KTA Digital</a></li>
                    </ul>
                </div>
            </div>

            <div class="pt-8 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <p><?= esc(site_setting('App.copyright_text', '© ' . date('Y') . ' KOMEO.ID. Hak Cipta Dilindungi Undang-Undang.')) ?></p>
                <p><?= esc(site_setting('App.footer_subtext', 'Dibangun untuk kemajuan ekosistem event Indonesia.')) ?></p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');
        if (btn && menu) {
            btn.addEventListener('click', () => {
                menu.classList.toggle('hidden');
            });
        }
    </script>

    <!-- Global Website Modal & Notification Popup -->
    <?= $this->include('layouts/modal_dialog') ?>
</body>
</html>
