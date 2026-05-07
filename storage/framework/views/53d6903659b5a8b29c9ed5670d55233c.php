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

    <div class="min-h-screen bg-[#F8FAFC]">
        
        <div class="bg-white/90 shadow-2xl border-b border-gray-100 sticky top-0 z-40 backdrop-blur-xl">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-5 sm:py-8">
                <div class="flex flex-col gap-5 sm:gap-8">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3 sm:gap-6">
                            <div class="p-2.5 sm:p-4 bg-gray-900 rounded-xl sm:rounded-[1.5rem] shadow-xl transform -rotate-3 hover:rotate-0 transition-transform duration-500">
                                <svg class="w-5 h-5 sm:w-8 sm:h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 sm:gap-3 mb-0.5 sm:mb-1">
                                    <span class="text-[8px] sm:text-[10px] font-black bg-gray-100 text-gray-600 px-1.5 sm:px-2 py-0.5 rounded-md uppercase tracking-widest italic border border-gray-200">AUDIT COMPLETED</span>
                                    <h1 class="text-xl sm:text-4xl font-black text-gray-900 tracking-tighter leading-none italic uppercase">Riwayat Audit CS</h1>
                                </div>
                                <p class="text-gray-400 text-[9px] sm:text-[11px] font-black uppercase tracking-[0.2em] sm:tracking-[0.3em] italic opacity-70 hidden sm:block">Log Pembayaran CS yang Sudah Terverifikasi Finance</p>
                            </div>
                        </div>

                        
                        <div class="flex bg-gray-100 p-1 rounded-2xl border border-gray-200 shadow-inner">
                            <a href="<?php echo e(route('finance.cs-verification')); ?>" class="px-6 py-2.5 text-xs font-black uppercase tracking-widest text-gray-400 hover:text-gray-900 transition-all italic">Pending</a>
                            <a href="<?php echo e(route('finance.cs-verification.history')); ?>" class="px-6 py-2.5 text-xs font-black uppercase tracking-widest bg-white text-orange-600 rounded-xl shadow-md border border-orange-100 italic">History</a>
                        </div>
                    </div>

                    
                    <form action="<?php echo e(route('finance.cs-verification.history')); ?>" method="GET" class="flex items-center gap-3">
                        <div class="relative group/search flex-1">
                            <input type="text" 
                                   name="search" 
                                   value="<?php echo e($search); ?>" 
                                   placeholder="Cari Riwayat Audit..." 
                                   class="w-full pl-12 pr-6 py-3 sm:py-4 bg-gray-50 border-2 border-transparent rounded-xl sm:rounded-[2rem] focus:bg-white focus:border-orange-500/20 focus:ring-4 focus:ring-orange-500/5 text-sm font-black italic tracking-tight placeholder-gray-300 transition-all duration-500 shadow-inner">
                            <svg class="w-5 h-5 text-gray-300 absolute left-4 sm:left-6 top-1/2 -translate-y-1/2 group-focus-within/search:text-orange-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <button type="submit" class="px-8 py-4 bg-gray-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-orange-600 transition-all shadow-xl active:scale-95">Cari Log</button>
                    </form>
                </div>
            </div>
        </div>

        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-12">
            <div class="bg-white rounded-[2.5rem] shadow-2xl border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] italic">Waktu Verifikasi</th>
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] italic">Data SPK & Pelanggan</th>
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] italic text-right">Nominal</th>
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] italic">Metode</th>
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] italic">Catatan Audit</th>
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] italic">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <tr class="hover:bg-orange-50/30 transition-colors group">
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-black text-gray-900 italic tracking-tighter"><?php echo e($payment->updated_at->format('d M Y')); ?></span>
                                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5"><?php echo e($payment->updated_at->format('H:i')); ?> WIB</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col gap-1">
                                            <a href="<?php echo e(route('finance.show', $payment->work_order_id)); ?>" class="text-xs font-black text-[#1B8A68] hover:underline uppercase italic tracking-tight"><?php echo e($payment->spk_number_snapshot); ?></a>
                                            <span class="text-[11px] font-bold text-gray-500 italic"><?php echo e($payment->customer_name_snapshot); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <span class="text-lg font-black text-gray-900 italic tracking-tighter">Rp <?php echo e(number_format($payment->amount_total, 0, ',', '.')); ?></span>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span class="px-2.5 py-1 bg-gray-100 text-gray-600 text-[9px] font-black rounded-lg uppercase tracking-widest italic border border-gray-200"><?php echo e($payment->payment_method); ?></span>
                                    </td>
                                    <td class="px-8 py-6">
                                        <p class="text-[11px] text-gray-500 font-medium italic line-clamp-2 max-w-xs">"<?php echo e($payment->notes); ?>"</p>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <div class="flex items-center gap-2 text-[#1B8A68]">
                                            <div class="w-6 h-6 rounded-full bg-[#1B8A68]/10 flex items-center justify-center">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                            <span class="text-[10px] font-black uppercase tracking-widest italic">Verified</span>
                                        </div>
                                    </td>
                                </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <tr>
                                    <td colspan="6" class="px-8 py-24 text-center">
                                        <div class="w-20 h-20 bg-gray-50 rounded-[1.5rem] flex items-center justify-center text-4xl mb-6 mx-auto grayscale opacity-20">📜</div>
                                        <h3 class="text-xl font-black text-gray-900 mb-1 uppercase tracking-tighter italic">Belum Ada Riwayat</h3>
                                        <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest italic opacity-60">Log audit pembayaran akan muncul di sini setelah diverifikasi.</p>
                                    </td>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payments->hasPages()): ?>
                    <div class="p-8 border-t border-gray-50">
                        <?php echo e($payments->links()); ?>

                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views/finance/cs_history.blade.php ENDPATH**/ ?>