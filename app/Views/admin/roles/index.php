<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-white flex items-center gap-2.5">
                <span>Role Member Komunitas</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-extrabold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                    SEBUTAN RESMI
                </span>
            </h1>
            <p class="text-sm text-slate-400 mt-1">
                Kelola nama dan sebutan peran anggota komunitas (misal: Pengurus, Koordinator Daerah, VIP) tanpa mengubah hak akses keamanan sistem (Shield).
            </p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="<?= base_url('admin/member-roles/create') ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-brand-600 text-white hover:bg-brand-500 transition-colors shadow-md shadow-brand-900/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Role Baru</span>
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="p-4 rounded-2xl bg-emerald-950/40 border border-emerald-500/30 text-emerald-200 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span><?= session()->getFlashdata('success') ?></span>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="p-4 rounded-2xl bg-rose-950/40 border border-rose-500/30 text-rose-200 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span><?= session()->getFlashdata('error') ?></span>
        </div>
    <?php endif; ?>

    <!-- Overview Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800">
            <span class="text-xs text-slate-400 font-semibold block">Total Definisi Role</span>
            <span class="text-2xl font-black text-white mt-1 block"><?= $totalRoles ?> Role</span>
            <span class="text-[11px] text-slate-500 mt-1 block">Sebutan peran komunitas</span>
        </div>
        <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800">
            <span class="text-xs text-emerald-400 font-semibold block">Role Berstatus Aktif</span>
            <span class="text-2xl font-black text-emerald-300 mt-1 block"><?= $activeRoles ?> Role</span>
            <span class="text-[11px] text-slate-500 mt-1 block">Dapat ditugaskan ke member</span>
        </div>
        <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800">
            <span class="text-xs text-brand-400 font-semibold block">Role Bawaan (Default)</span>
            <span class="text-lg font-black text-brand-300 mt-1 block truncate">
                <?= esc($defaultKey) ?>
            </span>
            <span class="text-[11px] text-slate-500 mt-1 block">Diberikan saat aktivasi anggota</span>
        </div>
    </div>

    <!-- Security Distinction Notice -->
    <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800 text-xs text-slate-400 space-y-1">
        <strong class="text-white block font-bold">Catatan Arsitektur Hak Akses:</strong>
        <p class="leading-relaxed">
            Role Komunitas di bawah ini murni merupakan <em>community designation</em> untuk pengakuan sosial dan profil publik. Role ini <strong>tidak mengubah</strong> kelompok autentikasi CodeIgniter Shield dan <strong>tidak memberikan</strong> hak akses administrasi web ke pengguna biasa.
        </p>
    </div>

    <!-- Roles Table -->
    <div class="rounded-2xl bg-slate-900/80 border border-slate-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/60 border-b border-slate-800 text-slate-400 uppercase text-[10px] font-bold tracking-wider">
                    <tr>
                        <th class="p-4">Urutan</th>
                        <th class="p-4">Nama Role & Kunci</th>
                        <th class="p-4">Pratinjau Badge</th>
                        <th class="p-4">Anggota Aktif</th>
                        <th class="p-4">Visibilitas Publik</th>
                        <th class="p-4">Status & Bawaan</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <?php foreach ($roles as $r): ?>
                        <tr class="hover:bg-slate-850/50 transition-colors">
                            <td class="p-4 font-mono text-slate-400 font-bold">
                                #<?= $r['sort_order'] ?>
                            </td>
                            <td class="p-4">
                                <span class="font-bold text-white text-sm block">
                                    <?= esc($r['name']) ?>
                                </span>
                                <span class="text-[11px] text-slate-400 font-mono block mt-0.5">
                                    <?= esc($r['role_key']) ?>
                                </span>
                                <?php if (! empty($r['description'])): ?>
                                    <span class="text-[11px] text-slate-500 block mt-1 line-clamp-1">
                                        <?= esc($r['description']) ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4">
                                <?= komeo_render_member_role($r, 'sm', false) ?>
                            </td>
                            <td class="p-4">
                                <a href="<?= base_url('admin/member-roles/' . $r['id'] . '/members') ?>" class="inline-flex items-center gap-1.5 font-bold text-brand-300 hover:text-brand-200">
                                    <span><?= $r['active_members_count'] ?> Anggota</span>
                                    <span class="text-[10px] text-slate-500">(<?= $r['primary_count'] ?> Utama)</span>
                                </a>
                            </td>
                            <td class="p-4">
                                <?= ! empty($r['is_public']) 
                                    ? '<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">Publik</span>' 
                                    : '<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-slate-400 border border-slate-700">Internal</span>' ?>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <?= ! empty($r['is_active'])
                                        ? '<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Aktif</span>'
                                        : '<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">Nonaktif</span>' ?>

                                    <?php if (! empty($r['is_default'])): ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                            DEFAULT
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="p-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="<?= base_url('admin/member-roles/' . $r['id'] . '/edit') ?>" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold transition-colors">
                                        Edit
                                    </a>

                                    <form method="post" action="<?= base_url('admin/member-roles/' . $r['id'] . '/toggle') ?>" class="inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white transition-colors" title="<?= $r['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                            <?= $r['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                                        </button>
                                    </form>

                                    <?php if (empty($r['is_default']) && ! empty($r['is_active'])): ?>
                                        <form method="post" action="<?= base_url('admin/member-roles/' . $r['id'] . '/default') ?>" class="inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="px-2 py-1 rounded-lg bg-slate-800 hover:bg-amber-600 text-slate-400 hover:text-white transition-colors" title="Jadikan role bawaan">
                                                Set Default
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bulk Assignment Action Card -->
    <div class="p-6 rounded-3xl bg-slate-900/80 border border-slate-800 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-sm font-bold text-white">Penugasan Massal Anggota Aktif Tanpa Role</h3>
            <p class="text-xs text-slate-400 mt-0.5">
                Opsi aman untuk menugaskan role komunitas bawaan kepada seluruh anggota aktif yang saat ini belum memiliki role.
            </p>
        </div>
        <form method="post" action="<?= base_url('admin/member-roles/bulk-assign') ?>" class="flex items-center gap-2">
            <?= csrf_field() ?>
            <select name="role_id" required class="px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-brand-500">
                <?php foreach ($roles as $r): ?>
                    <?php if (! empty($r['is_active'])): ?>
                        <option value="<?= $r['id'] ?>" <?= ! empty($r['is_default']) ? 'selected' : '' ?>>
                            <?= esc($r['name']) ?> <?= ! empty($r['is_default']) ? '(Default)' : '' ?>
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menugaskan role ini kepada seluruh anggota aktif yang belum memiliki role?')" class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white transition-colors shrink-0">
                Tugaskan Massal
            </button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
