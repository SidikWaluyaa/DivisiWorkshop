<div class="py-6 bg-slate-100 dark:bg-gray-900 min-h-screen font-sans">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- PAGE HEADER & BREADCRUMB --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-slate-200 dark:border-gray-700">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-md bg-orange-500 text-white font-black text-[10px] uppercase tracking-widest">
                        Divisi Gudang Utama
                    </span>
                    <span class="text-xs font-semibold text-slate-400">Logistik & Serah Terima Fisik</span>
                </div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight">
                    Penerimaan Outbound (QC ➔ Gudang)
                </h1>
                <p class="text-xs font-medium text-slate-500 dark:text-gray-400 mt-1">
                    Verifikasi fisik dan konfirmasi serah terima Surat Jalan Manifest Outbound yang dikirim dari Workshop QC.
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="px-4 py-2.5 bg-slate-100 dark:bg-gray-700 hover:bg-slate-200 dark:hover:bg-gray-600 text-slate-700 dark:text-gray-200 text-xs font-bold rounded-xl transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>

        {{-- DASHBOARD OVERVIEW STAT CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Stat 1: Pending Confirmation Count --}}
            <div class="p-5 bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-slate-200 dark:border-gray-700 relative overflow-hidden group hover:border-amber-500/50 transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 dark:text-gray-400 uppercase tracking-widest">PERLU KONFIRMASI GUDANG</p>
                        <h3 class="text-3xl font-black text-slate-900 dark:text-white mt-1 font-mono">
                            {{ $pendingCount }}
                        </h3>
                        <p class="text-[10px] font-bold text-amber-600 dark:text-amber-400 mt-1 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            Dalam Pengiriman dari Workshop QC
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center font-black text-xl border border-amber-100 dark:border-amber-900/50">
                        🚚
                    </div>
                </div>
            </div>

            {{-- Stat 2: Received Today --}}
            <div class="p-5 bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-slate-200 dark:border-gray-700 relative overflow-hidden group hover:border-emerald-500/50 transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 dark:text-gray-400 uppercase tracking-widest">DITERIMA HARI INI</p>
                        <h3 class="text-3xl font-black text-slate-900 dark:text-white mt-1 font-mono">
                            {{ $receivedTodayCount }}
                        </h3>
                        <p class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 mt-1">
                            Manifest Selesai Dikonfirmasi
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-black text-xl border border-emerald-100 dark:border-emerald-900/50">
                        ✅
                    </div>
                </div>
            </div>

            {{-- Stat 3: Total Manifest Outbound Received --}}
            <div class="p-5 bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-slate-200 dark:border-gray-700 relative overflow-hidden group hover:border-blue-500/50 transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 dark:text-gray-400 uppercase tracking-widest">TOTAL MANIFEST DITERIMA</p>
                        <h3 class="text-3xl font-black text-slate-900 dark:text-white mt-1 font-mono">
                            {{ $totalReceivedCount }}
                        </h3>
                        <p class="text-[10px] font-semibold text-slate-500 dark:text-gray-400 mt-1">
                            Histori Serah Terima Outbound
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center font-black text-xl border border-blue-100 dark:border-blue-900/50">
                        📦
                    </div>
                </div>
            </div>
        </div>

        {{-- MAIN CONTENT CARD --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-slate-200 dark:border-gray-700 overflow-hidden">
            
            {{-- TAB NAVIGATION & SEARCH BAR --}}
            <div class="p-6 border-b border-slate-200 dark:border-gray-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
                {{-- Tabs --}}
                <div class="flex items-center gap-2 bg-slate-100 dark:bg-gray-750 p-1.5 rounded-2xl border border-slate-200 dark:border-gray-700">
                    <button wire:click="$set('tab', 'pending')" 
                            class="px-5 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-2 cursor-pointer
                            {{ $tab === 'pending' ? 'bg-white dark:bg-gray-800 text-amber-600 dark:text-amber-400 shadow-sm' : 'text-slate-500 hover:text-slate-900 dark:text-gray-400' }}">
                        <span>🚚 Dalam Pengiriman (Pending)</span>
                        @if($pendingCount > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500 text-white animate-pulse">
                                {{ $pendingCount }}
                            </span>
                        @endif
                    </button>

                    <button wire:click="$set('tab', 'history')" 
                            class="px-5 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-2 cursor-pointer
                            {{ $tab === 'history' ? 'bg-white dark:bg-gray-800 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-slate-500 hover:text-slate-900 dark:text-gray-400' }}">
                        <span>📋 Histori Diterima</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 dark:bg-gray-700 text-slate-700 dark:text-gray-300">
                            {{ $totalReceivedCount }}
                        </span>
                    </button>
                </div>

                {{-- Search Control --}}
                <div class="relative">
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Cari Manifest / Customer / SPK..." 
                           class="text-xs border border-slate-300 dark:border-gray-600 rounded-xl pl-9 pr-4 py-2.5 bg-slate-50 dark:bg-gray-750 text-slate-900 dark:text-gray-100 focus:ring-2 focus:ring-amber-500 outline-none w-64 shadow-sm">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            {{-- MANIFEST TABLE (Zero Horizontal Scroll) --}}
            <div class="w-full">
                <table class="w-full divide-y divide-slate-200 dark:divide-gray-700 text-left table-auto">
                    <thead class="bg-slate-50 dark:bg-gray-750 text-[10px] font-black text-slate-500 dark:text-gray-400 uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3.5 whitespace-nowrap">NOMOR MANIFEST</th>
                            <th class="px-4 py-3.5 whitespace-nowrap">PENGIRIM &amp; WAKTU KIRIM</th>
                            <th class="px-4 py-3.5 whitespace-nowrap">MUATAN &amp; STATUS</th>
                            <th class="px-4 py-3.5 whitespace-nowrap">CATATAN DISPATCHER</th>
                            <th class="px-4 py-3.5 text-right whitespace-nowrap">AKSI PENGESAHAN</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-gray-800 bg-white dark:bg-gray-800 text-xs">
                        @forelse($manifests as $mnf)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-gray-750/50 transition-colors">
                                {{-- Nomor Manifest --}}
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <a href="{{ route('qc.outbound.print', $mnf->id) }}" target="_blank" class="inline-flex items-center font-mono font-black text-teal-700 dark:text-teal-300 bg-teal-50 dark:bg-teal-950/60 px-2.5 py-1 rounded-lg hover:underline border border-teal-200 dark:border-teal-800 shadow-sm text-xs whitespace-nowrap" title="Cetak Surat Jalan A4">
                                        📄 {{ $mnf->manifest_number }}
                                    </a>
                                </td>

                                {{-- Pengirim & Tanggal --}}
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="font-bold text-slate-900 dark:text-white whitespace-nowrap">
                                        {{ $mnf->dispatched_at ? $mnf->dispatched_at->format('d M Y, H:i') : '-' }} WIB
                                    </div>
                                    <div class="text-[10px] font-semibold text-slate-500 dark:text-gray-400 mt-0.5 whitespace-nowrap">
                                        Oleh: {{ $mnf->dispatcher->name ?? 'Admin Workshop QC' }}
                                    </div>
                                </td>

                                {{-- Muatan & Status --}}
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5 whitespace-nowrap">
                                        <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-lg font-black text-[10px] border border-slate-200 dark:border-slate-600 whitespace-nowrap">
                                            📦 {{ $mnf->workOrders->count() }} SPK
                                        </span>
                                        @if($mnf->status === 'RECEIVED')
                                            <span class="px-2 py-0.5 rounded-lg bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 font-black text-[9px] uppercase border border-emerald-200 dark:border-emerald-800 whitespace-nowrap" title="Diterima oleh {{ $mnf->receiver->name ?? 'Gudang' }}">
                                                DITERIMA GUDANG
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-lg bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 font-black text-[9px] uppercase border border-amber-200 dark:border-amber-800 whitespace-nowrap animate-pulse">
                                                DALAM PENGIRIMAN
                                            </span>
                                        @endif
                                    </div>
                                    @if($mnf->status === 'RECEIVED' && $mnf->received_at)
                                        <div class="text-[9px] text-emerald-700 dark:text-emerald-400 font-semibold mt-1">
                                            Penerima: {{ $mnf->receiver->name ?? 'Gudang' }} ({{ $mnf->received_at->format('d/m H:i') }})
                                        </div>
                                    @endif
                                </td>

                                {{-- Catatan Dispatcher --}}
                                <td class="px-4 py-4 max-w-[160px]">
                                    <span class="text-[10px] text-slate-400 dark:text-gray-400 italic truncate block max-w-[150px]" title="{{ $mnf->notes }}">
                                        {{ $mnf->notes ?? '-' }}
                                    </span>
                                </td>

                                {{-- Aksi Pengesahan --}}
                                <td class="px-4 py-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center justify-end gap-1.5 whitespace-nowrap">
                                        <a href="{{ route('qc.outbound.print', $mnf->id) }}" target="_blank" class="px-2.5 py-1 bg-slate-800 hover:bg-black text-white rounded-lg text-[10px] font-black uppercase tracking-wider shadow-sm transition-all active:scale-95 whitespace-nowrap flex items-center gap-1">
                                            🖨️ Cetak
                                        </a>

                                        @if($mnf->status === 'SENT')
                                            <button wire:click="confirmReceive({{ $mnf->id }})" 
                                                    wire:confirm="Konfirmasi serah terima fisik {{ $mnf->workOrders->count() }} unit SPK pada Manifest #{{ $mnf->manifest_number }} oleh Gudang Utama?"
                                                    class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[10px] font-black uppercase tracking-wider shadow-md transition-all active:scale-95 cursor-pointer whitespace-nowrap flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                Terima Gudang
                                            </button>
                                        @endif
                                        
                                        <button wire:click="toggleManifestDetail({{ $mnf->id }})" class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-gray-700 text-slate-400 transition-colors" title="Lihat SPK di Dalam Manifest">
                                            <svg class="w-3.5 h-3.5 {{ $expandedManifestId === $mnf->id ? 'rotate-180' : '' }} transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            {{-- Collapsible Manifest Items Detail Drawer --}}
                            @if($expandedManifestId === $mnf->id)
                                <tr class="bg-slate-50 dark:bg-gray-850">
                                    <td colspan="5" class="p-5 border-t border-b border-slate-200 dark:border-gray-700">
                                        <div class="space-y-3">
                                            <div class="flex items-center justify-between">
                                                <span class="text-[10px] font-black uppercase text-slate-500 tracking-wider">
                                                    Rincian SPK dalam Manifest #{{ $mnf->manifest_number }} ({{ $mnf->workOrders->count() }} Unit Sepatu):
                                                </span>
                                                @if($mnf->status === 'SENT')
                                                    <button wire:click="confirmReceive({{ $mnf->id }})" 
                                                            wire:confirm="Konfirmasi serah terima fisik {{ $mnf->workOrders->count() }} unit SPK pada Manifest #{{ $mnf->manifest_number }} oleh Gudang Utama?"
                                                            class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow transition-all active:scale-95 cursor-pointer">
                                                        ✓ Konfirmasi Terima Semua {{ $mnf->workOrders->count() }} SPK
                                                    </button>
                                                @endif
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                                @foreach($mnf->workOrders as $wo)
                                                    <div class="p-3 bg-white dark:bg-gray-800 rounded-xl border border-slate-200 dark:border-gray-700 text-xs shadow-sm space-y-1">
                                                        <div class="flex items-center justify-between">
                                                            <span class="font-mono font-black text-slate-900 dark:text-white bg-slate-100 dark:bg-gray-700 px-2 py-0.5 rounded text-[11px] border border-slate-200 dark:border-gray-600">
                                                                {{ $wo->spk_number }}
                                                            </span>
                                                            <span class="text-[9px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200">
                                                                LOLOS QC
                                                            </span>
                                                        </div>
                                                        <div class="font-bold text-slate-900 dark:text-white text-xs mt-1">{{ $wo->customer_name }}</div>
                                                        <div class="text-[11px] font-semibold text-slate-500 dark:text-gray-400">{{ $wo->shoe_brand }} - {{ $wo->shoe_type }}</div>
                                                        <div class="pt-1.5 flex flex-wrap gap-1">
                                                            @foreach($wo->workOrderServices as $svc)
                                                                <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                                                                    {{ $svc->custom_service_name ?? ($svc->service ? $svc->service->name : 'Layanan') }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">
                                    @if($tab === 'pending')
                                        Tidak ada Manifest Outbound yang sedang dalam pengiriman dari QC saat ini.
                                    @else
                                        Belum ada histori Manifest Outbound yang diterima oleh Gudang Utama.
                                    @endif
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
