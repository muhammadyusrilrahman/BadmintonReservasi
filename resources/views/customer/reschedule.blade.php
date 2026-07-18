<x-layouts.app :title="$title">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm mb-6" aria-label="Breadcrumb">
        <a href="{{ route('customer.reservations.index') }}" class="text-slate-500 dark:text-slate-400 hover:text-pink-600 dark:hover:text-pink-400 transition-colors">Reservasi Saya</a>
        <svg class="w-4 h-4 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('customer.reservations.show', $reservation) }}" class="text-slate-500 dark:text-slate-400 hover:text-pink-600 dark:hover:text-pink-400 transition-colors">Detail #{{ $reservation->id }}</a>
        <svg class="w-4 h-4 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-800 dark:text-white font-medium">Ubah Jadwal</span>
    </nav>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Ubah Jadwal Reservasi (Reschedule) 🏸</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Anda dapat memindahkan jadwal bermain Anda ke slot lain yang tersedia secara independen.</p>
    </div>

    <div x-data="rescheduleApp()" class="flex flex-col lg:flex-row gap-6">

        {{-- Left Pane: Date and Slots Picker --}}
        <div class="flex-1 space-y-6">
            
            {{-- Slot Selection Panel --}}
            @if($sessionReservations && $sessionReservations->count() > 1)
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
                <h2 class="font-bold text-slate-800 dark:text-white flex items-center gap-2 mb-4">
                    <span class="w-6 h-6 rounded bg-pink-100 dark:bg-pink-500/20 text-pink-600 flex items-center justify-center text-xs">1</span>
                    Pilih Slot yang Ingin Di-reschedule
                </h2>
                <div class="space-y-3">
                    @foreach($sessionReservations as $slot)
                        <label class="flex items-center gap-3 p-3 border {{ $slot->id === $reservation->id ? 'border-pink-300 dark:border-pink-700 bg-pink-50/30 dark:bg-pink-900/10' : 'border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50' }} rounded-xl cursor-pointer transition">
                            <input type="checkbox" 
                                   :checked="selectedOriginalIds.includes({{ $slot->id }})"
                                   @change="toggleSelection({{ $slot->id }})"
                                   {{ !$slot->canReschedule() && $slot->id !== $reservation->id ? 'disabled' : '' }}
                                   class="w-5 h-5 text-pink-600 rounded border-slate-300 focus:ring-pink-500">
                            <div>
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-200">
                                    {{ $slot->date->translatedFormat('d M Y') }} ({{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }})
                                </p>
                                @if(!$slot->canReschedule() && $slot->id !== $reservation->id)
                                    <p class="text-xs text-red-500 mt-0.5">Tidak memenuhi syarat reschedule</p>
                                @else
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $slot->formatted_total_price }}</p>
                                @endif
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Reschedule Forms Loop --}}
            <template x-for="(id, index) in selectedOriginalIds" :key="id">
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-gradient-to-br from-blue-500 to-pink-600 flex items-center justify-center text-white text-xs font-bold shadow-md shadow-pink-500/20" x-text="index + ({{ $sessionReservations->count() > 1 ? 2 : 1 }})"></span>
                            <div>
                                <h2 class="font-bold text-slate-800 dark:text-white text-sm">Pilih Jadwal Pengganti</h2>
                                <p class="text-xs text-slate-500" x-text="'Untuk slot asli: ' + originalReservations[id].formatted_original_date + ' ' + originalReservations[id].original_time"></p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-6">
                        
                        {{-- Date Input --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Tanggal Baru</label>
                            <div class="relative max-w-md">
                                <input type="date" 
                                       x-model="originalReservations[id].date" 
                                       :min="minDate"
                                       @change="fetchSlots(id)"
                                       class="w-full px-4 py-3 text-sm bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-pink-500 transition font-medium">
                            </div>
                        </div>

                        {{-- Slots Picker --}}
                        <div class="border-t border-slate-100 dark:border-slate-800/80 pt-6">
                            <div class="flex items-center justify-between mb-4">
                                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pilih Waktu Mulai</label>
                                <span class="text-xs text-pink-600 dark:text-pink-400 font-semibold" x-text="'Durasi: ' + originalReservations[id].duration + ' Jam'"></span>
                            </div>

                            {{-- Loading --}}
                            <div x-show="originalReservations[id].loading" class="flex items-center justify-center py-8" x-cloak>
                                <svg class="animate-spin h-6 w-6 text-pink-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            </div>

                            {{-- Slots Grid --}}
                            <div x-show="!originalReservations[id].loading && originalReservations[id].slots.length > 0" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3" x-cloak>
                                <template x-for="slot in originalReservations[id].slots" :key="slot.id">
                                    <button type="button"
                                            @click="slot.available && selectNewSlot(id, slot.id)"
                                            :disabled="!slot.available"
                                            :class="{
                                                'border-pink-500 ring-2 ring-pink-500/30 bg-pink-50/50 dark:bg-pink-500/10 shadow-sm': isSlotSelected(id, slot.id),
                                                'border-slate-200 dark:border-slate-800 hover:border-pink-300 dark:hover:border-pink-800 hover:bg-slate-50 dark:hover:bg-slate-800/30': slot.available && !isSlotSelected(id, slot.id),
                                                'border-slate-100 dark:border-slate-900 bg-slate-50 dark:bg-slate-950/50 opacity-40 cursor-not-allowed': !slot.available
                                            }"
                                            class="relative flex flex-col items-center p-3 rounded-xl border transition-all text-center group">
                                        
                                        <div x-show="isSlotSelected(id, slot.id)" class="absolute top-1 right-1" x-cloak>
                                            <span class="flex h-4 w-4 items-center justify-center rounded-full bg-pink-500 text-[9px] font-bold text-white shadow-sm shadow-pink-500/30">✓</span>
                                        </div>
                                        
                                        <div x-show="!slot.available" class="absolute top-1 right-1" x-cloak>
                                            <span class="text-[8px] font-extrabold text-red-500 uppercase bg-red-100 dark:bg-red-500/20 px-1 rounded">Terisi</span>
                                        </div>
                                        
                                        <span class="text-sm font-bold text-slate-800 dark:text-white" x-text="formatTime(slot.start_time) + ' - ' + formatTime(slot.end_time)"></span>
                                        <span class="text-xs font-semibold text-pink-600 dark:text-pink-400 mt-1" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(slot.price)"></span>
                                    </button>
                                </template>
                            </div>

                            {{-- Empty Slots --}}
                            <div x-show="!originalReservations[id].loading && originalReservations[id].slots.length === 0" class="text-center py-8 bg-slate-50 dark:bg-slate-950 rounded-xl border border-dashed border-slate-300 dark:border-slate-800" x-cloak>
                                <p class="text-slate-500 dark:text-slate-400 text-sm">Tidak ada jadwal tersedia.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Right Pane: Reschedule Review & Action --}}
        <div class="w-full lg:w-96 flex-shrink-0">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm sticky top-6">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-gradient-to-r from-blue-900 via-[#152647] to-pink-700 flex items-center justify-between">
                    <h3 class="text-white font-bold">Review Reschedule</h3>
                    <span class="text-[10px] text-white/80 font-bold uppercase tracking-wider bg-white/10 px-2 py-0.5 rounded border border-white/10">Lapangan {{ $court->name }}</span>
                </div>
                <div class="p-6 space-y-6">
                    
                    {{-- Visual Comparison List --}}
                    <div class="space-y-4 max-h-[40vh] overflow-y-auto pr-2 custom-scrollbar">
                        <template x-for="id in selectedOriginalIds" :key="id">
                            <div class="relative pl-4 border-l-2 border-slate-200 dark:border-slate-700 py-1">
                                <div class="mb-3">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-[9px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-widest bg-amber-100 dark:bg-amber-500/20 px-1.5 py-0.5 rounded">Asli</span>
                                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300" x-text="originalReservations[id].formatted_original_date + ' ' + originalReservations[id].original_time"></span>
                                    </div>
                                </div>
                                
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-[9px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest bg-emerald-100 dark:bg-emerald-500/20 px-1.5 py-0.5 rounded">Baru</span>
                                        <span x-show="originalReservations[id].selectedNewSlots.length > 0" class="text-xs font-bold text-slate-800 dark:text-white" x-text="formatDate(originalReservations[id].date) + ' ' + (originalReservations[id].selectedNewSlots[0]?.start_time.substring(0,5)) + '-' + (originalReservations[id].selectedNewSlots[originalReservations[id].selectedNewSlots.length-1]?.end_time.substring(0,5))"></span>
                                        <span x-show="originalReservations[id].selectedNewSlots.length === 0" class="text-[10px] text-slate-400 italic">Belum dipilih...</span>
                                    </div>
                                </div>
                            </div>
                        </template>
                        
                        <div x-show="selectedOriginalIds.length === 0" class="text-sm text-slate-500 text-center py-4">
                            Belum ada slot yang dipilih.
                        </div>
                    </div>

                    {{-- Total Price Review --}}
                    <div class="pt-4 border-t border-slate-200 dark:border-slate-800" x-show="selectedOriginalIds.length > 0">
                        <div class="flex justify-between items-center text-sm mb-1">
                            <span class="text-slate-500">Tarif Awal:</span>
                            <span class="font-semibold text-slate-700 dark:text-slate-300" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(selectedOldPrice)"></span>
                        </div>
                        <div class="flex justify-between items-center text-sm font-bold mb-2">
                            <span class="text-slate-800 dark:text-white">Tarif Baru:</span>
                            <span class="text-emerald-600 dark:text-emerald-400" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(newTotalPrice)"></span>
                        </div>
                        
                        <div x-show="newTotalPrice !== selectedOldPrice" class="p-2 bg-slate-50 dark:bg-slate-800/50 rounded-lg text-xs font-semibold text-center mt-2 border border-slate-200 dark:border-slate-700">
                            <span :class="newTotalPrice > selectedOldPrice ? 'text-amber-500' : 'text-emerald-500'" x-text="(newTotalPrice > selectedOldPrice ? 'Kekurangan Tagihan: ' : 'Kelebihan Pembayaran: ') + 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.abs(newTotalPrice - selectedOldPrice))"></span>
                        </div>
                    </div>

                    {{-- Form Submit --}}
                    <div>
                        <form method="POST" action="{{ route('customer.reservations.reschedule.process', $reservation) }}" id="reschedule-form">
                            @csrf
                            <template x-for="(id, index) in selectedOriginalIds" :key="id">
                                <div>
                                    <input type="hidden" :name="`reschedules[${index}][reservation_id]`" :value="id">
                                    <input type="hidden" :name="`reschedules[${index}][date]`" :value="originalReservations[id].date">
                                    <template x-for="slot in originalReservations[id].selectedNewSlots" :key="slot.id">
                                        <input type="hidden" :name="`reschedules[${index}][schedule_ids][]`" :value="slot.id">
                                    </template>
                                </div>
                            </template>

                            <button type="button"
                                    :disabled="!isReadyToSubmit()"
                                    @click="$dispatch('open-global-confirm', { 
                                        formId: 'reschedule-form', 
                                        message: 'Konfirmasi pemindahan jadwal bermain Anda? Tindakan ini hanya bisa dilakukan 1 kali dan perubahan tarif (jika ada) akan disesuaikan otomatis.' 
                                    })"
                                    :class="isReadyToSubmit() 
                                        ? 'bg-gradient-to-r from-blue-600 to-pink-600 hover:from-blue-700 hover:to-pink-700 text-white shadow-lg shadow-pink-500/20 cursor-pointer' 
                                        : 'bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-600 cursor-not-allowed'"
                                    class="w-full px-6 py-3 text-center text-sm font-bold rounded-xl transition-all duration-200 hover:-translate-y-0.5 active:scale-[0.98]">
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
            const today = new Date();
            // Sesuaikan zona waktu lokal (offset)
            today.setMinutes(today.getMinutes() - today.getTimezoneOffset());
            const minDateStr = today.toISOString().split('T')[0];

            // Parse session reservations from backend
            const sessionData = @json($sessionData);
            
            const forms = {};
            sessionData.forEach(r => {
                forms[r.id] = {
                    id: r.id,
                    original_date: r.original_date_ymd,
                    formatted_original_date: r.formatted_original_date,
                    original_time: r.start_time.substring(0,5) + '-' + r.end_time.substring(0,5),
                    original_price: parseInt(r.total_price),
                    duration: parseInt(r.duration_hours),
                    date: minDateStr,
                    slots: [], 
                    selectedNewSlots: [], 
                    loading: false
                };
            });

            return {
                minDate: minDateStr,
                originalReservations: forms,
                selectedOriginalIds: [{{ $reservation->id }}], 

                init() {
                    this.selectedOriginalIds.forEach(id => this.fetchSlots(id));
                },

                toggleSelection(id) {
                    const idx = this.selectedOriginalIds.indexOf(id);
                    if (idx > -1) {
                        this.selectedOriginalIds.splice(idx, 1);
                    } else {
                        this.selectedOriginalIds.push(id);
                        if (this.originalReservations[id].slots.length === 0) {
                            this.fetchSlots(id);
                        }
                    }
                },

                async fetchSlots(id) {
                    const form = this.originalReservations[id];
                    if (!form.date) return;
                    form.loading = true;
                    form.slots = [];
                    form.selectedNewSlots = [];

                    try {
                        const response = await fetch(`{{ route('customer.booking.slots') }}?court_id={{ $court->id }}&date=${form.date}`, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        if (response.ok) {
                            const data = await response.json();
                            form.slots = data.slots || [];
                            form.slots.sort((a, b) => a.start_time.localeCompare(b.start_time));
                        }
                    } catch (e) {} finally {
                        form.loading = false;
                    }
                },

                selectNewSlot(id, slotId) {
                    const form = this.originalReservations[id];
                    
                    // Clear if clicked already selected
                    if (form.selectedNewSlots.some(s => s.id === slotId)) {
                        form.selectedNewSlots = [];
                        return;
                    }

                    const index = form.slots.findIndex(s => s.id === slotId);
                    if (index === -1) return;

                    if (index + form.duration > form.slots.length) {
                        alert(`Slot tidak cukup untuk memenuhi durasi permainan Anda (${form.duration} Jam).`);
                        return;
                    }

                    const candidates = [];
                    for (let i = 0; i < form.duration; i++) {
                        const slot = form.slots[index + i];
                        if (!slot.available) {
                            alert(`Beberapa slot berikutnya sudah terisi.`);
                            return;
                        }
                        if (i > 0 && slot.start_time !== candidates[i-1].end_time) {
                            alert(`Rentang waktu tidak berurutan.`);
                            return;
                        }
                        candidates.push(slot);
                    }

                    form.selectedNewSlots = candidates;
                },

                isSlotSelected(id, slotId) {
                    return this.originalReservations[id].selectedNewSlots.some(s => s.id === slotId);
                },

                isReadyToSubmit() {
                    if (this.selectedOriginalIds.length === 0) return false;
                    return this.selectedOriginalIds.every(id => {
                        return this.originalReservations[id].selectedNewSlots.length === this.originalReservations[id].duration;
                    });
                },

                get selectedOldPrice() {
                    return this.selectedOriginalIds.reduce((sum, id) => sum + this.originalReservations[id].original_price, 0);
                },

                get newTotalPrice() {
                    return this.selectedOriginalIds.reduce((sum, id) => {
                        const newSlots = this.originalReservations[id].selectedNewSlots;
                        const newPrice = newSlots.reduce((s, slot) => s + slot.price, 0);
                        return sum + newPrice;
                    }, 0);
                },
                
                formatTime(timeStr) {
                    if (!timeStr) return '';
                    return timeStr.substring(0, 5); 
                },

                formatDate(dateStr) {
                    if (!dateStr) return '';
                    const d = new Date(dateStr + 'T00:00:00');
                    return d.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
                }
            };
        }
    </script>
    @endpush

</x-layouts.app>
