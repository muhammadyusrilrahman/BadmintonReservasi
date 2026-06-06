<x-guest-layout>
    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Verifikasi Email</h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
            Terima kasih telah mendaftar! Sebelum melanjutkan, silakan verifikasi email Anda dengan klik link yang kami kirimkan. Jika Anda tidak menerima email, kami akan mengirimkan ulang.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-300 text-sm">
            Link verifikasi baru telah dikirim ke alamat email Anda.
        </div>
    @endif

    <div class="flex flex-col gap-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="w-full btn-primary justify-center py-3 text-base">
                Kirim Ulang Email Verifikasi
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full btn-secondary justify-center py-3 text-base text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 border-red-200 dark:border-red-500/20">
                Keluar
            </button>
        </form>
    </div>
</x-guest-layout>
