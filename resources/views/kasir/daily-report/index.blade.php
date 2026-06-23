<x-layouts.app :title="$title">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $title }} 📊</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Rekapitulasi pendapatan untuk tanggal {{ $date->format('d F Y') }}</p>
        </div>
        
        <form method="GET" action="{{ route('kasir.daily-report.index') }}" class="flex items-center gap-2">
            <input type="date" name="date" value="{{ $date->format('Y-m-d') }}"
                   class="px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-colors"
                   onchange="this.form.submit()">
            <noscript><button type="submit" class="px-3 py-2 bg-slate-800 text-white rounded-xl text-sm">Pilih</button></noscript>
        </form>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-6 text-white shadow-lg shadow-emerald-500/20">
            <p class="text-emerald-100 font-medium mb-1">Total Pendapatan</p>
            <h2 class="text-4xl font-bold tracking-tight">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h2>
            <p class="text-sm text-emerald-100 mt-2">Dari {{ $totalPaidTransactions }} transaksi lunas hari ini.</p>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 flex flex-col justify-center">
            <p class="text-slate-500 dark:text-slate-400 font-medium mb-4 text-sm uppercase tracking-wider">Pendapatan per Metode Pembayaran</p>
            
            <div class="space-y-3">
                @forelse($revenueByMethod as $method => $amount)
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">
                            {{ \App\Models\Payment::METHOD_LABELS[$method] ?? ucfirst($method) }}
                        </span>
                        <span class="text-sm font-bold text-slate-800 dark:text-white">Rp {{ number_format($amount, 0, ',', '.') }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-400 italic">Belum ada pendapatan dari metode apapun.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Transactions Table --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50">
            <h3 class="text-base font-bold text-slate-800 dark:text-white">Rincian Transaksi Lunas</h3>
        </div>

        @if($transactions->isEmpty())
            <div class="p-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 mb-4">
                    <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <p class="text-slate-500 dark:text-slate-400 font-medium">Tidak ada transaksi lunas pada tanggal ini</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Waktu Bayar</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Customer</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Kode Booking</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Metode</th>
                            <th class="px-5 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($transactions as $trx)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-5 py-4">
                                <span class="font-medium text-slate-800 dark:text-white">{{ $trx->paid_at->format('H:i:s') }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-medium text-slate-800 dark:text-white">{{ $trx->reservation->user->name ?? '-' }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-mono text-pink-600 dark:text-pink-400">{{ $trx->reservation->booking_code }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-slate-600 dark:text-slate-300">{{ $trx->method_label }}</span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <span class="font-bold text-slate-800 dark:text-white">{{ $trx->formatted_amount }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</x-layouts.app>
