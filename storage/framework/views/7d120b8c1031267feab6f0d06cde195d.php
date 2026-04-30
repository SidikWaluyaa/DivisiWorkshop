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

    <!-- Content -->
    <div class="min-h-screen bg-gray-50 pb-20">
    <!-- Header -->
    <div class="bg-gradient-to-r from-orange-600 to-pink-600 pb-24 pt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-white">Kolam OTO (Upsell) 🎁</h1>
                    <p class="mt-2 text-orange-100">Manage penawaran One Time Offer untuk customer</p>
                </div>
                <!-- Stats -->
                <div class="flex space-x-4">
                    <div class="bg-white/10 backdrop-blur-lg rounded-lg p-4 text-white">
                        <div class="text-sm opacity-80">Pending Call</div>
                        <div class="text-2xl font-bold"><?php echo e($stats['pending']); ?></div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-lg rounded-lg p-4 text-white">
                        <div class="text-sm opacity-80">Contacted</div>
                        <div class="text-2xl font-bold"><?php echo e($stats['contacted']); ?></div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-lg rounded-lg p-4 text-white">
                        <div class="text-sm opacity-80">Revenue Potensial</div>
                        <div class="text-2xl font-bold">Rp <?php echo e(number_format($stats['total_revenue'], 0, ',', '.')); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16">
        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-lg p-4 mb-6 flex items-center justify-between">
            <div class="flex space-x-2">
                <a href="<?php echo e(route('cx.oto.index', ['filter' => 'all'])); ?>" 
                   class="px-4 py-2 rounded-lg text-sm font-medium <?php echo e($filter === 'all' ? 'bg-orange-100 text-orange-700' : 'text-gray-600 hover:bg-gray-100'); ?>">
                   Semua
                </a>
                <a href="<?php echo e(route('cx.oto.index', ['filter' => 'urgent'])); ?>" 
                   class="px-4 py-2 rounded-lg text-sm font-medium <?php echo e($filter === 'urgent' ? 'bg-red-100 text-red-700' : 'text-gray-600 hover:bg-gray-100'); ?>">
                   🔥 Urgent (< 3 hari)
                </a>
                <a href="<?php echo e(route('cx.oto.index', ['filter' => 'my'])); ?>" 
                   class="px-4 py-2 rounded-lg text-sm font-medium <?php echo e($filter === 'my' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100'); ?>">
                   👋 My OTO
                </a>
            </div>
            
            <form action="<?php echo e(route('cx.oto.index')); ?>" method="GET" class="relative">
                <input type="text" name="search" placeholder="Cari SPK / Customer..." 
                       class="pl-10 pr-4 py-2 border rounded-lg focus:ring-orange-500 focus:border-orange-500">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" class="feather feather-search" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>
            </form>
        </div>

        <!-- OTO List -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $otos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $oto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition duration-200 border border-gray-100 overflow-hidden">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="bg-gray-100 text-gray-800 text-xs font-bold px-2.5 py-0.5 rounded"><?php echo e($oto->workOrder->spk_number); ?></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($oto->status === 'PENDING_CX'): ?>
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2.5 py-0.5 rounded">Perlu Dihubungi</span>
                                <?php else: ?>
                                    <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded">Sudah Dihubungi</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mt-2"><?php echo e($oto->workOrder->customer_name); ?></h3>
                            <p class="text-sm text-gray-500"><?php echo e($oto->workOrder->customer_phone); ?></p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500">Valid Until</div>
                            <div class="text-red-500 font-bold <?php echo e(Carbon\Carbon::parse($oto->valid_until)->diffInDays(now()) < 3 ? 'animate-pulse' : ''); ?>">
                                <?php echo e(Carbon\Carbon::parse($oto->valid_until)->format('d M Y')); ?>

                            </div>
                            <div class="text-xs text-gray-400"><?php echo e(Carbon\Carbon::parse($oto->valid_until)->diffForHumans()); ?></div>
                        </div>
                    </div>

                    <!-- Offer Details -->
                    <div class="bg-orange-50 rounded-lg p-4 mb-4">
                        <div class="text-xs font-bold text-orange-800 mb-2 uppercase tracking-wide">Penawaran</div>
                        <div class="space-y-2">
                            <div class="text-sm font-medium text-gray-700">
                                <?php echo e($oto->proposed_services); ?>

                            </div>
                            <div class="border-t border-orange-200 pt-2 mt-2 flex justify-between items-center">
                                <span class="font-bold text-orange-900">Total</span>
                                <div class="text-right">
                                    <span class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded-full mr-2">Hemat <?php echo e($oto->total_discount); ?></span>
                                    <span class="font-bold text-orange-700 text-lg"><?php echo e($oto->total_oto_price); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-2" x-data="{ openContact: false }">
                        <button @click="openContact = true" 
                            class="flex-1 bg-gradient-to-r from-orange-500 to-pink-500 text-white py-2 rounded-lg font-medium hover:from-orange-600 hover:to-pink-600 transition shadow-sm flex justify-center items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            Hubungi
                        </button>
                        
                        <!-- Contact Modal Component -->
                        <div x-show="openContact" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openContact = false">
                                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                                </div>
                                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                    <form action="<?php echo e(route('cx.oto.contact', $oto->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Log Kontak Customer</h3>
                                            
                                            <!-- Script Template -->
                                            <div class="bg-gray-50 p-3 rounded-lg mb-4 text-sm text-gray-600 relative group">
                                                <p>"Halo Kak <?php echo e($oto->workOrder->customer_name); ?>, sepatu <?php echo e($oto->workOrder->custom_name ?? 'Anda'); ?> sudah selesai nih! Kami ada penawaran spesial OTO <?php echo e($oto->proposed_services); ?> diskon <?php echo e(number_format($oto->discount_percent)); ?>% lho kak. Cuma nambah <?php echo e($oto->total_oto_price); ?> aja. Minat kak?"</p>
                                                <button type="button" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600" onclick="navigator.clipboard.writeText(this.parentElement.querySelector('p').innerText)">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                                </button>
                                            </div>

                                            <div class="mb-4">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Metode Kontak</label>
                                                <select name="contact_method" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500">
                                                    <option value="WHATSAPP">WhatsApp</option>
                                                    <option value="PHONE">Phone Call</option>
                                                    <option value="EMAIL">Email</option>
                                                </select>
                                            </div>

                                            <div class="mb-4">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Respon Customer</label>
                                                <div class="grid grid-cols-2 gap-3">
                                                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-green-50 has-[:checked]:bg-green-50 has-[:checked]:border-green-500">
                                                        <input type="radio" name="customer_response" value="INTERESTED" class="text-green-600 focus:ring-green-500">
                                                        <span class="ml-2 text-sm text-gray-700">Tertarik (Pending)</span>
                                                    </label>
                                                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-yellow-50 has-[:checked]:bg-yellow-50 has-[:checked]:border-yellow-500">
                                                        <input type="radio" name="customer_response" value="NEED_TIME" class="text-yellow-600 focus:ring-yellow-500">
                                                        <span class="ml-2 text-sm text-gray-700">Mikir-mikir</span>
                                                    </label>
                                                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-red-50 has-[:checked]:bg-red-50 has-[:checked]:border-red-500">
                                                        <input type="radio" name="customer_response" value="NOT_INTERESTED" class="text-red-600 focus:ring-red-500">
                                                        <span class="ml-2 text-sm text-gray-700">Tidak Minat</span>
                                                    </label>
                                                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 has-[:checked]:bg-gray-50 has-[:checked]:border-gray-500">
                                                        <input type="radio" name="customer_response" value="NO_ANSWER" class="text-gray-600 focus:ring-gray-500">
                                                        <span class="ml-2 text-sm text-gray-700">Tidak Diangkat</span>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="mb-2">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                                                <textarea name="notes" rows="2" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500" placeholder="Hasil pembicaraan..."></textarea>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                                Simpan Log
                                            </button>
                                            <button type="button" @click="openContact = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                Batal
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Direct Actions -->
                        <div x-data="{ openAccept: false }">
                            <button @click="openAccept = true" class="bg-green-100 text-green-700 p-2 rounded-lg hover:bg-green-200 transition" title="Customer Accept">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </button>
                            
                             <!-- Accept Modal -->
                             <div x-show="openAccept" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                    <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openAccept = false">
                                        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                                    </div>
                                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                        <form action="<?php echo e(route('cx.oto.accept', $oto->id)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                <div class="sm:flex sm:items-start">
                                                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    </div>
                                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                                        <h3 class="text-lg leading-6 font-medium text-gray-900">Konfirmasi Terima OTO</h3>
                                                        <div class="mt-2">
                                                            <p class="text-sm text-gray-500">
                                                                Apakah Anda yakin customer menyetujui penawaran ini? Order akan otomatis ditambahkan layanan dan masuk ke antrian <strong>PRIORITAS (Express)</strong>.
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                                    Ya, Terima Penawaran
                                                </button>
                                                <button type="button" @click="openAccept = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                    Batal
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form action="<?php echo e(route('cx.oto.cancel', $oto->id)); ?>" method="POST" onsubmit="return confirm('Yakin batalkan penawaran ini?')">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="bg-red-100 text-red-700 p-2 rounded-lg hover:bg-red-200 transition" title="Cancel OTO">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- History -->
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($oto->contactLogs->count() > 0): ?>
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
                    <div class="text-xs font-bold text-gray-500 mb-2 uppercase">Riwayat Kontak</div>
                    <div class="space-y-3">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $oto->contactLogs->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <div class="flex text-xs">
                            <div class="w-20 text-gray-400"><?php echo e($log->created_at->format('d/m H:i')); ?></div>
                            <div class="flex-1">
                                <span class="font-medium text-gray-700"><?php echo e($log->contactedBy->name); ?></span>
                                <span class="text-gray-500">: <?php echo e(Str::limit($log->notes, 40)); ?></span>
                            </div>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div class="col-span-2 text-center py-20 bg-white rounded-xl shadow-sm border border-dashed border-gray-300">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Kolam OTO Kosong</h3>
                <p class="mt-1 text-sm text-gray-500">Tidak ada penawaran OTO yang perlu ditangani saat ini.</p>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        
        <!-- Pagination -->
        <div class="mt-8">
            <?php echo e($otos->links()); ?>

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
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\cx\oto\index.blade.php ENDPATH**/ ?>