
<div x-data="{ 
        open: false, 
        workOrderId: null, 
        selectedRack: '', 
        autoAssign: true,
        accessories: { tali: false, insole: false, box: false, other: '' }
     }" 
     @storage-modal.window="open = true; workOrderId = $event.detail.workOrderId; accessories = $event.detail.accessories || { tali: false, insole: false, box: false, other: '' }">
    
    <div x-show="open" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         x-cloak
         style="display: none;">
        
        
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" 
             @click="open = false"></div>
        
        
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-3xl shadow-2xl max-w-2xl w-full p-8 transform transition-all overflow-hidden border border-gray-100" 
                 @click.away="open = false"
                 x-show="open"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                
                
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-teal-50 rounded-full blur-3xl opacity-50 -z-10"></div>
                
                
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-teal-600 rounded-2xl shadow-lg shadow-teal-200">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-gray-900 tracking-tight">
                                Simpan ke Gudang
                            </h3>
                            <p class="text-sm text-gray-500 font-medium">Lengkapi data penyimpanan untuk monitoring unit</p>
                        </div>
                    </div>
                    <button @click="open = false" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                
                <form action="<?php echo e(route('storage.store')); ?>" method="POST" class="space-y-6">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="work_order_id" x-model="workOrderId">

                    
                    <div class="bg-gray-50/50 rounded-2xl border border-gray-100 p-6 space-y-6">
                        <div class="flex items-center gap-3">
                            <div class="w-1.5 h-6 bg-teal-500 rounded-full"></div>
                            <label class="text-xs font-black text-gray-700 uppercase tracking-widest">Aksesoris Penyerta</label>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            
                            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between group hover:border-teal-200 transition-all">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest group-hover:text-teal-600 transition-colors">Tali</span>
                                <div class="flex gap-1.5">
                                    <template x-for="type in ['T', 'N', 'S']">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-[10px] font-black transition-all shadow-sm"
                                             :class="{
                                                 'bg-red-500 text-white shadow-red-200': accessories.tali === type && type === 'T',
                                                 'bg-teal-500 text-white shadow-teal-200': accessories.tali === type && type !== 'T',
                                                 'bg-gray-50 text-gray-300': accessories.tali !== type
                                             }"
                                             x-text="type"></div>
                                    </template>
                                </div>
                            </div>

                            
                            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between group hover:border-teal-200 transition-all">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest group-hover:text-teal-600 transition-colors">Insole</span>
                                <div class="flex gap-1.5">
                                    <template x-for="type in ['T', 'N', 'S']">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-[10px] font-black transition-all shadow-sm"
                                             :class="{
                                                 'bg-red-500 text-white shadow-red-200': accessories.insole === type && type === 'T',
                                                 'bg-teal-500 text-white shadow-teal-200': accessories.insole === type && type !== 'T',
                                                 'bg-gray-50 text-gray-300': accessories.insole !== type
                                             }"
                                             x-text="type"></div>
                                    </template>
                                </div>
                            </div>

                            
                            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between group hover:border-teal-200 transition-all">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest group-hover:text-teal-600 transition-colors">Box</span>
                                <div class="flex gap-1.5">
                                    <template x-for="type in ['T', 'N', 'S']">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-[10px] font-black transition-all shadow-sm"
                                             :class="{
                                                 'bg-red-500 text-white shadow-red-200': accessories.box === type && type === 'T',
                                                 'bg-teal-500 text-white shadow-teal-200': accessories.box === type && type !== 'T',
                                                 'bg-gray-50 text-gray-300': accessories.box !== type
                                             }"
                                             x-text="type"></div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        
                        <div x-show="accessories.other" x-transition class="pt-2 border-t border-gray-100">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Lainnya:</span>
                                <span class="text-xs font-bold text-gray-700" x-text="accessories.other"></span>
                            </div>
                        </div>
                    </div>
                    
                    
                    <div class="flex items-center justify-between p-5 bg-teal-50/50 rounded-2xl border-2 border-teal-100 transition-all hover:bg-teal-50">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-teal-100 rounded-lg text-teal-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <div>
                                <label class="font-black text-gray-900 block tracking-tight">Auto-Assign Rak</label>
                                <p class="text-xs text-gray-500 font-medium">Sistem akan memilih rak paling optimal</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="autoAssign" class="sr-only peer">
                            <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-teal-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-teal-600"></div>
                        </label>
                    </div>
                    
                    
                    <div x-show="!autoAssign" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-3">
                        <label class="block font-black text-gray-700 text-sm uppercase tracking-wider">Pilih Rak Manual</label>
                        <div class="relative">
                            <select name="rack_code" 
                                    x-model="selectedRack"
                                    class="w-full pl-12 pr-4 py-4 bg-white border-2 border-gray-100 rounded-2xl focus:border-teal-500 focus:ring-4 focus:ring-teal-50 transition-all appearance-none font-bold text-gray-700 shadow-sm">
                                <option value="">-- Pilih Rak --</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = \App\Models\StorageRack::active()->available()->where('category', 'shoes')->orderBy('rack_code')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rack): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <option value="<?php echo e($rack->rack_code); ?>">
                                        <?php echo e($rack->rack_code); ?> - <?php echo e($rack->location); ?> 
                                        (<?php echo e($rack->current_count); ?>/<?php echo e($rack->capacity); ?>)
                                    </option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </select>
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                        </div>
                    </div>
                    
                    
                    <div class="space-y-3">
                        <label class="block font-black text-gray-700 text-sm uppercase tracking-wider">Catatan (Optional)</label>
                        <textarea name="notes" 
                                  rows="3" 
                                  placeholder="Contoh: Handle with care, Sepatu basah, dll."
                                  class="w-full px-5 py-4 bg-white border-2 border-gray-100 rounded-2xl focus:border-teal-500 focus:ring-4 focus:ring-teal-50 transition-all font-medium text-gray-700 placeholder-gray-300 shadow-sm"></textarea>
                    </div>
                    
                    
                    <div class="p-5 bg-orange-50/50 border-2 border-orange-100 rounded-2xl flex gap-4">
                        <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center text-2xl shrink-0">
                            💡
                        </div>
                        <div class="text-sm text-gray-700">
                            <p class="font-black text-orange-700 mb-1 uppercase tracking-wider">Panduan Penyimpanan:</p>
                            <ul class="space-y-1 font-medium text-gray-600">
                                <li class="flex items-center gap-2"><div class="w-1.5 h-1.5 rounded-full bg-orange-400"></div> Label akan otomatis ter-generate</li>
                                <li class="flex items-center gap-2"><div class="w-1.5 h-1.5 rounded-full bg-orange-400"></div> Tempel label pada unit/box</li>
                                <li class="flex items-center gap-2"><div class="w-1.5 h-1.5 rounded-full bg-orange-400"></div> Letakkan di rak sesuai kode <span class="font-black text-teal-600" x-text="selectedRack || '(Auto)'"></span></li>
                            </ul>
                        </div>
                    </div>
                    
                    
                    <div class="flex gap-4 pt-4">
                        <button type="button" 
                                @click="open = false"
                                class="flex-1 px-8 py-4 border-2 border-gray-100 text-gray-500 font-black rounded-2xl hover:bg-gray-50 transition-all uppercase tracking-widest text-xs">
                            Batal
                        </button>
                        <button type="submit"
                                class="flex-2 px-12 py-4 bg-gradient-to-r from-teal-600 to-orange-600 text-white font-black rounded-2xl hover:from-teal-700 hover:to-orange-700 transition-all shadow-xl shadow-orange-200 uppercase tracking-widest text-xs flex items-center justify-center gap-3 group">
                            <span>Simpan & Print Label</span>
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </form>
                
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views/storage/partials/assign-modal.blade.php ENDPATH**/ ?>