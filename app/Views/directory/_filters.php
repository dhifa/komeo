<?php
/**
 * Directory Search & Filter Form Partial
 * @var string $actionUrl
 * @var array $data
 * @var array $options
 * @var bool $lockType (e.g. true for /cari-vendor or /cari-crew)
 * @var bool $lockCategory (e.g. true for /kategori/{slug})
 */
$f = $data['filters'] ?? [];
$hasActiveFilters = ! empty($f['q']) || ! empty($f['type']) || ! empty($f['category']) || ! empty($f['specialization']) || ! empty($f['province']) || ! empty($f['city']);
$clearUrl = $actionUrl;
?>

<!-- Search Bar & Main Filter Trigger -->
<div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-4 sm:p-6 space-y-4">
    <form id="directorySearchForm" action="<?= esc($actionUrl) ?>" method="get" class="space-y-4">
        
        <!-- Search Input Row -->
        <div class="relative flex flex-col sm:flex-row items-stretch gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" 
                       name="q" 
                       id="searchInput"
                       value="<?= esc($f['q'] ?? '') ?>" 
                       placeholder="<?= esc($searchPlaceholder ?? 'Cari nama, vendor, atau keahlian...') ?>" 
                       class="w-full pl-11 pr-4 py-3 sm:py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs sm:text-sm font-medium text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition">
            </div>

            <div class="flex items-center gap-2">
                <!-- Mobile Filter Drawer Toggle Button -->
                <button type="button" 
                        onclick="toggleMobileFilters()" 
                        class="sm:hidden flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 transition">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    <span>Filter</span>
                    <?php if ($hasActiveFilters): ?>
                        <span class="w-2 h-2 rounded-full bg-brand-600"></span>
                    <?php endif; ?>
                </button>

                <!-- Submit Search Button -->
                <button type="submit" 
                        class="px-6 sm:px-8 py-3 sm:py-3.5 rounded-2xl text-xs sm:text-sm font-bold text-white bg-brand-600 hover:bg-brand-700 shadow-sm shadow-brand-500/25 transition flex items-center justify-center gap-2 shrink-0">
                    <span>Cari</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </div>
        </div>

        <!-- Desktop Filter Dropdowns Row -->
        <div class="hidden sm:grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 pt-2">
            
            <!-- Type (if not locked) -->
            <?php if (empty($lockType)): ?>
                <div>
                    <label for="filter_type" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Tipe Member</label>
                    <select id="filter_type" name="type" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500">
                        <option value="">Semua Tipe</option>
                        <option value="business" <?= (($f['type'] ?? '') === 'business') ? 'selected' : '' ?>>🏢 Vendor & Bisnis</option>
                        <option value="individual" <?= (($f['type'] ?? '') === 'individual') ? 'selected' : '' ?>>👤 Individu & Freelancer</option>
                    </select>
                </div>
            <?php else: ?>
                <input type="hidden" name="type" value="<?= esc($f['type'] ?? '') ?>">
            <?php endif; ?>

            <!-- Category (if not locked) -->
            <?php if (empty($lockCategory)): ?>
                <div>
                    <label for="filter_category" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Kategori</label>
                    <select id="filter_category" name="category" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500">
                        <option value="">Semua Kategori</option>
                        <?php foreach (($options['categories'] ?? []) as $c): ?>
                            <option value="<?= esc($c['slug']) ?>" <?= (($f['category'] ?? '') === $c['slug']) ? 'selected' : '' ?>>
                                <?= esc($c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php else: ?>
                <input type="hidden" name="category" value="<?= esc($f['category'] ?? '') ?>">
            <?php endif; ?>

            <!-- Specialization -->
            <div>
                <label for="filter_spec" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Spesialisasi</label>
                <select id="filter_spec" name="specialization" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <option value="">Semua Spesialisasi</option>
                    <?php foreach (($options['specializations'] ?? []) as $s): ?>
                        <option value="<?= esc($s['slug']) ?>" <?= (($f['specialization'] ?? '') === $s['slug']) ? 'selected' : '' ?>>
                            <?= esc($s['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Province -->
            <div>
                <label for="filter_province" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Provinsi</label>
                <select id="filter_province" name="province" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <option value="">Semua Provinsi</option>
                    <?php foreach (($options['provinces'] ?? []) as $prov): ?>
                        <option value="<?= esc($prov) ?>" <?= (($f['province'] ?? '') === $prov) ? 'selected' : '' ?>>
                            <?= esc($prov) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Sort By -->
            <div>
                <label for="filter_sort" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Urutan</label>
                <select id="filter_sort" name="sort" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <option value="newest" <?= (($f['sort'] ?? '') === 'newest') ? 'selected' : '' ?>>Terbaru Bergabung</option>
                    <option value="oldest" <?= (($f['sort'] ?? '') === 'oldest') ? 'selected' : '' ?>>Terlama Bergabung</option>
                    <option value="name_asc" <?= (($f['sort'] ?? '') === 'name_asc') ? 'selected' : '' ?>>Nama A - Z</option>
                    <option value="name_desc" <?= (($f['sort'] ?? '') === 'name_desc') ? 'selected' : '' ?>>Nama Z - A</option>
                </select>
            </div>
        </div>

        <!-- Active Filter Tags & Reset Link -->
        <?php if ($hasActiveFilters): ?>
            <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-100 text-xs">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-slate-500 font-medium">Filter aktif:</span>

                    <?php if (! empty($f['q'])): ?>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-brand-50 text-brand-700 font-bold border border-brand-200/80">
                            "<?= esc($f['q']) ?>"
                        </span>
                    <?php endif; ?>

                    <?php if (! empty($f['type']) && empty($lockType)): ?>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-bold border border-indigo-200/80">
                            <?= ($f['type'] === 'business') ? 'Vendor & Bisnis' : 'Individu & Freelancer' ?>
                        </span>
                    <?php endif; ?>

                    <?php if (! empty($f['category']) && empty($lockCategory)): ?>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-purple-50 text-purple-700 font-bold border border-purple-200/80">
                            Kategori: <?= esc($f['category']) ?>
                        </span>
                    <?php endif; ?>

                    <?php if (! empty($f['specialization'])): ?>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 font-bold border border-blue-200/80">
                            Keahlian: <?= esc($f['specialization']) ?>
                        </span>
                    <?php endif; ?>

                    <?php if (! empty($f['province'])): ?>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 font-bold border border-amber-200/80">
                            Prov: <?= esc($f['province']) ?>
                        </span>
                    <?php endif; ?>

                    <?php if (! empty($f['city'])): ?>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 font-bold border border-amber-200/80">
                            Kota: <?= esc($f['city']) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <a href="<?= esc($clearUrl) ?>" class="inline-flex items-center gap-1 font-bold text-rose-600 hover:text-rose-700 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span>Reset Semua Filter</span>
                </a>
            </div>
        <?php endif; ?>

    </form>
</div>

<!-- Mobile Filter Drawer Modal -->
<div id="mobileFilterDrawer" class="fixed inset-0 z-50 flex items-end sm:hidden bg-slate-900/60 backdrop-blur-xs hidden transition-opacity">
    <div class="bg-white w-full rounded-t-3xl max-h-[85vh] overflow-y-auto p-6 space-y-5 border-t border-slate-200">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-base font-extrabold text-slate-900">Filter Pencarian</h3>
            <button type="button" onclick="toggleMobileFilters()" class="p-1 text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="<?= esc($actionUrl) ?>" method="get" class="space-y-4">
            <input type="hidden" name="q" value="<?= esc($f['q'] ?? '') ?>">

            <?php if (empty($lockType)): ?>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tipe Member</label>
                    <select name="type" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold">
                        <option value="">Semua Tipe</option>
                        <option value="business" <?= (($f['type'] ?? '') === 'business') ? 'selected' : '' ?>>Vendor & Bisnis</option>
                        <option value="individual" <?= (($f['type'] ?? '') === 'individual') ? 'selected' : '' ?>>Individu & Freelancer</option>
                    </select>
                </div>
            <?php else: ?>
                <input type="hidden" name="type" value="<?= esc($f['type'] ?? '') ?>">
            <?php endif; ?>

            <?php if (empty($lockCategory)): ?>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Industri</label>
                    <select name="category" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold">
                        <option value="">Semua Kategori</option>
                        <?php foreach (($options['categories'] ?? []) as $c): ?>
                            <option value="<?= esc($c['slug']) ?>" <?= (($f['category'] ?? '') === $c['slug']) ? 'selected' : '' ?>>
                                <?= esc($c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php else: ?>
                <input type="hidden" name="category" value="<?= esc($f['category'] ?? '') ?>">
            <?php endif; ?>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Spesialisasi</label>
                <select name="specialization" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold">
                    <option value="">Semua Spesialisasi</option>
                    <?php foreach (($options['specializations'] ?? []) as $s): ?>
                        <option value="<?= esc($s['slug']) ?>" <?= (($f['specialization'] ?? '') === $s['slug']) ? 'selected' : '' ?>>
                            <?= esc($s['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Provinsi</label>
                <select name="province" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold">
                    <option value="">Semua Provinsi</option>
                    <?php foreach (($options['provinces'] ?? []) as $prov): ?>
                        <option value="<?= esc($prov) ?>" <?= (($f['province'] ?? '') === $prov) ? 'selected' : '' ?>>
                            <?= esc($prov) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Urutan</label>
                <select name="sort" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold">
                    <option value="newest" <?= (($f['sort'] ?? '') === 'newest') ? 'selected' : '' ?>>Terbaru</option>
                    <option value="oldest" <?= (($f['sort'] ?? '') === 'oldest') ? 'selected' : '' ?>>Terlama</option>
                    <option value="name_asc" <?= (($f['sort'] ?? '') === 'name_asc') ? 'selected' : '' ?>>A - Z</option>
                    <option value="name_desc" <?= (($f['sort'] ?? '') === 'name_desc') ? 'selected' : '' ?>>Z - A</option>
                </select>
            </div>

            <div class="flex items-center gap-3 pt-3">
                <a href="<?= esc($clearUrl) ?>" class="flex-1 py-3 text-center text-xs font-bold text-slate-700 bg-slate-100 rounded-xl">
                    Reset
                </a>
                <button type="submit" class="flex-1 py-3 text-center text-xs font-bold text-white bg-brand-600 rounded-xl shadow-xs">
                    Terapkan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleMobileFilters() {
    const drawer = document.getElementById('mobileFilterDrawer');
    if (drawer) {
        drawer.classList.toggle('hidden');
    }
}
</script>
