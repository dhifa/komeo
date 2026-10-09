<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="bg-slate-50 min-h-screen py-12 sm:py-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 space-y-8">
        
        <!-- Header -->
        <div class="space-y-3">
            <a href="<?= base_url('member/' . esc($targetUser->username)) ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-brand-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Profil Member
            </a>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Hubungi & Ajukan Kerja Sama
            </h1>
            <p class="text-xs sm:text-sm text-slate-500">
                Kirim pesan langsung, permintaan penawaran, atau permohonan CV resmi kepada <span class="font-bold text-slate-800"><?= esc($profile->display_name ?: $profile->full_name ?: $targetUser->username) ?></span>.
            </p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-10 shadow-xs">
            <form action="<?= base_url('member/' . esc($targetUser->username) . '/hubungi') ?>" method="POST" class="space-y-6">
                <?= csrf_field() ?>

                <!-- Honeypot anti-spam hidden field -->
                <input type="text" name="website_hp" style="display:none;" tabindex="-1" autocomplete="off">

                <!-- Target Member Info Card -->
                <div class="p-4 rounded-2xl bg-indigo-50/60 border border-indigo-100 flex items-center gap-3.5">
                    <?php 
                        $avatarUrl = method_exists($profile, 'getAvatarUrl') ? $profile->getAvatarUrl() : 'https://ui-avatars.com/api/?name=M&background=4f46e5&color=fff';
                    ?>
                    <img src="<?= esc($avatarUrl) ?>" alt="Member" class="w-12 h-12 rounded-xl object-cover border border-slate-200 shrink-0">
                    <div class="min-w-0">
                        <span class="font-extrabold text-sm text-slate-900 block truncate"><?= esc($profile->display_name ?: $profile->full_name ?: $targetUser->username) ?></span>
                        <span class="text-xs text-brand-600 font-semibold block truncate"><?= esc($profile->company_name ?: ($profile->job_title ?: 'Member KOMEO')) ?></span>
                    </div>
                </div>

                <!-- Input Fields Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Client Name -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">Nama Anda / Klien <span class="text-rose-500">*</span></label>
                        <input type="text" name="client_name" value="<?= old('client_name') ?>" required
                               placeholder="Nama lengkap Anda"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    </div>

                    <!-- Client Email -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">Alamat Email Aktif <span class="text-rose-500">*</span></label>
                        <input type="email" name="client_email" value="<?= old('client_email') ?>" required
                               placeholder="nama@perusahaan.com"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    </div>

                    <!-- Client WhatsApp -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">Nomor WhatsApp</label>
                        <input type="tel" name="client_whatsapp" value="<?= old('client_whatsapp') ?>"
                               placeholder="0812xxxxxxxx"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    </div>

                    <!-- Client Organization -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">Perusahaan / Instansi</label>
                        <input type="text" name="client_organization" value="<?= old('client_organization') ?>"
                               placeholder="Contoh: PT Kreasi Bangsa"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    </div>

                    <!-- Inquiry Purpose / Type -->
                    <?php $defaultPurpose = request()->getGet('purpose') ?: old('inquiry_type', 'job_offer'); ?>
                    <div class="sm:col-span-2 space-y-1.5">
                        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">Tujuan Permintaan / Kontak <span class="text-rose-500">*</span></label>
                        <select name="inquiry_type" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm bg-white outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                            <option value="job_offer" <?= $defaultPurpose === 'job_offer' ? 'selected' : '' ?>>Penawaran Pekerjaan / Pengadaan Vendor</option>
                            <option value="cv_request" <?= $defaultPurpose === 'cv_request' ? 'selected' : '' ?>>Permintaan Curriculum Vitae (CV)</option>
                            <option value="portfolio_request" <?= $defaultPurpose === 'portfolio_request' ? 'selected' : '' ?>>Permintaan Portofolio & Rate Card</option>
                            <option value="collaboration" <?= $defaultPurpose === 'collaboration' ? 'selected' : '' ?>>Kolaborasi Proyek / Partnership</option>
                            <option value="general" <?= $defaultPurpose === 'general' ? 'selected' : '' ?>>Pertanyaan Umum</option>
                        </select>
                    </div>

                    <!-- Subject -->
                    <div class="sm:col-span-2 space-y-1.5">
                        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">Subjek Pesan <span class="text-rose-500">*</span></label>
                        <input type="text" name="subject" value="<?= old('subject') ?>" required
                               placeholder="Contoh: Penawaran Kolaborasi Acara Konser Musik Jakarta 2026"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                    </div>

                    <!-- Optional Event Details -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">Lokasi Rencana Event</label>
                        <input type="text" name="event_location" value="<?= old('event_location') ?>"
                               placeholder="Contoh: Jakarta / Bali / Surabaya"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm outline-none">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">Estimasi Tanggal Event</label>
                        <input type="date" name="event_date" value="<?= old('event_date') ?>"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm outline-none">
                    </div>

                    <!-- Message -->
                    <div class="sm:col-span-2 space-y-1.5">
                        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">Isi Pesan / Penawaran <span class="text-rose-500">*</span></label>
                        <textarea name="initial_message" rows="5" required placeholder="Jelaskan kebutuhan, ruang lingkup pekerjaan, atau detail permohonan Anda..."
                                  class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs sm:text-sm outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500"><?= old('initial_message') ?></textarea>
                    </div>
                </div>

                <!-- Privacy Consent -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-600">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="privacy_consent" value="1" required class="mt-0.5 w-4 h-4 text-brand-600 rounded">
                        <span class="leading-relaxed">
                            Saya menyetujui pesan dan informasi kontak saya diteruskan kepada member terkait melalui sistem terverifikasi KOMEO Connect, dan bersedia melakukan verifikasi email satu kali untuk keamanan komunikasi. <span class="text-rose-500">*</span>
                        </span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-brand-600 hover:bg-brand-700 transition shadow-sm flex items-center justify-center gap-2">
                        <span>Kirim Permintaan & Verifikasi</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
<?= $this->endSection() ?>
