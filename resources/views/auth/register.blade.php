<x-guest-layout>
    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Daftar Akun</h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Buat akun baru untuk mulai booking lapangan.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="form-label">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                   class="form-input" placeholder="Masukkan nama lengkap">
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                   class="form-input" placeholder="nama@email.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                   class="form-input" placeholder="Minimal 8 karakter">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                   class="form-input" placeholder="Ulangi password">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full btn-primary justify-center py-3 text-base">
            Daftar
        </button>

        <!-- Login Link -->
        <p class="text-center text-sm text-slate-500 dark:text-slate-400">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-pink-600 dark:text-pink-400 hover:text-pink-700 dark:hover:text-pink-300 font-semibold transition-colors">
                Masuk di sini
            </a>
        </p>
    </form>
</x-guest-layout>
