<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Manajemen KTA Digital</h2>
            <p class="text-xs text-slate-500 mt-1">Pratinjau kartu anggota, unduh KTA individu, cek status verifikasi QR, dan kelola penerbitan identitas resmi.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="<?= site_url('admin/settings/kta') ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-colors shadow-xs">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                <span>Desain KTA</span>
            </a>

            <a href="<?= site_url('admin/kta/bulk') ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak KTA Massal</span>
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

    <!-- Filter Form Bar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="get" action="<?= site_url('admin/kta') ?>" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            
            <!-- Search -->
            <div class="sm:col-span-5 relative">
                <input type="text" name="q" value="<?= esc($search) ?>" placeholder="Cari nomor anggota, nama, instansi..." 
                       class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <!-- Status Filter -->
            <div class="sm:col-span-3">
                <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    <option value="">-- Semua Status --</option>
                    <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Aktif (Active)</option>
                    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Menunggu (Pending)</option>
                    <option value="suspended" <?= $status === 'suspended' ? 'selected' : '' ?>>Ditangguhkan (Suspended)</option>
                    <option value="expired" <?= $status === 'expired' ? 'selected' : '' ?>>Kedaluwarsa (Expired)</option>
                    <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Ditolak (Rejected)</option>
                </select>
            </div>

            <!-- Category Filter -->
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

            <!-- Buttons -->
            <div class="sm:col-span-1 flex gap-2">
                <button type="submit" class="w-full py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition-colors">
                    Filter
                </button>
            </div>

        </form>
    </div>

    <!-- Table of Members -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-bold">
                    <tr>
                        <th class="py-3.5 px-4">Member / Instansi</th>
                        <th class="py-3.5 px-4">No. Anggota</th>
                        <th class="py-3.5 px-4">Kategori Industri</th>
                        <th class="py-3.5 px-4">Status KTA</th>
                        <th class="py-3.5 px-4">QR Token</th>
                        <th class="py-3.5 px-4 text-right">Aksi & Unduhan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php if (empty($members)): ?>
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                Tidak ada data anggota yang sesuai dengan filter.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($members as $m): ?>
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <!-- Member Info -->
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl overflow-hidden bg-slate-100 shrink-0 border border-slate-200">
                                            <?php if (!empty($m['avatar'])): ?>
                                                <img src="<?= base_url(esc($m['avatar'])) ?>" alt="Avatar" class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <div class="w-full h-full flex items-center justify-center font-bold text-slate-400 text-xs">
                                                    <?= esc(substr($m['full_name'] ?? $m['username'] ?? 'M', 0, 1)) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <a href="<?= site_url('admin/members/detail/' . $m['user_id']) ?>" class="font-bold text-slate-900 hover:text-brand-600 block transition-colors">
                                                <?= esc($m['full_name'] ?? $m['username']) ?>
                                            </a>
                                            <?php if (!empty($m['company_name'])): ?>
                                                <span class="text-[11px] text-slate-500 block truncate max-w-[180px]"><?= esc($m['company_name']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>

                                <!-- Member Number -->
                                <td class="py-3.5 px-4">
                                    <?php if (!empty($m['membership_number'])): ?>
                                        <span class="font-mono font-bold text-slate-900 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200/80">
                                            <?= esc($m['membership_number']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-slate-400 italic">Belum terbit</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Category -->
                                <td class="py-3.5 px-4">
                                    <span class="text-slate-600 font-medium"><?= esc($m['category_name'] ?? 'Umum') ?></span>
                                    <?php if (!empty($m['city'])): ?>
                                        <span class="block text-[10px] text-slate-400"><?= esc($m['city']) ?></span>
                                    <?php endif; ?>
                                </td>

                                <!-- Status -->
                                <td class="py-3.5 px-4">
                                    <?php if ($m['status'] === 'active'): ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                        </span>
                                    <?php elseif ($m['status'] === 'pending'): ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Menunggu
                                        </span>
                                    <?php elseif ($m['status'] === 'suspended'): ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Ditangguhkan
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                            <?= esc(ucfirst($m['status'])) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- QR Status -->
                                <td class="py-3.5 px-4">
                                    <?php if (!empty($m['verification_token_selector'])): ?>
                                        <span class="text-[11px] text-emerald-700 font-medium flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Token Aktif
                                        </span>
                                    <?php else: ?>
                                        <span class="text-[11px] text-slate-400">Belum di-generate</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Actions -->
                                <td class="py-3.5 px-4 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <!-- Preview Button -->
                                        <button type="button" 
                                                onclick="openKtaModal(<?= $m['id'] ?>, '<?= esc(addslashes($m['full_name'] ?? $m['username'])) ?>', '<?= esc($m['membership_number']) ?>')" 
                                                class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition-colors" title="Lihat Kartu">
                                            Preview
                                        </button>

                                        <!-- Download Dropdown / Buttons -->
                                        <a href="<?= site_url('admin/kta/download/' . $m['id'] . '/front') ?>" class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Download PNG Depan">
                                            <span class="text-[10px] font-mono font-bold bg-indigo-50 text-indigo-700 px-1.5 py-0.5 rounded border border-indigo-200">PNG</span>
                                        </a>

                                        <a href="<?= site_url('admin/kta/download/' . $m['id'] . '/pdf') ?>" class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Download PDF CR80">
                                            <span class="text-[10px] font-mono font-bold bg-purple-50 text-purple-700 px-1.5 py-0.5 rounded border border-purple-200">PDF</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar -->
        <?php if ($totalPages > 1): ?>
            <div class="p-4 bg-slate-50/60 border-t border-slate-200/80 flex items-center justify-between text-xs text-slate-500">
                <div>
                    Menampilkan <?= count($members) ?> dari <?= $total ?> total anggota
                </div>
                <div class="flex items-center gap-1">
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <a href="<?= site_url('admin/kta') ?>?<?= http_build_query(array_merge(service('request')->getGet() ?? [], ['page' => $p])) ?>"
                           class="px-3 py-1.5 rounded-lg font-bold <?= $p === $currentPage ? 'bg-brand-600 text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' ?>">
                            <?= $p ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- Admin KTA Preview Modal -->
<div id="admin-kta-modal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="max-w-2xl w-full bg-slate-900 rounded-3xl p-6 shadow-2xl border border-slate-800">
        <div class="flex items-center justify-between pb-4 border-b border-slate-800">
            <div>
                <h4 id="modal-member-name" class="text-white font-extrabold text-base">Pratinjau KTA</h4>
                <p id="modal-member-no" class="text-xs text-indigo-400 font-mono"></p>
            </div>
            <button onclick="closeKtaModal()" class="text-slate-400 hover:text-white p-1 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="py-6 flex flex-col items-center">
            <img id="modal-card-img" src="" alt="KTA Preview" class="w-full max-w-[520px] rounded-2xl shadow-2xl border border-slate-700/60 object-contain aspect-[1011/638]">
            <div class="mt-4 flex gap-3">
                <button type="button" onclick="setModalSide('front')" id="btn-side-front" class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 text-white">Sisi Depan</button>
                <button type="button" onclick="setModalSide('back')" id="btn-side-back" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-800 text-slate-300 hover:text-white">Sisi Belakang</button>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
            <span>Ukuran: 85.60 × 53.98 mm (ID-1 / CR80)</span>
            <div class="flex gap-2">
                <a id="modal-dl-front" href="" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-lg transition-colors">PNG Depan</a>
                <a id="modal-dl-back" href="" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-lg transition-colors">PNG Belakang</a>
                <a id="modal-dl-pdf" href="" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg transition-colors">PDF (2 Sisi)</a>
            </div>
        </div>
    </div>
</div>

<script>
    let currentModalId = 0;

    function openKtaModal(id, name, number) {
        currentModalId = id;
        document.getElementById('modal-member-name').textContent = name;
        document.getElementById('modal-member-no').textContent = number || 'Nomor Belum Terbit';
        
        document.getElementById('modal-dl-front').href = '<?= site_url('admin/kta/download/') ?>' + id + '/front';
        document.getElementById('modal-dl-back').href = '<?= site_url('admin/kta/download/') ?>' + id + '/back';
        document.getElementById('modal-dl-pdf').href = '<?= site_url('admin/kta/download/') ?>' + id + '/pdf';

        setModalSide('front');
        document.getElementById('admin-kta-modal').classList.remove('hidden');
    }

    function closeKtaModal() {
        document.getElementById('admin-kta-modal').classList.add('hidden');
    }

    function setModalSide(side) {
        const img = document.getElementById('modal-card-img');
        const btnF = document.getElementById('btn-side-front');
        const btnB = document.getElementById('btn-side-back');

        img.src = '<?= site_url('admin/kta/preview/') ?>' + currentModalId + '/' + side;

        if (side === 'front') {
            btnF.className = 'px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 text-white';
            btnB.className = 'px-4 py-2 rounded-xl text-xs font-bold bg-slate-800 text-slate-300 hover:text-white';
        } else {
            btnB.className = 'px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 text-white';
            btnF.className = 'px-4 py-2 rounded-xl text-xs font-bold bg-slate-800 text-slate-300 hover:text-white';
        }
    }
</script>
<?= $this->endSection() ?>
