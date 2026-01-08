<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Station: Barang Selesai & Pickup') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Ready for Pickup -->
            <div class="p-6 bg-green-50 dark:bg-gray-900 border border-green-100 dark:border-green-900 shadow sm:rounded-lg">
                <header class="mb-4">
                    <h2 class="text-lg font-bold text-green-900 dark:text-green-100 mb-1">Siap Diambil (Ready for Pickup)</h2>
                    <p class="text-sm text-green-600 dark:text-green-300">Hubungi customer untuk pengambilan.</p>
                </header>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($ready as $order)
                        @if(is_null($order->taken_date))
                        <div class="border p-4 rounded-lg bg-white dark:bg-gray-800 shadow-sm flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-start mb-2">
                                    <a href="{{ route('finish.show', $order->id) }}" class="font-bold text-xl hover:text-indigo-600 hover:underline">
                                        {{ $order->spk_number }} 🔗
                                    </a>
                                    <span class="text-xs bg-green-200 text-green-900 px-2 py-0.5 rounded">READY</span>
                                </div>
                                <div class="text-sm mb-4">
                                     <p class="font-semibold">{{ $order->customer_name }}</p>
                                     <p>{{ $order->customer_phone }}</p>
                                     <hr class="my-2">
                                     <p>{{ $order->shoe_brand }} - {{ $order->shoe_color }}</p>
                                </div>
                            </div>
                            
                            <form action="{{ route('finish.pickup', $order->id) }}" method="POST">
                                @csrf
                                <button class="w-full bg-gray-800 text-white py-2 rounded shadow hover:bg-gray-900 font-bold uppercase text-sm">
                                    Sudah Diambil
                                </button>
                            </form>
                        </div>
                        @endif
                    @empty
                    <p class="text-gray-500 italic col-span-3">Tidak ada barang yang menunggu pickup.</p>
                    @endforelse
                </div>
            </div>

            <!-- History Taken -->
            <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <header class="mb-4">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">Riwayat Pengambilan</h2>
                </header>

                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">SPK</th>
                                <th class="px-6 py-3">Customer</th>
                                <th class="px-6 py-3">Waktu Ambil</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $order)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-6 py-4 font-bold">
                                    <a href="{{ route('finish.show', $order->id) }}" class="hover:text-indigo-600 hover:underline">
                                        {{ $order->spk_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-4">{{ $order->customer_name }}</td>
                                <td class="px-6 py-4">{{ $order->taken_date->format('d M Y H:i') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center">Belum ada riwayat.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
