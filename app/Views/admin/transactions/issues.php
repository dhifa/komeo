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

    <!-- Important Legal Disclaimer Notice -->
    <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-200 text-xs flex items-start gap-3">
        <svg class="w-5 h-5 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div class="space-y-1">
            <strong class="font-bold text-amber-300 block">Protokol Penanganan Masalah Kontrak & Perlindungan Asas Praduga:</strong>
            <p class="leading-relaxed">
                Keterlambatan pembayaran tidak otomatis disimpulkan sebagai wanprestasi hukum. Status "Indikasi Wanprestasi" adalah klasifikasi pendahuluan internal admin dan tidak boleh digunakan untuk menuduh pihak mana pun secara sepihak. Pada tampilan member, sistem secara netral menampilkan status <strong>"Dalam Penanganan"</strong> demi menjaga kerahasiaan para pihak dan menghindari pencemaran nama baik.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Record / Update Issue Form -->
        <div class="lg:col-span-1">
            <div class="p-6 rounded-3xl bg-slate-900/80 border border-slate-800 shadow-sm sticky top-6">
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Catat / Perbarui Isu Kontrak</span>
                </h2>

                <form method="post" action="<?= base_url('admin/transactions/' . $trx['id'] . '/issues') ?>" class="space-y-4 mt-5">
                    <?= csrf_field() ?>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">
                            Status Penanganan <span class="text-rose-400">*</span>
                        </label>
                        <select name="status" required class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-brand-500">
                            <?php foreach ($issueStatuses as $k => $v): ?>
                                <option value="<?= $k ?>" <?= ($trx['contract_issue_status'] === $k) ? 'selected' : '' ?>>
                                    <?= $v ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">
                            Jenis Permasalahan <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" name="issue_type" required placeholder="Contoh: Keterlambatan Termin II / Ketidaksesuaian Rider" class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-brand-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">
                            Deskripsi Internal / Bukti Penunjang <span class="text-rose-400">*</span>
                        </label>
                        <textarea name="description" rows="3" required placeholder="Deskripsi temuan, kronologi, nomor referensi aduan, atau tindak lanjut..." class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-brand-500"></textarea>
                        <p class="text-[11px] text-slate-500 mt-1">Detail tuduhan dan identitas pihak ketiga tetap rahasia internal admin.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">
                            Catatan Resolusi / Kesepakatan Damai
                        </label>
                        <textarea name="resolution_note" rows="2" placeholder="Diisi saat permasalahan telah mencapai solusi damai atau addendum..." class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-brand-500"></textarea>
                    </div>

                    <button type="submit" class="w-full px-4 py-2.5 rounded-xl text-xs font-bold bg-brand-600 text-white hover:bg-brand-500 transition-colors shadow-md shadow-brand-900/30">
                        Simpan Catatan Masalah
                    </button>
                </form>
            </div>
        </div>

        <!-- Issue History List -->
        <div class="lg:col-span-2 space-y-4">
            <div class="p-6 rounded-3xl bg-slate-900/80 border border-slate-800 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <div>
                        <h2 class="text-base font-bold text-white">Riwayat Pengawasan Isu & Sengketa</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Semua eskalasi dan catatan penanganan kontrak tersimpan lengkap.</p>
                    </div>
                    <span class="text-xs px-2.5 py-1 rounded-full bg-slate-800 text-slate-300 font-bold">
                        <?= count($issues) ?> Isu Tercatat
                    </span>
                </div>

                <div class="mt-6 space-y-4">
                    <?php if (empty($issues)): ?>
                        <div class="p-8 text-center text-slate-500 text-sm">
                            Tidak ada catatan masalah kontrak untuk transaksi ini. Seluruh alur berjalan tertib.
                        </div>
                    <?php else: ?>
                        <?php foreach ($issues as $iss): ?>
                            <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-2">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <?php
                                            $st = $iss['status'];
                                            $stColor = match($st) {
                                                'resolved'         => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
                                                'suspected_breach' => 'bg-rose-500/20 text-rose-300 border-rose-500/30',
                                                'disputed'         => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                                                default            => 'bg-purple-500/20 text-purple-300 border-purple-500/30',
                                            };
                                        ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold border <?= $stColor ?>">
                                            <?= esc(TransactionModel::CONTRACT_ISSUE_STATUSES[$st] ?? $st) ?>
                                        </span>
                                        <span class="text-xs font-bold text-white">
                                            <?= esc($iss['issue_type']) ?>
                                        </span>
                                    </div>
                                    <span class="text-[11px] text-slate-400 font-mono">
                                        <?= date('d/m/Y H:i', strtotime($iss['created_at'])) ?>
                                    </span>
                                </div>

                                <p class="text-xs text-slate-300 leading-relaxed">
                                    <?= nl2br(esc($iss['description'])) ?>
                                </p>

                                <?php if (! empty($iss['resolution_note'])): ?>
                                    <div class="p-3 rounded-xl bg-emerald-950/30 border border-emerald-500/20 text-xs text-emerald-200">
                                        <strong class="font-bold text-emerald-400 block mb-0.5">Hasil Resolusi / Kesepakatan:</strong>
                                        <?= nl2br(esc($iss['resolution_note'])) ?>
                                    </div>
                                <?php endif; ?>

                                <div class="pt-2 border-t border-slate-850 flex items-center justify-between text-[11px] text-slate-400">
                                    <span>Tampilan di portal member: <strong class="text-slate-300">"<?= esc($iss['public_label']) ?>"</strong></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
