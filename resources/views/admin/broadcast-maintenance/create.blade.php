<x-layouts.app :title="$title">

    {{-- Page Header --}}
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.broadcast-maintenance.index') }}"
           class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Buat Broadcast Baru</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Kirim informasi pemeliharaan sistem atau lapangan langsung ke email dan inbox customer.</p>
        </div>
    </div>

    {{-- Form --}}
    <div class="max-w-3xl">
        <form method="POST" action="{{ route('admin.broadcast-maintenance.store') }}"
              class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 space-y-6"
              x-data="{
                  maintenanceType: '{{ old('type', 'system') }}',
                  targetType: '{{ old('target_type', 'all') }}'
              }">
            @csrf

            {{-- Tipe Maintenance --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">
                    Tipe Maintenance <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label class="relative flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all duration-200"
                           :class="maintenanceType === 'system' ? 'border-pink-500 bg-pink-50/50 dark:bg-pink-500/5' : 'border-slate-200 dark:border-slate-700 hover:border-slate-350 dark:hover:border-slate-600'">
                        <input type="radio" name="type" value="system" x-model="maintenanceType" class="sr-only">
                        <div class="flex-shrink-0 w-5 h-5 mt-0.5 rounded-full border-2 flex items-center justify-center transition-colors"
                             :class="maintenanceType === 'system' ? 'border-pink-500' : 'border-slate-300 dark:border-slate-600'">
                            <span class="w-2.5 h-2.5 rounded-full bg-pink-500 transition-transform"
                                  :class="maintenanceType === 'system' ? 'scale-100' : 'scale-0'"></span>
                        </div>
                        <div>
                            <p class="font-semibold text-sm text-slate-800 dark:text-white">💻 Pemeliharaan Sistem</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Pemeliharaan global aplikasi, upgrade server, atau pembaruan database.</p>
                        </div>
                    </label>
                    <label class="relative flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all duration-200"
                           :class="maintenanceType === 'court' ? 'border-pink-500 bg-pink-50/50 dark:bg-pink-500/5' : 'border-slate-200 dark:border-slate-700 hover:border-slate-350 dark:hover:border-slate-600'">
                        <input type="radio" name="type" value="court" x-model="maintenanceType" class="sr-only">
                        <div class="flex-shrink-0 w-5 h-5 mt-0.5 rounded-full border-2 flex items-center justify-center transition-colors"
                             :class="maintenanceType === 'court' ? 'border-pink-500' : 'border-slate-300 dark:border-slate-600'">
                            <span class="w-2.5 h-2.5 rounded-full bg-pink-500 transition-transform"
                                  :class="maintenanceType === 'court' ? 'scale-100' : 'scale-0'"></span>
                        </div>
                        <div>
                            <p class="font-semibold text-sm text-slate-800 dark:text-white">🛠️ Pemeliharaan Lapangan</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Perbaikan fisik, penggantian lampu, pembersihan karpet lapangan tertentu.</p>
                        </div>
                    </label>
                </div>
                @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Pilih Lapangan (court-only) --}}
            <div x-show="maintenanceType === 'court'" x-transition class="bg-slate-50 dark:bg-slate-800/40 p-4 rounded-xl border border-slate-100 dark:border-slate-800">
                <label for="court_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Pilih Lapangan <span class="text-red-500">*</span>
                </label>
                <select name="court_id" id="court_id"
                        class="w-full px-4 py-2.5 text-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-pink-500 transition">
                    <option value="">-- Pilih Lapangan --</option>
                    @foreach($courts as $court)
                        <option value="{{ $court->id }}" @selected(old('court_id') == $court->id)>{{ $court->name }} ({{ $court->type_label }})</option>
                    @endforeach
                </select>
                @error('court_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Jadwal & Estimasi Durasi --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="scheduled_date" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Tanggal Maintenance <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="scheduled_date" id="scheduled_date"
                           value="{{ old('scheduled_date', date('Y-m-d')) }}"
                           class="w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-pink-500 transition"
                           required>
                    @error('scheduled_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="duration" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Estimasi Durasi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="duration" id="duration"
                           value="{{ old('duration') }}"
                           placeholder="Contoh: 3 Jam, 1 Hari, 30 Menit"
                           class="w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-pink-500 transition"
                           required>
                    @error('duration') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Target Penerima --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">
                    Target Penerima Notifikasi <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label class="relative flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all duration-200"
                           :class="targetType === 'all' ? 'border-pink-500 bg-pink-50/50 dark:bg-pink-500/5' : 'border-slate-200 dark:border-slate-700 hover:border-slate-350 dark:hover:border-slate-600'">
                        <input type="radio" name="target_type" value="all" x-model="targetType" class="sr-only">
                        <div class="flex-shrink-0 w-5 h-5 mt-0.5 rounded-full border-2 flex items-center justify-center transition-colors"
                             :class="targetType === 'all' ? 'border-pink-500' : 'border-slate-300 dark:border-slate-600'">
                            <span class="w-2.5 h-2.5 rounded-full bg-pink-500 transition-transform"
                                  :class="targetType === 'all' ? 'scale-100' : 'scale-0'"></span>
                        </div>
                        <div>
                            <p class="font-semibold text-sm text-slate-800 dark:text-white">📢 Semua Pelanggan</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Kirim informasi broadcast ke seluruh pelanggan yang terdaftar.</p>
                        </div>
                    </label>
                    <label class="relative flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all duration-200"
                           :class="targetType === 'affected' ? 'border-pink-500 bg-pink-50/50 dark:bg-pink-500/5' : 'border-slate-200 dark:border-slate-700 hover:border-slate-350 dark:hover:border-slate-600'">
                        <input type="radio" name="target_type" value="affected" x-model="targetType" class="sr-only">
                        <div class="flex-shrink-0 w-5 h-5 mt-0.5 rounded-full border-2 flex items-center justify-center transition-colors"
                             :class="targetType === 'affected' ? 'border-pink-500' : 'border-slate-300 dark:border-slate-600'">
                            <span class="w-2.5 h-2.5 rounded-full bg-pink-500 transition-transform"
                                  :class="targetType === 'affected' ? 'scale-100' : 'scale-0'"></span>
                        </div>
                        <div>
                            <p class="font-semibold text-sm text-slate-800 dark:text-white">⚠️ Pelanggan Terdampak</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Hanya kirim ke pelanggan yang memiliki booking aktif di tanggal/lapangan terpilih.</p>
                        </div>
                    </label>
                </div>
                @error('target_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <hr class="border-slate-200 dark:border-slate-800 my-4" />

            {{-- Judul / Subjek --}}
            <div>
                <label for="title" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Subjek / Judul Email <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title"
                       value="{{ old('title') }}"
                       placeholder="Contoh: Pemberitahuan Maintenance Sistem / Perawatan Lapangan 1"
                       class="w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-pink-500 transition"
                       required>
                @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Deskripsi --}}
            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Detail Informasi Pemeliharaan <span class="text-red-500">*</span>
                </label>
                <textarea name="description" id="description" rows="6"
                          placeholder="Tuliskan pesan detail pemeliharaan Anda di sini..."
                          class="w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-pink-500 transition"
                          required>{{ old('description') }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Submit & Cancel --}}
            <div class="flex items-center gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-[#1e3a5f] to-[#e91e8c] text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:shadow-pink-500/25 hover:-translate-y-0.5 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Kirim Broadcast
                </button>
                <a href="{{ route('admin.broadcast-maintenance.index') }}"
                   class="px-5 py-2.5 text-sm text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>

</x-layouts.app>
