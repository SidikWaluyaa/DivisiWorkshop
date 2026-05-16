<div class="py-12" x-data="{ 
    confirmCleanup() {
        Swal.fire({
            title: 'Konfirmasi Cleanup',
            text: 'Data yang dipilih akan dipindahkan ke Sampah. Lanjutkan?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Bersihkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $wire.cleanupSelected();
            }
        })
    }
}">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('finish.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-[#22B086] dark:text-gray-400 dark:hover:text-white">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                        Finish Station
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 text-sm font-bold text-gray-500 md:ml-2 dark:text-gray-400">Cleanup Hub</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100 dark:border-gray-700">
            {{-- Header --}}
            <div class="p-8 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-br from-gray-50 to-white dark:from-gray-800 dark:to-gray-900">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div>
                        <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Cleanup Hub <span class="text-[#22B086]">Data Finish</span></h1>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 font-medium italic">Gunakan halaman ini untuk membersihkan data "hantu" (data sistem ada, fisik barang tidak ditemukan).</p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <div class="relative">
                            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari SPK atau Nama..." 
                                   class="pl-10 pr-4 py-2.5 bg-white dark:bg-gray-700 border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-[#22B086] focus:border-[#22B086] w-64 shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                        </div>
                        <select wire:model.live="filterMonths" class="py-2.5 bg-white dark:bg-gray-700 border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-[#22B086] focus:border-[#22B086] shadow-sm">
                            <option value="">Semua Usia Data</option>
                            <option value="1">> 1 Bulan</option>
                            <option value="3">> 3 Bulan</option>
                            <option value="6">> 6 Bulan</option>
                            <option value="12">> 1 Tahun</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700/50 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 border-b border-gray-100 dark:border-gray-700">
                            <th class="px-6 py-4 w-10">
                                <input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 text-[#22B086] border-gray-300 rounded focus:ring-[#22B086]">
                            </th>
                            <th class="px-6 py-4 w-20">Foto</th>
                            <th class="px-6 py-4">SPK & Customer</th>
                            <th class="px-6 py-4">Item & Merek</th>
                            <th class="px-6 py-4 text-center">Status Rak</th>
                            <th class="px-6 py-4 text-center">Tgl Selesai</th>
                            <th class="px-6 py-4 text-right">Usia (Hari)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                        @forelse($workOrders as $order)
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-colors group {{ in_array($order->id, $selectedIds) ? 'bg-emerald-50/30 dark:bg-emerald-900/10' : '' }}">
                                <td class="px-6 py-4">
                                    <input type="checkbox" wire:model.live="selectedIds" value="{{ $order->id }}" class="w-4 h-4 text-[#22B086] border-gray-300 rounded focus:ring-[#22B086]">
                                </td>
                                <td class="px-6 py-4">
                                    @if($order->spk_cover_photo_url)
                                        <img src="{{ $order->spk_cover_photo_url }}" class="w-12 h-12 rounded-lg object-cover shadow-sm border border-gray-200 dark:border-gray-600">
                                    @else
                                        <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-300 dark:text-gray-500 border border-gray-200 dark:border-gray-600">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 dark:text-white">{{ $order->spk_number }}</div>
                                    <div class="text-xs text-gray-500">{{ $order->customer_name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ $order->shoe_brand }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $order->shoe_color }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($order->storage_rack_code)
                                        <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-[9px] font-black rounded-lg border border-emerald-200">
                                            📦 {{ $order->storage_rack_code }}
                                        </span>
                                    @else
                                        <span class="px-2 py-1 bg-amber-100 text-amber-700 text-[9px] font-black rounded-lg border border-amber-200">
                                            ⏳ BELUM RAK
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="text-xs font-medium text-gray-600 dark:text-gray-400">
                                        {{ $order->finished_date ? $order->finished_date->format('d M Y') : '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @php
                                        $days = $order->finished_date ? now()->diffInDays($order->finished_date) : 0;
                                        $color = $days > 180 ? 'text-red-500' : ($days > 90 ? 'text-amber-500' : 'text-gray-500');
                                    @endphp
                                    <span class="text-xs font-black {{ $color }}">{{ $days }} Hari</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                        <p class="text-lg font-bold">Data Tidak Ditemukan</p>
                                        <p class="text-sm">Cobalah filter atau kata kunci pencarian yang berbeda.</p>
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
                <span class="w-10 h-10 rounded-full bg-[#22B086] flex items-center justify-center font-black text-sm ring-4 ring-gray-900 shadow-lg shadow-emerald-500/20" x-text="$wire.selectedIds.length"></span>
            </div>
            <div>
                <div class="text-xs font-black uppercase tracking-widest text-emerald-400">Terpilih</div>
                <div class="text-[10px] font-medium text-gray-400 leading-none mt-1">Siap diproses cleanup</div>
            </div>
        </div>
        
        <div class="w-px h-10 bg-white/10"></div>
        
        <div class="flex items-center gap-4">
            <button @click="confirmCleanup()" class="bg-red-500 hover:bg-red-600 px-8 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all flex items-center gap-3 shadow-lg shadow-red-500/30 group">
                <svg class="w-4 h-4 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Bersihkan Data
            </button>
            
            <button @click="$wire.set('selectedIds', [])" class="text-xs font-black text-gray-400 hover:text-white uppercase tracking-widest transition-colors px-4">
                Batal
            </button>
        </div>
    </div>

    @push('scripts')
    <script>
        window.addEventListener('swal:alert', event => {
            Swal.fire({
                icon: event.detail[0].icon,
                title: event.detail[0].title,
                text: event.detail[0].text,
                timer: 3000,
                showConfirmButton: false
            });
        });
    </script>
    @endpush
</div>
