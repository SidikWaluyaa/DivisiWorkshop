
<section class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in-up delay-300">

    
    <div class="lg:col-span-2 bg-white rounded-3xl p-6 shadow-xl border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-orange-100 to-orange-200 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-black text-gray-900">Production Funnel</h3>
                    <p class="text-xs text-gray-400 font-medium">Live distribusi SPK per tahap produksi</p>
                </div>
            </div>
            <div class="px-3 py-1.5 bg-orange-50 rounded-lg border border-orange-100">
                <span class="text-[9px] font-black text-orange-500 uppercase tracking-widest">Live</span>
            </div>
        </div>
        <div id="productionFunnelChart" style="min-height: 320px;"></div>
    </div>

    
    <div class="bg-white rounded-3xl p-6 shadow-xl border border-gray-100">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
            </div>
            <div>
                <h3 class="text-lg font-black text-gray-900">Top Teknisi</h3>
                <p class="text-xs text-gray-400 font-medium">Leaderboard periode ini</p>
            </div>
        </div>

        <div id="technician-list" class="space-y-3">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $production['technicians']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $tech): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
            <div class="flex items-center gap-3 p-3.5 rounded-2xl <?php echo e($index === 0 ? 'bg-gradient-to-r from-[#FFC232]/10 to-[#FFC232]/5 border border-[#FFC232]/20' : 'bg-gray-50/50 border border-gray-100'); ?> hover:shadow-md transition-all group">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-black flex-shrink-0 shadow-sm
                    <?php echo e($index === 0 ? 'bg-gradient-to-br from-[#FFC232] to-orange-400 text-white' :
                       ($index === 1 ? 'bg-gradient-to-br from-gray-300 to-gray-400 text-white' :
                       ($index === 2 ? 'bg-gradient-to-br from-orange-300 to-orange-400 text-white' : 'bg-gray-100 text-gray-500'))); ?>">
                    <?php echo e($index < 3 ? ['🥇','🥈','🥉'][$index] : '#'.($index+1)); ?>

                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-sm text-gray-800 truncate group-hover:text-[#22AF85] transition-colors"><?php echo e($tech['name']); ?></div>
                    <div class="text-[10px] text-gray-400 font-medium"><?php echo e($tech['specialization']); ?></div>
                </div>
                <div class="text-right flex-shrink-0">
                    <div class="text-xl font-black text-[#22AF85]"><?php echo e($tech['count']); ?></div>
                    <div class="text-[8px] text-gray-400 font-bold uppercase tracking-wider">Jobs</div>
                </div>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <div class="text-gray-400 text-sm font-bold">Belum ada data</div>
                <div class="text-[10px] text-gray-300 mt-1">untuk periode ini</div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</section>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\dashboard-v2\sections\production.blade.php ENDPATH**/ ?>