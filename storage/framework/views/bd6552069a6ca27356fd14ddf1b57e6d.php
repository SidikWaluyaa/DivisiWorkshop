<section class="section-gradient rounded-3xl p-8 animate-fade-in-up">
    <!-- Section Header -->
    <div class="flex items-center gap-4 mb-8">
        <div class="flex-shrink-0 w-12 h-12 rounded-2xl bg-[#22AF85] flex items-center justify-center shadow-lg shadow-[#22AF85]/30 section-icon-glow">
            <span class="text-2xl">🏭</span>
        </div>
        <div class="flex-1">
            <h2 class="text-3xl font-black text-gray-900 tracking-tight">Operational Performance</h2>
            <p class="text-sm text-gray-500 font-medium">Performa teknisi, waktu proses, dan deadline mendatang</p>
        </div>
        <div class="hidden md:block flex-grow h-px section-divider"></div>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        
        <div class="dashboard-card shadow-xl shadow-gray-200/50">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">📊 Distribusi Status</h3>
            </div>
            <div class="dashboard-card-body">
                <div id="statusChart" class="min-h-[250px]"></div>
            </div>
        </div>

        
        <div class="dashboard-card shadow-xl shadow-gray-200/50">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">📈 Trend Order (Periode Ini)</h3>
            </div>
            <div class="dashboard-card-body">
                <div id="trendsChart" class="min-h-[250px]"></div>
            </div>
        </div>
    </div>

    
    <div class="bg-white border border-[#22AF85]/20 rounded-3xl p-8 mb-8 shadow-xl relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-[#22AF85] rounded-full mix-blend-multiply filter blur-3xl opacity-10 -mr-16 -mt-16 animate-pulse"></div>
        
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-black flex items-center gap-2 tracking-tight text-gray-900">
                    <span>🏆</span> Leaderboard Teknisi
                </h3>
            </div>

             <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($technicianPerformance->count() > 0): ?>
                <div x-data="{ activeTab: '<?php echo e($technicianPerformance->keys()->first()); ?>' }">
                    
                    <div class="flex space-x-1 mb-6 bg-gray-100 p-1 rounded-xl inline-flex">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $technicianPerformance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $spec => $techs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <button 
                                @click="activeTab = '<?php echo e($spec); ?>'"
                                :class="{ 'bg-[#22AF85] text-white shadow-lg shadow-[#22AF85]/30': activeTab === '<?php echo e($spec); ?>', 'text-gray-500 hover:bg-white hover:text-[#22AF85]': activeTab !== '<?php echo e($spec); ?>' }"
                                class="px-4 py-2 rounded-lg text-[10px] font-black transition-all duration-200 uppercase tracking-widest">
                                <?php echo e($spec); ?>

                            </button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $technicianPerformance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $spec => $techs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <div x-show="activeTab === '<?php echo e($spec); ?>'" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 translate-y-4"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $techs->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $tech): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <div class="bg-gray-50 border border-gray-100 rounded-2xl p-4 flex items-center gap-4 relative overflow-hidden group hover:border-[#22AF85]/30 transition-colors">
                                    <div class="text-4xl font-black opacity-10 absolute right-2 bottom-0 group-hover:scale-110 transition-transform text-gray-900">#<?php echo e($index + 1); ?></div>
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-xl font-bold
                                        <?php echo e($index === 0 ? 'bg-yellow-100 text-yellow-700 border border-yellow-200' : 
                                          ($index === 1 ? 'bg-gray-100 text-gray-700 border border-gray-200' : 
                                          ($index === 2 ? 'bg-orange-100 text-orange-700 border border-orange-200' : 'bg-gray-50 text-gray-400'))); ?>">
                                        <?php echo e(substr($tech['name'], 0, 1)); ?>

                                    </div>
                                    <div>
                                        <div class="font-bold text-lg leading-tight text-gray-900"><?php echo e($tech['name']); ?></div>
                                        <div class="text-[#22AF85] font-mono text-xs font-bold"><?php echo e($tech['count']); ?> Order Selesai</div>
                                    </div>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-8 text-gray-400 text-sm italic">Belum ada data performa teknisi.</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
         
         
        <div class="dashboard-card shadow-xl shadow-gray-200/50">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">⏳ Analisis Bottleneck (Rata-rata Durasi)</h3>
            </div>
            <div class="dashboard-card-body">
                <div id="bottleneckChart" class="min-h-[250px]"></div>
            </div>
        </div>

        
        <div class="dashboard-card shadow-xl shadow-gray-200/50">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">📅 Deadline Mendatang</h3>
            </div>
            <div class="dashboard-card-body space-y-4">
                <div class="flex items-center justify-between p-4 bg-red-50 rounded-2xl border border-red-100 hover:bg-red-100/50 transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">🔥</span>
                        <div>
                            <div class="font-black text-gray-800 uppercase text-[10px] tracking-widest">Hari Ini</div>
                            <div class="text-xs text-red-600 font-bold">Harus Segera Selesai</div>
                        </div>
                    </div>
                    <div class="text-4xl font-black text-red-600"><?php echo e($upcomingDeadlines['today']); ?></div>
                </div>
                
                <div class="flex items-center justify-between p-4 bg-orange-50 rounded-2xl border border-orange-100 hover:bg-orange-100/50 transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">⚡</span>
                        <div>
                            <div class="font-black text-gray-800 uppercase text-[10px] tracking-widest">Besok</div>
                            <div class="text-xs text-orange-600 font-bold">Antrian Prioritas</div>
                        </div>
                    </div>
                    <div class="text-4xl font-black text-orange-600"><?php echo e($upcomingDeadlines['tomorrow']); ?></div>
                </div>

                <div class="flex items-center justify-between p-4 bg-blue-50 rounded-2xl border border-blue-100 hover:bg-blue-100/50 transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">📅</span>
                        <div>
                            <div class="font-black text-gray-800 uppercase text-[10px] tracking-widest">Minggu Ini</div>
                            <div class="text-xs text-blue-600 font-bold">Volume Produksi</div>
                        </div>
                    </div>
                    <div class="text-4xl font-black text-blue-600"><?php echo e($upcomingDeadlines['thisWeek']); ?></div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\dashboard\partials\operational.blade.php ENDPATH**/ ?>