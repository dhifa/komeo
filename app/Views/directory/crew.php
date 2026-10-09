<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="bg-slate-50 min-h-screen py-8 sm:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Hero Header -->
        <div class="text-center max-w-3xl mx-auto space-y-3.5 mb-8 sm:mb-10">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-brand-50 text-brand-700 border border-brand-200/60 shadow-xs">
                <span>👤 Direktori Talenta & Crew Produksi Event</span>
            </div>

            <h1 class="text-2xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">
                Cari Crew & Freelancer Event
            </h1>

            <p class="text-xs sm:text-base text-slate-600 leading-relaxed max-w-2xl mx-auto">
                Temukan profesional dan freelancer untuk mendukung kebutuhan produksi event Anda.
            </p>

            <!-- Quick Navigation Pills -->
            <div class="pt-2 flex flex-wrap items-center justify-center gap-2">
                <a href="<?= base_url('member') ?>" class="px-4 py-2 rounded-xl text-xs font-bold bg-white text-slate-700 border border-slate-200 hover:border-brand-300 hover:text-brand-600 transition shadow-2xs">
                    Semua Direktori
                </a>
                <a href="<?= base_url('cari-vendor') ?>" class="px-4 py-2 rounded-xl text-xs font-bold bg-white text-slate-700 border border-slate-200 hover:border-brand-300 hover:text-brand-600 transition shadow-2xs">
                    🏢 Cari Vendor Event
                </a>
                <a href="<?= base_url('cari-crew') ?>" class="px-4 py-2 rounded-xl text-xs font-bold bg-brand-600 text-white shadow-xs">
                    👤 Cari Crew & Freelancer
                </a>
            </div>
        </div>

        <!-- Search & Filter Controls -->
        <div class="mb-6 sm:mb-8">
            <?= view('directory/_filters', [
                'actionUrl'         => base_url('cari-crew'),
                'data'              => $data,
                'options'           => $options,
                'lockType'          => true, // Locked to individual
                'searchPlaceholder' => 'Cari nama kru, operator vMix, sound engineer, kamera...',
            ]) ?>
        </div>

        <!-- Results Section -->
        <div class="space-y-6">
            <!-- Results Counter & Info Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-slate-500 font-semibold px-1">
                <div>
                    Menemukan <span class="text-slate-900 font-extrabold"><?= number_format($data['total']) ?></span> talenta & crew event terverifikasi
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

            <!-- Crew Cards Grid (Consistent Height with items-stretch) -->
            <?php if (! empty($data['items'])): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-7 items-stretch">
                    <?php foreach ($data['items'] as $item): ?>
                        <?= view('directory/_member_card', ['item' => $item, 'isVendorView' => false]) ?>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <div class="pt-4">
                    <?= view('directory/_pagination', [
                        'data'    => $data,
                        'baseUrl' => base_url('cari-crew'),
                    ]) ?>
                </div>

            <?php else: ?>
                <!-- Empty State -->
                <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 p-8 sm:p-12 text-center max-w-xl mx-auto space-y-4 shadow-xs">
                    <div class="w-16 h-16 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <h3 class="text-base font-extrabold text-slate-900">Tidak Ada Crew Ditemukan</h3>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        Tidak ditemukan profesional atau freelancer yang cocok dengan kriteria pencarian dan keahlian yang Anda pilih.
                    </p>
                    <div class="pt-2">
                        <a href="<?= base_url('cari-crew') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition shadow-xs">
                            Reset Filter Crew
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
