<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$status = is_object($membership) ? ($membership->status ?? '') : ($membership['status'] ?? '');
$memberNumber = is_object($membership) 
    ? ($membership->member_number ?? $membership->membership_number ?? '') 
    : ($membership['member_number'] ?? $membership['membership_number'] ?? '');
$membershipId = is_object($membership) ? ($membership->id ?? null) : ($membership['id'] ?? null);
$isActive = ($status === 'active');
?>
<div class="space-y-6">

    <!-- Header Section -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-3
                <?php if ($status === 'active'): ?>
                    bg-emerald-50 text-emerald-700 border border-emerald-200
                <?php elseif ($status === 'pending'): ?>
                    bg-amber-50 text-amber-700 border border-amber-200
                <?php elseif ($status === 'suspended'): ?>
                    bg-rose-50 text-rose-700 border border-rose-200
                <?php elseif ($status === 'expired'): ?>
                    bg-slate-100 text-slate-700 border border-slate-300
                <?php else: ?>
                    bg-rose-50 text-rose-700 border border-rose-200
                <?php endif; ?>">
                <span class="w-2 h-2 rounded-full 
                    <?php if ($status === 'active'): ?>bg-emerald-500 animate-pulse<?php else: ?>bg-slate-400<?php endif; ?>"></span>
                Status: <?= esc(strtoupper($status ?: 'BELUM ADA KEANGGOTAAN')) ?>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Kartu Tanda Anggota (KTA) Digital
            </h1>
            <p class="text-slate-500 text-sm mt-1 max-w-2xl">
                Identitas resmi anggota KOMEO.ID berstandar internasional ID-1 / CR80 dengan verifikasi QR Code terenkripsi.
            </p>
        </div>

        <?php if (!empty($memberNumber)): ?>
            <div class="bg-slate-50 border border-slate-200 rounded-xl px-5 py-3.5 text-right shrink-0">
                <span class="text-xs uppercase font-bold text-slate-400 block tracking-wider">No. Anggota Resmi</span>
                <span class="text-lg font-mono font-extrabold text-indigo-900 tracking-tight"><?= esc($memberNumber) ?></span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Alert / Status Notice if NOT Active -->
    <?php if (!$isActive): ?>
        <div class="p-5 rounded-2xl border 
            <?php if ($status === 'pending'): ?>
                bg-amber-50/80 border-amber-200 text-amber-900
            <?php elseif ($status === 'suspended'): ?>
                bg-rose-50/80 border-rose-200 text-rose-900
            <?php elseif ($status === 'rejected'): ?>
                bg-rose-50/80 border-rose-200 text-rose-900
            <?php elseif ($status === 'expired'): ?>
                bg-slate-100 border-slate-300 text-slate-800
            <?php else: ?>
                bg-slate-100 border-slate-300 text-slate-800
            <?php endif; ?> flex items-start gap-4">
            <div class="p-2 rounded-xl shrink-0 
                <?php if ($status === 'pending'): ?>
                    bg-amber-100 text-amber-700
                <?php else: ?>
                    bg-rose-100 text-rose-700
                <?php endif; ?>">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <h4 class="font-extrabold text-base">
                    <?php if ($status === 'pending'): ?>
                        Keanggotaan Anda masih menunggu persetujuan.
                    <?php elseif ($status === 'rejected'): ?>
                        Pengajuan keanggotaan Anda belum disetujui.
                    <?php elseif ($status === 'suspended'): ?>
                        KTA Anda saat ini tidak aktif.
                    <?php elseif ($status === 'expired'): ?>
                        Masa berlaku KTA Anda telah berakhir.
                    <?php else: ?>
                        Anda belum memiliki status keanggotaan terdaftar.
                    <?php endif; ?>
                </h4>
                <p class="text-sm mt-1 opacity-90">
                    <?php if ($status === 'pending'): ?>
                        Tim Admin KOMEO.ID sedang memverifikasi profil dan berkas keanggotaan Anda. Unduhan KTA resmi akan aktif otomatis begitu akun disetujui.
                    <?php elseif ($status === 'suspended'): ?>
                        Keanggotaan Anda dinonaktifkan sementara oleh administrator. Silakan hubungi admin KOMEO.ID untuk informasi lebih lanjut.
                    <?php else: ?>
                        Hanya anggota berstatus aktif yang berhak menggunakan dan mengunduh Kartu Tanda Anggota resmi KOMEO.ID.
                    <?php endif; ?>
                </p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main KTA Card Experience -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- Left / Center: Interactive 3D Flip Card Preview -->
        <div class="lg:col-span-7 flex flex-col items-center">
            
            <!-- Card Container with Perspective -->
            <div class="w-full max-w-[520px] aspect-[1011/638] select-none" style="perspective: 1200px;">
                <div id="kta-card" class="relative w-full h-full duration-700 transition-transform shadow-2xl rounded-2xl cursor-pointer" style="transform-style: preserve-3d;" onclick="flipKtaCard()">
                    
                    <!-- Front Side -->
                    <div class="absolute inset-0 w-full h-full rounded-2xl overflow-hidden border border-slate-700/50 bg-[#070b19] backface-hidden" style="backface-visibility: hidden; -webkit-backface-visibility: hidden;">
                        <?php if (!empty($membershipId)): ?>
                            <img src="<?= site_url('dashboard/kta/preview/front') ?>" alt="KTA KOMEO Depan" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center text-slate-400">Pratinjau KTA Tidak Tersedia</div>
                        <?php endif; ?>
                    </div>

                    <!-- Back Side -->
                    <div class="absolute inset-0 w-full h-full rounded-2xl overflow-hidden border border-slate-700/50 bg-[#070b19] backface-hidden" style="backface-visibility: hidden; -webkit-backface-visibility: hidden; transform: rotateY(180deg);">
                        <?php if (!empty($membershipId)): ?>
                            <img src="<?= site_url('dashboard/kta/preview/back') ?>" alt="KTA KOMEO Belakang" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center text-slate-400">Pratinjau KTA Tidak Tersedia</div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

            <!-- Flip Control Button -->
            <div class="mt-6 flex items-center gap-3">
                <button type="button" onclick="flipKtaCard()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 text-white hover:bg-slate-800 text-sm font-bold shadow-md hover:shadow-lg transition-all active:scale-95">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Balik Kartu (Lihat <span id="flip-label">Belakang</span>)</span>
                </button>

                <button type="button" onclick="openFullscreenPreview()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-bold shadow-xs transition-colors">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                    <span>Lihat Penuh</span>
                </button>
            </div>
            <p class="text-xs text-slate-400 mt-2 text-center">Klik kartu atau tombol di atas untuk melihat sisi depan & belakang</p>

        </div>

        <!-- Right: Actions & Download Center -->
        <div class="lg:col-span-5 space-y-6">

            <!-- Download Panel -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-5">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Unduh Kartu Tanda Anggota</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Format siap cetak berkualitas tinggi 300 DPI dan dokumen PDF standar CR80.</p>
                </div>

                <div class="space-y-3">
                    <!-- Download Front PNG -->
                    <a href="<?= $isActive ? site_url('dashboard/kta/download/front') : 'javascript:void(0)' ?>" 
                       class="w-full flex items-center justify-between px-4 py-3.5 rounded-xl border <?= $isActive ? 'border-slate-200 hover:border-indigo-500 hover:bg-indigo-50/30 text-slate-800 group transition-all shadow-xs' : 'border-slate-200 bg-slate-50 text-slate-400 cursor-not-allowed' ?>">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg <?= $isActive ? 'bg-indigo-100 text-indigo-700 group-hover:scale-105' : 'bg-slate-200 text-slate-400' ?> flex items-center justify-center font-bold text-xs transition-transform">
                                PNG
                            </div>
                            <div class="text-left">
                                <span class="font-bold text-sm block">Download PNG Depan</span>
                                <span class="text-[11px] <?= $isActive ? 'text-slate-500' : 'text-slate-400' ?>">1011 × 638 px (300 DPI)</span>
                            </div>
                        </div>
                        <svg class="w-5 h-5 <?= $isActive ? 'text-indigo-600 group-hover:translate-x-0.5' : 'text-slate-300' ?> transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </a>

                    <!-- Download Back PNG -->
                    <a href="<?= $isActive ? site_url('dashboard/kta/download/back') : 'javascript:void(0)' ?>" 
                       class="w-full flex items-center justify-between px-4 py-3.5 rounded-xl border <?= $isActive ? 'border-slate-200 hover:border-indigo-500 hover:bg-indigo-50/30 text-slate-800 group transition-all shadow-xs' : 'border-slate-200 bg-slate-50 text-slate-400 cursor-not-allowed' ?>">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg <?= $isActive ? 'bg-indigo-100 text-indigo-700 group-hover:scale-105' : 'bg-slate-200 text-slate-400' ?> flex items-center justify-center font-bold text-xs transition-transform">
                                PNG
                            </div>
                            <div class="text-left">
                                <span class="font-bold text-sm block">Download PNG Belakang</span>
                                <span class="text-[11px] <?= $isActive ? 'text-slate-500' : 'text-slate-400' ?>">1011 × 638 px (300 DPI)</span>
                            </div>
                        </div>
                        <svg class="w-5 h-5 <?= $isActive ? 'text-indigo-600 group-hover:translate-x-0.5' : 'text-slate-300' ?> transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </a>

                    <!-- Download PDF -->
                    <a href="<?= $isActive ? site_url('dashboard/kta/download/pdf') : 'javascript:void(0)' ?>" 
                       class="w-full flex items-center justify-between px-4 py-3.5 rounded-xl border <?= $isActive ? 'border-indigo-300 bg-indigo-50/50 hover:bg-indigo-100/60 text-indigo-950 group transition-all shadow-xs' : 'border-slate-200 bg-slate-50 text-slate-400 cursor-not-allowed' ?>">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg <?= $isActive ? 'bg-indigo-600 text-white shadow-xs group-hover:scale-105' : 'bg-slate-200 text-slate-400' ?> flex items-center justify-center font-bold text-xs transition-transform">
                                PDF
                            </div>
                            <div class="text-left">
                                <span class="font-bold text-sm block">Download PDF (2 Sisi)</span>
                                <span class="text-[11px] <?= $isActive ? 'text-indigo-700' : 'text-slate-400' ?>">Ukuran Standar ID-1 (85.6 × 53.98 mm)</span>
                            </div>
                        </div>
                        <svg class="w-5 h-5 <?= $isActive ? 'text-indigo-600 group-hover:translate-x-0.5' : 'text-slate-300' ?> transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </a>
                </div>

                <?php if (!$isActive): ?>
                    <p class="text-xs text-rose-600 bg-rose-50 p-2.5 rounded-lg border border-rose-100 flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Fitur unduh dinonaktifkan sementara karena status keanggotaan belum aktif.
                    </p>
                <?php endif; ?>
            </div>

            <!-- Verification Link Panel -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Link Verifikasi Resmi</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Tautan publik hasil pindai QR Code pada KTA Anda.</p>
                </div>

                <?php if (!empty($verificationUrl)): ?>
                    <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl p-2">
                        <input id="verification-input" type="text" readonly value="<?= esc($verificationUrl) ?>" class="bg-transparent text-xs text-slate-700 font-mono flex-1 px-2 focus:outline-none truncate">
                        <button type="button" onclick="copyVerificationLink()" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold shrink-0 transition-colors flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            <span id="copy-text">Salin Link</span>
                        </button>
                    </div>
                    <div class="text-right">
                        <a href="<?= esc($verificationUrl) ?>" target="_blank" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 inline-flex items-center gap-1">
                            Buka Halaman Verifikasi
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </div>
                <?php else: ?>
                    <p class="text-xs text-slate-400 italic">Token verifikasi belum diterbitkan.</p>
                <?php endif; ?>
            </div>

            <!-- Specifications Card -->
            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200/80 text-xs text-slate-600 space-y-2">
                <div class="font-bold text-slate-800">Spesifikasi Kartu Fisik:</div>
                <div class="grid grid-cols-2 gap-2 text-[11px]">
                    <div>• Standar: <strong>ISO/IEC 7810 ID-1 (CR80)</strong></div>
                    <div>• Dimensi: <strong>85.60 × 53.98 mm</strong></div>
                    <div>• Resolusi: <strong>300 DPI (1011 × 638 px)</strong></div>
                    <div>• Ketebalan PVC: <strong>0.76 mm - 0.9 mm</strong></div>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- Fullscreen Modal Preview -->
<div id="fullscreen-modal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm hidden flex items-center justify-center p-4" onclick="closeFullscreenPreview(event)">
    <div class="max-w-3xl w-full bg-slate-900 rounded-3xl p-6 shadow-2xl border border-slate-800" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between pb-4 border-b border-slate-800">
            <h4 class="text-white font-extrabold text-base">Pratinjau KTA Resolusi Tinggi</h4>
            <button onclick="closeFullscreenPreview()" class="text-slate-400 hover:text-white p-1 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="py-6 flex flex-col items-center">
            <img id="fullscreen-img" src="<?= site_url('dashboard/kta/preview/front') ?>" alt="KTA KOMEO" class="w-full max-w-[600px] rounded-2xl shadow-2xl border border-slate-700/60 object-contain">
            <div class="mt-4 flex gap-3">
                <button onclick="toggleFullscreenSide('front')" id="btn-modal-front" class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 text-white">Sisi Depan</button>
                <button onclick="toggleFullscreenSide('back')" id="btn-modal-back" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-800 text-slate-300 hover:text-white">Sisi Belakang</button>
            </div>
        </div>
    </div>
</div>

<script>
    let isCardFlipped = false;

    function flipKtaCard() {
        const card = document.getElementById('kta-card');
        const label = document.getElementById('flip-label');
        isCardFlipped = !isCardFlipped;

        if (isCardFlipped) {
            card.style.transform = 'rotateY(180deg)';
            label.textContent = 'Depan';
        } else {
            card.style.transform = 'rotateY(0deg)';
            label.textContent = 'Belakang';
        }
    }

    function openFullscreenPreview() {
        document.getElementById('fullscreen-modal').classList.remove('hidden');
    }

    function closeFullscreenPreview() {
        document.getElementById('fullscreen-modal').classList.add('hidden');
    }

    function toggleFullscreenSide(side) {
        const img = document.getElementById('fullscreen-img');
        const btnFront = document.getElementById('btn-modal-front');
        const btnBack = document.getElementById('btn-modal-back');

        if (side === 'front') {
            img.src = '<?= site_url('dashboard/kta/preview/front') ?>';
            btnFront.className = 'px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 text-white';
            btnBack.className = 'px-4 py-2 rounded-xl text-xs font-bold bg-slate-800 text-slate-300 hover:text-white';
        } else {
            img.src = '<?= site_url('dashboard/kta/preview/back') ?>';
            btnBack.className = 'px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 text-white';
            btnFront.className = 'px-4 py-2 rounded-xl text-xs font-bold bg-slate-800 text-slate-300 hover:text-white';
        }
    }

    function copyVerificationLink() {
        const input = document.getElementById('verification-input');
        if (!input) return;

        navigator.clipboard.writeText(input.value).then(() => {
            const btnText = document.getElementById('copy-text');
            btnText.textContent = 'Tersalin!';
            setTimeout(() => {
                btnText.textContent = 'Salin Link';
            }, 2000);
        }).catch(err => {
            input.select();
            document.execCommand('copy');
            window.KomeoModal.toast('Link verifikasi berhasil disalin ke clipboard!', 'success');
        });
    }
</script>
<?= $this->endSection() ?>
