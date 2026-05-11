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
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white shadow-lg" style="background-color: #22AF85">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <h2 class="font-black text-2xl text-white leading-tight tracking-tight uppercase">
                    <?php echo e($selectedCs ? __('Individual KPI: ' . $selectedCs->name) : __('CS Performance Analytics')); ?>

                </h2>
                <p class="text-xs font-bold text-teal-100 tracking-widest uppercase opacity-70">
                    <?php echo e($selectedCs ? __('Laporan Performa Akun CS') : __('Laporan Konversi & KPI CS Group')); ?>

                </p>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-10 bg-gray-50/50 dark:bg-gray-900/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            
            
            
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-wider">Periode Laporan</h3>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Gunakan filter untuk merubah rentang data</p>
                </div>
                
                <form action="<?php echo e(route('cs.analytics')); ?>" method="GET" class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center bg-gray-50 dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 px-4 py-2.5">
                        <div class="flex flex-col">
                            <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Start Date</span>
                            <input type="date" name="start_date" value="<?php echo e(request('start_date', now()->startOfMonth()->format('Y-m-d'))); ?>" 
                                   class="bg-transparent border-none text-xs font-black text-gray-900 dark:text-white focus:ring-0 p-0">
                        </div>
                        <span class="mx-4 text-gray-300 font-bold">/</span>
                        <div class="flex flex-col">
                            <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-0.5">End Date</span>
                            <input type="date" name="end_date" value="<?php echo e(request('end_date', now()->format('Y-m-d'))); ?>" 
                                   class="bg-transparent border-none text-xs font-black text-gray-900 dark:text-white focus:ring-0 p-0">
                        </div>
                    </div>

                    <div class="flex items-center bg-gray-50 dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 px-4 py-2.5">
                        <div class="flex flex-col w-full min-w-[150px]">
                            <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Filter Akun CS</span>
                            <select name="cs_id" class="bg-transparent border-none text-xs font-black text-gray-900 dark:text-white focus:ring-0 p-0 appearance-none">
                                <option value="" class="text-gray-900">📊 Keseluruhan (Global)</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $csUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <option value="<?php echo e($user->id); ?>" <?php echo e(request('cs_id') == $user->id ? 'selected' : ''); ?> class="text-gray-900">
                                        👤 <?php echo e($user->name); ?>

                                    </option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </select>
                        </div>
                    </div>
                    
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-2xl hover:bg-indigo-700 transition shadow-lg shadow-indigo-100 dark:shadow-none flex items-center gap-2 font-black text-xs uppercase tracking-widest">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Update
                    </button>
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->anyFilled(['start_date', 'end_date'])): ?>
                        <a href="<?php echo e(route('cs.analytics')); ?>" class="p-3 bg-gray-100 dark:bg-gray-700 text-gray-500 rounded-2xl hover:bg-gray-200 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </form>
            </div>

            
            
            
            <div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-2 h-8 bg-[#22AF85] rounded-full"></div>
                    <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">
                        <?php echo e($selectedCs ? 'Performance: ' . $selectedCs->name : 'Global Overview Metrics'); ?>

                    </h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    
                    <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-xl p-7 border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:shadow-2xl transition-shadow">
                        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-[#22AF85] to-emerald-400"></div>
                        <div class="relative z-10">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.25em] mb-2">Total Lead Intake</p>
                            <h3 class="text-4xl font-black text-gray-900 dark:text-white leading-none tracking-tighter"><?php echo e(number_format($overview['total_leads'])); ?></h3>
                            <p class="mt-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest bg-gray-50 dark:bg-gray-900 inline-block px-3 py-1.5 rounded-full">Input periode ini</p>
                        </div>
                    </div>

                    
                    <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-xl p-7 border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:shadow-2xl transition-shadow">
                        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-[#FFC232] to-amber-400"></div>
                        <div class="relative z-10">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.25em] mb-2">Total Closing</p>
                            <h3 class="text-4xl font-black text-gray-900 dark:text-white leading-none tracking-tighter"><?php echo e(number_format($overview['total_closings'])); ?></h3>
                            <div class="mt-4 flex items-center gap-3">
                                <div class="h-2 w-full bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden shadow-inner">
                                    <div class="h-full bg-[#FFC232] rounded-full shadow-lg transition-all duration-1000" style="width: <?php echo e($overview['conversion_rate']); ?>%"></div>
                                </div>
                                <span class="text-sm font-black text-[#FFC232] whitespace-nowrap"><?php echo e($overview['conversion_rate']); ?>%</span>
                            </div>
                        </div>
                    </div>

                    
                    <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-xl p-7 border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:shadow-2xl transition-shadow">
                        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-teal-500 to-cyan-400"></div>
                        <div class="relative z-10">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.25em] mb-2">Total Sepatu Masuk</p>
                            <h3 class="text-4xl font-black text-gray-900 dark:text-white leading-none tracking-tighter"><?php echo e(number_format($overview['total_incoming_items'])); ?></h3>
                            <p class="mt-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest bg-gray-50 dark:bg-gray-900 inline-block px-3 py-1.5 rounded-full">Volume fisik masuk</p>
                        </div>
                    </div>

                    
                    <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-xl p-7 border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:shadow-2xl transition-shadow col-span-1 lg:col-span-1">
                        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-indigo-500 to-purple-500"></div>
                        <div class="relative z-10">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.25em] mb-2">Revenue Realization</p>
                            <h3 class="text-3xl font-black text-gray-900 dark:text-white leading-none tracking-tighter">Rp <?php echo e(number_format($overview['total_revenue'], 0, ',', '.')); ?></h3>
                            <p class="mt-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest bg-gray-50 dark:bg-gray-900 inline-block px-3 py-1.5 rounded-full">Omset closing valid</p>
                        </div>
                    </div>

                    
                    <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-xl p-7 border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:shadow-2xl transition-shadow">
                        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-rose-500 to-pink-500"></div>
                        <div class="relative z-10">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.25em] mb-2">Avg Deal Value</p>
                            <h3 class="text-3xl font-black text-gray-900 dark:text-white leading-none tracking-tighter">Rp <?php echo e(number_format($overview['avg_deal_value'], 0, ',', '.')); ?></h3>
                            <p class="mt-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest bg-gray-50 dark:bg-gray-900 inline-block px-3 py-1.5 rounded-full">Rata-rata per deal</p>
                        </div>
                    </div>
                </div>
            </div>

            
            
            
            <div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-2 h-8 bg-[#FFC232] rounded-full"></div>
                    <div>
                        <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">Closing Path Analysis</h3>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Jalur lead menuju closing</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    
                    <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-xl p-7 border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:shadow-2xl transition-shadow">
                        <div class="absolute top-0 right-0 p-5 opacity-10 group-hover:opacity-20 transition-opacity text-green-500">
                            <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 24 24"><path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" stroke="currentColor" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-green-500 to-emerald-400"></div>
                        <div class="relative z-10">
                            <p class="text-[10px] font-black text-green-600 uppercase tracking-[0.25em] mb-2">Closing Langsung</p>
                            <h3 class="text-4xl font-black text-gray-900 dark:text-white leading-none tracking-tighter"><?php echo e(number_format($pathAnalysis['closed_direct'])); ?></h3>
                            <p class="mt-3 text-[10px] font-bold text-gray-500 leading-relaxed">Konsultasi → Closing (tanpa Follow-up)</p>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pathAnalysis['total_closed'] > 0): ?>
                                <div class="mt-3 flex items-center gap-2">
                                    <div class="h-2 flex-1 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-green-500 rounded-full" style="width: <?php echo e(round(($pathAnalysis['closed_direct'] / $pathAnalysis['total_closed']) * 100)); ?>%"></div>
                                    </div>
                                    <span class="text-xs font-black text-green-600"><?php echo e(round(($pathAnalysis['closed_direct'] / $pathAnalysis['total_closed']) * 100)); ?>%</span>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-xl p-7 border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:shadow-2xl transition-shadow">
                        <div class="absolute top-0 right-0 p-5 opacity-10 group-hover:opacity-20 transition-opacity text-orange-500">
                            <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" stroke="currentColor" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-orange-500 to-amber-400"></div>
                        <div class="relative z-10">
                            <p class="text-[10px] font-black text-orange-600 uppercase tracking-[0.25em] mb-2">Closing via Follow-up</p>
                            <h3 class="text-4xl font-black text-gray-900 dark:text-white leading-none tracking-tighter"><?php echo e(number_format($pathAnalysis['closed_via_followup'])); ?></h3>
                            <p class="mt-3 text-[10px] font-bold text-gray-500 leading-relaxed">Konsultasi → Follow-up → Closing</p>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pathAnalysis['total_closed'] > 0): ?>
                                <div class="mt-3 flex items-center gap-2">
                                    <div class="h-2 flex-1 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-orange-500 rounded-full" style="width: <?php echo e(round(($pathAnalysis['closed_via_followup'] / $pathAnalysis['total_closed']) * 100)); ?>%"></div>
                                    </div>
                                    <span class="text-xs font-black text-orange-600"><?php echo e(round(($pathAnalysis['closed_via_followup'] / $pathAnalysis['total_closed']) * 100)); ?>%</span>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-xl p-7 border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:shadow-2xl transition-shadow">
                        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-yellow-400 to-amber-300"></div>
                        <div class="relative z-10">
                            <p class="text-[10px] font-black text-yellow-600 uppercase tracking-[0.25em] mb-2">Konsultasi → Follow-up</p>
                            <h3 class="text-4xl font-black text-gray-900 dark:text-white leading-none tracking-tighter"><?php echo e(number_format($pathAnalysis['total_to_followup'])); ?></h3>
                            <p class="mt-3 text-[10px] font-bold text-gray-500 leading-relaxed">Total lead yang masuk tahap Follow-up</p>
                            <div class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-50 dark:bg-orange-900/20 rounded-full">
                                <span class="text-[10px] font-black text-orange-600 uppercase">Efektivitas:</span>
                                <span class="text-xs font-black text-orange-700"><?php echo e($pathAnalysis['followup_effectiveness']); ?>%</span>
                            </div>
                        </div>
                    </div>

                    
                    <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-xl p-7 border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:shadow-2xl transition-shadow">
                        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-blue-500 to-cyan-400"></div>
                        <div class="relative z-10">
                            <p class="text-[10px] font-black text-blue-600 uppercase tracking-[0.25em] mb-2">Follow-up Aktif</p>
                            <h3 class="text-4xl font-black text-gray-900 dark:text-white leading-none tracking-tighter"><?php echo e(number_format($pathAnalysis['active_followup'])); ?></h3>
                            <p class="mt-3 text-[10px] font-bold text-gray-500 leading-relaxed">Saat ini masih di tahap Follow-up</p>
                            <div class="mt-3">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 dark:bg-blue-900/20 rounded-full">
                                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                                    <span class="text-[10px] font-black text-blue-600 uppercase">Live Count</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            
            
            <div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-2 h-8 bg-indigo-500 rounded-full"></div>
                    <div>
                        <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">Pipeline Funnel</h3>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Distribusi lead per status</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-xl border border-gray-100 dark:border-gray-700 p-8">
                    <?php
                        $funnelColors = [
                            'GREETING' => ['bg' => 'bg-green-500', 'text' => 'text-green-600', 'light' => 'bg-green-50'],
                            'KONSULTASI' => ['bg' => 'bg-yellow-500', 'text' => 'text-yellow-600', 'light' => 'bg-yellow-50'],
                            'FOLLOW_UP' => ['bg' => 'bg-orange-500', 'text' => 'text-orange-600', 'light' => 'bg-orange-50'],
                            'CLOSING' => ['bg' => 'bg-blue-500', 'text' => 'text-blue-600', 'light' => 'bg-blue-50'],
                            'CONVERTED' => ['bg' => 'bg-emerald-500', 'text' => 'text-emerald-600', 'light' => 'bg-emerald-50'],
                            'LOST' => ['bg' => 'bg-red-500', 'text' => 'text-red-600', 'light' => 'bg-red-50'],
                        ];
                        $funnelLabels = [
                            'GREETING' => 'Greeting',
                            'KONSULTASI' => 'Konsultasi',
                            'FOLLOW_UP' => 'Follow-up',
                            'CLOSING' => 'Closing',
                            'CONVERTED' => 'Converted',
                            'LOST' => 'Lost',
                        ];
                        $funnelIcons = [
                            'GREETING' => '👋',
                            'KONSULTASI' => '💬',
                            'FOLLOW_UP' => '🔥',
                            'CLOSING' => '📋',
                            'CONVERTED' => '✅',
                            'LOST' => '❌',
                        ];
                    ?>
                    <div class="space-y-4">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $funnel['stages']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <div class="flex items-center gap-4">
                                <div class="w-32 flex-shrink-0 text-right">
                                    <span class="text-xs font-black <?php echo e($funnelColors[$stage['status']]['text'] ?? 'text-gray-600'); ?> uppercase tracking-wider">
                                        <?php echo e($funnelIcons[$stage['status']] ?? ''); ?> <?php echo e($funnelLabels[$stage['status']] ?? $stage['status']); ?>

                                    </span>
                                </div>
                                <div class="flex-1 relative">
                                    <div class="h-10 bg-gray-50 dark:bg-gray-700 rounded-xl overflow-hidden shadow-inner">
                                        <div class="<?php echo e($funnelColors[$stage['status']]['bg'] ?? 'bg-gray-400'); ?> h-full rounded-xl transition-all duration-1000 flex items-center px-4 shadow-lg"
                                             style="width: <?php echo e(max($stage['percentage'], 3)); ?>%">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($stage['count'] > 0): ?>
                                                <span class="text-white text-xs font-black whitespace-nowrap"><?php echo e($stage['count']); ?></span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="w-16 flex-shrink-0 text-right">
                                    <span class="text-sm font-black text-gray-700 dark:text-gray-300"><?php echo e($stage['percentage']); ?>%</span>
                                </div>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($funnel['total_created'] > 0): ?>
                        <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total lead masuk periode ini: <span class="text-gray-700 dark:text-gray-300"><?php echo e(number_format($funnel['total_created'])); ?></span></p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            
            
            <div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-2 h-8 bg-cyan-500 rounded-full"></div>
                    <div>
                        <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">Response Time Analytics</h3>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kecepatan respon CS</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <div class="space-y-4">
                        <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-xl p-7 border border-gray-100 dark:border-gray-700">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.25em] mb-2">Rata-rata Response</p>
                            <h3 class="text-3xl font-black text-gray-900 dark:text-white leading-none tracking-tighter">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($responseTime['avg_minutes'] < 60): ?>
                                    <?php echo e($responseTime['avg_minutes']); ?> <span class="text-lg text-gray-400">menit</span>
                                <?php else: ?>
                                    <?php echo e(floor($responseTime['avg_minutes'] / 60)); ?><span class="text-lg text-gray-400">j</span> 
                                    <?php echo e($responseTime['avg_minutes'] % 60); ?><span class="text-lg text-gray-400">m</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </h3>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-5 border border-gray-100 dark:border-gray-700">
                                <p class="text-[8px] font-black text-green-500 uppercase tracking-widest mb-1">⚡ Tercepat</p>
                                <p class="text-xl font-black text-gray-900 dark:text-white">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($responseTime['fastest'] < 60): ?>
                                        <?php echo e($responseTime['fastest']); ?>m
                                    <?php else: ?>
                                        <?php echo e(floor($responseTime['fastest'] / 60)); ?>j <?php echo e($responseTime['fastest'] % 60); ?>m
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </p>
                            </div>
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-5 border border-gray-100 dark:border-gray-700">
                                <p class="text-[8px] font-black text-red-500 uppercase tracking-widest mb-1">🐌 Terlambat</p>
                                <p class="text-xl font-black text-gray-900 dark:text-white">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($responseTime['slowest'] < 60): ?>
                                        <?php echo e($responseTime['slowest']); ?>m
                                    <?php else: ?>
                                        <?php echo e(floor($responseTime['slowest'] / 60)); ?>j <?php echo e($responseTime['slowest'] % 60); ?>m
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    
                    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-[2rem] shadow-xl p-8 border border-gray-100 dark:border-gray-700">
                        <h4 class="font-black text-sm text-gray-900 dark:text-white uppercase tracking-tight mb-6">Distribusi Waktu Respon</h4>
                        <div class="space-y-5">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $responseTime['distribution']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bucket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <?php
                                    $bucketPct = $responseTime['total_with_response'] > 0 
                                        ? round(($bucket['count'] / $responseTime['total_with_response']) * 100) 
                                        : 0;
                                ?>
                                <div>
                                    <div class="flex justify-between items-end mb-2">
                                        <span class="text-xs font-black text-gray-700 dark:text-gray-300"><?php echo e($bucket['label']); ?></span>
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-black text-gray-900 dark:text-white"><?php echo e($bucket['count']); ?></span>
                                            <span class="text-[10px] font-bold text-gray-400">(<?php echo e($bucketPct); ?>%)</span>
                                        </div>
                                    </div>
                                    <div class="h-5 w-full bg-gray-50 dark:bg-gray-700 rounded-lg overflow-hidden shadow-inner">
                                        <div class="h-full rounded-lg transition-all duration-1000 shadow-lg" 
                                             style="width: <?php echo e(max($bucketPct, 2)); ?>%; background-color: <?php echo e($bucket['color']); ?>">
                                        </div>
                                    </div>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                        <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total data response: <span class="text-gray-700 dark:text-gray-300"><?php echo e($responseTime['total_with_response']); ?> leads</span></p>
                        </div>
                    </div>
                </div>
            </div>

            
            
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-2 h-8 bg-violet-500 rounded-full"></div>
                        <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">Channel</h3>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700">
                        <div class="p-7 space-y-8">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $channelStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <div>
                                <div class="flex justify-between items-end mb-3">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="w-3 h-3 rounded-full <?php echo e($stat['channel'] == 'ONLINE' ? 'bg-indigo-500' : 'bg-[#FFC232]'); ?>"></span>
                                            <span class="text-xs font-black text-gray-800 dark:text-gray-200 uppercase tracking-wider"><?php echo e($stat['channel']); ?></span>
                                        </div>
                                        <div class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-widest">
                                            Rev: Rp <?php echo e(number_format($stat['revenue'], 0, ',', '.')); ?>

                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-2xl font-black text-gray-900 dark:text-white leading-none"><?php echo e($stat['leads']); ?></span>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase ml-1 block">Leads</span>
                                    </div>
                                </div>
                                <div class="h-4 w-full bg-gray-50 dark:bg-gray-700 rounded-full overflow-hidden flex shadow-inner">
                                    <div class="<?php echo e($stat['channel'] == 'ONLINE' ? 'bg-indigo-500 shadow-indigo-200' : 'bg-[#FFC232] shadow-yellow-200'); ?> h-full transition-all duration-1000 rounded-full" 
                                         style="width: <?php echo e($overview['total_leads'] > 0 ? ($stat['leads'] / $overview['total_leads'] * 100) : 0); ?>%"></div>
                                </div>
                                <div class="flex justify-between mt-2">
                                    <span class="text-[10px] font-bold text-gray-400">Closing: <?php echo e($stat['closings']); ?></span>
                                    <span class="text-[10px] font-black text-green-600">CR: <?php echo e($stat['conversion_rate']); ?>%</span>
                                </div>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($channelStats) == 0): ?>
                            <div class="text-center py-8 text-gray-400 italic text-sm">Belum ada data channel.</div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>

                
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-2 h-8 bg-red-500 rounded-full"></div>
                        <div>
                            <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">Lost Analysis</h3>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Analisa lead yang tidak closing</p>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                        
                        <div class="p-7 border-b border-gray-50 dark:border-gray-700 flex items-center gap-8 bg-gray-50/50 dark:bg-gray-800/50">
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Lost</p>
                                <h4 class="text-3xl font-black text-red-600 leading-none mt-1"><?php echo e(number_format($lostAnalysis['total_lost'])); ?></h4>
                            </div>
                            <div class="h-12 w-px bg-gray-200 dark:bg-gray-700"></div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Lost Rate</p>
                                <h4 class="text-3xl font-black text-gray-900 dark:text-white leading-none mt-1"><?php echo e($lostAnalysis['lost_rate']); ?><span class="text-lg text-gray-400">%</span></h4>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-100 dark:divide-gray-700">
                            
                            <div class="p-7">
                                <h5 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Alasan Lost</h5>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($lostAnalysis['reasons']) > 0): ?>
                                    <div class="space-y-3">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $lostAnalysis['reasons']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reason): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300 truncate mr-2"><?php echo e($reason['lost_reason']); ?></span>
                                                <span class="flex-shrink-0 text-xs font-black text-red-600 bg-red-50 dark:bg-red-900/20 px-2.5 py-1 rounded-full"><?php echo e($reason['count']); ?></span>
                                            </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-xs italic text-gray-400 py-4">Belum ada data alasan lost.</p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            
                            <div class="p-7">
                                <h5 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Lost per CS</h5>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($lostAnalysis['per_cs']) > 0): ?>
                                    <div class="space-y-3">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $lostAnalysis['per_cs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lostCs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-7 h-7 rounded-full bg-red-50 dark:bg-red-900/30 flex items-center justify-center">
                                                        <span class="text-[10px] font-black text-red-600"><?php echo e(substr($lostCs['cs_name'], 0, 1)); ?></span>
                                                    </div>
                                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300"><?php echo e($lostCs['cs_name']); ?></span>
                                                </div>
                                                <span class="text-xs font-black text-red-600"><?php echo e($lostCs['count']); ?></span>
                                            </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-xs italic text-gray-400 py-4">Belum ada data lost per CS.</p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            
            
            <div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-2 h-8 bg-[#22AF85] rounded-full"></div>
                    <div>
                        <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">CS KPI Leaderboard</h3>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Ranking performa individu CS</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-[1100px]">
                            <thead>
                                <tr class="bg-gray-50/80 dark:bg-gray-700/30">
                                    <th class="py-4 px-5 text-[9px] font-black text-gray-400 uppercase tracking-widest sticky left-0 bg-gray-50/80 dark:bg-gray-700/30 z-10">CS</th>
                                    <th class="py-4 px-4 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center">Intake</th>
                                    <th class="py-4 px-4 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center">Closing</th>
                                    <th class="py-4 px-4 text-[9px] font-black text-emerald-600 uppercase tracking-widest text-center">In Gudang</th>
                                    <th class="py-4 px-4 text-[9px] font-black text-orange-400 uppercase tracking-widest text-center">Pending</th>
                                    <th class="py-4 px-4 text-[9px] font-black text-teal-500 uppercase tracking-widest text-center">Items In</th>
                                    <th class="py-4 px-4 text-[9px] font-black text-teal-600 uppercase tracking-widest text-center">AIO</th>
                                    <th class="py-4 px-4 text-[9px] font-black text-green-500 uppercase tracking-widest text-center">Langsung</th>
                                    <th class="py-4 px-4 text-[9px] font-black text-orange-500 uppercase tracking-widest text-center">Via F/U</th>
                                    <th class="py-4 px-4 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center">F/U Aktif</th>
                                    <th class="py-4 px-4 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center">CR%</th>
                                    <th class="py-4 px-4 text-[9px] font-black text-red-400 uppercase tracking-widest text-center">Lost</th>
                                    <th class="py-4 px-4 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center">⏱ Avg Resp</th>
                                    <th class="py-4 px-4 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right">Revenue</th>
                                    <th class="py-4 px-4 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right">Avg Deal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $csKpis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $kpi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="py-5 px-5 sticky left-0 bg-white dark:bg-gray-800 z-10">
                                        <div class="flex items-center gap-3">
                                            <div class="relative">
                                                <div class="w-9 h-9 rounded-full flex items-center justify-center <?php echo e($index === 0 ? 'bg-amber-50 ring-2 ring-amber-300' : ($index === 1 ? 'bg-gray-100 ring-2 ring-gray-300' : ($index === 2 ? 'bg-orange-50 ring-2 ring-orange-300' : 'bg-indigo-50'))); ?>">
                                                    <span class="text-xs font-black <?php echo e($index === 0 ? 'text-amber-600' : ($index === 1 ? 'text-gray-500' : ($index === 2 ? 'text-orange-600' : 'text-indigo-600'))); ?>"><?php echo e(substr($kpi['cs_name'], 0, 1)); ?></span>
                                                </div>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($index < 3): ?>
                                                    <span class="absolute -top-1.5 -right-1.5 text-xs"><?php echo e($index === 0 ? '🥇' : ($index === 1 ? '🥈' : '🥉')); ?></span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                            <div>
                                                <div class="font-black text-gray-900 dark:text-white text-sm"><?php echo e($kpi['cs_name']); ?></div>
                                                <div class="flex gap-1.5 mt-1">
                                                    <span class="text-[7px] font-black px-1.5 py-0.5 bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300 rounded-full uppercase">ON:<?php echo e($kpi['online_leads']); ?></span>
                                                    <span class="text-[7px] font-black px-1.5 py-0.5 bg-orange-50 dark:bg-orange-900/40 text-orange-600 dark:text-orange-300 rounded-full uppercase">OFF:<?php echo e($kpi['offline_leads']); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-5 px-4 text-center font-black text-gray-700 dark:text-gray-300 text-sm"><?php echo e(number_format($kpi['total_leads'])); ?></td>
                                    <td class="py-5 px-4 text-center font-black text-blue-600 text-sm"><?php echo e(number_format($kpi['closings'])); ?></td>
                                    <td class="py-5 px-4 text-center">
                                        <div class="inline-flex flex-col items-center">
                                            <span class="text-sm font-black text-emerald-600"><?php echo e(number_format($kpi['spk_diterima'])); ?></span>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kpi['incoming_items'] > 0): ?>
                                            <span class="text-[8px] font-bold text-gray-400"><?php echo e(round(($kpi['spk_diterima'] / $kpi['incoming_items']) * 100)); ?>%</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="py-5 px-4 text-center">
                                        <div class="inline-flex flex-col items-center">
                                            <span class="text-sm font-black text-orange-500"><?php echo e(number_format($kpi['spk_pending'])); ?></span>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kpi['incoming_items'] > 0): ?>
                                            <span class="text-[8px] font-bold text-gray-400"><?php echo e(round(($kpi['spk_pending'] / $kpi['incoming_items']) * 100)); ?>%</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="py-5 px-4 text-center">
                                        <span class="text-sm font-black text-teal-600"><?php echo e(number_format($kpi['incoming_items'])); ?></span>
                                    </td>
                                    <td class="py-5 px-4 text-center">
                                        <span class="text-[10px] font-black text-gray-400">avg</span>
                                        <span class="text-xs font-black text-gray-700 dark:text-gray-300"><?php echo e($kpi['aio']); ?></span>
                                    </td>
                                    <td class="py-5 px-4 text-center">
                                        <span class="inline-flex items-center justify-center text-xs font-black text-green-700 bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded-full"><?php echo e($kpi['closing_direct']); ?></span>
                                    </td>
                                    <td class="py-5 px-4 text-center">
                                        <span class="inline-flex items-center justify-center text-xs font-black text-orange-700 bg-orange-50 dark:bg-orange-900/20 px-2 py-1 rounded-full"><?php echo e($kpi['closing_via_followup']); ?></span>
                                    </td>
                                    <td class="py-5 px-4 text-center">
                                        <span class="text-sm font-black text-blue-600"><?php echo e($kpi['follow_up_active']); ?></span>
                                    </td>
                                    <td class="py-5 px-4 text-center">
                                        <div class="inline-flex flex-col items-center">
                                            <span class="text-sm font-black text-gray-900 dark:text-white"><?php echo e($kpi['conversion_rate']); ?>%</span>
                                            <div class="w-16 h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full mt-1.5 overflow-hidden shadow-inner">
                                                <div class="h-full bg-[#22AF85] rounded-full shadow-lg" style="width: <?php echo e($kpi['conversion_rate']); ?>%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-5 px-4 text-center">
                                        <div class="inline-flex flex-col items-center">
                                            <span class="text-sm font-black text-red-600"><?php echo e($kpi['lost']); ?></span>
                                            <span class="text-[8px] font-bold text-gray-400"><?php echo e($kpi['lost_rate']); ?>%</span>
                                        </div>
                                    </td>
                                    <td class="py-5 px-4 text-center">
                                        <span class="text-xs font-black text-gray-700 dark:text-gray-300">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kpi['avg_response_time'] > 0): ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kpi['avg_response_time'] < 60): ?>
                                                    <?php echo e($kpi['avg_response_time']); ?>m
                                                <?php else: ?>
                                                    <?php echo e(floor($kpi['avg_response_time'] / 60)); ?>j <?php echo e($kpi['avg_response_time'] % 60); ?>m
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-gray-400">-</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </span>
                                    </td>
                                    <td class="py-5 px-4 text-right">
                                        <span class="px-3 py-1.5 bg-gray-50 dark:bg-gray-900 rounded-xl font-black text-gray-900 dark:text-teal-400 text-xs">
                                            Rp <?php echo e(number_format($kpi['revenue'], 0, ',', '.')); ?>

                                        </span>
                                    </td>
                                    <td class="py-5 px-4 text-right">
                                        <span class="text-xs font-black text-gray-600 dark:text-gray-400">
                                            Rp <?php echo e(number_format($kpi['avg_deal_value'], 0, ',', '.')); ?>

                                        </span>
                                    </td>
                                </tr>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <tr>
                                    <td colspan="13" class="py-12 text-center text-gray-400 italic text-sm">Data performa belum tersedia untuk periode ini.</td>
                                </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div class="bg-indigo-600 rounded-[2rem] shadow-xl p-8 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-8 -mt-8 opacity-20">
                    <svg class="w-48 h-48" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z"/></svg>
                </div>
                <h4 class="font-black text-lg uppercase leading-tight mb-4 relative z-10">💡 Insights Performa</h4>
                <div class="relative z-10 grid grid-cols-1 md:grid-cols-3 gap-6 text-xs font-bold leading-relaxed opacity-90">
                    <div>
                        <p class="font-black text-yellow-300 uppercase text-[10px] tracking-widest mb-1">Overview & Path</p>
                        <p>Data intake berdasarkan leads masuk. Path Analysis menunjukkan jalur closing: Konsultasi langsung atau via Follow-up.</p>
                    </div>
                    <div>
                        <p class="font-black text-yellow-300 uppercase text-[10px] tracking-widest mb-1">Response Time</p>
                        <p>Dihitung dari selisih waktu kontak pertama (first_contact_at) dan respon pertama CS (first_response_at).</p>
                    </div>
                    <div>
                        <p class="font-black text-yellow-300 uppercase text-[10px] tracking-widest mb-1">Revenue</p>
                        <p>Revenue dihitung dari SPK valid (bukan draft) yang dibuat oleh CS dalam periode tersebut.</p>
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
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views/admin/cs/dashboard/index.blade.php ENDPATH**/ ?>