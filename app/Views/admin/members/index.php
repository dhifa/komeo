<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="space-y-6">
    <!-- Header with Quick Stats -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Manajemen Member & Verifikasi</h2>
            <p class="text-xs text-slate-500 mt-1">Kelola permohonan keanggotaan, verifikasi profil, penerbitan nomor anggota, dan status akun</p>
        </div>

        <div class="flex items-center gap-2">
            <?php if (($stats['pending'] ?? 0) > 0): ?>
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold bg-amber-500 text-slate-950 shadow-sm animate-pulse">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <?= $stats['pending'] ?> Menunggu Persetujuan
                </span>
            <?php else: ?>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Semua Pengajuan Telah Diproses
                </span>
            <?php endif; ?>
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

    <!-- Reset Password Result Notification Card -->
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
                    Kredensial Baru
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                <div class="p-3 bg-white rounded-2xl border border-emerald-200 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Password Baru:</span>
                        <code class="text-sm font-mono font-bold text-slate-900 tracking-wider"><?= esc($resetRes['password']) ?></code>
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

    <!-- Status Tabs Bar -->
    <?php 
    $curStatus = $filters['status'] ?? ''; 
    $baseRoute = base_url('admin/members');
    ?>
    <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs font-bold select-none">
        <a href="<?= $baseRoute ?>?<?= http_build_query(array_merge($filters, ['status' => ''])) ?>" 
           class="px-4 py-2 rounded-xl transition whitespace-nowrap <?= empty($curStatus) ? 'bg-brand-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' ?>">
            Semua (<?= $stats['total'] ?? 0 ?>)
        </a>
        <a href="<?= $baseRoute ?>?<?= http_build_query(array_merge($filters, ['status' => 'pending'])) ?>" 
           class="px-4 py-2 rounded-xl transition whitespace-nowrap flex items-center gap-1.5 <?= $curStatus === 'pending' ? 'bg-amber-500 text-slate-950 font-extrabold shadow-xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-amber-50' ?>">
            <span>Menunggu (<?= $stats['pending'] ?? 0 ?>)</span>
            <?php if (($stats['pending'] ?? 0) > 0): ?>
                <span class="w-2 h-2 rounded-full bg-amber-600 <?= $curStatus === 'pending' ? 'bg-slate-900' : '' ?>"></span>
            <?php endif; ?>
        </a>
        <a href="<?= $baseRoute ?>?<?= http_build_query(array_merge($filters, ['status' => 'active'])) ?>" 
           class="px-4 py-2 rounded-xl transition whitespace-nowrap <?= $curStatus === 'active' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-emerald-50' ?>">
            Aktif (<?= $stats['active'] ?? 0 ?>)
        </a>
        <a href="<?= $baseRoute ?>?<?= http_build_query(array_merge($filters, ['status' => 'rejected'])) ?>" 
           class="px-4 py-2 rounded-xl transition whitespace-nowrap <?= $curStatus === 'rejected' ? 'bg-rose-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-rose-50' ?>">
            Ditolak (<?= $stats['rejected'] ?? 0 ?>)
        </a>
        <a href="<?= $baseRoute ?>?<?= http_build_query(array_merge($filters, ['status' => 'suspended'])) ?>" 
           class="px-4 py-2 rounded-xl transition whitespace-nowrap <?= $curStatus === 'suspended' ? 'bg-orange-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-orange-50' ?>">
            Ditangguhkan (<?= $stats['suspended'] ?? 0 ?>)
        </a>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <form action="<?= base_url('admin/members') ?>" method="get" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 items-end">
            <?php if (! empty($filters['status'])): ?>
                <input type="hidden" name="status" value="<?= esc($filters['status']) ?>">
            <?php endif; ?>

            <!-- Search Query -->
            <div class="lg:col-span-2">
                <label for="f_q" class="block text-xs font-bold text-slate-700 mb-1">Cari Anggota</label>
                <div class="relative">
                    <input type="text" 
                           id="f_q" 
                           name="q" 
                           value="<?= esc($filters['q'] ?? '') ?>" 
                           class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition" 
                           placeholder="Nama, email, username, no. KMO...">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>
            </div>

            <!-- Kategori Industri -->
            <div>
                <label for="f_cat" class="block text-xs font-bold text-slate-700 mb-1">Kategori Industri</label>
                <select id="f_cat" name="category_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                    <option value="">-- Semua Kategori --</option>
                    <?php foreach ($categories as $cat): ?>
                        <?php $catObj = (object) $cat; ?>
                        <option value="<?= $catObj->id ?>" <?= (string)($filters['category_id'] ?? '') === (string)$catObj->id ? 'selected' : '' ?>>
                            <?= esc($catObj->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Lokasi -->
            <div>
                <label for="f_loc" class="block text-xs font-bold text-slate-700 mb-1">Kota / Domisili</label>
                <input type="text" 
                       id="f_loc" 
                       name="location" 
                       value="<?= esc($filters['location'] ?? '') ?>" 
                       class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition" 
                       placeholder="Contoh: Jakarta, Surabaya...">
            </div>

            <!-- Actions (Filter & Reset) -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-xl transition shadow-xs text-center">
                    Terapkan
                </button>
                <a href="<?= base_url('admin/members') ?>" class="px-3 py-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition text-center" title="Reset Filter">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Members Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 border-b border-slate-200/80 text-slate-500 font-bold uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Member / Kontak</th>
                        <th class="px-6 py-4">Tipe & Entitas</th>
                        <th class="px-6 py-4">Nomor Anggota</th>
                        <th class="px-6 py-4">Kategori & Domisili</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Tgl Daftar</th>
                        <th class="px-6 py-4 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($members)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 italic">
                                Tidak ada data member yang sesuai dengan filter pencarian.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($members as $m): ?>
                            <?php $mObj = (object) $m; ?>
                            <tr class="hover:bg-slate-50/70 transition">
                                <!-- Member Identity -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-slate-100 text-brand-600 font-bold flex items-center justify-center shrink-0 border border-slate-200 overflow-hidden">
                                            <?php if (! empty($mObj->photo_path) && file_exists(FCPATH . $mObj->photo_path)): ?>
                                                <img src="<?= base_url(esc($mObj->photo_path)) ?>" alt="" class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <?= strtoupper(substr($mObj->full_name ?: $mObj->account_username, 0, 1)) ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="min-w-0">
                                            <a href="<?= base_url('admin/members/view/' . $mObj->id) ?>" class="block font-bold text-slate-900 hover:text-brand-600 transition truncate">
                                                <?= esc($mObj->display_name ?: $mObj->full_name ?: $mObj->account_username) ?>
                                            </a>
                                            <span class="block text-[11px] text-slate-400 font-mono truncate">
                                                @<?= esc($mObj->profile_username ?: $mObj->account_username) ?> &bull; <?= esc($mObj->email) ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Member Type -->
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900">
                                        <?= ($mObj->member_type ?? 'individual') === 'business' ? 'Perusahaan / Bisnis' : 'Individu / Freelancer' ?>
                                    </div>
                                    <?php if (! empty($mObj->business_name)): ?>
                                        <div class="text-[11px] text-brand-600 font-semibold truncate max-w-[180px]">
                                            <?= esc($mObj->business_name) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <!-- Member Number -->
                                <td class="px-6 py-4">
                                    <?php 
                                        $memberNum = $mObj->member_number ?: $mObj->membership_number ?: ($mObj->toRawArray()['member_number'] ?? null);
                                    ?>
                                    <?php if (! empty($memberNum)): ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-slate-900 text-white tracking-wider">
                                            <?= esc($memberNum) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-xs text-slate-400 italic font-mono">Belum Diterbitkan</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Category & Location -->
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-900">
                                        <?= esc($mObj->category_name ?: 'Kategori Belum Dipilih') ?>
                                    </div>
                                    <div class="text-[11px] text-slate-400 truncate">
                                        <?= esc($mObj->city ? $mObj->city . ($mObj->province ? ', ' . $mObj->province : '') : 'Lokasi belum disetel') ?>
                                    </div>
                                </td>

                                <!-- Status Badge -->
                                <td class="px-6 py-4">
                                    <?php 
                                    $st = $mObj->status ?? 'pending';
                                    $badgeColor = match($st) {
                                        'active'    => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                                        'pending'   => 'bg-amber-100 text-amber-800 border border-amber-200',
                                        'rejected'  => 'bg-rose-100 text-rose-800 border border-rose-200',
                                        'suspended' => 'bg-orange-100 text-orange-800 border border-orange-200',
                                        default     => 'bg-slate-100 text-slate-700',
                                    };
                                    $label = match($st) {
                                        'active'    => 'Aktif',
                                        'pending'   => 'Menunggu',
                                        'rejected'  => 'Ditolak',
                                        'suspended' => 'Ditangguhkan',
                                        default     => ucfirst($st),
                                    };
                                    ?>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold <?= $badgeColor ?>">
                                        <span class="w-1.5 h-1.5 rounded-full <?= $st === 'active' ? 'bg-emerald-500' : ($st === 'pending' ? 'bg-amber-500' : 'bg-rose-500') ?>"></span>
                                        <?= $label ?>
                                    </span>

                                    <?php if (! empty($mObj->active_warning_level)): ?>
                                        <div class="mt-1">
                                            <?php 
                                            $warnLvl = strtolower($mObj->active_warning_level);
                                            $warnBadgeStyle = match($warnLvl) {
                                                'sp1' => 'bg-amber-100 text-amber-900 border-amber-300 hover:bg-amber-200',
                                                'sp2' => 'bg-orange-100 text-orange-950 border-orange-300 hover:bg-orange-200',
                                                'sp3' => 'bg-rose-100 text-rose-950 border-rose-300 hover:bg-rose-200',
                                                default => 'bg-slate-100 text-slate-800 border-slate-300',
                                            };
                                            ?>
                                            <a href="<?= base_url('admin/members/view/' . $mObj->id) ?>" 
                                               class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black uppercase border transition <?= $warnBadgeStyle ?>" 
                                               title="Memiliki Surat Peringatan Aktif (Klik untuk Kelola / Cabut ke SP0)">
                                                <?= strtoupper($mObj->active_warning_level) ?> &bull; Kelola
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <!-- Created At -->
                                <td class="px-6 py-4 font-mono text-slate-500 whitespace-nowrap">
                                    <?= date('d/m/Y', strtotime($mObj->created_at ?? 'now')) ?>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="<?= base_url('admin/members/view/' . $mObj->id) ?>" 
                                            class="px-2.5 py-1.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition" 
                                            title="Lihat Detail & Tinjau Profil">
                                            Detail
                                        </a>

                                        <!-- Reset Password Quick Action -->
                                        <button type="button" 
                                                onclick="openResetPasswordModal(<?= (int)$mObj->id ?>, '<?= esc($mObj->full_name ?: $mObj->account_username, 'js') ?>', '<?= esc($mObj->email ?? '', 'js') ?>', '<?= esc($mObj->whatsapp ?? '', 'js') ?>')" 
                                                class="px-2 py-1.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition" 
                                                title="Reset Password Member">
                                            <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                        </button>

                                        <!-- Terbitkan SP Quick Action -->
                                        <button type="button" 
                                                onclick="openWarningModal(<?= (int)$mObj->id ?>, '<?= esc($mObj->full_name ?: $mObj->account_username, 'js') ?>')" 
                                                class="px-2 py-1.5 text-xs font-bold text-amber-900 bg-amber-50 hover:bg-amber-100 rounded-lg transition border border-amber-300" 
                                                title="Terbitkan Surat Peringatan (SP1, SP2, SP3)">
                                            <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        </button>

                                        <?php if ($mObj->status === 'pending'): ?>
                                            <button type="button" 
                                                    onclick="confirmApprove(<?= (int)$mObj->id ?>, '<?= esc($mObj->full_name ?: $mObj->account_username, 'js') ?>')" 
                                                    class="px-2.5 py-1.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition shadow-xs" 
                                                    title="Setujui Keanggotaan">
                                                Setujui
                                            </button>
                                            <button type="button" 
                                                    onclick="openRejectModal(<?= (int)$mObj->id ?>, '<?= esc($mObj->full_name ?: $mObj->account_username, 'js') ?>')" 
                                                    class="px-2.5 py-1.5 text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg transition border border-rose-200" 
                                                    title="Tolak Pengajuan">
                                                Tolak
                                            </button>
                                        <?php elseif ($mObj->status === 'active'): ?>
                                            <button type="button" 
                                                    onclick="openSuspendModal(<?= (int)$mObj->id ?>, '<?= esc($mObj->full_name ?: $mObj->account_username, 'js') ?>')" 
                                                    class="px-2.5 py-1.5 text-xs font-bold text-orange-700 bg-orange-50 hover:bg-orange-100 rounded-lg transition border border-orange-200" 
                                                    title="Tangguhkan Keanggotaan">
                                                Tangguhkan
                                            </button>
                                        <?php elseif (in_array($mObj->status, ['suspended', 'rejected'], true)): ?>
                                            <button type="button" 
                                                    onclick="confirmReactivate(<?= (int)$mObj->id ?>, '<?= esc($mObj->full_name ?: $mObj->account_username, 'js') ?>')" 
                                                    class="px-2.5 py-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition border border-emerald-200" 
                                                    title="Aktifkan Kembali">
                                                Aktifkan
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Pager -->
        <?php if ($pager): ?>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                <?= $pager->links('default', 'default_full') ?>
            </div>
        <?php endif; ?>
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
                          placeholder="Jelaskan alasan penolakan, misalnya: data identitas tidak valid, bidang usaha tidak sesuai event..."></textarea>
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
                          placeholder="Contoh: Laporan pelanggaran kode etik komunitas event..."></textarea>
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
</script>

<?= $this->endSection() ?>
