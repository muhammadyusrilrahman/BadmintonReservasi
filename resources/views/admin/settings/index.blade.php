<x-app-layout>
    <x-slot name="title">Pengaturan Aplikasi</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-800 dark:text-white">Pengaturan Aplikasi</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Kelola konfigurasi umum, pembayaran, dan notifikasi sistem.</p>
            </div>
        </div>

        {{-- Alert sukses --}}
        @if(session('success'))
            <div class="flex items-center gap-3 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded-xl text-emerald-700 dark:text-emerald-400 text-sm">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Tab Navigation --}}
        <div x-data="{ activeTab: 'general' }">

            <div class="flex gap-1 p-1 bg-slate-100 dark:bg-slate-800 rounded-xl mb-6">
                <button @click="activeTab = 'general'"
                    :class="activeTab === 'general' ? 'bg-white dark:bg-slate-700 text-slate-800 dark:text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'"
                    class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Umum
                </button>
                <button @click="activeTab = 'payment'"
                    :class="activeTab === 'payment' ? 'bg-white dark:bg-slate-700 text-slate-800 dark:text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'"
                    class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Pembayaran
                </button>
                {{-- TAB NOTIFIKASI: dinonaktifkan sementara, uncomment jika fitur notifikasi siap
                <button @click="activeTab = 'notification'"
                    :class="activeTab === 'notification' ? 'bg-white dark:bg-slate-700 text-slate-800 dark:text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'"
                    class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    Notifikasi
                </button>
                --}}
            </div>

            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                @method('PUT')

                {{-- ==================== TAB: UMUM ==================== --}}
                <div x-show="activeTab === 'general'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                            <h3 class="font-semibold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                                <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Informasi Umum Aplikasi
                            </h3>
                        </div>
                        <div class="p-6 space-y-5">
                            {{-- Nama Aplikasi --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Nama Aplikasi</label>
                                <input type="text" name="app_name" id="app_name"
                                    value="{{ old('app_name', $settings['app_name'] ?? '') }}"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all"
                                    placeholder="Nama aplikasi Anda">
                                @error('app_name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>

                            {{-- Telepon & Email --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Nomor Telepon / WhatsApp</label>
                                    <input type="text" name="app_phone" id="app_phone"
                                        value="{{ old('app_phone', $settings['app_phone'] ?? '') }}"
                                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all"
                                        placeholder="08xxxxxxxxxx">
                                    @error('app_phone')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Alamat Email</label>
                                    <input type="email" name="app_email" id="app_email"
                                        value="{{ old('app_email', $settings['app_email'] ?? '') }}"
                                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all"
                                        placeholder="email@contoh.com">
                                    @error('app_email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            {{-- Alamat --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Alamat Lengkap</label>
                                <textarea name="app_address" id="app_address" rows="3"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all resize-none"
                                    placeholder="Jl. Nama Jalan No. XX, Kota...">{{ old('app_address', $settings['app_address'] ?? '') }}</textarea>
                                @error('app_address')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>

                            {{-- Jam Operasional --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Jam Operasional</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Jam Buka</label>
                                        <input type="time" name="operating_hours_open" id="operating_hours_open"
                                            value="{{ old('operating_hours_open', $settings['operating_hours_open'] ?? '06:00') }}"
                                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all">
                                        @error('operating_hours_open')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Jam Tutup</label>
                                        <input type="time" name="operating_hours_close" id="operating_hours_close"
                                            value="{{ old('operating_hours_close', $settings['operating_hours_close'] ?? '22:00') }}"
                                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all">
                                        @error('operating_hours_close')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ==================== TAB: PEMBAYARAN ==================== --}}
                <div x-show="activeTab === 'payment'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                            <h3 class="font-semibold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                                <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                Pengaturan Pembayaran
                            </h3>
                        </div>
                        <div class="p-6 space-y-5">

                            {{-- Info rekening --}}
                            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl text-sm text-blue-700 dark:text-blue-300 flex items-start gap-3">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Informasi rekening ini akan ditampilkan kepada pelanggan saat melakukan pembayaran manual.</span>
                            </div>

                            {{-- Bank --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Nama Bank</label>
                                    <input type="text" name="bank_name" id="bank_name"
                                        value="{{ old('bank_name', $settings['bank_name'] ?? '') }}"
                                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all"
                                        placeholder="Contoh: BCA, Mandiri, BRI">
                                    @error('bank_name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Nama Pemilik Rekening</label>
                                    <input type="text" name="bank_account_name" id="bank_account_name"
                                        value="{{ old('bank_account_name', $settings['bank_account_name'] ?? '') }}"
                                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all"
                                        placeholder="Nama pemilik rekening">
                                    @error('bank_account_name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            {{-- Nomor Rekening --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Nomor Rekening</label>
                                <input type="text" name="bank_account_number" id="bank_account_number"
                                    value="{{ old('bank_account_number', $settings['bank_account_number'] ?? '') }}"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all font-mono tracking-wider"
                                    placeholder="0000-0000-0000-0000">
                                @error('bank_account_number')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>

                            {{-- Preview Kartu Rekening --}}
                            <div class="mt-2 p-4 rounded-xl bg-gradient-to-r from-slate-800 to-slate-700 text-white space-y-1">
                                <p class="text-xs text-slate-400 uppercase tracking-widest">Preview Tampilan Rekening</p>
                                <p class="text-lg font-bold font-mono tracking-wider" id="preview-account-number">{{ $settings['bank_account_number'] ?? '—' }}</p>
                                <div class="flex justify-between items-end mt-1">
                                    <span class="text-sm text-slate-300" id="preview-account-name">{{ $settings['bank_account_name'] ?? '—' }}</span>
                                    <span class="text-sm font-semibold text-pink-400" id="preview-bank-name">{{ $settings['bank_name'] ?? '—' }}</span>
                                </div>
                            </div>

                            {{-- Batas Waktu Bayar --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Batas Waktu Pembayaran</label>
                                <div class="flex items-center gap-3">
                                    <input type="number" name="payment_deadline" id="payment_deadline"
                                        value="{{ old('payment_deadline', $settings['payment_deadline'] ?? '60') }}"
                                        min="5" max="1440"
                                        class="w-32 px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all">
                                    <span class="text-sm text-slate-500 dark:text-slate-400">menit setelah booking dibuat</span>
                                </div>
                                <p class="mt-1 text-xs text-slate-400">Reservasi akan otomatis dibatalkan jika tidak dibayar dalam batas waktu ini. (5–1440 menit)</p>
                                @error('payment_deadline')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ==================== TAB: NOTIFIKASI (dinonaktifkan sementara) ====================
                Uncomment seluruh blok ini jika fitur notifikasi Email & WhatsApp sudah siap diimplementasikan.

                <div x-show="activeTab === 'notification'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                            <h3 class="font-semibold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                                Pengaturan Notifikasi
                            </h3>
                        </div>
                        <div class="p-6 space-y-6">
                            ... isi notifikasi email & whatsapp ...
                        </div>
                    </div>
                </div>
                --}}

                {{-- Tombol Simpan --}}
                <div class="flex justify-end pt-2">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-pink-500 to-pink-600 hover:from-pink-600 hover:to-pink-700 text-white text-sm font-semibold rounded-xl shadow-md shadow-pink-500/25 hover:shadow-pink-500/40 transition-all duration-200 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Pengaturan
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- Script preview rekening --}}
    @push('scripts')
    <script>
        const fields = {
            'bank_account_number': 'preview-account-number',
            'bank_account_name':   'preview-account-name',
            'bank_name':           'preview-bank-name',
        };
        Object.entries(fields).forEach(([inputId, previewId]) => {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            if (input && preview) {
                input.addEventListener('input', () => {
                    preview.textContent = input.value || '—';
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
