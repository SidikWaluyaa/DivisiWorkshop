<div class="supply-chain-dashboard min-h-screen bg-[#F9FAFB] pb-12">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800;900&display=swap');
        
        .supply-chain-dashboard {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #111827;
        }

        .font-inter { font-family: 'Inter', sans-serif; }

        [x-cloak] { display: none !important; }

        .kpi-card-pattern {
            position: absolute;
            right: -20px;
            bottom: -20px;
            opacity: 0.05;
            pointer-events: none;
        }
        
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #22AF85; }

        .timeline-line {
            position: absolute;
            left: 20px;
            top: 40px;
            bottom: 40px;
            width: 2px;
            background: #E5E7EB;
        }
    </style>

    {{-- White Header with Brand Colors --}}
    <div class="bg-white text-gray-900 px-8 py-5 flex items-center justify-between sticky top-0 z-50 border-b border-gray-100 shadow-sm">
        <div class="flex items-center gap-12 flex-1">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-[#22AF85] rounded-2xl flex items-center justify-center shadow-lg shadow-[#22AF85]/20">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div class="hidden md:block">
                    <h1 class="text-base font-black uppercase tracking-widest leading-none text-[#22AF85]">Control Center</h1>
                    <p class="text-[10px] text-gray-400 font-bold mt-1 uppercase">Logistik Global</p>
                </div>
            </div>

            <div class="max-w-md w-full relative">
                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" placeholder="Cari parameter..." 
                    class="w-full bg-gray-50 border border-gray-100 rounded-2xl py-3 pl-12 pr-4 text-sm font-semibold focus:ring-2 focus:ring-[#22AF85] focus:bg-white transition-all placeholder:text-gray-400">
            </div>
        </div>

        <div class="flex items-center gap-8">
            <div class="flex items-center gap-5 text-gray-400">
                <button class="hover:text-[#22AF85] transition-colors relative p-2 rounded-xl hover:bg-gray-50">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <div class="absolute top-2 right-2 w-2.5 h-2.5 bg-[#FFC232] rounded-full border-2 border-white"></div>
                </button>
                <button class="hover:text-[#22AF85] transition-colors p-2 rounded-xl hover:bg-gray-50">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </button>
            </div>
            <div class="h-10 w-[1px] bg-gray-100"></div>
            <div class="flex items-center gap-4 group cursor-pointer pl-2">
                <div class="text-right">
                    <p class="text-xs font-black uppercase tracking-tight text-gray-900 group-hover:text-[#22AF85] transition-colors lowercase">NODE ALPHA</p>
                    <p class="text-[9px] text-[#22AF85] font-black uppercase mt-0.5">Operasional</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-[#22AF85] p-[2px]">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=FFFFFF&color=22AF85&bold=true" class="w-full h-full rounded-[14px] object-cover" />
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-[1600px] mx-auto px-8 mt-10 space-y-10">
        
        {{-- KPI Row --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            {{-- Valuasi Stok --}}
            <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100 relative overflow-hidden group">
                <div class="kpi-card-pattern">
                    <svg class="w-32 h-32 text-gray-50" fill="currentColor" viewBox="0 0 20 20"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div class="flex items-center justify-between mb-6">
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Valuasi Stok</span>
                    <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-[#22AF85]">
                        <svg class="w-6 h-6 border-2 border-[#22AF85] rounded-full p-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>
                <h3 class="text-4xl font-black font-inter tracking-tight text-gray-900 lowercase">Rp {{ number_format($stats['total_valuation'], 0, ',', '.') }}</h3>
                <div class="flex items-center gap-2 mt-4">
                    <svg class="w-4 h-4 text-[#22AF85]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    <span class="text-[10px] font-black text-[#22AF85] uppercase tracking-widest">+{{ $stats['valuation_trend'] }}% vs Bulan Lalu</span>
                </div>
            </div>

            {{-- Status Kritikal --}}
            <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100 relative overflow-hidden group">
                <div class="kpi-card-pattern">
                    <svg class="w-24 h-24 text-gray-50" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                </div>
                <div class="flex items-center justify-between mb-6">
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Status Kritikal</span>
                    <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center text-[#FFC232]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                </div>
                <h3 class="text-4xl font-black font-inter tracking-tight text-gray-900">{{ $stats['low_stock_count'] }} Item</h3>
                <p class="text-[10px] font-black text-[#FFC232] uppercase mt-4 tracking-[0.15em]">Ambang Batas Stok Tercapai</p>
            </div>

            {{-- Pesanan Tertunda --}}
            <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100 relative overflow-hidden group">
                <div class="kpi-card-pattern">
                    <svg class="w-24 h-24 text-gray-50" fill="currentColor" viewBox="0 0 20 20"><path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 100-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" /></svg>
                </div>
                <div class="flex items-center justify-between mb-6">
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Pesanan Tertunda</span>
                    <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </div>
                </div>
                <h3 class="text-4xl font-black font-inter tracking-tight text-gray-900">{{ $stats['pending_requests'] }} Pesanan</h3>
                <p class="text-[10px] font-black text-gray-400 uppercase mt-4 tracking-[0.15em]">Konfirmasi Supplier</p>
            </div>

            {{-- Total Pengadaan --}}
            <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border-4 border-[#22AF85]/10 relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 opacity-[0.03]">
                    <svg class="w-48 h-48 text-[#22AF85]" fill="currentColor" viewBox="0 0 24 24"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
                <div class="flex items-center justify-between mb-6">
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-[#22AF85]">Total Pengadaan</span>
                    <div class="w-12 h-12 bg-[#22AF85] rounded-2xl flex items-center justify-center text-white shadow-lg shadow-[#22AF85]/30">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </div>
                </div>
                <h3 class="text-4xl font-black font-inter tracking-tight text-gray-900">Rp {{ number_format($stats['total_purchased_value'], 0, ',', '.') }}</h3>
                <p class="text-[10px] font-black text-[#22AF85] uppercase mt-4 tracking-[0.15em]">{{ $stats['total_purchased_count'] }} Transaksi Berstatus Purchased</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            {{-- Material Pulse Section --}}
            <div class="lg:col-span-8 bg-white rounded-[3rem] p-12 shadow-sm border border-gray-100 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-12">
                        <div>
                            <h2 class="text-3xl font-black tracking-tighter text-gray-900">Aliran Material</h2>
                            <p class="text-[11px] font-black text-gray-400 mt-2 uppercase tracking-[0.2em]">Metrik pemanfaatan sumber daya real-time</p>
                        </div>
                        <a href="{{ route('admin.supply-chain.transactions') }}" wire:navigate class="px-7 py-3 bg-[#FFC232] text-gray-900 text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-gray-900 hover:text-white transition-all flex items-center gap-3 shadow-lg shadow-[#FFC232]/20 border border-[#FFC232]">
                            Matriks Detail
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    <div class="space-y-10">
                        @foreach($topConsumed as $item)
                        <div class="group">
                            <div class="flex items-center justify-between mb-4 text-xs font-black uppercase tracking-[0.1em]">
                                <div class="flex items-center gap-4">
                                    <span class="text-gray-900 text-base">{{ $item->material->name }}</span>
                                    <span class="text-gray-300 font-bold">Batch #{{ rand(1000, 9999) }}</span>
                                </div>
                                <div class="text-right flex items-center gap-3">
                                    <span class="text-gray-900 font-inter text-base">{{ number_format($item->total_qty, 0) }} <span class="text-[10px] text-gray-400 font-bold uppercase">{{ $item->material->unit }}</span></span>
                                    <span class="px-3 py-1 bg-gray-50 text-[10px] rounded-lg tracking-widest {{ str_contains($item->status_label, 'Kritikal') ? 'text-[#FFC232] bg-gray-900' : ($item->ratio > 80 ? 'text-[#22AF85]' : 'text-gray-500') }}">
                                        {{ $item->status_label }}
                                    </span>
                                </div>
                            </div>
                            <div class="h-4 bg-gray-50 rounded-full overflow-hidden border border-gray-100 p-1">
                                <div class="h-full {{ $item->color_class }} rounded-full transition-all duration-1000 origin-left scale-x-0" 
                                     x-init="setTimeout(() => $el.classList.remove('scale-x-0'), 200)"
                                     style="width: {{ $item->ratio }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-16 bg-[#22AF85]/5 rounded-[2.5rem] p-10 relative overflow-hidden group border border-[#22AF85]/10 text-[#22AF85]">
                    <div class="absolute right-12 top-1/2 -translate-y-1/2 opacity-[0.08] transition-transform group-hover:scale-110">
                        <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div class="flex items-center gap-8">
                        <div class="w-16 h-16 bg-[#22AF85] rounded-3xl flex items-center justify-center text-white shadow-xl shadow-[#22AF85]/20">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-black uppercase tracking-[0.2em] text-sm">Prediksi Produksi</h4>
                            <p class="text-[11px] font-bold uppercase tracking-[0.1em] mt-2 text-gray-400">Estimasi Kenaikan Konsumsi 15% Minggu Depan</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Audit Ledger Section --}}
            <div class="lg:col-span-4 bg-white rounded-[3rem] p-12 shadow-sm border border-gray-100 flex flex-col">
                <div class="flex items-center gap-4 mb-14">
                    <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center text-[#22AF85]">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </div>
                    <h3 class="text-2xl font-black tracking-tight text-gray-900 leading-none">Log Mutasi</h3>
                </div>

                <div class="flex-1 relative space-y-12">
                    <div class="timeline-line"></div>
                    
                    @foreach($auditLedger as $tx)
                    <div class="relative pl-16 group pb-4">
                        {{-- Icon Node --}}
                        <div class="absolute left-0 top-0 w-11 h-11 rounded-2xl {{ $tx->type == 'IN' ? 'bg-[#22AF85] text-white shadow-[#22AF85]/30' : 'bg-[#FFC232] text-gray-900 shadow-[#FFC232]/30' }} shadow-lg flex items-center justify-center z-10 transition-all group-hover:scale-110">
                            @if($tx->type == 'IN')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            @endif
                        </div>
                        
                        {{-- Content --}}
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <h5 class="text-[13px] font-black text-gray-900 leading-none">{{ $tx->event_label }}</h5>
                                    <span class="px-2 py-0.5 rounded-lg {{ $tx->type == 'IN' ? 'bg-emerald-50 text-[#22AF85]' : 'bg-amber-50 text-[#FFC232]' }} text-[8px] font-black tracking-widest uppercase border border-current/20">
                                        {{ $tx->status_label }}
                                    </span>
                                </div>
                                <span class="text-[9px] font-black text-gray-300 uppercase italic">{{ $tx->created_at->format('H:i') }} WIB</span>
                            </div>
                            <p class="text-[11px] font-bold text-gray-400 leading-relaxed pr-4">
                                {{ number_format($tx->quantity, 0) }} {{ $tx->material->unit }} '{{ $tx->material->name }}' {{ $tx->type == 'IN' ? 'masuk' : 'keluar' }} untuk {{ $tx->ref_detail }}.
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <a href="{{ route('admin.supply-chain.transactions') }}" wire:navigate class="w-full mt-14 py-5 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-[0.25em] rounded-[2rem] hover:bg-gray-900 hover:text-white transition-all text-center border-2 border-transparent hover:border-gray-900">
                    Unduh Log Lengkap
                </a>
            </div>
        </div>

        {{-- Supply Chain Bottlenecks --}}
        <div class="bg-white rounded-[3rem] p-12 shadow-sm border border-gray-100">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 mb-12">
                <div>
                    <h2 class="text-3xl font-black tracking-tighter text-gray-900">Hambatan Rantai Pasok</h2>
                    <p class="text-[11px] font-black text-gray-400 mt-2 uppercase tracking-[0.2em]">Pesanan yang tertunda karena ketidaktersediaan stok</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="px-5 py-3 bg-gray-50 rounded-2xl text-[10px] font-black text-gray-500 uppercase tracking-widest border border-gray-100">Filter: Kritikal</div>
                    <button class="px-8 py-3.5 bg-[#FFC232] text-gray-900 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-gray-900 hover:text-white transition-all flex items-center gap-3 shadow-lg shadow-[#FFC232]/20 border border-[#FFC232]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Ekspor CSV
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="text-[11px] font-black text-gray-400 uppercase tracking-[0.25em] border-b border-gray-50">
                        <tr>
                            <th class="px-6 py-8">ID Referensi</th>
                            <th class="px-6 py-8">Material Dibutuhkan</th>
                            <th class="px-6 py-8 text-center">Qty Defisit</th>
                            <th class="px-6 py-8 text-center">Status</th>
                            <th class="px-6 py-8 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($bottlenecks as $order)
                        <tr class="group hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-8">
                                <p class="text-sm font-black text-gray-900 leading-none">{{ $order->spk_number }}</p>
                                <p class="text-[10px] text-gray-400 font-bold uppercase mt-2 tracking-widest">{{ $order->customer_name }} • {{ $order->shoe_brand }}</p>
                            </td>
                            <td class="px-6 py-8">
                                <div class="flex items-center gap-5">
                                    <div class="w-12 h-12 bg-white rounded-2xl border border-gray-100 flex items-center justify-center text-gray-300 group-hover:text-[#22AF85] group-hover:border-[#22AF85]/20 group-hover:bg-[#22AF85]/5 shadow-sm transition-all">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-gray-700 leading-none">{{ $order->materials->first()->name ?? 'N/A' }}</p>
                                        <p class="text-[10px] text-gray-400 font-bold uppercase mt-2 tracking-wider">{{ $order->materials->first()->category ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-8 text-center">
                                <span class="text-sm font-black text-[#FFC232] bg-[#FFC232]/5 px-3 py-1 rounded-lg">
                                    {{ $order->materials->sum('pivot.quantity') }} {{ $order->materials->first()->unit ?? 'Unit' }}
                                </span>
                            </td>
                            <td class="px-6 py-8 text-center">
                                <span class="px-4 py-2 rounded-xl bg-gray-900 text-[#FFC232] text-[10px] font-black uppercase tracking-widest border-2 border-[#FFC232]">STOK HABIS</span>
                            </td>
                            <td class="px-6 py-8 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('material-requests.create', ['workOrderId' => $order->id]) }}" wire:navigate class="px-8 py-3.5 bg-gray-900 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-gray-800 transition-all shadow-xl shadow-gray-200">
                                        Buat RFQ
                                    </a>
                                    <button class="w-12 h-12 bg-[#22AF85] text-white rounded-2xl flex items-center justify-center shadow-lg shadow-[#22AF85]/30 hover:scale-105 transition-all">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-24 text-center">
                                <p class="text-gray-400 font-black italic uppercase tracking-widest">Tidak ada hambatan pengadaan saat ini.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
