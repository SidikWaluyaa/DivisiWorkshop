{{-- MOBILE BOTTOM NAVIGATION BAR FOR WORKSHOP PWA (< 768px) --}}
<div class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-xl border-t border-slate-200/90 text-slate-800 shadow-xl px-2 py-1.5 pb-safe">
    <div class="grid grid-cols-5 gap-1 items-center justify-items-center">

        {{-- 1. Dashboard --}}
        <a href="{{ route('workshop.dashboard-v2') }}" 
           class="flex flex-col items-center justify-center w-full py-1.5 rounded-2xl transition-all touch-manipulation min-h-[48px]
           {{ request()->routeIs('workshop.dashboard-v2') ? 'bg-[#22AF85] text-white shadow-md shadow-[#22AF85]/30 scale-105 font-black' : 'text-slate-500 hover:text-[#22AF85] hover:bg-slate-100' }}">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="text-[10px] font-bold tracking-tight">Dashboard</span>
        </a>

        {{-- 2. Inbound --}}
        @php
            $inboundCount = \App\Models\WorkshopManifest::where('status', 'SENT')->where('manifest_number', 'not like', 'MNF-OUT-%')->count();
            $isInboundActive = request()->routeIs('manifest.*');
        @endphp
        <a href="{{ route('manifest.index', ['status' => 'SENT', 'mode' => 'pwa']) }}" 
           class="flex flex-col items-center justify-center w-full py-1.5 rounded-2xl transition-all touch-manipulation relative min-h-[48px]
           {{ $isInboundActive ? 'bg-[#FFC232] text-slate-950 font-black shadow-md shadow-[#FFC232]/40 scale-105' : 'text-slate-500 hover:text-[#22AF85] hover:bg-slate-100' }}">
            <div class="relative">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                @if($inboundCount > 0)
                    <span class="absolute -top-1.5 -right-2 px-1.5 py-0.2 rounded-full text-[9px] font-black bg-[#FFC232] text-slate-950 border border-white shadow-sm animate-bounce">
                        {{ $inboundCount }}
                    </span>
                @endif
            </div>
            <span class="text-[10px] font-bold tracking-tight">Inbound</span>
        </a>

        {{-- 3. Proses Pengerjaan --}}
        @php
            $isProsesActive = request()->routeIs('preparation.*') || request()->routeIs('sortir.*') || request()->routeIs('production.*') || request()->routeIs('qc.index') || request()->routeIs('surat-jalan.*');
        @endphp
        <button @click="activeDrawer = (activeDrawer === 'proses') ? null : 'proses'" 
                type="button"
                class="flex flex-col items-center justify-center w-full py-1.5 rounded-2xl transition-all touch-manipulation min-h-[48px]
                {{ $isProsesActive ? 'bg-[#22AF85] text-white font-black shadow-md shadow-[#22AF85]/30 scale-105' : 'text-slate-500 hover:text-[#22AF85] hover:bg-slate-100' }}">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <span class="text-[10px] font-bold tracking-tight">Proses</span>
        </button>

        {{-- 4. Material WS --}}
        @php
            $isMaterialActive = request()->routeIs('admin.materials.*') || request()->routeIs('material-requests.*') || request()->routeIs('storage.disbursement.*') || request()->routeIs('storage.history');
        @endphp
        <button @click="activeDrawer = (activeDrawer === 'material') ? null : 'material'" 
                type="button"
                class="flex flex-col items-center justify-center w-full py-1.5 rounded-2xl transition-all touch-manipulation min-h-[48px]
                {{ $isMaterialActive ? 'bg-[#22AF85] text-white font-black shadow-md shadow-[#22AF85]/30 scale-105' : 'text-slate-500 hover:text-[#22AF85] hover:bg-slate-100' }}">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <span class="text-[10px] font-bold tracking-tight">Material</span>
        </button>

        {{-- 5. Outbound / Drawer --}}
        @php
            $outboundStagingCount = \App\Models\WorkOrder::where('status', \App\Enums\WorkOrderStatus::STAGING_OUTBOUND)->count();
            $isOutboundActive = request()->routeIs('qc.outbound*');
        @endphp
        <button @click="activeDrawer = (activeDrawer === 'outbound') ? null : 'outbound'" 
                type="button"
                class="flex flex-col items-center justify-center w-full py-1.5 rounded-2xl transition-all touch-manipulation relative min-h-[48px]
                {{ $isOutboundActive ? 'bg-[#22AF85] text-white font-black shadow-md shadow-[#22AF85]/30 scale-105' : 'text-slate-500 hover:text-[#22AF85] hover:bg-slate-100' }}">
            <div class="relative">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8m-5 5h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293h3.172a1 1 0 00.707-.293l2.414-2.414a1 1 0 01.707-.293H20"/>
                </svg>
                @if($outboundStagingCount > 0)
                    <span class="absolute -top-1.5 -right-2 px-1.5 py-0.2 rounded-full text-[9px] font-black bg-[#FFC232] text-slate-950 border border-white shadow-sm animate-pulse">
                        {{ $outboundStagingCount }}
                    </span>
                @endif
            </div>
            <span class="text-[10px] font-bold tracking-tight">Outbound</span>
        </button>

    </div>
</div>
