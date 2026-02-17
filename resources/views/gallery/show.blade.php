<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('gallery.index') }}" class="p-2 bg-white/20 rounded-lg backdrop-blur-sm shadow-sm border border-white/30 hover:bg-white/30 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            
            <div class="flex flex-col">
                <h2 class="font-bold text-xl leading-tight tracking-wide">
                    {{ $order->spk_number }}
                </h2>
                <div class="text-xs font-medium opacity-90">
                    {{ $order->customer_name }} - {{ $order->shoe_brand }}
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50/50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Info Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Customer</span>
                        <div class="font-bold text-gray-800">{{ $order->customer_name }}</div>
                        <div class="text-sm text-gray-500">{{ $order->customer_phone }}</div>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Sepatu</span>
                        <div class="font-bold text-gray-800">{{ $order->shoe_brand }}</div>
                        <div class="text-sm text-gray-500">{{ $order->shoe_color }} - {{ $order->shoe_size }}</div>
                    </div>
                     <div>
                        <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Layanan</span>
                        <div class="flex flex-wrap gap-1">
                            @foreach($order->services as $s)
                                <span class="px-2 py-1 rounded bg-teal-50 text-teal-700 text-xs font-bold border border-teal-100">{{ $s->name }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline Gallery -->
            <div class="space-y-8 relative before:absolute before:inset-y-0 before:left-4 md:before:left-1/2 before:-ml-px before:w-0.5 before:bg-gray-200">
                
                @foreach(['PREPARATION', 'SORTIR / MATERIAL', 'PRODUCTION', 'QC CHECK', 'FINISHING', 'UPSELL / TAMBAH JASA', 'OTHER'] as $phase)
                    @if(isset($groupedPhotos[$phase]) && $groupedPhotos[$phase]->isNotEmpty())
                        <div class="relative flex flex-col md:flex-row items-center justify-between">
                            
                            <!-- Dot -->
                            <div class="absolute left-4 md:left-1/2 -ml-3 md:-ml-3 mt-4 md:mt-0 w-6 h-6 rounded-full border-4 border-white shadow-sm flex items-center justify-center
                                {{ $phase === 'QC CHECK' ? 'bg-purple-500' : ($phase === 'UPSELL / TAMBAH JASA' ? 'bg-blue-500' : 'bg-teal-500') }} z-10">
                            </div>

                            <!-- Label -->
                            <div class="pl-12 md:pl-0 md:w-1/2 md:pr-12 text-left md:text-right mb-4 md:mb-0 order-1 md:order-1 {{ $loop->index % 2 == 0 ? '' : 'md:order-3 md:text-left md:pl-12 md:pr-0' }}">
                                <h3 class="text-lg font-black text-gray-800 uppercase tracking-wider">{{ $phase }}</h3>
                                <div class="text-xs text-gray-500">{{ $groupedPhotos[$phase]->count() }} Foto Dokumentasi</div>
                            </div>

                            <!-- Spacer for alternating layout -->
                            <div class="hidden md:block md:w-1/2 order-2"></div>

                            <!-- Photo Grid for this Phase -->
                            <div class="pl-12 md:pl-0 w-full md:w-1/2 md:pl-12 order-3 {{ $loop->index % 2 == 0 ? 'md:order-3' : 'md:order-1 md:pr-12 md:pl-0' }}">
                                <div class="grid grid-cols-2 gap-3 bg-white p-3 rounded-xl shadow-sm border border-gray-100">
                                    @foreach($groupedPhotos[$phase] as $photo)
                                        <div class="relative group aspect-square rounded-lg overflow-hidden bg-gray-100 cursor-pointer" onclick="window.open('{{ $photo->photo_url }}', '_blank')">
                                            <img src="{{ $photo->photo_url }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" alt="{{ $photo->step }}">
                                            <div class="absolute inset-x-0 bottom-0 bg-black/60 p-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <div class="text-[10px] text-white font-mono truncate">{{ $photo->step }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    @endif
                @endforeach
                
            </div>
            
            <div class="mt-12 text-center">
                <div class="inline-flex items-center justify-center p-4 bg-gray-100 rounded-full">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <p class="mt-2 text-xs text-gray-400 uppercase tracking-widest font-bold">End of Gallery</p>
            </div>

        </div>
    </div>
</x-app-layout>
