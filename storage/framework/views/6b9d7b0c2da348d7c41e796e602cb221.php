<div class="py-6 bg-gray-50" x-data="{ 
    activeTab: <?php if ((object) ('activeTab') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('activeTab'->value()); ?>')<?php echo e('activeTab'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('activeTab'); ?>')<?php endif; ?>,
    selectedItems: <?php if ((object) ('selectedItems') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('selectedItems'->value()); ?>')<?php echo e('selectedItems'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('selectedItems'); ?>')<?php endif; ?>,
    selectAll: <?php if ((object) ('selectAll') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('selectAll'->value()); ?>')<?php echo e('selectAll'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('selectAll'); ?>')<?php endif; ?>
}">
    
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-gradient-to-r from-teal-600 to-teal-700 p-6 rounded-2xl shadow-lg text-white">
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

            
            <div class="relative group w-full md:w-80">
                <input type="text" 
                       wire:model.live.debounce.300ms="search"
                       placeholder="Cari SPK / Pelanggan..." 
                       class="w-full pl-11 pr-4 py-2.5 bg-white/10 border border-white/30 rounded-xl text-sm font-bold text-white placeholder-white/60 focus:ring-2 focus:ring-white/50 focus:bg-white/20 transition-all outline-none">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
    <?php $__env->startPush('head'); ?>
        <?php echo app('Illuminate\Foundation\Vite')(['resources/js/preparation.js']); ?>
    <?php $__env->stopPush(); ?>
        
        <div wire:loading.flex class="fixed inset-0 z-[100] bg-gray-900/40 backdrop-blur-[2px] items-center justify-center transition-all duration-300">
            <div class="bg-white p-6 rounded-2xl shadow-2xl flex flex-col items-center border border-gray-100">
                <div class="relative w-12 h-12 mb-3">
                    <div class="absolute inset-0 rounded-full border-4 border-teal-50 border-t-teal-600 animate-spin"></div>
                </div>
                <p class="text-xs font-bold text-gray-800 animate-pulse uppercase tracking-widest">Loading...</p>
            </div>
        </div>

        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            
            <div wire:click="setTab('washing')"
                 class="group relative overflow-hidden rounded-2xl p-6 cursor-pointer transition-all duration-300 hover:scale-[1.02] <?php echo e($activeTab === 'washing' ? 'ring-4 ring-teal-400 ring-opacity-50' : 'opacity-80 grayscale-[20%] hover:grayscale-0'); ?>">
                <div class="absolute inset-0 bg-gradient-to-br from-teal-400 via-teal-500 to-teal-600"></div>
                <div class="absolute inset-0 backdrop-blur-sm bg-white/10"></div>
                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-3">
                        <div class="p-3 bg-white/20 rounded-xl backdrop-blur-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                            </svg>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'washing'): ?>
                            <span class="px-2 py-1 bg-white/30 backdrop-blur-md rounded-full text-[10px] font-bold text-white uppercase tracking-wider">Active</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="text-sm font-semibold text-white/90 uppercase tracking-wide mb-1">Washing</div>
                    <div class="text-4xl font-black text-white mb-1"><?php echo e($this->counts['washing']); ?></div>
                    <div class="text-xs text-white/80 font-medium">Orders in queue</div>
                </div>
            </div>

            
            <div wire:click="setTab('sol')"
                 class="group relative overflow-hidden rounded-2xl p-6 cursor-pointer transition-all duration-300 hover:scale-[1.02] <?php echo e($activeTab === 'sol' ? 'ring-4 ring-orange-400 ring-opacity-50' : 'opacity-80 grayscale-[20%] hover:grayscale-0'); ?>">
                <div class="absolute inset-0 bg-gradient-to-br from-orange-400 via-orange-500 to-orange-600"></div>
                <div class="absolute inset-0 backdrop-blur-sm bg-white/10"></div>
                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-3">
                        <div class="p-3 bg-white/20 rounded-xl backdrop-blur-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                            </svg>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'sol'): ?>
                            <span class="px-2 py-1 bg-white/30 backdrop-blur-md rounded-full text-[10px] font-bold text-white uppercase tracking-wider">Active</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="text-sm font-semibold text-white/90 uppercase tracking-wide mb-1">Sol Repair</div>
                    <div class="text-4xl font-black text-white mb-1"><?php echo e($this->counts['sol']); ?></div>
                    <div class="text-xs text-white/80 font-medium">Orders in queue</div>
                </div>
            </div>

            
            <div wire:click="setTab('upper')"
                 class="group relative overflow-hidden rounded-2xl p-6 cursor-pointer transition-all duration-300 hover:scale-[1.02] <?php echo e($activeTab === 'upper' ? 'ring-4 ring-purple-400 ring-opacity-50' : 'opacity-80 grayscale-[20%] hover:grayscale-0'); ?>">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-400 via-purple-500 to-purple-600"></div>
                <div class="absolute inset-0 backdrop-blur-sm bg-white/10"></div>
                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-3">
                        <div class="p-3 bg-white/20 rounded-xl backdrop-blur-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                            </svg>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'upper'): ?>
                            <span class="px-2 py-1 bg-white/30 backdrop-blur-md rounded-full text-[10px] font-bold text-white uppercase tracking-wider">Active</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="text-sm font-semibold text-white/90 uppercase tracking-wide mb-1">Upper & Repaint</div>
                    <div class="text-4xl font-black text-white mb-1"><?php echo e($this->counts['upper']); ?></div>
                    <div class="text-xs text-white/80 font-medium">Orders in queue</div>
                </div>
            </div>

            
            <div wire:click="setTab('review')"
                 class="group relative overflow-hidden rounded-2xl p-6 cursor-pointer transition-all duration-300 hover:scale-[1.02] <?php echo e($activeTab === 'review' ? 'ring-4 ring-blue-400 ring-opacity-50' : 'opacity-80 grayscale-[20%] hover:grayscale-0'); ?>">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-400 via-blue-500 to-blue-600"></div>
                <div class="absolute inset-0 backdrop-blur-sm bg-white/10"></div>
                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-3">
                        <div class="p-3 bg-white/20 rounded-xl backdrop-blur-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'review'): ?>
                            <span class="px-2 py-1 bg-white/30 backdrop-blur-md rounded-full text-[10px] font-bold text-white uppercase tracking-wider">Active</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="text-sm font-semibold text-white/90 uppercase tracking-wide mb-1">Review Admin</div>
                    <div class="text-4xl font-black text-white mb-1"><?php echo e($this->counts['review']); ?></div>
                    <div class="text-xs text-white/80 font-medium">Awaiting approval</div>
                </div>
            </div>
        </div>

        
        <div class="flex flex-wrap items-center justify-between gap-4 bg-white p-4 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center gap-3">
                <select wire:model.live="priority" class="text-xs font-bold border-2 border-gray-100 rounded-lg focus:ring-teal-500 focus:border-teal-500 shadow-sm">
                    <option value="all">Semua Prioritas</option>
                    <option value="urgent">Urgent / Express</option>
                    <option value="regular">Regular</option>
                </select>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab !== 'review'): ?>
                <select wire:model.live="technicianFilter" class="text-xs font-bold border-2 border-gray-100 rounded-lg focus:ring-teal-500 focus:border-teal-500 shadow-sm">
                    <option value="all">Semua Teknisi</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->techs[$activeTab] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <option value="<?php echo e($t->id); ?>"><?php echo e($t->name); ?></option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </select>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="flex items-center gap-2">
                <button wire:click="$set('sort', 'asc')" class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all <?php echo e($sort === 'asc' ? 'bg-teal-600 text-white shadow-md' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'); ?>">ID ↑</button>
                <button wire:click="$set('sort', 'desc')" class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all <?php echo e($sort === 'desc' ? 'bg-teal-600 text-white shadow-md' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'); ?>">ID ↓</button>
            </div>
        </div>

        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden min-h-[500px]">
            <?php
                $activeTabLabel = match($activeTab) {
                    'washing' => 'Washing & Cleaning',
                    'sol' => 'Bongkar Sol',
                    'upper' => 'Bongkar Upper',
                    'review' => 'Review Admin',
                    default => ''
                };
                $activeTabEmoji = match($activeTab) {
                    'washing' => '🧼',
                    'sol' => '👟',
                    'upper' => '🎨',
                    'review' => '👮',
                    default => ''
                };
                $activeTabColor = match($activeTab) {
                    'washing' => 'teal',
                    'sol' => 'orange',
                    'upper' => 'purple',
                    'review' => 'blue',
                    default => 'gray'
                };
            ?>
            
            <div class="p-4 border-b border-<?php echo e($activeTabColor); ?>-200 bg-gradient-to-r from-<?php echo e($activeTabColor); ?>-50 to-<?php echo e($activeTabColor); ?>-100 flex justify-between items-center">
                <h3 class="font-bold text-<?php echo e($activeTabColor); ?>-800 flex items-center gap-2">
                    <span><?php echo e($activeTabEmoji); ?> Station <?php echo e($activeTabLabel); ?></span>
                    <span class="px-2 py-0.5 bg-white rounded-full text-[10px] border border-<?php echo e($activeTabColor); ?>-200"><?php echo e($orders->total()); ?> items</span>
                </h3>
            </div>

            <div class="divide-y divide-gray-100">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <div <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-'.e($order->id).'-'.e($activeTab).''; ?>wire:key="order-<?php echo e($order->id); ?>-<?php echo e($activeTab); ?>">
                        <?php if (isset($component)) { $__componentOriginale671599c22350500c6881a76377982e5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale671599c22350500c6881a76377982e5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.station-card','data' => ['order' => $order,'type' => 'prep_'.$activeTab,'technicians' => $this->techs[$activeTab] ?? collect(),'loopIteration' => ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('station-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['order' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($order),'type' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('prep_'.$activeTab),'technicians' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($this->techs[$activeTab] ?? collect()),'loopIteration' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale671599c22350500c6881a76377982e5)): ?>
<?php $attributes = $__attributesOriginale671599c22350500c6881a76377982e5; ?>
<?php unset($__attributesOriginale671599c22350500c6881a76377982e5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale671599c22350500c6881a76377982e5)): ?>
<?php $component = $__componentOriginale671599c22350500c6881a76377982e5; ?>
<?php unset($__componentOriginale671599c22350500c6881a76377982e5); ?>
<?php endif; ?>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="p-12 text-center text-gray-400">
                        <span class="text-4xl block mb-2">✨</span>
                        <p>Tidak ada antrian di stasiun ini.</p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div class="p-4 bg-gray-50 border-t border-gray-100">
                <?php echo e($orders->links()); ?>

            </div>
        </div>

        
        <div x-show="selectedItems.length > 0" 
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
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Terpilih</span>
                        <span class="bg-gray-800 text-white px-2 py-0.5 rounded-md font-bold text-sm" x-text="selectedItems.length"></span>
                    </div>
                    <button @click="selectedItems = []; selectAll = false" class="text-[10px] font-bold text-red-500 hover:text-red-700 hover:bg-red-50 px-3 py-1.5 rounded-lg transition-colors uppercase tracking-widest">
                        Batal
                    </button>
                </div>

                <div class="h-8 w-px bg-gray-200 hidden md:block"></div>

                <div class="flex items-center gap-2 w-full md:w-auto overflow-x-auto pb-2 md:pb-0 scrollbar-hide justify-end">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab !== 'review'): ?>
                        <div class="flex items-center gap-2">
                            <div class="relative">
                                <select id="bulk-tech-<?php echo e($activeTab); ?>" class="appearance-none bg-white border border-gray-200 text-gray-700 text-[10px] rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-40 pl-3 pr-8 py-2.5 font-bold shadow-sm cursor-pointer hover:border-blue-300 transition-colors uppercase">
                                    <option value="">-- PILIH TEKNISI --</option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->techs[$activeTab] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                        <option value="<?php echo e($t->id); ?>"><?php echo e($t->name); ?></option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>

                            <button type="button" onclick="confirmBulkAction('assign', document.getElementById('bulk-tech-<?php echo e($activeTab); ?>').value)" 
                                    class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-4 py-2.5 rounded-lg text-[10px] font-black shadow hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center gap-2 active:scale-95 uppercase tracking-widest">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                Assign &amp; Mulai
                            </button>
                        </div>

                        <button type="button" onclick="confirmBulkAction('finish')" 
                                class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-lg text-[10px] font-black shadow hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center gap-2 active:scale-95 uppercase tracking-widest">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Selesai Semua
                        </button>
                    <?php else: ?>
                        <button type="button" onclick="confirmBulkAction('approve')" 
                                class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-xl text-xs font-black shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all flex items-center gap-2 active:scale-95 uppercase tracking-widest">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Approve &amp; Sortir Terpilih
                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
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

    
        <?php
        $__scriptKey = '1357732715-0';
        ob_start();
    ?>
    <script>
        // Global listener for report modal
        window.openReportModal = function(orderId) {
            window.dispatchEvent(new CustomEvent('open-report-modal', { detail: orderId }));
        };
        window.confirmBulkAction = (action, techId = null) => {
            const count = $wire.selectedItems.length;
            if (count === 0) return;

            const title = action === 'approve' ? `Setujui ${count} Order?` : `Proses ${count} Order?`;
            const text = action === 'approve' ? 'Semua order terpilih akan langsung dikirim ke Sortir.' : 'Proses status pengerjaan untuk order terpilih.';
            
            Swal.fire({
                title: title,
                text: text,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d9488',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.bulkAction(action, techId);
                }
            });
        }

        $wire.on('swal:toast', (event) => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                icon: event.icon,
                title: event.title
            });
        });

        window.confirmApprovePrep = (id) => {
             Swal.fire({
                title: 'Setujui Preparation?',
                text: "Order akan dipindahkan ke antrian Sortir.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Approve',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.performApprove(id);
                }
            });
        };

        window.updateStation = (id, type, action, techId = null, finishedAt = null) => {
            if (action === 'start') {
                const select = document.getElementById(`tech-${type}-${id}`);
                techId = select ? select.value : null;
                if (!techId) {
                    Swal.fire({ icon: 'warning', title: 'Pilih Teknisi', text: 'Silakan pilih teknisi persiapan terlebih dahulu.' });
                    return;
                }
            }
            $wire.updateStation(id, type, action, techId, finishedAt);
        };
    </script>
        <?php
        $__output = ob_get_clean();

        \Livewire\store($this)->push('scripts', $__output, $__scriptKey)
    ?>
</div>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views/livewire/preparation/prep-index.blade.php ENDPATH**/ ?>