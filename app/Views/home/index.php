<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- 1. Hero Section -->
<?php if (site_setting('Homepage.section_hero_visible', '1') === '1'): ?>
<section class="relative overflow-hidden pt-12 pb-24 md:pt-20 md:pb-32 bg-gradient-to-b from-brand-50/50 via-white to-white">
    <?php if ($heroBg = site_setting('Homepage.hero_background_image')): ?>
        <div class="absolute inset-0 -z-20 bg-cover bg-center opacity-10" style="background-image: url('<?= base_url(esc($heroBg)) ?>');"></div>
    <?php endif; ?>

    <!-- Subtle background accent shapes -->
    <div class="absolute top-0 right-1/4 -z-10 w-96 h-96 bg-brand-200/40 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-10 left-1/4 -z-10 w-80 h-80 bg-indigo-200/30 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto space-y-6">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-brand-50 text-brand-700 border border-brand-200/60 shadow-xs">
                <span class="w-2 h-2 rounded-full bg-brand-600 animate-pulse"></span>
                <?= esc(site_setting('Homepage.hero_badge', 'Komunitas Resmi Industri Event Indonesia')) ?>
            </div>

            <!-- Hero Title -->
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-[1.15]">
                <?= esc(site_setting('Homepage.hero_headline', 'Satu Komunitas,')) ?><br class="hidden sm:inline">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 via-indigo-600 to-indigo-800">
                    <?= esc(site_setting('Homepage.hero_headline_highlight', 'Ribuan Peluang Kolaborasi.')) ?>
                </span>
            </h1>

            <!-- Hero Description -->
            <p class="text-base sm:text-lg text-slate-600 leading-relaxed font-normal whitespace-pre-line"><?= esc(site_setting('Homepage.hero_description', 'Tempat berkumpulnya para pelaku industri event Indonesia. Bangun koneksi, temukan kolaborasi, dan kembangkan peluang bersama KOMEO.')) ?></p>

            <!-- Buttons -->
            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-3">
                <?php 
                    $btnPrimaryLabel = site_setting('Homepage.hero_btn_primary_label', 'Gabung KOMEO');
                    $btnPrimaryUrl   = site_setting('Homepage.hero_btn_primary_url', 'register');
                ?>
                <a href="<?= (str_starts_with($btnPrimaryUrl, 'http') || str_starts_with($btnPrimaryUrl, '#')) ? esc($btnPrimaryUrl) : base_url($btnPrimaryUrl) ?>" 
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl font-bold text-white bg-brand-600 hover:bg-brand-700 shadow-md shadow-brand-500/25 hover:shadow-lg hover:shadow-brand-500/30 transition-all duration-200 group">
                    <span><?= esc($btnPrimaryLabel) ?></span>
                    <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>

                <?php 
                    $btnSecondaryLabel = site_setting('Homepage.hero_btn_secondary_label', 'Tentang Kami');
                    $btnSecondaryUrl   = site_setting('Homepage.hero_btn_secondary_url', '#tentang');
                ?>
                <?php if (!empty($btnSecondaryLabel)): ?>
                    <a href="<?= (str_starts_with($btnSecondaryUrl, 'http') || str_starts_with($btnSecondaryUrl, '#')) ? esc($btnSecondaryUrl) : base_url($btnSecondaryUrl) ?>" 
                       class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-3.5 rounded-xl font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:border-slate-300 transition-all duration-200">
                        <?= esc($btnSecondaryLabel) ?>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Stats Highlight Bar -->
            <?php if (site_setting('Homepage.section_statistics_visible', '1') === '1'): ?>
                <div class="pt-12 grid grid-cols-2 md:grid-cols-4 gap-6 border-t border-slate-100 mt-12">
                    <!-- Metric 1: Real Active Members or custom -->
                    <div class="p-4 rounded-xl bg-slate-50/70">
                        <p class="text-2xl sm:text-3xl font-extrabold text-slate-900">
                            <?php if (site_setting('Homepage.stats_use_real_members', '1') === '1'): ?>
                                <?= max((int)($totalMembers ?? 0), 1) ?>+
                            <?php else: ?>
                                <?= esc(site_setting('Homepage.stats_custom_metric1_number', '10+')) ?>
                            <?php endif; ?>
                        </p>
                        <p class="text-xs font-medium text-slate-500 mt-1">
                            <?= (site_setting('Homepage.stats_use_real_members', '1') === '1') ? 'Member Terverifikasi' : esc(site_setting('Homepage.stats_custom_metric1_label', 'Sektor Tergabung')) ?>
                        </p>
                    </div>

                    <!-- Metric 2: Real Cities or custom -->
                    <div class="p-4 rounded-xl bg-slate-50/70">
                        <p class="text-2xl sm:text-3xl font-extrabold text-slate-900">
                            <?php if (site_setting('Homepage.stats_use_real_cities', '1') === '1'): ?>
                                <?= max((int)($totalCities ?? 0), 1) ?>
                            <?php else: ?>
                                38
                            <?php endif; ?>
                        </p>
                        <p class="text-xs font-medium text-slate-500 mt-1">
                            <?= (site_setting('Homepage.stats_use_real_cities', '1') === '1') ? 'Kota & Wilayah' : 'Provinsi se-Indonesia' ?>
                        </p>
                    </div>

                    <!-- Metric 3: Custom Metric 1 -->
                    <div class="p-4 rounded-xl bg-slate-50/70">
                        <p class="text-2xl sm:text-3xl font-extrabold text-slate-900"><?= esc(site_setting('Homepage.stats_custom_metric1_number', '100%')) ?></p>
                        <p class="text-xs font-medium text-slate-500 mt-1"><?= esc(site_setting('Homepage.stats_custom_metric1_label', 'Kolaborasi Terbuka')) ?></p>
                    </div>

                    <!-- Metric 4: Custom Metric 2 -->
                    <div class="p-4 rounded-xl bg-slate-50/70">
                        <p class="text-2xl sm:text-3xl font-extrabold text-slate-900"><?= esc(site_setting('Homepage.stats_custom_metric2_number', 'Gratis')) ?></p>
                        <p class="text-xs font-medium text-slate-500 mt-1"><?= esc(site_setting('Homepage.stats_custom_metric2_label', 'Registrasi Anggota')) ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 2. Tentang KOMEO Section -->
<?php if (site_setting('Homepage.section_about_visible', '1') === '1'): ?>
<section id="tentang" class="py-20 bg-slate-50 border-y border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <!-- Left col: narrative -->
            <div class="lg:col-span-7 space-y-6">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white text-slate-700 border border-slate-200 shadow-xs">
                    <?= esc(site_setting('Homepage.about_badge', 'Mengenal Lebih Dekat')) ?>
                </div>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    <?= esc(site_setting('Homepage.about_title', 'Wadah Sinergi Terbesar bagi Para Pelaku Industri Event Indonesia')) ?>
                </h2>
                <?php if ($sub = site_setting('Homepage.about_subtitle')): ?>
                    <p class="text-xs uppercase font-bold tracking-wider text-brand-600"><?= esc($sub) ?></p>
                <?php endif; ?>
                <div class="text-slate-600 leading-relaxed space-y-4 whitespace-pre-line text-sm sm:text-base">
                    <?= esc(site_setting('Homepage.about_description', "Industri event adalah ekosistem yang dinamis dan saling bergantung.\n\nKOMEO.ID hadir sebagai jembatan profesional yang menyatukan perorangan maupun entitas bisnis untuk meruntuhkan sekat birokrasi dan membuka akses langsung antar penyedia jasa.")) ?>
                </div>

                <!-- Value Highlights -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div class="flex items-start gap-3 p-3 rounded-lg bg-white border border-slate-100">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Verifikasi Resmi</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Memastikan profil anggota valid dan terpercaya.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 rounded-lg bg-white border border-slate-100">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Peluang Proyek Cepat</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Akses cepat ke info kebutuhan vendor di tiap kota.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right col: visual card or uploaded about image -->
            <div class="lg:col-span-5">
                <?php if ($aboutImg = site_setting('Homepage.about_image')): ?>
                    <div class="relative mx-auto max-w-md rounded-2xl overflow-hidden shadow-2xl border border-slate-200/80 bg-slate-100">
                        <img src="<?= base_url(esc($aboutImg)) ?>" alt="Tentang KOMEO.ID" class="w-full h-auto object-cover">
                    </div>
                <?php else: ?>
                    <div class="relative mx-auto max-w-md bg-white rounded-2xl p-6 sm:p-8 shadow-xl shadow-slate-200/50 border border-slate-100">
                        <div class="flex items-center justify-between pb-6 border-b border-slate-100">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-brand-600 text-white font-bold flex items-center justify-center text-xl">
                                    K
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-900">Ekosistem KOMEO</h3>
                                    <p class="text-xs text-slate-500">Terbuka Untuk Perorangan & Bisnis</p>
                                </div>
                            </div>
                            <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700">Aktif</span>
                        </div>

                        <div class="mt-6 space-y-4">
                            <div class="flex items-center justify-between text-sm py-2 border-b border-slate-50">
                                <span class="text-slate-500">Event & Wedding Organizer</span>
                                <span class="font-semibold text-slate-800">Klien & Kreator</span>
                            </div>
                            <div class="flex items-center justify-between text-sm py-2 border-b border-slate-50">
                                <span class="text-slate-500">Vendor Teknis & Multimedia</span>
                                <span class="font-semibold text-slate-800">Penyedia Solusi</span>
                            </div>
                            <div class="flex items-center justify-between text-sm py-2 border-b border-slate-50">
                                <span class="text-slate-500">Talent, MC, & Pengisi Acara</span>
                                <span class="font-semibold text-slate-800">Daya Tarik Acara</span>
                            </div>
                            <div class="flex items-center justify-between text-sm py-2">
                                <span class="text-slate-500">Freelancer & Kru Profesional</span>
                                <span class="font-semibold text-slate-800">Dukungan Operasional</span>
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                            <a href="<?= base_url('register') ?>" class="block w-full py-3 px-4 rounded-xl text-center text-sm font-bold text-white bg-slate-900 hover:bg-brand-600 transition-colors">
                                Gabung Ekosistem Sekarang
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 3. Mengapa Bergabung (Benefits) Section -->
<?php if (site_setting('Homepage.section_benefits_visible', '1') === '1'): ?>
<section id="mengapa-komeo" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16 space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-brand-50 text-brand-700 border border-brand-100">
                <?= esc(site_setting('Homepage.benefits_badge', 'Keuntungan Eksklusif')) ?>
            </div>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                <?= esc(site_setting('Homepage.benefits_title', 'Mengapa Bergabung di KOMEO?')) ?>
            </h2>
            <p class="text-slate-600 text-base">
                <?= esc(site_setting('Homepage.benefits_description', 'Platform komunitas yang dirancang khusus untuk memenuhi kebutuhan nyata industri event.')) ?>
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php if (!empty($benefits)): ?>
                <?php foreach ($benefits as $b): ?>
                    <div class="p-8 rounded-2xl bg-white border border-slate-100 hover:border-brand-200 hover:shadow-xl hover:shadow-brand-500/5 transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center mb-6">
                                <?php if ($b['icon'] === 'users'): ?>
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <?php elseif ($b['icon'] === 'briefcase'): ?>
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <?php elseif ($b['icon'] === 'badge-check'): ?>
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <?php elseif ($b['icon'] === 'globe'): ?>
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                <?php elseif ($b['icon'] === 'sparkles' || $b['icon'] === 'star'): ?>
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                <?php elseif ($b['icon'] === 'camera'): ?>
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg>
                                <?php elseif ($b['icon'] === 'music'): ?>
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                                <?php elseif ($b['icon'] === 'zap'): ?>
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                <?php else: ?>
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <?php endif; ?>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-2"><?= esc($b['title']) ?></h3>
                            <p class="text-sm text-slate-600 leading-relaxed"><?= esc($b['description']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 4. Kategori Industri Event Section (Phase 5: Jelajahi Ekosistem Event) -->
<?php if (site_setting('Homepage.section_categories_visible', '1') === '1'): ?>
<section id="kategori" class="py-20 bg-slate-50 border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16 space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white text-slate-700 border border-slate-200 shadow-xs">
                Sektor Industri
            </div>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                Jelajahi Ekosistem Event
            </h2>
            <p class="text-slate-600 text-base">
                Siapa pun peran Anda dalam industri event, temukan rekanan kerja berdasarkan sektor keahlian terverifikasi.
            </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <?php if (! empty($categories)): ?>
                <?php foreach ($categories as $cat): ?>
                    <a href="<?= base_url('kategori/' . esc($cat['slug'])) ?>" 
                       class="bg-white p-5 rounded-2xl border border-slate-200/80 hover:border-brand-400 hover:shadow-lg transition-all text-center group flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 mx-auto rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center mb-3 group-hover:scale-110 group-hover:bg-brand-600 group-hover:text-white transition-all duration-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <h4 class="font-bold text-sm text-slate-900 group-hover:text-brand-600 transition-colors line-clamp-1">
                                <?= esc($cat['name']) ?>
                            </h4>
                            <p class="text-[11px] text-slate-500 mt-1 line-clamp-1">
                                <?= esc($cat['description'] ?: 'Eksplorasi Vendor & Crew') ?>
                            </p>
                        </div>
                        <div class="pt-3 mt-3 border-t border-slate-50 text-[11px] font-bold text-brand-600 flex items-center justify-center gap-1">
                            <span>Jelajahi</span>
                            <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full text-center text-xs text-slate-400 py-6">
                    Kategori industri sedang dimuat oleh sistem.
                </div>
            <?php endif; ?>
        </div>

        <div class="mt-10 text-center">
            <a href="<?= base_url('member') ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-xs font-bold text-brand-700 bg-brand-50 hover:bg-brand-100 border border-brand-200/80 transition shadow-2xs">
                <span>Lihat Semua Sektor di Direktori</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 5. Featured Members Showcase (Phase 5: Kenalan dengan Member KOMEO) -->
<?php if (site_setting('Homepage.section_members_visible', '1') === '1'): ?>
<section id="member" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 border-b border-slate-100 pb-8">
            <div class="space-y-3 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-brand-50 text-brand-700 border border-brand-100">
                    <span class="w-2 h-2 rounded-full bg-brand-600 animate-pulse"></span>
                    <span>Direktori Komunitas Resmi</span>
                </div>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Kenalan dengan Member KOMEO
                </h2>
                <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                    Jelajahi profil profesional, vendor, dan talenta terverifikasi yang siap berkolaborasi untuk menyukseskan event Anda.
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <a href="<?= base_url('member') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition shadow-sm shadow-brand-500/20">
                    <span>Buka Direktori Lengkap</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>

        <?php if (! empty($featuredMembers)): ?>
            <?php
            $featCount = count($featuredMembers);
            $featHomeGridClass = match($featCount) {
                1 => 'max-w-md mx-auto',
                2 => 'grid grid-cols-1 md:grid-cols-2 max-w-4xl mx-auto gap-6 sm:gap-8 items-stretch',
                default => 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 items-stretch',
            };
            ?>
            <div class="<?= $featHomeGridClass ?>">
                <?php foreach ($featuredMembers as $featItem): ?>
                    <?= view('directory/_member_card', ['item' => $featItem]) ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="bg-slate-50 border border-slate-200/80 rounded-3xl p-10 text-center max-w-xl mx-auto space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-brand-100 text-brand-600 flex items-center justify-center mx-auto">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <h3 class="text-base font-extrabold text-slate-900">Direktori Member Aktif</h3>
                <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                    Jadilah bagian dari jejaring profesional industri event dan tampilkan profil portofolio Anda di sini.
                </p>
                <div class="pt-2">
                    <a href="<?= base_url('register') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition shadow-xs">
                        Daftar Sebagai Anggota
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<!-- 6. Daftar Blacklist & Peringatan Industri Event (KOMEO.ID Warning Center) -->
<?php if (site_setting('Homepage.section_blacklist_visible', '1') === '1'): ?>
<section id="blacklist" class="py-20 bg-slate-50 border-t border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div class="space-y-3 max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                    <span class="w-2 h-2 rounded-full bg-rose-600 animate-ping"></span>
                    <span>Pusat Transparansi & Perlindungan Komunitas</span>
                </div>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    <?= esc(site_setting('Homepage.blacklist_title', 'Daftar Peringatan & Blacklist Industri Event')) ?>
                </h2>
                <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                    <?= esc(site_setting('Homepage.blacklist_subtitle', 'Informasi resmi pencegahan kerugian bagi ekosistem event: daftar entitas vendor, EO, dan freelance yang terbukti wanprestasi fatal, gagal bayar, atau melanggar kesepakatan kerja resmi.')) ?>
                </p>
            </div>

            <!-- Report Button -->
            <div class="shrink-0">
                <a href="#tentang" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-rose-700 bg-rose-100 hover:bg-rose-200 transition border border-rose-200/80">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Lapor Entitas Bermasalah</span>
                </a>
            </div>
        </div>

        <!-- Filter & Search Controls for Blacklist -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="relative w-full sm:w-80">
                <input type="text" 
                       id="blacklistSearchInput" 
                       onkeyup="filterBlacklist()"
                       placeholder="Cari nama pelaku, kota, pelanggaran..." 
                       class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <div class="flex items-center gap-1.5 w-full sm:w-auto overflow-x-auto pb-1 sm:pb-0">
                <button type="button" onclick="setBlacklistCategory('all')" id="btn_bl_all" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-900 text-white transition">Semua</button>
                <button type="button" onclick="setBlacklistCategory('vendor')" id="btn_bl_vendor" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 transition">Vendor</button>
                <button type="button" onclick="setBlacklistCategory('eo')" id="btn_bl_eo" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 transition">EO</button>
                <button type="button" onclick="setBlacklistCategory('freelance')" id="btn_bl_freelance" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 transition">Freelance</button>
            </div>
        </div>

        <!-- Table Display -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs" id="blacklistTable">
                    <thead class="bg-slate-900 text-white font-bold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="py-4 px-5">Nama Pelaku / Entitas</th>
                            <th class="py-4 px-5">Tipe</th>
                            <th class="py-4 px-5">Kota Domisili</th>
                            <th class="py-4 px-5">Kategori Pelanggaran</th>
                            <th class="py-4 px-5">Periode Kasus</th>
                            <th class="py-4 px-5">Status Komunitas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100" id="blacklistTbody">
                        <?php if (! empty($blacklists)): ?>
                            <?php foreach ($blacklists as $bl): ?>
                                <tr class="blacklist-row hover:bg-rose-50/30 transition-colors" data-type="<?= esc(strtolower($bl['entity_type'])) ?>">
                                    <td class="py-4 px-5">
                                        <div class="font-extrabold text-slate-900 text-sm flex items-center gap-1.5">
                                            <span><?= esc($bl['name']) ?></span>
                                        </div>
                                        <p class="text-xs text-slate-500 mt-1 leading-relaxed max-w-xl">
                                            <?= esc($bl['description']) ?>
                                        </p>
                                        <?php if (! empty($bl['evidence_notes'])): ?>
                                            <p class="text-[11px] text-slate-400 italic mt-0.5">
                                                Bukti: <?= esc($bl['evidence_notes']) ?>
                                            </p>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-4 px-5">
                                        <?php
                                        $typeBadge = match(strtolower($bl['entity_type'])) {
                                            'vendor'    => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                            'eo'        => 'bg-purple-50 text-purple-700 border-purple-200',
                                            'freelance' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            default     => 'bg-slate-100 text-slate-700 border-slate-200',
                                        };
                                        ?>
                                        <span class="inline-flex px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase border <?= $typeBadge ?>">
                                            <?= esc($bl['entity_type']) ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-5 font-medium text-slate-700">
                                        <?= esc($bl['city'] ?: '-') ?>
                                    </td>
                                    <td class="py-4 px-5 font-bold text-rose-700">
                                        <?= esc($bl['case_category']) ?>
                                    </td>
                                    <td class="py-4 px-5 text-slate-500 font-medium">
                                        <?php 
                                            $incDate = $bl['incident_date'];
                                            if (!empty($incDate) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $incDate)) {
                                                echo date('d M Y', strtotime($incDate));
                                            } else {
                                                echo esc($incDate ?: '-');
                                            }
                                        ?>
                                    </td>
                                    <td class="py-4 px-5">
                                        <?php if ($bl['status'] === 'blacklisted'): ?>
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-200 shadow-2xs">
                                                <svg class="w-3 h-3 text-rose-600 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                                BLACKLIST RESMI
                                            </span>
                                        <?php elseif ($bl['status'] === 'monitoring'): ?>
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                                DALAM PENGAWASAN
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                SELESAI / DAMAI
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">Belum ada data blacklist yang dipublikasikan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Disclaimer Footer -->
            <div class="p-4 bg-slate-50 border-t border-slate-100 text-[11px] text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p>⚠️ <em>Catatan: Data di atas dipublikasikan berdasarkan laporan resmi yang terverifikasi demi transparansi dan perlindungan industri event bersama.</em></p>
                <a href="#tentang" class="text-brand-600 hover:underline font-bold shrink-0">Kode Etik KOMEO</a>
            </div>
        </div>

    </div>
</section>

<script>
let currentCategory = 'all';

function setBlacklistCategory(cat) {
    currentCategory = cat;
    ['all', 'vendor', 'eo', 'freelance'].forEach(c => {
        const btn = document.getElementById('btn_bl_' + c);
        if (btn) {
            if (c === cat) {
                btn.className = 'px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-900 text-white transition';
            } else {
                btn.className = 'px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 transition';
            }
        }
    });
    filterBlacklist();
}

function filterBlacklist() {
    const q = (document.getElementById('blacklistSearchInput')?.value || '').toLowerCase().trim();
    const rows = document.querySelectorAll('.blacklist-row');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const type = row.getAttribute('data-type');
        const matchCat = (currentCategory === 'all' || type === currentCategory);
        const matchSearch = (q === '' || text.includes(q));
        if (matchCat && matchSearch) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
<?php endif; ?>

<!-- 7. Vendor & Crew Discovery CTA Section -->
<?php if (site_setting('Homepage.section_cta_visible', '1') === '1'): ?>
<section class="py-20 bg-gradient-to-br from-slate-900 via-brand-950 to-slate-900 text-white relative overflow-hidden">
    <div class="absolute -top-24 -right-24 w-96 h-96 bg-brand-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center space-y-8">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-white/10 text-indigo-200 border border-white/20 backdrop-blur-xs">
            <span>Pusat Kebutuhan Produksi Event</span>
        </div>

        <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight max-w-3xl mx-auto leading-tight">
            Butuh Vendor atau Crew untuk Event?
        </h2>

        <p class="text-slate-300 text-sm sm:text-lg max-w-2xl mx-auto font-normal leading-relaxed">
            Temukan vendor terpercaya untuk perlengkapan event atau rekrut kru freelance profesional untuk kelancaran produksi acara Anda di seluruh Indonesia.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-2">
            <a href="<?= base_url('cari-vendor') ?>" class="w-full sm:w-auto px-8 py-4 rounded-xl font-bold text-slate-950 bg-white hover:bg-slate-100 shadow-xl shadow-black/20 hover:scale-105 transition-all duration-200 flex items-center justify-center gap-2">
                <span>🏢 Cari Vendor Event</span>
                <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
            <a href="<?= base_url('cari-crew') ?>" class="w-full sm:w-auto px-8 py-4 rounded-xl font-bold text-white bg-brand-600 hover:bg-brand-500 shadow-xl shadow-brand-600/30 hover:scale-105 transition-all duration-200 flex items-center justify-center gap-2">
                <span>👤 Cari Crew & Freelancer</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<?= $this->endSection() ?>
