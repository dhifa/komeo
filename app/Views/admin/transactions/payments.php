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

    <!-- Financial Calculation Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800">
            <span class="text-xs text-slate-400 font-medium block">Total Nilai Disepakati</span>
            <span class="text-xl font-black text-white mt-1 block">
                <?= komeo_format_rupiah($trx['total_amount']) ?>
            </span>
            <span class="text-[11px] text-slate-500 mt-1 block">Nilai kontrak yang disepakati</span>
        </div>
        <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800">
            <span class="text-xs text-emerald-400 font-medium block">Net Dana Diterima</span>
            <span class="text-xl font-black text-emerald-300 mt-1 block">
                <?= komeo_format_rupiah($financials['net_received'] ?? 0) ?>
            </span>
            <span class="text-[11px] text-slate-500 mt-1 block">
                Progress: <?= ($financials['payment_percentage'] !== null) ? $financials['payment_percentage'] . '%' : 'N/A' ?>
            </span>
        </div>
        <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800">
            <span class="text-xs text-amber-400 font-medium block">Sisa Saldo Pembayaran</span>
            <span class="text-xl font-black text-amber-300 mt-1 block">
                <?= komeo_format_rupiah($financials['outstanding_balance'] ?? null) ?>
            </span>
            <span class="text-[11px] text-slate-500 mt-1 block">Belum dibayarkan oleh klien</span>
        </div>
        <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800">
            <span class="text-xs text-slate-400 font-medium block">Status Rekonsiliasi</span>
            <span class="text-lg font-black text-white mt-1 block">
                <?= TransactionModel::PAYMENT_STATUSES[$trx['payment_status'] ?? 'UNPAID'] ?? esc($trx['payment_status']) ?>
            </span>
            <?php if (! empty($trx['is_overdue'])): ?>
                <span class="text-[11px] text-rose-400 font-bold block mt-1">⚠️ Terlambat dari jatuh tempo</span>
            <?php else: ?>
                <span class="text-[11px] text-emerald-400 block mt-1">✓ Tertib pembayaran</span>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Record Payment Form -->
        <div class="lg:col-span-1">
            <div class="p-6 rounded-3xl bg-slate-900/80 border border-slate-800 shadow-sm sticky top-6">
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Catat Pembayaran Masuk</span>
                </h2>
                <p class="text-xs text-slate-400 mt-1">
                    Hanya dana yang benar-benar diterima dan diverifikasi mutasinya yang dicatat di sini.
                </p>

                <form method="post" action="<?= base_url('admin/transactions/' . $trx['id'] . '/payments') ?>" class="space-y-4 mt-5">
                    <?= csrf_field() ?>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">
                            Jenis Pembayaran <span class="text-rose-400">*</span>
                        </label>
                        <select name="payment_type" required class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-brand-500">
                            <option value="DP">DP (Uang Muka)</option>
                            <option value="Cicilan / Termin">Cicilan / Termin</option>
                            <option value="Pelunasan">Pelunasan</option>
                            <option value="Penyesuaian">Penyesuaian</option>
                            <option value="Refund">Refund (Pengembalian Dana)</option>
                            <option value="Koreksi">Koreksi</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">
                            Nominal (IDR) <span class="text-rose-400">*</span>
                        </label>
                        <input type="number" step="0.01" name="amount" required placeholder="Contoh: 5000000" class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-brand-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">
                            Tanggal Pembayaran <span class="text-rose-400">*</span>
                        </label>
                        <input type="date" name="payment_date" value="<?= date('Y-m-d') ?>" required class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-brand-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">
                            Metode Pembayaran
                        </label>
                        <input type="text" name="payment_method" placeholder="Contoh: Transfer Bank BCA / Mandiri" class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-brand-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">
                            Nomor Referensi / Bukti Transfer
                        </label>
                        <input type="text" name="reference" placeholder="Contoh: TRX-20261010-8812" class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-brand-500">
                        <p class="text-[11px] text-slate-500 mt-1">Nomor rekening dan bukti privat tidak akan diungkap ke member.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">
                            Catatan Internal Pembukuan
                        </label>
                        <textarea name="internal_note" rows="2" placeholder="Catatan internal kas/bendahara..." class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-brand-500"></textarea>
                    </div>

                    <button type="submit" class="w-full px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-500 transition-colors shadow-md shadow-emerald-950/40">
                        Bukukan Pembayaran
                    </button>
                </form>
            </div>
        </div>

        <!-- Payments History List -->
        <div class="lg:col-span-2 space-y-4">
            <div class="p-6 rounded-3xl bg-slate-900/80 border border-slate-800 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <div>
                        <h2 class="text-base font-bold text-white">Riwayat Transaksi Pembayaran</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Daftar mutasi pembayaran yang sah dan audit pembatalan.</p>
                    </div>
                    <span class="text-xs px-2.5 py-1 rounded-full bg-slate-800 text-slate-300 font-bold">
                        <?= count($payments) ?> Rekord
                    </span>
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-300">
                        <thead class="bg-slate-950/60 border-b border-slate-800 text-slate-400 uppercase text-[10px] font-bold tracking-wider">
                            <tr>
                                <th class="p-3">Tanggal</th>
                                <th class="p-3">Tipe</th>
                                <th class="p-3">Nominal</th>
                                <th class="p-3">Metode & Ref</th>
                                <th class="p-3">Status Audit</th>
                                <th class="p-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            <?php if (empty($payments)): ?>
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-slate-500">
                                        Belum ada catatan pembayaran untuk transaksi ini.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($payments as $p): ?>
                                    <tr class="<?= ! empty($p['voided_at']) ? 'bg-rose-950/10 opacity-70' : 'hover:bg-slate-850/50' ?> transition-colors">
                                        <td class="p-3 font-mono">
                                            <?= date('d/m/Y', strtotime($p['payment_date'])) ?>
                                        </td>
                                        <td class="p-3">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-slate-200 border border-slate-700">
                                                <?= esc($p['payment_type']) ?>
                                            </span>
                                        </td>
                                        <td class="p-3 font-bold <?= ! empty($p['voided_at']) ? 'text-slate-400 line-through' : 'text-emerald-400' ?>">
                                            <?= komeo_format_rupiah($p['amount']) ?>
                                        </td>
                                        <td class="p-3">
                                            <span class="text-white block font-medium"><?= esc($p['payment_method'] ?: 'Transfer') ?></span>
                                            <span class="text-[10px] text-slate-400 block font-mono"><?= esc($p['reference'] ?: 'Tanpa Ref') ?></span>
                                        </td>
                                        <td class="p-3">
                                            <?php if (! empty($p['voided_at'])): ?>
                                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-rose-500/20 text-rose-300 border border-rose-500/30 block w-max">
                                                    DIBATALKAN (VOID)
                                                </span>
                                                <span class="text-[10px] text-rose-400 block mt-1" title="<?= esc($p['void_reason']) ?>">
                                                    Alasan: <?= esc($p['void_reason']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 block w-max">
                                                    Sah / Dibukukan
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="p-3 text-right">
                                            <?php if (empty($p['voided_at'])): ?>
                                                <button type="button" onclick="openVoidModal(<?= $p['id'] ?>, '<?= esc($p['payment_type']) ?>', '<?= komeo_format_rupiah($p['amount']) ?>')" class="px-2.5 py-1 rounded-lg text-xs font-bold text-rose-400 hover:text-white bg-rose-500/10 hover:bg-rose-600 transition-colors">
                                                    Batalkan
                                                </button>
                                            <?php else: ?>
                                                <span class="text-slate-500 text-[10px] italic">Voided</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pembatalan (Void) Pembayaran -->
<div id="voidModal" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="w-full max-w-md p-6 rounded-3xl bg-slate-900 border border-slate-800 shadow-2xl space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Batalkan Pembayaran (Void)</span>
            </h3>
            <button type="button" onclick="closeVoidModal()" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <p class="text-xs text-slate-300">
            Pembatalan pembayaran tidak akan menghapus data dari database, melainkan ditandai sebagai pembatalan yang tercatat dalam audit log.
        </p>

        <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 text-xs">
            <span id="voidPaymentInfo" class="font-bold text-white block"></span>
        </div>

        <form method="post" action="<?= base_url('admin/transactions/' . $trx['id'] . '/payments/void') ?>" class="space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="payment_id" id="voidPaymentId" value="">

            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1.5">
                    Alasan Pembatalan / Koreksi <span class="text-rose-400">*</span>
                </label>
                <textarea name="void_reason" required rows="3" placeholder="Jelaskan alasan pembatalan (misal: salah input nominal, cek retur, bukti palsu)..." class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-rose-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="closeVoidModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-400 hover:text-white bg-slate-800">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-rose-600 hover:bg-rose-500 text-white transition-colors">
                    Konfirmasi Pembatalan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openVoidModal(id, type, amount) {
    document.getElementById('voidPaymentId').value = id;
    document.getElementById('voidPaymentInfo').textContent = 'Pembayaran: ' + type + ' (' + amount + ')';
    document.getElementById('voidModal').classList.remove('hidden');
}
function closeVoidModal() {
    document.getElementById('voidModal').classList.add('hidden');
}
</script>
<?= $this->endSection() ?>
