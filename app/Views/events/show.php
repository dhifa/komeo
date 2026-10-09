<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="bg-slate-50 min-h-screen py-10 sm:py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500">
            <a href="<?= base_url() ?>" class="hover:text-brand-600 transition">Beranda</a>
            <span>/</span>
            <a href="<?= base_url('kegiatan') ?>" class="hover:text-brand-600 transition">Kegiatan</a>
            <span>/</span>
            <span class="text-slate-900 truncate max-w-xs"><?= esc($event['title']) ?></span>
        </nav>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 sm:gap-10 items-start">
            
            <!-- Left 2 Cols: Event Detail Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Banner Card -->
                <div class="bg-white rounded-3xl border border-slate-200/80 overflow-hidden shadow-xs">
                    <div class="relative h-64 sm:h-96 w-full bg-slate-900">
                        <?php if (! empty($event['banner_path']) && file_exists(FCPATH . $event['banner_path'])): ?>
                            <img src="<?= base_url(esc($event['banner_path'])) ?>" alt="<?= esc($event['title']) ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-tr from-brand-900 via-indigo-950 to-slate-900 text-brand-400">
                                <svg class="w-20 h-20 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        <?php endif; ?>

                        <!-- Floating Badges -->
                        <div class="absolute top-4 left-4 flex flex-wrap gap-2">
                            <?php if ($event['event_type'] === 'internal'): ?>
                                <span class="px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-indigo-900/90 text-indigo-200 backdrop-blur-md border border-indigo-700/50 shadow-md">
                                    Internal — Khusus Member KOMEO
                                </span>
                            <?php elseif ($event['event_type'] === 'external'): ?>
                                <span class="px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-emerald-900/90 text-emerald-200 backdrop-blur-md border border-emerald-700/50 shadow-md">
                                    External — Peserta Umum
                                </span>
                            <?php else: ?>
                                <span class="px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-amber-900/90 text-amber-200 backdrop-blur-md border border-amber-700/50 shadow-md">
                                    Hybrid — Member & Peserta Umum
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Event Title & Info -->
                    <div class="p-6 sm:p-8 space-y-6">
                        <h1 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                            <?= esc($event['title']) ?>
                        </h1>

                        <!-- Highlights Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                            <div class="flex items-start gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                                <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tanggal & Waktu</span>
                                    <span class="text-xs sm:text-sm font-bold text-slate-900"><?= date('l, d M Y', strtotime($event['start_date'])) ?></span>
                                    <span class="block text-xs text-slate-500"><?= date('H:i', strtotime($event['start_date'])) ?> - <?= date('H:i', strtotime($event['end_date'])) ?> WIB</span>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Lokasi / Venue</span>
                                    <span class="text-xs sm:text-sm font-bold text-slate-900 block truncate"><?= esc($event['venue_name'] ?: 'Lokasi Kegiatan') ?></span>
                                    <span class="block text-xs text-slate-500 truncate"><?= esc($event['address'] ?: ($event['city'] ?: 'Indonesia')) ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="space-y-4 pt-4 border-t border-slate-100">
                            <h2 class="text-base font-extrabold text-slate-900">Deskripsi Lengkap</h2>
                            <div class="prose prose-slate max-w-none text-xs sm:text-sm text-slate-600 leading-relaxed whitespace-pre-line">
                                <?= esc($event['description']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right 1 Col: Registration & Quota Action Card -->
            <div class="space-y-6 sticky top-28">
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-6">
                    <h3 class="text-base font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                        <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                        Pendaftaran & Tiket
                    </h3>

                    <!-- Registration Status Badge -->
                    <div>
                        <?php if ($regActive['active']): ?>
                            <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center gap-3">
                                <div class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></div>
                                <div class="text-xs font-bold text-emerald-800">
                                    Pendaftaran Sedang Dibuka
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-100 flex items-center gap-3">
                                <div class="w-3 h-3 rounded-full bg-rose-500"></div>
                                <div class="text-xs font-bold text-rose-800">
                                    <?= esc($regActive['reason']) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Quota Information -->
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between py-2 border-b border-slate-100 font-semibold">
                            <span class="text-slate-500">Kapasitas Quota Total:</span>
                            <span class="text-slate-900"><?= $event['total_quota'] > 0 ? $event['total_quota'] . ' Peserta' : 'Terbuka' ?></span>
                        </div>
                        <?php if ($event['event_type'] === 'hybrid'): ?>
                            <div class="flex justify-between py-2 border-b border-slate-100 font-semibold">
                                <span class="text-slate-500">Quota Khusus Member:</span>
                                <span class="text-slate-900"><?= $event['member_quota'] > 0 ? $event['member_quota'] : 'Sesuai total' ?></span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-slate-100 font-semibold">
                                <span class="text-slate-500">Quota Peserta Umum:</span>
                                <span class="text-slate-900"><?= $event['external_quota'] > 0 ? $event['external_quota'] : 'Sesuai total' ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="flex justify-between py-2 border-b border-slate-100 font-semibold">
                            <span class="text-slate-500">Batas Waktu Pendaftaran:</span>
                            <span class="text-slate-900"><?= ! empty($event['reg_end_date']) ? date('d M Y, H:i', strtotime($event['reg_end_date'])) . ' WIB' : 'Sebelum Hari-H' ?></span>
                        </div>
                    </div>

                    <!-- Already Registered Status -->
                    <?php if (! empty($memberRegistration)): ?>
                        <div class="p-4 rounded-2xl bg-indigo-50 border border-indigo-100 space-y-3">
                            <div class="flex items-center gap-2 text-xs font-bold text-indigo-900">
                                <svg class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Anda Sudah Terdaftar
                            </div>
                            <p class="text-[11px] text-indigo-700">No. Registrasi: <span class="font-mono font-bold"><?= esc($memberRegistration['registration_number']) ?></span></p>
                            <a href="<?= base_url('kegiatan/tiket/' . esc($memberRegistration['ticket_token_selector'])) ?>" class="block text-center w-full px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition shadow-xs">
                                Buka Tiket QR Saya
                            </a>
                        </div>
                    <?php elseif ($regActive['active']): ?>
                        <!-- Action Pathways -->
                        <div class="space-y-3">
                            <!-- Pathway 1: Member Registration -->
                            <?php if ($event['event_type'] === 'internal' || $event['event_type'] === 'hybrid'): ?>
                                <?php if (auth()->loggedIn()): ?>
                                    <form action="<?= base_url('dashboard/kegiatan/daftar/' . $event['id']) ?>" method="POST">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-5 py-3 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                            Daftar sebagai Member KOMEO
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <a href="<?= base_url('login?redirect=' . urlencode(current_url())) ?>" class="w-full flex items-center justify-center gap-2 px-5 py-3 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                        Login Member untuk Mendaftar
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>

                            <!-- Pathway 2: External Public Registration -->
                            <?php if ($event['event_type'] === 'external' || $event['event_type'] === 'hybrid'): ?>
                                <a href="<?= base_url('kegiatan/' . esc($event['slug']) . '/daftar') ?>" class="w-full flex items-center justify-center gap-2 px-5 py-3 rounded-xl text-xs font-bold <?= $event['event_type'] === 'external' ? 'text-white bg-slate-900 hover:bg-slate-800' : 'text-slate-800 bg-slate-100 hover:bg-slate-200' ?> transition shadow-xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Daftar sebagai Peserta Umum
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Check-in Policy Notice -->
                    <div class="text-[11px] text-slate-400 space-y-1.5 pt-2">
                        <p class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                            Check-in dibuka pada hari pelaksanaan.
                        </p>
                        <?php if ((bool) ($event['allow_kta_checkin'] ?? true)): ?>
                            <p class="flex items-center gap-1.5 text-brand-600 font-semibold">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Mendukung presensi menggunakan KTA Digital Member.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
<?= $this->endSection() ?>
