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
        <div class="flex items-center gap-4">
            <div class="p-2 bg-red-500/20 rounded-lg backdrop-blur-sm shadow-sm border border-red-500/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            
            <div class="flex flex-col">
                <h2 class="font-bold text-xl leading-tight tracking-wide">
                    <?php echo e(__('Pusat Pengelolaan Data (Hidden Data)')); ?>

                </h2>
                <div class="text-xs font-medium opacity-90">
                    Pemulihan dan Pembersihan Data SPK Terhapus
                </div>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12 bg-gray-50/50" x-data="{ selectedItems: [] }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-4">
                <a href="<?php echo e(route('reception.index')); ?>" class="flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-teal-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l-7 7m7-7H21"></path></svg>
                    Kembali ke Penerimaan
                </a>

                <form action="<?php echo e(route('reception.trash')); ?>" method="GET" class="w-full md:w-96 relative">
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" 
                           class="w-full pl-10 pr-4 py-2 border-gray-200 rounded-xl text-sm focus:ring-teal-500 focus:border-teal-500 shadow-sm"
                           placeholder="Cari SPK, Nama, atau No. Telp...">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <div>
                        <h3 class="font-bold text-gray-800">Daftar Data Tersembunyi</h3>
                        <p class="text-[10px] text-gray-500 mt-1 uppercase font-bold tracking-widest">Gunakan fitur ini untuk memulihkan data yang "hilang" atau membersihkan SPK agar bisa di-import ulang.</p>
                    </div>
                    <div class="text-sm text-gray-500 bg-white px-3 py-1 rounded-full border border-gray-100 shadow-sm">Total: <span class="font-bold text-gray-900"><?php echo e($orders->total()); ?></span> data</div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/80 text-gray-600 text-[10px] uppercase tracking-widest font-black">
                                <th class="px-6 py-4 w-10 text-center">
                                    <input type="checkbox" @click="if($event.target.checked) { selectedItems = <?php echo e(json_encode($orders->pluck('id'))); ?> } else { selectedItems = [] }" class="rounded border-gray-300 text-teal-600 shadow-sm focus:ring-teal-500">
                                </th>
                                <th class="px-6 py-4">Informasi SPK</th>
                                <th class="px-6 py-4 text-center">Status Terakhir</th>
                                <th class="px-6 py-4 text-center">Waktu Hapus</th>
                                <th class="px-6 py-4 text-right">Aksi Pemulihan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="px-6 py-4 text-center">
                                        <input type="checkbox" value="<?php echo e($order->id); ?>" x-model="selectedItems" class="rounded border-gray-300 text-teal-600 shadow-sm focus:ring-teal-500">
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="font-black text-gray-900 text-sm tracking-tight"><?php echo e($order->spk_number); ?></span>
                                            <span class="text-xs text-gray-500 font-medium"><?php echo e($order->customer_name); ?></span>
                                            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter"><?php echo e($order->customer_phone ?? '-'); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-[10px] font-bold uppercase"><?php echo e($order->status->label()); ?></span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="text-xs text-gray-700 font-bold"><?php echo e($order->deleted_at->format('d M Y')); ?></span>
                                            <span class="text-[10px] text-gray-400"><?php echo e($order->deleted_at->format('H:i')); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <form action="<?php echo e(route('reception.restore', $order->id)); ?>" method="POST" class="inline">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="px-4 py-2 bg-teal-500 hover:bg-teal-600 text-white rounded-xl text-[10px] font-black uppercase tracking-wider shadow-sm transition-all flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                    Pulihkan
                                                </button>
                                            </form>
                                            <form action="<?php echo e(route('reception.force-delete', $order->id)); ?>" method="POST" class="inline" onsubmit="return confirm('Hapus PERMANEN data ini? Tindakan ini tidak dapat dibatalkan dan semua riwayat/foto akan hilang.')">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="px-4 py-2 bg-white text-red-600 border border-red-100 hover:bg-red-50 rounded-xl text-[10px] font-black uppercase tracking-wider shadow-sm transition-all">
                                                    Hapus Permanen
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center text-3xl mb-4">📭</div>
                                            <p class="font-bold text-gray-400">Pusat Data Bersih</p>
                                            <p class="text-xs text-gray-400 mt-1">Tidak ada data tersembunyi yang terdeteksi.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($orders->hasPages()): ?>
                    <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
                        <?php echo e($orders->links()); ?>

                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <!-- Bulk Actions -->
            <div x-show="selectedItems.length > 0" x-cloak class="fixed bottom-6 inset-x-0 z-50 flex justify-center px-4">
                <div class="bg-gray-900/90 backdrop-blur shadow-2xl rounded-2xl p-4 w-full max-w-lg flex items-center justify-between gap-4 border border-white/10">
                    <div class="flex items-center gap-3 ml-2">
                        <span class="w-8 h-8 bg-teal-500 rounded-lg flex items-center justify-center text-white font-black text-xs" x-text="selectedItems.length"></span>
                        <span class="text-xs font-bold text-white uppercase tracking-widest">Item Terpilih</span>
                    </div>
                    <form action="<?php echo e(route('reception.bulk-force-delete')); ?>" method="POST" id="bulk-force-delete-form" onsubmit="return confirm('HAPUS PERMANEN semua data terpilih? Tindakan ini tidak bisa dibatalkan!')">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <template x-for="id in selectedItems" :key="id">
                            <input type="hidden" name="ids[]" :value="id">
                        </template>
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg transition-all border border-red-500/20">
                            Hapus Permanen
                        </button>
                    </form>
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
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\reception\trash.blade.php ENDPATH**/ ?>