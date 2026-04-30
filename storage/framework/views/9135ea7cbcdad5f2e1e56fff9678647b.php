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
                        <div class="p-4 bg-[#1B8A68] rounded-[1.5rem] shadow-[0_10px_30px_-10px_rgba(27,138,104,0.5)] transform -rotate-3 hover:rotate-0 transition-transform duration-500">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-3 mb-1">
                                <span class="text-[10px] font-black bg-emerald-50 text-[#1B8A68] px-2 py-0.5 rounded-md uppercase tracking-widest italic border border-emerald-100">RIWAYAT</span>
                                <h1 class="text-4xl font-black text-gray-900 tracking-tighter leading-none italic uppercase">Input Pembayaran</h1>
                            </div>
                            <p class="text-gray-400 text-[11px] font-black uppercase tracking-[0.3em] italic opacity-70">Daftar Pembayaran Manual Invoice</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <form action="<?php echo e(route('finance.payments.index')); ?>" method="GET" class="flex flex-wrap items-center gap-3">
                            
                            <div class="flex items-center gap-2 px-6 py-2 bg-gray-50 border-2 border-transparent rounded-[2rem] shadow-inner">
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-tighter italic">Dari</span>
                                    <input type="date" name="start_date" value="<?php echo e(request('start_date')); ?>" onchange="this.form.submit()" class="bg-transparent border-none p-0 text-xs font-black italic text-gray-600 focus:ring-0">
                                </div>
                                <div class="w-px h-6 bg-gray-200 mx-1"></div>
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-tighter italic">Sampai</span>
                                    <input type="date" name="end_date" value="<?php echo e(request('end_date')); ?>" onchange="this.form.submit()" class="bg-transparent border-none p-0 text-xs font-black italic text-gray-600 focus:ring-0">
                                </div>
                            </div>

                            <select name="status" onchange="this.form.submit()" class="px-5 py-4 bg-gray-50 border-2 border-transparent rounded-[2rem] focus:bg-white focus:border-[#1B8A68]/20 focus:ring-4 focus:ring-[#1B8A68]/5 text-sm font-black italic tracking-tight text-gray-600 transition-all duration-500 shadow-inner cursor-pointer appearance-none outline-none">
                                <option value="">Semua Status</option>
                                <option value="verified" <?php echo e(request('status') === 'verified' ? 'selected' : ''); ?>>✅ Terverifikasi</option>
                                <option value="unverified" <?php echo e(request('status') === 'unverified' ? 'selected' : ''); ?>>⏳ Belum Verifikasi</option>
                            </select>

                            <div class="relative group/search">
                                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="No. Invoice..." class="pl-14 pr-6 py-4 bg-gray-50 border-2 border-transparent rounded-[2rem] focus:bg-white focus:border-[#1B8A68]/20 focus:ring-4 focus:ring-[#1B8A68]/5 text-sm font-black italic tracking-tight placeholder-gray-300 w-48 transition-all duration-500 shadow-inner">
                                <svg class="w-5 h-5 text-gray-300 absolute left-6 top-1/2 -translate-y-1/2 group-focus-within/search:text-[#1B8A68] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->anyFilled(['start_date', 'end_date', 'status', 'search'])): ?>
                                <a href="<?php echo e(route('finance.payments.index')); ?>" class="p-4 bg-red-50 text-red-500 rounded-full hover:bg-red-100 transition-colors shadow-inner" title="Reset Filter">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </form>

                        <a href="<?php echo e(route('finance.payments.create')); ?>" class="group relative inline-flex items-center gap-4 px-8 py-4 bg-[#FFC232] hover:bg-[#FFD666] text-gray-900 rounded-[2rem] font-black text-xs uppercase tracking-[0.2em] italic shadow-xl shadow-amber-100 transition-all hover:-translate-y-1 active:scale-95">
                            <span>Input Pembayaran</span>
                            <div class="w-6 h-6 rounded-full bg-black/5 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M12 4v16m8-8H4"></path></svg>
                            </div>
                        </a>
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

        
        <div class="max-w-7xl mx-auto px-6 py-12">
            <div class="bg-white rounded-[3rem] shadow-2xl border border-gray-100 overflow-hidden relative">
                <div class="absolute top-0 right-0 w-64 h-64 bg-[#1B8A68]/5 rounded-bl-[10rem] -mr-16 -mt-16 pointer-events-none"></div>
                
                <div class="overflow-x-auto relative z-10">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#F8FAFC] border-b border-gray-100">
                                <th class="px-10 py-8 text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic">No. Invoice</th>
                                <th class="px-10 py-8 text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic text-right">Jumlah Bayar</th>
                                <th class="px-10 py-8 text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic text-center">Tanggal</th>
                                <th class="px-10 py-8 text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic text-center">Status</th>
                                <th class="px-10 py-8 text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic">Dicatat Oleh</th>
                                <th class="px-10 py-8 text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <tr class="hover:bg-[#F8FAFC] transition-all duration-300 group">
                                    <td class="px-10 py-8">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 bg-gray-50 text-gray-400 rounded-xl group-hover:bg-[#1B8A68] group-hover:text-white transition-all duration-500 flex items-center justify-center shadow-inner">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </div>
                                            <div>
                                                <div class="text-sm font-black text-gray-900 italic uppercase tracking-tighter group-hover:text-[#1B8A68] transition-colors"><?php echo e($payment->invoice->invoice_number ?? '-'); ?></div>
                                                <div class="text-[10px] text-gray-400 font-black uppercase tracking-widest italic opacity-60"><?php echo e($payment->invoice->customer->name ?? '-'); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-10 py-8 text-right">
                                        <div class="text-xl font-black text-gray-900 italic tabular-nums tracking-tighter">Rp <?php echo e(number_format($payment->amount, 0, ',', '.')); ?></div>
                                    </td>
                                    <td class="px-10 py-8 text-center">
                                        <div class="text-xs font-black text-gray-600 italic tracking-tight uppercase"><?php echo e($payment->payment_date->format('d M Y')); ?></div>
                                    </td>
                                    <td class="px-10 py-8 text-center">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment->verified): ?>
                                            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl border-2 bg-emerald-50 text-[#1B8A68] border-emerald-100 shadow-sm">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#1B8A68]"></span>
                                                <span class="text-[11px] font-black uppercase tracking-[0.2em] italic">Verified</span>
                                            </div>
                                        <?php else: ?>
                                            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl border-2 bg-amber-50 text-amber-600 border-amber-100 shadow-sm">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                <span class="text-[11px] font-black uppercase tracking-[0.2em] italic">Pending</span>
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td class="px-10 py-8">
                                        <div class="text-xs font-black text-gray-600 italic uppercase tracking-tight"><?php echo e($payment->creator->name ?? '-'); ?></div>
                                    </td>
                                    <td class="px-10 py-8">
                                        <div class="text-xs text-gray-400 italic max-w-[200px] truncate"><?php echo e($payment->notes ?? '-'); ?></div>
                                    </td>
                                </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <tr>
                                    <td colspan="6" class="px-10 py-40 text-center">
                                        <div class="w-32 h-32 bg-[#F8FAFC] rounded-[2.5rem] flex items-center justify-center text-6xl mb-8 shadow-inner border border-gray-100 mx-auto filter grayscale opacity-20">💰</div>
                                        <h3 class="text-3xl font-black text-gray-900 mb-2 uppercase tracking-tighter italic">Belum Ada Data</h3>
                                        <p class="text-gray-400 text-[11px] font-black uppercase tracking-[0.3em] italic opacity-60">Belum ada pembayaran yang dicatat</p>
                                    </td>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payments->hasPages()): ?>
                <div class="px-10 py-10 border-t border-gray-50 bg-[#F8FAFC]/50 flex justify-center">
                    <?php echo e($payments->links()); ?>

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
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\finance\payments\index.blade.php ENDPATH**/ ?>