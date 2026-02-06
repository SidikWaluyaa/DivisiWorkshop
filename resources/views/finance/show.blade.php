<x-app-layout>
    {{-- Print Stylesheet --}}
    <style>
        @media print {
            /* Hide unnecessary elements */
            .print\:hidden,
            nav,
            footer,
            button,
            .no-print {
                display: none !important;
            }
            
            /* Reset page margins */
            @page {
                margin: 1cm;
                size: A4;
            }
            
            body {
                background: white !important;
                color: black !important;
            }
            
            /* Header styling for print */
            .print-header {
                background: #22AF85 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                padding: 20px;
                margin-bottom: 20px;
                border-radius: 8px;
            }
            
            /* Card styling for print */
            .print-card {
                border: 2px solid #e5e7eb;
                padding: 15px;
                margin-bottom: 15px;
                page-break-inside: avoid;
                border-radius: 8px;
            }
            
            /* Timeline for print */
            .print-timeline-dot {
                width: 12px;
                height: 12px;
                background: #22AF85;
                border-radius: 50%;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .print-timeline-line {
                width: 2px;
                background: #d1d5db;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            /* Ensure colors print */
            * {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            /* Page breaks */
            .page-break-before {
                page-break-before: always;
            }
            
            .page-break-after {
                page-break-after: always;
            }
            
            .no-page-break {
                page-break-inside: avoid;
            }
        }
    </style>
    
    <div class="min-h-screen bg-[#F8FAFC] pb-24">
        {{-- Elite Sticky Header --}}
        <div class="bg-white/80 shadow-2xl border-b border-gray-100 sticky top-0 z-40 backdrop-blur-xl">
            <div class="max-w-7xl mx-auto px-6 py-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-6">
                        <a href="{{ route('finance.index') }}" class="group flex items-center justify-center w-12 h-12 bg-white rounded-2xl shadow-lg border border-gray-100 hover:border-[#22AF85]/30 hover:shadow-emerald-100 transition-all active:scale-95 print:hidden">
                            <svg class="w-6 h-6 text-gray-400 group-hover:text-[#22AF85] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                        <div>
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="bg-[#22AF85] text-white text-[10px] font-black px-2 py-0.5 rounded-md uppercase tracking-widest shadow-lg shadow-emerald-100 italic cursor-default">SPK</span>
                                <h1 class="text-2xl font-black text-gray-900 tracking-tight italic">{{ $order->spk_number }}</h1>
                            </div>
                            <p class="text-gray-400 text-[10px] font-black uppercase tracking-[0.2em] flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                                Terminal Pembayaran & Buku Besar
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <div class="hidden md:flex flex-col items-end mr-4">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Status Pesanan</span>
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-50 rounded-full border border-gray-100">
                                <span class="w-2 h-2 rounded-full {{ $order->status === \App\Enums\WorkOrderStatus::SELESAI ? 'bg-emerald-500 shadow-[0_0_8px_rgba(34,175,133,0.5)]' : 'bg-blue-500 animate-pulse shadow-[0_0_8px_rgba(59,130,246,0.5)]' }}"></span>
                                <span class="text-[10px] font-black uppercase text-gray-700 tracking-wider">{{ str_replace('_', ' ', $order->status->value) }}</span>
                            </div>
                        </div>

                        <a href="{{ route('finance.print-invoice', $order->id) }}" target="_blank"
                                class="print:hidden h-12 inline-flex items-center gap-3 px-6 bg-[#FFC232] hover:bg-[#FFD666] rounded-2xl text-gray-900 font-black text-xs uppercase tracking-widest transition-all shadow-xl shadow-amber-100 hover:shadow-amber-200 hover:-translate-y-0.5 active:scale-95 group">
                            <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            Invoice
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 mt-8">
            {{-- Elite Summary Section --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                {{-- Card: Total Bill --}}
                <div class="group relative bg-white rounded-[2.5rem] p-8 shadow-2xl border border-gray-100 overflow-hidden hover:shadow-[#22AF85]/10 transition-all duration-500">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-[#22AF85]/5 rounded-bl-[5rem] -mr-8 -mt-8 transition-transform group-hover:scale-125 duration-700"></div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center shadow-inner border border-emerald-100 group-hover:scale-110 transition-transform duration-500">
                                <svg class="w-6 h-6 text-[#22AF85]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                            </div>
                            <span class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em]">Total Tagihan</span>
                        </div>
                        <h3 class="text-4xl font-black text-gray-900 tracking-tighter mb-4 group-hover:text-[#22AF85] transition-colors leading-none italic">
                            Rp {{ number_format($order->total_transaksi, 0, ',', '.') }}
                        </h3>
                        <div class="pt-6 border-t border-gray-50 flex flex-col gap-1.5">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Jatuh Tempo</span>
                            <div class="flex items-center gap-2 group/date">
                                <input type="date" 
                                       value="{{ $order->payment_due_date ? $order->payment_due_date->format('Y-m-d') : '' }}"
                                       onchange="updateDueDate(this.value)"
                                       class="text-sm font-black text-gray-900 border-none bg-[#F8FAFC] py-2 px-4 rounded-xl focus:ring-2 focus:ring-[#22AF85]/20 cursor-pointer hover:bg-white transition-all shadow-sm w-full italic">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card: Total Paid --}}
                <div class="group relative bg-[#22AF85] rounded-[2.5rem] p-8 shadow-2xl shadow-emerald-100 overflow-hidden hover:shadow-emerald-200 transition-all duration-500 border border-[#22AF85]">
                    <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/10 rounded-tr-[5rem] -ml-8 -mb-8 transition-transform group-hover:scale-125 duration-700"></div>
                    <div class="relative z-10 text-white">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center shadow-lg border border-white/20 group-hover:rotate-12 transition-transform duration-500">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <span class="text-[11px] font-black text-white/70 uppercase tracking-[0.2em]">Sudah Dibayar</span>
                        </div>
                        <h3 class="text-4xl font-black text-white tracking-tighter mb-4 leading-none italic">
                            Rp {{ number_format($order->total_paid, 0, ',', '.') }}
                        </h3>
                        <div class="pt-6 border-t border-white/20 flex items-center justify-between">
                            <span class="text-[10px] font-black text-white/60 uppercase tracking-widest">{{ $order->payments->count() }} Transaksi</span>
                            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center blur-[0.3px]">
                                <span class="text-xs font-black">✓</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card: Balance Due --}}
                <div class="group relative {{ $order->sisa_tagihan > 0 ? 'bg-white shadow-[#FFC232]/10' : 'bg-gray-50' }} rounded-[2.5rem] p-8 shadow-2xl border {{ $order->sisa_tagihan > 0 ? 'border-amber-100' : 'border-gray-100' }} overflow-hidden transition-all duration-500">
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-12 h-12 {{ $order->sisa_tagihan > 0 ? 'bg-amber-50 shadow-amber-100 border-amber-100' : 'bg-gray-200 shadow-inner' }} rounded-2xl flex items-center justify-center shadow-inner border group-hover:scale-110 transition-transform duration-500">
                                <svg class="w-6 h-6 {{ $order->sisa_tagihan > 0 ? 'text-[#FFC232]' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <span class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em]">Sisa Tagihan</span>
                        </div>
                        <h3 class="text-4xl font-black {{ $order->sisa_tagihan > 0 ? 'text-[#FFC232]' : 'text-gray-300' }} tracking-tighter mb-4 leading-none italic">
                            Rp {{ number_format($order->sisa_tagihan, 0, ',', '.') }}
                        </h3>
                        
                        @if($order->sisa_tagihan > 0)
                            <div class="pt-6 border-t border-gray-50">
                                <form action="{{ route('finance.donations.force', $order->id) }}" method="POST" onsubmit="return confirm('Pindahkan data ke list DONASI? Data ini akan diparkir selamanya.');">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-rose-50 hover:bg-rose-100 text-rose-500 rounded-2xl border border-rose-100 transition-all text-[10px] font-black uppercase tracking-widest shadow-sm hover:shadow active:scale-95 group/donasi">
                                        <svg class="w-4 h-4 group-hover/donasi:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.859L4.764 7M16 17v-4m-4 4v-4m-4 4v-4m-6-6h14m2 0a2 2 0 002-2V7a2 2 0 00-2 2H3a2 2 0 00-2 2v.17c0 1.1.9 2 2 2h1M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"></path>
                                        </svg>
                                        Pindahkan ke Donasi
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="pt-6 border-t border-emerald-100 flex items-center gap-2 text-[#22AF85]">
                                <div class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center">
                                    <span class="text-[10px] font-black">✓</span>
                                </div>
                                <span class="text-[10px] font-black uppercase tracking-widest italic">Lunas Sepenuhnya</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Customer Info Card --}}
            {{-- Elite Customer Card --}}
            <div class="bg-white rounded-[2.5rem] shadow-2xl p-8 mb-8 border border-gray-100 group relative overflow-hidden">
                <div class="absolute top-0 left-0 w-2 h-full bg-[#22AF85] opacity-20"></div>
                <div class="relative z-10">
                    <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] mb-8 flex items-center gap-2">
                        <div class="w-8 h-8 bg-emerald-50 rounded-xl flex items-center justify-center border border-emerald-100 shadow-inner">
                            👤
                        </div>
                        Informasi Pelanggan & Logistik
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                        <div class="space-y-6">
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Kontak Utama</p>
                                <p class="text-2xl font-black text-gray-900 leading-none tracking-tight italic">{{ $order->customer_name }}</p>
                                <p class="text-xs text-[#22AF85] font-black mt-2 bg-emerald-50 inline-block px-3 py-1 rounded-lg border border-emerald-100 shadow-sm">{{ $order->customer_phone }}</p>
                            </div>
                            <div class="pt-6 border-t border-gray-50">
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Item Specifications</p>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-xl shadow-inner border border-gray-100">👟</div>
                                    <div>
                                        <p class="text-sm font-black text-gray-900 leading-none">{{ $order->shoe_brand }} [{{ $order->shoe_size }}]</p>
                                        <p class="text-[10px] text-gray-400 font-bold mt-1 uppercase tracking-wider">{{ $order->shoe_color }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-3">Sektor Pengiriman</p>
                            <div class="bg-[#F8FAFC] border border-gray-100 rounded-[2rem] p-6 shadow-inner relative group/address transition-all hover:bg-white hover:shadow-xl duration-500">
                                <div class="text-gray-900 font-black text-base leading-relaxed italic mb-4">
                                    {{ $order->customer->address ?? ($order->customer_address ?? '-') }}
                                </div>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div class="p-3 bg-white rounded-2xl border border-gray-100 shadow-sm">
                                        <span class="text-[8px] font-black text-gray-400 uppercase block mb-1">Kelurahan</span>
                                        <span class="text-[10px] font-black text-gray-900 truncate block">{{ $order->customer->village ?? '-' }}</span>
                                    </div>
                                    <div class="p-3 bg-white rounded-2xl border border-gray-100 shadow-sm">
                                        <span class="text-[8px] font-black text-gray-400 uppercase block mb-1">Kecamatan</span>
                                        <span class="text-[10px] font-black text-gray-900 truncate block">{{ $order->customer->district ?? '-' }}</span>
                                    </div>
                                    <div class="p-3 bg-white rounded-2xl border border-gray-100 shadow-sm border-b-2 border-b-[#22AF85]/30">
                                        <span class="text-[8px] font-black text-gray-400 uppercase block mb-1">Kota/Kab</span>
                                        <span class="text-[10px] font-black text-[#22AF85] truncate block italic">{{ $order->customer->city ?? '-' }}</span>
                                    </div>
                                    <div class="p-3 bg-white rounded-2xl border border-gray-100 shadow-sm">
                                        <span class="text-[8px] font-black text-gray-400 uppercase block mb-1">Kode Pos</span>
                                        <span class="text-[10px] font-black text-gray-900 truncate block">{{ $order->customer->postal_code ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Elite Bill Details --}}
                <div class="space-y-6">
                    <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-gray-100">
                        <div class="bg-gray-50/50 px-8 py-6 border-b border-gray-100">
                            <h4 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] flex items-center gap-2">
                                <div class="w-8 h-8 bg-white rounded-xl flex items-center justify-center border border-gray-100 shadow-sm leading-none">
                                    📜
                                </div>
                                Ledger & Bill Specifications
                            </h4>
                        </div>
                        <div class="p-8">
                            <table class="w-full">
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($order->workOrderServices as $detail)
                                        <tr class="group">
                                            <td class="py-4 text-xs font-black text-gray-700 uppercase tracking-tight group-hover:text-[#22AF85] transition-colors leading-none">
                                                {{ $detail->custom_service_name ?? ($detail->service ? $detail->service->name : 'Layanan Hapus') }}
                                            </td>
                                            <td class="py-4 text-right font-black text-gray-900 tracking-tighter italic">Rp {{ number_format($detail->cost, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                    @if($order->cost_oto + $order->cost_add_service > 0)
                                    <tr>
                                        <td class="py-4 text-xs font-black text-gray-400 uppercase tracking-tight">Biaya OTO / Additional</td>
                                        <td class="py-4 text-right font-black text-gray-900 tracking-tighter italic">Rp {{ number_format($order->cost_oto + $order->cost_add_service, 0, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                    <tr class="group">
                                        <td class="py-4 text-gray-700 flex flex-col">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-black text-gray-700 uppercase tracking-tight">Biaya Pengiriman</span>
                                                <button onclick="editShipping()" class="w-6 h-6 flex items-center justify-center text-[#22AF85] bg-emerald-50 hover:bg-[#22AF85] hover:text-white rounded-lg transition-all opacity-0 group-hover:opacity-100 print:hidden shadow-sm" title="Edit Ongkir">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="text-[9px] text-[#22AF85] font-black uppercase tracking-widest mt-1 italic" id="display-shipping-zone">
                                                {{ $order->shipping_zone ? ($order->shipping_zone . ' (' . ($order->shipping_type ?? 'Ekspedisi') . ')') : '' }}
                                            </div>
                                        </td>
                                        <td class="py-4 text-right font-black text-gray-900 tracking-tighter italic" id="display-shipping">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                                    </tr>
                                    @if($order->discount > 0)
                                    <tr>
                                        <td class="py-4 text-xs font-black text-rose-400 uppercase tracking-tight italic">Diskon Khusus Diterapkan</td>
                                        <td class="py-4 text-right font-black text-rose-500 tracking-tighter italic">- Rp {{ number_format($order->discount, 0, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                    @if($order->unique_code > 0)
                                    <tr class="bg-emerald-50/30">
                                        <td class="py-4 px-4 text-[#22AF85]">
                                            <div class="flex items-center gap-2">
                                                <span class="text-[10px] font-black uppercase tracking-widest leading-none">Protokol Kode Unik</span>
                                                <span class="px-1.5 py-0.5 bg-[#22AF85]/10 text-[8px] font-black rounded border border-[#22AF85]/30 uppercase tracking-[0.2em] italic">Validated</span>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 text-right font-black text-[#22AF85] tracking-tighter italic">+ Rp {{ number_format($order->unique_code, 0, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                </tbody>
                                <tfoot class="border-t-[3px] border-gray-900">
                                    <tr>
                                        <td class="py-6 font-black text-gray-900 text-xl tracking-tighter uppercase italic">Total Keseluruhan</td>
                                        <td class="py-6 text-right font-black text-[#22AF85] text-3xl tracking-tighter italic" id="display-total-transaksi">Rp {{ number_format($order->total_transaksi, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                            
                            @if($order->status === \App\Enums\WorkOrderStatus::WAITING_PAYMENT)
                                <div class="mt-8 pt-8 border-t border-gray-50">
                                    <button onclick="confirmMove('{{ $order->id }}')" class="w-full bg-[#FFC232] hover:bg-[#FFD666] text-gray-900 font-black py-4 px-8 rounded-2xl shadow-xl shadow-amber-100 hover:shadow-amber-200 transition-all active:scale-95 uppercase tracking-widest text-xs flex items-center justify-center gap-3">
                                        🚀 Deploy to Workshop Floor
                                    </button>
                                    <div class="flex items-center justify-center gap-2 mt-4">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Siap untuk Aktivasi Produksi</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Payment Form & History --}}
                <div class="space-y-6">
                    {{-- Payment Form --}}
                {{-- Elite Payment Terminal --}}
                <div class="space-y-6">
                    @if($order->sisa_tagihan > 0)
                    <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-gray-100 group">
                        <div class="bg-gray-900 px-8 py-6 relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-bl-full -mr-8 -mt-8"></div>
                            <h4 class="font-black text-white text-xs uppercase tracking-[0.3em] flex items-center gap-3 relative z-10 italic">
                                <div class="w-8 h-8 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-md border border-white/20">
                                    💰
                                </div>
                                Terminal Pembayaran
                            </h4>
                        </div>
                        <form action="{{ route('finance.payment.store', $order->id) }}" method="POST" enctype="multipart/form-data" class="p-8" onsubmit="return validatePayment(event)">
                            @csrf
                            <div class="grid grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 italic">Jenis Pembayaran</label>
                                    <select name="payment_type" class="w-full border-gray-100 bg-gray-50 rounded-xl focus:ring-[#22AF85] focus:border-[#22AF85] text-xs font-black uppercase tracking-tight py-3 px-4 shadow-inner">
                                        <option value="BEFORE">Uang Muka (DP)</option>
                                        <option value="AFTER">Angsuran / Pelunasan</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 italic">Metode Pembayaran</label>
                                    <select name="payment_method" class="w-full border-gray-100 bg-gray-50 rounded-xl focus:ring-[#22AF85] focus:border-[#22AF85] text-xs font-black uppercase tracking-tight py-3 px-4 shadow-inner">
                                        <option value="Cash">Tunai</option>
                                        <option value="Transfer">Transfer Bank</option>
                                        <option value="QRIS">QRIS</option>
                                        <option value="Debit">Kartu Debit</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-6 relative">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 italic">Jumlah Pembayaran (Rp)</label>
                                <div class="relative group/input">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 font-black italic transition-colors group-focus-within/input:text-[#22AF85]">Rp</span>
                                    <input type="number" 
                                           name="amount_total" 
                                           id="payment_amount"
                                           max="{{ $order->sisa_tagihan }}"
                                           required 
                                           class="w-full pl-12 border-gray-100 bg-gray-50 rounded-2xl focus:ring-4 focus:ring-[#22AF85]/10 focus:border-[#22AF85] text-2xl font-black italic tracking-tighter shadow-inner py-4 transition-all"
                                           placeholder="0">
                                </div>
                                <div class="flex justify-between items-center mt-2 px-1">
                                    <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest italic">Max: Rp {{ number_format($order->sisa_tagihan, 0, ',', '.') }}</p>
                                    <button type="button" onclick="document.getElementById('payment_amount').value = '{{ $order->sisa_tagihan }}'" class="text-[9px] text-[#22AF85] font-black uppercase tracking-widest hover:underline cursor-pointer">Set Maximum</button>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 italic">Waktu Pembayaran</label>
                                    <input type="datetime-local" name="paid_at" value="{{ date('Y-m-d\TH:i') }}" class="w-full border-gray-100 bg-gray-50 rounded-xl focus:ring-[#22AF85] focus:border-[#22AF85] text-xs font-black uppercase tracking-tight py-3 px-4 shadow-inner">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 italic">Bukti Audit (Gambar)</label>
                                    <div class="relative group/upload h-[42px]">
                                        <input type="file" 
                                               name="proof_image" 
                                               accept="image/jpeg,image/png,image/jpg"
                                               onchange="previewImage(event)"
                                               class="absolute inset-0 opacity-0 z-10 cursor-pointer">
                                        <div class="h-full w-full bg-gray-50 border border-gray-100 rounded-xl flex items-center justify-center gap-2 group-hover/upload:bg-white group-hover/upload:border-[#22AF85]/30 transition-all shadow-inner">
                                            <svg class="w-4 h-4 text-gray-400 group-hover/upload:text-[#22AF85]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest group-hover/upload:text-[#22AF85]">Lampirkan Bukti</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="image_preview" class="mb-6 hidden">
                                <div class="relative rounded-2xl overflow-hidden border-2 border-[#22AF85]/20 shadow-xl">
                                    <img id="preview_img" src="" alt="Preview" class="w-full h-48 object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent flex items-end p-4">
                                        <span class="text-white text-[10px] font-black uppercase tracking-widest">Image Preview Loaded</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-8">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 italic">Audit Memo</label>
                                <textarea name="notes" rows="2" class="w-full border-gray-100 bg-gray-50 rounded-xl focus:ring-[#22AF85] focus:border-[#22AF85] text-xs font-medium py-3 px-4 shadow-inner" placeholder="Optional audit notes..."></textarea>
                            </div>

                            <button type="submit" class="w-full bg-[#22AF85] hover:bg-[#1A8A6A] text-white font-black py-5 px-8 rounded-[1.5rem] shadow-2xl shadow-emerald-100 hover:shadow-emerald-200 transition-all active:scale-95 uppercase tracking-[0.2em] italic text-sm flex items-center justify-center gap-3 group/save">
                                <svg class="w-5 h-5 group-hover/save:scale-125 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Simpan Transaksi
                            </button>
                        </form>
                    </div>
                    @else
                    <div class="bg-emerald-50 rounded-[2.5rem] p-12 text-center border border-emerald-100 shadow-2xl relative overflow-hidden group">
                        <div class="absolute -top-12 -left-12 w-48 h-48 bg-emerald-100/50 rounded-full blur-3xl group-hover:bg-emerald-200/50 transition-colors duration-1000"></div>
                        <div class="relative z-10">
                            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-3xl mb-8 shadow-xl border border-emerald-100 scale-110">
                                <svg class="w-10 h-10 text-[#22AF85]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <h3 class="text-3xl font-black text-gray-900 mb-2 italic">Protocol: Complete</h3>
                            <p class="text-gray-400 text-xs font-black uppercase tracking-[0.2em]">Akun Lunas & Ditutup</p>
                        </div>
                    </div>
                    @endif

                    {{-- Elite Payment History Timeline --}}
                    <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-gray-100">
                        <div class="bg-gray-50/50 px-8 py-6 border-b border-gray-100">
                            <h4 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] flex items-center gap-2">
                                <div class="w-8 h-8 bg-white rounded-xl flex items-center justify-center border border-gray-100 shadow-sm leading-none">
                                    📅
                                </div>
                                Riwayat Pembayaran
                            </h4>
                        </div>
                        <div class="p-8">
                            @php $paidAccumulated = 0; @endphp
                            @forelse($order->payments->sortBy('paid_at') as $payment)
                                @php $paidAccumulated += $payment->amount_total; @endphp
                                <div class="flex gap-8 {{ !$loop->last ? 'pb-10 mb-10' : '' }} group/item">
                                    {{-- Elite Timeline Connector --}}
                                    <div class="flex flex-col items-center">
                                        <div class="w-6 h-6 rounded-full bg-white border-[3px] border-[#22AF85] shadow-[0_0_12px_rgba(34,175,133,0.3)] z-10 group-hover/item:scale-125 transition-transform duration-500"></div>
                                        @if(!$loop->last)
                                            <div class="w-[2px] flex-1 bg-gradient-to-b from-[#22AF85] to-gray-50 mt-2"></div>
                                        @endif
                                    </div>
                                    
                                    {{-- Elite Payment Card --}}
                                    <div class="flex-1 bg-white rounded-3xl p-6 shadow-xl border border-gray-100 hover:border-[#22AF85]/30 transition-all duration-500 hover:shadow-[#22AF85]/5 relative overflow-hidden">
                                        <div class="absolute top-0 right-0 w-24 h-24 bg-gray-50 rounded-bl-[3rem] -mr-4 -mt-4 -z-0"></div>
                                        <div class="relative z-10">
                                            <div class="flex items-start justify-between mb-4">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-3 mb-2">
                                                        <span class="px-2 py-0.5 bg-emerald-50 text-[#22AF85] text-[9px] font-black rounded-lg border border-emerald-100 uppercase tracking-widest italic shadow-sm">
                                                            {{ $payment->payment_method }}
                                                        </span>
                                                        <span class="text-[10px] font-black text-gray-300 uppercase tracking-widest leading-none">TRX-{{ $payment->id }}</span>
                                                    </div>
                                                    <h5 class="font-black text-gray-900 text-xl tracking-tight italic leading-none mb-2">
                                                        @if($payment->type === 'BEFORE')
                                                            Uang Muka (DP)
                                                        @elseif($paidAccumulated < $order->total_transaksi)
                                                            Angsuran / Cicilan
                                                        @else
                                                            Pelunasan Akhir
                                                        @endif
                                                    </h5>
                                                    <p class="text-[9px] text-gray-400 font-black uppercase tracking-[0.2em] flex items-center gap-2">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-200"></span>
                                                        {{ $payment->paid_at->format('d M Y') }}
                                                        <span class="text-gray-200">/</span>
                                                        {{ $payment->paid_at->format('H:i') }}
                                                    </p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="font-black text-gray-900 text-2xl tracking-tighter italic leading-none mb-1">Rp {{ number_format($payment->amount_total, 0, ',', '.') }}</p>
                                                    <p class="text-[8px] font-black text-[#22AF85] uppercase tracking-widest">Validated</p>
                                                </div>
                                            </div>
                                            
                                            @if($payment->notes)
                                                <div class="bg-[#F8FAFC] p-4 rounded-2xl border border-gray-50 mb-4 shadow-inner">
                                                    <p class="text-xs text-gray-500 font-medium italic">"{{ $payment->notes }}"</p>
                                                </div>
                                            @endif
                                            
                                            <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                                                <div class="flex items-center gap-4">
                                                    @if($payment->proof_image)
                                                        <button onclick="showProofLightbox('{{ asset($payment->proof_image) }}')" 
                                                                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 text-white hover:bg-[#22AF85] rounded-xl text-[9px] font-black uppercase tracking-widest transition-all shadow-xl active:scale-95">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                            </svg>
                                                            View Evidence
                                                        </button>
                                                    @else
                                                        <span class="text-[9px] font-black text-gray-300 uppercase tracking-widest italic">No Media Evidence</span>
                                                    @endif
                                                </div>
                                                <div class="flex flex-col items-end">
                                                    <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Authenticated By</span>
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-5 h-5 bg-emerald-50 rounded-lg flex items-center justify-center text-[10px] border border-emerald-100">🛡️</div>
                                                        <p class="text-[10px] font-black text-gray-900 uppercase tracking-tight italic">{{ $payment->pic->name ?? '-' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-16 bg-[#F8FAFC] rounded-[2rem] border-2 border-dashed border-gray-100">
                                    <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-3xl mb-6 shadow-xl border border-gray-50">
                                        <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                        </svg>
                                    </div>
                                    <p class="text-gray-400 text-[10px] font-black uppercase tracking-[0.2em]">Zero Transactions Detected</p>
                                    <p class="text-gray-900 font-black italic mt-2">Awaiting Initial Injection</p>
                                </div>
                            @endforelse
                        </div>
                        @if($order->payments->count() > 0)
                        <div class="bg-gray-900 px-8 py-8 flex justify-between items-center relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-bl-full -mr-8 -mt-8"></div>
                            <div class="relative z-10 flex flex-col">
                                <span class="text-[9px] font-black text-white/50 uppercase tracking-[0.3em] mb-1 italic">Consolidated Total</span>
                                <span class="font-black text-white text-xs uppercase tracking-widest flex items-center gap-2">
                                    <div class="w-1.5 h-1.5 rounded-full bg-[#22AF85]"></div>
                                    Total Terbayar
                                </span>
                            </div>
                            <span class="relative z-10 font-black text-[#22AF85] text-4xl tracking-tighter italic">Rp {{ number_format($order->payments->sum('amount_total'), 0, ',', '.') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Edit Ongkir --}}
    <div x-data="{ 
            show: false, 
            loading: false,
            checkingRates: false,
            tab: 'manual', // manual | check
            type: '{{ $order->shipping_type ?? 'Ekspedisi' }}',
            zone: '{{ $order->shipping_zone ?? 'Custom' }}',
            cost: {{ $order->shipping_cost ?? 0 }},
            weight: 1000,
            searchQuery: '',
            searchResults: [],
            selectedDestination: null,
            rates: [],
            zones: {
                'Self-Pickup': 0,
                'Zona 1: Dalam Kota': 15000,
                'Zona 2: Luar Kota': 25000,
                'Zona 3: Luar Provinsi': 45000,
                'Custom': {{ $order->shipping_cost ?? 0 }}
            },
            init() {
                this.$watch('zone', value => {
                    if (this.tab === 'manual' && value !== 'Custom') {
                        this.cost = this.zones[value];
                    }
                });
            },
            searchLocation() {
                if (this.searchQuery.length < 3) return;
                fetch(`{{ route('finance.shipping.search') }}?q=${this.searchQuery}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                })
                .then(res => res.json())
                .then(data => this.searchResults = data);
            },
            selectLocation(loc) {
                this.selectedDestination = loc;
                this.searchResults = [];
                this.searchQuery = loc.text;
                this.checkRates();
            },
            useCustomerLocation(cityId, cityName) {
                this.selectedDestination = { id: cityId, text: cityName };
                this.searchQuery = cityName;
                this.checkRates();
            },
            checkRates() {
                if (!this.selectedDestination) return;
                this.checkingRates = true;
                this.rates = [];
                
                fetch(`{{ route('finance.shipping.rates') }}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({
                        destination: this.selectedDestination.id,
                        weight: this.weight
                    })
                })
                .then(res => res.json())
                .then(data => {
                    this.checkingRates = false;
                    if (data.success) {
                        this.rates = data.rates;
                    }
                })
                .catch(() => this.checkingRates = false);
            },
            applyRate(rate) {
                this.cost = rate.cost;
                this.type = rate.courier + ' ' + rate.service;
                this.zone = 'Custom'; // Set to custom so it doesn't auto-reset
                this.tab = 'manual'; // Switch back to manual tab to save
            }
         }" 
         x-show="show" 
         @open-shipping-modal.window="show = true"
         @close-shipping-modal.window="show = false"
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="show = false">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-[#22AF85]/10 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-[#22AF85]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-black text-gray-900 mb-4">Pengaturan Pengiriman</h3>
                            
                            {{-- Tabs --}}
                            <div class="flex border-b border-gray-200 mb-4">
                                <button @click="tab = 'manual'" :class="{'border-[#22AF85] text-[#22AF85]': tab === 'manual', 'border-transparent text-gray-500': tab !== 'manual'}" class="flex-1 py-2 px-4 text-center border-b-2 font-bold text-xs uppercase tracking-wider focus:outline-none transition-colors">
                                    Input Manual
                                </button>
                                {{-- Hidden due to API Issues (410) --}}
                                <button disabled class="flex-1 py-2 px-4 text-center border-b-2 border-transparent text-gray-300 cursor-not-allowed font-bold text-xs uppercase tracking-wider" title="Fitur dinonaktifkan sementara">
                                    Cek Tarif (Non-Aktif)
                                </button>
                            </div>

                            {{-- Tab Manual --}}
                            <div x-show="tab === 'manual'" class="space-y-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Jenis Kurir</label>
                                    <input type="text" x-model="type" class="w-full border-gray-300 rounded-xl focus:ring-[#22AF85] focus:border-[#22AF85] text-sm" placeholder="Contoh: JNE REG / Pickup">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Zona Pengiriman</label>
                                    <select x-model="zone" class="w-full border-gray-300 rounded-xl focus:ring-teal-500 focus:border-teal-500 text-sm">
                                        <template x-for="(price, name) in zones">
                                            <option :value="name" x-text="name"></option>
                                        </template>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Nominal Ongkir (Rp)</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">Rp</span>
                                        <input type="number" x-model="cost" 
                                               class="w-full pl-12 border-gray-300 rounded-xl focus:ring-teal-500 focus:border-teal-500 text-lg font-black"
                                               placeholder="0">
                                    </div>
                                    <p class="mt-1 text-[10px] text-gray-500 italic" x-show="zone !== 'Custom'">* Nominal otomatis berdasarkan zona</p>
                                </div>
                            </div>
                            
                            {{-- Tab Check Rates --}}
                            <div x-show="tab === 'check'" class="space-y-4">
                                <div class="bg-blue-50 p-3 rounded-lg text-xs text-blue-700 mb-2">
                                    Fitur ini menggunakan integrasi <strong>RajaOngkir</strong> (Starter) untuk cek estimasi ongkir real-time.
                                </div>
                                
                                {{-- Customer Location Shortcuts --}}
                                @if($order->customer && $order->customer->city_id)
                                <div class="mb-4 p-3 bg-[#22AF85]/10 border border-[#22AF85]/30 rounded-lg flex items-center justify-between">
                                    <div class="text-xs text-gray-700">
                                        <span class="font-bold block">Lokasi Customer Terdaftar:</span>
                                        {{ $order->customer->city }}
                                    </div>
                                    <button @click="useCustomerLocation({{ $order->customer->city_id }}, '{{ $order->customer->city }}')" 
                                            class="px-3 py-1 bg-[#22AF85] text-white text-[10px] font-bold rounded-md hover:bg-[#1A8A6A] transition shadow">
                                        Gunakan Lokasi Ini
                                    </button>
                                </div>
                                @endif

                                <div class="relative">
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Cari Kota / Kabupaten Tujuan</label>
                                    <input type="text" x-model="searchQuery" @input.debounce.500ms="searchLocation()" 
                                           class="w-full border-gray-300 rounded-xl focus:ring-teal-500 focus:border-teal-500 text-sm" 
                                           placeholder="Ketik nama kota (min 3 huruf)...">
                                    
                                    {{-- Search Results Dropdown (High Z-Index) --}}
                                    <div x-show="searchResults.length > 0" class="absolute z-[100] w-full bg-white border border-gray-200 mt-1 rounded-lg shadow-xl max-h-48 overflow-y-auto">
                                        <ul>
                                            <template x-for="res in searchResults">
                                                <li @click="selectLocation(res)" class="px-4 py-2 hover:bg-gray-50 cursor-pointer text-xs border-b border-gray-100 last:border-0">
                                                    <span x-text="res.text" class="font-medium text-gray-700"></span>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Berat (Gram)</label>
                                    <div class="flex gap-2">
                                        <input type="number" x-model="weight" class="w-24 border-gray-300 rounded-xl text-center focus:ring-teal-500 focus:border-teal-500 text-sm" value="1000">
                                        <button @click="checkRates()" class="px-3 py-2 bg-gray-200 hover:bg-gray-300 rounded-xl text-xs font-bold transition-colors">
                                            Hitung Ulang
                                        </button>
                                    </div>
                                </div>
                                
                                {{-- Rate Results --}}
                                <div class="mt-4 border-t pt-4">
                                    <p x-show="checkingRates" class="text-center text-sm text-gray-500 animate-pulse">Sedang memuat tarif...</p>
                                    
                                    <div x-show="!checkingRates && rates.length > 0" class="space-y-2 max-h-48 overflow-y-auto pr-2">
                                        <template x-for="rate in rates">
                                            <div @click="applyRate(rate)" class="flex justify-between items-center p-3 border border-gray-200 rounded-lg hover:border-[#22AF85] hover:bg-[#22AF85]/10 cursor-pointer group transition-all">
                                                <div>
                                                    <div class="font-bold text-gray-800 text-sm" x-text="rate.courier + ' ' + rate.service"></div>
                                                    <div class="text-[10px] text-gray-500" x-text="'Estimasi: ' + rate.etd"></div>
                                                </div>
                                                <div class="text-right">
                                                    <div class="font-black text-[#22AF85] text-sm" x-text="'Rp ' + rate.cost.toLocaleString('id-ID')"></div>
                                                    <div class="text-[10px] text-[#22AF85] opacity-0 group-hover:opacity-100 font-bold uppercase tracking-wider">Pilih</div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    
                                    <div x-show="!checkingRates && rates.length === 0 && selectedDestination" class="text-center text-gray-400 text-xs py-4">
                                        Tidak ada layanan tersedia.
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button type="button" @click="saveShipping(cost, type, zone)" :disabled="loading"
                            class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-[#FFC232] text-base font-bold text-gray-900 hover:bg-[#FFD666] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#FFC232] sm:w-auto sm:text-sm disabled:opacity-50">
                        <span x-show="!loading">Simpan Perubahan</span>
                        <span x-show="loading">Memproses...</span>
                    </button>
                    <button type="button" @click="show = false"
                            class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Lightbox Modal for Proof Images --}}
    <div x-data="{ showLightbox: false, lightboxImage: '' }" 
         x-show="showLightbox" 
         @click="showLightbox = false"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 backdrop-blur-sm"
         style="display: none;"
         x-transition>
        <div class="relative max-w-4xl max-h-screen p-4">
            <button @click="showLightbox = false" class="absolute top-2 right-2 p-2 bg-white/20 hover:bg-white/30 rounded-full text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <img :src="lightboxImage" class="max-w-full max-h-screen rounded-lg shadow-2xl" @click.stop>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Image Preview Function
        function previewImage(event) {
            const file = event.target.files[0];
            if (!file) return;
            
            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Terlalu Besar!',
                    text: 'Ukuran file maksimal 5MB',
                    confirmButtonColor: '#14B8A6'
                });
                event.target.value = '';
                return;
            }
            
            // Validate file type
            if (!['image/jpeg', 'image/png', 'image/jpg'].includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Format Tidak Valid!',
                    text: 'Hanya file JPG dan PNG yang diperbolehkan',
                    confirmButtonColor: '#14B8A6'
                });
                event.target.value = '';
                return;
            }
            
            // Show preview
            // Read image and display
            reader.onload = function(e){
                const preview = document.getElementById('preview_img');
                const container = document.getElementById('image_preview');
                preview.src = e.target.result;
                container.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }

        // --- NEW: Update Due Date via AJAX ---
        function updateDueDate(date) {
            if(!date) return;

            fetch('{{ route('finance.update-due-date', $order->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ payment_due_date: date })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'success',
                        title: 'Tanggal Jatuh Tempo Diperbarui'
                    });
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Gagal menyimpan tanggal jatuh tempo', 'error');
            });
        }
        
        // Shipping Modal Functions

        // Payment Validation with SweetAlert
        function validatePayment(event) {
            event.preventDefault();
            
            const amount = parseInt(document.getElementById('payment_amount').value);
            const maxAmount = {{ $order->sisa_tagihan }};
            
            if (!amount || amount <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Jumlah Tidak Valid!',
                    text: 'Jumlah pembayaran harus lebih dari 0',
                    confirmButtonColor: '#14B8A6'
                });
                return false;
            }
            
            if (amount > maxAmount) {
                Swal.fire({
                    icon: 'error',
                    title: 'Melebihi Sisa Tagihan!',
                    html: `Jumlah pembayaran tidak boleh melebihi sisa tagihan<br><strong>Maksimal: Rp ${maxAmount.toLocaleString('id-ID')}</strong>`,
                    confirmButtonColor: '#14B8A6'
                });
                return false;
            }
            
            // Confirmation
            Swal.fire({
                title: 'Konfirmasi Pembayaran',
                html: `Simpan pembayaran sebesar<br><strong class="text-2xl text-teal-600">Rp ${amount.toLocaleString('id-ID')}</strong>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#14B8A6',
                cancelButtonColor: '#6B7280',
                confirmButtonText: '✓ Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.submit();
                }
            });
            
            return false;
        }

        // Show Lightbox for Proof Image
        function showProofImage(imagePath) {
            Alpine.store('lightbox', {
                show: true,
                image: imagePath
            });
        }

        // Show Proof Lightbox (Alternative method using Alpine directly)
        function showProofLightbox(imagePath) {
            // Find the lightbox element and trigger it
            const lightboxDiv = document.querySelector('[x-data*="showLightbox"]');
            if (lightboxDiv) {
                Alpine.$data(lightboxDiv).showLightbox = true;
                Alpine.$data(lightboxDiv).lightboxImage = imagePath;
            }
        }

        // Confirm Move to Workshop
        function confirmMove(orderId) {
            Swal.fire({
                title: 'Pindahkan ke Workshop?',
                text: 'Order akan dipindahkan ke proses Preparation',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#14B8A6',
                cancelButtonColor: '#6B7280',
                confirmButtonText: '✓ Ya, Pindahkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/finance/${orderId}/update-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ action: 'move_to_prep' })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                confirmButtonColor: '#14B8A6'
                            }).then(() => location.reload());
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: data.message,
                                confirmButtonColor: '#14B8A6'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat memproses request',
                            confirmButtonColor: '#14B8A6'
                        });
                    });
                }
            });
        }

        function editShipping() {
            window.dispatchEvent(new CustomEvent('open-shipping-modal'));
        }

        function saveShipping(amount, type, zone) {
            // Access Alpine data from the modal element
            const modalEl = document.querySelector('[x-data*="show: false"]');
            const alpineData = Alpine.$data(modalEl);
            
            alpineData.loading = true;

            fetch("{{ route('finance.shipping.update', $order->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ 
                    shipping_cost: amount,
                    shipping_type: type,
                    shipping_zone: zone
                })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alpineData.show = false;
                    
                    // Update UI elements
                    document.getElementById('display-shipping').innerText = 'Rp ' + data.new_shipping;
                    document.getElementById('display-shipping-zone').innerText = zone + ' (' + type + ')';
                    document.getElementById('display-total-transaksi').innerText = 'Rp ' + data.new_total;
                    
                    // Update summary cards if they exist in the DOM
                    // Total Tagihan card
                    const tagihanH3 = document.querySelector('h3.text-3xl.font-black.text-gray-900.mt-2');
                    if(tagihanH3) tagihanH3.innerText = 'Rp ' + data.new_total;
                    
                    // Sisa Tagihan card
                    const sisaTagihanH3 = document.querySelector('h3.text-3xl.font-black.text-red-600.mt-2') || document.querySelector('h3.text-3xl.font-black.text-gray-400.mt-2');
                    if(sisaTagihanH3) sisaTagihanH3.innerText = 'Rp ' + data.new_sisa;

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            })
            .finally(() => {
                alpineData.loading = false;
            });
        }

        // Success/Error Messages
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session("success") }}',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session("error") }}',
                confirmButtonColor: '#14B8A6'
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal!',
                html: '<ul class="text-left">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                confirmButtonColor: '#14B8A6'
            });
        @endif
    </script>
</x-app-layout>

