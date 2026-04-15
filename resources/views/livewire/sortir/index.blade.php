<div class="min-h-screen bg-[#f8fafc] pb-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
        
        {{-- Breadcrumbs & Header --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
            <div class="space-y-2">
                <nav class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">
                    <a href="#" class="hover:text-teal-600 transition-colors">Inventory</a>
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                    <span class="text-teal-600">Sortir Queue</span>
                </nav>
                <h1 class="text-4xl font-black text-[#1a3b34] tracking-tight">Sortir Queue</h1>
                <p class="text-sm font-medium text-gray-500 max-w-lg">Manage and monitor live workshop intake and material allocation status.</p>
            </div>

            <div class="flex items-center gap-3">
                <div class="relative group">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search SPK or Customer..." 
                           class="pl-11 pr-5 py-3.5 text-xs border-transparent bg-white rounded-2xl focus:ring-2 focus:ring-teal-500/20 focus:bg-white shadow-sm w-72 transition-all font-bold text-gray-700 placeholder:text-gray-400 ring-1 ring-gray-100">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>

                {{-- Advanced Filters Dropdown --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" 
                            class="flex items-center gap-2 px-5 py-3.5 bg-white border border-gray-100 rounded-2xl shadow-sm text-[10px] font-black uppercase tracking-widest transition-all {{ $filterPriority || $filterBrand || $filterType ? 'text-teal-600 ring-2 ring-teal-500/20' : 'text-gray-600 hover:bg-gray-50' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4.5h18m-18 5h18m-18 5h18m-18 5h18"></path></svg>
                        Filters
                        @if($filterPriority || $filterBrand || $filterType)
                            <span class="w-2 h-2 bg-teal-500 rounded-full"></span>
                        @endif
                    </button>

                    <div x-show="open" x-cloak @click.away="open = false" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         style="width: 420px; min-width: 420px;"
                         class="absolute right-0 mt-3 bg-white rounded-[2.5rem] shadow-2xl border border-gray-100 z-50 p-10 space-y-9 ring-1 ring-black/5">
                        
                        <div class="flex flex-col items-start justify-between border-b border-gray-100 pb-6 gap-5">
                            <div class="space-y-1">
                                <h3 class="text-sm font-black uppercase tracking-[0.25em] text-[#1a3b34]">Advanced Filters</h3>
                                <p class="text-[10px] font-bold text-gray-400">Refine your workshop queue</p>
                            </div>
                            <button wire:click="resetFilters" @click="open = false" class="w-full sm:w-auto px-6 py-2 bg-gray-50 text-[11px] font-black uppercase tracking-widest text-[#22AF85] rounded-2xl hover:bg-[#22AF85] hover:text-white transition-all border border-gray-100 shadow-sm">
                                Reset All Filters
                            </button>
                        </div>

                        {{-- Priority Filter --}}
                        <div class="space-y-5">
                            <div class="flex items-center justify-between">
                                <label class="text-[11px] font-black uppercase tracking-widest text-gray-400 flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-teal-500 shadow-sm shadow-teal-500/40"></div>
                                    Priority Level
                                </label>
                                @if($filterPriority)
                                    <span class="text-[10px] font-black text-teal-600 uppercase bg-teal-50 px-2 py-0.5 rounded-md border border-teal-100">{{ $filterPriority }}</span>
                                @endif
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                @foreach(['' => 'All Status', 'Prioritas' => 'Prioritas', 'Reguler' => 'Reguler', 'Urgent' => 'Urgent'] as $val => $label)
                                    <button wire:click="$set('filterPriority', '{{ $val }}')" 
                                            class="px-5 py-4 rounded-2xl text-[11px] font-black uppercase tracking-widest border transition-all flex items-center justify-center {{ $filterPriority === (string)$val ? 'bg-[#1a3b34] border-[#1a3b34] text-white shadow-xl shadow-[#1a3b34]/30 scale-[1.02]' : 'bg-gray-50 border-gray-100 text-gray-500 hover:bg-white hover:border-teal-200 hover:text-teal-600' }}">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Data Selection Grid --}}
                        <div class="grid grid-cols-1 gap-6">
                            {{-- Brand Filter --}}
                            <div class="space-y-3">
                                <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 flex items-center gap-2">
                                    <div class="w-1.5 h-1.5 rounded-full bg-gray-300"></div>
                                    Product Brand
                                </label>
                                <div class="relative group">
                                    <select wire:model.live="filterBrand" class="w-full bg-gray-50 border-gray-100 rounded-2xl py-3.5 pl-5 pr-10 text-[11px] font-bold text-gray-700 appearance-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all cursor-pointer">
                                        <option value="">All Brands</option>
                                        @foreach($availableBrands as $brand)
                                            <option value="{{ $brand }}">{{ $brand }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                            </div>

                            {{-- Type Filter --}}
                            <div class="space-y-3">
                                <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 flex items-center gap-2">
                                    <div class="w-1.5 h-1.5 rounded-full bg-gray-300"></div>
                                    Shoe Category
                                </label>
                                <div class="relative group">
                                    <select wire:model.live="filterType" class="w-full bg-gray-50 border-gray-100 rounded-2xl py-3.5 pl-5 pr-10 text-[11px] font-bold text-gray-700 appearance-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all cursor-pointer">
                                        <option value="">All Types</option>
                                        @foreach($availableTypes as $type)
                                            <option value="{{ $type }}">{{ $type }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Metrics Row --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            {{-- Total Queue --}}
            <div class="bg-[#1a3b34] rounded-[2rem] p-8 text-white shadow-xl shadow-teal-900/10 relative overflow-hidden group">
                <div class="relative z-10">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-teal-300/80 mb-2">Total Queue</p>
                    <div class="flex items-baseline gap-2">
                        <h2 class="text-4xl font-black tabular-nums">{{ number_format($totalCount) }}</h2>
                        <span class="text-xs font-bold text-teal-400/80 flex items-center gap-1">Live</span>
                    </div>
                </div>
            </div>

            {{-- Ready --}}
            <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-100 flex flex-col justify-between relative group">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-2">Ready</p>
                    <h2 class="text-4xl font-black text-[#1a3b34] tabular-nums">{{ number_format($readyCount) }}</h2>
                </div>
                <div class="mt-4">
                    <span class="inline-flex items-center px-3 py-1 bg-green-50 text-[10px] font-black uppercase tracking-widest text-[#22AF85] rounded-lg">ALLOCATED</span>
                </div>
            </div>

            {{-- Waiting (Stock Shortage) --}}
            <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-100 flex flex-col justify-between relative group">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-2">Stock Shortage</p>
                    <h2 class="text-4xl font-black text-[#1a3b34] tabular-nums">{{ number_format($waitingCount) }}</h2>
                </div>
                <div class="mt-4">
                    <span class="inline-flex items-center px-3 py-1 bg-amber-50 text-[10px] font-black uppercase tracking-widest text-amber-600 rounded-lg">WAITING ACTION</span>
                </div>
            </div>

            {{-- Empty Materials (Friction Zone) --}}
            <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-100 flex flex-col justify-between relative group border-l-4 border-l-red-400">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-red-400 mb-2">Needs Material Data</p>
                    <h2 class="text-4xl font-black text-red-600 tabular-nums">{{ number_format($needsRequestCount) }}</h2>
                </div>
                <div class="mt-4">
                    <span class="inline-flex items-center px-3 py-1 bg-red-50 text-[10px] font-black uppercase tracking-widest text-red-600 rounded-lg">EMPTY INPUT</span>
                </div>
            </div>
        </div>

        {{-- Tab Controls & List Header --}}
        <div class="flex flex-col sm:flex-row items-center justify-between border-b border-gray-200 mb-8 gap-4">
            <div class="flex gap-8">
                <button wire:click="$set('activeTab', 'ready')" 
                        class="pb-4 px-1 relative transition-all group {{ $activeTab === 'ready' ? 'text-[#1a3b34]' : 'text-gray-400 hover:text-gray-600' }}">
                    <span class="text-sm font-black uppercase tracking-widest flex items-center gap-2">
                        Siap Produksi
                        <span class="text-[10px] px-2 py-0.5 rounded-md {{ $activeTab === 'ready' ? 'bg-teal-100 text-teal-700' : 'bg-gray-100 text-gray-400' }}">
                            {{ number_format($readyOrders->total()) }}
                        </span>
                    </span>
                    @if($activeTab === 'ready') <div class="absolute bottom-0 left-0 w-full h-[3px] bg-teal-500 rounded-t-full"></div> @endif
                </button>
                <button wire:click="$set('activeTab', 'waiting')" 
                        class="pb-4 px-1 relative transition-all group {{ $activeTab === 'waiting' ? 'text-[#1a3b34]' : 'text-gray-400 hover:text-gray-600' }}">
                    <span class="text-sm font-black uppercase tracking-widest flex items-center gap-2">
                        In Procurement
                        <span class="text-[10px] px-2 py-0.5 rounded-md {{ $activeTab === 'waiting' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-400' }}">
                            {{ number_format($waitingCount) }}
                        </span>
                    </span>
                    @if($activeTab === 'waiting') <div class="absolute bottom-0 left-0 w-full h-[3px] bg-amber-500 rounded-t-full"></div> @endif
                </button>
                <button wire:click="$set('activeTab', 'needs_request')" 
                        class="pb-4 px-1 relative transition-all group {{ $activeTab === 'needs_request' ? 'text-[#1a3b34]' : 'text-gray-400 hover:text-gray-600' }}">
                    <span class="text-sm font-black uppercase tracking-widest flex items-center gap-2">
                        Belum Request
                        <span class="text-[10px] px-2 py-0.5 rounded-md {{ $activeTab === 'needs_request' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-400' }}">
                            {{ number_format($needsRequestCount) }}
                        </span>
                    </span>
                    @if($activeTab === 'needs_request') <div class="absolute bottom-0 left-0 w-full h-[3px] bg-red-500 rounded-t-full"></div> @endif
                </button>
            </div>
            <div x-data="{ open: false }" class="relative pb-4">
                <button @click="open = !open" class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-[#1a3b34] transition-colors">
                    Sorted by: <span class="text-gray-900">{{ collect([
                        'priority_newest' => 'Newest Priority',
                        'newest_spk' => 'Newest SPK',
                        'oldest_spk' => 'Oldest SPK',
                        'spk_asc' => 'SPK Number (A-Z)'
                    ])->get($sortBy) }}</span>
                    <svg class="w-4 h-4 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4.5h18m-18 5h10m-10 5h6"></path></svg>
                </button>

                <div x-show="open" x-cloak @click.away="open = false" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-gray-100 z-50 p-2 overflow-hidden">
                    @foreach([
                        'priority_newest' => 'Newest Priority',
                        'newest_spk' => 'Newest SPK',
                        'oldest_spk' => 'Oldest SPK',
                        'spk_asc' => 'SPK Number (A-Z)'
                    ] as $val => $label)
                        <button wire:click="$set('sortBy', '{{ $val }}')" @click="open = false" 
                                class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $sortBy === $val ? 'bg-teal-50 text-teal-700' : 'text-gray-600 hover:bg-gray-50' }}">
                            {{ $label }}
                            @if($sortBy === $val)
                                <svg class="w-4 h-4 text-teal-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Main Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            @php 
                $currentOrders = match($activeTab) {
                    'ready' => $readyOrders,
                    'waiting' => $waitingOrders,
                    'needs_request' => $needsRequestOrders,
                    default => $readyOrders
                }; 
            @endphp
            
            @forelse($currentOrders as $order)
                <div class="bg-white rounded-[2rem] overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group flex flex-col sm:flex-row border border-gray-100">
                    {{-- Visual Left Side --}}
                    <div class="w-full sm:w-[220px] bg-black relative flex items-center justify-center overflow-hidden min-h-[220px]">
                        @if($order->spk_cover_photo_url)
                            <img src="{{ $order->spk_cover_photo_url }}" class="absolute inset-0 w-full h-full object-cover opacity-80 group-hover:scale-110 transition-transform duration-700" alt="Item Photo">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        @else
                            <div class="absolute inset-0 bg-gradient-to-br from-gray-800 to-black opacity-60"></div>
                            <svg class="w-16 h-16 text-white/5 relative z-10" fill="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        @endif
                        
                        <div class="absolute top-4 left-4 bg-white/20 backdrop-blur-md px-3 py-1.5 rounded-lg border border-white/10">
                            <span class="text-[10px] font-black text-white uppercase tracking-widest">{{ $order->spk_number }}</span>
                        </div>

                        {{-- Hover overlay for quick image view or gallery? --}}
                    </div>

                    {{-- Content Right Side --}}
                    <div class="flex-1 p-8 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-1">
                                <h3 class="text-xl font-black text-[#1a3b34] leading-tight group-hover:text-teal-600 transition-colors uppercase">
                                    {{ $order->customer?->name ?? 'Guest' }} • {{ $order->shoe_type ?? 'Sneakers' }}
                                </h3>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black tracking-tighter uppercase {{ $order->priority == 'Urgent' ? 'bg-red-50 text-red-600' : 'bg-blue-50 text-blue-600' }}">
                                    {{ $order->priority }}
                                </span>
                            </div>
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-6">
                                BRAND: <span class="text-gray-600 mr-3 truncate max-w-[120px] inline-block align-bottom">{{ $order->shoe_brand ?? 'N/A' }}</span>
                                SIZE: <span class="text-gray-600">{{ $order->shoe_size ?? 'N/A' }}</span>
                            </p>

                            {{-- Progress Bar --}}
                            <div class="space-y-2 mb-6">
                                @php
                                    $totalMats = $order->materials->count();
                                    $allocatedMats = $order->materials->where('pivot.status', 'ALLOCATED')->count();
                                    $percent = $totalMats > 0 ? round(($allocatedMats / $totalMats) * 100) : 0;
                                    $isReady = $totalMats > 0 && $percent == 100;
                                @endphp
                                <div class="flex justify-between items-center text-[9px] font-black uppercase tracking-widest mb-1.5">
                                    <span class="text-gray-400">Material Allocation</span>
                                    @if($totalMats > 0)
                                        <span class="{{ $isReady ? 'text-[#22AF85]' : 'text-gray-400' }}">{{ $percent }}% READY</span>
                                    @else
                                        <span class="text-red-400">0% INPUT</span>
                                    @endif
                                </div>
                                <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden flex">
                                    <div class="h-full rounded-full transition-all duration-700 {{ $isReady ? 'bg-[#22AF85]' : 'bg-amber-400' }}" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>

                            {{-- Micro-indicators (Technical Accessories T, I, B, O) --}}
                            <div class="flex items-center gap-4">
                                <div class="flex gap-1.5">
                                    @foreach(['tali' => 'T', 'insole' => 'I', 'box' => 'B'] as $field => $label)
                                        @php
                                            $status = $order->{"accessories_$field"} ?? 'N';
                                            // Handle long format from legacy or other modules
                                            $statusCode = match($status) {
                                                'Simpan', 'T' => 'T',
                                                'Susulan', 'S' => 'S',
                                                default => 'N'
                                            };
                                            
                                            $style = match($statusCode) {
                                                'T' => 'bg-[#22AF85] text-white shadow-md shadow-[#22AF85]/20',
                                                'S' => 'bg-[#FFC232] text-white shadow-md shadow-[#FFC232]/20',
                                                default => 'bg-gray-50 text-gray-400 border border-gray-100'
                                            };
                                        @endphp
                                        <div class="w-8 h-8 rounded-xl flex flex-col items-center justify-center text-[10px] font-black transition-all border {{ $style }}" title="{{ strtoupper($field) }}: {{ $status }}">
                                            <span class="opacity-50 text-[7px] -mb-1">{{ $label }}</span>
                                            <span>{{ $statusCode }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="h-4 w-px bg-gray-200"></div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest truncate max-w-[150px]">
                                    Color: <span class="text-gray-500 font-black">{{ $order->shoe_color ?? '-' }}</span>
                                </span>
                            </div>
                        </div>

                        {{-- Footer Actions --}}
                        <div class="flex items-center gap-2 mt-8">
                            <a href="{{ route('sortir.show', $order->id) }}" wire:navigate
                               class="flex-1 bg-[#1a3b34] hover:bg-teal-800 text-white py-3.5 px-6 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] transition-all flex items-center justify-center gap-2 shadow-lg shadow-teal-900/10">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                Check Detail
                            </a>
                            
                            
                            @if($activeTab === 'waiting')
                                @php
                                    $hasActivePO = $order->materialRequests->whereIn('status', ['PENDING', 'APPROVED', 'PURCHASED'])->isNotEmpty();
                                @endphp

                                @if(!$hasActivePO)
                                    <button wire:click="requestMaterial({{ $order->id }})" wire:loading.attr="disabled"
                                            class="flex-1 bg-red-600 hover:bg-red-700 text-white py-3.5 px-6 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] transition-all flex items-center justify-center gap-2 shadow-lg shadow-red-900/20">
                                        <svg wire:loading.remove wire:target="requestMaterial({{ $order->id }})" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                                        <svg wire:loading wire:target="requestMaterial({{ $order->id }})" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Ajukan ke Purchasing
                                    </button>
                                @else
                                    <div class="flex-1 bg-gray-50 border border-gray-100 text-gray-400 py-3.5 px-6 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] flex items-center justify-center gap-2 cursor-default">
                                        <svg class="w-4 h-4 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        PO Dikirim
                                    </div>
                                @endif
                            @endif

                            @if(in_array(auth()->user()->role, ['admin', 'owner', 'production_manager']))
                                <button wire:click="bypassSingle({{ $order->id }})" wire:confirm="Kirim order #{{ $order->spk_number }} langsung ke Produksi?"
                                        class="p-4 bg-white border border-gray-100 text-gray-400 hover:text-teal-600 hover:border-teal-100 hover:bg-teal-50 rounded-2xl transition-all shadow-sm"
                                        title="Bypass to Production">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                                </button>
                            @endif

                            {{-- Follow Up / Report Issue --}}
                            <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-report-modal', { detail: {{ $order->id }} }))" 
                                    class="p-4 bg-amber-50 border border-amber-100 text-amber-600 hover:text-amber-700 hover:bg-amber-100 rounded-2xl transition-all shadow-sm"
                                    title="Lapor / Follow Up">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="lg:col-span-2 py-32 flex flex-col items-center justify-center bg-white rounded-[2rem] border-2 border-dashed border-gray-100 italic">
                    <svg class="w-16 h-16 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">No active orders in this queue</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-12">
            {{ $currentOrders->links() }}
        </div>
    </div>

    {{-- REPORT ISSUE MODAL --}}
    <x-report-modal />
</div>
