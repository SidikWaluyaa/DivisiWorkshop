<div x-data="{ collapsed: false }" 
     :class="collapsed ? 'w-16' : 'w-64'" 
     class="sidebar-gradient h-screen sticky top-0 hidden sm:flex flex-col transition-all duration-300 relative">
    
    {{-- Toggle Button - Absolute Position --}}
    <button @click="collapsed = !collapsed" 
            class="absolute top-4 right-4 z-50 p-2 rounded-lg bg-teal-600 hover:bg-teal-700 transition-colors shadow-lg"
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
    <div class="flex-1 px-2 overflow-y-auto sidebar-scroll">
        {{-- Main Navigation --}}
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
            
            <a href="{{ route('reception.index') }}" 
               class="nav-item {{ request()->routeIs('reception.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
               :class="collapsed ? 'justify-center' : ''">
                <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                </svg>
                <span x-show="!collapsed" class="nav-item-text ml-3">Gudang</span>
                <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Gudang</span>
            </a>

            <a href="{{ route('assessment.index') }}" 
               class="nav-item {{ request()->routeIs('assessment.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
               :class="collapsed ? 'justify-center' : ''">
                <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                </svg>
                <span x-show="!collapsed" class="nav-item-text ml-3">Assessment</span>
                <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Assessment</span>
            </a>

            <a href="{{ route('preparation.index') }}" 
               class="nav-item {{ request()->routeIs('preparation.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
               :class="collapsed ? 'justify-center' : ''">
                <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
                <span x-show="!collapsed" class="nav-item-text ml-3">Persiapan</span>
                <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Persiapan</span>
            </a>

            <a href="{{ route('sortir.index') }}" 
               class="nav-item {{ request()->routeIs('sortir.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
               :class="collapsed ? 'justify-center' : ''">
                <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/>
                </svg>
                <span x-show="!collapsed" class="nav-item-text ml-3">Sortir</span>
                <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Sortir</span>
            </a>

            <a href="{{ route('production.index') }}" 
               class="nav-item {{ request()->routeIs('production.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
               :class="collapsed ? 'justify-center' : ''">
                <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                </svg>
                <span x-show="!collapsed" class="nav-item-text ml-3">Produksi</span>
                <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Produksi</span>
            </a>

            <a href="{{ route('qc.index') }}" 
               class="nav-item {{ request()->routeIs('qc.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
               :class="collapsed ? 'justify-center' : ''">
                <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span x-show="!collapsed" class="nav-item-text ml-3">QC</span>
                <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">QC</span>
            </a>

            <a href="{{ route('finish.index') }}" 
               class="nav-item {{ request()->routeIs('finish.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
               :class="collapsed ? 'justify-center' : ''">
                <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                    <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"/>
                </svg>
                <span x-show="!collapsed" class="nav-item-text ml-3">Finish</span>
                <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Finish</span>
            </a>
        </div>

        {{-- Master Data Section --}}
        <div x-show="!collapsed" class="section-divider my-4"></div>
        <div x-show="collapsed" class="my-4 border-t border-white/20"></div>
        
        <div class="mt-2">
            <h3 x-show="!collapsed" class="section-title px-3 mb-2">Master Data</h3>
            
            <a href="{{ route('admin.services.index') }}" 
               class="nav-item {{ request()->routeIs('admin.services.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
               :class="collapsed ? 'justify-center' : ''">
                <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                </svg>
                <span x-show="!collapsed" class="nav-item-text ml-3">Layanan</span>
                <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Layanan</span>
            </a>

            <a href="{{ route('admin.materials.index') }}" 
               class="nav-item {{ request()->routeIs('admin.materials.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
               :class="collapsed ? 'justify-center' : ''">
                <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"/>
                </svg>
                <span x-show="!collapsed" class="nav-item-text ml-3">Material</span>
                <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Material</span>
            </a>

            <a href="{{ route('admin.purchases.index') }}" 
               class="nav-item {{ request()->routeIs('admin.purchases.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
               :class="collapsed ? 'justify-center' : ''">
                <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
                </svg>
                <span x-show="!collapsed" class="nav-item-text ml-3">Pembelian</span>
                <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Pembelian</span>
            </a>

            <a href="{{ route('admin.users.index') }}" 
               class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
               :class="collapsed ? 'justify-center' : ''">
                <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                </svg>
                <span x-show="!collapsed" class="nav-item-text ml-3">Pengguna</span>
                <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Pengguna</span>
            </a>

            <a href="{{ route('admin.performance.index') }}" 
               class="nav-item {{ request()->routeIs('admin.performance.*') ? 'active' : '' }} flex items-center px-3 py-3 rounded-lg group relative"
               :class="collapsed ? 'justify-center' : ''">
                <svg class="nav-icon flex-shrink-0" :class="collapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                </svg>
                <span x-show="!collapsed" class="nav-item-text ml-3">Performa</span>
                <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Performa</span>
            </a>
        </div>
    </div>

    {{-- User Section --}}
    <div class="user-section p-4">
        <div x-show="!collapsed" class="user-name mb-2 text-center">
            👤 {{ Auth::user()->name }}
        </div>
        <div x-show="collapsed" class="flex justify-center mb-2">
            <div class="w-8 h-8 bg-teal-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <a href="{{ route('logout') }}" 
               onclick="event.preventDefault(); this.closest('form').submit();" 
               class="logout-btn flex items-center justify-center px-3 py-2 rounded-lg group relative"
               :class="collapsed ? 'px-2' : ''">
                <svg class="flex-shrink-0" :class="collapsed ? 'w-5 h-5' : 'w-4 h-4 mr-2'" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/>
                </svg>
                <span x-show="!collapsed">Keluar</span>
                <span x-show="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Keluar</span>
            </a>
        </form>
    </div>
</div>
