<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Station: Sortir & Material Check') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-4">Validasi Material</h3>
                    
                    <div class="relative overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">SPK</th>
                                    <th scope="col" class="px-6 py-3">Customer</th>
                                    <th scope="col" class="px-6 py-3">Layanan</th>
                                    <th scope="col" class="px-6 py-3">Status Material</th>
                                    <th scope="col" class="px-6 py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($queue as $order)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-6 py-4 font-bold">{{ $order->spk_number }}</td>
                                    <td class="px-6 py-4">{{ $order->customer_name }}</td>
                                    <td class="px-6 py-4">
                                        @foreach($order->services as $s)
                                            <span class="block text-xs">• {{ $s->name }}</span>
                                        @endforeach
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $hasPending = $order->materials->where('pivot.status', 'REQUESTED')->count() > 0;
                                        @endphp
                                        @if($order->materials->isEmpty())
                                            <span class="text-gray-400 italic">Belum dicek</span>
                                        @elseif($hasPending)
                                            <span class="text-red-500 font-bold">BUTUH BELANJA</span>
                                        @else
                                            <span class="text-green-500 font-bold">READY</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('sortir.show', $order->id) }}" class="text-blue-600 hover:text-blue-900 font-semibold">CEK MATERIAL</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center">Tidak ada antrian di Sortir.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
