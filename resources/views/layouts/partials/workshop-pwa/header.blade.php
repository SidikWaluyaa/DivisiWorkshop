{{-- TOP PWA APP HEADER BAR FOR WORKSHOP --}}
<header class="bg-white/90 backdrop-blur-md border-b border-slate-200/80 text-slate-800 sticky top-0 z-30 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">
        
        {{-- Left: Brand & Mobile Sidebar Toggle --}}
        <div class="flex items-center gap-3">
            {{-- Desktop Sidebar Toggle (≥ 768px) --}}
            <button @click="$dispatch('toggle-sidebar')" 
                    class="hidden md:flex p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 hover:text-[#0F172A] transition-all border border-slate-200"
                    title="Toggle Sidebar Workshop">
                <svg class="w-5 h-5 transition-transform duration-300" :class="{ 'rotate-180': sidebarCollapsed }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                </svg>
            </button>

            {{-- Brand Logo --}}
            <a href="{{ route('workshop.dashboard-v2') }}" class="flex items-center gap-2.5 group">
                <div class="w-9 h-9 rounded-xl bg-white p-1 flex items-center justify-center border border-slate-200 shadow-md shadow-slate-900/10 group-hover:scale-105 transition-transform">
                    <img src="{{ asset('images/logo.png') }}" alt="ShoeWorkshop Logo" class="w-full h-full object-contain">
                </div>
                <div class="hidden xs:block">
                    <div class="font-black text-sm text-[#0F172A] tracking-tight">
                        ShoeWorkshop
                    </div>
                    <p class="text-[9px] font-bold text-slate-400 -mt-0.5">Mobile-First Workshop System</p>
                </div>
            </a>
        </div>

        {{-- Center: Active Station Indicator (Pill Badge) --}}
        <div class="flex items-center gap-2">
            @php
                $currentRoute = request()->route()?->getName();
                $stationLabel = 'Dashboard';

                if (str_contains($currentRoute, 'preparation')) {
                    $stationLabel = 'Persiapan (Cuci)';
                } elseif (str_contains($currentRoute, 'sortir')) {
                    $stationLabel = 'Sortir & Klasifikasi';
                } elseif (str_contains($currentRoute, 'production')) {
                    $stationLabel = 'Produksi (Reparasi)';
                } elseif (str_contains($currentRoute, 'qc')) {
                    $stationLabel = 'Quality Control (QC)';
                } elseif (str_contains($currentRoute, 'manifest')) {
                    $stationLabel = 'Logistik Inbound';
                } elseif (str_contains($currentRoute, 'revision')) {
                    $stationLabel = 'Revisi Teknik';
                } elseif (str_contains($currentRoute, 'garansi')) {
                    $stationLabel = 'Sistem Garansi';
                } elseif (str_contains($currentRoute, 'materials')) {
                    $stationLabel = 'Katalog Material';
                }
            @endphp
            <span class="px-3.5 py-1 rounded-xl text-xs font-black border uppercase tracking-wider flex items-center gap-2 shadow-sm bg-[#22AF85]/10 text-[#22AF85] border-[#22AF85]/30">
                <span class="w-2 h-2 rounded-full bg-[#FFC232] animate-pulse"></span>
                <span class="truncate max-w-[140px] sm:max-w-none">{{ $stationLabel }}</span>
            </span>
        </div>

        {{-- Right: Quick Tracking & User Profile --}}
        <div class="flex items-center gap-2">
            {{-- Quick Tracking Search Icon --}}
            <a href="{{ route('internal-tracking.index') }}" 
               class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200/80 text-[#22AF85] transition-colors relative group border border-slate-200/80" 
               title="Lacak SPK Cepat">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </a>

            {{-- User Profile Pill --}}
            <div class="flex items-center gap-2 pl-2 border-l border-slate-200">
                <div class="w-8 h-8 rounded-xl bg-[#22AF85] flex items-center justify-center text-white font-black text-xs shadow-sm">
                    {{ strtoupper(substr(Auth::user()->name ?? 'WS', 0, 2)) }}
                </div>
                <div class="hidden lg:block text-left">
                    <div class="text-xs font-black text-[#0F172A] leading-tight truncate max-w-[110px]">{{ Auth::user()->name ?? 'Admin WS' }}</div>
                    <div class="text-[9px] font-black text-[#22AF85] uppercase tracking-wider">Workshop</div>
                </div>
            </div>
        </div>

    </div>
</header>
