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

    <div class="min-h-screen bg-[#F8FAFC]">
        
        <div class="bg-white/90 shadow-2xl border-b border-gray-100 sticky top-0 z-40 backdrop-blur-xl">
            <div class="max-w-7xl mx-auto px-6 py-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-8">
                    <div class="flex items-center gap-6">
                        <div class="p-4 bg-blue-600 rounded-[1.5rem] shadow-[0_10px_30px_-10px_rgba(37,99,235,0.5)] transform -rotate-3 hover:rotate-0 transition-transform duration-500">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-3 mb-1">
                                <span class="text-[10px] font-black bg-blue-50 text-blue-600 px-2 py-0.5 rounded-md uppercase tracking-widest italic border border-blue-100">IMPORT</span>
                                <h1 class="text-4xl font-black text-gray-900 tracking-tighter leading-none italic uppercase">Mutasi Bank</h1>
                            </div>
                            <p class="text-gray-400 text-[11px] font-black uppercase tracking-[0.3em] italic opacity-70">Import Data Mutasi Rekening Bank</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <form action="<?php echo e(route('finance.mutations.index')); ?>" method="GET" class="flex items-center gap-3">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($banks->isNotEmpty()): ?>
                            <select name="bank" onchange="this.form.submit()" class="px-5 py-4 bg-gray-50 border-2 border-transparent rounded-[2rem] focus:bg-white focus:border-blue-500/20 focus:ring-4 focus:ring-blue-500/5 text-sm font-black italic tracking-tight text-gray-600 transition-all duration-500 shadow-inner cursor-pointer appearance-none outline-none">
                                <option value="">Semua Bank</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $banks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bank): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <option value="<?php echo e($bank); ?>" <?php echo e(request('bank') === $bank ? 'selected' : ''); ?>><?php echo e($bank); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </select>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <select name="status" onchange="this.form.submit()" class="hidden md:block px-5 py-4 bg-gray-50 border-2 border-transparent rounded-[2rem] focus:bg-white focus:border-blue-500/20 focus:ring-4 focus:ring-blue-500/5 text-sm font-black italic tracking-tight text-gray-600 transition-all duration-500 shadow-inner cursor-pointer appearance-none outline-none">
                                <option value="">Semua Status</option>
                                <option value="unused" <?php echo e(request('status') === 'unused' ? 'selected' : ''); ?>>🟡 Belum Dipakai</option>
                                <option value="used" <?php echo e(request('status') === 'used' ? 'selected' : ''); ?>>✅ Sudah Dipakai</option>
                            </select>
                            <div class="relative w-full md:w-64">
                                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari invoice/ket..." class="w-full pl-12 pr-4 py-4 bg-gray-50 border-2 border-transparent rounded-[2rem] focus:bg-white focus:border-blue-500/20 focus:ring-4 focus:ring-blue-500/5 text-sm font-black italic tracking-tight text-gray-600 transition-all duration-500 shadow-inner outline-none placeholder-gray-400">
                                <svg class="w-5 h-5 text-gray-400 outline-none absolute left-5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                <button type="submit" class="hidden"></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="max-w-7xl mx-auto px-6 pt-6">
            <div class="bg-emerald-50 border-2 border-emerald-200 text-[#1B8A68] px-6 py-4 rounded-2xl font-black text-sm italic flex items-center gap-3 shadow-lg shadow-emerald-50">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <?php echo e(session('success')); ?>

            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
        <div class="max-w-7xl mx-auto px-6 pt-6">
            <div class="bg-red-50 border-2 border-red-200 text-red-700 px-6 py-4 rounded-2xl font-black text-sm italic flex items-center gap-3 shadow-lg shadow-red-50">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <?php echo e(session('error')); ?>

            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="max-w-7xl mx-auto px-6 pt-12">
            <div class="bg-white rounded-[3rem] shadow-2xl border border-gray-100 overflow-hidden relative">
                <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500/5 rounded-bl-[10rem] -mr-16 -mt-16 pointer-events-none"></div>
                
                <div class="relative z-10 p-10">
                    <form action="<?php echo e(route('finance.mutations.import')); ?>" method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row items-center gap-6">
                        <?php echo csrf_field(); ?>
                        <div class="flex-1 w-full">
                            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic mb-3">Upload File Excel / CSV</label>
                            <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="w-full px-6 py-4 bg-gray-50 border-2 border-dashed border-gray-200 rounded-2xl text-sm font-bold text-gray-600 file:mr-4 file:py-2 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:tracking-wider file:bg-blue-600 file:text-white file:cursor-pointer hover:file:bg-blue-700 file:transition-all file:italic transition-all focus:border-blue-500/30 outline-none">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs font-bold mt-2 italic"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="<?php echo e(route('finance.mutations.template')); ?>" class="inline-flex items-center gap-2 px-8 py-5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-[2rem] font-black text-xs uppercase tracking-[0.15em] italic transition-all hover:-translate-y-1 active:scale-95 whitespace-nowrap border border-gray-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Template
                            </a>
                            <button type="submit" class="inline-flex items-center gap-3 px-10 py-5 bg-blue-600 hover:bg-blue-700 text-white rounded-[2rem] font-black text-xs uppercase tracking-[0.2em] italic shadow-xl shadow-blue-100 transition-all hover:-translate-y-1 active:scale-95 whitespace-nowrap">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                Import Mutasi
                            </button>
                        </div>
                    </form>

                    
                    <div class="mt-6 p-5 bg-blue-50/50 rounded-2xl border border-blue-100">
                        <p class="text-[10px] font-black text-blue-600 uppercase tracking-[0.2em] italic mb-2">FORMAT KOLOM EXCEL YANG DIDUKUNG:</p>
                        <div class="flex flex-wrap gap-2">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['tanggal/transaction_date', 'invoice_number/no_invoice', 'amount/nominal', 'keterangan/description', 'bank/bank_code', 'type (CR/DB)']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <span class="px-3 py-1 bg-white border border-blue-100 rounded-lg text-[10px] font-black text-blue-700 italic"><?php echo e($col); ?></span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="max-w-7xl mx-auto px-6 py-12">
            <div class="bg-white rounded-[3rem] shadow-2xl border border-gray-100 overflow-hidden relative">
                <div class="overflow-x-auto relative z-10">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#F8FAFC] border-b border-gray-100">
                                <th class="px-10 py-8 text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic">Tanggal</th>
                                <th class="px-10 py-8 text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic">No. Invoice</th>
                                <th class="px-10 py-8 text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic text-right">Nominal</th>
                                <th class="px-10 py-8 text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic">Bank</th>
                                <th class="px-10 py-8 text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic text-center">Tipe</th>
                                <th class="px-10 py-8 text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic text-center">Status</th>
                                <th class="px-10 py-8 text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $mutations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mutation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <tr class="hover:bg-[#F8FAFC] transition-all duration-300 group">
                                    <td class="px-10 py-6">
                                        <div class="text-sm font-black text-gray-700 italic"><?php echo e($mutation->transaction_date->format('d M Y')); ?></div>
                                    </td>
                                    <td class="px-10 py-6">
                                        <div class="text-sm font-black text-gray-900 italic uppercase tracking-tighter"><?php echo e($mutation->invoice_number ?: '-'); ?></div>
                                    </td>
                                    <td class="px-10 py-6 text-right">
                                        <div class="text-lg font-black italic tabular-nums tracking-tighter <?php echo e($mutation->mutation_type === 'CR' ? 'text-[#1B8A68]' : 'text-red-500'); ?>">
                                            <?php echo e($mutation->mutation_type === 'CR' ? '+' : '-'); ?> Rp <?php echo e(number_format($mutation->amount, 0, ',', '.')); ?>

                                        </div>
                                    </td>
                                    <td class="px-10 py-6">
                                        <span class="text-xs font-black text-gray-500 italic uppercase"><?php echo e($mutation->bank_code ?: '-'); ?></span>
                                    </td>
                                    <td class="px-10 py-6 text-center">
                                        <span class="inline-flex px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest italic <?php echo e($mutation->mutation_type === 'CR' ? 'bg-emerald-50 text-[#1B8A68] border border-emerald-100' : 'bg-red-50 text-red-500 border border-red-100'); ?>">
                                            <?php echo e($mutation->mutation_type); ?>

                                        </span>
                                    </td>
                                    <td class="px-10 py-6 text-center">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mutation->used): ?>
                                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border-2 bg-emerald-50 text-[#1B8A68] border-emerald-100">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#1B8A68]"></span>
                                                <span class="text-[10px] font-black uppercase tracking-[0.15em] italic">Terpakai</span>
                                            </div>
                                        <?php else: ?>
                                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border-2 bg-amber-50 text-amber-600 border-amber-100">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                <span class="text-[10px] font-black uppercase tracking-[0.15em] italic">Tersedia</span>
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td class="px-10 py-6">
                                        <div class="text-xs text-gray-400 italic max-w-[200px] truncate"><?php echo e($mutation->description ?? '-'); ?></div>
                                    </td>
                                </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <tr>
                                    <td colspan="7" class="px-10 py-40 text-center">
                                        <div class="w-32 h-32 bg-[#F8FAFC] rounded-[2.5rem] flex items-center justify-center text-6xl mb-8 shadow-inner border border-gray-100 mx-auto filter grayscale opacity-20">🏦</div>
                                        <h3 class="text-3xl font-black text-gray-900 mb-2 uppercase tracking-tighter italic">Belum Ada Mutasi</h3>
                                        <p class="text-gray-400 text-[11px] font-black uppercase tracking-[0.3em] italic opacity-60">Upload file Excel untuk mengimport data mutasi bank</p>
                                    </td>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mutations->hasPages()): ?>
                <div class="px-10 py-10 border-t border-gray-50 bg-[#F8FAFC]/50 flex justify-center">
                    <?php echo e($mutations->links()); ?>

                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\finance\mutations\import.blade.php ENDPATH**/ ?>