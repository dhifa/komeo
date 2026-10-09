<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="bg-slate-50 min-h-screen py-8 sm:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Category Hero Banner -->
        <div class="bg-gradient-to-br from-brand-900 via-indigo-900 to-slate-900 rounded-2xl sm:rounded-3xl p-6 sm:p-10 text-white shadow-xl relative overflow-hidden mb-6 sm:mb-8">
            <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-3xl space-y-3.5">
                <nav class="flex items-center gap-2 text-xs font-semibold text-indigo-200">
                    <a href="<?= base_url('member') ?>" class="hover:text-white transition">Direktori</a>
                    <span>/</span>
                    <span class="text-white">Kategori</span>
                    <span>/</span>
                    <span class="text-amber-300 font-bold"><?= esc($category['name']) ?></span>
                </nav>

                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white/10 text-indigo-100 border border-white/20 backdrop-blur-xs">
                    <span>Sektor Industri Event</span>
                </div>

                <h1 class="text-2xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white leading-tight">
                    <?= esc($category['name']) ?>
                </h1>

                <p class="text-xs sm:text-sm text-indigo-100/90 leading-relaxed max-w-2xl">
                    <?= esc(! empty($category['description']) ? $category['description'] : "Temukan jejaring vendor, profesional, dan talenta terverifikasi dalam kategori {$category['name']} di seluruh Indonesia.") ?>
                </p>
            </div>
        </div>

        <!-- Related Categories Chips -->
        <?php if (! empty($relatedCategories)): ?>
            <div class="space-y-2.5 mb-6 sm:mb-8">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kategori Terkait Lainnya</h3>
                <div class="flex flex-wrap items-center gap-2">
                    <?php foreach ($relatedCategories as $rc): ?>
                        <a href="<?= base_url('kategori/' . esc($rc['slug'])) ?>" 
                           class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-white text-slate-700 hover:bg-brand-50 hover:text-brand-600 hover:border-brand-200 border border-slate-200 shadow-2xs transition">
                            <?= esc($rc['name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Search & Filter Controls -->
        <div class="mb-6 sm:mb-8">
            <?= view('directory/_filters', [
                'actionUrl'         => base_url('kategori/' . esc($category['slug'])),
                'data'              => $data,
                'options'           => $options,
                'lockCategory'      => true, // Category locked to this slug
                'searchPlaceholder' => "Cari nama member atau vendor dalam {$category['name']}...",
            ]) ?>
        </div>

        <!-- Results Section -->
        <div class="space-y-6">
            <!-- Results Counter & Info Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-slate-500 font-semibold px-1">
                <div>
                    Menemukan <span class="text-slate-900 font-extrabold"><?= number_format($data['total']) ?></span> member dalam kategori <span class="text-brand-600 font-bold"><?= esc($category['name']) ?></span>
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

            <!-- Matching Members Grid (Consistent Height with items-stretch) -->
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
                        'baseUrl' => base_url('kategori/' . esc($category['slug'])),
                    ]) ?>
                </div>

            <?php else: ?>
                <!-- Empty State -->
                <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 p-8 sm:p-12 text-center max-w-xl mx-auto space-y-4 shadow-xs">
                    <div class="w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <h3 class="text-base font-extrabold text-slate-900">Belum Ada Member di Kategori Ini</h3>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        Belum ada anggota publik aktif yang cocok dengan filter pada kategori <?= esc($category['name']) ?>.
                    </p>
                    <div class="pt-2 flex flex-wrap items-center justify-center gap-3">
                        <a href="<?= base_url('kategori/' . esc($category['slug'])) ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition shadow-xs">
                            Reset Filter
                        </a>
                        <a href="<?= base_url('member') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                            Lihat Semua Member
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
