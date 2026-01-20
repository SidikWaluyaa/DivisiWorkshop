<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-teal-50/30 to-orange-50/20">
        {{-- Premium Header with Gradient --}}
        <div class="bg-gradient-to-r from-teal-600 via-teal-500 to-orange-500 shadow-xl">
            <div class="max-w-7xl mx-auto px-6 py-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-white/20 backdrop-blur-sm rounded-2xl shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-black text-white tracking-tight">Finance Dashboard</h1>
                            <p class="text-teal-50 text-sm mt-1">Kelola pembayaran & tagihan customer</p>
                        </div>
                    </div>
                    
                    {{-- Search Bar --}}
                    <form action="{{ route('finance.index') }}" method="GET" class="flex gap-2">
                        <input type="hidden" name="tab" value="{{ request('tab', 'waiting_dp') }}">
                        <div class="relative">
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Cari SPK / Customer..." 
                                   class="pl-10 pr-4 py-2.5 bg-white/90 backdrop-blur-sm border-0 rounded-xl focus:ring-2 focus:ring-white/50 text-gray-700 placeholder-gray-400 shadow-lg w-64">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <button type="submit" class="px-6 py-2.5 bg-white text-teal-600 rounded-xl hover:bg-teal-50 transition-all shadow-lg font-bold">
                            Cari
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modern Tab Navigation --}}
        <div class="max-w-7xl mx-auto px-6 -mt-4">
            <div class="bg-white rounded-2xl shadow-xl p-2 flex gap-2 overflow-x-auto" x-data="{ activeTab: '{{ request('tab', 'waiting_dp') }}' }">
                <a href="{{ route('finance.index', ['tab' => 'waiting_dp']) }}" 
                   @click="activeTab = 'waiting_dp'"
                   :class="activeTab === 'waiting_dp' ? 'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg scale-105' : 'bg-gray-50 text-gray-600 hover:bg-gray-100'"
                   class="flex-1 min-w-[200px] px-6 py-4 rounded-xl transition-all duration-200 font-bold text-center flex items-center justify-center gap-2 group">
                    <span class="text-2xl">🚨</span>
                    <div class="text-left">
                        <div class="text-xs opacity-75">Urgent</div>
                        <div class="text-sm">Menunggu DP</div>
                    </div>
                </a>
                
                <a href="{{ route('finance.index', ['tab' => 'in_progress']) }}" 
                   @click="activeTab = 'in_progress'"
                   :class="activeTab === 'in_progress' ? 'bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-lg scale-105' : 'bg-gray-50 text-gray-600 hover:bg-gray-100'"
                   class="flex-1 min-w-[200px] px-6 py-4 rounded-xl transition-all duration-200 font-bold text-center flex items-center justify-center gap-2 group">
                    <span class="text-2xl">⚙️</span>
                    <div class="text-left">
                        <div class="text-xs opacity-75">Piutang</div>
                        <div class="text-sm">Dalam Proses</div>
                    </div>
                </a>
                
                <a href="{{ route('finance.index', ['tab' => 'ready_pickup']) }}" 
                   @click="activeTab = 'ready_pickup'"
                   :class="activeTab === 'ready_pickup' ? 'bg-gradient-to-r from-teal-500 to-teal-600 text-white shadow-lg scale-105' : 'bg-gray-50 text-gray-600 hover:bg-gray-100'"
                   class="flex-1 min-w-[200px] px-6 py-4 rounded-xl transition-all duration-200 font-bold text-center flex items-center justify-center gap-2 group">
                    <span class="text-2xl">✅</span>
                    <div class="text-left">
                        <div class="text-xs opacity-75">Siap Ambil</div>
                        <div class="text-sm">Pelunasan</div>
                    </div>
                </a>
                
                <a href="{{ route('finance.index', ['tab' => 'completed']) }}" 
                   @click="activeTab = 'completed'"
                   :class="activeTab === 'completed' ? 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white shadow-lg scale-105' : 'bg-gray-50 text-gray-600 hover:bg-gray-100'"
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
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                                <th class="px-6 py-4 text-left text-xs font-black text-gray-600 uppercase tracking-wider">No</th>
                                <th class="px-6 py-4 text-left text-xs font-black text-gray-600 uppercase tracking-wider">Info Order</th>
                                <th class="px-6 py-4 text-center text-xs font-black text-gray-600 uppercase tracking-wider">Status Workshop</th>
                                <th class="px-6 py-4 text-right text-xs font-black text-gray-600 uppercase tracking-wider">Total Bill</th>
                                <th class="px-6 py-4 text-left text-xs font-black text-gray-600 uppercase tracking-wider w-1/4">Progress Bayar</th>
                                <th class="px-6 py-4 text-right text-xs font-black text-gray-600 uppercase tracking-wider">Sisa Tagihan</th>
                                <th class="px-6 py-4 text-center text-xs font-black text-gray-600 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($orders as $order)
                                @php
                                    $percent = $order->total_transaksi > 0 ? min(100, round(($order->total_paid / $order->total_transaksi) * 100)) : 0;
                                    $lastPayment = $order->payments->last();
                                @endphp
                                <tr class="hover:bg-gradient-to-r hover:from-teal-50/50 hover:to-orange-50/30 transition-all duration-200 group">
                                    <td class="px-6 py-4 text-gray-500 font-mono text-xs font-bold">
                                        {{ ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration }}
                                    </td>
                                    
                                    {{-- INFO ORDER --}}
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-1">
                                            <div class="flex items-center gap-2">
                                                <span class="font-mono font-black text-teal-700 bg-gradient-to-r from-teal-50 to-teal-100 px-3 py-1 rounded-lg border border-teal-200 text-xs shadow-sm">
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
                                            {{ $order->status === \App\Enums\WorkOrderStatus::WAITING_PAYMENT ? 'bg-red-50 text-red-700 border-red-200' : '' }}
                                            {{ in_array($order->status, [\App\Enums\WorkOrderStatus::PREPARATION, \App\Enums\WorkOrderStatus::SORTIR, \App\Enums\WorkOrderStatus::PRODUCTION, \App\Enums\WorkOrderStatus::QC]) ? 'bg-amber-50 text-amber-700 border-amber-200' : '' }}
                                            {{ in_array($order->status, [\App\Enums\WorkOrderStatus::SELESAI, \App\Enums\WorkOrderStatus::DIANTAR]) ? 'bg-green-50 text-green-700 border-green-200' : '' }}
                                        ">
                                            {{ str_replace('_', ' ', $order->status->value) }}
                                        </span>
                                    </td>

                                    {{-- BILLING --}}
                                    <td class="px-6 py-4 text-right">
                                        <div class="font-black text-gray-900 text-base">Rp {{ number_format($order->total_transaksi, 0, ',', '.') }}</div>
                                        @if($order->discount > 0)
                                            <div class="text-[10px] text-gray-400 line-through">Rp {{ number_format($order->total_transaksi + $order->discount, 0, ',', '.') }}</div>
                                            <div class="text-[10px] text-green-600 font-bold bg-green-50 px-1 rounded inline-block">
                                                - Rp {{ number_format($order->discount, 0, ',', '.') }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- PROGRESS PAYMENT --}}
                                    <td class="px-6 py-4">
                                        <div class="space-y-2">
                                            <div class="flex items-center justify-between text-xs mb-1">
                                                <span class="font-bold {{ $percent >= 100 ? 'text-green-600' : 'text-orange-600' }}">
                                                    Rp {{ number_format($order->total_paid, 0, ',', '.') }}
                                                </span>
                                                <span class="font-black {{ $percent >= 100 ? 'text-green-600' : 'text-orange-600' }}">
                                                    {{ $percent }}%
                                                </span>
                                            </div>
                                            <div class="relative w-full bg-gray-200 rounded-full h-3 overflow-hidden shadow-inner">
                                                <div class="absolute inset-0 bg-gradient-to-r {{ $percent >= 100 ? 'from-green-400 to-green-500' : 'from-orange-400 to-orange-500' }} rounded-full transition-all duration-500 shadow-sm" 
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
                                                <span class="font-black text-red-600 text-lg">
                                                    Rp {{ number_format($order->sisa_tagihan, 0, ',', '.') }}
                                                </span>
                                                <span class="text-[10px] text-red-400 font-bold uppercase">Belum Lunas</span>
                                            </div>
                                        @else
                                            <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-200">
                                                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span class="font-black text-green-600">LUNAS</span>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- ACTION --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('finance.show', $order->id) }}" 
                                               class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-r from-teal-500 to-teal-600 text-white shadow-md hover:shadow-lg hover:scale-110 transition-all duration-200 group">
                                                <svg class="w-5 h-5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </a>

                                            <form action="{{ route('finance.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Hapus data finance ini?');" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-red-50 border-2 border-red-200 text-red-500 hover:bg-red-500 hover:text-white hover:border-red-500 shadow-sm hover:shadow-md transition-all duration-200" 
                                                        title="Hapus">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-2.132-1.859L4.764 7M16 17v-4m-4 4v-4m-4 4v-4m-6-6h14m2 0a2 2 0 002-2V7a2 2 0 00-2 2H3a2 2 0 00-2 2v.17c0 1.1.9 2 2 2h1M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"></path>
                                                    </svg>
                                                </button>
                                            </form>
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
                @if($orders->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $orders->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
