<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight truncate">
                {{ __('Order Details: ') . $order->spk_number }}
            </h2>
            <a href="{{ route('finish.index') }}" class="shrink-0 px-4 py-2 bg-white/20 hover:bg-white/30 border border-white/50 text-white text-sm font-medium rounded-lg transition-colors shadow-sm flex items-center gap-2 backdrop-blur-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- LEFT COLUMN: Order Info & Actions -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Main Card -->
                    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700">
                        <div class="bg-gradient-to-r from-teal-600 to-emerald-600 p-6 text-white">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-3xl font-bold mb-1">{{ $order->customer_name }}</h3>
                                    <p class="text-teal-100 font-medium">{{ $order->customer_phone }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="bg-white/20 backdrop-blur-sm px-3 py-1 rounded-lg text-sm font-semibold border border-white/30">
                                        {{ $order->spk_number }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-6">
                             <div class="flex items-center gap-4 mb-6">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center text-2xl shadow-inner">
                                    👟
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 uppercase tracking-wide font-bold">Item Detail</p>
                                    <h4 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ $order->shoe_brand }}</h4>
                                    <p class="text-gray-600 dark:text-gray-400">{{ $order->shoe_color }}</p>
                                </div>
                             </div>
                             
                             <!-- Action Area -->
                             <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-5 border border-gray-100 dark:border-gray-600">
                                 @if(is_null($order->taken_date))
                                    <div class="flex flex-col gap-3">
                                        <form action="{{ route('finish.pickup', $order->id) }}" method="POST">
                                            @csrf
                                            <button class="w-full bg-gradient-to-r from-gray-800 to-gray-900 hover:from-black hover:to-black text-white py-3.5 rounded-lg shadow-lg hover:shadow-xl font-bold text-sm uppercase tracking-wider flex items-center justify-center gap-2 transform transition-all hover:-translate-y-0.5">
                                                <span>✅ Konfirmasi Barang Diambil</span>
                                            </button>
                                        </form>
                                        
                                        <div x-data="{ open: false }" class="text-center pt-2 space-y-3">
                                            @php
                                                $waMessage = "Halo Kak {$order->customer_name}, sepatu {$order->shoe_brand} - {$order->shoe_color} (SPK: {$order->spk_number}) sudah selesai dicuci/diperbaiki.\n\nApakah berminat untuk menambah layanan lain (Upsell) agar sepatu Kakak makin kinclong?";
                                                $waLink = "https://wa.me/" . preg_replace('/^0/', '62', $order->customer_phone) . "?text=" . urlencode($waMessage);
                                            @endphp
                                            
                                            <a href="{{ $waLink }}" target="_blank" class="block w-full border border-green-500 text-green-600 hover:bg-green-50 font-bold py-2.5 rounded-lg transition-colors flex items-center justify-center gap-2">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                                Tawarkan Jasa via WhatsApp
                                            </a>

                                            <button @click="open = true" class="text-sm font-medium text-teal-600 hover:text-teal-800 flex items-center justify-center gap-1 mx-auto transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                Input Tambah Jasa (System)
                                            </button>

                                            <!-- Modal -->
                                            <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" style="display: none;" x-transition.opacity>
                                                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 w-full max-w-md transform transition-all scale-100" @click.away="open = false">
                                                    <div class="mb-4">
                                                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                                                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                                                        </div>
                                                        <h3 class="text-lg font-bold text-center text-gray-900 dark:text-gray-100 mt-4">Tambah Layanan (Upsell)</h3>
                                                        <p class="text-sm text-center text-gray-500 mt-1">
                                                            Order akan dikembalikan ke status <strong>PREPARATION</strong> untuk pengerjaan ulang.
                                                        </p>
                                                    </div>

                                                    <form action="{{ route('finish.add-service', $order->id) }}" method="POST">
                                                        @csrf
                                                        <div class="mb-6">
                                                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Pilih Layanan</label>
                                                            <div class="relative">
                                                                <select name="service_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 sm:text-sm py-3" required>
                                                                    <option value="">-- Cari Layanan --</option>
                                                                    @foreach($services as $service)
                                                                        <option value="{{ $service->id }}">{{ $service->name }} (Rp {{ number_format($service->price, 0, ',', '.') }})</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="grid grid-cols-2 gap-3">
                                                            <button type="button" @click="open = false" class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-semibold transition-colors">Batal</button>
                                                            <button type="submit" class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold shadow-md transition-colors">Simpan & Proses</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <div class="inline-flex flex-col items-center">
                                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-600 mb-2">
                                                 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                            <span class="text-lg font-bold text-green-700">SUDAH DIAMBIL</span>
                                            <p class="text-sm text-gray-500 mt-1">Pada: {{ $order->taken_date->format('d M Y, H:i') }}</p>
                                        </div>
                                    </div>
                                @endif
                             </div>
                        </div>
                    </div>

                    <!-- Services List -->
                    <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 border border-gray-100 dark:border-gray-700">
                        <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                            <span class="w-1 h-6 bg-teal-500 rounded-full"></span>
                            Layanan yang Dikerjakan
                        </h3>
                        <div class="space-y-3">
                            @foreach($order->services as $service)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <span class="font-medium text-gray-700 dark:text-gray-200">{{ $service->name }}</span>
                                <span class="text-sm font-bold text-teal-600 bg-teal-50 px-2 py-1 rounded">Rp {{ number_format($service->price, 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>
                        
                        @if($order->notes)
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <h4 class="text-sm font-bold text-gray-500 uppercase mb-2">Catatan Order</h4>
                            <p class="text-gray-600 italic bg-yellow-50 p-3 rounded-lg border border-yellow-100">{{ $order->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- RIGHT COLUMN: Timeline & Team -->
                <div class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 border border-gray-100 dark:border-gray-700 h-full">
                        <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-6 flex items-center gap-2">
                            <span class="w-1 h-6 bg-indigo-500 rounded-full"></span>
                            Tim Eksekusi
                        </h3>

                        <div class="relative border-l-2 border-gray-200 ml-3 space-y-8">
                            <!-- Sortir -->
                            <div class="relative pl-8">
                                <span class="absolute -left-[9px] top-0 bg-white dark:bg-gray-800 w-4 h-4 rounded-full border-2 border-indigo-500"></span>
                                <h4 class="font-bold text-gray-800 dark:text-gray-100">Sortir Material</h4>
                                <div class="mt-2 space-y-2">
                                    <div class="text-sm">
                                        <span class="text-gray-400 block text-xs">PIC Sol</span> 
                                        <span class="font-medium">{{ $order->picSortirSol->name ?? '-' }}</span>
                                    </div>
                                    <div class="text-sm">
                                        <span class="text-gray-400 block text-xs">PIC Upper</span>
                                        <span class="font-medium">{{ $order->picSortirUpper->name ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Production -->
                            <div class="relative pl-8">
                                <span class="absolute -left-[9px] top-0 bg-white dark:bg-gray-800 w-4 h-4 rounded-full border-2 border-blue-500"></span>
                                <h4 class="font-bold text-gray-800 dark:text-gray-100">Production</h4>
                                <div class="mt-2 space-y-2">
                                    <div class="text-sm">
                                        <span class="text-gray-400 block text-xs">Sol</span>
                                        <span class="font-medium">{{ $order->prodSolBy->name ?? '-' }}</span>
                                    </div>
                                    <div class="text-sm">
                                        <span class="text-gray-400 block text-xs">Upper</span>
                                        <span class="font-medium">{{ $order->prodUpperBy->name ?? '-' }}</span>
                                    </div>
                                    <div class="text-sm">
                                        <span class="text-gray-400 block text-xs">Cleaning/Repaint</span>
                                        <span class="font-medium">{{ $order->prodCleaningBy->name ?? '-' }}</span>
                                    </div>
                                    {{-- Fallback for legacy data --}}
                                    @if(!$order->prodSolBy && !$order->prodUpperBy && !$order->prodCleaningBy && $order->technicianProduction)
                                        <div class="text-sm pt-2 border-t border-gray-100">
                                            <span class="text-gray-400 block text-xs">Teknisi (Legacy)</span>
                                            <span class="font-medium">{{ $order->technicianProduction->name ?? '-' }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- QC -->
                            <div class="relative pl-8">
                                <span class="absolute -left-[9px] top-0 bg-white dark:bg-gray-800 w-4 h-4 rounded-full border-2 border-green-500"></span>
                                <h4 class="font-bold text-gray-800 dark:text-gray-100">Quality Control</h4>
                                <div class="mt-2 space-y-3 bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Jahit</span>
                                        <span class="font-medium">{{ $order->qcJahitBy->name ?? $order->qcJahitTechnician->name ?? '-' }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Cleanup</span>
                                        <span class="font-medium">{{ $order->qcCleanupBy->name ?? $order->qcCleanupTechnician->name ?? '-' }}</span>
                                    </div>
                                    <div class="border-t border-gray-200 my-1"></div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500 font-bold">FINAL CHECK</span>
                                        <span class="font-bold text-green-600">{{ $order->qcFinalBy->name ?? $order->qcFinalPic->name ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
