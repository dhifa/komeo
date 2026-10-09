<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header Section -->
<section class="bg-gradient-to-b from-slate-900 to-slate-800 text-white py-16 sm:py-20 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#6366f1_1px,transparent_1px)] [background-size:16px_16px]"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="max-w-3xl">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-brand-500/20 text-brand-300 border border-brand-500/30 mb-4">
                <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Agenda & Kegiatan Resmi
            </span>
            <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-white mb-4">
                Kegiatan & Acara Komunitas
            </h1>
            <p class="text-base sm:text-lg text-slate-300 leading-relaxed">
                Temukan seminar, workshop, festival, dan sesi jejaring bisnis eksklusif bagi praktisi industri event organizer di seluruh Indonesia.
            </p>
        </div>
    </div>
</section>

<!-- Filter & Event Showcase -->
<section class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Filter Bar -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mr-2">Tipe Kegiatan:</span>
                <a href="<?= base_url('kegiatan') ?>" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition <?= empty($currentType) ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                    Semua
                </a>
                <a href="<?= base_url('kegiatan?type=internal') ?>" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition <?= $currentType === 'internal' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                    Internal Member
                </a>
                <a href="<?= base_url('kegiatan?type=external') ?>" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition <?= $currentType === 'external' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                    Peserta Umum
                </a>
                <a href="<?= base_url('kegiatan?type=hybrid') ?>" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition <?= $currentType === 'hybrid' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                    Hybrid (Member & Umum)
                </a>
            </div>

            <div class="text-xs text-slate-500 font-semibold">
                Menampilkan <span class="font-bold text-slate-900"><?= count($events) ?></span> agenda kegiatan
            </div>
        </div>

        <?php if (empty($events)): ?>
            <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center max-w-xl mx-auto shadow-xs">
                <div class="w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-1">Belum Ada Kegiatan Terjadwal</h3>
                <p class="text-xs sm:text-sm text-slate-500 mb-6">Saat ini belum ada agenda kegiatan aktif untuk kategori yang dipilih. Silakan periksa kembali nanti.</p>
                <a href="<?= base_url('kegiatan') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-brand-600 bg-brand-50 hover:bg-brand-100 transition">
                    Lihat Semua Agenda
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                <?php foreach ($events as $event): ?>
                    <div class="bg-white rounded-3xl border border-slate-200/80 overflow-hidden shadow-xs hover:shadow-lg transition-all duration-300 flex flex-col justify-between group">
                        <div>
                            <!-- Banner Thumbnail -->
                            <div class="relative h-48 w-full bg-slate-900 overflow-hidden">
                                <?php if (! empty($event['banner_path']) && file_exists(FCPATH . $event['banner_path'])): ?>
                                    <img src="<?= base_url(esc($event['banner_path'])) ?>" alt="<?= esc($event['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-tr from-brand-900 via-indigo-950 to-slate-900 text-brand-400">
                                        <svg class="w-12 h-12 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                <?php endif; ?>

                                <!-- Event Type Badge -->
                                <div class="absolute top-3.5 left-3.5">
                                    <?php if ($event['event_type'] === 'internal'): ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-extrabold bg-indigo-900/90 text-indigo-200 backdrop-blur-sm border border-indigo-700/50 shadow-xs">
                                            Khusus Member
                                        </span>
                                    <?php elseif ($event['event_type'] === 'external'): ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-extrabold bg-emerald-900/90 text-emerald-200 backdrop-blur-sm border border-emerald-700/50 shadow-xs">
                                            Peserta Umum
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-extrabold bg-amber-900/90 text-amber-200 backdrop-blur-sm border border-amber-700/50 shadow-xs">
                                            Hybrid (Member & Umum)
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="p-6 space-y-4">
                                <div class="flex items-center gap-2 text-xs font-semibold text-brand-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span><?= date('d M Y, H:i', strtotime($event['start_date'])) ?> WIB</span>
                                </div>

                                <h3 class="text-lg font-bold text-slate-900 group-hover:text-brand-600 transition-colors line-clamp-2">
                                    <a href="<?= base_url('kegiatan/' . esc($event['slug'])) ?>">
                                        <?= esc($event['title']) ?>
                                    </a>
                                </h3>

                                <div class="flex items-center gap-2 text-xs text-slate-500">
                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span class="truncate"><?= esc($event['venue_name'] ?: 'Lokasi diinformasikan segera') ?> (<?= esc($event['city'] ?: 'Indonesia') ?>)</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="p-6 pt-0 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-500">
                                <?= $event['total_quota'] > 0 ? 'Kuota: ' . $event['total_quota'] . ' Peserta' : 'Kuota Terbuka' ?>
                            </span>

                            <a href="<?= base_url('kegiatan/' . esc($event['slug'])) ?>" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-slate-900 group-hover:bg-brand-600 transition-colors shadow-xs">
                                <span>Detail Acara</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>
<?= $this->endSection() ?>
