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

    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-100 to-teal-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            
            
            <section class="relative overflow-hidden bg-gradient-to-br from-teal-600 via-teal-700 to-orange-600 rounded-3xl shadow-2xl">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="absolute -right-20 -top-20 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-orange-500/20 rounded-full blur-3xl"></div>
                
                <div class="relative px-8 py-10">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <div class="space-y-2">
                            <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-white/90 text-xs font-bold mb-2">
                                <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                                Live Monitoring
                            </div>
                            <span id="ws-live-indicator" class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/15 backdrop-blur-sm rounded-full ml-2">
                                <span class="text-[10px] font-bold text-white/70 ws-live-time"></span>
                            </span>
                            <h1 class="text-4xl lg:text-5xl font-black text-white tracking-tight">
                                Workshop Dashboard
                            </h1>
                            <p class="text-teal-100 text-lg font-medium">
                                Metrik Performansi & Analitik Operasional
                            </p>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
                            
                            <form action="<?php echo e(route('workshop.dashboard')); ?>" method="GET" class="flex items-center gap-2 bg-white/15 backdrop-blur-md px-4 py-3 rounded-xl border border-white/20 shadow-lg">
                                <svg class="w-5 h-5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <input type="date" name="start_date" value="<?php echo e($filterStartDate); ?>" 
                                    class="bg-transparent border-none text-white text-sm focus:ring-0 cursor-pointer font-medium placeholder-white/50"
                                    onchange="this.form.submit()">
                                <span class="text-white/60">—</span>
                                <input type="date" name="end_date" value="<?php echo e($filterEndDate); ?>" 
                                    class="bg-transparent border-none text-white text-sm focus:ring-0 cursor-pointer font-medium placeholder-white/50"
                                    onchange="this.form.submit()">
                            </form>

                            
                            <a href="<?php echo e(route('workshop.dashboard-v2')); ?>" class="inline-flex items-center gap-2 px-5 py-3 bg-orange-500/20 text-orange-50 hover:text-white border border-orange-400/30 rounded-xl font-bold hover:bg-orange-500/40 transition-all shadow-lg hover:shadow-xl hover:scale-105 duration-200 backdrop-blur-md">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                <span>Dashboard V2</span>
                            </a>

                            
                            <form action="<?php echo e(route('workshop.export')); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="start_date" value="<?php echo e($filterStartDate); ?>">
                                <input type="hidden" name="end_date" value="<?php echo e($filterEndDate); ?>">
                                <button type="submit" class="inline-flex items-center gap-2 px-5 py-3 bg-white text-teal-700 rounded-xl font-bold hover:bg-teal-50 transition-all shadow-lg hover:shadow-xl hover:scale-105 duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Export Laporan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

            
            <section>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    
                    <div class="group bg-white rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 border border-teal-100 hover:border-teal-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-teal-100 to-teal-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div id="ws-stat-inprogress" class="text-3xl font-black text-teal-600 mb-1"><?php echo e($inProgress); ?></div>
                        <div class="text-xs font-bold text-teal-500 uppercase tracking-wider">Diproses</div>
                    </div>

                    
                    <div class="group bg-white rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-gray-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="text-3xl font-black text-gray-800 mb-1"><?php echo e($throughput); ?></div>
                        <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Selesai</div>
                    </div>

                    
                    <div class="group bg-white rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 border border-red-100 hover:border-red-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-red-100 to-red-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                        </div>
                        <div id="ws-stat-urgent" class="text-3xl font-black text-red-600 mb-1"><?php echo e($urgentCount); ?></div>
                        <div class="text-xs font-bold text-red-500 uppercase tracking-wider">Mendesak</div>
                    </div>

                    
                    <div class="group bg-white rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 border border-orange-100 hover:border-orange-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-orange-100 to-orange-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="text-3xl font-black text-orange-600 mb-1"><?php echo e($qcPassRate); ?>%</div>
                        <div class="text-xs font-bold text-orange-500 uppercase tracking-wider">Lolos QC</div>
                    </div>

                    
                    <div class="group bg-white rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-gray-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="text-3xl font-black text-gray-800 mb-1"><?php echo e($capacityUtilization); ?></div>
                        <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Kapasitas</div>
                    </div>

                    
                    <div class="group bg-gradient-to-br from-teal-500 to-orange-500 rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="text-3xl font-black text-white mb-1">Rp <?php echo e(number_format($revenue/1000, 0)); ?>k</div>
                        <div class="text-xs font-bold text-white/90 uppercase tracking-wider">Pendapatan</div>
                    </div>
                </div>
            </section>

            
            <section>
                <?php echo $__env->make('workshop.dashboard.partials.spk-matrix', ['matrixData' => $matrixData], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </section>

            
            <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-teal-50 px-6 py-5 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-orange-500 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-gray-800">Tren Penyelesaian</h3>
                                    <p class="text-xs text-gray-500 font-medium">Daily completion tracking</p>
                                </div>
                            </div>
                            <span class="px-3 py-1.5 bg-white rounded-lg text-xs font-bold text-gray-600 shadow-sm border border-gray-200">
                                <?php echo e(\Carbon\Carbon::parse($filterStartDate)->format('d M')); ?> - <?php echo e(\Carbon\Carbon::parse($filterEndDate)->format('d M')); ?>

                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <?php
                            $datasets = [[
                                'label' => 'Order Selesai',
                                'data' => $trendData,
                                'borderColor' => '#14b8a6',
                                'backgroundColor' => 'rgba(20, 184, 166, 0.1)',
                                'fill' => true,
                                'tension' => 0.4
                            ]];
                        ?>
                        <?php if (isset($component)) { $__componentOriginal1d640cb4ac758fdb081df5b51e265af0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1d640cb4ac758fdb081df5b51e265af0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.line-chart','data' => ['id' => 'completionChart','labels' => $trendLabels,'datasets' => $datasets]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('line-chart'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'completionChart','labels' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($trendLabels),'datasets' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($datasets)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1d640cb4ac758fdb081df5b51e265af0)): ?>
<?php $attributes = $__attributesOriginal1d640cb4ac758fdb081df5b51e265af0; ?>
<?php unset($__attributesOriginal1d640cb4ac758fdb081df5b51e265af0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1d640cb4ac758fdb081df5b51e265af0)): ?>
<?php $component = $__componentOriginal1d640cb4ac758fdb081df5b51e265af0; ?>
<?php unset($__componentOriginal1d640cb4ac758fdb081df5b51e265af0); ?>
<?php endif; ?>
                    </div>
                </div>

                
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-orange-50 px-6 py-5 border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-teal-500 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-gray-800">Tenggat Waktu</h3>
                                <p class="text-xs text-gray-500 font-medium">Deadline status</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="relative h-64">
                            <?php if (isset($component)) { $__componentOriginaleb4680f19f6910399be4a108cac83983 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaleb4680f19f6910399be4a108cac83983 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.donut-chart','data' => ['id' => 'deadlineChart','labels' => ['Aman', 'Perlu Perhatian', 'Terlambat'],'data' => [$onTimeOrders, $atRiskOrders, $overdueOrders],'colors' => ['#14b8a6', '#f97316', '#ef4444'],'height' => '250']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('donut-chart'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'deadlineChart','labels' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Aman', 'Perlu Perhatian', 'Terlambat']),'data' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([$onTimeOrders, $atRiskOrders, $overdueOrders]),'colors' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['#14b8a6', '#f97316', '#ef4444']),'height' => '250']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaleb4680f19f6910399be4a108cac83983)): ?>
<?php $attributes = $__attributesOriginaleb4680f19f6910399be4a108cac83983; ?>
<?php unset($__attributesOriginaleb4680f19f6910399be4a108cac83983); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaleb4680f19f6910399be4a108cac83983)): ?>
<?php $component = $__componentOriginaleb4680f19f6910399be4a108cac83983; ?>
<?php unset($__componentOriginaleb4680f19f6910399be4a108cac83983); ?>
<?php endif; ?>
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <div class="text-center">
                                    <div class="text-4xl font-black bg-gradient-to-r from-teal-600 to-orange-600 bg-clip-text text-transparent">
                                        <?php echo e($inProgress); ?>

                                    </div>
                                    <div class="text-xs text-gray-400 font-bold uppercase tracking-wider">Aktif</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </section>

            
            <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-teal-50 px-6 py-5 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-gray-800">Beban Kerja Teknisi</h3>
                                    <p class="text-xs text-gray-500 font-medium">Current workload distribution</p>
                                </div>
                            </div>
                            <span class="px-3 py-1.5 bg-green-100 text-green-700 rounded-lg text-xs font-bold animate-pulse">
                                ● Live
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($technicianLoad->count() > 0): ?>
                            <?php if (isset($component)) { $__componentOriginaldd2408fb2f24aa9646f201b7544a475f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldd2408fb2f24aa9646f201b7544a475f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.bar-chart','data' => ['id' => 'techLoadChart','labels' => $technicianLoad->pluck('name'),'data' => $technicianLoad->pluck('count'),'label' => 'Order Sedang Dikerjakan','color' => '#14b8a6']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('bar-chart'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'techLoadChart','labels' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($technicianLoad->pluck('name')),'data' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($technicianLoad->pluck('count')),'label' => 'Order Sedang Dikerjakan','color' => '#14b8a6']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldd2408fb2f24aa9646f201b7544a475f)): ?>
<?php $attributes = $__attributesOriginaldd2408fb2f24aa9646f201b7544a475f; ?>
<?php unset($__attributesOriginaldd2408fb2f24aa9646f201b7544a475f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldd2408fb2f24aa9646f201b7544a475f)): ?>
<?php $component = $__componentOriginaldd2408fb2f24aa9646f201b7544a475f; ?>
<?php unset($__componentOriginaldd2408fb2f24aa9646f201b7544a475f); ?>
<?php endif; ?>
                        <?php else: ?>
                            <div class="text-center py-12">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-500 text-sm font-medium">Belum ada teknisi aktif</p>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-orange-50 px-6 py-5 border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-gray-800">Aktivitas Terbaru</h3>
                                <p class="text-xs text-gray-500 font-medium">Latest updates</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3 max-h-[300px] overflow-y-auto custom-scrollbar">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $recentLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <div class="flex gap-3 p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-all border border-gray-100 hover:border-gray-200">
                                <div class="flex-shrink-0 mt-1">
                                    <div class="w-2 h-2 rounded-full bg-teal-500 ring-4 ring-teal-100"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-bold text-gray-800 mb-1">
                                        <?php echo e($log->user->name ?? 'System'); ?> 
                                        <span class="font-normal text-gray-500">mengupdate</span> 
                                        <span class="text-teal-600"><?php echo e($log->workOrder?->spk_number ?? 'Unknown'); ?></span>
                                    </div>
                                    <div class="text-xs text-gray-600 mb-1 line-clamp-1"><?php echo e($log->description); ?></div>
                                    <div class="text-[10px] text-gray-400"><?php echo e($log->created_at->diffForHumans()); ?></div>
                                </div>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <div class="text-center py-12">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-500 text-sm font-medium">Belum ada aktivitas</p>
                            </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>

            </section>

            
            <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-teal-50 px-6 py-5 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-gray-800">Antrian per Stasiun</h3>
                                    <p class="text-xs text-gray-500 font-medium">Current queue status</p>
                                </div>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bottleneckCount > 10): ?>
                                <span class="px-3 py-1.5 bg-red-100 text-red-700 rounded-lg text-xs font-bold animate-pulse">
                                    ⚠️ <?php echo e(ucfirst($bottleneckStation)); ?>

                                </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                    <div class="p-6 space-y-3">
                        <?php if (isset($component)) { $__componentOriginalc24ad5afb65df15418363d12b07ac941 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc24ad5afb65df15418363d12b07ac941 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.workload-bar','data' => ['label' => 'Asesmen','count' => $workloadByStation['assessment'],'max' => 30,'href' => ''.e(route('assessment.index')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('workload-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Asesmen','count' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workloadByStation['assessment']),'max' => 30,'href' => ''.e(route('assessment.index')).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc24ad5afb65df15418363d12b07ac941)): ?>
<?php $attributes = $__attributesOriginalc24ad5afb65df15418363d12b07ac941; ?>
<?php unset($__attributesOriginalc24ad5afb65df15418363d12b07ac941); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc24ad5afb65df15418363d12b07ac941)): ?>
<?php $component = $__componentOriginalc24ad5afb65df15418363d12b07ac941; ?>
<?php unset($__componentOriginalc24ad5afb65df15418363d12b07ac941); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginalc24ad5afb65df15418363d12b07ac941 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc24ad5afb65df15418363d12b07ac941 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.workload-bar','data' => ['label' => 'Preparation','count' => $workloadByStation['preparation'],'max' => 30,'href' => ''.e(route('preparation.index')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('workload-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Preparation','count' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workloadByStation['preparation']),'max' => 30,'href' => ''.e(route('preparation.index')).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc24ad5afb65df15418363d12b07ac941)): ?>
<?php $attributes = $__attributesOriginalc24ad5afb65df15418363d12b07ac941; ?>
<?php unset($__attributesOriginalc24ad5afb65df15418363d12b07ac941); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc24ad5afb65df15418363d12b07ac941)): ?>
<?php $component = $__componentOriginalc24ad5afb65df15418363d12b07ac941; ?>
<?php unset($__componentOriginalc24ad5afb65df15418363d12b07ac941); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginalc24ad5afb65df15418363d12b07ac941 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc24ad5afb65df15418363d12b07ac941 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.workload-bar','data' => ['label' => 'Sortir & Material','count' => $workloadByStation['sortir'],'max' => 30,'href' => ''.e(route('sortir.index')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('workload-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Sortir & Material','count' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workloadByStation['sortir']),'max' => 30,'href' => ''.e(route('sortir.index')).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc24ad5afb65df15418363d12b07ac941)): ?>
<?php $attributes = $__attributesOriginalc24ad5afb65df15418363d12b07ac941; ?>
<?php unset($__attributesOriginalc24ad5afb65df15418363d12b07ac941); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc24ad5afb65df15418363d12b07ac941)): ?>
<?php $component = $__componentOriginalc24ad5afb65df15418363d12b07ac941; ?>
<?php unset($__componentOriginalc24ad5afb65df15418363d12b07ac941); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginalc24ad5afb65df15418363d12b07ac941 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc24ad5afb65df15418363d12b07ac941 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.workload-bar','data' => ['label' => 'Produksi','count' => $workloadByStation['production'],'max' => 30,'href' => ''.e(route('production.index')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('workload-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Produksi','count' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workloadByStation['production']),'max' => 30,'href' => ''.e(route('production.index')).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc24ad5afb65df15418363d12b07ac941)): ?>
<?php $attributes = $__attributesOriginalc24ad5afb65df15418363d12b07ac941; ?>
<?php unset($__attributesOriginalc24ad5afb65df15418363d12b07ac941); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc24ad5afb65df15418363d12b07ac941)): ?>
<?php $component = $__componentOriginalc24ad5afb65df15418363d12b07ac941; ?>
<?php unset($__componentOriginalc24ad5afb65df15418363d12b07ac941); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginalc24ad5afb65df15418363d12b07ac941 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc24ad5afb65df15418363d12b07ac941 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.workload-bar','data' => ['label' => 'Quality Control','count' => $workloadByStation['qc'],'max' => 30,'href' => ''.e(route('qc.index')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('workload-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Quality Control','count' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workloadByStation['qc']),'max' => 30,'href' => ''.e(route('qc.index')).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc24ad5afb65df15418363d12b07ac941)): ?>
<?php $attributes = $__attributesOriginalc24ad5afb65df15418363d12b07ac941; ?>
<?php unset($__attributesOriginalc24ad5afb65df15418363d12b07ac941); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc24ad5afb65df15418363d12b07ac941)): ?>
<?php $component = $__componentOriginalc24ad5afb65df15418363d12b07ac941; ?>
<?php unset($__componentOriginalc24ad5afb65df15418363d12b07ac941); ?>
<?php endif; ?>
                    </div>
                </div>

                
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-orange-50 px-6 py-5 border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-gray-800">Teknisi Terbaik</h3>
                                <p class="text-xs text-gray-500 font-medium">Period top performers</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <?php if (isset($component)) { $__componentOriginalc94facc724193882a7e04b7cf1b3a93f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc94facc724193882a7e04b7cf1b3a93f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.leaderboard','data' => ['performers' => $topPerformers]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('leaderboard'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['performers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($topPerformers)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc94facc724193882a7e04b7cf1b3a93f)): ?>
<?php $attributes = $__attributesOriginalc94facc724193882a7e04b7cf1b3a93f; ?>
<?php unset($__attributesOriginalc94facc724193882a7e04b7cf1b3a93f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc94facc724193882a7e04b7cf1b3a93f)): ?>
<?php $component = $__componentOriginalc94facc724193882a7e04b7cf1b3a93f; ?>
<?php unset($__componentOriginalc94facc724193882a7e04b7cf1b3a93f); ?>
<?php endif; ?>
                    </div>
                </div>

            </section>

            
            <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-red-50 px-6 py-5 border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-gray-800">Order Mendesak</h3>
                                <p class="text-xs text-gray-500 font-medium">Requires immediate attention</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($urgentOrders->count() > 0): ?>
                            <div class="space-y-3 max-h-96 overflow-y-auto custom-scrollbar">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $urgentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <div class="p-4 bg-gradient-to-r from-red-50 to-orange-50 rounded-xl border-l-4 border-red-500 hover:shadow-md transition-shadow">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="flex-1">
                                                <div class="font-bold text-gray-800 mb-1"><?php echo e($order->spk_number); ?></div>
                                                <div class="text-sm text-gray-600 mb-2"><?php echo e($order->customer_name); ?></div>
                                                <span class="inline-block px-2 py-1 bg-white rounded text-xs font-bold text-gray-700">
                                                    <?php echo e($order->status->label()); ?>

                                                </span>
                                            </div>
                                            <div class="flex flex-col items-end gap-2">
                                                <?php if (isset($component)) { $__componentOriginala2c46be9105826f6af7d8ff8b2d4eb31 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala2c46be9105826f6af7d8ff8b2d4eb31 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.countdown-badge','data' => ['order' => $order]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('countdown-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['order' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($order)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala2c46be9105826f6af7d8ff8b2d4eb31)): ?>
<?php $attributes = $__attributesOriginala2c46be9105826f6af7d8ff8b2d4eb31; ?>
<?php unset($__attributesOriginala2c46be9105826f6af7d8ff8b2d4eb31); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala2c46be9105826f6af7d8ff8b2d4eb31)): ?>
<?php $component = $__componentOriginala2c46be9105826f6af7d8ff8b2d4eb31; ?>
<?php unset($__componentOriginala2c46be9105826f6af7d8ff8b2d4eb31); ?>
<?php endif; ?>
                                                <?php
                                                    $routeName = match($order->status->value) {
                                                        'ASSESSMENT' => 'assessment.create',
                                                        'PREPARATION' => 'preparation.show',
                                                        'SORTIR' => 'sortir.show',
                                                        'QC' => 'qc.show',
                                                        default => null,
                                                    };
                                                ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($routeName): ?>
                                                    <a href="<?php echo e(route($routeName, $order->id)); ?>" class="text-xs font-bold text-teal-600 hover:text-teal-700">Lihat →</a>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-12">
                                <div class="text-6xl mb-4">🎉</div>
                                <div class="text-gray-500 font-semibold">Tidak ada order mendesak!</div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <div class="space-y-6">
                    
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lowStockMaterials->count() > 0): ?>
                    <div class="bg-gradient-to-br from-red-50 to-orange-50 rounded-2xl shadow-xl border border-red-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-red-500 to-orange-500 px-6 py-5 border-b border-red-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-white">Stok Menipis</h3>
                                    <p class="text-xs text-red-100 font-medium">Material alerts</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $lowStockMaterials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <div class="flex items-center justify-between p-3 bg-white rounded-xl shadow-sm border border-red-100">
                                    <span class="font-bold text-gray-700"><?php echo e($material->name); ?></span>
                                    <span class="px-3 py-1.5 bg-red-100 text-red-700 rounded-lg font-bold text-xs">
                                        <?php echo e($material->stock); ?> <?php echo e($material->unit); ?>

                                    </span>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-gray-50 to-orange-50 px-6 py-5 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-gray-800">Layanan Terpopuler</h3>
                                    <p class="text-xs text-gray-500 font-medium">Top services by revenue</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $serviceMix; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mix): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <div>
                                    <div class="flex justify-between text-sm mb-2">
                                        <span class="font-bold text-gray-700 truncate"><?php echo e($mix->service?->name ?? 'Service Terhapus/Lainnya'); ?></span>
                                        <span class="font-black bg-gradient-to-r from-teal-600 to-orange-600 bg-clip-text text-transparent">
                                            Rp <?php echo e(number_format($mix->total_revenue/1000, 0)); ?>k
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                        <div class="h-2.5 rounded-full bg-gradient-to-r from-teal-500 to-orange-500 transition-all duration-500" 
                                             style="width: <?php echo e(min(($mix->order_count / 20) * 100, 100)); ?>%"></div>
                                    </div>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        </div>
                    </div>

                </div>
            </section>

        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, #14b8a6, #f97316);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom, #0d9488, #ea580c);
        }
    </style>

    
    <script>
        (function() {
            const POLL_INTERVAL = 30000;
            const API_URL = '<?php echo e(route("workshop.dashboard.api-stats")); ?>';

            function updateWorkshopDashboard() {
                fetch(API_URL, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    const updates = {
                        'ws-stat-inprogress': data.in_progress,
                        'ws-stat-urgent': data.urgent_count,
                    };
                    Object.entries(updates).forEach(([id, val]) => {
                        const el = document.getElementById(id);
                        if (el && el.textContent != val) {
                            el.textContent = val;
                            el.classList.add('animate-pulse');
                            setTimeout(() => el.classList.remove('animate-pulse'), 1500);
                        }
                    });

                    // Update timestamp
                    const timeEl = document.querySelector('.ws-live-time');
                    if (timeEl) timeEl.textContent = 'Updated ' + data.timestamp;
                })
                .catch(err => console.warn('Workshop poll error:', err));
            }

            setInterval(updateWorkshopDashboard, POLL_INTERVAL);
        })();
    </script>

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
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\workshop\dashboard\index.blade.php ENDPATH**/ ?>