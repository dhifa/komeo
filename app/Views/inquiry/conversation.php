<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="bg-slate-50 min-h-screen py-10 sm:py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 space-y-6">
        
        <!-- Breadcrumb / Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-brand-50 text-brand-700 border border-brand-200 mb-2">
                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    Portal Percakapan Klien Terverifikasi
                </span>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                    <?= esc($inquiry['subject']) ?>
                </h1>
                <p class="text-xs text-slate-500">
                    Percakapan antara Anda (<span class="font-bold text-slate-800"><?= esc($inquiry['client_name']) ?></span>) dan <span class="font-bold text-slate-800"><?= esc($profile->display_name ?: $profile->full_name ?: $targetUser->username) ?></span>.
                </p>
            </div>

            <div>
                <span class="px-3 py-1.5 rounded-full text-xs font-extrabold uppercase <?= $inquiry['status'] === 'replied' ? 'bg-emerald-100 text-emerald-800' : ($inquiry['status'] === 'in_progress' ? 'bg-indigo-100 text-indigo-800' : ($inquiry['status'] === 'closed' ? 'bg-slate-100 text-slate-600' : 'bg-amber-100 text-amber-800')) ?>">
                    Status: <?= esc($inquiry['status']) ?>
                </span>
            </div>
        </div>

        <!-- Shared Documents Box (If Member shared documents) -->
        <?php if (! empty($shares)): ?>
            <div class="bg-indigo-50/80 rounded-3xl border border-indigo-200/80 p-6 space-y-4 shadow-xs">
                <div class="flex items-center gap-2 text-indigo-950 font-extrabold text-sm">
                    <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Dokumen Resmi Dibagikan untuk Anda
                </div>
                <p class="text-xs text-indigo-800">
                    Member telah membagikan dokumen terverifikasi khusus untuk email Anda. Klik tombol di bawah untuk mengunduh atau melihat dokumen.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <?php foreach ($shares as $sh): ?>
                        <div class="p-4 bg-white rounded-2xl border border-indigo-100 shadow-xs flex items-center justify-between gap-3 text-xs">
                            <div class="min-w-0">
                                <span class="font-extrabold text-slate-900 block truncate"><?= esc($sh['doc_title']) ?></span>
                                <span class="text-[10px] text-slate-400 block">
                                    Berlaku hingga <?= date('d/m/Y', strtotime($sh['expires_at'])) ?>
                                </span>
                            </div>
                            <a href="<?= base_url('shared-document/' . esc($sh['share_token_selector'])) ?>" target="_blank" class="px-3.5 py-1.5 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition shadow-xs shrink-0 flex items-center gap-1">
                                <span>Buka / Unduh</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Messages Thread Box -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-6">
            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3">
                Pesan & Riwayat Tanggapan
            </h3>

            <div class="space-y-4">
                <?php foreach ($messages as $msg): ?>
                    <?php $isClient = $msg['sender_type'] === 'client'; ?>
                    <div class="flex <?= $isClient ? 'justify-end' : 'justify-start' ?>">
                        <div class="max-w-xl rounded-2xl p-4 text-xs sm:text-sm <?= $isClient ? 'bg-indigo-600 text-white rounded-tr-none' : 'bg-slate-100 text-slate-900 rounded-tl-none border border-slate-200/60' ?> space-y-1 shadow-xs">
                            <div class="flex items-center justify-between gap-4 text-[10px] <?= $isClient ? 'text-indigo-200' : 'text-slate-400' ?> font-semibold">
                                <span><?= $isClient ? 'Anda' : esc($profile->display_name ?: $profile->full_name ?: $targetUser->username) . ' (Member KOMEO)' ?></span>
                                <span><?= date('d M Y, H:i', strtotime($msg['created_at'])) ?></span>
                            </div>
                            <p class="leading-relaxed whitespace-pre-line"><?= esc($msg['message']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Client Reply Box (if not closed) -->
            <?php if ($inquiry['status'] !== 'closed'): ?>
                <div class="pt-6 border-t border-slate-100">
                    <form action="<?= base_url('inquiry/conversation/' . esc($inquiry['client_access_token_selector']) . '/reply') ?>" method="POST" class="space-y-3">
                        <?= csrf_field() ?>
                        <label class="block text-xs font-bold text-slate-700">Kirim Pesan Balasan</label>
                        <textarea name="message" rows="4" required placeholder="Tuliskan respon atau pertanyaan tambahan untuk member..."
                                  class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none transition"></textarea>
                        <div class="text-right">
                            <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition shadow-xs inline-flex items-center gap-2">
                                <span>Kirim Pesan</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="p-4 rounded-2xl bg-slate-100 text-center text-xs text-slate-500 font-semibold">
                    Percakapan ini telah ditutup.
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>
<?= $this->endSection() ?>
