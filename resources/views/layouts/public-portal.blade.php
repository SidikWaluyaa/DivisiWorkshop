<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

    <title>{{ $title ?? 'Konfirmasi Pembayaran — Shoe Workshop' }}</title>

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
            background-color: #F8FAFC;
        }
        .font-poppins {
            font-family: 'Poppins', sans-serif;
        }
    </style>

    @livewireStyles
</head>
<body class="min-h-screen bg-[#F8FAFC] text-slate-900 antialiased selection:bg-[#FFC232] selection:text-slate-950">
    <div class="relative min-h-screen flex flex-col justify-between overflow-x-hidden">
        {{-- Subtle Background Glow Elements (Hijau & Kuning) --}}
        <div class="fixed top-0 left-1/2 -translate-x-1/2 w-full max-w-5xl h-[350px] pointer-events-none opacity-15">
            <div class="absolute top-[-20%] left-[10%] w-[320px] h-[320px] bg-[#22AF85] rounded-full blur-[100px]"></div>
            <div class="absolute top-[-10%] right-[10%] w-[300px] h-[300px] bg-[#FFC232] rounded-full blur-[110px]"></div>
        </div>

        {{-- Top Brand Bar --}}
        <header class="sticky top-0 z-10 w-full bg-white/90 backdrop-blur-md border-b border-slate-200/80 shadow-xs">
            <div class="max-w-xl mx-auto px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-[#22AF85] text-white font-black flex items-center justify-center text-xs shadow-md shadow-[#22AF85]/20 transform -rotate-3">
                        <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                            <circle cx="12" cy="12" r="5"/>
                        </svg>
                    </div>
                    <div>
                        <span class="block text-xs font-black font-poppins text-slate-900 leading-tight uppercase italic tracking-tight">Shoe Workshop</span>
                        <span class="block text-[9px] font-bold text-[#22AF85] uppercase tracking-widest leading-none">Payment Portal</span>
                    </div>
                </div>

                <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-[#22AF85] text-[10px] font-black uppercase tracking-wider">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#22AF85] animate-ping"></span>
                    <span>Resmi &amp; Aman</span>
                </div>
            </div>
        </header>

        {{-- Main Content --}}
        <main class="relative z-10 flex-1">
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <footer class="relative z-10 py-5 text-center text-xs text-slate-500 border-t border-slate-200 bg-white/80 backdrop-blur-md mt-auto">
            <div class="max-w-xl mx-auto px-4 flex flex-col items-center justify-center gap-1">
                <div class="flex items-center gap-1.5">
                    <span class="font-bold text-slate-700 text-[11px]">PT. Terang Garam Solusindo</span>
                    <span class="text-slate-300">•</span>
                    <span class="text-[11px] text-[#22AF85] font-bold">Shoe Workshop</span>
                </div>
                <p class="text-[10px] text-slate-400">
                    &copy; {{ date('Y') }} Sistem Konfirmasi Pembayaran Digital.
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
                    background: '#FFFFFF',
                    color: '#0F172A',
                    customClass: {
                        popup: 'rounded-2xl shadow-xl border border-slate-100 text-xs font-bold'
                    }
                });
            });
        });
    </script>
</body>
</html>
