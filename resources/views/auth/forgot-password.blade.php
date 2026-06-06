<x-guest-layout>
    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Lupa Password</h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
            Masukkan email Anda dan kami akan mengirimkan link untuk mereset password.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="form-input" placeholder="nama@email.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full btn-primary justify-center py-3 text-base">
            Kirim Link Reset Password
        </button>

        <!-- Back to Login -->
        <p class="text-center text-sm text-slate-500 dark:text-slate-400">
            Ingat password Anda?
            <a href="{{ route('login') }}" class="text-pink-600 dark:text-pink-400 hover:text-pink-700 dark:hover:text-pink-300 font-semibold transition-colors">
                Kembali ke halaman masuk
            </a>
        </p>
    </form>
</x-guest-layout>
