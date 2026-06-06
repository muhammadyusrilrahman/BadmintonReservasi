<x-layouts.app :title="$title">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm mb-6" aria-label="Breadcrumb">
        <a href="{{ route('admin.refunds.index') }}" class="text-slate-500 dark:text-slate-400 hover:text-pink-600 dark:hover:text-pink-400 transition-colors">Daftar Refund</a>
        <svg class="w-4 h-4 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-800 dark:text-white font-medium">Detail #REF-{{ $refund->id }}</span>
    </nav>

    {{-- Header --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Detail Pengajuan Refund #REF-{{ $refund->id }} ⚖️</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Gunakan panel ini untuk meninjau pengajuan, menyetujui, menolak, atau menandai refund selesai.</p>
        </div>
        <div>
            <a href="{{ route('admin.refunds.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/50 text-sm font-semibold transition-all duration-200 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Daftar
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Details & Log (2 Columns on Large Screens) --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Refund detail card --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50">
                    <h2 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Rincian Pengembalian Dana
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-4">
                        <div>
                            <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Customer Pemohon</p>
                            <p class="text-sm font-bold text-slate-800 dark:text-white">{{ $refund->user->name }}</p>
                            <p class="text-xs text-slate-500">{{ $refund->user->email }} | {{ $refund->user->phone ?? 'No Phone' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Nominal Refund (100%)</p>
                            <p class="text-lg font-extrabold text-pink-600 dark:text-pink-400">{{ $refund->formatted_amount }}</p>
                        </div>
                        <div class="sm:col-span-2 p-4 bg-slate-50 dark:bg-slate-950 rounded-xl border border-slate-100 dark:border-slate-800/80">
                            <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Tujuan Transfer Balik</p>
                            <div class="grid grid-cols-3 gap-2 text-xs">
                                <div>
                                    <span class="text-slate-400 block mb-0.5">Nama Bank:</span>
                                    <span class="font-bold text-slate-800 dark:text-white text-sm bg-slate-200 dark:bg-slate-800 px-2.5 py-0.5 rounded">{{ $refund->bank_name }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block mb-0.5">Nomor Rekening:</span>
                                    <span class="font-mono font-bold text-slate-800 dark:text-white text-sm tracking-wider" id="acc-number">{{ $refund->account_number }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block mb-0.5">Atas Nama Pemilik:</span>
                                    <span class="font-bold text-slate-800 dark:text-white text-sm capitalize">{{ $refund->account_name }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1.5">Alasan Pengajuan Refund</p>
                            <div class="p-3 bg-red-50/50 dark:bg-red-500/5 border border-red-100 dark:border-red-500/10 rounded-xl text-sm italic text-slate-700 dark:text-slate-350">
                                "{{ $refund->reason }}"
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Original Booking details --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50">
                    <h2 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Informasi Reservasi & Pembayaran Asli
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                        <div>
                            <span class="text-xs font-semibold text-slate-400 block mb-1">Kode Booking</span>
                            <span class="font-mono font-bold text-pink-600 dark:text-pink-400 bg-pink-500/10 px-2 py-0.5 rounded text-xs">{{ $refund->reservation->booking_code }}</span>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-slate-400 block mb-1">Lapangan</span>
                            <span class="font-bold text-slate-800 dark:text-white">{{ $refund->reservation->court->name }}</span>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-slate-400 block mb-1">Jadwal Bermain</span>
                            <span class="font-semibold text-slate-800 dark:text-white">{{ $refund->reservation->date->translatedFormat('d M Y') }}</span>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-slate-400 block mb-1">Durasi / Jam</span>
                            <span class="font-semibold text-slate-800 dark:text-white">{{ \Carbon\Carbon::parse($refund->reservation->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($refund->reservation->end_time)->format('H:i') }} ({{ $refund->reservation->duration_hours }} Jam)</span>
                        </div>
                    </div>

                    @if($refund->reservation->payment)
                        <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800/80 grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                            <div>
                                <span class="text-slate-400 block mb-0.5">ID Transaksi Midtrans</span>
                                <span class="font-mono font-medium text-slate-800 dark:text-white">{{ $refund->reservation->payment->midtrans_transaction_id ?? 'N/A (Transfer Manual)' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block mb-0.5">Metode Pembayaran</span>
                                <span class="font-semibold text-slate-800 dark:text-white capitalize">{{ $refund->reservation->payment->payment_type ? $refund->reservation->payment->method_label : 'Midtrans Snap' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block mb-0.5">Waktu Lunas</span>
                                <span class="font-semibold text-slate-800 dark:text-white">{{ $refund->reservation->payment->paid_at ? $refund->reservation->payment->paid_at->translatedFormat('d M Y H:i') . ' WIB' : '-' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block mb-0.5">Status Bayar</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                                    {{ $refund->reservation->payment->status_label }}
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Reservation status logs timeline --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50">
                    <h2 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Histori Perubahan Status & Log
                    </h2>
                </div>
                <div class="p-6">
                    @if($statusLogs->count() > 0)
                        <div class="flow-root">
                            <ul role="list" class="-mb-8">
                                @foreach($statusLogs as $log)
                                    <li>
                                        <div class="relative pb-8">
                                            @if(!$loop->last)
                                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-slate-200 dark:bg-slate-800" aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center ring-8 ring-white dark:ring-slate-900 text-xs">
                                                        @if($log->change_type === 'refund_requested')
                                                            ⚖️
                                                        @elseif($log->change_type === 'refund_approved')
                                                            ✓
                                                        @elseif($log->change_type === 'refund_rejected')
                                                            ✕
                                                        @elseif($log->change_type === 'refund_completed')
                                                            💰
                                                        @elseif($log->change_type === 'reschedule')
                                                            📅
                                                        @else
                                                            ⚙️
                                                        @endif
                                                    </span>
                                                </div>
                                                <div class="flex-1 min-w-0 pt-1.5">
                                                    <div class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                                                        {{ $log->description }}
                                                    </div>
                                                    <div class="text-[10px] text-slate-400 mt-1 flex items-center gap-2">
                                                        <span>Oleh: <strong class="text-slate-500">{{ $log->user ? $log->user->name : 'Sistem Otomatis' }}</strong></span>
                                                        <span>•</span>
                                                        <span>{{ $log->created_at->translatedFormat('d M Y H:i') }} WIB</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <p class="text-xs text-slate-400 italic text-center py-4">Belum ada log aktivitas untuk reservasi ini.</p>
                    @endif
                </div>
            </div>

        </div>

        {{-- Right: Actions Panel --}}
        <div class="space-y-6">
            
            {{-- Status Card --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm text-center">
                <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Status Refund Saat Ini</p>
                <div class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-extrabold bg-{{ $refund->status_color }}-50 dark:bg-{{ $refund->status_color }}-500/10 text-{{ $refund->status_color }}-600 dark:text-{{ $refund->status_color }}-400 uppercase tracking-widest border border-{{ $refund->status_color }}-200 dark:border-{{ $refund->status_color }}-800/30">
                    {{ $refund->status_label }}
                </div>
                
                @if($refund->processed_at)
                    <p class="text-[10px] text-slate-400 mt-3">
                        Diproses oleh: <strong>{{ $refund->processedBy->name }}</strong><br>
                        Tanggal: {{ $refund->processed_at->translatedFormat('d M Y H:i') }} WIB
                    </p>
                @endif
            </div>

            {{-- Action Forms (Tergantung Status) --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white">Panel Tindakan Admin</h3>
                </div>
                
                <div class="p-6">
                    
                    {{-- 1. Status: REQUESTED --}}
                    @if($refund->status === \App\Models\Refund::STATUS_REQUESTED)
                        <div x-data="{ action: 'approve' }" class="space-y-6">
                            
                            {{-- Selector --}}
                            <div class="grid grid-cols-2 gap-2 p-1 bg-slate-100 dark:bg-slate-950 rounded-xl">
                                <button type="button" 
                                        @click="action = 'approve'"
                                        :class="action === 'approve' ? 'bg-white dark:bg-slate-900 text-slate-800 dark:text-white shadow-sm font-bold' : 'text-slate-500 hover:text-slate-700'"
                                        class="py-2 text-xs font-semibold rounded-lg transition-all">
                                    Setujui Refund
                                </button>
                                <button type="button" 
                                        @click="action = 'reject'"
                                        :class="action === 'reject' ? 'bg-white dark:bg-slate-900 text-slate-800 dark:text-white shadow-sm font-bold' : 'text-slate-500 hover:text-slate-700'"
                                        class="py-2 text-xs font-semibold rounded-lg transition-all">
                                    Tolak Refund
                                </button>
                            </div>

                            {{-- Form Approve --}}
                            <div x-show="action === 'approve'" class="space-y-4">
                                <div class="p-4 bg-emerald-50 dark:bg-emerald-500/5 border border-emerald-200 dark:border-emerald-500/20 rounded-xl text-[11px] text-emerald-800 dark:text-emerald-400 leading-relaxed">
                                    <strong>Efek Persetujuan:</strong><br>
                                    1. Status Reservasi menjadi **Dibatalkan** (lapangan dibebaskan kembali).<br>
                                    2. Status Pembayaran menjadi **Refunded**.<br>
                                    3. Status Refund menjadi **Disetujui** (tinggal ditransfer dana).
                                </div>

                                <form action="{{ route('admin.refunds.approve', $refund) }}" method="POST" id="approve-form">
                                    @csrf
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Catatan Persetujuan</label>
                                        <textarea name="admin_notes" required placeholder="Contoh: Pengajuan disetujui. Dana akan ditransfer dalam 1x24 jam." rows="3" class="w-full px-4 py-2.5 text-xs bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-white focus:ring-pink-500 focus:border-pink-500 transition resize-none"></textarea>
                                    </div>
                                    <button type="button"
                                            @click="$dispatch('open-global-confirm', { formId: 'approve-form', message: 'Apakah Anda yakin ingin MENYETUJUI pengajuan refund ini? Slot reservasi lapangan akan dibebaskan kembali secara otomatis.' })"
                                            class="w-full mt-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-all shadow-md active:scale-[0.98] text-xs">
                                        Setujui & Bebaskan Slot
                                    </button>
                                </form>
                            </div>

                            {{-- Form Reject --}}
                            <div x-show="action === 'reject'" class="space-y-4" x-cloak>
                                <div class="p-4 bg-red-50 dark:bg-red-500/5 border border-red-200 dark:border-red-500/20 rounded-xl text-[11px] text-red-800 dark:text-red-400 leading-relaxed">
                                    <strong>Efek Penolakan:</strong><br>
                                    1. Reservasi tetap aktif berstatus **Confirmed**.<br>
                                    2. Pembayaran tetap aman berstatus **Paid**.<br>
                                    3. Status Refund menjadi **Ditolak**.
                                </div>

                                <form action="{{ route('admin.refunds.reject', $refund) }}" method="POST" id="reject-form">
                                    @csrf
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Alasan Penolakan</label>
                                        <textarea name="admin_notes" required placeholder="Tulis alasan logis penolakan refund, contoh: Pengajuan diajukan melewati batas H-1." rows="3" class="w-full px-4 py-2.5 text-xs bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-white focus:ring-pink-500 focus:border-pink-500 transition resize-none"></textarea>
                                    </div>
                                    <button type="button"
                                            @click="$dispatch('open-global-confirm', { formId: 'reject-form', message: 'Apakah Anda yakin ingin MENOLAK pengajuan refund ini? Reservasi dan pembayaran customer akan tetap aktif.' })"
                                            class="w-full mt-4 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition-all shadow-md active:scale-[0.98] text-xs">
                                        Tolak Refund Customer
                                    </button>
                                </form>
                            </div>

                        </div>
                    
                    {{-- 2. Status: APPROVED (Waiting Transfer) --}}
                    @elseif($refund->status === \App\Models\Refund::STATUS_APPROVED)
                        <div class="space-y-4">
                            <div class="p-4 bg-blue-50 dark:bg-blue-500/5 border border-blue-200 dark:border-blue-500/20 rounded-xl text-[11px] text-blue-800 dark:text-blue-400 leading-relaxed">
                                <strong>Catatan Admin Sebelumnya:</strong><br>
                                <span class="italic">"{{ $refund->admin_notes }}"</span>
                            </div>

                            <div class="p-4 bg-amber-50 dark:bg-amber-500/5 border border-amber-200 dark:border-amber-500/20 rounded-xl text-[11px] text-amber-800 dark:text-amber-400 leading-relaxed">
                                <strong>Instruksi Transfer:</strong><br>
                                1. Transfer uang sebesar **{{ $refund->formatted_amount }}** ke rekening tujuan.<br>
                                2. Setelah transfer sukses, klik tombol di bawah untuk menandai refund telah diselesaikan.
                            </div>

                            <form action="{{ route('admin.refunds.complete', $refund) }}" method="POST" id="complete-form">
                                @csrf
                                <button type="button"
                                        @click="$dispatch('open-global-confirm', { formId: 'complete-form', message: 'Konfirmasi bahwa Anda telah mentransfer dana sebesar ' + '{{ $refund->formatted_amount }}' + ' ke rekening customer?' })"
                                        class="w-full py-3 bg-gradient-to-r from-blue-600 to-pink-600 hover:from-blue-700 hover:to-pink-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-pink-500/20 active:scale-[0.98] text-xs">
                                    Tandai Transfer Selesai (Completed)
                                </button>
                            </form>
                        </div>

                    {{-- 3. Status: COMPLETED --}}
                    @elseif($refund->status === \App\Models\Refund::STATUS_COMPLETED)
                        <div class="p-4 bg-emerald-50 dark:bg-emerald-500/5 border border-emerald-200 dark:border-emerald-500/20 rounded-xl text-center">
                            <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-500/20 rounded-full flex items-center justify-center mx-auto mb-3 text-emerald-500">
                                ✓
                            </div>
                            <h4 class="text-xs font-bold text-emerald-800 dark:text-emerald-400 uppercase tracking-wider">Refund Telah Selesai</h4>
                            <p class="text-[11px] text-emerald-700 dark:text-emerald-500 mt-1">
                                Seluruh dana telah dikirimkan ke rekening customer. Sesi penanganan refund ini ditutup.
                            </p>
                        </div>
                    
                    {{-- 4. Status: REJECTED --}}
                    @elseif($refund->status === \App\Models\Refund::STATUS_REJECTED)
                        <div class="p-4 bg-red-50 dark:bg-red-500/5 border border-red-200 dark:border-red-500/20 rounded-xl text-center">
                            <div class="w-10 h-10 bg-red-100 dark:bg-red-500/20 rounded-full flex items-center justify-center mx-auto mb-3 text-red-500">
                                ✕
                            </div>
                            <h4 class="text-xs font-bold text-red-800 dark:text-red-400 uppercase tracking-wider font-extrabold">Refund Ditolak</h4>
                            <p class="text-[11px] text-red-700 dark:text-red-500 mt-1.5 leading-relaxed">
                                Pengajuan refund ini ditolak. Reservasi tetap aktif di sistem.<br>
                                Alasan penolakan:<br>
                                <span class="italic font-semibold text-slate-700 dark:text-slate-300">"{{ $refund->admin_notes }}"</span>
                            </p>
                        </div>
                    @endif

                </div>
            </div>

        </div>

    </div>

</x-layouts.app>
