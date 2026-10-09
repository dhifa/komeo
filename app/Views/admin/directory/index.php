<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="space-y-6">

    <!-- Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Manajemen Direktori Komunitas</h2>
            <p class="text-xs text-slate-500 mt-1">Kelola visibilitas, status sorotan (featured), dan direktori profesional event KOMEO.ID</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="<?= base_url('admin/settings/directory') ?>" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Pengaturan Direktori</span>
            </a>
            <a href="<?= base_url('admin/categories') ?>" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                <span>Kategori Industri</span>
            </a>
            <a href="<?= base_url('member') ?>" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-xl transition shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Lihat Direktori Publik</span>
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

    <?php if (session()->getFlashdata('error')): ?>
        <div class="p-4 bg-red-50 border border-red-200 rounded-2xl flex items-center gap-3">
            <div class="p-1.5 bg-red-100 text-red-700 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <p class="text-xs font-bold text-red-900"><?= esc(session()->getFlashdata('error')) ?></p>
        </div>
    <?php endif; ?>

    <!-- Statistics Overview Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-1">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Member Publik Aktif</span>
            <div class="flex items-center justify-between">
                <span class="text-2xl font-extrabold text-slate-900"><?= number_format($stats['total_public']) ?></span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-1">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Vendor & Bisnis</span>
            <div class="flex items-center justify-between">
                <span class="text-2xl font-extrabold text-slate-900"><?= number_format($stats['total_vendor']) ?></span>
                <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-1">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Crew & Talenta</span>
            <div class="flex items-center justify-between">
                <span class="text-2xl font-extrabold text-slate-900"><?= number_format($stats['total_crew']) ?></span>
                <div class="w-8 h-8 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-1">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Member Featured</span>
            <div class="flex items-center justify-between">
                <span class="text-2xl font-extrabold text-amber-600"><?= number_format($stats['total_featured']) ?></span>
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <form action="<?= base_url('admin/directory') ?>" method="get" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Cari Member</label>
                <input type="text" 
                       name="q" 
                       value="<?= esc($filters['q']) ?>" 
                       placeholder="Nama, username, vendor..." 
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Tipe Member</label>
                <select name="type" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <option value="">Semua Tipe</option>
                    <option value="business" <?= ($filters['type'] === 'business') ? 'selected' : '' ?>>🏢 Vendor & Bisnis</option>
                    <option value="individual" <?= ($filters['type'] === 'individual') ? 'selected' : '' ?>>👤 Individu & Kru</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Kategori</label>
                <select name="category_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= (int) $cat['id'] ?>" <?= ((int) $filters['category_id'] === (int) $cat['id']) ? 'selected' : '' ?>>
                            <?= esc($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Status Featured</label>
                <select name="featured" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <option value="">Semua</option>
                    <option value="1" <?= ($filters['featured'] === '1') ? 'selected' : '' ?>>⭐ Hanya Featured</option>
                    <option value="0" <?= ($filters['featured'] === '0') ? 'selected' : '' ?>>Bukan Featured</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition">
                    Filter
                </button>
                <a href="<?= base_url('admin/directory') ?>" class="py-2 px-3 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Member Directory Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 border-b border-slate-200/80 text-slate-500 font-bold uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Profil Member</th>
                        <th class="px-6 py-4">Tipe & Kategori</th>
                        <th class="px-6 py-4">Portofolio</th>
                        <th class="px-6 py-4">Visibilitas</th>
                        <th class="px-6 py-4">Sorotan (Featured)</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($members)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-slate-400 italic">
                                Tidak ada data member yang sesuai kriteria pencarian.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($members as $m): ?>
                            <tr class="hover:bg-slate-50/70 transition">
                                <!-- Profile Identity -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <?php 
                                        $avatar = ! empty($m['photo_path']) && file_exists(FCPATH . $m['photo_path']) ? base_url($m['photo_path']) : null;
                                        if (! $avatar) {
                                            $avName = urlencode($m['display_name'] ?: $m['full_name']);
                                            $avatar = "https://ui-avatars.com/api/?name={$avName}&background=e0e7ff&color=4f46e5&bold=true&size=80";
                                        }
                                        ?>
                                        <img src="<?= esc($avatar) ?>" alt="" class="w-10 h-10 rounded-xl object-cover ring-1 ring-slate-200">
                                        <div class="min-w-0">
                                            <div class="font-bold text-slate-900 truncate flex items-center gap-1.5">
                                                <span><?= esc($m['display_name'] ?: $m['full_name']) ?></span>
                                                <?php if (! empty($m['is_featured'])): ?>
                                                    <span class="text-amber-500" title="Member Featured">⭐</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-[11px] text-slate-400 font-mono">
                                                @<?= esc($m['username']) ?> 
                                                <?php if (! empty($m['member_number'])): ?>
                                                    • <?= esc($m['member_number']) ?>
                                                <?php endif; ?>
                                            </div>
                                            <?php if ($m['member_type'] === 'business' && ! empty($m['business_name'])): ?>
                                                <div class="text-[11px] font-semibold text-slate-600 truncate"><?= esc($m['business_name']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>

                                <!-- Type & Category -->
                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-extrabold <?= $m['member_type'] === 'business' ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-100 text-slate-700' ?>">
                                            <?= $m['member_type'] === 'business' ? 'Vendor Bisnis' : 'Individu / Kru' ?>
                                        </span>
                                        <div class="text-[11px] font-semibold text-slate-700"><?= esc($m['category_name'] ?: 'Belum diatur') ?></div>
                                        <?php if (! empty($m['city'])): ?>
                                            <div class="text-[10px] text-slate-400"><?= esc($m['city']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <!-- Portfolio Count -->
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold <?= (int)$m['portfolio_count'] > 0 ? 'bg-brand-50 text-brand-700' : 'bg-slate-100 text-slate-400' ?>">
                                        <?= (int)$m['portfolio_count'] ?> Proyek
                                    </span>
                                </td>

                                <!-- Visibility -->
                                <td class="px-6 py-4">
                                    <?php if (! empty($m['is_public'])): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Publik
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            Privat
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Featured Status -->
                                <td class="px-6 py-4">
                                    <?php if (! empty($m['is_featured'])): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-amber-100 text-amber-900 border border-amber-200">
                                            Featured
                                        </span>
                                    <?php else: ?>
                                        <span class="text-[11px] text-slate-400">Standar</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Public Profile Link -->
                                        <a href="<?= base_url('member/' . esc($m['username'])) ?>" 
                                           target="_blank" 
                                           class="p-1.5 rounded-lg text-slate-500 hover:text-brand-600 hover:bg-brand-50 transition" 
                                           title="Lihat Profil Publik">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>

                                        <!-- Toggle Featured Button Form -->
                                        <form action="<?= base_url('admin/directory/feature/' . (int)$m['id']) ?>" method="post" class="inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" 
                                                    class="p-1.5 rounded-lg <?= ! empty($m['is_featured']) ? 'text-amber-600 hover:bg-amber-50' : 'text-slate-400 hover:text-amber-600 hover:bg-amber-50' ?> transition" 
                                                    title="<?= ! empty($m['is_featured']) ? 'Hapus dari Featured' : 'Tetapkan sebagai Featured' ?>">
                                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            </button>
                                        </form>

                                        <!-- Toggle Visibility Button Form -->
                                        <form action="<?= base_url('admin/directory/visibility/' . (int)$m['id']) ?>" method="post" class="inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" 
                                                    class="p-1.5 rounded-lg <?= ! empty($m['is_public']) ? 'text-emerald-600 hover:bg-emerald-50' : 'text-slate-400 hover:text-emerald-600 hover:bg-emerald-50' ?> transition" 
                                                    title="<?= ! empty($m['is_public']) ? 'Sembunyikan dari Direktori' : 'Tampilkan di Direktori' ?>">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                            </button>
                                        </form>

                                        <!-- Admin Member Detail Link -->
                                        <a href="<?= base_url('admin/members/view/' . (int)$m['user_id']) ?>" 
                                           class="p-1.5 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 transition" 
                                           title="Detail Administrasi Member">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Table Footer Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
                <div>
                    Halaman <span class="font-bold"><?= $page ?></span> dari <span class="font-bold"><?= $totalPages ?></span> (Total <?= number_format($total) ?> member)
                </div>
                <div class="flex items-center gap-2">
                    <?php 
                    $queryParams = $_GET;
                    ?>
                    <?php if ($page > 1): ?>
                        <?php $queryParams['page'] = $page - 1; ?>
                        <a href="<?= base_url('admin/directory?' . http_build_query($queryParams)) ?>" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 font-bold">
                            &laquo; Sebelumnya
                        </a>
                    <?php endif; ?>

                    <?php if ($page < $totalPages): ?>
                        <?php $queryParams['page'] = $page + 1; ?>
                        <a href="<?= base_url('admin/directory?' . http_build_query($queryParams)) ?>" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 font-bold">
                            Selanjutnya &raquo;
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

</div>

<?= $this->endSection() ?>
