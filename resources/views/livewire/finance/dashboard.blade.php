@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* Ultra-premium Flatpickr Custom Overrides - HSL emerald tailored styles */
        .flatpickr-calendar {
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(20px) !important;
            border: 1px solid rgba(241, 245, 249, 0.9) !important;
            border-radius: 24px !important;
            box-shadow: 0 30px 60px -15px rgba(27, 138, 104, 0.08), 0 10px 20px -5px rgba(0, 0, 0, 0.03) !important;
            padding: 8px 6px !important;
            font-family: inherit !important;
            width: 320px !important;
        }
        .flatpickr-day {
            border-radius: 12px !important;
            font-weight: 700 !important;
            font-size: 11px !important;
        }
        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange {
            background: linear-gradient(135deg, #1B8A68 0%, #10B981 100%) !important;
            border-color: transparent !important;
            color: #ffffff !important;
            box-shadow: 0 10px 15px -3px rgba(27, 138, 104, 0.3) !important;
        }
        .flatpickr-day.inRange {
            background: rgba(27, 138, 104, 0.08) !important;
            color: #1B8A68 !important;
        }

        /* Dark mode overrides for Flatpickr */
        .dark .flatpickr-calendar {
            background: rgba(15, 23, 42, 0.95) !important;
            border-color: rgba(51, 65, 85, 0.5) !important;
            box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.5) !important;
        }
        .dark .flatpickr-months .flatpickr-month,
        .dark .flatpickr-weekdays,
        .dark .flatpickr-weekday {
            color: #e2e8f0 !important;
            fill: #e2e8f0 !important;
        }
        .dark .flatpickr-day {
            color: #cbd5e1 !important;
        }
        .dark .flatpickr-day.prevMonthDay, .dark .flatpickr-day.nextMonthDay {
            color: #475569 !important;
        }
        .dark .flatpickr-day:hover {
            background: rgba(255, 255, 255, 0.05) !important;
            color: #ffffff !important;
        }
        .dark .flatpickr-day.inRange {
            background: rgba(27, 138, 104, 0.2) !important;
            color: #34d399 !important;
        }

        /* Ensure Flatpickr inputs inside the capsule are styled properly */
        .flatpickr-input.form-control,
        .flatpickr-input {
            background-color: transparent !important;
            border: none !important;
            box-shadow: none !important;
            outline: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }
    </style>
@endpush

@push('head')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@endpush

@php
    // Calculate total status amounts to show percentages
    $totalStatusAmount = ($data['status_breakdown']['belum_bayar']['total_amount'] ?? 0) 
        + ($data['status_breakdown']['dp_cicil']['total_amount'] ?? 0) 
        + ($data['status_breakdown']['lunas']['total_amount'] ?? 0);
    
    $pctBelumBayar = $totalStatusAmount > 0 ? (($data['status_breakdown']['belum_bayar']['total_amount'] ?? 0) / $totalStatusAmount * 100) : 0;
    $pctDpCicil = $totalStatusAmount > 0 ? (($data['status_breakdown']['dp_cicil']['total_amount'] ?? 0) / $totalStatusAmount * 100) : 0;
    $pctLunas = $totalStatusAmount > 0 ? (($data['status_breakdown']['lunas']['total_amount'] ?? 0) / $totalStatusAmount * 100) : 0;
@endphp

<div class="min-h-screen bg-[#F8FAFC] dark:bg-slate-950 text-slate-800 dark:text-slate-100 transition-colors duration-300" wire:poll.10s>
    
    {{-- Elite Premium Header --}}
    <div class="bg-white/90 dark:bg-slate-900/90 shadow-2xl border-b border-gray-100 dark:border-slate-800/80 sticky top-0 z-40 backdrop-blur-xl transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-5 sm:py-7">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
                {{-- Left Title --}}
                <div class="flex items-center gap-3 sm:gap-6">
                    <div class="p-2.5 sm:p-4 bg-gradient-to-br from-[#2d9b7b] to-emerald-600 rounded-xl sm:rounded-[1.5rem] shadow-[0_10px_30px_-10px_rgba(45,155,123,0.5)] transform -rotate-3 hover:rotate-0 transition-transform duration-500">
                        <svg class="w-5 h-5 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 sm:gap-3 mb-0.5 sm:mb-1">
                            <span class="text-[8px] sm:text-[10px] font-black bg-emerald-50 dark:bg-emerald-950/50 text-[#2d9b7b] dark:text-emerald-400 px-1.5 sm:px-2 py-0.5 rounded-md uppercase tracking-widest italic border border-emerald-100 dark:border-emerald-900/50">RINGKASAN REAKTIF</span>
                            <h1 class="text-xl sm:text-4xl font-black text-gray-900 dark:text-white tracking-tighter leading-none italic uppercase">Dashboard Finance</h1>
                        </div>
                        <p class="text-gray-400 dark:text-slate-400 text-[9px] sm:text-[11px] font-black uppercase tracking-[0.2em] sm:tracking-[0.3em] italic opacity-70 hidden sm:block">Pemantauan Arus Kas, Piutang, & Penagihan (Livewire)</p>
                    </div>
                </div>

                {{-- Date Filter Form --}}
                <div class="flex items-center gap-2 bg-gray-50 dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-xl sm:rounded-[2rem] p-1.5 shadow-inner relative transition-colors duration-300"
                     x-data="{
                         clearRange() {
                             setTimeout(() => {
                                 $wire.set('startDate', '', false);
                                 $wire.set('endDate', '', true);
                             }, 0);
                         }
                     }"
                     wire:ignore
                     wire:key="flatpickr-finance-container">
                    <span class="text-gray-400 pl-3">📅</span>
                    <input type="text" readonly
                           x-init="
                               flatpickr($el, {
                                   mode: 'range',
                                   dateFormat: 'Y-m-d',
                                   altInput: true,
                                   altFormat: 'd/m/Y',
                                   closeOnSelect: true,
                                   locale: {
                                       rangeSeparator: ' s/d '
                                   },
                                   defaultDate: $wire.startDate && $wire.endDate ? [$wire.startDate, $wire.endDate] : null,
                                   onChange: (selectedDates, dateStr, instance) => {
                                       if (selectedDates.length === 2) {
                                           let start = instance.formatDate(selectedDates[0], 'Y-m-d');
                                           let end = instance.formatDate(selectedDates[1], 'Y-m-d');
                                           setTimeout(() => {
                                               $wire.set('startDate', start, false);
                                               $wire.set('endDate', end, true);
                                           }, 0);
                                       }
                                   }
                               });
                               $watch('$wire.startDate', (value) => {
                                   if ($el._flatpickr) {
                                       if (value) {
                                           $el._flatpickr.setDate([value, $wire.endDate], false);
                                       } else {
                                           $el._flatpickr.clear();
                                       }
                                   }
                               });
                               $watch('$wire.endDate', (value) => {
                                   if ($el._flatpickr && value && $wire.startDate) {
                                       $el._flatpickr.setDate([$wire.startDate, value], false);
                                   }
                               });
                           "
                           class="bg-transparent border-0 text-xs sm:text-sm font-bold text-gray-700 dark:text-slate-200 focus:ring-0 cursor-pointer outline-none w-48 sm:w-56"
                           placeholder="Pilih Periode...">
                    
                    {{-- Clear button --}}
                    <button type="button" x-show="$wire.startDate" @click="clearRange()" 
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 transition-colors pr-3 border-none bg-transparent outline-none text-xs font-bold">
                        ✕
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Dashboard Layout --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-10 space-y-8 sm:space-y-10">
        
        {{-- 1. Hero Metrics Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Total Billing Value --}}
            <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-3xl border border-slate-100 dark:border-slate-800/80 p-6 sm:p-8 shadow-xl hover:-translate-y-2 hover:shadow-[0_20px_40px_-15px_rgba(45,155,123,0.15)] dark:hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.6)] hover:border-emerald-500/20 dark:hover:border-emerald-500/30 transition-all duration-300 group relative overflow-hidden">
                <div class="absolute top-0 right-0 w-36 h-36 bg-gradient-to-br from-[#2d9b7b]/10 to-[#1a4d3e]/5 rounded-bl-[5rem] pointer-events-none transition-all duration-500 group-hover:scale-125 group-hover:opacity-85"></div>
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-950/50 text-[#2d9b7b] dark:text-emerald-400 rounded-2xl group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span class="text-[9px] font-black bg-emerald-50 dark:bg-emerald-950/50 text-[#2d9b7b] dark:text-emerald-400 px-2 py-0.5 rounded uppercase tracking-wider italic border border-emerald-100/50 dark:border-emerald-900/30">NILAI TAGIHAN</span>
                </div>
                <h3 class="text-xs font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest italic mb-1.5">Total Nilai Tagihan</h3>
                <div class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white italic tracking-tighter leading-none mb-1 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                    Rp {{ number_format($data['metrics']['total_invoiced_value'], 0, ',', '.') }}
                </div>
                <p class="text-[10px] text-gray-400 dark:text-slate-500 font-bold uppercase tracking-wider italic">Periode Aktif</p>
            </div>

            {{-- Cash Received --}}
            <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-3xl border border-slate-100 dark:border-slate-800/80 p-6 sm:p-8 shadow-xl hover:-translate-y-2 hover:shadow-[0_20px_40px_-15px_rgba(59,130,246,0.15)] dark:hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.6)] hover:border-blue-500/20 dark:hover:border-blue-500/30 transition-all duration-300 group relative overflow-hidden">
                <div class="absolute top-0 right-0 w-36 h-36 bg-gradient-to-br from-blue-500/10 to-indigo-600/5 rounded-bl-[5rem] pointer-events-none transition-all duration-500 group-hover:scale-125 group-hover:opacity-85"></div>
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 rounded-2xl group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <span class="text-[9px] font-black bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 px-2 py-0.5 rounded uppercase tracking-wider italic border border-blue-100/50 dark:border-blue-900/30">KAS MASUK</span>
                </div>
                <h3 class="text-xs font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest italic mb-1.5">Kas Masuk (Tervalidasi)</h3>
                <div class="text-2xl sm:text-3xl font-black text-blue-600 dark:text-blue-400 italic tracking-tighter leading-none mb-1 group-hover:text-blue-700 dark:group-hover:text-blue-300 transition-colors">
                    Rp {{ number_format($data['metrics']['total_cash_received'], 0, ',', '.') }}
                </div>
                <p class="text-[10px] text-gray-400 dark:text-slate-500 font-bold uppercase tracking-wider italic">Realisasi Penerimaan</p>
            </div>

            {{-- Outstanding Receivables --}}
            <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-3xl border border-slate-100 dark:border-slate-800/80 p-6 sm:p-8 shadow-xl hover:-translate-y-2 hover:shadow-[0_20px_40px_-15px_rgba(244,63,94,0.15)] dark:hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.6)] hover:border-rose-500/20 dark:hover:border-rose-500/30 transition-all duration-300 group relative overflow-hidden">
                <div class="absolute top-0 right-0 w-36 h-36 bg-gradient-to-br from-rose-500/10 to-red-600/5 rounded-bl-[5rem] pointer-events-none transition-all duration-500 group-hover:scale-125 group-hover:opacity-85"></div>
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 rounded-2xl group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-[9px] font-black bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 px-2 py-0.5 rounded uppercase tracking-wider italic border border-rose-100/50 dark:border-rose-900/30">PIUTANG BERJALAN</span>
                </div>
                <h3 class="text-xs font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest italic mb-1.5">Sisa Piutang Aktif</h3>
                <div class="text-2xl sm:text-3xl font-black text-rose-600 dark:text-rose-400 italic tracking-tighter leading-none mb-1 group-hover:text-rose-700 dark:group-hover:text-rose-300 transition-colors">
                    Rp {{ number_format($data['metrics']['total_outstanding_receivables'], 0, ',', '.') }}
                </div>
                <p class="text-[10px] text-gray-400 dark:text-slate-500 font-bold uppercase tracking-wider italic">Belum Tertagih</p>
            </div>

            {{-- Collection Rate --}}
            <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-3xl border border-slate-100 dark:border-slate-800/80 p-6 sm:p-8 shadow-xl hover:-translate-y-2 hover:shadow-[0_20px_40px_-15px_rgba(245,158,11,0.15)] dark:hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.6)] hover:border-amber-500/20 dark:hover:border-amber-500/30 transition-all duration-300 group relative overflow-hidden">
                <div class="absolute top-0 right-0 w-36 h-36 bg-gradient-to-br from-amber-500/10 to-yellow-600/5 rounded-bl-[5rem] pointer-events-none transition-all duration-500 group-hover:scale-125 group-hover:opacity-85"></div>
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 rounded-2xl group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-[9px] font-black bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 px-2 py-0.5 rounded uppercase tracking-wider italic border border-amber-100/50 dark:border-amber-900/30">RASIO PENAGIHAN</span>
                </div>
                <h3 class="text-xs font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest italic mb-1.5">Rasio Penagihan (Collection)</h3>
                <div class="text-2xl sm:text-3xl font-black text-amber-600 dark:text-amber-400 italic tracking-tighter leading-none mb-1 group-hover:text-amber-700 dark:group-hover:text-amber-300 transition-colors">
                    {{ $data['metrics']['collection_rate'] }}%
                </div>
                <p class="text-[10px] text-gray-400 dark:text-slate-500 font-bold uppercase tracking-wider italic">Efektivitas Cash Flow</p>
            </div>
        </div>

        {{-- 2. Status Breakdown --}}
        <div class="bg-white/80 dark:bg-slate-900/80 border border-slate-100 dark:border-slate-800/80 backdrop-blur-md rounded-3xl p-6 sm:p-8 shadow-xl transition-colors duration-300">
            <h3 class="text-lg font-black text-gray-900 dark:text-white italic uppercase tracking-tight mb-5">Distribusi Status Tagihan</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Belum Bayar --}}
                <div class="flex flex-col p-4 bg-gray-50 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800/40 rounded-2xl hover:bg-gray-100 dark:hover:bg-slate-950/60 transition-colors shadow-inner">
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center gap-3">
                            <span class="w-3.5 h-3.5 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                            <div>
                                <h4 class="text-sm font-black text-gray-900 dark:text-slate-200 italic uppercase tracking-tight">Belum Bayar</h4>
                                <p class="text-[10px] text-gray-400 dark:text-slate-500 font-bold uppercase tracking-wider italic">{{ $data['status_breakdown']['belum_bayar']['count'] }} Transaksi</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-baseline justify-between mt-2 mb-1">
                        <span class="text-xs text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider italic">Total Nominal</span>
                        <span class="text-sm sm:text-base font-black text-gray-900 dark:text-slate-100 italic">Rp {{ number_format($data['status_breakdown']['belum_bayar']['total_amount'], 0, ',', '.') }}</span>
                    </div>
                    {{-- Sleek percentage bar --}}
                    <div class="w-full bg-slate-200/60 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden mt-1">
                        <div class="bg-slate-400 dark:bg-slate-500 h-full rounded-full transition-all duration-500" style="width: {{ $pctBelumBayar }}%"></div>
                    </div>
                </div>

                {{-- DP/Cicil --}}
                <div class="flex flex-col p-4 bg-amber-50/50 dark:bg-amber-950/10 border border-amber-100/30 dark:border-amber-900/20 rounded-2xl hover:bg-amber-100/30 dark:hover:bg-amber-950/20 transition-colors shadow-inner">
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center gap-3">
                            <span class="w-3.5 h-3.5 rounded-full bg-[#FFC232] animate-pulse"></span>
                            <div>
                                <h4 class="text-sm font-black text-[#FFC232] italic uppercase tracking-tight">DP/Cicil</h4>
                                <p class="text-[10px] text-gray-400 dark:text-slate-500 font-bold uppercase tracking-wider italic">{{ $data['status_breakdown']['dp_cicil']['count'] }} Transaksi</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-baseline justify-between mt-2 mb-1">
                        <span class="text-xs text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider italic">Total Nominal</span>
                        <span class="text-sm sm:text-base font-black text-gray-900 dark:text-slate-100 italic">Rp {{ number_format($data['status_breakdown']['dp_cicil']['total_amount'], 0, ',', '.') }}</span>
                    </div>
                    {{-- Sleek percentage bar --}}
                    <div class="w-full bg-slate-200/60 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden mt-1">
                        <div class="bg-[#FFC232] h-full rounded-full transition-all duration-500" style="width: {{ $pctDpCicil }}%"></div>
                    </div>
                </div>

                {{-- Lunas --}}
                <div class="flex flex-col p-4 bg-emerald-50/50 dark:bg-emerald-950/10 border border-emerald-100/30 dark:border-emerald-900/20 rounded-2xl hover:bg-emerald-100/30 dark:hover:bg-emerald-950/20 transition-colors shadow-inner">
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center gap-3">
                            <span class="w-3.5 h-3.5 rounded-full bg-[#2d9b7b] dark:bg-emerald-400"></span>
                            <div>
                                <h4 class="text-sm font-black text-[#2d9b7b] dark:text-emerald-400 italic uppercase tracking-tight">Lunas</h4>
                                <p class="text-[10px] text-gray-400 dark:text-slate-500 font-bold uppercase tracking-wider italic">{{ $data['status_breakdown']['lunas']['count'] }} Transaksi</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-baseline justify-between mt-2 mb-1">
                        <span class="text-xs text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider italic">Total Nominal</span>
                        <span class="text-sm sm:text-base font-black text-gray-900 dark:text-slate-100 italic">Rp {{ number_format($data['status_breakdown']['lunas']['total_amount'], 0, ',', '.') }}</span>
                    </div>
                    {{-- Sleek percentage bar --}}
                    <div class="w-full bg-slate-200/60 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden mt-1">
                        <div class="bg-[#2d9b7b] dark:bg-emerald-400 h-full rounded-full transition-all duration-500" style="width: {{ $pctLunas }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Distribusi Type Pembayaran --}}
        <div class="bg-white/80 dark:bg-slate-900/80 border border-slate-100 dark:border-slate-800/80 backdrop-blur-md rounded-3xl p-6 sm:p-8 shadow-xl transition-colors duration-300">
            <h3 class="text-lg font-black text-gray-900 dark:text-white italic uppercase tracking-tight mb-5">Distribusi Type Pembayaran</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 {{ (isset($paymentTypeBreakdown['LAINNYA']) && $paymentTypeBreakdown['LAINNYA']['count'] > 0) ? 'lg:grid-cols-7' : 'lg:grid-cols-6' }} gap-4 sm:gap-5">
                {{-- BEFORE --}}
                <div class="relative overflow-hidden bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-2xl border border-slate-100 dark:border-slate-800/80 p-5 shadow-lg hover:-translate-y-1 hover:shadow-[0_15px_30px_-10px_rgba(59,130,246,0.15)] dark:hover:shadow-[0_15px_30px_-10px_rgba(0,0,0,0.5)] hover:border-blue-500/20 dark:hover:border-blue-500/30 transition-all duration-300 group">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-500/10 to-indigo-600/5 rounded-bl-[3rem] pointer-events-none transition-all duration-500 group-hover:scale-125 group-hover:opacity-85"></div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2.5 bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 rounded-xl group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-[8px] font-black bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 px-1.5 py-0.5 rounded uppercase tracking-wider italic border border-blue-100/50 dark:border-blue-900/30">BEFORE</span>
                    </div>
                    <h4 class="text-[10px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest italic mb-1">DP Awal</h4>
                    <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white italic tracking-tighter leading-none mb-0.5 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                        Rp {{ number_format($paymentTypeBreakdown['BEFORE']['total_amount'], 0, ',', '.') }}
                    </div>
                    <p class="text-[9px] text-gray-400 dark:text-slate-500 font-bold uppercase tracking-wider italic">{{ $paymentTypeBreakdown['BEFORE']['count'] }} Transaksi</p>
                    <div class="w-full bg-slate-200/60 dark:bg-slate-800 h-1 rounded-full overflow-hidden mt-2">
                        <div class="bg-blue-500 h-full rounded-full transition-all duration-500" style="width: {{ $paymentTypeBreakdown['BEFORE']['percentage'] }}%"></div>
                    </div>
                </div>

                {{-- AFTER --}}
                <div class="relative overflow-hidden bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-2xl border border-slate-100 dark:border-slate-800/80 p-5 shadow-lg hover:-translate-y-1 hover:shadow-[0_15px_30px_-10px_rgba(16,185,129,0.15)] dark:hover:shadow-[0_15px_30px_-10px_rgba(0,0,0,0.5)] hover:border-emerald-500/20 dark:hover:border-emerald-500/30 transition-all duration-300 group">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-emerald-500/10 to-teal-600/5 rounded-bl-[3rem] pointer-events-none transition-all duration-500 group-hover:scale-125 group-hover:opacity-85"></div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2.5 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 rounded-xl group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-[8px] font-black bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 px-1.5 py-0.5 rounded uppercase tracking-wider italic border border-emerald-100/50 dark:border-emerald-900/30">AFTER</span>
                    </div>
                    <h4 class="text-[10px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest italic mb-1">Pelunasan</h4>
                    <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white italic tracking-tighter leading-none mb-0.5 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                        Rp {{ number_format($paymentTypeBreakdown['AFTER']['total_amount'], 0, ',', '.') }}
                    </div>
                    <p class="text-[9px] text-gray-400 dark:text-slate-500 font-bold uppercase tracking-wider italic">{{ $paymentTypeBreakdown['AFTER']['count'] }} Transaksi</p>
                    <div class="w-full bg-slate-200/60 dark:bg-slate-800 h-1 rounded-full overflow-hidden mt-2">
                        <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ $paymentTypeBreakdown['AFTER']['percentage'] }}%"></div>
                    </div>
                </div>

                {{-- TAMBAH_JASA --}}
                <div class="relative overflow-hidden bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-2xl border border-slate-100 dark:border-slate-800/80 p-5 shadow-lg hover:-translate-y-1 hover:shadow-[0_15px_30px_-10px_rgba(168,85,247,0.15)] dark:hover:shadow-[0_15px_30px_-10px_rgba(0,0,0,0.5)] hover:border-purple-500/20 dark:hover:border-purple-500/30 transition-all duration-300 group">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-purple-500/10 to-violet-600/5 rounded-bl-[3rem] pointer-events-none transition-all duration-500 group-hover:scale-125 group-hover:opacity-85"></div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2.5 bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400 rounded-xl group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        </div>
                        <span class="text-[8px] font-black bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400 px-1.5 py-0.5 rounded uppercase tracking-wider italic border border-purple-100/50 dark:border-purple-900/30">TAMBAH JASA</span>
                    </div>
                    <h4 class="text-[10px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest italic mb-1">Tambah Jasa</h4>
                    <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white italic tracking-tighter leading-none mb-0.5 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                        Rp {{ number_format($paymentTypeBreakdown['TAMBAH_JASA']['total_amount'], 0, ',', '.') }}
                    </div>
                    <p class="text-[9px] text-gray-400 dark:text-slate-500 font-bold uppercase tracking-wider italic">{{ $paymentTypeBreakdown['TAMBAH_JASA']['count'] }} Transaksi</p>
                    <div class="w-full bg-slate-200/60 dark:bg-slate-800 h-1 rounded-full overflow-hidden mt-2">
                        <div class="bg-purple-500 h-full rounded-full transition-all duration-500" style="width: {{ $paymentTypeBreakdown['TAMBAH_JASA']['percentage'] }}%"></div>
                    </div>
                </div>

                {{-- LUNAS_AWAL --}}
                <div class="relative overflow-hidden bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-2xl border border-slate-100 dark:border-slate-800/80 p-5 shadow-lg hover:-translate-y-1 hover:shadow-[0_15px_30px_-10px_rgba(245,158,11,0.15)] dark:hover:shadow-[0_15px_30px_-10px_rgba(0,0,0,0.5)] hover:border-amber-500/20 dark:hover:border-amber-500/30 transition-all duration-300 group">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-amber-500/10 to-yellow-600/5 rounded-bl-[3rem] pointer-events-none transition-all duration-500 group-hover:scale-125 group-hover:opacity-85"></div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2.5 bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 rounded-xl group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <span class="text-[8px] font-black bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 px-1.5 py-0.5 rounded uppercase tracking-wider italic border border-amber-100/50 dark:border-amber-900/30">LUNAS AWAL</span>
                    </div>
                    <h4 class="text-[10px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest italic mb-1">Lunas Awal</h4>
                    <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white italic tracking-tighter leading-none mb-0.5 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">
                        Rp {{ number_format($paymentTypeBreakdown['LUNAS_AWAL']['total_amount'], 0, ',', '.') }}
                    </div>
                    <p class="text-[9px] text-gray-400 dark:text-slate-500 font-bold uppercase tracking-wider italic">{{ $paymentTypeBreakdown['LUNAS_AWAL']['count'] }} Transaksi</p>
                    <div class="w-full bg-slate-200/60 dark:bg-slate-800 h-1 rounded-full overflow-hidden mt-2">
                        <div class="bg-amber-500 h-full rounded-full transition-all duration-500" style="width: {{ $paymentTypeBreakdown['LUNAS_AWAL']['percentage'] }}%"></div>
                    </div>
                </div>

                {{-- ONGKIR --}}
                <div class="relative overflow-hidden bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-2xl border border-slate-100 dark:border-slate-800/80 p-5 shadow-lg hover:-translate-y-1 hover:shadow-[0_15px_30px_-10px_rgba(244,63,94,0.15)] dark:hover:shadow-[0_15px_30px_-10px_rgba(0,0,0,0.5)] hover:border-rose-500/20 dark:hover:border-rose-500/30 transition-all duration-300 group">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-rose-500/10 to-red-600/5 rounded-bl-[3rem] pointer-events-none transition-all duration-500 group-hover:scale-125 group-hover:opacity-85"></div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2.5 bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 rounded-xl group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        </div>
                        <span class="text-[8px] font-black bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 px-1.5 py-0.5 rounded uppercase tracking-wider italic border border-rose-100/50 dark:border-rose-900/30">ONGKIR</span>
                    </div>
                    <h4 class="text-[10px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest italic mb-1">Ongkos Kirim</h4>
                    <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white italic tracking-tighter leading-none mb-0.5 group-hover:text-rose-600 dark:group-hover:text-rose-400 transition-colors">
                        Rp {{ number_format($paymentTypeBreakdown['ONGKIR']['total_amount'], 0, ',', '.') }}
                    </div>
                    <p class="text-[9px] text-gray-400 dark:text-slate-500 font-bold uppercase tracking-wider italic">{{ $paymentTypeBreakdown['ONGKIR']['count'] }} Transaksi</p>
                    <div class="w-full bg-slate-200/60 dark:bg-slate-800 h-1 rounded-full overflow-hidden mt-2">
                        <div class="bg-rose-500 h-full rounded-full transition-all duration-500" style="width: {{ $paymentTypeBreakdown['ONGKIR']['percentage'] }}%"></div>
                    </div>
                </div>

                {{-- OTO --}}
                <div class="relative overflow-hidden bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-2xl border border-slate-100 dark:border-slate-800/80 p-5 shadow-lg hover:-translate-y-1 hover:shadow-[0_15px_30px_-10px_rgba(219,39,119,0.15)] dark:hover:shadow-[0_15px_30px_-10px_rgba(0,0,0,0.5)] hover:border-pink-500/20 dark:hover:border-pink-500/30 transition-all duration-300 group">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-pink-500/10 to-rose-600/5 rounded-bl-[3rem] pointer-events-none transition-all duration-500 group-hover:scale-125 group-hover:opacity-85"></div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2.5 bg-pink-50 dark:bg-pink-950/50 text-pink-600 dark:text-pink-400 rounded-xl group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <span class="text-[8px] font-black bg-pink-50 dark:bg-pink-950/50 text-pink-600 dark:text-pink-400 px-1.5 py-0.5 rounded uppercase tracking-wider italic border border-pink-100/50 dark:border-pink-900/30">OTO</span>
                    </div>
                    <h4 class="text-[10px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest italic mb-1">Pembayaran OTO</h4>
                    <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white italic tracking-tighter leading-none mb-0.5 group-hover:text-pink-600 dark:group-hover:text-pink-400 transition-colors">
                        Rp {{ number_format($paymentTypeBreakdown['OTO']['total_amount'], 0, ',', '.') }}
                    </div>
                    <p class="text-[9px] text-gray-400 dark:text-slate-500 font-bold uppercase tracking-wider italic">{{ $paymentTypeBreakdown['OTO']['count'] }} Transaksi</p>
                    <div class="w-full bg-slate-200/60 dark:bg-slate-800 h-1 rounded-full overflow-hidden mt-2">
                        <div class="bg-pink-500 h-full rounded-full transition-all duration-500" style="width: {{ $paymentTypeBreakdown['OTO']['percentage'] }}%"></div>
                    </div>
                </div>

                {{-- LAINNYA --}}
                @if(isset($paymentTypeBreakdown['LAINNYA']) && $paymentTypeBreakdown['LAINNYA']['count'] > 0)
                <div class="relative overflow-hidden bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-2xl border border-slate-100 dark:border-slate-800/80 p-5 shadow-lg hover:-translate-y-1 hover:shadow-[0_15px_30px_-10px_rgba(148,163,184,0.15)] dark:hover:shadow-[0_15px_30px_-10px_rgba(0,0,0,0.5)] hover:border-slate-500/20 dark:hover:border-slate-500/30 transition-all duration-300 group">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-slate-500/10 to-slate-600/5 rounded-bl-[3rem] pointer-events-none transition-all duration-500 group-hover:scale-125 group-hover:opacity-85"></div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2.5 bg-slate-50 dark:bg-slate-950/50 text-slate-600 dark:text-slate-400 rounded-xl group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-[8px] font-black bg-slate-50 dark:bg-slate-950/50 text-slate-600 dark:text-slate-400 px-1.5 py-0.5 rounded uppercase tracking-wider italic border border-slate-100/50 dark:border-slate-900/30">LAINNYA</span>
                    </div>
                    <h4 class="text-[10px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest italic mb-1">Tanpa Kategori</h4>
                    <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white italic tracking-tighter leading-none mb-0.5 group-hover:text-slate-600 dark:group-hover:text-slate-400 transition-colors">
                        Rp {{ number_format($paymentTypeBreakdown['LAINNYA']['total_amount'], 0, ',', '.') }}
                    </div>
                    <p class="text-[9px] text-gray-400 dark:text-slate-500 font-bold uppercase tracking-wider italic">{{ $paymentTypeBreakdown['LAINNYA']['count'] }} Transaksi</p>
                    <div class="w-full bg-slate-200/60 dark:bg-slate-800 h-1 rounded-full overflow-hidden mt-2">
                        <div class="bg-slate-400 h-full rounded-full transition-all duration-500" style="width: {{ $paymentTypeBreakdown['LAINNYA']['percentage'] }}%"></div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- 4. Dual-Tab Data Tables Section --}}
        <div class="bg-white/80 dark:bg-slate-900/80 border border-slate-100 dark:border-slate-800/80 backdrop-blur-md rounded-3xl shadow-xl overflow-hidden transition-colors duration-300"
             x-data="{ tab: @entangle('activeTab') }">

            {{-- Tab Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 dark:border-slate-800/80 bg-gray-50/50 dark:bg-slate-950/30 px-4 sm:px-6 py-3 gap-3">
                <div class="flex items-center gap-1 p-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-inner">
                    <button @click="tab = 'invoices'; $wire.switchTab('invoices')"
                            :class="tab === 'invoices'
                                ? 'bg-gradient-to-br from-[#2d9b7b] to-emerald-600 text-white shadow-lg shadow-emerald-500/25'
                                : 'text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-800'"
                            class="px-4 sm:px-5 py-2 text-[10px] sm:text-xs font-black uppercase tracking-wider italic rounded-xl transition-all duration-300">
                        📋 Data Invoices
                    </button>
                    <button @click="tab = 'payments'; $wire.switchTab('payments')"
                            :class="tab === 'payments'
                                ? 'bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-500/25'
                                : 'text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-800'"
                            class="px-4 sm:px-5 py-2 text-[10px] sm:text-xs font-black uppercase tracking-wider italic rounded-xl transition-all duration-300">
                        💰 Data Pembayaran
                    </button>
                </div>

                {{-- Export PDF Button --}}
                <a href="{{ $exportPdfUrl }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-rose-500 to-red-600 hover:from-rose-600 hover:to-red-700 text-white text-[10px] sm:text-xs font-black uppercase tracking-wider italic rounded-xl shadow-lg shadow-rose-500/20 hover:shadow-rose-500/30 hover:-translate-y-0.5 active:scale-95 transition-all duration-300">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export PDF
                </a>
            </div>

            {{-- Filter Bar --}}
            <div class="px-4 sm:px-6 py-3 border-b border-slate-100/50 dark:border-slate-800/50 bg-white/50 dark:bg-slate-900/50">
                {{-- Invoices Status Filter --}}
                <div x-show="tab === 'invoices'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="flex flex-wrap items-center gap-2">
                    <span class="text-[9px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest italic mr-1">Filter Status:</span>
                    <button wire:click="$set('filterStatus', '')"
                            class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider rounded-lg border transition-all duration-200
                            {{ $filterStatus === '' ? 'bg-slate-800 dark:bg-slate-200 text-white dark:text-slate-900 border-slate-800 dark:border-slate-200 shadow-md' : 'bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-400 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        Semua
                    </button>
                    <button wire:click="$set('filterStatus', 'BB')"
                            class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider rounded-lg border transition-all duration-200
                            {{ $filterStatus === 'BB' ? 'bg-slate-500 text-white border-slate-500 shadow-md' : 'bg-white dark:bg-slate-900 text-slate-400 dark:text-slate-500 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        BB <span class="opacity-60 text-[8px]">Belum Bayar</span>
                    </button>
                    <button wire:click="$set('filterStatus', 'BL')"
                            class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider rounded-lg border transition-all duration-200
                            {{ $filterStatus === 'BL' ? 'bg-amber-500 text-white border-amber-500 shadow-md shadow-amber-500/20' : 'bg-white dark:bg-slate-900 text-amber-500 border-slate-200 dark:border-slate-700 hover:bg-amber-50 dark:hover:bg-slate-800' }}">
                        BL <span class="opacity-60 text-[8px]">DP/Cicil</span>
                    </button>
                    <button wire:click="$set('filterStatus', 'L')"
                            class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider rounded-lg border transition-all duration-200
                            {{ $filterStatus === 'L' ? 'bg-emerald-500 text-white border-emerald-500 shadow-md shadow-emerald-500/20' : 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 border-slate-200 dark:border-slate-700 hover:bg-emerald-50 dark:hover:bg-slate-800' }}">
                        L <span class="opacity-60 text-[8px]">Lunas</span>
                    </button>
                </div>

                {{-- Payments Type Filter --}}
                <div x-show="tab === 'payments'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="flex flex-wrap items-center gap-2">
                    <span class="text-[9px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest italic mr-1">Filter Type:</span>
                    <button wire:click="$set('filterType', '')"
                            class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider rounded-lg border transition-all duration-200
                            {{ $filterType === '' ? 'bg-slate-800 dark:bg-slate-200 text-white dark:text-slate-900 border-slate-800 dark:border-slate-200 shadow-md' : 'bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-400 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        Semua
                    </button>
                    @foreach(['BEFORE', 'AFTER', 'TAMBAH_JASA', 'LUNAS_AWAL', 'ONGKIR'] as $type)
                        <button wire:click="$set('filterType', '{{ $type }}')"
                                class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider rounded-lg border transition-all duration-200
                                {{ $filterType === $type ? 'bg-blue-500 text-white border-blue-500 shadow-md shadow-blue-500/20' : 'bg-white dark:bg-slate-900 text-blue-500 dark:text-blue-400 border-slate-200 dark:border-slate-700 hover:bg-blue-50 dark:hover:bg-slate-800' }}">
                            {{ $type }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Tab Content: Invoices --}}
            <div x-show="tab === 'invoices'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-slate-950/60 border-b border-slate-100 dark:border-slate-800/80">
                                <th class="px-4 py-3 text-[9px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest italic w-10 text-center">#</th>
                                <th class="px-4 py-3 text-[9px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest italic">No. Invoice</th>
                                <th class="px-4 py-3 text-[9px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest italic">Customer</th>
                                <th class="px-4 py-3 text-[9px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest italic text-right">Total</th>
                                <th class="px-4 py-3 text-[9px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest italic text-right">Ongkir</th>
                                <th class="px-4 py-3 text-[9px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest italic text-right">Diskon</th>
                                <th class="px-4 py-3 text-[9px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest italic text-right">Terbayar</th>
                                <th class="px-4 py-3 text-[9px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest italic text-right">Sisa</th>
                                <th class="px-4 py-3 text-[9px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest italic text-center">Status</th>
                                <th class="px-4 py-3 text-[9px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest italic text-center">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-800/40">
                            @forelse($invoices as $idx => $inv)
                                @php
                                    $remaining = $inv->remaining_balance;
                                    $statusCode = $inv->payment_status_code;
                                @endphp
                                <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-950/30 transition-colors duration-150 group">
                                    <td class="px-4 py-3 text-xs text-gray-400 dark:text-slate-500 text-center font-mono">{{ $invoices->firstItem() + $idx }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('finance.invoices.show', $inv->id) }}" class="text-xs font-black text-gray-900 dark:text-slate-200 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors uppercase italic">
                                            {{ $inv->invoice_number }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-600 dark:text-slate-300 font-semibold">{{ $inv->customer->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-xs font-bold text-gray-900 dark:text-slate-100 text-right font-mono">{{ number_format($inv->total_amount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-500 dark:text-slate-400 text-right font-mono">{{ number_format($inv->shipping_cost, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-500 dark:text-slate-400 text-right font-mono">{{ number_format($inv->discount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-xs font-bold text-emerald-600 dark:text-emerald-400 text-right font-mono">{{ number_format($inv->paid_amount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-xs font-black text-right font-mono {{ $remaining > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                        {{ number_format($remaining, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($statusCode === 'BB')
                                            <span class="inline-block px-2 py-0.5 text-[8px] font-black uppercase tracking-wider rounded-md bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700">BB</span>
                                        @elseif($statusCode === 'BL')
                                            <span class="inline-block px-2 py-0.5 text-[8px] font-black uppercase tracking-wider rounded-md bg-amber-50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-900/40">BL</span>
                                        @else
                                            <span class="inline-block px-2 py-0.5 text-[8px] font-black uppercase tracking-wider rounded-md bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900/40">L</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-[10px] text-gray-400 dark:text-slate-500 text-center font-mono">{{ $inv->created_at->format('d/m/Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-4 py-16 text-center">
                                        <span class="text-3xl block mb-2 opacity-20">📋</span>
                                        <p class="text-[11px] font-black text-gray-300 dark:text-slate-700 uppercase tracking-widest italic">Tidak ada data invoice untuk filter ini</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($invoices->hasPages())
                    <div class="px-4 sm:px-6 py-4 border-t border-slate-100 dark:border-slate-800/80 bg-gray-50/30 dark:bg-slate-950/20">
                        {{ $invoices->links() }}
                    </div>
                @endif

                {{-- Summary Footer --}}
                <div class="px-4 sm:px-6 py-3 border-t border-slate-100 dark:border-slate-800/80 bg-gray-50/50 dark:bg-slate-950/30 flex flex-wrap items-center justify-between gap-2">
                    <span class="text-[10px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider italic">
                        Menampilkan {{ $invoices->firstItem() ?? 0 }}-{{ $invoices->lastItem() ?? 0 }} dari {{ $invoices->total() }} data
                    </span>
                </div>
            </div>

            {{-- Tab Content: Payments --}}
            <div x-show="tab === 'payments'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-slate-950/60 border-b border-slate-100 dark:border-slate-800/80">
                                <th class="px-4 py-3 text-[9px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest italic w-10 text-center">#</th>
                                <th class="px-4 py-3 text-[9px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest italic">No. Invoice</th>
                                <th class="px-4 py-3 text-[9px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest italic text-right">Jumlah (Rp)</th>
                                <th class="px-4 py-3 text-[9px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest italic text-center">Tgl Bayar</th>
                                <th class="px-4 py-3 text-[9px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest italic text-center">Type</th>
                                <th class="px-4 py-3 text-[9px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest italic text-center">Verifikasi</th>
                                <th class="px-4 py-3 text-[9px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest italic">Catatan</th>
                                <th class="px-4 py-3 text-[9px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest italic">Dibuat Oleh</th>
                                <th class="px-4 py-3 text-[9px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest italic text-center">Waktu Input</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-800/40">
                            @forelse($payments as $idx => $pay)
                                <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-950/30 transition-colors duration-150 group">
                                    <td class="px-4 py-3 text-xs text-gray-400 dark:text-slate-500 text-center font-mono">{{ $payments->firstItem() + $idx }}</td>
                                    <td class="px-4 py-3">
                                        @if($pay->invoice)
                                            <a href="{{ route('finance.invoices.show', $pay->invoice->id) }}" class="text-xs font-black text-gray-900 dark:text-slate-200 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors uppercase italic">
                                                {{ $pay->invoice->invoice_number }}
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400 italic">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-xs font-black text-blue-600 dark:text-blue-400 text-right font-mono">{{ number_format($pay->amount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-[10px] text-gray-500 dark:text-slate-400 text-center font-mono">{{ $pay->payment_date ? $pay->payment_date->format('d/m/Y') : '-' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-block px-2 py-0.5 text-[8px] font-black uppercase tracking-wider rounded-md bg-indigo-50 dark:bg-indigo-950/30 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-900/40">
                                            {{ $pay->type ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($pay->verified)
                                            <span class="inline-block px-2 py-0.5 text-[8px] font-black uppercase tracking-wider rounded-md bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900/40">✓ Ya</span>
                                        @else
                                            <span class="inline-block px-2 py-0.5 text-[8px] font-black uppercase tracking-wider rounded-md bg-amber-50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-900/40">Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-[10px] text-gray-500 dark:text-slate-400 max-w-[180px] truncate">{{ $pay->notes ?? '-' }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-600 dark:text-slate-300 font-semibold">{{ $pay->creator->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-[10px] text-gray-400 dark:text-slate-500 text-center font-mono">{{ $pay->created_at ? $pay->created_at->format('d/m/Y H:i') : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-16 text-center">
                                        <span class="text-3xl block mb-2 opacity-20">💰</span>
                                        <p class="text-[11px] font-black text-gray-300 dark:text-slate-700 uppercase tracking-widest italic">Tidak ada data pembayaran untuk filter ini</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($payments->hasPages())
                    <div class="px-4 sm:px-6 py-4 border-t border-slate-100 dark:border-slate-800/80 bg-gray-50/30 dark:bg-slate-950/20">
                        {{ $payments->links() }}
                    </div>
                @endif

                {{-- Summary Footer --}}
                <div class="px-4 sm:px-6 py-3 border-t border-slate-100 dark:border-slate-800/80 bg-gray-50/50 dark:bg-slate-950/30 flex flex-wrap items-center justify-between gap-2">
                    <span class="text-[10px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider italic">
                        Menampilkan {{ $payments->firstItem() ?? 0 }}-{{ $payments->lastItem() ?? 0 }} dari {{ $payments->total() }} data
                    </span>
                </div>
            </div>
        </div>

        {{-- API Developer panel (Real-Time Integration Panel) --}}
        <div class="bg-slate-950 border border-slate-850/80 shadow-2xl p-6 sm:p-8 mt-12 relative overflow-hidden rounded-3xl">
            {{-- Decorative accent --}}
            <div class="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-emerald-500 via-[#2d9b7b] to-indigo-500"></div>

            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-6">
                <div>
                    <h3 class="text-sm sm:text-base font-black text-slate-100 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        ⚡ Integrasi Real-Time Finance API
                    </h3>
                    <p class="text-xs text-slate-400 mt-1 font-medium">
                        Gunakan endpoint di bawah ini untuk menarik data status arus kas, piutang, dan metrik keuangan secara real-time dari aplikasi luar.
                    </p>
                </div>
                <span class="px-3 py-1 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-black uppercase tracking-widest shrink-0">
                    Active Endpoint
                </span>
            </div>

            <div class="space-y-6">
                {{-- URL Input Box --}}
                <div class="flex flex-col gap-2" x-data="{ 
                    copied: false,
                    apiUrl: '{{ url('/api/v1/finance/dashboard') }}{{ $apiKey ? '?api_key=' . $apiKey : '' }}',
                    copyToClipboard() {
                        try {
                            this.$refs.apiFinanceInput.select();
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
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Request Endpoint URL (GET)</label>
                    <div class="flex gap-2">
                        <input x-ref="apiFinanceInput" type="text" readonly :value="apiUrl" 
                               class="flex-grow bg-slate-900/60 border border-slate-800/80 rounded-xl py-3 px-4 text-xs font-mono text-emerald-400 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        <button @click="copyToClipboard()" class="px-5 py-3 bg-emerald-500 hover:bg-emerald-600 active:scale-95 text-slate-950 text-[10px] font-black rounded-xl transition-all shadow-lg shadow-emerald-500/20 flex items-center gap-2 shrink-0">
                            <span x-show="!copied">📋 COPY URL</span>
                            <span x-show="copied" x-cloak>✅ COPIED!</span>
                        </button>
                    </div>
                </div>

                {{-- Header & Key --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest">HTTP Header Key</label>
                        <input type="text" readonly value="X-API-KEY" 
                               class="bg-slate-900/60 border border-slate-800/80 rounded-xl py-3 px-4 text-xs font-mono text-slate-300 focus:outline-none">
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest">HTTP Header Value (API Key)</label>
                        <input type="text" readonly value="{{ $apiKey ?: 'DASHBOARD_API_KEY_NOT_CONFIGURED' }}" 
                               class="bg-slate-900/60 border border-slate-800/80 rounded-xl py-3 px-4 text-xs font-mono text-emerald-400 focus:outline-none">
                    </div>
                </div>

                {{-- Parameters Info --}}
                <div class="pt-4 border-t border-slate-800/80">
                    <h4 class="text-[10px] font-black text-slate-300 uppercase tracking-wider mb-2">Query Parameters yang Didukung:</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-[11px] font-semibold">
                        <div class="bg-slate-900/40 p-3 rounded-xl border border-slate-800/40">
                            <span class="text-emerald-400 font-mono">start_date</span>
                            <p class="text-[9px] text-slate-400 mt-1">Tanggal awal filter periode (e.g. `YYYY-MM-DD`)</p>
                        </div>
                        <div class="bg-slate-900/40 p-3 rounded-xl border border-slate-800/40">
                            <span class="text-emerald-400 font-mono">end_date</span>
                            <p class="text-[9px] text-slate-400 mt-1">Tanggal akhir filter periode (e.g. `YYYY-MM-DD`)</p>
                        </div>
                        <div class="bg-slate-900/40 p-3 rounded-xl border border-slate-800/40">
                            <span class="text-emerald-400 font-mono">api_key / key</span>
                            <p class="text-[9px] text-slate-400 mt-1">API key otentikasi (jika tidak dikirim via HTTP header)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
