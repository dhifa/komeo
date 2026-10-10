<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="<?= base_url('admin/transactions/' . $trx['id'] . '/edit') ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 hover:text-white transition-colors mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Manajemen Transaksi
            </a>
            <h1 class="text-2xl font-black tracking-tight text-white flex items-center gap-2">
                <span>Pratinjau Tampilan Member</span>
                <span class="px-2 py-0.5 rounded text-xs font-extrabold bg-blue-500/20 text-blue-300 border border-blue-500/30">
                    SIMULASI TAMPILAN
                </span>
            </h1>
            <p class="text-sm text-slate-400 mt-1">
                Ini adalah tampilan persis seperti yang dilihat oleh anggota aktif KOMEO dengan aturan privasi yang berlaku saat ini.
            </p>
        </div>
    </div>

    <!-- Active Policy Banner -->
    <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 text-xs text-slate-300 flex flex-wrap items-center justify-between gap-3">
        <div>
            <span class="text-slate-400">Kebijakan Global Sistem:</span>
            <strong class="text-white ml-1"><?= $globalPolicy === 'hide_all' ? 'Sembunyikan Semua Nominal (Default)' : 'Izinkan Pemilihan Per Transaksi' ?></strong>
        </div>
        <div>
            <span class="text-slate-400">Setelan Transaksi Ini:</span>
            <strong class="text-white ml-1"><?= TransactionModel::VISIBILITY_OPTIONS[$trx['amount_visibility'] ?? 'hide'] ?></strong>
        </div>
        <div>
            <span class="text-slate-400">Efektif Nominal Member:</span>
            <?php if (! empty($maskedTrx['amount_visible'])): ?>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                    DITAMPILKAN (<?= strtoupper($maskedTrx['amount_mode'] ?? '') ?>)
                </span>
            <?php else: ?>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-slate-300 border border-slate-700">
                    DISEMBUNYIKAN SEPENUHNYA
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Member Card Preview -->
    <div class="p-8 rounded-3xl bg-white text-slate-900 border border-slate-200 shadow-xl space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <div class="flex items-center gap-2">
                    <span class="font-mono text-xs px-2.5 py-0.5 rounded-md font-bold bg-brand-50 text-brand-700 border border-brand-200">
                        <?= esc($maskedTrx['transaction_code']) ?>
                    </span>
                    <span class="px-2.5 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-700">
                        <?= esc($maskedTrx['category_name'] ?? 'Umum') ?>
                    </span>
                </div>
                <h2 class="text-xl font-black text-slate-900 mt-2">
                    <?= esc($maskedTrx['public_title']) ?>
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Tanggal Transaksi: <?= date('d M Y', strtotime($maskedTrx['transaction_date'])) ?> &bull; Diperbarui: <?= date('d M Y H:i', strtotime($maskedTrx['updated_at'])) ?>
                </p>
            </div>

            <!-- Badges -->
            <div class="flex flex-col sm:items-end gap-1.5">
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                    <?= esc($maskedTrx['current_work_stage']) ?>
                </span>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <?= TransactionModel::PAYMENT_STATUSES[$maskedTrx['payment_status']] ?? esc($maskedTrx['payment_status']) ?>
                </span>
            </div>
        </div>

        <!-- Description -->
        <?php if (! empty($maskedTrx['description'])): ?>
            <div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Deskripsi Proyek</h3>
                <p class="text-sm text-slate-700 leading-relaxed">
                    <?= nl2br(esc($maskedTrx['description'])) ?>
                </p>
            </div>
        <?php endif; ?>

        <!-- Monetary Section -->
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Informasi Pembayaran & Nilai</h3>

            <?php if (empty($maskedTrx['amount_visible'])): ?>
                <div class="p-4 rounded-xl bg-white border border-slate-200 text-xs text-slate-600 flex items-center gap-3">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Informasi rincian nominal dilindungi oleh kebijakan privasi pengurus KOMEO. Status pembayaran: <strong><?= TransactionModel::PAYMENT_STATUSES[$maskedTrx['payment_status']] ?? esc($maskedTrx['payment_status']) ?></strong>.</span>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <span class="text-[11px] text-slate-500 font-medium block">Total Nilai:</span>
                        <strong class="text-base text-slate-900 font-extrabold block"><?= komeo_format_rupiah($maskedTrx['total_amount']) ?></strong>
                    </div>
                    <?php if ($maskedTrx['payment_percentage'] !== null): ?>
                        <div>
                            <span class="text-[11px] text-slate-500 font-medium block">Persentase Terbayar:</span>
                            <strong class="text-base text-brand-600 font-extrabold block"><?= $maskedTrx['payment_percentage'] ?>%</strong>
                        </div>
                    <?php endif; ?>
                    <?php if ($maskedTrx['net_received'] !== null): ?>
                        <div>
                            <span class="text-[11px] text-slate-500 font-medium block">Dana Diterima:</span>
                            <strong class="text-base text-emerald-600 font-extrabold block"><?= komeo_format_rupiah($maskedTrx['net_received']) ?></strong>
                        </div>
                    <?php endif; ?>
                    <?php if ($maskedTrx['outstanding_balance'] !== null): ?>
                        <div>
                            <span class="text-[11px] text-slate-500 font-medium block">Sisa Saldo:</span>
                            <strong class="text-base text-amber-600 font-extrabold block"><?= komeo_format_rupiah($maskedTrx['outstanding_balance']) ?></strong>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Contract Issue Neutral Display -->
        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between text-xs">
            <span class="text-slate-600 font-medium">Status Kontrak & Komitmen:</span>
            <span class="font-bold text-slate-800"><?= esc($maskedTrx['contract_issue_display']) ?></span>
        </div>

        <!-- Published Timeline -->
        <div>
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Linimasa Progres</h3>
            <div class="relative pl-6 space-y-4 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                <?php if (empty($timeline)): ?>
                    <p class="text-xs text-slate-500">Belum ada linimasa perkembangan.</p>
                <?php else: ?>
                    <?php foreach ($timeline as $tl): ?>
                        <div class="relative">
                            <span class="absolute -left-6 top-1 w-3.5 h-3.5 rounded-full bg-white border-2 border-brand-600"></span>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 text-xs space-y-1">
                                <div class="flex items-center justify-between text-[11px] text-slate-400">
                                    <span class="font-bold text-slate-700"><?= esc($tl['new_status'] ?: 'Update Aktivitas') ?></span>
                                    <span><?= date('d M Y, H:i', strtotime($tl['created_at'])) ?> WIB</span>
                                </div>
                                <p class="text-slate-700 font-medium">
                                    <?= esc($tl['public_description']) ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
