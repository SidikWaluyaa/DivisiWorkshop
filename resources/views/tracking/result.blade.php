<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Sepatu - {{ $input }}</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #f3f4f6;
            background-image: radial-gradient(#e5e7eb 1px, transparent 1px);
            background-size: 20px 20px;
        }
    </style>
</head>
<body class="p-4 md:p-8 antialiased text-gray-800">
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
                                <div class="w-full h-40 mb-4 rounded-xl overflow-hidden relative border border-gray-200 group-hover:shadow-md transition-shadow">
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
                        
                        @if($order->notes)
                            <div class="mt-6 pt-6 border-t border-gray-100">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Catatan Pesanan</p>
                                <div class="bg-gray-50 text-gray-700 p-4 rounded-xl border border-gray-200 text-sm italic relative">
                                    <svg class="w-8 h-8 text-gray-200 absolute -top-3 -left-2 transform -rotate-12" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21L14.017 18C14.017 16.8954 14.9124 16 16.017 16H19.017C19.5693 16 20.017 15.5523 20.017 15V9C20.017 8.44772 19.5693 8 19.017 8H4.017C3.46472 8 3.017 8.44772 3.017 9V18C3.017 18.5523 3.46472 19 4.017 19H14.017ZM16.017 18V20.1005C16.017 20.7072 16.7153 21.0506 17.18 20.672L19.5765 18.7548C19.8242 18.5566 19.9652 18.2577 19.9652 17.9402V18C19.9652 16.8954 19.0706 16 17.9652 16H16.017Z"></path></svg>
                                    <span class="relative z-10">"{{ $order->notes }}"</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Column: Timeline -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl md:rounded-3xl shadow-xl p-4 md:p-8 lg:p-10 relative overflow-hidden min-h-[500px]">
                        <h2 class="text-xl md:text-2xl font-black text-gray-800 mb-6 md:mb-8 flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full bg-orange-500 animate-pulse"></span>
                            TIMELINE PENGERJAAN
                        </h2>

                        @php
                            $statuses = [
                                'DITERIMA' => [
                                    'label' => 'Diterima Gudang', 
                                    'icon' => '📦', 
                                    'desc' => 'Sepatu Anda sudah aman di tangan kami! Siap untuk didata sebelum masuk proses selanjutnya.'
                                ],
                                'ASSESSMENT' => [
                                    'label' => 'Assessment & Pengecekan', 
                                    'icon' => '🔍', 
                                    'desc' => 'Tim kami sedang memeriksa kondisi sepatu secara detail untuk memastikan penanganan yang tepat.'
                                ],
                                'PREPARATION' => [
                                    'label' => 'Preparation & Cleaning', 
                                    'icon' => '🧼', 
                                    'desc' => 'Tahap awal pembersihan mendalam. Debu dan kotoran mulai kami hilangkan.'
                                ],
                                'SORTIR' => [
                                    'label' => 'Persiapan Material', 
                                    'icon' => '📋', 
                                    'desc' => 'Sedang menyiapkan material terbaik (Sol, Lem, dll) agar sepatu Anda kembali prima.'
                                ],
                                'PRODUCTION' => [
                                    'label' => 'Production (Repair & Repaint)', 
                                    'icon' => '🔨', 
                                    'desc' => 'Magic happens here! Para ahli kami sedang bekerja keras memperbaiki dan memoles sepatu Anda.'
                                ],
                                'QC' => [
                                    'label' => 'Quality Control', 
                                    'icon' => '✅', 
                                    'desc' => 'Pengecekan akhir yang ketat demi hasil presisi. Kami pastikan tidak ada yang terlewat!'
                                ],
                                'SELESAI' => [
                                    'label' => 'Selesai & Siap Diambil', 
                                    'icon' => '🎉', 
                                    'desc' => 'Horee! Sepatu Anda sudah ganteng maksimal. Yuk segera jemput sepatu kesayangan Anda.'
                                ],
                            ];
                            $statusKeys = array_keys($statuses);
                            $currentIndex = array_search($order->status, $statusKeys); // Status is Enum or string
                            if (is_object($order->status)) $currentIndex = array_search($order->status->name, $statusKeys);
                        @endphp

                        <div class="relative pl-0">
                            <!-- Premium Gradient Line (Background) -->
                            <div class="absolute left-[39px] md:left-[63px] top-8 bottom-8 w-1 bg-gradient-to-b from-gray-200 via-gray-300 to-gray-200 rounded-full z-0"></div>

                            <div class="space-y-8 relative z-10">
                                @foreach($statuses as $key => $status)
                                    @php
                                        $index = array_search($key, $statusKeys);
                                        $isCompleted = $index <= $currentIndex;
                                        $currentStatusName = is_object($order->status) ? $order->status->name : $order->status;
                                        $isCurrent = $key === $currentStatusName;
                                        
                                        // Dynamic Classes
                                        $cardClasses = $isCurrent 
                                            ? 'bg-white border-2 border-orange-400 shadow-[0_10px_40px_-10px_rgba(251,146,60,0.3)] scale-[1.02] md:scale-105 ring-4 ring-orange-50' 
                                            : ($isCompleted ? 'bg-white border border-gray-100 shadow-sm opacity-100' : 'bg-gray-50 border border-transparent opacity-60 grayscale');
                                            
                                        $iconClasses = $isCurrent
                                            ? 'bg-gradient-to-br from-orange-400 to-pink-500 text-white shadow-lg scale-110 ring-4 ring-white'
                                            : ($isCompleted ? 'bg-gradient-to-br from-teal-400 to-emerald-500 text-white shadow-md' : 'bg-gray-200 text-gray-400');

                                        $timestamp = null;
                                        if ($key === 'DITERIMA') $timestamp = $order->created_at; 
                                        elseif ($key === 'SELESAI' && $order->finished_date) $timestamp = $order->finished_date;
                                        else {
                                            $log = $order->logs->where('step', $key)->sortByDesc('created_at')->first();
                                            if ($log) $timestamp = $log->created_at;
                                            if (!$timestamp && $isCurrent) $timestamp = $order->updated_at;
                                        }
                                    @endphp
                                    
                                    <div class="flex flex-col md:flex-row gap-6 md:gap-10 group relative transition-all duration-300 {{ $isCurrent ? 'z-20' : 'z-10' }}">
                                        <!-- Time & Icon Column -->
                                        <div class="flex flex-row md:flex-col items-center md:items-center md:w-32 flex-shrink-0 relative">
                                            <!-- Icon Bubble -->
                                            <div class="relative z-10 w-12 h-12 md:w-16 md:h-16 rounded-2xl {{ $iconClasses }} flex items-center justify-center text-xl md:text-2xl transition-all duration-500">
                                                {{ $status['icon'] }}
                                                @if($isCompleted && !$isCurrent)
                                                    <div class="absolute -bottom-1 -right-1 bg-white rounded-full p-1 shadow-sm">
                                                        <svg class="w-3 h-3 md:w-4 md:h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            @if(($isCurrent || $isCompleted) && $timestamp)
                                                <div class="hidden md:flex flex-col items-center mt-3 text-center bg-gray-50 px-2 py-1 rounded-lg">
                                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ \Carbon\Carbon::parse($timestamp)->format('d M') }}</span>
                                                    <span class="text-[10px] font-medium text-gray-400">{{ \Carbon\Carbon::parse($timestamp)->format('H:i') }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Content Card -->
                                        <div class="flex-1 rounded-2xl p-5 md:p-6 transition-all duration-300 {{ $cardClasses }}">
                                            <div class="flex justify-between items-start mb-2">
                                                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                                    {{ $status['label'] }}
                                                    @if($isCurrent)
                                                        <span class="flex h-2 w-2 relative">
                                                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                                                          <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
                                                        </span>
                                                    @endif
                                                </h3>
                                                @if(($isCurrent || $isCompleted) && $timestamp)
                                                    <span class="md:hidden text-xs text-gray-400 font-mono bg-gray-100 px-2 py-1 rounded">{{ \Carbon\Carbon::parse($timestamp)->format('d M, H:i') }}</span>
                                                @endif
                                            </div>
                                            
                                            <p class="text-sm text-gray-500 leading-relaxed font-medium mb-3">
                                                {{ $status['desc'] }}
                                            </p>


                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        @endif
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
