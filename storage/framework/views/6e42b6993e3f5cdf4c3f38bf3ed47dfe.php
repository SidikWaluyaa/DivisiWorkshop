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
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-5 sm:py-8">
                <div class="flex flex-col gap-5 sm:gap-8">
                    
                    <div class="flex items-center justify-between">
                        
                        <div class="flex items-center gap-3 sm:gap-6">
                            <div class="p-2.5 sm:p-4 bg-[#1B8A68] rounded-xl sm:rounded-[1.5rem] shadow-[0_10px_30px_-10px_rgba(27,138,104,0.5)] transform -rotate-3 hover:rotate-0 transition-transform duration-500">
                                <svg class="w-5 h-5 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 sm:gap-3 mb-0.5 sm:mb-1">
                                    <span class="text-[8px] sm:text-[10px] font-black bg-emerald-50 text-[#1B8A68] px-1.5 sm:px-2 py-0.5 rounded-md uppercase tracking-widest italic border border-emerald-100">DATA ARSIP</span>
                                    <h1 class="text-xl sm:text-4xl font-black text-gray-900 tracking-tighter leading-none italic uppercase">Sentral Invoice</h1>
                                </div>
                                <p class="text-gray-400 text-[9px] sm:text-[11px] font-black uppercase tracking-[0.2em] sm:tracking-[0.3em] italic opacity-70 hidden sm:block">Manajemen Tagihan Gabungan Terintegrasi</p>
                            </div>
                        </div>

                        
                        <a href="<?php echo e(route('finance.invoices.create')); ?>" class="group relative inline-flex items-center gap-2 sm:gap-4 px-4 sm:px-8 py-3 sm:py-4 bg-[#FFC232] hover:bg-[#FFD666] text-gray-900 rounded-xl sm:rounded-[2rem] font-black text-[10px] sm:text-xs uppercase tracking-[0.1em] sm:tracking-[0.2em] italic shadow-xl shadow-amber-100 transition-all hover:-translate-y-1 active:scale-95">
                            <span class="hidden sm:inline">Buat Invoice Baru</span>
                            <span class="sm:hidden">Buat</span>
                            <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-full bg-black/5 flex items-center justify-center">
                                <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M12 4v16m8-8H4"></path></svg>
                            </div>
                        </a>
                    </div>

                    
                    <form action="<?php echo e(route('finance.invoices.index')); ?>" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 sm:gap-3">
                        
                        <select name="gateway" onchange="this.form.submit()" class="px-4 sm:px-5 py-3 sm:py-4 bg-gray-50 border-2 border-transparent rounded-xl sm:rounded-[2rem] focus:bg-white focus:border-[#1B8A68]/20 focus:ring-4 focus:ring-[#1B8A68]/5 text-sm font-black italic tracking-tight text-gray-600 transition-all duration-500 shadow-inner cursor-pointer appearance-none outline-none">
                            <option value="" class="font-bold">Semua Gateway</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $gateways; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gw): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <option value="<?php echo e($gw); ?>" <?php echo e(request('gateway') === $gw ? 'selected' : ''); ?> class="font-bold">🖥️ Gateway: <?php echo e($gw); ?></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>

                        
                        <select name="payment_status" onchange="this.form.submit()" class="px-4 sm:px-5 py-3 sm:py-4 bg-gray-50 border-2 border-transparent rounded-xl sm:rounded-[2rem] focus:bg-white focus:border-[#1B8A68]/20 focus:ring-4 focus:ring-[#1B8A68]/5 text-sm font-black italic tracking-tight text-gray-600 transition-all duration-500 shadow-inner cursor-pointer appearance-none outline-none">
                            <option value="" class="font-bold">Semua Pembayaran</option>
                            <option value="Belum Bayar" <?php echo e(request('payment_status') === 'Belum Bayar' ? 'selected' : ''); ?> class="font-bold">⚪ Belum Bayar</option>
                            <option value="DP/Cicil" <?php echo e(request('payment_status') === 'DP/Cicil' ? 'selected' : ''); ?> class="font-bold">🟡 DP/Cicil</option>
                            <option value="Lunas" <?php echo e(request('payment_status') === 'Lunas' ? 'selected' : ''); ?> class="font-bold">🟢 Lunas</option>
                        </select>

                        
                        <div class="relative group/search flex-1 sm:flex-none">
                            <input type="text" 
                                   name="search" 
                                   value="<?php echo e(request('search')); ?>" 
                                   placeholder="Cari Nomor/Nama..." 
                                   class="w-full sm:w-64 md:w-80 pl-12 sm:pl-14 pr-6 py-3 sm:py-4 bg-gray-50 border-2 border-transparent rounded-xl sm:rounded-[2rem] focus:bg-white focus:border-[#1B8A68]/20 focus:ring-4 focus:ring-[#1B8A68]/5 text-sm font-black italic tracking-tight placeholder-gray-300 transition-all duration-500 shadow-inner">
                            <svg class="w-5 h-5 text-gray-300 absolute left-4 sm:left-6 top-1/2 -translate-y-1/2 group-focus-within/search:text-[#1B8A68] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-12">

            
            <div class="hidden lg:block bg-white rounded-[2rem] sm:rounded-[3rem] shadow-2xl border border-gray-100 overflow-hidden relative">
                <div class="absolute top-0 right-0 w-64 h-64 bg-[#1B8A68]/5 rounded-bl-[10rem] -mr-16 -mt-16 pointer-events-none"></div>
                
                <div class="overflow-x-auto relative z-10">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#F8FAFC] border-b border-gray-100">
                                <th class="px-8 py-8 text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic">No. Invoice</th>
                                <th class="px-8 py-8 text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic">Data Pelanggan</th>
                                <th class="px-8 py-8 text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic">Rincian</th>
                                <th class="px-8 py-8 text-[11px] font-black text-gray-400 text-center uppercase tracking-[0.3em] italic">Status SPK</th>
                                <th class="px-8 py-8 text-[11px] font-black text-gray-400 text-right uppercase tracking-[0.3em] italic">Total Tagihan</th>
                                <th class="px-8 py-8 text-[11px] font-black text-gray-400 text-center uppercase tracking-[0.3em] italic">Status</th>
                                <th class="px-8 py-8 text-[11px] font-black text-gray-400 text-center uppercase tracking-[0.3em] italic">Estimasi</th>
                                <th class="px-8 py-8 text-[11px] font-black text-gray-400 text-center uppercase tracking-[0.3em] italic">Nota</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <tr class="hover:bg-[#F8FAFC] transition-all duration-300 group">
                                    <td class="px-8 py-8">
                                        <div class="flex items-center gap-5">
                                            <div class="w-12 h-12 bg-gray-50 text-gray-400 rounded-2xl group-hover:bg-[#1B8A68] group-hover:text-white transition-all duration-500 flex items-center justify-center shadow-inner group-hover:shadow-lg group-hover:shadow-emerald-100 group-hover:-rotate-6">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <a href="<?php echo e(route('finance.invoices.show', $invoice->id)); ?>" class="text-lg font-black text-gray-900 leading-none italic uppercase tracking-tighter group-hover:text-[#1B8A68] transition-colors block pb-1"><?php echo e($invoice->invoice_number); ?></a>
                                                <div class="text-[10px] text-gray-400 font-black uppercase tracking-widest italic opacity-60"><?php echo e($invoice->created_at->format('d M Y • H:i')); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-8">
                                        <div class="font-black text-gray-900 italic uppercase tracking-tight leading-none mb-1.5"><?php echo e($invoice->customer?->name ?? 'Data Terhapus'); ?></div>
                                        <div class="text-[11px] text-gray-400 font-black tracking-widest italic opacity-80"><?php echo e($invoice->customer?->phone ?? '-'); ?></div>
                                    </td>
                                    <td class="px-8 py-8">
                                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-gray-50 border border-gray-100 rounded-lg shadow-inner mb-2 group-hover:bg-white group-hover:border-emerald-100 transition-colors">
                                            <span class="text-[10px] font-black text-[#1B8A68] italic"><?php echo e($invoice->workOrders->count()); ?> Pasang Sepatu</span>
                                        </div>
                                        <div class="flex flex-wrap gap-1.5">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $invoice->workOrders->unique('cs_code'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->cs_code): ?>
                                                    <span class="text-[9px] font-black px-1.5 py-0.5 bg-emerald-50 text-[#1B8A68] rounded uppercase tracking-widest border border-emerald-100 italic"><?php echo e($order->cs_code); ?></span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-8 py-8 text-center">
                                        <?php
                                            $spkStyle = match($invoice->spk_status) {
                                                'SELESAI' => 'bg-emerald-50 text-[#1B8A68] border-emerald-100',
                                                'BELUM SELESAI' => 'bg-amber-50 text-[#FFC232] border-amber-100',
                                                default => 'bg-gray-50 text-gray-400 border-gray-100'
                                            };
                                        ?>
                                        <div class="inline-flex items-center px-3 py-1.5 rounded-xl border <?php echo e($spkStyle); ?> shadow-sm">
                                            <span class="text-[10px] font-black uppercase tracking-[0.1em] italic"><?php echo e($invoice->spk_status); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-8 text-right">
                                        <div class="text-2xl font-black text-gray-900 italic tabular-nums tracking-tighter leading-none mb-1 group-hover:scale-105 transition-transform origin-right">Rp <?php echo e(number_format($invoice->total_amount + $invoice->shipping_cost, 0, ',', '.')); ?></div>
                                        
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($invoice->paid_amount > 0): ?>
                                            <div class="text-[9px] text-[#1B8A68] font-black uppercase tracking-widest italic opacity-80 mb-2">Terbayar: Rp <?php echo e(number_format($invoice->paid_amount, 0, ',', '.')); ?></div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        <div class="space-y-1 pt-2 border-t border-gray-50">
                                            <div class="flex justify-end items-center gap-2">
                                                <span class="text-[8px] font-black text-gray-400 italic uppercase tracking-tighter">DP (70%):</span>
                                                <span class="text-[10px] font-black text-emerald-600 italic tabular-nums">Rp <?php echo e(number_format($invoice->target_dp_amount + ($invoice->dp_unique_code ?? 0), 0, ',', '.')); ?> <span class="text-[8px] opacity-60">(<?php echo e($invoice->dp_unique_code ?? '-'); ?>)</span></span>
                                            </div>
                                            <div class="flex justify-end items-center gap-2">
                                                <span class="text-[8px] font-black text-gray-400 italic uppercase tracking-tighter">Penagihan Full:</span>
                                                <span class="text-[10px] font-black text-rose-600 italic tabular-nums">Rp <?php echo e(number_format($invoice->total_amount + $invoice->shipping_cost + ($invoice->final_unique_code ?? 0), 0, ',', '.')); ?> <span class="text-[8px] opacity-60">(<?php echo e($invoice->final_unique_code ?? '-'); ?>)</span></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-8 text-center">
                                        <?php
                                            $statusStyle = match($invoice->status) {
                                                'Lunas' => 'bg-emerald-50 text-[#1B8A68] border-emerald-100 shadow-emerald-50',
                                                'DP/Cicil' => 'bg-amber-50 text-[#FFC232] border-amber-100 shadow-amber-50',
                                                default => 'bg-gray-50 text-gray-400 border-gray-200'
                                            };
                                            $dotStyle = match($invoice->status) {
                                                'Lunas' => 'bg-[#1B8A68]',
                                                'DP/Cicil' => 'bg-[#FFC232]',
                                                default => 'bg-gray-300'
                                            };
                                        ?>
                                        <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-2xl border-2 <?php echo e($statusStyle); ?> shadow-sm">
                                            <span class="w-1.5 h-1.5 rounded-full <?php echo e($dotStyle); ?> <?php echo e($invoice->status === 'DP/Cicil' ? 'animate-pulse' : ''); ?>"></span>
                                            <span class="text-[11px] font-black uppercase tracking-[0.2em] italic"><?php echo e($invoice->status); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-8 text-center">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($invoice->estimasi_selesai): ?>
                                            <div class="inline-flex flex-col items-center">
                                                <span class="text-[10px] font-black text-[#1B8A68] uppercase tracking-widest italic leading-none mb-1">Target</span>
                                                <span class="text-xs font-black text-gray-900 italic tracking-tight uppercase"><?php echo e(\Carbon\Carbon::parse($invoice->estimasi_selesai)->format('d M Y')); ?></span>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-[10px] font-black text-gray-300 uppercase tracking-widest italic">Belum Set</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td class="px-8 py-8 text-center">
                                        <div class="flex items-center justify-center gap-3">
                                            <a href="<?php echo e(url('/api/invoice_share_grouped.php?token=' . urlencode($invoice->invoice_number) . '&type=' . ($invoice->status === 'Belum Bayar' ? 'BL' : 'L'))); ?>" 
                                               target="_blank" 
                                               class="w-12 h-12 rounded-full bg-white border-2 border-gray-100 text-gray-400 hover:text-[#1B8A68] hover:border-[#1B8A68]/30 hover:shadow-lg hover:shadow-emerald-50 transition-all active:scale-90 flex items-center justify-center group/btn" 
                                               title="Cetak Nota Gabungan">
                                                <svg class="w-5 h-5 group-hover/btn:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <tr>
                                    <td colspan="8" class="px-10 py-40 text-center">
                                        <div class="w-32 h-32 bg-[#F8FAFC] rounded-[2.5rem] flex items-center justify-center text-6xl mb-8 shadow-inner border border-gray-100 mx-auto filter grayscale opacity-20">📋</div>
                                        <h3 class="text-3xl font-black text-gray-900 mb-2 uppercase tracking-tighter italic">Belum Ada Data</h3>
                                        <p class="text-gray-400 text-[11px] font-black uppercase tracking-[0.3em] italic opacity-60">Tidak ada rincian penagihan yang terdata</p>
                                    </td>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($invoices) && $invoices->hasPages()): ?>
                <div class="px-10 py-10 border-t border-gray-50 bg-[#F8FAFC]/50 flex justify-center">
                    <?php echo e($invoices->links()); ?>

                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div class="lg:hidden space-y-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <a href="<?php echo e(route('finance.invoices.show', $invoice->id)); ?>" class="block bg-white rounded-2xl shadow-lg border border-gray-100 p-5 hover:shadow-xl transition-all duration-300 active:scale-[0.98] group">
                        
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gray-50 text-gray-400 rounded-xl group-hover:bg-[#1B8A68] group-hover:text-white transition-all flex items-center justify-center shadow-inner">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-base font-black text-gray-900 italic uppercase tracking-tighter leading-none group-hover:text-[#1B8A68] transition-colors"><?php echo e($invoice->invoice_number); ?></div>
                                    <div class="text-[10px] text-gray-400 font-black uppercase tracking-widest italic opacity-60 mt-0.5"><?php echo e($invoice->created_at->format('d M Y • H:i')); ?></div>
                                </div>
                            </div>
                            <?php
                                $statusStyle = match($invoice->status) {
                                    'Lunas' => 'bg-emerald-50 text-[#1B8A68] border-emerald-100',
                                    'DP/Cicil' => 'bg-amber-50 text-[#FFC232] border-amber-100',
                                    default => 'bg-gray-50 text-gray-400 border-gray-200'
                                };
                                $dotStyle = match($invoice->status) {
                                    'Lunas' => 'bg-[#1B8A68]',
                                    'DP/Cicil' => 'bg-[#FFC232]',
                                    default => 'bg-gray-300'
                                };
                            ?>
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl border <?php echo e($statusStyle); ?> shadow-sm">
                                <span class="w-1.5 h-1.5 rounded-full <?php echo e($dotStyle); ?> <?php echo e($invoice->status === 'DP/Cicil' ? 'animate-pulse' : ''); ?>"></span>
                                <span class="text-[10px] font-black uppercase tracking-[0.1em] italic"><?php echo e($invoice->status); ?></span>
                            </div>
                        </div>

                        
                        <div class="flex items-center justify-between mb-3 pb-3 border-b border-gray-50">
                            <div>
                                <div class="font-black text-gray-900 italic uppercase tracking-tight text-sm leading-none mb-1"><?php echo e($invoice->customer?->name ?? 'Data Terhapus'); ?></div>
                                <div class="text-[10px] text-gray-400 font-black tracking-widest italic opacity-80"><?php echo e($invoice->customer?->phone ?? '-'); ?></div>
                            </div>
                            <div class="flex items-center gap-2">
                                <?php
                                    $spkStyle = match($invoice->spk_status) {
                                        'SELESAI' => 'bg-emerald-50 text-[#1B8A68] border-emerald-100',
                                        'BELUM SELESAI' => 'bg-amber-50 text-[#FFC232] border-amber-100',
                                        default => 'bg-gray-50 text-gray-400 border-gray-100'
                                    };
                                ?>
                                <div class="inline-flex items-center px-2 py-1 rounded-lg border <?php echo e($spkStyle); ?>">
                                    <span class="text-[9px] font-black uppercase tracking-[0.05em] italic"><?php echo e($invoice->spk_status); ?></span>
                                </div>
                            </div>
                        </div>

                        
                        <div class="flex items-end justify-between">
                            <div class="flex-1">
                                <div class="text-xl font-black text-gray-900 italic tabular-nums tracking-tighter leading-none mb-1">Rp <?php echo e(number_format($invoice->total_amount + $invoice->shipping_cost, 0, ',', '.')); ?></div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($invoice->paid_amount > 0): ?>
                                    <div class="text-[9px] text-[#1B8A68] font-black uppercase tracking-widest italic opacity-80 mb-2">Terbayar: Rp <?php echo e(number_format($invoice->paid_amount, 0, ',', '.')); ?></div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <div class="space-y-0.5 mt-2 pt-2 border-t border-gray-50">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[7px] font-black text-gray-400 italic uppercase">DP (70%):</span>
                                        <span class="text-[9px] font-black text-emerald-600 italic">Rp <?php echo e(number_format($invoice->target_dp_amount + ($invoice->dp_unique_code ?? 0), 0, ',', '.')); ?> (<?php echo e($invoice->dp_unique_code ?? '-'); ?>)</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[7px] font-black text-gray-400 italic uppercase">Full:</span>
                                        <span class="text-[9px] font-black text-rose-600 italic">Rp <?php echo e(number_format($invoice->total_amount + $invoice->shipping_cost + ($invoice->final_unique_code ?? 0), 0, ',', '.')); ?> (<?php echo e($invoice->final_unique_code ?? '-'); ?>)</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-50 border border-gray-100 rounded-lg">
                                    <span class="text-[9px] font-black text-[#1B8A68] italic"><?php echo e($invoice->workOrders->count()); ?> SPK</span>
                                </div>
                                <div onclick="event.preventDefault(); event.stopPropagation(); window.open('<?php echo e(url('/api/invoice_share_grouped.php?token=' . urlencode($invoice->invoice_number) . '&type=' . ($invoice->status === 'Belum Bayar' ? 'BL' : 'L'))); ?>', '_blank')" 
                                     class="w-9 h-9 rounded-full bg-white border-2 border-gray-100 text-gray-400 hover:text-[#1B8A68] hover:border-[#1B8A68]/30 transition-all active:scale-90 flex items-center justify-center cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="bg-white rounded-2xl p-12 text-center border border-gray-100 shadow-lg">
                        <div class="w-20 h-20 bg-[#F8FAFC] rounded-2xl flex items-center justify-center text-4xl mb-6 shadow-inner border border-gray-100 mx-auto filter grayscale opacity-20">📋</div>
                        <h3 class="text-xl font-black text-gray-900 mb-2 uppercase tracking-tighter italic">Belum Ada Data</h3>
                        <p class="text-gray-400 text-[10px] font-black uppercase tracking-[0.2em] italic opacity-60">Tidak ada rincian penagihan yang terdata</p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($invoices) && $invoices->hasPages()): ?>
                <div class="py-6 flex justify-center">
                    <?php echo e($invoices->links()); ?>

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
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\finance\invoices.blade.php ENDPATH**/ ?>