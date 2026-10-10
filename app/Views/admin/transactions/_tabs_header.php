<!-- Transaction Tabs Header -->
<div class="space-y-4">
    <!-- Breadcrumb & Quick Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <a href="<?= base_url('admin/transactions') ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 hover:text-white transition-colors mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Daftar Transaksi
            </a>
            <div class="flex items-center gap-3 flex-wrap">
                <h1 class="text-2xl font-black tracking-tight text-white flex items-center gap-2">
                    <span><?= esc($trx['title']) ?></span>
                </h1>
                <span class="font-mono text-xs px-2.5 py-0.5 rounded-full font-bold bg-brand-500/20 text-brand-300 border border-brand-500/30">
                    <?= esc($trx['transaction_code']) ?>
                </span>
                <?php if (! empty($trx['is_published'])): ?>
                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                        PUBLISHED
                    </span>
                <?php else: ?>
                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-800 text-slate-400 border border-slate-700">
                        DRAFT
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <!-- Toggle Publish Form -->
            <form method="post" action="<?= base_url('admin/transactions/' . $trx['id'] . '/toggle-publish') ?>">
                <?= csrf_field() ?>
                <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-bold <?= ! empty($trx['is_published']) ? 'bg-amber-500/20 text-amber-300 hover:bg-amber-500/30 border border-amber-500/40' : 'bg-emerald-600 text-white hover:bg-emerald-500' ?> transition-colors">
                    <?= ! empty($trx['is_published']) ? 'Tarik Publikasi' : 'Publikasikan' ?>
                </button>
            </form>

            <!-- Member Preview Button -->
            <a href="<?= base_url('admin/transactions/' . $trx['id'] . '/preview') ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-800 text-slate-200 hover:bg-slate-700 transition-colors border border-slate-700" title="Pratinjau bagaimana member aktif melihat data ini">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <span>Pratinjau Member</span>
            </a>
        </div>
    </div>

    <!-- Quick Financial & Workflow Status Bar -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 rounded-2xl bg-slate-900/90 border border-slate-800">
        <div>
            <span class="text-[11px] text-slate-400 font-medium block">Tahap Pekerjaan:</span>
            <span class="text-xs font-bold text-white mt-0.5 block"><?= esc($trx['current_work_stage']) ?></span>
        </div>
        <div>
            <span class="text-[11px] text-slate-400 font-medium block">Status Pembayaran:</span>
            <span class="text-xs font-bold text-white mt-0.5 block">
                <?= TransactionModel::PAYMENT_STATUSES[$trx['payment_status'] ?? 'UNPAID'] ?? esc($trx['payment_status']) ?>
                <?= ! empty($trx['is_overdue']) ? '<span class="text-rose-400 text-[10px]">(Terlambat)</span>' : '' ?>
            </span>
        </div>
        <div>
            <span class="text-[11px] text-slate-400 font-medium block">Total Disepakati:</span>
            <span class="text-xs font-bold text-emerald-400 mt-0.5 block"><?= komeo_format_rupiah($trx['total_amount']) ?></span>
        </div>
        <div>
            <span class="text-[11px] text-slate-400 font-medium block">Net Diterima / Sisa:</span>
            <span class="text-xs font-bold text-slate-200 mt-0.5 block">
                <?= komeo_format_rupiah($financials['net_received'] ?? 0) ?>
                <span class="text-slate-400 font-normal">/ <?= komeo_format_rupiah($financials['outstanding_balance'] ?? null) ?></span>
            </span>
        </div>
    </div>

    <!-- Tab Navigation Navigation Buttons -->
    <div class="flex items-center gap-2 border-b border-slate-800 overflow-x-auto pb-1 text-xs font-bold">
        <a href="<?= base_url('admin/transactions/' . $trx['id'] . '/edit') ?>" class="px-4 py-2.5 rounded-xl transition-colors shrink-0 <?= ($activeTab === 'general') ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800' ?>">
            Informasi Transaksi
        </a>
        <a href="<?= base_url('admin/transactions/' . $trx['id'] . '/timeline') ?>" class="px-4 py-2.5 rounded-xl transition-colors shrink-0 <?= ($activeTab === 'timeline') ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800' ?>">
            Progres Pekerjaan & Linimasa
        </a>
        <a href="<?= base_url('admin/transactions/' . $trx['id'] . '/payments') ?>" class="px-4 py-2.5 rounded-xl transition-colors shrink-0 <?= ($activeTab === 'payments') ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800' ?>">
            Pencatatan Pembayaran
        </a>
        <a href="<?= base_url('admin/transactions/' . $trx['id'] . '/issues') ?>" class="px-4 py-2.5 rounded-xl transition-colors shrink-0 <?= ($activeTab === 'issues') ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800' ?>">
            Permasalahan Kontrak
        </a>
    </div>
</div>
