<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6 max-w-5xl">

    <!-- Top Action & Title Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="<?= base_url('admin/events') ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-brand-600 transition mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Kegiatan
            </a>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                Pemindai Presensi (Check-in): <?= esc($event['title']) ?>
            </h1>
            <p class="text-xs text-slate-500">
                Pindai kode QR Tiket Kegiatan atau KTA Digital Member untuk validasi kehadiran.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="<?= base_url('admin/events/' . $event['id'] . '/attendance') ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 shadow-xs transition">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Laporan Kehadiran
            </a>
        </div>
    </div>

    <!-- Window Alert if check-in is not open -->
    <?php if (! $isWindowOpen): ?>
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-center gap-3">
            <svg class="w-5 h-5 text-amber-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            <div>
                <span class="font-bold">Peringatan Jadwal:</span> <?= esc($windowReason) ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Scanner + Result Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left: Camera Scanner Box (7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-5">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Kamera Pemindai QR
                </h3>

                <div class="flex items-center gap-2">
                    <label class="flex items-center gap-1.5 text-xs text-slate-600 cursor-pointer">
                        <input type="checkbox" id="auto-confirm-checkin" checked class="w-4 h-4 text-brand-600 rounded">
                        <span class="font-bold">Auto Check-in</span>
                    </label>
                </div>
            </div>

            <!-- Scanner Video Container -->
            <div class="relative bg-slate-950 rounded-2xl overflow-hidden aspect-video flex items-center justify-center border border-slate-800">
                <div id="qr-reader" class="w-full h-full"></div>
                <div id="scanner-placeholder" class="text-center p-6 text-slate-400 space-y-2">
                    <svg class="w-12 h-12 mx-auto text-slate-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    <p class="text-xs">Klik "Mulai Kamera" di bawah untuk mengaktifkan pemindai.</p>
                </div>
            </div>

            <!-- Camera Controls -->
            <div class="flex flex-wrap items-center gap-3">
                <button type="button" id="start-scan-btn" onclick="startScanner()" class="flex-1 py-2.5 px-4 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition shadow-xs flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Mulai Kamera
                </button>
                <button type="button" id="stop-scan-btn" onclick="stopScanner()" style="display:none;" class="flex-1 py-2.5 px-4 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/></svg>
                    Hentikan Kamera
                </button>
            </div>

            <!-- Manual Input Form -->
            <div class="pt-4 border-t border-slate-100 space-y-3">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Pencarian / Input Manual</span>
                <div class="flex gap-2">
                    <input type="text" id="manual-identifier-input" placeholder="Masukkan No. Registrasi (EVT-...), KTA, atau scan barcode..."
                           class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    <button type="button" onclick="submitManualCheckin()" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-900 bg-slate-100 hover:bg-slate-200 transition">
                        Periksa
                    </button>
                </div>
            </div>
        </div>

        <!-- Right: Real-time Scan Result Card (5 cols) -->
        <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-5">
            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Hasil Pemindaian Terakhir
            </h3>

            <!-- State 1: Idle (Waiting for Scan) -->
            <div id="result-idle" class="p-8 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200 space-y-2">
                <div class="w-10 h-10 rounded-xl bg-slate-200 text-slate-400 flex items-center justify-center mx-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                </div>
                <p class="text-xs text-slate-500 font-semibold">Menunggu pemindaian tiket...</p>
                <p class="text-[10px] text-slate-400">Arahkan kamera ke QR tiket peserta atau masukkan nomor registrasi.</p>
            </div>

            <!-- State 2: Dynamic Result Display -->
            <div id="result-card" style="display:none;" class="space-y-4">
                <!-- Status Banner -->
                <div id="result-status-banner" class="p-4 rounded-2xl text-xs font-bold flex items-center gap-3">
                    <span id="result-status-text">Status Check-in</span>
                </div>

                <!-- Participant Detail Box -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3 text-xs">
                    <div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Nama Peserta</span>
                        <span id="res-name" class="text-sm font-extrabold text-slate-900 block">-</span>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase block">No. Registrasi</span>
                            <span id="res-reg-number" class="font-mono font-bold text-brand-600 block">-</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase block">Tipe Peserta</span>
                            <span id="res-type" class="font-bold text-slate-800 block">-</span>
                        </div>
                    </div>

                    <div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Instansi / No. Anggota</span>
                        <span id="res-identity" class="font-semibold text-slate-700 block truncate">-</span>
                    </div>
                </div>

                <!-- Action Confirmation (if not auto-confirm) -->
                <div id="manual-confirm-box" style="display:none;" class="pt-2">
                    <button type="button" onclick="confirmCheckinAction()" class="w-full py-3 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-sm">
                        Konfirmasi Kehadiran Peserta
                    </button>
                </div>
            </div>

            <!-- Recent session scans list -->
            <div class="pt-4 border-t border-slate-100 space-y-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Sesi Pemindaian Ini</span>
                <div id="session-scan-list" class="space-y-1.5 max-h-48 overflow-y-auto text-xs text-slate-600">
                    <p class="text-slate-400 text-[11px] italic">Belum ada peserta yang dipindai pada sesi ini.</p>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- Include html5-qrcode library for camera barcode & QR reading -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
let html5QrCode = null;
let currentIdentifier = '';
let isProcessing = false;

function startScanner() {
    document.getElementById('scanner-placeholder').style.display = 'none';
    document.getElementById('start-scan-btn').style.display = 'none';
    document.getElementById('stop-scan-btn').style.display = 'flex';

    html5QrCode = new Html5Qrcode("qr-reader");
    const config = { fps: 10, qrbox: { width: 250, height: 250 } };

    html5QrCode.start(
        { facingMode: "environment" },
        config,
        (decodedText, decodedResult) => {
            if (!isProcessing) {
                onQrScanned(decodedText);
            }
        },
        (errorMessage) => {
            // scan error ignored
        }
    ).catch(err => {
        komeoAlert('Kamera tidak dapat diakses: ' + err, 'error');
        stopScanner();
    });
}

function stopScanner() {
    if (html5QrCode) {
        html5QrCode.stop().then(() => {
            html5QrCode.clear();
            document.getElementById('scanner-placeholder').style.display = 'block';
            document.getElementById('start-scan-btn').style.display = 'flex';
            document.getElementById('stop-scan-btn').style.display = 'none';
        }).catch(err => console.error(err));
    }
}

function onQrScanned(qrData) {
    isProcessing = true;
    currentIdentifier = qrData;
    processCheckinPayload(qrData);
}

function submitManualCheckin() {
    const val = document.getElementById('manual-identifier-input').value.trim();
    if (!val) {
        komeoAlert('Masukkan nomor registrasi atau kode scan.', 'warning');
        return;
    }
    currentIdentifier = val;
    processCheckinPayload(val);
}

function processCheckinPayload(identifier) {
    const autoConfirm = document.getElementById('auto-confirm-checkin').checked ? '1' : '';

    fetch('<?= base_url('admin/events/' . $event['id'] . '/checkin') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
        },
        body: new URLSearchParams({
            'identifier': identifier,
            'confirm_checkin': autoConfirm,
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        })
    })
    .then(r => r.json())
    .then(data => {
        displayResult(data);
        setTimeout(() => { isProcessing = false; }, 2000); // 2 second pause before next scan
    })
    .catch(err => {
        komeoAlert('Terjadi gangguan saat validasi check-in: ' + err, 'error');
        isProcessing = false;
    });
}

function displayResult(data) {
    document.getElementById('result-idle').style.display = 'none';
    const card = document.getElementById('result-card');
    card.style.display = 'block';

    const banner = document.getElementById('result-status-banner');
    const statusText = document.getElementById('result-status-text');
    const p = data.participant || {};

    document.getElementById('res-name').textContent = p.participant_name || '-';
    document.getElementById('res-reg-number').textContent = p.registration_number || '-';
    document.getElementById('res-type').textContent = p.participant_type === 'member' ? 'Member KOMEO' : 'Peserta Umum';
    document.getElementById('res-identity').textContent = p.participant_type === 'member' ? ('No. KTA: ' + (p.membership_number || '-')) : (p.company || '-');

    if (data.status === 'success') {
        banner.className = 'p-4 rounded-2xl text-xs font-bold flex items-center gap-3 bg-emerald-50 text-emerald-800 border border-emerald-200';
        statusText.textContent = 'CHECK-IN BERHASIL: ' + data.message;
        document.getElementById('manual-confirm-box').style.display = 'none';
        addSessionLog(p.participant_name, p.registration_number, 'SUCCESS');
        playAudioNotification(true);
    } else if (data.status === 'duplicate') {
        banner.className = 'p-4 rounded-2xl text-xs font-bold flex items-center gap-3 bg-amber-50 text-amber-800 border border-amber-200';
        statusText.textContent = 'PERINGATAN DUPLIKAT: ' + data.message;
        document.getElementById('manual-confirm-box').style.display = 'none';
        addSessionLog(p.participant_name, p.registration_number, 'DUPLICATE');
        playAudioNotification(false);
    } else if (data.status === 'ready') {
        banner.className = 'p-4 rounded-2xl text-xs font-bold flex items-center gap-3 bg-indigo-50 text-indigo-800 border border-indigo-200';
        statusText.textContent = 'TIKET VALID: Menunggu konfirmasi staf.';
        document.getElementById('manual-confirm-box').style.display = 'block';
    } else {
        banner.className = 'p-4 rounded-2xl text-xs font-bold flex items-center gap-3 bg-rose-50 text-rose-800 border border-rose-200';
        statusText.textContent = 'GAGAL: ' + data.message;
        document.getElementById('manual-confirm-box').style.display = 'none';
        playAudioNotification(false);
    }
}

function confirmCheckinAction() {
    fetch('<?= base_url('admin/events/' . $event['id'] . '/checkin') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams({
            'identifier': currentIdentifier,
            'confirm_checkin': '1',
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        })
    })
    .then(r => r.json())
    .then(data => {
        displayResult(data);
    });
}

function addSessionLog(name, regNum, status) {
    const list = document.getElementById('session-scan-list');
    if (list.querySelector('p')) {
        list.innerHTML = '';
    }
    const item = document.createElement('div');
    item.className = 'flex items-center justify-between p-2 rounded-xl bg-slate-50 border border-slate-100 text-[11px]';
    item.innerHTML = `
        <span class="font-bold text-slate-800 truncate">${name || 'Peserta'} (${regNum || '-'})</span>
        <span class="font-bold px-2 py-0.5 rounded text-[10px] ${status === 'SUCCESS' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'}">${status}</span>
    `;
    list.prepend(item);
}

function playAudioNotification(success) {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        if (success) {
            osc.frequency.setValueAtTime(880, ctx.currentTime); // A5
            gain.gain.setValueAtTime(0.1, ctx.currentTime);
            osc.start();
            osc.stop(ctx.currentTime + 0.15);
        } else {
            osc.frequency.setValueAtTime(300, ctx.currentTime);
            gain.gain.setValueAtTime(0.15, ctx.currentTime);
            osc.start();
            osc.stop(ctx.currentTime + 0.3);
        }
    } catch(e) {}
}
</script>
<?= $this->endSection() ?>
