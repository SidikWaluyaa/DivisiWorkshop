<div x-data="{ showPreview: false, previewUrl: '' }" class="min-h-screen pb-16">
    {{-- Flatpickr Styles & Scripts --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-500/20 to-teal-400/30 border border-emerald-500/30 flex items-center justify-center text-emerald-300 shadow-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-0.5">
                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-white/10 text-emerald-300 border border-white/10">
                            Divisi Gudang
                        </span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight font-display">
                        Riwayat Pengambilan Sepatu
                    </h1>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('storage.pickup-history.print') }}?search={{ urlencode($search) }}&startDate={{ urlencode($startDate) }}&endDate={{ urlencode($endDate) }}&sort={{ urlencode($sort) }}&pickup_method_filter={{ urlencode($pickup_method_filter) }}" 
                   target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs backdrop-blur-md border border-white/15 transition-all active:scale-95 shadow-sm">
                    <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Laporan</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8">
        
        {{-- ══════════════════════════════════════════════════════════════ --}}
        {{-- STATS CARDS (Refined Modern Glassmorphism & High Contrast)    --}}
        {{-- ══════════════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            
            {{-- Card 1: Hari Ini --}}
            <div class="relative overflow-hidden rounded-3xl p-6 bg-gradient-to-br from-emerald-600 to-teal-800 text-white shadow-lg shadow-emerald-900/20 border border-emerald-400/20 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl group">
                <div class="absolute -right-6 -bottom-6 w-28 h-28 bg-white/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500 pointer-events-none"></div>
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 rounded-xl bg-white/15 backdrop-blur-md flex items-center justify-center border border-white/20 text-white shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full bg-emerald-400/20 text-emerald-100 border border-emerald-300/30">
                        Hari Ini
                    </span>
                </div>
                <div class="relative z-10">
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-black tracking-tight leading-none">{{ number_format($stats['today']) }}</span>
                        <span class="text-xs font-bold text-emerald-200 uppercase tracking-wider">Pasang Sepatu</span>
                    </div>
                    <p class="text-[11px] text-emerald-100/70 font-medium mt-2">Diambil oleh customer hari ini</p>
                </div>
            </div>

            {{-- Card 2: Minggu Ini --}}
            <div class="relative overflow-hidden rounded-3xl p-6 bg-gradient-to-br from-indigo-600 to-blue-800 text-white shadow-lg shadow-indigo-900/20 border border-indigo-400/20 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl group">
                <div class="absolute -right-6 -bottom-6 w-28 h-28 bg-white/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500 pointer-events-none"></div>
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 rounded-xl bg-white/15 backdrop-blur-md flex items-center justify-center border border-white/20 text-white shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full bg-indigo-400/20 text-indigo-100 border border-indigo-300/30">
                        Minggu Ini
                    </span>
                </div>
                <div class="relative z-10">
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-black tracking-tight leading-none">{{ number_format($stats['week']) }}</span>
                        <span class="text-xs font-bold text-indigo-200 uppercase tracking-wider">Pasang Sepatu</span>
                    </div>
                    <p class="text-[11px] text-indigo-100/70 font-medium mt-2">Akumulasi pekan berjalan</p>
                </div>
            </div>

            {{-- Card 3: Bulan Ini --}}
            <div class="relative overflow-hidden rounded-3xl p-6 bg-gradient-to-br from-violet-600 to-purple-800 text-white shadow-lg shadow-purple-900/20 border border-violet-400/20 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl group">
                <div class="absolute -right-6 -bottom-6 w-28 h-28 bg-white/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500 pointer-events-none"></div>
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 rounded-xl bg-white/15 backdrop-blur-md flex items-center justify-center border border-white/20 text-white shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full bg-violet-400/20 text-violet-100 border border-violet-300/30">
                        Bulan Ini
                    </span>
                </div>
                <div class="relative z-10">
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-black tracking-tight leading-none">{{ number_format($stats['month']) }}</span>
                        <span class="text-xs font-bold text-violet-200 uppercase tracking-wider">Pasang Sepatu</span>
                    </div>
                    <p class="text-[11px] text-violet-100/70 font-medium mt-2">Volume selesai & diambil bulan ini</p>
                </div>
            </div>

            {{-- Card 4: Total Keseluruhan --}}
            <div class="relative overflow-hidden rounded-3xl p-6 bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-md border border-gray-100 dark:border-gray-700/80 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl group">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-700 dark:text-gray-200 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                        Semua Waktu
                    </span>
                </div>
                <div class="relative z-10">
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-black tracking-tight leading-none text-gray-900 dark:text-white">{{ number_format($stats['total']) }}</span>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Order</span>
                    </div>
                    <p class="text-[11px] text-gray-400 font-medium mt-2">Riwayat pengambilan keseluruhan</p>
                </div>
            </div>

        </div>

        {{-- ══════════════════════════════════════════════════════════════ --}}
        {{-- FILTER & SEARCH BAR (Unified Glass Container)                  --}}
        {{-- ══════════════════════════════════════════════════════════════ --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-xl border border-gray-100 dark:border-gray-700/80 transition-all">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-end">
                
                {{-- Search Input --}}
                <div class="lg:col-span-5">
                    <label class="block text-[10px] font-black text-gray-400 dark:text-gray-400 uppercase tracking-widest mb-2 px-1">
                        Pencarian SPK / Customer / Merk
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" 
                               wire:model.live.debounce.300ms="search" 
                               class="w-full pl-11 pr-4 py-3 bg-gray-50 dark:bg-gray-900/80 border border-gray-200/80 dark:border-gray-700 rounded-2xl text-sm font-semibold text-gray-800 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all outline-none" 
                               placeholder="Cari nomor SPK, nama customer, atau merk sepatu...">
                    </div>
                </div>

                {{-- Date Range (Flatpickr) --}}
                <div class="lg:col-span-3" wire:ignore x-data="{
                    initFlatpickr() {
                        flatpickr($refs.datePicker, {
                            mode: 'range',
                            dateFormat: 'Y-m-d',
                            defaultDate: [@js($startDate), @js($endDate)],
                            locale: {
                                rangeSeparator: ' s/d '
                            },
                            onChange: (selectedDates, dateStr, instance) => {
                                if (selectedDates.length === 2) {
                                    let start = instance.formatDate(selectedDates[0], 'Y-m-d');
                                    let end = instance.formatDate(selectedDates[1], 'Y-m-d');
                                    $wire.set('startDate', start);
                                    $wire.set('endDate', end);
                                } else if (selectedDates.length === 0) {
                                    $wire.set('startDate', '');
                                    $wire.set('endDate', '');
                                }
                            }
                        });

                        $watch('$wire.startDate', (value) => {
                            if ($refs.datePicker._flatpickr) {
                                if (!value) {
                                    $refs.datePicker._flatpickr.clear();
                                } else {
                                    $refs.datePicker._flatpickr.setDate([value, $wire.endDate], false);
                                }
                            }
                        });
                        $watch('$wire.endDate', (value) => {
                            if ($refs.datePicker._flatpickr && value) {
                                $refs.datePicker._flatpickr.setDate([$wire.startDate, value], false);
                            }
                        });
                    }
                }" x-init="initFlatpickr()">
                    <label class="block text-[10px] font-black text-gray-400 dark:text-gray-400 uppercase tracking-widest mb-2 px-1">
                        Rentang Waktu Ambil
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <input x-ref="datePicker" type="text" readonly 
                               class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-900/80 border border-gray-200/80 dark:border-gray-700 rounded-2xl text-xs font-bold text-gray-800 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all cursor-pointer text-center outline-none"
                               placeholder="Pilih Rentang Tanggal...">
                    </div>
                </div>

                {{-- Pickup Method Filter --}}
                <div class="lg:col-span-2">
                    <label class="block text-[10px] font-black text-gray-400 dark:text-gray-400 uppercase tracking-widest mb-2 px-1">
                        Metode Ambil
                    </label>
                    <div class="relative">
                        <select wire:model.live="pickup_method_filter" 
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900/80 border border-gray-200/80 dark:border-gray-700 rounded-2xl text-xs font-bold text-gray-800 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all cursor-pointer outline-none">
                            <option value="">Semua Metode</option>
                            <option value="offline">Direct / Toko</option>
                            <option value="delivery">Kurir / Ekspedisi</option>
                        </select>
                    </div>
                </div>

                {{-- Actions: Reset & Print --}}
                <div class="lg:col-span-2 flex items-center gap-2.5">
                    <button wire:click="resetFilters" 
                            title="Reset Semua Filter"
                            class="flex-1 py-3 px-4 bg-gray-100 dark:bg-gray-700/80 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-200 rounded-2xl transition-all duration-200 font-bold text-xs flex items-center justify-center gap-1.5 active:scale-95 border border-transparent">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Reset</span>
                    </button>
                    
                    <a href="{{ route('storage.pickup-history.print') }}?search={{ urlencode($search) }}&startDate={{ urlencode($startDate) }}&endDate={{ urlencode($endDate) }}&sort={{ urlencode($sort) }}&pickup_method_filter={{ urlencode($pickup_method_filter) }}" 
                       target="_blank"
                       title="Cetak Laporan Sesuai Filter"
                       class="py-3 px-4 bg-[#22B086] hover:bg-[#1fa17a] text-white rounded-2xl transition-all duration-200 font-extrabold text-xs flex items-center justify-center gap-1.5 shadow-md shadow-emerald-500/20 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak</span>
                    </a>
                </div>

            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════ --}}
        {{-- TABLE: RIWAYAT PENGAMBILAN                                     --}}
        {{-- ══════════════════════════════════════════════════════════════ --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700/80 overflow-hidden transition-all">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700/80">
                    <thead>
                        <tr class="bg-gray-50/80 dark:bg-gray-900/60">
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Data Sepatu</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Customer</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Waktu Ambil</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Metode Ambil</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Logistik & Margin</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Layanan</th>
                            <th class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/40">
                        @forelse($orders as $order)
                        <tr class="hover:bg-emerald-50/40 dark:hover:bg-emerald-950/20 transition-all duration-200 group">
                            
                            {{-- 1. Data Sepatu --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-700/60 flex items-center justify-center overflow-hidden border border-gray-200/80 dark:border-gray-600/60 group-hover:border-emerald-500 group-hover:shadow-md transition-all cursor-zoom-in shrink-0"
                                         @if($order->spk_cover_photo_url) 
                                            @click="previewUrl = '{{ $order->spk_cover_photo_url }}'; showPreview = true" 
                                            title="Klik untuk memperbesar foto"
                                         @endif>
                                        @if($order->spk_cover_photo_url)
                                            <img src="{{ $order->spk_cover_photo_url }}" 
                                                 class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500"
                                                 alt="SPK Cover Photo">
                                        @else
                                            <div class="text-gray-400 group-hover:text-emerald-500 transition-colors">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-xs font-black text-gray-900 dark:text-white font-mono tracking-tight bg-gray-100 dark:bg-gray-700/50 px-2 py-0.5 rounded-md inline-block border border-gray-200/50 dark:border-gray-600/50">
                                            {{ $order->spk_number }}
                                        </div>
                                        <div class="text-xs font-extrabold text-gray-800 dark:text-gray-200 mt-1 truncate">
                                            {{ $order->shoe_brand ?: 'Tanpa Merk' }}
                                        </div>
                                        <div class="text-[11px] text-gray-400 truncate">
                                            {{ $order->shoe_type ?: '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- 2. Customer --}}
                            <td class="px-6 py-4">
                                <div class="min-w-0">
                                    <div class="text-xs font-black text-gray-900 dark:text-white truncate">
                                        {{ $order->customer_name }}
                                    </div>
                                    <div class="text-[11px] font-semibold text-gray-400 dark:text-gray-400 mt-0.5 flex items-center gap-1">
                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        <span>{{ $order->customer_phone ?: '-' }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- 3. Waktu Ambil --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="text-xs font-black text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ \Carbon\Carbon::parse($order->taken_date)->format('d M Y') }}
                                    </span>
                                    <span class="text-[10px] font-bold text-gray-400 ml-4.5 mt-0.5">
                                        Pukul {{ \Carbon\Carbon::parse($order->taken_date)->format('H:i') }} WIB
                                    </span>
                                </div>
                            </td>

                            {{-- 4. Metode Ambil --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $isOffline = str_contains(strtolower($order->pickup_method ?: ''), 'offline') || str_contains(strtolower($order->pickup_method ?: ''), 'toko') || empty($order->pickup_method);
                                @endphp
                                <div class="inline-flex items-center gap-2 group/method">
                                    <span class="px-2.5 py-1 rounded-xl text-xs font-extrabold flex items-center gap-1.5 border {{ $isOffline ? 'bg-amber-50 text-amber-800 dark:bg-amber-950/30 dark:text-amber-300 border-amber-200/80 dark:border-amber-800/40' : 'bg-blue-50 text-blue-700 dark:bg-blue-950/30 dark:text-blue-300 border-blue-200/80 dark:border-blue-800/40' }}">
                                        <span>{{ $isOffline ? '🏬' : '🚚' }}</span>
                                        <span>{{ $order->pickup_method ?: 'Offline / Toko' }}</span>
                                    </span>
                                    <button onclick="editPickupMethod({{ $order->id }}, '{{ addslashes($order->pickup_method) }}')" 
                                            class="p-1 rounded-lg text-gray-300 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-all opacity-0 group-hover:opacity-100"
                                            title="Ubah Metode Pengambilan">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </button>
                                </div>
                            </td>

                            {{-- 5. Logistik & Margin --}}
                            <td class="px-6 py-4">
                                @php
                                    $custOngkir = $order->invoice ? $order->invoice->shipping_cost : $order->shipping_cost;
                                    $realOngkir = $order->actual_shipping_cost ?? 0;
                                    $margin = $custOngkir - $realOngkir;
                                @endphp
                                <div class="p-2.5 rounded-2xl bg-gray-50/80 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700/60 flex flex-col gap-1.5 w-44">
                                    <div class="flex justify-between items-center text-[10px]">
                                        <span class="text-gray-400 font-bold uppercase tracking-wider">Cust:</span>
                                        <span class="font-extrabold text-gray-700 dark:text-gray-200">Rp {{ number_format($custOngkir, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-[10px] group/edit-real">
                                        <span class="text-gray-400 font-bold uppercase tracking-wider">Real:</span>
                                        <div class="flex items-center gap-1">
                                            <span class="font-black text-indigo-600 dark:text-indigo-400">Rp {{ number_format($realOngkir, 0, ',', '.') }}</span>
                                            <button onclick="editActualOngkir({{ $order->id }}, '{{ addslashes($order->pickup_method) }}', {{ $realOngkir }})" 
                                                    class="opacity-0 group-hover/edit-real:opacity-100 transition-opacity text-gray-400 hover:text-indigo-500 p-0.5"
                                                    title="Edit Ongkir Real">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="pt-1.5 border-t border-gray-200/50 dark:border-gray-700/60 flex justify-between items-center text-[10px]">
                                        <span class="text-gray-400 font-bold uppercase tracking-wider">Margin:</span>
                                        <span class="px-1.5 py-0.5 rounded-md font-black {{ $margin >= 0 ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/40' : 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300 border border-rose-200 dark:border-rose-800/40' }}">
                                            {{ $margin >= 0 ? '+' : '' }}Rp {{ number_format($margin, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            {{-- 6. Layanan --}}
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1.5 max-w-xs">
                                    @forelse($order->workOrderServices as $svc)
                                        <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-700/50 text-slate-700 dark:text-slate-200 rounded-lg text-[10px] font-extrabold border border-slate-200/60 dark:border-slate-600/50 shadow-2xs">
                                            {{ $svc->service->name ?? $svc->custom_service_name }}
                                        </span>
                                    @empty
                                        <span class="text-gray-400 text-xs italic">-</span>
                                    @endforelse
                                </div>
                            </td>

                            {{-- 7. Aksi --}}
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    <button wire:click="undoPickup({{ $order->id }})" 
                                            wire:confirm="Apakah Anda yakin ingin membatalkan pengambilan ini? Sepatu akan dikembalikan ke status 'Menunggu Disimpan' di Gudang Finish."
                                            class="p-2.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/30 rounded-xl transition-all duration-200 active:scale-95"
                                            title="Batalkan Pengambilan (Kembalikan ke Gudang Finish)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                    </button>
                                    <a href="{{ route('admin.orders.show', $order->id) }}" 
                                       class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-gray-50 hover:bg-emerald-50 dark:bg-gray-700/60 dark:hover:bg-emerald-950/30 border border-gray-200 dark:border-gray-600/80 rounded-xl text-xs font-black text-gray-700 hover:text-emerald-700 dark:text-gray-200 dark:hover:text-emerald-300 transition-all duration-200 shadow-2xs active:scale-95">
                                        <span>Detail</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                    <div class="w-20 h-20 rounded-3xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400 dark:text-gray-500 mb-4 shadow-inner">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                    </div>
                                    <h3 class="text-base font-black text-gray-800 dark:text-gray-200">Tidak ada riwayat pengambilan</h3>
                                    <p class="text-xs text-gray-400 mt-1">Coba sesuaikan filter rentang tanggal, metode, atau kata kunci pencarian Anda.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($orders->hasPages())
                <div class="px-6 py-4 bg-gray-50/80 dark:bg-gray-900/60 border-t border-gray-100 dark:border-gray-700/80">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>

    </div>

    {{-- Image Preview Modal --}}
    <div x-show="showPreview" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/90 backdrop-blur-md"
         x-cloak
         @keydown.escape.window="showPreview = false">
        
        <button @click="showPreview = false" class="absolute top-6 right-6 w-11 h-11 rounded-2xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors border border-white/10">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <img :src="previewUrl" 
             @click.away="showPreview = false"
             class="max-w-full max-h-[88vh] rounded-3xl shadow-2xl border-2 border-white/20 object-contain"
             alt="Enlarged Shoe Preview">
    </div>

    <script>
        function editPickupMethod(orderId, currentMethod) {
            Swal.fire({
                title: 'Edit Metode Pengambilan',
                input: 'text',
                inputLabel: 'Masukkan metode pengambilan baru:',
                inputValue: currentMethod || 'Offline',
                showCancelButton: true,
                confirmButtonColor: '#22B086',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Simpan Perubahan',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'rounded-xl font-bold px-5 py-2.5',
                    cancelButton: 'rounded-xl font-bold px-5 py-2.5',
                },
                inputValidator: (value) => {
                    if (!value) {
                        return 'Metode tidak boleh kosong!'
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('updatePickupMethod', orderId, result.value);
                }
            })
        }

        function editActualOngkir(orderId, currentMethod, currentCost) {
            Swal.fire({
                title: 'Edit Ongkir Real (Workshop)',
                html: `
                    <div class="text-left space-y-3 p-1">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-wider mb-1">Metode Pengiriman:</label>
                            <input id="swal-method" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-emerald-500 outline-none" value="${currentMethod}">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-wider mb-1">Ongkir Real Dibayar (Rp):</label>
                            <input id="swal-cost" type="number" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-emerald-500 outline-none" value="${currentCost}">
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonColor: '#22B086',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Simpan Ongkir',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'rounded-xl font-bold px-5 py-2.5',
                    cancelButton: 'rounded-xl font-bold px-5 py-2.5',
                },
                preConfirm: () => {
                    return [
                        document.getElementById('swal-method').value,
                        document.getElementById('swal-cost').value
                    ]
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('updatePickupMethod', orderId, result.value[0], result.value[1]);
                }
            })
        }

        window.addEventListener('swal', event => {
            const data = event.detail[0] || event.detail;
            Swal.fire({
                icon: data.icon,
                title: data.title,
                text: data.text,
                timer: 3000,
                showConfirmButton: false,
                customClass: {
                    popup: 'rounded-3xl',
                }
            });
        });
    </script>
</div>
