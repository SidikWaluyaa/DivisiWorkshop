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
            <?php echo e(__('Kolam Cancel (Riwayat Pembatalan)')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                        <div class="space-y-1">
                            <h1 class="text-xl font-black text-gray-900 tracking-tight">Daftar Order Dibatalkan</h1>
                            <p class="text-xs font-medium text-gray-400">Arsip data yang telah dibatalkan dari sistem.</p>
                        </div>

                        <form method="GET" action="<?php echo e(route('cx.cancelled')); ?>" class="w-full md:w-auto bg-gray-50 p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col md:flex-row gap-3 items-end">
                            <div class="w-full md:w-64 space-y-1.5">
                                <label class="text-[10px] uppercase font-black tracking-widest text-gray-400 ml-1">Pencarian</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-3.5 w-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" 
                                           class="w-full pl-9 pr-3 py-2 border-gray-200 rounded-xl text-xs font-bold text-gray-700 bg-white shadow-inner focus:ring-teal-500 focus:border-teal-500" 
                                           placeholder="Cari SPK / Nama...">
                                </div>
                            </div>

                            <div class="w-full md:w-40 space-y-1.5">
                                <label class="text-[10px] uppercase font-black tracking-widest text-gray-400 ml-1">Urutan</label>
                                <select name="sort" class="w-full border-amber-100 rounded-xl text-xs font-black text-amber-600 bg-amber-50 focus:ring-amber-500 py-2 shadow-sm appearance-none px-3 pr-8" style="background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22 fill=%22none%22 viewBox=%220 0 20 20%22%3E%3Cpath stroke=%22%23b45309%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%221.5%22 d=%22m6 8 4 4 4-4%22%2F%3E%3C%2Fsvg%3E'); background-position: right 0.5rem center; background-repeat: no-repeat; background-size: 1rem auto;">
                                    <option value="asc" <?php echo e(request('sort', 'asc') == 'asc' ? 'selected' : ''); ?>>⏳ Terlama</option>
                                    <option value="desc" <?php echo e(request('sort') == 'desc' ? 'selected' : ''); ?>>🔥 Terbaru</option>
                                </select>
                            </div>

                            <div class="flex gap-2 w-full md:w-auto">
                                <button type="submit" class="flex-1 md:flex-none bg-gray-900 hover:bg-black text-white px-5 py-2 rounded-xl text-xs font-black tracking-widest shadow-lg shadow-gray-200 transition-all border border-gray-800">
                                    CARI
                                </button>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->anyFilled(['search'])): ?>
                                    <a href="<?php echo e(route('cx.cancelled')); ?>" class="p-2 bg-white border border-gray-100 text-gray-300 hover:text-red-400 rounded-xl flex items-center justify-center transition-all shadow-sm" title="Reset Filter">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </a>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                                <tr>
                                    <th class="px-6 py-3">Tanggal Cancel</th>
                                    <th class="px-6 py-3">SPK & Customer</th>
                                    <th class="px-6 py-3">Sepatu</th>
                                    <th class="px-6 py-3">Alasan / History</th>
                                    <th class="px-6 py-3">Dibatalkan Oleh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <tr class="bg-white border-b hover:bg-gray-50 opacity-75 grayscale hover:grayscale-0 transition-all">
                                        <td class="px-6 py-4">
                                            <div class="font-bold"><?php echo e($order->updated_at->format('d M Y')); ?></div>
                                            <div class="text-xs text-gray-400"><?php echo e($order->updated_at->format('H:i')); ?></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-mono bg-red-50 text-red-600 px-2 py-1 rounded inline-block text-xs font-bold mb-1">
                                                <?php echo e($order->spk_number); ?>

                                            </div>
                                            <div class="font-bold text-gray-900"><?php echo e($order->customer_name); ?></div>
                                            <div class="text-xs"><?php echo e($order->customer_phone); ?></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-bold"><?php echo e($order->shoe_brand); ?></div>
                                            <div class="text-xs"><?php echo e($order->shoe_color); ?> [<?php echo e($order->shoe_size); ?>]</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            
                                            <?php
                                                $lastLog = $order->logs()->where('step', 'CX_FOLLOWUP')->latest()->first();
                                                $msg = $lastLog ? $lastLog->description : ($order->reception_rejection_reason ?? 'Dibatalkan');
                                            ?>
                                            <div class="italic text-gray-600 text-xs max-w-xs break-words">
                                                "<?php echo e(Str::limit($msg, 100)); ?>"
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                 <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lastLog && $lastLog->user): ?>
                                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs">
                                                        <?php echo e($lastLog->user->name); ?>

                                                    </span>
                                                 <?php else: ?>
                                                    -
                                                 <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                                 
                                                 <form action="<?php echo e(route('cx.destroy', $order->id)); ?>" method="POST" onsubmit="return confirm('Yakin ingin menghapus permanen data ini? Action tidak bisa dibatalkan.');">
                                                     <?php echo csrf_field(); ?>
                                                     <?php echo method_field('DELETE'); ?>
                                                     <button type="submit" class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors" title="Hapus Permanen">
                                                         <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                     </button>
                                                 </form>
                                             </div>
                                        </td>
                                    </tr>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                            Tidak ada data cancel.
                                        </td>
                                    </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <?php echo e($orders->links()); ?>

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
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\cx\cancelled.blade.php ENDPATH**/ ?>