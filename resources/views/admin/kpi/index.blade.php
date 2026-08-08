<x-app-layout>
<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<!-- Flatpickr Theme Dark if needed, but let's use standard and customize -->
<style>
    .flatpickr-calendar {
        background: rgba(255, 255, 255, 0.98) !important;
        backdrop-filter: blur(20px) !important;
        border: 1px solid rgba(226, 232, 240, 0.9) !important;
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.15), 0 0 0 1px rgba(15, 23, 42, 0.05) !important;
        border-radius: 1.5rem !important;
        padding: 12px !important;
        width: 320px !important;
        box-sizing: border-box !important;
        font-family: inherit !important;
    }
    .dark .flatpickr-calendar {
        background: rgba(30, 41, 59, 0.98) !important;
        border: 1px solid rgba(51, 65, 85, 0.9) !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important;
    }
    .flatpickr-innerContainer {
        width: 100% !important;
        box-sizing: border-box !important;
    }
    .flatpickr-rContainer, .flatpickr-days, .dayContainer {
        width: 100% !important;
        min-width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }
    .dayContainer {
        justify-content: space-around !important;
    }
    .flatpickr-months {
        align-items: center !important;
        margin-bottom: 6px !important;
    }
    .flatpickr-months .flatpickr-month,
    .flatpickr-current-month .flatpickr-monthDropdown-months,
    .flatpickr-current-month input.cur-year {
        color: #0f172a !important;
        fill: #0f172a !important;
        font-weight: 800 !important;
    }
    .dark .flatpickr-months .flatpickr-month,
    .dark .flatpickr-current-month .flatpickr-monthDropdown-months,
    .dark .flatpickr-current-month input.cur-year {
        color: #f8fafc !important;
        fill: #f8fafc !important;
    }
    .flatpickr-months .flatpickr-prev-month,
    .flatpickr-months .flatpickr-next-month {
        padding: 6px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    .flatpickr-months .flatpickr-prev-month svg,
    .flatpickr-months .flatpickr-next-month svg {
        fill: #475569 !important;
        width: 12px !important;
        height: 12px !important;
    }
    .dark .flatpickr-months .flatpickr-prev-month svg,
    .dark .flatpickr-months .flatpickr-next-month svg {
        fill: #cbd5e1 !important;
    }
    .flatpickr-weekdays {
        width: 100% !important;
        margin-bottom: 6px !important;
    }
    .flatpickr-weekday {
        color: #64748b !important;
        font-weight: 700 !important;
        font-size: 0.75rem !important;
    }
    .dark .flatpickr-weekday {
        color: #94a3b8 !important;
    }
    .flatpickr-day {
        color: #1e293b !important;
        font-weight: 600 !important;
        font-size: 0.8125rem !important;
        max-width: 38px !important;
        height: 38px !important;
        line-height: 38px !important;
        margin: 1px 0 !important;
        border-radius: 8px !important;
        border-color: transparent !important;
    }
    .dark .flatpickr-day {
        color: #e2e8f0 !important;
    }
    .flatpickr-day.today {
        border: 2px solid #0d9488 !important;
        color: #0d9488 !important;
        font-weight: 800 !important;
        background: transparent !important;
    }
    .dark .flatpickr-day.today {
        border-color: #14b8a6 !important;
        color: #2dd4bf !important;
    }
    .flatpickr-day:hover {
        background: #f1f5f9 !important;
        color: #0f172a !important;
    }
    .dark .flatpickr-day:hover {
        background: #334155 !important;
        color: #ffffff !important;
    }
    .flatpickr-day.startRange {
        background: #0d9488 !important;
        color: #ffffff !important;
        font-weight: 800 !important;
        border-radius: 12px 0 0 12px !important;
    }
    .flatpickr-day.endRange {
        background: #0d9488 !important;
        color: #ffffff !important;
        font-weight: 800 !important;
        border-radius: 0 12px 12px 0 !important;
    }
    .flatpickr-day.startRange.endRange,
    .flatpickr-day.selected {
        background: #0d9488 !important;
        color: #ffffff !important;
        font-weight: 800 !important;
        border-radius: 12px !important;
    }
    .flatpickr-day.inRange {
        background: rgba(13, 148, 136, 0.15) !important;
        border-color: transparent !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        color: #0f766e !important;
        font-weight: 700 !important;
    }
    .dark .flatpickr-day.inRange {
        background: rgba(20, 184, 166, 0.25) !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        color: #5eead4 !important;
        font-weight: 700 !important;
    }
    .flatpickr-day.inRange:hover {
        background: rgba(13, 148, 136, 0.25) !important;
    }
    .dark .flatpickr-day.inRange:hover {
        background: rgba(20, 184, 166, 0.35) !important;
    }
    .flatpickr-calendar .flatpickr-innerContainer {
        padding: 0.25rem !important;
    }
</style>

<div class="container mx-auto px-4 py-8 max-w-7xl" x-data="{ activeTab: '{{ request('tab', 'workshop') }}', showApiModal: false }">
    
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-1.5 h-8 rounded-full transition-colors" :class="activeTab === 'workshop' ? 'bg-teal-500' : (activeTab === 'gudang' ? 'bg-amber-500' : (activeTab === 'finance' ? 'bg-emerald-500' : 'bg-cyan-500'))"></div>
                <h1 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight" x-text="activeTab === 'workshop' ? 'KPI WORKSHOP' : (activeTab === 'gudang' ? 'KPI GUDANG' : (activeTab === 'finance' ? 'KPI FINANCE' : 'KPI CS'))"></h1>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest mt-1 ml-4" x-text="activeTab === 'workshop' ? 'Ringkasan Kinerja & Beban Kerja Divisi Workshop' : (activeTab === 'gudang' ? 'Ringkasan Kinerja & Logistik Gudang' : (activeTab === 'finance' ? 'Ringkasan Arus Kas, Tagihan & Piutang Keuangan' : 'Ringkasan Performa, Closing Path & Hasill CS'))"></p>
        </div>
        
        <div class="flex gap-2">
            {{-- API Integration Button --}}
            <button @click="showApiModal = true" 
               class="inline-flex items-center gap-2.5 px-6 py-3.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-2xl text-xs font-black shadow-sm transition-all hover:scale-105 active:scale-95 uppercase tracking-widest border border-gray-200 dark:border-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                </svg>
                API
            </button>

            {{-- Export Excel Button Workshop --}}
            <a href="{{ route('admin.kpi.export', request()->all()) }}" 
               x-show="activeTab === 'workshop'"
               class="inline-flex items-center gap-2.5 px-6 py-3.5 bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white rounded-2xl text-xs font-black shadow-lg shadow-teal-500/20 transition-all hover:scale-105 active:scale-95 uppercase tracking-widest">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Ekspor Excel
            </a>

            {{-- Export Excel Button Gudang --}}
            <a href="{{ route('admin.kpi.exportGudang', request()->all()) }}" 
               x-show="activeTab === 'gudang'" style="display: none;"
               class="inline-flex items-center gap-2.5 px-6 py-3.5 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white rounded-2xl text-xs font-black shadow-lg shadow-amber-500/20 transition-all hover:scale-105 active:scale-95 uppercase tracking-widest">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Ekspor Excel
            </a>

            {{-- Export Excel Button Finance --}}
            <a href="{{ route('admin.kpi.exportFinance', request()->all()) }}" 
               x-show="activeTab === 'finance'" style="display: none;"
               class="inline-flex items-center gap-2.5 px-6 py-3.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white rounded-2xl text-xs font-black shadow-lg shadow-emerald-500/20 transition-all hover:scale-105 active:scale-95 uppercase tracking-widest">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Ekspor Excel
            </a>

            {{-- Export Excel Button CS --}}
            <a href="{{ route('admin.kpi.exportCs', request()->all()) }}" 
               x-show="activeTab === 'cs'" style="display: none;"
               class="inline-flex items-center gap-2.5 px-6 py-3.5 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white rounded-2xl text-xs font-black shadow-lg shadow-cyan-500/20 transition-all hover:scale-105 active:scale-95 uppercase tracking-widest">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Ekspor Excel
            </a>
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div class="flex flex-wrap gap-2 mb-8 bg-gray-100/50 dark:bg-gray-800/50 p-1.5 rounded-2xl border border-gray-200 dark:border-gray-700/50 w-max">
        <button @click="activeTab = 'workshop'" 
                :class="activeTab === 'workshop' ? 'bg-white dark:bg-gray-700 text-teal-600 dark:text-teal-400 shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-white/50 dark:hover:bg-gray-700/50'"
                class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-widest transition-all">
            <span class="text-base">🛠️</span> KPI WORKSHOP
        </button>
        <button @click="activeTab = 'gudang'" 
                :class="activeTab === 'gudang' ? 'bg-white dark:bg-gray-700 text-amber-600 dark:text-amber-400 shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-white/50 dark:hover:bg-gray-700/50'"
                class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-widest transition-all">
            <span class="text-base">📦</span> KPI GUDANG
        </button>
        <button @click="activeTab = 'finance'" 
                :class="activeTab === 'finance' ? 'bg-white dark:bg-gray-700 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-white/50 dark:hover:bg-gray-700/50'"
                class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-widest transition-all">
            <span class="text-base">💰</span> KPI FINANCE
        </button>
        <button @click="activeTab = 'cs'" 
                :class="activeTab === 'cs' ? 'bg-white dark:bg-gray-700 text-cyan-600 dark:text-cyan-400 shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-white/50 dark:hover:bg-gray-700/50'"
                class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-widest transition-all">
            <span class="text-base">🎧</span> KPI CS
        </button>
    </div>

    {{-- Filter Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-xl p-6 mb-8">
        <form method="GET" action="{{ route('admin.kpi.index') }}" class="flex flex-col sm:flex-row items-end gap-4">
            <input type="hidden" name="tab" :value="activeTab">
            
            {{-- Date Range Picker --}}
            <div class="space-y-1.5 flex-1 w-full">
                <label class="text-[9px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest ml-1">Pilih Rentang Tanggal SPK</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 pointer-events-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </span>
                    <input type="text" id="date-range" name="date_range" value="{{ $dateRange }}" placeholder="Pilih Tanggal Mulai s/d Selesai..." readonly
                           class="w-full bg-gray-50 dark:bg-gray-750 border border-gray-200 dark:border-gray-650 rounded-2xl pl-11 pr-4 py-3.5 text-sm text-gray-805 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all font-bold cursor-pointer">
                </div>
            </div>

            {{-- Submit and Reset --}}
            <div class="flex gap-3 w-full sm:w-auto">
                <button type="submit" 
                        class="flex-1 sm:flex-none px-10 py-3.5 bg-teal-600 hover:bg-teal-500 text-white rounded-2xl text-xs font-black shadow-md shadow-teal-600/10 transition-all hover:scale-105 active:scale-95 uppercase tracking-widest">
                    Terapkan
                </button>
            </div>
        </form>
    </div>

    {{-- WORKSHOP TAB CONTENT --}}
    <div x-show="activeTab === 'workshop'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
        {{-- Main Layout Wrapper --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">
        
        {{-- Left Column: Grid 4 KPI Cards --}}
        <div class="xl:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
        
        {{-- PREPARATION Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-xl overflow-hidden group hover:shadow-2xl transition-all duration-300">
            <div class="bg-gradient-to-r from-teal-500/10 to-emerald-500/10 px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-teal-500/10 rounded-xl flex items-center justify-center text-teal-600 font-bold">
                        🧼
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-gray-800 dark:text-white tracking-tight">1. PREPARATION</h3>
                        <p class="text-[9px] text-gray-400 uppercase font-bold tracking-wider">Tahap Cuci & Pembongkaran</p>
                    </div>
                </div>
            </div>
            <div class="p-6 grid grid-cols-3 gap-4">
                <div class="bg-slate-50 dark:bg-gray-750/30 rounded-2xl p-4 border border-gray-100 dark:border-gray-700 text-center">
                    <span class="block text-[8px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest mb-1">📥 TOTAL MASUK</span>
                    <div class="text-xl font-black text-slate-800 dark:text-white tracking-tight">
                        {{ $summary['PREPARATION']['total_masuk'] }} SPK
                    </div>
                </div>
                <div class="bg-slate-50 dark:bg-gray-750/30 rounded-2xl p-4 border border-gray-100 dark:border-gray-700 text-center flex flex-col justify-center">
                    <span class="block text-[8px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest mb-1">📤 TOTAL KELUAR</span>
                    <div class="text-xl font-black text-slate-800 dark:text-white tracking-tight">
                        {{ $summary['PREPARATION']['total_keluar'] }} SPK
                    </div>
                </div>
                <div class="bg-teal-50/50 dark:bg-teal-950/20 rounded-2xl p-4 border border-teal-100/50 dark:border-teal-900/30 text-center col-span-3 sm:col-span-1 flex flex-col justify-center items-center">
                    <span class="block text-[8px] font-black text-teal-600 dark:text-teal-400 uppercase tracking-widest mb-2">✨ NET (BERSIH)</span>
                    <div class="flex gap-4 w-full justify-center">
                        <div class="text-center">
                            <span class="block text-[9px] text-teal-600/70 dark:text-teal-400/70 font-bold mb-0.5">MASUK</span>
                            <span class="text-sm font-black text-teal-700 dark:text-teal-400">{{ $summary['PREPARATION']['masuk_bersih'] }}</span>
                        </div>
                        <div class="w-px bg-teal-600/20 dark:bg-teal-400/20"></div>
                        <div class="text-center">
                            <span class="block text-[9px] text-teal-600/70 dark:text-teal-400/70 font-bold mb-0.5">KELUAR</span>
                            <span class="text-sm font-black text-teal-700 dark:text-teal-400">{{ $summary['PREPARATION']['keluar_bersih'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SORTIR Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-xl overflow-hidden group hover:shadow-2xl transition-all duration-300">
            <div class="bg-gradient-to-r from-amber-500/10 to-orange-500/10 px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-500/10 rounded-xl flex items-center justify-center text-amber-600 font-bold">
                        🔍
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-gray-800 dark:text-white tracking-tight">2. SORTIR</h3>
                        <p class="text-[9px] text-gray-400 uppercase font-bold tracking-wider">Tahap Sortir & Kelengkapan Material</p>
                    </div>
                </div>
            </div>
            <div class="p-6 grid grid-cols-3 gap-4">
                <div class="bg-slate-50 dark:bg-gray-750/30 rounded-2xl p-4 border border-gray-100 dark:border-gray-700 text-center">
                    <span class="block text-[8px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest mb-1">📥 TOTAL MASUK</span>
                    <div class="text-xl font-black text-slate-800 dark:text-white tracking-tight">
                        {{ $summary['SORTIR']['total_masuk'] }} SPK
                    </div>
                </div>
                <div class="bg-slate-50 dark:bg-gray-750/30 rounded-2xl p-4 border border-gray-100 dark:border-gray-700 text-center flex flex-col justify-center">
                    <span class="block text-[8px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest mb-1">📤 TOTAL KELUAR</span>
                    <div class="text-xl font-black text-slate-800 dark:text-white tracking-tight">
                        {{ $summary['SORTIR']['total_keluar'] }} SPK
                    </div>
                </div>
                <div class="bg-amber-50/50 dark:bg-amber-955/20 rounded-2xl p-4 border border-amber-100/50 dark:border-amber-900/30 text-center col-span-3 sm:col-span-1 flex flex-col justify-center items-center">
                    <span class="block text-[8px] font-black text-amber-600 dark:text-amber-400 uppercase tracking-widest mb-2">✨ NET (BERSIH)</span>
                    <div class="flex gap-4 w-full justify-center">
                        <div class="text-center">
                            <span class="block text-[9px] text-amber-600/70 dark:text-amber-400/70 font-bold mb-0.5">MASUK</span>
                            <span class="text-sm font-black text-amber-700 dark:text-amber-400">{{ $summary['SORTIR']['masuk_bersih'] }}</span>
                        </div>
                        <div class="w-px bg-amber-600/20 dark:bg-amber-400/20"></div>
                        <div class="text-center">
                            <span class="block text-[9px] text-amber-600/70 dark:text-amber-400/70 font-bold mb-0.5">KELUAR</span>
                            <span class="text-sm font-black text-amber-700 dark:text-amber-400">{{ $summary['SORTIR']['keluar_bersih'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- PRODUCTION Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-xl overflow-hidden group hover:shadow-2xl transition-all duration-300">
            <div class="bg-gradient-to-r from-blue-500/10 to-indigo-500/10 px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-500/10 rounded-xl flex items-center justify-center text-blue-600 font-bold">
                        🛠️
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-gray-800 dark:text-white tracking-tight">3. PRODUCTION</h3>
                        <p class="text-[9px] text-gray-400 uppercase font-bold tracking-wider">Tahap Produksi / Repacking & Reparasi</p>
                    </div>
                </div>
            </div>
            <div class="p-6 grid grid-cols-3 gap-4">
                <div class="bg-slate-50 dark:bg-gray-750/30 rounded-2xl p-4 border border-gray-100 dark:border-gray-700 text-center">
                    <span class="block text-[8px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest mb-1">📥 TOTAL MASUK</span>
                    <div class="text-xl font-black text-slate-800 dark:text-white tracking-tight">
                        {{ $summary['PRODUCTION']['total_masuk'] }} SPK
                    </div>
                </div>
                <div class="bg-slate-50 dark:bg-gray-750/30 rounded-2xl p-4 border border-gray-100 dark:border-gray-700 text-center flex flex-col justify-center">
                    <span class="block text-[8px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest mb-1">📤 TOTAL KELUAR</span>
                    <div class="text-xl font-black text-slate-800 dark:text-white tracking-tight">
                        {{ $summary['PRODUCTION']['total_keluar'] }} SPK
                    </div>
                </div>
                <div class="bg-blue-50/50 dark:bg-blue-955/20 rounded-2xl p-4 border border-blue-100/50 dark:border-blue-900/30 text-center col-span-3 sm:col-span-1 flex flex-col justify-center items-center">
                    <span class="block text-[8px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest mb-2">✨ NET (BERSIH)</span>
                    <div class="flex gap-4 w-full justify-center">
                        <div class="text-center">
                            <span class="block text-[9px] text-blue-600/70 dark:text-blue-400/70 font-bold mb-0.5">MASUK</span>
                            <span class="text-sm font-black text-blue-700 dark:text-blue-400">{{ $summary['PRODUCTION']['masuk_bersih'] }}</span>
                        </div>
                        <div class="w-px bg-blue-600/20 dark:bg-blue-400/20"></div>
                        <div class="text-center">
                            <span class="block text-[9px] text-blue-600/70 dark:text-blue-400/70 font-bold mb-0.5">KELUAR</span>
                            <span class="text-sm font-black text-blue-700 dark:text-blue-400">{{ $summary['PRODUCTION']['keluar_bersih'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- QC Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-xl overflow-hidden group hover:shadow-2xl transition-all duration-300">
            <div class="bg-gradient-to-r from-purple-500/10 to-pink-500/10 px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-500/10 rounded-xl flex items-center justify-center text-purple-600 font-bold">
                        ✅
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-gray-800 dark:text-white tracking-tight">4. QUALITY CONTROL</h3>
                        <p class="text-[9px] text-gray-400 uppercase font-bold tracking-wider">Tahap Quality Control & Finishing</p>
                    </div>
                </div>
            </div>
            <div class="p-6 grid grid-cols-3 gap-4">
                <div class="bg-slate-50 dark:bg-gray-750/30 rounded-2xl p-4 border border-gray-100 dark:border-gray-700 text-center">
                    <span class="block text-[8px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest mb-1">📥 TOTAL MASUK</span>
                    <div class="text-xl font-black text-slate-800 dark:text-white tracking-tight">
                        {{ $summary['QC']['total_masuk'] }} SPK
                    </div>
                </div>
                <div class="bg-slate-50 dark:bg-gray-750/30 rounded-2xl p-4 border border-gray-100 dark:border-gray-700 text-center flex flex-col justify-center">
                    <span class="block text-[8px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest mb-1">📤 TOTAL KELUAR</span>
                    <div class="text-xl font-black text-slate-800 dark:text-white tracking-tight">
                        {{ $summary['QC']['total_keluar'] }} SPK
                    </div>
                </div>
                <div class="bg-purple-50/50 dark:bg-purple-955/20 rounded-2xl p-4 border border-purple-100/50 dark:border-purple-900/30 text-center col-span-3 sm:col-span-1 flex flex-col justify-center items-center">
                    <span class="block text-[8px] font-black text-purple-600 dark:text-purple-400 uppercase tracking-widest mb-2">✨ NET (BERSIH)</span>
                    <div class="flex gap-4 w-full justify-center">
                        <div class="text-center">
                            <span class="block text-[9px] text-purple-600/70 dark:text-purple-400/70 font-bold mb-0.5">MASUK</span>
                            <span class="text-sm font-black text-purple-700 dark:text-purple-400">{{ $summary['QC']['masuk_bersih'] }}</span>
                        </div>
                        <div class="w-px bg-purple-600/20 dark:bg-purple-400/20"></div>
                        <div class="text-center">
                            <span class="block text-[9px] text-purple-600/70 dark:text-purple-400/70 font-bold mb-0.5">KELUAR</span>
                            <span class="text-sm font-black text-purple-700 dark:text-purple-400">{{ $summary['QC']['keluar_bersih'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        </div> {{-- End of Left Column --}}

        {{-- Right Column: CX Follow Up Anomalies Table --}}
        <div class="xl:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-rose-100 dark:border-rose-900/30 shadow-xl overflow-hidden group transition-all duration-300">
                <div class="bg-gradient-to-r from-rose-500/10 to-red-500/10 px-6 py-5 border-b border-rose-100 dark:border-rose-900/30 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-rose-500/10 rounded-xl flex items-center justify-center text-rose-600 font-bold">
                            ⚠️
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-gray-800 dark:text-white tracking-tight">CX FOLLOW UP</h3>
                            <p class="text-[9px] text-gray-400 uppercase font-bold tracking-wider">Laporan Anomali Status</p>
                        </div>
                    </div>
                </div>
                <div class="p-0 overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 dark:bg-gray-750/50 text-[10px] uppercase text-gray-400 font-black tracking-wider">
                            <tr>
                                <th class="px-6 py-3">Arah Pergerakan</th>
                                <th class="px-6 py-3 text-center">Total SPK</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                            <!-- Stage to CX -->
                            @foreach(['PREPARATION', 'SORTIR', 'PRODUCTION', 'QC'] as $s)
                                <tr class="hover:bg-rose-50/50 dark:hover:bg-rose-900/10 transition-colors">
                                    <td class="px-6 py-3 font-medium text-slate-700 dark:text-gray-300">
                                        <div class="flex items-center gap-2 text-xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                                            {{ $s }} <span class="text-gray-300">→</span> CX
                                        </div>
                                    </td>
                                    <td class="px-6 py-3 text-center font-black text-rose-600 dark:text-rose-400">
                                        {{ $cxTransitions[$s]['to_cx'] }}
                                    </td>
                                </tr>
                            @endforeach
                            <!-- CX to Stage -->
                            @foreach(['PREPARATION', 'SORTIR', 'PRODUCTION', 'QC'] as $s)
                                <tr class="hover:bg-teal-50/50 dark:hover:bg-teal-900/10 transition-colors">
                                    <td class="px-6 py-3 font-medium text-slate-700 dark:text-gray-300">
                                        <div class="flex items-center gap-2 text-xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-teal-400"></span>
                                            CX <span class="text-gray-300">→</span> {{ $s }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-3 text-center font-black text-teal-600 dark:text-teal-400">
                                        {{ $cxTransitions[$s]['from_cx'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div> {{-- End of Right Column --}}

        </div> {{-- End of Main Layout Wrapper --}}
    </div> {{-- End of WORKSHOP TAB CONTENT --}}

    {{-- GUDANG TAB CONTENT --}}
    <div x-show="activeTab === 'gudang'" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
        
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            
            {{-- 1. SEPATU MASUK (BEFORE) --}}
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-xl overflow-hidden group hover:shadow-2xl transition-all duration-300 relative">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <span class="block text-[10px] font-black text-teal-600 dark:text-teal-400 uppercase tracking-widest mb-1">1. SEPATU MASUK (BEFORE)</span>
                            <div class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">DITERIMA FISIK DI GUDANG</div>
                        </div>
                        <div class="w-10 h-10 bg-teal-50 dark:bg-teal-900/20 rounded-xl flex items-center justify-center text-teal-600 dark:text-teal-400 font-bold border border-teal-100 dark:border-teal-800">
                            📥
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-5xl font-black text-slate-800 dark:text-white tracking-tighter">{{ $gudangSummary['sepatu_masuk'] }}</span>
                        <span class="text-sm font-bold text-gray-400 dark:text-gray-500">Pasang</span>
                    </div>
                </div>
                <!-- Decorative background element -->
                <div class="absolute -bottom-6 -right-6 text-9xl opacity-5 dark:opacity-10 pointer-events-none transform group-hover:scale-110 transition-transform duration-500">
                    📥
                </div>
            </div>

            {{-- 2. SPK PRINT (OTW WS) --}}
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-xl overflow-hidden group hover:shadow-2xl transition-all duration-300 relative">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <span class="block text-[10px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest mb-1">2. SPK PRINT (OTW WS)</span>
                            <div class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">DIKIRIM KE REPARASI / MANIFEST</div>
                        </div>
                        <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold border border-blue-100 dark:border-blue-800">
                            🚚
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-5xl font-black text-slate-800 dark:text-white tracking-tighter">{{ $gudangSummary['spk_otw'] }}</span>
                        <span class="text-sm font-bold text-gray-400 dark:text-gray-500">Pasang</span>
                    </div>
                </div>
                <div class="absolute -bottom-6 -right-6 text-9xl opacity-5 dark:opacity-10 pointer-events-none transform group-hover:scale-110 transition-transform duration-500">
                    🚚
                </div>
            </div>

            {{-- 3. SPK TERTAHAN (QC REJECT) --}}
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-xl overflow-hidden group hover:shadow-2xl transition-all duration-300 relative">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <span class="block text-[10px] font-black text-rose-600 dark:text-rose-400 uppercase tracking-widest mb-1">3. SPK TERTAHAN (QC REJECT)</span>
                            <div class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">GAGAL PENERIMAAN AWAL</div>
                        </div>
                        <div class="w-10 h-10 bg-rose-50 dark:bg-rose-900/20 rounded-xl flex items-center justify-center text-rose-600 dark:text-rose-400 font-bold border border-rose-100 dark:border-rose-800">
                            ⚠️
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-5xl font-black text-slate-800 dark:text-white tracking-tighter">{{ $gudangSummary['qc_reject'] }}</span>
                        <span class="text-sm font-bold text-gray-400 dark:text-gray-500">Pasang</span>
                    </div>
                </div>
                <div class="absolute -bottom-6 -right-6 text-9xl opacity-5 dark:opacity-10 pointer-events-none transform group-hover:scale-110 transition-transform duration-500">
                    ⚠️
                </div>
            </div>

            {{-- 4. AFTER MASUK --}}
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-xl overflow-hidden group hover:shadow-2xl transition-all duration-300 relative">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <span class="block text-[10px] font-black text-amber-600 dark:text-amber-400 uppercase tracking-widest mb-1">4. AFTER MASUK</span>
                            <div class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">SELESAI REPARASI MASUK RAK</div>
                        </div>
                        <div class="w-10 h-10 bg-amber-50 dark:bg-amber-900/20 rounded-xl flex items-center justify-center text-amber-600 dark:text-amber-400 font-bold border border-amber-100 dark:border-amber-800">
                            ✨
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-5xl font-black text-slate-800 dark:text-white tracking-tighter">{{ $gudangSummary['after_masuk'] }}</span>
                        <span class="text-sm font-bold text-gray-400 dark:text-gray-500">Pasang</span>
                    </div>
                </div>
                <div class="absolute -bottom-6 -right-6 text-9xl opacity-5 dark:opacity-10 pointer-events-none transform group-hover:scale-110 transition-transform duration-500">
                    ✨
                </div>
            </div>

            {{-- 5. SEPATU KELUAR --}}
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-xl overflow-hidden group hover:shadow-2xl transition-all duration-300 relative">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <span class="block text-[10px] font-black text-purple-600 dark:text-purple-400 uppercase tracking-widest mb-1">5. SEPATU KELUAR</span>
                            <div class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">PENGAMBILAN & KIRIM LUNAS</div>
                        </div>
                        <div class="w-10 h-10 bg-purple-50 dark:bg-purple-900/20 rounded-xl flex items-center justify-center text-purple-600 dark:text-purple-400 font-bold border border-purple-100 dark:border-purple-800">
                            📤
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-5xl font-black text-slate-800 dark:text-white tracking-tighter">{{ $gudangSummary['sepatu_keluar'] }}</span>
                        <span class="text-sm font-bold text-gray-400 dark:text-gray-500">Pasang</span>
                    </div>
                </div>
                <div class="absolute -bottom-6 -right-6 text-9xl opacity-5 dark:opacity-10 pointer-events-none transform group-hover:scale-110 transition-transform duration-500">
                    📤
                </div>
            </div>
        </div> {{-- End of Gudang Grid --}}
    </div> {{-- End of GUDANG TAB CONTENT --}}

    {{-- FINANCE TAB CONTENT --}}
    <div x-show="activeTab === 'finance'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
        
        {{-- SECTION 1: METRIK KEUANGAN PERIODE AKTIF --}}
        <div class="mb-12">
            <div class="flex items-center gap-2 mb-6">
                <span class="w-2.5 h-6 bg-teal-500 rounded-full inline-block"></span>
                <h3 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-widest italic">
                    METRIK KEUANGAN PERIODE AKTIF ({{ (isset($startDate) && $startDate) ? $startDate->format('d M Y') . ' s/d ' . $endDate->format('d M Y') : $dateRange }})
                </h3>
            </div>

            {{-- 4 Hero Cards Periode Aktif --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                {{-- Card 1: Total Nilai Tagihan (Periode) --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-xl p-6 relative overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl flex items-center justify-center text-emerald-600 dark:text-emerald-400 font-bold border border-emerald-100 dark:border-emerald-800">
                                🧮
                            </div>
                            <span class="px-3 py-1 bg-emerald-100/60 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 rounded-full text-[10px] font-black uppercase tracking-widest">
                                PERIODE AKTIF
                            </span>
                        </div>
                        <span class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">TOTAL NILAI TAGIHAN</span>
                        <div class="text-3xl font-black text-slate-800 dark:text-white italic tracking-tight mb-2">
                            Rp {{ number_format($financeSummary['total_invoiced'], 0, ',', '.') }}
                        </div>
                    </div>
                    <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">INVOICE DITERBITKAN PERIODE INI</span>
                </div>

                {{-- Card 2: Kas Masuk Tervalidasi (Periode) --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-xl p-6 relative overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-2xl flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold border border-blue-100 dark:border-blue-800">
                                💵
                            </div>
                            <span class="px-3 py-1 bg-blue-100/60 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 rounded-full text-[10px] font-black uppercase tracking-widest">
                                PERIODE AKTIF
                            </span>
                        </div>
                        <span class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">KAS MASUK (TERVALIDASI)</span>
                        <div class="text-3xl font-black text-blue-600 dark:text-blue-400 italic tracking-tight mb-2">
                            Rp {{ number_format($financeSummary['cash_received'], 0, ',', '.') }}
                        </div>
                    </div>
                    <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">PENERIMAAN KAS PERIODE INI</span>
                </div>

                {{-- Card 3: Sisa Piutang Aktif (Periode) --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-xl p-6 relative overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <div class="w-10 h-10 bg-rose-50 dark:bg-rose-900/20 rounded-2xl flex items-center justify-center text-rose-600 dark:text-rose-400 font-bold border border-rose-100 dark:border-rose-800">
                                💲
                            </div>
                            <span class="px-3 py-1 bg-rose-100/60 dark:bg-rose-900/40 text-rose-700 dark:text-rose-300 rounded-full text-[10px] font-black uppercase tracking-widest">
                                PERIODE AKTIF
                            </span>
                        </div>
                        <span class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">SISA PIUTANG AKTIF</span>
                        <div class="text-3xl font-black text-rose-600 dark:text-rose-400 italic tracking-tight mb-2">
                            Rp {{ number_format($financeSummary['active_receivables'], 0, ',', '.') }}
                        </div>
                    </div>
                    <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">BELUM TERTAGIH DARI TAGIHAN PERIODE INI</span>
                </div>

                {{-- Card 4: Rasio Penagihan (Periode) --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-xl p-6 relative overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <div class="w-10 h-10 bg-amber-50 dark:bg-amber-900/20 rounded-2xl flex items-center justify-center text-amber-600 dark:text-amber-400 font-bold border border-amber-100 dark:border-amber-800">
                                🎯
                            </div>
                            <span class="px-3 py-1 bg-amber-100/60 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 rounded-full text-[10px] font-black uppercase tracking-widest">
                                EFEKTIVITAS
                            </span>
                        </div>
                        <span class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">RASIO PENAGIHAN (COLLECTION)</span>
                        <div class="text-3xl font-black text-amber-600 dark:text-amber-400 italic tracking-tight mb-2">
                            {{ $financeSummary['collection_rate'] }}%
                        </div>
                    </div>
                    <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">PENERIMAAN VS TAGIHAN PERIODE INI</span>
                </div>
            </div>

            {{-- Distribusi Status Tagihan (Periode Aktif) --}}
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-xl p-8 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-widest italic">DISTRIBUSI STATUS TAGIHAN PERIODE AKTIF</h3>
                    <span class="px-3 py-1 bg-slate-100 dark:bg-gray-700 text-slate-600 dark:text-slate-300 rounded-full text-[10px] font-black uppercase tracking-widest">
                        TAGIHAN INVOICE PERIODE INI
                    </span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Belum Bayar --}}
                    <div class="bg-slate-50 dark:bg-gray-750 p-6 rounded-2xl border border-slate-100 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-slate-400"></span>
                                <span class="text-xs font-black text-slate-700 dark:text-slate-200 uppercase tracking-wider">BELUM BAYAR</span>
                            </div>
                            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">{{ $financeSummary['status_distribution']['belum_bayar']['count'] }} TRANSAKSI</span>
                        </div>
                        <div class="flex justify-between items-baseline mb-3">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">TOTAL NOMINAL</span>
                            <span class="text-lg font-black text-slate-800 dark:text-white italic">Rp {{ number_format($financeSummary['status_distribution']['belum_bayar']['total'], 0, ',', '.') }}</span>
                        </div>
                        <div class="w-full bg-slate-200 dark:bg-gray-700 h-2 rounded-full overflow-hidden">
                            @php
                                $totalAll = max(1, $financeSummary['total_invoiced']);
                                $pctBelum = min(100, round(($financeSummary['status_distribution']['belum_bayar']['total'] / $totalAll) * 100));
                            @endphp
                            <div class="bg-slate-400 h-full rounded-full" style="width: {{ $pctBelum }}%;"></div>
                        </div>
                    </div>

                    {{-- DP / Cicil --}}
                    <div class="bg-amber-50/40 dark:bg-amber-900/10 p-6 rounded-2xl border border-amber-100 dark:border-amber-900/30">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                                <span class="text-xs font-black text-amber-700 dark:text-amber-400 uppercase tracking-wider">DP/CICIL</span>
                            </div>
                            <span class="text-[10px] font-extrabold text-amber-500/80 uppercase tracking-widest">{{ $financeSummary['status_distribution']['dp_cicil']['count'] }} TRANSAKSI</span>
                        </div>
                        <div class="flex justify-between items-baseline mb-3">
                            <span class="text-[10px] font-black text-amber-500/80 uppercase tracking-widest">TOTAL NOMINAL</span>
                            <span class="text-lg font-black text-amber-800 dark:text-amber-300 italic">Rp {{ number_format($financeSummary['status_distribution']['dp_cicil']['total'], 0, ',', '.') }}</span>
                        </div>
                        <div class="w-full bg-amber-200/50 dark:bg-gray-700 h-2 rounded-full overflow-hidden">
                            @php
                                $pctDp = min(100, round(($financeSummary['status_distribution']['dp_cicil']['total'] / $totalAll) * 100));
                            @endphp
                            <div class="bg-amber-500 h-full rounded-full" style="width: {{ $pctDp }}%;"></div>
                        </div>
                    </div>

                    {{-- Lunas --}}
                    <div class="bg-emerald-50/40 dark:bg-emerald-900/10 p-6 rounded-2xl border border-emerald-100 dark:border-emerald-900/30">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                                <span class="text-xs font-black text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">LUNAS</span>
                            </div>
                            <span class="text-[10px] font-extrabold text-emerald-500/80 uppercase tracking-widest">{{ $financeSummary['status_distribution']['lunas']['count'] }} TRANSAKSI</span>
                        </div>
                        <div class="flex justify-between items-baseline mb-3">
                            <span class="text-[10px] font-black text-emerald-500/80 uppercase tracking-widest">TOTAL NOMINAL</span>
                            <span class="text-lg font-black text-emerald-800 dark:text-emerald-300 italic">Rp {{ number_format($financeSummary['status_distribution']['lunas']['total'], 0, ',', '.') }}</span>
                        </div>
                        <div class="w-full bg-emerald-200/50 dark:bg-gray-700 h-2 rounded-full overflow-hidden">
                            @php
                                $pctLunas = min(100, round(($financeSummary['status_distribution']['lunas']['total'] / $totalAll) * 100));
                            @endphp
                            <div class="bg-emerald-500 h-full rounded-full" style="width: {{ $pctLunas }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Distribusi Type Pembayaran (Periode Aktif) --}}
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-xl p-8 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-widest italic">DISTRIBUSI TYPE PEMBAYARAN PERIODE AKTIF</h3>
                    <span class="px-3 py-1 bg-slate-100 dark:bg-gray-700 text-slate-600 dark:text-slate-300 rounded-full text-[10px] font-black uppercase tracking-widest">
                        PEMBAYARAN TERDRAFT & VERIFIKASI PERIODE INI
                    </span>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    {{-- DP Awal --}}
                    <div class="bg-white dark:bg-gray-750 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-center mb-3">
                                <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs font-bold">⏱️</div>
                                <span class="px-2 py-0.5 bg-blue-100/60 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300 rounded text-[9px] font-black uppercase tracking-widest">BEFORE</span>
                            </div>
                            <span class="block text-[9px] font-black text-gray-400 uppercase tracking-wider mb-1">DP AWAL</span>
                            <div class="text-lg font-black text-blue-600 dark:text-blue-400 italic mb-1">Rp {{ number_format($financeSummary['payment_type_distribution']['dp_awal']['total'], 0, ',', '.') }}</div>
                        </div>
                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $financeSummary['payment_type_distribution']['dp_awal']['count'] }} TRANSAKSI</span>
                    </div>

                    {{-- Pelunasan --}}
                    <div class="bg-white dark:bg-gray-750 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-center mb-3">
                                <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs font-bold">✔️</div>
                                <span class="px-2 py-0.5 bg-emerald-100/60 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-300 rounded text-[9px] font-black uppercase tracking-widest">AFTER</span>
                            </div>
                            <span class="block text-[9px] font-black text-gray-400 uppercase tracking-wider mb-1">PELUNASAN</span>
                            <div class="text-lg font-black text-emerald-600 dark:text-emerald-400 italic mb-1">Rp {{ number_format($financeSummary['payment_type_distribution']['pelunasan']['total'], 0, ',', '.') }}</div>
                        </div>
                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $financeSummary['payment_type_distribution']['pelunasan']['count'] }} TRANSAKSI</span>
                    </div>

                    {{-- Tambah Jasa --}}
                    <div class="bg-white dark:bg-gray-750 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-center mb-3">
                                <div class="w-8 h-8 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xs font-bold">➕</div>
                                <span class="px-2 py-0.5 bg-purple-100/60 dark:bg-purple-900/40 text-purple-600 dark:text-purple-300 rounded text-[9px] font-black uppercase tracking-widest">TAMBAH JASA</span>
                            </div>
                            <span class="block text-[9px] font-black text-gray-400 uppercase tracking-wider mb-1">TAMBAH JASA</span>
                            <div class="text-lg font-black text-purple-600 dark:text-purple-400 italic mb-1">Rp {{ number_format($financeSummary['payment_type_distribution']['tambah_jasa']['total'], 0, ',', '.') }}</div>
                        </div>
                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $financeSummary['payment_type_distribution']['tambah_jasa']['count'] }} TRANSAKSI</span>
                    </div>

                    {{-- Lunas Awal --}}
                    <div class="bg-white dark:bg-gray-750 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-center mb-3">
                                <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs font-bold">⚡</div>
                                <span class="px-2 py-0.5 bg-amber-100/60 dark:bg-amber-900/40 text-amber-600 dark:text-amber-300 rounded text-[9px] font-black uppercase tracking-widest">LUNAS AWAL</span>
                            </div>
                            <span class="block text-[9px] font-black text-gray-400 uppercase tracking-wider mb-1">LUNAS AWAL</span>
                            <div class="text-lg font-black text-amber-600 dark:text-amber-400 italic mb-1">Rp {{ number_format($financeSummary['payment_type_distribution']['lunas_awal']['total'], 0, ',', '.') }}</div>
                        </div>
                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $financeSummary['payment_type_distribution']['lunas_awal']['count'] }} TRANSAKSI</span>
                    </div>

                    {{-- Ongkos Kirim --}}
                    <div class="bg-white dark:bg-gray-750 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-center mb-3">
                                <div class="w-8 h-8 rounded-xl bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xs font-bold">📦</div>
                                <span class="px-2 py-0.5 bg-rose-100/60 dark:bg-rose-900/40 text-rose-600 dark:text-rose-300 rounded text-[9px] font-black uppercase tracking-widest">ONGKIR</span>
                            </div>
                            <span class="block text-[9px] font-black text-gray-400 uppercase tracking-wider mb-1">ONGKOS KIRIM</span>
                            <div class="text-lg font-black text-rose-600 dark:text-rose-400 italic mb-1">Rp {{ number_format($financeSummary['payment_type_distribution']['ongkir']['total'], 0, ',', '.') }}</div>
                        </div>
                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $financeSummary['payment_type_distribution']['ongkir']['count'] }} TRANSAKSI</span>
                    </div>

                    {{-- Pembayaran OTO --}}
                    <div class="bg-white dark:bg-gray-750 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-center mb-3">
                                <div class="w-8 h-8 rounded-xl bg-pink-50 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400 flex items-center justify-center text-xs font-bold">⚡</div>
                                <span class="px-2 py-0.5 bg-pink-100/60 dark:bg-pink-900/40 text-pink-600 dark:text-pink-300 rounded text-[9px] font-black uppercase tracking-widest">OTO</span>
                            </div>
                            <span class="block text-[9px] font-black text-gray-400 uppercase tracking-wider mb-1">PEMBAYARAN OTO</span>
                            <div class="text-lg font-black text-pink-600 dark:text-pink-400 italic mb-1">Rp {{ number_format($financeSummary['payment_type_distribution']['oto']['total'], 0, ',', '.') }}</div>
                        </div>
                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $financeSummary['payment_type_distribution']['oto']['count'] }} TRANSAKSI</span>
                    </div>
                </div>
            </div>

            {{-- Summary Cards (Revenue Realization & Total Diskon) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 max-w-2xl">
                {{-- Revenue Realization --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-xl p-8 relative overflow-hidden border-t-4 border-t-purple-500 flex flex-col justify-between">
                    <div>
                        <span class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">REVENUE REALIZATION</span>
                        <div class="text-3xl xl:text-4xl font-black text-slate-800 dark:text-white tracking-tight mb-4">
                            Rp {{ number_format($financeSummary['revenue_realization'], 0, ',', '.') }}
                        </div>
                    </div>
                    <span class="inline-block w-max px-4 py-1.5 bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-300 rounded-full text-[10px] font-black uppercase tracking-widest border border-purple-100 dark:border-purple-800">
                        OMSET CLOSING VALID PERIODE INI
                    </span>
                </div>

                {{-- Total Diskon Diberikan --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-xl p-8 relative overflow-hidden border-t-4 border-t-amber-500 flex flex-col justify-between">
                    <div>
                        <span class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">TOTAL DISKON DIBERIKAN</span>
                        <div class="text-3xl xl:text-4xl font-black text-amber-600 dark:text-amber-400 tracking-tight mb-4">
                            Rp {{ number_format($financeSummary['total_discount'], 0, ',', '.') }}
                        </div>
                    </div>
                    <span class="inline-block w-max px-4 py-1.5 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-300 rounded-full text-[10px] font-black uppercase tracking-widest border border-amber-100 dark:border-amber-800">
                        DISKON INVOICE PERIODE INI
                    </span>
                </div>
            </div>
        </div>

    </div> {{-- End of FINANCE TAB CONTENT --}}

    {{-- CS TAB CONTENT --}}
    <div x-show="activeTab === 'cs'" style="display: none;" class="space-y-8">
        
        {{-- SECTION 1: GLOBAL OVERVIEW METRICS --}}
        <div>
            <div class="flex items-center gap-3 mb-6">
                <div class="w-1.5 h-6 bg-emerald-500 rounded-full"></div>
                <h2 class="text-xs font-black text-gray-800 dark:text-gray-200 uppercase tracking-[0.2em]">GLOBAL OVERVIEW METRICS</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
                {{-- Total Lead Intake --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 shadow-xl border-t-4 border-t-emerald-500">
                    <p class="text-[10px] font-black text-gray-400 dark:text-gray-400 uppercase tracking-widest mb-3">TOTAL LEAD INTAKE</p>
                    <p class="text-4xl font-black text-slate-800 dark:text-white tracking-tight mb-4">{{ number_format($csSummary['overview']['total_leads']) }}</p>
                    <span class="inline-block px-3 py-1 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-full text-[9px] font-black uppercase tracking-widest border border-emerald-100 dark:border-emerald-800">
                        INPUT PERIODE INI
                    </span>
                </div>

                {{-- Total Closing --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 shadow-xl border-t-4 border-t-amber-400">
                    <p class="text-[10px] font-black text-gray-400 dark:text-gray-400 uppercase tracking-widest mb-3">TOTAL CLOSING</p>
                    <p class="text-4xl font-black text-slate-800 dark:text-white tracking-tight mb-4">{{ number_format($csSummary['overview']['total_closings']) }}</p>
                    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2 overflow-hidden flex items-center mb-1">
                        <div class="bg-amber-400 h-2 rounded-full" style="width: {{ min(100, $csSummary['overview']['conversion_rate']) }}%"></div>
                    </div>
                    <p class="text-right text-[10px] font-black text-amber-500">{{ $csSummary['overview']['conversion_rate'] }}%</p>
                </div>

                {{-- Total Sepatu Masuk --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 shadow-xl border-t-4 border-t-cyan-400">
                    <p class="text-[10px] font-black text-gray-400 dark:text-gray-400 uppercase tracking-widest mb-3">TOTAL SEPATU MASUK</p>
                    <p class="text-4xl font-black text-slate-800 dark:text-white tracking-tight mb-4">{{ number_format($csSummary['overview']['total_incoming_items']) }}</p>
                    <span class="inline-block px-3 py-1 bg-cyan-50 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400 rounded-full text-[9px] font-black uppercase tracking-widest border border-cyan-100 dark:border-cyan-800">
                        VOLUME FISIK MASUK
                    </span>
                </div>

                {{-- Revenue Realization --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 shadow-xl border-t-4 border-t-indigo-500">
                    <p class="text-[10px] font-black text-gray-400 dark:text-gray-400 uppercase tracking-widest mb-3">REVENUE REALIZATION</p>
                    <p class="text-3xl font-black text-slate-800 dark:text-white tracking-tight mb-4">Rp {{ number_format($csSummary['overview']['total_revenue'], 0, ',', '.') }}</p>
                    <span class="inline-block px-3 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-full text-[9px] font-black uppercase tracking-widest border border-indigo-100 dark:border-indigo-800">
                        OMSET CLOSING VALID
                    </span>
                </div>

                {{-- Avg Deal Value --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 shadow-xl border-t-4 border-t-pink-500">
                    <p class="text-[10px] font-black text-gray-400 dark:text-gray-400 uppercase tracking-widest mb-3">AVG DEAL VALUE</p>
                    <p class="text-3xl font-black text-slate-800 dark:text-white tracking-tight mb-4">Rp {{ number_format($csSummary['overview']['avg_deal_value'], 0, ',', '.') }}</p>
                    <span class="inline-block px-3 py-1 bg-pink-50 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400 rounded-full text-[9px] font-black uppercase tracking-widest border border-pink-100 dark:border-pink-800">
                        RATA-RATA PER DEAL
                    </span>
                </div>
            </div>
        </div>

        {{-- SECTION 2: CLOSING PATH ANALYSIS --}}
        <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-1.5 h-6 bg-amber-500 rounded-full"></div>
                <h2 class="text-xs font-black text-gray-800 dark:text-gray-200 uppercase tracking-[0.2em]">CLOSING PATH ANALYSIS</h2>
            </div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-6 ml-4">JALUR LEAD MENUJU CLOSING</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                {{-- Closing Langsung --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 shadow-xl border-t-4 border-t-emerald-500 relative overflow-hidden">
                    <svg class="w-16 h-16 absolute -right-3 top-4 text-emerald-100 dark:text-emerald-900/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    <p class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mb-3">CLOSING LANGSUNG</p>
                    <p class="text-4xl font-black text-slate-800 dark:text-white tracking-tight mb-4">{{ number_format($csSummary['path_analysis']['closed_direct']) }}</p>
                    <p class="text-[10px] font-bold text-gray-400 italic">Konsultasi → Closing (tanpa Follow-up)</p>
                </div>

                {{-- Closing via Follow-up --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 shadow-xl border-t-4 border-t-orange-400 relative overflow-hidden">
                    <svg class="w-16 h-16 absolute -right-3 top-4 text-orange-100 dark:text-orange-900/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <p class="text-[10px] font-black text-orange-500 uppercase tracking-widest mb-3">CLOSING VIA FOLLOW-UP</p>
                    <p class="text-4xl font-black text-slate-800 dark:text-white tracking-tight mb-4">{{ number_format($csSummary['path_analysis']['closed_via_followup']) }}</p>
                    <p class="text-[10px] font-bold text-gray-400 italic">Konsultasi → Follow-up → Closing</p>
                </div>

                {{-- Konsultasi -> Follow-up --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 shadow-xl border-t-4 border-t-amber-400">
                    <p class="text-[10px] font-black text-amber-600 dark:text-amber-400 uppercase tracking-widest mb-3">KONSULTASI → FOLLOW-UP</p>
                    <p class="text-4xl font-black text-slate-800 dark:text-white tracking-tight mb-4">{{ number_format($csSummary['path_analysis']['total_to_followup']) }}</p>
                    <p class="text-[10px] font-bold text-gray-400 italic mb-3">Total lead yang masuk tahap Follow-up</p>
                    <span class="inline-block px-3 py-1 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-full text-[9px] font-black uppercase tracking-widest border border-amber-100 dark:border-amber-800">
                        EFEKTIVITAS: {{ $csSummary['path_analysis']['followup_effectiveness'] }}%
                    </span>
                </div>

                {{-- Follow-up Aktif --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 shadow-xl border-t-4 border-t-sky-400">
                    <p class="text-[10px] font-black text-sky-500 uppercase tracking-widest mb-3">FOLLOW-UP AKTIF</p>
                    <p class="text-4xl font-black text-slate-800 dark:text-white tracking-tight mb-4">{{ number_format($csSummary['path_analysis']['active_followup']) }}</p>
                    <p class="text-[10px] font-bold text-gray-400 italic mb-3">Saat ini masih di tahap Follow-up</p>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-sky-50 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 rounded-full text-[9px] font-black uppercase tracking-widest border border-sky-100 dark:border-sky-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-sky-500 animate-pulse"></span> LIVE COUNT
                    </span>
                </div>
            </div>
        </div>

        {{-- SECTION 3: CHANNEL & SUMMARY CARDS --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Channel Card --}}
            <div class="lg:col-span-1 bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 shadow-xl">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-1.5 h-6 bg-indigo-500 rounded-full"></div>
                    <h2 class="text-xs font-black text-gray-800 dark:text-gray-200 uppercase tracking-[0.2em]">CHANNEL</h2>
                </div>

                <div class="space-y-6">
                    {{-- Online --}}
                    <div class="p-4 bg-gray-50 dark:bg-gray-750 rounded-2xl border border-gray-100 dark:border-gray-700">
                        <div class="flex justify-between items-center mb-1">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                                <span class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-wider">ONLINE</span>
                            </div>
                            <span class="text-xl font-black text-gray-900 dark:text-white">{{ number_format($csSummary['channel_stats']['ONLINE']['leads']) }} <span class="text-[9px] text-gray-400 font-bold">LEADS</span></span>
                        </div>
                        <p class="text-[10px] font-bold text-gray-400 mb-2">REV: RP {{ number_format($csSummary['channel_stats']['ONLINE']['revenue'], 0, ',', '.') }}</p>
                        
                        <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-1.5 overflow-hidden mb-2">
                            <div class="bg-indigo-500 h-1.5 rounded-full" style="width: {{ min(100, $csSummary['channel_stats']['ONLINE']['cr']) }}%"></div>
                        </div>
                        
                        <div class="flex justify-between text-[10px] font-bold">
                            <span class="text-gray-500">Closing: {{ number_format($csSummary['channel_stats']['ONLINE']['closings']) }}</span>
                            <span class="text-emerald-500">CR: {{ $csSummary['channel_stats']['ONLINE']['cr'] }}%</span>
                        </div>
                    </div>

                    {{-- Offline --}}
                    <div class="p-4 bg-gray-50 dark:bg-gray-750 rounded-2xl border border-gray-100 dark:border-gray-700">
                        <div class="flex justify-between items-center mb-1">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                <span class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-wider">OFFLINE</span>
                            </div>
                            <span class="text-xl font-black text-gray-900 dark:text-white">{{ number_format($csSummary['channel_stats']['OFFLINE']['leads']) }} <span class="text-[9px] text-gray-400 font-bold">LEADS</span></span>
                        </div>
                        <p class="text-[10px] font-bold text-gray-400 mb-2">REV: RP {{ number_format($csSummary['channel_stats']['OFFLINE']['revenue'], 0, ',', '.') }}</p>
                        
                        <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-1.5 overflow-hidden mb-2">
                            <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ min(100, $csSummary['channel_stats']['OFFLINE']['cr']) }}%"></div>
                        </div>
                        
                        <div class="flex justify-between text-[10px] font-bold">
                            <span class="text-gray-500">Closing: {{ number_format($csSummary['channel_stats']['OFFLINE']['closings']) }}</span>
                            <span class="text-emerald-500">CR: {{ $csSummary['channel_stats']['OFFLINE']['cr'] }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Summary Cards (Sepatu Diterima & SPK Pending) --}}
            <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-6">
                {{-- Sepatu Diterima Card --}}
                <div class="bg-slate-900 rounded-3xl p-6 border border-slate-800 shadow-xl flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-6">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">TOTAL SEPATU DITERIMA</p>
                            <div class="w-8 h-8 rounded-xl bg-teal-500/20 text-teal-400 flex items-center justify-center border border-teal-500/30">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </div>
                        <p class="text-4xl font-black text-white tracking-tight mb-2">{{ number_format($csSummary['summary_cards']['total_sepatu_diterima']) }} <span class="text-lg font-bold text-slate-400">Pasang</span></p>
                    </div>
                    <p class="text-xs font-black text-cyan-400 tracking-wider">{{ $csSummary['summary_cards']['sepatu_diterima_online'] }} OL / {{ $csSummary['summary_cards']['sepatu_diterima_offline'] }} OFF</p>
                </div>

                {{-- SPK Pending Card --}}
                <div class="bg-slate-900 rounded-3xl p-6 border border-slate-800 shadow-xl flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-6">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">TOTAL SPK PENDING</p>
                            <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center border border-amber-500/30">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        </div>
                        <p class="text-4xl font-black text-white tracking-tight mb-2">{{ number_format($csSummary['summary_cards']['total_spk_pending']) }} <span class="text-lg font-bold text-slate-400">Pasang</span></p>
                    </div>
                    <p class="text-xs font-black text-amber-400 tracking-wider">BELUM DI-RECEIVE WORKSHOP</p>
                </div>
            </div>
        </div>

        {{-- SECTION 4: LEADERBOARD TABLE --}}
        <div class="bg-slate-900 rounded-3xl border border-slate-800 shadow-2xl overflow-hidden p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-1.5 h-6 bg-cyan-400 rounded-full"></div>
                    <h2 class="text-xs font-black text-white uppercase tracking-[0.2em]">RANGKING EFISIENSI & HASIL CS</h2>
                </div>
                <div class="flex items-center gap-4 text-[10px] font-black uppercase tracking-wider text-slate-400">
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-400"></span> ONLINE</span>
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-purple-400"></span> OFFLINE</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-[9px] font-black text-slate-400 uppercase tracking-widest">
                            <th class="py-4 px-3">RANK</th>
                            <th class="py-4 px-3">CS AGENT</th>
                            <th class="py-4 px-3">INTAKE (SEPATU MASUK)</th>
                            <th class="py-4 px-3">CLOSING (CONVERTED)</th>
                            <th class="py-4 px-3">SEPATU DITERIMA</th>
                            <th class="py-4 px-3">SPK PENDING</th>
                            <th class="py-4 px-3">BATAL (TRASH)</th>
                            <th class="py-4 px-3 text-right">REVENUE</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @foreach($csSummary['leaderboard'] as $index => $cs)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            {{-- Rank --}}
                            <td class="py-4 px-3 font-black text-sm text-slate-300">
                                @if($index === 0)
                                    <span class="w-7 h-7 rounded-full bg-amber-500/20 text-amber-400 border border-amber-500/30 inline-flex items-center justify-center">🥇</span>
                                @elseif($index === 1)
                                    <span class="w-7 h-7 rounded-full bg-slate-300/20 text-slate-300 border border-slate-400/30 inline-flex items-center justify-center">🥈</span>
                                @elseif($index === 2)
                                    <span class="w-7 h-7 rounded-full bg-amber-700/20 text-amber-600 border border-amber-600/30 inline-flex items-center justify-center">🥉</span>
                                @else
                                    <span class="w-7 h-7 rounded-lg bg-slate-800 text-slate-400 inline-flex items-center justify-center text-xs font-bold">{{ $index + 1 }}</span>
                                @endif
                            </td>

                            {{-- Agent Name & Avatar --}}
                            <td class="py-4 px-3 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-amber-500 to-orange-500 text-white font-black text-xs flex items-center justify-center shadow-sm">
                                        {{ $cs['avatar_initial'] }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-white tracking-tight">{{ $cs['cs_name'] }}</p>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">TOTAL {{ $cs['total_leads'] }} LEADS</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Intake --}}
                            <td class="py-4 px-3 whitespace-nowrap">
                                <p class="text-sm font-black text-white">{{ $cs['incoming_total'] }} Psg</p>
                                <p class="text-[10px] font-bold text-emerald-400">{{ $cs['incoming_online'] }} OL <span class="text-slate-500">•</span> <span class="text-purple-400">{{ $cs['incoming_offline'] }} OFF</span></p>
                            </td>

                            {{-- Closing --}}
                            <td class="py-4 px-3 whitespace-nowrap">
                                <p class="text-sm font-black text-white">{{ $cs['closings'] }} Closing</p>
                                <p class="text-[10px] font-bold text-emerald-400">DIR: {{ $cs['closing_direct'] }} <span class="text-slate-500">•</span> <span class="text-amber-400">FU: {{ $cs['closing_via_fu'] }}</span></p>
                            </td>

                            {{-- Sepatu Diterima --}}
                            <td class="py-4 px-3 whitespace-nowrap">
                                <p class="text-sm font-black text-white">{{ $cs['diterima_total'] }} Psg</p>
                                <p class="text-[10px] font-bold text-emerald-400">{{ $cs['diterima_online'] }} OL <span class="text-slate-500">•</span> <span class="text-purple-400">{{ $cs['diterima_offline'] }} OFF</span></p>
                            </td>

                            {{-- SPK Pending --}}
                            <td class="py-4 px-3 whitespace-nowrap">
                                <span class="text-sm font-black text-amber-400">{{ $cs['spk_pending'] }} Psg</span>
                            </td>

                            {{-- Batal --}}
                            <td class="py-4 px-3 whitespace-nowrap">
                                <span class="text-sm font-black text-red-400">{{ $cs['batal'] }} Psg</span>
                            </td>

                            {{-- Revenue --}}
                            <td class="py-4 px-3 whitespace-nowrap text-right">
                                <p class="text-sm font-black text-emerald-400">Rp {{ number_format($cs['revenue'], 0, ',', '.') }}</p>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">AIO: {{ $cs['aio'] }} PSG/ORDER</p>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div> {{-- End of CS TAB CONTENT --}}

    {{-- API Documentation Modal --}}
    <div x-show="showApiModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showApiModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="showApiModal = false" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showApiModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-100 dark:border-gray-700">
                <div class="px-6 pt-5 pb-4 sm:p-8 sm:pb-6">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-teal-100 dark:bg-teal-900/30 sm:mx-0 sm:h-12 sm:w-12 border border-teal-200 dark:border-teal-800">
                            <svg class="h-6 w-6 text-teal-600 dark:text-teal-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-5 sm:text-left w-full">
                            <h3 class="text-xl leading-6 font-black text-gray-900 dark:text-white mb-2" id="modal-title" x-text="activeTab === 'workshop' ? 'API Integration (Workshop KPI)' : (activeTab === 'gudang' ? 'API Integration (Gudang KPI)' : (activeTab === 'finance' ? 'API Integration (Finance KPI)' : 'API Integration (CS KPI)'))">API Integration</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                                Gunakan *endpoint* ini untuk menarik data laporan KPI secara terprogram.
                            </p>
                            
                            <div class="mb-5">
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-widest mb-2">Endpoint URL (GET)</label>
                                <div class="relative bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-4 pr-24 shadow-inner">
                                    <div class="text-sm font-mono text-gray-800 dark:text-green-400 break-all" id="apiUrlText" x-text="activeTab === 'workshop' ? '{{ url('/api/v1/kpi/workshop') }}{{ $dateRange ? '?date_range='.urlencode($dateRange).'&' : '?' }}api_key={{ env('KPI_API_KEY', 'kuncirahasia123') }}' : (activeTab === 'gudang' ? '{{ url('/api/v1/kpi/gudang') }}{{ $dateRange ? '?date_range='.urlencode($dateRange).'&' : '?' }}api_key={{ env('KPI_API_KEY', 'kuncirahasia123') }}' : (activeTab === 'finance' ? '{{ url('/api/v1/kpi/finance') }}{{ $dateRange ? '?date_range='.urlencode($dateRange).'&' : '?' }}api_key={{ env('KPI_API_KEY', 'kuncirahasia123') }}' : '{{ url('/api/v1/kpi/cs') }}{{ $dateRange ? '?date_range='.urlencode($dateRange).'&' : '?' }}api_key={{ env('KPI_API_KEY', 'kuncirahasia123') }}'))">
                                        {{ url('/api/v1/kpi/workshop') }}{{ $dateRange ? '?date_range='.urlencode($dateRange).'&' : '?' }}api_key={{ env('KPI_API_KEY', 'kuncirahasia123') }}
                                    </div>
                                    <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('apiUrlText').innerText.trim()); alert('URL tersalin!')" class="absolute top-1/2 -translate-y-1/2 right-3 inline-flex items-center px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 font-bold text-xs uppercase transition-colors shadow-sm">
                                        Copy
                                    </button>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-widest mb-2">Required Headers</label>
                                <div class="bg-gray-900 rounded-xl p-4 overflow-x-auto border border-gray-700">
                                    <pre class="text-sm font-mono text-green-400"><code>Authorization: Bearer {{ env('KPI_API_KEY', 'kuncirahasia123') }}
Accept: application/json</code></pre>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 font-medium">
                                    * Atur variabel <code class="text-pink-500 bg-pink-50 dark:bg-pink-900/30 px-1 py-0.5 rounded">KPI_API_KEY</code> di file .env server Anda.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-750 px-4 py-4 sm:px-8 sm:flex sm:flex-row-reverse border-t border-gray-200 dark:border-gray-700">
                    <button type="button" @click="showApiModal = false" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-teal-600 text-base font-black text-white hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 sm:ml-3 sm:w-auto sm:text-sm uppercase tracking-widest transition-all">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("#date-range", {
            mode: "range",
            dateFormat: "Y-m-d",
            onReady: function(selectedDates, dateStr, instance) {
                // Ensure styling dark mode elements if needed
            }
        });
    });
</script>
</x-app-layout>
