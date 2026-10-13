<div class="p-4 sm:p-6 lg:p-8 space-y-6">

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
                    title="Reset Filter ke Bulan Berjalan">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Filter Bar: Quick Presets & Date Range Inputs --}}
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

        {{-- Date Range Inputs --}}
        <div class="flex items-center gap-2">
            <div class="relative">
                <input type="date" 
                       wire:model.live="startDate" 
                       class="text-xs font-semibold px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-gray-900 dark:text-white" />
            </div>
            <span class="text-gray-400 font-bold text-xs">s/d</span>
            <div class="relative">
                <input type="date" 
                       wire:model.live="endDate" 
                       class="text-xs font-semibold px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-gray-900 dark:text-white" />
            </div>
        </div>
    </div>

    {{-- Banner Metrik Utama (Sesuai Desain Gambar 1) --}}
    @php
        $sum = $analytics['summary'];
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
        
        {{-- 1. Jumlah Sepatu Masuk --}}
        <div class="relative overflow-hidden rounded-2xl p-4 sm:p-5 bg-gradient-to-br from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-500/10">
            <div class="flex items-center justify-between opacity-90 mb-2">
                <span class="text-[10px] sm:text-xs font-black uppercase tracking-wider">Sepatu Masuk</span>
                <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <div class="text-2xl sm:text-3xl font-black tracking-tight">
                {{ number_format($sum['total_masuk']) }}
            </div>
            <div class="text-[10px] sm:text-xs text-emerald-100 font-semibold mt-1">
                Unit Periode Aktif
            </div>
        </div>

        {{-- 2. Jumlah Lolos Murni --}}
        <div class="relative overflow-hidden rounded-2xl p-4 sm:p-5 bg-gradient-to-br from-green-500 to-green-600 text-white shadow-md shadow-green-500/10">
            <div class="flex items-center justify-between opacity-90 mb-2">
                <span class="text-[10px] sm:text-xs font-black uppercase tracking-wider">Jumlah Lolos</span>
                <span class="px-1.5 py-0.5 rounded text-[10px] font-black bg-white/20">
                    {{ $sum['pct_lolos'] }}%
                </span>
            </div>
            <div class="text-2xl sm:text-3xl font-black tracking-tight">
                {{ number_format($sum['total_lolos']) }}
            </div>
            <div class="text-[10px] sm:text-xs text-green-100 font-semibold mt-1">
                Lolos Tanpa Catatan
            </div>
        </div>

        {{-- 3. Jumlah Lolos Dengan Catatan --}}
        <div class="relative overflow-hidden rounded-2xl p-4 sm:p-5 bg-gradient-to-br from-amber-500 to-amber-600 text-white shadow-md shadow-amber-500/10">
            <div class="flex items-center justify-between opacity-90 mb-2">
                <span class="text-[10px] sm:text-xs font-black uppercase tracking-wider">Lolos Catatan</span>
                <span class="px-1.5 py-0.5 rounded text-[10px] font-black bg-white/20">
                    {{ $sum['pct_lolos_catatan'] }}%
                </span>
            </div>
            <div class="text-2xl sm:text-3xl font-black tracking-tight">
                {{ number_format($sum['total_lolos_catatan']) }}
            </div>
            <div class="text-[10px] sm:text-xs text-amber-100 font-semibold mt-1">
                Lolos Dengan Catatan
            </div>
        </div>

        {{-- 4. Jumlah Tolak (Reject) --}}
        <div class="relative overflow-hidden rounded-2xl p-4 sm:p-5 bg-gradient-to-br from-rose-600 to-rose-700 text-white shadow-md shadow-rose-500/10">
            <div class="flex items-center justify-between opacity-90 mb-2">
                <span class="text-[10px] sm:text-xs font-black uppercase tracking-wider">Jumlah Tolak</span>
                <span class="px-1.5 py-0.5 rounded text-[10px] font-black bg-white/20">
                    {{ $sum['pct_tolak'] }}%
                </span>
            </div>
            <div class="text-2xl sm:text-3xl font-black tracking-tight">
                {{ number_format($sum['total_tolak']) }}
            </div>
            <div class="text-[10px] sm:text-xs text-rose-100 font-semibold mt-1">
                Reject / Tolak QC Awal
            </div>
        </div>

        {{-- 5. Total QC --}}
        <div class="relative overflow-hidden rounded-2xl p-4 sm:p-5 bg-gradient-to-br from-teal-600 to-teal-700 text-white shadow-md shadow-teal-500/10">
            <div class="flex items-center justify-between opacity-90 mb-2">
                <span class="text-[10px] sm:text-xs font-black uppercase tracking-wider">Total QC</span>
                <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
            </div>
            <div class="text-2xl sm:text-3xl font-black tracking-tight">
                {{ number_format($sum['total_qc']) }}
            </div>
            <div class="text-[10px] sm:text-xs text-teal-100 font-semibold mt-1">
                Total Selesai Di-QC
            </div>
        </div>

        {{-- 6. Yang Belum Di-QC --}}
        <div class="relative overflow-hidden rounded-2xl p-4 sm:p-5 bg-gradient-to-br from-indigo-600 to-indigo-700 text-white shadow-md shadow-indigo-500/10">
            <div class="flex items-center justify-between opacity-90 mb-2">
                <span class="text-[10px] sm:text-xs font-black uppercase tracking-wider">Belum Di-QC</span>
                <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="text-2xl sm:text-3xl font-black tracking-tight">
                {{ number_format($sum['belum_qc']) }}
            </div>
            <div class="text-[10px] sm:text-xs text-indigo-100 font-semibold mt-1">
                Sisa Antrean QC Awal
            </div>
        </div>

    </div>

    {{-- Navigation Tabs & Table Search --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-200 dark:border-gray-700 pb-2">
        <div class="flex items-center gap-2">
            <button type="button"
                    wire:click="$set('activeTab', 'both')"
                    class="px-4 py-2 text-xs font-bold rounded-xl transition-all {{ $activeTab === 'both' ? 'bg-emerald-600 text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                Semua Tabel
            </button>
            <button type="button"
                    wire:click="$set('activeTab', 'cs')"
                    class="px-4 py-2 text-xs font-bold rounded-xl transition-all {{ $activeTab === 'cs' ? 'bg-emerald-600 text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                Kontribusi CS QC
            </button>
            <button type="button"
                    wire:click="$set('activeTab', 'daily')"
                    class="px-4 py-2 text-xs font-bold rounded-xl transition-all {{ $activeTab === 'daily' ? 'bg-emerald-600 text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                Rekapan Harian QC
            </button>
        </div>

        @if($activeTab === 'cs' || $activeTab === 'both')
            <div class="w-full sm:w-64">
                <input type="text"
                       wire:model.live.debounce.300ms="searchCs"
                       placeholder="Cari CS atau Kode..."
                       class="w-full text-xs px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-gray-900 dark:text-white" />
            </div>
        @endif
    </div>

    {{-- ========================================================================= --}}
    {{-- TABEL 1: REKAPAN KONTRIBUSI CS QC (Sesuai Desain Gambar 2) --}}
    {{-- ========================================================================= --}}
    @if($activeTab === 'both' || $activeTab === 'cs')
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-emerald-50/50 dark:bg-emerald-950/20">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <h2 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-wider">
                        Rekapan Kontribusi Bulanan CS QC
                    </h2>
                </div>
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">
                    Total: {{ count($analytics['cs_table']['rows']) }} Agen CS
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-gray-600 dark:text-gray-300">
                    <thead class="text-[11px] uppercase tracking-wider bg-emerald-600 text-white font-extrabold text-center">
                        <tr>
                            <th scope="col" class="px-4 py-3.5 text-left">Nama CS (Kode)</th>
                            <th scope="col" class="px-3 py-3.5">Jumlah Masuk</th>
                            <th scope="col" class="px-3 py-3.5 bg-green-700/40">Lolos</th>
                            <th scope="col" class="px-3 py-3.5 bg-amber-600/40">Lolos Catatan</th>
                            <th scope="col" class="px-3 py-3.5 bg-rose-700/40">Tolak</th>
                            <th scope="col" class="px-3 py-3.5 bg-teal-700/40">Jumlah Ter-QC</th>
                            <th scope="col" class="px-3 py-3.5 bg-indigo-700/40">Jml Blm Ter-QC</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-center font-medium">
                        @forelse($analytics['cs_table']['rows'] as $cs)
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-4 py-3 text-left font-black text-gray-900 dark:text-white flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 flex items-center justify-center font-bold text-[10px]">
                                        {{ $cs['code'] }}
                                    </span>
                                    <span>{{ $cs['name'] }}</span>
                                </td>
                                <td class="px-3 py-3 font-bold text-gray-900 dark:text-white">
                                    {{ number_format($cs['masuk']) }}
                                </td>
                                <td class="px-3 py-3">
                                    <div class="font-bold text-green-600 dark:text-green-400">{{ $cs['lolos'] }}</div>
                                    <div class="text-[10px] text-gray-400 font-semibold">{{ $cs['pct_lolos'] }}%</div>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="font-bold text-amber-600 dark:text-amber-400">{{ $cs['lolos_catatan'] }}</div>
                                    <div class="text-[10px] text-gray-400 font-semibold">{{ $cs['pct_lolos_catatan'] }}%</div>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="font-bold {{ $cs['tolak'] > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-gray-400' }}">{{ $cs['tolak'] }}</div>
                                    <div class="text-[10px] text-gray-400 font-semibold">{{ $cs['pct_tolak'] }}%</div>
                                </td>
                                <td class="px-3 py-3 bg-teal-50/30 dark:bg-teal-950/10">
                                    <div class="font-black text-teal-700 dark:text-teal-300">{{ $cs['ter_qc'] }}</div>
                                    <div class="text-[10px] text-teal-600/80 dark:text-teal-400 font-semibold">{{ $cs['pct_ter_qc'] }}%</div>
                                </td>
                                <td class="px-3 py-3 bg-indigo-50/30 dark:bg-indigo-950/10">
                                    <div class="font-black {{ $cs['blm_qc'] > 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400' }}">{{ $cs['blm_qc'] }}</div>
                                    <div class="text-[10px] text-indigo-600/80 dark:text-indigo-400 font-semibold">{{ $cs['pct_blm_qc'] }}%</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                                    Tidak ada data kontribusi CS pada rentang tanggal ini.
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
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- ========================================================================= --}}
    {{-- TABEL 2: REKAPAN QC BEFORE HARIAN (Sesuai Desain Gambar 3) --}}
    {{-- ========================================================================= --}}
    @if($activeTab === 'both' || $activeTab === 'daily')
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

</div>
