<div class="procurement-index-root min-h-screen bg-slate-50/60 dark:bg-slate-900 pb-16">
    {{-- Header Section --}}
    <div class="bg-white/95 dark:bg-slate-800/95 backdrop-blur-xl border-b border-slate-200/80 dark:border-slate-700/80 px-4 sm:px-8 py-5 sticky top-0 z-30 shadow-sm">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-[#22AF85] to-emerald-400 text-white flex items-center justify-center font-black text-xl shadow-lg shadow-emerald-500/20">
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
    <div class="max-w-7xl mx-auto px-4 sm:px-8 pt-8">
        {{-- Search & Filter Bar --}}
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-4 sm:p-6 border border-slate-200/80 dark:border-slate-700/80 shadow-sm mb-8">
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
                    <select wire:model.live="status" class="w-full md:w-auto text-xs font-bold border border-slate-200 dark:border-slate-600 rounded-2xl px-4 py-3 bg-slate-50 dark:bg-slate-700/50 text-slate-900 dark:text-white focus:ring-2 focus:ring-[#22AF85]/30 focus:border-[#22AF85] outline-none cursor-pointer">
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

        {{-- Cards List --}}
        <div class="grid grid-cols-1 gap-5">
            @forelse($requests as $request)
                @php
                    $statusColor = match($request->status) {
                        'PENDING' => 'bg-amber-500',
                        'APPROVED', 'PURCHASED' => 'bg-blue-600',
                        'RECEIVED' => 'bg-[#22AF85]',
                        'REJECTED', 'CANCELLED' => 'bg-rose-500',
                        default => 'bg-slate-400'
                    };
                    $statusLabel = match($request->status) {
                        'PENDING' => 'Menunggu Approval Finlog',
                        'APPROVED', 'PURCHASED' => 'Dalam Pengiriman (Finlog)',
                        'RECEIVED' => 'Bahan Baku Tiba di Workshop',
                        'REJECTED' => 'Ditolak',
                        'CANCELLED' => 'Dibatalkan',
                        default => $request->status
                    };
                    $typeLabel = match($request->type) {
                        'SHOPPING' => 'Belanja Umum / Gudang',
                        'PRODUCTION_PO' => 'PO Spesifik SPK',
                        default => $request->type
                    };
                @endphp
                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm hover:shadow-xl transition-all duration-300 group relative overflow-hidden">
                    {{-- Status Accent Bar --}}
                    <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $statusColor }}"></div>

                    <div class="p-6 sm:p-8 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <div class="flex items-start sm:items-center gap-5">
                            {{-- Icon Badge --}}
                            <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-700/60 flex items-center justify-center text-slate-500 dark:text-slate-300 group-hover:bg-[#22AF85]/10 group-hover:text-[#22AF85] transition-all flex-shrink-0">
                                @if(in_array($request->status, ['CANCELLED', 'REJECTED']))
                                    <svg class="w-7 h-7 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @elseif($request->status == 'PENDING')
                                    <svg class="w-7 h-7 text-amber-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @else
                                    <svg class="w-7 h-7 text-[#22AF85]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                @endif
                            </div>

                            <div class="space-y-3 min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight font-mono group-hover:text-[#22AF85] transition-colors">
                                        {{ $request->request_number }}
                                    </h2>
                                    <span class="px-3 py-1 rounded-full {{ $statusColor }} text-white text-[10px] font-black uppercase tracking-wider shadow-sm">
                                        {{ $statusLabel }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[10px] font-bold uppercase tracking-wider border border-slate-200 dark:border-slate-600">
                                        {{ $typeLabel }}
                                    </span>
                                    @if($request->finlog_request_id)
                                        <span class="px-2.5 py-0.5 rounded-lg bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 text-[10px] font-black uppercase tracking-wider border border-indigo-200 dark:border-indigo-800">
                                            Ref Finlog: {{ $request->finlog_request_id }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Meta Grid --}}
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-x-8 gap-y-2 text-xs">
                                    <div>
                                        <span class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Diajukan Oleh</span>
                                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $request->requestedBy->name ?? 'Sistem' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Tanggal</span>
                                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $request->created_at->format('d M Y, H:i') }}</span>
                                    </div>
                                    <div>
                                        <span class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Cakupan SPK</span>
                                        <span class="font-bold text-slate-800 dark:text-slate-200">
                                            @if($request->work_order_id)
                                                SPK #{{ $request->workOrder->spk_number }}
                                            @else
                                                <span class="text-[#22AF85] font-black">{{ $request->items->pluck('work_order_id')->unique()->filter()->count() }} SPK (Batch)</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Jumlah Material</span>
                                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $request->items->count() }} Jenis Item</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Total & Action Buttons --}}
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between lg:justify-end gap-4 pt-4 lg:pt-0 border-t lg:border-t-0 border-slate-100 dark:border-slate-700">
                            <div class="flex flex-col text-left lg:text-right">
                                <span class="text-[9px] font-black text-[#22AF85] uppercase tracking-wider">Estimasi Total Belanja</span>
                                <span class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">Rp {{ number_format($request->total_estimated_cost, 0, ',', '.') }}</span>
                            </div>

                            <div class="flex items-center gap-2 w-full sm:w-auto">
                                @if(in_array($request->status, ['APPROVED', 'PURCHASED', 'RECEIVED']))
                                    <button type="button" 
                                            wire:click="quickFulfill({{ $request->id }})" 
                                            wire:confirm="Verifikasi & konfirmasi penerimaan fisik bahan baku untuk {{ $request->request_number }}? Stok akan otomatis bertambah dan SPK akan lanjut ke PRODUKSI."
                                            class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-[#22AF85] hover:bg-emerald-600 text-white font-black text-xs shadow-md shadow-emerald-500/20 transition-all active:scale-95 flex items-center justify-center gap-1.5 whitespace-nowrap">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        <span>Terima Barang</span>
                                    </button>
                                @endif

                                <a href="{{ route('material-requests.show', $request) }}" wire:navigate 
                                   class="flex-1 sm:flex-none px-5 py-2.5 rounded-xl bg-[#FFC232] hover:bg-amber-400 text-slate-950 font-black text-xs shadow-md shadow-amber-500/20 transition-all active:scale-95 flex items-center justify-center gap-1.5 whitespace-nowrap">
                                    <span>Detail Request</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                </a>

                                <button type="button" 
                                        wire:click="deleteRequest({{ $request->id }})" 
                                        wire:confirm="Apakah Anda yakin ingin menghapus pengajuan ini?"
                                        class="p-2.5 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-400 hover:text-rose-600 dark:bg-slate-700 dark:hover:bg-rose-950/50 transition-all border border-slate-200 dark:border-slate-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
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
