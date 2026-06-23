{{-- Navbar Component --}}
@props(['title' => 'Dashboard'])

@php
    $authUser = auth()->user();
    $unreadCount = $authUser->unreadNotifications()->count();
    $unreadNotifications = $unreadCount > 0 ? $authUser->unreadNotifications()->take(5)->get() : collect();
@endphp

<header class="sticky top-0 z-20 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border-b border-slate-200/80 dark:border-slate-800/80">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
            <button @click="sidebarOpen = !sidebarOpen" class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-all duration-200" id="sidebar-toggle">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <h2 class="hidden sm:block text-lg font-semibold text-slate-800 dark:text-white">{{ $title ?? 'Dashboard' }}</h2>
        </div>
        <div class="flex items-center gap-2">
            {{-- Dark Mode --}}
            <button @click="darkMode = !darkMode" class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-all duration-200" id="dark-mode-toggle">
                <svg x-show="darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            </button>

            {{-- Notifications --}}
            <div x-data="{ notifOpen: false }" class="relative">
                <button @click="notifOpen = !notifOpen" class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all duration-200 relative" id="notification-bell">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    @if($unreadCount > 0)
                    <span class="absolute -top-0.5 -right-0.5 flex items-center justify-center w-5 h-5 text-[10px] font-bold text-white bg-pink-500 rounded-full shadow-lg shadow-pink-500/30 animate-pulse">{{ min($unreadCount, 9) }}{{ $unreadCount > 9 ? '+' : '' }}</span>
                    @endif
                </button>
                <div x-show="notifOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-2" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-2" @click.outside="notifOpen = false" class="absolute right-0 mt-3 w-80 bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl rounded-2xl shadow-xl border border-slate-200/60 dark:border-slate-800/80 overflow-hidden z-50" x-cloak>
                    <div class="px-4 py-3 border-b border-slate-200/60 dark:border-slate-800/80 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-slate-800 dark:text-white">Notifikasi</h3>
                        @if($unreadCount > 0)
                        <form method="POST" action="{{ route('customer.notifications.read-all') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-xs font-semibold text-indigo-650 dark:text-indigo-400 hover:underline">Tandai semua dibaca</button>
                        </form>
                        @endif
                    </div>
                    <div class="max-h-72 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/40">
                        @forelse($unreadNotifications as $notification)
                        <div class="px-4 py-3 hover:bg-slate-50/80 dark:hover:bg-slate-800/30 transition duration-150 relative">
                            <form method="POST" action="{{ route('customer.notifications.read', $notification->id) }}">
                                @csrf
                                <button type="submit" class="w-full text-left focus:outline-none">
                                    <div class="flex items-start gap-2.5">
                                        <span class="text-base mt-0.5">
                                            @php
                                                $iconMap = [
                                                    'booking_success' => '🏸',
                                                    'payment_success' => '✅',
                                                    'payment_failed' => '❌',
                                                    'refund_approved' => '💰',
                                                    'reschedule_approved' => '🕒',
                                                    'schedule_changed' => '⚠️',
                                                    'maintenance' => '🛠️',
                                                ];
                                                echo $iconMap[$notification->data['type'] ?? ''] ?? '🔔';
                                            @endphp
                                        </span>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate">{{ $notification->data['title'] ?? 'Pemberitahuan Baru' }}</p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-2 leading-normal">{{ $notification->data['message'] ?? '' }}</p>
                                            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </button>
                            </form>
                        </div>
                        @empty
                        <div class="px-4 py-8 text-center">
                            <div class="w-10 h-10 mx-auto rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 dark:text-slate-550 mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                            </div>
                            <p class="text-xs font-medium text-slate-700 dark:text-slate-300">Tidak ada notifikasi baru</p>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">Semua pemberitahuan sudah dibaca.</p>
                        </div>
                        @endforelse
                    </div>
                    @if(auth()->user()->hasRole('customer'))
                    <a href="{{ route('customer.notifications.index') }}" class="block px-4 py-2 text-center text-xs font-semibold text-indigo-600 dark:text-indigo-400 bg-slate-50 dark:bg-slate-800/40 hover:bg-slate-100 dark:hover:bg-slate-800 transition duration-150 border-t border-slate-200/60 dark:border-slate-800/80">
                        Lihat Semua Notifikasi
                    </a>
                    @endif
                </div>
            </div>

            {{-- User Dropdown --}}
            <div x-data="{ profileOpen: false }" class="relative">
                <button @click="profileOpen = !profileOpen" class="flex items-center gap-2 pl-2 pr-3 py-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-all duration-200" id="user-profile-dropdown">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-[#1e3a5f] to-[#e91e8c] flex items-center justify-center text-white font-semibold text-xs shadow-md">{{ strtoupper(substr($authUser->name, 0, 1)) }}</div>
                    <span class="hidden md:inline text-sm font-medium text-slate-700 dark:text-slate-200">{{ $authUser->name }}</span>
                    <svg class="w-4 h-4 text-slate-400 transition-transform" :class="profileOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="profileOpen" x-transition @click.outside="profileOpen = false" class="absolute right-0 mt-3 w-56 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden py-1" x-cloak>
                    <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-700">
                        <p class="text-sm font-medium text-slate-800 dark:text-white">{{ $authUser->name }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ $authUser->email }}</p>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Profil Saya
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
