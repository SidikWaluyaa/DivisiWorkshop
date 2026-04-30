<div class="px-4 pb-12 pt-8 sm:px-6 lg:px-8 max-w-7xl mx-auto"
    x-data="{
        init() {
            // Hotkey '/' or 'Ctrl+K'
            document.addEventListener('keydown', (e) => {
                if ((e.key === '/' || (e.key === 'k' && e.ctrlKey)) && e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                    this.$refs.searchInput.focus();
                }
            });

            // Global Barcode Scanner
            let barcode = '';
            let barcodeTimeout;
            document.addEventListener('keypress', (e) => {
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
                barcode += e.key;
                clearTimeout(barcodeTimeout);
                barcodeTimeout = setTimeout(() => {
                    if (barcode.length >= 5) {
                        window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('searchKeyword', barcode);
                        this.$refs.searchInput.focus();
                    }
                    barcode = '';
                }, 50);
            });
        },
        playBeep(type) {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                if(ctx.state === 'suspended') ctx.resume();
                const osc = ctx.createOscillator();
                const gainNode = ctx.createGain();
                osc.connect(gainNode);
                gainNode.connect(ctx.destination);
                
                if (type === 'success') {
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(800, ctx.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(1200, ctx.currentTime + 0.1);
                    gainNode.gain.setValueAtTime(0.1, ctx.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.1);
                    osc.start();
                    osc.stop(ctx.currentTime + 0.1);
                } else {
                    osc.type = 'sawtooth';
                    osc.frequency.setValueAtTime(300, ctx.currentTime);
                    gainNode.gain.setValueAtTime(0.1, ctx.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.3);
                    osc.start();
                    osc.stop(ctx.currentTime + 0.3);
                }
            } catch(e) {}
        }
    }"
>
    
    
    <div class="mb-10 text-center max-w-3xl mx-auto">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-[#22AF85]/10 border border-[#22AF85]/20 shadow-sm text-[#22AF85] mb-5">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
        <h1 class="text-3xl md:text-5xl font-black text-gray-900 tracking-tight">Internal Tracking</h1>
        <p class="mt-4 text-sm md:text-base text-gray-500 font-medium tracking-wide">Pencarian kilat. Langsung Scan Barcode (tanpa klik) atau Ketik Nama Customer.</p>
    </div>

    
    <div class="max-w-4xl mx-auto mb-12 relative z-10 group">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-6 md:pl-8 flex items-center pointer-events-none">
                <svg class="h-6 w-6 md:h-8 md:w-8 text-[#22AF85] transition-transform duration-300 group-focus-within:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input 
                x-ref="searchInput"
                wire:model.live.debounce.300ms="searchKeyword" 
                type="text" 
                autofocus
                class="block w-full pl-16 md:pl-20 pr-16 py-5 md:py-6 text-xl md:text-2xl font-bold bg-white border-2 border-gray-200 rounded-3xl text-gray-900 placeholder-gray-400 focus:ring-4 focus:ring-[#22AF85]/20 focus:border-[#22AF85] shadow-xl shadow-gray-200/50 hover:shadow-2xl hover:shadow-gray-200/60 transition-all duration-300 outline-none" 
                placeholder="Scan / Ketik SPK atau Nama..."
                autocomplete="off"
            >
            <div wire:loading class="absolute right-6 top-1/2 -translate-y-1/2">
                <svg class="animate-spin h-6 w-6 md:h-8 md:w-8 text-[#22AF85]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            
            <div class="absolute top-1/2 -translate-y-1/2 right-6 hidden sm:flex items-center gap-1 border border-gray-200 bg-gray-50 rounded-lg px-2.5 py-1.5 text-xs text-gray-500 font-bold tracking-wide shadow-sm" wire:loading.remove border-gray-200>
                <span class="text-gray-400 text-[10px] uppercase font-bold mr-1">Hotkeys</span> /
            </div>
        </div>
    </div>

    
    <div class="relative z-0">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(strlen(trim($searchKeyword)) > 0): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($results->count() > 0): ?>
                <div x-init="if(window.lastBeep !== '<?php echo e($searchKeyword); ?>-success') { playBeep('success'); window.lastBeep = '<?php echo e($searchKeyword); ?>-success'; }"></div>
                
                
                <div class="mb-5 text-sm font-bold text-gray-500 text-center">
                    Menemukan <span class="text-[#22AF85] font-black text-lg mx-1"><?php echo e($results->count()); ?></span> data relevan
                </div>

                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $spk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <div class="bg-white border-2 border-gray-100 hover:border-[#22AF85] hover:shadow-xl hover:shadow-[#22AF85]/10 transition-all duration-300 rounded-2xl p-6 shadow-md flex flex-col group relative overflow-hidden">
                            
                            
                            <div class="absolute top-0 left-0 w-full h-1.5 bg-gray-100 group-hover:bg-[#22AF85] transition-colors duration-300"></div>

                            <div class="flex flex-col mb-4 pt-1">
                                <span class="inline-flex w-fit items-center px-3 py-1 rounded bg-[#22AF85]/10 text-[#22AF85] font-black text-xs font-mono mb-3 border border-[#22AF85]/20 uppercase tracking-widest">
                                    <?php echo e($spk->spk_number); ?>

                                </span>
                                <h3 class="text-xl font-black text-gray-900 leading-tight line-clamp-2" title="<?php echo e(optional($spk->customer)->name ?? $spk->customer_name); ?>">
                                    <?php echo e(optional($spk->customer)->name ?? $spk->customer_name); ?>

                                </h3>
                                
                                <span class="inline-flex w-fit items-center mt-3 px-3 py-1.5 rounded-lg text-xs font-black shadow-sm font-mono bg-[#FFC232]/10 text-[#c99517] border border-[#FFC232]/50">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#FFC232] mr-2 animate-pulse"></span>
                                    <?php echo e(str_replace('_', ' ', $spk->status->name ?? $spk->status->value)); ?>

                                </span>
                            </div>
                            
                            <div class="text-sm font-semibold text-gray-500 space-y-2 mt-2 flex-grow">
                                <p class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                    <?php echo e($spk->shoe_brand ?? '-'); ?> (<?php echo e($spk->shoe_color ?? '-'); ?>)
                                </p>
                                <p class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($spk->status_pembayaran === 'L'): ?>
                                        <span class="text-[#22AF85] font-black">Lunas</span>
                                    <?php else: ?>
                                        <span class="text-red-500 font-bold">Minus: Rp <?php echo e(number_format($spk->sisa_tagihan, 0, ',', '.')); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </p>
                            </div>

                            
                            <?php
                                $statusVal = $spk->status->value ?? $spk->status;
                                $progress = 10;
                                if(in_array($statusVal, ['READY_TO_DISPATCH'])) $progress = 20;
                                elseif(in_array($statusVal, ['OTW_WORKSHOP'])) $progress = 35;
                                elseif(in_array($statusVal, ['ASSESSMENT'])) $progress = 50;
                                elseif(in_array($statusVal, ['PREPARATION', 'SORTIR'])) $progress = 65;
                                elseif(in_array($statusVal, ['PRODUCTION'])) $progress = 80;
                                elseif(in_array($statusVal, ['QC'])) $progress = 90;
                                elseif(in_array($statusVal, ['SELESAI', 'DIANTAR'])) $progress = 100;
                                elseif(in_array($statusVal, ['BATAL', 'DONASI'])) $progress = 0;
                            ?>
                            <div class="mt-6 mb-4 bg-gray-50 p-4 rounded-xl border border-gray-100/80">
                                <div class="w-full bg-gray-200 rounded-full h-2 relative overflow-hidden">
                                    <div class="bg-[#22AF85] h-2 rounded-full transition-all duration-1000" style="width: <?php echo e($progress); ?>%"></div>
                                </div>
                                <div class="flex justify-between text-[9px] text-gray-400 font-black uppercase tracking-widest mt-2.5">
                                    <span class="<?php echo e($progress >= 10 && $progress > 0 ? 'text-[#22AF85]' : ''); ?>">Gdng</span>
                                    <span class="<?php echo e($progress >= 40 ? 'text-[#22AF85]' : ''); ?>">Prep</span>
                                    <span class="<?php echo e($progress >= 60 ? 'text-[#22AF85]' : ''); ?>">Prod</span>
                                    <span class="<?php echo e($progress >= 80 ? 'text-[#22AF85]' : ''); ?>">QC</span>
                                    <span class="<?php echo e($progress >= 100 ? 'text-[#22AF85]' : ''); ?>">Done</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3 w-full mt-auto">
                                <a href="<?php echo e($this->getRedirectUrl($spk)); ?>" class="flex items-center justify-center gap-2 py-3 px-3 bg-[#FFC232] hover:bg-[#eeb121] text-gray-900 rounded-xl text-sm font-black transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5 truncate border border-[#eeb121]/20">
                                    Stasiun 
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </a>
                                <a href="<?php echo e(route('admin.orders.show', $spk->id)); ?>" class="flex items-center justify-center gap-2 py-3 px-3 bg-white border-2 border-gray-200 hover:border-gray-300 hover:bg-gray-50 text-gray-700 rounded-xl text-sm font-bold transition-all shadow-sm hover:shadow truncate focus:outline-none focus:ring-4 focus:ring-gray-100" title="Lihat History Keseluruhan">
                                    History
                                </a>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            <?php else: ?>
                <div x-init="if(window.lastBeep !== '<?php echo e($searchKeyword); ?>-fail') { playBeep('fail'); window.lastBeep = '<?php echo e($searchKeyword); ?>-fail'; }"></div>
                <div class="text-center py-20 bg-white border-2 border-gray-200 border-dashed rounded-3xl shadow-sm max-w-3xl mx-auto">
                    <div class="w-24 h-24 mx-auto bg-gray-50 rounded-full flex items-center justify-center mb-6 border border-gray-100">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900">SPK Tidak Ditemukan</h3>
                    <p class="mt-3 text-gray-500 font-medium max-w-md mx-auto leading-relaxed">Kami tidak dapat menemukan pencarian <br><span class="text-[#22AF85] font-mono font-bold bg-[#22AF85]/10 px-3 py-1 rounded inline-block mt-2">"<?php echo e($searchKeyword); ?>"</span></p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php else: ?>
            <div class="text-center py-20 opacity-80 flex flex-col items-center justify-center min-h-[40vh] bg-white rounded-3xl border border-gray-100 shadow-sm border-dashed max-w-4xl mx-auto group">
                <div class="w-28 h-28 bg-[#22AF85]/5 rounded-full flex items-center justify-center mb-8 border border-[#22AF85]/10 group-hover:bg-[#22AF85]/10 group-hover:scale-110 transition-all duration-500">
                    <svg class="w-14 h-14 text-[#22AF85]/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-gray-400 tracking-widest uppercase">Siap Menerima Scan Barcode</h3>
                <p class="mt-4 text-gray-400 font-medium">Bisa juga ketik manual dengan tekan tombol <kbd class="bg-gray-100 border border-gray-200 px-3 py-1.5 rounded-lg text-gray-600 font-black shadow-sm mx-1 text-sm">/</kbd> di keyboard Anda.</p>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\livewire\internal-tracking.blade.php ENDPATH**/ ?>