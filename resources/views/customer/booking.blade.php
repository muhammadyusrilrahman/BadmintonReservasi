<x-layouts.app :title="$title">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Booking Lapangan 🏸</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Pilih lapangan, tambahkan tanggal, dan pilih jadwal yang tersedia.</p>
    </div>

    <div x-data="bookingApp()" class="flex flex-col lg:flex-row gap-6">

        {{-- Left: Steps --}}
        <div class="flex-1 space-y-6">

            {{-- Step 1: Pilih Lapangan --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center gap-3">
                    <span class="w-7 h-7 rounded-lg bg-gradient-to-br from-[#1e3a5f] to-[#e91e8c] flex items-center justify-center text-white text-xs font-bold">1</span>
                    <h2 class="font-bold text-slate-800 dark:text-white">Pilih Lapangan</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($courts as $court)
                            <button type="button"
                                    @click="selectCourt({{ $court->id }}, '{{ $court->name }}')"
                                    :class="selectedCourtId === {{ $court->id }}
                                        ? 'border-pink-500 ring-2 ring-pink-500/30 bg-pink-50/50 dark:bg-pink-500/10'
                                        : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600'"
                                    class="flex items-center gap-4 p-4 rounded-xl border transition-all text-left">
                                @if($court->photo_url)
                                    <img src="{{ $court->photo_url }}" alt="{{ $court->name }}" class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
                                @else
                                    <div class="w-16 h-16 rounded-lg bg-gradient-to-br from-[#1e3a5f] to-[#2a4a73] flex items-center justify-center flex-shrink-0">
                                        <svg class="w-8 h-8 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-800 dark:text-white text-sm">{{ $court->name }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $court->type_label }}</p>
                                    @if($court->price_per_hour)
                                        <p class="text-xs text-pink-600 dark:text-pink-400 font-medium mt-1">Mulai {{ $court->formatted_price }}/jam</p>
                                    @endif
                                </div>
                                <div class="ml-auto flex-shrink-0" x-show="selectedCourtId === {{ $court->id }}">
                                    <svg class="w-5 h-5 text-pink-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Step 2: Tanggal & Jadwal (Multi-Day) --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden" x-show="selectedCourtId" x-transition>
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center gap-3">
                    <span class="w-7 h-7 rounded-lg bg-gradient-to-br from-[#1e3a5f] to-[#e91e8c] flex items-center justify-center text-white text-xs font-bold">2</span>
                    <h2 class="font-bold text-slate-800 dark:text-white">Pilih Tanggal & Jadwal</h2>
                    <span class="ml-auto text-xs text-slate-400" x-show="totalSlots > 0" x-text="totalSlots + ' slot di ' + dateEntries.length + ' hari'"></span>
                </div>
                <div class="p-6 space-y-4">

                    {{-- Add Date Input --}}
                    <div class="flex items-end gap-3">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tambah Tanggal</label>
                            <input type="date" x-model="newDate" :min="today"
                                   class="w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-pink-500 transition">
                        </div>
                        <button type="button" @click="addDate()" :disabled="!newDate"
                                class="px-4 py-2.5 bg-gradient-to-r from-[#1e3a5f] to-[#e91e8c] text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:shadow-pink-500/25 transition-all disabled:opacity-40 disabled:cursor-not-allowed">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>

                    {{-- Date entries --}}
                    <template x-for="(entry, idx) in dateEntries" :key="entry.date">
                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                            {{-- Date Header --}}
                            <div class="px-4 py-3 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span class="text-sm font-semibold text-slate-800 dark:text-white" x-text="formatDate(entry.date)"></span>
                                    <span class="text-xs text-slate-400" x-show="entry.selectedSlots.length > 0" x-text="'(' + entry.selectedSlots.length + ' slot)'"></span>
                                </div>
                                <button type="button" @click="removeDate(idx)" class="text-red-400 hover:text-red-600 transition-colors p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            {{-- Slots --}}
                            <div class="p-4">
                                {{-- Loading --}}
                                <div x-show="entry.loading" class="flex items-center justify-center py-6">
                                    <svg class="animate-spin h-6 w-6 text-pink-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                </div>
                                {{-- No slots --}}
                                <div x-show="!entry.loading && entry.slots.length === 0" class="text-center py-6">
                                    <p class="text-slate-400 text-sm">Tidak ada jadwal tersedia untuk hari ini.</p>
                                </div>
                                {{-- Slots Grid --}}
                                <div x-show="!entry.loading && entry.slots.length > 0" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
                                    <template x-for="slot in entry.slots" :key="slot.id + '-' + entry.date">
                                        <button type="button"
                                                @click="slot.available && toggleSlot(entry, slot)"
                                                :disabled="!slot.available"
                                                :class="{
                                                    'border-pink-500 ring-2 ring-pink-500/30 bg-pink-50 dark:bg-pink-500/10': isSlotSelected(entry, slot.id),
                                                    'border-slate-200 dark:border-slate-700 hover:border-pink-300 dark:hover:border-pink-700': slot.available && !isSlotSelected(entry, slot.id),
                                                    'border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/30 opacity-50 cursor-not-allowed': !slot.available
                                                }"
                                                class="relative flex flex-col items-center p-3 rounded-lg border transition-all text-center">
                                            <div x-show="isSlotSelected(entry, slot.id)" class="absolute top-1 right-1">
                                                <svg class="w-3.5 h-3.5 text-pink-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                            </div>
                                            <div x-show="!slot.available" class="absolute top-1 right-1">
                                                <span class="text-[9px] font-bold text-red-500 uppercase">Terisi</span>
                                            </div>
                                            <span class="text-xs font-bold text-slate-800 dark:text-white" x-text="slot.start_time + ' - ' + slot.end_time"></span>
                                            <span class="text-[10px] font-medium text-pink-600 dark:text-pink-400 mt-0.5" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(slot.price)"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Empty state --}}
                    <div x-show="dateEntries.length === 0" class="text-center py-8 bg-slate-50 dark:bg-slate-800/30 rounded-xl border border-dashed border-slate-300 dark:border-slate-700">
                        <svg class="w-10 h-10 text-slate-300 dark:text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <p class="text-slate-500 dark:text-slate-400 text-sm">Pilih tanggal di atas untuk melihat jadwal tersedia</p>
                    </div>
                </div>
            </div>

            {{-- Step 3: Metode Pembayaran & Catatan --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden" x-show="totalSlots > 0" x-transition>
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center gap-3">
                    <span class="w-7 h-7 rounded-lg bg-gradient-to-br from-[#1e3a5f] to-[#e91e8c] flex items-center justify-center text-white text-xs font-bold">3</span>
                    <h2 class="font-bold text-slate-800 dark:text-white">Pembayaran & Catatan</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Metode Pembayaran</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label :class="paymentMethod === 'transfer' ? 'border-pink-500 ring-2 ring-pink-500/30 bg-pink-50 dark:bg-pink-500/10' : 'border-slate-200 dark:border-slate-700'"
                                   class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all">
                                <input type="radio" x-model="paymentMethod" value="transfer" class="text-pink-600 focus:ring-pink-500">
                                <div>
                                    <p class="text-sm font-medium text-slate-800 dark:text-white">Transfer Bank</p>
                                    <p class="text-xs text-slate-500">BCA, BNI, Mandiri, dll</p>
                                </div>
                            </label>
                            <label :class="paymentMethod === 'ewallet' ? 'border-pink-500 ring-2 ring-pink-500/30 bg-pink-50 dark:bg-pink-500/10' : 'border-slate-200 dark:border-slate-700'"
                                   class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all">
                                <input type="radio" x-model="paymentMethod" value="ewallet" class="text-pink-600 focus:ring-pink-500">
                                <div>
                                    <p class="text-sm font-medium text-slate-800 dark:text-white">E-Wallet</p>
                                    <p class="text-xs text-slate-500">GoPay, OVO, DANA, dll</p>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Catatan <span class="text-slate-400 text-xs font-normal">(opsional)</span></label>
                        <textarea x-model="notes" rows="2" placeholder="Catatan tambahan..."
                                  class="w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-pink-500 transition resize-none"></textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Order Summary --}}
        <div class="w-full lg:w-80 flex-shrink-0">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden sticky top-6">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-gradient-to-r from-[#1e3a5f] to-[#e91e8c]">
                    <h3 class="text-white font-bold">Ringkasan Pesanan</h3>
                </div>
                <div class="p-6">
                    {{-- Empty state --}}
                    <div x-show="totalSlots === 0" class="text-center py-6">
                        <p class="text-slate-400 dark:text-slate-500 text-sm">Belum ada slot dipilih</p>
                    </div>

                    {{-- Summary items --}}
                    <div x-show="totalSlots > 0" class="space-y-4">
                        <div>
                            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Lapangan</p>
                            <p class="text-sm font-medium text-slate-800 dark:text-white" x-text="selectedCourtName"></p>
                        </div>

                        {{-- Per-date breakdown --}}
                        <template x-for="entry in dateEntries.filter(e => e.selectedSlots.length > 0)" :key="'sum-' + entry.date">
                            <div>
                                <p class="text-xs font-semibold text-pink-600 dark:text-pink-400 mb-1.5" x-text="formatDate(entry.date)"></p>
                                <div class="space-y-1.5">
                                    <template x-for="slot in entry.selectedSlots" :key="'sum-slot-' + slot.id + '-' + entry.date">
                                        <div class="flex items-center justify-between p-2 bg-slate-50 dark:bg-slate-800/50 rounded-lg">
                                            <span class="text-xs text-slate-700 dark:text-slate-300" x-text="slot.start_time + ' - ' + slot.end_time"></span>
                                            <span class="text-xs font-medium text-pink-600 dark:text-pink-400" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(slot.price)"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <div class="pt-4 border-t border-slate-200 dark:border-slate-800">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs text-slate-500 dark:text-slate-400">Total slot</span>
                                <span class="text-xs font-medium text-slate-800 dark:text-white" x-text="totalSlots + ' slot'"></span>
                            </div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs text-slate-500 dark:text-slate-400">Subtotal</span>
                                <span class="text-xs font-medium text-slate-800 dark:text-white" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(totalPrice)"></span>
                            </div>

                            {{-- Promo Discount Row --}}
                            <div x-show="promoApplied" x-transition class="flex items-center justify-between mb-1">
                                <span class="text-xs text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    Diskon (<span x-text="appliedPromoCode"></span>)
                                </span>
                                <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400" x-text="'- Rp ' + new Intl.NumberFormat('id-ID').format(promoDiscount)"></span>
                            </div>

                            <div class="flex items-center justify-between pt-2 border-t border-dashed border-slate-200 dark:border-slate-700">
                                <span class="text-sm font-semibold text-slate-800 dark:text-white">Total Bayar</span>
                                <span class="text-lg font-extrabold" :class="promoApplied ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-800 dark:text-white'" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(finalPrice)"></span>
                            </div>
                        </div>

                        {{-- Promo Code Input --}}
                        <div class="pt-4 border-t border-slate-200 dark:border-slate-800">
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Kode Promo</label>
                            <div class="flex gap-2">
                                <input type="text" x-model="promoCodeInput" :disabled="promoApplied"
                                       placeholder="Masukkan kode promo"
                                       @keydown.enter.prevent="applyPromo()"
                                       class="flex-1 px-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-pink-500 transition uppercase"
                                       style="text-transform: uppercase;">
                                <button type="button" x-show="!promoApplied" @click="applyPromo()" :disabled="!promoCodeInput || promoLoading"
                                        class="px-3 py-2 text-xs font-semibold bg-[#1e3a5f] text-white rounded-lg hover:bg-[#162d4a] transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                                    <span x-show="!promoLoading">Terapkan</span>
                                    <svg x-show="promoLoading" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                </button>
                                <button type="button" x-show="promoApplied" @click="removePromo()"
                                        class="px-3 py-2 text-xs font-semibold text-red-600 bg-red-50 dark:bg-red-500/10 rounded-lg hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors">
                                    Hapus
                                </button>
                            </div>
                            <p x-show="promoMessage" x-transition
                               class="text-xs mt-1.5" :class="promoApplied ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400'"
                               x-text="promoMessage"></p>
                        </div>

                        {{-- Submit --}}
                        <form method="POST" action="{{ route('customer.booking.store') }}" id="booking-form">
                            @csrf
                            <input type="hidden" name="court_id" :value="selectedCourtId">
                            <input type="hidden" name="payment_method" :value="paymentMethod">
                            <input type="hidden" name="notes" :value="notes">
                            <input type="hidden" name="promo_code" :value="promoApplied ? appliedPromoCode : ''">

                            <template x-for="(entry, ei) in dateEntries.filter(e => e.selectedSlots.length > 0)" :key="'f-' + entry.date">
                                <div>
                                    <input type="hidden" :name="'bookings[' + ei + '][date]'" :value="entry.date">
                                    <template x-for="slot in entry.selectedSlots" :key="'f-s-' + slot.id + '-' + entry.date">
                                        <input type="hidden" :name="'bookings[' + ei + '][schedule_ids][]'" :value="slot.id">
                                    </template>
                                </div>
                            </template>
                            <button type="button"
                                    @click="$dispatch('open-global-confirm', { formId: 'booking-form', message: 'Konfirmasi pemesanan ' + totalSlots + ' slot di ' + dateEntries.filter(e => e.selectedSlots.length > 0).length + ' hari dengan total Rp ' + new Intl.NumberFormat('id-ID').format(finalPrice) + '?' + (promoApplied ? '\nPromo: ' + appliedPromoCode : '') })"
                                    class="w-full mt-2 px-6 py-3 bg-gradient-to-r from-[#1e3a5f] to-[#e91e8c] text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-pink-500/25 hover:-translate-y-0.5 transition-all duration-200">
                                Pesan Sekarang
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function bookingApp() {
            return {
                selectedCourtId: null,
                selectedCourtName: '',
                newDate: new Date().toISOString().split('T')[0],
                today: new Date().toISOString().split('T')[0],
                dateEntries: [], // [{ date, slots, selectedSlots, loading }]
                paymentMethod: 'transfer',
                notes: '',

                // Promo state
                promoCodeInput: '',
                promoApplied: false,
                appliedPromoCode: '',
                promoDiscount: 0,
                promoMessage: '',
                promoLoading: false,

                get totalSlots() {
                    return this.dateEntries.reduce((sum, e) => sum + e.selectedSlots.length, 0);
                },

                get totalPrice() {
                    return this.dateEntries.reduce((sum, e) =>
                        sum + e.selectedSlots.reduce((s, slot) => s + slot.price, 0)
                    , 0);
                },

                get finalPrice() {
                    return Math.max(0, this.totalPrice - this.promoDiscount);
                },

                formatDate(dateStr) {
                    const d = new Date(dateStr + 'T00:00:00');
                    return d.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
                },

                selectCourt(id, name) {
                    this.selectedCourtId = id;
                    this.selectedCourtName = name;
                    this.removePromo(); // Reset promo when court changes
                    // Re-fetch slots for all existing dates
                    this.dateEntries.forEach(entry => this.fetchSlotsForEntry(entry));
                },

                addDate() {
                    if (!this.newDate || !this.selectedCourtId) return;
                    // Prevent duplicate
                    if (this.dateEntries.some(e => e.date === this.newDate)) {
                        alert('Tanggal ini sudah ditambahkan.');
                        return;
                    }
                    const dateVal = this.newDate;
                    this.dateEntries.push({ date: dateVal, slots: [], selectedSlots: [], loading: false });
                    // Sort by date
                    this.dateEntries.sort((a, b) => a.date.localeCompare(b.date));
                    // Retrieve the reactive proxy from the array (not the raw local object)
                    const reactiveEntry = this.dateEntries.find(e => e.date === dateVal);
                    this.fetchSlotsForEntry(reactiveEntry);
                    // Advance to next day for convenience
                    const next = new Date(dateVal + 'T00:00:00');
                    next.setDate(next.getDate() + 1);
                    this.newDate = next.toISOString().split('T')[0];
                },

                removeDate(idx) {
                    this.dateEntries.splice(idx, 1);
                    // Recalculate promo if applied
                    if (this.promoApplied) {
                        this.revalidatePromo();
                    }
                },

                async fetchSlotsForEntry(entry) {
                    if (!this.selectedCourtId || !entry.date) return;
                    entry.loading = true;
                    entry.slots = [];
                    entry.selectedSlots = [];

                    try {
                        const response = await fetch(`{{ route('customer.booking.slots') }}?court_id=${this.selectedCourtId}&date=${entry.date}`, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        if (!response.ok) { entry.slots = []; return; }
                        const data = await response.json();
                        entry.slots = data.slots || [];
                    } catch (e) {
                        entry.slots = [];
                    } finally {
                        entry.loading = false;
                    }
                },

                toggleSlot(entry, slot) {
                    const idx = entry.selectedSlots.findIndex(s => s.id === slot.id);
                    if (idx > -1) {
                        entry.selectedSlots.splice(idx, 1);
                    } else {
                        entry.selectedSlots.push({ ...slot });
                    }
                    // Recalculate promo discount when slots change
                    if (this.promoApplied) {
                        this.revalidatePromo();
                    }
                },

                isSlotSelected(entry, slotId) {
                    return entry.selectedSlots.some(s => s.id === slotId);
                },

                // Promo methods
                async applyPromo() {
                    if (!this.promoCodeInput || this.promoLoading) return;
                    if (this.totalPrice <= 0) {
                        this.promoMessage = 'Pilih slot terlebih dahulu sebelum menerapkan promo.';
                        return;
                    }

                    this.promoLoading = true;
                    this.promoMessage = '';

                    try {
                        const response = await fetch('{{ route("customer.booking.apply-promo") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                promo_code: this.promoCodeInput.toUpperCase(),
                                total_price: this.totalPrice
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.promoApplied = true;
                            this.appliedPromoCode = data.promo_code;
                            this.promoDiscount = data.discount;
                            this.promoMessage = data.message;
                        } else {
                            this.promoApplied = false;
                            this.promoDiscount = 0;
                            this.promoMessage = data.message || 'Kode promo tidak valid.';
                        }
                    } catch (e) {
                        this.promoMessage = 'Gagal memvalidasi kode promo. Silakan coba lagi.';
                    } finally {
                        this.promoLoading = false;
                    }
                },

                removePromo() {
                    this.promoApplied = false;
                    this.appliedPromoCode = '';
                    this.promoDiscount = 0;
                    this.promoMessage = '';
                    this.promoCodeInput = '';
                },

                async revalidatePromo() {
                    if (!this.promoApplied || this.totalPrice <= 0) {
                        this.removePromo();
                        return;
                    }

                    try {
                        const response = await fetch('{{ route("customer.booking.apply-promo") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                promo_code: this.appliedPromoCode,
                                total_price: this.totalPrice
                            })
                        });

                        const data = await response.json();
                        if (data.success) {
                            this.promoDiscount = data.discount;
                        } else {
                            this.removePromo();
                        }
                    } catch (e) {
                        // Keep current state on network error
                    }
                }
            };
        }
    </script>
    @endpush

</x-layouts.app>
