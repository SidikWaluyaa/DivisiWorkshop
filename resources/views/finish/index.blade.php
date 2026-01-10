<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Station: Barang Selesai & Pickup') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Ready for Pickup -->
            <div class="p-6 bg-gradient-to-r from-teal-50 to-emerald-50 dark:from-gray-800 dark:to-gray-900 border border-teal-100 dark:border-gray-700 shadow-md sm:rounded-xl">
                <header class="mb-6 flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold text-teal-900 dark:text-teal-100 flex items-center gap-2">
                            <span class="p-2 bg-white dark:bg-gray-700 rounded-lg shadow-sm">🚀</span>
                            Siap Diambil
                        </h2>
                        <p class="text-sm text-teal-600 dark:text-teal-300 mt-1 ml-11">Barang sudah lolos QC dan siap diserahkan ke customer.</p>
                    </div>
                    <span class="px-3 py-1 bg-teal-100 text-teal-800 rounded-full text-xs font-bold">{{ $ready->count() }} Order</span>
                </header>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($ready as $order)
                        @if(is_null($order->taken_date))
                        <div class="group relative bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100 dark:border-gray-700 overflow-hidden transform hover:-translate-y-1">
                            <!-- Status Bar -->
                            <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-teal-400 to-emerald-500"></div>
                            
                            <div class="p-5">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <a href="{{ route('finish.show', $order->id) }}" class="font-bold text-lg text-gray-800 dark:text-gray-100 hover:text-teal-600 transition-colors">
                                            {{ $order->spk_number }}
                                        </a>
                                        <div class="text-[10px] text-gray-400 mt-0.5">Merek & Warna</div>
                                        <div class="font-medium text-sm text-gray-600 dark:text-gray-300">{{ $order->shoe_brand }} - {{ $order->shoe_color }}</div>
                                    </div>
                                    <span class="text-[10px] font-bold bg-green-100 text-green-700 px-2 py-1 rounded border border-green-200 uppercase tracking-wide">
                                        SIAP
                                    </span>
                                </div>
                                
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 mb-4 border border-gray-100 dark:border-gray-600">
                                    <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Info Pelanggan</div>
                                    <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $order->customer_name }}</p>
                                    <p class="text-sm text-gray-500">{{ $order->customer_phone }}</p>
                                </div>

                                <form action="{{ route('finish.pickup', $order->id) }}" method="POST">
                                    @csrf
                                    <button class="w-full bg-gray-900 dark:bg-gray-700 text-white py-2.5 rounded-lg shadow-md hover:bg-gray-800 dark:hover:bg-gray-600 font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2 transition-all">
                                        <span>Konfirmasi Ambil</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endif
                    @empty
                    <div class="col-span-3 flex flex-col items-center justify-center p-12 text-center text-gray-400 bg-white dark:bg-gray-800 rounded-xl border border-dashed border-gray-300 dark:border-gray-700">
                        <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        <p>Tidak ada barang yang menunggu pickup.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- History Taken -->
            <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700">
                <header class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">Riwayat Pengambilan Terakhir</h2>
                    <a href="#" class="text-xs font-semibold text-teal-600 hover:text-teal-800">Lihat Semua →</a>
                </header>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-700 uppercase bg-gradient-to-r from-gray-100 to-gray-50 dark:from-gray-700 dark:to-gray-800 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">SPK & Customer</th>
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
