<x-layouts.app :title="$title">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm mb-6">
        <a href="{{ route('admin.reservations.index') }}" class="text-slate-500 dark:text-slate-400 hover:text-pink-600 dark:hover:text-pink-400 transition-colors">Kelola Reservasi</a>
        <svg class="w-4 h-4 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-slate-800 dark:text-white font-medium">Detail Pesanan #{{ $reservation->id }}</span>
    </nav>

    {{-- Header Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Pesanan #{{ $reservation->id }}</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Dibuat pada {{ $reservation->created_at->format('d M Y, H:i') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-sm font-semibold
                bg-{{ $reservation->status_color }}-50 dark:bg-{{ $reservation->status_color }}-500/10
                text-{{ $reservation->status_color }}-700 dark:text-{{ $reservation->status_color }}-400 border border-{{ $reservation->status_color }}-200 dark:border-{{ $reservation->status_color }}-800">
                Status: {{ $reservation->status_label }}
            </span>
            
            @if(in_array($reservation->status, ['pending', 'confirmed']))
                <form method="POST" action="{{ route('admin.reservations.cancel', $reservation) }}" id="cancel-reservation-form">
                    @csrf
                    <button type="button"
                            x-data
                            @click="$dispatch('open-global-confirm', { formId: 'cancel-reservation-form', message: 'Yakin ingin membatalkan pesanan ini?' })"
                            class="px-4 py-2 bg-red-50 text-red-600 hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400 dark:hover:bg-red-500/20 text-sm font-semibold rounded-xl transition-colors border border-red-200 dark:border-red-800">
                        Batalkan Pesanan
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Left Column: Order & Customer Detail --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Customer Card --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
                <h2 class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Data Pelanggan
                </h2>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#1e3a5f] to-[#e91e8c] flex items-center justify-center text-white font-bold text-lg">
                        {{ strtoupper(substr($reservation->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800 dark:text-white">{{ $reservation->user->name }}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $reservation->user->email }}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $reservation->user->phone ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Order Details --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Rincian Pesanan
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-y-6 gap-x-4">
                        <div>
                            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Lapangan</p>
                            <p class="text-sm font-medium text-slate-800 dark:text-white">{{ $reservation->court->name }}</p>
                            <p class="text-xs text-slate-500">{{ $reservation->court->type_label }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Tanggal Main</p>
                            <p class="text-sm font-medium text-slate-800 dark:text-white">{{ $reservation->date->format('l, d F Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Waktu</p>
                            <p class="text-sm font-medium text-slate-800 dark:text-white">
                                {{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($reservation->end_time)->format('H:i') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Durasi</p>
                            <p class="text-sm font-medium text-slate-800 dark:text-white">{{ $reservation->duration_hours }} Jam</p>
                        </div>
                    </div>

                    @if($reservation->notes)
                        <div class="mt-6 p-4 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 rounded-xl">
                            <p class="text-xs font-semibold text-amber-800 dark:text-amber-500 uppercase tracking-wider mb-1">Catatan</p>
                            <p class="text-sm text-amber-900 dark:text-amber-200">{{ $reservation->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column: Payment Details --}}
        <div class="space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-gradient-to-r from-[#1e3a5f] to-[#e91e8c]">
                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Pembayaran
                    </h2>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">Total Tagihan</p>
                        <p class="text-xl font-extrabold text-slate-800 dark:text-white">{{ $reservation->formatted_total_price }}</p>
                    </div>

                    @if(!$reservation->payment)
                        <div class="text-center py-6 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-dashed border-slate-300 dark:border-slate-700">
                            <p class="text-slate-500 dark:text-slate-400 text-sm">Belum ada data pembayaran masuk.</p>
                        </div>
                    @else
                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500 dark:text-slate-400">Metode</span>
                                <span class="font-medium text-slate-800 dark:text-white">{{ $reservation->payment->method_label }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500 dark:text-slate-400">Status</span>
                                @php
                                    $pStatus = $reservation->payment->status;
                                    $pColor = match($pStatus) {
                                        'paid' => 'emerald',
                                        'failed' => 'red',
                                        'refunded' => 'slate',
                                        default => 'amber',
                                    };
                                @endphp
                                <span class="font-bold text-{{ $pColor }}-600 dark:text-{{ $pColor }}-400">{{ $reservation->payment->status_label }}</span>
                            </div>
                            @if($reservation->payment->verified_by)
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-500 dark:text-slate-400">Diverifikasi Oleh</span>
                                    <span class="font-medium text-slate-800 dark:text-white">{{ $reservation->payment->verifiedBy->name ?? 'Admin' }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Payment Proof Image --}}
                        @if($reservation->payment->payment_proof)
                            <div class="mb-6">
                                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Bukti Transfer</p>
                                <a href="{{ asset('storage/' . $reservation->payment->payment_proof) }}" target="_blank" class="block rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 hover:opacity-90 transition-opacity">
                                    <img src="{{ asset('storage/' . $reservation->payment->payment_proof) }}" alt="Bukti Pembayaran" class="w-full object-cover">
                                </a>
                            </div>
                        @endif

                        {{-- Verification Actions --}}
                        @if($reservation->payment->status === 'pending')
                            <div class="pt-4 border-t border-slate-200 dark:border-slate-800">
                                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3">Tindakan Kasir/Admin</p>
                                <div class="flex gap-2">
                                    <form method="POST" action="{{ route('admin.reservations.verify-payment', $reservation) }}" class="flex-1" id="verify-paid-form">
                                        @csrf
                                        <input type="hidden" name="status" value="paid">
                                        <button type="button"
                                                x-data
                                                @click="$dispatch('open-global-confirm', { formId: 'verify-paid-form', message: 'Konfirmasi pembayaran valid? Status akan menjadi Lunas.' })"
                                                class="w-full px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm shadow-emerald-500/20">
                                            Lunas
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.reservations.verify-payment', $reservation) }}" class="flex-1" id="verify-failed-form">
                                        @csrf
                                        <input type="hidden" name="status" value="failed">
                                        <button type="button"
                                                x-data
                                                @click="$dispatch('open-global-confirm', { formId: 'verify-failed-form', message: 'Tolak pembayaran ini? Pesanan otomatis dibatalkan.' })"
                                                class="w-full px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 dark:bg-red-500/10 dark:hover:bg-red-500/20 dark:text-red-400 text-sm font-semibold rounded-xl transition-colors border border-red-200 dark:border-red-800">
                                            Tolak
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

    </div>

</x-layouts.app>
