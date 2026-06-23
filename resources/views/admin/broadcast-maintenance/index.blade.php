<x-layouts.app :title="$title">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Broadcast Maintenance</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Kirim dan kelola notifikasi pemeliharaan sistem atau lapangan kepada pelanggan.</p>
        </div>
        <a href="{{ route('admin.broadcast-maintenance.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-[#1e3a5f] to-[#e91e8c] text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:shadow-pink-500/25 hover:-translate-y-0.5 transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Broadcast
        </a>
    </div>

    {{-- Table Card --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
        @if($broadcasts->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-slate-300 dark:text-slate-650" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                </div>
                <p class="text-slate-500 dark:text-slate-400 font-medium">Belum ada riwayat broadcast maintenance</p>
                <p class="text-slate-400 dark:text-slate-500 text-sm mt-1">Mulai dengan membuat pemberitahuan broadcast pertama Anda.</p>
                <a href="{{ route('admin.broadcast-maintenance.create') }}"
                   class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-[#1e3a5f] text-white text-sm font-medium rounded-xl hover:bg-[#162d4a] transition-colors">
                    Buat Broadcast Baru
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Judul & Detail</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tipe Maintenance</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jadwal & Durasi</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Target</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Penerima</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pengirim / Waktu Kirim</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($broadcasts as $broadcast)
                            <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                {{-- Number --}}
                                <td class="px-6 py-4 text-sm text-slate-400 dark:text-slate-505 w-12">
                                    {{ $broadcasts->firstItem() + $loop->index }}
                                </td>

                                {{-- Title & Description Preview --}}
                                <td class="px-6 py-4">
                                    <div class="max-w-xs sm:max-w-sm md:max-w-md">
                                        <p class="font-semibold text-slate-850 dark:text-white text-sm">{{ $broadcast->title }}</p>
                                        <p class="text-xs text-slate-405 dark:text-slate-500 mt-1 line-clamp-2 leading-relaxed">{{ $broadcast->description }}</p>
                                    </div>
                                </td>

                                {{-- Type --}}
                                <td class="px-6 py-4">
                                    @if($broadcast->type === 'court')
                                        <div>
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400">
                                                🛠️ Lapangan
                                            </span>
                                            <p class="text-xs font-medium text-slate-600 dark:text-slate-350 mt-1">{{ $broadcast->court?->name ?? 'N/A' }}</p>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400">
                                            💻 Sistem
                                        </span>
                                    @endif
                                </td>

                                {{-- Schedule & Duration --}}
                                <td class="px-6 py-4">
                                    <div class="text-sm text-slate-605 dark:text-slate-300">
                                        <p class="font-medium">{{ $broadcast->scheduled_date->format('d M Y') }}</p>
                                        <p class="text-xs text-slate-405 dark:text-slate-500 mt-0.5">Estimasi: <span class="font-semibold">{{ $broadcast->duration }}</span></p>
                                    </div>
                                </td>

                                {{-- Target Type --}}
                                <td class="px-6 py-4">
                                    @if($broadcast->target_type === 'affected')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-450">
                                            Pelanggan Terdampak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-350">
                                            Semua Pelanggan
                                        </span>
                                    @endif
                                </td>

                                {{-- Recipients Count --}}
                                <td class="px-6 py-4">
                                    <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">
                                        {{ $broadcast->recipients_count }} orang
                                    </span>
                                </td>

                                {{-- Sender & Created At --}}
                                <td class="px-6 py-4 text-xs text-slate-605 dark:text-slate-400">
                                    <p class="font-medium text-slate-700 dark:text-slate-200">{{ $broadcast->sender?->name ?? 'Sistem' }}</p>
                                    <p class="text-slate-405 dark:text-slate-500 mt-0.5">{{ $broadcast->created_at->format('d M Y H:i') }}</p>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($broadcasts->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-4">
                    <p class="text-sm text-slate-550 dark:text-slate-400">
                        Menampilkan {{ $broadcasts->firstItem() }}–{{ $broadcasts->lastItem() }} dari {{ $broadcasts->total() }} riwayat
                    </p>
                    {{ $broadcasts->links() }}
                </div>
            @else
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Total {{ $broadcasts->total() }} riwayat broadcast
                    </p>
                </div>
            @endif
        @endif
    </div>

</x-layouts.app>
