<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <!-- Top Action & Title Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="<?= base_url('admin/events') ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-brand-600 transition mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Manajemen Kegiatan
            </a>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                Laporan Presensi & Kehadiran: <?= esc($event['title']) ?>
            </h1>
            <p class="text-xs text-slate-500">
                Statistik riil presensi peserta dan riwayat check-in acara.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="<?= base_url('admin/events/' . $event['id'] . '/checkin') ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                Scanner Check-in
            </a>

            <a href="<?= base_url('admin/events/' . $event['id'] . '/attendance/csv') ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Ekspor Presensi CSV
            </a>
        </div>
    </div>

    <!-- Analytics Cards Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <!-- Confirmed / Quota -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs space-y-1">
            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Peserta Terkonfirmasi</span>
            <div class="text-2xl font-extrabold text-slate-900"><?= $stats['confirmed'] ?></div>
            <span class="text-[11px] text-slate-500 font-semibold block">
                Total Registrasi: <?= $stats['total_registrations'] ?>
            </span>
        </div>

        <!-- Checked In -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs space-y-1">
            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Hadir (Checked-in)</span>
            <div class="text-2xl font-extrabold text-emerald-600"><?= $stats['checked_in'] ?></div>
            <span class="text-[11px] text-emerald-700 font-bold block">
                Tingkat Kehadiran: <?= $stats['attendance_percentage'] ?>%
            </span>
        </div>

        <!-- Not Checked In -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs space-y-1">
            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Belum Hadir</span>
            <div class="text-2xl font-extrabold text-amber-600"><?= $stats['not_checked_in'] ?></div>
            <span class="text-[11px] text-slate-400 font-semibold block">
                Sisa belum check-in
            </span>
        </div>

        <!-- Member vs External breakdown -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs space-y-1">
            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Proporsi Peserta</span>
            <div class="text-2xl font-extrabold text-brand-600">
                <?= $stats['member_registrations'] ?> <span class="text-xs font-bold text-slate-400">Member</span>
            </div>
            <span class="text-[11px] text-slate-500 font-semibold block">
                <?= $stats['external_registrations'] ?> Peserta Umum
            </span>
        </div>
    </div>

    <!-- Recent Check-ins Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden space-y-4 p-6 sm:p-8">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Riwayat Presensi Check-in Terbaru
            </h3>
            <span class="text-xs text-slate-400 font-bold"><?= count($recentCheckins) ?> data ditampilkan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-50 border-b border-slate-200/80 text-slate-500 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="py-3 px-4 font-bold">Waktu Check-in</th>
                        <th class="py-3 px-4 font-bold">No. Registrasi</th>
                        <th class="py-3 px-4 font-bold">Nama Peserta</th>
                        <th class="py-3 px-4 font-bold">Tipe Peserta</th>
                        <th class="py-3 px-4 font-bold">Metode Check-in</th>
                        <th class="py-3 px-4 font-bold">Petugas Presensi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($recentCheckins)): ?>
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                Belum ada riwayat check-in yang tercatat pada kegiatan ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentCheckins as $att): ?>
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3.5 px-4 whitespace-nowrap font-bold text-slate-900">
                                    <?= date('d/m/Y H:i:s', strtotime($att['checked_in_at'])) ?> WIB
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap font-mono font-bold text-brand-600">
                                    <?= esc($att['registration_number']) ?>
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    <?= esc($att['participant_type'] === 'member' ? $att['member_name'] : $att['guest_name']) ?>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold <?= $att['participant_type'] === 'member' ? 'bg-indigo-100 text-indigo-800' : 'bg-emerald-100 text-emerald-800' ?>">
                                        <?= $att['participant_type'] === 'member' ? 'Member' : 'Umum' ?>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <?php if ($att['checkin_method'] === 'qr_ticket'): ?>
                                        <span class="inline-flex items-center gap-1 font-bold text-indigo-600">
                                            QR Tiket
                                        </span>
                                    <?php elseif ($att['checkin_method'] === 'kta_qr'): ?>
                                        <span class="inline-flex items-center gap-1 font-bold text-brand-600">
                                            QR KTA Member
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 text-slate-600">
                                            Manual
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap text-slate-500">
                                    <?= esc($att['staff_name'] ?: 'Admin') ?>
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
