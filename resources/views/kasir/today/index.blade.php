<x-layouts.app :title="$title">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $title }} 📅</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ \Carbon\Carbon::today()->format('l, d F Y') }}</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $totalReservations }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Total Reservasi</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $totalPaid }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Sudah Lunas</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $totalPending }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Belum Dibayar</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-6">
        <form method="GET" action="{{ route('kasir.today.index') }}" class="flex flex-col sm:flex-row gap-3 w-full">
            <select name="court_id" class="px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-colors" onchange="this.form.submit()">
                <option value="">Semua Lapangan</option>
                @foreach($courts as $court)
                    <option value="{{ $court->id }}" {{ $selectedCourt == $court->id ? 'selected' : '' }}>{{ $court->name }}</option>
                @endforeach
            </select>
            
            <select name="status" class="px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-colors" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Confirmed (Lunas)</option>
                <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed (Selesai Main)</option>
                <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            
            <noscript><button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-xl">Filter</button></noscript>
        </form>
    </div>

    {{-- Reservations List --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
        @if($reservations->isEmpty())
            <div class="p-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 mb-4">
                    <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-slate-500 dark:text-slate-400 font-medium">Belum ada reservasi untuk hari ini</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Waktu</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Lapangan</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Customer</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Kode Booking</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Status Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($reservations as $res)
                        @php
                            $paymentStatusStyle = match($res->payment?->status) {
                                'paid' => 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10',
                                'pending' => 'text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10',
                                'failed' => 'text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-500/10',
                                default => 'text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-800',
                            };
                            $paymentLabel = $res->payment ? $res->payment->status_label : 'Belum Ada';
                        @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-5 py-4">
                                <span class="font-bold text-slate-800 dark:text-white">{{ \Carbon\Carbon::parse($res->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($res->end_time)->format('H:i') }}</span>
                            </td>
                            <td class="px-5 py-4 text-slate-600 dark:text-slate-300">
                                {{ $res->court->name ?? '-' }}
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-medium text-slate-800 dark:text-white">{{ $res->user->name ?? '-' }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-mono text-pink-600 dark:text-pink-400">{{ $res->booking_code }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center text-xs font-medium px-2.5 py-1 rounded-lg {{ $paymentStatusStyle }}">
                                    {{ $paymentLabel }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</x-layouts.app>
