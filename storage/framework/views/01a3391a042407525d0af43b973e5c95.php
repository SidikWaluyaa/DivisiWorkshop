<div class="min-h-screen bg-white py-8">
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
        
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <nav class="flex text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-2">
                    <span class="hover:text-[#22AF85] transition-colors cursor-default">INVENTARIS</span>
                    <span class="mx-2">/</span>
                    <span class="text-[#22AF85]">VALIDASI MATERIAL</span>
                </nav>
                <div class="flex items-center gap-4">
                    <a href="<?php echo e(route('sortir.index')); ?>" wire:navigate class="w-10 h-10 bg-gray-50 border border-gray-100 rounded-xl flex items-center justify-center text-gray-400 hover:text-[#22AF85] hover:border-[#22AF85] transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </a>
                    <h1 class="text-3xl font-black text-black tracking-tight">Validasi Material</h1>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="<?php echo e(route('materials.selection.create', $order->id)); ?>" 
                    class="px-6 py-3 bg-white border-2 border-gray-100 text-gray-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-50 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Request Barang (Lanjutan)
                </a>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session()->has('success') || session()->has('error')): ?>
            <div class="mb-8">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session()->has('success')): ?>
                    <div class="p-4 bg-gray-50 border border-gray-100 rounded-xl flex items-center gap-3 text-[#22AF85] animate-fade-in">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span class="text-sm font-bold"><?php echo e(session('success')); ?></span>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session()->has('error')): ?>
                    <div class="p-4 bg-gray-50 border border-gray-100 rounded-xl flex items-center gap-3 text-red-600 animate-fade-in">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span class="text-sm font-bold"><?php echo e(session('error')); ?></span>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            
            <div class="lg:col-span-4 space-y-6">
                
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/20">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-[#22AF85]">DATA SPK & PELANGGAN</h3>
                        <span class="px-3 py-1 bg-black text-white text-[9px] font-black rounded-lg uppercase tracking-widest"><?php echo e($order->spk_number); ?></span>
                    </div>
                    
                    <div class="p-8 space-y-8">
                        
                        <div class="flex items-center justify-between border-b border-gray-50 pb-6">
                            <div>
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1.5">PRIORITAS</p>
                                <?php
                                    $prioClass = match(strtolower($order->priority ?? 'normal')) {
                                        'urgent' => 'bg-red-500 text-white shadow-red-100',
                                        'high' => 'bg-orange-500 text-white shadow-orange-100',
                                        default => 'bg-blue-500 text-white shadow-blue-100',
                                    };
                                ?>
                                <span class="px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest shadow-lg <?php echo e($prioClass); ?>">
                                    <?php echo e($order->priority ?? 'NORMAL'); ?>

                                </span>
                            </div>
                            <div class="text-right">
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1.5">TANGGAL MASUK</p>
                                <p class="text-xs font-black text-gray-800"><?php echo e($order->created_at->format('d M Y')); ?></p>
                            </div>
                        </div>

                        
                        <div class="flex items-start gap-5">
                            <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center border border-gray-100 shrink-0">
                                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-black text-black tracking-tight mb-1"><?php echo e($order->customer?->name ?? $order->customer_name ?? 'Guest'); ?></h4>
                                <div class="flex flex-col gap-1">
                                    <p class="text-xs font-bold text-[#22AF85] font-mono leading-none"><?php echo e($order->customer?->phone ?? $order->customer_phone ?? 'N/A'); ?></p>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->customer?->email || $order->customer_email): ?>
                                        <p class="text-[10px] font-bold text-gray-400 truncate"><?php echo e($order->customer?->email ?? $order->customer_email); ?></p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        </div>

                        
                        <?php
                            $latestCxIssue = $order->cxIssues->where('status', 'RESOLVED')->sortByDesc('resolved_at')->first();
                        ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($latestCxIssue && $latestCxIssue->resolution_notes): ?>
                            <div class="p-6 bg-amber-50/50 border border-amber-100 rounded-3xl space-y-3 relative overflow-hidden group transition-all hover:bg-amber-50">
                                <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                                    <svg class="w-12 h-12 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                                </div>
                                <div class="relative z-10">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        <h5 class="text-[9px] font-black text-amber-600 uppercase tracking-widest">INFORMASI KHUSUS DARI CX</h5>
                                    </div>
                                    <p class="text-[11px] font-black text-gray-800 leading-relaxed italic">"<?php echo e($latestCxIssue->resolution_notes); ?>"</p>
                                    <div class="mt-4 flex items-center gap-2 pt-3 border-t border-amber-100/50">
                                        <div class="w-5 h-5 rounded-full bg-amber-100 flex items-center justify-center text-[8px] font-black text-amber-600">CX</div>
                                        <span class="text-[8px] font-bold text-amber-500 uppercase tracking-tighter">Dijawab oleh <?php echo e($latestCxIssue->resolver?->name ?? 'CX Team'); ?> • <?php echo e($latestCxIssue->resolved_at?->diffForHumans()); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        
                        <div class="p-6 bg-gray-50/50 rounded-2xl border border-gray-100 space-y-4">
                            <div>
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">DETAIL BARANG PRODUKSI</span>
                                <h5 class="text-sm font-black text-black tracking-tight leading-tight"><?php echo e($order->brand ?? $order->shoe_brand); ?> - <?php echo e($order->type ?? $order->shoe_type); ?></h5>
                                <div class="flex items-center gap-3 mt-1.5">
                                    <span class="text-[10px] font-bold text-gray-500 uppercase"><?php echo e($order->color ?? $order->shoe_color); ?></span>
                                    <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                    <span class="text-[10px] font-bold text-gray-500 uppercase">Size: <?php echo e($order->size ?? $order->shoe_size ?? '-'); ?></span>
                                </div>
                            </div>

                            
                            <div class="pt-4 border-t border-gray-100 space-y-3">
                                <?php
                                    $accs = [
                                        'Tali' => $order->accessories_tali,
                                        'Insole' => $order->accessories_insole,
                                        'Box' => $order->accessories_box
                                    ];
                                ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $accs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <?php
                                        $val = strtoupper($val ?? '');
                                        $isN = in_array($val, ['N', 'NEMPEL']);
                                        $isS = in_array($val, ['S', 'SIMPAN']);
                                        $isT = !$val || in_array($val, ['T', 'TIDAK ADA', 'NONE', '-', '0']);
                                    ?>
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-tight"><?php echo e($label); ?></span>
                                        <div class="flex gap-1">
                                            <span class="w-7 h-7 flex items-center justify-center rounded-lg text-[9px] font-black transition-all <?php echo e($isT ? 'bg-red-500 text-white shadow-lg shadow-red-100' : 'bg-gray-100 text-gray-300 cursor-default'); ?>">T</span>
                                            <span class="w-7 h-7 flex items-center justify-center rounded-lg text-[9px] font-black transition-all <?php echo e($isN ? 'bg-[#22AF85] text-white shadow-lg shadow-emerald-100' : 'bg-gray-100 text-gray-300 cursor-default'); ?>">N</span>
                                            <span class="w-7 h-7 flex items-center justify-center rounded-lg text-[9px] font-black transition-all <?php echo e($isS ? 'bg-[#FFC232] text-white shadow-lg shadow-yellow-100' : 'bg-gray-100 text-gray-300 cursor-default'); ?>">S</span>
                                        </div>
                                    </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        </div>

                        
                        <div>
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-3 block">LAYANAN YANG DIAMBIL</span>
                            <div class="flex flex-wrap gap-2">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $order->services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <span class="px-3 py-1.5 bg-white border border-gray-100 rounded-xl text-[10px] font-black text-gray-700 shadow-sm flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#22AF85]"></span>
                                        <?php echo e($service->name); ?>

                                        <span class="opacity-30">/</span>
                                        <span class="text-[8px] opacity-60 uppercase"><?php echo e($service->category); ?></span>
                                    </span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    <p class="text-[10px] font-bold text-gray-400 italic">Tidak ada layanan spesifik.</p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>

                        
                        <div class="pt-6 border-t border-gray-50">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">ALAMAT PENGIRIMAN</p>
                            <p class="text-xs font-bold text-gray-600 leading-relaxed"><?php echo e($order->customer?->address ?? $order->customer_address ?? 'Alamat tidak tersedia.'); ?></p>
                        </div>
                    </div>
                </div>

                
                <?php $availability = $this->stockAvailability; ?>
                <div class="bg-black rounded-3xl p-8 shadow-xl relative overflow-hidden group border border-gray-800">
                    <div class="relative z-10">
                        <p class="text-[10px] font-black text-[#22AF85] uppercase tracking-widest mb-1">KETERSEDIAAN STOK</p>
                        <div class="flex items-baseline gap-2 mb-4">
                            <h3 class="text-4xl font-black text-white tracking-tighter"><?php echo e($availability); ?>%</h3>
                        </div>
                        <div class="h-2.5 w-full bg-gray-800 rounded-full overflow-hidden mb-4">
                            <div class="h-full bg-[#22AF85] rounded-full transition-all duration-1000 shadow-[0_0_10px_rgba(34,175,133,0.3)]" style="width: <?php echo e($availability); ?>%"></div>
                        </div>
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider leading-relaxed mb-6">Rentang optimal untuk beban produksi saat ini.</p>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($availability < 100): ?>
                            <button wire:click="requestMaterial" wire:loading.attr="disabled"
                                    class="w-full bg-[#22AF85] hover:bg-[#1a8e6b] text-white py-4 px-6 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] transition-all flex items-center justify-center gap-2 shadow-lg shadow-emerald-900/40">
                                <svg wire:loading.remove wire:target="requestMaterial" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                                <svg wire:loading wire:target="requestMaterial" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                🚀 Ajukan ke Purchasing
                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>

            
            <div class="lg:col-span-8 space-y-6">
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex flex-col min-h-[500px]">
                    <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/20">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-black">DAFTAR MATERIAL SEPATU INI</h3>
                        <span class="text-[10px] font-black text-gray-400 uppercase">Total: <?php echo e(count($selectedMaterials)); ?> Material</span>
                    </div>

                    <div class="flex-1 overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest text-left">
                                    <th class="px-8 py-6">NAMA MATERIAL</th>
                                    <th class="px-6 py-6">TIPE / BATCH</th>
                                    <th class="px-6 py-6 text-center">KEBUTUHAN</th>
                                    <th class="px-6 py-6 text-center">STATUS</th>
                                    <th class="px-8 py-6 text-right">AKSI</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $selectedMaterials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <tr class="group hover:bg-gray-50/30 transition-colors">
                                        <td class="px-8 py-6">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center border border-gray-100 group-hover:border-[#22AF85]/30 transition-colors">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(str_contains(strtolower($data['name']), 'sol')): ?>
                                                        <svg class="w-5 h-5 text-[#22AF85]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 3v18m9-9H3" stroke-width="2" stroke-linecap="round"/></svg>
                                                    <?php else: ?>
                                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-black text-gray-700 leading-tight"><?php echo e($data['name']); ?></span>
                                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter"><?php echo e($data['type'] ?? 'Material'); ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-6 font-mono text-[11px] font-bold text-gray-400">
                                            #<?php echo e(strtoupper(substr(md5($data['name']), 0, 4))); ?>

                                        </td>
                                        <td class="px-6 py-6 text-center">
                                            <div class="flex items-center justify-center gap-3">
                                                <button wire:click="updateQuantity(<?php echo e($id); ?>, <?php echo e($data['quantity'] - 1); ?>)" class="p-1 hover:text-black transition-colors text-gray-300">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"></path></svg>
                                                </button>
                                                <span class="text-sm font-black text-gray-700 w-8"><?php echo e($data['quantity']); ?></span>
                                                <button wire:click="updateQuantity(<?php echo e($id); ?>, <?php echo e($data['quantity'] + 1); ?>)" class="p-1 hover:text-[#22AF85] transition-colors text-gray-300">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="px-6 py-6 text-center">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($data['status'] ?? '') == 'ALLOCATED'): ?>
                                                <span class="px-3 py-1 bg-gray-50 text-[#22AF85] text-[9px] font-black uppercase rounded-full tracking-wider border border-[#22AF85]/20">SIAP</span>
                                            <?php else: ?>
                                                <span class="px-3 py-1 bg-gray-50 text-red-500 text-[9px] font-black uppercase rounded-full tracking-wider border border-red-100">DIREQUEST</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td class="px-8 py-6 text-right">
                                            <button wire:click="removeMaterial(<?php echo e($id); ?>)" class="text-gray-300 hover:text-red-600 transition-all p-2 hover:bg-gray-50 rounded-xl">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    <tr>
                                        <td colspan="5" class="py-24 text-center">
                                            <div class="flex flex-col items-center opacity-20">
                                                <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                                <p class="text-xs font-black uppercase tracking-[0.2em] text-gray-500">Belum ada material dipilih</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="px-8 py-8 border-t border-gray-50 bg-gray-50/20 flex items-center justify-between">
                        <div class="flex flex-col">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">ESTIMASI BIAYA</span>
                            <span class="text-xl font-black text-black tracking-tight">Rp <?php echo e(number_format(collect($selectedMaterials)->sum(fn($m) => $m['price'] * $m['quantity']))); ?></span>
                        </div>
                        <button wire:click="saveMaterials" 
                            class="px-10 py-4 bg-black text-white rounded-2xl text-[11px] font-black uppercase tracking-[0.1em] shadow-xl hover:bg-gray-900 transition-all transform hover:scale-[1.02] active:scale-[0.98]">
                            SIMPAN PERUBAHAN
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-6 p-8 bg-gray-50 rounded-[2rem] border border-gray-100">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-[#22AF85] shadow-sm border border-gray-50">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-black tracking-tight leading-none mb-1">Tambah Layanan (Upsell)?</h4>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Jika customer ingin menambah layanan workshop</p>
                        </div>
                    </div>
                    <button wire:click="$set('showUpsellModal', true)" class="px-6 py-3 bg-[#FFC232] text-black rounded-xl text-[10px] font-black uppercase tracking-widest hover:scale-[1.02] transition-all shadow-lg shadow-yellow-100">
                        TAMBAH LAYANAN
                    </button>
                </div>
            </div>
        </div>

        
        <div class="mt-12">
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-10 py-10 border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-gray-50/20">
                    <div class="flex items-start gap-6">
                        <div class="w-16 h-16 bg-black rounded-3xl flex items-center justify-center text-white shadow-xl shadow-gray-100">
                            <svg class="w-8 h-8 text-[#22AF85]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-3 mb-1">
                                <h3 class="text-2xl font-black text-black tracking-tight leading-none">Gudang Material</h3>
                                <a href="<?php echo e(route('materials.selection.create', $order->id)); ?>" class="text-[9px] font-black text-blue-600 hover:text-blue-800 transition-colors uppercase underline tracking-tighter">Advanced Request</a>
                            </div>
                            <p class="text-[10px] font-black text-[#22AF85] uppercase tracking-[0.2em]">AKSES INVENTARIS GUDANG AKTIF</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-4">
                        <div class="relative w-full md:w-96">
                            <input type="text" wire:model.live="searchMaterial" placeholder="Cari material dari stok..." 
                                class="w-full pl-12 pr-6 py-4 text-xs border-gray-100 rounded-2xl focus:ring-4 focus:ring-yellow-500/10 focus:border-[#FFC232] font-bold bg-gray-50 shadow-inner transition-all">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                        </div>
                        <button class="px-10 py-4 bg-[#FFC232] text-black rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-xl shadow-yellow-100 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            AMBIL
                        </button>
                    </div>
                </div>

                <div class="p-10">
                    
                    <div class="flex flex-wrap gap-4 mb-10">
                        <button wire:click="$set('activeTab', 'sol')" 
                            class="px-10 py-8 rounded-[2rem] border-2 transition-all flex flex-col min-w-[150px] shadow-sm <?php echo e($activeTab == 'sol' ? 'border-[#FFC232] bg-[#FFC232]/5 ring-8 ring-yellow-50' : 'border-gray-50 bg-gray-50/30 grayscale hover:grayscale-0'); ?>">
                            <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1.5 leading-none">PRODUCTION CATEGORY</span>
                            <span class="text-sm font-black <?php echo e($activeTab == 'sol' ? 'text-black' : 'text-gray-600'); ?>">Karet / Sol</span>
                        </button>
                        <button wire:click="$set('activeTab', 'upper')" 
                            class="px-10 py-8 rounded-[2rem] border-2 transition-all flex flex-col min-w-[150px] shadow-sm <?php echo e($activeTab == 'upper' ? 'border-[#FFC232] bg-[#FFC232]/5 ring-8 ring-yellow-50' : 'border-gray-50 bg-gray-50/30 grayscale hover:grayscale-0'); ?>">
                            <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1.5 leading-none">PRODUCTION CATEGORY</span>
                            <span class="text-sm font-black <?php echo e($activeTab == 'upper' ? 'text-black' : 'text-gray-600'); ?>">Kulit / Upper</span>
                        </button>
                        <button wire:click="$set('activeTab', 'other')" 
                            class="px-10 py-8 rounded-[2rem] border-2 transition-all flex flex-col min-w-[150px] shadow-sm <?php echo e($activeTab == 'other' ? 'border-[#FFC232] bg-[#FFC232]/5 ring-8 ring-yellow-50' : 'border-gray-50 bg-gray-50/30 grayscale hover:grayscale-0'); ?>">
                            <span class="text-[8px] font-black text-[#22AF85] uppercase tracking-widest mb-1.5 leading-none">SHOPPING / REQUEST</span>
                            <span class="text-sm font-black <?php echo e($activeTab == 'other' ? 'text-black' : 'text-gray-600'); ?>">Belanja / Request</span>
                        </button>
                    </div>

                    
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6 max-h-[500px] overflow-y-auto pr-4 custom-scrollbar">
                        <?php
                            $currentList = match($activeTab) {
                                'sol' => $solMaterials,
                                'upper' => $upperMaterials,
                                'other' => $otherMaterials,
                            };
                        ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $currentList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <div wire:click="addMaterial(<?php echo e($material->id); ?>)" class="p-6 border border-gray-100 rounded-3xl hover:border-[#FFC232] hover:shadow-xl hover:shadow-yellow-50 cursor-pointer transition-all group relative overflow-hidden bg-white">
                                <div class="absolute top-0 right-0 p-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <div class="w-8 h-8 bg-[#FFC232] text-black rounded-2xl flex items-center justify-center shadow-lg transform rotate-12">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                                    </div>
                                </div>
                                <h5 class="text-xs font-black text-gray-800 group-hover:text-black transition-colors mb-3 pr-2"><?php echo e($material->name); ?></h5>
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter"><?php echo e($material->size ?? 'Standar'); ?></span>
                                    <span class="text-[10px] font-black text-[#22AF85]">STOK: <?php echo e($material->stock); ?></span>
                                </div>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </div>

                <div class="px-10 py-8 bg-gray-50 border-t border-gray-100 flex flex-wrap items-center justify-between gap-6 pb-12">
                    <div class="flex items-center gap-10">
                        <div>
                            <span class="text-[8px] font-black text-gray-400 uppercase tracking-[0.25em] mb-1.5 block">KONTROL BATCH AKTIF</span>
                            <span class="text-xs font-black text-black">#SORTIR-<?php echo e(substr(md5($order->id), 0, 8)); ?></span>
                        </div>
                        <div class="h-12 w-px bg-gray-200"></div>
                        <div>
                            <span class="text-[8px] font-black text-gray-400 uppercase tracking-[0.25em] mb-1.5 block">ITEM TERKONEKSI</span>
                            <span class="text-xs font-black text-black"><?php echo e(count($selectedMaterials)); ?> / 40 Unit Total</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-6">
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-[0.25em]">STATUS SISTEM</span>
                        <div class="flex items-center gap-2">
                             <span class="w-2.5 h-2.5 rounded-full bg-[#22AF85] animate-pulse"></span>
                             <span class="text-[10px] font-black text-[#22AF85] uppercase tracking-widest">Terhubung & Online</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="mt-16 bg-black rounded-[3rem] p-12 border border-gray-800 shadow-2xl relative overflow-hidden">
            <div class="relative z-10">
                
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(collect($selectedMaterials)->contains(fn($m) => ($m['status'] ?? '') == 'REQUESTED')): ?>
                    <div class="mb-10 p-6 bg-red-500/10 border border-red-500/20 rounded-3xl flex items-center gap-5 text-red-500 animate-pulse">
                        <div class="w-12 h-12 bg-red-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-red-500/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-black uppercase tracking-widest leading-none mb-1">Validasi Tertunda</h4>
                            <p class="text-[10px] font-bold text-red-500/80 uppercase tracking-widest">Ada material dengan status DIREQUEST (Stok Kurang). Mohon lakukan pembelanjaan / restock terlebih dahulu.</p>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-10">
                    <div class="flex items-start gap-6">
                        <div class="w-16 h-16 bg-gray-900 text-[#FFC232] rounded-[1.5rem] flex items-center justify-center shadow-lg border border-gray-800">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-white tracking-tight leading-none mb-1">Penugasan Operasional Akhir</h3>
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em]">TENTUKAN PERSONEL YANG BERTANGGUNG JAWAB</p>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row gap-6 w-full md:w-auto">
                        <div class="relative w-full md:w-64">
                            <label class="block text-[8px] font-black text-gray-500 uppercase tracking-widest mb-1.5 ml-2">PIC MAT SOL</label>
                            <select wire:model="pic_sortir_sol_id" class="w-full bg-gray-900 border-gray-800 rounded-2xl px-5 py-4 text-xs font-black text-white focus:ring-4 focus:ring-yellow-500/10 focus:border-[#FFC232] transition-all appearance-none cursor-pointer">
                                <option value="">-- Pilih --</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $techSol; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tech): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <option value="<?php echo e($tech->id); ?>"><?php echo e($tech->name); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </select>
                        </div>
                        <div class="relative w-full md:w-64">
                            <label class="block text-[8px] font-black text-gray-500 uppercase tracking-widest mb-1.5 ml-2">PIC MAT UPPER</label>
                            <select wire:model="pic_sortir_upper_id" class="w-full bg-gray-900 border-gray-800 rounded-2xl px-5 py-4 text-xs font-black text-white focus:ring-4 focus:ring-yellow-500/10 focus:border-[#FFC232] transition-all appearance-none cursor-pointer">
                                <option value="">-- Pilih --</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $techUpper; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tech): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <option value="<?php echo e($tech->id); ?>"><?php echo e($tech->name); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-12 pt-10 border-t border-gray-800 flex flex-col md:flex-row items-center gap-6">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array(auth()->user()->role, ['admin', 'owner', 'production_manager'])): ?>
                        <button wire:click="bypassSortir" wire:confirm="Bypass sortir akan melewati semua validasi material. Lanjutkan?"
                            class="w-full md:w-auto px-10 py-5 border-2 border-gray-800 text-gray-500 hover:border-gray-600 hover:text-white rounded-2xl transition-all text-[11px] font-black uppercase tracking-[0.2em]">
                            BYPASS VALIDASI
                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <button wire:click="completeSortir" 
                        class="flex-1 w-full px-12 py-5 bg-[#FFC232] text-black rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] shadow-2xl shadow-yellow-500/10 hover:scale-[1.01] transition-all">
                        SELESAIKAN & MULAI FASE PRODUKSI
                    </button>
                </div>
            </div>
        </div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showUpsellModal): ?>
    <div class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-black/90 backdrop-blur-md animate-fade-in">
        <div class="bg-white rounded-[3rem] shadow-2xl w-full max-w-2xl overflow-hidden animate-bounce-in border border-gray-100">
            <div class="px-12 py-10 bg-black text-white flex justify-between items-center relative">
                <div class="flex items-center gap-4">
                     <span class="w-2 h-10 bg-[#FFC232] rounded-full"></span>
                     <h3 class="text-xl font-black uppercase tracking-[0.1em]">Tambah Layanan</h3>
                </div>
                <button wire:click="$set('showUpsellModal', false)" class="text-white/40 hover:text-white transition-colors p-2 hover:bg-gray-900 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-12 space-y-10">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-[0.15em] text-gray-400 mb-4 ml-2">PILIH LAYANAN WORKSHOP</label>
                    <select wire:model.live="upsellServiceId" class="w-full bg-gray-50 border-gray-100 rounded-2xl px-8 py-5 font-black text-black focus:ring-4 focus:ring-yellow-500/10 focus:border-[#FFC232] appearance-none">
                        <option value="">-- Pilih Layanan --</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $svc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <option value="<?php echo e($svc->id); ?>"><?php echo e($svc->name); ?> (Rp <?php echo e(number_format($svc->price)); ?>)</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <option value="custom">-- CUSTOM / MANUAL --</option>
                    </select>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($upsellServiceId === 'custom'): ?>
                <div class="grid grid-cols-2 gap-8">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-4 ml-2">NAMA LAYANAN</label>
                        <input type="text" wire:model="upsellCustomName" class="w-full bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 font-black text-black focus:ring-4 focus:ring-teal-500/10 focus:border-[#22AF85]">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-4 ml-2">HARGA (RP)</label>
                        <input type="number" wire:model="upsellCustomPrice" class="w-full bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 font-black text-black focus:ring-4 focus:ring-teal-500/10 focus:border-[#22AF85]">
                    </div>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="flex gap-4 pt-6">
                    <button wire:click="$set('showUpsellModal', false)" class="flex-1 py-5 bg-gray-50 text-gray-400 rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-gray-200 transition-colors">BATALKAN</button>
                    <button wire:click="processUpsell" class="flex-1 py-5 bg-[#FFC232] text-black rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-xl shadow-yellow-100 hover:scale-[1.02] active:scale-[0.98] transition-all">SIMPAN TAHAPAN</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\livewire\sortir\detail.blade.php ENDPATH**/ ?>