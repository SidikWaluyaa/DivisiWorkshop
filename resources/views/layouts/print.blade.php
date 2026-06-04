<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Cetak Laporan - {{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-white min-h-screen text-black">
        
        <div class="p-4 sm:p-6 lg:p-8">
            {{ $slot }}
        </div>

        @livewireScripts
        @stack('scripts')

        {{-- Auto print trigger after DOM is ready --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Delay slightly to ensure fonts, Tailwind CSS, and other styles are rendered properly
                setTimeout(function() {
                    window.print();
                }, 800);
            });
        </script>
    </body>
</html>
