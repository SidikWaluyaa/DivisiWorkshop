<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Kelola Rak Manual') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('storage.manual.racks.sync') }}" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Sync Count
                </a>
                <button onclick="document.getElementById('createRackModal').showModal()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Rak
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Search -->
            <div class="mb-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                <form action="{{ route('storage.manual.racks.index') }}" method="GET" class="flex gap-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Kode Rak atau Lokasi..." class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 focus:ring-red-500 shadow-sm">
                    <button type="submit" class="px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        Cari
                    </button>
                    @if(request('search'))
                        <a href="{{ route('storage.manual.racks.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-gray-300 flex items-center">Reset</a>
                    @endif
                </form>
            </div>

            <!-- Racks Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($racks as $rack)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 {{ $rack->isAvailable() ? 'border-green-500' : 'border-red-500' }} relative group">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $rack->rack_code }}</h3>
                                    <p class="text-sm text-gray-500">{{ $rack->location }}</p>
                                </div>
                                <span class="px-2 py-1 rounded text-xs font-semibold {{ $rack->status == \App\Enums\RackStatus::ACTIVE ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $rack->status instanceof \App\Enums\RackStatus ? $rack->status->label() : ucfirst($rack->status) }}
                                </span>
                            </div>

                            <!-- Progress Bar -->
                            <div class="mb-2">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600 dark:text-gray-400">Terisi: {{ $rack->current_count }} / {{ $rack->capacity }}</span>
                                    <span class="font-bold {{ $rack->getUtilizationPercentage() > 90 ? 'text-red-600' : 'text-green-600' }}">
                                        {{ number_format($rack->getUtilizationPercentage(), 0) }}%
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                    <div class="h-2.5 rounded-full {{ $rack->getUtilizationPercentage() > 90 ? 'bg-red-600' : ($rack->getUtilizationPercentage() > 70 ? 'bg-yellow-400' : 'bg-green-600') }}" 
                                         style="width: {{ $rack->getUtilizationPercentage() }}%"></div>
                                </div>
                            </div>
                            
                            @if($rack->notes)
                                <p class="text-xs text-gray-400 italic mb-4">{{ $rack->notes }}</p>
                            @endif

                            <div class="flex justify-end gap-3 mt-4">
                                <a href="{{ route('storage.manual.racks.print-pdf', $rack->id) }}" target="_blank" class="text-green-600 hover:text-green-900 text-sm font-medium flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    Print
                                </a>
                                <button onclick="editRack('{{ $rack->id }}', '{{ $rack->rack_code }}', '{{ $rack->location }}', '{{ $rack->capacity }}', '{{ $rack->status instanceof \App\Enums\RackStatus ? $rack->status->value : $rack->status }}', '{{ $rack->category instanceof \BackedEnum ? $rack->category->value : $rack->category }}', '{{ $rack->notes }}')" 
                                        class="text-blue-600 hover:text-blue-900 text-sm font-medium">Edit</button>
                                
                                @if($rack->current_count == 0)
                                    <form action="{{ route('storage.manual.racks.destroy', $rack->id) }}" method="POST" onsubmit="return confirm('Hapus rak ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">Hapus</button>
                                    </form>
                                @else
                                    <span class="text-gray-400 text-sm cursor-not-allowed" title="Kosongkan rak terlebih dahulu">Hapus</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 text-gray-500">
                        Belum ada rak manual. Silakan tambah rak baru.
                    </div>
                @endforelse
            </div>
            
            <div class="mt-6">
                {{ $racks->links() }}
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <dialog id="createRackModal" class="modal rounded-lg shadow-xl p-0 w-full max-w-lg backdrop:bg-gray-900/50">
        <div class="bg-white dark:bg-gray-800 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Tambah Rak Manual Baru</h3>
                <button onclick="document.getElementById('createRackModal').close()" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <form action="{{ route('storage.manual.racks.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <x-input-label for="rack_code" :value="__('Kode Rak')" />
                        <x-text-input id="rack_code" class="block mt-1 w-full" type="text" name="rack_code" placeholder="Contoh: M-21" required autofocus />
                        <p class="text-xs text-gray-500 mt-1">Saran: Gunakan awalan 'M-' untuk membedakan.</p>
                    </div>
                    <div>
                        <x-input-label for="location" :value="__('Lokasi')" />
                        <x-text-input id="location" class="block mt-1 w-full" type="text" name="location" placeholder="Gudang Manual - Area X" required />
                    </div>
                    <div>
                        <x-input-label for="category" :value="__('Kategori Rak')" />
                        <select id="category" name="category" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 focus:ring-red-500 shadow-sm">
                            <option value="manual">Manual (Umum)</option>
                            <option value="manual_tl">Manual - Tagih Lunas (TL)</option>
                            <option value="manual_tn">Manual - Tagih Nanti (TN)</option>
                            <option value="manual_l">Manual - Lunas (L)</option>
                        </select>
                         <p class="text-xs text-gray-500 mt-1">Pilih kategori keamanan finansial.</p>
                    </div>
                    <div>
                        <x-input-label for="capacity" :value="__('Kapasitas')" />
                        <x-text-input id="capacity" class="block mt-1 w-full" type="number" name="capacity" value="30" min="1" required />
                    </div>
                    <div>
                        <x-input-label for="notes" :value="__('Catatan (Opsional)')" />
                        <textarea name="notes" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 focus:ring-red-500 shadow-sm" rows="2"></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('createRackModal').close()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Simpan</button>
                </div>
            </form>
        </div>
    </dialog>

    <!-- Edit Modal -->
    <dialog id="editRackModal" class="modal rounded-lg shadow-xl p-0 w-full max-w-lg backdrop:bg-gray-900/50">
        <div class="bg-white dark:bg-gray-800 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Edit Rak: <span id="editRackCodeDisplay"></span></h3>
                <button onclick="document.getElementById('editRackModal').close()" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <form id="editRackForm" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <x-input-label for="edit_location" :value="__('Lokasi')" />
                        <x-text-input id="edit_location" class="block mt-1 w-full" type="text" name="location" required />
                    </div>
                    <div>
                        <x-input-label for="edit_category" :value="__('Kategori Rak')" />
                        <select id="edit_category" name="category" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 focus:ring-red-500 shadow-sm">
                            <option value="manual">Manual (Umum)</option>
                            <option value="manual_tl">Manual - Tagih Lunas (TL)</option>
                            <option value="manual_tn">Manual - Tagih Nanti (TN)</option>
                            <option value="manual_l">Manual - Lunas (L)</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="edit_capacity" :value="__('Kapasitas')" />
                        <x-text-input id="edit_capacity" class="block mt-1 w-full" type="number" name="capacity" min="1" required />
                    </div>
                    <div>
                        <x-input-label for="edit_status" :value="__('Status')" />
                        <select id="edit_status" name="status" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 focus:ring-red-500 shadow-sm">
                            <option value="active">Active</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="full">Full</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="edit_notes" :value="__('Catatan')" />
                        <textarea id="edit_notes" name="notes" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 focus:ring-red-500 shadow-sm" rows="2"></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('editRackModal').close()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Update</button>
                </div>
            </form>
        </div>
    </dialog>

    <script>
        function editRack(id, code, location, capacity, status, category, notes) {
            document.getElementById('editRackCodeDisplay').innerText = code;
            document.getElementById('edit_location').value = location;
            document.getElementById('edit_capacity').value = capacity;
            document.getElementById('edit_status').value = status;
            document.getElementById('edit_category').value = category;
            document.getElementById('edit_notes').value = notes;
            
            let form = document.getElementById('editRackForm');
            form.action = "{{ url('warehouse/manual/racks') }}/" + id;
            
            document.getElementById('editRackModal').showModal();
        }
    </script>
</x-app-layout>
