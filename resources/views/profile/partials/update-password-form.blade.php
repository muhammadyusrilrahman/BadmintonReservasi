<section>
    <header>
        <h2 class="text-lg font-semibold text-slate-800 dark:text-white">
            Ubah Password
        </h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            Gunakan password yang panjang dan acak agar akun Anda tetap aman.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="form-label">Password Saat Ini</label>
            <input id="update_password_current_password" name="current_password" type="password" class="form-input" autocomplete="current-password" placeholder="••••••••">
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <label for="update_password_password" class="form-label">Password Baru</label>
            <input id="update_password_password" name="password" type="password" class="form-input" autocomplete="new-password" placeholder="Minimal 8 karakter">
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-input" autocomplete="new-password" placeholder="Ulangi password baru">
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="btn-primary">Simpan</button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                   class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">
                    Tersimpan.
                </p>
            @endif
        </div>
    </form>
</section>
