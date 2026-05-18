<div class="min-h-screen bg-[#f8fafc] pb-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
        
        {{-- Breadcrumbs & Header --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
            <div class="space-y-2">
                <nav class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">
                    <a href="#" class="hover:text-teal-600 transition-colors">Finance</a>
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                    <span class="text-teal-600">Waiting Payment</span>
                </nav>
                <h1 class="text-4xl font-black text-[#1a3b34] tracking-tight">Menunggu Pembayaran</h1>
                <p class="text-sm font-medium text-gray-500 max-w-lg">Pantau SPK yang menunggu konfirmasi pembayaran dari pelanggan sebelum masuk ke logistik.</p>
            </div>

            <div class="flex items-center gap-3">
                <div class="relative group">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari SPK atau Nama..." 
                           class="pl-11 pr-5 py-3.5 text-xs border-transparent bg-white rounded-2xl focus:ring-2 focus:ring-teal-500/20 focus:bg-white shadow-sm w-72 transition-all font-bold text-gray-700 placeholder:text-gray-400 ring-1 ring-gray-100">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Metrics Row --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="bg-[#1a3b34] rounded-[2rem] p-8 text-white shadow-xl shadow-teal-900/10 relative overflow-hidden group">
                <div class="relative z-10">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-teal-300/80 mb-2">Total SPK Menunggu</p>
                    <div class="flex items-baseline gap-2">
                        <h2 class="text-4xl font-black tabular-nums">{{ number_format($orders->total()) }}</h2>
                        <span class="text-xs font-bold text-teal-400/80 flex items-center gap-1">Data</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            @forelse($orders as $order)
                <div class="bg-white rounded-[2rem] overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group flex flex-col sm:flex-row border border-gray-100">
                    
                    {{-- Visual Left Side --}}
                    <div class="w-full sm:w-[220px] bg-black relative flex items-center justify-center overflow-hidden min-h-[220px]">
                        @if($order->spk_cover_photo_url)
                            <img src="{{ $order->spk_cover_photo_url }}" class="absolute inset-0 w-full h-full object-cover opacity-80 group-hover:scale-110 transition-transform duration-700" alt="Item Photo">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        @else
                            <div class="absolute inset-0 bg-gradient-to-br from-gray-800 to-black opacity-60"></div>
                            <svg class="w-16 h-16 text-white/5 relative z-10" fill="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        @endif
                        
                        <div class="absolute top-4 left-4 bg-white/20 backdrop-blur-md px-3 py-1.5 rounded-lg border border-white/10">
                            <span class="text-[10px] font-black text-white uppercase tracking-widest">{{ $order->spk_number }}</span>
                        </div>
                    </div>

                    {{-- Content Right Side --}}
                    <div class="flex-1 p-8 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-1">
                                <h3 class="text-xl font-black text-[#1a3b34] leading-tight group-hover:text-teal-600 transition-colors uppercase">
                                    {{ $order->customer?->name ?? $order->customer_name ?? 'Guest' }}
                                </h3>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black tracking-tighter uppercase {{ $order->priority == 'Urgent' ? 'bg-red-50 text-red-600' : 'bg-blue-50 text-blue-600' }}">
                                    {{ $order->priority ?? 'Reguler' }}
                                </span>
                            </div>
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-6">
                                BRAND: <span class="text-gray-600 mr-3 truncate max-w-[120px] inline-block align-bottom">{{ $order->shoe_brand ?? '-' }}</span>
                                TIPE: <span class="text-gray-600">{{ $order->shoe_type ?? '-' }}</span>
                            </p>

                            <div class="space-y-2 mb-6">
                                <div class="flex justify-between items-center text-[9px] font-black uppercase tracking-widest mb-1.5">
                                    <span class="text-gray-400">Total Tagihan</span>
                                    <span class="text-[#22AF85]">Rp {{ number_format($order->total_transaksi, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center text-[9px] font-black uppercase tracking-widest mb-1.5">
                                    <span class="text-gray-400">Sisa Pembayaran</span>
                                    <span class="text-amber-500">Rp {{ number_format($order->sisa_tagihan, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Footer Actions --}}
                        <div class="flex items-center gap-2 mt-8">
                            <a href="{{ route('finance.show', $order->id) }}" wire:navigate
                               class="flex-1 bg-white border border-gray-100 hover:border-teal-100 hover:bg-teal-50 text-[#1a3b34] py-3.5 px-6 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] transition-all flex items-center justify-center gap-2 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                Detail
                            </a>
                            
                            <button wire:click="dispatchSpk({{ $order->id }})" wire:confirm="Anda yakin SPK ini sudah bisa dilanjutkan ke logistik untuk dispatch?"
                                    class="flex-1 bg-[#1a3b34] hover:bg-teal-800 text-white py-3.5 px-6 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] transition-all flex items-center justify-center gap-2 shadow-lg shadow-teal-900/10">
                                <svg wire:loading.remove wire:target="dispatchSpk({{ $order->id }})" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                <svg wire:loading wire:target="dispatchSpk({{ $order->id }})" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Lanjutkan
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="lg:col-span-2 py-32 flex flex-col items-center justify-center bg-white rounded-[2rem] border-2 border-dashed border-gray-100 italic">
                    <svg class="w-16 h-16 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Tidak ada SPK dengan status Waiting Payment</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-12">
            {{ $orders->links() }}
        </div>
    </div>
</div>
