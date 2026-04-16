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
                <div class="flex items-center gap-6">
                    @if($invoice->status === 'Belum Bayar' && !$invoice->payments()->exists() && !$invoice->invoicePayments()->exists())
                    <div class="flex flex-col items-center group">
                        <span class="text-[10px] font-black text-white/30 uppercase tracking-[0.5em] italic mb-4 group-hover:text-red-400 transition-colors">Hapus Invoice</span>
                        <button @click="$dispatch('open-delete-modal')" class="w-16 h-16 rounded-[2rem] bg-red-500/20 flex items-center justify-center text-red-400 border-2 border-red-500/30 hover:bg-red-500 hover:text-white hover:scale-110 transition-all duration-500 active:scale-95 group-hover:rotate-6">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                    @endif

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
                            <div class="text-4xl font-black text-gray-900 italic tracking-tighter leading-none uppercase mb-2">{{ $invoice->customer?->name ?? 'Data Terhapus' }}</div>
                            <div class="text-gray-400 font-black tracking-widest uppercase italic opacity-80 flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#FFC232]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                {{ $invoice->customer?->phone ?? '-' }}
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
                                    <div class="flex flex-col gap-1 items-end">
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] italic">Subtotal SPK</span>
                                        <div class="text-2xl font-black text-gray-900 italic tracking-tighter tabular-nums leading-none">Rp {{ number_format($order->total_transaksi, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="flex justify-end mt-4">
                                        <a href="{{ route('reception.print-tag', $order->id) }}" target="_blank" class="w-12 h-12 rounded-full bg-white border border-gray-100 text-[#1B8A68] shadow-xl flex items-center justify-center hover:scale-110 hover:-rotate-12 transition-all active:scale-95 group/btn3">
                                            <svg class="w-5 h-5 group-hover/btn3:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Payment History & Verification Status --}}
                @if($invoice->invoicePayments->isNotEmpty())
                <div class="space-y-6">
                    <h2 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.5em] italic flex items-center gap-4">
                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        Riwayat Pembayaran & Verifikasi Mutasi
                        <div class="h-px flex-1 bg-gray-100"></div>
                    </h2>
                    
                    @foreach($invoice->invoicePayments as $payment)
                        @php
                            $isVerified = $payment->verified;
                            $verification = $payment->verification;
                            $mutation = $verification?->mutation;
                        @endphp
                        <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-2xl overflow-hidden relative {{ $isVerified ? 'border-l-4 border-l-emerald-400' : 'border-l-4 border-l-amber-400' }}">
                            <div class="flex flex-col md:flex-row justify-between gap-6">
                                {{-- Payment Info --}}
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-4">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-inner {{ $isVerified ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                            @if($isVerified)
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            @else
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-xl font-black text-gray-900 italic tabular-nums tracking-tighter">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
                                            <div class="text-[10px] text-gray-400 font-black uppercase tracking-widest italic">{{ $payment->payment_date->format('d M Y') }} • oleh {{ $payment->creator->name ?? '-' }}</div>
                                        </div>
                                    </div>

                                    @if($payment->notes)
                                        <div class="text-xs text-gray-500 italic bg-gray-50 rounded-xl px-4 py-2 inline-block">📝 {{ $payment->notes }}</div>
                                    @endif
                                </div>

                                {{-- Verification / Mutation Status --}}
                                <div class="md:min-w-[280px] p-6 rounded-[2rem] border {{ $isVerified ? 'bg-emerald-50/50 border-emerald-100' : 'bg-gray-50 border-gray-100' }}">
                                    @if($isVerified && $mutation)
                                        <div class="flex items-center gap-2 mb-3">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            <span class="text-[10px] font-black text-emerald-700 uppercase tracking-[0.2em] italic">Terverifikasi</span>
                                        </div>
                                        <div class="space-y-2">
                                            <div class="flex justify-between items-center">
                                                <span class="text-[10px] text-gray-500 font-black italic uppercase tracking-wider">Mutasi Bank</span>
                                                <span class="text-sm font-black text-emerald-700 italic tabular-nums">Rp {{ number_format($mutation->amount, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-[10px] text-gray-500 font-black italic uppercase tracking-wider">Bank</span>
                                                <span class="text-xs font-black text-gray-700 italic">{{ $mutation->bank_code ?: '-' }}</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-[10px] text-gray-500 font-black italic uppercase tracking-wider">Tgl Mutasi</span>
                                                <span class="text-xs font-black text-gray-700 italic">{{ $mutation->transaction_date->format('d M Y') }}</span>
                                            </div>
                                            @if($verification)
                                            <div class="pt-2 mt-2 border-t border-emerald-100 flex justify-between items-center">
                                                <span class="text-[10px] text-gray-500 font-black italic uppercase tracking-wider">Diverifikasi</span>
                                                <span class="text-[10px] font-black text-emerald-600 italic">{{ $verification->verified_at->format('d M Y H:i') }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                            <span class="text-[10px] font-black text-amber-700 uppercase tracking-[0.2em] italic">Menunggu Verifikasi</span>
                                        </div>
                                        <p class="text-[10px] text-gray-400 italic font-bold leading-relaxed">Pembayaran ini belum dicocokkan dengan mutasi bank. Buka halaman <a href="{{ route('finance.verifications.index') }}" class="text-purple-600 underline">Verifikasi Mutasi</a> untuk mencocokkan.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @endif
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

                        <div class="pt-4 border-t border-white/10 flex justify-between items-center group/total">
                            <span class="text-[10px] font-black text-[#FFC232] uppercase tracking-widest italic">Total Tagihan (Inc. Ongkir)</span>
                            <span class="text-xl font-black text-[#FFC232] italic tabular-nums tracking-tighter">Rp {{ number_format($invoice->total_amount + $invoice->shipping_cost, 0, ',', '.') }}</span>
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
                        @php $hasEstimasi = !empty($invoice->estimasi_selesai); @endphp
                        <form action="{{ route('finance.invoices.update-estimasi', $invoice->id) }}" method="POST" 
                              class="mt-4 p-6 bg-white/5 rounded-[2rem] border {{ $hasEstimasi ? 'border-white/10' : 'border-[#FFC232]/50 shadow-[0_0_20px_rgba(255,194,50,0.1)]' }} transition-all duration-500" 
                              x-data="{ editing: {{ $hasEstimasi ? 'false' : 'true' }} }">
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
                        <div class="text-4xl font-black text-[#FFC232] italic tracking-tighter leading-none tabular-nums shadow-amber-500/20 drop-shadow-lg mb-8">
                            Rp {{ number_format($invoice->remaining_balance, 0, ',', '.') }}
                        </div>
                        
                        @if($invoice->remaining_balance > 0)
                            @if($hasEstimasi)
                                <button @click="$dispatch('open-payment-modal')" class="w-full bg-[#1B8A68] hover:bg-emerald-600 text-white font-black italic tracking-widest text-sm py-4 rounded-2xl shadow-xl shadow-emerald-500/30 transition-all hover:scale-105 active:scale-95 flex items-center justify-center gap-3 relative overflow-hidden group/pay">
                                    <div class="absolute inset-0 bg-white/20 -translate-x-full group-hover/pay:animate-[shimmer_1s_infinite]"></div>
                                    <svg class="w-5 h-5 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                    <span class="relative z-10">CATAT PEMBAYARAN</span>
                                </button>
                            @else
                                <div class="relative group/warning">
                                    <button disabled class="w-full bg-gray-800 text-white/30 font-black italic tracking-widest text-[10px] py-4 rounded-2xl border border-white/5 cursor-not-allowed flex flex-col items-center justify-center gap-1">
                                        <svg class="w-4 h-4 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        SET ESTIMASI DULU UNTUK BAYAR
                                    </button>
                                </div>
                            @endif
                        @endif
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

{{-- Payment Modal --}}
<div x-data="{ 
    open: false,
    sisaTagihan: {{ $invoice->remaining_balance }}
}" 
@open-payment-modal.window="open = true"
x-show="open" 
class="fixed inset-0 z-50 overflow-y-auto" 
style="display: none;"
aria-labelledby="modal-title" role="dialog" aria-modal="true">
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="open" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity" 
             @click="open = false" aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="open" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-[0_35px_60px_-15px_rgba(0,0,0,0.5)] transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full border border-gray-100">
            
            <form action="{{ route('finance.invoices.payment', $invoice->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 px-8 py-6 border-b border-gray-700 relative overflow-hidden">
                    <div class="absolute inset-0 bg-[#1B8A68]/10 mix-blend-overlay"></div>
                    <div class="flex justify-between items-center relative z-10">
                        <div>
                            <h3 class="text-2xl font-black text-white italic tracking-tighter uppercase" id="modal-title">Catat Pembayaran</h3>
                            <p class="text-[10px] font-black text-[#1B8A68] uppercase tracking-[0.3em] mt-1">{{ $invoice->invoice_number }}</p>
                        </div>
                        <button type="button" @click="open = false" class="text-white/50 hover:text-white hover:bg-white/10 p-2 rounded-xl transition-colors">
                            <span class="sr-only">Tutup</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                </div>

                <div class="px-8 py-8 space-y-8 bg-[#F8FAFC]">
                    <!-- Amount Input -->
                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest italic mb-2">Jumlah Pembayaran</label>
                        <div class="relative group">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-black italic">Rp</span>
                            <input type="number" name="amount_total" x-model="sisaTagihan" max="{{ $invoice->remaining_balance }}" required
                                class="w-full pl-12 pr-4 py-4 bg-white border-2 border-gray-100 rounded-2xl text-2xl font-black italic tracking-tighter focus:ring-2 focus:ring-[#1B8A68]/50 focus:border-[#1B8A68] transition-all shadow-sm text-gray-900">
                        </div>
                        <p class="text-xs font-black text-rose-500 uppercase tracking-widest italic mt-2 text-right">Maksimal: Rp {{ number_format($invoice->remaining_balance, 0, ',', '.') }}</p>
                    </div>

                    <!-- Payment Details Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest italic mb-2">Tanggal Bayar</label>
                            <input type="date" name="paid_at" value="{{ date('Y-m-d') }}" required
                                class="w-full px-4 py-3 bg-white border-2 border-gray-100 rounded-xl text-sm font-black italic tracking-tighter focus:ring-2 focus:ring-[#1B8A68]/50 focus:border-[#1B8A68] transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest italic mb-2">Metode Bayar</label>
                            <select name="payment_method" required
                                class="w-full px-4 py-3 bg-white border-2 border-gray-100 rounded-xl text-sm font-black italic tracking-tighter focus:ring-2 focus:ring-[#1B8A68]/50 focus:border-[#1B8A68] transition-all">
                                <option value="BCA">Transfer BCA</option>
                                <option value="MANDIRI">Transfer Mandiri</option>
                                <option value="QRIS">QRIS</option>
                                <option value="TUNAI">Tunai / Cash</option>
                                <option value="EDC">Mesin EDC</option>
                            </select>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest italic mb-2">Tipe Pembayaran</label>
                            <select name="payment_type" required
                                class="w-full px-4 py-3 bg-white border-2 border-gray-100 rounded-xl text-sm font-black italic tracking-tighter focus:ring-2 focus:ring-[#1B8A68]/50 focus:border-[#1B8A68] transition-all">
                                <option value="BEFORE" {{ $invoice->paid_amount == 0 ? 'selected' : '' }}>DP / Pencicilan</option>
                                <option value="AFTER" {{ $invoice->paid_amount > 0 ? 'selected' : '' }}>Pelunasan Pesanan</option>
                            </select>
                        </div>
                    </div>

                    <!-- Proof & Notes -->
                    <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest italic mb-2">Bukti Bayar (Opsional)</label>
                            <div class="flex items-center justify-center w-full">
                                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-2xl cursor-pointer bg-white hover:bg-gray-50 transition-colors group">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-3 text-gray-400 group-hover:text-[#1B8A68] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                        <p class="mb-2 text-[10px] font-black text-gray-500 tracking-widest uppercase italic"><span class="font-bold text-[#1B8A68]">Upload</span> atau Tarik Gambar</p>
                                        <p class="text-xs text-gray-400">PNG, JPG (Max 5MB)</p>
                                    </div>
                                    <input type="file" name="proof_image" class="hidden" accept="image/*" />
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest italic mb-2">Catatan Tambahan</label>
                            <textarea name="notes" rows="2" placeholder="Cth: Titip DP via WA istri..."
                                class="w-full px-4 py-3 bg-white border-2 border-gray-100 rounded-xl text-sm font-black italic tracking-tight focus:ring-2 focus:ring-[#1B8A68]/50 focus:border-[#1B8A68] transition-all"></textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white px-8 py-6 border-t border-gray-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                    <button type="button" @click="open = false" 
                            class="w-full sm:w-auto px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-[10px] font-black uppercase tracking-widest italic transition-colors">
                        BATAL
                    </button>
                    <button type="submit" 
                            class="w-full sm:w-auto px-8 py-3 bg-[#1B8A68] hover:bg-emerald-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest italic shadow-lg shadow-emerald-500/20 transition-all hover:-translate-y-0.5 active:scale-95 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        SIMPAN PEMBAYARAN
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Invoice Confirmation Modal (at page root to avoid overflow clipping) --}}
@if($invoice->status === 'Belum Bayar' && !$invoice->payments()->exists() && !$invoice->invoicePayments()->exists())
<div x-data="{ open: false }" 
     @open-delete-modal.window="open = true"
     x-show="open" 
     class="fixed inset-0 z-[100] overflow-y-auto" 
     style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div x-show="open" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm" @click="open = false"></div>
        
        <div x-show="open" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-3xl p-10 max-w-md w-full mx-4 shadow-[0_35px_60px_-15px_rgba(0,0,0,0.5)] border border-gray-100 relative z-10">
            <div class="text-center">
                <div class="w-20 h-20 mx-auto bg-red-50 rounded-[2rem] flex items-center justify-center mb-6 border border-red-100">
                    <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h3 class="text-2xl font-black text-gray-900 italic tracking-tighter uppercase mb-2">Hapus Invoice?</h3>
                <p class="text-sm text-gray-500 font-bold italic mb-2">{{ $invoice->invoice_number }}</p>
                <p class="text-xs text-gray-400 font-bold italic leading-relaxed mb-8">
                    Invoice ini akan dihapus permanen dan semua SPK terkait akan dilepas sehingga bisa dibuatkan invoice baru.
                </p>
                <div class="flex gap-3">
                    <button @click="open = false" class="flex-1 px-6 py-4 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-colors">Batal</button>
                    <form action="{{ route('finance.invoices.delete', $invoice->id) }}" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-6 py-4 bg-red-500 hover:bg-red-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic shadow-lg shadow-red-500/30 transition-all hover:-translate-y-0.5 active:scale-95">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

</x-app-layout>
