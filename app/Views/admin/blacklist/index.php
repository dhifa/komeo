<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Manajemen Blacklist Industri Event</h2>
            <p class="text-sm text-slate-500 mt-1">Daftar peringatan entitas (vendor, EO, freelance) yang terbukti wanprestasi atau bermasalah untuk transparansi komunitas KOMEO.ID</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" onclick="openCategoryManageModal()" class="inline-flex items-center gap-2 px-3.5 py-2.5 text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition shadow-xs border border-indigo-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span>Kelola Database Kategori</span>
            </button>
            <button type="button" onclick="openBlacklistModal('manual')" class="inline-flex items-center gap-2 px-3.5 py-2.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Input Manual</span>
            </button>
            <button type="button" onclick="openBlacklistModal('member')" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                <span>Blacklist Member KOMEO</span>
            </button>
        </div>
    </div>

    <!-- Info Banner -->
    <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800 flex items-start gap-3">
        <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <div>
            <span class="font-bold">Perhatian Etika & Legalitas:</span>
            Pastikan setiap entitas yang dimasukkan ke dalam daftar blacklist telah melalui investigasi awal atau memiliki bukti konkret (surat somasi, SPK bermaterai, laporan kolektif anggota, atau bukti percakapan transaksi kerja). Fitur ini dapat digunakan untuk entitas luar (manual) maupun anggota terdaftar KOMEO.ID.
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm">Daftar Entitas Terdaftar (<?= count($items) ?> Kasus)</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-bold uppercase tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="py-3.5 px-4">Nama Pelaku / Entitas</th>
                        <th class="py-3.5 px-4">Tipe & Asal</th>
                        <th class="py-3.5 px-4">Kategori Kasus</th>
                        <th class="py-3.5 px-4">Kota / Tanggal</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Publik</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (! empty($items)): ?>
                        <?php foreach ($items as $it): ?>
                            <tr class="hover:bg-slate-50/75 transition-colors">
                                <td class="py-3.5 px-4">
                                    <div class="font-extrabold text-slate-900"><?= esc($it['name']) ?></div>
                                    <p class="text-[11px] text-slate-500 line-clamp-1 mt-0.5"><?= esc($it['description']) ?></p>
                                    <?php if (! empty($it['member_number'])): ?>
                                        <div class="mt-1 flex items-center gap-1.5">
                                            <?php if (! empty($it['membership_id'])): ?>
                                                <a href="<?= base_url('admin/members/view/' . $it['membership_id']) ?>" target="_blank" class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100 transition">
                                                    <span>Member KOMEO: <?= esc($it['member_number']) ?></span>
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                </a>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                    Member: <?= esc($it['member_number']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="flex flex-col gap-1 items-start">
                                        <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-slate-100 text-slate-700">
                                            <?= esc($it['entity_type']) ?>
                                        </span>
                                        <?php if (! empty($it['member_number'])): ?>
                                            <span class="text-[10px] font-semibold text-rose-600">Member KOMEO</span>
                                        <?php else: ?>
                                            <span class="text-[10px] text-slate-400">Non-Member / Eksternal</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-rose-700">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-rose-50 border border-rose-100 text-rose-800 text-[11px]">
                                        <?= esc($it['case_category']) ?>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    <div class="font-medium text-slate-800"><?= esc($it['city'] ?: '-') ?></div>
                                    <div class="text-[11px] text-slate-500 font-medium">
                                        <?php 
                                            $incDate = $it['incident_date'];
                                            if (!empty($incDate) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $incDate)) {
                                                echo date('d M Y', strtotime($incDate));
                                            } else {
                                                echo esc($incDate ?: '-');
                                            }
                                        ?>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <?php if ($it['status'] === 'blacklisted'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-200">
                                            Blacklist Aktif
                                        </span>
                                    <?php elseif ($it['status'] === 'monitoring'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                            Pengawasan
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            Selesai / Damai
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3.5 px-4">
                                    <?= $it['is_public'] ? '<span class="text-emerald-600 font-bold">Ya</span>' : '<span class="text-slate-400">Tidak</span>' ?>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <button type="button" 
                                                onclick='editBlacklist(<?= json_encode($it, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                                class="px-2.5 py-1 text-[11px] font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                                            Edit
                                        </button>
                                        <form action="<?= base_url('admin/blacklist/delete/' . $it['id']) ?>" method="post"
                                              data-komeo-confirm-form="Apakah Anda yakin ingin menghapus data blacklist ini?"
                                              data-komeo-confirm-title="Hapus Data Blacklist"
                                              data-komeo-confirm-type="danger">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="px-2.5 py-1 text-[11px] font-bold text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded-lg transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Belum ada data blacklist tercatat.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form Add/Edit Blacklist -->
<div id="blacklistModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 id="modalTitle" class="text-base font-extrabold text-slate-900">Tambah Entitas Blacklist</h3>
            <button type="button" onclick="closeBlacklistModal()" class="text-slate-400 hover:text-slate-600">&times;</button>
        </div>

        <!-- Mode Toggle Tabs (Manual vs Member KOMEO) -->
        <div id="modeToggleContainer" class="flex items-center p-1 bg-slate-100 rounded-xl text-xs font-bold">
            <button type="button" id="tabModeManual" onclick="switchMode('manual')" class="flex-1 py-1.5 px-3 rounded-lg text-center transition bg-white text-slate-900 shadow-xs">
                Input Manual (Eksternal)
            </button>
            <button type="button" id="tabModeMember" onclick="switchMode('member')" class="flex-1 py-1.5 px-3 rounded-lg text-center transition text-slate-600 hover:text-slate-900">
                Pilih Member KOMEO.ID
            </button>
        </div>

        <form action="<?= base_url('admin/blacklist/save') ?>" method="post" class="space-y-3.5 text-xs">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="form_id" value="">
            <input type="hidden" name="user_id" id="form_user_id" value="">
            <input type="hidden" name="membership_id" id="form_membership_id" value="">
            <input type="hidden" name="member_number" id="form_member_number" value="">

            <!-- Member Picker Field (Shown in Member Mode) -->
            <div id="memberPickerBlock" class="p-3 bg-indigo-50/70 border border-indigo-100 rounded-xl space-y-2 hidden">
                <label class="block font-bold text-indigo-900">Pilih Anggota Terdaftar KOMEO.ID <span class="text-rose-500">*</span></label>
                <select id="memberSelector" onchange="onSelectKomeoMember(this)" class="w-full px-3 py-2 rounded-xl border border-indigo-200 bg-white text-slate-800 text-xs font-semibold">
                    <option value="">-- Pilih Anggota dari Database --</option>
                    <?php if (! empty($members)): ?>
                        <?php foreach ($members as $m): ?>
                            <?php 
                                $label = esc($m['member_number'] . ' - ' . ($m['full_name'] ?: 'Tanpa Nama'));
                                if (! empty($m['business_name'])) {
                                    $label .= ' (' . esc($m['business_name']) . ')';
                                }
                            ?>
                            <option value="<?= $m['user_id'] ?>"
                                data-membership-id="<?= $m['membership_id'] ?>"
                                data-member-number="<?= esc($m['member_number']) ?>"
                                data-fullname="<?= esc($m['full_name']) ?>"
                                data-business="<?= esc($m['business_name']) ?>"
                                data-type="<?= esc($m['member_type']) ?>"
                                data-city="<?= esc($m['city'] ?? '') ?>"
                                data-category="<?= esc($m['category_name'] ?? '') ?>"
                                data-status="<?= esc($m['member_status']) ?>">
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>

                <!-- Checkbox Suspend Member -->
                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="suspend_member" id="form_suspend_member" value="1" checked class="rounded border-indigo-300 text-rose-600">
                    <label for="form_suspend_member" class="font-bold text-indigo-900 text-[11px]">
                        Otomatis tangguhkan (Suspend) keanggotaan KOMEO
                    </label>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Nama Pelaku / Vendor / EO <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="form_name" required class="w-full px-3 py-2 rounded-xl border border-slate-200">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tipe Entitas <span class="text-rose-500">*</span></label>
                    <select name="entity_type" id="form_entity_type" class="w-full px-3 py-2 rounded-xl border border-slate-200">
                        <option value="vendor">Vendor</option>
                        <option value="eo">Event Organizer (EO)</option>
                        <option value="freelance">Freelance / Kru</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Domisili Kota</label>
                    <input type="text" name="city" id="form_city" placeholder="Misal: Bandung" class="w-full px-3 py-2 rounded-xl border border-slate-200">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Dropdown Kategori Pelanggaran dari Database -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block font-bold text-slate-700">Kategori Kasus <span class="text-rose-500">*</span></label>
                        <button type="button" onclick="openCategoryManageModal()" class="text-[11px] font-bold text-brand-600 hover:text-brand-700 underline">
                            + Kelola
                        </button>
                    </div>
                    <select name="case_category" id="form_case_category" required class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white">
                        <option value="">-- Pilih Kategori Kasus --</option>
                        <?php if (! empty($caseCategories)): ?>
                            <?php foreach ($caseCategories as $cat): ?>
                                <option value="<?= esc($cat['name']) ?>"><?= esc($cat['name']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Input Tanggal Kejadian (Kalender Datepicker) -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tanggal Kejadian <span class="text-slate-400 font-normal">(Kalender)</span></label>
                    <input type="date" name="incident_date" id="form_incident_date" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Kronologi Ringkas Kasus <span class="text-rose-500">*</span></label>
                <textarea name="description" id="form_description" required rows="3" class="w-full px-3 py-2 rounded-xl border border-slate-200"></textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Catatan Bukti / Lampiran</label>
                <input type="text" name="evidence_notes" id="form_evidence_notes" placeholder="Misal: SPK bermaterai & somasi resmi" class="w-full px-3 py-2 rounded-xl border border-slate-200">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Status Kasus <span class="text-rose-500">*</span></label>
                    <select name="status" id="form_status" class="w-full px-3 py-2 rounded-xl border border-slate-200">
                        <option value="blacklisted">Blacklist Aktif</option>
                        <option value="monitoring">Dalam Pengawasan</option>
                        <option value="resolved">Selesai / Diselesaikan</option>
                    </select>
                </div>
                <div class="flex items-center gap-2 pt-6">
                    <input type="checkbox" name="is_public" id="form_is_public" value="1" checked class="rounded border-slate-300 text-rose-600">
                    <label for="form_is_public" class="font-bold text-slate-700">Tampilkan ke Publik</label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeBlacklistModal()" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">Batal</button>
                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Kelola Database Kategori Kasus Blacklist -->
<div id="categoryManageModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 max-h-[85vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Database Kategori Kasus</h3>
                <p class="text-[11px] text-slate-500">Kelola opsi pilihan kategori pelanggaran/kasus blacklist</p>
            </div>
            <button type="button" onclick="closeCategoryManageModal()" class="text-slate-400 hover:text-slate-600">&times;</button>
        </div>

        <!-- Form Tambah Kategori -->
        <form action="<?= base_url('admin/blacklist/category/save') ?>" method="post" class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl space-y-2.5 text-xs">
            <?= csrf_field() ?>
            <div class="font-bold text-slate-800 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Kategori Baru:</span>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nama Kategori <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: Penahanan Aset / Alat Musik" class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-xs">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Deskripsi Singkat (Opsional)</label>
                <input type="text" name="description" placeholder="Penjelasan ruang lingkup kasus" class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-xs">
            </div>
            <button type="submit" class="w-full py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-lg transition shadow-xs">
                + Simpan ke Database Kategori
            </button>
        </form>

        <!-- Daftar Kategori Kasus Terdaftar -->
        <div class="space-y-2 text-xs">
            <div class="font-bold text-slate-800 flex items-center justify-between">
                <span>Kategori Terdaftar:</span>
                <span class="text-[11px] text-slate-400"><?= count($caseCategories) ?> kategori</span>
            </div>
            <div class="divide-y divide-slate-100 border border-slate-200/80 rounded-xl overflow-hidden max-h-56 overflow-y-auto">
                <?php if (! empty($caseCategories)): ?>
                    <?php foreach ($caseCategories as $c): ?>
                        <div class="p-2.5 flex items-center justify-between hover:bg-slate-50 transition-colors">
                            <div class="pr-3">
                                <div class="font-bold text-slate-800"><?= esc($c['name']) ?></div>
                                <?php if (! empty($c['description'])): ?>
                                    <div class="text-[10px] text-slate-400 mt-0.5"><?= esc($c['description']) ?></div>
                                <?php endif; ?>
                            </div>
                            <form action="<?= base_url('admin/blacklist/category/delete/' . $c['id']) ?>" method="post"
                                  data-komeo-confirm-form="Apakah Anda yakin ingin menghapus kategori kasus blacklist ini?"
                                  data-komeo-confirm-title="Hapus Kategori Kasus"
                                  data-komeo-confirm-type="danger">
                                <?= csrf_field() ?>
                                <button type="submit" class="text-rose-500 hover:text-rose-700 text-[11px] font-bold p-1 hover:bg-rose-50 rounded transition" title="Hapus kategori">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="p-4 text-center text-slate-400 text-xs">Belum ada kategori kasus.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="pt-2 text-right border-t border-slate-100">
            <button type="button" onclick="closeCategoryManageModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition">Tutup</button>
        </div>
    </div>
</div>

<script>
let currentMode = 'manual';

function switchMode(mode) {
    currentMode = mode;
    const tabManual = document.getElementById('tabModeManual');
    const tabMember = document.getElementById('tabModeMember');
    const memberBlock = document.getElementById('memberPickerBlock');

    if (mode === 'member') {
        tabMember.className = 'flex-1 py-1.5 px-3 rounded-lg text-center transition bg-white text-slate-900 shadow-xs font-bold';
        tabManual.className = 'flex-1 py-1.5 px-3 rounded-lg text-center transition text-slate-600 hover:text-slate-900 font-bold';
        memberBlock.classList.remove('hidden');
    } else {
        tabManual.className = 'flex-1 py-1.5 px-3 rounded-lg text-center transition bg-white text-slate-900 shadow-xs font-bold';
        tabMember.className = 'flex-1 py-1.5 px-3 rounded-lg text-center transition text-slate-600 hover:text-slate-900 font-bold';
        memberBlock.classList.add('hidden');
        document.getElementById('memberSelector').value = '';
        document.getElementById('form_user_id').value = '';
        document.getElementById('form_membership_id').value = '';
        document.getElementById('form_member_number').value = '';
    }
}

function onSelectKomeoMember(selectEl) {
    const selectedOption = selectEl.options[selectEl.selectedIndex];
    if (!selectedOption || !selectedOption.value) return;

    const userId = selectedOption.value;
    const membershipId = selectedOption.getAttribute('data-membership-id');
    const memberNumber = selectedOption.getAttribute('data-member-number');
    const fullName = selectedOption.getAttribute('data-fullname');
    const businessName = selectedOption.getAttribute('data-business');
    const memberType = selectedOption.getAttribute('data-type');
    const city = selectedOption.getAttribute('data-city');
    const category = selectedOption.getAttribute('data-category') || '';

    document.getElementById('form_user_id').value = userId;
    document.getElementById('form_membership_id').value = membershipId;
    document.getElementById('form_member_number').value = memberNumber;

    // Set name
    let displayName = fullName;
    if (businessName) {
        displayName += ' (' + businessName + ')';
    }
    document.getElementById('form_name').value = displayName;

    // Map entity type
    if (category.toLowerCase().includes('organizer') || category.toLowerCase().includes('eo')) {
        document.getElementById('form_entity_type').value = 'eo';
    } else if (memberType === 'business') {
        document.getElementById('form_entity_type').value = 'vendor';
    } else {
        document.getElementById('form_entity_type').value = 'freelance';
    }

    if (city) {
        document.getElementById('form_city').value = city;
    }
}

function openBlacklistModal(mode = 'manual') {
    document.getElementById('form_id').value = '';
    document.getElementById('form_user_id').value = '';
    document.getElementById('form_membership_id').value = '';
    document.getElementById('form_member_number').value = '';
    document.getElementById('form_name').value = '';
    document.getElementById('form_entity_type').value = 'vendor';
    document.getElementById('form_city').value = '';
    document.getElementById('form_case_category').value = '';
    document.getElementById('form_incident_date').value = '';
    document.getElementById('form_description').value = '';
    document.getElementById('form_evidence_notes').value = '';
    document.getElementById('form_status').value = 'blacklisted';
    document.getElementById('form_is_public').checked = true;
    document.getElementById('form_suspend_member').checked = true;
    document.getElementById('modeToggleContainer').classList.remove('hidden');

    switchMode(mode);

    document.getElementById('modalTitle').textContent = mode === 'member' ? 'Blacklist Member KOMEO.ID' : 'Tambah Entitas Blacklist (Manual)';
    document.getElementById('blacklistModal').classList.remove('hidden');
}

function editBlacklist(data) {
    document.getElementById('form_id').value = data.id;
    document.getElementById('form_user_id').value = data.user_id || '';
    document.getElementById('form_membership_id').value = data.membership_id || '';
    document.getElementById('form_member_number').value = data.member_number || '';
    document.getElementById('form_name').value = data.name;
    document.getElementById('form_entity_type').value = data.entity_type;
    document.getElementById('form_city').value = data.city || '';
    
    // Select category or append if not in list
    const catSelect = document.getElementById('form_case_category');
    let found = false;
    for (let i = 0; i < catSelect.options.length; i++) {
        if (catSelect.options[i].value === data.case_category) {
            catSelect.selectedIndex = i;
            found = true;
            break;
        }
    }
    if (!found && data.case_category) {
        const opt = document.createElement('option');
        opt.value = data.case_category;
        opt.textContent = data.case_category;
        catSelect.appendChild(opt);
        catSelect.value = data.case_category;
    }

    // Date
    let d = data.incident_date || '';
    if (d && /^\d{4}-\d{2}-\d{2}$/.test(d)) {
        document.getElementById('form_incident_date').value = d;
    } else {
        document.getElementById('form_incident_date').value = '';
    }

    document.getElementById('form_description').value = data.description;
    document.getElementById('form_evidence_notes').value = data.evidence_notes || '';
    document.getElementById('form_status').value = data.status;
    document.getElementById('form_is_public').checked = (parseInt(data.is_public) === 1);
    document.getElementById('modalTitle').textContent = 'Edit Entitas Blacklist';

    if (data.member_number) {
        switchMode('member');
        const sel = document.getElementById('memberSelector');
        if (sel) {
            sel.value = data.user_id || '';
        }
    } else {
        switchMode('manual');
    }

    document.getElementById('blacklistModal').classList.remove('hidden');
}

function closeBlacklistModal() {
    document.getElementById('blacklistModal').classList.add('hidden');
}

function openCategoryManageModal() {
    document.getElementById('categoryManageModal').classList.remove('hidden');
}

function closeCategoryManageModal() {
    document.getElementById('categoryManageModal').classList.add('hidden');
}
</script>

<?= $this->endSection() ?>
