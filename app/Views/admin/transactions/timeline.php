<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <?= $this->include('admin/transactions/_tabs_header') ?>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="p-4 rounded-2xl bg-emerald-950/40 border border-emerald-500/30 text-emerald-200 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span><?= session()->getFlashdata('success') ?></span>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Add New Work Stage / Timeline Update Form -->
        <div class="lg:col-span-1">
            <div class="p-6 rounded-3xl bg-slate-900/80 border border-slate-800 shadow-sm sticky top-6">
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    <span>Catat Update Progres</span>
                </h2>
                <p class="text-xs text-slate-400 mt-1">
                    Setiap perubahan tahapan pekerjaan dan perkembangan aktivitas akan disimpan permanen dalam riwayat linimasa.
                </p>

                <form method="post" action="<?= base_url('admin/transactions/' . $trx['id'] . '/timeline') ?>" class="space-y-4 mt-5">
                    <?= csrf_field() ?>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">
                            Tahapan Pekerjaan Baru <span class="text-rose-400">*</span>
                        </label>
                        <select name="new_stage" required class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-brand-500">
                            <?php foreach ($workStages as $k => $v): ?>
                                <option value="<?= $k ?>" <?= ($trx['current_work_stage'] === $k) ? 'selected' : '' ?>>
                                    <?= $v ?> <?= ($trx['current_work_stage'] === $k) ? '(Tahap Saat Ini)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">
                            Deskripsi Perkembangan (Tampilan Member) <span class="text-rose-400">*</span>
                        </label>
                        <textarea name="public_description" rows="3" required placeholder="Contoh: Tim teknis telah selesai loading in peralatan LED Screen di venue..." class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-brand-500"></textarea>
                        <p class="text-[11px] text-slate-500 mt-1">Dapat dibaca oleh anggota aktif pada portal member.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">
                            Catatan Internal Admin (Rahasia)
                        </label>
                        <textarea name="internal_note" rows="2" placeholder="Catatan internal pengurus / staf teknis..." class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-brand-500"></textarea>
                        <p class="text-[11px] text-slate-500 mt-1">Hanya dapat dibaca oleh administrator.</p>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-950/70 border border-slate-800 flex items-center gap-2.5">
                        <input type="checkbox" name="visible_to_members" id="visible_to_members" value="1" checked class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 bg-slate-900 border-slate-700">
                        <label for="visible_to_members" class="text-xs text-slate-300 select-none cursor-pointer">
                            Tampilkan di Linimasa Member
                        </label>
                    </div>

                    <button type="submit" class="w-full px-4 py-2.5 rounded-xl text-xs font-bold bg-brand-600 text-white hover:bg-brand-500 transition-colors shadow-md shadow-brand-900/30">
                        Simpan Update Progres
                    </button>
                </form>
            </div>
        </div>

        <!-- Right: Immutable Timeline History List -->
        <div class="lg:col-span-2">
            <div class="p-6 rounded-3xl bg-slate-900/80 border border-slate-800 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <div>
                        <h2 class="text-base font-bold text-white">Riwayat Linimasa & Aktivitas</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Catatan perkembangan urut waktu kronologis.</p>
                    </div>
                    <span class="text-xs px-2.5 py-1 rounded-full bg-slate-800 text-slate-300 font-bold">
                        <?= count($timeline) ?> Catatan
                    </span>
                </div>

                <div class="mt-6 space-y-6">
                    <?php if (empty($timeline)): ?>
                        <div class="p-8 text-center text-slate-500 text-sm">
                            Belum ada riwayat perkembangan yang tercatat.
                        </div>
                    <?php else: ?>
                        <div class="relative pl-6 space-y-8 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-800">
                            <?php foreach ($timeline as $entry): ?>
                                <div class="relative group">
                                    <!-- Dot Marker -->
                                    <span class="absolute -left-6 top-1.5 w-4 h-4 rounded-full bg-slate-900 border-2 <?= (! empty($entry['visible_to_members'])) ? 'border-brand-500' : 'border-slate-600' ?> flex items-center justify-center">
                                        <span class="w-1.5 h-1.5 rounded-full <?= (! empty($entry['visible_to_members'])) ? 'bg-brand-400' : 'bg-slate-500' ?>"></span>
                                    </span>

                                    <!-- Content Card -->
                                    <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-2">
                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                            <div class="flex items-center gap-2">
                                                <?php if (! empty($entry['new_status'])): ?>
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-brand-500/20 text-brand-300 border border-brand-500/30">
                                                        <?= esc($entry['new_status']) ?>
                                                    </span>
                                                <?php endif; ?>
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-slate-300 uppercase">
                                                    <?= esc($entry['update_type']) ?>
                                                </span>
                                                <?php if (empty($entry['visible_to_members'])): ?>
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                                        INTERNAL ADMIN ONLY
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <span class="text-[11px] text-slate-400 font-mono">
                                                <?= date('d M Y, H:i', strtotime($entry['created_at'])) ?> WIB
                                            </span>
                                        </div>

                                        <!-- Public description -->
                                        <?php if (! empty($entry['public_description'])): ?>
                                            <p class="text-xs text-slate-200 leading-relaxed font-medium">
                                                <?= esc($entry['public_description']) ?>
                                            </p>
                                        <?php endif; ?>

                                        <!-- Internal note (admin view only) -->
                                        <?php if (! empty($entry['internal_note'])): ?>
                                            <div class="p-2.5 rounded-xl bg-slate-900 border border-slate-800 text-[11px] text-amber-200/90 font-mono">
                                                <strong class="text-amber-400 font-bold block mb-0.5">Catatan Internal:</strong>
                                                <?= esc($entry['internal_note']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
