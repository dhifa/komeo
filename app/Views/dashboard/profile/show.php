<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Profil Saya</h2>
            <p class="text-xs text-slate-500 mt-1">Pratinjau detail informasi profil member Anda di KOMEO.ID</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?= base_url('dashboard/profil/edit') ?>" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-xl transition-colors shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit Profil
            </a>
            <a href="<?= base_url('member/' . esc($profile->username)) ?>" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Lihat Halaman Publik
            </a>
        </div>
    </div>

    <!-- Main Profile Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 overflow-hidden shadow-xs">
        <!-- Cover Banner -->
        <div class="h-36 sm:h-48 bg-gradient-to-r from-brand-700 via-indigo-600 to-slate-900 relative">
            <div class="absolute inset-0 bg-white/5 bg-grid-white/[0.05]"></div>
        </div>

        <div class="p-6 sm:p-8 pt-0 relative">
            <!-- Avatar & Company Logo -->
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 -mt-16 sm:-mt-20 mb-6">
                <div class="flex items-end gap-4">
                    <img src="<?= esc($profile->getAvatarUrl()) ?>" 
                         alt="<?= esc($profile->display_name) ?>" 
                         class="w-28 h-28 sm:w-32 sm:h-32 rounded-3xl object-cover ring-4 ring-white shadow-xl bg-slate-100 shrink-0">

                    <?php if ($logo = $profile->getCompanyLogoUrl()): ?>
                        <div class="relative -ml-8 mb-1" title="Logo Perusahaan">
                            <img src="<?= esc($logo) ?>" 
                                 alt="Logo Perusahaan" 
                                 class="w-12 h-12 rounded-xl object-contain bg-white p-1 ring-2 ring-slate-100 shadow-md">
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Membership Tag -->
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold <?= ($membership->status ?? 'pending') === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-800' ?>">
                        <?= ($membership->status ?? 'pending') === 'active' ? 'Anggota Terverifikasi' : 'Status: Menunggu Aktivasi' ?>
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                        <?= $profile->isBusiness() ? 'Perusahaan / Bisnis' : 'Individu / Freelancer' ?>
                    </span>
                </div>
            </div>

            <!-- Profile Title & Details -->
            <div class="space-y-4">
                <div>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        <?= esc($profile->display_name ?: $profile->full_name) ?>
                    </h3>
                    <p class="text-sm font-semibold text-slate-500">
                        @<?= esc($profile->username) ?>
                        <?php if ($profile->isBusiness() && ! empty($profile->business_name)): ?>
                            &bull; <span class="text-slate-800 font-bold"><?= esc($profile->business_name) ?></span>
                        <?php endif; ?>
                    </p>
                </div>

                <!-- Badges -->
                <div class="flex flex-wrap items-center gap-2 pt-1">
                    <?php if ($category): ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-brand-50 text-brand-700 border border-brand-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <?= esc($category['name']) ?>
                        </span>
                    <?php endif; ?>

                    <?php if (! empty($profile->city)): ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-semibold bg-slate-100 text-slate-700">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <?= esc($profile->city) ?><?= $profile->province ? ', ' . esc($profile->province) : '' ?>
                        </span>
                    <?php endif; ?>

                    <?php if ($profile->years_of_experience): ?>
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-semibold bg-slate-100 text-slate-700">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <?= $profile->years_of_experience ?> Tahun Pengalaman
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Specializations -->
                <?php if (! empty($specializations)): ?>
                    <div class="pt-2">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Spesialisasi Keahlian:</p>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($specializations as $sp): ?>
                                <span class="px-3 py-1.5 rounded-xl bg-slate-50 text-slate-800 font-bold text-xs border border-slate-200">
                                    <?= esc($sp['name']) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Bio -->
                <?php if (! empty($profile->bio)): ?>
                    <div class="pt-4 border-t border-slate-100">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tentang / Biografi:</p>
                        <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line bg-slate-50 p-5 rounded-2xl border border-slate-100">
                            <?= esc($profile->bio) ?>
                        </p>
                    </div>
                <?php endif; ?>

                <!-- Business Description -->
                <?php if ($profile->isBusiness() && ! empty($profile->business_description)): ?>
                    <div class="pt-4 border-t border-slate-100">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tentang Bisnis:</p>
                        <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line bg-slate-50 p-5 rounded-2xl border border-slate-100">
                            <?= esc($profile->business_description) ?>
                        </p>
                    </div>
                <?php endif; ?>

                <!-- Social & Contacts Links -->
                <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center gap-3">
                    <?php if (! empty($profile->whatsapp)): ?>
                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $profile->whatsapp) ?>" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            WhatsApp: <?= esc($profile->whatsapp) ?>
                        </a>
                    <?php endif; ?>

                    <?php if (! empty($profile->instagram)): ?>
                        <a href="https://instagram.com/<?= ltrim(esc($profile->instagram), '@') ?>" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors">
                            Instagram: @<?= ltrim(esc($profile->instagram), '@') ?>
                        </a>
                    <?php endif; ?>

                    <?php if (! empty($profile->tiktok)): ?>
                        <a href="https://tiktok.com/@<?= ltrim(esc($profile->tiktok), '@') ?>" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors">
                            TikTok: @<?= ltrim(esc($profile->tiktok), '@') ?>
                        </a>
                    <?php endif; ?>

                    <?php if (! empty($profile->linkedin)): ?>
                        <a href="<?= esc($profile->linkedin) ?>" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors">
                            LinkedIn
                        </a>
                    <?php endif; ?>

                    <?php if (! empty($profile->website)): ?>
                        <a href="<?= esc($profile->website) ?>" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors">
                            Website Portofolio
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Portfolios Gallery Grid -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-lg font-extrabold text-slate-900 tracking-tight">Karya & Portofolio Event</h3>
                <p class="text-xs text-slate-500 mt-0.5">Daftar perhelatan event yang pernah digarap</p>
            </div>
            <a href="<?= base_url('dashboard/portofolio') ?>" class="text-xs font-bold text-brand-600 hover:text-brand-700">
                Kelola Portofolio
            </a>
        </div>

        <?php if (empty($portfolios)): ?>
            <div class="text-center py-10 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                <p class="text-xs text-slate-500 mb-3">Belum ada portofolio yang ditambahkan ke profil Anda.</p>
                <a href="<?= base_url('dashboard/portofolio') ?>" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-xl transition-colors">
                    Tambah Portofolio Sekarang
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($portfolios as $port): ?>
                    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-xs hover:shadow-md transition-shadow flex flex-col justify-between">
                        <div>
                            <div class="aspect-video bg-slate-100 overflow-hidden relative">
                                <img src="<?= base_url(esc($port['cover_image'])) ?>" alt="<?= esc($port['title']) ?>" class="w-full h-full object-cover">
                                <span class="absolute top-3 right-3 px-2 py-0.5 rounded-md text-2xs font-extrabold bg-slate-900/80 backdrop-blur-xs text-white">
                                    <?= esc($port['project_year']) ?>
                                </span>
                            </div>
                            <div class="p-5 space-y-2">
                                <h4 class="font-extrabold text-base text-slate-900 leading-snug"><?= esc($port['title']) ?></h4>
                                <?php if (! empty($port['event_location'])): ?>
                                    <p class="text-xs text-slate-400 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <?= esc($port['event_location']) ?>
                                    </p>
                                <?php endif; ?>
                                <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line line-clamp-3">
                                    <?= esc($port['description']) ?>
                                </p>
                            </div>
                        </div>
                        <?php if (! empty($port['external_url'])): ?>
                            <div class="px-5 pb-5 pt-0">
                                <a href="<?= esc($port['external_url']) ?>" target="_blank" class="inline-flex items-center gap-1 text-xs font-bold text-brand-600 hover:text-brand-700">
                                    <span>Tautan Event</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
