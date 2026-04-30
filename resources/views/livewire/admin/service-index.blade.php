<div>
    {{-- Filter Section --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-teal-100 dark:border-gray-700 overflow-hidden mb-6 p-5">
        <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-4 items-end">
            {{-- Search --}}
            <div class="col-span-1 md:col-span-2">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Cari Layanan</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" 
                           wire:model.live.debounce.300ms="search"
                           class="w-full pl-10 pr-4 py-2 rounded-xl border-gray-200 focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50 text-sm transition-all bg-gray-50 focus:bg-white" 
                           placeholder="Nama atau deskripsi...">
                </div>
            </div>

            {{-- Category --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Kategori</label>
                <select wire:model.live="category" 
                        class="w-full py-2 rounded-xl border-gray-200 focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50 text-sm bg-gray-50">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Price Range --}}
            <div class="col-span-1 md:col-span-2 lg:col-span-1">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Harga (Min - Max)</label>
                <div class="flex items-center gap-2">
                    <input type="number" wire:model.live.debounce.500ms="minPrice" placeholder="Min" class="w-full py-2 rounded-xl border-gray-200 text-sm bg-gray-50">
                    <span class="text-gray-400">-</span>
                    <input type="number" wire:model.live.debounce.500ms="maxPrice" placeholder="Max" class="w-full py-2 rounded-xl border-gray-200 text-sm bg-gray-50">
                </div>
            </div>

            {{-- Sorting --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Urutkan</label>
                <select wire:model.live="sortBy" 
                        class="w-full py-2 rounded-xl border-gray-200 focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50 text-sm bg-gray-50">
                    <option value="latest">Terbaru</option>
                    <option value="price_asc">Harga Terendah</option>
                    <option value="price_desc">Harga Tertinggi</option>
                    <option value="name_asc">Nama A-Z</option>
                    <option value="name_desc">Nama Z-A</option>
                </select>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2">
                <button wire:click="resetFilters" 
                        class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition-colors text-sm font-medium flex-1">
                    Reset
                </button>
            </div>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center text-xs text-gray-400 font-medium">
            <div>
                Menampilkan <span class="text-teal-600">{{ $services->total() }}</span> layanan dari total semua data.
            </div>
            @if($search || $category || $minPrice || $maxPrice)
                <div class="flex items-center gap-1 text-teal-600 bg-teal-50 px-2 py-1 rounded-lg">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"></path></svg>
                    Filter Aktif
                </div>
            @endif
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div class="flex items-center gap-3">
            @if(count($selected) > 0)
                <button wire:click="deleteSelected" 
                        wire:confirm="Yakin ingin menghapus {{ count($selected) }} layanan terpilih?"
                        class="px-4 py-2 bg-red-50 text-red-600 rounded-xl hover:bg-red-100 transition-colors flex items-center gap-2 font-medium text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus ({{ count($selected) }})
                </button>
            @endif
        </div>
        
        <div class="flex items-center gap-3">
            <select wire:model.live="perPage" class="py-2 rounded-xl border-gray-200 text-sm bg-white">
                <option value="10">10 per hal</option>
                <option value="25">25 per hal</option>
                <option value="50">50 per hal</option>
                <option value="100">100 per hal</option>
            </select>
            
            <div class="flex items-center gap-2">
                 <a href="{{ route('admin.services.export-excel') }}" target="_blank" class="px-3 py-2.5 bg-green-50 text-green-600 rounded-xl hover:bg-green-100 transition-colors flex items-center gap-2 font-medium text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Excel
                </a>
                <a href="{{ route('admin.services.template') }}" class="px-3 py-2.5 bg-yellow-50 text-yellow-600 rounded-xl hover:bg-yellow-100 transition-colors flex items-center gap-2 font-medium text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Template
                </a>
                <button x-on:click="$dispatch('open-modal', 'import-service-modal')" class="px-3 py-2.5 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-100 transition-colors flex items-center gap-2 font-medium text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Import
                </button>
            </div>
            
            <button x-on:click.prevent="$dispatch('open-modal', 'create-service-modal')" 
                    class="px-6 py-2 bg-gradient-to-r from-teal-500 to-emerald-600 text-white rounded-xl shadow-lg transition-all hover:scale-105 flex items-center gap-2 font-medium text-sm whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Layanan
            </button>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-teal-100 dark:border-gray-700 overflow-hidden">
        {{-- Desktop View --}}
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                <thead>
                    <tr class="bg-gradient-to-r from-teal-50 to-emerald-50 dark:from-gray-700 dark:to-gray-700">
                        <th class="px-6 py-4 text-left w-10">
                            <input type="checkbox" 
                                   wire:model.live="selectedAll"
                                   class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-teal-800 uppercase tracking-wider">Layanan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-teal-800 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-teal-800 uppercase tracking-wider">Harga</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-teal-800 uppercase tracking-wider">Durasi</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-teal-800 uppercase tracking-wider">HK</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-teal-800 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($services as $service)
                        <tr wire:key="service-{{ $service->id }}" class="hover:bg-teal-50/30 transition-colors group">
                            <td class="px-6 py-4">
                                <input type="checkbox" value="{{ $service->id }}" wire:model.live="selected" class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $service->name }}</div>
                                <div class="text-xs text-gray-500">{{ Str::limit($service->description, 60) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-teal-100 text-teal-800">
                                    {{ $service->category }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-700">
                                Rp {{ number_format($service->price, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ $service->duration_minutes }} Mnt
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-emerald-600">
                                {{ $service->hk_days ?? 0 }} HK
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button x-on:click.prevent="$dispatch('open-modal', 'edit-service-modal-{{ $service->id }}')" 
                                        class="text-teal-600 hover:text-teal-900 transition-colors p-2 hover:bg-teal-50 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                @if(!in_array(strtolower($service->name), ['custom', 'custom service']))
                                    <button wire:click="deleteSelected([{{ $service->id }}])" 
                                            wire:confirm="Hapus layanan ini?"
                                            class="text-red-400 hover:text-red-600 transition-colors p-2 hover:bg-red-50 rounded-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </button>
                                @endif

                                <!-- Edit Modal -->
                                <x-modal name="edit-service-modal-{{ $service->id }}" :show="false" focusable>
                                    <form method="POST" action="{{ route('admin.services.update', $service) }}" class="p-6 text-left">
                                        @csrf
                                        @method('PUT')
                                        <div class="flex justify-between items-center p-4 rounded-t-xl bg-gradient-to-r from-teal-500 to-emerald-600 text-white mb-6">
                                            <h2 class="text-lg font-bold flex items-center gap-2">
                                                <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </div>
                                                Edit Layanan
                                            </h2>
                                            <button type="button" x-on:click="$dispatch('close')" class="text-white/80 hover:text-white transition-colors">
                                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                            </button>
                                        </div>
                                        <input type="hidden" name="form_type" value="edit_service_{{ $service->id }}">
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div class="col-span-1 md:col-span-2">
                                                <x-input-label for="name_{{ $service->id }}" :value="__('Nama Layanan')" />
                                                <x-text-input id="name_{{ $service->id }}" class="block mt-1 w-full" type="text" name="name" :value="$service->name" required />
                                            </div>
                                            <div>
                                                <x-input-label for="category_{{ $service->id }}" :value="__('Kategori')" />
                                                <x-text-input id="category_{{ $service->id }}" class="block mt-1 w-full" type="text" name="category" :value="$service->category" required />
                                            </div>
                                            <div>
                                                <x-input-label for="price_{{ $service->id }}" :value="__('Harga (Rp)')" />
                                                <x-text-input id="price_{{ $service->id }}" class="block mt-1 w-full" type="number" name="price" :value="$service->price" required />
                                            </div>
                                            <div class="col-span-1">
                                                <x-input-label for="duration_minutes_{{ $service->id }}" :value="__('Durasi (Menit)')" />
                                                <x-text-input id="duration_minutes_{{ $service->id }}" class="block mt-1 w-full" type="number" name="duration_minutes" :value="$service->duration_minutes" required />
                                            </div>
                                            <div class="col-span-1">
                                                <x-input-label for="hk_days_{{ $service->id }}" :value="__('Hari Kerja (HK)')" />
                                                <x-text-input id="hk_days_{{ $service->id }}" class="block mt-1 w-full" type="number" name="hk_days" :value="$service->hk_days" required />
                                            </div>
                                            <div class="col-span-1 md:col-span-2">
                                                <x-input-label for="description_{{ $service->id }}" :value="__('Deskripsi')" />
                                                <textarea id="description_{{ $service->id }}" name="description" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 dark:focus:border-teal-600 focus:ring-teal-500 dark:focus:ring-teal-600 rounded-md shadow-sm">{{ $service->description }}</textarea>
                                            </div>
                                        </div>

                                        <div class="mt-8 flex justify-end gap-3 pt-6 border-t">
                                            <x-secondary-button x-on:click="$dispatch('close')">{{ __('Batal') }}</x-secondary-button>
                                            <x-primary-button class="bg-teal-600 hover:bg-teal-700">{{ __('Simpan Perubahan') }}</x-primary-button>
                                        </div>
                                    </form>
                                </x-modal>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 italic">
                                Tidak ada layanan yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile View --}}
        <div class="block lg:hidden divide-y divide-gray-100">
            @forelse($services as $service)
                <div class="p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" value="{{ $service->id }}" wire:model.live="selected" class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                            <div>
                                <div class="font-bold text-gray-900">{{ $service->name }}</div>
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-teal-50 text-teal-700 border border-teal-100">{{ $service->category }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold text-teal-600">Rp {{ number_format($service->price, 0, ',', '.') }}</div>
                            <div class="text-[10px] text-gray-500">{{ $service->duration_minutes }} mnt</div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-3 pl-8">
                        <button x-on:click.prevent="$dispatch('open-modal', 'edit-service-modal-mobile-{{ $service->id }}')" 
                                class="flex-1 bg-teal-50 text-teal-700 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-teal-100">
                            Edit
                        </button>
                        <button wire:click="deleteSelected([{{ $service->id }}])" 
                                wire:confirm="Hapus layanan ini?"
                                class="flex-1 bg-red-50 text-red-700 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-red-100">
                            Hapus
                        </button>
                    </div>

                    <!-- Edit Modal Mobile -->
                    <x-modal name="edit-service-modal-mobile-{{ $service->id }}" :show="false" focusable>
                        <form method="POST" action="{{ route('admin.services.update', $service) }}" class="p-6 text-left">
                            @csrf
                            @method('PUT')
                            <div class="flex justify-between items-center p-4 rounded-t-xl bg-gradient-to-r from-teal-500 to-emerald-600 text-white mb-6">
                                <h2 class="text-lg font-bold flex items-center gap-2">
                                    Edit Layanan
                                </h2>
                                <button type="button" x-on:click="$dispatch('close')" class="text-white/80 hover:text-white transition-colors">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                            
                            <div class="space-y-4">
                                <div>
                                    <x-input-label :value="__('Nama Layanan')" />
                                    <x-text-input class="block mt-1 w-full" type="text" name="name" :value="$service->name" required />
                                </div>
                                <div>
                                    <x-input-label :value="__('Kategori')" />
                                    <x-text-input class="block mt-1 w-full" type="text" name="category" :value="$service->category" required />
                                </div>
                                <div>
                                    <x-input-label :value="__('Harga')" />
                                    <x-text-input class="block mt-1 w-full" type="number" name="price" :value="$service->price" required />
                                </div>
                                <div>
                                    <x-input-label :value="__('Hari Kerja (HK)')" />
                                    <x-text-input class="block mt-1 w-full" type="number" name="hk_days" :value="$service->hk_days" required />
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end gap-3">
                                <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                                <x-primary-button class="bg-teal-600">Simpan</x-primary-button>
                            </div>
                        </form>
                    </x-modal>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500 italic">Belum ada data.</div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ $services->links() }}
        </div>
    </div>
</div>
