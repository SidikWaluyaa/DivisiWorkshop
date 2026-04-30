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

     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?php echo e(__('CX Issue Resolution Center')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            
            <div class="flex flex-col gap-4 mb-6">
                
                <div class="flex flex-wrap gap-2">
                    <a href="<?php echo e(route('cx.index')); ?>" class="px-4 py-2 bg-teal-600 text-white rounded-lg shadow font-medium text-sm">
                        ⚠️ Butuh Follow Up (<?php echo e($orders->total()); ?>)
                    </a>
                    <a href="<?php echo e(route('cx.cancelled')); ?>" class="px-4 py-2 bg-white text-gray-700 hover:bg-gray-50 rounded-lg shadow font-medium text-sm">
                        🚫 Kolam Cancel
                    </a>
                    <a href="<?php echo e(route('complaints.index')); ?>" class="px-4 py-2 bg-white text-gray-700 hover:bg-gray-50 rounded-lg shadow font-medium text-sm">
                        📢 Data Komplain
                    </a>
                </div>

                
                <form action="<?php echo e(route('cx.index')); ?>" method="GET" class="w-full bg-white p-5 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                    
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                        
                        <div class="lg:col-span-12 xl:col-span-8 flex flex-col md:flex-row gap-3">
                            <div class="relative flex-grow">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input type="text" name="search" value="<?php echo e(request('search')); ?>" 
                                       class="w-full pl-10 pr-4 py-2.5 border-gray-200 rounded-xl text-sm focus:ring-teal-500 focus:border-teal-500 shadow-sm transition-all bg-gray-50/30" 
                                       placeholder="Cari Nomor SPK, Nama Pelanggan, atau WhatsApp...">
                            </div>

                            <div class="flex gap-2 shrink-0">
                                
                                <select name="sort" class="w-full md:w-auto border-amber-200 rounded-xl text-sm font-black text-amber-700 focus:ring-amber-500 py-2.5 px-4 bg-amber-50/50 shadow-sm appearance-none pr-10" style="background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22 fill=%22none%22 viewBox=%220 0 20 20%22%3E%3Cpath stroke=%22%23b45309%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%221.5%22 d=%22m6 8 4 4 4-4%22%2F%3E%3C%2Fsvg%3E'); background-position: right 0.75rem center; background-repeat: no-repeat; background-size: 1.25rem auto;">
                                    <option value="asc" <?php echo e(request('sort', 'asc') == 'asc' ? 'selected' : ''); ?>>⏳ Terlama</option>
                                    <option value="desc" <?php echo e(request('sort') == 'desc' ? 'selected' : ''); ?>>🔥 Terbaru</option>
                                </select>

                                <button type="submit" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl text-sm shadow-md shadow-teal-100 transition-all hover:-translate-y-0.5 active:translate-y-0 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                                    Filter
                                </button>
                            </div>
                        </div>

                        
                        <div class="hidden xl:flex xl:col-span-4 justify-end">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->anyFilled(['search', 'start_date', 'end_date', 'handler_id', 'last_status', 'source'])): ?>
                                <a href="<?php echo e(route('cx.index')); ?>" class="px-4 py-2.5 bg-gray-50 hover:bg-gray-100 text-gray-500 font-bold rounded-xl text-xs transition-all flex items-center gap-2 border border-gray-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    Reset Semua
                                </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-3 pt-4 border-t border-gray-50">
                        
                        <div class="flex items-center gap-2 md:col-span-2">
                            <div class="relative flex-grow">
                                <label class="text-[10px] uppercase font-black tracking-widest text-gray-400 absolute -top-2.5 left-2 bg-white px-1 z-10">Dari Tgl</label>
                                <input type="date" name="start_date" value="<?php echo e(request('start_date')); ?>" 
                                       class="w-full border-gray-200 rounded-xl text-xs font-bold text-gray-600 focus:ring-teal-500 py-2.5 bg-gray-50/30">
                            </div>
                            <span class="text-gray-300 text-xs">s/d</span>
                            <div class="relative flex-grow">
                                <label class="text-[10px] uppercase font-black tracking-widest text-gray-400 absolute -top-2.5 left-2 bg-white px-1 z-10">Sampai Tgl</label>
                                <input type="date" name="end_date" value="<?php echo e(request('end_date')); ?>" 
                                       class="w-full border-gray-200 rounded-xl text-xs font-bold text-gray-600 focus:ring-teal-500 py-2.5 bg-gray-50/30">
                            </div>
                        </div>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array(auth()->user()->role, ['admin', 'owner'])): ?>
                        <div class="relative">
                            <label class="text-[10px] uppercase font-black tracking-widest text-gray-400 absolute -top-2.5 left-2 bg-white px-1 z-10">Handler CX</label>
                            <select name="handler_id" class="w-full border-gray-200 rounded-xl text-xs font-bold text-gray-600 focus:ring-teal-500 py-2.5 bg-gray-50/30">
                                <option value="">Semua Handler</option>
                                <?php
                                    $cxHandlers = \App\Models\User::where('access_rights', 'LIKE', '%"cx"%')->get();
                                ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $cxHandlers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <option value="<?php echo e($h->id); ?>" <?php echo e(request('handler_id') == $h->id ? 'selected' : ''); ?>>👤 <?php echo e($h->name); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </select>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        
                        <div class="relative">
                            <label class="text-[10px] uppercase font-black tracking-widest text-gray-400 absolute -top-2.5 left-2 bg-white px-1 z-10">Status Terakhir</label>
                            <select name="last_status" class="w-full border-gray-200 rounded-xl text-xs font-bold text-gray-600 focus:ring-teal-500 py-2.5 bg-gray-50/30">
                                <option value="">Semua Status</option>
                                <option value="QC_REJECT" <?php echo e(request('last_status') == 'QC_REJECT' ? 'selected' : ''); ?>>🚩 QC Reject</option>
                                <option value="BATAL" <?php echo e(request('last_status') == 'BATAL' ? 'selected' : ''); ?>>🚫 Batal</option>
                                <option value="PRODUCTION" <?php echo e(request('last_status') == 'PRODUCTION' ? 'selected' : ''); ?>>🔨 Production</option>
                                <option value="SORTIR" <?php echo e(request('last_status') == 'SORTIR' ? 'selected' : ''); ?>>🔍 Sortir</option>
                                <option value="PREPARATION" <?php echo e(request('last_status') == 'PREPARATION' ? 'selected' : ''); ?>>🔧 Preparation</option>
                                <option value="ASSESSMENT" <?php echo e(request('last_status') == 'ASSESSMENT' ? 'selected' : ''); ?>>📋 Assessment</option>
                            </select>
                        </div>

                        
                        <div class="relative">
                            <label class="text-[10px] uppercase font-black tracking-widest text-gray-400 absolute -top-2.5 left-2 bg-white px-1 z-10">Sumber Kendala</label>
                            <select name="source" class="w-full border-gray-200 rounded-xl text-xs font-bold text-gray-600 focus:ring-teal-500 py-2.5 bg-gray-50/30">
                                <option value="">Semua Sumber</option>
                                <option value="GUDANG" <?php echo e(request('source') == 'GUDANG' ? 'selected' : ''); ?>>📦 Gudang</option>
                                <option value="WS" <?php echo e(request('source') == 'WS' ? 'selected' : ''); ?>>🔨 WS (Workshop)</option>
                                <option value="MANUAL" <?php echo e(request('source') == 'MANUAL' ? 'selected' : ''); ?>>📝 Manual</option>
                            </select>
                        </div>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->anyFilled(['search', 'start_date', 'end_date', 'handler_id', 'last_status', 'source'])): ?>
                            <div class="xl:hidden flex items-center justify-center pt-2">
                                <a href="<?php echo e(route('cx.index')); ?>" class="text-xs font-bold text-red-500 hover:text-red-600 flex items-center gap-1.5 underline decoration-dotted">
                                    Reset Semua Filter
                                </a>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </form>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" x-data="{}">
                <div class="p-6 text-gray-900">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


                    
                    <div class="block lg:hidden grid grid-cols-1 divide-y divide-gray-100 mb-4">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <?php
                                // Determine Issue Source
                                $openIssue = $order->cxIssues->where('status', 'OPEN')->first();
                                $issueSource = $openIssue ? $openIssue->type : ($order->status == \App\Enums\WorkOrderStatus::HOLD_FOR_CX ? 'RECEPTION_REJECT' : 'UNKNOWN');
                                $reporter = $openIssue ? $openIssue->reporter->name : 'Gudang/Admin';
                                $desc = $openIssue ? $openIssue->description : ($order->reception_rejection_reason ?? 'Tidak ada keterangan');
                                $photos = $openIssue ? $openIssue->photo_urls : [];

                            ?>
                            <div id="spk-cx-mobile-<?php echo e($order->spk_number); ?>"
                                 x-data="{ 
                                    isHighlighted: false,
                                    init() {
                                        const urlParams = new URLSearchParams(window.location.search);
                                        if (urlParams.get('highlight') === '<?php echo e($order->spk_number); ?>') {
                                            this.isHighlighted = true;
                                            setTimeout(() => { this.$el.scrollIntoView({ behavior: 'smooth', block: 'center' }); }, 500);
                                            setTimeout(() => { this.isHighlighted = false; }, 5000);
                                        }
                                    }
                                 }"
                                 class="p-4 bg-white hover:bg-gray-50 transition-all duration-500 border-b border-gray-100"
                                 :class="isHighlighted ? 'bg-yellow-50 border-l-4 border-yellow-400 shadow-lg relative z-10 scale-[1.01]' : ''">
                                
                                <div class="flex justify-between items-start mb-2">
                                     <div>
                                         <span class="font-mono bg-amber-50 text-amber-700 px-2 py-1 rounded text-xs font-bold border border-amber-100">
                                            <?php echo e($order->spk_number); ?>

                                        </span>
                                        <div class="flex flex-col mt-1">
                                            <span class="text-[9px] text-gray-400 font-medium">Estimasi: <?php echo e($order->entry_date->format('d M Y')); ?></span>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue): ?>
                                                <span class="text-[9px] font-black text-teal-600 uppercase tracking-tight">Masuk CX: <?php echo e($openIssue->created_at->translatedFormat('d M Y H:i')); ?></span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end">
                                        <div class="font-bold text-gray-900 text-sm"><?php echo e($order->customer_name); ?></div>
                                        <div class="flex items-center gap-1 mt-1">
                                            <span class="text-[10px] text-gray-400">Handler:</span>
                                            <span class="text-[10px] font-bold text-teal-600 bg-teal-50 px-1.5 py-0.5 rounded"><?php echo e($order->cxHandler->name ?? 'Unassigned'); ?></span>
                                        </div>
                                    </div>
                                </div>
                    
                                
                                <div class="mb-3">
                                     <a href="https://wa.me/<?php echo e(preg_replace('/^0/', '62', $order->customer_phone)); ?>?text=Halo%20Kak%20<?php echo e($order->customer_name); ?>,%20kami%20dari%20Workshop...%20ada%20kendala%20di%20sepatu%20<?php echo e($order->spk_number); ?>..." 
                                       target="_blank"
                                       class="inline-flex items-center gap-1 text-xs bg-green-100 text-green-700 px-2 py-1 rounded font-bold hover:bg-green-200">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.463 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                        Chat WA (<?php echo e($order->customer_phone); ?>)
                                    </a>
                                </div>
                    
                                
                                <div class="mb-4 bg-gray-50 p-3 rounded-lg border border-gray-100">
                                     <div class="flex items-center justify-between mb-2">
                                         <div class="flex items-center gap-2 flex-wrap">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue && $openIssue->source): ?>
                                            <?php
                                                $srcLabel = match($openIssue->source) {
                                                    'GUDANG' => '📦 Gudang',
                                                    'MANUAL' => '📝 Manual',
                                                    default => (str_starts_with($openIssue->source, 'WORKSHOP_') ? '🔨 Workshop' : $openIssue->source),
                                                };
                                                $srcColor = str_starts_with($openIssue->source, 'WORKSHOP') ? 'bg-purple-100 text-purple-700 border-purple-200' : ($openIssue->source === 'GUDANG' ? 'bg-red-100 text-red-700 border-red-200' : 'bg-gray-100 text-gray-600 border-gray-200');
                                            ?>
                                            <span class="text-[10px] uppercase font-black tracking-wider px-1.5 py-0.5 rounded border <?php echo e($srcColor); ?>"><?php echo e($srcLabel); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <span class="text-[10px] uppercase font-bold tracking-wider text-gray-500">Pelapor: <?php echo e($reporter); ?></span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue && $openIssue->category): ?>
                                            <span class="text-[10px] uppercase font-bold tracking-wider text-teal-600 border border-teal-200 px-1 rounded"><?php echo e($openIssue->category); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                         </div>
                                         <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue): ?>
                                            <button type="button" 
                                                    @click="$dispatch('open-edit-issue-modal', <?php echo e(json_encode($openIssue)); ?>)"
                                                    class="text-[10px] font-bold text-blue-600 bg-blue-50 border border-blue-200 px-2 py-1 rounded hover:bg-blue-100 flex items-center gap-1 transition-colors whitespace-nowrap">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                Edit
                                            </button>
                                         <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                     </div>
                                     <div class="mt-3 space-y-1.5">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue && $openIssue->category === 'OVERLOAD'): ?>
                                            <div class="bg-pink-50 border border-pink-200 p-3 rounded-lg flex items-center justify-center gap-2 shadow-sm">
                                                <span class="text-xl">📅</span>
                                                <div class="flex flex-col">
                                                    <span class="text-[10px] font-black text-pink-600 uppercase tracking-widest leading-none">Request Estimasi Baru</span>
                                                    <?php
                                                        $parsedDate = strtotime($openIssue->description) ? \Carbon\Carbon::parse($openIssue->description)->translatedFormat('d F Y') : $openIssue->description;
                                                    ?>
                                                    <span class="text-sm font-bold text-gray-800"><?php echo e($parsedDate); ?></span>
                                                </div>
                                            </div>
                                        <?php elseif($openIssue && ($openIssue->category === 'TEKNIS' || $openIssue->category === 'MATERIAL')): ?>
                                            <?php
                                                $hasKendala = $openIssue->kendala || $openIssue->kendala_1 || $openIssue->kendala_2;
                                                $hasSolusi = $openIssue->opsi_solusi || $openIssue->opsi_solusi_1 || $openIssue->opsi_solusi_2;
                                            ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasKendala || $hasSolusi || $openIssue->desc_upper || $openIssue->desc_sol || $openIssue->desc_kondisi_bawaan || $openIssue->rec_service_1): ?>
                                                <div class="flex flex-col gap-2">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasKendala): ?>
                                                        <div class="bg-white border border-red-100 rounded-lg shadow-sm p-3">
                                                            <div class="text-[9px] font-black text-red-600 uppercase tracking-widest mb-1 flex items-center gap-1">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                                Detail Kendala
                                                            </div>
                                                            <div class="text-xs font-semibold text-gray-800 leading-relaxed space-y-1 mt-1">
                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->kendala): ?><div><?php echo e($openIssue->kendala); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->kendala_1): ?><div class="flex gap-1"><span>•</span><span class="flex-1"><?php echo e($openIssue->kendala_1); ?></span></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->kendala_2): ?><div class="flex gap-1"><span>•</span><span class="flex-1"><?php echo e($openIssue->kendala_2); ?></span></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->desc_upper || $openIssue->desc_sol || $openIssue->desc_kondisi_bawaan): ?>
                                                        <div class="bg-red-50/50 border border-red-100 rounded-lg p-2.5">
                                                            <div class="text-[9px] font-black text-red-600 uppercase tracking-widest mb-1">Reject Details (Kondisi)</div>
                                                            <div class="text-[10px] font-medium text-gray-700 space-y-0.5">
                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->desc_upper): ?> <div><span class="text-red-400">Upper:</span> <?php echo e($openIssue->desc_upper); ?></div> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->desc_sol): ?> <div><span class="text-red-400">Sol:</span> <?php echo e($openIssue->desc_sol); ?></div> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->desc_kondisi_bawaan): ?> <div><span class="text-red-400">Bawaan:</span> <?php echo e($openIssue->desc_kondisi_bawaan); ?></div> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->rec_service_1 || $openIssue->rec_service_2): ?>
                                                        <div class="bg-blue-50/50 border border-blue-100 rounded-lg p-2.5">
                                                            <div class="text-[9px] font-black text-blue-600 uppercase tracking-widest mb-1 flex items-center gap-1">
                                                                <span>💎</span> Rekomendasi Layanan
                                                            </div>
                                                            <div class="text-[10px] font-bold text-gray-800 space-y-0.5">
                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->rec_service_1): ?> <div>• <?php echo e($openIssue->rec_service_1); ?></div> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->rec_service_2): ?> <div>• <?php echo e($openIssue->rec_service_2); ?></div> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasSolusi): ?>
                                                        <div class="bg-white border border-amber-100 rounded-lg shadow-sm p-3 mt-1">
                                                            <div class="text-[9px] font-black text-amber-600 uppercase tracking-widest mb-1 flex items-center gap-1">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                                                Opsi Solusi
                                                            </div>
                                                            <div class="text-xs font-semibold text-gray-700 leading-relaxed space-y-1 mt-1">
                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->opsi_solusi): ?><div><?php echo e($openIssue->opsi_solusi); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->opsi_solusi_1): ?><div class="flex gap-1"><span>•</span><span class="flex-1"><?php echo e($openIssue->opsi_solusi_1); ?></span></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->opsi_solusi_2): ?><div class="flex gap-1"><span>•</span><span class="flex-1"><?php echo e($openIssue->opsi_solusi_2); ?></span></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="bg-red-50 p-3 rounded-xl border border-red-100 text-sm italic text-gray-700">
                                                    "<?php echo e($desc); ?>"
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php else: ?>
                                            <div class="bg-red-50 p-3 rounded-xl border border-red-100 text-sm italic text-gray-700">
                                                "<?php echo e($desc); ?>"
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue && ($openIssue->recommended_services || $openIssue->suggested_services)): ?>
                                                        <div class="mt-2 space-y-1">
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->recommended_services): ?>
                                                                <div class="p-2 rounded-lg text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                                                    <div class="uppercase text-[8px] mb-1 opacity-70">💎 Recommended</div>
                                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->rec_service_1 || $openIssue->rec_service_2): ?>
                                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->rec_service_1): ?> <div>• <?php echo e($openIssue->rec_service_1); ?></div> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->rec_service_2): ?> <div>• <?php echo e($openIssue->rec_service_2); ?></div> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                     <?php else: ?>
                                                                        <?php echo nl2br(e($openIssue->recommended_services)); ?>

                                                                     <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                </div>
                                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->suggested_services): ?>
                                                                <div class="p-2 rounded-lg text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100">
                                                                    <div class="uppercase text-[8px] mb-1 opacity-70">✨ Optional</div>
                                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->sug_service_1 || $openIssue->sug_service_2): ?>
                                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->sug_service_1): ?> <div>• <?php echo e($openIssue->sug_service_1); ?></div> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->sug_service_2): ?> <div>• <?php echo e($openIssue->sug_service_2); ?></div> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                     <?php else: ?>
                                                                        <?php echo nl2br(e($openIssue->suggested_services)); ?>

                                                                     <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                </div>
                                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        </div>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                     <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($photos) > 0): ?>
                                        <div class="flex gap-2 mt-2 overflow-x-auto pb-1">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $photos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photoUrl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                <a href="<?php echo e(route('cx-issues.report', $openIssue->spk_number)); ?>" target="_blank" class="block w-10 h-10 rounded border hover:opacity-75 flex-shrink-0">
                                                    <img src="<?php echo e($photoUrl); ?>" class="w-full h-full object-cover rounded">
                                                </a>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                    
                                
                                <div class="mb-4 bg-gray-50 border border-gray-100 rounded-lg p-3 flex items-center justify-between shadow-sm">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-gray-800">Status Pengiriman</span>
                                        <span class="text-[9px] text-gray-500">Tahan atau lepas order</span>
                                    </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue): ?>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-black tracking-wider uppercase status-label <?php echo e($openIssue->shipping_status === 'SEND' ? 'text-teal-600' : 'text-red-500'); ?>">
                                                <?php echo e($openIssue->shipping_status === 'SEND' ? 'SEND ✅' : 'HOLD ⛔'); ?>

                                            </span>
                                            <label class="relative inline-flex items-center cursor-pointer group m-0">
                                                <input type="checkbox" 
                                                       class="sr-only peer" 
                                                       <?php echo e($openIssue->shipping_status === 'SEND' ? 'checked' : ''); ?>

                                                       onchange="toggleShippingStatus(<?php echo e($openIssue->id); ?>, this)">
                                                <div class="w-11 h-6 bg-red-100 rounded-full peer peer-checked:bg-teal-100 transition-colors border border-red-200 peer-checked:border-teal-200"></div>
                                                <div class="absolute left-[6px] top-[6px] w-3 h-3 bg-red-500 rounded-full transition-transform peer-checked:translate-x-5 peer-checked:bg-teal-500 shadow-sm"></div>
                                            </label>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-xs font-bold text-gray-400">-</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <button onclick="openActionModal('<?php echo e($order->id); ?>', 'lanjut', <?php echo e($openIssue ? json_encode($openIssue) : 'null'); ?>)" class="w-full text-white bg-teal-600 hover:bg-teal-700 font-bold rounded px-2 py-2 shadow">
                                        ✅ Lanjut
                                    </button>
                                    <button onclick="openActionModal('<?php echo e($order->id); ?>', 'tambah_jasa', <?php echo e($openIssue ? json_encode($openIssue) : 'null'); ?>)" class="w-full text-white bg-blue-600 hover:bg-blue-700 font-bold rounded px-2 py-2 shadow">
                                        ➕ Tambah Jasa
                                    </button>
                                    <button onclick="openActionModal('<?php echo e($order->id); ?>', 'komplain', <?php echo e($openIssue ? json_encode($openIssue) : 'null'); ?>)" class="w-full text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 font-semibold rounded px-2 py-2">
                                        ⚠️ Komplain
                                    </button>
                                    <button onclick="openActionModal('<?php echo e($order->id); ?>', 'cancel', <?php echo e($openIssue ? json_encode($openIssue) : 'null'); ?>)" class="w-full text-red-600 bg-white border border-red-200 hover:bg-red-50 font-semibold rounded px-2 py-2">
                                        ❌ Cancel
                                    </button>
                                </div>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                             <div class="text-center p-6 text-gray-500 italic text-sm">Tidak ada issue yang perlu penanganan.</div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="hidden lg:block overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                                <tr>
                                    <th class="px-6 py-3">Order Info</th>
                                    <th class="px-6 py-3">Customer</th>
                                    <th class="px-6 py-3 w-1/3">Detail Kendala (Issue)</th>
                                    <th class="px-6 py-3 text-center">Status Pengiriman</th>
                                    <th class="px-6 py-3 text-center">Resolusi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <?php
                                        // Determine Issue Source
                                        $openIssue = $order->cxIssues->where('status', 'OPEN')->first();
                                        $issueSource = $openIssue ? $openIssue->type : ($order->status == \App\Enums\WorkOrderStatus::HOLD_FOR_CX ? 'RECEPTION_REJECT' : 'UNKNOWN');
                                        $reporter = $openIssue ? $openIssue->reporter->name : 'Gudang/Admin';
                                        $desc = $openIssue ? $openIssue->description : ($order->reception_rejection_reason ?? 'Tidak ada keterangan');
                                        $photos = $openIssue ? $openIssue->photo_urls : [];
                                    ?>
                                    <tr id="spk-cx-desktop-<?php echo e($order->spk_number); ?>"
                                        x-data="{ 
                                            isHighlighted: false,
                                            init() {
                                                const urlParams = new URLSearchParams(window.location.search);
                                                if (urlParams.get('highlight') === '<?php echo e($order->spk_number); ?>') {
                                                    this.isHighlighted = true;
                                                    setTimeout(() => { this.$el.scrollIntoView({ behavior: 'smooth', block: 'center' }); }, 500);
                                                    setTimeout(() => { this.isHighlighted = false; }, 5000);
                                                }
                                            }
                                        }"
                                        class="hover:bg-gray-50 transition-all duration-500"
                                        :class="isHighlighted ? 'bg-yellow-50 border-l-4 border-yellow-400 shadow-lg relative z-10' : 'bg-white'">
                                        <td class="px-6 py-4 align-top text-left">
                                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Estimasi Selesai</div>
                                            <div class="font-bold text-gray-900 leading-tight"><?php echo e($order->entry_date->format('d M Y')); ?></div>
                                            
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue): ?>
                                                <div class="mt-2 pt-2 border-t border-gray-50">
                                                    <div class="text-[10px] font-black text-teal-600 uppercase tracking-widest leading-none mb-1">Masuk Divisi CX</div>
                                                    <div class="text-[11px] font-bold text-gray-700 leading-tight"><?php echo e($openIssue->created_at->translatedFormat('d M Y H:i')); ?></div>
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                            <div class="font-mono bg-amber-50 text-amber-700 px-2 py-1 rounded inline-block mt-2 text-xs font-bold border border-amber-100">
                                                <?php echo e($order->spk_number); ?>

                                            </div>
                                            <div class="mt-2 text-xs">
                                                <span class="px-2 py-0.5 rounded-full bg-gray-200 text-gray-600 font-semibold">
                                                    <?php echo e($order->previous_status ? 'Pre: ' . str_replace('_', ' ', ($order->previous_status instanceof \App\Enums\WorkOrderStatus ? $order->previous_status->value : $order->previous_status)) : 'QC Reject'); ?>

                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 align-top">
                                            <div class="font-bold text-gray-800"><?php echo e($order->customer_name); ?></div>
                                            <div class="text-xs text-gray-500 mb-1"><?php echo e($order->customer_phone); ?></div>
                                            <div class="text-xs font-medium text-gray-700">
                                                <?php echo e($order->shoe_brand); ?> <?php echo e($order->shoe_color); ?>

                                            </div>

                                            <div class="flex items-center gap-1.5 mt-2 bg-gray-50 p-1.5 rounded-lg border border-gray-100 w-fit">
                                                <div class="w-5 h-5 rounded-full bg-teal-100 flex items-center justify-center text-[8px] text-teal-600 font-bold border border-teal-200">
                                                    <?php echo e($order->cxHandler ? substr($order->cxHandler->name, 0, 1) : '?'); ?>

                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-[9px] text-gray-400 leading-none">Handler CX</span>
                                                    <span class="text-[10px] font-bold text-gray-700"><?php echo e($order->cxHandler->name ?? 'Unassigned'); ?></span>
                                                </div>
                                            </div>
                                            
                                            <a href="https://wa.me/<?php echo e(preg_replace('/^0/', '62', $order->customer_phone)); ?>?text=Halo%20Kak%20<?php echo e($order->customer_name); ?>,%20kami%20dari%20Workshop...%20ada%20kendala%20di%20sepatu%20<?php echo e($order->spk_number); ?>..." 
                                               target="_blank"
                                               class="inline-flex items-center gap-1 text-[10px] bg-green-100 text-green-700 px-2 py-1 rounded mt-2 hover:bg-green-200 font-bold">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.463 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                                Chat WA
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 align-top">
                                            <div class="flex items-start gap-2">
                                                <div class="flex-1">
                                                    <div class="flex items-center justify-between mb-1">
                                                        <div class="flex items-center gap-2 flex-wrap">
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue && $openIssue->source): ?>
                                                            <?php
                                                                $srcLabel = match($openIssue->source) {
                                                                    'GUDANG' => '📦 Gudang',
                                                                    'MANUAL' => '📝 Manual',
                                                                    default => (str_starts_with($openIssue->source, 'WORKSHOP_') ? '🔨 Workshop' : $openIssue->source),
                                                                };
                                                                $srcColor = str_starts_with($openIssue->source, 'WORKSHOP') ? 'bg-purple-100 text-purple-700 border-purple-200' : ($openIssue->source === 'GUDANG' ? 'bg-red-100 text-red-700 border-red-200' : 'bg-gray-100 text-gray-600 border-gray-200');
                                                            ?>
                                                            <span class="text-[10px] uppercase font-black tracking-wider px-1.5 py-0.5 rounded border <?php echo e($srcColor); ?>"><?php echo e($srcLabel); ?></span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        <span class="text-[10px] uppercase font-bold tracking-wider text-gray-500">Pelapor: <?php echo e($reporter); ?></span>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue && $openIssue->category): ?>
                                                            <span class="text-[10px] uppercase font-bold tracking-wider text-teal-600 border border-teal-200 px-1 rounded"><?php echo e($openIssue->category); ?></span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue): ?>
                                                        <button type="button" 
                                                                @click="$dispatch('open-edit-issue-modal', <?php echo e(json_encode($openIssue)); ?>)"
                                                                class="text-[10px] font-bold text-blue-600 bg-blue-50 border border-blue-200 px-2 py-1 rounded hover:bg-blue-100 flex items-center gap-1 transition-colors whitespace-nowrap">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                            Edit
                                                        </button>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                                    
                                                    <div class="mt-2 space-y-1.5">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue && $openIssue->category === 'OVERLOAD'): ?>
                                                            <div class="bg-pink-50 border border-pink-200 p-3 rounded-lg flex items-center gap-3 shadow-sm max-w-sm">
                                                                <span class="text-2xl">📅</span>
                                                                <div class="flex flex-col">
                                                                    <span class="text-[10px] font-black text-pink-600 uppercase tracking-widest leading-none">Request Estimasi Baru</span>
                                                                    <?php
                                                                        $parsedDate = strtotime($openIssue->description) ? \Carbon\Carbon::parse($openIssue->description)->translatedFormat('d F Y') : $openIssue->description;
                                                                    ?>
                                                                    <span class="text-sm font-bold text-gray-800"><?php echo e($parsedDate); ?></span>
                                                                </div>
                                                            </div>
                                                        <?php elseif($openIssue && ($openIssue->category === 'TEKNIS' || $openIssue->category === 'MATERIAL')): ?>
                                                            <?php
                                                                $hasKendala = $openIssue->kendala || $openIssue->kendala_1 || $openIssue->kendala_2;
                                                                $hasSolusi = $openIssue->opsi_solusi || $openIssue->opsi_solusi_1 || $openIssue->opsi_solusi_2;
                                                            ?>
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasKendala || $hasSolusi): ?>
                                                                <div class="flex flex-col gap-2">
                                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasKendala): ?>
                                                                        <div class="bg-white border border-red-100 rounded-lg shadow-sm p-3 relative overflow-hidden">
                                                                            <div class="absolute top-0 left-0 w-1 h-full bg-red-500"></div>
                                                                            <div class="text-[9px] font-black text-red-600 uppercase tracking-widest mb-1 flex items-center gap-1 pl-2">
                                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                                                Detail Kendala
                                                                            </div>
                                                                            <div class="text-xs font-semibold text-gray-800 leading-relaxed pl-2 space-y-1 mt-1">
                                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->kendala): ?><div><?php echo e($openIssue->kendala); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->kendala_1): ?><div class="flex gap-1"><span>•</span><span class="flex-1"><?php echo e($openIssue->kendala_1); ?></span></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->kendala_2): ?><div class="flex gap-1"><span>•</span><span class="flex-1"><?php echo e($openIssue->kendala_2); ?></span></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                            </div>
                                                                        </div>
                                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasSolusi): ?>
                                                                        <div class="bg-white border border-amber-100 rounded-lg shadow-sm p-3 relative overflow-hidden mt-1">
                                                                            <div class="absolute top-0 left-0 w-1 h-full bg-amber-500"></div>
                                                                            <div class="text-[9px] font-black text-amber-600 uppercase tracking-widest mb-1 flex items-center gap-1 pl-2">
                                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                                                                Opsi Solusi
                                                                            </div>
                                                                            <div class="text-xs font-semibold text-gray-700 leading-relaxed pl-2 space-y-1 mt-1">
                                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->opsi_solusi): ?><div><?php echo e($openIssue->opsi_solusi); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->opsi_solusi_1): ?><div class="flex gap-1"><span>•</span><span class="flex-1"><?php echo e($openIssue->opsi_solusi_1); ?></span></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->opsi_solusi_2): ?><div class="flex gap-1"><span>•</span><span class="flex-1"><?php echo e($openIssue->opsi_solusi_2); ?></span></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                            </div>
                                                                        </div>
                                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                                                    
                                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->desc_upper || $openIssue->desc_sol || $openIssue->desc_kondisi_bawaan): ?>
                                                                        <div class="bg-red-50/50 border border-red-100 rounded-lg p-3 mt-1">
                                                                            <div class="text-[9px] font-black text-red-600 uppercase tracking-widest mb-1">Reject Details (Kondisi Sepatu)</div>
                                                                            <div class="text-[11px] font-medium text-gray-700 space-y-0.5">
                                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->desc_upper): ?> <div><span class="text-red-400">Upper:</span> <?php echo e($openIssue->desc_upper); ?></div> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->desc_sol): ?> <div><span class="text-red-400">Sol:</span> <?php echo e($openIssue->desc_sol); ?></div> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->desc_kondisi_bawaan): ?> <div><span class="text-red-400">Bawaan:</span> <?php echo e($openIssue->desc_kondisi_bawaan); ?></div> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                            </div>
                                                                        </div>
                                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                                                    
                                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->rec_service_1 || $openIssue->rec_service_2): ?>
                                                                        <div class="bg-blue-50/50 border border-blue-100 rounded-lg p-3 mt-1">
                                                                            <div class="text-[9px] font-black text-blue-600 uppercase tracking-widest mb-1">💎 Layanan Rekomendasi</div>
                                                                            <div class="text-[11px] font-bold text-gray-800 space-y-0.5">
                                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->rec_service_1): ?> <div>• <?php echo e($openIssue->rec_service_1); ?></div> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->rec_service_2): ?> <div>• <?php echo e($openIssue->rec_service_2); ?></div> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                            </div>
                                                                        </div>
                                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="bg-red-50 p-3 rounded-lg border border-red-100 text-sm text-gray-800 italic">
                                                                    "<?php echo e($desc); ?>"
                                                                </div>
                                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        <?php else: ?>
                                                            <div class="bg-red-50 p-3 rounded-lg border border-red-100 text-sm text-gray-800 italic">
                                                                "<?php echo e($desc); ?>"
                                                            </div>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>

                                                     <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue && ($openIssue->recommended_services || $openIssue->suggested_services || $openIssue->rec_service_1 || $openIssue->rec_service_2 || $openIssue->sug_service_1 || $openIssue->sug_service_2)): ?>
                                                        <div class="mt-2 space-y-1">
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->recommended_services): ?>
                                                                <div class="p-2 rounded-lg text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                                                    <div class="uppercase text-[8px] mb-1 opacity-70">💎 Recommended</div>
                                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->rec_service_1 || $openIssue->rec_service_2): ?>
                                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->rec_service_1): ?> <div>• <?php echo e($openIssue->rec_service_1); ?></div> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->rec_service_2): ?> <div>• <?php echo e($openIssue->rec_service_2); ?></div> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                     <?php else: ?>
                                                                        <?php echo nl2br(e($openIssue->recommended_services)); ?>

                                                                     <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                </div>
                                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->suggested_services): ?>
                                                                <div class="p-2 rounded-lg text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100">
                                                                    <div class="uppercase text-[8px] mb-1 opacity-70">✨ Optional</div>
                                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->sug_service_1 || $openIssue->sug_service_2): ?>
                                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->sug_service_1): ?> <div>• <?php echo e($openIssue->sug_service_1); ?></div> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue->sug_service_2): ?> <div>• <?php echo e($openIssue->sug_service_2); ?></div> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                     <?php else: ?>
                                                                        <?php echo nl2br(e($openIssue->suggested_services)); ?>

                                                                     <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                </div>
                                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        </div>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                                     
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($photos) > 0): ?>
                                                        <div class="flex gap-2 mt-2">
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $photos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photoUrl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                                <a href="<?php echo e(route('cx-issues.report', $openIssue->spk_number)); ?>" target="_blank" class="block w-12 h-12 rounded border hover:opacity-75">
                                                                    <img src="<?php echo e($photoUrl); ?>" class="w-full h-full object-cover rounded">
                                                                </a>
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                                        </div>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 align-middle text-center border-l border-gray-50 border-r">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openIssue): ?>
                                                <div class="flex flex-col items-center justify-center gap-1.5">
                                                    <label class="relative inline-flex items-center cursor-pointer group m-0 shrink-0">
                                                        <input type="checkbox" 
                                                               class="sr-only peer" 
                                                               <?php echo e($openIssue->shipping_status === 'SEND' ? 'checked' : ''); ?>

                                                               onchange="toggleShippingStatus(<?php echo e($openIssue->id); ?>, this)">
                                                        <div class="w-11 h-6 bg-red-100 rounded-full peer peer-checked:bg-teal-100 transition-colors border border-red-200 peer-checked:border-teal-200"></div>
                                                        <div class="absolute left-[6px] top-[6px] w-3 h-3 bg-red-500 rounded-full transition-transform peer-checked:translate-x-5 peer-checked:bg-teal-500 shadow-sm"></div>
                                                    </label>
                                                    <span class="text-[10px] leading-none font-black tracking-wider uppercase status-label <?php echo e($openIssue->shipping_status === 'SEND' ? 'text-teal-600' : 'text-red-500'); ?>">
                                                        <?php echo e($openIssue->shipping_status === 'SEND' ? 'SEND ✅' : 'HOLD ⛔'); ?>

                                                    </span>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-[10px] text-gray-400 font-bold uppercase">-</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 align-middle">
                                            <div class="grid grid-cols-1 gap-2">
                                                <button onclick="openActionModal('<?php echo e($order->id); ?>', 'lanjut', <?php echo e($openIssue ? json_encode($openIssue) : 'null'); ?>)" class="w-full text-white bg-teal-600 hover:bg-teal-700 font-bold rounded text-xs px-3 py-2 shadow transition-all">
                                                    ✅ Lanjut (Resume)
                                                </button>
                                                <button onclick="openActionModal('<?php echo e($order->id); ?>', 'tambah_jasa', <?php echo e($openIssue ? json_encode($openIssue) : 'null'); ?>)" class="w-full text-white bg-blue-600 hover:bg-blue-700 font-bold rounded text-xs px-3 py-2 shadow transition-all">
                                                    ➕ Tambah Jasa
                                                </button>
                                                <button onclick="openActionModal('<?php echo e($order->id); ?>', 'komplain', <?php echo e($openIssue ? json_encode($openIssue) : 'null'); ?>)" class="w-full text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 font-semibold rounded text-xs px-3 py-2 transition-all">
                                                    ⚠️ Komplain
                                                </button>
                                                <button onclick="openActionModal('<?php echo e($order->id); ?>', 'cancel', <?php echo e($openIssue ? json_encode($openIssue) : 'null'); ?>)" class="w-full text-red-600 bg-white border border-red-200 hover:bg-red-50 font-semibold rounded text-xs px-3 py-2 transition-all">
                                                    ❌ Cancel Order
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="p-4 bg-green-50 rounded-full mb-3">
                                                    <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                </div>
                                                <p class="text-base font-medium">Bagus! Tidak ada issue yang perlu penanganan.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                     <div class="mt-4 px-4 pb-4">
                        <?php echo e($orders->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div id="actionModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm overflow-y-auto h-full w-full z-50 transition-opacity">
        <div class="relative top-20 mx-auto p-0 border w-full max-w-md shadow-2xl rounded-xl bg-white transform transition-all">
            <div class="bg-gray-50 px-6 py-4 rounded-t-xl border-b flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800" id="modalTitle">Konfirmasi Aksi</h3>
                <button onclick="closeActionModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form id="actionForm" method="POST" class="p-6" x-data="{ submitting: false, wasSubmitted: false }" @submit="if(wasSubmitted) { $event.preventDefault(); return; } submitting = true; wasSubmitted = true;">

                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" id="modalActionInput">
                <input type="hidden" name="issue_id" id="modalIssueInput">
                
                <div class="mb-4">
                    <div id="modalDescription" class="text-sm text-gray-600 mb-4 bg-blue-50 p-3 rounded-lg border border-blue-100 flex gap-2">
                        <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span id="modalDescText">Deskripsi aksi akan muncul disini.</span>
                    </div>

                    
                    <div id="addServiceInputs" class="hidden space-y-4 mb-4 p-4 bg-gray-50 rounded-xl border border-gray-200"
                         x-data="{ 
                            showDropdown: false, 
                            search: '',
                            isCustom: false,
                            selectedCategory: '',
                            selectedService: null,
                            customName: '',
                            price: 0,
                            serviceDetails: '',
                            allServices: <?php echo \Illuminate\Support\Js::from($services ?? [])->toHtml() ?>,
                            addedServices: [],
                            
                            get uniqueCategories() {
                                return [...new Set(this.allServices.map(s => s.category))].sort();
                            },

                            get filteredServices() {
                                if (!this.selectedCategory) return [];
                                let filtered = this.allServices.filter(s => s.category === this.selectedCategory);
                                if (this.search) {
                                    filtered = filtered.filter(s => 
                                        s.name.toLowerCase().includes(this.search.toLowerCase())
                                    );
                                }
                                return filtered;
                            },
                            
                            selectService(service) {
                                if (service === 'custom') {
                                    this.setCustom(this.search || 'Layanan Kustom');
                                    return;
                                }
                                this.selectedService = service;
                                this.isCustom = false;
                                this.price = service.price;
                                this.search = service.name;
                                this.showDropdown = false;
                            },
                            
                            setCustom(name) {
                                this.selectedService = { id: null, name: name }; 
                                this.isCustom = true;
                                this.customName = name;
                                this.showDropdown = false;
                                this.search = name;
                            },

                            addServiceToList() {
                                // Validation before adding
                                if(!this.selectedService && !this.isCustom) return alert('Pilih layanan atau input custom name.');
                                if(this.isCustom && !this.customName) return alert('Nama layanan custom harus diisi.');
                                if(this.price === '' || this.price < 0) return alert('Harga tidak valid.');

                                this.addedServices.push({
                                    id: Date.now(), // temporary unique id for removal
                                    service_id: this.selectedService ? this.selectedService.id : null,
                                    category_name: this.selectedCategory || 'Custom',
                                    custom_name: this.isCustom ? this.customName : null,
                                    display_name: this.isCustom ? this.customName : this.selectedService.name,
                                    cost: parseInt(this.price) || 0,
                                    service_details: this.serviceDetails,
                                    is_custom: this.isCustom
                                });

                                // Reset input fields, leave category alone for ease of use
                                this.selectedService = null;
                                this.isCustom = false;
                                this.customName = '';
                                this.price = 0;
                                this.search = '';
                                this.serviceDetails = '';
                            },

                            removeService(id) {
                                this.addedServices = this.addedServices.filter(s => s.id !== id);
                            },

                            get totalPrice() {
                                return this.addedServices.reduce((total, s) => total + s.cost, 0);
                            },

                            reset() {
                                this.showDropdown = false;
                                this.search = '';
                                this.isCustom = false;
                                this.selectedCategory = '';
                                this.selectedService = null;
                                this.customName = '';
                                this.price = 0;
                                this.serviceDetails = '';
                                this.addedServices = [];
                            }
                         }"
                         @cx-add-service-reset.window="reset()">
                         
                        
                        <input type="hidden" name="services_data" :value="JSON.stringify(addedServices)">

                        
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Kategori</label>
                            <select x-model="selectedCategory" @change="selectedService = null; search = ''; isCustom = false; price = 0"
                                    class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                                <option value="">-- Pilih Kategori --</option>
                                <template x-for="cat in uniqueCategories" :key="cat">
                                    <option :value="cat" x-text="cat"></option>
                                </template>
                            </select>
                        </div>

                        
                        <div x-show="selectedCategory" x-transition>
                            <div class="relative">
                                <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Layanan / Jasa</label>
                                
                                
                                <div class="relative">
                                    <input type="text" 
                                           x-model="search"
                                           @click="showDropdown = true"
                                           @click.away="showDropdown = false"
                                           placeholder="Cari jasa atau pilih kategori..."
                                           class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm pl-10">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </div>
                                </div>

                                
                                <div x-show="showDropdown" 
                                     class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-xl max-h-60 overflow-y-auto custom-scrollbar p-1">
                                    
                                    
                                    <template x-for="service in filteredServices" :key="service.id">
                                        <div @click="selectService(service)" 
                                             class="px-3 py-2 hover:bg-blue-50 rounded cursor-pointer flex items-center justify-between text-sm transition-colors border-b border-gray-50 last:border-0">
                                            <div class="flex flex-col">
                                                <span class="font-medium" x-text="service.name"></span>
                                                <span class="text-[10px] text-gray-400" x-text="service.category"></span>
                                            </div>
                                            <span class="text-xs font-bold text-blue-600" x-text="'Rp ' + parseInt(service.price).toLocaleString()"></span>
                                        </div>
                                    </template>

                                    
                                    <div @click="selectService('custom')"
                                         class="p-3 bg-blue-50 hover:bg-blue-100 rounded cursor-pointer mt-1 flex items-center justify-between transition-colors border border-blue-100">
                                        <div class="flex flex-col">
                                            <span class="text-[10px] text-blue-600 font-bold uppercase tracking-widest">✏️ Layanan Custom...</span>
                                            <span class="text-xs text-gray-500 italic" x-text="search ? 'Gunakan \'' + search + '\'' : 'Ketik nama layanan manual'"></span>
                                        </div>
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div x-show="isCustom" x-transition>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Nama Layanan Custom</label>
                            <input type="text" x-model="customName" 
                                   class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                                   placeholder="Masukkan nama layanan kustom...">
                        </div>

                        
                        <div class="grid grid-cols-2 gap-4" x-show="selectedService || isCustom">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Harga (Rp)</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-xs">Rp</span>
                                    <input type="number" x-model="price" 
                                           :readonly="!isCustom"
                                           :class="!isCustom ? 'bg-gray-100' : 'bg-white'"
                                           class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 pl-8" placeholder="0">
                                </div>
                            </div>
                            <div class="flex items-end pb-1">
                                <div class="flex items-center gap-2 p-2 bg-white rounded-lg border border-gray-200 w-full">
                                    <span class="text-[10px] uppercase font-bold text-gray-400">Tipe:</span>
                                    <span x-text="isCustom ? 'CUSTOM' : 'STANDAR'" 
                                          :class="isCustom ? 'text-blue-600' : 'text-gray-600'"
                                          class="text-[10px] font-black italic"></span>
                                </div>
                            </div>
                        </div>

                        
                        <div x-show="selectedService || isCustom">
                            <label class="block text-xs font-bold text-gray-700 mb-1">Detail Jasa / Instruksi SPK (Muncul di Nota/SPK)</label>
                            <textarea x-model="serviceDetails" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: Warna Hitam, Ukuran 42, Ganti Insole Ori, dll..."></textarea>
                            
                            
                            <div class="mt-3 flex justify-end">
                                <button type="button" @click="addServiceToList()" class="px-3 py-1.5 bg-blue-100 text-blue-700 hover:bg-blue-200 rounded-lg text-xs font-bold flex items-center gap-1 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Tambahkan ke List
                                </button>
                            </div>
                        </div>

                        
                        <div x-show="addedServices.length > 0" x-transition class="mt-6 pt-4 border-t border-gray-200">
                            <label class="block text-xs font-bold text-gray-700 mb-2">Daftar Jasa Ditambahkan:</label>
                            <div class="space-y-2 max-h-40 overflow-y-auto custom-scrollbar pr-1">
                                <template x-for="s in addedServices" :key="s.id">
                                    <div class="flex items-start justify-between bg-white p-2.5 rounded-lg border border-gray-200 shadow-sm">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-bold text-gray-800" x-text="s.display_name"></span>
                                                <span x-show="s.is_custom" class="text-[8px] px-1.5 py-0.5 rounded bg-amber-100 text-amber-700 font-bold uppercase tracking-wider">Custom</span>
                                            </div>
                                            <div class="text-[10px] text-gray-500 mt-0.5" x-text="s.category_name"></div>
                                            <div x-show="s.service_details" class="text-[10px] italic text-gray-600 mt-1" x-text="'Instruksi: ' + s.service_details"></div>
                                        </div>
                                        <div class="flex flex-col items-end gap-2 ml-2">
                                            <span class="text-xs font-bold text-blue-600" x-text="'Rp ' + s.cost.toLocaleString()"></span>
                                            <button type="button" @click="removeService(s.id)" class="text-red-500 hover:text-red-700 p-0.5" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            
                            
                            <div class="mt-3 flex items-center justify-between bg-blue-50 p-2.5 rounded-lg border border-blue-100">
                                <span class="text-xs font-bold text-blue-800">Total Biaya:</span>
                                <span class="text-sm font-black text-blue-700" x-text="'Rp ' + totalPrice.toLocaleString()"></span>
                            </div>
                        </div>
                    </div>

                    <label class="block text-sm font-bold text-gray-700 mb-2">Catatan Follow Up (Internal CX)</label>
                    <textarea name="notes" required rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-teal-500 focus:border-teal-500 mb-4" placeholder="Jelaskan alasan follow up / hasil kesepakatan dengan customer..."></textarea>
                    
                    
                    <div>
                        <label class="block text-sm font-bold text-teal-700 mb-1">📅 Update Estimasi Selesai (Opsional)</label>
                        <p class="text-[10px] text-gray-500 mb-2 leading-tight">Isi tanggal ini HANYA jika Anda ingin mengubah Estimasi Selesai (Due Date) asli pada Master Order. Kosongkan jika tidak ada perubahan jadwal.</p>
                        <input type="date" name="estimasi_selesai_baru" class="w-full border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 text-sm py-2">
                    </div>
                </div>

                <div class="flex gap-3 justify-end mt-6">
                    <button type="button" @click="if(!submitting) closeActionModal()" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 disabled:opacity-50" :disabled="submitting">Batal</button>
                    <button type="submit" id="modalSubmitBtn" 
                            :disabled="submitting"
                            class="px-4 py-2 bg-teal-600 text-white rounded-lg text-sm font-bold hover:bg-teal-700 shadow-lg flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed transform active:scale-95 transition-all">

                        <span x-show="!submitting">Konfirmasi</span>
                        <div x-show="submitting" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span>Memproses...</span>
                        </div>
                    </button>
                </div>
            </form>
        </div>
    </div>


    <script>
    function openActionModal(orderId, action, issue = null) {
        const form = document.getElementById('actionForm');
        const title = document.getElementById('modalTitle');
        const desc = document.getElementById('modalDescText');
        const actionInput = document.getElementById('modalActionInput');
        const btn = document.getElementById('modalSubmitBtn');
        const serviceInputs = document.getElementById('addServiceInputs');
        const dateInput = form.querySelector('input[name="estimasi_selesai_baru"]');
        
        // Reset inputs
        if (dateInput) dateInput.value = '';
        
        // Prevent modal from working if currently submitting
        const formComp = document.getElementById('actionForm').__x; // Access Alpine data if needed, but safer to use a flag
        // However, standard JS is easier here for a global lock
        if (window.isCxSubmitting) return;

        window.dispatchEvent(new CustomEvent('cx-add-service-reset'));


        serviceInputs.classList.add('hidden');

        form.action = '/cx/' + orderId + '/process';
        actionInput.value = action;
        document.getElementById('actionModal').classList.remove('hidden');

        // Pre-fill date only if action is 'lanjut' and category is 'OVERLOAD'
        if (action === 'lanjut' && issue && issue.category === 'OVERLOAD' && issue.description) {
            // Check if description is a valid ISO date string (YYYY-MM-DD)
            const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
            if (dateRegex.test(issue.description.trim())) {
                if (dateInput) dateInput.value = issue.description.trim();
            }
        }

        // Dynamic Content
        switch(action) {
            case 'lanjut':
                title.textContent = 'Lanjutkan Order (Resume)';
                desc.textContent = 'Order akan dilanjutkan ke tahap Assessment (Teknisi). Pastikan customer sudah setuju dengan kondisi tersebut.';
                btn.className = "px-4 py-2 bg-teal-600 text-white rounded-lg text-sm font-bold hover:bg-teal-700 shadow flex items-center gap-2";
                break;
            case 'tambah_jasa':
                title.textContent = 'Tambah Jasa (CX Input)';
                desc.textContent = 'Silakan input layanan tambahan yang disepakati customer. Order akan diteruskan ke Sortir untuk cek material.';
                btn.className = "px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 shadow flex items-center gap-2";
                serviceInputs.classList.remove('hidden');
                break;
            case 'komplain':
                title.textContent = 'Buat Komplain (Complaint)';
                desc.textContent = 'Order akan ditransfer ke modul Komplain untuk penanganan lebih lanjut.';
                btn.className = "px-4 py-2 bg-amber-500 text-white rounded-lg text-sm font-bold hover:bg-amber-600 shadow flex items-center gap-2";
                break;
            case 'cancel':
                title.textContent = 'Batalkan Order (Cancel)';
                desc.textContent = 'PERINGATAN: Order akan dibatalkan permanen dan masuk ke Kolam Cancel.';
                btn.className = "px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-bold hover:bg-red-700 shadow flex items-center gap-2";
                break;
        }
    }

    function closeActionModal() {
        document.getElementById('actionModal').classList.add('hidden');
    }

    function toggleShippingStatus(issueId, element) {
        if (!issueId || element.disabled) return;

        // Find the label text element to update
        const container = element.closest('div.flex') || element.closest('td') || element.parentElement.parentElement;
        const labelSpan = container.querySelector('.status-label');
        const originalStatusText = labelSpan ? labelSpan.innerHTML : '';
        const originalClass = labelSpan ? labelSpan.className : '';
        
        // Disable and add loading state
        element.disabled = true;
        const parentLabel = element.parentElement;
        if (parentLabel) parentLabel.style.opacity = '0.5';

        // Optimistic UI update
        const isChecked = element.checked;
        if (labelSpan) {
            if (isChecked) {
                labelSpan.innerHTML = 'SEND ✅';
                labelSpan.className = 'text-[10px] leading-none font-black tracking-wider uppercase status-label text-teal-600 transition-colors';
            } else {
                labelSpan.innerHTML = 'HOLD ⛔';
                labelSpan.className = 'text-[10px] leading-none font-black tracking-wider uppercase status-label text-red-500 transition-colors';
            }
        }

        console.log(`[Toggle] Attempting to change status for Issue #${issueId} to ${isChecked ? 'SEND' : 'HOLD'}`);

        // Production-resilient fetch
        fetch(`/cx-issues/${issueId}/toggle-shipping`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                _method: 'PATCH'
            })
        })
        .then(response => {
            if (!response.ok) {
                if(response.status === 404) throw new Error('Data issue tidak ditemukan atau sudah ditangani.');
                if(response.status === 419) throw new Error('Sesi berakhir, silakan refresh halaman.');
                throw new Error('Server error (' + response.status + ')');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                console.log(`[Toggle] Success for issue ${issueId}: ${data.status}`);
                // Sync any other open instances of the same toggle
                document.querySelectorAll(`input[onchange*="toggleShippingStatus(${issueId},"]`).forEach(el => {
                    if (el !== element) {
                        el.checked = isChecked;
                        const otherContainer = el.closest('div.flex') || el.closest('td') || el.parentElement.parentElement;
                        const otherLabel = otherContainer ? otherContainer.querySelector('.status-label') : null;
                        if(otherLabel){
                            otherLabel.innerHTML = isChecked ? 'SEND ✅' : 'HOLD ⛔';
                            otherLabel.className = isChecked 
                                ? 'text-[10px] leading-none font-black tracking-wider uppercase status-label text-teal-600' 
                                : 'text-[10px] leading-none font-black tracking-wider uppercase status-label text-red-500';
                        }
                    }
                });
            } else {
                throw new Error(data.message || 'Gagal mengubah status.');
            }
        })
        .catch(error => {
            console.error('[Toggle Error]:', error);
            // Revert UI on failure
            element.checked = !isChecked;
            if (labelSpan) {
                labelSpan.innerHTML = originalStatusText;
                labelSpan.className = originalClass;
            }
            alert('Gagal: ' + error.message);
        })
        .finally(() => {
            element.disabled = false;
            if (parentLabel) parentLabel.style.opacity = '1';
        });
    }
    </script>
    <?php if (isset($component)) { $__componentOriginal94410223a1b3c56859dd375944febe8c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal94410223a1b3c56859dd375944febe8c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.edit-issue-modal','data' => ['services' => $services]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('edit-issue-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['services' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($services)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal94410223a1b3c56859dd375944febe8c)): ?>
<?php $attributes = $__attributesOriginal94410223a1b3c56859dd375944febe8c; ?>
<?php unset($__attributesOriginal94410223a1b3c56859dd375944febe8c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal94410223a1b3c56859dd375944febe8c)): ?>
<?php $component = $__componentOriginal94410223a1b3c56859dd375944febe8c; ?>
<?php unset($__componentOriginal94410223a1b3c56859dd375944febe8c); ?>
<?php endif; ?>

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
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\cx\index.blade.php ENDPATH**/ ?>