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
            <div class="max-w-4xl mx-auto px-6 py-8">
                <div class="flex items-center gap-6">
                    <a href="<?php echo e(route('finance.payments.index')); ?>" class="w-12 h-12 rounded-full bg-gray-50 border-2 border-gray-100 text-gray-400 hover:text-[#1B8A68] hover:border-[#1B8A68]/30 hover:shadow-lg transition-all flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                    </a>
                    <div class="p-4 bg-[#1B8A68] rounded-[1.5rem] shadow-[0_10px_30px_-10px_rgba(27,138,104,0.5)] transform -rotate-3 hover:rotate-0 transition-transform duration-500">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <span class="text-[10px] font-black bg-emerald-50 text-[#1B8A68] px-2 py-0.5 rounded-md uppercase tracking-widest italic border border-emerald-100">BARU</span>
                            <h1 class="text-4xl font-black text-gray-900 tracking-tighter leading-none italic uppercase">Input Pembayaran</h1>
                        </div>
                        <p class="text-gray-400 text-[11px] font-black uppercase tracking-[0.3em] italic opacity-70">Catat Pembayaran Manual Dari Pelanggan</p>
                    </div>
                </div>
            </div>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
        <div class="max-w-4xl mx-auto px-6 pt-6">
            <div class="bg-red-50 border-2 border-red-200 text-red-700 px-6 py-4 rounded-2xl font-black text-sm italic flex items-center gap-3 shadow-lg shadow-red-50">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <?php echo e(session('error')); ?>

            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="max-w-4xl mx-auto px-6 py-12">
            <div class="bg-white rounded-[3rem] shadow-2xl border border-gray-100 overflow-hidden relative">
                <div class="absolute top-0 right-0 w-64 h-64 bg-[#1B8A68]/5 rounded-bl-[10rem] -mr-16 -mt-16 pointer-events-none"></div>

                <form action="<?php echo e(route('finance.payments.store')); ?>" method="POST" class="relative z-10 p-10 space-y-8">
                    <?php echo csrf_field(); ?>

                    
                    <div>
                        <label for="invoice_id" class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic mb-3">Pilih Invoice <span class="text-red-400">*</span></label>
                        <select name="invoice_id" id="invoice_id" required class="w-full px-6 py-5 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-[#1B8A68]/20 focus:ring-4 focus:ring-[#1B8A68]/5 text-sm font-black italic tracking-tight text-gray-700 transition-all duration-500 shadow-inner outline-none appearance-none">
                            <option value="">-- Pilih Invoice --</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <option value="<?php echo e($invoice->id); ?>" <?php echo e(old('invoice_id') == $invoice->id ? 'selected' : ''); ?>>
                                    <?php echo e($invoice->invoice_number); ?> — <?php echo e($invoice->customer->name ?? 'N/A'); ?> — Sisa: Rp <?php echo e(number_format($invoice->remaining_balance, 0, ',', '.')); ?>

                                </option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['invoice_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs font-bold mt-2 italic"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <div>
                        <label for="amount" class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic mb-3">Jumlah Pembayaran (Rp) <span class="text-red-400">*</span></label>
                        <input type="number" name="amount" id="amount" value="<?php echo e(old('amount')); ?>" required min="1" step="1" placeholder="Masukkan nominal..." class="w-full px-6 py-5 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-[#1B8A68]/20 focus:ring-4 focus:ring-[#1B8A68]/5 text-xl font-black italic tracking-tight text-gray-900 transition-all duration-500 shadow-inner outline-none tabular-nums placeholder-gray-300">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs font-bold mt-2 italic"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <div>
                            <label for="payment_date" class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic mb-3">Tanggal Pembayaran <span class="text-red-400">*</span></label>
                            <input type="date" name="payment_date" id="payment_date" value="<?php echo e(old('payment_date', date('Y-m-d'))); ?>" required class="w-full px-6 py-5 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-[#1B8A68]/20 focus:ring-4 focus:ring-[#1B8A68]/5 text-sm font-black italic tracking-tight text-gray-700 transition-all duration-500 shadow-inner outline-none">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['payment_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs font-bold mt-2 italic"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        
                        <div>
                            <label for="payment_method" class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic mb-3">Metode Bayar <span class="text-red-400">*</span></label>
                            <div class="relative">
                                <select name="payment_method" id="payment_method" required class="w-full px-6 py-5 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-[#1B8A68]/20 focus:ring-4 focus:ring-[#1B8A68]/5 text-sm font-black italic tracking-tight text-gray-700 transition-all duration-500 shadow-inner outline-none appearance-none">
                                    <option value="BCA" <?php echo e(old('payment_method') == 'BCA' ? 'selected' : ''); ?>>Transfer BCA</option>
                                    <option value="MANDIRI" <?php echo e(old('payment_method') == 'MANDIRI' ? 'selected' : ''); ?>>Transfer Mandiri</option>
                                    <option value="QRIS" <?php echo e(old('payment_method') == 'QRIS' ? 'selected' : ''); ?>>QRIS</option>
                                    <option value="TUNAI" <?php echo e(old('payment_method') == 'TUNAI' ? 'selected' : ''); ?>>Tunai / Cash (Otomatis Verified)</option>
                                    <option value="EDC" <?php echo e(old('payment_method') == 'EDC' ? 'selected' : ''); ?>>Mesin EDC</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-6 pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs font-bold mt-2 italic"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    
                    <div>
                        <label for="notes" class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic mb-3">Catatan <span class="text-gray-300">(Opsional)</span></label>
                        <textarea name="notes" id="notes" rows="3" placeholder="Contoh: DP via BCA, Transfer Mandiri, dsb..." class="w-full px-6 py-5 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-[#1B8A68]/20 focus:ring-4 focus:ring-[#1B8A68]/5 text-sm font-black italic tracking-tight text-gray-700 transition-all duration-500 shadow-inner outline-none resize-none placeholder-gray-300"><?php echo e(old('notes')); ?></textarea>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs font-bold mt-2 italic"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <div class="flex items-center justify-end gap-4 pt-4">
                        <a href="<?php echo e(route('finance.payments.index')); ?>" class="px-8 py-4 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-[2rem] font-black text-xs uppercase tracking-[0.2em] italic transition-all">Batal</a>
                        <button type="submit" class="inline-flex items-center gap-4 px-10 py-4 bg-[#1B8A68] hover:bg-[#157A5C] text-white rounded-[2rem] font-black text-xs uppercase tracking-[0.2em] italic shadow-xl shadow-emerald-100 transition-all hover:-translate-y-1 active:scale-95">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            Simpan Pembayaran
                        </button>
                    </div>
                </form>
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
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\finance\payments\create.blade.php ENDPATH**/ ?>