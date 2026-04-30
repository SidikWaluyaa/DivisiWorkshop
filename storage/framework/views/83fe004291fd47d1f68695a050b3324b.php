<div class="procurement-show-root min-h-screen bg-[#FDFDFD] pb-12">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
        
        :root {
            --primary-green: #22AF85;
            --accent-yellow: #FFC232;
        }

        .procurement-show-root {
            font-family: 'Inter', sans-serif;
        }

        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f9fafb; transition: all 0.3s; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: var(--primary-green); }
    </style>

    
    <div x-data="{ show: false, message: '', type: 'success' }" 
         x-on:notify.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 3000)"
         x-show="show" x-transition 
         class="fixed top-24 right-8 z-50 pointer-events-none">
        <div :class="type === 'success' ? 'bg-[#22AF85]' : (type === 'error' ? 'bg-rose-600' : 'bg-blue-600')" 
             class="px-6 py-4 rounded-2xl shadow-2xl text-white font-black text-sm flex items-center gap-3">
             <template x-if="type === 'success'"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></template>
             <span x-text="message"></span>
        </div>
    </div>

    
    <div class="bg-white border-b border-gray-100 px-8 py-6 flex items-center justify-between sticky top-0 z-30 shadow-sm">
        <div class="flex items-center gap-6">
            <a href="<?php echo e(route('material-requests.index')); ?>" wire:navigate class="p-2.5 bg-gray-50 border border-gray-200 text-gray-400 hover:text-[#22AF85] rounded-xl transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-black text-gray-900 tracking-tight"><?php echo e($materialRequest->request_number); ?></h1>
                    <?php
                        $statusColor = match($materialRequest->status) {
                            'PENDING' => 'bg-amber-500',
                            'APPROVED', 'PURCHASED' => 'bg-[#22AF85]',
                            'REJECTED', 'CANCELLED' => 'bg-rose-500',
                            default => 'bg-gray-400'
                        };
                    ?>
                    <span class="px-3 py-1 rounded-lg <?php echo e($statusColor); ?> text-white text-[10px] font-black uppercase tracking-widest shadow-lg shadow-<?php echo e(str_starts_with($statusColor, 'bg-') ? substr($statusColor, 3) : $statusColor); ?>/20">
                        <?php echo e($materialRequest->status); ?>

                    </span>
                </div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1">
                    <?php echo e($materialRequest->type == 'SHOPPING' ? 'Shopping Request' : 'Production PO'); ?> • <?php echo e($materialRequest->created_at->format('d M Y, H:i')); ?>

                </p>
            </div>
        </div>

        <div class="flex items-center gap-3">
             <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($materialRequest->status === 'PENDING'): ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manageInventory', \App\Models\WorkOrder::class)): ?>
                    <button wire:click="approve" wire:loading.attr="disabled" class="px-6 py-3 bg-[#FFC232] text-gray-900 text-sm font-black rounded-xl hover:shadow-lg hover:shadow-[#FFC232]/20 transition-all flex items-center gap-2 border-none">
                        <svg wire:loading.remove class="w-4 h-4 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        <span wire:loading class="w-4 h-4 border-2 border-gray-900/30 border-t-gray-900 rounded-full animate-spin"></span>
                        Approve Request
                    </button>
                    <button wire:click="reject" wire:loading.attr="disabled" class="px-6 py-3 bg-white border border-rose-100 text-rose-600 text-sm font-black rounded-xl hover:bg-rose-50 transition-all shadow-sm">
                        Reject
                    </button>
                <?php endif; ?>
                <button wire:click="cancel" class="px-6 py-3 bg-gray-50 border border-gray-200 text-gray-400 text-sm font-bold rounded-xl hover:bg-gray-100 transition-all">
                    Batalkan
                </button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($materialRequest->status === 'APPROVED'): ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manageInventory', \App\Models\WorkOrder::class)): ?>
                    <button wire:click="markAsPurchased" wire:loading.attr="disabled" class="px-6 py-3 bg-[#FFC232] text-gray-900 text-sm font-black rounded-xl hover:shadow-lg hover:shadow-[#FFC232]/20 transition-all flex items-center gap-2 border-none">
                        <svg wire:loading.remove class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <span wire:loading class="w-4 h-4 border-2 border-gray-900/30 border-t-gray-900 rounded-full animate-spin"></span>
                        Mark as Purchased
                    </button>
                <?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-8 pt-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            
            <div class="lg:col-span-8 space-y-10">
                
                <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-10">
                    <h3 class="text-lg font-black text-gray-900 tracking-tight mb-8">Informasi Pengajuan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                        <div class="flex flex-col gap-1">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Requested By</span>
                            <span class="text-base font-bold text-gray-800"><?php echo e($materialRequest->requestedBy->name ?? 'System'); ?></span>
                            <span class="text-xs text-gray-400 font-medium"><?php echo e($materialRequest->requestedBy->email ?? '-'); ?></span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Audit Status</span>
                            <div class="flex items-center gap-2 mt-1">
                                <div class="w-2 h-2 rounded-full <?php echo e($statusColor); ?>"></div>
                                <span class="text-sm font-black text-gray-700 uppercase tracking-widest"><?php echo e($materialRequest->status); ?></span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1 text-right">
                            <span class="text-[10px] font-black text-[#22AF85] uppercase tracking-[0.2em]">Estimated Value</span>
                            <span class="text-2xl font-black text-[#22AF85]">Rp <?php echo e(number_format($materialRequest->total_estimated_cost, 0, ',', '.')); ?></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-10 mt-10 pt-10 border-t border-gray-50">
                        <div class="flex flex-col gap-1">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Work Order Context</span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($materialRequest->work_order_id): ?>
                                <span class="text-sm font-bold text-gray-800 uppercase"><?php echo e($materialRequest->workOrder->spk_number); ?></span>
                                <span class="text-xs text-gray-400 font-medium"><?php echo e($materialRequest->workOrder->customer_name); ?></span>
                            <?php else: ?>
                                <span class="text-sm font-bold text-gray-400 italic">No WO linked</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Approved At</span>
                            <span class="text-sm font-bold text-gray-800"><?php echo e($materialRequest->approved_at ? $materialRequest->approved_at->format('d M Y • H:i') : '-'); ?></span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($materialRequest->approved_by): ?>
                                <span class="text-xs text-[#22AF85] font-bold">By <?php echo e($materialRequest->approvedBy->name); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="flex flex-col gap-1 text-right">
                             <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Procurement Type</span>
                             <span class="text-sm font-bold text-gray-800 uppercase tracking-widest"><?php echo e($materialRequest->type); ?></span>
                        </div>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($materialRequest->notes): ?>
                        <div class="mt-10 p-6 bg-gray-50 rounded-2xl border border-gray-100 flex gap-4">
                            <svg class="w-6 h-6 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                            <p class="text-sm text-gray-600 leading-relaxed italic">"<?php echo e($materialRequest->notes); ?>"</p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-10 py-8 border-b border-gray-100 bg-gray-50/30">
                        <h3 class="text-lg font-black text-gray-900 tracking-tight">Daftar Material</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-white">
                                    <th class="px-10 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Material & Specs</th>
                                    <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Qty</th>
                                    <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">Price</th>
                                    <th class="px-10 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $materialRequest->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-10 py-6">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-black text-gray-800"><?php echo e($item->material_name); ?></span>
                                                <span class="text-xs text-gray-400 mt-0.5"><?php echo e($item->specification ?? 'Standard Specs'); ?></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-6 text-center">
                                            <span class="px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-black rounded-lg border border-gray-200">
                                                <?php echo e($item->quantity); ?> <?php echo e($item->unit ?? 'Unit'); ?>

                                            </span>
                                        </td>
                                        <td class="px-6 py-6 text-right">
                                            <span class="text-sm font-bold text-gray-600">Rp <?php echo e(number_format($item->estimated_price, 0, ',', '.')); ?></span>
                                        </td>
                                        <td class="px-10 py-6 text-right">
                                            <span class="text-base font-black text-gray-900">Rp <?php echo e(number_format($item->getSubtotal(), 0, ',', '.')); ?></span>
                                        </td>
                                    </tr>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-50/50">
                                    <td colspan="3" class="px-10 py-8 text-right font-black text-gray-500 uppercase tracking-widest text-xs">Total Estimate Value</td>
                                    <td class="px-10 py-8 text-right font-black text-2xl text-[#22AF85]">Rp <?php echo e(number_format($materialRequest->total_estimated_cost, 0, ',', '.')); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            
            <div class="lg:col-span-4 space-y-8">
                <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-8 sticky top-32">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-lg font-black text-gray-900 tracking-tight">Riwayat Aktivitas</h3>
                        <div class="w-2 h-2 rounded-full bg-[#22AF85] animate-pulse"></div>
                    </div>

                    <div class="space-y-8 max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($materialRequest->workOrder && $materialRequest->workOrder->logs): ?>
                             <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $materialRequest->workOrder->logs->whereIn('action', ['APPROVED', 'REJECTED', 'PURCHASED', 'CANCELLED'])->sortByDesc('created_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <div class="relative pl-8 group">
                                    
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$loop->last): ?>
                                        <div class="absolute left-[3.5px] top-4 bottom-[-32px] w-[1px] bg-gray-100"></div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    
                                    
                                    <div class="absolute left-0 top-1.5 w-2 h-2 rounded-full border-2 border-white ring-4 ring-gray-50 bg-gray-300 group-hover:ring-[#22AF85]/10 group-hover:bg-[#22AF85] transition-all"></div>
                                    
                                    <div class="flex flex-col">
                                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1.5"><?php echo e($log->created_at->format('d M, H:i')); ?></div>
                                        <div class="text-xs font-black text-gray-800 uppercase tracking-tight"><?php echo e($log->action); ?></div>
                                        <p class="text-xs text-gray-500 mt-1 leading-relaxed"><?php echo e($log->description); ?></p>
                                        <div class="text-[9px] font-bold text-gray-400 mt-2 flex items-center gap-1">
                                            <div class="w-4 h-4 rounded-full bg-gray-100 flex items-center justify-center text-[7px]"><?php echo e(substr($log->user->name ?? 'S', 0, 1)); ?></div>
                                            <?php echo e($log->user->name ?? 'System'); ?>

                                        </div>
                                    </div>
                                </div>
                             <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <?php else: ?>
                            <div class="text-center py-10 opacity-40">
                                <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-xs font-bold uppercase tracking-widest">No matching history</p>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        
                        <div class="relative pl-8 group">
                            <div class="absolute left-0 top-1.5 w-2 h-2 rounded-full border-2 border-white ring-4 ring-gray-50 bg-[#22AF85]"></div>
                            <div class="flex flex-col">
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1.5"><?php echo e($material_activity_date ?? $materialRequest->created_at->format('d M, H:i')); ?></div>
                                <div class="text-xs font-black text-gray-800 uppercase tracking-tight">CONCEIVED</div>
                                <p class="text-xs text-gray-500 mt-1 leading-relaxed">Request created by <?php echo e($materialRequest->requestedBy->name ?? 'System'); ?>.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\livewire\procurement\show.blade.php ENDPATH**/ ?>