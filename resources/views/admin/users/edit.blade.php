<x-layouts.app :title="$title">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-slate-500 dark:text-slate-400 hover:text-pink-600 dark:hover:text-pink-400 transition-colors">Kelola Pengguna</a>
        <svg class="w-4 h-4 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-slate-800 dark:text-white font-medium">Edit Pengguna</span>
    </nav>

    <div class="max-w-2xl">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">

            {{-- Header --}}
            <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 bg-gradient-to-r from-[#1e3a5f]/5 to-transparent">
                <h1 class="text-lg font-bold text-slate-800 dark:text-white">Edit Pengguna: {{ $user->name }}</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Perbarui informasi dan hak akses pengguna.</p>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                {{-- Nama Lengkap --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                           placeholder="Contoh: Budi Santoso"
                           class="w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border
                               {{ $errors->has('name') ? 'border-red-400 dark:border-red-500 focus:ring-red-500' : 'border-slate-200 dark:border-slate-700 focus:ring-pink-500/50 focus:border-pink-500' }}
                               rounded-xl text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 transition">
                    @error('name')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email & Phone --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Alamat Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                               placeholder="contoh@adenialsa.com"
                               class="w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border
                                   {{ $errors->has('email') ? 'border-red-400 dark:border-red-500 focus:ring-red-500' : 'border-slate-200 dark:border-slate-700 focus:ring-pink-500/50 focus:border-pink-500' }}
                                   rounded-xl text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 transition">
                        @error('email')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Nomor Telepon <span class="text-slate-400 text-xs font-normal">(opsional)</span>
                        </label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                               placeholder="08123456789"
                               class="w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border
                                   {{ $errors->has('phone') ? 'border-red-400 dark:border-red-500 focus:ring-red-500' : 'border-slate-200 dark:border-slate-700 focus:ring-pink-500/50 focus:border-pink-500' }}
                                   rounded-xl text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 transition">
                        @error('phone')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Peran / Role --}}
                <div>
                    <label for="role" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Peran (Role) <span class="text-red-500">*</span>
                    </label>
                    <select id="role" name="role"
                            class="w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border
                                {{ $errors->has('role') ? 'border-red-400 dark:border-red-500 focus:ring-red-500' : 'border-slate-200 dark:border-slate-700 focus:ring-pink-500/50 focus:border-pink-500' }}
                                rounded-xl text-slate-800 dark:text-white focus:outline-none focus:ring-2 transition capitalize"
                            @if(auth()->id() === $user->id) disabled @endif>
                        <option value="">-- Pilih Peran --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role }}" @selected(old('role', $user->roles->first()?->name) === $role)>{{ $role }}</option>
                        @endforeach
                    </select>
                    @if(auth()->id() === $user->id)
                        <input type="hidden" name="role" value="{{ $user->roles->first()?->name }}">
                        <p class="mt-1.5 text-xs text-amber-600 dark:text-amber-500">Anda tidak dapat mengubah peran Anda sendiri saat sedang login.</p>
                    @endif
                    @error('role')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700">
                    <div class="sm:col-span-2">
                        <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Ubah Kata Sandi</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Biarkan kosong jika tidak ingin mengubah kata sandi saat ini.</p>
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Kata Sandi Baru
                        </label>
                        <input type="password" id="password" name="password"
                               placeholder="Minimal 8 karakter"
                               class="w-full px-4 py-2.5 text-sm bg-white dark:bg-slate-900 border
                                   {{ $errors->has('password') ? 'border-red-400 dark:border-red-500 focus:ring-red-500' : 'border-slate-200 dark:border-slate-700 focus:ring-pink-500/50 focus:border-pink-500' }}
                                   rounded-xl text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 transition">
                        @error('password')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Konfirmasi Kata Sandi Baru
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               placeholder="Ulangi kata sandi baru"
                               class="w-full px-4 py-2.5 text-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-pink-500 transition">
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-[#1e3a5f] to-[#e91e8c] text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:shadow-pink-500/25 hover:-translate-y-0.5 transition-all duration-200">
                        Perbarui Pengguna
                    </button>
                    <a href="{{ route('admin.users.index') }}"
                       class="px-6 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

</x-layouts.app>
