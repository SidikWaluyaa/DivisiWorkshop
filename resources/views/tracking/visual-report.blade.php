<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <title>Visual Transformation - {{ $order->spk_number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0f172a;
            color: #f8fafc;
        }
        [x-cloak] { display: none !important; }
        .glass {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .text-gradient {
            background: linear-gradient(to right, #2dd4bf, #22d3ee);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .glow {
            box-shadow: 0 0 20px rgba(45, 212, 191, 0.2);
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
    
    <div class="min-h-screen pb-20">
        <!-- Top Navigation / Logo -->
        <div class="absolute top-0 left-0 right-0 p-6 z-50 flex justify-between items-center">
            <a href="{{ route('tracking.index') }}" class="glass p-2 rounded-xl hover:bg-white/20 transition-all group">
                <svg class="w-6 h-6 text-white group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-10 md:h-12 drop-shadow-lg">
            <div class="w-10"></div> <!-- Spacer -->
        </div>

        <!-- Header -->
        <div class="relative h-[50vh] overflow-hidden flex items-center justify-center">
            <div class="absolute inset-0 bg-gradient-to-b from-teal-500/30 via-[#0f172a]/80 to-[#0f172a] z-10"></div>
            @php
                $headerPhoto = $afterPhotos->last() ?? $beforePhotos->first();
            @endphp
            @if($headerPhoto)
                <img src="{{ $headerPhoto->photo_url }}" class="absolute inset-0 w-full h-full object-cover opacity-40 blur-[2px] scale-110" alt="Header Background">
            @endif
            
            <div class="relative z-20 text-center px-6 mt-10">
                <div class="inline-flex items-center gap-3 px-4 py-1.5 bg-black/40 backdrop-blur-xl rounded-full border border-white/10 mb-8 shadow-2xl">
                    <span class="w-2 h-2 rounded-full bg-teal-400 animate-pulse shadow-[0_0_10px_rgba(45,212,191,0.8)]"></span>
                    <span class="text-[10px] font-extrabold uppercase tracking-[0.3em] text-teal-50">Visual Transformation Report</span>
                </div>
                <h1 class="text-5xl md:text-8xl font-black tracking-tighter mb-4">
                    THE <span class="text-gradient">GLOW UP</span>
                </h1>
                <div class="flex flex-col md:flex-row items-center justify-center gap-4 text-gray-400 font-bold tracking-widest text-xs md:text-sm">
                    <span class="px-3 py-1 bg-white/5 rounded-lg border border-white/10 uppercase italic">#{{ $order->spk_number }}</span>
                    <span class="hidden md:block opacity-30">•</span>
                    <span class="uppercase">{{ $order->shoe_brand }}</span>
                </div>
            </div>
        </div>

        <div class="max-w-6xl mx-auto px-6 -mt-16 relative z-30">
            <!-- Info Card -->
            <div class="glass rounded-[2rem] p-8 md:p-10 mb-16 glow relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-64 h-64 bg-teal-500/10 rounded-full blur-3xl -mr-32 -mt-32 group-hover:bg-teal-500/20 transition-all duration-1000"></div>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-center relative z-10">
                    <div class="lg:col-span-2">
                        <h2 class="text-3xl font-black mb-4 tracking-tight">Halo, <span class="text-teal-400 italic">{{ $order->customer_name }}!</span></h2>
                        <p class="text-gray-300 leading-relaxed text-lg font-medium opacity-90">
                            Terima kasih telah mempercayakan perawatan sepatu kesayangan Anda kepada tim ahli kami. Di bawah ini adalah dokumentasi lengkap proses transformasi sepatu Anda.
                        </p>
                    </div>
                    <div class="flex flex-col items-center lg:items-end">
                        <div class="text-center lg:text-right">
                            <span class="text-[10px] font-black text-teal-400 uppercase tracking-[0.2em] block mb-2 opacity-70">Status Pengerjaan</span>
                            <div class="inline-flex items-center gap-3 px-6 py-3 bg-teal-500/10 text-teal-400 rounded-2xl font-black border border-teal-500/30 shadow-inner group-hover:shadow-teal-500/20 transition-all">
                                <span class="w-2.5 h-2.5 rounded-full bg-teal-400 animate-pulse"></span>
                                <span class="uppercase tracking-widest">{{ str_replace('_', ' ', $order->status->value ?? $order->status) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- AFTER GALLERY (The Hero) -->
            <section class="mb-24">
                <div class="flex items-center justify-between mb-10">
                    <div>
                        <h3 class="text-4xl font-black italic tracking-tighter">THE <span class="text-teal-400">RESULT</span></h3>
                        <div class="flex items-center gap-2 mt-1">
                            <div class="w-8 h-[2px] bg-teal-500"></div>
                            <p class="text-gray-500 text-xs font-bold uppercase tracking-widest">Masterpiece Documentation</p>
                        </div>
                    </div>
                </div>

                @if($afterPhotos->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($afterPhotos as $photo)
                            <div class="group relative aspect-[4/5] rounded-[2.5rem] overflow-hidden glass border-white/5 cursor-zoom-in shadow-2xl"
                                 @click="openLightbox('{{ $photo->photo_url }}', 'Hasil Akhir - {{ $order->shoe_brand }}')">
                                <img src="{{ $photo->photo_url }}" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" alt="After Photo">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex items-end p-8">
                                    <div class="translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                                        <p class="text-[10px] font-black text-teal-400 uppercase tracking-widest mb-1">View Detail</p>
                                        <p class="text-lg font-bold text-white tracking-tight">Hasil Akhir Sempurna</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="glass rounded-[3rem] p-16 text-center border-dashed border-2 border-white/10 relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-teal-500/5 to-transparent"></div>
                        <div class="relative z-10">
                            <div class="w-24 h-24 bg-teal-500/10 rounded-full flex items-center justify-center mx-auto mb-8 ring-1 ring-teal-500/20">
                                <svg class="w-12 h-12 text-teal-500/50 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            </div>
                            <h4 class="text-2xl font-black mb-4 tracking-tight uppercase">Sabar Ya! <span class="text-teal-400">Sedang Diproses...</span></h4>
                            <p class="text-gray-400 max-w-lg mx-auto leading-relaxed font-medium">
                                Tim kami sedang melakukan tahap akhir pengerjaan dan dokumentasi QC. Hasil transformasi sepatu Anda akan segera muncul di sini dalam waktu dekat.
                            </p>
                        </div>
                    </div>
                @endif
            </section>

            <!-- BEFORE GALLERY -->
            <section>
                <div class="flex items-center justify-between mb-10">
                    <div>
                        <h3 class="text-3xl font-black italic tracking-tighter opacity-30 uppercase">The <span class="text-gray-400">Origins</span></h3>
                        <div class="flex items-center gap-2 mt-1">
                            <div class="w-6 h-[2px] bg-gray-500/30"></div>
                            <p class="text-gray-600 text-[10px] font-bold uppercase tracking-[0.2em]">Initial Condition Documentation</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    @foreach($beforePhotos as $photo)
                        <div class="group relative aspect-square rounded-[2rem] overflow-hidden glass border-white/5 cursor-zoom-in grayscale hover:grayscale-0 transition-all duration-700 hover:shadow-xl"
                             @click="openLightbox('{{ $photo->photo_url }}', 'Kondisi Awal - {{ $order->shoe_brand }}')">
                            <img src="{{ $photo->photo_url }}" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" alt="Before Photo">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex items-center justify-center">
                                <span class="px-4 py-1.5 bg-white/10 backdrop-blur-xl rounded-full border border-white/20 text-[9px] font-black uppercase tracking-widest">{{ str_replace('_', ' ', $photo->step) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
    </div>

    <!-- Footer Action -->
    <div class="fixed bottom-8 left-1/2 -translate-x-1/2 z-[100] w-[95vw] max-w-lg">
        <div class="glass rounded-3xl p-5 flex items-center justify-between glow shadow-2xl border-white/20">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-white p-2 flex items-center justify-center shadow-inner overflow-hidden">
                    <img src="{{ asset('images/logo.png') }}" class="w-full h-full object-contain" alt="Logo Icon">
                </div>
                <div>
                    <p class="text-[10px] font-black text-teal-400 uppercase tracking-[0.2em]">Workshop Partner</p>
                    <p class="text-sm font-black tracking-tight">Terima kasih telah berkunjung!</p>
                </div>
            </div>
            <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', config('app.contact_whatsapp', '628123456789'))) }}" 
               class="group relative flex items-center gap-2 bg-teal-500 hover:bg-teal-400 text-white px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all shadow-xl shadow-teal-500/40 hover:-translate-y-1">
                <span>Hubungi CS</span>
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.592 2.654-.694c1.003.545 1.987.96 3.218.96 3.183 0 5.768-2.587 5.768-5.765.001-3.187-2.575-5.756-5.78-5.756zm0 0"></path><path d="M12 2C6.48 2 2 6.48 2 12c0 1.822.487 3.53 1.338 5.008l-1.42 5.236 5.348-1.405A9.957 9.957 0 0012 22c5.52 0 10-4.48 10-10S17.52 2 12 2zm0 18c-1.72 0-3.284-.6-4.593-1.603l-1.98.52.54-1.906A8.02 8.02 0 014 12c0-4.411 3.589-8 8-8s8 3.589 8 8-3.589 8-8 8z"></path></svg>
            </a>
        </div>
    </div>

    <!-- Lightbox Modal -->
    <div x-show="showLightbox" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-black/95 backdrop-blur-md"
         @keydown.escape.window="showLightbox = false">
        
        <button @click="showLightbox = false" class="absolute top-6 right-6 text-white/50 hover:text-white transition-colors z-[1000]">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <div class="relative max-w-5xl w-full flex flex-col items-center" @click.away="showLightbox = false">
            <img :src="lightboxImage" class="max-h-[80vh] w-auto rounded-2xl shadow-2xl border border-white/10" alt="Full Image">
            <p x-text="lightboxCaption" class="mt-6 text-white font-black text-xl tracking-wide bg-white/10 px-8 py-3 rounded-full backdrop-blur-md border border-white/10"></p>
        </div>
    </div>

</body>
</html>
