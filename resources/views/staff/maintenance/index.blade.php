<x-layouts.app :title="$title">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $title }} 🔧</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola jadwal maintenance dan perawatan lapangan.</p>
        </div>
        <a href="{{ route('staff.maintenance.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-[#1e3a5f] to-[#e91e8c] text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:shadow-pink-500/25 transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Jadwal Baru
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $scheduledCount }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Dijadwalkan</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $inProgressCount }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Sedang Berjalan</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $completedCount }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Selesai</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-6">
        {{-- Status Filter --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-1">
            <a href="{{ route('staff.maintenance.index', request()->except('status', 'page')) }}"
               class="px-4 py-2.5 text-sm font-medium rounded-xl whitespace-nowrap transition-colors {{ !$selectedStatus ? 'bg-gradient-to-r from-[#1e3a5f] to-[#e91e8c] text-white shadow-sm' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                Semua
            </a>
            @foreach(\App\Models\CourtMaintenance::STATUS_LABELS as $value => $label)
                <a href="{{ route('staff.maintenance.index', array_merge(request()->except('status', 'page'), ['status' => $value])) }}"
                   class="px-4 py-2.5 text-sm font-medium rounded-xl whitespace-nowrap transition-colors {{ $selectedStatus === $value ? 'bg-gradient-to-r from-[#1e3a5f] to-[#e91e8c] text-white shadow-sm' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Court Filter --}}
        <div class="sm:ml-auto">
            <form method="GET" action="{{ route('staff.maintenance.index') }}">
                @if($selectedStatus)
                    <input type="hidden" name="status" value="{{ $selectedStatus }}">
                @endif
                <select name="court_id" onchange="this.form.submit()"
                        class="px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-colors">
                    <option value="">Semua Lapangan</option>
                    @foreach($courts as $court)
                        <option value="{{ $court->id }}" {{ $selectedCourt == $court->id ? 'selected' : '' }}>{{ $court->name }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    {{-- Maintenance List --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
        @if($maintenances->isEmpty())
            <div class="p-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 mb-4">
                    <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <p class="text-slate-500 dark:text-slate-400 font-medium">Belum ada data maintenance</p>
                <p class="text-xs text-slate-400 mt-1">Buat jadwal maintenance baru untuk memulai.</p>
            </div>
        @else
            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Judul</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Lapangan</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Dijadwalkan</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">PIC</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Status</th>
                            <th class="px-5 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($maintenances as $m)
                        @php
                            $color = $m->status_color;
                            $statusBadge = match($color) {
                                'amber' => 'text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10',
                                'blue'  => 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10',
                                'emerald' => 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10',
                                default => 'text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-800',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-5 py-4">
                                <a href="{{ route('staff.maintenance.show', $m) }}" class="font-medium text-slate-800 dark:text-white hover:text-pink-600 dark:hover:text-pink-400 transition-colors">{{ $m->title }}</a>
                                @if($m->description)
                                    <p class="text-xs text-slate-400 mt-0.5 truncate max-w-xs">{{ Str::limit($m->description, 50) }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-slate-600 dark:text-slate-300">{{ $m->court->name ?? '-' }}</td>
                            <td class="px-5 py-4">
                                <span class="text-slate-600 dark:text-slate-300">{{ $m->scheduled_date->format('d M Y') }}</span>
                                @if($m->scheduled_date->isToday())
                                    <span class="ml-1 text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold">Hari ini</span>
                                @elseif($m->scheduled_date->isPast() && $m->status !== 'completed')
                                    <span class="ml-1 text-[10px] text-red-600 dark:text-red-400 font-semibold">Terlambat</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-slate-600 dark:text-slate-300">{{ $m->staff->name ?? '-' }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center text-xs font-medium px-2.5 py-1 rounded-lg {{ $statusBadge }}">{{ $m->status_label }}</span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('staff.maintenance.show', $m) }}"
                                       class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    @if($m->status === 'scheduled')
                                        <form method="POST" action="{{ route('staff.maintenance.update-status', $m) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="in_progress">
                                            <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-500/20 transition-colors"
                                                    onclick="return confirm('Mulai proses maintenance ini?')">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>
                                                Mulai
                                            </button>
                                        </form>
                                    @elseif($m->status === 'in_progress')
                                        <form method="POST" action="{{ route('staff.maintenance.update-status', $m) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 rounded-lg hover:bg-emerald-100 dark:hover:bg-emerald-500/20 transition-colors"
                                                    onclick="return confirm('Tandai maintenance ini sebagai selesai?')">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Selesai
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden divide-y divide-slate-100 dark:divide-slate-800">
                @foreach($maintenances as $m)
                @php
                    $color = $m->status_color;
                    $statusBadge = match($color) {
                        'amber' => 'text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10',
                        'blue'  => 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10',
                        'emerald' => 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10',
                        default => 'text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-800',
                    };
                @endphp
                <div class="p-4">
                    <div class="flex items-start justify-between mb-2">
                        <a href="{{ route('staff.maintenance.show', $m) }}" class="font-semibold text-sm text-slate-800 dark:text-white hover:text-pink-600">{{ $m->title }}</a>
                        <span class="inline-flex text-xs font-medium px-2 py-0.5 rounded-lg {{ $statusBadge }} ml-2 flex-shrink-0">{{ $m->status_label }}</span>
                    </div>
                    <p class="text-xs text-slate-500">{{ $m->court->name ?? '-' }} · {{ $m->scheduled_date->format('d M Y') }}</p>
                    <div class="flex items-center justify-between mt-3">
                        <span class="text-xs text-slate-400">PIC: {{ $m->staff->name ?? '-' }}</span>
                        <div class="flex gap-2">
                            @if($m->status === 'scheduled')
                                <form method="POST" action="{{ route('staff.maintenance.update-status', $m) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="in_progress">
                                    <button type="submit" class="text-xs font-semibold text-blue-600 dark:text-blue-400" onclick="return confirm('Mulai?')">Mulai →</button>
                                </form>
                            @elseif($m->status === 'in_progress')
                                <form method="POST" action="{{ route('staff.maintenance.update-status', $m) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400" onclick="return confirm('Selesai?')">Selesai ✓</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($maintenances->hasPages())
                <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800">
                    {{ $maintenances->links() }}
                </div>
            @endif
        @endif
    </div>

</x-layouts.app>
