<div class="py-6 bg-slate-100 dark:bg-gray-900 min-h-screen font-sans">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- PAGE HEADER & BREADCRUMB --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-slate-200 dark:border-gray-700">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-md bg-[#22AF85] text-white font-black text-[10px] uppercase tracking-widest">
                        ShoeWorkshop QC
                    </span>
                    <span class="text-xs font-semibold text-slate-400">Staging & Logistik Outbound</span>
                </div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight">
                    Staging &amp; Manifest Outbound QC
                </h1>
                <p class="text-xs font-medium text-slate-500 dark:text-gray-400 mt-1">
                    Kelola SPK yang telah Lolos QC Akhir dan terbitkan Surat Jalan Manifest Pengiriman ke Gudang Utama.
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('qc.outbound.create') }}" wire:navigate 
                   class="px-5 py-3 bg-[#FFC232] text-slate-950 font-black text-xs uppercase tracking-wider rounded-2xl hover:shadow-lg hover:shadow-[#FFC232]/20 transition-all flex items-center gap-2 border-none active:scale-95">
                    <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                    Buat Manifest Outbound
                </a>
                <a href="{{ route('qc.index') }}" class="px-4 py-3 bg-slate-100 dark:bg-gray-700 hover:bg-slate-200 dark:hover:bg-gray-600 text-slate-700 dark:text-gray-200 text-xs font-bold rounded-2xl transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
                    Kembali
                </a>
            </div>
        </div>

        {{-- DASHBOARD OVERVIEW STAT CARDS (3 COLUMNS) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Stat 1: Staging Count --}}
            <div class="p-5 bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-slate-200 dark:border-gray-700 relative overflow-hidden group hover:border-teal-500/50 transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 dark:text-gray-400 uppercase tracking-widest">SPK SIAP OUTBOUND</p>
                        <h3 class="text-3xl font-black text-slate-900 dark:text-white mt-1 font-mono">
                            {{ $this->stagingOrders->count() }}
                        </h3>
                        <p class="text-[10px] font-bold text-teal-600 dark:text-teal-400 mt-1 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-teal-500 animate-pulse"></span>
                            Lolos QC Akhir (Antrean Staging)
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-teal-50 dark:bg-teal-950/50 text-teal-600 dark:text-teal-400 flex items-center justify-center font-black text-xl border border-teal-100 dark:border-teal-900/50">
                        📦
                    </div>
                </div>
            </div>

            {{-- Stat 2: Total Manifest Dispatched --}}
            <div class="p-5 bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-slate-200 dark:border-gray-700 relative overflow-hidden group hover:border-blue-500/50 transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 dark:text-gray-400 uppercase tracking-widest">MANIFEST OUTBOUND</p>
                        <h3 class="text-3xl font-black text-slate-900 dark:text-white mt-1 font-mono">
                            {{ $manifests->total() }}
                        </h3>
                        <p class="text-[10px] font-semibold text-slate-500 dark:text-gray-400 mt-1">
                            Surat Jalan Manifest Terbit
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center font-black text-xl border border-blue-100 dark:border-blue-900/50">
                        📄
                    </div>
                </div>
            </div>

            {{-- Stat 3: Receiver Gudang Status --}}
            <div class="p-5 bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-slate-200 dark:border-gray-700 relative overflow-hidden group hover:border-amber-500/50 transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 dark:text-gray-400 uppercase tracking-widest">STATUS SERAH TERIMA GUDANG</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs font-black text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/50 px-2.5 py-0.5 rounded-lg border border-amber-200 dark:border-amber-900">
                                {{ $manifests->where('status', 'SENT')->count() }} Dalam Pengiriman
                            </span>
                        </div>
                        <p class="text-[10px] font-semibold text-slate-500 dark:text-gray-400 mt-1">
                            Menunggu Konfirmasi Gudang Utama
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center font-black text-xl border border-amber-100 dark:border-amber-900/50">
                        🚚
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 2: RIWAYAT MANIFEST OUTBOUND --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-slate-200 dark:border-gray-700 overflow-hidden">
            <div class="p-6 border-b border-slate-200 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-black text-slate-900 dark:text-white uppercase tracking-tight">Riwayat Manifest Outbound</h2>
                    <p class="text-xs text-slate-500 dark:text-gray-400">Daftar Surat Jalan Manifest yang telah diterbitkan dari QC ke Gudang Utama</p>
                </div>
                <span class="text-xs font-bold text-slate-400 bg-slate-100 dark:bg-gray-700 px-3 py-1 rounded-xl whitespace-nowrap">
                    Total: {{ $manifests->total() }} Manifest
                </span>
            </div>

            <div class="w-full">
                <table class="w-full divide-y divide-slate-200 dark:divide-gray-700 text-left table-auto">
                    <thead class="bg-slate-50 dark:bg-gray-750 text-[10px] font-black text-slate-500 dark:text-gray-400 uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3.5 whitespace-nowrap">NOMOR MANIFEST</th>
                            <th class="px-4 py-3.5 whitespace-nowrap">PENGIRIM &amp; TANGGAL</th>
                            <th class="px-4 py-3.5 whitespace-nowrap">MUATAN &amp; STATUS</th>
                            <th class="px-4 py-3.5 whitespace-nowrap">CATATAN</th>
                            <th class="px-4 py-3.5 text-right whitespace-nowrap">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-gray-800 bg-white dark:bg-gray-800 text-xs">
                        @forelse($manifests as $mnf)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-gray-750/50 transition-colors">
                                {{-- Nomor Manifest --}}
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <a href="{{ route('qc.outbound.show', $mnf->id) }}" class="inline-flex items-center font-mono font-black text-teal-700 dark:text-teal-300 bg-teal-50 dark:bg-teal-950/60 px-2.5 py-1 rounded-lg hover:underline border border-teal-200 dark:border-teal-800 shadow-sm text-xs whitespace-nowrap" title="Lihat Manifest Outbound">
                                        📄 {{ $mnf->manifest_number }}
                                    </a>
                                </td>

                                {{-- Pengirim & Tanggal --}}
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="font-bold text-slate-900 dark:text-white whitespace-nowrap">
                                        {{ $mnf->dispatched_at ? $mnf->dispatched_at->format('d M Y, H:i') : '-' }} WIB
                                    </div>
                                    <div class="text-[10px] font-semibold text-slate-500 dark:text-gray-400 mt-0.5 whitespace-nowrap">
                                        Oleh: {{ $mnf->dispatcher->name ?? 'Admin Workshop QC' }}
                                    </div>
                                </td>

                                {{-- Muatan & Status --}}
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5 whitespace-nowrap">
                                        <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-lg font-black text-[10px] border border-slate-200 dark:border-slate-600 whitespace-nowrap">
                                            📦 {{ $mnf->workOrders->count() }} SPK
                                        </span>
                                        @if($mnf->status === 'RECEIVED')
                                            <span class="px-2 py-0.5 rounded-lg bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 font-black text-[9px] uppercase border border-emerald-200 dark:border-emerald-800 whitespace-nowrap" title="Diterima oleh {{ $mnf->receiver->name ?? 'Gudang' }}">
                                                DITERIMA GUDANG
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-lg bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 font-black text-[9px] uppercase border border-amber-200 dark:border-amber-800 whitespace-nowrap">
                                                DALAM PENGIRIMAN
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Catatan (Ringkas & Perkecil) --}}
                                <td class="px-4 py-3.5 max-w-[160px]">
                                    <span class="text-[10px] text-slate-400 dark:text-gray-400 italic truncate block max-w-[150px]" title="{{ $mnf->notes }}">
                                        {{ $mnf->notes ?? '-' }}
                                    </span>
                                </td>

                                {{-- Aksi (Sleek Compact Buttons) --}}
                                <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center justify-end gap-1.5 whitespace-nowrap">
                                        <a href="{{ route('qc.outbound.show', $mnf->id) }}" class="px-2.5 py-1 bg-slate-900 hover:bg-black text-white dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg text-[10px] font-black uppercase tracking-wider shadow-sm transition-all active:scale-95 whitespace-nowrap">
                                            Detail
                                        </a>
                                        <a href="{{ route('qc.outbound.print', $mnf->id) }}" target="_blank" class="px-2.5 py-1 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-[10px] font-black uppercase tracking-wider shadow-sm transition-all active:scale-95 whitespace-nowrap" title="Cetak Surat Jalan A4">
                                            🖨️ Cetak
                                        </a>

                                        @if($mnf->status === 'SENT')
                                            <button wire:click="receiveManifest({{ $mnf->id }})" 
                                                    wire:confirm="Konfirmasi penerimaan fisik {{ $mnf->workOrders->count() }} SPK pada Manifest #{{ $mnf->manifest_number }} oleh Gudang Utama?"
                                                    class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[10px] font-black uppercase tracking-wider shadow-sm transition-all active:scale-95 cursor-pointer whitespace-nowrap">
                                                Terima
                                            </button>
                                        @endif
                                        
                                        <button wire:click="toggleManifestDetail({{ $mnf->id }})" class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-gray-700 text-slate-400 transition-colors">
                                            <svg class="w-3.5 h-3.5 {{ $expandedManifestId === $mnf->id ? 'rotate-180' : '' }} transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @if($expandedManifestId === $mnf->id)
                                <tr class="bg-slate-50 dark:bg-gray-850">
                                    <td colspan="5" class="p-5 border-t border-b border-slate-200 dark:border-gray-700">
                                        <div class="space-y-2">
                                            <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Daftar SPK dalam Manifest #{{ $mnf->manifest_number }}:</span>
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                                @foreach($mnf->workOrders as $wo)
                                                    <div class="p-3 bg-white dark:bg-gray-800 rounded-xl border border-slate-200 dark:border-gray-700 text-xs shadow-sm">
                                                        <div class="font-mono font-black text-slate-900 dark:text-white">{{ $wo->spk_number }}</div>
                                                        <div class="text-[11px] font-semibold text-slate-600 dark:text-gray-400 mt-0.5">{{ $wo->customer_name }} — {{ $wo->shoe_brand }}</div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-slate-400 italic">
                                    Belum ada histori Manifest Outbound.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($manifests->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-gray-700">
                    {{ $manifests->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
