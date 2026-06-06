<x-layouts.app :title="$title">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm mb-6" aria-label="Breadcrumb">
        <a href="{{ route('customer.reservations.index') }}" class="text-slate-500 dark:text-slate-400 hover:text-pink-600 dark:hover:text-pink-400 transition-colors">Reservasi Saya</a>
        <svg class="w-4 h-4 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('customer.reservations.show', $reservation) }}" class="text-slate-500 dark:text-slate-400 hover:text-pink-600 dark:hover:text-pink-400 transition-colors">Detail #{{ $reservation->id }}</a>
        <svg class="w-4 h-4 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-800 dark:text-white font-medium">Ubah Jadwal</span>
    </nav>

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Ubah Jadwal Reservasi (Reschedule) 🏸</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Anda dapat memindahkan jadwal bermain Anda ke slot lain yang tersedia pada lapangan yang sama.</p>
    </div>

    <div x-data="rescheduleApp()" class="flex flex-col lg:flex-row gap-6">

        {{-- Left Pane: Date and Slots Picker --}}
        <div class="flex-1 space-y-6">
            
            {{-- Date & Slot Panel --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center gap-3">
                    <span class="w-7 h-7 rounded-lg bg-gradient-to-br from-blue-500 to-pink-600 flex items-center justify-center text-white text-xs font-bold shadow-md shadow-pink-500/20">1</span>
                    <h2 class="font-bold text-slate-800 dark:text-white">Pilih Tanggal & Jam Baru</h2>
                </div>
                <div class="p-6 space-y-6">
                    
                    {{-- Date Input --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Tanggal Baru</label>
                        <div class="relative max-w-md">
                            <input type="date" 
                                   x-model="selectedDate" 
                                   :min="minDate"
                                   @change="fetchSlots()"
                                   class="w-full px-4 py-3 text-sm bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-pink-500 transition font-medium">
                        </div>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Sesuai ketentuan, reschedule minimal dilakukan H-1 (besok).
                        </p>
                    </div>

                    {{-- Slots Picker --}}
                    <div class="border-t border-slate-100 dark:border-slate-800/80 pt-6">
                        <div class="flex items-center justify-between mb-4">
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pilih Waktu Mulai</label>
                            <span class="text-xs text-pink-600 dark:text-pink-400 font-semibold" x-text="'Durasi: ' + durationHours + ' Jam'"></span>
                        </div>

                        {{-- Loading --}}
                        <div x-show="loading" class="flex items-center justify-center py-12" x-cloak>
                            <div class="flex flex-col items-center gap-3">
                                <svg class="animate-spin h-8 w-8 text-pink-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                <span class="text-xs text-slate-400">Memuat jadwal lapangan...</span>
                            </div>
                        </div>

                        {{-- Slots Grid --}}
                        <div x-show="!loading && slots.length > 0" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3" x-cloak>
                            <template x-for="slot in slots" :key="slot.id">
                                <button type="button"
                                        @click="slot.available && selectSlot(slot.id)"
                                        :disabled="!slot.available"
                                        :class="{
                                            'border-pink-500 ring-2 ring-pink-500/30 bg-pink-50/50 dark:bg-pink-500/10 shadow-sm': isSlotSelected(slot.id),
                                            'border-slate-200 dark:border-slate-800 hover:border-pink-300 dark:hover:border-pink-800 hover:bg-slate-50 dark:hover:bg-slate-800/30': slot.available && !isSlotSelected(slot.id),
                                            'border-slate-100 dark:border-slate-900 bg-slate-50 dark:bg-slate-950/50 opacity-40 cursor-not-allowed': !slot.available
                                        }"
                                        class="relative flex flex-col items-center p-4 rounded-xl border transition-all text-center group">
                                    
                                    {{-- Selected indicator badge --}}
                                    <div x-show="isSlotSelected(slot.id)" class="absolute top-1.5 right-1.5" x-cloak>
                                        <span class="flex h-4.5 w-4.5 items-center justify-center rounded-full bg-pink-500 text-[10px] font-bold text-white shadow-sm shadow-pink-500/30">✓</span>
                                    </div>
                                    
                                    {{-- Occupied indicator --}}
                                    <div x-show="!slot.available" class="absolute top-1.5 right-1.5" x-cloak>
                                        <span class="text-[9px] font-extrabold text-red-500 uppercase bg-red-100 dark:bg-red-500/20 px-1.5 py-0.5 rounded">Terisi</span>
                                    </div>
                                    
                                    <span class="text-sm font-bold text-slate-800 dark:text-white" x-text="formatTime(slot.start_time) + ' - ' + formatTime(slot.end_time)"></span>
                                    <span class="text-xs font-semibold text-pink-600 dark:text-pink-400 mt-1" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(slot.price)"></span>
                                    
                                    {{-- Subtext on hover --}}
                                    <div x-show="slot.available && !isSlotSelected(slot.id)" class="text-[9px] text-slate-400 mt-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                        Pilih Mulai Jam Ini
                                    </div>
                                </button>
                            </template>
                        </div>

                        {{-- Empty Slots state --}}
                        <div x-show="!loading && slots.length === 0" class="text-center py-12 bg-slate-50 dark:bg-slate-950 rounded-xl border border-dashed border-slate-300 dark:border-slate-800" x-cloak>
                            <svg class="w-12 h-12 text-slate-300 dark:text-slate-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Tidak ada jadwal tersedia untuk tanggal yang dipilih.</p>
                            <p class="text-slate-400 dark:text-slate-500 text-xs mt-1">Silakan coba pilih tanggal lain.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Pane: Reschedule Review & Action --}}
        <div class="w-full lg:w-96 flex-shrink-0">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm sticky top-6">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-gradient-to-r from-blue-900 via-[#152647] to-pink-700 flex items-center justify-between">
                    <h3 class="text-white font-bold">Review Reschedule</h3>
                    <span class="text-[10px] text-white/80 font-bold uppercase tracking-wider bg-white/10 px-2 py-0.5 rounded border border-white/10">Lapangan {{ $court->name }}</span>
                </div>
                <div class="p-6 space-y-6">
                    
                    {{-- Visual Comparison --}}
                    <div class="space-y-4">
                        {{-- Old Booking Detail --}}
                        <div class="relative pl-4 border-l-4 border-amber-500/80">
                            <div class="absolute -left-2.5 top-0.5 w-4 h-4 bg-amber-500 rounded-full border-4 border-white dark:border-slate-900 flex items-center justify-center"></div>
                            <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-widest block">Jadwal Asli</span>
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-300 mt-1">
                                {{ $reservation->date->translatedFormat('d M Y') }}
                            </p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Jam {{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($reservation->end_time)->format('H:i') }} ({{ $reservation->duration_hours }} Jam)
                            </p>
                            <p class="text-xs font-semibold text-slate-600 dark:text-slate-400 mt-0.5">
                                Total: {{ $reservation->formatted_total_price }}
                            </p>
                        </div>

                        {{-- Arrow Separator --}}
                        <div class="flex justify-center my-1 text-slate-300 dark:text-slate-700">
                            <svg class="w-6 h-6 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 13l-7 7-7-7m14-6l-7 7-7-7"/></svg>
                        </div>

                        {{-- New Booking Detail --}}
                        <div class="relative pl-4 border-l-4 border-emerald-500">
                            <div class="absolute -left-2.5 top-0.5 w-4 h-4 bg-emerald-500 rounded-full border-4 border-white dark:border-slate-900 flex items-center justify-center"></div>
                            <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest block">Jadwal Baru</span>
                            
                            {{-- Not selected placeholder --}}
                            <div x-show="selectedSlots.length === 0" class="text-xs text-slate-400 italic mt-2">
                                Silakan pilih slot di panel kiri...
                            </div>
                            
                            {{-- Selected details --}}
                            <div x-show="selectedSlots.length > 0" x-cloak>
                                <p class="text-sm font-bold text-slate-800 dark:text-white mt-1" x-text="formatSelectedDate()"></p>
                                <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5">
                                    Jam <span x-text="selectedTimeRange()"></span> (<span x-text="durationHours"></span> Jam)
                                </p>
                                <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400 mt-1 flex items-center gap-1.5">
                                    Total: <span x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(newTotalPrice)"></span>
                                    <template x-if="newTotalPrice !== originalPrice">
                                        <span :class="newTotalPrice > originalPrice ? 'text-amber-500' : 'text-emerald-500'" class="text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded">
                                            <span x-text="newTotalPrice > originalPrice ? 'Selisih +' : 'Selisih -'"></span>
                                            <span x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(Math.abs(newTotalPrice - originalPrice))"></span>
                                        </span>
                                    </template>
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Info / Policy Alert --}}
                    <div class="p-4 bg-blue-50 dark:bg-blue-500/5 border border-blue-200 dark:border-blue-500/20 rounded-xl text-[11px] text-blue-800 dark:text-blue-400 space-y-1.5">
                        <p class="font-bold flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                            Kebijakan Perubahan Jadwal:
                        </p>
                        <ul class="list-disc pl-4 space-y-0.5">
                            <li>Reschedule hanya diperbolehkan **maksimal 1 kali**.</li>
                            <li>Durasi slot baru harus **sama persis** dengan durasi slot lama ({{ $reservation->duration_hours }} Jam).</li>
                            <li>Jika tarif slot baru berbeda dengan tarif awal, tagihan/pembayaran akan disesuaikan secara otomatis.</li>
                        </ul>
                    </div>

                    {{-- Form Submit --}}
                    <div>
                        <form method="POST" action="{{ route('customer.reservations.reschedule.process', $reservation) }}" id="reschedule-form">
                            @csrf
                            <input type="hidden" name="date" :value="selectedDate">
                            <template x-for="slot in selectedSlots" :key="slot.id">
                                <input type="hidden" name="schedule_ids[]" :value="slot.id">
                            </template>

                            <button type="button"
                                    :disabled="selectedSlots.length === 0"
                                    @click="$dispatch('open-global-confirm', { 
                                        formId: 'reschedule-form', 
                                        message: 'Konfirmasi pemindahan jadwal bermain Anda ke tanggal ' + formatSelectedDate() + ' jam ' + selectedTimeRange() + '? Tindakan ini hanya bisa dilakukan 1 kali.' 
                                    })"
                                    :class="selectedSlots.length > 0 
                                        ? 'bg-gradient-to-r from-blue-600 to-pink-600 hover:from-blue-700 hover:to-pink-700 text-white shadow-lg shadow-pink-500/20 cursor-pointer' 
                                        : 'bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-600 cursor-not-allowed'"
                                    class="w-full px-6 py-3.5 text-center font-bold rounded-xl transition-all duration-200 hover:-translate-y-0.5 active:scale-[0.98]">
                                Simpan Jadwal Baru
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        function rescheduleApp() {
            // Calculate minDate (tomorrow)
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            const minDateStr = tomorrow.toISOString().split('T')[0];

            return {
                selectedDate: minDateStr,
                minDate: minDateStr,
                durationHours: parseInt('{{ $reservation->duration_hours }}'),
                originalPrice: parseInt('{{ $reservation->total_price }}'),
                slots: [],
                selectedSlots: [],
                loading: false,

                get newTotalPrice() {
                    return this.selectedSlots.reduce((sum, s) => sum + s.price, 0);
                },

                init() {
                    this.fetchSlots();
                },

                formatTime(timeStr) {
                    if (!timeStr) return '';
                    return timeStr.substring(0, 5); // '08:00:00' -> '08:00'
                },

                formatSelectedDate() {
                    if (!this.selectedDate) return '';
                    const d = new Date(this.selectedDate + 'T00:00:00');
                    return d.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
                },

                selectedTimeRange() {
                    if (this.selectedSlots.length === 0) return '';
                    // Assuming sorted slots
                    const start = this.formatTime(this.selectedSlots[0].start_time);
                    const end = this.formatTime(this.selectedSlots[this.selectedSlots.length - 1].end_time);
                    return `${start} - ${end}`;
                },

                async fetchSlots() {
                    if (!this.selectedDate) return;
                    this.loading = true;
                    this.slots = [];
                    this.selectedSlots = [];

                    try {
                        const response = await fetch(`{{ route('customer.booking.slots') }}?court_id={{ $court->id }}&date=${this.selectedDate}`, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        if (!response.ok) { this.slots = []; return; }
                        const data = await response.json();
                        this.slots = data.slots || [];
                        
                        // Sort slots by start_time just to be safe
                        this.slots.sort((a, b) => a.start_time.localeCompare(b.start_time));
                    } catch (e) {
                        this.slots = [];
                    } finally {
                        this.loading = false;
                    }
                },

                selectSlot(slotId) {
                    // Check if already selected, clear if so
                    if (this.isSlotSelected(slotId)) {
                        this.selectedSlots = [];
                        return;
                    }

                    // Find index of clicked slot
                    const index = this.slots.findIndex(s => s.id === slotId);
                    if (index === -1) return;

                    // We need a contiguous block of 'durationHours' slots starting from index
                    if (index + this.durationHours > this.slots.length) {
                        alert(`Slot tidak cukup untuk memenuhi durasi permainan Anda (${this.durationHours} Jam).`);
                        return;
                    }

                    const candidates = [];
                    for (let i = 0; i < this.durationHours; i++) {
                        const slot = this.slots[index + i];
                        
                        // Must be available
                        if (!slot.available) {
                            alert(`Beberapa slot berikutnya dalam rentang waktu ini sudah terisi.`);
                            return;
                        }

                        // Must be contiguous
                        if (i > 0) {
                            const prevSlot = candidates[i - 1];
                            if (slot.start_time !== prevSlot.end_time) {
                                alert(`Rentang waktu tidak berurutan.`);
                                return;
                            }
                        }

                        candidates.push(slot);
                    }

                    // Success! Auto-select the bundle
                    this.selectedSlots = candidates;
                },

                isSlotSelected(slotId) {
                    return this.selectedSlots.some(s => s.id === slotId);
                }
            };
        }
    </script>
    @endpush

</x-layouts.app>
