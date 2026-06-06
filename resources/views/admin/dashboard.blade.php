<x-layouts.app :title="$title ?? 'Dashboard Admin'">
    {{-- Welcome Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Selamat Datang, {{ auth()->user()->name }}! 👋</h1>
        <p class="text-slate-500 dark:text-slate-400 mt-1">Ringkasan aktivitas sistem hari ini.</p>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
        {{-- Total Lapangan --}}
        <div class="group relative bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 hover:shadow-lg hover:shadow-slate-200/50 dark:hover:shadow-slate-900/50 transition-all duration-300 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h18v18H3V3zm9 0v18M3 12h18"/></svg>
                    </div>
                    <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 px-2 py-1 rounded-lg">Aktif</span>
                </div>
                <p class="text-3xl font-extrabold text-slate-800 dark:text-white">{{ number_format($totalCourts, 0, ',', '.') }}</p>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Total Lapangan</p>
            </div>
        </div>

        {{-- Reservasi Hari Ini --}}
        <div class="group relative bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 hover:shadow-lg transition-all duration-300 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-pink-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-pink-100 dark:bg-pink-500/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-pink-600 dark:text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <span class="text-xs font-medium text-pink-600 dark:text-pink-400 bg-pink-50 dark:bg-pink-500/10 px-2 py-1 rounded-lg">Hari ini</span>
                </div>
                <p class="text-3xl font-extrabold text-slate-800 dark:text-white">{{ number_format($todayReservations, 0, ',', '.') }}</p>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Reservasi Hari Ini</p>
            </div>
        </div>

        {{-- Total Customer --}}
        <div class="group relative bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 hover:shadow-lg transition-all duration-300 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-violet-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-violet-100 dark:bg-violet-500/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                </div>
                <p class="text-3xl font-extrabold text-slate-800 dark:text-white">{{ number_format($totalCustomers, 0, ',', '.') }}</p>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Total Customer</p>
            </div>
        </div>

        {{-- Pendapatan --}}
        <div class="group relative bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 hover:shadow-lg transition-all duration-300 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 px-2 py-1 rounded-lg">Bulan ini</span>
                </div>
                <p class="text-3xl font-extrabold text-slate-800 dark:text-white">Rp {{ number_format($incomeThisMonth, 0, ',', '.') }}</p>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Pendapatan</p>
            </div>
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Recent Activity --}}
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <h3 class="font-semibold text-slate-800 dark:text-white">Aktivitas Terbaru</h3>
                <span class="text-xs text-slate-400">Terakhir diperbarui: {{ now()->format('H:i') }}</span>
            </div>
            <div class="p-0">
                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($recentActivities as $activity)
                        <div class="px-6 py-4 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-pink-100 dark:bg-pink-500/20 flex items-center justify-center text-pink-600 dark:text-pink-400 font-bold uppercase">
                                    {{ substr($activity->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-800 dark:text-white">{{ $activity->user->name }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Memesan {{ $activity->court->name }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-slate-800 dark:text-white">Rp {{ number_format($activity->total_price, 0, ',', '.') }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-8 text-center px-6">
                            <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            <p class="text-slate-500 dark:text-slate-400 text-sm">Belum ada aktivitas terbaru</p>
                            <p class="text-slate-400 dark:text-slate-500 text-xs mt-1">Aktivitas akan muncul di sini</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Quick Info --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800">
                <h3 class="font-semibold text-slate-800 dark:text-white">Info Cepat</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between py-2">
                    <span class="text-sm text-slate-500 dark:text-slate-400">Tanggal</span>
                    <span class="text-sm font-medium text-slate-800 dark:text-white">{{ now()->translatedFormat('d F Y') }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-t border-slate-100 dark:border-slate-800">
                    <span class="text-sm text-slate-500 dark:text-slate-400">Hari</span>
                    <span class="text-sm font-medium text-slate-800 dark:text-white">{{ now()->translatedFormat('l') }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-t border-slate-100 dark:border-slate-800">
                    <span class="text-sm text-slate-500 dark:text-slate-400">Role</span>
                    <span class="text-xs font-semibold text-pink-600 dark:text-pink-400 bg-pink-50 dark:bg-pink-500/10 px-2.5 py-1 rounded-lg uppercase tracking-wide">Admin</span>
                </div>
                <div class="flex items-center justify-between py-2 border-t border-slate-100 dark:border-slate-800">
                    <span class="text-sm text-slate-500 dark:text-slate-400">Sistem</span>
                    <span class="flex items-center gap-1.5 text-sm font-medium text-emerald-600 dark:text-emerald-400">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Online
                    </span>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
