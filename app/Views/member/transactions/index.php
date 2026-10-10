<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Header Card -->
    <div class="relative overflow-hidden bg-gradient-to-r from-slate-900 via-brand-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl">
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-brand-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>MONITORING LIVE AKTIVITAS</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                    Live Transaksi KOMEO
                </h1>
                <p class="text-sm text-slate-300 max-w-2xl leading-relaxed">
                    Sistem pemantauan perkembangan transaksi ekosistem event di Indonesia. Seluruh progres dan status pembayaran dikelola dan diverifikasi manual oleh Administrator KOMEO.
                </p>
            </div>

            <!-- Refresh Indicator -->
            <div class="flex flex-col sm:items-end gap-1 shrink-0">
                <div class="flex items-center gap-2 text-xs text-slate-300">
                    <span id="poll-spinner" class="hidden">
                        <svg class="animate-spin h-3.5 w-3.5 text-brand-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </span>
                    <span id="last-updated-text" class="text-slate-400">Terakhir diperbarui: <?= date('H:i') ?> WIB</span>
                </div>
                <span class="text-[11px] text-slate-500 italic">Auto-refresh setiap <?= $pollInterval ?> detik</span>
            </div>
        </div>
    </div>

    <!-- Statistics Overview -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <span class="text-xs text-slate-500 font-medium block">Total Transaksi</span>
            <span class="text-2xl font-black text-slate-900 mt-1 block" id="stat-total"><?= $statistics['total'] ?></span>
            <span class="text-[10px] text-slate-400 mt-1 block">Aktivitas terpublikasi</span>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <span class="text-xs text-blue-600 font-medium block">Sedang Berjalan</span>
            <span class="text-2xl font-black text-blue-700 mt-1 block" id="stat-progress"><?= $statistics['in_progress'] ?></span>
            <span class="text-[10px] text-slate-400 mt-1 block">Dalam pengerjaan</span>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <span class="text-xs text-emerald-600 font-medium block">Pekerjaan Selesai</span>
            <span class="text-2xl font-black text-emerald-700 mt-1 block" id="stat-completed"><?= $statistics['completed'] ?></span>
            <span class="text-[10px] text-slate-400 mt-1 block">Tahap tuntas</span>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <span class="text-xs text-amber-600 font-medium block">Menunggu DP</span>
            <span class="text-2xl font-black text-amber-700 mt-1 block" id="stat-awaiting"><?= $statistics['awaiting_payment'] ?></span>
            <span class="text-[10px] text-slate-400 mt-1 block">Belum ada bayar</span>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <span class="text-xs text-indigo-600 font-medium block">DP Diterima</span>
            <span class="text-2xl font-black text-indigo-700 mt-1 block" id="stat-dp"><?= $statistics['dp_received'] ?></span>
            <span class="text-[10px] text-slate-400 mt-1 block">Uang muka masuk</span>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <span class="text-xs text-teal-600 font-medium block">Pembayaran Lunas</span>
            <span class="text-2xl font-black text-teal-700 mt-1 block" id="stat-paid"><?= $statistics['paid'] ?></span>
            <span class="text-[10px] text-slate-400 mt-1 block">Pelunasan penuh</span>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
        <form method="get" action="<?= base_url('dashboard/transaksi') ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2">
                <input type="text" name="search" value="<?= esc($filters['search'] ?? '') ?>" placeholder="Cari kode atau judul proyek..." class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 focus:outline-none focus:border-brand-500 focus:bg-white transition-colors">
            </div>
            <div>
                <select name="category_id" class="w-full px-3 py-2.5 rounded-xl text-xs bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:border-brand-500 focus:bg-white">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($filters['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                            <?= esc($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <select name="work_stage" class="w-full px-3 py-2.5 rounded-xl text-xs bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:border-brand-500 focus:bg-white">
                    <option value="">Semua Tahap Kerja</option>
                    <?php foreach ($workStages as $k => $v): ?>
                        <option value="<?= $k ?>" <?= ($filters['work_stage'] === $k) ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="w-full px-4 py-2.5 rounded-xl text-xs font-bold bg-brand-600 text-white hover:bg-brand-500 transition-colors">
                    Filter
                </button>
                <a href="<?= base_url('dashboard/transaksi') ?>" class="p-2.5 rounded-xl bg-slate-100 text-slate-500 hover:text-slate-800 transition-colors" title="Reset filter">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </a>
            </div>
        </form>
    </div>

    <!-- Transaction Cards Grid -->
    <div id="transactions-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php if (empty($transactions)): ?>
            <div class="md:col-span-2 lg:col-span-3 p-12 text-center rounded-3xl bg-white border border-slate-200/80 shadow-xs">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto text-slate-400 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 class="text-base font-bold text-slate-800">Belum ada transaksi yang dipublikasikan</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                    Data transaksi resmi yang telah diverifikasi oleh pengurus KOMEO akan segera tampil di sini.
                </p>
            </div>
        <?php else: ?>
            <?php foreach ($transactions as $t): ?>
                <div class="p-6 rounded-3xl bg-white border border-slate-200/80 hover:border-brand-300 shadow-xs hover:shadow-md transition-all flex flex-col justify-between gap-5 group">
                    <div class="space-y-4">
                        <!-- Top Meta -->
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-mono text-xs px-2.5 py-0.5 rounded-lg font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                <?= esc($t['transaction_code']) ?>
                            </span>
                            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-brand-50 text-brand-700">
                                <?= esc($t['category_name'] ?? 'Umum') ?>
                            </span>
                        </div>

                        <!-- Title -->
                        <div>
                            <h2 class="text-base font-black text-slate-900 group-hover:text-brand-600 transition-colors line-clamp-2">
                                <?= esc($t['public_title']) ?>
                            </h2>
                            <p class="text-xs text-slate-500 mt-1">
                                Tanggal: <?= date('d M Y', strtotime($t['transaction_date'])) ?>
                            </p>
                        </div>

                        <!-- Statuses -->
                        <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100">
                            <div>
                                <span class="text-[10px] text-slate-400 font-medium block">Progres Kerja:</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-indigo-50 text-indigo-700 mt-0.5">
                                    <?= esc($t['current_work_stage']) ?>
                                </span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 font-medium block">Pembayaran:</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-700 mt-0.5">
                                    <?= TransactionModel::PAYMENT_STATUSES[$t['payment_status']] ?? esc($t['payment_status']) ?>
                                </span>
                            </div>
                        </div>

                        <!-- Monetary (Only if approved and permitted by privacy policy) -->
                        <?php if (! empty($t['amount_visible'])): ?>
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 text-xs flex items-center justify-between">
                                <span class="text-slate-500">Nilai Disepakati:</span>
                                <strong class="text-slate-900 font-extrabold"><?= komeo_format_rupiah($t['total_amount']) ?></strong>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Card Footer -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[11px] text-slate-400">
                            Update: <?= date('d/m/Y', strtotime($t['updated_at'])) ?>
                        </span>
                        <a href="<?= base_url('dashboard/transaksi/' . $t['id']) ?>" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold bg-slate-900 text-white hover:bg-brand-600 transition-colors shadow-xs">
                            <span>Lihat Detail</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 flex items-center justify-between text-xs text-slate-600">
            <span>Halaman <?= $page ?> dari <?= $totalPages ?> (Total <?= $totalRecords ?> transaksi)</span>
            <div class="flex items-center gap-2">
                <?php if ($page > 1): ?>
                    <a href="<?= base_url('dashboard/transaksi?' . http_build_query(array_merge($filters, ['page' => $page - 1]))) ?>" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 font-bold text-slate-800">
                        Sebelumnya
                    </a>
                <?php endif; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="<?= base_url('dashboard/transaksi?' . http_build_query(array_merge($filters, ['page' => $page + 1]))) ?>" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 font-bold text-slate-800">
                        Selanjutnya
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- AJAX Polling Script -->
<script>
(function() {
    const pollIntervalMs = <?= (int) $pollInterval * 1000 ?>;
    let isPolling = false;
    let pollTimer = null;

    function doPoll() {
        if (document.hidden || isPolling) {
            return;
        }
        isPolling = true;
        const spinner = document.getElementById('poll-spinner');
        if (spinner) spinner.classList.remove('hidden');

        fetch('<?= base_url('dashboard/transaksi/poll') ?>', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.json();
        })
        .then(data => {
            if (data.status === 'ok') {
                const updatedEl = document.getElementById('last-updated-text');
                if (updatedEl) {
                    updatedEl.textContent = 'Terakhir diperbarui: ' + data.timestamp + ' WIB';
                }
                if (data.statistics) {
                    if (document.getElementById('stat-total')) document.getElementById('stat-total').textContent = data.statistics.total;
                    if (document.getElementById('stat-progress')) document.getElementById('stat-progress').textContent = data.statistics.in_progress;
                    if (document.getElementById('stat-completed')) document.getElementById('stat-completed').textContent = data.statistics.completed;
                    if (document.getElementById('stat-awaiting')) document.getElementById('stat-awaiting').textContent = data.statistics.awaiting_payment;
                    if (document.getElementById('stat-dp')) document.getElementById('stat-dp').textContent = data.statistics.dp_received;
                    if (document.getElementById('stat-paid')) document.getElementById('stat-paid').textContent = data.statistics.paid;
                }
            }
        })
        .catch(err => {
            console.debug('Polling background tick skipped:', err);
        })
        .finally(() => {
            isPolling = false;
            if (spinner) spinner.classList.add('hidden');
        });
    }

    // Start polling timer
    pollTimer = setInterval(doPoll, pollIntervalMs);

    // Refresh immediately when tab becomes active again
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            doPoll();
        }
    });
})();
</script>
<?= $this->endSection() ?>
