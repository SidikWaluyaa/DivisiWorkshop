<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Form Assessment: ') . $order->spk_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <form action="{{ route('assessment.store', $order->id) }}" method="POST" class="p-6 text-gray-900 dark:text-gray-100">
                    @csrf
                    
                    <!-- Item Info -->
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <h3 class="font-bold text-lg mb-2">Info Sepatu (Edit jika perlu)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <x-input-label for="shoe_brand" value="Brand Sepatu" />
                                <x-text-input id="shoe_brand" name="shoe_brand" type="text" class="mt-1 block w-full" :value="$order->shoe_brand" required />
                            </div>
                            <div>
                                <x-input-label for="shoe_size" value="Size" />
                                <x-text-input id="shoe_size" name="shoe_size" type="text" class="mt-1 block w-full" :value="$order->shoe_size" required />
                            </div>
                            <div>
                                <x-input-label for="shoe_color" value="Warna" />
                                <x-text-input id="shoe_color" name="shoe_color" type="text" class="mt-1 block w-full" :value="$order->shoe_color" required />
                            </div>
                        </div>
                        <div class="mt-2">
                            <p class="text-sm"><strong>Customer:</strong> {{ $order->customer_name }}</p>
                        </div>
                    </div>

                    <!-- Service Selection -->
                    <div class="mb-6">
                        <h3 class="font-bold text-lg mb-4">Pilih Layanan / Tindakan</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($services as $category => $serviceList)
                            <div class="border rounded-lg p-4 dark:border-gray-600">
                                <h4 class="font-semibold text-md mb-2 border-b">{{ $category }}</h4>
                                <div class="space-y-2">
                                    @foreach($serviceList as $service)
                                    <label class="flex items-center space-x-2 cursor-pointer">
                                        <input type="checkbox" name="services[]" value="{{ $service->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span>{{ $service->name }}</span>
                                        <span class="text-xs text-gray-500">(Rp {{ number_format($service->price, 0, ',', '.') }})</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('services')" />
                    </div>

                    <!-- Notes -->
                    <div class="mb-6">
                        <x-input-label for="notes" :value="__('Catatan / Kondisi / Risiko')" />
                        <textarea id="notes" name="notes" rows="4" class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Contoh: Midsole menguning parah, lem lekang bagian tumit..."></textarea>
                         <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                    </div>

                    <div class="flex items-center justify-end gap-4 border-t pt-4">
                        <a href="{{ route('assessment.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900">Cancel</a>
                        <x-primary-button class="bg-blue-600 hover:bg-blue-700">
                            {{ __('SIMPAN & LOCK LAYANAN') }}
                        </x-primary-button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
