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
