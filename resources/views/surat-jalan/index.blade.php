<x-workshop-pwa-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-slate-800 dark:text-white leading-tight flex items-center gap-2">
            <span>📄</span> Surat Jalan Workshop
        </h2>
    </x-slot>

    <div class="py-8 bg-slate-50/50 dark:bg-slate-900/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            {{-- Title & Action Bar inside page body --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-6 bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700">
                <div>
                    <h1 class="text-xl font-black text-slate-900 dark:text-white flex items-center gap-2">
                        <span>📄</span> Dokumen Surat Jalan Internal
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">
                        Serah-terima fisik SPK antar divisi Workshop (Sortir ➔ Produksi & Produksi ➔ QC)
                    </p>
                </div>
                <a href="{{ route('surat-jalan.create') }}" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-lg shadow-indigo-200 dark:shadow-none transition-all active:scale-95 flex items-center justify-center gap-2 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Buat Surat Jalan Baru</span>
                </a>
            </div>

            {{-- Metrics Overview --}}
            @php
                $totalCount = \App\Models\SuratJalan::count();
                $dikirimCount = \App\Models\SuratJalan::where('status', 'DIKIRIM')->count();
                $diterimaCount = \App\Models\SuratJalan::where('status', 'DITERIMA')->count();
                $sortirToProdCount = \App\Models\SuratJalan::where('jenis_serah_terima', 'sortir_to_produksi')->count();
            @endphp

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Metric 1 --}}
                <div class="p-5 bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm relative overflow-hidden group hover:border-indigo-200 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Total Surat Jalan</span>
                        <span class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center font-bold text-xs">📋</span>
                    </div>
                    <p class="text-2xl font-black text-slate-900 dark:text-white mt-2">{{ $totalCount }}</p>
                    <span class="text-[10px] font-bold text-slate-400 mt-1 block">Seluruh dokumen serah-terima</span>
                </div>

                {{-- Metric 2 --}}
                <div class="p-5 bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm relative overflow-hidden group hover:border-amber-200 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase tracking-wider text-amber-600">Sedang Kirim (OTW)</span>
                        <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xs">🚚</span>
                    </div>
                    <p class="text-2xl font-black text-amber-600 mt-2">{{ $dikirimCount }}</p>
                    <span class="text-[10px] font-bold text-slate-400 mt-1 block">Belum dikonfirmasi penerima</span>
                </div>

                {{-- Metric 3 --}}
                <div class="p-5 bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm relative overflow-hidden group hover:border-emerald-200 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase tracking-wider text-emerald-600">Terverifikasi</span>
                        <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs">✅</span>
                    </div>
                    <p class="text-2xl font-black text-emerald-600 mt-2">{{ $diterimaCount }}</p>
                    <span class="text-[10px] font-bold text-slate-400 mt-1 block">Telah dikonfirmasi diterima</span>
                </div>

                {{-- Metric 4 --}}
                <div class="p-5 bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm relative overflow-hidden group hover:border-blue-200 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase tracking-wider text-blue-600">Sortir ➔ Produksi</span>
                        <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs">🔨</span>
                    </div>
                    <p class="text-2xl font-black text-blue-600 mt-2">{{ $sortirToProdCount }}</p>
                    <span class="text-[10px] font-bold text-slate-400 mt-1 block">Rute pengerjaan produksi</span>
                </div>
            </div>

            {{-- Filter Bar --}}
            <div class="bg-white dark:bg-slate-800 p-5 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 space-y-4">
                <form method="GET" action="{{ route('surat-jalan.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                    
                    {{-- Route Tabs Filter --}}
                    <div class="flex flex-wrap items-center gap-1.5 p-1 bg-slate-100 dark:bg-slate-700/60 rounded-2xl">
                        <a href="{{ route('surat-jalan.index', array_merge(request()->except('jenis'), ['jenis' => ''])) }}" 
                           class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ empty(request('jenis')) ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-sm font-black' : 'text-slate-500 hover:text-slate-800' }}">
                            Semua Rute
                        </a>
                        <a href="{{ route('surat-jalan.index', array_merge(request()->except('jenis'), ['jenis' => 'sortir_to_produksi'])) }}" 
                           class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request('jenis') == 'sortir_to_produksi' ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-sm font-black' : 'text-slate-500 hover:text-slate-800' }}">
                            🔨 Sortir ➔ Produksi
                        </a>
                        <a href="{{ route('surat-jalan.index', array_merge(request()->except('jenis'), ['jenis' => 'produksi_to_post_qc'])) }}" 
                           class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request('jenis') == 'produksi_to_post_qc' ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-sm font-black' : 'text-slate-500 hover:text-slate-800' }}">
                            ✅ Produksi ➔ QC
                        </a>
                    </div>

                    {{-- Status Dropdown & Reset --}}
                    <div class="flex items-center gap-3">
                        <select name="status" class="text-xs font-bold rounded-xl border-slate-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white p-2.5 focus:ring-indigo-500" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="DIKIRIM" {{ request('status') == 'DIKIRIM' ? 'selected' : '' }}>🚚 DIKIRIM (Belum Diterima)</option>
                            <option value="DITERIMA" {{ request('status') == 'DITERIMA' ? 'selected' : '' }}>✅ DITERIMA (Terverifikasi)</option>
                        </select>

                        @if(request()->has('jenis') || request()->has('status'))
                            <a href="{{ route('surat-jalan.index') }}" class="px-3.5 py-2.5 bg-rose-50 text-rose-600 hover:bg-rose-100 text-xs font-bold rounded-xl transition flex items-center gap-1">
                                <span>✕</span> Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Main Data List --}}
            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 dark:border-slate-700 overflow-hidden">
                
                {{-- Desktop Table View --}}
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50/80 dark:bg-slate-700/50 text-slate-400 uppercase text-[10px] font-black tracking-wider border-b border-slate-100 dark:border-slate-700">
                            <tr>
                                <th class="px-6 py-4">Nomor Surat Jalan</th>
                                <th class="px-6 py-4">Rute Serah Terima</th>
                                <th class="px-6 py-4">Muatan SPK & Jasa</th>
                                <th class="px-6 py-4">Pengirim & Waktu Kirim</th>
                                <th class="px-6 py-4">Penerima</th>
                                <th class="px-6 py-4">Status Dokumen</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse ($suratJalanList as $sj)
                                @php
                                    $totalJasa = $sj->items->sum(function($item) {
                                        return $item->workOrder?->services?->count() ?? 0;
                                    });
                                @endphp
                                <tr class="hover:bg-indigo-50/30 dark:hover:bg-slate-700/40 transition-all group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-400 font-bold text-xs flex items-center justify-center shrink-0">📄</span>
                                            <div>
                                                <span class="font-mono font-black text-xs text-slate-800 dark:text-white group-hover:text-indigo-600 transition-colors block">
                                                    {{ $sj->nomor_surat }}
                                                </span>
                                                <span class="text-[10px] text-slate-400 font-bold">Ref: #{{ $sj->id }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($sj->jenis_serah_terima == 'sortir_to_produksi')
                                            <span class="px-3 py-1.5 text-[10px] font-black uppercase tracking-wider rounded-xl bg-blue-50 text-blue-700 border border-blue-100 flex items-center gap-1.5 w-max">
                                                <span>🔨</span> Sortir ➔ Produksi
                                            </span>
                                        @else
                                            <span class="px-3 py-1.5 text-[10px] font-black uppercase tracking-wider rounded-xl bg-purple-50 text-purple-700 border border-purple-100 flex items-center gap-1.5 w-max">
                                                <span>✅</span> Produksi ➔ QC
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-0.5">
                                            <span class="px-3 py-1 rounded-xl bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300 font-black text-xs inline-flex items-center gap-1 w-max">
                                                <span>📦</span> {{ $sj->items->count() }} Pasang
                                            </span>
                                            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-400 pl-1">
                                                Total {{ $totalJasa }} Layanan Jasa
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-xs font-bold text-slate-800 dark:text-white">{{ $sj->pengirim?->name ?? 'Admin' }}</p>
                                        <p class="text-[10px] text-slate-400 font-bold mt-0.5">{{ $sj->dikirim_at ? $sj->dikirim_at->translatedFormat('d M Y H:i') : '-' }} WIB</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($sj->penerima)
                                            <p class="text-xs font-bold text-emerald-600">{{ $sj->penerima->name }}</p>
                                            <p class="text-[10px] text-slate-400 font-bold mt-0.5">{{ $sj->diterima_at ? $sj->diterima_at->translatedFormat('d M Y H:i') : '-' }} WIB</p>
                                        @else
                                            <span class="text-xs font-bold text-amber-500 italic">Belum Diterima</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($sj->status == 'DITERIMA')
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> DITERIMA
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-100 animate-pulse">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> DIKIRIM
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('surat-jalan.show', $sj->id) }}" class="px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs rounded-xl transition-all active:scale-95 inline-block">
                                            Detail
                                        </a>
                                        <a href="{{ route('surat-jalan.print', $sj->id) }}" target="_blank" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all active:scale-95 inline-block">
                                            🖨️ Cetak
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="max-w-xs mx-auto text-center space-y-3">
                                            <span class="text-4xl block">📭</span>
                                            <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Belum ada dokumen Surat Jalan</p>
                                            <a href="{{ route('surat-jalan.create') }}" class="inline-block px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold shadow-md hover:bg-indigo-700 transition">
                                                + Terbitkan Surat Jalan Pertama
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Responsive Card View (< 1024px) --}}
                <div class="block lg:hidden divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse ($suratJalanList as $sj)
                        <div class="p-5 space-y-4 hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors">
                            <div class="flex items-center justify-between">
                                <span class="font-mono font-black text-xs text-slate-800 dark:text-white">{{ $sj->nomor_surat }}</span>
                                @if ($sj->status == 'DITERIMA')
                                    <span class="px-2.5 py-0.5 rounded-lg text-[9px] font-black uppercase bg-emerald-50 text-emerald-700 border border-emerald-100">✅ DITERIMA</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-lg text-[9px] font-black uppercase bg-amber-50 text-amber-700 border border-amber-100">🚚 DIKIRIM</span>
                                @endif
                            </div>

                            <div class="flex items-center justify-between text-xs">
                                <div>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase block">Rute Handover</span>
                                    <span class="font-bold text-slate-700 dark:text-slate-300">
                                        {{ $sj->jenis_serah_terima === 'sortir_to_produksi' ? '🔨 Sortir ➔ Produksi' : '✅ Produksi ➔ QC' }}
                                    </span>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] text-slate-400 font-bold uppercase block">Muatan</span>
                                    <span class="font-black text-indigo-600">{{ $sj->items->count() }} Pasang</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-100 dark:border-slate-700">
                                <div>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase block">Pengirim</span>
                                    <span class="font-bold text-slate-700 dark:text-slate-300">{{ $sj->pengirim?->name ?? 'Admin' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('surat-jalan.show', $sj->id) }}" class="px-3 py-1.5 bg-indigo-50 text-indigo-700 font-bold text-xs rounded-xl">Detail</a>
                                    <a href="{{ route('surat-jalan.print', $sj->id) }}" target="_blank" class="px-3 py-1.5 bg-slate-100 text-slate-700 font-bold text-xs rounded-xl">🖨️ Cetak</a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400">
                            <p class="text-xs font-bold">Belum ada dokumen Surat Jalan.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination Footer --}}
                <div class="p-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800">
                    {{ $suratJalanList->links() }}
                </div>
            </div>
        </div>
    </div>
</x-workshop-pwa-layout>
