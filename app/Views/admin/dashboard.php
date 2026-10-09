<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="space-y-8">
    <!-- Top Welcome Card -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Ikhtisar Platform KOMEO.ID</h2>
            <p class="text-sm text-slate-500 mt-1">Data statistik pendaftaran dan keanggotaan real-time</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Database Terhubung
            </span>
        </div>
    </div>

    <!-- Statistics Cards Grid (Total, Pending, Active, Recent) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- 1. Total Members -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Member</p>
                <p class="text-3xl font-extrabold text-slate-900"><?= number_format($stats['total'] ?? 0) ?></p>
                <p class="text-xs text-slate-400">Semua pendaftar di sistem</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>

        <!-- 2. Pending Members -->
        <a href="<?= base_url('admin/members?status=pending') ?>" class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between hover:border-amber-400 hover:shadow-md transition group">
            <div class="space-y-1">
                <p class="text-xs font-bold uppercase tracking-wider text-amber-600">Menunggu Persetujuan</p>
                <p class="text-3xl font-extrabold text-amber-600"><?= number_format($stats['pending'] ?? 0) ?></p>
                <p class="text-xs text-amber-600/80 flex items-center gap-1 group-hover:underline">
                    <span>Tinjau pengajuan</span>
                    <span aria-hidden="true">&rarr;</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 group-hover:bg-amber-100 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </a>

        <!-- 3. Active Members -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-xs font-bold uppercase tracking-wider text-emerald-600">Member Aktif</p>
                <p class="text-3xl font-extrabold text-emerald-600"><?= number_format($stats['active'] ?? 0) ?></p>
                <p class="text-xs text-emerald-600/80">Terverifikasi resmi</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- 4. Recent Registrations (7 Days) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Pendaftar 7 Hari Terakhir</p>
                <p class="text-3xl font-extrabold text-slate-900"><?= number_format($stats['recent'] ?? 0) ?></p>
                <p class="text-xs text-slate-400">Pertumbuhan mingguan</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
        </div>
    </div>

    <!-- Recent Registrations Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Pendaftaran Terbaru (Recent Registrations)</h3>
                <p class="text-xs text-slate-500 mt-0.5">Daftar pengguna yang baru bergabung ke KOMEO.ID</p>
            </div>
            <span class="text-xs text-slate-500 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200/60">
                Menampilkan <?= count($recentRegistrations) ?> pendaftar terakhir
            </span>
        </div>

        <?php if (empty($recentRegistrations)): ?>
            <div class="p-12 text-center space-y-3">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                </div>
                <p class="text-sm font-semibold text-slate-700">Belum ada pendaftaran anggota baru.</p>
                <p class="text-xs text-slate-400">Pendaftaran publik akan muncul secara otomatis di tabel ini.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/70 text-[11px] uppercase tracking-wider font-bold text-slate-500">
                            <th class="py-3.5 px-6">Member</th>
                            <th class="py-3.5 px-6">Tipe & Usaha</th>
                            <th class="py-3.5 px-6">Kota</th>
                            <th class="py-3.5 px-6">Kontak</th>
                            <th class="py-3.5 px-6">Status</th>
                            <th class="py-3.5 px-6">Tanggal Daftar</th>
                            <th class="py-3.5 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <?php foreach ($recentRegistrations as $item): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <!-- Member info -->
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-700 font-bold flex items-center justify-center text-xs shrink-0">
                                            <?= strtoupper(substr($item->full_name ?? $item->username ?? 'U', 0, 1)) ?>
                                        </div>
                                        <div>
                                            <a href="<?= base_url('admin/members/view/' . $item->id) ?>" class="font-bold text-slate-900 hover:text-indigo-600 transition-colors">
                                                <?= esc($item->full_name ?? '-') ?>
                                            </a>
                                            <p class="text-xs text-slate-500">@<?= esc($item->username ?? '-') ?></p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Member type & business -->
                                <td class="py-4 px-6">
                                    <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-700">
                                        <?= ($item->member_type ?? 'individual') === 'business' ? 'Badan Usaha' : 'Perorangan' ?>
                                    </span>
                                    <?php if (! empty($item->business_name)): ?>
                                        <p class="text-xs text-slate-600 mt-1 truncate max-w-xs font-medium"><?= esc($item->business_name) ?></p>
                                    <?php endif; ?>
                                </td>

                                <!-- City -->
                                <td class="py-4 px-6 text-slate-600 text-xs">
                                    <?= esc($item->city ?? '-') ?>
                                </td>

                                <!-- Contact -->
                                <td class="py-4 px-6 text-xs text-slate-600">
                                    <p class="truncate"><?= esc($item->email ?? '-') ?></p>
                                    <?php if (! empty($item->whatsapp)): ?>
                                        <p class="text-emerald-600 font-medium mt-0.5"><?= esc($item->whatsapp) ?></p>
                                    <?php endif; ?>
                                </td>

                                <!-- Status Badge -->
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold <?= $item->getStatusBadgeClasses() ?>">
                                        <?= $item->getStatusLabel() ?>
                                    </span>
                                </td>

                                <!-- Created at -->
                                <td class="py-4 px-6 text-xs text-slate-500 whitespace-nowrap">
                                    <?= $item->created_at ? date('d M Y H:i', strtotime((string)$item->created_at)) : '-' ?>
                                </td>

                                <!-- Action button -->
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <a href="<?= base_url('admin/members/view/' . $item->id) ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors">
                                        <span>Detail</span>
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
