<div class="w-64 bg-white dark:bg-gray-800 border-r border-gray-100 dark:border-gray-700 min-h-screen hidden sm:block">
    <div class="h-16 flex items-center justify-center border-b border-gray-100 dark:border-gray-700">
        <a href="{{ route('dashboard') }}">
            <x-application-logo class="block h-20 w-auto fill-current text-gray-800 dark:text-gray-200" />
        </a>
    </div>

    <div class="p-4 space-y-2 overflow-y-auto">
        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="block w-full text-left px-4 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
            {{ __('Dashboard') }}
        </x-nav-link>
        
        <x-nav-link :href="route('reception.index')" :active="request()->routeIs('reception.*')" class="block w-full text-left px-4 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
            {{ __('Gudang / Reception') }}
        </x-nav-link>



        <x-nav-link :href="route('assessment.index')" :active="request()->routeIs('assessment.*')" class="block w-full text-left px-4 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
            {{ __('Assessment') }}
        </x-nav-link>

        <x-nav-link :href="route('preparation.index')" :active="request()->routeIs('preparation.*')" class="block w-full text-left px-4 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
            {{ __('Preparation') }}
        </x-nav-link>

        <x-nav-link :href="route('sortir.index')" :active="request()->routeIs('sortir.*')" class="block w-full text-left px-4 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
            {{ __('Sortir/Material') }}
        </x-nav-link>

        <x-nav-link :href="route('production.index')" :active="request()->routeIs('production.*')" class="block w-full text-left px-4 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
            {{ __('Production') }}
        </x-nav-link>

        <x-nav-link :href="route('qc.index')" :active="request()->routeIs('qc.*')" class="block w-full text-left px-4 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
            {{ __('QC') }}
        </x-nav-link>

        <x-nav-link :href="route('finish.index')" :active="request()->routeIs('finish.*')" class="block w-full text-left px-4 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
            {{ __('Finish / Pickup') }}
        </x-nav-link>

        <div class="pt-4 mt-4 border-t border-gray-100 dark:border-gray-700">
            <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                Master Data
            </h3>
            
            <x-nav-link :href="route('admin.services.index')" :active="request()->routeIs('admin.services.*')" class="block w-full text-left px-4 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                {{ __('Manajemen Layanan') }}
            </x-nav-link>

            <x-nav-link :href="route('admin.materials.index')" :active="request()->routeIs('admin.materials.*')" class="block w-full text-left px-4 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                {{ __('Manajemen Material') }}
            </x-nav-link>

            <x-nav-link :href="route('admin.purchases.index')" :active="request()->routeIs('admin.purchases.*')" class="block w-full text-left px-4 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                {{ __('Purchase / Belanja') }}
            </x-nav-link>

            <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" class="block w-full text-left px-4 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                {{ __('Manajemen User/Teknisi') }}
            </x-nav-link>

            <x-nav-link :href="route('admin.performance.index')" :active="request()->routeIs('admin.performance.*')" class="block w-full text-left px-4 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                {{ __('Performance & Kinerja') }}
            </x-nav-link>
        </div>

        <div class="border-t pt-4 mt-4 dark:border-gray-700">
            <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                User
            </h3>
            <div class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">
                 {{ Auth::user()->name }}
            </div>
             <form method="POST" action="{{ route('logout') }}" class="px-4">
                @csrf
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-500 text-sm hover:underline">
                    Log Out
                </a>
            </form>
        </div>
    </div>
</div>
