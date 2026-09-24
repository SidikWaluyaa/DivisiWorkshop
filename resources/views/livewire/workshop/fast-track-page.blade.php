<div class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-100 to-teal-50">
    {{-- Flatpickr Scripts & Styles --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <style>
        .flatpickr-calendar {
            background: #ffffff !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 1.5rem !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
            font-family: inherit !important;
        }
        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange {
            background: #0f766e !important; /* teal-700 */
            border-color: #0f766e !important;
            color: #ffffff !important;
        }
        .flatpickr-day.inRange {
            background: #f0fdfa !important; /* teal-50 */
            box-shadow: -5px 0 0 #f0fdfa, 5px 0 0 #f0fdfa !important;
        }
        .flatpickr-months .flatpickr-month {
            color: #0f766e !important;
        }
    </style>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-gradient-to-r from-teal-700 to-teal-900 p-6 rounded-3xl shadow-xl text-white">
            <div>
                <h1 class="text-2xl font-black tracking-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-yellow-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Monitoring & Analisis Fast Track SPK
                </h1>
                <p class="text-xs text-teal-100 mt-1">Kelola dan telusuri kinerja layanan Fast Track secara real-time</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                {{-- Flatpickr Date Range Picker --}}
                <div class="relative" x-data="{
                    initFlatpickr() {
                        flatpickr($refs.rangeInput, {
                            mode: 'range',
                            dateFormat: 'Y-m-d',
                            defaultDate: ['{{ $startDate }}', '{{ $endDate }}'],
                            positionElement: $refs.rangeInput.previousElementSibling,
                            onChange: (selectedDates, dateStr, instance) => {
                                if (selectedDates.length === 2) {
                                    let start = instance.formatDate(selectedDates[0], 'Y-m-d');
                                    let end = instance.formatDate(selectedDates[1], 'Y-m-d');
                                    $wire.set('startDate', start);
                                    $wire.set('endDate', end);
                                }
                            }
                        });
                    }
                }" x-init="initFlatpickr()">
                    <button @click="$refs.rangeInput._flatpickr.open()" type="button" class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md px-4 py-2.5 rounded-xl border border-white/20 shadow-md text-white text-xs font-black hover:bg-white/20 active:scale-95 transition-all">
                        <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>📅 {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</span>
                    </button>
                    <input x-ref="rangeInput" type="text" class="hidden">
                </div>

                {{-- Back Button --}}
                <a href="{{ route('workshop.dashboard-v2') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-white text-teal-800 hover:bg-teal-50 rounded-xl text-xs font-black transition-all shadow-md active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Dashboard Utama
                </a>
            </div>
        </div>

        {{-- KPI Cards Section (6 Metrik Stat Card Pro Max) --}}
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3 sm:gap-4">
            {{-- Card 1: Total Fast Track --}}
            <button wire:click="setMetric('total_fast_track')" 
                    type="button"
                    class="text-left p-4 sm:p-5 rounded-2xl border transition-all duration-200 relative overflow-hidden active:scale-95 group flex flex-col justify-between min-h-[118px] {{ $selectedMetric === 'total_fast_track' ? 'bg-gradient-to-br from-teal-600 via-teal-700 to-emerald-700 text-white border-teal-500 shadow-lg shadow-teal-700/25 ring-2 ring-teal-300 dark:ring-teal-500' : 'bg-white dark:bg-gray-900 text-gray-900 dark:text-white border-gray-200/90 dark:border-gray-800 hover:border-teal-300 dark:hover:border-teal-800 hover:shadow-md hover:-translate-y-0.5 shadow-sm' }}">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[11px] font-black uppercase tracking-wider {{ $selectedMetric === 'total_fast_track' ? 'text-teal-100' : 'text-gray-500 dark:text-gray-400' }} truncate">Total Fast Track</span>
                    <div class="w-7 h-7 rounded-xl flex items-center justify-center shrink-0 {{ $selectedMetric === 'total_fast_track' ? 'bg-white/20 text-white' : 'bg-teal-50 text-teal-600 dark:bg-teal-950/40 dark:text-teal-400 group-hover:scale-110 transition-transform' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="my-1.5">
                    <div class="text-2xl sm:text-3xl font-black tracking-tight leading-none">{{ $totalFastTrack }} <span class="text-xs font-bold opacity-75">SPK</span></div>
                </div>
                <p class="text-[10px] font-medium leading-snug {{ $selectedMetric === 'total_fast_track' ? 'text-teal-100/90' : 'text-gray-400 dark:text-gray-500' }} truncate">Seluruh SPK prioritas</p>
            </button>

            {{-- Card 2: Fast Track Berhasil --}}
            <button wire:click="setMetric('successful_fast_track')" 
                    type="button"
                    class="text-left p-4 sm:p-5 rounded-2xl border transition-all duration-200 relative overflow-hidden active:scale-95 group flex flex-col justify-between min-h-[118px] {{ $selectedMetric === 'successful_fast_track' ? 'bg-gradient-to-br from-emerald-600 via-emerald-700 to-green-700 text-white border-emerald-500 shadow-lg shadow-emerald-700/25 ring-2 ring-emerald-300 dark:ring-emerald-500' : 'bg-white dark:bg-gray-900 text-gray-900 dark:text-white border-gray-200/90 dark:border-gray-800 hover:border-emerald-300 dark:hover:border-emerald-800 hover:shadow-md hover:-translate-y-0.5 shadow-sm' }}">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[11px] font-black uppercase tracking-wider {{ $selectedMetric === 'successful_fast_track' ? 'text-emerald-100' : 'text-emerald-700 dark:text-emerald-400' }} truncate">Fast Track Berhasil</span>
                    <div class="w-7 h-7 rounded-xl flex items-center justify-center shrink-0 {{ $selectedMetric === 'successful_fast_track' ? 'bg-white/20 text-white' : 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400 group-hover:scale-110 transition-transform' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="my-1.5">
                    <div class="text-2xl sm:text-3xl font-black tracking-tight leading-none {{ $selectedMetric === 'successful_fast_track' ? 'text-white' : 'text-emerald-700 dark:text-emerald-400' }}">{{ $successfulFastTrack }} <span class="text-xs font-bold opacity-75">SPK</span></div>
                </div>
                <p class="text-[10px] font-medium leading-snug {{ $selectedMetric === 'successful_fast_track' ? 'text-emerald-100/90' : 'text-gray-400 dark:text-gray-500' }} truncate">Selesai on-time SLA</p>
            </button>

            {{-- Card 3: Gagal SLA --}}
            <button wire:click="setMetric('failed_fast_track')" 
                    type="button"
                    class="text-left p-4 sm:p-5 rounded-2xl border transition-all duration-200 relative overflow-hidden active:scale-95 group flex flex-col justify-between min-h-[118px] {{ $selectedMetric === 'failed_fast_track' ? 'bg-gradient-to-br from-rose-600 via-rose-700 to-red-700 text-white border-rose-500 shadow-lg shadow-rose-700/25 ring-2 ring-rose-300 dark:ring-rose-500' : 'bg-white dark:bg-gray-900 text-gray-900 dark:text-white border-gray-200/90 dark:border-gray-800 hover:border-rose-300 dark:hover:border-rose-800 hover:shadow-md hover:-translate-y-0.5 shadow-sm' }}">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[11px] font-black uppercase tracking-wider {{ $selectedMetric === 'failed_fast_track' ? 'text-rose-100' : 'text-rose-600 dark:text-rose-400' }} truncate">Gagal SLA</span>
                    <div class="w-7 h-7 rounded-xl flex items-center justify-center shrink-0 {{ $selectedMetric === 'failed_fast_track' ? 'bg-white/20 text-white' : 'bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400 group-hover:scale-110 transition-transform' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                </div>
                <div class="my-1.5">
                    <div class="text-2xl sm:text-3xl font-black tracking-tight leading-none {{ $selectedMetric === 'failed_fast_track' ? 'text-white' : 'text-rose-600 dark:text-rose-400' }}">{{ $failedFastTrack }} <span class="text-xs font-bold opacity-75">SPK</span></div>
                </div>
                <p class="text-[10px] font-medium leading-snug {{ $selectedMetric === 'failed_fast_track' ? 'text-rose-100/90' : 'text-gray-400 dark:text-gray-500' }} truncate">Batas stasiun terlewati</p>
            </button>

            {{-- Card 4: Gagal Operasional --}}
            <button wire:click="setMetric('operational_failed_fast_track')" 
                    type="button"
                    class="text-left p-4 sm:p-5 rounded-2xl border transition-all duration-200 relative overflow-hidden active:scale-95 group flex flex-col justify-between min-h-[118px] {{ $selectedMetric === 'operational_failed_fast_track' ? 'bg-gradient-to-br from-orange-500 via-orange-600 to-amber-600 text-white border-orange-400 shadow-lg shadow-orange-700/25 ring-2 ring-orange-300 dark:ring-orange-500' : 'bg-white dark:bg-gray-900 text-gray-900 dark:text-white border-gray-200/90 dark:border-gray-800 hover:border-orange-300 dark:hover:border-orange-800 hover:shadow-md hover:-translate-y-0.5 shadow-sm' }}">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[11px] font-black uppercase tracking-wider {{ $selectedMetric === 'operational_failed_fast_track' ? 'text-orange-100' : 'text-orange-600 dark:text-orange-400' }} truncate">Gagal Operasional</span>
                    <div class="w-7 h-7 rounded-xl flex items-center justify-center shrink-0 {{ $selectedMetric === 'operational_failed_fast_track' ? 'bg-white/20 text-white' : 'bg-orange-50 text-orange-600 dark:bg-orange-950/40 dark:text-orange-400 group-hover:scale-110 transition-transform' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                    </div>
                </div>
                <div class="my-1.5">
                    <div class="text-2xl sm:text-3xl font-black tracking-tight leading-none {{ $selectedMetric === 'operational_failed_fast_track' ? 'text-white' : 'text-orange-600 dark:text-orange-400' }}">{{ $operationalFailed }} <span class="text-xs font-bold opacity-75">SPK</span></div>
                </div>
                <p class="text-[10px] font-medium leading-snug {{ $selectedMetric === 'operational_failed_fast_track' ? 'text-orange-100/90' : 'text-gray-400 dark:text-gray-500' }} truncate">Kendala teknis & CX</p>
            </button>

            {{-- Card 5: Pending CS --}}
            <button wire:click="setMetric('pending_fast_track')" 
                    type="button"
                    class="text-left p-4 sm:p-5 rounded-2xl border transition-all duration-200 relative overflow-hidden active:scale-95 group flex flex-col justify-between min-h-[118px] {{ $selectedMetric === 'pending_fast_track' ? 'bg-gradient-to-br from-purple-600 via-purple-700 to-indigo-700 text-white border-purple-500 shadow-lg shadow-purple-700/25 ring-2 ring-purple-300 dark:ring-purple-500' : 'bg-white dark:bg-gray-900 text-gray-900 dark:text-white border-gray-200/90 dark:border-gray-800 hover:border-purple-300 dark:hover:border-purple-800 hover:shadow-md hover:-translate-y-0.5 shadow-sm' }}">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[11px] font-black uppercase tracking-wider {{ $selectedMetric === 'pending_fast_track' ? 'text-purple-100' : 'text-purple-600 dark:text-purple-400' }} truncate">Pending CS</span>
                    <div class="w-7 h-7 rounded-xl flex items-center justify-center shrink-0 {{ $selectedMetric === 'pending_fast_track' ? 'bg-white/20 text-white' : 'bg-purple-50 text-purple-600 dark:bg-purple-950/40 dark:text-purple-400 group-hover:scale-110 transition-transform' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="my-1.5">
                    <div class="text-2xl sm:text-3xl font-black tracking-tight leading-none {{ $selectedMetric === 'pending_fast_track' ? 'text-white' : 'text-purple-600 dark:text-purple-400' }}">{{ $pendingFastTrack }} <span class="text-xs font-bold opacity-75">SPK</span></div>
                </div>
                <p class="text-[10px] font-medium leading-snug {{ $selectedMetric === 'pending_fast_track' ? 'text-purple-100/90' : 'text-gray-400 dark:text-gray-500' }} truncate">Menunggu konfirmasi</p>
            </button>

            {{-- Card 6: Batal / Downgrade --}}
            <button wire:click="setMetric('downgraded_fast_track')" 
                    type="button"
                    class="text-left p-4 sm:p-5 rounded-2xl border transition-all duration-200 relative overflow-hidden active:scale-95 group flex flex-col justify-between min-h-[118px] {{ $selectedMetric === 'downgraded_fast_track' ? 'bg-gradient-to-br from-slate-700 via-slate-800 to-gray-900 text-white border-slate-600 shadow-lg shadow-slate-900/25 ring-2 ring-slate-400' : 'bg-white dark:bg-gray-900 text-gray-900 dark:text-white border-gray-200/90 dark:border-gray-800 hover:border-slate-300 dark:hover:border-slate-700 hover:shadow-md hover:-translate-y-0.5 shadow-sm' }}">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[11px] font-black uppercase tracking-wider {{ $selectedMetric === 'downgraded_fast_track' ? 'text-slate-200' : 'text-slate-600 dark:text-slate-300' }} truncate">Batal Fast Track</span>
                    <div class="w-7 h-7 rounded-xl flex items-center justify-center shrink-0 {{ $selectedMetric === 'downgraded_fast_track' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 group-hover:scale-110 transition-transform' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 13l-7 7-7-7m14-6l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
                <div class="my-1.5">
                    <div class="text-2xl sm:text-3xl font-black tracking-tight leading-none {{ $selectedMetric === 'downgraded_fast_track' ? 'text-white' : 'text-slate-700 dark:text-slate-200' }}">{{ $downgradedFastTrack }} <span class="text-xs font-bold opacity-75">SPK</span></div>
                </div>
                <p class="text-[10px] font-medium leading-snug {{ $selectedMetric === 'downgraded_fast_track' ? 'text-slate-300/90' : 'text-gray-400 dark:text-gray-500' }} truncate">Diturunkan tambah jasa</p>
            </button>
        </div>

        {{-- Filter Bar & Table Section --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl shadow-sm overflow-hidden transition-all">
            {{-- Filter Bar --}}
            <div class="p-6 border-b border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/50 flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
                {{-- Search Bar --}}
                <div class="relative flex-1 max-w-md">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nomor SPK, nama pelanggan, sepatu..." class="w-full pl-10 pr-4 py-2 border border-gray-200 dark:border-gray-850 bg-white dark:bg-gray-950 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-300 placeholder-gray-400 focus:ring-teal-500 focus:border-teal-500 shadow-sm">
                </div>

                {{-- Dropdown Filters & Actions --}}
                <div class="flex flex-wrap items-center gap-3">
                    {{-- Status Filter --}}
                    <div class="flex items-center gap-2">
                        <label class="text-xs font-bold text-gray-500 dark:text-gray-400 whitespace-nowrap">Filter Status:</label>
                        <select wire:model.live="selectedStatus" class="bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-850 rounded-xl px-3 py-2 text-xs font-bold text-gray-700 dark:text-gray-300 focus:ring-teal-500 focus:border-teal-500 shadow-sm">
                            <option value="">Semua Status</option>
                            @foreach($availableStatuses as $statusVal)
                                <option value="{{ $statusVal }}">{{ $statusVal }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Reset Filter Shortcut --}}
                    @if($selectedStatus !== '' || !empty($search))
                        <button type="button" 
                                wire:click="resetFilters" 
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 dark:bg-rose-955/30 dark:text-rose-400 dark:hover:bg-rose-955/50 border border-rose-200/80 dark:border-rose-900/30 transition-all active:scale-95 shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span>Reset Filter</span>
                        </button>
                    @endif

                    {{-- PDF Export Link --}}
                    <a href="{{ route('workshop.fast-track.export-pdf', ['metric' => $selectedMetric, 'start_date' => $startDate, 'end_date' => $endDate, 'status' => $selectedStatus, 'date_filter_type' => ($selectedMetric === 'pending_fast_track' ? 'created_at' : 'entry_date'), 'search' => $search]) }}" 
                       target="_blank" 
                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-black shadow-sm active:scale-95 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Unduh PDF
                    </a>
                </div>
            </div>

            {{-- Filtered Results Summary Banner --}}
            <div class="px-6 py-3.5 bg-teal-50/50 dark:bg-teal-950/20 border-b border-gray-200 dark:border-gray-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs font-bold text-teal-850 dark:text-teal-300">
                <div class="flex items-center gap-2 flex-wrap">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Menampilkan <span class="text-teal-700 dark:text-teal-400 font-extrabold">{{ $orders->total() }}</span> SPK Fast Track ter-filter.
                    </div>
                    @if($selectedStatus !== '')
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-teal-100 text-teal-800 dark:bg-teal-900/50 dark:text-teal-300">
                            Status: {{ $selectedStatus }}
                        </span>
                    @endif
                    @if(!empty($search))
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300">
                            Cari: "{{ $search }}"
                        </span>
                    @endif
                </div>
                <div class="flex items-center gap-1">
                    Total Transaksi: <span class="text-teal-700 dark:text-teal-400 font-extrabold text-sm ml-1">Rp {{ number_format($totalFilteredRevenue, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                @if($orders->isEmpty())
                    <div class="text-center py-16 text-gray-400 dark:text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-3 opacity-40 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5"/>
                        </svg>
                        <p class="font-black text-sm">Tidak ada data SPK yang sesuai dengan filter pencarian.</p>
                    </div>
                @else
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                        <thead class="bg-gray-50/50 dark:bg-gray-850/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider w-16">No.</th>
                                <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">No. SPK</th>
                                <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">Pelanggan</th>
                                <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">Sepatu</th>
                                <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">Tgl Diterima</th>
                                <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">Status Stasiun</th>
                                <th class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-wider">Nilai Transaksi</th>
                                
                                @if($selectedMetric === 'successful_fast_track')
                                    <th class="px-6 py-4 text-left text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Durasi Stasiun & SLA</th>
                                @elseif($selectedMetric === 'failed_fast_track' || $selectedMetric === 'operational_failed_fast_track' || $selectedMetric === 'pending_fast_track' || $selectedMetric === 'downgraded_fast_track')
                                    <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">Keterangan</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800 text-xs text-gray-750 dark:text-gray-300 font-semibold">
                            @foreach($orders as $index => $order)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-850/50 transition-colors">
                                    <td class="px-6 py-4 text-gray-400">{{ $orders->firstItem() + $index }}</td>
                                    <td class="px-6 py-4 font-mono font-bold text-teal-600 dark:text-teal-400">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" target="_blank" class="hover:underline hover:text-teal-800 dark:hover:text-teal-300">
                                            {{ $order->spk_number }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-gray-900 dark:text-white font-bold">
                                        {{ $order->customer?->name ?? $order->customer_name }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-550 dark:text-gray-400">
                                        {{ $order->shoe_brand }} - {{ $order->shoe_type }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-550 dark:text-gray-400">
                                        {{ $order->entry_date ? $order->entry_date->format('d M Y H:i') : '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statusVal = $order->status->value;
                                            $statusBadgeClass = match($statusVal) {
                                                'PREPARATION' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-955/20 dark:text-blue-400 dark:border-blue-900/30',
                                                'SORTIR' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-955/20 dark:text-amber-400 dark:border-amber-900/30',
                                                'PRODUCTION' => 'bg-orange-50 text-orange-700 border-orange-200 dark:bg-orange-955/20 dark:text-orange-400 dark:border-orange-900/30',
                                                'QC' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-955/20 dark:text-emerald-400 dark:border-emerald-900/30',
                                                'SELESAI', 'FINISH', 'COMPLETED', 'HISTORY' => 'bg-teal-50 text-teal-700 border-teal-200 dark:bg-teal-955/20 dark:text-teal-400 dark:border-teal-900/30',
                                                'ASSESSMENT' => 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-955/20 dark:text-indigo-400 dark:border-indigo-900/30',
                                                'BATAL' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-955/20 dark:text-rose-400 dark:border-rose-900/30',
                                                default => 'bg-gray-50 text-gray-700 border-gray-200 dark:bg-gray-800/30 dark:text-gray-400 dark:border-gray-700/50',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider border {{ $statusBadgeClass }}">
                                            {{ $statusVal }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-gray-900 dark:text-white">
                                        Rp {{ number_format($order->total_transaksi, 0, ',', '.') }}
                                    </td>
                                    
                                    @if($selectedMetric === 'successful_fast_track')
                                        <td class="px-6 py-4">
                                            @php
                                                $stationDurations = $order->getStationDurations();
                                            @endphp
                                            <div class="flex flex-col gap-1 text-[11px]">
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <span class="px-1.5 py-0.5 rounded bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-mono font-bold text-[10px]">Prep: {{ $stationDurations['PREPARATION']['display'] }}</span>
                                                    <span class="px-1.5 py-0.5 rounded bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 font-mono font-bold text-[10px]">Sortir: {{ $stationDurations['SORTIR']['display'] }}</span>
                                                    <span class="px-1.5 py-0.5 rounded bg-orange-50 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 font-mono font-bold text-[10px]">Prod: {{ $stationDurations['PRODUCTION']['display'] }}</span>
                                                    <span class="px-1.5 py-0.5 rounded bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 font-mono font-bold text-[10px]">QC: {{ $stationDurations['QC']['display'] }}</span>
                                                </div>
                                                <div class="flex items-center gap-2 mt-0.5 text-[10px] text-gray-500">
                                                    <span>Total Waktu: <b class="text-gray-800 dark:text-gray-200">{{ $stationDurations['total_lead_time']['display'] }}</b></span>
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-black bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300">✓ SLA ON-TIME</span>
                                                </div>
                                            </div>
                                        </td>
                                    @elseif($selectedMetric === 'failed_fast_track')
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col gap-0.5 text-[10px]">
                                                @php
                                                    $logs = $order->logs->where('action', 'STATUS_CHANGE')->sortBy('created_at');
                                                    $transitions = [];
                                                    foreach ($logs as $log) {
                                                        $transitions[$log->step] = $log->created_at;
                                                    }

                                                    $prepStart = $transitions['PREPARATION'] ?? $order->created_at;
                                                    $prepEnd = $transitions['SORTIR'] ?? $transitions['PRODUCTION'] ?? $transitions['QC'] ?? $transitions['FINISH'] ?? ($order->status->value === 'PREPARATION' ? now() : null);
                                                    
                                                    $sortirStart = $transitions['SORTIR'] ?? null;
                                                    $sortirEnd = $transitions['PRODUCTION'] ?? $transitions['QC'] ?? $transitions['FINISH'] ?? ($order->status->value === 'SORTIR' ? now() : null);

                                                    $prodStart = $transitions['PRODUCTION'] ?? null;
                                                    $prodEnd = $transitions['QC'] ?? $transitions['FINISH'] ?? ($order->status->value === 'PRODUCTION' ? now() : null);

                                                    $qcStart = $transitions['QC'] ?? null;
                                                    $qcEnd = $transitions['FINISH'] ?? ($order->status->value === 'QC' ? now() : null);
                                                @endphp
                                                
                                                @if($prepEnd && $prepStart->diffInDays($prepEnd) > 1)
                                                    <span class="text-red-600 dark:text-red-400 font-bold">⚠️ Prep: {{ $prepStart->diffInDays($prepEnd) }} hari (Limit: 1 hr)</span>
                                                @endif
                                                @if($sortirStart && $sortirEnd && $sortirStart->diffInDays($sortirEnd) > 3)
                                                    <span class="text-red-600 dark:text-red-400 font-bold">⚠️ Sortir: {{ $sortirStart->diffInDays($sortirEnd) }} hari (Limit: 3 hr)</span>
                                                @endif
                                                @if($prodStart && $prodEnd && $prodStart->diffInDays($prodEnd) > 4)
                                                    <span class="text-red-650 dark:text-red-400 font-bold">⚠️ Prod: {{ $prodStart->diffInDays($prodEnd) }} hari (Limit: 4 hr)</span>
                                                @endif
                                                @if($qcStart && $qcEnd && $qcStart->diffInDays($qcEnd) > 1)
                                                    <span class="text-red-655 dark:text-red-400 font-bold">⚠️ QC: {{ $qcStart->diffInDays($qcEnd) }} hari (Limit: 1 hr)</span>
                                                @endif
                                            </div>
                                        </td>
                                    @elseif($selectedMetric === 'operational_failed_fast_track')
                                        <td class="px-6 py-4 text-[11px]">
                                            @php
                                                $reason = $order->getNonSlaFailureReason();
                                            @endphp
                                            <span class="font-bold text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-955/20 px-2 py-1 rounded border border-orange-200 dark:border-orange-900/30">
                                                {{ $reason === 'TAMBAH_JASA' ? 'Tambah Jasa Regulasi' : ($reason === 'CX_FOLLOWUP' ? 'CX Followup Issues' : 'Batal Donasi / Hangus') }}
                                            </span>
                                        </td>
                                    @elseif($selectedMetric === 'pending_fast_track')
                                        <td class="px-6 py-4">
                                            <span class="text-purple-600 dark:text-purple-400 font-bold bg-purple-50 dark:bg-purple-955/20 px-2 py-1 rounded border border-purple-200 dark:border-purple-900/30">
                                                ⏳ Menunggu Konfirmasi Gudang
                                            </span>
                                        </td>
                                    @elseif($selectedMetric === 'downgraded_fast_track')
                                        <td class="px-6 py-4 text-gray-550 text-[11px] leading-relaxed">
                                            @php
                                                $downgradedLog = $order->logs->where('action', 'fast_track_downgrade')->first();
                                            @endphp
                                            <div class="flex flex-col gap-0.5">
                                                <span class="font-bold text-gray-800 dark:text-gray-200">{{ $downgradedLog?->description ?? 'SPK diturunkan dari Fast Track karena penambahan jasa reguler.' }}</span>
                                                <span class="text-[9px] opacity-75">Pada: {{ $downgradedLog?->created_at ? $downgradedLog->created_at->format('d M Y H:i') : '-' }} WIB</span>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            {{-- Pagination Links --}}
            @if(!$orders->isEmpty())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 font-bold">
                        <span>Tampilkan</span>
                        <select wire:model.live="perPage" class="bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-850 rounded-xl px-2.5 py-1.5 text-xs font-black text-gray-700 dark:text-gray-300 focus:ring-teal-500 focus:border-teal-500 shadow-sm cursor-pointer">
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="500">Semua</option>
                        </select>
                        <span>data per halaman</span>
                    </div>
                    <div>
                        {{ $orders->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
