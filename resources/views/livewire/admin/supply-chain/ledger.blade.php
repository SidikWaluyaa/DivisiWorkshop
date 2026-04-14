<div class="supply-chain-ledger min-h-screen bg-[#F9FAFB] pb-12">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800;900&display=swap');
        
        .supply-chain-ledger {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #111827;
        }

        .font-inter { font-family: 'Inter', sans-serif; }

        [x-cloak] { display: none !important; }

        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #22AF85; }
    </style>

    {{-- White Header matching Dashboard --}}
    <div class="bg-white text-gray-900 px-8 py-5 flex items-center justify-between sticky top-0 z-50 border-b border-gray-100 shadow-sm">
        <div class="flex items-center gap-12 flex-1">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-[#22AF85] rounded-2xl flex items-center justify-center shadow-lg shadow-[#22AF85]/20">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-base font-black uppercase tracking-widest leading-none text-[#22AF85]">Buku Kas Audit</h1>
                    <p class="text-[10px] text-gray-400 font-bold mt-1 uppercase">Log Mutasi Material</p>
                </div>
            </div>

            <div class="hidden md:flex items-center gap-2">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-3 text-[10px] font-black uppercase tracking-widest">
                        <li><a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-[#22AF85] transition-colors">Utama</a></li>
                        <li><svg class="h-3 w-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg></li>
                        <li><a href="{{ route('admin.supply-chain.index') }}" wire:navigate class="text-gray-400 hover:text-[#22AF85] transition-colors">Supply Chain</a></li>
                        <li><svg class="h-3 w-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg></li>
                        <li class="text-[#22AF85]">Log Mutasi</li>
                    </ol>
                </nav>
            </div>
        </div>

        <a href="{{ route('admin.supply-chain.index') }}" wire:navigate
           class="inline-flex items-center px-6 py-3 bg-gray-50 text-gray-900 border border-gray-100 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-gray-100 transition-all gap-3 overflow-hidden">
            <svg class="w-4 h-4 text-[#22AF85]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    <div class="max-w-[1600px] mx-auto px-8 mt-10 space-y-8">
        
        <!-- Filter Bar -->
        <div class="bg-white border border-gray-100 rounded-[2.5rem] p-10 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                {{-- Material Filter --}}
                <div class="space-y-3">
                    <label class="text-[10px] uppercase font-black text-gray-400 tracking-[0.2em] pl-1">Item Material</label>
                    <div class="relative">
                        <select wire:model.live="material_id" class="w-full bg-gray-50 border-gray-100 rounded-2xl text-xs font-black text-gray-700 focus:ring-2 focus:ring-[#22AF85] focus:border-[#22AF85] py-3.5 pl-4 pr-10 appearance-none lowercase">
                            <option value="">Semua Material</option>
                            @foreach($materials as $material)
                                <option value="{{ $material->id }}">{{ $material->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>

                {{-- Type Filter --}}
                <div class="space-y-3">
                    <label class="text-[10px] uppercase font-black text-gray-400 tracking-[0.2em] pl-1">Tipe Mutasi</label>
                    <div class="relative">
                        <select wire:model.live="type" class="w-full bg-gray-50 border-gray-100 rounded-2xl text-xs font-black text-gray-700 focus:ring-2 focus:ring-[#22AF85] focus:border-[#22AF85] py-3.5 pl-4 pr-10 appearance-none">
                            <option value="">Semua Tipe</option>
                            <option value="IN">Stok Masuk (IN)</option>
                            <option value="OUT">Stok Keluar (OUT)</option>
                        </select>
                        <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>

                {{-- Date From --}}
                <div class="space-y-3">
                    <label class="text-[10px] uppercase font-black text-gray-400 tracking-[0.2em] pl-1">Dari Tanggal</label>
                    <input type="date" wire:model.live="date_from" class="w-full bg-gray-50 border-gray-100 rounded-2xl text-xs font-black text-gray-700 focus:ring-2 focus:ring-[#22AF85] focus:border-[#22AF85] py-3 px-4">
                </div>

                {{-- Date To & Reset --}}
                <div class="space-y-3">
                    <label class="text-[10px] uppercase font-black text-gray-400 tracking-[0.2em] pl-1">Sampai Tanggal</label>
                    <div class="flex gap-4">
                        <input type="date" wire:model.live="date_to" class="flex-1 bg-gray-50 border-gray-100 rounded-2xl text-xs font-black text-gray-700 focus:ring-2 focus:ring-[#22AF85] focus:border-[#22AF85] py-3 px-4">
                        <button wire:click="resetFilters" class="p-4 bg-[#FFC232] text-gray-900 rounded-2xl hover:bg-gray-900 hover:text-white transition-all shadow-lg shadow-[#FFC232]/20" title="Reset Filter">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ledger Table -->
        <div class="bg-white border border-gray-100 rounded-[3rem] shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50/50 text-[11px] font-black text-gray-400 uppercase tracking-[0.25em]">
                        <tr>
                            <th class="px-8 py-10 border-b border-gray-50">Timestamp</th>
                            <th class="px-8 py-10 border-b border-gray-50">Detail Item</th>
                            <th class="px-8 py-10 border-b border-gray-50 text-center">Tipe</th>
                            <th class="px-8 py-10 border-b border-gray-50">Kuantitas</th>
                            <th class="px-8 py-10 border-b border-gray-50">Operator</th>
                            <th class="px-8 py-10 border-b border-gray-50 text-right">Referensi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($transactions as $tx)
                            <tr class="hover:bg-gray-50/50 transition-colors group" wire:key="tx-row-{{ $tx->id }}">
                                <td class="px-8 py-8">
                                    <div class="text-sm font-black text-gray-900">{{ $tx->created_at->format('d M Y') }}</div>
                                    <div class="text-[10px] text-gray-300 font-bold uppercase tracking-wider mt-1">{{ $tx->created_at->format('H:i') }} WIB</div>
                                </td>
                                <td class="px-8 py-8">
                                    <div class="text-base font-black text-[#22AF85]">{{ $tx->material->name }}</div>
                                    <div class="text-[10px] text-gray-400 uppercase font-black tracking-[0.1em] mt-2">{{ $tx->material->category }}</div>
                                </td>
                                <td class="px-8 py-8 text-center uppercase tracking-widest text-[10px] font-black">
                                    <span class="inline-flex items-center px-4 py-1.5 rounded-xl {{ $tx->type == 'IN' ? 'bg-[#22AF85] text-white shadow-lg shadow-[#22AF85]/20' : 'bg-gray-900 text-[#FFC232]' }}">
                                        {{ $tx->type }}
                                    </span>
                                </td>
                                <td class="px-8 py-8">
                                    <div class="text-lg font-black font-inter {{ $tx->type == 'IN' ? 'text-[#22AF85]' : 'text-gray-900' }}">
                                        {{ $tx->type == 'IN' ? '+' : '-' }} {{ number_format($tx->quantity, 0) }}
                                        <span class="text-[10px] text-gray-300 font-black ml-1 uppercase">{{ $tx->material->unit }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-8">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-2xl bg-gray-50 flex items-center justify-center text-[11px] font-black text-[#22AF85] border border-gray-100">
                                            {{ substr($tx->user->name, 0, 1) }}
                                        </div>
                                        <span class="text-sm font-black text-gray-700">{{ $tx->user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-8 text-right">
                                    @if($tx->reference_type === 'WorkOrder')
                                        <div class="inline-flex items-center gap-3 px-4 py-2 bg-gray-50 rounded-2xl border border-gray-100 text-gray-900 shadow-sm">
                                            <div class="w-2 h-2 rounded-full bg-[#FFC232]"></div>
                                            <span class="text-xs font-black tracking-widest">{{ $tx->reference_label }}</span>
                                        </div>
                                    @elseif($tx->reference_type === 'MaterialRequest')
                                        <div class="inline-flex items-center gap-3 px-4 py-2 bg-gray-50 rounded-2xl border border-gray-100 text-gray-900 shadow-sm">
                                            <div class="w-2 h-2 rounded-full bg-[#22AF85]"></div>
                                            <span class="text-xs font-black tracking-widest">{{ $tx->reference_label }}</span>
                                        </div>
                                    @elseif($tx->reference_label)
                                        <div class="inline-flex items-center gap-3 px-4 py-2 bg-gray-50 rounded-2xl border border-gray-100 text-gray-900 shadow-sm">
                                            <div class="w-2 h-2 rounded-full bg-gray-400"></div>
                                            <span class="text-xs font-black tracking-widest">{{ $tx->reference_label }}</span>
                                        </div>
                                    @else
                                        <span class="text-[10px] font-black text-gray-300 uppercase tracking-widest italic">Manual Adj.</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-8 py-32 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-20 h-20 bg-gray-50 rounded-[2rem] flex items-center justify-center text-gray-200 mb-6">
                                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                                        </div>
                                        <p class="text-xl font-black text-gray-400 uppercase tracking-widest leading-none">Tidak ada transaksi</p>
                                        <p class="text-[10px] text-gray-300 font-bold uppercase tracking-[0.2em] mt-3">Sesuaikan filter pencarian Anda</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transactions->hasPages())
                <div class="px-10 py-8 bg-gray-50/50 border-t border-gray-100 custom-pagination">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
