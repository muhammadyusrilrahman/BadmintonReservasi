{{-- Sidebar Component --}}
@php
    $authUser = auth()->user();
    $userRole = $authUser->roles->first()?->name ?? 'User';
    $userInitial = strtoupper(substr($authUser->name, 0, 1));
@endphp

<aside x-show="sidebarOpen"
       x-transition:enter="transition-transform ease-out duration-300"
       x-transition:enter-start="-translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition-transform ease-in duration-200"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="-translate-x-full"
       class="fixed inset-y-0 left-0 z-40 w-72 flex flex-col bg-gradient-to-b from-[#0f1d36] via-[#152647] to-[#0f1d36] shadow-2xl shadow-navy-900/50"
       x-cloak>

    {{-- Logo --}}
    <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
        <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-gradient-to-br from-pink-500 to-pink-600 flex items-center justify-center shadow-lg shadow-pink-500/30">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-7 h-7 object-contain" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <span class="text-white font-extrabold text-sm hidden items-center justify-center">AS</span>
        </div>
        <div class="min-w-0">
            <h1 class="text-white font-bold text-base leading-tight truncate">Adenia Salsa</h1>
            <p class="text-slate-400 text-xs">Badminton Center</p>
        </div>
        <button @click="sidebarOpen = false" class="lg:hidden ml-auto text-slate-400 hover:text-white transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- User Info --}}
    <div class="px-4 py-4">
        <div class="flex items-center gap-3 px-3 py-3 rounded-xl bg-white/5 border border-white/5">
            <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-gradient-to-br from-pink-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm shadow-md">{{ $userInitial }}</div>
            <div class="min-w-0 flex-1">
                <p class="text-white text-sm font-medium truncate">{{ $authUser->name }}</p>
                <p class="text-slate-400 text-xs capitalize">
                    <span class="inline-flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        {{ $userRole }}
                    </span>
                </p>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-4 pb-4 space-y-6 scrollbar-thin">

        {{-- Dashboard --}}
        <div>
            <p class="px-3 mb-2 text-[10px] font-semibold uppercase tracking-widest text-slate-500">Menu Utama</p>
            <div class="space-y-1">
                <x-sidebar-link href="{{ route($authUser->getDashboardRoute()) }}" :active="request()->routeIs('*.dashboard')" icon="dashboard">
                    Dashboard
                </x-sidebar-link>
            </div>
        </div>

        @if($userRole === 'admin')
        {{-- Admin: Manajemen --}}
        <div>
            <p class="px-3 mb-2 text-[10px] font-semibold uppercase tracking-widest text-slate-500">Manajemen</p>
            <div class="space-y-1">
                <x-sidebar-link href="{{ route('admin.courts.index') }}" :active="request()->routeIs('admin.courts.*')" icon="court">Kelola Lapangan</x-sidebar-link>
                <x-sidebar-link href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.*')" icon="users">Kelola Pengguna</x-sidebar-link>
                <x-sidebar-link href="{{ route('admin.promos.index') }}" :active="request()->routeIs('admin.promos.*')" icon="promo">Kelola Promo</x-sidebar-link>
                <x-sidebar-link href="{{ route('admin.reservations.index') }}" :active="request()->routeIs('admin.reservations.*')" icon="calendar">Reservasi</x-sidebar-link>
                <x-sidebar-link href="{{ route('admin.refunds.index') }}" :active="request()->routeIs('admin.refunds.*')" icon="money">Persetujuan Refund</x-sidebar-link>
            </div>
        </div>
        <div>
            <p class="px-3 mb-2 text-[10px] font-semibold uppercase tracking-widest text-slate-500">Keuangan & Laporan</p>
            <div class="space-y-1">
                <x-sidebar-link href="{{ route('admin.finance.index') }}" :active="request()->routeIs('admin.finance.*')" icon="chart">Keuangan</x-sidebar-link>
                <x-sidebar-link href="{{ route('admin.reports.index') }}" :active="request()->routeIs('admin.reports.*')" icon="document">Laporan</x-sidebar-link>
            </div>
        </div>
        <div>
            <p class="px-3 mb-2 text-[10px] font-semibold uppercase tracking-widest text-slate-500">Sistem</p>
            <div class="space-y-1">
                <x-sidebar-link href="{{ route('admin.broadcast-maintenance.index') }}" :active="request()->routeIs('admin.broadcast-maintenance.*')" icon="tool">Broadcast Maintenance</x-sidebar-link>
                <x-sidebar-link href="{{ route('admin.activity-logs.index') }}" :active="request()->routeIs('admin.activity-logs.index')" icon="log">Activity Log</x-sidebar-link>
                <x-sidebar-link href="#" :active="request()->routeIs('admin.settings.*')" icon="settings">Pengaturan</x-sidebar-link>
            </div>
        </div>
        @elseif($userRole === 'customer')
        {{-- Customer --}}
        <div>
            <p class="px-3 mb-2 text-[10px] font-semibold uppercase tracking-widest text-slate-500">Reservasi</p>
            <div class="space-y-1">
                <x-sidebar-link href="{{ route('customer.booking.create') }}" :active="request()->routeIs('customer.booking.*')" icon="calendar">Booking Lapangan</x-sidebar-link>
                <x-sidebar-link href="{{ route('customer.reservations.index') }}" :active="request()->routeIs('customer.reservations.*')" icon="history">Reservasi Saya</x-sidebar-link>
                <x-sidebar-link href="{{ route('customer.refunds.index') }}" :active="request()->routeIs('customer.refunds.*')" icon="money">Refund Saya</x-sidebar-link>
            </div>
        </div>
        @elseif($userRole === 'kasir')
        {{-- Kasir --}}
        <div>
            <p class="px-3 mb-2 text-[10px] font-semibold uppercase tracking-widest text-slate-500">Transaksi</p>
            <div class="space-y-1">
                <x-sidebar-link href="{{ route('kasir.promos.index') }}" :active="request()->routeIs('kasir.promos.*')" icon="promo">Daftar Promo</x-sidebar-link>
                <x-sidebar-link href="{{ route('kasir.transactions.index') }}" :active="request()->routeIs('kasir.transactions.*')" icon="money">Transaksi</x-sidebar-link>
                <x-sidebar-link href="{{ route('kasir.today.index') }}" :active="request()->routeIs('kasir.today.*')" icon="calendar">Reservasi Hari Ini</x-sidebar-link>
                <x-sidebar-link href="{{ route('kasir.daily-report.index') }}" :active="request()->routeIs('kasir.daily-report.*')" icon="chart">Laporan Harian</x-sidebar-link>
            </div>
        </div>
        @elseif($userRole === 'staff')
        {{-- Staff --}}
        <div>
            <p class="px-3 mb-2 text-[10px] font-semibold uppercase tracking-widest text-slate-500">Operasional</p>
            <div class="space-y-1">
                <x-sidebar-link :href="route('staff.checkin.index')" :active="request()->routeIs('staff.checkin.index') || request()->routeIs('staff.checkin.verify')" icon="calendar">Check-in Hari Ini</x-sidebar-link>
                <x-sidebar-link :href="route('staff.checkin.history')" :active="request()->routeIs('staff.checkin.history')" icon="history">Riwayat Check-in</x-sidebar-link>
            </div>
        </div>
        <div>
            <p class="px-3 mb-2 text-[10px] font-semibold uppercase tracking-widest text-slate-500">Lapangan</p>
            <div class="space-y-1">
                <x-sidebar-link :href="route('staff.schedule.index')" :active="request()->routeIs('staff.schedule.*')" icon="calendar">Jadwal Lapangan</x-sidebar-link>
                <x-sidebar-link :href="route('staff.maintenance.index')" :active="request()->routeIs('staff.maintenance.*')" icon="tool">Maintenance</x-sidebar-link>
            </div>
        </div>
        @endif

        {{-- Akun --}}
        <div>
            <p class="px-3 mb-2 text-[10px] font-semibold uppercase tracking-widest text-slate-500">Akun</p>
            <div class="space-y-1">
                <x-sidebar-link href="{{ route('profile.edit') }}" :active="request()->routeIs('profile.*')" icon="profile">Profil Saya</x-sidebar-link>
            </div>
        </div>
    </nav>

    {{-- Logout --}}
    <div class="px-4 py-4 border-t border-white/10">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-all duration-200 text-sm group">
                <svg class="w-5 h-5 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Keluar
            </button>
        </form>
    </div>
</aside>
