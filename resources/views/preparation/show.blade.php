<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm shadow-sm border border-white/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
            </div>
            
            <div class="flex flex-col">
                <h2 class="font-bold text-xl leading-tight tracking-wide">
                    {{ __('Preparation Details') }}
                </h2>
                <div class="text-xs font-medium opacity-90 flex items-center gap-2">
                    <span class="bg-white/20 px-2 py-0.5 rounded text-white font-mono">
                        {{ $order->spk_number }}
                    </span>
                    <span>{{ $order->customer_name }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50/50">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column: Order Info -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="dashboard-card overflow-hidden">
                        <div class="dashboard-card-header">
                            <h3 class="dashboard-card-title text-base">
                                👟 Info Sepatu
                            </h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="text-xs uppercase text-gray-500 font-bold tracking-wider mb-1 block">Brand & Artikel</label>
                                <div class="font-bold text-gray-800 text-lg">{{ $order->shoe_brand }}</div>
                                <div class="text-sm text-gray-600">{{ $order->shoe_color }} • {{ $order->shoe_size }}</div>
                            </div>
                            
                            <div class="pt-4 border-t border-gray-100">
                                <label class="text-xs uppercase text-gray-500 font-bold tracking-wider mb-2 block">Layanan (Services)</label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($order->services as $s)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800 border border-teal-200">
                                            {{ $s->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <div class="pt-4 border-t border-gray-100">
                                <div class="bg-orange-50 border border-orange-100 rounded-lg p-3">
                                    <label class="text-xs uppercase text-orange-800 font-bold tracking-wider mb-1 block flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        Catatan Assessment
                                    </label>
                                    <p class="text-sm text-gray-700 italic">"{{ $order->notes ?? 'Tidak ada catatan khusus.' }}"</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-card overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-sm font-medium text-gray-500">Progress</span>
                                <span class="text-sm font-bold text-teal-600">
                                    {{ ($canFinish ? '100%' : 'In Progress') }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-teal-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $canFinish ? '100%' : '50%' }}"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Sub-Tasks -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="dashboard-card overflow-hidden">
                        <div class="dashboard-card-header flex justify-between items-center">
                            <h3 class="dashboard-card-title">
                                ✅ Sub-Task Checklist
                            </h3>
                            <span class="text-xs text-gray-400 font-mono">
                                Task ID: #PREP-{{ $order->id }}
                            </span>
                        </div>

                        <div class="divide-y divide-gray-100">
                            <!-- Task Item Template (Repeatable) -->
                            
                            <!-- 1. Cuci -->
                            <div class="p-6 hover:bg-gray-50 transition-colors">
                                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0 mt-1">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm {{ $status['cleaning']['done'] ? 'bg-green-100 text-green-600' : 'bg-gray-200 text-gray-600' }}">
                                                1
                                            </div>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-900 text-lg">P. Cuci (Washing)</h4>
                                            <p class="text-sm text-gray-500 mb-2">Pembersihan awal sebelum tindakan reparasi.</p>
                                            
                                            @if($status['cleaning']['done'] === true)
                                                <div class="inline-flex flex-col gap-1 mt-2">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                        Selesai · {{ $status['cleaning']['duration'] }} min
                                                    </span>
                                                    <span class="text-xs text-gray-400">
                                                        Start: {{ $status['cleaning']['start']->format('H:i') }} | End: {{ $status['cleaning']['end']->format('H:i') }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    @if($status['cleaning']['done'] !== true)
                                    <div class="w-full sm:w-64 bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                        <form action="{{ route('preparation.update', $order->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="type" value="cleaning">
                                            <div class="mb-3">
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Pilih Teknisi Washing</label>
                                                <select name="worker_id" class="w-full text-sm border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500" required>
                                                    <option value="">-- Select Worker --</option>
                                                    @foreach($techWashing as $tech)
                                                        <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-xs text-gray-400">
                                                    In: {{ $status['cleaning']['start'] ? $status['cleaning']['start']->format('H:i') : '-' }}
                                                </span>
                                                <button class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-1.5 rounded text-xs font-bold shadow-sm transition-colors uppercase tracking-wide">
                                                    Mark Done
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    @endif
                                </div>
                                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                                        <span class="text-xs font-bold text-gray-500 uppercase block mb-2">Kondisi Awal (Before)</span>
                                        <x-photo-uploader :order="$order" step="PREP_WASHING_BEFORE" />
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                                        <span class="text-xs font-bold text-gray-500 uppercase block mb-2">Hasil Akhir (After)</span>
                                        <x-photo-uploader :order="$order" step="PREP_WASHING_AFTER" />
                                    </div>
                                </div>
                            </div>

                            <!-- 2. Sol -->
                            <div class="p-6 hover:bg-gray-50 transition-colors">
                                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0 mt-1">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm {{ ($status['sol'] !== 'SKIP' && $status['sol']['done']) ? 'bg-green-100 text-green-600' : ($status['sol'] === 'SKIP' ? 'bg-gray-100 text-gray-400' : 'bg-gray-200 text-gray-600') }}">
                                                2
                                            </div>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-900 text-lg flex items-center gap-2">
                                                P. Reparasi Sol
                                                @if($status['sol'] === 'SKIP')
                                                    <span class="bg-gray-100 text-gray-500 text-[10px] px-2 py-0.5 rounded border border-gray-200">NOT REQUIRED</span>
                                                @endif
                                            </h4>
                                            <p class="text-sm text-gray-500 mb-2">Bongkar sol lama & bersihkan sisa lem.</p>

                                            @if($status['sol'] !== 'SKIP' && $status['sol']['done'] === true)
                                                <div class="inline-flex flex-col gap-1 mt-2">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                        Selesai · {{ $status['sol']['duration'] }} min
                                                    </span>
                                                    <span class="text-xs text-gray-400">
                                                        Start: {{ $status['sol']['start']->format('H:i') }} | End: {{ $status['sol']['end']->format('H:i') }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    @if($status['sol'] !== 'SKIP' && $status['sol']['done'] !== true)
                                    <div class="w-full sm:w-64 bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                        <form action="{{ route('preparation.update', $order->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="type" value="sol">
                                            <div class="mb-3">
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Pilih Teknisi Sol</label>
                                                <select name="worker_id" class="w-full text-sm border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500" required>
                                                    <option value="">-- Select Worker --</option>
                                                    @foreach($techSol as $tech)
                                                        <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-xs text-gray-400">
                                                    In: {{ $status['sol']['start']->format('H:i') }}
                                                </span>
                                                <button class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-1.5 rounded text-xs font-bold shadow-sm transition-colors uppercase tracking-wide">
                                                    Mark Done
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    @endif
                                </div>
                                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                     <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                                        <span class="text-xs font-bold text-gray-500 uppercase block mb-2">Kondisi Awal (Before)</span>
                                        <x-photo-uploader :order="$order" step="PREP_SOL_BEFORE" />
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                                        <span class="text-xs font-bold text-gray-500 uppercase block mb-2">Hasil Akhir (After)</span>
                                        <x-photo-uploader :order="$order" step="PREP_SOL_AFTER" />
                                    </div>
                                </div>
                            </div>

                             <!-- 3. Upper -->
                             <div class="p-6 hover:bg-gray-50 transition-colors">
                                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0 mt-1">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm {{ ($status['upper'] !== 'SKIP' && $status['upper']['done']) ? 'bg-green-100 text-green-600' : ($status['upper'] === 'SKIP' ? 'bg-gray-100 text-gray-400' : 'bg-gray-200 text-gray-600') }}">
                                                3
                                            </div>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-900 text-lg flex items-center gap-2">
                                                P. Reparasi Upper
                                                @if($status['upper'] === 'SKIP')
                                                    <span class="bg-gray-100 text-gray-500 text-[10px] px-2 py-0.5 rounded border border-gray-200">NOT REQUIRED</span>
                                                @endif
                                            </h4>
                                            <p class="text-sm text-gray-500 mb-2">Acetone, amplas, & proses masking.</p>

                                            @if($status['upper'] !== 'SKIP' && $status['upper']['done'] === true)
                                                <div class="inline-flex flex-col gap-1 mt-2">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                        Selesai · {{ $status['upper']['duration'] }} min
                                                    </span>
                                                    <span class="text-xs text-gray-400">
                                                        Start: {{ $status['upper']['start']->format('H:i') }} | End: {{ $status['upper']['end']->format('H:i') }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    @if($status['upper'] !== 'SKIP' && $status['upper']['done'] !== true)
                                    <div class="w-full sm:w-64 bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                        <form action="{{ route('preparation.update', $order->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="type" value="upper">
                                            <div class="mb-3">
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Pilih Teknisi Upper</label>
                                                <select name="worker_id" class="w-full text-sm border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500" required>
                                                    <option value="">-- Select Worker --</option>
                                                    @foreach($techUpper as $tech)
                                                        <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-xs text-gray-400">
                                                    In: {{ $status['upper']['start']->format('H:i') }}
                                                </span>
                                                <button class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-1.5 rounded text-xs font-bold shadow-sm transition-colors uppercase tracking-wide">
                                                    Mark Done
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    @endif
                                </div>
                                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                     <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                                        <span class="text-xs font-bold text-gray-500 uppercase block mb-2">Kondisi Awal (Before)</span>
                                        <x-photo-uploader :order="$order" step="PREP_UPPER_BEFORE" />
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                                        <span class="text-xs font-bold text-gray-500 uppercase block mb-2">Hasil Akhir (After)</span>
                                        <x-photo-uploader :order="$order" step="PREP_UPPER_AFTER" />
                                    </div>
                                </div>
                            </div>
                        </div>

                         <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                             <a href="{{ route('preparation.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-800 transition-colors">
                                 &larr; Kembali
                             </a>
                             @if($canFinish)
                                 <form action="{{ route('preparation.finish', $order->id) }}" method="POST">
                                     @csrf
                                     <button class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold rounded-lg shadow-lg transform hover:-translate-y-0.5 transition-all text-sm uppercase tracking-wider">
                                         <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                         Preparation Selesai
                                     </button>
                                 </form>
                             @else
                                 <button disabled class="inline-flex items-center px-6 py-3 bg-gray-300 text-gray-500 font-bold rounded-lg cursor-not-allowed shadow-none">
                                     Lengkapi Semua Task
                                 </button>
                             @endif
                         </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
