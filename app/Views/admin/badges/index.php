<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="space-y-6">

    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Manajemen Badge Member</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Kelola label kurasi, penghargaan khusus, dan verifikasi profil bisnis untuk anggota KOMEO.ID.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= base_url('admin/badges/create') ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs sm:text-sm font-bold shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Badge Baru
            </a>
        </div>
    </div>

    <!-- Official Verification vs Custom Badge Notice Card (Rule 5) -->
    <div class="p-5 bg-gradient-to-r from-indigo-50/80 via-slate-50 to-indigo-50/80 rounded-2xl border border-indigo-200/70 text-xs text-slate-700 flex items-start gap-3.5">
        <div class="p-2 bg-indigo-600 text-white rounded-xl shrink-0 mt-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="space-y-1">
            <strong class="text-indigo-950 font-bold block text-sm">Ketentuan Badge Kustom vs Verifikasi Resmi Keanggotaan</strong>
            <p class="leading-relaxed">
                Status keanggotaan resmi KOMEO.ID selalu ditentukan oleh tabel sistem keanggotaan (Aktif, Menunggu, Ditangguhkan). Custom badge bersifat penghargaan editorial atau label keahlian, dan <strong>tidak</strong> secara otomatis menggantikan persetujuan keanggotaan resmi atau izin administrator.
            </p>
        </div>
    </div>

    <!-- Badges Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/75 text-[11px] font-black uppercase tracking-wider text-slate-500">
                        <th class="py-4 px-6">Badge / Tampilan</th>
                        <th class="py-4 px-6">Deskripsi & Slug</th>
                        <th class="py-4 px-6 text-center">Urutan</th>
                        <th class="py-4 px-6 text-center">Penerima Aktif</th>
                        <th class="py-4 px-6 text-center">Status</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    <?php if (empty($badges)): ?>
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                Belum ada badge yang dibuat. Klik tombol "Tambah Badge Baru" di atas.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($badges as $b): ?>
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <?= komeo_render_badge($b, 'sm', false) ?>
                                        <div class="hidden sm:block text-[11px] font-mono text-slate-400">
                                            bg: <?= esc($b['background_color']) ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 max-w-xs">
                                    <p class="font-bold text-slate-900"><?= esc($b['name']) ?></p>
                                    <p class="text-slate-500 text-[11px] line-clamp-2 mt-0.5"><?= esc($b['description'] ?: 'Tidak ada deskripsi') ?></p>
                                    <span class="inline-block mt-1 font-mono text-[10px] text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded">
                                        slug: <?= esc($b['slug']) ?>
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center font-mono font-bold text-slate-700">
                                    <?= (int) $b['sort_order'] ?>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <a href="<?= base_url('admin/badges/members/' . $b['id']) ?>" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition border border-indigo-200/60">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                        <span><?= (int) ($b['active_members_count'] ?? 0) ?> Member</span>
                                    </a>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <?php if ($b['is_active']): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Aktif
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600">
                                            Nonaktif
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="<?= base_url('admin/badges/members/' . $b['id']) ?>" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-xl transition" title="Kelola Anggota Penerima">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        </a>

                                        <a href="<?= base_url('admin/badges/edit/' . $b['id']) ?>" class="p-2 text-slate-600 hover:bg-slate-100 rounded-xl transition" title="Edit Badge">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>

                                        <form action="<?= base_url('admin/badges/toggle/' . $b['id']) ?>" method="post" class="inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="p-2 <?= $b['is_active'] ? 'text-amber-600 hover:bg-amber-50' : 'text-emerald-600 hover:bg-emerald-50' ?> rounded-xl transition" title="<?= $b['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                                <?php if ($b['is_active']): ?>
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                <?php else: ?>
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                <?php endif; ?>
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

<?= $this->endSection() ?>
