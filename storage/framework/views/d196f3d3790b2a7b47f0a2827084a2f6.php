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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?php echo e(__('Data SPK CS (Transit Log)')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-[2rem] shadow-xl p-6 border-l-8 border-[#22AF85]">
                    <div class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Total SPK Dibuat</div>
                    <div class="text-3xl font-black text-gray-900 leading-none"><?php echo e($totalSpk); ?></div>
                </div>
                <div class="bg-white rounded-[2rem] shadow-xl p-6 border-l-8 border-[#FFC232]">
                    <div class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Menunggu Handover (Gudang)</div>
                    <div class="text-3xl font-black text-gray-900 leading-none"><?php echo e($waitingHandover); ?></div>
                </div>
                <div class="bg-white rounded-[2rem] shadow-xl p-6 border-l-8 border-[#22AF85]">
                    <div class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Total Nilai Transaksi</div>
                    <div class="text-3xl font-black text-gray-900 leading-none">Rp <?php echo e(number_format($totalRevenue, 0, ',', '.')); ?></div>
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] shadow-xl overflow-hidden border border-gray-100" x-data="{ selected: [], selectAll: false }">
                <div class="p-8 bg-white border-b border-gray-50">
                    
                    
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-8">
                        <form method="GET" class="flex-1 grid grid-cols-1 md:grid-cols-4 gap-3 w-full">
                            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari No SPK / Customer..." class="rounded-2xl border-none bg-gray-50 px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-[#22AF85] shadow-sm">
                            
                            <select name="status" class="rounded-2xl border-none bg-gray-50 px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-[#22AF85] shadow-sm">
                                <option value="">Semua Status</option>
                                <option value="WAITING_DP" <?php echo e(request('status') == 'WAITING_DP' ? 'selected' : ''); ?>>Menunggu DP</option>
                                <option value="DP_PAID" <?php echo e(request('status') == 'DP_PAID' ? 'selected' : ''); ?>>DP Lunas</option>
                                <option value="HANDED_TO_WORKSHOP" <?php echo e(request('status') == 'HANDED_TO_WORKSHOP' ? 'selected' : ''); ?>>Masuk Gudang</option>
                            </select>
                            
                            <div class="flex gap-2 col-span-1 md:col-span-2">
                                <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>" class="flex-1 rounded-2xl border-none bg-gray-50 px-4 py-3 text-sm font-bold shadow-sm">
                                <button type="submit" class="px-8 bg-[#22AF85] text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:shadow-lg transition">
                                    Filter
                                </button>
                            </div>
                        </form>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($spks) > 0): ?>
                        <div x-show="selected.length > 0" x-transition class="flex items-center gap-3">
                            <span class="text-[10px] font-black uppercase text-gray-400"><span x-text="selected.length"></span> Item Terpilih</span>
                            <form action="<?php echo e(route('cs.spk.bulk-destroy')); ?>" method="POST" onsubmit="return confirm('Hapus SPK yang dipilih?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <template x-for="id in selected" :key="id">
                                    <input type="hidden" name="ids[]" :value="id">
                                </template>
                                <button type="submit" class="bg-red-500 text-white px-6 py-3 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl hover:bg-red-600 transition">
                                    🗑️ Hapus Banyak
                                </button>
                            </form>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-gray-50 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                    <th class="px-6 py-4">
                                        <input type="checkbox" x-model="selectAll" @change="selected = selectAll ? [<?php echo e($spks->pluck('id')->implode(',')); ?>] : []" class="rounded text-[#22AF85] focus:ring-[#22AF85]">
                                    </th>
                                    <th class="px-6 py-4">No SPK</th>
                                    <th class="px-6 py-4">Customer</th>
                                    <th class="px-6 py-4 text-right">Total & DP</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $spks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $spk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <tr class="hover:bg-gray-50/50 transition group">
                                    <td class="px-6 py-4">
                                        <input type="checkbox" :value="<?php echo e($spk->id); ?>" x-model="selected" class="rounded text-[#22AF85] focus:ring-[#22AF85]">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($spk->lead): ?>
                                            <a href="<?php echo e(route('cs.leads.show', $spk->lead->id)); ?>" class="font-black text-[#22AF85] bg-[#22AF85]/5 px-3 py-1 rounded-lg border border-[#22AF85]/20 hover:bg-[#22AF85]/10 transition">
                                                <?php echo e($spk->spk_number); ?>

                                            </a>
                                        <?php else: ?>
                                            <span class="font-black text-gray-400 bg-gray-100 px-3 py-1 rounded-lg border border-gray-200 cursor-not-allowed">
                                                <?php echo e($spk->spk_number); ?> (Lead Deleted)
                                            </span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <div class="text-[10px] text-gray-400 font-bold mt-1"><?php echo e($spk->created_at->format('d M Y H:i')); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-black text-gray-900"><?php echo e($spk->lead?->customer_name ?? 'Unknown Customer'); ?></div>
                                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter"><?php echo e($spk->lead?->customer_phone ?? '-'); ?></div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="font-black text-gray-900">Rp <?php echo e(number_format($spk->total_price, 0, ',', '.')); ?></div>
                                        <div class="text-[10px] font-bold text-gray-400">DP: Rp <?php echo e(number_format($spk->dp_amount, 0, ',', '.')); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-[9px] font-black uppercase tracking-widest rounded-full <?php echo e($spk->status_badge_class); ?>">
                                            <?php echo e($spk->label); ?>

                                        </span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($spk->work_order_id && $spk->workOrder): ?>
                                            <div class="text-[9px] text-[#22AF85] font-black mt-1 uppercase">To: <?php echo e($spk->workOrder->spk_number); ?></div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex justify-end gap-2">
                                            <a href="<?php echo e(route('cs.spk.export-pdf', $spk->id)); ?>" target="_blank" class="p-2 bg-gray-50 text-gray-400 hover:bg-[#22AF85]/10 hover:text-[#22AF85] rounded-xl transition shadow-sm" title="Download PDF">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path></svg>
                                            </a>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$spk->work_order_id && $spk->lead): ?>
                                                <a href="<?php echo e(route('cs.leads.show', $spk->lead->id)); ?>" class="p-2 bg-gray-50 text-gray-400 hover:bg-[#FFC232]/10 hover:text-[#FFC232] rounded-xl transition shadow-sm" title="Handover">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                                                </a>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-20 h-20 bg-gray-50 rounded-[2rem] flex items-center justify-center text-gray-200 mb-4">
                                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </div>
                                            <p class="font-black text-gray-400 uppercase tracking-widest text-xs">Belum ada data SPK</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-8">
                        <?php echo e($spks->links()); ?>

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
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views/cs/spk/index.blade.php ENDPATH**/ ?>