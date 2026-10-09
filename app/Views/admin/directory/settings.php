<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Pengaturan Direktori Member & Vendor</h2>
            <p class="text-xs text-slate-500 mt-1">Konfigurasi teks hero, jumlah tampilan kartu, pengurutan default, dan modul direktori publik KOMEO.ID</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?= base_url('admin/directory') ?>" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Manajemen Direktori</span>
            </a>
            <a href="<?= base_url('member') ?>" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-xl transition shadow-xs">
                <span>Pratinjau Publik</span>
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    <?php if (session()->getFlashdata('message')): ?>
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-3">
            <div class="p-1.5 bg-emerald-100 text-emerald-700 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <p class="text-xs font-bold text-emerald-900"><?= esc(session()->getFlashdata('message')) ?></p>
        </div>
    <?php endif; ?>

    <?php if ($errors = session()->getFlashdata('errors')): ?>
        <div class="p-4 bg-red-50 border border-red-200 rounded-2xl space-y-1">
            <div class="flex items-center gap-2 text-xs font-bold text-red-900 mb-1">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Periksa kesalahan isian:
            </div>
            <ul class="list-disc list-inside text-xs text-red-700 space-y-0.5">
                <?php foreach ($errors as $err): ?>
                    <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Settings Form Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <form action="<?= base_url('admin/settings/directory') ?>" method="post" class="space-y-8">
            <?= csrf_field() ?>

            <!-- Section 1: Hero & Identitas Direktori -->
            <div class="space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-brand-600"></span>
                        1. Teks Hero & Identitas Direktori
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Judul utama dan deskripsi yang tampil di bagian atas halaman direktori publik.</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label for="directory_title" class="block text-xs font-bold text-slate-800 mb-1">Judul Utama Hero <span class="text-red-500">*</span></label>
                        <input type="text" 
                               id="directory_title" 
                               name="directory_title" 
                               value="<?= esc(old('directory_title', site_setting('Directory.title', 'Temukan Profesional Event Terbaik di KOMEO'))) ?>" 
                               required 
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                    </div>

                    <div>
                        <label for="directory_subtitle" class="block text-xs font-bold text-slate-800 mb-1">Subjudul / Deskripsi Hero <span class="text-red-500">*</span></label>
                        <textarea id="directory_subtitle" 
                                  name="directory_subtitle" 
                                  rows="2" 
                                  required 
                                  class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition"><?= esc(old('directory_subtitle', site_setting('Directory.subtitle', 'Jelajahi jaringan Event Organizer, vendor, freelancer, dan talent dari berbagai daerah di Indonesia.'))) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Section 2: Kapasitas & Pengurutan -->
            <div class="space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                        2. Kapasitas Halaman & Urutan Tampilan
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Konfigurasi jumlah member per halaman dan preferensi sorting.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="directory_featured_count" class="block text-xs font-bold text-slate-800 mb-1">Jumlah Member Sorotan (Featured) <span class="text-red-500">*</span></label>
                        <input type="number" 
                               id="directory_featured_count" 
                               name="directory_featured_count" 
                               min="1" 
                               max="30" 
                               value="<?= esc(old('directory_featured_count', site_setting('Directory.featured_count', '6'))) ?>" 
                               required 
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 transition">
                        <span class="text-[10px] text-slate-400 mt-1 block">Tampil di beranda dan header direktori (Maks. 30)</span>
                    </div>

                    <div>
                        <label for="directory_per_page" class="block text-xs font-bold text-slate-800 mb-1">Jumlah Kartu Per Halaman <span class="text-red-500">*</span></label>
                        <input type="number" 
                               id="directory_per_page" 
                               name="directory_per_page" 
                               min="3" 
                               max="50" 
                               value="<?= esc(old('directory_per_page', site_setting('Directory.per_page', '12'))) ?>" 
                               required 
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 transition">
                        <span class="text-[10px] text-slate-400 mt-1 block">Rekomendasi kelipatan 3 atau 6 (Default: 12)</span>
                    </div>

                    <div>
                        <label for="directory_default_sort" class="block text-xs font-bold text-slate-800 mb-1">Urutan Default <span class="text-red-500">*</span></label>
                        <?php $currentSort = old('directory_default_sort', site_setting('Directory.default_sort', 'newest')); ?>
                        <select id="directory_default_sort" 
                                name="directory_default_sort" 
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 transition">
                            <option value="newest" <?= ($currentSort === 'newest') ? 'selected' : '' ?>>Terbaru Bergabung</option>
                            <option value="oldest" <?= ($currentSort === 'oldest') ? 'selected' : '' ?>>Terlama Bergabung</option>
                            <option value="name_asc" <?= ($currentSort === 'name_asc') ? 'selected' : '' ?>>Nama A - Z</option>
                            <option value="name_desc" <?= ($currentSort === 'name_desc') ? 'selected' : '' ?>>Nama Z - A</option>
                        </select>
                        <span class="text-[10px] text-slate-400 mt-1 block">Metode pengurutan awal ketika filter kosong</span>
                    </div>
                </div>
            </div>

            <!-- Section 3: Tampilan Kartu & Metadata -->
            <div class="space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        3. Elemen Kartu Member
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Tampilkan atau sembunyikan informasi tertentu pada kartu direktori.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">Tampilkan Kota & Wilayah</span>
                            <span class="text-[11px] text-slate-500">Tampilkan lokasi domisili anggota di kartu direktori</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   name="directory_show_city" 
                                   value="1" 
                                   <?= (site_setting('Directory.show_city', '1') === '1') ? 'checked' : '' ?> 
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-600"></div>
                        </label>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">Tampilkan Tag Spesialisasi</span>
                            <span class="text-[11px] text-slate-500">Tampilkan keahlian spesifik member di kartu direktori</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   name="directory_show_specs" 
                                   value="1" 
                                   <?= (site_setting('Directory.show_specializations', '1') === '1') ? 'checked' : '' ?> 
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Section 4: Sakelar Modul Direktori Publik -->
            <div class="space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                        4. Sakelar Modul Direktori Publik
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Aktifkan atau nonaktifkan rute publik direktori sesuai kebutuhan operasional.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">Direktori Member</span>
                            <span class="text-[11px] text-slate-500">Rute: /member</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   name="directory_enable_public" 
                                   value="1" 
                                   <?= (site_setting('Directory.enable_public', '1') === '1') ? 'checked' : '' ?> 
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">Direktori Vendor</span>
                            <span class="text-[11px] text-slate-500">Rute: /cari-vendor</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   name="directory_enable_vendor" 
                                   value="1" 
                                   <?= (site_setting('Directory.enable_vendor', '1') === '1') ? 'checked' : '' ?> 
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">Direktori Crew</span>
                            <span class="text-[11px] text-slate-500">Rute: /cari-crew</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   name="directory_enable_crew" 
                                   value="1" 
                                   <?= (site_setting('Directory.enable_crew', '1') === '1') ? 'checked' : '' ?> 
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="<?= base_url('admin/directory') ?>" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition shadow-xs">
                    Simpan Pengaturan Direktori
                </button>
            </div>
        </form>
    </div>

</div>

<?= $this->endSection() ?>
