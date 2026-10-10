<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Panel Administrator - ' . site_setting('App.site_name', 'KOMEO.ID')) ?></title>
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
                        }
                    }
                }
            }
        }
    </script>
    <?= brand_colors_css() ?>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .custom-sidebar-scroll {
            scrollbar-width: thin;
            scrollbar-color: #334155 transparent;
        }
        .custom-sidebar-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .custom-sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-sidebar-scroll::-webkit-scrollbar-thumb {
            background-color: #334155;
            border-radius: 9999px;
        }
        .custom-sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background-color: #475569;
        }
    </style>
</head>
<body class="min-h-full flex antialiased bg-slate-100 text-slate-900 selection:bg-brand-500 selection:text-white">

    <!-- Mobile Sidebar Backdrop -->
    <div id="admin-sidebar-backdrop" class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-xs hidden lg:hidden"></div>

    <!-- Admin Sidebar Navigation -->
    <aside id="admin-sidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-white flex flex-col h-screen max-h-screen overflow-hidden transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out">
        <!-- Admin Brand Header (Pinned Top) -->
        <div class="h-20 shrink-0 flex items-center justify-between px-6 border-b border-slate-800 bg-slate-900">
                <a href="<?= base_url('admin') ?>" class="flex items-center gap-3">
                    <?php 
                        $adminLogo = site_setting('App.logo_footer') ?: site_setting('App.logo_header');
                    ?>
                    <?php if ($adminLogo && file_exists(FCPATH . $adminLogo)): ?>
                        <img src="<?= base_url(esc($adminLogo)) ?>" alt="<?= esc(site_setting('App.site_name', 'KOMEO.ID')) ?>" class="h-10 w-auto max-w-[170px] object-contain">
                    <?php else: ?>
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-500 to-indigo-500 flex items-center justify-center text-white font-extrabold text-xl shadow-md">
                            K
                        </div>
                        <div>
                            <span class="text-xl font-extrabold tracking-tight text-white">
                                <?= esc(site_setting('App.site_name', 'KOMEO.ID')) ?>
                            </span>
                            <span class="block text-[10px] uppercase tracking-wider font-bold text-brand-400">Panel Administrator</span>
                        </div>
                    <?php endif; ?>
                </a>
                <button id="admin-sidebar-close-btn" class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Scrollable Navigation Area (Middle Body) -->
            <div class="flex-1 min-h-0 overflow-y-auto overscroll-contain custom-sidebar-scroll">
                <!-- Admin User Badge -->
                <div class="p-5 border-b border-slate-800 bg-slate-950/40">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white font-bold flex items-center justify-center text-sm shadow-sm">
                        <?= strtoupper(substr(auth()->user()->username ?? 'A', 0, 1)) ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-white truncate">
                            <?= esc(auth()->user()->username) ?>
                        </p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-extrabold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                            SUPER ADMIN
                        </span>
                    </div>
                </div>
            </div>

            <!-- Admin Sidebar Menu -->
            <nav class="p-4 space-y-1.5">
                <?php $currentUri = uri_string(); ?>

                <!-- 1. Dashboard -->
                <a href="<?= base_url('admin') ?>" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-bold transition-colors <?= ($currentUri === 'admin' || $currentUri === 'admin/dashboard') ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800' ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span>Dashboard</span>
                </a>

                <!-- 2. Manajemen Member -->
                <?php $pendingBadgeCount = komeo_pending_members_count(); ?>
                <a href="<?= base_url('admin/members') ?>" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors <?= str_starts_with($currentUri, 'admin/members') ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800' ?>">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 <?= str_starts_with($currentUri, 'admin/members') ? 'text-white' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span>Manajemen Member</span>
                    </div>
                    <?php if ($pendingBadgeCount > 0): ?>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-500 text-slate-950 animate-pulse">
                            <?= $pendingBadgeCount ?>
                        </span>
                    <?php endif; ?>
                </a>

                <!-- Badge Member (Phase 5 Extension) -->
                <a href="<?= base_url('admin/badges') ?>" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors <?= str_starts_with($currentUri, 'admin/badges') ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800' ?>">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 <?= str_starts_with($currentUri, 'admin/badges') ? 'text-white' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        <span>Badge Member</span>
                    </div>
                </a>

                <!-- Verifikasi Identitas & Usaha (Phase 5 Extension) -->
                <?php $pendingVerifCount = komeo_pending_verifications_count(); ?>
                <a href="<?= base_url('admin/identity-verifications') ?>" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors <?= str_starts_with($currentUri, 'admin/identity-verifications') ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800' ?>">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 <?= str_starts_with($currentUri, 'admin/identity-verifications') ? 'text-white' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Verifikasi Identitas</span>
                    </div>
                    <?php if ($pendingVerifCount > 0): ?>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-500 text-white animate-pulse">
                            <?= $pendingVerifCount ?>
                        </span>
                    <?php endif; ?>
                </a>

                <!-- Role Member (Phase 6.3 Custom Member Roles) -->
                <a href="<?= base_url('admin/member-roles') ?>" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors <?= str_starts_with($currentUri, 'admin/member-roles') ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800' ?>">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 <?= str_starts_with($currentUri, 'admin/member-roles') ? 'text-white' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Role Member</span>
                    </div>
                </a>

                <!-- Live Transaksi (Phase 6.3 Module A) -->
                <a href="<?= base_url('admin/transactions') ?>" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors <?= str_starts_with($currentUri, 'admin/transactions') ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800' ?>">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 <?= str_starts_with($currentUri, 'admin/transactions') ? 'text-white' : 'text-emerald-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>Live Transaksi</span>
                    </div>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                        LIVE
                    </span>
                </a>

                <!-- 3. Direktori & Vendor (Phase 5) -->
                <a href="<?= base_url('admin/directory') ?>" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors <?= (str_starts_with($currentUri, 'admin/directory') || str_starts_with($currentUri, 'admin/settings/directory')) ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800' ?>">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 <?= (str_starts_with($currentUri, 'admin/directory') || str_starts_with($currentUri, 'admin/settings/directory')) ? 'text-white' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span>Direktori & Vendor</span>
                    </div>
                </a>

                <!-- 4. Kategori Industri -->
                <a href="<?= base_url('admin/categories') ?>" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors <?= str_starts_with($currentUri, 'admin/categories') ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800' ?>">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 <?= str_starts_with($currentUri, 'admin/categories') ? 'text-white' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        <span>Kategori Industri</span>
                    </div>
                </a>

                <!-- 4. Spesialisasi Profesi -->
                <a href="<?= base_url('admin/specializations') ?>" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors <?= str_starts_with($currentUri, 'admin/specializations') ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800' ?>">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 <?= str_starts_with($currentUri, 'admin/specializations') ? 'text-white' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        <span>Spesialisasi Profesi</span>
                    </div>
                </a>

                <!-- 5. KTA Digital -->
                <a href="<?= base_url('admin/kta') ?>" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors <?= ($currentUri === 'admin/kta' || str_starts_with($currentUri, 'admin/kta/preview')) ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800' ?>">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 <?= ($currentUri === 'admin/kta' || str_starts_with($currentUri, 'admin/kta/preview')) ? 'text-white' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                        <span>KTA Digital</span>
                    </div>
                </a>

                <!-- Scan KTA Anggota (Phase 5.5) -->
                <a href="<?= base_url('admin/kta/scan') ?>" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors <?= str_starts_with($currentUri, 'admin/kta/scan') ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800' ?>">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 <?= str_starts_with($currentUri, 'admin/kta/scan') ? 'text-white' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                        <span>Scan KTA Anggota</span>
                    </div>
                </a>

                <!-- 6. Cetak KTA Massal -->
                <a href="<?= base_url('admin/kta/bulk') ?>" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors <?= str_starts_with($currentUri, 'admin/kta/bulk') ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800' ?>">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 <?= str_starts_with($currentUri, 'admin/kta/bulk') ? 'text-white' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak KTA Massal</span>
                    </div>
                </a>

                <!-- Manajemen Kegiatan (Phase 5.5) -->
                <a href="<?= base_url('admin/events') ?>" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors <?= str_starts_with($currentUri, 'admin/events') ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800' ?>">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 <?= str_starts_with($currentUri, 'admin/events') ? 'text-white' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Manajemen Kegiatan</span>
                    </div>
                </a>

                <!-- KOMEO Connect (Phase 5.6) -->
                <a href="<?= base_url('admin/connect') ?>" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors <?= str_starts_with($currentUri, 'admin/connect') ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800' ?>">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 <?= str_starts_with($currentUri, 'admin/connect') ? 'text-white' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span>KOMEO Connect</span>
                    </div>
                </a>

                <!-- 4. Konten Website -->
                <a href="<?= base_url('admin/content/homepage') ?>" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors <?= str_starts_with($currentUri, 'admin/content') ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800' ?>">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 <?= str_starts_with($currentUri, 'admin/content') ? 'text-white' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                        <span>Konten Website</span>
                    </div>
                </a>

                <!-- 5. Pengaturan Website -->
                <a href="<?= base_url('admin/settings') ?>" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors <?= ($currentUri === 'admin/settings' || (str_starts_with($currentUri, 'admin/settings') && ! str_starts_with($currentUri, 'admin/settings/membership'))) ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800' ?>">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 <?= str_starts_with($currentUri, 'admin/settings') && ! str_starts_with($currentUri, 'admin/settings/membership') ? 'text-white' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Pengaturan Website</span>
                    </div>
                </a>

                <!-- Pengaturan Aktivasi Keanggotaan (Phase 5 Extension) -->
                <a href="<?= base_url('admin/settings/membership') ?>" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors <?= str_starts_with($currentUri, 'admin/settings/membership') ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800' ?>">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 <?= str_starts_with($currentUri, 'admin/settings/membership') ? 'text-white' : 'text-amber-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span>Pengaturan Aktivasi</span>
                    </div>
                </a>

                <!-- 6. Daftar Blacklist Industri Event -->
                <a href="<?= base_url('admin/blacklist') ?>" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors <?= str_starts_with($currentUri, 'admin/blacklist') ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800' ?>">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 <?= str_starts_with($currentUri, 'admin/blacklist') ? 'text-white' : 'text-rose-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Daftar Blacklist</span>
                    </div>
                </a>

                <!-- 7. Manajemen Role & Staf -->
                <a href="<?= base_url('admin/users') ?>" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors <?= str_starts_with($currentUri, 'admin/users') ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800' ?>">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 <?= str_starts_with($currentUri, 'admin/users') ? 'text-white' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Manajemen Role & Staf</span>
                    </div>
                </a>

                <div class="pt-4 mt-4 border-t border-slate-800">
                    <a href="<?= base_url('dashboard') ?>" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-slate-300 hover:bg-slate-800 transition-colors">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Portal Member Saya</span>
                    </a>
                </div>
            </nav>
        </div>

        <!-- Sidebar Footer (Pinned Bottom) -->
        <div class="p-4 border-t border-slate-800 shrink-0 bg-slate-900">
            <a href="<?= base_url('logout') ?>" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-rose-400 hover:bg-rose-500/10 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span>Keluar</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 lg:pl-72">
        <!-- Top bar -->
        <header class="h-20 bg-white border-b border-slate-200/80 sticky top-0 z-30 flex items-center justify-between px-4 sm:px-8">
            <div class="flex items-center gap-4">
                <button id="admin-sidebar-open-btn" class="lg:hidden p-2 rounded-xl text-slate-600 hover:bg-slate-100 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <h1 class="text-lg font-extrabold text-slate-900">Dashboard Administrator</h1>
                    <p class="text-xs text-slate-500">Ringkasan aktivitas keanggotaan KOMEO.ID</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="<?= base_url() ?>" target="_blank" class="text-xs font-semibold text-slate-600 hover:text-brand-600 px-3 py-1.5 rounded-lg hover:bg-slate-50 transition-colors inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>Website Utama</span>
                </a>
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-1 p-4 sm:p-8 max-w-7xl w-full mx-auto">
            <!-- Flash Alert Messages -->
            <?php if (session()->has('message')): ?>
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <div class="flex-1"><?= esc(session('message')) ?></div>
                </div>
            <?php endif; ?>

            <?php if (session()->has('error')): ?>
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div class="flex-1"><?= esc(session('error')) ?></div>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <!-- Mobile Drawer Scripts -->
    <script>
        const adminSidebar = document.getElementById('admin-sidebar');
        const adminBackdrop = document.getElementById('admin-sidebar-backdrop');
        const adminOpenBtn = document.getElementById('admin-sidebar-open-btn');
        const adminCloseBtn = document.getElementById('admin-sidebar-close-btn');

        function toggleAdminSidebar(open) {
            if (open) {
                adminSidebar.classList.remove('-translate-x-full');
                adminBackdrop.classList.remove('hidden');
            } else {
                adminSidebar.classList.add('-translate-x-full');
                adminBackdrop.classList.add('hidden');
            }
        }

        if (adminOpenBtn) adminOpenBtn.addEventListener('click', () => toggleAdminSidebar(true));
        if (adminCloseBtn) adminCloseBtn.addEventListener('click', () => toggleAdminSidebar(false));
        if (adminBackdrop) adminBackdrop.addEventListener('click', () => toggleAdminSidebar(false));
    </script>

    <!-- Global Website Modal & Notification Popup -->
    <?= $this->include('layouts/modal_dialog') ?>
</body>
</html>
