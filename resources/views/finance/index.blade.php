<x-app-layout>
    <div class="min-h-screen bg-white">
        {{-- Premium Header --}}
        <div class="bg-white shadow-xl border-b border-gray-100 sticky top-0 z-30 backdrop-blur-md bg-white/90">
            <div class="max-w-7xl mx-auto px-6 py-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    {{-- Left: Icon & Title --}}
                    <div class="flex items-center gap-5">
                        <div class="p-3.5 bg-gradient-to-br from-[#22AF85] to-[#1E9873] rounded-2xl shadow-emerald-200/50 shadow-lg border border-[#22AF85]/20 transform transition-transform group-hover:rotate-3">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-black text-gray-900 tracking-tight leading-none italic">Finance Dashboard</h1>
                            <p class="text-gray-400 text-[10px] mt-1.5 font-black uppercase tracking-widest italic opacity-70">Pusat kontrol pembayaran & pelaporan keuangan</p>
                        </div>
                    </div>
                    
                    {{-- Right: Actions & Search --}}
                    <div class="flex items-center gap-4">
                        <a href="{{ route('finance.donations') }}" class="group relative inline-flex items-center gap-2.5 px-5 py-2.5 bg-gray-50 hover:bg-gray-100 text-gray-900 rounded-2xl font-bold shadow-sm border border-gray-200 transition-all hover:-translate-y-1">
                            <span class="absolute top-0 right-0 -mt-1 -mr-1 flex h-3.5 w-3.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#FFC232] opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-[#FFC232]"></span>
                            </span>
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            Data Donasi
                        </a>

                        <form action="{{ route('finance.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                            <input type="hidden" name="tab" value="{{ request('tab', 'waiting_dp') }}">
                            
                            {{-- Unified Search --}}
                            <div class="relative group">
                                <input type="text" 
                                       name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="Cari SPK / Nama / No HP..." 
                                       class="pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-[#22AF85]/10 focus:border-[#22AF85] text-gray-900 placeholder-gray-400 shadow-sm w-48 focus:w-64 transition-all duration-300">
                                <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2 group-focus-within:text-[#22AF85] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>

                            {{-- Date Filters --}}
                            <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-2xl px-3 py-1 text-sm shadow-sm group hover:border-[#22AF85]/30 transition-all">
                                <div class="flex items-center gap-2">
                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest italic pt-0.5">From</label>
                                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="bg-transparent border-none focus:ring-0 p-0 text-xs font-bold text-gray-700 w-28">
                                </div>
                                <div class="w-px h-4 bg-gray-200 mx-1"></div>
                                <div class="flex items-center gap-2">
                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest italic pt-0.5">To</label>
                                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="bg-transparent border-none focus:ring-0 p-0 text-xs font-bold text-gray-700 w-28">
                                </div>
                            </div>

                            <button type="submit" class="px-5 py-2.5 bg-[#FFC232] text-gray-900 rounded-2xl hover:bg-[#FFD666] transition-all shadow-md hover:shadow-lg font-bold">
                                Terapkan
                            </button>
                        </form>

                        {{-- Quick Date Filters --}}
                        <div class="flex items-center gap-2 bg-white/50 p-1 rounded-2xl border border-gray-100 shadow-inner">
                            @php
                                $today = now()->format('Y-m-d');
                                $startOfWeek = now()->startOfWeek()->format('Y-m-d');
                                $startOfMonth = now()->startOfMonth()->format('Y-m-d');
                            @endphp
                            <a href="{{ request()->fullUrlWithQuery(['date_from' => $today, 'date_to' => $today]) }}" 
                               class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ request('date_from') == $today && request('date_to') == $today ? 'bg-[#22AF85] text-white shadow-md' : 'text-gray-400 hover:bg-gray-100' }}">
                                Today
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['date_from' => $startOfWeek, 'date_to' => $today]) }}" 
                               class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ request('date_from') == $startOfWeek && request('date_to') == $today ? 'bg-[#22AF85] text-white shadow-md' : 'text-gray-400 hover:bg-gray-100' }}">
                                This Week
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['date_from' => $startOfMonth, 'date_to' => $today]) }}" 
                               class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ request('date_from') == $startOfMonth && request('date_to') == $today ? 'bg-[#22AF85] text-white shadow-md' : 'text-gray-400 hover:bg-gray-100' }}">
                                This Month
                            </a>
                            <a href="{{ route('finance.index', ['tab' => request('tab', 'waiting_dp')]) }}" 
                               class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest text-rose-400 hover:bg-rose-50 transition-all">
                                Clear
                            </a>
                        </div>

                        {{-- Excel Export Button --}}
                        <a href="{{ route('finance.export-excel', [
                                'tab' => request('tab', 'waiting_dp'), 
                                'search' => request('search'),
                                'date_from' => request('date_from'),
                                'date_to' => request('date_to')
                           ]) }}" 
                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#22AF85] hover:bg-[#1E9873] text-white rounded-2xl font-bold shadow-lg shadow-emerald-100 hover:shadow-emerald-200 transition-all hover:-translate-y-1">
                            <svg class="w-5 h-5 shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Volume Intelligence Bar (Summary Cards) --}}
        <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Card 1: Today's Intake --}}
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-xl shadow-gray-100/50 group hover:border-[#22AF85]/30 transition-all duration-500">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-emerald-50 rounded-2xl text-[#22AF85] group-hover:bg-[#22AF85] group-hover:text-white transition-all duration-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <span class="text-[10px] font-black text-gray-300 uppercase tracking-widest italic group-hover:text-[#22AF85]">Today</span>
                    </div>
                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 italic">Transaksi Baru</div>
                    <div class="flex items-baseline gap-2">
                        <div class="text-4xl font-black text-gray-900 tracking-tighter italic tabular-nums">{{ $stats['total_today'] ?? 0 }}</div>
                        <div class="text-xs font-bold text-gray-400">Unit</div>
                    </div>
                </div>

                {{-- Card 2: Pending DP --}}
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-xl shadow-gray-100/50 group hover:border-[#FFC232]/30 transition-all duration-500">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-amber-50 rounded-2xl text-[#FFC232] group-hover:bg-[#FFC232] group-hover:text-white transition-all duration-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-[10px] font-black text-gray-300 uppercase tracking-widest italic group-hover:text-[#FFC232]">Urgent</span>
                    </div>
                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 italic">Menunggu DP</div>
                    <div class="flex items-baseline gap-2">
                        <div class="text-4xl font-black text-gray-900 tracking-tighter italic tabular-nums">{{ $stats['pending_dp'] ?? 0 }}</div>
                        <div class="text-xs font-bold text-rose-400">🚨</div>
                    </div>
                </div>

                {{-- Card 3: Ready for Pickup --}}
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-xl shadow-gray-100/50 group hover:border-blue-300 transition-all duration-500">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-blue-50 rounded-2xl text-blue-500 group-hover:bg-blue-500 group-hover:text-white transition-all duration-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-[10px] font-black text-gray-300 uppercase tracking-widest italic group-hover:text-blue-500">Revenue</span>
                    </div>
                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 italic">Siap Pelunasan</div>
                    <div class="flex items-baseline gap-2">
                        <div class="text-4xl font-black text-gray-900 tracking-tighter italic tabular-nums">{{ $stats['ready_pickup'] ?? 0 }}</div>
                        <div class="text-xs font-bold text-blue-400">Ready</div>
                    </div>
                </div>

                {{-- Card 4: Daily Revenue --}}
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-3xl p-6 shadow-2xl shadow-gray-900/20 group hover:scale-[1.02] transition-all duration-500 overflow-hidden relative">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/5 rounded-full blur-2xl"></div>
                    <div class="flex items-center justify-between mb-4 relative z-10">
                        <div class="p-3 bg-white/10 rounded-2xl text-[#22AF85]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <span class="text-[10px] font-black text-white/30 uppercase tracking-widest italic">Live Cash</span>
                    </div>
                    <div class="text-[10px] font-black text-[#22AF85] uppercase tracking-widest mb-1 italic relative z-10">Pendapatan Hari Ini</div>
                    <div class="text-2xl font-black text-white tracking-tighter italic tabular-nums relative z-10">Rp {{ number_format($stats['revenue_today'] ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        {{-- Elite Tab Navigation --}}
        <div class="max-w-7xl mx-auto px-6 -mt-8 relative z-40">
            <div class="bg-white/80 backdrop-blur-xl rounded-[2rem] shadow-2xl p-2.5 flex gap-2.5 overflow-x-auto border border-white/20" x-data="{ activeTab: '{{ request('tab', 'waiting_dp') }}' }">

                <a href="{{ route('finance.index', ['tab' => 'waiting_dp']) }}" 
                   @click="activeTab = 'waiting_dp'"
                   :class="activeTab === 'waiting_dp' ? 'bg-[#22AF85] text-white shadow-xl shadow-emerald-200/50 scale-[1.02]' : 'bg-gray-50/50 text-gray-500 hover:bg-[#22AF85]/5'"
                   class="flex-1 min-w-[180px] px-6 py-4 rounded-[1.5rem] transition-all duration-300 font-bold text-center flex items-center justify-center gap-3 group border border-transparent">
                    <span class="text-2xl filter drop-shadow-sm group-hover:scale-125 transition-transform">🚨</span>
                    <div class="text-left">
                        <div class="text-[10px] uppercase tracking-widest opacity-70 font-black">Mendesak</div>
                        <div class="text-sm">Menunggu DP</div>
                    </div>
                </a>
                
                <a href="{{ route('finance.index', ['tab' => 'in_progress']) }}" 
                   @click="activeTab = 'in_progress'"
                   :class="activeTab === 'in_progress' ? 'bg-[#22AF85] text-white shadow-xl shadow-emerald-200/50 scale-[1.02]' : 'bg-gray-50/50 text-gray-500 hover:bg-[#22AF85]/5'"
                   class="flex-1 min-w-[180px] px-6 py-4 rounded-[1.5rem] transition-all duration-300 font-bold text-center flex items-center justify-center gap-3 group border border-transparent">
                    <span class="text-2xl filter drop-shadow-sm group-hover:scale-125 transition-transform">⚙️</span>
                    <div class="text-left">
                        <div class="text-[10px] uppercase tracking-widest opacity-70 font-black">Piutang</div>
                        <div class="text-sm">Dalam Proses</div>
                    </div>
                </a>
                
                <a href="{{ route('finance.index', ['tab' => 'ready_pickup']) }}" 
                   @click="activeTab = 'ready_pickup'"
                   :class="activeTab === 'ready_pickup' ? 'bg-[#22AF85] text-white shadow-xl shadow-emerald-200/50 scale-[1.02]' : 'bg-gray-50/50 text-gray-500 hover:bg-[#22AF85]/5'"
                   class="flex-1 min-w-[180px] px-6 py-4 rounded-[1.5rem] transition-all duration-300 font-bold text-center flex items-center justify-center gap-3 group border border-transparent">
                    <span class="text-2xl filter drop-shadow-sm group-hover:scale-125 transition-transform">✅</span>
                    <div class="text-left">
                        <div class="text-[10px] uppercase tracking-widest opacity-70 font-black">Siap Ambil</div>
                        <div class="text-sm">Pelunasan</div>
                    </div>
                </a>
                
                <a href="{{ route('finance.index', ['tab' => 'completed']) }}" 
                   @click="activeTab = 'completed'"
                   :class="activeTab === 'completed' ? 'bg-[#22AF85] text-white shadow-xl shadow-emerald-200/50 scale-[1.02]' : 'bg-gray-50/50 text-gray-500 hover:bg-[#22AF85]/5'"
                   class="flex-1 min-w-[180px] px-6 py-4 rounded-[1.5rem] transition-all duration-300 font-bold text-center flex items-center justify-center gap-3 group border border-transparent">
                    <span class="text-2xl filter drop-shadow-sm group-hover:scale-125 transition-transform">📜</span>
                    <div class="text-left">
                        <div class="text-[10px] uppercase tracking-widest opacity-70 font-black">Riwayat</div>
                        <div class="text-sm">Riwayat Lunas</div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="max-w-7xl mx-auto px-6 py-6">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                {{-- Elite Mobile Card View --}}
                <div class="block lg:hidden p-4 space-y-6 bg-gray-50/30">
                    @forelse ($orders as $order)
                        @php
                            $percent = $order->total_transaksi > 0 ? min(100, round(($order->total_paid / $order->total_transaksi) * 100)) : 0;
                            $statusColor = $percent >= 100 ? '#22AF85' : ($percent > 0 ? '#3B82F6' : '#FFC232');
                        @endphp
                        <div class="bg-white rounded-[2.5rem] shadow-2xl border border-gray-100 overflow-hidden hover:shadow-[#22AF85]/10 transition-all duration-500 group relative">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-gray-50 rounded-bl-[4rem] group-hover:bg-[#22AF85]/5 transition-colors duration-500"></div>
                            
                            <div class="p-8 border-b border-gray-50 relative z-10 flex justify-between items-center">
                                <div>
                                    <span class="text-[9px] font-black text-[#22AF85] uppercase tracking-[0.3em] block mb-1 italic">Protocol ID</span>
                                    <span class="text-2xl font-black text-gray-900 group-hover:tracking-wider transition-all leading-none tracking-tighter italic">{{ $order->spk_number }}</span>
                                </div>
                                <div class="px-4 py-2 bg-white rounded-2xl border border-gray-100 shadow-sm text-[9px] font-black text-gray-400 uppercase tracking-widest italic group-hover:border-[#22AF85]/30 transition-all">
                                    {{ $order->finance_entry_at ? $order->finance_entry_at->format('d M') : $order->created_at->format('d M') }}
                                </div>
                            </div>
                            
                            <div class="p-8 space-y-8 relative z-10">
                                <div class="flex items-center gap-5">
                                    <div class="w-16 h-16 rounded-3xl bg-gray-900 flex items-center justify-center text-3xl shadow-xl group-hover:rotate-6 transition-transform duration-500 border-2 border-white">
                                        👤
                                    </div>
                                    <div>
                                        <div class="text-[9px] uppercase font-black text-gray-400 tracking-[0.2em] mb-1 italic">Informasi Pelanggan</div>
                                        <div class="text-xl font-black text-gray-900 leading-tight italic">{{ $order->customer_name }}</div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-6">
                                    <div class="p-5 bg-gray-50/80 rounded-3xl border border-gray-100 shadow-inner group-hover:bg-white transition-colors">
                                        <div class="text-[9px] uppercase font-black text-gray-400 tracking-widest mb-3 italic">Division Status</div>
                                        <div class="inline-flex items-center gap-2.5 px-3 py-1.5 bg-white rounded-full border border-gray-100 shadow-sm">
                                            <span class="w-2 h-2 rounded-full {{ $order->status === \App\Enums\WorkOrderStatus::SELESAI ? 'bg-[#22AF85] shadow-[0_0_8px_rgba(34,175,133,0.5)]' : 'bg-blue-500 animate-pulse' }}"></span>
                                            <span class="text-[9px] font-black uppercase text-gray-700 tracking-tight leading-none">{{ str_replace('_', ' ', $order->status->value) }}</span>
                                        </div>
                                    </div>
                                    <div class="p-5 bg-gray-50/80 rounded-3xl border border-gray-100 text-right shadow-inner group-hover:bg-white transition-colors">
                                        <div class="text-[9px] uppercase font-black text-gray-400 tracking-widest mb-3 italic">Total Tagihan</div>
                                        <div class="text-lg font-black text-gray-900 tracking-tighter italic">Rp {{ number_format($order->total_transaksi, 0, ',', '.') }}</div>
                                    </div>
                                </div>

                                {{-- Elite Progress --}}
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full" style="background-color: {{ $statusColor }}"></span>
                                            <span class="text-[10px] font-black uppercase tracking-[0.2em] italic" style="color: {{ $statusColor }}">
                                                {{ $percent >= 100 ? 'PROTOCOL CLOSED' : ($percent > 0 ? 'DIBAYAR '.round($percent).'%' : 'WAITING DP') }}
                                            </span>
                                        </div>
                                        <span class="text-xs font-black text-gray-900 italic">Rp {{ number_format($order->total_paid, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="h-3 w-full bg-gray-100 rounded-full overflow-hidden shadow-inner p-[1.5px] border border-gray-50">
                                        <div class="h-full rounded-full transition-all duration-1000 ease-out shadow-sm relative overflow-hidden" 
                                             style="width: {{ $percent }}%; background-color: {{ $statusColor }}">
                                             <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Elite Actions --}}
                                <div class="flex gap-4 pt-4">
                                    <a href="{{ route('finance.show', $order->id) }}" 
                                       class="flex-1 bg-[#22AF85] text-white px-8 py-5 rounded-[1.5rem] font-black text-sm text-center shadow-2xl shadow-emerald-100 hover:shadow-emerald-200 transition-all active:scale-95 flex items-center justify-center gap-3 uppercase tracking-[0.2em] italic group/btn">
                                        Audit Detail
                                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7-7 7"></path></svg>
                                    </a>
                                    @can('manageFinance', $order)
                                    <form action="{{ route('finance.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Hapus data finance ini?');" class="shrink-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-16 h-16 bg-gray-50 text-rose-400 border border-gray-100 rounded-[1.5rem] hover:bg-rose-50 hover:text-rose-600 transition-all active:scale-90 flex items-center justify-center shadow-xl hover:shadow-rose-100">
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.859L4.764 7M16 17v-4m-4 4v-4m-4 4v-4m-6-6h14m2 0a2 2 0 002-2V7a2 2 0 00-2 2H3a2 2 0 00-2 2v.17c0 1.1.9 2 2 2h1M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"></path></svg>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-24 px-8 bg-white rounded-[3rem] border-2 border-dashed border-gray-100">
                            <div class="w-24 h-24 bg-gray-50 rounded-3xl flex items-center justify-center text-5xl mb-8 shadow-inner mx-auto grayscale opacity-50 border border-gray-100">🔍</div>
                            <h3 class="text-2xl font-black text-gray-900 mb-2 uppercase tracking-tighter italic">No Active Protocols</h3>
                            <p class="text-gray-400 text-xs font-black uppercase tracking-widest italic leading-relaxed">System scan complete. No financial records found for this sector.</p>
                        </div>
                    @endforelse
                </div>
            
                {{-- Elite Desktop Table View --}}
                <div class="hidden lg:block relative overflow-hidden bg-white/50 backdrop-blur-sm rounded-3xl">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-900/5 border-b border-gray-100">
                                <th class="px-8 py-7 text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] w-20 text-center italic">Ref</th>
                                <th class="px-8 py-7 text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] italic">Detail Protokol</th>
                                <th class="px-8 py-7 text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] text-center italic">Status Divisi</th>
                                <th class="px-8 py-7 text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] text-right italic">Total Transaksi</th>
                                <th class="px-8 py-7 text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] italic">Total Terbayar</th>
                                <th class="px-8 py-7 text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] text-right italic">Sisa Tagihan</th>
                                <th class="px-8 py-7 text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] text-center italic">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50/50">
                            @forelse ($orders as $order)
                                @php
                                    $percent = $order->total_transaksi > 0 ? min(100, round(($order->total_paid / $order->total_transaksi) * 100)) : 0;
                                    $statusColor = $percent >= 100 ? '#22AF85' : ($percent > 0 ? '#3B82F6' : '#FFC232');
                                @endphp
                                <tr class="hover:bg-gray-50/80 transition-all duration-500 group relative">
                                    <td class="px-8 py-8 text-center relative">
                                        <div class="absolute left-0 top-0 w-1 h-full bg-[#22AF85] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                        <span class="text-xs font-black text-gray-300 group-hover:text-[#22AF85] transition-colors leading-none tracking-tighter tabular-nums italic">
                                            {{ str_pad(($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                        </span>
                                    </td>
                                    
                                    <td class="px-8 py-8">
                                        <div class="flex flex-col gap-2">
                                            <div class="flex items-center gap-3">
                                                <span class="font-black text-gray-900 text-xl leading-none tracking-tighter italic group-hover:text-[#22AF85] group-hover:translate-x-1 transition-all">
                                                    {{ $order->spk_number }}
                                                </span>
                                                @if($order->cs_code)
                                                    <span class="text-[8px] text-gray-400 font-black bg-white px-2 py-0.5 rounded-lg uppercase tracking-[0.2em] border border-gray-100 shadow-sm leading-none group-hover:border-[#22AF85]/30 group-hover:text-[#22AF85] transition-all">
                                                        {{ $order->cs_code }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <div class="w-1.5 h-1.5 rounded-full bg-gray-200 group-hover:bg-[#22AF85] transition-colors"></div>
                                                <div class="font-black text-gray-500 group-hover:text-gray-900 transition-colors uppercase tracking-tight text-[11px] leading-none italic">{{ $order->customer_name }}</div>
                                            </div>
                                            <div class="text-[9px] text-gray-400 font-black uppercase tracking-widest flex items-center gap-1.5 italic opacity-60">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"/></svg>
                                                {{ $order->finance_entry_at ? $order->finance_entry_at->format('M d, Y - H:i') : $order->created_at->format('M d, Y - H:i') }}
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-8 py-8 text-center">
                                        <div class="inline-flex items-center gap-2.5 px-4 py-2.5 bg-white rounded-2xl border border-gray-100 shadow-sm transition-all group-hover:scale-105 group-hover:border-[#22AF85]/20 group-hover:shadow-md">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $order->status === \App\Enums\WorkOrderStatus::SELESAI ? 'bg-[#22AF85] shadow-[0_0_10px_rgba(34,175,133,0.5)]' : 'bg-blue-500 animate-pulse shadow-[0_0_10px_rgba(59,130,246,0.3)]' }}"></span>
                                            <span class="text-[9px] font-black uppercase text-gray-600 tracking-[0.15em] leading-none">{{ str_replace('_', ' ', $order->status->value) }}</span>
                                        </div>
                                    </td>

                                    <td class="px-8 py-8 text-right">
                                        <div class="font-black text-gray-900 text-lg tracking-tighter group-hover:text-[#22AF85] transition-colors italic">Rp {{ number_format($order->total_transaksi, 0, ',', '.') }}</div>
                                        @if($order->discount > 0)
                                            <div class="inline-flex items-center gap-1 px-2 py-0.5 bg-rose-50 text-rose-500 rounded-lg text-[8px] font-black mt-1.5 uppercase tracking-widest border border-rose-100 italic">
                                                Disc -Rp {{ number_format($order->discount, 0, ',', '.') }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-8 py-8 min-w-[240px]">
                                        <div class="space-y-3">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $statusColor }}"></span>
                                                    <span class="text-[9px] font-black uppercase tracking-widest italic" style="color: {{ $statusColor }}">
                                                        {{ $percent >= 100 ? 'SETTLED' : ($percent > 0 ? 'LIQUIDITY '.round($percent).'%' : 'WAITING DP') }}
                                                    </span>
                                                </div>
                                                <span class="text-[10px] font-black text-gray-400 group-hover:text-gray-900 transition-colors tabular-nums italic">Rp {{ number_format($order->total_paid, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="relative w-full bg-gray-100 rounded-full h-2.5 overflow-hidden shadow-inner p-[1.5px] border border-gray-100">
                                                <div class="h-full rounded-full transition-all duration-1000 ease-out shadow-sm relative overflow-hidden" 
                                                     style="width: {{ $percent }}%; background-color: {{ $statusColor }}">
                                                     <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-8 py-8 text-right">
                                        @if($order->sisa_tagihan > 0)
                                            <div class="flex flex-col items-end gap-1.5 group-hover:-translate-x-1 transition-transform">
                                                <span class="font-black text-[#FFC232] text-xl leading-none tracking-tighter italic">Rp {{ number_format($order->sisa_tagihan, 0, ',', '.') }}</span>
                                                <span class="text-[8px] font-black bg-[#FFC232]/5 text-[#FFC232] px-2.5 py-1 rounded-lg border border-[#FFC232]/20 uppercase tracking-[0.2em] shadow-sm italic leading-none">Unsettled</span>
                                            </div>
                                        @else
                                            <div class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 text-[#22AF85] rounded-2xl border border-emerald-100 shadow-sm group-hover:scale-110 group-hover:rotate-2 transition-all duration-500">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                                <span class="font-black text-[9px] uppercase tracking-[0.3em] leading-none italic">Closed</span>
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-8 py-8">
                                        <div class="flex items-center justify-center gap-4">
                                            <a href="{{ route('finance.show', $order->id) }}" 
                                               class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-[#FFC232] text-gray-900 shadow-2xl shadow-amber-100 hover:shadow-amber-200 hover:scale-110 active:scale-95 transition-all duration-500 overflow-hidden relative group/btn">
                                                <div class="absolute inset-0 bg-white/20 translate-y-full group-hover/btn:translate-y-0 transition-transform duration-500"></div>
                                                <svg class="w-5 h-5 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7-7 7"></path></svg>
                                            </a>

                                            @can('manageFinance', $order)
                                            <form action="{{ route('finance.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Hapus data finance ini?');" class="shrink-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-white border border-gray-100 text-rose-300 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-100 shadow-lg shadow-gray-100 hover:shadow-rose-100 hover:scale-110 active:scale-90 transition-all duration-500">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-2.132-1.859L4.764 7M16 17v-4m-4 4v-4m-4 4v-4m-6-6h14m2 0a2 2 0 002-2V7a2 2 0 00-2 2H3a2 2 0 00-2 2v.17c0 1.1.9 2 2 2h1M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"></path></svg>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-8 py-40 text-center relative overflow-hidden">
                                        <div class="absolute -top-12 -left-12 w-64 h-64 bg-gray-50 rounded-full blur-3xl opacity-50"></div>
                                        <div class="relative z-10 flex flex-col items-center justify-center translate-y-2">
                                            <div class="w-28 h-28 bg-white rounded-[3rem] shadow-2xl border border-gray-50 flex items-center justify-center text-5xl mb-8 group hover:rotate-12 transition-transform duration-1000">
                                                📂
                                            </div>
                                            <span class="font-black text-gray-900 text-3xl uppercase tracking-tighter italic">Sector Empty</span>
                                            <p class="text-gray-400 text-xs mt-4 max-w-xs font-black uppercase tracking-widest leading-loose italic opacity-60">No financial transactions detected within this operational parameter.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if(isset($orders) && $orders instanceof \Illuminate\Pagination\LengthAwarePaginator && $orders->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $orders->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

</x-app-layout>
