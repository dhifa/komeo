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
                        Email Berhasil Diverifikasi!
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        <?= esc($message) ?>
                    </p>
                </div>
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
                    <a href="<?= base_url() ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 transition">
                        Kembali ke Beranda
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>
<?= $this->endSection() ?>
