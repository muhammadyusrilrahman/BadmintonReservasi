<x-app-layout>
    <x-slot name="title">Edit Ulasan</x-slot>

    <div class="max-w-2xl mx-auto space-y-6">

        {{-- Header --}}
        <div>
            <a href="{{ route('customer.reservations.show', $reservation) }}"
               class="inline-flex items-center gap-1.5 text-sm text-slate-500 dark:text-slate-400 hover:text-pink-500 dark:hover:text-pink-400 transition-colors mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali ke Detail Reservasi
            </a>
            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Edit Ulasan</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Perbarui ulasan Anda untuk <strong>{{ $reservation->court->name }}</strong></p>
        </div>

        {{-- Card Info Reservasi --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-pink-500 to-pink-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <p class="font-semibold text-slate-800 dark:text-white">{{ $reservation->court->name }}</p>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    {{ $reservation->date->translatedFormat('d F Y') }} &bull;
                    {{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($reservation->end_time)->format('H:i') }}
                </p>
            </div>
        </div>

        {{-- Info: balasan admin akan direset --}}
        @if($review->admin_reply)
            <div class="flex items-start gap-3 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl text-sm text-amber-700 dark:text-amber-400">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <p>Jika Anda menyimpan perubahan, <strong>balasan admin akan dihapus</strong> karena konten ulasan berubah.</p>
            </div>
        @endif

        {{-- Form Edit --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden"
             x-data="{ rating: {{ old('rating', $review->rating) }}, hovered: 0 }">

            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                <h3 class="font-semibold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    Penilaian Anda
                </h3>
            </div>

            <form method="POST" action="{{ route('customer.reservations.review.update', $reservation) }}" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                {{-- Pilih Rating Bintang --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                        Rating <span class="text-red-500">*</span>
                    </label>
                    <input type="hidden" name="rating" :value="rating">

                    <div class="flex items-center gap-2">
                        @for ($i = 1; $i <= 5; $i++)
                            <button type="button"
                                @click="rating = {{ $i }}"
                                @mouseenter="hovered = {{ $i }}"
                                @mouseleave="hovered = 0"
                                class="transition-transform hover:scale-110 focus:outline-none">
                                <svg class="w-10 h-10 transition-colors duration-150"
                                    :class="(hovered || rating) >= {{ $i }} ? 'text-amber-400' : 'text-slate-300 dark:text-slate-600'"
                                    fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            </button>
                        @endfor
                        <span class="ml-3 text-sm text-slate-500 dark:text-slate-400"
                            x-text="['', 'Sangat Buruk', 'Buruk', 'Cukup', 'Bagus', 'Sangat Bagus'][hovered || rating] || 'Pilih rating'">
                        </span>
                    </div>

                    @error('rating')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Komentar --}}
                <div>
                    <label for="comment" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Komentar <span class="text-slate-400 font-normal">(opsional)</span>
                    </label>
                    <textarea name="comment" id="comment" rows="5" maxlength="1000"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all resize-none"
                        placeholder="Ceritakan pengalaman Anda...">{{ old('comment', $review->comment) }}</textarea>
                    @error('comment')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tombol --}}
                <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700">
                    <a href="{{ route('customer.reservations.show', $reservation) }}"
                       class="text-sm text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        :disabled="rating === 0"
                        :class="rating === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:from-pink-600 hover:to-pink-700 hover:shadow-pink-500/40'"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-pink-500 to-pink-600 text-white text-sm font-semibold rounded-xl shadow-md shadow-pink-500/25 transition-all duration-200 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>
