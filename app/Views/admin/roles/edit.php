<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div>
        <a href="<?= base_url('admin/member-roles') ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 hover:text-white transition-colors mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar Role
        </a>
        <h1 class="text-2xl font-black tracking-tight text-white flex items-center gap-3">
            <span>Edit Role: <?= esc($role['name']) ?></span>
            <?= komeo_render_member_role($role, 'sm', false) ?>
        </h1>
        <p class="text-sm text-slate-400 mt-1">
            Perubahan nama dan tampilan role akan langsung terupdate di seluruh profil dan direktori tanpa mengubah riwayat penugasan.
        </p>
    </div>

    <!-- Errors -->
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="p-4 rounded-2xl bg-rose-950/40 border border-rose-500/30 text-rose-200 text-sm space-y-1">
            <div class="font-bold">Periksa input formulir:</div>
            <ul class="list-disc list-inside text-xs">
                <?php foreach (session()->getFlashdata('errors') as $e): ?>
                    <li><?= esc($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Form Card -->
    <div class="p-6 rounded-3xl bg-slate-900/80 border border-slate-800 shadow-sm">
        <form method="post" action="<?= base_url('admin/member-roles/' . $role['id'] . '/edit') ?>" class="space-y-5">
            <?= csrf_field() ?>

            <!-- Fixed Stable Key -->
            <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-850 flex items-center justify-between">
                <div>
                    <span class="text-xs text-slate-400 font-semibold block">Kunci Permanen (Role Key)</span>
                    <span class="font-mono text-sm font-bold text-brand-300 mt-0.5 block"><?= esc($role['role_key']) ?></span>
                </div>
                <span class="text-[10px] text-slate-500 italic">ID Terikat #<?= $role['id'] ?></span>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1.5">
                    Nama Role (Display Name) <span class="text-rose-400">*</span>
                </label>
                <input type="text" name="name" value="<?= esc(old('name', $role['name'])) ?>" required class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-brand-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1.5">
                    Deskripsi Ringkas Peran
                </label>
                <textarea name="description" rows="2" class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-brand-500"><?= esc(old('description', $role['description'])) ?></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">
                        Warna Latar (Hex) <span class="text-rose-400">*</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="color" id="bgPicker" value="<?= old('background_color', $role['background_color']) ?>" class="w-10 h-10 rounded-xl bg-transparent border-0 cursor-pointer">
                        <input type="text" name="background_color" id="bgInput" value="<?= esc(old('background_color', $role['background_color'])) ?>" required class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white font-mono focus:outline-none focus:border-brand-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">
                        Warna Teks (Hex) <span class="text-rose-400">*</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="color" id="textPicker" value="<?= old('text_color', $role['text_color']) ?>" class="w-10 h-10 rounded-xl bg-transparent border-0 cursor-pointer">
                        <input type="text" name="text_color" id="textInput" value="<?= esc(old('text_color', $role['text_color'])) ?>" required class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white font-mono focus:outline-none focus:border-brand-500">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">
                        Urutan Tampilan (Sort Order)
                    </label>
                    <input type="number" name="sort_order" value="<?= esc(old('sort_order', $role['sort_order'])) ?>" class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-brand-500">
                </div>

                <div class="space-y-3 pt-6">
                    <label class="flex items-center gap-2.5 text-xs text-slate-300 cursor-pointer">
                        <input type="checkbox" name="is_public" value="1" <?= old('is_public', $role['is_public']) ? 'checked' : '' ?> class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 bg-slate-900 border-slate-700">
                        <span class="font-bold">Tampilkan di Profil Publik & Direktori</span>
                    </label>

                    <label class="flex items-center gap-2.5 text-xs text-slate-300 cursor-pointer">
                        <input type="checkbox" name="is_default" value="1" <?= old('is_default', $role['is_default']) ? 'checked' : '' ?> class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 bg-slate-900 border-slate-700">
                        <span class="font-bold text-amber-300">Jadikan Role Bawaan (Default)</span>
                    </label>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-800">
                <a href="<?= base_url('admin/member-roles') ?>" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-400 hover:text-white bg-slate-800">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold bg-brand-600 text-white hover:bg-brand-500 transition-colors shadow-md shadow-brand-900/30">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('bgPicker').addEventListener('input', function(e) {
    document.getElementById('bgInput').value = e.target.value.toUpperCase();
});
document.getElementById('bgInput').addEventListener('input', function(e) {
    if (e.target.value.match(/^#[a-fA-F0-9]{6}$/)) {
        document.getElementById('bgPicker').value = e.target.value;
    }
});
document.getElementById('textPicker').addEventListener('input', function(e) {
    document.getElementById('textInput').value = e.target.value.toUpperCase();
});
document.getElementById('textInput').addEventListener('input', function(e) {
    if (e.target.value.match(/^#[a-fA-F0-9]{6}$/)) {
        document.getElementById('textPicker').value = e.target.value;
    }
});
</script>
<?= $this->endSection() ?>
