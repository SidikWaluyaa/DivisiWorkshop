<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Station: Preparation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-4">Dalam Pengerjaan (Preparation)</h3>
                    
                    <div class="grid grid-cols-1 gap-6">
                        @forelse($queue as $order)
                        <div class="border p-4 rounded-lg flex justify-between items-center dark:border-gray-600">
                            <div>
                                <div class="font-bold text-lg">{{ $order->spk_number }}</div>
                                <div class="text-sm">
                                    {{ $order->shoe_brand }} - {{ $order->shoe_color }}
                                </div>
                                <div class="text-xs mt-2 text-gray-500">
                                    Services: 
                                    @foreach($order->services as $s)
                                        <span class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $s->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('preparation.show', $order->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                                    Kelola Sub-Task
                                </a>
                            </div>
                        </div>
                        @empty
                        <p class="text-center text-gray-500">Tidak ada sepatu di Preparation.</p>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
