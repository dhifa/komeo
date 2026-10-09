<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6 max-w-5xl">

    <!-- Top Action & Title Bar -->
    <div class="flex items-center justify-between">
        <div>
            <a href="<?= base_url('admin/events') ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-brand-600 transition mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Daftar Kegiatan
            </a>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                <?= $event ? 'Edit Kegiatan: ' . esc($event['title']) : 'Tambah Kegiatan Baru' ?>
            </h1>
        </div>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-10 shadow-xs">
        <form action="<?= $event ? base_url('admin/events/' . $event['id'] . '/edit') : base_url('admin/events/create') ?>" 
              method="POST" enctype="multipart/form-data" class="space-y-8">
            <?= csrf_field() ?>

            <!-- Section 1: Informasi Utama -->
            <div class="space-y-4">
                <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">
                    1. Informasi Dasar Acara
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2 space-y-1.5">
                        <label class="block text-xs font-bold text-slate-800">Judul Kegiatan <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" value="<?= old('title', $event['title'] ?? '') ?>" required
                               placeholder="Contoh: KOMEO Annual Convention & Expo 2026"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-800">Tipe Partisipasi Acara <span class="text-rose-500">*</span></label>
                        <select name="event_type" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm bg-white focus:ring-2 focus:ring-brand-500/20 outline-none">
                            <option value="internal" <?= old('event_type', $event['event_type'] ?? '') === 'internal' ? 'selected' : '' ?>>Type A — Internal (Khusus Member KOMEO)</option>
                            <option value="external" <?= old('event_type', $event['event_type'] ?? '') === 'external' ? 'selected' : '' ?>>Type B — External (Peserta Umum)</option>
                            <option value="hybrid" <?= old('event_type', $event['event_type'] ?? 'hybrid') === 'hybrid' ? 'selected' : '' ?>>Type C — Hybrid (Member & Peserta Umum)</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-800">Status Publikasi <span class="text-rose-500">*</span></label>
                        <select name="status" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm bg-white focus:ring-2 focus:ring-brand-500/20 outline-none">
                            <option value="draft" <?= old('status', $event['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft (Draf - Belum Tayang)</option>
                            <option value="published" <?= old('status', $event['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published (Publikasikan)</option>
                            <option value="cancelled" <?= old('status', $event['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled (Dibatalkan)</option>
                            <option value="completed" <?= old('status', $event['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed (Selesai)</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2 space-y-1.5">
                        <label class="block text-xs font-bold text-slate-800">Deskripsi Lengkap Acara</label>
                        <textarea name="description" rows="5" placeholder="Tuliskan latar belakang, agenda, pembicara, dan syarat keikutsertaan..."
                                  class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none"><?= old('description', $event['description'] ?? '') ?></textarea>
                    </div>

                    <!-- Banner Upload -->
                    <div class="sm:col-span-2 space-y-2">
                        <label class="block text-xs font-bold text-slate-800">Banner / Gambar Acara (JPG, PNG, WebP)</label>
                        <input type="file" name="banner" accept="image/png,image/jpeg,image/webp"
                               class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                        <?php if (! empty($event['banner_path']) && file_exists(FCPATH . $event['banner_path'])): ?>
                            <div class="mt-2 flex items-center gap-3">
                                <img src="<?= base_url(esc($event['banner_path'])) ?>" alt="Banner Saat Ini" class="w-24 h-14 rounded-lg object-cover border">
                                <span class="text-xs text-slate-500">Banner saat ini aktif</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Section 2: Jadwal & Lokasi -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">
                    2. Jadwal & Lokasi Pelaksanaan
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-800">Waktu Mulai Acara <span class="text-rose-500">*</span></label>
                        <input type="datetime-local" name="start_date" 
                               value="<?= old('start_date', ! empty($event['start_date']) ? date('Y-m-d\TH:i', strtotime($event['start_date'])) : '') ?>" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-brand-500/20 outline-none">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-800">Waktu Selesai Acara <span class="text-rose-500">*</span></label>
                        <input type="datetime-local" name="end_date" 
                               value="<?= old('end_date', ! empty($event['end_date']) ? date('Y-m-d\TH:i', strtotime($event['end_date'])) : '') ?>" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-brand-500/20 outline-none">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-800">Nama Venue / Tempat</label>
                        <input type="text" name="venue_name" value="<?= old('venue_name', $event['venue_name'] ?? '') ?>"
                               placeholder="Contoh: Jakarta International Expo (JIExpo)"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm outline-none">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-800">Kota / Wilayah</label>
                        <input type="text" name="city" value="<?= old('city', $event['city'] ?? '') ?>"
                               placeholder="Contoh: Jakarta Pusat"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm outline-none">
                    </div>

                    <div class="sm:col-span-2 space-y-1.5">
                        <label class="block text-xs font-bold text-slate-800">Alamat Lengkap</label>
                        <input type="text" name="address" value="<?= old('address', $event['address'] ?? '') ?>"
                               placeholder="Alamat detail lokasi acara..."
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm outline-none">
                    </div>
                </div>
            </div>

            <!-- Section 3: Pengaturan Pendaftaran & Kuota -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">
                    3. Pengaturan Pendaftaran, Kuota & Presensi
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-800">Total Kuota Peserta (0 = Tak Terbatas)</label>
                        <input type="number" name="total_quota" min="0" value="<?= old('total_quota', $event['total_quota'] ?? 0) ?>"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm outline-none">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-800">Kuota Khusus Member (Hybrid)</label>
                        <input type="number" name="member_quota" min="0" value="<?= old('member_quota', $event['member_quota'] ?? 0) ?>"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm outline-none">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-800">Kuota Peserta Umum (Hybrid)</label>
                        <input type="number" name="external_quota" min="0" value="<?= old('external_quota', $event['external_quota'] ?? 0) ?>"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm outline-none">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-800">Mulai Pendaftaran</label>
                        <input type="datetime-local" name="reg_start_date" 
                               value="<?= old('reg_start_date', ! empty($event['reg_start_date']) ? date('Y-m-d\TH:i', strtotime($event['reg_start_date'])) : '') ?>"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs outline-none">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-800">Batas Akhir Pendaftaran</label>
                        <input type="datetime-local" name="reg_end_date" 
                               value="<?= old('reg_end_date', ! empty($event['reg_end_date']) ? date('Y-m-d\TH:i', strtotime($event['reg_end_date'])) : '') ?>"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs outline-none">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-800">Batas Check-in Dibuka</label>
                        <input type="datetime-local" name="checkin_start_date" 
                               value="<?= old('checkin_start_date', ! empty($event['checkin_start_date']) ? date('Y-m-d\TH:i', strtotime($event['checkin_start_date'])) : '') ?>"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs outline-none">
                    </div>
                </div>

                <!-- Toggles Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-3">
                    <label class="flex items-center gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer">
                        <input type="checkbox" name="is_registration_open" value="1" <?= old('is_registration_open', $event['is_registration_open'] ?? 1) ? 'checked' : '' ?>
                               class="w-4 h-4 text-brand-600 rounded">
                        <span class="text-xs font-bold text-slate-800">Buka Pendaftaran</span>
                    </label>

                    <label class="flex items-center gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer">
                        <input type="checkbox" name="requires_approval" value="1" <?= old('requires_approval', $event['requires_approval'] ?? 0) ? 'checked' : '' ?>
                               class="w-4 h-4 text-brand-600 rounded">
                        <span class="text-xs font-bold text-slate-800">Wajib Approval Panitia</span>
                    </label>

                    <label class="flex items-center gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer">
                        <input type="checkbox" name="allow_kta_checkin" value="1" <?= old('allow_kta_checkin', $event['allow_kta_checkin'] ?? 1) ? 'checked' : '' ?>
                               class="w-4 h-4 text-brand-600 rounded">
                        <span class="text-xs font-bold text-slate-800">Izinkan Scan via KTA</span>
                    </label>
                </div>
            </div>

            <!-- Section 4: Penugasan Staf Event (Event Staff Assignments) -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">
                    4. Penugasan Staf Presensi (Event Staff)
                </h3>
                <p class="text-xs text-slate-500">Pilih akun staf yang ditugaskan untuk melakukan scanning tiket dan presensi pada kegiatan ini.</p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 max-h-48 overflow-y-auto p-3 bg-slate-50 rounded-2xl border border-slate-200">
                    <?php foreach ($allStaff as $staffUser): ?>
                        <label class="flex items-center gap-2.5 p-2 rounded-xl bg-white border border-slate-100 cursor-pointer hover:border-brand-200">
                            <input type="checkbox" name="staff_ids[]" value="<?= $staffUser->id ?>" 
                                   <?= in_array($staffUser->id, $assignedStaffIds ?? []) ? 'checked' : '' ?>
                                   class="w-4 h-4 text-brand-600 rounded">
                            <div class="truncate text-xs">
                                <span class="font-bold text-slate-900 block truncate"><?= esc($staffUser->username) ?></span>
                                <span class="text-[10px] text-slate-400 truncate"><?= esc($staffUser->email) ?></span>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="<?= base_url('admin/events') ?>" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition shadow-sm">
                    <?= $event ? 'Simpan Perubahan Kegiatan' : 'Buat Kegiatan' ?>
                </button>
            </div>
        </form>
    </div>

</div>
<?= $this->endSection() ?>
