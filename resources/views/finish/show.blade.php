<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Order Details: ') . $order->spk_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Order Info & Pickup Action -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">{{ $order->customer_name }}</h3>
                        <p class="text-gray-500">{{ $order->customer_phone }}</p>
                        <p class="mt-2 font-semibold">{{ $order->shoe_brand }} - {{ $order->shoe_color }}</p>
                    </div>
                    <div>
                         @if(is_null($order->taken_date))
                            <form action="{{ route('finish.pickup', $order->id) }}" method="POST">
                                @csrf
                                <button class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-bold shadow-lg">
                                    CUSTOMER PICKUP
                                </button>
                            </form>
                        @else
                            <div class="text-right">
                                <span class="bg-gray-200 text-gray-800 px-3 py-1 rounded-full text-sm font-semibold">SUDAH DIAMBIL</span>
                                <p class="text-xs text-gray-400 mt-1">Tanggal: {{ $order->taken_date->format('d M Y H:i') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Execution Team (New Data) -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100 border-b pb-2">Tim Eksekusi (PIC & Teknisi)</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Sortir Stage -->
                    <div>
                        <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-2">1. Sortir Material</h4>
                        <ul class="space-y-2 text-sm">
                            <li class="flex justify-between">
                                <span class="text-gray-500">PIC Sol:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-200">
                                    {{ $order->picSortirSol->name ?? '-' }}
                                </span>
                            </li>
                            <li class="flex justify-between">
                                <span class="text-gray-500">PIC Upper:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-200">
                                    {{ $order->picSortirUpper->name ?? '-' }}
                                </span>
                            </li>
                        </ul>
                    </div>

                    <!-- Production Stage -->
                    <div>
                        <h4 class="font-semibold text-blue-700 dark:text-blue-300 mb-2">2. Production</h4>
                        <ul class="space-y-2 text-sm">
                            <li class="flex justify-between">
                                <span class="text-gray-500">Main Technician:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-200">
                                    {{ $order->technicianProduction->name ?? '-' }}
                                </span>
                            </li>
                        </ul>
                    </div>

                    <!-- QC Stage -->
                    <div class="md:col-span-2">
                        <h4 class="font-semibold text-green-700 dark:text-green-300 mb-2">3. Quality Control (QC)</h4>
                         <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                            <div>
                                <span class="block text-gray-500 text-xs uppercase">Jahit Sol Tech</span>
                                <span class="font-medium">{{ $order->qcJahitTechnician->name ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="block text-gray-500 text-xs uppercase">Clean Up Tech</span>
                                <span class="font-medium">{{ $order->qcCleanupTechnician->name ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="block text-gray-500 text-xs uppercase">Final QC PIC</span>
                                <span class="font-bold text-green-600 dark:text-green-400">{{ $order->qcFinalPic->name ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes & Services -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">Layanan & Catatan</h3>
                <div class="mb-4">
                    <strong class="block text-sm text-gray-500">Services:</strong>
                    <ul class="list-disc list-inside">
                        @foreach($order->services as $service)
                            <li>{{ $service->name }}</li>
                        @endforeach
                    </ul>
                </div>
                <div>
                     <strong class="block text-sm text-gray-500">Catatan Order:</strong>
                     <p class="text-gray-700 dark:text-gray-300">{{ $order->notes ?: '-' }}</p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
