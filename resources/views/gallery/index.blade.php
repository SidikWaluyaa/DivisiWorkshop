<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm shadow-sm border border-white/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            
            <div class="flex flex-col">
                <h2 class="font-bold text-xl leading-tight tracking-wide">
                    {{ __('Galeri Dokumentasi') }}
                </h2>
                <div class="text-xs font-medium opacity-90">
                    Arsip Foto Pengerjaan
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Search & Filter -->
            <div class="mb-8">
                <form action="{{ route('gallery.index') }}" method="GET" class="relative max-w-lg mx-auto md:mx-0">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 sm:text-sm shadow-sm transition-shadow" 
                           placeholder="Cari No SPK / Nama Customer / Sepatu...">
                </form>
            </div>

            @if($orders->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($orders as $order)
                        <a href="{{ route('gallery.show', $order->id) }}" class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:border-teal-200 transition-all duration-300 transform hover:-translate-y-1">
                            <!-- Thumbnail Cover -->
                            <div class="aspect-square bg-gray-100 relative overflow-hidden">
                                @if($order->photos->isNotEmpty())
                                    <img src="{{ Storage::url($order->photos->first()->file_path) }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" alt="Cover">
                                @else
                                    <div class="flex items-center justify-center h-full text-gray-300">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                @endif
                                
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4 translate-y-2 group-hover:translate-y-0 transition-transform">
                                    <span class="text-white text-xs font-bold bg-teal-500 px-2 py-0.5 rounded-md shadow-sm">
                                        {{ $order->photos->count() }} Foto
                                    </span>
                                </div>
                            </div>

                            <div class="p-4">
                                <div class="text-xs text-gray-400 font-mono mb-1">{{ $order->spk_number }}</div>
                                <h3 class="font-bold text-gray-800 text-sm mb-1 truncate group-hover:text-teal-600 transition-colors">{{ $order->customer_name }}</h3>
                                <p class="text-xs text-gray-500 truncate">{{ $order->shoe_brand }} - {{ $order->shoe_color }}</p>
                                <div class="mt-3 flex flex-wrap gap-1">
                                    @foreach($order->services->take(2) as $s)
                                        <span class="text-[10px] bg-gray-50 border border-gray-100 px-1.5 py-0.5 rounded text-gray-600">{{ $s->name }}</span>
                                    @endforeach
                                    @if($order->services->count() > 2)
                                        <span class="text-[10px] text-gray-400">+{{ $order->services->count() - 2 }}</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="text-center py-16 bg-white rounded-xl border border-dashed border-gray-300">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada foto ditemukan</h3>
                    <p class="mt-1 text-sm text-gray-500">Coba cari dengan kata kunci lain atau belum ada foto yang diupload.</p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
