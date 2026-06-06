<x-layouts.app :title="$title">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm mb-6">
        <a href="{{ route('admin.reservations.index') }}" class="text-slate-500 dark:text-slate-400 hover:text-pink-600 dark:hover:text-pink-400 transition-colors">Kelola Reservasi</a>
        <svg class="w-4 h-4 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-slate-800 dark:text-white font-medium">Buat Pesanan</span>
    </nav>

    <div class="max-w-2xl">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">

            {{-- Header --}}
            <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 bg-gradient-to-r from-[#1e3a5f]/5 to-transparent">
                <h1 class="text-lg font-bold text-slate-800 dark:text-white">Buat Pesanan Manual (Kasir)</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Catat pesanan offline. Status pesanan akan langsung dikonfirmasi dan lunas secara tunai.</p>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('admin.reservations.store') }}" class="p-6 space-y-6">
                @csrf

                {{-- User Selection --}}
                <div>
                    <label for="user_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Pelanggan <span class="text-red-500">*</span>
                    </label>
                    <select id="user_id" name="user_id" required
                            class="w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border
                                {{ $errors->has('user_id') ? 'border-red-400 dark:border-red-500 focus:ring-red-500' : 'border-slate-200 dark:border-slate-700 focus:ring-pink-500/50 focus:border-pink-500' }}
                                rounded-xl text-slate-800 dark:text-white focus:outline-none focus:ring-2 transition">
                        <option value="">-- Pilih Pelanggan --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Court Selection --}}
                <div>
                    <label for="court_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Lapangan <span class="text-red-500">*</span>
                    </label>
                    <select id="court_id" name="court_id" required
                            class="w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border
                                {{ $errors->has('court_id') ? 'border-red-400 dark:border-red-500 focus:ring-red-500' : 'border-slate-200 dark:border-slate-700 focus:ring-pink-500/50 focus:border-pink-500' }}
                                rounded-xl text-slate-800 dark:text-white focus:outline-none focus:ring-2 transition">
                        <option value="">-- Pilih Lapangan --</option>
                        @foreach($courts as $court)
                            <option value="{{ $court->id }}" @selected(old('court_id') == $court->id)>{{ $court->name }} (Rp {{ number_format($court->price_per_hour, 0, ',', '.') }}/jam)</option>
                        @endforeach
                    </select>
                    @error('court_id')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Schedule Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700">
                    {{-- Date --}}
                    <div>
                        <label for="date" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Tanggal <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required min="{{ date('Y-m-d') }}"
                               class="w-full px-4 py-2.5 text-sm bg-white dark:bg-slate-900 border
                                   {{ $errors->has('date') ? 'border-red-400 dark:border-red-500 focus:ring-red-500' : 'border-slate-200 dark:border-slate-700 focus:ring-pink-500/50 focus:border-pink-500' }}
                                   rounded-xl text-slate-800 dark:text-white focus:outline-none focus:ring-2 transition">
                        @error('date')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Start Time --}}
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Waktu Mulai <span class="text-red-500">*</span>
                        </label>
                        <input type="time" id="start_time" name="start_time" value="{{ old('start_time') }}" required step="3600"
                               class="w-full px-4 py-2.5 text-sm bg-white dark:bg-slate-900 border
                                   {{ $errors->has('start_time') ? 'border-red-400 dark:border-red-500 focus:ring-red-500' : 'border-slate-200 dark:border-slate-700 focus:ring-pink-500/50 focus:border-pink-500' }}
                                   rounded-xl text-slate-800 dark:text-white focus:outline-none focus:ring-2 transition">
                        @error('start_time')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Duration --}}
                    <div>
                        <label for="duration_hours" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Durasi (Jam) <span class="text-red-500">*</span>
                        </label>
                        <select id="duration_hours" name="duration_hours" required
                                class="w-full px-4 py-2.5 text-sm bg-white dark:bg-slate-900 border
                                    {{ $errors->has('duration_hours') ? 'border-red-400 dark:border-red-500 focus:ring-red-500' : 'border-slate-200 dark:border-slate-700 focus:ring-pink-500/50 focus:border-pink-500' }}
                                    rounded-xl text-slate-800 dark:text-white focus:outline-none focus:ring-2 transition">
                            @for($i=1; $i<=5; $i++)
                                <option value="{{ $i }}" @selected(old('duration_hours') == $i)>{{ $i }} Jam</option>
                            @endfor
                        </select>
                        @error('duration_hours')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Notes --}}
                <div>
                    <label for="notes" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Catatan Internal <span class="text-slate-400 text-xs font-normal">(opsional)</span>
                    </label>
                    <textarea id="notes" name="notes" rows="2"
                              placeholder="Keterangan tambahan..."
                              class="w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border
                                  {{ $errors->has('notes') ? 'border-red-400 dark:border-red-500 focus:ring-red-500' : 'border-slate-200 dark:border-slate-700 focus:ring-pink-500/50 focus:border-pink-500' }}
                                  rounded-xl text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 transition resize-none">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <div class="flex items-center gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                    <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-[#1e3a5f] to-[#e91e8c] text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:shadow-pink-500/25 hover:-translate-y-0.5 transition-all duration-200">
                        Buat Pesanan & Tandai Lunas
                    </button>
                    <a href="{{ route('admin.reservations.index') }}"
                       class="px-6 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
