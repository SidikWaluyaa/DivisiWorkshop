<x-app-layout>
<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<!-- Flatpickr Theme Dark if needed, but let's use standard and customize -->
<style>
    .flatpickr-calendar {
        background: #1f2937 !important;
        border: 1px solid #374151 !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3) !important;
        border-radius: 1.5rem !important;
    }
    .flatpickr-day {
        color: #e5e7eb !important;
        border-radius: 0.75rem !important;
    }
    .flatpickr-day.today {
        border-color: #14b8a6 !important;
    }
    .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange {
        background: #14b8a6 !important;
        border-color: #14b8a6 !important;
        color: #ffffff !important;
    }
    .flatpickr-day:hover {
        background: #374151 !important;
    }
    .flatpickr-day.inRange {
        background: rgba(20, 184, 166, 0.15) !important;
        border-color: rgba(20, 184, 166, 0.1) !important;
        box-shadow: -5px 0 0 rgba(20, 184, 166, 0.15), 5px 0 0 rgba(20, 184, 166, 0.15) !important;
        color: #14b8a6 !important;
    }
    .flatpickr-day.inRange:hover {
        background: rgba(20, 184, 166, 0.25) !important;
    }
    .flatpickr-months .flatpickr-month, .flatpickr-current-month .flatpickr-monthDropdown-months {
        color: #ffffff !important;
        fill: #ffffff !important;
    }
    .flatpickr-weekday {
        color: #9ca3af !important;
    }
    .flatpickr-calendar .flatpickr-innerContainer {
        padding: 0.5rem !important;
    }
</style>

<div class="container mx-auto px-4 py-8 max-w-7xl" x-data="{ activeTab: 'workshop' }">
    
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-1.5 h-8 rounded-full transition-colors" :class="activeTab === 'workshop' ? 'bg-teal-500' : 'bg-amber-500'"></div>
                <h1 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight" x-text="activeTab === 'workshop' ? 'KPI WORKSHOP' : 'KPI GUDANG'"></h1>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest mt-1 ml-4" x-text="activeTab === 'workshop' ? 'Ringkasan Kinerja & Beban Kerja Divisi Workshop' : 'Ringkasan Kinerja & Logistik Gudang'"></p>
        </div>
        
        {{-- Export Excel Button --}}
        <a href="{{ route('admin.kpi.export', request()->all()) }}" 
           x-show="activeTab === 'workshop'"
           class="inline-flex items-center gap-2.5 px-6 py-3.5 bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white rounded-2xl text-xs font-black shadow-lg shadow-teal-500/20 transition-all hover:scale-105 active:scale-95 uppercase tracking-widest">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Ekspor Excel
        </a>
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
    </div>

    {{-- Filter Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-xl p-6 mb-8">
        <form method="GET" action="{{ route('admin.kpi.index') }}" class="flex flex-col sm:flex-row items-end gap-4">
            
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

    {{-- GUDANG TAB CONTENT (PLACEHOLDER) --}}
    <div x-show="activeTab === 'gudang'" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
        <div class="bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-900/30 rounded-3xl p-12 text-center flex flex-col items-center justify-center min-h-[400px]">
            <div class="w-24 h-24 bg-amber-100 dark:bg-amber-900/20 rounded-full flex items-center justify-center text-5xl mb-6 shadow-inner shadow-amber-500/20">
                🚧
            </div>
            <h2 class="text-2xl font-black text-amber-800 dark:text-amber-400 mb-3 tracking-tight">Area Konstruksi KPI Gudang</h2>
            <p class="text-amber-700/80 dark:text-amber-500/80 font-medium max-w-lg mx-auto text-sm leading-relaxed">
                Data dan metrik untuk KPI Divisi Gudang saat ini sedang dalam tahap perancangan. Area ini akan segera diisi dengan laporan performa logistik dan persediaan barang Anda.
            </p>
        </div>
    </div> {{-- End of GUDANG TAB CONTENT --}}

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
