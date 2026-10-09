<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Pengaturan Website</h2>
            <p class="text-sm text-slate-500 mt-1">Kelola identitas, visual, branding, kontak, dan SEO KOMEO.ID</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?= base_url() ?>" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Lihat Website
            </a>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-2 shadow-xs">
        <div class="flex flex-wrap gap-1.5 sm:gap-2">
            <a href="<?= base_url('admin/settings?tab=identity') ?>"
               class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all inline-flex items-center gap-2 <?= $activeTab === 'identity' ? 'bg-brand-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' ?>">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Identitas
            </a>
            <a href="<?= base_url('admin/settings?tab=logo') ?>"
               class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all inline-flex items-center gap-2 <?= $activeTab === 'logo' ? 'bg-brand-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' ?>">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Logo & Favicon
            </a>
            <a href="<?= base_url('admin/settings?tab=appearance') ?>"
               class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all inline-flex items-center gap-2 <?= $activeTab === 'appearance' ? 'bg-brand-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' ?>">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                Warna & Tampilan
            </a>
            <a href="<?= base_url('admin/settings?tab=social') ?>"
               class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all inline-flex items-center gap-2 <?= $activeTab === 'social' ? 'bg-brand-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' ?>">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                Media Sosial
            </a>
            <a href="<?= base_url('admin/settings?tab=contact') ?>"
               class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all inline-flex items-center gap-2 <?= $activeTab === 'contact' ? 'bg-brand-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' ?>">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Kontak
            </a>
            <a href="<?= base_url('admin/settings?tab=seo') ?>"
               class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all inline-flex items-center gap-2 <?= $activeTab === 'seo' ? 'bg-brand-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' ?>">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                SEO & Metadata
            </a>
            <a href="<?= base_url('admin/settings?tab=footer') ?>"
               class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all inline-flex items-center gap-2 <?= $activeTab === 'footer' ? 'bg-brand-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' ?>">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                Footer Website
            </a>
        </div>
    </div>

    <!-- Tab 1: Identitas Website -->
    <?php if ($activeTab === 'identity'): ?>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Form Card -->
            <div class="lg:col-span-8 bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs">
                <form action="<?= base_url('admin/settings/update/identity') ?>" method="POST" class="space-y-5">
                    <?= csrf_field() ?>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="site_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Nama Website <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="site_name" name="site_name" required
                                   value="<?= esc(old('site_name', $settings['App.site_name'])) ?>"
                                   class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        </div>

                        <div>
                            <label for="site_subtitle" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Subtitle Website
                            </label>
                            <input type="text" id="site_subtitle" name="site_subtitle"
                                   value="<?= esc(old('site_subtitle', $settings['App.site_subtitle'])) ?>"
                                   class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label for="site_tagline" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Tagline Utama <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="site_tagline" name="site_tagline" required
                               value="<?= esc(old('site_tagline', $settings['App.site_tagline'])) ?>"
                               class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        <p class="text-[11px] text-slate-400 mt-1">Tagline tampil di header, hero beranda, dan materi promosi.</p>
                    </div>

                    <div>
                        <label for="site_description" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Deskripsi Singkat Website
                        </label>
                        <textarea id="site_description" name="site_description" rows="3"
                                  class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none"><?= esc(old('site_description', $settings['App.site_description'])) ?></textarea>
                    </div>

                    <div>
                        <label for="footer_description" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Deskripsi Footer
                        </label>
                        <textarea id="footer_description" name="footer_description" rows="3"
                                  class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none"><?= esc(old('footer_description', $settings['App.footer_description'])) ?></textarea>
                    </div>

                    <div>
                        <label for="copyright_text" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Teks Hak Cipta (Copyright) <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="copyright_text" name="copyright_text" required
                               value="<?= esc(old('copyright_text', $settings['App.copyright_text'])) ?>"
                               class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        <p class="text-[11px] text-slate-400 mt-1">Tahun akan digenerate otomatis di depan teks ini.</p>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-sm text-white bg-brand-600 hover:bg-brand-700 shadow-md shadow-brand-500/20 transition-all">
                            Simpan Identitas
                        </button>
                    </div>
                </form>
            </div>

            <!-- Preview Card -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Pratinjau Identitas</h3>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-2">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tampilan Header</p>
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-lg bg-brand-600 text-white font-extrabold flex items-center justify-center text-sm">K</span>
                            <div>
                                <span class="text-sm font-extrabold text-slate-900"><?= esc($settings['App.site_name']) ?></span>
                                <span class="block text-[9px] uppercase tracking-wider text-slate-400 font-semibold"><?= esc($settings['App.site_subtitle']) ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-2">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tagline & Footer</p>
                        <p class="text-xs font-bold text-slate-800 italic">"<?= esc($settings['App.site_tagline']) ?>"</p>
                        <p class="text-xs text-slate-500 leading-relaxed"><?= esc($settings['App.footer_description']) ?></p>
                        <p class="text-[10px] text-slate-400 pt-2 border-t border-slate-200">&copy; <?= date('Y') ?> <?= esc($settings['App.copyright_text']) ?></p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Tab 2: Logo & Favicon -->
    <?php if ($activeTab === 'logo'): ?>
        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs space-y-8">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Manajemen Logo & Favicon</h3>
                <p class="text-xs text-slate-500 mt-0.5">Unggah logo resmi dan favicon situs (format PNG, JPG, WEBP, ICO)</p>
            </div>

            <form action="<?= base_url('admin/settings/update/logo') ?>" method="POST" enctype="multipart/form-data" class="space-y-8">
                <?= csrf_field() ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- 1. Header Logo -->
                    <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-bold text-slate-900">Logo Header</h4>
                            <span class="text-[10px] bg-slate-200 text-slate-700 px-2 py-0.5 rounded font-semibold">Max 2MB</span>
                        </div>

                        <div class="h-28 rounded-xl bg-white border border-slate-200/80 flex items-center justify-center p-3 relative overflow-hidden">
                            <?php if (! empty($settings['App.logo_header']) && file_exists(FCPATH . $settings['App.logo_header'])): ?>
                                <img src="<?= base_url($settings['App.logo_header']) ?>" alt="Header Logo" class="max-h-full max-w-full object-contain">
                            <?php else: ?>
                                <div class="flex items-center gap-2 text-slate-400">
                                    <span class="w-9 h-9 rounded-xl bg-brand-600 text-white font-bold flex items-center justify-center text-base">K</span>
                                    <span class="text-sm font-bold text-slate-700"><?= esc($settings['App.site_name']) ?> (Fallback Emblem)</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div>
                            <input type="file" name="logo_header" accept=".png,.jpg,.jpeg,.webp"
                                   class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 cursor-pointer">
                            <p class="text-[10px] text-slate-400 mt-1">Disarankan PNG transparan atau WEBP, rasio horizontal.</p>
                        </div>

                        <?php if (! empty($settings['App.logo_header'])): ?>
                            <button type="submit" formaction="<?= base_url('admin/settings/delete-image/header') ?>" formmethod="POST"
                                    class="text-xs text-rose-600 hover:text-rose-700 font-semibold inline-flex items-center gap-1"
                                    data-komeo-confirm="Apakah Anda yakin ingin menghapus logo header ini dan menggunakan emblem standar?"
                                    data-komeo-confirm-title="Hapus Logo Header"
                                    data-komeo-confirm-type="danger">
                                &times; Hapus & Gunakan Emblem Standar
                            </button>
                        <?php endif; ?>
                    </div>

                    <!-- 2. Footer Logo -->
                    <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-bold text-slate-900">Logo Footer</h4>
                            <span class="text-[10px] bg-slate-200 text-slate-700 px-2 py-0.5 rounded font-semibold">Max 2MB</span>
                        </div>

                        <div class="h-28 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center p-3 relative overflow-hidden">
                            <?php if (! empty($settings['App.logo_footer']) && file_exists(FCPATH . $settings['App.logo_footer'])): ?>
                                <img src="<?= base_url($settings['App.logo_footer']) ?>" alt="Footer Logo" class="max-h-full max-w-full object-contain">
                            <?php else: ?>
                                <div class="flex items-center gap-2 text-white">
                                    <span class="w-9 h-9 rounded-xl bg-brand-600 text-white font-bold flex items-center justify-center text-base">K</span>
                                    <span class="text-sm font-bold"><?= esc($settings['App.site_name']) ?> (Fallback Emblem)</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div>
                            <input type="file" name="logo_footer" accept=".png,.jpg,.jpeg,.webp"
                                   class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 cursor-pointer">
                            <p class="text-[10px] text-slate-400 mt-1">Disarankan versi terang/putih untuk kontras latar gelap footer.</p>
                        </div>

                        <?php if (! empty($settings['App.logo_footer'])): ?>
                            <button type="submit" formaction="<?= base_url('admin/settings/delete-image/footer') ?>" formmethod="POST"
                                    class="text-xs text-rose-600 hover:text-rose-700 font-semibold inline-flex items-center gap-1"
                                    data-komeo-confirm="Apakah Anda yakin ingin menghapus logo footer ini dan menggunakan emblem standar?"
                                    data-komeo-confirm-title="Hapus Logo Footer"
                                    data-komeo-confirm-type="danger">
                                &times; Hapus & Gunakan Emblem Standar
                            </button>
                        <?php endif; ?>
                    </div>

                    <!-- 3. Favicon -->
                    <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-bold text-slate-900">Favicon Browser</h4>
                            <span class="text-[10px] bg-slate-200 text-slate-700 px-2 py-0.5 rounded font-semibold">Max 1MB</span>
                        </div>

                        <div class="h-28 rounded-xl bg-white border border-slate-200/80 flex items-center justify-center p-3 relative">
                            <div class="flex items-center gap-3 p-2 px-4 rounded-lg bg-slate-100 border border-slate-200 shadow-xs">
                                <?php if (! empty($settings['App.favicon']) && file_exists(FCPATH . $settings['App.favicon'])): ?>
                                    <img src="<?= base_url($settings['App.favicon']) ?>" alt="Favicon" class="w-6 h-6 object-contain">
                                <?php else: ?>
                                    <span class="w-6 h-6 rounded bg-brand-600 text-white font-bold text-xs flex items-center justify-center">K</span>
                                <?php endif; ?>
                                <span class="text-xs font-semibold text-slate-700"><?= esc($settings['App.site_name']) ?> - Tab Browser</span>
                            </div>
                        </div>

                        <div>
                            <input type="file" name="favicon" accept=".png,.ico"
                                   class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 cursor-pointer">
                            <p class="text-[10px] text-slate-400 mt-1">Ukuran ideal: 32x32 atau 64x64 pixel (.ico atau .png).</p>
                        </div>

                        <?php if (! empty($settings['App.favicon'])): ?>
                            <button type="submit" formaction="<?= base_url('admin/settings/delete-image/favicon') ?>" formmethod="POST"
                                    class="text-xs text-rose-600 hover:text-rose-700 font-semibold inline-flex items-center gap-1"
                                    data-komeo-confirm="Apakah Anda yakin ingin menghapus favicon kustom ini?"
                                    data-komeo-confirm-title="Hapus Favicon"
                                    data-komeo-confirm-type="danger">
                                &times; Hapus Favicon Kustom
                            </button>
                        <?php endif; ?>
                    </div>

                    <!-- 4. Open Graph Image -->
                    <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-bold text-slate-900">Gambar Social Share (OG Image)</h4>
                            <span class="text-[10px] bg-slate-200 text-slate-700 px-2 py-0.5 rounded font-semibold">Max 3MB</span>
                        </div>

                        <div class="h-28 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center p-2 relative overflow-hidden">
                            <?php if (! empty($settings['App.og_image']) && file_exists(FCPATH . $settings['App.og_image'])): ?>
                                <img src="<?= base_url($settings['App.og_image']) ?>" alt="OG Image" class="max-h-full max-w-full object-cover rounded">
                            <?php else: ?>
                                <div class="text-center text-slate-400 text-xs">
                                    <svg class="w-8 h-8 mx-auto mb-1 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span>Belum ada gambar share sosial</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div>
                            <input type="file" name="og_image" accept=".png,.jpg,.jpeg,.webp"
                                   class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 cursor-pointer">
                            <p class="text-[10px] text-slate-400 mt-1">Ukuran rekomendasi: 1200x630 pixel untuk WhatsApp, FB, X, LinkedIn.</p>
                        </div>

                        <?php if (! empty($settings['App.og_image'])): ?>
                            <button type="submit" formaction="<?= base_url('admin/settings/delete-image/og') ?>" formmethod="POST"
                                    class="text-xs text-rose-600 hover:text-rose-700 font-semibold inline-flex items-center gap-1"
                                    data-komeo-confirm="Apakah Anda yakin ingin menghapus gambar pratinjau media sosial (OG Image) ini?"
                                    data-komeo-confirm-title="Hapus Gambar Share"
                                    data-komeo-confirm-type="danger">
                                &times; Hapus Gambar Share
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-sm text-white bg-brand-600 hover:bg-brand-700 shadow-md shadow-brand-500/20 transition-all">
                        Unggah & Simpan Berkas
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- Tab 3: Warna & Tampilan -->
    <?php if ($activeTab === 'appearance'): ?>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-8 bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Warna Brand Dinamis</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Atur skema warna global menggunakan CSS custom properties tanpa merusak Tailwind CSS</p>
                </div>

                <form action="<?= base_url('admin/settings/update/appearance') ?>" method="POST" class="space-y-5">
                    <?= csrf_field() ?>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- Primary Color -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Warna Utama (Primary)
                            </label>
                            <div class="flex items-center gap-3">
                                <input type="color" id="picker_primary" value="<?= esc($settings['App.color_primary']) ?>"
                                       oninput="document.getElementById('color_primary').value = this.value; updatePreview();"
                                       class="w-11 h-11 rounded-xl border border-slate-200 cursor-pointer p-0.5">
                                <input type="text" id="color_primary" name="color_primary" required
                                       value="<?= esc(old('color_primary', $settings['App.color_primary'])) ?>"
                                       oninput="document.getElementById('picker_primary').value = this.value; updatePreview();"
                                       placeholder="#4F46E5"
                                       class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm font-mono focus:ring-2 focus:ring-brand-500 focus:outline-none">
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Digunakan untuk tombol utama, badge aktif, aksen header.</p>
                        </div>

                        <!-- Secondary Color -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Warna Sekunder (Secondary)
                            </label>
                            <div class="flex items-center gap-3">
                                <input type="color" id="picker_secondary" value="<?= esc($settings['App.color_secondary']) ?>"
                                       oninput="document.getElementById('color_secondary').value = this.value; updatePreview();"
                                       class="w-11 h-11 rounded-xl border border-slate-200 cursor-pointer p-0.5">
                                <input type="text" id="color_secondary" name="color_secondary" required
                                       value="<?= esc(old('color_secondary', $settings['App.color_secondary'])) ?>"
                                       oninput="document.getElementById('picker_secondary').value = this.value; updatePreview();"
                                       placeholder="#4338CA"
                                       class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm font-mono focus:ring-2 focus:ring-brand-500 focus:outline-none">
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Digunakan untuk efek hover tombol dan gradien pendukung.</p>
                        </div>

                        <!-- Accent Color -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Warna Aksen (Accent)
                            </label>
                            <div class="flex items-center gap-3">
                                <input type="color" id="picker_accent" value="<?= esc($settings['App.color_accent']) ?>"
                                       oninput="document.getElementById('color_accent').value = this.value; updatePreview();"
                                       class="w-11 h-11 rounded-xl border border-slate-200 cursor-pointer p-0.5">
                                <input type="text" id="color_accent" name="color_accent" required
                                       value="<?= esc(old('color_accent', $settings['App.color_accent'])) ?>"
                                       oninput="document.getElementById('picker_accent').value = this.value; updatePreview();"
                                       placeholder="#818CF8"
                                       class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm font-mono focus:ring-2 focus:ring-brand-500 focus:outline-none">
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Untuk highlight teks dan variasi gradasi visual.</p>
                        </div>

                        <!-- Background Color -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Warna Latar (Background)
                            </label>
                            <div class="flex items-center gap-3">
                                <input type="color" id="picker_background" value="<?= esc($settings['App.color_background']) ?>"
                                       oninput="document.getElementById('color_background').value = this.value; updatePreview();"
                                       class="w-11 h-11 rounded-xl border border-slate-200 cursor-pointer p-0.5">
                                <input type="text" id="color_background" name="color_background" required
                                       value="<?= esc(old('color_background', $settings['App.color_background'])) ?>"
                                       oninput="document.getElementById('picker_background').value = this.value; updatePreview();"
                                       placeholder="#FFFFFF"
                                       class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm font-mono focus:ring-2 focus:ring-brand-500 focus:outline-none">
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Warna dasar kanvas halaman (default: #FFFFFF).</p>
                        </div>

                        <!-- Text Color -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Warna Tipografi (Text)
                            </label>
                            <div class="flex items-center gap-3 max-w-sm">
                                <input type="color" id="picker_text" value="<?= esc($settings['App.color_text']) ?>"
                                       oninput="document.getElementById('color_text').value = this.value; updatePreview();"
                                       class="w-11 h-11 rounded-xl border border-slate-200 cursor-pointer p-0.5">
                                <input type="text" id="color_text" name="color_text" required
                                       value="<?= esc(old('color_text', $settings['App.color_text'])) ?>"
                                       oninput="document.getElementById('picker_text').value = this.value; updatePreview();"
                                       placeholder="#0F172A"
                                       class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm font-mono focus:ring-2 focus:ring-brand-500 focus:outline-none">
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <button type="submit" formaction="<?= base_url('admin/settings/reset-colors') ?>" formmethod="POST"
                                data-komeo-confirm="Kembalikan seluruh konfigurasi warna tema ke standar awal KOMEO.ID?"
                                data-komeo-confirm-title="Reset Warna Standar"
                                data-komeo-confirm-type="warning"
                                data-komeo-confirm-btn="Ya, Reset Warna"
                                class="px-4 py-2.5 rounded-xl font-bold text-xs text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">
                            Reset ke Warna Standar
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-sm text-white bg-brand-600 hover:bg-brand-700 shadow-md shadow-brand-500/20 transition-all">
                            Simpan Perubahan Warna
                        </button>
                    </div>
                </form>
            </div>

            <!-- Live Color Preview Box -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Pratinjau Komponen Visual</h3>

                    <div id="preview_box" class="p-5 rounded-2xl border border-slate-200 space-y-4 transition-all">
                        <div class="flex items-center justify-between">
                            <span id="preview_badge" class="px-2.5 py-1 rounded-full text-xs font-bold text-white transition-colors" style="background-color: <?= esc($settings['App.color_primary']) ?>">
                                Badge Aksen
                            </span>
                            <span class="text-xs font-medium text-slate-400">Preview UI</span>
                        </div>
                        <h4 id="preview_heading" class="text-base font-extrabold transition-colors" style="color: <?= esc($settings['App.color_text']) ?>">
                            Satu Komunitas, Ribuan Peluang Kolaborasi
                        </h4>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Simulasi tampilan tombol dan tipografi dengan skema warna yang dipilih.
                        </p>
                        <div class="pt-2 flex gap-2">
                            <button id="preview_btn" type="button" class="px-4 py-2 rounded-xl text-xs font-bold text-white transition-colors" style="background-color: <?= esc($settings['App.color_primary']) ?>">
                                Tombol Utama
                            </button>
                            <button id="preview_btn_sec" type="button" class="px-4 py-2 rounded-xl text-xs font-bold text-white transition-colors" style="background-color: <?= esc($settings['App.color_secondary']) ?>">
                                Tombol Hover
                            </button>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-500 space-y-1">
                        <p class="font-bold text-slate-700">CSS Custom Properties Terpasang:</p>
                        <code class="block text-[11px] text-slate-600 bg-white p-2 rounded border border-slate-200">
                            --brand-primary<br>
                            --brand-secondary<br>
                            --brand-accent
                        </code>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function updatePreview() {
                const primary = document.getElementById('color_primary').value;
                const secondary = document.getElementById('color_secondary').value;
                const text = document.getElementById('color_text').value;

                const badge = document.getElementById('preview_badge');
                const heading = document.getElementById('preview_heading');
                const btn = document.getElementById('preview_btn');
                const btnSec = document.getElementById('preview_btn_sec');

                if (badge) badge.style.backgroundColor = primary;
                if (btn) btn.style.backgroundColor = primary;
                if (btnSec) btnSec.style.backgroundColor = secondary;
                if (heading) heading.style.color = text;
            }
        </script>
    <?php endif; ?>

    <!-- Tab 4: Media Sosial -->
    <?php if ($activeTab === 'social'): ?>
        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs space-y-6">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Media Sosial Komunitas</h3>
                <p class="text-xs text-slate-500 mt-0.5">Ikon hanya akan muncul di website jika tautan diisi (kosongkan jika belum tersedia)</p>
            </div>

            <form action="<?= base_url('admin/settings/update/social') ?>" method="POST" class="space-y-5">
                <?= csrf_field() ?>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="social_instagram" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Instagram URL
                        </label>
                        <input type="url" id="social_instagram" name="social_instagram"
                               value="<?= esc(old('social_instagram', $settings['App.social_instagram'])) ?>"
                               placeholder="https://instagram.com/komeo.id"
                               class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="social_linkedin" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            LinkedIn URL
                        </label>
                        <input type="url" id="social_linkedin" name="social_linkedin"
                               value="<?= esc(old('social_linkedin', $settings['App.social_linkedin'])) ?>"
                               placeholder="https://linkedin.com/company/komeo-id"
                               class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="social_tiktok" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            TikTok URL
                        </label>
                        <input type="url" id="social_tiktok" name="social_tiktok"
                               value="<?= esc(old('social_tiktok', $settings['App.social_tiktok'])) ?>"
                               placeholder="https://tiktok.com/@komeo.id"
                               class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="social_youtube" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            YouTube Channel URL
                        </label>
                        <input type="url" id="social_youtube" name="social_youtube"
                               value="<?= esc(old('social_youtube', $settings['App.social_youtube'])) ?>"
                               placeholder="https://youtube.com/@komeoid"
                               class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="social_facebook" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Facebook Page URL
                        </label>
                        <input type="url" id="social_facebook" name="social_facebook"
                               value="<?= esc(old('social_facebook', $settings['App.social_facebook'])) ?>"
                               placeholder="https://facebook.com/komeoid"
                               class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="social_whatsapp" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Tautan Langsung WhatsApp
                        </label>
                        <input type="url" id="social_whatsapp" name="social_whatsapp"
                               value="<?= esc(old('social_whatsapp', $settings['App.social_whatsapp'])) ?>"
                               placeholder="https://wa.me/6281234567890"
                               class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-sm text-white bg-brand-600 hover:bg-brand-700 shadow-md shadow-brand-500/20 transition-all">
                        Simpan Media Sosial
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- Tab 5: Kontak -->
    <?php if ($activeTab === 'contact'): ?>
        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs space-y-6">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Informasi Kontak & Alamat</h3>
                <p class="text-xs text-slate-500 mt-0.5">Informasi resmi yang akan ditampilkan di footer dan bagian kontak</p>
            </div>

            <form action="<?= base_url('admin/settings/update/contact') ?>" method="POST" class="space-y-5">
                <?= csrf_field() ?>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="contact_email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Alamat Email Resmi <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" id="contact_email" name="contact_email" required
                               value="<?= esc(old('contact_email', $settings['App.contact_email'])) ?>"
                               placeholder="contact@komeo.id"
                               class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="contact_phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Nomor Telepon Kantor
                        </label>
                        <input type="text" id="contact_phone" name="contact_phone"
                               value="<?= esc(old('contact_phone', $settings['App.contact_phone'])) ?>"
                               placeholder="+62 21 12345678"
                               class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="contact_whatsapp" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Nomor WhatsApp Helpdesk
                        </label>
                        <input type="text" id="contact_whatsapp" name="contact_whatsapp"
                               value="<?= esc(old('contact_whatsapp', $settings['App.contact_whatsapp'])) ?>"
                               placeholder="081234567890"
                               class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="contact_city" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Kota / Wilayah Operasional
                        </label>
                        <input type="text" id="contact_city" name="contact_city"
                               value="<?= esc(old('contact_city', $settings['App.contact_city'])) ?>"
                               placeholder="Jakarta & Seluruh Indonesia"
                               class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="contact_address" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Alamat Kantor / Sekretariat
                        </label>
                        <input type="text" id="contact_address" name="contact_address"
                               value="<?= esc(old('contact_address', $settings['App.contact_address'])) ?>"
                               placeholder="Jl. Event Kreatif No. 10, Jakarta Selatan"
                               class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="contact_maps_url" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Tautan Google Maps
                        </label>
                        <input type="url" id="contact_maps_url" name="contact_maps_url"
                               value="<?= esc(old('contact_maps_url', $settings['App.contact_maps_url'])) ?>"
                               placeholder="https://maps.google.com/?q=..."
                               class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-sm text-white bg-brand-600 hover:bg-brand-700 shadow-md shadow-brand-500/20 transition-all">
                        Simpan Kontak
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- Tab 6: SEO & Metadata -->
    <?php if ($activeTab === 'seo'): ?>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-8 bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">SEO & Metadata Global</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Optimasi mesin pencari dan pratinjau cuplikan media sosial</p>
                </div>

                <form action="<?= base_url('admin/settings/update/seo') ?>" method="POST" class="space-y-5">
                    <?= csrf_field() ?>

                    <div>
                        <label for="meta_title" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Default Meta Title <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="meta_title" name="meta_title" required
                               value="<?= esc(old('meta_title', $settings['App.meta_title'])) ?>"
                               oninput="document.getElementById('serp_title').innerText = this.value;"
                               class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        <p class="text-[11px] text-slate-400 mt-1">Judul yang muncul di tab browser dan hasil pencarian Google (50-60 karakter ideal).</p>
                    </div>

                    <div>
                        <label for="meta_description" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Default Meta Description <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="meta_description" name="meta_description" rows="3" required
                                  oninput="document.getElementById('serp_desc').innerText = this.value;"
                                  class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none"><?= esc(old('meta_description', $settings['App.meta_description'])) ?></textarea>
                        <p class="text-[11px] text-slate-400 mt-1">Ringkasan isi situs di hasil pencarian (150-160 karakter ideal).</p>
                    </div>

                    <div>
                        <label for="meta_keywords" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            SEO Keywords (Opsional)
                        </label>
                        <input type="text" id="meta_keywords" name="meta_keywords"
                               value="<?= esc(old('meta_keywords', $settings['App.meta_keywords'])) ?>"
                               placeholder="event organizer, vendor event, multimedia, lighting"
                               class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="social_share_title" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Judul Berbagi Sosial (Open Graph)
                            </label>
                            <input type="text" id="social_share_title" name="social_share_title"
                                   value="<?= esc(old('social_share_title', $settings['App.social_share_title'])) ?>"
                                   class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        </div>

                        <div>
                            <label for="robots_indexing" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Kebijakan Robot Search Engine
                            </label>
                            <select id="robots_indexing" name="robots_indexing"
                                    class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                                <option value="index, follow" <?= $settings['App.robots_indexing'] === 'index, follow' ? 'selected' : '' ?>>Index, Follow (Publik & Terindeks)</option>
                                <option value="noindex, follow" <?= $settings['App.robots_indexing'] === 'noindex, follow' ? 'selected' : '' ?>>Noindex, Follow (Sembunyikan Halaman)</option>
                                <option value="noindex, nofollow" <?= $settings['App.robots_indexing'] === 'noindex, nofollow' ? 'selected' : '' ?>>Noindex, Nofollow (Blokir Total Mesin Pencari)</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-sm text-white bg-brand-600 hover:bg-brand-700 shadow-md shadow-brand-500/20 transition-all">
                            Simpan SEO & Metadata
                        </button>
                    </div>
                </form>
            </div>

            <!-- SERP Preview Card -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Simulasi Google Search (SERP)</h3>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5">
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <span class="w-4 h-4 rounded-full bg-slate-200 flex items-center justify-center text-[9px] font-bold">K</span>
                            <span class="truncate"><?= base_url() ?></span>
                        </div>
                        <h4 id="serp_title" class="text-sm font-semibold text-blue-700 hover:underline cursor-pointer line-clamp-1 leading-snug">
                            <?= esc($settings['App.meta_title']) ?>
                        </h4>
                        <p id="serp_desc" class="text-xs text-slate-600 line-clamp-2 leading-relaxed">
                            <?= esc($settings['App.meta_description']) ?>
                        </p>
                    </div>

                    <div class="text-[11px] text-slate-400 leading-relaxed">
                        Perubahan metadata SEO akan langsung terpasang pada tag &lt;title&gt;, meta description, serta meta tag Open Graph di halaman publik.
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Tab 7: Footer Website -->
    <?php if ($activeTab === 'footer'): ?>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Form Card -->
            <div class="lg:col-span-8 bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs">
                <form action="<?= base_url('admin/settings/update/footer') ?>" method="POST" class="space-y-5">
                    <?= csrf_field() ?>

                    <div>
                        <label for="footer_description" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Deskripsi Footer (Slogan & Penjelasan Ringkas) <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="footer_description" name="footer_description" rows="4" required
                                  class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none"><?= esc(old('footer_description', $settings['App.footer_description'] ?? '')) ?></textarea>
                        <p class="text-[11px] text-slate-400 mt-1">Paragraf penjelasan resmi yang tampil di bawah logo pada bagian footer seluruh halaman.</p>
                    </div>

                    <div>
                        <label for="copyright_text" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Teks Hak Cipta (Copyright) <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="copyright_text" name="copyright_text" required
                               value="<?= esc(old('copyright_text', $settings['App.copyright_text'] ?? '')) ?>"
                               class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="footer_subtext" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Catatan Kaki Sub-Footer (Sisi Kanan Bawah)
                        </label>
                        <input type="text" id="footer_subtext" name="footer_subtext"
                               value="<?= esc(old('footer_subtext', $settings['App.footer_subtext'] ?? 'Dibangun untuk kemajuan ekosistem event Indonesia.')) ?>"
                               class="block w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        <p class="text-[11px] text-slate-400 mt-1">Teks kecil di baris paling bawah sebelah kanan copyright.</p>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-sm text-white bg-brand-600 hover:bg-brand-700 shadow-md shadow-brand-500/20 transition-all">
                            Simpan Pengaturan Footer
                        </button>
                    </div>
                </form>
            </div>

            <!-- Preview Card -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-slate-900 text-slate-300 p-6 rounded-2xl border border-slate-800 shadow-md space-y-4">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pratinjau Footer Publik</h3>
                    
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-brand-600 text-white font-extrabold flex items-center justify-center text-sm">K</div>
                        <span class="font-bold text-white text-base"><?= esc($settings['App.site_name']) ?></span>
                    </div>

                    <p class="text-xs text-slate-400 leading-relaxed whitespace-pre-line border-b border-slate-800 pb-4">
                        <?= esc($settings['App.footer_description'] ?? 'Satu Komunitas, Ribuan Peluang Kolaborasi...') ?>
                    </p>

                    <div class="text-[10px] text-slate-500 space-y-1">
                        <p><?= esc($settings['App.copyright_text'] ?? '© KOMEO.ID') ?></p>
                        <p class="italic text-slate-400"><?= esc($settings['App.footer_subtext'] ?? 'Dibangun untuk kemajuan ekosistem event Indonesia.') ?></p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
