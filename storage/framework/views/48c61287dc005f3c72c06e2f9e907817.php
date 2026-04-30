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

    <style>
        :root {
            --primary-green: #22AF85;
            --accent-yellow: #FFC232;
            --dark-gray: #1F2937;
            --light-gray: #F9FAFB;
        }
        .bg-primary-green { background-color: var(--primary-green); }
        .text-primary-green { color: var(--primary-green); }
        .border-primary-green { border-color: var(--primary-green); }
        .bg-accent-yellow { background-color: var(--accent-yellow); }
        .text-accent-yellow { color: var(--accent-yellow); }
        .border-accent-yellow { border-color: var(--accent-yellow); }
        
        .premium-card {
            background: white;
            border: 1px solid rgba(0,0,0,0.03);
            box-shadow: 0 4px 20px -5px rgba(0,0,0,0.05);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        }
        .premium-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -10px rgba(34, 175, 133, 0.08);
        }
        .glass-header {
            background: linear-gradient(135deg, var(--primary-green) 0%, #1a8a69 100%);
            position: relative;
        }
        .rack-item {
            aspect-ratio: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            font-weight: 900;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 3px solid white;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }
        .rack-item:hover {
            transform: scale(1.15) rotate(1deg);
            z-index: 20;
            box-shadow: 0 15px 30px -5px rgba(34, 175, 133, 0.2);
        }

        /* Premium Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #22AF85;
            border-radius: 10px;
            border: 2px solid #f1f1f1;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #1a8a69;
        }
    </style>

    <?php
        $category = request('category', session('storage_category', 'shoes'));
    ?>

    <div x-data="{ 
        showRackModal: false, 
        selectedRack: null, 
        rackItems: [],
        isLoading: false,
        fetchRackDetails(rackCode) {
            this.selectedRack = rackCode;
            this.showRackModal = true;
            this.isLoading = true;
            this.rackItems = [];
            
            fetch(`<?php echo e(route('storage.rack-details', ['rackCode' => 'PLACEHOLDER'])); ?>`.replace('PLACEHOLDER', rackCode) + `?category=<?php echo e($category); ?>`)
                .then(res => res.json())
                .then(data => {
                    this.rackItems = data.items;
                    this.isLoading = false;
                })
                .catch(err => {
                    console.error(err);
                    this.isLoading = false;
                });
        }
    }" class="min-h-screen bg-[#FDFDFD]">
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">
            
            
            <section class="glass-header overflow-hidden rounded-[3rem] shadow-2xl relative border-b-8 border-accent-yellow">
                <div class="relative z-10 px-12 py-14 flex flex-col lg:flex-row lg:items-center justify-between gap-10">
                    <div>
                        <div class="inline-flex items-center gap-3 px-5 py-2.5 bg-white/10 backdrop-blur-xl rounded-full border border-white/20 mb-6 shadow-xl">
                            <span class="w-2.5 h-2.5 rounded-full bg-accent-yellow shadow-[0_0_15px_rgba(255,194,50,0.8)] animate-pulse"></span>
                            <span class="text-[10px] font-black text-white uppercase tracking-[0.3em]">Storage Management System</span>
                        </div>
                        <h1 class="text-6xl lg:text-7xl font-black text-white tracking-tighter leading-[0.9]">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($category === 'before'): ?> GUDANG<br/><span class="text-accent-yellow">INBOUND</span>
                            <?php elseif($category === 'accessories'): ?> AREA<br/><span class="text-accent-yellow">AKSESORIS</span>
                            <?php else: ?> GUDANG<br/><span class="text-accent-yellow">FINISH</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </h1>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-5">
                        <a href="<?php echo e(route('storage.racks.index')); ?>" class="px-10 py-5 bg-accent-yellow text-gray-900 rounded-2xl font-black hover:bg-yellow-400 transition-all flex items-center justify-center gap-3 shadow-[0_15px_30px_-5px_rgba(255,194,50,0.4)] transform hover:-translate-y-1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                            MASTER RAK
                        </a>

                        <form action="<?php echo e(route('storage.index')); ?>" method="GET" class="relative">
                            <input type="hidden" name="category" value="<?php echo e($category); ?>">
                            <input type="text" name="search" value="<?php echo e($search ?? ''); ?>" 
                                   placeholder="Cari SPK / Customer..." 
                                   class="w-full sm:w-80 pl-16 pr-8 py-5 rounded-2xl border-none bg-white/10 backdrop-blur-xl text-white placeholder-white/50 focus:bg-white focus:text-gray-900 focus:ring-0 transition-all shadow-xl font-bold border border-white/20">
                            <div class="absolute left-6 top-1/2 -translate-y-1/2 text-accent-yellow">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 -mr-48 -mt-48 rounded-full blur-3xl"></div>
            </section>

            
            <nav class="flex overflow-x-auto gap-3 pb-2 scrollbar-hide">
                <a href="<?php echo e(route('storage.index', ['category' => 'shoes'])); ?>" 
                   class="<?php echo e($category === 'shoes' ? 'bg-primary-green text-white shadow-2xl translate-y-[-2px]' : 'bg-white text-gray-400 hover:text-primary-green'); ?> px-10 py-5 rounded-2xl font-black text-sm flex flex-col items-center gap-1 min-w-[160px] transition-all border border-gray-100 shadow-sm">
                    <span class="text-2xl">👟</span>
                    <span>SEPATU FINISH</span>
                </a>
                <a href="<?php echo e(route('storage.index', ['category' => 'accessories'])); ?>" 
                   class="<?php echo e($category === 'accessories' ? 'bg-primary-green text-white shadow-2xl translate-y-[-2px]' : 'bg-white text-gray-400 hover:text-primary-green'); ?> px-10 py-5 rounded-2xl font-black text-sm flex flex-col items-center gap-1 min-w-[160px] transition-all border border-gray-100 shadow-sm">
                    <span class="text-2xl">🎒</span>
                    <span>AKSESORIS</span>
                </a>
                <a href="<?php echo e(route('storage.index', ['category' => 'before'])); ?>" 
                   class="<?php echo e($category === 'before' ? 'bg-primary-green text-white shadow-2xl translate-y-[-2px]' : 'bg-white text-gray-400 hover:text-primary-green'); ?> px-10 py-5 rounded-2xl font-black text-sm flex flex-col items-center gap-1 min-w-[160px] transition-all border border-gray-100 shadow-sm">
                    <span class="text-2xl">📥</span>
                    <span>INBOUND RACK</span>
                </a>
            </nav>

            
            <section class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <?php
                    $statsConfig = [
                        ['label' => 'Total Item Stored', 'value' => $stats['total_stored'], 'color' => 'text-primary-green', 'bg' => 'bg-white'],
                        ['label' => 'Item Out / Retrieved', 'value' => $stats['total_retrieved'], 'color' => 'text-gray-900', 'bg' => 'bg-accent-yellow'],
                        ['label' => 'Peringatan Overdue', 'value' => $stats['overdue_count'], 'color' => 'text-primary-green', 'bg' => 'bg-white'],
                        ['label' => 'Rata-rata Simpan', 'value' => number_format($stats['avg_storage_days'], 1) . ' Hari', 'color' => 'text-gray-500', 'bg' => 'bg-gray-50'],
                    ];
                ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $statsConfig; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <div class="premium-card rounded-3xl p-8 <?php echo e($s['bg']); ?> border">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2"><?php echo e($s['label']); ?></p>
                        <h4 class="text-3xl font-black <?php echo e($s['color']); ?>"><?php echo e($s['value']); ?></h4>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </section>

            
            <section class="premium-card rounded-[3rem] overflow-hidden border-2 border-gray-50">
                <div class="px-12 py-10 border-b border-gray-50 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 bg-gray-50/20">
                    <div>
                        <h3 class="text-3xl font-black text-gray-900 tracking-tighter">PETA VISUAL GUDANG</h3>
                        <p class="text-xs font-bold text-primary-green uppercase tracking-[0.2em] mt-1">Status Ketersediaan Rak & Slot</p>
                    </div>
                    <div class="flex items-center gap-6">
                        <div class="flex items-center gap-2">
                            <span class="w-4 h-4 rounded-lg bg-white border-2 border-primary-green/20"></span>
                            <span class="text-[10px] font-black text-gray-400 uppercase">KOSONG</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-4 h-4 rounded-lg bg-primary-green"></span>
                            <span class="text-[10px] font-black text-gray-400 uppercase">TERISI</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-4 h-4 rounded-lg bg-accent-yellow"></span>
                            <span class="text-[10px] font-black text-gray-400 uppercase">PENUH / HIGH</span>
                        </div>
                    </div>
                </div>
                
                <div class="p-12">
                    
                    <div class="mb-14">
                        <div class="flex justify-between items-end mb-4">
                            <span class="text-xs font-black text-gray-900 uppercase tracking-widest">Utilisasi Kapasitas Global</span>
                            <span class="text-3xl font-black text-primary-green"><?php echo e(number_format($rackUtilization['utilization_percentage'], 1)); ?>%</span>
                        </div>
                        <div class="w-full bg-gray-100 h-6 rounded-full overflow-hidden p-1 shadow-inner">
                            <div class="bg-primary-green h-full rounded-full shadow-lg shadow-emerald-200 transition-all duration-1000" style="width: <?php echo e($rackUtilization['utilization_percentage']); ?>%"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10 gap-5">
                        <?php
                            $currentRacks = $category === 'before' ? $beforeRacks : ($category === 'accessories' ? $accessoryRacks : $shoeRacks);
                        ?>
                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $currentRacks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rack): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <?php
                                $utilization = $rack->getUtilizationPercentage();
                                $rackStyle = $utilization >= 100 
                                    ? 'bg-accent-yellow text-gray-900 border-accent-yellow shadow-[0_10px_20px_-5px_rgba(255,194,50,0.5)]' 
                                    : ($utilization > 0 
                                        ? 'bg-primary-green text-white border-primary-green shadow-[0_10px_20px_-5px_rgba(34,175,133,0.3)]' 
                                        : 'bg-white text-primary-green border-gray-100');
                            ?>
                            <div @click="fetchRackDetails('<?php echo e($rack->rack_code); ?>')" 
                                 class="rack-item <?php echo e($rackStyle); ?> border-2 group relative">
                                <span class="text-lg font-black tracking-tighter"><?php echo e($rack->rack_code); ?></span>
                                <span class="text-[9px] font-bold opacity-60"><?php echo e($rack->current_count); ?>/<?php echo e($rack->capacity); ?></span>
                                
                                
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-5 w-44 p-4 bg-gray-900 rounded-2xl opacity-0 group-hover:opacity-100 transition-all pointer-events-none z-50 shadow-2xl transform scale-75 group-hover:scale-100">
                                    <p class="text-[10px] font-black text-accent-yellow uppercase mb-2 tracking-widest text-center"><?php echo e($rack->location); ?></p>
                                    <div class="flex justify-between items-center text-white px-1">
                                        <span class="text-[10px] font-bold opacity-60 uppercase">Load:</span>
                                        <span class="text-sm font-black"><?php echo e(number_format($utilization, 0)); ?>%</span>
                                    </div>
                                    <div class="w-full bg-white/10 h-1 rounded-full mt-2 overflow-hidden">
                                        <div class="bg-accent-yellow h-full" style="width: <?php echo e($utilization); ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </div>
            </section>

            
            <section id="storage-table-section" 
                     x-data="{ 
                        selectedIds: [], 
                        selectAll: false,
                        toggleAll() {
                            this.selectedIds = this.selectAll ? <?php echo e(Js::from(collect($storedItems->items())->pluck('id'))); ?> : [];
                        },
                        updateSelectAll() {
                            this.selectAll = this.selectedIds.length === <?php echo e(count($storedItems->items())); ?> && <?php echo e(count($storedItems->items())); ?> > 0;
                        }
                     }"
                     class="premium-card rounded-[3rem] overflow-hidden relative">
                
                <!-- Floating Action Bar -->
                <div x-show="selectedIds.length > 0" 
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="translate-y-full opacity-0"
                     x-transition:enter-end="translate-y-0 opacity-100"
                     x-transition:leave="transition ease-in duration-200 transform"
                     x-transition:leave-start="translate-y-0 opacity-100"
                     x-transition:leave-end="translate-y-full opacity-0"
                     class="fixed bottom-10 left-1/2 -translate-x-1/2 z-50 flex items-center gap-6 px-8 py-4 bg-gray-900/95 backdrop-blur-md text-white rounded-full shadow-[0_20px_50px_rgba(0,0,0,0.3)] border border-white/10">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-primary-green flex items-center justify-center font-black text-xs text-white" x-text="selectedIds.length"></span>
                        <span class="text-sm font-bold uppercase tracking-widest text-gray-300">Item Terpilih</span>
                    </div>
                    
                    <div class="w-px h-8 bg-white/10"></div>
                    
                    <div class="flex items-center gap-2">
                        <form action="<?php echo e(route('storage.bulk-retrieve')); ?>" method="POST" @submit.prevent="if(confirm('Ambil massal ' + selectedIds.length + ' item?')) $el.submit()">
                            <?php echo csrf_field(); ?>
                            <template x-for="id in selectedIds" :key="id">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                            <button type="submit" class="bg-primary-green hover:bg-emerald-600 px-6 py-2 rounded-full text-[10px] font-black uppercase tracking-tighter transition-all flex items-center gap-2 shadow-lg shadow-emerald-500/20">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path></svg>
                                Ambil Massal
                            </button>
                        </form>

                        <form action="<?php echo e(route('storage.bulk-destroy-selection')); ?>" method="POST" @submit.prevent="if(confirm('Hapus massal ' + selectedIds.length + ' data penugasan?')) $el.submit()">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <template x-for="id in selectedIds" :key="id">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                            <button type="submit" class="bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white px-6 py-2 rounded-full text-[10px] font-black uppercase tracking-tighter transition-all flex items-center gap-2 border border-red-500/20 shadow-lg shadow-red-500/10">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Hapus Data
                            </button>
                        </form>
                    </div>
                    
                    <button @click="selectedIds = []; selectAll = false" class="text-xs font-bold text-gray-400 hover:text-white uppercase tracking-widest">
                        Batal
                    </button>
                </div>

                <div class="px-12 py-10 flex flex-col md:flex-row justify-between items-center gap-8 bg-white border-b border-gray-50">
                    <div>
                        <h3 class="text-3xl font-black text-gray-900 tracking-tighter">DATA LOG ITEM</h3>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Riwayat Penyimpanan Real-Time</p>
                    </div>
                    <button class="px-8 py-4 bg-primary-green text-white rounded-2xl font-black text-xs hover:bg-emerald-600 transition-all shadow-xl shadow-emerald-100">
                        EXPORT DATA REKAP
                    </button>
                </div>
                
                <div class="p-4">
                    <table class="w-full border-separate border-spacing-y-4">
                        <thead>
                            <tr class="text-primary-green text-[10px] font-black uppercase tracking-[0.4em]">
                                <th class="px-8 pb-4 text-center w-10">
                                     <input type="checkbox" 
                                           x-model="selectAll" 
                                           @change="toggleAll()"
                                           class="w-4 h-4 text-primary-green bg-gray-100 border-gray-300 rounded focus:ring-primary-green focus:ring-2">
                                </th>
                                <th class="px-8 pb-4 text-left">ITEM ANALYSIS</th>
                                <th class="px-8 pb-4 text-left">CUSTOMER / OWNER</th>
                                <th class="px-8 pb-4 text-center">RACK POS</th>
                                <th class="px-8 pb-4 text-right whitespace-nowrap">ACTION PANEL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $storedItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <tr :class="selectedIds.includes(<?php echo e($item->id); ?>) ? 'bg-primary-green/5' : ''" class="bg-white hover:bg-gray-50/50 transition-all group border border-gray-100">
                                    <td class="px-8 py-7 rounded-l-3xl border-l border-y border-gray-100 text-center">
                                         <input type="checkbox" 
                                               :value="<?php echo e($item->id); ?>" 
                                               x-model="selectedIds"
                                               @change="updateSelectAll()"
                                               class="w-4 h-4 text-primary-green bg-gray-100 border-gray-300 rounded focus:ring-primary-green focus:ring-2">
                                    </td>
                                    <td class="px-8 py-7 border-y border-gray-100">
                                        <div class="flex items-center gap-5">
                                            <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center text-2xl group-hover:bg-primary-green group-hover:text-white transition-all transform group-hover:rotate-6">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->item_type === 'shoes'): ?> 👟 <?php else: ?> 📦 <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                            <div>
                                                <div class="text-xl font-black text-gray-900 tracking-tight leading-none mb-1"><?php echo e($item->workOrder?->spk_number ?? 'N/A'); ?></div>
                                                <div class="text-[10px] font-black text-primary-green uppercase tracking-widest">
                                                    <?php echo e($item->item_type === 'shoes' ? ($item->workOrder?->shoe_brand ?? 'SHOES') : 'ACCESSORIES'); ?>

                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-7 border-y border-gray-100">
                                        <div class="font-black text-gray-900 text-sm leading-none mb-1"><?php echo e($item->workOrder?->customer?->name ?? 'Unknown'); ?></div>
                                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter"><?php echo e($item->workOrder?->customer?->phone ?? '-'); ?></div>
                                    </td>
                                    <td class="px-8 py-7 border-y border-gray-100 text-center">
                                        <span class="inline-block px-5 py-2.5 rounded-2xl bg-white border-2 border-primary-green/20 text-primary-green font-black text-sm shadow-sm group-hover:border-primary-green/50 transition-colors">
                                            <?php echo e($item->rack_code); ?>

                                        </span>
                                    </td>
                                    <td class="px-8 py-7 rounded-r-3xl border-r border-y border-gray-100 text-right">
                                        <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all transform translate-x-10 group-hover:translate-x-0">
                                            <a href="<?php echo e(route('storage.label', $item->id)); ?>" target="_blank" class="p-4 bg-white text-primary-green border border-gray-100 rounded-2xl hover:bg-gray-900 hover:text-white transition-all shadow-xl">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                            </a>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manageStorage')): ?>
                                            <form action="<?php echo e(route('storage.retrieve', $item->id)); ?>" method="POST" class="inline">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" onclick="return confirm('Konfirmasi pengambilan item?')" class="p-4 bg-accent-yellow text-gray-900 rounded-2xl border-none hover:bg-yellow-400 transition-all shadow-xl font-black">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path></svg>
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <tr>
                                    <td colspan="4" class="py-32 text-center rounded-[3rem] bg-gray-50 border-4 border-dashed border-gray-200">
                                        <div class="text-6xl mb-6">📭</div>
                                        <h4 class="text-4xl font-black text-gray-300 uppercase tracking-tighter">DATA KOSONG</h4>
                                        <p class="text-gray-400 font-bold text-sm mt-2 tracking-widest uppercase">Inventory Level: 0</p>
                                    </td>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(method_exists($storedItems, 'links')): ?>
                    <div class="px-12 py-10 bg-white border-t border-gray-50">
                        <?php echo e($storedItems->links()); ?>

                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </section>
        </div>

        
        <div x-show="showRackModal" 
             class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             x-cloak>
            
            
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-2xl" @click="showRackModal = false"></div>

            <div class="relative w-full max-w-4xl bg-white shadow-[0_50px_100px_-20px_rgba(0,0,0,0.5)] rounded-[4rem] overflow-hidden border-[12px] border-white flex flex-col max-h-[90vh]">
                
                
                <div class="glass-header px-12 py-10 flex justify-between items-center shrink-0">
                    <div class="flex items-center gap-8">
                        <div class="w-24 h-24 bg-white rounded-3xl flex flex-col items-center justify-center shadow-2xl rotate-2 border-b-4 border-accent-yellow">
                            <span class="text-[10px] font-black text-primary-green opacity-40 uppercase tracking-widest mb-1">Unit</span>
                            <span class="text-5xl font-black text-primary-green leading-none" x-text="selectedRack"></span>
                        </div>
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="px-3 py-1 bg-accent-yellow text-gray-900 text-[10px] font-black rounded-lg uppercase tracking-widest shadow-lg shadow-yellow-200/40">Real-Time Data</span>
                                <span class="w-1.5 h-1.5 rounded-full bg-white opacity-40"></span>
                                <span class="text-white/60 text-[10px] font-black uppercase tracking-[0.2em]" x-text="'Sector: ' + '<?php echo e($category); ?>'"></span>
                            </div>
                            <h3 class="text-4xl font-black text-white uppercase tracking-tighter leading-none">ANALISIS RAK TERINTEGRASI</h3>
                        </div>
                    </div>
                    
                    
                    <div class="hidden md:flex items-center gap-4 bg-white/10 px-6 py-3 rounded-2xl border border-white/20 backdrop-blur-md">
                        <div class="text-right">
                            <p class="text-[10px] font-black text-white/60 uppercase tracking-widest leading-none mb-1">Total Stored</p>
                            <p class="text-2xl font-black text-accent-yellow leading-none tabular-nums" x-text="rackItems.length"></p>
                        </div>
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                    </div>

                    <button @click="showRackModal = false" class="bg-black/10 hover:bg-white text-white hover:text-primary-green p-4 rounded-full transition-all border border-white/20 backdrop-blur-md ml-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                
                <div class="p-12 overflow-y-auto bg-gray-50/20 custom-scrollbar">
                    
                    <template x-if="isLoading">
                        <div class="flex flex-col items-center justify-center py-24 space-y-8">
                            <div class="relative">
                                <div class="w-24 h-24 border-8 border-primary-green/5 border-t-primary-green rounded-full animate-spin"></div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="w-5 h-5 bg-accent-yellow rounded-full animate-ping"></div>
                                </div>
                            </div>
                            <p class="text-xs font-black text-primary-green animate-pulse uppercase tracking-[0.5em]">Synchronizing Master Data...</p>
                        </div>
                    </template>

                    
                    <template x-if="!isLoading && rackItems.length > 0">
                        <div class="grid grid-cols-1 gap-8">
                            <template x-for="item in rackItems" :key="item.id">
                                <div class="bg-white border-2 border-gray-100 rounded-[3rem] p-10 hover:border-primary-green hover:shadow-2xl transition-all group flex flex-col md:flex-row items-center gap-10">
                                    <div class="flex-1 w-full">
                                        
                                        <div class="flex items-start justify-between mb-8">
                                            <div>
                                                <div class="flex items-center gap-4 mb-2">
                                                    <span class="text-5xl font-black text-gray-900 tracking-tighter group-hover:text-primary-green transition-colors leading-none" x-text="item.spk_number"></span>
                                                    <div class="px-5 py-2 bg-gray-900 text-white text-[10px] font-black rounded-full uppercase tracking-widest shadow-xl whitespace-nowrap" x-text="'SINCE ' + item.stored_at"></div>
                                                </div>
                                                <p class="text-xs font-bold text-gray-400 uppercase tracking-[0.1em]">Verification Complete • Secure Storage</p>
                                            </div>
                                            <div class="hidden sm:block">
                                                <svg class="w-12 h-12 text-primary-green opacity-10 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                            <div class="p-6 bg-gray-50 rounded-3xl border border-gray-100 flex items-center gap-5 group-hover:bg-white transition-colors">
                                                <div class="text-4xl">👟</div>
                                                <div>
                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Item Specification</p>
                                                    <p class="text-base font-black text-gray-800" x-text="item.item_info"></p>
                                                </div>
                                            </div>
                                            
                                            <template x-if="item.accessories">
                                                <div class="p-6 bg-primary-green/5 border-2 border-primary-green/10 rounded-3xl flex items-center gap-5">
                                                    <div class="text-4xl">📦</div>
                                                    <div>
                                                        <p class="text-[9px] font-black text-primary-green uppercase tracking-widest mb-1">Verified Accessories</p>
                                                        <p class="text-[11px] font-black text-gray-600 leading-snug" x-text="Object.values(item.accessories).filter(v => v && v !== '-').join(' • ') || 'Standard Unit Only'"></p>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class="flex flex-row md:flex-col gap-4 w-full md:w-28">
                                        <a :href="`/warehouse/${item.id}/label`" target="_blank" class="flex-1 md:flex-none p-6 bg-white text-gray-400 border-2 border-gray-100 rounded-3xl hover:bg-gray-900 hover:text-white hover:border-gray-900 transition-all shadow-xl flex items-center justify-center">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        </a>
                                        <form :action="`/warehouse/${item.id}/retrieve`" method="POST" class="flex-1 md:flex-none">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="w-full p-6 bg-accent-yellow text-gray-900 rounded-3xl hover:bg-yellow-400 transition-all shadow-[0_15px_30px_-5px_rgba(255,194,50,0.5)] flex items-center justify-center">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    
                    <template x-if="!isLoading && rackItems.length === 0">
                        <div class="flex flex-col items-center justify-center py-20 text-center rounded-[4rem] border-4 border-dashed border-gray-100 bg-gray-50/30">
                            <span class="text-8xl mb-8 opacity-40 grayscale">🗳️</span>
                            <h4 class="text-4xl font-black text-gray-300 uppercase tracking-tighter mb-2">CAPACITY READY</h4>
                            <p class="text-primary-green font-black text-[10px] tracking-[0.4em] uppercase">Sector clear • Waiting for deployment</p>
                        </div>
                    </template>
                </div>

                
                <div class="px-14 py-10 bg-white border-t border-gray-50 flex justify-between items-center shrink-0">
                    <div class="text-[10px] font-black text-gray-300 uppercase tracking-widest">
                        System Integrity Checked
                    </div>
                    <button @click="showRackModal = false" class="px-14 py-5 bg-gray-900 text-white font-black rounded-3x-large hover:bg-black transition-all shadow-2xl uppercase tracking-[0.3em] text-[10px] rounded-3xl">
                        RETURN TO HUB
                    </button>
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
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\storage\index.blade.php ENDPATH**/ ?>