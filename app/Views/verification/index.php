<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="min-h-[80vh] py-12 sm:py-20 px-4 sm:px-6 lg:px-8 bg-slate-50 flex items-center justify-center">
    <div class="max-w-xl w-full">
        <!-- Verification Card Container -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xl overflow-hidden">
            <!-- Header Brand Ribbon -->
            <div class="bg-gradient-to-r from-slate-900 via-brand-950 to-slate-900 px-6 py-6 text-white text-center relative overflow-hidden">
                <div class="absolute -right-8 -bottom-8 w-40 h-40 bg-brand-500/20 rounded-full blur-2xl pointer-events-none"></div>
                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-12 h-12 rounded-2xl bg-brand-600/30 border border-brand-400/30 flex items-center justify-center mb-2.5 shadow-sm">
                        <span class="text-xl font-black text-white">K</span>
                    </div>
                    <h1 class="text-lg font-black tracking-tight text-white">SISTEM VERIFIKASI RESMI</h1>
                    <p class="text-xs text-brand-200 font-medium tracking-wider uppercase mt-0.5">KOMEO.ID — Komunitas Event Organizer Indonesia</p>
                </div>
            </div>

            <div class="p-6 sm:p-8 space-y-6">
                <?php if ($result['valid'] && $result['status'] === 'active'): ?>
                    <!-- STATE: VERIFIED ACTIVE -->
                    <div class="text-center space-y-3">
                        <div class="w-20 h-20 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto shadow-inner ring-8 ring-emerald-50">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Keanggotaan Terverifikasi
                            </span>
                            <h2 class="text-xl sm:text-2xl font-black text-slate-900 mt-2">Kartu Tanda Anggota Sah</h2>
                            <p class="text-xs text-slate-500 max-w-md mx-auto mt-1">
                                <?= esc($result['message']) ?>
                            </p>
                        </div>
                    </div>

                    <!-- Member Identity Summary Box -->
                    <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200/80 space-y-4">
                        <div class="flex items-center gap-4">
                            <?php if (! empty($result['member']['avatar_url'])): ?>
                                <img src="<?= esc($result['member']['avatar_url']) ?>" 
                                     alt="<?= esc($result['member']['display_name']) ?>" 
                                     class="w-16 h-16 rounded-2xl object-cover ring-2 ring-slate-200 shrink-0 bg-slate-200">
                            <?php else: ?>
                                <div class="w-16 h-16 rounded-2xl bg-brand-100 text-brand-700 flex items-center justify-center font-black text-xl shrink-0 ring-2 ring-slate-200">
                                    <?= strtoupper(substr($result['member']['display_name'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>

                            <div class="min-w-0">
                                <h3 class="text-base font-extrabold text-slate-900 truncate">
                                    <?= esc($result['member']['display_name']) ?>
                                </h3>
                                <?php if (! empty($result['member']['business_name'])): ?>
                                    <p class="text-xs font-semibold text-brand-600 truncate"><?= esc($result['member']['business_name']) ?></p>
                                <?php endif; ?>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    <?= esc($result['member']['member_type'] === 'business' ? 'Badan Usaha' : 'Perorangan / Freelancer') ?> &bull; <?= esc($result['member']['city']) ?>
                                </p>
                            </div>
                        </div>

                        <!-- Data Grid -->
                        <div class="grid grid-cols-2 gap-3 pt-3 border-t border-slate-200/70 text-xs">
                            <div>
                                <span class="text-slate-400 block font-medium">Nomor Anggota</span>
                                <span class="font-mono font-bold text-slate-900 text-sm tracking-wide">
                                    <?= esc($result['member']['member_number']) ?>
                                </span>
                            </div>
                            <div>
                                <span class="text-slate-400 block font-medium">Spesialisasi / Kategori</span>
                                <span class="font-bold text-slate-900 truncate block">
                                    <?= esc($result['member']['category_name']) ?>
                                </span>
                            </div>
                            <div>
                                <span class="text-slate-400 block font-medium">Tahun Bergabung</span>
                                <span class="font-bold text-slate-900"><?= esc($result['member']['joined_year']) ?></span>
                            </div>
                            <div>
                                <span class="text-slate-400 block font-medium">Tanggal Disetujui</span>
                                <span class="font-bold text-slate-900"><?= esc($result['member']['approved_date']) ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Trust Footer Notice -->
                    <div class="p-3.5 bg-emerald-50 rounded-2xl border border-emerald-200 flex items-start gap-2.5 text-xs text-emerald-900">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <p class="leading-relaxed">
                            Data keanggotaan ini telah diverifikasi secara aman secara real-time langsung melalui server resmi <strong>KOMEO.ID</strong>.
                        </p>
                    </div>

                <?php elseif ($result['valid'] && $result['status'] !== 'active'): ?>
                    <!-- STATE: INACTIVE / SUSPENDED / REJECTED -->
                    <div class="text-center space-y-3">
                        <div class="w-20 h-20 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center mx-auto shadow-inner ring-8 ring-amber-50">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-200">
                                <?= esc($result['status_title']) ?>
                            </span>
                            <h2 class="text-xl sm:text-2xl font-black text-slate-900 mt-2">Keanggotaan Tidak Aktif</h2>
                            <p class="text-xs text-slate-500 max-w-md mx-auto mt-1">
                                <?= esc($result['message']) ?>
                            </p>
                        </div>
                    </div>

                    <?php if (! empty($result['member'])): ?>
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200 text-xs space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500">Nomor Anggota:</span>
                                <span class="font-mono font-bold text-slate-700"><?= esc($result['member']['member_number']) ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500">Kategori:</span>
                                <span class="font-semibold text-slate-700"><?= esc($result['member']['category_name']) ?></span>
                            </div>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- STATE: INVALID / NOT FOUND -->
                    <div class="text-center space-y-3">
                        <div class="w-20 h-20 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto shadow-inner ring-8 ring-rose-50">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-rose-100 text-rose-800 border border-rose-200">
                                <?= esc($result['status_title']) ?>
                            </span>
                            <h2 class="text-xl sm:text-2xl font-black text-slate-900 mt-2">Kartu Tidak Dikenali</h2>
                            <p class="text-xs text-slate-500 max-w-md mx-auto mt-1">
                                <?= esc($result['message']) ?>
                            </p>
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-xs text-slate-600 text-center">
                        Pastikan Anda memindai kode QR asli dari kartu fisik resmi KOMEO.ID. Apabila Anda merasa ini kekeliruan, silakan hubungi tim pengurus KOMEO.ID.
                    </div>
                <?php endif; ?>

                <!-- Back to Portal Button -->
                <div class="pt-2 text-center">
                    <a href="<?= base_url() ?>" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali ke Beranda KOMEO.ID
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
