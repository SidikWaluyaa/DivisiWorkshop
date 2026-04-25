<div x-data="{ showPreview: false, previewUrl: '' }">
     <?php $__env->slot('header', null, []); ?> 
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-bold text-xl text-white leading-tight flex items-center gap-2">
                <div class="p-2 bg-emerald-500/20 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <?php echo e(__('Riwayat Pengambilan Sepatu')); ?>

            </h2>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 bg-white/10 rounded-full text-xs font-medium text-emerald-200 border border-white/10 backdrop-blur-md">
                    Divisi Gudang
                </span>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-3xl p-6 shadow-xl shadow-emerald-500/20 text-white relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 opacity-10 transform rotate-12 group-hover:scale-110 transition-transform duration-500">
                    <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.828a1 1 0 101.414-1.414L11 9.586V6z" clip-rule="evenodd"></path></svg>
                </div>
                <div class="relative z-10">
                    <p class="text-emerald-100 text-sm font-medium mb-1">Hari Ini</p>
                    <h3 class="text-4xl font-black"><?php echo e($stats['today']); ?> <span class="text-lg font-normal text-emerald-200">Sepatu</span></h3>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-3xl p-6 shadow-xl shadow-blue-500/20 text-white relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 opacity-10 transform rotate-12 group-hover:scale-110 transition-transform duration-500">
                    <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path></svg>
                </div>
                <div class="relative z-10">
                    <p class="text-blue-100 text-sm font-medium mb-1">Minggu Ini</p>
                    <h3 class="text-4xl font-black"><?php echo e($stats['week']); ?> <span class="text-lg font-normal text-blue-200">Sepatu</span></h3>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-fuchsia-600 rounded-3xl p-6 shadow-xl shadow-purple-500/20 text-white relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 opacity-10 transform rotate-12 group-hover:scale-110 transition-transform duration-500">
                    <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
                </div>
                <div class="relative z-10">
                    <p class="text-purple-100 text-sm font-medium mb-1">Bulan Ini</p>
                    <h3 class="text-4xl font-black"><?php echo e($stats['month']); ?> <span class="text-lg font-normal text-purple-200">Sepatu</span></h3>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-xl border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                <div class="relative z-10">
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium mb-1">Total Keseluruhan</p>
                    <h3 class="text-4xl font-black text-gray-900 dark:text-white"><?php echo e($stats['total']); ?></h3>
                </div>
            </div>
        </div>

        
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 p-6 mb-8">
            <div class="flex flex-col lg:flex-row gap-6 items-end">
                <div class="flex-1 w-full">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Pencarian SPK / Customer</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search" 
                               class="w-full pl-11 pr-4 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-emerald-500 text-sm transition-all" 
                               placeholder="Ketik nomor SPK, nama customer, atau merk sepatu...">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 w-full lg:w-auto">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Dari Tanggal</label>
                        <input type="date" wire:model.live="startDate" 
                               class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-emerald-500 text-sm transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Sampai Tanggal</label>
                        <input type="date" wire:model.live="endDate" 
                               class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-emerald-500 text-sm transition-all">
                    </div>
                </div>

                <div class="flex gap-2">
                    <button wire:click="resetFilters" class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-2xl hover:bg-gray-200 transition-colors font-bold text-sm">
                        Reset
                    </button>
                </div>
            </div>
        </div>

        
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900/50">
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Data Sepatu</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Customer</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Waktu Ambil</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Layanan</th>
                            <th class="px-6 py-4 text-right text-xs font-black text-gray-400 uppercase tracking-widest">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <tr class="hover:bg-emerald-50/30 dark:hover:bg-emerald-900/10 transition-colors group">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden border-2 border-transparent group-hover:border-emerald-500 transition-all shadow-sm cursor-zoom-in"
                                         <?php if($order->spk_cover_photo_url): ?> 
                                            @click="previewUrl = '<?php echo e($order->spk_cover_photo_url); ?>'; showPreview = true" 
                                         <?php endif; ?>>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->spk_cover_photo_url): ?>
                                            <img src="<?php echo e($order->spk_cover_photo_url); ?>" 
                                                 class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500"
                                                 alt="SPK Cover Photo">
                                        <?php else: ?>
                                            <svg class="w-6 h-6 text-gray-400 group-hover:text-emerald-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                            </svg>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <div>
                                        <div class="text-sm font-black text-gray-900 dark:text-white font-mono"><?php echo e($order->spk_number); ?></div>
                                        <div class="text-xs text-gray-500 mt-0.5"><?php echo e($order->shoe_brand); ?> <?php echo e($order->shoe_type); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="text-sm font-bold text-gray-900 dark:text-white"><?php echo e($order->customer_name); ?></div>
                                <div class="text-xs text-gray-500"><?php echo e($order->customer_phone); ?></div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">
                                        <?php echo e(\Carbon\Carbon::parse($order->taken_date)->format('d M Y')); ?>

                                    </span>
                                    <span class="text-[10px] text-gray-400">Jam: <?php echo e(\Carbon\Carbon::parse($order->taken_date)->format('H:i')); ?> WIB</span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-wrap gap-1">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $order->workOrderServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $svc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                        <span class="px-2 py-0.5 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded text-[10px] font-bold">
                                            <?php echo e($svc->service->name ?? $svc->custom_service_name); ?>

                                        </span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-right flex justify-end gap-2">
                                <button wire:click="undoPickup(<?php echo e($order->id); ?>)" 
                                        wire:confirm="Apakah Anda yakin ingin membatalkan pengambilan ini? Sepatu akan kembali ke status 'Menunggu Disimpan' di Gudang Finish."
                                        class="p-2 text-gray-400 hover:text-orange-500 hover:bg-orange-50 dark:hover:bg-orange-900/20 rounded-xl transition-all"
                                        title="Kembalikan ke Menunggu Disimpan">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                    </svg>
                                </button>
                                <a href="<?php echo e(route('admin.orders.show', $order->id)); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-xs font-bold text-gray-600 dark:text-gray-300 hover:bg-gray-50 transition-all shadow-sm">
                                    Detail
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </td>
                        </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center justify-center opacity-40">
                                    <svg class="w-20 h-20 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                    <p class="text-xl font-bold">Tidak ada riwayat pengambilan</p>
                                    <p class="text-sm">Gunakan filter atau cari nomor SPK lain.</p>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($orders->hasPages()): ?>
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700">
                    <?php echo e($orders->links()); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <div x-show="showPreview" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/90 backdrop-blur-sm"
         x-cloak
         @keydown.escape.window="showPreview = false">
        
        <button @click="showPreview = false" class="absolute top-6 right-6 text-white/50 hover:text-white transition-colors">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <img :src="previewUrl" 
             @click.away="showPreview = false"
             class="max-w-full max-h-[90vh] rounded-2xl shadow-2xl border-4 border-white/10 object-contain"
             alt="Enlarged Preview">
    </div>
</div>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views/livewire/warehouse/pickup-history.blade.php ENDPATH**/ ?>