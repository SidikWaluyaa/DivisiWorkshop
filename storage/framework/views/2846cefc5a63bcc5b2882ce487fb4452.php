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
        <div class="flex flex-col md:flex-row justify-between md:items-center gap-3">
            <h2 class="font-semibold text-xl text-white leading-tight">
                <?php echo e(__('Performance & Produktivitas Teknisi')); ?>

            </h2>
            <div class="flex flex-wrap gap-2">
                <span class="px-3 py-1 bg-white/10 rounded-lg backdrop-blur-sm border border-white/20 text-white text-xs">
                    Total: <?php echo e($users->count()); ?> Teknisi/PIC
                </span>
                <span class="px-3 py-1 bg-white/10 rounded-lg backdrop-blur-sm border border-white/20 text-white text-xs">
                    Spesialisasi: <?php echo e($usersBySpecialization->count()); ?>

                </span>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
                        <h3 class="text-lg font-bold">Ringkasan Pekerjaan (All Time)</h3>
                        <div class="text-xs text-gray-500 bg-blue-50 dark:bg-gray-700 px-3 py-2 rounded-lg">
                            <strong>Filter:</strong> Hanya Teknisi & PIC | Diurutkan berdasarkan Spesialisasi
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto -mx-4 sm:mx-0">
                        <table class="min-w-full w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th rowspan="2" class="px-6 py-3 border-r">Nama Teknisi / PIC</th>
                                    <th rowspan="2" class="px-6 py-3 border-r bg-purple-50 dark:bg-gray-800">Spesialisasi</th>
                                    <th rowspan="2" class="px-6 py-3 text-center border-r bg-yellow-50 dark:bg-gray-800 font-bold">Preparation<br>(Subtasks)</th>
                                    <th colspan="2" class="px-6 py-3 text-center border-r bg-blue-50 dark:bg-gray-800">Sortir</th>
                                    <th rowspan="2" class="px-6 py-3 text-center border-r bg-indigo-50 dark:bg-gray-800 font-bold">Production</th>
                                    <th colspan="3" class="px-6 py-3 text-center bg-green-50 dark:bg-gray-800">Quality Control (QC)</th>
                                    <th rowspan="2" class="px-6 py-3 text-center border-l bg-gray-100 dark:bg-gray-600 font-black">TOTAL</th>
                                    <th rowspan="2" class="px-6 py-3 text-center border-l bg-red-50 dark:bg-red-900 font-bold text-red-600">Keluhan</th>
                                </tr>
                                <tr>
                                    <!-- Sortir headers -->
                                    <th class="px-4 py-2 text-center bg-blue-100 dark:bg-gray-700">Sol</th>
                                    <th class="px-4 py-2 text-center border-r bg-blue-100 dark:bg-gray-700">Upper</th>
                                    
                                    <!-- QC headers -->
                                    <th class="px-4 py-2 text-center bg-green-100 dark:bg-gray-700">Jahit</th>
                                    <th class="px-4 py-2 text-center bg-green-100 dark:bg-gray-700">Clean</th>
                                    <th class="px-4 py-2 text-center bg-green-100 dark:bg-gray-700">Final</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $currentSpecialization = null; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($currentSpecialization !== $user->specialization): ?>
                                        <?php $currentSpecialization = $user->specialization; ?>
                                        <tr class="bg-teal-50 dark:bg-gray-700 border-t-2 border-teal-200">
                                            <td colspan="10" class="px-6 py-2 font-bold text-teal-800 dark:text-teal-300 text-xs uppercase tracking-wider">
                                                📌 <?php echo e($user->specialization ?? 'Tidak Ada Spesialisasi'); ?>

                                            </td>
                                        </tr>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white border-r">
                                        <?php echo e($user->name); ?>

                                        <div class="text-xs text-gray-400"><?php echo e(ucfirst($user->role)); ?></div>
                                    </td>
                                    
                                    <!-- Specialization -->
                                    <td class="px-6 py-4 border-r">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->specialization): ?>
                                            <span class="px-2 py-1 bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300 rounded-full text-xs font-semibold">
                                                <?php echo e($user->specialization); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="text-gray-400 text-xs italic">-</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    
                                    <!-- Preparation -->
                                    <td class="px-6 py-4 text-center border-r font-bold text-yellow-600">
                                        <?php echo e($user->prep_tasks_count); ?>

                                    </td>

                                    <!-- Sortir -->
                                    <td class="px-4 py-4 text-center">
                                        <?php echo e($user->jobs_sortir_sol_count); ?>

                                    </td>
                                    <td class="px-4 py-4 text-center border-r">
                                        <?php echo e($user->jobs_sortir_upper_count); ?>

                                    </td>

                                    <!-- Production -->
                                    <td class="px-6 py-4 text-center border-r font-bold text-indigo-600">
                                        <?php echo e($user->jobs_production_count); ?>

                                    </td>
                                    
                                    <!-- QC -->
                                    <td class="px-4 py-4 text-center">
                                        <?php echo e($user->jobs_qc_jahit_count); ?>

                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <?php echo e($user->jobs_qc_cleanup_count); ?>

                                    </td>
                                    <td class="px-4 py-4 text-center font-bold text-green-600">
                                        <?php echo e($user->jobs_qc_final_count); ?>

                                    </td>

                                    <!-- TOTAL -->
                                    <?php
                                        $total = $user->prep_tasks_count + 
                                                 $user->jobs_sortir_sol_count + 
                                                 $user->jobs_sortir_upper_count + 
                                                 $user->jobs_production_count + 
                                                 $user->jobs_qc_jahit_count + 
                                                 $user->jobs_qc_cleanup_count + 
                                                 $user->jobs_qc_final_count;
                                    ?>
                                    <td class="px-6 py-4 text-center border-l font-black text-lg <?php echo e($total > 0 ? 'text-teal-600' : 'text-gray-400'); ?>">
                                        <?php echo e($total); ?>

                                    </td>
                                    <td class="px-6 py-4 text-center border-l font-bold text-red-600">
                                        <?php echo e($user->complaints_count ?: '-'); ?>

                                    </td>
                                </tr>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($users->isEmpty()): ?>
                                <tr>
                                    <td colspan="10" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="p-4 bg-gray-100 rounded-full mb-3">
                                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                                </svg>
                                            </div>
                                            <p class="font-medium text-gray-900">Tidak Ada Data Teknisi/PIC</p>
                                            <p class="text-sm">Belum ada teknisi atau PIC yang terdaftar di sistem.</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
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
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\admin\performance\index.blade.php ENDPATH**/ ?>