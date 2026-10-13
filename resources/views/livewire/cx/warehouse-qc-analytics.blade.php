<div class="p-4 sm:p-6 lg:p-8 space-y-6">

    {{-- Flatpickr Styles & Scripts --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>

    <style>
        .flatpickr-calendar {
            border-radius: 1rem !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
            border: 1px solid #e5e7eb !important;
            font-family: inherit !important;
            z-index: 9999 !important;
        }
        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange {
            background: #059669 !important;
            border-color: #059669 !important;
        }
        .flatpickr-day.inRange {
            background: #d1fae5 !important;
            border-color: #a7f3d0 !important;
            color: #065f46 !important;
        }
        .flatpickr-day.today {
            border-color: #059669 !important;
        }
    </style>

    {{-- Top Header & Action Controls --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 mb-1">
                <span>Divisi Customer Care (CC / CX)</span>
                <span>•</span>
                <span>Audit & Kontrol Kualitas</span>
            </div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight flex items-center gap-3">
                <span class="p-2 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                Analisis Warehouse QC (QC Before)
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">
                Monitoring kualitas unit masuk penerimaan, rasio kelulusan QC awal, dan kontribusi rekapan per agen CS.
            </p>
        </div>

        {{-- Export Action Buttons --}}
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('cx.warehouse-qc.export-excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
               target="_blank"
               class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm hover:shadow transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Export Excel</span>
            </a>

            <a href="{{ route('cx.warehouse-qc.export-pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
               target="_blank"
               class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-sm hover:shadow transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Export PDF</span>
            </a>

            <button type="button" 
                    wire:click="resetFilters"
                    class="p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 transition-colors"
                    title="Reset Filter">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Filter Bar: Quick Presets & Interactive Flatpickr Date Range Picker --}}
    <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 flex flex-col md:flex-row md:items-center justify-between gap-4">
        {{-- Quick Presets --}}
        <div class="flex flex-wrap items-center gap-1.5">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 mr-1 hidden sm:inline">Preset:</span>
            <button type="button" 
                    wire:click="setPreset('this_month')"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all {{ $preset === 'this_month' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200' }}">
                Bulan Ini
            </button>
            <button type="button" 
                    wire:click="setPreset('last_month')"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all {{ $preset === 'last_month' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200' }}">
                Bulan Lalu
            </button>
            <button type="button" 
                    wire:click="setPreset('last_7_days')"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all {{ $preset === 'last_7_days' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200' }}">
                7 Hari
            </button>
            <button type="button" 
                    wire:click="setPreset('last_30_days')"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all {{ $preset === 'last_30_days' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200' }}">
                30 Hari
            </button>
        </div>

        {{-- Date Range Picker (Flatpickr Integrated) --}}
        <div class="relative" 
             wire:ignore
             x-data="{
                initPicker() {
                    flatpickr(this.$refs.rangeInput, {
                        mode: 'range',
                        dateFormat: 'Y-m-d',
                        defaultDate: ['{{ $startDate }}', '{{ $endDate }}'],
                        locale: 'id',
                        onChange: (selectedDates, dateStr, instance) => {
                            if (selectedDates.length === 2) {
                                let start = instance.formatDate(selectedDates[0], 'Y-m-d');
                                let end = instance.formatDate(selectedDates[1], 'Y-m-d');
                                $wire.setDateRange(start, end);
                            }
                        }
                    });
                }
             }" 
             x-init="initPicker()">
            <button @click="$refs.rangeInput._flatpickr.open()" 
                    type="button" 
                    class="px-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 hover:border-emerald-500 rounded-xl text-xs font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2 shadow-sm transition-all group">
                <svg class="w-4 h-4 text-emerald-600 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="font-mono text-emerald-700 dark:text-emerald-400">{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</span>
                <span class="text-gray-400 font-normal">s/d</span>
                <span class="font-mono text-emerald-700 dark:text-emerald-400">{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</span>
                <svg class="w-3.5 h-3.5 text-gray-400 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <input x-ref="rangeInput" type="text" class="hidden" />
        </div>
    </div>

    {{-- Banner Metrik Utama (Klik Card untuk Membuka Rincian SPK) --}}
    @php
        $sum = $analytics['summary'];
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
        
        {{-- 1. Jumlah Sepatu Masuk --}}
        <div wire:click="filterSpkByStatus('all')"
             class="group relative overflow-hidden rounded-2xl p-4 sm:p-5 bg-gradient-to-br from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-500/10 cursor-pointer hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <div class="flex items-center justify-between opacity-90 mb-2">
                <span class="text-[10px] sm:text-xs font-black uppercase tracking-wider">Sepatu Masuk</span>
                <svg class="w-4 h-4 opacity-80 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <div class="text-2xl sm:text-3xl font-black tracking-tight">
                {{ number_format($sum['total_masuk']) }}
            </div>
            <div class="text-[10px] sm:text-xs text-emerald-100 font-semibold mt-1 flex items-center justify-between">
                <span>Unit Periode Aktif</span>
                <span class="opacity-0 group-hover:opacity-100 transition-opacity text-[10px]">Lihat SPK →</span>
            </div>
        </div>

        {{-- 2. Jumlah Lolos (Tanpa Catatan) --}}
        <div wire:click="filterSpkByStatus('lolos')"
             class="group relative overflow-hidden rounded-2xl p-4 sm:p-5 bg-gradient-to-br from-green-500 to-green-600 text-white shadow-md shadow-green-500/10 cursor-pointer hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <div class="flex items-center justify-between opacity-90 mb-2">
                <span class="text-[10px] sm:text-xs font-black uppercase tracking-wider">Jumlah Lolos</span>
                <span class="px-1.5 py-0.5 text-[9px] font-bold rounded bg-white/20 backdrop-blur-sm">
                    {{ $sum['pct_lolos'] }}%
                </span>
            </div>
            <div class="text-2xl sm:text-3xl font-black tracking-tight">
                {{ number_format($sum['total_lolos']) }}
            </div>
            <div class="text-[10px] sm:text-xs text-green-100 font-semibold mt-1 flex items-center justify-between">
                <span>Lolos Tanpa Catatan</span>
                <span class="opacity-0 group-hover:opacity-100 transition-opacity text-[10px]">Lihat SPK →</span>
            </div>
        </div>

        {{-- 3. Lolos Catatan --}}
        <div wire:click="filterSpkByStatus('lolos_catatan')"
             class="group relative overflow-hidden rounded-2xl p-4 sm:p-5 bg-gradient-to-br from-amber-500 to-amber-600 text-white shadow-md shadow-amber-500/10 cursor-pointer hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <div class="flex items-center justify-between opacity-90 mb-2">
                <span class="text-[10px] sm:text-xs font-black uppercase tracking-wider">Lolos Catatan</span>
                <span class="px-1.5 py-0.5 text-[9px] font-bold rounded bg-white/20 backdrop-blur-sm">
                    {{ $sum['pct_lolos_catatan'] }}%
                </span>
            </div>
            <div class="text-2xl sm:text-3xl font-black tracking-tight">
                {{ number_format($sum['total_lolos_catatan']) }}
            </div>
            <div class="text-[10px] sm:text-xs text-amber-100 font-semibold mt-1 flex items-center justify-between">
                <span>Rekomendasi / Upsell</span>
                <span class="opacity-0 group-hover:opacity-100 transition-opacity text-[10px]">Lihat SPK →</span>
            </div>
        </div>

        {{-- 4. Jumlah Tolak --}}
        <div wire:click="filterSpkByStatus('tolak')"
             class="group relative overflow-hidden rounded-2xl p-4 sm:p-5 bg-gradient-to-br from-rose-600 to-rose-700 text-white shadow-md shadow-rose-500/10 cursor-pointer hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <div class="flex items-center justify-between opacity-90 mb-2">
                <span class="text-[10px] sm:text-xs font-black uppercase tracking-wider">Jumlah Tolak</span>
                <span class="px-1.5 py-0.5 text-[9px] font-bold rounded bg-white/20 backdrop-blur-sm">
                    {{ $sum['pct_tolak'] }}%
                </span>
            </div>
            <div class="text-2xl sm:text-3xl font-black tracking-tight">
                {{ number_format($sum['total_tolak']) }}
            </div>
            <div class="text-[10px] sm:text-xs text-rose-100 font-semibold mt-1 flex items-center justify-between">
                <span>Reject / Tolak QC Awal</span>
                <span class="opacity-0 group-hover:opacity-100 transition-opacity text-[10px]">Lihat SPK →</span>
            </div>
        </div>

        {{-- 5. Total QC Selesai --}}
        <div wire:click="filterSpkByStatus('all')"
             class="group relative overflow-hidden rounded-2xl p-4 sm:p-5 bg-gradient-to-br from-teal-600 to-teal-700 text-white shadow-md shadow-teal-500/10 cursor-pointer hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <div class="flex items-center justify-between opacity-90 mb-2">
                <span class="text-[10px] sm:text-xs font-black uppercase tracking-wider">Total QC</span>
                <svg class="w-4 h-4 opacity-80 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
            </div>
            <div class="text-2xl sm:text-3xl font-black tracking-tight">
                {{ number_format($sum['total_qc']) }}
            </div>
            <div class="text-[10px] sm:text-xs text-teal-100 font-semibold mt-1 flex items-center justify-between">
                <span>Total Selesai Di-QC</span>
                <span class="opacity-0 group-hover:opacity-100 transition-opacity text-[10px]">Lihat SPK →</span>
            </div>
        </div>

        {{-- 6. Belum Di-QC --}}
        <div wire:click="filterSpkByStatus('belum_qc')"
             class="group relative overflow-hidden rounded-2xl p-4 sm:p-5 bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-700 text-white shadow-md shadow-indigo-500/10 cursor-pointer hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <div class="flex items-center justify-between opacity-90 mb-2">
                <span class="text-[10px] sm:text-xs font-black uppercase tracking-wider">Belum Di-QC</span>
                <svg class="w-4 h-4 opacity-80 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="text-2xl sm:text-3xl font-black tracking-tight">
                {{ number_format($sum['belum_qc']) }}
            </div>
            <div class="text-[10px] sm:text-xs text-indigo-100 font-semibold mt-1 flex items-center justify-between">
                <span>Sisa Antrean QC Awal</span>
                <span class="opacity-0 group-hover:opacity-100 transition-opacity text-[10px]">Lihat SPK →</span>
            </div>
        </div>

    </div>

    {{-- Tab Navigation Bar (3 Tabs) --}}
    <div class="flex items-center gap-2 border-b border-gray-200 dark:border-gray-700 overflow-x-auto pb-1">
        <button type="button" 
                wire:click="$set('activeTab', 'cs')"
                class="px-4 py-2.5 text-xs font-extrabold uppercase tracking-wider rounded-xl transition-all flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'cs' ? 'bg-emerald-600 text-white shadow-sm' : 'text-gray-500 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800' }}">
            <span>👥 Rekapan Kontribusi CS QC</span>
        </button>

        <button type="button" 
                wire:click="$set('activeTab', 'daily')"
                class="px-4 py-2.5 text-xs font-extrabold uppercase tracking-wider rounded-xl transition-all flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'daily' ? 'bg-teal-600 text-white shadow-sm' : 'text-gray-500 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800' }}">
            <span>📅 Rekapan QC Before Harian</span>
        </button>

        <button type="button" 
                wire:click="$set('activeTab', 'spk')"
                class="px-4 py-2.5 text-xs font-extrabold uppercase tracking-wider rounded-xl transition-all flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'spk' ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-500 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800' }}">
            <span>📋 Rincian Data SPK</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-mono {{ $activeTab === 'spk' ? 'bg-white/20 text-white' : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' }}">
                {{ number_format($spkCounts['all']) }}
            </span>
        </button>
    </div>

    {{-- ========================================================================= --}}
    {{-- TAB 1: REKAPAN KONTRIBUSI BULANAN CS QC --}}
    {{-- ========================================================================= --}}
    @if($activeTab === 'cs')
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-emerald-50/50 dark:bg-emerald-950/20">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <h2 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-wider">
                        Rekapan Kontribusi Bulanan CS QC
                    </h2>
                </div>
                
                {{-- Quick Filter CS Name --}}
                <div class="relative w-full sm:w-64">
                    <input type="text" 
                           wire:model.live.debounce.300ms="searchCs" 
                           placeholder="Cari Agen CS / Kode..." 
                           class="w-full text-xs font-semibold px-3 py-1.5 pl-8 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-emerald-500 text-gray-900 dark:text-white" />
                    <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-gray-600 dark:text-gray-300">
                    <thead class="text-[11px] uppercase tracking-wider bg-emerald-700 text-white font-extrabold text-center">
                        <tr>
                            <th scope="col" class="px-4 py-3.5 text-left">Nama CS</th>
                            <th scope="col" class="px-3 py-3.5">Jumlah Masuk</th>
                            <th scope="col" class="px-3 py-3.5 bg-green-700/40">Lolos</th>
                            <th scope="col" class="px-3 py-3.5 bg-amber-600/40">Lolos Catatan</th>
                            <th scope="col" class="px-3 py-3.5 bg-rose-700/40">Tolak</th>
                            <th scope="col" class="px-3 py-3.5 bg-teal-800">Jumlah Ter-QC</th>
                            <th scope="col" class="px-3 py-3.5 bg-indigo-800">Belum Ter-QC</th>
                            <th scope="col" class="px-3 py-3.5">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-center font-medium">
                        @forelse($analytics['cs_table']['rows'] as $row)
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-4 py-3 text-left font-bold text-gray-900 dark:text-white">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 flex items-center justify-center font-black text-[10px]">
                                            {{ $row['code'] }}
                                        </span>
                                        <span>{{ $row['display_name'] }}</span>
                                    </div>
                                </td>
                                <td class="px-3 py-3 font-bold text-gray-900 dark:text-white">
                                    {{ number_format($row['masuk']) }}
                                </td>
                                <td class="px-3 py-3 text-green-600 dark:text-green-400 font-bold">
                                    {{ number_format($row['lolos']) }}
                                    <span class="text-[10px] text-gray-400 font-normal">({{ $row['pct_lolos'] }}%)</span>
                                </td>
                                <td class="px-3 py-3 text-amber-600 dark:text-amber-400 font-bold">
                                    {{ number_format($row['lolos_catatan']) }}
                                    <span class="text-[10px] text-gray-400 font-normal">({{ $row['pct_lolos_catatan'] }}%)</span>
                                </td>
                                <td class="px-3 py-3 text-rose-600 dark:text-rose-400 font-bold">
                                    {{ number_format($row['tolak']) }}
                                    <span class="text-[10px] text-gray-400 font-normal">({{ $row['pct_tolak'] }}%)</span>
                                </td>
                                <td class="px-3 py-3 text-teal-700 dark:text-teal-300 font-black bg-teal-50/50 dark:bg-teal-950/20">
                                    {{ number_format($row['ter_qc']) }}
                                    <span class="text-[10px] text-teal-600/70 font-normal">({{ $row['pct_ter_qc'] }}%)</span>
                                </td>
                                <td class="px-3 py-3 text-indigo-700 dark:text-indigo-300 font-black bg-indigo-50/50 dark:bg-indigo-950/20">
                                    {{ number_format($row['blm_qc']) }}
                                    <span class="text-[10px] text-indigo-600/70 font-normal">({{ $row['pct_blm_qc'] }}%)</span>
                                </td>
                                <td class="px-3 py-3">
                                    <button type="button" 
                                            wire:click="filterSpkByCs('{{ $row['code'] }}')"
                                            class="px-2 py-1 bg-gray-100 hover:bg-emerald-600 text-gray-700 hover:text-white rounded-lg text-[10px] font-bold transition-all"
                                            title="Buka Daftar SPK CS {{ $row['code'] }}">
                                        Rincian SPK →
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                                    Tidak ada data CS yang sesuai pencarian atau rentang tanggal.
                                </td>
                            </tr>
                        @endforelse

                        {{-- Baris Total Keseluruhan CS --}}
                        @if(!empty($analytics['cs_table']['total']))
                            @php $tot = $analytics['cs_table']['total']; @endphp
                            <tr class="bg-emerald-50 dark:bg-emerald-950/40 font-black text-gray-900 dark:text-white border-t-2 border-emerald-500">
                                <td class="px-4 py-3.5 text-left text-emerald-800 dark:text-emerald-300 uppercase tracking-wider">
                                    {{ $tot['display_name'] }}
                                </td>
                                <td class="px-3 py-3.5">{{ number_format($tot['masuk']) }}</td>
                                <td class="px-3 py-3.5 text-green-700 dark:text-green-300">
                                    {{ number_format($tot['lolos']) }}
                                    <div class="text-[10px] text-green-600 font-normal">({{ $tot['pct_lolos'] }}%)</div>
                                </td>
                                <td class="px-3 py-3.5 text-amber-700 dark:text-amber-300">
                                    {{ number_format($tot['lolos_catatan']) }}
                                    <div class="text-[10px] text-amber-600 font-normal">({{ $tot['pct_lolos_catatan'] }}%)</div>
                                </td>
                                <td class="px-3 py-3.5 text-rose-700 dark:text-rose-300">
                                    {{ number_format($tot['tolak']) }}
                                    <div class="text-[10px] text-rose-600 font-normal">({{ $tot['pct_tolak'] }}%)</div>
                                </td>
                                <td class="px-3 py-3.5 text-teal-800 dark:text-teal-200 bg-teal-100/40 dark:bg-teal-900/30">
                                    {{ number_format($tot['ter_qc']) }}
                                    <div class="text-[10px] text-teal-700 dark:text-teal-300 font-normal">({{ $tot['pct_ter_qc'] }}%)</div>
                                </td>
                                <td class="px-3 py-3.5 text-indigo-800 dark:text-indigo-200 bg-indigo-100/40 dark:bg-indigo-900/30">
                                    {{ number_format($tot['blm_qc']) }}
                                    <div class="text-[10px] text-indigo-700 dark:text-indigo-300 font-normal">({{ $tot['pct_blm_qc'] }}%)</div>
                                </td>
                                <td class="px-3 py-3.5 text-gray-400 font-normal">-</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- ========================================================================= --}}
    {{-- TAB 2: REKAPAN QC BEFORE HARIAN --}}
    {{-- ========================================================================= --}}
    @if($activeTab === 'daily')
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-teal-50/50 dark:bg-teal-950/20">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-teal-500"></span>
                    <h2 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-wider">
                        Rekapan QC Before Harian
                    </h2>
                </div>
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">
                    {{ count($analytics['daily_table']['rows']) }} Hari Kalender
                </span>
            </div>

            <div class="overflow-x-auto max-h-[500px]">
                <table class="w-full text-left text-xs text-gray-600 dark:text-gray-300">
                    <thead class="text-[11px] uppercase tracking-wider bg-teal-700 text-white font-extrabold text-center sticky top-0 z-10">
                        <tr>
                            <th scope="col" class="px-4 py-3.5 text-left">Tanggal</th>
                            <th scope="col" class="px-3 py-3.5">Jumlah Sepatu Masuk</th>
                            <th scope="col" class="px-3 py-3.5 bg-green-700/40">Lolos</th>
                            <th scope="col" class="px-3 py-3.5 bg-amber-600/40">Lolos Dengan Catatan</th>
                            <th scope="col" class="px-3 py-3.5 bg-rose-700/40">Tolak</th>
                            <th scope="col" class="px-3 py-3.5 bg-teal-800">Jumlah Ter-QC</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-center font-medium">
                        @forelse($analytics['daily_table']['rows'] as $d)
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-colors {{ $d['ter_qc'] > 0 || $d['masuk'] > 0 ? '' : 'opacity-60' }}">
                                <td class="px-4 py-2.5 text-left font-bold text-gray-900 dark:text-white">
                                    {{ $d['tanggal'] }}
                                </td>
                                <td class="px-3 py-2.5 font-bold {{ $d['masuk'] > 0 ? 'text-gray-900 dark:text-white' : 'text-gray-400' }}">
                                    {{ $d['masuk'] }}
                                </td>
                                <td class="px-3 py-2.5 font-bold {{ $d['lolos'] > 0 ? 'text-green-600 dark:text-green-400' : 'text-gray-400' }}">
                                    {{ $d['lolos'] }}
                                </td>
                                <td class="px-3 py-2.5 font-bold {{ $d['lolos_catatan'] > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-400' }}">
                                    {{ $d['lolos_catatan'] }}
                                </td>
                                <td class="px-3 py-2.5 font-bold {{ $d['tolak'] > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-gray-400' }}">
                                    {{ $d['tolak'] }}
                                </td>
                                <td class="px-3 py-2.5 font-black bg-teal-50/30 dark:bg-teal-950/10 {{ $d['ter_qc'] > 0 ? 'text-teal-700 dark:text-teal-300' : 'text-gray-400' }}">
                                    {{ $d['ter_qc'] }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                    Tidak ada data harian pada rentang tanggal ini.
                                </td>
                            </tr>
                        @endforelse

                        {{-- Baris Total Keseluruhan Harian --}}
                        @if(!empty($analytics['daily_table']['total']))
                            @php $totD = $analytics['daily_table']['total']; @endphp
                            <tr class="bg-teal-50 dark:bg-teal-950/40 font-black text-gray-900 dark:text-white border-t-2 border-teal-600 sticky bottom-0">
                                <td class="px-4 py-3.5 text-left text-teal-800 dark:text-teal-300 uppercase tracking-wider">
                                    {{ $totD['tanggal'] }}
                                </td>
                                <td class="px-3 py-3.5">{{ number_format($totD['masuk']) }}</td>
                                <td class="px-3 py-3.5 text-green-700 dark:text-green-300">{{ number_format($totD['lolos']) }}</td>
                                <td class="px-3 py-3.5 text-amber-700 dark:text-amber-300">{{ number_format($totD['lolos_catatan']) }}</td>
                                <td class="px-3 py-3.5 text-rose-700 dark:text-rose-300">{{ number_format($totD['tolak']) }}</td>
                                <td class="px-3 py-3.5 text-teal-800 dark:text-teal-200 bg-teal-100/40 dark:bg-teal-900/40">
                                    {{ number_format($totD['ter_qc']) }}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- ========================================================================= --}}
    {{-- TAB 3: RINCIAN DATA SPK (FITUR BARU) --}}
    {{-- ========================================================================= --}}
    @if($activeTab === 'spk')
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 overflow-hidden space-y-4 p-5">
            
            {{-- Toolbar: Status Filter Pills, Search, CS Filter --}}
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                
                {{-- Status Filter Pills --}}
                <div class="flex flex-wrap items-center gap-1.5">
                    <button type="button" 
                            wire:click="$set('spkStatusFilter', 'all')"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $spkStatusFilter === 'all' ? 'bg-gray-900 text-white shadow-sm' : 'bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                        Semua ({{ $spkCounts['all'] }})
                    </button>
                    <button type="button" 
                            wire:click="$set('spkStatusFilter', 'lolos')"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $spkStatusFilter === 'lolos' ? 'bg-green-600 text-white shadow-sm' : 'bg-green-50 text-green-700 hover:bg-green-100 dark:bg-green-950/40 dark:text-green-300' }}">
                        ✓ Lolos Murni ({{ $spkCounts['lolos'] }})
                    </button>
                    <button type="button" 
                            wire:click="$set('spkStatusFilter', 'lolos_catatan')"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $spkStatusFilter === 'lolos_catatan' ? 'bg-amber-500 text-white shadow-sm' : 'bg-amber-50 text-amber-700 hover:bg-amber-100 dark:bg-amber-950/40 dark:text-amber-300' }}">
                        ★ Lolos Catatan ({{ $spkCounts['lolos_catatan'] }})
                    </button>
                    <button type="button" 
                            wire:click="$set('spkStatusFilter', 'tolak')"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $spkStatusFilter === 'tolak' ? 'bg-rose-600 text-white shadow-sm' : 'bg-rose-50 text-rose-700 hover:bg-rose-100 dark:bg-rose-950/40 dark:text-rose-300' }}">
                        ✕ Tolak / Reject ({{ $spkCounts['tolak'] }})
                    </button>
                    <button type="button" 
                            wire:click="$set('spkStatusFilter', 'belum_qc')"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $spkStatusFilter === 'belum_qc' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-950/40 dark:text-indigo-300' }}">
                        ⏳ Belum Di-QC ({{ $spkCounts['belum_qc'] }})
                    </button>
                </div>

                {{-- Search & CS Filter --}}
                <div class="flex flex-wrap items-center gap-2">
                    {{-- CS Filter Dropdown --}}
                    <select wire:model.live="spkCsFilter" 
                            class="text-xs font-semibold px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 text-gray-900 dark:text-white">
                        <option value="">Semua CS</option>
                        @foreach($distinctCs as $cs)
                            <option value="{{ $cs }}">CS: {{ $cs }}</option>
                        @endforeach
                    </select>

                    {{-- Search Input --}}
                    <div class="relative w-full sm:w-64">
                        <input type="text" 
                               wire:model.live.debounce.300ms="spkSearch" 
                               placeholder="Cari SPK, Nama, HP, Brand..." 
                               class="w-full text-xs font-semibold px-3 py-2 pl-8 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 text-gray-900 dark:text-white" />
                        <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    @if(!empty($spkSearch) || !empty($spkCsFilter) || $spkStatusFilter !== 'all')
                        <button type="button" 
                                wire:click="$set('spkSearch', ''); $set('spkCsFilter', ''); $set('spkStatusFilter', 'all');" 
                                class="px-2.5 py-2 text-xs font-bold text-gray-500 hover:text-rose-600 rounded-xl border border-gray-200 hover:border-rose-200 transition-colors"
                                title="Reset Filter SPK">
                            Reset
                        </button>
                    @endif
                </div>

            </div>

            {{-- Table Container --}}
            <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-700">
                <table class="w-full text-left text-xs text-gray-600 dark:text-gray-300">
                    <thead class="text-[11px] uppercase tracking-wider bg-gray-900 text-white font-extrabold">
                        <tr>
                            <th scope="col" class="px-4 py-3.5">Informasi SPK</th>
                            <th scope="col" class="px-4 py-3.5">Pelanggan & Sepatu</th>
                            <th scope="col" class="px-3 py-3.5 text-center">CS Handler</th>
                            <th scope="col" class="px-3 py-3.5 text-center">Status QC Awal</th>
                            <th scope="col" class="px-4 py-3.5">Catatan / Rekomendasi Tambah Jasa</th>
                            <th scope="col" class="px-4 py-3.5">Waktu & Petugas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 font-medium">
                        @forelse($paginatedSpk as $spk)
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-colors">
                                
                                {{-- 1. Informasi SPK --}}
                                <td class="px-4 py-3.5 align-top">
                                    <div class="font-mono font-black text-gray-900 dark:text-white text-xs">
                                        <a href="/orders/{{ $spk['id'] }}" target="_blank" class="hover:text-emerald-600 hover:underline flex items-center gap-1">
                                            <span>{{ $spk['spk_number'] }}</span>
                                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>
                                    </div>
                                    <div class="mt-1">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black tracking-wide uppercase bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
                                            {{ $spk['status_spk'] }}
                                        </span>
                                    </div>
                                </td>

                                {{-- 2. Pelanggan & Sepatu --}}
                                <td class="px-4 py-3.5 align-top">
                                    <div class="font-bold text-gray-900 dark:text-white">
                                        {{ $spk['customer_name'] }}
                                    </div>
                                    <div class="text-[11px] text-gray-400 font-mono">
                                        {{ $spk['customer_phone'] }}
                                    </div>
                                    <div class="text-xs text-emerald-700 dark:text-emerald-400 font-semibold mt-0.5">
                                        {{ $spk['brand_type'] }}
                                    </div>
                                </td>

                                {{-- 3. CS Handler --}}
                                <td class="px-3 py-3.5 align-top text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                        {{ $spk['cs_code'] }}
                                    </span>
                                </td>

                                {{-- 4. Status QC Awal --}}
                                <td class="px-3 py-3.5 align-top text-center">
                                    @if($spk['classification'] === 'lolos')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-black bg-green-100 text-green-800 dark:bg-green-950/60 dark:text-green-300 border border-green-200 dark:border-green-800">
                                            <span>✓</span> Lolos Murni
                                        </span>
                                    @elseif($spk['classification'] === 'lolos_catatan')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-black bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                            <span>★</span> Lolos Catatan
                                        </span>
                                    @elseif($spk['classification'] === 'tolak')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-black bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                            <span>✕</span> Gudang Reject
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-black bg-indigo-100 text-indigo-800 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                            <span>⏳</span> Belum Di-QC
                                        </span>
                                    @endif
                                </td>

                                {{-- 5. Catatan / Rekomendasi Jasa --}}
                                <td class="px-4 py-3.5 align-top max-w-xs">
                                    @if(!empty($spk['suggested_services']) && count($spk['suggested_services']) > 0)
                                        <div class="space-y-1 mb-1.5">
                                            <span class="text-[10px] font-black uppercase tracking-wider text-amber-600 block">Rekomendasi Layanan:</span>
                                            @foreach($spk['suggested_services'] as $svc)
                                                @php
                                                    $sName = is_array($svc) ? ($svc['display_name'] ?? $svc['custom_name'] ?? $svc['name'] ?? 'Layanan') : $svc;
                                                    $sCost = is_array($svc) && isset($svc['cost']) ? ' (Rp ' . number_format($svc['cost'], 0, ',', '.') . ')' : '';
                                                @endphp
                                                <div class="inline-block bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-200 px-2 py-0.5 rounded text-[11px] font-semibold border border-amber-200/60 mr-1 mb-1">
                                                    + {{ $sName }}{{ $sCost }}
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if(!empty($spk['desc_kondisi_bawaan']))
                                        <div class="text-[11px] text-gray-700 dark:text-gray-300">
                                            <span class="font-bold text-gray-500">Kondisi:</span> {{ $spk['desc_kondisi_bawaan'] }}
                                        </div>
                                    @endif

                                    @if(!empty($spk['warehouse_qc_notes']))
                                        <div class="text-[11px] text-gray-500 italic mt-0.5">
                                            "{{ $spk['warehouse_qc_notes'] }}"
                                        </div>
                                    @endif

                                    @if(empty($spk['suggested_services']) && empty($spk['desc_kondisi_bawaan']) && empty($spk['warehouse_qc_notes']))
                                        <span class="text-gray-400 italic text-[11px]">Tidak ada catatan khusus.</span>
                                    @endif
                                </td>

                                {{-- 6. Waktu & Petugas --}}
                                <td class="px-4 py-3.5 align-top text-xs">
                                    <div class="text-gray-900 dark:text-white font-semibold">
                                        <span class="text-gray-400 font-normal">QC:</span> {{ $spk['qc_date'] }}
                                    </div>
                                    <div class="text-gray-500 text-[11px]">
                                        <span class="text-gray-400">Masuk:</span> {{ $spk['entry_date'] }}
                                    </div>
                                    <div class="text-[10px] text-emerald-600 font-bold mt-1">
                                        👤 {{ $spk['inspector'] }}
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-10 h-10 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="font-bold text-sm">Tidak ada SPK yang ditemukan</span>
                                        <span class="text-xs text-gray-500 mt-0.5">Coba ubah kata kunci pencarian, filter status, atau rentang tanggal.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Controls --}}
            @if($totalSpkFiltered > 0)
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
                    <div class="text-xs text-gray-500">
                        Menampilkan <span class="font-bold text-gray-900 dark:text-white">{{ min($totalSpkFiltered, ($spkPage - 1) * $spkPerPage + 1) }}</span> 
                        sampai <span class="font-bold text-gray-900 dark:text-white">{{ min($totalSpkFiltered, $spkPage * $spkPerPage) }}</span> 
                        dari <span class="font-bold text-gray-900 dark:text-white">{{ number_format($totalSpkFiltered) }}</span> SPK
                    </div>

                    <div class="flex items-center gap-1.5 self-end sm:self-auto">
                        <button type="button" 
                                wire:click="previousSpkPage"
                                {{ $spkPage <= 1 ? 'disabled' : '' }}
                                class="px-3 py-1.5 rounded-xl border border-gray-200 dark:border-gray-700 text-xs font-bold transition-all {{ $spkPage <= 1 ? 'opacity-40 cursor-not-allowed bg-gray-50' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200' }}">
                            ← Sebelumnya
                        </button>

                        <span class="px-3 py-1.5 text-xs font-black text-gray-700 dark:text-gray-300">
                            Hal {{ $spkPage }} dari {{ $totalPages }}
                        </span>

                        <button type="button" 
                                wire:click="nextSpkPage({{ $totalPages }})"
                                {{ $spkPage >= $totalPages ? 'disabled' : '' }}
                                class="px-3 py-1.5 rounded-xl border border-gray-200 dark:border-gray-700 text-xs font-bold transition-all {{ $spkPage >= $totalPages ? 'opacity-40 cursor-not-allowed bg-gray-50' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200' }}">
                            Berikutnya →
                        </button>
                    </div>
                </div>
            @endif

        </div>
    @endif

</div>
