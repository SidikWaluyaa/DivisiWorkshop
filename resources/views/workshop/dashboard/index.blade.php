<x-app-layout>
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
                                Live Monitoring
                            </div>
                            <h1 class="text-4xl lg:text-5xl font-black text-white tracking-tight">
                                Workshop Dashboard
                            </h1>
                            <p class="text-teal-100 text-lg font-medium">
                                Metrik Performansi & Analitik Operasional
                            </p>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
                            {{-- Date Filter --}}
                            <form action="{{ route('workshop.dashboard') }}" method="GET" class="flex items-center gap-2 bg-white/15 backdrop-blur-md px-4 py-3 rounded-xl border border-white/20 shadow-lg">
                                <svg class="w-5 h-5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <input type="date" name="start_date" value="{{ $filterStartDate }}" 
                                    class="bg-transparent border-none text-white text-sm focus:ring-0 cursor-pointer font-medium placeholder-white/50"
                                    onchange="this.form.submit()">
                                <span class="text-white/60">—</span>
                                <input type="date" name="end_date" value="{{ $filterEndDate }}" 
                                    class="bg-transparent border-none text-white text-sm focus:ring-0 cursor-pointer font-medium placeholder-white/50"
                                    onchange="this.form.submit()">
                            </form>

                            {{-- Export Button --}}
                            <form action="{{ route('workshop.export') }}" method="POST">
                                @csrf
                                <input type="hidden" name="start_date" value="{{ $filterStartDate }}">
                                <input type="hidden" name="end_date" value="{{ $filterEndDate }}">
                                <button type="submit" class="inline-flex items-center gap-2 px-5 py-3 bg-white text-teal-700 rounded-xl font-bold hover:bg-teal-50 transition-all shadow-lg hover:shadow-xl hover:scale-105 duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Export Laporan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

            {{-- KPI Metrics Section --}}
            <section>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    {{-- In Progress --}}
                    <div class="group bg-white rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 border border-teal-100 hover:border-teal-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-teal-100 to-teal-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="text-3xl font-black text-teal-600 mb-1">{{ $inProgress }}</div>
                        <div class="text-xs font-bold text-teal-500 uppercase tracking-wider">Diproses</div>
                    </div>

                    {{-- Completed --}}
                    <div class="group bg-white rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-gray-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="text-3xl font-black text-gray-800 mb-1">{{ $throughput }}</div>
                        <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Selesai</div>
                    </div>

                    {{-- Urgent --}}
                    <div class="group bg-white rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 border border-red-100 hover:border-red-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-red-100 to-red-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="text-3xl font-black text-red-600 mb-1">{{ $urgentCount }}</div>
                        <div class="text-xs font-bold text-red-500 uppercase tracking-wider">Mendesak</div>
                    </div>

                    {{-- QC Pass Rate --}}
                    <div class="group bg-white rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 border border-orange-100 hover:border-orange-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-orange-100 to-orange-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="text-3xl font-black text-orange-600 mb-1">{{ $qcPassRate }}%</div>
                        <div class="text-xs font-bold text-orange-500 uppercase tracking-wider">Lolos QC</div>
                    </div>

                    {{-- Capacity --}}
                    <div class="group bg-white rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-gray-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="text-3xl font-black text-gray-800 mb-1">{{ $capacityUtilization }}</div>
                        <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Kapasitas</div>
                    </div>

                    {{-- Revenue --}}
                    <div class="group bg-gradient-to-br from-teal-500 to-orange-500 rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="text-3xl font-black text-white mb-1">Rp {{ number_format($revenue/1000, 0) }}k</div>
                        <div class="text-xs font-bold text-white/90 uppercase tracking-wider">Pendapatan</div>
                    </div>
                </div>
            </section>

            {{-- SPK Matrix Section --}}
            <section>
                @include('workshop.dashboard.partials.spk-matrix', ['matrixData' => $matrixData])
            </section>

            {{-- Charts Section --}}
            <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- Completion Trend --}}
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-teal-50 px-6 py-5 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-orange-500 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-gray-800">Tren Penyelesaian</h3>
                                    <p class="text-xs text-gray-500 font-medium">Daily completion tracking</p>
                                </div>
                            </div>
                            <span class="px-3 py-1.5 bg-white rounded-lg text-xs font-bold text-gray-600 shadow-sm border border-gray-200">
                                {{ \Carbon\Carbon::parse($filterStartDate)->format('d M') }} - {{ \Carbon\Carbon::parse($filterEndDate)->format('d M') }}
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        @php
                            $datasets = [[
                                'label' => 'Order Selesai',
                                'data' => $trendData,
                                'borderColor' => '#14b8a6',
                                'backgroundColor' => 'rgba(20, 184, 166, 0.1)',
                                'fill' => true,
                                'tension' => 0.4
                            ]];
                        @endphp
                        <x-line-chart id="completionChart" :labels="$trendLabels" :datasets="$datasets" />
                    </div>
                </div>

                {{-- Deadline Distribution --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-orange-50 px-6 py-5 border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-teal-500 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-gray-800">Tenggat Waktu</h3>
                                <p class="text-xs text-gray-500 font-medium">Deadline status</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="relative h-64">
                            <x-donut-chart 
                                id="deadlineChart" 
                                :labels="['Aman', 'Perlu Perhatian', 'Terlambat']" 
                                :data="[$onTimeOrders, $atRiskOrders, $overdueOrders]" 
                                :colors="['#14b8a6', '#f97316', '#ef4444']" 
                                height="250" />
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <div class="text-center">
                                    <div class="text-4xl font-black bg-gradient-to-r from-teal-600 to-orange-600 bg-clip-text text-transparent">
                                        {{ $inProgress }}
                                    </div>
                                    <div class="text-xs text-gray-400 font-bold uppercase tracking-wider">Aktif</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </section>

            {{-- Operational Monitoring Section --}}
            <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                {{-- Technician Load --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-teal-50 px-6 py-5 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-gray-800">Beban Kerja Teknisi</h3>
                                    <p class="text-xs text-gray-500 font-medium">Current workload distribution</p>
                                </div>
                            </div>
                            <span class="px-3 py-1.5 bg-green-100 text-green-700 rounded-lg text-xs font-bold animate-pulse">
                                ● Live
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        @if($technicianLoad->count() > 0)
                            <x-bar-chart 
                                id="techLoadChart" 
                                :labels="$technicianLoad->pluck('name')" 
                                :data="$technicianLoad->pluck('count')"
                                label="Order Sedang Dikerjakan"
                                color="#14b8a6"
                            />
                        @else
                            <div class="text-center py-12">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-500 text-sm font-medium">Belum ada teknisi aktif</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Recent Activity --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-orange-50 px-6 py-5 border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-gray-800">Aktivitas Terbaru</h3>
                                <p class="text-xs text-gray-500 font-medium">Latest updates</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3 max-h-[300px] overflow-y-auto custom-scrollbar">
                            @forelse($recentLogs as $log)
                            <div class="flex gap-3 p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-all border border-gray-100 hover:border-gray-200">
                                <div class="flex-shrink-0 mt-1">
                                    <div class="w-2 h-2 rounded-full bg-teal-500 ring-4 ring-teal-100"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-bold text-gray-800 mb-1">
                                        {{ $log->user->name ?? 'System' }} 
                                        <span class="font-normal text-gray-500">mengupdate</span> 
                                        <span class="text-teal-600">{{ $log->workOrder?->spk_number ?? 'Unknown' }}</span>
                                    </div>
                                    <div class="text-xs text-gray-600 mb-1 line-clamp-1">{{ $log->description }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $log->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-12">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-500 text-sm font-medium">Belum ada aktivitas</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </section>

            {{-- Station & Performance Section --}}
            <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                {{-- Workload by Station --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-teal-50 px-6 py-5 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-gray-800">Antrian per Stasiun</h3>
                                    <p class="text-xs text-gray-500 font-medium">Current queue status</p>
                                </div>
                            </div>
                            @if($bottleneckCount > 10)
                                <span class="px-3 py-1.5 bg-red-100 text-red-700 rounded-lg text-xs font-bold animate-pulse">
                                    ⚠️ {{ ucfirst($bottleneckStation) }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="p-6 space-y-3">
                        <x-workload-bar label="Asesmen" :count="$workloadByStation['assessment']" :max="30" href="{{ route('assessment.index') }}" />
                        <x-workload-bar label="Preparation" :count="$workloadByStation['preparation']" :max="30" href="{{ route('preparation.index') }}" />
                        <x-workload-bar label="Sortir & Material" :count="$workloadByStation['sortir']" :max="30" href="{{ route('sortir.index') }}" />
                        <x-workload-bar label="Produksi" :count="$workloadByStation['production']" :max="30" href="{{ route('production.index') }}" />
                        <x-workload-bar label="Quality Control" :count="$workloadByStation['qc']" :max="30" href="{{ route('qc.index') }}" />
                    </div>
                </div>

                {{-- Top Performers --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-orange-50 px-6 py-5 border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-gray-800">Teknisi Terbaik</h3>
                                <p class="text-xs text-gray-500 font-medium">Period top performers</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <x-leaderboard :performers="$topPerformers" />
                    </div>
                </div>

            </section>

            {{-- Alerts & Insights Section --}}
            <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Urgent Orders --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-red-50 px-6 py-5 border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-gray-800">Order Mendesak</h3>
                                <p class="text-xs text-gray-500 font-medium">Requires immediate attention</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        @if($urgentOrders->count() > 0)
                            <div class="space-y-3 max-h-96 overflow-y-auto custom-scrollbar">
                                @foreach($urgentOrders as $order)
                                    <div class="p-4 bg-gradient-to-r from-red-50 to-orange-50 rounded-xl border-l-4 border-red-500 hover:shadow-md transition-shadow">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="flex-1">
                                                <div class="font-bold text-gray-800 mb-1">{{ $order->spk_number }}</div>
                                                <div class="text-sm text-gray-600 mb-2">{{ $order->customer_name }}</div>
                                                <span class="inline-block px-2 py-1 bg-white rounded text-xs font-bold text-gray-700">
                                                    {{ $order->status->label() }}
                                                </span>
                                            </div>
                                            <div class="flex flex-col items-end gap-2">
                                                <x-countdown-badge :order="$order" />
                                                @php
                                                    $routeName = match($order->status->value) {
                                                        'ASSESSMENT' => 'assessment.create',
                                                        'PREPARATION' => 'preparation.show',
                                                        'SORTIR' => 'sortir.show',
                                                        'QC' => 'qc.show',
                                                        default => null,
                                                    };
                                                @endphp
                                                @if($routeName)
                                                    <a href="{{ route($routeName, $order->id) }}" class="text-xs font-bold text-teal-600 hover:text-teal-700">Lihat →</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <div class="text-6xl mb-4">🎉</div>
                                <div class="text-gray-500 font-semibold">Tidak ada order mendesak!</div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Alerts & Popular Services --}}
                <div class="space-y-6">
                    
                    {{-- Low Stock Alert --}}
                    @if($lowStockMaterials->count() > 0)
                    <div class="bg-gradient-to-br from-red-50 to-orange-50 rounded-2xl shadow-xl border border-red-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-red-500 to-orange-500 px-6 py-5 border-b border-red-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-white">Stok Menipis</h3>
                                    <p class="text-xs text-red-100 font-medium">Material alerts</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                @foreach($lowStockMaterials as $material)
                                <div class="flex items-center justify-between p-3 bg-white rounded-xl shadow-sm border border-red-100">
                                    <span class="font-bold text-gray-700">{{ $material->name }}</span>
                                    <span class="px-3 py-1.5 bg-red-100 text-red-700 rounded-lg font-bold text-xs">
                                        {{ $material->stock }} {{ $material->unit }}
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Popular Services --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-gray-50 to-orange-50 px-6 py-5 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-gray-800">Layanan Terpopuler</h3>
                                    <p class="text-xs text-gray-500 font-medium">Top services by revenue</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                @foreach($serviceMix as $mix)
                                <div>
                                    <div class="flex justify-between text-sm mb-2">
                                        <span class="font-bold text-gray-700 truncate">{{ $mix->service->name }}</span>
                                        <span class="font-black bg-gradient-to-r from-teal-600 to-orange-600 bg-clip-text text-transparent">
                                            Rp {{ number_format($mix->total_revenue/1000, 0) }}k
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                        <div class="h-2.5 rounded-full bg-gradient-to-r from-teal-500 to-orange-500 transition-all duration-500" 
                                             style="width: {{ min(($mix->order_count / 20) * 100, 100) }}%"></div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>
            </section>

        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, #14b8a6, #f97316);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom, #0d9488, #ea580c);
        }
    </style>
</x-app-layout>
