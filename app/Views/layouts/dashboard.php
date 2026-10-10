<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Dashboard Member - KOMEO.ID') ?></title>
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
            scrollbar-color: #cbd5e1 transparent;
        }
        .custom-sidebar-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .custom-sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-sidebar-scroll::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 9999px;
        }
        .custom-sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background-color: #94a3b8;
        }
    </style>
</head>
<body class="min-h-full flex antialiased bg-slate-50 text-slate-900 selection:bg-brand-500 selection:text-white">

    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebar-backdrop" class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-xs hidden lg:hidden"></div>

    <?php 
        $currentUri = uri_string();
    ?>

    <!-- Sidebar Navigation -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-slate-200/80 flex flex-col h-screen max-h-screen overflow-hidden transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out">
        <!-- Sidebar Brand Header (Pinned Top) -->
        <div class="h-20 shrink-0 flex items-center justify-between px-6 border-b border-slate-100 bg-white">
                <a href="<?= base_url() ?>" class="flex items-center gap-3 group">
                    <?php if ($headerLogo = site_setting('App.logo_header')): ?>
                        <img src="<?= base_url(esc($headerLogo)) ?>" alt="KOMEO.ID" class="h-9 w-auto object-contain max-w-[170px]">
                    <?php else: ?>
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-indigo-500 flex items-center justify-center text-white font-extrabold text-xl shadow-md shadow-brand-500/20">
                            K
                        </div>
                        <div>
                            <span class="text-xl font-extrabold tracking-tight text-slate-900">
                                KOMEO<span class="text-brand-600">.ID</span>
                            </span>
                            <span class="block text-[10px] uppercase tracking-wider font-semibold text-slate-400">Portal Member</span>
                        </div>
                    <?php endif; ?>
                </a>
                <button id="sidebar-close-btn" class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Scrollable Middle Body (Member Sidebar) -->
            <div class="flex-1 min-h-0 overflow-y-auto overscroll-contain custom-sidebar-scroll">
                <!-- Member Identity Summary Card -->
                <div class="p-5 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <?php 
                        $avatarUrl = ! empty($profile) && method_exists($profile, 'getAvatarUrl') 
                            ? $profile->getAvatarUrl() 
                            : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->username ?? 'M') . '&background=4f46e5&color=ffffff&bold=true';
                    ?>
                    <img src="<?= esc($avatarUrl) ?>" alt="Avatar" class="w-11 h-11 rounded-xl object-cover border border-slate-200/80 shadow-xs shrink-0">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-900 truncate">
                            <?= esc($profile->display_name ?? $profile->full_name ?? auth()->user()->username) ?>
                        </p>
                        <p class="text-xs text-slate-500 truncate">
                            @<?= esc($profile->username ?? auth()->user()->username) ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="p-4 space-y-1.5">
                <!-- 1. Dashboard -->
                <a href="<?= base_url('dashboard') ?>" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-bold transition-colors <?= $currentUri === 'dashboard' ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
                    <svg class="w-5 h-5 <?= $currentUri === 'dashboard' ? 'text-brand-600' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Dashboard</span>
                </a>

                <!-- 2. Profil Saya -->
                <a href="<?= base_url('dashboard/profil') ?>" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-bold transition-colors <?= $currentUri === 'dashboard/profil' ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
                    <svg class="w-5 h-5 <?= $currentUri === 'dashboard/profil' ? 'text-brand-600' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>Profil Saya</span>
                </a>

                <!-- 3. Edit Profil -->
                <a href="<?= base_url('dashboard/profil/edit') ?>" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-bold transition-colors <?= $currentUri === 'dashboard/profil/edit' ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
                    <svg class="w-5 h-5 <?= $currentUri === 'dashboard/profil/edit' ? 'text-brand-600' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Edit Profil</span>
                </a>

                <!-- Verifikasi Identitas & Usaha -->
                <a href="<?= base_url('dashboard/verifikasi-identitas') ?>" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-bold transition-colors <?= str_starts_with($currentUri, 'dashboard/verifikasi-identitas') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
                    <svg class="w-5 h-5 <?= str_starts_with($currentUri, 'dashboard/verifikasi-identitas') ? 'text-brand-600' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>Verifikasi Identitas</span>
                </a>

                <!-- 4. Portofolio Foto & Proyek -->
                <a href="<?= base_url('dashboard/portofolio') ?>" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-bold transition-colors <?= str_starts_with($currentUri, 'dashboard/portofolio') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
                    <svg class="w-5 h-5 <?= str_starts_with($currentUri, 'dashboard/portofolio') ? 'text-brand-600' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Portofolio</span>
                </a>

                <!-- 5. CV & Dokumen Profesional (Phase 5.6) -->
                <a href="<?= base_url('dashboard/dokumen') ?>" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-bold transition-colors <?= str_starts_with($currentUri, 'dashboard/dokumen') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
                    <svg class="w-5 h-5 <?= str_starts_with($currentUri, 'dashboard/dokumen') ? 'text-brand-600' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>CV & Portofolio</span>
                </a>

                <!-- 6. Pesan & Permintaan (Phase 5.6) -->
                <a href="<?= base_url('dashboard/permintaan') ?>" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-bold transition-colors <?= str_starts_with($currentUri, 'dashboard/permintaan') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
                    <svg class="w-5 h-5 <?= str_starts_with($currentUri, 'dashboard/permintaan') ? 'text-brand-600' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    <span>Pesan & Permintaan</span>
                </a>

                <!-- 7. Kegiatan Saya (Phase 5.5) -->
                <a href="<?= base_url('dashboard/kegiatan') ?>" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-bold transition-colors <?= str_starts_with($currentUri, 'dashboard/kegiatan') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
                    <svg class="w-5 h-5 <?= str_starts_with($currentUri, 'dashboard/kegiatan') ? 'text-brand-600' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Kegiatan Saya</span>
                </a>

                <!-- 8. KTA Digital -->
                <a href="<?= base_url('dashboard/kta') ?>" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-bold transition-colors <?= str_starts_with($currentUri, 'dashboard/kta') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
                    <svg class="w-5 h-5 <?= str_starts_with($currentUri, 'dashboard/kta') ? 'text-brand-600' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                    <span>KTA Digital</span>
                </a>

                <!-- 9. Live Transaksi KOMEO (Phase 6.3 Module A) -->
                <a href="<?= base_url('dashboard/transaksi') ?>" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-bold transition-colors <?= str_starts_with($currentUri, 'dashboard/transaksi') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 <?= str_starts_with($currentUri, 'dashboard/transaksi') ? 'text-brand-600' : 'text-emerald-500' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>Live Transaksi</span>
                    </div>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-emerald-100 text-emerald-800">
                        LIVE
                    </span>
                </a>

                <?php if (auth()->user()->inGroup('admin', 'superadmin')): ?>
                    <div class="pt-3 mt-3 border-t border-slate-100">
                        <a href="<?= base_url('admin') ?>" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-bold text-indigo-700 bg-indigo-50/80 hover:bg-indigo-100 transition-colors">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>Panel Admin</span>
                        </a>
                    </div>
                <?php endif; ?>
            </nav>
        </div>

        <!-- Sidebar Footer: Logout (Pinned Bottom) -->
        <div class="p-4 border-t border-slate-100 shrink-0 bg-white">
            <a href="<?= base_url('logout') ?>" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-rose-600 hover:bg-rose-50 transition-colors">
                <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span>Keluar Akun</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="lg:pl-72 flex flex-col flex-1 min-w-0">
        <!-- Topbar Header -->
        <header class="h-20 bg-white border-b border-slate-200/80 px-4 sm:px-6 lg:px-8 flex items-center justify-between sticky top-0 z-30">
            <div class="flex items-center gap-4">
                <button id="sidebar-open-btn" class="lg:hidden p-2 rounded-xl text-slate-600 hover:bg-slate-100 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">
                    <?= esc($title ?? 'Dashboard Member') ?>
                </h1>
            </div>

            <div class="flex items-center gap-3 sm:gap-4">
                <a href="<?= base_url() ?>" target="_blank" class="hidden sm:inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Lihat Website
                </a>

                <?php if (! empty($profile->username)): ?>
                    <a href="<?= base_url('member/' . esc($profile->username)) ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-brand-700 bg-brand-50 hover:bg-brand-100 rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Profil Publik
                    </a>
                <?php endif; ?>
            </div>
        </header>

        <!-- Flash Alert Messages -->
        <div class="px-4 sm:px-6 lg:px-8 pt-6">
            <?php if (session()->getFlashdata('message')): ?>
                <div class="p-4 mb-4 text-sm font-semibold text-emerald-800 bg-emerald-50 rounded-2xl border border-emerald-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span><?= session()->getFlashdata('message') ?></span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="p-4 mb-4 text-sm font-semibold text-rose-800 bg-rose-50 rounded-2xl border border-rose-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span><?= session()->getFlashdata('error') ?></span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">&times;</button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="p-4 mb-4 text-sm text-rose-800 bg-rose-50 rounded-2xl border border-rose-200">
                    <p class="font-bold mb-1">Perhatian:</p>
                    <ul class="list-disc list-inside space-y-1">
                        <?php foreach (session()->getFlashdata('errors') as $err): ?>
                            <li><?= esc($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

        <!-- Main Page Body -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <!-- Mobile Sidebar Interaction Script -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');
        const openBtn = document.getElementById('sidebar-open-btn');
        const closeBtn = document.getElementById('sidebar-close-btn');

        if (openBtn && sidebar && backdrop) {
            openBtn.addEventListener('click', () => {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            });
        }

        const closeSidebar = () => {
            if (sidebar && backdrop) {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        };

        if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
        if (backdrop) backdrop.addEventListener('click', closeSidebar);
    </script>

    <!-- Global Website Modal & Notification Popup -->
    <?= $this->include('layouts/modal_dialog') ?>
</body>
</html>
