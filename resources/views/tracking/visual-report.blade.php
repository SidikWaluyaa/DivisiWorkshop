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
        <!-- Header -->
        <div class="relative h-[40vh] overflow-hidden flex items-center justify-center">
            <div class="absolute inset-0 bg-gradient-to-b from-teal-500/20 to-[#0f172a] z-10"></div>
            @php
                $headerPhoto = $afterPhotos->last() ?? $beforePhotos->first();
            @endphp
            @if($headerPhoto)
                <img src="{{ $headerPhoto->photo_url }}" class="absolute inset-0 w-full h-full object-cover opacity-30 blur-sm scale-110" alt="Header Background">
            @endif
            
            <div class="relative z-20 text-center px-6">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 backdrop-blur-md rounded-full border border-white/20 mb-6">
                    <span class="w-2 h-2 rounded-full bg-teal-400 animate-pulse"></span>
                    <span class="text-[10px] font-bold uppercase tracking-[0.2em]">Visual Transformation Report</span>
                </div>
                <h1 class="text-4xl md:text-6xl font-black tracking-tighter mb-2">
                    THE <span class="text-gradient">GLOW UP</span>
                </h1>
                <p class="text-gray-400 font-medium tracking-wide">SPK #{{ $order->spk_number }} • {{ $order->shoe_brand }}</p>
            </div>
        </div>

        <div class="max-w-6xl mx-auto px-6 -mt-20 relative z-30">
            <!-- Info Card -->
            <div class="glass rounded-3xl p-8 mb-12 glow">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-center">
                    <div class="md:col-span-2">
                        <h2 class="text-2xl font-bold mb-2">Halo, {{ $order->customer_name }}!</h2>
                        <p class="text-gray-400 leading-relaxed">
                            Terima kasih telah mempercayakan perawatan sepatu Anda kepada kami. Di bawah ini adalah dokumentasi lengkap transformasi sepatu Anda dari awal masuk hingga selesai dikerjakan oleh tim ahli kami.
                        </p>
                    </div>
                    <div class="flex flex-col items-center md:items-end">
                        <div class="text-right">
                            <span class="text-[10px] font-bold text-teal-400 uppercase tracking-widest block mb-1">Status Pengerjaan</span>
                            <span class="px-4 py-2 bg-teal-500/20 text-teal-400 rounded-xl font-bold border border-teal-500/30">
                                {{ str_replace('_', ' ', $order->status->value ?? $order->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- AFTER GALLERY (The Hero) -->
            <section class="mb-20">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-3xl font-black italic tracking-tight">THE <span class="text-teal-400">RESULT</span></h3>
                        <p class="text-gray-400 text-sm">Hasil akhir pengerjaan tim workshop</p>
                    </div>
                    <div class="h-px flex-1 bg-gradient-to-r from-teal-500/50 to-transparent ml-8 hidden md:block"></div>
                </div>

                @if($afterPhotos->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($afterPhotos as $photo)
                            <div class="group relative aspect-square rounded-3xl overflow-hidden glass border-white/5 cursor-zoom-in"
                                 @click="openLightbox('{{ $photo->photo_url }}', 'Hasil Akhir - {{ $order->shoe_brand }}')">
                                <img src="{{ $photo->photo_url }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="After Photo">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex items-end p-6">
                                    <span class="text-xs font-bold tracking-widest uppercase">Lihat Detail</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="glass rounded-3xl p-12 text-center border-dashed border-2 border-white/10">
                        <div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-gray-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h4 class="text-xl font-bold mb-2">Hampir Selesai!</h4>
                        <p class="text-gray-400 max-w-md mx-auto">
                            Tim kami sedang melakukan tahap akhir pengerjaan dan dokumentasi. Foto hasil transformasi sepatu Anda akan segera muncul di sini setelah proses Quality Control (QC) selesai.
                        </p>
                    </div>
                @endif
            </section>

            <!-- BEFORE GALLERY -->
            <section>
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-3xl font-black italic tracking-tight opacity-50 uppercase">The <span class="text-gray-400">Condition</span></h3>
                        <p class="text-gray-500 text-sm">Kondisi awal saat sepatu kami terima</p>
                    </div>
                    <div class="h-px flex-1 bg-gradient-to-r from-gray-500/20 to-transparent ml-8 hidden md:block"></div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($beforePhotos as $photo)
                        <div class="group relative aspect-square rounded-2xl overflow-hidden glass border-white/5 cursor-zoom-in grayscale hover:grayscale-0 transition-all duration-500"
                             @click="openLightbox('{{ $photo->photo_url }}', 'Kondisi Awal - {{ $order->shoe_brand }}')">
                            <img src="{{ $photo->photo_url }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="Before Photo">
                            <div class="absolute top-2 right-2 px-2 py-0.5 bg-black/50 backdrop-blur-md rounded text-[8px] font-bold uppercase tracking-tighter opacity-0 group-hover:opacity-100 transition-opacity">
                                {{ str_replace('_', ' ', $photo->step) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
    </div>

    <!-- Footer Action -->
    <div class="fixed bottom-8 left-1/2 -translate-x-1/2 z-[100] w-[90vw] max-w-md">
        <div class="glass rounded-2xl p-4 flex items-center justify-between glow shadow-2xl">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-500 flex items-center justify-center text-white font-black italic">S</div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Workshop Partner</p>
                    <p class="text-xs font-bold">Terima kasih telah berkunjung!</p>
                </div>
            </div>
            <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', config('app.contact_whatsapp', '628123456789'))) }}" class="bg-teal-500 hover:bg-teal-600 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-tight transition-all shadow-lg shadow-teal-500/20">
                Hubungi CS
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
