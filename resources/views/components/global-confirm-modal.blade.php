{{-- 
    Global Confirm Modal — Self-contained (tanpa x-modal wrapper)
    Menghindari nested x-data scope issue.
--}}
<div x-data="{
    show: false,
    formId: null,
    message: '',
    open(detail) {
        this.formId = detail.formId;
        this.message = detail.message;
        this.show = true;
    },
    close() {
        this.show = false;
    },
    doConfirm() {
        if (this.formId) {
            const form = document.getElementById(this.formId);
            if (form) form.submit();
        }
        this.close();
    }
}"
@open-global-confirm.window="open($event.detail)"
@keydown.escape.window="close()"
>
    {{-- Backdrop --}}
    <template x-teleport="body">
        <div x-show="show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6"
             x-transition:enter="ease-out duration-200" x-transition:leave="ease-in duration-150">

            {{-- Overlay --}}
            <div x-show="show"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="close()"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm">
            </div>

            {{-- Dialog --}}
            <div x-show="show"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                 class="relative w-full max-w-sm bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">

                <div class="p-6">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 dark:bg-red-500/20 rounded-full">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    
                    <h3 class="text-lg font-bold text-center text-slate-800 dark:text-white mb-2">Konfirmasi Aksi</h3>
                    <p class="text-sm text-center text-slate-500 dark:text-slate-400 mb-6" x-text="message"></p>
                    
                    <div class="flex items-center justify-center gap-3">
                        <button type="button" @click="close()" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-colors">
                            Batal
                        </button>
                        <button type="button" @click="doConfirm()" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-xl transition-colors">
                            Ya, Lanjutkan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
