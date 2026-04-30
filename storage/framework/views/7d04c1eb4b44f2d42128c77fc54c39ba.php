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

<div class="container-fluid px-4 py-6">
    
    <div class="mb-6">
        <h1 class="text-3xl font-bold" style="color: #1f2937;">Pengajuan Material</h1>
        <p class="text-gray-600 mt-1">Kolam Belanja & Purchase Order</p>
    </div>

    
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form method="GET" action="<?php echo e(route('material-requests.index')); ?>" class="flex flex-wrap gap-4">
            
            <div class="flex-1 min-w-[250px]">
                <input 
                    type="text" 
                    name="search" 
                    value="<?php echo e(request('search')); ?>"
                    placeholder="Cari nomor request atau nama..." 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-opacity-50"
                    style="focus:ring-color: #22AF85;"
                >
            </div>

            
            <div class="min-w-[180px]">
                <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-opacity-50" style="focus:ring-color: #22AF85;">
                    <option value="all" <?php echo e(request('type') == 'all' ? 'selected' : ''); ?>>Semua Tipe</option>
                    <option value="SHOPPING" <?php echo e(request('type') == 'SHOPPING' ? 'selected' : ''); ?>>Belanja</option>
                    <option value="PRODUCTION_PO" <?php echo e(request('type') == 'PRODUCTION_PO' ? 'selected' : ''); ?>>PO Produksi</option>
                </select>
            </div>

            
            <div class="min-w-[180px]">
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-opacity-50" style="focus:ring-color: #22AF85;">
                    <option value="all" <?php echo e(request('status') == 'all' ? 'selected' : ''); ?>>Semua Status</option>
                    <option value="PENDING" <?php echo e(request('status') == 'PENDING' ? 'selected' : ''); ?>>Pending</option>
                    <option value="APPROVED" <?php echo e(request('status') == 'APPROVED' ? 'selected' : ''); ?>>Approved</option>
                    <option value="REJECTED" <?php echo e(request('status') == 'REJECTED' ? 'selected' : ''); ?>>Rejected</option>
                    <option value="PURCHASED" <?php echo e(request('status') == 'PURCHASED' ? 'selected' : ''); ?>>Purchased</option>
                    <option value="CANCELLED" <?php echo e(request('status') == 'CANCELLED' ? 'selected' : ''); ?>>Cancelled</option>
                </select>
            </div>

            
            <button type="submit" class="px-6 py-2 rounded-lg font-medium text-white transition-all duration-200 hover:shadow-lg" style="background-color: #22AF85;">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>

            
            <a href="<?php echo e(route('material-requests.index')); ?>" class="px-6 py-2 rounded-lg font-medium border-2 transition-all duration-200 hover:shadow-md" style="border-color: #22AF85; color: #22AF85;">
                <i class="fas fa-redo mr-2"></i>Reset
            </a>
        </form>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="bg-green-50 border-l-4 p-4 mb-6 rounded-lg" style="border-color: #22AF85;">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-xl mr-3" style="color: #22AF85;"></i>
                <p class="font-medium" style="color: #22AF85;"><?php echo e(session('success')); ?></p>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                <p class="text-red-700 font-medium"><?php echo e(session('error')); ?></p>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="space-y-4">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
            <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3">
                                
                                <h3 class="text-xl font-bold" style="color: #1f2937;">
                                    <?php echo e($request->request_number); ?>

                                </h3>

                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($request->type === 'SHOPPING'): ?>
                                    <span class="px-3 py-1 rounded-full text-sm font-medium text-white" style="background-color: #FFC232;">
                                        <i class="fas fa-shopping-cart mr-1"></i>Belanja
                                    </span>
                                <?php else: ?>
                                    <span class="px-3 py-1 rounded-full text-sm font-medium text-white" style="background-color: #22AF85;">
                                        <i class="fas fa-box mr-1"></i>PO Produksi
                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    <?php if($request->status === 'PENDING'): ?> bg-yellow-100 text-yellow-800
                                    <?php elseif($request->status === 'APPROVED'): ?> bg-green-100 text-green-800
                                    <?php elseif($request->status === 'REJECTED'): ?> bg-red-100 text-red-800
                                    <?php elseif($request->status === 'PURCHASED'): ?> bg-blue-100 text-blue-800
                                    <?php else: ?> bg-gray-100 text-gray-800
                                    <?php endif; ?>">
                                    <?php echo e($request->status); ?>

                                </span>
                            </div>

                            
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-gray-600">
                                <div>
                                    <i class="fas fa-user mr-2" style="color: #22AF85;"></i>
                                    <span class="font-medium">Diminta oleh:</span>
                                    <p class="ml-6"><?php echo e($request->requestedBy->name ?? 'N/A'); ?></p>
                                </div>

                                <div>
                                    <i class="fas fa-calendar mr-2" style="color: #22AF85;"></i>
                                    <span class="font-medium">Tanggal:</span>
                                    <p class="ml-6"><?php echo e($request->created_at->format('d M Y')); ?></p>
                                </div>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($request->work_order_id): ?>
                                    <div>
                                        <i class="fas fa-file-alt mr-2" style="color: #22AF85;"></i>
                                        <span class="font-medium">Work Order:</span>
                                        <p class="ml-6"><?php echo e($request->workOrder->spk_number ?? 'N/A'); ?></p>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($request->oto_id): ?>
                                    <div>
                                        <i class="fas fa-sync-alt mr-2" style="color: #22AF85;"></i>
                                        <span class="font-medium">OTO:</span>
                                        <p class="ml-6"><?php echo e($request->oto->oto_number ?? 'N/A'); ?></p>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <div>
                                    <i class="fas fa-money-bill-wave mr-2" style="color: #FFC232;"></i>
                                    <span class="font-medium">Estimasi:</span>
                                    <p class="ml-6 font-bold" style="color: #FFC232;">Rp <?php echo e(number_format($request->total_estimated_cost, 0, ',', '.')); ?></p>
                                </div>
                            </div>

                            
                            <div class="mt-3 text-sm text-gray-600">
                                <i class="fas fa-list mr-2"></i>
                                <span class="font-medium"><?php echo e($request->items->count()); ?> item(s)</span>
                            </div>

                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($request->notes): ?>
                                <div class="mt-3 p-3 bg-gray-50 rounded-lg text-sm text-gray-700">
                                    <i class="fas fa-sticky-note mr-2 text-gray-500"></i>
                                    <span class="font-medium">Catatan:</span> <?php echo e($request->notes); ?>

                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($request->approved_by): ?>
                                <div class="mt-3 text-sm text-gray-600">
                                    <i class="fas fa-check-circle mr-2" style="color: #22AF85;"></i>
                                    <span class="font-medium">Disetujui oleh:</span> <?php echo e($request->approvedBy->name ?? 'N/A'); ?>

                                    <span class="ml-2">pada <?php echo e($request->approved_at->format('d M Y H:i')); ?></span>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        
                        <div class="flex flex-col gap-2 ml-4">
                            <a href="<?php echo e(route('material-requests.show', $request)); ?>" class="px-4 py-2 rounded-lg font-medium text-white text-center transition-all duration-200 hover:shadow-lg" style="background-color: #22AF85;">
                                <i class="fas fa-eye mr-2"></i>Detail
                            </a>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($request->status === 'PENDING'): ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manageInventory')): ?>
                                <form action="<?php echo e(route('material-requests.approve', $request)); ?>" method="POST" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="w-full px-4 py-2 rounded-lg font-medium text-white transition-all duration-200 hover:shadow-lg" style="background-color: #22AF85;" onclick="return confirm('Setujui pengajuan ini?')">
                                        <i class="fas fa-check mr-2"></i>Approve
                                    </button>
                                </form>

                                <form action="<?php echo e(route('material-requests.reject', $request)); ?>" method="POST" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="w-full px-4 py-2 bg-red-500 rounded-lg font-medium text-white transition-all duration-200 hover:bg-red-600 hover:shadow-lg" onclick="return confirm('Tolak pengajuan ini?')">
                                        <i class="fas fa-times mr-2"></i>Reject
                                    </button>
                                </form>
                                <?php endif; ?>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($request->status === 'APPROVED'): ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manageInventory')): ?>
                                <form action="<?php echo e(route('material-requests.mark-purchased', $request)); ?>" method="POST" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="w-full px-4 py-2 rounded-lg font-medium text-white transition-all duration-200 hover:shadow-lg" style="background-color: #FFC232;" onclick="return confirm('Tandai sebagai sudah dibeli?')">
                                        <i class="fas fa-shopping-bag mr-2"></i>Mark Purchased
                                    </button>
                                </form>
                                <?php endif; ?>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">Tidak ada pengajuan material</p>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($requests->hasPages()): ?>
        <div class="mt-6">
            <?php echo e($requests->links()); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<style>
    /* Custom focus ring color */
    input:focus, select:focus {
        outline: none;
        ring-color: #22AF85;
        border-color: #22AF85;
    }
</style>
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
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\material-requests\index.blade.php ENDPATH**/ ?>