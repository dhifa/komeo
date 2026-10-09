<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="space-y-6 max-w-5xl">

    <!-- Top Action & Title Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="<?= base_url('dashboard/permintaan') ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-brand-600 transition mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Kotak Masuk
            </a>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                <?= esc($inquiry['subject']) ?>
            </h1>
            <p class="text-xs text-slate-500">
                Dari: <span class="font-bold text-slate-800"><?= esc($inquiry['client_name']) ?></span> (<?= esc($inquiry['client_email']) ?>)
            </p>
        </div>

        <div class="flex items-center gap-2">
            <!-- Bagikan Dokumen Button -->
            <button type="button" onclick="document.getElementById('share-doc-modal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                Bagikan Dokumen
            </button>

            <!-- Status Dropdown Form -->
            <form action="<?= base_url('dashboard/permintaan/' . $inquiry['id'] . '/status') ?>" method="POST" class="inline">
                <?= csrf_field() ?>
                <select name="status" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold bg-white text-slate-700 outline-none">
                    <option value="new" <?= $inquiry['status'] === 'new' ? 'selected' : '' ?>>Baru</option>
                    <option value="in_progress" <?= $inquiry['status'] === 'in_progress' ? 'selected' : '' ?>>Sedang Proses</option>
                    <option value="replied" <?= $inquiry['status'] === 'replied' ? 'selected' : '' ?>>Dibalas</option>
                    <option value="closed" <?= $inquiry['status'] === 'closed' ? 'selected' : '' ?>>Selesai / Tutup</option>
                    <option value="spam" <?= $inquiry['status'] === 'spam' ? 'selected' : '' ?>>Tandai Spam</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Client Info Summary Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-5 sm:p-6 shadow-xs">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Klien</span>
                <span class="font-bold text-slate-900 block"><?= esc($inquiry['client_name']) ?></span>
                <span class="text-[11px] text-slate-500 block truncate"><?= esc($inquiry['client_organization'] ?: '-') ?></span>
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Kontak Klien</span>
                <span class="font-semibold text-slate-800 block truncate"><?= esc($inquiry['client_email']) ?></span>
                <span class="text-[11px] text-slate-500 block"><?= esc($inquiry['client_whatsapp'] ?: '-') ?></span>
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Tipe Permintaan</span>
                <span class="font-bold text-brand-600 block uppercase"><?= esc($inquiry['inquiry_type']) ?></span>
                <span class="text-[11px] text-slate-500 block"><?= $inquiry['event_date'] ? 'Tgl Acara: ' . date('d/m/Y', strtotime($inquiry['event_date'])) : '-' ?></span>
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Lokasi Acara</span>
                <span class="font-semibold text-slate-800 block"><?= esc($inquiry['event_location'] ?: 'Belum ditentukan') ?></span>
                <span class="text-[11px] text-slate-400 block">Diterima: <?= date('d/m/Y H:i', strtotime($inquiry['created_at'])) ?></span>
            </div>
        </div>
    </div>

    <!-- Active Shared Documents in this thread -->
    <?php if (! empty($shares)): ?>
        <div class="bg-indigo-50/70 rounded-3xl border border-indigo-100 p-5 space-y-3">
            <h4 class="text-xs font-bold text-indigo-900 uppercase tracking-wider flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Dokumen yang Sedang Dibagikan ke Klien
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <?php foreach ($shares as $sh): ?>
                    <div class="p-3.5 bg-white rounded-2xl border border-indigo-200/60 shadow-xs flex items-center justify-between gap-3 text-xs">
                        <div class="min-w-0">
                            <span class="font-bold text-slate-900 block truncate"><?= esc($sh['doc_title']) ?></span>
                            <span class="text-[10px] text-slate-500 block">
                                <?= (int) $sh['is_revoked'] === 1 ? 'Akses Telah Dicabut' : 'Berlaku s/d: ' . date('d/m/Y', strtotime($sh['expires_at'])) ?> • Diakses <?= $sh['access_count'] ?> kali
                            </span>
                        </div>
                        <?php if ((int) $sh['is_revoked'] === 0): ?>
                            <form action="<?= base_url('dashboard/permintaan/' . $inquiry['id'] . '/revoke-share/' . $sh['id']) ?>" method="POST">
                                <?= csrf_field() ?>
                                <button type="submit" class="px-2.5 py-1 text-[11px] font-bold text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Cabut Akses Sekarang">
                                    Cabut Akses
                                </button>
                            </form>
                        <?php else: ?>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500">Revoked</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Conversation Messages Thread -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-6">
        <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            Riwayat Percakapan
        </h3>

        <div class="space-y-4">
            <?php foreach ($messages as $msg): ?>
                <?php $isMe = $msg['sender_type'] === 'member'; ?>
                <div class="flex <?= $isMe ? 'justify-end' : 'justify-start' ?>">
                    <div class="max-w-xl rounded-2xl p-4 text-xs sm:text-sm <?= $isMe ? 'bg-brand-600 text-white rounded-tr-none' : 'bg-slate-100 text-slate-800 rounded-tl-none' ?> space-y-1 shadow-xs">
                        <div class="flex items-center justify-between gap-4 text-[10px] <?= $isMe ? 'text-indigo-200' : 'text-slate-400' ?> font-semibold">
                            <span><?= $isMe ? 'Anda (Member KOMEO)' : esc($inquiry['client_name']) ?></span>
                            <span><?= date('d M Y, H:i', strtotime($msg['created_at'])) ?></span>
                        </div>
                        <p class="leading-relaxed whitespace-pre-line"><?= esc($msg['message']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Reply Input Form -->
        <div class="pt-6 border-t border-slate-100">
            <form action="<?= base_url('dashboard/permintaan/' . $inquiry['id'] . '/reply') ?>" method="POST" class="space-y-3">
                <?= csrf_field() ?>
                <label class="block text-xs font-bold text-slate-700">Tulis Balasan ke Klien</label>
                <textarea name="message" rows="4" required placeholder="Tuliskan respon atau penawaran kerja sama kepada klien..."
                          class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none transition"></textarea>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] text-slate-400">Klien akan menerima email notifikasi dan dapat membalas via portal tamu terverifikasi.</span>
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition shadow-xs flex items-center gap-2">
                        <span>Kirim Balasan</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<!-- Modal Bagikan Dokumen (Section 26) -->
<div id="share-doc-modal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-5 shadow-2xl">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-sm font-extrabold text-slate-900">Bagikan Dokumen Secara Aman</h3>
            <button type="button" onclick="document.getElementById('share-doc-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="<?= base_url('dashboard/permintaan/' . $inquiry['id'] . '/share-document') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>

            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Pilih Dokumen yang Dibagikan <span class="text-rose-500">*</span></label>
                <select name="document_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm bg-white outline-none">
                    <?php if (empty($myDocuments)): ?>
                        <option value="">Belum ada dokumen yang diunggah</option>
                    <?php else: ?>
                        <?php foreach ($myDocuments as $d): ?>
                            <option value="<?= $d['id'] ?>">
                                [<?= strtoupper($d['document_type']) ?>] <?= esc($d['title']) ?> (<?= esc($d['visibility']) ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Email Penerima yang Berhak</label>
                <input type="email" name="recipient_email" value="<?= esc($inquiry['client_email']) ?>" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm outline-none bg-slate-50">
                <p class="text-[10px] text-slate-400">Tautan hanya dapat dibuka oleh email klien terverifikasi ini.</p>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Masa Berlaku Akses (Hari)</label>
                <select name="expiry_days" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm bg-white outline-none">
                    <option value="3">3 Hari</option>
                    <option value="7" selected>7 Hari (Standar)</option>
                    <option value="14">14 Hari</option>
                    <option value="30">30 Hari</option>
                </select>
            </div>

            <div class="p-3 rounded-xl bg-slate-50 text-[11px] text-slate-500 leading-relaxed border border-slate-100">
                Dokumen akan dibagikan melalui token terenkripsi. Anda dapat mencabut akses dokumen sewaktu-waktu.
            </div>

            <div class="pt-3 border-t flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('share-doc-modal').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">Batal</button>
                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-xl shadow-xs">Bagikan Dokumen</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
