<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumb & Top Bar -->
    <div class="flex items-center justify-between">
        <a href="<?= base_url('dashboard/transaksi') ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-900 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar Transaksi
        </a>
        <div class="flex items-center gap-2 text-xs text-slate-400">
            <span id="poll-spinner" class="hidden">
                <svg class="animate-spin h-3.5 w-3.5 text-brand-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            </span>
            <span id="last-updated-text">Terakhir diperbarui: <?= date('H:i') ?> WIB</span>
        </div>
    </div>

    <!-- Main Detail Card -->
    <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200/80 shadow-sm space-y-6">
        <!-- Title & Badges Header -->
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 pb-6 border-b border-slate-100">
            <div class="space-y-2">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="font-mono text-xs px-2.5 py-0.5 rounded-lg font-bold bg-slate-100 text-slate-700 border border-slate-200">
                        <?= esc($trx['transaction_code']) ?>
                    </span>
                    <span class="px-2.5 py-0.5 rounded-md text-xs font-semibold bg-brand-50 text-brand-700">
                        <?= esc($trx['category_name'] ?? 'Umum') ?>
                    </span>
                    <span class="text-xs text-slate-400">
                        &bull; Tanggal: <?= date('d M Y', strtotime($trx['transaction_date'])) ?>
                    </span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                    <?= esc($trx['public_title']) ?>
                </h1>
            </div>

            <!-- Badges -->
            <div class="flex flex-wrap sm:flex-col sm:items-end gap-2">
                <div class="text-right">
                    <span class="text-[10px] text-slate-400 font-medium block">Tahap Pekerjaan:</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 mt-0.5" id="val-work-stage">
                        <?= esc($trx['current_work_stage']) ?>
                    </span>
                </div>
                <div class="text-right">
                    <span class="text-[10px] text-slate-400 font-medium block">Status Pembayaran:</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 mt-0.5" id="val-payment-status">
                        <?= TransactionModel::PAYMENT_STATUSES[$trx['payment_status']] ?? esc($trx['payment_status']) ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Description -->
        <?php if (! empty($trx['description'])): ?>
            <div class="space-y-1.5">
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Lingkup Pekerjaan / Deskripsi</h2>
                <p class="text-sm text-slate-700 leading-relaxed">
                    <?= nl2br(esc($trx['description'])) ?>
                </p>
            </div>
        <?php endif; ?>

        <!-- Monetary Information Block (Server-Side Privacy Checked) -->
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Informasi Nilai & Pembayaran</h2>

            <?php if (empty($trx['amount_visible'])): ?>
                <div class="p-4 rounded-xl bg-white border border-slate-200 text-xs text-slate-600 flex items-center gap-3">
                    <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Informasi nilai nominal dirahasiakan oleh pengurus. Seluruh perkembangan pekerjaan dan status pembayaran tetap dipantau berkala secara transparan.</span>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <span class="text-[11px] text-slate-500 font-medium block">Total Nilai Proyek:</span>
                        <strong class="text-base text-slate-900 font-extrabold block"><?= komeo_format_rupiah($trx['total_amount']) ?></strong>
                    </div>
                    <?php if ($trx['payment_percentage'] !== null): ?>
                        <div>
                            <span class="text-[11px] text-slate-500 font-medium block">Persentase Terbayar:</span>
                            <strong class="text-base text-brand-600 font-extrabold block"><?= $trx['payment_percentage'] ?>%</strong>
                        </div>
                    <?php endif; ?>
                    <?php if ($trx['net_received'] !== null): ?>
                        <div>
                            <span class="text-[11px] text-slate-500 font-medium block">Dana Diterima:</span>
                            <strong class="text-base text-emerald-600 font-extrabold block"><?= komeo_format_rupiah($trx['net_received']) ?></strong>
                        </div>
                    <?php endif; ?>
                    <?php if ($trx['outstanding_balance'] !== null): ?>
                        <div>
                            <span class="text-[11px] text-slate-500 font-medium block">Sisa Saldo:</span>
                            <strong class="text-base text-amber-600 font-extrabold block"><?= komeo_format_rupiah($trx['outstanding_balance']) ?></strong>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Contract Commitments & Neutral Issues Status -->
        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between text-xs">
            <span class="text-slate-600 font-medium">Status Pengawasan Kontrak & Komitmen:</span>
            <span class="font-bold text-slate-800" id="val-contract-issue">
                <?= esc($trx['contract_issue_display']) ?>
            </span>
        </div>

        <!-- Published Timeline History -->
        <div class="pt-4 border-t border-slate-100 space-y-4">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Linimasa Perkembangan Transaksi</h2>

            <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200" id="timeline-list">
                <?php if (empty($timeline)): ?>
                    <p class="text-xs text-slate-500">Belum ada catatan linimasa untuk transaksi ini.</p>
                <?php else: ?>
                    <?php foreach ($timeline as $tl): ?>
                        <div class="relative">
                            <span class="absolute -left-6 top-1.5 w-3.5 h-3.5 rounded-full bg-white border-2 border-brand-600"></span>
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 text-xs space-y-1">
                                <div class="flex items-center justify-between text-[11px] text-slate-400">
                                    <span class="font-bold text-slate-700"><?= esc($tl['new_status'] ?: 'Update Aktivitas') ?></span>
                                    <span><?= date('d M Y, H:i', strtotime($tl['created_at'])) ?> WIB</span>
                                </div>
                                <p class="text-slate-700 font-medium leading-relaxed">
                                    <?= esc($tl['public_description']) ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Footer Notice -->
        <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
            <span>Data diperbarui secara manual oleh Dewan Pengurus / Administrator KOMEO.ID.</span>
        </div>
    </div>
</div>

<!-- Auto-refresh polling for single transaction -->
<script>
(function() {
    const trxId = <?= (int) $trx['id'] ?>;
    const pollIntervalMs = <?= (int) $pollInterval * 1000 ?>;
    let isPolling = false;

    function doPoll() {
        if (document.hidden || isPolling) return;
        isPolling = true;
        const spinner = document.getElementById('poll-spinner');
        if (spinner) spinner.classList.remove('hidden');

        fetch('<?= base_url('dashboard/transaksi/poll') ?>?id=' + trxId, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'ok' && data.transaction) {
                const t = data.transaction;
                const updatedEl = document.getElementById('last-updated-text');
                if (updatedEl) updatedEl.textContent = 'Terakhir diperbarui: ' + data.timestamp + ' WIB';

                if (document.getElementById('val-work-stage')) {
                    document.getElementById('val-work-stage').textContent = t.current_work_stage;
                }
                if (document.getElementById('val-contract-issue')) {
                    document.getElementById('val-contract-issue').textContent = t.contract_issue_display;
                }
            }
        })
        .catch(err => {
            console.debug('Poll skipped:', err);
        })
        .finally(() => {
            isPolling = false;
            if (spinner) spinner.classList.add('hidden');
        });
    }

    setInterval(doPoll, pollIntervalMs);
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) doPoll();
    });
})();
</script>
<?= $this->endSection() ?>
