<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <title><?php echo e(config('app.name', 'Laravel')); ?></title>
        <link rel="icon" href="<?php echo e(asset('images/logo.png')); ?>" type="image/png">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Page-specific head content (must load before Alpine) -->
        <?php echo $__env->yieldPushContent('head'); ?>

        <!-- Scripts -->
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
        <script src="<?php echo e(asset('js/vendor/html5-qrcode.min.js')); ?>" type="text/javascript"></script>
        
        <!-- PhotoSwipe for Image Zoom -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/photoswipe@5.3.8/dist/photoswipe.css">
        <?php echo $__env->yieldPushContent('styles'); ?>
        <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

        
        <style>
            [x-cloak] { display: none !important; }

            /* Sidebar collapse handling */
            @media (min-width: 1024px) {
                .sidebar-collapsed .main-content {
                    margin-left: 4rem !important; /* 64px for collapsed sidebar */
                }
            }
        </style>
    </head>
    <body class="font-sans antialiased overflow-x-hidden" 
          x-data="{ 
              sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
              mobileMenuOpen: false
          }"
          @toggle-sidebar.window="sidebarCollapsed = !sidebarCollapsed; localStorage.setItem('sidebarCollapsed', sidebarCollapsed)"
          @toggle-mobile-menu.window="mobileMenuOpen = !mobileMenuOpen"
          :class="{ 'sidebar-collapsed': sidebarCollapsed }"
          @storage.window="sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true'">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex overflow-x-hidden">
            <!-- Sidebar -->
            <?php echo $__env->make('layouts.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <!-- Main Content Wrapper -->
            <div class="main-content flex-1 flex flex-col overflow-x-hidden ml-0 lg:ml-64 transition-all duration-300">
                
                <!-- Top Navigation (Mobile/User Profile) -->
                <?php echo $__env->make('layouts.navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                <!-- Scrollable Content -->
                <main class="flex-1 bg-gray-100 dark:bg-gray-900 overflow-x-hidden">
                    <!-- Flash Messages -->
                    <?php echo $__env->make('components.flash-message', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                    <div class="py-6 overflow-x-hidden">
                        <?php echo e($slot); ?>

                    </div>
                </main>
            </div>
        </div>
        
        <!-- PhotoSwipe JS -->
        <script src="https://cdn.jsdelivr.net/npm/photoswipe@5.3.8/dist/umd/photoswipe.umd.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/photoswipe@5.3.8/dist/umd/photoswipe-lightbox.umd.min.js"></script>
        
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        
        
        <?php echo $__env->yieldPushContent('modals'); ?>
        <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

        <?php echo $__env->yieldPushContent('scripts'); ?>
    </body>
</html>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views/layouts/app.blade.php ENDPATH**/ ?>