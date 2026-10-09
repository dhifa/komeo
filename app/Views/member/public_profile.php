<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="bg-slate-50 min-h-screen py-10 sm:py-14">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- Preview Notice Banner for Owner / Admin -->
        <?php if ($previewNotice): ?>
            <div class="p-4 sm:p-5 bg-amber-500/10 border border-amber-500/30 rounded-3xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="p-2 bg-amber-500 text-white rounded-xl shrink-0 mt-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-amber-900"><?= esc($previewNotice) ?></h4>
                        <p class="text-xs text-amber-800/80 mt-0.5">Halaman ini hanya dapat dilihat oleh Anda dan Administrator KOMEO.ID.</p>
                    </div>
                </div>

                <?php if ($isOwner): ?>
                    <a href="<?= base_url('dashboard/profil/edit') ?>" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-amber-900 bg-amber-200/60 hover:bg-amber-200 rounded-xl transition whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Ubah Pengaturan Profil
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Member Profile Hero Card -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
            <!-- Cover Banner -->
            <div class="h-44 sm:h-56 bg-gradient-to-r from-brand-700 via-indigo-600 to-slate-900 relative">
                <div class="absolute inset-0 bg-white/5 bg-grid-white/[0.05]"></div>
                
                <!-- Share Button Top-Right -->
                <div class="absolute top-4 right-4 flex items-center gap-2">
                    <button type="button" 
                            onclick="copyProfileUrl()" 
                            id="btnShare"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/90 hover:bg-white text-slate-800 text-xs font-bold shadow-md backdrop-blur-xs transition">
                        <svg class="w-3.5 h-3.5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                        <span id="shareText">Bagikan</span>
                    </button>

                    <?php if ($isOwner): ?>
                        <a href="<?= base_url('dashboard/profil/edit') ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900/80 hover:bg-slate-900 text-white text-xs font-bold shadow-md backdrop-blur-xs transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Profile Details Header -->
            <div class="px-6 sm:px-10 pb-8 pt-0 relative">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-6 -mt-20 sm:-mt-24 mb-6">
                    <!-- Photo & Logos -->
                    <div class="flex items-end gap-5">
                        <div class="relative shrink-0">
                            <img src="<?= esc($profile->getAvatarUrl()) ?>" 
                                 alt="<?= esc($profile->display_name) ?>" 
                                 class="w-32 h-32 sm:w-40 sm:h-40 rounded-3xl object-cover ring-4 ring-white shadow-xl bg-slate-100">
                        </div>

                        <?php if ($logo = $profile->getCompanyLogoUrl()): ?>
                            <div class="relative -ml-9 mb-1" title="Logo Perusahaan">
                                <img src="<?= esc($logo) ?>" 
                                     alt="Logo Perusahaan" 
                                     class="w-14 h-14 rounded-2xl object-contain bg-white p-1.5 ring-2 ring-slate-100 shadow-lg">
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Status & Contact Quick CTA -->
                    <div class="flex flex-wrap items-center gap-3">
                        <?php if ($isActive): ?>
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Anggota Terverifikasi
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Status: Menunggu Verifikasi
                            </span>
                        <?php endif; ?>

                        <!-- Contact Member CTA -->
                        <a href="<?= base_url('member/' . esc($profile->username) . '/hubungi') ?>" 
                           class="inline-flex items-center gap-2 px-5 py-2 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-xl transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            Hubungi Member
                        </a>

                        <a href="<?= base_url('member/' . esc($profile->username) . '/hubungi?purpose=cv_request') ?>" 
                           class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-brand-700 bg-brand-50 hover:bg-brand-100 border border-brand-200 rounded-xl transition shadow-xs">
                            <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Minta CV / Portofolio
                        </a>

                        <!-- WhatsApp Action CTA (if allowed & available) -->
                        <?php if ($profile->show_whatsapp && ! empty($profile->whatsapp)): ?>
                            <?php 
                            $cleanPhone = preg_replace('/[^0-9]/', '', $profile->whatsapp);
                            if (str_starts_with($cleanPhone, '0')) {
                                $cleanPhone = '62' . substr($cleanPhone, 1);
                            }
                            $waMessage = rawurlencode('Halo ' . ($profile->display_name ?: $profile->full_name) . ', saya menemukan profil Anda di KOMEO.ID dan ingin mendiskusikan peluang kerja sama / event.');
                            ?>
                            <a href="https://wa.me/<?= $cleanPhone ?>?text=<?= $waMessage ?>" 
                               target="_blank" 
                               rel="noopener noreferrer" 
                               class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-xl transition shadow-xs">
                                <svg class="w-4 h-4 fill-current text-emerald-600" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                WhatsApp
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Names & Badges -->
                <div class="space-y-3">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight flex items-center flex-wrap gap-2">
                            <span><?= esc($profile->display_name ?: $profile->full_name) ?></span>
                            <?php if (! $profile->isBusiness()): ?>
                                <?= komeo_verification_badge($verification ?? null, 'individual', $membership->status ?? '', 'md') ?>
                            <?php endif; ?>
                        </h1>

                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 text-xs text-slate-500 font-medium">
                            <span class="font-mono text-brand-600 font-bold">@<?= esc($profile->username) ?></span>

                            <?php if ($profile->isBusiness() && ! empty($profile->business_name)): ?>
                                <span>•</span>
                                <span class="font-bold text-slate-700 flex items-center gap-1.5">
                                    <span><?= esc($profile->business_name) ?></span>
                                    <?= komeo_verification_badge($verification ?? null, 'business', $membership->status ?? '', 'sm') ?>
                                </span>
                                <span class="text-slate-400">(PIC: <?= esc($profile->full_name) ?>)</span>
                            <?php elseif ($profile->isBusiness()): ?>
                                <span>•</span>
                                <?= komeo_verification_badge($verification ?? null, 'business', $membership->status ?? '', 'sm') ?>
                            <?php endif; ?>

                            <?php if ($profile->show_location && (! empty($profile->city) || ! empty($profile->province))): ?>
                                <span>•</span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <?= esc(trim(($profile->city ? $profile->city . ', ' : '') . ($profile->province ?? ''), ', ')) ?>
                                </span>
                            <?php endif; ?>

                            <?php if ($isActive && ! empty($membership->member_number)): ?>
                                <span>•</span>
                                <span class="font-mono font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-md text-[11px]" title="Nomor Anggota Resmi">
                                    No. <?= esc($membership->member_number) ?>
                                </span>
                            <?php endif; ?>

                            <?php if ($joinedTime = ($membership->approved_at ?? ($membership->joined_at ?? $profile->created_at))): ?>
                                <span>•</span>
                                <span class="text-slate-400 text-[11px]">
                                    Bergabung <?= date('M Y', strtotime($joinedTime)) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Category & Type Pills -->
                    <div class="flex flex-wrap items-center gap-2 pt-1">
                        <?php if ($category): ?>
                            <span class="px-3 py-1 rounded-xl text-xs font-bold bg-brand-50 text-brand-700 border border-brand-200">
                                <?= esc(is_array($category) ? ($category['name'] ?? '') : ($category->name ?? '')) ?>
                            </span>
                        <?php endif; ?>

                        <span class="px-3 py-1 rounded-xl text-xs font-bold bg-slate-100 text-slate-700">
                            <?= $profile->isBusiness() ? 'Perusahaan / Bisnis' : 'Individu / Freelancer' ?>
                        </span>

                        <?php if (! empty($profile->years_of_experience)): ?>
                            <span class="px-3 py-1 rounded-xl text-xs font-bold bg-slate-100 text-slate-700">
                                <?= (int) $profile->years_of_experience ?> Tahun Pengalaman
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Custom Badges Cluster (Phase 5 Extension) -->
                    <?php if (! empty($memberBadges)): ?>
                        <div class="flex flex-wrap items-center gap-2 pt-1" title="Badge Penghargaan & Kualifikasi Komunitas">
                            <?php foreach ($memberBadges as $mb): ?>
                                <?= komeo_render_badge($mb, 'sm', true) ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- 2 Column Layout: Details & Portfolios -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            <!-- Left 2 Cols: About, Specializations, and Portfolios -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- About & Biography -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-4">
                    <h3 class="text-base font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                        <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Tentang Profil
                    </h3>

                    <?php if (! empty($profile->bio)): ?>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed whitespace-pre-line">
                            <?= nl2br(esc($profile->bio)) ?>
                        </p>
                    <?php else: ?>
                        <p class="text-xs text-slate-400 italic">Member belum menambahkan deskripsi biografi.</p>
                    <?php endif; ?>

                    <?php if ($profile->isBusiness() && ! empty($profile->business_description)): ?>
                        <div class="mt-6 pt-5 border-t border-slate-100">
                            <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-2">Profil & Kapasitas Bisnis</h4>
                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed whitespace-pre-line">
                                <?= nl2br(esc($profile->business_description)) ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Specializations -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-4">
                    <h3 class="text-base font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                        <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        Spesialisasi Profesional
                    </h3>

                    <?php if (! empty($specializations)): ?>
                        <div class="flex flex-wrap gap-2.5">
                            <?php foreach ($specializations as $spec): ?>
                                <span class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-indigo-50 text-indigo-800 border border-indigo-100 flex items-center gap-1.5">
                                    <svg class="w-3 h-3 text-indigo-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    <?= esc(is_array($spec) ? ($spec['name'] ?? '') : ($spec->name ?? '')) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-xs text-slate-400 italic">Member belum memilih keahlian spesialisasi.</p>
                    <?php endif; ?>
                </div>

                <!-- Event Portfolios Gallery -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                            <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Portofolio & Dokumentasi Event
                        </h3>
                        <span class="text-xs font-bold text-slate-500"><?= count($portfolios) ?> Proyek</span>
                    </div>

                    <?php if (empty($portfolios)): ?>
                        <div class="p-8 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                            <p class="text-xs text-slate-500">Member belum mengunggah karya portofolio event.</p>
                        </div>
                    <?php else: ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <?php foreach ($portfolios as $item): ?>
                                <div class="rounded-2xl border border-slate-200/80 overflow-hidden bg-slate-50/50 hover:bg-white hover:shadow-md transition group flex flex-col justify-between">
                                    <div>
                                        <div class="h-44 w-full bg-slate-200 overflow-hidden relative cursor-pointer" onclick='openPortfolioModal(<?= json_encode($item, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
                                            <?php if (! empty($item['cover_image']) && file_exists(FCPATH . $item['cover_image'])): ?>
                                                <img src="<?= base_url(esc($item['cover_image'])) ?>" 
                                                     alt="<?= esc($item['title']) ?>" 
                                                     loading="lazy"
                                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                            <?php else: ?>
                                                <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-400">
                                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                </div>
                                            <?php endif; ?>

                                            <span class="absolute top-2.5 left-2.5 px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-900/80 backdrop-blur-xs text-white">
                                                <?= esc($item['project_year']) ?>
                                            </span>

                                            <div class="absolute inset-0 bg-slate-900/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-white/95 text-slate-900 shadow-sm backdrop-blur-xs">
                                                    Lihat Detail
                                                </span>
                                            </div>
                                        </div>

                                        <div class="p-4 space-y-2">
                                            <h4 class="text-xs font-extrabold text-slate-900 group-hover:text-brand-600 transition line-clamp-1 cursor-pointer" onclick='openPortfolioModal(<?= json_encode($item, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
                                                <?= esc($item['title']) ?>
                                            </h4>

                                            <?php if (! empty($item['event_location'])): ?>
                                                <div class="flex items-center gap-1 text-[11px] text-slate-500">
                                                    <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                    <span class="truncate"><?= esc($item['event_location']) ?></span>
                                                </div>
                                            <?php endif; ?>

                                            <p class="text-[11px] text-slate-600 line-clamp-3 leading-relaxed">
                                                <?= nl2br(esc($item['description'])) ?>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="p-4 pt-0 flex items-center justify-between gap-2 border-t border-slate-100/60 mt-2">
                                        <button type="button" 
                                                onclick='openPortfolioModal(<?= json_encode($item, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                                class="text-[11px] font-bold text-slate-600 hover:text-brand-600 transition">
                                            Detail Proyek
                                        </button>

                                        <?php if (! empty($item['external_url'])): ?>
                                            <a href="<?= esc($item['external_url']) ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-[11px] font-bold text-brand-600 hover:text-brand-700">
                                                <span>Liputan</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- CV & Professional Documents Section (Phase 5.6) -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                            <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            CV & Dokumen Profesional
                        </h3>
                    </div>

                    <div class="space-y-4">
                        <!-- Public CV (if available) -->
                        <?php if (! empty($publicCv)): ?>
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-indigo-50/60 border border-indigo-100">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-10 h-10 rounded-xl bg-brand-600 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                                        PDF
                                    </div>
                                    <div>
                                        <h4 class="text-xs sm:text-sm font-bold text-slate-900"><?= esc($publicCv['title']) ?></h4>
                                        <p class="text-[11px] text-slate-500">Curriculum Vitae Resmi • Terbuka untuk Umum</p>
                                    </div>
                                </div>
                                <a href="<?= base_url('member/' . esc($profile->username) . '/hubungi?purpose=cv_request') ?>" class="px-3.5 py-1.5 text-xs font-bold text-brand-700 bg-white hover:bg-brand-50 border border-brand-200 rounded-xl transition shadow-xs">
                                    Minta / Akses CV
                                </a>
                            </div>
                        <?php endif; ?>

                        <!-- Public Portfolios & External Links -->
                        <?php if (! empty($publicDocs)): ?>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-2">
                                <?php foreach ($publicDocs as $pDoc): ?>
                                    <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-200/80 flex items-center justify-between hover:bg-white hover:shadow-sm transition">
                                        <div class="flex items-center gap-3 min-w-0 flex-1 mr-2">
                                            <div class="w-8 h-8 rounded-lg <?= $pDoc['document_type'] === 'portfolio_external' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700' ?> flex items-center justify-center text-xs font-bold shrink-0">
                                                <?= $pDoc['document_type'] === 'portfolio_external' ? 'EXT' : 'PDF' ?>
                                            </div>
                                            <div class="truncate">
                                                <h5 class="text-xs font-bold text-slate-800 truncate"><?= esc($pDoc['title']) ?></h5>
                                                <span class="text-[10px] text-slate-400 font-medium"><?= esc($pDoc['external_platform'] ?: 'Dokumen Portofolio') ?></span>
                                            </div>
                                        </div>
                                        <?php if ($pDoc['document_type'] === 'portfolio_external' && ! empty($pDoc['external_url'])): ?>
                                            <a href="<?= esc($pDoc['external_url']) ?>" target="_blank" rel="noopener noreferrer" class="px-2.5 py-1 text-[11px] font-bold text-brand-600 hover:text-brand-800 hover:underline shrink-0">
                                                Buka
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= base_url('member/' . esc($profile->username) . '/hubungi?purpose=portfolio_request') ?>" class="px-2.5 py-1 text-[11px] font-bold text-brand-600 hover:text-brand-800 hover:underline shrink-0">
                                                Minta
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Request CV & Portfolio Notice Box -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                            <div class="text-xs text-slate-600">
                                <span class="font-bold text-slate-800 block">Butuh CV Lengkap atau Dokumen Kerja Sama?</span>
                                Ajukan permintaan resmi langsung kepada member untuk mendapatkan tautan aman dokumen terverifikasi.
                            </div>
                            <a href="<?= base_url('member/' . esc($profile->username) . '/hubungi?purpose=cv_request') ?>" class="px-4 py-2 text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 rounded-xl transition shadow-xs whitespace-nowrap">
                                Ajukan Permintaan
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right 1 Col: Contact, Social, and Verification Card -->
            <div class="space-y-6">
                <!-- Contact & Social Card -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-5">
                    <h3 class="text-sm font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Kontak & Media Sosial
                    </h3>

                    <div class="space-y-3 text-xs">
                        <!-- Website -->
                        <?php if (! empty($profile->website)): ?>
                            <a href="<?= esc($profile->website) ?>" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 hover:bg-brand-50 hover:text-brand-700 transition group border border-slate-100">
                                <div class="w-8 h-8 rounded-xl bg-white flex items-center justify-center text-slate-600 group-hover:text-brand-600 shadow-xs shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                </div>
                                <div class="truncate flex-1">
                                    <span class="block text-[10px] text-slate-400 font-bold uppercase">Website Portofolio</span>
                                    <span class="font-bold text-slate-800 group-hover:text-brand-600 truncate"><?= esc(preg_replace('#^https?://#', '', rtrim($profile->website, '/'))) ?></span>
                                </div>
                            </a>
                        <?php endif; ?>

                        <!-- Social Media Links (if permitted) -->
                        <?php if ($profile->show_social): ?>
                            <!-- Instagram -->
                            <?php if (! empty($profile->instagram)): ?>
                                <a href="https://instagram.com/<?= esc(ltrim($profile->instagram, '@')) ?>" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 hover:bg-pink-50 hover:text-pink-700 transition group border border-slate-100">
                                    <div class="w-8 h-8 rounded-xl bg-white flex items-center justify-center text-pink-600 shadow-xs shrink-0 font-bold">
                                        IG
                                    </div>
                                    <div class="truncate flex-1">
                                        <span class="block text-[10px] text-slate-400 font-bold uppercase">Instagram</span>
                                        <span class="font-bold text-slate-800 group-hover:text-pink-600 truncate">@<?= esc(ltrim($profile->instagram, '@')) ?></span>
                                    </div>
                                </a>
                            <?php endif; ?>

                            <!-- TikTok -->
                            <?php if (! empty($profile->tiktok)): ?>
                                <a href="https://tiktok.com/@<?= esc(ltrim($profile->tiktok, '@')) ?>" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 hover:bg-slate-100 transition group border border-slate-100">
                                    <div class="w-8 h-8 rounded-xl bg-white flex items-center justify-center text-slate-900 shadow-xs shrink-0 font-bold">
                                        TT
                                    </div>
                                    <div class="truncate flex-1">
                                        <span class="block text-[10px] text-slate-400 font-bold uppercase">TikTok</span>
                                        <span class="font-bold text-slate-800 truncate">@<?= esc(ltrim($profile->tiktok, '@')) ?></span>
                                    </div>
                                </a>
                            <?php endif; ?>

                            <!-- LinkedIn -->
                            <?php if (! empty($profile->linkedin)): ?>
                                <a href="<?= esc($profile->linkedin) ?>" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 hover:bg-blue-50 hover:text-blue-700 transition group border border-slate-100">
                                    <div class="w-8 h-8 rounded-xl bg-white flex items-center justify-center text-blue-600 shadow-xs shrink-0 font-bold">
                                        in
                                    </div>
                                    <div class="truncate flex-1">
                                        <span class="block text-[10px] text-slate-400 font-bold uppercase">LinkedIn</span>
                                        <span class="font-bold text-slate-800 group-hover:text-blue-600 truncate">Profil LinkedIn</span>
                                    </div>
                                </a>
                            <?php endif; ?>

                            <!-- YouTube -->
                            <?php if (! empty($profile->youtube)): ?>
                                <a href="<?= esc($profile->youtube) ?>" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 hover:bg-red-50 hover:text-red-700 transition group border border-slate-100">
                                    <div class="w-8 h-8 rounded-xl bg-white flex items-center justify-center text-red-600 shadow-xs shrink-0 font-bold">
                                        YT
                                    </div>
                                    <div class="truncate flex-1">
                                        <span class="block text-[10px] text-slate-400 font-bold uppercase">YouTube</span>
                                        <span class="font-bold text-slate-800 group-hover:text-red-600 truncate">Channel YouTube</span>
                                    </div>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- Fallback if all social media hidden / empty -->
                        <?php if (empty($profile->website) && (! $profile->show_social || (empty($profile->instagram) && empty($profile->tiktok) && empty($profile->linkedin) && empty($profile->youtube)))): ?>
                            <p class="text-xs text-slate-400 italic">Tidak ada tautan media sosial yang dipublikasikan.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Membership Community Badge -->
                <div class="bg-gradient-to-br from-brand-900 to-indigo-950 p-6 rounded-3xl text-white shadow-xl space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-white/20 text-white backdrop-blur-xs">KOMEO.ID</span>
                        <span class="text-xs text-indigo-200">Official Member</span>
                    </div>
                    <h4 class="text-sm font-extrabold text-white">Satu Komunitas, Ribuan Peluang Kolaborasi</h4>
                    <p class="text-xs text-indigo-200/80 leading-relaxed">
                        Terhubung bersama jejaring pelaku industri event se-Indonesia. Bangun kolaborasi tanpa batas untuk memajukan industri event tanah air.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Portfolio Detail Modal -->
<div id="portfolioModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/70 backdrop-blur-xs p-4 hidden transition-opacity" role="dialog" aria-modal="true" aria-labelledby="modalProjectTitle">
    <div class="bg-white rounded-3xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-slate-100 flex flex-col justify-between">
        <div class="p-6 sm:p-8 space-y-5">
            <div class="flex items-start justify-between gap-4">
                <div class="space-y-1">
                    <span id="modalProjectYear" class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-brand-50 text-brand-700 border border-brand-100"></span>
                    <h3 id="modalProjectTitle" class="text-lg sm:text-xl font-extrabold text-slate-900 leading-snug"></h3>
                    <p id="modalProjectLocation" class="text-xs text-slate-500 flex items-center gap-1.5"></p>
                </div>
                <button type="button" onclick="closePortfolioModal()" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 transition" aria-label="Tutup Modal">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Image Preview -->
            <div id="modalImageContainer" class="rounded-2xl overflow-hidden bg-slate-100 max-h-80 w-full relative">
                <img id="modalProjectImage" src="" alt="" class="w-full h-full object-cover max-h-80">
            </div>

            <!-- Modal Description -->
            <div class="space-y-2">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Deskripsi Proyek</h4>
                <p id="modalProjectDescription" class="text-xs sm:text-sm text-slate-600 leading-relaxed whitespace-pre-line"></p>
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-3 rounded-b-3xl">
            <a id="modalExternalUrl" href="#" target="_blank" rel="noopener noreferrer" class="hidden inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-brand-600 hover:text-brand-700 hover:bg-brand-50 border border-brand-200 transition">
                <span>Buka Tautan Liputan / Dokumentasi</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
            <button type="button" onclick="closePortfolioModal()" class="ml-auto px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-100 transition">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
function copyProfileUrl() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        const text = document.getElementById('shareText');
        const original = text.textContent;
        text.textContent = 'Tersalin!';
        setTimeout(() => {
            text.textContent = original;
        }, 2000);
    }).catch(err => {
        prompt('Salin tautan profil ini:', window.location.href);
    });
}

function openPortfolioModal(item) {
    if (!item) return;

    document.getElementById('modalProjectTitle').textContent = item.title || 'Detail Proyek';
    document.getElementById('modalProjectYear').textContent = item.project_year || '-';

    const locEl = document.getElementById('modalProjectLocation');
    if (item.event_location) {
        locEl.innerHTML = `<svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg> <span>${item.event_location}</span>`;
        locEl.classList.remove('hidden');
    } else {
        locEl.innerHTML = '';
        locEl.classList.add('hidden');
    }

    const imgEl = document.getElementById('modalProjectImage');
    const imgContainer = document.getElementById('modalImageContainer');
    if (item.cover_image) {
        imgEl.src = item.cover_image.startsWith('http') ? item.cover_image : ('<?= base_url() ?>' + item.cover_image);
        imgEl.alt = item.title || 'Proyek';
        imgContainer.classList.remove('hidden');
    } else {
        imgContainer.classList.add('hidden');
    }

    document.getElementById('modalProjectDescription').textContent = item.description || 'Tidak ada deskripsi rinci proyek.';

    const linkEl = document.getElementById('modalExternalUrl');
    if (item.external_url && (item.external_url.startsWith('http://') || item.external_url.startsWith('https://'))) {
        linkEl.href = item.external_url;
        linkEl.classList.remove('hidden');
    } else {
        linkEl.classList.add('hidden');
    }

    document.getElementById('portfolioModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closePortfolioModal() {
    document.getElementById('portfolioModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePortfolioModal();
    }
});

// Close when clicking modal backdrop
document.getElementById('portfolioModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closePortfolioModal();
    }
});
</script>

<?= $this->endSection() ?>
