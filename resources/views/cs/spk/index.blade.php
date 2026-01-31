<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data SPK CS (Transit Log)') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Metrics Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-[2rem] shadow-xl p-6 border-l-8 border-[#22AF85]">
                    <div class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Total SPK Dibuat</div>
                    <div class="text-3xl font-black text-gray-900 leading-none">{{ $totalSpk }}</div>
                </div>
                <div class="bg-white rounded-[2rem] shadow-xl p-6 border-l-8 border-[#FFC232]">
                    <div class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Menunggu Handover (Gudang)</div>
                    <div class="text-3xl font-black text-gray-900 leading-none">{{ $waitingHandover }}</div>
                </div>
                <div class="bg-white rounded-[2rem] shadow-xl p-6 border-l-8 border-[#22AF85]">
                    <div class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Total Nilai Transaksi</div>
                    <div class="text-3xl font-black text-gray-900 leading-none">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] shadow-xl overflow-hidden border border-gray-100" x-data="{ selected: [], selectAll: false }">
                <div class="p-8 bg-white border-b border-gray-50">
                    
                    {{-- Filters & Bulk Actions --}}
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-8">
                        <form method="GET" class="flex-1 grid grid-cols-1 md:grid-cols-4 gap-3 w-full">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No SPK / Customer..." class="rounded-2xl border-none bg-gray-50 px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-[#22AF85] shadow-sm">
                            
                            <select name="status" class="rounded-2xl border-none bg-gray-50 px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-[#22AF85] shadow-sm">
                                <option value="">Semua Status</option>
                                <option value="WAITING_DP" {{ request('status') == 'WAITING_DP' ? 'selected' : '' }}>Menunggu DP</option>
                                <option value="DP_PAID" {{ request('status') == 'DP_PAID' ? 'selected' : '' }}>DP Lunas</option>
                                <option value="HANDED_TO_WORKSHOP" {{ request('status') == 'HANDED_TO_WORKSHOP' ? 'selected' : '' }}>Masuk Gudang</option>
                            </select>
                            
                            <div class="flex gap-2 col-span-1 md:col-span-2">
                                <input type="date" name="date_from" value="{{ request('date_from') }}" class="flex-1 rounded-2xl border-none bg-gray-50 px-4 py-3 text-sm font-bold shadow-sm">
                                <button type="submit" class="px-8 bg-[#22AF85] text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:shadow-lg transition">
                                    Filter
                                </button>
                            </div>
                        </form>

                        @if(count($spks) > 0)
                        <div x-show="selected.length > 0" x-transition class="flex items-center gap-3">
                            <span class="text-[10px] font-black uppercase text-gray-400"><span x-text="selected.length"></span> Item Terpilih</span>
                            <form action="{{ route('cs.spk.bulk-destroy') }}" method="POST" onsubmit="return confirm('Hapus SPK yang dipilih?')">
                                @csrf @method('DELETE')
                                <template x-for="id in selected" :key="id">
                                    <input type="hidden" name="ids[]" :value="id">
                                </template>
                                <button type="submit" class="bg-red-500 text-white px-6 py-3 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl hover:bg-red-600 transition">
                                    🗑️ Hapus Banyak
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>

                    {{-- Table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-gray-50 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                    <th class="px-6 py-4">
                                        <input type="checkbox" x-model="selectAll" @change="selected = selectAll ? [{{ $spks->pluck('id')->implode(',') }}] : []" class="rounded text-[#22AF85] focus:ring-[#22AF85]">
                                    </th>
                                    <th class="px-6 py-4">No SPK</th>
                                    <th class="px-6 py-4">Customer</th>
                                    <th class="px-6 py-4 text-right">Total & DP</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($spks as $spk)
                                <tr class="hover:bg-gray-50/50 transition group">
                                    <td class="px-6 py-4">
                                        <input type="checkbox" :value="{{ $spk->id }}" x-model="selected" class="rounded text-[#22AF85] focus:ring-[#22AF85]">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($spk->lead)
                                            <a href="{{ route('cs.leads.show', $spk->lead->id) }}" class="font-black text-[#22AF85] bg-[#22AF85]/5 px-3 py-1 rounded-lg border border-[#22AF85]/20 hover:bg-[#22AF85]/10 transition">
                                                {{ $spk->spk_number }}
                                            </a>
                                        @else
                                            <span class="font-black text-gray-400 bg-gray-100 px-3 py-1 rounded-lg border border-gray-200 cursor-not-allowed">
                                                {{ $spk->spk_number }} (Lead Deleted)
                                            </span>
                                        @endif
                                        <div class="text-[10px] text-gray-400 font-bold mt-1">{{ $spk->created_at->format('d M Y H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-black text-gray-900">{{ $spk->lead?->customer_name ?? 'Unknown Customer' }}</div>
                                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">{{ $spk->lead?->customer_phone ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="font-black text-gray-900">Rp {{ number_format($spk->total_price, 0, ',', '.') }}</div>
                                        <div class="text-[10px] font-bold text-gray-400">DP: Rp {{ number_format($spk->dp_amount, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-[9px] font-black uppercase tracking-widest rounded-full {{ $spk->status_badge_class }}">
                                            {{ $spk->label }}
                                        </span>
                                        @if($spk->work_order_id && $spk->workOrder)
                                            <div class="text-[9px] text-[#22AF85] font-black mt-1 uppercase">To: {{ $spk->workOrder->spk_number }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('cs.spk.export-pdf', $spk->id) }}" target="_blank" class="p-2 bg-gray-50 text-gray-400 hover:bg-[#22AF85]/10 hover:text-[#22AF85] rounded-xl transition shadow-sm" title="Download PDF">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path></svg>
                                            </a>
                                            @if(!$spk->work_order_id && $spk->lead)
                                                <a href="{{ route('cs.leads.show', $spk->lead->id) }}" class="p-2 bg-gray-50 text-gray-400 hover:bg-[#FFC232]/10 hover:text-[#FFC232] rounded-xl transition shadow-sm" title="Handover">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-20 h-20 bg-gray-50 rounded-[2rem] flex items-center justify-center text-gray-200 mb-4">
                                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </div>
                                            <p class="font-black text-gray-400 uppercase tracking-widest text-xs">Belum ada data SPK</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-8">
                        {{ $spks->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
