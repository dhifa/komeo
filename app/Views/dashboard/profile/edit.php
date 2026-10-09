<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<div class="max-w-5xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="<?= base_url('dashboard/profil') ?>" class="text-xs font-semibold text-brand-600 hover:text-brand-700 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Kembali ke Profil
                </a>
                <span class="text-slate-300">•</span>
                <span class="text-xs font-semibold text-slate-500">Kelola Akun</span>
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Edit Profil Member</h2>
            <p class="text-xs text-slate-500 mt-1">Perbarui informasi profil profesional Anda untuk jaringan industri event KOMEO.ID</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="<?= base_url('member/' . esc($profile->username)) ?>" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Halaman Publik
            </a>
            <button type="submit" form="profileEditForm" class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-xl transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Perubahan
            </button>
        </div>
    </div>

    <!-- Alert Notifications -->
    <?php if (session()->getFlashdata('message') || session()->getFlashdata('success')): ?>
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-start gap-3">
            <div class="p-1.5 bg-emerald-100 text-emerald-700 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-emerald-900"><?= esc(session()->getFlashdata('message') ?? session()->getFlashdata('success')) ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="p-4 bg-red-50 border border-red-200 rounded-2xl flex items-start gap-3">
            <div class="p-1.5 bg-red-100 text-red-700 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-red-900"><?= esc(session()->getFlashdata('error')) ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($errors = session()->getFlashdata('errors')): ?>
        <div class="p-4 bg-red-50 border border-red-200 rounded-2xl space-y-1">
            <div class="flex items-center gap-2 text-sm font-bold text-red-900 mb-2">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Periksa kembali isian formulir:
            </div>
            <ul class="list-disc list-inside text-xs text-red-700 space-y-1">
                <?php foreach ($errors as $field => $errorMsg): ?>
                    <li><?= esc($errorMsg) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Navigation Tabs / Section Anchors -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-3 overflow-x-auto text-xs font-bold">
        <a href="#section-pribadi" class="px-3.5 py-2 rounded-xl bg-brand-50 text-brand-700 hover:bg-brand-100 transition whitespace-nowrap">
            1. Informasi Pribadi
        </a>
        <a href="#section-profesional" class="px-3.5 py-2 rounded-xl text-slate-600 hover:bg-slate-100 transition whitespace-nowrap">
            2. Informasi Profesional
        </a>
        <a href="#section-sosial" class="px-3.5 py-2 rounded-xl text-slate-600 hover:bg-slate-100 transition whitespace-nowrap">
            3. Sosial Media & Kontak
        </a>
        <a href="#section-privasi" class="px-3.5 py-2 rounded-xl text-slate-600 hover:bg-slate-100 transition whitespace-nowrap">
            4. Privasi Profil
        </a>
    </div>

    <!-- Main Edit Form -->
    <form id="profileEditForm" action="<?= base_url('dashboard/profil/update') ?>" method="post" enctype="multipart/form-data" class="space-y-8">
        <?= csrf_field() ?>

        <!-- SECTION 1: INFORMASI PRIBADI -->
        <div id="section-pribadi" class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-brand-600 text-white text-xs font-bold flex items-center justify-center">1</span>
                    <h3 class="text-lg font-extrabold text-slate-900 tracking-tight">Informasi Pribadi</h3>
                </div>
                <p class="text-xs text-slate-500 mt-1">Identitas utama Anda sebagai anggota terdaftar di ekosistem KOMEO.ID</p>
            </div>

            <!-- Tipe Member Radio Switcher -->
            <div>
                <label class="block text-xs font-bold text-slate-800 mb-2">Tipe Keanggotaan <span class="text-red-500">*</span></label>
                <?php $currentType = old('member_type', $profile->member_type ?? 'individual'); ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label class="relative flex items-center gap-3 p-4 rounded-2xl border-2 cursor-pointer transition <?= $currentType === 'individual' ? 'border-brand-600 bg-brand-50/40' : 'border-slate-200 hover:border-slate-300' ?>">
                        <input type="radio" name="member_type" value="individual" class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-slate-300 member-type-radio" <?= $currentType === 'individual' ? 'checked' : '' ?>>
                        <div>
                            <span class="block text-xs font-bold text-slate-900">Individu / Freelancer</span>
                            <span class="block text-[11px] text-slate-500 mt-0.5">Untuk profesional mandiri, kru, teknisi, talent, atau pekerja lepas event.</span>
                        </div>
                    </label>

                    <label class="relative flex items-center gap-3 p-4 rounded-2xl border-2 cursor-pointer transition <?= $currentType === 'business' ? 'border-brand-600 bg-brand-50/40' : 'border-slate-200 hover:border-slate-300' ?>">
                        <input type="radio" name="member_type" value="business" class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-slate-300 member-type-radio" <?= $currentType === 'business' ? 'checked' : '' ?>>
                        <div>
                            <span class="block text-xs font-bold text-slate-900">Perusahaan / Bisnis</span>
                            <span class="block text-[11px] text-slate-500 mt-0.5">Untuk Event Organizer, Vendor Rental, Agensi, Studio, atau Badan Usaha.</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Foto Profil Upload with live preview -->
            <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200/70">
                <label class="block text-xs font-bold text-slate-800 mb-2">Foto Profil <span class="text-slate-400 font-normal">(Maks. 2MB - JPG, PNG, WEBP)</span></label>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
                    <div class="relative group shrink-0">
                        <img id="avatarPreview" 
                             src="<?= esc($profile->getAvatarUrl()) ?>" 
                             alt="Avatar Preview" 
                             class="w-24 h-24 rounded-2xl object-cover ring-4 ring-white shadow-md bg-slate-200">
                    </div>
                    <div class="flex-1 space-y-2">
                        <input type="file" 
                               name="photo" 
                               id="photoInput" 
                               accept="image/png,image/jpeg,image/webp" 
                               class="block w-full text-xs text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-600 file:text-white hover:file:bg-brand-700 cursor-pointer">
                        <p class="text-[11px] text-slate-500">Gunakan foto wajah yang jelas atau identitas profesional berasio 1:1 (persegi).</p>
                    </div>
                </div>
            </div>

            <!-- Nama Lengkap & Nama Tampilan -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="full_name" class="block text-xs font-bold text-slate-800 mb-1.5">
                        <span id="label_full_name"><?= $currentType === 'business' ? 'Nama Lengkap Penanggung Jawab' : 'Nama Lengkap' ?></span> <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="full_name" 
                           name="full_name" 
                           value="<?= esc(old('full_name', $profile->full_name)) ?>" 
                           required 
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-xs text-slate-900 transition" 
                           placeholder="Contoh: Rian Hendrawan">
                    <p class="text-[11px] text-slate-400 mt-1">Nama lengkap resmi sesuai identitas.</p>
                </div>

                <div>
                    <label for="display_name" class="block text-xs font-bold text-slate-800 mb-1.5">
                        Nama Tampilan / Panggilan
                    </label>
                    <input type="text" 
                           id="display_name" 
                           name="display_name" 
                           value="<?= esc(old('display_name', $profile->display_name)) ?>" 
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-xs text-slate-900 transition" 
                           placeholder="Contoh: Rian Sound / Rian">
                    <p class="text-[11px] text-slate-400 mt-1">Nama yang ditampilkan pada kartu profil publik.</p>
                </div>
            </div>

            <!-- Username & Kategori Utama -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="username" class="block text-xs font-bold text-slate-800 mb-1.5">
                        Username Publik <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-bold text-slate-400 select-none">
                            komeo.id/member/
                        </span>
                        <input type="text" 
                               id="username" 
                               name="username" 
                               value="<?= esc(old('username', $profile->username)) ?>" 
                               required 
                               pattern="^[a-zA-Z0-9_\-]+$" 
                               class="w-full pl-36 pr-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-xs font-mono text-slate-900 transition" 
                               placeholder="username">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Hanya huruf, angka, tanda hubung (-) dan garis bawah (_). Minimal 3 karakter.</p>
                </div>

                <div>
                    <label for="category_id" class="block text-xs font-bold text-slate-800 mb-1.5">
                        Kategori Industri Utama <span class="text-brand-600">*</span>
                    </label>
                    <select id="category_id" 
                            name="category_id" 
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-xs text-slate-900 transition">
                        <option value="">-- Pilih Kategori Utama --</option>
                        <?php foreach ($categories as $cat): ?>
                            <?php $cat = (object) $cat; ?>
                            <option value="<?= $cat->id ?>" <?= (string) old('category_id', $profile->category_id) === (string) $cat->id ? 'selected' : '' ?>>
                                <?= esc($cat->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1">Kategori bidang utama pekerjaan atau layanan event Anda.</p>
                </div>
            </div>

            <!-- Pilihan Spesialisasi Keahlian (Multi-select Max 5) -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold text-slate-800">
                        Spesialisasi Profesional <span class="text-slate-400 font-normal">(Maksimal 5 Pilihan)</span>
                    </label>
                    <span id="specCounterBadge" class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700">
                        <span id="specCount">0</span> / 5 Dipilih
                    </span>
                </div>
                
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2.5 max-h-60 overflow-y-auto p-3 bg-slate-50/70 border border-slate-200 rounded-2xl">
                    <?php 
                    $selectedSpecs = old('specializations', $selectedSpecIds ?? []);
                    if (! is_array($selectedSpecs)) {
                        $selectedSpecs = [];
                    }
                    ?>
                    <?php foreach ($specializations as $spec): ?>
                        <?php $spec = (object) $spec; ?>
                        <?php $isChecked = in_array((int) $spec->id, array_map('intval', $selectedSpecs), true); ?>
                        <label class="spec-label flex items-center gap-2 p-2 rounded-xl border text-xs cursor-pointer transition select-none <?= $isChecked ? 'bg-brand-50 border-brand-500 text-brand-900 font-bold' : 'bg-white border-slate-200 text-slate-700 hover:border-slate-300' ?>">
                            <input type="checkbox" 
                                   name="specializations[]" 
                                   value="<?= $spec->id ?>" 
                                   class="spec-checkbox h-3.5 w-3.5 text-brand-600 rounded focus:ring-brand-500 border-slate-300" 
                                   <?= $isChecked ? 'checked' : '' ?>>
                            <span class="truncate"><?= esc($spec->name) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <p id="specWarning" class="text-[11px] text-amber-600 font-semibold mt-1 hidden">
                    Maksimal 5 spesialisasi telah tercapai. Hapus salah satu pilihan jika ingin memilih lainnya.
                </p>
            </div>

            <!-- Kota & Provinsi -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="city" class="block text-xs font-bold text-slate-800 mb-1.5">Kota / Kabupaten</label>
                    <input type="text" 
                           id="city" 
                           name="city" 
                           value="<?= esc(old('city', $profile->city)) ?>" 
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-xs text-slate-900 transition" 
                           placeholder="Contoh: Jakarta Selatan, Surabaya, Bali">
                </div>

                <div>
                    <label for="province" class="block text-xs font-bold text-slate-800 mb-1.5">Provinsi</label>
                    <input type="text" 
                           id="province" 
                           name="province" 
                           value="<?= esc(old('province', $profile->province)) ?>" 
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-xs text-slate-900 transition" 
                           placeholder="Contoh: DKI Jakarta, Jawa Timur, Bali">
                </div>
            </div>

            <!-- Biografi -->
            <div>
                <label for="bio" class="block text-xs font-bold text-slate-800 mb-1.5">Biografi Singkat</label>
                <textarea id="bio" 
                          name="bio" 
                          rows="3" 
                          class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-xs text-slate-900 transition" 
                          placeholder="Ceritakan latar belakang profesional Anda, pengalaman di industri event, atau visi karya Anda..."><?= esc(old('bio', $profile->bio)) ?></textarea>
                <p class="text-[11px] text-slate-400 mt-1">Biografi akan tampil di bagian ringkasan profil publik.</p>
            </div>
        </div>

        <!-- SECTION 2: INFORMASI PROFESIONAL & BISNIS -->
        <div id="section-profesional" class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-brand-600 text-white text-xs font-bold flex items-center justify-center">2</span>
                    <h3 class="text-lg font-extrabold text-slate-900 tracking-tight">Informasi Profesional & Bisnis</h3>
                </div>
                <p class="text-xs text-slate-500 mt-1">Detail pengalaman dan informasi badan usaha Anda</p>
            </div>

            <!-- Conditional Business Container -->
            <div id="businessFieldsContainer" class="<?= $currentType === 'business' ? '' : 'hidden' ?> p-5 bg-indigo-50/50 rounded-2xl border border-indigo-100 space-y-5">
                <div class="flex items-center gap-2 text-indigo-900 font-bold text-xs">
                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Informasi Entitas Bisnis / Perusahaan
                </div>

                <!-- Nama Perusahaan -->
                <div>
                    <label for="business_name" class="block text-xs font-bold text-slate-800 mb-1.5">
                        Nama Perusahaan / Usaha / Agensi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="business_name" 
                           name="business_name" 
                           value="<?= esc(old('business_name', $profile->business_name)) ?>" 
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-xs text-slate-900 bg-white transition" 
                           placeholder="Contoh: PT Audio Visual Nusantara / Kreatif Production">
                </div>

                <!-- Deskripsi Bisnis -->
                <div>
                    <label for="business_description" class="block text-xs font-bold text-slate-800 mb-1.5">
                        Deskripsi Layanan & Profil Perusahaan
                    </label>
                    <textarea id="business_description" 
                              name="business_description" 
                              rows="3" 
                              class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-xs text-slate-900 bg-white transition" 
                              placeholder="Deskripsikan layanan, fasilitas rental, armada, atau kapasitas produksi bisnis Anda..."><?= esc(old('business_description', $profile->business_description)) ?></textarea>
                </div>

                <!-- Logo Perusahaan Upload -->
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-2">Logo Perusahaan <span class="text-slate-400 font-normal">(Maks. 2MB - JPG, PNG, WEBP)</span></label>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <div class="relative shrink-0">
                            <?php $logoUrl = $profile->getCompanyLogoUrl(); ?>
                            <img id="logoPreview" 
                                 src="<?= $logoUrl ? esc($logoUrl) : 'data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'80\' height=\'80\' fill=\'none\' stroke=\'%2394a3b8\' stroke-width=\'1.5\' viewBox=\'0 0 24 24\'><rect width=\'20\' height=\'20\' x=\'2\' y=\'2\' rx=\'5\'/><path d=\'M9 10h.01M15 10h.01M9.5 15a3.5 3.5 0 005 0\'/></svg>' ?>" 
                                 alt="Logo Preview" 
                                 class="w-20 h-20 rounded-2xl object-contain p-1.5 bg-white ring-2 ring-slate-200 shadow-sm">
                        </div>
                        <div class="flex-1 space-y-1.5">
                            <input type="file" 
                                   name="company_logo" 
                                   id="logoInput" 
                                   accept="image/png,image/jpeg,image/webp" 
                                   class="block w-full text-xs text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer">
                            <p class="text-[11px] text-slate-500">Logo perusahaan akan ditampilkan bersama foto profil pada kartu publik.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pengalaman & Website Portofolio -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="years_of_experience" class="block text-xs font-bold text-slate-800 mb-1.5">
                        Pengalaman Kerja di Industri Event (Tahun)
                    </label>
                    <input type="number" 
                           id="years_of_experience" 
                           name="years_of_experience" 
                           min="0" 
                           max="60" 
                           value="<?= esc(old('years_of_experience', $profile->years_of_experience)) ?>" 
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-xs text-slate-900 transition" 
                           placeholder="Contoh: 5">
                </div>

                <div>
                    <label for="website" class="block text-xs font-bold text-slate-800 mb-1.5">
                        Website Portofolio / Perusahaan
                    </label>
                    <input type="url" 
                           id="website" 
                           name="website" 
                           value="<?= esc(old('website', $profile->website)) ?>" 
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-xs text-slate-900 transition" 
                           placeholder="https://portofolio-anda.com">
                    <p class="text-[11px] text-slate-400 mt-1">Sertakan http:// atau https://</p>
                </div>
            </div>
        </div>

        <!-- SECTION 3: SOSIAL MEDIA & KONTAK -->
        <div id="section-sosial" class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-brand-600 text-white text-xs font-bold flex items-center justify-center">3</span>
                    <h3 class="text-lg font-extrabold text-slate-900 tracking-tight">Sosial Media & Kontak Bisnis</h3>
                </div>
                <p class="text-xs text-slate-500 mt-1">Koneksikan saluran komunikasi untuk memudahkan klien dan rekan kolaborator menghubungi Anda</p>
            </div>

            <!-- WhatsApp Bisnis -->
            <div>
                <label for="whatsapp" class="block text-xs font-bold text-slate-800 mb-1.5">
                    Nomor WhatsApp Bisnis
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-600 font-bold text-xs">
                        WA
                    </div>
                    <input type="text" 
                           id="whatsapp" 
                           name="whatsapp" 
                           value="<?= esc(old('whatsapp', $profile->whatsapp)) ?>" 
                           class="w-full pl-12 pr-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-xs text-slate-900 transition" 
                           placeholder="Contoh: 08123456789 atau 628123456789">
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Digunakan untuk tombol chat WhatsApp langsung pada halaman profil publik (bisa diatur privasinya).</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Instagram -->
                <div>
                    <label for="instagram" class="block text-xs font-bold text-slate-800 mb-1.5">Instagram (Username)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-bold text-slate-400 select-none">@</span>
                        <input type="text" 
                               id="instagram" 
                               name="instagram" 
                               value="<?= esc(old('instagram', $profile->instagram)) ?>" 
                               class="w-full pl-8 pr-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-xs text-slate-900 transition" 
                               placeholder="username_instagram">
                    </div>
                </div>

                <!-- TikTok -->
                <div>
                    <label for="tiktok" class="block text-xs font-bold text-slate-800 mb-1.5">TikTok (Username)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-bold text-slate-400 select-none">@</span>
                        <input type="text" 
                               id="tiktok" 
                               name="tiktok" 
                               value="<?= esc(old('tiktok', $profile->tiktok)) ?>" 
                               class="w-full pl-8 pr-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-xs text-slate-900 transition" 
                               placeholder="username_tiktok">
                    </div>
                </div>

                <!-- LinkedIn URL -->
                <div>
                    <label for="linkedin" class="block text-xs font-bold text-slate-800 mb-1.5">LinkedIn (URL Lengkap)</label>
                    <input type="url" 
                           id="linkedin" 
                           name="linkedin" 
                           value="<?= esc(old('linkedin', $profile->linkedin)) ?>" 
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-xs text-slate-900 transition" 
                           placeholder="https://linkedin.com/in/username">
                </div>

                <!-- YouTube URL -->
                <div>
                    <label for="youtube" class="block text-xs font-bold text-slate-800 mb-1.5">YouTube Channel (URL Lengkap)</label>
                    <input type="url" 
                           id="youtube" 
                           name="youtube" 
                           value="<?= esc(old('youtube', $profile->youtube)) ?>" 
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-xs text-slate-900 transition" 
                           placeholder="https://youtube.com/@channel">
                </div>
            </div>
        </div>

        <!-- SECTION 4: PENGATURAN PRIVASI PROFIL -->
        <div id="section-privasi" class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-brand-600 text-white text-xs font-bold flex items-center justify-center">4</span>
                    <h3 class="text-lg font-extrabold text-slate-900 tracking-tight">Privasi & Visibilitas Publik</h3>
                </div>
                <p class="text-xs text-slate-500 mt-1">Kontrol data apa saja yang dapat dilihat oleh publik di halaman <span class="font-mono text-brand-600">komeo.id/member/<?= esc($profile->username) ?></span></p>
            </div>

            <div class="space-y-4">
                <!-- Toggle Public Profile -->
                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-200">
                    <div>
                        <span class="block text-xs font-bold text-slate-900">Aktifkan Profil Publik</span>
                        <span class="block text-[11px] text-slate-500 mt-0.5">Jika dinonaktifkan, halaman publik Anda tidak dapat diakses dan tidak muncul di direktori member.</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_public" value="0">
                        <input type="checkbox" name="is_public" value="1" class="sr-only peer" <?= (string) old('is_public', (string)$profile->is_public) === '1' ? 'checked' : '' ?>>
                        <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-600"></div>
                    </label>
                </div>

                <!-- Toggle Show WhatsApp -->
                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-200">
                    <div>
                        <span class="block text-xs font-bold text-slate-900">Tampilkan Tombol WhatsApp</span>
                        <span class="block text-[11px] text-slate-500 mt-0.5">Izinkan pengunjung halaman profil untuk menghubungi nomor WhatsApp Anda secara langsung.</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="show_whatsapp" value="0">
                        <input type="checkbox" name="show_whatsapp" value="1" class="sr-only peer" <?= (string) old('show_whatsapp', (string)($profile->show_whatsapp ?? 1)) === '1' ? 'checked' : '' ?>>
                        <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                    </label>
                </div>

                <!-- Toggle Show Social Media -->
                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-200">
                    <div>
                        <span class="block text-xs font-bold text-slate-900">Tampilkan Tautan Sosial Media</span>
                        <span class="block text-[11px] text-slate-500 mt-0.5">Tampilkan ikon dan tautan Instagram, TikTok, LinkedIn, dan YouTube pada profil publik.</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="show_social" value="0">
                        <input type="checkbox" name="show_social" value="1" class="sr-only peer" <?= (string) old('show_social', (string)($profile->show_social ?? 1)) === '1' ? 'checked' : '' ?>>
                        <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-600"></div>
                    </label>
                </div>

                <!-- Toggle Show City & Province -->
                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-200">
                    <div>
                        <span class="block text-xs font-bold text-slate-900">Tampilkan Lokasi (Kota & Provinsi)</span>
                        <span class="block text-[11px] text-slate-500 mt-0.5">Izinkan pengunjung melihat domisili kota dan provinsi operasional Anda.</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="show_location" value="0">
                        <input type="checkbox" name="show_location" value="1" class="sr-only peer" <?= (string) old('show_location', (string)($profile->show_location ?? 1)) === '1' ? 'checked' : '' ?>>
                        <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-600"></div>
                    </label>
                </div>

                <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-[11px] text-amber-800 flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span><strong>Keamanan Privasi:</strong> Alamat email akun Anda <strong>tidak akan pernah dipublikasikan</strong> secara umum di halaman profil.</span>
                </div>
            </div>
        </div>

        <!-- Submit Button Bottom -->
        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="<?= base_url('dashboard/profil') ?>" class="px-5 py-3 text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl transition">
                Batal
            </a>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-xl transition shadow-md shadow-brand-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Semua Perubahan
            </button>
        </div>
    </form>
</div>

<!-- Dynamic Script for Type Switching, Image Previews, and Specialization Limit -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Member Type Switcher
    const radios = document.querySelectorAll('.member-type-radio');
    const businessContainer = document.getElementById('businessFieldsContainer');
    const labelFullName = document.getElementById('label_full_name');
    const businessNameInput = document.getElementById('business_name');

    radios.forEach(radio => {
        radio.addEventListener('change', function () {
            if (this.value === 'business') {
                businessContainer.classList.remove('hidden');
                labelFullName.textContent = 'Nama Lengkap Penanggung Jawab';
                businessNameInput.setAttribute('required', 'required');
            } else {
                businessContainer.classList.add('hidden');
                labelFullName.textContent = 'Nama Lengkap';
                businessNameInput.removeAttribute('required');
            }
        });
    });

    // 2. Avatar Preview
    const photoInput = document.getElementById('photoInput');
    const avatarPreview = document.getElementById('avatarPreview');
    if (photoInput) {
        photoInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                if (file.size > 2097152) {
                    window.KomeoModal.alert({
                        title: 'Ukuran Foto Terlalu Besar',
                        message: 'Ukuran foto profil melebihi batas maksimal 2MB! Silakan pilih foto dengan resolusi/ukuran lebih kecil.',
                        type: 'warning'
                    });
                    this.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = function (e) {
                    avatarPreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // 3. Logo Preview
    const logoInput = document.getElementById('logoInput');
    const logoPreview = document.getElementById('logoPreview');
    if (logoInput) {
        logoInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                if (file.size > 2097152) {
                    window.KomeoModal.alert({
                        title: 'Ukuran Logo Terlalu Besar',
                        message: 'Ukuran logo perusahaan melebihi batas maksimal 2MB! Silakan pilih berkas logo yang lebih kecil.',
                        type: 'warning'
                    });
                    this.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = function (e) {
                    logoPreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // 4. Specializations Counter & Limit (Max 5)
    const specCheckboxes = document.querySelectorAll('.spec-checkbox');
    const specCount = document.getElementById('specCount');
    const specWarning = document.getElementById('specWarning');
    const maxSpecs = 5;

    function updateSpecCount() {
        const checked = document.querySelectorAll('.spec-checkbox:checked');
        const count = checked.length;
        specCount.textContent = count;

        if (count >= maxSpecs) {
            specWarning.classList.remove('hidden');
            specCheckboxes.forEach(cb => {
                if (!cb.checked) {
                    cb.disabled = true;
                    cb.closest('label').classList.add('opacity-50', 'cursor-not-allowed');
                }
            });
        } else {
            specWarning.classList.add('hidden');
            specCheckboxes.forEach(cb => {
                cb.disabled = false;
                cb.closest('label').classList.remove('opacity-50', 'cursor-not-allowed');
            });
        }

        // Toggle label styling
        specCheckboxes.forEach(cb => {
            const label = cb.closest('.spec-label');
            if (cb.checked) {
                label.classList.add('bg-brand-50', 'border-brand-500', 'text-brand-900', 'font-bold');
                label.classList.remove('bg-white', 'border-slate-200', 'text-slate-700');
            } else {
                label.classList.remove('bg-brand-50', 'border-brand-500', 'text-brand-900', 'font-bold');
                label.classList.add('bg-white', 'border-slate-200', 'text-slate-700');
            }
        });
    }

    specCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateSpecCount);
    });

    // Run once on load
    updateSpecCount();
});
</script>

<?= $this->endSection() ?>
