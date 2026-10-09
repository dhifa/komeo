<!-- KOMEO Global Modal & Notification Component -->
<div id="komeo-modal-root" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-200 opacity-0 pointer-events-none" aria-hidden="true" role="dialog" aria-modal="true">
    <div id="komeo-modal-card" class="bg-white rounded-3xl border border-slate-200/80 max-w-md w-full p-6 sm:p-7 shadow-2xl relative transform scale-95 transition-all duration-200 ease-out">
        
        <!-- Icon & Header Container -->
        <div class="flex items-start gap-4 mb-4">
            <!-- Dynamic Icon Container -->
            <div id="komeo-modal-icon-container" class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0">
                <!-- Danger Icon -->
                <svg id="komeo-modal-icon-danger" class="w-6 h-6 text-rose-600 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                <!-- Warning Icon -->
                <svg id="komeo-modal-icon-warning" class="w-6 h-6 text-amber-600 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <!-- Info Icon -->
                <svg id="komeo-modal-icon-info" class="w-6 h-6 text-indigo-600 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <!-- Success Icon -->
                <svg id="komeo-modal-icon-success" class="w-6 h-6 text-emerald-600 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <!-- Title & Subtitle/Type -->
            <div class="flex-1 min-w-0 pt-0.5">
                <h3 id="komeo-modal-title" class="text-base sm:text-lg font-extrabold text-slate-900 leading-snug">
                    Konfirmasi Tindakan
                </h3>
                <p id="komeo-modal-badge" class="text-[11px] font-bold tracking-wide uppercase mt-0.5 text-slate-400">
                    KOMEO.ID
                </p>
            </div>

            <!-- Close (X) button -->
            <button type="button" id="komeo-modal-btn-close" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition-colors focus:outline-none" aria-label="Tutup">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <!-- Body Message -->
        <div class="mb-6 pl-0 sm:pl-16">
            <div id="komeo-modal-message" class="text-xs sm:text-sm text-slate-600 leading-relaxed whitespace-pre-line">
                Apakah Anda yakin ingin melanjutkan tindakan ini?
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
            <button type="button" id="komeo-modal-btn-cancel" class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors focus:outline-none">
                Batal
            </button>
            <button type="button" id="komeo-modal-btn-confirm" class="px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white transition-all shadow-xs focus:outline-none">
                Ya, Lanjutkan
            </button>
        </div>
    </div>
</div>

<!-- KOMEO Toast Container -->
<div id="komeo-toast-container" class="fixed bottom-5 right-5 z-[99999] flex flex-col gap-2 pointer-events-none max-w-sm w-full px-4"></div>

<script>
(function() {
    // Elements
    const root = document.getElementById('komeo-modal-root');
    const card = document.getElementById('komeo-modal-card');
    const titleEl = document.getElementById('komeo-modal-title');
    const badgeEl = document.getElementById('komeo-modal-badge');
    const messageEl = document.getElementById('komeo-modal-message');
    const iconContainer = document.getElementById('komeo-modal-icon-container');
    const btnCancel = document.getElementById('komeo-modal-btn-cancel');
    const btnConfirm = document.getElementById('komeo-modal-btn-confirm');
    const btnClose = document.getElementById('komeo-modal-btn-close');
    const toastContainer = document.getElementById('komeo-toast-container');

    const icons = {
        danger: document.getElementById('komeo-modal-icon-danger'),
        warning: document.getElementById('komeo-modal-icon-warning'),
        info: document.getElementById('komeo-modal-icon-info'),
        success: document.getElementById('komeo-modal-icon-success')
    };

    let activeResolve = null;
    let onConfirmCb = null;
    let onCancelCb = null;

    function setIconAndColors(type) {
        // Reset icon visibility
        Object.values(icons).forEach(ic => { if (ic) ic.classList.add('hidden'); });

        if (type === 'danger') {
            if (icons.danger) icons.danger.classList.remove('hidden');
            iconContainer.className = 'w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 bg-rose-50 border border-rose-100';
            btnConfirm.className = 'px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 active:bg-rose-800 transition-all shadow-md shadow-rose-600/20';
            badgeEl.className = 'text-[11px] font-bold tracking-wide uppercase mt-0.5 text-rose-500';
            badgeEl.textContent = 'Peringatan Bahaya';
        } else if (type === 'warning') {
            if (icons.warning) icons.warning.classList.remove('hidden');
            iconContainer.className = 'w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 bg-amber-50 border border-amber-100';
            btnConfirm.className = 'px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-amber-600 hover:bg-amber-700 active:bg-amber-800 transition-all shadow-md shadow-amber-600/20';
            badgeEl.className = 'text-[11px] font-bold tracking-wide uppercase mt-0.5 text-amber-500';
            badgeEl.textContent = 'Perhatian';
        } else if (type === 'success') {
            if (icons.success) icons.success.classList.remove('hidden');
            iconContainer.className = 'w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 bg-emerald-50 border border-emerald-100';
            btnConfirm.className = 'px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 transition-all shadow-md shadow-emerald-600/20';
            badgeEl.className = 'text-[11px] font-bold tracking-wide uppercase mt-0.5 text-emerald-600';
            badgeEl.textContent = 'Sukses';
        } else {
            // Default: info / brand
            if (icons.info) icons.info.classList.remove('hidden');
            iconContainer.className = 'w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 bg-indigo-50 border border-indigo-100';
            btnConfirm.className = 'px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-brand-600 hover:bg-brand-700 active:bg-brand-800 transition-all shadow-md shadow-brand-600/20';
            badgeEl.className = 'text-[11px] font-bold tracking-wide uppercase mt-0.5 text-brand-600';
            badgeEl.textContent = 'Informasi';
        }
    }

    function showModal() {
        root.classList.remove('opacity-0', 'pointer-events-none');
        root.classList.add('opacity-100');
        card.classList.remove('scale-95');
        card.classList.add('scale-100');
        document.body.style.overflow = 'hidden';
        btnConfirm.focus();
    }

    function hideModal() {
        root.classList.add('opacity-0', 'pointer-events-none');
        root.classList.remove('opacity-100');
        card.classList.add('scale-95');
        card.classList.remove('scale-100');
        document.body.style.overflow = '';
    }

    btnConfirm.addEventListener('click', function() {
        hideModal();
        if (typeof onConfirmCb === 'function') onConfirmCb();
        if (activeResolve) activeResolve(true);
        activeResolve = null;
        onConfirmCb = null;
        onCancelCb = null;
    });

    function handleCancel() {
        hideModal();
        if (typeof onCancelCb === 'function') onCancelCb();
        if (activeResolve) activeResolve(false);
        activeResolve = null;
        onConfirmCb = null;
        onCancelCb = null;
    }

    btnCancel.addEventListener('click', handleCancel);
    btnClose.addEventListener('click', handleCancel);

    // Click outside to cancel
    root.addEventListener('click', function(e) {
        if (e.target === root) {
            handleCancel();
        }
    });

    // Escape key to cancel
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !root.classList.contains('pointer-events-none')) {
            handleCancel();
        }
    });

    // Global KomeoModal API
    window.KomeoModal = {
        /**
         * Open a confirmation modal popup
         * @param {Object} options
         * @returns {Promise<boolean>}
         */
        confirm: function(options) {
            const opts = Object.assign({
                title: 'Konfirmasi Tindakan',
                message: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
                confirmText: 'Ya, Lanjutkan',
                cancelText: 'Batal',
                type: 'danger',
                onConfirm: null,
                onCancel: null
            }, options);

            titleEl.textContent = opts.title;
            messageEl.textContent = opts.message;
            btnConfirm.textContent = opts.confirmText;
            btnCancel.textContent = opts.cancelText;
            btnCancel.classList.remove('hidden');

            setIconAndColors(opts.type);

            onConfirmCb = opts.onConfirm;
            onCancelCb = opts.onCancel;

            showModal();

            return new Promise((resolve) => {
                activeResolve = resolve;
            });
        },

        /**
         * Open an alert modal popup
         * @param {Object|string} options
         * @returns {Promise<boolean>}
         */
        alert: function(options) {
            let opts;
            if (typeof options === 'string') {
                opts = { message: options };
            } else {
                opts = options || {};
            }

            const config = Object.assign({
                title: 'Pemberitahuan',
                message: '',
                confirmText: 'Mengerti',
                type: 'info',
                onOk: null
            }, opts);

            titleEl.textContent = config.title;
            messageEl.textContent = config.message;
            btnConfirm.textContent = config.confirmText;
            btnCancel.classList.add('hidden'); // Hide Cancel button for alerts

            setIconAndColors(config.type);

            onConfirmCb = config.onOk;
            onCancelCb = config.onOk;

            showModal();

            return new Promise((resolve) => {
                activeResolve = resolve;
            });
        },

        /**
         * Quick Toast popup
         * @param {string} message
         * @param {string} type ('success'|'info'|'warning'|'danger')
         * @param {number} duration ms
         */
        toast: function(message, type = 'success', duration = 3000) {
            const toast = document.createElement('div');
            toast.className = 'pointer-events-auto flex items-center gap-3 p-4 rounded-2xl bg-slate-900 text-white shadow-xl border border-slate-700/60 transform translate-y-3 opacity-0 transition-all duration-300';
            
            let iconSvg = '';
            if (type === 'success') {
                iconSvg = '<svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
            } else if (type === 'warning') {
                iconSvg = '<svg class="w-5 h-5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>';
            } else if (type === 'danger') {
                iconSvg = '<svg class="w-5 h-5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
            } else {
                iconSvg = '<svg class="w-5 h-5 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
            }

            toast.innerHTML = `${iconSvg}<div class="text-xs sm:text-sm font-semibold flex-1 leading-snug">${message}</div>`;
            toastContainer.appendChild(toast);

            // Animate in
            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-3', 'opacity-0');
            });

            // Animate out
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, duration);
        }
    };

    // Override native browser alert with website modal popup
    window.alert = function(msg) {
        window.KomeoModal.alert({
            title: 'Pemberitahuan',
            message: String(msg),
            type: 'info'
        });
    };

    // Auto-intercept elements with data-komeo-confirm
    document.addEventListener('click', function(e) {
        const trigger = e.target.closest('[data-komeo-confirm]');
        if (!trigger) return;

        e.preventDefault();
        e.stopPropagation();

        const message = trigger.getAttribute('data-komeo-confirm') || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
        const title = trigger.getAttribute('data-komeo-confirm-title') || 'Konfirmasi Tindakan';
        const type = trigger.getAttribute('data-komeo-confirm-type') || 'danger';
        const confirmText = trigger.getAttribute('data-komeo-confirm-btn') || 'Ya, Lanjutkan';

        window.KomeoModal.confirm({
            title: title,
            message: message,
            confirmText: confirmText,
            type: type,
            onConfirm: function() {
                if (trigger.tagName === 'A') {
                    window.location.href = trigger.href;
                } else if (trigger.form) {
                    trigger.form.submit();
                } else if (trigger.tagName === 'BUTTON' && trigger.getAttribute('type') === 'submit') {
                    const form = trigger.closest('form');
                    if (form) form.submit();
                }
            }
        });
    }, true);

    // Auto-intercept forms with data-komeo-confirm-form
    document.addEventListener('submit', function(e) {
        const form = e.target.closest('form[data-komeo-confirm-form]');
        if (!form) return;

        if (form.dataset.confirmed === 'true') {
            return; // Allow submission
        }

        e.preventDefault();
        e.stopPropagation();

        const message = form.getAttribute('data-komeo-confirm-form') || 'Apakah Anda yakin ingin memproses data ini?';
        const title = form.getAttribute('data-komeo-confirm-title') || 'Konfirmasi Formulir';
        const type = form.getAttribute('data-komeo-confirm-type') || 'danger';
        const confirmText = form.getAttribute('data-komeo-confirm-btn') || 'Ya, Lanjutkan';

        window.KomeoModal.confirm({
            title: title,
            message: message,
            confirmText: confirmText,
            type: type,
            onConfirm: function() {
                form.dataset.confirmed = 'true';
                form.submit();
            }
        });
    }, true);

})();
</script>
