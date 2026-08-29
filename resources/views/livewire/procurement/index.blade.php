<div class="procurement-index-root min-h-screen bg-slate-50/60 dark:bg-slate-900 pb-16">
    {{-- Header Section --}}
    <div class="bg-white/95 dark:bg-slate-800/95 backdrop-blur-xl border-b border-slate-200/80 dark:border-slate-700/80 px-4 sm:px-8 py-5 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-[#22AF85] to-emerald-400 text-white flex items-center justify-center font-black text-2xl shadow-lg shadow-emerald-500/20 shrink-0">
                    🛍️
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">Pengajuan Belanja Bahan Baku</h1>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Pengelolaan nota belanja bahan baku workshop &amp; integrasi Finlog</p>
                </div>
            </div>

            {{-- Controls & CTA --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('material-requests.create') }}" wire:navigate 
                   class="px-6 py-3 bg-[#FFC232] hover:bg-amber-400 text-slate-950 text-xs font-black rounded-2xl shadow-lg shadow-amber-500/20 hover:shadow-xl transition-all duration-200 active:scale-95 flex items-center gap-2 uppercase tracking-wider">
                    <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Buat Pengajuan Baru</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Main Content Container --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-8 pt-6">

        {{-- Top 4 KPI Summary Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            {{-- Card 1: Total Pengajuan --}}
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-700/80 shadow-xs flex items-center gap-4 hover:shadow-md transition-all">
                <div class="w-12 h-12 rounded-2xl bg-teal-50 dark:bg-teal-950/40 text-teal-600 dark:text-teal-400 flex items-center justify-center text-xl shrink-0">
                    📦
                </div>
                <div class="min-w-0 flex-1">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Total Pengajuan</span>
                    <div class="flex items-baseline gap-1.5 mt-0.5">
                        <span class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($metrics['total_requests']) }}</span>
                        <span class="text-xs font-bold text-slate-400">Nota</span>
                    </div>
                    <span class="text-[10px] font-bold text-teal-600 dark:text-teal-400 block truncate mt-0.5">Rp {{ number_format($metrics['total_cost'], 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Card 2: Menunggu Approval Finlog --}}
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-700/80 shadow-xs flex items-center gap-4 hover:shadow-md transition-all">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-500 flex items-center justify-center text-xl shrink-0">
                    ⏳
                </div>
                <div class="min-w-0 flex-1">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Menunggu Approval</span>
                    <div class="flex items-baseline gap-1.5 mt-0.5">
                        <span class="text-2xl font-black text-amber-500">{{ number_format($metrics['pending_count']) }}</span>
                        <span class="text-xs font-bold text-slate-400">Pengajuan</span>
                    </div>
                    <span class="text-[10px] font-bold text-amber-600/80 dark:text-amber-400/80 block mt-0.5">Proses Finlog</span>
                </div>
            </div>

            {{-- Card 3: Dalam Pengiriman --}}
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-700/80 shadow-xs flex items-center gap-4 hover:shadow-md transition-all">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl shrink-0">
                    🚚
                </div>
                <div class="min-w-0 flex-1">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Dalam Pengiriman</span>
                    <div class="flex items-baseline gap-1.5 mt-0.5">
                        <span class="text-2xl font-black text-blue-600 dark:text-blue-400">{{ number_format($metrics['shipping_count']) }}</span>
                        <span class="text-xs font-bold text-slate-400">OTW WS</span>
                    </div>
                    <span class="text-[10px] font-bold text-blue-600/80 dark:text-blue-400/80 block mt-0.5">Sudah Disetujui</span>
                </div>
            </div>

            {{-- Card 4: Bahan Baku Tiba --}}
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-700/80 shadow-xs flex items-center gap-4 hover:shadow-md transition-all">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-[#22AF85] flex items-center justify-center text-xl shrink-0">
                    ✅
                </div>
                <div class="min-w-0 flex-1">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Bahan Baku Tiba</span>
                    <div class="flex items-baseline gap-1.5 mt-0.5">
                        <span class="text-2xl font-black text-[#22AF85]">{{ number_format($metrics['received_count']) }}</span>
                        <span class="text-xs font-bold text-slate-400">Diterima</span>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-600/80 dark:text-emerald-400/80 block mt-0.5">Stok Terupdate</span>
                </div>
            </div>
        </div>

        {{-- Search & Filter Bar --}}
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-4 sm:p-6 border border-slate-200/80 dark:border-slate-700/80 shadow-xs mb-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                {{-- Search Bar --}}
                <div class="relative w-full md:w-96">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Cari No. Request, Pemohon, SPK..." 
                           class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-2xl text-xs font-bold text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-[#22AF85]/30 focus:border-[#22AF85] outline-none transition-all">
                </div>

                {{-- Status Filter & Indicator --}}
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <select wire:model.live="status" class="w-full md:w-auto text-xs font-bold border border-slate-200 dark:border-slate-600 rounded-2xl px-4 py-3 bg-slate-50 dark:bg-slate-700/50 text-slate-900 dark:text-white focus:ring-2 focus:ring-[#22AF85]/30 focus:border-[#22AF85] outline-none cursor-pointer shadow-2xs">
                        <option value="all">Semua Status Belanja</option>
                        <option value="PENDING">Menunggu Approval Finlog</option>
                        <option value="APPROVED">Dalam Pengiriman (Finlog)</option>
                        <option value="PURCHASED">Sudah Dibeli (Purchased)</option>
                        <option value="RECEIVED">Bahan Baku Tiba di Workshop</option>
                        <option value="REJECTED">Ditolak</option>
                        <option value="CANCELLED">Dibatalkan</option>
                    </select>

                    <div wire:loading class="hidden sm:flex items-center gap-2 text-xs font-black text-[#22AF85] uppercase tracking-wider whitespace-nowrap animate-pulse">
                        <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Menyinkronkan...</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cards List (3-Tier Card System) --}}
        <div class="space-y-5">
            @forelse($requests as $request)
                @php
                    $statusColor = match($request->status) {
                        'PENDING' => 'bg-amber-500 text-white',
                        'APPROVED', 'PURCHASED' => 'bg-blue-600 text-white',
                        'RECEIVED' => 'bg-[#22AF85] text-white',
                        'REJECTED', 'CANCELLED' => 'bg-rose-500 text-white',
                        default => 'bg-slate-400 text-white'
                    };
                    $statusLabel = match($request->status) {
                        'PENDING' => 'Menunggu Approval Finlog',
                        'APPROVED', 'PURCHASED' => 'Dalam Pengiriman (Finlog)',
                        'RECEIVED' => 'Bahan Baku Tiba di Workshop',
                        'REJECTED' => 'Ditolak',
                        'CANCELLED' => 'Dibatalkan',
                        default => $request->status
                    };
                    $isBulkSpk = $request->items->pluck('work_order_id')->filter()->unique()->count() > 1;
                    $hasSpk = $request->work_order_id || $request->items->pluck('work_order_id')->filter()->isNotEmpty();

                    if ($request->type === 'PRODUCTION_PO') {
                        $typeLabel = '🏭 PO Khusus Produksi';
                        $typeBadgeColor = 'bg-sky-50 text-sky-700 dark:bg-sky-950/50 dark:text-sky-300 border-sky-200 dark:border-sky-800';
                    } elseif ($isBulkSpk) {
                        $typeLabel = '📦 Belanja Gabungan SPK';
                        $typeBadgeColor = 'bg-purple-50 text-purple-700 dark:bg-purple-950/50 dark:text-purple-300 border-purple-200 dark:border-purple-800';
                    } elseif ($hasSpk) {
                        $typeLabel = '🛒 Belanja Sortir SPK';
                        $typeBadgeColor = 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800';
                    } else {
                        $typeLabel = '🏬 Restock Gudang Workshop';
                        $typeBadgeColor = 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-600';
                    }
                    $materialNames = $request->items->map(function($i) {
                        return $i->material->name ?? $i->custom_item_name ?? 'Material';
                    })->take(2)->implode(', ');
                    if($request->items->count() > 2) {
                        $materialNames .= ' +' . ($request->items->count() - 2) . ' lainnya';
                    }
                @endphp

                {{-- 3-TIER MODERN CARD --}}
                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/90 dark:border-slate-700/90 shadow-sm hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 overflow-hidden group">
                    
                    {{-- TIER 1: CARD HEADER --}}
                    <div class="bg-slate-50/70 dark:bg-slate-800/80 px-6 py-4 border-b border-slate-100 dark:border-slate-700/80 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex flex-wrap items-center gap-2.5 min-w-0">
                            <div class="w-2.5 h-2.5 rounded-full {{ $statusColor }} shrink-0"></div>
                            
                            <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white tracking-tight font-mono group-hover:text-[#22AF85] transition-colors">
                                {{ $request->request_number }}
                            </h2>

                            {{-- Status Pill --}}
                            <span class="px-3 py-1 rounded-full {{ $statusColor }} text-[10px] font-black uppercase tracking-wider shadow-2xs">
                                {{ $statusLabel }}
                            </span>

                            {{-- Type Chip --}}
                            <span class="px-2.5 py-1 rounded-xl {{ $typeBadgeColor }} text-[10px] font-bold uppercase tracking-wider border">
                                {{ $typeLabel }}
                            </span>

                            {{-- Ref Finlog --}}
                            @if($request->finlog_request_id)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 text-[10px] font-black uppercase tracking-wider border border-purple-200 dark:border-purple-800 shadow-2xs">
                                    <svg class="w-3 h-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                    <span>Ref Finlog: {{ $request->finlog_request_id }}</span>
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center gap-1.5 text-slate-400 dark:text-slate-500 text-xs font-bold">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span>{{ $request->created_at->format('d M Y • H:i') }}</span>
                        </div>
                    </div>

                    {{-- TIER 2: CARD BODY (4-COLUMN INFO GRID) --}}
                    <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        
                        {{-- Col 1: Diajukan Oleh --}}
                        <div class="bg-slate-50/90 dark:bg-slate-750/50 rounded-2xl p-4 border border-slate-100 dark:border-slate-700/60 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-slate-200/80 dark:bg-slate-700 text-slate-700 dark:text-slate-200 flex items-center justify-center font-black text-xs shrink-0">
                                {{ strtoupper(substr($request->requestedBy->name ?? 'A', 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider block">Diajukan Oleh</span>
                                <span class="text-xs font-black text-slate-800 dark:text-slate-200 truncate block mt-0.5">{{ $request->requestedBy->name ?? 'Admin Gudang' }}</span>
                                <span class="text-[9px] font-bold text-slate-400 block">Pemohon Workshop</span>
                            </div>
                        </div>

                        {{-- Col 2: Cakupan SPK --}}
                        <div class="bg-slate-50/90 dark:bg-slate-750/50 rounded-2xl p-4 border border-slate-100 dark:border-slate-700/60 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-[#22AF85] flex items-center justify-center text-lg shrink-0">
                                🎯
                            </div>
                            <div class="min-w-0">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider block">Cakupan SPK</span>
                                @if($request->work_order_id)
                                    <span class="text-xs font-black text-slate-800 dark:text-slate-200 truncate block mt-0.5">#{{ $request->workOrder->spk_number }}</span>
                                    <span class="text-[9px] font-bold text-slate-400 block">Single Order</span>
                                @else
                                    <span class="text-xs font-black text-[#22AF85] block mt-0.5">{{ $request->items->pluck('work_order_id')->unique()->filter()->count() }} SPK Batch</span>
                                    <span class="text-[9px] font-bold text-slate-400 block">Multi Target</span>
                                @endif
                            </div>
                        </div>

                        {{-- Col 3: Item Material --}}
                        <div class="bg-slate-50/90 dark:bg-slate-750/50 rounded-2xl p-4 border border-slate-100 dark:border-slate-700/60 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-lg shrink-0">
                                📦
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider block">Jumlah Material</span>
                                <span class="text-xs font-black text-slate-800 dark:text-slate-200 block mt-0.5">{{ $request->items->count() }} Jenis Item</span>
                                <span class="text-[9px] font-bold text-slate-400 truncate block" title="{{ $materialNames }}">{{ $materialNames ?: 'Bahan Baku' }}</span>
                            </div>
                        </div>

                        {{-- Col 4: Estimasi Total Belanja --}}
                        <div class="bg-gradient-to-br from-emerald-50/80 to-teal-50/80 dark:from-emerald-950/30 dark:to-teal-950/30 rounded-2xl p-4 border border-emerald-100 dark:border-emerald-900/40 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#22AF85] text-white flex items-center justify-center text-lg shadow-sm shadow-emerald-500/20 shrink-0">
                                💰
                            </div>
                            <div class="min-w-0">
                                <span class="text-[9px] font-black text-[#22AF85] uppercase tracking-wider block">Estimasi Belanja</span>
                                <span class="text-base sm:text-lg font-black text-slate-900 dark:text-white block mt-0.5">Rp {{ number_format($request->total_estimated_cost, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- TIER 3: CARD FOOTER / ACTION BAR --}}
                    <div class="px-6 py-4 bg-slate-50/40 dark:bg-slate-800/40 border-t border-slate-100 dark:border-slate-700/60 flex flex-col sm:flex-row items-center justify-between gap-4">
                        
                        {{-- Left Footer: Status Note --}}
                        <div class="flex items-center gap-2 text-xs font-bold text-slate-400">
                            @if($request->status === 'RECEIVED')
                                <span class="inline-flex items-center gap-1.5 text-[#22AF85]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    Bahan baku telah diverifikasi &amp; stok gudang telah diperbarui.
                                </span>
                            @elseif($request->status === 'PENDING')
                                <span class="inline-flex items-center gap-1.5 text-amber-500">
                                    <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Menunggu persetujuan dan nota pembelian dari Finlog.
                                </span>
                            @else
                                <span>Status pengajuan: {{ $statusLabel }}</span>
                            @endif
                        </div>

                        {{-- Right Footer: Action Buttons --}}
                        <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
                            
                            {{-- Terima Barang / Material Tiba Button --}}
                            @if(!in_array($request->status, ['RECEIVED', 'CANCELLED', 'REJECTED']))
                                <button type="button" 
                                        wire:click="quickFulfill({{ $request->id }})" 
                                        wire:confirm="Verifikasi & konfirmasi penerimaan fisik bahan baku untuk {{ $request->request_number }}? Stok akan otomatis bertambah dan SPK akan lanjut ke PRODUKSI."
                                        class="px-4 py-2.5 rounded-xl bg-[#22AF85] hover:bg-emerald-600 text-white font-black text-xs shadow-md shadow-emerald-500/20 transition-all active:scale-95 flex items-center justify-center gap-1.5 whitespace-nowrap">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Terima Barang</span>
                                </button>
                            @elseif($request->status === 'RECEIVED')
                                <span class="px-3.5 py-2 rounded-xl bg-emerald-50 text-[#22AF85] font-black text-xs border border-emerald-200 flex items-center gap-1.5 whitespace-nowrap shadow-2xs">
                                    <svg class="w-4 h-4 text-[#22AF85]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Material Tiba</span>
                                </span>
                            @endif

                            {{-- Detail Request Button --}}
                            <a href="{{ route('material-requests.show', $request) }}" wire:navigate 
                               class="px-5 py-2.5 rounded-xl bg-[#FFC232] hover:bg-amber-400 text-slate-950 font-black text-xs shadow-md shadow-amber-500/20 hover:shadow-lg transition-all active:scale-95 flex items-center justify-center gap-1.5 whitespace-nowrap">
                                <span>Detail Request</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </a>

                            {{-- High Contrast Rose Red Delete Button --}}
                            <button type="button" 
                                    wire:click="deleteRequest({{ $request->id }})" 
                                    wire:confirm="Apakah Anda yakin ingin menghapus pengajuan {{ $request->request_number }} ini?"
                                    title="Hapus Pengajuan"
                                    class="px-3.5 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white border border-rose-200/90 hover:border-rose-600 shadow-2xs hover:shadow-md hover:shadow-rose-500/20 transition-all duration-200 active:scale-95 flex items-center justify-center gap-1.5 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span class="hidden sm:inline text-xs font-bold">Hapus</span>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-dashed border-slate-300 dark:border-slate-700 p-16 text-center">
                    <div class="w-16 h-16 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                    </div>
                    <h3 class="text-base font-black text-slate-900 dark:text-white mb-1">Tidak Ada Pengajuan Belanja</h3>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mb-6">Sesuaikan filter pencarian Anda atau buat pengajuan belanja baru.</p>
                    <a href="{{ route('material-requests.create') }}" wire:navigate class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#FFC232] text-slate-950 font-black text-xs rounded-xl shadow">
                        + Buat Pengajuan Baru
                    </a>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $requests->links() }}
        </div>
    </div>
</div>
