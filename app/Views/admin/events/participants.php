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
                Daftar Peserta: <?= esc($event['title']) ?>
            </h1>
            <p class="text-xs text-slate-500">
                <?= date('d M Y, H:i', strtotime($event['start_date'])) ?> WIB • <?= esc($event['venue_name'] ?: 'Venue') ?>
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="<?= base_url('admin/events/' . $event['id'] . '/checkin') ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                Buka Scanner Check-in
            </a>

            <a href="<?= base_url('admin/events/' . $event['id'] . '/attendance') ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Presensi & Ekspor
            </a>
        </div>
    </div>

    <!-- Quick Stats Metric Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Total Registrasi</span>
            <span class="text-xl font-extrabold text-slate-900"><?= $stats['total_registrations'] ?></span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Member / Umum</span>
            <span class="text-xl font-extrabold text-brand-600"><?= $stats['member_registrations'] ?> <span class="text-sm font-semibold text-slate-400">/ <?= $stats['external_registrations'] ?></span></span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Terkonfirmasi</span>
            <span class="text-xl font-extrabold text-emerald-600"><?= $stats['confirmed'] ?></span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Hadir (Checked-in)</span>
            <span class="text-xl font-extrabold text-indigo-600"><?= $stats['checked_in'] ?> <span class="text-xs text-slate-400 font-semibold">(<?= $stats['attendance_percentage'] ?>%)</span></span>
        </div>
    </div>

    <!-- Search & Filter Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <form action="<?= base_url('admin/events/' . $event['id'] . '/participants') ?>" method="GET" class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="q" value="<?= esc($search ?? '') ?>" placeholder="Cari nama, email, no. registrasi, no. anggota..." 
                       class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none">
            </div>

            <div>
                <select name="type" class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs bg-white focus:ring-2 focus:ring-brand-500/20 outline-none">
                    <option value="">Semua Tipe</option>
                    <option value="member" <?= ($type ?? '') === 'member' ? 'selected' : '' ?>>Member KOMEO</option>
                    <option value="external" <?= ($type ?? '') === 'external' ? 'selected' : '' ?>>Peserta Umum</option>
                </select>
            </div>

            <div>
                <select name="status" class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs bg-white focus:ring-2 focus:ring-brand-500/20 outline-none">
                    <option value="">Semua Status</option>
                    <option value="confirmed" <?= ($status ?? '') === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                    <option value="pending_approval" <?= ($status ?? '') === 'pending_approval' ? 'selected' : '' ?>>Pending Approval</option>
                    <option value="pending_verification" <?= ($status ?? '') === 'pending_verification' ? 'selected' : '' ?>>Pending Verification</option>
                    <option value="rejected" <?= ($status ?? '') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
            </div>

            <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                Filter
            </button>
        </form>
    </div>

    <!-- Participants Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-50 border-b border-slate-200/80 text-slate-500 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="py-3.5 px-4 font-bold">No. Registrasi</th>
                        <th class="py-3.5 px-4 font-bold">Nama Peserta</th>
                        <th class="py-3.5 px-4 font-bold">Tipe & Identitas</th>
                        <th class="py-3.5 px-4 font-bold">Status Registrasi</th>
                        <th class="py-3.5 px-4 font-bold">Kehadiran</th>
                        <th class="py-3.5 px-4 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($participants)): ?>
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                Belum ada peserta terdaftar untuk kegiatan ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($participants as $p): ?>
                            <tr class="hover:bg-slate-50/70 transition">
                                <!-- Reg Number -->
                                <td class="py-4 px-4 whitespace-nowrap font-mono font-bold text-brand-700">
                                    <?= esc($p['registration_number']) ?>
                                    <span class="block text-[10px] text-slate-400 font-sans font-normal"><?= date('d/m/Y H:i', strtotime($p['registered_at'])) ?></span>
                                </td>

                                <!-- Participant Name -->
                                <td class="py-4 px-4">
                                    <div class="font-bold text-slate-900">
                                        <?= esc($p['participant_type'] === 'member' ? ($p['member_name'] ?: $p['username']) : ($p['guest_name'] ?: 'Umum')) ?>
                                    </div>
                                    <div class="text-[11px] text-slate-400">
                                        <?= esc($p['participant_type'] === 'member' ? $p['user_email'] : $p['guest_email']) ?>
                                    </div>
                                </td>

                                <!-- Type & ID -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold <?= $p['participant_type'] === 'member' ? 'bg-indigo-100 text-indigo-800' : 'bg-emerald-100 text-emerald-800' ?>">
                                        <?= $p['participant_type'] === 'member' ? 'MEMBER' : 'UMUM' ?>
                                    </span>
                                    <?php if ($p['participant_type'] === 'member'): ?>
                                        <span class="block text-[10px] text-slate-500 font-mono mt-0.5">KTA: <?= esc($p['membership_number'] ?: '-') ?></span>
                                    <?php else: ?>
                                        <span class="block text-[10px] text-slate-500 mt-0.5"><?= esc($p['guest_company'] ?: '-') ?></span>
                                    <?php endif; ?>
                                </td>

                                <!-- Status -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <?php if ($p['status'] === 'confirmed'): ?>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            TERKONFIRMASI
                                        </span>
                                    <?php elseif ($p['status'] === 'pending_approval'): ?>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                            MENUNGGU APPROVAL
                                        </span>
                                    <?php elseif ($p['status'] === 'pending_verification'): ?>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                            VERIFIKASI EMAIL
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                            <?= strtoupper($p['status']) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Attendance Status -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <?php if (! empty($p['attendance_id'])): ?>
                                        <span class="inline-flex items-center gap-1 font-bold text-emerald-600">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                            HADIR (<?= date('H:i', strtotime($p['checked_in_at'])) ?>)
                                        </span>
                                    <?php else: ?>
                                        <span class="text-slate-400">Belum Hadir</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Actions -->
                                <td class="py-4 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <?php if ($p['status'] === 'pending_approval' && $isAdmin): ?>
                                            <form action="<?= base_url('admin/events/' . $event['id'] . '/participants/' . $p['id'] . '/approve') ?>" method="POST" class="inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="px-2.5 py-1 rounded-lg text-[11px] font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-xs" title="Setujui Peserta">
                                                    Setujui
                                                </button>
                                            </form>
                                            <form action="<?= base_url('admin/events/' . $event['id'] . '/participants/' . $p['id'] . '/reject') ?>" method="POST" class="inline" onsubmit="return confirm('Tolak pendaftaran peserta ini?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="px-2 py-1 rounded-lg text-[11px] font-bold text-rose-600 hover:bg-rose-50 transition" title="Tolak">
                                                    Tolak
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <?php if ($p['status'] === 'confirmed'): ?>
                                            <a href="<?= base_url('kegiatan/tiket/' . esc($p['ticket_token_selector'])) ?>" target="_blank" class="px-2.5 py-1 rounded-lg text-[11px] font-bold text-brand-700 bg-brand-50 hover:bg-brand-100 transition" title="Lihat Tiket">
                                                Tiket
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
