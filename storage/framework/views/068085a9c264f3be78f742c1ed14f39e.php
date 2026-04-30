<section class="section-gradient rounded-3xl p-8 animate-fade-in-up">
    <!-- Section Header -->
    <div class="flex items-center gap-4 mb-8">
        <div class="flex-shrink-0 w-12 h-12 rounded-2xl bg-[#22AF85] flex items-center justify-center shadow-lg shadow-[#22AF85]/30 section-icon-glow">
            <span class="text-2xl">📍</span>
        </div>
        <div class="flex-1">
            <h2 class="text-3xl font-black text-gray-900 tracking-tight">Live Operations</h2>
            <p class="text-sm text-gray-500 font-medium">Pantau posisi setiap sepatu di lantai produksi secara real-time</p>
        </div>
        <div class="hidden md:block flex-grow h-px section-divider"></div>
    </div>

    
    <div class="bg-white rounded-3xl p-8 shadow-xl shadow-gray-200/50 border border-gray-100 chart-card" x-data="{ activeLocation: null }">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
            <h3 class="text-2xl font-black text-gray-800 tracking-tight flex items-center gap-3">
                <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-orange-100 text-orange-600">📍</span>
                Live Workshop Flow
            </h3>
            <p class="text-gray-500 text-sm font-medium mt-1 ml-14">Pantau posisi setiap sepatu di lantai produksi secara real-time.</p>
        </div>
    </div>

    
    <div class="flex flex-wrap gap-3 mb-8">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location => $orders): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
            <?php
                $count = $orders->count();
                // Determine color based on location/status keywords
                $colorClass = 'gray'; // default
                $icon = '📍';

                if (str_contains($location, 'Penerimaan')) { $colorClass = 'blue'; $icon = '📥'; }
                elseif (str_contains($location, 'Preparation')) { $colorClass = 'cyan'; $icon = '🧼'; }
                elseif (str_contains($location, 'Sortir')) { $colorClass = 'indigo'; $icon = '📋'; }
                elseif (str_contains($location, 'Production')) { $colorClass = 'orange'; $icon = '🔨'; }
                elseif (str_contains($location, 'Jahit')) { $colorClass = 'orange'; $icon = '🧵'; }
                elseif (str_contains($location, 'Clean Up')) { $colorClass = 'teal'; $icon = '✨'; }
                elseif (str_contains($location, 'QC Akhir')) { $colorClass = 'green'; $icon = '✅'; }
                elseif (str_contains($location, 'Selesai')) { $colorClass = 'emerald'; $icon = '🛍️'; }
            ?>
            
            <button 
                @click="activeLocation = activeLocation === '<?php echo e($location); ?>' ? null : '<?php echo e($location); ?>'"
                :class="activeLocation === '<?php echo e($location); ?>' 
                    ? 'bg-<?php echo e($colorClass); ?>-600 text-white shadow-lg shadow-<?php echo e($colorClass); ?>-500/30 ring-2 ring-<?php echo e($colorClass); ?>-400 ring-offset-2' 
                    : 'bg-white text-gray-600 border border-gray-200 hover:border-<?php echo e($colorClass); ?>-400 hover:bg-<?php echo e($colorClass); ?>-50'"
                class="group relative flex items-center gap-3 px-5 py-3 rounded-2xl transition-all duration-200 ease-out">
                
                <span class="text-xl"><?php echo e($icon); ?></span>
                <div class="text-left">
                    <div class="text-[10px] uppercase font-bold tracking-wider opacity-70 leading-none mb-1 group-hover:text-<?php echo e($colorClass); ?>-600"
                         :class="activeLocation === '<?php echo e($location); ?>' ? 'text-<?php echo e($colorClass); ?>-100' : ''">
                        Lokasi
                    </div>
                    <div class="font-bold text-sm leading-none"><?php echo e($location); ?></div>
                </div>
                <span class="ml-2 flex items-center justify-center w-6 h-6 rounded-full text-xs font-black transition-colors"
                      :class="activeLocation === '<?php echo e($location); ?>' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-600 group-hover:bg-<?php echo e($colorClass); ?>-100 group-hover:text-<?php echo e($colorClass); ?>-700'">
                    <?php echo e($count); ?>

                </span>
                
                
                 <div x-show="activeLocation === '<?php echo e($location); ?>'" 
                      class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 w-4 h-4 bg-<?php echo e($colorClass); ?>-600 rotate-45 border-r border-b border-<?php echo e($colorClass); ?>-400"></div>
            </button>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
    
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location => $orders): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($orders->count() > 0): ?>
            <div x-show="activeLocation === '<?php echo e($location); ?>'" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-collapse
                 class="mb-6 bg-white rounded-2xl border border-gray-100 shadow-xl overflow-hidden relative z-10">
                
                
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h4 class="font-black text-gray-800 flex items-center gap-2">
                        <span>📂</span> Detail: <span class="text-teal-600"><?php echo e($location); ?></span>
                    </h4>
                    <span class="text-xs font-bold text-gray-400 uppercase"><?php echo e($orders->count()); ?> Sepatu dalam antrian</span>
                </div>
                
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-white text-gray-500 border-b border-gray-100">
                            <tr>
                                <th class="text-left py-4 px-6 font-bold uppercase text-xs tracking-wider">No SPK</th>
                                <th class="text-left py-4 px-6 font-bold uppercase text-xs tracking-wider">Pelanggan</th>
                                <th class="text-left py-4 px-6 font-bold uppercase text-xs tracking-wider">Merek</th>
                                <th class="text-center py-4 px-6 font-bold uppercase text-xs tracking-wider">Tanggal Masuk</th>
                                <th class="text-center py-4 px-6 font-bold uppercase text-xs tracking-wider">Estimasi</th>
                                <th class="text-center py-4 px-6 font-bold uppercase text-xs tracking-wider">Status System</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <tr class="hover:bg-teal-50/50 transition-colors group">
                                <td class="py-4 px-6">
                                    <a href="<?php echo e(route('reception.show', $order->id)); ?>" class="font-mono text-sm font-bold text-teal-600 hover:text-teal-800 hover:underline">
                                        <?php echo e($order->spk_number); ?>

                                    </a>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="font-bold text-gray-900"><?php echo e($order->customer_name); ?></div>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-gray-100 text-gray-800">
                                        <?php echo e($order->shoe_brand ?? '-'); ?>

                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="text-xs text-gray-500 font-medium font-mono">
                                        <?php echo e(\Carbon\Carbon::parse($order->entry_date)->format('d/m/Y')); ?>

                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <?php
                                        $estDates = \Carbon\Carbon::parse($order->estimation_date);
                                        $isOverdue = $estDates->isPast() && $order->status !== 'SELESAI';
                                        $isToday = $estDates->isToday();
                                    ?>
                                    <div class="flex items-center justify-center gap-1">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOverdue): ?>
                                            <span class="px-2 py-1 bg-red-50 text-red-600 rounded-md text-xs font-bold ring-1 ring-red-200">
                                                <?php echo e($estDates->format('d/m')); ?>!
                                            </span>
                                        <?php elseif($isToday): ?>
                                             <span class="px-2 py-1 bg-orange-50 text-orange-600 rounded-md text-xs font-bold ring-1 ring-orange-200">
                                                Hari Ini
                                            </span>
                                        <?php else: ?>
                                            <span class="text-gray-500 text-xs font-mono"><?php echo e($estDates->format('d/m')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="status-badge <?php echo e(in_array($order->status->value, ['PRODUCTION', 'ASSESSMENT', 'PREPARATION', 'SORTIR', 'QC']) ? 'orange' : 'teal'); ?> text-[10px]">
                                        <?php echo e($order->status->label()); ?>

                                    </span>
                                </td>
                            </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
</div>
</section>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\dashboard\partials\logistics.blade.php ENDPATH**/ ?>