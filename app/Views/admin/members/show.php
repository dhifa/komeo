<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="space-y-6 max-w-6xl mx-auto">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="<?= base_url('admin/members') ?>" class="text-xs font-semibold text-brand-600 hover:text-brand-700 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Kembali ke Daftar Member
                </a>
                <span class="text-slate-300">•</span>
                <span class="text-xs font-semibold text-slate-500">ID Keanggotaan #<?= esc($member['id']) ?></span>
            </div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                <?= esc($member['full_name'] ?: $member['account_username']) ?>
            </h2>
        </div>

        <!-- Quick Status & Action Buttons -->
        <div class="flex flex-wrap items-center gap-2.5">
            <?php 
            $st = $member['status'] ?? 'pending';
            $badgeColor = match($st) {
                'active'    => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                'pending'   => 'bg-amber-100 text-amber-800 border border-amber-200',
                'rejected'  => 'bg-rose-100 text-rose-800 border border-rose-200',
                'suspended' => 'bg-orange-100 text-orange-800 border border-orange-200',
                default     => 'bg-slate-100 text-slate-700',
            };
            $label = match($st) {
                'active'    => 'Anggota Aktif',
                'pending'   => 'Menunggu Persetujuan',
                'rejected'  => 'Ditolak',
                'suspended' => 'Ditangguhkan',
                default     => ucfirst($st),
            };
            ?>
            <span class="px-3 py-1.5 rounded-xl text-xs font-bold <?= $badgeColor ?>">
                <?= $label ?>
            </span>

            <?php if ($st === 'pending'): ?>
                <button type="button" 
                        onclick="confirmApprove(<?= (int)$member['id'] ?>, '<?= esc($member['full_name'] ?: $member['account_username'], 'js') ?>')" 
                        class="px-4 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition shadow-xs flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Setujui Keanggotaan
                </button>
                <button type="button" 
                        onclick="openRejectModal(<?= (int)$member['id'] ?>, '<?= esc($member['full_name'] ?: $member['account_username'], 'js') ?>')" 
                        class="px-4 py-2 text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-xl transition border border-rose-200 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Tolak
                </button>
            <?php elseif ($st === 'active'): ?>
                <button type="button" 
                        onclick="openSuspendModal(<?= (int)$member['id'] ?>, '<?= esc($member['full_name'] ?: $member['account_username'], 'js') ?>')" 
                        class="px-4 py-2 text-xs font-bold text-orange-700 bg-orange-50 hover:bg-orange-100 rounded-xl transition border border-orange-200 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    Tangguhkan
                </button>
            <?php elseif (in_array($st, ['suspended', 'rejected'], true)): ?>
                <button type="button" 
                        onclick="confirmReactivate(<?= (int)$member['id'] ?>, '<?= esc($member['full_name'] ?: $member['account_username'], 'js') ?>')" 
                        class="px-4 py-2 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-xl transition border border-emerald-200 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Aktifkan Kembali
                </button>
            <?php endif; ?>

            <!-- Action: Reset Password by Admin -->
            <button type="button" 
                    onclick="openResetPasswordModal(<?= (int)$member['id'] ?>, '<?= esc($member['full_name'] ?: $member['account_username'], 'js') ?>', '<?= esc($member['email'] ?? '', 'js') ?>', '<?= esc($member['whatsapp'] ?? '', 'js') ?>')" 
                    class="px-3.5 py-2 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition flex items-center gap-1.5 border border-slate-200 shadow-xs"
                    title="Reset Password Member dan kirim ke Email/WhatsApp">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                Reset Password
            </button>

            <!-- Action: Terbitkan Surat Peringatan (SP) -->
            <button type="button" 
                    onclick="openWarningModal(<?= (int)$member['id'] ?>, '<?= esc($member['full_name'] ?: $member['account_username'], 'js') ?>')" 
                    class="px-3.5 py-2 text-xs font-bold text-amber-900 bg-amber-50 hover:bg-amber-100 rounded-xl transition flex items-center gap-1.5 border border-amber-300 shadow-xs"
                    title="Terbitkan Surat Peringatan (SP1, SP2, SP3)">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Terbitkan SP
            </button>
        </div>
    </div>

    <!-- Alert Notifications -->
    <?php if (session()->getFlashdata('message')): ?>
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-3">
            <div class="p-1.5 bg-emerald-100 text-emerald-700 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <p class="text-xs font-bold text-emerald-900"><?= esc(session()->getFlashdata('message')) ?></p>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="p-4 bg-red-50 border border-red-200 rounded-2xl flex items-center gap-3">
            <div class="p-1.5 bg-red-100 text-red-700 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <p class="text-xs font-bold text-red-900"><?= esc(session()->getFlashdata('error')) ?></p>
        </div>
    <?php endif; ?>

    <?php if ($resetRes = session()->getFlashdata('reset_result')): ?>
        <div class="p-5 bg-gradient-to-r from-emerald-50 via-teal-50 to-emerald-50 border-2 border-emerald-400 rounded-3xl shadow-sm space-y-3">
            <div class="flex items-center justify-between border-b border-emerald-200 pb-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <div>
                        <h4 class="text-sm font-extrabold text-emerald-950">Password Berhasil Direset untuk <?= esc($resetRes['member_name']) ?>!</h4>
                        <p class="text-[11px] text-emerald-700">Pastikan untuk menyampaikan kredensial baru ini kepada member terkait.</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-200 text-emerald-900">
                    Kredensial Aktif
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                <div class="p-3 bg-white rounded-2xl border border-emerald-200 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Password Baru:</span>
                        <code class="text-sm font-mono font-bold text-slate-900 tracking-wider" id="newPwdDisplay"><?= esc($resetRes['password']) ?></code>
                    </div>
                    <button type="button" onclick="navigator.clipboard.writeText('<?= esc($resetRes['password'], 'js') ?>'); window.KomeoModal.toast('Password baru berhasil disalin ke clipboard!', 'success');" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Salin
                    </button>
                </div>

                <div class="p-3 bg-white rounded-2xl border border-emerald-200 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Status Notifikasi:</span>
                        <div class="text-[11px] text-slate-700 font-medium">
                            <?php if (! empty($resetRes['email_status'])): ?>
                                <div>&bull; Email: <?= esc($resetRes['email_status']) ?></div>
                            <?php endif; ?>
                            <div>&bull; WhatsApp: <?= ! empty($resetRes['phone']) ? esc($resetRes['phone']) : 'Nomor tidak terdaftar' ?></div>
                        </div>
                    </div>
                    <?php if (! empty($resetRes['wa_url'])): ?>
                        <a href="<?= esc($resetRes['wa_url']) ?>" target="_blank" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-xs flex items-center gap-1.5 shrink-0">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                            Kirim via WhatsApp
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Profile Completeness Review Card -->
    <?php if ($completion): ?>
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                        <span>Peninjauan Kelengkapan Profil</span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold <?= $completion['percentage'] === 100 ? 'bg-emerald-100 text-emerald-800' : 'bg-brand-50 text-brand-700' ?>">
                            <?= $completion['percentage'] ?>% Lengkap
                        </span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Telah memenuhi <?= $completion['completed_count'] ?> dari <?= $completion['total_count'] ?> parameter kelengkapan data.
                    </p>
                </div>
            </div>

            <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all duration-300 <?= $completion['percentage'] === 100 ? 'bg-emerald-500' : 'bg-brand-600' ?>" style="width: <?= $completion['percentage'] ?>%"></div>
            </div>

            <?php if (! empty($completion['missing_fields'])): ?>
                <div class="pt-2 border-t border-slate-100">
                    <p class="text-xs font-bold text-slate-500 mb-2">Item data yang belum dilengkapi:</p>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($completion['missing_fields'] as $missing): ?>
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 border border-amber-200/60">
                                <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <?= esc($missing) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- 2 Column Layout: Details & Sidebar -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <!-- Left 2 Cols: Main Info, Entitas Bisnis, Spesialisasi, Portofolio -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Identitas Pribadi Card -->
            <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/80 shadow-xs space-y-5">
                <div class="flex items-center gap-4">
                    <div class="relative shrink-0">
                        <img src="<?= esc($profile ? $profile->getAvatarUrl() : base_url('uploads/avatars/default.png')) ?>" 
                             alt="<?= esc($member['display_name']) ?>" 
                             class="w-20 h-20 rounded-2xl object-cover ring-2 ring-slate-100 shadow-sm bg-slate-100">
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900"><?= esc($member['full_name'] ?: '-') ?></h3>
                        <p class="text-xs text-slate-500 font-mono">@<?= esc($member['profile_username'] ?: $member['account_username']) ?></p>
                        <div class="flex flex-wrap items-center gap-2 mt-2">
                            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-slate-100 text-slate-700">
                                <?= ($member['member_type'] ?? 'individual') === 'business' ? 'Perusahaan / Bisnis' : 'Individu / Freelancer' ?>
                            </span>
                            <?php if (! empty($member['category_name'])): ?>
                                <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-brand-50 text-brand-700 border border-brand-200">
                                    <?= esc($member['category_name']) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-3 border-t border-slate-100 text-xs">
                    <div>
                        <span class="block text-slate-400 font-bold uppercase text-[10px]">Nama Tampilan</span>
                        <span class="font-bold text-slate-800"><?= esc($member['display_name'] ?: '-') ?></span>
                    </div>
                    <div>
                        <span class="block text-slate-400 font-bold uppercase text-[10px]">Email Terdaftar</span>
                        <span class="font-bold text-slate-800 font-mono"><?= esc($member['email'] ?: '-') ?></span>
                    </div>
                    <div>
                        <span class="block text-slate-400 font-bold uppercase text-[10px]">Domisili</span>
                        <span class="font-bold text-slate-800"><?= esc(trim(($member['city'] ? $member['city'] . ', ' : '') . ($member['province'] ?? ''), ', ') ?: '-') ?></span>
                    </div>
                    <div>
                        <span class="block text-slate-400 font-bold uppercase text-[10px]">Pengalaman di Industri Event</span>
                        <span class="font-bold text-slate-800"><?= $member['years_of_experience'] ? ((int)$member['years_of_experience'] . ' Tahun') : '-' ?></span>
                    </div>
                </div>

                <?php if (! empty($member['bio'])): ?>
                    <div class="pt-3 border-t border-slate-100 text-xs">
                        <span class="block text-slate-400 font-bold uppercase text-[10px] mb-1">Biografi</span>
                        <p class="text-slate-700 leading-relaxed whitespace-pre-line bg-slate-50 p-4 rounded-xl border border-slate-100">
                            <?= nl2br(esc($member['bio'])) ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Entitas Bisnis (If Business) -->
            <?php if (($member['member_type'] ?? '') === 'business'): ?>
                <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                    <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Informasi Badan Usaha / Perusahaan
                    </h3>

                    <div class="flex items-start gap-4">
                        <?php if (! empty($member['company_logo_path']) && file_exists(FCPATH . $member['company_logo_path'])): ?>
                            <img src="<?= base_url(esc($member['company_logo_path'])) ?>" alt="Logo Perusahaan" class="w-16 h-16 rounded-xl object-contain bg-white p-1 border border-slate-200 shadow-xs">
                        <?php endif; ?>
                        <div class="space-y-1">
                            <h4 class="text-xs font-bold text-slate-900"><?= esc($member['business_name'] ?: 'Nama bisnis belum diisi') ?></h4>
                            <p class="text-[11px] text-slate-500">Penanggung Jawab: <span class="font-bold text-slate-700"><?= esc($member['full_name']) ?></span></p>
                        </div>
                    </div>

                    <?php if (! empty($member['business_description'])): ?>
                        <div class="pt-3 border-t border-slate-100 text-xs">
                            <span class="block text-slate-400 font-bold uppercase text-[10px] mb-1">Deskripsi Layanan & Usaha</span>
                            <p class="text-slate-700 leading-relaxed whitespace-pre-line bg-slate-50 p-4 rounded-xl border border-slate-100">
                                <?= nl2br(esc($member['business_description'])) ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Spesialisasi Keahlian -->
            <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    Keahlian Spesialisasi (<?= count($specializations) ?> Dipilih)
                </h3>

                <?php if (empty($specializations)): ?>
                    <p class="text-xs text-slate-400 italic">Member belum memilih spesialisasi.</p>
                <?php else: ?>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($specializations as $sp): ?>
                            <?php $spObj = (object) $sp; ?>
                            <span class="px-3 py-1.5 rounded-xl text-xs font-bold bg-indigo-50 text-indigo-800 border border-indigo-100 flex items-center gap-1.5">
                                <svg class="w-3 h-3 text-indigo-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                <?= esc($spObj->name) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Galeri Portofolio Proyek -->
            <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Portofolio Event (<?= count($portfolios) ?> Proyek)
                    </h3>
                </div>

                <?php if (empty($portfolios)): ?>
                    <p class="text-xs text-slate-400 italic">Member belum mengunggah karya portofolio.</p>
                <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php foreach ($portfolios as $port): ?>
                            <?php $portObj = (object) $port; ?>
                            <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200/70 space-y-2">
                                <div class="h-32 rounded-xl bg-slate-200 overflow-hidden relative">
                                    <?php if (! empty($portObj->cover_image) && file_exists(FCPATH . $portObj->cover_image)): ?>
                                        <img src="<?= base_url(esc($portObj->cover_image)) ?>" alt="" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center text-slate-400">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    <?php endif; ?>
                                    <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-900/80 text-white">
                                        <?= esc($portObj->project_year) ?>
                                    </span>
                                </div>
                                <h4 class="text-xs font-bold text-slate-900 truncate"><?= esc($portObj->title) ?></h4>
                                <?php if (! empty($portObj->event_location)): ?>
                                    <p class="text-[11px] text-slate-500 truncate"><?= esc($portObj->event_location) ?></p>
                                <?php endif; ?>
                                <p class="text-[11px] text-slate-600 line-clamp-2"><?= esc($portObj->description) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right 1 Col: Keanggotaan, Privasi, Riwayat Status -->
        <div class="space-y-6">

            <!-- Permohonan Aktivasi Keanggotaan & Kontak (Phase 5 Extension) -->
            <?php if (! empty($activationRequest)): ?>
                <?php 
                $actStatus = $activationRequest['status'] ?? 'awaiting_contact';
                $actBadge = match($actStatus) {
                    'awaiting_contact' => 'bg-amber-100 text-amber-900 border-amber-300',
                    'awaiting_review'  => 'bg-blue-100 text-blue-900 border-blue-300',
                    'approved'         => 'bg-emerald-100 text-emerald-900 border-emerald-300',
                    'rejected'         => 'bg-rose-100 text-rose-900 border-rose-300',
                    default            => 'bg-slate-100 text-slate-800 border-slate-200',
                };
                $actStatusLabel = match($actStatus) {
                    'awaiting_contact' => 'Menunggu Kontak Pemohon',
                    'awaiting_review'  => 'Kontak Masuk (Siap Ditinjau)',
                    'approved'         => 'Aktivasi Disetujui',
                    'rejected'         => 'Aktivasi Ditolak',
                    default            => ucfirst($actStatus),
                };
                $hasContact = ! empty($activationRequest['contact_confirmed_at']);
                ?>
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                            <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Alur Aktivasi Keanggotaan
                        </h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase border <?= $actBadge ?>">
                            <?= $actStatusLabel ?>
                        </span>
                    </div>

                    <div class="space-y-2.5 text-xs">
                        <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-500">Nomor Pengajuan</span>
                            <span class="font-mono font-bold text-brand-700 bg-brand-50 px-2 py-0.5 rounded-md">
                                <?= esc($activationRequest['application_reference']) ?>
                            </span>
                        </div>

                        <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-500">Verifikasi Kontak</span>
                            <?php if ($hasContact): ?>
                                <span class="text-emerald-700 font-bold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Dikonfirmasi
                                </span>
                            <?php else: ?>
                                <span class="text-amber-700 font-bold italic">Belum Ada Kontak</span>
                            <?php endif; ?>
                        </div>

                        <?php if ($hasContact): ?>
                            <div class="text-[11px] text-slate-500 bg-slate-50 p-2.5 rounded-xl border border-slate-100 space-y-0.5">
                                <div>Dikonfirmasi: <span class="font-mono text-slate-700"><?= date('d/m/Y H:i', strtotime($activationRequest['contact_confirmed_at'])) ?></span></div>
                                <?php if (! empty($activationRequest['review_notes'])): ?>
                                    <div>Catatan: <span class="text-slate-700"><?= nl2br(esc($activationRequest['review_notes'])) ?></span></div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Actions for Activation Workflow -->
                    <?php if (($member['status'] ?? '') === 'pending'): ?>
                        <div class="pt-2 border-t border-slate-100 space-y-2">
                            <?php if (! $hasContact): ?>
                                <form action="<?= base_url('admin/members/activation/confirm-contact/' . $member['user_id']) ?>" method="post">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="w-full py-2 px-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition flex items-center justify-center gap-1.5 shadow-xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                        Catat Konfirmasi Kontak Masuk
                                    </button>
                                </form>
                            <?php endif; ?>

                            <form action="<?= base_url('admin/members/activation/approve/' . $member['id']) ?>" method="post" class="space-y-2">
                                <?= csrf_field() ?>
                                <?php if (! $hasContact): ?>
                                    <label class="flex items-center gap-2 text-[11px] text-slate-600 cursor-pointer pt-1">
                                        <input type="checkbox" name="override_contact" value="1" class="rounded text-brand-600 focus:ring-brand-500">
                                        <span>Lewati syarat konfirmasi kontak (Override Admin)</span>
                                    </label>
                                <?php endif; ?>
                                <button type="submit" class="w-full py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition flex items-center justify-center gap-1.5 shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Setujui & Aktifkan Member
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Kelola Badge Anggota (Phase 5 Extension) -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Kelola Badge Anggota
                    </h3>
                    <span class="text-xs font-mono text-slate-400">Total: <?= count($memberBadges ?? []) ?></span>
                </div>

                <!-- Form Sematkan Badge -->
                <?php if (! empty($availableBadges)): ?>
                    <form action="<?= base_url('admin/members/badges/assign/' . $member['user_id']) ?>" method="post" class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                        <?= csrf_field() ?>
                        <span class="block text-[11px] font-black uppercase text-slate-700 tracking-wider">+ Sematkan Badge Baru</span>
                        
                        <div>
                            <select name="badge_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white focus:ring-2 focus:ring-brand-500">
                                <option value="">-- Pilih Badge --</option>
                                <?php foreach ($availableBadges as $bd): ?>
                                    <option value="<?= (int) $bd['id'] ?>">
                                        <?= esc($bd['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[10px] text-slate-500 font-bold mb-0.5">Masa Berlaku (Opsional)</label>
                                <input type="date" name="expires_at" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs bg-white">
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-500 font-bold mb-0.5">Catatan Internal</label>
                                <input type="text" name="internal_note" placeholder="Keterangan..." class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs bg-white">
                            </div>
                        </div>

                        <div class="flex justify-end pt-1">
                            <button type="submit" class="px-4 py-1.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-xs transition">
                                Sematkan Badge
                            </button>
                        </div>
                    </form>
                <?php endif; ?>

                <!-- List Assigned Badges -->
                <?php if (empty($memberBadges)): ?>
                    <p class="text-xs text-slate-400 italic text-center py-2">Belum ada badge yang disematkan ke anggota ini.</p>
                <?php else: ?>
                    <div class="space-y-2.5 pt-1">
                        <?php 
                        $now = date('Y-m-d H:i:s');
                        foreach ($memberBadges as $mb): 
                            $isRevoked = ! empty($mb['revoked_at']);
                            $isExpired = ! empty($mb['expires_at']) && $mb['expires_at'] < $now;
                            $isActive  = ! $isRevoked && ! $isExpired;
                        ?>
                            <div class="p-3 rounded-2xl border <?= $isActive ? 'bg-white border-slate-200 shadow-2xs' : 'bg-slate-50/60 border-slate-200/60 opacity-75' ?> text-xs space-y-1.5">
                                <div class="flex items-center justify-between gap-2">
                                    <?= komeo_render_badge($mb, 'sm', true) ?>

                                    <?php if ($isRevoked): ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">Dicabut</span>
                                    <?php elseif ($isExpired): ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">Kadaluarsa</span>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Aktif</span>
                                    <?php endif; ?>
                                </div>

                                <div class="text-[10px] text-slate-500 space-y-0.5 pt-0.5">
                                    <div>Disematkan: <?= date('d/m/Y', strtotime($mb['assigned_at'])) ?> oleh @<?= esc($mb['assigned_by_username'] ?: 'admin') ?></div>
                                    <?php if (! empty($mb['expires_at'])): ?>
                                        <div>Berlaku hingga: <?= date('d/m/Y', strtotime($mb['expires_at'])) ?></div>
                                    <?php endif; ?>
                                    <?php if (! empty($mb['internal_note'])): ?>
                                        <div class="italic text-slate-600 font-medium">Catatan: <?= esc($mb['internal_note']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <?php if ($isActive): ?>
                                    <div class="flex justify-end pt-1 border-t border-slate-100">
                                        <form action="<?= base_url('admin/members/badges/revoke/' . $mb['id']) ?>" method="post">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="px-2.5 py-1 rounded-lg text-[10px] font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition">
                                                Cabut Badge
                                            </button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Role Keanggotaan Komunitas (Phase 6.3 Module B) -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                            <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span>Role Keanggotaan Komunitas</span>
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">Penugasan sebutan peran resmi komunitas.</p>
                    </div>
                    <a href="<?= base_url('admin/member-roles') ?>" class="text-[11px] font-bold text-brand-600 hover:underline">
                        Kelola Role &rarr;
                    </a>
                </div>

                <!-- Form Assign Role -->
                <?php if (! empty($availableRoles)): ?>
                    <form action="<?= base_url('admin/members/roles/assign/' . $member['user_id']) ?>" method="post" class="space-y-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-200/70 text-xs">
                        <?= csrf_field() ?>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Pilih Role Komunitas:</label>
                            <select name="role_id" required class="w-full px-2.5 py-1.5 rounded-xl border border-slate-200 bg-white text-xs text-slate-800 focus:outline-none focus:border-brand-500">
                                <option value="">-- Pilih Role --</option>
                                <?php foreach ($availableRoles as $ar): ?>
                                    <option value="<?= $ar['id'] ?>">
                                        <?= esc($ar['name']) ?> <?= ! empty($ar['is_default']) ? '(Default)' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center gap-2 text-[11px] font-bold text-slate-700 cursor-pointer pt-1">
                                <input type="checkbox" name="is_primary" value="1" checked class="w-3.5 h-3.5 rounded text-brand-600 focus:ring-brand-500">
                                <span>Role Utama (Primary)</span>
                            </label>
                            <div>
                                <input type="date" name="expires_at" placeholder="Masa berlaku" title="Opsional: Masa berlaku role" class="w-full px-2 py-1 rounded-xl border border-slate-200 bg-white text-[11px] text-slate-800 focus:outline-none focus:border-brand-500">
                            </div>
                        </div>

                        <div>
                            <input type="text" name="internal_note" placeholder="Catatan penugasan (opsional)..." class="w-full px-2.5 py-1.5 rounded-xl border border-slate-200 bg-white text-[11px] text-slate-800 focus:outline-none focus:border-brand-500">
                        </div>

                        <div class="flex justify-end pt-1">
                            <button type="submit" class="px-4 py-1.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-xs transition">
                                Tugaskan Role
                            </button>
                        </div>
                    </form>
                <?php endif; ?>

                <!-- List Assigned Roles -->
                <?php if (empty($userRoles)): ?>
                    <p class="text-xs text-slate-400 italic text-center py-2">Belum ada role komunitas yang ditugaskan ke anggota ini.</p>
                <?php else: ?>
                    <div class="space-y-2 pt-1">
                        <?php foreach ($userRoles as $ur): ?>
                            <div class="p-3 rounded-2xl border bg-white border-slate-200 text-xs space-y-1.5 shadow-2xs">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <?= komeo_render_member_role($ur, 'xs', false) ?>
                                        <?php if (! empty($ur['is_primary'])): ?>
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-brand-50 text-brand-700 border border-brand-200">
                                                UTAMA
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="text-[10px] text-slate-400 font-mono">
                                        <?= date('d/m/Y', strtotime($ur['assigned_at'])) ?>
                                    </span>
                                </div>

                                <?php if (! empty($ur['internal_note'])): ?>
                                    <div class="text-[10px] text-slate-500 italic">
                                        <?= esc($ur['internal_note']) ?>
                                    </div>
                                <?php endif; ?>

                                <div class="flex justify-end pt-1 border-t border-slate-100">
                                    <form action="<?= base_url('admin/members/roles/revoke/' . $ur['assignment_id']) ?>" method="post">
                                        <?= csrf_field() ?>
                                        <button type="submit" onclick="return confirm('Cabut role komunitas ini dari anggota?')" class="px-2 py-0.5 rounded-lg text-[10px] font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition">
                                            Cabut Role
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Verifikasi Identitas & Legalitas Usaha Card (Phase 5 Extension) -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Verifikasi Identitas & Usaha</span>
                    </h3>
                    <?php if (! empty($verification)): ?>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase <?= match($verification['verification_status']) {
                            'approved' => 'bg-emerald-100 text-emerald-800',
                            'pending'  => 'bg-amber-100 text-amber-800 animate-pulse',
                            'rejected' => 'bg-rose-100 text-rose-800',
                            'revoked'  => 'bg-slate-100 text-slate-800',
                            default    => 'bg-slate-100 text-slate-600',
                        } ?>">
                            <?= esc($verification['verification_status']) ?>
                        </span>
                    <?php else: ?>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500">Belum Ada Data</span>
                    <?php endif; ?>
                </div>

                <div class="space-y-2 text-xs">
                    <?php if (! empty($verification)): ?>
                        <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-500">Jalur Verifikasi:</span>
                            <span class="font-bold text-slate-800"><?= esc($verification['verification_method']) ?></span>
                        </div>
                        <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-500">Level Saat Ini:</span>
                            <span class="font-mono font-bold text-slate-800"><?= esc($verification['verification_level']) ?></span>
                        </div>
                        <div class="pt-2">
                            <a href="<?= base_url('admin/identity-verifications/view/' . $verification['id']) ?>" class="w-full inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition shadow-2xs">
                                <span>Buka Halaman Pemeriksaan Dokumen</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    <?php else: ?>
                        <p class="text-slate-500 text-2xs italic">Member ini belum mengajukan verifikasi identitas / NIB.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Ringkasan Status Keanggotaan -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                <h3 class="text-sm font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                    Status Keanggotaan
                </h3>

                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Status Saat Ini</span>
                        <span class="font-bold px-2 py-0.5 rounded-full <?= $badgeColor ?>">
                            <?= $label ?>
                        </span>
                    </div>

                    <div class="flex items-center justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Nomor Anggota</span>
                        <span class="font-mono font-bold <?= ! empty($member['member_number']) ? 'text-slate-900' : 'text-slate-400 italic' ?>">
                            <?= esc($member['member_number'] ?: 'Belum Terbit') ?>
                        </span>
                    </div>

                    <div class="flex items-center justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Tanggal Daftar</span>
                        <span class="font-mono text-slate-700">
                            <?= date('d M Y H:i', strtotime($member['created_at'] ?? 'now')) ?>
                        </span>
                    </div>

                    <?php if (! empty($member['approved_at'])): ?>
                        <div class="flex items-center justify-between py-2 border-b border-slate-100">
                            <span class="text-slate-500">Disetujui Pada</span>
                            <span class="font-mono text-slate-700">
                                <?= date('d M Y H:i', strtotime($member['approved_at'])) ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <?php if (! empty($member['approver_username'])): ?>
                        <div class="flex items-center justify-between py-2 border-b border-slate-100">
                            <span class="text-slate-500">Diverifikasi Oleh</span>
                            <span class="font-bold text-slate-800 font-mono">
                                @<?= esc($member['approver_username']) ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <?php if (! empty($member['rejection_reason'])): ?>
                        <div class="p-3 bg-rose-50 rounded-xl border border-rose-200 text-rose-800">
                            <span class="block text-[10px] font-bold uppercase text-rose-600">Catatan Penolakan</span>
                            <span class="text-xs"><?= esc($member['rejection_reason']) ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (! empty($member['suspension_reason'])): ?>
                        <div class="p-3 bg-orange-50 rounded-xl border border-orange-200 text-orange-800">
                            <span class="block text-[10px] font-bold uppercase text-orange-600">Catatan Penangguhan</span>
                            <span class="text-xs"><?= esc($member['suspension_reason']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Link to Public Profile -->
                <?php if (! empty($member['profile_username'])): ?>
                    <div class="pt-2">
                        <a href="<?= base_url('member/' . esc($member['profile_username'])) ?>" 
                           target="_blank" 
                           class="w-full flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            Buka Halaman Publik Member
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Surat Peringatan & Disiplin Anggota Card -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Disiplin & Surat Peringatan (SP)
                    </h3>
                    <button type="button" 
                            onclick="openWarningModal(<?= (int)$member['id'] ?>, '<?= esc($member['full_name'] ?: $member['account_username'], 'js') ?>')" 
                            class="px-2.5 py-1 text-[11px] font-bold text-amber-900 bg-amber-50 hover:bg-amber-100 rounded-lg transition border border-amber-300">
                        + Beri SP
                    </button>
                </div>

                <?php if (empty($warnings)): ?>
                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 text-center">
                        <p class="text-xs text-slate-400">Tidak ada catatan pelanggaran atau surat peringatan aktif.</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($warnings as $w): ?>
                            <?php 
                            $wObj = (object) $w; 
                            $lvlBadge = match(strtolower($wObj->warning_level)) {
                                'sp1' => 'bg-amber-100 text-amber-900 border-amber-300',
                                'sp2' => 'bg-orange-100 text-orange-950 border-orange-300',
                                'sp3' => 'bg-rose-100 text-rose-950 border-rose-300',
                                default => 'bg-slate-100 text-slate-800 border-slate-200',
                            };
                            $stBadge = match(strtolower($wObj->status)) {
                                'active'   => 'bg-rose-50 text-rose-700 border-rose-200',
                                'resolved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'revoked'  => 'bg-slate-100 text-slate-600 border-slate-200',
                                default    => 'bg-slate-50 text-slate-600 border-slate-200',
                            };
                            ?>
                            <div class="p-3.5 rounded-2xl border <?= $wObj->status === 'active' ? 'bg-amber-50/40 border-amber-200' : 'bg-slate-50 border-slate-200' ?> text-xs space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md font-bold uppercase text-[10px] border <?= $lvlBadge ?>">
                                        <?= strtoupper($wObj->warning_level) ?>
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold border <?= $stBadge ?>">
                                        <?= $wObj->status === 'active' ? 'Aktif' : ($wObj->status === 'resolved' ? 'Diselesaikan' : 'Dicabut') ?>
                                    </span>
                                </div>

                                <div>
                                    <div class="font-bold text-slate-900 leading-snug"><?= esc($wObj->reason) ?></div>
                                    <?php if (! empty($wObj->notes)): ?>
                                        <div class="text-[11px] text-slate-500 mt-0.5">Catatan: <?= esc($wObj->notes) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="text-[10px] text-slate-400 font-mono flex items-center justify-between pt-1 border-t border-slate-200/60">
                                    <span><?= date('d/m/Y H:i', strtotime($wObj->created_at)) ?></span>
                                    <span>Oleh: <?= esc($wObj->issuer_name ?: ($wObj->issuer_username ? '@' . $wObj->issuer_username : 'Admin')) ?></span>
                                </div>

                                <?php if ($wObj->status === 'active'): ?>
                                    <div class="flex items-center justify-end gap-1.5 pt-1">
                                        <button type="button" 
                                                onclick="openResolveWarningModal(<?= (int)$wObj->id ?>, '<?= esc(strtoupper($wObj->warning_level), 'js') ?>', <?= $wObj->warning_level === 'sp3' ? 'true' : 'false' ?>)"
                                                class="px-2 py-1 rounded-lg text-[10px] font-bold bg-emerald-100 hover:bg-emerald-200 text-emerald-900 transition"
                                                title="Tandai masalah telah dipenuhi">
                                            Selesaikan SP
                                        </button>
                                        <button type="button" 
                                                onclick="openRevokeWarningModal(<?= (int)$wObj->id ?>, '<?= esc(strtoupper($wObj->warning_level), 'js') ?>', <?= $wObj->warning_level === 'sp3' ? 'true' : 'false' ?>)"
                                                class="px-2 py-1 rounded-lg text-[10px] font-bold bg-slate-200 hover:bg-slate-300 text-slate-700 transition"
                                                title="Batalkan SP (Kembali ke SP0/bersih)">
                                            Cabut SP (SP0)
                                        </button>
                                        <button type="button" 
                                                onclick="confirmDeleteWarning(<?= (int)$wObj->id ?>, '<?= esc(strtoupper($wObj->warning_level), 'js') ?>')"
                                                class="px-2 py-1 rounded-lg text-[10px] font-bold bg-rose-50 hover:bg-rose-100 text-rose-700 transition border border-rose-200"
                                                title="Hapus permanen surat peringatan">
                                            Hapus
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <div class="flex items-center justify-between pt-1">
                                        <?php if (! empty($wObj->resolution_notes)): ?>
                                            <div class="text-[10px] text-slate-500 bg-white/70 p-1.5 rounded-lg border border-slate-100 flex-1 mr-2">
                                                <em>Resolusi: <?= esc($wObj->resolution_notes) ?></em>
                                            </div>
                                        <?php else: ?>
                                            <div></div>
                                        <?php endif; ?>
                                        <button type="button" 
                                                onclick="confirmDeleteWarning(<?= (int)$wObj->id ?>, '<?= esc(strtoupper($wObj->warning_level), 'js') ?>')"
                                                class="px-2 py-1 rounded-lg text-[10px] font-bold bg-rose-50 hover:bg-rose-100 text-rose-600 transition"
                                                title="Hapus riwayat surat peringatan ini">
                                            Hapus
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Riwayat Status Keanggotaan Timeline -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                <h3 class="text-sm font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Riwayat Status Keanggotaan
                </h3>

                <?php if (empty($history)): ?>
                    <p class="text-xs text-slate-400 italic">Belum ada riwayat perubahan status.</p>
                <?php else: ?>
                    <div class="space-y-3.5 relative before:absolute before:inset-0 before:left-2.5 before:w-0.5 before:bg-slate-200">
                        <?php foreach ($history as $h): ?>
                            <?php $hObj = (object) $h; ?>
                            <div class="relative pl-7 text-xs space-y-1">
                                <div class="absolute left-1 top-1 w-3.5 h-3.5 rounded-full border-2 border-white bg-brand-600 shadow-xs"></div>
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900 uppercase tracking-wider text-[10px]">
                                        <?= esc($hObj->previous_status ?: 'Daftar') ?> &rarr; <?= esc($hObj->new_status) ?>
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-mono">
                                        <?= date('d/m/y H:i', strtotime($hObj->created_at)) ?>
                                    </span>
                                </div>
                                <?php if (! empty($hObj->reason)): ?>
                                    <p class="text-slate-600 bg-slate-50 p-2.5 rounded-lg border border-slate-100 text-[11px]">
                                        <?= esc($hObj->reason) ?>
                                    </p>
                                <?php endif; ?>
                                <span class="block text-[10px] text-slate-400">
                                    Oleh: <?= esc($hObj->admin_full_name ?: ($hObj->admin_username ? '@' . $hObj->admin_username : 'Sistem')) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Tolak Member -->
<div id="rejectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs hidden">
    <div class="bg-white rounded-3xl border border-slate-200 max-w-md w-full p-6 shadow-2xl relative">
        <h3 class="text-base font-extrabold text-slate-900 mb-1">Tolak Permohonan Keanggotaan</h3>
        <p class="text-xs text-slate-500 mb-4" id="rejectModalSubtitle">Beri alasan penolakan untuk member ini.</p>

        <form id="rejectForm" action="" method="post" class="space-y-4">
            <?= csrf_field() ?>
            <div>
                <label for="reject_reason" class="block text-xs font-bold text-slate-800 mb-1">Alasan Penolakan <span class="text-rose-500">*</span></label>
                <textarea id="reject_reason" 
                          name="reason" 
                          rows="3" 
                          required 
                          class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition" 
                          placeholder="Jelaskan alasan penolakan..."></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition shadow-xs">
                    Konfirmasi Penolakan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Tangguhkan Member -->
<div id="suspendModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs hidden">
    <div class="bg-white rounded-3xl border border-slate-200 max-w-md w-full p-6 shadow-2xl relative">
        <h3 class="text-base font-extrabold text-slate-900 mb-1">Tangguhkan Akun Member</h3>
        <p class="text-xs text-slate-500 mb-4" id="suspendModalSubtitle">Akun yang ditangguhkan akan kehilangan status publik dan hak anggota.</p>

        <form id="suspendForm" action="" method="post" class="space-y-4">
            <?= csrf_field() ?>
            <div>
                <label for="suspend_reason" class="block text-xs font-bold text-slate-800 mb-1">Alasan Penangguhan <span class="text-orange-500">*</span></label>
                <textarea id="suspend_reason" 
                          name="reason" 
                          rows="3" 
                          required 
                          class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition" 
                          placeholder="Jelaskan alasan penangguhan..."></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="closeSuspendModal()" class="px-4 py-2 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-orange-600 hover:bg-orange-700 rounded-xl transition shadow-xs">
                    Konfirmasi Penangguhan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Hidden POST form for Approve / Reactivate -->
<form id="actionPostForm" action="" method="post" class="hidden">
    <?= csrf_field() ?>
    <input type="hidden" name="reason" id="actionReasonInput" value="">
</form>

<!-- Modal: Reset Password by Admin -->
<div id="resetPasswordModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs hidden">
    <div class="bg-white rounded-3xl border border-slate-200 max-w-md w-full p-6 shadow-2xl relative">
        <div class="flex items-center gap-3 mb-3">
            <span class="w-9 h-9 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
            </span>
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Reset Password Member</h3>
                <p class="text-xs text-slate-500" id="resetModalSubtitle">Atur ulang password login member.</p>
            </div>
        </div>

        <form id="resetPasswordForm" action="" method="post" class="space-y-4">
            <?= csrf_field() ?>

            <!-- Opsi Mode Password -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-800">Opsi Password Baru</label>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 font-medium">
                        <input type="radio" name="mode" value="auto" checked onchange="toggleManualPwd(false)" class="text-brand-600 focus:ring-brand-500">
                        <span>Acak Otomatis (Rekomendasi)</span>
                    </label>
                    <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 font-medium">
                        <input type="radio" name="mode" value="manual" onchange="toggleManualPwd(true)" class="text-brand-600 focus:ring-brand-500">
                        <span>Tentukan Manual</span>
                    </label>
                </div>
            </div>

            <!-- Custom Password Input (Hidden by default) -->
            <div id="manualPwdContainer" class="hidden">
                <label for="custom_password" class="block text-xs font-bold text-slate-800 mb-1">Password Baru <span class="text-rose-500">*</span></label>
                <input type="text" id="custom_password" name="custom_password" placeholder="Minimal 8 karakter..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500">
            </div>

            <!-- Opsi Notifikasi Pengiriman -->
            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
                <span class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">Kirim Notifikasi Password:</span>
                
                <label class="flex items-center gap-2 text-xs text-slate-800 cursor-pointer">
                    <input type="checkbox" name="notify_email" value="1" checked class="rounded text-brand-600 focus:ring-brand-500">
                    <span>Kirim ke Email Resmi Member (<span id="resetEmailTarget" class="font-mono text-slate-500 text-[11px]"></span>)</span>
                </label>

                <label class="flex items-center gap-2 text-xs text-slate-800 cursor-pointer">
                    <input type="checkbox" name="notify_whatsapp" value="1" checked class="rounded text-brand-600 focus:ring-brand-500">
                    <span>Siapkan Kirim via WhatsApp (<span id="resetWaTarget" class="font-mono text-slate-500 text-[11px]"></span>)</span>
                </label>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="closeResetPasswordModal()" class="px-4 py-2 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-xl transition shadow-xs flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan & Reset Password
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Terbitkan Surat Peringatan (SP) -->
<div id="issueWarningModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs hidden">
    <div class="bg-white rounded-3xl border border-slate-200 max-w-lg w-full p-6 shadow-2xl relative">
        <div class="flex items-center gap-3 mb-3">
            <span class="w-9 h-9 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </span>
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Terbitkan Surat Peringatan (SP)</h3>
                <p class="text-xs text-slate-500" id="issueWarningModalSubtitle">Peringatan disiplin resmi untuk member.</p>
            </div>
        </div>

        <form id="issueWarningForm" action="" method="post" class="space-y-4">
            <?= csrf_field() ?>

            <!-- Tingkat Peringatan (Radio cards) -->
            <div>
                <label class="block text-xs font-bold text-slate-800 mb-1.5">Tingkat Surat Peringatan <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-3 gap-2.5 text-xs">
                    <label class="p-3 rounded-2xl border-2 border-amber-300 bg-amber-50/50 cursor-pointer hover:bg-amber-100/50 transition flex flex-col items-center text-center">
                        <input type="radio" name="warning_level" value="sp1" checked class="text-amber-600 focus:ring-amber-500 mb-1">
                        <span class="font-extrabold text-amber-950">SP 1</span>
                        <span class="text-[10px] text-amber-800 mt-0.5">Teguran Pertama</span>
                    </label>

                    <label class="p-3 rounded-2xl border-2 border-orange-300 bg-orange-50/50 cursor-pointer hover:bg-orange-100/50 transition flex flex-col items-center text-center">
                        <input type="radio" name="warning_level" value="sp2" class="text-orange-600 focus:ring-orange-500 mb-1">
                        <span class="font-extrabold text-orange-950">SP 2</span>
                        <span class="text-[10px] text-orange-800 mt-0.5">Peringatan Keras</span>
                    </label>

                    <label class="p-3 rounded-2xl border-2 border-rose-300 bg-rose-50/50 cursor-pointer hover:bg-rose-100/50 transition flex flex-col items-center text-center">
                        <input type="radio" name="warning_level" value="sp3" class="text-rose-600 focus:ring-rose-500 mb-1">
                        <span class="font-extrabold text-rose-950">SP 3</span>
                        <span class="text-[10px] text-rose-800 mt-0.5">Penangguhan Akun</span>
                    </label>
                </div>
                <p class="text-[11px] text-rose-600 font-medium mt-2 bg-rose-50 p-2.5 rounded-xl border border-rose-200">
                    <strong>PENTING:</strong> Menerbitkan <strong>SP3</strong> akan secara otomatis menangguhkan status keanggotaan member (status berubah menjadi <em>suspended</em>) dan menonaktifkan hak akses publik & KTA.
                </p>
            </div>

            <!-- Alasan Pelanggaran -->
            <div>
                <label for="warn_reason" class="block text-xs font-bold text-slate-800 mb-1">Alasan Pelanggaran / Teguran <span class="text-rose-500">*</span></label>
                <textarea id="warn_reason" 
                          name="reason" 
                          rows="3" 
                          required 
                          class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition" 
                          placeholder="Jelaskan jenis pelanggaran kode etik, wanprestasi, ketidaksesuaian data..."></textarea>
            </div>

            <!-- Catatan / Arahan Perbaikan -->
            <div>
                <label for="warn_notes" class="block text-xs font-bold text-slate-800 mb-1">Catatan / Arahan Perbaikan untuk Member <span class="text-slate-400 font-normal">(Opsional)</span></label>
                <textarea id="warn_notes" 
                          name="notes" 
                          rows="2" 
                          class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition" 
                          placeholder="Langkah perbaikan atau dokumen klarifikasi yang harus diserahkan..."></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="closeWarningModal()" class="px-4 py-2 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 rounded-xl transition shadow-xs flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Terbitkan Surat Peringatan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Selesaikan Surat Peringatan -->
<div id="resolveWarningModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs hidden">
    <div class="bg-white rounded-3xl border border-slate-200 max-w-md w-full p-6 shadow-2xl relative">
        <h3 class="text-base font-extrabold text-slate-900 mb-1" id="resolveModalTitle">Selesaikan Surat Peringatan</h3>
        <p class="text-xs text-slate-500 mb-4">Tandai peringatan ini telah dipenuhi atau diselesaikan oleh member.</p>

        <form id="resolveWarningForm" action="" method="post" class="space-y-4">
            <?= csrf_field() ?>
            <div>
                <label for="resolution_notes" class="block text-xs font-bold text-slate-800 mb-1">Catatan Penyelesaian</label>
                <textarea id="resolution_notes" 
                          name="resolution_notes" 
                          rows="3" 
                          class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition" 
                          placeholder="Jelaskan bentuk klarifikasi atau komitmen perbaikan dari member..."></textarea>
            </div>

            <div id="resolveReactivateContainer" class="p-3 bg-emerald-50 rounded-xl border border-emerald-200">
                <label class="flex items-center gap-2 text-xs font-bold text-emerald-950 cursor-pointer">
                    <input type="checkbox" name="reactivate" value="1" checked class="rounded text-emerald-600 focus:ring-emerald-500">
                    <span>Pulihkan Keanggotaan ke Status Aktif</span>
                </label>
                <p class="text-[11px] text-emerald-700 mt-1">Mengembalikan hak akses KTA dan visibilitas direktori publik member.</p>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="closeResolveWarningModal()" class="px-4 py-2 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition shadow-xs">
                    Selesaikan SP
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Cabut Surat Peringatan -->
<div id="revokeWarningModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs hidden">
    <div class="bg-white rounded-3xl border border-slate-200 max-w-md w-full p-6 shadow-2xl relative">
        <h3 class="text-base font-extrabold text-slate-900 mb-1" id="revokeModalTitle">Cabut Surat Peringatan</h3>
        <p class="text-xs text-slate-500 mb-4">Batalkan surat peringatan jika terjadi kesalahan administrasi atau banding diterima.</p>

        <form id="revokeWarningForm" action="" method="post" class="space-y-4">
            <?= csrf_field() ?>
            <div>
                <label for="revoke_notes" class="block text-xs font-bold text-slate-800 mb-1">Alasan Pencabutan SP</label>
                <textarea id="revoke_notes" 
                          name="notes" 
                          rows="3" 
                          class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-slate-500 focus:border-slate-500 transition" 
                          placeholder="Jelaskan alasan pencabutan surat peringatan..."></textarea>
            </div>

            <div id="revokeReactivateContainer" class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                <label class="flex items-center gap-2 text-xs font-bold text-slate-800 cursor-pointer">
                    <input type="checkbox" name="reactivate" value="1" checked class="rounded text-brand-600 focus:ring-brand-500">
                    <span>Pulihkan Keanggotaan ke Status Aktif (jika saat ini ditangguhkan)</span>
                </label>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="closeRevokeWarningModal()" class="px-4 py-2 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-slate-800 hover:bg-slate-900 rounded-xl transition shadow-xs">
                    Cabut SP
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function confirmApprove(id, name) {
    window.KomeoModal.confirm({
        title: 'Setujui Keanggotaan',
        message: 'Apakah Anda yakin ingin MENYETUJUI keanggotaan untuk "' + name + '"?\n\nSistem akan menerbitkan Nomor Anggota (KMO-YYYY-XXXXXX) secara otomatis.',
        confirmText: 'Ya, Setujui',
        cancelText: 'Batal',
        type: 'success',
        onConfirm: function() {
            const form = document.getElementById('actionPostForm');
            form.action = '<?= base_url('admin/members/approve') ?>/' + id;
            form.submit();
        }
    });
}

function confirmReactivate(id, name) {
    window.KomeoModal.confirm({
        title: 'Aktifkan Kembali Keanggotaan',
        message: 'Apakah Anda yakin ingin MENGAKTIFKAN KEMBALI keanggotaan untuk "' + name + '"?',
        confirmText: 'Ya, Aktifkan',
        cancelText: 'Batal',
        type: 'info',
        onConfirm: function() {
            const form = document.getElementById('actionPostForm');
            form.action = '<?= base_url('admin/members/reactivate') ?>/' + id;
            form.submit();
        }
    });
}

function openRejectModal(id, name) {
    document.getElementById('rejectModalSubtitle').textContent = 'Member: ' + name;
    document.getElementById('rejectForm').action = '<?= base_url('admin/members/reject') ?>/' + id;
    document.getElementById('reject_reason').value = '';
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

function openSuspendModal(id, name) {
    document.getElementById('suspendModalSubtitle').textContent = 'Member: ' + name;
    document.getElementById('suspendForm').action = '<?= base_url('admin/members/suspend') ?>/' + id;
    document.getElementById('suspend_reason').value = '';
    document.getElementById('suspendModal').classList.remove('hidden');
}

function closeSuspendModal() {
    document.getElementById('suspendModal').classList.add('hidden');
}

function openResetPasswordModal(id, name, email, wa) {
    document.getElementById('resetModalSubtitle').textContent = 'Member: ' + name;
    document.getElementById('resetEmailTarget').textContent = email || 'tidak ada';
    document.getElementById('resetWaTarget').textContent = wa || 'tidak ada';
    document.getElementById('resetPasswordForm').action = '<?= base_url('admin/members/reset-password') ?>/' + id;
    document.getElementById('custom_password').value = '';
    document.getElementById('resetPasswordModal').classList.remove('hidden');
}

function closeResetPasswordModal() {
    document.getElementById('resetPasswordModal').classList.add('hidden');
}

function toggleManualPwd(isManual) {
    const el = document.getElementById('manualPwdContainer');
    if (isManual) {
        el.classList.remove('hidden');
        document.getElementById('custom_password').setAttribute('required', 'required');
    } else {
        el.classList.add('hidden');
        document.getElementById('custom_password').removeAttribute('required');
    }
}

function openWarningModal(id, name) {
    document.getElementById('issueWarningModalSubtitle').textContent = 'Member: ' + name;
    document.getElementById('issueWarningForm').action = '<?= base_url('admin/members/warning/issue') ?>/' + id;
    document.getElementById('warn_reason').value = '';
    document.getElementById('warn_notes').value = '';
    document.getElementById('issueWarningModal').classList.remove('hidden');
}

function closeWarningModal() {
    document.getElementById('issueWarningModal').classList.add('hidden');
}

function openResolveWarningModal(id, level, isSp3) {
    document.getElementById('resolveModalTitle').textContent = 'Selesaikan ' + level;
    document.getElementById('resolveWarningForm').action = '<?= base_url('admin/members/warning/resolve') ?>/' + id;
    document.getElementById('resolution_notes').value = '';
    const reactivateBox = document.getElementById('resolveReactivateContainer');
    if (isSp3) {
        reactivateBox.classList.remove('hidden');
    } else {
        reactivateBox.classList.add('hidden');
    }
    document.getElementById('resolveWarningModal').classList.remove('hidden');
}

function closeResolveWarningModal() {
    document.getElementById('resolveWarningModal').classList.add('hidden');
}

function openRevokeWarningModal(id, level, isSp3) {
    document.getElementById('revokeModalTitle').textContent = 'Cabut ' + level;
    document.getElementById('revokeWarningForm').action = '<?= base_url('admin/members/warning/revoke') ?>/' + id;
    document.getElementById('revoke_notes').value = '';
    const reactivateBox = document.getElementById('revokeReactivateContainer');
    if (isSp3) {
        reactivateBox.classList.remove('hidden');
    } else {
        reactivateBox.classList.add('hidden');
    }
    document.getElementById('revokeWarningModal').classList.remove('hidden');
}

function closeRevokeWarningModal() {
    document.getElementById('revokeWarningModal').classList.add('hidden');
}

function confirmDeleteWarning(id, level) {
    window.KomeoModal.confirm({
        title: 'Hapus ' + level,
        message: 'Apakah Anda yakin ingin MENGHAPUS PERMANEN ' + level + ' ini?\n\nJika ini adalah satu-satunya surat peringatan aktif, status member akan otomatis kembali bersih ke SP0 (tanpa sanksi/peringatan).',
        confirmText: 'Ya, Hapus Permanen',
        cancelText: 'Batal',
        type: 'danger',
        onConfirm: function() {
            const form = document.getElementById('actionPostForm');
            form.action = '<?= base_url('admin/members/warning/delete') ?>/' + id;
            form.submit();
        }
    });
}
</script>

<?= $this->endSection() ?>
