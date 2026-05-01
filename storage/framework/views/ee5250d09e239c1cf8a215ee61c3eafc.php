<div class="p-6 space-y-6 bg-[#F8F9FA] min-h-screen relative font-sans print:bg-white print:p-0">
    <!-- Global Print Reset -->
    <style>
        @media print {
            nav, header, aside, [role="navigation"], .main-header, .sidebar { display: none !important; }
            .print\:hidden { display: none !important; }
            body { background: white !important; padding: 0 !important; }
        }
    </style>
    <!-- Action Bar: Hidden on Print -->
    <div class="flex items-center justify-between max-w-[1000px] mx-auto print:hidden">
        <div class="flex items-center space-x-3">
            <a href="<?php echo e(route('storage.purchase.index')); ?>" class="p-2 bg-white rounded-xl shadow-sm border border-gray-100 text-gray-400 hover:text-[#22AF85] transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h1 class="text-xl font-black text-gray-900 uppercase tracking-tight">Rincian <span class="text-[#22AF85]">Belanja</span></h1>
        </div>
        
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-6 py-2.5 bg-gray-900 text-white font-black text-[11px] rounded-xl shadow-lg hover:bg-black transition-all uppercase tracking-widest flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 00-2 2h2m24 0v-5a2 2 0 012-2h10a2 2 0 012 2v5m-16 0h12"></path></svg>
                CETAK LAPORAN
            </button>
            <a href="<?php echo e(route('storage.purchase.edit', $purchase->id)); ?>" class="px-6 py-2.5 bg-[#FFC232] text-gray-900 font-black text-[11px] rounded-xl shadow-lg hover:scale-105 transition-all uppercase tracking-widest">
                EDIT DATA
            </a>
        </div>
    </div>

    <!-- MAIN REPORT CONTAINER -->
    <div class="max-w-[1000px] mx-auto bg-white rounded-[2rem] shadow-xl border border-gray-100 overflow-hidden print:shadow-none print:border-none print:max-w-full">
        <!-- Report Header -->
        <div class="p-10 border-b-4 border-[#22AF85] bg-gray-50/50 relative overflow-hidden print:p-6 print:bg-white">
            <div class="absolute top-0 right-0 w-64 h-64 bg-[#22AF85]/5 rounded-full -mr-32 -mt-32 print:hidden"></div>
            
            <div class="relative z-10 flex justify-between items-start">
                <div>
                    <h2 class="text-3xl font-black text-gray-900 tracking-tighter leading-none">PURCHASE ORDER</h2>
                    <p class="text-[11px] font-bold text-[#22AF85] uppercase tracking-[0.3em] mt-2">DOKUMEN BELANJA MATERIAL GUDANG</p>
                    
                </div>

                <div class="text-right">
                    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm print:p-0 print:border-none print:shadow-none">
                        <div class="mb-4">
                            <span class="text-[9px] font-black text-gray-300 uppercase tracking-widest block leading-none">NOMOR TRANSAKSI</span>
                            <p class="text-lg font-black text-[#22AF85] leading-none mt-1"><?php echo e($purchase->purchase_number); ?></p>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-left">
                            <div>
                                <span class="text-[8px] font-black text-gray-300 uppercase tracking-widest block">TANGGAL</span>
                                <p class="text-[11px] font-black text-gray-700 uppercase"><?php echo e($purchase->purchase_date->format('d M Y')); ?></p>
                            </div>
                            <div>
                                <span class="text-[8px] font-black text-gray-300 uppercase tracking-widest block">STATUS</span>
                                <p class="text-[11px] font-black text-[#22AF85] uppercase"><?php echo e($purchase->status); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block">REF. VENDOR / NOTA</span>
                        <p class="text-sm font-black text-gray-900 uppercase"><?php echo e($purchase->external_reference ?: '-'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Content -->
        <div class="p-10 space-y-10 print:p-6 print:space-y-6">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $spkGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b-2 border-gray-100 pb-2">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-[#FFC232]"></div>
                        <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest">ALOKASI SPK: <span class="text-[#22AF85]"><?php echo e($group['spk_number'] ?: 'STOK UMUM'); ?></span></h3>
                    </div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest"><?php echo e(count($group['items'])); ?> Material</span>
                </div>

                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 print:bg-white print:border-b print:border-gray-200">
                            <th class="px-4 py-2.5 text-[9px] font-black text-gray-400 uppercase tracking-widest w-12 text-center">NO</th>
                            <th class="px-4 py-2.5 text-[9px] font-black text-gray-400 uppercase tracking-widest">NAMA MATERIAL</th>
                            <th class="px-4 py-2.5 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center">QUANTITY</th>
                            <th class="px-4 py-2.5 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right">HARGA SATUAN</th>
                            <th class="px-4 py-2.5 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right">SUBTOTAL</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $group['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <tr class="border-b border-gray-50 last:border-none">
                            <td class="px-4 py-3 text-center text-[11px] font-black text-gray-300"><?php echo e($index + 1); ?></td>
                            <td class="px-4 py-3">
                                <p class="text-sm font-black text-gray-800 uppercase tracking-tight"><?php echo e($item->material->name); ?></p>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-sm font-black text-gray-900"><?php echo e($item->quantity); ?></span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="text-sm font-black text-gray-900">Rp <?php echo e(number_format($item->price, 0, ',', '.')); ?></span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="text-sm font-black text-[#22AF85]">Rp <?php echo e(number_format($item->subtotal, 0, ',', '.')); ?></span>
                            </td>
                        </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50/50 font-black">
                            <td colspan="4" class="px-4 py-3 text-right text-[10px] text-gray-400 uppercase tracking-widest">TOTAL UNTUK SPK INI</td>
                            <td class="px-4 py-3 text-right text-sm text-[#22AF85]">
                                Rp <?php echo e(number_format($group['items']->sum('subtotal'), 0, ',', '.')); ?>

                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

            <!-- Final Summary -->
            <div class="mt-12 flex justify-end print:mt-8">
                <div class="w-full max-w-md space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex justify-between items-center px-4 py-2 bg-gray-50 rounded-xl">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">TOTAL SPK</span>
                            <span class="text-sm font-black text-gray-900"><?php echo e(count($spkGroups)); ?> Grup</span>
                        </div>
                        <div class="flex justify-between items-center px-4 py-2 bg-gray-50 rounded-xl">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">TOTAL ITEM</span>
                            <span class="text-sm font-black text-gray-900"><?php echo e($purchase->items->count()); ?> Material</span>
                        </div>
                    </div>
                    <div class="flex justify-between items-center px-4 py-4 bg-[#22AF85] rounded-2xl shadow-lg shadow-[#22AF85]/20 text-white print:bg-white print:text-black print:border-2 print:border-black print:shadow-none">
                        <span class="text-xs font-black uppercase tracking-widest">GRAND TOTAL BELANJA</span>
                        <span class="text-2xl font-black tracking-tighter">Rp <?php echo e(number_format($purchase->total_amount, 0, ',', '.')); ?></span>
                    </div>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($purchase->notes): ?>
            <div class="mt-8 p-6 bg-gray-50 rounded-2xl border border-dashed border-gray-200 print:p-4 print:bg-white">
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-2">CATATAN KHUSUS:</span>
                <p class="text-xs font-bold text-gray-600 leading-relaxed italic">"<?php echo e($purchase->notes); ?>"</p>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        </div>

        <!-- Footer Decoration -->
        <div class="h-2 bg-gray-50 w-full print:hidden"></div>
        <div class="p-6 text-center text-gray-300 print:p-2 print:text-gray-400">
            <p class="text-[8px] font-bold uppercase tracking-[0.4em]">Dokumen ini digenerate secara otomatis melalui Sistem Workshop v2.0 - <?php echo e(now()->format('d/m/Y H:i')); ?></p>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views/livewire/warehouse/purchase/detail.blade.php ENDPATH**/ ?>