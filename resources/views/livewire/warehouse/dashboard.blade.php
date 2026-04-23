<div class="relative">
    {{-- Global Loading Bar --}}
    <div wire:loading class="fixed top-0 left-0 right-0 h-1 bg-gradient-to-r from-[#22AF85] to-[#FFC232] z-[9999] opacity-100 transition-opacity"></div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 w-full">
            <h2 class="font-black text-xl text-white leading-tight flex items-center gap-4">
                <div class="p-2 bg-white/10 rounded-xl shadow-inner backdrop-blur-md border border-white/20">
                    <span class="text-xl">🏢</span>
                </div>
                {{ __('Pusat Kendali Gudang') }}
            </h2>
            <div x-data="{ now: new Date().toLocaleTimeString() }" x-init="setInterval(() => now = new Date().toLocaleTimeString(), 1000)" 
                 class="flex items-center gap-3 px-4 py-2 bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl shadow-lg shrink-0">
                <span class="h-1.5 w-1.5 rounded-full bg-[#FFC232] live-indicator"></span>
                <span class="text-[9px] font-black text-white/90 uppercase tracking-[0.2em]">
                    DASHBOARD <span class="text-[#FFC232]">LIVE</span>: <span x-text="now" class="text-white"></span>
                </span>
            </div>
        </div>
    </x-slot>

    <style>
        :root { --brand-green: #22AF85; --brand-yellow: #FFC232; }
        [x-cloak] { display: none !important; }
        .stat-card { transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 15px 30px -10px rgba(0, 0, 0, 0.08); }
        .glass-panel { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.3); }
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        @keyframes pulse-ring { 0% { transform: scale(0.8); opacity: 0.5; } 100% { transform: scale(2); opacity: 0; } }
        .live-indicator { position: relative; }
        .live-indicator::after { content: ''; position: absolute; width: 100%; height: 100%; top: 0; left: 0; background: inherit; border-radius: inherit; animation: pulse-ring 1.5s cubic-bezier(0.455, 0.03, 0.515, 0.955) infinite; }
    </style>

    <div class="py-6 bg-[#F8FAFC] min-h-screen" wire:poll.10s="refreshData" x-data="{ 
        activeTab: @entangle('activeTab').live,
        activeRackTab: @entangle('activeRackTab').live,
        showCustomDate: false
    }">
        <div class="max-w-[1600px] mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Toolbar: Compact Search --}}
            <div class="flex flex-col xl:flex-row justify-between items-center gap-4 bg-white/60 p-4 rounded-[1.5rem] shadow-lg border border-white glass-panel relative z-[50]">
                <div class="flex flex-col md:flex-row items-center gap-4 w-full xl:w-auto">
                    {{-- Search Input --}}
                    <div class="relative group w-full md:w-80" x-data>
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#22AF85] transition-colors">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search" x-ref="searchInput"
                               class="block w-full pl-10 pr-4 py-2.5 bg-white border border-gray-100 rounded-[1.2rem] text-[11px] font-black placeholder-gray-400 focus:outline-none focus:ring-4 focus:ring-[#22AF85]/5 focus:border-[#22AF85] transition-all shadow-sm" 
                               placeholder="CARI SPK / MEMBER...">
                    </div>

                    {{-- Date Filter Presets --}}
                    <div class="flex items-center gap-1.5 p-1 bg-gray-50/80 rounded-[1.2rem] border border-gray-100 flex-wrap sm:flex-nowrap relative">
                        <button wire:click="$set('dateRange', 'today')" 
                                class="px-4 py-1.5 rounded-lg text-[9px] font-black transition-all uppercase tracking-tighter {{ $dateRange === 'today' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-400 hover:text-gray-600' }}">
                            HARI INI
                        </button>
                        <button wire:click="$set('dateRange', '7_days')" 
                                class="px-4 py-1.5 rounded-lg text-[9px] font-black transition-all uppercase tracking-tighter {{ $dateRange === '7_days' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-400 hover:text-gray-600' }}">
                            7 HARI
                        </button>
                        <button wire:click="$set('dateRange', '30_days')" 
                                class="px-4 py-1.5 rounded-lg text-[9px] font-black transition-all uppercase tracking-tighter {{ $dateRange === '30_days' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-400 hover:text-gray-600' }}">
                            30 HARI
                        </button>
                        
                        <div class="h-4 w-px bg-gray-200 mx-1 hidden sm:block"></div>

                        {{-- Toggle Button for Inline Calendar --}}
                        <button @click="showCustomDate = !showCustomDate" 
                                :class="showCustomDate || '{{ $dateRange }}' === 'custom' ? 'bg-[#22AF85] text-white shadow-lg' : 'text-[#22AF85] hover:bg-[#22AF85]/5 bg-white/50 border border-white'"
                                class="px-5 py-1.5 rounded-xl text-[9px] font-black transition-all uppercase tracking-widest flex items-center gap-2">
                            📅 KALENDER
                        </button>
                    </div>
                </div>

                {{-- Range Summary Info --}}
                <div class="hidden xl:flex items-center gap-3 px-6 py-2.5 bg-[#22AF85]/5 border border-[#22AF85]/10 rounded-full">
                    <span class="h-1.5 w-1.5 rounded-full bg-[#22AF85]"></span>
                    <span class="text-[10px] font-bold text-[#22AF85]/80 uppercase tracking-widest">
                        PERIODE: <span class="text-[#22AF85]">{{ \Carbon\Carbon::parse($startDate)->format('d M') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</span>
                    </span>
                </div>
            </div>

            {{-- Inline Dynamic Filter Panel --}}
            <div x-show="showCustomDate || '{{ $dateRange }}' === 'custom'" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-cloak
                 class="bg-white/80 backdrop-blur-md p-6 rounded-[2rem] shadow-xl border border-white flex flex-col md:flex-row items-center justify-between gap-6">
                
                <div class="flex flex-col">
                    <h5 class="text-[11px] font-black text-gray-900 uppercase tracking-[0.2em] mb-1">RENTANG ANALISA KUSTOM</h5>
                    <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest">Konfigurasi periode data gudang secara presisi</p>
                </div>

                <div class="flex flex-1 items-center gap-4 w-full md:w-auto">
                    <div class="flex-1 space-y-1.5">
                        <label class="text-[8px] font-black text-gray-500 uppercase tracking-widest ml-1">MULAI</label>
                        <input type="date" wire:model.live="startDate" 
                               class="w-full text-xs font-black border-gray-100 bg-white rounded-xl focus:ring-4 focus:ring-[#22AF85]/10 focus:border-[#22AF85] transition-all">
                    </div>
                    
                    <div class="mt-6 text-gray-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </div>

                    <div class="flex-1 space-y-1.5">
                        <label class="text-[8px] font-black text-gray-500 uppercase tracking-widest ml-1">SELESAI</label>
                        <input type="date" wire:model.live="endDate" 
                               class="w-full text-xs font-black border-gray-100 bg-white rounded-xl focus:ring-4 focus:ring-[#22AF85]/10 focus:border-[#22AF85] transition-all">
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button @click="showCustomDate = false" class="px-6 py-2.5 text-[9px] font-black text-gray-400 hover:text-gray-600 transition-all uppercase tracking-widest">
                        TUTUP
                    </button>
                    <button wire:click="$set('dateRange', 'custom')" 
                            class="px-8 py-3 bg-gray-900 text-white text-[9px] font-black rounded-xl uppercase tracking-widest hover:bg-black transition-all shadow-lg shadow-gray-900/10">
                        SINKRONKAN DATA
                    </button>
                </div>
            </div>

            {{-- Hidden Tab Navigation placeholder --}}
            <div class="hidden">
                <button @click="activeTab = 'summary'" class="px-8 py-2.5 rounded-[1rem] bg-white text-gray-900 shadow-lg text-xs font-black">
                        📊 RINGKASAN
                </button>
            </div>

            {{-- Summary Grid --}}
            <div x-show="activeTab === 'summary'" x-transition:enter="transition ease-out duration-300" class="space-y-6">
                <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
                    
                    {{-- Hero Compact --}}
                    <div class="xl:col-span-8 bg-white rounded-[2rem] p-8 shadow-lg border border-gray-100 relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-[#22AF85]/5 via-transparent to-[#FFC232]/5"></div>
                        <div class="relative z-10">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="px-3 py-1 bg-[#22AF85]/10 text-[#22AF85] rounded-full text-[9px] font-black uppercase tracking-widest border border-[#22AF85]/20">Sistem Aktif</span>
                                <span class="h-1.5 w-1.5 rounded-full bg-green-500 live-indicator"></span>
                            </div>
                            <h1 class="text-3xl font-black text-gray-900 leading-tight mb-2">Pusat Komando <span class="text-[#22AF85]">Operasional</span></h1>
                            <p class="text-gray-400 text-sm font-medium max-w-2xl mb-8">Pantau kesehatan inventaris dan optimalisasi gudang secara real-time.</p>
                            
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-8">
                                {{-- Row 1: Current Snapshot --}}
                                <div class="space-y-1">
                                    <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-1">
                                        <span>📥</span> SPK PENDING
                                    </div>
                                    <div class="text-2xl font-black text-gray-900">{{ $stats['pending_reception'] ?? 0 }}</div>
                                </div>
                                <div class="space-y-1 border-l border-gray-50 pl-6">
                                    <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-1">
                                        <span>✨</span> DI FINISH
                                    </div>
                                    <div class="text-2xl font-black text-[#FFC232]">{{ $stats['finished_not_stored'] ?? 0 }}</div>
                                </div>
                                <div class="space-y-1 border-l border-gray-50 pl-6">
                                    <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-1">
                                        <span>📦</span> DI RAK
                                    </div>
                                    <div class="text-2xl font-black text-blue-600">{{ $stats['stored_items'] ?? 0 }}</div>
                                </div>
                                <div class="space-y-1 border-l border-gray-50 pl-6">
                                    <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-1">
                                        <span>🚀</span> SIAP DIAMBIL
                                    </div>
                                    <div class="text-2xl font-black text-[#22AF85]">{{ $stats['ready_for_pickup'] ?? 0 }}</div>
                                </div>

                                {{-- Row 2: Performance (Based on Date Range) --}}
                                <div class="pt-4 border-t border-gray-50">
                                    <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-1">
                                        <span>👟</span> SEPATU MASUK
                                    </div>
                                    <div class="text-2xl font-black text-gray-700">{{ $stats['incoming_day'] ?? 0 }}</div>
                                    <div class="text-[8px] font-bold text-gray-400 uppercase">Periode Ini</div>
                                </div>
                                <div class="pt-4 border-t border-gray-50 border-l pl-6">
                                    <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-1">
                                        <span>🏁</span> SEPATU SELESAI
                                    </div>
                                    <div class="text-2xl font-black text-gray-700">{{ $stats['finished_day'] ?? 0 }}</div>
                                    <div class="text-[8px] font-bold text-gray-400 uppercase">Periode Ini</div>
                                </div>
                                <div class="pt-4 border-t border-gray-50 border-l pl-6">
                                    <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-1">
                                        <span>🖨️</span> SPK PRINT
                                    </div>
                                    <div class="text-2xl font-black text-indigo-600">{{ $stats['spk_print'] ?? 0 }}</div>
                                    <div class="text-[8px] font-bold text-gray-400 uppercase">Lolos - Reject</div>
                                </div>
                                <div class="pt-4 border-t border-gray-50 border-l pl-6">
                                    <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-1">
                                        <span>🚚</span> ANTREAN KIRIM
                                    </div>
                                    <div class="text-2xl font-black text-orange-500">{{ $stats['shipping_pending'] ?? 0 }}</div>
                                    <div class="text-[8px] font-bold text-gray-400 uppercase">Verified Pending</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Pulse Cards Compact --}}
                    <div class="xl:col-span-4 grid grid-rows-2 gap-6">
                        <div class="bg-[#22AF85] rounded-[2rem] p-6 shadow-xl text-white relative overflow-hidden group">
                            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                            <h3 class="text-[9px] font-black uppercase tracking-widest text-white/70 mb-4">Skor Kesehatan Ops</h3>
                            <div class="flex items-end justify-between mb-4">
                                <div class="text-5xl font-black text-white">{{ $efficiencyStats['health_score'] ?? 0 }}%</div>
                                <div class="px-3 py-1 bg-white/20 rounded-lg text-[9px] font-black uppercase text-white">SEMPURNA</div>
                            </div>
                            <div class="h-1.5 bg-white/20 rounded-full overflow-hidden">
                                <div class="h-full bg-[#FFC232] transition-all duration-1000" style="width: {{ $efficiencyStats['health_score'] ?? 0 }}%"></div>
                            </div>
                        </div>

                        <div class="bg-white rounded-[2rem] p-6 shadow-lg border border-gray-100 relative group">
                            <h3 class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-4">Rata-rata Waktu Inap</h3>
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center text-2xl shadow-inner">⏳</div>
                                <div>
                                    <div class="text-3xl font-black text-gray-900">{{ $efficiencyStats['avg_dwell_hours'] ?? 0 }}<span class="text-base font-bold">jam</span></div>
                                    <div class="text-[8px] font-black text-[#22AF85] flex items-center gap-1 uppercase">
                                        🚀 Perputaran Cepat
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Storage Heatmap Compact --}}
                    <div class="xl:col-span-12 bg-white rounded-[2rem] p-8 shadow-lg border border-gray-100">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-8 bg-gray-50/50 p-4 rounded-[1.5rem] border border-gray-100">
                            <div class="flex items-center gap-6">
                                <div>
                                    <h3 class="text-xl font-black text-gray-900">Peta Okupansi <span class="text-gray-400">Rak</span></h3>
                                    <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mt-0.5">Live Occupancy Grid</p>
                                </div>
                                <div class="hidden sm:flex bg-white/50 p-1 rounded-xl shadow-inner border border-gray-200/50">
                                    <button @click="activeRackTab = 'shoes'" 
                                            :class="activeRackTab === 'shoes' ? 'bg-[#22AF85] text-white shadow-lg' : 'text-gray-400 hover:text-gray-600'"
                                            class="px-5 py-2 rounded-lg text-[9px] font-black transition-all uppercase tracking-widest">
                                        ✨ Finish
                                    </button>
                                    <button @click="activeRackTab = 'accessories'" 
                                            :class="activeRackTab === 'accessories' ? 'bg-[#22AF85] text-white shadow-lg' : 'text-gray-400 hover:text-gray-600'"
                                            class="px-5 py-2 rounded-lg text-[9px] font-black transition-all uppercase tracking-widest">
                                        📦 Aksesoris
                                    </button>
                                    <button @click="activeRackTab = 'before'" 
                                            :class="activeRackTab === 'before' ? 'bg-[#22AF85] text-white shadow-lg' : 'text-gray-400 hover:text-gray-600'"
                                            class="px-5 py-2 rounded-lg text-[9px] font-black transition-all uppercase tracking-widest">
                                        🚚 Inbound
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-1.5 px-3 py-1.5 bg-white rounded-lg border border-gray-100 shadow-sm">
                                    <div class="w-2 h-2 rounded-full bg-[#22AF85] animate-pulse"></div>
                                    <span class="text-[9px] font-black text-gray-500 uppercase">Tersedia</span>
                                </div>
                                <div class="flex items-center gap-1.5 px-3 py-1.5 bg-white rounded-lg border border-gray-100 shadow-sm">
                                    <div class="w-2 h-2 rounded-full bg-[#FFC232]"></div>
                                    <span class="text-[9px] font-black text-gray-500 uppercase">Optimal</span>
                                </div>
                                <div class="flex items-center gap-1.5 px-3 py-1.5 bg-white rounded-lg border border-gray-100 shadow-sm">
                                    <div class="w-2 h-2 rounded-full bg-gray-900"></div>
                                    <span class="text-[9px] font-black text-gray-500 uppercase">Penuh</span>
                                </div>
                            </div>
                        </div>

                        {{-- Mobile Tabs --}}
                        <div class="sm:hidden flex bg-gray-100 p-1 rounded-xl mb-6 shadow-inner">
                            <button @click="activeRackTab = 'shoes'" class="flex-1 py-3 rounded-lg text-[10px] font-black" :class="activeRackTab === 'shoes' ? 'bg-white shadow' : ''">FINISH</button>
                            <button @click="activeRackTab = 'accessories'" class="flex-1 py-3 rounded-lg text-[10px] font-black" :class="activeRackTab === 'accessories' ? 'bg-white shadow' : ''">AKSESORIS</button>
                            <button @click="activeRackTab = 'before'" class="flex-1 py-3 rounded-lg text-[10px] font-black" :class="activeRackTab === 'before' ? 'bg-white shadow' : ''">INBOUND</button>
                        </div>
                        
                        <div class="grid grid-cols-5 md:grid-cols-7 lg:grid-cols-10 xl:grid-cols-14 gap-3">
                            @foreach($heatmapData as $rack)
                                <div class="group relative" x-show="activeRackTab === '{{ $rack['category'] }}'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                                    <div class="aspect-square rounded-xl border-2 transition-all duration-300 flex flex-col items-center justify-center cursor-help shadow-sm
                                        {{ $rack['color'] === 'black' ? 'bg-gray-900 border-gray-900' : ($rack['color'] === 'yellow' ? 'bg-[#FFC232]/10 border-[#FFC232]/30' : 'bg-[#22AF85]/5 border-[#22AF85]/20') }}">
                                        <div class="text-[10px] font-black {{ $rack['color'] === 'black' ? 'text-white' : ($rack['color'] === 'yellow' ? 'text-[#FFC232]' : 'text-[#22AF85]') }}">{{ $rack['code'] }}</div>
                                        <div class="text-[8px] font-black {{ $rack['color'] === 'black' ? 'text-white/40' : 'opacity-60' }}">{{ $rack['count'] }} Unit</div>
                                    </div>
                                    <div class="absolute bottom-[110%] left-1/2 -translate-x-1/2 mb-2 w-40 p-3 bg-gray-900 rounded-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-[100] text-white">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-[9px] font-black uppercase tracking-widest text-[#22AF85]">Rak {{ $rack['code'] }}</span>
                                            <span class="text-[7px] bg-white/10 px-1.5 py-0.5 rounded text-white/50 opacity-100">{{ $rack['category'] }}</span>
                                        </div>
                                        <div class="h-1 bg-white/10 rounded-full overflow-hidden">
                                            <div class="h-full bg-[#22AF85]" style="width: {{ $rack['usage'] }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Metrics Grid Compact (2 Columns) --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white rounded-[2rem] p-6 shadow-lg border border-gray-100">
                        <h4 class="text-[10px] font-black text-gray-800 mb-4 flex items-center gap-2 uppercase tracking-widest text-[#22AF85]">📈 TREN PERFORMA QC</h4>
                        <div style="height: 200px;" wire:ignore><canvas id="qcTrendsChart"></canvas></div>
                    </div>
                    <div class="bg-white rounded-[2rem] p-6 shadow-lg border border-gray-100">
                        <h4 class="text-[10px] font-black text-gray-800 mb-4 flex items-center gap-2 uppercase tracking-widest text-gray-400">📊 KOMPOSISI HASIL QC</h4>
                        <div style="height: 200px;" wire:ignore><canvas id="qcStatsChart"></canvas></div>
                    </div>
                </div>

                {{-- Operational Activity Section --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- SPK Pending --}}
                    <div class="bg-white rounded-[2rem] overflow-hidden shadow-lg border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                            <h4 class="text-[10px] font-black text-gray-800 flex items-center gap-2 uppercase tracking-widest">📥 SPK PENDING</h4>
                            <span class="px-2 py-0.5 bg-[#22AF85] text-white text-[9px] font-black rounded-full">{{ $queues['reception']->count() }}</span>
                        </div>
                        <div class="divide-y divide-gray-50 max-h-[400px] overflow-y-auto sidebar-scroll">
                            @forelse($queues['reception'] as $order)
                                <div class="p-4 hover:bg-gray-50 transition-all flex items-center justify-between group">
                                    <div class="space-y-0.5">
                                        <div class="text-[10px] font-black text-[#22AF85]">{{ $order->spk_number }}</div>
                                        <div class="text-xs font-black text-gray-900">{{ $order->customer_name }}</div>
                                        <div class="text-[8px] text-gray-400 font-bold uppercase tracking-tighter">Antre {{ $order->updated_at->diffForHumans(null, true) }}</div>
                                    </div>
                                    <a href="{{ route('reception.show', $order->id) }}" class="opacity-0 group-hover:opacity-100 px-4 py-1.5 bg-[#FFC232] text-[9px] font-black rounded-lg transition-all uppercase shadow-lg shadow-[#FFC232]/20">
                                        Proses →
                                    </a>
                                </div>
                            @empty
                                <div class="p-12 text-center text-gray-400 text-[10px] font-black uppercase opacity-40">Antrean Bersih</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- SPK Received/Needs QC --}}
                    <div class="bg-white rounded-[2rem] overflow-hidden shadow-lg border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                            <h4 class="text-[10px] font-black text-gray-800 flex items-center gap-2 uppercase tracking-widest">🔍 DITERIMA</h4>
                            <span class="px-2 py-0.5 bg-[#FFC232] text-gray-900 text-[9px] font-black rounded-full">{{ $queues['needs_qc']->count() }}</span>
                        </div>
                        <div class="divide-y divide-gray-50 max-h-[400px] overflow-y-auto sidebar-scroll">
                            @forelse($queues['needs_qc'] as $order)
                                <div class="p-4 hover:bg-gray-50 transition-all flex items-center justify-between group">
                                    <div class="space-y-0.5">
                                        <div class="text-[10px] font-black text-[#22AF85]">{{ $order->spk_number }}</div>
                                        <div class="text-xs font-black text-gray-900">{{ $order->customer_name }}</div>
                                        <div class="text-[8px] text-gray-400 font-bold uppercase tracking-tighter">Tahap QC - {{ $order->updated_at->diffForHumans(null, true) }}</div>
                                    </div>
                                    <a href="{{ route('reception.show', $order->id) }}" class="opacity-0 group-hover:opacity-100 px-4 py-1.5 bg-[#22AF85] text-white text-[9px] font-black rounded-lg transition-all uppercase shadow-lg shadow-[#22AF85]/20">
                                        Inspeksi →
                                    </a>
                                </div>
                            @empty
                                <div class="p-12 text-center text-gray-400 text-[10px] font-black uppercase opacity-40">Belum Ada Aset Diterima</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Shipping Queue Section --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="h-px flex-1 bg-gray-100"></div>
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Pusat Pengiriman</h3>
                        <div class="h-px flex-1 bg-gray-100"></div>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Shipping Unverified --}}
                        <div class="bg-white rounded-[2rem] overflow-hidden shadow-lg border border-gray-100 border-t-4 border-t-red-500">
                            <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                                <h4 class="text-[10px] font-black text-gray-800 flex items-center gap-2 uppercase tracking-widest">🚩 BELUM VERIFIKASI</h4>
                                <span class="px-2 py-0.5 bg-red-500 text-white text-[9px] font-black rounded-full">{{ $queues['shipping_unverified']->count() }}</span>
                            </div>
                            <div class="divide-y divide-gray-50 max-h-[300px] overflow-y-auto sidebar-scroll">
                                @forelse($queues['shipping_unverified'] as $ship)
                                    <div class="p-4 hover:bg-gray-50 transition-all flex items-center justify-between group">
                                        <div class="space-y-0.5">
                                            <div class="text-[10px] font-black text-red-500">{{ $ship->spk_number }}</div>
                                            <div class="text-xs font-black text-gray-900">{{ $ship->customer_name }}</div>
                                            <div class="text-[8px] text-gray-400 font-bold uppercase tracking-tighter">{{ $ship->kategori_pengiriman }} • Menunggu Verifikasi</div>
                                        </div>
                                        <a href="/shipping" class="opacity-0 group-hover:opacity-100 px-4 py-1.5 bg-gray-900 text-white text-[9px] font-black rounded-lg transition-all uppercase shadow-lg">
                                            Verifikasi →
                                        </a>
                                    </div>
                                @empty
                                    <div class="p-8 text-center text-gray-300 text-[9px] font-black uppercase tracking-widest">Semua Data Terverifikasi</div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Shipping Verified --}}
                        <div class="bg-white rounded-[2rem] overflow-hidden shadow-lg border border-gray-100 border-t-4 border-t-[#22AF85]">
                            <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                                <h4 class="text-[10px] font-black text-gray-800 flex items-center gap-2 uppercase tracking-widest">✅ SUDAH VERIFIKASI</h4>
                                <span class="px-2 py-0.5 bg-[#22AF85] text-white text-[9px] font-black rounded-full">{{ $queues['shipping_verified']->count() }}</span>
                            </div>
                            <div class="divide-y divide-gray-50 max-h-[300px] overflow-y-auto sidebar-scroll">
                                @forelse($queues['shipping_verified'] as $ship)
                                    <div class="p-4 hover:bg-gray-50 transition-all flex items-center justify-between group">
                                        <div class="space-y-0.5">
                                            <div class="text-[10px] font-black text-[#22AF85]">{{ $ship->spk_number }}</div>
                                            <div class="text-xs font-black text-gray-900">{{ $ship->customer_name }}</div>
                                            <div class="text-[8px] text-gray-500 font-bold uppercase tracking-tighter">
                                                {{ $ship->resi_pengiriman ?? 'Resi Belum Input' }} • {{ $ship->tanggal_pengiriman ? $ship->tanggal_pengiriman->format('d/m/Y') : 'Siap Kirim' }}
                                            </div>
                                        </div>
                                        <div class="px-3 py-1 bg-[#22AF85]/10 text-[#22AF85] text-[8px] font-black rounded-full uppercase">Siap / Terkirim</div>
                                    </div>
                                @empty
                                    <div class="p-8 text-center text-gray-300 text-[9px] font-black uppercase tracking-widest">Belum Ada Data Terverifikasi</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            let charts = {};
            
            const standardOptions = {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 1000, easing: 'easeOutQuart' },
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [5, 5], color: '#f1f1f1' }, ticks: { font: { weight: 'bold', size: 9 } } },
                    x: { grid: { display: false }, ticks: { font: { weight: 'bold', size: 9 } } }
                }
            };

            const updateOrInitChart = (id, type, data, options, key) => {
                const ctx = document.getElementById(id);
                if (!ctx) return;

                if (charts[key]) {
                    charts[key].data = data;
                    charts[key].update('active'); // Use active mode for smooth transition
                } else {
                    charts[key] = new Chart(ctx, { type, data, options });
                }
            };

            const processCharts = (payload = null) => {
                // QC Trends
                const trendsData = payload ? payload.qcTrends : @json($qcTrends);
                if (trendsData) {
                    updateOrInitChart('qcTrendsChart', 'line', {
                        labels: trendsData.labels,
                        datasets: [
                            {
                                label: 'Lolos',
                                data: trendsData.lolos,
                                borderColor: '#22AF85',
                                backgroundColor: 'rgba(34, 175, 133, 0.05)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4,
                                pointRadius: 3,
                                pointBackgroundColor: '#fff'
                            },
                            {
                                label: 'Reject',
                                data: trendsData.reject,
                                borderColor: '#FFC232',
                                backgroundColor: 'rgba(255, 194, 50, 0.05)',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                fill: false,
                                tension: 0.4,
                                pointRadius: 2,
                                pointBackgroundColor: '#fff'
                            }
                        ]
                    }, {
                        ...standardOptions,
                        plugins: { legend: { display: true, position: 'top', labels: { boxWidth: 8, font: { size: 8, weight: 'bold' } } } }
                    }, 'qc');
                }

                // QC Stats (Doughnut)
                const statsData = payload ? payload.qcStats : @json($qcStats);
                if (statsData) {
                    updateOrInitChart('qcStatsChart', 'doughnut', {
                        labels: statsData.labels,
                        datasets: [{
                            data: statsData.data,
                            backgroundColor: ['#22AF85', '#FFC232'],
                            borderRadius: 4,
                            spacing: 8
                        }]
                    }, {
                        ...standardOptions,
                        plugins: { legend: { display: true, position: 'bottom', labels: { boxWidth: 6, font: { size: 8, weight: 'bold' } } } },
                        cutout: '83%'
                    }, 'stats');
                }

                // Efficiency (Bar)
                const effData = payload ? payload.efficiency : @json($efficiencyStats);
                if (effData) {
                    updateOrInitChart('efficiencyChart', 'bar', {
                        labels: ['Throughput', 'Health'],
                        datasets: [{
                            data: [effData.total_throughput, effData.health_score],
                            backgroundColor: ['#22AF85', '#FFC232'],
                            borderRadius: 12
                        }]
                    }, standardOptions, 'efficiency');
                }
            };

            // Initial Load
            setTimeout(() => processCharts(), 200);

            // Reactive Hook
            Livewire.on('refreshCharts', (data) => {
                // In Livewire 3, event payloads are often the first argument or in d[0]
                const payload = Array.isArray(data) ? data[0] : data;
                processCharts(payload);
            });
        });
    </script>
    @endpush
</div>
