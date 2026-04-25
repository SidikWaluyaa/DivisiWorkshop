
<section class="grid grid-cols-1 lg:grid-cols-2 gap-6 animate-fade-in-up delay-500">

    
    <div class="bg-white rounded-3xl p-6 shadow-xl border border-gray-100" x-data="{ tab: 'overdue' }">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-12 h-12 bg-gradient-to-br from-red-100 to-red-200 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div class="flex items-center justify-between w-full">
                <div>
                    <h3 class="text-lg font-black text-gray-900">Needs Attention</h3>
                    <p class="text-xs text-gray-400 font-medium">SPK yang membutuhkan tindakan</p>
                </div>
                <div x-data="{ open: false }" class="relative">
                    <button @click.stop="open = !open" class="p-1 text-gray-400 hover:text-gray-600 transition-colors cursor-pointer outline-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </button>
                    <div x-show="open" x-cloak x-transition @click.away="open = false" class="absolute z-[100] w-64 max-w-none p-4 bg-white rounded-2xl shadow-2xl border border-gray-100 right-0 bottom-full mb-3 whitespace-normal text-left">
                        <div class="absolute -bottom-1.5 right-1.5 w-3 h-3 bg-white border-b border-r border-gray-100 rotate-45"></div>
                        <div class="relative text-gray-800">
                            <div class="text-[10px] font-black text-red-500 uppercase tracking-widest mb-1">Maksud</div>
                            <div class="text-[12px] text-gray-700 leading-relaxed font-medium">Daftar SPK yang melewati estimasi (Overdue) atau tidak berprogres selama 48 jam (Stuck).</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-xl mb-4 w-fit">
            <button @click="tab='overdue'" :class="tab==='overdue' ? 'bg-white text-red-600 shadow-sm' : 'text-gray-400'" class="px-4 py-2 text-xs font-bold rounded-lg transition-all">
                🔥 Overdue (<span id="overdue-count"><?php echo e($urgentActions['overdue_spks']->count()); ?></span>)
            </button>
            <button @click="tab='stuck'" :class="tab==='stuck' ? 'bg-white text-orange-600 shadow-sm' : 'text-gray-400'" class="px-4 py-2 text-xs font-bold rounded-lg transition-all">
                ⏸️ Stuck (<span id="stuck-count"><?php echo e($urgentActions['stuck_spks']->count()); ?></span>)
            </button>
        </div>

        
        <div x-show="tab==='overdue'" id="overdue-list" class="space-y-2 max-h-[350px] overflow-y-auto">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $urgentActions['overdue_spks']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $spk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
            <a href="<?php echo e(route('reception.show', $spk->id)); ?>" class="flex items-center justify-between p-3.5 rounded-2xl bg-red-50/50 border border-red-100 hover:bg-red-50 hover:border-red-200 transition-all group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <div class="text-sm font-black text-gray-800 group-hover:text-red-600"><?php echo e($spk->spk_number); ?></div>
                        <div class="text-[10px] text-gray-400"><?php echo e($spk->customer_name); ?></div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs font-bold text-red-500"><?php echo e(\Carbon\Carbon::parse($spk->estimation_date)->diffForHumans()); ?></div>
                    <div class="text-[10px] text-gray-400"><?php echo e($spk->status); ?></div>
                </div>
            </a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div class="text-center py-8">
                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center mx-auto mb-2">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="text-gray-400 text-sm font-bold">Tidak ada SPK overdue</div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div x-show="tab==='stuck'" x-cloak id="stuck-list" class="space-y-2 max-h-[350px] overflow-y-auto">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $urgentActions['stuck_spks']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $spk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
            <a href="<?php echo e(route('reception.show', $spk->id)); ?>" class="flex items-center justify-between p-3.5 rounded-2xl bg-orange-50/50 border border-orange-100 hover:bg-orange-50 hover:border-orange-200 transition-all group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <div class="text-sm font-black text-gray-800 group-hover:text-orange-600"><?php echo e($spk->spk_number); ?></div>
                        <div class="text-[10px] text-gray-400"><?php echo e($spk->customer_name); ?></div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs font-bold text-orange-500">Update <?php echo e($spk->updated_at->diffForHumans()); ?></div>
                    <div class="text-[10px] text-gray-400"><?php echo e($spk->status); ?></div>
                </div>
            </a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div class="text-center py-8">
                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center mx-auto mb-2">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="text-gray-400 text-sm font-bold">Tidak ada SPK stuck</div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <div class="space-y-6">
        
        <div class="bg-white rounded-3xl p-6 shadow-xl border border-gray-100 <?php echo e($urgentActions['cx_overdue']->count() > 0 ? 'border-l-4 border-l-orange-400' : ''); ?>">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-orange-100 to-orange-200 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <h3 class="text-sm font-black text-gray-900">CX Issues > 3 Hari</h3>
                </div>
                <div x-data="{ open: false }" class="relative">
                    <button @click.stop="open = !open" class="p-1 text-gray-400 hover:text-gray-600 transition-colors cursor-pointer outline-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </button>
                    <div x-show="open" x-cloak x-transition @click.away="open = false" class="absolute z-[100] w-64 max-w-none p-4 bg-white rounded-2xl shadow-2xl border border-gray-100 right-0 bottom-full mb-3 whitespace-normal text-left">
                        <div class="absolute -bottom-1.5 right-1.5 w-3 h-3 bg-white border-b border-r border-gray-100 rotate-45"></div>
                        <div class="relative text-gray-800">
                            <div class="text-[10px] font-black text-orange-500 uppercase tracking-widest mb-1">Maksud</div>
                            <div class="text-[12px] text-gray-700 leading-relaxed font-medium">Komplain atau isu customer yang belum terselesaikan dalam waktu lebih dari 3 hari.</div>
                        </div>
                    </div>
                </div>
                <span id="cx-overdue-count" class="px-2.5 py-1 bg-orange-100 text-orange-600 rounded-lg text-xs font-black" <?php if($urgentActions['cx_overdue']->count() == 0): ?> style="display:none" <?php endif; ?>><?php echo e($urgentActions['cx_overdue']->count()); ?></span>
            </div>
            <div id="cx-overdue-list" class="space-y-2 max-h-[200px] overflow-y-auto">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $urgentActions['cx_overdue']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $issue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                <div class="flex items-center justify-between p-3 rounded-xl bg-orange-50/50 border border-orange-100">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-gray-700"><?php echo e($issue->workOrder->spk_number ?? $issue->spk_number ?? '-'); ?></div>
                            <div class="text-[9px] text-gray-400 truncate max-w-[150px]"><?php echo e($issue->category); ?></div>
                        </div>
                    </div>
                    <div class="text-[10px] font-bold text-orange-500"><?php echo e($issue->created_at->diffForHumans()); ?></div>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <div class="text-center py-4 text-gray-300 text-xs font-bold">✅ Semua isu CX terkendali</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        
        <div class="bg-white rounded-3xl p-6 shadow-xl border border-gray-100 <?php echo e($urgentActions['low_stock']->count() > 0 ? 'border-l-4 border-l-red-400' : ''); ?>">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-gradient-to-br from-red-100 to-red-200 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <div class="flex items-center justify-between w-full">
                    <h3 class="text-sm font-black text-gray-900">Stok Kritis</h3>
                    <div x-data="{ open: false }" class="relative">
                        <button @click.stop="open = !open" class="p-1 text-gray-400 hover:text-gray-600 transition-colors cursor-pointer outline-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </button>
                        <div x-show="open" x-cloak x-transition @click.away="open = false" class="absolute z-[100] w-64 max-w-none p-4 bg-white rounded-2xl shadow-2xl border border-gray-100 right-0 bottom-full mb-3 whitespace-normal text-left">
                            <div class="absolute -bottom-1.5 right-1.5 w-3 h-3 bg-white border-b border-r border-gray-100 rotate-45"></div>
                            <div class="relative text-gray-800">
                                <div class="text-[10px] font-black text-red-500 uppercase tracking-widest mb-1">Maksud</div>
                                <div class="text-[12px] text-gray-700 leading-relaxed font-medium">Material dengan stok di bawah ambang batas minimum yang harus segera dipesan ulang.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="low-stock-list" class="space-y-2 max-h-[150px] overflow-y-auto">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $urgentActions['low_stock']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                <div class="flex items-center justify-between p-3 rounded-xl bg-red-50/30 border border-red-100">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-gray-700"><?php echo e($material->name); ?></div>
                            <div class="text-[9px] text-gray-400">Min: <?php echo e($material->min_stock); ?> <?php echo e($material->unit); ?></div>
                        </div>
                    </div>
                    <div class="px-2 py-0.5 bg-red-100 text-red-600 rounded-md text-[10px] font-black">
                        Sisa: <?php echo e($material->stock); ?>

                    </div>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <div class="text-center py-4 text-gray-300 text-xs font-bold">✅ Semua stok aman</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        
        <div class="grid grid-cols-2 gap-4">
            <a href="<?php echo e(route('reception.index')); ?>" class="group bg-gradient-to-br from-teal-500 to-teal-600 p-4 rounded-3xl shadow-lg border border-teal-400/30 hover:shadow-teal-500/20 transition-all text-center">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform shadow-inner">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </div>
                <div class="text-xs font-black text-white">SPK Baru</div>
            </a>
            <a href="<?php echo e(route('admin.customers.create')); ?>" class="group bg-white p-4 rounded-3xl shadow-lg border border-gray-100 hover:shadow-xl transition-all text-center">
                <div class="w-10 h-10 bg-teal-50 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                </div>
                <div class="text-xs font-black text-gray-800">Customer</div>
            </a>
        </div>
    </div>
</section>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views/dashboard-v2/sections/urgent-actions.blade.php ENDPATH**/ ?>