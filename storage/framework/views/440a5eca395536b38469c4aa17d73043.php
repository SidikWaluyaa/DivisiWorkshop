<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?php echo e(asset('images/logo.png')); ?>" type="image/png">
    <title>Customer Experience Report - <?php echo e($order->spk_number ?? 'SPK'); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            /* Tracking Style dots */
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
        activePhoto: '<?php echo e($photoUrls[0] ?? ''); ?>',
        photos: <?php echo e(json_encode($photoUrls)); ?>,
        photoSizes: <?php echo e(json_encode($photoSizes ?? [])); ?>

      }">
    
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-6 md:mb-10 items-center">
            <div>
                <a href="<?php echo e(url()->previous() !== url()->current() ? url()->previous() : '/'); ?>" class="group inline-flex items-center gap-2 mb-4 md:mb-6 text-gray-500 hover:text-teal-600 transition-colors">
                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow group-hover:bg-teal-500 group-hover:text-white transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </div>
                    <span class="font-medium text-sm tracking-wide">Kembali</span>
                </a>
                <div class="flex items-center gap-3 mb-2">
                    <span class="px-2 py-0.5 rounded bg-teal-100 text-teal-700 text-[10px] font-black uppercase tracking-widest border border-teal-200">
                        CX ISSUE REPORT
                    </span>
                </div>
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-black text-gray-800 mb-2 tracking-tight uppercase">
                    <?php echo e($order->spk_number ?? 'SPK_TIDAK_DITEMUKAN'); ?>

                </h1>
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 rounded bg-white border border-gray-200 text-gray-600 font-bold text-xs md:text-sm shadow-sm">
                        <?php echo e(\Carbon\Carbon::parse($issue->created_at)->format('d F Y, H:i')); ?>

                    </span>
                    <span class="text-gray-400 font-bold text-sm">•</span>
                    <span class="text-gray-500 font-bold text-sm"><?php echo e($order->customer_name ?? $issue->customer_name); ?> • <?php echo e($order->shoe_brand ?? '-'); ?></span>
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
                <!-- Changed border visually to match tracking (teal) instead of red -->
                <div class="bg-white rounded-[2.5rem] shadow-xl border-t-8 border-teal-500 overflow-hidden relative group">
                    <!-- Main Preview -->
                    <template x-if="photos.length > 0">
                        <div class="relative aspect-[4/3] bg-gray-900 flex items-center justify-center cursor-zoom-in overflow-hidden" 
                             @click="openLightbox(activePhoto, 'Dokumentasi CX - <?php echo e($order->spk_number ?? ''); ?>')">
                            <img :src="activePhoto" class="w-full h-full object-contain transition-transform duration-700 group-hover:scale-105" alt="Active CX Issue Photo">
                            
                            <!-- Overlay Labels -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="absolute bottom-8 left-8 right-8 flex justify-between items-end transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                                <div class="bg-white/10 backdrop-blur-md border border-white/20 px-4 py-2 rounded-2xl text-white">
                                    <p class="text-[10px] font-black uppercase tracking-widest opacity-70">Visual Reference</p>
                                    <p class="text-sm font-bold flex items-center gap-2">
                                        Bukti Kendala CX
                                        <span class="px-2 py-0.5 rounded-full bg-black/40 text-[10px] font-mono border border-white/10" x-text="photoSizes[activePhoto] || 'N/A'"></span>
                                    </p>
                                </div>
                                <div class="bg-teal-600 text-white p-3 rounded-2xl shadow-lg shadow-teal-600/20">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" /></svg>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template x-if="photos.length === 0">
                        <div class="relative aspect-[4/3] bg-gray-50 flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <p class="font-bold">Tidak ada foto dilampirkan</p>
                        </div>
                    </template>

                    <!-- Thumbnails Strip -->
                    <template x-if="photos.length > 1">
                        <div class="p-6 bg-gray-50/50 border-t border-gray-100">
                            <div class="flex gap-4 overflow-x-auto no-scrollbar pb-2">
                                <template x-for="(photo, index) in photos" :key="index">
                                    <button @click="activePhoto = photo" 
                                            :class="activePhoto === photo ? 'ring-4 ring-teal-500 scale-95 shadow-xl' : 'opacity-40 hover:opacity-100 hover:scale-105'"
                                            class="relative flex-shrink-0 w-24 h-24 rounded-2xl overflow-hidden transition-all duration-500 transform bg-gray-200 border-2 border-white shadow-sm">
                                        <img :src="photo" class="w-full h-full object-cover" alt="Thumbnail">
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Right Column: Notes & Recommendations -->
            <div class="lg:col-span-12 xl:col-span-4 space-y-6">
                <!-- Detailed Findings Card -->
                <div class="bg-white rounded-[2.5rem] shadow-xl border-t-8 border-teal-500 p-8 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-teal-50 rounded-bl-full -mr-4 -mt-4 opacity-50"></div>
                    
                    <h2 class="text-xl font-black text-gray-800 mb-8 flex items-center gap-3 relative z-10">
                        <span class="w-2 h-7 bg-teal-600 rounded-full"></span>
                        DETAIL KENDALA (<?php echo e($issue->category); ?>)
                    </h2>
                    
                    <div class="space-y-6 relative z-10">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($issue->kendala_1 || $issue->kendala_2): ?>
                            <div class="group">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 group-hover:text-teal-500 transition-colors">List Kendala</p>
                                <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 shadow-sm transition-all text-sm group-hover:bg-white group-hover:shadow-md">
                                    <ul class="list-disc pl-5 space-y-2 text-gray-700 font-bold">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($issue->kendala_1): ?> <li><?php echo e($issue->kendala_1); ?></li> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($issue->kendala_2): ?> <li><?php echo e($issue->kendala_2); ?></li> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($issue->opsi_solusi_1 || $issue->opsi_solusi_2): ?>
                            <div class="group">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 group-hover:text-teal-500 transition-colors">Opsi Solusi</p>
                                <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 shadow-sm transition-all text-sm group-hover:bg-teal-50 group-hover:shadow-md border border-transparent group-hover:border-teal-100">
                                    <ul class="list-disc pl-5 space-y-2 text-gray-700 font-bold">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($issue->opsi_solusi_1): ?> <li><?php echo e($issue->opsi_solusi_1); ?></li> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($issue->opsi_solusi_2): ?> <li><?php echo e($issue->opsi_solusi_2); ?></li> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$issue->kendala_1 && !$issue->kendala_2 && !$issue->opsi_solusi_1 && !$issue->opsi_solusi_2): ?>
                            <!-- Fallback for older data that only has description -->
                             <div class="group">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 group-hover:text-teal-500 transition-colors">Deskripsi</p>
                                <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 font-bold text-gray-700 leading-relaxed shadow-sm group-hover:bg-white group-hover:shadow-md transition-all whitespace-pre-wrap">
                                    <?php echo e(rtrim($issue->description) ?: 'Tidak ada deskripsi'); ?>

                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        
                        <div class="mt-4 pt-4 border-t border-gray-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center font-bold">
                                         <?php echo e(substr($issue->reporter->name ?? 'User', 0, 1)); ?>

                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Dilaporkan Oleh</p>
                                        <p class="text-sm font-bold text-gray-800"><?php echo e($issue->reporter->name ?? 'Unknown User'); ?></p>
                                    </div>
                                </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-12 text-center">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.4em] mb-2">&copy; <?php echo e(date('Y')); ?> Shoe Workshop Elite System</p>
            <div class="inline-flex gap-4">
                <span class="w-8 h-1 bg-teal-500/20 rounded-full"></span>
                <span class="w-16 h-1 bg-teal-500/40 rounded-full"></span>
                <span class="w-8 h-1 bg-teal-500/20 rounded-full"></span>
            </div>
        </div>
    </div>

    <!-- Lightbox Modal (Premium Tracking Theme) -->
    <div x-show="showLightbox" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-black/90 backdrop-blur-md"
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
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\cx\issue-report.blade.php ENDPATH**/ ?>