<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Masuk ke Portal | {{ config('app.name', 'ShoeWorkshop') }}</title>
    <link class="rounded" rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased h-full bg-gray-50 dark:bg-gray-950 flex">

    <!-- LEFT SIDE: Branding, Live Cards & Radial Glow (Hidden on Mobile) -->
    <div class="hidden md:flex md:w-1/2 relative bg-gradient-to-br from-[#1a4d3e] to-[#0d2b22] flex-col justify-between p-12 overflow-hidden select-none">
        
        <!-- Subtle Animated Glowing Background Orbs -->
        <div class="absolute top-[-20%] left-[-20%] w-[70%] h-[70%] bg-emerald-500/10 rounded-full blur-[120px] animate-pulse"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[60%] h-[60%] bg-[#f5a623]/10 rounded-full blur-[100px] animate-pulse" style="animation-delay: 2s;"></div>

        <!-- Top Header: Logo + App Name -->
        <div class="relative z-10 flex items-center gap-3">
            <img src="{{ asset('images/logo.png') }}" class="h-9 w-auto object-contain filter brightness-110 drop-shadow-md" alt="ShoeWorkshop Logo">
            <span class="text-white font-extrabold text-lg tracking-wider uppercase">Shoe<span class="text-[#f5a623]">Workshop</span></span>
        </div>

        <!-- Middle Content: Animated Center Logo & Floating Simulated Cards -->
        <div class="relative w-full h-[400px] flex items-center justify-center my-auto">
            
            <!-- Radial pulse background ring -->
            <div class="absolute w-56 h-56 rounded-full bg-emerald-500/5 border border-emerald-500/10 animate-ping" style="animation-duration: 3s;"></div>
            
            <!-- Central Premium Logo Ring -->
            <div class="relative z-10 flex flex-col items-center justify-center p-6 rounded-full bg-[#133e32]/90 border border-emerald-500/20 shadow-2xl w-44 h-44 text-center transform hover:scale-105 transition-all duration-300">
                <img src="{{ asset('images/logo.png') }}" class="h-16 w-auto object-contain filter brightness-110 drop-shadow-[0_4px_10px_rgba(245,166,35,0.4)] mb-2" alt="Shoe Workshop Logo">
                <span class="text-white text-xs font-black tracking-widest uppercase">SHOE</span>
                <span class="text-[#f5a623] text-[10px] font-black tracking-widest uppercase -mt-0.5">WORKSHOP</span>
            </div>

            <!-- Card 1: Top Left - Cuci / Washing -->
            <div class="absolute top-2 left-2 bg-white/10 backdrop-blur-md border border-white/15 p-4 rounded-2xl shadow-xl w-64 transform -rotate-2 hover:rotate-0 hover:scale-105 transition-all duration-300">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-black text-emerald-400 tracking-wider">#WO-842</span>
                    <span class="px-2 py-0.5 text-[9px] font-black bg-[#f5a623]/20 text-[#f5a623] rounded-full">CUCI (WASHING)</span>
                </div>
                <p class="text-white text-xs font-semibold leading-snug">Pembersihan & deep cleaning treatment dari Surabaya</p>
                <span class="text-[9px] text-gray-300 block mt-2 text-right">2 jam lalu</span>
            </div>

            <!-- Card 2: Top Right - Reparasi -->
            <div class="absolute top-16 right-2 bg-white/10 backdrop-blur-md border border-white/15 p-4 rounded-2xl shadow-xl w-64 transform rotate-3 hover:rotate-0 hover:scale-105 transition-all duration-300">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-black text-indigo-300 tracking-wider">#WO-118</span>
                    <span class="px-2 py-0.5 text-[9px] font-black bg-emerald-500/20 text-emerald-400 rounded-full">SELESAI</span>
                </div>
                <p class="text-white text-xs font-semibold leading-snug">Reparasi sol sepatu Adidas Samba Bandung</p>
                <span class="text-[9px] text-gray-300 block mt-2 text-right">5 jam lalu</span>
            </div>

            <!-- Card 3: Bottom Left - Rekondisi / upper -->
            <div class="absolute bottom-6 left-8 bg-white/10 backdrop-blur-md border border-white/15 p-4 rounded-2xl shadow-xl w-64 transform rotate-1 hover:rotate-0 hover:scale-105 transition-all duration-300">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-black text-emerald-400 tracking-wider">#WO-031</span>
                    <span class="px-2 py-0.5 text-[9px] font-black bg-[#ff8c00]/20 text-[#ff8c00] rounded-full">PROSES JAHIT</span>
                </div>
                <p class="text-white text-xs font-semibold leading-snug">Rekondisi upper & repaint sepatu Nike Air Jordan</p>
                <span class="text-[9px] text-gray-300 block mt-2 text-right">1 hari lalu</span>
            </div>
        </div>

        <!-- Footer Section on Left Pane -->
        <div class="relative z-10">
            <h3 class="text-white font-extrabold text-2xl tracking-tight leading-tight">Satu tempat untuk setiap <span class="text-[#f5a623] underline decoration-wavy decoration-1 underline-offset-4">Treatment</span> & <span class="text-emerald-400">Reparasi</span>.</h3>
            <p class="text-gray-300 text-sm mt-2 leading-relaxed">Sistem manajemen workshop terintegrasi, pelacakan real-time, dan pemantauan kualitas pengerjaan sepatu dari awal hingga selesai.</p>
        </div>

    </div>

    <!-- RIGHT SIDE: Clean Premium Login Form (Full-screen on mobile) -->
    <div class="w-full md:w-1/2 flex flex-col justify-center items-center p-8 sm:p-12 md:p-16 bg-white dark:bg-gray-900 transition-colors duration-200">
        
        <div class="w-full max-w-md">

            <!-- Mobile Only Header Brand Logo -->
            <div class="flex md:hidden items-center justify-center gap-2 mb-8">
                <img src="{{ asset('images/logo.png') }}" class="h-10 w-auto object-contain filter drop-shadow-sm" alt="ShoeWorkshop Logo">
                <span class="text-gray-900 dark:text-white font-black text-lg tracking-wider uppercase">Shoe<span class="text-emerald-600">Workshop</span></span>
            </div>

            <!-- Page Titles -->
            <div class="mb-8">
                <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Selamat datang kembali</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Masuk untuk mengelola alur pengerjaan dan melacak status reparasi sepatu.</p>
            </div>

            <!-- Laravel Session Status (e.g. Password Reset Message) -->
            @if (session('status'))
                <div class="mb-4 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900/50 text-sm font-medium text-emerald-800 dark:text-emerald-300">
                    {{ session('status') }}
                </div>
            @endif

            {{-- ==================== DEACTIVATED ACCOUNT ALERT ==================== --}}
            @if (session('deactivated'))
                <div x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)" x-show="show"
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 scale-90 -translate-y-4"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     class="mb-6 relative overflow-hidden rounded-2xl bg-gradient-to-br from-red-500 via-rose-600 to-red-700 p-6 shadow-2xl shadow-red-500/30 ring-1 ring-red-400/30">
                    
                    {{-- Decorative background pattern --}}
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-white rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                        <div class="absolute bottom-0 left-0 w-32 h-32 bg-white rounded-full blur-3xl translate-y-1/2 -translate-x-1/2"></div>
                    </div>

                    <div class="relative z-10 flex flex-col items-center text-center">
                        {{-- Animated Shield Icon --}}
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mb-4 ring-4 ring-white/10 shadow-lg"
                             x-data="{ pulse: false }" x-init="setInterval(() => { pulse = true; setTimeout(() => pulse = false, 600); }, 2000)"
                             :class="pulse ? 'scale-110' : 'scale-100'" class="transition-transform duration-300">
                            <svg class="w-8 h-8 text-white drop-shadow-md" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                        </div>

                        {{-- Title --}}
                        <h3 class="text-white text-lg font-extrabold tracking-tight">Akun Anda Dinonaktifkan</h3>
                        
                        {{-- Description --}}
                        <p class="text-red-100 text-sm mt-2 max-w-xs leading-relaxed">
                            Akses login Anda telah dinonaktifkan oleh Administrator sistem. Anda tidak dapat masuk sampai akun diaktifkan kembali.
                        </p>

                        {{-- Divider --}}
                        <div class="w-12 h-0.5 bg-white/20 rounded-full my-4"></div>

                        {{-- CTA: Contact Admin --}}
                        <a href="https://wa.me/62895339939800?text=Halo%20Admin%2C%20akun%20saya%20dinonaktifkan.%20Mohon%20bantuannya%20untuk%20mengaktifkan%20kembali." 
                           target="_blank"
                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-red-600 text-sm font-bold rounded-xl shadow-lg hover:shadow-xl hover:bg-red-50 transform hover:-translate-y-0.5 transition-all duration-200">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            Hubungi Administrator via WhatsApp
                        </a>
                    </div>
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email Input Field -->
                <div>
                    <label for="email" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Alamat Email</label>
                    <div class="mt-1.5 relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <!-- Envelope SVG Icon -->
                            <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                            </svg>
                        </div>
                        <input type="email" name="email" id="email" required autofocus
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-sm"
                            placeholder="email@shoeworkshop.com" value="{{ old('email') }}" autocomplete="username">
                    </div>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" fill-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password Input Field -->
                <div>
                    <label for="password" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Kata Sandi Aman</label>
                    <div class="mt-1.5 relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <!-- Lock SVG Icon -->
                            <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input type="password" name="password" id="password" required autocomplete="current-password"
                            class="block w-full pl-10 pr-10 py-3 border border-gray-300 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-sm"
                            placeholder="••••••••">
                        <!-- Show / Hide Password Toggle Button -->
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-emerald-500 dark:text-gray-500 dark:hover:text-emerald-400 transition-colors duration-150">
                            <!-- Eye Open Icon -->
                            <svg id="eyeOpenIcon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <!-- Eye Closed Icon -->
                            <svg id="eyeCloseIcon" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 5.656m0 0l-8.485 8.485m11.799-11.799L21.414 2.586M15 12a3 3 0 11-6 0 3 3 0 016 0zm-9 9a9 9 0 0118 0" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" fill-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Remember Me Checkbox & Forgot Password Link -->
                <div class="flex items-center justify-between mt-4">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer select-none">
                        <input id="remember_me" type="checkbox" name="remember"
                            class="rounded border-gray-300 dark:border-gray-700 text-emerald-600 shadow-sm focus:ring-emerald-500 focus:ring-offset-white dark:focus:ring-offset-gray-900 transition duration-150 ease-in-out cursor-pointer">
                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400 font-medium">Ingat saya</span>
                    </label>
                    
                    @if (Route::has('password.request'))
                        <a class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition duration-150" href="{{ route('password.request') }}">
                            Lupa Kata Sandi?
                        </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" id="btnSubmit" class="w-full flex justify-center items-center px-4 py-3.5 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-200 transform hover:-translate-y-0.5">
                        <span id="btnText">Masuk ke Portal</span>
                        <svg id="btnArrow" class="ms-2 h-4 w-4 transform transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                        <!-- Inline Loading Spinner -->
                        <svg id="btnSpinner" class="animate-spin ml-2 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </form>

            <!-- Divider -->
            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white dark:bg-gray-900 text-gray-500 dark:text-gray-400">atau</span>
                </div>
            </div>

            <!-- Register Redirect Footer -->
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Belum punya akun? 
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="font-bold text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors duration-150">Daftar Akun Baru</a>
                    @else
                        <a href="https://wa.me/62895339939800?text=Halo%20Admin%20ShoeWorkshop,%20saya%20memerlukan%20akses%20masuk%20ke%20dalam%20Sistem%20Workshop" target="_blank" class="font-bold text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors duration-150">Hubungi Admin</a>
                    @endif
                </p>
            </div>

        </div>
    </div>

    <!-- Client-Side UX Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Password Visibility Toggle Logic
            const togglePasswordBtn = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeOpenIcon = document.getElementById('eyeOpenIcon');
            const eyeCloseIcon = document.getElementById('eyeCloseIcon');

            if (togglePasswordBtn && passwordInput) {
                togglePasswordBtn.addEventListener('click', function () {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    
                    if (isPassword) {
                        eyeOpenIcon.classList.add('hidden');
                        eyeCloseIcon.classList.remove('hidden');
                    } else {
                        eyeOpenIcon.classList.remove('hidden');
                        eyeCloseIcon.classList.add('hidden');
                    }
                });
            }

            // 2. Remember Me Email UX Persistence Logic
            const emailInput = document.getElementById('email');
            const rememberMeCheckbox = document.getElementById('remember_me');
            const loginForm = document.querySelector('form');

            // Load remembered email if present
            try {
                const rememberedEmail = localStorage.getItem('remembered_email');
                if (rememberedEmail && emailInput && rememberMeCheckbox) {
                    emailInput.value = rememberedEmail;
                    rememberMeCheckbox.checked = true;
                }
            } catch (e) {
                console.warn('LocalStorage is blocked or unsupported.', e);
            }

            // Handle Form Submission for Remember Me and Loading Transition
            if (loginForm) {
                loginForm.addEventListener('submit', function () {
                    // Save or clear email address on client device
                    if (rememberMeCheckbox && emailInput) {
                        try {
                            if (rememberMeCheckbox.checked) {
                                localStorage.setItem('remembered_email', emailInput.value);
                            } else {
                                localStorage.removeItem('remembered_email');
                            }
                        } catch (e) {
                            console.warn('LocalStorage save failed.', e);
                        }
                    }

                    // Animate Submit Button to Loading State
                    const btnText = document.getElementById('btnText');
                    const btnArrow = document.getElementById('btnArrow');
                    const btnSpinner = document.getElementById('btnSpinner');
                    const btnSubmit = document.getElementById('btnSubmit');

                    if (btnText && btnSpinner && btnArrow && btnSubmit) {
                        btnText.textContent = 'Menghubungkan...';
                        btnArrow.classList.add('hidden');
                        btnSpinner.classList.remove('hidden');
                        btnSubmit.style.pointerEvents = 'none';
                        btnSubmit.classList.add('opacity-85');
                    }
                });
            }
        });
    </script>
</body>
</html>
