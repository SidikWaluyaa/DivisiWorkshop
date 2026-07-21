<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <title>Customer Experience Report - {{ $order->spk_number ?? 'SPK' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            /* Tracking Style dots */
            background-color: #f3f4f6;
            background-image: radial-gradient(#e5e7eb 1px, transparent 1px);
            background-size: 20px 20px;
        }
        [x-cloak] { display: none !important; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="p-3 sm:p-5 md:p-8 pb-24 md:pb-8 antialiased text-gray-800" 
      x-data="{ 
        showLightbox: false, 
        lightboxImage: '', 
        lightboxCaption: '',
        openLightbox(url, caption) {
            this.lightboxImage = url;
            this.lightboxCaption = caption;
            this.showLightbox = true;
        },
        activePhoto: '{{ $photoUrls[0] ?? '' }}',
        photos: {{ json_encode($photoUrls) }},
        photoSizes: {{ json_encode($photoSizes ?? []) }}
      }">
    
    <div class="max-w-6xl mx-auto">
        <!-- Header Section -->
        <div class="mb-6 md:mb-10">
            <!-- Mobile Brand Bar -->
            <div class="flex items-center justify-between md:hidden mb-4 pb-3 border-b border-gray-200/60">
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : '/' }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 bg-white px-3 py-1.5 rounded-full shadow-sm border border-gray-200 hover:text-teal-600">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali
                </a>
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-9 w-auto object-contain drop-shadow-sm">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                <div>
                    <!-- Desktop Back Button -->
                    <a href="{{ url()->previous() !== url()->current() ? url()->previous() : '/' }}" class="hidden md:inline-flex items-center gap-2 mb-4 text-gray-500 hover:text-teal-600 transition-colors group">
                        <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm border border-gray-200 group-hover:bg-teal-500 group-hover:text-white group-hover:border-teal-500 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        </div>
                        <span class="font-bold text-xs tracking-wider uppercase">Kembali</span>
                    </a>

                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <span class="px-2.5 py-0.5 rounded-md bg-teal-100 text-teal-800 text-[10px] font-black uppercase tracking-widest border border-teal-200/80 shadow-xs">
                            ⚠️ CX ISSUE REPORT
                        </span>
                        <span class="px-2.5 py-0.5 rounded-md bg-amber-100 text-amber-800 text-[10px] font-black uppercase tracking-widest border border-amber-200/80">
                            KATEGORI: {{ $issue->category }}
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-black text-gray-900 tracking-tight uppercase break-words">
                        {{ $order->spk_number ?? ($issue->spk_number ?? 'SPK_TIDAK_DITEMUKAN') }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-2 sm:gap-3 mt-2 text-xs md:text-sm font-bold text-gray-600">
                        <span class="px-2.5 py-1 rounded-lg bg-white border border-gray-200/80 text-gray-700 shadow-xs flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            {{ \Carbon\Carbon::parse($issue->created_at)->format('d M Y, H:i') }}
                        </span>
                        <span class="text-gray-300 hidden sm:inline">•</span>
                        <span class="text-gray-600 bg-gray-100/80 px-2.5 py-1 rounded-lg border border-gray-200/50">
                            👤 {{ $order->customer_name ?? $issue->customer_name }}
                        </span>
                        <span class="text-gray-300 hidden sm:inline">•</span>
                        <span class="text-gray-600 bg-gray-100/80 px-2.5 py-1 rounded-lg border border-gray-200/50">
                            👟 {{ $order->shoe_brand ?? '-' }}
                        </span>
                    </div>
                </div>
                
                <!-- Desktop Logo Branding -->
                <div class="hidden md:flex justify-end items-center opacity-95">
                     <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-20 lg:h-24 w-auto object-contain drop-shadow-md">
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-8 items-start">
            <!-- Left Column: Photo Gallery -->
            <div class="lg:col-span-7 xl:col-span-8 space-y-6">
                <div class="bg-white rounded-3xl md:rounded-[2.5rem] shadow-xl border-t-8 border-teal-500 overflow-hidden relative group">
                    <!-- Main Preview -->
                    <template x-if="photos.length > 0">
                        <div class="relative aspect-[4/3] bg-gray-950 flex items-center justify-center cursor-zoom-in overflow-hidden" 
                             @click="openLightbox(activePhoto, 'Dokumentasi CX - {{ $order->spk_number ?? $issue->spk_number }}')">
                            <img :src="activePhoto" class="w-full h-full object-contain transition-transform duration-700 group-hover:scale-105" alt="Active CX Issue Photo">
                            
                            <!-- Mobile Always-Visible Tap Hint Badge -->
                            <div class="absolute top-3 right-3 md:hidden z-10">
                                <span class="bg-black/60 backdrop-blur-md text-white text-[10px] font-bold px-2.5 py-1 rounded-full border border-white/20 shadow-md flex items-center gap-1">
                                    🔍 Ketuk foto memperbesar
                                </span>
                            </div>

                            <!-- Desktop Overlay Labels -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 hidden md:block"></div>
                            <div class="absolute bottom-6 left-6 right-6 flex justify-between items-end transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 hidden md:flex">
                                <div class="bg-white/10 backdrop-blur-md border border-white/20 px-4 py-2 rounded-2xl text-white shadow-lg">
                                    <p class="text-[10px] font-black uppercase tracking-widest opacity-80">Visual Reference</p>
                                    <p class="text-sm font-bold flex items-center gap-2">
                                        Bukti Foto Kendala
                                        <span class="px-2 py-0.5 rounded-full bg-black/40 text-[10px] font-mono border border-white/10" x-text="photoSizes[activePhoto] || 'JPG/PNG'"></span>
                                    </p>
                                </div>
                                <div class="bg-teal-600 text-white p-3 rounded-2xl shadow-lg shadow-teal-600/30">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" /></svg>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template x-if="photos.length === 0">
                        <div class="relative aspect-[4/3] bg-gray-50 flex flex-col items-center justify-center text-gray-400 p-6 text-center">
                            <svg class="w-16 h-16 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <p class="font-bold text-sm text-gray-500">Tidak ada foto dilampirkan</p>
                        </div>
                    </template>

                    <!-- Thumbnails Strip -->
                    <template x-if="photos.length > 1">
                        <div class="p-3 sm:p-5 bg-gray-50/80 border-t border-gray-100">
                            <p class="text-[10px] font-black uppercase tracking-wider text-gray-400 mb-2">Pilih Foto ({{ count($photoUrls) }})</p>
                            <div class="flex gap-2.5 sm:gap-3 overflow-x-auto no-scrollbar pb-1">
                                <template x-for="(photo, index) in photos" :key="index">
                                    <button @click="activePhoto = photo" 
                                            :class="activePhoto === photo ? 'ring-4 ring-teal-500 scale-95 shadow-lg opacity-100' : 'opacity-60 hover:opacity-100 hover:scale-105'"
                                            class="relative flex-shrink-0 w-16 h-16 sm:w-20 sm:h-20 rounded-2xl overflow-hidden transition-all duration-300 bg-gray-900 border-2 border-white shadow-xs">
                                        <img :src="photo" class="w-full h-full object-cover" alt="Thumbnail">
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Right Column: Detailed Findings & Action Cards -->
            <div class="lg:col-span-5 xl:col-span-4 space-y-6">
                <!-- Detailed Findings Card -->
                <div class="bg-white rounded-3xl md:rounded-[2.5rem] shadow-xl border-t-8 border-teal-500 p-4 sm:p-6 md:p-8 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-teal-50/60 rounded-bl-full -mr-4 -mt-4 opacity-50"></div>
                    
                    <h2 class="text-lg sm:text-xl font-black text-gray-800 mb-5 md:mb-6 flex items-center gap-2.5 relative z-10">
                        <span class="w-2 h-6 bg-teal-600 rounded-full"></span>
                        DETAIL LAPORAN KENDALA
                    </h2>
                    
                    <div class="space-y-4 md:space-y-5 relative z-10">
                        @php
                            // Helper to clean up strings and detect if they are empty or just dash
                            $cleanFn = function($str) {
                                $trimmed = trim($str ?? '');
                                return ($trimmed === '-' || $trimmed === '' || strtolower($trimmed) === 'null') ? '' : $trimmed;
                            };

                            $upper = $cleanFn($issue->desc_upper);
                            $sol = $cleanFn($issue->desc_sol);
                            $bawaan = $cleanFn($issue->desc_kondisi_bawaan);

                            if (empty($upper) && empty($sol) && empty($bawaan) && !empty($issue->description)) {
                                $parts = explode('|', $issue->description);
                                $upper = isset($parts[0]) ? $cleanFn($parts[0]) : '';
                                $sol = isset($parts[1]) ? $cleanFn($parts[1]) : '';
                                $bawaan = isset($parts[2]) ? $cleanFn($parts[2]) : '';
                            }

                            $hasPipedParts = !empty($upper) || !empty($sol) || !empty($bawaan);
                        @endphp

                        @if($hasPipedParts)
                            @if(!empty($upper))
                                <div class="group">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1.5">👟 Upper / Bagian Atas</p>
                                    <div class="p-3.5 sm:p-4 bg-gray-50 rounded-2xl border border-gray-100 font-bold text-gray-800 text-xs sm:text-sm leading-relaxed shadow-xs whitespace-pre-wrap">
                                        {{ $upper }}
                                    </div>
                                </div>
                            @endif

                            @if(!empty($sol))
                                <div class="group">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1.5">👣 Midsole & Outsole</p>
                                    <div class="p-3.5 sm:p-4 bg-gray-50 rounded-2xl border border-gray-100 font-bold text-gray-800 text-xs sm:text-sm leading-relaxed shadow-xs whitespace-pre-wrap">
                                        {{ $sol }}
                                    </div>
                                </div>
                            @endif

                            @if(!empty($bawaan))
                                <div class="group">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1.5">💼 Kondisi Bawaan</p>
                                    <div class="p-3.5 sm:p-4 bg-gray-50 rounded-2xl border border-gray-100 font-bold text-gray-800 text-xs sm:text-sm leading-relaxed shadow-xs whitespace-pre-wrap">
                                        {{ $bawaan }}
                                    </div>
                                </div>
                            @endif
                        @else
                            @if($issue->kendala_1 || $issue->kendala_2)
                                <div class="group">
                                    <p class="text-[10px] font-black text-red-500 uppercase tracking-[0.2em] mb-1.5 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        Detail Kendala
                                    </p>
                                    <div class="p-3.5 sm:p-4 bg-red-50/50 rounded-2xl border border-red-100 shadow-xs text-xs sm:text-sm">
                                        <ul class="list-disc pl-5 space-y-1.5 text-gray-800 font-bold">
                                            @if($issue->kendala_1) <li>{{ $issue->kendala_1 }}</li> @endif
                                            @if($issue->kendala_2) <li>{{ $issue->kendala_2 }}</li> @endif
                                        </ul>
                                    </div>
                                </div>
                            @endif

                            @if($issue->opsi_solusi_1 || $issue->opsi_solusi_2)
                                <div class="group">
                                    <p class="text-[10px] font-black text-teal-600 uppercase tracking-[0.2em] mb-1.5 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                        Opsi Solusi
                                    </p>
                                    <div class="p-3.5 sm:p-4 bg-teal-50/50 rounded-2xl border border-teal-100 shadow-xs text-xs sm:text-sm">
                                        <ul class="list-disc pl-5 space-y-1.5 text-gray-800 font-bold">
                                            @if($issue->opsi_solusi_1) <li>{{ $issue->opsi_solusi_1 }}</li> @endif
                                            @if($issue->opsi_solusi_2) <li>{{ $issue->opsi_solusi_2 }}</li> @endif
                                        </ul>
                                    </div>
                                </div>
                            @endif

                            @if(!$issue->kendala_1 && !$issue->kendala_2 && !$issue->opsi_solusi_1 && !$issue->opsi_solusi_2)
                                 <div class="group">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1.5">Deskripsi Laporan</p>
                                    <div class="p-3.5 sm:p-4 bg-gray-50 rounded-2xl border border-gray-100 font-bold text-gray-800 text-xs sm:text-sm leading-relaxed shadow-xs whitespace-pre-wrap">
                                        {{ rtrim($issue->description) ?: 'Tidak ada deskripsi' }}
                                    </div>
                                </div>
                            @endif
                        @endif

                        {{-- Estimasi Waktu Tambahan Card --}}
                        @if($issue->estimasi_tambahan)
                            <div class="group pt-2">
                                <p class="text-[10px] font-black text-amber-700 uppercase tracking-[0.2em] mb-1.5 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Estimasi Waktu Tambahan
                                </p>
                                <div class="p-4 bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl border border-amber-200/80 font-black text-amber-950 text-sm sm:text-base shadow-xs flex items-center justify-between">
                                    <span>{{ $issue->estimasi_tambahan }}</span>
                                    <span class="px-2.5 py-0.5 rounded-full bg-amber-200/60 text-amber-900 text-[10px] font-bold uppercase tracking-wider">Durasi Tambahan</span>
                                </div>
                            </div>
                        @endif

                        {{-- Rekomendasi Tambah Jasa Baru Card --}}
                        @if($issue->rec_service_1 || $issue->rec_service_2 || ($issue->recommended_services && $issue->recommended_services !== '-'))
                            <div class="group pt-2">
                                <p class="text-[10px] font-black text-purple-700 uppercase tracking-[0.2em] mb-1.5 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    Rekomendasi Tambah Jasa Baru
                                </p>
                                <div class="p-4 bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl border border-purple-200/80 font-bold text-purple-950 text-xs sm:text-sm shadow-xs space-y-1.5">
                                    @if($issue->rec_service_1) <div>1. {{ $issue->rec_service_1 }}</div> @endif
                                    @if($issue->rec_service_2) <div>2. {{ $issue->rec_service_2 }}</div> @endif
                                    @if(!$issue->rec_service_1 && !$issue->rec_service_2 && $issue->recommended_services)
                                        <div>{!! nl2br(e($issue->recommended_services)) !!}</div>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        <!-- Reporter Footer Info -->
                        <div class="mt-5 pt-4 border-t border-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-xs">
                                     {{ substr($issue->reporter->name ?? 'User', 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Dilaporkan Oleh</p>
                                    <p class="text-xs sm:text-sm font-bold text-gray-800">{{ $issue->reporter->name ?? 'Tim Workshop' }}</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Desktop Action Panel (WhatsApp Contact) -->
                @php
                    $supportPhone = preg_replace('/[^0-9]/', '', config('services.whatsapp.support_number', '62895339939800'));
                    $messageText = "Halo Admin Shoe Workshop, saya ingin berdiskusi mengenai Laporan Kendala (CX) untuk No. SPK *" . ($order->spk_number ?? $issue->spk_number) . "*. Berikut link laporan saya: " . request()->url();
                    $waUrl = "https://wa.me/" . $supportPhone . "?text=" . urlencode($messageText);
                @endphp
                
                <div class="bg-[#1a3b34] rounded-3xl md:rounded-[2.5rem] shadow-xl border border-teal-900/40 p-5 sm:p-6 md:p-8 relative overflow-hidden group">
                    <div class="absolute -right-12 -bottom-12 w-36 h-36 bg-[#22AF85]/10 rounded-full blur-2xl group-hover:bg-[#22AF85]/25 transition-all duration-500"></div>
                    <div class="relative z-10 space-y-3.5">
                        <span class="inline-flex items-center gap-1.5 px-3 py-0.5 bg-[#22AF85]/20 text-[#22AF85] text-[10px] font-black uppercase tracking-widest rounded-lg border border-[#22AF85]/30">
                            💬 Bantuan & Konfirmasi
                        </span>
                        <h3 class="text-white text-base sm:text-lg font-black tracking-tight leading-tight">
                            Butuh Diskusi / Konfirmasi Pengerjaan?
                        </h3>
                        <p class="text-gray-300 text-xs leading-relaxed">
                            Hubungi admin Customer Experience kami via WhatsApp untuk berdiskusi, memberikan persetujuan, atau mengajukan pertanyaan mengenai kendala sepatu Anda.
                        </p>
                        
                        <a href="{{ $waUrl }}" target="_blank" class="w-full flex items-center justify-center gap-2 py-3.5 px-5 bg-[#22AF85] hover:bg-[#1c926f] active:scale-95 text-white rounded-2xl text-xs sm:text-sm font-black transition-all shadow-lg shadow-teal-900/40 tracking-wider uppercase">
                            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.73-1.45L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.42 9.864-9.864.002-2.637-1.03-5.114-2.905-6.99C16.486 1.88 14.021.845 12.012.845c-5.437 0-9.866 4.418-9.87 9.862-.001 1.702.461 3.351 1.341 4.771l-.98 3.586 3.673-.963zm10.741-6.937c-.3-.15-1.774-.875-2.046-.974-.273-.1-.472-.15-.671.15-.198.3-.77.974-.944 1.173-.173.2-.347.225-.647.075-.3-.15-1.266-.466-2.41-1.487-.89-.794-1.774-1.664-2.074-.173-.3-.018-.462.13-.61.135-.13.3-.349.45-.523.15-.174.2-.3.3-.5.1-.2.05-.374-.025-.524-.075-.15-.671-1.62-.92-2.22-.242-.584-.487-.504-.671-.514-.172-.01-.371-.01-.57-.01-.2 0-.526.075-.801.374-.275.3-1.05 1.024-1.05 2.5s1.075 2.9 1.225 3.1c.15.2 2.11 3.224 5.116 4.522.715.31 1.273.495 1.71.635.72.23 1.375.197 1.892.12.576-.087 1.774-.726 2.022-1.43.247-.704.247-1.306.173-1.43-.075-.124-.273-.198-.572-.348z"/>
                            </svg>
                            Hubungi Admin via WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-10 md:mt-14 text-center">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.4em] mb-2">&copy; {{ date('Y') }} Shoe Workshop Elite System</p>
            <div class="inline-flex gap-3">
                <span class="w-6 h-1 bg-teal-500/20 rounded-full"></span>
                <span class="w-12 h-1 bg-teal-500/40 rounded-full"></span>
                <span class="w-6 h-1 bg-teal-500/20 rounded-full"></span>
            </div>
        </div>
    </div>

    <!-- 📱 Mobile Sticky Bottom Action Bar -->
    <div class="fixed bottom-0 inset-x-0 z-40 bg-white/90 backdrop-blur-lg border-t border-gray-200/80 p-3 md:hidden shadow-[0_-8px_30px_rgba(0,0,0,0.12)]">
        <a href="{{ $waUrl }}" target="_blank" class="w-full flex items-center justify-center gap-2.5 py-3 px-5 bg-[#22AF85] hover:bg-[#1c926f] active:scale-95 text-white rounded-xl text-xs font-black transition-all shadow-md uppercase tracking-wider">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.73-1.45L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.42 9.864-9.864.002-2.637-1.03-5.114-2.905-6.99C16.486 1.88 14.021.845 12.012.845c-5.437 0-9.866 4.418-9.87 9.862-.001 1.702.461 3.351 1.341 4.771l-.98 3.586 3.673-.963zm10.741-6.937c-.3-.15-1.774-.875-2.046-.974-.273-.1-.472-.15-.671.15-.198.3-.77.974-.944 1.173-.173.2-.347.225-.647.075-.3-.15-1.266-.466-2.41-1.487-.89-.794-1.774-1.664-2.074-.173-.3-.018-.462.13-.61.135-.13.3-.349.45-.523.15-.174.2-.3.3-.5.1-.2.05-.374-.025-.524-.075-.15-.671-1.62-.92-2.22-.242-.584-.487-.504-.671-.514-.172-.01-.371-.01-.57-.01-.2 0-.526.075-.801.374-.275.3-1.05 1.024-1.05 2.5s1.075 2.9 1.225 3.1c.15.2 2.11 3.224 5.116 4.522.715.31 1.273.495 1.71.635.72.23 1.375.197 1.892.12.576-.087 1.774-.726 2.022-1.43.247-.704.247-1.306.173-1.43-.075-.124-.273-.198-.572-.348z"/>
            </svg>
            💬 Hubungi Admin via WhatsApp
        </a>
    </div>

    <!-- Lightbox Modal (Premium Tracking Theme) -->
    <div x-show="showLightbox" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[999] flex items-center justify-center p-3 sm:p-6 bg-black/90 backdrop-blur-md"
         @keydown.escape.window="showLightbox = false">
        
        <!-- Close Button -->
        <button @click="showLightbox = false" class="absolute top-4 right-4 sm:top-8 sm:right-8 text-white/70 hover:text-white transition-all transform hover:rotate-90 z-50 p-2">
            <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <div class="relative max-w-5xl w-full flex flex-col items-center p-2" @click.away="showLightbox = false">
            <img :src="lightboxImage" class="max-h-[75vh] sm:max-h-[80vh] w-auto rounded-2xl sm:rounded-3xl shadow-2xl border-2 sm:border-4 border-white/10 transition-transform duration-500" alt="Full Image">
            <div class="mt-4 sm:mt-6 flex flex-col items-center text-center">
                <p x-text="lightboxCaption" class="text-white font-bold text-sm sm:text-lg tracking-wide bg-white/10 backdrop-blur-md px-5 py-2.5 rounded-2xl border border-white/10 shadow-xl max-w-full truncate"></p>
                <p class="mt-2 text-white/40 text-[9px] sm:text-[10px] font-black uppercase tracking-[0.3em]">Dokumentasi Workshop Shoe Workshop</p>
            </div>
        </div>
    </div>

</body>
</html>
