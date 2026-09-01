<x-workshop-pwa-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-slate-800 dark:text-white leading-tight flex items-center gap-2">
            <span>📄</span> 
            @if($jenis === 'sortir_to_produksi')
                Surat Jalan Sortir ➔ Produksi
            @elseif($jenis === 'produksi_to_post_qc')
                Surat Jalan Produksi ➔ QC
            @else
                Surat Jalan Workshop
            @endif
        </h2>
    </x-slot>

    <div class="py-8 bg-[#F2F1EC] dark:bg-slate-950 min-h-screen" x-data="{ 
        activeTab: '{{ $candidateCount > 0 ? 'candidates' : 'otw' }}',
        searchCandidate: '',
        selectAllCandidates: false,
        toggleAllCandidates() {
            const cbs = document.querySelectorAll('.candidate-cb');
            cbs.forEach(cb => {
                const tr = cb.closest('tr');
                if (!tr || tr.style.display !== 'none') {
                    cb.checked = this.selectAllCandidates;
                }
            });
        },
        matchesSearch(text) {
            if (!this.searchCandidate || this.searchCandidate.trim() === '') return true;
            return text.toLowerCase().includes(this.searchCandidate.toLowerCase().trim());
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            {{-- 1. TITLE & ACTION BAR (PRO MAX GLASSMORPHISM) --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 bg-slate-900 text-white rounded-3xl shadow-xl shadow-slate-950/20 border border-slate-800 relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-[#FFC232]/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 bg-[#FFC232] text-slate-950 font-black text-[10px] rounded-xl uppercase tracking-wider shadow-sm">
                            DEDICATED HANDOVER
                        </span>
                        @if($jenis === 'sortir_to_produksi')
                            <span class="text-xs font-mono font-bold text-amber-300">RUTE #1: SORTIR ➔ PRODUKSI</span>
                        @elseif($jenis === 'produksi_to_post_qc')
                            <span class="text-xs font-mono font-bold text-emerald-300">RUTE #2: PRODUKSI ➔ QC</span>
                        @else
                            <span class="text-xs font-mono font-bold text-slate-400">SEMUA RUTE WORKSHOP</span>
                        @endif
                    </div>

                    <h1 class="text-2xl font-black text-white mt-2 flex items-center gap-2 tracking-tight">
                        @if($jenis === 'sortir_to_produksi')
                            <span>🔨</span> Surat Jalan Sortir ➔ Produksi
                        @elseif($jenis === 'produksi_to_post_qc')
                            <span>✅</span> Surat Jalan Produksi ➔ Quality Control
                        @else
                            <span>📄</span> Dokumen Surat Jalan Internal Workshop
                        @endif
                    </h1>

                    <p class="text-xs text-slate-300 font-medium mt-1">
                        @if($jenis === 'sortir_to_produksi')
                            Dokumen resmi serah-terima unit SPK &amp; material dari Stasiun Sortir ke Stasiun Produksi.
                        @elseif($jenis === 'produksi_to_post_qc')
                            Dokumen resmi serah-terima unit SPK dari Stasiun Produksi ke Stasiun Quality Control (QC).
                        @else
                            Monitoring &amp; verifikasi seluruh berkas penyerahan fisik unit sepatu antar stasiun workshop.
                        @endif
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3 relative z-10">
                    <a href="{{ route('surat-jalan.create', ['jenis' => $jenis !== 'all' ? $jenis : 'sortir_to_produksi']) }}" 
                       class="px-6 py-3.5 bg-[#FFC232] hover:bg-amber-300 text-slate-950 rounded-2xl text-xs font-black uppercase tracking-wider shadow-lg shadow-amber-500/20 transition-all active:scale-95 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        <span>+ Terbitkan Surat Jalan</span>
                    </a>
                </div>
            </div>

            {{-- 2. METRICS OVERVIEW (4 BIG 4 CARDS) --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Metric 1: Kandidat SPK --}}
                <div @click="activeTab = 'candidates'" 
                     class="p-5 bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700 shadow-sm relative overflow-hidden group hover:border-[#FFC232] cursor-pointer transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Siap Handover</span>
                        <span class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-slate-700 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-xs">📦</span>
                    </div>
                    <p class="text-3xl font-black text-slate-900 dark:text-white mt-2">{{ $candidateCount }} <span class="text-xs font-bold text-slate-400">SPK</span></p>
                    <span class="text-[10px] font-extrabold text-amber-600 mt-1 block">Menunggu diterbitkan Surat Jalan</span>
                </div>

                {{-- Metric 2: OTW / Sedang Dikirim --}}
                <div @click="activeTab = 'otw'" 
                     class="p-5 bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700 shadow-sm relative overflow-hidden group hover:border-amber-400 cursor-pointer transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase tracking-wider text-amber-600">Sedang Kirim (OTW)</span>
                        <span class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-xs">🚚</span>
                    </div>
                    <p class="text-3xl font-black text-amber-600 mt-2">{{ $dikirimCount }} <span class="text-xs font-bold text-amber-500">Doc</span></p>
                    <span class="text-[10px] font-extrabold text-amber-700 mt-1 block">Belum dikonfirmasi penerima</span>
                </div>

                {{-- Metric 3: Terverifikasi / Diterima --}}
                <div @click="activeTab = 'history'" 
                     class="p-5 bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700 shadow-sm relative overflow-hidden group hover:border-emerald-400 cursor-pointer transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase tracking-wider text-emerald-600">Terverifikasi</span>
                        <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs">✅</span>
                    </div>
                    <p class="text-3xl font-black text-emerald-600 mt-2">{{ $diterimaCount }} <span class="text-xs font-bold text-emerald-500">Doc</span></p>
                    <span class="text-[10px] font-extrabold text-emerald-600 mt-1 block">Telah dikonfirmasi diterima</span>
                </div>

                {{-- Metric 4: Total Surat Jalan --}}
                <div class="p-5 bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700 shadow-sm relative overflow-hidden group hover:border-slate-400 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-500">Total Berkas</span>
                        <span class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center font-bold text-xs">📋</span>
                    </div>
                    <p class="text-3xl font-black text-slate-900 dark:text-white mt-2">{{ $totalCount }} <span class="text-xs font-bold text-slate-400">Total</span></p>
                    <span class="text-[10px] font-extrabold text-slate-400 mt-1 block">Riwayat keseluruhan rute ini</span>
                </div>
            </div>

            {{-- 3. INTERACTIVE TAB NAVIGATION --}}
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-2 overflow-x-auto">
                <div class="flex items-center gap-2">
                    <button @click="activeTab = 'candidates'" 
                            :class="activeTab === 'candidates' ? 'bg-slate-900 text-white font-black shadow-md' : 'bg-white text-slate-600 hover:bg-slate-100 font-bold'"
                            class="px-5 py-2.5 rounded-2xl text-xs transition-all flex items-center gap-2">
                        <span>📦 Kandidat SPK Siap Handover</span>
                        <span :class="activeTab === 'candidates' ? 'bg-[#FFC232] text-slate-950' : 'bg-slate-100 text-slate-700'" class="px-2 py-0.5 rounded-full text-[10px] font-black">
                            {{ $candidateCount }}
                        </span>
                    </button>

                    <button @click="activeTab = 'otw'" 
                            :class="activeTab === 'otw' ? 'bg-slate-900 text-white font-black shadow-md' : 'bg-white text-slate-600 hover:bg-slate-100 font-bold'"
                            class="px-5 py-2.5 rounded-2xl text-xs transition-all flex items-center gap-2">
                        <span>🚚 Surat Jalan OTW (Dikirim)</span>
                        <span :class="activeTab === 'otw' ? 'bg-amber-400 text-slate-950' : 'bg-amber-100 text-amber-800'" class="px-2 py-0.5 rounded-full text-[10px] font-black">
                            {{ $dikirimCount }}
                        </span>
                    </button>

                    <button @click="activeTab = 'history'" 
                            :class="activeTab === 'history' ? 'bg-slate-900 text-white font-black shadow-md' : 'bg-white text-slate-600 hover:bg-slate-100 font-bold'"
                            class="px-5 py-2.5 rounded-2xl text-xs transition-all flex items-center gap-2">
                        <span>✅ Riwayat Surat Jalan Diterima</span>
                        <span :class="activeTab === 'history' ? 'bg-emerald-400 text-slate-950' : 'bg-emerald-100 text-emerald-800'" class="px-2 py-0.5 rounded-full text-[10px] font-black">
                            {{ $diterimaCount }}
                        </span>
                    </button>
                </div>
            </div>

            {{-- TAB CONTENT 1: KANDIDAT SPK SIAP HANDOVER --}}
            <div x-show="activeTab === 'candidates'" x-cloak class="space-y-4">
                <form action="{{ route('surat-jalan.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="jenis_serah_terima" value="{{ $jenis !== 'all' ? $jenis : 'sortir_to_produksi' }}">

                    <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl shadow-sm border border-slate-200/80 dark:border-slate-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-base font-black text-slate-900 dark:text-white flex items-center gap-2">
                                <span>📦</span> Daftar SPK Siap Dibuatkan Surat Jalan
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                                Unit sepatu di Stasiun {{ $jenis === 'sortir_to_produksi' ? 'Sortir' : 'Produksi' }} yang sudah lolos tahap pengerjaan &amp; siap diserah-terimakan.
                            </p>
                        </div>

                        @if($candidateCount > 0)
                            <div class="flex items-center gap-3">
                                <label class="flex items-center gap-2 text-xs font-bold text-slate-700 dark:text-slate-300 cursor-pointer bg-slate-100 dark:bg-slate-700 px-4 py-2.5 rounded-xl">
                                    <input type="checkbox" x-model="selectAllCandidates" @change="toggleAllCandidates()" class="rounded text-amber-500 focus:ring-amber-400 w-4 h-4">
                                    <span>Pilih Semua SPK</span>
                                </label>

                                <button type="submit" class="px-5 py-2.5 bg-[#22AF85] hover:bg-emerald-600 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-md transition active:scale-95 flex items-center gap-2">
                                    <span>🚀 Terbitkan Surat Jalan Instan</span>
                                </button>
                            </div>
                        @endif
                    </div>

                    @if($candidateCount > 0)
                        {{-- REAL-TIME LIVE SEARCH BAR --}}
                        <div class="bg-white dark:bg-slate-800 p-4 rounded-3xl shadow-sm border border-slate-200/80 dark:border-slate-700 flex items-center gap-3">
                            <div class="relative flex-1">
                                <svg class="w-4 h-4 absolute left-3.5 top-1/2 transform -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input type="text" 
                                       x-model="searchCandidate" 
                                       placeholder="Cari berdasarkan Nomor SPK, Nama Pelanggan, Merk/Jenis Sepatu, atau Layanan Jasa..." 
                                       class="w-full pl-10 pr-10 py-2.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-2xl text-xs font-bold text-slate-800 dark:text-white focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-all">
                                <button type="button" 
                                        x-show="searchCandidate.length > 0" 
                                        @click="searchCandidate = ''" 
                                        class="absolute right-3.5 top-1/2 transform -translate-y-1/2 text-slate-400 hover:text-slate-600 font-black text-xs">
                                    ✕ Clear
                                </button>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200/80 dark:border-slate-700 overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-400 uppercase text-[10px] font-black tracking-wider border-b border-slate-100 dark:border-slate-700">
                                        <tr>
                                            <th class="px-6 py-4 w-12 text-center">Pilih</th>
                                            <th class="px-6 py-4">Nomor SPK &amp; Pelanggan</th>
                                            <th class="px-6 py-4">Merk &amp; Jenis Sepatu</th>
                                            <th class="px-6 py-4">Layanan Jasa Pengerjaan</th>
                                            <th class="px-6 py-4">Status Posisi SPK</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                        @foreach($availableOrders as $wo)
                                            @php
                                                $servicesString = $wo->workOrderServices->map(fn($s) => $s->service?->name)->join(' ');
                                                $searchableText = strtolower($wo->spk_number . ' ' . ($wo->customer?->name ?? '') . ' ' . ($wo->brand ?? '') . ' ' . ($wo->shoe_type ?? '') . ' ' . $servicesString);
                                            @endphp
                                            <tr x-show="matchesSearch('{{ addslashes($searchableText) }}')"
                                                class="hover:bg-amber-50/20 dark:hover:bg-slate-700/40 transition">
                                                <td class="px-6 py-4 text-center">
                                                    <input type="checkbox" name="work_order_ids[]" value="{{ $wo->id }}" class="candidate-cb rounded text-amber-500 focus:ring-amber-400 w-4 h-4">
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span class="font-mono font-black text-xs text-slate-900 dark:text-white block">{{ $wo->spk_number }}</span>
                                                    <span class="text-xs font-bold text-slate-500">{{ $wo->customer?->name ?? 'Customer Umum' }}</span>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span class="font-black text-xs text-slate-800 dark:text-white block">{{ $wo->brand ?? '-' }}</span>
                                                    <span class="text-[10px] font-bold text-slate-400">{{ $wo->shoe_type ?? '-' }}</span>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="flex flex-wrap gap-1">
                                                        @foreach($wo->workOrderServices as $svc)
                                                            <span class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-[10px] font-bold">
                                                                {{ $svc->service?->name ?? 'Jasa Workshop' }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span class="px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                        ✓ Ready Handover
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="p-12 bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700 text-center space-y-3">
                            <span class="text-4xl block">✨</span>
                            <h4 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">Tidak Ada SPK Siap Handover Saat Ini</h4>
                            <p class="text-xs font-medium text-slate-400 max-w-md mx-auto">
                                Seluruh SPK di stasiun ini telah diterbitkan Surat Jalan-nya. SPK baru akan muncul di sini otomatis setelah pengerjaan selesai disetujui.
                            </p>
                        </div>
                    @endif
                </form>
            </div>

            {{-- TAB CONTENT 2: SURAT JALAN OTW (DIKIRIM) --}}
            <div x-show="activeTab === 'otw'" x-cloak class="space-y-4">
                <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200/80 dark:border-slate-700 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-400 uppercase text-[10px] font-black tracking-wider border-b border-slate-100 dark:border-slate-700">
                                <tr>
                                    <th class="px-6 py-4">Nomor Surat Jalan</th>
                                    <th class="px-6 py-4">Muatan SPK</th>
                                    <th class="px-6 py-4">Pengirim &amp; Waktu Kirim</th>
                                    <th class="px-6 py-4">Status Transito</th>
                                    <th class="px-6 py-4 text-right">Aksi &amp; Verifikasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @forelse ($suratJalanList->where('status', 'DIKIRIM') as $sj)
                                    <tr class="hover:bg-amber-50/20 dark:hover:bg-slate-700/40 transition">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <span class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 font-bold text-xs flex items-center justify-center shrink-0">🚚</span>
                                                <div>
                                                    <span class="font-mono font-black text-xs text-slate-800 dark:text-white block">{{ $sj->nomor_surat }}</span>
                                                    <span class="text-[10px] text-slate-400 font-bold">Ref: #{{ $sj->id }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-3 py-1 rounded-xl bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300 font-black text-xs inline-flex items-center gap-1">
                                                <span>📦</span> {{ $sj->items->count() }} Pasang SPK
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-xs font-bold text-slate-800 dark:text-white">{{ $sj->pengirim?->name ?? 'Admin Workshop' }}</p>
                                            <p class="text-[10px] text-slate-400 font-bold mt-0.5">{{ $sj->dikirim_at ? $sj->dikirim_at->translatedFormat('d M Y H:i') : '-' }} WIB</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200 animate-pulse">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> DIKIRIM (OTW)
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right space-x-2">
                                            <form action="{{ route('surat-jalan.receive', $sj->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Konfirmasi terima barang fisik untuk Surat Jalan ini?')" class="px-4 py-2 bg-[#22AF85] hover:bg-emerald-600 text-white font-black text-xs rounded-xl shadow-md transition active:scale-95">
                                                    ✓ Konfirmasi Terima
                                                </button>
                                            </form>
                                            <a href="{{ route('surat-jalan.show', $sj->id) }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition inline-block">
                                                Detail
                                            </a>
                                            <a href="{{ route('surat-jalan.print', $sj->id) }}" target="_blank" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition inline-block">
                                                🖨️ Cetak
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                            <span class="text-3xl block">🚚</span>
                                            <p class="text-xs font-bold mt-2">Tidak ada Surat Jalan dalam pengiriman (OTW) saat ini.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- TAB CONTENT 3: RIWAYAT SURAT JALAN DITERIMA --}}
            <div x-show="activeTab === 'history'" x-cloak class="space-y-4">
                <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200/80 dark:border-slate-700 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-400 uppercase text-[10px] font-black tracking-wider border-b border-slate-100 dark:border-slate-700">
                                <tr>
                                    <th class="px-6 py-4">Nomor Surat Jalan</th>
                                    <th class="px-6 py-4">Muatan SPK</th>
                                    <th class="px-6 py-4">Pengirim</th>
                                    <th class="px-6 py-4">Penerima &amp; Waktu Terverifikasi</th>
                                    <th class="px-6 py-4">Status Dokumen</th>
                                    <th class="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @forelse ($suratJalanList->where('status', 'DITERIMA') as $sj)
                                    <tr class="hover:bg-emerald-50/20 dark:hover:bg-slate-700/40 transition">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 font-bold text-xs flex items-center justify-center shrink-0">📄</span>
                                                <div>
                                                    <span class="font-mono font-black text-xs text-slate-800 dark:text-white block">{{ $sj->nomor_surat }}</span>
                                                    <span class="text-[10px] text-slate-400 font-bold">Ref: #{{ $sj->id }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-3 py-1 rounded-xl bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 font-black text-xs inline-flex items-center gap-1">
                                                <span>📦</span> {{ $sj->items->count() }} Pasang SPK
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-xs font-bold text-slate-800 dark:text-white">{{ $sj->pengirim?->name ?? 'Admin Workshop' }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-xs font-bold text-emerald-600">{{ $sj->penerima?->name ?? 'Admin Penerima' }}</p>
                                            <p class="text-[10px] text-slate-400 font-bold mt-0.5">{{ $sj->diterima_at ? $sj->diterima_at->translatedFormat('d M Y H:i') : '-' }} WIB</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> DITERIMA
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right space-x-2">
                                            <a href="{{ route('surat-jalan.show', $sj->id) }}" class="px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs rounded-xl transition inline-block">
                                                Detail
                                            </a>
                                            <a href="{{ route('surat-jalan.print', $sj->id) }}" target="_blank" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition inline-block">
                                                🖨️ Cetak PDF
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                            <span class="text-3xl block">📋</span>
                                            <p class="text-xs font-bold mt-2">Belum ada riwayat Surat Jalan yang telah diterima.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-workshop-pwa-layout>
