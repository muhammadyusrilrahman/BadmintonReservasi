<x-layouts.app :title="$title">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $title }} 💳</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Pantau semua riwayat transaksi pembayaran.</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 mb-6">
        <form method="GET" action="{{ route('kasir.transactions.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Pencarian</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Nama / Kode Booking..."
                           class="w-full pl-9 pr-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-colors">
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Status</label>
                <select name="status" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-colors">
                    <option value="">Semua Status</option>
                    @foreach(\App\Models\Payment::STATUS_LABELS as $val => $label)
                        <option value="{{ $val }}" {{ $status === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}"
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-colors">
            </div>

            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}"
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-colors">
                </div>
                <button type="submit" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 dark:bg-white dark:hover:bg-slate-100 dark:text-slate-900 text-white text-sm font-semibold rounded-xl transition-colors shrink-0">
                    Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Transactions Table --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
        @if($transactions->isEmpty())
            <div class="p-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 mb-4">
                    <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <p class="text-slate-500 dark:text-slate-400 font-medium">Tidak ada transaksi yang ditemukan</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Waktu</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Customer & Kode</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Metode</th>
                            <th class="px-5 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">Nominal</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($transactions as $trx)
                        @php
                            $statusStyle = match($trx->status) {
                                'paid' => 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10',
                                'pending' => 'text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10',
                                'failed' => 'text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-500/10',
                                'refunded' => 'text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800',
                                default => 'text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-800',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-5 py-4">
                                <p class="text-slate-800 dark:text-white font-medium">{{ $trx->created_at->format('d M Y') }}</p>
                                <p class="text-xs text-slate-400">{{ $trx->created_at->format('H:i') }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-medium text-slate-800 dark:text-white">{{ $trx->reservation->user->name ?? '-' }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-xs font-mono font-bold text-pink-600 dark:text-pink-400 bg-pink-50 dark:bg-pink-500/10 px-1.5 py-0.5 rounded">{{ $trx->reservation->booking_code ?? '-' }}</span>
                                    <span class="text-xs text-slate-500">{{ $trx->reservation->court->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-slate-600 dark:text-slate-300">{{ $trx->method_label }}</span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <p class="font-bold text-slate-800 dark:text-white">{{ $trx->formatted_amount }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center text-xs font-medium px-2.5 py-1 rounded-lg {{ $statusStyle }}">
                                    {{ $trx->status_label }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($transactions->hasPages())
                <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800">
                    {{ $transactions->links() }}
                </div>
            @endif
        @endif
    </div>

</x-layouts.app>
