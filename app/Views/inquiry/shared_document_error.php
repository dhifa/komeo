<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="bg-slate-50 min-h-screen py-16 flex items-center justify-center">
    <div class="max-w-md mx-auto px-4 w-full">
        <div class="bg-white rounded-3xl border border-slate-200/80 p-8 sm:p-10 shadow-xs text-center space-y-5">
            <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>

            <div class="space-y-2">
                <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">
                    <?= esc($title ?? 'Akses Dokumen Tidak Tersedia') ?>
                </h1>
                <p class="text-xs text-slate-600 leading-relaxed">
                    <?= esc($message ?? 'Tautan dokumen ini sudah tidak aktif atau izin akses telah dicabut oleh pemilik dokumen.') ?>
                </p>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <a href="<?= base_url() ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
