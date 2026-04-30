<div class="h-full">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 flex flex-col h-full overflow-hidden">
        <div class="px-7 py-6 border-b border-gray-100 bg-gradient-to-r from-orange-50 to-orange-100/30">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 bg-orange-500 rounded-2xl flex items-center justify-center shadow-lg shadow-orange-200 ring-4 ring-white">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-gray-800 tracking-tight leading-none mb-1">Leaderboard Layanan</h3>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Specific Performance Ranking</p>
                    </div>
                </div>
                <div x-data="{ open: false }" class="relative inline-block text-left">
                    <button @click.stop="open = !open" class="text-orange-400 hover:text-orange-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition @click.away="open = false" 
                         class="absolute z-50 w-72 p-5 bg-white rounded-2xl shadow-2xl border border-gray-100 right-0 mt-3 whitespace-normal">
                        <div class="text-xs text-gray-600 font-medium leading-relaxed">
                            Peringkat jasa paling populer berdasarkan **Pendapatan Kotor** yang dihasilkan pada periode yang dipilih.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-1 px-6 py-4 overflow-y-auto custom-scrollbar" style="max-height: 320px; min-height: 320px">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($services) > 0): ?>
                <div class="space-y-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <div class="group relative">
                            <div class="flex items-center justify-between mb-1.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 flex items-center justify-center rounded-lg text-[11px] font-black <?php echo e($index < 3 ? 'bg-orange-500 text-white shadow-sm' : 'bg-gray-100 text-gray-400'); ?>">
                                        <?php echo e($index + 1); ?>

                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[13px] font-black text-gray-700 group-hover:text-orange-600 transition-colors leading-none mb-1">
                                            <?php echo e(Str::limit($service['name'], 25)); ?>

                                        </span>
                                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">
                                            <?php echo e($service['count']); ?> SPK 
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-[13px] font-black text-gray-800 tabular-nums">
                                        Rp <?php echo e(number_format($service['revenue'], 0, ',', '.')); ?>

                                    </span>
                                </div>
                            </div>
                            
                            
                            <div class="h-1.5 w-full bg-gray-50 rounded-full overflow-hidden border border-gray-100/30">
                                <div class="h-full bg-gradient-to-r <?php echo e($index < 3 ? 'from-orange-400 to-orange-500' : 'from-gray-300 to-gray-400'); ?> rounded-full transition-all duration-1000"
                                     style="width: <?php echo e($service['percentage']); ?>%">
                                </div>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            <?php else: ?>
                <div class="flex flex-col items-center justify-center h-full py-10 opacity-40">
                    <svg class="w-16 h-16 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <p class="text-sm font-bold text-gray-400">Belum ada data pengerjaan</p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="px-7 py-4 bg-gray-50/50 border-t border-gray-100">
            <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest text-gray-400">
                <span>Total 10 Teratas</span>
                <span class="text-orange-500">
                    Rp <?php echo e(number_format(collect($services)->sum('revenue'), 0, ',', '.')); ?>

                </span>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\livewire\workshop\widgets\top-service-names.blade.php ENDPATH**/ ?>