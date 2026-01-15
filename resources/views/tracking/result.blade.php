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
                    
                    {{-- Complaint Button --}}
                    <a href="{{ route('complaints.index') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1 rounded bg-rose-50 border border-rose-200 text-rose-600 hover:bg-rose-100 transition-colors text-xs font-bold uppercase tracking-wide">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Lapor Masalah
                    </a>
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
                                        <span class="text-sm font-bold text-teal-600">{{ str_replace('_', ' ', $order->status) }}</span>
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
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Sepatu</p>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-xl">👟</div>
                                    <div>
                                        <p class="font-bold text-gray-800 leading-tight">{{ $order->shoe_brand }}</p>
                                        <p class="text-sm text-gray-500">{{ $order->shoe_color }}</p>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Estimasi Selesai</p>
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-orange-50 rounded-lg border border-orange-100">
                                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <p class="font-bold text-orange-700">{{ $order->estimation_date->format('d M Y') }}</p>
                                </div>
                            </div>
                        </div>

                        @if($order->services->count() > 0)
                            <div class="mt-8 pt-6 border-t border-gray-100">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Layanan</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($order->services as $service)
                                        <span class="px-3 py-1 bg-teal-50 text-teal-700 border border-teal-100 rounded-lg text-xs font-bold">
                                            {{ $service->name }}
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
                                'DITERIMA' => ['label' => 'Diterima Gudang', 'icon' => '📦', 'color' => 'gray'],
                                'ASSESSMENT' => ['label' => 'Assessment', 'icon' => '🔍', 'color' => 'blue'],
                                'PREPARATION' => ['label' => 'Preparation', 'icon' => '🧼', 'color' => 'cyan'],
                                'SORTIR' => ['label' => 'Sortir & Material', 'icon' => '📋', 'color' => 'indigo'],
                                'PRODUCTION' => ['label' => 'Production (Pijat & Jahit)', 'icon' => '🔨', 'color' => 'orange'],
                                'QC' => ['label' => 'Quality Control', 'icon' => '✅', 'color' => 'teal'],
                                'SELESAI' => ['label' => 'Siap Diambil', 'icon' => '🎉', 'color' => 'green'],
                            ];
                            $statusKeys = array_keys($statuses);
                            $currentIndex = array_search($order->status, $statusKeys);
                        @endphp

                        <div class="relative">
                            <!-- Connecting Line -->
                            <div class="absolute left-[22px] top-4 bottom-4 w-1 bg-gray-100 rounded-full"></div>

                            <div class="space-y-8 relative z-10">
                                @foreach($statuses as $key => $status)
                                    @php
                                        $index = array_search($key, $statusKeys);
                                        $isCompleted = $index <= $currentIndex;
                                        $isCurrent = $key === $order->status;
                                        
                                        $activeColor = $isCurrent ? 'bg-orange-500 ring-4 ring-orange-100 shadow-lg scale-110' : ($isCompleted ? 'bg-teal-500' : 'bg-gray-200');
                                        $textColor = $isCompleted ? 'text-gray-900' : 'text-gray-400';
                                    @endphp
                                    
                                    <div class="flex gap-6 group">
                                        <!-- Icon Node -->
                                        <div class="flex-shrink-0 w-12 h-12 rounded-full {{ $activeColor }} flex items-center justify-center text-xl text-white transition-all duration-300 z-10 border-4 border-white shadow-sm">
                                            {{ $status['icon'] }}
                                        </div>

                                        <!-- Content -->
                                        <div class="flex-1 pt-1 pb-4 border-b border-gray-50 group-last:border-0">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <h3 class="font-bold text-lg {{ $textColor }} transition-colors flex items-center gap-2">
                                                        {{ $status['label'] }}
                                                        @if($isCurrent)
                                                            <span class="px-2 py-0.5 rounded-full bg-orange-100 text-orange-600 text-[10px] font-black uppercase tracking-wider animate-pulse">
                                                                Sedang Dikerjakan
                                                            </span>
                                                        @endif
                                                    </h3>
                                                    
                                                    <!-- Step Logs -->
                                                    @if($isCompleted)
                                                        @php
                                                            $stepLogs = $order->logs->where('step', $key)->sortBy('created_at');
                                                        @endphp
                                                        {{-- Sortir Materials --}}
                                                        @if($key === 'SORTIR' && $order->materials->count() > 0)
                                                            <div class="mt-3 p-3 bg-indigo-50 rounded-xl border border-indigo-100 inline-block">
                                                                <p class="text-xs font-bold text-indigo-400 uppercase mb-2">Material</p>
                                                                <div class="space-y-1">
                                                                    @foreach($order->materials as $mat)
                                                                        <div class="flex items-center gap-2 text-sm text-indigo-900">
                                                                            <svg class="w-3 h-3 text-indigo-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                                                            {{ $mat->name }} <span class="font-bold">x{{ $mat->pivot->quantity }}</span>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if($stepLogs->count() > 0)
                                                            <div class="mt-3 space-y-2">
                                                                @foreach($stepLogs as $log)
                                                                    @if($log->action !== 'MOVED')
                                                                        <div class="flex items-center gap-3 text-sm group-hover/log">
                                                                            <div class="h-1.5 w-1.5 rounded-full bg-teal-300"></div>
                                                                            <div class="flex-1">
                                                                                <span class="font-medium text-gray-600">
                                                                                    @php
                                                                                        $friendlyText = $log->description ?? ucwords(strtolower(str_replace('_', ' ', $log->action)));
                                                                                        $rawDesc = strtoupper($log->description ?? '');
                                                                                        $rawAction = strtoupper($log->action ?? '');

                                                                                        // COPYWRITING DICTIONARY
                                                                                        if ($key === 'PREPARATION') {
                                                                                            if (str_contains($rawDesc, 'START') || str_contains($rawAction, 'START')) {
                                                                                                if (str_contains($rawDesc, 'WASHING')) $friendlyText = "Sepatu sedang dicuci bersih (Deep Cleaning)";
                                                                                                elseif (str_contains($rawDesc, 'SOL')) $friendlyText = "Sedang proses pembongkaran Sol lama";
                                                                                                elseif (str_contains($rawDesc, 'UPPER')) $friendlyText = "Sedang proses pembongkaran Upper";
                                                                                            } elseif (str_contains($rawDesc, 'FINISH') || str_contains($rawDesc, 'MENYELESAIKAN')) {
                                                                                                if (str_contains($rawDesc, 'WASHING')) $friendlyText = "Proses Deep Cleaning selesai";
                                                                                                elseif (str_contains($rawDesc, 'SOL')) $friendlyText = "Pembongkaran Sol selesai";
                                                                                                elseif (str_contains($rawDesc, 'UPPER')) $friendlyText = "Pembongkaran Upper selesai";
                                                                                            }
                                                                                        }
                                                                                        elseif ($key === 'SORTIR') {
                                                                                            if (str_contains($rawDesc, 'VERIFIED')) $friendlyText = "Material dicek & siap untuk produksi";
                                                                                        }
                                                                                        elseif ($key === 'PRODUCTION') {
                                                                                            if (str_contains($rawDesc, 'START') || str_contains($rawAction, 'START')) {
                                                                                                if (str_contains($rawDesc, 'SOL')) $friendlyText = "Sedang dalam pengerjaan pemasangan Sol baru";
                                                                                                elseif (str_contains($rawDesc, 'UPPER')) $friendlyText = "Sedang dalam perbaikan/jahit Upper";
                                                                                                elseif (str_contains($rawDesc, 'CLEANING')) $friendlyText = "Sedang dalam proses Retouch / Finishing";
                                                                                            } elseif (str_contains($rawDesc, 'FINISH') || str_contains($rawDesc, 'MENYELESAIKAN')) {
                                                                                                if (str_contains($rawDesc, 'SOL')) $friendlyText = "Pemasangan Sol baru selesai";
                                                                                                elseif (str_contains($rawDesc, 'UPPER')) $friendlyText = "Perbaikan Upper selesai";
                                                                                                elseif (str_contains($rawDesc, 'CLEANING')) $friendlyText = "Retouch / Finishing selesai";
                                                                                            }
                                                                                        }
                                                                                        elseif ($key === 'QC') {
                                                                                            if (str_contains($rawDesc, 'START') || str_contains($rawAction, 'START')) {
                                                                                                if (str_contains($rawDesc, 'JAHIT')) $friendlyText = "Quality Control: Pengecekan jahitan";
                                                                                                elseif (str_contains($rawDesc, 'CLEANUP')) $friendlyText = "Quality Control: Pengecekan kebersihan akhir";
                                                                                                elseif (str_contains($rawDesc, 'FINAL')) $friendlyText = "Final Inspection sebelum serah terima";
                                                                                            } elseif (str_contains($rawDesc, 'FINISH') || str_contains($rawDesc, 'MENYELESAIKAN')) {
                                                                                                if (str_contains($rawDesc, 'JAHIT')) $friendlyText = "Jahitan lolos QC";
                                                                                                elseif (str_contains($rawDesc, 'CLEANUP')) $friendlyText = "Kebersihan lolos QC";
                                                                                                elseif (str_contains($rawDesc, 'FINAL')) $friendlyText = "Sepatu Lolos QC Final & Siap Diambil";
                                                                                            }
                                                                                        }
                                                                                        
                                                                                        // Universal Rejection
                                                                                        if (str_contains($rawAction, 'REJEKSI') || str_contains($rawDesc, 'REVISI')) {
                                                                                            $friendlyText = "Ditemukan ketidaksesuaian, sedang direvisi kembali";
                                                                                        }
                                                                                    @endphp
                                                                                    {{ $friendlyText }}
                                                                                </span>
                                                                                <span class="text-xs text-gray-400 ml-2">
                                                                                    {{ $log->created_at->format('d/m H:i') }}
                                                                                    @php
                                                                                        $actorName = $log->user ? explode(' ', $log->user->name)[0] : 'System';

                                                                                        // Override with Assigned Technician if available
                                                                                        if ($key === 'PRODUCTION' && $order->technicianProduction) {
                                                                                            $actorName = explode(' ', $order->technicianProduction->name)[0];
                                                                                        }
                                                                                        elseif ($key === 'QC') {
                                                                                            $desc = strtoupper($log->description ?? '');
                                                                                            if (str_contains($desc, 'JAHIT') && $order->qcJahitTechnician) {
                                                                                                $actorName = explode(' ', $order->qcJahitTechnician->name)[0];
                                                                                            } elseif (str_contains($desc, 'CLEANUP') && $order->qcCleanupTechnician) {
                                                                                                $actorName = explode(' ', $order->qcCleanupTechnician->name)[0];
                                                                                            } elseif (str_contains($desc, 'FINAL') && $order->qcFinalPic) {
                                                                                                $actorName = explode(' ', $order->qcFinalPic->name)[0];
                                                                                            } elseif ($log->action == 'DONE' && $order->qcFinalPic) {
                                                                                                $actorName = explode(' ', $order->qcFinalPic->name)[0];
                                                                                            }
                                                                                        }
                                                                                        elseif ($key === 'SORTIR') {
                                                                                            $desc = strtoupper($log->description ?? '');
                                                                                            if (str_contains($desc, 'SOL') && $order->picSortirSol) {
                                                                                                $actorName = explode(' ', $order->picSortirSol->name)[0];
                                                                                            } elseif (str_contains($desc, 'UPPER') && $order->picSortirUpper) {
                                                                                                $actorName = explode(' ', $order->picSortirUpper->name)[0];
                                                                                            }
                                                                                        }
                                                                                        // NEW: Preparation Logic
                                                                                        elseif ($key === 'PREPARATION') {
                                                                                            $desc = strtoupper($log->description ?? '');
                                                                                            if ((str_contains($desc, 'WASHING') || str_contains($desc, 'CUCI')) && $order->prepWashingBy) {
                                                                                                $actorName = explode(' ', $order->prepWashingBy->name)[0];
                                                                                            } elseif (str_contains($desc, 'SOL') && $order->prepSolBy) {
                                                                                                $actorName = explode(' ', $order->prepSolBy->name)[0];
                                                                                            } elseif (str_contains($desc, 'UPPER') && $order->prepUpperBy) {
                                                                                                $actorName = explode(' ', $order->prepUpperBy->name)[0];
                                                                                            }
                                                                                        }
                                                                                    @endphp
                                                                                    by {{ $actorName }}
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    @endif
                                                    
                                                    @php
                                                        // Filter photos for this step
                                                        $stepPhotos = $order->photos->where('is_public', true)->filter(function($photo) use ($key) {
                                                            if ($key === 'DITERIMA') return str_contains($photo->step, 'RECEIVING'); // Match RECEIVING photos
                                                            if ($key === 'ASSESSMENT') return str_contains($photo->step, 'ASSESSMENT');
                                                            if ($key === 'PREPARATION') return str_contains($photo->step, 'PREP') || str_contains($photo->step, 'UPSELL');
                                                            if ($key === 'SORTIR') return str_contains($photo->step, 'SORTIR');
                                                            // STRICT: Production photos must contain PROD but NOT QC
                                                            if ($key === 'PRODUCTION') return str_contains($photo->step, 'PROD') && !str_contains($photo->step, 'QC');
                                                            if ($key === 'QC') return str_contains($photo->step, 'QC');
                                                            if ($key === 'SELESAI') return str_contains($photo->step, 'FINISH');
                                                            return false;
                                                        });
                                                    @endphp

                                                    @if($stepPhotos->count() > 0)
                                                        <div class="mt-4 grid grid-cols-2 md:grid-cols-3 gap-2 md:gap-3">
                                                            @foreach($stepPhotos as $photo)
                                                                <div class="relative group cursor-pointer overflow-hidden rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-all" onclick="window.open('{{ Storage::url($photo->file_path) }}', '_blank')">
                                                                    <img src="{{ Storage::url($photo->file_path) }}" class="w-full h-20 md:h-24 object-cover" alt="{{ $photo->step }}">
                                                                    <div class="absolute inset-x-0 bottom-0 bg-black/60 p-1.5 backdrop-blur-[2px]">
                                                                         <span class="text-white text-[9px] font-bold uppercase tracking-wider block truncate text-center">
                                                                             {{-- Clean up label: Remove prefixes like PROD_ QC_ etc --}}
                                                                             {{ str_replace('_', ' ', str_replace([$key.'_', 'PROD_', 'QC_', 'BEFORE', 'AFTER'], ['', '', '', 'Awal', 'Akhir'], $photo->step)) }}
                                                                         </span>
                                                                    </div>
                                                                    
                                                                    {{-- Badge Before/After --}}
                                                                    @if(str_contains($photo->step, 'BEFORE'))
                                                                        <span class="absolute top-1 left-1 bg-gray-800/80 text-white text-[8px] font-bold px-1.5 py-0.5 rounded">BEFORE</span>
                                                                    @elseif(str_contains($photo->step, 'AFTER'))
                                                                        <span class="absolute top-1 right-1 bg-green-500/90 text-white text-[8px] font-bold px-1.5 py-0.5 rounded">AFTER</span>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
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
                                        <a href="https://wa.me/6288211288331?text=Halo%20Admin,%20saya%20mau%20tanya%20tentang%20pengambilan%20sepatu%20{{ $order->spk_number }}" target="_blank" class="w-full md:w-auto inline-flex items-center justify-center gap-2 bg-white text-green-700 border-2 border-green-600 px-6 py-3 rounded-xl font-bold hover:bg-green-50 transition-colors shadow-sm">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                            Hubungi Admin
                                        </a>
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
</body>
</html>
