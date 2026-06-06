<x-layouts.app :title="$title ?? 'Dashboard Kasir'">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Dashboard Kasir 💰</h1>
        <p class="text-slate-500 dark:text-slate-400 mt-1">Kelola transaksi dan pembayaran reservasi.</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-8">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 9v1m9-5a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 px-2.5 py-1 rounded-lg">Hari ini</span>
            </div>
            <p class="text-3xl font-extrabold text-slate-800 dark:text-white">Rp {{ number_format($todayIncome, 0, ',', '.') }}</p>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Pendapatan Hari Ini</p>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 px-2.5 py-1 rounded-lg">Hari ini</span>
            </div>
            <p class="text-3xl font-extrabold text-slate-800 dark:text-white">{{ $todayTransactions }}</p>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Transaksi Hari Ini</p>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                @if($pendingPayments > 0)
                <span class="text-xs font-semibold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10 px-2.5 py-1 rounded-lg animate-pulse">Perlu aksi</span>
                @endif
            </div>
            <p class="text-3xl font-extrabold text-slate-800 dark:text-white">{{ $pendingPayments }}</p>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Menunggu Pembayaran</p>
        </div>
    </div>

    {{-- Today's Reservations --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <h3 class="font-semibold text-slate-800 dark:text-white">Reservasi Hari Ini</h3>
            <span class="text-xs text-slate-400">{{ now()->translatedFormat('l, d F Y') }}</span>
        </div>
        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($todayReservations as $reservation)
            <div class="px-6 py-4 flex items-center justify-between gap-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <div class="flex items-center gap-4 min-w-0">
                    <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gradient-to-br from-[#1e3a5f] to-[#e91e8c] flex items-center justify-center text-white font-semibold text-xs">
                        {{ strtoupper(substr($reservation->user->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-slate-800 dark:text-white truncate">{{ $reservation->user->name ?? 'Tanpa Nama' }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ $reservation->court->name ?? '-' }}
                            • {{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($reservation->end_time)->format('H:i') }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3 flex-shrink-0">
                    <span class="text-sm font-semibold text-slate-800 dark:text-white">Rp {{ number_format($reservation->total_price, 0, ',', '.') }}</span>
                    @php
                        $statusColor = match($reservation->payment->status ?? 'pending') {
                            'paid' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400',
                            'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400',
                            'failed' => 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400',
                            default => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300',
                        };
                        $statusLabel = match($reservation->payment->status ?? 'pending') {
                            'paid' => 'Lunas',
                            'pending' => 'Menunggu',
                            'failed' => 'Gagal',
                            default => '-',
                        };
                    @endphp
                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-md {{ $statusColor }}">{{ $statusLabel }}</span>
                </div>
            </div>
            @empty
            <div class="p-6">
                <div class="flex flex-col items-center justify-center py-8 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <p class="text-slate-500 dark:text-slate-400 text-sm">Belum ada reservasi hari ini</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</x-layouts.app>
