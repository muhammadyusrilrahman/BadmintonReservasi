<x-layouts.app :title="$title ?? 'Dashboard Staff'">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Dashboard Staff 🔧</h1>
        <p class="text-slate-500 dark:text-slate-400 mt-1">Lihat jadwal lapangan dan status hari ini.</p>
    </div>

    {{-- Schedule Section (TOP) --}}
    <x-schedule-grid
        :courts="$courts"
        :schedule-date="$scheduleDate"
        :operational-hours="$operationalHours"
        dashboard-route="staff.dashboard"
        :show-user-name="true"
    />

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
</x-layouts.app>
