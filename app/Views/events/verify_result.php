<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="bg-slate-50 min-h-screen py-16 flex items-center justify-center">
    <div class="max-w-xl mx-auto px-4 sm:px-6 w-full">
        <div class="bg-white rounded-3xl border border-slate-200/80 p-8 sm:p-10 shadow-xs text-center space-y-6">
            
            <?php if ($success): ?>
                <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>

                <div class="space-y-2">
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                        Verifikasi Berhasil!
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        <?= esc($message) ?>
                    </p>
                </div>

                <?php if (! empty($ticketUrl)): ?>
                    <div class="p-6 rounded-2xl bg-indigo-50/70 border border-indigo-100 space-y-4">
                        <div class="text-xs text-indigo-900 font-semibold">
                            Tiket QR resmi Anda telah aktif dan siap digunakan untuk check-in kehadiran.
                        </div>
                        <a href="<?= esc($ticketUrl) ?>" class="inline-flex items-center justify-center gap-2 w-full px-6 py-3 rounded-xl text-xs sm:text-sm font-bold text-white bg-brand-600 hover:bg-brand-700 transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                            Buka & Unduh Tiket QR
                        </a>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="w-16 h-16 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>

                <div class="space-y-2">
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                        Verifikasi Gagal
                    </h1>
                    <p class="text-xs sm:text-sm text-rose-600 leading-relaxed">
                        <?= esc($message) ?>
                    </p>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <a href="<?= base_url('kegiatan') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 transition">
                        Kembali ke Halaman Kegiatan
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>
<?= $this->endSection() ?>
