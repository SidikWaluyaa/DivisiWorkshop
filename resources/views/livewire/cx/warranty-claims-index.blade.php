@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* Premium Flatpickr CX Overrides - Teal & Emerald Gradient Theme */
        .flatpickr-calendar {
            background: rgba(255, 255, 255, 0.96) !important;
            backdrop-filter: blur(20px) !important;
            border: 1px solid rgba(241, 245, 249, 0.9) !important;
            border-radius: 24px !important;
            box-shadow: 0 30px 60px -15px rgba(15, 118, 110, 0.08), 0 10px 20px -5px rgba(0, 0, 0, 0.03) !important;
            padding: 8px 6px !important;
            font-family: inherit !important;
            width: 320px !important;
            box-sizing: border-box !important;
            animation: fpFadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .flatpickr-days, .dayContainer {
            width: 307.875px !important;
            min-width: 307.875px !important;
            max-width: 307.875px !important;
        }
        @keyframes fpFadeIn {
            from { opacity: 0; transform: scale(0.96) translateY(8px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .flatpickr-months {
            align-items: center !important;
            margin-bottom: 8px !important;
        }
        .flatpickr-months .flatpickr-prev-month, 
        .flatpickr-months .flatpickr-next-month {
            top: 15px !important;
            padding: 8px !important;
            border-radius: 12px !important;
            background: #f1f5f9 !important;
            color: #1e293b !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.2s ease !important;
        }
        .flatpickr-months .flatpickr-prev-month:hover, 
        .flatpickr-months .flatpickr-next-month:hover {
            background: #e2e8f0 !important;
            color: #14b8a6 !important;
            transform: scale(1.05);
        }
        .flatpickr-current-month {
            font-size: 13px !important;
            font-weight: 800 !important;
            color: #1e293b !important;
        }
        .flatpickr-current-month select {
            font-weight: 800 !important;
            color: #1e293b !important;
        }
        .flatpickr-weekday {
            font-weight: 800 !important;
            font-size: 9px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.1em !important;
            color: #94a3b8 !important;
        }
        .flatpickr-day {
            border-radius: 12px !important;
            font-weight: 700 !important;
            font-size: 11px !important;
            color: #334155 !important;
            margin: 2px 0 !important;
            transition: all 0.15s ease !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .flatpickr-day:hover {
            background: #f1f5f9 !important;
            color: #0f766e !important;
        }
        .flatpickr-day.today {
            border: 2px solid #14b8a6 !important;
            color: #0f766e !important;
        }
        .flatpickr-day.selected, 
        .flatpickr-day.startRange, 
        .flatpickr-day.endRange {
            background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%) !important;
            border-color: transparent !important;
            color: #ffffff !important;
            box-shadow: 0 10px 15px -3px rgba(20, 184, 166, 0.3) !important;
            border-radius: 12px !important;
        }
        .flatpickr-day.inRange {
            background: rgba(20, 184, 166, 0.08) !important;
            color: #0d9488 !important;
            border-radius: 0 !important;
            box-shadow: none !important;
        }
        .flatpickr-day.prevMonthDay, 
        .flatpickr-day.nextMonthDay {
            color: #cbd5e1 !important;
            opacity: 0.5 !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@endpush

<div class="min-h-screen bg-gray-50/80" style="background-image: radial-gradient(#e2e8f0 1px, transparent 1px); background-size: 20px 20px;">
<style>
    /* ── Premium Scrollbar ── */
    .inbox-scroll::-webkit-scrollbar { width: 4px; }
    .inbox-scroll::-webkit-scrollbar-track { background: transparent; }
    .inbox-scroll::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 99px; }
    .inbox-scroll::-webkit-scrollbar-thumb:hover { background: #9ca3af; }

    /* ── Claim card active glow ── */
    .claim-card-active {
        background: linear-gradient(135deg, #f0fdfa 0%, #ecfdf5 100%);
        border-left: 3px solid #14b8a6;
    }
    .claim-card-inactive {
        background: #ffffff;
        border-left: 3px solid transparent;
    }
    .claim-card-inactive:hover {
        background: linear-gradient(135deg, #f8fafc 0%, #f0fdfa 100%);
        border-left-color: #99f6e4;
    }

    /* ── Photo zoom ── */
    .photo-card { transition: transform 0.2s, box-shadow 0.2s; }
    .photo-card:hover { transform: scale(1.015); box-shadow: 0 12px 28px -8px rgba(0,0,0,0.18); }

    /* ── Slide-in animation ── */
    @keyframes workspace-in {
        from { opacity: 0; transform: translateX(16px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    .workspace-in { animation: workspace-in 0.35s cubic-bezier(0.22,1,0.36,1) both; }

    /* ── Section label ── */
    .section-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.65rem;
        font-weight: 900;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 0.625rem;
    }
    .section-label::after {
        content: '';
        flex: 1;
        height: 1px;
        background: linear-gradient(to right, #e2e8f0, transparent);
    }

    /* ── Status pill ── */
    .pill-pending  { background:#fef3c7; color:#92400e; border:1px solid #fde68a; }
    .pill-approved { background:#d1fae5; color:#065f46; border:1px solid #a7f3d0; }
    .pill-rejected { background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; }

    /* ── Decision CTA bar ── */
    .btn-reject {
        transition: all 0.2s;
        border: 1.5px solid #fca5a5;
        background: #fff;
        color: #dc2626;
        border-radius: 0.875rem;
        font-weight: 800;
        font-size: 0.75rem;
        padding: 0.875rem 1.5rem;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }
    .btn-reject:hover { background: #fef2f2; border-color: #ef4444; box-shadow: 0 4px 12px -4px rgba(239,68,68,0.25); }

    .btn-approve {
        transition: all 0.2s;
        background: linear-gradient(135deg, #0d9488, #059669);
        color: #fff;
        border-radius: 0.875rem;
        font-weight: 900;
        font-size: 0.75rem;
        padding: 0.875rem 2rem;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        box-shadow: 0 8px 20px -6px rgba(13,148,136,0.45);
    }
    .btn-approve:hover { background: linear-gradient(135deg, #0f766e, #047857); box-shadow: 0 8px 24px -4px rgba(13,148,136,0.5); transform: translateY(-1px); }
    .btn-approve:active { transform: translateY(0); }
</style>

    {{-- ══════════════ TOP HEADER BAR ══════════════ --}}
    <div class="px-6 pt-7 pb-5 max-w-[1440px] mx-auto">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                {{-- Icon badge --}}
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-teal-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-teal-200 shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-black text-gray-900 tracking-tight leading-tight">Inbox Klaim Garansi Mandiri</h1>
                    <p class="text-xs text-gray-500 mt-0.5">Verifikasi dan kelola pengajuan klaim garansi dari customer</p>
                </div>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                {{-- Pending badge --}}
                @if($pendingCount > 0)
                <div class="flex items-center gap-2 px-3 py-1.5 bg-amber-50 border border-amber-200 rounded-xl">
                    <span class="w-2 h-2 bg-amber-400 rounded-full animate-pulse"></span>
                    <span class="text-xs font-black text-amber-700">{{ $pendingCount }} Menunggu Review</span>
                </div>
                @endif

                <button wire:click="$refresh"
                        class="flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-xl text-xs font-bold shadow-sm transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Refresh
                </button>

                <a href="{{ route('cx.index') }}"
                   class="flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-xl text-xs font-bold shadow-sm transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke CX
                </a>
            </div>
        </div>
    </div>

    {{-- ══════════════ STATUS FILTER + SEARCH ══════════════ --}}
    <div class="px-6 pb-4 max-w-[1440px] mx-auto">
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-4 flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
            {{-- Filter tabs --}}
            <div class="flex gap-1.5 shrink-0 overflow-x-auto inbox-scroll pb-1 lg:pb-0">
                @php
                    $tabs = [
                        ['key' => 'PENDING',  'label' => 'Pending',   'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'activeClass' => 'bg-amber-500 text-white border-amber-500 shadow-amber-200'],
                        ['key' => 'APPROVED', 'label' => 'Disetujui', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'activeClass' => 'bg-emerald-500 text-white border-emerald-500 shadow-emerald-200'],
                        ['key' => 'REJECTED', 'label' => 'Ditolak',   'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z', 'activeClass' => 'bg-red-500 text-white border-red-500 shadow-red-200'],
                        ['key' => 'ALL',      'label' => 'Semua',     'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16', 'activeClass' => 'bg-gray-800 text-white border-gray-800 shadow-gray-200'],
                    ];
                @endphp
                @foreach($tabs as $tab)
                    <button wire:click="$set('statusFilter', '{{ $tab['key'] }}')"
                            class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl border text-[11px] font-black uppercase tracking-wide transition-all shadow-sm
                                   {{ $statusFilter === $tab['key'] ? $tab['activeClass'] . ' shadow-md' : 'bg-gray-50 text-gray-500 border-gray-200 hover:bg-gray-100' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}"/>
                        </svg>
                        {{ $tab['label'] }}
                        @if($tab['key'] === 'PENDING' && $pendingCount > 0)
                            <span class="ml-0.5 min-w-[18px] h-[18px] flex items-center justify-center rounded-full text-[9px] font-black
                                         {{ $statusFilter === 'PENDING' ? 'bg-white/30 text-white' : 'bg-amber-500 text-white' }}">
                                {{ $pendingCount }}
                            </span>
                        @endif
                    </button>
                @endforeach
            </div>

            <!-- Premium Single-Field Date Picker & Presets Group -->
            <div class="flex items-center gap-2 p-1 bg-gray-50/80 rounded-2xl border border-gray-200/80 flex-wrap lg:flex-nowrap relative shrink-0"
                 x-data="{
                     setPreset(type) {
                         let start = '';
                         let end = '';
                         const today = new Date();
                         
                         if (type === 'today') {
                             start = this.formatDate(today);
                             end = this.formatDate(today);
                         } else if (type === '7_days') {
                             const past = new Date();
                             past.setDate(today.getDate() - 6);
                             start = this.formatDate(past);
                             end = this.formatDate(today);
                         } else if (type === 'this_month') {
                             const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                             start = this.formatDate(firstDay);
                             end = this.formatDate(today);
                         }
                         
                         $wire.set('dateStart', start);
                         $wire.set('dateEnd', end);
                     },
                     formatDate(date) {
                         const y = date.getFullYear();
                         const m = String(date.getMonth() + 1).padStart(2, '0');
                         const d = String(date.getDate()).padStart(2, '0');
                         return `${y}-${m}-${d}`;
                     },
                     clearAll() {
                         $wire.set('dateStart', '');
                         $wire.set('dateEnd', '');
                     }
                 }">
                
                <!-- Quick Presets -->
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest pl-2.5 pr-1 shrink-0 hidden sm:inline-block">Filter Klaim:</span>
                <button type="button" @click="clearAll()" 
                        :class="!$wire.dateStart ? 'bg-white text-gray-900 shadow-sm border border-gray-100' : 'text-gray-400 hover:text-gray-600'"
                         class="px-3 py-1.5 rounded-xl text-[10px] font-black tracking-wide transition-all uppercase">
                    SEMUA
                </button>
                <button type="button" @click="setPreset('today')" 
                        :class="$wire.dateStart && $wire.dateStart === $wire.dateEnd ? 'bg-white text-gray-900 shadow-sm border border-gray-100' : 'text-gray-400 hover:text-gray-600'"
                        class="px-3 py-1.5 rounded-xl text-[10px] font-black tracking-wide transition-all uppercase">
                    HARI INI
                </button>
                <button type="button" @click="setPreset('7_days')" 
                        :class="$wire.dateStart && $wire.dateStart !== $wire.dateEnd && (new Date($wire.dateEnd) - new Date($wire.dateStart)) <= 7*24*60*60*1000 ? 'bg-white text-gray-900 shadow-sm border border-gray-100' : 'text-gray-400 hover:text-gray-600'"
                        class="px-3 py-1.5 rounded-xl text-[10px] font-black tracking-wide transition-all uppercase">
                    7 HARI
                </button>
                <button type="button" @click="setPreset('this_month')" 
                        :class="$wire.dateStart && $wire.dateStart !== $wire.dateEnd && new Date($wire.dateStart).getDate() === 1 ? 'bg-white text-gray-900 shadow-sm border border-gray-100' : 'text-gray-400 hover:text-gray-600'"
                        class="px-3 py-1.5 rounded-xl text-[10px] font-black tracking-wide transition-all uppercase">
                    BULAN INI
                </button>

                <div class="h-4 w-px bg-gray-200 mx-1 hidden sm:block"></div>

                <!-- Decoupled Flatpickr Calendar Button -->
                <div class="relative">
                    <button @click="$refs.rangeInput._flatpickr.open()" type="button"
                            :class="$wire.dateStart && $wire.dateStart !== $wire.dateEnd ? 'bg-teal-500 text-white shadow-md' : 'text-teal-600 hover:bg-teal-50 bg-white'"
                            class="px-4 py-1.5 rounded-xl text-[10px] font-black tracking-wide transition-all uppercase flex items-center justify-center gap-2 cursor-pointer w-44 text-center border-none outline-none focus:ring-0 shadow-sm">
                        <span>📅</span>
                        <span x-text="$wire.dateStart && $wire.dateEnd ? `${$wire.dateStart.substring(5)} - ${$wire.dateEnd.substring(5)}` : 'TANGGAL PENGAJUAN'"></span>
                    </button>

                    <!-- Hidden Input wrapper to isolate Flatpickr -->
                    <div wire:ignore wire:key="flatpickr-hidden-container" class="hidden">
                        <input x-init="
                            flatpickr($el, {
                                mode: 'range',
                                dateFormat: 'Y-m-d',
                                defaultDate: $wire.dateStart && $wire.dateEnd ? [$wire.dateStart, $wire.dateEnd] : null,
                                positionElement: $el.parentElement.previousElementSibling,
                                onChange: (selectedDates, dateStr, instance) => {
                                    if (selectedDates.length === 2) {
                                        let start = instance.formatDate(selectedDates[0], 'Y-m-d');
                                        let end = instance.formatDate(selectedDates[1], 'Y-m-d');
                                        $wire.set('dateStart', start);
                                        $wire.set('dateEnd', end);
                                    }
                                }
                            });
                            
                            $watch('$wire.dateStart', (value) => {
                                if ($el._flatpickr) {
                                    if (value) {
                                        $el._flatpickr.setDate([value, $wire.dateEnd], false);
                                    } else {
                                        $el._flatpickr.clear();
                                    }
                                }
                            });
                            $watch('$wire.dateEnd', (value) => {
                                if ($el._flatpickr && value && $wire.dateStart) {
                                    $el._flatpickr.setDate([$wire.dateStart, value], false);
                                }
                            });
                        " x-ref="rangeInput" type="text">
                    </div>
                </div>
            </div>

            {{-- Search --}}
            <div class="relative flex-1 min-w-0 w-full sm:w-auto">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 placeholder-gray-400 focus:ring-2 focus:ring-teal-400 focus:border-teal-400 focus:bg-white transition-all outline-none"
                       placeholder="Cari SPK, nama customer, nomor WhatsApp...">
            </div>
        </div>
    </div>

    {{-- ══════════════ FLASH MESSAGES ══════════════ --}}
    <div class="px-6 max-w-[1440px] mx-auto space-y-2">
        @if(session()->has('success'))
            <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm font-semibold shadow-sm">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session()->has('error'))
            <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl text-sm font-semibold shadow-sm">
                <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
        @endif
    </div>

    {{-- ══════════════ MAIN SPLIT PANEL ══════════════ --}}
    <div class="px-6 pb-8 pt-4 max-w-[1440px] mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

            {{-- ─────────────────────────────────────────
                 LEFT PANEL: Claim Queue (4 cols)
            ───────────────────────────────────────── --}}
            <div class="lg:col-span-4">
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">

                    {{-- Queue header --}}
                    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <span class="text-[11px] font-black uppercase text-gray-500 tracking-widest">Antrean Klaim</span>
                        </div>
                        <span class="px-2.5 py-0.5 bg-gray-100 text-gray-600 text-[11px] font-black rounded-full">
                            {{ $claims->total() }}
                        </span>
                    </div>

                    {{-- Claim list --}}
                    <div class="divide-y divide-gray-50/80 max-h-[calc(100vh-280px)] overflow-y-auto inbox-scroll">
                        @forelse($claims as $claim)
                            @php
                                $isActive = $selectedClaimId === $claim->id;
                                $statusDot = match($claim->status) {
                                    'PENDING'  => 'bg-amber-400',
                                    'APPROVED' => 'bg-emerald-400',
                                    'REJECTED' => 'bg-red-400',
                                    default    => 'bg-gray-300',
                                };
                                $statusPill = match($claim->status) {
                                    'PENDING'  => 'pill-pending',
                                    'APPROVED' => 'pill-approved',
                                    'REJECTED' => 'pill-rejected',
                                    default    => '',
                                };
                            @endphp
                            <div wire:click="selectClaim({{ $claim->id }})"
                                 class="p-4 cursor-pointer transition-all duration-200 {{ $isActive ? 'claim-card-active' : 'claim-card-inactive' }}">

                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-2 min-w-0">
                                        {{-- Status dot --}}
                                        <span class="shrink-0 w-2 h-2 rounded-full {{ $statusDot }} mt-1.5
                                                     {{ $claim->status === 'PENDING' ? 'animate-pulse' : '' }}"></span>
                                        {{-- SPK --}}
                                        <span class="font-mono text-[11px] font-black text-amber-700 bg-amber-50 px-2 py-0.5 rounded-lg border border-amber-100 truncate">
                                            {{ $claim->spk_number }}
                                        </span>
                                    </div>
                                    <span class="text-[10px] text-gray-400 font-medium shrink-0 mt-1 flex flex-col items-end gap-0.5">
                                        <span class="font-black text-gray-700 text-[10px]">{{ $claim->created_at->translatedFormat('d M Y') }}</span>
                                        <span class="text-[9px] text-gray-400 font-medium">{{ $claim->created_at->translatedFormat('H:i') }} ({{ $claim->created_at->diffForHumans() }})</span>
                                    </span>
                                </div>

                                <div class="mt-2.5 pl-4">
                                    <p class="font-bold text-gray-900 text-sm leading-snug">{{ $claim->customer_name }}</p>
                                    <p class="text-[11px] text-gray-400 mt-0.5">{{ $claim->customer_phone }}</p>
                                </div>

                                <div class="mt-2.5 pl-4 flex items-center justify-between">
                                    <span class="text-[11px] text-gray-500 font-medium flex items-center gap-1">
                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                        {{ $claim->workOrder->shoe_brand ?? '-' }}
                                        @if($claim->workOrder->shoe_type ?? null)
                                            · {{ $claim->workOrder->shoe_type }}
                                        @endif
                                    </span>
                                    <span class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wide {{ $statusPill }}">
                                        {{ $claim->status }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="py-16 text-center">
                                <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                </div>
                                <p class="text-sm font-bold text-gray-400">Tidak ada klaim</p>
                                <p class="text-[11px] text-gray-300 mt-1">Belum ada pengajuan di kategori ini</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Pagination --}}
                    @if($claims->hasPages())
                        <div class="px-4 py-3 border-t border-gray-100 bg-gray-50/40">
                            {{ $claims->links() }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- ─────────────────────────────────────────
                 RIGHT PANEL: Workspace (8 cols)
            ───────────────────────────────────────── --}}
            <div class="lg:col-span-8">
                @if($selectedClaim)

                    {{-- ── WORKSPACE CARD ── --}}
                    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden workspace-in">

                        {{-- ── Workspace Top Header (colored by status) ── --}}
                        @php
                            $headerGradient = match($selectedClaim->status) {
                                'PENDING'  => 'from-amber-500 to-orange-500',
                                'APPROVED' => 'from-teal-500 to-emerald-500',
                                'REJECTED' => 'from-red-500 to-rose-500',
                                default    => 'from-gray-500 to-gray-600',
                            };
                            $statusLabel = match($selectedClaim->status) {
                                'PENDING'  => '⏳ Menunggu Keputusan',
                                'APPROVED' => '✅ Klaim Disetujui',
                                'REJECTED' => '❌ Klaim Ditolak',
                                default    => $selectedClaim->status,
                            };
                        @endphp
                        <div class="bg-gradient-to-r {{ $headerGradient }} px-6 py-4 flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-2.5 mb-1">
                                    <span class="font-mono text-white/80 text-xs font-bold bg-white/15 px-2.5 py-1 rounded-lg backdrop-blur-sm">
                                        {{ $selectedClaim->spk_number }}
                                    </span>
                                    <span class="text-white/70 text-[10px]">·</span>
                                    <span class="text-white/70 text-[10px] font-medium">
                                        {{ $selectedClaim->created_at->translatedFormat('d M Y, H:i') }}
                                    </span>
                                </div>
                                <h2 class="text-white font-black text-lg tracking-tight">Lembar Verifikasi Klaim</h2>
                            </div>
                            <span class="px-3.5 py-1.5 bg-white/20 text-white text-[11px] font-black uppercase tracking-widest rounded-xl backdrop-blur-sm border border-white/20
                                         {{ $selectedClaim->status === 'PENDING' ? 'animate-pulse' : '' }}">
                                {{ $statusLabel }}
                            </span>
                        </div>

                        <div class="p-6 space-y-6">

                            {{-- ── SECTION 1: Customer Info ── --}}
                            <div>
                                <div class="section-label">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Informasi Customer
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="bg-gray-50 rounded-xl p-3.5 border border-gray-100">
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Nama Customer</p>
                                        <p class="font-bold text-gray-900 text-sm">{{ $selectedClaim->customer_name }}</p>
                                    </div>
                                    <div class="bg-gray-50 rounded-xl p-3.5 border border-gray-100">
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">WhatsApp / Telepon</p>
                                        <div x-data="{ copied: false }" class="flex items-center justify-between gap-1.5">
                                            <div class="flex items-center gap-1.5 min-w-0">
                                                <svg class="w-3.5 h-3.5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                                <span class="font-mono font-bold text-gray-900 text-xs sm:text-sm select-all truncate">{{ $selectedClaim->customer_phone }}</span>
                                            </div>
                                            <button @click="navigator.clipboard.writeText('{{ $selectedClaim->customer_phone }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                                    class="shrink-0 px-2 py-1 bg-white hover:bg-gray-100 border border-gray-200 rounded-lg text-gray-400 hover:text-gray-600 transition-all flex items-center gap-1 text-[10px] font-bold"
                                                    :class="copied ? 'text-emerald-600 border-emerald-200 bg-emerald-50 hover:bg-emerald-50 hover:text-emerald-600 shadow-sm' : ''"
                                                    title="Salin nomor">
                                                <span x-show="!copied" class="flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                                    Salin
                                                </span>
                                                <span x-show="copied" x-cloak class="flex items-center gap-1 text-emerald-600">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                    Tersalin!
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 rounded-xl p-3.5 border border-gray-100 col-span-2">
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Penggunaan Sepatu</p>
                                        <p class="font-bold text-gray-900 text-sm flex items-center gap-2">
                                            <span class="text-base">🏷️</span>
                                            <span>{{ $selectedClaim->penggunaan ?: '-' }}</span>
                                        </p>
                                    </div>
                                    <div class="bg-gray-50 rounded-xl p-3.5 border border-gray-100 col-span-2">
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Tanggal Pengajuan</p>
                                        <p class="font-bold text-gray-900 text-sm flex items-center gap-2">
                                            <span class="text-base">📅</span>
                                            <span>{{ $selectedClaim->created_at->translatedFormat('l, d M Y · H:i') }} WIB</span>
                                        </p>
                                    </div>
                                </div>

                                {{-- Problem description --}}
                                <div class="mt-3 bg-blue-50/60 border border-blue-100 rounded-xl p-4">
                                    <p class="text-[9px] font-black text-blue-600 uppercase tracking-widest mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                        Keluhan Customer
                                    </p>
                                    <p class="text-sm text-gray-700 font-medium leading-relaxed italic">
                                        "{{ $selectedClaim->problem_description }}"
                                    </p>
                                </div>
                            </div>

                            {{-- ── SECTION 2: Evidence Photos ── --}}
                            <div>
                                <div class="section-label">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Bukti Foto Upload
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                                    {{-- Foto Kerusakan --}}
                                    <div x-data="{ 
                                        activeImg: '{{ addslashes(asset(is_array($selectedClaim->problem_photo) ? ($selectedClaim->problem_photo[0] ?? '') : $selectedClaim->problem_photo)) }}',
                                        images: {{ json_encode(array_map(fn($p) => asset($p), is_array($selectedClaim->problem_photo) ? $selectedClaim->problem_photo : ($selectedClaim->problem_photo ? [$selectedClaim->problem_photo] : []))) }}
                                    }">
                                        <p class="text-[10px] font-black text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-1">
                                            <span class="w-4 h-4 bg-red-500 text-white rounded-full flex items-center justify-center text-[8px] font-black shrink-0">!</span>
                                            Foto Kerusakan Sepatu ({{ is_array($selectedClaim->problem_photo) ? count($selectedClaim->problem_photo) : 1 }} Foto)
                                        </p>
                                        <a :href="activeImg" target="_blank"
                                           class="photo-card block aspect-video rounded-xl overflow-hidden border border-gray-200 shadow-sm relative group bg-gray-50">
                                            <img :src="activeImg"
                                                 class="w-full h-full object-cover transition-all duration-300 group-hover:scale-105"
                                                 alt="Foto kerusakan sepatu"
                                                 onerror="this.closest('a').innerHTML='<div class=\'w-full h-full flex flex-col items-center justify-center bg-red-50 text-red-400 gap-2\'><svg class=\'w-8 h-8\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z\'/></svg><span class=\'text-[10px] font-bold\'>Foto tidak ditemukan</span></div>'">
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-center pb-3">
                                                <span class="text-[11px] text-white font-black bg-black/50 px-3 py-1.5 rounded-full flex items-center gap-1.5 backdrop-blur-sm">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                    Buka di Tab Baru
                                                </span>
                                            </div>
                                        </a>

                                        <!-- Thumbnails Selector -->
                                        <template x-if="images.length > 1">
                                            <div class="flex flex-wrap gap-1.5 mt-2">
                                                <template x-for="(img, idx) in images" :key="idx">
                                                    <button type="button" @click="activeImg = img"
                                                            :class="activeImg === img ? 'ring-2 ring-teal-500 scale-95 border-transparent' : 'opacity-70 hover:opacity-100 hover:scale-95 border-gray-200'"
                                                            class="w-12 h-9 rounded-lg border overflow-hidden transition-all shrink-0 bg-gray-50 focus:outline-none">
                                                        <img :src="img" class="w-full h-full object-cover">
                                                    </button>
                                                </template>
                                            </div>
                                        </template>
                                    </div>

                                    {{-- Foto Google Review --}}
                                    <div>
                                        <p class="text-[10px] font-black text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-yellow-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            Bukti Google Review
                                        </p>
                                        <a href="{{ asset($selectedClaim->google_review_photo) }}" target="_blank"
                                           class="photo-card block aspect-video rounded-xl overflow-hidden border border-gray-200 shadow-sm relative group bg-gray-50">
                                            <img src="{{ asset($selectedClaim->google_review_photo) }}"
                                                 class="w-full h-full object-cover"
                                                 alt="Foto Google Review"
                                                 onerror="this.closest('a').innerHTML='<div class=\'w-full h-full flex flex-col items-center justify-center bg-red-50 text-red-400 gap-2\'><svg class=\'w-8 h-8\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z\'/></svg><span class=\'text-[10px] font-bold\'>Foto tidak ditemukan</span></div>'">
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-center pb-3">
                                                <span class="text-[11px] text-white font-black bg-black/50 px-3 py-1.5 rounded-full flex items-center gap-1.5 backdrop-blur-sm">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                    Buka di Tab Baru
                                                </span>
                                            </div>
                                        </a>
                                    </div>

                                </div>
                            </div>

                            {{-- ── SECTION 3: Order Context ── --}}
                            <div>
                                <div class="section-label">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    Data Teknis Pengerjaan Sebelumnya
                                </div>

                                <div class="grid grid-cols-3 gap-3 text-xs">
                                    {{-- Shoe Info --}}
                                    <div class="bg-gray-50 rounded-xl p-3.5 border border-gray-100">
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-wider mb-2">Informasi Sepatu</p>
                                        <p class="font-black text-gray-900 text-sm">{{ $selectedClaim->workOrder->shoe_brand ?? '-' }}</p>
                                        <p class="text-gray-500 text-[11px] mt-0.5">{{ $selectedClaim->workOrder->shoe_type ?? '-' }}</p>
                                        <p class="text-gray-400 text-[11px]">{{ $selectedClaim->workOrder->shoe_color ?? '-' }}
                                            @if($selectedClaim->workOrder->shoe_size) · Size {{ $selectedClaim->workOrder->shoe_size }}@endif
                                        </p>
                                    </div>

                                    {{-- Warranty --}}
                                    <div class="bg-emerald-50 rounded-xl p-3.5 border border-emerald-100">
                                        <p class="text-[9px] font-black text-emerald-600 uppercase tracking-wider mb-2">Masa Garansi SPK</p>
                                        <p class="font-black text-gray-900 text-sm">{{ $selectedClaim->workOrder->warranty_duration_months ?? 0 }} Bulan</p>
                                        <p class="text-emerald-600 font-bold text-[11px] mt-0.5">
                                            Hingga {{ $selectedClaim->workOrder->warranty_expires_at?->format('d M Y') ?? '-' }}
                                        </p>
                                    </div>

                                    {{-- Services --}}
                                    <div class="bg-gray-50 rounded-xl p-3.5 border border-gray-100">
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-wider mb-2">Layanan Diambil</p>
                                        <div class="space-y-1 max-h-[80px] overflow-y-auto inbox-scroll">
                                            @foreach($selectedClaim->workOrder->workOrderServices ?? [] as $svc)
                                                <div class="text-[11px] text-gray-700 font-medium flex items-start gap-1">
                                                    <span class="text-teal-500 shrink-0 font-black">·</span>
                                                    {{ $svc->custom_service_name ?? ($svc->service->name ?? '-') }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                {{-- Baseline Photos --}}
                                @if($selectedClaim->workOrder->photos->isNotEmpty())
                                    <div class="mt-3">
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                            Foto Baseline Awal Masuk (Pembanding)
                                        </p>
                                        <div class="flex gap-2 overflow-x-auto pb-1 inbox-scroll">
                                            @foreach($selectedClaim->workOrder->photos as $ph)
                                                <a href="{{ $ph->photo_url }}" target="_blank"
                                                   class="block flex-shrink-0 hover:scale-105 transition-all rounded-xl overflow-hidden border border-gray-200 shadow-sm">
                                                    <img src="{{ $ph->photo_url }}"
                                                         class="w-20 h-20 object-cover"
                                                         onerror="this.style.display='none'">
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- ── SECTION 4: Decision Bar ── --}}
                            <div class="pt-2">
                                @if($selectedClaim->status === 'PENDING')
                                    <div class="bg-amber-50 border border-amber-100 rounded-2xl p-5">
                                        <p class="text-[10px] font-black text-amber-700 uppercase tracking-widest mb-4 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Keputusan CX — Tindakan Diperlukan
                                        </p>
                                        <div class="flex items-center justify-between gap-3">
                                            <button type="button" wire:click="openRejectModal" class="btn-reject">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    Tolak Klaim
                                                </div>
                                            </button>

                                            <button type="button"
                                                    wire:click="approveClaim({{ $selectedClaim->id }})"
                                                    wire:loading.attr="disabled"
                                                    class="btn-approve flex items-center gap-2.5">
                                                <span wire:loading.remove wire:target="approveClaim" class="flex items-center gap-2.5">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                    Setujui & Buat SPK Rework
                                                </span>
                                                <span wire:loading wire:target="approveClaim" class="flex items-center gap-2">
                                                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    Memproses...
                                                </span>
                                            </button>
                                        </div>
                                    </div>

                                @elseif($selectedClaim->status === 'APPROVED')
                                    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 bg-emerald-500 rounded-xl flex items-center justify-center shrink-0">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            </div>
                                            <div>
                                                <p class="text-xs font-black text-emerald-800">Klaim Garansi Disetujui</p>
                                                <p class="text-[11px] text-emerald-600 mt-0.5">
                                                    oleh <strong>{{ $selectedClaim->processor->name ?? 'System' }}</strong>
                                                    · {{ $selectedClaim->processed_at?->translatedFormat('d M Y, H:i') }}
                                                </p>
                                            </div>
                                        </div>
                                        <span class="px-3 py-1.5 bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase tracking-widest rounded-xl border border-emerald-200">APPROVED</span>
                                    </div>

                                @elseif($selectedClaim->status === 'REJECTED')
                                    <div class="bg-red-50 border border-red-200 rounded-2xl p-4 space-y-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 bg-red-500 rounded-xl flex items-center justify-center shrink-0">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </div>
                                            <div>
                                                <p class="text-xs font-black text-red-800">Klaim Garansi Ditolak</p>
                                                <p class="text-[11px] text-red-600 mt-0.5">
                                                    oleh <strong>{{ $selectedClaim->processor->name ?? 'System' }}</strong>
                                                    · {{ $selectedClaim->processed_at?->translatedFormat('d M Y, H:i') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="bg-red-100/60 border border-red-200/80 rounded-xl p-3 text-xs text-red-800 font-medium">
                                            <span class="font-black text-red-700 uppercase text-[9px] tracking-widest block mb-1">Alasan Penolakan:</span>
                                            "{{ $selectedClaim->rejection_reason }}"
                                        </div>
                                    </div>
                                @endif
                            </div>

                        </div>{{-- /p-6 --}}
                    </div>{{-- /workspace card --}}

                @else
                    {{-- Empty state --}}
                    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm flex flex-col items-center justify-center py-28 text-center">
                        <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-50 rounded-3xl flex items-center justify-center mb-5 shadow-inner">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>
                        </div>
                        <p class="font-black text-gray-500 text-base">Pilih Klaim untuk Ditinjau</p>
                        <p class="text-sm text-gray-400 mt-2 max-w-xs leading-relaxed">
                            Klik salah satu pengajuan di panel kiri untuk memulai proses verifikasi.
                        </p>
                        @if($pendingCount > 0)
                            <div class="mt-5 flex items-center gap-2 px-4 py-2.5 bg-amber-50 border border-amber-200 rounded-xl">
                                <span class="w-2 h-2 bg-amber-400 rounded-full animate-pulse"></span>
                                <span class="text-xs font-black text-amber-700">{{ $pendingCount }} klaim menunggu keputusan Anda</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- ══════════════ REJECTION MODAL ══════════════ --}}
    @if($showRejectModal)
        <div class="fixed inset-0 z-[200] flex items-center justify-center p-4">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm"
                 wire:click="$set('showRejectModal', false)"></div>

            {{-- Modal --}}
            <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden border border-gray-100 z-10">

                {{-- Header --}}
                <div class="bg-gradient-to-r from-red-500 to-rose-500 px-6 py-5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        </div>
                        <div>
                            <h3 class="text-white font-black text-base tracking-tight">Form Penolakan Klaim</h3>
                            <p class="text-white/70 text-[10px] font-medium">Alasan wajib diisi dan akan dikirim ke customer</p>
                        </div>
                    </div>
                    <button wire:click="$set('showRejectModal', false)"
                            class="w-8 h-8 bg-white/20 hover:bg-white/30 text-white rounded-lg flex items-center justify-center transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="px-6 py-6 space-y-5">
                    {{-- Category --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">
                            1 · Pilih Kategori Alasan
                        </label>
                        <select wire:model="selectedRejectionReasonType"
                                class="w-full bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3 text-sm font-bold text-gray-700 focus:ring-2 focus:ring-red-400 focus:border-red-400 transition-all outline-none appearance-none">
                            <option value="Masa Garansi Habis">Masa Garansi Habis</option>
                            <option value="Kerusakan Akibat Kelalaian Penggunaan">Kelalaian Pengguna / Human Error</option>
                            <option value="Bukti Google Review Tidak Sesuai">Bukti Google Review Tidak Sesuai / Palsu</option>
                            <option value="Foto Masalah Kurang Jelas">Bukti Foto Tidak Jelas / Blur</option>
                            <option value="Lainnya">Alasan Lainnya (Tulis Manual)</option>
                        </select>
                    </div>

                    {{-- Note --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">
                            2 · Catatan Tambahan <span class="normal-case text-gray-400 font-medium">(opsional)</span>
                        </label>
                        <textarea wire:model="customRejectionNote"
                                  rows="3"
                                  class="w-full bg-gray-50 border-2 border-gray-200 rounded-xl p-4 text-gray-700 text-sm font-medium focus:ring-2 focus:ring-red-400 focus:border-red-400 transition-all outline-none resize-none placeholder-gray-400 leading-relaxed"
                                  placeholder="Tulis rincian penolakan yang akan dikirimkan sebagai feedback ke customer..."></textarea>
                    </div>

                    {{-- Warning note --}}
                    <div class="bg-amber-50 border border-amber-100 rounded-xl p-3.5 flex gap-2.5">
                        <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <p class="text-[11px] text-amber-700 leading-relaxed">Keputusan penolakan <strong>tidak dapat dibatalkan</strong> setelah dikonfirmasi. Pastikan alasan sudah benar sebelum melanjutkan.</p>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between gap-3">
                    <button wire:click="$set('showRejectModal', false)"
                            class="px-5 py-2.5 text-xs font-bold text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-all">
                        Batalkan
                    </button>
                    <button wire:click="rejectClaim"
                            wire:loading.attr="disabled"
                            class="flex items-center gap-2.5 px-7 py-3 bg-gradient-to-r from-red-500 to-rose-500 hover:from-red-600 hover:to-rose-600 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-red-200 transition-all">
                        <span wire:loading.remove wire:target="rejectClaim" class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            Konfirmasi Penolakan
                        </span>
                        <span wire:loading wire:target="rejectClaim" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memproses...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
