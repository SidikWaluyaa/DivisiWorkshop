<x-app-layout>
    <style>
        /* ── Premium Dot Grid & Variables ── */
        .premium-bg {
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 20px 20px;
        }
        .dark .premium-bg {
            background-image: radial-gradient(#334155 1.2px, transparent 1.2px);
        }

        /* ── Card hover glow effects ── */
        .glow-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        .glow-card::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            opacity: 0;
            transition: opacity 0.3s;
            pointer-events: none;
            box-shadow: 0 0 24px var(--glow-color, rgba(16, 185, 129, 0.15));
        }
        .glow-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 20px -8px rgba(0, 0, 0, 0.08);
        }
        .glow-card:hover::after {
            opacity: 1;
        }

        /* ── Custom Scrollbar ── */
        .table-scroll::-webkit-scrollbar {
            height: 6px;
            width: 6px;
        }
        .table-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .table-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        .dark .table-scroll::-webkit-scrollbar-thumb {
            background: #475569;
        }

        /* ── Active Segment Indicator ── */
        .segment-tab {
            position: relative;
            transition: all 0.25s;
        }
        .segment-tab::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background-color: #10b981;
            border-radius: 9999px;
            transition: width 0.25s;
        }
        .segment-tab.active::after {
            width: 60%;
        }

        /* ── Pulse animation for warning ── */
        @keyframes pulse-amber {
            0%, 100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4); }
            50% { box-shadow: 0 0 0 6px rgba(245, 158, 11, 0); }
        }
        .pulse-amber-badge {
            animation: pulse-amber 2s infinite;
        }
    </style>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                {{-- Premium Gradient Shield Icon --}}
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-100 dark:shadow-none shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-black text-xl text-gray-900 dark:text-white tracking-tight leading-none">List Garansi Mandiri</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5 font-medium">Monitoring masa berlaku garansi dan rekap pengerjaan workshop</p>
                </div>
            </div>
            <a href="{{ route('finish.index') }}" 
               style="color: #1e293b !important;"
               class="flex items-center justify-center gap-2 px-4 py-2.5 bg-white hover:bg-slate-50 border border-gray-200 rounded-xl text-xs font-black uppercase tracking-wider shadow-sm transition-all shrink-0">
                <svg class="w-3.5 h-3.5" style="stroke: #1e293b !important;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Finish
            </a>
        </div>
    </x-slot>

    <div class="py-6 min-h-screen bg-slate-50/50 dark:bg-slate-900/40 premium-bg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- ══════════════ STATS GRID (Big 4 Inspired) ══════════════ --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                
                {{-- Total Garansi --}}
                <div class="glow-card bg-white dark:bg-gray-800 rounded-2xl border border-gray-200/80 dark:border-gray-700 p-5 flex items-center gap-4 hover:border-indigo-400 dark:hover:border-indigo-500" style="--glow-color: rgba(99, 102, 241, 0.15)">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-indigo-500 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">{{ $stats['total'] }}</div>
                        <div class="text-[10px] text-gray-400 dark:text-gray-500 font-extrabold uppercase tracking-widest mt-0.5">Total Garansi</div>
                    </div>
                </div>

                {{-- Masih Aktif --}}
                <div class="glow-card bg-white dark:bg-gray-800 rounded-2xl border border-gray-200/80 dark:border-gray-700 p-5 flex items-center gap-4 hover:border-emerald-400 dark:hover:border-emerald-500" style="--glow-color: rgba(16, 185, 129, 0.15)">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight">{{ $stats['active'] }}</div>
                        <div class="text-[10px] text-emerald-600 dark:text-emerald-500/80 font-extrabold uppercase tracking-widest mt-0.5">Masih Aktif</div>
                    </div>
                </div>

                {{-- Expired --}}
                <div class="glow-card bg-white dark:bg-gray-800 rounded-2xl border border-gray-200/80 dark:border-gray-700 p-5 flex items-center gap-4 hover:border-rose-400 dark:hover:border-rose-500" style="--glow-color: rgba(244, 63, 94, 0.15)">
                    <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-950/40 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-rose-500 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-rose-600 dark:text-rose-400 tracking-tight">{{ $stats['expired'] }}</div>
                        <div class="text-[10px] text-rose-500 dark:text-rose-500/80 font-extrabold uppercase tracking-widest mt-0.5">Sudah Expired</div>
                    </div>
                </div>

                {{-- Berakhir ≤7 hari --}}
                <div class="glow-card bg-white dark:bg-gray-800 rounded-2xl border border-gray-200/80 dark:border-gray-700 p-5 flex items-center gap-4 hover:border-amber-400 dark:hover:border-amber-500" style="--glow-color: rgba(245, 158, 11, 0.15)">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/40 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-amber-600 dark:text-amber-400 tracking-tight">{{ $stats['soon'] }}</div>
                        <div class="text-[10px] text-amber-600 dark:text-amber-500/80 font-extrabold uppercase tracking-widest mt-0.5">Berakhir ≤7 Hari</div>
                    </div>
                </div>
            </div>

            {{-- ══════════════ FILTER & SEARCH (Modern Segment Bar) ══════════════ --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200/80 dark:border-gray-700 p-4 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4 shadow-sm">
                
                {{-- Custom Segment Control --}}
                <div class="flex items-center bg-gray-50 dark:bg-gray-900 rounded-xl p-1 shrink-0 border border-gray-100 dark:border-gray-800">
                    @foreach(['active' => '🟢 Aktif', 'expired' => '🔴 Expired', 'all' => '📋 Semua'] as $val => $label)
                        @php $isActiveTab = $filter === $val; @endphp
                        <a href="{{ route('finish.list-garansi', ['filter' => $val, 'search' => $search]) }}"
                           class="flex-1 text-center px-4 py-2 text-xs font-black uppercase tracking-wide rounded-lg transition-all
                                  {{ $isActiveTab 
                                      ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm border border-gray-200/50 dark:border-gray-700' 
                                      : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                {{-- Interactive Search Form --}}
                <form method="GET" action="{{ route('finish.list-garansi') }}" class="flex-1 flex flex-col sm:flex-row items-stretch gap-2.5">
                    <input type="hidden" name="filter" value="{{ $filter }}">
                    
                    {{-- Glowing Search Input --}}
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}" 
                               placeholder="Cari nomor SPK, nama customer, nomor HP..."
                               class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-xl text-sm font-semibold outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 focus:bg-white dark:focus:bg-gray-900 transition-all placeholder-gray-400 dark:placeholder-gray-600">
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" 
                                class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-md shadow-emerald-100 dark:shadow-none flex items-center justify-center gap-1.5 shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Cari
                        </button>
                        @if($search)
                            <a href="{{ route('finish.list-garansi', ['filter' => $filter]) }}" 
                               class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-xs font-black uppercase tracking-widest rounded-xl transition-all flex items-center justify-center">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- ══════════════ MAIN LIST/TABLE ══════════════ --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200/80 dark:border-gray-700 overflow-hidden shadow-sm">
                
                {{-- Table Header Info Band --}}
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700/80 bg-gray-50/50 dark:bg-gray-900/30 flex items-center justify-between">
                    <h3 class="font-extrabold text-sm text-gray-800 dark:text-gray-200 flex items-center gap-2 uppercase tracking-wider">
                        📋 Daftar Garansi Aktif
                        <span class="px-2.5 py-0.5 bg-gray-200/60 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-[10px] font-black rounded-full font-sans">
                            {{ $orders->total() }} Order
                        </span>
                    </h3>
                </div>

                @if($orders->isEmpty())
                    {{-- Elegant Empty State --}}
                    <div class="flex flex-col items-center justify-center py-20 px-4 text-center">
                        <div class="w-16 h-16 bg-gray-50 dark:bg-gray-900 rounded-2xl flex items-center justify-center mb-4 border border-gray-100 dark:border-gray-800">
                            <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <p class="text-sm font-black text-gray-700 dark:text-gray-300">Tidak ada data garansi ditemukan</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 max-w-xs leading-relaxed">Garansi akan muncul setelah proses pengerjaan dinyatakan selesai dan dikonfirmasi diambil.</p>
                    </div>
                @else
                    {{-- Responsive Table Wrap --}}
                    <div class="overflow-x-auto table-scroll">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-[10px] font-black uppercase text-gray-400 dark:text-gray-500 tracking-wider bg-gray-50/50 dark:bg-gray-900/20 border-b border-gray-100 dark:border-gray-700/80">
                                    <th class="px-6 py-4">SPK & Pemesan</th>
                                    <th class="px-6 py-4">Informasi Sepatu</th>
                                    <th class="px-6 py-4">Detail Layanan</th>
                                    <th class="px-6 py-4 text-center">Tgl Ambil</th>
                                    <th class="px-6 py-4 text-center">Garansi</th>
                                    <th class="px-6 py-4 text-center">Status & Durasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/80">
                                @foreach($orders as $order)
                                    @php
                                        $now = now();
                                        $isExpired = $order->warranty_expires_at < $now;
                                        $isSoon = !$isExpired && $order->warranty_expires_at->diffInDays($now) <= 7;
                                        $sisaHari = $isExpired ? 0 : (int) $now->diffInDays($order->warranty_expires_at, false);
                                        
                                        // Visual Progress Bar Calculation
                                        $startDate = \Carbon\Carbon::parse($order->taken_date ?? $order->stored_at ?? $order->finished_date ?? now());
                                        $totalDays = max(1, $startDate->diffInDays($order->warranty_expires_at));
                                        $progressPercent = $isExpired ? 0 : min(100, max(0, ($sisaHari / $totalDays) * 100));
                                    @endphp
                                    <tr class="hover:bg-slate-50/60 dark:hover:bg-gray-700/20 transition-all duration-150 {{ $isExpired ? 'opacity-65' : '' }}">
                                        
                                        {{-- 1. SPK & Pemesan --}}
                                        <td class="px-6 py-4.5">
                                            <a href="{{ route('finish.show', $order->id) }}" 
                                               class="group flex items-center gap-1.5 font-mono font-black text-xs text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/30 px-2.5 py-1 rounded-lg border border-amber-100 dark:border-amber-900/50 w-fit hover:bg-amber-100 dark:hover:bg-amber-900/50 transition-colors">
                                                {{ $order->spk_number }}
                                                <svg class="w-3 h-3 text-amber-500 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                            </a>
                                            <div class="text-sm font-black text-gray-900 dark:text-white mt-2">{{ $order->customer_name }}</div>
                                            <div x-data="{ copied: false }" class="flex items-center gap-1 mt-1">
                                                <span class="text-xs text-gray-400 font-mono select-all">{{ $order->customer_phone }}</span>
                                                <button @click="navigator.clipboard.writeText('{{ $order->customer_phone }}'); copied = true; setTimeout(() => copied = false, 1500)"
                                                        class="text-gray-300 hover:text-gray-500 dark:text-gray-600 dark:hover:text-gray-400 transition-colors"
                                                        title="Salin nomor HP">
                                                    <svg x-show="!copied" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2"/></svg>
                                                    <svg x-show="copied" x-cloak class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                </button>
                                            </div>
                                        </td>
 
                                        {{-- 2. Info Sepatu --}}
                                        <td class="px-6 py-4.5">
                                            <div class="text-sm font-black text-gray-950 dark:text-gray-200">{{ $order->shoe_brand }}</div>
                                            <div class="text-xs text-gray-500 mt-0.5">{{ $order->shoe_type }} · {{ $order->shoe_color }}</div>
                                            @if($order->shoe_size)
                                                <span class="inline-block mt-1.5 px-2 py-0.5 bg-gray-100 dark:bg-gray-700/80 text-gray-500 dark:text-gray-400 text-[10px] font-bold rounded-md">Size: {{ $order->shoe_size }}</span>
                                            @endif
                                        </td>
 
                                        {{-- 3. Detail Layanan --}}
                                        <td class="px-6 py-4.5">
                                            <div class="space-y-1 max-h-[85px] overflow-y-auto table-scroll">
                                                @foreach($order->workOrderServices->take(3) as $svc)
                                                    <div class="text-xs text-gray-700 dark:text-gray-300 font-medium flex items-start gap-1">
                                                        <span class="text-emerald-500 shrink-0 mt-0.5">•</span>
                                                        <span>{{ $svc->custom_service_name ?? ($svc->service->name ?? '-') }}</span>
                                                    </div>
                                                @endforeach
                                                @if($order->workOrderServices->count() > 3)
                                                    <div class="text-[10px] text-gray-400 font-bold pl-2.5">+{{ $order->workOrderServices->count() - 3 }} Layanan Lainnya</div>
                                                @endif
                                            </div>
                                        </td>
 
                                        {{-- 4. Tanggal Ambil --}}
                                        <td class="px-6 py-4.5 text-center shrink-0">
                                            @if($order->taken_date)
                                                <div class="text-xs font-black text-gray-800 dark:text-gray-300">
                                                    {{ $order->taken_date->format('d M Y') }}
                                                </div>
                                                <div class="text-[10px] text-gray-400 font-bold mt-0.5">{{ $order->taken_date->format('H:i') }} WIB</div>
                                            @else
                                                <div class="flex flex-col items-center gap-1.5">
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 dark:bg-gray-700 text-slate-500 dark:text-gray-400 rounded-xl text-[10px] font-black uppercase tracking-wider border border-slate-200 dark:border-gray-600">
                                                        📦 Di Rak Gudang
                                                    </span>
                                                    @if($order->finished_date)
                                                        <div class="text-[10px] text-gray-400 dark:text-gray-500 font-bold">
                                                            Selesai: <span class="text-gray-600 dark:text-gray-450">{{ $order->finished_date->format('d M Y') }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>

                                        {{-- 5. Garansi (Progress visual) --}}
                                        <td class="px-6 py-4.5 min-w-[160px]">
                                            <div class="flex items-center justify-between text-xs font-black">
                                                <span class="text-gray-800 dark:text-gray-200">{{ $order->warranty_duration_months }} Bulan</span>
                                                <span class="text-gray-400">Hingga {{ $order->warranty_expires_at->format('d M Y') }}</span>
                                            </div>
                                            
                                            {{-- Visual Progress Track --}}
                                            <div class="w-full bg-gray-100 dark:bg-gray-700/60 h-1.5 rounded-full overflow-hidden mt-1.5 border border-gray-200/20">
                                                <div class="h-full rounded-full transition-all duration-700 
                                                            {{ $isExpired ? 'bg-red-400' : ($isSoon ? 'bg-amber-400 pulse-amber-badge' : 'bg-gradient-to-r from-emerald-400 to-teal-500') }}" 
                                                     style="width: {{ $progressPercent }}%"></div>
                                            </div>

                                            <div class="text-[10px] font-bold text-gray-400 dark:text-gray-500 mt-1 flex items-center justify-between">
                                                @if(!$isExpired)
                                                    <span>Sisa {{ $sisaHari }} Hari</span>
                                                    <span>{{ round($progressPercent) }}%</span>
                                                @else
                                                    <span class="text-red-400">Sudah Kedaluwarsa</span>
                                                    <span>0%</span>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- 6. Status & Durasi --}}
                                        <td class="px-6 py-4.5 text-center">
                                            @if($isExpired)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 dark:bg-red-950/20 text-red-700 dark:text-red-400 rounded-full text-[10px] font-extrabold border border-red-100 dark:border-red-900/50 uppercase tracking-wider shrink-0">
                                                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                                    Expired
                                                </span>
                                            @elseif($isSoon)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 dark:bg-amber-950/30 text-amber-800 dark:text-amber-400 rounded-full text-[10px] font-extrabold border border-amber-200 dark:border-amber-800/50 uppercase tracking-wider pulse-amber-badge shrink-0">
                                                    <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-ping"></span>
                                                    Segera Habis
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 dark:bg-emerald-950/20 text-emerald-800 dark:text-emerald-400 rounded-full text-[10px] font-extrabold border border-emerald-100 dark:border-emerald-900/50 uppercase tracking-wider shrink-0">
                                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                                    Aktif
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Premium Pagination --}}
                    <div class="px-6 py-4 bg-gray-50/50 dark:bg-gray-900/10 border-t border-gray-100 dark:border-gray-700/80">
                        {{ $orders->withQueryString()->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
