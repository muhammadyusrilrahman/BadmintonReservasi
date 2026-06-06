<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: (() => { try { return localStorage.getItem('darkMode') === 'true' } catch(e) { return false } })(), sidebarOpen: window.innerWidth >= 1024 }" x-init="$watch('darkMode', val => { try { localStorage.setItem('darkMode', val) } catch(e) {} })" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sistem Informasi Reservasi Lapangan Badminton Adenia Salsa">

    <title>{{ $title ?? 'Dashboard' }} — {{ config('app.name', 'Adenia Salsa') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="font-sans antialiased bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-200">
    {{-- Skip Navigation Link (L4: Aksesibilitas keyboard) --}}
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-pink-600 focus:text-white focus:rounded-lg focus:shadow-lg focus:outline-none">
        Langsung ke konten utama
    </a>
    <div class="min-h-screen flex">

        {{-- Sidebar Overlay (mobile) --}}
        <div x-show="sidebarOpen" x-transition.opacity.duration.200ms @click="sidebarOpen = false"
             class="fixed inset-0 z-30 bg-slate-900/60 backdrop-blur-sm lg:hidden" x-cloak></div>

        <x-sidebar />

        {{-- Main --}}
        <div class="flex-1 flex flex-col min-h-screen transition-all duration-300"
             :class="sidebarOpen ? 'lg:ml-72' : 'lg:ml-0'">

            <x-navbar :title="$title ?? 'Dashboard'" />

            {{-- Flash Messages --}}
            @session('success') <div class="px-4 sm:px-6 lg:px-8 pt-4"><x-alert type="success" :message="$value" /></div> @endsession
            @session('error') <div class="px-4 sm:px-6 lg:px-8 pt-4"><x-alert type="error" :message="$value" /></div> @endsession
            @session('warning') <div class="px-4 sm:px-6 lg:px-8 pt-4"><x-alert type="warning" :message="$value" /></div> @endsession
            @session('info') <div class="px-4 sm:px-6 lg:px-8 pt-4"><x-alert type="info" :message="$value" /></div> @endsession

            <main id="main-content" class="flex-1 p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </main>

            <footer class="border-t border-slate-200 dark:border-slate-800 px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-2 text-sm text-slate-500 dark:text-slate-400">
                    <p>&copy; {{ date('Y') }} Adenia Salsa Badminton. All rights reserved.</p>
                    <p class="flex items-center gap-1">
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse" aria-hidden="true"></span>
                        Sistem aktif
                    </p>
                </div>
            </footer>
        </div>
    </div>

    <x-global-confirm-modal />

    @stack('scripts')
</body>
</html>
