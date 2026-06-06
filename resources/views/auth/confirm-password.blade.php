<x-guest-layout>
    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Konfirmasi Password</h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
            Ini adalah area yang aman. Silakan konfirmasi password Anda sebelum melanjutkan.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <!-- Password -->
        <div>
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   class="form-input" placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full btn-primary justify-center py-3 text-base">
            Konfirmasi
        </button>
    </form>
</x-guest-layout>
