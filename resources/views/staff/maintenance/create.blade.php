<x-layouts.app :title="$title">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $title }}</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Isi detail maintenance lapangan yang akan dijadwalkan.</p>
        </div>
        <a href="{{ route('staff.maintenance.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
    </div>

    {{-- Form --}}
    <div class="max-w-2xl">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50">
                <h3 class="text-base font-bold text-slate-800 dark:text-white">Form Jadwal Maintenance</h3>
            </div>

            <form method="POST" action="{{ route('staff.maintenance.store') }}" class="p-6 space-y-5">
                @csrf

                {{-- Lapangan --}}
                <div>
                    <label for="court_id" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Lapangan <span class="text-red-500">*</span></label>
                    <select name="court_id" id="court_id" required
                            class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-colors">
                        <option value="">-- Pilih Lapangan --</option>
                        @foreach($courts as $court)
                            <option value="{{ $court->id }}" {{ old('court_id') == $court->id ? 'selected' : '' }}>
                                {{ $court->name }} ({{ $court->type_label }})
                                @if(!$court->is_active) — ⚠️ Tidak Aktif @endif
                            </option>
                        @endforeach
                    </select>
                    @error('court_id')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Judul --}}
                <div>
                    <label for="title" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Judul Maintenance <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                           placeholder="Contoh: Perbaikan Jaring, Cat Ulang Garis, dll."
                           class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-colors">
                    @error('title')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label for="description" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Deskripsi <span class="text-slate-400">(Opsional)</span></label>
                    <textarea name="description" id="description" rows="4"
                              placeholder="Jelaskan detail pekerjaan maintenance yang perlu dilakukan..."
                              class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-colors resize-none">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tanggal --}}
                <div>
                    <label for="scheduled_date" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Tanggal Dijadwalkan <span class="text-red-500">*</span></label>
                    <input type="date" name="scheduled_date" id="scheduled_date" value="{{ old('scheduled_date', now()->format('Y-m-d')) }}" required
                           min="{{ now()->format('Y-m-d') }}"
                           class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-colors">
                    @error('scheduled_date')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-[#1e3a5f] to-[#e91e8c] text-white text-sm font-bold rounded-xl hover:shadow-lg hover:shadow-pink-500/25 transition-all duration-200">
                        Simpan Jadwal
                    </button>
                    <a href="{{ route('staff.maintenance.index') }}"
                       class="px-6 py-2.5 text-sm font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

</x-layouts.app>
