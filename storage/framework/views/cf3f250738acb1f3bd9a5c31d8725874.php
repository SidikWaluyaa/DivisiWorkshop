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

    <div class="min-h-screen bg-[#F9FAFB] dark:bg-gray-900">
        
        <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4">
            <div class="max-w-[1600px] mx-auto">
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3 text-xs font-medium text-gray-400">
                        <li>Master Data</li>
                        <li>
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        </li>
                        <li class="text-teal-600 font-bold uppercase tracking-wider">Material</li>
                    </ol>
                </nav>
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-black text-gray-900 dark:text-white flex items-center gap-3">
                            Master Data Material
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-teal-50 text-teal-600 border border-teal-100 uppercase tracking-widest">
                                TOTAL MATERIAL: <?php echo e($totalCount); ?>

                            </span>
                        </h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-[1600px] mx-auto p-6" 
             x-data="{ 
                selected: [], 
                currentTab: '<?php echo e($activeTab); ?>',
                selectAllMatching: false,
                lastSelectedIndex: null,
                totalResults: <?php echo e($activeTab === 'upper' ? $upperMaterials->total() : $solMaterials->total()); ?>,

                toggleRow(id, index, event) {
                    if (event.shiftKey && this.lastSelectedIndex !== null) {
                        const type = this.currentTab === 'upper' ? 'upper' : 'sol';
                        const checkboxes = Array.from(document.querySelectorAll('.' + type + '-checkbox'));
                        const start = Math.min(this.lastSelectedIndex, index);
                        const end = Math.max(this.lastSelectedIndex, index);
                        
                        checkboxes.slice(start, end + 1).forEach(cb => {
                            const val = parseInt(cb.value);
                            if (!this.selected.includes(val)) this.selected.push(val);
                        });
                    } else {
                        if (this.selected.includes(id)) {
                            this.selected = this.selected.filter(i => i !== id);
                        } else {
                            this.selected.push(id);
                        }
                    }
                    this.lastSelectedIndex = index;
                    this.selectAllMatching = false;
                },

                selectAllOnPage() {
                    const type = this.currentTab === 'upper' ? 'upper' : 'sol';
                    const checkboxes = document.querySelectorAll('.' + type + '-checkbox');
                    const allOnPage = Array.from(checkboxes).map(cb => parseInt(cb.value));
                    
                    if (allOnPage.every(id => this.selected.includes(id))) {
                        this.selected = this.selected.filter(id => !allOnPage.includes(id));
                    } else {
                        allOnPage.forEach(id => {
                            if (!this.selected.includes(id)) this.selected.push(id);
                        });
                    }
                    this.selectAllMatching = false;
                },

                isAllOnPageSelected() {
                    const type = this.currentTab === 'upper' ? 'upper' : 'sol';
                    const checkboxes = document.querySelectorAll('.' + type + '-checkbox');
                    if (checkboxes.length === 0) return false;
                    return Array.from(checkboxes).every(cb => this.selected.includes(parseInt(cb.value)));
                }
             }">
            
            
            <template x-if="isAllOnPageSelected() && totalResults > selected.length">
                <div x-transition class="bg-teal-600 text-white px-6 py-3 rounded-xl mb-6 flex justify-between items-center shadow-lg shadow-teal-900/10">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-teal-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-sm font-bold">
                            Semua <span x-text="selected.length"></span> material di halaman ini terpilih.
                        </p>
                    </div>
                    <button type="button" @click="selectAllMatching = true; selected = Array(totalResults).fill(0)" 
                            class="px-4 py-1.5 bg-white/20 hover:bg-white text-white hover:text-teal-700 rounded-lg text-xs font-black uppercase tracking-widest transition-all">
                        Pilih semua <span x-text="totalResults"></span> material terfilter
                    </button>
                </div>
            </template>

            <template x-if="selectAllMatching">
                <div x-transition class="bg-gray-900 text-white px-6 py-3 rounded-xl mb-6 flex justify-between items-center shadow-lg">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-sm font-bold">
                            Semua <span x-text="totalResults"></span> material dalam filter ini telah terpilih.
                        </p>
                    </div>
                    <button type="button" @click="selectAllMatching = false; selected = []" 
                            class="px-4 py-1.5 bg-white/10 hover:bg-white/20 rounded-lg text-xs font-black uppercase tracking-widest transition-all">
                        Batalkan Seleksi
                    </button>
                </div>
            </template>

            
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
                <div class="flex flex-col xl:flex-row justify-between items-center gap-4">
                    <form action="<?php echo e(route('admin.materials.index')); ?>" method="GET" class="flex flex-col md:flex-row items-center gap-3 w-full xl:w-auto">
                        <input type="hidden" name="tab" :value="currentTab">
                        <div class="relative w-full md:w-80 group">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400 group-focus-within:text-teal-500 transition-colors" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                                class="block w-full pl-10 pr-4 py-2.5 text-sm bg-gray-50 border-gray-200 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                                placeholder="Cari material...">
                        </div>

                        <div class="flex items-center gap-2 w-full md:w-auto">
                            <select name="status" onchange="this.form.submit()" 
                                class="w-full md:w-44 py-2.5 text-sm bg-gray-50 border-gray-200 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="all">Semua Status</option>
                                <option value="Ready" <?php echo e(request('status') == 'Ready' ? 'selected' : ''); ?>>Ready</option>
                                <option value="Belanja" <?php echo e(request('status') == 'Belanja' ? 'selected' : ''); ?>>Belanja</option>
                                <option value="Followup" <?php echo e(request('status') == 'Followup' ? 'selected' : ''); ?>>Followup</option>
                                <option value="Reject" <?php echo e(request('status') == 'Reject' ? 'selected' : ''); ?>>Reject</option>
                                <option value="Retur" <?php echo e(request('status') == 'Retur' ? 'selected' : ''); ?>>Retur</option>
                            </select>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request('tab') == 'sol' || (!request('tab') && $activeTab == 'sol')): ?>
                            <select name="sub_category" onchange="this.form.submit()"
                                class="w-full md:w-44 py-2.5 text-sm bg-gray-50 border-gray-200 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="all">Semua Kategori</option>
                                <option value="Sol Potong" <?php echo e(request('sub_category') == 'Sol Potong' ? 'selected' : ''); ?>>Sol Potong</option>
                                <option value="Sol Jadi" <?php echo e(request('sub_category') == 'Sol Jadi' ? 'selected' : ''); ?>>Sol Jadi</option>
                                <option value="Foxing" <?php echo e(request('sub_category') == 'Foxing' ? 'selected' : ''); ?>>Foxing</option>
                                <option value="Vibram" <?php echo e(request('sub_category') == 'Vibram' ? 'selected' : ''); ?>>Vibram</option>
                            </select>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </form>

                    <div class="flex items-center gap-3 w-full xl:w-auto justify-end">
                        <div class="flex items-center bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl p-1 shadow-sm">
                            <a href="<?php echo e(route('admin.materials.export-pdf')); ?>" class="p-2.5 hover:bg-white dark:hover:bg-gray-600 rounded-lg text-gray-500 hover:text-teal-600 transition-all" title="Export PDF">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            </a>
                            <a href="<?php echo e(route('admin.materials.export-excel')); ?>" class="p-2.5 hover:bg-white dark:hover:bg-gray-600 rounded-lg text-gray-500 hover:text-green-600 transition-all" title="Export Excel">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </a>
                            <button x-on:click="$dispatch('open-modal', 'import-material-modal')" class="p-2.5 hover:bg-white dark:hover:bg-gray-600 rounded-lg text-gray-500 hover:text-blue-600 transition-all" title="Import Data">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            </button>
                            <a href="<?php echo e(route('admin.materials.template')); ?>" class="p-2.5 hover:bg-white dark:hover:bg-gray-600 rounded-lg text-gray-500 hover:text-yellow-600 transition-all" title="Download Template">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </a>
                        </div>
                        <button x-on:click.prevent="$dispatch('open-modal', 'create-material-modal')" 
                                class="px-6 py-3 bg-[#0F766E] hover:bg-[#0D635C] text-white rounded-xl shadow-lg shadow-teal-900/10 transition-all transform hover:-translate-y-0.5 flex items-center gap-2 font-black text-sm whitespace-nowrap">
                            <span class="text-xl leading-none">+</span>
                            Tambah Material
                        </button>
                    </div>
                </div>
            </div>

            
            <div class="mb-6" x-cloak>
                <div class="flex gap-8 border-b border-gray-200 dark:border-gray-700">
                    <a href="<?php echo e(route('admin.materials.index', ['tab' => 'upper'] + request()->except('tab'))); ?>" 
                       @click="currentTab = 'upper'"
                       class="pb-4 px-2 text-sm font-bold border-b-2 transition-all"
                       :class="currentTab === 'upper' ? 'border-teal-500 text-teal-600' : 'border-transparent text-gray-500 hover:text-gray-700'">
                       Material Upper
                    </a>
                    <a href="<?php echo e(route('admin.materials.index', ['tab' => 'sol'] + request()->except('tab'))); ?>" 
                       @click="currentTab = 'sol'"
                       class="pb-4 px-2 text-sm font-bold border-b-2 transition-all"
                       :class="currentTab === 'sol' ? 'border-teal-500 text-teal-600' : 'border-transparent text-gray-500 hover:text-gray-700'">
                       Material Sol
                    </a>
                </div>
            </div>

            
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                
                <div x-show="currentTab === 'upper'">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50/80 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-4 w-12 text-center">
                                        <input type="checkbox" @click="selectAllOnPage" :checked="isAllOnPageSelected" class="rounded border-gray-300 text-teal-600 shadow-sm focus:ring-teal-500">
                                    </th>
                                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Material</th>
                                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Stock</th>
                                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Harga Beli</th>
                                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Status & PIC</th>
                                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $upperMaterials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <tr @click="toggleRow(<?php echo e($material->id); ?>, <?php echo e($index); ?>, $event)" 
                                    class="cursor-pointer transition-all duration-200"
                                    :class="selected.includes(<?php echo e($material->id); ?>) ? 'bg-teal-50/80 dark:bg-teal-900/20' : 'hover:bg-gray-50/50 dark:hover:bg-gray-700/30'">
                                    <td class="px-6 py-5 text-center" @click.stop>
                                        <input type="checkbox" value="<?php echo e($material->id); ?>" x-model="selected" class="upper-checkbox rounded border-gray-300 text-teal-600 shadow-sm focus:ring-teal-500">
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="font-bold text-gray-900 dark:text-white mb-0.5"><?php echo e($material->name); ?></div>
                                        <div class="text-[10px] text-gray-400 font-medium">Min: <?php echo e($material->min_stock); ?> <?php echo e($material->unit); ?></div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="text-sm font-black <?php echo e($material->stock <= $material->min_stock ? 'text-red-500' : 'text-gray-900 dark:text-gray-100'); ?>">
                                            <?php echo e($material->stock); ?> <span class="text-[10px] font-bold text-gray-400 uppercase ml-0.5"><?php echo e($material->unit); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-right whitespace-nowrap">
                                        <div class="text-sm font-black text-gray-900 dark:text-gray-100 italic">Rp <?php echo e(number_format($material->price, 0, ',', '.')); ?></div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-3">
                                            <?php
                                                $statusClass = match($material->status) {
                                                    'Ready' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                                                    'Belanja', 'Followup' => 'bg-[#FFFBEB] text-[#D97706] border-[#FEF3C7]',
                                                    'Reject', 'Retur' => 'bg-red-50 text-red-600 border-red-200',
                                                    default => 'bg-gray-50 text-gray-600 border-gray-200',
                                                };
                                                // Override for low stock
                                                if ($material->stock <= $material->min_stock) {
                                                    $statusClass = 'bg-[#FFFBEB] text-[#D97706] border-[#FEF3C7] shadow-sm shadow-[#D97706]/5';
                                                    $displayText = 'LOW STOCK';
                                                } else {
                                                    $displayText = Str::upper($material->status);
                                                }
                                            ?>
                                            <span class="px-2.5 py-1 text-[10px] font-black rounded border <?php echo e($statusClass); ?> tracking-widest">
                                                <?php echo e($displayText); ?>

                                            </span>
                                            
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($material->pic): ?>
                                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-teal-50 dark:bg-gray-700 flex items-center justify-center text-[#0F766E] font-black text-[10px] border border-teal-100 dark:border-gray-600 shadow-inner" title="<?php echo e($material->pic->name); ?>">
                                                <?php echo e(collect(explode(' ', $material->pic->name))->map(fn($n) => Str::substr($n, 0, 1))->take(2)->implode('')); ?>

                                            </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-right space-x-1" @click.stop>
                                        <button x-on:click.prevent="$dispatch('open-modal', 'audit-material-<?php echo e($material->id); ?>')" 
                                                class="p-2 text-gray-400 hover:text-blue-600 transition-colors" title="Audit Stok">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                        </button>
                                        <button x-on:click.prevent="$dispatch('open-modal', 'edit-material-<?php echo e($material->id); ?>')" 
                                                class="p-2 text-gray-400 hover:text-teal-600 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <form action="<?php echo e(route('admin.materials.destroy', $material)); ?>" method="POST" class="inline" onsubmit="return confirm('Hapus material ini?')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500 italic">Tidak ada material upper ditemukan.</td>
                                </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-5 bg-gray-50/50 dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700">
                        <?php echo e($upperMaterials->links()); ?>

                    </div>
                </div>

                
                <div x-show="currentTab === 'sol'">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50/80 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-4 w-12 text-center">
                                        <input type="checkbox" @click="selectAllOnPage" :checked="isAllOnPageSelected" class="rounded border-gray-300 text-teal-600 shadow-sm focus:ring-teal-500">
                                    </th>
                                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Material</th>
                                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Stock</th>
                                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Harga Beli</th>
                                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Status & PIC</th>
                                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $solMaterials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <tr @click="toggleRow(<?php echo e($material->id); ?>, <?php echo e($index); ?>, $event)" 
                                    class="cursor-pointer transition-all duration-200"
                                    :class="selected.includes(<?php echo e($material->id); ?>) ? 'bg-teal-50/80 dark:bg-teal-900/20' : 'hover:bg-gray-50/50 dark:hover:bg-gray-700/30'">
                                    <td class="px-6 py-5 text-center" @click.stop>
                                        <input type="checkbox" value="<?php echo e($material->id); ?>" x-model="selected" class="sol-checkbox rounded border-gray-300 text-teal-600 shadow-sm focus:ring-teal-500">
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="font-bold text-gray-900 dark:text-white mb-0.5"><?php echo e($material->name); ?></div>
                                        <div class="flex items-center gap-2">
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-100"><?php echo e($material->sub_category); ?></span>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($material->size): ?>
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-700"><?php echo e($material->size); ?></span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <span class="text-[10px] text-gray-400">Min: <?php echo e($material->min_stock); ?> <?php echo e($material->unit); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="text-sm font-black <?php echo e($material->stock <= $material->min_stock ? 'text-red-500' : 'text-gray-900 dark:text-gray-100'); ?>">
                                            <?php echo e($material->stock); ?> <span class="text-[10px] font-bold text-gray-400 uppercase ml-0.5"><?php echo e($material->unit); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-right whitespace-nowrap">
                                        <div class="text-sm font-black text-gray-900 dark:text-gray-100 italic">Rp <?php echo e(number_format($material->price, 0, ',', '.')); ?></div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-3">
                                            <?php
                                                $statusClass = match($material->status) {
                                                    'Ready' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                                                    'Belanja', 'Followup' => 'bg-[#FFFBEB] text-[#D97706] border-[#FEF3C7]',
                                                    'Reject', 'Retur' => 'bg-red-50 text-red-600 border-red-200',
                                                    default => 'bg-gray-50 text-gray-600 border-gray-200',
                                                };
                                                if ($material->stock <= $material->min_stock) {
                                                    $statusClass = 'bg-[#FFFBEB] text-[#D97706] border-[#FEF3C7] shadow-sm shadow-[#D97706]/5';
                                                    $displayText = 'LOW STOCK';
                                                } else {
                                                    $displayText = Str::upper($material->status);
                                                }
                                            ?>
                                            <span class="px-2.5 py-1 text-[10px] font-black rounded border <?php echo e($statusClass); ?> tracking-widest">
                                                <?php echo e($displayText); ?>

                                            </span>
                                            
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($material->pic): ?>
                                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-teal-50 dark:bg-gray-700 flex items-center justify-center text-[#0F766E] font-black text-[10px] border border-teal-100 dark:border-gray-600 shadow-inner" title="<?php echo e($material->pic->name); ?>">
                                                <?php echo e(collect(explode(' ', $material->pic->name))->map(fn($n) => Str::substr($n, 0, 1))->take(2)->implode('')); ?>

                                            </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-right space-x-1" @click.stop>
                                        <button x-on:click.prevent="$dispatch('open-modal', 'audit-material-<?php echo e($material->id); ?>')" 
                                                class="p-2 text-gray-400 hover:text-blue-600 transition-colors" title="Audit Stok">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                        </button>
                                        <button x-on:click.prevent="$dispatch('open-modal', 'edit-material-<?php echo e($material->id); ?>')" 
                                                class="p-2 text-gray-400 hover:text-teal-600 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <form action="<?php echo e(route('admin.materials.destroy', $material)); ?>" method="POST" class="inline" onsubmit="return confirm('Hapus material ini?')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500 italic">Tidak ada material sol ditemukan.</td>
                                </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-5 bg-gray-50/50 dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700">
                        <?php echo e($solMaterials->links()); ?>

                    </div>
                </div>
            </div>

            <!-- Floating Command Center -->
            <div x-show="selected.length > 0" 
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="translate-y-full opacity-0"
                 x-transition:enter-end="translate-y-0 opacity-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="translate-y-0 opacity-100"
                 x-transition:leave-end="translate-y-full opacity-0"
                 class="fixed bottom-10 left-1/2 -translate-x-1/2 z-50 flex items-center gap-6 px-8 py-4 bg-gray-900/95 backdrop-blur-md text-white rounded-full shadow-[0_20px_50px_rgba(0,0,0,0.3)] border border-white/10">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full bg-teal-500 flex items-center justify-center font-black text-xs text-white" x-text="selectAllMatching ? totalResults : selected.length"></span>
                    <span class="text-xs font-bold uppercase tracking-widest text-gray-300">Material Terpilih</span>
                </div>
                
                <div class="w-px h-8 bg-white/10"></div>
                
                <div class="flex items-center gap-4">
                    <form action="<?php echo e(route('admin.materials.bulk-destroy', request()->query())); ?>" method="POST" @submit.prevent="if(confirm(selectAllMatching ? 'PERINGATAN: Hapus SEMUA ' + totalResults + ' material terfilter di SEMUA HALAMAN?' : 'Hapus ' + selected.length + ' material terpilih?')) $el.submit()">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <template x-if="selectAllMatching">
                            <input type="hidden" name="select_all_matching" value="1">
                        </template>
                        <template x-if="!selectAllMatching">
                            <template x-for="id in selected" :key="id">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                        </template>
                        
                        <button type="submit" class="bg-red-500 hover:bg-red-600 px-6 py-2.5 rounded-full text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-2 shadow-lg shadow-red-500/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Hapus Massal
                        </button>
                    </form>

                    <button @click="selected = []; selectAllMatching = false" class="text-[10px] font-black text-gray-400 hover:text-white uppercase tracking-widest transition-colors px-2">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    
    <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['name' => 'create-material-modal','show' => $errors->any() && old('form_type') === 'create_material','focusable' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'create-material-modal','show' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->any() && old('form_type') === 'create_material'),'focusable' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <form method="POST" action="<?php echo e(route('admin.materials.store')); ?>" class="p-0">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="form_type" value="create_material">
            
            
            <div class="px-8 py-6 border-b border-gray-100 relative">
                <h2 class="text-xl font-black text-gray-900 dark:text-white">Tambah Material Baru</h2>
                <p class="text-xs text-gray-400 mt-1 font-medium">Lengkapi informasi material untuk inventori pusat</p>
                <button type="button" x-on:click="$dispatch('close')" class="absolute top-6 right-8 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            <div class="p-8 space-y-8" x-data="{ type: '' }">
                
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-5 bg-[#0F766E] rounded-full"></div>
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-[#0F766E]">Informasi Utama</h3>
                    </div>

                    <div class="grid grid-cols-2 gap-x-6 gap-y-5">
                        <div class="col-span-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Nama Material</label>
                            <input type="text" name="name" required placeholder="Contoh: Plat Besi Galvanis 2mm"
                                class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Tipe</label>
                            <select name="type" x-model="type" class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold p-3">
                                <option value="" disabled selected>Pilih Tipe</option>
                                <option value="Material Upper">Material Upper</option>
                                <option value="Material Sol">Material Sol</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Kategori Material</label>
                            <select name="category" class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold p-3">
                                <option value="" selected>Tanpa Kategori (Opsional)</option>
                                <option value="PRODUCTION">Produksi (Cek Stok)</option>
                                <option value="SHOPPING">Belanja (Budget)</option>
                            </select>
                            <p class="mt-1.5 text-[10px] text-gray-400 italic leading-relaxed">Kategori menentukan lokasi penyimpanan di zona gudang A atau B.</p>
                        </div>

                        <div x-show="type === 'Material Sol'" x-cloak>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Kategori Sol</label>
                            <select name="sub_category" class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold">
                                <option value="" selected>Tanpa Sub Kategori</option>
                                <option value="Sol Potong">Sol Potong</option>
                                <option value="Sol Jadi">Sol Jadi</option>
                                <option value="Foxing">Foxing</option>
                                <option value="Vibram">Vibram</option>
                            </select>
                        </div>

                        <div x-show="type === 'Material Sol'" x-cloak>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Size <span class="text-[8px] opacity-50 lowercase tracking-normal font-medium">(Opsional)</span></label>
                            <input type="text" name="size" placeholder="Contoh: 40, 41, M"
                                class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Stock</label>
                            <input type="number" name="stock" required placeholder="0"
                                class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Unit</label>
                            <input type="text" name="unit" required placeholder="Kg, Pcs, Liter..."
                                class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Harga per Unit</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none border-r border-gray-100 pr-3">
                                    <span class="text-[10px] font-black text-gray-400 group-focus-within:text-teal-500 transition-colors uppercase tracking-widest">IDR</span>
                                </div>
                                <input type="number" name="price" required step="0.01" placeholder="0.00"
                                    class="w-full pl-16 pr-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Minimal Stock</label>
                            <input type="number" name="min_stock" required placeholder="Batas aman stok"
                                class="w-full px-4 py-3 text-sm bg-gray-50 border-red-100 rounded-xl focus:ring-4 focus:ring-red-500/10 focus:border-red-400 transition-all font-semibold">
                        </div>
                    </div>
                </div>

                
                <div class="space-y-6 pt-4">
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-5 bg-[#C2410C] rounded-full"></div>
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-[#C2410C]">Administrasi & PIC</h3>
                    </div>

                    <div class="grid grid-cols-2 gap-x-6 gap-y-5">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Status</label>
                            <select name="status" class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold p-3">
                                <option value="Ready">Aktif / Ready</option>
                                <option value="Belanja">Belanja</option>
                                <option value="Followup">Followup</option>
                                <option value="Reject">Reject</option>
                                <option value="Retur">Retur</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">PIC Material <span class="text-[8px] opacity-50 lowercase tracking-normal font-medium">(Opsional)</span></label>
                            <select name="pic_user_id" class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold p-3">
                                <option value="">Pilih Penanggung Jawab</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $pics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <option value="<?php echo e($pic->id); ?>"><?php echo e($pic->name); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="px-8 py-6 bg-gray-50/80 dark:bg-gray-700/50 flex justify-end items-center gap-6 border-t border-gray-100">
                <button type="button" x-on:click="$dispatch('close')" class="text-sm font-black text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-widest">Batal</button>
                <button type="submit" class="px-8 py-3 bg-[#0F766E] hover:bg-[#0D635C] text-white rounded-xl shadow-lg shadow-teal-900/10 transition-all transform hover:-translate-y-0.5 font-black text-sm uppercase tracking-widest">
                    Simpan Material
                </button>
            </div>
        </form>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $attributes = $__attributesOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__attributesOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $component = $__componentOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__componentOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
    <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['name' => 'edit-material-'.e($material->id).'','show' => $errors->any() && old('form_type') === 'edit_material_' . $material->id,'focusable' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'edit-material-'.e($material->id).'','show' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->any() && old('form_type') === 'edit_material_' . $material->id),'focusable' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <form method="POST" action="<?php echo e(route('admin.materials.update', $material)); ?>" class="p-0">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
            <input type="hidden" name="form_type" value="edit_material_<?php echo e($material->id); ?>">

            
            <div class="px-8 py-6 border-b border-gray-100 relative">
                <h2 class="text-xl font-black text-gray-900 dark:text-white">Edit: <?php echo e($material->name); ?></h2>
                <p class="text-xs text-gray-400 mt-1 font-medium">Perbarui informasi material inventori</p>
                <button type="button" x-on:click="$dispatch('close')" class="absolute top-6 right-8 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="p-8 space-y-8" x-data="{ type: '<?php echo e($material->type); ?>' }">
                
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-5 bg-[#0F766E] rounded-full"></div>
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-[#0F766E]">Informasi Utama</h3>
                    </div>

                    <div class="grid grid-cols-2 gap-x-6 gap-y-5">
                        <div class="col-span-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Nama Material</label>
                            <input type="text" name="name" value="<?php echo e($material->name); ?>" required
                                class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Tipe</label>
                            <select name="type" x-model="type" class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold p-3">
                                <option value="Material Upper" <?php echo e($material->type == 'Material Upper' ? 'selected' : ''); ?>>Material Upper</option>
                                <option value="Material Sol" <?php echo e($material->type == 'Material Sol' ? 'selected' : ''); ?>>Material Sol</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Kategori Material</label>
                            <select name="category" class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold p-3">
                                <option value="" <?php echo e(is_null($material->category) ? 'selected' : ''); ?>>Tanpa Kategori (Opsional)</option>
                                <option value="PRODUCTION" <?php echo e($material->category == 'PRODUCTION' ? 'selected' : ''); ?>>Produksi (Cek Stok)</option>
                                <option value="SHOPPING" <?php echo e($material->category == 'SHOPPING' ? 'selected' : ''); ?>>Belanja (Budget)</option>
                            </select>
                        </div>

                        <div x-show="type === 'Material Sol'" x-cloak>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Kategori Sol</label>
                            <select name="sub_category" class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold">
                                <option value="" <?php echo e(is_null($material->sub_category) ? 'selected' : ''); ?>>Tanpa Sub Kategori</option>
                                <option value="Sol Potong" <?php echo e($material->sub_category == 'Sol Potong' ? 'selected' : ''); ?>>Sol Potong</option>
                                <option value="Sol Jadi" <?php echo e($material->sub_category == 'Sol Jadi' ? 'selected' : ''); ?>>Sol Jadi</option>
                                <option value="Foxing" <?php echo e($material->sub_category == 'Foxing' ? 'selected' : ''); ?>>Foxing</option>
                                <option value="Vibram" <?php echo e($material->sub_category == 'Vibram' ? 'selected' : ''); ?>>Vibram</option>
                            </select>
                        </div>

                        <div x-show="type === 'Material Sol'" x-cloak>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Size <span class="text-[8px] opacity-50 lowercase tracking-normal font-medium">(Opsional)</span></label>
                            <input type="text" name="size" value="<?php echo e($material->size); ?>"
                                class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Stock</label>
                            <input type="number" name="stock" value="<?php echo e($material->stock); ?>" required
                                class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Unit</label>
                            <input type="text" name="unit" value="<?php echo e($material->unit); ?>" required
                                class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Harga per Unit</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none border-r border-gray-100 pr-3">
                                    <span class="text-[10px] font-black text-gray-400 group-focus-within:text-teal-500 transition-colors uppercase tracking-widest">IDR</span>
                                </div>
                                <input type="number" name="price" value="<?php echo e($material->price); ?>" required step="0.01"
                                    class="w-full pl-16 pr-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Minimal Stock</label>
                            <input type="number" name="min_stock" value="<?php echo e($material->min_stock); ?>" required
                                class="w-full px-4 py-3 text-sm bg-gray-50 border-red-100 rounded-xl focus:ring-4 focus:ring-red-500/10 focus:border-red-400 transition-all font-semibold">
                        </div>
                    </div>
                </div>

                
                <div class="space-y-6 pt-4">
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-5 bg-[#C2410C] rounded-full"></div>
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-[#C2410C]">Administrasi & PIC</h3>
                    </div>

                    <div class="grid grid-cols-2 gap-x-6 gap-y-5">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Status</label>
                            <select name="status" class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold p-3">
                                <option value="Ready" <?php echo e($material->status == 'Ready' ? 'selected' : ''); ?>>Aktif / Ready</option>
                                <option value="Belanja" <?php echo e($material->status == 'Belanja' ? 'selected' : ''); ?>>Belanja</option>
                                <option value="Followup" <?php echo e($material->status == 'Followup' ? 'selected' : ''); ?>>Followup</option>
                                <option value="Reject" <?php echo e($material->status == 'Reject' ? 'selected' : ''); ?>>Reject</option>
                                <option value="Retur" <?php echo e($material->status == 'Retur' ? 'selected' : ''); ?>>Retur</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">PIC Material <span class="text-[8px] opacity-50 lowercase tracking-normal font-medium">(Opsional)</span></label>
                            <select name="pic_user_id" class="w-full px-4 py-3 text-sm bg-gray-50 border-gray-100 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-semibold p-3">
                                <option value="">Pilih Penanggung Jawab</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $pics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <option value="<?php echo e($p->id); ?>" <?php echo e($material->pic_user_id == $p->id ? 'selected' : ''); ?>><?php echo e($p->name); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="px-8 py-6 bg-gray-50/80 dark:bg-gray-700/50 flex justify-end items-center gap-6 border-t border-gray-100">
                <button type="button" x-on:click="$dispatch('close')" class="text-sm font-black text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-widest">Batal</button>
                <button type="submit" class="px-8 py-3 bg-[#0F766E] hover:bg-[#0D635C] text-white rounded-xl shadow-lg shadow-teal-900/10 transition-all transform hover:-translate-y-0.5 font-black text-sm uppercase tracking-widest">
                    Simpan Perubahan
                </button>
            </div>
        </form>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $attributes = $__attributesOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__attributesOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $component = $__componentOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__componentOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

    
    <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['name' => 'import-material-modal','focusable' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'import-material-modal','focusable' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <form method="POST" action="<?php echo e(route('admin.materials.import')); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div class="px-8 py-6 border-b border-gray-100 relative">
                <h2 class="text-xl font-black text-gray-900 dark:text-white">Import Material</h2>
                <p class="text-xs text-gray-400 mt-1 font-medium">Unggah berkas excel untuk pembaruan massal</p>
                <button type="button" x-on:click="$dispatch('close')" class="absolute top-6 right-8 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="p-8">
                <input type="file" name="file" required class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-black file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 transition-all">
            </div>
            <div class="px-8 py-6 bg-gray-50 dark:bg-gray-800 flex justify-end items-center gap-6 border-t border-gray-100">
                <button type="button" x-on:click="$dispatch('close')" class="text-sm font-black text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-widest">Batal</button>
                <button type="submit" class="px-8 py-3 bg-[#0F766E] hover:bg-[#0D635C] text-white rounded-xl shadow-lg shadow-teal-900/10 transition-all transform hover:-translate-y-0.5 font-black text-sm uppercase tracking-widest">Unggah Berkas</button>
            </div>
        </form>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $attributes = $__attributesOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__attributesOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $component = $__componentOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__componentOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
    <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['name' => 'audit-material-'.e($material->id).'','focusable' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'audit-material-'.e($material->id).'','focusable' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <form method="POST" action="<?php echo e(route('admin.materials.reconcile', $material)); ?>" class="p-0">
            <?php echo csrf_field(); ?>
            
            
            <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-700 relative">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-lg text-blue-600 dark:text-blue-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Audit & Rekonsiliasi Stok</h2>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5"><?php echo e($material->name); ?></p>
                    </div>
                </div>
                <button type="button" x-on:click="$dispatch('close')" class="absolute top-6 right-8 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="p-8 space-y-6 dark:bg-gray-800" x-data="{ 
                systemStock: <?php echo e($material->stock); ?>,
                physicalStock: <?php echo e($material->stock); ?>,
                get difference() { return this.physicalStock - this.systemStock }
            }">
                <div class="grid grid-cols-2 gap-6">
                    
                    <div class="bg-gray-50 dark:bg-gray-700 border border-gray-100 dark:border-gray-600 rounded-2xl p-5 shadow-inner">
                        <div class="text-[10px] font-black text-gray-400 dark:text-gray-400 uppercase tracking-widest mb-2">Stok Sistem Saat Ini</div>
                        <div class="flex items-end gap-2 text-2xl font-black text-gray-900 dark:text-white leading-none">
                            <span x-text="systemStock"></span>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1"><?php echo e($material->unit); ?></span>
                        </div>
                    </div>

                    
                    <div class="rounded-2xl p-5 border shadow-sm transition-all" 
                         :class="difference < 0 ? 'bg-red-50 border-red-100 text-red-700 dark:bg-red-900/20 dark:border-red-800' : (difference > 0 ? 'bg-emerald-50 border-emerald-100 text-emerald-700 dark:bg-emerald-900/20 dark:border-emerald-800' : 'bg-gray-50 border-gray-100 text-gray-500 dark:bg-gray-700 dark:border-gray-600')">
                        <div class="text-[10px] font-black uppercase tracking-widest mb-2" :class="difference === 0 ? 'text-gray-400' : ''">Selisih Stok</div>
                        <div class="text-2xl font-black leading-none">
                            <span x-text="difference > 0 ? '+' : ''"></span><span x-text="difference"></span>
                            <span class="text-xs font-bold uppercase tracking-widest ml-1"><?php echo e($material->unit); ?></span>
                        </div>
                    </div>
                </div>

                <div class="space-y-5 pt-2">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 dark:text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Input Stok Fisik Aktual</label>
                        <div class="relative group">
                            <input type="number" name="physical_stock" x-model="physicalStock" required min="0"
                                class="w-full pl-5 pr-16 py-4 text-lg font-black bg-gray-50 dark:bg-gray-700 border-gray-100 dark:border-gray-600 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all shadow-sm dark:text-white">
                            <div class="absolute inset-y-0 right-5 flex items-center pointer-events-none">
                                <span class="text-[10px] font-black text-gray-400 group-focus-within:text-blue-500 transition-colors uppercase tracking-widest"><?php echo e($material->unit); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-5">
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-[10px] font-black text-gray-400 dark:text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Alasan Penyesuaian</label>
                            <select name="reason" required class="w-full px-5 py-4 text-sm font-bold bg-gray-50 dark:bg-gray-700 border-gray-100 dark:border-gray-600 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all p-3 shadow-sm dark:text-white">
                                <option value="" disabled selected>Pilih Alasan...</option>
                                <option value="Rusak/Cacat">Barang Rusak / Cacat</option>
                                <option value="Hilang">Barang Hilang / Kurang</option>
                                <option value="Salah Input">Koreksi Salah Input Sebelumnya</option>
                                <option value="Ditemukan Kembali">Ditemukan Kembali (Update Fisik)</option>
                                <option value="Barang Baru">Penambahan Barang Baru (Manual)</option>
                            </select>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-[10px] font-black text-gray-400 dark:text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Catatan Tambahan</label>
                            <input type="text" name="notes" placeholder="Opsional detail tambahan..."
                                class="w-full px-5 py-3.5 text-sm font-bold bg-gray-50 dark:bg-gray-700 border-gray-100 dark:border-gray-600 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all shadow-sm dark:text-white">
                        </div>
                    </div>
                </div>

                <div x-show="difference !== 0" x-cloak class="p-4 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800 flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <p class="text-[11px] text-amber-700 dark:text-amber-300 font-bold italic leading-relaxed">
                        Anda akan melakukan penyesuaian stok sebesar <span x-text="difference"></span> <?php echo e($material->unit); ?>. Tindakan ini akan tercatat permanen di dalam log transaksi sebagai [ADJUSTMENT].
                    </p>
                </div>
            </div>

            
            <div class="px-8 py-6 bg-gray-50/80 dark:bg-gray-700/50 flex justify-end items-center gap-6 border-t border-gray-100 dark:border-gray-700">
                <button type="button" x-on:click="$dispatch('close')" class="text-sm font-black text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors uppercase tracking-widest">Batal</button>
                <button type="submit" class="px-10 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl shadow-lg shadow-blue-900/10 transition-all transform hover:-translate-y-0.5 font-black text-sm uppercase tracking-widest">
                    Simpan Penyesuaian
                </button>
            </div>
        </form>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $attributes = $__attributesOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__attributesOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $component = $__componentOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__componentOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
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
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\admin\materials\index.blade.php ENDPATH**/ ?>