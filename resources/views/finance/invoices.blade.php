<x-app-layout>
    <div class="min-h-screen bg-[#F8FAFC]">
        {{-- Elite Premium Header --}}
        <div class="bg-white/90 shadow-2xl border-b border-gray-100 sticky top-0 z-40 backdrop-blur-xl">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-5 sm:py-8">
                <div class="flex flex-col gap-5 sm:gap-8">
                    {{-- Top Row: Title + Create Button --}}
                    <div class="flex items-center justify-between">
                        {{-- Left Title --}}
                        <div class="flex items-center gap-3 sm:gap-6">
                            <div class="p-2.5 sm:p-4 bg-[#1B8A68] rounded-xl sm:rounded-[1.5rem] shadow-[0_10px_30px_-10px_rgba(27,138,104,0.5)] transform -rotate-3 hover:rotate-0 transition-transform duration-500">
                                <svg class="w-5 h-5 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 sm:gap-3 mb-0.5 sm:mb-1">
                                    <span class="text-[8px] sm:text-[10px] font-black bg-emerald-50 text-[#1B8A68] px-1.5 sm:px-2 py-0.5 rounded-md uppercase tracking-widest italic border border-emerald-100">DATA ARSIP</span>
                                    <h1 class="text-xl sm:text-4xl font-black text-gray-900 tracking-tighter leading-none italic uppercase">Sentral Invoice</h1>
                                </div>
                                <p class="text-gray-400 text-[9px] sm:text-[11px] font-black uppercase tracking-[0.2em] sm:tracking-[0.3em] italic opacity-70 hidden sm:block">Manajemen Tagihan Gabungan Terintegrasi</p>
                            </div>
                        </div>

                        {{-- Create Button --}}
                        <a href="{{ route('finance.invoices.create') }}" class="group relative inline-flex items-center gap-2 sm:gap-4 px-4 sm:px-8 py-3 sm:py-4 bg-[#FFC232] hover:bg-[#FFD666] text-gray-900 rounded-xl sm:rounded-[2rem] font-black text-[10px] sm:text-xs uppercase tracking-[0.1em] sm:tracking-[0.2em] italic shadow-xl shadow-amber-100 transition-all hover:-translate-y-1 active:scale-95">
                            <span class="hidden sm:inline">Buat Invoice Baru</span>
                            <span class="sm:hidden">Buat</span>
                            <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-full bg-black/5 flex items-center justify-center">
                                <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M12 4v16m8-8H4"></path></svg>
                            </div>
                        </a>
                    </div>

                    {{-- Bottom Row: Filters --}}
                    <form action="{{ route('finance.invoices.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 sm:gap-3">
                        {{-- Gateway Filter --}}
                        <select name="gateway" onchange="this.form.submit()" class="px-4 sm:px-5 py-3 sm:py-4 bg-gray-50 border-2 border-transparent rounded-xl sm:rounded-[2rem] focus:bg-white focus:border-[#1B8A68]/20 focus:ring-4 focus:ring-[#1B8A68]/5 text-sm font-black italic tracking-tight text-gray-600 transition-all duration-500 shadow-inner cursor-pointer appearance-none outline-none">
                            <option value="" class="font-bold">Semua Gateway</option>
                            @foreach($gateways as $gw)
                                <option value="{{ $gw }}" {{ request('gateway') === $gw ? 'selected' : '' }} class="font-bold">🖥️ Gateway: {{ $gw }}</option>
                            @endforeach
                        </select>

                        {{-- Payment Status Filter --}}
                        <select name="payment_status" onchange="this.form.submit()" class="px-4 sm:px-5 py-3 sm:py-4 bg-gray-50 border-2 border-transparent rounded-xl sm:rounded-[2rem] focus:bg-white focus:border-[#1B8A68]/20 focus:ring-4 focus:ring-[#1B8A68]/5 text-sm font-black italic tracking-tight text-gray-600 transition-all duration-500 shadow-inner cursor-pointer appearance-none outline-none">
                            <option value="" class="font-bold">Semua Pembayaran</option>
                            <option value="Belum Bayar" {{ request('payment_status') === 'Belum Bayar' ? 'selected' : '' }} class="font-bold">⚪ Belum Bayar</option>
                            <option value="DP/Cicil" {{ request('payment_status') === 'DP/Cicil' ? 'selected' : '' }} class="font-bold">🟡 DP/Cicil</option>
                            <option value="Lunas" {{ request('payment_status') === 'Lunas' ? 'selected' : '' }} class="font-bold">🟢 Lunas</option>
                        </select>

                        {{-- Search Input --}}
                        <div class="relative group/search flex-1 sm:flex-none">
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Cari Nomor/Nama..." 
                                   class="w-full sm:w-64 md:w-80 pl-12 sm:pl-14 pr-6 py-3 sm:py-4 bg-gray-50 border-2 border-transparent rounded-xl sm:rounded-[2rem] focus:bg-white focus:border-[#1B8A68]/20 focus:ring-4 focus:ring-[#1B8A68]/5 text-sm font-black italic tracking-tight placeholder-gray-300 transition-all duration-500 shadow-inner">
                            <svg class="w-5 h-5 text-gray-300 absolute left-4 sm:left-6 top-1/2 -translate-y-1/2 group-focus-within/search:text-[#1B8A68] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Main Content Section --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-12">

            {{-- DESKTOP TABLE VIEW (hidden on mobile/tablet) --}}
            <div class="hidden lg:block bg-white rounded-[2rem] sm:rounded-[3rem] shadow-2xl border border-gray-100 overflow-hidden relative">
                <div class="absolute top-0 right-0 w-64 h-64 bg-[#1B8A68]/5 rounded-bl-[10rem] -mr-16 -mt-16 pointer-events-none"></div>
                
                <div class="overflow-x-auto relative z-10">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#F8FAFC] border-b border-gray-100">
                                <th class="px-8 py-8 text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic">No. Invoice</th>
                                <th class="px-8 py-8 text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic">Data Pelanggan</th>
                                <th class="px-8 py-8 text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic">Rincian</th>
                                <th class="px-8 py-8 text-[11px] font-black text-gray-400 text-center uppercase tracking-[0.3em] italic">Status SPK</th>
                                <th class="px-8 py-8 text-[11px] font-black text-gray-400 text-right uppercase tracking-[0.3em] italic">Total Tagihan</th>
                                <th class="px-8 py-8 text-[11px] font-black text-gray-400 text-center uppercase tracking-[0.3em] italic">Status</th>
                                <th class="px-8 py-8 text-[11px] font-black text-gray-400 text-center uppercase tracking-[0.3em] italic">Estimasi</th>
                                <th class="px-8 py-8 text-[11px] font-black text-gray-400 text-center uppercase tracking-[0.3em] italic">Nota</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($invoices as $invoice)
                                <tr class="hover:bg-[#F8FAFC] transition-all duration-300 group">
                                    <td class="px-8 py-8">
                                        <div class="flex items-center gap-5">
                                            <div class="w-12 h-12 bg-gray-50 text-gray-400 rounded-2xl group-hover:bg-[#1B8A68] group-hover:text-white transition-all duration-500 flex items-center justify-center shadow-inner group-hover:shadow-lg group-hover:shadow-emerald-100 group-hover:-rotate-6">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <a href="{{ route('finance.invoices.show', $invoice->id) }}" class="text-lg font-black text-gray-900 leading-none italic uppercase tracking-tighter group-hover:text-[#1B8A68] transition-colors block pb-1">{{ $invoice->invoice_number }}</a>
                                                <div class="text-[10px] text-gray-400 font-black uppercase tracking-widest italic opacity-60">{{ $invoice->created_at->format('d M Y • H:i') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-8">
                                        <div class="font-black text-gray-900 italic uppercase tracking-tight leading-none mb-1.5">{{ $invoice->customer?->name ?? 'Data Terhapus' }}</div>
                                        <div class="text-[11px] text-gray-400 font-black tracking-widest italic opacity-80">{{ $invoice->customer?->phone ?? '-' }}</div>
                                    </td>
                                    <td class="px-8 py-8">
                                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-gray-50 border border-gray-100 rounded-lg shadow-inner mb-2 group-hover:bg-white group-hover:border-emerald-100 transition-colors">
                                            <span class="text-[10px] font-black text-[#1B8A68] italic">{{ $invoice->workOrders->count() }} Pasang Sepatu</span>
                                        </div>
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach($invoice->workOrders->unique('cs_code') as $order)
                                                @if($order->cs_code)
                                                    <span class="text-[9px] font-black px-1.5 py-0.5 bg-emerald-50 text-[#1B8A68] rounded uppercase tracking-widest border border-emerald-100 italic">{{ $order->cs_code }}</span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-8 py-8 text-center">
                                        @php
                                            $spkStyle = match($invoice->spk_status) {
                                                'SELESAI' => 'bg-emerald-50 text-[#1B8A68] border-emerald-100',
                                                'BELUM SELESAI' => 'bg-amber-50 text-[#FFC232] border-amber-100',
                                                default => 'bg-gray-50 text-gray-400 border-gray-100'
                                            };
                                        @endphp
                                        <div class="inline-flex items-center px-3 py-1.5 rounded-xl border {{ $spkStyle }} shadow-sm">
                                            <span class="text-[10px] font-black uppercase tracking-[0.1em] italic">{{ $invoice->spk_status }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-8 text-right">
                                        <div class="text-2xl font-black text-gray-900 italic tabular-nums tracking-tighter leading-none mb-1 group-hover:scale-105 transition-transform origin-right">Rp {{ number_format($invoice->total_amount + $invoice->shipping_cost, 0, ',', '.') }}</div>
                                        
                                        @if($invoice->paid_amount > 0)
                                            <div class="text-[9px] text-[#1B8A68] font-black uppercase tracking-widest italic opacity-80 mb-2">Terbayar: Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</div>
                                        @endif

                                        <div class="space-y-1 pt-2 border-t border-gray-50">
                                            <div class="flex justify-end items-center gap-2">
                                                <span class="text-[8px] font-black text-gray-400 italic uppercase tracking-tighter">DP (70%):</span>
                                                <span class="text-[10px] font-black text-emerald-600 italic tabular-nums">Rp {{ number_format($invoice->target_dp_amount + ($invoice->dp_unique_code ?? 0), 0, ',', '.') }} <span class="text-[8px] opacity-60">({{ $invoice->dp_unique_code ?? '-' }})</span></span>
                                            </div>
                                            <div class="flex justify-end items-center gap-2">
                                                <span class="text-[8px] font-black text-gray-400 italic uppercase tracking-tighter">Penagihan Full:</span>
                                                <span class="text-[10px] font-black text-rose-600 italic tabular-nums">Rp {{ number_format($invoice->total_amount + $invoice->shipping_cost + ($invoice->final_unique_code ?? 0), 0, ',', '.') }} <span class="text-[8px] opacity-60">({{ $invoice->final_unique_code ?? '-' }})</span></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-8 text-center">
                                        @php
                                            $statusStyle = match($invoice->status) {
                                                'Lunas' => 'bg-emerald-50 text-[#1B8A68] border-emerald-100 shadow-emerald-50',
                                                'DP/Cicil' => 'bg-amber-50 text-[#FFC232] border-amber-100 shadow-amber-50',
                                                default => 'bg-gray-50 text-gray-400 border-gray-200'
                                            };
                                            $dotStyle = match($invoice->status) {
                                                'Lunas' => 'bg-[#1B8A68]',
                                                'DP/Cicil' => 'bg-[#FFC232]',
                                                default => 'bg-gray-300'
                                            };
                                        @endphp
                                        <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-2xl border-2 {{ $statusStyle }} shadow-sm">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $dotStyle }} {{ $invoice->status === 'DP/Cicil' ? 'animate-pulse' : '' }}"></span>
                                            <span class="text-[11px] font-black uppercase tracking-[0.2em] italic">{{ $invoice->status }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-8 text-center">
                                        @if($invoice->estimasi_selesai)
                                            <div class="inline-flex flex-col items-center">
                                                <span class="text-[10px] font-black text-[#1B8A68] uppercase tracking-widest italic leading-none mb-1">Target</span>
                                                <span class="text-xs font-black text-gray-900 italic tracking-tight uppercase">{{ \Carbon\Carbon::parse($invoice->estimasi_selesai)->format('d M Y') }}</span>
                                            </div>
                                        @else
                                            <span class="text-[10px] font-black text-gray-300 uppercase tracking-widest italic">Belum Set</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-8 text-center">
                                        <div class="flex items-center justify-center gap-3">
                                            <a href="{{ url('/api/invoice_share_grouped.php?token=' . urlencode($invoice->invoice_number) . '&type=' . ($invoice->status === 'Belum Bayar' ? 'BL' : 'L')) }}" 
                                               target="_blank" 
                                               class="w-12 h-12 rounded-full bg-white border-2 border-gray-100 text-gray-400 hover:text-[#1B8A68] hover:border-[#1B8A68]/30 hover:shadow-lg hover:shadow-emerald-50 transition-all active:scale-90 flex items-center justify-center group/btn" 
                                               title="Cetak Nota Gabungan">
                                                <svg class="w-5 h-5 group-hover/btn:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-10 py-40 text-center">
                                        <div class="w-32 h-32 bg-[#F8FAFC] rounded-[2.5rem] flex items-center justify-center text-6xl mb-8 shadow-inner border border-gray-100 mx-auto filter grayscale opacity-20">📋</div>
                                        <h3 class="text-3xl font-black text-gray-900 mb-2 uppercase tracking-tighter italic">Belum Ada Data</h3>
                                        <p class="text-gray-400 text-[11px] font-black uppercase tracking-[0.3em] italic opacity-60">Tidak ada rincian penagihan yang terdata</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if(isset($invoices) && $invoices->hasPages())
                <div class="px-10 py-10 border-t border-gray-50 bg-[#F8FAFC]/50 flex justify-center">
                    {{ $invoices->links() }}
                </div>
                @endif
            </div>

            {{-- MOBILE/TABLET CARD VIEW (visible on mobile/tablet, hidden on desktop) --}}
            <div class="lg:hidden space-y-4">
                @forelse($invoices as $invoice)
                    <a href="{{ route('finance.invoices.show', $invoice->id) }}" class="block bg-white rounded-2xl shadow-lg border border-gray-100 p-5 hover:shadow-xl transition-all duration-300 active:scale-[0.98] group">
                        {{-- Top: Invoice Number + Status Badge --}}
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gray-50 text-gray-400 rounded-xl group-hover:bg-[#1B8A68] group-hover:text-white transition-all flex items-center justify-center shadow-inner">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-base font-black text-gray-900 italic uppercase tracking-tighter leading-none group-hover:text-[#1B8A68] transition-colors">{{ $invoice->invoice_number }}</div>
                                    <div class="text-[10px] text-gray-400 font-black uppercase tracking-widest italic opacity-60 mt-0.5">{{ $invoice->created_at->format('d M Y • H:i') }}</div>
                                </div>
                            </div>
                            @php
                                $statusStyle = match($invoice->status) {
                                    'Lunas' => 'bg-emerald-50 text-[#1B8A68] border-emerald-100',
                                    'DP/Cicil' => 'bg-amber-50 text-[#FFC232] border-amber-100',
                                    default => 'bg-gray-50 text-gray-400 border-gray-200'
                                };
                                $dotStyle = match($invoice->status) {
                                    'Lunas' => 'bg-[#1B8A68]',
                                    'DP/Cicil' => 'bg-[#FFC232]',
                                    default => 'bg-gray-300'
                                };
                            @endphp
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl border {{ $statusStyle }} shadow-sm">
                                <span class="w-1.5 h-1.5 rounded-full {{ $dotStyle }} {{ $invoice->status === 'DP/Cicil' ? 'animate-pulse' : '' }}"></span>
                                <span class="text-[10px] font-black uppercase tracking-[0.1em] italic">{{ $invoice->status }}</span>
                            </div>
                        </div>

                        {{-- Middle: Customer + SPK Status --}}
                        <div class="flex items-center justify-between mb-3 pb-3 border-b border-gray-50">
                            <div>
                                <div class="font-black text-gray-900 italic uppercase tracking-tight text-sm leading-none mb-1">{{ $invoice->customer?->name ?? 'Data Terhapus' }}</div>
                                <div class="text-[10px] text-gray-400 font-black tracking-widest italic opacity-80">{{ $invoice->customer?->phone ?? '-' }}</div>
                            </div>
                            <div class="flex items-center gap-2">
                                @php
                                    $spkStyle = match($invoice->spk_status) {
                                        'SELESAI' => 'bg-emerald-50 text-[#1B8A68] border-emerald-100',
                                        'BELUM SELESAI' => 'bg-amber-50 text-[#FFC232] border-amber-100',
                                        default => 'bg-gray-50 text-gray-400 border-gray-100'
                                    };
                                @endphp
                                <div class="inline-flex items-center px-2 py-1 rounded-lg border {{ $spkStyle }}">
                                    <span class="text-[9px] font-black uppercase tracking-[0.05em] italic">{{ $invoice->spk_status }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Bottom: Amount + Rincian + Print --}}
                        <div class="flex items-end justify-between">
                            <div class="flex-1">
                                <div class="text-xl font-black text-gray-900 italic tabular-nums tracking-tighter leading-none mb-1">Rp {{ number_format($invoice->total_amount + $invoice->shipping_cost, 0, ',', '.') }}</div>
                                @if($invoice->paid_amount > 0)
                                    <div class="text-[9px] text-[#1B8A68] font-black uppercase tracking-widest italic opacity-80 mb-2">Terbayar: Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</div>
                                @endif

                                <div class="space-y-0.5 mt-2 pt-2 border-t border-gray-50">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[7px] font-black text-gray-400 italic uppercase">DP (70%):</span>
                                        <span class="text-[9px] font-black text-emerald-600 italic">Rp {{ number_format($invoice->target_dp_amount + ($invoice->dp_unique_code ?? 0), 0, ',', '.') }} ({{ $invoice->dp_unique_code ?? '-' }})</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[7px] font-black text-gray-400 italic uppercase">Full:</span>
                                        <span class="text-[9px] font-black text-rose-600 italic">Rp {{ number_format($invoice->total_amount + $invoice->shipping_cost + ($invoice->final_unique_code ?? 0), 0, ',', '.') }} ({{ $invoice->final_unique_code ?? '-' }})</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-50 border border-gray-100 rounded-lg">
                                    <span class="text-[9px] font-black text-[#1B8A68] italic">{{ $invoice->workOrders->count() }} SPK</span>
                                </div>
                                <div onclick="event.preventDefault(); event.stopPropagation(); window.open('{{ url('/api/invoice_share_grouped.php?token=' . urlencode($invoice->invoice_number) . '&type=' . ($invoice->status === 'Belum Bayar' ? 'BL' : 'L')) }}', '_blank')" 
                                     class="w-9 h-9 rounded-full bg-white border-2 border-gray-100 text-gray-400 hover:text-[#1B8A68] hover:border-[#1B8A68]/30 transition-all active:scale-90 flex items-center justify-center cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="bg-white rounded-2xl p-12 text-center border border-gray-100 shadow-lg">
                        <div class="w-20 h-20 bg-[#F8FAFC] rounded-2xl flex items-center justify-center text-4xl mb-6 shadow-inner border border-gray-100 mx-auto filter grayscale opacity-20">📋</div>
                        <h3 class="text-xl font-black text-gray-900 mb-2 uppercase tracking-tighter italic">Belum Ada Data</h3>
                        <p class="text-gray-400 text-[10px] font-black uppercase tracking-[0.2em] italic opacity-60">Tidak ada rincian penagihan yang terdata</p>
                    </div>
                @endforelse

                {{-- Pagination Mobile --}}
                @if(isset($invoices) && $invoices->hasPages())
                <div class="py-6 flex justify-center">
                    {{ $invoices->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
