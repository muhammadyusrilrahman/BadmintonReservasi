<div x-data="{
    formId: null,
    message: '',
    confirm() {
        if (this.formId) {
            document.getElementById(this.formId).submit();
        }
    }
}"
@open-global-confirm.window="formId = $event.detail.formId; message = $event.detail.message; $dispatch('open-modal', 'global-confirm')"
>
    <x-modal name="global-confirm" maxWidth="sm">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 dark:bg-red-500/20 rounded-full">
                <svg class="w-6 h-6 text-red-600 dark:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            
            <h3 class="text-lg font-bold text-center text-slate-800 dark:text-white mb-2">Konfirmasi Aksi</h3>
            <p class="text-sm text-center text-slate-500 dark:text-slate-400 mb-6" x-text="message"></p>
            
            <div class="flex items-center justify-center gap-3">
                <button type="button" x-on:click="$dispatch('close-modal', 'global-confirm')" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="button" x-on:click="confirm" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-xl transition-colors">
                    Ya, Lanjutkan
                </button>
            </div>
        </div>
    </x-modal>
</div>
