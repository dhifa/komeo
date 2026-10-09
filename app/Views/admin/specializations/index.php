<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Keahlian Spesialisasi Event</h2>
            <p class="text-xs text-slate-500 mt-1">Kelola daftar keahlian profesional dan peran teknis yang dapat dipilih oleh para member (maks. 5 per member)</p>
        </div>
        <button type="button" onclick="openSpecModal()" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-xl transition-colors shadow-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Spesialisasi Baru
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

    <!-- Specializations Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 border-b border-slate-200/80 text-slate-500 font-bold uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Urutan</th>
                        <th class="px-6 py-4">Nama Keahlian</th>
                        <th class="px-6 py-4">Slug</th>
                        <th class="px-6 py-4">Grup Kategori</th>
                        <th class="px-6 py-4">Member Pemilih</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($specializations)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-400 italic">
                                Belum ada data spesialisasi keahlian.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($specializations as $spec): ?>
                            <?php $spec = (object) $spec; ?>
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-4 font-mono font-bold text-slate-500">
                                    <?= (int) $spec->sort_order ?>
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-900">
                                    <?= esc($spec->name) ?>
                                </td>
                                <td class="px-6 py-4 font-mono text-slate-500">
                                    <?= esc($spec->slug) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if (! empty($spec->category_name)): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-bold bg-brand-50 text-brand-700 border border-brand-100">
                                            <?= esc($spec->category_name) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-slate-400 italic">Umum / Semua</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold <?= (int)$spec->member_count > 0 ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-100 text-slate-500' ?>">
                                        <?= (int) $spec->member_count ?> Member
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($spec->is_active): ?>
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
                                                onclick='editSpecModal(<?= json_encode($spec) ?>)'
                                                class="px-2.5 py-1.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                                            Edit
                                        </button>
                                        
                                        <?php if ((int)$spec->member_count > 0): ?>
                                            <button type="button" 
                                                    disabled 
                                                    title="Tidak dapat dihapus karena telah dipilih oleh member"
                                                    class="px-2.5 py-1.5 text-xs font-bold text-slate-300 bg-slate-50 rounded-lg cursor-not-allowed">
                                                Hapus
                                            </button>
                                        <?php else: ?>
                                            <button type="button" 
                                                    onclick="confirmDeleteSpec(<?= (int) $spec->id ?>, '<?= esc($spec->name, 'js') ?>')"
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

<!-- Modal Create / Edit Specialization -->
<div id="specModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs hidden">
    <div class="bg-white rounded-3xl border border-slate-200 max-w-lg w-full p-6 sm:p-8 shadow-2xl relative">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
            <div>
                <h3 id="specModalTitle" class="text-base font-extrabold text-slate-900 tracking-tight">Tambah Spesialisasi Baru</h3>
                <p class="text-xs text-slate-500 mt-0.5">Daftarkan jenis profesi atau peran teknis event baru</p>
            </div>
            <button type="button" onclick="closeSpecModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="specForm" action="<?= base_url('admin/specializations/save') ?>" method="post" class="space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="spec_id" value="0">

            <div>
                <label for="spec_name" class="block text-xs font-bold text-slate-800 mb-1">Nama Keahlian Spesialisasi <span class="text-red-500">*</span></label>
                <input type="text" 
                       id="spec_name" 
                       name="name" 
                       required 
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-xs text-slate-900 transition" 
                       placeholder="Contoh: vMix Operator / Lighting Director">
            </div>

            <div>
                <label for="spec_slug" class="block text-xs font-bold text-slate-800 mb-1">Slug URL <span class="text-red-500">*</span></label>
                <input type="text" 
                       id="spec_slug" 
                       name="slug" 
                       required 
                       pattern="^[a-zA-Z0-9_\-]+$"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-xs font-mono text-slate-900 transition" 
                       placeholder="vmix-operator">
            </div>

            <div>
                <label for="spec_category_id" class="block text-xs font-bold text-slate-800 mb-1">Hubungkan ke Kategori (Opsional)</label>
                <select id="spec_category_id" 
                        name="category_id" 
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-xs text-slate-900 transition">
                    <option value="">-- Umum / Semua Kategori --</option>
                    <?php foreach ($categories as $c): ?>
                        <?php $c = (object) $c; ?>
                        <option value="<?= $c->id ?>"><?= esc($c->name) ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="text-[10px] text-slate-400 mt-1">Dapat dikelompokkan ke kategori industri terkait atau dibiarkan umum.</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="spec_sort_order" class="block text-xs font-bold text-slate-800 mb-1">Urutan Tampilan</label>
                    <input type="number" 
                           id="spec_sort_order" 
                           name="sort_order" 
                           value="0" 
                           required 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-xs text-slate-900 transition">
                </div>

                <div class="flex items-center pt-5">
                    <label class="relative flex items-center gap-2 cursor-pointer select-none">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="spec_is_active" value="1" checked class="h-4 w-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500">
                        <span class="text-xs font-bold text-slate-800">Status Aktif</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeSpecModal()" class="px-4 py-2.5 text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-xl transition shadow-xs">
                    Simpan Spesialisasi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Form Delete Post -->
<form id="deleteSpecForm" action="" method="post" class="hidden">
    <?= csrf_field() ?>
</form>

<script>
function openSpecModal() {
    document.getElementById('specModalTitle').textContent = 'Tambah Spesialisasi Baru';
    document.getElementById('spec_id').value = '0';
    document.getElementById('spec_name').value = '';
    document.getElementById('spec_slug').value = '';
    document.getElementById('spec_category_id').value = '';
    document.getElementById('spec_sort_order').value = '0';
    document.getElementById('spec_is_active').checked = true;
    document.getElementById('specModal').classList.remove('hidden');
}

function editSpecModal(spec) {
    document.getElementById('specModalTitle').textContent = 'Edit Keahlian Spesialisasi';
    document.getElementById('spec_id').value = spec.id;
    document.getElementById('spec_name').value = spec.name || '';
    document.getElementById('spec_slug').value = spec.slug || '';
    document.getElementById('spec_category_id').value = spec.category_id || '';
    document.getElementById('spec_sort_order').value = spec.sort_order || 0;
    document.getElementById('spec_is_active').checked = (parseInt(spec.is_active) === 1);
    document.getElementById('specModal').classList.remove('hidden');
}

function closeSpecModal() {
    document.getElementById('specModal').classList.add('hidden');
}

function confirmDeleteSpec(id, name) {
    window.KomeoModal.confirm({
        title: 'Hapus Spesialisasi',
        message: 'Apakah Anda yakin ingin menghapus spesialisasi "' + name + '"?\n\nTindakan ini tidak dapat dibatalkan.',
        confirmText: 'Ya, Hapus',
        cancelText: 'Batal',
        type: 'danger',
        onConfirm: function() {
            const form = document.getElementById('deleteSpecForm');
            form.action = '<?= base_url('admin/specializations/delete') ?>/' + id;
            form.submit();
        }
    });
}

// Auto slug on type when creating
document.getElementById('spec_name').addEventListener('input', function() {
    const specId = parseInt(document.getElementById('spec_id').value);
    if (specId === 0) {
        const slug = this.value.toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
        document.getElementById('spec_slug').value = slug;
    }
});
</script>

<?= $this->endSection() ?>
