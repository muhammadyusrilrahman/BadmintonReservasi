<x-layouts.app :title="$title">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $title }}</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>
        <a href="{{ route('staff.checkin.history') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Riwayat
        </a>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Total Booking --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800 dark:text-white">{{ $stats['total_booking'] }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Total Booking</p>
                </div>
            </div>
        </div>
        {{-- Checked In --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['checked_in'] }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Sudah Check-in</p>
                </div>
            </div>
        </div>
        {{-- Waiting --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $stats['waiting_checkin'] }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Menunggu Check-in</p>
                </div>
            </div>
        </div>
        {{-- Revenue --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-pink-50 dark:bg-pink-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-pink-600 dark:text-pink-400">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Pendapatan</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Search + Court Filter --}}
    <div class="flex flex-col sm:flex-row gap-4 mb-6">
        {{-- Search --}}
        <div class="flex-1 relative" x-data="bookingSearch()">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" x-model="query" @input.debounce.300ms="search()" @focus="showResults = results.length > 0"
                       placeholder="Cari booking code, nama, atau email..."
                       class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 dark:text-white transition-colors">
            </div>
            {{-- Search Results Dropdown --}}
            <div x-show="showResults && results.length > 0" @click.away="showResults = false" x-cloak
                 class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xl z-20 max-h-80 overflow-y-auto">
                <template x-for="item in results" :key="item.id">
                    <a :href="item.verify_url" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors border-b border-slate-100 dark:border-slate-800 last:border-0">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-mono font-bold text-pink-600 dark:text-pink-400 bg-pink-50 dark:bg-pink-500/10 px-2 py-0.5 rounded" x-text="item.booking_code"></span>
                                <span class="text-sm font-medium text-slate-800 dark:text-white truncate" x-text="item.customer_name"></span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5"><span x-text="item.court_name"></span> · <span x-text="item.date"></span> · <span x-text="item.time"></span></p>
                        </div>
                        <span class="text-xs font-medium px-2 py-1 rounded-lg"
                              :class="item.status === 'confirmed' ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : item.status === 'completed' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400'"
                              x-text="item.status_label"></span>
                    </a>
                </template>
            </div>
        </div>
        {{-- Court Filter --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-1">
            <a href="{{ route('staff.checkin.index') }}"
               class="px-4 py-2.5 text-sm font-medium rounded-xl whitespace-nowrap transition-colors {{ !$selectedCourt ? 'bg-gradient-to-r from-[#1e3a5f] to-[#e91e8c] text-white shadow-sm' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                Semua
            </a>
            @foreach($courts as $court)
                <a href="{{ route('staff.checkin.index', ['court_id' => $court->id]) }}"
                   class="px-4 py-2.5 text-sm font-medium rounded-xl whitespace-nowrap transition-colors {{ $selectedCourt == $court->id ? 'bg-gradient-to-r from-[#1e3a5f] to-[#e91e8c] text-white shadow-sm' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    {{ $court->name }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Schedule Table --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
        @if($reservations->isEmpty())
            <div class="p-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 mb-4">
                    <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <p class="text-slate-500 dark:text-slate-400 font-medium">Belum ada booking untuk hari ini</p>
            </div>
        @else
            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Waktu</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Lapangan</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Customer</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Kode</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Status</th>
                            <th class="px-5 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($reservations as $reservation)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-5 py-4">
                                <span class="font-mono font-medium text-slate-800 dark:text-white">{{ substr($reservation->start_time, 0, 5) }} - {{ substr($reservation->end_time, 0, 5) }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-slate-700 dark:text-slate-300">{{ $reservation->court->name }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-medium text-slate-800 dark:text-white">{{ $reservation->user->name }}</p>
                                <p class="text-xs text-slate-500">{{ $reservation->user->email }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-xs font-mono font-bold text-pink-600 dark:text-pink-400 bg-pink-50 dark:bg-pink-500/10 px-2 py-1 rounded-lg">{{ $reservation->booking_code }}</span>
                            </td>
                            <td class="px-5 py-4">
                                @if($reservation->status === 'completed' && $reservation->is_checked_in)
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 px-2.5 py-1 rounded-lg">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Checked In · {{ $reservation->checked_in_at->format('H:i') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 px-2.5 py-1 rounded-lg">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Menunggu Check-in
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                @if($reservation->status === 'confirmed')
                                    <a href="{{ route('staff.checkin.verify', $reservation->booking_code) }}"
                                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-gradient-to-r from-[#1e3a5f] to-[#e91e8c] text-white text-xs font-semibold rounded-lg hover:shadow-lg hover:shadow-pink-500/25 transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
                                        Verifikasi
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden divide-y divide-slate-100 dark:divide-slate-800">
                @foreach($reservations as $reservation)
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-mono font-bold text-sm text-slate-800 dark:text-white">{{ substr($reservation->start_time, 0, 5) }} - {{ substr($reservation->end_time, 0, 5) }}</span>
                        <span class="text-xs font-mono font-bold text-pink-600 dark:text-pink-400 bg-pink-50 dark:bg-pink-500/10 px-2 py-0.5 rounded">{{ $reservation->booking_code }}</span>
                    </div>
                    <p class="text-sm font-medium text-slate-800 dark:text-white">{{ $reservation->user->name }}</p>
                    <p class="text-xs text-slate-500">{{ $reservation->court->name }}</p>
                    <div class="flex items-center justify-between mt-3">
                        @if($reservation->status === 'completed' && $reservation->is_checked_in)
                            <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400">✓ Checked In · {{ $reservation->checked_in_at->format('H:i') }}</span>
                        @else
                            <span class="text-xs font-medium text-blue-600 dark:text-blue-400">Menunggu Check-in</span>
                            <a href="{{ route('staff.checkin.verify', $reservation->booking_code) }}" class="text-xs font-semibold text-pink-600 dark:text-pink-400">Verifikasi →</a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        function bookingSearch() {
            return {
                query: '',
                results: [],
                showResults: false,
                async search() {
                    if (this.query.length < 2) { this.results = []; this.showResults = false; return; }
                    try {
                        const res = await fetch(`{{ route('staff.checkin.search') }}?q=${encodeURIComponent(this.query)}`, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await res.json();
                        this.results = data.results || [];
                        this.showResults = this.results.length > 0;
                    } catch(e) { this.results = []; }
                }
            }
        }
    </script>
    @endpush

</x-layouts.app>
