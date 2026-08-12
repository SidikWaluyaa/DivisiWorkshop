<div class="min-h-screen bg-[#f8fafc] dark:bg-slate-900 pb-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
        
        {{-- Header & Title --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
            <div>
                <nav class="flex items-center gap-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">
                    <span>INVENTARIS WORKSHOP</span>
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                    <span class="text-amber-500 font-black">STAGE SORTIR</span>
                </nav>
                <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Antrean & Klasifikasi Sortir</h1>
                <p class="text-xs font-medium text-slate-500 max-w-lg mt-1">Kelola klasifikasi Bongkar & Belanja bahan baku, integrasi Finlog, serta rute OTW Produksi.</p>
            </div>

            <div class="flex items-center gap-3">
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari SPK atau Customer..." 
                           class="pl-10 pr-4 py-2.5 text-xs border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl focus:ring-2 focus:ring-amber-500/20 shadow-sm w-64 font-bold text-slate-700 dark:text-slate-200">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Interactive Stage Status Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div wire:click="$set('activeTab', 'ready')" class="cursor-pointer p-5 rounded-2xl border transition-all {{ $activeTab == 'ready' ? 'bg-amber-500 text-slate-950 border-amber-500 shadow-lg shadow-amber-500/20 scale-[1.02]' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700 hover:border-amber-400' }}">
                <span class="text-[10px] font-black uppercase tracking-wider opacity-80 block mb-1">1. Antrean Sortir</span>
                <div class="flex items-baseline justify-between">
                    <h3 class="text-3xl font-black tabular-nums">{{ number_format($readyCount) }}</h3>
                    <span class="text-xs font-bold px-2 py-0.5 rounded bg-black/10">PREP Completed</span>
                </div>
            </div>

            <div wire:click="$set('activeTab', 'waiting')" class="cursor-pointer p-5 rounded-2xl border transition-all {{ $activeTab == 'waiting' ? 'bg-purple-600 text-white border-purple-600 shadow-lg shadow-purple-600/20 scale-[1.02]' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700 hover:border-purple-400' }}">
                <span class="text-[10px] font-black uppercase tracking-wider opacity-80 block mb-1">2. Rak Tunggu Belanja (Finlog)</span>
                <div class="flex items-baseline justify-between">
                    <h3 class="text-3xl font-black tabular-nums">{{ number_format($waitingCount) }}</h3>
                    <span class="text-xs font-bold px-2 py-0.5 rounded bg-white/20">REST API / Webhook</span>
                </div>
            </div>

            <div wire:click="$set('activeTab', 'priority')" class="cursor-pointer p-5 rounded-2xl border transition-all {{ $activeTab == 'priority' ? 'bg-rose-600 text-white border-rose-600 shadow-lg shadow-rose-600/20 scale-[1.02]' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700 hover:border-rose-400' }}">
                <span class="text-[10px] font-black uppercase tracking-wider opacity-80 block mb-1">3. OTO & Fast Track</span>
                <div class="flex items-baseline justify-between">
                    <h3 class="text-3xl font-black tabular-nums">{{ number_format($totalCount - $readyCount - $waitingCount) }}</h3>
                    <span class="text-xs font-bold px-2 py-0.5 rounded bg-white/20">Parallel Bypass</span>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block mb-1">4. Total Unit Aktif</span>
                <div class="flex items-baseline justify-between">
                    <h3 class="text-3xl font-black tabular-nums">{{ number_format($totalCount) }}</h3>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Live Intake</span>
                </div>
            </div>
        </div>

        {{-- KPI Metrics Row --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 border border-slate-200 dark:border-slate-700 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">Throughput Harian</span>
                        <span class="text-xl font-black text-slate-900 dark:text-white tabular-nums">{{ $dailyThroughput }}</span>
                        <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 ml-1">SPK hari ini</span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 border border-slate-200 dark:border-slate-700 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center text-blue-600 dark:text-blue-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">Rata-rata Lead Time</span>
                        <span class="text-xl font-black text-slate-900 dark:text-white tabular-nums">{{ $avgLeadTimeHours }}</span>
                        <span class="text-[10px] font-bold text-blue-600 dark:text-blue-400 ml-1">jam di Sortir</span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 border border-slate-200 dark:border-slate-700 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl {{ $bottleneckCount > 5 ? 'bg-rose-100 dark:bg-rose-900/40' : 'bg-amber-100 dark:bg-amber-900/40' }} flex items-center justify-center {{ $bottleneckCount > 5 ? 'text-rose-600 dark:text-rose-400' : 'text-amber-600 dark:text-amber-400' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">Bottleneck Aktif</span>
                        <span class="text-xl font-black text-slate-900 dark:text-white tabular-nums">{{ $bottleneckCount }}</span>
                        <span class="text-[10px] font-bold {{ $bottleneckCount > 5 ? 'text-rose-600 dark:text-rose-400' : 'text-amber-600 dark:text-amber-400' }} ml-1">FU + Belanja</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rack Capacity Visualization --}}
        @if($racks->isNotEmpty())
        <div x-data="{ showRacks: false }" class="mb-8">
            <button @click="showRacks = !showRacks" class="w-full flex items-center justify-between bg-white dark:bg-slate-800 rounded-2xl p-4 border border-slate-200 dark:border-slate-700 shadow-sm hover:border-amber-400 transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-sm">🗄️</div>
                    <span class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white">Indikator Kapasitas Rak ({{ $racks->count() }} Rak Aktif)</span>
                    @php $overCapRacks = $racks->where('is_over', true)->count(); @endphp
                    @if($overCapRacks > 0)
                    <span class="px-2 py-0.5 bg-rose-100 dark:bg-rose-900/40 text-rose-700 dark:text-rose-300 text-[10px] font-bold rounded-lg border border-rose-200 dark:border-rose-800">{{ $overCapRacks }} OVER CAPACITY</span>
                    @endif
                </div>
                <svg :class="showRacks ? 'rotate-180' : ''" class="w-4 h-4 text-slate-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <div x-show="showRacks" x-transition class="mt-3 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach($racks as $rack)
                @php 
                    $util = $rack['utilization'];
                    $barColor = $util >= 100 ? 'bg-rose-500' : ($util >= 80 ? 'bg-amber-500' : 'bg-emerald-500');
                    $borderColor = $util >= 100 ? 'border-rose-300 dark:border-rose-700' : 'border-slate-200 dark:border-slate-700';
                @endphp
                <div class="p-3 rounded-xl bg-white dark:bg-slate-800 border {{ $borderColor }} shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-black text-slate-900 dark:text-white uppercase">{{ $rack['rack_code'] }}</span>
                        <span class="w-2 h-2 rounded-full {{ $rack['is_over'] ? 'bg-rose-500' : 'bg-emerald-500' }}"></span>
                    </div>
                    <div class="h-1.5 w-full bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden mb-1.5">
                        <div class="h-full {{ $barColor }} rounded-full transition-all" style="width: {{ min($util, 100) }}%"></div>
                    </div>
                    <div class="flex items-baseline justify-between">
                        <span class="text-[10px] font-bold text-slate-500">{{ $rack['current_count'] }}/{{ $rack['capacity'] }}</span>
                        <span class="text-[10px] font-black {{ $rack['is_over'] ? 'text-rose-600' : 'text-slate-400' }}">{{ $util }}%</span>
                    </div>
                    @if($rack['workshop_zone'])
                    <span class="text-[9px] font-bold text-indigo-500 block mt-1">{{ $rack['workshop_zone'] }}</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- SPK List Table --}}
        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            @php
                $orderList = match($activeTab) {
                    'ready' => $readyOrders ?? collect([]),
                    'waiting' => $waitingOrders ?? collect([]),
                    'priority' => $priorityOrders ?? collect([]),
                    default => $allSortirOrders ?? collect([]),
                };
                $waitingIds = $activeTab === 'waiting' ? $orderList->pluck('id')->map(fn($id) => (string)$id)->toArray() : [];
            @endphp

            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 bg-slate-50/50 dark:bg-slate-700/50">
                <div>
                    <h3 class="text-xs font-black uppercase tracking-widest text-slate-900 dark:text-white flex items-center gap-2">
                        <span>DAFTAR ANTREAN SPK SORTIR — {{ strtoupper($activeTab) }}</span>
                    </h3>
                    <span class="text-xs font-bold text-slate-500">Klik baris untuk buka klasifikasi & validasi material</span>
                </div>

                @if($activeTab === 'waiting')
                    <div class="flex items-center gap-2">
                        @if(count($selectedWaitingItems) > 0)
                            <button type="button" wire:click="openBulkPengajuanModal" class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-md shadow-indigo-500/20 transition-all active:scale-95 flex items-center gap-2">
                                <span>📋 Buat 1 Pengajuan Belanja Gabungan ({{ count($selectedWaitingItems) }} SPK Ditandai) ➔</span>
                            </button>
                        @else
                            <button type="button" wire:click="openBulkPengajuanModal" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-400 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-not-allowed" title="Centang SPK terlebih dahulu pada tabel di bawah">
                                <span>📋 Buat Pengajuan Belanja Gabungan (Centang SPK)</span>
                            </button>
                        @endif
                    </div>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-400 text-xs uppercase border-b border-slate-100 dark:border-slate-700">
                        <tr>
                            @if($activeTab === 'waiting')
                            <th class="px-4 py-3.5 text-center w-12 bg-indigo-50/50 dark:bg-indigo-950/20">
                                <div class="flex items-center justify-center gap-1">
                                    <input type="checkbox" wire:click="toggleSelectWaitingAll({{ json_encode($waitingIds) }})" 
                                           {{ count($waitingIds) > 0 && collect($waitingIds)->every(fn($id) => in_array($id, $selectedWaitingItems)) ? 'checked' : '' }}
                                           class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                </div>
                            </th>
                            @endif
                            <th class="px-6 py-3.5">Nomor SPK</th>
                            <th class="px-6 py-3.5">Customer & Sepatu</th>
                            <th class="px-6 py-3.5 text-center">Klasifikasi Sortir</th>
                            @if($activeTab === 'waiting')
                            <th class="px-6 py-3.5 text-center">Status Material</th>
                            @endif
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($orderList as $order)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition {{ in_array((string)$order->id, $selectedWaitingItems) ? 'bg-indigo-50/40 dark:bg-indigo-950/30' : '' }}">
                                @if($activeTab === 'waiting')
                                <td class="px-4 py-4 text-center bg-indigo-50/30 dark:bg-indigo-950/10">
                                    <input type="checkbox" value="{{ $order->id }}" wire:model.live="selectedWaitingItems" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                </td>
                                @endif
                                <td class="px-6 py-4 font-mono font-bold text-slate-900 dark:text-white">
                                    {{ $order->spk_number }}
                                    @if($order->has_active_oto)
                                        <span class="ml-2 px-2 py-0.5 bg-amber-500 text-slate-950 text-[10px] font-black rounded">OTO</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-bold text-slate-800 dark:text-white block">{{ $order->customer_name }}</span>
                                    <span class="text-xs text-slate-500">{{ $order->shoe_brand }} - {{ $order->shoe_type }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        // Pre-compute material status so we can use it in the classification column
                                        $matCountEarly  = $order->materials->count();
                                        $hasRequestedEarly = $order->materials->contains(fn($m) => ($m->pivot->status ?? '') === 'REQUESTED');
                                        $hasAllocatedEarly  = $order->materials->contains(fn($m) => in_array($m->pivot->status ?? '', ['ALLOCATED', 'RECEIVED']));

                                        if ($hasRequestedEarly) {
                                            $matStatusEarly = 'requested';
                                        } elseif ($hasAllocatedEarly) {
                                            $matStatusEarly = 'ready';
                                        } elseif ($order->perlu_belanja === null && $matCountEarly === 0) {
                                            $matStatusEarly = 'unvalidated';
                                        } else {
                                            $matStatusEarly = 'no_material';
                                        }
                                    @endphp
                                    @if($order->perlu_bongkar !== null && $order->perlu_belanja !== null)
                                        <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                            {{-- Badge Bongkar --}}
                                            @if($order->perlu_bongkar)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-bold rounded-lg bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300 border border-orange-200 dark:border-orange-700">
                                                    🔨 Bongkar
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-bold rounded-lg bg-slate-100 text-slate-500 dark:bg-slate-700/60 dark:text-slate-400 border border-slate-200 dark:border-slate-600">
                                                    🔨 Tdk Bongkar
                                                </span>
                                            @endif

                                            {{-- Badge Belanja — disembunyikan jika Material sudah Siap --}}
                                            @if($matStatusEarly !== 'ready')
                                                @if($order->perlu_belanja)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-bold rounded-lg bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300 border border-purple-200 dark:border-purple-700">
                                                        🛒 Belanja
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-bold rounded-lg bg-slate-100 text-slate-500 dark:bg-slate-700/60 dark:text-slate-400 border border-slate-200 dark:border-slate-600">
                                                        🛒 Tdk Belanja
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    @else
                                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 border border-amber-200">
                                            ⏳ Belum Diisi
                                        </span>
                                    @endif
                                </td>
                                @if($activeTab === 'waiting')
                                <td class="px-6 py-4 text-center">
                                    @php
                                        // Reuse $matStatusEarly already computed in the Klasifikasi column above
                                        $matStatus = $matStatusEarly;
                                    @endphp

                                    @if($matStatus === 'requested')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300 border border-purple-200 dark:border-purple-700">
                                            🛒 Menunggu Belanja Finlog
                                        </span>
                                    @elseif($matStatus === 'ready')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-700">
                                            ✅ Material Siap
                                        </span>
                                    @elseif($matStatus === 'unvalidated')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 border border-amber-200 dark:border-amber-700">
                                            ⏳ Belum Divalidasi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold rounded-full bg-slate-100 text-slate-500 dark:bg-slate-700/60 dark:text-slate-400 border border-slate-200 dark:border-slate-600">
                                            ℹ️ Tidak Butuh Material
                                        </span>
                                    @endif
                                </td>
                                @endif
                                <td class="px-6 py-4 text-right flex items-center justify-end gap-2">
                                    @if($activeTab === 'waiting')
                                    @php
                                        $hasActiveReq = $order->materialRequests
                                            ? $order->materialRequests->whereIn('status', ['PENDING','APPROVED','PURCHASED'])->count() > 0
                                            : false;
                                    @endphp
                                    @if(!$hasActiveReq)
                                    <button type="button" wire:click="openPengajuanModal({{ $order->id }})" class="px-3 py-2 bg-indigo-100 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-200 font-bold text-xs rounded-xl transition inline-flex items-center gap-1">
                                        📋 Buat Pengajuan
                                    </button>
                                    @else
                                    <span class="px-3 py-2 bg-purple-100 text-purple-700 font-bold text-xs rounded-xl inline-flex items-center gap-1">
                                        🛒 Sudah Diajukan
                                    </span>
                                    @endif
                                    @endif
                                    <button type="button" @click="window.dispatchEvent(new CustomEvent('open-report-modal', { detail: { id: {{ $order->id }} } }))" class="px-3 py-2 bg-rose-100 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 hover:bg-rose-200 font-bold text-xs rounded-xl transition inline-flex items-center gap-1">
                                        ⚠️ Lapor Kendala / FU
                                    </button>
                                    <a href="{{ route('sortir.show', $order->id) }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl shadow-sm transition inline-flex items-center gap-1">
                                        Proses Sortir ➔
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $activeTab === 'waiting' ? 5 : 4 }}" class="px-6 py-12 text-center text-slate-400 text-xs">
                                    Tidak ada antrean SPK pada kategori ini saat ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MASTER REPORT MODAL COMPONENT --}}
    <x-report-modal />

    {{-- MODAL: Buat Pengajuan Belanja Finlog --}}
    @if($showPengajuanModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data x-init="$nextTick(() => document.body.style.overflow='hidden')" x-destroy="document.body.style.overflow=''">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="closePengajuanModal"></div>

        {{-- Modal Panel --}}
        <div class="relative bg-white dark:bg-slate-800 rounded-3xl shadow-2xl w-full max-w-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-indigo-50 dark:bg-indigo-950/30">
                <div>
                    <h3 class="text-sm font-black text-indigo-900 dark:text-indigo-200">📋 Buat Pengajuan Belanja Finlog</h3>
                    <p class="text-xs text-indigo-600 dark:text-indigo-400 mt-0.5">SPK: <span class="font-mono font-bold">{{ $pengajuanSpkNumber }}</span></p>
                </div>
                <button wire:click="closePengajuanModal" class="w-8 h-8 flex items-center justify-center rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    ✕
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5 space-y-4 max-h-[60vh] overflow-y-auto">
                {{-- Material List --}}
                <div>
                    <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-2">Daftar Material yang Diajukan</p>
                    @if(empty($pengajuanMaterials))
                        <div class="py-6 text-center text-slate-400 text-xs bg-slate-50 dark:bg-slate-700/30 rounded-xl">
                            ⚠️ Tidak ada material REQUESTED untuk SPK ini.
                        </div>
                    @else
                        <div class="space-y-2">
                            @foreach($pengajuanMaterials as $mat)
                            <div class="flex items-center justify-between bg-slate-50 dark:bg-slate-700/40 rounded-xl px-4 py-2.5">
                                <div>
                                    <span class="text-xs font-bold text-slate-800 dark:text-white block">{{ $mat['name'] }}</span>
                                    <span class="text-[10px] text-slate-400 block">{{ $mat['sub_category'] }}</span>
                                    @if(!empty($mat['spk_number']))
                                        <span class="mt-1 inline-flex items-center gap-1 px-2 py-0.5 bg-indigo-100 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 text-[10px] font-black rounded-md border border-indigo-200 dark:border-indigo-800">
                                            📦 SPK #{{ $mat['spk_number'] }} — {{ $mat['customer_name'] ?? '' }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-black text-indigo-600 dark:text-indigo-400">{{ $mat['quantity'] }} {{ $mat['unit'] }}</span>
                                    @if(($mat['price'] ?? 0) > 0)
                                    <span class="text-[10px] text-slate-400 block">~Rp {{ number_format($mat['price'] * $mat['quantity'], 0, ',', '.') }}</span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>

                        {{-- Total Estimasi --}}
                        @php
                            $totalEstimasi = collect($pengajuanMaterials)->sum(fn($m) => ($m['price'] ?? 0) * ($m['quantity'] ?? 1));
                        @endphp
                        @if($totalEstimasi > 0)
                        <div class="mt-3 flex justify-between items-center px-4 py-2 bg-indigo-50 dark:bg-indigo-950/30 rounded-xl border border-indigo-100 dark:border-indigo-800">
                            <span class="text-xs font-bold text-indigo-700 dark:text-indigo-300">Total Estimasi Biaya</span>
                            <span class="text-sm font-black text-indigo-800 dark:text-indigo-200">Rp {{ number_format($totalEstimasi, 0, ',', '.') }}</span>
                        </div>
                        @endif
                    @endif
                </div>

                {{-- Notes --}}
                <div>
                    <label class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-1.5">Catatan (opsional)</label>
                    <textarea wire:model="pengajuanNotes" rows="2" placeholder="Mis: warna khusus, ukuran, spesifikasi tambahan..." class="w-full text-sm border border-slate-200 dark:border-slate-600 rounded-xl px-3 py-2 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none"></textarea>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
                <button wire:click="closePengajuanModal" class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition">
                    Batal
                </button>
                <button wire:click="submitPengajuan" wire:loading.attr="disabled" @disabled(empty($pengajuanMaterials)) class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs rounded-xl shadow-sm transition disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center gap-2">
                    <span wire:loading.remove wire:target="submitPengajuan">📤 Kirim ke Finlog</span>
                    <span wire:loading wire:target="submitPengajuan">⏳ Memproses...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
