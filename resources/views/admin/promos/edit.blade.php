<x-layouts.app :title="$title">

    {{-- Page Header --}}
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.promos.index') }}"
           class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Edit Promo</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Ubah detail kode promo <span class="font-mono font-semibold text-pink-500">{{ $promo->code }}</span>.</p>
        </div>
    </div>

    {{-- Usage Stats --}}
    @if($promo->usage_count > 0)
    <div class="max-w-2xl mb-6">
        <div class="bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 rounded-xl p-4 flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            <div>
                <p class="text-sm font-medium text-amber-800 dark:text-amber-200">Promo ini sudah digunakan {{ $promo->usage_count }}x</p>
                <p class="text-xs text-amber-600 dark:text-amber-400 mt-0.5">Perubahan pada tipe atau nilai diskon tidak akan mempengaruhi reservasi yang sudah menggunakan promo ini.</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Form --}}
    <div class="max-w-2xl">
        <form method="POST" action="{{ route('admin.promos.update', $promo) }}"
              class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 space-y-6"
              x-data="{
                  discountType: '{{ old('discount_type', $promo->discount_type) }}',
                  activationMode: '{{ old('activation_mode', $promo->activation_mode) }}',
                  generateCode() {
                      const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                      let code = 'PROMO-';
                      for (let i = 0; i < 6; i++) code += chars.charAt(Math.floor(Math.random() * chars.length));
                      this.$refs.codeInput.value = code;
                  }
              }">
            @csrf
            @method('PUT')

            {{-- Kode Promo --}}
            <div>
                <label for="code" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Kode Promo <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-2">
                    <input type="text" name="code" id="code" x-ref="codeInput"
                           value="{{ old('code', $promo->code) }}"
                           class="flex-1 px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-pink-500 transition uppercase"
                           style="text-transform: uppercase;"
                           required>
                    <button type="button" @click="generateCode()"
                            class="px-4 py-2.5 text-sm font-medium text-pink-600 dark:text-pink-400 bg-pink-50 dark:bg-pink-500/10 border border-pink-200 dark:border-pink-500/20 rounded-xl hover:bg-pink-100 dark:hover:bg-pink-500/20 transition-colors whitespace-nowrap">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Generate
                    </button>
                </div>
                @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Deskripsi --}}
            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Deskripsi
                </label>
                <textarea name="description" id="description" rows="3"
                          placeholder="Deskripsi singkat tentang promo ini..."
                          class="w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-pink-500 transition resize-none">{{ old('description', $promo->description) }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Tipe Diskon + Nilai --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="discount_type" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Tipe Diskon <span class="text-red-500">*</span>
                    </label>
                    <select name="discount_type" id="discount_type" x-model="discountType"
                            class="w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-pink-500 transition">
                        <option value="percentage" @selected(old('discount_type', $promo->discount_type) === 'percentage')>Persentase (%)</option>
                        <option value="fixed" @selected(old('discount_type', $promo->discount_type) === 'fixed')>Nominal Tetap (Rp)</option>
                    </select>
                    @error('discount_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="discount_value" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Nilai Diskon <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400" x-text="discountType === 'percentage' ? '%' : 'Rp'"></span>
                        <input type="number" name="discount_value" id="discount_value"
                               value="{{ old('discount_value', $promo->discount_value) }}"
                               min="1"
                               class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-pink-500 transition"
                               required>
                    </div>
                    @error('discount_value') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Maks Diskon (for percentage) --}}
            <div x-show="discountType === 'percentage'" x-transition>
                <label for="max_discount" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Maks Potongan (Rp)
                    <span class="text-slate-400 font-normal">— opsional</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400">Rp</span>
                    <input type="number" name="max_discount" id="max_discount"
                           value="{{ old('max_discount', $promo->max_discount) }}"
                           placeholder="Contoh: 50000"
                           min="0"
                           class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-pink-500 transition">
                </div>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Batas maksimal potongan dalam Rupiah. Kosongkan jika tidak dibatasi.</p>
                @error('max_discount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Periode --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="valid_from" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Berlaku Dari <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="valid_from" id="valid_from"
                           value="{{ old('valid_from', $promo->valid_from->format('Y-m-d\TH:i')) }}"
                           class="w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-pink-500 transition"
                           required>
                    @error('valid_from') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="valid_until" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Berlaku Sampai <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="valid_until" id="valid_until"
                           value="{{ old('valid_until', $promo->valid_until->format('Y-m-d\TH:i')) }}"
                           class="w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-pink-500 transition"
                           required>
                    @error('valid_until') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Maks Penggunaan --}}
            <div>
                <label for="max_usage" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Batas Penggunaan
                    <span class="text-slate-400 font-normal">— opsional</span>
                </label>
                <input type="number" name="max_usage" id="max_usage"
                       value="{{ old('max_usage', $promo->max_usage) }}"
                       placeholder="Kosongkan jika unlimited"
                       min="1"
                       class="w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-pink-500 transition">
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Sudah digunakan: <span class="font-semibold">{{ $promo->usage_count }}x</span></p>
                @error('max_usage') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Mode Aktivasi --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                    Mode Aktivasi <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="relative flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all duration-200"
                           :class="activationMode === 'manual' ? 'border-pink-500 bg-pink-50/50 dark:bg-pink-500/5' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600'">
                        <input type="radio" name="activation_mode" value="manual" x-model="activationMode" class="sr-only">
                        <div class="flex-shrink-0 w-5 h-5 mt-0.5 rounded-full border-2 flex items-center justify-center transition-colors"
                             :class="activationMode === 'manual' ? 'border-pink-500' : 'border-slate-300 dark:border-slate-600'">
                            <span class="w-2.5 h-2.5 rounded-full bg-pink-500 transition-transform"
                                  :class="activationMode === 'manual' ? 'scale-100' : 'scale-0'"></span>
                        </div>
                        <div>
                            <p class="font-medium text-sm text-slate-800 dark:text-white">Manual</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Anda mengaktifkan/menonaktifkan promo secara manual.</p>
                        </div>
                    </label>
                    <label class="relative flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all duration-200"
                           :class="activationMode === 'auto' ? 'border-pink-500 bg-pink-50/50 dark:bg-pink-500/5' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600'">
                        <input type="radio" name="activation_mode" value="auto" x-model="activationMode" class="sr-only">
                        <div class="flex-shrink-0 w-5 h-5 mt-0.5 rounded-full border-2 flex items-center justify-center transition-colors"
                             :class="activationMode === 'auto' ? 'border-pink-500' : 'border-slate-300 dark:border-slate-600'">
                            <span class="w-2.5 h-2.5 rounded-full bg-pink-500 transition-transform"
                                  :class="activationMode === 'auto' ? 'scale-100' : 'scale-0'"></span>
                        </div>
                        <div>
                            <p class="font-medium text-sm text-slate-800 dark:text-white">Otomatis</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Promo aktif/nonaktif otomatis berdasarkan tanggal berlaku.</p>
                        </div>
                    </label>
                </div>
                @error('activation_mode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Status Aktif (manual mode only) --}}
            <div x-show="activationMode === 'manual'" x-transition>
                <label class="flex items-center gap-3 cursor-pointer group">
                    <div class="relative">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $promo->is_active) ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 dark:bg-slate-700 rounded-full peer-checked:bg-pink-500 transition-colors"></div>
                        <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow-sm transition-transform peer-checked:translate-x-5"></div>
                    </div>
                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Aktifkan promo</span>
                </label>
            </div>

            {{-- Submit --}}
            <div class="flex items-center gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-[#1e3a5f] to-[#e91e8c] text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:shadow-pink-500/25 hover:-translate-y-0.5 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.promos.index') }}"
                   class="px-5 py-2.5 text-sm text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>

</x-layouts.app>
