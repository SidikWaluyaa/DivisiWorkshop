@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* Complete Flatpickr Overrides for ultra-premium dashboard aesthetics */
        .flatpickr-calendar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(20px) !important;
            border: 1px solid rgba(255, 255, 255, 0.6) !important;
            border-radius: 24px !important;
            box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.1) !important;
            padding: 8px 6px !important;
            font-family: 'Inter', sans-serif !important;
            width: 320px !important;
            box-sizing: border-box !important;
            animation: fpFadeIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .flatpickr-days, .dayContainer {
            width: 307.875px !important;
            min-width: 307.875px !important;
            max-width: 307.875px !important;
        }
        @keyframes fpFadeIn {
            from { opacity: 0; transform: scale(0.95) translateY(10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .flatpickr-months {
            align-items: center !important;
            margin-bottom: 8px !important;
        }
        .flatpickr-months .flatpickr-prev-month, 
        .flatpickr-months .flatpickr-next-month {
            top: 15px !important;
            padding: 8px !important;
            border-radius: 12px !important;
            background: #f1f5f9 !important;
            color: #1e293b !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.2s ease !important;
        }
        .flatpickr-months .flatpickr-prev-month:hover, 
        .flatpickr-months .flatpickr-next-month:hover {
            background: #e2e8f0 !important;
            color: #22AF85 !important;
            transform: scale(1.05);
        }
        .flatpickr-current-month {
            font-size: 13px !important;
            font-weight: 800 !important;
            color: #1e293b !important;
        }
        .flatpickr-current-month select {
            font-weight: 800 !important;
            color: #1e293b !important;
        }
        .flatpickr-weekday {
            font-weight: 800 !important;
            font-size: 9px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.1em !important;
            color: #94a3b8 !important;
        }
        .flatpickr-day {
            border-radius: 12px !important;
            font-weight: 700 !important;
            font-size: 11px !important;
            color: #334155 !important;
            margin: 2px 0 !important;
            transition: all 0.15s ease !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .flatpickr-day:hover {
            background: #f1f5f9 !important;
            color: #1e293b !important;
        }
        .flatpickr-day.today {
            border: 2px solid #FFC232 !important;
            color: #1e293b !important;
        }
        .flatpickr-day.selected, 
        .flatpickr-day.startRange, 
        .flatpickr-day.endRange {
            background: linear-gradient(135deg, #22AF85 0%, #1d9d76 100%) !important;
            border-color: transparent !important;
            color: #ffffff !important;
            box-shadow: 0 10px 15px -3px rgba(34, 175, 133, 0.3) !important;
            border-radius: 12px !important;
        }
        .flatpickr-day.inRange {
            background: rgba(34, 175, 133, 0.08) !important;
            color: #22AF85 !important;
            border-radius: 0 !important;
            box-shadow: none !important;
        }
        .flatpickr-day.prevMonthDay, 
        .flatpickr-day.nextMonthDay {
            color: #cbd5e1 !important;
            opacity: 0.5 !important;
        }
    </style>
@endpush

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
        .kpi-card { transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); background: #ffffff !important; border: 1px solid rgba(241, 245, 249, 0.8) !important; color: #1e293b !important; }
        .kpi-card:hover { transform: translateY(-6px) !important; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important; }
        .kpi-card::before { display: none !important; } /* Disable global stat-card pulse ring overlay */
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

                        {{-- DECOUPLED FLATPICKR RANGE PICKER --}}
                        <div class="relative">
                            <button @click="$refs.rangeInput._flatpickr.open()" type="button"
                                    class="px-4 py-1.5 rounded-xl text-[9px] font-black transition-all uppercase tracking-widest flex items-center justify-center gap-2 cursor-pointer w-44 text-center border-none outline-none focus:outline-none focus:ring-0 focus:border-none ring-0 shadow-sm
                                    {{ $dateRange === 'custom' ? 'bg-[#22AF85] text-white shadow-md' : 'text-[#22AF85] hover:bg-[#22AF85]/5 bg-white' }}">
                                {{ $dateRange === 'custom' ? '📅 ' . \Carbon\Carbon::parse($startDate)->format('d M') . ' - ' . \Carbon\Carbon::parse($endDate)->format('d M') : '📅 KALENDER' }}
                            </button>

                            <div wire:ignore wire:key="flatpickr-hidden-container" class="hidden">
                                <input x-init="
                                    flatpickr($el, {
                                        mode: 'range',
                                        dateFormat: 'Y-m-d',
                                        defaultDate: ['{{ $startDate }}', '{{ $endDate }}'],
                                        positionElement: $el.parentElement.previousElementSibling, // Align relative to the visible button
                                        onChange: (selectedDates, dateStr, instance) => {
                                            if (selectedDates.length === 2) {
                                                let start = instance.formatDate(selectedDates[0], 'Y-m-d');
                                                let end = instance.formatDate(selectedDates[1], 'Y-m-d');
                                                $wire.set('startDate', start);
                                                $wire.set('endDate', end);
                                                $wire.set('dateRange', 'custom');
                                            }
                                        }
                                    });
                                    
                                    $watch('$wire.startDate', (value) => {
                                        if ($el._flatpickr && value) {
                                            $el._flatpickr.setDate([value, $wire.endDate], false);
                                        }
                                    });
                                    $watch('$wire.endDate', (value) => {
                                        if ($el._flatpickr && value) {
                                            $el._flatpickr.setDate([$wire.startDate, value], false);
                                        }
                                    });
                                " x-ref="rangeInput" type="text">
                            </div>
                        </div>
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



            {{-- Premium Tab Navigation --}}
            <div class="flex gap-2 p-2 bg-white/60 rounded-[1.5rem] shadow-lg border border-white glass-panel w-fit relative z-10 flex-wrap md:flex-nowrap">
                <button @click="activeTab = 'summary'" 
                        :class="activeTab === 'summary' ? 'bg-[#22AF85] text-white shadow-lg' : 'text-gray-500 hover:text-gray-700'"
                        class="px-8 py-2.5 rounded-[1rem] text-[10px] font-black uppercase tracking-wider transition-all">
                    📊 RINGKASAN
                </button>
                <button @click="activeTab = 'piutang_before'" 
                        :class="activeTab === 'piutang_before' ? 'bg-[#22AF85] text-white shadow-lg' : 'text-gray-500 hover:text-gray-700'"
                        class="px-8 py-2.5 rounded-[1rem] text-[10px] font-black uppercase tracking-wider transition-all flex items-center gap-2">
                    💸 PIUTANG BEFORE (BELUM SELESAI)
                    <span :class="activeTab === 'piutang_before' ? 'bg-white text-[#22AF85]' : 'bg-amber-100 text-amber-600'"
                          class="px-2 py-0.5 rounded-full text-[9px] font-black">
                        {{ count($this->piutangBeforeOrders) }}
                    </span>
                </button>
                <button @click="activeTab = 'piutang'" 
                        :class="activeTab === 'piutang' ? 'bg-[#22AF85] text-white shadow-lg' : 'text-gray-500 hover:text-gray-700'"
                        class="px-8 py-2.5 rounded-[1rem] text-[10px] font-black uppercase tracking-wider transition-all flex items-center gap-2">
                    💸 PIUTANG AFTER (SELESAI)
                    <span :class="activeTab === 'piutang' ? 'bg-white text-[#22AF85]' : 'bg-rose-100 text-rose-600'"
                          class="px-2 py-0.5 rounded-full text-[9px] font-black">
                        {{ count($this->piutangAfterOrders) }}
                    </span>
                </button>
                <button @click="activeTab = 'shoe_rack'" 
                        :class="activeTab === 'shoe_rack' ? 'bg-[#22AF85] text-white shadow-lg' : 'text-gray-500 hover:text-gray-700'"
                        class="px-8 py-2.5 rounded-[1rem] text-[10px] font-black uppercase tracking-wider transition-all flex items-center gap-2">
                    👟 SEPATU DI RAK
                    <span :class="activeTab === 'shoe_rack' ? 'bg-white text-[#22AF85]' : 'bg-emerald-100 text-emerald-600'"
                          class="px-2 py-0.5 rounded-full text-[9px] font-black">
                        {{ count($this->shoeRackOrders) }}
                    </span>
                </button>
                <button @click="activeTab = 'manifest_dashboard'" 
                        :class="activeTab === 'manifest_dashboard' ? 'bg-[#22AF85] text-white shadow-lg' : 'text-gray-500 hover:text-gray-700'"
                        class="px-8 py-2.5 rounded-[1rem] text-[10px] font-black uppercase tracking-wider transition-all flex items-center gap-2">
                    📋 MANIFEST LOGISTIK
                    <span :class="activeTab === 'manifest_dashboard' ? 'bg-white text-[#22AF85]' : 'bg-emerald-100 text-emerald-600'"
                          class="px-2 py-0.5 rounded-full text-[9px] font-black">
                        {{ count($this->manifestSummary['recent_manifests']) }}
                    </span>
                </button>
                <button @click="activeTab = 'sortir_dashboard'" 
                        :class="activeTab === 'sortir_dashboard' ? 'bg-[#22AF85] text-white shadow-lg' : 'text-gray-500 hover:text-gray-700'"
                        class="px-8 py-2.5 rounded-[1rem] text-[10px] font-black uppercase tracking-wider transition-all flex items-center gap-2">
                    👟 DATA SORTIR
                    <span :class="activeTab === 'sortir_dashboard' ? 'bg-white text-[#22AF85]' : 'bg-emerald-100 text-emerald-600'"
                          class="px-2 py-0.5 rounded-full text-[9px] font-black">
                        {{ count($this->sortirSummary['items']) }}
                    </span>
                </button>
                <button @click="activeTab = 'production_dashboard'" 
                        :class="activeTab === 'production_dashboard' ? 'bg-[#22AF85] text-white shadow-lg' : 'text-gray-500 hover:text-gray-700'"
                        class="px-8 py-2.5 rounded-[1rem] text-[10px] font-black uppercase tracking-wider transition-all flex items-center gap-2">
                    ⚙️ DATA PRODUKSI
                    <span :class="activeTab === 'production_dashboard' ? 'bg-white text-[#22AF85]' : 'bg-emerald-100 text-emerald-600'"
                          class="px-2 py-0.5 rounded-full text-[9px] font-black">
                        {{ count($this->productionSummary['items']) }}
                    </span>
                </button>
                <button @click="activeTab = 'qc_dashboard'" 
                        :class="activeTab === 'qc_dashboard' ? 'bg-[#22AF85] text-white shadow-lg' : 'text-gray-500 hover:text-gray-700'"
                        class="px-8 py-2.5 rounded-[1rem] text-[10px] font-black uppercase tracking-wider transition-all flex items-center gap-2">
                    🔍 DATA QC
                    <span :class="activeTab === 'qc_dashboard' ? 'bg-white text-[#22AF85]' : 'bg-emerald-100 text-emerald-600'"
                          class="px-2 py-0.5 rounded-full text-[9px] font-black">
                        {{ count($this->qcSummary['items']) }}
                    </span>
                </button>
            </div>

            {{-- Summary Grid --}}
            <div x-show="activeTab === 'summary'" x-transition:enter="transition ease-out duration-300" class="space-y-6">
                {{-- API Integration Developer Panel --}}
                <div class="bg-slate-900 rounded-[2rem] p-6 text-white relative overflow-hidden shadow-2xl border border-slate-800">
                    <div class="absolute -right-16 -bottom-16 text-9xl opacity-10 pointer-events-none">🔌</div>
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-400 text-[8px] font-black rounded uppercase tracking-wider">Active Service API</span>
                                <span class="px-2 py-0.5 bg-slate-800 text-slate-400 text-[8px] font-black rounded uppercase tracking-wider">v1.0</span>
                            </div>
                            <h4 class="text-sm font-black tracking-wide text-slate-100">🔌 API INTEGRATION: WAREHOUSE SUMMARY SYNC</h4>
                            <p class="text-slate-400 text-[9px] font-bold">Sinkronisasi data ringkasan gudang (inbound/outbound/metrics) secara real-time dengan external services.</p>
                        </div>
                        
                        <div class="flex flex-col items-end gap-2 w-full lg:w-auto">
                            <div class="flex items-center gap-3 w-full lg:w-auto" x-data="{ 
                                copied: false,
                                apiUrl: '{{ url('/api/v1/warehouse-summary') . '?api_key=' . config('app.dashboard_api_key') }}',
                                copyToClipboard() {
                                    try {
                                        this.$refs.apiSummaryInput.select();
                                        if (navigator.clipboard && navigator.clipboard.writeText) {
                                            navigator.clipboard.writeText(this.apiUrl);
                                        } else {
                                            document.execCommand('copy');
                                        }
                                        this.copied = true;
                                        setTimeout(() => this.copied = false, 2000);
                                    } catch (err) {
                                        console.error('Failed to copy: ', err);
                                    }
                                }
                            }">
                                <div class="relative flex-1 lg:flex-none">
                                    <input x-ref="apiSummaryInput" type="text" readonly :value="apiUrl" 
                                           class="w-full lg:w-[480px] bg-slate-800/80 border border-slate-700 rounded-xl px-4 py-2.5 text-[9px] font-mono text-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                </div>
                                <button @click="copyToClipboard()" class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 active:scale-95 text-slate-950 text-[10px] font-black rounded-xl transition-all shadow-lg shadow-emerald-500/20 flex items-center gap-2 shrink-0">
                                    <span x-show="!copied">📋 COPY URL</span>
                                    <span x-show="copied" x-cloak>✅ COPIED!</span>
                                </button>
                            </div>
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[8px] font-black text-slate-400 self-start lg:self-auto uppercase tracking-wider">
                                <span class="text-emerald-400">Parameter Opsional:</span>
                                <span>• start_date (YYYY-MM-DD)</span>
                                <span>• end_date (YYYY-MM-DD)</span>
                                <span>• search (String)</span>
                                <span class="text-slate-500 font-bold lowercase">Contoh: &start_date=2026-06-01&end_date=2026-06-07</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Operation Snapshot Queues (Header Compact Strip) --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 bg-white/60 p-4 rounded-[1.5rem] shadow-lg border border-white glass-panel relative z-10">
                    <div class="text-center py-2">
                        <div class="text-[9px] font-black text-gray-400 uppercase tracking-wider flex items-center justify-center gap-1">📥 SPK PENDING</div>
                        <div class="text-xl font-black text-gray-900 mt-0.5">{{ $stats['pending_reception'] ?? 0 }}</div>
                    </div>
                    <div class="text-center py-2 border-l border-gray-100">
                        <div class="text-[9px] font-black text-gray-400 uppercase tracking-wider flex items-center justify-center gap-1">✨ DI FINISH (NOT RACKED)</div>
                        <div class="text-xl font-black text-[#FFC232] mt-0.5">{{ $stats['finished_not_stored'] ?? 0 }}</div>
                    </div>
                    <div class="text-center py-2 border-l border-gray-100">
                        <div class="text-[9px] font-black text-gray-400 uppercase tracking-wider flex items-center justify-center gap-1">📦 DI RAK (STORED)</div>
                        <div class="text-xl font-black text-blue-600 mt-0.5">{{ $stats['stored_items'] ?? 0 }}</div>
                    </div>
                    <div class="text-center py-2 border-l border-gray-100">
                        <div class="text-[9px] font-black text-gray-400 uppercase tracking-wider flex items-center justify-center gap-1">🚀 SIAP DIAMBIL (READY)</div>
                        <div class="text-xl font-black text-[#22AF85] mt-0.5">{{ $stats['ready_for_pickup'] ?? 0 }}</div>
                    </div>
                </div>

                {{-- The Big 9 Scoreboard Cards Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    
                    {{-- Card 1: Sepatu Masuk Before --}}
                    <div class="bg-white rounded-[2rem] p-6 shadow-md border border-gray-100 kpi-card relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 text-8xl opacity-5 pointer-events-none group-hover:scale-110 transition-transform duration-300">📥</div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">1. Sepatu Masuk (Before)</span>
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('storage.dashboard.detail', ['type' => 'sepatu_masuk', 'start_date' => $startDate, 'end_date' => $endDate, 'search' => $search]) }}" target="_blank" class="text-[9px] font-black text-emerald-600 bg-emerald-50 hover:bg-emerald-100 px-2.5 py-1.5 rounded-lg transition-all active:scale-95 duration-200 cursor-pointer outline-none flex items-center justify-center">
                                    🔍 Detail
                                </a>
                                <div class="w-8 h-8 bg-emerald-50 rounded-xl flex items-center justify-center text-sm shadow-inner">📥</div>
                            </div>
                        </div>
                        <div class="text-3xl font-black text-gray-900">{{ $stats['incoming_day'] ?? 0 }} <span class="text-xs font-bold text-gray-400">Pasang</span></div>
                        <div class="text-[8px] font-black text-gray-400 uppercase tracking-wider mt-1">Diterima Fisik di Gudang</div>
                    </div>

                    {{-- Card 2: SPK Print / Otw Ws --}}
                    <div class="bg-white rounded-[2rem] p-6 shadow-md border border-gray-100 kpi-card relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 text-8xl opacity-5 pointer-events-none group-hover:scale-110 transition-transform duration-300">🚚</div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">2. SPK Print (Otw Ws)</span>
                            <div class="w-8 h-8 bg-blue-50 rounded-xl flex items-center justify-center text-sm shadow-inner">🚚</div>
                        </div>
                        <div class="text-3xl font-black text-blue-600">{{ $stats['spk_print'] ?? 0 }} <span class="text-xs font-bold text-blue-400">Pasang</span></div>
                        <div class="text-[8px] font-black text-gray-400 uppercase tracking-wider mt-1">Dikirim ke Reparasi / Manifest</div>
                    </div>

                    {{-- Card 3: SPK Tertahan / QC Reject --}}
                    <div class="bg-white rounded-[2rem] p-6 shadow-md border border-gray-100 kpi-card relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 text-8xl opacity-5 pointer-events-none group-hover:scale-110 transition-transform duration-300">⚠️</div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">3. SPK Tertahan (QC Reject)</span>
                            <div class="w-8 h-8 bg-rose-50 rounded-xl flex items-center justify-center text-sm shadow-inner text-rose-600">⚠️</div>
                        </div>
                        <div class="text-3xl font-black text-rose-600">{{ $stats['qc_reject'] ?? 0 }} <span class="text-xs font-bold text-rose-400">Pasang</span></div>
                        <div class="text-[8px] font-black text-gray-400 uppercase tracking-wider mt-1">Gagal Penerimaan Awal</div>
                    </div>

                    {{-- Card 4: After Masuk --}}
                    <div class="bg-white rounded-[2rem] p-6 shadow-md border border-gray-100 kpi-card relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 text-8xl opacity-5 pointer-events-none group-hover:scale-110 transition-transform duration-300">✨</div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">4. After Masuk</span>
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('storage.dashboard.detail', ['type' => 'after_masuk', 'start_date' => $startDate, 'end_date' => $endDate, 'search' => $search]) }}" target="_blank" class="text-[9px] font-black text-amber-600 bg-amber-50 hover:bg-amber-100 px-2.5 py-1.5 rounded-lg transition-all active:scale-95 duration-200 cursor-pointer outline-none flex items-center justify-center">
                                    🔍 Detail
                                </a>
                                <div class="w-8 h-8 bg-amber-50 rounded-xl flex items-center justify-center text-sm shadow-inner">✨</div>
                            </div>
                        </div>
                        <div class="text-3xl font-black text-gray-900">{{ $stats['after_masuk'] ?? 0 }} <span class="text-xs font-bold text-gray-400">Pasang</span></div>
                        <div class="text-[8px] font-black text-gray-400 uppercase tracking-wider mt-1">Selesai Reparasi Masuk Rak</div>
                    </div>

                    {{-- Card 5: Sepatu Keluar --}}
                    <div class="bg-white rounded-[2rem] p-6 shadow-md border border-gray-100 kpi-card relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 text-8xl opacity-5 pointer-events-none group-hover:scale-110 transition-transform duration-300">📤</div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">5. Sepatu Keluar</span>
                            <div class="w-8 h-8 bg-sky-50 rounded-xl flex items-center justify-center text-sm shadow-inner">📤</div>
                        </div>
                        <div class="text-3xl font-black text-sky-600">{{ $stats['sepatu_keluar'] ?? 0 }} <span class="text-xs font-bold text-sky-400">Pasang</span></div>
                        <div class="text-[8px] font-black text-gray-400 uppercase tracking-wider mt-1">Pengambilan & Kirim Lunas</div>
                    </div>

                    {{-- Card 6: Rak Inbound (Transit) --}}
                    <div class="bg-white rounded-[2rem] p-6 shadow-md border border-gray-100 kpi-card relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 text-8xl opacity-5 pointer-events-none group-hover:scale-110 transition-transform duration-300">📥</div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">6. Rak Inbound (Transit)</span>
                            <div class="w-8 h-8 bg-amber-50 rounded-xl flex items-center justify-center text-sm shadow-inner">📥</div>
                        </div>
                        <div class="text-3xl font-black text-amber-600">{{ $stats['inbound_inventory'] ?? 0 }} <span class="text-xs font-bold text-amber-400">Pasang</span></div>
                        <div class="text-[8px] font-black text-gray-400 uppercase tracking-wider mt-1">Fisik di Rak Penerimaan/Sebelum</div>
                    </div>

                    {{-- Card 7: Rak Finish (Selesai) --}}
                    <div class="bg-white rounded-[2rem] p-6 shadow-md border border-gray-100 kpi-card relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 text-8xl opacity-5 pointer-events-none group-hover:scale-110 transition-transform duration-300">📦</div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">7. Rak Finish (Selesai)</span>
                            <div class="w-8 h-8 bg-slate-50 rounded-xl flex items-center justify-center text-sm shadow-inner">📦</div>
                        </div>
                        <div class="text-3xl font-black text-slate-800">{{ $stats['finish_inventory'] ?? 0 }} <span class="text-xs font-bold text-slate-400">Pasang</span></div>
                        <div class="text-[8px] font-black text-gray-400 uppercase tracking-wider mt-1">Fisik di Rak Selesai/Siap Ambil</div>
                    </div>

                    {{-- Card 8: Clearance Rate (Before / Inbound Flow) --}}
                    <div class="bg-white rounded-[2rem] p-6 shadow-md border border-gray-100 kpi-card relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 text-8xl opacity-5 pointer-events-none group-hover:scale-110 transition-transform duration-300">⚖️</div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">8. Clearance Rate Inbound</span>
                            <div class="w-8 h-8 {{ $stats['clearance_rate_before'] >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }} rounded-xl flex items-center justify-center text-sm shadow-inner">⚖️</div>
                        </div>
                        <div class="text-3xl font-black {{ $stats['clearance_rate_before'] >= 0 ? 'text-emerald-600' : 'text-amber-600' }}">
                            {{ $stats['clearance_rate_before'] >= 0 ? '+' : '' }}{{ $stats['clearance_rate_before'] }}%
                        </div>
                        <div class="flex items-center gap-1.5 mt-1">
                            @if($stats['clearance_rate_before'] >= 0)
                                <span class="px-1.5 py-0.5 bg-emerald-50 text-emerald-600 rounded text-[7px] font-black uppercase">Ops Optimal</span>
                            @else
                                <span class="px-1.5 py-0.5 bg-amber-50 text-amber-600 rounded text-[7px] font-black uppercase">Antrean Clog</span>
                            @endif
                                <span class="text-[8px] font-black text-gray-400 uppercase tracking-wider">Inbound Flow Balance</span>
                        </div>
                    </div>

                    {{-- Card 9: Clearance Rate (After / Outbound Flow) --}}
                    <div class="bg-white rounded-[2rem] p-6 shadow-md border border-gray-100 kpi-card relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 text-8xl opacity-5 pointer-events-none group-hover:scale-110 transition-transform duration-300">🔄</div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">9. Clearance Rate Outbound</span>
                            <div class="w-8 h-8 {{ $stats['clearance_rate_after'] >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }} rounded-xl flex items-center justify-center text-sm shadow-inner">🔄</div>
                        </div>
                        <div class="text-3xl font-black {{ $stats['clearance_rate_after'] >= 0 ? 'text-emerald-600' : 'text-amber-600' }}">
                            {{ $stats['clearance_rate_after'] >= 0 ? '+' : '' }}{{ $stats['clearance_rate_after'] }}%
                        </div>
                        <div class="flex items-center gap-1.5 mt-1">
                            @if($stats['clearance_rate_after'] >= 0)
                                <span class="px-1.5 py-0.5 bg-emerald-50 text-emerald-600 rounded text-[7px] font-black uppercase">Ops Optimal</span>
                            @else
                                <span class="px-1.5 py-0.5 bg-amber-50 text-amber-600 rounded text-[7px] font-black uppercase">Rack Clog</span>
                            @endif
                                <span class="text-[8px] font-black text-gray-400 uppercase tracking-wider">Outbound Flow Balance</span>
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

                {{-- Flow Balance Analytics Section --}}
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                    {{-- Double-Curve Flow Line Chart --}}
                    <div class="xl:col-span-2 bg-white rounded-[2rem] p-6 shadow-lg border border-gray-100 relative overflow-hidden">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="text-xs font-black text-[#22AF85] uppercase tracking-widest flex items-center gap-2">📈 GRAFIK LAJU ARUS KESEIMBANGAN</h4>
                                <span class="text-[8px] font-bold text-gray-400 uppercase tracking-widest">Inbound (Masuk vs OTW WS) & Outbound (After vs Keluar)</span>
                            </div>
                        </div>
                        <div style="height: 220px;" wire:ignore><canvas id="dailyFlowChart"></canvas></div>
                    </div>
                    
                    {{-- Clearance Rates Bar Chart --}}
                    <div class="xl:col-span-1 bg-white rounded-[2rem] p-6 shadow-lg border border-gray-100 relative overflow-hidden">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="text-xs font-black text-indigo-600 uppercase tracking-widest flex items-center gap-2">📊 TINGKAT CLEARANCE (%)</h4>
                                <span class="text-[8px] font-bold text-gray-400 uppercase tracking-widest">Perbandingan Sebelum vs Sesudah (%)</span>
                            </div>
                        </div>
                        <div style="height: 220px;" wire:ignore><canvas id="clearanceComparisonChart"></canvas></div>
                    </div>
                </div>

                {{-- Daily Audit Table (Collapsible) --}}
                <div class="bg-white rounded-[2rem] p-6 shadow-lg border border-gray-100" x-data="{ expanded: false }">
                    <div class="flex items-center justify-between cursor-pointer" @click="expanded = !expanded">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-[#22AF85]/10 rounded-xl flex items-center justify-center text-lg">📋</div>
                            <div>
                                <h4 class="text-xs font-black text-gray-800 uppercase tracking-widest">Tabel Audit Arus Harian</h4>
                                <p class="text-gray-400 text-[10px] font-black uppercase tracking-wider mt-0.5">Lihat audit-trail lengkap harian dari seluruh variabel logistik</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg text-[9px] font-black uppercase tracking-wider transition-all">
                                <span x-show="!expanded">TAMPILKAN ({{ count($dailyFlow['table_rows']) }} Hari)</span>
                                <span x-show="expanded" x-cloak>SEMBUNYIKAN</span>
                            </span>
                            <span class="text-gray-400 transition-transform duration-300" :class="expanded ? 'rotate-180' : ''">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </span>
                        </div>
                    </div>

                    <div x-show="expanded" x-collapse x-cloak class="mt-6 pt-6 border-t border-gray-100 overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="py-3 text-[9px] font-black text-gray-400 uppercase tracking-wider">Tanggal</th>
                                    <th class="py-3 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Sepatu Masuk (Before)</th>
                                    <th class="py-3 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">SPK Print (Otw Ws)</th>
                                    <th class="py-3 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Clearance Before</th>
                                    <th class="py-3 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">SPK Tertahan (Reject)</th>
                                    <th class="py-3 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">After Masuk</th>
                                    <th class="py-3 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Sepatu Keluar</th>
                                    <th class="py-3 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Clearance Outbound</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($dailyFlow['table_rows'] as $row)
                                    <tr class="hover:bg-[#22AF85]/5 hover:text-[#22AF85] transition-all cursor-pointer duration-200" 
                                        wire:click="setSingleDate('{{ $row['full_date'] }}')" 
                                        title="Klik untuk mem-filter dashboard pada tanggal {{ $row['date'] }} saja">
                                        <td class="py-3 text-[10px] font-black text-gray-900">{{ $row['date'] }}</td>
                                        <td class="py-3 text-[10px] font-bold text-gray-700 text-center">{{ $row['sepatu_masuk'] }}</td>
                                        <td class="py-3 text-[10px] font-bold text-gray-700 text-center">{{ $row['spk_otw'] }}</td>
                                        <td class="py-3 text-center">
                                            <span class="px-2 py-0.5 rounded-lg text-[9px] font-black {{ $row['clearance_before'] >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                                {{ $row['clearance_before'] >= 0 ? '+' : '' }}{{ $row['clearance_before'] }}%
                                            </span>
                                        </td>
                                        <td class="py-3 text-[10px] font-bold text-rose-500 text-center">{{ $row['qc_reject'] }}</td>
                                        <td class="py-3 text-[10px] font-bold text-gray-700 text-center">{{ $row['after_masuk'] }}</td>
                                        <td class="py-3 text-[10px] font-bold text-gray-700 text-center">{{ $row['sepatu_keluar'] }}</td>
                                        <td class="py-3 text-center">
                                            <span class="px-2 py-0.5 rounded-lg text-[9px] font-black {{ $row['clearance_after'] >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                                {{ $row['clearance_after'] >= 0 ? '+' : '' }}{{ $row['clearance_after'] }}%
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="py-8 text-center text-gray-400 text-[10px] font-black uppercase opacity-40">Tidak ada data untuk periode ini</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Metrics Grid Compact (2 Columns) --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white rounded-[2rem] p-6 shadow-lg border border-gray-100">
                        <h4 class="text-[10px] font-black text-[#22AF85] mb-4 flex items-center gap-2 uppercase tracking-widest">📈 TREN PERFORMA QC</h4>
                        <div style="height: 200px;" wire:ignore><canvas id="qcTrendsChart"></canvas></div>
                    </div>
                    <div class="bg-white rounded-[2rem] p-6 shadow-lg border border-gray-100">
                        <h4 class="text-[10px] font-black text-gray-400 mb-4 flex items-center gap-2 uppercase tracking-widest">📊 KOMPOSISI HASIL QC</h4>
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

            {{-- Manifest Logistik Dashboard Grid --}}
            <div x-show="activeTab === 'manifest_dashboard'" x-transition:enter="transition ease-out duration-300" x-cloak class="space-y-6">
                {{-- API Integration Developer Panel --}}
                <div class="bg-slate-900 rounded-[2rem] p-6 text-white relative overflow-hidden shadow-2xl border border-slate-800">
                    <div class="absolute -right-16 -bottom-16 text-9xl opacity-10 pointer-events-none">🔌</div>
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-400 text-[8px] font-black rounded uppercase tracking-wider">Active Service API</span>
                                <span class="px-2 py-0.5 bg-slate-800 text-slate-400 text-[8px] font-black rounded uppercase tracking-wider">v1.0</span>
                            </div>
                            <h4 class="text-sm font-black tracking-wide text-slate-100">🔌 API INTEGRATION: WAREHOUSE MANIFEST SUMMARY SYNC</h4>
                            <p class="text-slate-400 text-[9px] font-bold">Sinkronisasi data ringkasan manifest logistik secara real-time dengan external services.</p>
                        </div>
                        
                        <div class="flex flex-col items-end gap-2 w-full lg:w-auto">
                            <div class="flex items-center gap-3 w-full lg:w-auto" x-data="{ 
                                copied: false,
                                apiUrl: '{{ url('/api/v1/warehouse-manifest-summary') . '?api_key=' . config('app.dashboard_api_key') }}',
                                copyToClipboard() {
                                    try {
                                        this.$refs.apiManifestInput.select();
                                        if (navigator.clipboard && navigator.clipboard.writeText) {
                                            navigator.clipboard.writeText(this.apiUrl);
                                        } else {
                                            document.execCommand('copy');
                                        }
                                        this.copied = true;
                                        setTimeout(() => this.copied = false, 2000);
                                    } catch (err) {
                                        console.error('Failed to copy: ', err);
                                    }
                                }
                            }">
                                <div class="relative flex-1 lg:flex-none">
                                    <input x-ref="apiManifestInput" type="text" readonly :value="apiUrl" 
                                           class="w-full lg:w-[480px] bg-slate-800/80 border border-slate-700 rounded-xl px-4 py-2.5 text-[9px] font-mono text-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                </div>
                                <button @click="copyToClipboard()" class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 active:scale-95 text-slate-950 text-[10px] font-black rounded-xl transition-all shadow-lg shadow-emerald-500/20 flex items-center gap-2 shrink-0">
                                    <span x-show="!copied">📋 COPY URL</span>
                                    <span x-show="copied" x-cloak>✅ COPIED!</span>
                                </button>
                            </div>
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[8px] font-black text-slate-400 self-start lg:self-auto uppercase tracking-wider">
                                <span class="text-emerald-400">Parameter Opsional:</span>
                                <span>• start_date (YYYY-MM-DD)</span>
                                <span>• end_date (YYYY-MM-DD)</span>
                                <span>• search (String)</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Metric Scoreboard Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-[2rem] p-6 shadow-md border border-gray-100 kpi-card relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 text-8xl opacity-5 pointer-events-none group-hover:scale-110 transition-transform duration-300">📦</div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Total Manifest Terkirim</span>
                            <div class="w-8 h-8 bg-blue-50 rounded-xl flex items-center justify-center text-sm shadow-inner">📦</div>
                        </div>
                        <div class="text-3xl font-black text-gray-900">{{ $this->manifestSummary['metrics']['total_manifests_sent'] ?? 0 }} <span class="text-xs font-bold text-gray-400">Manifest</span></div>
                        <div class="text-[8px] font-black text-gray-400 uppercase tracking-wider mt-1">
                            Diterima: {{ $this->manifestSummary['metrics']['total_manifests_received'] ?? 0 }}
                        </div>
                    </div>

                    <div class="bg-white rounded-[2rem] p-6 shadow-md border border-gray-100 kpi-card relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 text-8xl opacity-5 pointer-events-none group-hover:scale-110 transition-transform duration-300">👟</div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Total SPK / Sepatu Terkirim</span>
                            <div class="w-8 h-8 bg-emerald-50 rounded-xl flex items-center justify-center text-sm shadow-inner text-[#22AF85]">👟</div>
                        </div>
                        <div class="text-3xl font-black text-[#22AF85]">{{ $this->manifestSummary['metrics']['total_spk_sent'] ?? 0 }} <span class="text-xs font-bold text-[#22AF85]/60">Pasang</span></div>
                        <div class="text-[8px] font-black text-gray-400 uppercase tracking-wider mt-1">
                            Rerata: {{ $this->manifestSummary['metrics']['total_manifests_sent'] > 0 ? round($this->manifestSummary['metrics']['total_spk_sent'] / max(1, $this->manifestSummary['metrics']['total_manifests_sent']), 1) : 0 }} Pasang / Manifest
                        </div>
                    </div>

                    <div class="bg-white rounded-[2rem] p-6 shadow-md border border-gray-100 kpi-card relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 text-8xl opacity-5 pointer-events-none group-hover:scale-110 transition-transform duration-300">💰</div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Total Jasa Logistik</span>
                            <div class="w-8 h-8 bg-amber-50 rounded-xl flex items-center justify-center text-sm shadow-inner text-[#FFC232]">💰</div>
                        </div>
                        <div class="text-3xl font-black text-[#FFC232]">{{ $this->manifestSummary['metrics']['total_services_count'] ?? 0 }} <span class="text-xs font-bold text-amber-500">Jasa</span></div>
                        <div class="text-[8px] font-black text-gray-400 uppercase tracking-wider mt-1">
                            Rerata Jasa: {{ $this->manifestSummary['metrics']['average_services_per_shoe'] ?? 0 }} Jasa / Sepatu
                        </div>
                    </div>
                </div>

                {{-- Interactive Charts --}}
                <div class="grid grid-cols-1 gap-6">
                    <div class="bg-white rounded-[2rem] p-6 shadow-lg border border-gray-100 relative overflow-hidden">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="text-xs font-black text-[#22AF85] uppercase tracking-widest flex items-center gap-2">📈 GRAFIK TREN HARIAN LOGISTIK & TOTAL JASA</h4>
                                <span class="text-[8px] font-bold text-gray-400 uppercase tracking-widest">Laju Sepatu yang Dikirim via Manifest vs Total Jumlah Jasa (Layanan)</span>
                            </div>
                        </div>
                        <div style="height: 280px;" wire:ignore><canvas id="manifestTrendsChart"></canvas></div>
                    </div>
                </div>

                {{-- Recent Manifests Datatable --}}
                <div class="bg-white rounded-[2rem] p-6 shadow-lg border border-gray-100 overflow-hidden">
                    <div class="px-4 py-4 border-b border-gray-50 flex justify-between items-center bg-white">
                        <div>
                            <h2 class="text-sm font-black text-gray-800 uppercase tracking-widest">Riwayat Manifest Logistik (Periode Ini)</h2>
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mt-0.5">Daftar Manifest dan Valuasi Jasa di Dalamnya</p>
                        </div>
                        <div class="text-[9px] font-black text-gray-400 uppercase tracking-wider">Total: {{ count($this->manifestSummary['recent_manifests']) }} Records</div>
                    </div>
                    
                    <div class="overflow-x-auto mt-4">
                        <table class="min-w-full divide-y divide-gray-100 text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="px-6 py-4 text-[9px] font-black text-gray-400 uppercase tracking-wider">No. Manifest</th>
                                    <th class="px-6 py-4 text-[9px] font-black text-gray-400 uppercase tracking-wider">Pengirim / Tanggal</th>
                                    <th class="px-6 py-4 text-[9px] font-black text-gray-400 uppercase tracking-wider">Penerima</th>
                                    <th class="px-6 py-4 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Batch Size</th>
                                    <th class="px-6 py-4 text-[9px] font-black text-gray-400 uppercase tracking-wider text-right">Jumlah Jasa</th>
                                    <th class="px-6 py-4 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($this->manifestSummary['recent_manifests'] as $manifest)
                                    <tr class="hover:bg-[#22AF85]/[0.02] transition-colors group">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('manifest.show', $manifest['id']) }}" class="text-xs font-black text-[#22AF85] tracking-tight hover:underline">
                                                {{ $manifest['manifest_number'] }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-xs font-black text-gray-800">{{ $manifest['dispatcher_name'] }}</div>
                                            <div class="text-[8px] text-gray-400 font-bold uppercase tracking-wider">{{ $manifest['dispatched_at_formatted'] }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs font-black text-gray-700">
                                            {{ $manifest['receiver_name'] ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-xs font-black">
                                            {{ $manifest['work_orders_count'] }} Pasang
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-black text-gray-900">
                                            {{ $manifest['total_services_count'] }} Jasa
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if($manifest['status'] === 'SENT')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[8px] font-black bg-blue-50 text-blue-600 border border-blue-100 uppercase tracking-wider">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600 mr-1.5 animate-pulse"></span>
                                                    Transit
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[8px] font-black bg-emerald-50 text-[#22AF85] border border-emerald-100 uppercase tracking-wider">
                                                    Diterima
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-300 text-[10px] font-black uppercase tracking-widest">
                                            Tidak Ada Manifest Terkirim Pada Periode Ini
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Piutang Before Grid --}}
            <div x-show="activeTab === 'piutang_before'" x-transition:enter="transition ease-out duration-300" x-cloak class="space-y-6">
                {{-- KPI Header Card --}}
                <div class="bg-white rounded-[2rem] p-8 shadow-lg border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 text-9xl opacity-5 pointer-events-none">💸</div>
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center text-2xl shadow-inner">💸</div>
                        <div>
                            <h3 class="text-xl font-black text-gray-900">Piutang Before <span class="text-gray-400">(Pengerjaan Belum Selesai)</span></h3>
                            <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mt-1">Daftar SPK Belum Selesai yang Belum Melakukan Pelunasan Pembayaran</p>
                        </div>
                    </div>
                    
                    {{-- Toggle & Total Piutang Card --}}
                    <div class="flex flex-col sm:flex-row items-center gap-4 shrink-0 w-full md:w-auto justify-end">
                        <div class="flex items-center gap-3 bg-gray-50 px-4 py-3 rounded-2xl border border-gray-100/80 shadow-sm shrink-0">
                            <div class="space-y-0.5">
                                <span class="text-[9px] font-black text-gray-800 uppercase tracking-widest block">Semua Waktu</span>
                                <span class="text-[7px] font-bold text-gray-400 uppercase tracking-wider block">
                                    {{ $ignorePiutangDateFilter ? 'Tanpa Batas Tanggal' : 'Sesuai Range Picker' }}
                                </span>
                            </div>
                            <button wire:click="$toggle('ignorePiutangDateFilter')" type="button"
                                    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none 
                                    {{ $ignorePiutangDateFilter ? 'bg-[#22AF85]' : 'bg-gray-300' }}">
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out
                                      {{ $ignorePiutangDateFilter ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </div>
                        
                        <div class="px-8 py-4 bg-amber-50/50 border border-amber-100/50 rounded-2xl text-center md:text-right shrink-0">
                            <span class="text-[9px] font-black text-amber-500 uppercase tracking-widest block mb-1">TOTAL OUTSTANDING PIUTANG</span>
                            <span class="text-3xl font-black text-amber-600 font-display">Rp {{ number_format($this->totalPiutangBeforeAmount, 0, ',', '.') }}</span>
                            <span class="text-[8px] font-bold text-gray-400 block mt-1">Dari {{ $this->piutangBeforeOrders->sum(fn($inv) => $inv->workOrders->count()) }} SPK Aktif ({{ count($this->piutangBeforeOrders) }} Invoice)</span>
                        </div>
                    </div>
                </div>

                {{-- API Integration Developer Panel --}}
                <div class="bg-slate-900 rounded-[2rem] p-6 text-white relative overflow-hidden shadow-2xl border border-slate-800">
                    <div class="absolute -right-16 -bottom-16 text-9xl opacity-10 pointer-events-none">🔌</div>
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-400 text-[8px] font-black rounded uppercase tracking-wider">Active Service API</span>
                                <span class="px-2 py-0.5 bg-slate-800 text-slate-400 text-[8px] font-black rounded uppercase tracking-wider">v1.0</span>
                            </div>
                            <h4 class="text-sm font-black tracking-wide text-slate-100">🔌 API INTEGRATION: WAREHOUSE PIUTANG BEFORE SYNC</h4>
                            <p class="text-slate-400 text-[9px] font-bold">Sinkronisasi data piutang invoice gudang (belum selesai) secara real-time dengan external services.</p>
                        </div>
                        
                        <div class="flex flex-col items-end gap-2 w-full lg:w-auto">
                            <div class="flex items-center gap-3 w-full lg:w-auto" x-data="{ 
                                copied: false,
                                apiUrl: '{{ url('/api/v1/warehouse-piutang-before-sync') . '?api_key=' . config('app.dashboard_api_key') }}',
                                copyToClipboard() {
                                    try {
                                        this.$refs.apiBeforeInput.select();
                                        if (navigator.clipboard && navigator.clipboard.writeText) {
                                            navigator.clipboard.writeText(this.apiUrl);
                                        } else {
                                            document.execCommand('copy');
                                        }
                                        this.copied = true;
                                        setTimeout(() => this.copied = false, 2000);
                                    } catch (err) {
                                        console.error('Failed to copy: ', err);
                                    }
                                }
                            }">
                                <div class="relative flex-1 lg:flex-none">
                                    <input x-ref="apiBeforeInput" type="text" readonly :value="apiUrl" 
                                           class="w-full lg:w-[480px] bg-slate-800/80 border border-slate-700 rounded-xl px-4 py-2.5 text-[9px] font-mono text-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                </div>
                                <button @click="copyToClipboard()" class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 active:scale-95 text-slate-950 text-[10px] font-black rounded-xl transition-all shadow-lg shadow-emerald-500/20 flex items-center gap-2 shrink-0">
                                    <span x-show="!copied">📋 COPY URL</span>
                                    <span x-show="copied" x-cloak>✅ COPIED!</span>
                                </button>
                            </div>
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[8px] font-black text-slate-400 self-start lg:self-auto uppercase tracking-wider">
                                <span class="text-emerald-400">Parameter Opsional:</span>
                                <span>• start_date (YYYY-MM-DD)</span>
                                <span>• end_date (YYYY-MM-DD)</span>
                                <span class="text-slate-500 font-bold lowercase">Contoh: &start_date=2026-06-01&end_date=2026-06-07</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Piutang Table --}}
                <div class="bg-white rounded-[2rem] p-8 shadow-lg border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider">Invoice / SPK</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider">Pelanggan</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider">Detail Sepatu</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider">Layanan / Jasa</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider text-right">Outstanding</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Status</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($this->piutangBeforeOrders as $invoice)
                                    <tr class="hover:bg-amber-50/10 transition-all duration-200">
                                        {{-- Invoice / SPK --}}
                                        <td class="py-4">
                                            <div class="text-[10px] font-black text-[#22AF85]">{{ $invoice->invoice_number }}</div>
                                            <div class="text-[8px] font-bold text-gray-400 mt-0.5">
                                                SPK: {{ $invoice->workOrders->pluck('spk_number')->implode(', ') }}
                                            </div>
                                        </td>
                                        
                                        {{-- Pelanggan & Phone --}}
                                        <td class="py-4">
                                            <div class="text-xs font-black text-gray-900">{{ $invoice->customer->name ?? 'N/A' }}</div>
                                            <div class="text-[9px] font-bold text-gray-400 mt-0.5">{{ $invoice->customer->phone ?? 'N/A' }}</div>
                                        </td>
                                        
                                        {{-- Detail Sepatu --}}
                                        <td class="py-4">
                                            <div class="space-y-2">
                                                @foreach($invoice->workOrders as $wo)
                                                    <div class="text-xs font-bold text-gray-700">
                                                        • {{ $wo->shoe_brand ?: '-' }} {{ $wo->shoe_type ?: '' }}
                                                        <span class="text-[8px] font-black text-gray-400 uppercase tracking-wider block ml-2">
                                                            {{ $wo->shoe_color ?: 'Warna N/A' }} • Size {{ $wo->shoe_size ?: 'N/A' }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        
                                        {{-- Jasa --}}
                                        <td class="py-4 max-w-[200px]">
                                            @php
                                                $uniqueServices = $invoice->workOrders->flatMap(function($wo) {
                                                    return $wo->workOrderServices->map(function($svc) {
                                                        return strtoupper($svc->custom_service_name ?: ($svc->service->name ?? 'Jasa'));
                                                    });
                                                })->unique();
                                            @endphp
                                            @foreach($uniqueServices as $serviceName)
                                                <span class="px-2.5 py-1 bg-slate-900 text-white text-[8px] font-black rounded-lg inline-block mr-1 mb-1 tracking-tight shadow-sm">
                                                    {{ $serviceName }}
                                                </span>
                                            @endforeach
                                            @if($uniqueServices->isEmpty())
                                                <span class="text-[9px] font-bold text-gray-300 italic">Tidak ada jasa</span>
                                            @endif
                                        </td>
                                        
                                        {{-- Outstanding --}}
                                        <td class="py-4 text-right">
                                            <div class="text-sm font-black text-[#FFC232] font-display">Rp {{ number_format($invoice->remaining_balance, 0, ',', '.') }}</div>
                                            <div class="text-[8px] font-bold text-gray-400 mt-0.5">Lunas: Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</div>
                                        </td>
                                        
                                        {{-- Status --}}
                                        <td class="py-4 text-center">
                                            <span class="px-3 py-1.5 rounded-xl text-[8px] font-black uppercase tracking-wider
                                                {{ $invoice->status === 'Belum Bayar' ? 'bg-red-50 text-red-600 border border-red-100' : 'bg-amber-50 text-amber-600 border border-amber-100' }}">
                                                {{ $invoice->status }}
                                            </span>
                                        </td>
                                        
                                        {{-- Action (WhatsApp) --}}
                                        <td class="py-4 text-center">
                                            @php
                                                $spkList = $invoice->workOrders->pluck('spk_number')->implode(', ');
                                                $shoeList = $invoice->workOrders->map(fn($wo) => ($wo->shoe_brand ?: '') . ' ' . ($wo->shoe_type ?: ''))->filter()->implode(', ');
                                                
                                                $waMessage = "Halo " . ($invoice->customer->name ?? 'Pelanggan') . ",\n\nSepatu (" . $shoeList . ") Anda dengan No SPK *" . $spkList . "* / Invoice *" . $invoice->invoice_number . "* saat ini SEDANG dalam pengerjaan.\n\nSisa pelunasan Anda adalah sebesar *Rp " . number_format($invoice->remaining_balance, 0, ',', '.') . "*.\n\nTerima kasih atas kepercayaan Anda pada workshop kami! 🙏";
                                                $waUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $invoice->customer->phone ?? '') . "?text=" . urlencode($waMessage);
                                            @endphp
                                            <a href="{{ $waUrl }}" target="_blank" 
                                               class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-[9px] font-black rounded-xl transition-all shadow-md shadow-emerald-500/20 hover:scale-105">
                                                <span>💬 WHATSAPP</span>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-12 text-center text-gray-400 text-[10px] font-black uppercase opacity-40">
                                            🎉 Hore! Tidak ada piutang sebelum pengerjaan selesai.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Piutang After Grid --}}
            <div x-show="activeTab === 'piutang'" x-transition:enter="transition ease-out duration-300" x-cloak class="space-y-6">
                {{-- KPI Header Card --}}
                <div class="bg-white rounded-[2rem] p-8 shadow-lg border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 text-9xl opacity-5 pointer-events-none">💸</div>
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-rose-50 rounded-2xl flex items-center justify-center text-2xl shadow-inner">💸</div>
                        <div>
                            <h3 class="text-xl font-black text-gray-900">Piutang After <span class="text-gray-400">(Pengerjaan Selesai)</span></h3>
                            <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mt-1">Daftar SPK Selesai yang Belum Melakukan Pelunasan Pembayaran</p>
                        </div>
                    </div>
                    
                    {{-- Toggle & Total Piutang Card --}}
                    <div class="flex flex-col sm:flex-row items-center gap-4 shrink-0 w-full md:w-auto justify-end">
                        <div class="flex items-center gap-3 bg-gray-50 px-4 py-3 rounded-2xl border border-gray-100/80 shadow-sm shrink-0">
                            <div class="space-y-0.5">
                                <span class="text-[9px] font-black text-gray-800 uppercase tracking-widest block">Semua Waktu</span>
                                <span class="text-[7px] font-bold text-gray-400 uppercase tracking-wider block">
                                    {{ $ignorePiutangDateFilter ? 'Tanpa Batas Tanggal' : 'Sesuai Range Picker' }}
                                </span>
                            </div>
                            <button wire:click="$toggle('ignorePiutangDateFilter')" type="button"
                                    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none 
                                    {{ $ignorePiutangDateFilter ? 'bg-[#22AF85]' : 'bg-gray-300' }}">
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out
                                      {{ $ignorePiutangDateFilter ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </div>
                        
                        <div class="px-8 py-4 bg-rose-50/50 border border-rose-100/50 rounded-2xl text-center md:text-right shrink-0">
                            <span class="text-[9px] font-black text-rose-500 uppercase tracking-widest block mb-1">TOTAL OUTSTANDING PIUTANG</span>
                            <span class="text-3xl font-black text-rose-600 font-display">Rp {{ number_format($this->totalPiutangAmount, 0, ',', '.') }}</span>
                            <span class="text-[8px] font-bold text-gray-400 block mt-1">Dari {{ $this->piutangAfterOrders->sum(fn($inv) => $inv->workOrders->count()) }} SPK Aktif ({{ count($this->piutangAfterOrders) }} Invoice)</span>
                        </div>
                    </div>
                </div>

                {{-- API Integration Developer Panel --}}
                <div class="bg-slate-900 rounded-[2rem] p-6 text-white relative overflow-hidden shadow-2xl border border-slate-800">
                    <div class="absolute -right-16 -bottom-16 text-9xl opacity-10 pointer-events-none">🔌</div>
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-400 text-[8px] font-black rounded uppercase tracking-wider">Active Service API</span>
                                <span class="px-2 py-0.5 bg-slate-800 text-slate-400 text-[8px] font-black rounded uppercase tracking-wider">v1.0</span>
                            </div>
                            <h4 class="text-sm font-black tracking-wide text-slate-100">🔌 API INTEGRATION: WAREHOUSE PIUTANG SYNC</h4>
                            <p class="text-slate-400 text-[9px] font-bold">Sinkronisasi data piutang invoice gudang secara real-time dengan external services.</p>
                        </div>
                        
                        <div class="flex flex-col items-end gap-2 w-full lg:w-auto">
                            <div class="flex items-center gap-3 w-full lg:w-auto" x-data="{ 
                                copied: false,
                                apiUrl: '{{ url('/api/v1/warehouse-piutang-sync') . '?api_key=' . config('app.dashboard_api_key') }}',
                                copyToClipboard() {
                                    try {
                                        this.$refs.apiInput.select();
                                        if (navigator.clipboard && navigator.clipboard.writeText) {
                                            navigator.clipboard.writeText(this.apiUrl);
                                        } else {
                                            document.execCommand('copy');
                                        }
                                        this.copied = true;
                                        setTimeout(() => this.copied = false, 2000);
                                    } catch (err) {
                                        console.error('Failed to copy: ', err);
                                    }
                                }
                            }">
                                <div class="relative flex-1 lg:flex-none">
                                    <input x-ref="apiInput" type="text" readonly :value="apiUrl" 
                                           class="w-full lg:w-[480px] bg-slate-800/80 border border-slate-700 rounded-xl px-4 py-2.5 text-[9px] font-mono text-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                </div>
                                <button @click="copyToClipboard()" class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 active:scale-95 text-slate-950 text-[10px] font-black rounded-xl transition-all shadow-lg shadow-emerald-500/20 flex items-center gap-2 shrink-0">
                                    <span x-show="!copied">📋 COPY URL</span>
                                    <span x-show="copied" x-cloak>✅ COPIED!</span>
                                </button>
                            </div>
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[8px] font-black text-slate-400 self-start lg:self-auto uppercase tracking-wider">
                                <span class="text-emerald-400">Parameter Opsional:</span>
                                <span>• start_date (YYYY-MM-DD)</span>
                                <span>• end_date (YYYY-MM-DD)</span>
                                <span class="text-slate-500 font-bold lowercase">Contoh: &start_date=2026-06-01&end_date=2026-06-07</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Piutang Table --}}
                <div class="bg-white rounded-[2rem] p-8 shadow-lg border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider">Invoice / SPK</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider">Pelanggan</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider">Detail Sepatu</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider">Layanan / Jasa</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider text-right">Outstanding</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Status</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($this->piutangAfterOrders as $invoice)
                                    <tr class="hover:bg-rose-50/10 transition-all duration-200">
                                        {{-- Invoice / SPK --}}
                                        <td class="py-4">
                                            <div class="text-[10px] font-black text-[#22AF85]">{{ $invoice->invoice_number }}</div>
                                            <div class="text-[8px] font-bold text-gray-400 mt-0.5">
                                                SPK: {{ $invoice->workOrders->pluck('spk_number')->implode(', ') }}
                                            </div>
                                        </td>
                                        
                                        {{-- Pelanggan & Phone --}}
                                        <td class="py-4">
                                            <div class="text-xs font-black text-gray-900">{{ $invoice->customer->name ?? 'N/A' }}</div>
                                            <div class="text-[9px] font-bold text-gray-400 mt-0.5">{{ $invoice->customer->phone ?? 'N/A' }}</div>
                                        </td>
                                        
                                        {{-- Detail Sepatu --}}
                                        <td class="py-4">
                                            <div class="space-y-2">
                                                @foreach($invoice->workOrders as $wo)
                                                    <div class="text-xs font-bold text-gray-700">
                                                        • {{ $wo->shoe_brand ?: '-' }} {{ $wo->shoe_type ?: '' }}
                                                        <span class="text-[8px] font-black text-gray-400 uppercase tracking-wider block ml-2">
                                                            {{ $wo->shoe_color ?: 'Warna N/A' }} • Size {{ $wo->shoe_size ?: 'N/A' }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        
                                        {{-- Jasa --}}
                                        <td class="py-4 max-w-[200px]">
                                            @php
                                                $uniqueServices = $invoice->workOrders->flatMap(function($wo) {
                                                    return $wo->workOrderServices->map(function($svc) {
                                                        return strtoupper($svc->custom_service_name ?: ($svc->service->name ?? 'Jasa'));
                                                    });
                                                })->unique();
                                            @endphp
                                            @foreach($uniqueServices as $serviceName)
                                                <span class="px-2.5 py-1 bg-slate-900 text-white text-[8px] font-black rounded-lg inline-block mr-1 mb-1 tracking-tight shadow-sm">
                                                    {{ $serviceName }}
                                                </span>
                                            @endforeach
                                            @if($uniqueServices->isEmpty())
                                                <span class="text-[9px] font-bold text-gray-300 italic">Tidak ada jasa</span>
                                            @endif
                                        </td>
                                        
                                        {{-- Outstanding --}}
                                        <td class="py-4 text-right">
                                            <div class="text-sm font-black text-rose-600 font-display">Rp {{ number_format($invoice->remaining_balance, 0, ',', '.') }}</div>
                                            <div class="text-[8px] font-bold text-gray-400 mt-0.5">Lunas: Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</div>
                                        </td>
                                        
                                        {{-- Status --}}
                                        <td class="py-4 text-center">
                                            <span class="px-3 py-1.5 rounded-xl text-[8px] font-black uppercase tracking-wider
                                                {{ $invoice->status === 'Belum Bayar' ? 'bg-red-50 text-red-600 border border-red-100' : 'bg-amber-50 text-amber-600 border border-amber-100' }}">
                                                {{ $invoice->status }}
                                            </span>
                                        </td>
                                        
                                        {{-- Action (WhatsApp) --}}
                                        <td class="py-4 text-center">
                                            @php
                                                $spkList = $invoice->workOrders->pluck('spk_number')->implode(', ');
                                                $shoeList = $invoice->workOrders->map(fn($wo) => ($wo->shoe_brand ?: '') . ' ' . ($wo->shoe_type ?: ''))->filter()->implode(', ');
                                                
                                                $waMessage = "Halo " . ($invoice->customer->name ?? 'Pelanggan') . ",\n\nSepatu (" . $shoeList . ") Anda dengan No SPK *" . $spkList . "* / Invoice *" . $invoice->invoice_number . "* telah SELESAI dikerjakan dan siap diambil.\n\nSisa pelunasan Anda adalah sebesar *Rp " . number_format($invoice->remaining_balance, 0, ',', '.') . "*.\n\nTerima kasih atas kepercayaan Anda pada workshop kami! 🙏";
                                                $waUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $invoice->customer->phone ?? '') . "?text=" . urlencode($waMessage);
                                            @endphp
                                            <a href="{{ $waUrl }}" target="_blank" 
                                               class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-[9px] font-black rounded-xl transition-all shadow-md shadow-emerald-500/20 hover:scale-105">
                                                <span>💬 WHATSAPP</span>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-12 text-center text-gray-400 text-[10px] font-black uppercase opacity-40">
                                            🎉 Hore! Tidak ada piutang setelah pengerjaan selesai.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Shoe Rack Grid --}}
            <div x-show="activeTab === 'shoe_rack'" x-transition:enter="transition ease-out duration-300" x-cloak class="space-y-6">
                {{-- KPI Header Card --}}
                <div class="bg-white rounded-[2rem] p-8 shadow-lg border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 text-9xl opacity-5 pointer-events-none">👟</div>
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-2xl shadow-inner">👟</div>
                        <div>
                            <h3 class="text-xl font-black text-gray-900">Sepatu di Rak <span class="text-gray-400">(Semua Status)</span></h3>
                            <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mt-1">Daftar Semua Sepatu yang Tersimpan di Rak Penyimpanan Berdasarkan Data Storage Assignments</p>
                        </div>
                    </div>
                    
                    {{-- Stats --}}
                    <div class="flex flex-col sm:flex-row items-center gap-4 shrink-0 w-full md:w-auto justify-end">
                        <div class="px-8 py-4 bg-emerald-50/50 border border-emerald-100/50 rounded-2xl text-center md:text-right shrink-0">
                            <span class="text-[9px] font-black text-emerald-500 uppercase tracking-widest block mb-1">TOTAL SEPATU DI RAK</span>
                            <span class="text-3xl font-black text-emerald-600 font-display">{{ count($this->shoeRackOrders) }}</span>
                            <span class="text-[8px] font-bold text-gray-400 block mt-1">Total Sepatu Tersimpan di Rak</span>
                        </div>
                        
                        <div class="px-8 py-4 bg-rose-50/50 border border-rose-100/50 rounded-2xl text-center md:text-right shrink-0">
                            <span class="text-[9px] font-black text-rose-500 uppercase tracking-widest block mb-1">REKOMENDASI DONASI (> 3 BULAN)</span>
                            <span class="text-3xl font-black text-rose-600 font-display">
                                {{ $this->shoeRackOrders->filter(fn($wo) => $wo->is_donation_candidate)->count() }}
                            </span>
                            <span class="text-[8px] font-bold text-gray-400 block mt-1">Sepatu Siap Disalurkan Donasi</span>
                        </div>
                    </div>
                </div>

                {{-- API Integration Developer Panel --}}
                <div class="bg-slate-900 rounded-[2rem] p-6 text-white relative overflow-hidden shadow-2xl border border-slate-800">
                    <div class="absolute -right-16 -bottom-16 text-9xl opacity-10 pointer-events-none">🔌</div>
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-400 text-[8px] font-black rounded uppercase tracking-wider">Active Service API</span>
                                <span class="px-2 py-0.5 bg-slate-800 text-slate-400 text-[8px] font-black rounded uppercase tracking-wider">v1.0</span>
                            </div>
                            <h4 class="text-sm font-black tracking-wide text-slate-100">🔌 API INTEGRATION: WAREHOUSE SHOE RACK SYNC</h4>
                            <p class="text-slate-400 text-[9px] font-bold">Sinkronisasi data posisi rak sepatu selesai dan kandidat donasi secara real-time.</p>
                        </div>
                        
                        <div class="flex flex-col items-end gap-2 w-full lg:w-auto">
                            <div class="flex items-center gap-3 w-full lg:w-auto" x-data="{ 
                                copied: false,
                                apiUrl: '{{ url('/api/v1/warehouse-shoerack-sync') . '?api_key=' . config('app.dashboard_api_key') }}',
                                copyToClipboard() {
                                    try {
                                        this.$refs.apiInputShoeRack.select();
                                        if (navigator.clipboard && navigator.clipboard.writeText) {
                                            navigator.clipboard.writeText(this.apiUrl);
                                        } else {
                                            document.execCommand('copy');
                                        }
                                        this.copied = true;
                                        setTimeout(() => this.copied = false, 2000);
                                    } catch (err) {
                                        console.error('Failed to copy: ', err);
                                    }
                                }
                            }">
                                <div class="relative flex-1 lg:flex-none">
                                    <input x-ref="apiInputShoeRack" type="text" readonly :value="apiUrl" 
                                           class="w-full lg:w-[480px] bg-slate-800/80 border border-slate-700 rounded-xl px-4 py-2.5 text-[9px] font-mono text-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                </div>
                                <button @click="copyToClipboard()" class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 active:scale-95 text-slate-950 text-[10px] font-black rounded-xl transition-all shadow-lg shadow-emerald-500/20 flex items-center gap-2 shrink-0">
                                    <span x-show="!copied">📋 COPY URL</span>
                                    <span x-show="copied" x-cloak>✅ COPIED!</span>
                                </button>
                            </div>
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[8px] font-black text-slate-400 self-start lg:self-auto uppercase tracking-wider">
                                <span class="text-emerald-400">Parameter Opsional:</span>
                                <span>• start_date (YYYY-MM-DD)</span>
                                <span>• end_date (YYYY-MM-DD)</span>
                                <span class="text-slate-500 font-bold lowercase">Contoh: &start_date=2026-06-01&end_date=2026-06-07</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Shoe Rack Table --}}
                <div class="bg-white rounded-[2rem] p-8 shadow-lg border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider">SPK / Order</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider">Pelanggan</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider">Detail Sepatu</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Status SPK</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Posisi Rak</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Tanggal Masuk Rak</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Durasi Tersimpan</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($this->shoeRackOrders as $order)
                                    <tr class="hover:bg-emerald-50/10 transition-all duration-200 {{ $order->is_donation_candidate ? 'bg-rose-50/20' : '' }}">
                                        {{-- SPK / Order --}}
                                        <td class="py-4 font-mono text-xs font-black text-gray-900">
                                            {{ $order->spk_number }}
                                        </td>
                                        
                                        {{-- Pelanggan --}}
                                        <td class="py-4">
                                            <div class="text-xs font-black text-gray-900">{{ $order->customer_name ?? 'N/A' }}</div>
                                            <div class="text-[9px] font-bold text-gray-400 mt-0.5">{{ $order->customer_phone ?? 'N/A' }}</div>
                                        </td>
                                        
                                        {{-- Detail Sepatu --}}
                                        <td class="py-4">
                                            <div class="text-xs font-bold text-gray-700">
                                                {{ $order->shoe_brand ?: '-' }} {{ $order->shoe_type ?: '' }}
                                            </div>
                                            <div class="text-[8px] font-black text-gray-400 uppercase tracking-wider mt-0.5">
                                                {{ $order->shoe_color ?: 'Warna N/A' }} • Size {{ $order->shoe_size ?: 'N/A' }}
                                            </div>
                                        </td>
                                        
                                        {{-- Status SPK --}}
                                        <td class="py-4 text-center">
                                            @php
                                                $statusObj = $order->wo_status;
                                                $statusLabel = ($statusObj instanceof \BackedEnum) ? $statusObj->value : (string) $statusObj;
                                                $statusColor = match($statusLabel) {
                                                    'SELESAI' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                    'ASSESSMENT' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                    'WAITING_PAYMENT' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                    'DONASI' => 'bg-purple-50 text-purple-700 border-purple-200',
                                                    default => 'bg-gray-50 text-gray-700 border-gray-200',
                                                };
                                            @endphp
                                            <span class="px-2.5 py-1 {{ $statusColor }} border text-[9px] font-black rounded-lg inline-block">
                                                {{ str_replace('_', ' ', $statusLabel) }}
                                            </span>
                                        </td>

                                        {{-- Posisi Rak --}}
                                        <td class="py-4 text-center">
                                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 text-[10px] font-black rounded-lg inline-block shadow-sm">
                                                📍 RAK: {{ $order->rack_code ?: '-' }}
                                            </span>
                                        </td>
                                        
                                        {{-- Tanggal Masuk Rak --}}
                                        <td class="py-4 text-center text-xs font-bold text-gray-600">
                                            {{ $order->stored_at_formatted }}
                                        </td>
                                        
                                        {{-- Durasi Tersimpan --}}
                                        <td class="py-4 text-center">
                                            @if($order->is_donation_candidate)
                                                <span class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-wider bg-rose-500 text-white shadow-sm border border-rose-600 animate-pulse-soft">
                                                    ⚠️ {{ $order->days_stored_formatted }} (> 3 Bln)
                                                </span>
                                            @else
                                                <span class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-wider bg-gray-100 text-gray-700 border border-gray-200">
                                                    {{ $order->days_stored_formatted }}
                                                </span>
                                            @endif
                                        </td>
                                        
                                        {{-- Aksi --}}
                                        <td class="py-4 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                @php
                                                    $waMessage = "Halo " . ($order->customer_name ?? 'Pelanggan') . ",\n\nSepatu " . ($order->shoe_brand ?: '') . " " . ($order->shoe_type ?: '') . " Anda dengan No SPK *" . $order->spk_number . "* telah SELESAI dikerjakan dan saat ini disimpan di rak penyimpanan kami sejak " . $order->stored_at_formatted . " (" . ($order->days_stored === 0 ? 'hari ini' : $order->days_stored . ' hari yang lalu') . ").\n\nMohon untuk segera melakukan pengambilan sepatu Anda di workshop kami.\n\nTerima kasih! 🙏";
                                                    $waUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $order->customer_phone ?? '') . "?text=" . urlencode($waMessage);
                                                @endphp
                                                <a href="{{ $waUrl }}" target="_blank" 
                                                   class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-[9px] font-black rounded-xl transition-all shadow-md shadow-emerald-500/20 hover:scale-105">
                                                    💬 NOTIFIKASI
                                                </a>
                                                
                                                @if($order->is_donation_candidate)
                                                    <button wire:click="moveToDonation({{ $order->work_order_id }})" 
                                                            onclick="return confirm('Pindahkan sepatu {{ $order->spk_number }} ke program donasi? Tindakan ini akan mengosongkan rak penyimpanan dan mengubah status menjadi Donasi.')"
                                                            class="inline-flex items-center gap-1.5 px-3 py-2 bg-rose-600 hover:bg-rose-700 text-white text-[9px] font-black rounded-xl transition-all shadow-md shadow-rose-600/20 hover:scale-105">
                                                        🎁 MASUK DONASI
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="py-12 text-center text-gray-400 text-[10px] font-black uppercase opacity-40">
                                            📭 Tidak ada sepatu terdata di rak penyimpanan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Sortir Dashboard Grid --}}
            <div x-show="activeTab === 'sortir_dashboard'" x-transition:enter="transition ease-out duration-300" x-cloak class="space-y-6">
                {{-- API Integration Developer Panel --}}
                <div class="bg-slate-900 rounded-[2rem] p-6 text-white relative overflow-hidden shadow-2xl border border-slate-800">
                    <div class="absolute -right-16 -bottom-16 text-9xl opacity-10 pointer-events-none">🔌</div>
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-400 text-[8px] font-black rounded uppercase tracking-wider">Active Service API</span>
                                <span class="px-2 py-0.5 bg-slate-800 text-slate-400 text-[8px] font-black rounded uppercase tracking-wider">v1.0</span>
                            </div>
                            <h4 class="text-sm font-black tracking-wide text-slate-100">🔌 API INTEGRATION: WAREHOUSE SORTIR SUMMARY SYNC</h4>
                            <p class="text-slate-400 text-[9px] font-bold">Sinkronisasi data sortir gudang (SPK di sortir, lama tertahan, dan alert) secara real-time dengan external services.</p>
                        </div>
                        
                        <div class="flex flex-col items-end gap-2 w-full lg:w-auto">
                            <div class="flex items-center gap-3 w-full lg:w-auto" x-data="{ 
                                copied: false,
                                apiUrl: '{{ url('/api/v1/warehouse-sortir-summary') . '?api_key=' . config('app.dashboard_api_key') }}',
                                copyToClipboard() {
                                    try {
                                        this.$refs.apiInputSortir.select();
                                        if (navigator.clipboard && navigator.clipboard.writeText) {
                                            navigator.clipboard.writeText(this.apiUrl);
                                        } else {
                                            document.execCommand('copy');
                                        }
                                        this.copied = true;
                                        setTimeout(() => this.copied = false, 2000);
                                    } catch (err) {
                                        console.error('Failed to copy: ', err);
                                    }
                                }
                            }">
                                <div class="relative flex-1 lg:flex-none">
                                    <input x-ref="apiInputSortir" type="text" readonly :value="apiUrl" 
                                           class="w-full lg:w-[480px] bg-slate-800/80 border border-slate-700 rounded-xl px-4 py-2.5 text-[9px] font-mono text-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                </div>
                                <button @click="copyToClipboard()" class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 active:scale-95 text-slate-950 text-[10px] font-black rounded-xl transition-all shadow-lg shadow-emerald-500/20 flex items-center gap-2 shrink-0">
                                    <span x-show="!copied">📋 COPY URL</span>
                                    <span x-show="copied" x-cloak>✅ COPIED!</span>
                                </button>
                            </div>
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[8px] font-black text-slate-400 self-start lg:self-auto uppercase tracking-wider">
                                <span class="text-emerald-400">Parameter Opsional:</span>
                                <span>• start_date (YYYY-MM-DD)</span>
                                <span>• end_date (YYYY-MM-DD)</span>
                                <span>• search (String)</span>
                                <span>• overdue_only (0/1)</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KPI Header Card --}}
                <div class="bg-white rounded-[2rem] p-8 shadow-lg border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 text-9xl opacity-5 pointer-events-none">👟</div>
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-2xl shadow-inner">👟</div>
                        <div>
                            <h3 class="text-xl font-black text-gray-900">Data Sortir <span class="text-gray-400">(Sedang Disortir)</span></h3>
                            <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mt-1">Daftar SPK yang sedang berada di tahap sortir beserta durasi pengendapan</p>
                        </div>
                    </div>
                    
                    {{-- Stats --}}
                    <div class="flex flex-col sm:flex-row items-center gap-4 shrink-0 w-full md:w-auto justify-end">
                        <div class="px-8 py-4 bg-emerald-50/50 border border-emerald-100/50 rounded-2xl text-center md:text-right shrink-0">
                            <span class="text-[9px] font-black text-emerald-500 uppercase tracking-widest block mb-1">TOTAL DI SORTIR</span>
                            <span class="text-3xl font-black text-emerald-600 font-display">{{ $this->sortirSummary['metrics']['total_items_in_sortir'] ?? 0 }}</span>
                            <span class="text-[8px] font-bold text-gray-400 block mt-1">SPK sedang disortir</span>
                        </div>
                        
                        <div class="px-8 py-4 bg-rose-50/50 border border-rose-100/50 rounded-2xl text-center md:text-right shrink-0">
                            <span class="text-[9px] font-black text-rose-500 uppercase tracking-widest block mb-1">OVERDUE (> 3 HARI)</span>
                            <span class="text-3xl font-black text-rose-600 font-display">
                                {{ $this->sortirSummary['metrics']['overdue_items_count'] ?? 0 }}
                            </span>
                            <span class="text-[8px] font-bold text-gray-400 block mt-1">Butuh penanganan segera</span>
                        </div>

                        <div class="px-8 py-4 bg-blue-50/50 border border-blue-100/50 rounded-2xl text-center md:text-right shrink-0">
                            <span class="text-[9px] font-black text-blue-500 uppercase tracking-widest block mb-1">RERATA DURASI</span>
                            <span class="text-3xl font-black text-blue-600 font-display">
                                {{ $this->sortirSummary['metrics']['average_days_in_sortir'] ?? 0 }} <span class="text-sm font-bold">Hari</span>
                            </span>
                            <span class="text-[8px] font-bold text-gray-400 block mt-1">Rata-rata waktu sortir</span>
                        </div>
                    </div>
                </div>

                {{-- Filter controls --}}
                <div class="flex flex-col xl:flex-row xl:justify-between xl:items-center gap-4 bg-white p-4 rounded-[1.5rem] shadow-md border border-gray-100">
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="flex items-center gap-1.5 p-1 bg-gray-50/80 rounded-[1.2rem] border border-gray-100 flex-wrap sm:flex-nowrap shrink-0">
                            <button wire:click="$set('sortirFilter', 'all')" 
                                    class="px-4 py-1.5 rounded-lg text-[9px] font-black transition-all uppercase tracking-tighter {{ $sortirFilter === 'all' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-400 hover:text-gray-600' }}">
                                Semua Status
                            </button>
                            <button wire:click="$set('sortirFilter', 'on_track')" 
                                    class="px-4 py-1.5 rounded-lg text-[9px] font-black transition-all uppercase tracking-tighter {{ $sortirFilter === 'on_track' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-400 hover:text-gray-600' }}">
                                On Track
                            </button>
                            <button wire:click="$set('sortirFilter', 'overdue')" 
                                    class="px-4 py-1.5 rounded-lg text-[9px] font-black transition-all uppercase tracking-tighter {{ $sortirFilter === 'overdue' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-400 hover:text-gray-600' }}">
                                Stagnan (> 3 Hari)
                            </button>
                        </div>
                        
                        {{-- Jasa Dropdown --}}
                        <div class="relative shrink-0">
                            <select wire:model.live="sortirServiceId" 
                                    class="block pl-4 pr-10 py-2 bg-gray-50 border border-gray-100 rounded-[1.2rem] text-[10px] font-black uppercase text-gray-600 focus:outline-none focus:ring-4 focus:ring-[#22AF85]/5 focus:border-[#22AF85] transition-all shadow-sm cursor-pointer appearance-none min-w-[200px]">
                                <option value="">Semua Jasa</option>
                                @foreach($services as $svc)
                                    <option value="{{ $svc->id }}">{{ $svc->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Kategori Jasa Dropdown --}}
                        <div class="relative shrink-0">
                            <select wire:model.live="sortirCategory" 
                                    class="block pl-4 pr-10 py-2 bg-gray-50 border border-gray-100 rounded-[1.2rem] text-[10px] font-black uppercase text-gray-600 focus:outline-none focus:ring-4 focus:ring-[#22AF85]/5 focus:border-[#22AF85] transition-all shadow-sm cursor-pointer appearance-none min-w-[180px]">
                                <option value="">Semua Kategori</option>
                                @foreach($serviceCategories as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Date Range Picker for Estimasi Selesai (Sortir) --}}
                        <div class="relative shrink-0" wire:ignore wire:key="sortir-est-picker-container">
                            <button id="sortir-est-btn" type="button"
                                    class="block pl-4 pr-10 py-2 rounded-[1.2rem] text-[10px] font-black uppercase text-left border focus:outline-none focus:ring-4 focus:ring-[#22AF85]/5 focus:border-[#22AF85] transition-all shadow-sm cursor-pointer min-w-[180px] relative {{ $sortirEstStart && $sortirEstEnd ? 'bg-[#22AF85] text-white border-[#22AF85]' : 'bg-gray-50 text-gray-600 border-gray-100 hover:bg-gray-100' }}">
                                📅 {{ $sortirEstStart && $sortirEstEnd ? \Carbon\Carbon::parse($sortirEstStart)->format('d M') . ' - ' . \Carbon\Carbon::parse($sortirEstEnd)->format('d M') : 'Estimasi Selesai' }}
                                @if($sortirEstStart && $sortirEstEnd)
                                    <span wire:click.stop="$set('sortirEstStart', ''); $set('sortirEstEnd', '')" class="absolute right-3 top-1/2 -translate-y-1/2 hover:text-red-200 cursor-pointer font-bold text-[14px]">×</span>
                                @endif
                            </button>
                            <input x-init="
                                flatpickr($el, {
                                    mode: 'range',
                                    dateFormat: 'Y-m-d',
                                    defaultDate: ['{{ $sortirEstStart }}', '{{ $sortirEstEnd }}'],
                                    positionElement: document.getElementById('sortir-est-btn'),
                                    onChange: (selectedDates, dateStr, instance) => {
                                        if (selectedDates.length === 2) {
                                            let start = instance.formatDate(selectedDates[0], 'Y-m-d');
                                            let end = instance.formatDate(selectedDates[1], 'Y-m-d');
                                            $wire.set('sortirEstStart', start);
                                            $wire.set('sortirEstEnd', end);
                                        }
                                    }
                                });
                            " type="text" class="hidden">
                        </div>
                    </div>

                    <div>
                        <a href="{{ route('storage.dashboard.export-sortir-pdf', ['start_date' => $startDate, 'end_date' => $endDate, 'search' => $search, 'filter' => $sortirFilter, 'service_id' => $sortirServiceId, 'category' => $sortirCategory, 'est_start' => $sortirEstStart, 'est_end' => $sortirEstEnd]) }}" 
                           target="_blank"
                           class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900 hover:bg-slate-800 active:scale-95 text-white text-[10px] font-black rounded-xl transition-all shadow-lg shadow-slate-950/20">
                            🖨️ CETAK LAPORAN PDF
                        </a>
                    </div>
                </div>

                {{-- Sortir Items Table --}}
                <div class="bg-white rounded-[2rem] p-8 shadow-lg border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider">SPK / Order</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider">Pelanggan</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider">Detail Sepatu</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Waktu Masuk Sortir</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Estimasi Selesai</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Durasi Sortir</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Status / Warning</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($this->sortirSummary['items'] as $item)
                                    <tr class="hover:bg-emerald-50/10 transition-all duration-200 {{ $item['is_overdue'] ? 'bg-rose-50/20' : '' }}">
                                        {{-- SPK / Order --}}
                                        <td class="py-4 font-mono text-xs font-black text-gray-900">
                                            {{ $item['spk_number'] }}
                                        </td>
                                        
                                        {{-- Pelanggan --}}
                                        <td class="py-4">
                                            <div class="text-xs font-black text-gray-900">{{ $item['customer_name'] ?? 'N/A' }}</div>
                                        </td>
                                        
                                        {{-- Detail Sepatu --}}
                                        <td class="py-4">
                                            <div class="text-xs font-black text-gray-900 mb-1">
                                                {{ $item['shoe_brand'] }} {{ $item['shoe_type'] }}
                                            </div>
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($item['services'] as $svcName)
                                                    <span class="px-1.5 py-0.5 bg-gray-50 border border-gray-100 rounded text-[8px] font-black uppercase text-gray-500 tracking-wider">
                                                        {{ $svcName }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        
                                        {{-- Waktu Masuk Sortir --}}
                                        <td class="py-4 text-center text-xs font-bold text-gray-600">
                                            {{ $item['entered_sortir_at_formatted'] }}
                                        </td>
                                        
                                        {{-- Estimasi Selesai --}}
                                        <td class="py-4 text-center text-xs font-bold text-gray-600">
                                            {{ $item['estimation_date_formatted'] }}
                                        </td>
                                        
                                        {{-- Durasi Sortir --}}
                                        <td class="py-4 text-center">
                                            <span class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-wider {{ $item['is_overdue'] ? 'bg-rose-100 text-rose-700 border border-rose-200' : 'bg-gray-100 text-gray-700 border border-gray-200' }}">
                                                {{ $item['days_in_sortir'] }} Hari
                                            </span>
                                        </td>
                                        
                                        {{-- Status / Warning --}}
                                        <td class="py-4 text-center">
                                            @if($item['is_overdue'])
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[8px] font-black bg-rose-500 text-white border border-rose-600 uppercase tracking-wider animate-pulse-soft">
                                                    🚨 TERTANAH > 3 HARI
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[8px] font-black bg-emerald-50 text-[#22AF85] border border-emerald-100 uppercase tracking-wider">
                                                    ON TRACK
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-12 text-center text-gray-400 text-[10px] font-black uppercase opacity-40">
                                            📭 Tidak ada sepatu terdata di tahap sortir.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Production Dashboard Grid --}}
            <div x-show="activeTab === 'production_dashboard'" x-transition:enter="transition ease-out duration-300" x-cloak class="space-y-6">
                {{-- API Integration Developer Panel --}}
                <div class="bg-slate-900 rounded-[2rem] p-6 text-white relative overflow-hidden shadow-2xl border border-slate-800">
                    <div class="absolute -right-16 -bottom-16 text-9xl opacity-10 pointer-events-none">🔌</div>
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-400 text-[8px] font-black rounded uppercase tracking-wider">Active Service API</span>
                                <span class="px-2 py-0.5 bg-slate-800 text-slate-400 text-[8px] font-black rounded uppercase tracking-wider">v1.0</span>
                            </div>
                            <h4 class="text-sm font-black tracking-wide text-slate-100">🔌 API INTEGRATION: WAREHOUSE PRODUCTION SUMMARY SYNC</h4>
                            <p class="text-slate-400 text-[9px] font-bold">Sinkronisasi data produksi gudang (antrean produksi, target estimasi, dan alert SLA) secara real-time dengan external services.</p>
                        </div>
                        
                        <div class="flex flex-col items-end gap-2 w-full lg:w-auto">
                            <div class="flex items-center gap-3 w-full lg:w-auto" x-data="{ 
                                copied: false,
                                apiUrl: '{{ url('/api/v1/warehouse-production-summary') . '?api_key=' . config('app.dashboard_api_key') }}',
                                copyToClipboard() {
                                    try {
                                        this.$refs.apiInputProduction.select();
                                        if (navigator.clipboard && navigator.clipboard.writeText) {
                                            navigator.clipboard.writeText(this.apiUrl);
                                        } else {
                                            document.execCommand('copy');
                                        }
                                        this.copied = true;
                                        setTimeout(() => this.copied = false, 2000);
                                    } catch (err) {
                                        console.error('Failed to copy: ', err);
                                    }
                                }
                            }">
                                <div class="relative flex-1 lg:flex-none">
                                    <input x-ref="apiInputProduction" type="text" readonly :value="apiUrl" 
                                           class="w-full lg:w-[480px] bg-slate-800/80 border border-slate-700 rounded-xl px-4 py-2.5 text-[9px] font-mono text-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                </div>
                                <button @click="copyToClipboard()" class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 active:scale-95 text-slate-950 text-[10px] font-black rounded-xl transition-all shadow-lg shadow-emerald-500/20 flex items-center gap-2 shrink-0">
                                    <span x-show="!copied">📋 COPY URL</span>
                                    <span x-show="copied" x-cloak>✅ COPIED!</span>
                                </button>
                            </div>
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[8px] font-black text-slate-400 self-start lg:self-auto uppercase tracking-wider">
                                <span class="text-emerald-400">Parameter Opsional:</span>
                                <span>• start_date (YYYY-MM-DD)</span>
                                <span>• end_date (YYYY-MM-DD)</span>
                                <span>• search (String)</span>
                                <span>• filter (all/overdue/upcoming)</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KPI Header Card --}}
                <div class="bg-white rounded-[2rem] p-8 shadow-lg border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 text-9xl opacity-5 pointer-events-none">⚙️</div>
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-2xl shadow-inner">⚙️</div>
                        <div>
                            <h3 class="text-xl font-black text-gray-900">Data Produksi <span class="text-gray-400">(Sedang Diproduksi)</span></h3>
                            <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mt-1">Daftar SPK yang sedang berada di tahap produksi beserta pemantauan estimasi selesai</p>
                        </div>
                    </div>
                    
                    {{-- Stats --}}
                    <div class="flex flex-col sm:flex-row items-center gap-4 shrink-0 w-full md:w-auto justify-end">
                        <div class="px-8 py-4 bg-emerald-50/50 border border-emerald-100/50 rounded-2xl text-center md:text-right shrink-0">
                            <span class="text-[9px] font-black text-emerald-500 uppercase tracking-widest block mb-1">TOTAL DI PRODUKSI</span>
                            <span class="text-3xl font-black text-emerald-600 font-display">{{ $this->productionSummary['metrics']['total_items_in_production'] ?? 0 }}</span>
                            <span class="text-[8px] font-bold text-gray-400 block mt-1">SPK dalam produksi</span>
                        </div>
                        
                        <div class="px-8 py-4 bg-rose-50/50 border border-rose-100/50 rounded-2xl text-center md:text-right shrink-0">
                            <span class="text-[9px] font-black text-rose-500 uppercase tracking-widest block mb-1">TERLEWAT ESTIMASI</span>
                            <span class="text-3xl font-black text-rose-600 font-display">
                                {{ $this->productionSummary['metrics']['overdue_items_count'] ?? 0 }}
                            </span>
                            <span class="text-[8px] font-bold text-gray-400 block mt-1">Melewati target estimasi</span>
                        </div>

                        <div class="px-8 py-4 bg-amber-50/50 border border-amber-100/50 rounded-2xl text-center md:text-right shrink-0">
                            <span class="text-[9px] font-black text-amber-600 uppercase tracking-widest block mb-1">MENDEKATI ESTIMASI</span>
                            <span class="text-3xl font-black text-amber-600 font-display">
                                {{ $this->productionSummary['metrics']['upcoming_items_count'] ?? 0 }} <span class="text-sm font-bold">SPK</span>
                            </span>
                            <span class="text-[8px] font-bold text-gray-400 block mt-1">Jatuh tempo ≤ 2 hari</span>
                        </div>
                    </div>
                </div>

                {{-- Filter controls --}}
                <div class="flex flex-col xl:flex-row xl:justify-between xl:items-center gap-4 bg-white p-4 rounded-[1.5rem] shadow-md border border-gray-100">
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="flex items-center gap-1.5 p-1 bg-gray-50/80 rounded-[1.2rem] border border-gray-100 flex-wrap sm:flex-nowrap shrink-0">
                            <button wire:click="$set('productionFilter', 'all')" 
                                    class="px-4 py-1.5 rounded-lg text-[9px] font-black transition-all uppercase tracking-tighter {{ $productionFilter === 'all' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-400 hover:text-gray-600' }}">
                                Semua Status
                            </button>
                            <button wire:click="$set('productionFilter', 'on_track')" 
                                    class="px-4 py-1.5 rounded-lg text-[9px] font-black transition-all uppercase tracking-tighter {{ $productionFilter === 'on_track' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-400 hover:text-gray-600' }}">
                                On Track
                            </button>
                            <button wire:click="$set('productionFilter', 'overdue')" 
                                    class="px-4 py-1.5 rounded-lg text-[9px] font-black transition-all uppercase tracking-tighter {{ $productionFilter === 'overdue' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-400 hover:text-gray-600' }}">
                                Terlewat Estimasi (Overdue)
                            </button>
                            <button wire:click="$set('productionFilter', 'upcoming')" 
                                    class="px-4 py-1.5 rounded-lg text-[9px] font-black transition-all uppercase tracking-tighter {{ $productionFilter === 'upcoming' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-400 hover:text-gray-600' }}">
                                Mendekati Estimasi (≤ 2 Hari)
                            </button>
                        </div>
                        
                        {{-- Jasa Dropdown --}}
                        <div class="relative shrink-0">
                            <select wire:model.live="productionServiceId" 
                                    class="block pl-4 pr-10 py-2 bg-gray-50 border border-gray-100 rounded-[1.2rem] text-[10px] font-black uppercase text-gray-600 focus:outline-none focus:ring-4 focus:ring-[#22AF85]/5 focus:border-[#22AF85] transition-all shadow-sm cursor-pointer appearance-none min-w-[200px]">
                                <option value="">Semua Jasa</option>
                                @foreach($services as $svc)
                                    <option value="{{ $svc->id }}">{{ $svc->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Kategori Jasa Dropdown --}}
                        <div class="relative shrink-0">
                            <select wire:model.live="productionCategory" 
                                    class="block pl-4 pr-10 py-2 bg-gray-50 border border-gray-100 rounded-[1.2rem] text-[10px] font-black uppercase text-gray-600 focus:outline-none focus:ring-4 focus:ring-[#22AF85]/5 focus:border-[#22AF85] transition-all shadow-sm cursor-pointer appearance-none min-w-[180px]">
                                <option value="">Semua Kategori</option>
                                @foreach($serviceCategories as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Date Range Picker for Estimasi Selesai (Production) --}}
                        <div class="relative shrink-0" wire:ignore wire:key="production-est-picker-container">
                            <button id="production-est-btn" type="button"
                                    class="block pl-4 pr-10 py-2 rounded-[1.2rem] text-[10px] font-black uppercase text-left border focus:outline-none focus:ring-4 focus:ring-[#22AF85]/5 focus:border-[#22AF85] transition-all shadow-sm cursor-pointer min-w-[180px] relative {{ $productionEstStart && $productionEstEnd ? 'bg-[#22AF85] text-white border-[#22AF85]' : 'bg-gray-50 text-gray-600 border-gray-100 hover:bg-gray-100' }}">
                                📅 {{ $productionEstStart && $productionEstEnd ? \Carbon\Carbon::parse($productionEstStart)->format('d M') . ' - ' . \Carbon\Carbon::parse($productionEstEnd)->format('d M') : 'Estimasi Selesai' }}
                                @if($productionEstStart && $productionEstEnd)
                                    <span wire:click.stop="$set('productionEstStart', ''); $set('productionEstEnd', '')" class="absolute right-3 top-1/2 -translate-y-1/2 hover:text-red-200 cursor-pointer font-bold text-[14px]">×</span>
                                @endif
                            </button>
                            <input x-init="
                                flatpickr($el, {
                                    mode: 'range',
                                    dateFormat: 'Y-m-d',
                                    defaultDate: ['{{ $productionEstStart }}', '{{ $productionEstEnd }}'],
                                    positionElement: document.getElementById('production-est-btn'),
                                    onChange: (selectedDates, dateStr, instance) => {
                                        if (selectedDates.length === 2) {
                                            let start = instance.formatDate(selectedDates[0], 'Y-m-d');
                                            let end = instance.formatDate(selectedDates[1], 'Y-m-d');
                                            $wire.set('productionEstStart', start);
                                            $wire.set('productionEstEnd', end);
                                        }
                                    }
                                });
                            " type="text" class="hidden">
                        </div>
                    </div>

                    <div>
                        <a href="{{ route('storage.dashboard.export-production-pdf', ['start_date' => $startDate, 'end_date' => $endDate, 'search' => $search, 'filter' => $productionFilter, 'service_id' => $productionServiceId, 'category' => $productionCategory, 'est_start' => $productionEstStart, 'est_end' => $productionEstEnd]) }}" 
                           target="_blank"
                           class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900 hover:bg-slate-800 active:scale-95 text-white text-[10px] font-black rounded-xl transition-all shadow-lg shadow-slate-950/20">
                            🖨️ CETAK LAPORAN PDF
                        </a>
                    </div>
                </div>

                {{-- Production Items Table --}}
                <div class="bg-white rounded-[2rem] p-8 shadow-lg border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider">SPK / Order</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider">Pelanggan</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider">Detail Sepatu</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Waktu Masuk Produksi</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Estimasi Selesai</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Sisa Waktu</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Status / SLA Badge</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($this->productionSummary['items'] as $item)
                                    <tr class="hover:bg-emerald-50/10 transition-all duration-200 {{ $item['is_overdue'] ? 'bg-rose-50/20' : ($item['is_upcoming'] ? 'bg-amber-50/10' : '') }}">
                                        {{-- SPK / Order --}}
                                        <td class="py-4 font-mono text-xs font-black text-gray-900">
                                            {{ $item['spk_number'] }}
                                        </td>
                                        
                                        {{-- Pelanggan --}}
                                        <td class="py-4">
                                            <div class="text-xs font-black text-gray-900">{{ $item['customer_name'] ?? 'N/A' }}</div>
                                        </td>
                                        
                                        {{-- Detail Sepatu --}}
                                        <td class="py-4">
                                            <div class="text-xs font-black text-gray-900 mb-1">
                                                {{ $item['shoe_brand'] }} {{ $item['shoe_type'] }}
                                            </div>
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($item['services'] as $svcName)
                                                    <span class="px-1.5 py-0.5 bg-gray-50 border border-gray-100 rounded text-[8px] font-black uppercase text-gray-500 tracking-wider">
                                                        {{ $svcName }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        
                                        {{-- Waktu Masuk Produksi --}}
                                        <td class="py-4 text-center">
                                            <div class="text-xs font-black text-gray-900">{{ $item['entered_production_at_formatted'] }}</div>
                                            <div class="text-[9px] text-[#22AF85] font-black uppercase tracking-wider mt-0.5">{{ $item['days_in_production'] }} Hari di Produksi</div>
                                        </td>
                                        
                                        {{-- Estimasi Selesai --}}
                                        <td class="py-4 text-center text-xs font-bold text-gray-600">
                                            {{ $item['estimation_date_formatted'] }}
                                        </td>
                                        
                                        {{-- Sisa Waktu --}}
                                        <td class="py-4 text-center">
                                            @if($item['has_estimation'])
                                                @if($item['is_overdue'])
                                                    <span class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-wider bg-rose-100 text-rose-700 border border-rose-200">
                                                        Kelewat {{ $item['days_diff'] }} Hari
                                                    </span>
                                                @elseif($item['is_upcoming'])
                                                    <span class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-wider bg-amber-100 text-amber-700 border border-amber-200">
                                                        {{ $item['days_diff'] }} Hari Lagi
                                                    </span>
                                                @else
                                                    <span class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-wider bg-gray-100 text-gray-700 border border-gray-200">
                                                        {{ $item['days_diff'] }} Hari Lagi
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-gray-400 font-bold italic">-</span>
                                            @endif
                                        </td>
                                        
                                        {{-- Status / SLA Badge --}}
                                        <td class="py-4 text-center">
                                            @if($item['is_overdue'])
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[8px] font-black bg-rose-500 text-white border border-rose-600 uppercase tracking-wider animate-pulse-soft">
                                                    🚨 OVERDUE
                                                </span>
                                            @elseif($item['is_upcoming'])
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[8px] font-black bg-amber-500 text-white border border-amber-600 uppercase tracking-wider animate-pulse-soft">
                                                    ⏰ DUE SOON
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[8px] font-black bg-emerald-50 text-[#22AF85] border border-emerald-100 uppercase tracking-wider">
                                                    ON TRACK
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-12 text-center text-gray-400 text-[10px] font-black uppercase opacity-40">
                                            📭 Tidak ada sepatu terdata di tahap produksi.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- QC Dashboard Grid --}}
            <div x-show="activeTab === 'qc_dashboard'" x-transition:enter="transition ease-out duration-300" x-cloak class="space-y-6">
                {{-- API Integration Developer Panel --}}
                <div class="bg-slate-900 rounded-[2rem] p-6 text-white relative overflow-hidden shadow-2xl border border-slate-800">
                    <div class="absolute -right-16 -bottom-16 text-9xl opacity-10 pointer-events-none">🔌</div>
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-400 text-[8px] font-black rounded uppercase tracking-wider">Active Service API</span>
                                <span class="px-2 py-0.5 bg-slate-800 text-slate-400 text-[8px] font-black rounded uppercase tracking-wider">v1.0</span>
                            </div>
                            <h4 class="text-sm font-black tracking-wide text-slate-100">🔌 API INTEGRATION: WAREHOUSE QC SUMMARY SYNC</h4>
                            <p class="text-slate-400 text-[9px] font-bold">Sinkronisasi data QC gudang (antrean QC, target estimasi, dan alert SLA) secara real-time dengan external services.</p>
                        </div>
                        
                        <div class="flex flex-col items-end gap-2 w-full lg:w-auto">
                            <div class="flex items-center gap-3 w-full lg:w-auto" x-data="{ 
                                copied: false,
                                apiUrl: '{{ url('/api/v1/warehouse-qc-summary') . '?api_key=' . config('app.dashboard_api_key') }}',
                                copyToClipboard() {
                                    try {
                                        this.$refs.apiInputQc.select();
                                        if (navigator.clipboard && navigator.clipboard.writeText) {
                                            navigator.clipboard.writeText(this.apiUrl);
                                        } else {
                                            document.execCommand('copy');
                                        }
                                        this.copied = true;
                                        setTimeout(() => this.copied = false, 2000);
                                    } catch (err) {
                                        console.error('Failed to copy: ', err);
                                    }
                                }
                            }">
                                <div class="relative flex-1 lg:flex-none">
                                    <input x-ref="apiInputQc" type="text" readonly :value="apiUrl" 
                                           class="w-full lg:w-[480px] bg-slate-800/80 border border-slate-700 rounded-xl px-4 py-2.5 text-[9px] font-mono text-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                </div>
                                <button @click="copyToClipboard()" class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 active:scale-95 text-slate-950 text-[10px] font-black rounded-xl transition-all shadow-lg shadow-emerald-500/20 flex items-center gap-2 shrink-0">
                                    <span x-show="!copied">📋 COPY URL</span>
                                    <span x-show="copied" x-cloak>✅ COPIED!</span>
                                </button>
                            </div>
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[8px] font-black text-slate-400 self-start lg:self-auto uppercase tracking-wider">
                                <span class="text-emerald-400">Parameter Opsional:</span>
                                <span>• start_date (YYYY-MM-DD)</span>
                                <span>• end_date (YYYY-MM-DD)</span>
                                <span>• search (String)</span>
                                <span>• filter (all/overdue/upcoming)</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KPI Header Card --}}
                <div class="bg-white rounded-[2rem] p-8 shadow-lg border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 text-9xl opacity-5 pointer-events-none">🔍</div>
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-2xl shadow-inner">🔍</div>
                        <div>
                            <h3 class="text-xl font-black text-gray-900">Data QC <span class="text-gray-400">(Quality Control)</span></h3>
                            <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mt-1">Daftar SPK yang sedang berada di tahap QC beserta pemantauan estimasi selesai</p>
                        </div>
                    </div>
                    
                    {{-- Stats --}}
                    <div class="flex flex-col sm:flex-row items-center gap-4 shrink-0 w-full md:w-auto justify-end">
                        <div class="px-8 py-4 bg-emerald-50/50 border border-emerald-100/50 rounded-2xl text-center md:text-right shrink-0">
                            <span class="text-[9px] font-black text-emerald-500 uppercase tracking-widest block mb-1">TOTAL DI QC</span>
                            <span class="text-3xl font-black text-emerald-600 font-display">{{ $this->qcSummary['metrics']['total_items_in_qc'] ?? 0 }}</span>
                            <span class="text-[8px] font-bold text-gray-400 block mt-1">SPK dalam QC</span>
                        </div>
                        
                        <div class="px-8 py-4 bg-rose-50/50 border border-rose-100/50 rounded-2xl text-center md:text-right shrink-0">
                            <span class="text-[9px] font-black text-rose-500 uppercase tracking-widest block mb-1">TERLEWAT ESTIMASI</span>
                            <span class="text-3xl font-black text-rose-600 font-display">
                                {{ $this->qcSummary['metrics']['overdue_items_count'] ?? 0 }}
                            </span>
                            <span class="text-[8px] font-bold text-gray-400 block mt-1">Melewati target estimasi</span>
                        </div>

                        <div class="px-8 py-4 bg-amber-50/50 border border-amber-100/50 rounded-2xl text-center md:text-right shrink-0">
                            <span class="text-[9px] font-black text-amber-600 uppercase tracking-widest block mb-1">MENDEKATI ESTIMASI</span>
                            <span class="text-3xl font-black text-amber-600 font-display">
                                {{ $this->qcSummary['metrics']['upcoming_items_count'] ?? 0 }} <span class="text-sm font-bold">SPK</span>
                            </span>
                            <span class="text-[8px] font-bold text-gray-400 block mt-1">Jatuh tempo ≤ 2 hari</span>
                        </div>
                    </div>
                </div>

                {{-- Filter controls --}}
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 bg-white p-4 rounded-[1.5rem] shadow-md border border-gray-100">
                    <div class="flex items-center gap-1.5 p-1 bg-gray-50/80 rounded-[1.2rem] border border-gray-100 flex-wrap sm:flex-nowrap">
                        <button wire:click="$set('qcFilter', 'all')" 
                                class="px-4 py-1.5 rounded-lg text-[9px] font-black transition-all uppercase tracking-tighter {{ $qcFilter === 'all' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-400 hover:text-gray-600' }}">
                            Semua Status
                        </button>
                        <button wire:click="$set('qcFilter', 'overdue')" 
                                class="px-4 py-1.5 rounded-lg text-[9px] font-black transition-all uppercase tracking-tighter {{ $qcFilter === 'overdue' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-400 hover:text-gray-600' }}">
                            Terlewat Estimasi (Overdue)
                        </button>
                        <button wire:click="$set('qcFilter', 'upcoming')" 
                                class="px-4 py-1.5 rounded-lg text-[9px] font-black transition-all uppercase tracking-tighter {{ $qcFilter === 'upcoming' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-400 hover:text-gray-600' }}">
                            Mendekati Estimasi (≤ 2 Hari)
                        </button>
                    </div>

                    <div>
                        <a href="{{ route('storage.dashboard.export-qc-pdf', ['start_date' => $startDate, 'end_date' => $endDate, 'search' => $search, 'filter' => $qcFilter]) }}" 
                           target="_blank"
                           class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900 hover:bg-slate-800 active:scale-95 text-white text-[10px] font-black rounded-xl transition-all shadow-lg shadow-slate-950/20">
                            🖨️ CETAK LAPORAN PDF
                        </a>
                    </div>
                </div>

                {{-- QC Items Table --}}
                <div class="bg-white rounded-[2rem] p-8 shadow-lg border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider">SPK / Order</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider">Pelanggan</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider">Detail Sepatu</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Waktu Masuk QC</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Estimasi Selesai</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Sisa Waktu</th>
                                    <th class="pb-4 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Status / SLA Badge</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($this->qcSummary['items'] as $item)
                                    <tr class="hover:bg-emerald-50/10 transition-all duration-200 {{ $item['is_overdue'] ? 'bg-rose-50/20' : ($item['is_upcoming'] ? 'bg-amber-50/10' : '') }}">
                                        {{-- SPK / Order --}}
                                        <td class="py-4 font-mono text-xs font-black text-gray-900">
                                            {{ $item['spk_number'] }}
                                        </td>
                                        
                                        {{-- Pelanggan --}}
                                        <td class="py-4">
                                            <div class="text-xs font-black text-gray-900">{{ $item['customer_name'] ?? 'N/A' }}</div>
                                        </td>
                                        
                                        {{-- Detail Sepatu --}}
                                        <td class="py-4">
                                            <div class="text-xs font-bold text-gray-700">
                                                {{ $item['shoe_brand'] }} {{ $item['shoe_type'] }}
                                            </div>
                                        </td>
                                        
                                        {{-- Waktu Masuk QC --}}
                                        <td class="py-4 text-center">
                                            <div class="text-xs font-black text-gray-900">{{ $item['entered_qc_at_formatted'] }}</div>
                                            <div class="text-[9px] text-[#22AF85] font-black uppercase tracking-wider mt-0.5">{{ $item['days_in_qc'] }} Hari di QC</div>
                                        </td>
                                        
                                        {{-- Estimasi Selesai --}}
                                        <td class="py-4 text-center text-xs font-bold text-gray-600">
                                            {{ $item['estimation_date_formatted'] }}
                                        </td>
                                        
                                        {{-- Sisa Waktu --}}
                                        <td class="py-4 text-center">
                                            @if($item['has_estimation'])
                                                @if($item['is_overdue'])
                                                    <span class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-wider bg-rose-100 text-rose-700 border border-rose-200">
                                                        Kelewat {{ $item['days_diff'] }} Hari
                                                    </span>
                                                @elseif($item['is_upcoming'])
                                                    <span class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-wider bg-amber-100 text-amber-700 border border-amber-200">
                                                        {{ $item['days_diff'] }} Hari Lagi
                                                    </span>
                                                @else
                                                    <span class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-wider bg-gray-100 text-gray-700 border border-gray-200">
                                                        {{ $item['days_diff'] }} Hari Lagi
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-gray-400 font-bold italic">-</span>
                                            @endif
                                        </td>
                                        
                                        {{-- Status / SLA Badge --}}
                                        <td class="py-4 text-center">
                                            @if($item['is_overdue'])
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[8px] font-black bg-rose-500 text-white border border-rose-600 uppercase tracking-wider animate-pulse-soft">
                                                    🚨 OVERDUE
                                                </span>
                                            @elseif($item['is_upcoming'])
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[8px] font-black bg-amber-500 text-white border border-amber-600 uppercase tracking-wider animate-pulse-soft">
                                                    ⏰ DUE SOON
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[8px] font-black bg-emerald-50 text-[#22AF85] border border-emerald-100 uppercase tracking-wider">
                                                    ON TRACK
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-12 text-center text-gray-400 text-[10px] font-black uppercase opacity-40">
                                            📭 Tidak ada sepatu terdata di tahap QC.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
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
                // Flow Balance Trends (Line Chart) & Clearance Rate Comparison (Bar Chart)
                const flowData = payload ? payload.dailyFlow : @json($dailyFlow);
                if (flowData) {
                    updateOrInitChart('dailyFlowChart', 'line', {
                        labels: flowData.labels,
                        datasets: [
                            {
                                label: 'Sepatu Masuk (Before)',
                                data: flowData.sepatu_masuk,
                                borderColor: '#10B981',
                                backgroundColor: 'transparent',
                                borderWidth: 3,
                                tension: 0.4,
                                pointRadius: 2,
                                pointBackgroundColor: '#10B981'
                            },
                            {
                                label: 'SPK Print (Otw Ws)',
                                data: flowData.spk_otw,
                                borderColor: '#3B82F6',
                                backgroundColor: 'transparent',
                                borderWidth: 3,
                                tension: 0.4,
                                pointRadius: 2,
                                pointBackgroundColor: '#3B82F6'
                            },
                            {
                                label: 'After Masuk',
                                data: flowData.after_masuk,
                                borderColor: '#F59E0B',
                                backgroundColor: 'transparent',
                                borderWidth: 3,
                                tension: 0.4,
                                pointRadius: 2,
                                pointBackgroundColor: '#F59E0B'
                            },
                            {
                                label: 'Sepatu Keluar',
                                data: flowData.sepatu_keluar,
                                borderColor: '#06B6D4',
                                backgroundColor: 'transparent',
                                borderWidth: 3,
                                tension: 0.4,
                                pointRadius: 2,
                                pointBackgroundColor: '#06B6D4'
                            }
                        ]
                    }, {
                        ...standardOptions,
                        plugins: { legend: { display: true, position: 'top', labels: { boxWidth: 6, font: { size: 8, weight: 'bold' } } } }
                    }, 'flow');

                    updateOrInitChart('clearanceComparisonChart', 'bar', {
                        labels: flowData.labels,
                        datasets: [
                            {
                                label: 'Before (%)',
                                data: flowData.clearance_before,
                                backgroundColor: 'rgba(59, 130, 246, 0.85)',
                                borderRadius: 4,
                            },
                            {
                                label: 'Outbound (%)',
                                data: flowData.clearance_after,
                                backgroundColor: 'rgba(16, 185, 129, 0.85)',
                                borderRadius: 4,
                            }
                        ]
                    }, {
                        ...standardOptions,
                        plugins: { legend: { display: true, position: 'top', labels: { boxWidth: 6, font: { size: 8, weight: 'bold' } } } }
                    }, 'clearance');
                }

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

                // Manifest Summary Trends (Volumetric Bar & Valuation Line Chart)
                const manifestData = payload ? payload.manifestSummary : @json($this->manifestSummary);
                if (manifestData && manifestData.chart_data) {
                    updateOrInitChart('manifestTrendsChart', 'bar', {
                        labels: manifestData.chart_data.labels,
                        datasets: [
                            {
                                type: 'bar',
                                label: 'Sepatu Terkirim (Pasang)',
                                data: manifestData.chart_data.spk_sent,
                                backgroundColor: 'rgba(34, 175, 133, 0.7)',
                                borderColor: '#22AF85',
                                borderWidth: 1,
                                borderRadius: 6,
                                yAxisID: 'y'
                            },
                            {
                                type: 'line',
                                label: 'Jumlah Jasa (Layanan)',
                                data: manifestData.chart_data.services_count,
                                borderColor: '#FFC232',
                                backgroundColor: 'transparent',
                                borderWidth: 3,
                                tension: 0.4,
                                pointRadius: 4,
                                pointBackgroundColor: '#FFC232',
                                yAxisID: 'y1'
                            }
                        ]
                    }, {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { 
                            legend: { 
                                display: true, 
                                position: 'top', 
                                labels: { boxWidth: 8, font: { size: 8, weight: 'bold' } } 
                            } 
                        },
                        scales: {
                            y: { 
                                type: 'linear',
                                display: true,
                                position: 'left',
                                beginAtZero: true, 
                                grid: { borderDash: [5, 5], color: '#f1f1f1' }, 
                                ticks: { font: { weight: 'bold', size: 9 } },
                                title: { display: true, text: 'Volume (Pasang)', font: { size: 9, weight: 'bold' } }
                            },
                            y1: { 
                                type: 'linear',
                                display: true,
                                position: 'right',
                                beginAtZero: true, 
                                grid: { drawOnChartArea: false }, 
                                ticks: { 
                                    font: { weight: 'bold', size: 9 },
                                    callback: function(value) {
                                        return value.toLocaleString('id-ID') + ' Jasa';
                                    }
                                },
                                title: { display: true, text: 'Volume Jasa (Layanan)', font: { size: 9, weight: 'bold' } }
                            },
                            x: { grid: { display: false }, ticks: { font: { weight: 'bold', size: 9 } } }
                        }
                    }, 'manifestSummary');
                }
            };

            // Initial Load
            setTimeout(() => processCharts(), 200);

            // Reactive Hook
            Livewire.on('refreshCharts', (data) => {
                const payload = Array.isArray(data) ? data[0] : data;
                processCharts(payload);
            });
        });
    </script>
    @endpush
</div>
