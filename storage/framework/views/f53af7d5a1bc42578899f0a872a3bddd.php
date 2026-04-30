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
        <div class="flex justify-between items-center gap-4">
            <div class="flex items-center gap-4">
                <a href="<?php echo e(route('revision.index')); ?>" class="p-2 bg-white/10 hover:bg-white/20 rounded-full transition-colors text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <h2 class="font-black text-2xl text-white leading-tight tracking-tight">
                    <?php echo e(__('Detail Revisi Teknik')); ?>

                </h2>
            </div>
            <div class="flex items-center gap-3">
                 <span class="bg-white/20 px-4 py-1.5 rounded-full text-sm font-bold font-mono border border-white/30 tracking-wider text-white">
                    <?php echo e($revision->workOrder->spk_number); ?>

                </span>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                
                <div class="lg:col-span-2 space-y-8">
                    
                    <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-[2.5rem] overflow-hidden border border-gray-100 dark:border-gray-700">
                        <div class="p-10">
                            <h3 class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.3em] mb-6">Deskripsi Masalah & Komplain</h3>
                            <div class="prose dark:prose-invert max-w-none">
                                <div class="bg-red-50/50 dark:bg-red-900/5 rounded-3xl p-8 border border-red-100/50 dark:border-red-900/10">
                                    <p class="text-xl text-gray-700 dark:text-gray-300 leading-relaxed italic font-medium">
                                        "<?php echo e($revision->description); ?>"
                                    </p>
                                </div>
                            </div>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($revision->photo_urls && count($revision->photo_urls) > 0): ?>
                        <div class="px-10 pb-10">
                            <h3 class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.3em] mb-6">Foto Dokumentasi Masalah (<?php echo e(count($revision->photo_urls)); ?>)</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $revision->photo_urls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <div class="rounded-[2rem] overflow-hidden border-4 border-gray-50 dark:border-gray-700 shadow-inner group relative aspect-video bg-gray-100 dark:bg-gray-900">
                                    <img src="<?php echo e($url); ?>" 
                                         alt="Foto Revisi" 
                                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-6">
                                        <a href="<?php echo e($url); ?>" target="_blank" class="bg-white text-gray-900 px-4 py-2 rounded-full font-black uppercase text-[10px] tracking-widest flex items-center gap-2 shadow-2xl transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Buka Ukuran Penuh
                                        </a>
                                    </div>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <div class="space-y-8">
                    
                    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-[2rem] p-8 border border-gray-100 dark:border-gray-700">
                        <h3 class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-6">Status Revisi</h3>
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 rounded-2xl bg-red-100 dark:bg-red-900/30 text-red-600 flex items-center justify-center text-2xl">
                                ⏳
                            </div>
                            <div>
                                <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Saat Ini</p>
                                <p class="text-lg font-black text-red-600 uppercase">SEDANG DIREVISI</p>
                            </div>
                        </div>

                        <form action="<?php echo e(route('revision.complete', $revision->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-2xl py-5 font-black uppercase text-xs tracking-[0.2em] shadow-xl shadow-green-200 dark:shadow-none hover:scale-[1.02] active:scale-[0.98] transition-all">
                                Selesai Revisi ✅
                            </button>
                        </form>
                    </div>

                    
                    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-[2rem] p-8 border border-gray-100 dark:border-gray-700 space-y-8">
                        
                        <div>
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Customer</h4>
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center font-bold text-gray-500">
                                    <?php echo e(substr($revision->workOrder->customer_name, 0, 1)); ?>

                                </div>
                                <div>
                                    <p class="text-sm font-black text-gray-800 dark:text-gray-200 leading-tight"><?php echo e($revision->workOrder->customer_name); ?></p>
                                    <p class="text-[10px] text-gray-400 font-medium"><?php echo e($revision->workOrder->customer_phone); ?></p>
                                </div>
                            </div>
                        </div>

                        
                        <div>
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Unit Sepatu</h4>
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-4 flex items-center gap-4 border border-gray-100 dark:border-gray-700">
                                <div class="text-2xl">👟</div>
                                <div>
                                    <p class="text-xs font-black text-gray-800 dark:text-gray-200 uppercase tracking-tight"><?php echo e($revision->workOrder->shoe_brand); ?></p>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase"><?php echo e($revision->workOrder->shoe_color); ?></p>
                                </div>
                            </div>
                        </div>

                        
                        <div>
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Dilaporkan Oleh</h4>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 flex items-center justify-center text-xs font-bold border border-indigo-100/50">
                                        <?php echo e(substr($revision->creator->name ?? '?', 0, 1)); ?>

                                    </div>
                                    <span class="text-xs font-bold text-gray-600 dark:text-gray-400"><?php echo e($revision->creator->name ?? 'System'); ?></span>
                                </div>
                                <span class="text-[10px] font-black text-gray-400 uppercase"><?php echo e($revision->created_at->diffForHumans()); ?></span>
                            </div>
                        </div>
                    </div>

                    
                    <div class="text-center">
                         <a href="<?php echo e(route('finish.show', $revision->work_order_id)); ?>" class="text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-indigo-500 transition-colors">
                            Lihat Detail SPK Lengkap
                        </a>
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
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\revision\show.blade.php ENDPATH**/ ?>