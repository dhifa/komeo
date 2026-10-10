<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<div class="space-y-8">
    <!-- Welcome Header Card -->
    <div class="relative overflow-hidden bg-gradient-to-r from-slate-900 via-brand-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl">
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-brand-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-4 sm:gap-6">
                <!-- Avatar -->
                <div class="relative">
                    <img src="<?= esc($profile->getAvatarUrl()) ?>" 
                         alt="<?= esc($profile->display_name ?? 'Member') ?>" 
                         class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover ring-4 ring-white/10 shadow-lg bg-slate-800 shrink-0">
                    <?php if (($membership->status ?? 'pending') === 'active'): ?>
                        <span class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center ring-2 ring-slate-900 shadow-sm" title="Member Terverifikasi">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Identity Information -->
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                        <h2 class="text-xl sm:text-2xl font-black tracking-tight text-white flex items-center gap-1.5 flex-wrap">
                            <span><?= esc($profile->display_name ?? $profile->full_name ?? auth()->user()->username) ?></span>
                            <?= komeo_verification_badge($verification ?? null, $profile->member_type ?? 'individual', $membership->status ?? '', 'md') ?>
                        </h2>
                        <!-- Membership Status Badge -->
                        <?php 
                            $status = $membership->status ?? 'pending';
                            $statusBadgeClass = match($status) {
                                'active'    => 'bg-emerald-500/20 text-emerald-300 ring-1 ring-emerald-500/30',
                                'pending'   => 'bg-amber-500/20 text-amber-300 ring-1 ring-amber-500/30',
                                'suspended' => 'bg-rose-500/20 text-rose-300 ring-1 ring-rose-500/30',
                                default     => 'bg-slate-500/20 text-slate-300 ring-1 ring-slate-500/30',
                            };
                            $statusText = match($status) {
                                'active'    => 'Anggota Aktif',
                                'pending'   => 'Menunggu Verifikasi',
                                'suspended' => 'Ditangguhkan',
                                default     => ucfirst($status),
                            };
                        ?>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold <?= $statusBadgeClass ?>">
                            <?= $statusText ?>
                        </span>

                        <!-- Member Type Badge -->
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/10 text-slate-200">
                            <?= $profile->isBusiness() ? 'Perusahaan / Bisnis' : 'Individu / Freelancer' ?>
                        </span>

                        <!-- Custom Member Badges (Phase 5 Extension) -->
                        <?php if (! empty($myBadges)): ?>
                            <?php foreach ($myBadges as $mb): ?>
                                <?= komeo_render_badge($mb, 'xs', true) ?>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- Custom Member Roles (Phase 6.3) -->
                        <?php if (! empty($myRoles)): ?>
                            <?php foreach ($myRoles as $mr): ?>
                                <?= komeo_render_member_role($mr, 'xs', true) ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <p class="text-xs sm:text-sm text-slate-300 font-medium flex flex-wrap items-center gap-x-2 gap-y-1">
                        <span>Nomor Anggota:</span>
                        <?php if (! empty($membership->member_number)): ?>
                            <span class="font-mono text-brand-300 font-bold bg-white/10 px-2 py-0.5 rounded-md tracking-wider">
                                <?= esc($membership->member_number) ?>
                            </span>
                            <?php if ($appDate = $membership->getFormattedApprovalDate()): ?>
                                <span class="text-slate-400 text-xs">(Disetujui: <?= esc($appDate) ?>)</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="font-mono text-amber-300 text-xs italic bg-amber-500/20 px-2 py-0.5 rounded-md">
                                Belum Diterbitkan (Menunggu Verifikasi)
                            </span>
                        <?php endif; ?>
                        <span class="text-slate-400">&bull; @<?= esc($profile->username ?? auth()->user()->username) ?></span>
                    </p>

                    <p class="text-xs text-slate-400 flex items-center gap-1.5 pt-0.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span><?= esc($profile->city ?: 'Kota belum disetel') ?><?= $profile->province ? ', ' . esc($profile->province) : '' ?></span>
                        <?php if ($category): ?>
                            <span class="mx-1">&bull;</span>
                            <span class="text-brand-300 font-semibold"><?= esc($category['name']) ?></span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>

            <!-- Quick Action Buttons & Public Status -->
            <div class="flex flex-col sm:flex-row md:flex-col gap-2.5 shrink-0">
                <a href="<?= base_url('dashboard/profil/edit') ?>" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold bg-white text-slate-900 hover:bg-slate-100 transition-colors shadow-sm">
                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Profil
                </a>
                <a href="<?= base_url('member/' . esc($profile->username)) ?>" target="_blank" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold bg-white/10 hover:bg-white/20 text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <?= ($membership->status ?? '') === 'active' ? 'Lihat Profil Publik' : 'Pratinjau Profil' ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Member Warning Alerts (SP1, SP2, SP3) -->
    <?php if (! empty($warnings)): ?>
        <div class="space-y-4">
            <?php foreach ($warnings as $warn): ?>
                <?php $warnObj = (object) $warn; ?>
                <?php if ($warnObj->warning_level === 'sp3'): ?>
                    <!-- SP3 Alert: Crimson Executive Card -->
                    <div class="relative overflow-hidden bg-gradient-to-r from-rose-950 via-slate-900 to-rose-950 text-white rounded-3xl p-6 sm:p-7 border-2 border-rose-500/80 shadow-2xl space-y-4">
                        <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-rose-500/20 rounded-full blur-3xl pointer-events-none"></div>
                        
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-rose-500/30 pb-4">
                            <div class="flex items-center gap-3">
                                <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-rose-600 text-white font-black shadow-lg ring-4 ring-rose-500/30">
                                    <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </span>
                                <div>
                                    <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full text-[11px] font-black uppercase tracking-wider bg-rose-500/30 text-rose-300 ring-1 ring-rose-400/50">
                                        Surat Peringatan 3 (SP3) &bull; Sanksi Penangguhan
                                    </div>
                                    <h3 class="text-base sm:text-lg font-black text-white tracking-tight mt-1">
                                        Keanggotaan Anda Sedang Ditangguhkan (Suspended)
                                    </h3>
                                </div>
                            </div>
                            <span class="text-xs font-mono text-rose-300/80 shrink-0">
                                Diterbitkan: <?= date('d M Y H:i', strtotime($warnObj->created_at)) ?>
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                            <div class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-1">
                                <span class="text-[10px] uppercase font-bold text-rose-400 tracking-wider block">Alasan Sanksi / Pelanggaran:</span>
                                <p class="text-rose-100 text-sm leading-relaxed font-medium"><?= nl2br(esc($warnObj->reason)) ?></p>
                            </div>
                            <?php if (! empty($warnObj->notes)): ?>
                                <div class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-1">
                                    <span class="text-[10px] uppercase font-bold text-amber-300 tracking-wider block">Catatan / Arahan Admin:</span>
                                    <p class="text-slate-300 text-xs leading-relaxed"><?= nl2br(esc($warnObj->notes)) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                            <p class="text-rose-200">
                                <strong>Konsekuensi:</strong> Akses Kartu Tanda Anggota (KTA) digital dinonaktifkan dan profil Anda tidak ditampilkan di direktori publik hingga verifikasi klarifikasi disetujui.
                            </p>
                            <a href="<?= base_url('dashboard/profil') ?>" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-white text-rose-950 font-bold hover:bg-rose-50 transition shrink-0 shadow-sm text-xs">
                                Periksa Data Akun
                            </a>
                        </div>
                    </div>

                <?php elseif ($warnObj->warning_level === 'sp2'): ?>
                    <!-- SP2 Alert: Orange Warning Card -->
                    <div class="p-5 sm:p-6 bg-gradient-to-r from-orange-50 via-amber-50 to-orange-50 rounded-3xl border-2 border-orange-300 shadow-sm space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-orange-200 pb-3">
                            <div class="flex items-center gap-2.5">
                                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-orange-600 text-white font-bold shadow-xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </span>
                                <div>
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-orange-100 text-orange-900 border border-orange-300">
                                        Surat Peringatan 2 (SP2) &bull; Peringatan Keras
                                    </span>
                                    <h4 class="text-sm font-extrabold text-orange-950 mt-0.5">Teguran Keras Terakhir Sebelum Penangguhan Akun</h4>
                                </div>
                            </div>
                            <span class="text-[11px] font-mono text-orange-700">
                                <?= date('d M Y H:i', strtotime($warnObj->created_at)) ?>
                            </span>
                        </div>

                        <div class="text-xs space-y-2">
                            <div class="p-3 bg-white/80 rounded-xl border border-orange-200">
                                <span class="font-bold text-orange-950 block mb-0.5">Alasan Peringatan:</span>
                                <p class="text-slate-800"><?= nl2br(esc($warnObj->reason)) ?></p>
                            </div>
                            <?php if (! empty($warnObj->notes)): ?>
                                <div class="p-3 bg-white/60 rounded-xl border border-orange-200/80">
                                    <span class="font-bold text-slate-700 block mb-0.5">Arahan Perbaikan:</span>
                                    <p class="text-slate-600"><?= nl2br(esc($warnObj->notes)) ?></p>
                                </div>
                            <?php endif; ?>
                            <p class="text-[11px] text-orange-900 font-semibold pt-1">
                                &bull; Harap segera melakukan perbaikan kepatuhan. Pengulangan pelanggaran akan memicu terbitnya SP3 (penangguhan akun otomatis).
                            </p>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- SP1 Alert: Amber / Yellow Warning Card -->
                    <div class="p-5 bg-gradient-to-r from-amber-50 via-yellow-50 to-amber-50 rounded-3xl border border-amber-300 shadow-xs space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-amber-200 pb-3">
                            <div class="flex items-center gap-2.5">
                                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-amber-500 text-slate-950 font-bold shadow-xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </span>
                                <div>
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-amber-200/80 text-amber-950 border border-amber-300">
                                        Surat Peringatan 1 (SP1) &bull; Teguran Resmi
                                    </span>
                                    <h4 class="text-sm font-extrabold text-amber-950 mt-0.5">Peringatan Pertama Mengenai Aktivitas / Profil Akun</h4>
                                </div>
                            </div>
                            <span class="text-[11px] font-mono text-amber-800">
                                <?= date('d M Y H:i', strtotime($warnObj->created_at)) ?>
                            </span>
                        </div>

                        <div class="text-xs space-y-2">
                            <div class="p-3 bg-white/80 rounded-xl border border-amber-200">
                                <span class="font-bold text-amber-950 block mb-0.5">Alasan Peringatan:</span>
                                <p class="text-slate-800"><?= nl2br(esc($warnObj->reason)) ?></p>
                            </div>
                            <?php if (! empty($warnObj->notes)): ?>
                                <div class="p-3 bg-white/60 rounded-xl border border-amber-200/80">
                                    <span class="font-bold text-slate-700 block mb-0.5">Catatan Admin:</span>
                                    <p class="text-slate-600"><?= nl2br(esc($warnObj->notes)) ?></p>
                                </div>
                            <?php endif; ?>
                            <p class="text-[11px] text-amber-800 font-medium">
                                Harap perhatikan tata tertib dan panduan komunitas KOMEO.ID agar akun tetap memiliki reputasi prima.
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Membership Status Description Banner -->
    <?php 
    $memStatus = $membership->status ?? 'pending';
    ?>
    <?php if ($memStatus === 'pending'): ?>
        <?php
        $appRef = $activationRequest['application_reference'] ?? ('APP-' . date('Ymd') . '-PENDING');
        $actReqStatus = $activationRequest['status'] ?? 'awaiting_contact';
        
        $adminWa = site_setting('Membership.activation_whatsapp', '081234567890');
        $adminEmail = site_setting('Membership.activation_email', 'admin@komeo.id');
        $enableWa = site_setting('Membership.enable_whatsapp', '1') === '1' && ! empty($adminWa);
        $enableEmail = site_setting('Membership.enable_email', '1') === '1' && ! empty($adminEmail);
        $btnLabel = site_setting('Membership.activation_btn_label', 'Hubungi Admin untuk Aktivasi');
        $instructions = site_setting('Membership.activation_instructions', 'Akun Anda berhasil dibuat. Untuk mengaktifkan keanggotaan KOMEO.ID dan mendapatkan KTA resmi, silakan hubungi Admin KOMEO untuk proses verifikasi dan persetujuan.');

        $memberName = $profile->display_name ?? $profile->full_name ?? auth()->user()->username;

        // Dynamic WhatsApp message template (non-sensitive reference)
        $cleanWa = preg_replace('/[^0-9]/', '', $adminWa);
        if (str_starts_with($cleanWa, '0')) {
            $cleanWa = '62' . substr($cleanWa, 1);
        }
        $rawWaMsg = "Halo Admin KOMEO, saya sudah melakukan registrasi dan ingin mengajukan aktivasi keanggotaan KOMEO.ID.\n\nNama: {$memberName}\nNomor Pengajuan: {$appRef}\n\nMohon informasi proses aktivasi keanggotaan. Terima kasih.";
        $waUrl = 'https://wa.me/' . $cleanWa . '?text=' . rawurlencode($rawWaMsg);

        // Email mailto URL
        $emailSubject = rawurlencode("Pengajuan Aktivasi Keanggotaan KOMEO.ID - {$appRef}");
        $emailBody = rawurlencode($rawWaMsg);
        $emailUrl = "mailto:{$adminEmail}?subject={$emailSubject}&body={$emailBody}";
        ?>
        <!-- Prominent Activation Section (Module B) -->
        <div class="relative overflow-hidden bg-gradient-to-br from-amber-500/10 via-amber-50/70 to-orange-500/10 rounded-3xl p-6 sm:p-8 border-2 border-amber-300 shadow-md space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-amber-200/80 pb-5">
                <div class="flex items-start gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-500 to-amber-400 text-slate-950 font-black flex items-center justify-center shrink-0 shadow-md ring-4 ring-amber-400/20">
                        <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full text-[11px] font-black uppercase tracking-wider bg-amber-200/80 text-amber-950 border border-amber-300 mb-1">
                            Tahap Verifikasi Manual
                        </div>
                        <h3 class="text-lg sm:text-xl font-black text-amber-950 tracking-tight">
                            Aktivasi Keanggotaan KOMEO
                        </h3>
                        <p class="text-xs sm:text-sm text-amber-900/90 mt-1 leading-relaxed max-w-2xl">
                            <?= esc($instructions) ?>
                        </p>
                    </div>
                </div>

                <div class="flex flex-col items-start md:items-end gap-1.5 shrink-0 bg-white/80 p-3.5 rounded-2xl border border-amber-200 shadow-2xs">
                    <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Nomor Pengajuan:</span>
                    <span class="font-mono text-sm sm:text-base font-extrabold text-brand-700 bg-brand-50 px-2.5 py-1 rounded-lg border border-brand-200">
                        <?= esc($appRef) ?>
                    </span>
                    <span class="text-[11px] font-semibold text-amber-800">
                        <?= $actReqStatus === 'awaiting_review' ? '✓ Kontak Diterima Admin' : '• Menunggu Kontak Pemohon' ?>
                    </span>
                </div>
            </div>

            <!-- Completeness & Action Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <!-- Status & Completion Summary -->
                <div class="bg-white/90 p-4 rounded-2xl border border-amber-200/80 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-800">Progres Kelengkapan Profil</span>
                        <span class="font-extrabold text-amber-900 font-mono"><?= $completion['percentage'] ?>%</span>
                    </div>
                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                        <div class="bg-amber-500 h-full rounded-full transition-all duration-300" style="width: <?= $completion['percentage'] ?>%"></div>
                    </div>
                    <p class="text-[11px] text-slate-500">
                        Semakin lengkap data usaha dan portofolio Anda, semakin cepat admin dapat memverifikasi akun Anda.
                    </p>
                </div>

                <!-- Call-To-Action Buttons -->
                <div class="bg-white/90 p-4 rounded-2xl border border-amber-200/80 flex flex-col justify-between gap-3">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Langkah Selanjutnya:</span>
                        <p class="text-slate-700 font-medium text-xs leading-relaxed">
                            Hubungi Administrator resmi KOMEO.ID untuk konfirmasi identitas dan proses aktivasi keanggotaan.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 pt-1">
                        <?php if ($enableWa): ?>
                            <a href="<?= esc($waUrl) ?>" 
                               target="_blank" 
                               rel="noopener noreferrer" 
                               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                <span><?= esc($btnLabel) ?></span>
                            </a>
                        <?php elseif ($enableEmail): ?>
                            <a href="<?= esc($emailUrl) ?>" 
                               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-brand-600 hover:bg-brand-700 shadow-sm transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span>Kirim Email Verifikasi</span>
                            </a>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold">
                                Hubungi pengurus KOMEO di sekretariat atau kontak resmi kami.
                            </span>
                        <?php endif; ?>

                        <a href="<?= base_url('dashboard/profil/edit') ?>" 
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-amber-950 bg-amber-200/70 hover:bg-amber-200 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Lengkapi Profil</span>
                        </a>
                    </div>
                </div>
            </div>

            <p class="text-[11px] text-amber-900/75 italic">
                * Pemberitahuan: Membuka kontak WhatsApp tidak secara otomatis mengaktifkan status keanggotaan. Keanggotaan akan diaktifkan secara resmi setelah administrator menyelesaikan verifikasi.
            </p>
        </div>
    <?php elseif ($memStatus === 'active'): ?>
        <div class="p-5 bg-emerald-50 rounded-3xl border border-emerald-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="w-9 h-9 rounded-2xl bg-emerald-600 text-white font-bold flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <h4 class="text-sm font-extrabold text-emerald-950">Keanggotaan KOMEO Anda telah aktif.</h4>
                    <p class="text-xs text-emerald-800/90 mt-0.5">
                        Profil Anda kini resmi terdaftar dan dapat ditemukan oleh mitra kolaborasi di ekosistem event KOMEO.ID.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-100 text-emerald-800 text-xs font-bold whitespace-nowrap">
                    <?= $profile->is_public ? 'Profil Publik Aktif' : 'Visibilitas Privat' ?>
                </span>
            </div>
        </div>
    <?php elseif ($memStatus === 'rejected'): ?>
        <div class="p-5 bg-rose-50 rounded-3xl border border-rose-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="w-9 h-9 rounded-2xl bg-rose-600 text-white font-bold flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <div>
                    <h4 class="text-sm font-extrabold text-rose-950">Permohonan keanggotaan belum disetujui.</h4>
                    <p class="text-xs text-rose-800/90 mt-0.5">
                        <?= esc($membership->rejection_reason ?: 'Silakan periksa kembali kelengkapan profil Anda atau hubungi pengurus KOMEO.ID.') ?>
                    </p>
                </div>
            </div>
            <a href="<?= base_url('dashboard/profil/edit') ?>" class="px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition whitespace-nowrap">
                Perbaiki Profil
            </a>
        </div>
    <?php elseif ($memStatus === 'suspended'): ?>
        <div class="p-5 bg-orange-50 rounded-3xl border border-orange-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="w-9 h-9 rounded-2xl bg-orange-600 text-white font-bold flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h4 class="text-sm font-extrabold text-orange-950">Keanggotaan Anda saat ini ditangguhkan.</h4>
                    <p class="text-xs text-orange-800/90 mt-0.5">
                        <?= esc($membership->suspension_reason ?: 'Hubungi tim administrator KOMEO.ID untuk klarifikasi keanggotaan Anda.') ?>
                    </p>
                </div>
            </div>
            <span class="inline-flex items-center px-3 py-1.5 rounded-xl bg-orange-200 text-orange-950 text-xs font-bold whitespace-nowrap">
                Akun Ditangguhkan
            </span>
        </div>
    <?php endif; ?>

    <!-- Profile Completion Progress Widget -->
    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <span>Kelengkapan Profil Member</span>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full <?= $completion['percentage'] === 100 ? 'bg-emerald-100 text-emerald-700' : 'bg-brand-50 text-brand-700' ?>">
                        <?= $completion['percentage'] ?>%
                    </span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Lengkapi data profil Anda agar menarik klien dan rekanan kolaborasi di direktori event.</p>
            </div>
            <?php if ($completion['percentage'] < 100): ?>
                <a href="<?= base_url('dashboard/profil/edit') ?>" class="text-xs font-bold text-brand-600 hover:text-brand-700 inline-flex items-center gap-1 shrink-0">
                    Lengkapi Sekarang &rarr;
                </a>
            <?php endif; ?>
        </div>

        <!-- Progress Bar -->
        <div class="w-full bg-slate-100 h-3 rounded-full overflow-hidden mb-4">
            <div class="h-full rounded-full transition-all duration-500 <?= $completion['percentage'] === 100 ? 'bg-emerald-500' : 'bg-brand-600' ?>" 
                 style="width: <?= $completion['percentage'] ?>%;"></div>
        </div>

        <?php if (! empty($completion['missing_fields'])): ?>
            <div class="pt-2 border-t border-slate-100">
                <p class="text-xs font-bold text-slate-600 mb-2">Item yang belum dilengkapi:</p>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($completion['missing_fields'] as $missing): ?>
                        <span class="inline-flex items-center gap-1 text-2xs font-semibold px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 border border-amber-200/60">
                            <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <?= esc($missing) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <p class="text-xs font-semibold text-emerald-700 flex items-center gap-1.5 pt-1">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Hebat! Profil Anda telah lengkap 100%. Profil Anda siap tampil optimal di direktori publik KOMEO.ID.
            </p>
        <?php endif; ?>
    </div>

    <!-- Live Transaksi KOMEO Monitoring Widget (Phase 6.3) -->
    <?php if ($memStatus === 'active'): ?>
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-xs space-y-6" id="komeo-live-trx-widget">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                <div>
                    <div class="flex items-center gap-2.5">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-brand-600"></span>
                        </span>
                        <h3 class="text-lg font-black tracking-tight text-slate-900">Live Transaksi KOMEO</h3>
                        <span class="px-2.5 py-0.5 rounded-full text-2xs font-extrabold uppercase tracking-wider bg-brand-50 text-brand-700 ring-1 ring-brand-200">
                            Monitoring & Transparansi
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1 flex flex-wrap items-center gap-x-2 gap-y-0.5">
                        <span>Aktivitas progres pekerjaan dan monitoring status transaksi komunitas.</span>
                        <span class="text-slate-400">&bull; Data diperbarui manual oleh administrator KOMEO.</span>
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <span class="text-2xs text-slate-400 font-mono" id="trx-sync-indicator">
                        Sinkronisasi: <span id="trx-last-sync"><?= date('H:i') ?></span> WIB
                    </span>
                    <a href="<?= base_url('dashboard/transaksi') ?>" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-brand-600 hover:bg-brand-700 text-white transition-colors shadow-xs">
                        <span>Lihat Semua Direktori</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>

            <!-- Statistics Grid (6 Metrics) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Transaksi</span>
                    <span class="text-xl font-black text-slate-900 mt-1 block" id="stat-total"><?= esc($trxStats['total'] ?? 0) ?></span>
                </div>
                <div class="p-3.5 rounded-2xl bg-blue-50/70 border border-blue-100">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-blue-700 block">Sedang Berjalan</span>
                    <span class="text-xl font-black text-blue-900 mt-1 block" id="stat-in_progress"><?= esc($trxStats['in_progress'] ?? 0) ?></span>
                </div>
                <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-100">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 block">Pekerjaan Selesai</span>
                    <span class="text-xl font-black text-emerald-900 mt-1 block" id="stat-completed"><?= esc($trxStats['completed'] ?? 0) ?></span>
                </div>
                <div class="p-3.5 rounded-2xl bg-amber-50/70 border border-amber-100">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700 block">Menunggu Bayar</span>
                    <span class="text-xl font-black text-amber-900 mt-1 block" id="stat-awaiting_payment"><?= esc($trxStats['awaiting_payment'] ?? 0) ?></span>
                </div>
                <div class="p-3.5 rounded-2xl bg-purple-50/70 border border-purple-100">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-purple-700 block">DP Diterima</span>
                    <span class="text-xl font-black text-purple-900 mt-1 block" id="stat-dp_received"><?= esc($trxStats['dp_received'] ?? 0) ?></span>
                </div>
                <div class="p-3.5 rounded-2xl bg-teal-50/70 border border-teal-100">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-teal-700 block">Lunas</span>
                    <span class="text-xl font-black text-teal-900 mt-1 block" id="stat-paid"><?= esc($trxStats['paid'] ?? 0) ?></span>
                </div>
            </div>

            <!-- Recent 5 Transactions Section -->
            <div class="space-y-3 pt-2">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500">5 Transaksi Terbaru Dipublikasikan</h4>
                    <span class="text-2xs text-slate-400">Pembaruan otomatis berkala</span>
                </div>

                <div id="recent-trx-container" class="space-y-3">
                    <?php if (empty($recentTrx)): ?>
                        <div class="text-center py-10 px-4 rounded-2xl bg-slate-50 border border-dashed border-slate-200">
                            <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <p class="text-xs font-semibold text-slate-500">Belum ada transaksi yang dipublikasikan.</p>
                            <p class="text-2xs text-slate-400 mt-0.5">Administrator akan merilis pembaruan progres transaksi proyek yang sedang berjalan di sini.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($recentTrx as $trx): ?>
                            <?php
                                $stage = $trx['current_work_stage'] ?? 'masuk';
                                $pStatus = $trx['payment_status'] ?? 'unpaid';
                                $percent = match($stage) {
                                    'masuk'       => 15,
                                    'verifikasi'  => 30,
                                    'negosiasi'   => 50,
                                    'proses'      => 75,
                                    'selesai'     => 100,
                                    'ditunda'     => 40,
                                    'dibatalkan'  => 0,
                                    default       => 20,
                                };
                                $stageLabels = [
                                    'masuk'      => ['label' => 'Transaksi Masuk', 'bg' => 'bg-slate-100 text-slate-700'],
                                    'verifikasi' => ['label' => 'Verifikasi', 'bg' => 'bg-blue-100 text-blue-800'],
                                    'negosiasi'  => ['label' => 'Negosiasi', 'bg' => 'bg-indigo-100 text-indigo-800'],
                                    'proses'     => ['label' => 'Dalam Proses', 'bg' => 'bg-purple-100 text-purple-800'],
                                    'selesai'    => ['label' => 'Selesai', 'bg' => 'bg-emerald-100 text-emerald-800'],
                                    'ditunda'    => ['label' => 'Ditunda', 'bg' => 'bg-amber-100 text-amber-800'],
                                    'dibatalkan' => ['label' => 'Dibatalkan', 'bg' => 'bg-rose-100 text-rose-800'],
                                ];
                                $paymentLabels = [
                                    'unpaid'         => ['label' => 'Belum Ada Pembayaran', 'bg' => 'bg-slate-100 text-slate-600'],
                                    'awaiting_dp'    => ['label' => 'Menunggu DP', 'bg' => 'bg-amber-100 text-amber-800'],
                                    'dp_received'    => ['label' => 'DP Diterima', 'bg' => 'bg-purple-100 text-purple-800'],
                                    'partially_paid' => ['label' => 'Pembayaran Sebagian', 'bg' => 'bg-indigo-100 text-indigo-800'],
                                    'paid'           => ['label' => 'Lunas', 'bg' => 'bg-emerald-100 text-emerald-800'],
                                    'overdue'        => ['label' => 'Terlambat Bayar', 'bg' => 'bg-rose-100 text-rose-800'],
                                ];
                                $sBadge = $stageLabels[$stage] ?? ['label' => ucfirst($stage), 'bg' => 'bg-slate-100 text-slate-700'];
                                $pBadge = $paymentLabels[$pStatus] ?? ['label' => ucfirst($pStatus), 'bg' => 'bg-slate-100 text-slate-700'];
                            ?>
                            <div class="p-4 rounded-2xl bg-slate-50/70 border border-slate-200/80 hover:border-brand-300 hover:bg-white transition-all space-y-3">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-mono text-2xs font-extrabold text-brand-700 bg-brand-50 px-2 py-0.5 rounded-md border border-brand-200">
                                            <?= esc($trx['transaction_code']) ?>
                                        </span>
                                        <h5 class="text-sm font-extrabold text-slate-900"><?= esc($trx['public_title'] ?: $trx['title']) ?></h5>
                                        <span class="text-2xs text-slate-500 font-medium">&bull; <?= esc($trx['category_name'] ?? 'Event') ?></span>
                                    </div>
                                    <span class="text-2xs text-slate-400 font-mono">
                                        <?= date('d M Y', strtotime($trx['transaction_date'])) ?>
                                    </span>
                                </div>

                                <!-- Stage progress bar -->
                                <div>
                                    <div class="flex items-center justify-between text-2xs font-semibold mb-1">
                                        <span class="text-slate-600">Progres Alur Kerja: <strong class="text-slate-900"><?= $sBadge['label'] ?></strong></span>
                                        <span class="text-slate-400"><?= $percent ?>%</span>
                                    </div>
                                    <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                                        <div class="bg-gradient-to-r from-brand-600 to-indigo-600 h-full rounded-full transition-all duration-300" style="width: <?= $percent ?>%;"></div>
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 pt-1 text-2xs">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="px-2 py-0.5 rounded-full font-bold <?= $sBadge['bg'] ?>">
                                            <?= $sBadge['label'] ?>
                                        </span>
                                        <span class="px-2 py-0.5 rounded-full font-bold <?= $pBadge['bg'] ?>">
                                            <?= $pBadge['label'] ?>
                                        </span>
                                        <?php if (! empty($trx['contract_issue_member_label'])): ?>
                                            <span class="px-2 py-0.5 rounded-full font-bold bg-amber-100 text-amber-800">
                                                <?= esc($trx['contract_issue_member_label']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <span class="text-slate-400 text-2xs">Update: <?= date('d M Y H:i', strtotime($trx['updated_at'])) ?></span>
                                        <a href="<?= base_url('dashboard/transaksi/' . $trx['id']) ?>" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 hover:text-brand-600 hover:border-brand-400 font-bold transition-colors shadow-2xs">
                                            Lihat Detail &rarr;
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- AJAX Live Polling Script (Phase 6.3) -->
        <script>
        (function() {
            const pollUrl = '<?= base_url('dashboard/transaksi/poll') ?>';
            const pollIntervalMs = <?= (int) site_setting('Transaction.live_refresh_interval', 60) * 1000 ?>;
            let isPolling = false;

            async function refreshTransactions() {
                if (document.hidden || isPolling) return;
                isPolling = true;

                try {
                    const response = await fetch(pollUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) throw new Error('Status ' + response.status);
                    const data = await response.json();

                    if (data && data.stats) {
                        const statMap = {
                            'stat-total': data.stats.total,
                            'stat-in_progress': data.stats.in_progress,
                            'stat-completed': data.stats.completed,
                            'stat-awaiting_payment': data.stats.awaiting_payment,
                            'stat-dp_received': data.stats.dp_received,
                            'stat-paid': data.stats.paid
                        };
                        for (const [id, val] of Object.entries(statMap)) {
                            const el = document.getElementById(id);
                            if (el && val !== undefined) el.textContent = val;
                        }
                    }

                    const syncEl = document.getElementById('trx-last-sync');
                    if (syncEl) {
                        const now = new Date();
                        syncEl.textContent = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
                    }
                } catch (err) {
                    console.debug('[KOMEO Live Trx] Refresh skipped:', err.message);
                } finally {
                    isPolling = false;
                }
            }

            // Set recurring polling interval
            setInterval(refreshTransactions, Math.max(15000, pollIntervalMs));

            // Resume polling when tab becomes visible
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) refreshTransactions();
            });
        })();
        </script>
    <?php endif; ?>

    <!-- Grid: Identity Overview & KTA Placeholder -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8">
        <!-- Col Left (7): Detailed Information Cards -->
        <div class="lg:col-span-7 space-y-6">
            <!-- Professional Summary Card -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-xs space-y-5">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Informasi Profesional
                    </h3>
                    <a href="<?= base_url('dashboard/profil/edit') ?>" class="text-xs font-bold text-brand-600 hover:text-brand-700">
                        Ubah
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 font-bold uppercase tracking-wider block mb-1">Kategori Utama</span>
                        <span class="font-extrabold text-slate-900 text-sm">
                            <?= esc($category['name'] ?? 'Belum dipilih') ?>
                        </span>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 font-bold uppercase tracking-wider block mb-1">Tipe Member</span>
                        <span class="font-extrabold text-slate-900 text-sm">
                            <?= $profile->isBusiness() ? 'Perusahaan / Vendor' : 'Individu / Freelancer' ?>
                        </span>
                    </div>

                    <?php if ($profile->isBusiness()): ?>
                        <div class="sm:col-span-2 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <span class="text-slate-400 font-bold uppercase tracking-wider block mb-1">Nama Perusahaan / Bisnis</span>
                            <span class="font-extrabold text-slate-900 text-sm">
                                <?= esc($profile->business_name ?: '-') ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <div class="sm:col-span-2 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 font-bold uppercase tracking-wider block mb-1.5">Keahlian Spesialisasi (<?= count($specializations) ?>/5)</span>
                        <?php if (! empty($specializations)): ?>
                            <div class="flex flex-wrap gap-1.5">
                                <?php foreach ($specializations as $sp): ?>
                                    <span class="px-2.5 py-1 rounded-lg bg-brand-50 text-brand-700 font-bold text-xs border border-brand-100">
                                        <?= esc($sp['name']) ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <span class="text-slate-400 italic">Belum memilih spesialisasi</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Bio Snippet -->
                <?php if (! empty($profile->bio)): ?>
                    <div class="pt-2">
                        <span class="text-xs font-bold text-slate-700 block mb-1">Biografi:</span>
                        <p class="text-xs text-slate-600 leading-relaxed bg-slate-50 p-4 rounded-2xl border border-slate-100 whitespace-pre-line">
                            <?= esc($profile->bio) ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Portfolio Overview Card -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Portofolio Event Terbaru (<?= count($portfolios) ?>/6)
                    </h3>
                    <a href="<?= base_url('dashboard/portofolio') ?>" class="text-xs font-bold text-brand-600 hover:text-brand-700">
                        Kelola Semua
                    </a>
                </div>

                <?php if (empty($portfolios)): ?>
                    <div class="text-center py-8 px-4 rounded-2xl bg-slate-50 border border-dashed border-slate-200">
                        <p class="text-xs text-slate-500 mb-3">Anda belum menambahkan karya / dokumentasi portofolio event.</p>
                        <a href="<?= base_url('dashboard/portofolio') ?>" class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Tambah Portofolio Sekarang
                        </a>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <?php foreach (array_slice($portfolios, 0, 3) as $port): ?>
                            <div class="rounded-xl overflow-hidden border border-slate-100 group">
                                <div class="aspect-video bg-slate-100 relative overflow-hidden">
                                    <img src="<?= base_url(esc($port['cover_image'])) ?>" alt="<?= esc($port['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                </div>
                                <div class="p-2.5 bg-slate-50">
                                    <p class="font-bold text-xs text-slate-900 truncate"><?= esc($port['title']) ?></p>
                                    <p class="text-2xs text-slate-400 mt-0.5"><?= esc($port['project_year']) ?><?= $port['event_location'] ? ' &bull; ' . esc($port['event_location']) : '' ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Col Right (5): Kartu Tanda Anggota (KTA) Digital & Privacy Status -->
        <div class="lg:col-span-5 space-y-6">
            <?php 
                $status = is_object($membership) ? ($membership->status ?? '') : ($membership['status'] ?? '');
                $memberNumber = is_object($membership) 
                    ? ($membership->member_number ?? $membership->membership_number ?? '') 
                    : ($membership['member_number'] ?? $membership['membership_number'] ?? '');
                $membershipId = is_object($membership) ? ($membership->id ?? null) : ($membership['id'] ?? null);
                $isActive = ($status === 'active');
            ?>

            <!-- Verifikasi Identitas & Usaha Overview Panel (Phase 5 Extension) -->
            <?php
                $vStatus = $verification['verification_status'] ?? 'unverified';
                $vLevel = $verification['verification_level'] ?? 'none';
                $vMethod = $verification['verification_method'] ?? ($profile->isBusiness() ? 'pic_only' : 'ktp_selfie');
                $isBiz = $profile->isBusiness();

                $vStatusBadge = match($vStatus) {
                    'approved' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'text' => 'Terverifikasi'],
                    'pending'  => ['bg' => 'bg-amber-50 text-amber-800 border-amber-200', 'text' => 'Menunggu Pemeriksaan'],
                    'rejected' => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'text' => 'Ditolak'],
                    'revoked'  => ['bg' => 'bg-slate-100 text-slate-700 border-slate-200', 'text' => 'Dicabut'],
                    default    => ['bg' => 'bg-slate-100 text-slate-600 border-slate-200', 'text' => 'Belum Diverifikasi'],
                };
            ?>
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-900 leading-none">Verifikasi Identitas & Usaha</h3>
                            <span class="text-[10px] text-slate-400 font-medium"><?= $isBiz ? 'Pemeriksaan PIC / Legalitas NIB' : 'Pemeriksaan KTP & Selfie' ?></span>
                        </div>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border <?= $vStatusBadge['bg'] ?>">
                        <?= $vStatusBadge['text'] ?>
                    </span>
                </div>

                <div class="text-xs space-y-2.5">
                    <?php if ($vStatus === 'approved'): ?>
                        <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-100 flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-emerald-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <div>
                                <?php if ($vLevel === 'business_verified'): ?>
                                    <p class="font-bold text-emerald-950">Data usaha dan PIC telah diperiksa oleh KOMEO.</p>
                                    <p class="text-emerald-800 text-2xs mt-0.5">Lencana centang biru resmi KOMEO aktif pada nama profil bisnis Anda.</p>
                                <?php elseif ($vLevel === 'pic_verified'): ?>
                                    <p class="font-bold text-emerald-950">Identitas PIC telah diverifikasi oleh KOMEO.</p>
                                    <p class="text-emerald-800 text-2xs mt-0.5">Lencana PIC Terverifikasi aktif pada profil Anda. Anda dapat meningkatkan ke verifikasi NIB sewaktu-waktu.</p>
                                <?php else: ?>
                                    <p class="font-bold text-emerald-950">Identitas individu Anda telah diverifikasi oleh KOMEO.</p>
                                    <p class="text-emerald-800 text-2xs mt-0.5">Lencana centang biru resmi KOMEO aktif pada nama profil publik Anda.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php elseif ($vStatus === 'pending'): ?>
                        <div class="p-3.5 rounded-2xl bg-amber-50/70 border border-amber-100 flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-amber-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <p class="font-bold text-amber-950">Dokumen dalam proses peninjauan.</p>
                                <p class="text-amber-800 text-2xs mt-0.5">Tim admin KOMEO sedang memvalidasi kesesuaian berkas Anda secara manual.</p>
                            </div>
                        </div>
                    <?php elseif ($vStatus === 'rejected' || $vStatus === 'revoked'): ?>
                        <div class="p-3.5 rounded-2xl bg-rose-50/70 border border-rose-100 flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-rose-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <div>
                                <p class="font-bold text-rose-950"><?= $vStatus === 'rejected' ? 'Pengajuan Verifikasi Ditolak' : 'Verifikasi Telah Dicabut' ?></p>
                                <?php if (! empty($verification['rejection_reason'])): ?>
                                    <p class="text-rose-800 text-2xs mt-0.5"><strong>Alasan:</strong> <?= esc($verification['rejection_reason']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-slate-600 leading-relaxed">
                            Verifikasi identitas atau bisnis Anda untuk mendapatkan lencana resmi KOMEO dan meningkatkan rasa percaya calon klien.
                        </p>
                    <?php endif; ?>

                    <div class="pt-1">
                        <a href="<?= base_url('dashboard/verifikasi-identitas') ?>" class="w-full inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-xl text-xs font-bold bg-slate-900 hover:bg-slate-800 text-white transition-colors shadow-2xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span><?= $vStatus === 'approved' ? 'Lihat Detail Verifikasi' : ($vStatus === 'pending' ? 'Cek Status Pengajuan' : 'Kelola / Ajukan Verifikasi') ?></span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- KTA Card Panel -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-900 leading-none">KTA Digital Resmi</h3>
                            <span class="text-[10px] text-slate-400 font-medium">Standar ID-1 / CR80</span>
                        </div>
                    </div>
                    <?php if ($isActive): ?>
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Aktif
                        </span>
                    <?php elseif ($status === 'pending'): ?>
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200">
                            Menunggu Persetujuan
                        </span>
                    <?php elseif ($status === 'suspended'): ?>
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
                            Ditangguhkan
                        </span>
                    <?php else: ?>
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-slate-100 text-slate-600 border border-slate-200">
                            Belum Terdaftar
                        </span>
                    <?php endif; ?>
                </div>

                <?php if ($isActive && !empty($membershipId)): ?>
                    <!-- Active Real KTA Card Preview Image -->
                    <div class="relative group rounded-2xl overflow-hidden shadow-lg border border-slate-800 bg-slate-900 aspect-[1011/638]">
                        <img src="<?= site_url('dashboard/kta/preview/front') ?>" 
                             alt="KTA Digital <?= esc($profile->full_name ?? auth()->user()->username) ?>" 
                             class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.02]">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4">
                            <a href="<?= site_url('dashboard/kta') ?>" class="text-xs font-bold text-white bg-brand-600/90 hover:bg-brand-600 px-3.5 py-2 rounded-xl backdrop-blur-xs flex items-center gap-1.5 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <span>Lihat Bolak-Balik & Unduh</span>
                            </a>
                        </div>
                    </div>

                    <!-- Member info snippet -->
                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between text-xs">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">No. Anggota</span>
                            <span class="font-mono font-extrabold text-slate-900"><?= esc($memberNumber) ?></span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Verifikasi QR</span>
                            <span class="font-semibold text-emerald-600 flex items-center gap-1 justify-end">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Terdaftar & Valid
                            </span>
                        </div>
                    </div>

                    <!-- Action buttons -->
                    <div class="grid grid-cols-2 gap-2.5 pt-1">
                        <a href="<?= site_url('dashboard/kta') ?>" class="py-2.5 px-3 rounded-xl font-bold text-xs text-center text-white bg-brand-600 hover:bg-brand-700 shadow-sm transition-colors flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span>Buka Portal KTA</span>
                        </a>
                        <a href="<?= site_url('dashboard/kta/download/pdf') ?>" class="py-2.5 px-3 rounded-xl font-bold text-xs text-center text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Unduh PDF</span>
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Pending or Inactive Placeholder Card -->
                    <div class="bg-gradient-to-br from-indigo-900 via-brand-900 to-slate-900 rounded-2xl p-6 text-white shadow-md relative overflow-hidden flex flex-col justify-between min-h-[200px]">
                        <div class="absolute -right-8 -top-8 w-40 h-40 bg-white/5 rounded-full blur-xl pointer-events-none"></div>
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <span class="w-7 h-7 rounded-lg bg-white/10 flex items-center justify-center font-extrabold text-xs">K</span>
                                <span class="font-bold text-xs">KOMEO.ID</span>
                            </div>
                            <p class="text-base font-bold text-white"><?= esc($profile->full_name ?? auth()->user()->username) ?></p>
                            <p class="text-xs text-brand-300 font-mono mt-0.5"><?= esc($memberNumber ?: 'MENUNGGU VERIFIKASI') ?></p>
                        </div>
                        <div class="pt-4 border-t border-white/10 text-xs text-slate-300">
                            <?php if ($status === 'pending'): ?>
                                Sedang diverifikasi oleh admin. KTA resmi akan diterbitkan otomatis setelah disetujui.
                            <?php elseif ($status === 'suspended'): ?>
                                Status keanggotaan saat ini ditangguhkan. Silakan hubungi admin.
                            <?php else: ?>
                                Selesaikan profil untuk penerbitan KTA digital resmi Anda.
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="pt-1">
                        <a href="<?= site_url('dashboard/kta') ?>" class="block w-full py-2.5 px-3 rounded-xl font-bold text-xs text-center text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                            Lihat Rincian Status KTA
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Privacy & Public Profile Settings Summary Card -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-extrabold text-slate-900">Visibilitas & Privasi</h3>
                    <a href="<?= base_url('dashboard/profil/edit#privacy') ?>" class="text-xs font-bold text-brand-600 hover:text-brand-700">
                        Atur
                    </a>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between py-1.5 border-b border-slate-50">
                        <span class="text-slate-600 font-medium">Tampil di Direktori Publik</span>
                        <?php if ($profile->is_public): ?>
                            <span class="font-bold text-emerald-600 flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Aktif
                            </span>
                        <?php else: ?>
                            <span class="font-bold text-slate-400 flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-slate-300"></span> Privat
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="flex items-center justify-between py-1.5 border-b border-slate-50">
                        <span class="text-slate-600 font-medium">Tampilkan Tombol WhatsApp</span>
                        <span class="font-bold <?= $profile->show_whatsapp ? 'text-emerald-600' : 'text-slate-400' ?>">
                            <?= $profile->show_whatsapp ? 'Ya' : 'Disembunyikan' ?>
                        </span>
                    </div>

                    <div class="flex items-center justify-between py-1.5 border-b border-slate-50">
                        <span class="text-slate-600 font-medium">Tampilkan Media Sosial</span>
                        <span class="font-bold <?= $profile->show_social ? 'text-emerald-600' : 'text-slate-400' ?>">
                            <?= $profile->show_social ? 'Ya' : 'Disembunyikan' ?>
                        </span>
                    </div>

                    <div class="flex items-center justify-between py-1.5">
                        <span class="text-slate-600 font-medium">Tampilkan Kota Domisili</span>
                        <span class="font-bold <?= $profile->show_location ? 'text-emerald-600' : 'text-slate-400' ?>">
                            <?= $profile->show_location ? 'Ya' : 'Disembunyikan' ?>
                        </span>
                    </div>
                </div>

                <div class="pt-2">
                    <a href="<?= base_url('member/' . esc($profile->username)) ?>" target="_blank" class="block w-full py-2.5 px-4 rounded-xl text-center text-xs font-bold text-brand-700 bg-brand-50 hover:bg-brand-100 transition-colors">
                        Buka Tampilan Profil Publik
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
