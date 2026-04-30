<div class="min-h-screen bg-[#F8FAFC] pb-20">
    
    <div class="max-w-7xl mx-auto px-8 pt-8">
        <div class="flex items-center justify-between mb-10">
            <div class="space-y-1">
                <nav class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">
                    <a href="<?php echo e(route('material-requests.index')); ?>" wire:navigate class="hover:text-[#22AF85] transition-colors">Procurement</a>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                    <span class="text-[#22AF85]">New Request</span>
                </nav>
                <h1 class="text-4xl font-black text-gray-900 tracking-tight">Manual Request</h1>
                <p class="text-sm text-gray-500 font-medium">Create a new shopping or production material requisition.</p>
            </div>

            <div class="hidden md:flex items-center gap-4">
                <a href="<?php echo e(route('material-requests.index')); ?>" wire:navigate class="px-6 py-3 bg-white border border-gray-200 text-gray-500 text-sm font-bold rounded-2xl hover:bg-gray-50 transition-all">
                    Cancel
                </a>
                <button wire:click="submit" wire:loading.attr="disabled" class="px-8 py-3 bg-[#FFC232] text-gray-900 text-sm font-black rounded-2xl hover:shadow-xl hover:shadow-[#FFC232]/20 transition-all flex items-center gap-3">
                    <span wire:loading.remove>Submit Request</span>
                    <span wire:loading class="w-4 h-4 border-2 border-gray-900/30 border-t-gray-900 rounded-full animate-spin"></span>
                    <svg wire:loading.remove class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            
            <div class="lg:col-span-1 space-y-8">
                <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm space-y-8">
                    <div class="space-y-4">
                        <label class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em]">Request Type</label>
                        <div class="grid grid-cols-2 gap-3 p-1.5 bg-gray-50 rounded-2xl border border-gray-100">
                            <button wire:click="$set('type', 'SHOPPING')" 
                                    class="py-3.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all <?php echo e($type === 'SHOPPING' ? 'bg-white text-[#22AF85] shadow-sm ring-1 ring-gray-100' : 'text-gray-400 hover:text-gray-600'); ?>">
                                Shopping
                            </button>
                            <button wire:click="$set('type', 'PRODUCTION_PO')" 
                                    class="py-3.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all <?php echo e($type === 'PRODUCTION_PO' ? 'bg-white text-[#22AF85] shadow-sm ring-1 ring-gray-100' : 'text-gray-400 hover:text-gray-600'); ?>">
                                Production PO
                            </button>
                        </div>
                        <p class="text-[10px] text-gray-400 font-bold italic">
                            <?php echo e($type === 'SHOPPING' ? 'Sifatnya umum (stok gudang/belanja rutin).' : 'Spesifik untuk kebutuhan satu SPK/Order.'); ?>

                        </p>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'PRODUCTION_PO'): ?>
                        <div class="space-y-4">
                            <label class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em]">Checklist SPK (Pending PO)</label>
                            
                            <div class="space-y-3 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $pendingOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <div wire:click="toggleSpk(<?php echo e($order->id); ?>)" 
                                         class="group flex items-center justify-between p-4 rounded-2xl border transition-all cursor-pointer <?php echo e(in_array($order->id, $selectedSpks) ? 'bg-[#22AF85]/10 border-[#22AF85] shadow-sm' : 'bg-gray-50 border-gray-100 hover:border-gray-200'); ?>">
                                        <div class="flex items-center gap-4">
                                            <div class="w-5 h-5 rounded-md border-2 flex items-center justify-center transition-all <?php echo e(in_array($order->id, $selectedSpks) ? 'bg-[#22AF85] border-[#22AF85]' : 'bg-white border-gray-200 group-hover:border-gray-300'); ?>">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($order->id, $selectedSpks)): ?>
                                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"/></svg>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-xs font-black <?php echo e(in_array($order->id, $selectedSpks) ? 'text-[#22AF85]' : 'text-gray-900'); ?>">SPK #<?php echo e($order->spk_number); ?></span>
                                                <div class="flex flex-col">
                                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter"><?php echo e($order->customer_name); ?></span>
                                                    
                                                    <span class="text-[10px] font-semibold text-gray-600 mt-1.5 line-clamp-1">
                                                        <span class="text-[#22AF85]">Butuh:</span> <?php echo e($order->materials->pluck('name')->join(', ')); ?>

                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex flex-col items-end">
                                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest bg-white border border-gray-100 px-2 py-0.5 rounded-full ring-1 ring-black/5">
                                                <?php echo e($order->materials->count()); ?> Items
                                            </span>
                                        </div>
                                    </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    <div class="py-10 text-center bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">No pending shortages found</p>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="space-y-4">
                        <label class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em]">Notes / Remarks</label>
                        <textarea wire:model="notes" rows="4" placeholder="Alasan pengajuan atau instruksi khusus..." 
                                  class="w-full p-6 border-gray-100 bg-gray-50 rounded-2xl text-xs font-bold text-gray-700 focus:ring-2 focus:ring-[#22AF85]/20 focus:border-[#22AF85] transition-all resize-none"></textarea>
                    </div>
                </div>
            </div>

            
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden min-h-[500px] flex flex-col">
                    <div class="p-10 border-b border-gray-50">
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="text-lg font-black text-gray-900">Requested Items</h3>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100">
                                <?php echo e(count($selectedItems)); ?> Materials Selected
                            </span>
                        </div>

                        
                        <div class="relative max-w-xl">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="searchMaterial" placeholder="Cari material (contoh: Lem, Tali, Insole)..." 
                                   class="w-full pl-12 pr-5 py-4 border-gray-100 bg-gray-50 rounded-2xl text-sm font-bold text-gray-800 placeholder:text-gray-400 focus:ring-2 focus:ring-[#22AF85]/20 focus:border-[#22AF85] transition-all ring-1 ring-black/5 shadow-inner">
                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($materialResults)): ?>
                                <div class="absolute left-0 right-0 mt-3 bg-white rounded-3xl shadow-2xl border border-gray-100 z-50 overflow-hidden ring-1 ring-black/5">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $materialResults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                        <button wire:click="addItem(<?php echo e($material['id']); ?>)" 
                                                class="w-full flex items-center justify-between px-8 py-5 hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-none text-left group">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-[#22AF85]">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-black text-gray-900 group-hover:text-[#22AF85] transition-colors"><?php echo e($material['name']); ?></span>
                                                    <span class="text-[10px] font-bold text-gray-400 flex items-center gap-2">
                                                        <?php echo e($material['type']); ?> • <?php echo e($material['unit']); ?>

                                                        <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                                        Stok: <?php echo e($material['stock']); ?>

                                                    </span>
                                                </div>
                                            </div>
                                            <div class="text-sm font-black text-gray-900">
                                                Rp <?php echo e(number_format($material['price'])); ?>

                                            </div>
                                        </button>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="flex-1">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($selectedItems)): ?>
                            <div class="h-full flex flex-col items-center justify-center p-20 text-center opacity-40">
                                <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                </div>
                                <h4 class="text-sm font-black text-gray-600 uppercase tracking-widest">Belum ada item</h4>
                                <p class="text-xs font-bold text-gray-400 mt-2">Gunakan kolom pencarian di atas untuk menambahkan barang.</p>
                            </div>
                        <?php else: ?>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead>
                                        <tr class="bg-gray-50/50 border-b border-gray-50">
                                            <th class="px-10 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Material</th>
                                            <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Quantity</th>
                                            <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">Est. Price</th>
                                            <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">Subtotal</th>
                                            <th class="px-10 py-5 text-right w-20"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $selectedItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                            <tr class="hover:bg-gray-50 transition-colors group">
                                                <td class="px-10 py-6">
                                                    <div class="flex items-center gap-4">
                                                        <div class="w-10 h-10 bg-[#22AF85]/10 rounded-xl flex items-center justify-center text-[#22AF85]">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                                        </div>
                                                        <span class="text-sm font-black text-gray-900 group-hover:text-[#22AF85]"><?php echo e($item['name']); ?></span>
                                                    </div>
                                                </td>
                                                <td class="px-8 py-6">
                                                    <div class="flex items-center justify-center">
                                                        <div class="inline-flex items-center bg-gray-50 rounded-xl p-1 border border-gray-100 ring-1 ring-black/5">
                                                            <button wire:click="$set('selectedItems.<?php echo e($index); ?>.quantity', <?php echo e(max(1, $item['quantity'] - 1)); ?>)" class="w-8 h-8 rounded-lg hover:bg-white flex items-center justify-center text-gray-400 hover:text-gray-900 transition-all">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"/></svg>
                                                            </button>
                                                            <input type="number" wire:model.live="selectedItems.<?php echo e($index); ?>.quantity" class="w-14 text-center border-none bg-transparent text-xs font-black text-gray-900 p-0 focus:ring-0">
                                                            <button wire:click="$set('selectedItems.<?php echo e($index); ?>.quantity', <?php echo e($item['quantity'] + 1); ?>)" class="w-8 h-8 rounded-lg hover:bg-white flex items-center justify-center text-gray-400 hover:text-gray-900 transition-all">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                                                            </button>
                                                        </div>
                                                        <span class="ml-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest"><?php echo e($item['unit']); ?></span>
                                                    </div>
                                                </td>
                                                <td class="px-8 py-6 text-right">
                                                    <span class="text-sm font-bold text-gray-400 tabular-nums">Rp <?php echo e(number_format($item['price'])); ?></span>
                                                </td>
                                                <td class="px-8 py-6 text-right">
                                                    <span class="text-sm font-black text-gray-900 tabular-nums">Rp <?php echo e(number_format($item['price'] * $item['quantity'])); ?></span>
                                                </td>
                                                <td class="px-10 py-6 text-right">
                                                    <button wire:click="removeItem(<?php echo e($index); ?>)" class="p-2 text-gray-300 hover:text-rose-500 transition-colors">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($selectedItems)): ?>
                        <div class="px-10 py-8 bg-gray-50/50 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between gap-6">
                            <div class="flex items-center gap-10">
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Items</span>
                                    <span class="text-lg font-black text-gray-900"><?php echo e(collect($selectedItems)->sum('quantity')); ?> Items</span>
                                </div>
                                <div class="w-px h-10 bg-gray-200"></div>
                                <div class="flex flex-col text-right md:text-left">
                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Estimated Total Cost</span>
                                    <span class="text-2xl font-black text-[#22AF85] tabular-nums">Rp <?php echo e(number_format(collect($selectedItems)->sum(fn($i) => $i['price'] * $i['quantity']))); ?></span>
                                </div>
                            </div>
                            
                            <button wire:click="submit" wire:loading.attr="disabled" class="w-full md:w-auto px-10 py-4 bg-[#FFC232] text-gray-900 text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl hover:shadow-xl hover:shadow-[#FFC232]/20 transition-all flex items-center justify-center gap-3">
                                <span wire:loading.remove>Generate Request</span>
                                <span wire:loading class="w-4 h-4 border-2 border-gray-900/30 border-t-gray-900 rounded-full animate-spin"></span>
                                <svg wire:loading.remove class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;300;400;500;600;700;800;900&display=swap');
    
    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    [x-cloak] { display: none !important; }
    
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\livewire\procurement\create.blade.php ENDPATH**/ ?>