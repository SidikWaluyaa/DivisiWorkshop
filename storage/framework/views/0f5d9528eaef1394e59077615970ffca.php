<div class="py-6 bg-gray-50 min-h-screen" x-data="{ selectedItems: <?php if ((object) ('selectedItems') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('selectedItems'->value()); ?>')<?php echo e('selectedItems'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('selectedItems'); ?>')<?php endif; ?> }">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            
            <button wire:click="setTab('jahit')"
                 class="group relative overflow-hidden rounded-2xl p-6 cursor-pointer transition-all duration-300 hover:scale-105 hover:shadow-2xl text-left"
                 :class="{ 'ring-4 ring-blue-400 ring-opacity-50': '<?php echo e($activeTab); ?>' === 'jahit' }">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-400 via-blue-500 to-blue-600 opacity-90 group-hover:opacity-100 transition-opacity"></div>
                <div class="absolute inset-0 backdrop-blur-sm bg-white/10"></div>
                <div class="relative z-10 text-white">
                    <div class="flex justify-between items-start mb-3">
                        <div class="p-3 bg-white/20 rounded-xl backdrop-blur-md">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'jahit'): ?>
                            <span class="px-3 py-1 bg-white/30 backdrop-blur-md rounded-full text-xs font-bold animate-pulse">Active</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <h3 class="font-black text-lg mb-1">QC Jahit</h3>
                    <p class="text-white/80 text-xs mb-3">Inspeksi jahitan & sol</p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-black"><?php echo e($this->counts['jahit']); ?></span>
                        <span class="text-white/70 text-sm font-medium">antrian</span>
                    </div>
                </div>
            </button>

            
            <button wire:click="setTab('cleanup')"
                 class="group relative overflow-hidden rounded-2xl p-6 cursor-pointer transition-all duration-300 hover:scale-105 hover:shadow-2xl text-left"
                 :class="{ 'ring-4 ring-teal-400 ring-opacity-50': '<?php echo e($activeTab); ?>' === 'cleanup' }">
                <div class="absolute inset-0 bg-gradient-to-br from-teal-400 via-teal-500 to-teal-600 opacity-90 group-hover:opacity-100 transition-opacity"></div>
                <div class="absolute inset-0 backdrop-blur-sm bg-white/10"></div>
                <div class="relative z-10 text-white">
                    <div class="flex justify-between items-start mb-3">
                        <div class="p-3 bg-white/20 rounded-xl backdrop-blur-md">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 15.536c-1.171 1.952-3.07 1.952-4.242 0-1.172-1.953-1.172-5.119 0-7.072 1.171-1.952 3.07-1.952 4.242 0M8 10.5h4m-4 3h4m9-1.5a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'cleanup'): ?>
                            <span class="px-3 py-1 bg-white/30 backdrop-blur-md rounded-full text-xs font-bold animate-pulse">Active</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <h3 class="font-black text-lg mb-1">QC Cleanup</h3>
                    <p class="text-white/80 text-xs mb-3">Pemeriksaan kebersihan</p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-black"><?php echo e($this->counts['cleanup']); ?></span>
                        <span class="text-white/70 text-sm font-medium">antrian</span>
                    </div>
                </div>
            </button>

            
            <button wire:click="setTab('final')"
                 class="group relative overflow-hidden rounded-2xl p-6 cursor-pointer transition-all duration-300 hover:scale-105 hover:shadow-2xl text-left"
                 :class="{ 'ring-4 ring-emerald-400 ring-opacity-50': '<?php echo e($activeTab); ?>' === 'final' }">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-400 via-emerald-500 to-emerald-600 opacity-90 group-hover:opacity-100 transition-opacity"></div>
                <div class="absolute inset-0 backdrop-blur-sm bg-white/10"></div>
                <div class="relative z-10 text-white">
                    <div class="flex justify-between items-start mb-3">
                        <div class="p-3 bg-white/20 rounded-xl backdrop-blur-md">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'final'): ?>
                            <span class="px-3 py-1 bg-white/30 backdrop-blur-md rounded-full text-xs font-bold animate-pulse">Active</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <h3 class="font-black text-lg mb-1">QC Final</h3>
                    <p class="text-white/80 text-xs mb-3">Verifikasi akhir produk</p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-black"><?php echo e($this->counts['final']); ?></span>
                        <span class="text-white/70 text-sm font-medium">antrian</span>
                    </div>
                </div>
            </button>

            
            <button wire:click="setTab('review')"
                 class="group relative overflow-hidden rounded-2xl p-6 cursor-pointer transition-all duration-300 hover:scale-105 hover:shadow-2xl text-left"
                 :class="{ 'ring-4 ring-indigo-400 ring-opacity-50': '<?php echo e($activeTab); ?>' === 'review' }">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 via-indigo-700 to-indigo-800 opacity-90 group-hover:opacity-100 transition-opacity"></div>
                <div class="absolute inset-0 backdrop-blur-sm bg-white/10"></div>
                <div class="relative z-10 text-white">
                    <div class="flex justify-between items-start mb-3">
                        <div class="p-3 bg-white/20 rounded-xl backdrop-blur-md">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'review'): ?>
                            <span class="px-3 py-1 bg-white/30 backdrop-blur-md rounded-full text-xs font-bold animate-pulse">Active</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <h3 class="font-black text-lg mb-1">Siap Selesai</h3>
                    <p class="text-white/80 text-xs mb-3">Pemeriksaan Admin Akhir</p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-black"><?php echo e($this->counts['review']); ?></span>
                        <span class="text-white/70 text-sm font-medium">order</span>
                    </div>
                </div>
            </button>
        </div>

        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 mb-6">
            <div class="flex flex-col md:flex-row items-center gap-4">
                
                <div class="relative flex-1 w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" 
                           wire:model.live.debounce.300ms="search"
                           class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-teal-500 focus:border-teal-500 bg-gray-50/50 font-medium transition-all" 
                           placeholder="Cari SPK, Customer, atau Brand...">
                </div>

                
                <div class="w-full md:w-48">
                    <select wire:model.live="priority" class="w-full py-2.5 pl-3 pr-10 border border-gray-200 rounded-xl text-xs font-bold uppercase tracking-wider focus:ring-teal-500 focus:border-teal-500 bg-gray-50/50">
                        <option value="all">⚡ Semua Prioritas</option>
                        <option value="urgent">🔴 PRIORITAS / URGENT</option>
                        <option value="regular">⚪ REGULER</option>
                    </select>
                </div>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab !== 'review'): ?>
                <div class="w-full md:w-56">
                    <select wire:model.live="technicianFilter" class="w-full py-2.5 pl-3 pr-10 border border-gray-200 rounded-xl text-xs font-bold uppercase tracking-wider focus:ring-teal-500 focus:border-teal-500 bg-gray-50/50">
                        <option value="all">👤 Semua Petugas</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->techs[$activeTab]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tech): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <option value="<?php echo e($tech->id); ?>"><?php echo e($tech->name); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <div class="w-full md:w-40">
                    <select wire:model.live="sort" class="w-full py-2.5 pl-3 pr-10 border border-gray-200 rounded-xl text-xs font-bold uppercase tracking-wider focus:ring-teal-500 focus:border-teal-500 bg-gray-50/50">
                        <option value="asc">📅 Terlama</option>
                        <option value="desc">🆕 Terbaru</option>
                    </select>
                </div>

                
                <button wire:click="$set('search', ''); $set('priority', 'all'); $set('technicianFilter', 'all'); $set('sort', 'asc')"
                        class="p-2.5 bg-gray-100 hover:bg-gray-200 text-gray-500 rounded-xl transition-all active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </button>
            </div>
        </div>

        
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden min-h-[500px] relative">
            
            
            <div wire:loading wire:target="setTab, search, priority, technicianFilter, sort, selectAll, selectedItems, nextBack, previousPage, gotoPage" 
                 class="absolute inset-0 bg-white/60 backdrop-blur-[2px] z-30 flex items-center justify-center transition-all duration-300">
                <div class="flex flex-col items-center bg-white p-6 rounded-2xl shadow-xl border border-gray-100">
                    <div class="w-12 h-12 border-4 border-teal-500 border-t-transparent rounded-full animate-spin"></div>
                    <div class="text-[10px] font-black text-teal-700 mt-4 tracking-widest uppercase">Verifikasi Data QC...</div>
                </div>
            </div>

            <?php
                $tabInfo = match($activeTab) {
                    'jahit' => ['color' => 'blue', 'label' => 'QC Jahit'],
                    'cleanup' => ['color' => 'teal', 'label' => 'QC Cleanup'],
                    'final' => ['color' => 'emerald', 'label' => 'QC Final'],
                    'review' => ['color' => 'indigo', 'label' => 'Menunggu Approval Admin'],
                };
            ?>

            <div class="p-4 bg-gradient-to-r from-<?php echo e($tabInfo['color']); ?>-50 to-<?php echo e($tabInfo['color']); ?>-100 border-b border-<?php echo e($tabInfo['color']); ?>-200 flex justify-between items-center">
                <h3 class="font-black text-<?php echo e($tabInfo['color']); ?>-800 flex items-center gap-2 uppercase tracking-tighter text-sm">
                    <span class="w-3 h-3 rounded-full bg-<?php echo e($tabInfo['color']); ?>-500 shadow-sm animate-pulse"></span> 
                    Daftar Antrian: <?php echo e($tabInfo['label']); ?>

                </h3>
                <div class="flex items-center gap-4">
                     <div class="flex items-center gap-2">
                          <input type="checkbox" id="select-all-top" wire:model.live="selectAll" class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500 transition-all cursor-pointer">
                          <label for="select-all-top" class="text-[10px] font-black text-<?php echo e($tabInfo['color']); ?>-700 cursor-pointer uppercase">Pilih Semua</label>
                     </div>
                </div>
            </div>

            <div class="divide-y divide-gray-100 bg-gray-50/30">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'review'): ?>
                        
                        <div <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'review-'.e($order->id).''; ?>wire:key="review-<?php echo e($order->id); ?>" class="p-4 bg-white border-b border-gray-100 hover:bg-indigo-50/30 transition-all group">
                             <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                                <div class="flex items-center gap-4 flex-1">
                                    <input type="checkbox" value="<?php echo e($order->id); ?>" wire:model.live="selectedItems" class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 transition-all cursor-pointer">
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="font-mono font-black text-gray-900"><?php echo e($order->spk_number); ?></span>
                                            <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded-md text-[10px] font-black uppercase"><?php echo e($order->priority); ?></span>
                                        </div>
                                        <div class="text-sm font-bold text-gray-700"><?php echo e($order->customer_name); ?></div>
                                        <div class="text-xs text-gray-500 font-medium"><?php echo e($order->shoe_brand); ?> - <?php echo e($order->shoe_type); ?></div>
                                    </div>
                                </div>

                                
                                <div class="flex flex-wrap gap-2 md:justify-center flex-1">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['jahit' => 'Jahit', 'cleanup' => 'Cleanup', 'final' => 'Final']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                        <?php 
                                            $completedAt = $order->{"qc_{$key}_completed_at"};
                                            $techName = $order->{"qc{$label}By"}->name ?? '-';
                                        ?>
                                        <div class="px-3 py-1.5 rounded-lg border <?php echo e($completedAt ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200'); ?> flex flex-col items-center min-w-[80px]">
                                            <span class="text-[9px] font-black uppercase <?php echo e($completedAt ? 'text-green-600' : 'text-gray-400'); ?>"><?php echo e($label); ?></span>
                                            <span class="text-[10px] font-bold text-gray-700 truncate w-full text-center"><?php echo e($techName); ?></span>
                                        </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>

                                
                                <div class="flex items-center gap-2">
                                    <button wire:click="performApprove(<?php echo e($order->id); ?>)" 
                                            wire:confirm="QC sudah OK semua? Order akan Finish."
                                            class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl font-black text-[10px] uppercase flex items-center gap-2 shadow-lg shadow-green-100 transition-all active:scale-95">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Approve & Finish
                                    </button>
                                    <button @click="$dispatch('open-revision-modal', { id: <?php echo e($order->id); ?>, number: '<?php echo e($order->spk_number); ?>' })"
                                            class="bg-white border-2 border-red-100 text-red-600 hover:bg-red-50 px-4 py-2.5 rounded-xl font-black text-[10px] uppercase transition-all">
                                        Revisi...
                                    </button>
                                </div>
                             </div>
                        </div>
                    <?php else: ?>
                        
                        <div class="p-4">
                            <?php if (isset($component)) { $__componentOriginale671599c22350500c6881a76377982e5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale671599c22350500c6881a76377982e5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.station-card','data' => ['wire:key' => 'card-'.e($activeTab).'-'.e($order->id).'','order' => $order,'type' => 'qc_' . $activeTab,'technicians' => $this->techs[$activeTab],'color' => $tabInfo['color']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('station-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:key' => 'card-'.e($activeTab).'-'.e($order->id).'','order' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($order),'type' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('qc_' . $activeTab),'technicians' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($this->techs[$activeTab]),'color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tabInfo['color'])]); ?>
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
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="p-20 text-center">
                        <div class="bg-gray-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                             <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <h4 class="text-lg font-black text-gray-400 uppercase tracking-widest">Antrian Kosong</h4>
                        <p class="text-sm text-gray-400 font-medium">Tidak ada data untuk filter ini.</p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($orders instanceof \Illuminate\Pagination\LengthAwarePaginator): ?>
                <div class="p-4 bg-white border-t border-gray-100">
                    <?php echo e($orders->links()); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <div x-show="selectedItems.length > 0" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="translate-y-full opacity-0"
         class="fixed bottom-6 inset-x-0 z-50 flex justify-center px-4"
         style="display: none;">
        
        <div class="bg-white/90 backdrop-blur-xl border border-indigo-100 shadow-2xl rounded-2xl p-4 w-full max-w-4xl flex flex-col md:flex-row items-center justify-between gap-4 ring-1 ring-black/5">
            
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 bg-indigo-600 px-4 py-2 rounded-xl shadow-lg shadow-indigo-200">
                    <span class="text-[10px] font-black text-white uppercase tracking-wider">Terpilih</span>
                    <span class="bg-white text-indigo-600 px-2.5 py-0.5 rounded-lg font-black text-sm" x-text="selectedItems.length"></span>
                </div>
                <button @click="selectedItems = []" class="text-[10px] font-black text-gray-400 hover:text-red-500 hover:bg-red-50 px-3 py-2 rounded-xl transition-all uppercase tracking-widest">
                    Batal
                </button>
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto overflow-x-auto justify-end">
                
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab !== 'review'): ?>
                <div class="flex items-center gap-2">
                    <div class="relative group">
                        <select id="bulk-tech-select-qc" class="appearance-none bg-gray-50 border border-gray-200 text-gray-700 text-[10px] rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block w-44 pl-3 pr-8 py-2.5 font-black uppercase tracking-wider cursor-pointer transition-all">
                            <option value="">-- PILIH PETUGAS --</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->techs[$activeTab]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tech): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <option value="<?php echo e($tech->id); ?>"><?php echo e($tech->name); ?></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                         <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400 group-hover:text-indigo-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>

                    <button type="button" 
                            onclick="window.bulkActionQc('assign')" 
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase shadow-lg shadow-indigo-100 transition-all flex items-center gap-2 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Assign
                    </button>
                </div>

                
                <button type="button" 
                        onclick="window.bulkActionQc('finish')" 
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase shadow-lg shadow-emerald-100 transition-all flex items-center gap-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Selesai
                </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'review'): ?>
                <button type="button" 
                        onclick="window.bulkActionQc('approve')" 
                        class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase shadow-lg shadow-green-100 transition-all flex items-center gap-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Approve All
                </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    
    <?php if (isset($component)) { $__componentOriginal0804d5970a1a6a5c4141ef9431f5394a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0804d5970a1a6a5c4141ef9431f5394a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.revision-modal','data' => ['currentStage' => 'QC']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('revision-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['currentStage' => 'QC']); ?>
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

    <script>
        window.bulkActionQc = (action) => {
            let techId = null;
            if (action === 'assign') {
                const select = document.getElementById('bulk-tech-select-qc');
                techId = select ? select.value : null;
                if (!techId) {
                    Swal.fire({ icon: 'warning', title: 'Pilih Petugas', text: 'Silakan pilih petugas QC terlebih dahulu.' });
                    return;
                }
            }

            Swal.fire({
                title: 'Konfirmasi Masal',
                text: `Proses item yang dipilih?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                confirmButtonText: 'Ya, Jalankan!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').bulkAction(action, techId);
                }
            });
        };

        window.updateStation = (id, type, action, finishedAt = null) => {
            let techId = null;
            if (action === 'start') {
                const select = document.getElementById(`tech-${type}-${id}`);
                techId = select ? select.value : null;
                if (!techId) {
                    Swal.fire({ icon: 'warning', title: 'Pilih Petugas', text: 'Silakan pilih petugas QC terlebih dahulu.' });
                    return;
                }
            }
            window.Livewire.find('<?php echo e($_instance->getId()); ?>').updateStation(id, type, action, techId, finishedAt);
        };
    </script>
</div>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views/livewire/qc/qc-index.blade.php ENDPATH**/ ?>