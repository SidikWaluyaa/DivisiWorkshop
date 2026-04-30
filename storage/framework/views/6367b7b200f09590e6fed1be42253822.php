<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'order', 
    'type', 
    'technicians',
    'titleAction' => 'Assign',
    'techByRelation', // e.g., 'prepWashingBy'
    'startedAtColumn', // e.g., 'prep_washing_started_at'
    'byColumn' // e.g., 'prep_washing_by'
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'order', 
    'type', 
    'technicians',
    'titleAction' => 'Assign',
    'techByRelation', // e.g., 'prepWashingBy'
    'startedAtColumn', // e.g., 'prep_washing_started_at'
    'byColumn' // e.g., 'prep_washing_by'
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div id="spk-<?php echo e($order->spk_number); ?>" 
     x-data="{ 
         showPhotos: false, 
         showFinishModal: false, 
         finishDate: '<?php echo e(now()->format('Y-m-d\TH:i')); ?>',
         isHighlighted: false,
         init() {
             const urlParams = new URLSearchParams(window.location.search);
             const hl = urlParams.get('highlight');
             if (hl === '<?php echo e($order->spk_number); ?>') {
                 this.isHighlighted = true;
                 setTimeout(() => {
                     this.$el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                 }, 500);
                 setTimeout(() => {
                     this.isHighlighted = false;
                 }, 5000);
             }
         }
     }" 
     :class="{ 'ring-4 ring-yellow-400 bg-yellow-50/50 scale-[1.01] shadow-2xl z-10' : isHighlighted }"
     class="group hover:bg-gradient-to-r hover:from-gray-50 hover:to-white transition-all duration-500 relative">
    
    <!-- Marker Label if highlighted -->
    <div x-show="isHighlighted" style="display: none;" class="absolute -top-3 left-4 bg-yellow-400 text-yellow-900 px-3 py-1 rounded-full text-[10px] font-black tracking-wider shadow-md z-20 animate-bounce">
        TARGET PENCARIAN
    </div>
    <div class="p-5 flex items-start gap-4">
        
        <div class="flex flex-col items-center gap-3 pt-1">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showCheckbox ?? false): ?>
                <input type="checkbox" value="<?php echo e($order->id); ?>" 
                       @change="$store.preparation.toggle('<?php echo e($order->id); ?>')" 
                       :checked="$store.preparation.includes('<?php echo e($order->id); ?>')"
                       class="w-5 h-5 text-teal-600 rounded-md border-2 border-gray-300 focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 cursor-pointer transition-all hover:border-teal-400 shadow-sm">
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 text-gray-700 flex items-center justify-center font-black text-sm border-2 border-gray-300 shadow-sm group-hover:scale-110 transition-transform">
                <?php echo e($loopIteration ?? $order->id); ?>

            </div>
        </div>

        
        <div class="flex-1 min-w-0">
            
            <div class="flex items-start justify-between gap-4 mb-3">
                <div class="flex items-center gap-3 flex-wrap">
                    
                    <div class="font-mono font-black text-base text-gray-800 bg-gradient-to-r from-gray-100 to-gray-50 px-3 py-1.5 rounded-lg border-2 border-gray-200 shadow-sm">
                        <?php echo e($order->spk_number); ?>

                    </div>
                    
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($order->priority, ['Prioritas', 'Urgent', 'Express'])): ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-red-500 to-red-600 text-white shadow-md animate-pulse">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            URGENT
                        </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div class="mb-3">
                <div class="flex items-center gap-2 mb-1">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="font-bold text-gray-900"><?php echo e($order->customer_name); ?></span>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                    <span><?php echo e($order->shoe_brand); ?> <?php echo e($order->shoe_type); ?> - <?php echo e($order->shoe_color); ?></span>
                </div>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->technician_notes): ?>
                    <div class="mt-2 p-2 bg-amber-50 border-l-4 border-amber-500 rounded-r text-xs text-amber-900 font-medium">
                        <span class="block font-bold text-amber-600 uppercase text-[10px] tracking-wide mb-0.5">⚠️ Instruksi Teknisi:</span>
                        <?php echo e($order->technician_notes); ?>

                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->notes): ?>
                    <div class="mt-1.5 text-[10px] text-gray-400 italic">
                        <strong class="text-gray-500">CS Note:</strong> "<?php echo e(Str::limit($order->notes, 50)); ?>"
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <?php
                    $resolvedIssue = $order->cxIssues->where('status', 'RESOLVED')->last();
                ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($resolvedIssue): ?>
                    <div class="mt-2 p-2 bg-purple-50 border-l-4 border-purple-500 rounded-r text-xs text-purple-900 font-medium">
                        <span class="block font-bold text-purple-600 uppercase text-[10px] tracking-wide mb-0.5">⚠️ Riwayat Follow Up CX:</span>
                        <div class="italic">"<?php echo e($resolvedIssue->resolution_notes ?? $resolvedIssue->description ?? '-'); ?>"</div>
                        <div class="mt-1 text-[9px] text-purple-500 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Done by <?php echo e($resolvedIssue->resolver->name ?? 'System'); ?> • <?php echo e($resolvedIssue->updated_at->format('d/M H:i')); ?>

                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div class="flex flex-wrap gap-2 mb-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $order->workOrderServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <?php
                        // Determine color based on service category
                        // WorkOrderService stores category_name directly or we can access via relation
                        $category = $detail->category_name ?? ($detail->service ? $detail->service->category : 'Unknown');
                        $name = $detail->custom_service_name ?? ($detail->service ? $detail->service->name : 'Layanan');
                        
                        $serviceColor = 'gray';
                        if (stripos($category, 'Cleaning') !== false || stripos($name, 'Cleaning') !== false || stripos($category, 'Treatment') !== false) {
                            $serviceColor = 'teal';
                        } elseif (stripos($category, 'Sol') !== false || stripos($name, 'Sol') !== false) {
                            $serviceColor = 'orange';
                        } elseif (stripos($category, 'Upper') !== false || stripos($category, 'Repaint') !== false) {
                            $serviceColor = 'purple';
                        } elseif (stripos($category, 'Production') !== false) {
                            $serviceColor = 'blue';
                        }
                    ?>
                    
                    <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-lg border-2 shadow-sm
                        bg-<?php echo e($serviceColor); ?>-50 text-<?php echo e($serviceColor); ?>-700 border-<?php echo e($serviceColor); ?>-200
                    ">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <?php echo e($name); ?>

                    </span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>

            
            <div class="flex items-center gap-2 flex-wrap">
                
                <button @click="showPhotos = !showPhotos" 
                        :class="showPhotos ? 'bg-teal-100 text-teal-700 border-teal-300' : 'bg-gray-100 text-gray-600 border-gray-300 hover:bg-gray-200'" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold border-2 transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Photos
                </button>

                
                <button type="button" @click.stop="$dispatch('open-report-modal', <?php echo e($order->id); ?>)" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border-2 border-amber-200 hover:bg-amber-100 transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Report
                </button>
            </div>
        </div>

        
        <div class="flex flex-col items-end gap-2 min-w-[200px]">
            <?php
                $techId = $order->{$byColumn};
                $techName = $order->{$techByRelation}->name ?? '...';
                $startedAt = $order->{$startedAtColumn};
                
                // Dynamic color classes based on type
                $colorClass = match($type) {
                    'washing' => 'teal',
                    'sol' => 'orange',
                    'upper' => 'purple',
                    default => 'gray'
                };
            ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$techId): ?>
                
                <div class="flex flex-col gap-2 w-full">
                    <select id="tech-<?php echo e($type); ?>-<?php echo e($order->id); ?>" class="text-sm border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full font-medium shadow-sm">
                        <option value="">-- Select Technician --</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $technicians; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <option value="<?php echo e($t->id); ?>"><?php echo e($t->name); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                    <button type="button" @click="window.updateStation(<?php echo e($order->id); ?>, '<?php echo e($type); ?>', 'start')" 
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg text-sm font-bold uppercase tracking-wide transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                        </svg>
                        Assign & Start
                    </button>
                </div>
            <?php else: ?>
                
                <div class="flex flex-col gap-2 w-full">
                    
                    <div class="bg-<?php echo e($colorClass); ?>-50 border-2 border-<?php echo e($colorClass); ?>-200 rounded-lg p-3 shadow-sm">
                        <div class="text-[10px] text-<?php echo e($colorClass); ?>-600 font-bold uppercase tracking-wider mb-1">Technician</div>
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 rounded-full bg-<?php echo e($colorClass); ?>-200 flex items-center justify-center text-<?php echo e($colorClass); ?>-700 font-bold text-sm">
                                <?php echo e(substr($techName, 0, 1)); ?>

                            </div>
                            <div class="flex-1">
                                <div class="font-bold text-sm text-<?php echo e($colorClass); ?>-900"><?php echo e($techName); ?></div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($startedAt): ?>
                                    <div class="text-[10px] text-<?php echo e($colorClass); ?>-600 font-medium">
                                        Started: <?php echo e($startedAt->format('H:i')); ?>

                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                        
                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($startedAt): ?>
                            <div class="flex items-center gap-2 pt-2 border-t border-<?php echo e($colorClass); ?>-200">
                                <svg class="w-4 h-4 text-<?php echo e($colorClass); ?>-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div class="flex-1">
                                    <div class="text-[9px] text-<?php echo e($colorClass); ?>-600 font-semibold uppercase">Elapsed Time</div>
                                    <div class="font-mono font-black text-sm" 
                                         data-started-at="<?php echo e($startedAt->toIso8601String()); ?>">
                                        Calculating...
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    
                    
                    <button type="button" @click="showFinishModal = true" 
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-lg text-sm font-bold uppercase tracking-wide transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Mark Complete
                    </button>
                    
                    
                    <div x-show="showFinishModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display: none;" x-transition>
                        <div class="bg-white rounded-lg shadow-xl p-4 w-80" @click.away="showFinishModal = false">
                            <h3 class="font-bold text-gray-800 mb-2">Konfirmasi Selesai</h3>
                            <p class="text-xs text-gray-600 mb-3">Masukkan tanggal & jam selesai aktual:</p>
                            <input type="datetime-local" x-model="finishDate" class="w-full text-sm border-gray-300 rounded mb-4 focus:ring-green-500 focus:border-green-500">
                            <div class="flex justify-end gap-2">
                                <button @click="showFinishModal = false" class="px-3 py-1.5 bg-gray-200 text-gray-700 rounded text-xs font-bold">Batal</button>
                                <button @click="window.updateStation(<?php echo e($order->id); ?>, '<?php echo e($type); ?>', 'finish', finishDate)" class="px-3 py-1.5 bg-green-600 text-white rounded text-xs font-bold">Simpan & Selesai</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <!-- Photo Section -->
    <div x-show="showPhotos" class="px-4 pb-4 bg-gray-50/50" style="display: none;" x-transition>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-2">Before (Awal)</span>
                <?php if (isset($component)) { $__componentOriginal0b55eebd37945af6c95e63b8e56be4d6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0b55eebd37945af6c95e63b8e56be4d6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.photo-uploader','data' => ['order' => $order,'step' => strtoupper('PREP_' . $type . '_BEFORE')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('photo-uploader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['order' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($order),'step' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(strtoupper('PREP_' . $type . '_BEFORE'))]); ?>
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
            <div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-2">After (Akhir)</span>
                <?php if (isset($component)) { $__componentOriginal0b55eebd37945af6c95e63b8e56be4d6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0b55eebd37945af6c95e63b8e56be4d6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.photo-uploader','data' => ['order' => $order,'step' => strtoupper('PREP_' . $type . '_AFTER')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('photo-uploader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['order' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($order),'step' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(strtoupper('PREP_' . $type . '_AFTER'))]); ?>
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
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\preparation\partials\station-card.blade.php ENDPATH**/ ?>