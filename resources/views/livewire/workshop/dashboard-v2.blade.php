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
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-5 mb-2">
            {{-- Total SPK Fast Track Card --}}
            <a href="{{ route('workshop.fast-track.index', ['metric' => 'total_fast_track', 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
               class="bg-gradient-to-br from-teal-500 to-emerald-600 rounded-3xl p-6 text-white shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
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
            </a>

            {{-- Total Revenue Card --}}
            <a href="{{ route('workshop.fast-track.index', ['metric' => 'total_revenue', 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
               class="bg-gradient-to-br from-indigo-500 to-blue-600 rounded-3xl p-6 text-white shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
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
            </a>

            {{-- Failed SLA Card --}}
            <a href="{{ route('workshop.fast-track.index', ['metric' => 'failed_fast_track', 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
               class="bg-gradient-to-br from-rose-500 to-red-600 rounded-3xl p-6 text-white shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
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
            </a>

            {{-- Non-SLA Operational Failed Card --}}
            <a href="{{ route('workshop.fast-track.index', ['metric' => 'operational_failed_fast_track', 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
               class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-3xl p-6 text-white shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
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
                    <span class="block text-[10px] text-amber-100/80 pt-2 font-medium">🛠️ ({{ $this->fastTrackData['cxFollowUpCount'] }} CX, {{ $this->fastTrackData['batalCount'] }} Batal)</span>
                </div>
            </a>

            {{-- Pending Fast Track Card --}}
            <a href="{{ route('workshop.fast-track.index', ['metric' => 'pending_fast_track', 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
               class="bg-gradient-to-br from-purple-600 to-indigo-700 rounded-3xl p-6 text-white shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
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
            </a>

            {{-- Batal / Downgrade Fast Track Card --}}
            <a href="{{ route('workshop.fast-track.index', ['metric' => 'downgraded_fast_track', 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
               class="bg-gradient-to-br from-slate-600 to-gray-700 rounded-3xl p-6 text-white shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
                <div class="absolute right-4 top-4 opacity-10 group-hover:opacity-20 group-hover:scale-110 transition-all duration-300">
                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 13l-7 7-7-7m14-6l-7 7-7-7"></path>
                    </svg>
                </div>
                <div class="space-y-1 relative z-10">
                    <span class="block text-xs font-bold text-slate-100 uppercase tracking-wider">Batal Fast Track</span>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-black">{{ $this->fastTrackData['downgradedCount'] }}</span>
                        <span class="text-xs text-slate-100">SPK</span>
                    </div>
                    <span class="block text-[10px] text-slate-100/80 pt-2 font-medium">📉 (Diturunkan karena tambah jasa)</span>
                </div>
            </a>
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
    </div>
</div>
