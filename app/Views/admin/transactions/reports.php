<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="<?= base_url('admin/transactions') ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 hover:text-white transition-colors mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Live Transaksi
            </a>
            <h1 class="text-2xl font-black tracking-tight text-white flex items-center gap-2">
                <span>Laporan & Rekapitulasi Transaksi</span>
            </h1>
            <p class="text-sm text-slate-400 mt-1">
                Laporan komprehensif aktivitas transaksi internal, status penerimaan pembayaran, dan mitigasi wanprestasi.
            </p>
        </div>

        <div>
            <a href="<?= base_url('admin/transactions/reports/csv?' . http_build_query($filters)) ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white transition-colors shadow-md shadow-emerald-950/40">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Unduh Laporan CSV (Excel)</span>
            </a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800">
            <span class="text-xs text-slate-400 font-semibold block">Total Transaksi Terfilter</span>
            <span class="text-2xl font-black text-white mt-1 block"><?= count($transactions) ?> Transaksi</span>
            <span class="text-[11px] text-slate-500 mt-1 block">Berdasarkan kriteria filter saat ini</span>
        </div>
        <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800">
            <span class="text-xs text-brand-400 font-semibold block">Akumulasi Nilai Kontrak</span>
            <span class="text-2xl font-black text-brand-300 mt-1 block"><?= komeo_format_rupiah($totalAgreed) ?></span>
            <span class="text-[11px] text-slate-500 mt-1 block">Total disepakati dalam transaksi</span>
        </div>
        <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800">
            <span class="text-xs text-emerald-400 font-semibold block">Total Penerimaan Kas (Net)</span>
            <span class="text-2xl font-black text-emerald-300 mt-1 block"><?= komeo_format_rupiah($totalNet) ?></span>
            <span class="text-[11px] text-slate-500 mt-1 block">
                Sisa saldo: <?= komeo_format_rupiah(max(0.0, $totalAgreed - $totalNet)) ?>
            </span>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800">
        <form method="get" action="<?= base_url('admin/transactions/reports') ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <input type="text" name="search" value="<?= esc($filters['search'] ?? '') ?>" placeholder="Cari kode / judul / klien..." class="w-full px-3.5 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-brand-500">
            </div>
            <div>
                <select name="category_id" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-brand-500">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($filters['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                            <?= esc($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <select name="work_stage" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-brand-500">
                    <option value="">Semua Tahap Kerja</option>
                    <?php foreach ($workStages as $k => $v): ?>
                        <option value="<?= $k ?>" <?= ($filters['work_stage'] === $k) ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <select name="payment_status" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-brand-500">
                    <option value="">Semua Status Bayar</option>
                    <?php foreach ($payStatuses as $k => $v): ?>
                        <option value="<?= $k ?>" <?= ($filters['payment_status'] === $k) ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="w-full px-4 py-2 rounded-xl text-xs font-bold bg-brand-600 text-white hover:bg-brand-500 transition-colors">
                    Terapkan
                </button>
                <a href="<?= base_url('admin/transactions/reports') ?>" class="p-2 rounded-xl bg-slate-800 text-slate-400 hover:text-white" title="Reset filter">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </a>
            </div>
        </form>
    </div>

    <!-- Report Table -->
    <div class="rounded-2xl bg-slate-900/80 border border-slate-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/60 border-b border-slate-800 text-slate-400 uppercase text-[10px] font-bold tracking-wider">
                    <tr>
                        <th class="p-3.5">Kode Transaksi</th>
                        <th class="p-3.5">Judul & Klien</th>
                        <th class="p-3.5">Kategori</th>
                        <th class="p-3.5">Tahap</th>
                        <th class="p-3.5">Status Bayar</th>
                        <th class="p-3.5">Total Kontrak</th>
                        <th class="p-3.5">Net Diterima</th>
                        <th class="p-3.5">Publikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <?php if (empty($transactions)): ?>
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-500">
                                Tidak ada data transaksi yang sesuai.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $t): ?>
                            <?php $fin = model(\App\Models\TransactionModel::class)->getComputedFinancials($t); ?>
                            <tr class="hover:bg-slate-850/50">
                                <td class="p-3.5 font-mono font-bold text-brand-300">
                                    <a href="<?= base_url('admin/transactions/' . $t['id'] . '/edit') ?>" class="hover:underline">
                                        <?= esc($t['transaction_code']) ?>
                                    </a>
                                </td>
                                <td class="p-3.5">
                                    <span class="font-bold text-white block"><?= esc($t['title']) ?></span>
                                    <span class="text-[11px] text-slate-400 block"><?= esc($t['internal_client_name'] ?: 'Klien Umum') ?></span>
                                </td>
                                <td class="p-3.5"><?= esc($t['category_name'] ?? '-') ?></td>
                                <td class="p-3.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-slate-200">
                                        <?= esc($t['current_work_stage']) ?>
                                    </span>
                                </td>
                                <td class="p-3.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-slate-200">
                                        <?= TransactionModel::PAYMENT_STATUSES[$t['payment_status']] ?? esc($t['payment_status']) ?>
                                    </span>
                                </td>
                                <td class="p-3.5 font-bold text-white"><?= komeo_format_rupiah($t['total_amount']) ?></td>
                                <td class="p-3.5 font-bold text-emerald-400"><?= komeo_format_rupiah($fin['net_received']) ?></td>
                                <td class="p-3.5">
                                    <?= ! empty($t['is_published']) ? '<span class="text-emerald-400 font-bold">Publik</span>' : '<span class="text-slate-500">Privat</span>' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
