<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <title>Visual Transformation - {{ $order->spk_number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/@alpinejs/intersect@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;400;600;800&family=Bebas+Neue&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        
        :root {
            --accent: #2dd4bf;
            --accent-glow: rgba(45, 212, 191, 0.4);
            --bg-deep: #020617;
            --bg-card: rgba(15, 23, 42, 0.6);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-deep);
            color: #f8fafc;
            overflow-x: hidden;
        }

        .font-bebas { font-family: 'Bebas Neue', sans-serif; }

        .cinematic-bg {
            position: fixed;
            inset: 0;
            background: 
                radial-gradient(circle at 20% 30%, rgba(45, 212, 191, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(34, 211, 238, 0.05) 0%, transparent 50%);
            z-index: -1;
        }

        .glass-card {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .text-glow {
            text-shadow: 0 0 20px var(--accent-glow);
        }

        .btn-premium {
            background: linear-gradient(135deg, var(--accent) 0%, #0ea5e9 100%);
            box-shadow: 0 10px 20px -5px var(--accent-glow);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-premium:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 15px 30px -5px var(--accent-glow);
        }

        .photo-frame {
            position: relative;
            border-radius: 2rem;
            overflow: hidden;
            background: #1e293b;
            transition: all 0.5s ease;
        }

        .photo-frame::after {
            content: '';
            position: absolute;
            inset: 0;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 2rem;
            pointer-events: none;
        }

        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        .status-pill {
            background: rgba(45, 212, 191, 0.1);
            border: 1px solid rgba(45, 212, 191, 0.2);
            color: var(--accent);
            text-shadow: 0 0 10px var(--accent-glow);
        }
    </style>
</head>
<body class="antialiased" 
      x-data="{ 
        showLightbox: false, 
        lightboxImage: '', 
        lightboxCaption: '',
        openLightbox(url, caption) {
            this.lightboxImage = url;
            this.lightboxCaption = caption;
            this.showLightbox = true;
        }
      }">
    
    <div class="cinematic-bg"></div>

    <div class="min-h-screen relative">
        <!-- Top Nav -->
        <nav class="fixed top-0 left-0 right-0 p-4 md:p-8 z-[100] bg-slate-950/40 backdrop-blur-2xl border-b border-white/5">
            <div class="max-w-7xl mx-auto w-full flex justify-between items-center px-4">
                <a href="{{ route('tracking.index') }}" class="glass-card p-2 md:p-3 rounded-xl md:rounded-2xl hover:bg-white/10 transition-all group">
                    <svg class="w-6 h-6 text-white group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-10 md:h-16 drop-shadow-[0_0_20px_rgba(255,255,255,0.15)]">
                <div class="hidden md:block">
                    <span class="status-pill px-6 py-2.5 rounded-full text-[11px] font-black uppercase tracking-[0.4em]">Official Report</span>
                </div>
                <div class="md:hidden w-10"></div>
            </div>
        </nav>

        <!-- Hero Section -->
        <header class="relative pt-48 md:pt-72 pb-16 md:pb-24 px-4 md:px-6 overflow-hidden">
            <div class="max-w-7xl mx-auto flex flex-col items-center text-center">
                <div x-intersect="$el.classList.add('opacity-100', 'translate-y-0')" 
                     class="transition-all duration-1000 transform opacity-0 translate-y-10">
                    <h2 class="font-bebas text-teal-400 text-lg md:text-3xl tracking-[0.4em] md:tracking-[0.5em] mb-2 md:mb-4 opacity-80">THE TRANSFORMATION</h2>
                    <h1 class="font-bebas text-5xl sm:text-7xl md:text-[10rem] leading-[0.9] tracking-tighter mb-8 md:mb-12">
                        THE <span class="text-white">GLOW</span> <span class="text-teal-400 italic">UP</span>
                    </h1>
                </div>

                <div class="glass-card rounded-[2rem] md:rounded-[2.5rem] p-1 max-w-2xl w-full mx-auto">
                    <div class="bg-slate-900/40 rounded-[1.8rem] md:rounded-[2rem] p-5 md:p-10 text-left border border-white/5 relative overflow-hidden group">
                        <div class="absolute -top-10 -right-10 w-32 md:w-40 h-32 md:h-40 bg-teal-500/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-1000"></div>
                        
                        <div class="flex flex-col sm:flex-row justify-between items-start gap-4 mb-6 relative z-10">
                            <div>
                                <h3 class="text-2xl md:text-3xl font-black mb-2 tracking-tight">Halo, <span class="text-teal-400">{{ $order->customer_name }}!</span></h3>
                                <div class="flex flex-wrap items-center gap-2 md:gap-3">
                                    <span class="px-2 md:px-3 py-1 bg-white/5 rounded-lg border border-white/10 text-[9px] md:text-[10px] font-bold text-gray-400 uppercase tracking-widest italic">#{{ $order->spk_number }}</span>
                                    <span class="hidden sm:inline text-gray-600 text-xs font-bold">•</span>
                                    <span class="text-gray-400 text-[10px] md:text-xs font-bold uppercase tracking-widest">{{ $order->shoe_brand }}</span>
                                </div>
                            </div>
                            <div class="status-pill px-4 md:px-5 py-1.5 md:py-2 rounded-xl md:rounded-2xl text-[9px] md:text-[10px] font-black uppercase tracking-widest flex items-center gap-2 self-start sm:self-auto">
                                <span class="w-1.5 md:w-2 h-1.5 md:h-2 rounded-full bg-teal-400 animate-pulse"></span>
                                {{ str_replace('_', ' ', $order->status->value ?? $order->status) }}
                            </div>
                        </div>
                        <p class="text-gray-300 text-sm md:text-base leading-relaxed font-medium opacity-80 relative z-10">
                            Kami sangat senang mengabarkan bahwa sepatu kesayangan Anda telah melalui proses perawatan intensif. Lihat hasil transformasinya di bawah ini.
                        </p>
                    </div>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-4 md:px-6 pb-40 space-y-20 md:space-y-32">
            
            <!-- RESULT SECTION -->
            <section>
                <div class="flex items-end gap-4 md:gap-6 mb-8 md:mb-12">
                    <div class="flex-shrink-0">
                        <h3 class="font-bebas text-5xl md:text-8xl leading-none text-white opacity-90">THE <span class="text-teal-400">RESULT</span></h3>
                    </div>
                    <div class="h-px w-full bg-gradient-to-r from-teal-500/30 to-transparent mb-2 md:mb-3"></div>
                </div>

                @if($afterPhotos->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-10">
                        @foreach($afterPhotos as $photo)
                            <div class="photo-frame aspect-[4/5] sm:aspect-[3/4] group cursor-zoom-in"
                                 @click="openLightbox('{{ $photo->photo_url }}', 'Hasil Akhir - {{ $order->shoe_brand }}')">
                                <img src="{{ $photo->photo_url }}" class="w-full h-full object-cover transition-transform duration-[1.5s] group-hover:scale-110" alt="After Photo">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent opacity-60 sm:opacity-0 group-hover:opacity-100 transition-all duration-500 flex items-end p-6 md:p-10">
                                    <div class="translate-y-0 sm:translate-y-10 group-hover:translate-y-0 transition-transform duration-500">
                                        <p class="text-teal-400 text-[9px] md:text-[10px] font-black uppercase tracking-[0.3em] mb-1 md:mb-2">Final Masterpiece</p>
                                        <h4 class="text-xl md:text-2xl font-bold text-white">Sudah Glowing!</h4>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="glass-card rounded-[2.5rem] md:rounded-[3rem] p-10 md:p-20 text-center relative overflow-hidden">
                        <div class="absolute inset-0 opacity-20">
                            <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,_var(--accent-glow)_0%,_transparent_70%)]"></div>
                        </div>
                        <div class="relative z-10 flex flex-col items-center">
                            <div class="w-20 md:w-32 h-20 md:h-32 bg-slate-900/50 rounded-2xl md:rounded-[2.5rem] flex items-center justify-center mb-6 md:mb-10 border border-white/5 floating">
                                <svg class="w-10 md:w-16 h-10 md:h-16 text-teal-500/30 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            </div>
                            <h4 class="font-bebas text-4xl md:text-7xl mb-4 tracking-tight uppercase">HAMPIR <span class="text-teal-400">SEMPURNA</span></h4>
                            <p class="text-gray-400 max-w-xl mx-auto text-sm md:text-lg leading-relaxed font-medium">
                                Tim ahli kami sedang memberikan sentuhan akhir dan dokumentasi QC. Hasil transformasi sepatu Anda akan muncul di galeri ini sesaat lagi.
                            </p>
                        </div>
                    </div>
                @endif
            </section>

            <!-- ORIGINS SECTION -->
            <section>
                <div class="flex items-end gap-4 md:gap-6 mb-8 md:mb-12">
                    <div class="flex-shrink-0">
                        <h3 class="font-bebas text-4xl md:text-7xl leading-none text-gray-500 italic opacity-50 uppercase">The <span class="text-gray-400">Origins</span></h3>
                    </div>
                    <div class="h-px w-full bg-gradient-to-r from-gray-500/20 to-transparent mb-2 md:mb-3"></div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-8">
                    @foreach($beforePhotos as $photo)
                        <div class="photo-frame aspect-square group cursor-zoom-in grayscale hover:grayscale-0 transition-all duration-700"
                             @click="openLightbox('{{ $photo->photo_url }}', 'Kondisi Awal - {{ $order->shoe_brand }}')">
                            <img src="{{ $photo->photo_url }}" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" alt="Before Photo">
                            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex items-center justify-center">
                                <span class="px-3 md:px-5 py-1.5 md:py-2 bg-white/10 backdrop-blur-xl rounded-xl md:rounded-2xl border border-white/20 text-[8px] md:text-[10px] font-black uppercase tracking-widest text-center">{{ str_replace('_', ' ', $photo->step) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

        </main>


        <!-- Lightbox -->
        <div x-show="showLightbox" 
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="fixed inset-0 z-[1000] flex items-center justify-center p-4 bg-slate-950/98 backdrop-blur-2xl"
             @keydown.escape.window="showLightbox = false">
            
            <button @click="showLightbox = false" class="absolute top-8 right-8 text-white/50 hover:text-white transition-all hover:scale-110">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <div class="relative max-w-6xl w-full flex flex-col items-center" @click.away="showLightbox = false">
                <img :src="lightboxImage" class="max-h-[80vh] w-auto rounded-[3rem] shadow-[0_0_100px_rgba(0,0,0,0.5)] border border-white/10" alt="Full Image">
                <div class="mt-10 px-10 py-4 glass-card rounded-full border-white/20">
                    <p x-text="lightboxCaption" class="text-white font-black text-2xl tracking-tighter italic"></p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
