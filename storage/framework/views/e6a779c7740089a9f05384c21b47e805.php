<div class="p-8 space-y-8 bg-[#FBFBFB] min-h-screen relative font-sans print:bg-white print:p-0">
    <!-- Global Print Guard -->
    <style>
        @media print {
            nav, header, aside, [role="navigation"], .main-header, .sidebar { display: none !important; }
            .print\:hidden { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .no-print { display: none !important; }
        }
    </style>
    <!-- Header: Ultra Slim Premium -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 max-w-[1600px] mx-auto">
        <div>
            <div class="flex items-center space-x-3 mb-2">
                <div class="w-1.5 h-6 bg-[#FFC232] rounded-full"></div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">RIWAYAT <span class="text-[#22AF85]">MUTASI</span></h1>
            </div>
            <p class="text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] ml-5">LOG PERGERAKAN MATERIAL WORKSHOP</p>
        </div>
        
        <div class="flex items-center gap-3">
            <div class="bg-white px-5 py-2 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="text-right">
                    <span class="text-[8px] font-black text-gray-300 uppercase tracking-widest block leading-none">TOTAL LOG</span>
                    <p class="text-sm font-black text-gray-900 leading-none mt-1"><?php echo e($transactions->total()); ?></p>
                </div>
                <div class="w-px h-8 bg-gray-100"></div>
                <button onclick="window.print()" class="p-2 text-gray-400 hover:text-gray-900 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Premium Filters: Hidden on Print -->
    <div class="max-w-[1600px] mx-auto bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 grid grid-cols-1 md:grid-cols-5 gap-6 print:hidden">
        <div class="space-y-1.5">
            <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Cari Catatan / SPK</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" wire:model.live="search" placeholder="SPK-XXXX..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-4 focus:ring-[#22AF85]/5 focus:border-[#22AF85] transition-all font-bold text-gray-700 text-xs">
            </div>
        </div>

        <div class="space-y-1.5">
            <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Pilih Material</label>
            <select wire:model.live="materialId" class="w-full px-4 py-2.5 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-4 focus:ring-[#22AF85]/5 focus:border-[#22AF85] font-black text-gray-700 text-xs">
                <option value="">Semua Material</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <option value="<?php echo e($material->id); ?>"><?php echo e($material->name); ?></option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </select>
        </div>

        <div class="space-y-1.5">
            <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Tipe Mutasi</label>
            <div class="grid grid-cols-3 gap-1 bg-gray-50 p-1 rounded-xl">
                <button wire:click="$set('type', '')" class="py-1.5 rounded-lg text-[8px] font-black uppercase tracking-widest transition-all <?php echo e($type == '' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-400'); ?>">ALL</button>
                <button wire:click="$set('type', 'IN')" class="py-1.5 rounded-lg text-[8px] font-black uppercase tracking-widest transition-all <?php echo e($type == 'IN' ? 'bg-[#22AF85] text-white' : 'text-gray-400'); ?>">IN</button>
                <button wire:click="$set('type', 'OUT')" class="py-1.5 rounded-lg text-[8px] font-black uppercase tracking-widest transition-all <?php echo e($type == 'OUT' ? 'bg-rose-500 text-white' : 'text-gray-400'); ?>">OUT</button>
            </div>
        </div>

        <div class="space-y-1.5">
            <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Dari Tanggal</label>
            <input type="date" wire:model.live="startDate" class="w-full px-4 py-2.5 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-4 focus:ring-[#22AF85]/5 font-black text-gray-700 text-xs uppercase">
        </div>

        <div class="space-y-1.5">
            <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Sampai Tanggal</label>
            <input type="date" wire:model.live="endDate" class="w-full px-4 py-2.5 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-4 focus:ring-[#22AF85]/5 font-black text-gray-700 text-xs uppercase">
        </div>
    </div>

    <!-- Data Table: Ultra Slim -->
    <div class="max-w-[1600px] mx-auto bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-8 py-5 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] w-48">Waktu & Tanggal</th>
                        <th class="px-8 py-5 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Material Workshop</th>
                        <th class="px-8 py-5 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] text-center w-24">Tipe</th>
                        <th class="px-8 py-5 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] text-center w-32">Kuantitas</th>
                        <th class="px-8 py-5 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Keterangan Transaksi</th>
                        <th class="px-8 py-5 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] w-48">Operator</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <tr class="group hover:bg-[#FBFBFB] transition-all">
                        <td class="px-8 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-1.5 h-8 <?php echo e($trx->type == 'IN' ? 'bg-[#22AF85]' : 'bg-rose-500'); ?> rounded-full opacity-0 group-hover:opacity-100 transition-all"></div>
                                <div>
                                    <p class="text-xs font-black text-gray-900 tracking-tight leading-none uppercase"><?php echo e($trx->created_at->translatedFormat('d M Y')); ?></p>
                                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1.5"><?php echo e($trx->created_at->format('H:i:s')); ?> WIB</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-4">
                            <p class="text-sm font-black text-gray-800 uppercase tracking-tight leading-none"><?php echo e($trx->material->name); ?></p>
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-[0.2em] mt-1.5 italic"><?php echo e($trx->material->unit); ?></p>
                        </td>
                        <td class="px-8 py-4 text-center">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($trx->type == 'IN'): ?>
                                <span class="px-4 py-1.5 bg-[#22AF85]/10 text-[#22AF85] rounded-full text-[9px] font-black uppercase tracking-widest border border-[#22AF85]/20">MASUK</span>
                            <?php else: ?>
                                <span class="px-4 py-1.5 bg-rose-50 text-rose-500 rounded-full text-[9px] font-black uppercase tracking-widest border border-rose-100">KELUAR</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td class="px-8 py-4 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-sm font-black tracking-tight <?php echo e($trx->type == 'IN' ? 'text-[#22AF85]' : 'text-rose-500'); ?>">
                                    <?php echo e($trx->type == 'IN' ? '+' : '-'); ?> <?php echo e(number_format($trx->quantity, 0, ',', '.')); ?>

                                </span>
                            </div>
                        </td>
                        <td class="px-8 py-4">
                            <div class="flex items-center gap-2">
                                <div class="px-2 py-1 bg-gray-50 rounded text-[9px] font-black text-gray-400 uppercase tracking-widest border border-gray-100 italic">
                                    <?php echo e($trx->notes ?: 'TANPA CATATAN'); ?>

                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-7 h-7 rounded-full bg-gray-900 flex items-center justify-center text-[10px] font-black text-white uppercase shadow-sm">
                                    <?php echo e(substr($trx->user->name ?? '?', 0, 1)); ?>

                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-900 uppercase leading-none"><?php echo e($trx->user->name ?? 'SYSTEM'); ?></p>
                                    <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-1">OPERATOR GUDANG</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <tr>
                        <td colspan="6" class="px-8 py-24 text-center">
                            <div class="flex flex-col items-center opacity-20">
                                <svg class="w-20 h-20 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-xl font-black text-gray-900 uppercase tracking-widest">BELUM ADA MUTASI</p>
                                <p class="font-medium text-gray-500">Log pergerakan barang akan muncul di sini</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100">
            <?php echo e($transactions->links()); ?>

        </div>
    </div>

    <!-- Custom Style for Modernity -->
    <style>
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(0.5) sepia(1) saturate(5) hue-rotate(110deg);
            cursor: pointer;
        }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #22AF85; border-radius: 10px; }
    </style>
</div>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views/livewire/warehouse/history.blade.php ENDPATH**/ ?>