<div class="min-h-screen bg-[#f8fafc]">
    {{-- High-End Header Section --}}
    <div class="bg-white border-b border-gray-200/60 pb-8 pt-6">
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <div class="px-3 py-1 rounded-full bg-teal-50 text-teal-600 text-[10px] font-black uppercase tracking-widest border border-teal-100 flex items-center gap-2">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-teal-500"></span>
                            </span>
                            Live Delivery Stream
                        </div>
                        <div class="px-3 py-1 rounded-full bg-pink-50 text-pink-600 text-[10px] font-black uppercase tracking-widest border border-pink-100">
                            CX Division
                        </div>
                    </div>
                    <h1 class="text-4xl font-black text-gray-900 tracking-tight flex items-center gap-3">
                        Monitoring Pengiriman
                        <span class="text-gray-300 font-light text-2xl">/</span>
                        <span class="text-gray-400 font-medium text-lg">Verified Only</span>
                    </h1>
                    <p class="text-gray-500 font-medium">Pantau status pengiriman SPK yang telah diverifikasi secara real-time.</p>
                </div>

                {{-- Stats Floating Cards --}}
                <div class="flex gap-4">
                    <button wire:click="filterByPreset('today')" class="relative group outline-none">
                        <div class="absolute -inset-1 bg-gradient-to-r from-teal-500 to-emerald-500 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-1000"></div>
                        <div class="relative bg-white px-6 py-4 rounded-2xl border {{ $date_start == date('Y-m-d') && $date_end == date('Y-m-d') ? 'border-teal-500 ring-2 ring-teal-50' : 'border-gray-100' }} flex items-center gap-4 min-w-[180px] transition-all">
                            <div class="w-12 h-12 rounded-xl bg-teal-50 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">📦</div>
                            <div class="text-left">
                                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Hari Ini</div>
                                <div class="text-2xl font-black text-gray-900">{{ $stats['today'] }}</div>
                            </div>
                        </div>
                    </button>
                    <button wire:click="filterByPreset('week')" class="relative group outline-none">
                        <div class="absolute -inset-1 bg-gradient-to-r from-pink-500 to-orange-500 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-1000"></div>
                        <div class="relative bg-white px-6 py-4 rounded-2xl border {{ $date_start == \Carbon\Carbon::today()->startOfWeek()->toDateString() ? 'border-pink-500 ring-2 ring-pink-50' : 'border-gray-100' }} flex items-center gap-4 min-w-[180px] transition-all">
                            <div class="w-12 h-12 rounded-xl bg-pink-50 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">📅</div>
                            <div class="text-left">
                                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Minggu Ini</div>
                                <div class="text-2xl font-black text-gray-900">{{ $stats['this_week'] }}</div>
                            </div>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Advanced Filter Bar --}}
        <div class="bg-white/70 backdrop-blur-md border border-white/20 shadow-xl shadow-gray-200/40 rounded-[2.5rem] p-3 mb-10 sticky top-4 z-40">
            <div class="flex flex-col lg:flex-row items-center gap-3">
                <!-- Search Input -->
                <div class="relative flex-1 w-full">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text" 
                        class="w-full pl-12 pr-4 py-4 bg-gray-50/50 border-none rounded-[2rem] focus:ring-2 focus:ring-teal-500 transition-all font-bold text-sm text-gray-700 placeholder-gray-400" 
                        placeholder="Cari Nama Pelanggan, Nomor SPK, atau No. Resi...">
                </div>

                <div class="flex items-center gap-3 w-full lg:w-auto">
                    <!-- Category Select -->
                    <div class="relative w-full lg:w-48">
                        <select wire:model.live="category" class="w-full pl-5 pr-10 py-4 bg-gray-50/50 border-none rounded-[2rem] focus:ring-2 focus:ring-teal-500 font-bold text-sm text-gray-600 appearance-none cursor-pointer">
                            <option value="">Semua Kurir</option>
                            <option value="Ojek Online">🛵 Ojek Online</option>
                            <option value="Ekspedisi">🚚 Ekspedisi</option>
                            <option value="Ambil Sendiri">🏠 Ambil Sendiri</option>
                        </select>
                    </div>

                    <!-- Date Picker Range -->
                    <div class="flex items-center bg-gray-50/50 rounded-[2rem] px-4 py-2 gap-2 border border-transparent focus-within:border-teal-500 transition-all">
                        <input wire:model.live="date_start" type="date" class="bg-transparent border-none focus:ring-0 text-xs font-black text-gray-600">
                        <span class="text-gray-300 font-bold">TO</span>
                        <input wire:model.live="date_end" type="date" class="bg-transparent border-none focus:ring-0 text-xs font-black text-gray-600">
                    </div>
                </div>
            </div>
        </div>

        {{-- Grouped Content Grid --}}
        <div class="space-y-12">
            @forelse($groupedShippings as $date => $items)
                <div class="space-y-6">
                    {{-- Date Separator --}}
                    <div class="flex items-center gap-4">
                        <div class="px-6 py-2 bg-gray-900 text-white rounded-full text-xs font-black uppercase tracking-[0.2em] shadow-lg shadow-gray-200">
                            @if($date == date('Y-m-d'))
                                Hari Ini
                            @elseif($date == date('Y-m-d', strtotime('-1 day')))
                                Kemarin
                            @else
                                {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}
                            @endif
                        </div>
                        <div class="h-px flex-1 bg-gradient-to-r from-gray-200 to-transparent"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-8">
                        @foreach($items as $shipping)
                            <div class="group relative bg-white rounded-[3rem] border border-gray-100 shadow-sm hover:shadow-2xl hover:shadow-teal-500/10 transition-all duration-500 overflow-hidden flex flex-col h-full">
                                
                                {{-- Status Badge Overlay --}}
                                <div class="absolute top-6 right-6 z-10">
                                    <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-tighter shadow-sm
                                        {{ $shipping->kategori_pengiriman == 'Ekspedisi' ? 'bg-teal-500 text-white' : 
                                           ($shipping->kategori_pengiriman == 'Ojek Online' ? 'bg-pink-500 text-white' : 'bg-orange-500 text-white') }}">
                                        {{ $shipping->kategori_pengiriman }}
                                    </span>
                                </div>

                                <div class="p-8 flex flex-col h-full">
                                    <div class="mb-6">
                                        <span class="inline-block px-3 py-1 bg-gray-100 rounded-lg text-[10px] font-black text-gray-400 tracking-widest mb-3">
                                            SPK: {{ $shipping->spk_number }}
                                        </span>
                                        <h3 class="text-2xl font-black text-gray-900 group-hover:text-teal-600 transition-colors leading-tight line-clamp-1">
                                            {{ $shipping->customer_name }}
                                        </h3>
                                        <p class="text-sm font-bold text-teal-500 mt-1">{{ $shipping->customer_phone }}</p>
                                    </div>

                                    <div class="space-y-5 flex-1">
                                        {{-- Shoe Info --}}
                                        @if($shipping->workOrder)
                                            <div class="bg-gray-50/80 rounded-[2rem] p-4 border border-gray-100/50">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                                                        👟
                                                    </div>
                                                    <div class="overflow-hidden">
                                                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">Produk</div>
                                                        <div class="text-sm font-bold text-gray-800 truncate">
                                                            {{ $shipping->workOrder->shoe_brand }}
                                                        </div>
                                                        <div class="text-[11px] text-gray-500 font-medium">Color: {{ $shipping->workOrder->shoe_color }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Resi Display --}}
                                        @if($shipping->resi_pengiriman)
                                            <div class="bg-teal-50/30 border border-teal-100/50 rounded-[2rem] p-5 flex items-center justify-between group/resi">
                                                <div class="overflow-hidden">
                                                    <div class="text-[10px] font-black text-teal-600 uppercase tracking-widest mb-1">Nomor Resi</div>
                                                    <div class="text-lg font-black text-gray-900 tracking-wider truncate mr-2">{{ $shipping->resi_pengiriman }}</div>
                                                </div>
                                                <button onclick="copyToClipboard('{{ $shipping->resi_pengiriman }}', this)" 
                                                    class="p-3 bg-white text-teal-600 rounded-2xl shadow-sm hover:bg-teal-600 hover:text-white transition-all active:scale-90 flex-shrink-0">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                                </button>
                                            </div>
                                        @else
                                            <div class="bg-gray-50/50 border border-dashed border-gray-200 rounded-[2rem] p-6 text-center">
                                                <span class="text-xs font-bold text-gray-400 italic">Resi Belum Diinput Gudang</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mt-8 pt-6 border-t border-gray-50 flex items-center justify-between">
                                        <div>
                                            <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Tgl Terkirim</div>
                                            <div class="text-sm font-black text-gray-800">{{ $shipping->tanggal_pengiriman?->format('d M Y') ?: '-' }}</div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="text-right">
                                                <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none">PIC</div>
                                                <div class="text-[11px] font-bold text-gray-700">{{ $shipping->pic ?: 'Gudang' }}</div>
                                            </div>
                                            <div class="w-10 h-10 rounded-full bg-teal-50 border-2 border-white shadow-sm flex items-center justify-center font-black text-teal-600 text-xs uppercase">
                                                {{ substr($shipping->pic ?: 'G', 0, 1) }}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Action Button --}}
                                    <div class="mt-6">
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $shipping->customer_phone) }}?text=Halo%20{{ urlencode($shipping->customer_name) }},%20sepatu%20dengan%20SPK%20{{ $shipping->spk_number }}%20sudah%20kami%20kirimkan%20ya.%0ANomor%20Resi:%20{{ $shipping->resi_pengiriman }}" 
                                           target="_blank"
                                           class="group/wa relative flex items-center justify-center gap-3 w-full py-4 bg-gray-900 text-white rounded-[2rem] font-black text-sm hover:bg-teal-600 transition-all duration-500 overflow-hidden shadow-lg shadow-gray-200 group-hover:shadow-teal-500/20">
                                            <span class="absolute inset-0 bg-gradient-to-r from-teal-400 to-teal-600 translate-y-full group-hover/wa:translate-y-0 transition-transform duration-500"></span>
                                            <span class="relative flex items-center gap-3">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.414 0 0 5.414 0 12.05c0 2.123.552 4.197 1.597 6.02L0 24l6.137-1.61a11.786 11.786 0 005.913 1.586h.005c6.637 0 12.05-5.414 12.05-12.05 0-3.21-1.248-6.228-3.511-8.491z"/></svg>
                                            Infokan Pelanggan
                                        </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="col-span-full py-32 bg-white rounded-[4rem] border border-dashed border-gray-200 text-center">
                    <div class="flex flex-col items-center justify-center space-y-6">
                        <div class="w-32 h-32 bg-teal-50 rounded-full flex items-center justify-center text-6xl animate-bounce">
                            🔎
                        </div>
                        <div class="space-y-2">
                            <h3 class="text-2xl font-black text-gray-900">Data Tidak Ditemukan</h3>
                            <p class="text-gray-500 font-medium max-w-xs mx-auto">Kami tidak dapat menemukan data pengiriman yang Anda cari. Coba ubah filter atau kata kunci.</p>
                        </div>
                        <button wire:click="filterByPreset('all')" class="px-8 py-3 bg-gray-900 text-white rounded-full font-black text-sm hover:bg-teal-600 transition-all shadow-lg active:scale-95">
                            Lihat Semua Data
                        </button>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Sophisticated Pagination --}}
        <div class="mt-16">
            {{ $shippings->links() }}
        </div>
    </div>

    <script>
        function copyToClipboard(text, btn) {
            navigator.clipboard.writeText(text).then(() => {
                const original = btn.innerHTML;
                btn.innerHTML = '<svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                btn.classList.add('bg-teal-600', 'text-white');
                setTimeout(() => {
                    btn.innerHTML = original;
                    btn.classList.remove('bg-teal-600', 'text-white');
                }, 2000);
            });
        }
    </script>

    <style>
        input[type="date"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
            filter: invert(0.5);
        }
        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</div>
