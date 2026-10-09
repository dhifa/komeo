<?php
/**
 * Reusable Member Card Component (Refined UI/UX)
 * @var array $item
 * @var bool|null $isVendorView
 */
$showCity = (site_setting('Directory.show_city', '1') === '1') && (! empty($item['show_location'])) && (! empty($item['city']) || ! empty($item['province']));
$showSpecs = (site_setting('Directory.show_specializations', '1') === '1') && ! empty($item['specializations']);
$isBusiness = ($item['member_type'] ?? '') === 'business';
$isVerified = ($item['membership_status'] ?? '') === 'active';

// Resolve Display Name & Monogram Initials
$displayName = $item['display_name'] ?: $item['full_name'] ?: 'Member';
$initials = '';
$words = preg_split('/\s+/', trim($displayName));
if (! empty($words)) {
    $initials .= mb_substr($words[0], 0, 1);
    if (count($words) > 1) {
        $initials .= mb_substr(end($words), 0, 1);
    }
}
$initials = strtoupper($initials ?: 'K');

$bizName = $item['business_name'] ?: $displayName;
$bizInitials = '';
$bWords = preg_split('/\s+/', trim($bizName));
if (! empty($bWords)) {
    $bizInitials .= mb_substr($bWords[0], 0, 1);
    if (count($bWords) > 1) {
        $bizInitials .= mb_substr(end($bWords), 0, 1);
    }
}
$bizInitials = strtoupper($bizInitials ?: 'V');

// Photo & Logo File Existence Checks (Local fallbacks)
$hasPhoto = ! empty($item['photo_path']) && file_exists(FCPATH . $item['photo_path']);
$photoUrl = $hasPhoto ? base_url($item['photo_path']) : null;

$hasLogo = ! empty($item['company_logo_path']) && file_exists(FCPATH . $item['company_logo_path']);
$logoUrl = $hasLogo ? base_url($item['company_logo_path']) : null;
?>

<div class="h-full flex flex-col bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xs hover:shadow-xl hover:border-brand-400 transition-all duration-300 overflow-hidden group relative">
    
    <!-- Top accent bar -->
    <div class="h-1.5 bg-gradient-to-r from-brand-600 via-indigo-500 to-brand-400 shrink-0"></div>

    <!-- Main Card Body -->
    <div class="p-5 sm:p-6 flex-1 flex flex-col gap-3.5">
        
        <!-- Header: Avatar/Logo & Status Badges -->
        <div class="flex items-start justify-between gap-3">
            
            <!-- Avatar / Business Emblem Container -->
            <div class="relative shrink-0">
                <?php if ($isBusiness && $hasLogo): ?>
                    <img src="<?= esc($logoUrl) ?>" 
                         alt="<?= esc($bizName) ?>" 
                         loading="lazy"
                         class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl object-contain p-1.5 bg-white border border-slate-200/70 shadow-xs group-hover:scale-102 transition-transform duration-200">
                    <?php if ($hasPhoto): ?>
                        <img src="<?= esc($photoUrl) ?>" 
                             alt="<?= esc($item['full_name']) ?>" 
                             class="w-6 h-6 rounded-lg object-cover absolute -bottom-1 -right-1 ring-2 ring-white shadow-xs" 
                             title="PIC: <?= esc($item['full_name']) ?>">
                    <?php endif; ?>
                <?php elseif ($isBusiness): ?>
                    <!-- Elegant Business Logo Fallback -->
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-br from-indigo-800 via-brand-700 to-indigo-900 text-white font-extrabold text-base sm:text-lg flex flex-col items-center justify-center shadow-xs border border-white/20 select-none">
                        <span><?= esc($bizInitials) ?></span>
                        <span class="text-[8px] uppercase tracking-wider font-semibold opacity-75">Vendor</span>
                    </div>
                <?php elseif ($hasPhoto): ?>
                    <!-- Individual Photo -->
                    <img src="<?= esc($photoUrl) ?>" 
                         alt="<?= esc($displayName) ?>" 
                         loading="lazy"
                         class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl object-cover shadow-xs ring-2 ring-slate-100 group-hover:ring-brand-200 transition-all duration-200">
                <?php else: ?>
                    <!-- Elegant Individual Avatar Fallback -->
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-br from-brand-600 via-indigo-600 to-brand-700 text-white font-extrabold text-base sm:text-lg flex items-center justify-center shadow-xs border border-white/20 select-none">
                        <span><?= esc($initials) ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Badges Cluster -->
            <div class="flex flex-col items-end gap-1.5 shrink-0">
                <?php if (! empty($item['is_featured'])): ?>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200/80 shadow-2xs">
                        <svg class="w-3 h-3 text-amber-500 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        Featured
                    </span>
                <?php endif; ?>

                <?php if ($isVerified): ?>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/70" title="Status Keanggotaan Aktif">
                        <svg class="w-2.5 h-2.5 fill-current text-emerald-500" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                        Anggota Aktif
                    </span>
                <?php endif; ?>

                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold <?= $isBusiness ? 'bg-indigo-50 text-indigo-700 border border-indigo-100' : 'bg-slate-100 text-slate-600' ?>">
                    <?= $isBusiness ? '🏢 Vendor' : '👤 Crew' ?>
                </span>
            </div>
        </div>

        <!-- Typography Hierarchy: Name, Business Name, Location -->
        <div class="space-y-1">
            <h3 class="text-base sm:text-lg font-bold text-slate-900 group-hover:text-brand-600 transition-colors line-clamp-1 leading-snug flex items-center flex-wrap gap-1">
                <a href="<?= base_url('member/' . esc($item['username'])) ?>">
                    <?= esc($displayName) ?>
                </a>
                <?php if (! $isBusiness): ?>
                    <?= komeo_verification_badge($item, 'individual', $item['membership_status'] ?? '', 'sm') ?>
                <?php endif; ?>
            </h3>

            <?php if ($isBusiness && ! empty($item['business_name'])): ?>
                <p class="text-xs sm:text-sm font-semibold text-slate-700 line-clamp-1 flex items-center flex-wrap gap-1.5">
                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span><?= esc($item['business_name']) ?></span>
                    <?= komeo_verification_badge($item, 'business', $item['membership_status'] ?? '', 'sm') ?>
                </p>
            <?php elseif ($isBusiness): ?>
                <div>
                    <?= komeo_verification_badge($item, 'business', $item['membership_status'] ?? '', 'sm') ?>
                </div>
            <?php endif; ?>

            <?php if ($showCity): ?>
                <p class="text-xs font-medium text-slate-500 flex items-center gap-1 line-clamp-1">
                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span><?= esc(trim(($item['city'] ? $item['city'] . ', ' : '') . ($item['province'] ?? ''), ', ')) ?></span>
                </p>
            <?php endif; ?>
        </div>

        <!-- Primary Category & Experience Pill -->
        <div class="flex flex-wrap items-center gap-1.5">
            <?php if (! empty($item['category_name'])): ?>
                <a href="<?= base_url('kategori/' . esc($item['category_slug'])) ?>" 
                   class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-brand-50 text-brand-700 hover:bg-brand-100 transition border border-brand-200/70">
                    <?= esc($item['category_name']) ?>
                </a>
            <?php endif; ?>

            <?php if (! empty($item['years_of_experience'])): ?>
                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-600">
                    <?= (int) $item['years_of_experience'] ?> Thn Pengalaman
                </span>
            <?php endif; ?>
        </div>

        <!-- Specialization Tags -->
        <?php if ($showSpecs): ?>
            <div class="flex flex-wrap gap-1.5">
                <?php 
                $specList = array_slice($item['specializations'], 0, 3);
                $remainingSpecs = count($item['specializations']) - count($specList);
                ?>
                <?php foreach ($specList as $sp): ?>
                    <a href="<?= base_url('member?specialization=' . urlencode($sp['slug'])) ?>" 
                       class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-indigo-50/70 text-indigo-700 border border-indigo-100 hover:bg-indigo-100 transition">
                        <?= esc($sp['name']) ?>
                    </a>
                <?php endforeach; ?>
                <?php if ($remainingSpecs > 0): ?>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-500">
                        +<?= $remainingSpecs ?>
                    </span>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Custom Member Badges (Phase 5 Extension - Max 3 on compact cards) -->
        <?php 
        $customBadges = [];
        if ($isVerified && ! empty($item['user_id']) && (! isset($item['is_public']) || (int) $item['is_public'] === 1)) {
            $customBadges = komeo_member_active_badges((int) $item['user_id'], 3);
        }
        ?>
        <?php if (! empty($customBadges)): ?>
            <div class="flex flex-wrap items-center gap-1.5 pt-0.5">
                <?php foreach ($customBadges as $cb): ?>
                    <?= komeo_render_badge($cb, 'xs', true) ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Bio Excerpt -->
        <?php if (! empty($item['bio'])): ?>
            <p class="text-xs text-slate-500 leading-relaxed line-clamp-2 pt-1 border-t border-slate-100">
                <?= esc(mb_strimwidth(strip_tags($item['bio']), 0, 110, '...')) ?>
            </p>
        <?php elseif (! empty($item['business_description'])): ?>
            <p class="text-xs text-slate-500 leading-relaxed line-clamp-2 pt-1 border-t border-slate-100">
                <?= esc(mb_strimwidth(strip_tags($item['business_description']), 0, 110, '...')) ?>
            </p>
        <?php endif; ?>

        <!-- Portfolios Preview (Clean thumbnails with elegant fallback) -->
        <?php if (! empty($item['portfolios'])): ?>
            <div class="pt-2 border-t border-slate-100 mt-auto">
                <div class="flex items-center justify-between text-[11px] font-bold text-slate-500 mb-2">
                    <span>Portofolio Event</span>
                    <span class="text-brand-600 font-bold"><?= count($item['portfolios']) ?> Proyek</span>
                </div>
                <div class="grid grid-cols-3 gap-1.5">
                    <?php 
                    $previewPortfolios = array_slice($item['portfolios'], 0, 3);
                    ?>
                    <?php foreach ($previewPortfolios as $pf): ?>
                        <div class="h-14 rounded-xl bg-slate-100 overflow-hidden relative group/pf border border-slate-200/60" title="<?= esc($pf['title']) ?>">
                            <?php if (! empty($pf['cover_image']) && file_exists(FCPATH . $pf['cover_image'])): ?>
                                <img src="<?= base_url(esc($pf['cover_image'])) ?>" 
                                     alt="<?= esc($pf['title']) ?>" 
                                     loading="lazy" 
                                     class="w-full h-full object-cover group-hover/pf:scale-105 transition-transform duration-300">
                            <?php else: ?>
                                <div class="w-full h-full bg-gradient-to-br from-slate-50 to-indigo-50/70 flex flex-col items-center justify-center p-1 text-center group-hover/pf:from-brand-50 group-hover/pf:to-indigo-100/70 transition-colors">
                                    <svg class="w-4 h-4 text-indigo-400 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-[9px] font-bold text-slate-500 line-clamp-1 leading-tight"><?= esc($pf['title'] ?: 'Proyek') ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <!-- Bottom Action Card Footer (Pinned to bottom of card) -->
    <div class="mt-auto px-5 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-3 shrink-0">
        <span class="text-xs font-mono font-bold text-slate-400 truncate">
            @<?= esc($item['username']) ?>
        </span>

        <a href="<?= base_url('member/' . esc($item['username'])) ?>" 
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition shadow-xs group-hover:shadow-md">
            <span>Lihat Profil</span>
            <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
</div>
