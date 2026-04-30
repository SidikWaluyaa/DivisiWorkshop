<div>
     <?php $__env->slot('header', null, []); ?> 
        <div class="flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-5">
                <div class="relative group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-red-600 to-orange-600 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                    <div class="relative p-3 bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 shadow-2xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                
                <div class="flex flex-col">
                    <h2 class="font-black text-2xl leading-tight tracking-tight text-white drop-shadow-sm">
                        <?php echo e(__('Informasi Produksi Terlambat')); ?>

                    </h2>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-white/20 text-white/90 uppercase tracking-widest backdrop-blur-sm border border-white/10">Analisis Real-time (Livewire)</span>
                        <span class="text-[10px] font-medium text-white/60 italic">Sinkronisasi JSON Aktif</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex flex-col items-end mr-2 hidden sm:flex">
                    <span class="text-[10px] font-black text-white/40 uppercase tracking-[0.2em]">Status Sistem</span>
                    <span class="text-xs font-bold text-emerald-400">Stabil & Aman</span>
                </div>
                <div class="bg-white/10 backdrop-blur-xl px-5 py-2.5 rounded-2xl border border-white/20 text-white flex items-center gap-3 shadow-xl transform transition-all hover:scale-105">
                    <div class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500 shadow-[0_0_10px_rgba(239,68,68,0.5)]"></span>
                    </div>
                    <span class="text-sm font-black uppercase tracking-widest text-white/90">Monitoring Langsung</span>
                </div>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-10 bg-[#F9FAFB] min-h-screen font-sans selection:bg-gray-900 selection:text-white" x-data="lateInfoApp()">
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
            
            
            <div class="mb-10 relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-20 -mr-20 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl"></div>
                
                <div class="relative bg-white/70 backdrop-blur-xl border border-white shadow-[0_8px_30px_rgb(0,0,0,0.04)] rounded-[2.5rem] p-6 flex flex-col md:flex-row items-center justify-between gap-6 transition-all hover:shadow-[0_20px_50px_rgba(0,0,0,0.08)]">
                    <div class="flex items-center gap-6">
                        <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-3xl flex items-center justify-center shadow-lg shadow-blue-200 rotate-3">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-gray-900 text-lg tracking-tight uppercase">Integrasi Spreadsheet Aktif</h4>
                            <p class="text-gray-500 text-sm font-medium mt-1 leading-relaxed">
                                Sinkronisasi data otomatis dengan Google Sheets menggunakan token enkripsi global.
                            </p>
                        </div>
                    </div>
                    
                    <div class="w-full md:w-auto flex flex-col items-end gap-2 group">
                        <div class="flex items-center bg-gray-50 rounded-2xl border border-gray-100 p-1 pl-4 w-full md:w-auto focus-within:ring-2 focus-within:ring-blue-500/20 transition-all shadow-inner">
                            <span class="text-[10px] font-black text-gray-400 mr-4 font-mono select-none">API ENDPOINT</span>
                            <input type="text" readonly value="<?php echo e(url('/api/sync_late_production.php?token=' . (config('app.sync_token') ?? 'SECRET_TOKEN_12345'))); ?>" 
                                   class="bg-transparent border-none text-[11px] font-bold text-blue-700 w-full md:w-[400px] focus:ring-0 truncate" id="apiKeyInput">
                            <button @click="copyApiKey()" class="ml-2 bg-white text-gray-700 hover:bg-gray-900 hover:text-white px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm active:scale-95 border border-gray-200">
                                <span x-text="copied ? 'Disalin!' : 'Salin Link'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="mb-8 flex flex-col xl:flex-row justify-between items-end gap-6">
                <div class="w-full xl:w-auto">
                    <div class="flex flex-wrap items-center gap-3 p-1.5 bg-gray-200/50 backdrop-blur-sm rounded-[1.5rem] border border-gray-200/50 w-fit">
                        <button wire:click="setStatus('')" 
                           class="px-6 py-2.5 rounded-2xl text-[11px] font-black tracking-widest transition-all <?php echo e(!$status ? 'bg-white text-gray-900 shadow-xl scale-105 z-10' : 'text-gray-500 hover:text-gray-900'); ?>">
                            SEMUA DATA
                        </button>
                        <button wire:click="setStatus('LATE')" 
                           class="px-6 py-2.5 rounded-2xl text-[11px] font-black tracking-widest transition-all flex items-center gap-2 <?php echo e($status == 'LATE' ? 'bg-red-500 text-white shadow-lg shadow-red-200 scale-105 z-10' : 'text-gray-500 hover:text-red-500'); ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?php echo e($status == 'LATE' ? 'bg-white' : 'bg-red-500'); ?>"></span>
                            TERLAMBAT
                        </button>
                        <button wire:click="setStatus('WARNING')" 
                           class="px-6 py-2.5 rounded-2xl text-[11px] font-black tracking-widest transition-all flex items-center gap-2 <?php echo e($status == 'WARNING' ? 'bg-orange-500 text-white shadow-lg shadow-orange-200 scale-105 z-10' : 'text-gray-500 hover:text-orange-500'); ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?php echo e($status == 'WARNING' ? 'bg-white' : 'bg-orange-500'); ?>"></span>
                            WARNING (<= 5 HARI)
                        </button>
                        <button wire:click="setStatus('ON TRACK')" 
                           class="px-6 py-2.5 rounded-2xl text-[11px] font-black tracking-widest transition-all flex items-center gap-2 <?php echo e($status == 'ON TRACK' ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-200 scale-105 z-10' : 'text-gray-500 hover:text-emerald-500'); ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?php echo e($status == 'ON TRACK' ? 'bg-white' : 'bg-emerald-500'); ?>"></span>
                            STABIL
                        </button>
                    </div>
                </div>

                <div class="w-full xl:w-96">
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-gray-900 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" 
                               wire:model.live.debounce.300ms="search"
                               class="block w-full pl-12 pr-12 py-4 bg-white border-2 border-gray-100 rounded-[2rem] text-sm font-bold placeholder-gray-400 focus:outline-none focus:ring-4 focus:ring-gray-900/5 focus:border-gray-900 transition-all shadow-xl shadow-gray-200/50" 
                               placeholder="Cari No SPK atau Pelanggan...">
                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search): ?>
                            <button wire:click="clearSearch" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-300 hover:text-red-500 transition-all active:scale-95">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        
                        <div wire:loading wire:target="search" class="absolute inset-y-0 right-10 pr-2 flex items-center">
                            <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="bg-white/80 backdrop-blur-xl overflow-hidden shadow-[0_32px_64px_-12px_rgba(0,0,0,0.14)] rounded-[3rem] border border-white relative">
                <div class="absolute inset-0 bg-gradient-to-br from-white/50 to-transparent pointer-events-none"></div>
                
                <div class="overflow-x-auto relative">
                    <table class="min-w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-8 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-100">#</th>
                                <th class="px-6 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-100">Produk / Pelanggan</th>
                                <th class="px-6 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-100">Metrik Deadline</th>
                                <th class="px-6 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-100">Status</th>
                                <th class="px-6 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-100">Alasan Keterlambatan</th>
                                <th class="px-6 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-100">Info Material</th>
                                <th class="px-6 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-100">Deadline Baru</th>
                                <th class="px-8 py-6 text-center text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <tr class="group hover:bg-white transition-all cursor-default" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'order-'.e($order->id).''; ?>wire:key="order-<?php echo e($order->id); ?>">
                                    
                                    <td class="px-8 py-8 whitespace-nowrap text-xs font-black text-gray-300 group-hover:text-gray-900 transition-colors">
                                        <?php echo e(str_pad(($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration, 2, '0', STR_PAD_LEFT)); ?>

                                    </td>
                                    
                                    <td class="px-6 py-8">
                                        <div class="flex flex-col gap-1">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-black text-gray-900 font-mono tracking-tighter"><?php echo e($order->spk_number); ?></span>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->priority_scale == 1): ?>
                                                    <span class="relative flex h-2 w-2">
                                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-red-600"></span>
                                                    </span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                            <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider"><?php echo e($order->customer_name); ?></span>
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-8">
                                        <div class="flex flex-col gap-2">
                                            <div class="flex items-center gap-2 text-gray-700">
                                                <svg class="w-3.5 h-3.5 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 00-2 2z"></path>
                                                </svg>
                                                <span class="text-xs font-extrabold"><?php echo e($order->estimation_date ? $order->estimation_date->format('d M Y') : 'UNDEFINED'); ?></span>
                                            </div>
                                            <?php
                                                $days = (int) $order->calendar_days_remaining;
                                                $indicatorColor = $days < 0 ? 'bg-red-500 shadow-red-200' : ($days <= 5 ? 'bg-orange-500 shadow-orange-200' : 'bg-emerald-500 shadow-emerald-200');
                                            ?>
                                            <div class="flex flex-col gap-1 w-24">
                                                <div class="h-1 bg-gray-100 rounded-full overflow-hidden">
                                                    <div class="h-full <?php echo e($indicatorColor); ?> transition-all" style="width: <?php echo e(max(10, min(100, 100 - abs($days)*10))); ?>%"></div>
                                                </div>
                                                <span class="text-[9px] font-black <?php echo e($days < 0 ? 'text-red-500' : ($days <= 5 ? 'text-orange-500' : 'text-emerald-500')); ?> uppercase">
                                                    <?php echo e(abs($days)); ?> HARI <?php echo e($days < 0 ? 'TERLAMBAT' : 'SISA'); ?>

                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <?php
                                                $statusLabel = $order->warning_status ?? 'ON TRACK';
                                                $badgeStyle = match($statusLabel) {
                                                    'LATE' => 'bg-red-50 text-red-600 border-red-100 ring-4 ring-red-500/5',
                                                    'WARNING' => 'bg-orange-50 text-orange-600 border-orange-100 ring-4 ring-orange-500/5',
                                                    default => 'bg-emerald-50 text-emerald-600 border-emerald-100 ring-4 ring-emerald-500/5',
                                                };
                                            ?>
                                            <div class="px-3 py-2 rounded-2xl border flex items-center gap-2 shadow-sm transition-all group-hover:shadow-md <?php echo e($badgeStyle); ?>">
                                                <div class="w-1.5 h-1.5 rounded-full bg-current animate-pulse"></div>
                                                <span class="text-[10px] font-black tracking-widest uppercase"><?php echo e($statusLabel); ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-8 min-w-[220px]">
                                        <div class="relative group">
                                            <select wire:change="updateDescription(<?php echo e($order->id); ?>, $event.target.value)" 
                                                   class="w-full bg-gray-50 border-2 border-gray-100 rounded-[1.25rem] text-[11px] font-bold px-4 py-3 focus:bg-white focus:ring-4 focus:ring-gray-900/5 focus:border-gray-900 transition-all cursor-pointer appearance-none disabled:opacity-50">
                                                <option value="">PILIH ALASAN...</option>
                                                <option value="PO Material" <?php echo e($order->late_description == 'PO Material' ? 'selected' : ''); ?>>PO Material</option>
                                            </select>
                                            <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none opacity-40" wire:loading.remove wire:target="updateDescription(<?php echo e($order->id); ?>)">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                            </div>
                                            <div wire:loading wire:target="updateDescription(<?php echo e($order->id); ?>)" class="absolute right-4 top-1/2 -translate-y-1/2">
                                                <svg class="animate-spin h-3.5 w-3.5 text-gray-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-8">
                                        <div class="flex items-center gap-4">
                                            
                                            <div class="relative group/photo" x-data="{ uploading: false }" x-on:livewire-upload-start="uploading = true" x-on:livewire-upload-finish="uploading = false" x-on:livewire-upload-error="uploading = false">
                                                <input type="file" class="hidden" wire:model="photos.<?php echo e($order->id); ?>" id="photo-upload-<?php echo e($order->id); ?>" accept="image/*">
                                                
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->material_photo_url): ?>
                                                    <button wire:click.prevent="deletePhoto(<?php echo e($order->id); ?>)" 
                                                            class="absolute -top-2 -right-2 z-10 bg-red-500 text-white rounded-full p-1 shadow-lg hover:bg-red-600 hover:rotate-90 transition-all scale-0 group-hover/photo:scale-100 active:scale-75 border-2 border-white">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    </button>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                                <label for="photo-upload-<?php echo e($order->id); ?>" 
                                                       class="relative block w-14 h-14 bg-gray-50 border-2 border-dashed border-gray-200 rounded-2xl flex items-center justify-center cursor-pointer transition-all hover:bg-gray-100 hover:border-gray-400 group-hover:shadow-lg overflow-hidden">
                                                    
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->material_photo_url): ?>
                                                        <div class="relative h-full w-full">
                                                            <img src="<?php echo e($order->material_photo_url); ?>" class="h-full w-full object-cover transition-transform group-hover/photo:scale-110">
                                                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/photo:opacity-100 transition-opacity flex items-center justify-center">
                                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                                                            </div>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="flex flex-col items-center gap-1 opacity-40" x-show="!uploading">
                                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                                        </div>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                                    <div x-show="uploading" class="absolute inset-0 bg-white/60 flex items-center justify-center" style="display: none;">
                                                        <svg class="animate-spin h-5 w-5 text-gray-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                    </div>
                                                </label>
                                            </div>

                                            <div class="flex flex-col gap-2.5">
                                                
                                                <div class="flex flex-col gap-1">
                                                    <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest pl-1">Nama Material</span>
                                                    <div class="relative flex items-center">
                                                        <input type="text"
                                                               wire:change="updateMaterialName(<?php echo e($order->id); ?>, $event.target.value)"
                                                               value="<?php echo e($order->material_name); ?>"
                                                               placeholder="Input Nama..."
                                                               class="bg-gray-100/50 border-none rounded-xl text-[10px] font-black px-3 py-2 w-full focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all shadow-inner disabled:opacity-50">
                                                        
                                                        <div wire:loading wire:target="updateMaterialName(<?php echo e($order->id); ?>)" class="ml-2 absolute -right-6">
                                                            <svg class="animate-spin h-3 w-3 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                        </div>
                                                    </div>
                                                </div>

                                                
                                                <div class="flex flex-col gap-1">
                                                    <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest pl-1">Tgl Datang</span>
                                                    <div class="relative flex items-center">
                                                        <input type="date"
                                                               wire:change="updateMaterialArrivalDate(<?php echo e($order->id); ?>, $event.target.value)"
                                                               value="<?php echo e($order->material_arrival_date ? $order->material_arrival_date->format('Y-m-d') : ''); ?>"
                                                               class="bg-gray-100/50 border-none rounded-xl text-[10px] font-black px-3 py-2 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all shadow-inner disabled:opacity-50">
                                                        
                                                        <div wire:loading wire:target="updateMaterialArrivalDate(<?php echo e($order->id); ?>)" class="ml-2 absolute -right-6">
                                                            <svg class="animate-spin h-3 w-3 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-8">
                                        <div class="flex flex-col gap-1.5">
                                            <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest pl-1">Estimasi Baru</span>
                                            <div class="relative flex items-center">
                                                <input type="date"
                                                       wire:change="updateNewEstimationDate(<?php echo e($order->id); ?>, $event.target.value)"
                                                       value="<?php echo e($order->new_estimation_date ? $order->new_estimation_date->format('Y-m-d') : ''); ?>"
                                                       class="bg-gray-100/80 text-gray-900 border-none rounded-xl text-[10px] font-black px-3 py-2 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all shadow-inner disabled:opacity-50">
                                                
                                                <div wire:loading wire:target="updateNewEstimationDate(<?php echo e($order->id); ?>)" class="ml-2 absolute -right-6">
                                                    <svg class="animate-spin h-3 w-3 text-gray-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="px-8 py-8 whitespace-nowrap text-right">
                                        <a href="<?php echo e(route('production.index', ['search' => $order->spk_number])); ?>" 
                                           class="inline-flex items-center justify-center p-3.5 bg-gray-950 text-white rounded-[1.25rem] hover:bg-black hover:shadow-2xl hover:shadow-black/20 transition-all active:scale-90 group-hover:rotate-1">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <tr>
                                    <td colspan="8" class="px-8 py-24 text-center">
                                        <div class="flex flex-col items-center gap-6">
                                            <div class="w-24 h-24 bg-emerald-50 rounded-[2.5rem] flex items-center justify-center shadow-inner">
                                                <svg class="w-12 h-12 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <div class="max-w-md">
                                                <h5 class="text-2xl font-black text-gray-900 tracking-tight">Sistem Optimal</h5>
                                                <p class="text-gray-500 mt-2 font-medium">Semua alur pengerjaan produksi berjalan tepat waktu. Tidak ada kendala terdeteksi saat ini.</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($orders->hasPages()): ?>
                    <div class="px-8 py-10 bg-gray-50/50 backdrop-blur-md border-t border-gray-100 flex justify-center">
                        <?php echo e($orders->links()); ?>

                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div class="mt-16 bg-gray-900 rounded-[3rem] p-10 flex flex-col lg:flex-row justify-between items-center gap-8 shadow-3xl shadow-gray-900/40 relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white/5 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-white/5 rounded-full blur-3xl"></div>
                
                <div class="flex flex-col md:flex-row items-center gap-10">
                    <div class="flex items-center gap-4">
                        <div class="w-2 h-2 rounded-full bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.8)]"></div>
                        <span class="text-[10px] font-black tracking-[0.2em] text-white/40 uppercase">Metrik Terlambat</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-2 h-2 rounded-full bg-orange-500 shadow-[0_0_8px_rgba(249,115,22,0.8)]"></div>
                        <span class="text-[10px] font-black tracking-[0.2em] text-white/40 uppercase">Peringatan Kritis</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]"></div>
                        <span class="text-[10px] font-black tracking-[0.2em] text-white/40 uppercase">Sinkronisasi Optimal</span>
                    </div>
                </div>

                <div class="text-[11px] font-black text-white/30 uppercase tracking-[0.3em] flex items-center gap-3">
                    <svg class="w-4 h-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                    Pembaruan Terakhir: <?php echo e(now()->format('d.m.Y — H:i:s')); ?>

                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
        function lateInfoApp() {
            return {
                copied: false,
                copyApiKey() {
                    const input = document.getElementById('apiKeyInput');
                    input.select();
                    document.execCommand('copy');
                    this.copied = true;
                    setTimeout(() => this.copied = false, 2000);
                }
            }
        }
    </script>
    <?php $__env->stopPush(); ?>

    <style>
        [x-cloak] { display: none !important; }
        
        /* Modern Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #ddd;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #ccc;
        }

        /* Input Date Styling */
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(0.5);
            cursor: pointer;
        }
        
        .shadow-3xl {
            box-shadow: 0 35px 60px -15px rgba(0, 0, 0, 0.3);
        }

        /* Smooth Table Hover */
        tr:hover {
            transform: translateY(-2px);
            z-index: 20;
            box-shadow: 0 10px 30px -10px rgba(0,0,0,0.1);
        }

        /* Glassmorphism Classes */
        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</div>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\livewire\production\late-info.blade.php ENDPATH**/ ?>