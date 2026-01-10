<nav x-data="{ open: false }" class="header-gradient">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Mobile Logo (shown only on small screens) -->
                <div class="shrink-0 flex items-center sm:hidden">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-12 w-auto" />
                    </a>
                </div>

                <!-- Page Heading (Desktop) -->
                @isset($header)
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <div class="header-text">
                        {{ $header }}
                    </div>
                </div>
                @endisset
            </div>

            <!-- Settings Dropdown Removed as per request -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Empty div to maintain spacing or removed entirely if not needed -->
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="hamburger-btn inline-flex items-center justify-center p-2 rounded-md focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden mobile-menu">
         <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="mobile-menu-item">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('reception.index')" :active="request()->routeIs('reception.*')" class="mobile-menu-item">
                {{ __('Gudang') }}
            </x-responsive-nav-link>

             <x-responsive-nav-link :href="route('assessment.index')" :active="request()->routeIs('assessment.*')" class="mobile-menu-item">
                {{ __('Assessment') }}
            </x-responsive-nav-link>
             <x-responsive-nav-link :href="route('preparation.index')" :active="request()->routeIs('preparation.*')" class="mobile-menu-item">
                {{ __('Persiapan') }}
            </x-responsive-nav-link>
             <x-responsive-nav-link :href="route('sortir.index')" :active="request()->routeIs('sortir.*')" class="mobile-menu-item">
                {{ __('Sortir') }}
            </x-responsive-nav-link>
             <x-responsive-nav-link :href="route('production.index')" :active="request()->routeIs('production.*')" class="mobile-menu-item">
                {{ __('Produksi') }}
            </x-responsive-nav-link>
             <x-responsive-nav-link :href="route('qc.index')" :active="request()->routeIs('qc.*')" class="mobile-menu-item">
                {{ __('QC') }}
            </x-responsive-nav-link>
             <x-responsive-nav-link :href="route('finish.index')" :active="request()->routeIs('finish.*')" class="mobile-menu-item">
                {{ __('Selesai') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 mobile-user-section">
            <div class="px-4">
                <div class="mobile-user-name text-base">{{ Auth::user()->name }}</div>
                <div class="mobile-user-email text-sm">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="mobile-menu-item">
                    {{ __('Profil') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();" class="mobile-menu-item">
                        {{ __('Keluar') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
