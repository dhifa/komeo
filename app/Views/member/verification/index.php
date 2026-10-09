<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <!-- Header & Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <span>Verifikasi Identitas & Bisnis</span>
                <?= komeo_verification_badge($verification, $profile->member_type ?? 'individual', $membership->status ?? '', 'md') ?>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Tingkatkan kepercayaan dan kredibilitas profesional Anda di komunitas dan direktori KOMEO.ID.
            </p>
        </div>
        <a href="<?= base_url('dashboard') ?>" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">
            &larr; Kembali ke Dashboard
        </a>
    </div>

    <!-- Alert Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span><?= session()->getFlashdata('success') ?></span>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm font-semibold flex items-center gap-3">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <span><?= session()->getFlashdata('error') ?></span>
        </div>
    <?php endif; ?>

    <?php
    $vStatus   = $verification['verification_status'] ?? 'unverified';
    $vLevel    = $verification['verification_level'] ?? 'none';
    $vMethod   = $verification['verification_method'] ?? ($profile->isBusiness() ? 'pic_only' : 'ktp_selfie');
    $isBiz     = $profile->isBusiness();
    $showForm  = in_array($vStatus, ['unverified', 'rejected', 'revoked'], true) || ($vLevel === 'pic_verified' && $this->request->getGet('upgrade') === 'nib');
    ?>

    <!-- Status Highlight Banner -->
    <?php if ($vStatus === 'approved'): ?>
        <div class="p-6 rounded-3xl bg-gradient-to-br from-emerald-50 via-teal-50 to-white border border-emerald-200 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 mb-1">
                            <svg class="w-3.5 h-3.5 fill-current text-emerald-600" viewBox="0 0 20 20"><circle cx="10" cy="10" r="8"/></svg>
                            <span>Terverifikasi Resmi KOMEO</span>
                        </div>
                        <h2 class="text-lg sm:text-xl font-black text-slate-900">
                            <?php if ($vLevel === 'business_verified'): ?>
                                Verifikasi Usaha & PIC (NIB Resmi) Disetujui
                            <?php elseif ($vLevel === 'pic_verified'): ?>
                                Verifikasi PIC (Bisnis Perorangan) Disetujui
                            <?php else: ?>
                                Verifikasi Identitas Anggota Disetujui
                            <?php endif; ?>
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                            <?php if ($vLevel === 'business_verified'): ?>
                                Dokumen NIB dan identitas PIC telah diperiksa manual oleh Admin KOMEO. Lencana centang biru resmi KOMEO terpasang di samping nama bisnis Anda.
                            <?php elseif ($vLevel === 'pic_verified'): ?>
                                Identitas penanggung jawab (PIC) telah diperiksa manual oleh Admin KOMEO. Lencana <strong>PIC Terverifikasi</strong> terpasang di profil Anda.
                            <?php else: ?>
                                Identitas Anda telah diverifikasi oleh KOMEO. Lencana centang biru resmi KOMEO terpasang di samping nama Anda.
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <?php if ($isBiz && $vLevel === 'pic_verified' && ! $showForm): ?>
                    <a href="<?= base_url('dashboard/verifikasi-identitas?upgrade=nib') ?>" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-brand-600 hover:bg-brand-700 text-white transition-colors shrink-0 shadow-xs flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                        <span>Upgrade ke Verifikasi NIB</span>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Verification Metadata Chips -->
            <div class="pt-3 border-t border-emerald-100 flex flex-wrap items-center gap-3 text-xs text-slate-600">
                <span>Disetujui pada: <strong><?= date('d M Y H:i', strtotime($verification['reviewed_at'] ?? $verification['updated_at'])) ?></strong></span>
                <span>&bull;</span>
                <span>Kategori Subjek: <strong><?= $isBiz ? 'Badan Usaha / Vendor' : 'Individu / Freelancer' ?></strong></span>
                <?php if (! empty($verification['business_name'])): ?>
                    <span>&bull;</span>
                    <span>Bisnis: <strong><?= esc($verification['business_name']) ?></strong></span>
                <?php endif; ?>
            </div>
        </div>
    <?php elseif ($vStatus === 'pending'): ?>
        <div class="p-6 rounded-3xl bg-amber-50 border border-amber-200 shadow-xs flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-500 text-slate-950 flex items-center justify-center shrink-0 font-bold shadow-xs">
                <svg class="w-6 h-6 animate-spin text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </div>
            <div>
                <span class="inline-block px-3 py-0.5 rounded-full text-xs font-extrabold bg-amber-200 text-amber-950 mb-1">
                    Menunggu Pemeriksaan Admin
                </span>
                <h2 class="text-lg font-black text-slate-900">Dokumen Verifikasi Sedang Ditinjau</h2>
                <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                    Pengajuan verifikasi Anda telah kami terima pada tanggal <strong><?= date('d M Y H:i', strtotime($verification['submitted_at'] ?? $verification['updated_at'])) ?></strong>.
                    Tim verifikator KOMEO sedang memeriksa keabsahan berkas Anda secara manual (estimasi 1x24 jam kerja).
                </p>
                <div class="mt-3 flex flex-wrap gap-2 text-xs">
                    <?php if (! empty($verification['ktp_document_path'])): ?>
                        <span class="px-2.5 py-1 rounded-lg bg-white border border-amber-200 text-slate-700 font-semibold flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            KTP Terunggah
                        </span>
                    <?php endif; ?>
                    <?php if (! empty($verification['selfie_document_path'])): ?>
                        <span class="px-2.5 py-1 rounded-lg bg-white border border-amber-200 text-slate-700 font-semibold flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Selfie Terunggah
                        </span>
                    <?php endif; ?>
                    <?php if (! empty($verification['nib_document_path'])): ?>
                        <span class="px-2.5 py-1 rounded-lg bg-white border border-amber-200 text-slate-700 font-semibold flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            NIB Dokumen Terunggah
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php elseif ($vStatus === 'rejected' || $vStatus === 'revoked'): ?>
        <div class="p-6 rounded-3xl bg-rose-50 border border-rose-200 shadow-xs space-y-4">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-rose-600 text-white flex items-center justify-center shrink-0 font-bold shadow-xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <div>
                    <span class="inline-block px-3 py-0.5 rounded-full text-xs font-extrabold bg-rose-200 text-rose-950 mb-1">
                        <?= $vStatus === 'rejected' ? 'Pengajuan Ditolak' : 'Verifikasi Dicabut' ?>
                    </span>
                    <h2 class="text-lg font-black text-slate-900">
                        <?= $vStatus === 'rejected' ? 'Dokumen Verifikasi Memerlukan Perbaikan' : 'Lencana Verifikasi Dicabut oleh Administrator' ?>
                    </h2>
                    <?php if (! empty($verification['rejection_reason'])): ?>
                        <div class="mt-2 p-3.5 rounded-2xl bg-white border border-rose-200 text-xs sm:text-sm text-slate-800">
                            <span class="font-bold text-rose-700 block mb-0.5">Alasan / Catatan Pemeriksaan:</span>
                            <?= nl2br(esc($verification['rejection_reason'])) ?>
                        </div>
                    <?php endif; ?>
                    <p class="text-xs text-slate-500 mt-2">
                        Silakan periksa kembali berkas dan ketentuan di bawah ini, kemudian ajukan perbaikan dokumen.
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Verification Guidelines & Privacy Card -->
    <div class="p-6 rounded-3xl bg-slate-900 text-white space-y-4 shadow-sm">
        <div class="flex items-center gap-3 pb-3 border-b border-slate-800">
            <div class="w-9 h-9 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-extrabold text-white">Panduan Keamanan & Privasi Dokumen KOMEO</h3>
                <p class="text-xs text-slate-400">Komitmen perlindungan data pribadi dan privasi anggota.</p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs text-slate-300">
            <div class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-1">
                <span class="font-bold text-blue-400 block">Penyimpanan Terisolasi</span>
                <p class="text-2xs text-slate-400 leading-relaxed">
                    Berkas KTP dan NIB disimpan di luar direktori web publik, tidak memiliki URL publik statis, dan hanya dapat diakses oleh administrator terotorisasi.
                </p>
            </div>
            <div class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-1">
                <span class="font-bold text-blue-400 block">Penyamaran Informasi KTP</span>
                <p class="text-2xs text-slate-400 leading-relaxed">
                    Anda diperbolehkan menyamarkan bagian nomor sensitif yang tidak diperlukan selama Nama Lengkap, Foto KTP, dan tulisan pokok tetap terbaca jelas.
                </p>
            </div>
            <div class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-1">
                <span class="font-bold text-blue-400 block">Bukan Izin Resmi Pemerintah</span>
                <p class="text-2xs text-slate-400 leading-relaxed">
                    Lencana verifikasi KOMEO adalah penandaan internal bahwa dokumen telah diperiksa komunitas, bukan sertifikasi atau jaminan kepatuhan hukum oleh otoritas pemerintah.
                </p>
            </div>
        </div>
    </div>

    <!-- Verification Submission Form -->
    <?php if ($showForm): ?>
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
            <div class="pb-4 border-b border-slate-100">
                <h2 class="text-lg font-black text-slate-900 tracking-tight">Formulir Pengajuan Verifikasi</h2>
                <p class="text-xs text-slate-500 mt-0.5">Unggah dokumen sesuai dengan kategori profil Anda di bawah ini.</p>
            </div>

            <form action="<?= base_url('dashboard/verifikasi-identitas/submit') ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
                <?= csrf_field() ?>

                <?php if ($isBiz): ?>
                    <!-- Business Pathway Method Selector -->
                    <div class="space-y-3">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Pilih Jalur Verifikasi Bisnis <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Option 1: Bisnis Perorangan / PIC Only -->
                            <label class="relative flex p-4 rounded-2xl border-2 cursor-pointer transition-all hover:border-brand-500 <?= $vMethod === 'pic_only' ? 'border-brand-600 bg-brand-50/30' : 'border-slate-200 bg-slate-50/50' ?>" id="label-pic-only">
                                <input type="radio" name="verification_method" value="pic_only" class="sr-only" <?= $vMethod === 'pic_only' ? 'checked' : '' ?> onchange="toggleBizFields('pic_only')">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="w-4 h-4 rounded-full border-2 border-brand-600 flex items-center justify-center">
                                            <span class="w-2 h-2 rounded-full bg-brand-600 <?= $vMethod === 'pic_only' ? '' : 'hidden' ?>" id="dot-pic-only"></span>
                                        </span>
                                        <span class="text-sm font-extrabold text-slate-900">Bisnis Perorangan — Verifikasi PIC</span>
                                    </div>
                                    <p class="text-xs text-slate-500 pl-6 leading-relaxed">
                                        Cocok untuk vendor perseorangan, freelancer, atau usaha mikro tanpa NIB. Memverifikasi identitas PIC dan menyematkan lencana <strong>PIC Terverifikasi</strong>.
                                    </p>
                                </div>
                            </label>

                            <!-- Option 2: Bisnis dengan NIB -->
                            <label class="relative flex p-4 rounded-2xl border-2 cursor-pointer transition-all hover:border-brand-500 <?= $vMethod === 'nib_pic' ? 'border-brand-600 bg-brand-50/30' : 'border-slate-200 bg-slate-50/50' ?>" id="label-nib-pic">
                                <input type="radio" name="verification_method" value="nib_pic" class="sr-only" <?= $vMethod === 'nib_pic' ? 'checked' : '' ?> onchange="toggleBizFields('nib_pic')">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="w-4 h-4 rounded-full border-2 border-brand-600 flex items-center justify-center">
                                            <span class="w-2 h-2 rounded-full bg-brand-600 <?= $vMethod === 'nib_pic' ? '' : 'hidden' ?>" id="dot-nib-pic"></span>
                                        </span>
                                        <span class="text-sm font-extrabold text-slate-900">Bisnis Terdaftar — Verifikasi Usaha & PIC (NIB)</span>
                                    </div>
                                    <p class="text-xs text-slate-500 pl-6 leading-relaxed">
                                        Untuk CV/PT atau badan usaha terdaftar NIB OSS. Mendapatkan lencana <strong>Centang Biru Resmi</strong> di samping nama badan usaha.
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Business & PIC Name Fields -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">Nama Bisnis / Perusahaan <span class="text-rose-500">*</span></label>
                            <input type="text" name="business_name" value="<?= esc(old('business_name', $verification['business_name'] ?? ($profile->business_name ?? ''))) ?>" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-medium focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">Nama Lengkap PIC (Penanggung Jawab) <span class="text-rose-500">*</span></label>
                            <input type="text" name="pic_name" value="<?= esc(old('pic_name', $verification['pic_name'] ?? ($profile->full_name ?? ''))) ?>" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-medium focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Document Upload Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                    
                    <!-- KTP Upload Field -->
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-slate-900 flex items-center gap-2">
                                <span>1. Foto KTP <?= $isBiz ? 'PIC / Penanggung Jawab' : 'Asli' ?></span>
                                <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-2xs font-semibold text-slate-400">Maks. 5MB (JPG/PNG)</span>
                        </div>
                        <input type="file" name="ktp_document" accept="image/jpeg,image/png,image/webp" class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 file:cursor-pointer cursor-pointer border border-slate-200 rounded-xl bg-white p-1">
                        <p class="text-2xs text-slate-500 leading-relaxed">
                            Pastikan foto KTP terlihat jelas, tidak buram, dan data nama sesuai dengan akun profil.
                        </p>
                        <?php if (! empty($verification['ktp_document_path'])): ?>
                            <div class="text-2xs text-emerald-700 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span>Berkas KTP telah terunggah sebelumnya. Unggah kembali jika ingin mengganti.</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Selfie Upload Field -->
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-slate-900 flex items-center gap-2">
                                <span>2. Foto Selfie <?= $isBiz ? 'PIC' : 'Diri' ?></span>
                                <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-2xs font-semibold text-slate-400">Maks. 5MB (JPG/PNG)</span>
                        </div>
                        <input type="file" name="selfie_document" accept="image/jpeg,image/png,image/webp" class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 file:cursor-pointer cursor-pointer border border-slate-200 rounded-xl bg-white p-1">
                        <p class="text-2xs text-slate-500 leading-relaxed">
                            Foto wajah Anda secara jelas di tempat dengan pencahayaan terang untuk pencocokan wajah dengan KTP.
                        </p>
                        <?php if (! empty($verification['selfie_document_path'])): ?>
                            <div class="text-2xs text-emerald-700 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span>Berkas Selfie telah terunggah sebelumnya.</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- NIB Document Field (Only for Business with NIB) -->
                    <?php if ($isBiz): ?>
                        <div class="md:col-span-2 p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3 <?= $vMethod === 'nib_pic' ? '' : 'hidden' ?>" id="container-nib-field">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-bold text-slate-900 flex items-center gap-2">
                                    <span>3. Berkas Dokumen NIB (Nomor Induk Berusaha)</span>
                                    <span class="text-rose-500">*</span>
                                </label>
                                <span class="text-2xs font-semibold text-slate-400">Maks. 5MB (PDF / JPG / PNG)</span>
                            </div>
                            <input type="file" name="nib_document" id="input-nib" accept="application/pdf,image/jpeg,image/png" class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 file:cursor-pointer cursor-pointer border border-slate-200 rounded-xl bg-white p-1">
                            <p class="text-2xs text-slate-500 leading-relaxed">
                                Unggah dokumen NIB yang diterbitkan melalui OSS Kementerian Investasi / BKPM. Pastikan nama usaha pada dokumen NIB sesuai atau memiliki hubungan legal dengan profil akun Anda.
                            </p>
                            <?php if (! empty($verification['nib_document_path'])): ?>
                                <div class="text-2xs text-emerald-700 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    <span>Berkas NIB telah terunggah sebelumnya.</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                </div>

                <!-- Declarations & Consent Checkboxes -->
                <div class="pt-4 border-t border-slate-100 space-y-3">
                    <?php if ($isBiz): ?>
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="pic_declaration" value="1" required class="w-4 h-4 mt-0.5 rounded text-brand-600 focus:ring-brand-500 border-slate-300">
                            <span class="text-xs text-slate-700 font-medium leading-relaxed">
                                <strong>Pernyataan Tanggung Jawab PIC:</strong> Saya menyatakan bahwa saya memiliki kewenangan yang sah untuk mewakili dan bertanggung jawab penuh atas segala aktivitas bisnis/vendor ini di platform KOMEO.ID.
                            </span>
                        </label>
                    <?php endif; ?>

                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="consent_agreement" value="1" required class="w-4 h-4 mt-0.5 rounded text-brand-600 focus:ring-brand-500 border-slate-300">
                        <span class="text-xs text-slate-700 font-medium leading-relaxed">
                            <strong>Persetujuan Pemeriksaan Dokumen:</strong> Saya menyatakan bahwa seluruh dokumen yang saya lampirkan adalah benar, otentik, dan saya memberikan persetujuan eksplisit kepada pengurus KOMEO.ID untuk memverifikasi keabsahan data saya secara internal.
                        </span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" class="w-full sm:w-auto px-7 py-3 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-extrabold text-xs sm:text-sm transition-colors shadow-md shadow-brand-600/20 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Kirim Permohonan Verifikasi</span>
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- History / Audit Log -->
    <?php if (! empty($history)): ?>
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
            <h3 class="text-sm font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Riwayat Pengajuan Verifikasi</span>
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                            <th class="py-2.5 px-3">Tanggal</th>
                            <th class="py-2.5 px-3">Aksi</th>
                            <th class="py-2.5 px-3">Status</th>
                            <th class="py-2.5 px-3">Level</th>
                            <th class="py-2.5 px-3">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <?php foreach ($history as $h): ?>
                            <tr>
                                <td class="py-2.5 px-3 font-mono text-2xs text-slate-500 whitespace-nowrap">
                                    <?= date('d M Y H:i', strtotime($h['created_at'])) ?>
                                </td>
                                <td class="py-2.5 px-3 font-semibold capitalize">
                                    <?= esc($h['action']) ?>
                                </td>
                                <td class="py-2.5 px-3">
                                    <span class="inline-block px-2 py-0.5 rounded-full text-2xs font-bold bg-slate-100 text-slate-700">
                                        <?= esc($h['new_status']) ?>
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 font-mono text-2xs">
                                    <?= esc($h['verification_level']) ?>
                                </td>
                                <td class="py-2.5 px-3 text-slate-500 max-w-xs truncate">
                                    <?= esc($h['notes'] ?: '-') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

</div>

<script>
function toggleBizFields(method) {
    const nibContainer = document.getElementById('container-nib-field');
    const labelPic = document.getElementById('label-pic-only');
    const labelNib = document.getElementById('label-nib-pic');
    const dotPic = document.getElementById('dot-pic-only');
    const dotNib = document.getElementById('dot-nib-pic');
    const nibInput = document.getElementById('input-nib');

    if (method === 'nib_pic') {
        if (nibContainer) nibContainer.classList.remove('hidden');
        if (nibInput) nibInput.setAttribute('required', 'required');
        if (labelNib) {
            labelNib.classList.add('border-brand-600', 'bg-brand-50/30');
            labelNib.classList.remove('border-slate-200', 'bg-slate-50/50');
        }
        if (labelPic) {
            labelPic.classList.remove('border-brand-600', 'bg-brand-50/30');
            labelPic.classList.add('border-slate-200', 'bg-slate-50/50');
        }
        if (dotNib) dotNib.classList.remove('hidden');
        if (dotPic) dotPic.classList.add('hidden');
    } else {
        if (nibContainer) nibContainer.classList.add('hidden');
        if (nibInput) nibInput.removeAttribute('required');
        if (labelPic) {
            labelPic.classList.add('border-brand-600', 'bg-brand-50/30');
            labelPic.classList.remove('border-slate-200', 'bg-slate-50/50');
        }
        if (labelNib) {
            labelNib.classList.remove('border-brand-600', 'bg-brand-50/30');
            labelNib.classList.add('border-slate-200', 'bg-slate-50/50');
        }
        if (dotPic) dotPic.classList.remove('hidden');
        if (dotNib) dotNib.classList.add('hidden');
    }
}
</script>
<?= $this->endSection() ?>
