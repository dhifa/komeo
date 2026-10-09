<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <!-- Top Action & Title Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Manajemen Kegiatan & Event</h1>
            <p class="text-xs text-slate-500">Kelola agenda kegiatan komunitas, pendaftaran peserta, dan presensi QR check-in.</p>
        </div>

        <?php if ($isAdmin): ?>
            <a href="<?= base_url('admin/events/create') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Kegiatan Baru
            </a>
        <?php endif; ?>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <form action="<?= base_url('admin/events') ?>" method="GET" class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="q" value="<?= esc($search ?? '') ?>" placeholder="Cari judul, venue, kota..." 
                       class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none">
            </div>

            <div>
                <select name="type" class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs bg-white focus:ring-2 focus:ring-brand-500/20 outline-none">
                    <option value="">Semua Tipe</option>
                    <option value="internal" <?= ($type ?? '') === 'internal' ? 'selected' : '' ?>>Internal</option>
                    <option value="external" <?= ($type ?? '') === 'external' ? 'selected' : '' ?>>External</option>
                    <option value="hybrid" <?= ($type ?? '') === 'hybrid' ? 'selected' : '' ?>>Hybrid</option>
                </select>
            </div>

            <div>
                <select name="status" class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs bg-white focus:ring-2 focus:ring-brand-500/20 outline-none">
                    <option value="">Semua Status</option>
                    <option value="draft" <?= ($status ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="published" <?= ($status ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                    <option value="cancelled" <?= ($status ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    <option value="completed" <?= ($status ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
                </select>
            </div>

            <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                Filter
            </button>
            <?php if (! empty($search) || ! empty($type) || ! empty($status)): ?>
                <a href="<?= base_url('admin/events') ?>" class="px-3 py-2 text-xs font-semibold text-rose-500 hover:underline">
                    Reset
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Events Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-50 border-b border-slate-200/80 text-slate-500 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="py-3.5 px-4 font-bold">Kegiatan</th>
                        <th class="py-3.5 px-4 font-bold">Tipe & Status</th>
                        <th class="py-3.5 px-4 font-bold">Waktu & Tempat</th>
                        <th class="py-3.5 px-4 font-bold text-center">Peserta</th>
                        <th class="py-3.5 px-4 font-bold text-center">Kehadiran</th>
                        <th class="py-3.5 px-4 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($events)): ?>
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                Belum ada agenda kegiatan yang ditemukan.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($events as $event): ?>
                            <tr class="hover:bg-slate-50/70 transition">
                                <!-- Title & Banner -->
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-xl bg-slate-100 overflow-hidden shrink-0 border border-slate-200/80">
                                            <?php if (! empty($event['banner_path']) && file_exists(FCPATH . $event['banner_path'])): ?>
                                                <img src="<?= base_url(esc($event['banner_path'])) ?>" alt="Banner" class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <div class="w-full h-full flex items-center justify-center text-slate-400 font-bold">
                                                    EVT
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="min-w-0">
                                            <a href="<?= base_url('admin/events/' . $event['id'] . '/participants') ?>" class="font-bold text-slate-900 hover:text-brand-600 block line-clamp-1">
                                                <?= esc($event['title']) ?>
                                            </a>
                                            <span class="text-[10px] text-slate-400 font-mono"><?= esc($event['slug']) ?></span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Type & Status -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <div class="space-y-1">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase <?= $event['event_type'] === 'internal' ? 'bg-indigo-100 text-indigo-800' : ($event['event_type'] === 'external' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800') ?>">
                                            <?= esc($event['event_type']) ?>
                                        </span>
                                        <div>
                                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase <?= $event['status'] === 'published' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($event['status'] === 'draft' ? 'bg-slate-100 text-slate-600' : 'bg-rose-50 text-rose-700 border border-rose-200') ?>">
                                                <?= esc($event['status']) ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Schedule & Venue -->
                                <td class="py-4 px-4 whitespace-nowrap text-slate-600">
                                    <div class="font-bold text-slate-900">
                                        <?= date('d M Y, H:i', strtotime($event['start_date'])) ?> WIB
                                    </div>
                                    <div class="text-[11px] text-slate-400 truncate max-w-[180px]">
                                        <?= esc($event['venue_name'] ?: 'Venue') ?> (<?= esc($event['city'] ?: '-') ?>)
                                    </div>
                                </td>

                                <!-- Participants Count -->
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    <span class="font-bold text-slate-900 text-sm">
                                        <?= $event['stats']['confirmed'] ?>
                                    </span>
                                    <span class="text-[10px] text-slate-400 block">
                                        / <?= $event['total_quota'] > 0 ? $event['total_quota'] : '∞' ?> kuota
                                    </span>
                                    <?php if ($event['stats']['pending'] > 0): ?>
                                        <span class="inline-block px-1.5 py-0.2 rounded-full text-[9px] font-extrabold bg-amber-400 text-slate-950 mt-0.5">
                                            <?= $event['stats']['pending'] ?> pending
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Attendance -->
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    <span class="font-bold text-emerald-600 text-sm">
                                        <?= $event['stats']['checked_in'] ?> Hadir
                                    </span>
                                    <span class="text-[10px] text-slate-400 block">
                                        (<?= $event['stats']['attendance_percentage'] ?>%)
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td class="py-4 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="<?= base_url('admin/events/' . $event['id'] . '/checkin') ?>" class="p-1.5 rounded-lg text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition" title="Buka Scanner Check-in">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                        </a>

                                        <a href="<?= base_url('admin/events/' . $event['id'] . '/participants') ?>" class="p-1.5 rounded-lg text-brand-700 bg-brand-50 hover:bg-brand-100 transition" title="Daftar Peserta">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                        </a>

                                        <a href="<?= base_url('admin/events/' . $event['id'] . '/attendance') ?>" class="p-1.5 rounded-lg text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition" title="Laporan Presensi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                        </a>

                                        <?php if ($isAdmin): ?>
                                            <a href="<?= base_url('admin/events/' . $event['id'] . '/edit') ?>" class="p-1.5 rounded-lg text-slate-700 bg-slate-100 hover:bg-slate-200 transition" title="Edit Kegiatan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
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
<?= $this->endSection() ?>
