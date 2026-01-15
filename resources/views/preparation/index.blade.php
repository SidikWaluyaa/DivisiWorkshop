<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm shadow-sm border border-white/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </div>
                
                <div class="flex flex-col">
                    <h2 class="font-bold text-xl leading-tight tracking-wide">
                        {{ __('Stasiun Persiapan') }}
                    </h2>
                    <div class="text-xs font-medium opacity-90">
                        Proses Cuci, Bongkar Sol, dan Bongkar Upper
                    </div>
                </div>
            </div>

            {{-- Search Form --}}
            <form method="GET" action="{{ route('preparation.index') }}" class="relative">
                {{-- Keep current tab in search --}}
                <input type="hidden" name="tab" value="{{ request('tab', 'washing') }}">
                
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Cari SPK / Customer..." 
                       style="color: #000000 !important; background-color: #ffffff !important;"
                       class="pl-9 pr-4 py-1.5 text-sm !text-gray-900 !bg-white border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 shadow-sm w-48 transition-all focus:w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </form>
        </div>
    </x-slot>

    <div class="py-6 bg-gray-50" x-data="{ activeTab: '{{ $activeTab }}' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Stats Overview --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                {{-- Washing Stat --}}
                <a href="{{ route('preparation.index', ['tab' => 'washing']) }}"
                     class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 cursor-pointer transition-all hover:shadow-md block"
                     :class="{ 'ring-2 ring-teal-500 bg-teal-50': '{{ $activeTab }}' === 'washing' }">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="text-xs font-bold text-gray-500 uppercase">Antrian Proses Cuci</div>
                            <div class="text-2xl font-black text-gray-800">{{ $counts['washing'] }}</div>
                        </div>
                        <span class="text-2xl">🧼</span>
                    </div>
                </a>

                {{-- Sol Stat --}}
                <a href="{{ route('preparation.index', ['tab' => 'sol']) }}" 
                     class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 cursor-pointer transition-all hover:shadow-md block"
                     :class="{ 'ring-2 ring-orange-500 bg-orange-50': '{{ $activeTab }}' === 'sol' }">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="text-xs font-bold text-gray-500 uppercase">Antrian Bongkar Sol</div>
                            <div class="text-2xl font-black text-gray-800">{{ $counts['sol'] }}</div>
                        </div>
                        <span class="text-2xl">👟</span>
                    </div>
                </a>

                {{-- Upper Stat --}}
                <a href="{{ route('preparation.index', ['tab' => 'upper']) }}" 
                     class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 cursor-pointer transition-all hover:shadow-md block"
                     :class="{ 'ring-2 ring-purple-500 bg-purple-50': '{{ $activeTab }}' === 'upper' }">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="text-xs font-bold text-gray-500 uppercase">Antrian Bongkar Upper</div>
                            <div class="text-2xl font-black text-gray-800">{{ $counts['upper'] }}</div>
                        </div>
                        <span class="text-2xl">🎨</span>
                    </div>
                </a>

                {{-- Final Check --}}
                <a href="{{ route('preparation.index', ['tab' => 'review']) }}" 
                     class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 cursor-pointer transition-all hover:shadow-md block"
                     :class="{ 'ring-2 ring-blue-500 bg-blue-50': '{{ $activeTab }}' === 'review' }">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="text-xs font-bold text-gray-500 uppercase">Review Admin</div>
                            <div class="text-2xl font-black text-gray-800">{{ $counts['review'] }}</div>
                        </div>
                        <span class="text-2xl">📋</span>
                    </div>
                </a>
            </div>

            {{-- Tab Content --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden min-h-[500px]">
                
                {{-- Washing Station --}}
                @if($activeTab === 'washing')
                <div>
                    <div class="p-4 border-b border-gray-100 bg-teal-50 flex justify-between items-center">
                        <h3 class="font-bold text-teal-800 flex items-center gap-2">
                            <span>🧼 Station Washing & Cleaning</span>
                            <span class="px-2 py-0.5 bg-white rounded-full text-xs border border-teal-200">{{ $orders->total() }} items</span>
                        </h3>
                    </div>
                    @if($orders->count() > 0)
                        <div class="divide-y divide-gray-100">
                            @foreach($orders as $order)
                                <div x-data="{ showPhotos: false, showFinishModal: false, finishDate: '{{ now()->format('Y-m-d\TH:i') }}' }" class="border-b border-gray-100 last:border-0 group">
                                    <div class="p-4 hover:bg-gray-50 transition-colors flex items-start justify-between">
                                        <div class="flex gap-4 items-start">
                                             <div class="flex flex-col items-center gap-2 pt-1">
                                                <input type="checkbox" value="{{ $order->id }}" 
                                                       @change="$store.preparation.toggle('{{ $order->id }}')" 
                                                       :checked="$store.preparation.includes('{{ $order->id }}')"
                                                       class="w-5 h-5 text-teal-600 rounded border-gray-300 focus:ring-teal-500 cursor-pointer shadow-sm">
                                                <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-bold text-xs border border-gray-300">
                                                    {{ ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration }}
                                                </div>
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2 mb-1">
                                                    <div class="font-mono font-bold text-gray-600 bg-gray-100 px-2 py-1 rounded text-sm">{{ $order->spk_number }}</div>
                                                    @if(in_array($order->priority, ['Prioritas', 'Urgent', 'Express']))
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700 border border-red-200 shadow-sm uppercase tracking-wider">
                                                            Prioritas
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="font-bold text-gray-800">{{ $order->customer_name }}</div>
                                                <div class="text-xs text-gray-500">{{ $order->shoe_brand }} {{ $order->shoe_type }} - {{ $order->shoe_color }}</div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-4">
                                            {{-- Services Tag --}}
                                            <div class="text-right hidden sm:block">
                                                @foreach($order->services as $s)
                                                    <span class="text-[10px] uppercase bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded border border-gray-200">{{ $s->name }}</span>
                                                @endforeach
                                            </div>

                                            {{-- Photo Toggle Button --}}
                                            <button @click="showPhotos = !showPhotos" :class="showPhotos ? 'text-teal-600 bg-teal-50' : 'text-gray-400 hover:text-teal-600'" class="p-2 rounded-lg transition-colors" title="Dokumentasi Foto">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            </button>

                                            {{-- Quick Action Button --}}
                                            @if(!$order->prep_washing_by)
                                                <div class="flex items-center gap-2">
                                                    <select id="tech-washing-{{ $order->id }}" class="text-xs border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                                                        <option value="">-- Pilih Teknisi --</option>
                                                        @foreach($techWashing as $t)
                                                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <button type="button" onclick="updateStation({{ $order->id }}, 'washing', 'start')" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs font-bold uppercase tracking-wide transition-all shadow hover:shadow-md">
                                                        Assign
                                                    </button>
                                                </div>
                                            @elseif($order->prep_washing_by)
                                                <div class="flex flex-col items-end gap-1">
                                                    <div class="text-right">
                                                        <span class="text-[10px] text-gray-400 block">Dikerjakan oleh:</span>
                                                        <span class="font-bold text-xs text-teal-600 bg-teal-50 px-2 py-0.5 rounded border border-teal-100">{{ $order->prepWashingBy->name ?? '...' }}</span>
                                                        @if($order->prep_washing_started_at)
                                                            <span class="text-[10px] text-gray-500 block mt-0.5">Mulai: {{ $order->prep_washing_started_at->format('H:i') }}</span>
                                                        @endif
                                                    </div>
                                                    <button type="button" @click="showFinishModal = true" class="flex items-center gap-2 px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white rounded text-xs font-bold uppercase tracking-wide transition-all shadow hover:shadow-md">
                                                        <span>✔ Selesai</span>
                                                    </button>
                                                    <div x-show="showFinishModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display: none;" x-transition>
                                                        <div class="bg-white rounded-lg shadow-xl p-4 w-80" @click.away="showFinishModal = false">
                                                            <h3 class="font-bold text-gray-800 mb-2">Konfirmasi Selesai</h3>
                                                            <p class="text-xs text-gray-600 mb-3">Masukkan tanggal & jam selesai aktual:</p>
                                                            <input type="datetime-local" x-model="finishDate" class="w-full text-sm border-gray-300 rounded mb-4 focus:ring-green-500 focus:border-green-500">
                                                            <div class="flex justify-end gap-2">
                                                                <button @click="showFinishModal = false" class="px-3 py-1.5 bg-gray-200 text-gray-700 rounded text-xs font-bold">Batal</button>
                                                                <button @click="updateStation({{ $order->id }}, 'washing', 'finish', finishDate)" class="px-3 py-1.5 bg-green-600 text-white rounded text-xs font-bold">Simpan & Selesai</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Photo Section -->
                                    <div x-show="showPhotos" class="px-4 pb-4 bg-gray-50/50" style="display: none;" x-transition>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-2">Before (Awal)</span>
                                                <x-photo-uploader :order="$order" step="PREP_WASHING_BEFORE" />
                                            </div>
                                            <div>
                                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-2">After (Akhir)</span>
                                                <x-photo-uploader :order="$order" step="PREP_WASHING_AFTER" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-12 text-center text-gray-400">
                            <span class="text-4xl block mb-2">✨</span>
                            <p>Tidak ada antrian cuci.</p>
                        </div>
                    @endif
                </div>
                @endif

                {{-- Sol Station --}}
                @if($activeTab === 'sol')
                <div>
                    <div class="p-4 border-b border-gray-100 bg-orange-50 flex justify-between items-center">
                        <h3 class="font-bold text-orange-800 flex items-center gap-2">
                            <span>👟 Station Bongkar Sol</span>
                            <span class="px-2 py-0.5 bg-white rounded-full text-xs border border-orange-200">{{ $orders->total() }} item</span>
                        </h3>
                    </div>
                     @if($orders->count() > 0)
                        <div class="divide-y divide-gray-100">
                            @foreach($orders as $order)
                                @include('preparation.partials.station-card', [
                                    'order' => $order,
                                    'type' => 'sol',
                                    'technicians' => $techSol,
                                    'techByRelation' => 'prepSolBy',
                                    'startedAtColumn' => 'prep_sol_started_at',
                                    'byColumn' => 'prep_sol_by',
                                    'showCheckbox' => true,
                                    'loopIteration' => ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration
                                ])
                            @endforeach
                        </div>
                    @else
                        <div class="p-12 text-center text-gray-400">
                            <span class="text-4xl block mb-2">✅</span>
                            <p>Antrian Bongkar Sol kosong.</p>
                        </div>
                    @endif
                </div>
                @endif

                {{-- Upper Station --}}
                @if($activeTab === 'upper')
                <div>
                    <div class="p-4 border-b border-gray-100 bg-purple-50 flex justify-between items-center">
                        <h3 class="font-bold text-purple-800 flex items-center gap-2">
                            <span>🎨 Station Bongkar Upper & Repaint</span>
                            <span class="px-2 py-0.5 bg-white rounded-full text-xs border border-purple-200">{{ $orders->total() }} items</span>
                        </h3>
                    </div>
                    @if($orders->count() > 0)
                        <div class="divide-y divide-gray-100">
                            @foreach($orders as $order)
                                @include('preparation.partials.station-card', [
                                    'order' => $order,
                                    'type' => 'upper',
                                    'technicians' => $techUpper,
                                    'techByRelation' => 'prepUpperBy',
                                    'startedAtColumn' => 'prep_upper_started_at',
                                    'byColumn' => 'prep_upper_by',
                                    'showCheckbox' => true,
                                    'loopIteration' => ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration
                                ])
                            @endforeach
                        </div>
                    @else
                        <div class="p-12 text-center text-gray-400">
                            <span class="text-4xl block mb-2">✅</span>
                            <p>Antrian Bongkar Upper kosong.</p>
                        </div>
                    @endif
                </div>
                @endif

                {{-- ADMIN REVIEW --}}
                @if($activeTab === 'review')
                <div class="mb-6 bg-white overflow-hidden">
                     <div class="bg-gradient-to-r from-orange-500 to-red-500 p-4 text-white flex justify-between items-center">
                        <h3 class="font-bold flex items-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Menunggu Pemeriksaan Admin (Preparation Selesai)
                        </h3>
                        <span class="bg-white/20 px-3 py-1 rounded-full text-xs font-bold">{{ $orders->total() }} Order</span>
                    </div>
                    
                    @if($orders->count() > 0)
                    <div class="overflow-x-auto -mx-4 sm:mx-0">
                        <table class="min-w-full w-full text-sm text-left">
                            <thead class="bg-gray-50 uppercase text-xs font-bold text-gray-600">
                                <tr>
                                    <th class="px-4 py-3">
                                        <input type="checkbox" @click="toggleAll($event)" class="rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                    </th>
                                    <th class="px-4 py-3">No</th>
                                    <th class="px-4 py-3">SPK</th>
                                    <th class="px-6 py-3">SPK</th>
                                    <th class="px-6 py-3">Item</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3 text-center">Aksi (Admin)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($orders as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <input type="checkbox" value="{{ $order->id }}" 
                                               @change="$store.preparation.toggle('{{ $order->id }}')"
                                               :checked="$store.preparation.includes('{{ $order->id }}')"
                                               class="rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                    </td>
                                    <td class="px-6 py-4 font-bold text-gray-500">{{ ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration }}</td>
                                    <td class="px-6 py-4 font-bold font-mono">{{ $order->spk_number }}</td>
                                    <td class="px-6 py-4 text-center">
                                        @if(in_array($order->priority, ['Prioritas', 'Urgent', 'Express']))
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200 shadow-sm">
                                                PRIORITAS
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                                REGULER
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold">{{ $order->shoe_brand }}</div>
                                        <div class="text-xs text-gray-500">{{ $order->customer_name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-2 text-xs">
                                            @if($order->prep_washing_completed_at)
                                                <div class="flex items-start gap-2">
                                                    <span class="text-green-600 font-bold min-w-[50px]">✔ Wash:</span>
                                                    <div>
                                                        <div class="font-medium text-gray-700">{{ $order->prepWashingBy->name ?? 'System' }}</div>
                                                        @if($order->prep_washing_started_at)
                                                             <div class="text-[10px] text-gray-500">
                                                                {{ $order->prep_washing_started_at->format('H:i') }} - {{ $order->prep_washing_completed_at->format('H:i') }} 
                                                                <span class="font-bold text-teal-600">({{ $order->prep_washing_started_at->diffInMinutes($order->prep_washing_completed_at) }} mnt)</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if($order->needs_sol)
                                                <div class="flex items-start gap-2">
                                                    <span class="text-green-600 font-bold min-w-[50px]">✔ Sol:</span>
                                                    @if($order->prep_sol_completed_at)
                                                        <div>
                                                            <div class="font-medium text-gray-700">{{ $order->prepSolBy->name ?? 'System' }}</div>
                                                            @if($order->prep_sol_started_at)
                                                                <div class="text-[10px] text-gray-500">
                                                                    {{ $order->prep_sol_started_at->format('H:i') }} - {{ $order->prep_sol_completed_at->format('H:i') }} 
                                                                    <span class="font-bold text-teal-600">({{ $order->prep_sol_started_at->diffInMinutes($order->prep_sol_completed_at) }} mnt)</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="text-gray-400 italic"> - </span>
                                                    @endif
                                                </div>
                                            @endif
                                            
                                            @if($order->needs_upper)
                                                <div class="flex items-start gap-2">
                                                    <span class="text-green-600 font-bold min-w-[50px]">✔ Upper:</span>
                                                    @if($order->prep_upper_completed_at)
                                                        <div>
                                                            <div class="font-medium text-gray-700">{{ $order->prepUpperBy->name ?? 'System' }}</div>
                                                            @if($order->prep_upper_started_at)
                                                                <div class="text-[10px] text-gray-500">
                                                                    {{ $order->prep_upper_started_at->format('H:i') }} - {{ $order->prep_upper_completed_at->format('H:i') }} 
                                                                    <span class="font-bold text-teal-600">({{ $order->prep_upper_started_at->diffInMinutes($order->prep_upper_completed_at) }} mnt)</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="text-gray-400 italic"> - </span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center items-center gap-2">
                                            <!-- Approve -->
                                            <form id="approve-form-{{ $order->id }}" action="{{ route('preparation.approve', $order->id) }}" method="POST">
                                                @csrf
                                                <button type="button" onclick="confirmApprove({{ $order->id }})" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-bold text-xs flex items-center gap-1 shadow hover:shadow-lg transition-all">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    Approve & Sortir
                                                </button>
                                            </form>
                                            
                                            <!-- Reject Modal Trigger -->
                                            <div x-data="{ openRevisi: false }">
                                                <button @click="openRevisi = true" type="button" class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-2 rounded-lg font-bold text-xs flex items-center gap-1 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    Revisi...
                                                </button>

                                                <!-- Modal -->
                                                <div x-show="openRevisi" class="fixed inset-0 z-[999] flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display: none;" x-transition>
                                                    <div class="bg-white rounded-xl shadow-2xl p-6 w-72 max-w-full text-left" @click.away="openRevisi = false">
                                                        <h3 class="font-bold text-lg text-gray-800 mb-4 border-b pb-2">Revisi Preparation</h3>
                                                        <p class="text-xs text-gray-500 mb-3">Pilih bagian yang perlu direvisi:</p>

                                                        <form action="{{ route('preparation.reject', $order->id) }}" method="POST" class="space-y-3">
                                                            @csrf
                                                            
                                                            <div>
                                                                <label class="flex items-center gap-2 mb-1 cursor-pointer">
                                                                    <input type="radio" name="target_station" value="washing" class="text-red-600" required>
                                                                    <span class="font-bold text-sm text-gray-700">Washing</span>
                                                                </label>
                                                            </div>

                                                            @if($order->needs_sol)
                                                            <div>
                                                                <label class="flex items-center gap-2 mb-1 cursor-pointer">
                                                                    <input type="radio" name="target_station" value="sol" class="text-red-600" required>
                                                                    <span class="font-bold text-sm text-gray-700">Bongkar Sol</span>
                                                                </label>
                                                            </div>
                                                            @endif

                                                            @if($order->needs_upper)
                                                            <div>
                                                                <label class="flex items-center gap-2 mb-1 cursor-pointer">
                                                                    <input type="radio" name="target_station" value="upper" class="text-red-600" required>
                                                                    <span class="font-bold text-sm text-gray-700">Bongkar Upper</span>
                                                                </label>
                                                            </div>
                                                            @endif
                                                            
                                                            <textarea name="reason" rows="2" class="w-full text-sm border-gray-300 rounded focus:border-red-500 focus:ring-red-500" placeholder="Alasan revisi..." required></textarea>

                                                            <div class="flex justify-end gap-2 mt-2">
                                                                <button type="button" @click="openRevisi = false" class="px-3 py-1.5 bg-gray-100 text-gray-600 rounded text-xs font-bold hover:bg-gray-200">Batal</button>
                                                                <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded text-xs font-bold hover:bg-red-700 shadow">Kirim Revisi</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                     @else
                        <div class="p-12 text-center text-gray-400">
                             <p>Tidak ada order yang menunggu review.</p>
                        </div>
                    @endif
                </div>
                @endif
                
                {{-- Pagination Links --}}
                 <div class="p-4 border-t border-gray-100 bg-gray-50">
                    {{ $orders->links() }}
                </div>
            </div>

            {{-- Instructions --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex gap-4 items-start">
                <span class="text-2xl">💡</span>
                <div class="text-sm text-blue-800">
                    <strong>Panduan Stasiun:</strong>
                    <ul class="list-disc ml-4 mt-1 space-y-1">
                        <li>Gunakan tab <strong>Washing</strong>, <strong>Sol</strong>, dan <strong>Upper</strong> untuk melihat antrian spesifik.</li>
                        <li>Klik tombol <strong>"SELESAI"</strong> pada setiap baris untuk menandai bahwa tahapan tersebut sudah beres.</li>
                        <li>Sistem otomatis mencatat nama Anda sebagai teknisi yang mengerjakan.</li>
                        <li>Jika semua tahap selesai, tombol <strong>"Kirim ke Sortir"</strong> akan muncul di tab "Semua Order".</li>
                    </ul>
                </div>
            </div>

        </div>

    {{-- FLOATING BULK ACTION BAR --}}
    <div x-show="$store.preparation.count() > 0" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full opacity-0 scale-95"
         x-transition:enter-end="translate-y-0 opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0 opacity-100 scale-100"
         x-transition:leave-end="translate-y-full opacity-0 scale-95"
         class="fixed bottom-6 inset-x-0 z-50 flex justify-center px-4"
         style="display: none;">
        
        <div class="bg-white/90 backdrop-blur-md border border-gray-200 shadow-2xl rounded-2xl p-4 w-full max-w-4xl flex flex-col md:flex-row items-center justify-between gap-4 ring-1 ring-black/5">
            
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 bg-gray-100 px-3 py-1.5 rounded-lg">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Terpilih</span>
                    <span class="bg-gray-800 text-white px-2 py-0.5 rounded-md font-bold text-sm" x-text="$store.preparation.count()"></span>
                </div>
                <button @click="$store.preparation.clear()" class="text-xs font-bold text-red-500 hover:text-red-700 hover:bg-red-50 px-3 py-1.5 rounded-lg transition-colors">
                    Batal
                </button>
            </div>

            <div class="h-8 w-px bg-gray-200 hidden md:block"></div>

            <div class="flex items-center gap-2 w-full md:w-auto overflow-x-auto pb-2 md:pb-0 scrollbar-hide justify-end">
                
                {{-- Assign Tech --}}
                <div class="flex items-center gap-2">
                    <div class="relative group">
                        <select id="bulk-tech-select" class="appearance-none bg-white border border-gray-200 text-gray-700 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-40 pl-3 pr-8 py-2.5 font-bold shadow-sm cursor-pointer hover:border-blue-300 transition-colors">
                            <option value="">-- PILIH TEKNISI --</option>
                            <optgroup label="Washing">
                                @foreach($techWashing as $t) <option value="{{ $t->id }}">Wash: {{ $t->name }}</option> @endforeach
                            </optgroup>
                            <optgroup label="Sol">
                                @foreach($techSol as $t) <option value="{{ $t->id }}">Sol: {{ $t->name }}</option> @endforeach
                            </optgroup>
                            <optgroup label="Upper">
                                @foreach($techUpper as $t) <option value="{{ $t->id }}">Upper: {{ $t->name }}</option> @endforeach
                            </optgroup>
                        </select>
                         <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400 group-hover:text-blue-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>

                    <button type="button" onclick="bulkAction('assign')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-xs font-bold shadow hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center gap-2 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Assign
                    </button>
                </div>

                {{-- Start button removed as per user request --}}

                {{-- Finish --}}
                <button type="button" onclick="bulkAction('finish')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-lg text-xs font-bold shadow hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center gap-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Selesai
                </button>
                
                {{-- Approve (Review Tab) --}}
                <button type="button" onclick="bulkAction('approve')" x-show="activeTab === 'review'" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-lg text-xs font-bold shadow hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center gap-2 active:scale-95" style="display: none;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Approve & Sortir
                </button>
            </div>
        </div>
    </div>
    </div>

    <script>
    function bulkAction(action) {
        // const alpineEl = document.querySelector('[x-data]'); 
        let selectedItems = Alpine.store('preparation').getIds();
        
        /* Fallback if store is empty but checked (edge case) */
        if (selectedItems.length === 0) {
             const checkedInputs = document.querySelectorAll('input[type="checkbox"]:checked');
             // filter potentially unrelated checkboxes if any
             // but here we rely on store
        }

        if (selectedItems.length === 0) {
            Swal.fire({ icon: 'warning', title: 'Pilih item', text: 'Tidak ada order yang dipilih.' });
            return;
        }

        let techId = null;
        if (action === 'assign' || action === 'start') {
            const selectEl = document.getElementById('bulk-tech-select');
            if (selectEl && selectEl.value) {
                techId = selectEl.value;
            } else if (action === 'assign') {
                Swal.fire({ icon: 'warning', title: 'Pilih Teknisi', text: 'Silakan pilih teknisi untuk Assign.' });
                return;
            }
        }

        Swal.fire({
            title: 'Konfirmasi Bulk Action',
            text: `Proses ${selectedItems.length} item dengan aksi: ${action.toUpperCase()}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Lanjutkan!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Determine Type from Active Tab
                // Use server-side activeTab as source of truth
                let activeTab = '{{ $activeTab }}';
                
                console.log('Active Tab (Server):', activeTab);
                
        // Map local tab names to Controller types
                let type = 'washing';
                if (activeTab === 'sol') type = 'sol';
                if (activeTab === 'upper') type = 'upper';

                // Debug
                console.log('Bulk Action Params:', { ids: selectedItems, type, action });
                // alert(`Debug: Sending ${action} for ${type} on ${selectedItems.length} items.`);

                fetch('{{ route('preparation.bulk-update') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        ids: selectedItems,
                        action: action,
                        type: type, 
                        technician_id: techId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Error: ' + (data.message || JSON.stringify(data.errors))
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Server Error',
                        text: 'Terjadi kesalahan pada request.'
                    });
                });
            }
        });
    }

    // Ensure functions are available globally
    window.updateStation = function(id, type, action = 'finish', finishedAt = null) {
        
        let techId = null;
        if (action === 'start') {
            const selectId = `tech-${type}-${id}`;
            const selectEl = document.getElementById(selectId);
            if (!selectEl) {
                console.error("Select Element not found:", selectId);
                alert("Error: Technician select not found for " + selectId);
                return;
            }
            techId = selectEl.value;
            if (!techId) {
                alert('Silakan pilih teknisi terlebih dahulu.');
                return;
            }
        }

        // if (!confirm('Apakah anda yakin ingin ' + (action === 'start' ? 'memulai' : 'menyelesaikan') + ' proses ini?')) return;
        if (action === 'start' && !confirm('Mulai proses ini?')) return;

        console.log('Sending Update (Global):', { id, type, action, finishedAt });

        fetch(`/preparation/${id}/update-station`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ 
                type: type, 
                action: action,
                technician_id: techId,
                finished_at: finishedAt
            })
        })
        .then(async response => {
            const data = await response.json().catch(() => ({})); 
            if (!response.ok) {
                throw new Error(data.message || response.statusText || 'Server Error ' + response.status);
            }
            return data;
        })
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Status berhasil diperbarui.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload(); 
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.message
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan: ' + error.message
            });
        });
    }

    window.confirmApprove = function(id) {
        Swal.fire({
            title: 'Preparation Selesai?',
            text: "Lanjutkan ke proses Sortir?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10B981',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Lanjut Sortir!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('approve-form-' + id).submit();
            }
        });
    }

    function toggleAll(e) {
        const checkboxes = document.querySelectorAll('input[type="checkbox"][x-model="selectedItems"]');
        const alpineEl = document.querySelector('[x-data]');
        let selected = [];
        if (e.target.checked) {
            checkboxes.forEach(cb => {
                 selected.push(cb.value);
            });
        }
        // Alpine.$data(alpineEl).selectedItems = selected;
        // Use store
        Alpine.store('preparation').items = selected;
    }

    document.addEventListener('alpine:init', () => {
        Alpine.store('preparation', {
            items: [],
            
            toggle(id) {
                id = String(id);
                if (this.items.includes(id)) {
                    this.items = this.items.filter(i => i !== id);
                } else {
                    this.items.push(id);
                }
                // Sync for fallback if needed, but primarily usage store
            },

            includes(id) {
                return this.items.includes(String(id));
            },

            count() {
                return this.items.length;
            },

            clear() {
                this.items = [];
            },
            
            getIds() {
                return this.items;
            }
        });
    });
    </script>
</x-app-layout>
