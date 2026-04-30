<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php $__env->startPush('head'); ?>
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
        <meta name="bulk-update-url" content="<?php echo e(route('preparation.bulk-update')); ?>">
        <?php echo app('Illuminate\Foundation\Vite')(['resources/js/preparation.js']); ?>
    <?php $__env->stopPush(); ?>

     <?php $__env->slot('header', null, []); ?> 
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm shadow-sm border border-white/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </div>
                
                <div class="flex flex-col">
                    <h2 class="font-bold text-xl leading-tight tracking-wide">
                        <?php echo e(__('Stasiun Persiapan')); ?>

                    </h2>
                    <div class="text-xs font-medium opacity-90">
                        Proses Cuci, Bongkar Sol, dan Bongkar Upper
                    </div>
                </div>
            </div>

            
            <form method="GET" action="<?php echo e(route('preparation.index')); ?>" class="relative">
                
                <input type="hidden" name="tab" value="<?php echo e(request('tab', 'washing')); ?>">
                
                <input type="text" 
                       name="search" 
                       value="<?php echo e(request('search')); ?>"
                       placeholder="Cari SPK / Customer..." 
                       style="color: #000000 !important; background-color: #ffffff !important;"
                       class="pl-9 pr-4 py-1.5 text-sm !text-gray-900 !bg-white border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 shadow-sm w-48 transition-all focus:w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </form>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6 bg-gray-50" x-data="{ activeTab: '<?php echo e($activeTab); ?>', showFilters: false }" data-active-tab="<?php echo e($activeTab); ?>">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" x-show="showFilters" x-transition style="display: none;">
                <div class="p-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            Advanced Filters
                        </h3>
                        <button @click="showFilters = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    
                    <form method="GET" action="<?php echo e(route('preparation.index')); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <input type="hidden" name="tab" value="<?php echo e(request('tab', 'washing')); ?>">
                        
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-2 uppercase tracking-wide">Priority</label>
                            <select name="priority" class="w-full text-sm border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-sm">
                                <option value="">All Priorities</option>
                                <option value="Urgent" <?php echo e(request('priority') == 'Urgent' ? 'selected' : ''); ?>>Urgent</option>
                                <option value="Prioritas" <?php echo e(request('priority') == 'Prioritas' ? 'selected' : ''); ?>>Prioritas</option>
                                <option value="Express" <?php echo e(request('priority') == 'Express' ? 'selected' : ''); ?>>Express</option>
                                <option value="Regular" <?php echo e(request('priority') == 'Regular' ? 'selected' : ''); ?>>Regular</option>
                            </select>
                        </div>
                        
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-2 uppercase tracking-wide">Date From</label>
                            <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>" class="w-full text-sm border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-sm">
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-2 uppercase tracking-wide">Date To</label>
                            <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>" class="w-full text-sm border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-sm">
                        </div>
                        
                        
                        <div class="flex items-end gap-2">
                            <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-gradient-to-r from-teal-600 to-teal-700 hover:from-teal-700 hover:to-teal-800 text-white rounded-lg text-sm font-bold shadow-md hover:shadow-lg transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Apply
                            </button>
                            <a href="<?php echo e(route('preparation.index', ['tab' => request('tab', 'washing')])); ?>" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-bold transition-colors">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            
            <div class="flex justify-between items-center gap-4">
                
                <button @click="showFilters = !showFilters" class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-gray-50 border-2 border-gray-200 rounded-lg text-sm font-bold text-gray-700 shadow-sm transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    <span x-text="showFilters ? 'Hide Filters' : 'Show Filters'">Show Filters</span>
                </button>

                
                <div class="flex items-center gap-3">
                    
                    <div x-data="{ autoRefresh: false, countdown: 30 }" class="flex items-center gap-2">
                        <button @click="autoRefresh = !autoRefresh; if(autoRefresh) startAutoRefresh()" 
                                :class="autoRefresh ? 'bg-green-100 text-green-700 border-green-300' : 'bg-white text-gray-700 border-gray-200'"
                                class="inline-flex items-center gap-2 px-3 py-2 border-2 rounded-lg text-xs font-bold shadow-sm transition-all hover:shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span x-show="!autoRefresh">Auto Refresh</span>
                            <span x-show="autoRefresh" x-text="countdown + 's'"></span>
                        </button>
                        
                        <script>
                            function startAutoRefresh() {
                                let countdown = 30;
                                const interval = setInterval(() => {
                                    countdown--;
                                    if (countdown <= 0) {
                                        window.location.reload();
                                    }
                                    // Update countdown display
                                    const el = document.querySelector('[x-data] [x-text*="countdown"]');
                                    if (el) el.textContent = countdown + 's';
                                }, 1000);
                            }
                        </script>
                    </div>

                    
                    <button onclick="window.location.reload()" 
                            class="inline-flex items-center gap-2 px-3 py-2 bg-white hover:bg-gray-50 border-2 border-gray-200 rounded-lg text-xs font-bold text-gray-700 shadow-sm transition-all hover:shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Refresh
                    </button>
                </div>
            </div>
            
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                
                <a href="<?php echo e(route('preparation.index', ['tab' => 'washing'])); ?>"
                     class="group relative overflow-hidden rounded-2xl p-6 cursor-pointer transition-all duration-300 hover:scale-105 hover:shadow-2xl"
                     :class="{ 'ring-4 ring-teal-400 ring-opacity-50': '<?php echo e($activeTab); ?>' === 'washing' }">
                    
                    <div class="absolute inset-0 bg-gradient-to-br from-teal-400 via-teal-500 to-teal-600 opacity-90 group-hover:opacity-100 transition-opacity"></div>
                    
                    
                    <div class="absolute inset-0 backdrop-blur-sm bg-white/10"></div>
                    
                    
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-3">
                            <div class="p-3 bg-white/20 rounded-xl backdrop-blur-md">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                </svg>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if('<?php echo e($activeTab); ?>' === 'washing'): ?>
                                <span class="px-2 py-1 bg-white/30 backdrop-blur-md rounded-full text-[10px] font-bold text-white uppercase tracking-wider">Active</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="text-sm font-semibold text-white/90 uppercase tracking-wide mb-1">Washing</div>
                        <div class="text-4xl font-black text-white mb-1 animate-pulse"><?php echo e($counts['washing']); ?></div>
                        <div class="text-xs text-white/80 font-medium">Orders in queue</div>
                    </div>
                    
                    
                    <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                </a>

                
                <a href="<?php echo e(route('preparation.index', ['tab' => 'sol'])); ?>" 
                     class="group relative overflow-hidden rounded-2xl p-6 cursor-pointer transition-all duration-300 hover:scale-105 hover:shadow-2xl"
                     :class="{ 'ring-4 ring-orange-400 ring-opacity-50': '<?php echo e($activeTab); ?>' === 'sol' }">
                    <div class="absolute inset-0 bg-gradient-to-br from-orange-400 via-orange-500 to-orange-600 opacity-90 group-hover:opacity-100 transition-opacity"></div>
                    <div class="absolute inset-0 backdrop-blur-sm bg-white/10"></div>
                    
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-3">
                            <div class="p-3 bg-white/20 rounded-xl backdrop-blur-md">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                                </svg>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if('<?php echo e($activeTab); ?>' === 'sol'): ?>
                                <span class="px-2 py-1 bg-white/30 backdrop-blur-md rounded-full text-[10px] font-bold text-white uppercase tracking-wider">Active</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="text-sm font-semibold text-white/90 uppercase tracking-wide mb-1">Sol Repair</div>
                        <div class="text-4xl font-black text-white mb-1 animate-pulse"><?php echo e($counts['sol']); ?></div>
                        <div class="text-xs text-white/80 font-medium">Orders in queue</div>
                    </div>
                    
                    <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                </a>

                
                <a href="<?php echo e(route('preparation.index', ['tab' => 'upper'])); ?>" 
                     class="group relative overflow-hidden rounded-2xl p-6 cursor-pointer transition-all duration-300 hover:scale-105 hover:shadow-2xl"
                     :class="{ 'ring-4 ring-purple-400 ring-opacity-50': '<?php echo e($activeTab); ?>' === 'upper' }">
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-400 via-purple-500 to-purple-600 opacity-90 group-hover:opacity-100 transition-opacity"></div>
                    <div class="absolute inset-0 backdrop-blur-sm bg-white/10"></div>
                    
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-3">
                            <div class="p-3 bg-white/20 rounded-xl backdrop-blur-md">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                </svg>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if('<?php echo e($activeTab); ?>' === 'upper'): ?>
                                <span class="px-2 py-1 bg-white/30 backdrop-blur-md rounded-full text-[10px] font-bold text-white uppercase tracking-wider">Active</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="text-sm font-semibold text-white/90 uppercase tracking-wide mb-1">Upper & Repaint</div>
                        <div class="text-4xl font-black text-white mb-1 animate-pulse"><?php echo e($counts['upper']); ?></div>
                        <div class="text-xs text-white/80 font-medium">Orders in queue</div>
                    </div>
                    
                    <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                </a>

                
                <a href="<?php echo e(route('preparation.index', ['tab' => 'review'])); ?>" 
                     class="group relative overflow-hidden rounded-2xl p-6 cursor-pointer transition-all duration-300 hover:scale-105 hover:shadow-2xl"
                     :class="{ 'ring-4 ring-blue-400 ring-opacity-50': '<?php echo e($activeTab); ?>' === 'review' }">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-400 via-blue-500 to-blue-600 opacity-90 group-hover:opacity-100 transition-opacity"></div>
                    <div class="absolute inset-0 backdrop-blur-sm bg-white/10"></div>
                    
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-3">
                            <div class="p-3 bg-white/20 rounded-xl backdrop-blur-md">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if('<?php echo e($activeTab); ?>' === 'review'): ?>
                                <span class="px-2 py-1 bg-white/30 backdrop-blur-md rounded-full text-[10px] font-bold text-white uppercase tracking-wider">Active</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="text-sm font-semibold text-white/90 uppercase tracking-wide mb-1">Review</div>
                        <div class="text-4xl font-black text-white mb-1 animate-pulse"><?php echo e($counts['review']); ?></div>
                        <div class="text-xs text-white/80 font-medium">Awaiting approval</div>
                    </div>
                    
                    <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                </a>
            </div>

            
            <?php if (isset($component)) { $__componentOriginal4512bd0ee22c27040f58fab150ec153e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4512bd0ee22c27040f58fab150ec153e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.workshop-filter-bar','data' => ['technicians' => match($activeTab) {
                    'washing' => $techWashing,
                    'sol' => $techSol,
                    'upper' => $techUpper,
                    default => collect([])
                }]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('workshop-filter-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['technicians' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(match($activeTab) {
                    'washing' => $techWashing,
                    'sol' => $techSol,
                    'upper' => $techUpper,
                    default => collect([])
                })]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4512bd0ee22c27040f58fab150ec153e)): ?>
<?php $attributes = $__attributesOriginal4512bd0ee22c27040f58fab150ec153e; ?>
<?php unset($__attributesOriginal4512bd0ee22c27040f58fab150ec153e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4512bd0ee22c27040f58fab150ec153e)): ?>
<?php $component = $__componentOriginal4512bd0ee22c27040f58fab150ec153e; ?>
<?php unset($__componentOriginal4512bd0ee22c27040f58fab150ec153e); ?>
<?php endif; ?>

            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden min-h-[500px]">
                
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'washing'): ?>
                <div>
                    <div class="p-4 border-b border-teal-200 bg-gradient-to-r from-teal-50 to-teal-100 flex justify-between items-center">
                        <h3 class="font-bold text-teal-800 flex items-center gap-2">
                            <span>🧼 Station Washing & Cleaning</span>
                            <span class="px-2 py-0.5 bg-white rounded-full text-xs border border-teal-200"><?php echo e($orders->total()); ?> items</span>
                        </h3>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($orders->count() > 0): ?>
                        <div class="divide-y divide-gray-100">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <?php echo $__env->make('preparation.partials.station-card', [
                                    'order' => $order,
                                    'type' => 'washing',
                                    'technicians' => $techWashing,
                                    'techByRelation' => 'prepWashingBy',
                                    'startedAtColumn' => 'prep_washing_started_at',
                                    'byColumn' => 'prep_washing_by',
                                    'showCheckbox' => true,
                                    'loopIteration' => ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration
                                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="p-12 text-center text-gray-400">
                            <span class="text-4xl block mb-2">✨</span>
                            <p>Tidak ada antrian cuci.</p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'sol'): ?>
                <div>
                    <div class="p-4 border-b border-orange-200 bg-gradient-to-r from-orange-50 to-orange-100 flex justify-between items-center">
                        <h3 class="font-bold text-orange-800 flex items-center gap-2">
                            <span>👟 Station Bongkar Sol</span>
                            <span class="px-2 py-0.5 bg-white rounded-full text-xs border border-orange-200"><?php echo e($orders->total()); ?> item</span>
                        </h3>
                    </div>
                     <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($orders->count() > 0): ?>
                        <div class="divide-y divide-gray-100">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <?php echo $__env->make('preparation.partials.station-card', [
                                    'order' => $order,
                                    'type' => 'sol',
                                    'technicians' => $techSol,
                                    'techByRelation' => 'prepSolBy',
                                    'startedAtColumn' => 'prep_sol_started_at',
                                    'byColumn' => 'prep_sol_by',
                                    'showCheckbox' => true,
                                    'loopIteration' => ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration
                                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="p-12 text-center text-gray-400">
                            <span class="text-4xl block mb-2">✅</span>
                            <p>Antrian Bongkar Sol kosong.</p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'upper'): ?>
                <div>
                    <div class="p-4 border-b border-purple-200 bg-gradient-to-r from-purple-50 to-purple-100 flex justify-between items-center">
                        <h3 class="font-bold text-purple-800 flex items-center gap-2">
                            <span>🎨 Station Bongkar Upper & Repaint</span>
                            <span class="px-2 py-0.5 bg-white rounded-full text-xs border border-purple-200"><?php echo e($orders->total()); ?> items</span>
                        </h3>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($orders->count() > 0): ?>
                        <div class="divide-y divide-gray-100">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <?php echo $__env->make('preparation.partials.station-card', [
                                    'order' => $order,
                                    'type' => 'upper',
                                    'technicians' => $techUpper,
                                    'techByRelation' => 'prepUpperBy',
                                    'startedAtColumn' => 'prep_upper_started_at',
                                    'byColumn' => 'prep_upper_by',
                                    'showCheckbox' => true,
                                    'loopIteration' => ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration
                                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="p-12 text-center text-gray-400">
                            <span class="text-4xl block mb-2">✅</span>
                            <p>Antrian Bongkar Upper kosong.</p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'review'): ?>
                <div class="mb-6 bg-white overflow-hidden">
                     <div class="bg-gradient-to-r from-orange-500 to-red-500 p-4 text-white flex justify-between items-center">
                        <h3 class="font-bold flex items-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Menunggu Pemeriksaan Admin (Preparation Selesai)
                        </h3>
                        <span class="bg-white/20 px-3 py-1 rounded-full text-xs font-bold"><?php echo e($orders->total()); ?> Order</span>
                    </div>
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($orders->count() > 0): ?>
                    <div class="overflow-x-auto -mx-4 sm:mx-0">
                        <table class="min-w-full w-full text-sm text-left">
                            <thead class="bg-gray-50 uppercase text-xs font-bold text-gray-600">
                                <tr>
                                    <th class="px-4 py-3">
                                        <input type="checkbox" @click="toggleAll($event)" class="rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                    </th>
                                    <th class="px-4 py-3">No</th>
                                    <th class="px-4 py-3">SPK</th>
                                    <th class="px-6 py-3">SPK</th>
                                    <th class="px-6 py-3">Item</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3 text-center">Aksi (Admin)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <tr id="row-<?php echo e($order->spk_number); ?>" 
                                    x-init="
                                        const urlParams = new URLSearchParams(window.location.search);
                                        if (urlParams.get('highlight') === '<?php echo e($order->spk_number); ?>') {
                                            setTimeout(() => {
                                                $el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                                $el.classList.add('bg-yellow-100', 'ring-2', 'ring-yellow-400', 'dark:bg-yellow-900/40');
                                                setTimeout(() => { $el.classList.remove('bg-yellow-100', 'ring-2', 'ring-yellow-400', 'dark:bg-yellow-900/40'); }, 3000);
                                            }, 400);
                                        }
                                    "
                                    class="hover:bg-gray-50 transition-colors duration-500">
                                    <td class="px-6 py-4">
                                        <input type="checkbox" value="<?php echo e($order->id); ?>" 
                                               @change="$store.preparation.toggle('<?php echo e($order->id); ?>')"
                                               :checked="$store.preparation.includes('<?php echo e($order->id); ?>')"
                                               class="wo-checkbox rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                    </td>
                                    <td class="px-6 py-4 font-bold text-gray-500"><?php echo e(($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration); ?></td>
                                    <td class="px-6 py-4 font-bold font-mono"><?php echo e($order->spk_number); ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($order->priority, ['Prioritas', 'Urgent', 'Express'])): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200 shadow-sm">
                                                PRIORITAS
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                                REGULER
                                            </span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold"><?php echo e($order->shoe_brand); ?></div>
                                        <div class="text-xs text-gray-500"><?php echo e($order->customer_name); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-2 text-xs">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->prep_washing_completed_at): ?>
                                                <div class="flex items-start gap-2">
                                                    <span class="text-green-600 font-bold min-w-[50px]">✔ Wash:</span>
                                                    <div>
                                                        <div class="font-medium text-gray-700"><?php echo e($order->prepWashingBy->name ?? 'System'); ?></div>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->prep_washing_started_at): ?>
                                                             <div class="text-[10px] text-gray-500">
                                                                <?php echo e($order->prep_washing_started_at->format('H:i')); ?> - <?php echo e($order->prep_washing_completed_at->format('H:i')); ?> 
                                                                <span class="font-bold text-teal-600">(<?php echo e($order->prep_washing_started_at->diffInMinutes($order->prep_washing_completed_at)); ?> mnt)</span>
                                                            </div>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->needs_sol): ?>
                                                <div class="flex items-start gap-2">
                                                    <span class="text-green-600 font-bold min-w-[50px]">✔ Sol:</span>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->prep_sol_completed_at): ?>
                                                        <div>
                                                            <div class="font-medium text-gray-700"><?php echo e($order->prepSolBy->name ?? 'System'); ?></div>
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->prep_sol_started_at): ?>
                                                                <div class="text-[10px] text-gray-500">
                                                                    <?php echo e($order->prep_sol_started_at->format('H:i')); ?> - <?php echo e($order->prep_sol_completed_at->format('H:i')); ?> 
                                                                    <span class="font-bold text-teal-600">(<?php echo e($order->prep_sol_started_at->diffInMinutes($order->prep_sol_completed_at)); ?> mnt)</span>
                                                                </div>
                                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="text-gray-400 italic"> - </span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->needs_upper): ?>
                                                <div class="flex items-start gap-2">
                                                    <span class="text-green-600 font-bold min-w-[50px]">✔ Upper:</span>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->prep_upper_completed_at): ?>
                                                        <div>
                                                            <div class="font-medium text-gray-700"><?php echo e($order->prepUpperBy->name ?? 'System'); ?></div>
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->prep_upper_started_at): ?>
                                                                <div class="text-[10px] text-gray-500">
                                                                    <?php echo e($order->prep_upper_started_at->format('H:i')); ?> - <?php echo e($order->prep_upper_completed_at->format('H:i')); ?> 
                                                                    <span class="font-bold text-teal-600">(<?php echo e($order->prep_upper_started_at->diffInMinutes($order->prep_upper_completed_at)); ?> mnt)</span>
                                                                </div>
                                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="text-gray-400 italic"> - </span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center items-center gap-2">
                                            <!-- Approve -->
                                            <form id="approve-form-<?php echo e($order->id); ?>" action="<?php echo e(route('preparation.approve', $order->id)); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <button type="button" onclick="confirmApprove(<?php echo e($order->id); ?>)" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-bold text-xs flex items-center gap-1 shadow hover:shadow-lg transition-all">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    Approve & Sortir
                                                </button>
                                            </form>
                                            
                                            <!-- Follow Up Button -->
                                            <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-report-modal', { detail: <?php echo e($order->id); ?> }))" class="bg-amber-100 hover:bg-amber-200 text-amber-700 px-3 py-2 rounded-lg font-bold text-xs flex items-center gap-1 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                Lapor/Follow Up
                                            </button>
                                            
                                            <!-- Revision Modal Trigger -->
                                            <button @click="$dispatch('open-revision-modal', { id: <?php echo e($order->id); ?>, number: '<?php echo e($order->spk_number); ?>' })" 
                                                    type="button" class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-2 rounded-lg font-bold text-xs flex items-center gap-1 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                Revisi...
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                     <?php else: ?>
                        <div class="p-12 text-center text-gray-400">
                             <p>Tidak ada order yang menunggu review.</p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                
                
                 <div class="p-4 border-t border-gray-100 bg-gray-50">
                    <?php echo e($orders->links()); ?>

                </div>
            </div>

            
            <?php if (isset($component)) { $__componentOriginal0804d5970a1a6a5c4141ef9431f5394a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0804d5970a1a6a5c4141ef9431f5394a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.revision-modal','data' => ['currentStage' => 'PREPARATION']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('revision-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['currentStage' => 'PREPARATION']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0804d5970a1a6a5c4141ef9431f5394a)): ?>
<?php $attributes = $__attributesOriginal0804d5970a1a6a5c4141ef9431f5394a; ?>
<?php unset($__attributesOriginal0804d5970a1a6a5c4141ef9431f5394a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0804d5970a1a6a5c4141ef9431f5394a)): ?>
<?php $component = $__componentOriginal0804d5970a1a6a5c4141ef9431f5394a; ?>
<?php unset($__componentOriginal0804d5970a1a6a5c4141ef9431f5394a); ?>
<?php endif; ?>

            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex gap-4 items-start">
                <span class="text-2xl">💡</span>
                <div class="text-sm text-blue-800">
                    <strong>Panduan Stasiun:</strong>
                    <ul class="list-disc ml-4 mt-1 space-y-1">
                        <li>Gunakan tab <strong>Washing</strong>, <strong>Sol</strong>, dan <strong>Upper</strong> untuk melihat antrian spesifik.</li>
                        <li>Klik tombol <strong>"SELESAI"</strong> pada setiap baris untuk menandai bahwa tahapan tersebut sudah beres.</li>
                        <li>Sistem otomatis mencatat nama Anda sebagai teknisi yang mengerjakan.</li>
                        <li>Jika semua tahap selesai, tombol <strong>"Kirim ke Sortir"</strong> akan muncul di tab "Semua Order".</li>
                    </ul>
                </div>
            </div>

        </div>

    
    <div x-show="$store.preparation.count() > 0" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full opacity-0 scale-95"
         x-transition:enter-end="translate-y-0 opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0 opacity-100 scale-100"
         x-transition:leave-end="translate-y-full opacity-0 scale-95"
         class="fixed bottom-6 inset-x-0 z-50 flex justify-center px-4"
         style="display: none;">
        
        <div class="bg-white/90 backdrop-blur-md border border-gray-200 shadow-2xl rounded-2xl p-4 w-full max-w-4xl flex flex-col md:flex-row items-center justify-between gap-4 ring-1 ring-black/5">
            
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 bg-gray-100 px-3 py-1.5 rounded-lg">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Terpilih</span>
                    <span class="bg-gray-800 text-white px-2 py-0.5 rounded-md font-bold text-sm" x-text="$store.preparation.count()"></span>
                </div>
                <button @click="$store.preparation.clear()" class="text-xs font-bold text-red-500 hover:text-red-700 hover:bg-red-50 px-3 py-1.5 rounded-lg transition-colors">
                    Batal
                </button>
            </div>

            <div class="h-8 w-px bg-gray-200 hidden md:block"></div>

            <div class="flex items-center gap-2 w-full md:w-auto overflow-x-auto pb-2 md:pb-0 scrollbar-hide justify-end">
                
                
                <div class="flex items-center gap-2">
                    <div class="relative group">
                        <select id="bulk-tech-select" class="appearance-none bg-white border border-gray-200 text-gray-700 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-40 pl-3 pr-8 py-2.5 font-bold shadow-sm cursor-pointer hover:border-blue-300 transition-colors">
                            <option value="">-- PILIH TEKNISI --</option>
                            <optgroup label="Washing">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $techWashing; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?> <option value="<?php echo e($t->id); ?>">Wash: <?php echo e($t->name); ?></option> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </optgroup>
                            <optgroup label="Sol">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $techSol; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?> <option value="<?php echo e($t->id); ?>">Sol: <?php echo e($t->name); ?></option> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </optgroup>
                            <optgroup label="Upper">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $techUpper; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?> <option value="<?php echo e($t->id); ?>">Upper: <?php echo e($t->name); ?></option> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </optgroup>
                        </select>
                         <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400 group-hover:text-blue-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>

                    <button type="button" @click="window.bulkAction('assign')" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-4 py-2.5 rounded-lg text-xs font-bold shadow hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center gap-2 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Assign &amp; Mulai Semua
                    </button>
                </div>

                

                
                <button type="button" @click="window.bulkAction('finish')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-lg text-xs font-bold shadow hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center gap-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Selesai
                </button>
                
                
                <button type="button" @click="window.bulkAction('approve')" x-show="activeTab === 'review'" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-lg text-xs font-bold shadow hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center gap-2 active:scale-95" style="display: none;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Approve &amp; Sortir
                </button>
            </div>
        </div>
    </div>
    </div>

    
    
    <?php if (isset($component)) { $__componentOriginalb8b4040615cb2503377c02c51985f6ba = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb8b4040615cb2503377c02c51985f6ba = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.report-modal','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('report-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb8b4040615cb2503377c02c51985f6ba)): ?>
<?php $attributes = $__attributesOriginalb8b4040615cb2503377c02c51985f6ba; ?>
<?php unset($__attributesOriginalb8b4040615cb2503377c02c51985f6ba); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb8b4040615cb2503377c02c51985f6ba)): ?>
<?php $component = $__componentOriginalb8b4040615cb2503377c02c51985f6ba; ?>
<?php unset($__componentOriginalb8b4040615cb2503377c02c51985f6ba); ?>
<?php endif; ?>
    
    
    <script>
        // Override global function to dispatch Alpine event
        window.openReportModal = function(orderId) {
            window.dispatchEvent(new CustomEvent('open-report-modal', { detail: orderId }));
        }
    </script>
    
    
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>


<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\preparation\index.blade.php ENDPATH**/ ?>