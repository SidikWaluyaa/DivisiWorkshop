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

                <!-- Ready for Pickup -->
            <div class="p-6 bg-gradient-to-r from-teal-600 to-orange-500 dark:from-gray-800 dark:to-gray-900 shadow-lg sm:rounded-xl text-white">
                <header class="mb-6 flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold text-white flex items-center gap-2">
                            <span class="p-1.5 bg-white/20 rounded-lg backdrop-blur-sm">🚀</span>
                            Siap Diambil
                        </h2>
                        <p class="text-sm text-teal-100 mt-1 ml-9 opacity-90">Barang sudah lolos QC dan siap diserahkan.</p>
                    </div>
                    <span class="px-3 py-1 bg-white/20 backdrop-blur-md text-white border border-white/30 rounded-full text-xs font-bold">{{ $ready->count() }} Order</span>
                </header>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 md:gap-4">
                    @forelse($ready as $order)
                        @if(is_null($order->taken_date))
                        <div class="group relative bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-xl transition-all duration-300 border border-teal-50 dark:border-gray-700 overflow-hidden transform hover:-translate-y-1">
                            <!-- Status Bar -->
                            <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-orange-400 to-teal-500"></div>
                            
                            <div class="p-3">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="min-w-0">
                                        <a href="{{ route('finish.show', $order->id) }}" class="font-bold text-base text-gray-800 dark:text-gray-100 hover:text-orange-500 transition-colors truncate block" title="{{ $order->spk_number }}">
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
                                        <div class="font-medium text-xs text-gray-600 dark:text-gray-300 truncate" title="{{ $order->shoe_brand }} - {{ $order->shoe_color }}">{{ $order->shoe_brand }} - {{ $order->shoe_color }}</div>
                                    </div>
                                    <span class="text-[10px] font-bold bg-green-100 text-green-700 px-1.5 py-0.5 rounded border border-green-200 uppercase tracking-wide shrink-0">
                                        SIAP
                                    </span>
                                </div>
                                
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-md p-2 mb-3 border border-gray-100 dark:border-gray-600">
                                    <div class="text-[10px] text-gray-500 uppercase tracking-wider mb-0.5">Info Pelanggan</div>
                                    <p class="font-semibold text-xs text-gray-900 dark:text-gray-100 truncate" title="{{ $order->customer_name }}">{{ $order->customer_name }}</p>
                                    <p class="text-[10px] text-gray-500 truncate">{{ $order->customer_phone }}</p>
                                </div>

                                <form action="{{ route('finish.pickup', $order->id) }}" method="POST">
                                    @csrf
                                    <button class="w-full bg-teal-600 hover:bg-teal-700 text-white py-2 rounded-md shadow hover:shadow-md font-bold text-[10px] uppercase tracking-wider flex items-center justify-center gap-1.5 transition-all">
                                        <span>Ambil</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endif
                    @empty
                    <div class="col-span-full flex flex-col items-center justify-center p-8 text-center text-teal-100/70 border border-dashed border-white/20 rounded-lg">
                        <svg class="w-10 h-10 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        <p class="text-sm">Tidak ada barang yang menunggu pickup.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- History Taken -->
            <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700">
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
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
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
                                                <span>{{ $service->name }}</span>
                                                <span class="font-mono">{{ number_format($service->price, 0, ',', '.') }}</span>
                                            </div>
                                        @endforeach
                                        <div class="border-t border-gray-200 dark:border-gray-600 pt-1 mt-1 flex justify-between gap-4 font-bold text-xs text-gray-800 dark:text-gray-200">
                                            <span>Total</span>
                                            <span>Rp {{ number_format($order->services->sum('price'), 0, ',', '.') }}</span>
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
            </div>

        </div>
    </div>
</x-app-layout>
