<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<div class="max-w-6xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-semibold text-brand-600">Karya & Rekam Jejak</span>
                <span class="text-slate-300">•</span>
                <span class="text-xs font-semibold text-slate-500">Maks. <?= $maxLimit ?> Proyek Event</span>
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Portofolio Event</h2>
            <p class="text-xs text-slate-500 mt-1">Tampilkan dokumentasi event terbaik Anda untuk meyakinkan calon klien dan mitra kolaborasi.</p>
        </div>

        <div class="flex items-center gap-3">
            <div class="hidden sm:flex flex-col items-end">
                <span class="text-xs font-bold text-slate-700"><?= $totalCount ?> / <?= $maxLimit ?> Slot Digunakan</span>
                <div class="w-28 bg-slate-100 rounded-full h-2 mt-1 overflow-hidden">
                    <div class="bg-brand-600 h-2 rounded-full" style="width: <?= min(100, round(($totalCount / $maxLimit) * 100)) ?>%"></div>
                </div>
            </div>

            <?php if ($totalCount < $maxLimit): ?>
                <button type="button" onclick="openPortfolioModal()" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-xl transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Portofolio
                </button>
            <?php else: ?>
                <button type="button" disabled class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-slate-400 bg-slate-100 rounded-xl cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Batas Maksimal Penuh (<?= $maxLimit ?>)
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Alert Notifications -->
    <?php if (session()->getFlashdata('message') || session()->getFlashdata('success')): ?>
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-start gap-3">
            <div class="p-1.5 bg-emerald-100 text-emerald-700 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-emerald-900"><?= esc(session()->getFlashdata('message') ?? session()->getFlashdata('success')) ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="p-4 bg-red-50 border border-red-200 rounded-2xl flex items-start gap-3">
            <div class="p-1.5 bg-red-100 text-red-700 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-red-900"><?= esc(session()->getFlashdata('error')) ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($errors = session()->getFlashdata('errors')): ?>
        <div class="p-4 bg-red-50 border border-red-200 rounded-2xl space-y-1">
            <div class="flex items-center gap-2 text-sm font-bold text-red-900 mb-2">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Periksa kembali isian formulir:
            </div>
            <ul class="list-disc list-inside text-xs text-red-700 space-y-1">
                <?php foreach ($errors as $errorMsg): ?>
                    <li><?= esc($errorMsg) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Portfolio Grid Cards -->
    <?php if (empty($portfolios)): ?>
        <div class="bg-white rounded-3xl border border-dashed border-slate-300 p-12 text-center">
            <div class="w-16 h-16 bg-brand-50 text-brand-600 rounded-3xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="text-base font-bold text-slate-800">Belum Ada Portofolio Ditambahkan</h3>
            <p class="text-xs text-slate-500 max-w-md mx-auto mt-1 mb-6">
                Unggah dokumentasi foto event atau proyek produksi yang pernah Anda kerjakan untuk meningkatkan kredibilitas Anda di KOMEO.ID.
            </p>
            <button type="button" onclick="openPortfolioModal()" class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-xl transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Mulai Tambah Portofolio Pertama
            </button>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($portfolios as $item): ?>
                <div class="bg-white rounded-3xl border border-slate-200/80 overflow-hidden shadow-xs hover:shadow-md transition group flex flex-col justify-between">
                    <div>
                        <!-- Cover Image -->
                        <div class="h-48 w-full bg-slate-100 overflow-hidden relative">
                            <?php if (! empty($item['cover_image']) && file_exists(FCPATH . $item['cover_image'])): ?>
                                <img src="<?= base_url(esc($item['cover_image'])) ?>" 
                                     alt="<?= esc($item['title']) ?>" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-400">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            <?php endif; ?>

                            <!-- Year badge -->
                            <span class="absolute top-3 left-3 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-slate-900/80 backdrop-blur-md text-white shadow-xs">
                                <?= esc($item['project_year']) ?>
                            </span>
                        </div>

                        <!-- Content -->
                        <div class="p-5 space-y-3">
                            <h4 class="text-sm font-extrabold text-slate-900 group-hover:text-brand-600 transition line-clamp-1">
                                <?= esc($item['title']) ?>
                            </h4>

                            <?php if (! empty($item['event_location'])): ?>
                                <div class="flex items-center gap-1.5 text-xs text-slate-500 font-medium">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span class="truncate"><?= esc($item['event_location']) ?></span>
                                </div>
                            <?php endif; ?>

                            <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed">
                                <?= nl2br(esc($item['description'])) ?>
                            </p>

                            <?php if (! empty($item['external_url'])): ?>
                                <div class="pt-1">
                                    <a href="<?= esc($item['external_url']) ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-[11px] font-bold text-brand-600 hover:text-brand-700">
                                        <span>Lihat Liputan / Tautan</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div class="px-5 py-3.5 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between gap-2">
                        <span class="text-[10px] text-slate-400 font-medium">
                            Urutan: <?= (int) $item['sort_order'] ?>
                        </span>

                        <div class="flex items-center gap-2">
                            <button type="button" 
                                    onclick='editPortfolioModal(<?= json_encode($item) ?>)' 
                                    class="px-2.5 py-1.5 text-[11px] font-bold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg transition">
                                Edit
                            </button>
                            <button type="button" 
                                    onclick="confirmDeletePortfolio(<?= (int) $item['id'] ?>, '<?= esc($item['title'], 'js') ?>')" 
                                    class="px-2.5 py-1.5 text-[11px] font-bold text-red-600 bg-white hover:bg-red-50 border border-red-200 rounded-lg transition">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Add / Edit Portfolio Modal -->
<div id="portfolioModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs hidden">
    <div class="bg-white rounded-3xl border border-slate-200 max-w-xl w-full max-h-[90vh] overflow-y-auto p-6 sm:p-8 shadow-2xl relative">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
            <div>
                <h3 id="modalTitle" class="text-lg font-extrabold text-slate-900 tracking-tight">Tambah Proyek Portofolio</h3>
                <p class="text-xs text-slate-500 mt-0.5">Maksimal 6 karya proyek event per anggota</p>
            </div>
            <button type="button" onclick="closePortfolioModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="portfolioForm" action="<?= base_url('dashboard/portofolio/save') ?>" method="post" enctype="multipart/form-data" class="space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="portfolio_id" value="0">

            <!-- Judul Proyek -->
            <div>
                <label for="modal_title" class="block text-xs font-bold text-slate-800 mb-1">Judul Proyek / Nama Event <span class="text-red-500">*</span></label>
                <input type="text" 
                       id="modal_title" 
                       name="title" 
                       required 
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-xs text-slate-900 transition" 
                       placeholder="Contoh: Sound & Lighting Konser Musik Mahakarya 2025">
            </div>

            <!-- Grid Tahun & Lokasi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="modal_project_year" class="block text-xs font-bold text-slate-800 mb-1">Tahun Proyek <span class="text-red-500">*</span></label>
                    <input type="text" 
                           id="modal_project_year" 
                           name="project_year" 
                           required 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-xs text-slate-900 transition" 
                           placeholder="Contoh: 2025 atau 2024-2025">
                </div>

                <div>
                    <label for="modal_event_location" class="block text-xs font-bold text-slate-800 mb-1">Lokasi Event</label>
                    <input type="text" 
                           id="modal_event_location" 
                           name="event_location" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-xs text-slate-900 transition" 
                           placeholder="Contoh: Gelora Bung Karno, Jakarta">
                </div>
            </div>

            <!-- Deskripsi Proyek -->
            <div>
                <label for="modal_description" class="block text-xs font-bold text-slate-800 mb-1">Deskripsi Proyek & Peran Anda <span class="text-red-500">*</span></label>
                <textarea id="modal_description" 
                          name="description" 
                          rows="4" 
                          required 
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-xs text-slate-900 transition" 
                          placeholder="Jelaskan peran Anda, peralatan atau tim yang digunakan, serta keberhasilan jalannya acara..."></textarea>
            </div>

            <!-- Upload Cover Image -->
            <div>
                <label class="block text-xs font-bold text-slate-800 mb-1">
                    Gambar Sampul <span id="coverImageRequiredMark" class="text-red-500">*</span> 
                    <span class="text-slate-400 font-normal">(Maks. 3MB - JPG, PNG, WEBP)</span>
                </label>
                <div class="flex items-center gap-4">
                    <img id="modalCoverPreview" 
                         src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='80' height='60' fill='none' stroke='%2394a3b8' stroke-width='1.5' viewBox='0 0 24 24'><rect width='20' height='16' x='2' y='4' rx='3'/><circle cx='8' cy='10' r='2'/><path d='m21 15-5-5L5 20'/></svg>" 
                         alt="Preview" 
                         class="w-20 h-16 rounded-xl object-cover border border-slate-200 bg-slate-50">
                    <div class="flex-1">
                        <input type="file" 
                               name="cover_image" 
                               id="modalCoverInput" 
                               accept="image/png,image/jpeg,image/webp" 
                               class="block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 cursor-pointer">
                        <p id="coverImageHint" class="text-[10px] text-slate-400 mt-1">Pilih foto dokumentasi yang menarik dengan resolusi tajam.</p>
                    </div>
                </div>
            </div>

            <!-- External URL & Sort Order -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label for="modal_external_url" class="block text-xs font-bold text-slate-800 mb-1">Tautan Eksternal / Video / Liputan</label>
                    <input type="url" 
                           id="modal_external_url" 
                           name="external_url" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-xs text-slate-900 transition" 
                           placeholder="https://youtube.com/watch?v=...">
                </div>

                <div>
                    <label for="modal_sort_order" class="block text-xs font-bold text-slate-800 mb-1">Nomor Urut</label>
                    <input type="number" 
                           id="modal_sort_order" 
                           name="sort_order" 
                           value="0" 
                           min="0" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-xs text-slate-900 transition">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="closePortfolioModal()" class="px-4 py-2.5 text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-xl transition shadow-sm">
                    Simpan Portofolio
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal Form -->
<form id="deletePortfolioForm" action="" method="post" class="hidden">
    <?= csrf_field() ?>
</form>

<script>
function openPortfolioModal() {
    document.getElementById('modalTitle').textContent = 'Tambah Proyek Portofolio';
    document.getElementById('portfolio_id').value = '0';
    document.getElementById('modal_title').value = '';
    document.getElementById('modal_project_year').value = '';
    document.getElementById('modal_event_location').value = '';
    document.getElementById('modal_description').value = '';
    document.getElementById('modal_external_url').value = '';
    document.getElementById('modal_sort_order').value = '0';
    document.getElementById('modalCoverInput').value = '';
    document.getElementById('modalCoverInput').setAttribute('required', 'required');
    document.getElementById('coverImageRequiredMark').classList.remove('hidden');
    document.getElementById('coverImageHint').textContent = 'Pilih foto dokumentasi yang menarik dengan resolusi tajam.';
    document.getElementById('modalCoverPreview').src = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='80' height='60' fill='none' stroke='%2394a3b8' stroke-width='1.5' viewBox='0 0 24 24'><rect width='20' height='16' x='2' y='4' rx='3'/><circle cx='8' cy='10' r='2'/><path d='m21 15-5-5L5 20'/></svg>";
    document.getElementById('portfolioModal').classList.remove('hidden');
}

function editPortfolioModal(item) {
    document.getElementById('modalTitle').textContent = 'Edit Proyek Portofolio';
    document.getElementById('portfolio_id').value = item.id;
    document.getElementById('modal_title').value = item.title || '';
    document.getElementById('modal_project_year').value = item.project_year || '';
    document.getElementById('modal_event_location').value = item.event_location || '';
    document.getElementById('modal_description').value = item.description || '';
    document.getElementById('modal_external_url').value = item.external_url || '';
    document.getElementById('modal_sort_order').value = item.sort_order || 0;
    
    document.getElementById('modalCoverInput').value = '';
    document.getElementById('modalCoverInput').removeAttribute('required');
    document.getElementById('coverImageRequiredMark').classList.add('hidden');
    document.getElementById('coverImageHint').textContent = 'Kosongkan jika tidak ingin mengubah gambar sampul.';

    if (item.cover_image) {
        document.getElementById('modalCoverPreview').src = '<?= base_url() ?>' + item.cover_image;
    } else {
        document.getElementById('modalCoverPreview').src = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='80' height='60' fill='none' stroke='%2394a3b8' stroke-width='1.5' viewBox='0 0 24 24'><rect width='20' height='16' x='2' y='4' rx='3'/><circle cx='8' cy='10' r='2'/><path d='m21 15-5-5L5 20'/></svg>";
    }

    document.getElementById('portfolioModal').classList.remove('hidden');
}

function closePortfolioModal() {
    document.getElementById('portfolioModal').classList.add('hidden');
}

function confirmDeletePortfolio(id, title) {
    window.KomeoModal.confirm({
        title: 'Hapus Portofolio',
        message: 'Apakah Anda yakin ingin menghapus portofolio "' + title + '"?\n\nTindakan ini tidak dapat dibatalkan.',
        confirmText: 'Ya, Hapus',
        cancelText: 'Batal',
        type: 'danger',
        onConfirm: function() {
            const form = document.getElementById('deletePortfolioForm');
            form.action = '<?= base_url('dashboard/portofolio/delete') ?>/' + id;
            form.submit();
        }
    });
}

// Live preview in modal
document.getElementById('modalCoverInput').addEventListener('change', function () {
    const file = this.files[0];
    if (file) {
        if (file.size > 3145728) {
            window.KomeoModal.alert({
                title: 'Ukuran Berkas Terlalu Besar',
                message: 'Ukuran gambar sampul melebihi batas maksimal 3MB! Silakan pilih berkas lain yang lebih kecil.',
                type: 'warning'
            });
            this.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('modalCoverPreview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});
</script>

<?= $this->endSection() ?>
