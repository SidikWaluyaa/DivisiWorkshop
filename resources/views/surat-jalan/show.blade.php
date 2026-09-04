<x-workshop-pwa-layout>
    <div x-data="{
        showModal: false,
        selectedWoId: null,
        selectedSpk: '',
        selectedShoe: '',
        station: 'prod_upper',
        technicianId: '',
        availableStations: [],
        openModal(woId, spk, shoe, stations) {
            this.selectedWoId = woId;
            this.selectedSpk = spk;
            this.selectedShoe = shoe;
            this.availableStations = stations;
            if (stations.length > 0) {
                this.station = stations[0].key;
            }
            this.technicianId = '';
            this.showModal = true;
        }
    }" class="py-8 bg-slate-50/50 dark:bg-slate-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            @php
                $totalSpk = $suratJalan->items->count();
                $totalJasa = $suratJalan->items->sum(function($item) {
                    return $item->workOrder?->services?->count() ?? 0;
                });
                $totalMaterial = $suratJalan->items->sum(function($item) {
                    return $item->workOrder?->materials?->count() ?? 0;
                });

                // Count incomplete production tasks in this Surat Jalan
                $incompleteSpkCount = 0;
                if ($suratJalan->jenis_serah_terima === 'produksi_to_post_qc') {
                    foreach ($suratJalan->items as $it) {
                        if ($it->workOrder && !$it->workOrder->is_production_finished) {
                            $incompleteSpkCount++;
                        }
                    }
                }
            @endphp

            {{-- FLASH NOTIFICATIONS (UI/UX Pro Max) --}}
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200 flex items-start gap-3 shadow-sm animate-fade-in">
                    <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-black shrink-0 text-sm">✓</div>
                    <div class="flex-1 text-xs">
                        <span class="font-black block text-sm mb-0.5">Berhasil!</span>
                        <p class="font-medium text-emerald-800 dark:text-emerald-300">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-900 dark:text-rose-200 flex items-start gap-3 shadow-sm animate-shake">
                    <div class="w-8 h-8 rounded-xl bg-rose-600 text-white flex items-center justify-center font-black shrink-0 text-sm">✕</div>
                    <div class="flex-1 text-xs">
                        <span class="font-black block text-sm mb-0.5">Perhatian / Validasi Diperlukan</span>
                        <p class="font-medium text-rose-800 dark:text-rose-300">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @if(session('info'))
                <div class="p-4 rounded-2xl bg-sky-50 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-800 text-sky-900 dark:text-sky-200 flex items-start gap-3 shadow-sm">
                    <div class="w-8 h-8 rounded-xl bg-sky-600 text-white flex items-center justify-center font-black shrink-0 text-sm">ℹ</div>
                    <div class="flex-1 text-xs">
                        <span class="font-black block text-sm mb-0.5">Informasi</span>
                        <p class="font-medium text-sky-800 dark:text-sky-300">{{ session('info') }}</p>
                    </div>
                </div>
            @endif

            {{-- 1. TOP METADATA GLASSMORPHISM CARD --}}
            <div class="bg-white/95 dark:bg-slate-800/95 backdrop-blur-xl rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 dark:border-slate-700/80 space-y-6">
                
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-700/60 pb-5">
                    <div>
                        <div class="flex items-center gap-3 flex-wrap">
                            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white font-mono tracking-tight flex items-center gap-2">
                                <span class="text-indigo-600 dark:text-indigo-400">📄</span>
                                <span>{{ $suratJalan->nomor_surat }}</span>
                            </h1>
                            @if($suratJalan->status === 'DITERIMA')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800 shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>SUDAH DITERIMA</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-300 dark:border-amber-800 shadow-2xs">
                                    <svg class="w-3.5 h-3.5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>DALAM PENGIRIMAN</span>
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 font-medium mt-1">
                            Dokumen resmi serah-terima fisik antar divisi Workshop • Dibuat: <strong>{{ $suratJalan->created_at ? $suratJalan->created_at->translatedFormat('d M Y • H:i') : '-' }} WIB</strong>
                        </p>
                    </div>

                    <div class="flex items-center gap-3 flex-wrap">
                        <a href="{{ route('surat-jalan.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-2xs">
                            <span>← Kembali</span>
                        </a>
                        <a href="{{ route('surat-jalan.print', $suratJalan->id) }}" target="_blank" class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-md shadow-indigo-500/20 transition-all active:scale-95 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            <span>Cetak Surat Jalan</span>
                        </a>
                        <livewire:workshop.surat-jalan-edit-modal :suratJalanId="$suratJalan->id" />
                    </div>
                </div>

                {{-- 6 METRIC TILES GRID --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                    
                    {{-- Tile 1: Rute Serah Terima --}}
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-700/40 border border-slate-100 dark:border-slate-600/60">
                        <span class="text-[10px] font-black uppercase text-slate-400 block mb-1.5 tracking-wider">RUTE INTERNAL</span>
                        @if($suratJalan->jenis_serah_terima === 'sortir_to_produksi')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-black uppercase bg-blue-100 text-blue-800 dark:bg-blue-950/50 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                Sortir ➔ Produksi
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-black uppercase bg-purple-100 text-purple-800 dark:bg-purple-950/50 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                Produksi ➔ QC
                            </span>
                        @endif
                    </div>

                    {{-- Tile 2: Total Muatan SPK --}}
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-700/40 border border-slate-100 dark:border-slate-600/60">
                        <span class="text-[10px] font-black uppercase text-slate-400 block mb-1.5 tracking-wider">TOTAL SPK</span>
                        <span class="text-base font-black text-slate-900 dark:text-white flex items-center gap-1">
                            <span>📦</span> {{ $totalSpk }} SPK
                        </span>
                    </div>

                    {{-- Tile 3: Total Layanan Jasa --}}
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-700/40 border border-slate-100 dark:border-slate-600/60">
                        <span class="text-[10px] font-black uppercase text-slate-400 block mb-1.5 tracking-wider">TOTAL JASA</span>
                        <span class="text-base font-black text-indigo-600 dark:text-indigo-400 flex items-center gap-1">
                            <span>🔨</span> {{ $totalJasa }} Jasa
                        </span>
                    </div>

                    {{-- Tile 4: Total Bahan Baku --}}
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-700/40 border border-slate-100 dark:border-slate-600/60">
                        <span class="text-[10px] font-black uppercase text-slate-400 block mb-1.5 tracking-wider">TOTAL MATERIAL</span>
                        <span class="text-base font-black text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                            <span>🧵</span> {{ $totalMaterial }} Item
                        </span>
                    </div>

                    {{-- Tile 5: Pengirim --}}
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-700/40 border border-slate-100 dark:border-slate-600/60">
                        <span class="text-[10px] font-black uppercase text-slate-400 block mb-1.5 tracking-wider">PENGIRIM (PIC)</span>
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block truncate" title="{{ $suratJalan->pengirim?->name ?? 'Admin Workshop' }}">
                            {{ $suratJalan->pengirim?->name ?? 'Admin Workshop' }}
                        </span>
                        <span class="text-[10px] text-slate-400 font-semibold block">
                            {{ $suratJalan->dikirim_at ? $suratJalan->dikirim_at->format('H:i') . ' WIB' : '-' }}
                        </span>
                    </div>

                    {{-- Tile 6: Penerima --}}
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-700/40 border border-slate-100 dark:border-slate-600/60">
                        <span class="text-[10px] font-black uppercase text-slate-400 block mb-1.5 tracking-wider">PENERIMA (PIC)</span>
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block truncate" title="{{ $suratJalan->penerima?->name ?? 'Belum Dikonfirmasi' }}">
                            {{ $suratJalan->penerima?->name ?? '-' }}
                        </span>
                        <span class="text-[10px] text-slate-400 font-semibold block">
                            {{ $suratJalan->diterima_at ? $suratJalan->diterima_at->format('H:i') . ' WIB' : 'Menunggu' }}
                        </span>
                    </div>

                </div>

                @if($suratJalan->catatan)
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-700/30 border border-slate-200/80 dark:border-slate-700 text-xs">
                        <span class="font-black text-slate-500 uppercase tracking-wider text-[10px] block mb-1">📝 Catatan Surat Jalan:</span>
                        <p class="text-slate-700 dark:text-slate-200 font-medium">{{ $suratJalan->catatan }}</p>
                    </div>
                @endif

            </div>

            {{-- 2. SPK ITEMS, TEKNISI & MATERIAL TABLE CARD --}}
            <div class="bg-white/95 dark:bg-slate-800/95 backdrop-blur-xl rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 dark:border-slate-700/80 space-y-6">
                
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-700/60 pb-4">
                    <div>
                        <h3 class="text-base font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                            <span>📋</span> Rincian Muatan SPK, Teknisi Stasiun & Bahan Baku
                        </h3>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">Daftar fisik sepatu, teknisi pelaksana, dan material yang diserahterimakan</p>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-3 py-1 bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300 rounded-full text-xs font-bold">
                            {{ $totalSpk }} Unit Sepatu
                        </span>
                        <span class="px-3 py-1 bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300 rounded-full text-xs font-bold">
                            {{ $totalJasa }} Layanan
                        </span>
                        <span class="px-3 py-1 bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 rounded-full text-xs font-bold">
                            {{ $totalMaterial }} Material
                        </span>
                        @if($incompleteSpkCount > 0)
                            <span class="px-3 py-1 bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 rounded-full text-xs font-black animate-pulse">
                                ⚠️ {{ $incompleteSpkCount }} Belum Lengkap
                            </span>
                        @endif
                    </div>
                </div>

                {{-- MODERN TABLE --}}
                <div class="overflow-x-auto rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-2xs">
                    <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                        <thead class="bg-slate-50 dark:bg-slate-700/50 text-[10px] font-black text-slate-400 uppercase tracking-wider border-b border-slate-200/80 dark:border-slate-700/80">
                            <tr>
                                <th class="px-5 py-4 w-12 text-center">No</th>
                                <th class="px-5 py-4">Nomor SPK & Customer</th>
                                <th class="px-5 py-4">Merk & Tipe Sepatu</th>
                                <th class="px-5 py-4">Rincian Jasa</th>
                                <th class="px-5 py-4 bg-indigo-50/40 dark:bg-indigo-950/20">Stasiun & Teknisi Pelaksana</th>
                                <th class="px-5 py-4 bg-emerald-50/40 dark:bg-emerald-950/20">Bahan Baku / Material</th>
                                <th class="px-5 py-4 text-center">Est. Selesai</th>
                                <th class="px-5 py-4 text-center">Status Handover</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 bg-white dark:bg-slate-800">
                            @foreach ($suratJalan->items as $index => $item)
                                @php
                                    $wo = $item->workOrder;
                                    $services = $wo?->services ?? collect();
                                    $materials = $wo?->materials ?? collect();
                                    $estDate = $wo?->new_estimation_date ?? $wo?->estimation_date;

                                    // Build station completion info for this SPK
                                    $missingStationList = [];
                                    $hasUpper = $wo?->needs_prod_upper;
                                    $hasSol = $wo?->needs_prod_sol;
                                    $hasJahit = $wo?->needs_prod_jahit;
                                    $hasTreatment = $wo?->needs_prod_treatment;

                                    if ($hasUpper && empty($wo?->prod_upper_completed_at)) {
                                        $missingStationList[] = ['key' => 'prod_upper', 'label' => 'Reparasi Upper'];
                                    }
                                    if ($hasSol && empty($wo?->prod_sol_completed_at)) {
                                        $missingStationList[] = ['key' => 'prod_sol', 'label' => 'Reparasi Sol'];
                                    }
                                    if ($hasJahit && empty($wo?->qc_jahit_completed_at)) {
                                        $missingStationList[] = ['key' => 'qc_jahit', 'label' => 'QC Jahit'];
                                    }

                                    $isReadyForQc = empty($missingStationList);
                                @endphp
                                <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-700/40 transition-colors {{ !$isReadyForQc && $suratJalan->jenis_serah_terima === 'produksi_to_post_qc' ? 'bg-amber-50/30 dark:bg-amber-950/10' : '' }}">
                                    {{-- Index --}}
                                    <td class="px-5 py-4 text-center font-bold text-slate-400">
                                        {{ $index + 1 }}
                                    </td>

                                    {{-- SPK & Customer --}}
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-1.5 flex-wrap mb-1">
                                            <span class="font-mono font-black text-slate-900 dark:text-white text-xs">
                                                {{ $wo?->spk_number }}
                                            </span>
                                            @if($wo?->has_active_oto)
                                                <span class="px-1.5 py-0.2 bg-amber-500 text-slate-950 text-[9px] font-black rounded">OTO</span>
                                            @endif
                                        </div>
                                        <span class="text-slate-600 dark:text-slate-300 font-bold block text-[11px]">{{ $wo?->customer_name }}</span>
                                        @if($wo?->phone)
                                            <span class="text-[10px] text-slate-400">{{ $wo->phone }}</span>
                                        @endif
                                    </td>

                                    {{-- Shoe Info --}}
                                    <td class="px-5 py-4">
                                        <span class="font-bold text-slate-800 dark:text-white block">{{ $wo?->shoe_brand }}</span>
                                        <span class="text-xs text-slate-500 dark:text-slate-400 block">{{ $wo?->shoe_type }}</span>
                                        <span class="inline-block mt-1 px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[10px] font-bold">
                                            Size: {{ $wo?->shoe_size ?? '-' }}
                                        </span>
                                    </td>

                                    {{-- Services --}}
                                    <td class="px-5 py-4">
                                        @if($services->isNotEmpty())
                                            <ul class="space-y-1">
                                                @foreach($services as $srv)
                                                    @php
                                                        $serviceName = $srv->pivot->custom_service_name ?? $srv->name ?? $srv->service_name ?? 'Layanan Servis';
                                                    @endphp
                                                    <li class="flex items-start gap-1.5 font-bold text-slate-700 dark:text-slate-200">
                                                        <span class="text-indigo-500 font-black">•</span>
                                                        <span>{{ $serviceName }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                            <span class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 mt-1.5 block">
                                                Total {{ $services->count() }} Layanan
                                            </span>
                                        @else
                                            <span class="text-slate-400 italic text-[11px]">- Tidak ada layanan -</span>
                                        @endif
                                    </td>

                                    {{-- Stasiun & Teknisi Pelaksana (NEW / ENHANCED) --}}
                                    <td class="px-5 py-4 bg-indigo-50/20 dark:bg-indigo-950/10">
                                        <div class="space-y-1.5">
                                            
                                            {{-- 1. Reparasi Upper --}}
                                            @if($hasUpper)
                                                <div class="flex items-center justify-between gap-2 p-1.5 rounded-xl {{ $wo?->prod_upper_completed_at ? 'bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800' : 'bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800' }}">
                                                    <div class="flex items-center gap-1.5 min-w-0">
                                                        <span class="text-[10px] font-black uppercase {{ $wo?->prod_upper_completed_at ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-700 dark:text-rose-300' }}">👞 Upper:</span>
                                                        <span class="text-[11px] font-bold text-slate-800 dark:text-slate-200 truncate">
                                                            {{ $wo?->prodUpperBy?->name ?? 'Belum Ditugaskan' }}
                                                        </span>
                                                    </div>
                                                    @if($wo?->prod_upper_completed_at)
                                                        <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-200">✓ Selesai</span>
                                                    @else
                                                        <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-rose-100 text-rose-800 dark:bg-rose-900/60 dark:text-rose-200">⏳ Belum Selesai</span>
                                                    @endif
                                                </div>
                                            @endif

                                            {{-- 2. Reparasi Sol --}}
                                            @if($hasSol)
                                                <div class="flex items-center justify-between gap-2 p-1.5 rounded-xl {{ $wo?->prod_sol_completed_at ? 'bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800' : 'bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800' }}">
                                                    <div class="flex items-center gap-1.5 min-w-0">
                                                        <span class="text-[10px] font-black uppercase {{ $wo?->prod_sol_completed_at ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-700 dark:text-rose-300' }}">👟 Soling:</span>
                                                        <span class="text-[11px] font-bold text-slate-800 dark:text-slate-200 truncate">
                                                            {{ $wo?->prodSolBy?->name ?? 'Belum Ditugaskan' }}
                                                        </span>
                                                    </div>
                                                    @if($wo?->prod_sol_completed_at)
                                                        <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-200">✓ Selesai</span>
                                                    @else
                                                        <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-rose-100 text-rose-800 dark:bg-rose-900/60 dark:text-rose-200">⏳ Belum Selesai</span>
                                                    @endif
                                                </div>
                                            @endif

                                            {{-- 3. QC Jahit --}}
                                            @if($hasJahit)
                                                <div class="flex items-center justify-between gap-2 p-1.5 rounded-xl {{ $wo?->qc_jahit_completed_at ? 'bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800' : 'bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800' }}">
                                                    <div class="flex items-center gap-1.5 min-w-0">
                                                        <span class="text-[10px] font-black uppercase {{ $wo?->qc_jahit_completed_at ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-700 dark:text-rose-300' }}">🧵 QC Jahit:</span>
                                                        <span class="text-[11px] font-bold text-slate-800 dark:text-slate-200 truncate">
                                                            {{ $wo?->qcJahitBy?->name ?? 'Belum Ditugaskan' }}
                                                        </span>
                                                    </div>
                                                    @if($wo?->qc_jahit_completed_at)
                                                        <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-200">✓ Selesai</span>
                                                    @else
                                                        <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-rose-100 text-rose-800 dark:bg-rose-900/60 dark:text-rose-200">⏳ Belum Selesai</span>
                                                    @endif
                                                </div>
                                            @endif

                                            {{-- 4. Treatment (Info) --}}
                                            @if($hasTreatment)
                                                <div class="flex items-center justify-between gap-2 p-1.5 rounded-xl bg-slate-50 dark:bg-slate-700/30 border border-slate-200/60 dark:border-slate-700">
                                                    <div class="flex items-center gap-1.5 min-w-0">
                                                        <span class="text-[10px] font-black uppercase text-slate-500">✨ Treatment:</span>
                                                        <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300 truncate">
                                                            {{ $wo?->prodCleaningBy?->name ?? 'Dikerjakan di QC' }}
                                                        </span>
                                                    </div>
                                                    <span class="text-[9px] font-bold text-slate-400">Tahap QC</span>
                                                </div>
                                            @endif

                                            {{-- Button to open complete technician modal if missing --}}
                                            @if(!empty($missingStationList) && $suratJalan->status === 'DIKIRIM')
                                                <button type="button" 
                                                    @click="openModal('{{ $wo->id }}', '{{ $wo->spk_number }}', '{{ addslashes($wo->shoe_brand . ' ' . $wo->shoe_type) }}', {{ json_encode($missingStationList) }})"
                                                    class="w-full mt-1.5 px-2.5 py-1.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-black rounded-xl text-[10px] uppercase tracking-wider shadow-sm transition-all active:scale-95 flex items-center justify-center gap-1.5">
                                                    <span>⚡ Lengkapi Teknisi ({{ count($missingStationList) }})</span>
                                                </button>
                                            @endif

                                        </div>
                                    </td>

                                    {{-- Material Column --}}
                                    <td class="px-5 py-4 bg-emerald-50/20 dark:bg-emerald-950/10">
                                        @if($materials->isNotEmpty())
                                            <ul class="space-y-1.5">
                                                @foreach($materials as $mat)
                                                    @php
                                                        $matStatus = $mat->pivot->status ?? 'ALLOCATED';
                                                        
                                                        $hasArrived = in_array($matStatus, ['ALLOCATED', 'RECEIVED', 'READY', 'CONSUMED']) 
                                                            || !empty($wo?->material_arrival_date) 
                                                            || ($wo && $wo->materialRequests()->where('status', 'RECEIVED')->exists())
                                                            || \App\Models\MaterialRequestItem::where('work_order_id', $wo?->id)->where('material_id', $mat->id)->whereHas('materialRequest', fn($mr) => $mr->where('status', 'RECEIVED'))->exists()
                                                            || (($mat->stock ?? 0) >= ($mat->pivot->quantity ?? 1));

                                                        $isAllocated = $hasArrived;
                                                    @endphp
                                                    <li class="p-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 shadow-2xs flex items-center justify-between gap-2">
                                                        <div>
                                                            <span class="font-black text-slate-800 dark:text-white block text-[11px] leading-tight">
                                                                {{ $mat->name }}
                                                            </span>
                                                            <span class="text-[10px] text-slate-400 font-medium block">
                                                                Qty: <strong class="text-slate-700 dark:text-slate-200">{{ $mat->pivot->quantity }} {{ $mat->unit ?? 'pcs' }}</strong>
                                                            </span>
                                                        </div>
                                                        <div>
                                                            @if($isAllocated)
                                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[9px] font-black bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-300">
                                                                    <span>✓</span> READY
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[9px] font-black bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-300">
                                                                    <span>⏳</span> BELUM READY
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                            <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-400 mt-1.5 block">
                                                Total {{ $materials->count() }} Bahan Baku Terkait
                                            </span>
                                        @else
                                            <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-700/30 border border-dashed border-slate-200 dark:border-slate-600 text-center">
                                                <span class="text-slate-400 dark:text-slate-500 font-semibold text-[10px] block">
                                                    ℹ️ Tidak butuh bahan baku tambahan
                                                </span>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Est. Selesai --}}
                                    <td class="px-5 py-4 text-center">
                                        @if($estDate)
                                            <span class="font-black text-slate-800 dark:text-white block text-xs">
                                                {{ $estDate->translatedFormat('d M Y') }}
                                            </span>
                                            <span class="text-[10px] text-slate-400 font-medium">Target SPK</span>
                                        @else
                                            <span class="text-slate-400 italic">-</span>
                                        @endif
                                    </td>

                                    {{-- Status Handover --}}
                                    <td class="px-5 py-4 text-center">
                                        @if($suratJalan->jenis_serah_terima === 'produksi_to_post_qc')
                                            @if($isReadyForQc)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-black uppercase bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-300">
                                                    <span>✓</span> SIAP KE QC
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-black uppercase bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-300 animate-pulse">
                                                    <span>⏳</span> BELUM LENGKAP
                                                </span>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-black uppercase bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-300">
                                                <span>✓</span> SIAP PRODUKSI
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- 3. FINAL ACTION / ACCEPTANCE FOOTER BANNER --}}
                @if ($suratJalan->status == 'DIKIRIM')
                    <div class="pt-6 border-t border-slate-100 dark:border-slate-700/60 flex flex-col sm:flex-row items-center justify-between gap-4">
                        @if($incompleteSpkCount > 0 && $suratJalan->jenis_serah_terima === 'produksi_to_post_qc')
                            <div class="flex items-center gap-3 text-xs text-rose-800 dark:text-rose-200 bg-rose-50 dark:bg-rose-950/40 px-4 py-3 rounded-2xl border border-rose-200 dark:border-rose-800">
                                <span class="text-lg">⚠️</span>
                                <div>
                                    <strong class="block font-black">Terdapat {{ $incompleteSpkCount }} SPK yang stasiun produksinya / teknisinya belum selesai.</strong>
                                    <span>Gunakan tombol kuning <strong>"⚡ Lengkapi Teknisi"</strong> pada tabel di atas untuk melengkapi teknisi sebelum konfirmasi serah terima.</span>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center gap-3 text-xs text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/40 px-4 py-3 rounded-2xl border border-amber-200 dark:border-amber-800">
                                <span class="text-base">⚠️</span>
                                <span>Pastikan kondisi fisik sepatu dan bahan baku telah dihitung & sesuai sebelum mengonfirmasi penerimaan.</span>
                            </div>
                        @endif

                        <form action="{{ route('surat-jalan.receive', $suratJalan->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-8 py-3.5 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white font-black rounded-2xl text-xs uppercase tracking-widest shadow-lg shadow-emerald-500/20 transition-all active:scale-95 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span>Konfirmasi Terima Surat Jalan Ini</span>
                            </button>
                        </form>
                    </div>
                @else
                    <div class="p-5 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-950/40 dark:to-teal-950/40 rounded-2xl border border-emerald-200 dark:border-emerald-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs text-emerald-900 dark:text-emerald-200">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-lg shadow-sm">
                                ✓
                            </div>
                            <div>
                                <span class="font-black text-sm block">Surat Jalan Resmi Diterima & Diverifikasi</span>
                                <span class="text-slate-600 dark:text-slate-300 text-xs font-semibold">
                                    Diterima oleh <strong class="text-emerald-700 dark:text-emerald-300">{{ $suratJalan->penerima?->name ?? 'Penerima Workshop' }}</strong> pada {{ $suratJalan->diterima_at ? $suratJalan->diterima_at->translatedFormat('d F Y • H:i') : '-' }} WIB.
                                </span>
                            </div>
                        </div>
                        <span class="px-3 py-1 bg-emerald-600 text-white rounded-xl text-[10px] font-black uppercase tracking-wider shadow-sm">
                            VERIFIED OK
                        </span>
                    </div>
                @endif

            </div>

        </div>

        {{-- 4. MODAL LENGKAPI TEKNISI & TUNTASKAN STASIUN (UI/UX PRO MAX) --}}
        <div x-show="showModal" 
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div @click.away="showModal = false" 
                 class="bg-white dark:bg-slate-800 rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 dark:border-slate-700 space-y-6 transform transition-all"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                
                <div class="flex items-start justify-between gap-4 border-b border-slate-100 dark:border-slate-700/60 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl font-bold">
                            ⚡
                        </div>
                        <div>
                            <h3 class="text-base font-black text-slate-900 dark:text-white">Lengkapi Teknisi & Tuntaskan Stasiun</h3>
                            <p class="text-xs text-slate-500 font-medium">Catat nama teknisi yang mengerjakan stasiun produksi</p>
                        </div>
                    </div>
                    <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- SPK INFO BADGE --}}
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-700/40 border border-slate-200/80 dark:border-slate-600 flex items-center justify-between gap-3 text-xs">
                    <div>
                        <span class="text-[10px] font-black uppercase text-slate-400 block tracking-wider">TARGET SPK</span>
                        <span class="font-mono font-black text-indigo-600 dark:text-indigo-400 text-sm" x-text="selectedSpk"></span>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-black uppercase text-slate-400 block tracking-wider">SEPATU</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200 truncate block max-w-[200px]" x-text="selectedShoe"></span>
                    </div>
                </div>

                <form action="{{ route('surat-jalan.complete-technician', $suratJalan->id) }}" method="POST" class="space-y-5">
                    @csrf
                    <input type="hidden" name="work_order_id" :value="selectedWoId">

                    {{-- Stasiun Selection --}}
                    <div>
                        <label class="block text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Pilih Stasiun yang Diselesaikan:
                        </label>
                        <select name="station" x-model="station" class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-bold focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                            <template x-for="st in availableStations" :key="st.key">
                                <option :value="st.key" x-text="st.label"></option>
                            </template>
                        </select>
                    </div>

                    {{-- Teknisi Selection --}}
                    <div>
                        <label class="block text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Pilih Teknisi Pelaksana:
                        </label>
                        <select name="technician_id" x-model="technicianId" required class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-bold focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                            <option value="">-- Pilih Teknisi --</option>
                            @foreach($technicians as $tech)
                                <option value="{{ $tech->id }}">{{ $tech->name }} ({{ ucfirst($tech->role ?? 'Teknisi') }})</option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-400 mt-1 font-medium">Teknisi ini akan dicatat di log audit stasiun dan status stasiun otomatis ditandai selesai.</p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-700/60">
                        <button type="button" @click="showModal = false" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold transition">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-black text-xs uppercase tracking-wider shadow-md shadow-amber-500/20 transition-all active:scale-95 flex items-center gap-2">
                            <span>Simpan & Tuntaskan Stasiun ➔</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>
</x-workshop-pwa-layout>
