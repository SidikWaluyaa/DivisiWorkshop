<div class="py-12" x-data="{ 
    confirmArchive() {
        Swal.fire({
            title: 'Konfirmasi Pengarsipan',
            text: 'Data SPK terpilih akan diarsipkan ke status HISTORY. Tindakan ini akan melepaskan alokasi penyimpanan rak secara otomatis. Lanjutkan?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Arsipkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $wire.archiveSelected();
            }
        })
    },
    confirmRestore() {
        Swal.fire({
            title: 'Konfirmasi Pemulihan',
            text: 'Data SPK terpilih akan dipulihkan dari status HISTORY ke status semula. Lanjutkan?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Pulihkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $wire.restoreSelected();
            }
        })
    }
}">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        {{-- Breadcrumb --}}
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-white">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                        Dashboard
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 text-sm font-bold text-gray-500 md:ml-2 dark:text-gray-400">Archive Hub</span>
                    </div>
                </li>
            </ol>
        </nav>

        {{-- Tabs --}}
        <div class="flex gap-2 overflow-x-auto pb-2">
            <button wire:click="$set('activeTab', 'active')" 
               class="px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all {{ $activeTab === 'active' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'bg-white text-gray-400 hover:text-gray-600 border border-gray-100' }}">
                SPK Aktif & Riwayat Kerja
            </button>
            <button wire:click="$set('activeTab', 'archived')" 
               class="px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all {{ $activeTab === 'archived' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'bg-white text-gray-400 hover:text-gray-600 border border-gray-100' }}">
                Arsip History (Archived)
            </button>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100 dark:border-gray-700">
            {{-- Header --}}
            <div class="p-8 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-br from-gray-50 to-white dark:from-gray-800 dark:to-gray-900">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div>
                        <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">
                            Archive Hub <span class="text-indigo-600">Work Orders</span>
                        </h1>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 font-medium italic">
                            @if($activeTab === 'active')
                                Menampilkan semua data SPK aktif yang siap diarsipkan ke status HISTORY (Kecuali SPK Pending).
                            @else
                                Menampilkan data SPK yang diarsipkan secara manual. Algoritme dashboard dan workshop mengabaikan data ini.
                            @endif
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3 w-full md:w-auto">
                        <div class="relative flex-1 md:flex-initial">
                            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari SPK, Customer, Merek..." 
                                   class="pl-10 pr-4 py-2.5 bg-white dark:bg-gray-700 border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 w-full md:w-64 shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                        </div>
                        @if($activeTab === 'active')
                            <select wire:model.live="statusFilter" class="py-2.5 bg-white dark:bg-gray-700 border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                                <option value="all">Semua Status</option>
                                @foreach($allStatuses as $val => $lbl)
                                    <option value="{{ $val }}">{{ $lbl }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700/50 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 border-b border-gray-100 dark:border-gray-700">
                            <th class="px-6 py-4 w-10">
                                <input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            </th>
                            <th class="px-6 py-4">SPK & Customer</th>
                            <th class="px-6 py-4">Item & Merek</th>
                            <th class="px-6 py-4">Status & Lokasi</th>
                            <th class="px-6 py-4">Penyimpanan Rak</th>
                            <th class="px-6 py-4 text-center">Pembaruan Terakhir</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                        @forelse($workOrders as $order)
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-colors group {{ in_array($order->id, $selectedIds) ? 'bg-indigo-50/30 dark:bg-indigo-900/10' : '' }}">
                                <td class="px-6 py-4">
                                    <input type="checkbox" wire:model.live="selectedIds" value="{{ $order->id }}" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 dark:text-white text-sm">{{ $order->spk_number }}</div>
                                    <div class="text-xs text-gray-500 font-bold uppercase mt-0.5">{{ $order->customer_name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ $order->shoe_brand ?? '-' }}</div>
                                    <div class="text-[10px] text-gray-400 mt-0.5">{{ $order->shoe_color ?? '-' }} (Size: {{ $order->shoe_size ?? '-' }})</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1 items-start">
                                        <span class="px-2.5 py-0.5 bg-indigo-50 text-indigo-700 text-[9px] font-black rounded-full border border-indigo-100 uppercase tracking-wider">
                                            {{ $order->status->label() }}
                                        </span>
                                        <span class="text-[9px] text-gray-400 font-bold uppercase tracking-tight">
                                            📍 {{ $order->current_location ?: 'Unknown' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($order->storage_rack_code)
                                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[9px] font-black rounded-lg border border-emerald-100 uppercase">
                                            📦 {{ $order->storage_rack_code }}
                                        </span>
                                    @else
                                        <span class="text-[9px] text-gray-400 font-bold uppercase">
                                            -
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="text-xs font-medium text-gray-600 dark:text-gray-400">
                                        {{ $order->updated_at ? $order->updated_at->format('d M Y') : '-' }}
                                    </div>
                                    <div class="text-[9px] text-gray-400 font-semibold mt-0.5 uppercase">
                                        {{ $order->updated_at ? $order->updated_at->diffForHumans() : '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" target="_blank" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-[10px] font-black uppercase tracking-widest transition-colors inline-block">
                                        Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                        <p class="text-lg font-bold">Data Tidak Ditemukan</p>
                                        <p class="text-sm">Tidak ada SPK yang sesuai dengan pencarian atau filter.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="p-6 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-700">
                {{ $workOrders->links() }}
            </div>
        </div>
    </div>

    {{-- Floating Action Bar --}}
    <div x-show="$wire.selectedIds.length > 0" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-y-full opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="translate-y-full opacity-0"
         class="fixed bottom-10 left-1/2 -translate-x-1/2 z-50 flex items-center gap-8 px-10 py-5 bg-gray-900/95 backdrop-blur-md text-white rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.4)] border border-white/10 border-t-white/20">
        
        <div class="flex items-center gap-4">
            <div class="flex -space-x-2">
                <span class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center font-black text-sm ring-4 ring-gray-900 shadow-lg shadow-indigo-500/20" x-text="$wire.selectedIds.length"></span>
            </div>
            <div>
                <div class="text-xs font-black uppercase tracking-widest text-indigo-400">Total Terpilih</div>
                <div class="text-[10px] font-medium text-gray-400 leading-none mt-1">
                    @php
                        $currentPageIds = $workOrders->pluck('id')->map(fn($id) => (string) $id)->toArray();
                        $currentSelectedCount = count(array_intersect($currentPageIds, $selectedIds));
                        $otherSelectedCount = count($selectedIds) - $currentSelectedCount;
                    @endphp
                    @if($otherSelectedCount > 0)
                        {{ $currentSelectedCount }} di hal. ini, {{ $otherSelectedCount }} di hal. lain
                    @else
                        Semua di halaman ini
                    @endif
                </div>
            </div>
        </div>
        
        <div class="w-px h-10 bg-white/10"></div>
        
        <div class="flex items-center gap-4">
            @if($activeTab === 'active')
                <button @click="confirmArchive()" class="bg-indigo-600 hover:bg-indigo-500 px-8 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all flex items-center gap-3 shadow-lg shadow-indigo-500/30 group">
                    <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    Arsipkan Data
                </button>
            @else
                <button @click="confirmRestore()" class="bg-emerald-600 hover:bg-emerald-500 px-8 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all flex items-center gap-3 shadow-lg shadow-emerald-500/30 group">
                    <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H17"></path></svg>
                    Pulihkan Data
                </button>
            @endif
            
            <button wire:click="clearSelection" class="text-xs font-black text-gray-400 hover:text-white uppercase tracking-widest transition-colors px-4">
                Reset Pilihan
            </button>
        </div>
    </div>
</div>
