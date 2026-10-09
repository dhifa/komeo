<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6 max-w-5xl">

    <!-- Top Action & Title Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                Pemindai QR KTA Anggota
            </h1>
            <p class="text-xs text-slate-500">
                Verifikasi keaslian dan status keanggotaan fisik maupun digital secara langsung (Real-time).
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="<?= base_url('admin/kta') ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 shadow-xs transition">
                <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                Kelola KTA Digital
            </a>
        </div>
    </div>

    <!-- Scanner & Verification Result Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left 7 Cols: Camera Scanner & Manual Input -->
        <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-5">
            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Kamera Pemindai KTA
            </h3>

            <!-- Video Frame -->
            <div class="relative bg-slate-950 rounded-2xl overflow-hidden aspect-video flex items-center justify-center border border-slate-800">
                <div id="kta-qr-reader" class="w-full h-full"></div>
                <div id="kta-scanner-placeholder" class="text-center p-6 text-slate-400 space-y-2">
                    <svg class="w-12 h-12 mx-auto text-slate-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    <p class="text-xs">Klik "Mulai Kamera" untuk memindai kode QR pada kartu KTA.</p>
                </div>
            </div>

            <!-- Controls -->
            <div class="flex gap-3">
                <button type="button" id="start-kta-btn" onclick="startKtaScanner()" class="flex-1 py-2.5 px-4 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition shadow-xs flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Mulai Kamera
                </button>
                <button type="button" id="stop-kta-btn" onclick="stopKtaScanner()" style="display:none;" class="flex-1 py-2.5 px-4 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/></svg>
                    Hentikan Kamera
                </button>
            </div>

            <!-- Manual Search Input -->
            <div class="pt-4 border-t border-slate-100 space-y-2">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Verifikasi Manual</span>
                <div class="flex gap-2">
                    <input type="text" id="manual-kta-input" placeholder="Masukkan Nomor Anggota KOMEO atau token..." 
                           class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    <button type="button" onclick="submitManualKta()" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-900 bg-slate-100 hover:bg-slate-200 transition">
                        Verifikasi
                    </button>
                </div>
            </div>
        </div>

        <!-- Right 5 Cols: Member Verification Result & History -->
        <div class="lg:col-span-5 space-y-6">
            <!-- Result Card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-5">
                <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Hasil Verifikasi Anggota
                </h3>

                <!-- Idle State -->
                <div id="kta-result-idle" class="p-8 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-slate-400 space-y-2">
                    <p class="text-xs font-semibold text-slate-500">Menunggu pemindaian KTA...</p>
                    <p class="text-[10px]">Arahkan kamera ke QR pada kartu fisik/digital anggota.</p>
                </div>

                <!-- Verified Member Card -->
                <div id="kta-result-card" style="display:none;" class="space-y-4">
                    <!-- Status Banner -->
                    <div id="kta-status-badge" class="p-3.5 rounded-2xl text-xs font-bold flex items-center gap-2.5">
                        <span id="kta-status-title">STATUS ANGGOTA</span>
                    </div>

                    <!-- Profile Bio -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-start gap-3.5">
                        <img id="kta-avatar" src="" alt="Foto Member" class="w-14 h-14 rounded-xl object-cover border border-slate-200 shrink-0">
                        <div class="min-w-0 flex-1 space-y-1">
                            <h4 id="kta-name" class="text-sm font-extrabold text-slate-900 truncate">-</h4>
                            <span id="kta-number" class="font-mono text-xs font-bold text-brand-600 block">-</span>
                            <span id="kta-category" class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700">-</span>
                        </div>
                    </div>

                    <!-- Details Table -->
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-400 font-semibold">Perusahaan / Usaha:</span>
                            <span id="kta-company" class="font-bold text-slate-800">-</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-400 font-semibold">Jabatan:</span>
                            <span id="kta-job" class="font-bold text-slate-800">-</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-400 font-semibold">Kota Asal:</span>
                            <span id="kta-city" class="font-bold text-slate-800">-</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-400 font-semibold">Waktu Pindai:</span>
                            <span id="kta-scanned-time" class="font-bold text-slate-600">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Scan Audit History -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-4">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Riwayat Scan Terakhir</span>
                <div class="space-y-2 max-h-56 overflow-y-auto">
                    <?php if (empty($recentLogs)): ?>
                        <p class="text-xs text-slate-400 italic">Belum ada riwayat verifikasi.</p>
                    <?php else: ?>
                        <?php foreach ($recentLogs as $log): ?>
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                                <div class="min-w-0 mr-2">
                                    <span class="font-bold text-slate-900 block truncate"><?= esc($log['full_name'] ?: $log['membership_number']) ?></span>
                                    <span class="text-[10px] text-slate-400"><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?> • Oleh <?= esc($log['verified_by_username'] ?: 'Admin') ?></span>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase shrink-0 <?= $log['status'] === 'valid' ? 'bg-emerald-100 text-emerald-800' : ($log['status'] === 'suspended' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') ?>">
                                    <?= esc($log['status']) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- Include html5-qrcode -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
let ktaQrCode = null;
let ktaProcessing = false;

function startKtaScanner() {
    document.getElementById('kta-scanner-placeholder').style.display = 'none';
    document.getElementById('start-kta-btn').style.display = 'none';
    document.getElementById('stop-kta-btn').style.display = 'flex';

    ktaQrCode = new Html5Qrcode("kta-qr-reader");
    const config = { fps: 10, qrbox: { width: 250, height: 250 } };

    ktaQrCode.start(
        { facingMode: "environment" },
        config,
        (decodedText) => {
            if (!ktaProcessing) {
                ktaProcessing = true;
                verifyKtaCode(decodedText);
            }
        },
        () => {}
    ).catch(err => {
        komeoAlert('Kamera tidak dapat diakses: ' + err, 'error');
        stopKtaScanner();
    });
}

function stopKtaScanner() {
    if (ktaQrCode) {
        ktaQrCode.stop().then(() => {
            ktaQrCode.clear();
            document.getElementById('kta-scanner-placeholder').style.display = 'block';
            document.getElementById('start-kta-btn').style.display = 'flex';
            document.getElementById('stop-kta-btn').style.display = 'none';
        }).catch(err => console.error(err));
    }
}

function submitManualKta() {
    const val = document.getElementById('manual-kta-input').value.trim();
    if (!val) {
        komeoAlert('Masukkan nomor anggota atau token verifikasi.', 'warning');
        return;
    }
    verifyKtaCode(val);
}

function verifyKtaCode(identifier) {
    fetch('<?= base_url('admin/kta/scan/verify') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
        },
        body: new URLSearchParams({
            'identifier': identifier,
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        })
    })
    .then(r => r.json())
    .then(data => {
        displayKtaResult(data);
        setTimeout(() => { ktaProcessing = false; }, 2000);
    })
    .catch(err => {
        komeoAlert('Gagal melakukan verifikasi KTA: ' + err, 'error');
        ktaProcessing = false;
    });
}

function displayKtaResult(data) {
    document.getElementById('kta-result-idle').style.display = 'none';
    const card = document.getElementById('kta-result-card');
    card.style.display = 'block';

    const badge = document.getElementById('kta-status-badge');
    const title = document.getElementById('kta-status-title');

    if (data.status === 'valid') {
        badge.className = 'p-3.5 rounded-2xl text-xs font-bold flex items-center gap-2.5 bg-emerald-50 text-emerald-800 border border-emerald-200';
        title.textContent = 'ANGGOTA AKTIF & TERVERIFIKASI RESMI';
    } else if (data.status === 'suspended') {
        badge.className = 'p-3.5 rounded-2xl text-xs font-bold flex items-center gap-2.5 bg-amber-50 text-amber-800 border border-amber-200';
        title.textContent = 'KEANGGOTAAN DITANGGUHKAN (SUSPENDED)';
    } else {
        badge.className = 'p-3.5 rounded-2xl text-xs font-bold flex items-center gap-2.5 bg-rose-50 text-rose-800 border border-rose-200';
        title.textContent = 'KTA TIDAK VALID ATAU TIDAK DITEMUKAN';
    }

    const m = data.member || {};
    document.getElementById('kta-avatar').src = m.avatar_url || 'https://ui-avatars.com/api/?name=M&background=4f46e5&color=fff';
    document.getElementById('kta-name').textContent = m.name || '-';
    document.getElementById('kta-number').textContent = m.membership_number || '-';
    document.getElementById('kta-category').textContent = m.category || '-';
    document.getElementById('kta-company').textContent = m.company || '-';
    document.getElementById('kta-job').textContent = m.job_title || '-';
    document.getElementById('kta-city').textContent = m.city || '-';
    document.getElementById('kta-scanned-time').textContent = data.scanned_at || '-';
}
</script>
<?= $this->endSection() ?>
