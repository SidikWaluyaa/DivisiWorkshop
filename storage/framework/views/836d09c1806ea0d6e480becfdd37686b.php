
<section class="relative overflow-hidden bg-gradient-to-br from-teal-600 via-teal-700 to-orange-600 rounded-3xl shadow-2xl animate-fade-in-up">
    <div class="absolute inset-0 bg-black/10"></div>
    <div class="absolute -right-20 -top-20 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
    <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-orange-500/20 rounded-full blur-3xl"></div>

    <div class="relative px-8 py-10">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-white/90 text-xs font-bold">
                        <span id="realtime-pulse" class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                        Live Analytics • Real-Time
                    </div>
                    <span id="last-update-time" class="text-[10px] font-bold text-white/50 uppercase tracking-wider"></span>
                </div>
                <h1 class="text-4xl lg:text-5xl font-black text-white tracking-tight">
                    Executive Dashboard
                </h1>
                <p class="text-teal-100 text-lg font-medium">
                    Command Center — CS, Gudang, Workshop & CX
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
                
                <div class="flex gap-1 bg-white/10 backdrop-blur-md rounded-xl p-1 border border-white/20">
                    <?php
                        $presets = [
                            'today' => 'Hari Ini',
                            '7d' => '7 Hari',
                            '30d' => '30 Hari',
                            'this_month' => 'Bulan Ini',
                            'ytd' => 'YTD',
                        ];
                    ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $presets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <button onclick="window.location.href='<?php echo e(route('dashboard', ['period' => $key])); ?>'"
                        class="px-3 py-2 rounded-lg text-xs font-bold transition-all duration-200
                        <?php echo e($selectedPeriod === $key ? 'bg-white text-teal-700 shadow-lg' : 'text-white/80 hover:bg-white/20'); ?>">
                        <?php echo e($label); ?>

                    </button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>

                
                <form action="<?php echo e(route('dashboard')); ?>" method="GET" class="flex items-center gap-2 bg-white/15 backdrop-blur-md px-4 py-3 rounded-xl border border-white/20 shadow-lg">
                    <svg class="w-5 h-5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <input type="hidden" name="period" value="custom">
                    <input type="date" name="start_date" value="<?php echo e($startDate); ?>"
                        class="bg-transparent border-none text-white text-sm focus:ring-0 cursor-pointer font-medium w-32"
                        onchange="this.form.submit()">
                    <span class="text-white/60">—</span>
                    <input type="date" name="end_date" value="<?php echo e($endDate); ?>"
                        class="bg-transparent border-none text-white text-sm focus:ring-0 cursor-pointer font-medium w-32"
                        onchange="this.form.submit()">
                </form>
            </div>
        </div>

        
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-8">
            <div class="bg-white/15 backdrop-blur-md rounded-2xl p-4 border border-white/20 hover:bg-white/25 transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <div>
                        <div id="hero-spk-active" class="text-2xl font-black text-white"><?php echo e($kpi['workshop']['active']); ?></div>
                        <div class="text-[10px] text-white/60 font-bold uppercase tracking-wider">SPK Aktif</div>
                    </div>
                </div>
            </div>
            <div class="bg-white/15 backdrop-blur-md rounded-2xl p-4 border border-white/20 hover:bg-white/25 transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div>
                        <div id="hero-leads" class="text-2xl font-black text-white"><?php echo e($kpi['cs']['leads']); ?></div>
                        <div class="text-[10px] text-white/60 font-bold uppercase tracking-wider">Leads Masuk</div>
                    </div>
                </div>
            </div>
            <div class="bg-white/15 backdrop-blur-md rounded-2xl p-4 border border-white/20 hover:bg-white/25 transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <div id="hero-cx-open" class="text-2xl font-black text-white"><?php echo e($kpi['cx']['open_issues']); ?></div>
                        <div class="text-[10px] text-white/60 font-bold uppercase tracking-wider">CX Open</div>
                    </div>
                </div>
            </div>
            <div class="bg-white/15 backdrop-blur-md rounded-2xl p-4 border border-white/20 hover:bg-white/25 transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <div id="server-clock" class="text-2xl font-black text-white font-mono"><?php echo e(now()->format('H:i')); ?></div>
                        <div class="text-[10px] text-white/60 font-bold uppercase tracking-wider"><?php echo e(\Carbon\Carbon::now()->translatedFormat('D, d M Y')); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\dashboard-v2\sections\header.blade.php ENDPATH**/ ?>