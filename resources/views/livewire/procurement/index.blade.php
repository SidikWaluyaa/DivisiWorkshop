<div class="procurement-index-root min-h-screen bg-[#FDFDFD] pb-12">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
        
        :root {
            --primary-green: #22AF85;
            --accent-yellow: #FFC232;
        }

        .procurement-index-root {
            font-family: 'Inter', sans-serif;
        }

        /* Custom scrollbar for better feel */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f9fafb; }
        ::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--primary-green); }
    </style>

    {{-- Top Navigation / Header --}}
    <div class="bg-white border-b border-gray-100 px-8 py-4 flex items-center justify-between sticky top-0 z-30 shadow-sm">
        <div class="flex items-center gap-8 flex-1">
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Pengajuan Material</h1>
            
            {{-- Search Bar --}}
            <div class="relative max-w-md w-full">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" 
                    class="block w-full pl-11 pr-4 py-2.5 bg-gray-50 border-none rounded-xl text-sm font-medium text-gray-700 placeholder-gray-400 focus:ring-2 focus:ring-[#22AF85]/10 focus:bg-white transition-all font-bold"
                    placeholder="Find request ID, requester, or work order...">
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button class="p-2.5 text-gray-400 hover:text-[#22AF85] transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </button>
            <div class="w-10 h-10 rounded-xl bg-gray-200 border border-gray-100 overflow-hidden shadow-sm">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=22AF85&color=fff" alt="User">
            </div>
        </div>
    </div>

    {{-- Main Content Area --}}
    <div class="max-w-7xl mx-auto px-8 pt-8">
        {{-- Metadata and Action Header --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
            <div class="space-y-1">
                <p class="text-sm font-bold text-[#22AF85] uppercase tracking-widest">Procurement Module</p>
                <div class="text-xs text-gray-500 font-medium">Review and manage production material requisitions and procurement status.</div>
            </div>
            
            <div class="flex items-center gap-3">
                <button class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-bold rounded-xl hover:bg-gray-50 transition-all shadow-sm">
                    Export List
                </button>
                <a href="{{ route('material-requests.create') }}" wire:navigate class="px-6 py-3 bg-[#FFC232] text-gray-900 text-sm font-black rounded-xl hover:shadow-lg hover:shadow-[#FFC232]/20 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                    Create New
                </a>
            </div>
        </div>

        {{-- Filters Ribbon --}}
        <div class="flex items-center gap-4 mb-8">
            <div class="bg-white border border-gray-200 p-1.5 rounded-2xl flex items-center gap-2 shadow-sm">
                <select wire:model.live="status" class="bg-transparent border-none text-sm font-bold text-gray-600 focus:ring-0 px-4 py-2 cursor-pointer hover:text-[#22AF85]">
                    <option value="all">All Status</option>
                    <option value="PENDING">Pending</option>
                    <option value="APPROVED">Approved</option>
                    <option value="PURCHASED">Purchased</option>
                    <option value="REJECTED">Rejected</option>
                    <option value="CANCELLED">Cancelled</option>
                </select>
                <div class="w-px h-6 bg-gray-100 mx-1"></div>
                <div class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-gray-400 cursor-pointer hover:text-[#22AF85] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Date Range</span>
                </div>
                <div class="w-px h-6 bg-gray-100 mx-1"></div>
                <button class="p-2 text-gray-400 hover:text-[#22AF85] transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                </button>
            </div>

            <div wire:loading class="text-xs font-black text-[#22AF85] uppercase tracking-widest animate-pulse">
                Synchronizing Data...
            </div>
        </div>

        {{-- Cards List --}}
        <div class="grid grid-cols-1 gap-6">
            @forelse($requests as $request)
                @php
                    $statusColor = match($request->status) {
                        'PENDING' => 'bg-amber-500',
                        'APPROVED', 'PURCHASED' => 'bg-[#22AF85]',
                        'REJECTED', 'CANCELLED' => 'bg-rose-500',
                        default => 'bg-gray-400'
                    };
                @endphp
                <div class="bg-white rounded-[24px] border border-gray-100 shadow-sm hover:shadow-xl hover:shadow-gray-200/40 transition-all duration-500 group relative overflow-hidden">
                    {{-- Status Vertical Accent --}}
                    <div class="absolute left-0 top-0 bottom-0 w-[5px] {{ $statusColor }}"></div>

                    <div class="p-8 flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                        <div class="flex items-center gap-6">
                            {{-- Icon Box --}}
                            <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-[#22AF85]/5 group-hover:text-[#22AF85] transition-colors">
                                @if($request->status == 'CANCELLED' || $request->status == 'REJECTED')
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                @elseif($request->status == 'PENDING')
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                @else
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                @endif
                            </div>

                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <h2 class="text-xl font-black text-gray-900 tracking-tight group-hover:text-[#22AF85] transition-colors">{{ $request->request_number }}</h2>
                                    <span class="px-2.5 py-1 rounded-lg {{ $statusColor }}/10 text-{{ str_starts_with($statusColor, 'bg-') ? substr($statusColor, 3) : $statusColor }} text-[10px] font-black uppercase tracking-widest border border-{{ str_starts_with($statusColor, 'bg-') ? substr($statusColor, 3) : $statusColor }}/20">
                                        @if($request->type == 'SHOPPING') BELANJA @else PO PRODUKSI @endif • {{ $request->status }}
                                    </span>
                                </div>

                                {{-- Meta Grid --}}
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-x-12 gap-y-2">
                                    <div class="flex flex-col">
                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Requested By</span>
                                        <span class="text-sm font-bold text-gray-700">{{ $request->requestedBy->name ?? 'System' }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Date</span>
                                        <span class="text-sm font-bold text-gray-700">{{ $request->created_at->format('d M Y') }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Work Order</span>
                                        <span class="text-sm font-bold text-gray-700">
                                            @if($request->work_order_id)
                                                {{ $request->workOrder->spk_number }}
                                            @else
                                                <span class="text-[#22AF85]">{{ $request->items->pluck('work_order_id')->unique()->filter()->count() }} SPKs</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Category</span>
                                        <span class="text-sm font-bold text-gray-700 uppercase">
                                            @if($request->work_order_id)
                                                {{ $request->workOrder->category ?? '-' }}
                                            @else
                                                Mixed
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-12 self-end lg:self-center">
                            <div class="flex flex-col text-right">
                                <span class="text-[9px] font-black text-[#22AF85] uppercase tracking-widest">Estimasi</span>
                                <span class="text-2xl font-black text-gray-900">Rp {{ number_format($request->total_estimated_cost, 0, ',', '.') }}</span>
                            </div>
                            
                            <div class="flex flex-col lg:flex-row items-center gap-3">
                                @if($request->status === 'APPROVED' || ($request->status === 'PENDING' && $request->type === 'SHOPPING'))
                                    <button wire:click="quickFulfill({{ $request->id }})" 
                                            wire:confirm="Konfirmasi penerimaan barang untuk {{ $request->request_number }}? Stok akan bertambah dan SPK akan otomatis dialokasikan."
                                            class="flex items-center gap-3 px-6 py-4 rounded-2xl bg-[#22AF85] text-white font-black text-sm hover:shadow-lg hover:shadow-[#22AF85]/20 transition-all group/btn border-none whitespace-nowrap">
                                        <i class="fas fa-shopping-bag mr-1"></i>
                                        Terima Barang
                                    </button>
                                @endif

                                <a href="{{ route('material-requests.show', $request) }}" wire:navigate class="flex items-center gap-3 px-8 py-4 rounded-2xl bg-[#FFC232] text-gray-900 font-black text-sm hover:shadow-lg hover:shadow-[#FFC232]/20 transition-all group/btn border-none whitespace-nowrap">
                                    Detail
                                    <svg class="w-4 h-4 text-gray-900 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>

                                <button wire:click="deleteRequest({{ $request->id }})" 
                                        wire:confirm="Apakah Anda yakin ingin menghapus pengajuan ini? Data tidak dapat dikembalikan."
                                        class="p-4 rounded-2xl bg-gray-50 text-gray-400 hover:bg-rose-50 hover:text-rose-500 transition-all border border-gray-100 hover:border-rose-100">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-3xl border border-dashed border-gray-200 p-20 text-center">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No Requests Found</h3>
                    <p class="text-gray-400 text-sm">Adjustment your filters or create a new request.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-12">
            {{ $requests->links() }}
        </div>
    </div>
</div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
        
        div {
            font-family: 'Inter', sans-serif;
        }

        /* Custom scrollbar for better feel */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f9fafb; }
        ::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
    </style>
</div>
