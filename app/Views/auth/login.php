<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>

<div class="mb-6 text-center">
    <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Selamat Datang Kembali</h2>
    <p class="text-sm text-slate-500 mt-1">Masuk ke akun KOMEO.ID Anda</p>
</div>

<form action="<?= base_url('login') ?>" method="POST" class="space-y-5">
    <?= csrf_field() ?>

    <!-- Login Field (Email or Username) -->
    <div>
        <label for="login" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
            Email atau Nama Pengguna
        </label>
        <div class="relative">
            <input type="text" id="login" name="login" required autocomplete="username"
                value="<?= old('login') ?>"
                placeholder="nama@email.com atau username"
                class="block w-full px-4 py-3 rounded-xl border border-slate-200 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm transition-all shadow-xs">
        </div>
    </div>

    <!-- Password Field -->
    <div>
        <div class="flex items-center justify-between mb-1.5">
            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                Kata Sandi
            </label>
            <a href="<?= base_url('forgot-password') ?>" class="text-xs font-semibold text-brand-600 hover:text-brand-700 transition-colors">
                Lupa sandi?
            </a>
        </div>
        <div class="relative">
            <input type="password" id="password" name="password" required autocomplete="current-password"
                placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                class="block w-full px-4 py-3 pr-11 rounded-xl border border-slate-200 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm transition-all shadow-xs">
            <button type="button" id="togglePasswordBtn" onclick="togglePasswordVisibility()"
                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-brand-600 focus:outline-none transition-colors"
                title="Tampilkan kata sandi" aria-label="Tampilkan atau sembunyikan kata sandi">
                <!-- Eye Icon (Password hidden / Click to show) -->
                <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <!-- Eye Off Icon (Password visible / Click to hide) -->
                <svg id="eyeOffIcon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Remember Me Checkbox -->
    <div class="flex items-center">
        <input id="remember" name="remember" type="checkbox" value="1" <?= old('remember') ? 'checked' : '' ?>
            class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500 cursor-pointer">
        <label for="remember" class="ml-2 block text-xs text-slate-600 cursor-pointer">
            Ingat saya di perangkat ini
        </label>
    </div>

    <!-- Submit Button -->
    <div>
        <button type="submit"
            class="w-full flex justify-center items-center py-3.5 px-4 rounded-xl text-sm font-bold text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 shadow-md shadow-brand-500/20 hover:shadow-brand-500/30 transition-all duration-200">
            Masuk ke Akun
        </button>
    </div>
</form>

<!-- Register CTA -->
<div class="mt-6 pt-6 border-t border-slate-100 text-center">
    <p class="text-xs text-slate-500">
        Belum bergabung di komunitas?
        <a href="<?= base_url('register') ?>" class="font-bold text-brand-600 hover:text-brand-700 ml-1 transition-colors">
            Daftar Sekarang
        </a>
    </p>
</div>

<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        const eyeOffIcon = document.getElementById('eyeOffIcon');
        const toggleBtn = document.getElementById('togglePasswordBtn');

        if (!passwordInput) return;

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            if (eyeIcon) eyeIcon.classList.add('hidden');
            if (eyeOffIcon) eyeOffIcon.classList.remove('hidden');
            if (toggleBtn) {
                toggleBtn.setAttribute('title', 'Sembunyikan kata sandi');
                toggleBtn.setAttribute('aria-label', 'Sembunyikan kata sandi');
            }
        } else {
            passwordInput.type = 'password';
            if (eyeIcon) eyeIcon.classList.remove('hidden');
            if (eyeOffIcon) eyeOffIcon.classList.add('hidden');
            if (toggleBtn) {
                toggleBtn.setAttribute('title', 'Tampilkan kata sandi');
                toggleBtn.setAttribute('aria-label', 'Tampilkan kata sandi');
            }
        }
    }
</script>

<?= $this->endSection() ?>
