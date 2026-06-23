<x-layouts.app :title="$title">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Kelola Promo</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Kelola kode promo dan diskon untuk reservasi.</p>
        </div>
        @if(auth()->user()->roles->first()?->name === 'admin')
        <a href="{{ route('admin.promos.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-[#1e3a5f] to-[#e91e8c] text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:shadow-pink-500/25 hover:-translate-y-0.5 transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Promo
        </a>
        @endif
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ url()->current() }}"
          class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            {{-- Search --}}
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari kode promo..."
                       class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-pink-500 transition">
            </div>
            {{-- Status Filter --}}
            <select name="status"
                    class="px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-pink-500 transition">
                <option value="">Semua Status</option>
                <option value="active" @selected(request('status') === 'active')>Aktif</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                <option value="expired" @selected(request('status') === 'expired')>Kedaluwarsa</option>
                <option value="scheduled" @selected(request('status') === 'scheduled')>Terjadwal</option>
            </select>
            {{-- Submit --}}
            <button type="submit"
                    class="px-5 py-2.5 bg-[#1e3a5f] text-white text-sm font-medium rounded-xl hover:bg-[#162d4a] transition-colors">
                Filter
            </button>
            @if(request()->hasAny(['search','status']))
                <a href="{{ url()->current() }}"
                   class="px-4 py-2.5 text-sm text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl transition-colors">
                    Reset
                </a>
            @endif
        </div>
    </form>

    {{-- Table Card --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
        @if($promos->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <p class="text-slate-500 dark:text-slate-400 font-medium">Tidak ada kode promo ditemukan</p>
                <p class="text-slate-400 dark:text-slate-500 text-sm mt-1">
                    @if(request()->hasAny(['search','status']))
                        Coba ubah filter pencarian Anda
                    @else
                        Mulai dengan menambahkan kode promo pertama
                    @endif
                </p>
                @if(!request()->hasAny(['search','status']) && auth()->user()->roles->first()?->name === 'admin')
                    <a href="{{ route('admin.promos.create') }}"
                       class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-[#1e3a5f] to-[#e91e8c] text-white text-sm font-medium rounded-xl hover:shadow-lg transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Promo
                    </a>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kode Promo</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Diskon</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Periode</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Penggunaan</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Mode</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                            @if(auth()->user()->roles->first()?->name === 'admin')
                            <th class="px-6 py-3.5 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($promos as $promo)
                            <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                {{-- Number --}}
                                <td class="px-6 py-4 text-sm text-slate-400 dark:text-slate-500 w-12">
                                    {{ $promos->firstItem() + $loop->index }}
                                </td>

                                {{-- Code + Description --}}
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-semibold text-slate-800 dark:text-white text-sm font-mono tracking-wide">{{ $promo->code }}</p>
                                        @if($promo->description)
                                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5 line-clamp-1 max-w-xs">{{ $promo->description }}</p>
                                        @endif
                                    </div>
                                </td>

                                {{-- Discount --}}
                                <td class="px-6 py-4">
                                    <div>
                                        <span class="text-sm font-semibold text-slate-800 dark:text-white">{{ $promo->formatted_discount }}</span>
                                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">{{ $promo->discount_type_label }}</p>
                                    </div>
                                </td>

                                {{-- Period --}}
                                <td class="px-6 py-4">
                                    <div class="text-sm text-slate-600 dark:text-slate-300">
                                        <p>{{ $promo->valid_from->format('d M Y') }}</p>
                                        <p class="text-xs text-slate-400 dark:text-slate-500">s/d {{ $promo->valid_until->format('d M Y') }}</p>
                                    </div>
                                </td>

                                {{-- Usage --}}
                                <td class="px-6 py-4">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">
                                        {{ $promo->usage_count }} / {{ $promo->max_usage ?? '∞' }}
                                    </span>
                                </td>

                                {{-- Activation Mode --}}
                                <td class="px-6 py-4">
                                    @php
                                        $modeColor = $promo->activation_mode === 'auto' ? 'blue' : 'slate';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium
                                        bg-{{ $modeColor }}-50 dark:bg-{{ $modeColor }}-500/10
                                        text-{{ $modeColor }}-700 dark:text-{{ $modeColor }}-400">
                                        {{ $promo->activation_mode_label }}
                                    </span>
                                </td>

                                {{-- Status Toggle --}}
                                <td class="px-6 py-4">
                                    @if(auth()->user()->roles->first()?->name === 'admin')
                                    <form method="POST" action="{{ route('admin.promos.toggle-active', $promo) }}" class="inline">
                                        @csrf
                                        <button type="submit"
                                                title="{{ $promo->is_active ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan' }}"
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium cursor-pointer transition-all duration-200
                                                    {{ $promo->current_status === 'active'
                                                        ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 hover:bg-red-50 dark:hover:bg-red-500/10 hover:text-red-600 dark:hover:text-red-400'
                                                        : ($promo->current_status === 'expired'
                                                            ? 'bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 cursor-not-allowed'
                                                            : ($promo->current_status === 'scheduled'
                                                                ? 'bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400'
                                                                : ($promo->current_status === 'exhausted'
                                                                    ? 'bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 cursor-not-allowed'
                                                                    : 'bg-slate-50 dark:bg-slate-500/10 text-slate-600 dark:text-slate-400 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 hover:text-emerald-600 dark:hover:text-emerald-400'
                                                                )
                                                            )
                                                        )
                                                    }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $promo->current_status === 'active' ? 'bg-emerald-500 animate-pulse' : ($promo->current_status === 'expired' ? 'bg-red-500' : ($promo->current_status === 'scheduled' ? 'bg-blue-500' : ($promo->current_status === 'exhausted' ? 'bg-amber-500' : 'bg-slate-400'))) }}"></span>
                                            {{ $promo->status_label }}
                                        </button>
                                    </form>
                                    @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium
                                        bg-{{ $promo->status_color }}-50 dark:bg-{{ $promo->status_color }}-500/10
                                        text-{{ $promo->status_color }}-700 dark:text-{{ $promo->status_color }}-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-{{ $promo->status_color }}-500 {{ $promo->current_status === 'active' ? 'animate-pulse' : '' }}"></span>
                                        {{ $promo->status_label }}
                                    </span>
                                    @endif
                                </td>

                                {{-- Actions (Admin only) --}}
                                @if(auth()->user()->roles->first()?->name === 'admin')
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 lg:group-focus-within:opacity-100 transition-opacity">
                                        <a href="{{ route('admin.promos.edit', $promo) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-500/20 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.promos.destroy', $promo->id) }}" id="delete-promo-{{ $promo->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                    x-data
                                                    @click="$dispatch('open-global-confirm', { formId: 'delete-promo-{{ $promo->id }}', message: 'Yakin ingin menghapus kode promo ini?' })"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-500/10 rounded-lg hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors">
                                                <svg class="w-3.5 h-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($promos->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-4">
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Menampilkan {{ $promos->firstItem() }}–{{ $promos->lastItem() }} dari {{ $promos->total() }} promo
                    </p>
                    {{ $promos->links() }}
                </div>
            @else
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Total {{ $promos->total() }} kode promo
                    </p>
                </div>
            @endif
        @endif
    </div>

</x-layouts.app>
