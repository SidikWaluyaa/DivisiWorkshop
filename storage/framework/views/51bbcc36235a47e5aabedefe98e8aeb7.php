<div>
    <div x-data="{ isOpen: <?php if ((object) ('isOpen') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('isOpen'->value()); ?>')<?php echo e('isOpen'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('isOpen'); ?>')<?php endif; ?> }" 
         x-show="isOpen"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[100] overflow-y-auto"
         style="display: none;">
        
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900/80 backdrop-blur-sm" @click="isOpen = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-[3rem] text-left overflow-hidden shadow-[0_35px_60px_-15px_rgba(0,0,0,0.5)] transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border border-gray-100">
                
                
                <div class="bg-gray-900 px-10 py-8 border-b border-gray-800 relative overflow-hidden">
                    <div class="absolute inset-0 bg-[#1B8A68]/10 mix-blend-overlay"></div>
                    <div class="flex justify-between items-center relative z-10">
                        <div>
                            <h3 class="text-3xl font-black text-white italic tracking-tighter uppercase">Tambah SPK ke Invoice</h3>
                            <p class="text-[10px] font-black text-[#1B8A68] uppercase tracking-[0.4em] mt-2">Daftar SPK Terdeteksi Untuk Pelanggan Ini</p>
                        </div>
                        <button @click="isOpen = false" class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-white/50 hover:text-white hover:bg-white/10 transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>

                <div class="p-10 space-y-8 bg-[#F8FAFC]">
                    
                    <div class="relative group/search">
                        <span class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within/search:text-[#1B8A68] transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input type="text" 
                               wire:model.live.debounce.300ms="search" 
                               placeholder="Cari Nomor SPK, Brand, atau Tipe Sepatu..." 
                               class="w-full pl-16 pr-8 py-5 bg-white border-2 border-transparent rounded-[2rem] focus:border-[#1B8A68]/20 focus:ring-4 focus:ring-[#1B8A68]/5 text-lg font-black italic tracking-tight placeholder-gray-300 transition-all shadow-inner">
                    </div>

                    
                    <div class="space-y-4 max-h-[400px] overflow-y-auto pr-4 custom-scrollbar">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $availableSpks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $spk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <label class="block relative group cursor-pointer">
                                <input type="checkbox" 
                                       wire:model="selectedSpks" 
                                       value="<?php echo e($spk->id); ?>" 
                                       class="absolute right-8 top-1/2 -translate-y-1/2 w-6 h-6 rounded-lg text-[#1B8A68] border-2 border-gray-200 focus:ring-[#1B8A68] focus:ring-offset-2 transition-all cursor-pointer">
                                
                                <div class="p-6 bg-white border-2 border-transparent rounded-3xl group-hover:border-[#1B8A68]/20 group-hover:bg-emerald-50/30 transition-all shadow-sm">
                                    <div class="flex items-center gap-6">
                                        <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center text-2xl shadow-inner border border-gray-100 group-hover:scale-110 transition-transform">👟</div>
                                        <div>
                                            <div class="text-lg font-black text-gray-900 italic tracking-tight uppercase leading-none mb-1 group-hover:text-[#1B8A68] transition-colors"><?php echo e($spk->spk_number); ?></div>
                                            <div class="flex items-center gap-3">
                                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest italic leading-none"><?php echo e($spk->shoe_brand); ?> • <?php echo e($spk->shoe_type); ?></span>
                                                <span class="w-1 h-1 rounded-full bg-gray-200"></span>
                                                <span class="text-[10px] font-black text-[#1B8A68] italic tracking-tight uppercase leading-none">Rp <?php echo e(number_format($spk->total_transaksi, 0, ',', '.')); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <div class="py-20 text-center">
                                <div class="w-20 h-20 mx-auto bg-gray-100 rounded-[2rem] flex items-center justify-center text-3xl mb-6 grayscale opacity-30">📂</div>
                                <h4 class="text-xl font-black text-gray-400 italic tracking-tighter uppercase">Tidak ada SPK Belum Terinvoice</h4>
                                <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest mt-2">Pastikan Nomor Telepon / Nama Customer Sesuai</p>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <div class="px-10 py-8 bg-white border-t border-gray-100 flex items-center justify-between">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest italic">Terpilih</span>
                        <span class="text-2xl font-black text-[#1B8A68] italic tracking-tighter leading-none"><?php echo e(count($selectedSpks)); ?> SPK</span>
                    </div>
                    
                    <div class="flex gap-4">
                        <button @click="isOpen = false" class="px-8 py-4 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-[1.5rem] text-[11px] font-black uppercase tracking-[0.2em] italic transition-all">BATAL</button>
                        <button wire:click="linkSpks" 
                                wire:loading.attr="disabled"
                                class="px-10 py-4 bg-[#1B8A68] hover:bg-emerald-600 text-white rounded-[1.5rem] text-[11px] font-black uppercase tracking-[0.2em] italic shadow-xl shadow-emerald-500/20 transition-all hover:-translate-y-1 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-3">
                            <span wire:loading.remove>HUBUNGKAN KE INVOICE</span>
                            <span wire:loading>MEMPROSES...</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\livewire\finance\invoice-add-spk.blade.php ENDPATH**/ ?>