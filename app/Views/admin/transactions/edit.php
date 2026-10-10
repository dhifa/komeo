<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <?= $this->include('admin/transactions/_tabs_header') ?>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="p-4 rounded-2xl bg-emerald-950/40 border border-emerald-500/30 text-emerald-200 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span><?= session()->getFlashdata('success') ?></span>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="p-4 rounded-2xl bg-rose-950/40 border border-rose-500/30 text-rose-200 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span><?= session()->getFlashdata('error') ?></span>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="p-4 rounded-2xl bg-rose-950/40 border border-rose-500/30 text-rose-200 text-sm space-y-1">
            <div class="font-bold flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Periksa formulir:
            </div>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                <?php foreach (session()->getFlashdata('errors') as $err): ?>
                    <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Edit Form Card -->
    <div class="p-6 rounded-3xl bg-slate-900/80 border border-slate-800 shadow-sm">
        <form method="post" action="<?= base_url('admin/transactions/' . $trx['id'] . '/edit') ?>" class="space-y-6">
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Title -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">
                        Judul Transaksi Internal <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" name="title" value="<?= esc(old('title', $trx['title'])) ?>" required class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-brand-500">
                </div>

                <!-- Public Title -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">
                        Judul Tampilan Publik (Member) <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" name="public_title" value="<?= esc(old('public_title', $trx['public_title'])) ?>" required class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-brand-500">
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">
                        Kategori Transaksi <span class="text-rose-400">*</span>
                    </label>
                    <select name="category_id" required class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-brand-500">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= (old('category_id', $trx['category_id']) == $cat['id']) ? 'selected' : '' ?>>
                                <?= esc($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Transaction Date -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">
                        Tanggal Transaksi
                    </label>
                    <input type="date" name="transaction_date" value="<?= esc(old('transaction_date', $trx['transaction_date'])) ?>" class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-brand-500">
                </div>

                <!-- Internal Client Name -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">
                        Nama Klien / Penyelenggara (Internal Admin)
                    </label>
                    <input type="text" name="internal_client_name" value="<?= esc(old('internal_client_name', $trx['internal_client_name'])) ?>" class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-brand-500">
                    <p class="text-[11px] text-slate-500 mt-1">Dirahasiakan sepenuhnya dari tampilan publik / anggota.</p>
                </div>

                <!-- Internal Project Ref -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">
                        Referensi Proyek / No Kontrak (Internal)
                    </label>
                    <input type="text" name="internal_project_ref" value="<?= esc(old('internal_project_ref', $trx['internal_project_ref'])) ?>" class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-brand-500">
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1.5">
                    Deskripsi Ringkas / Lingkup Pekerjaan
                </label>
                <textarea name="description" rows="3" class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-brand-500"><?= esc(old('description', $trx['description'])) ?></textarea>
            </div>

            <!-- Financial Values -->
            <div class="pt-4 border-t border-slate-800 space-y-4">
                <h3 class="text-sm font-bold text-white">Rencana Finansial & Jatuh Tempo</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">Total Nilai Disepakati (IDR)</label>
                        <input type="number" step="0.01" name="total_amount" value="<?= esc(old('total_amount', $trx['total_amount'])) ?>" class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-brand-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">Nominal DP Disepakati (IDR)</label>
                        <input type="number" step="0.01" name="agreed_dp_amount" value="<?= esc(old('agreed_dp_amount', $trx['agreed_dp_amount'])) ?>" class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-brand-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">Persentase DP (%)</label>
                        <input type="number" step="0.1" name="agreed_dp_percentage" value="<?= esc(old('agreed_dp_percentage', $trx['agreed_dp_percentage'])) ?>" class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-brand-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">Jatuh Tempo Pembayaran DP</label>
                        <input type="date" name="dp_due_date" value="<?= esc(old('dp_due_date', $trx['dp_due_date'])) ?>" class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-brand-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">Jatuh Tempo Pelunasan Akhir</label>
                        <input type="date" name="final_due_date" value="<?= esc(old('final_due_date', $trx['final_due_date'])) ?>" class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-brand-500">
                    </div>
                </div>
            </div>

            <!-- Privacy & Publication Settings -->
            <div class="pt-4 border-t border-slate-800 space-y-4">
                <h3 class="text-sm font-bold text-white">Privasi Finansial & Pengaturan Publikasi</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">Tingkat Visibilitas Nominal</label>
                        <select name="amount_visibility" class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-brand-500">
                            <?php foreach ($visibilityOptions as $k => $v): ?>
                                <option value="<?= $k ?>" <?= (old('amount_visibility', $trx['amount_visibility']) === $k) ? 'selected' : '' ?>>
                                    <?= $v ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="text-[11px] text-slate-500 mt-1">
                            Kebijakan global saat ini: <strong class="text-slate-300"><?= esc(site_setting('Transaction.monetary_visibility_policy', 'hide_all') === 'hide_all' ? 'Sembunyikan Semua Nominal' : 'Izinkan Admin Memilih Per Transaksi') ?></strong>.
                        </p>
                    </div>

                    <div class="flex items-center">
                        <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 flex items-center gap-3 w-full">
                            <input type="checkbox" name="is_published" id="is_published" value="1" <?= old('is_published', $trx['is_published']) ? 'checked' : '' ?> class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 bg-slate-900 border-slate-700">
                            <label for="is_published" class="text-xs text-slate-300 select-none cursor-pointer">
                                <strong class="text-white block font-bold">Publikasikan Transaksi</strong>
                                <span>Tampilkan aktivitas transaksi ini ke portal seluruh anggota aktif KOMEO.</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-800">
                <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold bg-brand-600 text-white hover:bg-brand-500 transition-colors shadow-md shadow-brand-900/30">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
