<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <!-- Header & Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="<?= base_url('admin/identity-verifications') ?>" class="hover:text-white">Verifikasi</a>
                <span>/</span>
                <span class="text-white">Pemeriksaan #<?= $verification['id'] ?></span>
            </div>
            <h1 class="text-2xl font-black text-white tracking-tight flex items-center gap-2.5">
                <span>Pemeriksaan Berkas: <?= esc($profile->display_name ?: $profile->full_name) ?></span>
                <?= komeo_verification_badge($verification, $profile->member_type ?? 'individual', $membership->status ?? '', 'md') ?>
            </h1>
        </div>
        <a href="<?= base_url('admin/identity-verifications') ?>" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-slate-400 bg-slate-800 hover:text-white hover:bg-slate-700 transition-colors">
            &larr; Kembali ke Daftar
        </a>
    </div>

    <!-- Alert Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs sm:text-sm font-semibold flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span><?= session()->getFlashdata('success') ?></span>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs sm:text-sm font-semibold flex items-center gap-3">
            <svg class="w-5 h-5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <span><?= session()->getFlashdata('error') ?></span>
        </div>
    <?php endif; ?>

    <?php
    $vStatus = $verification['verification_status'];
    $vLevel  = $verification['verification_level'];
    $vMethod = $verification['verification_method'];
    $isBiz   = ($verification['verification_subject_type'] === 'business');
    ?>

    <!-- Overview Status Card -->
    <div class="p-6 rounded-3xl bg-slate-900 border border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-600/20 text-blue-400 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-black uppercase <?= match($vStatus) {
                        'approved' => 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30',
                        'pending'  => 'bg-amber-500/20 text-amber-300 border border-amber-500/30 animate-pulse',
                        'rejected' => 'bg-rose-500/20 text-rose-400 border border-rose-500/30',
                        'revoked'  => 'bg-slate-800 text-slate-400 border border-slate-700',
                        default    => 'bg-slate-800 text-slate-400',
                    } ?>">
                        Status: <?= strtoupper($vStatus) ?>
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-800 text-slate-300 border border-slate-700">
                        Level: <?= strtoupper($vLevel) ?>
                    </span>
                </div>
                <p class="text-xs text-slate-400">
                    Jalur Verifikasi: <strong><?= match($vMethod) {
                        'nib_pic'    => 'Bisnis Terdaftar (NIB OSS & KTP PIC)',
                        'pic_only'   => 'Bisnis Perorangan (KTP & Selfie PIC Only)',
                        'ktp_selfie' => 'Individu / Freelancer (KTP & Selfie)',
                        default      => $vMethod,
                    } ?></strong>
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2 text-xs">
            <span class="px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-400">
                Diajukan: <?= ! empty($verification['submitted_at']) ? date('d M Y H:i', strtotime($verification['submitted_at'])) : '-' ?>
            </span>
            <?php if (! empty($verification['reviewed_at'])): ?>
                <span class="px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-400">
                    Ditinjau: <?= date('d M Y H:i', strtotime($verification['reviewed_at'])) ?>
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- 2 Column Grid: Info & Document Inspection -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Column (7): Profile & Document Inspection -->
        <div class="lg:col-span-7 space-y-6">

            <!-- Member Account Summary -->
            <div class="bg-slate-900 rounded-3xl p-6 border border-slate-800 space-y-4">
                <h2 class="text-sm font-extrabold text-white uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>Informasi Akun & Entitas</span>
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800">
                        <span class="text-slate-500 font-bold block mb-0.5">Nama Lengkap Akun</span>
                        <span class="text-white font-semibold"><?= esc($profile->full_name) ?></span>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800">
                        <span class="text-slate-500 font-bold block mb-0.5">Nama Tampilan</span>
                        <span class="text-white font-semibold"><?= esc($profile->display_name ?: '-') ?></span>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800">
                        <span class="text-slate-500 font-bold block mb-0.5">Email & Username</span>
                        <span class="text-white font-semibold"><?= esc($user['email'] ?? '-') ?> (@<?= esc($user['username'] ?? '-') ?>)</span>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800">
                        <span class="text-slate-500 font-bold block mb-0.5">Status Keanggotaan</span>
                        <span class="font-bold <?= ($membership->status ?? '') === 'active' ? 'text-emerald-400' : 'text-amber-400' ?>">
                            <?= strtoupper($membership->status ?? 'pending') ?> (No. <?= esc($membership->member_number ?? '-') ?>)
                        </span>
                    </div>

                    <?php if ($isBiz || ! empty($verification['business_name'])): ?>
                        <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800">
                            <span class="text-slate-500 font-bold block mb-0.5">Nama Bisnis (Permohonan)</span>
                            <span class="text-brand-300 font-bold text-sm"><?= esc($verification['business_name'] ?: ($profile->business_name ?: '-')) ?></span>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800">
                            <span class="text-slate-500 font-bold block mb-0.5">Nama PIC (Penanggung Jawab)</span>
                            <span class="text-brand-300 font-bold text-sm"><?= esc($verification['pic_name'] ?: ($profile->full_name ?: '-')) ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="pt-2 text-2xs text-slate-500 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Persetujuan eksplisit telah disetujui pemohon pada <?= ! empty($verification['consent_at']) ? date('d M Y H:i:s', strtotime($verification['consent_at'])) : '-' ?></span>
                </div>
            </div>

            <!-- Uploaded Documents Inspection Panel -->
            <div class="bg-slate-900 rounded-3xl p-6 border border-slate-800 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <h2 class="text-sm font-extrabold text-white uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Pemeriksaan Berkas Fisik</span>
                    </h2>
                    <span class="text-2xs text-slate-500 font-mono">Akses Terproteksi & Dicatat di Audit Log</span>
                </div>

                <div class="space-y-4">
                    
                    <!-- 1. KTP Document -->
                    <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center text-slate-300 font-bold text-xs">
                                KTP
                            </div>
                            <div>
                                <span class="text-sm font-bold text-white block">Foto KTP Penanggung Jawab</span>
                                <span class="text-2xs text-slate-400 font-mono">
                                    <?= ! empty($verification['ktp_document_path']) ? basename($verification['ktp_document_path']) : 'Belum diunggah' ?>
                                </span>
                            </div>
                        </div>

                        <?php if (! empty($verification['ktp_document_path'])): ?>
                            <a href="<?= base_url('admin/identity-verifications/document/' . $verification['id'] . '/ktp') ?>" target="_blank" class="px-4 py-2 rounded-xl text-xs font-bold bg-brand-600 hover:bg-brand-500 text-white transition-colors shrink-0 flex items-center gap-1.5 shadow-2xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <span>Buka / Periksa KTP</span>
                            </a>
                        <?php else: ?>
                            <span class="text-xs text-slate-600 italic">Tidak tersedia</span>
                        <?php endif; ?>
                    </div>

                    <!-- 2. Selfie Document -->
                    <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center text-slate-300 font-bold text-xs">
                                FOTO
                            </div>
                            <div>
                                <span class="text-sm font-bold text-white block">Foto Selfie Penanggung Jawab</span>
                                <span class="text-2xs text-slate-400 font-mono">
                                    <?= ! empty($verification['selfie_document_path']) ? basename($verification['selfie_document_path']) : 'Belum diunggah' ?>
                                </span>
                            </div>
                        </div>

                        <?php if (! empty($verification['selfie_document_path'])): ?>
                            <a href="<?= base_url('admin/identity-verifications/document/' . $verification['id'] . '/selfie') ?>" target="_blank" class="px-4 py-2 rounded-xl text-xs font-bold bg-brand-600 hover:bg-brand-500 text-white transition-colors shrink-0 flex items-center gap-1.5 shadow-2xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <span>Buka / Periksa Selfie</span>
                            </a>
                        <?php else: ?>
                            <span class="text-xs text-slate-600 italic">Tidak tersedia</span>
                        <?php endif; ?>
                    </div>

                    <!-- 3. NIB Document (If Applicable) -->
                    <?php if ($vMethod === 'nib_pic' || ! empty($verification['nib_document_path'])): ?>
                        <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-900/40 text-blue-300 flex items-center justify-center font-bold text-xs border border-blue-800">
                                    NIB
                                </div>
                                <div>
                                    <span class="text-sm font-bold text-white block">Dokumen NIB (Nomor Induk Berusaha)</span>
                                    <span class="text-2xs text-slate-400 font-mono">
                                        <?= ! empty($verification['nib_document_path']) ? basename($verification['nib_document_path']) : 'Belum diunggah' ?>
                                    </span>
                                </div>
                            </div>

                            <?php if (! empty($verification['nib_document_path'])): ?>
                                <a href="<?= base_url('admin/identity-verifications/document/' . $verification['id'] . '/nib') ?>" target="_blank" class="px-4 py-2 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-500 text-white transition-colors shrink-0 flex items-center gap-1.5 shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    <span>Buka Dokumen NIB</span>
                                </a>
                            <?php else: ?>
                                <span class="text-xs text-slate-600 italic">Tidak tersedia</span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

            <!-- Verification History / Audit Log -->
            <div class="bg-slate-900 rounded-3xl p-6 border border-slate-800 space-y-4">
                <h2 class="text-sm font-extrabold text-white uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Riwayat Audit Verifikasi</span>
                </h2>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-800 text-slate-500 font-bold uppercase tracking-wider">
                                <th class="py-2.5 px-3">Waktu</th>
                                <th class="py-2.5 px-3">Aksi</th>
                                <th class="py-2.5 px-3">Status</th>
                                <th class="py-2.5 px-3">Level</th>
                                <th class="py-2.5 px-3">Pelaku</th>
                                <th class="py-2.5 px-3">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800 text-slate-300">
                            <?php if (empty($history)): ?>
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-slate-500">Belum ada riwayat tercatat.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($history as $h): ?>
                                    <tr>
                                        <td class="py-2.5 px-3 font-mono text-2xs text-slate-400 whitespace-nowrap">
                                            <?= date('d M Y H:i', strtotime($h['created_at'])) ?>
                                        </td>
                                        <td class="py-2.5 px-3 font-semibold capitalize text-white">
                                            <?= esc($h['action']) ?>
                                        </td>
                                        <td class="py-2.5 px-3">
                                            <span class="px-2 py-0.5 rounded-full text-2xs font-bold bg-slate-800 text-slate-300">
                                                <?= esc($h['new_status']) ?>
                                            </span>
                                        </td>
                                        <td class="py-2.5 px-3 font-mono text-2xs text-brand-300">
                                            <?= esc($h['verification_level']) ?>
                                        </td>
                                        <td class="py-2.5 px-3 text-slate-400">
                                            <?= esc($h['actor_username'] ?? 'User #' . $h['actor_id']) ?> (<?= esc($h['actor_role'] ?? '-') ?>)
                                        </td>
                                        <td class="py-2.5 px-3 text-slate-400 max-w-xs truncate">
                                            <?= esc($h['notes'] ?: '-') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Right Column (5): Action Decisions Panel -->
        <div class="lg:col-span-5 space-y-6">

            <!-- 1. Approval Action Card -->
            <div class="bg-slate-900 rounded-3xl p-6 border border-emerald-500/30 space-y-4">
                <div class="flex items-center gap-3 pb-3 border-b border-slate-800">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-white">Setujui Verifikasi</h3>
                        <p class="text-2xs text-slate-400">Pemberian lencana resmi sesuai validasi dokumen.</p>
                    </div>
                </div>

                <form action="<?= base_url('admin/identity-verifications/approve/' . $verification['id']) ?>" method="POST" class="space-y-4">
                    <?= csrf_field() ?>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-300">Pilih Level Verifikasi Yang Disetujui <span class="text-rose-500">*</span></label>
                        
                        <?php if ($vMethod === 'ktp_selfie'): ?>
                            <label class="flex items-start gap-2.5 p-3 rounded-xl bg-slate-950 border border-slate-800 cursor-pointer">
                                <input type="radio" name="target_level" value="identity_verified" checked class="mt-0.5 text-brand-600">
                                <div class="text-xs">
                                    <strong class="text-white block">Identitas Terverifikasi (Centang Biru)</strong>
                                    <span class="text-slate-400 text-2xs">Lencana centang biru resmi di samping nama anggota individu.</span>
                                </div>
                            </label>
                        <?php elseif ($vMethod === 'pic_only'): ?>
                            <label class="flex items-start gap-2.5 p-3 rounded-xl bg-slate-950 border border-slate-800 cursor-pointer">
                                <input type="radio" name="target_level" value="pic_verified" checked class="mt-0.5 text-teal-600">
                                <div class="text-xs">
                                    <strong class="text-teal-400 block">PIC Terverifikasi (Bisnis Perorangan)</strong>
                                    <span class="text-slate-400 text-2xs">Lencana "PIC Terverifikasi" di profil usaha, tanpa centang biru legalitas NIB.</span>
                                </div>
                            </label>
                        <?php elseif ($vMethod === 'nib_pic'): ?>
                            <div class="space-y-2">
                                <label class="flex items-start gap-2.5 p-3 rounded-xl bg-slate-950 border border-slate-800 cursor-pointer">
                                    <input type="radio" name="target_level" value="business_verified" checked class="mt-0.5 text-blue-600">
                                    <div class="text-xs">
                                        <strong class="text-blue-400 block">Bisnis Terdaftar NIB (Centang Biru)</strong>
                                        <span class="text-slate-400 text-2xs">Lencana centang biru resmi di samping nama bisnis terdaftar NIB.</span>
                                    </div>
                                </label>
                                <label class="flex items-start gap-2.5 p-3 rounded-xl bg-slate-950 border border-slate-800 cursor-pointer">
                                    <input type="radio" name="target_level" value="pic_verified" class="mt-0.5 text-teal-600">
                                    <div class="text-xs">
                                        <strong class="text-teal-400 block">Hanya PIC Terverifikasi</strong>
                                        <span class="text-slate-400 text-2xs">Jika dokumen NIB belum meyakinkan tapi identitas PIC valid.</span>
                                    </div>
                                </label>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-300">Catatan Internal Admin (Opsional)</label>
                        <input type="text" name="admin_notes" placeholder="Catatan hasil verifikasi NIB/KTP..." class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-500">
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs transition-colors shadow-xs flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Setujui Permohonan Verifikasi</span>
                    </button>
                </form>
            </div>

            <!-- 2. Rejection Action Card -->
            <div class="bg-slate-900 rounded-3xl p-6 border border-rose-500/30 space-y-4">
                <div class="flex items-center gap-3 pb-3 border-b border-slate-800">
                    <div class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-white">Tolak Pengajuan / Minta Perbaikan</h3>
                        <p class="text-2xs text-slate-400">Pemohon akan melihat alasan penolakan dan dapat mengajukan ulang.</p>
                    </div>
                </div>

                <form action="<?= base_url('admin/identity-verifications/reject/' . $verification['id']) ?>" method="POST" class="space-y-4">
                    <?= csrf_field() ?>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-300">Alasan Penolakan <span class="text-rose-500">*</span></label>
                        <textarea name="reason" rows="3" required placeholder="Contoh: Foto KTP tidak terbaca jelas atau buram, nama bisnis pada NIB tidak sesuai dengan profil..." class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500"></textarea>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-300">Catatan Internal (Opsional)</label>
                        <input type="text" name="admin_notes" placeholder="Catatan internal pengurus..." class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-500">
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs transition-colors shadow-xs flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Tolak Pengajuan Ini</span>
                    </button>
                </form>
            </div>

            <!-- 3. Revoke Verification (If currently approved) -->
            <?php if ($vStatus === 'approved'): ?>
                <div class="bg-slate-900 rounded-3xl p-6 border border-slate-800 space-y-4">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-800">
                        <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-white">Cabut Status Verifikasi</h3>
                            <p class="text-2xs text-slate-400">Menghapus lencana verifikasi publik dari profil anggota.</p>
                        </div>
                    </div>

                    <form action="<?= base_url('admin/identity-verifications/revoke/' . $verification['id']) ?>" method="POST" class="space-y-3">
                        <?= csrf_field() ?>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-300">Alasan Pencabutan <span class="text-rose-500">*</span></label>
                            <input type="text" name="reason" required placeholder="Alasan pencabutan status..." class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-500">
                        </div>
                        <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-rose-900 text-rose-300 hover:text-white font-extrabold text-xs transition-colors border border-rose-500/30">
                            Cabut Verifikasi Anggota
                        </button>
                    </form>
                </div>
            <?php endif; ?>

            <!-- 4. Data Minimization / Retention Cleanup -->
            <?php if (empty($verification['documents_deleted_at']) && (! empty($verification['ktp_document_path']) || ! empty($verification['selfie_document_path']) || ! empty($verification['nib_document_path']))): ?>
                <div class="p-5 rounded-3xl bg-slate-950 border border-slate-800 space-y-3">
                    <span class="text-xs font-bold text-slate-400 block">Kebijakan Retensi Data Pribadi</span>
                    <p class="text-2xs text-slate-500 leading-relaxed">
                        Jika proses pemeriksaan telah selesai dan tidak lagi memerlukan berkas fisik KTP/Selfie/NIB, Anda dapat membersihkan dokumen fisik untuk meminimalkan penyimpanan data pribadi.
                    </p>
                    <form action="<?= base_url('admin/identity-verifications/delete-documents/' . $verification['id']) ?>" method="POST">
                        <?= csrf_field() ?>
                        <button type="submit" class="py-2 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-white text-2xs font-bold border border-slate-700 transition-colors">
                            Bersihkan Berkas Dokumen Fisik
                        </button>
                    </form>
                </div>
            <?php endif; ?>

        </div>
    </div>

</div>
<?= $this->endSection() ?>
