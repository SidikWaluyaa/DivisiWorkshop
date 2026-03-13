<div>
    @if($lowStockMaterials && $lowStockMaterials->count() > 0)
    <div class="bg-gradient-to-br from-red-50 to-orange-50 rounded-2xl shadow-xl border border-red-100">
        <div class="bg-gradient-to-r from-red-500 to-orange-500 px-6 py-5 border-b border-red-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-black text-white tracking-tight">Stok Menipis</h3>
                            {{-- Info Tooltip --}}
                            <div x-data="{ open: false }" class="relative inline-block text-left">
                                <button @mouseenter="open = true" @mouseleave="open = false" class="text-red-200 hover:text-white transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                                <div x-show="open" x-cloak x-transition class="absolute z-50 w-80 max-w-none p-5 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-red-100 left-0 mt-3 whitespace-normal text-left">
                                    <div class="absolute -top-1.5 left-4 w-3 h-3 bg-white border-t border-l border-red-100 rotate-45"></div>
                                    <div class="relative">
                                        <div class="flex items-center gap-2 mb-2">
                                            <div class="w-1 h-4 bg-red-500 rounded-full"></div>
                                            <div class="text-[10px] font-black text-red-600 uppercase tracking-widest">Maksud</div>
                                        </div>
                                        <div class="text-[13px] text-gray-700 leading-relaxed mb-4 pl-3 font-medium">Pengingat otomatis untuk material workshop yang jumlah stoknya sudah berada di bawah batas minimum agar produksi tidak terhambat.</div>
                                        
                                        <div class="flex items-center gap-2 mb-2">
                                            <div class="w-1 h-4 bg-gray-400 rounded-full"></div>
                                            <div class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Sumber Data</div>
                                        </div>
                                        <div class="text-[12px] text-gray-500 leading-relaxed italic pl-3">Inventaris Gudang (Master Data Material vs Stok Berjalan).</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-[10px] text-red-100 font-bold uppercase tracking-widest leading-none">Material yang perlu restock</p>
                    </div>
                </div>
                <span class="px-3 py-1.5 bg-white/20 text-white rounded-lg text-xs font-bold backdrop-blur-sm">
                    {{ $lowStockMaterials->count() }} item
                </span>
            </div>
        </div>
        <div class="p-5">
            <div class="space-y-2">
                @foreach($lowStockMaterials as $material)
                <div class="flex items-center justify-between p-3 bg-white rounded-xl shadow-sm border border-red-100 hover:shadow-md transition-shadow">
                    <div>
                        <span class="font-bold text-gray-700 text-sm">{{ $material->name }}</span>
                        @if($material->stock <= 0)
                        <span class="ml-2 px-1.5 py-0.5 bg-red-600 text-white rounded text-[10px] font-black animate-pulse">HABIS</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-400">min: {{ $material->min_stock }}</span>
                        <span class="px-2.5 py-1 {{ $material->stock <= 0 ? 'bg-red-600 text-white' : 'bg-red-100 text-red-700' }} rounded-lg font-bold text-xs">
                            {{ $material->stock }} {{ $material->unit }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @else
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden p-8 text-center">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <p class="text-gray-500 text-sm font-bold">Semua stok material aman! ✅</p>
    </div>
    @endif
</div>
