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
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <?php echo e(__('Edit Purchase Order')); ?> - <?php echo e($purchase->po_number); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="<?php echo e(route('admin.purchases.update', $purchase)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Supplier / Toko</label>
                            <input type="text" name="supplier_name" value="<?php echo e($purchase->supplier_name); ?>" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg" placeholder="Contoh: Toko Sepatu Jaya">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Material</label>
                            <select name="material_id" id="materialSelect" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg" required>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <option value="<?php echo e($material->id); ?>" 
                                    data-price="<?php echo e($material->price); ?>" 
                                    data-unit="<?php echo e($material->unit); ?>"
                                    <?php echo e($purchase->material_id == $material->id ? 'selected' : ''); ?>>
                                    <?php echo e($material->name); ?>

                                </option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Jumlah</label>
                                <input type="number" name="quantity" id="quantity" value="<?php echo e($purchase->quantity); ?>" min="1" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Harga per Unit</label>
                                <input type="number" name="unit_price" id="unitPrice" value="<?php echo e($purchase->unit_price); ?>" step="0.01" min="0" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg" required>
                            </div>
                        </div>

                        <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Total Harga</div>
                            <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400" id="totalPrice">Rp <?php echo e(number_format($purchase->total_price, 0, ',', '.')); ?></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                                <select name="status" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg" required>
                                    <option value="pending" <?php echo e($purchase->status == 'pending' ? 'selected' : ''); ?>>Pending</option>
                                    <option value="ordered" <?php echo e($purchase->status == 'ordered' ? 'selected' : ''); ?>>Dipesan (Ordered)</option>
                                    <option value="received" <?php echo e($purchase->status == 'received' ? 'selected' : ''); ?>>Diterima (Stock akan bertambah)</option>
                                    <option value="cancelled" <?php echo e($purchase->status == 'cancelled' ? 'selected' : ''); ?>>Dibatalkan</option>
                                </select>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($purchase->status === 'received'): ?>
                                <p class="text-xs text-green-600 mt-1">✓ Barang sudah diterima pada <?php echo e($purchase->received_date?->format('d M Y')); ?></p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rating Kualitas (1-5)</label>
                                <select name="quality_rating" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg">
                                    <option value="">-- Beri Penilaian --</option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i=1; $i<=5; $i++): ?>
                                        <option value="<?php echo e($i); ?>" <?php echo e($purchase->quality_rating == $i ? 'selected' : ''); ?>><?php echo e($i); ?> Bintang <?php echo e(str_repeat('⭐', $i)); ?></option>
                                    <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal Order</label>
                                <input type="date" name="order_date" value="<?php echo e($purchase->order_date?->format('Y-m-d')); ?>" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal Jatuh Tempo</label>
                                <input type="date" name="due_date" value="<?php echo e($purchase->due_date?->format('Y-m-d')); ?>" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg">
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catatan</label>
                            <textarea name="notes" rows="3" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg"><?php echo e($purchase->notes); ?></textarea>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-semibold">
                                Simpan Perubahan
                            </button>
                            <a href="<?php echo e(route('admin.purchases.index')); ?>" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg font-semibold text-center">
                                Batal
                            </a>
                        </div>
                    </form>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($purchase->status !== 'received'): ?>
                    <form action="<?php echo e(route('admin.purchases.destroy', $purchase)); ?>" method="POST" class="mt-4" onsubmit="return confirm('Yakin ingin hapus PO ini?')">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold">
                            Hapus Purchase Order
                        </button>
                    </form>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        const quantityInput = document.getElementById('quantity');
        const unitPriceInput = document.getElementById('unitPrice');
        const totalPriceDisplay = document.getElementById('totalPrice');

        quantityInput.addEventListener('input', calculateTotal);
        unitPriceInput.addEventListener('input', calculateTotal);

        function calculateTotal() {
            const qty = parseFloat(quantityInput.value) || 0;
            const price = parseFloat(unitPriceInput.value) || 0;
            const total = qty * price;
            
            totalPriceDisplay.textContent = 'Rp ' + total.toLocaleString('id-ID');
        }
    </script>
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
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\admin\purchases\edit.blade.php ENDPATH**/ ?>