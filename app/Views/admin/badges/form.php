<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?php
$isEdit = ! empty($badge);
$actionUrl = $isEdit ? base_url('admin/badges/edit/' . $badge['id']) : base_url('admin/badges/create');

$nameVal = old('name', $badge['name'] ?? '');
$descVal = old('description', $badge['description'] ?? '');
$iconVal = old('icon', $badge['icon'] ?? 'award');
$bgVal   = old('background_color', $badge['background_color'] ?? '#4F46E5');
$txtVal  = old('text_color', $badge['text_color'] ?? '#FFFFFF');
$orderVal = old('sort_order', $badge['sort_order'] ?? 0);
$activeVal = (bool) old('is_active', $badge['is_active'] ?? 1);
?>

<div class="max-w-4xl mx-auto space-y-6">

    <!-- Breadcrumb / Back Navigation -->
    <div class="flex items-center justify-between">
        <a href="<?= base_url('admin/badges') ?>" class="inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-500 hover:text-brand-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar Badge
        </a>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        
        <div class="p-6 sm:p-8 border-b border-slate-100 bg-slate-50/50">
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                <?= $isEdit ? 'Edit Konfigurasi Badge' : 'Buat Badge Anggota Baru' ?>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Atur label kustom, ikon representatif, palet warna heksadesimal, dan prioritas tampilan badge.
            </p>
        </div>

        <!-- Form -->
        <form action="<?= $actionUrl ?>" method="post" class="p-6 sm:p-8 space-y-6">
            <?= csrf_field() ?>

            <!-- Interactive Live Preview Box -->
            <div class="p-5 rounded-2xl bg-slate-900 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-1">Pratinjau Langsung (Live Preview):</span>
                    <div id="liveBadgePreview" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold shadow-md transition-all" style="background-color: <?= esc($bgVal) ?>; color: <?= esc($txtVal) ?>;">
                        <span id="previewIconWrap"><?= komeo_badge_icon($iconVal, 'w-3.5 h-3.5') ?></span>
                        <span id="previewName"><?= esc($nameVal ?: 'Nama Badge') ?></span>
                    </div>
                </div>
                <div class="text-[11px] text-slate-400 max-w-xs leading-relaxed">
                    Tampilan pill ini akan langsung terpasang pada kartu direktori, profil publik, dan dashboard anggota penerima.
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Badge Name -->
                <div class="md:col-span-2 space-y-1.5">
                    <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Nama Badge <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="<?= esc($nameVal) ?>" 
                           required 
                           maxlength="100" 
                           placeholder="Contoh: Tergercep, Top Vendor, Event Expert"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-hidden transition">
                    <p class="text-[11px] text-slate-400">Nama badge kustom dapat disesuaikan tanpa batasan label tetap.</p>
                </div>

                <!-- Description -->
                <div class="md:col-span-2 space-y-1.5">
                    <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Deskripsi / Keterangan Penghargaan
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="2" 
                              maxlength="500" 
                              placeholder="Kriteria atau alasan penghargaan badge ini diberikan kepada anggota..."
                              class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-hidden transition"><?= esc($descVal) ?></textarea>
                </div>

                <!-- Icon Selection -->
                <div class="space-y-1.5">
                    <label for="icon" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Pilihan Ikon Lucide <span class="text-rose-500">*</span>
                    </label>
                    <select id="icon" 
                            name="icon" 
                            required 
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-hidden transition bg-white">
                        <?php foreach ($allowedIcons as $iconKey => $iconLabel): ?>
                            <option value="<?= esc($iconKey) ?>" <?= $iconVal === $iconKey ? 'selected' : '' ?>>
                                <?= esc($iconLabel) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Sort Order -->
                <div class="space-y-1.5">
                    <label for="sort_order" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Prioritas Urutan Tampilan
                    </label>
                    <input type="number" 
                           id="sort_order" 
                           name="sort_order" 
                           value="<?= (int) $orderVal ?>" 
                           min="0" 
                           step="1" 
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-hidden transition">
                    <p class="text-[11px] text-slate-400">Angka lebih kecil (misal 1) akan ditampilkan lebih dulu.</p>
                </div>

                <!-- Background Color Hex -->
                <div class="space-y-1.5">
                    <label for="background_color" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Warna Background (Hex) <span class="text-rose-500">*</span>
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="color" 
                               id="bgColorPicker" 
                               value="<?= esc($bgVal) ?>" 
                               class="w-11 h-11 p-1 rounded-xl border border-slate-200 cursor-pointer bg-white shrink-0">
                        <input type="text" 
                               id="background_color" 
                               name="background_color" 
                               value="<?= esc($bgVal) ?>" 
                               required 
                               pattern="^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$" 
                               placeholder="#4F46E5"
                               class="w-full font-mono uppercase px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-hidden transition">
                    </div>
                </div>

                <!-- Text Color Hex -->
                <div class="space-y-1.5">
                    <label for="text_color" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Warna Teks (Hex) <span class="text-rose-500">*</span>
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="color" 
                               id="txtColorPicker" 
                               value="<?= esc($txtVal) ?>" 
                               class="w-11 h-11 p-1 rounded-xl border border-slate-200 cursor-pointer bg-white shrink-0">
                        <input type="text" 
                               id="text_color" 
                               name="text_color" 
                               value="<?= esc($txtVal) ?>" 
                               required 
                               pattern="^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$" 
                               placeholder="#FFFFFF"
                               class="w-full font-mono uppercase px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-hidden transition">
                    </div>
                </div>

                <!-- Active Status -->
                <div class="md:col-span-2 pt-2">
                    <label class="flex items-center gap-3 cursor-pointer select-none">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1" 
                               <?= $activeVal ? 'checked' : '' ?> 
                               class="w-5 h-5 rounded-md text-brand-600 focus:ring-brand-500 border-slate-300">
                        <span class="text-xs sm:text-sm font-bold text-slate-800">
                            Aktifkan badge ini sekarang (dapat langsung disematkan ke anggota)
                        </span>
                    </label>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="<?= base_url('admin/badges') ?>" class="px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-slate-600 hover:bg-slate-100 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs sm:text-sm font-bold shadow-md transition">
                    <?= $isEdit ? 'Simpan Perubahan' : 'Buat Badge' ?>
                </button>
            </div>
        </form>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const bgInput   = document.getElementById('background_color');
    const bgPicker  = document.getElementById('bgColorPicker');
    const txtInput  = document.getElementById('text_color');
    const txtPicker = document.getElementById('txtColorPicker');
    const liveBadge = document.getElementById('liveBadgePreview');
    const prevName  = document.getElementById('previewName');

    function updatePreview() {
        prevName.textContent = nameInput.value.trim() || 'Nama Badge';
        liveBadge.style.backgroundColor = bgInput.value;
        liveBadge.style.color = txtInput.value;
    }

    nameInput.addEventListener('input', updatePreview);

    bgPicker.addEventListener('input', function() {
        bgInput.value = bgPicker.value.toUpperCase();
        updatePreview();
    });
    bgInput.addEventListener('input', function() {
        if (/^#[0-9A-Fa-f]{6}$/.test(bgInput.value)) {
            bgPicker.value = bgInput.value;
        }
        updatePreview();
    });

    txtPicker.addEventListener('input', function() {
        txtInput.value = txtPicker.value.toUpperCase();
        updatePreview();
    });
    txtInput.addEventListener('input', function() {
        if (/^#[0-9A-Fa-f]{6}$/.test(txtInput.value)) {
            txtPicker.value = txtInput.value;
        }
        updatePreview();
    });
});
</script>

<?= $this->endSection() ?>
