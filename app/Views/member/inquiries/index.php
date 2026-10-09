<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="space-y-6 max-w-5xl">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 rounded-3xl p-6 sm:p-8 text-white relative overflow-hidden shadow-xs">
        <div class="max-w-2xl relative z-10 space-y-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-brand-500/20 text-brand-300 border border-brand-500/30">
                <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                Peluang Bisnis & Kolaborasi
            </span>
            <h1 class="text-xl sm:text-3xl font-extrabold tracking-tight">Pesan & Permintaan Klien</h1>
            <p class="text-xs sm:text-sm text-slate-300">
                Kelola kontak langsung, tawaran pekerjaan, permintaan dokumen CV, dan percakapan dengan calon klien atau mitra bisnis.
            </p>
        </div>
    </div>

    <!-- Filter Pills & Status Counters -->
    <div class="flex flex-wrap items-center gap-2 p-1.5 bg-white rounded-2xl border border-slate-200/80 shadow-xs text-xs">
        <a href="<?= base_url('dashboard/permintaan') ?>" class="px-3.5 py-1.5 rounded-xl font-bold transition <?= empty($status) ? 'bg-brand-600 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
            Semua (<?= $stats['total'] ?>)
        </a>
        <a href="<?= base_url('dashboard/permintaan?status=new') ?>" class="px-3.5 py-1.5 rounded-xl font-bold transition <?= $status === 'new' ? 'bg-brand-600 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
            Baru (<?= $stats['new'] ?>)
        </a>
        <a href="<?= base_url('dashboard/permintaan?status=in_progress') ?>" class="px-3.5 py-1.5 rounded-xl font-bold transition <?= $status === 'in_progress' ? 'bg-brand-600 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
            Sedang Proses (<?= $stats['in_progress'] ?>)
        </a>
        <a href="<?= base_url('dashboard/permintaan?status=replied') ?>" class="px-3.5 py-1.5 rounded-xl font-bold transition <?= $status === 'replied' ? 'bg-brand-600 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
            Dibalas (<?= $stats['replied'] ?>)
        </a>
        <a href="<?= base_url('dashboard/permintaan?status=closed') ?>" class="px-3.5 py-1.5 rounded-xl font-bold transition <?= $status === 'closed' ? 'bg-brand-600 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
            Selesai (<?= $stats['closed'] ?>)
        </a>
        <a href="<?= base_url('dashboard/permintaan?status=spam') ?>" class="px-3.5 py-1.5 rounded-xl font-bold transition <?= $status === 'spam' ? 'bg-brand-600 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
            Spam (<?= $stats['spam'] ?>)
        </a>
    </div>

    <!-- Inquiries List -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <?php if (empty($inquiries)): ?>
            <div class="p-12 text-center text-slate-400 space-y-2">
                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                </div>
                <h3 class="text-sm font-bold text-slate-700">Kotak Masuk Permintaan Kosong</h3>
                <p class="text-xs text-slate-500">Belum ada pesan atau penawaran baru yang sesuai dengan filter.</p>
            </div>
        <?php else: ?>
            <div class="divide-y divide-slate-100">
                <?php foreach ($inquiries as $inq): ?>
                    <div class="p-5 sm:p-6 hover:bg-slate-50/70 transition flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 group">
                        <div class="space-y-1.5 min-w-0 flex-1">
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <!-- Status Badge -->
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase <?= $inq['status'] === 'new' ? 'bg-indigo-100 text-indigo-800 animate-pulse' : ($inq['status'] === 'replied' ? 'bg-emerald-100 text-emerald-800' : ($inq['status'] === 'in_progress' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600')) ?>">
                                    <?= esc($inq['status']) ?>
                                </span>

                                <!-- Inquiry Type Badge -->
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                    <?php
                                        $typeLabels = [
                                            'job_offer'          => 'Tawaran Pekerjaan',
                                            'cv_request'         => 'Permintaan CV',
                                            'portfolio_request'  => 'Permintaan Portofolio',
                                            'collaboration'      => 'Kolaborasi',
                                            'general'            => 'Pertanyaan Umum',
                                        ];
                                        echo $typeLabels[$inq['inquiry_type']] ?? $inq['inquiry_type'];
                                    ?>
                                </span>

                                <span class="text-[11px] text-slate-400">
                                    <?= date('d M Y, H:i', strtotime($inq['created_at'])) ?> WIB
                                </span>
                            </div>

                            <a href="<?= base_url('dashboard/permintaan/' . $inq['id']) ?>" class="block">
                                <h3 class="text-sm sm:text-base font-extrabold text-slate-900 group-hover:text-brand-600 transition-colors line-clamp-1">
                                    <?= esc($inq['subject']) ?>
                                </h3>
                                <p class="text-xs text-slate-500 line-clamp-2 mt-0.5">
                                    <?= esc($inq['initial_message']) ?>
                                </p>
                            </a>

                            <div class="flex items-center gap-2 text-xs text-slate-600 pt-1">
                                <span class="font-bold text-slate-800"><?= esc($inq['client_name']) ?></span>
                                <?php if (! empty($inq['client_organization'])): ?>
                                    <span>• <?= esc($inq['client_organization']) ?></span>
                                <?php endif; ?>
                                <?php if (! empty($inq['event_location'])): ?>
                                    <span>• Lokasi: <?= esc($inq['event_location']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="shrink-0 flex items-center gap-2 w-full sm:w-auto justify-end">
                            <a href="<?= base_url('dashboard/permintaan/' . $inq['id']) ?>" class="w-full sm:w-auto text-center px-4 py-2 rounded-xl text-xs font-bold text-brand-700 bg-brand-50 hover:bg-brand-100 border border-brand-200 transition shadow-xs">
                                Buka Pesan & Balas
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>
<?= $this->endSection() ?>
