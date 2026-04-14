<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <nav class="flex pb-1" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-xs font-medium text-gray-500">
                        <li><a href="{{ route('dashboard') }}" class="hover:text-[#22AF85] transition-colors">Dashboard</a></li>
                        <li><svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
                        <li><a href="{{ route('admin.supply-chain.index') }}" class="hover:text-[#22AF85] transition-colors">Supply Chain</a></li>
                        <li><svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
                        <li class="text-[#22AF85]">Audit Ledger</li>
                    </ol>
                </nav>
                <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight flex items-center gap-3">
                    <div class="p-2 bg-gray-900/5 rounded-lg">
                        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    Transaction <span class="text-[#22AF85]">Audit Ledger</span>
                </h2>
            </div>
            
            <a href="{{ route('admin.supply-chain.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 hover:border-[#22AF85] transition-all shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-6 bg-[#F9FAFB] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- Filter Bar -->
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <form action="{{ route('admin.supply-chain.transactions') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    {{-- Material Filter --}}
                    <div class="space-y-1.5">
                        <label class="text-[10px] uppercase font-black text-gray-400 tracking-widest pl-1">Material Item</label>
                        <select name="material_id" class="w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-gray-700 focus:ring-[#22AF85] focus:border-[#22AF85]">
                            <option value="">All Materials</option>
                            @foreach($materials as $material)
                                <option value="{{ $material->id }}" {{ request('material_id') == $material->id ? 'selected' : '' }}>
                                    {{ $material->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Type Filter --}}
                    <div class="space-y-1.5">
                        <label class="text-[10px] uppercase font-black text-gray-400 tracking-widest pl-1">Mutation Type</label>
                        <select name="type" class="w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-gray-700 focus:ring-[#22AF85] focus:border-[#22AF85]">
                            <option value="">All Types</option>
                            <option value="IN" {{ request('type') == 'IN' ? 'selected' : '' }}>Stok Masuk (IN)</option>
                            <option value="OUT" {{ request('type') == 'OUT' ? 'selected' : '' }}>Stok Keluar (OUT)</option>
                        </select>
                    </div>

                    {{-- Date From --}}
                    <div class="space-y-1.5">
                        <label class="text-[10px] uppercase font-black text-gray-400 tracking-widest pl-1">Date From</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-gray-700 focus:ring-[#22AF85] focus:border-[#22AF85]">
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 bg-[#22AF85] text-white font-extrabold py-2 px-4 rounded-xl hover:bg-[#1b8a69] transition-all shadow-md shadow-[#22AF85]/20">
                            Apply Filter
                        </button>
                        <a href="{{ route('admin.supply-chain.transactions') }}" class="p-2 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Ledger Table -->
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50/50 text-[10px] font-black text-gray-500 uppercase tracking-widest">
                            <tr>
                                <th class="px-6 py-4 border-b border-gray-100">Timestamp</th>
                                <th class="px-6 py-4 border-b border-gray-100">Item Details</th>
                                <th class="px-6 py-4 border-b border-gray-100 text-center">Type</th>
                                <th class="px-6 py-4 border-b border-gray-100">Quantity</th>
                                <th class="px-6 py-4 border-b border-gray-100">Operator</th>
                                <th class="px-6 py-4 border-b border-gray-100">Reference</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($transactions as $tx)
                                <tr class="hover:bg-gray-50/30 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $tx->created_at->format('d M Y') }}</div>
                                        <div class="text-[10px] text-gray-400 font-black uppercase">{{ $tx->created_at->format('H:i') }} WIB</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-extrabold text-[#22AF85]">{{ $tx->material->name }}</div>
                                        <div class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">{{ $tx->material->category }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $tx->type == 'IN' ? 'bg-[#22AF85]/10 text-[#22AF85] border border-[#22AF85]/20' : 'bg-red-50 text-red-600 border border-red-100' }}">
                                            {{ $tx->type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-extrabold {{ $tx->type == 'IN' ? 'text-[#22AF85]' : 'text-red-600' }}">
                                            {{ $tx->type == 'IN' ? '+' : '-' }} {{ number_format($tx->quantity, 0) }}
                                            <span class="text-[10px] text-gray-400 font-bold ml-1">{{ $tx->material->unit }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center text-[10px] font-black text-gray-500">
                                                {{ substr($tx->user->name, 0, 1) }}
                                            </div>
                                            <span class="text-sm font-bold text-gray-700">{{ $tx->user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($tx->reference_spk)
                                            <div class="inline-flex items-center gap-1.5 px-2 py-1 bg-[#FFC232]/10 rounded-lg border border-[#FFC232]/20">
                                                <svg class="w-3 h-3 text-[#FFC232]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                                <span class="text-xs font-black text-gray-800">{{ $tx->reference_spk }}</span>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Manual Adj.</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-400">
                                            <svg class="w-16 h-16 mb-4 opacity-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                                            <p class="text-lg font-bold text-gray-300">No transactions recorded</p>
                                            <p class="text-xs italic">Try adjusting your filters</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($transactions->hasPages())
                    <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
