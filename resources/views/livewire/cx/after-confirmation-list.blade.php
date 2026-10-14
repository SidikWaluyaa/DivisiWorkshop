<div>
    {{-- Flatpickr Styles & Scripts --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        .flatpickr-calendar {
            background: #ffffff !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 1.25rem !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
            font-family: inherit !important;
            z-index: 99999 !important;
        }
        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange {
            background: #22AF85 !important;
            border-color: #22AF85 !important;
            color: #ffffff !important;
            font-weight: 800 !important;
            border-radius: 8px !important;
        }
        .flatpickr-day.inRange {
            background: rgba(34, 175, 133, 0.12) !important;
            border-color: transparent !important;
            box-shadow: -5px 0 0 rgba(34, 175, 133, 0.12), 5px 0 0 rgba(34, 175, 133, 0.12) !important;
        }
        .flatpickr-day:hover {
            background: rgba(34, 175, 133, 0.15) !important;
            border-radius: 8px !important;
        }
        .flatpickr-months .flatpickr-month {
            color: #1f2937 !important;
            font-weight: 800 !important;
        }
        .flatpickr-current-month .flatpickr-monthDropdown-months {
            font-weight: 800 !important;
        }
    </style>

    {{-- Header Quick Stat Chips (7 Cards: Total Keseluruhan, Reguler, Garansi, Revisi, OTO, R&D, After Ke-2+) --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-3 mb-6">
        {{-- Total (Keseluruhan) --}}
        <div wire:click="setStageQuickFilter('')" 
             class="bg-white rounded-2xl p-4 border transition-all cursor-pointer group shadow-sm hover:shadow {{ empty($stageFilter) && empty($roundFilter) ? 'border-gray-900 ring-2 ring-gray-900/10' : 'border-gray-100 hover:border-gray-300' }}">
            <span class="text-[9px] font-black uppercase text-gray-400 tracking-wider block mb-1">Total Konfirmasi</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-black text-gray-900 group-hover:text-emerald-600 transition-colors">{{ $stats['total'] ?? 0 }}</span>
                <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-gray-100 text-gray-700 uppercase">Semua</span>
            </div>
        </div>

        {{-- Reguler (Hijau) --}}
        <div wire:click="setStageQuickFilter('REGULER')" 
             class="bg-emerald-50/50 rounded-2xl p-4 border transition-all cursor-pointer group shadow-sm hover:shadow {{ $stageFilter === 'REGULER' ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-emerald-200/60 hover:border-emerald-400' }}">
            <span class="text-[9px] font-black uppercase text-emerald-600 tracking-wider block mb-1">Reguler</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-black text-emerald-700">{{ $stats['reguler'] ?? 0 }}</span>
                <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 uppercase">Hijau</span>
            </div>
        </div>

        {{-- Garansi (Kuning) --}}
        <div wire:click="setStageQuickFilter('GARANSI')" 
             class="bg-amber-50/50 rounded-2xl p-4 border transition-all cursor-pointer group shadow-sm hover:shadow {{ $stageFilter === 'GARANSI' ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-amber-200/60 hover:border-amber-400' }}">
            <span class="text-[9px] font-black uppercase text-amber-600 tracking-wider block mb-1">Garansi</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-black text-amber-700">{{ $stats['garansi'] ?? 0 }}</span>
                <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 uppercase">Kuning</span>
            </div>
        </div>

        {{-- Revisi (Merah) --}}
        <div wire:click="setStageQuickFilter('REVISI')" 
             class="bg-rose-50/50 rounded-2xl p-4 border transition-all cursor-pointer group shadow-sm hover:shadow {{ $stageFilter === 'REVISI' ? 'border-rose-500 ring-2 ring-rose-500/20' : 'border-rose-200/60 hover:border-rose-400' }}">
            <span class="text-[9px] font-black uppercase text-rose-600 tracking-wider block mb-1">Revisi</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-black text-rose-700">{{ $stats['revisi'] ?? 0 }}</span>
                <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-rose-100 text-rose-800 uppercase">Merah</span>
            </div>
        </div>

        {{-- OTO (Orange) --}}
        <div wire:click="setStageQuickFilter('OTO')" 
             class="bg-orange-50/50 rounded-2xl p-4 border transition-all cursor-pointer group shadow-sm hover:shadow {{ $stageFilter === 'OTO' ? 'border-orange-500 ring-2 ring-orange-500/20' : 'border-orange-200/60 hover:border-orange-400' }}">
            <span class="text-[9px] font-black uppercase text-orange-600 tracking-wider block mb-1">OTO</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-black text-orange-700">{{ $stats['oto'] ?? 0 }}</span>
                <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-orange-100 text-orange-800 uppercase">Orange</span>
            </div>
        </div>

        {{-- R&D (Biru) --}}
        <div wire:click="setStageQuickFilter('RND')" 
             class="bg-blue-50/50 rounded-2xl p-4 border transition-all cursor-pointer group shadow-sm hover:shadow {{ $stageFilter === 'RND' ? 'border-blue-500 ring-2 ring-blue-500/20' : 'border-blue-200/60 hover:border-blue-400' }}">
            <span class="text-[9px] font-black uppercase text-blue-600 tracking-wider block mb-1">R&D</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-black text-blue-700">{{ $stats['rnd'] ?? 0 }}</span>
                <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-blue-100 text-blue-800 uppercase">Biru</span>
            </div>
        </div>

        {{-- After Ke-2+ (Ungu) --}}
        <div wire:click="setRoundQuickFilter('2+')" 
             class="bg-purple-50/50 rounded-2xl p-4 border transition-all cursor-pointer group shadow-sm hover:shadow {{ in_array($roundFilter, ['2', '2+']) ? 'border-purple-500 ring-2 ring-purple-500/20' : 'border-purple-200/60 hover:border-purple-400' }}">
            <span class="text-[9px] font-black uppercase text-purple-600 tracking-wider block mb-1">After Ke-2+</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-black text-purple-700">{{ $stats['after2'] ?? 0 }}</span>
                <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-purple-100 text-purple-800 uppercase">Lanjutan</span>
            </div>
        </div>
    </div>
    
    {{-- Premium Filters --}}
    <div class="relative bg-white backdrop-blur-2xl rounded-[2rem] border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-6 mb-8 group overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-[#22AF85]/5 to-[#22AF85]/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
        <div class="relative grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5">
            {{-- Search Bar (3 cols on lg) --}}
            <div class="sm:col-span-2 lg:col-span-3 relative">
                <label class="text-[9px] font-black text-gray-400/80 uppercase tracking-[0.2em] ml-2 mb-2 block transition-colors group-hover:text-[#22AF85]">Pencarian</label>
                <div class="relative flex items-center">
                    <div class="absolute left-4 z-10 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search" 
                        class="w-full bg-gray-50/50 backdrop-blur-sm border border-gray-100 placeholder:text-gray-400 rounded-2xl pl-11 pr-10 py-3.5 text-sm focus:bg-white focus:ring-4 focus:ring-[#22AF85]/10 focus:border-[#22AF85]/40 transition-all font-bold text-gray-800"
                        placeholder="SPK / Nama / No. Telp...">
                    <div wire:loading wire:target="search" class="absolute right-4">
                        <span class="flex h-3 w-3 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#22AF85] opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-[#22AF85]"></span>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Tipe SPK (Garansi/Revisi/OTO/RND/Reguler) (2 cols on lg) --}}
            <div class="lg:col-span-2">
                <label class="text-[9px] font-black text-gray-400/80 uppercase tracking-[0.2em] ml-2 mb-2 block transition-colors group-hover:text-[#22AF85]">Tipe SPK</label>
                <div class="relative">
                    <select wire:model.live="stageFilter" class="w-full appearance-none bg-gray-50/50 backdrop-blur-sm border border-gray-100 rounded-2xl px-4 py-3.5 text-sm focus:bg-white focus:ring-4 focus:ring-[#22AF85]/10 focus:border-[#22AF85]/40 transition-all font-bold text-gray-800 cursor-pointer">
                        <option value="">Semua Tipe</option>
                        <option value="GARANSI">🟡 Garansi</option>
                        <option value="REVISI">🔴 Revisi</option>
                        <option value="OTO">🟠 OTO</option>
                        <option value="RND">🔵 R&D</option>
                        <option value="REGULER">🟢 Reguler</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>

            {{-- Siklus After (After 1 / After 2 / After 3) (2 cols on lg) --}}
            <div class="lg:col-span-2">
                <label class="text-[9px] font-black text-gray-400/80 uppercase tracking-[0.2em] ml-2 mb-2 block transition-colors group-hover:text-[#22AF85]">Siklus After</label>
                <div class="relative">
                    <select wire:model.live="roundFilter" class="w-full appearance-none bg-gray-50/50 backdrop-blur-sm border border-gray-100 rounded-2xl px-4 py-3.5 text-sm focus:bg-white focus:ring-4 focus:ring-[#22AF85]/10 focus:border-[#22AF85]/40 transition-all font-bold text-gray-800 cursor-pointer">
                        <option value="">Semua Siklus</option>
                        <option value="1">After 1 (Awal)</option>
                        <option value="2">After 2 (Revisi/Pasca)</option>
                        <option value="3">After 3 (OTO 2x/Lanjutan)</option>
                        <option value="2+">After 2+ (Semua Lanjutan)</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>

            {{-- Status Pengambilan (Taken / Not Taken) (2 cols on lg) --}}
            <div class="lg:col-span-2">
                <label class="text-[9px] font-black text-gray-400/80 uppercase tracking-[0.2em] ml-2 mb-2 block transition-colors group-hover:text-[#22AF85]">Status Pengambilan</label>
                <div class="relative">
                    <select wire:model.live="pickupFilter" class="w-full appearance-none bg-gray-50/50 backdrop-blur-sm border border-gray-100 rounded-2xl px-4 py-3.5 text-sm focus:bg-white focus:ring-4 focus:ring-[#22AF85]/10 focus:border-[#22AF85]/40 transition-all font-bold text-gray-800 cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="not_taken">🏠 Belum Diambil</option>
                        <option value="taken">📦 Sudah Diambil</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>

            {{-- Response Dropdown (2 cols on lg) --}}
            <div class="lg:col-span-2">
                <label class="text-[9px] font-black text-gray-400/80 uppercase tracking-[0.2em] ml-2 mb-2 block transition-colors group-hover:text-[#22AF85]">Status Respon</label>
                <div class="relative">
                    <select wire:model.live="response" class="w-full appearance-none bg-gray-50/50 backdrop-blur-sm border border-gray-100 rounded-2xl px-4 py-3.5 text-sm focus:bg-white focus:ring-4 focus:ring-[#22AF85]/10 focus:border-[#22AF85]/40 transition-all font-bold text-gray-800 cursor-pointer">
                        <option value="">Semua Respon</option>
                        @foreach(['Puas', 'Komplain', 'Kurang Puas', 'No Respon 1x24 Jam', 'Hold'] as $res)
                            <option value="{{ $res }}">{{ $res }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>

            {{-- Reset Button (1 col on lg) --}}
            <div class="lg:col-span-1 flex items-end text-right h-full">
                <button wire:click="resetFilters" class="w-full h-[52px] bg-white border border-gray-100 hover:border-[#FFC232]/50 hover:bg-[#FFC232]/5 hover:text-[#FFC232] text-gray-500 font-black rounded-2xl transition-all duration-300 flex items-center justify-center gap-1.5 text-[10px] uppercase tracking-[0.2em] shadow-sm hover:shadow active:scale-95" title="Reset Filter">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    <span class="lg:hidden xl:inline">RESET</span>
                </button>
            </div>
        </div>

        {{-- Date Range sub-row (Flatpickr Range Picker) --}}
        <div class="mt-4 pt-4 border-t border-gray-100/80 flex flex-col sm:flex-row items-center justify-between gap-4"
             wire:ignore
             x-data="{
                fp: null,
                initFlatpickr() {
                    this.fp = flatpickr(this.$refs.datePicker, {
                        mode: 'range',
                        dateFormat: 'Y-m-d',
                        altInput: true,
                        altFormat: 'd M Y',
                        altInputClass: 'w-full bg-gray-50/70 hover:bg-white focus:bg-white border border-gray-200/80 hover:border-[#22AF85]/50 focus:border-[#22AF85] rounded-xl px-4 py-2.5 text-xs font-bold text-gray-800 outline-none cursor-pointer shadow-sm text-center min-w-[260px] transition-all',
                        defaultDate: [@js($startDate), @js($endDate)],
                        position: 'auto right',
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

                    this.$watch('$wire.startDate', (val) => {
                        if (this.fp) {
                            if (!val) {
                                this.fp.clear();
                            } else if ($wire.endDate) {
                                this.fp.setDate([val, $wire.endDate], false);
                            }
                        }
                    });
                    this.$watch('$wire.endDate', (val) => {
                        if (this.fp && val && $wire.startDate) {
                            this.fp.setDate([$wire.startDate, val], false);
                        }
                    });
                }
             }"
             x-init="initFlatpickr()">
            <div class="flex items-center gap-2 text-xs font-bold text-gray-400">
                <svg class="w-4 h-4 text-[#22AF85]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span class="text-gray-600 font-extrabold">Filter Rentang Tanggal:</span>
                @if($startDate && $endDate)
                    <span class="text-[9px] font-black px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase tracking-wider">
                        Aktif
                    </span>
                @endif
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <div class="relative flex-1 sm:flex-initial">
                    <input x-ref="datePicker" type="text" readonly
                           placeholder="Pilih Rentang Tanggal...">
                </div>

                @if($startDate || $endDate)
                <button type="button" 
                        wire:click="$set('startDate', ''); $set('endDate', '');"
                        @click="if(fp) { fp.clear(); }"
                        class="px-3 py-2.5 text-[10px] font-black uppercase tracking-wider text-rose-600 hover:text-white bg-rose-50 hover:bg-rose-500 rounded-xl border border-rose-200 transition-all flex items-center gap-1 active:scale-95 shadow-sm"
                        title="Hapus Filter Tanggal">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    <span>Reset</span>
                </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Premium Data Table --}}
    <div class="bg-white backdrop-blur-2xl rounded-[2.5rem] border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden relative transition-all duration-300">
        {{-- Custom Loading Overlay --}}
        <div wire:loading.delay class="absolute inset-0 bg-white/60 backdrop-blur-md z-20 flex items-center justify-center rounded-[2.5rem]">
             <div class="flex flex-col items-center gap-4 bg-white px-8 py-6 rounded-3xl shadow-xl border border-gray-100">
                <svg class="animate-spin h-10 w-10 text-[#22AF85]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                    <path class="opacity-100" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-xs font-black text-gray-800 uppercase tracking-[0.2em]">Memuat Data...</span>
             </div>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100/80">
                        <th class="px-8 py-5 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] w-40">Waktu Masuk</th>
                        <th class="px-6 py-5 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Detail SPK & Customer</th>
                        <th class="px-6 py-5 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">PIC & Waktu Kontak</th>
                        <th class="px-6 py-5 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Respon Kepuasan</th>
                        <th class="px-8 py-5 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($items as $item)
                    @php
                        $stage = $item->stage_badge;
                        $round = $item->round_badge;
                    @endphp
                    <tr class="hover:bg-gray-50/80 active:bg-gray-100 transition-colors duration-200 group relative">
                        {{-- Data: Date with Hover indicator rail --}}
                        <td class="px-8 py-5 align-top relative">
                            <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $stage['dot'] }} opacity-0 group-hover:opacity-100 transition-opacity rounded-r-full"></div>
                            
                            <div class="flex flex-col gap-1">
                                <span class="text-xs font-black text-gray-800">{{ $item->entered_at->format('d M Y') }}</span>
                                <span class="text-[10px] text-gray-400 font-black uppercase tracking-wider">{{ $item->entered_at->format('H:i') }} WIB</span>
                            </div>
                        </td>
                        
                        {{-- Data: Info SPK & Customer --}}
                        <td class="px-6 py-5 align-top">
                            <div class="flex flex-col gap-2">
                                {{-- Baris 1: Badge SPK & Badge Tipe & Badge After --}}
                                <div class="flex flex-wrap items-center gap-1.5">
                                    {{-- SPK Number beraksen warna sesuai tipe --}}
                                    <span class="text-xs font-black px-2.5 py-1 rounded-xl border uppercase tracking-tight {{ $stage['spk_bg'] }}">
                                        {{ $item->workOrder->spk_number }}
                                    </span>

                                    {{-- Badge Tipe: Kuning (Garansi), Merah (Revisi), Orange (OTO), Biru (R&D) --}}
                                    @if(($item->stage_type ?? 'REGULER') !== 'REGULER')
                                    <span class="inline-flex items-center gap-1 text-[9px] font-black px-2 py-0.5 rounded-lg border uppercase tracking-wider {{ $stage['bg'] }} {{ $stage['text'] }} {{ $stage['border'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $stage['dot'] }}"></span>
                                        {{ $stage['label'] }}
                                    </span>
                                    @endif

                                    {{-- Badge Siklus: Dinamis (After 1, After 2, After 3, dst.) --}}
                                    @php
                                        $roundBadge = $item->round_badge;
                                    @endphp
                                    <span class="inline-flex items-center gap-1 text-[9px] font-black px-2 py-0.5 rounded-lg uppercase tracking-wider {{ $roundBadge['badge_class'] }}">
                                        @if((int)$item->round >= 2)
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-300 animate-pulse"></span>
                                        @else
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        @endif
                                        {{ $roundBadge['label'] }}
                                    </span>
                                </div>

                                <div>
                                    <span class="text-sm font-black text-gray-900 block leading-tight">{{ $item->workOrder->customer_name }}</span>
                                    <div class="text-xs font-bold text-gray-500 flex items-center gap-1.5 select-all mt-1">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                        {{ $item->workOrder->customer_phone }}
                                    </div>
                                    <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $item->workOrder->shoe_brand }} - {{ $item->workOrder->shoe_color }}</span>
                                        @if($item->workOrder->taken_date)
                                            <span class="inline-flex items-center gap-1 text-[9px] font-black px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200/80 uppercase tracking-wider" title="Diambil pada {{ $item->workOrder->taken_date->format('d M Y H:i') }}">
                                                📦 Sudah Diambil ({{ $item->workOrder->taken_date->format('d M Y') }})
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[9px] font-black px-2 py-0.5 rounded-lg bg-amber-50 text-amber-700 border border-amber-200/80 uppercase tracking-wider">
                                                🏠 Belum Diambil
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        
                        {{-- Data: PIC --}}
                        <td class="px-6 py-5 align-top">
                            @if($item->pic)
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-xl bg-[#22AF85] flex items-center justify-center shadow-lg shadow-[#22AF85]/20 text-white font-black text-xs">
                                    {{ substr($item->pic->name, 0, 1) }}
                                </div>
                                <div class="flex flex-col gap-0.5">
                                    <span class="text-xs font-black text-gray-800 leading-tight uppercase">{{ $item->pic->name }}</span>
                                    <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">{{ $item->contacted_at?->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                            @else
                            <div class="inline-flex items-center gap-2 px-2.5 py-1.5 bg-gray-50 border border-gray-100 rounded-xl relative group cursor-help text-[9px] font-black text-gray-400 uppercase tracking-widest">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                                Belum Dikontak
                            </div>
                            @endif
                        </td>
                        
                        {{-- Data: Response Status --}}
                        <td class="px-6 py-5 align-top">
                            @php
                                $colorMap = [
                                    'Puas' => ['bg' => 'bg-[#22AF85]/10', 'text' => 'text-[#22AF85]', 'border' => 'border-[#22AF85]/20', 'dot' => 'bg-[#22AF85]'],
                                    'Komplain' => ['bg' => 'bg-[#FFC232]', 'text' => 'text-gray-900', 'border' => 'border-[#FFC232]', 'dot' => 'bg-white'],
                                    'Kurang Puas' => ['bg' => 'bg-[#FFC232]', 'text' => 'text-gray-900', 'border' => 'border-[#FFC232]', 'dot' => 'bg-white'],
                                    'No Respon 1x24 Jam' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'border' => 'border-gray-200', 'dot' => 'bg-gray-400'],
                                    'Hold' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'border' => 'border-gray-200', 'dot' => 'bg-gray-400'],
                                ];
                                $style = $colorMap[$item->response] ?? null;
                            @endphp
                            
                            @if($style)
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl {{ $style['bg'] }} {{ $style['border'] }} border">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $style['dot'] }}"></span>
                                    <span class="text-[10px] font-black uppercase tracking-widest {{ $style['text'] }}">
                                        {{ $item->response }}
                                    </span>
                                </div>
                            @else
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-50 border border-dashed border-gray-200 rounded-xl">
                                    <span class="flex h-1.5 w-1.5 relative">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#FFC232] opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-[#FFC232]"></span>
                                    </span>
                                    <span class="text-[10px] font-black text-gray-600 uppercase tracking-widest">
                                        Menunggu Respon
                                    </span>
                                </div>
                            @endif
                        </td>
                        
                        {{-- Data: Action (Single Unified Button) --}}
                        <td class="px-6 py-5 align-top text-right">
                            <div class="flex items-center justify-end">
                                <button type="button" 
                                        wire:click="openActionModal({{ $item->id }})" 
                                        title="Tindakan Konfirmasi Pelanggan"
                                        class="px-4 py-2 bg-white hover:bg-[#22AF85] text-slate-700 hover:text-white border border-slate-200 hover:border-[#22AF85] rounded-xl text-xs font-bold transition-all shadow-2xs hover:shadow-md active:scale-95 whitespace-nowrap">
                                    Tindakan
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-24 text-center border-none">
                            <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                <div class="w-24 h-24 mb-6 rounded-full bg-gray-50 flex items-center justify-center border border-gray-100 shadow-inner">
                                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.675.337a6 6 0 01-3.86.517l-2.388-.477a2 2 0 00-1.023.547l-1.318 1.319a2 2 0 00-.566 1.104l-1.49 10.161a2 2 0 00.358 1.581l4.411 5.415a2 2 0 003.088 0l4.411-5.415a2 2 0 00.358-1.581l-1.49-10.161a2 2 0 00-.566-1.104l-1.317-1.319z"></path></svg>
                                </div>
                                <h3 class="text-lg font-black text-gray-800 uppercase tracking-tight mb-2">Tidak Ada Data Konfirmasi</h3>
                                <p class="text-xs font-medium text-gray-500 leading-relaxed">Belum ada pelanggan yang siap dikonfirmasi dalam rentang waktu atau filter ini. Coba sesuaikan filter pencarian.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-center justify-between gap-4">
            {{-- Per Page Dropdown --}}
            <div class="flex items-center gap-2 text-xs font-bold text-gray-500">
                <span>Tampilkan</span>
                <div class="relative">
                    <select wire:model.live="perPage" class="appearance-none bg-white border border-gray-200 rounded-xl pl-3 pr-8 py-1.5 text-xs font-black text-gray-700 hover:border-[#22AF85]/50 focus:ring-2 focus:ring-[#22AF85]/20 focus:border-[#22AF85] shadow-sm cursor-pointer outline-none transition-all">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
                <span>data per halaman</span>
            </div>

            {{-- Pagination Links --}}
            @if($items->hasPages())
            <div>
                {{ $items->links() }}
            </div>
            @endif
    {{-- Unified Action Modal (Respon + Accordion OTO / Revisi) --}}
    <div x-data="{
            openAction: false,
            workOrder: null,
            confirmation: null,
            actionType: @entangle('selectedActionType'),
            
            // OTO Local State
            searchOto: '',
            selectedServices: {},
            otoDescription: '',
            validDays: 5,
            sendAutomation: true,
            services: @js($availableOtoServices),

            init() {
                this.$wire.on('open-unified-action-modal', (event) => {
                    let data = event[0] || {};
                    this.workOrder = data.workOrder || null;
                    this.confirmation = data.confirmation || null;
                    this.searchOto = '';
                    this.selectedServices = {};
                    this.otoDescription = '';
                    this.validDays = 5;
                    this.sendAutomation = true;
                    this.openAction = true;
                });
                this.$wire.on('close-unified-action-modal', () => {
                    this.openAction = false;
                });
            },

            matchesSearch(name) {
                if (!this.searchOto.trim()) return true;
                return name.toLowerCase().includes(this.searchOto.toLowerCase());
            },

            toggleService(service) {
                if (this.selectedServices[service.id]) {
                    delete this.selectedServices[service.id];
                } else {
                    let basePrice = parseFloat(service.price) || 0;
                    this.selectedServices[service.id] = {
                        id: service.id,
                        name: service.name,
                        oto_price: basePrice,
                        normal_price: basePrice,
                        hk_days: parseInt(service.hk_days) || 0
                    };
                }
            },

            isSelected(id) {
                return !!this.selectedServices[id];
            },

            get hasMatches() {
                if (!this.searchOto.trim()) return true;
                return this.services.some(s => this.matchesSearch(s.name));
            },

            get totalNormal() {
                return Object.values(this.selectedServices).reduce((sum, s) => sum + (parseFloat(s.normal_price) || 0), 0);
            },

            get totalOto() {
                return Object.values(this.selectedServices).reduce((sum, s) => sum + (parseFloat(s.oto_price) || 0), 0);
            },

            get validUntilText() {
                let d = new Date();
                d.setDate(d.getDate() + parseInt(this.validDays || 5));
                return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
            },

            formatRupiah(val) {
                return 'Rp ' + Number(val || 0).toLocaleString('id-ID');
            },

            submit() {
                if (this.actionType === 'oto') {
                    let servicesList = Object.values(this.selectedServices);
                    if (servicesList.length === 0) {
                        alert('Pilih minimal satu layanan OTO pada accordion OTO.');
                        return;
                    }
                    if (!this.otoDescription.trim()) {
                        alert('Alasan penawaran OTO wajib diisi pada accordion OTO.');
                        return;
                    }
                    $wire.saveAction({
                        services: servicesList,
                        description: this.otoDescription,
                        valid_days: this.validDays,
                        send_automation: this.sendAutomation
                    });
                } else {
                    $wire.saveAction();
                }
            }
         }"
         x-show="openAction"
         class="fixed inset-0 z-[100] flex items-center justify-center p-3 sm:p-4 bg-black/70 backdrop-blur-sm"
         style="display: none;"
         x-cloak>
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-3xl max-h-[92vh] overflow-hidden flex flex-col border border-gray-100" @click.away="openAction = false">
            {{-- Header --}}
            <div class="p-6 pb-4 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white relative">
                <button type="button" @click="openAction = false" class="absolute top-4 right-4 text-white/70 hover:text-white p-1.5 rounded-full hover:bg-white/10 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-md bg-[#22AF85]/20 text-[#22AF85] border border-[#22AF85]/30 text-[10px] font-black uppercase tracking-wider">
                        Form Terpadu
                    </span>
                    <h3 class="text-xl font-black tracking-tight text-white">Tindakan Konfirmasi Pelanggan</h3>
                </div>
                <p class="text-slate-400 text-xs mt-1">Catat respon customer serta sertakan penawaran OTO atau revisi workshop sekaligus.</p>

                {{-- SPK & Customer Meta Pill --}}
                <template x-if="workOrder">
                    <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
                        <span class="font-black px-2.5 py-1 bg-white/10 rounded-xl text-[#22AF85] border border-white/10" x-text="workOrder.spk_number"></span>
                        <span class="font-bold text-white" x-text="workOrder.customer_name"></span>
                        <span class="text-slate-400">•</span>
                        <span class="text-slate-300 font-medium" x-text="workOrder.customer_phone"></span>
                        <template x-if="workOrder.shoe_brand">
                            <span class="text-slate-400">• <span class="uppercase font-bold text-slate-200" x-text="workOrder.shoe_brand"></span></span>
                        </template>
                        <template x-if="workOrder.taken_date">
                            <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 rounded-lg text-[10px] font-bold">
                                📦 Sudah Diambil (<span x-text="workOrder.taken_date"></span>)
                            </span>
                        </template>
                        <template x-if="!workOrder.taken_date">
                            <span class="px-2 py-0.5 bg-amber-500/20 text-amber-300 border border-amber-500/30 rounded-lg text-[10px] font-bold">
                                🏠 Belum Diambil
                            </span>
                        </template>
                    </div>
                </template>
            </div>

            {{-- Body Scrollable --}}
            <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar bg-slate-50/40">
                {{-- SEKSI 1: FORM RESPON KEPUASAN (UTAMA) --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-2xs space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-teal-50 text-[#22AF85] font-black text-xs flex items-center justify-center">1</span>
                            <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider">Respon Kepuasan Pelanggan</h4>
                        </div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Langkah Utama</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Select Respon --}}
                        <div>
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-wider block mb-1.5">
                                Status Respon Customer <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <select wire:model="editingResponse" 
                                        class="w-full appearance-none bg-slate-50 hover:bg-white border border-slate-200 focus:border-[#22AF85] focus:ring-4 focus:ring-[#22AF85]/10 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-800 transition-all cursor-pointer">
                                    <option value="">— Pilih Respon Customer —</option>
                                    @foreach(['Puas', 'Komplain', 'Kurang Puas', 'No Respon 1x24 Jam', 'Hold'] as $res)
                                        <option value="{{ $res }}">{{ $res }}</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-400">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>

                        {{-- Catatan Ringkas --}}
                        <div>
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-wider block mb-1.5">
                                Catatan Hasil Kontak (Opsional)
                            </label>
                            <input type="text" wire:model="editingNotes" 
                                   class="w-full bg-slate-50 hover:bg-white border border-slate-200 focus:border-[#22AF85] focus:ring-4 focus:ring-[#22AF85]/10 rounded-xl px-4 py-2.5 text-xs font-medium text-slate-800 placeholder:text-slate-400 transition-all"
                                   placeholder="Contoh: Customer puas dengan hasil repaint...">
                        </div>
                    </div>
                </div>

                {{-- SEKSI 2: ACCORDION PILIHAN BEBAS TINDAK LANJUT (SALING EKSKLUSIF) --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-2xs space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-indigo-50 text-indigo-600 font-black text-xs flex items-center justify-center">2</span>
                            <div>
                                <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider">Tindak Lanjut Lanjutan (Saling Eksklusif)</h4>
                                <p class="text-[10px] text-slate-400 mt-0.5">Pilihan bebas: simpan respon saja, atau sertakan OTO / Revisi sekaligus.</p>
                            </div>
                        </div>
                    </div>

                    {{-- 3 Radio Segmented Cards --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        {{-- Opsi 1: Hanya Respon --}}
                        <label class="cursor-pointer">
                            <input type="radio" name="unified_action_type" value="none" x-model="actionType" class="sr-only">
                            <div class="p-3.5 rounded-xl border-2 transition-all flex flex-col justify-between h-full select-none"
                                 :class="actionType === 'none' ? 'border-[#22AF85] bg-emerald-50/40 shadow-xs' : 'border-slate-200 bg-slate-50/50 hover:border-slate-300'">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-base">📝</span>
                                    <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center"
                                         :class="actionType === 'none' ? 'border-[#22AF85] bg-[#22AF85]' : 'border-slate-300'">
                                        <div class="w-1.5 h-1.5 rounded-full bg-white" x-show="actionType === 'none'"></div>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-black text-slate-800">Hanya Catat Respon</p>
                                    <p class="text-[10px] text-slate-500 mt-0.5 leading-tight">Simpan hasil feedback tanpa aksi tambahan.</p>
                                </div>
                            </div>
                        </label>

                        {{-- Opsi 2: Sertakan OTO --}}
                        <label class="cursor-pointer">
                            <input type="radio" name="unified_action_type" value="oto" x-model="actionType" class="sr-only">
                            <div class="p-3.5 rounded-xl border-2 transition-all flex flex-col justify-between h-full select-none"
                                 :class="actionType === 'oto' ? 'border-amber-500 bg-amber-50/40 shadow-xs' : 'border-slate-200 bg-slate-50/50 hover:border-slate-300'">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-base">✨</span>
                                    <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center"
                                         :class="actionType === 'oto' ? 'border-amber-500 bg-amber-500' : 'border-slate-300'">
                                        <div class="w-1.5 h-1.5 rounded-full bg-white" x-show="actionType === 'oto'"></div>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-black text-slate-800">Tawarkan OTO</p>
                                    <p class="text-[10px] text-slate-500 mt-0.5 leading-tight">Upsell paket jasa promo ke customer.</p>
                                </div>
                            </div>
                        </label>

                        {{-- Opsi 3: Sertakan Revisi --}}
                        <label class="cursor-pointer">
                            <input type="radio" name="unified_action_type" value="revision" x-model="actionType" class="sr-only">
                            <div class="p-3.5 rounded-xl border-2 transition-all flex flex-col justify-between h-full select-none"
                                 :class="actionType === 'revision' ? 'border-rose-500 bg-rose-50/40 shadow-xs' : 'border-slate-200 bg-slate-50/50 hover:border-slate-300'">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-base">🛠️</span>
                                    <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center"
                                         :class="actionType === 'revision' ? 'border-rose-500 bg-rose-500' : 'border-slate-300'">
                                        <div class="w-1.5 h-1.5 rounded-full bg-white" x-show="actionType === 'revision'"></div>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-black text-slate-800">Ajukan Revisi</p>
                                    <p class="text-[10px] text-slate-500 mt-0.5 leading-tight">Kembalikan unit ke workshop untuk perbaikan.</p>
                                </div>
                            </div>
                        </label>
                    </div>

                    {{-- SUB-PANEL ACCORDION OTO --}}
                    <div x-show="actionType === 'oto'" x-transition class="pt-4 border-t border-amber-200/70 space-y-4">
                        <div class="flex items-center justify-between bg-amber-500/10 p-3 rounded-xl border border-amber-200">
                            <div class="flex items-center gap-2">
                                <span class="text-amber-600 font-black text-sm">✨</span>
                                <span class="text-xs font-black text-amber-900 uppercase tracking-wide">Form Penawaran One Time Offer (OTO)</span>
                            </div>
                            <span class="text-[10px] font-bold text-amber-700 bg-amber-100 px-2.5 py-0.5 rounded-md">Aktif</span>
                        </div>

                        {{-- Search Bar OTO --}}
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </span>
                            <input type="text" x-model="searchOto" 
                                   class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 focus:ring-amber-500 rounded-xl pl-9 pr-8 py-2 text-xs text-slate-800 font-bold" 
                                   placeholder="Cari layanan OTO... (contoh: Repaint, Sol, Deep Clean)">
                            <button type="button" x-show="searchOto.length > 0" @click="searchOto = ''" class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400 hover:text-slate-600">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        {{-- Grid Layanan OTO --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 max-h-56 overflow-y-auto custom-scrollbar p-1">
                            <template x-for="s in services" :key="s.id">
                                <div x-show="matchesSearch(s.name)"
                                     @click="toggleService(s)"
                                     class="border-2 rounded-xl p-3 cursor-pointer transition-all select-none"
                                     :class="isSelected(s.id) ? 'border-amber-500 bg-amber-50/50 shadow-2xs' : 'border-slate-200 hover:border-slate-300 bg-white'">
                                    <div class="flex justify-between items-center font-bold text-xs text-slate-800">
                                        <span x-text="s.name"></span>
                                        <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center transition-colors"
                                             :class="isSelected(s.id) ? 'bg-amber-500 border-amber-500 text-white' : 'border-slate-300'">
                                            <span x-show="isSelected(s.id)" class="text-[9px] font-black">✓</span>
                                        </div>
                                    </div>
                                    <div class="mt-1.5 flex items-center justify-between">
                                        <div class="text-xs font-black text-amber-600">
                                            <span x-text="formatRupiah(s.price)"></span>
                                            <span class="text-[8px] font-bold text-slate-400 uppercase ml-1">(Promo)</span>
                                        </div>
                                    </div>

                                    <div x-show="isSelected(s.id)" @click.stop class="mt-2.5 space-y-1.5 pt-2 border-t border-amber-200">
                                        <div>
                                            <label class="text-[8px] font-bold text-slate-400 uppercase block">Harga Normal (Sebelum Diskon)</label>
                                            <input type="number" x-model.number="selectedServices[s.id].normal_price" 
                                                   class="w-full bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-700 py-1 px-2">
                                        </div>
                                        <div>
                                            <label class="text-[8px] font-bold text-slate-400 uppercase block">Tambahan HK (Hari Kerja)</label>
                                            <input type="number" x-model.number="selectedServices[s.id].hk_days" 
                                                   class="w-full bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-700 py-1 px-2">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Reason Description --}}
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">
                                Alasan Penawaran OTO <span class="text-rose-500">*</span>
                            </label>
                            <textarea x-model="otoDescription" rows="2" 
                                      class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-xs focus:ring-amber-500 focus:border-amber-500 font-medium text-slate-800"
                                      placeholder="Contoh: Sol sudah tipis dan mulai lepas, disarankan jahit sol agar awet..."></textarea>
                        </div>

                        {{-- Masa Berlaku --}}
                        <div class="pt-2 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <p class="text-[10px] font-black text-slate-500 uppercase tracking-wider">Masa Berlaku:</p>
                                <p class="text-[10px] text-amber-600 font-bold mt-0.5">Sampai: <span x-text="validUntilText"></span></p>
                            </div>
                            <div class="flex gap-2">
                                <template x-for="d in [3, 5, 7, 14]" :key="d">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="unified_oto_valid_days" :value="d" x-model.number="validDays" class="sr-only">
                                        <div class="w-11 h-9 rounded-lg border-2 flex items-center justify-center transition-all"
                                             :class="validDays == d ? 'border-amber-500 bg-amber-50 text-amber-800 font-black' : 'border-slate-200 bg-slate-50 text-slate-400'">
                                            <span class="text-xs" x-text="d + 'h'"></span>
                                        </div>
                                    </label>
                                </template>
                            </div>
                        </div>

                        {{-- WhatsApp Automation Option --}}
                        <div class="p-3 bg-amber-50/50 border border-amber-200/60 rounded-xl flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <span class="text-base">📲</span>
                                <div>
                                    <p class="text-xs font-black text-slate-800">Kirim Otomatis via WhatsApp</p>
                                    <p class="text-[9px] text-slate-500">Kirim draft penawaran ke WhatsApp customer via antrean CX</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" x-model="sendAutomation" class="sr-only peer">
                                <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-amber-500"></div>
                            </label>
                        </div>

                        {{-- Total Summary OTO --}}
                        <div x-show="Object.keys(selectedServices).length > 0" class="flex justify-between items-center p-3 bg-amber-50 rounded-xl border border-amber-200">
                            <div>
                                <p class="text-[9px] uppercase font-black text-slate-400">Total Normal</p>
                                <p class="text-xs font-bold text-slate-400 line-through" x-text="formatRupiah(totalNormal)"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-[9px] uppercase font-black text-amber-600">Total Promo OTO ✨</p>
                                <p class="text-lg font-black text-amber-700" x-text="formatRupiah(totalOto)"></p>
                            </div>
                        </div>
                    </div>

                    {{-- SUB-PANEL ACCORDION REVISI --}}
                    <div x-show="actionType === 'revision'" x-transition class="pt-4 border-t border-rose-200/70 space-y-4">
                        <div class="flex items-center justify-between bg-rose-500/10 p-3 rounded-xl border border-rose-200">
                            <div class="flex items-center gap-2">
                                <span class="text-rose-600 font-black text-sm">🛠️</span>
                                <span class="text-xs font-black text-rose-900 uppercase tracking-wide">Form Pengajuan Revisi Teknik ke Workshop</span>
                            </div>
                            <span class="text-[10px] font-bold text-rose-700 bg-rose-100 px-2.5 py-0.5 rounded-md">Aktif</span>
                        </div>

                        {{-- Deskripsi Masalah --}}
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">
                                Deskripsi Masalah / Alasan Revisi <span class="text-rose-500">*</span>
                            </label>
                            <textarea wire:model="revisionDescription" rows="3" 
                                      class="w-full bg-slate-50 border border-slate-200 focus:border-rose-500 focus:ring-rose-500 rounded-xl p-3 text-xs font-medium text-slate-800"
                                      placeholder="Jelaskan detail kendala teknis atau komplain hasil pengerjaan sepatu dari customer..."></textarea>
                            @error('revisionDescription')
                                <p class="text-rose-500 text-[10px] mt-1 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Foto Masalah Multiple --}}
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">
                                Upload Foto Bukti Kendala (Opsional)
                            </label>
                            <input type="file" wire:model="revisionPhotos" accept="image/*" multiple
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100 cursor-pointer">
                            <p class="text-[9px] text-slate-400 mt-1 italic">Format: JPG, PNG, WEBP. Otomatis dikompresi ke JPG ringan.</p>

                            <div wire:loading wire:target="revisionPhotos" class="text-xs text-rose-600 font-bold mt-1.5 flex items-center gap-1.5">
                                <svg class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Mengunggah foto...
                            </div>

                            @if(!empty($revisionPhotos))
                            <div class="mt-2.5 flex flex-wrap gap-2">
                                @foreach($revisionPhotos as $p)
                                    @if(is_object($p) && method_exists($p, 'temporaryUrl'))
                                        <img src="{{ $p->temporaryUrl() }}" class="w-14 h-14 object-cover rounded-xl border border-rose-200 shadow-2xs">
                                    @endif
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Action Buttons --}}
            <div class="p-4 sm:p-5 bg-white border-t border-slate-200 flex items-center justify-between gap-3">
                <button type="button" @click="openAction = false" 
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all uppercase tracking-wider active:scale-95">
                    Batal
                </button>

                <button type="button" @click="submit" 
                        wire:loading.attr="disabled"
                        class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-md active:scale-95 flex items-center gap-2"
                        :class="actionType === 'oto' ? 'bg-amber-500 hover:bg-amber-600 text-white shadow-amber-500/20' : (actionType === 'revision' ? 'bg-rose-600 hover:bg-rose-700 text-white shadow-rose-600/20' : 'bg-[#22AF85] hover:bg-[#1b916d] text-white shadow-[#22AF85]/20')">
                    
                    <span wire:loading.remove wire:target="saveAction">
                        <span x-show="actionType === 'none'">Simpan Respon</span>
                        <span x-show="actionType === 'oto'">Simpan Respon &amp; Buat OTO ✨</span>
                        <span x-show="actionType === 'revision'">Simpan Respon &amp; Ajukan Revisi 🛠️</span>
                    </span>

                    <span wire:loading wire:target="saveAction" class="inline-flex items-center gap-2">
                        <svg class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>Menyimpan Data...</span>
                    </span>
                </button>
            </div>
        </div>
    </div>

    @script
    <script>
        $wire.on('notify', (event) => {
            alert(event[0].message);
        });
    </script>
    @endscript
</div>
