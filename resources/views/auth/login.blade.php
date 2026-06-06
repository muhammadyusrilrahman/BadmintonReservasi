<x-guest-layout>
    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Masuk</h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Selamat datang kembali! Silakan masuk ke akun Anda.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   class="form-input" placeholder="nama@email.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   class="form-input" placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" name="remember"
                       class="rounded border-slate-300 dark:border-slate-600 text-pink-600 shadow-sm focus:ring-pink-500 dark:bg-slate-800">
                <span class="ms-2 text-sm text-slate-600 dark:text-slate-400">Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-pink-600 dark:text-pink-400 hover:text-pink-700 dark:hover:text-pink-300 font-medium transition-colors" href="{{ route('password.request') }}">
                    Lupa password?
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full btn-primary justify-center py-3 text-base">
            Masuk
        </button>

        <!-- Register Link -->
        <p class="text-center text-sm text-slate-500 dark:text-slate-400">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-pink-600 dark:text-pink-400 hover:text-pink-700 dark:hover:text-pink-300 font-semibold transition-colors">
                Daftar sekarang
            </a>
        </p>
    </form>
</x-guest-layout>
