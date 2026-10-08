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
                                    'Batal', 'BATAL' => 'bg-rose-500/20 text-rose-400 border-rose-500/30',
                                    default => 'bg-white/10 text-white/50 border-white/10'
                                };
                            @endphp
                            <span class="text-[10px] font-black uppercase tracking-[0.3em] px-4 py-1.5 rounded-full border {{ $statusBadge }} italic">
                                {{ $invoice->status }}
                            </span>
                            @if($invoice->is_dp_paid && !in_array($invoice->status, ['Lunas', 'Batal', 'BATAL']))
                                <span class="text-[10px] font-black uppercase tracking-[0.3em] px-4 py-1.5 rounded-full border bg-emerald-500/20 text-emerald-400 border-emerald-500/30 italic flex items-center gap-2">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    DP LUNAS
                                </span>
                            @endif
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

                {{-- Smart Overpayment Alert Banner --}}
                @if($invoice->has_overpayment)
                <div class="bg-gradient-to-r from-amber-500/15 via-rose-500/15 to-amber-500/15 border-2 border-amber-400/50 rounded-[2.5rem] p-8 shadow-2xl flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden backdrop-blur-md">
                    <div class="flex items-center gap-6">
                        <div class="w-16 h-16 rounded-[1.5rem] bg-gradient-to-tr from-amber-500 to-rose-500 text-white flex items-center justify-center text-3xl shadow-lg shadow-amber-500/30 shrink-0">
                            ⚠️
                        </div>
                        <div>
                            <div class="flex items-center gap-3">
                                <span class="text-[10px] font-black uppercase tracking-widest text-amber-700 bg-amber-100 px-3 py-1 rounded-lg">Terdeteksi Lebih Bayar</span>
                                <span class="text-[10px] font-bold text-gray-500 italic">Penyesuaian Layanan / Kelebihan Transfer</span>
                            </div>
                            <div class="text-3xl font-black text-gray-900 tracking-tight mt-1 tabular-nums">
                                Rp {{ number_format($invoice->overpaid_amount, 0, ',', '.') }}
                            </div>
                            <p class="text-xs text-gray-600 font-medium mt-1 leading-relaxed">
                                Total transfer masuk (Gross: <b>Rp {{ number_format($invoice->gross_paid_amount, 0, ',', '.') }}</b>) melebihi total tagihan (<b>Rp {{ number_format($invoice->total_bill, 0, ',', '.') }}</b>). Segera proses pengembalian dana ke pelanggan atau alokasikan sebagai kompensasi.
                            </p>
                        </div>
                    </div>
                    <button @click="$dispatch('open-refund-modal', { amount: {{ $invoice->overpaid_amount }} })"
                            class="px-8 py-4 bg-gradient-to-r from-rose-600 to-amber-600 hover:from-rose-700 hover:to-amber-700 text-white font-black text-xs uppercase tracking-widest rounded-2xl shadow-xl shadow-rose-500/25 hover:scale-105 active:scale-95 transition-all flex items-center gap-3 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                        <span>PROSES REFUND / KOMPENSASI</span>
                    </button>
                </div>
                @endif

                {{-- SPK Segment Analysis --}}
                <div class="space-y-6">
                    <div class="flex justify-between items-center pr-2">
                        <h2 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.5em] italic flex items-center gap-4 flex-1">
                            Rincian Pesanan Terkait
                            <div class="h-px flex-1 bg-gray-100"></div>
                        </h2>
                        <button @click="$dispatch('openAddSpkModal')" class="ml-4 px-6 py-2 bg-white border-2 border-[#1B8A68]/20 text-[#1B8A68] rounded-xl text-[9px] font-black uppercase tracking-[0.2em] italic hover:bg-[#1B8A68] hover:text-white hover:border-[#1B8A68] transition-all shadow-sm active:scale-95">
                            ➕ Tambah SPK
                        </button>
                    </div>
                    
                    @foreach($invoice->workOrders as $order)
                        <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-2xl hover:shadow-[#1B8A68]/5 transition-all group/item overflow-hidden relative">
                            <div class="absolute inset-y-0 left-0 w-2 bg-[#1B8A68] opacity-30 group-hover/item:opacity-100 transition-opacity"></div>
                            
                            <div class="flex flex-col md:flex-row justify-between gap-10">
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-end gap-3 mb-6">
                                        <a href="{{ route('finance.show', $order->id) }}" class="text-2xl font-black text-gray-900 group-hover/item:text-[#1B8A68] italic tracking-tight uppercase leading-none transition-colors">
                                            {{ $order->spk_number }}
                                        </a>
                                        @if($order->fast_track_status === 'yes')
                                            <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black bg-orange-100 text-orange-700 uppercase tracking-[0.2em] border border-orange-200 animate-pulse italic">🚀 FAST TRACK</span>
                                        @endif
                                        @if($order->cs_code)
                                            <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black bg-emerald-50 text-[#1B8A68] uppercase tracking-[0.2em] border border-emerald-100 italic">GATEWAY: {{ $order->cs_code }}</span>
                                        @endif
                                        @if($order->hk_days !== null)
                                            <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black bg-blue-50 text-blue-600 uppercase tracking-[0.2em] border border-blue-100 italic">SLA: {{ $order->hk_days }} HARI</span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black bg-gray-50 text-gray-400 uppercase tracking-[0.2em] border border-gray-100 italic">SLA: - HARI</span>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-4 mb-6">
                                        <div class="w-1.5 h-1.5 rounded-full bg-[#1B8A68]"></div>
                                        <span class="text-sm font-black text-gray-700 italic uppercase tracking-tight">{{ $order->shoe_brand }} &bull; {{ $order->shoe_type }}</span>
                                    </div>

                                    <div class="space-y-2 pl-5 border-l-2 border-gray-50">
                                        @foreach($order->workOrderServices as $svc)
                                            @php
                                                $isAdditional = !empty($svc->service_details['is_cx_additional']) && $svc->service_details['is_cx_additional'];
                                            @endphp
                                            <div class="flex items-center gap-4 py-1.5">
                                                <span class="text-[10px] text-[#1B8A68] font-black">●</span>
                                                <div class="flex items-center gap-2">
                                                    <p class="text-[11px] font-black text-gray-500 uppercase tracking-widest italic opacity-80">{{ $svc->custom_service_name ?? ($svc->service ? $svc->service->name : 'Layanan Custom') }}</p>
                                                    @if($isAdditional)
                                                        <span class="text-[8px] font-black text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded uppercase tracking-wider border border-amber-200">
                                                            ➕ Jasa Tambahan
                                                        </span>
                                                    @else
                                                        <span class="text-[8px] font-black text-slate-500 bg-slate-50 px-1.5 py-0.5 rounded uppercase tracking-wider border border-slate-200">
                                                            ⚙️ Jasa Reguler
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="h-px flex-1 bg-gray-50 bg-dotted border-b border-gray-100"></div>
                                                <span class="text-[11px] font-black text-gray-900 italic tabular-nums">Rp {{ number_format($svc->cost, 0, ',', '.') }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="md:text-right p-8 bg-[#F8FAFC] rounded-[2rem] border border-gray-100 min-w-[280px] flex flex-col justify-center gap-4 group-hover/item:bg-white transition-colors duration-500">
                                    <div class="flex flex-col gap-1 items-end">
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] italic">Subtotal SPK</span>
                                        <div class="text-2xl font-black text-gray-900 italic tracking-tighter tabular-nums leading-none">Rp {{ number_format($order->total_transaksi - $order->shipping_cost, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="flex justify-end mt-4 gap-3">
                                        <form action="{{ route('finance.invoices.unlink-spk', [$invoice->id, $order->id]) }}" method="POST" onsubmit="return confirm('Lepas SPK {{ $order->spk_number }} dari Invoice ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-12 h-12 rounded-full bg-white border border-gray-100 text-red-500 shadow-xl flex items-center justify-center hover:bg-red-50 hover:scale-110 transition-all active:scale-95 group/unlink" title="Lepas dari Invoice">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </form>
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
                            $isRefund = $payment->is_refund;
                        @endphp
                        <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-2xl overflow-hidden relative {{ $isRefund ? 'border-l-4 border-l-rose-500 bg-rose-50/10' : ($isVerified ? 'border-l-4 border-l-emerald-400' : 'border-l-4 border-l-amber-400') }}">
                            <div class="flex flex-col md:flex-row justify-between gap-6">
                                {{-- Payment Info --}}
                                <div class="flex-1">
                                    <div class="flex items-center justify-between gap-3 mb-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-inner {{ $isRefund ? 'bg-rose-100 text-rose-600' : ($isVerified ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600') }}">
                                                @if($isRefund)
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                                @elseif($isVerified)
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                @else
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <div class="text-xl font-black {{ $isRefund ? 'text-rose-600' : 'text-gray-900' }} italic tabular-nums tracking-tighter">
                                                        {{ $isRefund ? '- ' : '' }}Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                                    </div>
                                                    @if($payment->type == 'ONGKIR')
                                                        <span class="bg-blue-100 text-blue-600 text-[8px] font-black px-2 py-0.5 rounded uppercase tracking-widest italic">ONGKIR</span>
                                                    @elseif($payment->type == 'TAMBAH_JASA')
                                                        <span class="bg-purple-100 text-purple-600 text-[8px] font-black px-2 py-0.5 rounded uppercase tracking-widest italic">TAMBAH JASA</span>
                                                    @elseif($payment->type == 'OTO')
                                                        <span class="bg-pink-100 text-pink-600 text-[8px] font-black px-2 py-0.5 rounded uppercase tracking-widest italic">OTO</span>
                                                    @elseif($payment->type == 'REFUND')
                                                        <span class="bg-rose-100 text-rose-700 text-[8px] font-black px-2.5 py-0.5 rounded uppercase tracking-widest italic flex items-center gap-1">↩️ REFUND DANA</span>
                                                    @elseif($payment->type == 'KOMPENSASI')
                                                        <span class="bg-amber-100 text-amber-800 text-[8px] font-black px-2.5 py-0.5 rounded uppercase tracking-widest italic flex items-center gap-1">🏷️ KOMPENSASI WORKSHOP</span>
                                                    @elseif($payment->type == 'DISKON_PENYESUAIAN')
                                                        <span class="bg-purple-100 text-purple-700 text-[8px] font-black px-2.5 py-0.5 rounded uppercase tracking-widest italic flex items-center gap-1">💸 DISKON PENYESUAIAN</span>
                                                    @endif
                                                </div>
                                                <div class="text-[10px] text-gray-400 font-black uppercase tracking-widest italic">{{ $payment->payment_date->format('d M Y') }} • oleh {{ $payment->creator->name ?? '-' }}</div>
                                            </div>
                                        </div>

                                        {{-- Actions for Payments --}}
                                        <div class="flex gap-2">
                                            @if(!$isRefund)
                                            <button @click="$dispatch('open-edit-payment', { 
                                                id: {{ $payment->id }}, 
                                                amount: {{ $payment->amount }}, 
                                                date: '{{ $payment->payment_date->format('Y-m-d') }}',
                                                notes: '{{ addslashes($payment->notes) }}',
                                                url: '{{ route('finance.invoice-payments.update', $payment->id) }}'
                                            })" class="w-8 h-8 rounded-lg bg-white border border-gray-100 text-gray-400 hover:text-blue-500 hover:border-blue-100 transition-all flex items-center justify-center shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </button>
                                            @endif
                                            <form action="{{ route('finance.invoice-payments.delete', $payment->id) }}" method="POST" onsubmit="return confirm('Hapus {{ $isRefund ? 'riwayat refund' : 'riwayat pembayaran' }} ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-8 h-8 rounded-lg bg-white border border-gray-100 text-gray-400 hover:text-red-500 hover:border-red-100 transition-all flex items-center justify-center shadow-sm">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    @if($payment->notes)
                                        <div class="text-xs text-gray-600 italic bg-gray-50/80 rounded-xl px-4 py-2 inline-block border border-gray-100">
                                            📝 {{ $payment->notes }}
                                        </div>
                                    @endif
                                    
                                    @if($isRefund && ($payment->refund_bank_name || $payment->refund_account_number))
                                        <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-gray-600 bg-rose-50/50 border border-rose-100 rounded-xl px-4 py-2">
                                            <span class="font-black text-rose-700 text-[10px] uppercase tracking-wider">Rekening Tujuan:</span>
                                            <span class="font-bold text-gray-800">{{ $payment->refund_bank_name }}</span>
                                            <span class="font-mono text-gray-700">{{ $payment->refund_account_number }}</span>
                                            @if($payment->refund_account_name)
                                                <span class="text-gray-500 italic">(a.n {{ $payment->refund_account_name }})</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                {{-- Verification / Mutation Status --}}
                                <div class="md:min-w-[280px] p-6 rounded-[2rem] border {{ $isRefund ? 'bg-rose-50/40 border-rose-100' : ($isVerified ? 'bg-emerald-50/50 border-emerald-100' : 'bg-gray-50 border-gray-100') }}">
                                    @if($isRefund)
                                        <div class="flex items-center gap-2 mb-3">
                                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                            <span class="text-[10px] font-black text-rose-700 uppercase tracking-[0.2em] italic">Penyesuaian / Refund Disetujui</span>
                                        </div>
                                        <p class="text-[10px] text-gray-600 italic font-bold leading-relaxed mb-3">Transaksi ini mengurangi total terbayar pada invoice ini secara otomatis.</p>
                                        @if($payment->proof_image)
                                            <a href="{{ asset($payment->proof_image) }}" target="_blank" class="inline-flex items-center gap-2 text-[10px] font-black text-rose-600 hover:text-rose-700 uppercase tracking-wider bg-white px-3 py-2 rounded-xl border border-rose-200 shadow-sm transition-all hover:scale-105">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                Lihat Bukti Transfer Keluar
                                            </a>
                                        @endif
                                    @elseif($isVerified && $mutation)
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
                                    @elseif($isVerified)
                                        <div class="flex items-center gap-2 mb-3">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            <span class="text-[10px] font-black text-emerald-700 uppercase tracking-[0.2em] italic">Otomatis Terverifikasi</span>
                                        </div>
                                        <p class="text-[10px] text-emerald-600/80 italic font-bold leading-relaxed">Pembayaran ini diinput langsung oleh Finance/Admin dan telah disahkan tanpa memerlukan pencocokan mutasi bank.</p>
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
                            <span class="text-sm font-black text-white italic tabular-nums tracking-tighter">Rp {{ number_format($invoice->workOrders->sum(fn($wo) => $wo->total_service_price > 0 ? $wo->total_service_price : $wo->total_transaksi), 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-black text-white/40 uppercase tracking-widest italic group-hover:text-[#1B8A68] transition-colors">Biaya Pengiriman Global</span>
                            <span class="text-sm font-black text-white italic tabular-nums tracking-tighter">Rp {{ number_format($invoice->shipping_cost, 0, ',', '.') }}</span>
                        </div>

                        @if($invoice->discount > 0)
                        <div class="flex justify-between items-center text-rose-400">
                            <span class="text-[10px] font-black uppercase tracking-widest italic">Diskon Promo</span>
                            <span class="text-sm font-black italic tabular-nums tracking-tighter">- Rp {{ number_format($invoice->discount, 0, ',', '.') }}</span>
                        </div>
                        @endif

                        <div class="pt-4 border-t border-white/10 flex justify-between items-center group/total">
                            <span class="text-[10px] font-black text-[#FFC232] uppercase tracking-widest italic">Total Tagihan</span>
                            <span class="text-xl font-black text-[#FFC232] italic tabular-nums tracking-tighter">Rp {{ number_format($invoice->total_bill, 0, ',', '.') }}</span>
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

                    {{-- Breakdown Pembayaran Kotor, Refund, & Bersih --}}
                    <div class="mb-8 bg-white/5 backdrop-blur-md p-5 sm:p-6 rounded-[2rem] border border-white/10 shadow-inner space-y-3.5">
                        <div class="flex justify-between items-center gap-2">
                            <span class="text-[10px] font-black text-white/50 uppercase tracking-widest italic shrink-0">Total Masuk (Gross)</span>
                            <span class="text-sm font-black text-white/95 italic tabular-nums whitespace-nowrap">Rp&nbsp;{{ number_format($invoice->gross_paid_amount, 0, ',', '.') }}</span>
                        </div>
                        @if($invoice->paid_unique_code_amount > 0)
                        <div class="flex justify-between items-center gap-2 text-amber-300/80 -mt-1 pl-2 border-l-2 border-amber-400/40">
                            <span class="text-[9px] font-black uppercase tracking-wider italic flex items-center gap-1.5 shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span> Termasuk Kode Unik
                            </span>
                            <span class="text-xs font-black italic tabular-nums whitespace-nowrap">+ Rp&nbsp;{{ number_format($invoice->paid_unique_code_amount, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        @if($invoice->total_refund_amount > 0)
                        <div class="flex justify-between items-center gap-2 text-rose-400 pl-2 border-l-2 border-rose-500/40">
                            <span class="text-[10px] font-black uppercase tracking-widest italic flex items-center gap-1.5 shrink-0">
                                <span>↩️</span> Pengembalian / Refund
                            </span>
                            <span class="text-sm font-black italic tabular-nums whitespace-nowrap">- Rp&nbsp;{{ number_format($invoice->total_refund_amount, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        <div class="pt-3 border-t border-white/10 flex justify-between items-end gap-2">
                            <div class="flex flex-col shrink-0">
                                <span class="text-[9px] font-black text-emerald-400 uppercase tracking-widest italic">Terbayar Bersih</span>
                                <span class="text-[8px] font-bold text-emerald-500/60 uppercase tracking-wider italic">(Net Amount)</span>
                            </div>
                            <div class="text-right whitespace-nowrap">
                                <span class="text-xl sm:text-2xl font-black text-emerald-400 italic tracking-tighter tabular-nums leading-none drop-shadow-[0_2px_8px_rgba(52,211,153,0.25)]">Rp&nbsp;{{ number_format($invoice->net_paid_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-8 bg-white/5 rounded-[2.5rem] border border-white/10 group-hover:bg-[#1B8A68]/10 transition-colors duration-700">
                        <span class="text-[10px] font-black text-white/30 uppercase tracking-[0.4em] mb-2 block italic">Sisa Tagihan Akhir</span>
                        @php
                            $relevantCode = $invoice->status !== 'Lunas' ? ($invoice->final_unique_code ?? 0) : 0;
                            $totalOutstanding = $invoice->remaining_balance + $relevantCode;
                        @endphp
                        <div class="text-4xl font-black {{ $invoice->status === 'Lunas' ? 'text-emerald-400' : 'text-[#FFC232]' }} italic tracking-tighter leading-none tabular-nums shadow-amber-500/20 drop-shadow-lg mb-2">
                            Rp {{ number_format($totalOutstanding, 0, ',', '.') }}
                        </div>
                        <p class="text-[10px] font-bold text-white/20 italic mb-8">(Pokok: Rp {{ number_format($invoice->remaining_balance, 0, ',', '.') }} + Unik: {{ $relevantCode }})</p>
                        
                        <div class="space-y-3">
                            @if($invoice->remaining_balance > 0)
                                <button @click="$dispatch('open-payment-modal')" class="w-full bg-[#1B8A68] hover:bg-emerald-600 text-white font-black italic tracking-widest text-sm py-4 rounded-2xl shadow-xl shadow-emerald-500/30 transition-all hover:scale-105 active:scale-95 flex items-center justify-center gap-3 relative overflow-hidden group/pay">
                                    <div class="absolute inset-0 bg-white/20 -translate-x-full group-hover/pay:animate-[shimmer_1s_infinite]"></div>
                                    <svg class="w-5 h-5 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                    <span class="relative z-10">CATAT PEMBAYARAN</span>
                                </button>
                            @endif

                            {{-- Tombol Catat Refund / Kompensasi --}}
                            <button @click="$dispatch('open-refund-modal', { amount: {{ $invoice->overpaid_amount > 0 ? $invoice->overpaid_amount : 0 }} })" 
                                    class="w-full {{ $invoice->has_overpayment ? 'bg-gradient-to-r from-rose-500 to-amber-500 text-white shadow-rose-500/30 animate-pulse' : 'bg-rose-500/20 hover:bg-rose-500 text-rose-300 hover:text-white border border-rose-500/30' }} font-black italic tracking-wider text-xs py-3.5 rounded-2xl shadow-lg transition-all hover:scale-105 active:scale-95 flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                <span>{{ $invoice->has_overpayment ? 'PROSES REFUND (LEBIH BAYAR)' : 'CATAT REFUND / KOMPENSASI' }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Penagihan Elite (DP, Pelunasan, & Full) --}}
                <div class="bg-white rounded-[3rem] p-10 shadow-2xl border border-gray-100 space-y-8 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-purple-500/5 rounded-bl-[5rem] -mr-8 -mt-8 transition-transform group-hover:scale-125 duration-700"></div>
                    
                    <h3 class="text-[11px] font-black text-purple-500 uppercase tracking-[0.5em] mb-2 italic flex items-center gap-3 relative z-10">
                        <div class="w-2 h-2 rounded-full bg-purple-500 animate-pulse"></div>
                        Penagihan Elite & Link Sharing
                    </h3>

                    <div class="grid grid-cols-1 gap-6 relative z-10">
                        {{-- Card 1: DP 70% --}}
                        @php $dpPaid = $invoice->is_dp_paid; @endphp
                        <div class="p-6 rounded-[2rem] border transition-all relative overflow-hidden {{ $dpPaid ? 'bg-gray-50 border-gray-100 opacity-60 grayscale' : 'bg-slate-50 border-gray-100 hover:bg-white hover:shadow-xl group/dp' }}" x-data="{ copied: false }">
                            @if($dpPaid)
                                <div class="absolute inset-0 z-20 flex items-center justify-center rotate-12 pointer-events-none">
                                    <div class="border-4 border-emerald-500/30 text-emerald-500/40 px-6 py-2 rounded-xl font-black text-2xl tracking-[0.3em] uppercase">DP SELESAI</div>
                                </div>
                            @endif
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <span class="text-[9px] font-black {{ $dpPaid ? 'text-gray-400' : 'text-emerald-600' }} uppercase tracking-widest italic mb-1 block">Termin 1: Down Payment (70%)</span>
                                    <div class="text-2xl font-black text-gray-900 italic tracking-tighter tabular-nums">
                                        Rp {{ number_format($invoice->total_dp, 0, ',', '.') }}
                                    </div>
                                    <p class="text-[10px] font-bold text-gray-400 italic mt-1">(Pokok: Rp {{ number_format($invoice->target_dp_amount, 0, ',', '.') }} + Unik: {{ $invoice->dp_unique_code ?? 0 }})</p>
                                </div>
                                <div class="bg-white px-3 py-1 rounded-lg border border-gray-200 text-[10px] font-black text-gray-400 italic">KODE: {{ $invoice->dp_unique_code ?? '-' }}</div>
                            </div>
                            <div class="flex gap-2">
                                <button @click="copyToClipboard('{{ $invoice->invoice_dp_url }}'); copied = true; setTimeout(() => copied = false, 2000)" 
                                        {{ $dpPaid ? 'disabled' : '' }}
                                        class="flex-1 bg-white border-2 border-gray-200 py-3 rounded-xl text-[9px] font-black uppercase tracking-widest italic hover:border-purple-500 hover:text-purple-500 transition-all flex items-center justify-center gap-2 {{ $dpPaid ? 'cursor-not-allowed opacity-50' : '' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                    <span x-text="copied ? 'LINK DISALIN!' : 'SALIN LINK DP 70%'"></span>
                                </button>
                                @if(!$dpPaid)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $invoice->customer?->phone) }}?text={{ urlencode("Halo Bapak/Ibu " . $invoice->customer?->name . ", berikut rincian tagihan DP 70% untuk pesanan Anda di Shoe Workshop.\n\nTotal Bayar: Rp " . number_format($invoice->total_dp, 0, ',', '.') . " (Sudah termasuk kode unik)\n\nLink Detail Tagihan: " . $invoice->invoice_dp_url . "\n\nTerima kasih!") }}" 
                                   target="_blank"
                                   class="w-12 bg-emerald-500 text-white rounded-xl flex items-center justify-center hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-500/20">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.588-5.946 0-6.556 5.332-11.888 11.888-11.888 3.176 0 6.161 1.237 8.404 3.48s3.481 5.229 3.481 8.405c0 6.555-5.332 11.89-11.888 11.89-2.015 0-4.004-.51-5.775-1.471l-6.209 1.692zm6.188-4.015c1.649.978 3.26 1.462 4.887 1.462 5.043 0 9.147-4.103 9.147-9.143 0-2.443-.951-4.74-2.678-6.467-1.726-1.726-4.024-2.677-6.468-2.677-5.041 0-9.143 4.103-9.143 9.143 0 1.83.499 3.511 1.442 5.053l-.963 3.518 3.676-.99zm10.14-5.922c-.274-.137-1.62-.799-1.871-.891-.252-.092-.435-.137-.617.137-.183.275-.708.892-.868 1.075-.16.183-.32.206-.594.069-.274-.138-1.159-.426-2.207-1.361-.817-.728-1.369-1.628-1.53-1.903-.16-.275-.017-.424.12-.561.124-.123.274-.321.412-.481.137-.161.183-.275.274-.459.092-.183.046-.344-.023-.481-.069-.137-.617-1.486-.845-2.035-.222-.534-.447-.461-.617-.47l-.527-.006c-.183 0-.48.069-.731.344-.251.275-.96.939-.96 2.29 0 1.352.983 2.656 1.12 2.84.137.183 1.935 2.956 4.688 4.141.655.282 1.165.451 1.564.577.658.209 1.258.179 1.731.109.528-.078 1.62-.66 1.849-1.298.228-.638.228-1.185.16-1.299-.069-.115-.252-.184-.526-.321z"/></svg>
                                </a>
                                @endif
                            </div>
                        </div>

                        {{-- Card 2: Pelunasan Sisa --}}
                        <div class="p-6 bg-slate-50 rounded-[2rem] border border-gray-100 transition-all hover:bg-white hover:shadow-xl group/fp" x-data="{ copied: false }">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <span class="text-[9px] font-black text-purple-600 uppercase tracking-widest italic mb-1 block">Termin 2: Pelunasan Sisa</span>
                                    <div class="text-2xl font-black text-gray-900 italic tracking-tighter tabular-nums">
                                        Rp {{ number_format($invoice->total_pelunasan, 0, ',', '.') }}
                                    </div>
                                    <p class="text-[10px] font-bold text-gray-400 italic mt-1">(Pokok Sisa: Rp {{ number_format($invoice->remaining_balance, 0, ',', '.') }} + Unik: {{ $invoice->final_unique_code ?? 0 }})</p>
                                </div>
                                <div class="bg-white px-3 py-1 rounded-lg border border-gray-200 text-[10px] font-black text-gray-400 italic">KODE: {{ $invoice->final_unique_code ?? '-' }}</div>
                            </div>
                            <div class="flex gap-2">
                                <button @click="copyToClipboard('{{ $invoice->invoice_final_url }}'); copied = true; setTimeout(() => copied = false, 2000)" 
                                        class="flex-1 bg-white border-2 border-gray-200 py-3 rounded-xl text-[9px] font-black uppercase tracking-widest italic hover:border-purple-500 hover:text-purple-500 transition-all flex items-center justify-center gap-2">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                    <span x-text="copied ? 'LINK DISALIN!' : 'SALIN LINK PELUNASAN'"></span>
                                </button>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $invoice->customer?->phone) }}?text={{ urlencode("Halo Bapak/Ibu " . $invoice->customer?->name . ", barang Anda sudah siap dikirim!\n\nBerikut rincian sisa pelunasan untuk pesanan Anda.\n\nTotal Bayar: Rp " . number_format($invoice->total_pelunasan, 0, ',', '.') . " (Sudah termasuk kode unik)\n\nLink Pelunasan: " . $invoice->invoice_final_url . "\n\nTerima kasih!") }}" 
                                   target="_blank"
                                   class="w-12 bg-emerald-500 text-white rounded-xl flex items-center justify-center hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-500/20">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.588-5.946 0-6.556 5.332-11.888 11.888-11.888 3.176 0 6.161 1.237 8.404 3.48s3.481 5.229 3.481 8.405c0 6.555-5.332 11.89-11.888 11.89-2.015 0-4.004-.51-5.775-1.471l-6.209 1.692zm6.188-4.015c1.649.978 3.26 1.462 4.887 1.462 5.043 0 9.147-4.103 9.147-9.143 0-2.443-.951-4.74-2.678-6.467-1.726-1.726-4.024-2.677-6.468-2.677-5.041 0-9.143 4.103-9.143 9.143 0 1.83.499 3.511 1.442 5.053l-.963 3.518 3.676-.99zm10.14-5.922c-.274-.137-1.62-.799-1.871-.891-.252-.092-.435-.137-.617.137-.183.275-.708.892-.868 1.075-.16.183-.32.206-.594.069-.274-.138-1.159-.426-2.207-1.361-.817-.728-1.369-1.628-1.53-1.903-.16-.275-.017-.424.12-.561.124-.123.274-.321.412-.481.137-.161.183-.275.274-.459.092-.183.046-.344-.023-.481-.069-.137-.617-1.486-.845-2.035-.222-.534-.447-.461-.617-.47l-.527-.006c-.183 0-.48.069-.731.344-.251.275-.96.939-.96 2.29 0 1.352.983 2.656 1.12 2.84.137.183 1.935 2.956 4.688 4.141.655.282 1.165.451 1.564.577.658.209 1.258.179 1.731.109.528-.078 1.62-.66 1.849-1.298.228-.638.228-1.185.16-1.299-.069-.115-.252-.184-.526-.321z"/></svg>
                                </a>
                            </div>
                        </div>

                        {{-- Card 3: Full Payment 100% --}}
                        @if($invoice->paid_amount == 0)
                        <div class="p-6 bg-slate-50 rounded-[2rem] border border-gray-100 transition-all hover:bg-white hover:shadow-xl group/full" x-data="{ copied: false }">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <span class="text-[9px] font-black text-rose-600 uppercase tracking-widest italic mb-1 block">Opsi Alternatif: Bayar Penuh (100%)</span>
                                    <div class="text-2xl font-black text-gray-900 italic tracking-tighter tabular-nums">
                                        Rp {{ number_format($invoice->total_full, 0, ',', '.') }}
                                    </div>
                                    <p class="text-[10px] font-bold text-gray-400 italic mt-1">(Total + Ongkir + Unik: {{ $invoice->final_unique_code ?? 0 }})</p>
                                </div>
                                <div class="bg-white px-3 py-1 rounded-lg border border-gray-200 text-[10px] font-black text-gray-400 italic">KODE: {{ $invoice->final_unique_code ?? '-' }}</div>
                            </div>
                            <div class="flex gap-2">
                                <button @click="copyToClipboard('{{ $invoice->invoice_full_url }}'); copied = true; setTimeout(() => copied = false, 2000)" 
                                        class="flex-1 bg-white border-2 border-gray-200 py-3 rounded-xl text-[9px] font-black uppercase tracking-widest italic hover:border-rose-500 hover:text-rose-500 transition-all flex items-center justify-center gap-2">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                    <span x-text="copied ? 'LINK DISALIN!' : 'SALIN LINK BAYAR FULL'"></span>
                                </button>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $invoice->customer?->phone) }}?text={{ urlencode("Halo Bapak/Ibu " . $invoice->customer?->name . ", berikut rincian tagihan pembayaran penuh untuk pesanan Anda.\n\nTotal Bayar: Rp " . number_format($invoice->total_full, 0, ',', '.') . " (Sudah termasuk kode unik)\n\nLink Pembayaran: " . $invoice->invoice_full_url . "\n\nTerima kasih!") }}" 
                                   target="_blank"
                                   class="w-12 bg-emerald-500 text-white rounded-xl flex items-center justify-center hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-500/20">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.588-5.946 0-6.556 5.332-11.888 11.888-11.888 3.176 0 6.161 1.237 8.404 3.48s3.481 5.229 3.481 8.405c0 6.555-5.332 11.89-11.888 11.89-2.015 0-4.004-.51-5.775-1.471l-6.209 1.692zm6.188-4.015c1.649.978 3.26 1.462 4.887 1.462 5.043 0 9.147-4.103 9.147-9.143 0-2.443-.951-4.74-2.678-6.467-1.726-1.726-4.024-2.677-6.468-2.677-5.041 0-9.143 4.103-9.143 9.143 0 1.83.499 3.511 1.442 5.053l-.963 3.518 3.676-.99zm10.14-5.922c-.274-.137-1.62-.799-1.871-.891-.252-.092-.435-.137-.617.137-.183.275-.708.892-.868 1.075-.16.183-.32.206-.594.069-.274-.138-1.159-.426-2.207-1.361-.817-.728-1.369-1.628-1.53-1.903-.16-.275-.017-.424.12-.561.124-.123.274-.321.412-.481.137-.161.183-.275.274-.459.092-.183.046-.344-.023-.481-.069-.137-.617-1.486-.845-2.035-.222-.534-.447-.461-.617-.47l-.527-.006c-.183 0-.48.069-.731.344-.251.275-.96.939-.96 2.29 0 1.352.983 2.656 1.12 2.84.137.183 1.935 2.956 4.688 4.141.655.282 1.165.451 1.564.577.658.209 1.258.179 1.731.109.528-.078 1.62-.66 1.849-1.298.228-.638.228-1.185.16-1.299-.069-.115-.252-.184-.526-.321z"/></svg>
                                </a>
                            </div>
                        </div>
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
    sisaTagihan: {{ $invoice->remaining_balance + ($invoice->status !== 'Lunas' ? ($invoice->final_unique_code ?? 0) : 0) }},
    maxTagihan: {{ $invoice->remaining_balance + ($invoice->status !== 'Lunas' ? ($invoice->final_unique_code ?? 0) : 0) }}
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
                            <input type="number" name="amount_total" x-model="sisaTagihan" :max="maxTagihan" required
                                class="w-full pl-12 pr-4 py-4 bg-white border-2 border-gray-100 rounded-2xl text-2xl font-black italic tracking-tighter focus:ring-2 focus:ring-[#1B8A68]/50 focus:border-[#1B8A68] transition-all shadow-sm text-gray-900">
                        </div>
                        <p class="text-xs font-black text-rose-500 uppercase tracking-widest italic mt-2 text-right">Maksimal: Rp <span x-text="new Intl.NumberFormat('id-ID').format(maxTagihan)"></span></p>
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
                                <option value="TAMBAH_JASA">Tambah Jasa</option>
                                <option value="LUNAS_AWAL">Lunas Awal</option>
                                <option value="ONGKIR">Pembayaran Ongkir</option>
                                <option value="OTO">Pembayaran OTO</option>
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

{{-- Edit Payment Modal --}}
<div x-data="{ 
    open: false,
    payment: { id: null, amount: 0, date: '', notes: '', url: '' }
}" 
@open-edit-payment.window="payment = $event.detail; open = true"
x-show="open" 
class="fixed inset-0 z-[100] overflow-y-auto" 
style="display: none;"
aria-labelledby="modal-title-edit" role="dialog" aria-modal="true">
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="open" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity" 
             @click="open = false" aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="open" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-[0_35px_60px_-15px_rgba(0,0,0,0.5)] transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-100">
            
            <form :action="payment.url" method="POST">
                @csrf
                @method('PUT')
                <div class="bg-blue-600 px-8 py-6 border-b border-blue-700 relative overflow-hidden">
                    <div class="flex justify-between items-center relative z-10">
                        <div>
                            <h3 class="text-2xl font-black text-white italic tracking-tighter uppercase" id="modal-title-edit">Edit Pembayaran</h3>
                            <p class="text-[10px] font-black text-blue-200 uppercase tracking-[0.3em] mt-1">Koreksi Kesalahan Input</p>
                        </div>
                        <button type="button" @click="open = false" class="text-white/50 hover:text-white hover:bg-white/10 p-2 rounded-xl transition-colors">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                </div>

                <div class="px-8 py-8 space-y-6 bg-[#F8FAFC]">
                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest italic mb-2">Jumlah Pembayaran</label>
                        <div class="relative group">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-black italic">Rp</span>
                            <input type="number" name="amount" x-model="payment.amount" required
                                class="w-full pl-12 pr-4 py-4 bg-white border-2 border-gray-100 rounded-2xl text-2xl font-black italic tracking-tighter focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all shadow-sm text-gray-900">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest italic mb-2">Tanggal Bayar</label>
                        <input type="date" name="payment_date" x-model="payment.date" required
                            class="w-full px-4 py-3 bg-white border-2 border-gray-100 rounded-xl text-sm font-black italic tracking-tighter focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all [color-scheme:light]">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest italic mb-2">Catatan Perubahan</label>
                        <textarea name="notes" rows="2" x-model="payment.notes"
                            class="w-full px-4 py-3 bg-white border-2 border-gray-100 rounded-xl text-sm font-black italic tracking-tight focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all"></textarea>
                    </div>
                </div>

                <div class="bg-white px-8 py-6 border-t border-gray-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                    <button type="button" @click="open = false" 
                            class="w-full sm:w-auto px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-[10px] font-black uppercase tracking-widest italic transition-colors">
                        BATAL
                    </button>
                    <button type="submit" 
                            class="w-full sm:w-auto px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-[10px] font-black uppercase tracking-widest italic shadow-lg shadow-blue-500/20 transition-all hover:-translate-y-0.5 active:scale-95 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        SIMPAN PERUBAHAN
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Refund / Compensation Modal --}}
<div x-data="{ 
    openRefund: false,
    refundAmount: {{ $invoice->overpaid_amount > 0 ? $invoice->overpaid_amount : 0 }},
    refundType: 'REFUND',
    bankName: '',
    accountNumber: '',
    accountName: '',
    notes: '{{ $invoice->has_overpayment ? 'Pengembalian selisih penurunan harga SPK di lapangan' : '' }}'
}" 
@open-refund-modal.window="openRefund = true; if($event.detail && $event.detail.amount) refundAmount = $event.detail.amount"
x-show="openRefund" 
class="fixed inset-0 z-[100] overflow-y-auto" 
style="display: none;"
aria-labelledby="modal-refund-title" role="dialog" aria-modal="true">
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="openRefund" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity" 
             @click="openRefund = false" aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="openRefund" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-[0_35px_60px_-15px_rgba(0,0,0,0.5)] transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full border border-gray-100">
            
            <form action="{{ route('finance.invoices.refund', $invoice->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="bg-gradient-to-br from-rose-950 via-gray-900 to-gray-900 px-8 py-6 border-b border-rose-900/30 relative overflow-hidden">
                    <div class="absolute inset-0 bg-rose-500/10 mix-blend-overlay"></div>
                    <div class="flex justify-between items-center relative z-10">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-ping"></span>
                                <h3 class="text-2xl font-black text-white italic tracking-tighter uppercase" id="modal-refund-title">Catat Refund / Kompensasi</h3>
                            </div>
                            <p class="text-[10px] font-black text-rose-400 uppercase tracking-[0.3em] mt-1">Invoice {{ $invoice->invoice_number }}</p>
                        </div>
                        <button type="button" @click="openRefund = false" class="text-white/50 hover:text-white hover:bg-white/10 p-2 rounded-xl transition-colors">
                            <span class="sr-only">Tutup</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                </div>

                <div class="px-8 py-8 space-y-6 bg-[#F8FAFC]">
                    @if($invoice->has_overpayment)
                    <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-black uppercase text-amber-700 tracking-wider">Kelebihan Bayar Terdeteksi</span>
                            <p class="text-xs text-amber-800 font-bold">Selisih uang masuk: Rp {{ number_format($invoice->overpaid_amount, 0, ',', '.') }}</p>
                        </div>
                        <button type="button" @click="refundAmount = {{ $invoice->overpaid_amount }}" class="text-[10px] font-black uppercase bg-amber-500 text-white px-3 py-1.5 rounded-xl hover:bg-amber-600 transition-colors">Gunakan Penuh</button>
                    </div>
                    @endif

                    <!-- Amount Input -->
                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest italic mb-2">Nominal Pengembalian / Kompensasi</label>
                        <div class="relative group">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-rose-500 font-black italic">Rp</span>
                            <input type="number" name="amount" x-model="refundAmount" required min="1"
                                class="w-full pl-12 pr-4 py-4 bg-white border-2 border-rose-200 focus:border-rose-500 rounded-2xl text-2xl font-black italic tracking-tighter focus:ring-2 focus:ring-rose-500/20 transition-all shadow-sm text-gray-900">
                        </div>
                    </div>

                    <!-- Type & Date -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest italic mb-2">Kategori Penyesuaian</label>
                            <select name="refund_type" x-model="refundType" required
                                class="w-full px-4 py-3 bg-white border-2 border-gray-100 rounded-xl text-xs font-black italic tracking-wider focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all">
                                <option value="REFUND">REFUND (Transfer Balik Dana)</option>
                                <option value="KOMPENSASI">KOMPENSASI (Masalah Workshop)</option>
                                <option value="DISKON_PENYESUAIAN">DISKON PENYESUAIAN (Potongan Khusus)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest italic mb-2">Tanggal Transaksi</label>
                            <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required
                                class="w-full px-4 py-3 bg-white border-2 border-gray-100 rounded-xl text-xs font-black italic tracking-wider focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all">
                        </div>
                    </div>

                    <!-- Destination Bank Info (Only relevant if REFUND) -->
                    <div class="p-5 bg-white rounded-2xl border border-gray-200/80 space-y-4">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest italic block">Informasi Rekening Tujuan Transfer Balik</span>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[9px] font-black text-gray-400 uppercase mb-1">Nama Bank</label>
                                <input type="text" name="refund_bank_name" placeholder="BCA / Mandiri / BRI"
                                    class="w-full px-3 py-2 bg-slate-50 border border-gray-200 rounded-xl text-xs font-bold uppercase focus:ring-1 focus:ring-rose-500 focus:bg-white">
                            </div>
                            <div>
                                <label class="block text-[9px] font-black text-gray-400 uppercase mb-1">Nomor Rekening</label>
                                <input type="text" name="refund_account_number" placeholder="Nomor Rekening"
                                    class="w-full px-3 py-2 bg-slate-50 border border-gray-200 rounded-xl text-xs font-bold focus:ring-1 focus:ring-rose-500 focus:bg-white">
                            </div>
                            <div>
                                <label class="block text-[9px] font-black text-gray-400 uppercase mb-1">Atas Nama (a.n)</label>
                                <input type="text" name="refund_account_name" placeholder="Nama Pemilik"
                                    class="w-full px-3 py-2 bg-slate-50 border border-gray-200 rounded-xl text-xs font-bold uppercase focus:ring-1 focus:ring-rose-500 focus:bg-white">
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest italic mb-2">Alasan & Keterangan Penurunan Harga / Refund <span class="text-rose-500">*</span></label>
                        <textarea name="notes" x-model="notes" rows="3" required placeholder="Jelaskan alasan penurunan harga jasa, kendala di lapangan, atau persetujuan refund dengan pelanggan..."
                            class="w-full p-4 bg-white border-2 border-gray-100 rounded-2xl text-xs font-bold focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all"></textarea>
                    </div>

                    <!-- Proof Upload -->
                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest italic mb-2">Upload Bukti Transfer Keluar / Struk Refund (Opsional)</label>
                        <div class="flex items-center justify-center w-full">
                            <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-rose-200 border-dashed rounded-2xl cursor-pointer bg-white hover:bg-rose-50/30 transition-colors group">
                                <div class="flex flex-col items-center justify-center pt-3 pb-3">
                                    <svg class="w-7 h-7 mb-2 text-rose-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                    <p class="text-[10px] font-black text-gray-500 tracking-widest uppercase italic"><span class="font-bold text-rose-600">Upload</span> Struk Bukti Pengembalian</p>
                                    <p class="text-[9px] text-gray-400 mt-0.5">PNG, JPG (Max 5MB)</p>
                                </div>
                                <input type="file" name="proof_image" class="hidden" accept="image/*" />
                            </label>
                        </div>
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button type="submit" class="flex-1 bg-rose-600 hover:bg-rose-700 text-white font-black italic tracking-widest text-xs py-4 rounded-2xl shadow-xl shadow-rose-600/30 transition-all hover:scale-105 active:scale-95 flex items-center justify-center gap-2">
                            <span>PROSES & SIMPAN PENGEMBALIAN DANA</span>
                        </button>
                        <button type="button" @click="openRefund = false" class="px-6 py-4 bg-gray-100 hover:bg-gray-200 text-gray-600 font-black italic text-xs uppercase rounded-2xl transition-all">
                            BATAL
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@livewire('finance.invoice-add-spk', ['invoiceId' => $invoice->id])

    <script>
        function copyToClipboard(text) {
            if (navigator.clipboard && window.isSecureContext) {
                // Metropolitan/Modern Safe Context
                return navigator.clipboard.writeText(text);
            } else {
                // Standard Fallback for HTTP / custom .test domains
                let textArea = document.createElement("textarea");
                textArea.value = text;
                textArea.style.position = "fixed";
                textArea.style.left = "-9999px";
                textArea.style.top = "0";
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                return new Promise((res, rej) => {
                    document.execCommand('copy') ? res() : rej();
                    textArea.remove();
                });
            }
        }
    </script>
</x-app-layout>
