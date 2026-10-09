<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-8 max-w-6xl">

    <!-- Top Action & Title Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                KOMEO Connect & Dokumen Profesional
            </h1>
            <p class="text-xs text-slate-500">
                Pengawasan kontak bisnis langsung klien ke member, moderasi laporan, dan statistik penyimpanan dokumen.
            </p>
        </div>
    </div>

    <!-- Analytics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs space-y-1">
            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Total Permintaan</span>
            <div class="text-2xl font-extrabold text-slate-900"><?= $stats['total_inquiries'] ?></div>
            <span class="text-[11px] text-brand-600 font-bold block">
                <?= $stats['new_inquiries'] ?> permintaan baru
            </span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs space-y-1">
            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Pesan Dibalas</span>
            <div class="text-2xl font-extrabold text-emerald-600"><?= $stats['replied'] ?></div>
            <span class="text-[11px] text-slate-400 font-semibold block">
                <?= $stats['closed'] ?> ditutup / selesai
            </span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs space-y-1">
            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Dokumen Tersimpan</span>
            <div class="text-2xl font-extrabold text-indigo-600"><?= $stats['total_documents'] ?></div>
            <span class="text-[11px] text-slate-500 font-semibold block">
                <?= $stats['active_shares'] ?> tautan berbagi aktif
            </span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs space-y-1">
            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Penyimpanan Terpakai</span>
            <div class="text-2xl font-extrabold text-slate-800"><?= $stats['storage_used'] ?></div>
            <span class="text-[11px] text-slate-400 font-semibold block">
                <?= $stats['storage_files'] ?> berkas PDF fisik
            </span>
        </div>
    </div>

    <!-- Grid 2 Cols: Settings & Moderation -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left 7 Cols: Recent Inquiries Table & Moderation -->
        <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-5">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    Permintaan Klien Terbaru
                </h3>
                <span class="text-xs text-slate-400 font-semibold"><?= count($recentInquiries) ?> data</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="py-3 px-3 font-bold">Klien & Member</th>
                            <th class="py-3 px-3 font-bold">Subjek / Tipe</th>
                            <th class="py-3 px-3 font-bold">Status</th>
                            <th class="py-3 px-3 font-bold text-right">Moderasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($recentInquiries)): ?>
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400">
                                    Belum ada permintaan bisnis yang tercatat.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentInquiries as $inq): ?>
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3.5 px-3">
                                        <div class="font-bold text-slate-900"><?= esc($inq['client_name']) ?></div>
                                        <div class="text-[10px] text-slate-400 truncate max-w-[150px]">
                                            Kepada: @<?= esc($inq['member_username']) ?>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3">
                                        <div class="font-semibold text-slate-800 line-clamp-1"><?= esc($inq['subject']) ?></div>
                                        <span class="text-[10px] text-brand-600 uppercase font-bold"><?= esc($inq['inquiry_type']) ?></span>
                                    </td>
                                    <td class="py-3.5 px-3 whitespace-nowrap">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase <?= $inq['status'] === 'new' ? 'bg-indigo-100 text-indigo-800' : ($inq['status'] === 'spam' ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-700') ?>">
                                            <?= esc($inq['status']) ?>
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-3 text-right whitespace-nowrap">
                                        <form action="<?= base_url('admin/connect/inquiry/' . $inq['id'] . '/status') ?>" method="POST" class="inline">
                                            <?= csrf_field() ?>
                                            <?php if ($inq['status'] !== 'spam'): ?>
                                                <input type="hidden" name="status" value="spam">
                                                <button type="submit" class="p-1 rounded text-rose-500 hover:bg-rose-50" title="Tandai Spam">
                                                    Spam
                                                </button>
                                            <?php else: ?>
                                                <input type="hidden" name="status" value="new">
                                                <button type="submit" class="p-1 rounded text-emerald-600 hover:bg-emerald-50" title="Pulihkan">
                                                    Pulihkan
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right 5 Cols: KOMEO Connect Settings -->
        <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-6">
            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
                <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Pengaturan Sistem Dokumen
            </h3>

            <form action="<?= base_url('admin/connect/settings') ?>" method="POST" class="space-y-4 text-xs">
                <?= csrf_field() ?>

                <div class="space-y-1.5">
                    <label class="block font-bold text-slate-800">Ukuran Maksimal CV (MB)</label>
                    <input type="number" name="cv_max_mb" min="1" max="50" value="<?= esc($settings['cv_max_mb']) ?>"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-brand-500/20">
                    <p class="text-[10px] text-slate-400">Standar rekomendasi: 5 MB</p>
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-slate-800">Ukuran Maksimal Portofolio PDF (MB)</label>
                    <input type="number" name="portfolio_max_mb" min="1" max="100" value="<?= esc($settings['portfolio_max_mb']) ?>"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-brand-500/20">
                    <p class="text-[10px] text-slate-400">Standar rekomendasi: 10 MB</p>
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-slate-800">Batas Jumlah Portofolio per Member</label>
                    <input type="number" name="portfolio_limit" min="1" max="20" value="<?= esc($settings['portfolio_limit']) ?>"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-brand-500/20">
                    <p class="text-[10px] text-slate-400">Standar: 5 berkas</p>
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-slate-800">Default Masa Kedaluwarsa Tautan Berbagi (Hari)</label>
                    <input type="number" name="default_expiry_days" min="1" max="60" value="<?= esc($settings['default_expiry']) ?>"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-brand-500/20">
                    <p class="text-[10px] text-slate-400">Standar: 7 hari</p>
                </div>

                <div class="pt-2">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="enable_inquiries" value="1" <?= (int) $settings['enable_inquiries'] === 1 ? 'checked' : '' ?>
                               class="w-4 h-4 text-brand-600 rounded">
                        <span class="font-bold text-slate-800">Aktifkan Fitur Kontak & Permintaan Klien Global</span>
                    </label>
                </div>

                <div class="pt-4 border-t border-slate-100 text-right">
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition shadow-xs">
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>
<?= $this->endSection() ?>
