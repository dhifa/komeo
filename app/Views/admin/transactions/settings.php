<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div>
        <a href="<?= base_url('admin/transactions') ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 hover:text-white transition-colors mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Live Transaksi
        </a>
        <h1 class="text-2xl font-black tracking-tight text-white">Pengaturan Live Transaksi & Privasi</h1>
        <p class="text-sm text-slate-400 mt-1">
            Konfigurasi kebijakan privasi finansial global dan parameter auto-refresh untuk portal anggota KOMEO.
        </p>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="p-4 rounded-2xl bg-emerald-950/40 border border-emerald-500/30 text-emerald-200 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span><?= session()->getFlashdata('success') ?></span>
        </div>
    <?php endif; ?>

    <!-- Settings Form -->
    <div class="p-6 rounded-3xl bg-slate-900/80 border border-slate-800 shadow-sm">
        <form method="post" action="<?= base_url('admin/transactions/settings') ?>" class="space-y-6">
            <?= csrf_field() ?>

            <!-- Global Monetary Policy -->
            <div class="space-y-3">
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>Kebijakan Tampilan Nominal Transaksi (Global)</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Menentukan batas tertinggi perlindungan keterbukaan informasi uang kepada anggota komunitas.
                    </p>
                </div>

                <div class="space-y-3 pt-2">
                    <label class="p-4 rounded-2xl border <?= ($policy === 'hide_all') ? 'border-brand-500 bg-brand-950/20' : 'border-slate-800 bg-slate-950/50' ?> flex items-start gap-3 cursor-pointer hover:border-slate-700 transition-colors">
                        <input type="radio" name="monetary_visibility_policy" value="hide_all" <?= ($policy === 'hide_all') ? 'checked' : '' ?> class="mt-1 text-brand-600 focus:ring-brand-500 bg-slate-900 border-slate-700">
                        <div>
                            <strong class="text-sm font-bold text-white block">Sembunyikan Semua Nominal (Bawaan / Direkomendasikan)</strong>
                            <p class="text-xs text-slate-400 mt-0.5 leading-relaxed">
                                Seluruh informasi angka rupiah, nominal DP, termin, dan sisa saldo disembunyikan total dari anggota aktif. Anggota hanya melihat tahapan progres pekerjaan dan status pembayaran secara kualitatif (Lunas, DP Diterima, dll).
                            </p>
                        </div>
                    </label>

                    <label class="p-4 rounded-2xl border <?= ($policy === 'per_transaction') ? 'border-brand-500 bg-brand-950/20' : 'border-slate-800 bg-slate-950/50' ?> flex items-start gap-3 cursor-pointer hover:border-slate-700 transition-colors">
                        <input type="radio" name="monetary_visibility_policy" value="per_transaction" <?= ($policy === 'per_transaction') ? 'checked' : '' ?> class="mt-1 text-brand-600 focus:ring-brand-500 bg-slate-900 border-slate-700">
                        <div>
                            <strong class="text-sm font-bold text-white block">Izinkan Admin Memilih Per Transaksi</strong>
                            <p class="text-xs text-slate-400 mt-0.5 leading-relaxed">
                                Administrator dapat mengatur visibilitas per transaksi: sembunyikan nominal, hanya nilai total, total beserta persentase pelunasan, atau rincian lengkap pembayaran (tanpa mengungkap rekening bank).
                            </p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Auto-Refresh / AJAX Polling Settings -->
            <div class="pt-6 border-t border-slate-800 space-y-4">
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Interval Pembaruan Otomatis (Live Polling)</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Frekuensi browser anggota melakukan sinkronisasi pembaruan status transaksi tanpa reload halaman.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">
                            Interval Polling (Detik) <span class="text-rose-400">*</span>
                        </label>
                        <input type="number" name="live_refresh_interval" min="15" max="300" value="<?= esc($interval) ?>" required class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-brand-500">
                        <p class="text-[11px] text-slate-500 mt-1">Standar default: 60 detik. Minimal 15 detik.</p>
                    </div>

                    <div class="flex items-center">
                        <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800 flex items-center gap-3 w-full">
                            <input type="checkbox" name="enable_member_live_ticker" id="enable_member_live_ticker" value="1" <?= ($ticker === '1') ? 'checked' : '' ?> class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 bg-slate-900 border-slate-700">
                            <label for="enable_member_live_ticker" class="text-xs text-slate-300 select-none cursor-pointer">
                                <strong class="text-white block font-bold">Aktifkan Widget Live Transaksi</strong>
                                <span>Tampilkan widget ringkasan transaksi di dashboard utama anggota.</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-800">
                <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold bg-brand-600 text-white hover:bg-brand-500 transition-colors shadow-md shadow-brand-900/30">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
