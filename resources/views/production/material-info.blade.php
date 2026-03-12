<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <title>Informasi Material - {{ $order->spk_number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            background-color: #f9fafb;
            background-image: radial-gradient(#e5e7eb 1px, transparent 1px);
            background-size: 24px 24px;
        }
        [x-cloak] { display: none !important; }
        .glass-effect {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body class="p-4 md:p-12 antialiased text-slate-800" x-data="{ showLightbox: false }">
    
    <div class="max-w-6xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12">
            <div>
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('production.late-info') }}" class="group inline-flex items-center gap-2 mb-6 text-slate-400 hover:text-blue-600 transition-all">
                    <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center shadow-sm group-hover:bg-blue-600 group-hover:text-white transition-all transform group-hover:-translate-x-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </div>
                    <span class="font-bold text-sm uppercase tracking-widest">Kembali</span>
                </a>
                
                <div class="flex items-center gap-3 mb-3">
                    <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-[10px] font-black uppercase tracking-[0.2em] border border-blue-200">
                        MATERIAL INTEL REPORT
                    </span>
                    @if($order->priority_scale == 1)
                        <span class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-[10px] font-black uppercase tracking-[0.2em] border border-red-200 animate-pulse">
                            PRIORITAS TINGGI
                        </span>
                    @endif
                </div>

                <h1 class="text-4xl md:text-5xl font-black text-slate-900 mb-3 tracking-tighter uppercase italic">
                    {{ $order->spk_number }}
                </h1>
                
                <div class="flex items-center gap-4 text-slate-500 font-bold text-sm">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2.5"></path></svg>
                        {{ $order->customer_name }}
                    </span>
                    <span class="opacity-20">|</span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 00-2 2z" stroke-width="2.5"></path></svg>
                        Estimasi Dasar: {{ $order->estimation_date ? $order->estimation_date->format('d M Y') : '-' }}
                    </span>
                </div>
            </div>

            <!-- Brand Logo -->
            <div class="hidden md:block">
                <div class="relative group">
                    <div class="absolute -inset-2 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-full blur opacity-20 group-hover:opacity-40 transition duration-500"></div>
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="relative h-24 w-auto drop-shadow-2xl grayscale hover:grayscale-0 transition-all duration-500">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <!-- Left: Visual Evidence -->
            <div class="lg:col-span-8">
                <div class="relative group bg-white p-4 rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                    <div class="relative aspect-[4/3] rounded-[2.5rem] overflow-hidden bg-slate-900 group-hover:shadow-3xl transition-all duration-700 cursor-zoom-in" @click="showLightbox = true">
                        @if($order->material_photo_url)
                            <img src="{{ $order->material_photo_url }}" class="w-full h-full object-contain transition-transform duration-1000 group-hover:scale-105" alt="Material Photo">
                            
                            <!-- Overlay Infro -->
                            <div class="absolute inset-x-0 bottom-0 p-8 bg-gradient-to-t from-black/80 via-black/20 to-transparent transform translate-y-2 group-hover:translate-y-0 transition-transform duration-500">
                                <div class="flex justify-between items-end">
                                    <div>
                                        <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-1">Visual Reference</p>
                                        <h3 class="text-white font-extrabold text-xl">Bukti Fisik Material</h3>
                                    </div>
                                    <div class="bg-blue-600 text-white p-4 rounded-2xl shadow-xl shadow-blue-600/20 active:scale-90 transition-all">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" /></svg>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-slate-600 gap-4">
                                <div class="w-20 h-20 bg-slate-800 rounded-3xl flex items-center justify-center border border-slate-700">
                                    <svg class="w-10 h-10 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2"></path></svg>
                                </div>
                                <p class="font-black text-xs uppercase tracking-widest opacity-40">Dokumentasi Belum Diunggah</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right: Detailed Intelligence -->
            <div class="lg:col-span-4 space-y-8">
                <!-- Arrival Status Card -->
                <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-100 p-8 relative overflow-hidden group">
                    <div class="absolute -top-12 -right-12 w-32 h-32 bg-blue-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
                    
                    <h2 class="text-xs font-black text-slate-600 uppercase tracking-[0.3em] mb-8 flex items-center gap-3">
                        <span class="w-1.5 h-6 bg-blue-600 rounded-full"></span>
                        Status Kedatangan
                    </h2>

                    <div class="space-y-6">
                        @if($order->material_name)
                        <div>
                            <p class="text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1">Nama Material</p>
                            <div class="p-5 bg-blue-50/50 rounded-3xl border border-blue-100 flex items-center gap-4 group-hover:bg-white group-hover:shadow-lg transition-all">
                                <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-sm text-blue-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                </div>
                                <div>
                                    <p class="text-xl font-black text-slate-900 leading-none uppercase italic tracking-tighter">
                                        {{ $order->material_name }}
                                    </p>
                                    <p class="text-[10px] font-bold text-blue-600 mt-1 uppercase">Material Identifikasi</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div>
                            <p class="text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1">Tanggal Kedatangan</p>
                            <div class="p-5 bg-slate-50 rounded-3xl border border-slate-100 flex items-center gap-4 group-hover:bg-white group-hover:shadow-lg transition-all">
                                <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-sm text-blue-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 00-2 2z" stroke-width="2.5"></path></svg>
                                </div>
                                <div>
                                    <p class="text-xl font-black text-slate-900 leading-none">
                                        {{ $order->material_arrival_date ? $order->material_arrival_date->format('d F Y') : 'BELUM DITETAPKAN' }}
                                    </p>
                                    <p class="text-[10px] font-bold text-slate-600 mt-1 uppercase">Material Readiness</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <p class="text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1">Analisis Keterlambatan</p>
                            <div class="p-5 bg-slate-50 rounded-3xl border border-slate-100 group-hover:bg-white group-hover:shadow-lg transition-all">
                                <div class="flex items-center gap-3 mb-3">
                                    <span class="px-3 py-1 bg-slate-900 text-white text-[10px] font-black rounded-lg uppercase tracking-widest italic">
                                        {{ $order->late_description ?: 'Tidak Ada Alasan' }}
                                    </span>
                                </div>
                                <p class="text-sm font-bold text-slate-600 leading-relaxed italic">
                                    "Kendala pada ketersediaan material mengharuskan penyesuaian jadwal produksi."
                                </p>
                            </div>
                        </div>

                        <div>
                            <p class="text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1">Estimasi Baru</p>
                            <div class="p-5 bg-gradient-to-br from-slate-800 to-slate-900 rounded-3xl border border-slate-700 shadow-2xl transition-all">
                                <p class="text-2xl font-black text-white leading-none tracking-tighter">
                                    {{ $order->new_estimation_date ? $order->new_estimation_date->format('d M Y') : 'BELUM UPDATE' }}
                                </p>
                                <p class="text-[9px] font-bold text-white/40 mt-2 uppercase tracking-[0.2em] flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Target Selesai Optimal
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Metadata -->
                <div class="bg-slate-50 rounded-[2.5rem] p-8 border border-slate-100 flex flex-col gap-4">
                    <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest text-slate-400">
                        <span>Laporan Dibuat</span>
                        <span class="text-slate-600 italic">Otomatis Oleh Sistem</span>
                    </div>
                    <div class="h-px bg-slate-200"></div>
                    <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest text-slate-400">
                        <span>Waktu Sinkronisasi</span>
                        <span class="text-slate-600">{{ now()->format('d.m.Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-20 py-10 border-t border-slate-200 text-center">
            <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.5em] mb-4">&copy; {{ date('Y') }} Shoe Workshop Elite Intelligence</p>
            <div class="flex justify-center gap-1.5">
                <div class="w-1.5 h-1.5 rounded-full bg-slate-200"></div>
                <div class="w-8 h-1.5 rounded-full bg-blue-600/20"></div>
                <div class="w-1.5 h-1.5 rounded-full bg-slate-200"></div>
            </div>
        </div>
    </div>

    <!-- Lightbox Modal -->
    @if($order->material_photo_url)
    <div x-show="showLightbox" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[999] p-8 flex items-center justify-center bg-slate-900/95 backdrop-blur-2xl"
         @keydown.escape.window="showLightbox = false">
        
        <button @click="showLightbox = false" class="absolute top-12 right-12 text-white/50 hover:text-white transition-all transform hover:rotate-90">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <div class="relative max-w-5xl w-full flex flex-col items-center gap-10" @click.away="showLightbox = false">
            <div class="relative group/lb">
                <div class="absolute -inset-1 bg-white/10 rounded-[3rem] blur-xl opacity-0 group-hover/lb:opacity-100 transition duration-500"></div>
                <img src="{{ $order->material_photo_url }}" class="relative max-h-[75vh] w-auto rounded-[3rem] shadow-[0_0_100px_rgba(0,0,0,0.5)] border-4 border-white/5" alt="Large View">
            </div>
            
            <div class="flex flex-col items-center gap-3">
                <div class="bg-white/10 backdrop-blur-xl px-10 py-5 rounded-[2rem] border border-white/20 text-white font-black text-xl tracking-tighter uppercase italic shadow-2xl">
                    {{ $order->spk_number }} — Visual Evidence
                </div>
                <p class="text-[10px] font-black text-white/30 uppercase tracking-[0.4em]">Elite Intelligence Documentation System</p>
            </div>
        </div>
    </div>
    @endif

</body>
</html>
