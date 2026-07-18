<x-layouts.app :title="$title">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm mb-6" aria-label="Breadcrumb">
        <a href="{{ route('customer.reservations.index') }}" class="text-slate-500 dark:text-slate-400 hover:text-pink-600 dark:hover:text-pink-400 transition-colors">Reservasi Saya</a>
        <svg class="w-4 h-4 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('customer.reservations.show', $reservation) }}" class="text-slate-500 dark:text-slate-400 hover:text-pink-600 dark:hover:text-pink-400 transition-colors">Detail #{{ $reservation->id }}</a>
        <svg class="w-4 h-4 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-800 dark:text-white font-medium">Ajukan Refund</span>
    </nav>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Pengajuan Refund 💸</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Pilih slot reservasi yang ingin dibatalkan dan ajukan pengembalian dana.</p>
    </div>

    <div x-data="refundApp()" class="flex flex-col lg:flex-row gap-6">

        {{-- Left Pane: Slot Selection --}}
        <div class="flex-1 space-y-6">
            
            {{-- Slot Selection Panel --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
                <h2 class="font-bold text-slate-800 dark:text-white flex items-center gap-2 mb-4">
                    <span class="w-6 h-6 rounded bg-pink-100 dark:bg-pink-500/20 text-pink-600 flex items-center justify-center text-xs">1</span>
                    Pilih Slot yang Ingin Di-refund
                </h2>
                
                <div class="space-y-3">
                    @foreach($sessionReservations as $slot)
                        <label class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer transition"
                               :class="selectedOriginalIds.includes({{ $slot->id }}) ? 'border-pink-300 dark:border-pink-700 bg-pink-50/30 dark:bg-pink-900/10' : 'border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50'">
                            <input type="checkbox" 
                                   :checked="selectedOriginalIds.includes({{ $slot->id }})"
                                   @change="toggleSelection({{ $slot->id }})"
                                   {{ !$slot->canRequestRefund() && $slot->id !== $reservation->id ? 'disabled' : '' }}
                                   class="w-5 h-5 text-pink-600 rounded border-slate-300 focus:ring-pink-500">
                            <div>
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-200">
                                    {{ $slot->date->translatedFormat('d M Y') }} ({{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }})
                                </p>
                                @if(!$slot->canRequestRefund() && $slot->id !== $reservation->id)
                                    <p class="text-xs text-red-500 mt-0.5">Tidak dapat di-refund (waktu habis/sudah proses)</p>
                                @else
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $slot->formatted_total_price }}</p>
                                @endif
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Bank Info Form --}}
            <div x-show="selectedOriginalIds.length > 0" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm" x-cloak x-transition>
                <h2 class="font-bold text-slate-800 dark:text-white flex items-center gap-2 mb-4">
                    <span class="w-6 h-6 rounded bg-blue-100 dark:bg-blue-500/20 text-blue-600 flex items-center justify-center text-xs">2</span>
                    Informasi Rekening & Alasan
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <label for="bank_name" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Nama Bank / E-Wallet</label>
                        <input type="text" id="bank_name" x-model="form.bank_name" placeholder="Contoh: BCA, Mandiri, OVO" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 outline-none transition text-sm">
                    </div>
                    <div>
                        <label for="account_number" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Nomor Rekening</label>
                        <input type="text" id="account_number" x-model="form.account_number" placeholder="Masukkan angka rekening" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 outline-none transition text-sm">
                    </div>
                    <div>
                        <label for="account_name" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Nama Pemilik Rekening</label>
                        <input type="text" id="account_name" x-model="form.account_name" placeholder="Sesuai buku tabungan" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 outline-none transition text-sm">
                    </div>
                    <div>
                        <label for="reason" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Alasan Refund</label>
                        <textarea id="reason" x-model="form.reason" rows="3" placeholder="Sebutkan alasan pembatalan dan pengajuan refund Anda secara detail..." class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 outline-none transition text-sm resize-none"></textarea>
                    </div>
                </div>
            </div>

        </div>

        {{-- Right Pane: Summary --}}
        <div class="lg:w-[350px]">
            <div class="sticky top-6">
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                        <h3 class="font-bold text-slate-800 dark:text-white">Ringkasan Pengajuan</h3>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        <div class="space-y-3" x-show="selectedOriginalIds.length > 0" x-cloak>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-500 dark:text-slate-400">Total Slot Dipilih</span>
                                <span class="font-semibold text-slate-800 dark:text-white" x-text="selectedOriginalIds.length + ' Slot'"></span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-500 dark:text-slate-400">Total Dana Estimasi</span>
                                <span class="font-bold text-pink-600 dark:text-pink-400" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(totalRefundAmount)"></span>
                            </div>
                        </div>

                        <div x-show="selectedOriginalIds.length === 0" class="text-center py-4" x-cloak>
                            <p class="text-sm text-slate-500">Silakan pilih minimal 1 slot untuk melanjutkan pengajuan refund.</p>
                        </div>

                        {{-- Info / Policy Alert --}}
                        <div class="p-4 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 rounded-xl text-xs text-amber-800 dark:text-amber-400 leading-relaxed">
                            <strong>Penting:</strong> Dana refund akan ditransfer kembali ke rekening Anda setelah disetujui oleh admin (Estimasi 1-3 hari kerja).
                        </div>

                        {{-- Hidden Form to Submit --}}
                        <form method="POST" action="{{ route('customer.reservations.refund.request', $reservation) }}" id="refund-form">
                            @csrf
                            <template x-for="id in selectedOriginalIds" :key="id">
                                <input type="hidden" name="reservation_ids[]" :value="id">
                            </template>
                            
                            <input type="hidden" name="bank_name" :value="form.bank_name">
                            <input type="hidden" name="account_number" :value="form.account_number">
                            <input type="hidden" name="account_name" :value="form.account_name">
                            <input type="hidden" name="reason" :value="form.reason">

                            <button type="button"
                                    :disabled="!isReadyToSubmit"
                                    @click="$dispatch('open-global-confirm', { 
                                        formId: 'refund-form', 
                                        message: 'Konfirmasi pengajuan refund untuk ' + selectedOriginalIds.length + ' slot sebesar Rp ' + new Intl.NumberFormat('id-ID').format(totalRefundAmount) + '? Reservasi yang dipilih akan langsung dibatalkan setelah disetujui.' 
                                    })"
                                    :class="isReadyToSubmit 
                                        ? 'bg-gradient-to-r from-blue-600 to-pink-600 hover:from-blue-700 hover:to-pink-700 text-white shadow-lg shadow-pink-500/20 cursor-pointer' 
                                        : 'bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-600 cursor-not-allowed'"
                                    class="w-full px-6 py-3.5 text-center font-bold rounded-xl transition-all duration-200 hover:-translate-y-0.5 active:scale-[0.98]">
                                Ajukan Refund
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('refundApp', () => {
                const sessionData = @json($sessionData);
                
                return {
                    originalReservations: sessionData,
                    selectedOriginalIds: [{{ $reservation->id }}],
                    
                    form: {
                        bank_name: '',
                        account_number: '',
                        account_name: '',
                        reason: ''
                    },

                    toggleSelection(id) {
                        if (this.selectedOriginalIds.includes(id)) {
                            this.selectedOriginalIds = this.selectedOriginalIds.filter(i => i !== id);
                        } else {
                            this.selectedOriginalIds.push(id);
                        }
                    },

                    get totalRefundAmount() {
                        return this.selectedOriginalIds.reduce((sum, id) => {
                            return sum + this.originalReservations[id].total_price;
                        }, 0);
                    },

                    get isReadyToSubmit() {
                        return this.selectedOriginalIds.length > 0 &&
                               this.form.bank_name.trim() !== '' &&
                               this.form.account_number.trim() !== '' &&
                               this.form.account_name.trim() !== '' &&
                               this.form.reason.trim().length >= 10;
                    }
                }
            })
        })
    </script>
    @endpush
</x-layouts.app>
