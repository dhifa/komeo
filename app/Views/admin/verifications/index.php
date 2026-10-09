<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <!-- Header & Stats Summary -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-800">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight flex items-center gap-2.5">
                <span>Verifikasi Identitas & Legalitas Usaha</span>
                <?php if (($counts['pending'] ?? 0) > 0): ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-black bg-blue-500 text-white animate-pulse">
                        <?= $counts['pending'] ?> Perlu Ditinjau
                    </span>
                <?php endif; ?>
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">
                Pemeriksaan manual dokumen KTP, Selfie, dan NIB untuk penyematan lencana terverifikasi resmi KOMEO.ID.
            </p>
        </div>
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

    <!-- Status Filter Tabs & Search -->
    <div class="bg-slate-900 rounded-3xl p-5 border border-slate-800 space-y-4">
        
        <!-- Status Tabs -->
        <div class="flex flex-wrap items-center gap-2 border-b border-slate-800 pb-3">
            <?php
            $currStatus = $filters['status'] ?? '';
            $statusTabs = [
                ''         => ['label' => 'Semua Permohonan', 'count' => $total],
                'pending'  => ['label' => 'Menunggu Pemeriksaan', 'count' => $counts['pending'] ?? 0],
                'approved' => ['label' => 'Terverifikasi', 'count' => $counts['approved'] ?? 0],
                'rejected' => ['label' => 'Ditolak', 'count' => $counts['rejected'] ?? 0],
                'revoked'  => ['label' => 'Dicabut', 'count' => $counts['revoked'] ?? 0],
            ];
            ?>
            <?php foreach ($statusTabs as $sKey => $sTab): ?>
                <a href="<?= base_url('admin/identity-verifications?' . http_build_query(array_merge($filters, ['status' => $sKey, 'page' => 1]))) ?>" 
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-colors <?= $currStatus === $sKey ? 'bg-brand-600 text-white shadow-xs' : 'text-slate-400 hover:text-white hover:bg-slate-800' ?>">
                    <span><?= $sTab['label'] ?></span>
                    <span class="px-2 py-0.5 rounded-full text-2xs <?= $currStatus === $sKey ? 'bg-white/20 text-white' : 'bg-slate-800 text-slate-400' ?>">
                        <?= $sTab['count'] ?>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Filter Form: Method & Keyword Search -->
        <form action="<?= base_url('admin/identity-verifications') ?>" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <?php if (! empty($filters['status'])): ?>
                <input type="hidden" name="status" value="<?= esc($filters['status']) ?>">
            <?php endif; ?>

            <!-- Pathway Filter -->
            <div class="sm:col-span-4">
                <select name="method" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-xs font-medium text-slate-300 focus:outline-none focus:border-brand-500" onchange="this.form.submit()">
                    <option value="">Semua Jalur Verifikasi</option>
                    <option value="ktp_selfie" <?= ($filters['method'] ?? '') === 'ktp_selfie' ? 'selected' : '' ?>>Individu (KTP & Selfie)</option>
                    <option value="pic_only" <?= ($filters['method'] ?? '') === 'pic_only' ? 'selected' : '' ?>>Bisnis Perorangan (PIC Only)</option>
                    <option value="nib_pic" <?= ($filters['method'] ?? '') === 'nib_pic' ? 'selected' : '' ?>>Bisnis Terdaftar (NIB & PIC)</option>
                </select>
            </div>

            <!-- Keyword Search -->
            <div class="sm:col-span-6">
                <div class="relative">
                    <input type="text" name="q" value="<?= esc($filters['q'] ?? '') ?>" placeholder="Cari nama, bisnis, PIC, nomor anggota..." class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-xs font-medium text-slate-300 placeholder-slate-500 focus:outline-none focus:border-brand-500">
                    <svg class="w-4 h-4 text-slate-500 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <!-- Actions -->
            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="flex-1 py-2.5 px-4 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs transition-colors">
                    Filter
                </button>
                <?php if (! empty($filters['q']) || ! empty($filters['method']) || ! empty($filters['status'])): ?>
                    <a href="<?= base_url('admin/identity-verifications') ?>" class="p-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white transition-colors" title="Reset Filter">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Verifications Table -->
    <div class="bg-slate-900 rounded-3xl border border-slate-800 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 bg-slate-950/40 text-slate-400 font-bold uppercase tracking-wider">
                        <th class="py-3.5 px-4">Pemohon & Akun</th>
                        <th class="py-3.5 px-4">Kategori & Jalur</th>
                        <th class="py-3.5 px-4">Nama Bisnis & PIC</th>
                        <th class="py-3.5 px-4">Dokumen</th>
                        <th class="py-3.5 px-4">Status & Level</th>
                        <th class="py-3.5 px-4">Update</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 text-slate-300">
                    <?php if (empty($verifications)): ?>
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">
                                Tidak ada permohonan verifikasi yang sesuai dengan filter pencarian.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($verifications as $v): ?>
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                
                                <!-- User & Account -->
                                <td class="py-3.5 px-4">
                                    <div class="font-extrabold text-white text-sm">
                                        <?= esc($v['display_name'] ?: $v['full_name']) ?>
                                    </div>
                                    <div class="text-2xs text-slate-400 font-mono flex items-center gap-1.5 mt-0.5">
                                        <span>@<?= esc($v['account_username'] ?? $v['profile_username']) ?></span>
                                        <?php if (! empty($v['member_number'])): ?>
                                            <span>&bull;</span>
                                            <span class="text-brand-400 font-bold"><?= esc($v['member_number']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <!-- Category & Pathway -->
                                <td class="py-3.5 px-4">
                                    <?php if ($v['verification_method'] === 'nib_pic'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-2xs font-extrabold bg-blue-500/10 text-blue-400 border border-blue-500/30">
                                            🏢 Bisnis NIB Resmi
                                        </span>
                                    <?php elseif ($v['verification_method'] === 'pic_only'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-2xs font-extrabold bg-teal-500/10 text-teal-400 border border-teal-500/30">
                                            👤 Bisnis Perorangan
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-2xs font-extrabold bg-slate-800 text-slate-300 border border-slate-700">
                                            🧑 Individu / Freelancer
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Business & PIC Name -->
                                <td class="py-3.5 px-4">
                                    <?php if (! empty($v['business_name'])): ?>
                                        <div class="font-bold text-slate-200"><?= esc($v['business_name']) ?></div>
                                    <?php endif; ?>
                                    <?php if (! empty($v['pic_name'])): ?>
                                        <div class="text-2xs text-slate-400">PIC: <?= esc($v['pic_name']) ?></div>
                                    <?php else: ?>
                                        <div class="text-2xs text-slate-500">-</div>
                                    <?php endif; ?>
                                </td>

                                <!-- Document Chips -->
                                <td class="py-3.5 px-4">
                                    <div class="flex flex-wrap gap-1">
                                        <?php if (! empty($v['ktp_document_path'])): ?>
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-emerald-400 border border-slate-700" title="KTP Terlampir">KTP</span>
                                        <?php endif; ?>
                                        <?php if (! empty($v['selfie_document_path'])): ?>
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-emerald-400 border border-slate-700" title="Selfie Terlampir">Selfie</span>
                                        <?php endif; ?>
                                        <?php if (! empty($v['nib_document_path'])): ?>
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-900/60 text-blue-300 border border-blue-700" title="NIB Terlampir">NIB</span>
                                        <?php endif; ?>
                                        <?php if (! empty($v['documents_deleted_at'])): ?>
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-950 text-rose-300 border border-rose-800" title="Berkas telah dibersihkan">Dibersihkan</span>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <!-- Status & Level -->
                                <td class="py-3.5 px-4">
                                    <?php
                                    $vBadge = match($v['verification_status']) {
                                        'approved' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                                        'pending'  => 'bg-amber-500/10 text-amber-400 border-amber-500/30 animate-pulse',
                                        'rejected' => 'bg-rose-500/10 text-rose-400 border-rose-500/30',
                                        'revoked'  => 'bg-slate-800 text-slate-400 border-slate-700',
                                        default    => 'bg-slate-800 text-slate-500 border-slate-700',
                                    };
                                    ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-2xs font-extrabold border <?= $vBadge ?>">
                                        <?= ucfirst($v['verification_status']) ?>
                                    </span>
                                    <?php if ($v['verification_status'] === 'approved'): ?>
                                        <div class="text-[10px] font-mono text-slate-400 mt-0.5">
                                            <?= esc($v['verification_level']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <!-- Update Timestamp -->
                                <td class="py-3.5 px-4 font-mono text-2xs text-slate-400 whitespace-nowrap">
                                    <?= date('d M Y H:i', strtotime($v['updated_at'] ?? $v['created_at'])) ?>
                                </td>

                                <!-- Action Button -->
                                <td class="py-3.5 px-4 text-right">
                                    <a href="<?= base_url('admin/identity-verifications/view/' . $v['id']) ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-brand-600 text-white font-bold text-xs transition-colors shadow-2xs">
                                        <span>Tinjau</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="p-4 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
                <span>Halaman <?= $page ?> dari <?= $totalPages ?> (Total <?= $total ?> data)</span>
                <div class="flex items-center gap-1.5">
                    <?php if ($page > 1): ?>
                        <a href="<?= base_url('admin/identity-verifications?' . http_build_query(array_merge($filters, ['page' => $page - 1]))) ?>" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-white">
                            &larr; Sebelumnya
                        </a>
                    <?php endif; ?>
                    <?php if ($page < $totalPages): ?>
                        <a href="<?= base_url('admin/identity-verifications?' . http_build_query(array_merge($filters, ['page' => $page + 1]))) ?>" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-white">
                            Berikutnya &rarr;
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

</div>
<?= $this->endSection() ?>
