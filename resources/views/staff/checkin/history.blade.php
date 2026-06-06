<x-layouts.app :title="$title">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $title }}</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Daftar customer yang sudah melakukan check-in</p>
        </div>
        <a href="{{ route('staff.checkin.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-[#1e3a5f] to-[#e91e8c] text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:shadow-pink-500/25 transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Check-in Hari Ini
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 mb-6">
        <form action="{{ route('staff.checkin.history') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <label class="text-xs font-medium text-slate-600 dark:text-slate-400 mb-1 block">Tanggal</label>
                <input type="date" name="date" value="{{ $selectedDate }}"
                       class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-white focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500">
            </div>
            <div class="flex-1">
                <label class="text-xs font-medium text-slate-600 dark:text-slate-400 mb-1 block">Lapangan</label>
                <select name="court_id"
                        class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-white focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500">
                    <option value="">Semua Lapangan</option>
                    @foreach($courts as $court)
                        <option value="{{ $court->id }}" {{ $selectedCourt == $court->id ? 'selected' : '' }}>{{ $court->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit"
                        class="px-6 py-2 bg-slate-800 dark:bg-slate-700 text-white text-sm font-medium rounded-xl hover:bg-slate-700 dark:hover:bg-slate-600 transition-colors">
                    Filter
                </button>
            </div>
        </form>
    </div>

    {{-- History Table --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
        @if($history->isEmpty())
            <div class="p-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 mb-4">
                    <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-slate-500 dark:text-slate-400 font-medium">Belum ada riwayat check-in</p>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Coba ubah filter tanggal atau lapangan</p>
            </div>
        @else
            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Waktu Check-in</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Kode</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Customer</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Lapangan</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Jam Main</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Staff</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($history as $item)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-5 py-4">
                                <span class="text-slate-800 dark:text-white font-medium">{{ $item->checked_in_at->format('d/m/Y') }}</span>
                                <span class="text-slate-500 ml-1">{{ $item->checked_in_at->format('H:i') }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-xs font-mono font-bold text-pink-600 dark:text-pink-400 bg-pink-50 dark:bg-pink-500/10 px-2 py-1 rounded-lg">{{ $item->booking_code }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-medium text-slate-800 dark:text-white">{{ $item->user->name }}</p>
                            </td>
                            <td class="px-5 py-4 text-slate-700 dark:text-slate-300">{{ $item->court->name }}</td>
                            <td class="px-5 py-4">
                                <span class="font-mono text-slate-700 dark:text-slate-300">{{ substr($item->start_time, 0, 5) }} - {{ substr($item->end_time, 0, 5) }}</span>
                            </td>
                            <td class="px-5 py-4 text-slate-700 dark:text-slate-300">{{ $item->checkedInBy->name ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden divide-y divide-slate-100 dark:divide-slate-800">
                @foreach($history as $item)
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-mono font-bold text-pink-600 dark:text-pink-400 bg-pink-50 dark:bg-pink-500/10 px-2 py-0.5 rounded">{{ $item->booking_code }}</span>
                        <span class="text-xs text-slate-500">{{ $item->checked_in_at->format('d/m H:i') }}</span>
                    </div>
                    <p class="text-sm font-medium text-slate-800 dark:text-white">{{ $item->user->name }}</p>
                    <p class="text-xs text-slate-500">{{ $item->court->name }} · {{ substr($item->start_time, 0, 5) }} - {{ substr($item->end_time, 0, 5) }}</p>
                    <p class="text-xs text-slate-400 mt-1">Staff: {{ $item->checkedInBy->name ?? '-' }}</p>
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800">
                {{ $history->links() }}
            </div>
        @endif
    </div>

</x-layouts.app>
