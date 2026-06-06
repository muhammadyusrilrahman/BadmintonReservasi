<x-layouts.app :title="$title">

    {{-- Header --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Refund Saya 💰</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Daftar histori pengajuan pengembalian dana (refund) reservasi Anda.</p>
        </div>
        <div>
            <a href="{{ route('customer.reservations.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/50 text-sm font-semibold transition-all duration-200 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Lihat Reservasi Aktif
            </a>
        </div>
    </div>

    {{-- Info Card --}}
    <div class="bg-blue-50 dark:bg-blue-500/5 border border-blue-200 dark:border-blue-500/20 rounded-2xl p-5 mb-6 flex items-start gap-4 shadow-sm">
        <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center text-blue-600 dark:text-blue-400 flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <h3 class="font-bold text-blue-900 dark:text-blue-300 text-sm">Informasi Proses Refund</h3>
            <p class="text-xs text-blue-800 dark:text-blue-400 mt-1 leading-relaxed">
                Pengajuan refund akan diverifikasi oleh Admin. Setelah disetujui, reservasi Anda akan dibatalkan secara otomatis agar lapangan dapat dipesan kembali oleh pengguna lain. Status transfer dana dapat dipantau langsung di halaman ini.
            </p>
        </div>
    </div>

    {{-- Main Card/Table --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        @if($refunds->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-800/40 border-b border-slate-200 dark:border-slate-800">
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">ID / Kode Booking</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Lapangan & Jadwal</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jumlah Refund</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Rekening Tujuan</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Keterangan Admin</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        @foreach($refunds as $refund)
                            <tr class="hover:bg-slate-50/30 dark:hover:bg-slate-800/10 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800 dark:text-white">#REF-{{ $refund->id }}</div>
                                    <div class="text-xs font-mono font-bold text-pink-600 dark:text-pink-400 mt-0.5">{{ $refund->reservation->booking_code }}</div>
                                    <div class="text-[10px] text-slate-400 mt-1">Diajukan: {{ $refund->created_at->translatedFormat('d/m/Y H:i') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-800 dark:text-slate-200 text-sm">{{ $refund->reservation->court->name }}</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                        {{ $refund->reservation->date->translatedFormat('d M Y') }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">
                                        {{ \Carbon\Carbon::parse($refund->reservation->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($refund->reservation->end_time)->format('H:i') }} ({{ $refund->reservation->duration_hours }} Jam)
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-extrabold text-slate-800 dark:text-white">
                                    {{ $refund->formatted_amount }}
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-600 dark:text-slate-300">
                                    <div class="font-bold text-slate-800 dark:text-white">{{ $refund->bank_name }}</div>
                                    <div class="font-mono mt-0.5">{{ $refund->account_number }}</div>
                                    <div class="text-slate-400 mt-0.5">a.n {{ $refund->account_name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold bg-{{ $refund->status_color }}-50 dark:bg-{{ $refund->status_color }}-500/10 text-{{ $refund->status_color }}-600 dark:text-{{ $refund->status_color }}-400 uppercase tracking-wider">
                                        {{ $refund->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs max-w-xs truncate">
                                    @if($refund->admin_notes)
                                        <p class="text-slate-600 dark:text-slate-300 italic">"{{ $refund->admin_notes }}"</p>
                                        @if($refund->completed_at)
                                            <p class="text-[10px] text-slate-400 mt-1">Ditransfer: {{ $refund->completed_at->translatedFormat('d M Y H:i') }}</p>
                                        @endif
                                    @else
                                        <span class="text-slate-400 italic">Belum ada tanggapan</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('customer.reservations.show', $refund->reservation) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-pink-600 hover:text-white dark:hover:bg-pink-600 dark:hover:text-white rounded-lg text-slate-700 dark:text-slate-300 text-xs font-semibold transition-all duration-200">
                                        Detail
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($refunds->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800">
                    {{ $refunds->links() }}
                </div>
            @endif
        @else
            {{-- Empty State --}}
            <div class="text-center py-16 px-6">
                <div class="w-16 h-16 bg-slate-50 dark:bg-slate-950 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-slate-100 dark:border-slate-800/80">
                    <svg class="w-8 h-8 text-slate-300 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 15v-6a4 4 0 00-4-4H3m0 0l3-3m-3 3l3 3m9 14V5M9 21h6"/></svg>
                </div>
                <h3 class="font-bold text-slate-800 dark:text-white text-base">Tidak ada pengajuan refund</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-1 max-w-sm mx-auto">Anda tidak memiliki pengajuan pengembalian dana saat ini.</p>
                <div class="mt-5">
                    <a href="{{ route('customer.reservations.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-[#1e3a5f] to-[#e91e8c] text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:shadow-pink-500/25 transition-all">
                        Pesan atau Cek Reservasi Anda
                    </a>
                </div>
            </div>
        @endif
    </div>

</x-layouts.app>
