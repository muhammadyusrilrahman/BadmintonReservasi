<x-layouts.app :title="$title ?? 'Laporan'">
    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Laporan 📄</h1>
                <p class="text-slate-500 dark:text-slate-400 mt-1">Export dan pantau seluruh data sistem dalam format tabel.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.reports.export.excel', ['type' => $activeType, 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-xl border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-500/10 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Excel
                </a>
                <a href="{{ route('admin.reports.export.pdf', ['type' => $activeType, 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-xl border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    PDF
                </a>
            </div>
        </div>
    </div>

    {{-- Date Filter --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 mb-8">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-col sm:flex-row items-end gap-3" id="filter-form">
            <input type="hidden" name="type" value="{{ $activeType }}">
            <div class="w-full sm:w-44">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                       class="w-full h-[42px] rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-3">
            </div>
            <div class="w-full sm:w-44">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                       class="w-full h-[42px] rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-3">
            </div>
            <button type="submit"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 h-[42px] px-5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm shadow-blue-600/25">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Terapkan
            </button>
        </form>
    </div>

    {{-- Report Type Tabs + Data Table --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
        {{-- Tabs --}}
        <div class="border-b border-slate-200 dark:border-slate-800 overflow-x-auto">
            <nav class="flex -mb-px px-4" aria-label="Report tabs">
                @foreach($reportTypes as $typeKey => $typeLabel)
                    <a href="{{ route('admin.reports.index', array_merge(request()->only(['start_date', 'end_date']), ['type' => $typeKey])) }}"
                       class="whitespace-nowrap px-4 py-3.5 text-sm font-medium border-b-2 transition-colors {{ $activeType === $typeKey ? 'border-blue-600 text-blue-600 dark:text-blue-400 dark:border-blue-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300' }}">
                        {{ $typeLabel }}
                    </a>
                @endforeach
            </nav>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            @if($reportData->isNotEmpty())
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">No</th>
                        @foreach($headings as $heading)
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ $heading }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($reportData as $index => $row)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $index + 1 }}</td>
                        @foreach($row as $key => $value)
                            <td class="px-4 py-3 {{ in_array($key, ['jumlah', 'total', 'total_belanja']) ? 'text-right tabular-nums font-medium text-slate-800 dark:text-white' : 'text-slate-600 dark:text-slate-300' }}">
                                @if(in_array($key, ['jumlah', 'total', 'total_belanja']))
                                    Rp {{ number_format((int) $value, 0, ',', '.') }}
                                @elseif($key === 'status')
                                    @php
                                        $badgeColor = match(true) {
                                            in_array($value, ['Lunas', 'Selesai']) => 'emerald',
                                            in_array($value, ['Dikonfirmasi', 'Disetujui']) => 'blue',
                                            in_array($value, ['Menunggu', 'Diajukan']) => 'amber',
                                            in_array($value, ['Gagal', 'Dibatalkan', 'Ditolak', 'Dikembalikan']) => 'red',
                                            default => 'slate',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-{{ $badgeColor }}-100 text-{{ $badgeColor }}-700 dark:bg-{{ $badgeColor }}-500/10 dark:text-{{ $badgeColor }}-400">{{ $value }}</span>
                                @elseif($key === 'rating')
                                    <span class="text-amber-500">{{ str_repeat('★', (int) $value) }}{{ str_repeat('☆', 5 - (int) $value) }}</span>
                                @else
                                    {{ $value }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <p class="text-slate-500 dark:text-slate-400 font-medium">Tidak ada data</p>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Coba ubah filter tanggal untuk melihat data.</p>
            </div>
            @endif
        </div>

        {{-- Table Footer --}}
        @if($reportData->isNotEmpty())
        <div class="px-4 py-3 bg-slate-50 dark:bg-slate-800/30 border-t border-slate-200 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400">
            Menampilkan {{ $reportData->count() }} data &bull; Periode {{ $startDate->format('d/m/Y') }} — {{ $endDate->format('d/m/Y') }}
        </div>
        @endif
    </div>
</x-layouts.app>
