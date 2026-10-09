<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="space-y-8">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 rounded-3xl p-6 sm:p-8 text-white relative overflow-hidden shadow-xs">
        <div class="max-w-2xl relative z-10 space-y-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-brand-500/20 text-brand-300 border border-brand-500/30">
                <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Program Komunitas
            </span>
            <h1 class="text-xl sm:text-3xl font-extrabold tracking-tight">Kegiatan & Acara Saya</h1>
            <p class="text-xs sm:text-sm text-slate-300">
                Kelola pendaftaran kegiatan, unduh tiket QR acara, dan pantau riwayat presensi kehadiran Anda sebagai Member KOMEO.
            </p>
        </div>
    </div>

    <!-- Section 1: My Registered Events -->
    <div class="space-y-4">
        <h2 class="text-base font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
            <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Tiket & Pendaftaran Saya (<?= count($myRegistrations) ?>)
        </h2>

        <?php if (empty($myRegistrations)): ?>
            <div class="bg-white rounded-3xl border border-slate-200/80 p-8 text-center shadow-xs">
                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-slate-800">Belum Ada Kegiatan yang Diikuti</h3>
                <p class="text-xs text-slate-500 mt-1">Anda belum mendaftar pada kegiatan atau acara apa pun.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <?php foreach ($myRegistrations as $reg): ?>
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col justify-between space-y-4">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="font-mono text-xs font-bold text-brand-600 bg-brand-50 px-2.5 py-1 rounded-md">
                                    <?= esc($reg['registration_number']) ?>
                                </span>
                                <?php if ($reg['status'] === 'confirmed'): ?>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">
                                        TERKONFIRMASI
                                    </span>
                                <?php elseif ($reg['status'] === 'pending_approval'): ?>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800">
                                        MENUNGGU APPROVAL
                                    </span>
                                <?php elseif ($reg['status'] === 'cancelled'): ?>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-600">
                                        DIBATALKAN
                                    </span>
                                <?php else: ?>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800">
                                        DITOLAK
                                    </span>
                                <?php endif; ?>
                            </div>

                            <h3 class="text-base font-bold text-slate-900 line-clamp-1">
                                <?= esc($reg['event_title']) ?>
                            </h3>

                            <div class="text-xs text-slate-500 space-y-1">
                                <p class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <?= date('d M Y - H:i', strtotime($reg['start_date'])) ?> WIB
                                </p>
                                <p class="flex items-center gap-1.5 truncate">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <?= esc($reg['venue_name'] ?: 'Lokasi Acara') ?> (<?= esc($reg['city'] ?: 'Indonesia') ?>)
                                </p>
                            </div>

                            <!-- Attendance Badge -->
                            <div class="pt-2">
                                <?php if (! empty($reg['attendance_id'])): ?>
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-600">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                        Hadir pada: <?= date('d/m/Y H:i', strtotime($reg['checked_in_at'])) ?> WIB
                                    </span>
                                <?php else: ?>
                                    <span class="text-xs text-slate-400">Belum melakukan check-in</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Card Action Buttons -->
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-3">
                            <?php if ($reg['status'] === 'confirmed'): ?>
                                <a href="<?= base_url('kegiatan/tiket/' . esc($reg['ticket_token_selector'])) ?>" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition shadow-xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                                    <span>Buka Tiket QR</span>
                                </a>
                            <?php else: ?>
                                <div></div>
                            <?php endif; ?>

                            <?php if ($reg['status'] === 'confirmed' && empty($reg['attendance_id'])): ?>
                                <form action="<?= base_url('dashboard/kegiatan/batal/' . $reg['id']) ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pendaftaran kegiatan ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="text-xs font-bold text-rose-500 hover:text-rose-700 transition">
                                        Batalkan
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Section 2: Upcoming Events to Explore -->
    <div class="space-y-4 pt-6 border-t border-slate-200">
        <h2 class="text-base font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Agenda Kegiatan Mendatang yang Tersedia
        </h2>

        <?php if (empty($upcomingEvents)): ?>
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 text-center text-xs text-slate-500">
                Tidak ada agenda kegiatan baru yang dapat didaftar saat ini.
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <?php foreach ($upcomingEvents as $evt): ?>
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col justify-between space-y-4">
                        <div class="space-y-2">
                            <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full <?= $evt['event_type'] === 'internal' ? 'bg-indigo-100 text-indigo-800' : 'bg-amber-100 text-amber-800' ?>">
                                <?= $evt['event_type'] === 'internal' ? 'KHUSUS MEMBER' : 'HYBRID' ?>
                            </span>

                            <h3 class="text-sm font-bold text-slate-900 line-clamp-2">
                                <?= esc($evt['title']) ?>
                            </h3>

                            <div class="text-[11px] text-slate-500 space-y-0.5">
                                <p><?= date('d M Y - H:i', strtotime($evt['start_date'])) ?> WIB</p>
                                <p class="truncate"><?= esc($evt['venue_name'] ?: 'Venue') ?></p>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                            <a href="<?= base_url('kegiatan/' . esc($evt['slug'])) ?>" class="text-xs font-bold text-slate-600 hover:text-slate-900">
                                Detail Acara
                            </a>

                            <form action="<?= base_url('dashboard/kegiatan/daftar/' . $evt['id']) ?>" method="POST">
                                <?= csrf_field() ?>
                                <button type="submit" class="px-3.5 py-1.5 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition shadow-xs">
                                    Daftar Sekarang
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>
<?= $this->endSection() ?>
