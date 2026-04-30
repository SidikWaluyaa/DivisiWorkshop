<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?php echo e(asset('images/logo.png')); ?>" type="image/png">
    <title>QC Reject Report - <?php echo e($order->spk_number); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            background-color: #f3f4f6;
            background-image: radial-gradient(#e5e7eb 1px, transparent 1px);
            background-size: 20px 20px;
        }
        [x-cloak] { display: none !important; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="p-4 md:p-8 antialiased text-gray-800" 
      x-data="{ 
        showLightbox: false, 
        lightboxImage: '', 
        lightboxCaption: '',
        openLightbox(url, caption) {
            this.lightboxImage = url;
            this.lightboxCaption = caption;
            this.showLightbox = true;
        },
        activePhoto: '<?php echo e($photos[0] ?? ''); ?>',
        photos: <?php echo e(json_encode($photos)); ?>

      }">
    
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-6 md:mb-10 items-center">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="px-2 py-0.5 rounded bg-red-100 text-red-700 text-[10px] font-black uppercase tracking-widest border border-red-200">
                        QC REJECT REPORT
                    </span>
                </div>
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-black text-gray-800 mb-2 tracking-tight uppercase">
                    <?php echo e($order->spk_number); ?>

                </h1>
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 rounded bg-white border border-gray-200 text-gray-600 font-bold text-xs md:text-sm shadow-sm">
                        <?php echo e(\Carbon\Carbon::parse($order->entry_date)->format('d F Y')); ?>

                    </span>
                    <span class="text-gray-400 font-bold text-sm">•</span>
                    <span class="text-gray-500 font-bold text-sm"><?php echo e($order->customer_name); ?> • <?php echo e($order->shoe_brand); ?></span>
                </div>
            </div>
            
            <!-- Logo Branding -->
            <div class="hidden md:flex justify-end opacity-90">
                 <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Logo" class="h-20 drop-shadow-md">
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Left Column: Photo Gallery -->
            <div class="lg:col-span-12 xl:col-span-8 space-y-6">
                <div class="bg-white rounded-[2.5rem] shadow-xl border-t-8 border-red-500 overflow-hidden relative group">
                    <!-- Main Preview -->
                    <div class="relative aspect-[4/3] bg-gray-900 flex items-center justify-center cursor-zoom-in overflow-hidden" 
                         @click="openLightbox(activePhoto, 'Detail Temuan - <?php echo e($order->spk_number); ?>')">
                        <img :src="activePhoto" class="w-full h-full object-contain transition-transform duration-700 group-hover:scale-105" alt="Active Reject Photo">
                        
                        <!-- Overlay Labels -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="absolute bottom-8 left-8 right-8 flex justify-between items-end transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                            <div class="bg-white/10 backdrop-blur-md border border-white/20 px-4 py-2 rounded-2xl text-white">
                                <p class="text-[10px] font-black uppercase tracking-widest opacity-70">Focus Area</p>
                                <p class="text-sm font-bold">Visual Bukti Reject</p>
                            </div>
                            <div class="bg-red-600 text-white p-3 rounded-2xl shadow-lg shadow-red-600/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" /></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Thumbnails Strip -->
                    <div class="p-6 bg-gray-50/50 border-t border-gray-100">
                        <div class="flex gap-4 overflow-x-auto no-scrollbar pb-2">
                            <template x-for="(photo, index) in photos" :key="index">
                                <button @click="activePhoto = photo" 
                                        :class="activePhoto === photo ? 'ring-4 ring-red-500 scale-95 shadow-xl' : 'opacity-40 hover:opacity-100 hover:scale-105'"
                                        class="relative flex-shrink-0 w-24 h-24 rounded-2xl overflow-hidden transition-all duration-500 transform bg-gray-200 border-2 border-white shadow-sm">
                                    <img :src="photo" class="w-full h-full object-cover" alt="Thumbnail">
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Notes & Recommendations -->
            <div class="lg:col-span-12 xl:col-span-4 space-y-6">
                <!-- Detailed Findings Card -->
                <div class="bg-white rounded-[2.5rem] shadow-xl border-t-8 border-red-500 p-8 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-red-50 rounded-bl-full -mr-4 -mt-4 opacity-50"></div>
                    
                    <h2 class="text-xl font-black text-gray-800 mb-8 flex items-center gap-3 relative z-10">
                        <span class="w-2 h-7 bg-red-600 rounded-full"></span>
                        DETAIL TEMUAN
                    </h2>
                    
                    <div class="space-y-8 relative z-10">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($issue->desc_upper): ?>
                        <div class="group">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 group-hover:text-red-500 transition-colors">Upper / Bagian Atas</p>
                            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 font-bold text-gray-700 leading-relaxed shadow-sm group-hover:bg-white group-hover:shadow-md transition-all">
                                <?php echo e($issue->desc_upper); ?>

                            </div>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($issue->desc_sol): ?>
                        <div class="group">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 group-hover:text-red-500 transition-colors">Midsole & Outsole</p>
                            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 font-bold text-gray-700 leading-relaxed shadow-sm group-hover:bg-white group-hover:shadow-md transition-all">
                                <?php echo e($issue->desc_sol); ?>

                            </div>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($issue->desc_kondisi_bawaan): ?>
                        <div class="group">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 group-hover:text-red-500 transition-colors">Kondisi Bawaan</p>
                            <div class="p-4 bg-red-50/30 rounded-2xl border border-red-100 font-bold text-gray-600 italic leading-relaxed shadow-sm">
                                "<?php echo e($issue->desc_kondisi_bawaan); ?>"
                            </div>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <!-- Solution Recommendation Card -->
                <div class="bg-gray-900 rounded-[2.5rem] shadow-xl p-8 relative overflow-hidden text-white border-t-8 border-red-600">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-red-600/10 rounded-full -mr-16 -mt-16 blur-2xl"></div>
                    
                    <h2 class="text-xl font-black mb-8 flex items-center gap-3 relative z-10 tracking-tight">
                        <svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        REKOMENDASI LAYANAN
                    </h2>
                    
                    <div class="space-y-6 relative z-10">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($issue->recommended_services): ?>
                        <div>
                            <p class="text-[10px] font-black text-white/40 uppercase tracking-[0.2em] mb-3">Wajib (Main Treatment)</p>
                            <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-5 border border-white/10 shadow-inner group hover:bg-white/10 transition-all">
                                <p class="text-sm font-bold text-gray-200 whitespace-pre-line leading-relaxed italic"><?php echo e($issue->recommended_services); ?></p>
                            </div>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($issue->suggested_services): ?>
                        <div>
                            <p class="text-[10px] font-black text-white/40 uppercase tracking-[0.2em] mb-3">Saran Tambahan (Add-on)</p>
                            <div class="bg-red-600/10 backdrop-blur-sm rounded-2xl p-5 border border-red-500/20 shadow-inner group hover:bg-red-600/20 transition-all">
                                <p class="text-sm font-bold text-red-100 whitespace-pre-line leading-relaxed italic"><?php echo e($issue->suggested_services); ?></p>
                            </div>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-12 text-center">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.4em] mb-2">&copy; <?php echo e(date('Y')); ?> Shoe Workshop Elite System</p>
            <div class="inline-flex gap-4">
                <span class="w-8 h-1 bg-red-500/20 rounded-full"></span>
                <span class="w-16 h-1 bg-red-500/40 rounded-full"></span>
                <span class="w-8 h-1 bg-red-500/20 rounded-full"></span>
            </div>
        </div>
    </div>

    <!-- Lightbox Modal (Premium) -->
    <div x-show="showLightbox" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-black/95 backdrop-blur-md"
         @keydown.escape.window="showLightbox = false">
        
        <!-- Close Button -->
        <button @click="showLightbox = false" class="absolute top-8 right-8 text-white/50 hover:text-white transition-all transform hover:rotate-90 z-50">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <div class="relative max-w-5xl w-full flex flex-col items-center" @click.away="showLightbox = false">
            <img :src="lightboxImage" class="max-h-[80vh] w-auto rounded-3xl shadow-2xl border-4 border-white/5 transition-transform duration-500 group-hover:scale-[1.02]" alt="Full Image">
            <div class="mt-6 flex flex-col items-center">
                <p x-text="lightboxCaption" class="text-white font-black text-xl tracking-wide bg-white/5 backdrop-blur-md px-8 py-3 rounded-2xl border border-white/10 shadow-xl"></p>
                <p class="mt-3 text-white/30 text-[10px] font-black uppercase tracking-[0.3em]">Premium Quality Documentation</p>
            </div>
        </div>
    </div>

</body>
</html>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\reception\qc-reject-report.blade.php ENDPATH**/ ?>