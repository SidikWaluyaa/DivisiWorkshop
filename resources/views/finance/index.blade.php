<x-app-layout>
    <div class="min-h-screen bg-white">
        {{-- Premium Header --}}
        <div class="bg-white shadow-lg border-b-2 border-gray-100">
            <div class="max-w-7xl mx-auto px-6 py-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    {{-- Left: Icon & Title --}}
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-[#22AF85]/10 backdrop-blur-sm rounded-2xl shadow-lg border-2 border-[#22AF85]/30">
                            <svg class="w-8 h-8 text-[#22AF85]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Finance Dashboard</h1>
                            <p class="text-gray-600 text-sm mt-1">Kelola pembayaran & tagihan customer</p>
                        </div>
                    </div>
                    
                    {{-- Right: Actions & Search --}}
                    <div class="flex items-center gap-3">
                        <a href="{{ route('finance.donations') }}" class="group relative inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-900 rounded-xl font-bold shadow-lg transition-all hover:-translate-y-1">
                            <span class="absolute top-0 right-0 -mt-1 -mr-1 flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#FFC232] opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-[#FFC232]"></span>
                            </span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            Data Donasi
                        </a>

                        <form action="{{ route('finance.index') }}" method="GET" class="flex gap-2">
                            <input type="hidden" name="tab" value="{{ request('tab', 'waiting_dp') }}">
                            <div class="relative">
                                <input type="text" 
                                       name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="Cari SPK / Customer..." 
                                       class="pl-10 pr-4 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#22AF85] focus:border-[#22AF85] text-gray-900 placeholder-gray-400 shadow-lg w-48 focus:w-64 transition-all">
                                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <button type="submit" class="px-4 py-2 bg-[#FFC232] text-gray-900 rounded-xl hover:bg-[#FFD666] transition-all shadow-lg font-bold">
                                Cari
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modern Tab Navigation --}}
        <div class="max-w-7xl mx-auto px-6 -mt-4">
            <div class="bg-white rounded-2xl shadow-xl p-2 flex gap-2 overflow-x-auto" x-data="{ activeTab: '{{ request('tab', 'waiting_dp') }}' }">

                <a href="{{ route('finance.index', ['tab' => 'waiting_dp']) }}" 
                   @click="activeTab = 'waiting_dp'"
                   :class="activeTab === 'waiting_dp' ? 'bg-[#22AF85] text-white shadow-lg scale-105' : 'bg-gray-50 text-gray-600 hover:bg-[#FFC232]/20'"
                   class="flex-1 min-w-[200px] px-6 py-4 rounded-xl transition-all duration-200 font-bold text-center flex items-center justify-center gap-2 group">
                    <span class="text-2xl">🚨</span>
                    <div class="text-left">
                        <div class="text-xs opacity-75">Urgent</div>
                        <div class="text-sm">Menunggu DP</div>
                    </div>
                </a>
                
                <a href="{{ route('finance.index', ['tab' => 'in_progress']) }}" 
                   @click="activeTab = 'in_progress'"
                   :class="activeTab === 'in_progress' ? 'bg-[#22AF85] text-white shadow-lg scale-105' : 'bg-gray-50 text-gray-600 hover:bg-[#FFC232]/20'"
                   class="flex-1 min-w-[200px] px-6 py-4 rounded-xl transition-all duration-200 font-bold text-center flex items-center justify-center gap-2 group">
                    <span class="text-2xl">⚙️</span>
                    <div class="text-left">
                        <div class="text-xs opacity-75">Piutang</div>
                        <div class="text-sm">Dalam Proses</div>
                    </div>
                </a>
                
                <a href="{{ route('finance.index', ['tab' => 'ready_pickup']) }}" 
                   @click="activeTab = 'ready_pickup'"
                   :class="activeTab === 'ready_pickup' ? 'bg-[#22AF85] text-white shadow-lg scale-105' : 'bg-gray-50 text-gray-600 hover:bg-[#FFC232]/20'"
                   class="flex-1 min-w-[200px] px-6 py-4 rounded-xl transition-all duration-200 font-bold text-center flex items-center justify-center gap-2 group">
                    <span class="text-2xl">✅</span>
                    <div class="text-left">
                        <div class="text-xs opacity-75">Siap Ambil</div>
                        <div class="text-sm">Pelunasan</div>
                    </div>
                </a>
                
                <a href="{{ route('finance.index', ['tab' => 'completed']) }}" 
                   @click="activeTab = 'completed'"
                   :class="activeTab === 'completed' ? 'bg-[#22AF85] text-white shadow-lg scale-105' : 'bg-gray-50 text-gray-600 hover:bg-[#FFC232]/20'"
                   class="flex-1 min-w-[200px] px-6 py-4 rounded-xl transition-all duration-200 font-bold text-center flex items-center justify-center gap-2 group">
                    <span class="text-2xl">📜</span>
                    <div class="text-left">
                        <div class="text-xs opacity-75">History</div>
                        <div class="text-sm">Riwayat Lunas</div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="max-w-7xl mx-auto px-6 py-6">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                {{-- Mobile Card View --}}
                <div class="block lg:hidden grid grid-cols-1 divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($orders as $order)
                            @php
                                $percent = $order->total_transaksi > 0 ? min(100, round(($order->total_paid / $order->total_transaksi) * 100)) : 0;
                                $lastPayment = $order->payments->last();
                            @endphp
                            <div class="p-4 bg-white hover:bg-gray-50 transition-colors">
                                {{-- Header --}}
                                 <div class="flex justify-between items-start mb-2">
                                     <div>
                                         <span class="font-mono bg-[#22AF85]/10 px-2 py-0.5 rounded border border-[#22AF85]/30 text-xs font-bold text-[#22AF85] shadow-sm">
                                            {{ $order->spk_number }}
                                        </span>
                                        <div class="font-bold text-gray-900 mt-1">{{ $order->customer_name }}</div>
                                         <div class="text-[10px] text-gray-500 flex items-center gap-1 mt-0.5">
                                             <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            {{ $order->finance_entry_at ? $order->finance_entry_at->format('d M, H:i') : $order->created_at->format('d M, H:i') }}
                                        </div>
                                    </div>
                    
                                     <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-black border uppercase
                                        {{ $order->status === \App\Enums\WorkOrderStatus::WAITING_PAYMENT ? 'bg-[#FFC232]/10 text-[#FFC232] border-[#FFC232]/30' : '' }}
                                        {{ in_array($order->status, [\App\Enums\WorkOrderStatus::PREPARATION, \App\Enums\WorkOrderStatus::SORTIR, \App\Enums\WorkOrderStatus::PRODUCTION, \App\Enums\WorkOrderStatus::QC]) ? 'bg-[#22AF85]/10 text-[#22AF85] border-[#22AF85]/30' : '' }}
                                        {{ in_array($order->status, [\App\Enums\WorkOrderStatus::SELESAI, \App\Enums\WorkOrderStatus::DIANTAR]) ? 'bg-[#22AF85]/10 text-[#22AF85] border-[#22AF85]/30' : '' }}
                                    ">
                                        {{ str_replace('_', ' ', $order->status->value) }}
                                    </span>
                                </div>
                                
                                {{-- Progress --}}
                                <div class="mb-3">
                                     <div class="flex items-center justify-between text-xs mb-1">
                                         <span class="font-bold {{ $percent >= 100 ? 'text-[#22AF85]' : 'text-gray-600' }}">
                                            paid: Rp {{ number_format($order->total_paid, 0, ',', '.') }}
                                        </span>
                                         <span class="font-black {{ $percent >= 100 ? 'text-[#22AF85]' : 'text-gray-600' }}">
                                            {{ $percent }}%
                                        </span>
                                    </div>
                                    <div class="relative w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                        <div class="absolute inset-0 bg-[#22AF85]" 
                                             style="width: {{ $percent }}%">
                                        </div>
                                    </div>
                                </div>
                    
                                {{-- Financials --}}
                                 <div class="flex justify-between items-end mb-3 border-t border-gray-100 pt-2">
                                    <div>
                                         <div class="text-xs text-gray-500 uppercase font-bold">Total Bill</div>
                                         <div class="font-bold text-gray-900">Rp {{ number_format($order->total_transaksi, 0, ',', '.') }}</div>
                                         @if($order->discount > 0)
                                            <span class="text-[10px] text-[#22AF85] font-bold bg-[#22AF85]/10 px-1 rounded">-{{ number_format($order->discount, 0, ',', '.') }}</span>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                         @if($order->sisa_tagihan > 0)
                                            <div class="text-xs text-[#FFC232] font-bold uppercase">Sisa Tagihan</div>
                                             <div class="font-black text-[#FFC232] text-lg">Rp {{ number_format($order->sisa_tagihan, 0, ',', '.') }}</div>
                                        @else
                                             <div class="inline-flex items-center gap-1 px-2 py-1 bg-[#22AF85]/10 rounded border border-[#22AF85]/30">
                                                 <span class="font-black text-[#22AF85] text-xs">LUNAS</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                    
                                {{-- Action --}}
                                 <div class="flex gap-2">
                                    <a href="{{ route('finance.show', $order->id) }}" 
                                        class="flex-1 bg-[#FFC232] text-gray-900 px-3 py-2 rounded-lg text-sm font-bold text-center hover:bg-[#FFD666] shadow-md transition-all">
                                        Lihat Detail
                                    </a>
                                    @can('manageFinance', $order)
                                    <form action="{{ route('finance.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Hapus data finance ini?');">
                                        @csrf
                                        @method('DELETE')
                                         <button type="submit" class="bg-white text-gray-600 border border-gray-300 px-3 py-2 rounded-lg hover:bg-gray-50 transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.859L4.764 7M16 17v-4m-4 4v-4m-4 4v-4m-6-6h14m2 0a2 2 0 002-2V7a2 2 0 00-2 2H3a2 2 0 00-2 2v.17c0 1.1.9 2 2 2h1M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"></path></svg>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </div>
                        @empty
                             <div class="text-center p-6 text-gray-500 italic text-sm">Tidak ada order.</div>
                        @endforelse
                </div>
            
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                                    <th class="px-6 py-4 text-left text-xs font-black text-gray-700 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-4 text-left text-xs font-black text-gray-700 uppercase tracking-wider">Info Order</th>
                                    <th class="px-6 py-4 text-center text-xs font-black text-gray-700 uppercase tracking-wider">Status Workshop</th>
                                    <th class="px-6 py-4 text-right text-xs font-black text-gray-700 uppercase tracking-wider">Total Bill</th>
                                    <th class="px-6 py-4 text-left text-xs font-black text-gray-700 uppercase tracking-wider w-1/4">Progress Bayar</th>
                                    <th class="px-6 py-4 text-right text-xs font-black text-gray-700 uppercase tracking-wider">Sisa Tagihan</th>
                                    <th class="px-6 py-4 text-center text-xs font-black text-gray-700 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($orders as $order)
                                    @php
                                        $percent = $order->total_transaksi > 0 ? min(100, round(($order->total_paid / $order->total_transaksi) * 100)) : 0;
                                        $lastPayment = $order->payments->last();
                                    @endphp
                                    <tr class="hover:bg-[#22AF85]/5 transition-all duration-200 group">
                                        <td class="px-6 py-4 text-gray-500 font-mono text-xs font-bold">
                                            {{ ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration }}
                                        </td>
                                        
                                        {{-- INFO ORDER --}}
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col gap-1">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-mono font-black text-[#22AF85] bg-[#22AF85]/10 px-3 py-1 rounded-lg border border-[#22AF85]/30 text-xs shadow-sm">
                                                        {{ $order->spk_number }}
                                                    </span>
                                                    @if($order->cs_code)
                                                        <span class="text-[10px] text-gray-400 font-mono bg-gray-100 px-2 py-0.5 rounded">{{ $order->cs_code }}</span>
                                                    @endif
                                                </div>
                                                <div class="font-bold text-gray-900 leading-tight">{{ $order->customer_name }}</div>
                                                <div class="text-xs text-gray-500 flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    {{ $order->finance_entry_at ? $order->finance_entry_at->format('d M, H:i') : $order->created_at->format('d M, H:i') }}
                                                </div>
                                            </div>
                                        </td>
    
                                        {{-- STATUS WORKSHOP --}}
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-black border-2 shadow-sm
                                                {{ $order->status === \App\Enums\WorkOrderStatus::WAITING_PAYMENT ? 'bg-[#FFC232]/10 text-[#FFC232] border-[#FFC232]/30' : '' }}
                                                {{ in_array($order->status, [\App\Enums\WorkOrderStatus::PREPARATION, \App\Enums\WorkOrderStatus::SORTIR, \App\Enums\WorkOrderStatus::PRODUCTION, \App\Enums\WorkOrderStatus::QC]) ? 'bg-[#22AF85]/10 text-[#22AF85] border-[#22AF85]/30' : '' }}
                                                {{ in_array($order->status, [\App\Enums\WorkOrderStatus::SELESAI, \App\Enums\WorkOrderStatus::DIANTAR]) ? 'bg-[#22AF85]/10 text-[#22AF85] border-[#22AF85]/30' : '' }}
                                            ">
                                                {{ str_replace('_', ' ', $order->status->value) }}
                                            </span>
                                        </td>
    
                                        {{-- BILLING --}}
                                        <td class="px-6 py-4 text-right">
                                            <div class="font-black text-gray-900 text-base">Rp {{ number_format($order->total_transaksi, 0, ',', '.') }}</div>
                                            @if($order->discount > 0)
                                                <div class="text-[10px] text-gray-400 line-through">Rp {{ number_format($order->total_transaksi + $order->discount, 0, ',', '.') }}</div>
                                                <div class="text-[10px] text-[#22AF85] font-bold bg-[#22AF85]/10 px-1 rounded inline-block">
                                                    - Rp {{ number_format($order->discount, 0, ',', '.') }}
                                                </div>
                                            @endif
                                        </td>
    
                                        {{-- PROGRESS PAYMENT --}}
                                        <td class="px-6 py-4">
                                            <div class="space-y-2">
                                                <div class="flex items-center justify-between text-xs mb-1">
                                                    <span class="font-bold {{ $percent >= 100 ? 'text-[#22AF85]' : 'text-gray-600' }}">
                                                        Rp {{ number_format($order->total_paid, 0, ',', '.') }}
                                                    </span>
                                                    <span class="font-black {{ $percent >= 100 ? 'text-[#22AF85]' : 'text-gray-600' }}">
                                                        {{ $percent }}%
                                                    </span>
                                                </div>
                                                <div class="relative w-full bg-gray-200 rounded-full h-3 overflow-hidden shadow-inner">
                                                    <div class="absolute inset-0 bg-[#22AF85] rounded-full transition-all duration-500 shadow-sm" 
                                                         style="width: {{ $percent }}%">
                                                    </div>
                                                </div>
                                                @if($lastPayment)
                                                    <div class="text-[10px] text-gray-400 flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                                        </svg>
                                                        {{ $lastPayment->payment_method }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
    
                                        {{-- SISA TAGIHAN --}}
                                        <td class="px-6 py-4 text-right">
                                            @if($order->sisa_tagihan > 0)
                                                <div class="inline-flex flex-col items-end">
                                                    <span class="font-black text-[#FFC232] text-lg">
                                                        Rp {{ number_format($order->sisa_tagihan, 0, ',', '.') }}
                                                    </span>
                                                    <span class="text-[10px] text-[#FFC232] font-bold uppercase">Belum Lunas</span>
                                                    
                                                    @if($order->payment_due_date)
                                                        @php
                                                            $daysLeft = now()->diffInDays($order->payment_due_date, false);
                                                            $isOverdue = $daysLeft < 0;
                                                            $isNear = $daysLeft >= 0 && $daysLeft <= 3;
                                                        @endphp
                                                        <div class="mt-1 flex items-center gap-1 {{ $isOverdue ? 'text-gray-700 bg-gray-100 border-gray-200' : ($isNear ? 'text-[#FFC232] bg-[#FFC232]/10 border-[#FFC232]/30' : 'text-gray-500 bg-gray-100 border-gray-200') }} px-2 py-0.5 rounded border text-[10px] font-bold">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"/></svg>
                                                            <span>Due: {{ $order->payment_due_date->format('d M') }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-[#22AF85]/10 rounded-lg border border-[#22AF85]/30">
                                                    <svg class="w-5 h-5 text-[#22AF85]" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <span class="font-black text-[#22AF85]">LUNAS</span>
                                                </div>
                                            @endif
                                        </td>
    
                                        {{-- ACTION --}}
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ route('finance.show', $order->id) }}" 
                                                   class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-[#FFC232] text-gray-900 shadow-md hover:shadow-lg hover:scale-110 hover:bg-[#FFD666] transition-all duration-200 group">
                                                    <svg class="w-5 h-5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                    </svg>
                                                </a>
    
                                                @can('manageFinance', $order)
                                                <form action="{{ route('finance.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Hapus data finance ini?');" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white border-2 border-gray-300 text-gray-600 hover:bg-gray-50 hover:border-gray-400 shadow-sm hover:shadow-md transition-all duration-200" 
                                                            title="Hapus">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-2.132-1.859L4.764 7M16 17v-4m-4 4v-4m-4 4v-4m-6-6h14m2 0a2 2 0 002-2V7a2 2 0 00-2 2H3a2 2 0 00-2 2v.17c0 1.1.9 2 2 2h1M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-16 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="p-4 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl mb-4 shadow-inner">
                                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                                    </svg>
                                                </div>
                                                <span class="font-bold text-gray-600 text-lg">Tidak ada data order</span>
                                                <span class="text-sm text-gray-400 mt-1">Belum ada transaksi pada kategori ini</span>
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
