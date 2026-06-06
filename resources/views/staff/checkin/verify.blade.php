<x-layouts.app :title="$title">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm mb-6" aria-label="Breadcrumb">
        <a href="{{ route('staff.checkin.index') }}" class="text-slate-500 dark:text-slate-400 hover:text-pink-600 dark:hover:text-pink-400 transition-colors">Check-in Hari Ini</a>
        <svg class="w-4 h-4 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-800 dark:text-white font-medium">Verifikasi {{ $reservation->booking_code }}</span>
    </nav>

    @php
        $isEligible = $reservation->status === 'confirmed'
            && $reservation->payment
            && $reservation->payment->status === 'paid'
            && $reservation->date->isToday()
            && $reservation->checked_in_at === null;
    @endphp

    {{-- Status Banner --}}
    @if($reservation->status === 'completed' && $reservation->is_checked_in)
        <div class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-800 rounded-2xl p-5 flex items-center gap-4 mb-6">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="font-bold text-emerald-800 dark:text-emerald-300">Sudah Check-in</p>
                <p class="text-sm text-emerald-700 dark:text-emerald-400 mt-0.5">Check-in pada {{ $reservation->checked_in_at->format('d/m/Y H:i') }} oleh {{ $reservation->checkedInBy->name ?? '-' }}</p>
            </div>
        </div>
    @elseif($isEligible)
        <div class="bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-800 rounded-2xl p-5 flex items-center gap-4 mb-6">
            <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="font-bold text-blue-800 dark:text-blue-300">Siap Check-in</p>
                <p class="text-sm text-blue-700 dark:text-blue-400 mt-0.5">Reservasi valid dan pembayaran sudah terverifikasi. Lanjutkan proses check-in.</p>
            </div>
        </div>
    @else
        <div class="bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-800 rounded-2xl p-5 flex items-center gap-4 mb-6">
            <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            </div>
            <div>
                <p class="font-bold text-amber-800 dark:text-amber-300">Tidak Dapat Check-in</p>
                <p class="text-sm text-amber-700 dark:text-amber-400 mt-0.5">
                    @if($reservation->status !== 'confirmed') Status reservasi: {{ $reservation->status_label }}.
                    @elseif(!$reservation->payment || $reservation->payment->status !== 'paid') Pembayaran belum lunas.
                    @elseif(!$reservation->date->isToday()) Reservasi bukan untuk hari ini ({{ $reservation->date->format('d/m/Y') }}).
                    @endif
                </p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Booking Info --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
                <h3 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2 mb-5">
                    <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Detail Booking
                </h3>

                {{-- Booking Code --}}
                <div class="text-center mb-6 py-4 bg-slate-50 dark:bg-slate-800 rounded-xl">
                    <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Booking Code</p>
                    <p class="text-3xl font-mono font-black text-slate-800 dark:text-white tracking-widest">{{ $reservation->booking_code }}</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Lapangan</p>
                            <p class="text-sm font-semibold text-slate-800 dark:text-white mt-0.5">{{ $reservation->court->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Tanggal</p>
                            <p class="text-sm font-semibold text-slate-800 dark:text-white mt-0.5">{{ $reservation->date->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Jam Main</p>
                            <p class="text-sm font-semibold text-slate-800 dark:text-white mt-0.5">{{ substr($reservation->start_time, 0, 5) }} - {{ substr($reservation->end_time, 0, 5) }} ({{ $reservation->duration_hours }} jam)</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Customer</p>
                            <p class="text-sm font-semibold text-slate-800 dark:text-white mt-0.5">{{ $reservation->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Email</p>
                            <p class="text-sm text-slate-700 dark:text-slate-300 mt-0.5">{{ $reservation->user->email }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">No. Telepon</p>
                            <p class="text-sm text-slate-700 dark:text-slate-300 mt-0.5">{{ $reservation->user->phone ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Payment + Action --}}
        <div class="space-y-6">
            {{-- Payment Info --}}
            @if($reservation->payment)
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
                <h3 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Info Pembayaran
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-xs text-slate-500">Metode</span>
                        <span class="text-sm font-medium text-slate-800 dark:text-white">{{ $reservation->payment->method_label }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-xs text-slate-500">Status</span>
                        <span class="text-sm font-medium {{ $reservation->payment->status === 'paid' ? 'text-emerald-600' : 'text-amber-600' }}">{{ $reservation->payment->status_label }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-xs text-slate-500">Jumlah</span>
                        <span class="text-sm font-bold text-slate-800 dark:text-white">{{ $reservation->payment->formatted_amount }}</span>
                    </div>
                    @if($reservation->payment->paid_at)
                    <div class="flex justify-between">
                        <span class="text-xs text-slate-500">Dibayar</span>
                        <span class="text-sm text-slate-700 dark:text-slate-300">{{ $reservation->payment->paid_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Check-in Action --}}
            @if($isEligible)
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
                <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-4">Proses Check-In</h3>
                <form action="{{ route('staff.checkin.process', $reservation) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="w-full relative inline-flex items-center justify-center gap-2 px-6 py-4 bg-gradient-to-r from-[#1e3a5f] to-[#e91e8c] hover:from-[#152a46] hover:to-[#ce1277] text-white text-base font-bold rounded-xl shadow-lg shadow-pink-500/20 hover:shadow-pink-500/30 transition-all duration-200 active:scale-[0.98]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Proses Check-In
                    </button>
                </form>
            </div>
            @elseif($reservation->status === 'completed' && $reservation->is_checked_in)
            <div class="bg-emerald-50 dark:bg-emerald-500/5 rounded-2xl border border-emerald-200 dark:border-emerald-500/20 p-6 text-center">
                <svg class="w-10 h-10 text-emerald-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm font-bold text-emerald-800 dark:text-emerald-400">Check-in Berhasil</p>
                <p class="text-xs text-emerald-600 dark:text-emerald-500 mt-1">{{ $reservation->checked_in_at->format('d/m/Y H:i') }}</p>
                <p class="text-xs text-emerald-600 dark:text-emerald-500">oleh {{ $reservation->checkedInBy->name ?? '-' }}</p>
            </div>
            @endif
        </div>
    </div>

</x-layouts.app>
