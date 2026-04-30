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
            <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm shadow-sm border border-white/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            
            <div class="flex flex-col">
                <h2 class="font-bold text-xl leading-tight tracking-wide">
                    <?php echo e(__('Inspection Details')); ?>

                </h2>
                <div class="text-xs font-medium opacity-90 flex items-center gap-2">
                    <span class="bg-white/20 px-2 py-0.5 rounded text-white font-mono">
                        <?php echo e($order->spk_number); ?>

                    </span>
                    <span><?php echo e($order->customer_name); ?></span>
                </div>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12 bg-gray-50/50">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- LEFT COLUMN: INFO SEPATU -->
                <div class="space-y-6">
                    <div class="dashboard-card overflow-hidden">
                        <div class="dashboard-card-header bg-gradient-to-r from-gray-800 to-gray-900 text-white">
                            <h3 class="dashboard-card-title text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Shoe Information
                            </h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Merek & Artikel</label>
                                <div class="font-bold text-gray-800 text-lg"><?php echo e($order->shoe_brand); ?></div>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Warna & Ukuran</label>
                                <div class="text-gray-700 flex items-center gap-2">
                                    <span class="px-2 py-1 bg-gray-100 rounded text-xs font-bold"><?php echo e($order->shoe_color); ?></span>
                                    <span class="text-gray-300">|</span>
                                    <span class="px-2 py-1 bg-gray-100 rounded text-xs font-bold"><?php echo e($order->shoe_size ?? '-'); ?></span>
                                    <span class="px-2 py-1 bg-gray-100 rounded text-xs font-bold"><?php echo e($order->shoe_size ?? '-'); ?></span>
                                </div>
                            </div>

                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->technician_notes): ?>
                                <div class="p-3 bg-amber-50 border-l-4 border-amber-500 rounded-r text-sm text-amber-900 font-medium">
                                    <span class="block font-bold text-amber-600 uppercase text-[10px] tracking-wide mb-1">⚠️ Instruksi Khusus Teknisi:</span>
                                    <?php echo e($order->technician_notes); ?>

                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->notes): ?>
                                <div>
                                    <label class="text-xs font-bold text-blue-500 uppercase tracking-wider mb-1 block">💬 Request / Keluhan Customer (CS)</label>
                                    <div class="text-xs text-blue-900 italic border-l-4 border-blue-200 p-2 rounded bg-blue-50 leading-relaxed">
                                        "<?php echo e($order->notes); ?>"
                                    </div>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            
                            <?php
                                $resolvedIssue = $order->cxIssues->where('status', 'RESOLVED')->last();
                            ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($resolvedIssue): ?>
                                <div class="p-3 bg-purple-50 border-l-4 border-purple-500 rounded-r text-sm text-purple-900 font-medium">
                                    <span class="block font-bold text-purple-600 uppercase text-[10px] tracking-wide mb-1">⚠️ Riwayat Follow Up CX:</span>
                                    <div class="italic">"<?php echo e($resolvedIssue->resolution_notes ?? $resolvedIssue->description ?? '-'); ?>"</div>
                                    <div class="mt-1 text-[9px] text-purple-500 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Done by <?php echo e($resolvedIssue->resolver->name ?? 'System'); ?> • <?php echo e($resolvedIssue->updated_at->format('d/M H:i')); ?>

                                    </div>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <div class="pt-4 border-t border-gray-100">
                                <label class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3 block">Layanan yang Dikerjakan</label>
                                <div class="space-y-3">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $order->workOrderServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                        <div class="bg-white border border-gray-200 rounded-xl p-3 shadow-sm hover:border-teal-500/30 transition-all group">
                                            <div class="flex justify-between items-start mb-2">
                                                <div class="flex flex-col">
                                                    <span class="text-[9px] font-black bg-teal-50 text-teal-600 px-2 py-0.5 rounded uppercase tracking-widest mb-1 self-start">
                                                        <?php echo e($detail->category_name ?? $detail->service->category ?? 'General'); ?>

                                                    </span>
                                                    <span class="text-xs font-black text-gray-800 uppercase tracking-tight group-hover:text-teal-600 transition-colors">
                                                        <?php echo e($detail->custom_service_name ?? ($detail->service ? $detail->service->name : 'Layanan')); ?>

                                                    </span>
                                                </div>
                                                <span class="text-[10px] font-black text-teal-600">
                                                    Rp <?php echo e(number_format($detail->cost, 0, ',', '.')); ?>

                                                </span>
                                            </div>

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($detail->service && $detail->service->description): ?>
                                                <p class="text-[10px] text-gray-400 font-medium mb-2 leading-relaxed italic">
                                                    <?php echo e($detail->service->description); ?>

                                                </p>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($detail->service_details['manual_detail']) && !empty($detail->service_details['manual_detail'])): ?>
                                                <div class="mb-3 p-2 bg-yellow-50 border border-yellow-100 rounded-lg text-[10px] text-yellow-800 font-bold italic">
                                                    "<?php echo e($detail->service_details['manual_detail']); ?>"
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                            <div class="pt-2 border-t border-gray-50 flex items-center justify-between">
                                                <div class="text-[9px] text-gray-500 font-black flex items-center gap-1.5 uppercase tracking-wide">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-teal-500"></div>
                                                    PIC: <?php echo e($detail->technician ? $detail->technician->name : ($detail->technician_id ? \App\Models\User::find($detail->technician_id)->name : '-')); ?>

                                                </div>
                                                <div class="flex gap-1">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($detail->service_details) && is_array($detail->service_details)): ?>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $detail->service_details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($key !== 'manual_detail' && !empty($val)): ?>
                                                                <span class="text-[8px] font-bold text-gray-400 bg-gray-50 px-1 py-0.5 rounded lowercase">
                                                                    #<?php echo e(is_array($val) ? implode(', ', $val) : $val); ?>

                                                                </span>
                                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: QC CHECKLIST -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="dashboard-card overflow-hidden">
                        <div class="dashboard-card-header bg-gradient-to-r from-teal-700 to-teal-800 border-b border-teal-600">
                            <h3 class="dashboard-card-title text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-teal-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                                Inspection Checklist
                            </h3>
                        </div>
                        
                        <div class="p-6 space-y-8">
                            <!-- 1. Jahit Sol -->
                            <div class="relative pl-8 border-l-2 <?php echo e($subtasks['jahit']['done'] ? 'border-green-500' : 'border-gray-200'); ?>">
                                <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full <?php echo e($subtasks['jahit']['done'] ? 'bg-green-500' : 'bg-gray-300'); ?>"></div>
                                <h4 class="text-sm font-bold text-gray-800 mb-2">1. Pengecekan Jahitan Sol (Jika ada)</h4>
                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subtasks['jahit']['done']): ?>
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-3 flex justify-between items-center">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span class="font-bold text-green-700">PASSED</span>
                                        </div>
                                        <div class="text-right text-xs text-gray-500">
                                            <div>Verified at <?php echo e($subtasks['jahit']['end']->format('H:i')); ?></div>
                                            <div>Duration: <?php echo e($subtasks['jahit']['duration']); ?> m</div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <form action="<?php echo e(route('qc.update', $order->id)); ?>" method="POST" class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="type" value="jahit">
                                        <div class="flex gap-4 items-end">
                                            <div class="flex-1">
                                                <label class="block text-xs text-gray-500 mb-1">Inspector Name</label>
                                                <select name="worker_id" class="block w-full text-sm border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500" required>
                                                    <option value="">-- Select Inspector --</option>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $techJahit; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tech): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                        <option value="<?php echo e($tech->id); ?>"><?php echo e($tech->name); ?></option>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                                </select>
                                            </div>
                                            <button class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow transition-colors">
                                                MARK PASS
                                            </button>
                                        </div>
                                    </form>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                    <span class="text-xs font-bold text-gray-500 uppercase block mb-2">Temuan Awal (Before)</span>
                                    <?php if (isset($component)) { $__componentOriginal0b55eebd37945af6c95e63b8e56be4d6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0b55eebd37945af6c95e63b8e56be4d6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.photo-uploader','data' => ['order' => $order,'step' => 'QC_JAHIT_BEFORE']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('photo-uploader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['order' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($order),'step' => 'QC_JAHIT_BEFORE']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0b55eebd37945af6c95e63b8e56be4d6)): ?>
<?php $attributes = $__attributesOriginal0b55eebd37945af6c95e63b8e56be4d6; ?>
<?php unset($__attributesOriginal0b55eebd37945af6c95e63b8e56be4d6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0b55eebd37945af6c95e63b8e56be4d6)): ?>
<?php $component = $__componentOriginal0b55eebd37945af6c95e63b8e56be4d6; ?>
<?php unset($__componentOriginal0b55eebd37945af6c95e63b8e56be4d6); ?>
<?php endif; ?>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                    <span class="text-xs font-bold text-gray-500 uppercase block mb-2">Bukti Pengecekan (After)</span>
                                    <?php if (isset($component)) { $__componentOriginal0b55eebd37945af6c95e63b8e56be4d6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0b55eebd37945af6c95e63b8e56be4d6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.photo-uploader','data' => ['order' => $order,'step' => 'QC_JAHIT_AFTER']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('photo-uploader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['order' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($order),'step' => 'QC_JAHIT_AFTER']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0b55eebd37945af6c95e63b8e56be4d6)): ?>
<?php $attributes = $__attributesOriginal0b55eebd37945af6c95e63b8e56be4d6; ?>
<?php unset($__attributesOriginal0b55eebd37945af6c95e63b8e56be4d6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0b55eebd37945af6c95e63b8e56be4d6)): ?>
<?php $component = $__componentOriginal0b55eebd37945af6c95e63b8e56be4d6; ?>
<?php unset($__componentOriginal0b55eebd37945af6c95e63b8e56be4d6); ?>
<?php endif; ?>
                                </div>
                            </div>
                        </div>

                            <!-- 2. Clean Up -->
                            <div class="relative pl-8 border-l-2 <?php echo e($subtasks['clean_up']['done'] ? 'border-green-500' : 'border-gray-200'); ?>">
                                <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full <?php echo e($subtasks['clean_up']['done'] ? 'bg-green-500' : 'bg-gray-300'); ?>"></div>
                                <h4 class="text-sm font-bold text-gray-800 mb-2">2. Kebersihan / Clean Up Detail</h4>
                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subtasks['clean_up']['done']): ?>
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-3 flex justify-between items-center">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span class="font-bold text-green-700">PASSED</span>
                                        </div>
                                        <div class="text-right text-xs text-gray-500">
                                            <div>Verified at <?php echo e($subtasks['clean_up']['end']->format('H:i')); ?></div>
                                            <div>Duration: <?php echo e($subtasks['clean_up']['duration']); ?> m</div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <form action="<?php echo e(route('qc.update', $order->id)); ?>" method="POST" class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="type" value="clean_up">
                                        <div class="flex gap-4 items-end">
                                            <div class="flex-1">
                                                <label class="block text-xs text-gray-500 mb-1">Inspector Name</label>
                                                <select name="worker_id" class="block w-full text-sm border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500" required>
                                                    <option value="">-- Select Inspector --</option>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $techCleanup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tech): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                        <option value="<?php echo e($tech->id); ?>"><?php echo e($tech->name); ?></option>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                                </select>
                                            </div>
                                            <button class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow transition-colors">
                                                MARK PASS
                                            </button>
                                        </div>
                                    </form>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                
                                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                     <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                        <span class="text-xs font-bold text-gray-500 uppercase block mb-2">Temuan Awal (Before)</span>
                                        <?php if (isset($component)) { $__componentOriginal0b55eebd37945af6c95e63b8e56be4d6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0b55eebd37945af6c95e63b8e56be4d6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.photo-uploader','data' => ['order' => $order,'step' => 'QC_CLEANUP_BEFORE']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('photo-uploader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['order' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($order),'step' => 'QC_CLEANUP_BEFORE']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0b55eebd37945af6c95e63b8e56be4d6)): ?>
<?php $attributes = $__attributesOriginal0b55eebd37945af6c95e63b8e56be4d6; ?>
<?php unset($__attributesOriginal0b55eebd37945af6c95e63b8e56be4d6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0b55eebd37945af6c95e63b8e56be4d6)): ?>
<?php $component = $__componentOriginal0b55eebd37945af6c95e63b8e56be4d6; ?>
<?php unset($__componentOriginal0b55eebd37945af6c95e63b8e56be4d6); ?>
<?php endif; ?>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                        <span class="text-xs font-bold text-gray-500 uppercase block mb-2">Bukti Pengecekan (After)</span>
                                        <?php if (isset($component)) { $__componentOriginal0b55eebd37945af6c95e63b8e56be4d6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0b55eebd37945af6c95e63b8e56be4d6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.photo-uploader','data' => ['order' => $order,'step' => 'QC_CLEANUP_AFTER']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('photo-uploader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['order' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($order),'step' => 'QC_CLEANUP_AFTER']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0b55eebd37945af6c95e63b8e56be4d6)): ?>
<?php $attributes = $__attributesOriginal0b55eebd37945af6c95e63b8e56be4d6; ?>
<?php unset($__attributesOriginal0b55eebd37945af6c95e63b8e56be4d6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0b55eebd37945af6c95e63b8e56be4d6)): ?>
<?php $component = $__componentOriginal0b55eebd37945af6c95e63b8e56be4d6; ?>
<?php unset($__componentOriginal0b55eebd37945af6c95e63b8e56be4d6); ?>
<?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. Final Check -->
                            <div class="relative pl-8 border-l-2 <?php echo e($subtasks['final']['done'] ? 'border-green-500' : 'border-gray-200'); ?>">
                                <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full <?php echo e($subtasks['final']['done'] ? 'bg-green-500' : 'bg-gray-300'); ?>"></div>
                                <h4 class="text-sm font-bold text-gray-800 mb-2">3. QC Akhir (Keseluruhan)</h4>
                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subtasks['final']['done']): ?>
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-3 flex justify-between items-center">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span class="font-bold text-green-700">PASSED</span>
                                        </div>
                                        <div class="text-right text-xs text-gray-500">
                                            <div>Verified at <?php echo e($subtasks['final']['end']->format('H:i')); ?></div>
                                            <div>Duration: <?php echo e($subtasks['final']['duration']); ?> m</div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <form action="<?php echo e(route('qc.update', $order->id)); ?>" method="POST" class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="type" value="final">
                                        <div class="flex gap-4 items-end">
                                            <div class="flex-1">
                                                <label class="block text-xs text-gray-500 mb-1">Final Inspector PIC</label>
                                                <select name="worker_id" class="block w-full text-sm border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500" required>
                                                    <option value="">-- Select Final PIC --</option>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $techFinal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tech): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                        <option value="<?php echo e($tech->id); ?>"><?php echo e($tech->name); ?></option>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                                </select>
                                            </div>
                                            <button class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow transition-colors">
                                                MARK PASS
                                            </button>
                                        </div>
                                    </form>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                
                                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                     <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                        <span class="text-xs font-bold text-gray-500 uppercase block mb-2">Temuan Awal (Before)</span>
                                        <?php if (isset($component)) { $__componentOriginal0b55eebd37945af6c95e63b8e56be4d6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0b55eebd37945af6c95e63b8e56be4d6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.photo-uploader','data' => ['order' => $order,'step' => 'QC_FINAL_BEFORE']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('photo-uploader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['order' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($order),'step' => 'QC_FINAL_BEFORE']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0b55eebd37945af6c95e63b8e56be4d6)): ?>
<?php $attributes = $__attributesOriginal0b55eebd37945af6c95e63b8e56be4d6; ?>
<?php unset($__attributesOriginal0b55eebd37945af6c95e63b8e56be4d6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0b55eebd37945af6c95e63b8e56be4d6)): ?>
<?php $component = $__componentOriginal0b55eebd37945af6c95e63b8e56be4d6; ?>
<?php unset($__componentOriginal0b55eebd37945af6c95e63b8e56be4d6); ?>
<?php endif; ?>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                        <span class="text-xs font-bold text-gray-500 uppercase block mb-2">Bukti Pengecekan (After)</span>
                                        <?php if (isset($component)) { $__componentOriginal0b55eebd37945af6c95e63b8e56be4d6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0b55eebd37945af6c95e63b8e56be4d6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.photo-uploader','data' => ['order' => $order,'step' => 'QC_FINAL_AFTER']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('photo-uploader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['order' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($order),'step' => 'QC_FINAL_AFTER']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0b55eebd37945af6c95e63b8e56be4d6)): ?>
<?php $attributes = $__attributesOriginal0b55eebd37945af6c95e63b8e56be4d6; ?>
<?php unset($__attributesOriginal0b55eebd37945af6c95e63b8e56be4d6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0b55eebd37945af6c95e63b8e56be4d6)): ?>
<?php $component = $__componentOriginal0b55eebd37945af6c95e63b8e56be4d6; ?>
<?php unset($__componentOriginal0b55eebd37945af6c95e63b8e56be4d6); ?>
<?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DECISION AREA -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- REJECT CARD -->
                        <div class="dashboard-card border-red-200 shadow-none border-2">
                            <div class="p-4 bg-red-50 border-b border-red-100">
                                <h3 class="font-bold text-red-800 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    Found Issues?
                                </h3>
                            </div>
                            <div class="p-4">
                                <form action="<?php echo e(route('qc.fail', $order->id)); ?>" method="POST" enctype="multipart/form-data">
                                    <?php echo csrf_field(); ?>
                                    <div class="mb-4">
                                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">Select Services to Reject:</label>
                                        <div class="space-y-2 bg-white p-3 rounded border border-gray-200 max-h-40 overflow-y-auto">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $order->workOrderServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                <label class="flex items-center p-2 hover:bg-red-50 rounded cursor-pointer transition-colors">
                                                    <input type="checkbox" name="rejected_services[]" value="<?php echo e($detail->id); ?>" class="rounded text-red-600 focus:ring-red-500 border-gray-300">
                                                    <div class="ml-2 text-sm text-gray-700">
                                                        <span class="font-medium"><?php echo e($detail->custom_service_name ?? ($detail->service ? $detail->service->name : 'Layanan')); ?></span>
                                                        <span class="text-xs text-gray-400 block">by <?php echo e($detail->technician ? $detail->technician->name : ($detail->technician_id ? \App\Models\User::find($detail->technician_id)->name : '-')); ?></span>
                                                    </div>
                                                </label>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Reason / Notes:</label>
                                        <input type="text" name="note" class="w-full text-sm border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" placeholder="e.g. Lem kurang rapi di bagian heel" required>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Evidence Photo (Optional):</label>
                                        <input type="file" name="evidence_photo" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100" accept="image/*">
                                    </div>

                                    <button class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg font-bold text-sm shadow hover:shadow-md transition-all">
                                        ⛔ REJECT & RETURN TO PRODUCTION
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- CX FOLLOW UP CARD -->
                        <div class="dashboard-card border-amber-200 shadow-none border-2">
                            <div class="p-4 bg-amber-50 border-b border-amber-100">
                                <h3 class="font-bold text-amber-800 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Need CX Followup?
                                </h3>
                            </div>
                            <div class="p-4">
                                <form action="<?php echo e(route('cx-issues.store')); ?>" method="POST" enctype="multipart/form-data">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="work_order_id" value="<?php echo e($order->id); ?>">
                                    
                                    <div class="mb-4">
                                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Category:</label>
                                        <select name="category" class="w-full text-sm border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500" required>
                                            <option value="">-- Select Category --</option>
                                            <option value="Teknis">Kendala Teknis (Konsultasi Customer)</option>
                                            <option value="Material">Masalah Material (Stok Habis/Beda)</option>
                                            <option value="Estimasi">Estimasi Waktu Meleset</option>
                                            <option value="Tambahan">Saran Tambah Jasa (Upsell)</option>
                                            <option value="Lainnya">Lainnya</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Description:</label>
                                        <textarea name="description" rows="2" class="w-full text-sm border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500" placeholder="Kirim pesan ke CX..." required></textarea>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Evidence Photo (Required):</label>
                                        <input type="file" name="photos[]" multiple class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100" accept="image/*" required>
                                    </div>

                                    <button class="w-full bg-amber-500 hover:bg-amber-600 text-white py-2 rounded-lg font-bold text-sm shadow hover:shadow-md transition-all">
                                        ⚠️ REPORT TO CX (PAUSE PROCESS)
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- APPROVE CARD -->
                        <div class="flex items-end justify-end">
                            <?php
                                $allDone = $subtasks['jahit']['done'] && $subtasks['clean_up']['done'] && $subtasks['final']['done'];
                            ?>
                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($allDone): ?>
                                <div class="w-full">
                                    <form action="<?php echo e(route('qc.pass', $order->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button class="w-full py-4 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-xl shadow-lg transform hover:-translate-y-1 transition-all flex flex-col items-center justify-center gap-1">
                                            <span class="font-black text-xl tracking-wide">APPROVE & FINISH</span>
                                            <span class="text-xs font-medium opacity-90">All checks passed. Move to Finish Station.</span>
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <div class="w-full text-center p-6 bg-gray-100 rounded-xl border border-gray-200 border-dashed text-gray-400">
                                    <span class="block font-bold text-sm mb-1">Approval Locked</span>
                                    <span class="text-xs">Complete all checklist items to approve.</span>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
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
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\qc\show.blade.php ENDPATH**/ ?>