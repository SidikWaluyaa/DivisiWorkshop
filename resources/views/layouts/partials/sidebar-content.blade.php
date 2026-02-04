{{-- Toggle Button - Desktop Only --}}
<button @click="collapsed = !collapsed" 
        class="absolute top-4 right-4 z-50 p-2 rounded-lg bg-teal-600 hover:bg-teal-700 transition-colors shadow-lg hidden lg:block"
        title="Toggle Sidebar">
    <svg class="w-5 h-5 text-white transition-transform duration-300" 
         :class="{ 'rotate-180': collapsed }"
         fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
    </svg>
</button>

{{-- Logo Section --}}
<div x-show="!collapsed" class="sidebar-logo-container flex items-center justify-center">
    <a href="{{ route('dashboard') }}" class="transition-transform hover:scale-105">
        <x-application-logo class="block h-20 w-auto" />
    </a>
</div>

{{-- Collapsed Logo Icon --}}
<div x-show="collapsed" x-cloak class="flex items-center justify-center py-6 mt-12">
    <a href="{{ route('dashboard') }}" class="transition-transform hover:scale-110">
        <div class="w-10 h-10 bg-gradient-to-br from-teal-400 to-orange-400 rounded-lg flex items-center justify-center shadow-lg">
            <span class="text-white font-bold text-xl">S</span>
        </div>
    </a>
</div>

{{-- Navigation Section --}}
<div class="flex-1 px-2 overflow-y-auto sidebar-scroll pb-4 min-h-0" style="max-height: calc(100vh - 180px);">
    
    {{-- Dashboard - Visible for ALL roles including HR --}}
    <div class="space-y-1">
        <a href="{{ route('dashboard') }}" 
           class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3">Dashboard</span>
            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Dashboard</span>
        </a>
    </div>
    
    {{-- Operational Navigation (Hidden for HR) --}}
    @if(Auth::user()->role !== 'hr')
    <div x-show="!collapsed" class="section-divider my-4"></div>
    <div x-show="collapsed" class="my-4 border-t border-white/20"></div>

    
    {{-- 1. DIVISI CUSTOMER SERVICE --}}
    @if(Auth::user()->hasAccess('cs') || Auth::user()->hasAccess('cs.spk') || Auth::user()->hasAccess('cs.greeting'))
    <div class="mt-2 space-y-1">
        <h3 x-show="!collapsed" class="section-title px-3 mb-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Divisi Customer Service</h3>
        
        @if(Auth::user()->hasAccess('cs'))
        <a href="{{ route('cs.dashboard') }}" 
           class="nav-item {{ request()->routeIs('cs.dashboard') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3 flex-1">CS Dashboard</span>
            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Dashboard</span>
        </a>

        <a href="{{ route('cs.analytics') }}" 
           class="nav-item {{ request()->routeIs('cs.analytics') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3 flex-1 text-teal-400 font-bold">Laporan Performa</span>
            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Laporan</span>
        </a>

        <a href="{{ route('cs.leads.konsultasi') }}" 
           class="nav-item {{ request()->routeIs('cs.leads.konsultasi') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3 flex-1">Konsultasi</span>
            
            @if(isset($sidebarCounts['cs_konsultasi']) && $sidebarCounts['cs_konsultasi'] > 0)
                <span x-show="!collapsed" class="ml-2 py-0.5 px-2 rounded-full text-xs font-bold bg-yellow-500 text-white shadow-sm">
                    {{ $sidebarCounts['cs_konsultasi'] }}
                </span>
                <span x-show="collapsed" class="absolute top-2 right-2 w-2.5 h-2.5 bg-yellow-500 border border-white rounded-full"></span>
            @endif

            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Konsultasi</span>
        </a>

        <a href="{{ route('cs.leads.closing') }}" 
           class="nav-item {{ request()->routeIs('cs.leads.closing') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3 flex-1">Closing</span>

            @if(isset($sidebarCounts['cs_closing']) && $sidebarCounts['cs_closing'] > 0)
                <span x-show="!collapsed" class="ml-2 py-0.5 px-2 rounded-full text-xs font-bold bg-blue-500 text-white shadow-sm">
                    {{ $sidebarCounts['cs_closing'] }}
                </span>
                <span x-show="collapsed" class="absolute top-2 right-2 w-2.5 h-2.5 bg-blue-500 border border-white rounded-full"></span>
            @endif

            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Closing</span>
        </a>
        @endif

        {{-- Data SPK --}}
        @if(Auth::user()->hasAccess('cs.spk'))
        <a href="{{ route('cs.spk.index') }}" 
           class="nav-item {{ request()->routeIs('cs.spk.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3 flex-1">Data SPK</span>
            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Data SPK</span>
        </a>
        @endif

        {{-- Greeting Chat --}}
        @if(Auth::user()->hasAccess('cs.greeting'))
        <a href="{{ route('cs.greeting.index') }}" 
           class="nav-item {{ request()->routeIs('cs.greeting.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3 flex-1">Greeting Chat</span>

            @if(isset($sidebarCounts['cs_greeting']) && $sidebarCounts['cs_greeting'] > 0)
                <span x-show="!collapsed" class="ml-2 py-0.5 px-2 rounded-full text-xs font-bold bg-green-500 text-white shadow-sm">
                    {{ $sidebarCounts['cs_greeting'] }}
                </span>
                <span x-show="collapsed" class="absolute top-2 right-2 w-2.5 h-2.5 bg-green-500 border border-white rounded-full"></span>
            @endif

            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Greeting</span>
        </a>
        @endif
    </div>
    @endif

    {{-- 2. DIVISI GUDANG --}}
    @if(Auth::user()->hasAccess('gudang'))
    <div class="mt-4 space-y-1">
        <h3 x-show="!collapsed" class="section-title px-3 mb-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Divisi Gudang</h3>
        
        {{-- Warehouse Dashboard --}}
        <a href="{{ route('storage.dashboard') }}" 
           class="nav-item {{ request()->routeIs('storage.dashboard') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3 flex-1 font-bold">Dashboard Gudang</span>
            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Dashboard</span>
        </a>

        <a href="{{ route('reception.index') }}" 
           class="nav-item {{ request()->routeIs('reception.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3 flex-1">Penerimaan</span>
            
            {{-- Badge --}}
            @if(isset($sidebarCounts['reception']) && $sidebarCounts['reception'] > 0)
                <span x-show="!collapsed" class="ml-2 py-0.5 px-2 rounded-full text-xs font-bold bg-teal-500 text-white shadow-sm">
                    {{ $sidebarCounts['reception'] }}
                </span>
                <span x-show="collapsed" class="absolute top-2 right-2 w-2.5 h-2.5 bg-teal-500 border border-white rounded-full"></span>
            @endif

            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Gudang</span>
        </a>

        {{-- Logistik Manifest --}}
        <a href="{{ route('manifest.index') }}" 
           class="nav-item {{ request()->routeIs('manifest.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3 flex-1 text-emerald-400 font-bold">Logistik Manifest</span>
            
            @php $otwCount = \App\Models\WorkshopManifest::where('status', 'SENT')->count(); @endphp
            @if($otwCount > 0)
                <span x-show="!collapsed" class="ml-2 py-0.5 px-2 rounded-full text-xs font-bold bg-blue-500 text-white shadow-sm">
                    {{ $otwCount }}
                </span>
                <span x-show="collapsed" class="absolute top-2 right-2 w-2.5 h-2.5 bg-blue-500 border border-white rounded-full"></span>
            @endif

            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Logistik</span>
        </a>
        
        @if(Auth::user()->hasAccess('admin.materials'))
        <a href="{{ route('material-requests.index') }}" 
           class="nav-item {{ request()->routeIs('material-requests.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3">Pengajuan Material</span>
            {{-- Pending Count --}}
            @php $pendingReq = \App\Models\MaterialRequest::where('status', 'PENDING')->count(); @endphp
            <span x-show="!collapsed && {{ $pendingReq }} > 0" class="ml-auto bg-red-100 text-red-600 py-0.5 px-2 rounded-full text-xs font-bold">{{ $pendingReq }}</span>
            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Pengajuan</span>
        </a>

        <a href="{{ route('admin.materials.index') }}" 
           class="nav-item {{ request()->routeIs('admin.materials.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"/>
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3">Stok Material</span>
            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Material</span>
        </a>
        @endif

        {{-- Customer Master Data --}}
        @if(Auth::user()->hasAccess('admin'))
        <a href="{{ route('admin.customers.index') }}" 
           class="nav-item {{ request()->routeIs('admin.customers.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3">Master Customer</span>
            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Customer</span>
        </a>
        @endif
    </div>
    @endif

    {{-- 3. DIVISI WORKSHOP --}}
    @if(Auth::user()->hasAccess('workshop'))
    <div class="mt-4 space-y-1">
        <h3 x-show="!collapsed" class="section-title px-3 mb-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Divisi Workshop</h3>

        {{-- Workshop Dashboard --}}
        <a href="{{ route('workshop.dashboard') }}" 
           class="nav-item {{ request()->routeIs('workshop.dashboard') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3">Workshop Dashboard</span>
            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Workshop</span>
        </a>

        @if(Auth::user()->hasAccess('assessment'))
        <a href="{{ route('assessment.index') }}" 
           class="nav-item {{ request()->routeIs('assessment.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3 flex-1">Assessment</span>
            
            {{-- Badge --}}
            @if(isset($sidebarCounts['assessment']) && $sidebarCounts['assessment'] > 0)
                <span x-show="!collapsed" class="ml-2 py-0.5 px-2 rounded-full text-xs font-bold bg-blue-500 text-white shadow-sm">
                    {{ $sidebarCounts['assessment'] }}
                </span>
                <span x-show="collapsed" class="absolute top-2 right-2 w-2.5 h-2.5 bg-blue-500 border border-white rounded-full"></span>
            @endif

            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Assessment</span>
        </a>
        @endif

        @if(Auth::user()->hasAccess('preparation'))
        <a href="{{ route('preparation.index') }}" 
           class="nav-item {{ request()->routeIs('preparation.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3 flex-1">Persiapan</span>
            
            {{-- Badge --}}
            @if(isset($sidebarCounts['preparation']) && $sidebarCounts['preparation'] > 0)
                <span x-show="!collapsed" class="ml-2 py-0.5 px-2 rounded-full text-xs font-bold bg-orange-500 text-white shadow-sm">
                    {{ $sidebarCounts['preparation'] }}
                </span>
                <span x-show="collapsed" class="absolute top-2 right-2 w-2.5 h-2.5 bg-orange-500 border border-white rounded-full"></span>
            @endif

            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Persiapan</span>
        </a>
        @endif

        @if(Auth::user()->hasAccess('sortir'))
        <a href="{{ route('sortir.index') }}" 
           class="nav-item {{ request()->routeIs('sortir.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/>
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3 flex-1">Sortir</span>
            
            {{-- Badge --}}
            @if(isset($sidebarCounts['sortir']) && $sidebarCounts['sortir'] > 0)
                <span x-show="!collapsed" class="ml-2 py-0.5 px-2 rounded-full text-xs font-bold bg-teal-500 text-white shadow-sm">
                    {{ $sidebarCounts['sortir'] }}
                </span>
                <span x-show="collapsed" class="absolute top-2 right-2 w-2.5 h-2.5 bg-teal-500 border border-white rounded-full"></span>
            @endif

            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Sortir</span>
        </a>
        @endif

        @if(Auth::user()->hasAccess('production'))
        <a href="{{ route('production.index') }}" 
           class="nav-item {{ request()->routeIs('production.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3 flex-1">Produksi</span>
            
            {{-- Badge --}}
            @if(isset($sidebarCounts['production']) && $sidebarCounts['production'] > 0)
                <span x-show="!collapsed" class="ml-2 py-0.5 px-2 rounded-full text-xs font-bold bg-purple-500 text-white shadow-sm">
                    {{ $sidebarCounts['production'] }}
                </span>
                <span x-show="collapsed" class="absolute top-2 right-2 w-2.5 h-2.5 bg-purple-500 border border-white rounded-full"></span>
            @endif

            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Produksi</span>
        </a>
        @endif

        @if(Auth::user()->hasAccess('qc'))
        <a href="{{ route('qc.index') }}" 
           class="nav-item {{ request()->routeIs('qc.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3 flex-1">QC</span>
            
            {{-- Badge --}}
            @if(isset($sidebarCounts['qc']) && $sidebarCounts['qc'] > 0)
                <span x-show="!collapsed" class="ml-2 py-0.5 px-2 rounded-full text-xs font-bold bg-teal-500 text-white shadow-sm">
                    {{ $sidebarCounts['qc'] }}
                </span>
                <span x-show="collapsed" class="absolute top-2 right-2 w-2.5 h-2.5 bg-teal-500 border border-white rounded-full"></span>
            @endif

            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">QC</span>
        </a>
        @endif

        @if(Auth::user()->hasAccess('workshop'))
        <a href="{{ route('finish.index') }}" 
           class="nav-item {{ request()->routeIs('finish.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"/>
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3 flex-1">Finish</span>
            
            {{-- Badge --}}
            @if(isset($sidebarCounts['finish']) && $sidebarCounts['finish'] > 0)
                <span x-show="!collapsed" class="ml-2 py-0.5 px-2 rounded-full text-xs font-bold bg-green-500 text-white shadow-sm">
                    {{ $sidebarCounts['finish'] }}
                </span>
                <span x-show="collapsed" class="absolute top-2 right-2 w-2.5 h-2.5 bg-green-500 border border-white rounded-full"></span>
            @endif

            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Finish</span>
        </a>
        @endif

        @if(Auth::user()->hasAccess('gudang'))
        <a href="{{ route('storage.index') }}" 
           class="nav-item {{ request()->routeIs('storage.index') || request()->routeIs('storage.show') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3 flex-1">Gudang Finish</span>
            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Gudang</span>
        </a>

        {{-- Manual Warehouse Separator --}}
        <div x-show="!collapsed" class="my-2 border-t border-white/10 mx-2"></div>

        <a href="{{ route('storage.manual.index') }}" 
           class="nav-item {{ request()->routeIs('storage.manual.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative bg-red-900/20 text-red-100 hover:bg-red-800 border border-red-500/30"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0 text-red-400" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3 flex-1 font-bold">Gudang Manual</span>
            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Manual</span>
        </a>

        <a href="{{ route('storage.manual.racks.index') }}" 
           class="nav-item {{ request()->routeIs('storage.manual.racks.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative ml-2"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0 text-red-300" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3 flex-1 text-sm">Kelola Rak Manual</span>
            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Rak Manual</span>
        </a>
        @endif

        <a href="{{ route('gallery.index') }}" 
           class="nav-item {{ request()->routeIs('gallery.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3">Galeri Foto</span>
            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Galeri</span>
        </a>

        @if(Auth::user()->hasAccess('assessment'))
        <a href="{{ route('finance.index') }}" 
           class="nav-item {{ request()->routeIs('finance.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3">Finance</span>
            {{-- Counter --}}
            @php $financeCount = \App\Models\WorkOrder::where('status', 'WAITING_PAYMENT')->count(); @endphp
            <span x-show="!collapsed && {{ $financeCount }} > 0" class="ml-auto bg-yellow-100 text-yellow-600 py-0.5 px-2 rounded-full text-xs font-bold">{{ $financeCount }}</span>
            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Finance</span>
        </a>
        @endif
    </div>
    @endif

    {{-- 4. DIVISI CUSTOMER EXPERIENCE --}}
    @if(Auth::user()->hasAccess('cx'))
    <div class="mt-4 space-y-1">
        <h3 x-show="!collapsed" class="section-title px-3 mb-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Divisi Customer Experience</h3>

        {{-- CX Analytics Dashboard --}}
        <a href="{{ route('cx.dashboard') }}" 
           class="nav-item {{ request()->routeIs('cx.dashboard') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3">CX Dashboard</span>
            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Dashboard</span>
        </a>

        {{-- CX Follow Up (Worklist) --}}
        <a href="{{ route('cx.index') }}" 
           class="nav-item {{ request()->routeIs('cx.index') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3">Follow Up</span>
            
            {{-- Counter --}}
            @php $cxCount = \App\Models\WorkOrder::where('status', 'HOLD_FOR_CX')->orWhere('status', 'CX_FOLLOWUP')->count(); @endphp
            <span x-show="!collapsed && {{ $cxCount }} > 0" class="ml-auto bg-red-100 text-red-600 py-0.5 px-2 rounded-full text-xs font-bold">{{ $cxCount }}</span>
            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">CX</span>
        </a>

        {{-- CX OTO Pool --}}
        <a href="{{ route('cx.oto.index') }}" 
           class="nav-item {{ request()->routeIs('cx.oto.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3">Kolam OTO (Upsell)</span>
            
            {{-- Counter for Pending CX --}}
            @php $otoPending = \App\Models\OTO::where('status', 'PENDING_CX')->count(); @endphp
            <span x-show="!collapsed && {{ $otoPending }} > 0" class="ml-auto bg-orange-100 text-orange-600 py-0.5 px-2 rounded-full text-xs font-bold">{{ $otoPending }}</span>
            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">OTO</span>
        </a>

        @if(Auth::user()->hasAccess('admin.complaints'))
        <a href="{{ route('admin.complaints.index') }}" 
           class="nav-item {{ request()->routeIs('admin.complaints.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3">Komplain</span>
            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Komplain</span>
        </a>
        @endif
    </div>
    @endif

    {{-- Master Data Section (Hidden for HR) --}}
    @if(Auth::user()->role !== 'hr')
    <div x-show="!collapsed" class="section-divider my-4"></div>
    <div x-show="collapsed" class="my-4 border-t border-white/20"></div>
    
    <div class="mt-2">
        <h3 x-show="!collapsed" class="section-title px-3 mb-2">Master Data</h3>
        
        @if(Auth::user()->hasAccess('admin.services'))
        <a href="{{ route('admin.services.index') }}" 
           class="nav-item {{ request()->routeIs('admin.services.*') && !request()->routeIs('admin.promotions.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3">Layanan</span>
            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Layanan</span>
        </a>
        @endif

        @if(Auth::user()->hasAccess('admin.services'))
        <a href="{{ route('admin.promotions.index') }}" 
           class="nav-item {{ request()->routeIs('admin.promotions.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3">Promo</span>
            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Promo</span>
        </a>
        @endif

        @if(Auth::user()->hasAccess('admin.materials'))
        <a href="{{ route('admin.materials.index') }}" 
           class="nav-item {{ request()->routeIs('admin.materials.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"/>
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3">Material</span>
            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Material</span>
        </a>
        @endif

        @if(Auth::user()->hasAccess('admin.purchases'))
        <a href="{{ route('admin.purchases.index') }}" 
           class="nav-item {{ request()->routeIs('admin.purchases.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3">Pembelian</span>
            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Pembelian</span>
        </a>
        @endif

        @if(Auth::user()->hasAccess('admin.users'))
        <a href="{{ route('admin.users.index') }}" 
           class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3">Pengguna</span>
            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Pengguna</span>
        </a>
        @endif

        @if(Auth::user()->hasAccess('admin.reports'))
        <a href="{{ route('admin.reports.index') }}" 
           class="nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"/>
                <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"/>
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3">Laporan</span>
            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Laporan</span>
        </a>
        @endif

        @if(Auth::user()->hasAccess('admin.performance'))
        <a href="{{ route('admin.performance.index') }}" 
           class="nav-item {{ request()->routeIs('admin.performance.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3">Performa</span>
            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Performa</span>
        </a>
        @endif


        @if(Auth::user()->hasAccess('admin.system'))
        <div x-show="!collapsed" class="section-divider my-4"></div>
        <div x-show="collapsed" class="my-4 border-t border-white/20"></div>

        <a href="{{ route('admin.data-integrity.index') }}" 
           class="nav-item {{ request()->routeIs('admin.data-integrity.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative border border-indigo-500/30 bg-indigo-900/20 text-indigo-100 hover:bg-indigo-800"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0 text-indigo-400" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04M12 21.48V11.5" />
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3 font-bold">Kesehatan Data</span>
            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Data</span>
        </a>

        <a href="{{ route('admin.system.index') }}" 
           class="nav-item {{ request()->routeIs('admin.system.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative bg-red-900/30 text-red-100 hover:bg-red-800 mt-2"
           :class="collapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0 text-red-400" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            <span x-show="!collapsed" class="nav-item-text ml-3 font-bold">Pembersihan Sistem</span>
            <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Reset</span>
        </a>
        @endif
    </div>
    @endif
    @endif
</div>
