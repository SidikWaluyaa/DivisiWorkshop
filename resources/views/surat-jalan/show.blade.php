@php
    $totalSpk = $suratJalan->items->count();
    $totalJasa = $suratJalan->items->sum(function($item) {
        $wo = $item->workOrder;
        if (!$wo) return 0;
        return ($wo->workOrderServices && $wo->workOrderServices->isNotEmpty()) 
            ? $wo->workOrderServices->count() 
            : ($wo->services?->count() ?? 0);
    });
    $totalMaterial = $suratJalan->items->sum(function($item) {
        return $item->workOrder?->materials?->count() ?? 0;
    });

    // Breakdown Layanan Jasa & Materials Mapping for Interactive Modal
    $serviceBreakdown = [];
    $servicesModalMap = [];
    $materialBreakdown = [];
    $materialsModalMap = [];

    foreach ($suratJalan->items as $item) {
        $wo = $item->workOrder;
        if (!$wo) continue;

        // Resolve cover photo
        $coverPhoto = $wo->photos ? ($wo->photos->firstWhere('is_spk_cover', true) ?: $wo->photos->first()) : null;
        $coverPhotoUrl = $coverPhoto?->photo_url;

        // Technicians list
        $techs = [];
        if ($wo->needs_prod_upper) {
            $techs[] = ['station' => 'Upper', 'name' => $wo->prodUpperBy?->name ?? 'Belum Ditugaskan', 'role_color' => 'amber'];
        }
        if ($wo->needs_prod_sol) {
            $techs[] = ['station' => 'Soling', 'name' => $wo->prodSolBy?->name ?? 'Belum Ditugaskan', 'role_color' => 'blue'];
        }
        if ($wo->needs_prod_jahit) {
            $techs[] = ['station' => 'QC Jahit', 'name' => $wo->qcJahitBy?->name ?? 'Belum Ditugaskan', 'role_color' => 'purple'];
        }

        $spkPayload = [
            'id' => $wo->id,
            'spk_number' => $wo->spk_number,
            'customer_name' => $wo->customer_name,
            'shoe_brand' => $wo->shoe_brand ?? '-',
            'shoe_type' => $wo->shoe_type ?? '-',
            'shoe_size' => $wo->shoe_size ?? '-',
            'has_active_oto' => (bool)$wo->has_active_oto,
            'status_label' => $wo->status?->label() ?? ($wo->status?->value ?? 'DIPROSES'),
            'estimation_date' => $wo->new_estimation_date ? $wo->new_estimation_date->format('d M Y') : ($wo->estimation_date ? $wo->estimation_date->format('d M Y') : '-'),
            'cover_photo_url' => $coverPhotoUrl,
            'technicians' => $techs,
            'detail_url' => url('/order-tracking/detail/' . $wo->id),
        ];

        // Services mapping
        $services = ($wo->workOrderServices && $wo->workOrderServices->isNotEmpty())
            ? $wo->workOrderServices
            : ($wo->services ?? collect());

        foreach ($services as $srv) {
            $serviceName = is_a($srv, \App\Models\WorkOrderService::class)
                ? ($srv->custom_service_name ?: ($srv->service?->name ?: ($srv->category_name ?: 'Layanan Servis')))
                : ($srv->pivot->custom_service_name ?? $srv->name ?? $srv->service_name ?? 'Layanan Servis');

            $serviceBreakdown[$serviceName] = ($serviceBreakdown[$serviceName] ?? 0) + 1;

            if (!isset($servicesModalMap[$serviceName])) {
                $servicesModalMap[$serviceName] = [];
            }
            $servicesModalMap[$serviceName][] = $spkPayload;
        }

        // Materials mapping
        if ($wo->materials && $wo->materials->isNotEmpty()) {
            foreach ($wo->materials as $mat) {
                $matName = $mat->name;
                $qty = (float)($mat->pivot->quantity ?? 1);
                $unit = $mat->unit ?? 'pcs';
                if (!isset($materialBreakdown[$matName])) {
                    $materialBreakdown[$matName] = ['qty' => 0, 'unit' => $unit];
                }
                $materialBreakdown[$matName]['qty'] += $qty;

                if (!isset($materialsModalMap[$matName])) {
                    $materialsModalMap[$matName] = [
                        'unit' => $unit,
                        'items' => []
                    ];
                }
                $matPayload = $spkPayload;
                $matPayload['quantity'] = $qty;
                $matPayload['unit'] = $unit;
                $materialsModalMap[$matName]['items'][] = $matPayload;
            }
        }
    }
    arsort($serviceBreakdown);
    uasort($materialBreakdown, fn($a, $b) => $b['qty'] <=> $a['qty']);

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

<x-workshop-pwa-layout>
    <div x-data="{
        showModal: false,
        selectedWoId: null,
        selectedSpk: '',
        selectedShoe: '',
        station: 'prod_upper',
        technicianId: '',
        availableStations: [],
        allTechnicians: {{ Js::from($technicians) }},
        
        // Breakdown Modal State (UI/UX Pro Max)
        showBreakdownModal: false,
        breakdownModalType: 'jasa',
        breakdownModalTitle: '',
        breakdownModalSubtitle: '',
        breakdownModalItems: [],
        servicesMap: {{ Js::from($servicesModalMap) }},
        materialsMap: {{ Js::from($materialsModalMap) }},
        
        openBreakdownModal(type, name) {
            this.breakdownModalType = type;
            this.breakdownModalTitle = name;
            if (type === 'jasa') {
                const items = this.servicesMap[name] || [];
                this.breakdownModalItems = items;
                this.breakdownModalSubtitle = `${items.length} SPK yang menggunakan layanan ini`;
            } else {
                const matData = this.materialsMap[name] || { unit: 'item', items: [] };
                this.breakdownModalItems = matData.items || [];
                const totalQty = (matData.items || []).reduce((acc, curr) => acc + (parseFloat(curr.quantity) || 1), 0);
                this.breakdownModalSubtitle = `Total ${totalQty} ${matData.unit || 'item'} pada ${matData.items.length} SPK`;
            }
            this.showBreakdownModal = true;
        },

        get filteredTechnicians() {
            if (!this.station) return this.allTechnicians;
            const stationMap = {
                'prod_upper': 'UPPER',
                'prod_sol': 'SOLING',
                'qc_jahit': 'QC',
                'prod_cleaning': 'TREATMENT'
            };
            const targetStation = stationMap[this.station] || '';
            
            let list = this.allTechnicians.filter(t => {
                const spec = (t.specialization || '').toLowerCase();
                const st = (t.station || '').toUpperCase();
                
                if (this.station === 'prod_upper') {
                    return st === 'UPPER' || spec.includes('upper');
                }
                if (this.station === 'prod_sol') {
                    return st === 'SOLING' || spec.includes('sol');
                }
                if (this.station === 'qc_jahit') {
                    return st === 'QC' || spec.includes('jahit') || st === 'SOLING';
                }
                if (this.station === 'prod_cleaning') {
                    return st === 'TREATMENT' || spec.includes('treatment') || spec.includes('clean') || spec.includes('repaint');
                }
                return st === targetStation;
            });

            return list.length > 0 ? list : this.allTechnicians;
        },
        openModal(woId, spk, shoe, stations, defaultTechId = '') {
            this.selectedWoId = woId;
            this.selectedSpk = spk;
            this.selectedShoe = shoe;
            this.availableStations = stations;
            if (stations.length > 0) {
                this.station = stations[0].key;
                this.technicianId = stations[0].current_tech_id ? String(stations[0].current_tech_id) : '';
            } else {
                this.technicianId = defaultTechId ? String(defaultTechId) : '';
            }
            this.showModal = true;
        },
        onStationChange() {
            const found = this.availableStations.find(s => s.key === this.station);
            this.technicianId = found && found.current_tech_id ? String(found.current_tech_id) : '';
        }
    }" class="py-8 bg-slate-50/50 dark:bg-slate-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

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
                        @if($incompleteSpkCount > 0 && $suratJalan->status === 'DIKIRIM' && $suratJalan->jenis_serah_terima === 'produksi_to_post_qc')
                            <form action="{{ route('surat-jalan.auto-complete-technicians', $suratJalan->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-black rounded-xl text-xs uppercase tracking-wider shadow-md shadow-amber-500/20 transition-all active:scale-95 flex items-center gap-2">
                                    <span>⚡ Lengkapi & Tuntaskan Otomatis ({{ $incompleteSpkCount }} SPK)</span>
                                </button>
                            </form>
                        @endif

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

                {{-- BREAKDOWN SUMMARY CHIPS CARD (UI/UX PRO MAX - CLICKABLE WITH MODAL) --}}
                @if(!empty($serviceBreakdown) || !empty($materialBreakdown))
                    <div class="p-4 sm:p-5 rounded-2xl bg-slate-50/80 dark:bg-slate-700/40 border border-slate-200/80 dark:border-slate-700 space-y-3.5">
                        @if(!empty($serviceBreakdown))
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                                <div class="flex items-center gap-1.5 text-xs font-black uppercase tracking-wider text-indigo-700 dark:text-indigo-300 shrink-0 min-w-[130px]">
                                    <span>🔨</span>
                                    <span>Rekap Jasa ({{ count($serviceBreakdown) }}):</span>
                                </div>
                                <div class="flex flex-wrap gap-2 flex-1">
                                    @foreach($serviceBreakdown as $name => $count)
                                        <button type="button"
                                                @click="openBreakdownModal('jasa', '{{ addslashes($name) }}')"
                                                title="Klik untuk melihat SPK dengan jasa {{ $name }}"
                                                class="group inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-indigo-200 dark:border-indigo-800/80 shadow-2xs text-xs font-bold text-slate-800 dark:text-slate-100 hover:border-indigo-500 hover:ring-2 hover:ring-indigo-400/30 hover:scale-105 active:scale-95 transition-all duration-150 cursor-pointer">
                                            <span class="group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $name }}</span>
                                            <span class="px-2 py-0.5 rounded-lg bg-indigo-600 group-hover:bg-indigo-700 text-white font-black text-[10px] transition-colors">
                                                {{ $count }}x
                                            </span>
                                            <span class="text-[10px] text-indigo-400 group-hover:translate-x-0.5 transition-transform">🔍</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if(!empty($materialBreakdown))
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 {{ !empty($serviceBreakdown) ? 'pt-3 border-t border-slate-200/60 dark:border-slate-600/60' : '' }}">
                                <div class="flex items-center gap-1.5 text-xs font-black uppercase tracking-wider text-emerald-700 dark:text-emerald-300 shrink-0 min-w-[130px]">
                                    <span>🧵</span>
                                    <span>Rekap Bahan ({{ count($materialBreakdown) }}):</span>
                                </div>
                                <div class="flex flex-wrap gap-2 flex-1">
                                    @foreach($materialBreakdown as $name => $data)
                                        <button type="button"
                                                @click="openBreakdownModal('material', '{{ addslashes($name) }}')"
                                                title="Klik untuk melihat SPK dengan bahan {{ $name }}"
                                                class="group inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-emerald-200 dark:border-emerald-800/80 shadow-2xs text-xs font-bold text-slate-800 dark:text-slate-100 hover:border-emerald-500 hover:ring-2 hover:ring-emerald-400/30 hover:scale-105 active:scale-95 transition-all duration-150 cursor-pointer">
                                            <span class="group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ $name }}</span>
                                            <span class="px-2 py-0.5 rounded-lg bg-emerald-600 group-hover:bg-emerald-700 text-white font-black text-[10px] transition-colors">
                                                {{ $data['qty'] }} {{ $data['unit'] }}
                                            </span>
                                            <span class="text-[10px] text-emerald-400 group-hover:translate-x-0.5 transition-transform">🔍</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

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

                {{-- MODERN TABLE (BALANCED MIN-WIDTHS) --}}
                <div class="overflow-x-auto rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-2xs">
                    <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300 divide-y divide-slate-100 dark:divide-slate-700/60">
                        <thead class="bg-slate-50 dark:bg-slate-700/50 text-[10px] font-black text-slate-400 uppercase tracking-wider border-b border-slate-200/80 dark:border-slate-700/80">
                            <tr>
                                <th class="px-4 py-4 w-12 text-center">No</th>
                                <th class="px-5 py-4 min-w-[170px]">Nomor SPK & Customer</th>
                                <th class="px-5 py-4 min-w-[150px]">Merk & Tipe Sepatu</th>
                                <th class="px-5 py-4 min-w-[160px]">Rincian Jasa</th>
                                <th class="px-5 py-4 min-w-[270px] bg-indigo-50/50 dark:bg-indigo-950/30 text-indigo-950 dark:text-indigo-200">Stasiun & Teknisi Pelaksana</th>
                                <th class="px-5 py-4 min-w-[210px] bg-emerald-50/40 dark:bg-emerald-950/20 text-emerald-950 dark:text-emerald-200">Bahan Baku / Material</th>
                                <th class="px-5 py-4 min-w-[110px] text-center">Est. Selesai</th>
                                <th class="px-5 py-4 min-w-[120px] text-center">Status Handover</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 bg-white dark:bg-slate-800">
                            @foreach ($suratJalan->items as $index => $item)
                                @php
                                    $wo = $item->workOrder;
                                    $services = ($wo?->workOrderServices && $wo->workOrderServices->isNotEmpty())
                                        ? $wo->workOrderServices
                                        : ($wo?->services ?? collect());
                                    $materials = $wo?->materials ?? collect();
                                    $estDate = $wo?->new_estimation_date ?? $wo?->estimation_date;

                                    // Build station completion info for this SPK
                                    $missingStationList = [];
                                    $hasUpper = $wo?->needs_prod_upper;
                                    $hasSol = $wo?->needs_prod_sol;
                                    $hasJahit = $wo?->needs_prod_jahit;
                                    $hasTreatment = $wo?->needs_prod_treatment;

                                    if ($hasUpper && empty($wo?->prod_upper_completed_at)) {
                                        $missingStationList[] = [
                                            'key' => 'prod_upper', 
                                            'label' => 'Reparasi Upper (Stasiun UPPER)',
                                            'current_tech_id' => $wo?->prod_upper_by
                                        ];
                                    }
                                    if ($hasSol && empty($wo?->prod_sol_completed_at)) {
                                        $missingStationList[] = [
                                            'key' => 'prod_sol', 
                                            'label' => 'Reparasi Sol (Stasiun SOLING)',
                                            'current_tech_id' => $wo?->prod_sol_by
                                        ];
                                    }
                                    if ($hasJahit && empty($wo?->qc_jahit_completed_at)) {
                                        $missingStationList[] = [
                                            'key' => 'qc_jahit', 
                                            'label' => 'QC Jahit (Stasiun QC / Jahit)',
                                            'current_tech_id' => $wo?->qc_jahit_by
                                        ];
                                    }

                                    $isReadyForQc = empty($missingStationList);
                                @endphp
                                <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-700/40 transition-colors {{ !$isReadyForQc && $suratJalan->jenis_serah_terima === 'produksi_to_post_qc' ? 'bg-amber-50/30 dark:bg-amber-950/10' : '' }}">
                                    {{-- Index --}}
                                    <td class="px-4 py-4 text-center font-bold text-slate-400">
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
                                        <span class="text-slate-700 dark:text-slate-300 font-bold block text-[11px]">{{ $wo?->customer_name }}</span>
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
                                                        $serviceName = is_a($srv, \App\Models\WorkOrderService::class)
                                                            ? ($srv->custom_service_name ?: ($srv->service?->name ?: ($srv->category_name ?: 'Layanan Servis')))
                                                            : ($srv->pivot->custom_service_name ?? $srv->name ?? $srv->service_name ?? 'Layanan Servis');
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

                                    {{-- Stasiun & Teknisi Pelaksana (PRO MAX STACKED MINI-CARDS) --}}
                                    <td class="px-5 py-4 bg-indigo-50/20 dark:bg-indigo-950/10">
                                        <div class="space-y-2">
                                            
                                            {{-- 1. Reparasi Upper --}}
                                            @if($hasUpper)
                                                <div class="p-2.5 rounded-xl border transition-all shadow-2xs {{ $wo?->prod_upper_completed_at ? 'bg-emerald-50/80 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800/80' : 'bg-rose-50/80 dark:bg-rose-950/40 border-rose-200 dark:border-rose-800/80' }}">
                                                    <div class="flex items-center justify-between gap-1.5 mb-1">
                                                        <span class="text-[10px] font-black uppercase tracking-wider {{ $wo?->prod_upper_completed_at ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-700 dark:text-rose-300' }} flex items-center gap-1">
                                                            <span>👞</span> Upper
                                                        </span>
                                                        @if($wo?->prod_upper_completed_at)
                                                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-[9px] font-black bg-emerald-200/80 text-emerald-900 dark:bg-emerald-900 dark:text-emerald-200">
                                                                ✓ Selesai
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-[9px] font-black bg-rose-200/80 text-rose-900 dark:bg-rose-900 dark:text-rose-200 animate-pulse">
                                                                ⏳ Belum
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-6 h-6 rounded-lg {{ $wo?->prod_upper_completed_at ? 'bg-emerald-600 text-white' : 'bg-slate-300 text-slate-700 dark:bg-slate-700 dark:text-slate-300' }} font-black text-[10px] flex items-center justify-center shrink-0">
                                                            {{ strtoupper(substr($wo?->prodUpperBy?->name ?? '?', 0, 1)) }}
                                                        </div>
                                                        <div class="min-w-0 flex-1">
                                                            <span class="text-xs font-bold text-slate-900 dark:text-white block truncate leading-tight">
                                                                {{ $wo?->prodUpperBy?->name ?? 'Belum Ditugaskan' }}
                                                            </span>
                                                            <span class="text-[9px] text-slate-500 dark:text-slate-400 font-medium block">
                                                                {{ $wo?->prod_upper_completed_at ? 'Selesai: ' . $wo->prod_upper_completed_at->format('d M H:i') : 'Menunggu Pengerjaan' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- 2. Reparasi Sol --}}
                                            @if($hasSol)
                                                <div class="p-2.5 rounded-xl border transition-all shadow-2xs {{ $wo?->prod_sol_completed_at ? 'bg-emerald-50/80 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800/80' : 'bg-rose-50/80 dark:bg-rose-950/40 border-rose-200 dark:border-rose-800/80' }}">
                                                    <div class="flex items-center justify-between gap-1.5 mb-1">
                                                        <span class="text-[10px] font-black uppercase tracking-wider {{ $wo?->prod_sol_completed_at ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-700 dark:text-rose-300' }} flex items-center gap-1">
                                                            <span>👟</span> Soling
                                                        </span>
                                                        @if($wo?->prod_sol_completed_at)
                                                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-[9px] font-black bg-emerald-200/80 text-emerald-900 dark:bg-emerald-900 dark:text-emerald-200">
                                                                ✓ Selesai
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-[9px] font-black bg-rose-200/80 text-rose-900 dark:bg-rose-900 dark:text-rose-200 animate-pulse">
                                                                ⏳ Belum
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-6 h-6 rounded-lg {{ $wo?->prod_sol_completed_at ? 'bg-emerald-600 text-white' : 'bg-slate-300 text-slate-700 dark:bg-slate-700 dark:text-slate-300' }} font-black text-[10px] flex items-center justify-center shrink-0">
                                                            {{ strtoupper(substr($wo?->prodSolBy?->name ?? '?', 0, 1)) }}
                                                        </div>
                                                        <div class="min-w-0 flex-1">
                                                            <span class="text-xs font-bold text-slate-900 dark:text-white block truncate leading-tight">
                                                                {{ $wo?->prodSolBy?->name ?? 'Belum Ditugaskan' }}
                                                            </span>
                                                            <span class="text-[9px] text-slate-500 dark:text-slate-400 font-medium block">
                                                                {{ $wo?->prod_sol_completed_at ? 'Selesai: ' . $wo->prod_sol_completed_at->format('d M H:i') : 'Menunggu Pengerjaan' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- 3. QC Jahit --}}
                                            @if($hasJahit)
                                                <div class="p-2.5 rounded-xl border transition-all shadow-2xs {{ $wo?->qc_jahit_completed_at ? 'bg-emerald-50/80 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800/80' : 'bg-rose-50/80 dark:bg-rose-950/40 border-rose-200 dark:border-rose-800/80' }}">
                                                    <div class="flex items-center justify-between gap-1.5 mb-1">
                                                        <span class="text-[10px] font-black uppercase tracking-wider {{ $wo?->qc_jahit_completed_at ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-700 dark:text-rose-300' }} flex items-center gap-1">
                                                            <span>🧵</span> QC Jahit
                                                        </span>
                                                        @if($wo?->qc_jahit_completed_at)
                                                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-[9px] font-black bg-emerald-200/80 text-emerald-900 dark:bg-emerald-900 dark:text-emerald-200">
                                                                ✓ Selesai
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-[9px] font-black bg-rose-200/80 text-rose-900 dark:bg-rose-900 dark:text-rose-200 animate-pulse">
                                                                ⏳ Belum
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-6 h-6 rounded-lg {{ $wo?->qc_jahit_completed_at ? 'bg-emerald-600 text-white' : 'bg-slate-300 text-slate-700 dark:bg-slate-700 dark:text-slate-300' }} font-black text-[10px] flex items-center justify-center shrink-0">
                                                            {{ strtoupper(substr($wo?->qcJahitBy?->name ?? '?', 0, 1)) }}
                                                        </div>
                                                        <div class="min-w-0 flex-1">
                                                            <span class="text-xs font-bold text-slate-900 dark:text-white block truncate leading-tight">
                                                                {{ $wo?->qcJahitBy?->name ?? 'Belum Ditugaskan' }}
                                                            </span>
                                                            <span class="text-[9px] text-slate-500 dark:text-slate-400 font-medium block">
                                                                {{ $wo?->qc_jahit_completed_at ? 'Selesai: ' . $wo->qc_jahit_completed_at->format('d M H:i') : 'Menunggu Pengerjaan' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- 4. Treatment (Cleaning/Repaint) --}}
                                            @if($hasTreatment)
                                                <div class="p-2.5 rounded-xl border border-indigo-100 dark:border-indigo-900/60 bg-indigo-50/40 dark:bg-indigo-950/30 transition-all shadow-2xs">
                                                    <div class="flex items-center justify-between gap-1.5 mb-1">
                                                        <span class="text-[10px] font-black uppercase tracking-wider text-indigo-800 dark:text-indigo-300 flex items-center gap-1">
                                                            <span>✨</span> Treatment
                                                        </span>
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                                            Tahap QC
                                                        </span>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-6 h-6 rounded-lg bg-indigo-600 text-white font-black text-[10px] flex items-center justify-center shrink-0">
                                                            {{ strtoupper(substr($wo?->prodCleaningBy?->name ?? 'T', 0, 1)) }}
                                                        </div>
                                                        <div class="min-w-0 flex-1">
                                                            <span class="text-xs font-bold text-slate-900 dark:text-white block truncate leading-tight">
                                                                {{ $wo?->prodCleaningBy?->name ?? 'Dikerjakan di QC' }}
                                                            </span>
                                                            <span class="text-[9px] text-slate-400 font-medium block">
                                                                Inspeksi & Treatment di QC
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- If no stations at all --}}
                                            @if(!$hasUpper && !$hasSol && !$hasJahit && !$hasTreatment)
                                                <div class="p-2 rounded-xl bg-slate-50 dark:bg-slate-700/30 border border-slate-200/60 dark:border-slate-700 text-center">
                                                    <span class="text-[10px] font-bold text-slate-400">Standar Workshop (Tanpa Stasiun Khusus)</span>
                                                </div>
                                            @endif

                                            {{-- Button to open complete technician modal if missing --}}
                                            @if(!empty($missingStationList) && $suratJalan->status === 'DIKIRIM')
                                                <button type="button" 
                                                    @click="openModal('{{ $wo->id }}', '{{ $wo->spk_number }}', '{{ addslashes($wo->shoe_brand . ' ' . $wo->shoe_type) }}', {{ json_encode($missingStationList) }})"
                                                    class="w-full mt-2 px-3 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-black rounded-xl text-[10px] uppercase tracking-wider shadow-sm transition-all active:scale-95 flex items-center justify-center gap-1.5">
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
                                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-black bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-300">
                                                                    <span>✓</span> READY
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-black bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-300">
                                                                    <span>⏳</span> BELUM
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                            <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-400 mt-1.5 block">
                                                Total {{ $materials->count() }} Bahan Baku
                                            </span>
                                        @else
                                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-700/30 border border-dashed border-slate-200 dark:border-slate-600 text-center">
                                                <span class="text-slate-400 dark:text-slate-500 font-semibold text-[10px] block">
                                                    ℹ️ Tidak butuh bahan baku tambahan
                                                </span>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Est. Selesai --}}
                                    <td class="px-5 py-4 text-center">
                                        @if($estDate)
                                            <span class="font-black text-slate-800 dark:text-white block text-xs font-mono">
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
                                                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-[10px] font-black uppercase bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-300 shadow-2xs">
                                                    <span>✓</span> SIAP KE QC
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-[10px] font-black uppercase bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-300 animate-pulse shadow-2xs">
                                                    <span>⏳</span> BELUM LENGKAP
                                                </span>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-[10px] font-black uppercase bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-300 shadow-2xs">
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
                            <div class="flex items-center gap-3 text-xs text-rose-800 dark:text-rose-200 bg-rose-50 dark:bg-rose-950/40 px-4 py-3.5 rounded-2xl border border-rose-200 dark:border-rose-800 w-full sm:w-auto">
                                <span class="text-lg shrink-0">⚠️</span>
                                <div>
                                    <strong class="block font-black">Terdapat {{ $incompleteSpkCount }} SPK yang stasiun produksinya / teknisinya belum selesai.</strong>
                                    <span class="text-rose-700 dark:text-rose-300">Gunakan tombol <strong>"⚡ Lengkapi & Tuntaskan Otomatis"</strong> di atas atau tombol pada tabel sebelum serah terima.</span>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center gap-3 text-xs text-amber-800 dark:text-amber-200 bg-amber-50 dark:bg-amber-950/40 px-4 py-3.5 rounded-2xl border border-amber-200 dark:border-amber-800 w-full sm:w-auto">
                                <span class="text-base shrink-0">⚠️</span>
                                <span>Pastikan kondisi fisik sepatu dan bahan baku telah dihitung & sesuai sebelum mengonfirmasi penerimaan.</span>
                            </div>
                        @endif

                        <form action="{{ route('surat-jalan.receive', $suratJalan->id) }}" method="POST" class="shrink-0 w-full sm:w-auto">
                            @csrf
                            <button type="submit" class="w-full sm:w-auto px-8 py-3.5 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white font-black rounded-2xl text-xs uppercase tracking-widest shadow-lg shadow-emerald-500/20 transition-all active:scale-95 flex items-center justify-center gap-2">
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

        {{-- 4. MODAL LENGKAPI TEKNISI & TUNTASKAN STASIUN (FILTERED BY ROLE TECHNICIAN, STATION & SPECIALIZATION) --}}
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
                            <p class="text-xs text-slate-500 font-medium">Pilih teknisi sesuai spesialisasi stasiun pengerjaan</p>
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
                        <select name="station" x-model="station" @change="onStationChange()" class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-bold focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                            <template x-for="st in availableStations" :key="st.key">
                                <option :value="st.key" x-text="st.label"></option>
                            </template>
                        </select>
                    </div>

                    {{-- Teknisi Selection (Filtered by Role Technician + Station & Specialization) --}}
                    <div>
                        <label class="flex items-center justify-between text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            <span>Pilih Teknisi Pelaksana:</span>
                            <span class="text-[10px] text-indigo-600 dark:text-indigo-400 font-bold" x-text="`${filteredTechnicians.length} Teknisi Tersedia`"></span>
                        </label>
                        <select name="technician_id" x-model="technicianId" required class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-bold focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                            <option value="">-- Pilih Teknisi Stasiun --</option>
                            <template x-for="tech in filteredTechnicians" :key="tech.id">
                                <option :value="tech.id" x-text="`${tech.name} — ${tech.specialization || tech.station || 'Teknisi'} [${tech.station || 'WORKSHOP'}]`"></option>
                            </template>
                        </select>
                        <p class="text-[11px] text-slate-400 mt-1.5 font-medium">Hanya menampilkan teknisi (Role: Technician) yang sesuai dengan stasiun & keahlian spesialisasi terpilih.</p>
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

        {{-- 5. MODAL INTERAKTIF RINCIAN REKAP JASA & BAHAN (UI/UX PRO MAX) --}}
        <div x-show="showBreakdownModal" 
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-slate-950/70 backdrop-blur-md"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div @click.away="showBreakdownModal = false" 
                 class="bg-white dark:bg-slate-900 rounded-3xl max-w-4xl w-full p-6 sm:p-7 shadow-2xl border border-slate-200 dark:border-slate-800 flex flex-col max-h-[90vh] space-y-5 transform transition-all"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                
                {{-- MODAL HEADER --}}
                <div class="flex items-start justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-4 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl font-bold shadow-inner"
                             :class="breakdownModalType === 'jasa' ? 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800' : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800'">
                            <span x-text="breakdownModalType === 'jasa' ? '🔨' : '🧵'"></span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider"
                                      :class="breakdownModalType === 'jasa' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300'"
                                      x-text="breakdownModalType === 'jasa' ? 'RINCIAN LAYANAN JASA' : 'RINCIAN BAHAN BAKU / MATERIAL'"></span>
                            </div>
                            <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white mt-0.5" x-text="breakdownModalTitle"></h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold" x-text="breakdownModalSubtitle"></p>
                        </div>
                    </div>
                    <button @click="showBreakdownModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- MODAL BODY: SPK CARDS GRID (SCROLLABLE) --}}
                <div class="flex-1 overflow-y-auto pr-1 space-y-3 custom-scrollbar">
                    <template x-if="breakdownModalItems.length > 0">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                            <template x-for="(item, idx) in breakdownModalItems" :key="item.id + '-' + idx">
                                <div class="bg-slate-50/80 dark:bg-slate-800/60 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-700/80 hover:border-indigo-400 dark:hover:border-indigo-600 transition-all flex flex-col justify-between gap-3 group">
                                    
                                    <div class="flex items-start gap-3">
                                        {{-- Thumbnail Cover Photo --}}
                                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl overflow-hidden bg-slate-200 dark:bg-slate-700 shrink-0 border border-slate-200 dark:border-slate-600 relative group-hover:scale-102 transition-transform">
                                            <template x-if="item.cover_photo_url">
                                                <img :src="item.cover_photo_url" :alt="item.spk_number" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!item.cover_photo_url">
                                                <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 text-xs font-bold gap-0.5">
                                                    <span>👟</span>
                                                    <span class="text-[9px]">No Photo</span>
                                                </div>
                                            </template>
                                            <template x-if="item.has_active_oto">
                                                <span class="absolute top-1 left-1 px-1 py-0.2 bg-amber-500 text-slate-950 font-black text-[8px] rounded shadow">OTO</span>
                                            </template>
                                        </div>

                                        {{-- SPK Info & Specs --}}
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-1 mb-0.5">
                                                <span class="font-mono font-black text-slate-900 dark:text-white text-xs" x-text="item.spk_number"></span>
                                                <span class="px-2 py-0.5 rounded-md bg-slate-200/80 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-[10px]" x-text="item.status_label"></span>
                                            </div>

                                            <span class="text-xs font-bold text-slate-700 dark:text-slate-200 block truncate" x-text="item.customer_name"></span>
                                            
                                            <div class="flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 font-medium">
                                                <span class="font-semibold text-slate-700 dark:text-slate-300" x-text="item.shoe_brand"></span>
                                                <span>•</span>
                                                <span class="truncate" x-text="item.shoe_type"></span>
                                                <span class="px-1.5 py-0.2 rounded bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[9px] font-bold shrink-0" x-text="'Sz ' + item.shoe_size"></span>
                                            </div>

                                            {{-- If material: quantity pill --}}
                                            <template x-if="breakdownModalType === 'material' && item.quantity">
                                                <div class="mt-1.5">
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-emerald-100 dark:bg-emerald-950/70 text-emerald-800 dark:text-emerald-300 text-[10px] font-black border border-emerald-200 dark:border-emerald-800">
                                                        <span>🧵 Pemakaian:</span>
                                                        <span x-text="item.quantity + ' ' + (item.unit || 'pcs')"></span>
                                                    </span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    {{-- Technicians & Timeline Footer --}}
                                    <div class="pt-2.5 border-t border-slate-200/60 dark:border-slate-700/60 flex items-center justify-between gap-2 text-[10px]">
                                        {{-- Assigned Technicians Mini Pills --}}
                                        <div class="flex flex-wrap gap-1 items-center flex-1">
                                            <template x-if="item.technicians && item.technicians.length > 0">
                                                <div class="flex flex-wrap gap-1">
                                                    <template x-for="tech in item.technicians" :key="tech.station">
                                                        <span class="px-1.5 py-0.5 rounded-md bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 font-bold text-slate-700 dark:text-slate-200"
                                                              :title="tech.station + ': ' + tech.name"
                                                              x-text="tech.station + ': ' + tech.name"></span>
                                                    </template>
                                                </div>
                                            </template>
                                            <template x-if="!item.technicians || item.technicians.length === 0">
                                                <span class="text-slate-400 italic font-medium">Stasiun standar</span>
                                            </template>
                                        </div>

                                        {{-- Link Quick Action --}}
                                        <a :href="item.detail_url" target="_blank" 
                                           class="px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950 hover:bg-indigo-600 hover:text-white dark:hover:bg-indigo-600 text-indigo-600 dark:text-indigo-400 font-bold transition-all shrink-0 flex items-center gap-1 border border-indigo-200 dark:border-indigo-800">
                                            <span>Buka SPK</span>
                                            <span>↗</span>
                                        </a>
                                    </div>

                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="breakdownModalItems.length === 0">
                        <div class="py-12 text-center text-slate-400 space-y-2">
                            <span class="text-4xl block">🔍</span>
                            <p class="font-bold text-sm">Tidak ada data SPK yang ditemukan untuk item ini.</p>
                        </div>
                    </template>
                </div>

                {{-- MODAL FOOTER --}}
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0">
                    <span class="text-xs text-slate-400 font-bold" x-text="`Total ${breakdownModalItems.length} SPK dalam Surat Jalan ini`"></span>
                    <button type="button" @click="showBreakdownModal = false" class="px-5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition">
                        Tutup
                    </button>
                </div>

            </div>
        </div>

    </div>
</x-workshop-pwa-layout>
