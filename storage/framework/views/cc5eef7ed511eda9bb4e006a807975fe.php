<div x-data="{ 
    showFilters: false,
    search: '<?php echo e(request('search')); ?>',
    workStatus: '<?php echo e(request('work_status', 'all')); ?>',
    priority: '<?php echo e(request('priority', 'all')); ?>',
    technician: '<?php echo e(request('technician', 'all')); ?>',
    sort: '<?php echo e(request('sort', 'asc')); ?>',
    
    applyFilters() {
        const params = new URLSearchParams(window.location.search);
        
        // Update search param
        if (this.search) {
            params.set('search', this.search);
        } else {
            params.delete('search');
        }
        
        // Update work_status param
        if (this.workStatus && this.workStatus !== 'all') {
            params.set('work_status', this.workStatus);
        } else {
            params.delete('work_status');
        }
        
        // Update priority param
        if (this.priority && this.priority !== 'all') {
            params.set('priority', this.priority);
        } else {
            params.delete('priority');
        }
        
        // Update technician param
        if (this.technician && this.technician !== 'all') {
            params.set('technician', this.technician);
        } else {
            params.delete('technician');
        }
        
        // Update sort param
        if (this.sort && this.sort !== 'asc') {
            params.set('sort', this.sort);
        } else {
            params.delete('sort');
        }
        
        // Keep the current tab
        const currentTab = new URLSearchParams(window.location.search).get('tab');
        if (currentTab) {
            params.set('tab', currentTab);
        }
        
        // Redirect with new params
        window.location.search = params.toString();
    },
    
    clearFilters() {
        const params = new URLSearchParams();
        const currentTab = new URLSearchParams(window.location.search).get('tab');
        if (currentTab) {
            params.set('tab', currentTab);
        }
        window.location.search = params.toString();
    },
    
    getActiveFilterCount() {
        let count = 0;
        if (this.search) count++;
        if (this.workStatus && this.workStatus !== 'all') count++;
        if (this.priority && this.priority !== 'all') count++;
        if (this.technician && this.technician !== 'all') count++;
        if (this.sort && this.sort !== 'asc') count++;
        return count;
    }
}" class="mb-6 bg-white rounded-xl shadow-sm border border-gray-200 p-4">
    
    
    <div class="flex flex-col xl:flex-row gap-4">
        
        <div class="flex-1">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" 
                       x-model="search"
                       placeholder="Cari SPK, Customer, Brand, HP..."
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 text-sm shadow-sm transition-all"
                       @keyup.enter="applyFilters()">
            </div>
        </div>

        
        <div class="flex flex-wrap lg:flex-nowrap items-center gap-3">
            
            <div class="w-full sm:w-48">
                <select x-model="workStatus"
                        <?php echo e(request('tab') === 'all' ? 'disabled' : ''); ?>

                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 text-sm <?php echo e(request('tab') === 'all' ? 'bg-gray-100 cursor-not-allowed' : ''); ?> shadow-sm">
                    <option value="all"><?php echo e(request('tab') === 'all' ? '📊 N/A (Tab All)' : '📊 Semua Status'); ?></option>
                    <option value="not_started">⏸️ Belum Start</option>
                    <option value="in_progress">⚙️ Sedang Dikerjakan</option>
                    <option value="completed">✅ Selesai</option>
                </select>
            </div>

            
            <div class="w-full sm:w-48">
                <select x-model="sort"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 text-sm shadow-sm">
                    <option value="asc">📅 Terlama (Oldest)</option>
                    <option value="desc">📅 Terbaru (Newest)</option>
                </select>
            </div>

            
            <button @click="showFilters = !showFilters"
                    type="button"
                    class="w-full sm:w-auto px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold transition-colors flex items-center justify-center gap-2 shadow-sm border border-gray-200 whitespace-nowrap">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                </svg>
                <span x-text="showFilters ? 'Tutup Filter' : 'Filter Lanjutan'">Filter Lanjutan</span>
            </button>

            
            <div class="flex gap-2 w-full sm:w-auto">
                <button @click="applyFilters()"
                        type="button"
                        class="flex-1 sm:flex-none px-6 py-2 bg-gradient-to-r from-teal-600 to-teal-700 hover:from-teal-700 hover:to-teal-800 text-white rounded-lg text-sm font-bold transition-all shadow-md active:scale-95 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Cari
                </button>
                <button @click="clearFilters()"
                        type="button"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-semibold transition-colors border border-gray-300">
                    Reset
                </button>
            </div>
        </div>
    </div>

    
    <div x-show="showFilters" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="mt-4 pt-4 border-t border-gray-200 grid grid-cols-1 md:grid-cols-3 gap-3"
         style="display: none;">
        
        
        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">⚡ Prioritas</label>
            <select x-model="priority"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 text-sm">
                <option value="all">Semua</option>
                <option value="urgent">Prioritas/Urgent</option>
                <option value="regular">Regular</option>
            </select>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($technicians) && $technicians->count() > 0): ?>
        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">👤 Teknisi</label>
            <select x-model="technician"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 text-sm">
                <option value="all">Semua Teknisi</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $technicians; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tech): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <option value="<?php echo e($tech->id); ?>"><?php echo e($tech->name); ?></option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </select>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="flex items-end">
            <div class="text-xs text-gray-500">
                <span class="font-semibold">Filter Aktif:</span>
                <span class="ml-1 px-2 py-1 bg-teal-100 text-teal-700 rounded-full font-bold"
                      x-text="getActiveFilterCount()">0</span>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\components\workshop-filter-bar.blade.php ENDPATH**/ ?>