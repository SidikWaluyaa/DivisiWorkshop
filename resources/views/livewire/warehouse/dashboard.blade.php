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



            {{-- Hidden Tab Navigation placeholder --}}
            <div class="hidden">
                <button @click="activeTab = 'summary'" class="px-8 py-2.5 rounded-[1rem] bg-white text-gray-900 shadow-lg text-xs font-black">
                        📊 RINGKASAN
                </button>
            </div>

            {{-- Summary Grid --}}
            <div x-show="activeTab === 'summary'" x-transition:enter="transition ease-out duration-300" class="space-y-6">
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

                {{-- The Big 8 Scoreboard Cards Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    
                    {{-- Card 1: Sepatu Masuk Before --}}
                    <div class="bg-white rounded-[2rem] p-6 shadow-md border border-gray-100 kpi-card relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 text-8xl opacity-5 pointer-events-none group-hover:scale-110 transition-transform duration-300">📥</div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">1. Sepatu Masuk (Before)</span>
                            <div class="w-8 h-8 bg-emerald-50 rounded-xl flex items-center justify-center text-sm shadow-inner">📥</div>
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
                            <div class="w-8 h-8 bg-amber-50 rounded-xl flex items-center justify-center text-sm shadow-inner">✨</div>
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

                    {{-- Card 6: Total Sepatu digudang & before --}}
                    <div class="bg-white rounded-[2rem] p-6 shadow-md border border-gray-100 kpi-card relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 text-8xl opacity-5 pointer-events-none group-hover:scale-110 transition-transform duration-300">📦</div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">6. Total Inventaris Gudang</span>
                            <div class="w-8 h-8 bg-slate-50 rounded-xl flex items-center justify-center text-sm shadow-inner">📦</div>
                        </div>
                        <div class="text-3xl font-black text-slate-800">{{ $stats['total_inventory'] ?? 0 }} <span class="text-xs font-bold text-slate-400">Pasang</span></div>
                        <div class="text-[8px] font-black text-gray-400 uppercase tracking-wider mt-1">Seluruh Fisik di Dalam Rak</div>
                    </div>

                    {{-- Card 7: Clearance Rate (Before / Inbound Flow) --}}
                    <div class="bg-white rounded-[2rem] p-6 shadow-md border border-gray-100 kpi-card relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 text-8xl opacity-5 pointer-events-none group-hover:scale-110 transition-transform duration-300">⚖️</div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">7. Clearance Rate Before</span>
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

                    {{-- Card 8: Clearance Rate (After / Outbound Flow) --}}
                    <div class="bg-white rounded-[2rem] p-6 shadow-md border border-gray-100 kpi-card relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 text-8xl opacity-5 pointer-events-none group-hover:scale-110 transition-transform duration-300">🔄</div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">8. Clearance Rate Outbound</span>
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
                </div>

                {{-- Flow Balance Analytics Section --}}
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                    {{-- Double-Curve Flow Line Chart --}}
                    <div class="xl:col-span-2 bg-white rounded-[2rem] p-6 shadow-lg border border-gray-100 relative overflow-hidden">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="text-xs font-black text-gray-800 uppercase tracking-widest flex items-center gap-2 text-[#22AF85]">📈 GRAFIK LAJU ARUS KESEIMBANGAN</h4>
                                <span class="text-[8px] font-bold text-gray-400 uppercase tracking-widest">Inbound (Masuk vs OTW WS) & Outbound (After vs Keluar)</span>
                            </div>
                        </div>
                        <div style="height: 220px;" wire:ignore><canvas id="dailyFlowChart"></canvas></div>
                    </div>
                    
                    {{-- Clearance Rates Bar Chart --}}
                    <div class="xl:col-span-1 bg-white rounded-[2rem] p-6 shadow-lg border border-gray-100 relative overflow-hidden">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="text-xs font-black text-gray-800 uppercase tracking-widest flex items-center gap-2 text-indigo-600">📊 TINGKAT CLEARANCE (%)</h4>
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
