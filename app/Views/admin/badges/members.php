<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="space-y-6">

    <!-- Header Navigation -->
    <div class="flex items-center justify-between">
        <a href="<?= base_url('admin/badges') ?>" class="inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-500 hover:text-brand-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar Badge
        </a>
    </div>

    <!-- Badge Info Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-2">
            <div class="flex items-center gap-3">
                <?= komeo_render_badge($badge, 'lg', false) ?>
                <?php if ($badge['is_active']): ?>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">Aktif</span>
                <?php else: ?>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600">Nonaktif</span>
                <?php endif; ?>
            </div>
            <p class="text-xs sm:text-sm text-slate-600 max-w-2xl">
                <?= esc($badge['description'] ?: 'Tidak ada deskripsi badge.') ?>
            </p>
            <div class="flex items-center gap-3 text-xs text-slate-400 font-mono">
                <span>Slug: <?= esc($badge['slug']) ?></span>
                <span>•</span>
                <span>Prioritas Urutan: <?= (int) $badge['sort_order'] ?></span>
            </div>
        </div>

        <div class="shrink-0 flex items-center gap-2">
            <a href="<?= base_url('admin/badges/edit/' . $badge['id']) ?>" class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                Edit Badge
            </a>
        </div>
    </div>

    <!-- Assign Badge Form Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
            <h2 class="text-base sm:text-lg font-black text-slate-900 tracking-tight flex items-center gap-2">
                <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Sematkan Badge ke Anggota
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">Pilih anggota individu atau badan usaha untuk menerima badge ini.</p>
        </div>

        <form action="<?= base_url('admin/badges/members/' . $badge['id'] . '/assign') ?>" method="post" class="p-6 space-y-4">
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Member select -->
                <div class="space-y-1">
                    <label for="user_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Pilih Anggota <span class="text-rose-500">*</span>
                    </label>
                    <select id="user_id" name="user_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-brand-500 bg-white">
                        <option value="">-- Cari / Pilih Anggota --</option>
                        <?php foreach ($allMembers as $m): ?>
                            <option value="<?= (int) $m['user_id'] ?>">
                                <?= esc($m['full_name'] ?: $m['display_name']) ?> (@<?= esc($m['username']) ?>) - <?= $m['member_type'] === 'business' ? 'Bisnis/Vendor' : 'Individu' ?> <?= ! empty($m['member_number']) ? '[' . esc($m['member_number']) . ']' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Expiration Date -->
                <div class="space-y-1">
                    <label for="expires_at" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Masa Berlaku (Opsional)
                    </label>
                    <input type="date" 
                           id="expires_at" 
                           name="expires_at" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-brand-500">
                    <p class="text-[10px] text-slate-400">Kosongkan jika berlaku permanen tanpa kadaluarsa.</p>
                </div>

                <!-- Internal Note -->
                <div class="space-y-1">
                    <label for="internal_note" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Catatan Internal Admin (Opsional)
                    </label>
                    <input type="text" 
                           id="internal_note" 
                           name="internal_note" 
                           placeholder="Alasan penugasan atau referensi verifikasi..."
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-brand-500">
                    <p class="text-[10px] text-slate-400">Catatan ini hanya dapat dilihat oleh tim admin.</p>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs sm:text-sm font-bold shadow-sm transition">
                    Sematkan Badge
                </button>
            </div>
        </form>
    </div>

    <!-- Assigned Members Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Riwayat Penugasan Badge (<?= count($assignments) ?>)</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/75 text-[11px] font-black uppercase tracking-wider text-slate-500">
                        <th class="py-4 px-6">Anggota</th>
                        <th class="py-4 px-6">Tipe & Status</th>
                        <th class="py-4 px-6">Tanggal Penugasan</th>
                        <th class="py-4 px-6">Masa Berlaku</th>
                        <th class="py-4 px-6">Catatan Internal</th>
                        <th class="py-4 px-6 text-center">Status Badge</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    <?php if (empty($assignments)): ?>
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                Belum ada anggota yang disematkan badge ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $now = date('Y-m-d H:i:s');
                        foreach ($assignments as $a): 
                            $isRevoked = ! empty($a['revoked_at']);
                            $isExpired = ! empty($a['expires_at']) && $a['expires_at'] < $now;
                            $isActive  = ! $isRevoked && ! $isExpired;
                        ?>
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center font-bold text-slate-600 shrink-0">
                                            <?= strtoupper(substr($a['full_name'] ?: ($a['username'] ?: 'M'), 0, 1)) ?>
                                        </div>
                                        <div>
                                            <a href="<?= base_url('admin/members/view/' . $a['user_id']) ?>" class="font-bold text-slate-900 hover:text-brand-600 transition">
                                                <?= esc($a['full_name'] ?: $a['display_name']) ?>
                                            </a>
                                            <p class="text-[11px] text-slate-400">@<?= esc($a['username']) ?> <?= ! empty($a['member_number']) ? '• ' . esc($a['member_number']) : '' ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">
                                        <?= ($a['member_type'] ?? '') === 'business' ? 'Badan Usaha' : 'Individu' ?>
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="font-medium text-slate-700"><?= date('d/m/Y H:i', strtotime($a['assigned_at'])) ?></span>
                                    <p class="text-[10px] text-slate-400">Oleh: @<?= esc($a['assigned_by_username'] ?: 'admin') ?></p>
                                </td>
                                <td class="py-4 px-6">
                                    <?php if (! empty($a['expires_at'])): ?>
                                        <span class="<?= $isExpired ? 'text-rose-600 font-bold' : 'text-slate-700' ?>">
                                            <?= date('d/m/Y', strtotime($a['expires_at'])) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-slate-400 italic">Permanen</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-6 max-w-xs text-slate-600">
                                    <?= esc($a['internal_note'] ?: '-') ?>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <?php if ($isRevoked): ?>
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800" title="Dicabut pada <?= date('d/m/Y H:i', strtotime($a['revoked_at'])) ?> oleh @<?= esc($a['revoked_by_username']) ?>">
                                            Dicabut
                                        </span>
                                    <?php elseif ($isExpired): ?>
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                            Kadaluarsa
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            Aktif
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <?php if ($isActive): ?>
                                        <form action="<?= base_url('admin/badges/members/' . $badge['id'] . '/revoke/' . $a['id']) ?>" method="post" class="inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="px-3 py-1.5 rounded-lg text-[11px] font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition">
                                                Cabut Badge
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-slate-300 text-[11px] italic">Tidak aktif</span>
                                    <?php endif; ?>
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
