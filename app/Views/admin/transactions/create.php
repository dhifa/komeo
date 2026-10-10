<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumb & Header -->
    <div>
        <a href="<?= base_url('admin/transactions') ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 hover:text-white transition-colors mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Live Transaksi
        </a>
        <h1 class="text-2xl font-black tracking-tight text-white">Catat Transaksi Baru</h1>
        <p class="text-sm text-slate-400 mt-1">
            Data transaksi ini akan dimonitor oleh admin dan dipublikasikan secara transparan untuk anggota aktif KOMEO.
        </p>
    </div>

    <!-- Errors -->
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="p-4 rounded-2xl bg-rose-950/40 border border-rose-500/30 text-rose-200 text-sm space-y-1">
            <div class="font-bold flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Periksa kembali data formulir:
            </div>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                <?php foreach (session()->getFlashdata('errors') as $err): ?>
                    <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Form Card -->
    <div class="p-6 rounded-3xl bg-slate-900/80 border border-slate-800 shadow-sm">
        <form method="post" action="<?= base_url('admin/transactions/create') ?>" class="space-y-6">
            <?= csrf_field() ?>

            <!-- Generated Transaction Code -->
            <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800 flex items-center justify-between">
                <div>
                    <span class="text-xs text-slate-400 font-semibold block">Kode Unik Transaksi (Auto-Generated)</span>
                    <span class="font-mono text-lg font-black text-brand-300 mt-0.5 block"><?= esc($generatedCode) ?></span>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-brand-500/20 text-brand-300 border border-brand-500/30">
                    KODE RESMI
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Internal Title -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">
                        Judul Transaksi Internal <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" name="title" value="<?= esc(old('title')) ?>" placeholder="Contoh: Pengadaan Sound System Acara Expo JCC" required class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-brand-500">
                    <p class="text-[11px] text-slate-500 mt-1">Digunakan untuk penamaan internal admin.</p>
                </div>

                <!-- Public Title -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">
                        Judul Tampilan Publik (Member) <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" name="public_title" value="<?= esc(old('public_title')) ?>" placeholder="Contoh: Produksi Tata Suara & Lighting Exhibition Jakarta" required class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-brand-500">
                    <p class="text-[11px] text-slate-500 mt-1">Nama proyek yang akan ditampilkan ke anggota aktif di portal.</p>
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">
                        Kategori Transaksi <span class="text-rose-400">*</span>
                    </label>
                    <select name="category_id" required class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-brand-500">
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= (old('category_id') == $cat['id']) ? 'selected' : '' ?>>
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
                    <input type="date" name="transaction_date" value="<?= esc(old('transaction_date', date('Y-m-d'))) ?>" class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-brand-500">
                </div>

                <!-- Internal Client Name -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">
                        Nama Klien / Penyelenggara (Internal Admin)
                    </label>
                    <input type="text" name="internal_client_name" value="<?= esc(old('internal_client_name')) ?>" placeholder="Contoh: PT Event Sukses Gemilang" class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-brand-500">
                    <p class="text-[11px] text-slate-500 mt-1">Privat dan tidak akan pernah dibocorkan kepada member.</p>
                </div>

                <!-- Internal Project Ref -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">
                        Referensi Proyek / No Kontrak (Internal)
                    </label>
                    <input type="text" name="internal_project_ref" value="<?= esc(old('internal_project_ref')) ?>" placeholder="Contoh: PO-2026-X88" class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-brand-500">
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1.5">
                    Deskripsi Ringkas / Lingkup Pekerjaan
                </label>
                <textarea name="description" rows="3" placeholder="Jelaskan ringkasan proyek atau kebutuhan event yang disepakati..." class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-brand-500"><?= esc(old('description')) ?></textarea>
            </div>

            <!-- Financial Information Section -->
            <div class="pt-4 border-t border-slate-800 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-white">Rencana Finansial & Pembayaran</h3>
                        <p class="text-xs text-slate-400">Pengisian nominal bersifat opsional jika total belum disepakati.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">Total Nilai Disepakati (IDR)</label>
                        <input type="number" step="0.01" name="total_amount" value="<?= esc(old('total_amount')) ?>" placeholder="Contoh: 25000000" class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-brand-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">Nominal DP Disepakati (IDR)</label>
                        <input type="number" step="0.01" name="agreed_dp_amount" value="<?= esc(old('agreed_dp_amount')) ?>" placeholder="Contoh: 7500000" class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-brand-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">Persentase DP (%)</label>
                        <input type="number" step="0.1" name="agreed_dp_percentage" value="<?= esc(old('agreed_dp_percentage')) ?>" placeholder="Contoh: 30" class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-brand-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">Jatuh Tempo Pembayaran DP</label>
                        <input type="date" name="dp_due_date" value="<?= esc(old('dp_due_date')) ?>" class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-brand-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">Jatuh Tempo Pelunasan Akhir</label>
                        <input type="date" name="final_due_date" value="<?= esc(old('final_due_date')) ?>" class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-brand-500">
                    </div>
                </div>
            </div>

            <!-- Workflow Stage & Privacy Controls -->
            <div class="pt-4 border-t border-slate-800 space-y-4">
                <h3 class="text-sm font-bold text-white">Status Awal & Kebijakan Privasi</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">Tahap Pekerjaan Awal</label>
                        <select name="current_work_stage" class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-brand-500">
                            <?php foreach ($workStages as $k => $v): ?>
                                <option value="<?= $k ?>" <?= (old('current_work_stage') === $k) ? 'selected' : '' ?>><?= $v ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">Tingkat Visibilitas Nominal (Member)</label>
                        <select name="amount_visibility" class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-brand-500">
                            <?php foreach ($visibilityOptions as $k => $v): ?>
                                <option value="<?= $k ?>" <?= (old('amount_visibility', 'hide') === $k) ? 'selected' : '' ?>><?= $v ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="text-[11px] text-slate-500 mt-1">Catatan: Pengaturan global tetap menjadi batas perlindungan tertinggi.</p>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 flex items-center gap-3">
                    <input type="checkbox" name="is_published" id="is_published" value="1" <?= old('is_published') ? 'checked' : '' ?> class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 bg-slate-900 border-slate-700">
                    <label for="is_published" class="text-xs text-slate-300 select-none cursor-pointer">
                        <strong class="text-white block font-bold">Publikasikan Langsung ke Portal Anggota Aktif</strong>
                        <span>Jika tidak dicentang, transaksi tersimpan sebagai draft privat internal admin.</span>
                    </label>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-800">
                <a href="<?= base_url('admin/transactions') ?>" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-400 hover:text-white bg-slate-800 hover:bg-slate-700 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold bg-brand-600 text-white hover:bg-brand-500 transition-colors shadow-md shadow-brand-900/30">
                    Simpan Transaksi
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
