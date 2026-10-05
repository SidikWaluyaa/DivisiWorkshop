<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- PWA Meta Tags --}}
        <meta name="theme-color" content="#22AF85">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="ShoeWorkshop Mobile">
        <meta name="application-name" content="ShoeWorkshop Mobile">
        <meta name="mobile-web-app-capable" content="yes">

        <title>{{ $title ?? 'Mobile Kontrol Teknisi' }} — ShoeWorkshop</title>
        <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">

        @stack('head')

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="{{ asset('js/vendor/html5-qrcode.min.js') }}" type="text/javascript"></script>

        @stack('styles')
        @livewireStyles

        <style>
            [x-cloak] { display: none !important; }
            body, .font-sans {
                font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
            }
            .font-mono {
                font-family: 'JetBrains Mono', monospace !important;
            }
            /* Safe area for modern bezel-less phones */
            .pb-safe {
                padding-bottom: env(safe-area-inset-bottom, 16px);
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-slate-100 text-slate-900 min-h-full flex flex-col selection:bg-[#22AF85] selection:text-white"
          x-data="{ burgerOpen: false }">

        {{-- Slide-over Backdrop Overlay --}}
        <div x-show="burgerOpen" 
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="burgerOpen = false" 
             class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm"></div>

        {{-- Slide-over Mobile Burger Sidebar Drawer (Clean Modern Light Theme) --}}
        <aside x-show="burgerOpen" 
               x-cloak
               x-transition:enter="transition ease-out duration-300 transform"
               x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in duration-200 transform"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="-translate-x-full"
               class="fixed top-0 bottom-0 left-0 z-50 w-72 max-w-[85vw] bg-white text-slate-800 shadow-2xl border-r border-slate-200 flex flex-col">
            
            {{-- Drawer Header (Brand Teal Gradient) --}}
            <div class="p-5 bg-gradient-to-r from-[#22AF85] via-[#1fa57d] to-[#1a906d] border-b border-[#188564] flex items-center justify-between text-white shadow-xs">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white p-1 flex items-center justify-center shadow-md">
                        <img src="{{ asset('images/logo.png') }}" alt="ShoeWorkshop Logo" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h2 class="text-sm font-black tracking-tight text-white leading-tight">ShoeWorkshop</h2>
                        <p class="text-[10px] font-black text-[#FFC232] tracking-wider uppercase mt-0.5">Mobile Kontrol Teknisi</p>
                    </div>
                </div>
                <button @click="burgerOpen = false" type="button" class="p-1.5 rounded-lg bg-white/15 hover:bg-white/25 text-white border border-white/20 transition-all active:scale-95" title="Tutup Menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- User Profile Card inside Drawer (Light & Fresh) --}}
            @if(Auth::check())
                <div class="p-3.5 mx-4 mt-4 rounded-2xl bg-slate-50 border border-slate-200/90 flex items-center gap-3 shadow-2xs">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-[#22AF85] to-teal-700 text-white font-black text-sm shadow-sm flex items-center justify-center">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-black text-slate-900 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] font-extrabold text-[#22AF85] uppercase tracking-wider mt-0.5 truncate">
                            {{ Auth::user()->specialization ?: (Auth::user()->station ?: Auth::user()->role) }}
                        </p>
                    </div>
                </div>
            @endif

            {{-- Navigation Links List (Light & Harmonious) --}}
            <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-1.5">
                <a href="{{ route('mobile.production.index') }}" 
                   @click="burgerOpen = false"
                   class="flex items-center gap-3 px-3.5 py-3 rounded-xl font-bold text-xs transition-all {{ request()->routeIs('mobile.production.*') ? 'bg-gradient-to-r from-[#22AF85] to-[#1b936f] text-white font-black shadow-md shadow-[#22AF85]/25' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('mobile.production.*') ? 'text-white' : 'text-[#22AF85]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span>Pusat Kontrol Produksi</span>
                </a>

                <a href="{{ route('internal-tracking.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-3 rounded-xl font-bold text-xs text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-all">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Lacak SPK Cepat</span>
                </a>

                <a href="{{ route('production.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-3 rounded-xl font-bold text-xs text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-all">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>Mode Desktop Workshop</span>
                </a>
            </nav>

            {{-- Drawer Footer: Logout (Clean Red Accent) --}}
            <div class="p-4 border-t border-slate-100 bg-slate-50/80 pb-safe">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                            class="w-full h-11 bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white border border-rose-200 hover:border-rose-600 rounded-xl font-black text-xs uppercase tracking-wider flex items-center justify-center gap-2 transition-all active:scale-95 shadow-2xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span>Keluar / Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main Content (No bottom navbar) --}}
        <main class="flex-1 min-h-screen">
            {{ $slot }}
        </main>

        @livewireScripts
        @stack('scripts')

        <script>
            // 1. Tangani & senyapkan rejected promise internal Livewire (saat request polling dibatalkan karena user klik tab/aksi)
            window.addEventListener('unhandledrejection', function(event) {
                if (event.reason && typeof event.reason === 'object' && event.reason.status === null && event.reason.body === null) {
                    event.preventDefault();
                }
            });

            // 2. Toast listener untuk notifikasi instan mobile
            document.addEventListener('livewire:init', () => {
                Livewire.on('swal:toast', (event) => {
                    const data = Array.isArray(event) ? event[0] : event;
                    if (window.Swal) {
                        Swal.fire({
                            toast: true,
                            position: 'top',
                            icon: data.icon || 'success',
                            title: data.title || '',
                            showConfirmButton: false,
                            timer: 2500,
                            timerProgressBar: true,
                            customClass: {
                                popup: 'rounded-2xl shadow-xl font-sans text-xs border border-slate-200'
                            }
                        });
                    }
                });
            });
        </script>
    </body>
</html>
