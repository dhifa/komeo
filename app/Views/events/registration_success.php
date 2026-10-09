<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="bg-slate-50 min-h-screen py-16 flex items-center justify-center">
    <div class="max-w-xl mx-auto px-4 sm:px-6 w-full">
        <div class="bg-white rounded-3xl border border-slate-200/80 p-8 sm:p-10 shadow-xs text-center space-y-6">
            
            <div class="w-16 h-16 rounded-2xl bg-indigo-50 text-brand-600 flex items-center justify-center mx-auto">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>

            <div class="space-y-2">
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                    Periksa Email Anda untuk Verifikasi
                </h1>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Halo <span class="font-bold text-slate-900"><?= esc($guestName) ?></span>, kami telah mengirimkan tautan verifikasi pendaftaran kegiatan ke:
                </p>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 font-mono text-xs font-bold text-brand-600 inline-block">
                    <?= esc($guestEmail) ?>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200/80 text-left text-xs text-amber-900 space-y-2">
                <span class="font-bold flex items-center gap-1.5 text-amber-800">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                    Langkah Berikutnya:
                </span>
                <p>1. Buka kotak masuk email Anda (periksa juga folder Spam/Promosi jika tidak muncul dalam 2 menit).</p>
                <p>2. Klik tombol "Verifikasi Pendaftaran Saya".</p>
                <p>3. Tiket QR Kegiatan resmi Anda akan langsung aktif setelah email terverifikasi.</p>
            </div>

            <?php if (! empty($verifyUrl) && ENVIRONMENT !== 'production'): ?>
                <!-- Local Testing Link Helper -->
                <div class="p-3 rounded-xl bg-slate-100 border border-slate-200 text-left text-[11px] text-slate-600">
                    <span class="font-bold block text-slate-800">Link Cepat Pengujian (Mode Uji Coba):</span>
                    <a href="<?= $verifyUrl ?>" class="text-brand-600 font-bold hover:underline break-all"><?= $verifyUrl ?></a>
                </div>
            <?php endif; ?>

            <div class="pt-4 border-t border-slate-100">
                <a href="<?= base_url('kegiatan') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition">
                    Kembali ke Halaman Kegiatan
                </a>
            </div>

        </div>
    </div>
</div>
<?= $this->endSection() ?>
