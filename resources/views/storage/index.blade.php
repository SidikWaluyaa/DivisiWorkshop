<x-app-layout>
    <style>
        :root {
            --primary-green: #22AF85;
            --accent-yellow: #FFC232;
            --dark-gray: #1F2937;
            --light-gray: #F9FAFB;
        }
        .bg-primary-green { background-color: var(--primary-green); }
        .text-primary-green { color: var(--primary-green); }
        .border-primary-green { border-color: var(--primary-green); }
        .bg-accent-yellow { background-color: var(--accent-yellow); }
        .text-accent-yellow { color: var(--accent-yellow); }
        .border-accent-yellow { border-color: var(--accent-yellow); }
        
        .premium-card {
            background: white;
            border: 1px solid rgba(0,0,0,0.03);
            box-shadow: 0 4px 20px -5px rgba(0,0,0,0.05);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        }
        .premium-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -10px rgba(34, 175, 133, 0.08);
        }
        .glass-header {
            background: linear-gradient(135deg, var(--primary-green) 0%, #1a8a69 100%);
            position: relative;
        }
        .rack-item {
            aspect-ratio: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            font-weight: 900;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 3px solid white;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }
        .rack-item:hover {
            transform: scale(1.15) rotate(1deg);
            z-index: 20;
            box-shadow: 0 15px 30px -5px rgba(34, 175, 133, 0.2);
        }

        /* Premium Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #22AF85;
            border-radius: 10px;
            border: 2px solid #f1f1f1;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #1a8a69;
        }
    </style>

    @php
        $category = request('category', session('storage_category', 'shoes'));
    @endphp

    <div x-data="{ 
        showRackModal: false, 
        selectedRack: null, 
        rackItems: [],
        isLoading: false,
        fetchRackDetails(rackCode) {
            this.selectedRack = rackCode;
            this.showRackModal = true;
            this.isLoading = true;
            this.rackItems = [];
            
            fetch(`{{ route('storage.rack-details', ['rackCode' => 'PLACEHOLDER']) }}`.replace('PLACEHOLDER', rackCode) + `?category={{ $category }}`)
                .then(res => res.json())
                .then(data => {
                    this.rackItems = data.items;
                    this.isLoading = false;
                })
                .catch(err => {
                    console.error(err);
                    this.isLoading = false;
                });
        }
    }" class="min-h-screen bg-[#FDFDFD]">
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">
            
            {{-- Header Section --}}
            <section class="glass-header overflow-hidden rounded-[3rem] shadow-2xl relative border-b-8 border-accent-yellow">
                <div class="relative z-10 px-12 py-14 flex flex-col lg:flex-row lg:items-center justify-between gap-10">
                    <div>
                        <div class="inline-flex items-center gap-3 px-5 py-2.5 bg-white/10 backdrop-blur-xl rounded-full border border-white/20 mb-6 shadow-xl">
                            <span class="w-2.5 h-2.5 rounded-full bg-accent-yellow shadow-[0_0_15px_rgba(255,194,50,0.8)] animate-pulse"></span>
                            <span class="text-[10px] font-black text-white uppercase tracking-[0.3em]">Storage Management System</span>
                        </div>
                        <h1 class="text-6xl lg:text-7xl font-black text-white tracking-tighter leading-[0.9]">
                            @if($category === 'before') GUDANG<br/><span class="text-accent-yellow">INBOUND</span>
                            @elseif($category === 'accessories') AREA<br/><span class="text-accent-yellow">AKSESORIS</span>
                            @else GUDANG<br/><span class="text-accent-yellow">FINISH</span>
                            @endif
                        </h1>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-5">
                        <a href="{{ route('storage.racks.index') }}" class="px-10 py-5 bg-accent-yellow text-gray-900 rounded-2xl font-black hover:bg-yellow-400 transition-all flex items-center justify-center gap-3 shadow-[0_15px_30px_-5px_rgba(255,194,50,0.4)] transform hover:-translate-y-1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                            MASTER RAK
                        </a>

                        <form action="{{ route('storage.index') }}" method="GET" class="relative">
                            <input type="hidden" name="category" value="{{ $category }}">
                            <input type="text" name="search" value="{{ $search ?? '' }}" 
                                   placeholder="Cari SPK / Customer..." 
                                   class="w-full sm:w-80 pl-16 pr-8 py-5 rounded-2xl border-none bg-white/10 backdrop-blur-xl text-white placeholder-white/50 focus:bg-white focus:text-gray-900 focus:ring-0 transition-all shadow-xl font-bold border border-white/20">
                            <div class="absolute left-6 top-1/2 -translate-y-1/2 text-accent-yellow">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                        </form>
                    </div>
                </div>
                {{-- Decor --}}
                <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 -mr-48 -mt-48 rounded-full blur-3xl"></div>
            </section>

            {{-- Category Filter --}}
            <nav class="flex overflow-x-auto gap-3 pb-2 scrollbar-hide">
                <a href="{{ route('storage.index', ['category' => 'shoes']) }}" 
                   class="{{ $category === 'shoes' ? 'bg-primary-green text-white shadow-2xl translate-y-[-2px]' : 'bg-white text-gray-400 hover:text-primary-green' }} px-10 py-5 rounded-2xl font-black text-sm flex flex-col items-center gap-1 min-w-[160px] transition-all border border-gray-100 shadow-sm">
                    <span class="text-2xl">👟</span>
                    <span>SEPATU FINISH</span>
                </a>
                <a href="{{ route('storage.index', ['category' => 'accessories']) }}" 
                   class="{{ $category === 'accessories' ? 'bg-primary-green text-white shadow-2xl translate-y-[-2px]' : 'bg-white text-gray-400 hover:text-primary-green' }} px-10 py-5 rounded-2xl font-black text-sm flex flex-col items-center gap-1 min-w-[160px] transition-all border border-gray-100 shadow-sm">
                    <span class="text-2xl">🎒</span>
                    <span>AKSESORIS</span>
                </a>
                <a href="{{ route('storage.index', ['category' => 'before']) }}" 
                   class="{{ $category === 'before' ? 'bg-primary-green text-white shadow-2xl translate-y-[-2px]' : 'bg-white text-gray-400 hover:text-primary-green' }} px-10 py-5 rounded-2xl font-black text-sm flex flex-col items-center gap-1 min-w-[160px] transition-all border border-gray-100 shadow-sm">
                    <span class="text-2xl">📥</span>
                    <span>INBOUND RACK</span>
                </a>
            </nav>

            {{-- Statistics --}}
            <section class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @php
                    $statsConfig = [
                        ['label' => 'Total Item Stored', 'value' => $stats['total_stored'], 'color' => 'text-primary-green', 'bg' => 'bg-white'],
                        ['label' => 'Item Out / Retrieved', 'value' => $stats['total_retrieved'], 'color' => 'text-gray-900', 'bg' => 'bg-accent-yellow'],
                        ['label' => 'Peringatan Overdue', 'value' => $stats['overdue_count'], 'color' => 'text-primary-green', 'bg' => 'bg-white'],
                        ['label' => 'Rata-rata Simpan', 'value' => number_format($stats['avg_storage_days'], 1) . ' Hari', 'color' => 'text-gray-500', 'bg' => 'bg-gray-50'],
                    ];
                @endphp
                @foreach($statsConfig as $s)
                    <div class="premium-card rounded-3xl p-8 {{ $s['bg'] }} border">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">{{ $s['label'] }}</p>
                        <h4 class="text-3xl font-black {{ $s['color'] }}">{{ $s['value'] }}</h4>
                    </div>
                @endforeach
            </section>

            {{-- Digital Warehouse Map --}}
            <section class="premium-card rounded-[3rem] overflow-hidden border-2 border-gray-50">
                <div class="px-12 py-10 border-b border-gray-50 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 bg-gray-50/20">
                    <div>
                        <h3 class="text-3xl font-black text-gray-900 tracking-tighter">PETA VISUAL GUDANG</h3>
                        <p class="text-xs font-bold text-primary-green uppercase tracking-[0.2em] mt-1">Status Ketersediaan Rak & Slot</p>
                    </div>
                    <div class="flex items-center gap-6">
                        <div class="flex items-center gap-2">
                            <span class="w-4 h-4 rounded-lg bg-white border-2 border-primary-green/20"></span>
                            <span class="text-[10px] font-black text-gray-400 uppercase">KOSONG</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-4 h-4 rounded-lg bg-primary-green"></span>
                            <span class="text-[10px] font-black text-gray-400 uppercase">TERISI</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-4 h-4 rounded-lg bg-accent-yellow"></span>
                            <span class="text-[10px] font-black text-gray-400 uppercase">PENUH / HIGH</span>
                        </div>
                    </div>
                </div>
                
                <div class="p-12">
                    {{-- Capacity Overview Bar --}}
                    <div class="mb-14">
                        <div class="flex justify-between items-end mb-4">
                            <span class="text-xs font-black text-gray-900 uppercase tracking-widest">Utilisasi Kapasitas Global</span>
                            <span class="text-3xl font-black text-primary-green">{{ number_format($rackUtilization['utilization_percentage'], 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 h-6 rounded-full overflow-hidden p-1 shadow-inner">
                            <div class="bg-primary-green h-full rounded-full shadow-lg shadow-emerald-200 transition-all duration-1000" style="width: {{ $rackUtilization['utilization_percentage'] }}%"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10 gap-5">
                        @php
                            $currentRacks = $category === 'before' ? $beforeRacks : ($category === 'accessories' ? $accessoryRacks : $shoeRacks);
                        @endphp
                        
                        @foreach($currentRacks as $rack)
                            @php
                                $utilization = $rack->getUtilizationPercentage();
                                $rackStyle = $utilization >= 100 
                                    ? 'bg-accent-yellow text-gray-900 border-accent-yellow shadow-[0_10px_20px_-5px_rgba(255,194,50,0.5)]' 
                                    : ($utilization > 0 
                                        ? 'bg-primary-green text-white border-primary-green shadow-[0_10px_20px_-5px_rgba(34,175,133,0.3)]' 
                                        : 'bg-white text-primary-green border-gray-100');
                            @endphp
                            <div @click="fetchRackDetails('{{ $rack->rack_code }}')" 
                                 class="rack-item {{ $rackStyle }} border-2 group relative">
                                <span class="text-lg font-black tracking-tighter">{{ $rack->rack_code }}</span>
                                <span class="text-[9px] font-bold opacity-60">{{ $rack->current_count }}/{{ $rack->capacity }}</span>
                                
                                {{-- Smart Tooltip --}}
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-5 w-44 p-4 bg-gray-900 rounded-2xl opacity-0 group-hover:opacity-100 transition-all pointer-events-none z-50 shadow-2xl transform scale-75 group-hover:scale-100">
                                    <p class="text-[10px] font-black text-accent-yellow uppercase mb-2 tracking-widest text-center">{{ $rack->location }}</p>
                                    <div class="flex justify-between items-center text-white px-1">
                                        <span class="text-[10px] font-bold opacity-60 uppercase">Load:</span>
                                        <span class="text-sm font-black">{{ number_format($utilization, 0) }}%</span>
                                    </div>
                                    <div class="w-full bg-white/10 h-1 rounded-full mt-2 overflow-hidden">
                                        <div class="bg-accent-yellow h-full" style="width: {{ $utilization }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- Modern Table Section --}}
            <section class="premium-card rounded-[3rem] overflow-hidden">
                <div class="px-12 py-10 flex flex-col md:flex-row justify-between items-center gap-8 bg-white border-b border-gray-50">
                    <div>
                        <h3 class="text-3xl font-black text-gray-900 tracking-tighter">DATA LOG ITEM</h3>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Riwayat Penyimpanan Real-Time</p>
                    </div>
                    <button class="px-8 py-4 bg-primary-green text-white rounded-2xl font-black text-xs hover:bg-emerald-600 transition-all shadow-xl shadow-emerald-100">
                        EXPORT DATA REKAP
                    </button>
                </div>
                
                <div class="p-4">
                    <table class="w-full border-separate border-spacing-y-4">
                        <thead>
                            <tr class="text-primary-green text-[10px] font-black uppercase tracking-[0.4em]">
                                <th class="px-8 pb-4 text-left">ITEM ANALYSIS</th>
                                <th class="px-8 pb-4 text-left">CUSTOMER / OWNER</th>
                                <th class="px-8 pb-4 text-center">RACK POS</th>
                                <th class="px-8 pb-4 text-right whitespace-nowrap">ACTION PANEL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($storedItems as $item)
                                <tr class="bg-white hover:bg-gray-50/50 transition-all group border border-gray-100">
                                    <td class="px-8 py-7 rounded-l-3xl border-l border-y border-gray-100">
                                        <div class="flex items-center gap-5">
                                            <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center text-2xl group-hover:bg-primary-green group-hover:text-white transition-all transform group-hover:rotate-6">
                                                @if($item->item_type === 'shoes') 👟 @else 📦 @endif
                                            </div>
                                            <div>
                                                <div class="text-xl font-black text-gray-900 tracking-tight leading-none mb-1">{{ $item->workOrder?->spk_number ?? 'N/A' }}</div>
                                                <div class="text-[10px] font-black text-primary-green uppercase tracking-widest">
                                                    {{ $item->item_type === 'shoes' ? ($item->workOrder?->shoe_brand ?? 'SHOES') : 'ACCESSORIES' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-7 border-y border-gray-100">
                                        <div class="font-black text-gray-900 text-sm leading-none mb-1">{{ $item->workOrder?->customer?->name ?? 'Unknown' }}</div>
                                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">{{ $item->workOrder?->customer?->phone ?? '-' }}</div>
                                    </td>
                                    <td class="px-8 py-7 border-y border-gray-100 text-center">
                                        <span class="inline-block px-5 py-2.5 rounded-2xl bg-white border-2 border-primary-green/20 text-primary-green font-black text-sm shadow-sm group-hover:border-primary-green/50 transition-colors">
                                            {{ $item->rack_code }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-7 rounded-r-3xl border-r border-y border-gray-100 text-right">
                                        <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all transform translate-x-10 group-hover:translate-x-0">
                                            <a href="{{ route('storage.label', $item->id) }}" target="_blank" class="p-4 bg-white text-primary-green border border-gray-100 rounded-2xl hover:bg-gray-900 hover:text-white transition-all shadow-xl">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                            </a>
                                            @can('manageStorage')
                                            <form action="{{ route('storage.retrieve', $item->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Konfirmasi pengambilan item?')" class="p-4 bg-accent-yellow text-gray-900 rounded-2xl border-none hover:bg-yellow-400 transition-all shadow-xl font-black">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path></svg>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-32 text-center rounded-[3rem] bg-gray-50 border-4 border-dashed border-gray-200">
                                        <div class="text-6xl mb-6">📭</div>
                                        <h4 class="text-4xl font-black text-gray-300 uppercase tracking-tighter">DATA KOSONG</h4>
                                        <p class="text-gray-400 font-bold text-sm mt-2 tracking-widest uppercase">Inventory Level: 0</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($storedItems, 'links'))
                    <div class="px-12 py-10 bg-white border-t border-gray-50">
                        {{ $storedItems->links() }}
                    </div>
                @endif
            </section>
        </div>

        {{-- Interaction Modal: High-Performance Rack Detail --}}
        <div x-show="showRackModal" 
             class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             x-cloak>
            
            {{-- Elite Backdrop --}}
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-2xl" @click="showRackModal = false"></div>

            <div class="relative w-full max-w-4xl bg-white shadow-[0_50px_100px_-20px_rgba(0,0,0,0.5)] rounded-[4rem] overflow-hidden border-[12px] border-white flex flex-col max-h-[90vh]">
                
                {{-- Modal Header: Command Center Style --}}
                <div class="glass-header px-12 py-10 flex justify-between items-center shrink-0">
                    <div class="flex items-center gap-8">
                        <div class="w-24 h-24 bg-white rounded-3xl flex flex-col items-center justify-center shadow-2xl rotate-2 border-b-4 border-accent-yellow">
                            <span class="text-[10px] font-black text-primary-green opacity-40 uppercase tracking-widest mb-1">Unit</span>
                            <span class="text-5xl font-black text-primary-green leading-none" x-text="selectedRack"></span>
                        </div>
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="px-3 py-1 bg-accent-yellow text-gray-900 text-[10px] font-black rounded-lg uppercase tracking-widest shadow-lg shadow-yellow-200/40">Real-Time Data</span>
                                <span class="w-1.5 h-1.5 rounded-full bg-white opacity-40"></span>
                                <span class="text-white/60 text-[10px] font-black uppercase tracking-[0.2em]" x-text="'Sector: ' + '{{ $category }}'"></span>
                            </div>
                            <h3 class="text-4xl font-black text-white uppercase tracking-tighter leading-none">ANALISIS RAK TERINTEGRASI</h3>
                        </div>
                    </div>
                    
                    {{-- Item Counter Indicator --}}
                    <div class="hidden md:flex items-center gap-4 bg-white/10 px-6 py-3 rounded-2xl border border-white/20 backdrop-blur-md">
                        <div class="text-right">
                            <p class="text-[10px] font-black text-white/60 uppercase tracking-widest leading-none mb-1">Total Stored</p>
                            <p class="text-2xl font-black text-accent-yellow leading-none tabular-nums" x-text="rackItems.length"></p>
                        </div>
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                    </div>

                    <button @click="showRackModal = false" class="bg-black/10 hover:bg-white text-white hover:text-primary-green p-4 rounded-full transition-all border border-white/20 backdrop-blur-md ml-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                {{-- Scrollable Analysis Area --}}
                <div class="p-12 overflow-y-auto bg-gray-50/20 custom-scrollbar">
                    {{-- Loading Sequence --}}
                    <template x-if="isLoading">
                        <div class="flex flex-col items-center justify-center py-24 space-y-8">
                            <div class="relative">
                                <div class="w-24 h-24 border-8 border-primary-green/5 border-t-primary-green rounded-full animate-spin"></div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="w-5 h-5 bg-accent-yellow rounded-full animate-ping"></div>
                                </div>
                            </div>
                            <p class="text-xs font-black text-primary-green animate-pulse uppercase tracking-[0.5em]">Synchronizing Master Data...</p>
                        </div>
                    </template>

                    {{-- Deployment Cards --}}
                    <template x-if="!isLoading && rackItems.length > 0">
                        <div class="grid grid-cols-1 gap-8">
                            <template x-for="item in rackItems" :key="item.id">
                                <div class="bg-white border-2 border-gray-100 rounded-[3rem] p-10 hover:border-primary-green hover:shadow-2xl transition-all group flex flex-col md:flex-row items-center gap-10">
                                    <div class="flex-1 w-full">
                                        {{-- Ticket Header --}}
                                        <div class="flex items-start justify-between mb-8">
                                            <div>
                                                <div class="flex items-center gap-4 mb-2">
                                                    <span class="text-5xl font-black text-gray-900 tracking-tighter group-hover:text-primary-green transition-colors leading-none" x-text="item.spk_number"></span>
                                                    <div class="px-5 py-2 bg-gray-900 text-white text-[10px] font-black rounded-full uppercase tracking-widest shadow-xl whitespace-nowrap" x-text="'SINCE ' + item.stored_at"></div>
                                                </div>
                                                <p class="text-xs font-bold text-gray-400 uppercase tracking-[0.1em]">Verification Complete • Secure Storage</p>
                                            </div>
                                            <div class="hidden sm:block">
                                                <svg class="w-12 h-12 text-primary-green opacity-10 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                            <div class="p-6 bg-gray-50 rounded-3xl border border-gray-100 flex items-center gap-5 group-hover:bg-white transition-colors">
                                                <div class="text-4xl">👟</div>
                                                <div>
                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Item Specification</p>
                                                    <p class="text-base font-black text-gray-800" x-text="item.item_info"></p>
                                                </div>
                                            </div>
                                            
                                            <template x-if="item.accessories">
                                                <div class="p-6 bg-primary-green/5 border-2 border-primary-green/10 rounded-3xl flex items-center gap-5">
                                                    <div class="text-4xl">📦</div>
                                                    <div>
                                                        <p class="text-[9px] font-black text-primary-green uppercase tracking-widest mb-1">Verified Accessories</p>
                                                        <p class="text-[11px] font-black text-gray-600 leading-snug" x-text="Object.values(item.accessories).filter(v => v && v !== '-').join(' • ') || 'Standard Unit Only'"></p>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                    
                                    {{-- Action Panel --}}
                                    <div class="flex flex-row md:flex-col gap-4 w-full md:w-28">
                                        <a :href="`/warehouse/${item.id}/label`" target="_blank" class="flex-1 md:flex-none p-6 bg-white text-gray-400 border-2 border-gray-100 rounded-3xl hover:bg-gray-900 hover:text-white hover:border-gray-900 transition-all shadow-xl flex items-center justify-center">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        </a>
                                        <form :action="`/warehouse/${item.id}/retrieve`" method="POST" class="flex-1 md:flex-none">
                                            @csrf
                                            <button type="submit" class="w-full p-6 bg-accent-yellow text-gray-900 rounded-3xl hover:bg-yellow-400 transition-all shadow-[0_15px_30px_-5px_rgba(255,194,50,0.5)] flex items-center justify-center">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    {{-- Empty State --}}
                    <template x-if="!isLoading && rackItems.length === 0">
                        <div class="flex flex-col items-center justify-center py-20 text-center rounded-[4rem] border-4 border-dashed border-gray-100 bg-gray-50/30">
                            <span class="text-8xl mb-8 opacity-40 grayscale">🗳️</span>
                            <h4 class="text-4xl font-black text-gray-300 uppercase tracking-tighter mb-2">CAPACITY READY</h4>
                            <p class="text-primary-green font-black text-[10px] tracking-[0.4em] uppercase">Sector clear • Waiting for deployment</p>
                        </div>
                    </template>
                </div>

                {{-- Modal Bottom Nav --}}
                <div class="px-14 py-10 bg-white border-t border-gray-50 flex justify-between items-center shrink-0">
                    <div class="text-[10px] font-black text-gray-300 uppercase tracking-widest">
                        System Integrity Checked
                    </div>
                    <button @click="showRackModal = false" class="px-14 py-5 bg-gray-900 text-white font-black rounded-3x-large hover:bg-black transition-all shadow-2xl uppercase tracking-[0.3em] text-[10px] rounded-3xl">
                        RETURN TO HUB
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
