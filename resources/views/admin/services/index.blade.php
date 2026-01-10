<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-bold text-xl text-white leading-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                {{ __('Master Data Layanan') }}
            </h2>
            <div class="flex items-center gap-3">
                <div class="px-4 py-2 bg-white/10 rounded-lg backdrop-blur-sm border border-white/20 text-white text-sm">
                    <span class="opacity-75">Total Layanan:</span> 
                    <span class="font-bold ml-1">{{ $services->count() }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ selected: [] }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Toolbar & Search --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-teal-100 dark:border-gray-700 overflow-hidden mb-6 p-5">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    {{-- Left: Search --}}
                    <form action="{{ route('admin.services.index') }}" method="GET" class="w-full md:w-96 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border-gray-200 focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50 text-sm transition-all bg-gray-50 focus:bg-white" 
                               placeholder="Cari layanan...">
                    </form>

                    {{-- Right: Actions --}}
                    <div class="flex items-center gap-3 w-full md:w-auto">
                        {{-- Bulk Delete --}}
                        <form action="{{ route('admin.services.bulk-destroy') }}" method="POST" onsubmit="return confirm('Yakin hapus ' + this.selected.length + ' layanan terpilih?')">
                            @csrf
                            @method('DELETE')
                            <template x-for="id in selected">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                            <button type="submit" 
                                    x-show="selected.length > 0"
                                    x-transition
                                    class="px-4 py-2.5 bg-red-50 text-red-600 rounded-xl hover:bg-red-100 transition-colors flex items-center gap-2 font-medium text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Hapus (<span x-text="selected.length"></span>)
                            </button>
                        </form>

                        <button x-on:click.prevent="$dispatch('open-modal', 'create-service-modal')" 
                                class="w-full md:w-auto px-6 py-2.5 bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white rounded-xl shadow-lg shadow-teal-500/30 transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2 font-medium text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Layanan
                        </button>
                    </div>
                </div>
            </div>

            {{-- Table Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-teal-100 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead>
                            <tr class="bg-gradient-to-r from-teal-50 to-emerald-50 dark:from-gray-700 dark:to-gray-700">
                                <th scope="col" class="px-6 py-4 text-left">
                                    <input type="checkbox" 
                                           class="rounded border-gray-300 text-teal-600 shadow-sm focus:ring-teal-500"
                                           @click="selected = $el.checked ? {{ json_encode($services->pluck('id')) }} : []"
                                           :checked="selected.length === {{ $services->count() }} && {{ $services->count() }} > 0">
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">Layanan</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">Kategori</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">Harga</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">Durasi</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($services as $service)
                            <tr class="hover:bg-teal-50/30 dark:hover:bg-gray-700 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" value="{{ $service->id }}" x-model="selected" class="rounded border-gray-300 text-teal-600 shadow-sm focus:ring-teal-500">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $service->name }}</div>
                                    <div class="text-xs text-gray-500">{{ Str::limit($service->description, 50) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-200">
                                        {{ $service->category }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300 font-medium">
                                    Rp {{ number_format($service->price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ $service->duration_minutes }} Menit
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button x-on:click.prevent="$dispatch('open-modal', 'edit-service-modal-{{ $service->id }}')" 
                                            class="text-teal-600 hover:text-teal-900 dark:hover:text-teal-400 mr-3 transition-colors p-1 hover:bg-teal-50 rounded-lg">
                                        <span class="sr-only">Edit</span>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus layanan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600 transition-colors p-1 hover:bg-red-50 rounded-lg">
                                            <span class="sr-only">Hapus</span>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <x-modal name="edit-service-modal-{{ $service->id }}" :show="false" focusable>
                                <form method="POST" action="{{ route('admin.services.update', $service) }}" class="p-6">
                                    @csrf
                                    @method('PUT')
                                    <div class="flex justify-between items-center p-4 rounded-t-xl bg-gradient-to-r from-teal-500 to-emerald-600 text-white">
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
                                            <x-input-label for="name" :value="__('Nama Layanan')" />
                                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="$service->name" required />
                                        </div>
                                        <div>
                                            <x-input-label for="category" :value="__('Kategori')" />
                                            <x-text-input id="category" class="block mt-1 w-full" type="text" name="category" :value="$service->category" required />
                                        </div>
                                        <div>
                                            <x-input-label for="price" :value="__('Harga (Rp)')" />
                                            <x-text-input id="price" class="block mt-1 w-full" type="number" name="price" :value="$service->price" required />
                                        </div>
                                        <div class="col-span-1 md:col-span-2">
                                            <x-input-label for="duration_minutes" :value="__('Estimasi Durasi (Menit)')" />
                                            <x-text-input id="duration_minutes" class="block mt-1 w-full" type="number" name="duration_minutes" :value="$service->duration_minutes" required />
                                            <p class="text-xs text-gray-500 mt-1">Estimasi waktu pengerjaan untuk satu item.</p>
                                        </div>
                                        <div class="col-span-1 md:col-span-2">
                                            <x-input-label for="description" :value="__('Deskripsi')" />
                                            <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 dark:focus:border-teal-600 focus:ring-teal-500 dark:focus:ring-teal-600 rounded-md shadow-sm">{{ $service->description }}</textarea>
                                        </div>
                                    </div>

                                    <div class="mt-8 flex justify-end gap-3">
                                        <x-secondary-button x-on:click="$dispatch('close')">{{ __('Batal') }}</x-secondary-button>
                                        <x-primary-button class="bg-teal-600 hover:bg-teal-700 focus:bg-teal-700 active:bg-teal-900">{{ __('Simpan Perubahan') }}</x-primary-button>
                                    </div>
                                </form>
                            </x-modal>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-500">
                                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                        <span class="text-lg font-medium">Belum ada layanan</span>
                                        <p class="text-sm mt-1">Silakan tambahkan layanan baru.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700 border-t border-gray-100 dark:border-gray-600 flex justify-between items-center text-sm text-gray-500">
                    <div>
                        Menampilkan <span class="font-bold text-gray-700 dark:text-gray-300">{{ $services->count() }}</span> data layanan.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <x-modal name="create-service-modal" :show="$errors->any() && old('form_type') === 'create_service'" focusable>
        <form method="POST" action="{{ route('admin.services.store') }}">
            @csrf
            <input type="hidden" name="form_type" value="create_service">

            <div class="flex justify-between items-center p-4 rounded-t-xl bg-gradient-to-r from-teal-500 to-emerald-600 text-white">
                <h2 class="text-lg font-bold flex items-center gap-2">
                    <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    Tambah Layanan Baru
                </h2>
                <button type="button" x-on:click="$dispatch('close')" class="text-white/80 hover:text-white transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="p-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-1 md:col-span-2">
                    <x-input-label for="name" :value="__('Nama Layanan')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required />
                </div>
                <div>
                   <x-input-label for="category" :value="__('Kategori')" />
                   <x-text-input id="category" class="block mt-1 w-full" type="text" name="category" placeholder="Contoh: Washing, Repair" required />
               </div>
                <div>
                   <x-input-label for="price" :value="__('Harga (Rp)')" />
                   <x-text-input id="price" class="block mt-1 w-full" type="number" name="price" required />
               </div>
                <div class="col-span-1 md:col-span-2">
                   <x-input-label for="duration_minutes" :value="__('Estimasi Durasi (Menit)')" />
                   <x-text-input id="duration_minutes" class="block mt-1 w-full" type="number" name="duration_minutes" required />
                   <p class="text-xs text-gray-500 mt-1">Estimasi waktu pengerjaan untuk satu item.</p>
               </div>
               <div class="col-span-1 md:col-span-2">
                   <x-input-label for="description" :value="__('Deskripsi')" />
                   <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 dark:focus:border-teal-600 focus:ring-teal-500 dark:focus:ring-teal-600 rounded-md shadow-sm"></textarea>
               </div>
            </div>

            <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-100">
                <x-secondary-button x-on:click="$dispatch('close')">{{ __('Batal') }}</x-secondary-button>
                <x-primary-button class="bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 shadow-md transform hover:-translate-y-0.5 transition-all">{{ __('Simpan Layanan') }}</x-primary-button>
            </div>
            </div>
        </form>
    </x-modal>
</x-app-layout>
