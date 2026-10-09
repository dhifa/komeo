<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Autentikasi - ' . site_setting('App.site_name', 'KOMEO.ID')) ?></title>
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
    </style>
</head>
<body class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-slate-50 text-slate-900 antialiased selection:bg-brand-500 selection:text-white">

    <!-- Brand Header -->
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <a href="<?= base_url() ?>" class="inline-flex items-center gap-3 group">
            <?php if ($authLogo = site_setting('App.logo_header')): ?>
                <img src="<?= base_url(esc($authLogo)) ?>" alt="<?= esc(site_setting('App.site_name', 'KOMEO.ID')) ?>" class="h-12 w-auto mx-auto object-contain max-w-[220px]">
            <?php else: ?>
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-brand-600 to-indigo-500 flex items-center justify-center text-white font-extrabold text-2xl shadow-md shadow-brand-500/20 group-hover:scale-105 transition-transform duration-200">
                    K
                </div>
                <div class="text-left">
                    <span class="text-2xl font-extrabold tracking-tight text-slate-900">
                        <?= esc(site_setting('App.site_name', 'KOMEO.ID')) ?>
                    </span>
                    <span class="block text-[11px] uppercase tracking-wider font-semibold text-slate-400">
                        <?= esc(site_setting('App.site_subtitle', 'Komunitas Event Indonesia')) ?>
                    </span>
                </div>
            <?php endif; ?>
        </a>
    </div>

    <!-- Auth Card Container -->
    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-xl px-4 sm:px-0">
        <!-- Flash Alert Messages -->
        <?php if (session()->has('message')): ?>
            <div class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-3 shadow-xs">
                <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <div class="flex-1"><?= esc(session('message')) ?></div>
            </div>
        <?php endif; ?>

        <?php if (session()->has('error')): ?>
            <div class="mb-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start gap-3 shadow-xs">
                <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div class="flex-1"><?= esc(session('error')) ?></div>
            </div>
        <?php endif; ?>

        <?php if (session()->has('errors')): ?>
            <div class="mb-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-xs space-y-1">
                <div class="font-semibold text-rose-900 flex items-center gap-1.5 mb-1.5">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Mohon periksa kesalahan berikut:
                </div>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    <?php foreach (session('errors') as $fieldError): ?>
                        <li><?= esc($fieldError) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="bg-white py-8 px-6 shadow-xl shadow-slate-200/50 rounded-2xl border border-slate-100 sm:px-10">
            <?= $this->renderSection('content') ?>
        </div>

        <div class="mt-8 text-center text-xs text-slate-500">
            <a href="<?= base_url() ?>" class="hover:text-brand-600 transition-colors inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Beranda
            </a>
            <div class="mt-2">
                &copy; <?= date('Y') ?> KOMEO.ID. Satu Komunitas, Ribuan Peluang Kolaborasi.
            </div>
        </div>
    </div>
</body>
</html>
