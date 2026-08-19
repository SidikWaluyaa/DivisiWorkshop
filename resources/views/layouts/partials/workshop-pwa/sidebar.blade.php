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

    $countActiveWorkshop = WorkOrder::whereIn('status', [
        WorkOrderStatus::SORTIR, 
        WorkOrderStatus::PREPARATION, 
        WorkOrderStatus::PRODUCTION, 
        WorkOrderStatus::QC, 
        WorkOrderStatus::REVISI
    ])->count();

    $countFastTrack = WorkOrder::where('fast_track_status', 'yes')
        ->whereNotIn('status', [WorkOrderStatus::SELESAI, WorkOrderStatus::BATAL])
        ->count();

    $countInbound = WorkshopManifest::where('status', 'SENT')->count();

    $countPrep = WorkOrder::where('status', WorkOrderStatus::PREPARATION)->count();
    $countSortir = WorkOrder::where('status', WorkOrderStatus::SORTIR)->count();
    $countProd = WorkOrder::where('status', WorkOrderStatus::PRODUCTION)
        ->whereDoesntHave('logs', function($lq) {
            $lq->where('step', 'PRODUCTION')
               ->where('action', 'PRODUCTION_APPROVED');
        })->count();
    $countQc = WorkOrder::where('status', WorkOrderStatus::QC)->count();

    $countLate = WorkOrder::productionLate()->whereRaw('DATEDIFF(estimation_date, NOW()) <= 0')->count();
    $countSuratJalan = SuratJalan::where('status', 'DIKIRIM')->count();

    $countRevisi = WorkOrderRevision::where('status', 'OPEN')->count();
    $countGaransiActive = WorkOrderWarranty::count();
    $countListGaransi = WorkOrder::whereNotNull('warranty_expires_at')->count();

    $countMaterialTotal = Material::count();
    $countMaterialRequests = Schema::hasTable('material_requests') ? MaterialRequest::where('status', 'PENDING')->count() : 0;
    $countDisbursement = Schema::hasTable('material_disbursements') ? MaterialDisbursement::where('status', 'PENDING')->count() : 0;
    $countMutationsToday = Schema::hasTable('material_mutations') ? MaterialMutation::whereDate('created_at', today())->count() : 0;

    $countOutboundStaging = WorkOrder::where('status', WorkOrderStatus::STAGING_OUTBOUND)->count();
@endphp

<aside class="hidden md:flex flex-col fixed inset-y-0 left-0 z-40 bg-[#22AF85] text-white border-r border-emerald-600/40 transition-all duration-300 shadow-2xl shadow-emerald-950/20 font-sans"
       :class="sidebarCollapsed ? 'w-[72px]' : 'w-64'">
    
    {{-- Workshop Logo Section --}}
    <div class="h-16 flex items-center border-b border-emerald-600/40 bg-emerald-700/30 shrink-0"
         :class="sidebarCollapsed ? 'justify-center px-0' : 'justify-between px-4'">
        <a href="{{ route('workshop.dashboard-v2') }}" class="flex items-center gap-3 overflow-hidden" title="Divisi Workshop PWA">
            <div class="w-10 h-10 rounded-2xl bg-white/95 p-1.5 flex-shrink-0 flex items-center justify-center shadow-md shadow-emerald-950/20 border border-white/20">
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

    {{-- Nav Links List --}}
    <div class="flex-1 overflow-y-auto overflow-x-hidden py-4 sidebar-scroll transition-all"
         :class="sidebarCollapsed ? 'px-2 space-y-3' : 'px-3 space-y-5'"
         x-init="
             let savedScroll = sessionStorage.getItem('sidebar_scroll_ws');
             if (savedScroll !== null) {
                 $el.scrollTop = parseInt(savedScroll);
             } else {
                 let activeEl = $el.querySelector('.bg-\\[\\#FFC232\\]');
                 if (activeEl) {
                     activeEl.scrollIntoView({ block: 'nearest' });
                 }
             }
         "
         @scroll.passive="sessionStorage.setItem('sidebar_scroll_ws', $el.scrollTop)">

        {{-- 1. UTAMA & DASHBOARD --}}
        <div class="space-y-1">
            <a href="{{ route('workshop.dashboard-v2') }}" 
               title="Dashboard Workshop ({{ $countActiveWorkshop }})"
               class="flex items-center transition-all group relative font-extrabold text-xs
               {{ request()->routeIs('workshop.dashboard-v2') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15' }}"
               :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-2xl'">
                <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('workshop.dashboard-v2') ? 'text-slate-950' : 'text-emerald-100 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1">Dashboard Workshop</span>
                @if($countActiveWorkshop > 0)
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-2 py-0.5 px-2 rounded-full text-[10px] font-black {{ request()->routeIs('workshop.dashboard-v2') ? 'bg-slate-950 text-[#FFC232]' : 'bg-white/20 text-white' }}">
                        {{ $countActiveWorkshop }}
                    </span>
                    <span x-show="sidebarCollapsed" x-cloak class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-slate-950 text-[#FFC232] font-black text-[9px] flex items-center justify-center shadow-sm border-2 border-white">
                        {{ $countActiveWorkshop }}
                    </span>
                @endif
            </a>

            <a href="{{ route('workshop.fast-track.index') }}" 
               title="Fast Track SPK ({{ $countFastTrack }})"
               class="flex items-center transition-all group relative font-extrabold text-xs
               {{ request()->routeIs('workshop.fast-track.*') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15' }}"
               :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-2xl'">
                <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('workshop.fast-track.*') ? 'text-slate-950' : 'text-amber-300 group-hover:scale-110' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1">Fast Track SPK</span>
                @if($countFastTrack > 0)
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-2 py-0.5 px-2 rounded-full text-[10px] font-black {{ request()->routeIs('workshop.fast-track.*') ? 'bg-slate-950 text-[#FFC232]' : 'bg-[#FFC232] text-slate-950' }}">
                        {{ $countFastTrack }}
                    </span>
                    <span x-show="sidebarCollapsed" x-cloak class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-slate-950 text-[#FFC232] font-black text-[9px] flex items-center justify-center shadow-sm border-2 border-white animate-bounce">
                        {{ $countFastTrack }}
                    </span>
                @endif
            </a>
        </div>

        {{-- 2. ANTREAN INBOUND --}}
        <div>
            <div x-show="!sidebarCollapsed" x-cloak class="px-3 mb-1.5 text-[10px] font-black uppercase text-emerald-100/90 tracking-wider flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-[#FFC232]"></span>
                <span>Antrean Inbound</span>
            </div>
            <div x-show="sidebarCollapsed" x-cloak class="my-2 border-t border-emerald-600/40"></div>

            <div class="space-y-1">
                <a href="{{ route('manifest.index', ['status' => 'SENT', 'mode' => 'pwa']) }}" 
                   title="Manifest Inbound Masuk ({{ $countInbound }})"
                   class="flex items-center transition-all text-xs font-extrabold group relative
                   {{ request()->routeIs('manifest.*') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
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
                </a>
            </div>
        </div>

        {{-- 3. ALUR PROSES PENGERJAAN --}}
        <div>
            <div x-show="!sidebarCollapsed" x-cloak class="px-3 mb-1.5 text-[10px] font-black uppercase text-emerald-100/90 tracking-wider flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                <span>Alur Proses Pengerjaan</span>
            </div>
            <div x-show="sidebarCollapsed" x-cloak class="my-2 border-t border-emerald-600/40"></div>

            <div class="space-y-1">
                {{-- 1. Prep --}}
                <a href="{{ route('preparation.index') }}" 
                   title="1. Persiapan Cuci ({{ $countPrep }})"
                   class="flex items-center transition-all text-xs font-extrabold group relative
                   {{ request()->routeIs('preparation.*') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
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
                </a>

                {{-- 2. Sortir --}}
                <a href="{{ route('sortir.index') }}" 
                   title="2. Sortir & Klasifikasi ({{ $countSortir }})"
                   class="flex items-center transition-all text-xs font-extrabold group relative
                   {{ request()->routeIs('sortir.*') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
                    <span class="w-5 h-5 rounded-lg {{ request()->routeIs('sortir.*') ? 'bg-slate-950 text-[#FFC232] font-black' : 'bg-white/20 text-white font-black' }} flex items-center justify-center text-[10px] flex-shrink-0">2</span>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1">Sortir &amp; Klasifikasi</span>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-2 py-0.5 px-2 rounded-full text-[10px] font-black {{ request()->routeIs('sortir.*') ? 'bg-slate-950 text-[#FFC232]' : 'bg-white/20 text-white' }}">
                        {{ $countSortir }}
                    </span>
                    @if($countSortir > 0)
                        <span x-show="sidebarCollapsed" x-cloak class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-slate-950 text-[#FFC232] font-black text-[9px] flex items-center justify-center shadow-sm border-2 border-white">
                            {{ $countSortir }}
                        </span>
                    @endif
                </a>

                {{-- 3. Produksi --}}
                <a href="{{ route('production.index') }}" 
                   title="3. Produksi Reparasi ({{ $countProd }})"
                   class="flex items-center transition-all text-xs font-extrabold group relative
                   {{ request()->routeIs('production.index') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
                    <span class="w-5 h-5 rounded-lg {{ request()->routeIs('production.index') ? 'bg-slate-950 text-[#FFC232] font-black' : 'bg-white/20 text-white font-black' }} flex items-center justify-center text-[10px] flex-shrink-0">3</span>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1">Produksi (Reparasi)</span>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-2 py-0.5 px-2 rounded-full text-[10px] font-black {{ request()->routeIs('production.index') ? 'bg-slate-950 text-[#FFC232]' : 'bg-white/20 text-white' }}">
                        {{ $countProd }}
                    </span>
                    @if($countProd > 0)
                        <span x-show="sidebarCollapsed" x-cloak class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-slate-950 text-[#FFC232] font-black text-[9px] flex items-center justify-center shadow-sm border-2 border-white">
                            {{ $countProd }}
                        </span>
                    @endif
                </a>

                {{-- 4. QC --}}
                <a href="{{ route('qc.index') }}" 
                   title="4. Quality Control ({{ $countQc }})"
                   class="flex items-center transition-all text-xs font-extrabold group relative
                   {{ request()->routeIs('qc.index') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
                    <span class="w-5 h-5 rounded-lg {{ request()->routeIs('qc.index') ? 'bg-slate-950 text-[#FFC232] font-black' : 'bg-white/20 text-white font-black' }} flex items-center justify-center text-[10px] flex-shrink-0">4</span>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1">Quality Control (QC)</span>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-2 py-0.5 px-2 rounded-full text-[10px] font-black {{ request()->routeIs('qc.index') ? 'bg-slate-950 text-[#FFC232]' : 'bg-white/20 text-white' }}">
                        {{ $countQc }}
                    </span>
                    @if($countQc > 0)
                        <span x-show="sidebarCollapsed" x-cloak class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-slate-950 text-[#FFC232] font-black text-[9px] flex items-center justify-center shadow-sm border-2 border-white">
                            {{ $countQc }}
                        </span>
                    @endif
                </a>
            </div>
        </div>

        {{-- 4. MANAJEMEN LAYANAN & TEKNISI --}}
        <div>
            <div x-show="!sidebarCollapsed" x-cloak class="px-3 mb-1.5 text-[10px] font-black uppercase text-[#FFC232] tracking-wider flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-[#FFC232]"></span>
                <span>Manajemen Layanan &amp; Teknisi</span>
            </div>
            <div x-show="sidebarCollapsed" x-cloak class="my-2 border-t border-emerald-600/40"></div>

            <div class="space-y-1">
                {{-- Asisten Data Teknisi --}}
                <a href="{{ route('production.technician-assistant') }}" 
                   title="Asisten Data Teknisi"
                   class="flex items-center transition-all text-xs font-extrabold group relative
                   {{ request()->routeIs('production.technician-assistant') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
                    <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('production.technician-assistant') ? 'text-slate-950' : 'text-emerald-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1">Asisten Data Teknisi</span>
                    <span x-show="!sidebarCollapsed" x-cloak class="px-1.5 py-0.5 bg-white/20 text-white font-black text-[9px] rounded-md uppercase tracking-wider">LIVE</span>
                </a>

                {{-- Skill & Jasa Teknisi --}}
                <a href="{{ route('admin.technician-skills') }}" 
                   title="Skill & Jasa Teknisi"
                   class="flex items-center transition-all text-xs font-extrabold group relative
                   {{ request()->routeIs('admin.technician-skills') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
                    <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('admin.technician-skills') ? 'text-slate-950' : 'text-emerald-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1">Skill &amp; Jasa Teknisi</span>
                </a>

                {{-- Manajemen Layanan --}}
                @if(Auth::user()->hasAccess('admin.services'))
                <a href="{{ route('admin.services.index') }}" 
                   title="Manajemen Layanan Master"
                   class="flex items-center transition-all text-xs font-extrabold group relative
                   {{ request()->routeIs('admin.services.*') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
                    <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('admin.services.*') ? 'text-slate-950' : 'text-emerald-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1">Manajemen Layanan</span>
                </a>
                @endif

                {{-- Laporan Performa Teknisi --}}
                @if(Auth::user()->hasAccess('admin.performance'))
                <a href="{{ route('admin.performance.index') }}" 
                   title="Laporan Performa Teknisi"
                   class="flex items-center transition-all text-xs font-extrabold group relative
                   {{ request()->routeIs('admin.performance.*') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
                    <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('admin.performance.*') ? 'text-slate-950' : 'text-emerald-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1">Laporan Performa</span>
                </a>
                @endif
            </div>
        </div>

        {{-- 5. UTILITAS & SURAT JALAN --}}
        <div>
            <div x-show="!sidebarCollapsed" x-cloak class="px-3 mb-1.5 text-[10px] font-black uppercase text-emerald-100/90 tracking-wider flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                <span>Utilitas &amp; Logistik</span>
            </div>
            <div x-show="sidebarCollapsed" x-cloak class="my-2 border-t border-emerald-600/40"></div>

            <div class="space-y-1">
                {{-- Info Keterlambatan --}}
                <a href="{{ route('production.late-info') }}" 
                   title="Info Keterlambatan ({{ $countLate }})"
                   class="flex items-center transition-all text-xs font-extrabold group relative
                   {{ request()->routeIs('production.late-info') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
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
                </a>

                {{-- Surat Jalan --}}
                <a href="{{ route('surat-jalan.index') }}" 
                   title="Surat Jalan Workshop ({{ $countSuratJalan }})"
                   class="flex items-center transition-all text-xs font-extrabold group relative
                   {{ request()->routeIs('surat-jalan.*') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
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
                </a>
            </div>
        </div>

        {{-- 6. REVISI & GARANSI --}}
        <div>
            <div x-show="!sidebarCollapsed" x-cloak class="px-3 mb-1.5 text-[10px] font-black uppercase text-emerald-100/90 tracking-wider flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-[#FFC232]"></span>
                <span>Revisi &amp; Garansi</span>
            </div>
            <div x-show="sidebarCollapsed" x-cloak class="my-2 border-t border-emerald-600/40"></div>

            <div class="space-y-1">
                <a href="{{ route('revision.index') }}" 
                   title="Revisi Teknik ({{ $countRevisi }})"
                   class="flex items-center transition-all text-xs font-extrabold group relative
                   {{ request()->routeIs('revision.*') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
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
                </a>

                <a href="{{ route('garansi.index') }}" 
                   title="Sistem Garansi ({{ $countGaransiActive }})"
                   class="flex items-center transition-all text-xs font-extrabold group relative
                   {{ request()->routeIs('garansi.*') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
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
                </a>

                <a href="{{ route('finish.list-garansi') }}" 
                   title="List Garansi ({{ $countListGaransi }})"
                   class="flex items-center transition-all text-xs font-extrabold group relative
                   {{ request()->routeIs('finish.list-garansi') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
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
                </a>
            </div>
        </div>

        {{-- 7. MANAJEMEN MATERIAL WS --}}
        <div>
            <div x-show="!sidebarCollapsed" x-cloak class="px-3 mb-1.5 text-[10px] font-black uppercase text-emerald-100/90 tracking-wider flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                <span>Material Workshop</span>
            </div>
            <div x-show="sidebarCollapsed" x-cloak class="my-2 border-t border-emerald-600/40"></div>

            <div class="space-y-1">
                @if(Auth::user()->hasAccess('admin.materials'))
                <a href="{{ route('admin.materials.index') }}" 
                   title="Stok & Katalog Material ({{ $countMaterialTotal }})"
                   class="flex items-center transition-all text-xs font-extrabold group relative
                   {{ request()->routeIs('admin.materials.*') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
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
                </a>
                @endif

                @if(Route::has('material-requests.index'))
                <a href="{{ route('material-requests.index') }}" 
                   title="Belanja Material WS ({{ $countMaterialRequests }})"
                   class="flex items-center transition-all text-xs font-extrabold group relative
                   {{ request()->routeIs('material-requests.*') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
                    <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('material-requests.*') ? 'text-slate-950' : 'text-emerald-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 0a2 2 0 100 4 2 2 0 000-4z"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1">Belanja Material WS</span>
                    @if($countMaterialRequests > 0)
                        <span x-show="!sidebarCollapsed" x-cloak class="ml-2 py-0.5 px-2 rounded-full text-[10px] font-black bg-slate-950 text-[#FFC232] shadow-sm">
                            {{ $countMaterialRequests }}
                        </span>
                        <span x-show="sidebarCollapsed" x-cloak class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-slate-950 text-[#FFC232] font-black text-[9px] flex items-center justify-center shadow-sm border-2 border-white">
                            {{ $countMaterialRequests }}
                        </span>
                    @endif
                </a>
                @endif

                <a href="{{ route('storage.disbursement.index') }}" 
                   title="Barang Keluar WS ({{ $countDisbursement }})"
                   class="flex items-center transition-all text-xs font-extrabold group relative
                   {{ request()->routeIs('storage.disbursement.*') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
                    <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('storage.disbursement.*') ? 'text-slate-950' : 'text-emerald-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" x-cloak class="ml-3 flex-1">Barang Keluar WS</span>
                    @if($countDisbursement > 0)
                        <span x-show="!sidebarCollapsed" x-cloak class="ml-2 py-0.5 px-2 rounded-full text-[10px] font-black bg-slate-950 text-[#FFC232] shadow-sm">
                            {{ $countDisbursement }}
                        </span>
                        <span x-show="sidebarCollapsed" x-cloak class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-slate-950 text-[#FFC232] font-black text-[9px] flex items-center justify-center shadow-sm border-2 border-white">
                            {{ $countDisbursement }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('storage.history') }}" 
                   title="Riwayat Mutasi Stok ({{ $countMutationsToday }})"
                   class="flex items-center transition-all text-xs font-extrabold group relative
                   {{ request()->routeIs('storage.history') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
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
                </a>
            </div>
        </div>

        {{-- 8. ANTREAN OUTBOUND --}}
        <div>
            <div x-show="!sidebarCollapsed" x-cloak class="px-3 mb-1.5 text-[10px] font-black uppercase text-emerald-100/90 tracking-wider flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-[#FFC232]"></span>
                <span>Antrean Outbound</span>
            </div>
            <div x-show="sidebarCollapsed" x-cloak class="my-2 border-t border-emerald-600/40"></div>

            <div class="space-y-1">
                <a href="{{ route('qc.outbound') }}" 
                   title="Staging & Manifest Outbound ({{ $countOutboundStaging }})"
                   class="flex items-center transition-all text-xs font-extrabold group relative
                   {{ request()->routeIs('qc.outbound*') ? 'bg-[#FFC232] text-slate-950 shadow-lg shadow-emerald-950/20 font-black' : 'text-white hover:bg-white/15' }}"
                   :class="sidebarCollapsed ? 'w-11 h-11 justify-center rounded-2xl mx-auto' : 'px-3.5 py-2.5 rounded-xl'">
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
                </a>
            </div>
        </div>

    </div>

    {{-- Bottom Collapse Toggle Bar --}}
    <div class="p-3 border-t border-emerald-600/40 bg-emerald-800/40 flex items-center justify-between shrink-0">
        <button @click="sidebarCollapsed = !sidebarCollapsed" 
                class="w-full flex items-center justify-center gap-2 py-2 px-3 rounded-xl bg-white/10 hover:bg-white/20 transition-all text-xs font-black tracking-wider text-white active:scale-95">
            <svg class="w-4 h-4 transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
            <span x-show="!sidebarCollapsed" x-cloak>Sembunyikan Sidebar</span>
        </button>
    </div>
</aside>
