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
                        <li class="text-[#22AF85]">Supply Chain</li>
                    </ol>
                </nav>
                <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight flex items-center gap-3">
                    <div class="p-2 bg-[#22AF85]/10 rounded-lg">
                        <svg class="w-6 h-6 text-[#22AF85]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    Supply Chain <span class="text-[#22AF85]">Control Center</span>
                </h2>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="<?php echo e(route('admin.supply-chain.transactions')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 hover:border-[#22AF85] transition-all shadow-sm group">
                    <svg class="w-4 h-4 mr-2 text-[#22AF85] group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Audit Ledger
                </a>
                <a href="<?php echo e(route('admin.materials.index')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-[#FFC232] text-gray-900 font-extrabold rounded-xl hover:bg-[#e6ae2d] transition-all shadow-md shadow-[#FFC232]/20">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Stock Management
                </a>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6 bg-[#F9FAFB] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- KPI Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-[0.08] transition-opacity">
                        <svg class="w-32 h-32 text-[#22AF85]" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 bg-[#22AF85]/10 rounded-xl">
                            <svg class="w-6 h-6 text-[#22AF85]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="text-gray-500 font-bold text-xs uppercase tracking-wider">Stock Valuation</span>
                    </div>
                    <h3 class="text-2xl font-extrabold text-gray-900">Rp <?php echo e(number_format($stats['total_valuation'], 0, ',', '.')); ?></h3>
                    <p class="text-[10px] text-gray-400 mt-1">Total current inventory asset value</p>
                </div>

                
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 bg-red-50 rounded-xl">
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <span class="text-gray-500 font-bold text-xs uppercase tracking-wider">Low Stock</span>
                    </div>
                    <h3 class="text-2xl font-extrabold text-red-600"><?php echo e($stats['low_stock_count']); ?> <span class="text-sm font-normal text-gray-400">Items</span></h3>
                    <p class="text-[10px] text-red-400 mt-1 font-medium">Critical attention required</p>
                </div>

                
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 bg-[#FFC232]/10 rounded-xl">
                            <svg class="w-6 h-6 text-[#FFC232]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <span class="text-gray-500 font-bold text-xs uppercase tracking-wider">Pending PO</span>
                    </div>
                    <h3 class="text-2xl font-extrabold text-gray-900"><?php echo e($stats['pending_requests']); ?> <span class="text-sm font-normal text-gray-400">Orders</span></h3>
                    <p class="text-[10px] text-gray-400 mt-1">Awaiting purchase approval</p>
                </div>

                
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 bg-gray-100 rounded-xl">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <span class="text-gray-500 font-bold text-xs uppercase tracking-wider">Total SKUs</span>
                    </div>
                    <h3 class="text-2xl font-extrabold text-gray-900"><?php echo e($stats['total_materials']); ?> <span class="text-sm font-normal text-gray-400">Types</span></h3>
                    <p class="text-[10px] text-gray-400 mt-1">Total materials in catalog</p>
                </div>
            </div>

            <!-- Main Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left: Consumption Pulse & Bottlenecks -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                        <div class="p-5 border-b border-gray-50 flex items-center justify-between">
                            <h3 class="font-extrabold text-gray-900 flex items-center gap-2">
                                <div class="w-1.5 h-6 bg-[#22AF85] rounded-full"></div>
                                Material Pulse <span class="text-xs font-normal text-gray-400 ml-1">(Last 30 Days)</span>
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-5">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $topConsumed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <div class="group">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center text-[#22AF85] font-black text-sm">
                                                    <?php echo e(substr($item->material->name, 0, 1)); ?>

                                                </div>
                                                <div>
                                                    <div class="text-sm font-extrabold text-gray-900 group-hover:text-[#22AF85] transition-colors"><?php echo e($item->material->name); ?></div>
                                                    <div class="text-[10px] text-gray-500 uppercase font-bold tracking-wider"><?php echo e($item->material->category); ?></div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-sm font-black text-[#22AF85]"><?php echo e(number_format($item->total_qty, 0)); ?> <?php echo e($item->material->unit); ?></div>
                                                <div class="text-[9px] text-gray-400 uppercase font-bold">Vol. Consumption</div>
                                            </div>
                                        </div>
                                        <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                            <?php 
                                                $max = $topConsumed->first()->total_qty ?: 1;
                                                $percent = ($item->total_qty / $max) * 100;
                                            ?>
                                            <div class="h-full bg-[#22AF85] transition-all duration-1000 shadow-sm" style="width: <?php echo e($percent); ?>%"></div>
                                        </div>
                                    </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    <div class="py-12 flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-12 h-12 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                                        <p class="italic text-sm">No consumption pulse detected</p>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>

                    
                    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                        <div class="p-5 border-b border-gray-50">
                            <h3 class="font-extrabold text-gray-900 flex items-center gap-2">
                                <div class="w-1.5 h-6 bg-red-500 rounded-full"></div>
                                Supply Chain Bottlenecks
                            </h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-gray-50/50 text-[10px] font-black text-gray-500 uppercase tracking-widest">
                                    <tr>
                                        <th class="px-6 py-4">SPK / Order</th>
                                        <th class="px-6 py-4">Missing Items</th>
                                        <th class="px-6 py-4">Status</th>
                                        <th class="px-6 py-4 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $waitingOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="text-[10px] font-black text-[#22AF85] mb-0.5"><?php echo e($order->spk_number); ?></div>
                                                <div class="text-sm font-bold text-gray-900"><?php echo e($order->customer_name); ?></div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex flex-wrap gap-1.5">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $order->materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-red-50 text-red-600 text-[10px] font-black border border-red-100">
                                                            <?php echo e($mat->name); ?> (<?php echo e($mat->pivot->quantity); ?>)
                                                        </span>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-[#FFC232]/10 text-gray-800 text-[10px] font-black border border-[#FFC232]/30 uppercase">
                                                    Waiting Supply
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <a href="<?php echo e(route('sortir.show', $order->id)); ?>" class="p-2 text-gray-400 hover:text-[#22AF85] transition-colors inline-block">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        <tr>
                                            <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                                <div class="flex flex-col items-center">
                                                    <span class="text-2xl mb-2">✅</span>
                                                    <p class="font-bold text-gray-900 text-sm italic">All orders are fully allocated. Logistics clear!</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Audit Trail -->
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden flex flex-col">
                    <div class="p-5 border-b border-gray-50 flex items-center justify-between">
                        <h3 class="font-extrabold text-gray-900 flex items-center gap-2">
                            <div class="w-1.5 h-6 bg-gray-900 rounded-full"></div>
                            Audit Ledger
                        </h3>
                        <a href="<?php echo e(route('admin.supply-chain.transactions')); ?>" class="text-[10px] font-black text-[#22AF85] uppercase tracking-widest hover:text-[#1b8a69] transition-colors flex items-center gap-1 group">
                            Full Log
                            <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                    
                    <div class="p-6 space-y-6 flex-1 overflow-y-auto max-h-[600px] scrollbar-thin scrollbar-thumb-gray-200">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $recentTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <div class="relative pl-7 pb-6 last:pb-0 group">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($loop->last)): ?>
                                    <div class="absolute left-[9px] top-6 bottom-0 w-[2px] bg-gray-100 group-hover:bg-[#22AF85]/20 transition-colors"></div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                
                                <div class="absolute left-0 top-1 w-5 h-5 rounded-full border-2 <?php echo e($tx->type == 'IN' ? 'border-[#22AF85] bg-[#22AF85]/10' : 'border-red-500 bg-red-50'); ?> flex items-center justify-center z-10">
                                    <div class="w-1.5 h-1.5 rounded-full <?php echo e($tx->type == 'IN' ? 'bg-[#22AF85]' : 'bg-red-500'); ?>"></div>
                                </div>
                                
                                <div class="bg-gray-50/50 rounded-xl p-3 border border-gray-100 group-hover:border-[#22AF85]/30 transition-all">
                                    <div class="flex justify-between items-start mb-1">
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-tighter"><?php echo e($tx->created_at->diffForHumans()); ?></span>
                                        <span class="text-[10px] font-black <?php echo e($tx->type == 'IN' ? 'text-[#22AF85]' : 'text-red-500'); ?> uppercase">
                                            <?php echo e($tx->type); ?>

                                        </span>
                                    </div>
                                    <div class="text-sm font-bold text-gray-900 line-clamp-1"><?php echo e($tx->material->name); ?></div>
                                    <div class="text-xs font-black <?php echo e($tx->type == 'IN' ? 'text-[#22AF85]' : 'text-red-600'); ?>">
                                        <?php echo e($tx->type == 'IN' ? '+' : '-'); ?> <?php echo e(number_format($tx->quantity, 0)); ?> <?php echo e($tx->material->unit); ?>

                                    </div>
                                    <div class="mt-2 pt-2 border-t border-gray-200/50 flex items-center gap-2">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tx->reference_spk): ?>
                                            <span class="text-[9px] font-black text-gray-500 bg-white border border-gray-100 px-1.5 py-0.5 rounded shadow-sm">SPK: <?php echo e($tx->reference_spk); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <span class="text-[10px] text-gray-400 font-medium">By <?php echo e($tx->user->name); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </div>
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
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\admin\supply-chain\index.blade.php ENDPATH**/ ?>