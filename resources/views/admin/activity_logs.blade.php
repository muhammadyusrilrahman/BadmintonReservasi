<x-layouts.app :title="$title ?? 'Log Aktivitas'">
    <div x-data="{ selectedLog: null, showModal: false }" class="max-w-7xl mx-auto">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <svg class="w-7 h-7 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h10l4 4v10a2 2 0 01-2 2z"/></svg>
                Log Aktivitas Pengguna
            </h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1">Audit log terperinci mengenai login, logout, transaksi, dan perubahan data di dalam sistem.</p>
        </div>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
            {{-- Total Aktivitas Hari Ini --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm hover:shadow-md transition duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <span class="text-[10px] font-semibold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 px-2 py-1 rounded-lg">Hari Ini</span>
                </div>
                <p class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ number_format($todayCount) }}</p>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Aktivitas Hari Ini</p>
            </div>

            {{-- Pengguna Aktif --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm hover:shadow-md transition duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <span class="text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 px-2 py-1 rounded-lg font-mono">Unique Users</span>
                </div>
                <p class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ number_format($uniqueUsersToday) }}</p>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">User Aktif Hari Ini</p>
            </div>

            {{-- Perubahan Data / CRUD --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm hover:shadow-md transition duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <span class="text-[10px] font-semibold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10 px-2 py-1 rounded-lg">State Changes</span>
                </div>
                <p class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ number_format($crudCount) }}</p>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Aktivitas CRUD Hari Ini</p>
            </div>

            {{-- Autentikasi --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm hover:shadow-md transition duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl bg-pink-100 dark:bg-pink-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-pink-600 dark:text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    </div>
                    <span class="text-[10px] font-semibold text-pink-600 dark:text-pink-400 bg-pink-50 dark:bg-pink-500/10 px-2 py-1 rounded-lg font-mono">Auth Events</span>
                </div>
                <p class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ number_format($authCount) }}</p>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Aktivitas Auth Hari Ini</p>
            </div>
        </div>

        {{-- Interactive Filter Bar --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 sm:p-5 mb-6 shadow-sm">
            <form action="{{ route('admin.activity-logs.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Search Input --}}
                <div class="flex flex-col">
                    <label for="search" class="text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Cari Keyword</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="User, IP, atau deskripsi..." class="w-full pl-9 pr-4 py-2 text-sm bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-200">
                    </div>
                </div>

                {{-- HTTP Method Filter --}}
                <div class="flex flex-col">
                    <label for="method" class="text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Metode HTTP</label>
                    <select name="method" id="method" class="w-full px-3 py-2 text-sm bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-200">
                        <option value="">Semua Metode</option>
                        <option value="POST" {{ request('method') === 'POST' ? 'selected' : '' }}>POST (Simpan)</option>
                        <option value="PUT" {{ request('method') === 'PUT' ? 'selected' : '' }}>PUT (Update)</option>
                        <option value="PATCH" {{ request('method') === 'PATCH' ? 'selected' : '' }}>PATCH (Update)</option>
                        <option value="DELETE" {{ request('method') === 'DELETE' ? 'selected' : '' }}>DELETE (Hapus)</option>
                        <option value="GET" {{ request('method') === 'GET' ? 'selected' : '' }}>GET (Akses)</option>
                    </select>
                </div>

                {{-- Date Filter --}}
                <div class="flex flex-col">
                    <label for="date" class="text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Tanggal</label>
                    <input type="date" name="date" id="date" value="{{ request('date') }}" class="w-full px-3 py-2 text-sm bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-200">
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 py-2 bg-indigo-650 hover:bg-indigo-755 text-white text-sm font-semibold rounded-xl transition duration-200 shadow-sm shadow-indigo-600/10 flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Filter
                    </button>
                    @if(request()->anyFilled(['search', 'method', 'date']))
                        <a href="{{ route('admin.activity-logs.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-650 dark:text-slate-350 text-sm font-semibold rounded-xl transition duration-200 flex items-center justify-center" title="Reset Filter">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89M9 11l3-3m0 0l3 3m-3-3v8"/></svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Logs Table --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/40 text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider">
                            <th class="py-4 px-6">Tanggal & Jam</th>
                            <th class="py-4 px-6">User / Pelaku</th>
                            <th class="py-4 px-6">Aktivitas</th>
                            <th class="py-4 px-6 text-center">HTTP Method</th>
                            <th class="py-4 px-6">IP Address</th>
                            <th class="py-4 px-6 text-center">Payload</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 text-sm">
                        @forelse($logs as $log)
                            @php
                                $methodColors = [
                                    'POST' => 'bg-emerald-50 text-emerald-650 border-emerald-100 dark:bg-emerald-950/40 dark:text-emerald-450 dark:border-emerald-900/30',
                                    'PUT' => 'bg-amber-50 text-amber-650 border-amber-100 dark:bg-amber-950/40 dark:text-amber-450 dark:border-amber-900/30',
                                    'PATCH' => 'bg-amber-50 text-amber-650 border-amber-100 dark:bg-amber-950/40 dark:text-amber-450 dark:border-amber-900/30',
                                    'DELETE' => 'bg-rose-50 text-rose-650 border-rose-100 dark:bg-rose-950/40 dark:text-rose-455 dark:border-rose-900/30',
                                    'GET' => 'bg-blue-50 text-blue-650 border-blue-100 dark:bg-blue-950/40 dark:text-blue-450 dark:border-blue-900/30',
                                ];
                                $colorClass = $methodColors[$log->method] ?? 'bg-slate-50 text-slate-600 border-slate-100 dark:bg-slate-850 dark:text-slate-400 dark:border-slate-800';
                            @endphp
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/20 transition-colors">
                                <td class="py-4 px-6 whitespace-nowrap text-xs text-slate-500 dark:text-slate-400">
                                    <div class="font-medium text-slate-700 dark:text-slate-300">
                                        {{ $log->created_at->translatedFormat('d M Y') }}
                                    </div>
                                    <div class="text-[10px] mt-0.5">{{ $log->created_at->format('H:i:s') }} ({{ $log->created_at->diffForHumans() }})</div>
                                </td>
                                <td class="py-4 px-6">
                                    @if($log->user)
                                        <div class="flex items-center gap-3">
                                            <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-650 flex items-center justify-center text-white font-semibold text-xs shadow-inner">
                                                {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-semibold text-slate-850 dark:text-slate-200 truncate">{{ $log->user->name }}</p>
                                                <p class="text-[10px] text-slate-500 truncate mt-0.5">
                                                    @if($log->user->hasRole('admin'))
                                                        <span class="inline-flex px-1.5 py-0.5 rounded text-[8px] font-bold bg-pink-50 text-pink-600 dark:bg-pink-950/30 dark:text-pink-400 uppercase tracking-wider">Admin</span>
                                                    @elseif($log->user->hasRole('staff'))
                                                        <span class="inline-flex px-1.5 py-0.5 rounded text-[8px] font-bold bg-violet-50 text-violet-600 dark:bg-violet-950/30 dark:text-violet-400 uppercase tracking-wider">Staff</span>
                                                    @elseif($log->user->hasRole('kasir'))
                                                        <span class="inline-flex px-1.5 py-0.5 rounded text-[8px] font-bold bg-teal-50 text-teal-600 dark:bg-teal-950/30 dark:text-teal-400 uppercase tracking-wider">Kasir</span>
                                                    @else
                                                        <span class="inline-flex px-1.5 py-0.5 rounded text-[8px] font-bold bg-blue-50 text-blue-600 dark:bg-blue-950/30 dark:text-blue-400 uppercase tracking-wider">Customer</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2 text-slate-400 dark:text-slate-550">
                                            <span class="text-xs italic">System / Visitor</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    <div class="font-medium text-slate-850 dark:text-slate-250 leading-normal">{{ $log->activity }}</div>
                                    <div class="text-[10px] text-slate-400 dark:text-slate-500 truncate max-w-xs mt-0.5" title="{{ $log->url }}">{{ $log->url }}</div>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-bold rounded-lg border {{ $colorClass }} font-mono">
                                        {{ $log->method }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 font-mono text-xs text-slate-600 dark:text-slate-400">
                                    {{ $log->ip_address }}
                                </td>
                                <td class="py-4 px-6 text-center font-semibold">
                                    @if($log->properties && count($log->properties) > 0)
                                        <button @click="selectedLog = {{ json_encode($log) }}; showModal = true" class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-105 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700/80 border border-slate-200 dark:border-slate-800 text-slate-750 dark:text-slate-300 text-xs font-semibold rounded-lg transition duration-200 shadow-inner">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Payload Info
                                        </button>
                                    @else
                                        <span class="text-xs text-slate-400 dark:text-slate-650 italic">Kosong</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto text-slate-400 dark:text-slate-500 mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Tidak ada log aktivitas</p>
                                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Coba sesuaikan filter pencarian atau rentang tanggal Anda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/40">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>

        {{-- Detail Log JSON Modal (Alpine.js) --}}
        <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak x-transition.opacity>
            <div @click.away="showModal = false" class="w-full max-w-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl flex flex-col overflow-hidden max-h-[85vh]">
                {{-- Modal Header --}}
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-850 dark:text-white text-base">Detail Payload Aktivitas</h3>
                        <p class="text-xs text-slate-400 mt-0.5" x-text="selectedLog ? selectedLog.activity : ''"></p>
                    </div>
                    <button @click="showModal = false" class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-700 dark:hover:text-white transition duration-150">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-6 overflow-y-auto space-y-4">
                    {{-- General Info Grid --}}
                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div class="bg-slate-50 dark:bg-slate-950/50 p-3 rounded-xl border border-slate-150 dark:border-slate-800/80">
                            <span class="block font-semibold text-slate-400 dark:text-slate-500 mb-1">HTTP METHOD</span>
                            <span class="font-mono font-bold text-sm text-indigo-600 dark:text-indigo-400" x-text="selectedLog ? selectedLog.method : ''"></span>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-950/50 p-3 rounded-xl border border-slate-150 dark:border-slate-800/80">
                            <span class="block font-semibold text-slate-400 dark:text-slate-500 mb-1">IP ADDRESS</span>
                            <span class="font-mono font-bold text-sm text-indigo-600 dark:text-indigo-400" x-text="selectedLog ? selectedLog.ip_address : ''"></span>
                        </div>
                    </div>

                    {{-- User Agent --}}
                    <div class="text-xs bg-slate-50 dark:bg-slate-950/50 p-3 rounded-xl border border-slate-150 dark:border-slate-800/80">
                        <span class="block font-semibold text-slate-400 dark:text-slate-500 mb-1">USER AGENT (DEVICE)</span>
                        <p class="font-mono text-slate-650 dark:text-slate-400" x-text="selectedLog ? selectedLog.user_agent : '-'"></p>
                    </div>

                    {{-- Payload Properties JSON Viewer --}}
                    <div>
                        <span class="block text-xs font-semibold text-slate-400 dark:text-slate-500 mb-2">PROPERTIES & REQUEST PAYLOAD</span>
                        <div class="bg-slate-950 text-slate-300 p-4 rounded-xl border border-slate-850 overflow-x-auto max-h-72">
                            <pre class="font-mono text-xs text-emerald-450 leading-relaxed"><code x-text="selectedLog ? JSON.stringify(selectedLog.properties, null, 4) : '{}'"></code></pre>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/20 border-t border-slate-200 dark:border-slate-800 flex justify-end">
                    <button @click="showModal = false" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-350 text-xs font-semibold rounded-lg transition duration-200">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
