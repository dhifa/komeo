<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="min-h-[60vh] flex items-center justify-center py-16 px-4 sm:px-6 lg:px-8 bg-slate-50">
    <div class="max-w-md w-full text-center bg-white p-8 sm:p-10 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
        <div class="w-16 h-16 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center mx-auto">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h2 class="text-xl font-extrabold text-slate-900 tracking-tight"><?= esc($title ?? 'Direktori Tidak Aktif') ?></h2>
        <p class="text-xs sm:text-sm text-slate-500 leading-relaxed"><?= esc($message ?? 'Fitur ini sedang tidak aktif.') ?></p>
        <div class="pt-4">
            <a href="<?= base_url() ?>" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition shadow-xs">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
