<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="bg-slate-100 min-h-screen py-10 sm:py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 space-y-6">
        
        <!-- Action Toolbar -->
        <div class="flex flex-wrap items-center justify-between gap-4 print:hidden">
            <a href="<?= base_url('kegiatan/' . esc($registration['event_slug'])) ?>" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-brand-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Acara
            </a>

            <div class="flex items-center gap-3">
                <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition shadow-xs">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak / Print
                </button>

                <a href="<?= base_url('kegiatan/tiket/' . esc($registration['ticket_token_selector']) . '/png') ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition shadow-xs">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Unduh PNG
                </a>

                <a href="<?= base_url('kegiatan/tiket/' . esc($registration['ticket_token_selector']) . '/pdf') ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Unduh PDF
                </a>
            </div>
        </div>

        <!-- Main Ticket Card -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden relative">
            <div class="grid grid-cols-1 md:grid-cols-3">
                
                <!-- Left 2 Cols: Ticket Main Info -->
                <div class="md:col-span-2 p-6 sm:p-10 space-y-6 border-b md:border-b-0 md:border-r border-dashed border-slate-200">
                    <!-- Brand & Type -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-brand-600 text-white font-extrabold flex items-center justify-center text-lg shadow-sm">
                                K
                            </div>
                            <div>
                                <span class="font-extrabold text-base text-slate-900 block tracking-tight">KOMEO.ID</span>
                                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Komunitas Event Organizer Indonesia</span>
                            </div>
                        </div>

                        <span class="px-3 py-1 rounded-full text-xs font-extrabold <?= $registration['participant_type'] === 'member' ? 'bg-indigo-100 text-indigo-800' : 'bg-emerald-100 text-emerald-800' ?>">
                            <?= $registration['participant_type'] === 'member' ? 'MEMBER KOMEO' : 'PESERTA UMUM' ?>
                        </span>
                    </div>

                    <!-- Event Title & Date -->
                    <div class="space-y-2 pt-2">
                        <span class="text-[11px] font-bold text-brand-600 uppercase tracking-wider block">Tiket Masuk Resmi</span>
                        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight leading-snug">
                            <?= esc($registration['event_title']) ?>
                        </h1>
                        <p class="text-xs text-slate-500 flex items-center gap-1.5 pt-1">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span><?= date('l, d F Y - H:i', strtotime($registration['event_start_date'])) ?> WIB</span>
                        </p>
                        <p class="text-xs text-slate-500 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span><?= esc($registration['venue_name'] ?: 'Venue Acara') ?>, <?= esc($registration['event_city'] ?: 'Indonesia') ?></span>
                        </p>
                    </div>

                    <!-- Participant Details Grid -->
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-100 text-xs">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Nama Peserta</span>
                            <span class="font-extrabold text-slate-900 text-sm block"><?= esc($registration['participant_name']) ?></span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">No. Registrasi</span>
                            <span class="font-mono font-bold text-brand-700 block"><?= esc($registration['registration_number']) ?></span>
                        </div>
                        <?php if ($registration['participant_type'] === 'member' && ! empty($registration['membership_number'])): ?>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Nomor Anggota</span>
                                <span class="font-mono font-bold text-slate-800 block"><?= esc($registration['membership_number']) ?></span>
                            </div>
                        <?php else: ?>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Instansi / Usaha</span>
                                <span class="font-bold text-slate-800 block truncate"><?= esc($registration['company'] ?: '-') ?></span>
                            </div>
                        <?php endif; ?>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Status Kehadiran</span>
                            <?php if ($registration['is_attended']): ?>
                                <span class="inline-flex items-center gap-1 font-bold text-emerald-600">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    Sudah Hadir
                                </span>
                            <?php else: ?>
                                <span class="font-bold text-slate-600">Belum Check-in</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Ticket Notice -->
                    <div class="text-[11px] text-slate-400 space-y-1 pt-2">
                        <p>* Tunjukkan kode QR di samping kepada petugas panitia di pintu masuk.</p>
                        <p>* Tiket ini berlaku untuk 1 (satu) kali check-in pada waktu pelaksanaan acara.</p>
                    </div>
                </div>

                <!-- Right 1 Col: QR Ticket Stub -->
                <div class="bg-slate-50/70 p-6 sm:p-8 flex flex-col items-center justify-center text-center space-y-4">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">KODE QR CHECK-IN</span>

                    <!-- QR Code Display -->
                    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-md">
                        <?php
                            $ticketService = new \App\Services\EventTicketService();
                            $ticketUrl = site_url('kegiatan/tiket/' . $registration['ticket_token_selector']);
                            $qrBase64 = base64_encode($ticketService->generateQrCodeImage($ticketUrl));
                        ?>
                        <img src="data:image/png;base64,<?= $qrBase64 ?>" alt="QR Ticket" class="w-44 h-44 object-contain mx-auto">
                    </div>

                    <div class="space-y-1">
                        <span class="font-mono text-xs font-bold text-slate-700 block">
                            <?= esc($registration['registration_number']) ?>
                        </span>
                        <span class="text-[10px] text-slate-400 block">
                            Verifikasi Terenkripsi KOMEO
                        </span>
                    </div>

                    <div class="pt-2 text-[10px] text-brand-600 font-bold uppercase tracking-wider">
                        STATUS: <?= strtoupper(esc($registration['status'])) ?>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
<?= $this->endSection() ?>
