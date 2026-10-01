<x-workshop-pwa-layout title="Divisi Riset & Pengembangan (R&D)">
<div class="min-h-screen bg-slate-50 py-6 px-4 sm:px-6 lg:px-8 font-sans text-slate-800"
     x-data="{
         expanded: {},
         toggleRow(id) {
             this.expanded[id] = !this.expanded[id];
         },
         isExpanded(id) {
             return !!this.expanded[id];
         }
     }">

    {{-- Breadcrumb & Identity Bar --}}
    <div class="max-w-7xl mx-auto mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 border border-emerald-200/60 flex items-center justify-center p-2.5 shadow-xs">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-contain">
                </div>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Divisi Riset &amp; Pengembangan (R&amp;D)</h1>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-[#22B086] text-white shadow-xs">
                            LABORATORIUM
                        </span>
                    </div>
                    <p class="text-xs font-semibold text-slate-500 mt-0.5">
                        Stasiun kerja terisolasi untuk riset formulasi, pengujian material, dan pengembangan teknik reparasi khusus.
                    </p>
                </div>
            </div>

            {{-- Search Bar --}}
            <div class="flex items-center gap-3">
                <form action="{{ route('workshop.rnd.index') }}" method="GET" class="relative">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <input type="hidden" name="stage" value="{{ $selectedStage }}">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari SPK / Merk / Customer..." 
                           class="w-64 sm:w-72 pl-9 pr-8 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#22B086] focus:border-[#22B086] transition-all">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    @if($search)
                        <a href="{{ route('workshop.rnd.index', ['tab' => $tab, 'stage' => $selectedStage]) }}" 
                           class="absolute right-3 top-2.5 text-xs text-slate-400 hover:text-slate-700 font-bold" title="Reset pencarian">✕</a>
                    @endif
                </form>
            </div>
        </div>
    </div>

    {{-- Metric Stat Cards (7 Columns) --}}
    <div class="max-w-7xl mx-auto mb-6">
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
            {{-- 1. Pra-Riset (Monitoring) --}}
            <a href="{{ route('workshop.rnd.index', ['tab' => 'monitoring', 'search' => $search]) }}" 
               class="bg-white p-3.5 rounded-2xl border transition-all shadow-xs hover:border-amber-400 {{ $tab === 'monitoring' ? 'border-amber-400 ring-2 ring-amber-400/20 bg-amber-50/30' : 'border-slate-200' }}">
                <div class="flex items-center justify-between text-slate-400">
                    <span class="text-[9px] font-black uppercase tracking-wider text-amber-700">Pra-Riset</span>
                    <span class="w-4 h-4 rounded-full bg-amber-100 text-amber-800 text-[9px] font-black flex items-center justify-center">⏳</span>
                </div>
                <div class="mt-1.5 flex items-baseline justify-between">
                    <span class="text-xl font-black text-slate-900">{{ $metrics['countMonitoring'] }}</span>
                    <span class="text-[9px] font-bold text-amber-800 bg-amber-100/70 px-1.5 py-0.5 rounded">Inbound</span>
                </div>
            </a>

            {{-- 2. Riset Aktif (WIP) --}}
            <a href="{{ route('workshop.rnd.index', ['tab' => 'active', 'stage' => 'all', 'search' => $search]) }}" 
               class="bg-white p-3.5 rounded-2xl border transition-all shadow-xs hover:border-[#22B086] {{ ($tab === 'active' && $selectedStage === 'all') ? 'border-[#22B086] ring-2 ring-[#22B086]/20 bg-emerald-50/30' : 'border-slate-200' }}">
                <div class="flex items-center justify-between text-slate-400">
                    <span class="text-[9px] font-black uppercase tracking-wider text-emerald-800">Riset Aktif</span>
                    <span class="w-2 h-2 rounded-full bg-[#22B086] animate-pulse"></span>
                </div>
                <div class="mt-1.5 flex items-baseline justify-between">
                    <span class="text-xl font-black text-slate-900">{{ $metrics['totalActive'] }}</span>
                    <span class="text-[9px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded">WIP</span>
                </div>
            </a>

            {{-- 3. Prep --}}
            <a href="{{ route('workshop.rnd.index', ['tab' => 'active', 'stage' => 'PREPARATION', 'search' => $search]) }}" 
               class="bg-white p-3.5 rounded-2xl border transition-all shadow-xs hover:border-[#22B086] {{ ($tab === 'active' && $selectedStage === 'PREPARATION') ? 'border-[#22B086] ring-2 ring-[#22B086]/20 bg-emerald-50/30' : 'border-slate-200' }}">
                <div class="flex items-center justify-between text-slate-400">
                    <span class="text-[9px] font-black uppercase tracking-wider text-slate-500">1. Prep</span>
                    <span class="w-4 h-4 rounded-md bg-emerald-100 text-emerald-800 text-[9px] font-black flex items-center justify-center">1</span>
                </div>
                <div class="mt-1.5 flex items-baseline justify-between">
                    <span class="text-xl font-black text-slate-900">{{ $metrics['countPrep'] }}</span>
                    <span class="text-[9px] font-bold text-slate-400">SPK</span>
                </div>
            </a>

            {{-- 4. Sortir --}}
            <a href="{{ route('workshop.rnd.index', ['tab' => 'active', 'stage' => 'SORTIR', 'search' => $search]) }}" 
               class="bg-white p-3.5 rounded-2xl border transition-all shadow-xs hover:border-[#22B086] {{ ($tab === 'active' && $selectedStage === 'SORTIR') ? 'border-[#22B086] ring-2 ring-[#22B086]/20 bg-emerald-50/30' : 'border-slate-200' }}">
                <div class="flex items-center justify-between text-slate-400">
                    <span class="text-[9px] font-black uppercase tracking-wider text-slate-500">2. Sortir</span>
                    <span class="w-4 h-4 rounded-md bg-emerald-100 text-emerald-800 text-[9px] font-black flex items-center justify-center">2</span>
                </div>
                <div class="mt-1.5 flex items-baseline justify-between">
                    <span class="text-xl font-black text-slate-900">{{ $metrics['countSortir'] }}</span>
                    <span class="text-[9px] font-bold text-slate-400">SPK</span>
                </div>
            </a>

            {{-- 5. Prod --}}
            <a href="{{ route('workshop.rnd.index', ['tab' => 'active', 'stage' => 'PRODUCTION', 'search' => $search]) }}" 
               class="bg-white p-3.5 rounded-2xl border transition-all shadow-xs hover:border-[#22B086] {{ ($tab === 'active' && $selectedStage === 'PRODUCTION') ? 'border-[#22B086] ring-2 ring-[#22B086]/20 bg-emerald-50/30' : 'border-slate-200' }}">
                <div class="flex items-center justify-between text-slate-400">
                    <span class="text-[9px] font-black uppercase tracking-wider text-slate-500">3. Produksi</span>
                    <span class="w-4 h-4 rounded-md bg-emerald-100 text-emerald-800 text-[9px] font-black flex items-center justify-center">3</span>
                </div>
                <div class="mt-1.5 flex items-baseline justify-between">
                    <span class="text-xl font-black text-slate-900">{{ $metrics['countProd'] }}</span>
                    <span class="text-[9px] font-bold text-slate-400">SPK</span>
                </div>
            </a>

            {{-- 6. QC --}}
            <a href="{{ route('workshop.rnd.index', ['tab' => 'active', 'stage' => 'QC', 'search' => $search]) }}" 
               class="bg-white p-3.5 rounded-2xl border transition-all shadow-xs hover:border-[#22B086] {{ ($tab === 'active' && $selectedStage === 'QC') ? 'border-[#22B086] ring-2 ring-[#22B086]/20 bg-emerald-50/30' : 'border-slate-200' }}">
                <div class="flex items-center justify-between text-slate-400">
                    <span class="text-[9px] font-black uppercase tracking-wider text-slate-500">4. QC Lab</span>
                    <span class="w-4 h-4 rounded-md bg-emerald-100 text-emerald-800 text-[9px] font-black flex items-center justify-center">4</span>
                </div>
                <div class="mt-1.5 flex items-baseline justify-between">
                    <span class="text-xl font-black text-slate-900">{{ $metrics['countQc'] }}</span>
                    <span class="text-[9px] font-bold text-slate-400">SPK</span>
                </div>
            </a>

            {{-- 7. Selesai --}}
            <a href="{{ route('workshop.rnd.index', ['tab' => 'completed', 'search' => $search]) }}" 
               class="bg-white p-3.5 rounded-2xl border transition-all shadow-xs hover:border-[#FFC232] {{ $tab === 'completed' ? 'border-[#FFC232] ring-2 ring-[#FFC232]/20 bg-amber-50/30' : 'border-slate-200' }}">
                <div class="flex items-center justify-between text-slate-400">
                    <span class="text-[9px] font-black uppercase tracking-wider text-slate-500">Selesai</span>
                    <span class="w-4 h-4 rounded-md bg-amber-100 text-amber-900 text-[9px] font-black flex items-center justify-center">OK</span>
                </div>
                <div class="mt-1.5 flex items-baseline justify-between">
                    <span class="text-xl font-black text-slate-900">{{ $metrics['totalCompleted'] }}</span>
                    <span class="text-[9px] font-bold text-amber-800 bg-amber-50 px-1.5 py-0.5 rounded">Arsip</span>
                </div>
            </a>
        </div>
    </div>

    {{-- Main 3-Tab Navigation Bar --}}
    <div class="max-w-7xl mx-auto mb-6">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 bg-white p-2.5 rounded-2xl border border-slate-200 shadow-xs">
            {{-- Tabs --}}
            <div class="flex items-center gap-1.5 p-1 bg-slate-100 rounded-xl overflow-x-auto">
                {{-- Tab 1: Pra-Riset (Monitoring) --}}
                <a href="{{ route('workshop.rnd.index', ['tab' => 'monitoring', 'search' => $search]) }}" 
                   class="px-4 py-2.5 rounded-lg text-xs font-black transition-all flex items-center gap-2 whitespace-nowrap {{ $tab === 'monitoring' ? 'bg-white text-slate-900 shadow-sm border border-slate-200' : 'text-slate-600 hover:text-slate-900' }}">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Monitoring Pra-Riset</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $tab === 'monitoring' ? 'bg-amber-400 text-slate-950' : 'bg-slate-200 text-slate-700' }}">
                        {{ $metrics['countMonitoring'] }}
                    </span>
                </a>

                {{-- Tab 2: Riset Berjalan (Active) --}}
                <a href="{{ route('workshop.rnd.index', ['tab' => 'active', 'stage' => $selectedStage, 'search' => $search]) }}" 
                   class="px-4 py-2.5 rounded-lg text-xs font-black transition-all flex items-center gap-2 whitespace-nowrap {{ $tab === 'active' ? 'bg-white text-slate-900 shadow-sm border border-slate-200' : 'text-slate-600 hover:text-slate-900' }}">
                    <svg class="w-4 h-4 text-[#22B086]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                    <span>Riset Berjalan (Active WIP)</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $tab === 'active' ? 'bg-[#22B086] text-white' : 'bg-slate-200 text-slate-700' }}">
                        {{ $metrics['totalActive'] }}
                    </span>
                </a>

                {{-- Tab 3: Riwayat Selesai --}}
                <a href="{{ route('workshop.rnd.index', ['tab' => 'completed', 'search' => $search]) }}" 
                   class="px-4 py-2.5 rounded-lg text-xs font-black transition-all flex items-center gap-2 whitespace-nowrap {{ $tab === 'completed' ? 'bg-white text-slate-900 shadow-sm border border-slate-200' : 'text-slate-600 hover:text-slate-900' }}">
                    <svg class="w-4 h-4 text-[#FFC232]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Riwayat Selesai</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $tab === 'completed' ? 'bg-slate-900 text-[#FFC232]' : 'bg-slate-200 text-slate-700' }}">
                        {{ $metrics['totalCompleted'] }}
                    </span>
                </a>
            </div>

            {{-- Stage Filter Pills (Shown only on Active Tab) --}}
            @if($tab === 'active')
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
                    <span class="text-[11px] font-extrabold text-slate-400 mr-1 hidden md:inline">Tahap:</span>
                    @php
                        $stages = [
                            'all' => 'Semua',
                            'PREPARATION' => '1. Prep',
                            'SORTIR' => '2. Sortir',
                            'PRODUCTION' => '3. Produksi',
                            'QC' => '4. QC',
                        ];
                    @endphp
                    @foreach($stages as $key => $label)
                        <a href="{{ route('workshop.rnd.index', ['tab' => 'active', 'stage' => $key, 'search' => $search]) }}" 
                           class="px-3 py-1.5 rounded-lg text-xs font-extrabold whitespace-nowrap transition-all {{ $selectedStage === $key ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Alert Messages --}}
    <div class="max-w-7xl mx-auto mb-6">
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-center justify-between shadow-xs animate-fade-in">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-[#22B086] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="text-xs font-bold">{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-black text-sm">✕</button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 flex items-center justify-between shadow-xs animate-fade-in">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span class="text-xs font-bold">{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900 font-black text-sm">✕</button>
            </div>
        @endif
    </div>

    {{-- Main Content Section --}}
    <div class="max-w-7xl mx-auto">

        {{-- ================================================================= --}}
        {{-- TAB 1: MONITORING PRA-RISET (SPK_PENDING, DITERIMA, ASSESSMENT) --}}
        {{-- ================================================================= --}}
        @if($tab === 'monitoring')
            @if($monitoringOrders->isEmpty())
                <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 shadow-sm max-w-xl mx-auto my-12">
                    <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto mb-4 border border-amber-100">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-black text-slate-900">Tidak Ada SPK di Tahap Pra-Riset</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                        Saat ini tidak ada SPK R&amp;D yang tertahan di tahap pendaftaran CS, penerimaan gudang, atau assessment awal.
                    </p>
                </div>
            @else
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-amber-50/50 border-b border-amber-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <span class="text-xs font-black text-amber-900 uppercase tracking-wider">Antrean Monitoring Pra-Riset (Sebelum Masuk Workshop)</span>
                        </div>
                        <span class="text-[11px] font-bold text-amber-800">
                            Klik baris untuk melihat posisi fisik &amp; tombol aksi 'Mulai Riset'
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-black uppercase tracking-wider text-[10px]">
                                <tr>
                                    <th class="py-3.5 px-3 w-10 text-center"></th>
                                    <th class="py-3.5 px-4">Nomor SPK</th>
                                    <th class="py-3.5 px-4">Customer</th>
                                    <th class="py-3.5 px-4">Sepatu &amp; Warna</th>
                                    <th class="py-3.5 px-4">Posisi Saat Ini</th>
                                    <th class="py-3.5 px-4">Tanggal Masuk</th>
                                    <th class="py-3.5 px-4 text-right">Aksi Cepat</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700 font-semibold">
                                @foreach($monitoringOrders as $order)
                                    @php
                                        $currentStatusVal = $order->status instanceof \App\Enums\WorkOrderStatus ? $order->status->value : (string)$order->status;
                                        $reportToken = $order->rndProgresses->first()?->report_token ?? $order->getOrCreateRndUploadToken();
                                        $uploadToken = $order->getOrCreateRndUploadToken();
                                    @endphp

                                    {{-- Primary Clickable Table Row --}}
                                    <tr class="hover:bg-amber-50/40 transition-colors cursor-pointer"
                                        :class="isExpanded('{{ $order->id }}') ? 'bg-amber-50/50 border-l-4 border-amber-500' : ''"
                                        @click="toggleRow('{{ $order->id }}')">
                                        <td class="py-3.5 px-3 text-center">
                                            <button type="button" class="w-6 h-6 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center transition-transform duration-200"
                                                    :class="isExpanded('{{ $order->id }}') ? 'rotate-90 bg-amber-200 text-amber-900' : ''">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </button>
                                        </td>
                                        <td class="py-3.5 px-4 font-mono font-black text-slate-900">
                                            <span class="px-2.5 py-1 rounded-lg bg-slate-100 border border-slate-200">
                                                {{ $order->spk_number }}
                                            </span>
                                            @if($order->invoice?->invoice_number)
                                                <span class="text-[10px] font-mono text-slate-400 font-bold block mt-1">
                                                    #{{ $order->invoice->invoice_number }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <div class="font-black text-slate-900">{{ $order->customer_name }}</div>
                                            <div class="text-[10px] text-slate-400">{{ $order->customer_phone }}</div>
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <div class="font-black text-slate-900">{{ $order->shoe_brand }} - {{ $order->shoe_type ?? 'Sepatu' }}</div>
                                            <div class="text-[10px] text-slate-400">Sz: {{ $order->shoe_size ?? '-' }} | {{ $order->shoe_color ?? '-' }}</div>
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-900 border border-amber-300">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                                {{ $currentStatusVal }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-500 text-[11px]">
                                            {{ $order->entry_date ? \Carbon\Carbon::parse($order->entry_date)->format('d M Y') : $order->created_at->format('d M Y') }}
                                        </td>
                                        <td class="py-3.5 px-4 text-right" @click.stop>
                                            <form action="{{ route('workshop.rnd.start-research', $order->id) }}" method="POST"
                                                  onsubmit="return confirm('Mulai riset sekarang dan pindahkan SPK #{{ $order->spk_number }} ke lantai kerja (PREPARATION)?');">
                                                @csrf
                                                <button type="submit" 
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#22B086] hover:bg-emerald-600 text-white font-black text-xs shadow-xs active:scale-95 transition-all">
                                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                                    </svg>
                                                    <span>Mulai Riset (Prep)</span>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    {{-- Collapsible Expanded Drawer Row --}}
                                    <tr x-show="isExpanded('{{ $order->id }}')" x-cloak class="bg-amber-50/20 border-b border-slate-200">
                                        <td colspan="7" class="p-5">
                                            <div class="bg-white rounded-2xl p-5 border border-amber-200/80 shadow-xs space-y-4">
                                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
                                                    <div>
                                                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Informasi Lokasi &amp; Posisi Fisik Sepatu</span>
                                                        <h4 class="text-sm font-black text-slate-900 mt-0.5">
                                                            Status Terkini: <span class="text-amber-700">{{ $currentStatusVal }}</span> (Tahap Pra-Riset)
                                                        </h4>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <a href="{{ route('rnd.report', $reportToken) }}" target="_blank"
                                                           class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-black transition-all flex items-center gap-1.5 shadow-xs">
                                                            <svg class="w-4 h-4 text-[#22B086]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                            </svg>
                                                            <span>Buka Living Report</span>
                                                        </a>
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                                    {{-- Catatan Customer / CS --}}
                                                    <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 text-xs flex flex-col justify-between">
                                                        <div>
                                                            <div class="font-black text-slate-700 mb-1">Catatan Pendaftaran &amp; Target Riset CS:</div>
                                                            <p class="text-slate-600 italic">
                                                                "{{ $order->notes ?? $order->technician_notes ?? 'Tidak ada catatan khusus dari CS.' }}"
                                                            </p>
                                                        </div>
                                                        <div class="mt-2 text-[10px] text-slate-400 font-semibold">
                                                            Didaftarkan: {{ $order->created_at->format('d M Y H:i') }}
                                                        </div>
                                                    </div>

                                                    {{-- Informasi Tagihan & Invoice --}}
                                                    @php
                                                        $inv = $order->invoice;
                                                        $invNumber = $inv?->invoice_number ?? ($order->invoice_id ? 'INV-#' . $order->invoice_id : null);
                                                        $invStatus = $inv?->status ?? 'Belum Ada';
                                                        $invDueDate = $inv?->due_date ? \Carbon\Carbon::parse($inv->due_date)->format('d M Y') : ($inv?->estimasi_selesai ? \Carbon\Carbon::parse($inv->estimasi_selesai)->format('d M Y') : null);
                                                        $invUrl = $inv?->invoice_full_url ?? $inv?->invoice_akhir_url ?? ($invNumber ? url('/api/invoice_share_grouped.php?token='.urlencode($invNumber).'&type=FULL') : null);
                                                        $statusColor = match(strtolower($invStatus)) {
                                                            'lunas' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                                            'dp/cicil', 'dp', 'cicil' => 'bg-amber-50 text-amber-800 border-amber-200',
                                                            'batal' => 'bg-slate-100 text-slate-600 border-slate-300',
                                                            default => 'bg-rose-50 text-rose-800 border-rose-200',
                                                        };
                                                    @endphp
                                                    <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 text-xs flex flex-col justify-between">
                                                        <div>
                                                            <div class="flex items-center justify-between gap-2 mb-2">
                                                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-500">Informasi Tagihan &amp; Invoice</span>
                                                                @if($inv)
                                                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase border {{ $statusColor }}">
                                                                        {{ $invStatus }}
                                                                    </span>
                                                                @endif
                                                            </div>

                                                            @if($inv)
                                                                <div class="flex items-center justify-between gap-2">
                                                                    <span class="font-mono font-black text-slate-800 text-[11px] bg-white px-2 py-0.5 rounded border border-slate-200">
                                                                        #{{ $invNumber }}
                                                                    </span>
                                                                    @if($invUrl)
                                                                        <a href="{{ $invUrl }}" target="_blank" class="text-[11px] font-black text-[#22B086] hover:underline flex items-center gap-0.5">
                                                                            <span>Buka Invoice</span>
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                                            </svg>
                                                                        </a>
                                                                    @endif
                                                                </div>

                                                                <div class="mt-2.5 pt-2 border-t border-slate-200/60 flex items-center justify-between text-[11px]">
                                                                    <span class="text-slate-400 font-bold">Jatuh Tempo / Estimasi:</span>
                                                                    <span class="font-black text-slate-700">{{ $invDueDate ?? 'Belum Ditentukan' }}</span>
                                                                </div>
                                                            @else
                                                                <div class="py-3 text-center text-slate-400 italic text-[11px]">
                                                                    Belum terhubung dengan nomor invoice
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    {{-- Tombol Tarik ke Lantai Kerja Riset --}}
                                                    <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 flex flex-col justify-between">
                                                        <div>
                                                            <div class="font-black text-emerald-900 text-xs">Fisik Sepatu Sudah Tiba di Lab R&amp;D?</div>
                                                            <p class="text-[11px] text-emerald-700 mt-0.5">
                                                                Tarik SPK ini langsung ke lantai kerja aktif (tahap PREPARATION) untuk mulai dikerjakan teknisi riset.
                                                            </p>
                                                        </div>
                                                        <div class="mt-3">
                                                            <form action="{{ route('workshop.rnd.start-research', $order->id) }}" method="POST"
                                                                  onsubmit="return confirm('Mulai riset dan tarik SPK #{{ $order->spk_number }} ke PREPARATION?');">
                                                                @csrf
                                                                <button type="submit" 
                                                                        class="w-full py-2 px-4 rounded-xl bg-[#22B086] hover:bg-emerald-600 text-white font-black text-xs shadow-xs active:scale-95 transition-all flex items-center justify-center gap-2">
                                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                                    </svg>
                                                                    <span>Mulai Riset Sekarang (Masuk ke Tahap 1: Prep)</span>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- DOKUMENTASI & PUSAT UPLOAD PROGRES (2 Card Berdampingan Ukuran Besar) --}}
                                                <div class="pt-3 border-t border-slate-100">
                                                    <div class="flex items-center justify-between mb-3">
                                                        <span class="text-[11px] font-black uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                                                            <span class="w-2 h-2 rounded-full bg-[#22B086]"></span>
                                                            <span>Pusat Unggah Progres &amp; Dokumentasi Foto (Pra-Riset)</span>
                                                        </span>
                                                        <span class="text-[10px] font-bold text-slate-400">Pilih metode upload via PC atau scan langsung dari HP</span>
                                                    </div>

                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        {{-- Card 1: Upload via PC --}}
                                                        <div class="p-5 rounded-2xl bg-gradient-to-br from-slate-50 to-white border-2 border-slate-200/90 hover:border-slate-300 shadow-xs flex flex-col justify-between transition-all">
                                                            <div class="flex items-start gap-4 mb-4">
                                                                <div class="w-12 h-12 rounded-2xl bg-slate-900 text-[#FFC232] flex items-center justify-center flex-shrink-0 shadow-sm">
                                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                                    </svg>
                                                                </div>
                                                                <div class="flex-1 min-w-0">
                                                                    <div class="flex items-center gap-2">
                                                                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 bg-slate-100 px-2 py-0.5 rounded">Metode PC / Laptop</span>
                                                                        <span class="text-[9px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">Auto-WebP Canvas</span>
                                                                    </div>
                                                                    <h4 class="text-sm font-black text-slate-900 mt-1">Upload Progres via PC</h4>
                                                                    <p class="text-[11px] text-slate-500 mt-0.5">Unggah foto resolusi tinggi dari kamera komputer/studio dengan kompresi WebP instan.</p>
                                                                </div>
                                                            </div>

                                                            <button type="button" onclick="openUploadModal('{{ $order->id }}', '{{ $order->spk_number }}')"
                                                                    class="w-full py-3 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-black text-xs shadow-md active:scale-98 transition-all flex items-center justify-center gap-2">
                                                                <svg class="w-4 h-4 text-[#FFC232]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                                                </svg>
                                                                <span>Buka Form Upload Foto (PC)</span>
                                                            </button>
                                                        </div>

                                                        {{-- Card 2: Scan QR Kamera HP --}}
                                                        <div class="p-5 rounded-2xl bg-gradient-to-br from-emerald-50/50 to-white border-2 border-emerald-200/90 hover:border-emerald-300 shadow-xs flex flex-col justify-between transition-all">
                                                            <div class="flex items-center gap-4">
                                                                <div class="bg-white p-2 rounded-xl border border-slate-200 shadow-sm flex-shrink-0 cursor-pointer group"
                                                                     onclick="openQrModal('{{ $order->spk_number }}', '{{ route('rnd.upload', $uploadToken) }}')"
                                                                     title="Klik untuk memperbesar QR Code">
                                                                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(95)->generate(route('rnd.upload', $uploadToken)) !!}
                                                                    <div class="text-[9px] font-bold text-slate-400 text-center mt-1 group-hover:text-[#22B086]">Perbesar</div>
                                                                </div>

                                                                <div class="flex-1 min-w-0 flex flex-col justify-between space-y-2">
                                                                    <div>
                                                                        <div class="flex items-center gap-2">
                                                                            <span class="text-[10px] font-black uppercase tracking-wider text-emerald-800 bg-emerald-100/80 px-2 py-0.5 rounded">Scan Kamera HP</span>
                                                                            <span class="text-[9px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">Praktis di Lab</span>
                                                                        </div>
                                                                        <h4 class="text-sm font-black text-slate-900 mt-1">Scan Kamera Smartphone</h4>
                                                                        <p class="text-[11px] text-slate-500 mt-0.5">Arahkan kamera ponsel ke QR di samping untuk langsung mengambil &amp; unggah foto riset.</p>
                                                                    </div>

                                                                    <div class="flex items-center gap-2 pt-1">
                                                                        <button type="button" onclick="openQrModal('{{ $order->spk_number }}', '{{ route('rnd.upload', $uploadToken) }}')"
                                                                                class="py-2 px-3.5 rounded-xl bg-[#22B086] hover:bg-emerald-600 text-white font-black text-xs flex items-center gap-1.5 shadow-sm active:scale-98 transition-all">
                                                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                                                            </svg>
                                                                            <span>Perbesar QR</span>
                                                                        </button>
                                                                        <a href="{{ route('rnd.upload', $uploadToken) }}" target="_blank"
                                                                           class="py-2 px-3 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs border border-slate-200 transition-all flex items-center gap-1">
                                                                            <span>Form HP</span>
                                                                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                                            </svg>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $monitoringOrders->links() }}
                </div>
            @endif

        {{-- ================================================================= --}}
        {{-- TAB 2: RISET BERJALAN (ACTIVE WORKSHOP - PREP, SORTIR, PROD, QC)   --}}
        {{-- ================================================================= --}}
        @elseif($tab === 'active')
            @if($activeOrders->isEmpty())
                <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 shadow-sm max-w-xl mx-auto my-12">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-[#22B086] flex items-center justify-center mx-auto mb-4 border border-emerald-100">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-black text-slate-900">Tidak Ada SPK Riset Sedang Berjalan</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                        @if($metrics['countMonitoring'] > 0)
                            Terdapat <strong>{{ $metrics['countMonitoring'] }} SPK</strong> di Tab Monitoring Pra-Riset. Tekan tombol 'Mulai Riset' pada SPK tersebut untuk memindahkannya ke sini.
                        @else
                            Saat ini belum ada proyek riset aktif di lantai kerja.
                        @endif
                    </p>
                    @if($metrics['countMonitoring'] > 0)
                        <a href="{{ route('workshop.rnd.index', ['tab' => 'monitoring']) }}" 
                           class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 text-white text-xs font-black hover:bg-amber-600 transition-all">
                            Buka Tab Monitoring Pra-Riset ({{ $metrics['countMonitoring'] }})
                        </a>
                    @endif
                </div>
            @else
                {{-- Collapsible Table: Riset Berjalan --}}
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-emerald-50/40 border-b border-emerald-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#22B086] animate-pulse"></span>
                            <span class="text-xs font-black text-emerald-900 uppercase tracking-wider">Daftar SPK Lantai Kerja Riset R&amp;D</span>
                        </div>
                        <span class="text-[11px] font-bold text-emerald-700">
                            Klik baris untuk expand kontrol stepper, teknisi PIC, &amp; dokumentasi
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-black uppercase tracking-wider text-[10px]">
                                <tr>
                                    <th class="py-3.5 px-3 w-10 text-center"></th>
                                    <th class="py-3.5 px-4">Nomor SPK</th>
                                    <th class="py-3.5 px-4">Customer</th>
                                    <th class="py-3.5 px-4">Sepatu &amp; Warna</th>
                                    <th class="py-3.5 px-4">Tahap Aktif</th>
                                    <th class="py-3.5 px-4">Teknisi PIC</th>
                                    <th class="py-3.5 px-4">Foto Progres</th>
                                    <th class="py-3.5 px-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700 font-semibold">
                                @foreach($activeOrders as $order)
                                    @php
                                        $currentStatusVal = $order->status instanceof \App\Enums\WorkOrderStatus ? $order->status->value : (string)$order->status;
                                        $reportToken = $order->rndProgresses->first()?->report_token ?? $order->getOrCreateRndUploadToken();
                                        $uploadToken = $order->getOrCreateRndUploadToken();
                                        $progressCount = $order->rndProgresses->whereNotNull('photo_path')->count();
                                        $latestProgress = $order->rndProgresses->whereNotNull('photo_path')->last();
                                        $summaryPicId = $order->technician_production_id 
                                            ?? $order->prep_washing_by 
                                            ?? $order->prep_sol_by 
                                            ?? $order->prep_upper_by 
                                            ?? $order->qc_final_by;
                                        $picName = $summaryPicId ? (\App\Models\User::find($summaryPicId)?->name ?? 'Belum Ditugaskan') : 'Belum Ditugaskan';
                                    @endphp

                                    {{-- Primary Clickable Table Row --}}
                                    <tr class="hover:bg-slate-50/80 transition-colors cursor-pointer"
                                        :class="isExpanded('{{ $order->id }}') ? 'bg-emerald-50/40 border-l-4 border-[#22B086]' : ''"
                                        @click="toggleRow('{{ $order->id }}')">
                                        <td class="py-3.5 px-3 text-center">
                                            <button type="button" class="w-6 h-6 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center transition-transform duration-200"
                                                    :class="isExpanded('{{ $order->id }}') ? 'rotate-90 bg-emerald-200 text-emerald-900' : ''">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </button>
                                        </td>
                                        <td class="py-3.5 px-4 font-mono font-black text-slate-900">
                                            <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                {{ $order->spk_number }}
                                            </span>
                                            @if($order->invoice?->invoice_number)
                                                <span class="text-[10px] font-mono text-slate-400 font-bold block mt-1">
                                                    #{{ $order->invoice->invoice_number }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <div class="font-black text-slate-900">{{ $order->customer_name }}</div>
                                            <div class="text-[10px] text-slate-400">{{ $order->customer_phone }}</div>
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <div class="font-black text-slate-900">{{ $order->shoe_brand }} - {{ $order->shoe_type ?? 'Sepatu' }}</div>
                                            <div class="text-[10px] text-slate-400">Sz: {{ $order->shoe_size ?? '-' }} | {{ $order->shoe_color ?? '-' }}</div>
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <span id="row-stage-badge-{{ $order->id }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-[#22B086]/10 text-emerald-800 border border-[#22B086]/30">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#22B086] animate-ping"></span>
                                                <span id="row-stage-text-{{ $order->id }}">{{ $currentStatusVal }}</span>
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <span id="row-pic-name-{{ $order->id }}" class="font-bold {{ $summaryPicId ? 'text-slate-800' : 'text-slate-400 italic' }}">
                                                {{ $picName }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-100 font-bold text-slate-700 text-[11px]">
                                                {{ $progressCount }} Foto
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-right" @click.stop>
                                            <div class="flex items-center justify-end gap-1.5">
                                                <button type="button" 
                                                        onclick="openUploadModal('{{ $order->id }}', '{{ $order->spk_number }}')"
                                                        class="p-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-[#FFC232] transition-all shadow-xs" title="Tambah Progres (PC)">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                                    </svg>
                                                </button>
                                                <a href="{{ route('rnd.report', $reportToken) }}" target="_blank"
                                                   class="p-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-[#22B086] border border-emerald-200 transition-all" title="Buka Living Report">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- Collapsible Expanded Drawer Row --}}
                                    <tr x-show="isExpanded('{{ $order->id }}')" x-cloak class="bg-emerald-50/20 border-b border-slate-200">
                                        <td colspan="8" class="p-5">
                                            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs space-y-4">
                                                
                                                {{-- Stepper Control Header --}}
                                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
                                                    <div>
                                                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Kontrol Alur Riset (Interactive Stepper)</span>
                                                        <h4 class="text-sm font-black text-slate-900 mt-0.5">
                                                            SPK <span class="font-mono text-emerald-800">{{ $order->spk_number }}</span> — {{ $order->shoe_brand }} ({{ $order->customer_name }})
                                                        </h4>
                                                    </div>
                                                    
                                                    {{-- Action Buttons --}}
                                                    <div class="flex items-center gap-2">
                                                        <a href="{{ route('rnd.report', $reportToken) }}" target="_blank"
                                                           class="py-2 px-3.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-[#22B086] border border-emerald-200 text-xs font-black flex items-center gap-1.5 transition-all shadow-xs">
                                                            <svg class="w-3.5 h-3.5 text-[#22B086]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                            </svg>
                                                            <span>Living Report</span>
                                                        </a>

                                                        @php
                                                            $stageActionConfigs = [
                                                                'PREPARATION' => ['next' => 'SORTIR', 'label' => 'Lanjut ke Sortir', 'color' => 'bg-indigo-600 hover:bg-indigo-700 text-white'],
                                                                'SORTIR'      => ['next' => 'PRODUCTION', 'label' => 'Lanjut ke Produksi', 'color' => 'bg-blue-600 hover:bg-blue-700 text-white'],
                                                                'PRODUCTION'  => ['next' => 'QC', 'label' => 'Lanjut ke QC', 'color' => 'bg-teal-600 hover:bg-teal-700 text-white'],
                                                                'QC'          => ['next' => 'FINISH', 'label' => 'Selesaikan Riset (Final)', 'color' => 'bg-[#22B086] hover:bg-emerald-600 text-white'],
                                                            ];
                                                            $currentStageAction = $stageActionConfigs[$currentStatusVal] ?? $stageActionConfigs['QC'];
                                                        @endphp

                                                        <div id="stage-action-container-{{ $order->id }}" data-spk="{{ $order->spk_number }}">
                                                            @if($currentStatusVal === 'QC')
                                                                <form action="{{ route('workshop.rnd.complete', $order->id) }}" method="POST" 
                                                                      onsubmit="return confirm('Apakah Anda yakin ingin menyelesaikan proyek riset SPK #{{ $order->spk_number }}? Status akan diubah menjadi SELESAI.');">
                                                                    @csrf
                                                                    <button type="submit" 
                                                                            class="py-2 px-4 rounded-xl bg-[#22B086] hover:bg-emerald-600 text-white text-xs font-black flex items-center gap-1.5 transition-all shadow-xs active:scale-95">
                                                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                                        </svg>
                                                                        <span>Selesaikan Riset (Final)</span>
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <button type="button" 
                                                                        onclick="advanceStageFromButton('{{ $order->id }}', '{{ $currentStageAction['next'] }}')"
                                                                        class="py-2 px-4 rounded-xl {{ $currentStageAction['color'] }} text-xs font-black flex items-center gap-1.5 transition-all shadow-xs active:scale-95">
                                                                    <span>{{ $currentStageAction['label'] }}</span>
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                                                    </svg>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Stepper Buttons & Sub-Process Technician Assignment --}}
                                                @php
                                                    $orderSubAssignments = [
                                                        'prep_washing' => $order->isStationUnneeded('prep_washing') ? 'none' : ($order->prep_washing_by ?? ''),
                                                        'prep_sol' => $order->isStationUnneeded('prep_sol') ? 'none' : ($order->prep_sol_by ?? ''),
                                                        'prep_upper' => $order->isStationUnneeded('prep_upper') ? 'none' : ($order->prep_upper_by ?? ''),
                                                        'prod_sol' => $order->isStationUnneeded('prod_sol') ? 'none' : ($order->prod_sol_by ?? ''),
                                                        'prod_upper' => $order->isStationUnneeded('prod_upper') ? 'none' : ($order->prod_upper_by ?? ''),
                                                        'prod_cleaning' => $order->isStationUnneeded('prod_cleaning') ? 'none' : ($order->prod_cleaning_by ?? ''),
                                                        'qc_jahit' => $order->isStationUnneeded('qc_jahit') ? 'none' : ($order->qc_jahit_by ?? ''),
                                                        'qc_cleanup' => $order->isStationUnneeded('qc_cleanup') ? 'none' : ($order->qc_cleanup_by ?? ''),
                                                        'qc_final' => $order->isStationUnneeded('qc_final') ? 'none' : ($order->qc_final_by ?? ''),
                                                    ];
                                                @endphp
                                                <div class="grid grid-cols-1 lg:grid-cols-12 gap-3.5">
                                                    {{-- Visual Stepper (4 Kolom) --}}
                                                    <div class="lg:col-span-4 p-3 bg-slate-50 rounded-2xl border border-slate-200/80 flex flex-col justify-between">
                                                        <div class="flex items-center justify-between mb-2">
                                                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-500">Pilih Tahapan Riset:</span>
                                                            <span class="text-[10px] font-bold text-slate-400">Klik 1x untuk pindah</span>
                                                        </div>
                                                        <div class="grid grid-cols-4 gap-1.5" id="stepper-buttons-{{ $order->id }}">
                                                            @php
                                                                $stepperStages = [
                                                                    'PREPARATION' => '1. PREP',
                                                                    'SORTIR'      => '2. SORTIR',
                                                                    'PRODUCTION'  => '3. PROD',
                                                                    'QC'          => '4. QC',
                                                                ];
                                                            @endphp
                                                            @foreach($stepperStages as $stgKey => $stgLabel)
                                                                @php $isActive = ($currentStatusVal === $stgKey); @endphp
                                                                <button type="button" 
                                                                        data-stage-key="{{ $stgKey }}"
                                                                        onclick="updateOrderStage('{{ $order->id }}', '{{ $stgKey }}', this)"
                                                                        class="py-2.5 px-1 text-[10px] rounded-xl transition-all duration-200 flex flex-col items-center justify-center text-center
                                                                        {{ $isActive ? 'bg-[#22B086] text-white shadow-sm ring-2 ring-emerald-400/40 font-black' : 'bg-white hover:bg-slate-200 text-slate-700 font-extrabold border border-slate-200' }}">
                                                                    <span>{{ $stgLabel }}</span>
                                                                </button>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    {{-- Penugasan Sub-Proses Teknisi (8 Kolom) --}}
                                                    <div class="lg:col-span-8 p-3 bg-slate-50 rounded-2xl border border-slate-200/80 flex flex-col justify-between"
                                                         id="substations-container-{{ $order->id }}"
                                                         data-order-id="{{ $order->id }}"
                                                         data-current-stage="{{ $currentStatusVal }}"
                                                         data-sub-assignments="{{ json_encode($orderSubAssignments) }}">
                                                        
                                                        <div class="flex items-center justify-between mb-2">
                                                            <div class="flex items-center gap-2">
                                                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-500">Penugasan Sub-Proses:</span>
                                                                <span id="tech-stage-label-{{ $order->id }}" class="text-[10px] font-black text-[#22B086] bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200 uppercase">
                                                                    {{ $currentStatusVal }}
                                                                </span>
                                                            </div>
                                                            <span id="tech-saved-indicator-{{ $order->id }}" class="text-emerald-600 text-[10px] font-black hidden">✓ Tersimpan</span>
                                                        </div>

                                                        <div id="substations-grid-{{ $order->id }}">
                                                            @if($currentStatusVal === 'SORTIR')
                                                                <div class="py-3 px-3 bg-slate-100 rounded-xl text-xs text-slate-500 font-bold border border-slate-200 flex items-center justify-center gap-2 text-center">
                                                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                                    <span>Tahap Sortir Material (Tanpa Penugasan Teknisi)</span>
                                                                </div>
                                                            @else
                                                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                                                    @php
                                                                        $activeSubList = $rndSubStations[$currentStatusVal] ?? [];
                                                                    @endphp
                                                                    @foreach($activeSubList as $subKey => $subData)
                                                                        @php
                                                                            $assignedVal = $orderSubAssignments[$subKey] ?? '';
                                                                            $isUnneeded = ($assignedVal === 'none');
                                                                        @endphp
                                                                        <div class="p-2 rounded-xl border transition-all flex flex-col justify-between {{ $isUnneeded ? 'bg-slate-100/90 border-dashed border-slate-300' : 'bg-white border-slate-200/90 shadow-2xs hover:border-[#22B086]' }}"
                                                                             id="subcard-{{ $order->id }}-{{ $subKey }}">
                                                                            <div class="flex items-center justify-between mb-1.5">
                                                                                <span class="text-[10px] font-black text-slate-800 truncate" title="{{ $subData['label'] }}">
                                                                                    {{ $subData['label'] }}
                                                                                </span>
                                                                                <span id="badge-{{ $order->id }}-{{ $subKey }}" 
                                                                                      class="text-[8px] font-black px-1.5 py-0.5 rounded uppercase tracking-wider {{ $isUnneeded ? 'bg-slate-200 text-slate-500' : ($assignedVal ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-400') }}">
                                                                                    {{ $isUnneeded ? 'Bypass' : ($assignedVal ? 'Aktif' : 'Antre') }}
                                                                                </span>
                                                                            </div>
                                                                            <select onchange="assignSubStation('{{ $order->id }}', '{{ $subKey }}', this.value)"
                                                                                    id="select-{{ $order->id }}-{{ $subKey }}"
                                                                                    class="w-full px-2 py-1 bg-white border border-slate-300 rounded-lg text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#22B086] focus:border-[#22B086] transition-all">
                                                                                <option value="">-- Belum Ditugaskan --</option>
                                                                                <option value="none" {{ $isUnneeded ? 'selected' : '' }} class="font-bold text-slate-400 bg-slate-50">-- Tidak Diperlukan --</option>
                                                                                @foreach($subData['techs'] as $tech)
                                                                                    <option value="{{ $tech->id }}" {{ (string)$assignedVal === (string)$tech->id ? 'selected' : '' }}>
                                                                                        {{ $tech->name }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- DOKUMENTASI & PUSAT UPLOAD PROGRES (2 Card Berdampingan Ukuran Besar) --}}
                                                <div class="pt-1">
                                                    <div class="flex items-center justify-between mb-3">
                                                        <span class="text-[11px] font-black uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                                                            <span class="w-2 h-2 rounded-full bg-[#22B086]"></span>
                                                            <span>Pusat Unggah Progres &amp; Dokumentasi Foto (Riset Aktif)</span>
                                                        </span>
                                                        <span class="text-[10px] font-bold text-slate-400">Pilih metode upload via PC atau scan langsung dari HP</span>
                                                    </div>

                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        {{-- Card 1: Upload via PC --}}
                                                        <div class="p-5 rounded-2xl bg-gradient-to-br from-slate-50 to-white border-2 border-slate-200/90 hover:border-slate-300 shadow-xs flex flex-col justify-between transition-all">
                                                            <div class="flex items-start gap-4 mb-4">
                                                                <div class="w-12 h-12 rounded-2xl bg-slate-900 text-[#FFC232] flex items-center justify-center flex-shrink-0 shadow-sm">
                                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                                    </svg>
                                                                </div>
                                                                <div class="flex-1 min-w-0">
                                                                    <div class="flex items-center gap-2">
                                                                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 bg-slate-100 px-2 py-0.5 rounded">Metode PC / Laptop</span>
                                                                        <span class="text-[9px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">Auto-WebP Canvas</span>
                                                                    </div>
                                                                    <h4 class="text-sm font-black text-slate-900 mt-1">Upload Progres via PC</h4>
                                                                    <p class="text-[11px] text-slate-500 mt-0.5">Unggah foto resolusi tinggi dari kamera komputer/studio dengan kompresi WebP instan.</p>
                                                                </div>
                                                            </div>

                                                            <button type="button" onclick="openUploadModal('{{ $order->id }}', '{{ $order->spk_number }}')"
                                                                    class="w-full py-3 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-black text-xs shadow-md active:scale-98 transition-all flex items-center justify-center gap-2">
                                                                <svg class="w-4 h-4 text-[#FFC232]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                                                </svg>
                                                                <span>Buka Form Upload Foto (PC)</span>
                                                            </button>
                                                        </div>

                                                        {{-- Card 2: Scan QR Kamera HP --}}
                                                        <div class="p-5 rounded-2xl bg-gradient-to-br from-emerald-50/50 to-white border-2 border-emerald-200/90 hover:border-emerald-300 shadow-xs flex flex-col justify-between transition-all">
                                                            <div class="flex items-center gap-4">
                                                                <div class="bg-white p-2 rounded-xl border border-slate-200 shadow-sm flex-shrink-0 cursor-pointer group"
                                                                     onclick="openQrModal('{{ $order->spk_number }}', '{{ route('rnd.upload', $uploadToken) }}')"
                                                                     title="Klik untuk memperbesar QR Code">
                                                                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(95)->generate(route('rnd.upload', $uploadToken)) !!}
                                                                    <div class="text-[9px] font-bold text-slate-400 text-center mt-1 group-hover:text-[#22B086]">Perbesar</div>
                                                                </div>

                                                                <div class="flex-1 min-w-0 flex flex-col justify-between space-y-2">
                                                                    <div>
                                                                        <div class="flex items-center gap-2">
                                                                            <span class="text-[10px] font-black uppercase tracking-wider text-emerald-800 bg-emerald-100/80 px-2 py-0.5 rounded">Scan Kamera HP</span>
                                                                            <span class="text-[9px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">Praktis di Lab</span>
                                                                        </div>
                                                                        <h4 class="text-sm font-black text-slate-900 mt-1">Scan Kamera Smartphone</h4>
                                                                        <p class="text-[11px] text-slate-500 mt-0.5">Arahkan kamera ponsel ke QR di samping untuk langsung mengambil &amp; unggah foto riset.</p>
                                                                    </div>

                                                                    <div class="flex items-center gap-2 pt-1">
                                                                        <button type="button" onclick="openQrModal('{{ $order->spk_number }}', '{{ route('rnd.upload', $uploadToken) }}')"
                                                                                class="py-2 px-3.5 rounded-xl bg-[#22B086] hover:bg-emerald-600 text-white font-black text-xs flex items-center gap-1.5 shadow-sm active:scale-98 transition-all">
                                                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                                                            </svg>
                                                                            <span>Perbesar QR</span>
                                                                        </button>
                                                                        <a href="{{ route('rnd.upload', $uploadToken) }}" target="_blank"
                                                                           class="py-2 px-3 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs border border-slate-200 transition-all flex items-center gap-1">
                                                                            <span>Form HP</span>
                                                                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                                            </svg>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Jurnal Progres Mini Status, Catatan & Informasi Invoice --}}
                                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 pt-1 border-t border-slate-100">
                                                    {{-- Catatan SPK --}}
                                                    <div class="text-xs flex flex-col justify-between">
                                                        <div>
                                                            <span class="font-black text-slate-700">Target Riset / Formulasi:</span>
                                                            <p class="text-slate-600 italic mt-0.5 bg-slate-50 p-2.5 rounded-xl border border-slate-200/70">
                                                                "{{ $order->notes ?? $order->technician_notes ?? 'Tidak ada catatan khusus.' }}"
                                                            </p>
                                                        </div>
                                                        <div class="mt-1.5 text-[10px] text-slate-400 font-semibold">
                                                            Posisi: <span class="font-black text-emerald-800">{{ $currentStatusVal }}</span>
                                                        </div>
                                                    </div>

                                                    {{-- Informasi Tagihan & Invoice --}}
                                                    @php
                                                        $inv = $order->invoice;
                                                        $invNumber = $inv?->invoice_number ?? ($order->invoice_id ? 'INV-#' . $order->invoice_id : null);
                                                        $invStatus = $inv?->status ?? 'Belum Ada';
                                                        $invDueDate = $inv?->due_date ? \Carbon\Carbon::parse($inv->due_date)->format('d M Y') : ($inv?->estimasi_selesai ? \Carbon\Carbon::parse($inv->estimasi_selesai)->format('d M Y') : null);
                                                        $invUrl = $inv?->invoice_full_url ?? $inv?->invoice_akhir_url ?? ($invNumber ? url('/api/invoice_share_grouped.php?token='.urlencode($invNumber).'&type=FULL') : null);
                                                        $statusColor = match(strtolower($invStatus)) {
                                                            'lunas' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                                            'dp/cicil', 'dp', 'cicil' => 'bg-amber-50 text-amber-800 border-amber-200',
                                                            'batal' => 'bg-slate-100 text-slate-600 border-slate-300',
                                                            default => 'bg-rose-50 text-rose-800 border-rose-200',
                                                        };
                                                    @endphp
                                                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200/70 text-xs flex flex-col justify-between">
                                                        <div>
                                                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-500">Invoice &amp; Pembayaran</span>
                                                                @if($inv)
                                                                    <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase border {{ $statusColor }}">
                                                                        {{ $invStatus }}
                                                                    </span>
                                                                @endif
                                                            </div>

                                                            @if($inv)
                                                                <div class="flex items-center justify-between gap-2">
                                                                    <span class="font-mono font-black text-slate-800 text-[11px] bg-white px-2 py-0.5 rounded border border-slate-200">
                                                                        #{{ $invNumber }}
                                                                    </span>
                                                                    @if($invUrl)
                                                                        <a href="{{ $invUrl }}" target="_blank" class="text-[11px] font-black text-[#22B086] hover:underline flex items-center gap-0.5">
                                                                            <span>Buka Invoice</span>
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                                            </svg>
                                                                        </a>
                                                                    @endif
                                                                </div>

                                                                <div class="mt-2 pt-1.5 border-t border-slate-200/60 flex items-center justify-between text-[11px]">
                                                                    <span class="text-slate-400 font-bold">Jatuh Tempo / Estimasi:</span>
                                                                    <span class="font-black text-slate-700">{{ $invDueDate ?? 'Belum Ditentukan' }}</span>
                                                                </div>
                                                            @else
                                                                <div class="py-2 text-center text-slate-400 italic text-[11px]">
                                                                    Belum terhubung dengan nomor invoice
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    {{-- Mini Preview Jurnal --}}
                                                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200/70 flex items-center justify-between">
                                                        <div class="flex items-center gap-2.5">
                                                            <div class="w-9 h-9 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-700 shadow-xs">
                                                                <svg class="w-4 h-4 text-[#22B086]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <div class="text-xs font-black text-slate-800">
                                                                    {{ $progressCount }} Foto Dokumentasi
                                                                </div>
                                                                <p class="text-[10px] font-bold text-slate-400 truncate max-w-xs">
                                                                    {{ $latestProgress ? 'Tahap terakhir: ' . $latestProgress->stage_title : 'Belum ada foto progres diunggah' }}
                                                                </p>
                                                            </div>
                                                        </div>

                                                        @if($latestProgress && $latestProgress->photo_path)
                                                            <div class="w-10 h-10 rounded-xl overflow-hidden border border-slate-200 shadow-xs flex-shrink-0 bg-white">
                                                                <img src="{{ asset('storage/' . $latestProgress->photo_path) }}" alt="Thumbnail" class="w-full h-full object-cover">
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $activeOrders->links() }}
                </div>
            @endif

        {{-- ================================================================= --}}
        {{-- TAB 3: RIWAYAT SELESAI (COMPLETED ARCHIVES)                       --}}
        {{-- ================================================================= --}}
        @else
            @if($completedOrders->isEmpty())
                <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 shadow-sm max-w-xl mx-auto my-12">
                    <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto mb-4 border border-amber-100">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-black text-slate-900">Belum Ada Riwayat Riset Selesai</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                        Proyek riset yang telah ditandai 'Selesai' akan diarsipkan di sini beserta riwayat lengkap dan dokumentasinya.
                    </p>
                </div>
            @else
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-black uppercase tracking-wider text-[10px]">
                                <tr>
                                    <th class="py-4 px-5">Nomor SPK</th>
                                    <th class="py-4 px-5">Sepatu &amp; Customer</th>
                                    <th class="py-4 px-5">Invoice &amp; Status</th>
                                    <th class="py-4 px-5">Teknisi PIC</th>
                                    <th class="py-4 px-5">Total Progres</th>
                                    <th class="py-4 px-5">Waktu Selesai</th>
                                    <th class="py-4 px-5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700 font-semibold">
                                @foreach($completedOrders as $order)
                                    @php
                                        $reportToken = $order->rndProgresses->first()?->report_token ?? $order->getOrCreateRndUploadToken();
                                        $photosCount = $order->rndProgresses->whereNotNull('photo_path')->count();
                                    @endphp
                                    <tr class="hover:bg-slate-50/60 transition-colors">
                                        <td class="py-4 px-5">
                                            <div class="flex items-center gap-2">
                                                <span class="font-mono font-black text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                                                    {{ $order->spk_number }}
                                                </span>
                                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase bg-emerald-700 text-white">
                                                    SELESAI
                                                </span>
                                            </div>
                                        </td>
                                        <td class="py-4 px-5">
                                            <div class="font-black text-slate-900">{{ $order->shoe_brand }} - {{ $order->shoe_type ?? 'Sepatu' }}</div>
                                            <div class="text-[11px] text-slate-500">{{ $order->customer_name }}</div>
                                        </td>
                                        <td class="py-4 px-5">
                                            @php
                                                $inv = $order->invoice;
                                                $invNumber = $inv?->invoice_number ?? ($order->invoice_id ? 'INV-#' . $order->invoice_id : null);
                                                $invStatus = $inv?->status ?? 'Belum Ada';
                                                $invDueDate = $inv?->due_date ? \Carbon\Carbon::parse($inv->due_date)->format('d M Y') : ($inv?->estimasi_selesai ? \Carbon\Carbon::parse($inv->estimasi_selesai)->format('d M Y') : null);
                                                $invUrl = $inv?->invoice_full_url ?? $inv?->invoice_akhir_url ?? ($invNumber ? url('/api/invoice_share_grouped.php?token='.urlencode($invNumber).'&type=FULL') : null);
                                                $statusColor = match(strtolower($invStatus)) {
                                                    'lunas' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                                    'dp/cicil', 'dp', 'cicil' => 'bg-amber-50 text-amber-800 border-amber-200',
                                                    'batal' => 'bg-slate-100 text-slate-600 border-slate-300',
                                                    default => 'bg-rose-50 text-rose-800 border-rose-200',
                                                };
                                            @endphp
                                            @if($invNumber)
                                                <div class="flex items-center gap-1.5">
                                                    <span class="font-mono font-black text-slate-800 text-[11px] bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                                        #{{ $invNumber }}
                                                    </span>
                                                    <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase border {{ $statusColor }}">
                                                        {{ $invStatus }}
                                                    </span>
                                                </div>
                                                <div class="text-[10px] text-slate-400 font-semibold mt-1">
                                                    Est: {{ $invDueDate ?? '-' }}
                                                    @if($invUrl)
                                                        &bull; <a href="{{ $invUrl }}" target="_blank" class="text-[#22B086] hover:underline font-bold">Buka Invoice</a>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-slate-400 italic text-[11px]">-</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-5">
                                            <span class="font-extrabold text-slate-800">
                                                {{ $order->technicianProduction?->name ?? ($order->technician_id ? \App\Models\User::find($order->technician_id)?->name : 'Teknisi Lab') }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-5">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 font-bold text-slate-700 text-[11px]">
                                                {{ $photosCount }} Dokumentasi
                                            </span>
                                        </td>
                                        <td class="py-4 px-5 text-slate-500 text-[11px]">
                                            {{ $order->finished_date ? \Carbon\Carbon::parse($order->finished_date)->format('d M Y H:i') : ($order->updated_at ? $order->updated_at->format('d M Y H:i') : '-') }}
                                        </td>
                                        <td class="py-4 px-5 text-right">
                                            <a href="{{ route('rnd.report', $reportToken) }}" target="_blank"
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-[#22B086] border border-emerald-200 font-black text-xs transition-all">
                                                <span>Buka Living Report</span>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $completedOrders->links() }}
                </div>
            @endif
        @endif

    </div>

</div>

{{-- MODAL 1: Upload Progres PC dengan Bahasa & Format Selaras /admin/orders/160 --}}
<div id="modal-upload-pc" style="display: none;" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-xl w-full shadow-2xl border border-slate-200 overflow-hidden max-h-[92vh] flex flex-col">
        {{-- Header Gradasi Hijau Resmi Selaras Admin --}}
        <div class="px-6 py-4 bg-gradient-to-r from-[#22B086] to-[#1C8D6C] text-white flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center text-white shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-black tracking-tight">Tambah Progres R&amp;D</h3>
                    <p id="upload-modal-spk" class="text-xs text-emerald-100 font-medium">Dokumentasikan tahapan eksperimen laboratorium</p>
                </div>
            </div>
            <button type="button" onclick="closeUploadModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center font-bold transition">✕</button>
        </div>

        <form id="form-upload-pc" method="POST" enctype="multipart/form-data" class="p-6 space-y-4 overflow-y-auto" onsubmit="return validateUploadForm()">
            @csrf
            <div>
                <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1.5">Judul Tahap / Aktivitas <span class="text-rose-500">*</span></label>
                <input type="text" name="stage_title" required placeholder="Contoh: Uji Rekat Formula Sol B" 
                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 text-xs font-bold focus:bg-white focus:ring-2 focus:ring-[#22B086] focus:border-[#22B086]">
            </div>

            {{-- Photo Upload with Client-Side Auto-Compression --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-black text-slate-700 uppercase tracking-wider">Foto Bukti Eksperimen <span class="text-rose-500">*</span></label>
                    <span class="text-[10px] text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 font-semibold">Auto-Kompres WebP</span>
                </div>

                {{-- Dropzone / Picker State --}}
                <div id="dropzone-box" onclick="document.getElementById('input-pc-photo').click()"
                     class="border-2 border-dashed border-slate-300 hover:border-[#22B086] bg-slate-50 hover:bg-emerald-50/30 rounded-2xl p-5 text-center cursor-pointer transition group">
                    <div class="w-10 h-10 mx-auto rounded-full bg-emerald-50 text-[#22B086] flex items-center justify-center mb-2 group-hover:scale-110 transition shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-slate-800 block">Pilih File Foto dari Komputer</span>
                    <span class="text-[10px] text-slate-500 mt-0.5 block">Format JPG, PNG, atau WebP (Otomatis dikompresi sebelum diunggah)</span>
                </div>

                <input type="file" id="input-pc-photo" accept="image/*" onchange="handleImageSelection(event)" class="hidden">
                <input type="file" id="input-compressed-photo" name="photo" class="hidden">

                {{-- Status & Preview Bar --}}
                <div id="compression-status" class="mt-2 text-[11px] font-bold text-slate-500 hidden">
                    <span id="compression-text">Mengompres foto ke WebP...</span>
                </div>

                <div id="preview-container" style="display: none;" class="mt-3 rounded-2xl overflow-hidden border border-slate-200 bg-slate-900 aspect-video max-h-56 flex items-center justify-center relative shadow-xs">
                    <img id="preview-image" src="" alt="Preview" class="w-full h-full object-contain">
                    <button type="button" onclick="document.getElementById('input-pc-photo').click()" 
                            class="absolute top-2.5 right-2.5 px-2.5 py-1 rounded-lg bg-rose-600/90 hover:bg-rose-600 text-white text-[11px] font-bold backdrop-blur flex items-center gap-1 shadow-md transition">
                        <span>Ganti Foto</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Hasil Evaluasi (Grid Radio 2x2 Persis Admin) --}}
            <div>
                <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1.5">Hasil Evaluasi <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <label class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer hover:border-[#22B086] transition">
                        <input type="radio" name="result_status" value="SUCCESS" checked class="text-[#22B086] focus:ring-0">
                        <span class="font-bold text-emerald-800">Berhasil</span>
                    </label>
                    <label class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer hover:border-[#22B086] transition">
                        <input type="radio" name="result_status" value="IN_PROGRESS" class="text-[#22B086] focus:ring-0">
                        <span class="font-bold text-blue-800">Sedang Berjalan</span>
                    </label>
                    <label class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer hover:border-[#22B086] transition">
                        <input type="radio" name="result_status" value="NEED_REVISION" class="text-[#22B086] focus:ring-0">
                        <span class="font-bold text-amber-800">Perlu Revisi</span>
                    </label>
                    <label class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer hover:border-[#22B086] transition">
                        <input type="radio" name="result_status" value="FAILED" class="text-[#22B086] focus:ring-0">
                        <span class="font-bold text-rose-800">Gagal / Rusak</span>
                    </label>
                </div>
            </div>

            {{-- Catatan Formula & Parameter --}}
            <div>
                <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1.5">Catatan Formula &amp; Parameter</label>
                <textarea name="notes" rows="3" placeholder="Suhu oven, durasi, komposisi lem..." 
                          class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:ring-2 focus:ring-[#22B086] focus:border-[#22B086]"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeUploadModal()" class="px-5 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs transition">
                    Batal
                </button>
                <button type="submit" id="btn-submit-upload" class="px-6 py-2.5 rounded-xl bg-[#22B086] hover:bg-[#1C8D6C] text-white font-bold text-xs shadow-md shadow-emerald-200 transition flex items-center gap-2 active:scale-95">
                    <span>Simpan Progres</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 2: Scan QR Code Smartphone (Ukuran Besar & Jarak Jauh) --}}
<div id="modal-qr" style="display: none;" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border border-slate-200 text-center space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#22B086]"></span>
                    <span>Scan Kamera Smartphone</span>
                </h3>
                <p class="text-xs text-slate-400 font-semibold mt-0.5 text-left">Pindai dari jarak jauh dengan kamera HP teknisi</p>
            </div>
            <button type="button" onclick="closeQrModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center font-bold">✕</button>
        </div>

        {{-- Huge QR Code Display (280px) --}}
        <div id="qr-code-container" class="bg-white p-5 rounded-3xl border-2 border-slate-200 shadow-md inline-block">
            <img id="qr-image" src="" alt="QR Code" class="w-72 h-72 mx-auto object-contain">
        </div>

        <div class="text-xs font-mono font-bold text-slate-700 bg-slate-100 p-2.5 rounded-xl break-all" id="qr-url-text">
            -
        </div>

        <div class="grid grid-cols-2 gap-2 pt-1">
            <button type="button" onclick="copyQrUrl()" class="py-3 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-black transition-all flex items-center justify-center gap-2">
                <svg class="w-4 h-4 text-[#FFC232]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                </svg>
                <span id="copy-btn-text">Salin Tautan</span>
            </button>
            <a id="btn-open-mobile-tab" href="#" target="_blank" class="py-3 px-4 rounded-xl bg-[#22B086] hover:bg-emerald-600 text-white text-xs font-black transition-all flex items-center justify-center gap-1.5 shadow-xs">
                <span>Buka Form HP</span>
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
            </a>
        </div>
    </div>
</div>

{{-- JAVASCRIPT LOGIC --}}
<script>
    // 0. Sub-Stations Data Map & Stage Definitions
    window.rndSubStations = @json($rndSubStations);

    // 1. Dynamic Sub-Stations Grid Renderer per Stage
    function renderSubStations(orderId, stage) {
        const container = document.getElementById(`substations-container-${orderId}`);
        const label = document.getElementById(`tech-stage-label-${orderId}`);
        const grid = document.getElementById(`substations-grid-${orderId}`);
        if (!container || !grid) return;

        if (label) label.textContent = stage;
        container.setAttribute('data-current-stage', stage);

        if (stage === 'SORTIR') {
            grid.innerHTML = `
                <div class="py-3 px-3 bg-slate-100 rounded-xl text-xs text-slate-500 font-bold border border-slate-200 flex items-center justify-center gap-2 text-center">
                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Tahap Sortir Material (Tanpa Penugasan Teknisi)</span>
                </div>
            `;
            return;
        }

        let assignments = {};
        try {
            assignments = JSON.parse(container.getAttribute('data-sub-assignments') || '{}');
        } catch(e) {
            assignments = {};
        }

        const subList = window.rndSubStations[stage] || {};
        let cardsHtml = '<div class="grid grid-cols-1 sm:grid-cols-3 gap-2">';

        for (const [subKey, subData] of Object.entries(subList)) {
            const assignedVal = assignments[subKey] || '';
            const isUnneeded = (assignedVal === 'none');
            const cardBg = isUnneeded 
                ? 'bg-slate-100/90 border-dashed border-slate-300' 
                : 'bg-white border-slate-200/90 shadow-2xs hover:border-[#22B086]';
            const badgeClass = isUnneeded 
                ? 'bg-slate-200 text-slate-500' 
                : (assignedVal ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-400');
            const badgeText = isUnneeded ? 'Bypass' : (assignedVal ? 'Aktif' : 'Antre');

            let optionsHtml = `
                <option value="">-- Belum Ditugaskan --</option>
                <option value="none" ${isUnneeded ? 'selected' : ''} class="font-bold text-slate-400 bg-slate-50">-- Tidak Diperlukan --</option>
            `;

            (subData.techs || []).forEach(t => {
                const isSel = (String(t.id) === String(assignedVal)) ? 'selected' : '';
                optionsHtml += `<option value="${t.id}" ${isSel}>${t.name}</option>`;
            });

            cardsHtml += `
                <div class="p-2 rounded-xl border transition-all flex flex-col justify-between ${cardBg}"
                     id="subcard-${orderId}-${subKey}">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-[10px] font-black text-slate-800 truncate" title="${subData.label}">
                            ${subData.label}
                        </span>
                        <span id="badge-${orderId}-${subKey}" 
                              class="text-[8px] font-black px-1.5 py-0.5 rounded uppercase tracking-wider ${badgeClass}">
                            ${badgeText}
                        </span>
                    </div>
                    <select onchange="assignSubStation('${orderId}', '${subKey}', this.value)"
                            id="select-${orderId}-${subKey}"
                            class="w-full px-2 py-1 bg-white border border-slate-300 rounded-lg text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#22B086] focus:border-[#22B086] transition-all">
                        ${optionsHtml}
                    </select>
                </div>
            `;
        }

        cardsHtml += '</div>';
        grid.innerHTML = cardsHtml;
    }

    // 2. Dynamic Stage Action & Interactive Stepper Updates
    const stageTransitions = {
        'PREPARATION': { next: 'SORTIR', label: 'Lanjut ke Sortir', color: 'bg-indigo-600 hover:bg-indigo-700 text-white' },
        'SORTIR':      { next: 'PRODUCTION', label: 'Lanjut ke Produksi', color: 'bg-blue-600 hover:bg-blue-700 text-white' },
        'PRODUCTION':  { next: 'QC', label: 'Lanjut ke QC', color: 'bg-teal-600 hover:bg-teal-700 text-white' },
        'QC':          { next: 'FINISH', label: 'Selesaikan Riset (Final)', color: 'bg-[#22B086] hover:bg-emerald-600 text-white' }
    };

    function renderStageActionButton(orderId, currentStage) {
        const container = document.getElementById(`stage-action-container-${orderId}`);
        if (!container) return;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        const action = stageTransitions[currentStage] || stageTransitions['QC'];

        if (action.next === 'FINISH') {
            const spkNumber = container.getAttribute('data-spk') || '';
            container.innerHTML = `
                <form action="/workshop/rnd/${orderId}/complete" method="POST" 
                      onsubmit="return confirm('Apakah Anda yakin ingin menyelesaikan proyek riset SPK #${spkNumber}? Status akan diubah menjadi SELESAI.');">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <button type="submit" 
                            class="py-2 px-4 rounded-xl bg-[#22B086] hover:bg-emerald-600 text-white text-xs font-black flex items-center gap-1.5 transition-all shadow-xs active:scale-95">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Selesaikan Riset (Final)</span>
                    </button>
                </form>
            `;
        } else {
            container.innerHTML = `
                <button type="button" 
                        onclick="advanceStageFromButton('${orderId}', '${action.next}')"
                        class="py-2 px-4 rounded-xl ${action.color} text-xs font-black flex items-center gap-1.5 transition-all shadow-xs active:scale-95">
                    <span>${action.label}</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            `;
        }
    }

    function advanceStageFromButton(orderId, nextStage) {
        const stepperContainer = document.getElementById(`stepper-buttons-${orderId}`);
        const targetBtn = stepperContainer ? stepperContainer.querySelector(`button[data-stage-key="${nextStage}"]`) : null;
        updateOrderStage(orderId, nextStage, targetBtn);
    }

    function updateOrderStage(orderId, newStage, btnEl) {
        if (!confirm('Pindahkan tahapan SPK ke ' + newStage + '?')) return;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        // A. Immediately update visual stepper buttons
        const btnContainer = btnEl?.closest('.grid-cols-4') || document.getElementById(`stepper-buttons-${orderId}`);
        if (btnContainer) {
            btnContainer.querySelectorAll('button').forEach(btn => {
                btn.className = "py-2.5 px-1 text-[10px] rounded-xl transition-all duration-200 flex flex-col items-center justify-center text-center bg-white hover:bg-slate-200 text-slate-700 font-extrabold border border-slate-200";
            });
            const activeBtn = btnEl || btnContainer.querySelector(`button[data-stage-key="${newStage}"]`);
            if (activeBtn) {
                activeBtn.className = "py-2.5 px-1 text-[10px] rounded-xl transition-all duration-200 flex flex-col items-center justify-center text-center bg-[#22B086] text-white shadow-sm ring-2 ring-emerald-400/40 font-black";
            }
        }

        // B. Update stage badge in table row
        const rowStageText = document.getElementById(`row-stage-text-${orderId}`);
        if (rowStageText) rowStageText.textContent = newStage;

        // C. Render sub-stations grid
        renderSubStations(orderId, newStage);

        // D. Update dynamic action button (Opsi B: Lanjut ke Stage Berikutnya atau Selesaikan)
        renderStageActionButton(orderId, newStage);

        // E. Send AJAX request to server
        fetch(`/workshop/rnd/${orderId}/stage`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ stage: newStage })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Update summary row PIC name
                const rowPicName = document.getElementById(`row-pic-name-${orderId}`);
                if (rowPicName) {
                    rowPicName.textContent = data.pic_name || 'Belum Ditugaskan';
                    rowPicName.className = data.pic_name ? 'font-bold text-slate-800' : 'font-bold text-slate-400 italic';
                }

                // Show status indicator
                const indicator = document.getElementById(`tech-saved-indicator-${orderId}`);
                if (indicator) {
                    indicator.textContent = '✓ Tahap Berpindah';
                    indicator.classList.remove('hidden');
                    setTimeout(() => indicator.classList.add('hidden'), 2500);
                }
            } else {
                alert(data.message || 'Gagal mengubah tahap');
                window.location.reload();
            }
        })
        .catch(err => {
            console.error(err);
            window.location.reload();
        });
    }

    // 3. Sub-Station Assignment via AJAX
    function assignSubStation(orderId, subKey, value) {
        const container = document.getElementById(`substations-container-${orderId}`);
        const stage = container ? container.getAttribute('data-current-stage') : '';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        const indicator = document.getElementById(`tech-saved-indicator-${orderId}`);
        const rowPicName = document.getElementById(`row-pic-name-${orderId}`);
        const card = document.getElementById(`subcard-${orderId}-${subKey}`);
        const badge = document.getElementById(`badge-${orderId}-${subKey}`);

        // Update local dataset attributes
        if (container) {
            let assignments = {};
            try {
                assignments = JSON.parse(container.getAttribute('data-sub-assignments') || '{}');
            } catch(e) {
                assignments = {};
            }
            assignments[subKey] = value;
            container.setAttribute('data-sub-assignments', JSON.stringify(assignments));
        }

        // Update card visual state immediately
        const isUnneeded = (value === 'none');
        if (card) {
            card.className = `p-2 rounded-xl border transition-all flex flex-col justify-between ${isUnneeded ? 'bg-slate-100/90 border-dashed border-slate-300' : 'bg-white border-slate-200/90 shadow-2xs hover:border-[#22B086]'}`;
        }
        if (badge) {
            badge.className = `text-[8px] font-black px-1.5 py-0.5 rounded uppercase tracking-wider ${isUnneeded ? 'bg-slate-200 text-slate-500' : (value ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-400')}`;
            badge.textContent = isUnneeded ? 'Bypass' : (value ? 'Aktif' : 'Antre');
        }

        fetch(`/workshop/rnd/${orderId}/technician`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ 
                sub_station: subKey,
                technician_id: value,
                stage: stage 
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (indicator) {
                    indicator.textContent = '✓ Tersimpan';
                    indicator.classList.remove('hidden');
                    setTimeout(() => {
                        indicator.classList.add('hidden');
                    }, 2500);
                }

                if (rowPicName && data.summary_pic) {
                    rowPicName.textContent = data.summary_pic;
                    rowPicName.className = (data.summary_pic !== 'Belum Ditugaskan') ? 'font-bold text-slate-800' : 'font-bold text-slate-400 italic';
                }
            }
        })
        .catch(err => console.error(err));
    }

    // 3. Modal Upload PC
    function openUploadModal(orderId, spkNumber) {
        document.getElementById('upload-modal-spk').textContent = `SPK: ${spkNumber}`;
        document.getElementById('form-upload-pc').action = `/workshop/rnd/${orderId}/progress`;
        const btn = document.getElementById('btn-submit-upload');
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<span>Simpan Progres</span>';
        }
        document.getElementById('modal-upload-pc').style.display = 'flex';
    }

    function closeUploadModal() {
        document.getElementById('modal-upload-pc').style.display = 'none';
        document.getElementById('form-upload-pc').reset();
        document.getElementById('preview-container').style.display = 'none';
        document.getElementById('compression-status').classList.add('hidden');
        const btn = document.getElementById('btn-submit-upload');
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<span>Simpan Progres</span>';
        }
    }

    function validateUploadForm() {
        const compressedInput = document.getElementById('input-compressed-photo');
        const rawInput = document.getElementById('input-pc-photo');
        
        if (!compressedInput.files || compressedInput.files.length === 0) {
            if (rawInput.files && rawInput.files.length > 0) {
                compressedInput.files = rawInput.files;
            } else {
                alert('Silakan pilih foto bukti eksperimen terlebih dahulu.');
                return false;
            }
        }
        
        const btn = document.getElementById('btn-submit-upload');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `
                <svg class="animate-spin w-4 h-4 text-white inline-block" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Menyimpan...</span>
            `;
        }
        return true;
    }

    // 4. Client-Side WebP Compression (Max 1600px, 0.82 Quality)
    function handleImageSelection(event) {
        const file = event.target.files[0];
        if (!file) return;

        const statusEl = document.getElementById('compression-status');
        const statusText = document.getElementById('compression-text');
        const previewContainer = document.getElementById('preview-container');
        const previewImage = document.getElementById('preview-image');
        const compressedInput = document.getElementById('input-compressed-photo');

        statusEl.classList.remove('hidden');
        statusText.textContent = `Mengompres foto asli (${(file.size / 1024 / 1024).toFixed(2)} MB)...`;

        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                const canvas = document.createElement('canvas');
                let width = img.width;
                let height = img.height;
                const maxDim = 1600;

                if (width > maxDim || height > maxDim) {
                    if (width > height) {
                        height = Math.round(height * (maxDim / width));
                        width = maxDim;
                    } else {
                        width = Math.round(width * (maxDim / height));
                        height = maxDim;
                    }
                }

                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                canvas.toBlob(function(blob) {
                    if (!blob) {
                        statusText.textContent = 'Gagal memproses gambar.';
                        return;
                    }

                    const originalSizeMB = (file.size / 1024 / 1024).toFixed(2);
                    const compressedSizeMB = (blob.size / 1024 / 1024).toFixed(2);
                    statusText.innerHTML = `<span class="text-emerald-700 font-black">Kompresi WebP Sukses: ${originalSizeMB} MB ➔ ${compressedSizeMB} MB</span>`;

                    const compressedFile = new File([blob], file.name.replace(/\.[^/.]+$/, "") + ".webp", {
                        type: 'image/webp',
                        lastModified: Date.now()
                    });

                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(compressedFile);
                    compressedInput.files = dataTransfer.files;

                    previewImage.src = URL.createObjectURL(blob);
                    previewContainer.style.display = 'flex';
                }, 'image/webp', 0.82);
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    // 5. Modal QR Code Smartphone (300px QR Image)
    let currentQrUrl = '';
    function openQrModal(spkNumber, uploadUrl) {
        currentQrUrl = uploadUrl;
        document.getElementById('qr-url-text').textContent = uploadUrl;
        
        const qrImage = document.getElementById('qr-image');
        qrImage.src = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(uploadUrl)}`;
        
        const openTabBtn = document.getElementById('btn-open-mobile-tab');
        if (openTabBtn) openTabBtn.href = uploadUrl;
        
        document.getElementById('modal-qr').style.display = 'flex';
    }

    function closeQrModal() {
        document.getElementById('modal-qr').style.display = 'none';
    }

    function copyQrUrl() {
        if (!currentQrUrl) return;
        navigator.clipboard.writeText(currentQrUrl).then(() => {
            const btnText = document.getElementById('copy-btn-text');
            btnText.textContent = 'Tersalin!';
            setTimeout(() => {
                btnText.textContent = 'Salin Tautan';
            }, 2000);
        });
    }
</script>
</x-workshop-pwa-layout>
