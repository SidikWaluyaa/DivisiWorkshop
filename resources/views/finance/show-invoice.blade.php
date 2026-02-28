<x-app-layout>
<div class="min-h-screen bg-[#F8FAFC]">
    {{-- Elite Multi-Layer Header --}}
    <div class="bg-gray-900 pt-12 pb-24 relative overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-gradient-to-br from-[#1B8A68]/20 to-transparent mix-blend-overlay"></div>
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-[#FFC232]/10 rounded-full blur-[100px]"></div>
            <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-[#1B8A68]/10 rounded-full blur-[100px]"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-10">
                <div class="flex items-center gap-8">
                    <a href="{{ route('finance.invoices.index') }}" class="group flex items-center justify-center w-14 h-14 bg-white/5 rounded-[1.5rem] border border-white/10 text-white hover:bg-white/10 transition-all hover:-translate-x-1 active:scale-90">
                        <svg class="w-6 h-6 text-white/50 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                    </a>
                    <div>
                        <div class="flex items-center gap-4 mb-2">
                            <h1 class="text-5xl font-black text-white italic tracking-tighter leading-none uppercase">Rincian Invoice</h1>
                            @php
                                $statusBadge = match($invoice->status) {
                                    'Lunas' => 'bg-[#1B8A68]/20 text-[#1B8A68] border-[#1B8A68]/30',
                                    'DP/Cicil' => 'bg-[#FFC232]/20 text-[#FFC232] border-[#FFC232]/30',
                                    default => 'bg-white/10 text-white/50 border-white/10'
                                };
                            @endphp
                            <span class="text-[10px] font-black uppercase tracking-[0.3em] px-4 py-1.5 rounded-full border {{ $statusBadge }} italic">
                                {{ $invoice->status }}
                            </span>
                        </div>
                        <p class="text-white/40 font-black text-xs uppercase tracking-[0.4em] italic flex items-center gap-3">
                            <span class="w-2 h-2 rounded-full bg-[#1B8A68]"></span>
                            No: {{ $invoice->invoice_number }} &bull; Dibuat {{ $invoice->created_at->format('d/m/Y - H:i') }}
                        </p>
                    </div>
                </div>

                {{-- Primary Action Slot --}}
                <div class="flex flex-col items-center group">
                    <span class="text-[10px] font-black text-white/30 uppercase tracking-[0.5em] italic mb-4 group-hover:text-[#FFC232] transition-colors">Cetak Nota Gabungan</span>
                    <a href="{{ url('/api/invoice_share_grouped.php?token='.urlencode($invoice->invoice_number).'&type=awal') }}" 
                       target="_blank" 
                       class="w-16 h-16 rounded-[2rem] bg-[#FFC232] flex items-center justify-center text-gray-900 shadow-[0_20px_40px_-10px_rgba(255,194,50,0.5)] hover:scale-110 transition-all duration-500 active:scale-95 border-4 border-white/10 group-hover:rotate-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="max-w-7xl mx-auto px-6 -mt-8 relative z-30">
            <div class="bg-white rounded-3xl border border-[#1B8A68]/20 p-6 shadow-2xl flex items-center gap-4 animate-bounce">
                <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center text-[#1B8A68]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <p class="text-sm font-black text-gray-900 italic tracking-tight uppercase">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    {{-- Content Layout --}}
    <div class="max-w-7xl mx-auto px-6 -mt-12 relative z-20">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            
            {{-- Main Data Stream --}}
            <div class="lg:col-span-2 space-y-10">
                {{-- Subject High-End Card --}}
                <div class="bg-white rounded-[3rem] p-10 shadow-2xl border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-8 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-[#1B8A68]/5 rounded-bl-[5rem] -mr-8 -mt-8 transition-transform group-hover:scale-125 duration-700"></div>
                    
                    <div class="flex items-center gap-8 relative z-10">
                        <div class="w-20 h-20 rounded-[2rem] bg-[#F8FAFC] flex items-center justify-center text-3xl shadow-inner border border-gray-100 group-hover:-rotate-12 transition-transform">👤</div>
                        <div>
                            <span class="text-[11px] font-black text-[#1B8A68] uppercase tracking-[0.4em] mb-2 block italic">Data Pelanggan</span>
                            <div class="text-4xl font-black text-gray-900 italic tracking-tighter leading-none uppercase mb-2">{{ $invoice->customer->name }}</div>
                            <div class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] italic opacity-60 flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#1B8A68]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                {{ $invoice->customer->phone }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SPK Segment Analysis --}}
                <div class="space-y-6">
                    <h2 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.5em] italic flex items-center gap-4">
                        Rincian Pesanan Terkait
                        <div class="h-px flex-1 bg-gray-100"></div>
                    </h2>
                    
                    @foreach($invoice->workOrders as $order)
                        <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-2xl hover:shadow-[#1B8A68]/5 transition-all group/item overflow-hidden relative">
                            <div class="absolute inset-y-0 left-0 w-2 bg-[#1B8A68] opacity-30 group-hover/item:opacity-100 transition-opacity"></div>
                            
                            <div class="flex flex-col md:flex-row justify-between gap-10">
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-end gap-3 mb-6">
                                        <a href="{{ route('finance.show', $order->id) }}" class="text-2xl font-black text-gray-900 group-hover/item:text-[#1B8A68] italic tracking-tight uppercase leading-none transition-colors">
                                            {{ $order->spk_number }}
                                        </a>
                                        @if($order->cs_code)
                                            <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black bg-emerald-50 text-[#1B8A68] uppercase tracking-[0.2em] border border-emerald-100 italic">GATEWAY: {{ $order->cs_code }}</span>
                                        @endif
                                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-[0.2em] border italic {{ $order->status_pembayaran === 'L' ? 'bg-emerald-50 text-[#1B8A68] border-emerald-100' : 'bg-amber-50 text-[#FFC232] border-amber-100' }}">
                                            {{ $order->status_pembayaran === 'L' ? 'LUNAS' : 'DP/CICIL' }}
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-4 mb-6">
                                        <div class="w-1.5 h-1.5 rounded-full bg-[#1B8A68]"></div>
                                        <span class="text-sm font-black text-gray-700 italic uppercase tracking-tight">{{ $order->shoe_brand }} &bull; {{ $order->shoe_type }}</span>
                                    </div>

                                    <div class="space-y-2 pl-5 border-l-2 border-gray-50">
                                        @foreach($order->workOrderServices as $svc)
                                            <div class="flex items-center gap-4 py-1">
                                                <span class="text-[10px] text-[#1B8A68] font-black">●</span>
                                                <p class="text-[11px] font-black text-gray-500 uppercase tracking-widest italic opacity-80">{{ $svc->custom_service_name ?? ($svc->service ? $svc->service->name : 'Layanan Custom') }}</p>
                                                <div class="h-px flex-1 bg-gray-50 bg-dotted border-b border-gray-100"></div>
                                                <span class="text-[11px] font-black text-gray-900 italic tabular-nums">Rp {{ number_format($svc->cost, 0, ',', '.') }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="md:text-right p-8 bg-[#F8FAFC] rounded-[2rem] border border-gray-100 min-w-[280px] flex flex-col justify-center gap-4 group-hover/item:bg-white transition-colors duration-500">
                                    <div class="flex justify-between items-center">
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest italic leading-none">Subtotal SPK</span>
                                        <span class="text-sm font-black text-gray-900 italic tracking-tighter">Rp {{ number_format($order->total_transaksi, 0, ',', '.') }}</span>
                                    </div>
                                    @if($order->discount > 0)
                                        <div class="flex justify-between items-center text-rose-500">
                                            <span class="text-[10px] font-black uppercase tracking-widest italic leading-none">Potongan</span>
                                            <span class="text-sm font-black italic tracking-tighter">-Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                                        </div>
                                    @endif
                                    <div class="flex justify-between items-center text-[#1B8A68] pb-4 border-b border-gray-200">
                                        <span class="text-[10px] font-black uppercase tracking-widest italic leading-none">Telah Dibayar</span>
                                        <span class="text-sm font-black italic tracking-tighter">Rp {{ number_format($order->total_paid, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between items-end pt-2">
                                        <div class="flex flex-col items-start gap-1">
                                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] italic">Sisa Tagihan</span>
                                            <div class="text-2xl font-black text-gray-900 italic tracking-tighter tabular-nums leading-none">Rp {{ number_format($order->sisa_tagihan, 0, ',', '.') }}</div>
                                        </div>
                                        <a href="{{ route('reception.print-tag', $order->id) }}" target="_blank" class="w-12 h-12 rounded-full bg-white border border-gray-100 text-[#1B8A68] shadow-xl flex items-center justify-center hover:scale-110 hover:-rotate-12 transition-all active:scale-95 group/btn3">
                                            <svg class="w-5 h-5 group-hover/btn3:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Sidebar Stack --}}
            <div class="space-y-10">
                {{-- Global Asset Summary Card --}}
                <div class="bg-gray-900 rounded-[3rem] p-10 shadow-[0_35px_60px_-15px_rgba(0,0,0,0.3)] relative overflow-hidden group">
                    <div class="absolute top-0 left-0 w-32 h-32 bg-[#1B8A68]/10 rounded-br-[5rem] -ml-8 -mt-8"></div>
                    
                    <h3 class="text-[11px] font-black text-[#1B8A68] uppercase tracking-[0.5em] mb-10 italic flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-[#1B8A68]"></div>
                        Rekapitulasi Keuangan
                    </h3>
                    
                    <div class="space-y-6 relative pb-10 border-b border-white/10 mb-10">
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-black text-white/40 uppercase tracking-widest italic">Total Harga Layanan</span>
                            <span class="text-sm font-black text-white italic tabular-nums tracking-tighter">Rp {{ number_format($invoice->workOrders->sum('total_transaksi'), 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-black text-white/40 uppercase tracking-widest italic group-hover:text-[#1B8A68] transition-colors">Biaya Pengiriman Global</span>
                            <span class="text-sm font-black text-white italic tabular-nums tracking-tighter">Rp {{ number_format($invoice->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        
                        {{-- Logistical Update Module --}}
                        <form action="{{ route('finance.invoices.update-shipping', $invoice->id) }}" method="POST" class="mt-8 p-6 bg-white/5 rounded-[2rem] border border-white/10" x-data="{ editing: false }">
                            @csrf
                            <div x-show="!editing" class="flex justify-between items-center group/edit">
                                <span class="text-[10px] font-black text-white/30 uppercase tracking-widest italic group-hover/edit:text-white/60 transition-colors">Iput/Ubah Ongkir</span>
                                <button type="button" @click="editing = true" class="w-10 h-10 rounded-full bg-[#FFC232] flex items-center justify-center text-gray-900 shadow-lg shadow-amber-500/20 hover:scale-110 active:scale-90 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                            </div>
                            <div x-show="editing" class="flex flex-col gap-4" style="display: none;">
                                <div class="relative group/input">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-white/20 text-xs font-black italic">RP</span>
                                    <input type="number" name="shipping_cost" value="{{ $invoice->shipping_cost }}" class="w-full pl-12 pr-4 py-3 bg-white/10 border border-white/10 rounded-xl text-white font-black italic tracking-tighter focus:ring-2 focus:ring-[#1B8A68]/50 focus:border-transparent transition-all shadow-inner" placeholder="0">
                                </div>
                                <div class="flex gap-2">
                                    <button type="submit" class="flex-1 bg-[#1B8A68] text-white py-3 rounded-xl text-[10px] font-black uppercase tracking-widest italic shadow-lg shadow-emerald-500/20 hover:bg-[#146B50] transition-all">SIMPAN</button>
                                    <button type="button" @click="editing = false" class="px-4 py-3 bg-white/5 text-white/50 border border-white/10 rounded-xl hover:text-white transition-colors">X</button>
                                </div>
                            </div>
                        </form>

                        {{-- Estimasi Selesai Module --}}
                        <form action="{{ route('finance.invoices.update-estimasi', $invoice->id) }}" method="POST" class="mt-4 p-6 bg-white/5 rounded-[2rem] border border-white/10" x-data="{ editing: false }">
                            @csrf
                            <div x-show="!editing" class="flex justify-between items-center group/edit">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-white/30 uppercase tracking-widest italic group-hover/edit:text-white/60 transition-colors">Estimasi Selesai</span>
                                    <span class="text-xs font-black text-[#FFC232] italic tracking-tight uppercase">
                                        {{ $invoice->estimasi_selesai ? \Carbon\Carbon::parse($invoice->estimasi_selesai)->format('d M Y') : 'Belum Atur' }}
                                    </span>
                                </div>
                                <button type="button" @click="editing = true" class="w-10 h-10 rounded-full bg-[#1B8A68] flex items-center justify-center text-white shadow-lg shadow-emerald-500/20 hover:scale-110 active:scale-90 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </button>
                            </div>
                            <div x-show="editing" class="flex flex-col gap-4" style="display: none;">
                                <div class="relative group/input">
                                    <input type="date" name="estimasi_selesai" value="{{ $invoice->estimasi_selesai ? \Carbon\Carbon::parse($invoice->estimasi_selesai)->format('Y-m-d') : '' }}" class="w-full px-4 py-3 bg-white/10 border border-white/10 rounded-xl text-white font-black italic tracking-tighter focus:ring-2 focus:ring-[#1B8A68]/50 focus:border-transparent transition-all shadow-inner [color-scheme:dark]">
                                </div>
                                <div class="flex gap-2">
                                    <button type="submit" class="flex-1 bg-[#FFC232] text-gray-900 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest italic shadow-lg shadow-amber-500/20 hover:bg-[#e6af2d] transition-all">UPDATE ESTIMASI</button>
                                    <button type="button" @click="editing = false" class="px-4 py-3 bg-white/5 text-white/50 border border-white/10 rounded-xl hover:text-white transition-colors">X</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="space-y-4 mb-10">
                        <div class="flex justify-between items-end">
                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-black text-[#1B8A68] uppercase tracking-[0.3em] italic">Total Terbayar</span>
                                <span class="text-3xl font-black text-[#1B8A68] italic tracking-tighter tabular-nums leading-none">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-8 bg-white/5 rounded-[2.5rem] border border-white/10 group-hover:bg-[#1B8A68]/10 transition-colors duration-700">
                        <span class="text-[10px] font-black text-white/30 uppercase tracking-[0.4em] mb-2 block italic">Sisa Tagihan Akhir</span>
                        <div class="text-4xl font-black text-[#FFC232] italic tracking-tighter leading-none tabular-nums shadow-amber-500/20 drop-shadow-lg">
                            Rp {{ number_format($invoice->remaining_balance, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                {{-- Protocol Sync Guard --}}
                <div class="bg-emerald-50 rounded-[2.5rem] p-10 border border-emerald-100 shadow-2xl relative overflow-hidden group">
                    <div class="absolute bottom-0 right-0 w-24 h-24 bg-[#1B8A68]/5 rounded-tl-[4rem] group-hover:scale-150 transition-transform duration-1000"></div>
                    <div class="flex items-center gap-5 mb-6">
                        <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-xl shadow-lg border border-emerald-100">🛡️</div>
                        <h4 class="text-[11px] font-black text-[#1B8A68] uppercase tracking-widest italic leading-tight">Sistem Keamanan Saldo Gabungan</h4>
                    </div>
                    <p class="text-[11px] font-black text-gray-600 bg-white/40 p-5 rounded-2xl border border-emerald-100 leading-relaxed italic opacity-80 backdrop-blur-sm">
                        Seluruh rincian harga dan status pembayaran disinkronkan secara real-time dengan data dari setiap <b>Nomor SPK Terkait</b>. Nota yang Anda cetak akan secara otomatis melampirkan rincian lengkap untuk pelanggan.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
