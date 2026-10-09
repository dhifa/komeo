<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>

<div class="mb-6 text-center">
    <div class="w-12 h-12 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center mx-auto mb-3">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
    </div>
    <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Lupa Kata Sandi?</h2>
    <p class="text-sm text-slate-500 mt-1">Masukkan alamat email terdaftar untuk mengatur ulang kata sandi Anda</p>
</div>

<form action="<?= base_url('forgot-password') ?>" method="POST" class="space-y-4">
    <?= csrf_field() ?>

    <div>
        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
            Alamat Email Terdaftar
        </label>
        <input type="email" id="email" name="email" required autocomplete="email"
            value="<?= old('email') ?>"
            placeholder="nama@email.com"
            class="block w-full px-4 py-3 rounded-xl border border-slate-200 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm transition-all shadow-xs">
    </div>

    <div>
        <button type="submit"
            class="w-full flex justify-center items-center py-3.5 px-4 rounded-xl text-sm font-bold text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 shadow-md shadow-brand-500/20 hover:shadow-brand-500/30 transition-all duration-200">
            Kirim Tautan Pemulihan
        </button>
    </div>
</form>

<div class="mt-6 pt-6 border-t border-slate-100 text-center">
    <p class="text-xs text-slate-500">
        Ingat kata sandi Anda?
        <a href="<?= base_url('login') ?>" class="font-bold text-brand-600 hover:text-brand-700 ml-1 transition-colors">
            Kembali Masuk
        </a>
    </p>
</div>

<?= $this->endSection() ?>
