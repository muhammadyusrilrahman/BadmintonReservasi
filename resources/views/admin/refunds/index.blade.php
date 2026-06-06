<x-layouts.app :title="$title">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Persetujuan Refund Reservasi ⚖️</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Kelola, verifikasi, dan selesaikan pengajuan pengembalian dana dari customer.</p>
    </div>

    {{-- Stats Cards Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        {{-- Card 1: Pending --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 flex items-center justify-between shadow-sm hover:shadow-md transition-shadow">
            <div class="space-y-1">
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Menunggu Review</p>
                <h3 class="text-3xl font-extrabold text-slate-800 dark:text-white">{{ $stats['pending'] }}</h3>
                <p class="text-[10px] text-amber-600 font-semibold mt-1">Perlu tindakan segera</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center text-amber-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        {{-- Card 2: Approved / Wait Transfer --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 flex items-center justify-between shadow-sm hover:shadow-md transition-shadow">
            <div class="space-y-1">
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Disetujui (Belum Transfer)</p>
                <h3 class="text-3xl font-extrabold text-slate-800 dark:text-white">{{ $stats['approved'] }}</h3>
                <p class="text-[10px] text-blue-600 font-semibold mt-1">Menunggu pembayaran bank</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        {{-- Card 3: Completed --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 flex items-center justify-between shadow-sm hover:shadow-md transition-shadow">
            <div class="space-y-1">
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Refund Selesai</p>
                <h3 class="text-3xl font-extrabold text-slate-800 dark:text-white">{{ $stats['completed'] }}</h3>
                <p class="text-[10px] text-emerald-600 font-semibold mt-1">Dana berhasil dikembalikan</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
        </div>

        {{-- Card 4: Total Amount --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 flex items-center justify-between shadow-sm hover:shadow-md transition-shadow">
            <div class="space-y-1">
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Nominal Refund</p>
                <h3 class="text-xl font-extrabold text-slate-800 dark:text-white">Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}</h3>
                <p class="text-[10px] text-pink-600 font-semibold mt-1">Dari status 'Selesai'</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-pink-50 dark:bg-pink-500/10 flex items-center justify-center text-pink-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>

    </div>

    {{-- Filters & Content Card --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        
        {{-- Status Filter Tabs --}}
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/40 dark:bg-slate-800/20 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 scrollbar-none">
                <a href="{{ route('admin.refunds.index') }}" 
                   class="px-4 py-2 text-xs font-bold rounded-lg transition-all duration-200 {{ !request('status') ? 'bg-gradient-to-r from-[#1e3a5f] to-[#e91e8c] text-white shadow-md' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    Semua
                </a>
                <a href="{{ route('admin.refunds.index', ['status' => 'requested']) }}" 
                   class="px-4 py-2 text-xs font-bold rounded-lg transition-all duration-200 {{ request('status') === 'requested' ? 'bg-amber-500 text-white shadow-md' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    Menunggu Review ({{ $stats['pending'] }})
                </a>
                <a href="{{ route('admin.refunds.index', ['status' => 'approved']) }}" 
                   class="px-4 py-2 text-xs font-bold rounded-lg transition-all duration-200 {{ request('status') === 'approved' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    Disetujui ({{ $stats['approved'] }})
                </a>
                <a href="{{ route('admin.refunds.index', ['status' => 'rejected']) }}" 
                   class="px-4 py-2 text-xs font-bold rounded-lg transition-all duration-200 {{ request('status') === 'rejected' ? 'bg-red-600 text-white shadow-md' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    Ditolak ({{ $stats['rejected'] }})
                </a>
                <a href="{{ route('admin.refunds.index', ['status' => 'completed']) }}" 
                   class="px-4 py-2 text-xs font-bold rounded-lg transition-all duration-200 {{ request('status') === 'completed' ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    Selesai ({{ $stats['completed'] }})
                </a>
            </div>
            
            <div class="text-xs text-slate-400 dark:text-slate-500">
                Menampilkan {{ $refunds->firstItem() ?? 0 }}-{{ $refunds->lastItem() ?? 0 }} dari {{ $refunds->total() }} pengajuan
            </div>
        </div>

        {{-- Table --}}
        @if($refunds->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/20 dark:bg-slate-800/10 border-b border-slate-200 dark:border-slate-800">
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">ID / Pemohon</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jadwal Asli</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nominal</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tujuan Transfer</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tanggal Pengajuan</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        @foreach($refunds as $refund)
                            <tr class="hover:bg-slate-50/20 dark:hover:bg-slate-800/10 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800 dark:text-white">#REF-{{ $refund->id }}</div>
                                    <div class="text-xs font-semibold text-slate-700 dark:text-slate-300 mt-0.5">{{ $refund->user->name }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">{{ $refund->user->email }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-850 dark:text-slate-200 text-sm">{{ $refund->reservation->court->name }}</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                        {{ $refund->reservation->date->translatedFormat('d M Y') }}
                                    </div>
                                    <div class="text-[10px] text-slate-450 dark:text-slate-500 mt-0.5">
                                        Jam {{ \Carbon\Carbon::parse($refund->reservation->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($refund->reservation->end_time)->format('H:i') }} ({{ $refund->reservation->duration_hours }} Jam)
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-extrabold text-slate-800 dark:text-white">
                                    {{ $refund->formatted_amount }}
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-650 dark:text-slate-350">
                                    <div class="font-bold text-slate-800 dark:text-white">{{ $refund->bank_name }}</div>
                                    <div class="font-mono mt-0.5">{{ $refund->account_number }}</div>
                                    <div class="text-slate-400 mt-0.5">a.n {{ $refund->account_name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold bg-{{ $refund->status_color }}-50 dark:bg-{{ $refund->status_color }}-500/10 text-{{ $refund->status_color }}-600 dark:text-{{ $refund->status_color }}-400 uppercase tracking-wider">
                                        {{ $refund->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-500 dark:text-slate-400">
                                    <div>{{ $refund->created_at->translatedFormat('d M Y') }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">{{ $refund->created_at->format('H:i') }} WIB</div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.refunds.show', $refund) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-[#1e3a5f] to-[#e91e8c] text-white hover:shadow-md rounded-lg text-xs font-bold transition-all duration-200 hover:-translate-y-0.5">
                                        Proses
                                        <svg class="w-3.5 h-3.5 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
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
                <div class="w-16 h-16 bg-slate-50 dark:bg-slate-950 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-slate-100 dark:border-slate-800">
                    <svg class="w-8 h-8 text-slate-350 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="font-bold text-slate-800 dark:text-white text-base">Tidak ada pengajuan refund</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-1 max-w-sm mx-auto">
                    Saat ini tidak ada pengajuan refund yang sesuai dengan filter yang Anda gunakan.
                </p>
            </div>
        @endif

    </div>

</x-layouts.app>
