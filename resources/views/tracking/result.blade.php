<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Sepatu - {{ $order->spk_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body class="p-4 md:p-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-6">
            <a href="{{ route('tracking.index') }}" class="inline-block mb-4 text-white hover:text-white/80">
                ← Kembali
            </a>
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Status Sepatu Anda</h1>
            <p class="text-white/80">{{ $order->spk_number }}</p>
        </div>

        <!-- Customer Info Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-6 md:p-8 mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">📋 Informasi Order</h2>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Nama Customer</p>
                    <p class="font-semibold text-lg">{{ $order->customer_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">No. Telepon</p>
                    <p class="font-semibold text-lg">{{ $order->customer_phone }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Sepatu</p>
                    <p class="font-semibold text-lg">{{ $order->shoe_brand }} - {{ $order->shoe_color }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Estimasi Selesai</p>
                    <p class="font-semibold text-lg">{{ $order->estimation_date->format('d M Y') }}</p>
                </div>
            </div>

            @if($order->services->count() > 0)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-600 mb-2">Jasa yang Dipilih:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($order->services as $service)
                            <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-medium">
                                {{ $service->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Status Timeline -->
        <div class="bg-white rounded-2xl shadow-2xl p-6 md:p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">📍 Status Tracking</h2>
            
            @php
                $statuses = [
                    'DITERIMA' => ['label' => 'Diterima Gudang', 'icon' => '📦', 'color' => 'blue'],
                    'ASSESSMENT' => ['label' => 'Assessment Workshop', 'icon' => '🔍', 'color' => 'yellow'],
                    'PREPARATION' => ['label' => 'Preparation', 'icon' => '🧼', 'color' => 'cyan'],
                    'SORTIR' => ['label' => 'Sortir & Material', 'icon' => '📋', 'color' => 'indigo'],
                    'PRODUCTION' => ['label' => 'Production', 'icon' => '🔨', 'color' => 'orange'],
                    'QC' => ['label' => 'Quality Control', 'icon' => '✅', 'color' => 'teal'],
                    'SELESAI' => ['label' => 'Selesai', 'icon' => '🎉', 'color' => 'green'],
                ];

                $currentStatus = $order->status;
                $statusKeys = array_keys($statuses);
                $currentIndex = array_search($currentStatus, $statusKeys);
            @endphp

            <div class="space-y-4">
                @foreach($statuses as $key => $status)
                    @php
                        $index = array_search($key, $statusKeys);
                        $isCompleted = $index <= $currentIndex;
                        $isCurrent = $key === $currentStatus;
                    @endphp
                    
                    <div class="flex items-start gap-4">
                        <!-- Icon -->
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-2xl
                                {{ $isCompleted ? 'bg-'.$status['color'].'-500 ring-4 ring-'.$status['color'].'-200' : 'bg-gray-200' }}
                                {{ $isCurrent ? 'animate-pulse' : '' }}">
                                {{ $status['icon'] }}
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 pb-8 {{ !$loop->last ? 'border-l-2 border-gray-300 ml-6 pl-6' : '' }}">
                            <h3 class="font-bold text-lg {{ $isCompleted ? 'text-gray-900' : 'text-gray-400' }}">
                                {{ $status['label'] }}
                                @if($isCurrent)
                                    <span class="ml-2 px-2 py-1 bg-{{ $status['color'] }}-100 text-{{ $status['color'] }}-800 text-xs rounded-full">
                                        Sedang Proses
                                    </span>
                                @elseif($isCompleted)
                                    <span class="ml-2 text-green-600 text-sm">✓</span>
                                @endif
                            </h3>
                            
                            @if($isCompleted)
                                @php
                                    $stepLogs = $order->logs->where('step', $key)->sortBy('created_at');
                                    $movedLog = $stepLogs->where('action', 'MOVED')->first();
                                @endphp
                                
                                @if($movedLog)
                                    <p class="text-sm text-gray-600 mt-1">
                                        <span class="font-medium">Dimulai:</span> {{ $movedLog->created_at->format('d M Y, H:i') }}
                                    </p>
                                @endif

                                {{-- Show detailed activities for each step --}}
                                @if($stepLogs->count() > 0)
                                    <div class="mt-3 space-y-2">
                                        @foreach($stepLogs as $log)
                                            @if($log->action !== 'MOVED')
                                                <div class="bg-gray-50 rounded-lg p-3 text-sm">
                                                    <div class="flex items-start gap-2">
                                                        <span class="text-green-500 mt-0.5">✓</span>
                                                        <div class="flex-1">
                                                            <p class="text-gray-800 font-medium">
                                                                @if(str_contains($log->action, 'PREP_'))
                                                                    @if($log->action === 'PREP_CLEANING_DONE') Pembersihan Selesai
                                                                    @elseif($log->action === 'PREP_SOL_DONE') Perbaikan Sol Selesai
                                                                    @elseif($log->action === 'PREP_UPPER_DONE') Perbaikan Upper Selesai
                                                                    @else {{ str_replace('_', ' ', $log->action) }}
                                                                    @endif
                                                                @elseif(str_contains($log->action, 'QC_'))
                                                                    @if($log->action === 'QC_JAHIT_DONE') QC Jahitan ✓
                                                                    @elseif($log->action === 'QC_CLEANUP_DONE') QC Kebersihan ✓
                                                                    @elseif($log->action === 'QC_FINAL_DONE') QC Final Check ✓
                                                                    @else {{ str_replace('_', ' ', $log->action) }}
                                                                    @endif
                                                                @elseif($log->action === 'STARTED')
                                                                    Pengerjaan Dimulai
                                                                @else
                                                                    {{ $log->description ?? str_replace('_', ' ', $log->action) }}
                                                                @endif
                                                            </p>
                                                            @if($log->description && $log->action !== 'MOVED')
                                                                <p class="text-gray-600 text-xs mt-1">{{ $log->description }}</p>
                                                            @endif
                                                            <p class="text-gray-500 text-xs mt-1">
                                                                {{ $log->created_at->format('H:i') }}
                                                                @if($log->user)
                                                                    • oleh {{ $log->user->name }}
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Show materials used in Sortir step --}}
                                @if($key === 'SORTIR' && $order->materials->count() > 0)
                                    <div class="mt-3 bg-blue-50 rounded-lg p-3">
                                        <p class="text-sm font-medium text-blue-900 mb-2">Material yang Digunakan:</p>
                                        <div class="space-y-1">
                                            @foreach($order->materials as $material)
                                                <div class="flex justify-between text-sm">
                                                    <span class="text-blue-800">• {{ $material->name }}</span>
                                                    <span class="text-blue-600 font-medium">{{ $material->pivot->quantity }} {{ $material->unit }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if($order->status === 'SELESAI')
                <div class="mt-8 p-6 bg-green-50 border-2 border-green-500 rounded-xl text-center">
                    <p class="text-2xl font-bold text-green-800 mb-2">🎉 Sepatu Anda Sudah Selesai!</p>
                    <p class="text-green-700">Silakan ambil di workshop kami</p>
                </div>
            @endif
        </div>

        <!-- Back Button -->
        <div class="mt-6 text-center">
            <a href="{{ route('tracking.index') }}" 
               class="inline-block bg-white text-purple-600 font-bold py-3 px-8 rounded-lg hover:bg-gray-100 transition-all duration-200 shadow-lg">
                🔍 Lacak Sepatu Lain
            </a>
        </div>
    </div>
</body>
</html>
