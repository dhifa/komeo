<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Cetak KTA Massal</h2>
            <p class="text-xs text-slate-500 mt-1">Ekspor kartu anggota dalam jumlah banyak untuk percetakan PVC ID card langsung maupun percetakan offset lembar A4/A3.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="<?= site_url('admin/kta') ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-colors shadow-xs">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Daftar KTA</span>
            </a>
            <a href="<?= site_url('admin/settings/kta') ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-colors shadow-xs">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                <span>Pengaturan Cetak</span>
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('message')): ?>
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <p class="text-xs font-bold text-emerald-900"><?= esc(session()->getFlashdata('message')) ?></p>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-center gap-3">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <p class="text-xs font-bold text-rose-900"><?= esc(session()->getFlashdata('error')) ?></p>
        </div>
    <?php endif; ?>

    <!-- Filter Bar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="get" action="<?= site_url('admin/kta/bulk') ?>" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            
            <div class="sm:col-span-4 relative">
                <input type="text" name="q" value="<?= esc($search) ?>" placeholder="Cari nama, no. anggota, kota..." 
                       class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <div class="sm:col-span-3">
                <select name="category_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    <option value="">-- Semua Kategori --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $categoryId == $cat['id'] ? 'selected' : '' ?>>
                            <?= esc($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="sm:col-span-3">
                <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Hanya Member Aktif (Rekomendasi)</option>
                    <option value="" <?= $status === '' ? 'selected' : '' ?>>Semua Status</option>
                    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Menunggu</option>
                    <option value="suspended" <?= $status === 'suspended' ? 'selected' : '' ?>>Ditangguhkan</option>
                </select>
            </div>

            <div class="sm:col-span-2">
                <button type="submit" class="w-full py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition-colors">
                    Terapkan Filter
                </button>
            </div>

        </form>
    </div>

    <!-- Bulk Form Wrapper -->
    <form id="bulk-export-form" method="post" action="<?= site_url('admin/kta/bulk/export') ?>">
        <?= csrf_field() ?>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- Member Selection Table (Left 8 cols) -->
            <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" id="check-all" class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 border-slate-300">
                            <span class="text-xs font-bold text-slate-700">Pilih Semua di Halaman</span>
                        </label>
                    </div>
                    <div class="text-xs text-slate-500">
                        Dipilih: <span id="selected-counter" class="font-extrabold text-brand-600">0</span> dari <?= count($members) ?> anggota
                    </div>
                </div>

                <div class="overflow-x-auto max-h-[560px] overflow-y-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-white sticky top-0 border-b border-slate-200 text-slate-400 uppercase tracking-wider font-bold">
                            <tr>
                                <th class="py-3 px-4 w-10"></th>
                                <th class="py-3 px-4">No. Anggota</th>
                                <th class="py-3 px-4">Nama Member / Instansi</th>
                                <th class="py-3 px-4">Kategori Industri</th>
                                <th class="py-3 px-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            <?php if (empty($members)): ?>
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-400">
                                        Tidak ada anggota yang ditemukan.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($members as $m): ?>
                                    <tr class="hover:bg-slate-50/80 transition-colors cursor-pointer" onclick="toggleMemberRow(event, <?= $m['id'] ?>)">
                                        <td class="py-3 px-4" onclick="event.stopPropagation()">
                                            <input type="checkbox" name="membership_ids[]" value="<?= $m['id'] ?>" class="member-checkbox w-4 h-4 rounded text-brand-600 focus:ring-brand-500 border-slate-300">
                                        </td>
                                        <td class="py-3 px-4 font-mono font-bold text-slate-900">
                                            <?= esc($m['membership_number'] ?: 'Belum Terbit') ?>
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="font-bold text-slate-900"><?= esc($m['full_name'] ?? $m['username']) ?></div>
                                            <?php if (!empty($m['company_name'])): ?>
                                                <div class="text-[10px] text-slate-400"><?= esc($m['company_name']) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-3 px-4 text-slate-600">
                                            <?= esc($m['category_name'] ?? 'Umum') ?>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold 
                                                <?= $m['status'] === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' ?>">
                                                <?= esc(ucfirst($m['status'])) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Export Settings Panel (Right 4 cols) -->
            <div class="lg:col-span-4 space-y-6">

                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-5">
                    <h3 class="text-base font-extrabold text-slate-900">Konfigurasi Cetak & Ekspor</h3>

                    <!-- Export Format Selection -->
                    <div class="space-y-3">
                        <label class="block text-xs font-bold text-slate-700">Pilih Format Hasil Ekspor:</label>

                        <!-- Option A: ZIP PNG -->
                        <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-200 hover:border-brand-500 cursor-pointer bg-slate-50/50">
                            <input type="radio" name="export_format" value="zip_png" checked class="mt-0.5 text-brand-600 focus:ring-brand-500">
                            <div>
                                <span class="font-bold text-xs text-slate-900 block">ZIP File PNG (300 DPI)</span>
                                <span class="text-[11px] text-slate-500 block">Berisi folder PNG depan & belakang 1011 × 638 px untuk tiap anggota.</span>
                            </div>
                        </label>

                        <!-- Option B: Multipage PDF CR80 -->
                        <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-200 hover:border-brand-500 cursor-pointer bg-slate-50/50">
                            <input type="radio" name="export_format" value="pdf_multipage" class="mt-0.5 text-brand-600 focus:ring-brand-500">
                            <div>
                                <span class="font-bold text-xs text-slate-900 block">PDF Multi-Halaman CR80</span>
                                <span class="text-[11px] text-slate-500 block">Satu PDF berukuran kartu CR80 (85.6 × 53.98 mm) urut Depan-Belakang.</span>
                            </div>
                        </label>

                        <!-- Option C: Complete Package -->
                        <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-200 hover:border-brand-500 cursor-pointer bg-slate-50/50">
                            <input type="radio" name="export_format" value="package_zip" class="mt-0.5 text-brand-600 focus:ring-brand-500">
                            <div>
                                <span class="font-bold text-xs text-slate-900 block">Paket Lengkap ZIP + Manifest CSV</span>
                                <span class="text-[11px] text-slate-500 block">PNG + PDF individu + manifest CSV verifikasi aman untuk vendor print.</span>
                            </div>
                        </label>

                        <!-- Mode B1: A4 Print Sheet -->
                        <label class="flex items-start gap-3 p-3 rounded-xl border border-indigo-200 bg-indigo-50/30 hover:border-brand-500 cursor-pointer">
                            <input type="radio" name="export_format" value="sheet_a4" class="mt-0.5 text-brand-600 focus:ring-brand-500">
                            <div>
                                <span class="font-bold text-xs text-indigo-950 block">Lembar Digital Printing A4 (10 Kartu)</span>
                                <span class="text-[11px] text-indigo-700 block">Grid 2×5 dengan halaman belakang duplex terbalik simetris & crop marks.</span>
                            </div>
                        </label>

                        <!-- Mode B2: A3 Print Sheet -->
                        <label class="flex items-start gap-3 p-3 rounded-xl border border-indigo-200 bg-indigo-50/30 hover:border-brand-500 cursor-pointer">
                            <input type="radio" name="export_format" value="sheet_a3" class="mt-0.5 text-brand-600 focus:ring-brand-500">
                            <div>
                                <span class="font-bold text-xs text-indigo-950 block">Lembar Digital Printing A3 (21 Kartu)</span>
                                <span class="text-[11px] text-indigo-700 block">Grid 3×7 untuk mesin digital offset komersial (A3+ 297 × 420 mm).</span>
                            </div>
                        </label>
                    </div>

                    <!-- Print Sheet Adjustments -->
                    <div class="pt-4 border-t border-slate-100 space-y-3">
                        <label class="block text-xs font-bold text-slate-700">Pengaturan Cetak Lanjutan:</label>

                        <!-- Bleed -->
                        <div>
                            <label class="text-[11px] text-slate-500 block mb-1">Bleed / Batas Lebih Cetak:</label>
                            <select name="bleed_mm" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700">
                                <option value="0">0 mm (Standar Direct-to-Card PVC Printer)</option>
                                <option value="3">3 mm (Standar Percetakan Lembar / Pisau Potong)</option>
                            </select>
                        </div>

                        <!-- Crop Marks -->
                        <div class="flex items-center justify-between pt-1">
                            <label for="crop_marks" class="text-xs text-slate-700 cursor-pointer">Tampilkan Crop Marks (Tanda Potong)</label>
                            <input type="checkbox" id="crop_marks" name="crop_marks" value="1" checked class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 border-slate-300">
                        </div>

                        <!-- Back Rotation -->
                        <div>
                            <label class="text-[11px] text-slate-500 block mb-1">Rotasi Sisi Belakang (Duplex):</label>
                            <select name="back_rotation" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700">
                                <option value="0">Normal (0 Derajat)</option>
                                <option value="180">Putar 180 Derajat (Untuk printer flip pendek)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button type="submit" id="submit-export-btn" disabled 
                                class="w-full py-3 px-4 rounded-xl font-extrabold text-xs text-white bg-slate-300 cursor-not-allowed shadow-sm transition-all flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span id="btn-submit-label">Pilih Anggota Terlebih Dahulu</span>
                        </button>
                        <p class="text-[10px] text-slate-400 text-center mt-2">Proses ekspor dieksekusi secara aman di server dengan kompresi streaming.</p>
                    </div>

                </div>

                <!-- Recent Batches History -->
                <?php if (!empty($recentBatches)): ?>
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                        <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Riwayat Batch Terakhir</h4>
                        <div class="space-y-2 text-xs">
                            <?php foreach ($recentBatches as $b): ?>
                                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                                    <div>
                                        <span class="font-mono font-bold text-slate-900 block"><?= esc($b['batch_code']) ?></span>
                                        <span class="text-[10px] text-slate-400 block"><?= esc($b['member_count']) ?> kartu • <?= esc(strtoupper($b['export_mode'] ?? $b['export_format'] ?? 'EXPORT')) ?></span>
                                    </div>
                                    <a href="<?= site_url('admin/kta/batch/download/' . $b['batch_code']) ?>" class="p-1.5 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

        </div>

    </form>

</div>

<script>
    const checkAll = document.getElementById('check-all');
    const memberCheckboxes = document.querySelectorAll('.member-checkbox');
    const selectedCounter = document.getElementById('selected-counter');
    const submitBtn = document.getElementById('submit-export-btn');
    const submitLabel = document.getElementById('btn-submit-label');

    function updateCounter() {
        const checkedCount = document.querySelectorAll('.member-checkbox:checked').length;
        selectedCounter.textContent = checkedCount;

        if (checkedCount > 0) {
            submitBtn.disabled = false;
            submitBtn.className = 'w-full py-3 px-4 rounded-xl font-extrabold text-xs text-white bg-brand-600 hover:bg-brand-700 shadow-sm transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-98';
            submitLabel.textContent = 'Mulai Ekspor (' + checkedCount + ' Anggota)';
        } else {
            submitBtn.disabled = true;
            submitBtn.className = 'w-full py-3 px-4 rounded-xl font-extrabold text-xs text-white bg-slate-300 cursor-not-allowed shadow-sm transition-all flex items-center justify-center gap-2';
            submitLabel.textContent = 'Pilih Anggota Terlebih Dahulu';
        }
    }

    if (checkAll) {
        checkAll.addEventListener('change', function() {
            memberCheckboxes.forEach(cb => {
                cb.checked = checkAll.checked;
            });
            updateCounter();
        });
    }

    memberCheckboxes.forEach(cb => {
        cb.addEventListener('change', () => {
            const allChecked = Array.from(memberCheckboxes).every(c => c.checked);
            if (checkAll) checkAll.checked = allChecked;
            updateCounter();
        });
    });

    function toggleMemberRow(event, id) {
        const cb = document.querySelector(`.member-checkbox[value="${id}"]`);
        if (cb) {
            cb.checked = !cb.checked;
            updateCounter();
        }
    }
</script>
<?= $this->endSection() ?>
