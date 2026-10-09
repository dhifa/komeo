<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="bg-slate-50 min-h-screen py-8 sm:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Hero Header -->
        <div class="text-center max-w-3xl mx-auto space-y-3.5 mb-8 sm:mb-10">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-brand-50 text-brand-700 border border-brand-200/60 shadow-xs">
                <span class="w-2 h-2 rounded-full bg-brand-600 animate-pulse"></span>
                <span>Direktori Komunitas Resmi KOMEO.ID</span>
            </div>

            <h1 class="text-2xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">
                <?= esc(site_setting('Directory.title', 'Temukan Profesional Event Terbaik di KOMEO')) ?>
            </h1>

            <p class="text-xs sm:text-base text-slate-600 leading-relaxed max-w-2xl mx-auto">
                <?= esc(site_setting('Directory.subtitle', 'Jelajahi jaringan Event Organizer, vendor, freelancer, dan talent dari berbagai daerah di Indonesia.')) ?>
            </p>

            <!-- Quick Navigation Pills -->
            <div class="pt-2 flex flex-wrap items-center justify-center gap-2">
                <a href="<?= base_url('member') ?>" class="px-4 py-2 rounded-xl text-xs font-bold bg-brand-600 text-white shadow-xs">
                    Semua Direktori
                </a>
                <a href="<?= base_url('cari-vendor') ?>" class="px-4 py-2 rounded-xl text-xs font-bold bg-white text-slate-700 border border-slate-200 hover:border-brand-300 hover:text-brand-600 transition shadow-2xs">
                    🏢 Cari Vendor Event
                </a>
                <a href="<?= base_url('cari-crew') ?>" class="px-4 py-2 rounded-xl text-xs font-bold bg-white text-slate-700 border border-slate-200 hover:border-brand-300 hover:text-brand-600 transition shadow-2xs">
                    👤 Cari Crew & Freelancer
                </a>
            </div>
        </div>

        <!-- Featured Members Spotlight (Proportionally distributed for 1-3 members) -->
        <?php if (! empty($featuredMembers)): ?>
            <?php
            $featCount = count($featuredMembers);
            $featGridClass = match($featCount) {
                1 => 'max-w-md mx-auto',
                2 => 'grid grid-cols-1 md:grid-cols-2 max-w-4xl mx-auto gap-6 sm:gap-8 items-stretch',
                default => 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 items-stretch',
            };
            ?>
            <div class="bg-gradient-to-br from-brand-900 via-indigo-950 to-slate-900 rounded-2xl sm:rounded-3xl p-6 sm:p-8 text-white shadow-xl space-y-6 mb-8 sm:mb-10">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-white/10 pb-4">
                    <div>
                        <div class="inline-flex items-center gap-1.5 text-xs font-extrabold text-amber-400 uppercase tracking-wider mb-1">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <span>Sorotan Anggota KOMEO</span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-extrabold text-white">Member Pilihan & Terpopuler</h2>
                    </div>
                    <span class="text-xs text-indigo-200">
                        Profil terverifikasi dengan portofolio aktif
                    </span>
                </div>

                <div class="<?= $featGridClass ?>">
                    <?php foreach ($featuredMembers as $featItem): ?>
                        <?= view('directory/_member_card', ['item' => $featItem]) ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Search & Filter Controls -->
        <div class="mb-6 sm:mb-8">
            <?= view('directory/_filters', [
                'actionUrl'         => base_url('member'),
                'data'              => $data,
                'options'           => $options,
                'searchPlaceholder' => 'Cari nama, vendor, atau keahlian...',
            ]) ?>
        </div>

        <!-- Directory Results Section -->
        <div class="space-y-6">
            <!-- Results Counter & Info Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-slate-500 font-semibold px-1">
                <div>
                    Menemukan <span class="text-slate-900 font-extrabold"><?= number_format($data['total']) ?></span> profesional event terdaftar
                    <?php if (! empty($data['items'])): ?>
                        <span class="text-slate-400 font-normal"> (menampilkan <?= count($data['items']) ?> profil pada halaman ini)</span>
                    <?php endif; ?>
                </div>

                <?php if (! empty($data['filters']['q'])): ?>
                    <div>
                        Kata kunci: <span class="text-brand-600 font-bold">"<?= esc($data['filters']['q']) ?>"</span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Members Grid (Consistent Height with items-stretch) -->
            <?php if (! empty($data['items'])): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-7 items-stretch">
                    <?php foreach ($data['items'] as $item): ?>
                        <?= view('directory/_member_card', ['item' => $item]) ?>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <div class="pt-4">
                    <?= view('directory/_pagination', [
                        'data'    => $data,
                        'baseUrl' => base_url('member'),
                    ]) ?>
                </div>

            <?php else: ?>
                <!-- Empty State -->
                <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 p-8 sm:p-12 text-center max-w-xl mx-auto space-y-4 shadow-xs">
                    <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <h3 class="text-base font-extrabold text-slate-900">Tidak Ada Member Ditemukan</h3>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        Tidak ditemukan member terverifikasi yang cocok dengan kriteria pencarian atau filter yang Anda terapkan.
                    </p>
                    <div class="pt-2">
                        <a href="<?= base_url('member') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition shadow-xs">
                            Reset Semua Filter
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
