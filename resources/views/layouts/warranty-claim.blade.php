<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Portal Klaim Garansi Mandiri - Ajukan klaim garansi sepatu Anda secara mudah dan cepat.">
    <title>Klaim Garansi Mandiri — Shoe Workshop</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

    <!-- Fonts: Inter for premium feel -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        * { font-family: 'Inter', sans-serif; }

        body {
            min-height: 100vh;
            background-color: #f3f4f6;
            background-image: radial-gradient(#d1d5db 1px, transparent 1px);
            background-size: 22px 22px;
        }

        /* Subtle animated gradient orbs in background */
        .bg-orb {
            position: fixed;
            border-radius: 9999px;
            filter: blur(80px);
            opacity: 0.18;
            pointer-events: none;
            animation: drift 12s ease-in-out infinite alternate;
        }
        .bg-orb-1 {
            width: 420px; height: 420px;
            background: radial-gradient(circle, #10b981, #059669);
            top: -120px; left: -100px;
            animation-delay: 0s;
        }
        .bg-orb-2 {
            width: 320px; height: 320px;
            background: radial-gradient(circle, #f59e0b, #f97316);
            bottom: -80px; right: -80px;
            animation-delay: -6s;
        }
        .bg-orb-3 {
            width: 240px; height: 240px;
            background: radial-gradient(circle, #6366f1, #8b5cf6);
            top: 50%; right: 5%;
            animation-delay: -3s;
        }

        @keyframes drift {
            from { transform: translateY(0px) scale(1); }
            to   { transform: translateY(40px) scale(1.08); }
        }

        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="antialiased text-gray-900">

    <!-- Background Orbs -->
    <div class="bg-orb bg-orb-1"></div>
    <div class="bg-orb bg-orb-2"></div>
    <div class="bg-orb bg-orb-3"></div>

    <!-- Main Content -->
    <div class="relative z-10 min-h-screen flex flex-col items-center justify-start px-4 py-8 sm:py-12">
        <!-- Logo Header -->
        <div class="text-center mb-6">
            <a href="{{ route('tracking.index') }}" class="inline-block transform hover:scale-105 transition-transform duration-300">
                <img src="{{ asset('images/logo.png') }}" alt="Shoe Workshop Logo" class="h-16 sm:h-20 mx-auto drop-shadow-xl">
            </a>
        </div>

        <!-- Card Container -->
        <div class="w-full max-w-md">
            {{ $slot }}
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center text-gray-400 text-xs font-medium tracking-wide">
            <p>© {{ date('Y') }} Shoe Workshop · <a href="{{ route('tracking.index') }}" class="hover:text-gray-600 transition-colors underline underline-offset-2">Lacak Pesanan</a></p>
        </div>
    </div>

    @livewireScripts
</body>
</html>
