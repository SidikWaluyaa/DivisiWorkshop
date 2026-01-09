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

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Dropdown items... keep as is -->
                 <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="header-dropdown-trigger inline-flex items-center px-4 py-2 text-sm leading-4 font-medium rounded-lg focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
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
                {{ __('Reception') }}
            </x-responsive-nav-link>

             <x-responsive-nav-link :href="route('assessment.index')" :active="request()->routeIs('assessment.*')" class="mobile-menu-item">
                {{ __('Assessment') }}
            </x-responsive-nav-link>
             <x-responsive-nav-link :href="route('preparation.index')" :active="request()->routeIs('preparation.*')" class="mobile-menu-item">
                {{ __('Preparation') }}
            </x-responsive-nav-link>
             <x-responsive-nav-link :href="route('sortir.index')" :active="request()->routeIs('sortir.*')" class="mobile-menu-item">
                {{ __('Sortir') }}
            </x-responsive-nav-link>
             <x-responsive-nav-link :href="route('production.index')" :active="request()->routeIs('production.*')" class="mobile-menu-item">
                {{ __('Production') }}
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
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();" class="mobile-menu-item">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
