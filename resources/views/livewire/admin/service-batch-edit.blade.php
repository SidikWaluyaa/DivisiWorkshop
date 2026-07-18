<div class="py-12 bg-gray-50 min-h-screen" x-data="{ search: '' }">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        {{-- Header Card --}}
        <div class="mb-8 bg-gradient-to-r from-teal-500 to-emerald-600 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden border border-white/20">
            <div class="absolute right-0 top-0 translate-x-20 -translate-y-20 w-80 h-80 rounded-full bg-white/10 blur-3xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h1 class="text-2xl font-black tracking-tight">Excel Mode: Edit & Tambah Jasa Massal</h1>
                    <p class="text-teal-50/90 text-sm font-medium mt-2 leading-relaxed">
                        Kelola seluruh master data layanan dalam bentuk tabel spreadsheet. Anda bisa mengedit kolom sel langsung dan menyisipkan baris layanan baru sekaligus.
                    </p>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('admin.services.index') }}" 
                       class="px-5 py-3 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs uppercase tracking-wider backdrop-blur-md border border-white/10 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Batal &amp; Kembali
                    </a>
                    <button wire:click="save" 
                            class="px-6 py-3 rounded-2xl bg-white hover:bg-teal-50 text-teal-700 font-extrabold text-xs uppercase tracking-widest shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V3"></path></svg>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>

        {{-- Excel Table Card --}}
        <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-gray-50/50">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="p-2 bg-teal-50 rounded-xl text-teal-600 font-bold text-xs uppercase tracking-wider">
                        Spreadsheet Grid
                    </span>
                    <span class="text-xs text-gray-500 font-bold">
                        Total Baris: {{ count($services) }}
                    </span>
                </div>
                
                {{-- Search & Add Bar --}}
                <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <div class="relative w-full md:w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" x-model="search" 
                               class="w-full pl-9 pr-3.5 py-1.5 border border-gray-200 rounded-xl text-xs focus:ring-1 focus:ring-teal-500 focus:border-teal-500 shadow-sm transition-all" 
                               placeholder="Cari nama, kategori, deskripsi...">
                    </div>
                    
                    <button wire:click="addRow" 
                            class="px-4 py-2 rounded-xl bg-teal-50 hover:bg-teal-100/80 text-teal-700 font-bold text-xs uppercase tracking-wider border border-teal-200/50 shadow-sm hover:shadow transition-all flex items-center gap-2 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Baris
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse table-fixed min-w-[1200px]">
                    <thead>
                        <tr class="bg-gray-100/80 border-b border-gray-200 text-gray-400 font-bold uppercase tracking-wider text-[9px] select-none">
                            <th class="py-3 px-4 w-[6%] text-center">Status</th>
                            <th class="py-3 px-4 w-[20%]">Nama Layanan</th>
                            <th class="py-3 px-4 w-[13%]">Kategori</th>
                            <th class="py-3 px-4 w-[11%]">Harga (Rp)</th>
                            <th class="py-3 px-4 w-[10%]">Durasi (Mnt)</th>
                            <th class="py-3 px-4 w-[8%]">Hari Kerja (HK)</th>
                            <th class="py-3 px-4 w-[9%]">Fast Track?</th>
                            <th class="py-3 px-4 w-[18%]">Deskripsi</th>
                            <th class="py-3 px-4 w-[5%] text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs">
                        @foreach($services as $index => $item)
                            <tr x-show="search === '' || 
                                        {{ $item['is_new'] ? 'true' : 'false' }} || 
                                        '{{ strtolower(addslashes($item['name'])) }}'.includes(search.toLowerCase()) || 
                                        '{{ strtolower(addslashes($item['category'])) }}'.includes(search.toLowerCase()) || 
                                        '{{ strtolower(addslashes($item['description'])) }}'.includes(search.toLowerCase())"
                                class="hover:bg-gray-50/50 transition-colors {{ $item['is_new'] ? 'bg-emerald-50/20' : '' }}" 
                                wire:key="row-{{ $item['id'] }}">
                                {{-- Status --}}
                                <td class="py-2.5 px-4 text-center">
                                    @if($item['is_new'])
                                        <span class="inline-block px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[8px] font-black uppercase tracking-wider">NEW</span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 text-[8px] font-bold uppercase tracking-wider">EDIT</span>
                                    @endif
                                </td>

                                {{-- Nama Layanan --}}
                                <td class="py-2.5 px-2">
                                    <input type="text" wire:model.defer="services.{{ $index }}.name" data-search="name"
                                           class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-xs font-semibold {{ $errors->has('services.'.$index.'.name') ? 'border-red-500 bg-red-50/30' : '' }}" 
                                           placeholder="Nama Jasa...">
                                    @error('services.'.$index.'.name')
                                        <span class="text-[9px] text-red-500 font-bold block mt-0.5 px-1">{{ $message }}</span>
                                    @enderror
                                </td>

                                {{-- Kategori --}}
                                <td class="py-2.5 px-2">
                                    <input type="text" wire:model.defer="services.{{ $index }}.category" data-search="category"
                                           class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-xs font-semibold {{ $errors->has('services.'.$index.'.category') ? 'border-red-500 bg-red-50/30' : '' }}" 
                                           placeholder="Kategori...">
                                    @error('services.'.$index.'.category')
                                        <span class="text-[9px] text-red-500 font-bold block mt-0.5 px-1">{{ $message }}</span>
                                    @enderror
                                </td>

                                {{-- Harga --}}
                                <td class="py-2.5 px-2">
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-gray-400 font-medium text-[10px]">Rp</span>
                                        <input type="text" wire:model.defer="services.{{ $index }}.price" 
                                               x-data
                                               x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')"
                                               class="w-full pl-7 pr-2.5 py-1.5 border border-gray-200 rounded-lg focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-xs font-bold font-mono text-right {{ $errors->has('services.'.$index.'.price') ? 'border-red-500 bg-red-50/30' : '' }}">
                                    </div>
                                    @error('services.'.$index.'.price')
                                        <span class="text-[9px] text-red-500 font-bold block mt-0.5 px-1">{{ $message }}</span>
                                    @enderror
                                </td>

                                {{-- Durasi --}}
                                <td class="py-2.5 px-2">
                                    <input type="number" wire:model.defer="services.{{ $index }}.duration_minutes" 
                                           class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-xs font-semibold text-center {{ $errors->has('services.'.$index.'.duration_minutes') ? 'border-red-500 bg-red-50/30' : '' }}">
                                    @error('services.'.$index.'.duration_minutes')
                                        <span class="text-[9px] text-red-500 font-bold block mt-0.5 px-1">{{ $message }}</span>
                                    @enderror
                                </td>

                                {{-- HK --}}
                                <td class="py-2.5 px-2">
                                    <input type="number" wire:model.defer="services.{{ $index }}.hk_days" 
                                           class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-xs font-bold text-center {{ $errors->has('services.'.$index.'.hk_days') ? 'border-red-500 bg-red-50/30' : '' }}">
                                    @error('services.'.$index.'.hk_days')
                                        <span class="text-[9px] text-red-500 font-bold block mt-0.5 px-1">{{ $message }}</span>
                                    @enderror
                                </td>

                                {{-- Fast Track --}}
                                <td class="py-2.5 px-2">
                                    <select wire:model.defer="services.{{ $index }}.allow_fast_track" 
                                            class="w-full px-2 py-1.5 border border-gray-200 rounded-lg focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-xs font-bold text-center">
                                        <option value="no">Tidak</option>
                                        <option value="yes">Ya</option>
                                    </select>
                                </td>

                                {{-- Deskripsi --}}
                                <td class="py-2.5 px-2">
                                    <input type="text" wire:model.defer="services.{{ $index }}.description" data-search="description"
                                           class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-xs font-semibold" 
                                           placeholder="Deskripsi singkat...">
                                </td>

                                {{-- Aksi Hapus --}}
                                <td class="py-2.5 px-4 text-center">
                                    <button type="button" wire:click="removeRow({{ $index }})" 
                                            class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-all"
                                            title="Hapus baris ini">
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if(empty($services))
                <div class="py-12 text-center bg-gray-50/50">
                    <p class="text-gray-400 font-medium">Tabel kosong. Silakan klik tombol "Tambah Baris Baru" di atas.</p>
                </div>
            @endif

            <div class="p-6 border-t border-gray-100 flex justify-end gap-3 bg-gray-50/30">
                <a href="{{ route('admin.services.index') }}" 
                   class="px-5 py-3 rounded-2xl bg-white hover:bg-gray-50 text-gray-700 font-bold text-xs uppercase tracking-wider border border-gray-200 shadow-sm transition-all">
                    Batal
                </a>
                <button wire:click="save" 
                        class="px-6 py-3 rounded-2xl bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white font-extrabold text-xs uppercase tracking-widest shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V3"></path></svg>
                    Simpan Semua Perubahan
                </button>
            </div>
        </div>

    </div>
</div>
