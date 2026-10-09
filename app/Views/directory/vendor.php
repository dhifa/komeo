<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="bg-slate-50 min-h-screen py-8 sm:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Hero Header -->
        <div class="text-center max-w-3xl mx-auto space-y-3.5 mb-8 sm:mb-10">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200/60 shadow-xs">
                <span>🏢 Direktori Vendor & Perusahaan Event</span>
            </div>

            <h1 class="text-2xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">
                Cari Vendor Event
            </h1>

            <p class="text-xs sm:text-base text-slate-600 leading-relaxed max-w-2xl mx-auto">
                Temukan vendor dan perusahaan penyedia kebutuhan event di berbagai kota di Indonesia.
            </p>

            <!-- Quick Navigation Pills -->
            <div class="pt-2 flex flex-wrap items-center justify-center gap-2">
                <a href="<?= base_url('member') ?>" class="px-4 py-2 rounded-xl text-xs font-bold bg-white text-slate-700 border border-slate-200 hover:border-brand-300 hover:text-brand-600 transition shadow-2xs">
                    Semua Direktori
                </a>
                <a href="<?= base_url('cari-vendor') ?>" class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 text-white shadow-xs">
                    🏢 Cari Vendor Event
                </a>
                <a href="<?= base_url('cari-crew') ?>" class="px-4 py-2 rounded-xl text-xs font-bold bg-white text-slate-700 border border-slate-200 hover:border-brand-300 hover:text-brand-600 transition shadow-2xs">
                    👤 Cari Crew & Freelancer
                </a>
            </div>
        </div>

        <!-- Search & Filter Controls -->
        <div class="mb-6 sm:mb-8">
            <?= view('directory/_filters', [
                'actionUrl'         => base_url('cari-vendor'),
                'data'              => $data,
                'options'           => $options,
                'lockType'          => true, // Locked to business
                'searchPlaceholder' => 'Cari nama vendor, bidang jasa, atau kota...',
            ]) ?>
        </div>

        <!-- Results Section -->
        <div class="space-y-6">
            <!-- Results Counter & Info Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-slate-500 font-semibold px-1">
                <div>
                    Menemukan <span class="text-slate-900 font-extrabold"><?= number_format($data['total']) ?></span> vendor & perusahaan event terverifikasi
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

            <!-- Vendor Cards Grid (Consistent Height with items-stretch) -->
            <?php if (! empty($data['items'])): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-7 items-stretch">
                    <?php foreach ($data['items'] as $item): ?>
                        <?= view('directory/_member_card', ['item' => $item, 'isVendorView' => true]) ?>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <div class="pt-4">
                    <?= view('directory/_pagination', [
                        'data'    => $data,
                        'baseUrl' => base_url('cari-vendor'),
                    ]) ?>
                </div>

            <?php else: ?>
                <!-- Empty State -->
                <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 p-8 sm:p-12 text-center max-w-xl mx-auto space-y-4 shadow-xs">
                    <div class="w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-400 flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <h3 class="text-base font-extrabold text-slate-900">Tidak Ada Vendor Ditemukan</h3>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        Tidak ditemukan vendor atau perusahaan yang cocok dengan kriteria pencarian dan filter lokasi saat ini.
                    </p>
                    <div class="pt-2">
                        <a href="<?= base_url('cari-vendor') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition shadow-xs">
                            Reset Filter Vendor
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
