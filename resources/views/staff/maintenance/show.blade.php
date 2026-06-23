<x-layouts.app :title="$title">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $title }}</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Detail jadwal maintenance lapangan.</p>
        </div>
        <a href="{{ route('staff.maintenance.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
    </div>

    @php
        $color = $maintenance->status_color;
        $statusBadge = match($color) {
            'amber' => 'text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10',
            'blue'  => 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10',
            'emerald' => 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10',
            default => 'text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-800',
        };
    @endphp

    <div class="max-w-3xl">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            {{-- Header Detail --}}
            <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex items-start justify-between bg-slate-50/50 dark:bg-slate-800/50">
                <div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">{{ $maintenance->title }}</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Dibuat pada {{ $maintenance->created_at->format('d M Y H:i') }}</p>
                </div>
                <span class="inline-flex text-sm font-bold px-3 py-1 rounded-lg {{ $statusBadge }}">{{ $maintenance->status_label }}</span>
            </div>

            {{-- Detail Content --}}
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Lapangan</p>
                        <p class="text-sm font-medium text-slate-800 dark:text-white">{{ $maintenance->court->name ?? '-' }} ({{ $maintenance->court->type_label ?? '-' }})</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Tanggal Dijadwalkan</p>
                        <p class="text-sm font-medium text-slate-800 dark:text-white">{{ $maintenance->scheduled_date->format('l, d F Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Dibuat Oleh (PIC)</p>
                        <p class="text-sm font-medium text-slate-800 dark:text-white">{{ $maintenance->staff->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Waktu Selesai</p>
                        <p class="text-sm font-medium text-slate-800 dark:text-white">{{ $maintenance->completed_at ? $maintenance->completed_at->format('d M Y H:i') : '-' }}</p>
                    </div>
                </div>

                <hr class="border-slate-200 dark:border-slate-800">

                <div>
                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Deskripsi Maintenance</p>
                    @if($maintenance->description)
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl text-sm text-slate-700 dark:text-slate-300">
                            {{ $maintenance->description }}
                        </div>
                    @else
                        <p class="text-sm text-slate-500 italic">Tidak ada deskripsi.</p>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            @if($maintenance->status !== 'completed')
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3">
                    @if($maintenance->status === 'scheduled')
                        <form method="POST" action="{{ route('staff.maintenance.update-status', $maintenance) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="in_progress">
                            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-colors"
                                    onclick="return confirm('Apakah Anda yakin ingin memulai maintenance ini sekarang?')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>
                                Mulai Pengerjaan
                            </button>
                        </form>
                    @elseif($maintenance->status === 'in_progress')
                        <form method="POST" action="{{ route('staff.maintenance.update-status', $maintenance) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-colors"
                                    onclick="return confirm('Apakah Anda yakin ingin menandai maintenance ini telah selesai?')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Tandai Selesai
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        </div>
    </div>

</x-layouts.app>
