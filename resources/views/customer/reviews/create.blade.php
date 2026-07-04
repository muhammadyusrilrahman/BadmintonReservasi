<x-app-layout>
    <x-slot name="title">Tulis Ulasan</x-slot>

    <div class="max-w-2xl mx-auto space-y-6">

        {{-- Header --}}
        <div>
            <a href="{{ route('customer.reservations.show', $reservation) }}"
               class="inline-flex items-center gap-1.5 text-sm text-slate-500 dark:text-slate-400 hover:text-pink-500 dark:hover:text-pink-400 transition-colors mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali ke Detail Reservasi
            </a>
            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Tulis Ulasan</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Bagikan pengalaman bermain Anda di <strong>{{ $reservation->court->name }}</strong></p>
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

        {{-- Form Ulasan --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden"
             x-data="{ rating: 0, hovered: 0 }">

            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                <h3 class="font-semibold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    Penilaian Anda
                </h3>
            </div>

            <form method="POST" action="{{ route('customer.reservations.review.store', $reservation) }}" class="p-6 space-y-6">
                @csrf

                {{-- Pilih Rating Bintang --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                        Rating <span class="text-red-500">*</span>
                    </label>
                    <input type="hidden" name="rating" :value="rating" id="rating-input">

                    {{-- Bintang interaktif --}}
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
                            Pilih rating
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
                    <textarea name="comment" id="comment" rows="5"
                        maxlength="1000"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all resize-none"
                        placeholder="Ceritakan pengalaman Anda bermain di sini. Bagaimana kondisi lapangan, fasilitas, pelayanan, dll.">{{ old('comment') }}</textarea>
                    <p class="mt-1 text-xs text-slate-400 text-right" x-data="{ count: {{ strlen(old('comment', '')) }} }" x-text="count + '/1000'" @keyup.window="count = $el.previousElementSibling.value.length"></p>
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
                        Kirim Ulasan
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>
