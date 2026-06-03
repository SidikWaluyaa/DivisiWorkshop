@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* Ultra-premium Flatpickr Custom Overrides - HSL tailwind tailored styles */
        .flatpickr-calendar {
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(20px) !important;
            border: 1px solid rgba(241, 245, 249, 0.9) !important;
            border-radius: 24px !important;
            box-shadow: 0 30px 60px -15px rgba(244, 63, 94, 0.08), 0 10px 20px -5px rgba(0, 0, 0, 0.03) !important;
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
            background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%) !important;
            border-color: transparent !important;
            color: #ffffff !important;
            box-shadow: 0 10px 15px -3px rgba(244, 63, 94, 0.3) !important;
        }
        .flatpickr-day.inRange {
            background: rgba(244, 63, 94, 0.08) !important;
            color: #e11d48 !important;
        }
    </style>
@endpush

<div class="py-10 bg-[#f8fafc] min-h-screen font-sans">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Elite Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="px-3 py-1 rounded-full text-[10px] font-black tracking-widest bg-rose-500/10 text-rose-600 uppercase border border-rose-500/10">
                        Operational SLA Audit
                    </span>
                </div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight leading-none mb-2">
                    Dashboard Monitoring Overdue SLA
                </h1>
                <p class="text-sm text-gray-500 font-medium">
                    Pantau kemacetan antrean kerja dan keterlambatan estimasi di setiap tahapan workshop secara real-time.
                </p>
            </div>
            
            {{-- Export Actions --}}
            <div class="flex items-center gap-3">
                <button wire:click="exportToCsv" 
                        class="px-6 py-3.5 bg-gray-900 hover:bg-gray-800 text-white rounded-2xl text-xs font-black tracking-wider uppercase shadow-lg shadow-gray-900/10 hover:shadow-gray-900/20 active:scale-95 transition-all flex items-center gap-3">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export Ke CSV
                </button>

                <a href="{{ route('cx.overdue-dashboard.export-pdf', [
                    'card' => $activeCard,
                    'spk' => $searchSpk,
                    'customer' => $searchCustomer,
                    'start' => $startDate,
                    'end' => $endDate,
                    'est' => $filterEstimation
                ]) }}" target="_blank" 
                   class="px-6 py-3.5 bg-rose-600 hover:bg-rose-500 text-white rounded-2xl text-xs font-black tracking-wider uppercase shadow-lg shadow-rose-600/10 hover:shadow-rose-600/20 active:scale-95 transition-all flex items-center gap-3">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Cetak PDF
                </a>
            </div>
        </div>

        {{-- Scoreboard Grid (8 Premium Cards) --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            @foreach($scoreboard as $key => $card)
                @php
                    $isActive = $activeCard === $key;
                    $isAnyActive = !empty($activeCard);
                    $borderStyle = $isActive 
                        ? 'ring-2 ring-gray-900 scale-102 shadow-lg shadow-gray-200' 
                        : ($isAnyActive ? 'opacity-40 border-gray-100' : 'border-gray-100');
                    
                    $themeMap = [
                        'amber' => ['bg' => 'bg-amber-500/[0.04]', 'border' => 'border-amber-100', 'text' => 'text-amber-700', 'badge' => 'bg-amber-500/10 text-amber-800', 'icon' => '⏳'],
                        'orange' => ['bg' => 'bg-orange-500/[0.04]', 'border' => 'border-orange-100', 'text' => 'text-orange-700', 'badge' => 'bg-orange-500/10 text-orange-800', 'icon' => '🔨'],
                        'rose' => ['bg' => 'bg-rose-500/[0.04]', 'border' => 'border-rose-100', 'text' => 'text-rose-700', 'badge' => 'bg-rose-500/10 text-rose-800', 'icon' => '🚨'],
                        'teal' => ['bg' => 'bg-teal-500/[0.04]', 'border' => 'border-teal-100', 'text' => 'text-teal-700', 'badge' => 'bg-teal-500/10 text-teal-800', 'icon' => '📦']
                    ];
                    
                    $theme = $themeMap[$card['color_theme']] ?? $themeMap['orange'];
                @endphp

                <div class="p-5 rounded-3xl border {{ $theme['bg'] }} {{ $theme['border'] }} transition-all duration-300 relative overflow-hidden group {{ $borderStyle }}">
                    
                    <div class="absolute -right-6 -bottom-6 text-7xl opacity-5 group-hover:scale-110 transition-transform duration-500 select-none">
                        {{ $theme['icon'] }}
                    </div>

                    <div class="flex justify-between items-start mb-3">
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">
                            {{ $card['label'] }}
                        </span>
                        @if($card['overdue_count'] > 0 && $card['total_days_overdue'] > 0)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black {{ $theme['badge'] }}">
                                ⏳ {{ $card['total_days_overdue'] }} Hari
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-gray-50 text-gray-400 border border-gray-100/50">
                                On Track
                            </span>
                        @endif
                    </div>

                    <div class="mt-2">
                        <div class="text-3xl font-black text-gray-900 tracking-tight leading-none mb-1">
                            {{ number_format($card['overdue_count']) }} <span class="text-xs font-bold text-gray-400">SPK</span>
                        </div>
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            {{ $key === 'GLOBAL' ? 'Total Kelewat Estimasi' : 'Total Terlambat Stage' }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Filter Bar (Combined & Flexible) --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 mb-8">
            <div class="flex flex-col lg:flex-row gap-4 items-end">
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4 flex-grow w-full">
                    
                    {{-- Search SPK --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[10px] font-black uppercase tracking-wider text-gray-400">Cari Nomor SPK</label>
                        <input type="text" wire:model.live.debounce.300ms="searchSpk" 
                               placeholder="Contoh: S-2602..." 
                               class="w-full bg-gray-50/50 border-gray-100 rounded-2xl text-xs font-bold text-gray-700 py-3.5 px-4 focus:bg-white focus:ring-2 focus:ring-rose-500/10 focus:border-rose-500 transition-all">
                    </div>

                    {{-- Search Customer --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[10px] font-black uppercase tracking-wider text-gray-400">Nama Customer</label>
                        <input type="text" wire:model.live.debounce.300ms="searchCustomer" 
                               placeholder="Cari nama pelanggan..." 
                               class="w-full bg-gray-50/50 border-gray-100 rounded-2xl text-xs font-bold text-gray-700 py-3.5 px-4 focus:bg-white focus:ring-2 focus:ring-rose-500/10 focus:border-rose-500 transition-all">
                    </div>

                    {{-- Rentang Tanggal Picker --}}
                    <div class="flex flex-col gap-1.5 md:col-span-2"
                         x-data="{
                             clearRange() {
                                 $wire.set('startDate', '', true);
                                 $wire.set('endDate', '', true);
                             }
                         }">
                        <label class="text-[10px] font-black uppercase tracking-wider text-gray-400">Rentang Tanggal Masuk Stage</label>
                        
                        <div class="relative" wire:ignore wire:key="flatpickr-stage-container">
                            <input type="text" readonly
                                   x-init="
                                       flatpickr($el, {
                                           mode: 'range',
                                           dateFormat: 'Y-m-d',
                                           defaultDate: $wire.startDate && $wire.endDate ? [$wire.startDate, $wire.endDate] : null,
                                           onChange: (selectedDates, dateStr, instance) => {
                                               if (selectedDates.length === 2) {
                                                   let start = instance.formatDate(selectedDates[0], 'Y-m-d');
                                                   let end = instance.formatDate(selectedDates[1], 'Y-m-d');
                                                   $wire.set('startDate', start, true);
                                                   $wire.set('endDate', end, true);
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
                                   placeholder="Pilih rentang tanggal masuk..."
                                   class="w-full bg-gray-50/50 border-gray-100 rounded-2xl text-xs font-bold text-gray-700 py-3.5 pl-10 pr-10 focus:bg-white focus:ring-2 focus:ring-rose-500/10 focus:border-rose-500 transition-all cursor-pointer">
                            
                            {{-- Calendar icon at the start --}}
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                📅
                            </div>

                            {{-- Clear icon at the end --}}
                            <button type="button" x-show="$wire.startDate" @click="clearRange()" 
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition-colors border-none bg-transparent outline-none">
                                ✕
                            </button>
                        </div>
                    </div>

                    {{-- Status Estimasi Dropdown --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[10px] font-black uppercase tracking-wider text-gray-400">Status Estimasi</label>
                        <select wire:model.live="filterEstimation" 
                                class="w-full bg-gray-50/50 border-gray-100 rounded-2xl text-xs font-bold text-gray-700 py-3.5 px-4 focus:bg-white focus:ring-2 focus:ring-rose-500/10 focus:border-rose-500 transition-all select-custom-premium">
                            <option value="all">Semua Estimasi</option>
                            <option value="missing">Belum Set Estimasi</option>
                            <option value="set">Sudah Set Estimasi</option>
                        </select>
                    </div>

                    {{-- Pilih Tahapan Dropdown --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[10px] font-black uppercase tracking-wider text-gray-400">Pilih Tahapan / Divisi</label>
                        <select wire:model.live="activeCard" 
                                class="w-full bg-gray-50/50 border-gray-100 rounded-2xl text-xs font-bold text-gray-700 py-3.5 px-4 focus:bg-white focus:ring-2 focus:ring-rose-500/10 focus:border-rose-500 transition-all select-custom-premium">
                            <option value="">Semua Tahapan (Overdue)</option>
                            <option value="GLOBAL">Estimasi Kelewat (Global)</option>
                            <option value="PREPARATION">Preparation</option>
                            <option value="SORTIR">Sortir</option>
                            <option value="PRODUCTION">Production</option>
                            <option value="QC">Quality Control</option>
                            <option value="REVISI">Revisi</option>
                            <option value="SELESAI">Selesai (Hold)</option>
                            <option value="DIANTAR">Diantar</option>
                        </select>
                    </div>
                </div>

                {{-- Reset Button --}}
                <button wire:click="resetFilters" 
                        class="px-5 py-3.5 bg-gray-50 hover:bg-gray-100 text-gray-400 font-bold rounded-2xl text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-2 border border-gray-100 shrink-0 w-full lg:w-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Reset Filter
                </button>
            </div>
        </div>

        {{-- Table Card --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden mb-10">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th wire:click="sortByField('spk_number')" class="px-6 py-4.5 text-[10px] font-black text-gray-400 uppercase tracking-widest cursor-pointer hover:bg-gray-50 transition-colors">
                                No. SPK {!! $sortBy === 'spk_number' ? ($sortDirection === 'asc' ? '▲' : '▼') : '' !!}
                            </th>
                            <th class="px-6 py-4.5 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Pelanggan & Sepatu
                            </th>
                            <th class="px-6 py-4.5 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Tahap Aktif
                            </th>
                            <th wire:click="sortByField('waktu')" class="px-6 py-4.5 text-[10px] font-black text-gray-400 uppercase tracking-widest cursor-pointer hover:bg-gray-50 transition-colors">
                                Masuk Stage {!! $sortBy === 'waktu' ? ($sortDirection === 'asc' ? '▲' : '▼') : '' !!}
                            </th>
                            <th wire:click="sortByField('estimation_date')" class="px-6 py-4.5 text-[10px] font-black text-gray-400 uppercase tracking-widest cursor-pointer hover:bg-gray-50 transition-colors">
                                Estimasi Selesai {!! $sortBy === 'estimation_date' ? ($sortDirection === 'asc' ? '▲' : '▼') : '' !!}
                            </th>
                            <th wire:click="sortByField('days_overdue')" class="px-6 py-4.5 text-[10px] font-black text-gray-400 uppercase tracking-widest cursor-pointer hover:bg-gray-50 transition-colors text-center">
                                Hari Kelewat {!! $sortBy === 'days_overdue' ? ($sortDirection === 'asc' ? '▲' : '▼') : '' !!}
                            </th>
                            <th class="px-6 py-4.5 text-[10px] font-black text-gray-400 uppercase tracking-widest w-1/4">
                                Keterangan & Catatan
                            </th>
                            <th class="px-6 py-4.5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($orders as $wo)
                            <tr class="hover:bg-gray-50/[0.4] transition-colors">
                                {{-- No SPK --}}
                                <td class="px-6 py-5 align-top font-mono font-black text-gray-900 text-xs">
                                    {{ $wo->spk_number }}
                                </td>
                                
                                {{-- Pelanggan & Sepatu --}}
                                <td class="px-6 py-5 align-top">
                                    <div class="font-bold text-gray-800 text-sm capitalize mb-1">{{ $wo->customer_name }}</div>
                                    <div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">
                                        {{ $wo->shoe_brand }} - <span class="text-gray-500">{{ $wo->shoe_type ?: '-' }}</span>
                                    </div>
                                </td>

                                {{-- Tahap Aktif --}}
                                <td class="px-6 py-5 align-top">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-gray-100 text-gray-700 text-[10px] font-black border border-gray-200/50 uppercase tracking-wider">
                                        {{ $wo->status->label() }}
                                    </span>
                                </td>

                                {{-- Tanggal Masuk Stage --}}
                                <td class="px-6 py-5 align-top text-xs font-bold text-gray-500">
                                    {{ $wo->waktu ? $wo->waktu->translatedFormat('d M Y') : $wo->updated_at->translatedFormat('d M Y') }}
                                    <div class="text-[10px] text-gray-400 font-bold mt-0.5">
                                        Pukul {{ $wo->waktu ? $wo->waktu->format('H:i') : $wo->updated_at->format('H:i') }}
                                    </div>
                                </td>

                                {{-- Estimasi Selesai --}}
                                <td class="px-6 py-5 align-top text-xs font-bold text-gray-600">
                                    {{ $wo->estimation_date && $wo->estimation_date->year > 2000 ? $wo->estimation_date->translatedFormat('d M Y') : 'Belum Set' }}
                                </td>

                                {{-- Hari Kelewat --}}
                                <td class="px-6 py-5 align-top text-center">
                                    @php $hasEstimation = $wo->estimation_date && $wo->estimation_date->year > 2000; @endphp
                                    @if($wo->days_overdue > 0 && !$hasEstimation)
                                        {{-- Tanpa estimasi: dihitung dari masuk stage --}}
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-500/10 text-amber-700 text-xs font-black shadow-sm shadow-amber-500/5">
                                            ⏳ {{ $wo->days_overdue }} Hari
                                        </span>
                                        <div class="text-[9px] text-amber-500 font-bold mt-0.5">Dari Masuk Stage</div>
                                    @elseif($wo->days_overdue > 0)
                                        {{-- Ada estimasi: dihitung dari estimasi selesai --}}
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-rose-500/10 text-rose-600 text-xs font-black shadow-sm shadow-rose-500/5">
                                            🚨 {{ $wo->days_overdue }} Hari
                                        </span>
                                        <div class="text-[9px] text-rose-400 font-bold mt-0.5">Dari Estimasi Selesai</div>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-500/10 text-emerald-600 text-xs font-black">
                                            ✅ On Track
                                        </span>
                                    @endif
                                </td>

                                {{-- Keterangan --}}
                                <td class="px-6 py-5 align-top text-xs text-gray-500 leading-relaxed italic">
                                    "{{ $wo->late_description ?: 'Tidak ada catatan hambatan khusus.' }}"
                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-5 align-middle text-center">
                                    <a href="{{ route('admin.orders.show', $wo->id) }}" 
                                       target="_blank"
                                       class="inline-flex items-center justify-center p-2.5 bg-gray-100 hover:bg-gray-900 text-gray-600 hover:text-white rounded-xl transition-all shadow-sm active:scale-95" 
                                       title="Lihat Detail SPK">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center gap-4">
                                        <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-300 border border-gray-100">
                                            🏆
                                        </div>
                                        <div class="space-y-1">
                                            <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">Kerja Bagus!</h3>
                                            <p class="text-gray-400 text-xs font-medium">Semua data pada filter ini berjalan on-track tanpa ada keterlambatan SLA.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Fixed Footer Pagination --}}
            @if($orders->hasPages())
                <div class="px-6 py-5 bg-gray-50/50 border-t border-gray-50 flex items-center justify-between">
                    <div class="text-xs font-bold text-gray-400">
                        Menampilkan {{ $orders->firstItem() }} - {{ $orders->lastItem() }} dari {{ $orders->total() }} Data
                    </div>
                    <div>
                        {{ $orders->links() }}
                    </div>
                </div>
            @endif
        </div>

        {{-- API Developer panel (Real-Time Integration Panel) --}}
        <div class="bg-gray-900 rounded-3xl border border-gray-800 shadow-xl p-8 relative overflow-hidden">
            {{-- Decorative accent --}}
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-rose-500 via-amber-500 to-teal-500"></div>

            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-6">
                <div>
                    <h3 class="text-base font-black text-white uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        ⚡ Integrasi Real-Time JSON API
                    </h3>
                    <p class="text-xs text-gray-400 mt-1 font-medium">
                        Gunakan endpoint di bawah ini untuk menarik data status overdue dan metrics scoreboard secara real-time dari aplikasi luar.
                    </p>
                </div>
                <span class="px-3 py-1 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-black uppercase tracking-widest">
                    Active Endpoint
                </span>
            </div>

            <div class="space-y-6">
                {{-- URL Input Box --}}
                <div class="flex flex-col gap-2">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Request Endpoint URL (GET)</label>
                    <div class="flex gap-2">
                        <input type="text" readonly 
                               value="{{ url('/api/v1/cx-overdue') }}{{ $apiKey ? '?api_key=' . $apiKey : '' }}" 
                               class="flex-grow bg-black/40 border border-gray-800 rounded-xl py-3 px-4 text-xs font-mono text-emerald-400 focus:outline-none">
                    </div>
                </div>

                {{-- Header & Key --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2">
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">HTTP Header Key</label>
                        <input type="text" readonly value="X-API-KEY" 
                               class="bg-black/40 border border-gray-800 rounded-xl py-3 px-4 text-xs font-mono text-gray-300 focus:outline-none">
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">HTTP Header Value (API Key)</label>
                        <input type="text" readonly value="{{ $apiKey ?: 'DASHBOARD_API_KEY_NOT_CONFIGURED' }}" 
                               class="bg-black/40 border border-gray-800 rounded-xl py-3 px-4 text-xs font-mono text-rose-400 focus:outline-none">
                    </div>
                </div>

                {{-- Parameters Info --}}
                <div class="pt-4 border-t border-gray-800/80">
                    <h4 class="text-[10px] font-black text-gray-300 uppercase tracking-wider mb-2">Query Parameters yang Didukung:</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-[11px] font-semibold">
                        <div class="bg-black/20 p-3 rounded-xl border border-gray-800/50">
                            <span class="text-teal-400 font-mono">stage</span>
                            <p class="text-[9px] text-gray-400 mt-1">Filter stage (e.g. `PRODUCTION`, `QC`, `GLOBAL`)</p>
                        </div>
                        <div class="bg-black/20 p-3 rounded-xl border border-gray-800/50">
                            <span class="text-teal-400 font-mono">search</span>
                            <p class="text-[9px] text-gray-400 mt-1">Cari berdasarkan No. SPK / Pelanggan</p>
                        </div>
                        <div class="bg-black/20 p-3 rounded-xl border border-gray-800/50">
                            <span class="text-teal-400 font-mono">sort_by</span>
                            <p class="text-[9px] text-gray-400 mt-1">Urutkan kolom (default: `days_overdue`)</p>
                        </div>
                        <div class="bg-black/20 p-3 rounded-xl border border-gray-800/50">
                            <span class="text-teal-400 font-mono">per_page</span>
                            <p class="text-[9px] text-gray-400 mt-1">Jumlah limit baris data (default: `25` data)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
