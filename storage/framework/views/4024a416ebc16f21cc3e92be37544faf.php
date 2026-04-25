
<button @click="$dispatch('toggle-sidebar')" 
        class="absolute top-4 right-4 z-50 p-2 rounded-lg bg-teal-600 hover:bg-teal-700 transition-colors shadow-lg hidden lg:block"
        title="Toggle Sidebar">
    <svg class="w-5 h-5 text-white transition-transform duration-300" 
         :class="{ 'rotate-180': sidebarCollapsed }"
         fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
    </svg>
</button>


<div x-show="!sidebarCollapsed" class="sidebar-logo-container flex items-center justify-center">
    <a href="<?php echo e(route('dashboard')); ?>" class="transition-transform hover:scale-105">
        <?php if (isset($component)) { $__componentOriginal8892e718f3d0d7a916180885c6f012e7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8892e718f3d0d7a916180885c6f012e7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.application-logo','data' => ['class' => 'block h-20 w-auto']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('application-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'block h-20 w-auto']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8892e718f3d0d7a916180885c6f012e7)): ?>
<?php $attributes = $__attributesOriginal8892e718f3d0d7a916180885c6f012e7; ?>
<?php unset($__attributesOriginal8892e718f3d0d7a916180885c6f012e7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8892e718f3d0d7a916180885c6f012e7)): ?>
<?php $component = $__componentOriginal8892e718f3d0d7a916180885c6f012e7; ?>
<?php unset($__componentOriginal8892e718f3d0d7a916180885c6f012e7); ?>
<?php endif; ?>
    </a>
</div>


<div x-show="sidebarCollapsed" x-cloak class="flex items-center justify-center py-6 mt-12">
    <a href="<?php echo e(route('dashboard')); ?>" class="transition-transform hover:scale-110">
        <div class="w-10 h-10 bg-gradient-to-br from-teal-400 to-orange-400 rounded-lg flex items-center justify-center shadow-lg">
            <span class="text-white font-bold text-xl">S</span>
        </div>
    </a>
</div>


<div id="sidebar-nav-container" class="flex-1 px-2 overflow-y-auto sidebar-scroll pb-4 min-h-0" style="max-height: calc(100vh - 180px);">
    

    
    <div class="space-y-1">
        <a href="<?php echo e(route('dashboard')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3">Dashboard</span>
            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Dashboard</span>
        </a>

        
        <a href="<?php echo e(route('internal-tracking.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('internal-tracking.index') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative border border-teal-500/20 bg-teal-900/10 hover:bg-teal-800/30"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0 text-teal-400" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3 font-bold text-teal-400">Internal Tracking</span>
            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-teal-400 text-xs font-bold rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Lacak SPK</span>
        </a>
    </div>
    
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->role !== 'hr'): ?>
    <div x-show="!sidebarCollapsed" class="section-divider my-4"></div>
    <div x-show="sidebarCollapsed" class="my-4 border-t border-white/20"></div>

    
    
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('access-cs')): ?>
    <div x-data="{ 
            open: localStorage.getItem('sb_cs') === 'true' || <?php echo e(request()->routeIs('cs.*') ? 'true' : 'false'); ?>,
            toggle() {
                this.open = !this.open;
                localStorage.setItem('sb_cs', this.open);
            }
         }" 
         class="mt-2 text-white">
        
        <button @click="toggle()" 
                type="button" 
                class="w-full flex items-center justify-between px-3 py-2.5 transition-all duration-300 group rounded-xl mb-1 active:scale-95 touch-manipulation"
                :class="open ? 'bg-white/15 text-white shadow-lg ring-1 ring-white/30 backdrop-blur-sm' : 'text-gray-400 hover:text-white hover:bg-white/5'"
                :title="sidebarCollapsed ? 'Divisi CS' : ''">
            <div class="flex items-center gap-3">
                <!-- CS Icon -->
                <svg class="w-5 h-5 flex-shrink-0 transition-transform duration-300" 
                     :class="{ 'text-teal-400 scale-110 rotate-3': open, 'text-gray-400 group-hover:scale-110': !open }" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <h3 x-show="!sidebarCollapsed" 
                    class="section-title mb-0 text-xs font-bold uppercase tracking-wider transition-colors"
                    :class="open ? 'text-teal-100' : 'text-gray-400 group-hover:text-teal-400'">Divisi CS</h3>
            </div>
            <svg x-show="!sidebarCollapsed" :class="{ 'rotate-180': open }" class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        
        <div x-show="open" x-collapse x-cloak class="space-y-1 mt-1 ml-4 border-l-2 border-white/10 pl-2">
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->hasAccess('cs')): ?>
        <a href="<?php echo e(route('cs.dashboard')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('cs.dashboard') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3 flex-1">CS Dashboard</span>
            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Dashboard</span>
        </a>

        <a href="<?php echo e(route('cs.analytics')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('cs.analytics') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3 flex-1 text-teal-400 font-bold">Laporan Performa</span>
            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Laporan</span>
        </a>

        <a href="<?php echo e(route('cs.leads.konsultasi')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('cs.leads.konsultasi') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3 flex-1">Konsultasi</span>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($sidebarCounts['cs_konsultasi']) && $sidebarCounts['cs_konsultasi'] > 0): ?>
                <span x-show="!sidebarCollapsed" class="ml-2 py-0.5 px-2 rounded-full text-xs font-bold bg-yellow-500 text-white shadow-sm">
                    <?php echo e($sidebarCounts['cs_konsultasi']); ?>

                </span>
                <span x-show="sidebarCollapsed" class="absolute top-2 right-2 w-2.5 h-2.5 bg-yellow-500 border border-white rounded-full"></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Konsultasi</span>
        </a>

        <a href="<?php echo e(route('cs.leads.follow-up')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('cs.leads.follow-up') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3 flex-1">Follow-up</span>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($sidebarCounts['cs_follow_up']) && $sidebarCounts['cs_follow_up'] > 0): ?>
                <span x-show="!sidebarCollapsed" class="ml-2 py-0.5 px-2 rounded-full text-xs font-bold bg-orange-500 text-white shadow-sm">
                    <?php echo e($sidebarCounts['cs_follow_up']); ?>

                </span>
                <span x-show="sidebarCollapsed" class="absolute top-2 right-2 w-2.5 h-2.5 bg-orange-500 border border-white rounded-full"></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Follow-up</span>
        </a>

        <a href="<?php echo e(route('cs.leads.closing')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('cs.leads.closing') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3 flex-1">Closing</span>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($sidebarCounts['cs_closing']) && $sidebarCounts['cs_closing'] > 0): ?>
                <span x-show="!sidebarCollapsed" class="ml-2 py-0.5 px-2 rounded-full text-xs font-bold bg-blue-500 text-white shadow-sm">
                    <?php echo e($sidebarCounts['cs_closing']); ?>

                </span>
                <span x-show="sidebarCollapsed" class="absolute top-2 right-2 w-2.5 h-2.5 bg-blue-500 border border-white rounded-full"></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Closing</span>
        </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->hasAccess('cs.spk')): ?>
        <a href="<?php echo e(route('cs.spk.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('cs.spk.*') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3 flex-1">Data SPK</span>
            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Data SPK</span>
        </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->hasAccess('cs.greeting')): ?>
        <a href="<?php echo e(route('cs.greeting.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('cs.greeting.*') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3 flex-1">Greeting Chat</span>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($sidebarCounts['cs_greeting']) && $sidebarCounts['cs_greeting'] > 0): ?>
                <span x-show="!sidebarCollapsed" class="ml-2 py-0.5 px-2 rounded-full text-xs font-bold bg-green-500 text-white shadow-sm">
                    <?php echo e($sidebarCounts['cs_greeting']); ?>

                </span>
                <span x-show="sidebarCollapsed" class="absolute top-2 right-2 w-2.5 h-2.5 bg-green-500 border border-white rounded-full"></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Greeting</span>
        </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('access-gudang')): ?>
    <div x-data="{ 
            open: localStorage.getItem('sb_gudang') === 'true' || <?php echo e(request()->routeIs('admin.supply-chain.*') || request()->routeIs('material-requests.*') || request()->routeIs('admin.materials.*') || request()->routeIs('admin.purchases.*') || request()->routeIs('storage.*') ? 'true' : 'false'); ?>,
            toggle() {
                this.open = !this.open;
                localStorage.setItem('sb_gudang', this.open);
            }
         }" 
         class="mt-4 text-white">
        
        <button @click="toggle()" 
                type="button" 
                class="w-full flex items-center justify-between px-3 py-2.5 transition-all duration-300 group rounded-xl mb-1 active:scale-95 touch-manipulation"
                :class="open ? 'bg-white/15 text-white shadow-lg ring-1 ring-white/30 backdrop-blur-sm' : 'text-gray-400 hover:text-white hover:bg-white/5'"
                :title="sidebarCollapsed ? 'Divisi Gudang' : ''">
            <div class="flex items-center gap-3">
                <!-- Gudang Icon -->
                <svg class="w-5 h-5 flex-shrink-0 transition-transform duration-300" 
                     :class="{ 'text-orange-400 scale-110 rotate-3': open, 'text-gray-400 group-hover:scale-110': !open }" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <h3 x-show="!sidebarCollapsed" 
                    class="section-title mb-0 text-xs font-bold uppercase tracking-wider transition-colors"
                    :class="open ? 'text-orange-100' : 'text-gray-400 group-hover:text-orange-400'">Divisi Gudang</h3>
            </div>
            <svg x-show="!sidebarCollapsed" :class="{ 'rotate-180': open }" class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <div x-show="open" x-collapse x-cloak class="space-y-1 mt-1 ml-4 border-l-2 border-white/10 pl-2">

        
        <a href="<?php echo e(route('admin.supply-chain.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('admin.supply-chain.*') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative border border-teal-500/20 bg-teal-900/10 hover:bg-teal-800/30"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0 text-teal-400" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3 flex-1 font-bold text-teal-400">Supply Chain Portal</span>
            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-teal-400 text-xs font-bold rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Supply Chain</span>
        </a>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->hasAccess('admin.materials')): ?>
        
        <a href="<?php echo e(route('material-requests.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('material-requests.*') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3">Pengajuan Material</span>
            
            <?php $pendingReq = \App\Models\MaterialRequest::where('status', 'PENDING')->count(); ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pendingReq > 0): ?>
                <span x-show="!sidebarCollapsed" class="ml-auto bg-red-100 text-red-600 py-0.5 px-2 rounded-full text-xs font-bold"><?php echo e($pendingReq); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Pengajuan</span>
        </a>

        
        <a href="<?php echo e(route('admin.materials.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('admin.materials.index') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"/>
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3">Stok Material</span>
            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Material</span>
        </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->hasAccess('admin.purchases')): ?>
        
        <a href="<?php echo e(route('admin.purchases.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('admin.purchases.*') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3">Pembelian</span>
            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Pembelian</span>
        </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div x-show="!sidebarCollapsed" class="section-divider my-4"></div>
        <div x-show="sidebarCollapsed" class="my-4 border-t border-white/20"></div>

        <h3 x-show="!sidebarCollapsed" class="section-title px-3 mb-2">Operasional Gudang</h3>

        
        <a href="<?php echo e(route('storage.dashboard')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('storage.dashboard') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3 flex-1 font-bold">Dashboard Gudang</span>
            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Dashboard</span>
        </a>

        
        <a href="<?php echo e(route('storage.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('storage.index') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3 flex-1 text-primary-green font-bold">Penyimpanan Rak</span>
            
            <?php $totalStored = \App\Models\StorageAssignment::stored()->count(); ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($totalStored > 0): ?>
                <span x-show="!sidebarCollapsed" class="ml-2 py-0.5 px-2 rounded-full text-xs font-bold bg-primary-green text-white shadow-sm">
                    <?php echo e($totalStored); ?>

                </span>
                <span x-show="sidebarCollapsed" class="absolute top-2 right-2 w-2.5 h-2.5 bg-primary-green border border-white rounded-full"></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Rak</span>
        </a>

        <a href="<?php echo e(route('reception.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('reception.*') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3 flex-1">Penerimaan</span>
            
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($sidebarCounts['reception']) && $sidebarCounts['reception'] > 0): ?>
                <span x-show="!sidebarCollapsed" class="ml-2 py-0.5 px-2 rounded-full text-xs font-bold bg-teal-500 text-white shadow-sm">
                    <?php echo e($sidebarCounts['reception']); ?>

                </span>
                <span x-show="sidebarCollapsed" class="absolute top-2 right-2 w-2.5 h-2.5 bg-teal-500 border border-white rounded-full"></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Gudang</span>
        </a>

        
        <a href="<?php echo e(route('manifest.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('manifest.*') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3 flex-1 text-emerald-400 font-bold">Logistik Manifest</span>
            
            <?php $manifestCount = \App\Models\WorkshopManifest::whereIn('status', ['DRAFT', 'SENT'])->count(); ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($manifestCount > 0): ?>
                <span x-show="!sidebarCollapsed" class="ml-2 py-0.5 px-2 rounded-full text-xs font-bold bg-blue-500 text-white shadow-sm">
                    <?php echo e($manifestCount); ?>

                </span>
                <span x-show="sidebarCollapsed" class="absolute top-2 right-2 w-2.5 h-2.5 bg-blue-500 border border-white rounded-full"></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Logistik</span>
        </a>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->hasAccess('admin')): ?>
        
        <a href="<?php echo e(route('storage.manual.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('storage.manual.*') && !request()->routeIs('storage.manual.racks.*') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3 flex-1 text-orange-400 font-bold">Gudang Manual</span>
            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">G. Manual</span>
        </a>

        
        <a href="<?php echo e(route('storage.manual.racks.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('storage.manual.racks.*') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3 flex-1 text-orange-400 font-bold">Rak Manual</span>
            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">R. Manual</span>
        </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('access-workshop')): ?>
    <div x-data="{ 
            open: localStorage.getItem('sb_workshop') === 'true' || <?php echo e(request()->routeIs('workshop.*') || request()->routeIs('assessment.*') || request()->routeIs('preparation.*') || request()->routeIs('sortir.*') || request()->routeIs('production.*') || request()->routeIs('qc.*') || request()->routeIs('finish.*') || request()->routeIs('revision.*') || request()->routeIs('garansi.*') ? 'true' : 'false'); ?>,
            toggle() {
                this.open = !this.open;
                localStorage.setItem('sb_workshop', this.open);
            }
         }" 
         class="mt-4 text-white">
        
        <button @click="toggle()" 
                type="button" 
                class="w-full flex items-center justify-between px-3 py-2.5 transition-all duration-300 group rounded-xl mb-1 active:scale-95 touch-manipulation"
                :class="open ? 'bg-white/15 text-white shadow-lg ring-1 ring-white/30 backdrop-blur-sm' : 'text-gray-400 hover:text-white hover:bg-white/5'"
                :title="sidebarCollapsed ? 'Divisi Workshop' : ''">
            <div class="flex items-center gap-3">
                <!-- Workshop Icon -->
                <svg class="w-5 h-5 flex-shrink-0 transition-transform duration-300" 
                     :class="{ 'text-blue-400 scale-110 rotate-3': open, 'text-gray-400 group-hover:scale-110': !open }" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <h3 x-show="!sidebarCollapsed" 
                    class="section-title mb-0 text-xs font-bold uppercase tracking-wider transition-colors"
                    :class="open ? 'text-blue-100' : 'text-gray-400 group-hover:text-blue-400'">Divisi Workshop</h3>
            </div>
            <svg x-show="!sidebarCollapsed" :class="{ 'rotate-180': open }" class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <div x-show="open" x-collapse x-cloak class="space-y-1 mt-1 ml-4 border-l-2 border-white/10 pl-2">

        
        <a href="<?php echo e(route('workshop.dashboard-v2')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('workshop.dashboard-v2') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3">Workshop Dashboard</span>
            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Workshop</span>
        </a>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->hasAccess('assessment')): ?>
        <a href="<?php echo e(route('assessment.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('assessment.*') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3 flex-1">Assessment</span>
            
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($sidebarCounts['assessment']) && $sidebarCounts['assessment'] > 0): ?>
                <span x-show="!sidebarCollapsed" class="ml-2 py-0.5 px-2 rounded-full text-xs font-bold bg-blue-500 text-white shadow-sm">
                    <?php echo e($sidebarCounts['assessment']); ?>

                </span>
                <span x-show="sidebarCollapsed" class="absolute top-2 right-2 w-2.5 h-2.5 bg-blue-500 border border-white rounded-full"></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Assessment</span>
        </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->hasAccess('preparation')): ?>
        <a href="<?php echo e(route('preparation.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('preparation.*') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3 flex-1">Persiapan</span>
            
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($sidebarCounts['preparation']) && $sidebarCounts['preparation'] > 0): ?>
                <span x-show="!sidebarCollapsed" class="ml-2 py-0.5 px-2 rounded-full text-xs font-bold bg-orange-500 text-white shadow-sm">
                    <?php echo e($sidebarCounts['preparation']); ?>

                </span>
                <span x-show="sidebarCollapsed" class="absolute top-2 right-2 w-2.5 h-2.5 bg-orange-500 border border-white rounded-full"></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Persiapan</span>
        </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->hasAccess('sortir')): ?>
        <a href="<?php echo e(route('sortir.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('sortir.*') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/>
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3 flex-1">Sortir</span>
            
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($sidebarCounts['sortir']) && $sidebarCounts['sortir'] > 0): ?>
                <span x-show="!sidebarCollapsed" class="ml-2 py-0.5 px-2 rounded-full text-xs font-bold bg-teal-500 text-white shadow-sm">
                    <?php echo e($sidebarCounts['sortir']); ?>

                </span>
                <span x-show="sidebarCollapsed" class="absolute top-2 right-2 w-2.5 h-2.5 bg-teal-500 border border-white rounded-full"></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Sortir</span>
        </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->hasAccess('production')): ?>
        <a href="<?php echo e(route('production.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('production.*') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3 flex-1 text-blue-400 font-bold">Produksi</span>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($sidebarCounts['production']) && $sidebarCounts['production'] > 0): ?>
                <span x-show="!sidebarCollapsed" class="ml-2 py-0.5 px-2 rounded-full text-xs font-bold bg-blue-500 text-white shadow-sm">
                    <?php echo e($sidebarCounts['production']); ?>

                </span>
                <span x-show="sidebarCollapsed" class="absolute top-2 right-2 w-2.5 h-2.5 bg-blue-500 border border-white rounded-full"></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Produksi</span>
        </a>

        
        <a href="<?php echo e(route('production.late-info')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('production.late-info') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3 flex-1 text-orange-400 font-bold">Info Keterlambatan</span>
            
            <?php 
                $lateCount = \App\Models\WorkOrder::productionLate()->whereRaw('DATEDIFF(estimation_date, NOW()) <= 0')->count(); 
            ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lateCount > 0): ?>
                <span x-show="!sidebarCollapsed" class="ml-2 py-0.5 px-2 rounded-full text-xs font-bold bg-red-500 text-white shadow-sm">
                    <?php echo e($lateCount); ?>

                </span>
                <span x-show="sidebarCollapsed" class="absolute top-2 right-2 w-2.5 h-2.5 bg-red-500 border border-white rounded-full"></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Terlambat</span>
        </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->hasAccess('qc')): ?>
        <a href="<?php echo e(route('qc.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('qc.*') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-10 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3 flex-1 text-purple-400 font-bold">Quality Control</span>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($sidebarCounts['qc']) && $sidebarCounts['qc'] > 0): ?>
                <span x-show="!sidebarCollapsed" class="ml-2 py-0.5 px-2 rounded-full text-xs font-bold bg-purple-500 text-white shadow-sm">
                    <?php echo e($sidebarCounts['qc']); ?>

                </span>
                <span x-show="sidebarCollapsed" class="absolute top-2 right-2 w-2.5 h-2.5 bg-purple-500 border border-white rounded-full"></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">QC</span>
        </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->hasAccess('finish')): ?>
        <a href="<?php echo e(route('finish.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('finish.*') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3 flex-1 text-emerald-400 font-bold">Finish</span>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($sidebarCounts['finish']) && $sidebarCounts['finish'] > 0): ?>
                <span x-show="!sidebarCollapsed" class="ml-2 py-0.5 px-2 rounded-full text-xs font-bold bg-emerald-500 text-white shadow-sm">
                    <?php echo e($sidebarCounts['finish']); ?>

                </span>
                <span x-show="sidebarCollapsed" class="absolute top-2 right-2 w-2.5 h-2.5 bg-emerald-500 border border-white rounded-full"></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">G. Finish</span>
        </a>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->hasAccess('finish')): ?>
        <a href="<?php echo e(route('revision.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('revision.*') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0 text-red-400" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3 flex-1 text-red-400 font-bold">Revisi</span>
            
            <?php $revCount = \App\Models\WorkOrderRevision::where('status', 'OPEN')->count(); ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($revCount > 0): ?>
                <span x-show="!sidebarCollapsed" class="ml-2 py-0.5 px-2 rounded-full text-xs font-bold bg-red-500 text-white shadow-sm">
                    <?php echo e($revCount); ?>

                </span>
                <span x-show="sidebarCollapsed" class="absolute top-2 right-2 w-2.5 h-2.5 bg-red-500 border border-white rounded-full"></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Revisi</span>
        </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->hasAccess('finish')): ?>
        <a href="<?php echo e(route('garansi.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('garansi.*') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0 text-yellow-400" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04M12 21.48V11.5" />
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3 flex-1 text-yellow-400 font-bold">Garansi</span>
            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Garansi</span>
        </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <a href="<?php echo e(route('shipping.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('shipping.*') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3 flex-1 text-blue-400 font-bold">Pengiriman</span>
            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Pengiriman</span>
        </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('access-finance')): ?>
    <div x-data="{ 
            open: localStorage.getItem('sb_finance') === 'true' || <?php echo e(request()->routeIs('admin.finance.*') || request()->routeIs('finance.*') ? 'true' : 'false'); ?>,
            toggle() {
                this.open = !this.open;
                localStorage.setItem('sb_finance', this.open);
            }
         }" 
         class="mt-4 text-white">
        
        <button @click="toggle()" 
                type="button" 
                class="w-full flex items-center justify-between px-3 py-2.5 transition-all duration-300 group rounded-xl mb-1 active:scale-95 touch-manipulation"
                :class="open ? 'bg-white/15 text-white shadow-lg ring-1 ring-white/30 backdrop-blur-sm' : 'text-gray-400 hover:text-white hover:bg-white/5'"
                :title="sidebarCollapsed ? 'Divisi Finance' : ''">
            <div class="flex items-center gap-3">
                <!-- Finance Icon -->
                <svg class="w-5 h-5 flex-shrink-0 transition-transform duration-300" 
                     :class="{ 'text-yellow-400 scale-110 rotate-3': open, 'text-gray-400 group-hover:scale-110': !open }" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 x-show="!sidebarCollapsed" 
                    class="section-title mb-0 text-xs font-bold uppercase tracking-wider transition-colors"
                    :class="open ? 'text-yellow-100' : 'text-gray-400 group-hover:text-yellow-400'">Divisi Finance</h3>
            </div>
            <svg x-show="!sidebarCollapsed" :class="{ 'rotate-180': open }" class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <div x-show="open" x-collapse x-cloak class="space-y-1 mt-1 ml-4 border-l-2 border-white/10 pl-2">
        
        <a href="<?php echo e(route('finance.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('finance.index') || request()->routeIs('finance.show') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3">Finance Transaksi</span>
            
            <?php $financeCount = \App\Models\WorkOrder::where('status', 'WAITING_PAYMENT')->count(); ?>
            <span x-show="!sidebarCollapsed && <?php echo e($financeCount); ?> > 0" class="ml-auto bg-yellow-100 text-yellow-600 py-0.5 px-2 rounded-full text-xs font-bold"><?php echo e($financeCount); ?></span>
            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Transaksi</span>
        </a>

        <a href="<?php echo e(route('finance.invoices.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('finance.invoices.*') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3 text-blue-400 font-bold">Data Invoice</span>
            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Invoice</span>
        </a>

        
        <a href="<?php echo e(route('finance.payments.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('finance.payments.*') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3">Input Pembayaran</span>
            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Pembayaran</span>
        </a>

        
        <a href="<?php echo e(route('finance.mutations.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('finance.mutations.*') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3">Import Mutasi</span>
            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Mutasi</span>
        </a>

        
        <a href="<?php echo e(route('finance.verifications.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('finance.verifications.*') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3 text-purple-400 font-bold">Verifikasi Mutasi</span>
            <?php $unverifiedCount = \App\Models\InvoicePayment::where('verified', false)->count(); ?>
            <span x-show="!sidebarCollapsed && <?php echo e($unverifiedCount); ?> > 0" class="ml-auto bg-purple-100 text-purple-600 py-0.5 px-2 rounded-full text-xs font-bold"><?php echo e($unverifiedCount); ?></span>
            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Verifikasi</span>
        </a>
        </div>
    </div>
    <?php endif; ?>

    
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('access-cx')): ?>
    <div x-data="{ 
            open: localStorage.getItem('sb_cx') === 'true' || <?php echo e(request()->routeIs('cx.*') || request()->routeIs('whatsapp.*') ? 'true' : 'false'); ?>,
            toggle() {
                this.open = !this.open;
                localStorage.setItem('sb_cx', this.open);
            }
         }" 
         class="mt-4 text-white">
        
        <button @click="toggle()" 
                type="button" 
                class="w-full flex items-center justify-between px-3 py-2.5 transition-all duration-300 group rounded-xl mb-1 active:scale-95 touch-manipulation"
                :class="open ? 'bg-white/15 text-white shadow-lg ring-1 ring-white/30 backdrop-blur-sm' : 'text-gray-400 hover:text-white hover:bg-white/5'"
                :title="sidebarCollapsed ? 'Divisi CX' : ''">
            <div class="flex items-center gap-3">
                <!-- CX Icon -->
                <svg class="w-5 h-5 flex-shrink-0 transition-transform duration-300" 
                     :class="{ 'text-pink-400 scale-110 rotate-3': open, 'text-gray-400 group-hover:scale-110': !open }" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <h3 x-show="!sidebarCollapsed" 
                    class="section-title mb-0 text-xs font-bold uppercase tracking-wider transition-colors"
                    :class="open ? 'text-pink-100' : 'text-gray-400 group-hover:text-pink-400'">Divisi CX</h3>
            </div>
            <svg x-show="!sidebarCollapsed" :class="{ 'rotate-180': open }" class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <div x-show="open" x-collapse x-cloak class="space-y-1 mt-1 ml-4 border-l-2 border-white/10 pl-2">

        
        <a href="<?php echo e(route('cx.dashboard')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('cx.dashboard') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3">CX Dashboard</span>
            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Dashboard</span>
        </a>

        
        <a href="<?php echo e(route('cx.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('cx.index') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3">Follow Up</span>
            
            
            <?php $cxCount = \App\Models\WorkOrder::where('status', 'HOLD_FOR_CX')->orWhere('status', 'CX_FOLLOWUP')->count(); ?>
            <span x-show="!sidebarCollapsed && <?php echo e($cxCount); ?> > 0" class="ml-auto bg-red-100 text-red-600 py-0.5 px-2 rounded-full text-xs font-bold"><?php echo e($cxCount); ?></span>
            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">CX</span>
        </a>

        
        <a href="<?php echo e(route('cx.history')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('cx.history') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0 text-orange-400 group-hover:text-orange-300 transition-colors" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3 text-orange-400 font-bold tracking-tight">History Resolusi</span>
            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">History</span>
        </a>

        
        <a href="<?php echo e(route('cx.oto.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('cx.oto.*') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3">Kolam OTO (Upsell)</span>
            
            
            <?php $otoPending = \App\Models\OTO::where('status', 'PENDING_CX')->count(); ?>
            <span x-show="!sidebarCollapsed && <?php echo e($otoPending); ?> > 0" class="ml-auto bg-orange-100 text-orange-600 py-0.5 px-2 rounded-full text-xs font-bold"><?php echo e($otoPending); ?></span>
            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">OTO</span>
        </a>

        
        <a href="<?php echo e(route('cx.after-confirmation.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('cx.after-confirmation.*') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0 text-teal-400 group-hover:text-teal-300 transition-colors" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3 text-teal-400 font-bold tracking-tight">Konfirmasi After</span>
            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">After Service</span>
        </a>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->hasAccess('admin.complaints')): ?>
        <a href="<?php echo e(route('admin.complaints.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('admin.complaints.*') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3">Komplain</span>
            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Komplain</span>
        </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    
    <div x-data="{ 
            open: localStorage.getItem('sb_master') === 'true' || <?php echo e(request()->routeIs('admin.*') && !request()->routeIs('admin.supply-chain.*') ? 'true' : 'false'); ?>,
            toggle() {
                this.open = !this.open;
                localStorage.setItem('sb_master', this.open);
            }
         }" 
         class="mt-4 text-white">
        
        <button @click="toggle()" 
                type="button" 
                class="w-full flex items-center justify-between px-3 py-2.5 transition-all duration-300 group rounded-xl mb-1 active:scale-95 touch-manipulation"
                :class="open ? 'bg-white/15 text-white shadow-lg ring-1 ring-white/30 backdrop-blur-sm' : 'text-gray-400 hover:text-white hover:bg-white/5'"
                :title="sidebarCollapsed ? 'Master Data' : ''">
            <div class="flex items-center gap-3">
                <!-- Master Data Icon -->
                <svg class="w-5 h-5 flex-shrink-0 transition-transform duration-300" 
                     :class="{ 'text-purple-400 scale-110 rotate-3': open, 'text-gray-400 group-hover:scale-110': !open }" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                </svg>
                <h3 x-show="!sidebarCollapsed" 
                    class="section-title mb-0 text-xs font-bold uppercase tracking-wider transition-colors"
                    :class="open ? 'text-purple-100' : 'text-gray-400 group-hover:text-purple-400'">Master Data</h3>
            </div>
            <svg x-show="!sidebarCollapsed" :class="{ 'rotate-180': open }" class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <div x-show="open" x-collapse x-cloak class="space-y-1 mt-1 ml-4 border-l-2 border-white/10 pl-2">
            <div x-show="!sidebarCollapsed" class="section-divider my-2"></div>
        
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->hasAccess('admin.customers')): ?>
        <a href="<?php echo e(route('admin.customers.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('admin.customers.*') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3">Master Customer</span>
            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Customer</span>
        </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->hasAccess('admin.services')): ?>
        <a href="<?php echo e(route('admin.services.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('admin.services.*') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3">Manajemen Layanan</span>
            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Layanan</span>
        </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->hasAccess('admin.users')): ?>
        <a href="<?php echo e(route('admin.users.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('admin.users.*') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3">Pengguna</span>
            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Pengguna</span>
        </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->hasAccess('admin.reports')): ?>
        <a href="<?php echo e(route('admin.reports.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('admin.reports.*') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"/>
                <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"/>
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3">Laporan</span>
            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Laporan</span>
        </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->hasAccess('admin.performance')): ?>
        <a href="<?php echo e(route('admin.performance.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('admin.performance.*') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3">Performa</span>
            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Performa</span>
        </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->hasAccess('admin.system')): ?>
        <div x-show="!sidebarCollapsed" class="section-divider my-4"></div>
        <div x-show="sidebarCollapsed" class="my-4 border-t border-white/20"></div>

        <a href="<?php echo e(route('admin.data-integrity.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('admin.data-integrity.*') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative border border-indigo-500/30 bg-indigo-900/20 text-indigo-100 hover:bg-indigo-800"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0 text-indigo-400" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04M12 21.48V11.5" />
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3 font-bold">Kesehatan Data</span>
            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Data</span>
        </a>

        <a href="<?php echo e(route('admin.system.index')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('admin.system.*') ? 'active' : ''); ?> flex items-center px-3 py-3 rounded-lg group relative bg-red-900/30 text-red-100 hover:bg-red-800 mt-2"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="nav-icon flex-shrink-0 text-red-400" :class="sidebarCollapsed ? 'w-6 h-6' : 'w-5 h-5'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            <span x-show="!sidebarCollapsed" class="nav-item-text ml-3 font-bold">Pembersihan Sistem</span>
            <span x-show="sidebarCollapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">Reset</span>
        </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>


<div class="mt-auto border-t border-white/10 p-4 bg-black/10 backdrop-blur-md rounded-t-2xl mx-2 mb-2">
    <div x-show="!sidebarCollapsed" class="flex items-center gap-3">
        <div class="flex-shrink-0">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-teal-400 to-orange-400 flex items-center justify-center text-white font-bold shadow-lg ring-2 ring-white/10">
                <?php echo e(substr(Auth::user()->name, 0, 1)); ?>

            </div>
        </div>
        <div class="flex-1 min-w-0">
            <p class="truncate text-sm font-bold text-white mb-0 uppercase tracking-tight"><?php echo e(Auth::user()->name); ?></p>
            <p class="text-[10px] text-teal-200 truncate uppercase font-bold tracking-widest opacity-80"><?php echo e(Auth::user()->role); ?></p>
        </div>
        <form method="POST" action="<?php echo e(route('logout')); ?>" @submit.prevent="if(confirm('Logout dari sistem?')) $el.submit()">
            <?php echo csrf_field(); ?>
            <button type="submit" class="p-2 text-white/50 hover:text-white transition-colors rounded-lg hover:bg-white/10" title="Logout">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
            </button>
        </form>
    </div>
    
    <div x-show="sidebarCollapsed" class="flex flex-col items-center">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-teal-400 to-orange-400 flex items-center justify-center text-white font-bold shadow-lg ring-2 ring-white/10 mb-4">
            <?php echo e(substr(Auth::user()->name, 0, 1)); ?>

        </div>
        <form method="POST" action="<?php echo e(route('logout')); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit" class="p-3 text-red-400 hover:text-red-300 transition-colors rounded-xl bg-red-400/10 hover:bg-red-400/20" title="Logout">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
            </button>
        </form>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.getElementById('sidebar-nav-container');
        if (!sidebar) return;

        // 1. Restore scroll position
        const savedScroll = localStorage.getItem('sidebar-scroll');
        if (savedScroll) {
            // Use requestAnimationFrame for smoother jump
            requestAnimationFrame(() => {
                sidebar.scrollTop = savedScroll;
            });
        }

        // 2. Save scroll position on scroll (throttled for performance)
        let isSaving = false;
        sidebar.addEventListener('scroll', () => {
            if (!isSaving) {
                isSaving = true;
                requestAnimationFrame(() => {
                    localStorage.setItem('sidebar-scroll', sidebar.scrollTop);
                    isSaving = false;
                });
            }
        }, { passive: true });
    });
</script>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views/layouts/partials/sidebar-content.blade.php ENDPATH**/ ?>