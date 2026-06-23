<x-layouts.app :title="$title">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $title }} 📅</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Lihat jadwal reservasi lapangan per tanggal.</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800 dark:text-white">{{ $totalReservations }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Total Reservasi</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $confirmedCount }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Dikonfirmasi</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $completedCount }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Selesai</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $pendingCount }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Menunggu</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Date Navigation + Court Filter --}}
    <div class="flex flex-col sm:flex-row gap-4 mb-6">
        {{-- Date Picker --}}
        <div class="flex items-center gap-2">
            <a href="{{ route('staff.schedule.index', array_merge(request()->except('date'), ['date' => $date->copy()->subDay()->format('Y-m-d')])) }}"
               class="p-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                <svg class="w-4 h-4 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <form method="GET" action="{{ route('staff.schedule.index') }}" class="flex items-center gap-2" id="date-form">
                @if($selectedCourt)
                    <input type="hidden" name="court_id" value="{{ $selectedCourt }}">
                @endif
                <input type="date" name="date" value="{{ $date->format('Y-m-d') }}"
                       onchange="document.getElementById('date-form').submit()"
                       class="px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-colors">
            </form>
            <a href="{{ route('staff.schedule.index', array_merge(request()->except('date'), ['date' => $date->copy()->addDay()->format('Y-m-d')])) }}"
               class="p-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                <svg class="w-4 h-4 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            @if(!$date->isToday())
            <a href="{{ route('staff.schedule.index', request()->except('date')) }}"
               class="px-3 py-2.5 text-xs font-medium text-pink-600 dark:text-pink-400 bg-pink-50 dark:bg-pink-500/10 rounded-xl hover:bg-pink-100 dark:hover:bg-pink-500/20 transition-colors">
                Hari Ini
            </a>
            @endif
        </div>

        {{-- Court Filter --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:ml-auto">
            <a href="{{ route('staff.schedule.index', array_merge(request()->except('court_id'), ['date' => $date->format('Y-m-d')])) }}"
               class="px-4 py-2.5 text-sm font-medium rounded-xl whitespace-nowrap transition-colors {{ !$selectedCourt ? 'bg-gradient-to-r from-[#1e3a5f] to-[#e91e8c] text-white shadow-sm' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                Semua
            </a>
            @foreach($courts as $court)
                <a href="{{ route('staff.schedule.index', ['date' => $date->format('Y-m-d'), 'court_id' => $court->id]) }}"
                   class="px-4 py-2.5 text-sm font-medium rounded-xl whitespace-nowrap transition-colors {{ $selectedCourt == $court->id ? 'bg-gradient-to-r from-[#1e3a5f] to-[#e91e8c] text-white shadow-sm' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    {{ $court->name }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Date Display --}}
    <div class="mb-4">
        <p class="text-sm font-semibold text-slate-600 dark:text-slate-300">
            {{ $date->translatedFormat('l, d F Y') }}
            @if($date->isToday())
                <span class="ml-2 inline-flex items-center gap-1 text-xs text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 px-2 py-0.5 rounded-lg">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Hari Ini
                </span>
            @endif
        </p>
    </div>

    {{-- Schedule Grid --}}
    <div class="space-y-4">
        @forelse($scheduleGrid as $item)
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                {{-- Court Header --}}
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-[#1e3a5f] to-[#2a4a73] flex items-center justify-center flex-shrink-0">
                        <svg class="w-4.5 h-4.5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-800 dark:text-white">{{ $item->court->name }}</p>
                        <p class="text-xs text-slate-400">{{ $item->court->type_label }} · {{ $item->court->formatted_price }}/jam</p>
                    </div>
                    <div class="ml-auto">
                        @if(!$item->court->is_active)
                            <span class="text-xs font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-500/10 px-2.5 py-1 rounded-lg">Tidak Aktif</span>
                        @else
                            <span class="text-xs text-slate-400">{{ $item->reservations->count() }} reservasi</span>
                        @endif
                    </div>
                </div>

                {{-- Reservations --}}
                @if($item->reservations->isEmpty())
                    <div class="px-5 py-8 text-center">
                        <p class="text-sm text-slate-400 dark:text-slate-500">Tidak ada reservasi pada tanggal ini</p>
                    </div>
                @else
                    {{-- Desktop Table --}}
                    <div class="hidden md:block">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50/50 dark:bg-slate-800/30">
                                <tr>
                                    <th class="px-5 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Waktu</th>
                                    <th class="px-5 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Customer</th>
                                    <th class="px-5 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kode Booking</th>
                                    <th class="px-5 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Durasi</th>
                                    <th class="px-5 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach($item->reservations as $res)
                                @php
                                    $statusStyle = match($res->status) {
                                        'confirmed' => 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10',
                                        'completed' => 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10',
                                        'pending'   => 'text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10',
                                        default     => 'text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-800',
                                    };
                                @endphp
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="px-5 py-3">
                                        <span class="font-mono font-semibold text-slate-800 dark:text-white">
                                            {{ \Carbon\Carbon::parse($res->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($res->end_time)->format('H:i') }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3">
                                        <p class="font-medium text-slate-800 dark:text-white">{{ $res->user->name ?? '-' }}</p>
                                        <p class="text-xs text-slate-400">{{ $res->user->email ?? '' }}</p>
                                    </td>
                                    <td class="px-5 py-3">
                                        <span class="text-xs font-mono font-bold text-pink-600 dark:text-pink-400 bg-pink-50 dark:bg-pink-500/10 px-2 py-1 rounded-lg">{{ $res->booking_code }}</span>
                                    </td>
                                    <td class="px-5 py-3">
                                        <span class="text-slate-600 dark:text-slate-300">{{ $res->duration_hours }} jam</span>
                                    </td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center text-xs font-medium px-2.5 py-1 rounded-lg {{ $statusStyle }}">
                                            {{ $res->status_label }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile Cards --}}
                    <div class="md:hidden divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($item->reservations as $res)
                        @php
                            $statusStyle = match($res->status) {
                                'confirmed' => 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10',
                                'completed' => 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10',
                                'pending'   => 'text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10',
                                default     => 'text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-800',
                            };
                        @endphp
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-mono font-bold text-sm text-slate-800 dark:text-white">
                                    {{ \Carbon\Carbon::parse($res->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($res->end_time)->format('H:i') }}
                                </span>
                                <span class="text-xs font-mono font-bold text-pink-600 dark:text-pink-400 bg-pink-50 dark:bg-pink-500/10 px-2 py-0.5 rounded">{{ $res->booking_code }}</span>
                            </div>
                            <p class="text-sm font-medium text-slate-800 dark:text-white">{{ $res->user->name ?? '-' }}</p>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs text-slate-500">{{ $res->duration_hours }} jam</span>
                                <span class="inline-flex text-xs font-medium px-2 py-0.5 rounded-lg {{ $statusStyle }}">{{ $res->status_label }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 mb-4">
                    <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <p class="text-slate-500 dark:text-slate-400 font-medium">Belum ada data lapangan</p>
            </div>
        @endforelse
    </div>

</x-layouts.app>
