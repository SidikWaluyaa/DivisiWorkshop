
<section class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in-up delay-400">

    
    <div class="lg:col-span-2 bg-white rounded-3xl p-6 shadow-xl border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-teal-100 to-teal-200 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-black text-gray-900">Revenue Trend</h3>
                    <p class="text-xs text-gray-400 font-medium">Analisis pendapatan per hari</p>
                </div>
            </div>
            <div class="px-4 py-2 bg-[#22AF85]/5 rounded-xl border border-[#22AF85]/10 text-right">
                <div class="text-[9px] text-[#22AF85] font-bold uppercase tracking-widest">Total Periode</div>
                <div class="text-xl font-black text-gray-800" id="revenue-total">Rp <?php echo e(number_format($businessIntel['revenue']['total'] / 1000, 0, ',', '.')); ?>rb</div>
            </div>
        </div>
        <div id="revenueChart" style="min-height: 300px;"></div>
    </div>

    
    <div class="space-y-6">
        
        <div class="bg-white rounded-3xl p-6 shadow-xl border border-gray-100">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-gradient-to-br from-purple-100 to-purple-200 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider">Top Masalah CX</h3>
            </div>
            <div id="top-issues-list" class="space-y-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $businessIntel['topIssues']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $issue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                <?php
                    $maxCount = $businessIntel['topIssues']->max('count');
                    $pct = $maxCount > 0 ? ($issue->count / $maxCount) * 100 : 0;
                ?>
                <div>
                    <div class="flex justify-between text-xs font-bold mb-1.5">
                        <span class="text-gray-600 truncate max-w-[160px]"><?php echo e($issue->category ?? 'Uncategorized'); ?></span>
                        <span class="text-[#22AF85] font-black"><?php echo e($issue->count); ?></span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2.5">
                        <div class="bg-gradient-to-r from-[#22AF85] to-teal-400 h-2.5 rounded-full transition-all" style="width: <?php echo e($pct); ?>%"></div>
                    </div>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <div class="text-center py-8">
                    <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center mx-auto mb-2">
                        <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="text-gray-300 text-xs font-bold">Tidak ada masalah CX</div>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        
        <div class="bg-white rounded-3xl p-6 shadow-xl border border-gray-100 <?php echo e($businessIntel['complaints']['overdue'] > 0 ? 'border-l-4 border-l-red-400' : ''); ?>">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-gradient-to-br from-red-100 to-red-200 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider">Keluhan</h3>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div class="text-center p-3 bg-orange-50 rounded-xl border border-orange-100">
                    <div class="text-2xl font-black text-orange-500" id="complaint-pending"><?php echo e($businessIntel['complaints']['pending']); ?></div>
                    <div class="text-[8px] text-orange-400 font-bold uppercase tracking-wider mt-1">Pending</div>
                </div>
                <div class="text-center p-3 bg-blue-50 rounded-xl border border-blue-100">
                    <div class="text-2xl font-black text-blue-500" id="complaint-process"><?php echo e($businessIntel['complaints']['process']); ?></div>
                    <div class="text-[8px] text-blue-400 font-bold uppercase tracking-wider mt-1">Proses</div>
                </div>
                <div class="text-center p-3 <?php echo e($businessIntel['complaints']['overdue'] > 0 ? 'bg-red-50 border border-red-200 urgent-glow' : 'bg-green-50 border border-green-100'); ?> rounded-xl">
                    <div class="text-2xl font-black <?php echo e($businessIntel['complaints']['overdue'] > 0 ? 'text-red-500' : 'text-green-500'); ?>" id="complaint-overdue"><?php echo e($businessIntel['complaints']['overdue']); ?></div>
                    <div class="text-[8px] <?php echo e($businessIntel['complaints']['overdue'] > 0 ? 'text-red-400' : 'text-green-400'); ?> font-bold uppercase tracking-wider mt-1">Overdue</div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\dashboard-v2\sections\business-intel.blade.php ENDPATH**/ ?>