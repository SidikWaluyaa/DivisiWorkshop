<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- PWA Meta Tags --}}
        <meta name="theme-color" content="#22AF85">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="ShoeWorkshop PWA">
        <meta name="application-name" content="ShoeWorkshop PWA">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="msapplication-TileColor" content="#22AF85">
        <meta name="msapplication-TileImage" content="{{ asset('pwa-icons/icon-144x144.png') }}">

        <title>{{ $title ?? 'Divisi Workshop PWA' }} — ShoeWorkshop</title>
        <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

        {{-- PWA Manifest & Icons --}}
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <link rel="apple-touch-icon" sizes="192x192" href="{{ asset('pwa-icons/icon-192x192.png') }}">
        <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('pwa-icons/icon-152x152.png') }}">
        <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('pwa-icons/icon-144x144.png') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        @stack('head')

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/css/pwa-mobile.css', 'resources/js/app.js', 'resources/js/pwa/main.js'])
        <script src="{{ asset('js/vendor/html5-qrcode.min.js') }}" type="text/javascript"></script>
        
        <!-- PhotoSwipe for Image Zoom -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/photoswipe@5.3.8/dist/photoswipe.css">
        @stack('styles')
        @livewireStyles
        
        <style>
            [x-cloak] { display: none !important; }

            /* Desktop sidebar collapse width adjustment */
            @media (min-width: 768px) {
                .ws-sidebar-collapsed .ws-main-content {
                    margin-left: 4.5rem !important; /* 72px */
                }
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-800 min-h-full flex flex-col overflow-x-hidden selection:bg-[#22AF85] selection:text-white"
          x-data="{ 
              sidebarCollapsed: localStorage.getItem('sidebarCollapsed_ws') === 'true',
              activeDrawer: null
          }"
          @toggle-sidebar.window="sidebarCollapsed = !sidebarCollapsed; localStorage.setItem('sidebarCollapsed_ws', sidebarCollapsed)"
          :class="{ 'ws-sidebar-collapsed': sidebarCollapsed }">

        {{-- Vue PWA Mount Point --}}
        <div id="pwa-mount"></div>

        <div class="min-h-screen bg-slate-50 flex flex-col flex-1">
            
            {{-- 1. Dedicated Desktop/Tablet Sidebar (≥ 768px) --}}
            @include('layouts.partials.workshop-pwa.sidebar')

            {{-- 2. Main Content Wrapper --}}
            <div class="ws-main-content flex-1 flex flex-col min-w-0 md:ml-64 transition-all duration-300">
                
                {{-- 3. Top PWA App Header Bar --}}
                @include('layouts.partials.workshop-pwa.header')

                {{-- 4. Main Scrollable Content View --}}
                <main class="flex-1 bg-slate-50 text-slate-800 pb-20 md:pb-6">
                    @include('components.flash-message')

                    {{ $slot }}
                </main>
            </div>
        </div>

        {{-- 5. Mobile Bottom Navigation Bar (< 768px) --}}
        @include('layouts.partials.workshop-pwa.bottom-nav')

        {{-- 6. Mobile Action Sheet Drawer (< 768px) --}}
        @include('layouts.partials.workshop-pwa.mobile-drawer')

        <!-- PhotoSwipe JS -->
        <script src="https://cdn.jsdelivr.net/npm/photoswipe@5.3.8/dist/umd/photoswipe.umd.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/photoswipe@5.3.8/dist/umd/photoswipe-lightbox.umd.min.js"></script>
        
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        
        @stack('modals')
        @livewireScripts
        @stack('scripts')
        @include('admin.customers.partials.toast-alert')
    </body>
</html>
