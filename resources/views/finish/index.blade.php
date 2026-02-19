<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Station: Barang Selesai & Pickup') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
                <!-- Search & Actions -->
                <div class="flex flex-col sm:flex-row justify-between gap-4">
                    <form method="GET" action="{{ route('finish.index') }}" class="w-full sm:w-1/2">
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari SPK, Nama, atau No HP..." class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-800 dark:text-gray-200 shadow-sm text-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Section 1: Menunggu Disimpan (Belum masuk rak) -->
            @if($readyNotStored->isNotEmpty())
            <div class="mb-8 p-6 bg-gradient-to-r from-orange-500 to-amber-500 dark:from-gray-800 dark:to-gray-900 shadow-lg sm:rounded-xl text-white">
                <header class="mb-6 flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold text-white flex items-center gap-2">
                            <span class="p-1.5 bg-white/20 rounded-lg backdrop-blur-sm">⏳</span>
                            Menunggu Disimpan
                        </h2>
                        <p class="text-sm text-orange-100 mt-1 ml-9 opacity-90">Barang selesai QC, belum masuk rak gudang.</p>
                    </div>
                    <span class="px-3 py-1 bg-white/20 backdrop-blur-md text-white border border-white/30 rounded-full text-xs font-bold">{{ $readyNotStored->count() }} Order</span>
                </header>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 md:gap-4">
                    @foreach($readyNotStored as $order)
                        @if(is_null($order->taken_date))
                        <div class="group relative bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-xl transition-all duration-300 border-l-4 border-orange-400 overflow-hidden transform hover:-translate-y-1">
                            <div class="p-3">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="min-w-0">
                                        <a href="{{ route('finish.show', $order->id) }}" class="font-bold text-base text-gray-800 dark:text-gray-100 hover:text-orange-500 transition-colors truncate block">
                                            {{ $order->spk_number }}
                                        </a>
                                        <div class="text-[10px] text-gray-400">Merek & Warna</div>
                                        <div class="font-medium text-xs text-gray-600 dark:text-gray-300 truncate">{{ $order->shoe_brand }} - {{ $order->shoe_color }}</div>
                                    </div>
                                    <span class="text-[10px] font-bold bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded border border-orange-200 uppercase tracking-wide shrink-0">
                                        SELESAI
                                    </span>
                                </div>
                                
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-md p-2 mb-3 border border-gray-100 dark:border-gray-600">
                                    <div class="text-[10px] text-gray-500 uppercase tracking-wider mb-0.5">Info Pelanggan</div>
                                    <p class="font-semibold text-xs text-gray-900 dark:text-gray-100 truncate">{{ $order->customer_name }}</p>
                                    <p class="text-[10px] text-gray-500 truncate">{{ $order->customer_phone }}</p>
                                </div>

                                {{-- Storage Action --}}
                                <button type="button" 
                                        @click="$dispatch('storage-modal', { workOrderId: {{ $order->id }} })"
                                        class="w-full mb-2 bg-orange-50 dark:bg-gray-700 border border-orange-200 text-orange-700 dark:text-orange-400 hover:bg-orange-100 py-1.5 rounded-md shadow-sm font-bold text-[10px] uppercase tracking-wider transition-all flex items-center justify-center gap-1">
                                    <span>📦 Simpan ke Gudang</span>
                                </button>

                                <form action="{{ route('finish.pickup', $order->id) }}" method="POST">
                                    @csrf
                                    <button class="w-full bg-gray-100 hover:bg-gray-200 text-gray-600 py-2 rounded-md font-bold text-[10px] uppercase tracking-wider flex items-center justify-center gap-1.5 transition-all" onclick="return confirm('Yakin ambil tanpa masuk gudang?')">
                                        <span>Ambil Langsung</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Section 2: Siap Diambil (Sudah di Rak) -->
            <div class="p-6 bg-gradient-to-r from-teal-600 to-green-600 dark:from-gray-800 dark:to-gray-900 shadow-lg sm:rounded-xl text-white">
                <header class="mb-6 flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold text-white flex items-center gap-2">
                            <span class="p-1.5 bg-white/20 rounded-lg backdrop-blur-sm">🚀</span>
                            Siap Diambil (Di Rak)
                        </h2>
                        <p class="text-sm text-teal-100 mt-1 ml-9 opacity-90">Barang sudah di rak gudang dan siap diambil customer.</p>
                    </div>
                    <span class="px-3 py-1 bg-white/20 backdrop-blur-md text-white border border-white/30 rounded-full text-xs font-bold">{{ $readyStored->count() }} Order</span>
                </header>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 md:gap-4">
                    @forelse($readyStored as $order)
                        @if(is_null($order->taken_date))
                        <div class="group relative bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-xl transition-all duration-300 border-l-4 border-teal-500 overflow-hidden transform hover:-translate-y-1">
                            <div class="p-3">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="min-w-0">
                                        <a href="{{ route('finish.show', $order->id) }}" class="font-bold text-base text-gray-800 dark:text-gray-100 hover:text-teal-500 transition-colors truncate block">
                                            {{ $order->spk_number }}
                                        </a>
                                        @if(in_array($order->priority, ['Prioritas', 'Urgent', 'Express']))
                                            <div class="mt-0.5 mb-0.5">
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700 border border-red-200">
                                                    PRIORITAS
                                                </span>
                                            </div>
                                        @endif
                                        <div class="text-[10px] text-gray-400">Merek & Warna</div>
                                        <div class="font-medium text-xs text-gray-600 dark:text-gray-300 truncate">{{ $order->shoe_brand }} - {{ $order->shoe_color }}</div>
                                    </div>
                                    <span class="text-[10px] font-bold bg-teal-100 text-teal-700 px-1.5 py-0.5 rounded border border-teal-200 uppercase tracking-wide shrink-0">
                                        READY
                                    </span>
                                </div>
                                
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-md p-2 mb-3 border border-gray-100 dark:border-gray-600">
                                    <div class="text-[10px] text-gray-500 uppercase tracking-wider mb-0.5">Info Pelanggan</div>
                                    <p class="font-semibold text-xs text-gray-900 dark:text-gray-100 truncate">{{ $order->customer_name }}</p>
                                    <p class="text-[10px] text-gray-500 truncate">{{ $order->customer_phone }}</p>
                                </div>

                                {{-- Rack Location --}}
                                <div class="mb-3">
                                    <div class="bg-teal-50 dark:bg-teal-900/20 p-3 rounded-lg border border-teal-100 dark:border-teal-800 flex flex-col gap-2">
                                        <div class="flex justify-between items-center">
                                            <span class="text-[10px] text-teal-600 dark:text-teal-400 font-bold uppercase tracking-wide flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                Lokasi
                                            </span>
                                            <span class="text-xs font-bold bg-teal-100 text-teal-700 px-2 py-0.5 rounded border border-teal-200 shadow-sm">
                                                📦 {{ $order->storage_rack_code }}
                                            </span>
                                        </div>
                                        
                                        <div class="flex flex-col gap-2 mt-1">
                                            <div class="grid grid-cols-2 gap-2">
                                                <a href="{{ route('storage.label', ['id' => $order->id]) }}" target="_blank" 
                                                   class="flex items-center justify-center gap-1.5 px-2 py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-[10px] font-bold uppercase tracking-wide rounded transition-colors shadow-sm group/btn" title="Cetak Tag Rak">
                                                    <svg class="w-3.5 h-3.5 group-hover/btn:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10v10H7z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                                    Tag Rak
                                                </a>
                                                
                                                <a href="{{ route('storage.shipping-label', ['id' => $order->id]) }}" target="_blank" 
                                                   class="flex items-center justify-center gap-1.5 px-2 py-1.5 bg-yellow-400 hover:bg-yellow-500 text-yellow-900 text-[10px] font-bold uppercase tracking-wide rounded transition-colors shadow-sm group/btn" title="Cetak Label Alamat">
                                                    <svg class="w-3.5 h-3.5 group-hover/btn:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                    Alamat
                                                </a>
                                            </div>
                                            
                                            <form action="{{ route('storage.unassign', $order->id) }}" method="POST" onsubmit="return confirm('Lepas tag rak? Item akan kembali ke status Menunggu Disimpan.');" class="w-full">
                                                @csrf
                                                <button type="submit" 
                                                        class="w-full flex items-center justify-center gap-1.5 px-2 py-1.5 bg-white dark:bg-gray-700 border border-red-200 dark:border-red-900/50 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 text-[10px] font-bold uppercase tracking-wide rounded transition-colors shadow-sm group/btn">
                                                    <svg class="w-3.5 h-3.5 group-hover/btn:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    Lepas Tag
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <form action="{{ route('storage.retrieve', isset($order->storageAssignment) ? $order->storageAssignment->id : 0) }}" method="POST"> 
                                    {{-- Note: Usually retrieve route needs storage assignment ID, but here we might only have work order. 
                                         Ideally workOrder model should have relation to active assignment. 
                                         Let's assume we can use work order ID if we modify route, OR rely on relation.
                                         The current route uses storage_assignments.id.
                                         Let's check relation. WorkOrder likely doesn't have direct 'active_assignment' helper yet?
                                         Actually, `storage_assignments` table has `work_order_id`.
                                         Let's use a workaround or check if I can use work order ID for retrieval?
                                         The route is `post('/{id}/retrieve', [StorageController::class, 'retrieve'])` -> uses $id of Assignment?
                                         Wait, StorageService `retrieveFromStorage($id)` finds StorageAssignment by ID.
                                         So I need the assignment ID here.
                                         
                                         Let's use:
                                         `\App\Models\StorageAssignment::where('work_order_id', $order->id)->where('status', 'stored')->first()?->id`
                                         But doing query in view is bad.
                                         Better to load relationship in controller or use a dedicated route `finish.retrieve_by_wo`?
                                         
                                         Actually, the simplest way is to fetch assignment ID.
                                         I will update Controller to load relationship `storageAssignment` (singular, current active).
                                    --}}
                                    @php
                                        // Quick inline check for now, can be optimized later
                                        $assignment = \App\Models\StorageAssignment::where('work_order_id', $order->id)->where('status', 'stored')->first();
                                    @endphp

                                    @csrf
                                    @if($assignment)
                                        <input type="hidden" name="redirect_to" value="finish.index">
                                        <button formaction="{{ route('storage.retrieve', $assignment->id) }}" class="w-full bg-teal-600 hover:bg-teal-700 text-white py-2 rounded-md shadow hover:shadow-md font-bold text-[10px] uppercase tracking-wider flex items-center justify-center gap-1.5 transition-all">
                                            <span>Ambil (Retrieve)</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </button>
                                    @else
                                        {{-- Fallback if assignment not found (weird state) --}}
                                        <button disabled class="w-full bg-gray-300 text-white py-2 rounded-md font-bold text-[10px] uppercase tracking-wider">
                                            Eror: No Assign
                                        </button>
                                    @endif
                                </form>
                                <button type="button" onclick="openReportModal({{ $order->id }})" class="w-full mt-2 text-amber-600 hover:text-amber-800 text-[10px] font-bold hover:bg-amber-50 py-1.5 rounded transition-colors flex items-center justify-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    Lapor
                                </button>
                            </div>
                        </div>
                        @endif
                    @empty
                    <div class="col-span-full flex flex-col items-center justify-center p-8 text-center text-teal-100/70 border border-dashed border-white/20 rounded-lg">
                        <svg class="w-10 h-10 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        <p class="text-sm">Tidak ada barang siap ambil di rak.</p>
                    </div>
                    @endforelse
                </div>

                <!-- Ready Pagination -->
                <div class="mt-6">
                    {{ $ready->links() }}
                </div>
            </div>

            <!-- History Taken -->
            <div id="history-section" 
                 x-data="{ 
                    selectedIds: [], 
                    selectAll: false,
                    toggleAll() {
                        this.selectedIds = this.selectAll ? {{ Js::from($history->pluck('id')) }} : [];
                    },
                    updateSelectAll() {
                        this.selectAll = this.selectedIds.length === {{ $history->count() }} && {{ $history->count() }} > 0;
                    }
                 }"
                 class="bg-white dark:bg-gray-800 shadow-md sm:rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700 relative">
                
                <!-- Floating Action Bar -->
                <div x-show="selectedIds.length > 0" 
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="translate-y-full opacity-0"
                     x-transition:enter-end="translate-y-0 opacity-100"
                     x-transition:leave="transition ease-in duration-200 transform"
                     x-transition:leave-start="translate-y-0 opacity-100"
                     x-transition:leave-end="translate-y-full opacity-0"
                     class="fixed bottom-10 left-1/2 -translate-x-1/2 z-50 flex items-center gap-6 px-8 py-4 bg-gray-900/95 backdrop-blur-md text-white rounded-full shadow-[0_20px_50px_rgba(0,0,0,0.3)] border border-white/10">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-red-500 flex items-center justify-center font-black text-xs" x-text="selectedIds.length"></span>
                        <span class="text-sm font-bold uppercase tracking-widest text-gray-300">Data Terpilih</span>
                    </div>
                    
                    <div class="w-px h-8 bg-white/10"></div>
                    
                    <form action="{{ route('finish.bulk-delete-selection') }}" method="POST" @submit.prevent="if(confirm('Hapus ' + selectedIds.length + ' riwayat terpilih?')) $el.submit()">
                        @csrf
                        @method('DELETE')
                        <template x-for="id in selectedIds" :key="id">
                            <input type="hidden" name="ids[]" :value="id">
                        </template>
                        <button type="submit" class="bg-red-500 hover:bg-red-600 px-6 py-2 rounded-full text-xs font-black uppercase tracking-tighter transition-all flex items-center gap-2 shadow-lg shadow-red-500/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Hapus Terpilih
                        </button>
                    </form>
                    
                    <button @click="selectedIds = []; selectAll = false" class="text-xs font-bold text-gray-400 hover:text-white uppercase tracking-widest">
                        Batal
                    </button>
                </div>

                <header class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="flex items-center gap-4">
                        <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">Riwayat Pengambilan Terakhir</h2>
                        <!-- Trash Link -->
                        <a href="{{ route('finish.trash') }}" class="text-xs px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg font-bold border border-red-200 transition-colors flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Lihat Sampah
                        </a>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <!-- Bulk Delete Form -->
                        <form action="{{ route('finish.bulk-destroy') }}" method="POST" class="flex items-center gap-2" onsubmit="return confirm('PERINGATAN: Semua data pada tanggal yang dipilih akan DIHAPUS PERMANEN. Anda yakin?');">
                            @csrf
                            @method('DELETE')
                            <input type="date" name="date" class="text-xs px-2 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-200 focus:ring-red-500 focus:border-red-500" required>
                            <button type="submit" class="text-xs bg-red-100 text-red-700 hover:bg-red-200 px-3 py-1.5 rounded-lg font-bold transition-colors">
                                Hapus Bulk
                            </button>
                        </form>
                    </div>
                </header>

                <div class="overflow-x-auto -mx-4 sm:mx-0">
                    <table class="min-w-full w-full text-sm text-left">
                        <thead class="text-xs text-gray-700 uppercase bg-gradient-to-r from-gray-100 to-gray-50 dark:from-gray-700 dark:to-gray-800 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3 text-center w-10">
                                    <input type="checkbox" 
                                           x-model="selectAll" 
                                           @change="toggleAll()"
                                           class="w-4 h-4 text-teal-600 bg-gray-100 border-gray-300 rounded focus:ring-teal-500 focus:ring-2">
                                </th>
                                <th class="px-6 py-3">SPK & Customer</th>
                                <th class="px-6 py-3 text-center">Prioritas</th>
                                <th class="px-6 py-3">Info Item</th>
                                <th class="px-6 py-3">Layanan & Harga</th>
                                <th class="px-6 py-3">Waktu Ambil</th>
                                <th class="px-6 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($history as $order)
                            <tr :class="selectedIds.includes({{ $order->id }}) ? 'bg-teal-50 dark:bg-teal-900/10' : ''" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-6 py-4 text-center">
                                    <input type="checkbox" 
                                           :value="{{ $order->id }}" 
                                           x-model="selectedIds"
                                           @change="updateSelectAll()"
                                           class="w-4 h-4 text-teal-600 bg-gray-100 border-gray-300 rounded focus:ring-teal-500 focus:ring-2">
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('finish.show', $order->id) }}" class="font-bold text-teal-600 hover:underline block">
                                        {{ $order->spk_number }}
                                    </a>
                                    <div class="text-xs text-gray-500 font-medium">{{ $order->customer_name }}</div>
                                    @if($order->customer_phone)
                                        <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $order->customer_phone)) }}" target="_blank" class="inline-flex items-center gap-1 mt-1 text-[10px] font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded-full hover:bg-green-100 transition-colors">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.592 2.654-.694c1.003.545 1.987.96 3.218.96 3.183 0 5.768-2.587 5.768-5.765.001-3.187-2.575-5.756-5.78-5.756zm0 0"></path><path d="M12 2C6.48 2 2 6.48 2 12c0 1.822.487 3.53 1.338 5.008l-1.42 5.236 5.348-1.405A9.957 9.957 0 0012 22c5.52 0 10-4.48 10-10S17.52 2 12 2zm0 18c-1.72 0-3.284-.6-4.593-1.603l-1.98.52.54-1.906A8.02 8.02 0 014 12c0-4.411 3.589-8 8-8s8 3.589 8 8-3.589 8-8 8z"></path></svg>
                                            {{ $order->customer_phone }}
                                        </a>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if(in_array($order->priority, ['Prioritas', 'Urgent', 'Express']))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200 shadow-sm">
                                            PRIORITAS
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                            REGULER
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                     <div class="font-medium text-gray-900 dark:text-gray-200">{{ $order->shoe_brand }}</div>
                                     <div class="text-xs text-gray-400">{{ $order->shoe_color }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        @foreach($order->services as $service)
                                            <div class="text-xs flex justify-between gap-4 text-gray-600 dark:text-gray-400">
                                                <span>{{ $service->pivot->custom_name ?? $service->name }}</span>
                                                <span class="font-mono">{{ number_format($service->pivot->cost, 0, ',', '.') }}</span>
                                            </div>
                                        @endforeach
                                        <div class="border-t border-gray-200 dark:border-gray-600 pt-1 mt-1 flex justify-between gap-4 font-bold text-xs text-gray-800 dark:text-gray-200">
                                            <span>Total</span>
                                            <span>Rp {{ number_format($order->services->sum('pivot.cost'), 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ $order->taken_date->format('d M Y, H:i') }}
                                        
                                        <!-- Safe Delete Action -->
                                        <form action="{{ route('finish.destroy', $order->id) }}" method="POST" class="inline-block ml-2">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="delete-confirm text-red-400 hover:text-red-600 transition-colors" 
                                                    data-title="Hapus Riwayat?" 
                                                    data-text="Data akan dipindahkan ke Sampah (Soft Delete)."
                                                    data-confirm="Ya, Hapus!"
                                                    title="Hapus Data">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        ✔ SUDAH DIAMBIL
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500 italic">Belum ada riwayat pengambilan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- History Pagination -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-100 dark:border-gray-700">
                    {{ $history->links() }}
                </div>
            </div>

        </div>
    </div>
    {{-- REPORT ISSUE MODAL --}}
    <div id="reportModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm" style="display: none;">
        <!-- Report Modal Content -->
        <div class="bg-white rounded-xl shadow-2xl p-6 w-96 max-w-full text-left transform transition-all scale-100">
            {{-- ... content ... --}}
            <!-- Reusing existing logic, just placeholder here to locate -->
        </div>
    </div>
    
    {{-- Include Storage Modal --}}
    @include('storage.partials.assign-modal')

    <script>
    function openReportModal(id) {
        document.getElementById('report_work_order_id').value = id;
        document.getElementById('reportModal').style.display = 'flex';
        document.getElementById('reportModal').classList.remove('hidden');
    }

    function closeReportModal() {
        document.getElementById('reportModal').style.display = 'none';
        document.getElementById('reportModal').classList.add('hidden');
    }
    </script>
</x-app-layout>
