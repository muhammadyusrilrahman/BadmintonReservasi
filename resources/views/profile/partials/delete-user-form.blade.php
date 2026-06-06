<section class="space-y-6">
    <header>
        <h2 class="text-lg font-semibold text-slate-800 dark:text-white">
            Hapus Akun
        </h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            Setelah akun dihapus, semua data akan dihapus secara permanen. Pastikan Anda telah mengunduh data yang diperlukan sebelum menghapus akun.
        </p>
    </header>

    <button type="button" class="btn-danger"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
        Hapus Akun
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-semibold text-slate-800 dark:text-white">
                Apakah Anda yakin ingin menghapus akun?
            </h2>

            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                Setelah akun dihapus, semua data akan dihapus secara permanen. Silakan masukkan password untuk mengonfirmasi penghapusan akun.
            </p>

            <div class="mt-6">
                <label for="password" class="sr-only">Password</label>
                <input id="password" name="password" type="password" class="form-input w-3/4" placeholder="Password">
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" class="btn-secondary" x-on:click="$dispatch('close')">
                    Batal
                </button>
                <button type="submit" class="btn-danger">
                    Hapus Akun
                </button>
            </div>
        </form>
    </x-modal>
</section>
