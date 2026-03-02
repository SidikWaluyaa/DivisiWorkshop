<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QC Reject Report - {{ $order->spk_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-card { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 1); }
    </style>
</head>
<body class="bg-gray-50 min-h-screen pb-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
            <div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 uppercase tracking-wider mb-3">
                    QC REJECT REPORT
                </span>
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">
                    {{ $order->spk_number }}
                </h1>
                <p class="text-gray-500 mt-2 font-medium">{{ $order->customer_name }} • {{ $order->shoe_brand }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm font-semibold text-gray-400 uppercase tracking-widest">Entry Date</p>
                <p class="text-xl font-bold text-gray-900">{{ \Carbon\Carbon::parse($order->entry_date)->format('d F Y') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start" x-data="{ 
            activePhoto: '{{ $photos[0] ?? '' }}',
            photos: {{ json_encode($photos) }},
            zoomMode: false
        }">
            <!-- Photo Gallery Section -->
            <div class="lg:col-span-8 space-y-4">
                <div class="relative aspect-[4/3] rounded-3xl overflow-hidden shadow-2xl bg-black group cursor-zoom-in" @click="zoomMode = true">
                    <img :src="activePhoto" class="w-full h-full object-contain transition-transform duration-500 group-hover:scale-105" alt="Active Reject Photo">
                    <div class="absolute bottom-6 left-6 right-6">
                        <div class="inline-flex items-center px-4 py-2 rounded-2xl bg-black/50 backdrop-blur-md text-white border border-white/20">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" /></svg>
                            <span class="text-sm font-semibold">Click to Zoom</span>
                        </div>
                    </div>
                </div>

                <!-- Thumbnails Card -->
                <div class="glass-card rounded-[2rem] p-4 flex gap-3 overflow-x-auto no-scrollbar">
                    <template x-for="(photo, index) in photos" :key="index">
                        <button @click="activePhoto = photo" 
                                :class="activePhoto === photo ? 'ring-4 ring-red-500 scale-95 shadow-lg' : 'opacity-60 hover:opacity-100'"
                                class="relative flex-shrink-0 w-24 h-24 rounded-2xl overflow-hidden transition-all duration-300 transform">
                            <img :src="photo" class="w-full h-full object-cover" alt="Thumbnail">
                        </button>
                    </template>
                </div>
            </div>

            <!-- Detailed Notes Section -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Condition Summary Card -->
                <div class="glass-card rounded-[2rem] p-8 shadow-sm">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="w-2 h-8 bg-red-600 rounded-full mr-4"></span>
                        Detail Temuan
                    </h2>
                    
                    <div class="space-y-6">
                        @if($issue->desc_upper)
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Upper / Bagian Atas</p>
                            <p class="text-gray-900 leading-relaxed font-medium">{{ $issue->desc_upper }}</p>
                        </div>
                        @endif

                        @if($issue->desc_sol)
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Midsole & Outsole</p>
                            <p class="text-gray-900 leading-relaxed font-medium">{{ $issue->desc_sol }}</p>
                        </div>
                        @endif

                        @if($issue->desc_kondisi_bawaan)
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Kondisi Bawaan / Titipan</p>
                            <p class="text-gray-900 leading-relaxed font-medium italic">"{{ $issue->desc_kondisi_bawaan }}"</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Solution Recommendation Card -->
                <div class="bg-gray-900 rounded-[2rem] p-8 text-white shadow-xl shadow-gray-200">
                    <h2 class="text-xl font-bold mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        Rekomendasi Layanan
                    </h2>
                    
                    <div class="space-y-4">
                        @if($issue->recommended_services)
                        <div class="space-y-3">
                            <p class="text-xs font-bold text-white/50 uppercase tracking-widest">Wajib (Main Treatment)</p>
                            <div class="bg-white/10 rounded-2xl p-4 border border-white/10">
                                <p class="text-sm font-semibold whitespace-pre-line">{{ $issue->recommended_services }}</p>
                            </div>
                        </div>
                        @endif

                        @if($issue->suggested_services)
                        <div class="space-y-3">
                            <p class="text-xs font-bold text-white/50 uppercase tracking-widest">Opsional (Add-on)</p>
                            <div class="bg-red-600/20 rounded-2xl p-4 border border-red-500/30">
                                <p class="text-sm font-semibold whitespace-pre-line">{{ $issue->suggested_services }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Lightbox Zoom -->
            <div x-show="zoomMode" 
                 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-[100] bg-black/95 flex items-center justify-center p-4"
                 @click="zoomMode = false">
                <button class="absolute top-10 right-10 text-white/60 hover:text-white transition-colors">
                    <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <img :src="activePhoto" class="max-w-full max-h-full object-contain rounded-xl shadow-2xl" alt="Zoomed Photo">
                <div class="absolute bottom-10 text-center">
                    <p class="text-white/60 text-sm font-medium tracking-widest uppercase">Click anywhere to close</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
