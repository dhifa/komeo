<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="space-y-8 max-w-5xl">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 rounded-3xl p-6 sm:p-8 text-white relative overflow-hidden shadow-xs">
        <div class="max-w-2xl relative z-10 space-y-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-brand-500/20 text-brand-300 border border-brand-500/30">
                <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                KOMEO Connect
            </span>
            <h1 class="text-xl sm:text-3xl font-extrabold tracking-tight">CV & Dokumen Portofolio</h1>
            <p class="text-xs sm:text-sm text-slate-300">
                Kelola berkas CV resmi, dokumen penawaran PDF, dan tautan portofolio eksternal dengan kendali privasi penuh untuk kolaborasi bisnis.
            </p>
        </div>
    </div>

    <!-- Flash Alerts -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm flex items-center gap-2.5 shadow-xs">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            <span class="font-medium"><?= esc(session()->getFlashdata('error')) ?></span>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-center gap-2.5 shadow-xs">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span class="font-medium"><?= esc(session()->getFlashdata('success')) ?></span>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm space-y-1 shadow-xs">
            <div class="font-bold flex items-center gap-1.5">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                Periksa isian formulir:
            </div>
            <ul class="list-disc list-inside space-y-0.5 ml-1">
                <?php foreach (session()->getFlashdata('errors') as $err): ?>
                    <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Section 1: Curriculum Vitae (CV) Management -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Curriculum Vitae (CV) Resmi
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Format PDF (Maks. 5 MB). Digunakan saat klien mengajukan permintaan resmi profil Anda.</p>
            </div>
            
            <?php if ($cv): ?>
                <span class="px-3 py-1 rounded-full text-xs font-bold <?= $cv['visibility'] === 'public' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' ?>">
                    Visibilitas: <?= $cv['visibility'] === 'public' ? 'PUBLIK' : 'REQUEST AKSES' ?>
                </span>
            <?php endif; ?>
        </div>

        <?php if (! $cv): ?>
            <!-- Form Upload CV Baru -->
            <form action="<?= base_url('dashboard/dokumen/cv') ?>" method="POST" enctype="multipart/form-data" class="p-6 rounded-2xl bg-slate-50 border border-dashed border-slate-200 space-y-4">
                <?= csrf_field() ?>
                <div class="text-center max-w-md mx-auto space-y-3">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-brand-600 flex items-center justify-center mx-auto">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800">Unggah Berkas CV Anda</h3>
                    <p class="text-xs text-slate-500">Pilih berkas PDF maksimal 5 MB untuk disimpan secara aman di profil Anda.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">Pilih Berkas PDF <span class="text-rose-500">*</span></label>
                        <input type="file" name="cv_file" accept="application/pdf" required
                               class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">Privasi & Visibilitas <span class="text-rose-500">*</span></label>
                        <select name="visibility" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs bg-white outline-none">
                            <option value="public">Publik (Bisa dilihat & diunduh langsung oleh siapa saja)</option>
                            <option value="request_only">Request Akses (Tamu harus meminta izin akses terlebih dahulu)</option>
                        </select>
                    </div>
                </div>

                <div class="text-right pt-2">
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition shadow-xs">
                        Unggah CV
                    </button>
                </div>
            </form>
        <?php else: ?>
            <!-- Tampilan CV Aktif -->
            <div class="p-5 rounded-2xl bg-indigo-50/60 border border-indigo-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-brand-600 text-white flex items-center justify-center font-extrabold text-sm shadow-xs shrink-0">
                        PDF
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900"><?= esc($cv['title']) ?></h4>
                        <p class="text-xs text-slate-500">
                            Ukuran: <?= round($cv['file_size'] / (1024 * 1024), 2) ?> MB • Diunggah pada: <?= date('d M Y, H:i', strtotime($cv['created_at'])) ?> WIB
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <!-- Download -->
                    <a href="<?= base_url('dashboard/dokumen/download/' . $cv['id']) ?>" class="px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition shadow-xs flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Unduh
                    </a>

                    <!-- Ubah Visibilitas -->
                    <form action="<?= base_url('dashboard/dokumen/visibility/' . $cv['id']) ?>" method="POST" class="inline">
                        <?= csrf_field() ?>
                        <select name="visibility" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white font-bold text-slate-700 outline-none">
                            <option value="public" <?= $cv['visibility'] === 'public' ? 'selected' : '' ?>>Publik</option>
                            <option value="request_only" <?= $cv['visibility'] === 'request_only' ? 'selected' : '' ?>>Request Akses</option>
                        </select>
                    </form>

                    <!-- Hapus -->
                    <form action="<?= base_url('dashboard/dokumen/delete/' . $cv['id']) ?>" method="POST" class="inline" onsubmit="return confirm('Hapus berkas CV ini?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="p-2 rounded-xl text-rose-600 hover:bg-rose-50 transition" title="Hapus CV">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Section 2: PDF Portfolios & External Links -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-base font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Portofolio PDF & Tautan Eksternal
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Unggah portofolio deck (maks. 5 berkas PDF, 10 MB per berkas) atau sertakan link Behance, Google Drive, dsb.</p>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="document.getElementById('upload-pdf-modal').classList.remove('hidden')" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition shadow-xs flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah PDF (<?= $pdfPortfolioCount ?>/5)
                </button>
                <button type="button" onclick="document.getElementById('link-modal').classList.remove('hidden')" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    Tambah Link
                </button>
            </div>
        </div>

        <?php if (empty($portfolios)): ?>
            <div class="p-8 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-xs text-slate-400">
                Belum ada berkas portofolio PDF atau tautan eksternal yang ditambahkan.
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php foreach ($portfolios as $doc): ?>
                    <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex flex-col justify-between space-y-3 hover:border-brand-300 transition">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl <?= $doc['document_type'] === 'portfolio_external' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700' ?> flex items-center justify-center text-xs font-extrabold shrink-0">
                                    <?= $doc['document_type'] === 'portfolio_external' ? 'LINK' : 'PDF' ?>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs sm:text-sm font-bold text-slate-900 truncate"><?= esc($doc['title']) ?></h4>
                                    <span class="text-[10px] text-slate-400 block">
                                        <?= $doc['document_type'] === 'portfolio_external' ? esc($doc['external_platform']) : round($doc['file_size'] / (1024 * 1024), 2) . ' MB' ?>
                                    </span>
                                </div>
                            </div>

                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase shrink-0 <?= $doc['visibility'] === 'public' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' ?>">
                                <?= $doc['visibility'] === 'public' ? 'PUBLIK' : 'REQUEST AKSES' ?>
                            </span>
                        </div>

                        <?php if (! empty($doc['description'])): ?>
                            <p class="text-xs text-slate-500 line-clamp-2"><?= esc($doc['description']) ?></p>
                        <?php endif; ?>

                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                            <?php if ($doc['document_type'] === 'portfolio_external'): ?>
                                <a href="<?= esc($doc['external_url']) ?>" target="_blank" rel="noopener noreferrer" class="font-bold text-brand-600 hover:underline flex items-center gap-1">
                                    <span>Buka Tautan</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            <?php else: ?>
                                <a href="<?= base_url('dashboard/dokumen/download/' . $doc['id']) ?>" class="font-bold text-brand-600 hover:underline flex items-center gap-1">
                                    <span>Unduh PDF</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                </a>
                            <?php endif; ?>

                            <div class="flex items-center gap-1">
                                <form action="<?= base_url('dashboard/dokumen/visibility/' . $doc['id']) ?>" method="POST" class="inline">
                                    <?= csrf_field() ?>
                                    <select name="visibility" onchange="this.form.submit()" class="px-2 py-1 rounded-lg border border-slate-200 text-[11px] bg-white font-semibold text-slate-600 outline-none">
                                        <option value="public" <?= $doc['visibility'] === 'public' ? 'selected' : '' ?>>Publik</option>
                                        <option value="request_only" <?= $doc['visibility'] === 'request_only' ? 'selected' : '' ?>>Request Akses</option>
                                    </select>
                                </form>

                                <form action="<?= base_url('dashboard/dokumen/delete/' . $doc['id']) ?>" method="POST" class="inline" onsubmit="return confirm('Hapus portofolio ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 transition" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- Modal 1: Upload Portfolio PDF -->
<div id="upload-pdf-modal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-5 shadow-2xl">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-sm font-extrabold text-slate-900">Unggah Portofolio PDF (Maks. 10 MB)</h3>
            <button type="button" onclick="document.getElementById('upload-pdf-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="<?= base_url('dashboard/dokumen/portfolio/pdf') ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
            <?= csrf_field() ?>
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Judul Portofolio <span class="text-rose-500">*</span></label>
                <input type="text" name="title" required placeholder="Contoh: Company Profile & Project Deck 2026"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm outline-none focus:ring-2 focus:ring-brand-500/20">
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Pilih Berkas PDF <span class="text-rose-500">*</span></label>
                <input type="file" name="portfolio_file" accept="application/pdf" required
                       class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Deskripsi Singkat</label>
                <textarea name="description" rows="2" placeholder="Penjelasan isi portofolio..." class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs outline-none"></textarea>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Visibilitas <span class="text-rose-500">*</span></label>
                <select name="visibility" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs bg-white outline-none">
                    <option value="public">Publik (Bisa dilihat & diunduh langsung di profil member)</option>
                    <option value="request_only">Request Akses (Tamu harus meminta izin akses terlebih dahulu)</option>
                </select>
            </div>

            <div class="pt-3 border-t flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('upload-pdf-modal').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">Batal</button>
                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-xl shadow-xs">Unggah Portofolio</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Add External Link -->
<div id="link-modal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-5 shadow-2xl">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-sm font-extrabold text-slate-900">Tambah Tautan Portofolio Eksternal</h3>
            <button type="button" onclick="document.getElementById('link-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="<?= base_url('dashboard/dokumen/portfolio/external') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Judul Tautan <span class="text-rose-500">*</span></label>
                <input type="text" name="title" required placeholder="Contoh: Showcase Portofolio di Behance"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm outline-none focus:ring-2 focus:ring-brand-500/20">
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Tautan HTTPS <span class="text-rose-500">*</span></label>
                <input type="url" name="external_url" required placeholder="https://behance.net/username atau Google Drive..."
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm outline-none focus:ring-2 focus:ring-brand-500/20">
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Deskripsi Singkat</label>
                <textarea name="description" rows="2" placeholder="Keterangan singkat karya..." class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs outline-none"></textarea>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Visibilitas <span class="text-rose-500">*</span></label>
                <select name="visibility" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs bg-white outline-none">
                    <option value="public">Publik (Bisa dibuka langsung di profil member)</option>
                    <option value="request_only">Request Akses (Tamu harus meminta izin akses terlebih dahulu)</option>
                </select>
            </div>

            <div class="pt-3 border-t flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('link-modal').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">Batal</button>
                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-xl shadow-xs">Simpan Tautan</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
