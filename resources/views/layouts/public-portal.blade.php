<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

    <title>{{ $title ?? 'Portal Pembayaran — Shoe Workshop' }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Poppins:ital,wght@0,600;0,700;0,800;0,900;1,700&display=swap" rel="stylesheet">

    <!-- CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- HTML5 QR Code Scanner -->
    <script src="{{ asset('js/vendor/html5-qrcode.min.js') }}"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        [x-cloak] { display: none !important; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .font-poppins {
            font-family: 'Poppins', sans-serif;
        }
    </style>

    @livewireStyles
</head>
<body class="min-h-screen bg-slate-900 text-slate-100 antialiased selection:bg-[#F5C518] selection:text-slate-950">
    <div class="relative min-h-screen flex flex-col justify-between overflow-x-hidden">
        {{-- Background Glow Accents --}}
        <div class="fixed top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-[500px] pointer-events-none opacity-20 dark:opacity-30">
            <div class="absolute top-[-10%] left-[20%] w-[400px] h-[400px] bg-emerald-500 rounded-full blur-[120px]"></div>
            <div class="absolute top-[10%] right-[20%] w-[350px] h-[350px] bg-[#F5C518] rounded-full blur-[130px]"></div>
        </div>

        {{-- Main Slot --}}
        <main class="relative z-10 flex-1">
            {{ $slot }}
        </main>

        {{-- Minimal Footer --}}
        <footer class="relative z-10 py-6 text-center text-xs text-slate-500 border-t border-slate-800/80 bg-slate-950/60 backdrop-blur-md">
            <div class="container mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-lg bg-[#F5C518] text-slate-950 font-black flex items-center justify-center text-[10px]">SW</div>
                    <span class="font-bold text-slate-400">Shoe Workshop & LAF Market</span>
                </div>
                <p class="text-[11px] text-slate-500">
                    &copy; {{ date('Y') }} Sistem Keuangan & Layanan Workshop Resmi.
                </p>
            </div>
        </footer>
    </div>

    @livewireScripts
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('swal:toast', (event) => {
                const data = event[0] || event;
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: data.icon || 'info',
                    title: data.title || '',
                    showConfirmButton: false,
                    timer: 3500,
                    timerProgressBar: true,
                    background: '#1E293B',
                    color: '#F8FAFC'
                });
            });
        });
    </script>
</body>
</html>
