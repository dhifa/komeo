<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="bg-slate-50 min-h-screen py-12 sm:py-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Header & Breadcrumb -->
        <div class="space-y-3 text-center sm:text-left">
            <a href="<?= base_url('kegiatan/' . esc($event['slug'])) ?>" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-brand-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Informasi Acara
            </a>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Pendaftaran Peserta Umum
            </h1>
            <p class="text-xs sm:text-sm text-slate-500">
                Pendaftaran untuk kegiatan: <span class="font-bold text-slate-800"><?= esc($event['title']) ?></span>
            </p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-10 shadow-xs">
            <form action="<?= base_url('kegiatan/' . esc($event['slug']) . '/daftar') ?>" method="POST" class="space-y-6">
                <?= csrf_field() ?>

                <!-- Event summary banner in form -->
                <div class="p-4 rounded-2xl bg-indigo-50/60 border border-indigo-100 flex items-center justify-between text-xs">
                    <div>
                        <span class="block font-bold text-indigo-950"><?= esc($event['title']) ?></span>
                        <span class="text-indigo-700"><?= date('l, d M Y - H:i', strtotime($event['start_date'])) ?> WIB • <?= esc($event['venue_name'] ?: 'Venue') ?></span>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-indigo-200 text-indigo-900">
                        Umum
                    </span>
                </div>

                <!-- Input Fields Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Nama Lengkap (Required) -->
                    <div class="sm:col-span-2 space-y-1.5">
                        <label for="name" class="block text-xs font-bold text-slate-900 uppercase tracking-wider">
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="<?= old('name') ?>" required
                               placeholder="Nama lengkap sesuai identitas resmi"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition">
                    </div>

                    <!-- Email (Required) -->
                    <div class="space-y-1.5">
                        <label for="email" class="block text-xs font-bold text-slate-900 uppercase tracking-wider">
                            Alamat Email Aktif <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="<?= old('email') ?>" required
                               placeholder="nama@email.com"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition">
                        <p class="text-[10px] text-slate-400">Tautan verifikasi dan tiket QR akan dikirimkan ke email ini.</p>
                    </div>

                    <!-- WhatsApp (Optional) -->
                    <div class="space-y-1.5">
                        <label for="whatsapp" class="block text-xs font-bold text-slate-900 uppercase tracking-wider">
                            Nomor WhatsApp
                        </label>
                        <input type="tel" id="whatsapp" name="whatsapp" value="<?= old('whatsapp') ?>"
                               placeholder="0812xxxxxxxx"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition">
                    </div>

                    <!-- Perusahaan / Instansi (Optional) -->
                    <div class="space-y-1.5">
                        <label for="company" class="block text-xs font-bold text-slate-900 uppercase tracking-wider">
                            Perusahaan / Instansi / Usaha
                        </label>
                        <input type="text" id="company" name="company" value="<?= old('company') ?>"
                               placeholder="Contoh: PT Kreasi Event Nusantara"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition">
                    </div>

                    <!-- Jabatan (Optional) -->
                    <div class="space-y-1.5">
                        <label for="job_title" class="block text-xs font-bold text-slate-900 uppercase tracking-wider">
                            Jabatan / Profesi
                        </label>
                        <input type="text" id="job_title" name="job_title" value="<?= old('job_title') ?>"
                               placeholder="Contoh: Project Manager, Show Director"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition">
                    </div>

                    <!-- Kota Asal (Optional) -->
                    <div class="space-y-1.5">
                        <label for="city" class="block text-xs font-bold text-slate-900 uppercase tracking-wider">
                            Kota Domisili
                        </label>
                        <input type="text" id="city" name="city" value="<?= old('city') ?>"
                               placeholder="Contoh: Jakarta Selatan, Surabaya"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition">
                    </div>

                    <!-- Kategori Profesi (Optional) -->
                    <div class="space-y-1.5">
                        <label for="profession_category" class="block text-xs font-bold text-slate-900 uppercase tracking-wider">
                            Bidang Industri
                        </label>
                        <select id="profession_category" name="profession_category"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition bg-white">
                            <option value="">Pilih Bidang Industri...</option>
                            <option value="Event Organizer (EO)">Event Organizer (EO)</option>
                            <option value="Vendor Sound & Lighting">Vendor Sound & Lighting</option>
                            <option value="Vendor Multimedia / LED">Vendor Multimedia / LED</option>
                            <option value="Production & Stage">Production & Stage</option>
                            <option value="Talent / Crew / Show Management">Talent / Crew / Show Management</option>
                            <option value="Dekorasi & Desain Event">Dekorasi & Desain Event</option>
                            <option value="Catering & Hospitality">Catering & Hospitality</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                </div>

                <!-- Privacy Consent Checkbox (Required) -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="privacy_consent" value="1" required
                               class="mt-1 w-4 h-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500">
                        <span class="text-xs text-slate-600 leading-relaxed">
                            <?= esc($event['privacy_consent_text'] ?: 'Saya menyatakan data yang saya masukkan adalah benar dan menyetujui data ini digunakan oleh panitia KOMEO.ID untuk kepentingan registrasi, verifikasi kehadiran, serta informasi terkait kegiatan ini.') ?>
                            <span class="text-rose-500">*</span>
                        </span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-sm font-bold text-white bg-brand-600 hover:bg-brand-700 transition shadow-sm">
                        <span>Lanjutkan Pendaftaran & Verifikasi Email</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
<?= $this->endSection() ?>
