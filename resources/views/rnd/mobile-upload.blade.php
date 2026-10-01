<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <title>Mobile Upload Progres R&D — {{ $order->spk_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        button:active, .btn-active:active { transform: scale(0.98); }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen pb-16 antialiased selection:bg-[#22B086] selection:text-white"
      x-data="rndUploadApp()">

    <!-- Header App Bar (2-Baris Rapi Mobile-First) -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/90 px-4 py-2.5 shadow-xs">
        <div class="max-w-lg mx-auto space-y-2">
            <!-- Baris 1: Logo Resmi & Tombol Living Report -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Shoe Workshop" class="h-8 w-auto object-contain">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-800 hidden sm:inline-block">Laboratorium R&amp;D</span>
                </div>
                
                <a href="{{ route('rnd.report', $initialProgress->report_token) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-emerald-50 text-[#147A59] border border-emerald-200 text-xs font-bold transition shadow-2xs active:scale-95">
                    <span>Living Report</span>
                    <svg class="w-3.5 h-3.5 text-[#147A59]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            </div>

            <!-- Baris 2: Kartu Identitas SPK & Sepatu Riset (Vertically Stacked, Anti-Overlap) -->
            <div class="bg-slate-50/90 rounded-2xl p-2.5 border border-slate-200/90 flex items-center justify-between gap-2.5">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-1.5 mb-1">
                        <span class="text-[9px] uppercase tracking-wider font-extrabold px-1.5 py-0.5 rounded bg-emerald-100/80 text-[#147A59] border border-emerald-300/60 font-mono shrink-0">
                            R&amp;D LAB
                        </span>
                        <span class="text-xs font-mono font-black text-slate-800 tracking-tight truncate">
                            {{ $order->spk_number }}
                        </span>
                    </div>
                    <p class="text-xs font-bold text-slate-900 truncate leading-tight">
                        {{ $order->shoe_brand ?? 'Sepatu Riset' }}{{ $order->shoe_type ? ' • ' . $order->shoe_type : '' }}
                    </p>
                </div>
                
                <div class="shrink-0 text-right">
                    <span class="inline-flex items-center px-2 py-1 rounded-xl bg-white border border-slate-200 text-[10px] font-bold text-slate-600 shadow-2xs">
                        Tahap #{{ $nextStageNumber }}
                    </span>
                </div>
            </div>
        </div>
    </header>

    <!-- Modal Sukses Muncul di Tengah Layar (Manual Dismiss) -->
    @if(session('success'))
    <div x-data="{ showSuccessModal: true }" 
         x-show="showSuccessModal" 
         style="display: none;" 
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 animate-fade-in"
         x-cloak>
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-slate-100 text-center space-y-4 animate-scale-in">
            <!-- Centang Hijau Animasi -->
            <div class="w-16 h-16 rounded-full bg-emerald-100 border-4 border-emerald-50 text-[#22B086] flex items-center justify-center mx-auto shadow-inner">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <div>
                <h3 class="text-base font-black text-slate-900">Berhasil Disimpan!</h3>
                <p class="text-xs text-slate-600 mt-1 font-medium leading-relaxed">
                    {{ session('success') }}
                </p>
            </div>

            <!-- Tombol Aksi Manual -->
            <div class="space-y-2 pt-2">
                <button type="button" 
                        @click="showSuccessModal = false"
                        class="w-full py-3 px-4 rounded-xl bg-[#22B086] hover:bg-[#1C8D6C] text-white text-xs font-black shadow-md shadow-emerald-200 transition active:scale-95 flex items-center justify-center gap-1.5">
                    <span>Lanjut Input Tahap Berikutnya</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>

                <a href="{{ route('rnd.report', $initialProgress->report_token) }}" target="_blank"
                   class="w-full py-2.5 px-4 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 text-xs font-bold transition flex items-center justify-center gap-1.5">
                    <span>Lihat di Living Report</span>
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Container -->
    <main class="max-w-lg mx-auto px-4 pt-4 space-y-4">

        <!-- Validation Errors -->
        @if(isset($errors) && $errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-3 shadow-xs">
            <div class="w-5 h-5 rounded-full bg-rose-100 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div class="text-xs space-y-1">
                <p class="font-bold text-rose-900">Mohon periksa kembali input Anda:</p>
                <ul class="list-disc pl-4 space-y-0.5 text-rose-700">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <!-- Form Card -->
        <form action="{{ route('rnd.upload.store', $token) }}" 
              method="POST" 
              enctype="multipart/form-data" 
              class="bg-white rounded-3xl border border-slate-200/90 p-5 shadow-xs space-y-5"
              @submit="isSubmitting = true">
            @csrf

            <!-- Stage Header Badge -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="flex h-2.5 w-2.5 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#FFC232] opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-[#22B086]"></span>
                    </span>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-800">
                        Input Progres Baru
                    </span>
                </div>
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-[#147A59] text-xs font-mono font-bold border border-emerald-200">
                    Tahap #{{ $nextStageNumber }}
                </span>
            </div>

            <!-- 1. Direct Camera / Photo Input with Auto-Compression -->
            <div>
                <label class="block text-xs font-bold text-slate-800 mb-2">
                    1. Foto Progres / Bukti Eksperimen <span class="text-rose-500">*</span>
                </label>
                
                <!-- Hidden file inputs -->
                <input type="file" 
                       id="cameraInput" 
                       accept="image/*" 
                       capture="environment" 
                       class="hidden" 
                       @change="handleImageSelect($event)">
                
                <input type="file" 
                       id="galleryInput" 
                       accept="image/*" 
                       class="hidden" 
                       @change="handleImageSelect($event)">

                <!-- Hidden compressed blob file to send to server -->
                <input type="file" name="photo" id="realPhotoInput" class="hidden" required>

                <!-- Photo Preview & Selector Area -->
                <div class="relative">
                    <template x-if="!previewUrl">
                        <div class="grid grid-cols-2 gap-3">
                            <button type="button" 
                                    @click="triggerCamera()" 
                                    class="h-32 rounded-2xl border-2 border-dashed border-emerald-300 bg-emerald-50/40 hover:bg-emerald-50 flex flex-col items-center justify-center gap-2 text-[#147A59] transition group p-3 text-center">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 text-[#147A59] flex items-center justify-center group-hover:scale-110 transition shadow-xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <span class="text-xs font-bold text-slate-800">Buka Kamera HP</span>
                                <span class="text-[10px] text-slate-500">Jepret Langsung</span>
                            </button>

                            <button type="button" 
                                    @click="document.getElementById('galleryInput').click()" 
                                    class="h-32 rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 hover:bg-slate-100 flex flex-col items-center justify-center gap-2 text-slate-700 transition group p-3 text-center">
                                <div class="w-10 h-10 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center group-hover:scale-110 transition shadow-xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <span class="text-xs font-bold text-slate-800">Pilih Galeri</span>
                                <span class="text-[10px] text-slate-500">File Tersimpan</span>
                            </button>
                        </div>
                    </template>

                    <!-- Preview Container -->
                    <template x-if="previewUrl">
                        <div class="relative rounded-2xl overflow-hidden border border-slate-200 bg-slate-900 aspect-video max-h-64 flex items-center justify-center group shadow-xs">
                            <img :src="previewUrl" class="w-full h-full object-contain">
                            
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-black/30 pointer-events-none"></div>
                            
                            <!-- Compression Status Badge -->
                            <div class="absolute bottom-2.5 left-2.5 text-[10px] font-mono px-2 py-1 rounded bg-black/70 backdrop-blur border border-white/20 text-slate-200">
                                <span x-text="compressionInfo"></span>
                            </div>

                            <!-- Retake / Remove Button -->
                            <button type="button" 
                                    @click="resetImage()" 
                                    class="absolute top-2.5 right-2.5 px-2.5 py-1 rounded-lg bg-rose-600/90 hover:bg-rose-600 text-white text-xs font-bold backdrop-blur flex items-center gap-1 shadow-md transition">
                                <span>Ganti Foto</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- 2. Stage Title & Quick Suggestions -->
            <div>
                <label for="stage_title" class="block text-xs font-bold text-slate-800 mb-2">
                    2. Judul Tahap / Aktivitas <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       id="stage_title" 
                       name="stage_title" 
                       x-model="stageTitle"
                       required 
                       placeholder="Contoh: Percobaan Formula PU Lem Tahap 1" 
                       class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-xs focus:ring-2 focus:ring-[#22B086] focus:border-[#22B086] transition">
                
                <!-- Quick Suggestion Chips -->
                <div class="flex flex-wrap gap-1.5 mt-2">
                    <button type="button" @click="setStageTitle('Uji Coba Formula Lem A')" class="text-[10px] px-2.5 py-1 rounded-md bg-slate-100 hover:bg-emerald-50 text-slate-700 hover:text-[#147A59] transition border border-slate-200">+ Formula Lem A</button>
                    <button type="button" @click="setStageTitle('Pengeringan Oven & Suhu')" class="text-[10px] px-2.5 py-1 rounded-md bg-slate-100 hover:bg-emerald-50 text-slate-700 hover:text-[#147A59] transition border border-slate-200">+ Oven & Suhu</button>
                    <button type="button" @click="setStageTitle('Uji Rekat & Tekanan 24 Jam')" class="text-[10px] px-2.5 py-1 rounded-md bg-slate-100 hover:bg-emerald-50 text-slate-700 hover:text-[#147A59] transition border border-slate-200">+ Uji Rekat 24 Jam</button>
                    <button type="button" @click="setStageTitle('Finishing & Evaluasi Material')" class="text-[10px] px-2.5 py-1 rounded-md bg-slate-100 hover:bg-emerald-50 text-slate-700 hover:text-[#147A59] transition border border-slate-200">+ Evaluasi Final</button>
                </div>
            </div>

            <!-- 3. Result Status Radio Buttons -->
            <div>
                <label class="block text-xs font-bold text-slate-800 mb-2">
                    3. Hasil Pengamatan Tahap Ini <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-2">
                    <!-- SUCCESS -->
                    <label class="relative flex items-center p-3 rounded-xl border cursor-pointer transition select-none"
                           :class="resultStatus === 'SUCCESS' ? 'bg-emerald-50 border-[#22B086] ring-1 ring-[#22B086] text-slate-900' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100'">
                        <input type="radio" name="result_status" value="SUCCESS" x-model="resultStatus" class="sr-only" required>
                        <div class="flex items-center gap-2.5">
                            <span class="w-3 h-3 rounded-full bg-[#22B086] shrink-0"></span>
                            <div>
                                <div class="text-xs font-bold text-slate-900">Berhasil</div>
                                <div class="text-[10px] text-slate-500">Sesuai target riset</div>
                            </div>
                        </div>
                    </label>

                    <!-- IN_PROGRESS -->
                    <label class="relative flex items-center p-3 rounded-xl border cursor-pointer transition select-none"
                           :class="resultStatus === 'IN_PROGRESS' ? 'bg-amber-50 border-amber-500 ring-1 ring-amber-500 text-slate-900' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100'">
                        <input type="radio" name="result_status" value="IN_PROGRESS" x-model="resultStatus" class="sr-only">
                        <div class="flex items-center gap-2.5">
                            <span class="w-3 h-3 rounded-full bg-amber-500 shrink-0"></span>
                            <div>
                                <div class="text-xs font-bold text-slate-900">Sedang Berjalan</div>
                                <div class="text-[10px] text-slate-500">Dalam pemantauan</div>
                            </div>
                        </div>
                    </label>

                    <!-- NEED_REVISION -->
                    <label class="relative flex items-center p-3 rounded-xl border cursor-pointer transition select-none"
                           :class="resultStatus === 'NEED_REVISION' ? 'bg-orange-50 border-orange-500 ring-1 ring-orange-500 text-slate-900' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100'">
                        <input type="radio" name="result_status" value="NEED_REVISION" x-model="resultStatus" class="sr-only">
                        <div class="flex items-center gap-2.5">
                            <span class="w-3 h-3 rounded-full bg-orange-500 shrink-0"></span>
                            <div>
                                <div class="text-xs font-bold text-slate-900">Perlu Revisi</div>
                                <div class="text-[10px] text-slate-500">Formula disesuaikan</div>
                            </div>
                        </div>
                    </label>

                    <!-- FAILED -->
                    <label class="relative flex items-center p-3 rounded-xl border cursor-pointer transition select-none"
                           :class="resultStatus === 'FAILED' ? 'bg-rose-50 border-rose-500 ring-1 ring-rose-500 text-slate-900' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100'">
                        <input type="radio" name="result_status" value="FAILED" x-model="resultStatus" class="sr-only">
                        <div class="flex items-center gap-2.5">
                            <span class="w-3 h-3 rounded-full bg-rose-500 shrink-0"></span>
                            <div>
                                <div class="text-xs font-bold text-slate-900">Gagal</div>
                                <div class="text-[10px] text-slate-500">Material/formula rusak</div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- 4. Notes / Formula Experiment -->
            <div>
                <label for="notes" class="block text-xs font-bold text-slate-800 mb-2">
                    4. Catatan Formula & Parameter Riset (Opsional)
                </label>
                <textarea id="notes" 
                          name="notes" 
                          rows="3" 
                          placeholder="Tuliskan formula kimia, rasio pencampuran, durasi pemanasan, suhu, atau observasi kendala teknis..."
                          class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-xs focus:ring-2 focus:ring-[#22B086] focus:border-[#22B086] transition"></textarea>
            </div>

            <!-- 5. Technician Name -->
            <div>
                <label for="technician_name" class="block text-xs font-bold text-slate-800 mb-2">
                    5. Nama Teknisi / PIC Riset (Opsional)
                </label>
                <input type="text" 
                       id="technician_name" 
                       name="technician_name" 
                       placeholder="Nama teknisi laboratorium..." 
                       class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-xs focus:ring-2 focus:ring-[#22B086] focus:border-[#22B086] transition">
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    :disabled="isSubmitting || !previewUrl"
                    class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-[#22B086] to-[#1C8D6C] hover:from-[#1A9E74] hover:to-[#147A59] text-white font-extrabold text-sm shadow-md shadow-emerald-200 flex items-center justify-center gap-2 transition disabled:opacity-50 disabled:cursor-not-allowed">
                <template x-if="!isSubmitting">
                    <span class="flex items-center gap-2">
                        <span>Simpan Progres Tahap Ini</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </span>
                </template>
                <template x-if="isSubmitting">
                    <span class="flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <span>Mengompres & Menyimpan...</span>
                    </span>
                </template>
            </button>
        </form>

        <!-- Previous Stages History Timeline (Accordion) -->
        @if($history->isNotEmpty())
        <div class="bg-white rounded-3xl border border-slate-200/90 p-5 space-y-3 shadow-xs" x-data="{ openHistory: true }">
            <button type="button" @click="openHistory = !openHistory" class="w-full flex items-center justify-between text-left">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-800">Riwayat Progres Terunggah</span>
                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-mono font-bold border border-slate-200">{{ $history->count() }} Tahap</span>
                </div>
                <svg class="w-4 h-4 text-slate-500 transform transition" :class="openHistory ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <div x-show="openHistory" class="pt-2 space-y-2.5">
                @foreach($history as $idx => $item)
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-start gap-3">
                    @if($item->photo_path)
                    <img src="{{ asset('storage/' . $item->photo_path) }}" 
                         alt="{{ $item->stage_title }}" 
                         class="w-14 h-14 rounded-xl object-cover border border-slate-200 flex-shrink-0">
                    @endif
                    <div class="flex-1 min-w-0">
                        @php
                            $histStatusClass = match($item->result_status) {
                                'SUCCESS' => 'bg-emerald-50 text-[#147A59] border-emerald-200',
                                'NEED_REVISION' => 'bg-orange-50 text-orange-700 border-orange-200',
                                'FAILED' => 'bg-rose-50 text-rose-700 border-rose-200',
                                default => 'bg-amber-50 text-amber-700 border-amber-200',
                            };
                        @endphp
                        <div class="flex items-center justify-between gap-1 mb-0.5">
                            <span class="text-[10px] font-mono text-[#147A59] font-bold">Tahap #{{ $idx + 1 }}</span>
                            <span class="text-[9px] px-1.5 py-0.5 rounded font-bold border {{ $histStatusClass }}">
                                {{ $item->result_status }}
                            </span>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800 truncate">{{ $item->stage_title }}</h4>
                        @if($item->notes)
                        <p class="text-[11px] text-slate-600 line-clamp-2 mt-0.5 font-mono">{{ $item->notes }}</p>
                        @endif
                        <span class="text-[9px] text-slate-400 block mt-1">{{ $item->created_at->format('d M Y, H:i') }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </main>

    <!-- Live Camera Webcam Modal (Untuk Penggunaan di Laptop / PC Desktop) -->
    <div x-show="showWebcamModal" 
         style="display: none;" 
         class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4" 
         x-cloak>
        <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-md w-full overflow-hidden shadow-2xl flex flex-col text-white">
            <div class="px-5 py-4 bg-slate-800/90 flex items-center justify-between border-b border-slate-700/60">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-xs font-black tracking-wider uppercase">Kamera Langsung</span>
                </div>
                <button type="button" @click="closeWebcam()" class="w-8 h-8 rounded-full bg-slate-700 hover:bg-slate-600 text-slate-300 flex items-center justify-center text-xs font-bold transition">✕</button>
            </div>
            
            <div class="relative bg-black aspect-video flex items-center justify-center overflow-hidden">
                <video id="webcamVideo" autoplay playsinline class="w-full h-full object-cover"></video>
                <div x-show="webcamLoading" class="absolute inset-0 flex items-center justify-center bg-slate-950/90 text-xs font-bold text-slate-300 gap-2">
                    <svg class="animate-spin w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span>Mengaktifkan kamera...</span>
                </div>
            </div>
            
            <div class="p-4 bg-slate-900 flex items-center justify-between gap-3 border-t border-slate-800">
                <button type="button" @click="closeWebcam()" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-bold text-slate-300 transition">
                    Batal
                </button>
                <button type="button" @click="captureWebcam()" class="px-6 py-2.5 rounded-xl bg-[#22B086] hover:bg-[#1C8D6C] text-xs font-black text-white flex items-center gap-2 shadow-lg shadow-emerald-500/20 active:scale-95 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Jepret Foto</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Client-Side Alpine JS App & Canvas Image Compression -->
    <script>
        function rndUploadApp() {
            return {
                previewUrl: null,
                compressionInfo: '',
                stageTitle: '',
                resultStatus: 'SUCCESS',
                isSubmitting: false,
                showWebcamModal: false,
                webcamLoading: false,
                webcamStream: null,

                setStageTitle(title) {
                    this.stageTitle = title;
                },

                triggerCamera() {
                    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
                    if (isMobile) {
                        // Pada Smartphone (Android/iOS): Buka native kamera belakang
                        document.getElementById('cameraInput').click();
                    } else {
                        // Pada PC / Laptop: Buka live webcam capture
                        this.openWebcam();
                    }
                },

                openWebcam() {
                    this.showWebcamModal = true;
                    this.webcamLoading = true;

                    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                        alert('Browser desktop tidak mendukung akses webcam langsung. Membuka pemilih file...');
                        this.closeWebcam();
                        document.getElementById('cameraInput').click();
                        return;
                    }

                    navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: 'environment',
                            width: { ideal: 1920 },
                            height: { ideal: 1080 }
                        }
                    })
                    .then((stream) => {
                        this.webcamStream = stream;
                        const video = document.getElementById('webcamVideo');
                        video.srcObject = stream;
                        video.onloadedmetadata = () => {
                            video.play();
                            this.webcamLoading = false;
                        };
                    })
                    .catch((err) => {
                        console.warn('Webcam tidak dapat diakses atau izin ditolak:', err);
                        alert('Kamera webcam tidak dapat diakses atau izin belum diberikan. Mengalihkan ke pemilihan file...');
                        this.closeWebcam();
                        document.getElementById('cameraInput').click();
                    });
                },

                closeWebcam() {
                    if (this.webcamStream) {
                        this.webcamStream.getTracks().forEach(track => track.stop());
                        this.webcamStream = null;
                    }
                    this.showWebcamModal = false;
                    this.webcamLoading = false;
                },

                captureWebcam() {
                    const video = document.getElementById('webcamVideo');
                    if (!video || !this.webcamStream) return;

                    const canvas = document.createElement('canvas');
                    canvas.width = video.videoWidth || 1280;
                    canvas.height = video.videoHeight || 720;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                    canvas.toBlob((blob) => {
                        if (!blob) {
                            alert('Gagal mengambil foto dari kamera.');
                            return;
                        }

                        const compressedSizeKB = (blob.size / 1024).toFixed(0);
                        this.compressionInfo = 'Foto Kamera WebP: ' + compressedSizeKB + ' KB';
                        this.previewUrl = URL.createObjectURL(blob);

                        const newFile = new File([blob], 'rnd_webcam_' + Date.now() + '.webp', { type: 'image/webp' });
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(newFile);
                        document.getElementById('realPhotoInput').files = dataTransfer.files;

                        this.closeWebcam();
                    }, 'image/webp', 0.82);
                },

                handleImageSelect(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    const originalSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                    this.compressionInfo = 'Mengompres foto (' + originalSizeMB + ' MB)...';

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const img = new Image();
                        img.onload = () => {
                            // Canvas Compression to WebP
                            const canvas = document.createElement('canvas');
                            let width = img.width;
                            let height = img.height;
                            const maxDim = 1600; // Optimal 1600px max width/height

                            if (width > height && width > maxDim) {
                                height = Math.round((height * maxDim) / width);
                                width = maxDim;
                            } else if (height > maxDim) {
                                width = Math.round((width * maxDim) / height);
                                height = maxDim;
                            }

                            canvas.width = width;
                            canvas.height = height;
                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(img, 0, 0, width, height);

                            canvas.toBlob((blob) => {
                                if (!blob) {
                                    alert('Gagal memproses gambar.');
                                    return;
                                }

                                const compressedSizeKB = (blob.size / 1024).toFixed(0);
                                this.compressionInfo = 'Terkonversi WebP: ' + compressedSizeKB + ' KB (dari ' + originalSizeMB + ' MB)';
                                this.previewUrl = URL.createObjectURL(blob);

                                // Attach blob to the real file input for submission
                                const newFile = new File([blob], 'rnd_capture.webp', { type: 'image/webp' });
                                const dataTransfer = new DataTransfer();
                                dataTransfer.items.add(newFile);
                                document.getElementById('realPhotoInput').files = dataTransfer.files;
                            }, 'image/webp', 0.82);
                        };
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                },

                resetImage() {
                    this.previewUrl = null;
                    this.compressionInfo = '';
                    document.getElementById('realPhotoInput').value = '';
                    document.getElementById('cameraInput').value = '';
                    document.getElementById('galleryInput').value = '';
                }
            };
        }
    </script>
</body>
</html>
