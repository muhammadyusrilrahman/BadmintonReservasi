<x-layouts.app :title="$title">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Halo, {{ auth()->user()->name }}! 🏸</h1>
        <p class="text-slate-500 dark:text-slate-400 mt-1">Ayo booking lapangan untuk bermain badminton!</p>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-8">
        <a href="{{ route('customer.booking.create') }}" class="group relative bg-gradient-to-br from-[#1e3a5f] to-[#2a4a73] rounded-2xl p-6 text-white hover:shadow-xl hover:shadow-[#1e3a5f]/30 transition-all duration-300 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-pink-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative">
                <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>
                </div>
                <h3 class="font-semibold text-lg">Booking Lapangan</h3>
                <p class="text-white/70 text-sm mt-1">Reservasi lapangan sekarang</p>
            </div>
        </a>

        <a href="{{ route('customer.reservations.index') }}" class="group relative bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 hover:shadow-lg transition-all duration-300 overflow-hidden">
            <div class="relative">
                <div class="w-12 h-12 rounded-xl bg-pink-100 dark:bg-pink-500/20 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-pink-600 dark:text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="font-semibold text-slate-800 dark:text-white">Reservasi Saya</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">{{ $totalReservations }} total reservasi</p>
            </div>
        </a>

        <div class="group relative bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 overflow-hidden">
            <div class="relative">
                <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="font-semibold text-slate-800 dark:text-white">Menunggu Bayar</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">{{ $pendingCount }} reservasi pending</p>
            </div>
        </div>
    </div>

    {{-- Upcoming Reservations --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <h3 class="font-semibold text-slate-800 dark:text-white">Reservasi Mendatang</h3>
            @if($upcomingReservations->isNotEmpty())
                <a href="{{ route('customer.reservations.index') }}" class="text-sm text-pink-600 dark:text-pink-400 hover:underline">Lihat Semua</a>
            @endif
        </div>
        <div class="p-6">
            @if($upcomingReservations->isEmpty())
                <div class="flex flex-col items-center justify-center py-8 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <p class="text-slate-500 dark:text-slate-400 text-sm">Belum ada reservasi mendatang</p>
                    <a href="{{ route('customer.booking.create') }}" class="mt-3 inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-[#1e3a5f] to-[#e91e8c] text-white text-sm font-medium rounded-xl hover:shadow-lg transition-all duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Booking Sekarang
                    </a>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($upcomingReservations as $reservation)
                        <a href="{{ route('customer.reservations.show', $reservation) }}" class="flex items-center gap-4 p-4 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 hover:shadow-sm transition-all group">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#1e3a5f] to-[#2a4a73] flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                                <svg class="w-6 h-6 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-slate-800 dark:text-white text-sm">{{ $reservation->court->name }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                    {{ $reservation->date->translatedFormat('l, d F Y') }} · {{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($reservation->end_time)->format('H:i') }}
                                </p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="text-sm font-bold text-slate-800 dark:text-white">{{ $reservation->formatted_total_price }}</p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium mt-1
                                    bg-{{ $reservation->status_color }}-50 dark:bg-{{ $reservation->status_color }}-500/10
                                    text-{{ $reservation->status_color }}-700 dark:text-{{ $reservation->status_color }}-400">
                                    {{ $reservation->status_label }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
