<x-app-layout>
    <x-slot name="title">Manajemen Ulasan</x-slot>

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-800 dark:text-white">Manajemen Ulasan</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Kelola ulasan pelanggan — balas, sembunyikan, atau hapus.</p>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="flex items-center gap-3 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded-xl text-emerald-700 dark:text-emerald-400 text-sm">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Statistik --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach([
                ['label' => 'Total Ulasan',   'value' => $stats['total'],              'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z', 'color' => 'blue'],
                ['label' => 'Rata-rata',      'value' => ($stats['average'] ?: '—') . ($stats['average'] ? ' ★' : ''), 'icon' => 'M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z', 'color' => 'amber', 'fill' => true],
                ['label' => 'Disembunyikan', 'value' => $stats['hidden'],             'icon' => 'M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21', 'color' => 'slate'],
                ['label' => 'Sudah Dibalas', 'value' => $stats['replied'],            'icon' => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6', 'color' => 'emerald'],
            ] as $stat)
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-{{ $stat['color'] }}-100 dark:bg-{{ $stat['color'] }}-500/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400"
                            @if(!empty($stat['fill'])) fill="currentColor" @else fill="none" stroke="currentColor" @endif
                            viewBox="0 0 24 24">
                            <path @if(empty($stat['fill'])) stroke-linecap="round" stroke-linejoin="round" stroke-width="2" @endif d="{{ $stat['icon'] }}"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-slate-800 dark:text-white">{{ $stat['value'] }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $stat['label'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Filter --}}
        <form method="GET" class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
            <div class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-36">
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Lapangan</label>
                    <select name="court_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-pink-500">
                        <option value="">Semua Lapangan</option>
                        @foreach($courts as $court)
                            <option value="{{ $court->id }}" {{ request('court_id') == $court->id ? 'selected' : '' }}>{{ $court->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-28">
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Rating</label>
                    <select name="rating" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-pink-500">
                        <option value="">Semua Rating</option>
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} Bintang</option>
                        @endfor
                    </select>
                </div>
                <div class="flex-1 min-w-36">
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-pink-500">
                        <option value="">Semua Status</option>
                        <option value="visible" {{ request('status') === 'visible' ? 'selected' : '' }}>Tampil</option>
                        <option value="hidden"  {{ request('status') === 'hidden'  ? 'selected' : '' }}>Disembunyikan</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-pink-500 hover:bg-pink-600 text-white text-sm font-semibold rounded-xl transition-colors">Filter</button>
                    <a href="{{ route('admin.reviews.index') }}" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-sm font-semibold rounded-xl transition-colors">Reset</a>
                </div>
            </div>
        </form>

        {{-- Tabel Ulasan --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            @if($reviews->isEmpty())
                <div class="text-center py-16">
                    <svg class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    <p class="text-slate-500 dark:text-slate-400 text-sm">Belum ada ulasan ditemukan.</p>
                </div>
            @else
                <div class="divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach($reviews as $review)
                        <div class="p-5 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors" x-data="{ showReplyForm: false }">
                            <div class="flex flex-col sm:flex-row sm:items-start gap-4">

                                {{-- Avatar --}}
                                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-pink-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm">
                                    {{ strtoupper(substr($review->user->name ?? '?', 0, 1)) }}
                                </div>

                                {{-- Konten --}}
                                <div class="flex-1 min-w-0 space-y-2">
                                    {{-- Header --}}
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="font-semibold text-slate-800 dark:text-white text-sm">{{ $review->user->name ?? 'Pengguna dihapus' }}</span>
                                        <span class="text-slate-400">·</span>
                                        <span class="text-xs text-slate-500 dark:text-slate-400">{{ $review->court->name ?? '—' }}</span>
                                        <span class="text-slate-400">·</span>
                                        <span class="text-xs text-slate-400">{{ $review->created_at->diffForHumans() }}</span>
                                        @if($review->is_hidden)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 text-xs rounded-full">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                                Tersembunyi
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Rating --}}
                                    <div class="flex items-center gap-0.5">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-amber-400' : 'text-slate-200 dark:text-slate-600' }}"
                                                fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                        @endfor
                                        <span class="ml-1 text-xs text-slate-500">{{ $review->rating }}/5</span>
                                    </div>

                                    {{-- Komentar --}}
                                    @if($review->comment)
                                        <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed">"{{ $review->comment }}"</p>
                                    @endif

                                    {{-- Balasan admin --}}
                                    @if($review->admin_reply)
                                        <div class="ml-3 pl-3 border-l-2 border-pink-300 dark:border-pink-700 mt-2">
                                            <p class="text-xs font-semibold text-pink-600 dark:text-pink-400 mb-0.5">
                                                Balasan Admin · {{ $review->admin_reply_at?->translatedFormat('d M Y') }}
                                            </p>
                                            <p class="text-sm text-slate-600 dark:text-slate-400">{{ $review->admin_reply }}</p>
                                        </div>
                                    @endif

                                    {{-- Form Balas (toggle) --}}
                                    <div x-show="showReplyForm" x-collapse class="mt-2">
                                        <form method="POST" action="{{ route('admin.reviews.reply', $review) }}">
                                            @csrf
                                            <div class="flex gap-2">
                                                <textarea name="admin_reply" rows="2" required maxlength="1000"
                                                    class="flex-1 px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-pink-500 resize-none"
                                                    placeholder="Tulis balasan Anda...">{{ $review->admin_reply }}</textarea>
                                                <button type="submit" class="px-4 py-2 bg-pink-500 hover:bg-pink-600 text-white text-sm font-semibold rounded-xl transition-colors flex-shrink-0">
                                                    Kirim
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                {{-- Aksi --}}
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    {{-- Balas --}}
                                    <button @click="showReplyForm = !showReplyForm"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                        Balas
                                    </button>

                                    {{-- Toggle Hidden --}}
                                    <form method="POST" action="{{ route('admin.reviews.toggle-hidden', $review) }}" class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border transition-colors
                                            {{ $review->is_hidden
                                                ? 'border-emerald-200 dark:border-emerald-700 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/30'
                                                : 'border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                                            @if($review->is_hidden)
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                Tampilkan
                                            @else
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                                Sembunyikan
                                            @endif
                                        </button>
                                    </form>

                                    {{-- Hapus --}}
                                    <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" class="inline"
                                        onsubmit="return confirm('Hapus ulasan ini? Tindakan ini tidak dapat dibatalkan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($reviews->hasPages())
                    <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-700">
                        {{ $reviews->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
