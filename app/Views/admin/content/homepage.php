<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Manajemen Konten Beranda</h2>
            <p class="text-sm text-slate-500 mt-1">Sesuaikan teks, gambar, poin keunggulan, statistik, dan visibilitas seksi halaman depan tanpa menyentuh kode program.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?= base_url() ?>" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Lihat Beranda
            </a>
        </div>
    </div>

    <!-- Main Content Form -->
    <form action="<?= base_url('admin/content/homepage/update') ?>" method="post" enctype="multipart/form-data" class="space-y-8">
        <?= csrf_field() ?>

        <!-- 1. SECTION VISIBILITY -->
        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Visibilitas Seksi Halaman Depan</h3>
                    <p class="text-xs text-slate-500">Centang seksi yang ingin ditampilkan di beranda publik</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <label class="flex items-center gap-3 p-3.5 rounded-xl border border-slate-200/80 hover:bg-slate-50 cursor-pointer transition-colors">
                    <input type="checkbox" name="section_hero_visible" value="1" <?= ($settings['Homepage.section_hero_visible'] ?? '1') === '1' ? 'checked' : '' ?> class="w-4 h-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500">
                    <span class="text-sm font-semibold text-slate-800">Seksi Hero</span>
                </label>

                <label class="flex items-center gap-3 p-3.5 rounded-xl border border-slate-200/80 hover:bg-slate-50 cursor-pointer transition-colors">
                    <input type="checkbox" name="section_about_visible" value="1" <?= ($settings['Homepage.section_about_visible'] ?? '1') === '1' ? 'checked' : '' ?> class="w-4 h-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500">
                    <span class="text-sm font-semibold text-slate-800">Tentang KOMEO</span>
                </label>

                <label class="flex items-center gap-3 p-3.5 rounded-xl border border-slate-200/80 hover:bg-slate-50 cursor-pointer transition-colors">
                    <input type="checkbox" name="section_benefits_visible" value="1" <?= ($settings['Homepage.section_benefits_visible'] ?? '1') === '1' ? 'checked' : '' ?> class="w-4 h-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500">
                    <span class="text-sm font-semibold text-slate-800">Keunggulan Komunitas</span>
                </label>

                <label class="flex items-center gap-3 p-3.5 rounded-xl border border-slate-200/80 hover:bg-slate-50 cursor-pointer transition-colors">
                    <input type="checkbox" name="section_categories_visible" value="1" <?= ($settings['Homepage.section_categories_visible'] ?? '1') === '1' ? 'checked' : '' ?> class="w-4 h-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500">
                    <span class="text-sm font-semibold text-slate-800">Kategori Member</span>
                </label>

                <label class="flex items-center gap-3 p-3.5 rounded-xl border border-slate-200/80 hover:bg-slate-50 cursor-pointer transition-colors">
                    <input type="checkbox" name="section_statistics_visible" value="1" <?= ($settings['Homepage.section_statistics_visible'] ?? '1') === '1' ? 'checked' : '' ?> class="w-4 h-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500">
                    <span class="text-sm font-semibold text-slate-800">Statistik Komunitas</span>
                </label>

                <label class="flex items-center gap-3 p-3.5 rounded-xl border border-slate-200/80 hover:bg-slate-50 cursor-pointer transition-colors">
                    <input type="checkbox" name="section_members_visible" value="1" <?= ($settings['Homepage.section_members_visible'] ?? '1') === '1' ? 'checked' : '' ?> class="w-4 h-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500">
                    <span class="text-sm font-semibold text-slate-800">Preview Anggota Baru</span>
                </label>

                <label class="flex items-center gap-3 p-3.5 rounded-xl border border-slate-200/80 hover:bg-slate-50 cursor-pointer transition-colors">
                    <input type="checkbox" name="section_cta_visible" value="1" <?= ($settings['Homepage.section_cta_visible'] ?? '1') === '1' ? 'checked' : '' ?> class="w-4 h-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500">
                    <span class="text-sm font-semibold text-slate-800">Ajakan Bergabung (CTA)</span>
                </label>
            </div>
        </div>

        <!-- 2. HERO SECTION SETTINGS -->
        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Hero Section (Banner Utama)</h3>
                    <p class="text-xs text-slate-500">Headline utama, deskripsi penarik, dan tombol Call-To-Action teratas</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Badge Teks</label>
                    <input type="text" name="hero_badge" value="<?= esc($settings['Homepage.hero_badge'] ?? 'Komunitas Resmi EO & Ekosistem Event') ?>"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Headline Baris 1 <span class="text-red-500">*</span></label>
                    <input type="text" name="hero_headline" required value="<?= esc($settings['Homepage.hero_headline'] ?? 'Satu Komunitas,') ?>"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 font-bold">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Headline Highlight (Teks Berwarna) <span class="text-red-500">*</span></label>
                    <input type="text" name="hero_headline_highlight" required value="<?= esc($settings['Homepage.hero_headline_highlight'] ?? 'Ribuan Peluang Kolaborasi.') ?>"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 font-bold text-brand-600">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Deskripsi Hero <span class="text-red-500">*</span></label>
                    <textarea name="hero_description" rows="3" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500"><?= esc($settings['Homepage.hero_description'] ?? 'Wadah profesional penghubung Event Organizer, Wedding Organizer, Vendor Audio, Lighting, Multimedia, Talent, hingga F&B di seluruh Indonesia.') ?></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Label Tombol Utama <span class="text-red-500">*</span></label>
                    <input type="text" name="hero_btn_primary_label" required value="<?= esc($settings['Homepage.hero_btn_primary_label'] ?? 'Gabung Sekarang') ?>"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">URL / Target Tombol Utama <span class="text-red-500">*</span></label>
                    <input type="text" name="hero_btn_primary_url" required value="<?= esc($settings['Homepage.hero_btn_primary_url'] ?? 'register') ?>"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Label Tombol Kedua</label>
                    <input type="text" name="hero_btn_secondary_label" value="<?= esc($settings['Homepage.hero_btn_secondary_label'] ?? 'Jelajahi Anggota') ?>"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">URL / Target Tombol Kedua</label>
                    <input type="text" name="hero_btn_secondary_url" value="<?= esc($settings['Homepage.hero_btn_secondary_url'] ?? '#members') ?>"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Gambar Background Hero (Opsional)</label>
                    <div class="flex items-center gap-4">
                        <?php if (!empty($settings['Homepage.hero_background_image'])): ?>
                            <div class="w-24 h-16 rounded-xl overflow-hidden border border-slate-200 bg-slate-100 shrink-0">
                                <img src="<?= base_url($settings['Homepage.hero_background_image']) ?>" class="w-full h-full object-cover" alt="Hero BG">
                            </div>
                        <?php endif; ?>
                        <div class="grow">
                            <input type="file" name="hero_background_image" accept="image/png,image/jpeg,image/webp"
                                   class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                            <p class="text-xs text-slate-400 mt-1">Format: PNG, JPG, WEBP. Maks 3MB.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. ABOUT SECTION SETTINGS -->
        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Seksi Tentang KOMEO</h3>
                    <p class="text-xs text-slate-500">Penjelasan identitas, latar belakang, dan visi misi komunitas</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Badge Seksi</label>
                    <input type="text" name="about_badge" value="<?= esc($settings['Homepage.about_badge'] ?? 'Tentang KOMEO.ID') ?>"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Judul Utama <span class="text-red-500">*</span></label>
                    <input type="text" name="about_title" required value="<?= esc($settings['Homepage.about_title'] ?? 'Membangun Ekosistem Event Nusantara yang Berdaya Saing') ?>"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 font-bold">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Subjudul</label>
                    <input type="text" name="about_subtitle" value="<?= esc($settings['Homepage.about_subtitle'] ?? 'KOMUNITAS EVENT ORGANIZER & INDUSTRI KREATIF INDONESIA') ?>"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Deskripsi Lengkap <span class="text-red-500">*</span></label>
                    <textarea name="about_description" rows="5" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500"><?= esc($settings['Homepage.about_description'] ?? "KOMEO hadir sebagai ruang kolaborasi strategis bagi para pelaku industri event di Indonesia.\n\nKami percaya bahwa kesuksesan sebuah perhelatan lahir dari sinergi vendor yang kredibel, terpercaya, dan berstandar profesional.") ?></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Gambar Ilustrasi Seksi Tentang (Opsional)</label>
                    <div class="flex items-center gap-4">
                        <?php if (!empty($settings['Homepage.about_image'])): ?>
                            <div class="w-24 h-16 rounded-xl overflow-hidden border border-slate-200 bg-slate-100 shrink-0">
                                <img src="<?= base_url($settings['Homepage.about_image']) ?>" class="w-full h-full object-cover" alt="About Image">
                            </div>
                        <?php endif; ?>
                        <div class="grow">
                            <input type="file" name="about_image" accept="image/png,image/jpeg,image/webp"
                                   class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                            <p class="text-xs text-slate-400 mt-1">Format: PNG, JPG, WEBP. Maks 3MB.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. BENEFITS HEADER & STATS -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Benefits Header -->
            <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">Header Seksi Keunggulan</h3>
                            <p class="text-xs text-slate-500">Teks pembuka untuk bagian keunggulan bergabung</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Badge Seksi</label>
                            <input type="text" name="benefits_badge" value="<?= esc($settings['Homepage.benefits_badge'] ?? 'Mengapa Bergabung?') ?>"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Judul Keunggulan</label>
                            <input type="text" name="benefits_title" value="<?= esc($settings['Homepage.benefits_title'] ?? 'Keuntungan Menjadi Bagian dari KOMEO') ?>"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Deskripsi Singkat</label>
                            <textarea name="benefits_description" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500"><?= esc($settings['Homepage.benefits_description'] ?? 'Bergabung dengan ratusan pelaku industri event ternama dan rasakan dampak nyata bagi akselerasi bisnis Anda.') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Configuration -->
            <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">Statistik Komunitas</h3>
                            <p class="text-xs text-slate-500">Gunakan angka database asli atau sesuaikan metrik teks</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="p-3.5 bg-slate-50 rounded-xl space-y-2 border border-slate-200/70">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="stats_use_real_members" value="1" <?= ($settings['Homepage.stats_use_real_members'] ?? '1') === '1' ? 'checked' : '' ?> class="w-4 h-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500">
                                <span class="text-xs font-bold text-slate-800">Tampilkan Jumlah Member Asli dari Database</span>
                            </label>
                            <p class="text-2xs text-slate-500 ml-7">Menghitung total member terdaftar berstatus aktif di database.</p>
                        </div>

                        <div class="p-3.5 bg-slate-50 rounded-xl space-y-2 border border-slate-200/70">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="stats_use_real_cities" value="1" <?= ($settings['Homepage.stats_use_real_cities'] ?? '1') === '1' ? 'checked' : '' ?> class="w-4 h-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500">
                                <span class="text-xs font-bold text-slate-800">Tampilkan Jumlah Kota Asli dari Database</span>
                            </label>
                            <p class="text-2xs text-slate-500 ml-7">Menghitung sebaran kota unik domisili para member.</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3 pt-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Metrik Kustom 1 (Nilai)</label>
                                <input type="text" name="stats_custom_metric1_number" value="<?= esc($settings['Homepage.stats_custom_metric1_number'] ?? '100%') ?>"
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Metrik Kustom 1 (Label)</label>
                                <input type="text" name="stats_custom_metric1_label" value="<?= esc($settings['Homepage.stats_custom_metric1_label'] ?? 'Vendor Terverifikasi') ?>"
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Metrik Kustom 2 (Nilai)</label>
                                <input type="text" name="stats_custom_metric2_number" value="<?= esc($settings['Homepage.stats_custom_metric2_number'] ?? '24/7') ?>"
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Metrik Kustom 2 (Label)</label>
                                <input type="text" name="stats_custom_metric2_label" value="<?= esc($settings['Homepage.stats_custom_metric2_label'] ?? 'Peluang Kolaborasi') ?>"
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Save Button -->
        <div class="sticky bottom-6 flex justify-end z-10">
            <button type="submit" class="px-8 py-3.5 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-2xl shadow-lg shadow-brand-600/30 transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Perubahan Beranda
            </button>
        </div>
    </form>

    <!-- 5. COMMUNITY BENEFITS ITEMS (NORMALIZED TABLE) -->
    <div id="benefits-section" class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs mt-10">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 mb-6 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Kelola Poin Keunggulan (Community Benefits)</h3>
                    <p class="text-xs text-slate-500">Daftar item keunggulan yang tampil pada seksi "Mengapa Bergabung"</p>
                </div>
            </div>
            <div>
                <button type="button" onclick="openBenefitModal()" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-xl transition-colors inline-flex items-center gap-2 shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Keunggulan
                </button>
            </div>
        </div>

        <!-- Table of Benefits -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 text-xs font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-4 w-12 text-center">Urutan</th>
                        <th class="py-3 px-4 w-28">Ikon</th>
                        <th class="py-3 px-4">Judul Keunggulan</th>
                        <th class="py-3 px-4">Deskripsi</th>
                        <th class="py-3 px-4 w-24 text-center">Status</th>
                        <th class="py-3 px-4 w-32 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($benefits)): ?>
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 text-xs">Belum ada poin keunggulan ditambahkan.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($benefits as $b): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-3.5 px-4 text-center font-bold text-slate-700"><?= $b['sort_order'] ?></td>
                                <td class="py-3.5 px-4">
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-medium">
                                        <span><?= esc($b['icon']) ?></span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-900"><?= esc($b['title']) ?></td>
                                <td class="py-3.5 px-4 text-xs text-slate-500 max-w-md"><?= esc($b['description']) ?></td>
                                <td class="py-3.5 px-4 text-center">
                                    <?php if ($b['is_active']): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-2xs font-bold bg-emerald-100 text-emerald-700">Aktif</span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-2xs font-bold bg-slate-100 text-slate-500">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button"
                                                onclick='editBenefit(<?= json_encode($b) ?>)'
                                                class="p-1.5 text-slate-400 hover:text-brand-600 rounded-lg hover:bg-brand-50 transition-colors"
                                                title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <form action="<?= base_url('admin/content/homepage/benefit/delete/' . $b['id']) ?>" method="post"
                                              data-komeo-confirm-form="Apakah Anda yakin ingin menghapus keunggulan ini?"
                                              data-komeo-confirm-title="Hapus Keunggulan Komunitas"
                                              data-komeo-confirm-type="danger">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Benefit Modal -->
<div id="benefitModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 sm:p-8 shadow-2xl relative animate-in fade-in zoom-in duration-200">
        <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
            <h3 id="modalTitle" class="text-lg font-extrabold text-slate-900">Tambah Poin Keunggulan</h3>
            <button type="button" onclick="closeBenefitModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="<?= base_url('admin/content/homepage/benefit/save') ?>" method="post" class="space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="benefitId" value="">

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Judul Keunggulan <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="benefitTitle" required placeholder="Contoh: Jaringan Terluas Lintas Kota"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilihan Ikon <span class="text-red-500">*</span></label>
                <select name="icon" id="benefitIcon" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 bg-white">
                    <?php foreach ($availableIcons as $key => $lbl): ?>
                        <option value="<?= esc($key) ?>"><?= esc($lbl) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Deskripsi Lengkap <span class="text-red-500">*</span></label>
                <textarea name="description" id="benefitDescription" rows="3" required placeholder="Jelaskan manfaat keunggulan ini bagi anggota..."
                          class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Urutan Tampil</label>
                    <input type="number" name="sort_order" id="benefitSortOrder" value="1" min="1" max="999" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500">
                </div>
                <div class="flex items-end pb-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" id="benefitIsActive" value="1" checked class="w-4 h-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500">
                        <span class="text-xs font-bold text-slate-700">Aktif & Tampilkan</span>
                    </label>
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeBenefitModal()" class="px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-xl transition-colors shadow-xs">
                    Simpan Keunggulan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openBenefitModal() {
    document.getElementById('modalTitle').textContent = 'Tambah Poin Keunggulan';
    document.getElementById('benefitId').value = '';
    document.getElementById('benefitTitle').value = '';
    document.getElementById('benefitDescription').value = '';
    document.getElementById('benefitSortOrder').value = '<?= count($benefits) + 1 ?>';
    document.getElementById('benefitIsActive').checked = true;
    document.getElementById('benefitModal').classList.remove('hidden');
}

function editBenefit(item) {
    document.getElementById('modalTitle').textContent = 'Edit Poin Keunggulan';
    document.getElementById('benefitId').value = item.id;
    document.getElementById('benefitTitle').value = item.title;
    document.getElementById('benefitDescription').value = item.description;
    document.getElementById('benefitIcon').value = item.icon;
    document.getElementById('benefitSortOrder').value = item.sort_order;
    document.getElementById('benefitIsActive').checked = (item.is_active == 1);
    document.getElementById('benefitModal').classList.remove('hidden');
}

function closeBenefitModal() {
    document.getElementById('benefitModal').classList.add('hidden');
}
</script>

<?= $this->endSection() ?>
