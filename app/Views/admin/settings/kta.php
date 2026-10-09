<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 mb-2">
                <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                KTA Studio & Live Visual Layout Editor
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Pengaturan Desain & Editor Tata Letak KTA</h2>
            <p class="text-xs text-slate-500 mt-1">Sesuaikan posisi nama, foto, nomor anggota, QR code secara visual, upload desain background sendiri, dan kustomisasi seluruh konten sisi belakang.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="<?= site_url('admin/kta') ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-colors shadow-xs">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Daftar KTA</span>
            </a>
            <a href="<?= site_url('admin/kta/bulk') ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Massal</span>
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('message')): ?>
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <p class="text-xs font-bold text-emerald-900"><?= esc(session()->getFlashdata('message')) ?></p>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-center gap-3">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <p class="text-xs font-bold text-rose-900"><?= esc(session()->getFlashdata('error')) ?></p>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl space-y-1">
            <?php foreach (session()->getFlashdata('errors') as $err): ?>
                <p class="text-xs font-bold text-rose-800">• <?= esc($err) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Two-Column Layout: Live Preview & Canvas (Left) and Configuration Tabs (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- Left: Live Preview Canvas -->
        <div class="lg:col-span-5 space-y-4 lg:sticky lg:top-20">
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-xs flex flex-col items-center">
                <div class="w-full flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-extrabold uppercase tracking-wider text-slate-800">Pratinjau Langsung</span>
                        <span id="canvas-mode-indicator" class="text-[10px] px-2 py-0.5 rounded-full font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">Mode Interaktif</span>
                    </div>
                    <button type="button" onclick="refreshPreview()" class="text-xs text-brand-600 hover:text-brand-800 font-bold flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Segarkan
                    </button>
                </div>

                <!-- Preview Card Box with Interactive Draggable Overlay -->
                <div id="card-preview-container" class="relative w-full aspect-[1011/638] rounded-2xl overflow-hidden border border-slate-800 shadow-xl bg-slate-950 select-none">
                    <!-- Base Rendered Image -->
                    <img id="sample-preview-img" src="<?= site_url('admin/settings/kta/preview-sample/front') ?>?t=<?= time() ?>" alt="Pratinjau KTA" class="w-full h-full object-cover pointer-events-none">

                    <!-- Visual Drag / Select Overlay (Only active on front side) -->
                    <div id="interactive-overlay" class="absolute inset-0 w-full h-full pointer-events-auto">
                        <!-- Photo Box Pin -->
                        <div id="pin-photo" class="canvas-pin absolute border-2 border-indigo-400/90 bg-indigo-500/20 rounded cursor-move flex items-center justify-center text-[10px] font-bold text-white shadow-xs backdrop-blur-[1px]" title="Geser untuk atur posisi Foto Profil">
                            <span class="bg-indigo-900/80 px-1 py-0.5 rounded text-[9px] pointer-events-none">📷 Foto</span>
                        </div>

                        <!-- Name Pin -->
                        <div id="pin-name" class="canvas-pin absolute border-2 border-cyan-400/90 bg-cyan-500/20 rounded cursor-move flex items-center justify-center text-[10px] font-bold text-white shadow-xs backdrop-blur-[1px]" title="Geser untuk atur posisi Nama">
                            <span class="bg-cyan-900/80 px-1 py-0.5 rounded text-[9px] pointer-events-none">👤 Nama</span>
                        </div>

                        <!-- Number Pin -->
                        <div id="pin-number" class="canvas-pin absolute border-2 border-amber-400/90 bg-amber-500/25 rounded cursor-move flex items-center justify-center text-[10px] font-bold text-white shadow-xs backdrop-blur-[1px]" title="Geser untuk atur posisi Nomor Anggota">
                            <span class="bg-amber-900/80 px-1 py-0.5 rounded text-[9px] pointer-events-none">🏷️ No. Anggota</span>
                        </div>

                        <!-- Category Pin -->
                        <div id="pin-category" class="canvas-pin absolute border-2 border-purple-400/90 bg-purple-500/20 rounded cursor-move flex items-center justify-center text-[10px] font-bold text-white shadow-xs backdrop-blur-[1px]" title="Geser untuk atur posisi Kategori">
                            <span class="bg-purple-900/80 px-1 py-0.5 rounded text-[9px] pointer-events-none">🏢 Kategori</span>
                        </div>

                        <!-- QR Code Pin -->
                        <div id="pin-qr" class="canvas-pin absolute border-2 border-emerald-400/90 bg-emerald-500/20 rounded cursor-move flex items-center justify-center text-[10px] font-bold text-white shadow-xs backdrop-blur-[1px]" title="Geser untuk atur posisi QR Code">
                            <span class="bg-emerald-900/80 px-1 py-0.5 rounded text-[9px] pointer-events-none">📱 QR Code</span>
                        </div>
                    </div>
                </div>

                <!-- Side Switcher -->
                <div class="mt-4 flex gap-2 w-full">
                    <button type="button" onclick="switchPreviewSide('front')" id="preview-tab-front" class="flex-1 py-2 rounded-xl text-xs font-bold bg-brand-600 text-white shadow-xs transition-colors">
                        Sisi Depan
                    </button>
                    <button type="button" onclick="switchPreviewSide('back')" id="preview-tab-back" class="flex-1 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors">
                        Sisi Belakang
                    </button>
                </div>

                <!-- Quick Help / Coordinates feedback -->
                <div class="w-full mt-3 p-3 bg-slate-50 border border-slate-200/80 rounded-xl text-[11px] text-slate-500 space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-700">Elemen yang Dipilih:</span>
                        <span id="active-pin-name" class="font-mono font-bold text-indigo-600">Klik / geser pin di kartu</span>
                    </div>
                    <div class="flex items-center justify-between text-[10px] text-slate-400">
                        <span>Koordinat Canvas:</span>
                        <span id="active-pin-coords" class="font-mono">X: -, Y: -</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Tabs and Settings Forms -->
        <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
            
            <!-- Navigation Tabs Bar -->
            <div class="flex items-center overflow-x-auto border-b border-slate-100 bg-slate-50/50 p-2 gap-1.5 text-xs font-bold select-none">
                <a href="<?= site_url('admin/settings/kta?tab=layout') ?>" class="px-4 py-2.5 rounded-xl whitespace-nowrap transition-colors <?= $activeTab === 'layout' ? 'bg-white text-brand-600 shadow-xs border border-slate-200/60' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50' ?>">
                    📐 1. Editor Tata Letak (Live)
                </a>
                <a href="<?= site_url('admin/settings/kta?tab=back') ?>" class="px-4 py-2.5 rounded-xl whitespace-nowrap transition-colors <?= $activeTab === 'back' ? 'bg-white text-brand-600 shadow-xs border border-slate-200/60' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50' ?>">
                    📝 2. Kustom Desain Belakang
                </a>
                <a href="<?= site_url('admin/settings/kta?tab=assets') ?>" class="px-4 py-2.5 rounded-xl whitespace-nowrap transition-colors <?= $activeTab === 'assets' ? 'bg-white text-brand-600 shadow-xs border border-slate-200/60' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50' ?>">
                    🖼️ 3. Upload Background & Logo
                </a>
                <a href="<?= site_url('admin/settings/kta?tab=front') ?>" class="px-4 py-2.5 rounded-xl whitespace-nowrap transition-colors <?= $activeTab === 'front' ? 'bg-white text-brand-600 shadow-xs border border-slate-200/60' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50' ?>">
                    🎨 4. Teks & Warna Depan
                </a>
                <a href="<?= site_url('admin/settings/kta?tab=colors') ?>" class="px-4 py-2.5 rounded-xl whitespace-nowrap transition-colors <?= $activeTab === 'colors' ? 'bg-white text-brand-600 shadow-xs border border-slate-200/60' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50' ?>">
                    🌈 5. Palet Warna Aksen
                </a>
                <a href="<?= site_url('admin/settings/kta?tab=qr') ?>" class="px-4 py-2.5 rounded-xl whitespace-nowrap transition-colors <?= $activeTab === 'qr' ? 'bg-white text-brand-600 shadow-xs border border-slate-200/60' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50' ?>">
                    📱 6. QR Code
                </a>
                <a href="<?= site_url('admin/settings/kta?tab=print') ?>" class="px-4 py-2.5 rounded-xl whitespace-nowrap transition-colors <?= $activeTab === 'print' ? 'bg-white text-brand-600 shadow-xs border border-slate-200/60' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50' ?>">
                    🖨️ 7. Profil Cetak
                </a>
            </div>

            <!-- Tab Content Forms -->
            <div class="p-6 sm:p-8">

                <!-- TAB: LIVE VISUAL LAYOUT EDITOR -->
                <?php if ($activeTab === 'layout'): ?>
                    <form id="layout-form" method="post" action="<?= site_url('admin/settings/kta/update/layout') ?>" enctype="multipart/form-data" class="space-y-6">
                        <?= csrf_field() ?>

                        <!-- Preset Quick Actions -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                            <span class="text-xs font-extrabold text-slate-800 uppercase tracking-wider block">Pilihan Preset Cepat Tata Letak:</span>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" onclick="applyLayoutPreset('default')" class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-100 text-xs font-bold text-slate-700 shadow-xs">
                                    ⚡ Standar KOMEO (Foto Kiri)
                                </button>
                                <button type="button" onclick="applyLayoutPreset('clean_upload')" class="px-3 py-1.5 rounded-xl bg-indigo-50 border border-indigo-200 hover:bg-indigo-100 text-xs font-bold text-indigo-700 shadow-xs">
                                    🎨 Bersih (Khusus Background Upload)
                                </button>
                                <button type="button" onclick="applyLayoutPreset('right_photo')" class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-100 text-xs font-bold text-slate-700 shadow-xs">
                                    🔄 Foto di Kanan
                                </button>
                            </div>
                        </div>

                        <!-- Shape Nomor Anggota & Background Toggles -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Shape Gaya Nomor Anggota -->
                            <div>
                                <label class="block text-xs font-bold text-slate-800 mb-1">Gaya Shape Nomor Anggota:</label>
                                <select id="input_number_style" name="number_style" onchange="syncFromInputs()" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                                    <option value="gold_badge" <?= ($settings['number_style'] ?? '') === 'gold_badge' ? 'selected' : '' ?>>👑 Gold Luxury Obsidian Chip (Rekomendasi)</option>
                                    <option value="glass_pill" <?= ($settings['number_style'] ?? '') === 'glass_pill' ? 'selected' : '' ?>>💎 Glassmorphism Pill + Glowing Cyan Dot</option>
                                    <option value="neon_cyan" <?= ($settings['number_style'] ?? '') === 'neon_cyan' ? 'selected' : '' ?>>⚡ High-Tech Cyber Neon Bracket</option>
                                    <option value="modern_slate" <?= ($settings['number_style'] ?? '') === 'modern_slate' ? 'selected' : '' ?>>🖤 Charcoal Slate Minimalis 1px</option>
                                    <option value="minimal_outline" <?= ($settings['number_style'] ?? '') === 'minimal_outline' ? 'selected' : '' ?>>🔲 Minimalist Outline</option>
                                </select>
                                <span class="text-[11px] text-slate-400">Pilih badge resmi agar nomor anggota terlihat elegan dan premium.</span>
                            </div>

                            <!-- Bentuk Foto Profil -->
                            <div>
                                <label class="block text-xs font-bold text-slate-800 mb-1">Bentuk Bingkai Foto Profil:</label>
                                <select id="input_photo_shape" name="photo_shape" onchange="syncFromInputs()" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                                    <option value="rounded" <?= ($settings['photo_shape'] ?? '') === 'rounded' ? 'selected' : '' ?>>🔲 Persegi Membulat (Modern Rounded)</option>
                                    <option value="circle" <?= ($settings['photo_shape'] ?? '') === 'circle' ? 'selected' : '' ?>>⚪ Lingkaran Presisi (Circle)</option>
                                    <option value="oval" <?= ($settings['photo_shape'] ?? '') === 'oval' ? 'selected' : '' ?>>🥚 Oval (Ellipse)</option>
                                    <option value="square" <?= ($settings['photo_shape'] ?? '') === 'square' ? 'selected' : '' ?>>⬛ Kotak Tegak (Sharp Square)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Checkboxes: Hide Default Shapes & Metadata -->
                        <div class="p-4 rounded-2xl bg-indigo-50/40 border border-indigo-100 space-y-3">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="hide_default_shapes" value="1" <?= ($settings['hide_default_shapes'] ?? '0') === '1' ? 'checked' : '' ?> class="mt-0.5 w-4 h-4 rounded text-brand-600 focus:ring-brand-500 border-slate-300">
                                <div>
                                    <span class="text-xs font-bold text-indigo-950 block">Sembunyikan Shape & Grafik Bawaan (Khusus Artwork Sendiri)</span>
                                    <span class="text-[11px] text-indigo-700">Aktifkan ini jika Anda mengunggah gambar background hasil desain Photoshop/Canva/Figma sendiri agar ornamen lingkaran bawaan tidak menumpuk.</span>
                                </div>
                            </label>

                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="show_meta" value="1" <?= ($settings['show_meta'] ?? '1') !== '0' ? 'checked' : '' ?> class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 border-slate-300">
                                <span class="text-xs font-bold text-slate-800">Tampilkan Info Domisili & Tahun Bergabung</span>
                            </label>

                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="show_status_badge" value="1" <?= ($settings['show_status_badge'] ?? '1') !== '0' ? 'checked' : '' ?> class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 border-slate-300">
                                <span class="text-xs font-bold text-slate-800">Tampilkan Pill "ANGGOTA AKTIF" di Kanan Atas</span>
                            </label>
                        </div>

                        <!-- Quick Upload Background Depan -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50 space-y-2">
                            <label class="block text-xs font-bold text-slate-800">Upload Cepat Desain Background Depan (1011 × 638 px):</label>
                            <input type="file" name="quick_bg_front" accept="image/png,image/jpeg,image/webp" class="text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <?php if (!empty($settings['bg_pattern_front'])): ?>
                                <p class="text-[11px] text-emerald-700 font-semibold">Background aktif: <?= esc(basename($settings['bg_pattern_front'])) ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Coordinates Section -->
                        <div class="space-y-4">
                            <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 pb-2 border-b border-slate-100">
                                Koordinat Presisi Elemen Depan (Canvas 1011 × 638 px)
                            </h4>

                            <!-- Foto Profil -->
                            <div class="p-3.5 rounded-2xl border border-slate-100 bg-slate-50/60 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-indigo-700">📷 Foto Profil</span>
                                    <span class="text-[10px] text-slate-400">Posisi & Dimensi</span>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-500">X (px)</label>
                                        <input type="number" id="input_pos_photo_x" name="pos_photo_x" value="<?= esc($settings['pos_photo_x']) ?>" oninput="syncFromInputs()" class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-mono">
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-500">Y (px)</label>
                                        <input type="number" id="input_pos_photo_y" name="pos_photo_y" value="<?= esc($settings['pos_photo_y']) ?>" oninput="syncFromInputs()" class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-mono">
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-500">Lebar (px)</label>
                                        <input type="number" id="input_pos_photo_w" name="pos_photo_w" value="<?= esc($settings['pos_photo_w']) ?>" oninput="syncFromInputs()" class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-mono">
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-500">Tinggi (px)</label>
                                        <input type="number" id="input_pos_photo_h" name="pos_photo_h" value="<?= esc($settings['pos_photo_h']) ?>" oninput="syncFromInputs()" class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-mono">
                                    </div>
                                </div>
                            </div>

                            <!-- Nama Anggota -->
                            <div class="p-3.5 rounded-2xl border border-slate-100 bg-slate-50/60 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-cyan-700">👤 Nama Anggota</span>
                                    <span class="text-[10px] text-slate-400">Posisi & Ukuran Teks</span>
                                </div>
                                <div class="grid grid-cols-3 gap-2">
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-500">X (px)</label>
                                        <input type="number" id="input_pos_name_x" name="pos_name_x" value="<?= esc($settings['pos_name_x']) ?>" oninput="syncFromInputs()" class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-mono">
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-500">Y (px)</label>
                                        <input type="number" id="input_pos_name_y" name="pos_name_y" value="<?= esc($settings['pos_name_y']) ?>" oninput="syncFromInputs()" class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-mono">
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-500">Ukuran Font (pt)</label>
                                        <input type="number" id="input_pos_name_size" name="pos_name_size" value="<?= esc($settings['pos_name_size']) ?>" oninput="syncFromInputs()" class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-mono">
                                    </div>
                                </div>
                            </div>

                            <!-- Nomor Anggota -->
                            <div class="p-3.5 rounded-2xl border border-slate-100 bg-slate-50/60 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-amber-700">🏷️ Nomor Anggota</span>
                                    <span class="text-[10px] text-slate-400">Posisi Badge</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-500">X (px)</label>
                                        <input type="number" id="input_pos_number_x" name="pos_number_x" value="<?= esc($settings['pos_number_x']) ?>" oninput="syncFromInputs()" class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-mono">
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-500">Y (px)</label>
                                        <input type="number" id="input_pos_number_y" name="pos_number_y" value="<?= esc($settings['pos_number_y']) ?>" oninput="syncFromInputs()" class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-mono">
                                    </div>
                                </div>
                            </div>

                            <!-- Kategori Spesialisasi -->
                            <div class="p-3.5 rounded-2xl border border-slate-100 bg-slate-50/60 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-purple-700">🏢 Kategori Spesialisasi</span>
                                    <span class="text-[10px] text-slate-400">Posisi Teks</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-500">X (px)</label>
                                        <input type="number" id="input_pos_category_x" name="pos_category_x" value="<?= esc($settings['pos_category_x']) ?>" oninput="syncFromInputs()" class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-mono">
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-500">Y (px)</label>
                                        <input type="number" id="input_pos_category_y" name="pos_category_y" value="<?= esc($settings['pos_category_y']) ?>" oninput="syncFromInputs()" class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-mono">
                                    </div>
                                </div>
                            </div>

                            <!-- QR Code -->
                            <div class="p-3.5 rounded-2xl border border-slate-100 bg-slate-50/60 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-emerald-700">📱 QR Code Verifikasi</span>
                                    <span class="text-[10px] text-slate-400">Posisi & Dimensi QR</span>
                                </div>
                                <div class="grid grid-cols-3 gap-2">
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-500">X (px)</label>
                                        <input type="number" id="input_pos_qr_x" name="pos_qr_x" value="<?= esc($settings['pos_qr_x']) ?>" oninput="syncFromInputs()" class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-mono">
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-500">Y (px)</label>
                                        <input type="number" id="input_pos_qr_y" name="pos_qr_y" value="<?= esc($settings['pos_qr_y']) ?>" oninput="syncFromInputs()" class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-mono">
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-500">Ukuran QR (px)</label>
                                        <input type="number" id="input_pos_qr_size" name="pos_qr_size" value="<?= esc($settings['pos_qr_size']) ?>" oninput="syncFromInputs()" class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-mono">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                            <button type="button" onclick="refreshPreview()" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold">
                                Cek Pratinjau
                            </button>
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs transition-colors shadow-sm">
                                Simpan Tata Letak
                            </button>
                        </div>
                    </form>
                <?php endif; ?>

                <!-- TAB: DESAIN BELAKANG (FULL CUSTOM) -->
                <?php if ($activeTab === 'back'): ?>
                    <form method="post" action="<?= site_url('admin/settings/kta/update/back') ?>" class="space-y-5">
                        <?= csrf_field() ?>

                        <div class="p-3.5 bg-indigo-50/50 rounded-2xl border border-indigo-100 text-xs text-indigo-900">
                            <strong>Kustomisasi Penuh Sisi Belakang:</strong> Seluruh teks, header, ketentuan, dan tanda tangan dapat diubah secara dinamis sesuai kebutuhan resmi organisasi.
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Judul Organisasi Atas</label>
                                <input type="text" name="back_header_title" value="<?= esc($settings['back_header_title'] ?? 'KOMEO.ID') ?>" required
                                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Subjudul Organisasi</label>
                                <input type="text" name="back_header_subtitle" value="<?= esc($settings['back_header_subtitle'] ?? 'KOMUNITAS EVENT ORGANIZER INDONESIA') ?>"
                                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Label Badge Kanan Atas</label>
                                <input type="text" name="back_badge_text" value="<?= esc($settings['back_badge_text'] ?? 'IDENTITAS RESMI KEANGGOTAAN') ?>"
                                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Tagline Komunitas</label>
                                <input type="text" name="tagline" value="<?= esc($settings['tagline']) ?>" required
                                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Pernyataan Resmi Keanggotaan</label>
                            <textarea name="back_statement" rows="3" required
                                      class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20"><?= esc($settings['back_statement']) ?></textarea>
                            <span class="text-[11px] text-slate-400">Pernyataan pembuka mengenai hak kepemilikan dan cara verifikasi kartu.</span>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Judul Bagian Ketentuan</label>
                            <input type="text" name="back_rules_title" value="<?= esc($settings['back_rules_title'] ?? 'KETENTUAN PENGGUNAAN KARTU:') ?>"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Poin-poin Ketentuan & Aturan (1 Baris = 1 Poin)</label>
                            <textarea name="back_rules_text" rows="4"
                                      class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20"><?= esc($settings['back_rules_text'] ?? "1. Kartu ini hanya berlaku bagi anggota yang terdaftar resmi dan berstatus aktif di KOMEO.ID.\n2. Kartu ini tidak dapat dipindahtangankan, digandakan, atau dipinjamkan kepada pihak mana pun.\n3. Anggota wajib menjunjung tinggi etika profesi dan integritas industri event Indonesia.\n4. Apabila keanggotaan ditangguhkan atau dicabut, hak kepemilikan dan verifikasi otomatis gugur.") ?></textarea>
                        </div>

                        <!-- Signature / Pengurus Section -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="back_show_sign" value="1" <?= ($settings['back_show_sign'] ?? '0') === '1' ? 'checked' : '' ?> class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 border-slate-300">
                                <span class="text-xs font-bold text-slate-800">Tampilkan Kotak Tanda Tangan / Dewan Pengurus di Belakang</span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                                <div>
                                    <label class="text-[11px] font-bold text-slate-600">Jabatan Penandatangan</label>
                                    <input type="text" name="back_sign_title" value="<?= esc($settings['back_sign_title'] ?? 'Dewan Pengurus KOMEO.ID') ?>" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs">
                                </div>
                                <div>
                                    <label class="text-[11px] font-bold text-slate-600">Nama Pejabat</label>
                                    <input type="text" name="back_sign_name" value="<?= esc($settings['back_sign_name'] ?? 'Ketua Umum') ?>" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs">
                                </div>
                            </div>
                        </div>

                        <!-- Footer Text & Website -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Sekretariat / Kontak Footer</label>
                                <input type="text" name="back_office_address" value="<?= esc($settings['back_office_address'] ?? 'Sekretariat Pusat KOMEO.ID | Hak Cipta Dilindungi Undang-Undang') ?>"
                                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Website Resmi</label>
                                <input type="url" name="website_url" value="<?= esc($settings['website_url']) ?>" required
                                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Warna Background Belakang</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" name="bg_color_back" value="<?= esc($settings['bg_color_back']) ?>" class="w-10 h-10 p-1 rounded-xl border border-slate-200 cursor-pointer">
                                    <input type="text" readonly value="<?= esc($settings['bg_color_back']) ?>" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-700">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Warna Teks Belakang</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" name="text_color_back" value="<?= esc($settings['text_color_back']) ?>" class="w-10 h-10 p-1 rounded-xl border border-slate-200 cursor-pointer">
                                    <input type="text" readonly value="<?= esc($settings['text_color_back']) ?>" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-700">
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex justify-end">
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs transition-colors shadow-sm">
                                Simpan Kustom Belakang
                            </button>
                        </div>
                    </form>
                <?php endif; ?>

                <!-- TAB: DESAIN DEPAN -->
                <?php if ($activeTab === 'front'): ?>
                    <form method="post" action="<?= site_url('admin/settings/kta/update/front') ?>" class="space-y-5">
                        <?= csrf_field() ?>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Judul Kartu (Card Title)</label>
                            <input type="text" name="card_title" value="<?= esc($settings['card_title']) ?>" required
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                            <span class="text-[11px] text-slate-400">Contoh: KARTU TANDA ANGGOTA</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Warna Background Depan</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" name="bg_color_front" value="<?= esc($settings['bg_color_front']) ?>" class="w-10 h-10 p-1 rounded-xl border border-slate-200 cursor-pointer">
                                    <input type="text" readonly value="<?= esc($settings['bg_color_front']) ?>" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-700">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Warna Teks Utama Depan</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" name="text_color_front" value="<?= esc($settings['text_color_front']) ?>" class="w-10 h-10 p-1 rounded-xl border border-slate-200 cursor-pointer">
                                    <input type="text" readonly value="<?= esc($settings['text_color_front']) ?>" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-700">
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex justify-end">
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs transition-colors shadow-sm">
                                Simpan Desain Depan
                            </button>
                        </div>
                    </form>
                <?php endif; ?>

                <!-- TAB: LOGO & BACKGROUND ASSETS -->
                <?php if ($activeTab === 'assets'): ?>
                    <form method="post" action="<?= site_url('admin/settings/kta/update/assets') ?>" enctype="multipart/form-data" class="space-y-6">
                        <?= csrf_field() ?>

                        <!-- Custom Logo -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-3">
                            <label class="block text-xs font-bold text-slate-800">Logo Khusus KTA (Opsional):</label>
                            <?php if (!empty($settings['logo'])): ?>
                                <div class="flex items-center gap-4">
                                    <img src="<?= base_url(esc($settings['logo'])) ?>" alt="Logo KTA" class="h-10 w-auto bg-slate-900 p-2 rounded-xl">
                                    <button type="button" onclick="deleteAsset('logo')" class="text-xs text-rose-600 hover:underline font-bold">Hapus Logo KTA</button>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="logo" accept="image/png,image/jpeg,image/webp" class="text-xs text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <p class="text-[11px] text-slate-400">Jika kosong, sistem akan menggunakan logo resmi website secara otomatis.</p>
                        </div>

                        <!-- Front Pattern -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-3">
                            <label class="block text-xs font-bold text-slate-800">Artwork Background Depan (1011 × 638 px PNG/JPG):</label>
                            <?php if (!empty($settings['bg_pattern_front'])): ?>
                                <div class="flex items-center gap-4">
                                    <span class="text-xs font-mono text-emerald-700 bg-emerald-50 px-2 py-1 rounded">Aktif: <?= esc(basename($settings['bg_pattern_front'])) ?></span>
                                    <button type="button" onclick="deleteAsset('bg_pattern_front')" class="text-xs text-rose-600 hover:underline font-bold">Hapus Background</button>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="bg_pattern_front" accept="image/png,image/jpeg,image/webp" class="text-xs text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <p class="text-[11px] text-slate-400">Upload desain kartu depan utuh. Setelah upload, Anda bisa menyetel posisi nama/foto/nomor di Tab "1. Editor Tata Letak".</p>
                        </div>

                        <!-- Back Pattern -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-3">
                            <label class="block text-xs font-bold text-slate-800">Artwork Background Belakang (1011 × 638 px PNG/JPG):</label>
                            <?php if (!empty($settings['bg_pattern_back'])): ?>
                                <div class="flex items-center gap-4">
                                    <span class="text-xs font-mono text-emerald-700 bg-emerald-50 px-2 py-1 rounded">Aktif: <?= esc(basename($settings['bg_pattern_back'])) ?></span>
                                    <button type="button" onclick="deleteAsset('bg_pattern_back')" class="text-xs text-rose-600 hover:underline font-bold">Hapus Background</button>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="bg_pattern_back" accept="image/png,image/jpeg,image/webp" class="text-xs text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex justify-end">
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs transition-colors shadow-sm">
                                Unggah & Simpan Berkas
                            </button>
                        </div>
                    </form>

                    <form id="delete-asset-form" method="post" action="" class="hidden">
                        <?= csrf_field() ?>
                    </form>
                <?php endif; ?>

                <!-- TAB: WARNA & TYPOGRAPHY -->
                <?php if ($activeTab === 'colors'): ?>
                    <form method="post" action="<?= site_url('admin/settings/kta/update/colors') ?>" class="space-y-5">
                        <?= csrf_field() ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Aksen Warna Ungu / Primer</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" name="accent_purple" value="<?= esc($settings['accent_purple']) ?>" class="w-10 h-10 p-1 rounded-xl border border-slate-200 cursor-pointer">
                                    <input type="text" readonly value="<?= esc($settings['accent_purple']) ?>" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-700">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Aksen Warna Cyan / Sekunder</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" name="accent_cyan" value="<?= esc($settings['accent_cyan']) ?>" class="w-10 h-10 p-1 rounded-xl border border-slate-200 cursor-pointer">
                                    <input type="text" readonly value="<?= esc($settings['accent_cyan']) ?>" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-700">
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex justify-end">
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs transition-colors shadow-sm">
                                Simpan Palet Warna
                            </button>
                        </div>
                    </form>
                <?php endif; ?>

                <!-- TAB: QR CODE -->
                <?php if ($activeTab === 'qr'): ?>
                    <form method="post" action="<?= site_url('admin/settings/kta/update/qr') ?>" class="space-y-5">
                        <?= csrf_field() ?>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Posisi QR Code pada Kartu Depan</label>
                            <select name="qr_position" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-900">
                                <option value="right" <?= $settings['qr_position'] === 'right' ? 'selected' : '' ?>>Kanan Bawah (Standar Direkomendasikan)</option>
                                <option value="left" <?= $settings['qr_position'] === 'left' ? 'selected' : '' ?>>Kiri Bawah</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Ukuran QR Code (Pixel Canvas):</label>
                            <input type="number" min="140" max="240" name="qr_size" value="<?= esc($settings['qr_size']) ?>" required
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-900">
                            <span class="text-[11px] text-slate-400">Rentang aman: 140 - 240 px (mempertahankan quiet zone dan scannability scanner fisik).</span>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex justify-end">
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs transition-colors shadow-sm">
                                Simpan Pengaturan QR
                            </button>
                        </div>
                    </form>
                <?php endif; ?>

                <!-- TAB: PENGATURAN CETAK -->
                <?php if ($activeTab === 'print'): ?>
                    <form method="post" action="<?= site_url('admin/settings/kta/update/print') ?>" class="space-y-5">
                        <?= csrf_field() ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Profil Cetak Standar</label>
                                <select name="print_profile" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-900">
                                    <option value="pvc" <?= $settings['print_profile'] === 'pvc' ? 'selected' : '' ?>>Printer ID Card PVC (CR80 Tunggal)</option>
                                    <option value="digital_sheet" <?= $settings['print_profile'] === 'digital_sheet' ? 'selected' : '' ?>>Lembaran Digital / Offset (Sheet A4/A3)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Resolusi Cetak (DPI)</label>
                                <select name="export_dpi" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-900">
                                    <option value="300" <?= $settings['export_dpi'] == 300 ? 'selected' : '' ?>>300 DPI (Standar Industri)</option>
                                    <option value="600" <?= $settings['export_dpi'] == 600 ? 'selected' : '' ?>>600 DPI (Ultra High Precision)</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Bleed / Batas Potong (mm)</label>
                                <input type="number" step="0.5" min="0" max="10" name="bleed_mm" value="<?= esc($settings['bleed_mm']) ?>" required
                                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-900">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Ukuran Kertas Lembaran</label>
                                <select name="sheet_size" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-900">
                                    <option value="A4" <?= $settings['sheet_size'] === 'A4' ? 'selected' : '' ?>>A4 (Grid 2×5 = 10 Kartu)</option>
                                    <option value="A3" <?= $settings['sheet_size'] === 'A3' ? 'selected' : '' ?>>A3 (Grid 3×7 = 21 Kartu)</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Batas Aman Konten / Safe Margin (mm)</label>
                                <input type="number" step="0.5" min="1" max="10" name="safe_margin_mm" value="<?= esc($settings['safe_margin_mm']) ?>" required
                                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-900">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Rotasi Belakang Duplex</label>
                                <select name="back_rotation" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-900">
                                    <option value="0" <?= $settings['back_rotation'] == 0 ? 'selected' : '' ?>>0 Derajat (Normal)</option>
                                    <option value="180" <?= $settings['back_rotation'] == 180 ? 'selected' : '' ?>>180 Derajat (Rotasi Terbalik)</option>
                                </select>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex justify-end">
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs transition-colors shadow-sm">
                                Simpan Profil Cetak
                            </button>
                        </div>
                    </form>
                <?php endif; ?>

            </div>

        </div>

    </div>

</div>

<script>
    let currentPreviewSide = 'front';
    const CARD_CANVAS_W = 1011;
    const CARD_CANVAS_H = 638;

    // Elements tracked on visual overlay
    const elements = {
        photo: {
            pinId: 'pin-photo',
            inputX: 'input_pos_photo_x',
            inputY: 'input_pos_photo_y',
            inputW: 'input_pos_photo_w',
            inputH: 'input_pos_photo_h',
            name: 'Foto Profil'
        },
        name: {
            pinId: 'pin-name',
            inputX: 'input_pos_name_x',
            inputY: 'input_pos_name_y',
            defaultW: 340,
            defaultH: 45,
            name: 'Nama Anggota'
        },
        number: {
            pinId: 'pin-number',
            inputX: 'input_pos_number_x',
            inputY: 'input_pos_number_y',
            defaultW: 285,
            defaultH: 50,
            offsetY: -34,
            name: 'Nomor Anggota'
        },
        category: {
            pinId: 'pin-category',
            inputX: 'input_pos_category_x',
            inputY: 'input_pos_category_y',
            defaultW: 280,
            defaultH: 36,
            name: 'Kategori'
        },
        qr: {
            pinId: 'pin-qr',
            inputX: 'input_pos_qr_x',
            inputY: 'input_pos_qr_y',
            inputW: 'input_pos_qr_size',
            inputH: 'input_pos_qr_size',
            name: 'QR Code'
        }
    };

    function updateOverlayFromInputs() {
        const container = document.getElementById('card-preview-container');
        if (!container) return;
        const scaleX = container.clientWidth / CARD_CANVAS_W;
        const scaleY = container.clientHeight / CARD_CANVAS_H;

        for (const [key, el] of Object.entries(elements)) {
            const pin = document.getElementById(el.pinId);
            const inX = document.getElementById(el.inputX);
            const inY = document.getElementById(el.inputY);
            if (!pin || !inX || !inY) continue;

            const cardX = parseFloat(inX.value) || 0;
            const cardY = parseFloat(inY.value) || 0;
            const cardW = el.inputW ? (parseFloat(document.getElementById(el.inputW)?.value) || el.defaultW || 100) : (el.defaultW || 100);
            const cardH = el.inputH ? (parseFloat(document.getElementById(el.inputH)?.value) || el.defaultH || 50) : (el.defaultH || 50);
            const offY = el.offsetY || 0;

            pin.style.left = (cardX * scaleX) + 'px';
            pin.style.top = ((cardY + offY) * scaleY) + 'px';
            pin.style.width = (cardW * scaleX) + 'px';
            pin.style.height = (cardH * scaleY) + 'px';
        }
    }

    function syncFromInputs() {
        updateOverlayFromInputs();
    }

    // Drag-and-drop setup
    let activeDrag = null;
    let dragStartX = 0;
    let dragStartY = 0;
    let initialCardX = 0;
    let initialCardY = 0;

    function initInteractiveOverlay() {
        const container = document.getElementById('card-preview-container');
        if (!container) return;

        for (const [key, el] of Object.entries(elements)) {
            const pin = document.getElementById(el.pinId);
            if (!pin) continue;

            pin.addEventListener('mousedown', (e) => {
                e.preventDefault();
                e.stopPropagation();
                activeDrag = key;
                dragStartX = e.clientX;
                dragStartY = e.clientY;

                const inX = document.getElementById(el.inputX);
                const inY = document.getElementById(el.inputY);
                initialCardX = parseFloat(inX?.value) || 0;
                initialCardY = parseFloat(inY?.value) || 0;

                document.getElementById('active-pin-name').textContent = el.name;
                document.getElementById('active-pin-coords').textContent = `X: ${initialCardX}, Y: ${initialCardY}`;
            });
        }

        window.addEventListener('mousemove', (e) => {
            if (!activeDrag) return;
            const el = elements[activeDrag];
            const scaleX = container.clientWidth / CARD_CANVAS_W;
            const scaleY = container.clientHeight / CARD_CANVAS_H;

            const deltaCardX = Math.round((e.clientX - dragStartX) / scaleX);
            const deltaCardY = Math.round((e.clientY - dragStartY) / scaleY);

            const newCardX = Math.max(0, Math.min(CARD_CANVAS_W - 50, initialCardX + deltaCardX));
            const newCardY = Math.max(0, Math.min(CARD_CANVAS_H - 30, initialCardY + deltaCardY));

            const inX = document.getElementById(el.inputX);
            const inY = document.getElementById(el.inputY);
            if (inX) inX.value = newCardX;
            if (inY) inY.value = newCardY;

            document.getElementById('active-pin-coords').textContent = `X: ${newCardX}, Y: ${newCardY}`;
            updateOverlayFromInputs();
        });

        window.addEventListener('mouseup', () => {
            if (activeDrag) {
                activeDrag = null;
            }
        });

        window.addEventListener('resize', updateOverlayFromInputs);
        updateOverlayFromInputs();
    }

    // Layout presets
    function applyLayoutPreset(preset) {
        if (preset === 'default') {
            setCoord('pos_photo_x', 45); setCoord('pos_photo_y', 125); setCoord('pos_photo_w', 210); setCoord('pos_photo_h', 270);
            setCoord('pos_name_x', 285); setCoord('pos_name_y', 179);
            setCoord('pos_category_x', 285); setCoord('pos_category_y', 245);
            setCoord('pos_number_x', 285); setCoord('pos_number_y', 325);
            setCoord('pos_qr_x', 756); setCoord('pos_qr_y', 373);
            const shapeSel = document.getElementById('input_number_style');
            if (shapeSel) shapeSel.value = 'gold_badge';
            const hideBox = document.querySelector('input[name="hide_default_shapes"]');
            if (hideBox) hideBox.checked = false;
        } else if (preset === 'clean_upload') {
            setCoord('pos_photo_x', 60); setCoord('pos_photo_y', 130); setCoord('pos_photo_w', 200); setCoord('pos_photo_h', 260);
            setCoord('pos_name_x', 290); setCoord('pos_name_y', 180);
            setCoord('pos_category_x', 290); setCoord('pos_category_y', 235);
            setCoord('pos_number_x', 290); setCoord('pos_number_y', 315);
            setCoord('pos_qr_x', 740); setCoord('pos_qr_y', 360);
            const shapeSel = document.getElementById('input_number_style');
            if (shapeSel) shapeSel.value = 'gold_badge';
            const hideBox = document.querySelector('input[name="hide_default_shapes"]');
            if (hideBox) hideBox.checked = true;
        } else if (preset === 'right_photo') {
            setCoord('pos_photo_x', 740); setCoord('pos_photo_y', 130); setCoord('pos_photo_w', 210); setCoord('pos_photo_h', 270);
            setCoord('pos_name_x', 60); setCoord('pos_name_y', 180);
            setCoord('pos_category_x', 60); setCoord('pos_category_y', 240);
            setCoord('pos_number_x', 60); setCoord('pos_number_y', 320);
            setCoord('pos_qr_x', 520); setCoord('pos_qr_y', 360);
        }
        updateOverlayFromInputs();
    }

    function setCoord(id, val) {
        const input = document.getElementById('input_' + id);
        if (input) input.value = val;
    }

    function switchPreviewSide(side) {
        currentPreviewSide = side;
        const img = document.getElementById('sample-preview-img');
        const tabF = document.getElementById('preview-tab-front');
        const tabB = document.getElementById('preview-tab-back');
        const overlay = document.getElementById('interactive-overlay');

        img.src = '<?= site_url('admin/settings/kta/preview-sample/') ?>' + side + '?t=' + Date.now();

        if (side === 'front') {
            tabF.className = 'flex-1 py-2 rounded-xl text-xs font-bold bg-brand-600 text-white shadow-xs transition-colors';
            tabB.className = 'flex-1 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors';
            if (overlay) overlay.style.display = 'block';
        } else {
            tabB.className = 'flex-1 py-2 rounded-xl text-xs font-bold bg-brand-600 text-white shadow-xs transition-colors';
            tabF.className = 'flex-1 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors';
            if (overlay) overlay.style.display = 'none';
        }
    }

    function refreshPreview() {
        switchPreviewSide(currentPreviewSide);
    }

    function deleteAsset(field) {
        window.KomeoModal.confirm({
            title: 'Hapus Berkas Gambar',
            message: 'Apakah Anda yakin ingin menghapus berkas gambar aset KTA ini?',
            confirmText: 'Ya, Hapus',
            cancelText: 'Batal',
            type: 'danger',
            onConfirm: function() {
                const form = document.getElementById('delete-asset-form');
                form.action = '<?= site_url('admin/settings/kta/delete-image/') ?>' + field;
                form.submit();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        initInteractiveOverlay();
    });
</script>
<?= $this->endSection() ?>
