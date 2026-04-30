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

     <?php $__env->slot('header', null, []); ?> 
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <nav class="flex pb-1" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-xs font-medium text-gray-500">
                        <li><a href="<?php echo e(route('dashboard')); ?>" class="hover:text-[#22AF85] transition-colors">Dashboard</a></li>
                        <li><svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
                        <li><a href="<?php echo e(route('admin.supply-chain.index')); ?>" class="hover:text-[#22AF85] transition-colors">Supply Chain</a></li>
                        <li><svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
                        <li class="text-[#22AF85]">Audit Ledger</li>
                    </ol>
                </nav>
                <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight flex items-center gap-3">
                    <div class="p-2 bg-gray-900/5 rounded-lg">
                        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    Transaction <span class="text-[#22AF85]">Audit Ledger</span>
                </h2>
            </div>
            
            <a href="<?php echo e(route('admin.supply-chain.index')); ?>" 
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 hover:border-[#22AF85] transition-all shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Dashboard
            </a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6 bg-[#F9FAFB] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- Filter Bar -->
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <form action="<?php echo e(route('admin.supply-chain.transactions')); ?>" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    
                    <div class="space-y-1.5">
                        <label class="text-[10px] uppercase font-black text-gray-400 tracking-widest pl-1">Material Item</label>
                        <select name="material_id" class="w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-gray-700 focus:ring-[#22AF85] focus:border-[#22AF85]">
                            <option value="">All Materials</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <option value="<?php echo e($material->id); ?>" <?php echo e(request('material_id') == $material->id ? 'selected' : ''); ?>>
                                    <?php echo e($material->name); ?>

                                </option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                    </div>

                    
                    <div class="space-y-1.5">
                        <label class="text-[10px] uppercase font-black text-gray-400 tracking-widest pl-1">Mutation Type</label>
                        <select name="type" class="w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-gray-700 focus:ring-[#22AF85] focus:border-[#22AF85]">
                            <option value="">All Types</option>
                            <option value="IN" <?php echo e(request('type') == 'IN' ? 'selected' : ''); ?>>Stok Masuk (IN)</option>
                            <option value="OUT" <?php echo e(request('type') == 'OUT' ? 'selected' : ''); ?>>Stok Keluar (OUT)</option>
                        </select>
                    </div>

                    
                    <div class="space-y-1.5">
                        <label class="text-[10px] uppercase font-black text-gray-400 tracking-widest pl-1">Date From</label>
                        <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>" class="w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-gray-700 focus:ring-[#22AF85] focus:border-[#22AF85]">
                    </div>

                    
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 bg-[#22AF85] text-white font-extrabold py-2 px-4 rounded-xl hover:bg-[#1b8a69] transition-all shadow-md shadow-[#22AF85]/20">
                            Apply Filter
                        </button>
                        <a href="<?php echo e(route('admin.supply-chain.transactions')); ?>" class="p-2 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Ledger Table -->
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50/50 text-[10px] font-black text-gray-500 uppercase tracking-widest">
                            <tr>
                                <th class="px-6 py-4 border-b border-gray-100">Timestamp</th>
                                <th class="px-6 py-4 border-b border-gray-100">Item Details</th>
                                <th class="px-6 py-4 border-b border-gray-100 text-center">Type</th>
                                <th class="px-6 py-4 border-b border-gray-100">Quantity</th>
                                <th class="px-6 py-4 border-b border-gray-100">Operator</th>
                                <th class="px-6 py-4 border-b border-gray-100">Reference</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <tr class="hover:bg-gray-50/30 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-900"><?php echo e($tx->created_at->format('d M Y')); ?></div>
                                        <div class="text-[10px] text-gray-400 font-black uppercase"><?php echo e($tx->created_at->format('H:i')); ?> WIB</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-extrabold text-[#22AF85]"><?php echo e($tx->material->name); ?></div>
                                        <div class="text-[10px] text-gray-400 uppercase font-bold tracking-wider"><?php echo e($tx->material->category); ?></div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest <?php echo e($tx->type == 'IN' ? 'bg-[#22AF85]/10 text-[#22AF85] border border-[#22AF85]/20' : 'bg-red-50 text-red-600 border border-red-100'); ?>">
                                            <?php echo e($tx->type); ?>

                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-extrabold <?php echo e($tx->type == 'IN' ? 'text-[#22AF85]' : 'text-red-600'); ?>">
                                            <?php echo e($tx->type == 'IN' ? '+' : '-'); ?> <?php echo e(number_format($tx->quantity, 0)); ?>

                                            <span class="text-[10px] text-gray-400 font-bold ml-1"><?php echo e($tx->material->unit); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center text-[10px] font-black text-gray-500">
                                                <?php echo e(substr($tx->user->name, 0, 1)); ?>

                                            </div>
                                            <span class="text-sm font-bold text-gray-700"><?php echo e($tx->user->name); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tx->reference_spk): ?>
                                            <div class="inline-flex items-center gap-1.5 px-2 py-1 bg-[#FFC232]/10 rounded-lg border border-[#FFC232]/20">
                                                <svg class="w-3 h-3 text-[#FFC232]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                                <span class="text-xs font-black text-gray-800"><?php echo e($tx->reference_spk); ?></span>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-xs text-gray-400 italic">Manual Adj.</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-400">
                                            <svg class="w-16 h-16 mb-4 opacity-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                                            <p class="text-lg font-bold text-gray-300">No transactions recorded</p>
                                            <p class="text-xs italic">Try adjusting your filters</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($transactions->hasPages()): ?>
                    <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
                        <?php echo e($transactions->links()); ?>

                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
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
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\admin\supply-chain\transactions.blade.php ENDPATH**/ ?>