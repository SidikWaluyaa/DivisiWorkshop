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
                <div class="flex items-center gap-6">
                    <div class="p-4 bg-purple-600 rounded-[1.5rem] shadow-[0_10px_30px_-10px_rgba(147,51,234,0.5)] transform -rotate-3 hover:rotate-0 transition-transform duration-500">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <span class="text-[10px] font-black bg-purple-50 text-purple-600 px-2 py-0.5 rounded-md uppercase tracking-widest italic border border-purple-100">REKONSILIASI</span>
                            <h1 class="text-4xl font-black text-gray-900 tracking-tighter leading-none italic uppercase">Verifikasi Mutasi</h1>
                        </div>
                        <p class="text-gray-400 text-[11px] font-black uppercase tracking-[0.3em] italic opacity-70">Cocokkan Pembayaran Manual Dengan Mutasi Bank</p>
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

        
        <div class="max-w-7xl mx-auto px-6 pt-8">
            <div class="flex flex-wrap gap-4">
                <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-xl border border-gray-100 shadow-sm">
                    <span class="w-3 h-3 rounded-full bg-purple-500"></span>
                    <span class="text-[10px] font-black text-gray-600 uppercase tracking-wider italic">Exact Match — Auto</span>
                </div>
                <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-xl border border-gray-100 shadow-sm">
                    <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                    <span class="text-[10px] font-black text-gray-600 uppercase tracking-wider italic">Invoice Sama, Nominal Beda — Pilih Manual</span>
                </div>
                <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-xl border border-gray-100 shadow-sm">
                    <span class="w-3 h-3 rounded-full bg-gray-300"></span>
                    <span class="text-[10px] font-black text-gray-600 uppercase tracking-wider italic">Tidak Ada Mutasi — Belum Bisa Verifikasi</span>
                </div>
            </div>
        </div>

        
        <div class="max-w-7xl mx-auto px-6 pt-6">
            <form action="<?php echo e(route('finance.verifications.index')); ?>" method="GET" class="flex flex-col md:flex-row items-center justify-between gap-4">
                
                <div class="flex flex-wrap items-center gap-4 w-full md:w-auto">
                    <select name="match_type" onchange="this.form.submit()" class="px-5 py-4 bg-white border-2 border-gray-100 rounded-xl focus:border-purple-500/30 focus:ring-4 focus:ring-purple-500/10 text-[11px] font-black uppercase tracking-wider italic text-gray-600 transition-all duration-300 shadow-sm outline-none appearance-none cursor-pointer">
                        <option value="">Semua Tipe Match</option>
                        <option value="exact" <?php echo e(request('match_type') === 'exact' ? 'selected' : ''); ?>>🟣 Exact Match (Auto)</option>
                        <option value="partial" <?php echo e(request('match_type') === 'partial' ? 'selected' : ''); ?>>🟡 Partial Match (Manual)</option>
                        <option value="none" <?php echo e(request('match_type') === 'none' ? 'selected' : ''); ?>>⚪ Tidak Ada Match</option>
                    </select>
                </div>

                
                <div class="relative w-full md:w-80">
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari No. Invoice / Pelanggan..." class="w-full pl-12 pr-4 py-4 bg-white border-2 border-gray-100 rounded-xl focus:border-purple-500/30 focus:ring-4 focus:ring-purple-500/10 text-sm font-black italic tracking-tight text-gray-700 transition-all duration-300 shadow-sm outline-none placeholder-gray-400">
                    <svg class="w-5 h-5 text-gray-400 outline-none absolute left-5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <button type="submit" class="hidden"></button>
                </div>
            </form>
        </div>

        
        <div class="max-w-7xl mx-auto px-6 py-6">
            <div class="space-y-6">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $candidates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $candidate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <?php
                        $payment = $candidate['payment'];
                        $matchType = $candidate['match_type'];
                        $mutation = $candidate['mutation'];
                        $partialMutations = $candidate['partial_mutations'];

                        $borderColor = match($matchType) {
                            'exact' => 'border-l-purple-500 bg-purple-50/20',
                            'partial' => 'border-l-amber-500 bg-amber-50/20',
                            default => 'border-l-gray-300',
                        };
                    ?>
                    <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 border-l-4 <?php echo e($borderColor); ?> shadow-2xl overflow-hidden relative">
                        <div class="flex flex-col lg:flex-row gap-8">
                            
                            <div class="flex-1">
                                <div class="flex items-center gap-4 mb-5">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-inner <?php echo e($matchType === 'exact' ? 'bg-purple-100 text-purple-600' : ($matchType === 'partial' ? 'bg-amber-100 text-amber-600' : 'bg-gray-100 text-gray-400')); ?>">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <div>
                                        <div class="text-sm font-black text-gray-900 italic uppercase tracking-tighter"><?php echo e($payment->invoice->invoice_number ?? '-'); ?></div>
                                        <div class="text-[10px] text-gray-400 font-black uppercase tracking-widest italic opacity-60"><?php echo e($payment->invoice->customer->name ?? '-'); ?></div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-3 gap-4 p-5 bg-gray-50 rounded-2xl border border-gray-100">
                                    <div>
                                        <span class="text-[10px] text-gray-400 font-black uppercase tracking-wider italic block mb-1">Pembayaran</span>
                                        <span class="text-lg font-black text-gray-900 italic tabular-nums tracking-tighter">Rp <?php echo e(number_format($payment->amount, 0, ',', '.')); ?></span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-gray-400 font-black uppercase tracking-wider italic block mb-1">Tanggal</span>
                                        <span class="text-sm font-black text-gray-700 italic"><?php echo e($payment->payment_date->format('d M Y')); ?></span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-gray-400 font-black uppercase tracking-wider italic block mb-1">Oleh</span>
                                        <span class="text-sm font-black text-gray-700 italic"><?php echo e($payment->creator->name ?? '-'); ?></span>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="hidden lg:flex items-center">
                                <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                </div>
                            </div>

                            
                            <div class="flex-1">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($matchType === 'exact'): ?>
                                    
                                    <div class="p-6 bg-purple-50 rounded-2xl border-2 border-purple-200 mb-4">
                                        <div class="flex items-center gap-2 mb-3">
                                            <span class="w-2 h-2 rounded-full bg-purple-500 animate-pulse"></span>
                                            <span class="text-[10px] font-black text-purple-700 uppercase tracking-[0.2em] italic">Exact Match!</span>
                                        </div>
                                        <div class="space-y-2">
                                            <div class="flex justify-between"><span class="text-[10px] text-gray-500 font-black italic uppercase tracking-wider">Mutasi</span><span class="text-sm font-black text-purple-700 italic tabular-nums">Rp <?php echo e(number_format($mutation->amount, 0, ',', '.')); ?></span></div>
                                            <div class="flex justify-between"><span class="text-[10px] text-gray-500 font-black italic uppercase tracking-wider">Bank</span><span class="text-xs font-black text-gray-700 italic"><?php echo e($mutation->bank_code ?: '-'); ?></span></div>
                                            <div class="flex justify-between"><span class="text-[10px] text-gray-500 font-black italic uppercase tracking-wider">Tanggal</span><span class="text-xs font-black text-gray-700 italic"><?php echo e($mutation->transaction_date->format('d M Y')); ?></span></div>
                                        </div>
                                    </div>
                                    <form action="<?php echo e(route('finance.verifications.verify', $payment->id)); ?>" method="POST" onsubmit="return confirm('Verifikasi pembayaran ini?')">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="mutation_id" value="<?php echo e($mutation->id); ?>">
                                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 bg-purple-600 hover:bg-purple-700 text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.15em] italic shadow-lg shadow-purple-100 transition-all hover:-translate-y-0.5 active:scale-95">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                            Verify (Auto Match)
                                        </button>
                                    </form>

                                <?php elseif($matchType === 'partial'): ?>
                                    
                                    <div class="p-6 bg-amber-50 rounded-2xl border-2 border-amber-200 mb-4">
                                        <div class="flex items-center gap-2 mb-3">
                                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                            <span class="text-[10px] font-black text-amber-700 uppercase tracking-[0.2em] italic">Invoice Cocok — Nominal Berbeda</span>
                                        </div>
                                        <p class="text-[10px] text-amber-800/70 italic font-bold leading-relaxed">Pilih mutasi yang sesuai dari daftar di bawah. Selisih nominal akan dicatat otomatis.</p>
                                    </div>

                                    <form action="<?php echo e(route('finance.verifications.verify', $payment->id)); ?>" method="POST" onsubmit="return confirm('Nominal berbeda! Yakin ingin verifikasi dengan mutasi ini? Selisih akan dicatat di log.')">
                                        <?php echo csrf_field(); ?>
                                        <div class="mb-4">
                                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] italic mb-2">Pilih Mutasi</label>
                                            <select name="mutation_id" required class="w-full px-5 py-4 bg-white border-2 border-amber-200 rounded-xl text-sm font-bold italic tracking-tight text-gray-700 focus:border-amber-400 focus:ring-2 focus:ring-amber-100 outline-none appearance-none">
                                                <option value="">— Pilih mutasi —</option>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $partialMutations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                    <?php $selisih = abs((float)$payment->amount - (float)$pm->amount); ?>
                                                    <option value="<?php echo e($pm->id); ?>">
                                                        Rp <?php echo e(number_format($pm->amount, 0, ',', '.')); ?> • <?php echo e($pm->transaction_date->format('d/m/Y')); ?> • <?php echo e($pm->bank_code ?: '-'); ?>

                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selisih > 0): ?> (selisih Rp <?php echo e(number_format($selisih, 0, ',', '.')); ?>) <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </option>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                            </select>
                                        </div>
                                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 bg-amber-500 hover:bg-amber-600 text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.15em] italic shadow-lg shadow-amber-100 transition-all hover:-translate-y-0.5 active:scale-95">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                            Verify Manual (Nominal Beda)
                                        </button>
                                    </form>

                                <?php else: ?>
                                    
                                    <div class="p-6 bg-gray-50 rounded-2xl border-2 border-gray-200 h-full flex flex-col items-center justify-center text-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center text-3xl mb-4 opacity-40">🔍</div>
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] italic block mb-2">Tidak Ada Mutasi yang Cocok</span>
                                        <p class="text-[10px] text-gray-400 italic font-bold leading-relaxed max-w-[250px]">Belum ada data mutasi bank dengan nomor invoice <strong class="text-gray-600"><?php echo e($payment->invoice->invoice_number ?? '-'); ?></strong>. Import mutasi terlebih dahulu.</p>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="bg-white rounded-[3rem] shadow-2xl border border-gray-100 overflow-hidden px-10 py-40 text-center">
                        <div class="w-32 h-32 bg-[#F8FAFC] rounded-[2.5rem] flex items-center justify-center text-6xl mb-8 shadow-inner border border-gray-100 mx-auto filter grayscale opacity-20">🛡️</div>
                        <h3 class="text-3xl font-black text-gray-900 mb-2 uppercase tracking-tighter italic">Semua Terverifikasi</h3>
                        <p class="text-gray-400 text-[11px] font-black uppercase tracking-[0.3em] italic opacity-60">Tidak ada pembayaran transfer yang menunggu verifikasi</p>
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
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\finance\verifications\index.blade.php ENDPATH**/ ?>