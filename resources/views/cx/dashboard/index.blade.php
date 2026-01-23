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
                                Live Analytics
                            </div>
                            <h1 class="text-4xl lg:text-5xl font-black text-white tracking-tight">
                                CX Dashboard
                            </h1>
                            <p class="text-teal-100 text-lg font-medium">
                                Issue Tracking & Customer Satisfaction Analytics
                            </p>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
                            {{-- Date Filter --}}
                            <form action="{{ route('cx.dashboard') }}" method="GET" class="flex items-center gap-2 bg-white/15 backdrop-blur-md px-4 py-3 rounded-xl border border-white/20 shadow-lg">
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

                            {{-- Action Button --}}
                            <a href="{{ route('cx.index') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-white text-teal-700 rounded-xl font-bold hover:bg-teal-50 transition-all shadow-lg hover:shadow-xl hover:scale-105 duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Follow Up List
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            {{-- KPI Metrics Section --}}
            <section>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    {{-- Total Issues --}}
                    <div class="group bg-white rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-gray-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="text-3xl font-black text-gray-800 mb-1">{{ $totalIssues }}</div>
                        <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Issues</div>
                    </div>

                    {{-- Open --}}
                    <div class="group bg-white rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 border border-red-100 hover:border-red-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-red-100 to-red-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="text-3xl font-black text-red-600 mb-1">{{ $openIssues }}</div>
                        <div class="text-xs font-bold text-red-500 uppercase tracking-wider">Open</div>
                    </div>

                    {{-- In Progress --}}
                    <div class="group bg-white rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 border border-orange-100 hover:border-orange-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-orange-100 to-orange-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="text-3xl font-black text-orange-600 mb-1">{{ $inProgressIssues }}</div>
                        <div class="text-xs font-bold text-orange-500 uppercase tracking-wider">In Progress</div>
                    </div>

                    {{-- Resolved --}}
                    <div class="group bg-white rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 border border-teal-100 hover:border-teal-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-teal-100 to-teal-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="text-3xl font-black text-teal-600 mb-1">{{ $resolvedIssues }}</div>
                        <div class="text-xs font-bold text-teal-500 uppercase tracking-wider">Resolved</div>
                    </div>

                    {{-- Avg Response --}}
                    <div class="group bg-white rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-gray-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="text-3xl font-black text-gray-800 mb-1">{{ $avgResponseTime }}h</div>
                        <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Avg Response</div>
                    </div>

                    {{-- Resolution Rate --}}
                    <div class="group bg-gradient-to-br from-teal-500 to-orange-500 rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="text-3xl font-black text-white mb-1">{{ $resolutionRate }}%</div>
                        <div class="text-xs font-bold text-white/90 uppercase tracking-wider">Resolution Rate</div>
                    </div>
                </div>
            </section>

            {{-- Charts Section --}}
            <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- Trend Chart --}}
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
                                    <h3 class="text-lg font-black text-gray-800">Issue Trend Analysis</h3>
                                    <p class="text-xs text-gray-500 font-medium">Daily performance tracking</p>
                                </div>
                            </div>
                            <span class="px-3 py-1.5 bg-white rounded-lg text-xs font-bold text-gray-600 shadow-sm border border-gray-200">
                                {{ \Carbon\Carbon::parse($filterStartDate)->format('d M') }} - {{ \Carbon\Carbon::parse($filterEndDate)->format('d M') }}
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        @php
                            $datasets = [
                                [
                                    'label' => 'Open',
                                    'data' => $trendOpen,
                                    'borderColor' => '#ef4444',
                                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                                    'fill' => true,
                                    'tension' => 0.4
                                ],
                                [
                                    'label' => 'In Progress',
                                    'data' => $trendProgress,
                                    'borderColor' => '#f97316',
                                    'backgroundColor' => 'rgba(249, 115, 22, 0.1)',
                                    'fill' => true,
                                    'tension' => 0.4
                                ],
                                [
                                    'label' => 'Resolved',
                                    'data' => $trendResolved,
                                    'borderColor' => '#14b8a6',
                                    'backgroundColor' => 'rgba(20, 184, 166, 0.1)',
                                    'fill' => true,
                                    'tension' => 0.4
                                ]
                            ];
                        @endphp
                        <x-line-chart id="issueTrendChart" :labels="$trendLabels" :datasets="$datasets" />
                    </div>
                </div>

                {{-- Issue by Category --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-orange-50 px-6 py-5 border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-teal-500 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-gray-800">By Category</h3>
                                <p class="text-xs text-gray-500 font-medium">Issue distribution</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="relative h-64">
                            <x-donut-chart 
                                id="categoryChart" 
                                :labels="$issuesByCategory->pluck('category')" 
                                :data="$issuesByCategory->pluck('count')" 
                                :colors="['#14b8a6', '#f97316', '#6b7280', '#ef4444', '#0d9488']" 
                                height="250" />
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <div class="text-center">
                                    <div class="text-4xl font-black bg-gradient-to-r from-teal-600 to-orange-600 bg-clip-text text-transparent">
                                        {{ $issuesByCategory->sum('count') }}
                                    </div>
                                    <div class="text-xs text-gray-400 font-bold uppercase tracking-wider">Total</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </section>

            {{-- Analytics Section --}}
            <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                {{-- Issue by Source --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-teal-50 px-6 py-5 border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-gray-800">Issue Source</h3>
                                <p class="text-xs text-gray-500 font-medium">Where issues originate</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        @if($issuesBySource->count() > 0)
                            <x-bar-chart 
                                id="sourceChart" 
                                :labels="$issuesBySource->pluck('source')" 
                                :data="$issuesBySource->pluck('count')"
                                label="Issues Reported"
                                color="#14b8a6"
                            />
                        @else
                            <div class="text-center py-12">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-500 text-sm font-medium">No source data available</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Top Resolvers --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-orange-50 px-6 py-5 border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-gray-800">Top Resolvers</h3>
                                <p class="text-xs text-gray-500 font-medium">Team performance leaders</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        @if($topResolvers->count() > 0)
                            <div class="space-y-4">
                                @foreach($topResolvers as $index => $resolver)
                                    <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 transition-colors">
                                        <div class="flex-shrink-0">
                                            @if($index === 0)
                                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-500 flex items-center justify-center shadow-lg">
                                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                </div>
                                            @elseif($index === 1)
                                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center shadow-lg">
                                                    <span class="text-white font-black text-sm">2</span>
                                                </div>
                                            @elseif($index === 2)
                                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-orange-400 to-orange-500 flex items-center justify-center shadow-lg">
                                                    <span class="text-white font-black text-sm">3</span>
                                                </div>
                                            @else
                                                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                                                    <span class="text-gray-600 font-bold text-sm">{{ $index + 1 }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-bold text-gray-800 truncate">{{ $resolver->resolver->name ?? 'Unknown' }}</div>
                                            <div class="w-full bg-gray-100 rounded-full h-2 mt-1.5">
                                                <div class="h-2 rounded-full bg-gradient-to-r from-teal-500 to-orange-500 transition-all duration-500" 
                                                     style="width: {{ min(($resolver->resolved_count / ($topResolvers->max('resolved_count') ?: 1)) * 100, 100) }}%"></div>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div class="text-2xl font-black bg-gradient-to-r from-teal-600 to-orange-600 bg-clip-text text-transparent">
                                                {{ $resolver->resolved_count }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-500 text-sm font-medium">No resolver data available</p>
                            </div>
                        @endif
                    </div>
                </div>

            </section>

            {{-- Activity Section --}}
            <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Recent Issues --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-teal-50 px-6 py-5 border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-gray-800">Recent Activity</h3>
                                <p class="text-xs text-gray-500 font-medium">Latest 15 issues</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3 max-h-[400px] overflow-y-auto custom-scrollbar">
                            @forelse($recentIssues as $issue)
                            <div class="flex gap-3 p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-all border border-gray-100 hover:border-gray-200">
                                <div class="flex-shrink-0 mt-1">
                                    <div class="w-2 h-2 rounded-full {{ $issue->status === 'OPEN' ? 'bg-red-500' : ($issue->status === 'IN_PROGRESS' ? 'bg-orange-500' : 'bg-teal-500') }} ring-4 {{ $issue->status === 'OPEN' ? 'ring-red-100' : ($issue->status === 'IN_PROGRESS' ? 'ring-orange-100' : 'ring-teal-100') }}"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2 mb-1">
                                        <div class="font-bold text-gray-800 text-sm truncate">
                                            {{ $issue->workOrder->spk_number ?? 'Unknown SPK' }}
                                        </div>
                                        <span class="flex-shrink-0 px-2 py-0.5 rounded-md text-[10px] font-bold {{ $issue->status === 'OPEN' ? 'bg-red-100 text-red-700' : ($issue->status === 'IN_PROGRESS' ? 'bg-orange-100 text-orange-700' : 'bg-teal-100 text-teal-700') }}">
                                            {{ $issue->status }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-600 mb-1 line-clamp-1">{{ Str::limit($issue->description, 50) }}</div>
                                    <div class="flex items-center gap-2 text-[10px] text-gray-400">
                                        <span>{{ $issue->created_at->diffForHumans() }}</span>
                                        <span>•</span>
                                        <span class="font-medium">{{ $issue->reporter->name ?? 'System' }}</span>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-12">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-500 text-sm font-medium">No recent issues</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Overdue & Common Problems --}}
                <div class="space-y-6">
                    
                    {{-- Overdue Issues --}}
                    @if($overdueIssues->count() > 0)
                    <div class="bg-gradient-to-br from-red-50 to-orange-50 rounded-2xl shadow-xl border border-red-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-red-500 to-orange-500 px-6 py-5 border-b border-red-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-black text-white">Overdue Issues</h3>
                                        <p class="text-xs text-red-100 font-medium">Requires immediate attention</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1.5 bg-white/20 backdrop-blur-sm rounded-lg text-xs font-black text-white animate-pulse">
                                    {{ $overdueIssues->count() }}
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3 max-h-48 overflow-y-auto custom-scrollbar">
                                @foreach($overdueIssues as $issue)
                                    <div class="p-3 bg-white rounded-xl border-l-4 border-red-500 shadow-sm hover:shadow-md transition-shadow">
                                        <div class="flex items-start justify-between gap-2 mb-1">
                                            <div class="font-bold text-gray-800 text-sm">{{ $issue->workOrder->spk_number ?? 'Unknown' }}</div>
                                            <div class="text-[10px] text-red-600 font-bold bg-red-50 px-2 py-1 rounded-md">
                                                {{ $issue->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                        <div class="text-xs text-gray-600">{{ $issue->category }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Common Problems --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-gray-50 to-orange-50 px-6 py-5 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-gray-800">Common Problems</h3>
                                    <p class="text-xs text-gray-500 font-medium">Top 5 categories</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                @foreach($commonProblems as $problem)
                                <div>
                                    <div class="flex justify-between text-sm mb-2">
                                        <span class="font-bold text-gray-700 truncate">{{ $problem->category }}</span>
                                        <span class="font-black bg-gradient-to-r from-teal-600 to-orange-600 bg-clip-text text-transparent">{{ $problem->count }}x</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                        <div class="h-2.5 rounded-full bg-gradient-to-r from-teal-500 to-orange-500 transition-all duration-500" 
                                             style="width: {{ min(($problem->count / ($commonProblems->max('count') ?: 1)) * 100, 100) }}%"></div>
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
