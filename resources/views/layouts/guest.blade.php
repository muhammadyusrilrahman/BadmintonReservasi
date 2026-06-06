<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sistem Informasi Reservasi Lapangan Badminton Adenia Salsa">

    <title>{{ config('app.name', 'Adenia Salsa') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex">

        {{-- Left Side — Decorative Panel --}}
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gradient-to-br from-[#0f1d36] via-[#1e3a5f] to-[#152647]">
            {{-- Decorative shapes --}}
            <div class="absolute inset-0">
                <div class="absolute top-20 left-10 w-72 h-72 bg-pink-500/10 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute bottom-20 right-10 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
                <div class="absolute top-1/2 left-1/3 w-64 h-64 bg-purple-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
            </div>

            {{-- Pattern overlay --}}
            <div class="absolute inset-0 opacity-5">
                <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                            <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="0.5"/>
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#grid)" />
                </svg>
            </div>

            {{-- Content --}}
            <div class="relative z-10 flex flex-col justify-center px-12 xl:px-20">
                {{-- Logo --}}
                <div class="flex items-center gap-4 mb-12">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-pink-500 to-pink-600 flex items-center justify-center shadow-xl shadow-pink-500/30">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-9 h-9 object-contain" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <span class="text-white font-extrabold text-lg hidden items-center justify-center">AS</span>
                    </div>
                    <div>
                        <h1 class="text-white text-2xl font-bold">Adenia Salsa</h1>
                        <p class="text-slate-400 text-sm">Badminton Center</p>
                    </div>
                </div>

                {{-- Tagline --}}
                <h2 class="text-4xl xl:text-5xl font-extrabold text-white leading-tight mb-6">
                    Reservasi Lapangan<br>
                    <span class="bg-gradient-to-r from-pink-400 to-pink-600 bg-clip-text text-transparent">Badminton Online</span>
                </h2>
                <p class="text-slate-400 text-lg leading-relaxed max-w-md">
                    Booking lapangan badminton dengan mudah, cepat, dan kapan saja. Nikmati pengalaman bermain terbaik di Adenia Salsa.
                </p>

                {{-- Feature pills --}}
                <div class="flex flex-wrap gap-3 mt-10">
                    <div class="flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10 text-white/70 text-sm">
                        <svg class="w-4 h-4 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Booking Online 24/7
                    </div>
                    <div class="flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10 text-white/70 text-sm">
                        <svg class="w-4 h-4 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Konfirmasi Instan
                    </div>
                    <div class="flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10 text-white/70 text-sm">
                        <svg class="w-4 h-4 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Lapangan Premium
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Side — Auth Form --}}
        <div class="w-full lg:w-1/2 flex flex-col justify-center items-center px-6 py-12 bg-slate-50 dark:bg-slate-950 transition-colors duration-300">
            {{-- Dark mode toggle --}}
            <div class="absolute top-6 right-6">
                <button @click="darkMode = !darkMode" class="w-10 h-10 rounded-xl flex items-center justify-center text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800 transition-all duration-200">
                    <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                </button>
            </div>

            {{-- Mobile Logo (shown on small screens) --}}
            <div class="lg:hidden flex items-center gap-3 mb-8">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-pink-500 to-pink-600 flex items-center justify-center shadow-lg shadow-pink-500/30">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-8 h-8 object-contain" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <span class="text-white font-extrabold text-sm hidden items-center justify-center">AS</span>
                </div>
                <div>
                    <h1 class="text-slate-800 dark:text-white text-xl font-bold">Adenia Salsa</h1>
                    <p class="text-slate-500 dark:text-slate-400 text-xs">Badminton Center</p>
                </div>
            </div>

            {{-- Form Card --}}
            <div class="w-full max-w-md">
                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-slate-900/50 border border-slate-200 dark:border-slate-800 p-8">
                    {{ $slot }}
                </div>

                {{-- Footer --}}
                <p class="text-center text-sm text-slate-400 dark:text-slate-500 mt-8">
                    &copy; {{ date('Y') }} Adenia Salsa Badminton. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
