<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Page-specific head content (must load before Alpine) -->
        @stack('head')

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="{{ asset('js/vendor/html5-qrcode.min.js') }}" type="text/javascript"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        
        <!-- PhotoSwipe for Image Zoom -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/photoswipe@5.3.8/dist/photoswipe.css">
        @stack('styles')
        
        <style>
            /* Sidebar collapse handling */
            @media (min-width: 1024px) {
                .sidebar-collapsed .main-content {
                    margin-left: 4rem !important; /* 64px for collapsed sidebar */
                }
            }
        </style>
    </head>
    <body class="font-sans antialiased overflow-x-hidden" 
          x-data="{ sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true' }"
          :class="{ 'sidebar-collapsed': sidebarCollapsed }"
          @storage.window="sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true'">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex overflow-x-hidden">
            <!-- Sidebar -->
            @include('layouts.sidebar')

            <!-- Main Content Wrapper -->
            <div class="main-content flex-1 flex flex-col overflow-x-hidden w-full ml-0 lg:ml-64 transition-all duration-300">
                
                <!-- Top Navigation (Mobile/User Profile) -->
                @include('layouts.navigation')

                <!-- Scrollable Content -->
                <main class="flex-1 bg-gray-100 dark:bg-gray-900 overflow-x-hidden">
                    <!-- Flash Messages -->
                    @include('components.flash-message')

                    <div class="py-6 overflow-x-hidden">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
        
        <!-- PhotoSwipe JS -->
        <script src="https://cdn.jsdelivr.net/npm/photoswipe@5.3.8/dist/umd/photoswipe.umd.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/photoswipe@5.3.8/dist/umd/photoswipe-lightbox.umd.min.js"></script>
        
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        
        @stack('scripts')
    </body>
</html>
