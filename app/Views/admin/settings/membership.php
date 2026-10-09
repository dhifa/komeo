<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Navigation -->
    <div class="flex items-center justify-between">
        <a href="<?= base_url('admin/settings') ?>" class="inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-500 hover:text-brand-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Pengaturan Website
        </a>
    </div>

    <!-- Main Settings Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        
        <div class="p-6 sm:p-8 border-b border-slate-100 bg-slate-50/50">
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                Pengaturan Aktivasi Keanggotaan & Kontak Admin
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Konfigurasikan alur kontak manual, instruksi verifikasi, dan kanal komunikasi WhatsApp/Email bagi pendaftar baru.
            </p>
        </div>

        <!-- Form -->
        <form action="<?= base_url('admin/settings/membership') ?>" method="post" class="p-6 sm:p-8 space-y-6">
            <?= csrf_field() ?>

            <!-- Workflow Guidance Box -->
            <div class="p-5 bg-gradient-to-r from-amber-50 via-yellow-50 to-amber-50 rounded-2xl border border-amber-200/80 text-xs text-amber-950 space-y-2">
                <div class="flex items-center gap-2 font-bold text-sm">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Alur Kerja Aktivasi Manual KOMEO.ID
                </div>
                <p class="leading-relaxed text-amber-900">
                    Sesuai ketentuan sistem KOMEO, akun member yang baru mendaftar akan berada dalam status <strong>pending</strong>. Verifikasi email terpisah dari aktivasi keanggotaan. Member diwajibkan menghubungi admin untuk verifikasi. Menekan tombol atau mengunjungi tautan WhatsApp <strong>tidak</strong> mengaktifkan akun secara otomatis. Persetujuan harus dilakukan secara sadar oleh administrator yang berwenang.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- WhatsApp Admin Number -->
                <div class="space-y-1.5">
                    <label for="activation_whatsapp" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Nomor WhatsApp Admin Aktivasi
                    </label>
                    <input type="text" 
                           id="activation_whatsapp" 
                           name="activation_whatsapp" 
                           value="<?= esc(old('activation_whatsapp', site_setting('Membership.activation_whatsapp', '081234567890'))) ?>" 
                           placeholder="081234567890 atau 6281234567890"
                           class="w-full font-mono px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-hidden transition">
                    <p class="text-[11px] text-slate-400">Tautan WhatsApp akan otomatis diarahkan ke nomor ini dengan pesan terformat.</p>
                </div>

                <!-- Activation Email -->
                <div class="space-y-1.5">
                    <label for="activation_email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Email Kontak Aktivasi
                    </label>
                    <input type="email" 
                           id="activation_email" 
                           name="activation_email" 
                           value="<?= esc(old('activation_email', site_setting('Membership.activation_email', 'admin@komeo.id'))) ?>" 
                           placeholder="admin@komeo.id"
                           class="w-full font-mono px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-hidden transition">
                    <p class="text-[11px] text-slate-400">Alamat email fallback jika member memilih menghubungi via email.</p>
                </div>

                <!-- Button Label CTA -->
                <div class="md:col-span-2 space-y-1.5">
                    <label for="activation_btn_label" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Label Tombol Ajukan Aktivasi (CTA) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           id="activation_btn_label" 
                           name="activation_btn_label" 
                           value="<?= esc(old('activation_btn_label', site_setting('Membership.activation_btn_label', 'Hubungi Admin untuk Aktivasi'))) ?>" 
                           required 
                           maxlength="100"
                           placeholder="Hubungi Admin untuk Aktivasi"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-hidden transition">
                </div>

                <!-- Activation Instructions -->
                <div class="md:col-span-2 space-y-1.5">
                    <label for="activation_instructions" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Instruksi Aktivasi Member (Tampil di Dashboard Pending) <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="activation_instructions" 
                              name="activation_instructions" 
                              rows="3" 
                              required 
                              class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-hidden transition"><?= esc(old('activation_instructions', site_setting('Membership.activation_instructions', 'Akun Anda berhasil dibuat. Untuk mengaktifkan keanggotaan KOMEO.ID dan mendapatkan KTA resmi, silakan hubungi Admin KOMEO untuk proses verifikasi dan persetujuan.'))) ?></textarea>
                    <p class="text-[11px] text-slate-400">Teks ini menjelaskan langkah yang perlu dilakukan oleh member yang berstatus pending.</p>
                </div>

                <!-- Channel Toggles -->
                <div class="md:col-span-2 space-y-3 pt-2 border-t border-slate-100">
                    <span class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Metode Kontak Aktif</span>
                    
                    <div class="flex flex-col sm:flex-row gap-4">
                        <label class="flex items-center gap-3 cursor-pointer select-none">
                            <input type="checkbox" 
                                   name="enable_whatsapp" 
                                   value="1" 
                                   <?= site_setting('Membership.enable_whatsapp', '1') === '1' ? 'checked' : '' ?> 
                                   class="w-5 h-5 rounded text-brand-600 focus:ring-brand-500 border-slate-300">
                            <span class="text-xs sm:text-sm font-bold text-slate-800">
                                Aktifkan Kontak WhatsApp (Rekomendasi)
                            </span>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer select-none">
                            <input type="checkbox" 
                                   name="enable_email" 
                                   value="1" 
                                   <?= site_setting('Membership.enable_email', '1') === '1' ? 'checked' : '' ?> 
                                   class="w-5 h-5 rounded text-brand-600 focus:ring-brand-500 border-slate-300">
                            <span class="text-xs sm:text-sm font-bold text-slate-800">
                                Aktifkan Kontak Email
                            </span>
                        </label>
                    </div>
                </div>

            </div>

            <!-- WhatsApp Template Preview Card -->
            <div class="p-5 rounded-2xl bg-slate-900 text-slate-200 text-xs space-y-2">
                <div class="flex items-center justify-between text-[11px] font-mono text-slate-400">
                    <span class="font-bold text-white uppercase tracking-wider">Format Pesan WhatsApp Otomatis:</span>
                    <span>Hanya referensi non-sensitif</span>
                </div>
                <div class="p-4 bg-slate-800/80 rounded-xl font-mono text-emerald-400 leading-relaxed whitespace-pre-wrap">Halo Admin KOMEO, saya sudah melakukan registrasi dan ingin mengajukan aktivasi keanggotaan KOMEO.ID.

Nama: {member_name}
Nomor Pengajuan: {application_reference}

Mohon informasi proses aktivasi keanggotaan. Terima kasih.</div>
                <p class="text-[11px] text-slate-400 pt-1">
                    Format pesan ini tidak memuat password, token otentikasi pribadi, atau berkas rahasia pengguna.
                </p>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs sm:text-sm font-bold shadow-md transition">
                    Simpan Pengaturan
                </button>
            </div>

        </form>

    </div>

</div>

<?= $this->endSection() ?>
