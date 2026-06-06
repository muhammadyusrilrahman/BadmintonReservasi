<x-layouts.app :title="$title">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm mb-6" aria-label="Breadcrumb">
        <a href="{{ route('customer.reservations.index') }}" class="text-slate-500 dark:text-slate-400 hover:text-pink-600 dark:hover:text-pink-400 transition-colors">Reservasi Saya</a>
        <svg class="w-4 h-4 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-800 dark:text-white font-medium">Detail #{{ $reservation->id }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Order Detail --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Status Banner --}}
            <div class="bg-{{ $reservation->status_color }}-50 dark:bg-{{ $reservation->status_color }}-500/10 border border-{{ $reservation->status_color }}-200 dark:border-{{ $reservation->status_color }}-800 rounded-2xl p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-{{ $reservation->status_color }}-100 dark:bg-{{ $reservation->status_color }}-500/20 flex items-center justify-center flex-shrink-0">
                    @if($reservation->status === 'pending')
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @elseif($reservation->status === 'confirmed')
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @elseif($reservation->status === 'completed')
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    @else
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    @endif
                </div>
                <div>
                    <p class="font-bold text-{{ $reservation->status_color }}-800 dark:text-{{ $reservation->status_color }}-300">Status: {{ $reservation->status_label }}</p>
                    <p class="text-sm text-{{ $reservation->status_color }}-700 dark:text-{{ $reservation->status_color }}-400 mt-0.5">
                        @if($reservation->status === 'pending')
                            Silakan lakukan pembayaran dan upload bukti transfer.
                        @elseif($reservation->status === 'confirmed')
                            Pembayaran telah diverifikasi. Selamat bermain!
                        @elseif($reservation->status === 'completed')
                            Reservasi telah selesai. Terima kasih!
                        @else
                            Reservasi telah dibatalkan.
                        @endif
                    </p>
                </div>
            </div>

            {{-- Order Detail Card --}}
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
                            <p class="text-sm font-medium text-slate-800 dark:text-white">{{ $reservation->date->translatedFormat('l, d F Y') }}</p>
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

        {{-- Right: Payment --}}
        <div class="space-y-6">
            {{-- Payment Card --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
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

                    @if($reservation->payment)
                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-500 dark:text-slate-400">Metode Pembayaran</span>
                                <span class="font-semibold text-slate-800 dark:text-white bg-slate-100 dark:bg-slate-800 px-3 py-1 rounded-lg">
                                    @if($reservation->payment->payment_type)
                                        {{ $reservation->payment->method_label }}
                                    @else
                                        Midtrans Snap Gateway
                                    @endif
                                </span>
                            </div>
                            @if($reservation->payment->midtrans_transaction_id)
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-slate-500 dark:text-slate-400">ID Transaksi</span>
                                    <span class="font-mono text-xs text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800/50 px-2 py-1 rounded-md">{{ $reservation->payment->midtrans_transaction_id }}</span>
                                </div>
                            @endif
                            @if($reservation->payment->paid_at)
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-slate-500 dark:text-slate-400">Waktu Bayar</span>
                                    <span class="font-medium text-slate-800 dark:text-white">{{ $reservation->payment->paid_at->translatedFormat('d M Y H:i') }} WIB</span>
                                </div>
                            @endif
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-500 dark:text-slate-400">Status Transaksi</span>
                                @php
                                    $pColor = match($reservation->payment->status) {
                                        'paid' => 'emerald',
                                        'failed' => 'red',
                                        'refunded' => 'slate',
                                        default => 'amber',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-{{ $pColor }}-50 dark:bg-{{ $pColor }}-500/10 text-{{ $pColor }}-600 dark:text-{{ $pColor }}-400 uppercase tracking-wider">
                                    {{ $reservation->payment->status_label }}
                                </span>
                            </div>
                        </div>

                        @if($reservation->payment->status === 'pending')
                            {{-- Info Box --}}
                            <div class="p-4 bg-amber-50 dark:bg-amber-500/5 border border-amber-200 dark:border-amber-500/20 rounded-xl mb-6 text-center">
                                <svg class="w-6 h-6 text-amber-500 mx-auto mb-2 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-xs font-bold text-amber-800 dark:text-amber-400 uppercase tracking-wider">Batas Waktu Pembayaran</p>
                                <p class="text-sm text-amber-950 dark:text-amber-200 mt-1 font-semibold">15 Menit Sejak Reservasi Dibuat</p>
                                <p class="text-xs text-amber-600/80 dark:text-amber-500/80 mt-1">Pemesanan Anda akan kedaluwarsa secara otomatis jika tidak dibayar tepat waktu.</p>
                            </div>

                            {{-- Payment Action Button --}}
                            <button id="pay-button" class="w-full relative inline-flex items-center justify-center gap-2 px-6 py-4 bg-gradient-to-r from-[#1e3a5f] to-[#e91e8c] hover:from-[#152a46] hover:to-[#ce1277] text-white text-base font-bold rounded-xl shadow-lg shadow-pink-500/20 hover:shadow-pink-500/30 transition-all duration-200 group active:scale-[0.98]">
                                <svg id="pay-spinner" class="hidden animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span id="pay-text" class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-white/80 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    Bayar Sekarang
                                </span>
                            </button>
                        @elseif($reservation->payment->status === 'paid')
                            <div class="p-5 bg-emerald-50 dark:bg-emerald-500/5 border border-emerald-200 dark:border-emerald-500/20 rounded-xl text-center">
                                <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-500/20 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <p class="text-sm font-bold text-emerald-800 dark:text-emerald-400">Pembayaran Terverifikasi</p>
                                <p class="text-xs text-emerald-600 dark:text-emerald-500 mt-1">Sistem telah memverifikasi pembayaran Anda. Selamat bermain di Adenia Salsa Badminton!</p>
                            </div>

                            {{-- Reschedule & Refund Actions --}}
                            @if($reservation->canReschedule() || $reservation->canRequestRefund())
                                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 flex flex-col gap-2">
                                    @if($reservation->canReschedule())
                                        <a href="{{ route('customer.reservations.reschedule', $reservation) }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-all duration-200 shadow-md active:scale-[0.98]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                            Ubah Jadwal (Reschedule)
                                        </a>
                                    @endif

                                    @if($reservation->canRequestRefund())
                                        <button type="button" onclick="openRefundModal()" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-red-50 dark:hover:bg-red-500/10 text-slate-700 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400 text-sm font-semibold rounded-xl transition-all duration-200 border border-slate-200 dark:border-slate-700 hover:border-red-200 dark:hover:border-red-800 active:scale-[0.98]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-6a4 4 0 00-4-4H3m0 0l3-3m-3 3l3 3m9 14V5M9 21h6"/></svg>
                                            Ajukan Pengembalian Dana (Refund)
                                        </button>
                                    @endif
                                </div>
                            @endif

                            {{-- Refund active tracking --}}
                            @if($reservation->refund)
                                <div class="mt-4 p-4 bg-{{ $reservation->refund->status_color }}-50 dark:bg-{{ $reservation->refund->status_color }}-500/5 border border-{{ $reservation->refund->status_color }}-200 dark:border-{{ $reservation->refund->status_color }}-500/20 rounded-xl text-left">
                                    <p class="text-xs font-bold text-{{ $reservation->refund->status_color }}-800 dark:text-{{ $reservation->refund->status_color }}-400 uppercase tracking-wider mb-2">Tracking Refund</p>
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-{{ $reservation->refund->status_color }}-100 dark:bg-{{ $reservation->refund->status_color }}-500/20 text-{{ $reservation->refund->status_color }}-800 dark:text-{{ $reservation->refund->status_color }}-300 uppercase tracking-wider">
                                            {{ $reservation->refund->status_label }}
                                        </span>
                                        <span class="text-[10px] text-slate-500">{{ $reservation->refund->created_at->translatedFormat('d M Y H:i') }} WIB</span>
                                    </div>
                                    <div class="text-[11px] text-slate-600 dark:text-slate-400 space-y-1">
                                        <p><strong class="text-slate-700 dark:text-slate-300">Jumlah:</strong> {{ $reservation->refund->formatted_amount }}</p>
                                        <p><strong class="text-slate-700 dark:text-slate-300">Tujuan:</strong> {{ $reservation->refund->bank_name }} - {{ $reservation->refund->account_number }} a.n {{ $reservation->refund->account_name }}</p>
                                        <p><strong class="text-slate-700 dark:text-slate-300">Alasan:</strong> "{{ $reservation->refund->reason }}"</p>
                                        @if($reservation->refund->admin_notes)
                                            <div class="mt-1.5 p-1.5 bg-white/50 dark:bg-slate-950 rounded-lg">
                                                <p class="font-bold text-slate-700 dark:text-slate-300">Catatan Admin:</p>
                                                <p class="italic">"{{ $reservation->refund->admin_notes }}"</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="p-5 bg-red-50 dark:bg-red-500/5 border border-red-200 dark:border-red-500/20 rounded-xl text-center">
                                <div class="w-10 h-10 bg-red-100 dark:bg-red-500/20 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </div>
                                <p class="text-sm font-bold text-red-800 dark:text-red-400">Pembayaran Gagal / Kedaluwarsa</p>
                                <p class="text-xs text-red-600 dark:text-red-500 mt-1">Sesi pembayaran telah berakhir atau dibatalkan. Silakan buat reservasi baru.</p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- QR Code Section (hanya tampil saat confirmed) --}}
    @if($reservation->status === 'confirmed')
    <div class="mt-6 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
        <h3 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2 mb-4">
            <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
            QR Code Check-in
        </h3>
        <div class="flex flex-col items-center">
            <div id="qr-code" class="bg-white p-4 rounded-xl shadow-sm border border-slate-100"></div>
            <div class="mt-4 text-center">
                <p class="text-xs font-mono font-bold text-slate-800 dark:text-white tracking-widest bg-slate-100 dark:bg-slate-800 px-4 py-2 rounded-lg">{{ $reservation->booking_code }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Tunjukkan QR Code ini ke petugas saat datang ke lapangan.</p>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.4/build/qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const qrContainer = document.getElementById('qr-code');
            if (qrContainer) {
                const canvas = document.createElement('canvas');
                QRCode.toCanvas(canvas, '{{ route("staff.checkin.verify", $reservation->booking_code) }}', {
                    width: 200,
                    margin: 2,
                    color: { dark: '#1e3a5f', light: '#ffffff' }
                });
                qrContainer.appendChild(canvas);
            }
        });
    </script>
    @endpush
    @endif

    @if($reservation->payment && $reservation->payment->status === 'pending')
        @push('scripts')
            <script src="https://app.{{ config('services.midtrans.is_production') ? '' : 'sandbox.' }}midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const payButton = document.getElementById('pay-button');
                    const paySpinner = document.getElementById('pay-spinner');
                    const payText = document.getElementById('pay-text');

                    if (payButton) {
                        payButton.addEventListener('click', function() {
                            // Set loading state
                            payButton.disabled = true;
                            paySpinner.classList.remove('hidden');
                            payText.classList.add('opacity-50');

                            // Fetch Snap Token
                            fetch('{{ route("customer.reservations.snap-token", $reservation) }}', {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Gagal mendapatkan token pembayaran.');
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success && data.snap_token) {
                                    // Verify Snap SDK loaded
                                    if (typeof window.snap === 'undefined') {
                                        throw new Error('Midtrans Snap SDK gagal dimuat. Silakan refresh halaman.');
                                    }
                                    // Trigger Midtrans Snap
                                    window.snap.pay(data.snap_token, {
                                        onSuccess: function(result) {
                                            window.location.reload();
                                        },
                                        onPending: function(result) {
                                            window.location.reload();
                                        },
                                        onError: function(result) {
                                            alert('Terjadi kesalahan pembayaran: ' + (result.status_message || 'Gagal diproses'));
                                            window.location.reload();
                                        },
                                        onClose: function() {
                                            // Restore button state
                                            payButton.disabled = false;
                                            paySpinner.classList.add('hidden');
                                            payText.classList.remove('opacity-50');
                                        }
                                    });
                                } else {
                                    throw new Error(data.message || 'Gagal memproses pembayaran.');
                                }
                            })
                            .catch(error => {
                                alert(error.message || 'Terjadi kesalahan sistem. Mohon coba beberapa saat lagi.');
                                payButton.disabled = false;
                                paySpinner.classList.add('hidden');
                                payText.classList.remove('opacity-50');
                            });
                        });
                    }
                });
            </script>
        @endpush
    @endif

    {{-- Refund Modal --}}
    <div id="refund-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity" onclick="closeRefundModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200 dark:border-slate-800">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 flex justify-between items-center">
                    <h3 class="text-base font-bold text-slate-800 dark:text-white" id="modal-title">Ajukan Refund Reservasi</h3>
                    <button type="button" onclick="closeRefundModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form action="{{ route('customer.reservations.refund.request', $reservation) }}" method="POST">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div class="p-4 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 rounded-xl text-xs text-amber-800 dark:text-amber-400">
                            <strong>Perhatian:</strong> Pengajuan refund Anda akan ditinjau oleh Admin. Pastikan data rekening Anda sudah benar.
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Nama Bank</label>
                            <input type="text" name="bank_name" required placeholder="Contoh: BCA, Mandiri, BNI" class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-white focus:ring-pink-500 focus:border-pink-500 text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Nomor Rekening</label>
                            <input type="text" name="account_number" required placeholder="Masukkan nomor rekening Anda" class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-white focus:ring-pink-500 focus:border-pink-500 text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Atas Nama Pemilik Rekening</label>
                            <input type="text" name="account_name" required placeholder="Nama pemilik rekening sesuai bank" class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-white focus:ring-pink-500 focus:border-pink-500 text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Alasan Refund</label>
                            <textarea name="reason" required minlength="10" placeholder="Tuliskan alasan mengapa Anda mengajukan refund" rows="3" class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-white focus:ring-pink-500 focus:border-pink-500 text-sm"></textarea>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-3">
                        <button type="button" onclick="closeRefundModal()" class="px-4 py-2 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white text-sm font-semibold rounded-xl transition-colors">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-gradient-to-r from-[#1e3a5f] to-[#e91e8c] hover:from-[#152a46] hover:to-[#ce1277] text-white text-sm font-bold rounded-xl transition-all duration-200">Kirim Pengajuan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openRefundModal() {
            document.getElementById('refund-modal').classList.remove('hidden');
        }
        function closeRefundModal() {
            document.getElementById('refund-modal').classList.add('hidden');
        }
    </script>
    @endpush

</x-layouts.app>
