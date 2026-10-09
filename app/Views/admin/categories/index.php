<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Kategori Industri Event</h2>
            <p class="text-xs text-slate-500 mt-1">Kelola daftar klasifikasi industri event yang dapat dipilih oleh member KOMEO.ID</p>
        </div>
        <button type="button" onclick="openCategoryModal()" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-xl transition-colors shadow-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Kategori Baru
        </button>
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

    <?php if (session()->getFlashdata('error')): ?>
        <div class="p-4 bg-red-50 border border-red-200 rounded-2xl flex items-center gap-3">
            <div class="p-1.5 bg-red-100 text-red-700 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <p class="text-xs font-bold text-red-900"><?= esc(session()->getFlashdata('error')) ?></p>
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

    <!-- Categories Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 border-b border-slate-200/80 text-slate-500 font-bold uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Urutan</th>
                        <th class="px-6 py-4">Nama Kategori</th>
                        <th class="px-6 py-4">Slug</th>
                        <th class="px-6 py-4">Member Terdaftar</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400 italic">
                                Belum ada data kategori.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($categories as $cat): ?>
                            <?php $cat = (object) $cat; ?>
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-4 font-mono font-bold text-slate-500">
                                    <?= (int) $cat->sort_order ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900"><?= esc($cat->name) ?></div>
                                    <?php if (! empty($cat->description)): ?>
                                        <div class="text-[11px] text-slate-400 mt-0.5 line-clamp-1"><?= esc($cat->description) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 font-mono text-slate-500">
                                    <?= esc($cat->slug) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold <?= (int)$cat->member_count > 0 ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-100 text-slate-500' ?>">
                                        <?= (int) $cat->member_count ?> Member
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($cat->is_active): ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Aktif
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            Nonaktif
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <button type="button" 
                                                onclick='editCategoryModal(<?= json_encode($cat) ?>)'
                                                class="px-2.5 py-1.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                                            Edit
                                        </button>
                                        
                                        <?php if ((int)$cat->member_count > 0): ?>
                                            <button type="button" 
                                                    disabled 
                                                    title="Tidak dapat dihapus karena digunakan oleh member"
                                                    class="px-2.5 py-1.5 text-xs font-bold text-slate-300 bg-slate-50 rounded-lg cursor-not-allowed">
                                                Hapus
                                            </button>
                                        <?php else: ?>
                                            <button type="button" 
                                                    onclick="confirmDeleteCategory(<?= (int) $cat->id ?>, '<?= esc($cat->name, 'js') ?>')"
                                                    class="px-2.5 py-1.5 text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition">
                                                Hapus
                                            </button>
                                        <?php endif; ?>
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

<!-- Modal Create / Edit Category -->
<div id="categoryModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs hidden">
    <div class="bg-white rounded-3xl border border-slate-200 max-w-lg w-full p-6 sm:p-8 shadow-2xl relative">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
            <div>
                <h3 id="catModalTitle" class="text-base font-extrabold text-slate-900 tracking-tight">Tambah Kategori Baru</h3>
                <p class="text-xs text-slate-500 mt-0.5">Atur nama, slug, dan urutan kategori</p>
            </div>
            <button type="button" onclick="closeCategoryModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="categoryForm" action="<?= base_url('admin/categories/save') ?>" method="post" class="space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="cat_id" value="0">

            <div>
                <label for="cat_name" class="block text-xs font-bold text-slate-800 mb-1">Nama Kategori <span class="text-red-500">*</span></label>
                <input type="text" 
                       id="cat_name" 
                       name="name" 
                       required 
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-xs text-slate-900 transition" 
                       placeholder="Contoh: Lighting & Visual">
            </div>

            <div>
                <label for="cat_slug" class="block text-xs font-bold text-slate-800 mb-1">Slug URL <span class="text-red-500">*</span></label>
                <input type="text" 
                       id="cat_slug" 
                       name="slug" 
                       required 
                       pattern="^[a-zA-Z0-9_\-]+$"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-xs font-mono text-slate-900 transition" 
                       placeholder="lighting-visual">
            </div>

            <div>
                <label for="cat_description" class="block text-xs font-bold text-slate-800 mb-1">Deskripsi Singkat</label>
                <textarea id="cat_description" 
                          name="description" 
                          rows="2" 
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-xs text-slate-900 transition" 
                          placeholder="Deskripsi ruang lingkup kategori..."></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="cat_sort_order" class="block text-xs font-bold text-slate-800 mb-1">Urutan Tampilan</label>
                    <input type="number" 
                           id="cat_sort_order" 
                           name="sort_order" 
                           value="0" 
                           required 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-xs text-slate-900 transition">
                </div>

                <div class="flex items-center pt-5">
                    <label class="relative flex items-center gap-2 cursor-pointer select-none">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="cat_is_active" value="1" checked class="h-4 w-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500">
                        <span class="text-xs font-bold text-slate-800">Status Aktif</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeCategoryModal()" class="px-4 py-2.5 text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-xl transition shadow-xs">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Form Delete Post -->
<form id="deleteCatForm" action="" method="post" class="hidden">
    <?= csrf_field() ?>
</form>

<script>
function openCategoryModal() {
    document.getElementById('catModalTitle').textContent = 'Tambah Kategori Baru';
    document.getElementById('cat_id').value = '0';
    document.getElementById('cat_name').value = '';
    document.getElementById('cat_slug').value = '';
    document.getElementById('cat_description').value = '';
    document.getElementById('cat_sort_order').value = '0';
    document.getElementById('cat_is_active').checked = true;
    document.getElementById('categoryModal').classList.remove('hidden');
}

function editCategoryModal(cat) {
    document.getElementById('catModalTitle').textContent = 'Edit Kategori Industri';
    document.getElementById('cat_id').value = cat.id;
    document.getElementById('cat_name').value = cat.name || '';
    document.getElementById('cat_slug').value = cat.slug || '';
    document.getElementById('cat_description').value = cat.description || '';
    document.getElementById('cat_sort_order').value = cat.sort_order || 0;
    document.getElementById('cat_is_active').checked = (parseInt(cat.is_active) === 1);
    document.getElementById('categoryModal').classList.remove('hidden');
}

function closeCategoryModal() {
    document.getElementById('categoryModal').classList.add('hidden');
}

function confirmDeleteCategory(id, name) {
    window.KomeoModal.confirm({
        title: 'Hapus Kategori',
        message: 'Apakah Anda yakin ingin menghapus kategori "' + name + '"?\n\nTindakan ini tidak dapat dibatalkan.',
        confirmText: 'Ya, Hapus',
        cancelText: 'Batal',
        type: 'danger',
        onConfirm: function() {
            const form = document.getElementById('deleteCatForm');
            form.action = '<?= base_url('admin/categories/delete') ?>/' + id;
            form.submit();
        }
    });
}

// Auto slug on type when creating
document.getElementById('cat_name').addEventListener('input', function() {
    const catId = parseInt(document.getElementById('cat_id').value);
    if (catId === 0) {
        const slug = this.value.toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
        document.getElementById('cat_slug').value = slug;
    }
});
</script>

<?= $this->endSection() ?>
