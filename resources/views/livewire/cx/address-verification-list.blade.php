<div class="min-h-screen bg-[#f8fafc]">
    {{-- High-End Header Section --}}
    <div class="bg-white border-b border-gray-200/60 pb-8 pt-6">
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <div class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-widest border border-emerald-100 flex items-center gap-2">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            Address Verification Hub
                        </div>
                        <div class="px-3 py-1 rounded-full bg-teal-50 text-teal-600 text-[10px] font-black uppercase tracking-widest border border-teal-100">
                            CX Division
                        </div>
                    </div>
                    <h1 class="text-4xl font-black text-gray-900 tracking-tight flex items-center gap-3">
                        Alamat Terverifikasi
                        <span class="text-gray-300 font-light text-2xl">/</span>
                        <span class="text-gray-400 font-medium text-lg">Verified Only</span>
                    </h1>
                    <p class="text-gray-500 font-medium">Daftar lengkap konsumen yang telah memverifikasi alamat pengiriman mereka secara online.</p>
                </div>

                {{-- Stats Floating Cards --}}
                <div class="flex gap-4">
                    <button wire:click="filterByPreset('today')" class="relative group outline-none">
                        <div class="absolute -inset-1 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-1000"></div>
                        <div class="relative bg-white px-6 py-4 rounded-2xl border {{ $date_start == date('Y-m-d') && $date_end == date('Y-m-d') ? 'border-emerald-500 ring-2 ring-emerald-50' : 'border-gray-100' }} flex items-center gap-4 min-w-[180px] transition-all">
                            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">✓</div>
                            <div class="text-left">
                                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Hari Ini</div>
                                <div class="text-2xl font-black text-gray-900">{{ $stats['today'] }}</div>
                            </div>
                        </div>
                    </button>
                    <button wire:click="filterByPreset('week')" class="relative group outline-none">
                        <div class="absolute -inset-1 bg-gradient-to-r from-teal-500 to-blue-500 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-1000"></div>
                        <div class="relative bg-white px-6 py-4 rounded-2xl border {{ $date_start == \Carbon\Carbon::today()->startOfWeek()->toDateString() ? 'border-teal-500 ring-2 ring-teal-50' : 'border-gray-100' }} flex items-center gap-4 min-w-[180px] transition-all">
                            <div class="w-12 h-12 rounded-xl bg-teal-50 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">📅</div>
                            <div class="text-left">
                                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Minggu Ini</div>
                                <div class="text-2xl font-black text-gray-900">{{ $stats['this_week'] }}</div>
                            </div>
                        </div>
                    </button>
                    <button wire:click="filterByPreset('all')" class="relative group outline-none">
                        <div class="absolute -inset-1 bg-gradient-to-r from-purple-500 to-pink-500 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-1000"></div>
                        <div class="relative bg-white px-6 py-4 rounded-2xl border {{ empty($date_start) && empty($date_end) ? 'border-purple-500 ring-2 ring-purple-50' : 'border-gray-100' }} flex items-center gap-4 min-w-[180px] transition-all">
                            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">👥</div>
                            <div class="text-left">
                                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Data</div>
                                <div class="text-2xl font-black text-gray-900">{{ $stats['total'] }}</div>
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
                        class="w-full pl-12 pr-4 py-4 bg-gray-50/50 border-none rounded-[2rem] focus:ring-2 focus:ring-emerald-500 transition-all font-bold text-sm text-gray-700 placeholder-gray-400" 
                        placeholder="Cari Nama Pelanggan atau Nomor Telepon...">
                </div>

                <div class="flex items-center gap-3 w-full lg:w-auto">
                    <!-- Date Picker Range -->
                    <div class="flex items-center bg-gray-50/50 rounded-[2rem] px-5 py-3.5 gap-2 border border-transparent focus-within:border-emerald-500 transition-all">
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mr-1">Filter Tanggal</span>
                        <input wire:model.live="date_start" type="date" class="bg-transparent border-none focus:ring-0 text-xs font-black text-gray-600 p-0">
                        <span class="text-gray-300 font-bold text-xs">s/d</span>
                        <input wire:model.live="date_end" type="date" class="bg-transparent border-none focus:ring-0 text-xs font-black text-gray-600 p-0">
                    </div>
                </div>
            </div>
        </div>

        {{-- Grouped Content Grid --}}
        <div class="space-y-12">
            @forelse($groupedCustomers as $date => $items)
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
                        <div class="h-0.5 flex-1 bg-gray-200/50 rounded-full"></div>
                    </div>

                    {{-- Customer Cards Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($items as $customer)
                            <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm hover:shadow-xl hover:border-emerald-200 transition-all duration-300 flex flex-col justify-between relative overflow-hidden group">
                                {{-- Top Accent Line --}}
                                <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-500 to-teal-500 opacity-80"></div>

                                <div>
                                    {{-- Customer Meta Info --}}
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <h3 class="text-xl font-black text-gray-900 tracking-tight leading-none mb-1.5">{{ $customer->name }}</h3>
                                            <p class="text-xs font-bold text-gray-400 font-mono tracking-tight">{{ $customer->phone }}</p>
                                        </div>
                                        <span class="px-2.5 py-1 bg-emerald-50 border border-emerald-100 text-emerald-700 text-[9px] font-black rounded-full uppercase tracking-wider">
                                            Verified Address
                                        </span>
                                    </div>

                                    {{-- Address Details --}}
                                    <div class="bg-slate-50/70 border border-slate-100/50 rounded-2xl p-4 mb-5 space-y-2">
                                        <div class="flex items-start gap-2.5">
                                            <div class="w-5 h-5 bg-emerald-50 rounded-full flex items-center justify-center shrink-0 text-emerald-600 font-bold text-xs mt-0.5">📍</div>
                                            <div class="space-y-0.5">
                                                <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Detail Alamat</p>
                                                <p class="text-sm font-bold text-gray-700 leading-snug">{{ $customer->address }}</p>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-y-2 pt-2 border-t border-slate-200/40 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                            <div>
                                                <span class="block text-[8px] font-bold text-gray-400 uppercase tracking-widest">Desa/Kel</span>
                                                <span class="text-gray-700">{{ $customer->village ?? '-' }}</span>
                                            </div>
                                            <div>
                                                <span class="block text-[8px] font-bold text-gray-400 uppercase tracking-widest">Kecamatan</span>
                                                <span class="text-gray-700">{{ $customer->district ?? '-' }}</span>
                                            </div>
                                            <div>
                                                <span class="block text-[8px] font-bold text-gray-400 uppercase tracking-widest">Kota/Kab</span>
                                                <span class="text-gray-700">{{ $customer->city ?? '-' }}</span>
                                            </div>
                                            <div>
                                                <span class="block text-[8px] font-bold text-gray-400 uppercase tracking-widest">Provinsi</span>
                                                <span class="text-gray-700">{{ $customer->province ?? '-' }}</span>
                                            </div>
                                        </div>
                                        @if($customer->postal_code)
                                            <div class="pt-1.5 border-t border-slate-200/40 flex items-center justify-between text-xs font-bold text-gray-500 uppercase">
                                                <span class="text-[8px] font-bold text-gray-400 tracking-widest">Kode Pos</span>
                                                <span class="px-2 py-0.5 bg-gray-100 rounded text-gray-700 text-[10px] font-black font-mono">{{ $customer->postal_code }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Active Work Orders Section --}}
                                <div>
                                    <div class="pt-4 border-t border-gray-100">
                                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 flex items-center gap-1.5">
                                            <span>🥿</span> SPK Aktif Pelanggan
                                        </h4>
                                        
                                        <div class="space-y-3 max-h-48 overflow-y-auto pr-1">
                                            @forelse($customer->workOrders as $order)
                                                <div class="bg-gray-50/50 hover:bg-slate-50 border border-gray-100 rounded-2xl p-3.5 transition-all flex items-center justify-between gap-3">
                                                    <div class="space-y-1">
                                                        <div class="flex items-center gap-1.5">
                                                            <span class="text-xs font-black text-gray-900 tracking-tight">{{ $order->spk_number }}</span>
                                                            <span class="px-2 py-0.5 bg-amber-50 border border-amber-100 text-amber-700 text-[8px] font-black rounded uppercase tracking-wider">
                                                                {{ is_object($order->status) ? $order->status->label() : $order->status }}
                                                            </span>
                                                        </div>
                                                        <p class="text-[10px] font-bold text-gray-500 uppercase">
                                                            {{ $order->shoe_brand }} ({{ $order->shoe_color ?? '-' }})
                                                        </p>
                                                    </div>

                                                    {{-- Shipping Label Print Button --}}
                                                    <a href="{{ route('admin.orders.shipping-label', $order->id) }}" target="_blank" 
                                                        class="px-3.5 py-2 bg-teal-500 hover:bg-teal-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-md shadow-teal-100 flex items-center gap-1 shrink-0">
                                                        🖨️ Cetak Label
                                                    </a>
                                                </div>
                                            @empty
                                                <div class="py-3 text-center bg-gray-50 border border-dashed border-gray-200 rounded-2xl">
                                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Tidak ada pesanan aktif</p>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                    
                                    {{-- Verification timestamp --}}
                                    <div class="mt-4 pt-3 border-t border-gray-100/50 flex justify-between items-center text-[9px] font-bold text-gray-400 uppercase tracking-wider font-mono">
                                        <span>Diverifikasi Pada:</span>
                                        <span>{{ $customer->address_verified_at?->format('d M H:i') ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                {{-- Empty State --}}
                <div class="text-center py-20 bg-white border border-gray-100 rounded-[2.5rem] shadow-sm max-w-md mx-auto px-6">
                    <div class="text-6xl mb-6">🔍</div>
                    <h3 class="text-2xl font-black text-gray-900 tracking-tight mb-2">Tidak Ada Data</h3>
                    <p class="text-gray-500 font-medium mb-6">Tidak ada alamat terverifikasi yang cocok dengan filter pencarian atau rentang tanggal Anda.</p>
                    <button wire:click="filterByPreset('all')" class="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-black text-xs uppercase tracking-widest rounded-full shadow-lg shadow-emerald-100 transition-all">
                        Reset Filter
                    </button>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-12">
            {{ $customers->links() }}
        </div>
    </div>
</div>
