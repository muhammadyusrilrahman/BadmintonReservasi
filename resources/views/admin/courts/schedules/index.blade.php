<x-layouts.app :title="$title">

    {{-- Breadcrumb & Header --}}
    <nav class="flex items-center gap-2 text-sm mb-6">
        <a href="{{ route('admin.courts.index') }}" class="text-slate-500 dark:text-slate-400 hover:text-pink-600 dark:hover:text-pink-400">Kelola Lapangan</a>
        <span class="text-slate-400">/</span>
        <span class="text-slate-800 dark:text-white font-medium">Jadwal: {{ $court->name }}</span>
    </nav>

    <div class="flex flex-col md:flex-row gap-6" x-data="{
        selected: [],
        selectAll: false,
        
        toggleAll() {
            if (this.selectAll) {
                this.selected = {{ $schedules->pluck('id')->toJson() }};
            } else {
                this.selected = [];
            }
        },
        confirmDelete(formId, message) {
            $dispatch('open-global-confirm', { formId: formId, message: message });
        }
    }">
        
        {{-- Form Create Schedule --}}
        <div class="w-full md:w-1/3">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 sticky top-6">
                <h2 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Buat Jadwal / Slot</h2>
                
                <form action="{{ route('admin.courts.schedules.store', $court->id) }}" method="POST" class="space-y-4">
                    @csrf
                    
                    {{-- Days --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Hari</label>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($days as $key => $label)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="day_of_week[]" value="{{ $key }}" class="rounded text-pink-600 border-slate-300 focus:ring-pink-500" checked>
                                    <span class="text-sm text-slate-600 dark:text-slate-400">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('day_of_week')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Time Range --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Jam Mulai</label>
                            <input type="time" name="start_time" value="08:00" required class="w-full rounded-xl border-slate-200 focus:border-pink-500 focus:ring-pink-500 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Jam Selesai</label>
                            <input type="time" name="end_time" value="18:00" required class="w-full rounded-xl border-slate-200 focus:border-pink-500 focus:ring-pink-500 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                        </div>
                    </div>

                    {{-- Price --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Harga (Rp)</label>
                        <input type="number" name="price" value="{{ $court->price_per_hour ?? 50000 }}" required min="0" class="w-full rounded-xl border-slate-200 focus:border-pink-500 focus:ring-pink-500 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                    </div>

                    {{-- Generate Hourly --}}
                    <label class="flex items-start gap-3 p-3 bg-blue-50 dark:bg-blue-500/10 rounded-xl cursor-pointer">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="generate_hourly" value="1" class="w-4 h-4 rounded text-blue-600 border-blue-300 focus:ring-blue-500" checked>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-blue-900 dark:text-blue-300">Generate Slot Per Jam</p>
                            <p class="text-xs text-blue-700 dark:text-blue-400 mt-1">Otomatis memecah rentang waktu menjadi slot 1 jam-an (cth: 08:00-09:00, 09:00-10:00).</p>
                        </div>
                    </label>

                    <button type="submit" class="w-full px-4 py-2 bg-pink-600 hover:bg-pink-700 text-white font-medium rounded-xl transition-colors">
                        Simpan Jadwal
                    </button>
                </form>
            </div>
        </div>

        {{-- List of Schedules --}}
        <div class="w-full md:w-2/3">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <h3 class="font-bold text-slate-800 dark:text-white">Daftar Jadwal Lapangan</h3>
                    
                    @if(!$schedules->isEmpty())
                        <div class="flex items-center gap-3">
                            <label class="flex items-center gap-2 cursor-pointer text-sm text-slate-600 dark:text-slate-400">
                                <input type="checkbox" x-model="selectAll" @change="toggleAll" class="rounded text-pink-600 border-slate-300 focus:ring-pink-500">
                                Pilih Semua
                            </label>

                            <form action="{{ route('admin.courts.schedules.destroy-bulk', $court->id) }}" method="POST" class="inline" id="form-delete-selected" x-show="selected.length > 0" style="display: none;">
                                @csrf
                                @method('DELETE')
                                <template x-for="id in selected" :key="id">
                                    <input type="hidden" name="schedule_ids[]" :value="id">
                                </template>
                                <button type="button" @click="confirmDelete('form-delete-selected', 'Hapus ' + selected.length + ' jadwal yang dipilih?')" class="px-3 py-1.5 text-xs font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                                    Hapus Terpilih (<span x-text="selected.length"></span>)
                                </button>
                            </form>

                            <form action="{{ route('admin.courts.schedules.destroy-bulk', $court->id) }}" method="POST" class="inline" id="form-delete-all">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="delete_all" value="1">
                                <button type="button" @click="confirmDelete('form-delete-all', 'Yakin ingin menghapus SEMUA jadwal untuk lapangan ini? Tindakan ini tidak dapat dibatalkan.')" class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 dark:bg-red-500/10 rounded-lg hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors">
                                    Hapus Semua
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
                
                <div class="p-6">
                    @if($schedules->isEmpty())
                        <div class="text-center py-8">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 mb-4">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <h4 class="text-lg font-medium text-slate-800 dark:text-white">Belum Ada Jadwal</h4>
                            <p class="text-slate-500 dark:text-slate-400 mt-1">Silakan buat jadwal baru menggunakan form di samping.</p>
                        </div>
                    @else
                        @php
                            $groupedSchedules = $schedules->groupBy('day_of_week');
                        @endphp

                        <div class="space-y-8">
                            @foreach([1, 2, 3, 4, 5, 6, 0] as $dayId)
                                @if(isset($groupedSchedules[$dayId]))
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-800 dark:text-white mb-3 flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-pink-500"></span>
                                            {{ $days[$dayId] }}
                                        </h4>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                            @foreach($groupedSchedules[$dayId] as $slot)
                                                <label class="flex flex-col p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 relative group cursor-pointer transition-colors" :class="selected.includes({{ $slot->id }}) ? 'border-pink-500 ring-1 ring-pink-500' : 'hover:border-slate-300 dark:hover:border-slate-600'">
                                                    <div class="flex justify-between items-start mb-2">
                                                        <div class="flex items-center gap-2">
                                                            <input type="checkbox" x-model="selected" value="{{ $slot->id }}" class="rounded text-pink-600 border-slate-300 focus:ring-pink-500" @change="selectAll = selected.length === {{ $schedules->count() }}">
                                                            <span class="text-sm font-semibold text-slate-800 dark:text-white">
                                                                {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}
                                                            </span>
                                                        </div>
                                                        <form action="{{ route('admin.courts.schedules.destroy', [$court->id, $slot->id]) }}" method="POST" id="form-delete-{{ $slot->id }}" class="opacity-0 group-hover:opacity-100 transition-opacity">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" @click.prevent="confirmDelete('form-delete-{{ $slot->id }}', 'Hapus slot {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}?')" class="text-red-500 hover:text-red-600 p-1">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                    <span class="text-xs font-medium text-pink-600 dark:text-pink-400 pl-6">Rp {{ number_format($slot->price, 0, ',', '.') }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>
