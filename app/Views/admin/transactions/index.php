<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-white flex items-center gap-2.5">
                <span>Live Transaksi</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-extrabold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                    MONITORING RESMI
                </span>
            </h1>
            <p class="text-sm text-slate-400 mt-1">
                Sistem monitoring aktivitas transaksi ekosistem event, progres pekerjaan, dan pelunasan pembayaran yang dikelola manual oleh admin.
            </p>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="<?= base_url('admin/transactions/settings') ?>" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-300 bg-slate-800 hover:bg-slate-700 transition-colors border border-slate-700">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Privasi & Pengaturan</span>
            </a>
            <a href="<?= base_url('admin/transactions/reports') ?>" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-300 bg-slate-800 hover:bg-slate-700 transition-colors border border-slate-700">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Laporan & Ekspor</span>
            </a>
            <a href="<?= base_url('admin/transactions/create') ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-brand-600 text-white hover:bg-brand-500 transition-colors shadow-md shadow-brand-900/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Catat Transaksi Baru</span>
            </a>
        </div>
    </div>

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

    <!-- Statistics Overview Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800">
            <span class="text-xs text-slate-400 font-semibold block">Total Transaksi</span>
            <span class="text-2xl font-black text-white mt-1 block"><?= $statistics['total'] ?></span>
            <span class="text-[10px] text-slate-500 mt-1 block">Semua record</span>
        </div>
        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800">
            <span class="text-xs text-blue-400 font-semibold block">Sedang Berjalan</span>
            <span class="text-2xl font-black text-blue-300 mt-1 block"><?= $statistics['in_progress'] ?></span>
            <span class="text-[10px] text-slate-500 mt-1 block">Proses aktif</span>
        </div>
        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800">
            <span class="text-xs text-emerald-400 font-semibold block">Pekerjaan Selesai</span>
            <span class="text-2xl font-black text-emerald-300 mt-1 block"><?= $statistics['completed'] ?></span>
            <span class="text-[10px] text-slate-500 mt-1 block">Tahap selesai</span>
        </div>
        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800">
            <span class="text-xs text-amber-400 font-semibold block">Menunggu DP</span>
            <span class="text-2xl font-black text-amber-300 mt-1 block"><?= $statistics['awaiting_payment'] ?></span>
            <span class="text-[10px] text-slate-500 mt-1 block">Belum ada bayar</span>
        </div>
        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800">
            <span class="text-xs text-teal-400 font-semibold block">Pembayaran Lunas</span>
            <span class="text-2xl font-black text-teal-300 mt-1 block"><?= $statistics['paid'] ?></span>
            <span class="text-[10px] text-slate-500 mt-1 block">Pelunasan penuh</span>
        </div>
        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800">
            <span class="text-xs text-rose-400 font-semibold block">Terlambat Bayar</span>
            <span class="text-2xl font-black text-rose-300 mt-1 block"><?= $statistics['overdue'] ?></span>
            <span class="text-[10px] text-slate-500 mt-1 block">Lewat due date</span>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800">
        <form method="get" action="<?= base_url('admin/transactions') ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            <div class="lg:col-span-2">
                <input type="text" name="search" value="<?= esc($filters['search'] ?? '') ?>" placeholder="Cari kode, judul transaksi, klien..." class="w-full px-3.5 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-brand-500">
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
                    Filter
                </button>
                <a href="<?= base_url('admin/transactions') ?>" class="p-2 rounded-xl bg-slate-800 text-slate-400 hover:text-white transition-colors" title="Reset filter">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </a>
            </div>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="rounded-2xl bg-slate-900/80 border border-slate-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/60 border-b border-slate-800 text-slate-400 uppercase text-[10px] font-bold tracking-wider">
                    <tr>
                        <th class="p-4">Kode & Judul</th>
                        <th class="p-4">Kategori & Klien</th>
                        <th class="p-4">Tahap Pekerjaan</th>
                        <th class="p-4">Status Pembayaran</th>
                        <th class="p-4">Total / Diterima</th>
                        <th class="p-4">Masalah Kontrak</th>
                        <th class="p-4">Publikasi</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <?php if (empty($transactions)): ?>
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-500">
                                Tidak ada transaksi yang sesuai dengan filter pencarian.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $t): ?>
                            <tr class="hover:bg-slate-850/50 transition-colors">
                                <td class="p-4">
                                    <span class="font-mono font-bold text-brand-300 tracking-wider block">
                                        <?= esc($t['transaction_code']) ?>
                                    </span>
                                    <span class="font-bold text-white text-sm block mt-0.5">
                                        <?= esc($t['title']) ?>
                                    </span>
                                    <span class="text-[11px] text-slate-400 block italic">
                                        Publik: <?= esc($t['public_title'] ?: $t['title']) ?>
                                    </span>
                                </td>
                                <td class="p-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-slate-200 border border-slate-700 block w-max">
                                        <?= esc($t['category_name'] ?? 'Umum') ?>
                                    </span>
                                    <span class="text-slate-400 text-[11px] block mt-1">
                                        <?= esc($t['internal_client_name'] ?: 'Klien tidak dicatat') ?>
                                    </span>
                                </td>
                                <td class="p-4">
                                    <?php
                                        $stage = $t['current_work_stage'] ?? 'Transaksi Masuk';
                                        $stageColor = match($stage) {
                                            'Selesai'         => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
                                            'Dalam Proses'    => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
                                            'Negosiasi'       => 'bg-purple-500/20 text-purple-300 border-purple-500/30',
                                            'Verifikasi'      => 'bg-indigo-500/20 text-indigo-300 border-indigo-500/30',
                                            'Ditunda'         => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                                            'Dibatalkan'      => 'bg-rose-500/20 text-rose-300 border-rose-500/30',
                                            default           => 'bg-slate-700 text-slate-200 border-slate-600',
                                        };
                                    ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold border <?= $stageColor ?>">
                                        <?= esc($stage) ?>
                                    </span>
                                </td>
                                <td class="p-4">
                                    <?php
                                        $pay = $t['payment_status'] ?? 'UNPAID';
                                        $payText = TransactionModel::PAYMENT_STATUSES[$pay] ?? $pay;
                                        $payColor = match($pay) {
                                            'PAID'           => 'bg-teal-500/20 text-teal-300 border-teal-500/30',
                                            'PARTIALLY_PAID' => 'bg-indigo-500/20 text-indigo-300 border-indigo-500/30',
                                            'DP_RECEIVED'    => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
                                            'AWAITING_DP'    => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                                            'OVERDUE'        => 'bg-rose-500/20 text-rose-300 border-rose-500/30',
                                            default          => 'bg-slate-700 text-slate-200 border-slate-600',
                                        };
                                    ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold border <?= $payColor ?>">
                                        <?= esc($payText) ?>
                                    </span>
                                    <?php if (! empty($t['is_overdue'])): ?>
                                        <span class="block text-[10px] text-rose-400 font-extrabold mt-1">
                                            ⚠️ Jatuh Tempo
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4">
                                    <span class="text-white font-bold block">
                                        <?= komeo_format_rupiah($t['total_amount']) ?>
                                    </span>
                                    <span class="text-[10px] text-slate-400 block mt-0.5">
                                        Visibilitas: <?= TransactionModel::VISIBILITY_OPTIONS[$t['amount_visibility'] ?? 'hide'] ?? 'Sembunyi' ?>
                                    </span>
                                </td>
                                <td class="p-4">
                                    <?php
                                        $issue = $t['contract_issue_status'] ?? 'none';
                                        $issueText = TransactionModel::CONTRACT_ISSUE_STATUSES[$issue] ?? $issue;
                                        $issueColor = match($issue) {
                                            'none'             => 'text-slate-400',
                                            'resolved'         => 'text-emerald-400 font-semibold',
                                            'suspected_breach' => 'text-rose-400 font-bold',
                                            'disputed'         => 'text-amber-400 font-bold',
                                            default            => 'text-purple-300 font-medium',
                                        };
                                    ?>
                                    <span class="text-[11px] <?= $issueColor ?> block">
                                        <?= esc($issueText) ?>
                                    </span>
                                </td>
                                <td class="p-4">
                                    <?php if (! empty($t['is_published'])): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                            Dipublikasikan
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-800 text-slate-400 border border-slate-700">
                                            Draft / Privat
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="<?= base_url('admin/transactions/' . $t['id'] . '/edit') ?>" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition-colors" title="Kelola Transaksi">
                                            Kelola
                                        </a>
                                        <a href="<?= base_url('admin/transactions/' . $t['id'] . '/preview') ?>" class="p-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white transition-colors" title="Pratinjau Tampilan Member">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="p-4 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
                <span>Halaman <?= $page ?> dari <?= $totalPages ?> (Total <?= $totalRecords ?> transaksi)</span>
                <div class="flex items-center gap-2">
                    <?php if ($page > 1): ?>
                        <a href="<?= base_url('admin/transactions?' . http_build_query(array_merge($filters, ['page' => $page - 1]))) ?>" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-white font-bold">
                            Sebelumnya
                        </a>
                    <?php endif; ?>
                    <?php if ($page < $totalPages): ?>
                        <a href="<?= base_url('admin/transactions?' . http_build_query(array_merge($filters, ['page' => $page + 1]))) ?>" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-white font-bold">
                            Selanjutnya
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
