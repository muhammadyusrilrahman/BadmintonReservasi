<x-layouts.app :title="$title ?? 'Dashboard Staff'">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Dashboard Staff 🔧</h1>
        <p class="text-slate-500 dark:text-slate-400 mt-1">Lihat jadwal lapangan dan status hari ini.</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-8">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 px-2.5 py-1 rounded-lg">Aktif</span>
            </div>
            <p class="text-3xl font-extrabold text-slate-800 dark:text-white">{{ $activeCourts }}</p>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Lapangan Tersedia</p>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                @if($maintenancePending > 0)
                <span class="text-xs font-semibold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10 px-2.5 py-1 rounded-lg animate-pulse">Perhatian</span>
                @endif
            </div>
            <p class="text-3xl font-extrabold text-slate-800 dark:text-white">{{ $maintenancePending }}</p>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Lapangan Tidak Aktif</p>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-pink-100 dark:bg-pink-500/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-pink-600 dark:text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <span class="text-xs font-semibold text-pink-600 dark:text-pink-400 bg-pink-50 dark:bg-pink-500/10 px-2.5 py-1 rounded-lg">Hari ini</span>
            </div>
            <p class="text-3xl font-extrabold text-slate-800 dark:text-white">{{ $todaySchedule }}</p>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Jadwal Hari Ini</p>
        </div>
    </div>

    {{-- Today's Schedule by Court --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <h3 class="font-semibold text-slate-800 dark:text-white">Jadwal Lapangan Hari Ini</h3>
            <span class="text-xs text-slate-400">{{ now()->translatedFormat('l, d F Y') }}</span>
        </div>

        @if($todayReservations->isEmpty())
        <div class="p-6">
            <div class="flex flex-col items-center justify-center py-8 text-center">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <p class="text-slate-500 dark:text-slate-400 text-sm">Belum ada jadwal hari ini</p>
            </div>
        </div>
        @else
        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            @foreach($courts as $court)
                @php $courtReservations = $todayReservations->get($court->id, collect()); @endphp
                <div class="px-6 py-4">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-[#1e3a5f] to-[#2a4a73] flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800 dark:text-white">{{ $court->name }}</p>
                            <p class="text-xs text-slate-400">{{ $courtReservations->count() }} reservasi</p>
                        </div>
                    </div>

                    @if($courtReservations->isNotEmpty())
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 ml-11">
                        @foreach($courtReservations as $res)
                        @php
                            $statusStyle = match($res->status) {
                                'confirmed' => 'bg-emerald-50 border-emerald-200 dark:bg-emerald-500/10 dark:border-emerald-500/30',
                                'pending' => 'bg-amber-50 border-amber-200 dark:bg-amber-500/10 dark:border-amber-500/30',
                                default => 'bg-slate-50 border-slate-200 dark:bg-slate-800 dark:border-slate-700',
                            };
                        @endphp
                        <div class="rounded-lg border p-2.5 {{ $statusStyle }}">
                            <p class="text-xs font-bold text-slate-800 dark:text-white">
                                {{ \Carbon\Carbon::parse($res->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($res->end_time)->format('H:i') }}
                            </p>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">{{ $res->user->name ?? '-' }}</p>
                            <p class="text-[10px] font-medium mt-0.5 {{ $res->status === 'confirmed' ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                                {{ $res->status === 'confirmed' ? 'Dikonfirmasi' : 'Menunggu' }}
                            </p>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-xs text-slate-400 ml-11">Tidak ada reservasi</p>
                    @endif
                </div>
            @endforeach
        </div>
        @endif
    </div>
</x-layouts.app>
