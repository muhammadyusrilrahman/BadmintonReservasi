<x-layouts.app :title="$title">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Kelola Pengguna</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Kelola akses dan data pengguna Adenia Salsa.</p>
        </div>
        <a href="{{ route('admin.users.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-[#1e3a5f] to-[#e91e8c] text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:shadow-pink-500/25 hover:-translate-y-0.5 transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Pengguna
        </a>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('admin.users.index') }}"
          class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            {{-- Search --}}
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama atau email..."
                       class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-pink-500 transition">
            </div>
            {{-- Role Filter --}}
            <select name="role"
                    class="px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-pink-500 transition capitalize">
                <option value="">Semua Peran</option>
                @foreach($roles as $role)
                    <option value="{{ $role }}" @selected(request('role') === $role)>{{ $role }}</option>
                @endforeach
            </select>
            {{-- Submit --}}
            <button type="submit"
                    class="px-5 py-2.5 bg-[#1e3a5f] text-white text-sm font-medium rounded-xl hover:bg-[#162d4a] transition-colors">
                Filter
            </button>
            @if(request()->hasAny(['search','role']))
                <a href="{{ route('admin.users.index') }}"
                   class="px-4 py-2.5 text-sm text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl transition-colors">
                    Reset
                </a>
            @endif
        </div>
    </form>

    {{-- Table Card --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
        {{-- Table --}}
        @if($users->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <p class="text-slate-500 dark:text-slate-400 font-medium">Tidak ada pengguna ditemukan</p>
                <p class="text-slate-400 dark:text-slate-500 text-sm mt-1">
                    @if(request()->hasAny(['search','role']))
                        Coba ubah filter pencarian Anda
                    @else
                        Mulai dengan menambahkan pengguna pertama
                    @endif
                </p>
                @if(!request()->hasAny(['search','role']))
                    <a href="{{ route('admin.users.create') }}"
                       class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-[#1e3a5f] to-[#e91e8c] text-white text-sm font-medium rounded-xl hover:shadow-lg transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Pengguna
                    </a>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pengguna</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Peran</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Telepon</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Terdaftar</th>
                            <th class="px-6 py-3.5 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($users as $user)
                            <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                {{-- Number --}}
                                <td class="px-6 py-4 text-sm text-slate-400 dark:text-slate-500 w-12">
                                    {{ $users->firstItem() + $loop->index }}
                                </td>

                                {{-- Name & Email --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-800 dark:text-white text-sm">
                                                {{ $user->name }}
                                                @if(auth()->id() === $user->id)
                                                    <span class="ml-1 text-[10px] bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded-md dark:bg-blue-900 dark:text-blue-300">Anda</span>
                                                @endif
                                            </p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Role --}}
                                <td class="px-6 py-4">
                                    @php
                                        $role = $user->roles->first()?->name ?? 'None';
                                        $roleColor = match($role) {
                                            'admin'    => 'purple',
                                            'kasir'    => 'emerald',
                                            'staff'    => 'blue',
                                            'customer' => 'slate',
                                            default    => 'slate',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium capitalize
                                        bg-{{ $roleColor }}-50 dark:bg-{{ $roleColor }}-500/10
                                        text-{{ $roleColor }}-700 dark:text-{{ $roleColor }}-400">
                                        {{ $role }}
                                    </span>
                                </td>

                                {{-- Phone --}}
                                <td class="px-6 py-4">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">
                                        {{ $user->phone ?? '-' }}
                                    </span>
                                </td>

                                {{-- Registered At --}}
                                <td class="px-6 py-4">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">
                                        {{ $user->created_at->format('d M Y') }}
                                    </span>
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 lg:group-focus-within:opacity-100 transition-opacity">
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-500/20 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </a>
                                        @if(auth()->id() !== $user->id)
                                            <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" id="delete-form-{{ $user->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                        x-data
                                                        @click="$dispatch('open-global-confirm', { formId: 'delete-form-{{ $user->id }}', message: 'Yakin ingin menghapus pengguna ini? Aksi ini tidak dapat dibatalkan.' })"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-500/10 rounded-lg hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors">
                                                    <svg class="w-3.5 h-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-4">
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }} pengguna
                    </p>
                    {{ $users->links() }}
                </div>
            @else
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Total {{ $users->total() }} pengguna
                    </p>
                </div>
            @endif
        @endif
    </div>

</x-layouts.app>
