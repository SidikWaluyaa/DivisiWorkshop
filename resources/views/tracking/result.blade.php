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

                    <!-- Photo Gallery Card -->
                    {{-- Visual Journey Removed from here, moved to Timeline --}}
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
                                    'color' => 'gray',
                                    'desc' => 'Sepatu Anda sudah aman di tangan kami! Siap untuk didata sebelum masuk proses selanjutnya.'
                                ],
                                'ASSESSMENT' => [
                                    'label' => 'Assessment & Pengecekan', 
                                    'icon' => '🔍', 
                                    'color' => 'blue',
                                    'desc' => 'Tim kami sedang memeriksa kondisi sepatu secara detail untuk memastikan penanganan yang tepat.'
                                ],
                                'PREPARATION' => [
                                    'label' => 'Preparation & Cleaning', 
                                    'icon' => '🧼', 
                                    'color' => 'cyan',
                                    'desc' => 'Tahap awal pembersihan mendalam. Debu dan kotoran mulai kami hilangkan.'
                                ],
                                'SORTIR' => [
                                    'label' => 'Persiapan Material', 
                                    'icon' => '📋', 
                                    'color' => 'indigo',
                                    'desc' => 'Sedang menyiapkan material terbaik (Sol, Lem, dll) agar sepatu Anda kembali prima.'
                                ],
                                'PRODUCTION' => [
                                    'label' => 'Production (Repair & Repaint)', 
                                    'icon' => '🔨', 
                                    'color' => 'orange',
                                    'desc' => 'Magic happens here! Para ahli kami sedang bekerja keras memperbaiki dan memoles sepatu Anda.'
                                ],
                                'QC' => [
                                    'label' => 'Quality Control', 
                                    'icon' => '✅', 
                                    'color' => 'teal',
                                    'desc' => 'Pengecekan akhir yang ketat demi hasil presisi. Kami pastikan tidak ada yang terlewat!'
                                ],
                                'SELESAI' => [
                                    'label' => 'Selesai & Siap Diambil', 
                                    'icon' => '🎉', 
                                    'color' => 'green',
                                    'desc' => 'Horee! Sepatu Anda sudah ganteng maksimal. Yuk segera jemput sepatu kesayangan Anda.'
                                ],
                            ];
                            $statusKeys = array_keys($statuses);
                            $currentIndex = array_search($order->status, $statusKeys); // Status is Enum or string
                            if (is_object($order->status)) $currentIndex = array_search($order->status->name, $statusKeys);
                        @endphp

                        <div class="relative pl-2">
                            <!-- Connecting Line -->
                            <div class="absolute left-[27px] top-6 bottom-6 w-0.5 bg-gray-200 -ml-px z-0"></div>

                            <div class="space-y-4 relative z-10">
                                @foreach($statuses as $key => $status)
                                    @php
                                        $index = array_search($key, $statusKeys);
                                        $isCompleted = $index <= $currentIndex;
                                        // Handle Enum comparison safely
                                        $currentStatusName = is_object($order->status) ? $order->status->name : $order->status;
                                        $isCurrent = $key === $currentStatusName;
                                        
                                        $activeColor = $isCurrent ? 'bg-orange-500 ring-4 ring-orange-100 shadow-xl scale-110' : ($isCompleted ? 'bg-teal-500' : 'bg-gray-200');
                                        $textColor = $isCompleted ? 'text-gray-900' : 'text-gray-400';
                                    @endphp
                                    
                                    <div class="flex gap-4 items-center group relative">
                                        <!-- Icon Node -->
                                        <div class="relative flex-shrink-0 w-12 h-12 rounded-full {{ $activeColor }} flex items-center justify-center text-xl text-white transition-all duration-300 z-10 border-4 border-white shadow-sm">
                                            {{ $status['icon'] }}
                                        </div>

                                        <!-- Content Body -->
                                        <div class="flex-1">
                                            <h3 class="font-bold text-base {{ $textColor }} transition-colors flex items-center gap-3">
                                                {{ $status['label'] }}
                                                @if($isCurrent)
                                                    <span class="px-2 py-0.5 rounded-full bg-orange-100 text-orange-600 text-[10px] font-black uppercase tracking-wider animate-pulse border border-orange-200">
                                                        Sedang Dikerjakan
                                                    </span>
                                                @endif
                                            </h3>
                                            <p class="text-xs mt-1 leading-relaxed text-gray-500 font-medium">
                                                {{ $status['desc'] }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        @if($order->status === 'SELESAI')
                            <div class="mt-10 p-8 bg-green-50 rounded-2xl border-2 border-green-500 shadow-lg text-center relative overflow-hidden">
                                <div class="relative z-10">
                                    <h3 class="text-3xl font-black mb-2 text-green-800">🎉 SELESAI & SIAP DIAMBIL!</h3>
                                    <p class="text-lg text-green-700 mb-6 font-medium">Sepatu Anda sudah kinclong kembali.</p>
                                    
                                    <div class="flex flex-col md:flex-row items-center justify-center gap-4">
                                        <!-- Hubungi Admin Removed for Live Chat -->

                                        <div class="w-full md:w-auto inline-flex items-center justify-center gap-2 bg-green-600 text-white px-6 py-3 rounded-xl font-bold shadow-md cursor-default">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            Ambil di Toko
                                        </div>
                                    </div>
                                    <p class="text-sm text-green-600/80 mt-4 italic">Silakan hubungi admin atau datang langsung ke toko.</p>
                                </div>
                            </div>
                        @endif
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
