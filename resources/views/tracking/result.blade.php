<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Sepatu - {{ $input }}</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            background-color: #f3f4f6;
            background-image: radial-gradient(#e5e7eb 1px, transparent 1px);
            background-size: 20px 20px;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="p-4 md:p-8 antialiased text-gray-800" 
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
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-6 md:mb-10 items-center">
            <div>
                <a href="{{ route('tracking.index') }}" class="group inline-flex items-center gap-2 mb-4 md:mb-6 text-gray-500 hover:text-teal-600 transition-colors">
                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow group-hover:bg-teal-500 group-hover:text-white transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </div>
                    <span class="font-medium text-sm tracking-wide">Kembali ke Pencarian</span>
                </a>
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-black text-gray-800 mb-2 tracking-tight">STATUS <span class="text-teal-600">ORDER</span></h1>
                <div class="flex items-center gap-3">
                    <span class="px-2 md:px-3 py-1 rounded bg-white border border-gray-200 text-gray-600 font-mono text-xs md:text-sm tracking-wider shadow-sm break-all">
                        {{ $isPhone ? 'Pencarian No HP' : 'Pencarian SPK' }}: {{ $input }}
                    </span>
                    

                </div>
            </div>
            
            <!-- Logo small -->
            <div class="hidden md:flex justify-end opacity-80 hover:opacity-100 transition-opacity duration-500">
                 <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-20 drop-shadow-md">
            </div>
        </div>

        {{-- MODE 1: LIST VIEW (If Phone Search OR Multiple Results) --}}
        @if($isPhone || $orders->count() > 1)
            <div class="bg-white rounded-2xl md:rounded-3xl shadow-xl p-4 md:p-8 border-t-4 md:border-t-8 border-teal-500 min-h-[400px]">
                <h2 class="text-xl md:text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-teal-600 to-teal-400 mb-4 md:mb-6">
                    Ditemukan {{ $orders->count() }} Sepatu Aktif
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($orders as $order)
                        <div class="bg-gray-50 rounded-2xl p-6 hover:shadow-lg transition-all duration-300 border border-gray-100 group relative overflow-hidden flex flex-col h-full">
                            <div class="absolute top-0 right-0 w-20 h-20 bg-teal-100 rounded-bl-full -mr-4 -mt-4 opacity-50 group-hover:bg-teal-200 transition-colors"></div>

                            {{-- Card Hero Image (If photos exist) --}}
                            @php
                                $heroPhoto = $order->photos->where('is_public', true)->last(); // Get latest public photo
                            @endphp
                            
                            @if($heroPhoto)
                                <div class="w-full h-40 mb-4 rounded-xl overflow-hidden relative border border-gray-200 group-hover:shadow-md transition-shadow cursor-zoom-in"
                                     @click="openLightbox('{{ Storage::url($heroPhoto->file_path) }}', '{{ $order->shoe_brand }} - {{ str_replace('_', ' ', $heroPhoto->step) }}')">
                                     <img src="{{ Storage::url($heroPhoto->file_path) }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" alt="Foto Sepatu">
                                     <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent p-3 pt-8">
                                         <span class="text-white text-[10px] font-black uppercase tracking-wider shadow-sm">{{ str_replace('_', ' ', $heroPhoto->step) }}</span>
                                     </div>
                                 </div>
                            @endif

                            <div class="relative z-10 flex-1">
                                <span class="px-2 py-1 bg-white rounded border border-gray-200 text-xs font-mono font-bold text-gray-500 mb-3 inline-block">
                                    {{ $order->spk_number }}
                                </span>
                                @if(in_array($order->priority, ['Prioritas', 'Urgent', 'Express']))
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700 border border-red-200 uppercase tracking-wider ml-1 align-middle">
                                        Prioritas
                                    </span>
                                @endif
                                
                                <h3 class="text-lg font-black text-gray-800 mb-1 leading-tight">{{ $order->shoe_brand }}</h3>
                                <p class="text-sm text-gray-500 mb-4">{{ $order->shoe_color }}</p>

                                <div class="mt-4 pt-4 border-t border-gray-200 flex justify-between items-center">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] uppercase font-bold text-gray-400">Status</span>
                                        <span class="text-sm font-bold text-teal-600">{{ str_replace('_', ' ', $order->status->value ?? $order->status) }}</span>
                                    </div>
                                    
                                    {{-- Recursive form to view detail (Using SPK Input to reuse logic or Link Mode) --}}
                                    {{-- Since we changed logic to accept SPK input in same form, we can just link to ?spk_number=SPK... --}}
                                    <form action="{{ route('tracking.track') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="spk_number" value="{{ $order->spk_number }}">
                                        <button type="submit" class="w-8 h-8 rounded-full bg-teal-500 text-white flex items-center justify-center hover:bg-teal-600 transition-colors shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        {{-- MODE 2: DETAIL VIEW (Single Result & NOT generic phone search logic if we want strict detail) --}}
        {{-- Actually if we reused the existing Detail View code, we just need to pick $orders->first() --}}
        {{-- MODE 2: DETAIL VIEW --}}
        @else
            @php $order = $orders->first(); @endphp
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column: Details -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Customer Info Card -->
                    <div class="bg-white rounded-2xl md:rounded-3xl shadow-xl p-4 md:p-8 border-t-4 md:border-t-8 border-teal-500 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-16 md:w-24 h-16 md:h-24 bg-teal-50 rounded-bl-full -mr-4 -mt-4 opacity-50"></div>
                        
                        <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2 relative z-10">
                            <svg class="w-5 h-5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Informasi Pelanggan
                            @if(in_array($order->priority, ['Prioritas', 'Urgent', 'Express']))
                                <span class="bg-red-100 text-red-700 text-[10px] font-bold px-2 py-0.5 rounded border border-red-200 uppercase tracking-wider">Prioritas</span>
                            @endif
                        </h2>
                        
                        <div class="space-y-5 relative z-10">
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Nama</p>
                                <p class="font-bold text-lg text-gray-800">{{ $order->customer_name }}</p>
                            </div>

                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Estimasi Selesai</p>
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-orange-50 rounded-lg border border-orange-100">
                                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <p class="font-bold text-orange-700">{{ $order->estimation_date->format('d M Y') }}</p>
                                </div>
                            </div>
                        </div>

                        @if($order->workOrderServices->count() > 0)
                            <div class="mt-8 pt-6 border-t border-gray-100">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Layanan</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($order->workOrderServices as $detail)
                                        <span class="px-3 py-1 bg-teal-50 text-teal-700 border border-teal-100 rounded-lg text-xs font-bold">
                                            {{ $detail->custom_service_name ?? ($detail->service ? $detail->service->name : 'Layanan') }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Foto Kondisi Section -->
                        <div class="mt-8 pt-6 border-t border-gray-100">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Visual Kondisi</p>
                            @php
                                $refPhoto = $order->photos->where('step', 'RECEPTION')->first();
                                $beforePhoto = $order->photos->where('is_spk_cover', true)->first();
                                $afterPhoto = $order->photos->where('step', 'FINISH')->last() ?? $order->photos->where('step', 'SELESAI')->last();
                            @endphp
                            
                            <div class="grid grid-cols-3 gap-3">
                                <!-- Reference -->
                                <div class="space-y-2">
                                    <div class="aspect-square rounded-xl bg-gray-50 border-2 border-gray-100 overflow-hidden relative group cursor-zoom-in"
                                         @if($refPhoto) @click="openLightbox('{{ Storage::url($refPhoto->file_path) }}', 'Foto Referensi')" @endif>
                                        @if($refPhoto)
                                            <img src="{{ Storage::url($refPhoto->file_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="Reference">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <p class="text-[9px] font-black text-center text-gray-400 uppercase tracking-tighter">Referansi</p>
                                </div>

                                <!-- Before (Strictly SPK Cover) -->
                                <div class="space-y-2">
                                    <div class="aspect-square rounded-xl bg-gray-50 border-2 border-teal-200 overflow-hidden relative group ring-4 ring-teal-50 cursor-zoom-in"
                                         @if($beforePhoto) @click="openLightbox('{{ Storage::url($beforePhoto->file_path) }}', 'Foto Sebelum')" @endif>
                                        @if($beforePhoto)
                                            <img src="{{ Storage::url($beforePhoto->file_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="Before">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <p class="text-[9px] font-bold text-center text-teal-600 uppercase tracking-tighter">Sebelum</p>
                                </div>

                                <!-- After -->
                                <div class="space-y-2">
                                    <div class="aspect-square rounded-xl bg-gray-50 border-2 border-gray-100 overflow-hidden relative group cursor-zoom-in"
                                         @if($afterPhoto) @click="openLightbox('{{ Storage::url($afterPhoto->file_path) }}', 'Foto Sesudah')" @endif>
                                        @if($afterPhoto)
                                            <img src="{{ Storage::url($afterPhoto->file_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="After">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <p class="text-[9px] font-black text-center text-gray-400 uppercase tracking-tighter">Sesudah</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Timeline -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl md:rounded-3xl shadow-xl p-6 md:p-10 relative overflow-hidden min-h-[400px]">
                        <h2 class="text-xl md:text-2xl font-black text-gray-800 mb-10 flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full bg-teal-500 animate-pulse"></span>
                            Timeline Pengerjaan
                        </h2>

                        @php
                            $statuses = [
                                'DITERIMA' => [
                                    'label' => 'Terima', 
                                    'icon' => '<svg class="w-8 h-8 md:w-10 md:h-10 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>', 
                                ],
                                'ASSESSMENT' => [
                                    'label' => 'Pengecekan', 
                                    'icon' => '<svg class="w-8 h-8 md:w-10 md:h-10 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>', 
                                ],
                                'PREPARATION' => [
                                    'label' => 'Cuci', 
                                    'icon' => '<svg class="w-8 h-8 md:w-10 md:h-10 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>', 
                                ],
                                'SORTIR' => [
                                    'label' => 'Persiapan Bahan', 
                                    'icon' => '<svg class="w-8 h-8 md:w-10 md:h-10 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>', 
                                ],
                                'PRODUCTION' => [
                                    'label' => 'Service', 
                                    'icon' => '<svg class="w-8 h-8 md:w-10 md:h-10 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>', 
                                ],
                                'QC' => [
                                    'label' => 'QC Checking', 
                                    'icon' => '<svg class="w-8 h-8 md:w-10 md:h-10 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>', 
                                ],
                                'SELESAI' => [
                                    'label' => 'Dikemas & Kirim', 
                                    'icon' => '<svg class="w-8 h-8 md:w-10 md:h-10 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>', 
                                ],
                            ];
                            $statusKeys = array_keys($statuses);
                            $currentStatusVal = is_object($order->status) ? $order->status->value : $order->status;
                            $currentIndex = array_search($currentStatusVal, $statusKeys);
                            if ($currentIndex === false && is_object($order->status)) {
                                $currentIndex = array_search($order->status->name, $statusKeys);
                            }
                        @endphp

                        <!-- Responsive Stepper Container -->
                        <div class="relative w-full pb-0 md:pb-8">
                             
                            <!-- Desktop: Horizontal Lines -->
                            <div class="hidden md:block absolute top-[20px] left-0 right-0 h-1 bg-gray-200 w-full z-0 translate-y-1/2"></div>
                            @php
                                $progressPercent = $currentIndex !== false ? min(100, ($currentIndex / (count($statuses) - 1)) * 100) : 0;
                            @endphp
                            <div class="hidden md:block absolute top-[20px] left-0 h-1 bg-teal-500 z-0 translate-y-1/2 transition-all duration-1000 ease-out" style="width: {{ $progressPercent }}%;"></div>

                            <!-- Mobile: Vertical Line -->
                            <div class="md:hidden absolute left-[22px] top-4 bottom-4 w-1 bg-gray-200 z-0"></div>

                            <!-- Flex Container -->
                            <div class="flex flex-col md:flex-row items-start justify-between w-full relative space-y-8 md:space-y-0 px-0 md:px-6">
                                @foreach($statuses as $key => $status)
                                    @php
                                        $index = array_search($key, $statusKeys);
                                        $isCompleted = $index <= $currentIndex;
                                        $isCurrent = $index === $currentIndex;
                                        
                                        $iconColorClass = $isCompleted ? 'text-teal-500' : 'text-gray-300';
                                        $circleColorClass = $isCompleted ? 'bg-teal-100 text-teal-600 border-teal-500' : 'bg-gray-100 text-gray-400 border-gray-200';
                                        
                                        if ($isCurrent) {
                                            $circleColorClass = 'bg-teal-500 text-white border-teal-600 shadow-lg scale-110';
                                            $iconColorClass = 'text-teal-600 drop-shadow-md';
                                        }

                                        // Get Timestamp
                                        $timestamp = null;
                                        if ($key === 'DITERIMA') $timestamp = $order->created_at; 
                                        elseif ($key === 'SELESAI' && $order->finished_date) $timestamp = $order->finished_date;
                                        else {
                                            $log = $order->logs->where('step', $key)->sortByDesc('created_at')->first();
                                            if ($log) $timestamp = $log->created_at;
                                        }
                                    @endphp
                                    
                                    <div class="relative z-10 flex flex-row md:flex-col items-center group cursor-default w-full md:w-auto gap-4 md:gap-0">
                                        
                                        <!-- Number Bubble -->
                                        <div class="w-12 h-12 flex-shrink-0 rounded-full border-4 {{ $circleColorClass }} flex items-center justify-center font-bold text-base transition-all duration-300 relative bg-white">
                                            {{ $loop->iteration }}
                                            
                                            <!-- Valid Checkmark for past items -->
                                            @if($isCompleted && !$isCurrent)
                                                <div class="absolute -right-1 -top-1 bg-teal-500 text-white rounded-full p-0.5">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Dashed Line Connector (Desktop Only) -->
                                        <div class="hidden md:block h-8 border-l-2 border-dashed {{ $isCompleted ? 'border-teal-300' : 'border-gray-300' }} my-1"></div>

                                        <!-- Content Wrapper (Mobile Row / Desktop Col) -->
                                        <div class="flex flex-row md:flex-col items-center flex-1 gap-4 md:gap-0">
                                            
                                            <!-- Icon -->
                                            <div class="{{ $iconColorClass }} transition-all duration-300 transform group-hover:scale-110 flex-shrink-0">
                                                {!! $status['icon'] !!}
                                            </div>

                                            <!-- Label -->
                                            <div class="md:mt-2 text-left md:text-center w-full">
                                                <p class="text-sm md:text-sm font-bold {{ $isCompleted ? 'text-gray-800' : 'text-gray-400' }}">{{ $status['label'] }}</p>
                                                
                                                @if($timestamp)
                                                    <p class="text-[10px] text-gray-500 mt-0.5 md:mt-1 font-mono bg-gray-50 px-1 rounded inline-block">
                                                        {{ \Carbon\Carbon::parse($timestamp)->format('d/m H:i') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Description Area for Active Step -->
                        @if($currentIndex !== false && isset($statusKeys[$currentIndex]))
                             <div class="mt-8 bg-teal-50 rounded-xl p-6 border border-teal-100 flex items-start gap-4">
                                <div class="bg-teal-100 p-3 rounded-full text-teal-600">
                                   <svg class="w-6 h-6 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-teal-800 text-lg">Status Saat Ini: {{ $statuses[$statusKeys[$currentIndex]]['label'] }}</h3>
                                    <p class="text-teal-700/80 text-sm mt-1">
                                        Order Anda sedang dalam proses {{ strtolower($statuses[$statusKeys[$currentIndex]]['label']) }}. 
                                        @if($currentIndex < count($statuses)-1)
                                            Langkah berikutnya: <strong>{{ $statuses[$statusKeys[$currentIndex+1]]['label'] }}</strong>.
                                        @else
                                            Terima kasih telah mempercayakan sepatu Anda kepada kami!
                                        @endif
                                    </p>
                                </div>
                             </div>
                        @endif

                    </div>
                </div>
            </div>
        @endif
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
         class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-black/90 backdrop-blur-sm"
         @keydown.escape.window="showLightbox = false">
        
        <!-- Close Button -->
        <button @click="showLightbox = false" class="absolute top-6 right-6 text-white/70 hover:text-white transition-colors z-50">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <div class="relative max-w-4xl w-full flex flex-col items-center" @click.away="showLightbox = false">
            <img :src="lightboxImage" class="max-h-[85vh] w-auto rounded-xl shadow-2xl border-4 border-white/10" alt="Full Image">
            <p x-text="lightboxCaption" class="mt-4 text-white font-bold text-lg tracking-wide bg-black/50 px-4 py-2 rounded-full"></p>
        </div>
    </div>

    <!-- Cekat AI Live Chat Widget -->
    <script type="text/javascript">
        !function(c,e,k,a,t){
        c.mychat=c.mychat||{server:"https://live.cekat.ai/widget.js",iframeWidth:"400px",iframeHeight:"700px",accessKey:"Shoeworksh-9qDov338"};
        var q=[];
        c.Cekat=function(){q.push(arguments)};
        c.Cekat.q=q;
        a=e.createElement(k);
        t=e.getElementsByTagName(k)[0];
        a.async=1;
        a.src=c.mychat.server;
        t.parentNode.insertBefore(a,t);
        }(window,document,"script");
    </script>
</body>
</html>
