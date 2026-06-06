<x-layouts.app :title="$title">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Reservasi Saya</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Kelola dan pantau status reservasi Anda.</p>
        </div>
        <a href="{{ route('customer.booking.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-[#1e3a5f] to-[#e91e8c] text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:shadow-pink-500/25 hover:-translate-y-0.5 transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Booking Baru
        </a>
    </div>

    {{-- Filter Tabs --}}
    <div class="flex items-center gap-2 mb-6 overflow-x-auto pb-2" role="tablist" aria-label="Filter status reservasi">
        @php
            $statuses = ['' => 'Semua', 'pending' => 'Menunggu', 'confirmed' => 'Dikonfirmasi', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'];
        @endphp
        @foreach($statuses as $key => $label)
            <a href="{{ route('customer.reservations.index', ['status' => $key ?: null]) }}"
               role="tab"
               aria-selected="{{ request('status', '') === $key ? 'true' : 'false' }}"
               class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap transition-colors
                   {{ request('status', '') === $key ? 'bg-pink-600 text-white shadow-sm' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Reservations List --}}
    @if($reservations->isEmpty())
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-12 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 mb-4">
                <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <p class="text-slate-500 dark:text-slate-400 font-medium">Belum ada reservasi</p>
            <a href="{{ route('customer.booking.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-[#1e3a5f] to-[#e91e8c] text-white text-sm font-medium rounded-xl hover:shadow-lg transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Booking Sekarang
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($reservations as $reservation)
                <a href="{{ route('customer.reservations.show', $reservation) }}"
                   class="block bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700 transition-all group">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                        {{-- Court Info --}}
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#1e3a5f] to-[#2a4a73] flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                                <svg class="w-6 h-6 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-slate-800 dark:text-white text-sm">{{ $reservation->court->name }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                    {{ $reservation->date->translatedFormat('l, d F Y') }} · {{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($reservation->end_time)->format('H:i') }}
                                </p>
                            </div>
                        </div>

                        {{-- Price --}}
                        <div class="text-right flex-shrink-0">
                            <p class="text-sm font-bold text-slate-800 dark:text-white">{{ $reservation->formatted_total_price }}</p>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium mt-1
                                bg-{{ $reservation->status_color }}-50 dark:bg-{{ $reservation->status_color }}-500/10
                                text-{{ $reservation->status_color }}-700 dark:text-{{ $reservation->status_color }}-400">
                                {{ $reservation->status_label }}
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($reservations->hasPages())
            <div class="mt-6">
                {{ $reservations->links() }}
            </div>
        @endif
    @endif

</x-layouts.app>
