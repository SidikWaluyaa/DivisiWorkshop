<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?php echo e(asset('images/logo.png')); ?>" type="image/png">
    <title>SPK - <?php echo e($order->spk_number); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Inter:wght@400;600;700&display=swap');
        
        body { 
            font-family: 'Inter', sans-serif; 
            -webkit-print-color-adjust: exact !important; 
            print-color-adjust: exact !important; 
        }
        
        .font-display { font-family: 'Outfit', sans-serif; }

        @page { size: A4; margin: 0; }
        
        .page-container {
            width: 210mm; 
            min-height: 297mm; 
            background: white; 
            margin: 0 auto;
            position: relative;
            display: grid;
            grid-template-columns: 75mm 135mm; /* Sidebar + Main */
        }

        .sidebar {
            background: #22B086; /* Emerald Green */
            color: white;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            min-height: 100%;
        }

        .main-content {
            padding: 16px 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .orange-bar {
            background: #FFC232; /* Official Orange */
            color: #1e293b; /* Dark Slate for high contrast on yellow/orange */
            padding: 6px 14px;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 12px;
            border-radius: 6px;
            letter-spacing: 0.05em;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        @media print {
            body { background: white; margin: 0; padding: 0; }
            .page-container { 
                box-shadow: none; 
                width: 210mm; 
                height: auto; 
                min-height: 297mm;
            }
            .no-print { display: none; }
            .avoid-break { page-break-inside: avoid; }
        }
    </style>
</head>
<body class="bg-gray-100 p-8 flex justify-center min-h-screen print:p-0 print:h-auto">


    <!-- MAIN PAGE CONTAINER -->
    <div class="page-container shadow-2xl overflow-hidden">
        
        <!-- SIDEBAR (LEFT) -->
        <aside class="sidebar h-full shrink-0" style="background-color: #22B086;">
            
            <div class="flex items-center justify-between gap-3 mb-2">
                <div class="flex items-center gap-3">
                    <img src="<?php echo e(asset('images/logo.png')); ?>" class="h-10 w-auto brightness-0 invert" onerror="this.style.display='none'">
                    <div>
                        <h1 class="font-display font-black text-xs leading-none">SHOE WORKSHOP</h1>
                        <p class="text-[10px] font-bold text-white/80 mt-0.5 tracking-tighter">Form <span class="text-white">SPK Customer</span></p>
                    </div>
                </div>
                
                <div class="bg-white p-1 rounded-lg">
                    <?php echo $barcode; ?>

                </div>
            </div>

            
            <div class="relative avoid-break">
                <div class="aspect-square bg-white/10 rounded-xl overflow-hidden border border-white/20 relative group">
                     <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->spk_cover_photo): ?>
                        <img src="<?php echo e($order->spk_cover_photo_url); ?>" class="w-full h-full object-contain">
                    <?php else: ?>
                        <div class="flex flex-col items-center justify-center h-full text-white/20">
                            <svg class="w-12 h-12 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="text-[8px] font-bold uppercase tracking-widest opacity-50">Tanpa Foto</span>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    
                    
                    <div class="absolute top-2 right-2 bg-white text-teal-900 w-12 h-12 rounded-lg flex flex-col items-center justify-center shadow-lg">
                        <span class="text-[8px] font-bold uppercase leading-none text-gray-400">Size</span>
                        <span class="text-xl font-black font-display leading-none"><?php echo e($order->shoe_size); ?></span>
                    </div>
                </div>
            </div>

            
            <div class="mt-1 space-y-1 avoid-break">
                <p class="text-[9px] font-black text-white uppercase tracking-widest">Keterangan Besar :</p>
                <div class="bg-white/5 rounded-lg border border-white/10 p-2 flex-grow min-h-[90px] text-[10px] leading-tight opacity-90">
                        <?php echo e($order->notes ?? $order->technician_notes ?? ''); ?>

                </div>
            </div>

            
            <div class="mt-auto space-y-3 avoid-break">
                
                <div class="bg-white/5 rounded-xl border border-white/10 overflow-hidden">
                    <div class="bg-white/10 px-3 py-1 flex items-center justify-center">
                        <span class="text-[9px] font-black tracking-widest uppercase" style="color: #FFC232;">ACC Follow Up</span>
                    </div>
                    <div class="p-3 space-y-3">
                        <div class="grid grid-cols-2 gap-2">
                             <div>
                                <p class="text-[8px] font-black text-white uppercase mb-1">Lolos QC:</p>
                                <div class="h-14 bg-white/5 rounded border border-white/5"></div>
                             </div>
                             <div>
                                <p class="text-[8px] font-black text-white uppercase mb-1">Verifikasi OTW:</p>
                                <div class="h-14 bg-white/5 rounded border border-white/5"></div>
                             </div>
                        </div>
                        <div class="grid grid-cols-1 gap-2 border-t border-white/5 pt-2">
                            <div class="flex justify-between items-center text-[9px]">
                                <span class="font-black text-white">Tanggal Selesai:</span>
                                <span class="w-20 border-b border-dotted border-white/60 h-4"></span>
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="font-black text-white text-[9px] uppercase">Follow up :</span>
                                <div class="h-8 border-b border-dotted border-white/60"></div>
                            </div>
                            <div class="flex justify-between items-end">
                                <span class="text-[8px] font-black text-white uppercase">Paraf QC</span>
                                <div class="w-10 h-10 border-2 border-white/20 rounded bg-white/5"></div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="bg-white/5 rounded-xl border border-white/10 overflow-hidden">
                    <div class="px-3 py-1 flex items-center justify-center" style="background-color: rgba(255, 255, 255, 0.1);">
                        <span class="text-[9px] font-black tracking-widest uppercase" style="color: #FFC232;">ACC QC</span>
                    </div>
                    <div class="p-2 space-y-2">
                        <div>
                            <p class="text-[8px] font-black text-white uppercase mb-1">Revisi :</p>
                            <div class="h-16 bg-white/5 rounded border border-white/5"></div>
                        </div>
                        <div class="flex justify-between items-end gap-2">
                            <div class="flex-grow">
                                <p class="text-[8px] font-black text-white uppercase mb-1">Lolos QC :</p>
                                <div class="h-6 border-b border-dotted border-white/40"></div>
                            </div>
                            <div class="flex flex-col items-center">
                                <span class="text-[8px] font-black text-white uppercase mb-1">Paraf QC</span>
                                <div class="w-10 h-10 border-2 border-white/20 rounded bg-white/5"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="mt-4 pt-4 border-t border-white/20 relative overflow-hidden avoid-break">
                <div class="absolute -left-10 bottom-0 opacity-10 blur-xl w-32 h-32 bg-amber-400 rounded-full"></div>
                <div class="flex items-center gap-2 relative z-10">
                    <div class="text-xs font-black leading-none text-white">
                        #<span style="color: #FFC232;">living</span>with<br><span class="text-xl">PASSION</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- MAIN AREA (RIGHT) -->
        <main class="main-content">
            
            <div class="grid grid-cols-2 gap-4 avoid-break">
                <div class="space-y-3">
                    <div class="bg-gray-50 rounded-lg p-2.5 px-4 border border-gray-100 flex items-center justify-between">
                        <span class="text-[10px] font-bold text-gray-600 uppercase tracking-tight">Nomor SPK</span>
                        <span class="text-sm font-black font-mono tracking-tighter" style="color: #22B086;"><?php echo e($order->spk_number); ?></span>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-2.5 px-4 border border-gray-100 flex items-center justify-between">
                        <span class="text-[10px] font-bold text-gray-600 uppercase tracking-tight">Nama Customer</span>
                        <span class="text-sm font-black text-gray-900 tracking-tight"><?php echo e($order->customer_name); ?></span>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->csLead): ?>
                    <div class="bg-gray-50 rounded-lg p-2.5 px-4 border border-gray-100 flex items-center justify-between">
                        <span class="text-[10px] font-bold text-gray-600 uppercase tracking-tight">Order Channel</span>
                        <div class="px-3 py-1 rounded-md border font-black text-[10px] tracking-widest <?php echo e($order->csLead->channel === 'ONLINE' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200'); ?>">
                            <?php echo e($order->csLead->channel); ?>

                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="bg-gray-50 rounded-lg p-3.5 border border-gray-100 min-h-[80px]">
                        <p class="text-[10px] font-bold text-gray-600 uppercase mb-1.5 tracking-tight">Alamat Lengkap</p>
                        <p class="text-xs font-bold text-gray-900 leading-snug">
                            <?php echo e($order->customer_address); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->customer): ?>
                                <br><span class="text-gray-500 font-medium">
                                    <?php echo e($order->customer->village ? $order->customer->village . ', ' : ''); ?>

                                    <?php echo e($order->customer->district ? $order->customer->district . ', ' : ''); ?>

                                    <?php echo e($order->customer->city ? $order->customer->city . ', ' : ''); ?>

                                    <?php echo e($order->customer->province ? $order->customer->province : ''); ?>

                                </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </p>
                    </div>
                </div>

                
                <div class="bg-white border rounded-lg p-4 space-y-3 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-full opacity-5 skew-x-12" style="background-color: #22B086;"></div>
                    
                    <p class="text-[10px] font-black uppercase tracking-widest mb-2 flex items-center gap-2" style="color: #22B086;">
                        <svg class="w-3 h-3" style="color: #FFC232;" fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path></svg>
                        Accessories
                    </p>
                    
                    <div class="flex flex-wrap gap-2">
                         <?php $acc = $order; ?>
                         <div class="px-2 py-1 rounded border flex items-center gap-2" style="background-color: #f0fdf4; border-color: #bbf7d0;">
                             <span class="text-[8px] font-black uppercase" style="color: #22B086; opacity: 0.6;">INS:</span>
                             <span class="text-[11px] font-black" style="color: #22B086;"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($acc->accessories_insole, ['Simpan', 'S'])): ?> S <?php elseif(in_array($acc->accessories_insole, ['Nempel', 'N'])): ?> N <?php else: ?> T <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
                         </div>
                         <div class="px-2 py-1 rounded border flex items-center gap-2" style="background-color: #f0fdf4; border-color: #bbf7d0;">
                             <span class="text-[8px] font-black uppercase" style="color: #22B086; opacity: 0.6;">TALI:</span>
                             <span class="text-[11px] font-black" style="color: #22B086;"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($acc->accessories_tali, ['Simpan', 'S'])): ?> S <?php elseif(in_array($acc->accessories_tali, ['Nempel', 'N'])): ?> N <?php else: ?> T <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
                         </div>
                         <div class="px-2 py-1 rounded border flex items-center gap-2" style="background-color: #f0fdf4; border-color: #bbf7d0;">
                             <span class="text-[8px] font-black uppercase" style="color: #22B086; opacity: 0.6;">BOX:</span>
                             <span class="text-[11px] font-black" style="color: #22B086;"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($acc->accessories_box, ['Simpan', 'S'])): ?> S <?php elseif(in_array($acc->accessories_box, ['Nempel', 'N'])): ?> N <?php else: ?> T <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
                         </div>
                    </div>
                    
                    <div class="pt-2 border-t border-gray-100 flex items-center gap-2">
                         <span class="text-[8px] font-black uppercase shrink-0" style="color: #22B086; opacity: 0.6;">LAINNYA:</span>
                         <span class="text-[10px] font-bold text-gray-700 border-b border-dotted border-gray-300 flex-grow pb-1">
                             <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->accessories_other && $order->accessories_other != 'Tidak Ada'): ?>
                                <?php echo e($order->accessories_other); ?>

                             <?php else: ?>
                                <span class="text-gray-200 tracking-tighter">...................................................</span>
                             <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                         </span>
                     </div>
                </div>
                
            </div>

            
            <div class="flex-grow mt-0">
                  <p class="text-[10px] font-black text-gray-600 uppercase tracking-[0.2em] mb-2 ml-1">Jasa Pengerjaan :</p>
                 
                 <div class="space-y-4">
                      <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $order->workOrderServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                      <div class="avoid-break group">
                          
                          <div class="orange-bar shadow-sm">
                               <div class="flex items-center gap-2">
                                   <span class="w-1.5 h-4 bg-slate-900/20 rounded-full"></span>
                                   <span class="font-black"><?php echo e(strtoupper($service->custom_service_name ?? $service->service->name ?? 'Service Name')); ?></span>
                                   <span class="mx-1 opacity-20 text-lg font-light">|</span>
                                   <span class="opacity-70"><?php echo e(strtoupper($service->category_name ?? ($service->service ? $service->service->category : 'S'))); ?></span>
                               </div>
                               <div class="text-[9px] font-black opacity-40 tracking-tighter">PROSES WORKSHOP</div>
                          </div>
                          
                          
                          <div class="mt-2 pl-4 border-l-2 border-gray-100 flex items-start justify-between gap-4">
                               
                               <div class="flex-grow space-y-1">
                                   <div class="flex items-start gap-2">
                                       <span class="text-[9px] font-black text-teal-600 uppercase tracking-tighter shrink-0 pt-0.5">NB :</span>
                                       <p class="text-[10px] font-bold text-gray-800 leading-normal">
                                           <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_array($service->service_details)): ?>
                                               <?php echo e(implode(', ', array_map(function($k, $v) { return strtoupper($v); }, array_keys($service->service_details), $service->service_details))); ?>

                                           <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                           <?php echo e($service->notes ?? ''); ?>

                                       </p>
                                   </div>
                               </div>

                               
                               <div class="flex items-center gap-4 shrink-0">
                                   <div class="flex flex-col items-center gap-1">
                                       <span class="text-[7px] font-black text-gray-400 uppercase">QC</span>
                                       <div class="w-5 h-5 rounded border-2 border-teal-500 bg-white"></div>
                                   </div>
                                   <div class="flex flex-col items-center gap-1">
                                       <span class="text-[7px] font-black text-gray-400 uppercase">Paraf</span>
                                       <div class="w-10 h-6 border-b-2 border-gray-200"></div>
                                   </div>
                               </div>
                          </div>
                      </div>
                      <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                 </div>
            </div>

            
            <div class="mt-auto pt-4 border-t-2 border-gray-50">
                 <div class="grid grid-cols-3 gap-3">
                      <div class="bg-gray-100/50 rounded-xl p-3 border border-gray-200/50 flex flex-col justify-between">
                          <p class="text-[9px] font-black text-center text-teal-900 uppercase mb-2">SPK Masuk :</p>
                          <div class="h-4 border-b border-dotted border-gray-300 mt-1"></div>
                      </div>
                      <div class="bg-teal-50 rounded-xl p-3 border border-teal-100 flex flex-col justify-between">
                          <p class="text-[9px] font-black text-center text-teal-900 uppercase mb-2">Estimasi Selesai :</p>
                          <div class="h-4 border-b border-dotted border-gray-300 mt-1"></div>
                      </div>
                      <div class="bg-gray-100/50 rounded-xl p-3 border border-gray-200/50 flex flex-col justify-between">
                          <p class="text-[9px] font-black text-center text-teal-900 uppercase mb-2">SPK Keluar :</p>
                          <div class="h-4 border-b border-dotted border-gray-300 mt-1"></div>
                      </div>
                 </div>

                 
                 <div class="mt-2 bg-white border-2 border-gray-100 rounded-xl p-3 min-h-[70px] shadow-inner relative">
                      <div class="absolute top-1 left-3 text-[9px] font-black text-teal-900 uppercase tracking-widest opacity-40">Revisi Jasa</div>
                      <div class="mt-3 text-[10px] text-gray-300 italic">Tambahan biaya/jasa baru...</div>
                 </div>
            </div>

            
            <div class="mt-4 flex justify-between items-center px-4 opacity-50">
                <div class="text-[10px] font-black uppercase" style="color: #22B086;">Shoe Workshop Premium</div>
                <div class="text-[10px] font-bold text-gray-400">#morethanrepair</div>
            </div>
        </main>

    </div>

    <script>
        window.onload = function() {
            // Auto Print
             window.print();
        }
    </script>
</body>
</html>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\assessment\print-spk-premium.blade.php ENDPATH**/ ?>