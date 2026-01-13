{{-- Mobile Menu State --}}
<div x-data="{ mobileMenuOpen: false, collapsed: false }" 
     @toggle-mobile-menu.window="mobileMenuOpen = !mobileMenuOpen"
     class="contents">
    
    {{-- Mobile Backdrop --}}
    <div x-show="mobileMenuOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileMenuOpen = false"
         class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm z-40 lg:hidden"
         style="display: none;">
    </div>

    {{-- Mobile Sidebar (Overlay) --}}
    <div x-show="mobileMenuOpen"
         x-transition:enter="transition ease-in-out duration-300 transform"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in-out duration-300 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="fixed inset-y-0 left-0 z-50 w-64 sidebar-gradient lg:hidden"
         style="display: none;">
        
        {{-- Close Button --}}
        <button @click="mobileMenuOpen = false" 
                class="absolute top-4 right-4 z-50 p-2 rounded-lg bg-teal-600 hover:bg-teal-700 transition-colors shadow-lg">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        {{-- Mobile Sidebar Content (Same as Desktop) --}}
        @include('layouts.partials.sidebar-content')
    </div>

    {{-- Desktop Sidebar --}}
    <div :class="collapsed ? 'w-16' : 'w-64'" 
         class="sidebar-gradient h-screen sticky top-0 hidden lg:flex flex-col transition-all duration-300 relative">
        
        @include('layouts.partials.sidebar-content')
        
    </div>

{{-- Close Alpine.js Mobile Menu Wrapper --}}
</div>
