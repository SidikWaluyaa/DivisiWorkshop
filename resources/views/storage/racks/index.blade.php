<x-app-layout>
    <div x-data="{
        showModal: false,
        editMode: false,
        rack: {
            id: null,
            rack_code: '',
            location: '',
            category: '{{ $category ?? 'shoes' }}',
            capacity: '',
            status: 'active',
            notes: ''
        },
        openModal(mode, data = null) {
            this.editMode = mode === 'edit';
            this.showModal = true;
            if (this.editMode && data) {
                this.rack = { ...data }; // Clone data
            } else {
                this.rack = {
                    id: null,
                    rack_code: '',
                    location: '',
                    category: '{{ $category ?? 'shoes' }}',
                    capacity: '',
                    status: 'active',
                    notes: ''
                };
            }
        },
        closeModal() {
            this.showModal = false;
        },
        get formAction() {
            return this.editMode 
                ? '{{ route('storage.racks.index') }}/' + this.rack.id 
                : '{{ route('storage.racks.store') }}';
        }
    }">
        <x-slot name="header">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Manajemen Rak Gudang') }}
                </h2>
                <div class="flex gap-2">
                    <a href="{{ route('storage.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg text-sm font-bold hover:bg-gray-700 transition">
                        &larr; Kembali
                    </a>
                    <a href="{{ route('storage.racks.sync') }}" class="px-4 py-2 bg-amber-600 text-white rounded-lg text-sm font-bold hover:bg-amber-700 transition flex items-center gap-2" title="Sinkronkan jumlah item di rak dengan data aktual">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        Sync Data
                    </a>
                    <a href="{{ route('storage.racks.trash') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg text-sm font-bold hover:bg-gray-600 transition flex items-center gap-2" title="Lihat rak yang sudah dihapus">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Liat Sampah
                    </a>
                    <button @click="openModal('create')" class="px-4 py-2 bg-teal-600 text-white rounded-lg text-sm font-bold hover:bg-teal-700 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Rak
                    </button>
                </div>
            </div>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Category Tabs -->
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <a href="{{ route('storage.racks.index', ['category' => 'shoes']) }}" 
                           class="{{ $category === 'shoes' ? 'border-teal-500 text-teal-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                            <svg class="w-5 h-5 {{ $category === 'shoes' ? 'text-teal-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            Rak Sepatu
                        </a>
                        <a href="{{ route('storage.racks.index', ['category' => 'accessories']) }}" 
                           class="{{ $category === 'accessories' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                            <svg class="w-5 h-5 {{ $category === 'accessories' ? 'text-purple-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            Rak Aksesoris
                        </a>
                        <a href="{{ route('storage.racks.index', ['category' => 'before']) }}" 
                           class="{{ $category === 'before' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                            <svg class="w-5 h-5 {{ $category === 'before' ? 'text-orange-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                            Rak Inbound (Transit)
                        </a>
                    </nav>
                </div>

                <!-- Search -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4">
                    <form method="GET" action="{{ route('storage.racks.index') }}" class="flex gap-2">
                        <input type="hidden" name="category" value="{{ $category }}">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Cari Kode Rak atau Lokasi di {{ $category === 'shoes' ? 'Rak Sepatu' : ($category === 'accessories' ? 'Rak Aksesoris' : 'Rak Inbound') }}..." 
                               class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                        <button type="submit" class="px-4 py-2 bg-teal-500 text-white rounded-md hover:bg-teal-600 font-bold text-sm">Cari</button>
                        @if(request('search'))
                            <a href="{{ route('storage.racks.index', ['category' => $category]) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 font-bold text-sm flex items-center">Reset</a>
                        @endif
                    </form>
                </div>

                <!-- Table -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kode Rak</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Lokasi</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kapasitas</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Isi (Item)</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($racks as $rack)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                                        {{ $rack->rack_code }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $rack->location }}
                                        @if($rack->notes)
                                            <div class="text-xs text-gray-400 italic truncate max-w-xs">{{ $rack->notes }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white text-center">
                                        {{ $rack->capacity }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $rack->current_count >= $rack->capacity ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $rack->current_count }} / {{ $rack->capacity }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        {{-- Manual cast to value if using Enum in DB but string in view, or just handle both --}}
                                        @php $status = is_object($rack->status) ? $rack->status->value : $rack->status; @endphp
                                        @if($status === 'active')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                        @elseif($status === 'maintenance')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Maintenance</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{ ucfirst($status) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-medium">
                                        {{-- Pass JSON encoded rack data --}}
                                        <button @click="openModal('edit', {{ json_encode($rack) }})" class="text-teal-600 hover:text-teal-900 mr-3 font-bold">Edit</button>
                                        @if($rack->current_count == 0)
                                            <form action="{{ route('storage.racks.destroy', $rack->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus rak ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 font-bold">Hapus</button>
                                            </form>
                                        @else
                                            <span class="text-gray-400 cursor-not-allowed" title="Kosongkan rak sebelum menghapus">Hapus</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                            <p class="font-medium">Tidak ada rak 
                                                @if($category === 'shoes') sepatu
                                                @elseif($category === 'accessories') aksesoris
                                                @else inbound (transit)
                                                @endif
                                                ditemukan.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    @if($racks->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                            {{ $racks->appends(['category' => $category, 'search' => request('search')])->links() }}
                        </div>
                    @endif
                </div>

            </div>
        </div>

        <!-- Modal (Alpine Controlled) -->
        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showModal" 
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal()"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showModal"
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    
                    <form method="POST" :action="formAction">
                        @csrf
                        <template x-if="editMode">
                            <input type="hidden" name="_method" value="PUT">
                        </template>
                        
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" x-text="editMode ? 'Edit Rak: ' + rack.rack_code : 'Tambah Rak Baru'"></h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label for="rack_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kode Rak</label>
                                    <input type="text" name="rack_code" id="rack_code" required x-model="rack.rack_code" :readonly="editMode" 
                                           class="mt-1 focus:ring-teal-500 focus:border-teal-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white uppercase" 
                                           :class="{'bg-gray-100 cursor-not-allowed': editMode}"
                                           placeholder="Contoh: A-01">
                                </div>
                                <div>
                                    <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lokasi</label>
                                    <input type="text" name="location" id="location" required x-model="rack.location" class="mt-1 focus:ring-teal-500 focus:border-teal-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Contoh: Lantai 1, Area Depan">
                                </div>
                                <div>
                                    <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori Rak</label>
                                    <select name="category" id="category" x-model="rack.category" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="shoes">Rak Sepatu (Finish)</option>
                                        <option value="accessories">Rak Aksesoris</option>
                                        <option value="before">Rak Inbound (Transit)</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="capacity" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kapasitas (Pasang)</label>
                                    <input type="number" name="capacity" id="capacity" required min="1" x-model="rack.capacity" class="mt-1 focus:ring-teal-500 focus:border-teal-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Contoh: 15">
                                </div>
                                <div x-show="editMode">
                                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                    <select name="status" id="status" x-model="rack.status" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="active">Active</option>
                                        <option value="maintenance">Maintenance</option>
                                        <option value="full">Full</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan (Optional)</label>
                                    <textarea name="notes" id="notes" rows="2" x-model="rack.notes" class="mt-1 focus:ring-teal-500 focus:border-teal-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-teal-600 text-base font-medium text-white hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Simpan
                            </button>
                            <button type="button" @click="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
</x-app-layout>
