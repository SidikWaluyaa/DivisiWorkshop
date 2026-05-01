<div class="p-6 space-y-4 bg-[#F8F9FA] min-h-screen relative font-sans">
    <!-- Header: Ultra Slim -->
    <div class="flex items-center justify-between max-w-[1600px] mx-auto">
        <div class="flex items-center space-x-3">
            <a href="<?php echo e(route('storage.purchase.index')); ?>" class="p-1.5 bg-white rounded-lg shadow-sm border border-gray-100 text-gray-400 hover:text-[#22AF85] transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-xl font-black text-gray-900 leading-none tracking-tight"><?php echo e($purchaseId ? 'EDIT' : 'BARU'); ?> <span class="text-[#22AF85]">BELANJA</span></h1>
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mt-1">GUDANG / MANAJEMEN SPK</p>
            </div>
        </div>
        
        <div class="bg-white px-4 py-1.5 rounded-xl border border-gray-100 shadow-sm flex items-center gap-3">
            <div class="text-right">
                <span class="text-[8px] font-black text-gray-300 uppercase tracking-widest block leading-none">SYSTEM ID</span>
                <p class="text-[11px] font-black text-[#22AF85] leading-none mt-1"><?php echo e($purchase_number); ?></p>
            </div>
            <div class="w-px h-6 bg-gray-100"></div>
            <div class="flex items-center gap-1.5">
                <div class="w-1.5 h-1.5 rounded-full <?php echo e($status === 'COMPLETED' ? 'bg-[#22AF85]' : 'bg-[#FFC232]'); ?>"></div>
                <span class="text-[9px] font-black text-gray-900 uppercase tracking-widest"><?php echo e($status); ?></span>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-4 max-w-[1600px] mx-auto pb-24">
        <!-- Main Form Data -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="space-y-1">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Nota Vendor</label>
                <input type="text" wire:model="external_reference" placeholder="..." 
                       class="w-full px-3 py-2 bg-gray-50 border-gray-100 rounded-lg focus:bg-white focus:ring-4 focus:ring-[#22AF85]/5 focus:border-[#22AF85] transition-all font-bold text-gray-700 text-xs">
            </div>

            <div class="space-y-1">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Tanggal</label>
                <input type="date" wire:model="purchase_date" 
                       class="w-full px-3 py-2 bg-gray-50 border-gray-100 rounded-lg focus:bg-white focus:ring-4 focus:ring-[#22AF85]/5 focus:border-[#22AF85] transition-all font-bold text-gray-700 text-xs uppercase">
            </div>

            <div class="space-y-1">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Prioritas</label>
                <select wire:model="purchase_type" class="w-full px-3 py-2 bg-gray-50 border-gray-100 rounded-lg focus:bg-white focus:ring-4 focus:ring-[#22AF85]/5 focus:border-[#22AF85] font-black text-gray-700 text-xs">
                    <option value="Reguler">Reguler</option>
                    <option value="Prioritas">Prioritas</option>
                    <option value="Urgent">Urgent</option>
                </select>
            </div>

            <div class="space-y-1">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Status</label>
                <select wire:model.live="status" 
                        class="w-full px-3 py-2 bg-gray-50 border-gray-100 rounded-lg focus:bg-white focus:ring-4 focus:ring-[#22AF85]/5 font-black text-xs
                        <?php echo e($status === 'COMPLETED' ? 'text-[#22AF85]' : ($status === 'CANCELLED' ? 'text-red-500' : 'text-gray-500')); ?>">
                    <option value="PENDING">PENDING</option>
                    <option value="PROCESSING">PROCESSING</option>
                    <option value="COMPLETED">COMPLETED</option>
                    <option value="CANCELLED">CANCELLED</option>
                </select>
            </div>
        </div>

        <!-- SPK GROUPS -->
        <div class="space-y-4">
            <div class="flex items-center justify-between px-1">
                <div class="flex items-center space-x-2">
                    <div class="w-1 h-4 bg-[#FFC232] rounded-full"></div>
                    <h2 class="text-[10px] font-black text-gray-900 uppercase tracking-widest">Grup SPK</h2>
                </div>
                <button type="button" wire:click="addSpkGroup" 
                        class="px-3 py-1.5 bg-[#22AF85]/5 border border-[#22AF85]/20 text-[#22AF85] font-black text-[9px] rounded-lg hover:bg-[#22AF85] hover:text-white transition-all uppercase tracking-widest flex items-center">
                    + GRUP SPK
                </button>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $spkGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gIndex => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-in fade-in duration-300">
                <div class="px-6 py-3 bg-gray-50/80 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-4 flex-1">
                        <div class="w-full max-w-[250px]">
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[#22AF85] font-black text-[10px]">SPK</span>
                                <input type="text" wire:model.live="spkGroups.<?php echo e($gIndex); ?>.spk_number" list="spks-<?php echo e($gIndex); ?>" 
                                       placeholder="KETIK NOMOR..."
                                       class="w-full pl-10 pr-4 py-1.5 bg-white border-gray-100 rounded-lg focus:border-[#22AF85] focus:ring-4 focus:ring-[#22AF85]/5 font-black text-sm text-[#22AF85] uppercase transition-all">
                                <datalist id="spks-<?php echo e($gIndex); ?>">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $allSpks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $spk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                        <option value="<?php echo e($spk); ?>">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </datalist>
                            </div>
                        </div>
                        <div class="h-6 w-px bg-gray-200"></div>
                        <div>
                            <p class="text-[10px] font-black text-gray-900 uppercase leading-none"><?php echo e(count($group['items'])); ?> Item</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" 
                                wire:click="openMaterialModal(<?php echo e($gIndex); ?>)"
                                class="px-4 py-2 bg-[#22AF85] text-white font-black text-[9px] rounded-lg shadow-sm hover:bg-[#1b8c6a] transition-all uppercase tracking-widest">
                            + PILIH MATERIAL
                        </button>
                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($spkGroups) > 1): ?>
                        <button type="button" wire:click="removeSpkGroup(<?php echo e($gIndex); ?>)" class="p-1.5 text-gray-300 hover:text-red-500 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <div class="px-6 py-4">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-gray-50">
                                <th class="pb-2 text-[8px] font-black text-gray-300 uppercase tracking-widest w-8 text-center">#</th>
                                <th class="pb-2 text-[8px] font-black text-gray-300 uppercase tracking-widest pl-2">MATERIAL</th>
                                <th class="pb-2 text-[8px] font-black text-gray-300 uppercase tracking-widest text-center w-24">QTY</th>
                                <th class="pb-2 text-[8px] font-black text-gray-300 uppercase tracking-widest text-right w-40">HARGA</th>
                                <th class="pb-2 text-[8px] font-black text-gray-300 uppercase tracking-widest text-right w-40">SUBTOTAL</th>
                                <th class="pb-2 text-[8px] font-black text-gray-300 uppercase tracking-widest w-12"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $group['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iIndex => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <tr class="hover:bg-gray-50/50 transition-all">
                                <td class="py-2.5 text-center text-[10px] font-black text-gray-200"><?php echo e($iIndex + 1); ?></td>
                                <td class="py-2.5 pl-2">
                                    <p class="text-xs font-black text-gray-700 uppercase tracking-tight"><?php echo e($item['material_name']); ?></p>
                                    <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">STOK: <?php echo e($item['material_stock']); ?></p>
                                </td>
                                <td class="py-2.5 text-center">
                                    <input type="number" wire:model.live="spkGroups.<?php echo e($gIndex); ?>.items.<?php echo e($iIndex); ?>.quantity" 
                                           class="w-full max-w-[80px] mx-auto px-2 py-1 bg-white border-gray-100 rounded-md focus:ring-2 focus:ring-[#22AF85]/10 font-black text-xs text-center text-gray-900 transition-all">
                                </td>
                                <td class="py-2.5 text-right">
                                    <div class="relative max-w-[140px] ml-auto">
                                        <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-300 font-bold text-[8px]">Rp</span>
                                        <input type="number" wire:model.live="spkGroups.<?php echo e($gIndex); ?>.items.<?php echo e($iIndex); ?>.price" 
                                               class="w-full pl-6 pr-2 py-1 bg-white border-gray-100 rounded-md focus:ring-2 focus:ring-[#22AF85]/10 font-black text-xs text-right text-gray-900 transition-all">
                                    </div>
                                </td>
                                <td class="py-2.5 text-right text-xs font-black text-gray-900">
                                    Rp <?php echo e(number_format($item['quantity'] * $item['price'], 0, ',', '.')); ?>

                                </td>
                                <td class="py-2.5 text-center">
                                    <button type="button" wire:click="removeMaterialFromGroup(<?php echo e($gIndex); ?>, <?php echo e($iIndex); ?>)" 
                                            class="p-1 text-gray-200 hover:text-red-500 transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </td>
                            </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr>
                                <td colspan="6" class="py-8 text-center text-[10px] font-black text-gray-300 uppercase tracking-widest italic">Belum ada material</td>
                            </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>

        <!-- Sticky Bottom Bar -->
        <div class="fixed bottom-4 right-6 left-auto z-50 w-[calc(100%-300px)] max-w-[1250px] bg-[#22AF85] p-3 rounded-2xl shadow-2xl flex items-center justify-between px-8 border border-white/10">
            <div class="flex items-center gap-10">
                <div class="text-white">
                    <span class="text-[8px] font-black uppercase tracking-[0.2em] opacity-60 leading-none">GRAND TOTAL</span>
                    <?php 
                        $grandTotal = 0;
                        foreach($spkGroups as $g) foreach($g['items'] as $i) $grandTotal += ($i['quantity'] * $i['price']);
                    ?>
                    <p class="text-2xl font-black tracking-tighter mt-1 leading-none">Rp <?php echo e(number_format($grandTotal, 0, ',', '.')); ?></p>
                </div>
                <div class="w-px h-8 bg-white/20"></div>
                <div class="flex items-center gap-6 text-white/80">
                    <div class="text-center">
                        <span class="text-[8px] font-black uppercase tracking-widest block opacity-60">GRUP</span>
                        <p class="text-sm font-black"><?php echo e(count($spkGroups)); ?></p>
                    </div>
                    <div class="text-center">
                        <span class="text-[8px] font-black uppercase tracking-widest block opacity-60">ITEM</span>
                        <?php $iCount = 0; foreach($spkGroups as $g) $iCount += count($g['items']); ?>
                        <p class="text-sm font-black"><?php echo e($iCount); ?></p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" 
                        class="px-10 py-3.5 bg-[#FFC232] text-gray-900 font-black rounded-xl shadow-xl hover:scale-[1.03] active:scale-95 transition-all text-center tracking-widest text-xs uppercase">
                    <?php echo e($purchaseId ? 'PERBARUI' : 'SIMPAN'); ?>

                </button>
            </div>
        </div>
    </form>

    <!-- MATERIAL MODAL -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showMaterialModal): ?>
    <style>body { overflow: hidden !important; }</style>
    <div class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm animate-in fade-in" wire:click="$set('showMaterialModal', false)"></div>
        <div class="relative bg-white w-full max-w-lg max-h-[70vh] rounded-2xl shadow-2xl flex flex-col overflow-hidden animate-in zoom-in-95">
            <div class="p-4 border-b border-gray-50 flex items-center justify-between bg-gray-50/30">
                <p class="text-xs font-black text-gray-900 uppercase tracking-widest">PILIH MATERIAL</p>
                <button type="button" wire:click="$set('showMaterialModal', false)" class="text-gray-300 hover:text-red-500 transition-all">&times;</button>
            </div>
            <div class="p-4 bg-white border-b border-gray-50">
                <input type="text" wire:model.live="checklistSearch" placeholder="Cari..." 
                       class="w-full px-4 py-2 bg-gray-50 border-gray-100 rounded-lg focus:bg-white font-bold text-xs">
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-1.5 custom-scrollbar">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $modalMaterials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                <button type="button" wire:click="toggleChecklist(<?php echo e($material->id); ?>)"
                        class="w-full flex items-center justify-between p-3 rounded-lg border transition-all
                        <?php echo e(in_array($material->id, $selectedChecklist) ? 'bg-[#22AF85]/5 border-[#22AF85]' : 'bg-white border-gray-50 hover:border-gray-100'); ?>">
                    <div class="flex items-center text-left">
                        <div class="w-6 h-6 rounded flex items-center justify-center mr-3 <?php echo e(in_array($material->id, $selectedChecklist) ? 'bg-[#22AF85] text-white' : 'bg-gray-100'); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($material->id, $selectedChecklist)): ?> <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div>
                            <p class="font-black text-gray-700 text-[11px] uppercase block leading-none"><?php echo e($material->name); ?></p>
                            <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-1">STOK: <?php echo e($material->stock); ?></p>
                        </div>
                    </div>
                </button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
            <div class="p-4 border-t border-gray-50 flex items-center justify-between bg-gray-50/30">
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest"><?php echo e(count($selectedChecklist)); ?> DIPILIH</span>
                <button type="button" wire:click="addFromChecklist" class="px-6 py-2 bg-[#FFC232] text-gray-900 font-black rounded-lg text-[10px] uppercase">TAMBAHKAN</button>
            </div>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views/livewire/warehouse/purchase/form.blade.php ENDPATH**/ ?>