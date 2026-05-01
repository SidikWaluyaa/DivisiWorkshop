<div class="p-8 space-y-8 bg-[#FBFBFB] min-h-screen">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">BARANG <span class="text-rose-500">KELUAR</span></h1>
            <p class="text-gray-500 font-medium">Distribusi Material ke Workshop</p>
        </div>
        <a href="{{ route('storage.disbursement.create') }}" 
           class="px-6 py-3 bg-[#FFC232] text-gray-900 font-bold rounded-2xl shadow-[0_8px_20px_-6px_rgba(255,194,50,0.5)] hover:scale-105 transition-all flex items-center group">
            <div class="bg-white/20 p-1 rounded-lg mr-3 group-hover:rotate-90 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
            </div>
            Tambah Pengeluaran
        </a>
    </div>

    <!-- Stats Overview (Consistent with Purchase) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 bg-rose-500/10 rounded-2xl flex items-center justify-center text-rose-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <div class="text-2xl font-black text-gray-900">{{ $disbursements->total() }}</div>
                <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Transaksi</div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 bg-[#FFC232]/10 rounded-2xl flex items-center justify-center text-[#FFC232]">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <div class="text-2xl font-black text-gray-900">{{ \App\Models\WarehouseDisbursement::where('status', 'PENDING')->count() }}</div>
                <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">Draft / Request</div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 bg-[#22AF85]/10 rounded-2xl flex items-center justify-center text-[#22AF85]">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            </div>
            <div>
                <div class="text-2xl font-black text-[#22AF85]">Rp {{ number_format(\App\Models\WarehouseDisbursement::where('status', 'COMPLETED')->sum('total_amount'), 0, ',', '.') }}</div>
                <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Nilai Keluar</div>
            </div>
        </div>
    </div>

    <!-- Notifications -->
    @if (session()->has('message'))
        <div class="bg-[#22AF85]/10 border-l-4 border-[#22AF85] p-4 rounded-2xl shadow-sm flex items-center justify-between">
            <div class="flex items-center text-[#22AF85]">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span class="font-bold text-sm">{{ session('message') }}</span>
            </div>
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-2xl shadow-sm flex items-center justify-between">
            <div class="flex items-center text-red-700">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <span class="font-bold text-sm">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Search -->
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
        <div class="relative w-full">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" wire:model.live="search" placeholder="Cari nomor pengeluaran, SPK, atau catatan..." 
                   class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-2 focus:ring-rose-500 transition-all font-medium">
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Dokumen Keluar</th>
                        <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-[0.2em] text-center">Status</th>
                        <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Workshop / SPK</th>
                        <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Financial</th>
                        <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-[0.2em] text-right">Manajemen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($disbursements as $disbursement)
                    <tr class="group hover:bg-[#FBFBFB] transition-all">
                        <td class="px-8 py-6">
                            <div class="flex flex-col">
                                <span class="text-lg font-black text-gray-900 group-hover:text-rose-500 transition-colors tracking-tight">{{ $disbursement->disbursement_number }}</span>
                                <div class="flex items-center mt-1 space-x-2">
                                    <span class="text-xs font-bold text-gray-400">{{ $disbursement->disbursement_date->format('d M Y') }}</span>
                                    @if($disbursement->external_reference)
                                        <span class="px-2 py-0.5 bg-rose-50 rounded text-[10px] font-bold text-rose-500 uppercase tracking-widest">REF: {{ $disbursement->external_reference }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex flex-col items-center justify-center">
                                @if($disbursement->status == 'PENDING')
                                    <div class="flex items-center text-gray-400 font-bold text-[11px] bg-gray-50 px-3 py-1 rounded-lg">
                                        <div class="w-2 h-2 bg-gray-300 rounded-full mr-2 animate-pulse"></div> DRAFT
                                    </div>
                                @elseif($disbursement->status == 'COMPLETED')
                                    <div class="flex items-center text-green-600 font-bold text-[11px] bg-green-50 px-3 py-1 rounded-lg">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg> TERKIRIM
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex flex-wrap gap-1 max-w-[200px]">
                                @php $spks = $disbursement->items->pluck('spk_number')->unique()->filter(); @endphp
                                @foreach($spks as $spk)
                                    <span class="text-[10px] font-black text-gray-400 bg-gray-50 px-2 py-0.5 rounded border border-gray-100 group-hover:border-rose-200 transition-all">{{ $spk }}</span>
                                @endforeach
                                @if($spks->isEmpty()) - @endif
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="text-lg font-black text-gray-900 tracking-tight">Rp {{ number_format($disbursement->total_amount, 0, ',', '.') }}</div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $disbursement->items->count() }} Jenis Material</div>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex justify-end items-center space-x-3 opacity-0 group-hover:opacity-100 transition-all">
                                <a href="{{ route('storage.disbursement.show', $disbursement->id) }}" 
                                   class="p-2 bg-rose-50 text-rose-500 rounded-xl hover:bg-rose-500 hover:text-white transition-all shadow-sm flex items-center gap-2 group/btn">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span class="text-[10px] font-black uppercase tracking-widest hidden group-hover/btn:block">Rincian</span>
                                </a>

                                @if($disbursement->status !== 'COMPLETED')
                                    <button wire:click="completeDisbursement({{ $disbursement->id }})" 
                                            wire:confirm="Konfirmasi bahwa material sudah keluar dari gudang dan diserahkan? Stok akan otomatis berkurang."
                                            class="bg-rose-500 text-white text-[10px] font-black uppercase tracking-widest px-4 py-2 rounded-xl shadow-[0_4px_12px_rgba(244,63,94,0.3)] hover:scale-105 active:scale-95 transition-all">
                                        Out
                                    </button>
                                @endif
                                <a href="{{ route('storage.disbursement.edit', $disbursement->id) }}" 
                                   class="p-2 bg-gray-100 text-gray-500 rounded-xl hover:bg-[#FFC232] hover:text-gray-900 transition-all shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center opacity-20">
                                <svg class="w-20 h-20 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p class="text-xl font-black text-gray-900">BELUM ADA BARANG KELUAR</p>
                                <p class="font-medium text-gray-500">Mulai catat distribusi material sekarang</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100">
            {{ $disbursements->links() }}
        </div>
    </div>
</div>
