<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>

<div class="mb-6 text-center">
    <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Daftar Anggota KOMEO</h2>
    <p class="text-sm text-slate-500 mt-1">Bergabung bersama ribuan pelaku industri event</p>
</div>

<form action="<?= base_url('register') ?>" method="POST" class="space-y-4">
    <?= csrf_field() ?>

    <!-- Full Name -->
    <div>
        <label for="full_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
            Nama Lengkap <span class="text-rose-500">*</span>
        </label>
        <input type="text" id="full_name" name="full_name" required
            value="<?= old('full_name') ?>"
            placeholder="Contoh: Budi Pratama"
            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm transition-all shadow-xs">
    </div>

    <!-- Username & Email Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="username" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                Username <span class="text-rose-500">*</span>
            </label>
            <input type="text" id="username" name="username" required autocomplete="username"
                value="<?= old('username') ?>"
                placeholder="budipratama"
                class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm transition-all shadow-xs">
        </div>
        <div>
            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                Email Aktif <span class="text-rose-500">*</span>
            </label>
            <input type="email" id="email" name="email" required autocomplete="email"
                value="<?= old('email') ?>"
                placeholder="budi@domain.com"
                class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm transition-all shadow-xs">
        </div>
    </div>

    <!-- Member Type Selection -->
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
            Tipe Keanggotaan <span class="text-rose-500">*</span>
        </label>
        <div class="grid grid-cols-2 gap-3">
            <label class="relative flex items-center p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition-all has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50/50 has-[:checked]:ring-1 has-[:checked]:ring-brand-500">
                <input type="radio" name="member_type" value="individual" <?= old('member_type', 'individual') === 'individual' ? 'checked' : '' ?>
                    class="h-4 w-4 text-brand-600 border-slate-300 focus:ring-brand-500" onchange="toggleBusinessInput()">
                <div class="ml-2.5">
                    <span class="block text-xs font-bold text-slate-900">Perorangan</span>
                    <span class="block text-[10px] text-slate-500">Freelancer, Talent, Kru</span>
                </div>
            </label>
            <label class="relative flex items-center p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition-all has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50/50 has-[:checked]:ring-1 has-[:checked]:ring-brand-500">
                <input type="radio" name="member_type" value="business" <?= old('member_type') === 'business' ? 'checked' : '' ?>
                    class="h-4 w-4 text-brand-600 border-slate-300 focus:ring-brand-500" onchange="toggleBusinessInput()">
                <div class="ml-2.5">
                    <span class="block text-xs font-bold text-slate-900">Badan Usaha</span>
                    <span class="block text-[10px] text-slate-500">EO, WO, Vendor, Studio</span>
                </div>
            </label>
        </div>
    </div>

    <!-- Business Name (Conditional) -->
    <div id="business-field" class="<?= old('member_type') === 'business' ? '' : 'hidden' ?>">
        <label for="business_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
            Nama Usaha / Perusahaan
        </label>
        <input type="text" id="business_name" name="business_name"
            value="<?= old('business_name') ?>"
            placeholder="Contoh: Pratama Event Production"
            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm transition-all shadow-xs">
    </div>

    <!-- WhatsApp & City Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="whatsapp" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                WhatsApp
            </label>
            <input type="text" id="whatsapp" name="whatsapp"
                value="<?= old('whatsapp') ?>"
                placeholder="081234567890"
                class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm transition-all shadow-xs">
        </div>
        <div>
            <label for="city" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                Kota / Kabupaten
            </label>
            <input type="text" id="city" name="city"
                value="<?= old('city') ?>"
                placeholder="Jakarta / Bandung / Surabaya"
                class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm transition-all shadow-xs">
        </div>
    </div>

    <!-- Password & Confirm Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                Kata Sandi <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <input type="password" id="password" name="password" required autocomplete="new-password"
                    placeholder="Minimal 8 karakter"
                    class="block w-full px-3.5 py-2.5 pr-10 rounded-xl border border-slate-200 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm transition-all shadow-xs">
                <button type="button" onclick="togglePasswordField('password', 'eyeIcon1', 'eyeOffIcon1')"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-brand-600 focus:outline-none transition-colors"
                    title="Tampilkan / Sembunyikan Sandi" aria-label="Tampilkan atau sembunyikan kata sandi">
                    <svg id="eyeIcon1" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg id="eyeOffIcon1" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                    </svg>
                </button>
            </div>
        </div>
        <div>
            <label for="password_confirm" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                Ulangi Sandi <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <input type="password" id="password_confirm" name="password_confirm" required autocomplete="new-password"
                    placeholder="Ketik ulang kata sandi"
                    class="block w-full px-3.5 py-2.5 pr-10 rounded-xl border border-slate-200 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm transition-all shadow-xs">
                <button type="button" onclick="togglePasswordField('password_confirm', 'eyeIcon2', 'eyeOffIcon2')"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-brand-600 focus:outline-none transition-colors"
                    title="Tampilkan / Sembunyikan Sandi" aria-label="Tampilkan atau sembunyikan konfirmasi kata sandi">
                    <svg id="eyeIcon2" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg id="eyeOffIcon2" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="pt-2">
        <button type="submit"
            class="w-full flex justify-center items-center py-3.5 px-4 rounded-xl text-sm font-bold text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 shadow-md shadow-brand-500/20 hover:shadow-brand-500/30 transition-all duration-200">
            Daftar Sebagai Anggota
        </button>
    </div>
</form>

<div class="mt-6 pt-6 border-t border-slate-100 text-center">
    <p class="text-xs text-slate-500">
        Sudah memiliki akun?
        <a href="<?= base_url('login') ?>" class="font-bold text-brand-600 hover:text-brand-700 ml-1 transition-colors">
            Masuk ke Akun
        </a>
    </p>
</div>

<script>
    function toggleBusinessInput() {
        const businessRadio = document.querySelector('input[name="member_type"][value="business"]');
        const field = document.getElementById('business-field');
        if (businessRadio && field) {
            if (businessRadio.checked) {
                field.classList.remove('hidden');
            } else {
                field.classList.add('hidden');
            }
        }
    }

    function togglePasswordField(inputId, eyeId, eyeOffId) {
        const input = document.getElementById(inputId);
        const eye = document.getElementById(eyeId);
        const eyeOff = document.getElementById(eyeOffId);
        if (!input) return;

        if (input.type === 'password') {
            input.type = 'text';
            if (eye) eye.classList.add('hidden');
            if (eyeOff) eyeOff.classList.remove('hidden');
        } else {
            input.type = 'password';
            if (eye) eye.classList.remove('hidden');
            if (eyeOff) eyeOff.classList.add('hidden');
        }
    }
</script>

<?= $this->endSection() ?>
