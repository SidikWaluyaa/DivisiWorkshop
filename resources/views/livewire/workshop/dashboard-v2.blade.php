<div class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-100 to-teal-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        {{-- Premium Header Section --}}
        <section class="relative overflow-hidden bg-gradient-to-br from-teal-600 via-teal-700 to-orange-600 rounded-3xl shadow-2xl">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-orange-500/20 rounded-full blur-3xl"></div>

            <div class="relative px-8 py-10">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <div class="space-y-2">
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-white/90 text-xs font-bold mb-2">
                            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                            Live Monitoring • Livewire V3
                        </div>
                        <h1 class="text-4xl lg:text-5xl font-black text-white tracking-tight">
                            Workshop Dashboard
                        </h1>
                        <p class="text-teal-100 text-lg font-medium">
                            Metrik Performansi & Analitik Operasional (Real-time)
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
                        {{-- Preset Buttons --}}
                        <div class="flex gap-1 bg-white/10 backdrop-blur-md rounded-xl p-1 border border-white/20">
                            @foreach([
                                'today' => 'Hari Ini',
                                'week' => '7 Hari',
                                'month' => 'Bulan Ini',
                                '3month' => '3 Bulan',
                            ] as $key => $label)
                            <button wire:click="applyPreset('{{ $key }}')"
                                class="px-3 py-2 rounded-lg text-xs font-bold transition-all duration-200
                                {{ $preset === $key ? 'bg-white text-teal-700 shadow-lg' : 'text-white/80 hover:bg-white/20' }}">
                                {{ $label }}
                            </button>
                            @endforeach
                        </div>

                        {{-- Date Inputs --}}
                        <div class="flex items-center gap-2 bg-white/15 backdrop-blur-md px-4 py-3 rounded-xl border border-white/20 shadow-lg">
                            <svg class="w-5 h-5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <input type="date" wire:model.live="startDate"
                                class="bg-transparent border-none text-white text-sm focus:ring-0 cursor-pointer font-medium w-32">
                            <span class="text-white/60">—</span>
                            <input type="date" wire:model.live="endDate"
                                class="bg-transparent border-none text-white text-sm focus:ring-0 cursor-pointer font-medium w-32">
                        </div>

                        {{-- Link to V1 --}}
                        <a href="{{ route('workshop.dashboard') }}"
                           class="inline-flex items-center gap-2 px-4 py-3 bg-white/15 backdrop-blur-md text-white rounded-xl font-bold text-xs hover:bg-white/25 transition-all border border-white/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Dashboard V1
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- Fast Track KPI Analytics Section --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-5 mb-2">
            {{-- Total SPK Fast Track Card --}}
            <div wire:click="openDetailModal('total_fast_track')" 
                 class="cursor-pointer bg-gradient-to-br from-teal-500 to-emerald-600 rounded-3xl p-6 text-white shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
                <div class="absolute right-4 top-4 opacity-10 group-hover:opacity-20 group-hover:scale-110 transition-all duration-300">
                    <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M13.13 2.18a10.02 10.02 0 0 0-3.3 0C8.16 2.45 6.47 3.51 5.3 5.03c-.27.35-.38.82-.26 1.25.1.34.25.66.45.95l1.66 2.37-1.42 1.42c-.2.2-.28.5-.22.78.07.28.27.52.54.61l4.02 1.34 1.34 4.02c.09.27.33.47.61.54l.28.01c.21 0 .42-.08.57-.23l1.42-1.42 2.37 1.66c.29.2.61.35.95.45.43.12.9-.01 1.25-.28 1.52-1.17 2.58-2.86 2.85-4.83a10.02 10.02 0 0 0 0-3.3c-.27-1.97-1.33-3.66-2.85-4.83a1.734 1.734 0 0 0-1.25-.28c-.34.1-.66.25-.95.45l-2.37 1.66-1.42-1.42c-.15-.15-.36-.23-.57-.23zm-.13 6.82a2 2 0 1 1 0 4 2 2 0 0 1 0-4z"/>
                    </svg>
                </div>
                <div class="space-y-1 relative z-10">
                    <span class="block text-xs font-bold text-teal-100 uppercase tracking-wider">Total Fast Track</span>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-black">{{ $this->fastTrackData['totalCount'] }}</span>
                        <span class="text-xs text-teal-100">SPK</span>
                    </div>
                    <span class="block text-[10px] text-teal-100/80 pt-2 font-medium">🚀 ({{ $this->fastTrackData['activeTotal'] }} Aktif, {{ $this->fastTrackData['finishedTotal'] }} Selesai)</span>
                </div>
            </div>

            {{-- Total Revenue Card --}}
            <div wire:click="openDetailModal('total_revenue')" 
                 class="cursor-pointer bg-gradient-to-br from-indigo-500 to-blue-600 rounded-3xl p-6 text-white shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
                <div class="absolute right-4 top-4 opacity-10 group-hover:opacity-20 group-hover:scale-110 transition-all duration-300">
                    <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H7c0-2.76 2.24-5 5-5s5 2.24 5 5c0 1.04-.42 1.99-1.07 2.75z"/>
                    </svg>
                </div>
                <div class="space-y-1 relative z-10">
                    <span class="block text-xs font-bold text-indigo-100 uppercase tracking-wider">Pendapatan Fast Track</span>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl font-black">Rp {{ number_format($this->fastTrackData['totalRevenue'], 0, ',', '.') }}</span>
                    </div>
                    <span class="block text-[10px] text-indigo-100/80 pt-3 font-medium">💰 (Berdasarkan {{ $this->fastTrackData['totalCount'] }} SPK)</span>
                </div>
            </div>

            {{-- Failed SLA Card --}}
            <div wire:click="openDetailModal('failed_fast_track')" 
                 class="cursor-pointer bg-gradient-to-br from-rose-500 to-red-600 rounded-3xl p-6 text-white shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
                <div class="absolute right-4 top-4 opacity-10 group-hover:opacity-20 group-hover:scale-110 transition-all duration-300">
                    <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                    </svg>
                </div>
                <div class="space-y-1 relative z-10">
                    <span class="block text-xs font-bold text-rose-100 uppercase tracking-wider">Fast Track Gagal SLA</span>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-black">{{ $this->fastTrackData['failedCount'] }}</span>
                        <span class="text-xs text-rose-100">SPK</span>
                    </div>
                    <span class="block text-[10px] text-rose-100/80 pt-2 font-medium">⚠️ ({{ $this->fastTrackData['activeFailed'] }} Aktif, {{ $this->fastTrackData['finishedFailed'] }} Selesai)</span>
                </div>
            </div>

            {{-- Non-SLA Operational Failed Card --}}
            <div wire:click="openDetailModal('operational_failed_fast_track')" 
                 class="cursor-pointer bg-gradient-to-br from-amber-500 to-orange-600 rounded-3xl p-6 text-white shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
                <div class="absolute right-4 top-4 opacity-10 group-hover:opacity-20 group-hover:scale-110 transition-all duration-300">
                    <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M11 15h2v2h-2zm0-8h2v6h-2zm.99-5C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/>
                    </svg>
                </div>
                <div class="space-y-1 relative z-10">
                    <span class="block text-xs font-bold text-amber-100 uppercase tracking-wider">Gagal Operasional</span>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-black">{{ $this->fastTrackData['operationalFailedCount'] }}</span>
                        <span class="text-xs text-amber-100">SPK</span>
                    </div>
                    <span class="block text-[10px] text-amber-100/80 pt-2 font-medium">🛠️ ({{ $this->fastTrackData['tambahJasaCount'] }} Jasa, {{ $this->fastTrackData['cxFollowUpCount'] }} CX, {{ $this->fastTrackData['batalCount'] }} Batal)</span>
                </div>
            </div>

            {{-- Pending Fast Track Card --}}
            <div wire:click="openDetailModal('pending_fast_track')" 
                 class="cursor-pointer bg-gradient-to-br from-purple-600 to-indigo-700 rounded-3xl p-6 text-white shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
                <div class="absolute right-4 top-4 opacity-10 group-hover:opacity-20 group-hover:scale-110 transition-all duration-300">
                    <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-8-3.59 8-8zm.5-13H11v6l5.2 3.2.8-1.3-4.5-2.7V7z"/>
                    </svg>
                </div>
                <div class="space-y-1 relative z-10">
                    <span class="block text-xs font-bold text-purple-100 uppercase tracking-wider">Pending CS</span>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-black">{{ $this->fastTrackData['pendingCount'] }}</span>
                        <span class="text-xs text-purple-100">SPK</span>
                    </div>
                    <span class="block text-[10px] text-purple-100/90 pt-2 font-medium">⏳ Rp {{ number_format($this->fastTrackData['pendingRevenue'], 0, ',', '.') }}</span>
                </div>
            </div>
        </section>

        {{-- Top Metrics KPI Row --}}
        <section>
            <livewire:workshop.widgets.top-metrics :startDate="$startDate" :endDate="$endDate" wire:key="top-metrics-{{ $startDate }}-{{ $endDate }}" />
        </section>

        {{-- SPK Matrix --}}
        <section>
            <livewire:workshop.widgets.spk-matrix wire:poll.30s wire:key="spk-matrix" />
        </section>

        {{-- Main Trend Chart: Full Width --}}
        <section class="mb-4">
            <livewire:workshop.widgets.production-lead-time-chart :startDate="$startDate" :endDate="$endDate" wire:key="lead-time-chart-{{ $preset }}" />
        </section>

        {{-- Distribution Row: Symmetric 3-Column Layout (1:1:1) --}}
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-4">
            <livewire:workshop.widgets.spk-pipeline-chart :startDate="$startDate" :endDate="$endDate" wire:poll.30s wire:key="pipeline-chart-{{ $preset }}" />
            <livewire:workshop.widgets.service-mix-chart :startDate="$startDate" :endDate="$endDate" wire:key="service-mix-chart-{{ $preset }}" />
            <livewire:workshop.widgets.top-service-names :startDate="$startDate" :endDate="$endDate" wire:key="top-services-{{ $preset }}" />
        </section>

        {{-- Operational Load: Heatmap --}}
        <section class="mb-10">
            <livewire:workshop.widgets.workload-heatmap :startDate="$startDate" :endDate="$endDate" wire:poll.30s wire:key="workload-heatmap-{{ $preset }}" />
        </section>

        {{-- Urgent Actions & Feed --}}
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-8 border-t border-gray-200 pt-8">
            <livewire:workshop.widgets.urgent-action-grid wire:poll.60s wire:key="urgent-grid" />
            <div class="space-y-8">
                <livewire:workshop.widgets.material-alerts wire:poll.120s wire:key="material-alerts" />
                <livewire:workshop.widgets.recent-activity-feed wire:poll.30s wire:key="recent-activity" />
            </div>
        </section>

        {{-- Modal Detail SPK Fast Track --}}
        @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-black/60 backdrop-blur-sm"
             x-transition x-cloak>
            <div class="bg-white dark:bg-gray-900 rounded-[2rem] shadow-2xl border border-gray-100 dark:border-gray-800 w-full max-w-7xl overflow-hidden flex flex-col max-h-[90vh]">
                
                {{-- Modal Header --}}
                <div class="p-5 sm:p-6 bg-gradient-to-r from-teal-600 to-teal-800 text-white flex items-center justify-between">
                    <div>
                        <h3 class="text-lg sm:text-xl font-black tracking-tight">{{ $modalTitle }}</h3>
                        <p class="text-xs text-teal-100 mt-1">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
                    </div>
                    <button wire:click="closeModal" class="p-1.5 rounded-xl hover:bg-white/20 transition-colors text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4">
                    @if($this->fastTrackData['modalOrders']->isEmpty())
                        <div class="text-center py-12 text-gray-400 dark:text-gray-505">
                            <svg class="w-16 h-16 mx-auto mb-3 opacity-50 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5"/>
                            </svg>
                            <p class="font-bold text-sm">Tidak ada data SPK yang sesuai.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto border border-gray-150 dark:border-gray-800 rounded-2xl shadow-sm">
                            <table class="min-w-full divide-y divide-gray-150 dark:divide-gray-800">
                                <thead class="bg-gray-50 dark:bg-gray-850">
                                    <tr>
                                        <th class="px-4 py-3.5 text-left text-xs font-black text-gray-400 uppercase tracking-wider whitespace-nowrap">No. SPK</th>
                                        <th class="px-4 py-3.5 text-left text-xs font-black text-gray-400 uppercase tracking-wider whitespace-nowrap">Pelanggan</th>
                                        <th class="px-4 py-3.5 text-left text-xs font-black text-gray-400 uppercase tracking-wider whitespace-nowrap">Sepatu</th>
                                        <th class="px-4 py-3.5 text-left text-xs font-black text-gray-400 uppercase tracking-wider whitespace-nowrap">Status Stasiun</th>
                                        <th class="px-4 py-3.5 text-right text-xs font-black text-gray-400 uppercase tracking-wider whitespace-nowrap">Nilai Transaksi</th>
                                        @if($selectedMetric === 'failed_fast_track')
                                            <th class="px-4 py-3.5 text-left text-xs font-black text-gray-400 uppercase tracking-wider whitespace-nowrap">Keterangan SLA Gagal</th>
                                        @elseif($selectedMetric === 'operational_failed_fast_track')
                                            <th class="px-4 py-3.5 text-left text-xs font-black text-gray-400 uppercase tracking-wider whitespace-nowrap">Keterangan Gagal Operasional</th>
                                        @elseif($selectedMetric === 'pending_fast_track')
                                            <th class="px-4 py-3.5 text-left text-xs font-black text-gray-400 uppercase tracking-wider whitespace-nowrap">Status Pending CS</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-150 dark:divide-gray-800 text-xs text-gray-700 dark:text-gray-300 font-medium">
                                    @foreach($this->fastTrackData['modalOrders'] as $order)
                                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-850/50 transition-colors">
                                            <td class="px-4 py-3.5 whitespace-nowrap font-mono font-bold text-teal-600 dark:text-teal-400">
                                                <a href="{{ route('admin.orders.show', $order->id) }}" target="_blank" class="hover:underline">
                                                    {{ $order->spk_number }}
                                                </a>
                                            </td>
                                            <td class="px-4 py-3.5 whitespace-nowrap font-bold text-gray-900 dark:text-white">
                                                {{ $order->customer?->name ?? $order->customer_name }}
                                            </td>
                                            <td class="px-4 py-3.5 whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                {{ $order->shoe_brand }} - {{ $order->shoe_type }}
                                            </td>
                                            <td class="px-4 py-3.5 whitespace-nowrap">
                                                @php
                                                    $statusColor = 'gray';
                                                    if ($order->status->value === 'PREPARATION') $statusColor = 'blue';
                                                    elseif ($order->status->value === 'SORTIR') $statusColor = 'amber';
                                                    elseif ($order->status->value === 'PRODUCTION') $statusColor = 'orange';
                                                    elseif ($order->status->value === 'QC') $statusColor = 'emerald';
                                                    elseif ($order->status->value === 'FINISH' || $order->status->value === 'COMPLETED') $statusColor = 'teal';
                                                @endphp
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-{{ $statusColor }}-50 text-{{ $statusColor }}-700 border border-{{ $statusColor }}-200 dark:bg-{{ $statusColor }}-955/20 dark:text-{{ $statusColor }}-400 dark:border-{{ $statusColor }}-900/30">
                                                    {{ $order->status->value }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3.5 whitespace-nowrap text-right font-bold text-gray-900 dark:text-white">
                                                Rp {{ number_format($order->total_transaksi, 0, ',', '.') }}
                                            </td>
                                            @if($selectedMetric === 'failed_fast_track')
                                                <td class="px-4 py-3.5">
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
                                                            <span class="text-red-600 dark:text-red-400 font-bold">🔴 Prep Overdue: {{ (int) $prepStart->diffInDays($prepEnd) }} Hari (SLA: 1 H)</span>
                                                        @endif
                                                        @if($sortirStart && $sortirEnd && $sortirStart->diffInDays($sortirEnd) > 3)
                                                            <span class="text-red-600 dark:text-red-400 font-bold">🔴 Sortir Overdue: {{ (int) $sortirStart->diffInDays($sortirEnd) }} Hari (SLA: 3 H)</span>
                                                        @endif
                                                        @if($prodStart && $prodEnd && $prodStart->diffInDays($prodEnd) > 4)
                                                            <span class="text-red-600 dark:text-red-400 font-bold">🔴 Prod Overdue: {{ (int) $prodStart->diffInDays($prodEnd) }} Hari (SLA: 4 H)</span>
                                                        @endif
                                                        @if($qcStart && $qcEnd && $qcStart->diffInDays($qcEnd) > 1)
                                                            <span class="text-red-600 dark:text-red-400 font-bold">🔴 QC Overdue: {{ (int) $qcStart->diffInDays($qcEnd) }} Hari (SLA: 1 H)</span>
                                                        @endif
                                                    </div>
                                                </td>
                                            @elseif($selectedMetric === 'operational_failed_fast_track')
                                                <td class="px-4 py-3.5">
                                                    <div class="flex flex-col gap-0.5 text-[10px]">
                                                        @php
                                                            $reason = $order->getNonSlaFailureReason();
                                                        @endphp
                                                        @if($reason === 'TAMBAH_JASA')
                                                            <span class="text-amber-600 dark:text-amber-400 font-bold">🔄 Downgrade: Penambahan Jasa Baru</span>
                                                        @elseif($reason === 'CX_FOLLOWUP')
                                                            <span class="text-purple-600 dark:text-purple-400 font-bold">💬 CX FollowUp: Menunggu Konfirmasi</span>
                                                        @elseif($reason === 'BATAL_DONASI')
                                                            <span class="text-red-600 dark:text-red-400 font-bold">❌ Status Batal / Donasi</span>
                                                        @endif
                                                    </div>
                                                </td>
                                            @elseif($selectedMetric === 'pending_fast_track')
                                                <td class="px-4 py-3.5">
                                                    <span class="text-purple-600 dark:text-purple-400 font-bold text-[10px]">⏳ Menunggu Verifikasi CS / Gudang</span>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- Modal Footer --}}
                <div class="p-6 bg-gray-50 dark:bg-gray-850 border-t border-gray-150 dark:border-gray-850 flex justify-end">
                    <button wire:click="closeModal" class="px-5 py-2.5 bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-300 transition-colors">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
        @endif

    </div>
</div>
