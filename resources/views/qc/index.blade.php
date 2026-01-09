<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Station: Quality Control') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-4">Antrian QC</h3>
                    
                    <div class="relative overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-6 py-3">SPK</th>
                                    <th class="px-6 py-3">Sepatu</th>
                                    <th class="px-6 py-3">Layanan</th>
                                    <th class="px-6 py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($queue as $order)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-6 py-4 font-bold">
                                        {{ $order->spk_number }}
                                        @if($order->is_revision)
                                            <span class="ml-2 bg-purple-100 text-purple-800 text-[10px] px-2 py-0.5 rounded border border-purple-400 font-extrabold animate-pulse">
                                                PRIORITY REVISION
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $order->shoe_brand }} - {{ $order->shoe_color }}
                                    </td>
                                    <td class="px-6 py-4 hidden sm:table-cell">
                                        @foreach($order->services as $s)
                                            <span class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-xs">{{ $s->name }}</span>
                                        @endforeach
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('qc.show', $order->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                            INSPECT
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center">Tidak ada antrian QC.</td>
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
