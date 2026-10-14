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
                        
                        {{-- Data: Action --}}
                        <td class="px-6 py-5 align-top text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                {{-- Tombol + OTO --}}
                                <button type="button" 
                                        wire:click="openOtoModal({{ $item->work_order_id }})" 
                                        title="Buat Penawaran OTO (One Time Offer)"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 border border-amber-200/80 hover:border-amber-400 text-amber-700 text-[10px] font-black uppercase tracking-wider transition-all shadow-xs active:scale-95 group/oto">
                                    <span class="text-xs group-hover/oto:rotate-12 transition-transform">✨</span>
                                    <span class="hidden sm:inline">OTO</span>
                                </button>

                                {{-- Tombol + Revisi --}}
                                <button type="button" 
                                        wire:click="openRevisionModal({{ $item->work_order_id }})" 
                                        title="Ajukan Revisi Teknik ke Workshop"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 border border-rose-200/80 hover:border-rose-400 text-rose-700 text-[10px] font-black uppercase tracking-wider transition-all shadow-xs active:scale-95 group/rev">
                                    <span class="text-xs group-hover/rev:rotate-12 transition-transform">🛠️</span>
                                    <span class="hidden sm:inline">Revisi</span>
                                </button>

                                {{-- Tombol Edit Respon --}}
                                <button type="button" 
                                        wire:click="edit({{ $item->id }})" 
                                        title="Catat / Perbarui Respon Pelanggan"
                                        class="inline-flex items-center justify-center p-2 bg-white text-[#22AF85] hover:bg-[#22AF85]/5 hover:border-[#22AF85]/30 rounded-xl transition-all shadow-sm shadow-black/5 border border-gray-100 active:scale-95">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
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
        </div>
    </div>

    {{-- Premium Edit Modal --}}
    <div x-data="{ open: false }" 
         x-on:open-edit-modal.window="open = true" 
         x-on:close-edit-modal.window="open = false"
         x-cloak>
        <x-modal-custom x-show="open" @close="open = false" title="📝 Konfirmasi Status">
            <form wire:submit.prevent="save" class="space-y-6">
                {{-- Detail Pelanggan Info Box --}}
                @php
                    $modalItem = $editingId ? App\Models\CxAfterConfirmation::with('workOrder')->find($editingId) : null;
                @endphp
                @if($modalItem)
                <div class="bg-gradient-to-br from-gray-50 to-white rounded-[1.5rem] p-5 border border-gray-200/60 shadow-sm relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-[#FFC232]/10 rounded-full blur-xl"></div>
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] block">Data Konfirmasi Pelanggan</label>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] font-black px-2 py-0.5 rounded-md border uppercase tracking-wider {{ $modalItem->stage_badge['bg'] }} {{ $modalItem->stage_badge['text'] }} {{ $modalItem->stage_badge['border'] }}">
                                {{ $modalItem->stage_badge['label'] }}
                            </span>
                            <span class="text-[10px] font-black px-2 py-0.5 rounded-md uppercase tracking-wider {{ $modalItem->round_badge['badge_class'] }}">
                                {{ $modalItem->round_badge['label'] }}
                            </span>
                        </div>
                    </div>
                    <div class="text-lg font-black text-gray-900 tracking-tight leading-tight">
                        {{ $modalItem->workOrder->customer_name }}
                    </div>
                    <div class="flex items-center gap-3 mt-1.5 text-xs text-gray-500 font-bold">
                        <span class="font-black text-[#22AF85]">{{ $modalItem->workOrder->spk_number }}</span>
                        <span>•</span>
                        <span>{{ $modalItem->workOrder->customer_phone }}</span>
                        <span>•</span>
                        <span class="text-gray-400 uppercase">{{ $modalItem->workOrder->shoe_brand }}</span>
                    </div>
                    <div class="mt-3 pt-2.5 border-t border-gray-100 flex items-center justify-between text-xs">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Status Pengambilan:</span>
                        @if($modalItem->workOrder->taken_date)
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-black px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                                📦 Sudah Diambil ({{ $modalItem->workOrder->taken_date->format('d M Y, H:i') }} WIB)
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-black px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-200">
                                🏠 Belum Diambil / Di Rak Selesai
                            </span>
                        @endif
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-1 gap-5">
                    {{-- Select Respon --}}
                    <div>
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] block mb-2 px-1">Opsi Respon</label>
                        <div class="relative">
                            <select wire:model="editingResponse" class="w-full appearance-none bg-white border-2 border-gray-100 rounded-2xl px-5 py-4 text-sm font-black text-gray-700 hover:border-[#22AF85]/40 focus:border-[#22AF85] focus:ring-4 focus:ring-[#22AF85]/10 transition-all cursor-pointer shadow-sm">
                                <option value="">— Pilih Respon Customer —</option>
                                @foreach(['Puas', 'Komplain', 'Kurang Puas', 'No Respon 1x24 Jam', 'Hold'] as $res)
                                    <option value="{{ $res }}">{{ $res }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-5 text-gray-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>

                    {{-- Textarea Note --}}
                    <div>
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] block mb-2 px-1">Catatan Tambahan (Opsional)</label>
                        <textarea wire:model="editingNotes" rows="3" class="w-full bg-white border-2 border-gray-100 rounded-2xl px-5 py-4 text-sm font-medium text-gray-700 placeholder:text-gray-300 hover:border-[#22AF85]/40 focus:border-[#22AF85] focus:ring-4 focus:ring-[#22AF85]/10 transition-all shadow-sm" placeholder="Tulis catatan historis panggilan disini..."></textarea>
                    </div>

                    {{-- Quick Action Shortcuts: OTO & Revisi --}}
                    @if($modalItem)
                    <div class="p-3.5 bg-gray-50/80 rounded-2xl border border-gray-200/60 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                        <div class="flex items-center gap-2">
                            <span class="text-xs">⚡</span>
                            <span class="text-[10px] font-black text-gray-500 uppercase tracking-wider">Tindak Lanjut Cepat:</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" 
                                    wire:click="openOtoModal({{ $modalItem->work_order_id }})"
                                    class="px-3 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-[10px] font-black uppercase tracking-wider flex items-center gap-1.5 transition-all shadow-xs active:scale-95">
                                <span>✨</span> + Tawarkan OTO
                            </button>
                            <button type="button" 
                                    wire:click="openRevisionModal({{ $modalItem->work_order_id }})"
                                    class="px-3 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-[10px] font-black uppercase tracking-wider flex items-center gap-1.5 transition-all shadow-xs active:scale-95">
                                <span>🛠️</span> + Ajukan Revisi
                            </button>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Action Buttons --}}
                <div class="pt-2 flex gap-3">
                    <button type="button" @click="open = false" class="px-8 py-4 bg-white border-2 border-gray-100 hover:bg-gray-50 hover:border-gray-200 rounded-[1.5rem] text-xs font-black text-gray-500 transition-all uppercase tracking-widest active:scale-95 shadow-sm">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-4 bg-[#FFC232] hover:bg-[#F2B01E] rounded-[1.5rem] text-xs font-black text-gray-900 shadow-[0_8px_20px_-6px_rgba(255,194,50,0.5)] transition-all uppercase tracking-[0.2em] active:scale-95 border border-[#E5A71B] relative overflow-hidden group">
                        <div class="absolute inset-0 bg-white/30 group-hover:translate-x-full transition-transform duration-500 -skew-x-12 -ml-8 w-20"></div>
                        <span wire:loading.remove wire:target="save" class="flex justify-center items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            Simpan Data
                        </span>
                        <span wire:loading wire:target="save" class="flex items-center justify-center gap-3">
                             <svg class="animate-spin h-4 w-4 text-gray-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                <path class="opacity-100" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </x-modal-custom>
    </div>

    {{-- Modal Penawaran OTO --}}
    <div x-data="{
            openOto: false,
            searchOto: '',
            workOrder: null,
            selectedServices: {},
            description: '',
            validDays: 5,
            sendAutomation: true,
            services: @js($availableOtoServices),

            init() {
                this.$wire.on('open-oto-modal', (event) => {
                    this.workOrder = event[0]?.workOrder || null;
                    this.selectedServices = {};
                    this.description = '';
                    this.validDays = 5;
                    this.sendAutomation = true;
                    this.searchOto = '';
                    this.openOto = true;
                });
                this.$wire.on('close-oto-modal', () => {
                    this.openOto = false;
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
                let servicesList = Object.values(this.selectedServices);
                if (servicesList.length === 0) {
                    alert('Pilih minimal satu layanan OTO.');
                    return;
                }
                if (!this.description.trim()) {
                    alert('Alasan penawaran OTO wajib diisi.');
                    return;
                }
                $wire.submitOto({
                    services: servicesList,
                    description: this.description,
                    valid_days: this.validDays,
                    send_automation: this.sendAutomation
                });
            }
         }"
         x-show="openOto"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
         style="display: none;"
         x-cloak>
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col border border-gray-100" @click.away="openOto = false">
            {{-- Header --}}
            <div class="p-6 pb-4 text-center bg-gradient-to-r from-amber-500 to-orange-500 text-white relative">
                <button type="button" @click="openOto = false" class="absolute top-4 right-4 text-white/80 hover:text-white p-1 rounded-full hover:bg-black/10 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                <h3 class="text-2xl font-black">Penawaran One Time Offer (OTO)</h3>
                <p class="text-amber-100 text-xs mt-0.5">Penawaran paket layanan spesial untuk customer ✨</p>
                
                {{-- SPK & Customer pill --}}
                <template x-if="workOrder">
                    <div class="mt-2.5 inline-flex items-center gap-2 px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold text-white">
                        <span x-text="workOrder.spk_number"></span>
                        <span>•</span>
                        <span x-text="workOrder.customer_name"></span>
                        <template x-if="workOrder.shoe_brand">
                            <span>• <span x-text="workOrder.shoe_brand" class="uppercase"></span></span>
                        </template>
                    </div>
                </template>
            </div>

            {{-- Search Bar --}}
            <div class="px-6 pt-4 pb-2">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" 
                           x-model="searchOto" 
                           class="w-full bg-gray-50 border-gray-200 focus:border-amber-500 focus:ring-amber-500 rounded-xl pl-10 pr-9 py-2.5 text-xs text-gray-800 font-bold transition-all" 
                           placeholder="Cari layanan OTO... (contoh: Repaint, Sol, Deep Clean)">
                    <button type="button" 
                            x-show="searchOto.length > 0" 
                            @click="searchOto = ''" 
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>

            {{-- Body Scrollable --}}
            <div class="flex-1 overflow-y-auto px-6 py-3 space-y-4">
                {{-- Services Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <template x-for="s in services" :key="s.id">
                        <div x-show="matchesSearch(s.name)"
                             @click="toggleService(s)"
                             class="border-2 rounded-2xl p-3.5 cursor-pointer transition-all select-none"
                             :class="isSelected(s.id) ? 'border-amber-500 bg-amber-50/50 shadow-xs' : 'border-gray-100 hover:border-gray-200 bg-white'">
                            <div class="flex justify-between items-center font-bold text-xs text-gray-800">
                                <span x-text="s.name"></span>
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors"
                                     :class="isSelected(s.id) ? 'bg-amber-500 border-amber-500 text-white' : 'border-gray-300'">
                                    <span x-show="isSelected(s.id)" class="text-[10px] font-black">✓</span>
                                </div>
                            </div>
                            <div class="mt-2 flex items-center justify-between">
                                <div class="text-sm font-black text-amber-600">
                                    <span x-text="formatRupiah(s.price)"></span>
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tight ml-1">(HARGA PROMO)</span>
                                </div>
                            </div>

                            {{-- Selected Service Details --}}
                            <div x-show="isSelected(s.id)" @click.stop class="mt-3 space-y-2 pt-2.5 border-t border-amber-200/60">
                                <div>
                                    <label class="text-[9px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Harga Normal (Sebelum Diskon)</label>
                                    <input type="number" 
                                           x-model.number="selectedServices[s.id].normal_price" 
                                           class="w-full bg-white border-gray-200 rounded-lg text-xs font-bold text-gray-700 focus:ring-amber-500 focus:border-amber-500 py-1.5 px-2.5">
                                </div>
                                <div>
                                    <label class="text-[9px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Tambahan Hari Kerja (HK)</label>
                                    <input type="number" 
                                           x-model.number="selectedServices[s.id].hk_days" 
                                           class="w-full bg-white border-gray-200 rounded-lg text-xs font-bold text-gray-700 focus:ring-amber-500 focus:border-amber-500 py-1.5 px-2.5">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Empty Search --}}
                <div x-show="!hasMatches" 
                     style="display: none;" 
                     class="py-8 text-center text-gray-400 text-xs font-bold">
                    Tidak ada layanan OTO yang cocok dengan kata kunci pencarian.
                </div>

                {{-- Reason Description --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-wider mb-1">Alasan Penawaran OTO</label>
                    <textarea x-model="description" rows="2" 
                              class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-xs focus:ring-amber-500 focus:border-amber-500 font-medium text-gray-700"
                              placeholder="Contoh: Sol sudah tipis dan mulai lepas, disarankan jahit sol agar awet..."></textarea>
                </div>

                {{-- Masa Berlaku --}}
                <div class="pt-3 border-t border-gray-100 text-center">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Masa Berlaku Penawaran</p>
                    <div class="flex justify-center gap-3">
                        <template x-for="d in [3, 5, 7, 14]" :key="d">
                            <label class="cursor-pointer">
                                <input type="radio" name="oto_valid_days" :value="d" x-model.number="validDays" class="sr-only">
                                <div class="w-14 h-12 rounded-xl border-2 flex flex-col items-center justify-center transition-all"
                                     :class="validDays == d ? 'border-amber-500 bg-amber-50 text-amber-700 font-black shadow-xs' : 'border-gray-100 bg-gray-50 text-gray-400 hover:border-gray-200'">
                                    <span class="text-sm font-black" x-text="d"></span>
                                    <span class="text-[8px] uppercase tracking-wider">HARI</span>
                                </div>
                            </label>
                        </template>
                    </div>
                    <p class="text-[10px] text-amber-600 mt-2 font-bold">Berlaku sampai: <span x-text="validUntilText"></span></p>
                </div>

                {{-- WhatsApp Automation Option --}}
                <div class="p-3.5 bg-amber-50/60 border border-amber-200/60 rounded-2xl flex items-center justify-between gap-3 text-left">
                    <div class="flex items-center gap-2.5">
                        <span class="text-xl">📲</span>
                        <div>
                            <p class="text-xs font-black text-gray-800">Kirim Otomatis via WhatsApp</p>
                            <p class="text-[10px] text-gray-500 font-semibold">Otomatis kirim draft penawaran ke WhatsApp customer melalui antrean CX</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" x-model="sendAutomation" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                    </label>
                </div>
            </div>

            {{-- Footer Summary & Buttons --}}
            <div class="p-6 pt-3 bg-gray-50 border-t border-gray-100">
                <div x-show="Object.keys(selectedServices).length > 0" class="flex justify-between items-end mb-4">
                    <div>
                        <p class="text-[9px] uppercase font-black text-gray-400">Total Normal</p>
                        <p class="text-sm font-bold text-gray-400 line-through" x-text="formatRupiah(totalNormal)"></p>
                    </div>
                    <div class="text-right">
                        <p class="text-[9px] uppercase font-black text-amber-500">Total Promo OTO ✨</p>
                        <p class="text-2xl font-black text-amber-600" x-text="formatRupiah(totalOto)"></p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" @click="openOto = false" class="py-3 font-bold text-xs text-gray-500 uppercase rounded-xl hover:bg-gray-200 transition-all">Batal</button>
                    <button type="button" @click="submit" :disabled="Object.keys(selectedServices).length === 0" class="bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl py-3 font-black uppercase text-xs shadow-lg disabled:opacity-50 transition-all active:scale-95">
                        Kirim OTO
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Ajukan Revisi Teknik --}}
    <div x-data="{ openRevision: false }"
         x-on:open-revision-modal.window="openRevision = true"
         x-on:close-revision-modal.window="openRevision = false"
         x-show="openRevision"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
         style="display: none;"
         x-cloak>
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden border border-gray-100" @click.away="openRevision = false">
            {{-- Header --}}
            <div class="p-6 text-center bg-rose-600 text-white relative">
                <button type="button" @click="openRevision = false" class="absolute top-4 right-4 text-white/80 hover:text-white p-1 rounded-full hover:bg-black/10 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                <h3 class="text-xl font-black uppercase tracking-wider">AJUKAN REVISI TEKNIK</h3>
                <p class="text-rose-100 text-xs mt-1">Kembalikan unit ke workshop untuk perbaikan stasiun</p>
                @if($revisionWorkOrder)
                <div class="mt-2.5 inline-flex items-center gap-2 px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold text-white">
                    <span>{{ $revisionWorkOrder->spk_number }}</span>
                    <span>•</span>
                    <span>{{ $revisionWorkOrder->customer_name }}</span>
                    @if($revisionWorkOrder->shoe_brand)
                        <span>• <span class="uppercase">{{ $revisionWorkOrder->shoe_brand }}</span></span>
                    @endif
                </div>
                @endif
            </div>

            <form wire:submit.prevent="submitRevision" class="p-6 space-y-4">
                {{-- Deskripsi Masalah --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">
                        DESKRIPSI MASALAH / ALASAN REVISI <span class="text-red-500">*</span>
                    </label>
                    <textarea wire:model="revisionDescription" rows="4" required
                              class="w-full bg-gray-50 border border-gray-200 rounded-2xl p-3.5 text-xs focus:ring-rose-500 focus:border-rose-500 font-medium text-gray-800"
                              placeholder="Jelaskan detail masalah teknis yang perlu diperbaiki ulang oleh teknisi..."></textarea>
                    @error('revisionDescription')
                        <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Foto Masalah --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">
                        FOTO MASALAH (BISA LEBIH DARI 1)
                    </label>
                    <input type="file" wire:model="revisionPhotos" accept="image/*" multiple
                           class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100 cursor-pointer">
                    <p class="text-[9px] text-gray-400 mt-1 italic">Format: JPG, PNG, WEBP. Maks 5MB/foto.</p>
                    @error('revisionPhotos.*')
                        <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                    @enderror

                    {{-- Uploading indicator --}}
                    <div wire:loading wire:target="revisionPhotos" class="text-xs text-rose-600 font-bold mt-1.5 flex items-center gap-1.5">
                        <svg class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Mengunggah foto...
                    </div>

                    {{-- Previews --}}
                    @if(!empty($revisionPhotos))
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach($revisionPhotos as $p)
                            @if(is_object($p) && method_exists($p, 'temporaryUrl'))
                                <img src="{{ $p->temporaryUrl() }}" class="w-14 h-14 object-cover rounded-xl border border-rose-200 shadow-xs">
                            @endif
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- Action Buttons --}}
                <div class="grid grid-cols-2 gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="openRevision = false" class="py-3 font-black text-xs text-gray-400 uppercase tracking-wider hover:bg-gray-100 rounded-xl transition-all">
                        BATAL
                    </button>
                    <button type="submit" 
                            wire:loading.attr="disabled"
                            class="bg-rose-600 hover:bg-rose-700 text-white rounded-xl py-3 font-black uppercase text-xs shadow-md shadow-rose-200 transition-all active:scale-95 disabled:opacity-50">
                        <span wire:loading.remove wire:target="submitRevision">KIRIM KE REVISI</span>
                        <span wire:loading wire:target="submitRevision" class="inline-flex items-center gap-1.5">
                            <svg class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            MEMPROSES...
                        </span>
                    </button>
                </div>
            </form>
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
