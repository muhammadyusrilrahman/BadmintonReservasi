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
                <button @click="activeTab = 'notification'"
                    :class="activeTab === 'notification' ? 'bg-white dark:bg-slate-700 text-slate-800 dark:text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'"
                    class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    Notifikasi
                </button>
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

                {{-- ==================== TAB: NOTIFIKASI ==================== --}}
                <div x-show="activeTab === 'notification'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                            <h3 class="font-semibold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                                <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                Pengaturan Notifikasi
                            </h3>
                        </div>
                        <div class="p-6 space-y-6">

                            {{-- Email Notifikasi --}}
                            <div>
                                <div class="flex items-center gap-2 mb-4">
                                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <h4 class="font-semibold text-slate-700 dark:text-slate-200 text-sm">Notifikasi Email</h4>
                                </div>
                                <div class="space-y-3">
                                    @foreach([
                                        ['key' => 'notif_email_booking', 'label' => 'Booking Baru', 'desc' => 'Kirim email ke admin saat ada booking baru masuk'],
                                        ['key' => 'notif_email_confirm', 'label' => 'Konfirmasi Pembayaran', 'desc' => 'Kirim email ke pelanggan saat pembayaran dikonfirmasi'],
                                        ['key' => 'notif_email_cancel', 'label' => 'Pembatalan Reservasi', 'desc' => 'Kirim email ke pelanggan saat reservasi dibatalkan'],
                                    ] as $notif)
                                        <label class="flex items-start gap-4 p-4 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer transition-colors">
                                            <div class="relative flex-shrink-0 mt-0.5">
                                                <input type="checkbox" name="{{ $notif['key'] }}" id="{{ $notif['key'] }}"
                                                    {{ ($settings[$notif['key']] ?? '0') == '1' ? 'checked' : '' }}
                                                    class="sr-only peer">
                                                <div class="w-10 h-6 bg-slate-200 dark:bg-slate-600 rounded-full peer-checked:bg-pink-500 transition-colors duration-200"></div>
                                                <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-4"></div>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ $notif['label'] }}</p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $notif['desc'] }}</p>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- WhatsApp Notifikasi --}}
                            <div>
                                <div class="flex items-center gap-2 mb-4">
                                    <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    <h4 class="font-semibold text-slate-700 dark:text-slate-200 text-sm">Notifikasi WhatsApp</h4>
                                    <span class="px-2 py-0.5 bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400 text-xs rounded-full">Perlu konfigurasi API</span>
                                </div>
                                <div class="space-y-3">
                                    @foreach([
                                        ['key' => 'notif_wa_booking', 'label' => 'Booking Baru', 'desc' => 'Kirim pesan WA ke admin saat ada booking baru masuk'],
                                        ['key' => 'notif_wa_reminder', 'label' => 'Pengingat Jadwal', 'desc' => 'Kirim pesan WA ke pelanggan 1 jam sebelum jadwal main'],
                                    ] as $notif)
                                        <label class="flex items-start gap-4 p-4 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer transition-colors">
                                            <div class="relative flex-shrink-0 mt-0.5">
                                                <input type="checkbox" name="{{ $notif['key'] }}" id="{{ $notif['key'] }}"
                                                    {{ ($settings[$notif['key']] ?? '0') == '1' ? 'checked' : '' }}
                                                    class="sr-only peer">
                                                <div class="w-10 h-6 bg-slate-200 dark:bg-slate-600 rounded-full peer-checked:bg-pink-500 transition-colors duration-200"></div>
                                                <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-4"></div>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ $notif['label'] }}</p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $notif['desc'] }}</p>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

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
