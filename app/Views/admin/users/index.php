<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Manajemen Role & Pengguna</h2>
            <p class="text-sm text-slate-500 mt-1">Atur hak akses staf dan peran khusus (Admin, Moderator, Editor Konten) untuk membantu mengelola website KOMEO.ID</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span>Total <?= count($users) ?> Pengguna Terdaftar</span>
            </span>
        </div>
    </div>

    <!-- Role Explanation Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-xl bg-purple-50/70 border border-purple-200/60">
            <div class="flex items-center gap-2 text-purple-800 font-bold text-xs mb-1">
                <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                <span>Super Admin</span>
            </div>
            <p class="text-[11px] text-purple-700 leading-relaxed">Akses tanpa batas ke seluruh konfigurasi, database, keuangan, dan pengaturan keamanan.</p>
        </div>
        <div class="p-4 rounded-xl bg-indigo-50/70 border border-indigo-200/60">
            <div class="flex items-center gap-2 text-indigo-800 font-bold text-xs mb-1">
                <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                <span>Admin</span>
            </div>
            <p class="text-[11px] text-indigo-700 leading-relaxed">Mengelola anggota, operasional harian, kartu KTA, kategori industri, dan pengaturan dasar.</p>
        </div>
        <div class="p-4 rounded-xl bg-amber-50/70 border border-amber-200/60">
            <div class="flex items-center gap-2 text-amber-800 font-bold text-xs mb-1">
                <span class="w-2 h-2 rounded-full bg-amber-600"></span>
                <span>Moderator</span>
            </div>
            <p class="text-[11px] text-amber-700 leading-relaxed">Membantu verifikasi berkas anggota, moderasi profil direktori, dan input daftar blacklist.</p>
        </div>
        <div class="p-4 rounded-xl bg-emerald-50/70 border border-emerald-200/60">
            <div class="flex items-center gap-2 text-emerald-800 font-bold text-xs mb-1">
                <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                <span>Editor Konten</span>
            </div>
            <p class="text-[11px] text-emerald-700 leading-relaxed">Mengelola tampilan beranda, banner promosi, teks footer, dan pengumuman komunitas.</p>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-3">
        <form action="<?= base_url('admin/users') ?>" method="get" class="w-full sm:w-80 flex items-center gap-2">
            <input type="text" name="q" value="<?= esc($search) ?>" placeholder="Cari nama, email, username..." class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500">
            <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-xl transition">Cari</button>
            <?php if (! empty($search)): ?>
                <a href="<?= base_url('admin/users') ?>" class="px-2.5 py-2 text-xs text-slate-500 hover:text-slate-800">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-bold uppercase tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="py-3.5 px-4">Pengguna</th>
                        <th class="py-3.5 px-4">Email</th>
                        <th class="py-3.5 px-4">Status Akun</th>
                        <th class="py-3.5 px-4">Peran Saat Ini (Role)</th>
                        <th class="py-3.5 px-4 text-right">Ubah Peran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($users as $u): ?>
                        <tr class="hover:bg-slate-50/75 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900"><?= esc($u['full_name'] ?: $u['username']) ?></div>
                                <div class="text-[11px] font-mono text-slate-400">@<?= esc($u['username']) ?> (ID: <?= $u['id'] ?>)</div>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                <?= esc($u['email'] ?: '-') ?>
                            </td>
                            <td class="py-3.5 px-4">
                                <?php if ($u['active']): ?>
                                    <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif</span>
                                <?php else: ?>
                                    <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex flex-wrap gap-1">
                                    <?php foreach ($u['groups'] as $grp): ?>
                                        <?php
                                        $badgeColor = match($grp) {
                                            'superadmin' => 'bg-purple-100 text-purple-800 border-purple-200',
                                            'admin'      => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                            'moderator'  => 'bg-amber-100 text-amber-800 border-amber-200',
                                            'editor'     => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                            default      => 'bg-slate-100 text-slate-700 border-slate-200',
                                        };
                                        ?>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold border <?= $badgeColor ?>">
                                            <?= strtoupper($grp) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <form action="<?= base_url('admin/users/role/' . $u['id']) ?>" method="post" class="inline-flex items-center gap-1.5">
                                    <?= csrf_field() ?>
                                    <select name="role" class="px-2.5 py-1 text-xs rounded-lg border border-slate-200 bg-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                                        <?php foreach ($availableRoles as $roleKey => $roleLabel): ?>
                                            <option value="<?= $roleKey ?>" <?= in_array($roleKey, $u['groups'], true) ? 'selected' : '' ?>>
                                                <?= esc($roleLabel) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="px-3 py-1 rounded-lg text-xs font-bold text-white bg-slate-800 hover:bg-brand-600 transition shadow-2xs">
                                        Simpan
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
