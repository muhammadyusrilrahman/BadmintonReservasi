<x-layouts.app :title="$title">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Kelola Reservasi</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Daftar semua pesanan lapangan dan status pembayarannya.</p>
        </div>
        <a href="{{ route('admin.reservations.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-[#1e3a5f] to-[#e91e8c] text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:shadow-pink-500/25 hover:-translate-y-0.5 transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Pesanan Manual
        </a>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('admin.reservations.index') }}"
          class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            {{-- Search --}}
            <div class="lg:col-span-2 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama atau email..."
                       class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-pink-500 transition">
            </div>
            {{-- Date Filter --}}
            <div>
                <input type="date" name="date" value="{{ request('date') }}"
                       class="w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-pink-500 transition">
            </div>
            {{-- Status Filter --}}
            <div>
                <select name="status"
                        class="w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-pink-500 transition">
                    <option value="">Semua Status</option>
                    @foreach(\App\Models\Reservation::STATUS_LABELS as $val => $label)
                        <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            {{-- Court Filter & Submit --}}
            <div class="flex gap-2">
                <select name="court_id"
                        class="flex-1 px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-pink-500 transition">
                    <option value="">Semua Lapangan</option>
                    @foreach($courts as $court)
                        <option value="{{ $court->id }}" @selected(request('court_id') == $court->id)>{{ $court->name }}</option>
                    @endforeach
                </select>
                <button type="submit"
                        class="px-4 py-2.5 bg-[#1e3a5f] text-white text-sm font-medium rounded-xl hover:bg-[#162d4a] transition-colors" title="Filter">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                </button>
            </div>
        </div>
        @if(request()->hasAny(['search','date','status','court_id']))
            <div class="mt-3">
                <a href="{{ route('admin.reservations.index') }}"
                   class="text-xs text-pink-600 dark:text-pink-400 hover:underline">Hapus Filter</a>
            </div>
        @endif
    </form>

    {{-- Table Card --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
        {{-- Table --}}
        @if($reservations->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="text-slate-500 dark:text-slate-400 font-medium">Tidak ada reservasi ditemukan</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pelanggan</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jadwal & Lapangan</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Harga</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3.5 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($reservations as $reservation)
                            <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400 font-medium">
                                    #{{ $reservation->id }}
                                </td>
                                
                                {{-- Customer --}}
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-slate-800 dark:text-white text-sm">{{ $reservation->user->name }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $reservation->user->phone ?? $reservation->user->email }}</p>
                                </td>

                                {{-- Schedule --}}
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-slate-800 dark:text-white">{{ $reservation->date->format('d M Y') }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                        {{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($reservation->end_time)->format('H:i') }} 
                                        &bull; <span class="font-medium">{{ $reservation->court->name }}</span>
                                    </p>
                                </td>

                                {{-- Price --}}
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-slate-800 dark:text-white">{{ $reservation->formatted_total_price }}</p>
                                    @if($reservation->payment)
                                        <p class="text-[10px] uppercase font-bold mt-0.5
                                            {{ $reservation->payment->status === 'paid' ? 'text-emerald-500' : ($reservation->payment->status === 'failed' ? 'text-red-500' : 'text-amber-500') }}">
                                            {{ $reservation->payment->status_label }}
                                        </p>
                                    @else
                                        <p class="text-[10px] uppercase font-bold text-slate-400 mt-0.5">Belum Bayar</p>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium
                                        bg-{{ $reservation->status_color }}-50 dark:bg-{{ $reservation->status_color }}-500/10
                                        text-{{ $reservation->status_color }}-700 dark:text-{{ $reservation->status_color }}-400">
                                        {{ $reservation->status_label }}
                                    </span>
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.reservations.show', $reservation) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-500/20 transition-colors">
                                        Detail
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($reservations->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">
                    {{ $reservations->links() }}
                </div>
            @endif
        @endif
    </div>

</x-layouts.app>
