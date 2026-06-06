<x-layouts.app :title="$title">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm mb-6">
        <a href="{{ route('admin.courts.index') }}" class="text-slate-500 dark:text-slate-400 hover:text-pink-600 dark:hover:text-pink-400 transition-colors">Kelola Lapangan</a>
        <svg class="w-4 h-4 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-slate-800 dark:text-white font-medium">Edit Lapangan</span>
    </nav>

    <div class="max-w-2xl">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">

            {{-- Header --}}
            <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 bg-gradient-to-r from-[#1e3a5f]/5 to-transparent">
                <h1 class="text-lg font-bold text-slate-800 dark:text-white">Edit Lapangan: {{ $court->name }}</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Ubah informasi data lapangan badminton.</p>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('admin.courts.update', $court) }}" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                {{-- Nama Lapangan --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Nama Lapangan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $court->name) }}"
                           placeholder="Contoh: Lapangan A"
                           class="w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border
                               {{ $errors->has('name') ? 'border-red-400 dark:border-red-500 focus:ring-red-500' : 'border-slate-200 dark:border-slate-700 focus:ring-pink-500/50 focus:border-pink-500' }}
                               rounded-xl text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 transition">
                    @error('name')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Jenis Lapangan --}}
                <div>
                    <label for="type" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Jenis Lapangan <span class="text-red-500">*</span>
                    </label>
                    <select id="type" name="type"
                            class="w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border
                                {{ $errors->has('type') ? 'border-red-400 dark:border-red-500 focus:ring-red-500' : 'border-slate-200 dark:border-slate-700 focus:ring-pink-500/50 focus:border-pink-500' }}
                                rounded-xl text-slate-800 dark:text-white focus:outline-none focus:ring-2 transition">
                        <option value="">-- Pilih Jenis --</option>
                        @foreach($typeLabels as $value => $label)
                            <option value="{{ $value }}" @selected(old('type', $court->type) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Harga per Jam --}}
                <div>
                    <label for="price_per_hour" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Harga Dasar / Jam <span class="text-slate-400 text-xs font-normal">(opsional)</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-medium text-slate-500 dark:text-slate-400">Rp</span>
                        <input type="number" id="price_per_hour" name="price_per_hour" value="{{ old('price_per_hour', $court->price_per_hour) }}"
                               placeholder="50000" min="0" step="1000"
                               class="w-full pl-12 pr-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border
                                   {{ $errors->has('price_per_hour') ? 'border-red-400 dark:border-red-500 focus:ring-red-500' : 'border-slate-200 dark:border-slate-700 focus:ring-pink-500/50 focus:border-pink-500' }}
                                   rounded-xl text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 transition">
                    </div>
                    @error('price_per_hour')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Deskripsi <span class="text-slate-400 text-xs font-normal">(opsional)</span>
                    </label>
                    <textarea id="description" name="description" rows="3"
                              placeholder="Jelaskan fasilitas dan kondisi lapangan..."
                              class="w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border
                                  {{ $errors->has('description') ? 'border-red-400 dark:border-red-500 focus:ring-red-500' : 'border-slate-200 dark:border-slate-700 focus:ring-pink-500/50 focus:border-pink-500' }}
                                  rounded-xl text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 transition resize-none">{{ old('description', $court->description) }}</textarea>
                    @error('description')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Foto --}}
                <div x-data="{ preview: '{{ $court->photo_url }}' }">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Foto Lapangan <span class="text-slate-400 text-xs font-normal">(opsional, maks. 2MB)</span>
                    </label>
                    <div class="flex items-start gap-4">
                        {{-- Preview Box --}}
                        <div class="flex-shrink-0 w-24 h-24 rounded-xl border-2 border-dashed border-slate-200 dark:border-slate-700 overflow-hidden bg-slate-50 dark:bg-slate-800 flex items-center justify-center">
                            <template x-if="preview">
                                <img :src="preview" alt="Preview" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!preview">
                                <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </template>
                        </div>
                        {{-- File Input --}}
                        <div class="flex-1">
                            <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp"
                                   @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : '{{ $court->photo_url }}'"
                                   class="w-full text-sm text-slate-500 dark:text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-[#1e3a5f]/10 file:text-[#1e3a5f] dark:file:bg-pink-500/10 dark:file:text-pink-400 hover:file:bg-[#1e3a5f]/20 transition cursor-pointer">
                            <p class="mt-1.5 text-xs text-slate-400">Biarkan kosong jika tidak ingin mengubah foto. JPG, PNG, atau WebP. Maks 2MB.</p>
                            @error('photo')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Status Aktif --}}
                <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
                    <div>
                        <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Status Aktif</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Lapangan aktif dapat dipesan oleh customer</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="is_active" name="is_active" value="1"
                               {{ old('is_active', $court->is_active ? '1' : '0') === '1' ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-300 dark:bg-slate-600 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-pink-500/50 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-[#1e3a5f] peer-checked:to-[#e91e8c]"></div>
                    </label>
                </div>

                {{-- Buttons --}}
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-[#1e3a5f] to-[#e91e8c] text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:shadow-pink-500/25 hover:-translate-y-0.5 transition-all duration-200">
                        Perbarui Lapangan
                    </button>
                    <a href="{{ route('admin.courts.index') }}"
                       class="px-6 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

</x-layouts.app>
