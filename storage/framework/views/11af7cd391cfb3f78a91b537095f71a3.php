<div class="p-8 space-y-8 bg-[#FBFBFB] min-h-screen">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">MANAJEMEN <span class="text-[#22AF85]">BELANJA</span></h1>
            <p class="text-gray-500 font-medium">Pencatatan & Monitoring Material Masuk</p>
        </div>
        <a href="<?php echo e(route('storage.purchase.create')); ?>" 
           class="px-6 py-3 bg-[#FFC232] text-gray-900 font-bold rounded-2xl shadow-[0_8px_20px_-6px_rgba(255,194,50,0.5)] hover:scale-105 transition-all flex items-center group">
            <div class="bg-white/20 p-1 rounded-lg mr-3 group-hover:rotate-90 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
            </div>
            Tambah Belanja
        </a>
    </div>

    <!-- Stats Overview (Premium Touch) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 bg-[#22AF85]/10 rounded-2xl flex items-center justify-center text-[#22AF85]">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            </div>
            <div>
                <div class="text-2xl font-black text-gray-900"><?php echo e($purchases->total()); ?></div>
                <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Transaksi</div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 bg-[#FFC232]/10 rounded-2xl flex items-center justify-center text-[#FFC232]">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <div class="text-2xl font-black text-gray-900"><?php echo e(\App\Models\WarehousePurchase::where('status', 'PENDING')->count()); ?></div>
                <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">Menunggu Selesai</div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 bg-[#22AF85]/10 rounded-2xl flex items-center justify-center text-[#22AF85]">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            </div>
            <div>
                <div class="text-2xl font-black text-[#22AF85]">Rp <?php echo e(number_format(\App\Models\WarehousePurchase::where('status', 'COMPLETED')->sum('total_amount'), 0, ',', '.')); ?></div>
                <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Nilai Selesai</div>
            </div>
        </div>
    </div>

    <!-- Notifications -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session()->has('message')): ?>
        <div class="bg-[#22AF85]/10 border-l-4 border-[#22AF85] p-4 rounded-2xl shadow-sm flex items-center justify-between animate-bounce-short">
            <div class="flex items-center text-[#22AF85]">
                <div class="bg-[#22AF85] text-white p-1 rounded-full mr-3">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <span class="font-bold text-sm"><?php echo e(session('message')); ?></span>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Search & Filters -->
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex flex-col md:flex-row gap-4 items-center">
        <div class="relative flex-1 w-full">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" wire:model.live="search" placeholder="Cari nomor belanja, SPK, atau vendor..." 
                   class="w-full pl-12 pr-4 py-3 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-2 focus:ring-[#22AF85] focus:border-[#22AF85] transition-all font-medium">
        </div>
        <div class="w-full md:w-64">
            <select wire:model.live="purchaseType" class="w-full px-4 py-3 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-2 focus:ring-[#22AF85] font-bold text-gray-700">
                <option value="">Semua Tipe</option>
                <option value="Reguler">Reguler</option>
                <option value="Prioritas">Prioritas</option>
                <option value="Urgent">Urgent</option>
            </select>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Info Belanja</th>
                        <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-[0.2em] text-center">Workflow</th>
                        <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Target SPK</th>
                        <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Financial</th>
                        <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-[0.2em] text-right">Manajemen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $purchase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <tr class="group hover:bg-[#FBFBFB] transition-all">
                        <td class="px-8 py-6">
                            <div class="flex flex-col">
                                <span class="text-lg font-black text-gray-900 group-hover:text-[#22AF85] transition-colors tracking-tight"><?php echo e($purchase->purchase_number); ?></span>
                                <div class="flex items-center mt-1 space-x-2">
                                    <span class="text-xs font-bold text-gray-400"><?php echo e($purchase->purchase_date->format('d M Y')); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($purchase->external_reference): ?>
                                        <span class="px-2 py-0.5 bg-gray-100 rounded text-[10px] font-bold text-gray-500">REF: <?php echo e($purchase->external_reference); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex flex-col items-center gap-2">
                                <!-- Type Badge -->
                                <div class="text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full
                                    <?php echo e($purchase->purchase_type == 'Urgent' ? 'bg-red-50 text-red-500' : 
                                       ($purchase->purchase_type == 'Prioritas' ? 'bg-[#FFC232]/20 text-[#FFC232]' : 'bg-[#22AF85]/20 text-[#22AF85]')); ?>">
                                    <?php echo e($purchase->purchase_type); ?>

                                </div>
                                <!-- Status Badge -->
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($purchase->status == 'PENDING'): ?>
                                    <div class="flex items-center text-gray-400 font-bold text-[11px]">
                                        <div class="w-2 h-2 bg-gray-300 rounded-full mr-2 animate-pulse"></div> PENDING
                                    </div>
                                <?php elseif($purchase->status == 'PROCESSING'): ?>
                                    <div class="flex items-center text-[#22AF85] font-bold text-[11px]">
                                        <div class="w-2 h-2 bg-[#22AF85] rounded-full mr-2 animate-pulse"></div> PROSES
                                    </div>
                                <?php elseif($purchase->status == 'COMPLETED'): ?>
                                    <div class="flex items-center text-green-600 font-bold text-[11px] bg-green-50 px-3 py-1 rounded-lg">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg> SELESAI
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex flex-wrap gap-1 max-w-[200px]">
                                <?php $spks = $purchase->items->pluck('spk_number')->unique()->filter(); ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $spks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $spk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <span class="text-[10px] font-black text-gray-400 bg-gray-50 px-2 py-0.5 rounded border border-gray-100 group-hover:border-[#22AF85]/30 transition-all"><?php echo e($spk); ?></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($spks->isEmpty()): ?> - <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="text-lg font-black text-gray-900 tracking-tight">Rp <?php echo e(number_format($purchase->total_amount, 0, ',', '.')); ?></div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest"><?php echo e($purchase->items->count()); ?> Item Material</div>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex justify-end items-center space-x-3 opacity-0 group-hover:opacity-100 transition-all">
                                <a href="<?php echo e(route('storage.purchase.show', $purchase->id)); ?>" 
                                   class="p-2 bg-[#22AF85]/10 text-[#22AF85] rounded-xl hover:bg-[#22AF85] hover:text-white transition-all shadow-sm flex items-center gap-2 group/btn">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span class="text-[10px] font-black uppercase tracking-widest hidden group-hover/btn:block">Rincian</span>
                                </a>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($purchase->status !== 'COMPLETED' && $purchase->status !== 'CANCELLED'): ?>
                                    <button wire:click="completePurchase(<?php echo e($purchase->id); ?>)" 
                                            wire:confirm="Konfirmasi barang sudah diterima? Stok akan otomatis bertambah."
                                            class="bg-[#22AF85] text-white text-[10px] font-black uppercase tracking-widest px-4 py-2 rounded-xl shadow-[0_4px_12px_rgba(34,175,133,0.3)] hover:scale-105 active:scale-95 transition-all">
                                        Terima
                                    </button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <a href="<?php echo e(route('storage.purchase.edit', $purchase->id)); ?>" 
                                   class="p-2 bg-gray-100 text-gray-500 rounded-xl hover:bg-[#FFC232] hover:text-gray-900 transition-all shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center opacity-20">
                                <svg class="w-20 h-20 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                <p class="text-xl font-black text-gray-900">BELUM ADA DATA</p>
                                <p class="font-medium text-gray-500">Mulai catat belanja material hari ini</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100">
            <?php echo e($purchases->links()); ?>

        </div>
    </div>

    <style>
        @keyframes bounce-short {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-4px); }
        }
        .animate-bounce-short {
            animation: bounce-short 1s ease-in-out 1;
        }
    </style>
</div>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views/livewire/warehouse/purchase/index.blade.php ENDPATH**/ ?>