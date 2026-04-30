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

    <div class="min-h-screen bg-white">
        
        <div class="bg-white shadow-xl border-b border-gray-100 sticky top-0 z-30 backdrop-blur-md bg-white/90">
            <div class="max-w-7xl mx-auto px-6 py-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    
                    <div class="flex items-center gap-5">
                        <div class="p-3.5 bg-gradient-to-br from-rose-600 to-rose-800 rounded-2xl shadow-rose-200/50 shadow-lg border border-rose-600/20 transform transition-transform hover:rotate-3 group">
                            <svg class="w-8 h-8 text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-black text-gray-900 tracking-tight leading-none italic">Arsip Donasi</h1>
                            <p class="text-gray-500 text-xs mt-1.5 font-black uppercase tracking-widest italic opacity-70">Sektor: Aset Tak Terklaim & Protokol Terhenti</p>
                        </div>
                    </div>
                    
                    
                    <div class="flex items-center gap-4">
                        <a href="<?php echo e(route('finance.index')); ?>" class="group relative inline-flex items-center gap-2.5 px-6 py-3 bg-gray-900 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] italic shadow-xl hover:shadow-gray-200 transition-all hover:-translate-y-1">
                            <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Exit Archive
                        </a>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="flex flex-wrap gap-6">
                
                <div class="flex-1 min-w-[200px] bg-white rounded-3xl p-6 border border-gray-100 shadow-xl shadow-gray-100/50 group hover:border-rose-200 transition-all duration-500">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest italic group-hover:text-rose-600 transition-colors">Protokol Terarsip</div>
                        <div class="p-2 bg-rose-50 rounded-xl text-rose-500 group-hover:bg-rose-500 group-hover:text-white transition-all duration-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        </div>
                    </div>
                    <div class="text-3xl font-black text-gray-900 tracking-tighter italic tabular-nums"><?php echo e($stats['total_archived'] ?? 0); ?></div>
                </div>

                
                <div class="flex-[2] min-w-[300px] bg-gray-900 rounded-3xl p-6 shadow-2xl shadow-rose-900/10 group hover:scale-[1.01] transition-all duration-500 relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-32 h-32 bg-rose-500/10 rounded-full blur-2xl"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-[10px] font-black text-rose-400 uppercase tracking-widest italic">Estimasi Nilai Terhenti</div>
                            <div class="p-2 bg-white/10 rounded-xl text-rose-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        </div>
                        <div class="text-3xl font-black text-white tracking-tighter italic tabular-nums">Rp <?php echo e(number_format($stats['total_value'] ?? 0, 0, ',', '.')); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 py-12">
            <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-gray-100 group">
                <div class="p-1">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($orders->isEmpty()): ?>
                        <div class="text-center py-40 relative overflow-hidden">
                            <div class="absolute -top-12 -left-12 w-64 h-64 bg-emerald-50 rounded-full blur-3xl opacity-50"></div>
                            <div class="relative z-10 flex flex-col items-center justify-center">
                                <div class="w-28 h-28 bg-white rounded-[3rem] shadow-2xl border border-gray-50 flex items-center justify-center text-5xl mb-8 group hover:scale-110 transition-transform duration-700">
                                    ✅
                                </div>
                                <span class="font-black text-gray-900 text-3xl uppercase tracking-tighter italic">Arsip Optimal</span>
                                <p class="text-gray-400 text-xs mt-4 max-w-xs font-black uppercase tracking-widest leading-loose italic opacity-60 text-center">Tidak ada aset terbengkalai yang terdeteksi. Semua protokol saat ini aktif atau sudah selesai.</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto overflow-hidden rounded-[2rem]">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-900/5 border-b border-gray-100">
                                        <th class="px-8 py-7 text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] italic">ID Protokol & Entitas</th>
                                        <th class="px-8 py-7 text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] italic">Spesifikasi Objek</th>
                                        <th class="px-8 py-7 text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] text-right italic">Nilai Terhenti</th>
                                        <th class="px-8 py-7 text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] text-right italic">Waktu Arsip</th>
                                        <th class="px-8 py-7 text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] text-center italic">Pemulihan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50/50">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <tr class="hover:bg-rose-50/30 transition-all duration-500 group relative">
                                        <td class="px-8 py-8 relative">
                                            <div class="absolute left-0 top-0 w-1 h-full bg-rose-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                            <div class="flex flex-col gap-2">
                                                <div class="font-black text-gray-900 text-xl leading-none tracking-tighter italic group-hover:text-rose-600 group-hover:translate-x-1 transition-all">
                                                    <?php echo e($order->spk_number); ?>

                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-gray-200 group-hover:bg-rose-500 transition-colors"></div>
                                                    <div class="font-black text-gray-500 group-hover:text-gray-900 transition-all uppercase tracking-tight text-[11px] leading-none italic"><?php echo e($order->customer_name); ?></div>
                                                </div>
                                                <div class="text-[9px] text-gray-400 font-black uppercase tracking-[0.2em] italic opacity-60">
                                                    Kontak: <?php echo e($order->customer_phone); ?>

                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-8">
                                            <div class="flex flex-col gap-1.5">
                                                <div class="text-sm font-black text-gray-800 italic uppercase tracking-tight group-hover:text-gray-900 transition-colors">
                                                    <?php echo e($order->shoe_brand); ?> <?php echo e($order->shoe_type); ?>

                                                </div>
                                                <div class="inline-flex items-center gap-2 px-2.5 py-1 bg-gray-50 rounded-lg border border-gray-100 w-fit group-hover:bg-white transition-colors">
                                                    <span class="w-2 h-2 rounded-full bg-gray-300 group-hover:bg-rose-400 transition-colors"></span>
                                                    <span class="text-[9px] font-black uppercase text-gray-400 group-hover:text-gray-600 transition-colors tracking-widest"><?php echo e($order->shoe_color); ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-8 text-right">
                                            <div class="font-black text-rose-600 text-lg tracking-tighter italic group-hover:scale-110 transition-transform origin-right">
                                                Rp <?php echo e(number_format($order->sisa_tagihan, 0, ',', '.')); ?>

                                            </div>
                                            <div class="text-[9px] text-gray-400 font-black uppercase tracking-widest mt-1 italic">
                                                Total Nilai: Rp <?php echo e(number_format($order->total_transaksi, 0, ',', '.')); ?>

                                            </div>
                                        </td>
                                        <td class="px-8 py-8 text-right">
                                            <div class="text-sm font-black text-gray-900 italic tracking-tight"><?php echo e($order->donated_at ? $order->donated_at->format('M d, Y') : '-'); ?></div>
                                            <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1 italic">
                                                <?php echo e($order->donated_at ? $order->donated_at->diffForHumans() : ''); ?>

                                            </div>
                                        </td>
                                        <td class="px-8 py-8 text-center">
                                            <form action="<?php echo e(route('finance.donations.restore', $order->id)); ?>" method="POST" onsubmit="return confirm('Restore this protocol to active sector?');">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="inline-flex items-center gap-3 px-6 py-3 bg-white border border-gray-100 hover:bg-emerald-50 hover:border-emerald-200 hover:text-emerald-700 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] italic text-gray-400 shadow-sm hover:shadow-emerald-100 hover:scale-110 active:scale-95 transition-all focus:outline-none group/restore">
                                                    <svg class="w-4 h-4 group-hover/restore:rotate-180 transition-transform duration-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                    </svg>
                                                    Pulihkan Protokol
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($orders->hasPages()): ?>
                        <div class="mt-8 px-8 py-6 border-t border-gray-50 bg-gray-50/50">
                            <?php echo e($orders->links()); ?>

                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\finance\donations\index.blade.php ENDPATH**/ ?>