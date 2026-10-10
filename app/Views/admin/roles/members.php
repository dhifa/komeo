<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="<?= base_url('admin/member-roles') ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 hover:text-white transition-colors mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Daftar Role
            </a>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-black tracking-tight text-white">
                    Anggota dengan Role: <?= esc($role['name']) ?>
                </h1>
                <?= komeo_render_member_role($role, 'sm', false) ?>
            </div>
            <p class="text-sm text-slate-400 mt-1">
                Daftar anggota komunitas yang saat ini memiliki penugasan aktif role ini.
            </p>
        </div>
    </div>

    <!-- Members Table -->
    <div class="rounded-2xl bg-slate-900/80 border border-slate-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/60 border-b border-slate-800 text-slate-400 uppercase text-[10px] font-bold tracking-wider">
                    <tr>
                        <th class="p-4">Anggota</th>
                        <th class="p-4">Tipe Role</th>
                        <th class="p-4">Tanggal Ditugaskan</th>
                        <th class="p-4">Masa Berlaku</th>
                        <th class="p-4">Status Keanggotaan</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <?php if (empty($members)): ?>
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500">
                                Belum ada anggota yang ditugaskan ke role ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($members as $m): ?>
                            <tr class="hover:bg-slate-850/50">
                                <td class="p-4">
                                    <span class="font-bold text-white block text-sm">
                                        <?= esc($m['full_name'] ?: $m['username']) ?>
                                    </span>
                                    <span class="text-[11px] text-slate-400 block font-mono">
                                        <?= esc($m['member_number'] ?: '@' . $m['username']) ?>
                                    </span>
                                </td>
                                <td class="p-4">
                                    <?php if (! empty($m['is_primary'])): ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-brand-500/20 text-brand-300 border border-brand-500/30">
                                            Role Utama (Primary)
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-800 text-slate-400 border border-slate-700">
                                            Role Sekunder
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4 font-mono text-slate-300">
                                    <?= date('d/m/Y H:i', strtotime($m['assigned_at'])) ?>
                                </td>
                                <td class="p-4">
                                    <?php if (! empty($m['expires_at'])): ?>
                                        <span class="text-amber-400 font-mono">
                                            s/d <?= date('d/m/Y', strtotime($m['expires_at'])) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-slate-500 italic">Permanen</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                        <?= esc($m['membership_status'] ?: 'Active') ?>
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <a href="<?= base_url('admin/members/view/' . $m['user_id']) ?>" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold transition-colors">
                                        Lihat Member
                                    </a>
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
