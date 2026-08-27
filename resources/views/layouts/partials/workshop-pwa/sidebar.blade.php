{{-- DEDICATED WORKSHOP SIDEBAR FOR TABLET & DESKTOP (≥ 768px) --}}
@php
    use App\Models\WorkOrder;
    use App\Models\WorkshopManifest;
    use App\Models\WorkOrderRevision;
    use App\Models\WorkOrderWarranty;
    use App\Models\SuratJalan;
    use App\Models\Material;
    use App\Models\MaterialRequest;
    use App\Models\MaterialDisbursement;
    use App\Models\MaterialMutation;
    use App\Enums\WorkOrderStatus;
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Support\Facades\Cache;

    // Cache sidebar counts for 15 seconds for ultra-fast response
    $wsCounts = Cache::remember('ws_sidebar_counts', 15, function() {
        return [
            'activeWorkshop' => WorkOrder::whereIn('status', [
                WorkOrderStatus::SORTIR, 
                WorkOrderStatus::PREPARATION, 
                WorkOrderStatus::PRODUCTION, 
                WorkOrderStatus::QC, 
                WorkOrderStatus::REVISI
            ])->count(),
            'fastTrack' => WorkOrder::where('fast_track_status', 'yes')
                ->whereNotIn('status', [WorkOrderStatus::SELESAI, WorkOrderStatus::BATAL])
                ->count(),
            'inbound' => WorkshopManifest::where('status', 'SENT')->count(),
            'prep' => WorkOrder::where('status', WorkOrderStatus::PREPARATION)->count(),
            // 2. Sortir Active WIP (SPK still being sorted, not yet classification completed)
            'sortir' => WorkOrder::where('status', WorkOrderStatus::SORTIR)
                ->whereDoesntHave('logs', function($lq) {
                    $lq->where('step', 'SORTIR')
                       ->where('action', 'CLASSIFICATION_COMPLETED');
                })->count(),
            // 4. Produksi Active WIP (SPK in production, not yet production approved)
            'prod' => WorkOrder::where('status', WorkOrderStatus::PRODUCTION)
                ->whereDoesntHave('logs', function($lq) {
                    $lq->where('step', 'PRODUCTION')
                       ->where('action', 'PRODUCTION_APPROVED');
                })->count(),
            'qc' => WorkOrder::where('status', WorkOrderStatus::QC)->count(),
            'late' => WorkOrder::productionLate()->whereRaw('DATEDIFF(estimation_date, NOW()) <= 0')->count(),
            'suratJalan' => SuratJalan::where('status', 'DIKIRIM')->count(),
            // 3. SJ Sortir -> Produksi Candidates (Completed Sortir ready for Surat Jalan)
            'suratJalanSortir' => WorkOrder::whereIn('status', [WorkOrderStatus::SORTIR, WorkOrderStatus::PRODUCTION])
                ->whereHas('logs', function($lq) {
                    $lq->where('step', 'SORTIR')
                       ->where('action', 'CLASSIFICATION_COMPLETED');
                })
                ->whereDoesntHave('suratJalanItems.suratJalan', function($q) {
                    $q->where('jenis_serah_terima', 'sortir_to_produksi');
                })->count(),
            // 5. SJ Produksi -> QC Candidates (Completed Production ready for Surat Jalan)
            'suratJalanProd' => WorkOrder::whereIn('status', [WorkOrderStatus::PRODUCTION, WorkOrderStatus::QC])
                ->whereHas('logs', function($lq) {
                    $lq->where('step', 'PRODUCTION')
                       ->where('action', 'PRODUCTION_APPROVED');
                })
                ->whereDoesntHave('suratJalanItems.suratJalan', function($q) {
                    $q->where('jenis_serah_terima', 'produksi_to_post_qc');
                })->count(),
            'revisi' => WorkOrderRevision::where('status', 'OPEN')->count(),
            'garansiActive' => WorkOrderWarranty::count(),
            'listGaransi' => WorkOrder::whereNotNull('warranty_expires_at')->count(),
            'materialTotal' => Material::count(),
            'materialRequests' => Schema::hasTable('material_requests') ? MaterialRequest::where('status', 'PENDING')->count() : 0,
            'disbursement' => Schema::hasTable('material_disbursements') ? MaterialDisbursement::where('status', 'PENDING')->count() : 0,
            'mutationsToday' => Schema::hasTable('material_mutations') ? MaterialMutation::whereDate('created_at', today())->count() : 0,
            'outboundStaging' => WorkOrder::where('status', WorkOrderStatus::STAGING_OUTBOUND)->count(),
        ];
    });

    $countActiveWorkshop = $wsCounts['activeWorkshop'];
    $countFastTrack = $wsCounts['fastTrack'];
    $countInbound = $wsCounts['inbound'];
    $countPrep = $wsCounts['prep'];
    $countSortir = $wsCounts['sortir'];
    $countProd = $wsCounts['prod'];
    $countQc = $wsCounts['qc'];
    $countLate = $wsCounts['late'];
    $countSuratJalan = $wsCounts['suratJalan'];
    $countSuratJalanSortir = $wsCounts['suratJalanSortir'];
    $countSuratJalanProd = $wsCounts['suratJalanProd'];
    $countRevisi = $wsCounts['revisi'];
    $countGaransiActive = $wsCounts['garansiActive'];
    $countListGaransi = $wsCounts['listGaransi'];
    $countMaterialTotal = $wsCounts['materialTotal'];
    $countMaterialRequests = $wsCounts['materialRequests'];
    $countDisbursement = $wsCounts['disbursement'];
    $countMutationsToday = $wsCounts['mutationsToday'];
    $countOutboundStaging = $wsCounts['outboundStaging'];

    $formatNum = function($num) {
        if ($num >= 1000) {
            $formatted = number_format($num / 1000, 1);
            return rtrim(rtrim($formatted, '0'), '.') . 'k';
        }
        return (string)$num;
    };
@endphp

<aside class="hidden md:flex flex-col fixed inset-y-0 left-0 z-40 bg-[#22AF85] text-white border-r border-emerald-600/40 transition-all duration-300 ease-out shadow-2xl shadow-emerald-950/20 font-sans"
       :class="sidebarCollapsed ? 'w-[72px]' : 'w-64'">
    
    {{-- Workshop Logo Section --}}
    <div class="h-16 flex items-center border-b border-emerald-600/40 bg-emerald-800/40 backdrop-blur-md shrink-0 transition-all duration-300"
         :class="sidebarCollapsed ? 'justify-center px-0' : 'justify-between px-4'">
        <a href="{{ route('workshop.dashboard-v2') }}" class="flex items-center gap-3 overflow-hidden group" title="Divisi Workshop PWA">
            <div class="w-10 h-10 rounded-2xl bg-white/95 p-1.5 flex-shrink-0 flex items-center justify-center shadow-md shadow-emerald-950/20 border border-white/20 group-hover:scale-105 transition-transform duration-200">
                <img src="{{ asset('images/logo.png') }}" alt="ShoeWorkshop Logo" class="w-full h-full object-contain">
            </div>
            <div x-show="!sidebarCollapsed" x-cloak class="whitespace-nowrap">
                <div class="font-black text-sm text-white tracking-tight">
                    Divisi Workshop
                </div>
                <p class="text-[10px] font-extrabold text-emerald-100/80 -mt-0.5">Sistem Produksi Terpadu</p>
            </div>
        </a>
    </div>

    {{-- Nav Links List Container --}}
    <div class="flex-1 overflow-y-auto overflow-x-hidden py-4 sidebar-scroll transition-all duration-300"
         :class="sidebarCollapsed ? 'px-2 space-y-3' : 'px-3 space-y-4'"
         x-data="{
             openDashboard: {{ request()->routeIs('dashboard', 'workshop.dashboard-v2', 'workshop.fast-track.*', 'internal-tracking.*') ? 'true' : 'false' }},
             openLayanan: {{ request()->routeIs('production.technician-assistant', 'admin.technicians.index', 'admin.technician-skills', 'admin.services.*', 'admin.performance.*') ? 'true' : 'false' }},
             openUtilitas: {{ request()->routeIs('production.late-info', 'surat-jalan.*') ? 'true' : 'false' }},
             openGaransi: {{ request()->routeIs('revision.*', 'garansi.*', 'finish.list-garansi') ? 'true' : 'false' }},
             openMaterial: {{ request()->routeIs('admin.materials.*', 'material-requests.*', 'storage.disbursement.*', 'storage.history') ? 'true' : 'false' }}
         }"
         x-init="
             let savedScroll = sessionStorage.getItem('sidebar_scroll_ws');
             if (savedScroll !== null) {
                 $el.scrollTop = parseInt(savedScroll);
             }
         "
         @scroll.passive="sessionStorage.setItem('sidebar_scroll_ws', $el.scrollTop)">

        {{-- 1. DASHBOARD & TRACKING (COLLAPSIBLE ACCORDION) --}}
        <div>
            <button @click="openDashboard = !openDashboard" type="button" 
                    class="w-full text-left px-3 py-2 mb-1.5 text-[10px] font-black uppercase tracking-wider flex items-center justify-between group cursor-pointer rounded-xl transition-all duration-200 border"
                    :class="openDashboard ? 'bg-emerald-900/60 border-emerald-400/30 text-white shadow-sm' : 'bg-emerald-800/40 hover:bg-emerald-800/70 border-emerald-500/30 text-emerald-100'"
                    x-show="!sidebarCollapsed" x-cloak>
                <div class="flex items-center gap-2 min-w-0 flex-1">
                    <svg class="w-3.5 h-3.5 text-[#FFC232] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    <span class="whitespace-nowrap font-black truncate">Dashboard &amp; Tracking</span>
                </div>
                <div class="flex items-center gap-1.5 flex-shrink-0 ml-2">
                    @if($countActiveWorkshop + $countFastTrack > 0)
                        <span x-show="!openDashboard" x-cloak class="px-2 py-0.5 min-w-[20px] h-[18px] rounded-full bg-[#FFC232] text-slate-950 font-black text-[9px] flex items-center justify-center shadow-sm">
                            {{ $formatNum($countActiveWorkshop + $countFastTrack) }}
                        </span>
                    @endif
                    <svg class="w-3.5 h-3.5 text-emerald-100 transition-transform duration-300 ease-out" 
                         :class="openDashboard ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </button>
            <div x-show="sidebarCollapsed" x-cloak class="my-2 border-t border-emerald-600/40"></div>

            <div class="space-y-1" x-show="openDashboard || sidebarCollapsed" x-collapse x-cloak>
                {{-- Portal Utama Admin General --}}
                <a href="{{ route('dashboard') }}" 
                   title="Portal Utama Admin General"
                   class="flex items-center transition-all duration-200 ease-out group relative font-extrabold text-xs
                   {{ request()->routeIs('dashboard') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15 hover:translate-x-1' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-2xl'">
                    
                    @if(request()->routeIs('dashboard'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-slate-950 rounded-r-full shadow-sm" x-show="!sidebarCollapsed"></span>
                    @endif

                    <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('dashboard') ? 'text-slate-950' : 'text-amber-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1 flex items-center justify-between">
                        <span>Portal Utama Admin</span>
                        <span class="px-1.5 py-0.5 bg-slate-950 text-[#FFC232] font-black text-[9px] rounded-md uppercase tracking-wider">PORTAL</span>
                    </span>

                    {{-- Compact Hover Tooltip --}}
                    <div x-show="sidebarCollapsed" x-cloak 
                         class="absolute left-16 px-3 py-1.5 bg-slate-900/95 text-white font-black text-xs rounded-xl shadow-2xl backdrop-blur-md border border-slate-700 whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200 z-50">
                        <span>Portal Utama Admin</span>
                    </div>
                </a>

                {{-- Dashboard Utama --}}
            <a href="{{ route('workshop.dashboard-v2') }}" 
               title="Dashboard Utama ({{ $countActiveWorkshop }})"
               class="flex items-center transition-all duration-200 ease-out group relative font-extrabold text-xs
               {{ request()->routeIs('workshop.dashboard-v2') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15 hover:translate-x-1' }}"
               :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-2xl'">
                
                @if(request()->routeIs('workshop.dashboard-v2'))
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-slate-950 rounded-r-full shadow-sm" x-show="!sidebarCollapsed"></span>
                @endif

                <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('workshop.dashboard-v2') ? 'text-slate-950' : 'text-emerald-100 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1">Dashboard Utama</span>
                @if($countActiveWorkshop > 0)
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-2 py-0.5 px-2 rounded-full text-[10px] font-black {{ request()->routeIs('workshop.dashboard-v2') ? 'bg-slate-950 text-[#FFC232]' : 'bg-white/20 text-white' }}">
                        {{ $countActiveWorkshop }}
                    </span>
                    <span x-show="sidebarCollapsed" x-cloak class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-slate-950 text-[#FFC232] font-black text-[9px] flex items-center justify-center shadow-sm border-2 border-white">
                        {{ $countActiveWorkshop }}
                    </span>
                @endif

                {{-- Compact Hover Tooltip --}}
                <div x-show="sidebarCollapsed" x-cloak 
                     class="absolute left-16 px-3 py-1.5 bg-slate-900/95 text-white font-black text-xs rounded-xl shadow-2xl backdrop-blur-md border border-slate-700 whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200 z-50 flex items-center gap-2">
                    <span>Dashboard Utama</span>
                    <span class="px-1.5 py-0.5 rounded-md bg-[#FFC232] text-slate-950 text-[10px] font-black">{{ $countActiveWorkshop }}</span>
                </div>
            </a>

            {{-- Fast Track (Prioritas) --}}
            <a href="{{ route('workshop.fast-track.index') }}" 
               title="Fast Track (Prioritas) ({{ $countFastTrack }})"
               class="flex items-center transition-all duration-200 ease-out group relative font-extrabold text-xs
               {{ request()->routeIs('workshop.fast-track.*') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15 hover:translate-x-1' }}"
               :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-2xl'">
                
                @if(request()->routeIs('workshop.fast-track.*'))
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-slate-950 rounded-r-full shadow-sm" x-show="!sidebarCollapsed"></span>
                @endif

                <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('workshop.fast-track.*') ? 'text-slate-950' : 'text-amber-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1 flex items-center justify-between">
                    <span>Fast Track (Prioritas)</span>
                    <span class="px-1.5 py-0.5 bg-rose-500 text-white font-black text-[9px] rounded-md uppercase tracking-wider animate-pulse">PRIORITY</span>
                </span>
                @if($countFastTrack > 0)
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-2 py-0.5 px-2 rounded-full text-[10px] font-black {{ request()->routeIs('workshop.fast-track.*') ? 'bg-slate-950 text-[#FFC232]' : 'bg-[#FFC232] text-slate-950' }}">
                        {{ $countFastTrack }}
                    </span>
                    <span x-show="sidebarCollapsed" x-cloak class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-slate-950 text-[#FFC232] font-black text-[9px] flex items-center justify-center shadow-sm border-2 border-white">
                        {{ $countFastTrack }}
                    </span>
                @endif

                {{-- Compact Hover Tooltip --}}
                <div x-show="sidebarCollapsed" x-cloak 
                     class="absolute left-16 px-3 py-1.5 bg-slate-900/95 text-white font-black text-xs rounded-xl shadow-2xl backdrop-blur-md border border-slate-700 whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200 z-50 flex items-center gap-2">
                    <span>Fast Track (Prioritas)</span>
                    <span class="px-1.5 py-0.5 rounded-md bg-[#FFC232] text-slate-950 text-[10px] font-black">{{ $countFastTrack }}</span>
                </div>
            </a>

            {{-- Lacak SPK Internal --}}
            <a href="{{ route('internal-tracking.index') }}" 
               title="Lacak SPK Internal"
               class="flex items-center transition-all duration-200 ease-out group relative font-extrabold text-xs
               {{ request()->routeIs('internal-tracking.index') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15 hover:translate-x-1' }}"
               :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-2xl'">
                
                @if(request()->routeIs('internal-tracking.index'))
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-slate-950 rounded-r-full shadow-sm" x-show="!sidebarCollapsed"></span>
                @endif

                <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('internal-tracking.index') ? 'text-slate-950' : 'text-emerald-100 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1">Lacak SPK Internal</span>

                {{-- Compact Hover Tooltip --}}
                <div x-show="sidebarCollapsed" x-cloak 
                     class="absolute left-16 px-3 py-1.5 bg-slate-900/95 text-white font-black text-xs rounded-xl shadow-2xl backdrop-blur-md border border-slate-700 whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200 z-50 flex items-center gap-2">
                    <span>Lacak SPK Internal</span>
                </div>
            </a>

            {{-- Lacak Jasa Workshop --}}
            <a href="{{ route('internal-tracking.services') }}" 
               title="Lacak Jasa Workshop"
               class="flex items-center transition-all duration-200 ease-out group relative font-extrabold text-xs
               {{ request()->routeIs('internal-tracking.services') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15 hover:translate-x-1' }}"
               :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-2xl'">
                
                @if(request()->routeIs('internal-tracking.services'))
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-slate-950 rounded-r-full shadow-sm" x-show="!sidebarCollapsed"></span>
                @endif

                <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('internal-tracking.services') ? 'text-slate-950' : 'text-emerald-100 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1">Lacak Jasa Workshop</span>

                {{-- Compact Hover Tooltip --}}
                <div x-show="sidebarCollapsed" x-cloak 
                     class="absolute left-16 px-3 py-1.5 bg-slate-900/95 text-white font-black text-xs rounded-xl shadow-2xl backdrop-blur-md border border-slate-700 whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200 z-50 flex items-center gap-2">
                    <span>Lacak Jasa Workshop</span>
                </div>
            </a>
        </div>

        {{-- 2. INBOUND WORKSHOP (STATIC) --}}
        <div>
            <div x-show="!sidebarCollapsed" x-cloak class="px-3 py-2 mb-1.5 text-[10px] font-black uppercase tracking-wider flex items-center justify-between bg-emerald-800/40 border border-emerald-500/30 rounded-xl text-emerald-100 shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-[#FFC232] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <span class="font-black">Inbound Workshop</span>
                </div>
            </div>
            <div x-show="sidebarCollapsed" x-cloak class="my-2 border-t border-emerald-600/40"></div>

            <div class="space-y-1">
                <a href="{{ route('manifest.index', ['status' => 'SENT', 'mode' => 'pwa']) }}" 
                   title="Manifest Inbound Masuk ({{ $countInbound }})"
                   class="flex items-center transition-all duration-200 ease-out text-xs font-extrabold group relative
                   {{ request()->routeIs('manifest.*') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15 hover:translate-x-1' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
                    
                    @if(request()->routeIs('manifest.*'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-slate-950 rounded-r-full shadow-sm" x-show="!sidebarCollapsed"></span>
                    @endif

                    <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('manifest.*') ? 'text-slate-950' : 'text-emerald-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1">Manifest Inbound Masuk</span>
                    @if($countInbound > 0)
                        <span x-show="!sidebarCollapsed" x-cloak class="ml-2 py-0.5 px-2 rounded-full text-[10px] font-black bg-slate-950 text-[#FFC232] shadow-sm animate-bounce">
                            {{ $countInbound }}
                        </span>
                        <span x-show="sidebarCollapsed" x-cloak class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-slate-950 text-[#FFC232] font-black text-[9px] flex items-center justify-center shadow-sm border-2 border-white animate-bounce">
                            {{ $countInbound }}
                        </span>
                    @else
                        <span x-show="!sidebarCollapsed" x-cloak class="ml-2 py-0.5 px-2 rounded-full text-[10px] font-bold text-emerald-200 bg-white/10">0</span>
                    @endif

                    {{-- Compact Hover Tooltip --}}
                    <div x-show="sidebarCollapsed" x-cloak 
                         class="absolute left-16 px-3 py-1.5 bg-slate-900/95 text-white font-black text-xs rounded-xl shadow-2xl backdrop-blur-md border border-slate-700 whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200 z-50 flex items-center gap-2">
                        <span>Manifest Inbound Masuk</span>
                        <span class="px-1.5 py-0.5 rounded-md bg-[#FFC232] text-slate-950 text-[10px] font-black">{{ $countInbound }}</span>
                    </div>
                </a>
            </div>
        </div>

        {{-- 3. STASIUN PENGERJAAN (STATIC 1 TO 6) --}}
        <div>
            <div x-show="!sidebarCollapsed" x-cloak class="px-3 py-2 mb-1.5 text-[10px] font-black uppercase tracking-wider flex items-center justify-between bg-emerald-800/40 border border-emerald-500/30 rounded-xl text-emerald-100 shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="font-black">Stasiun Pengerjaan</span>
                </div>
            </div>
            <div x-show="sidebarCollapsed" x-cloak class="my-2 border-t border-emerald-600/40"></div>

            <div class="space-y-1">
                {{-- 1. Prep --}}
                <a href="{{ route('preparation.index') }}" 
                   title="1. Persiapan Cuci ({{ $countPrep }})"
                   class="flex items-center transition-all duration-200 ease-out text-xs font-extrabold group relative
                   {{ request()->routeIs('preparation.*') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15 hover:translate-x-1' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
                    
                    @if(request()->routeIs('preparation.*'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-slate-950 rounded-r-full shadow-sm" x-show="!sidebarCollapsed"></span>
                    @endif

                    <span class="w-5 h-5 rounded-lg {{ request()->routeIs('preparation.*') ? 'bg-slate-950 text-[#FFC232] font-black' : 'bg-white/20 text-white font-black' }} flex items-center justify-center text-[10px] flex-shrink-0">1</span>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1">Persiapan (Cuci)</span>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-2 py-0.5 px-2 rounded-full text-[10px] font-black {{ request()->routeIs('preparation.*') ? 'bg-slate-950 text-[#FFC232]' : 'bg-white/20 text-white' }}">
                        {{ $countPrep }}
                    </span>
                    @if($countPrep > 0)
                        <span x-show="sidebarCollapsed" x-cloak class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-slate-950 text-[#FFC232] font-black text-[9px] flex items-center justify-center shadow-sm border-2 border-white">
                            {{ $countPrep }}
                        </span>
                    @endif

                    {{-- Compact Hover Tooltip --}}
                    <div x-show="sidebarCollapsed" x-cloak 
                         class="absolute left-16 px-3 py-1.5 bg-slate-900/95 text-white font-black text-xs rounded-xl shadow-2xl backdrop-blur-md border border-slate-700 whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200 z-50 flex items-center gap-2">
                        <span>1. Persiapan (Cuci)</span>
                        <span class="px-1.5 py-0.5 rounded-md bg-[#FFC232] text-slate-950 text-[10px] font-black">{{ $countPrep }}</span>
                    </div>
                </a>

                {{-- 2. Sortir --}}
                <a href="{{ route('sortir.index') }}" 
                   title="2. Sortir & Penilaian ({{ $countSortir }})"
                   class="flex items-center transition-all duration-200 ease-out text-xs font-extrabold group relative
                   {{ request()->routeIs('sortir.*') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15 hover:translate-x-1' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
                    
                    @if(request()->routeIs('sortir.*'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-slate-950 rounded-r-full shadow-sm" x-show="!sidebarCollapsed"></span>
                    @endif

                    <span class="w-5 h-5 rounded-lg {{ request()->routeIs('sortir.*') ? 'bg-slate-950 text-[#FFC232] font-black' : 'bg-white/20 text-white font-black' }} flex items-center justify-center text-[10px] flex-shrink-0">2</span>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1">Sortir &amp; Penilaian</span>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-2 py-0.5 px-2 rounded-full text-[10px] font-black {{ request()->routeIs('sortir.*') ? 'bg-slate-950 text-[#FFC232]' : 'bg-white/20 text-white' }}">
                        {{ $countSortir }}
                    </span>
                    @if($countSortir > 0)
                        <span x-show="sidebarCollapsed" x-cloak class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-slate-950 text-[#FFC232] font-black text-[9px] flex items-center justify-center shadow-sm border-2 border-white">
                            {{ $countSortir }}
                        </span>
                    @endif

                    {{-- Compact Hover Tooltip --}}
                    <div x-show="sidebarCollapsed" x-cloak 
                         class="absolute left-16 px-3 py-1.5 bg-slate-900/95 text-white font-black text-xs rounded-xl shadow-2xl backdrop-blur-md border border-slate-700 whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200 z-50 flex items-center gap-2">
                        <span>2. Sortir &amp; Penilaian</span>
                        <span class="px-1.5 py-0.5 rounded-md bg-[#FFC232] text-slate-950 text-[10px] font-black">{{ $countSortir }}</span>
                    </div>
                </a>

                {{-- 3. Surat Jalan Sortir ke Production --}}
                <a href="{{ route('surat-jalan.index', ['jenis' => 'sortir_to_produksi']) }}" 
                   title="3. SJ Sortir ke Produksi ({{ $countSuratJalanSortir }})"
                   class="flex items-center transition-all duration-200 ease-out text-xs font-extrabold group relative
                   {{ request()->fullUrlIs('*surat-jalan*jenis=sortir_to_produksi*') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-emerald-100 hover:bg-white/15 hover:translate-x-1' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
                    
                    @if(request()->fullUrlIs('*surat-jalan*jenis=sortir_to_produksi*'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-slate-950 rounded-r-full shadow-sm" x-show="!sidebarCollapsed"></span>
                    @endif

                    <span class="w-5 h-5 rounded-lg {{ request()->fullUrlIs('*surat-jalan*jenis=sortir_to_produksi*') ? 'bg-slate-950 text-[#FFC232] font-black' : 'bg-white/20 text-white font-black' }} flex items-center justify-center text-[10px] flex-shrink-0">3</span>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1 text-[11px] leading-tight">SJ Sortir ➔ Produksi</span>
                    @if($countSuratJalanSortir > 0)
                        <span x-show="!sidebarCollapsed" x-cloak class="ml-2 py-0.5 px-2 rounded-full text-[10px] font-black bg-slate-950 text-[#FFC232] shadow-sm animate-pulse">
                            {{ $countSuratJalanSortir }}
                        </span>
                        <span x-show="sidebarCollapsed" x-cloak class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-slate-950 text-[#FFC232] font-black text-[9px] flex items-center justify-center shadow-sm border-2 border-white animate-pulse">
                            {{ $countSuratJalanSortir }}
                        </span>
                    @endif

                    {{-- Compact Hover Tooltip --}}
                    <div x-show="sidebarCollapsed" x-cloak 
                         class="absolute left-16 px-3 py-1.5 bg-slate-900/95 text-white font-black text-xs rounded-xl shadow-2xl backdrop-blur-md border border-slate-700 whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200 z-50 flex items-center gap-2">
                        <span>3. SJ Sortir ➔ Produksi</span>
                        <span class="px-1.5 py-0.5 rounded-md bg-[#FFC232] text-slate-950 text-[10px] font-black">{{ $countSuratJalanSortir }}</span>
                    </div>
                </a>

                {{-- 4. Produksi --}}
                <a href="{{ route('production.index') }}" 
                   title="4. Produksi Reparasi ({{ $countProd }})"
                   class="flex items-center transition-all duration-200 ease-out text-xs font-extrabold group relative
                   {{ request()->routeIs('production.index') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15 hover:translate-x-1' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
                    
                    @if(request()->routeIs('production.index'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-slate-950 rounded-r-full shadow-sm" x-show="!sidebarCollapsed"></span>
                    @endif

                    <span class="w-5 h-5 rounded-lg {{ request()->routeIs('production.index') ? 'bg-slate-950 text-[#FFC232] font-black' : 'bg-white/20 text-white font-black' }} flex items-center justify-center text-[10px] flex-shrink-0">4</span>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1">Produksi (Reparasi)</span>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-2 py-0.5 px-2 rounded-full text-[10px] font-black {{ request()->routeIs('production.index') ? 'bg-slate-950 text-[#FFC232]' : 'bg-white/20 text-white' }}">
                        {{ $countProd }}
                    </span>
                    @if($countProd > 0)
                        <span x-show="sidebarCollapsed" x-cloak class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-slate-950 text-[#FFC232] font-black text-[9px] flex items-center justify-center shadow-sm border-2 border-white">
                            {{ $countProd }}
                        </span>
                    @endif

                    {{-- Compact Hover Tooltip --}}
                    <div x-show="sidebarCollapsed" x-cloak 
                         class="absolute left-16 px-3 py-1.5 bg-slate-900/95 text-white font-black text-xs rounded-xl shadow-2xl backdrop-blur-md border border-slate-700 whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200 z-50 flex items-center gap-2">
                        <span>4. Produksi (Reparasi)</span>
                        <span class="px-1.5 py-0.5 rounded-md bg-[#FFC232] text-slate-950 text-[10px] font-black">{{ $countProd }}</span>
                    </div>
                </a>

                {{-- 5. Surat Jalan Production ke QC --}}
                <a href="{{ route('surat-jalan.index', ['jenis' => 'produksi_to_post_qc']) }}" 
                   title="5. SJ Produksi ke QC ({{ $countSuratJalanProd }})"
                   class="flex items-center transition-all duration-200 ease-out text-xs font-extrabold group relative
                   {{ request()->fullUrlIs('*surat-jalan*jenis=produksi_to_post_qc*') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-emerald-100 hover:bg-white/15 hover:translate-x-1' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
                    
                    @if(request()->fullUrlIs('*surat-jalan*jenis=produksi_to_post_qc*'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-slate-950 rounded-r-full shadow-sm" x-show="!sidebarCollapsed"></span>
                    @endif

                    <span class="w-5 h-5 rounded-lg {{ request()->fullUrlIs('*surat-jalan*jenis=produksi_to_post_qc*') ? 'bg-slate-950 text-[#FFC232] font-black' : 'bg-white/20 text-white font-black' }} flex items-center justify-center text-[10px] flex-shrink-0">5</span>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1 text-[11px] leading-tight">SJ Produksi ➔ QC</span>
                    @if($countSuratJalanProd > 0)
                        <span x-show="!sidebarCollapsed" x-cloak class="ml-2 py-0.5 px-2 rounded-full text-[10px] font-black bg-slate-950 text-[#FFC232] shadow-sm animate-pulse">
                            {{ $countSuratJalanProd }}
                        </span>
                        <span x-show="sidebarCollapsed" x-cloak class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-slate-950 text-[#FFC232] font-black text-[9px] flex items-center justify-center shadow-sm border-2 border-white animate-pulse">
                            {{ $countSuratJalanProd }}
                        </span>
                    @endif

                    {{-- Compact Hover Tooltip --}}
                    <div x-show="sidebarCollapsed" x-cloak 
                         class="absolute left-16 px-3 py-1.5 bg-slate-900/95 text-white font-black text-xs rounded-xl shadow-2xl backdrop-blur-md border border-slate-700 whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200 z-50 flex items-center gap-2">
                        <span>5. SJ Produksi ➔ QC</span>
                        <span class="px-1.5 py-0.5 rounded-md bg-[#FFC232] text-slate-950 text-[10px] font-black">{{ $countSuratJalanProd }}</span>
                    </div>
                </a>

                {{-- 6. QC --}}
                <a href="{{ route('qc.index') }}" 
                   title="6. Quality Control ({{ $countQc }})"
                   class="flex items-center transition-all duration-200 ease-out text-xs font-extrabold group relative
                   {{ request()->routeIs('qc.index') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15 hover:translate-x-1' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
                    
                    @if(request()->routeIs('qc.index'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-slate-950 rounded-r-full shadow-sm" x-show="!sidebarCollapsed"></span>
                    @endif

                    <span class="w-5 h-5 rounded-lg {{ request()->routeIs('qc.index') ? 'bg-slate-950 text-[#FFC232] font-black' : 'bg-white/20 text-white font-black' }} flex items-center justify-center text-[10px] flex-shrink-0">6</span>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1">Quality Control (QC)</span>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-2 py-0.5 px-2 rounded-full text-[10px] font-black {{ request()->routeIs('qc.index') ? 'bg-slate-950 text-[#FFC232]' : 'bg-white/20 text-white' }}">
                        {{ $countQc }}
                    </span>
                    @if($countQc > 0)
                        <span x-show="sidebarCollapsed" x-cloak class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-slate-950 text-[#FFC232] font-black text-[9px] flex items-center justify-center shadow-sm border-2 border-white">
                            {{ $countQc }}
                        </span>
                    @endif

                    {{-- Compact Hover Tooltip --}}
                    <div x-show="sidebarCollapsed" x-cloak 
                         class="absolute left-16 px-3 py-1.5 bg-slate-900/95 text-white font-black text-xs rounded-xl shadow-2xl backdrop-blur-md border border-slate-700 whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200 z-50 flex items-center gap-2">
                        <span>6. Quality Control (QC)</span>
                        <span class="px-1.5 py-0.5 rounded-md bg-[#FFC232] text-slate-950 text-[10px] font-black">{{ $countQc }}</span>
                    </div>
                </a>
            </div>
        </div>

        {{-- 4. OUTBOUND WORKSHOP (STATIC) --}}
        <div>
            <div x-show="!sidebarCollapsed" x-cloak class="px-3 py-2 mb-1.5 text-[10px] font-black uppercase tracking-wider flex items-center justify-between bg-emerald-800/40 border border-emerald-500/30 rounded-xl text-emerald-100 shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-[#FFC232] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v1a2 2 0 01-2 2M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                    <span class="font-black">Outbound Workshop</span>
                </div>
            </div>
            <div x-show="sidebarCollapsed" x-cloak class="my-2 border-t border-emerald-600/40"></div>

            <div class="space-y-1">
                <a href="{{ route('qc.outbound') }}" 
                   title="Staging & Manifest Outbound ({{ $countOutboundStaging }})"
                   class="flex items-center transition-all duration-200 ease-out text-xs font-extrabold group relative
                   {{ request()->routeIs('qc.outbound*') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15 hover:translate-x-1' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
                    
                    @if(request()->routeIs('qc.outbound*'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-slate-950 rounded-r-full shadow-sm" x-show="!sidebarCollapsed"></span>
                    @endif

                    <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('qc.outbound*') ? 'text-slate-950' : 'text-emerald-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8m-5 5h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293h3.172a1 1 0 00.707-.293l2.414-2.414a1 1 0 01.707-.293H20"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1">Staging &amp; Manifest Outbound</span>
                    @if($countOutboundStaging > 0)
                        <span x-show="!sidebarCollapsed" x-cloak class="ml-2 py-0.5 px-2 rounded-full text-[10px] font-black bg-slate-950 text-[#FFC232] shadow-sm animate-pulse">
                            {{ $countOutboundStaging }}
                        </span>
                        <span x-show="sidebarCollapsed" x-cloak class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-slate-950 text-[#FFC232] font-black text-[9px] flex items-center justify-center shadow-sm border-2 border-white animate-pulse">
                            {{ $countOutboundStaging }}
                        </span>
                    @endif

                    {{-- Compact Hover Tooltip --}}
                    <div x-show="sidebarCollapsed" x-cloak 
                         class="absolute left-16 px-3 py-1.5 bg-slate-900/95 text-white font-black text-xs rounded-xl shadow-2xl backdrop-blur-md border border-slate-700 whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200 z-50 flex items-center gap-2">
                        <span>Staging &amp; Outbound</span>
                        <span class="px-1.5 py-0.5 rounded-md bg-[#FFC232] text-slate-950 text-[10px] font-black">{{ $countOutboundStaging }}</span>
                    </div>
                </a>
            </div>
        </div>

        {{-- 5. LAYANAN & TEKNISI (COLLAPSIBLE ACCORDION - SINGLE LINE) --}}
        <div>
            <button @click="openLayanan = !openLayanan" type="button" 
                    class="w-full text-left px-3 py-2 mb-1.5 text-[10px] font-black uppercase tracking-wider flex items-center justify-between group cursor-pointer rounded-xl transition-all duration-200 border"
                    :class="openLayanan ? 'bg-emerald-900/60 border-emerald-400/30 text-white shadow-sm' : 'bg-emerald-800/40 hover:bg-emerald-800/70 border-emerald-500/30 text-emerald-100'"
                    x-show="!sidebarCollapsed" x-cloak>
                <div class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-[#FFC232] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="whitespace-nowrap font-black">Layanan &amp; Teknisi</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-emerald-100 transition-transform duration-300 ease-out" 
                         :class="openLayanan ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </button>
            <div x-show="sidebarCollapsed" x-cloak class="my-2 border-t border-emerald-600/40"></div>

            <div class="space-y-1" x-show="openLayanan || sidebarCollapsed" x-collapse x-cloak>
                {{-- Asisten Data Teknisi --}}
                <a href="{{ route('production.technician-assistant') }}" 
                   title="Asisten Data Teknisi"
                   class="flex items-center transition-all duration-200 ease-out text-xs font-extrabold group relative
                   {{ request()->routeIs('production.technician-assistant') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15 hover:translate-x-1' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
                    
                    @if(request()->routeIs('production.technician-assistant'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-slate-950 rounded-r-full shadow-sm" x-show="!sidebarCollapsed"></span>
                    @endif

                    <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('production.technician-assistant') ? 'text-slate-950' : 'text-emerald-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1">Asisten Data Teknisi</span>
                    <span x-show="!sidebarCollapsed" x-cloak class="px-1.5 py-0.5 bg-white/20 text-white font-black text-[9px] rounded-md uppercase tracking-wider">LIVE</span>

                    {{-- Compact Hover Tooltip --}}
                    <div x-show="sidebarCollapsed" x-cloak 
                         class="absolute left-16 px-3 py-1.5 bg-slate-900/95 text-white font-black text-xs rounded-xl shadow-2xl backdrop-blur-md border border-slate-700 whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200 z-50 flex items-center gap-2">
                        <span>Asisten Data Teknisi</span>
                    </div>
                </a>

                {{-- Manajemen Data Teknisi --}}
                <a href="{{ route('admin.technicians.index') }}" 
                   title="Manajemen Data Teknisi"
                   class="flex items-center transition-all duration-200 ease-out text-xs font-extrabold group relative
                   {{ request()->routeIs('admin.technicians.index') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15 hover:translate-x-1' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
                    
                    @if(request()->routeIs('admin.technicians.index'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-slate-950 rounded-r-full shadow-sm" x-show="!sidebarCollapsed"></span>
                    @endif

                    <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('admin.technicians.index') ? 'text-slate-950' : 'text-emerald-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1">Manajemen Data Teknisi</span>

                    {{-- Compact Hover Tooltip --}}
                    <div x-show="sidebarCollapsed" x-cloak 
                         class="absolute left-16 px-3 py-1.5 bg-slate-900/95 text-white font-black text-xs rounded-xl shadow-2xl backdrop-blur-md border border-slate-700 whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200 z-50 flex items-center gap-2">
                        <span>Manajemen Data Teknisi</span>
                    </div>
                </a>

                {{-- Skill & Tarif Jasa --}}
                <a href="{{ route('admin.technician-skills') }}" 
                   title="Skill & Tarif Jasa"
                   class="flex items-center transition-all duration-200 ease-out text-xs font-extrabold group relative
                   {{ request()->routeIs('admin.technician-skills') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15 hover:translate-x-1' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
                    
                    @if(request()->routeIs('admin.technician-skills'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-slate-950 rounded-r-full shadow-sm" x-show="!sidebarCollapsed"></span>
                    @endif

                    <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('admin.technician-skills') ? 'text-slate-950' : 'text-emerald-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1">Skill &amp; Tarif Jasa</span>

                    {{-- Compact Hover Tooltip --}}
                    <div x-show="sidebarCollapsed" x-cloak 
                         class="absolute left-16 px-3 py-1.5 bg-slate-900/95 text-white font-black text-xs rounded-xl shadow-2xl backdrop-blur-md border border-slate-700 whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200 z-50 flex items-center gap-2">
                        <span>Skill &amp; Tarif Jasa</span>
                    </div>
                </a>

                {{-- Katalog Layanan Master --}}
                @if(Auth::user()->hasAccess('admin.services'))
                <a href="{{ route('admin.services.index') }}" 
                   title="Katalog Layanan Master"
                   class="flex items-center transition-all duration-200 ease-out text-xs font-extrabold group relative
                   {{ request()->routeIs('admin.services.*') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15 hover:translate-x-1' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
                    
                    @if(request()->routeIs('admin.services.*'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-slate-950 rounded-r-full shadow-sm" x-show="!sidebarCollapsed"></span>
                    @endif

                    <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('admin.services.*') ? 'text-slate-950' : 'text-emerald-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1">Katalog Layanan Master</span>

                    {{-- Compact Hover Tooltip --}}
                    <div x-show="sidebarCollapsed" x-cloak 
                         class="absolute left-16 px-3 py-1.5 bg-slate-900/95 text-white font-black text-xs rounded-xl shadow-2xl backdrop-blur-md border border-slate-700 whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200 z-50 flex items-center gap-2">
                        <span>Katalog Layanan Master</span>
                    </div>
                </a>
                @endif

                {{-- Laporan Performa Teknisi --}}
                @if(Auth::user()->hasAccess('admin.performance'))
                <a href="{{ route('admin.performance.index') }}" 
                   title="Laporan Performa Teknisi"
                   class="flex items-center transition-all duration-200 ease-out text-xs font-extrabold group relative
                   {{ request()->routeIs('admin.performance.*') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15 hover:translate-x-1' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
                    
                    @if(request()->routeIs('admin.performance.*'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-slate-950 rounded-r-full shadow-sm" x-show="!sidebarCollapsed"></span>
                    @endif

                    <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('admin.performance.*') ? 'text-slate-950' : 'text-emerald-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1">Laporan Performa</span>

                    {{-- Compact Hover Tooltip --}}
                    <div x-show="sidebarCollapsed" x-cloak 
                         class="absolute left-16 px-3 py-1.5 bg-slate-900/95 text-white font-black text-xs rounded-xl shadow-2xl backdrop-blur-md border border-slate-700 whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200 z-50 flex items-center gap-2">
                        <span>Laporan Performa</span>
                    </div>
                </a>
                @endif
            </div>
        </div>

        {{-- 6. LOGISTIK & SURAT JALAN (COLLAPSIBLE ACCORDION) --}}
        <div>
            <button @click="openUtilitas = !openUtilitas" type="button" 
                    class="w-full text-left px-3 py-2 mb-1.5 text-[10px] font-black uppercase tracking-wider flex items-center justify-between group cursor-pointer rounded-xl transition-all duration-200 border"
                    :class="openUtilitas ? 'bg-emerald-900/60 border-emerald-400/30 text-white shadow-sm' : 'bg-emerald-800/40 hover:bg-emerald-800/70 border-emerald-500/30 text-emerald-100'"
                    x-show="!sidebarCollapsed" x-cloak>
                <div class="flex items-center gap-2 min-w-0 flex-1">
                    <svg class="w-3.5 h-3.5 text-emerald-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="whitespace-nowrap font-black truncate">Logistik &amp; Surat Jalan</span>
                </div>
                <div class="flex items-center gap-1.5 flex-shrink-0 ml-2">
                    @if($countLate + $countSuratJalan > 0)
                        <span x-show="!openUtilitas" x-cloak class="px-2 py-0.5 min-w-[20px] h-[18px] rounded-full bg-[#FFC232] text-slate-950 font-black text-[9px] flex items-center justify-center shadow-sm">
                            {{ $formatNum($countLate + $countSuratJalan) }}
                        </span>
                    @endif
                    <svg class="w-3.5 h-3.5 text-emerald-100 transition-transform duration-300 ease-out" 
                         :class="openUtilitas ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </button>
            <div x-show="sidebarCollapsed" x-cloak class="my-2 border-t border-emerald-600/40"></div>

            <div class="space-y-1" x-show="openUtilitas || sidebarCollapsed" x-collapse x-cloak>
                {{-- Info Keterlambatan --}}
                <a href="{{ route('production.late-info') }}" 
                   title="Info Keterlambatan ({{ $countLate }})"
                   class="flex items-center transition-all duration-200 ease-out text-xs font-extrabold group relative
                   {{ request()->routeIs('production.late-info') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15 hover:translate-x-1' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
                    
                    @if(request()->routeIs('production.late-info'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-slate-950 rounded-r-full shadow-sm" x-show="!sidebarCollapsed"></span>
                    @endif

                    <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('production.late-info') ? 'text-slate-950' : 'text-emerald-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1">Info Keterlambatan</span>
                    @if($countLate > 0)
                        <span x-show="!sidebarCollapsed" x-cloak class="ml-2 py-0.5 px-2 rounded-full text-[10px] font-black bg-slate-950 text-[#FFC232] shadow-sm">
                            {{ $countLate }}
                        </span>
                        <span x-show="sidebarCollapsed" x-cloak class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-slate-950 text-[#FFC232] font-black text-[9px] flex items-center justify-center shadow-sm border-2 border-white">
                            {{ $countLate }}
                        </span>
                    @else
                        <span x-show="!sidebarCollapsed" x-cloak class="ml-2 py-0.5 px-2 rounded-full text-[10px] font-bold text-emerald-200 bg-white/10">0</span>
                    @endif

                    {{-- Compact Hover Tooltip --}}
                    <div x-show="sidebarCollapsed" x-cloak 
                         class="absolute left-16 px-3 py-1.5 bg-slate-900/95 text-white font-black text-xs rounded-xl shadow-2xl backdrop-blur-md border border-slate-700 whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200 z-50 flex items-center gap-2">
                        <span>Info Keterlambatan</span>
                        <span class="px-1.5 py-0.5 rounded-md bg-[#FFC232] text-slate-950 text-[10px] font-black">{{ $countLate }}</span>
                    </div>
                </a>

                {{-- Surat Jalan --}}
                <a href="{{ route('surat-jalan.index') }}" 
                   title="Surat Jalan Workshop ({{ $countSuratJalan }})"
                   class="flex items-center transition-all duration-200 ease-out text-xs font-extrabold group relative
                   {{ request()->routeIs('surat-jalan.*') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15 hover:translate-x-1' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
                    
                    @if(request()->routeIs('surat-jalan.*'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-slate-950 rounded-r-full shadow-sm" x-show="!sidebarCollapsed"></span>
                    @endif

                    <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('surat-jalan.*') ? 'text-slate-950' : 'text-emerald-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1">Surat Jalan Workshop</span>
                    @if($countSuratJalan > 0)
                        <span x-show="!sidebarCollapsed" x-cloak class="ml-2 py-0.5 px-2 rounded-full text-[10px] font-black bg-slate-950 text-[#FFC232] shadow-sm">
                            {{ $countSuratJalan }}
                        </span>
                        <span x-show="sidebarCollapsed" x-cloak class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-slate-950 text-[#FFC232] font-black text-[9px] flex items-center justify-center shadow-sm border-2 border-white">
                            {{ $countSuratJalan }}
                        </span>
                    @endif

                    {{-- Compact Hover Tooltip --}}
                    <div x-show="sidebarCollapsed" x-cloak 
                         class="absolute left-16 px-3 py-1.5 bg-slate-900/95 text-white font-black text-xs rounded-xl shadow-2xl backdrop-blur-md border border-slate-700 whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200 z-50 flex items-center gap-2">
                        <span>Surat Jalan Workshop</span>
                        <span class="px-1.5 py-0.5 rounded-md bg-[#FFC232] text-slate-950 text-[10px] font-black">{{ $countSuratJalan }}</span>
                    </div>
                </a>
            </div>
        </div>

        {{-- 7. REVISI & GARANSI (COLLAPSIBLE ACCORDION) --}}
        <div>
            <button @click="openGaransi = !openGaransi" type="button" 
                    class="w-full text-left px-3 py-2 mb-1.5 text-[10px] font-black uppercase tracking-wider flex items-center justify-between group cursor-pointer rounded-xl transition-all duration-200 border"
                    :class="openGaransi ? 'bg-emerald-900/60 border-emerald-400/30 text-white shadow-sm' : 'bg-emerald-800/40 hover:bg-emerald-800/70 border-emerald-500/30 text-emerald-100'"
                    x-show="!sidebarCollapsed" x-cloak>
                <div class="flex items-center gap-2 min-w-0 flex-1">
                    <svg class="w-3.5 h-3.5 text-[#FFC232] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04M12 21.48V11.5"/>
                    </svg>
                    <span class="whitespace-nowrap font-black truncate">Revisi &amp; Garansi</span>
                </div>
                <div class="flex items-center gap-1.5 flex-shrink-0 ml-2">
                    @if($countRevisi + $countGaransiActive > 0)
                        <span x-show="!openGaransi" x-cloak class="px-2 py-0.5 min-w-[20px] h-[18px] rounded-full bg-[#FFC232] text-slate-950 font-black text-[9px] flex items-center justify-center shadow-sm">
                            {{ $formatNum($countRevisi + $countGaransiActive) }}
                        </span>
                    @endif
                    <svg class="w-3.5 h-3.5 text-emerald-100 transition-transform duration-300 ease-out" 
                         :class="openGaransi ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </button>
            <div x-show="sidebarCollapsed" x-cloak class="my-2 border-t border-emerald-600/40"></div>

            <div class="space-y-1" x-show="openGaransi || sidebarCollapsed" x-collapse x-cloak>
                <a href="{{ route('revision.index') }}" 
                   title="Revisi Teknik ({{ $countRevisi }})"
                   class="flex items-center transition-all duration-200 ease-out text-xs font-extrabold group relative
                   {{ request()->routeIs('revision.*') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15 hover:translate-x-1' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
                    
                    @if(request()->routeIs('revision.*'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-slate-950 rounded-r-full shadow-sm" x-show="!sidebarCollapsed"></span>
                    @endif

                    <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('revision.*') ? 'text-slate-950' : 'text-emerald-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1">Revisi Teknik</span>
                    @if($countRevisi > 0)
                        <span x-show="!sidebarCollapsed" x-cloak class="ml-2 py-0.5 px-2 rounded-full text-[10px] font-black bg-slate-950 text-[#FFC232] shadow-sm">
                            {{ $countRevisi }}
                        </span>
                        <span x-show="sidebarCollapsed" x-cloak class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-slate-950 text-[#FFC232] font-black text-[9px] flex items-center justify-center shadow-sm border-2 border-white">
                            {{ $countRevisi }}
                        </span>
                    @else
                        <span x-show="!sidebarCollapsed" x-cloak class="ml-2 py-0.5 px-2 rounded-full text-[10px] font-bold text-emerald-200 bg-white/10">0</span>
                    @endif

                    {{-- Compact Hover Tooltip --}}
                    <div x-show="sidebarCollapsed" x-cloak 
                         class="absolute left-16 px-3 py-1.5 bg-slate-900/95 text-white font-black text-xs rounded-xl shadow-2xl backdrop-blur-md border border-slate-700 whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200 z-50 flex items-center gap-2">
                        <span>Revisi Teknik</span>
                        <span class="px-1.5 py-0.5 rounded-md bg-[#FFC232] text-slate-950 text-[10px] font-black">{{ $countRevisi }}</span>
                    </div>
                </a>

                <a href="{{ route('garansi.index') }}" 
                   title="Sistem Garansi ({{ $countGaransiActive }})"
                   class="flex items-center transition-all duration-200 ease-out text-xs font-extrabold group relative
                   {{ request()->routeIs('garansi.*') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15 hover:translate-x-1' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
                    
                    @if(request()->routeIs('garansi.*'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-slate-950 rounded-r-full shadow-sm" x-show="!sidebarCollapsed"></span>
                    @endif

                    <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('garansi.*') ? 'text-slate-950' : 'text-emerald-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04M12 21.48V11.5"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1">Sistem Garansi</span>
                    @if($countGaransiActive > 0)
                        <span x-show="!sidebarCollapsed" x-cloak class="ml-2 py-0.5 px-2 rounded-full text-[10px] font-black {{ request()->routeIs('garansi.*') ? 'bg-slate-950 text-[#FFC232]' : 'bg-white/20 text-white' }}">
                            {{ $countGaransiActive }}
                        </span>
                        <span x-show="sidebarCollapsed" x-cloak class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-slate-950 text-[#FFC232] font-black text-[9px] flex items-center justify-center shadow-sm border-2 border-white">
                            {{ $countGaransiActive }}
                        </span>
                    @endif

                    {{-- Compact Hover Tooltip --}}
                    <div x-show="sidebarCollapsed" x-cloak 
                         class="absolute left-16 px-3 py-1.5 bg-slate-900/95 text-white font-black text-xs rounded-xl shadow-2xl backdrop-blur-md border border-slate-700 whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200 z-50 flex items-center gap-2">
                        <span>Sistem Garansi</span>
                        <span class="px-1.5 py-0.5 rounded-md bg-[#FFC232] text-slate-950 text-[10px] font-black">{{ $countGaransiActive }}</span>
                    </div>
                </a>

                <a href="{{ route('finish.list-garansi') }}" 
                   title="List Garansi ({{ $countListGaransi }})"
                   class="flex items-center transition-all duration-200 ease-out text-xs font-extrabold group relative
                   {{ request()->routeIs('finish.list-garansi') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15 hover:translate-x-1' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
                    
                    @if(request()->routeIs('finish.list-garansi'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-slate-950 rounded-r-full shadow-sm" x-show="!sidebarCollapsed"></span>
                    @endif

                    <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('finish.list-garansi') ? 'text-slate-950' : 'text-emerald-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1">List Garansi</span>
                    @if($countListGaransi > 0)
                        <span x-show="!sidebarCollapsed" x-cloak class="ml-2 py-0.5 px-2 rounded-full text-[10px] font-black {{ request()->routeIs('finish.list-garansi') ? 'bg-slate-950 text-[#FFC232]' : 'bg-white/20 text-white' }}">
                            {{ $countListGaransi }}
                        </span>
                        <span x-show="sidebarCollapsed" x-cloak class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-slate-950 text-[#FFC232] font-black text-[9px] flex items-center justify-center shadow-sm border-2 border-white">
                            {{ $countListGaransi }}
                        </span>
                    @endif

                    {{-- Compact Hover Tooltip --}}
                    <div x-show="sidebarCollapsed" x-cloak 
                         class="absolute left-16 px-3 py-1.5 bg-slate-900/95 text-white font-black text-xs rounded-xl shadow-2xl backdrop-blur-md border border-slate-700 whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200 z-50 flex items-center gap-2">
                        <span>List Garansi</span>
                        <span class="px-1.5 py-0.5 rounded-md bg-[#FFC232] text-slate-950 text-[10px] font-black">{{ $countListGaransi }}</span>
                    </div>
                </a>
            </div>
        </div>

        {{-- 8. STOK & MATERIAL (COLLAPSIBLE ACCORDION - SINGLE LINE) --}}
        <div>
            <button @click="openMaterial = !openMaterial" type="button" 
                    class="w-full text-left px-3 py-2 mb-1.5 text-[10px] font-black uppercase tracking-wider flex items-center justify-between group cursor-pointer rounded-xl transition-all duration-200 border"
                    :class="openMaterial ? 'bg-emerald-900/60 border-emerald-400/30 text-white shadow-sm' : 'bg-emerald-800/40 hover:bg-emerald-800/70 border-emerald-500/30 text-emerald-100'"
                    x-show="!sidebarCollapsed" x-cloak>
                <div class="flex items-center gap-2 min-w-0 flex-1">
                    <svg class="w-3.5 h-3.5 text-emerald-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span class="whitespace-nowrap font-black truncate">Stok &amp; Material</span>
                </div>
                <div class="flex items-center gap-1.5 flex-shrink-0 ml-2">
                    @if($countMaterialRequests + $countDisbursement > 0)
                        <span x-show="!openMaterial" x-cloak class="px-2 py-0.5 min-w-[20px] h-[18px] rounded-full bg-[#FFC232] text-slate-950 font-black text-[9px] flex items-center justify-center shadow-sm">
                            {{ $formatNum($countMaterialRequests + $countDisbursement) }}
                        </span>
                    @endif
                    <svg class="w-3.5 h-3.5 text-emerald-100 transition-transform duration-300 ease-out" 
                         :class="openMaterial ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </button>
            <div x-show="sidebarCollapsed" x-cloak class="my-2 border-t border-emerald-600/40"></div>

            <div class="space-y-1" x-show="openMaterial || sidebarCollapsed" x-collapse x-cloak>
                @if(Auth::user()->hasAccess('admin.materials'))
                <a href="{{ route('admin.materials.index') }}" 
                   title="Stok & Katalog Material ({{ $countMaterialTotal }})"
                   class="flex items-center transition-all duration-200 ease-out text-xs font-extrabold group relative
                   {{ request()->routeIs('admin.materials.*') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15 hover:translate-x-1' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
                    
                    @if(request()->routeIs('admin.materials.*'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-slate-950 rounded-r-full shadow-sm" x-show="!sidebarCollapsed"></span>
                    @endif

                    <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('admin.materials.*') ? 'text-slate-950' : 'text-emerald-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1">Stok &amp; Katalog Material</span>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-2 py-0.5 px-2 rounded-full text-[10px] font-black {{ request()->routeIs('admin.materials.*') ? 'bg-slate-950 text-[#FFC232]' : 'bg-white/20 text-white' }}">
                        {{ $countMaterialTotal }}
                    </span>
                    @if($countMaterialTotal > 0)
                        <span x-show="sidebarCollapsed" x-cloak class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-slate-950 text-[#FFC232] font-black text-[9px] flex items-center justify-center shadow-sm border-2 border-white">
                            {{ $countMaterialTotal }}
                        </span>
                    @endif

                    {{-- Compact Hover Tooltip --}}
                    <div x-show="sidebarCollapsed" x-cloak 
                         class="absolute left-16 px-3 py-1.5 bg-slate-900/95 text-white font-black text-xs rounded-xl shadow-2xl backdrop-blur-md border border-slate-700 whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200 z-50 flex items-center gap-2">
                        <span>Stok &amp; Katalog Material</span>
                        <span class="px-1.5 py-0.5 rounded-md bg-[#FFC232] text-slate-950 text-[10px] font-black">{{ $countMaterialTotal }}</span>
                    </div>
                </a>
                @endif

                @if(Route::has('material-requests.index'))
                <a href="{{ route('material-requests.index') }}" 
                   title="Permintaan Material ({{ $countMaterialRequests }})"
                   class="flex items-center transition-all duration-200 ease-out text-xs font-extrabold group relative
                   {{ request()->routeIs('material-requests.*') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15 hover:translate-x-1' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
                    
                    @if(request()->routeIs('material-requests.*'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-slate-950 rounded-r-full shadow-sm" x-show="!sidebarCollapsed"></span>
                    @endif

                    <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('material-requests.*') ? 'text-slate-950' : 'text-emerald-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 0a2 2 0 100 4 2 2 0 000-4z"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1">Permintaan Material</span>
                    @if($countMaterialRequests > 0)
                        <span x-show="!sidebarCollapsed" x-cloak class="ml-2 py-0.5 px-2 rounded-full text-[10px] font-black bg-slate-950 text-[#FFC232] shadow-sm">
                            {{ $countMaterialRequests }}
                        </span>
                        <span x-show="sidebarCollapsed" x-cloak class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-slate-950 text-[#FFC232] font-black text-[9px] flex items-center justify-center shadow-sm border-2 border-white">
                            {{ $countMaterialRequests }}
                        </span>
                    @endif

                    {{-- Compact Hover Tooltip --}}
                    <div x-show="sidebarCollapsed" x-cloak 
                         class="absolute left-16 px-3 py-1.5 bg-slate-900/95 text-white font-black text-xs rounded-xl shadow-2xl backdrop-blur-md border border-slate-700 whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200 z-50 flex items-center gap-2">
                        <span>Permintaan Material</span>
                        <span class="px-1.5 py-0.5 rounded-md bg-[#FFC232] text-slate-950 text-[10px] font-black">{{ $countMaterialRequests }}</span>
                    </div>
                </a>
                @endif

                <a href="{{ route('storage.disbursement.index') }}" 
                   title="Pengeluaran Material ({{ $countDisbursement }})"
                   class="flex items-center transition-all duration-200 ease-out text-xs font-extrabold group relative
                   {{ request()->routeIs('storage.disbursement.*') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15 hover:translate-x-1' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
                    
                    @if(request()->routeIs('storage.disbursement.*'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-slate-950 rounded-r-full shadow-sm" x-show="!sidebarCollapsed"></span>
                    @endif

                    <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('storage.disbursement.*') ? 'text-slate-950' : 'text-emerald-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1">Pengeluaran Material</span>
                    @if($countDisbursement > 0)
                        <span x-show="!sidebarCollapsed" x-cloak class="ml-2 py-0.5 px-2 rounded-full text-[10px] font-black bg-slate-950 text-[#FFC232] shadow-sm">
                            {{ $countDisbursement }}
                        </span>
                        <span x-show="sidebarCollapsed" x-cloak class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-slate-950 text-[#FFC232] font-black text-[9px] flex items-center justify-center shadow-sm border-2 border-white">
                            {{ $countDisbursement }}
                        </span>
                    @endif

                    {{-- Compact Hover Tooltip --}}
                    <div x-show="sidebarCollapsed" x-cloak 
                         class="absolute left-16 px-3 py-1.5 bg-slate-900/95 text-white font-black text-xs rounded-xl shadow-2xl backdrop-blur-md border border-slate-700 whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200 z-50 flex items-center gap-2">
                        <span>Pengeluaran Material</span>
                        <span class="px-1.5 py-0.5 rounded-md bg-[#FFC232] text-slate-950 text-[10px] font-black">{{ $countDisbursement }}</span>
                    </div>
                </a>

                <a href="{{ route('storage.history') }}" 
                   title="Riwayat Mutasi Stok ({{ $countMutationsToday }})"
                   class="flex items-center transition-all duration-200 ease-out text-xs font-extrabold group relative
                   {{ request()->routeIs('storage.history') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15 hover:translate-x-1' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
                    
                    @if(request()->routeIs('storage.history'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-slate-950 rounded-r-full shadow-sm" x-show="!sidebarCollapsed"></span>
                    @endif

                    <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('storage.history') ? 'text-slate-950' : 'text-emerald-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1">Riwayat Mutasi Stok</span>
                    @if($countMutationsToday > 0)
                        <span x-show="!sidebarCollapsed" x-cloak class="ml-2 py-0.5 px-2 rounded-full text-[10px] font-black {{ request()->routeIs('storage.history') ? 'bg-slate-950 text-[#FFC232]' : 'bg-white/20 text-white' }}">
                            {{ $countMutationsToday }}
                        </span>
                        <span x-show="sidebarCollapsed" x-cloak class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-slate-950 text-[#FFC232] font-black text-[9px] flex items-center justify-center shadow-sm border-2 border-white">
                            {{ $countMutationsToday }}
                        </span>
                    @endif

                    {{-- Compact Hover Tooltip --}}
                    <div x-show="sidebarCollapsed" x-cloak 
                         class="absolute left-16 px-3 py-1.5 bg-slate-900/95 text-white font-black text-xs rounded-xl shadow-2xl backdrop-blur-md border border-slate-700 whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200 z-50 flex items-center gap-2">
                        <span>Riwayat Mutasi Stok</span>
                        <span class="px-1.5 py-0.5 rounded-md bg-[#FFC232] text-slate-950 text-[10px] font-black">{{ $countMutationsToday }}</span>
                    </div>
                </a>
            </div>
        </div>

    </div>

    {{-- Bottom Collapse Toggle Bar --}}
    <div class="p-3 border-t border-emerald-600/40 bg-emerald-800/40 backdrop-blur-md flex flex-col gap-2 shrink-0">
        <a href="{{ route('dashboard') }}" 
           class="w-full flex items-center justify-center gap-2 py-2 px-3 rounded-xl bg-[#FFC232] hover:bg-amber-300 transition-all duration-200 text-xs font-black tracking-wider text-slate-950 active:scale-95 shadow-md shadow-emerald-950/30 group relative"
           title="Kembali ke Portal Utama Admin">
            <svg class="w-4 h-4 flex-shrink-0 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            <span x-show="!sidebarCollapsed" x-cloak>Portal Utama Admin</span>

            {{-- Compact Hover Tooltip --}}
            <div x-show="sidebarCollapsed" x-cloak 
                 class="absolute left-16 px-3 py-1.5 bg-slate-900/95 text-white font-black text-xs rounded-xl shadow-2xl backdrop-blur-md border border-slate-700 whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200 z-50">
                <span>Portal Utama Admin</span>
            </div>
        </a>

        <button @click="sidebarCollapsed = !sidebarCollapsed" 
                class="w-full flex items-center justify-center gap-2 py-2 px-3 rounded-xl bg-white/10 hover:bg-white/20 transition-all duration-200 text-xs font-black tracking-wider text-white active:scale-95 group relative">
            <svg class="w-4 h-4 transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
            <span x-show="!sidebarCollapsed" x-cloak>Sembunyikan Sidebar</span>

            {{-- Compact Hover Tooltip --}}
            <div x-show="sidebarCollapsed" x-cloak 
                 class="absolute left-16 px-3 py-1.5 bg-slate-900/95 text-white font-black text-xs rounded-xl shadow-2xl backdrop-blur-md border border-slate-700 whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200 z-50">
                <span x-text="sidebarCollapsed ? 'Perluas Sidebar' : 'Sembunyikan Sidebar'"></span>
            </div>
        </button>
    </div>
</aside>
