<x-app-layout>
    <div class="py-12 bg-gray-50/50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- Header with Filter & Actions --}}
            <div class="bg-gradient-to-r from-amber-600 to-orange-600 rounded-2xl p-6 md:p-8 text-white shadow-xl">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <h1 class="text-3xl font-black mb-2">📞 Dashboard Customer Experience</h1>
                        <p class="text-amber-100">Issue Tracking & Customer Satisfaction Analytics</p>
                    </div>
                    
                    <div class="flex flex-col md:flex-row gap-4 items-end md:items-center">
                        {{-- Date Filter Form --}}
                        <form action="{{ route('cx.dashboard') }}" method="GET" class="flex items-center gap-2 bg-white/10 p-1 rounded-lg backdrop-blur-sm">
                            <input type="date" name="start_date" value="{{ $filterStartDate }}" 
                                class="bg-transparent border-none text-white text-sm focus:ring-0 cursor-pointer placeholder-gray-300"
                                onchange="this.form.submit()">
                            <span class="text-amber-200">-</span>
                            <input type="date" name="end_date" value="{{ $filterEndDate }}" 
                                class="bg-transparent border-none text-white text-sm focus:ring-0 cursor-pointer placeholder-gray-300"
                                onchange="this.form.submit()">
                        </form>

                        {{-- Link to Issue List --}}
                        <a href="{{ route('cx.index') }}" class="flex items-center gap-2 px-4 py-2 bg-white text-amber-700 rounded-lg font-bold hover:bg-amber-50 transition-colors shadow-sm text-sm">
                            <span>📋</span> Lihat Semua Issue
                        </a>
                    </div>
                </div>
            </div>

            {{-- KPI Metrics --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <x-kpi-card title="Total Issues" :value="$totalIssues" icon="📋" color="amber" />
                <x-kpi-card title="Open" :value="$openIssues" icon="🔴" color="red" />
                <x-kpi-card title="In Progress" :value="$inProgressIssues" icon="⏳" color="blue" />
                <x-kpi-card title="Resolved" :value="$resolvedIssues" icon="✅" color="green" />
                <x-kpi-card title="Avg Response" :value="$avgResponseTime . 'h'" icon="⏱️" color="purple" />
                <x-kpi-card title="Resolution Rate" :value="$resolutionRate . '%'" icon="📈" color="teal" />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- Trend Chart --}}
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-xl font-black text-gray-800 mb-6 flex items-center justify-between">
                        <span class="flex items-center gap-2"><span class="text-2xl">📈</span> Tren Issue</span>
                        <span class="text-xs font-bold px-2 py-1 bg-gray-100 rounded text-gray-500">
                            {{ \Carbon\Carbon::parse($filterStartDate)->format('d M') }} - {{ \Carbon\Carbon::parse($filterEndDate)->format('d M') }}
                        </span>
                    </h3>
                    @php
                        $datasets = [
                            [
                                'label' => 'Open',
                                'data' => $trendOpen,
                                'borderColor' => '#ef4444',
                                'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                                'fill' => true
                            ],
                            [
                                'label' => 'In Progress',
                                'data' => $trendProgress,
                                'borderColor' => '#3b82f6',
                                'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                                'fill' => true
                            ],
                            [
                                'label' => 'Resolved',
                                'data' => $trendResolved,
                                'borderColor' => '#10b981',
                                'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                                'fill' => true
                            ]
                        ];
                    @endphp
                    <x-line-chart id="issueTrendChart" :labels="$trendLabels" :datasets="$datasets" />
                </div>

                {{-- Issue by Category --}}
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-xl font-black text-gray-800 mb-6 flex items-center gap-2">
                        <span class="text-2xl">🎯</span> Issue by Category
                    </h3>
                    <div class="relative h-64">
                        <x-donut-chart 
                            id="categoryChart" 
                            :labels="$issuesByCategory->pluck('category')" 
                            :data="$issuesByCategory->pluck('count')" 
                            :colors="['#f59e0b', '#ef4444', '#3b82f6', '#10b981', '#8b5cf6']" 
                            height="250" />
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none pb-8">
                            <div class="text-center">
                                <div class="text-3xl font-black text-gray-800">{{ $issuesByCategory->sum('count') }}</div>
                                <div class="text-xs text-gray-400 font-bold uppercase tracking-wider">Total</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                {{-- Issue by Source --}}
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-xl font-black text-gray-800 mb-6 flex items-center gap-2">
                        <span class="text-2xl">📊</span> Issue by Source
                    </h3>
                    @if($issuesBySource->count() > 0)
                        <x-bar-chart 
                            id="sourceChart" 
                            :labels="$issuesBySource->pluck('source')" 
                            :data="$issuesBySource->pluck('count')"
                            label="Issues Reported"
                            color="#f59e0b"
                        />
                    @else
                        <div class="text-center py-12 text-gray-400">
                            Belum ada data issue berdasarkan source.
                        </div>
                    @endif
                </div>

                {{-- Team Performance --}}
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-xl font-black text-gray-800 mb-6 flex items-center gap-2">
                        <span class="text-2xl">🏆</span> Top Resolvers
                    </h3>
                    @if($topResolvers->count() > 0)
                        <div class="space-y-4">
                            @foreach($topResolvers as $index => $resolver)
                                <div class="flex items-center gap-4">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full {{ $index === 0 ? 'bg-yellow-100 text-yellow-600' : ($index === 1 ? 'bg-gray-100 text-gray-600' : 'bg-orange-100 text-orange-600') }} flex items-center justify-center font-bold text-sm">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-bold text-gray-800">{{ $resolver->resolver->name ?? 'Unknown' }}</div>
                                        <div class="w-full bg-gray-100 rounded-full h-2 mt-1">
                                            <div class="bg-amber-500 h-2 rounded-full" style="width: {{ min(($resolver->resolved_count / 20) * 100, 100) }}%"></div>
                                        </div>
                                    </div>
                                    <div class="text-amber-600 font-bold text-lg">{{ $resolver->resolved_count }}</div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-400">
                            Belum ada data resolver.
                        </div>
                    @endif
                </div>

            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                {{-- Recent Issues --}}
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-xl font-black text-gray-800 mb-6 flex items-center gap-2">
                        <span class="text-2xl">📡</span> Recent Issues
                    </h3>
                    <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2">
                        @forelse($recentIssues as $issue)
                        <div class="flex gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex-shrink-0 mt-1">
                                <div class="w-2 h-2 rounded-full {{ $issue->status === 'OPEN' ? 'bg-red-400' : ($issue->status === 'IN_PROGRESS' ? 'bg-blue-400' : 'bg-green-400') }} ring-4 {{ $issue->status === 'OPEN' ? 'ring-red-50' : ($issue->status === 'IN_PROGRESS' ? 'ring-blue-50' : 'ring-green-50') }}"></div>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="text-sm font-bold text-gray-800">
                                        {{ $issue->workOrder->spk_number ?? 'Unknown SPK' }}
                                        <span class="font-normal text-gray-500">- {{ $issue->category }}</span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $issue->status === 'OPEN' ? 'bg-red-100 text-red-700' : ($issue->status === 'IN_PROGRESS' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                                        {{ $issue->status }}
                                    </span>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">{{ Str::limit($issue->description, 60) }}</div>
                                <div class="text-[10px] text-gray-400 mt-1">{{ $issue->created_at->diffForHumans() }} • {{ $issue->reporter->name ?? 'System' }}</div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-6 text-gray-400 text-sm">Belum ada issue tercatat.</div>
                        @endforelse
                    </div>
                </div>

                {{-- Overdue Issues & Common Problems --}}
                <div class="space-y-8">
                    
                    {{-- Overdue Issues --}}
                    @if($overdueIssues->count() > 0)
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-xl font-black text-gray-800 mb-6 flex items-center gap-2">
                            <span class="text-2xl">🚨</span> Overdue Issues
                            <span class="ml-auto text-xs font-bold px-2 py-1 bg-red-100 text-red-600 rounded-full animate-pulse">
                                {{ $overdueIssues->count() }}
                            </span>
                        </h3>
                        <div class="space-y-3 max-h-48 overflow-y-auto pr-2">
                            @foreach($overdueIssues as $issue)
                                <div class="p-3 bg-gradient-to-r from-red-50 to-orange-50 rounded-lg border-l-4 border-red-500">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex-1">
                                            <div class="font-bold text-gray-800 text-sm">{{ $issue->workOrder->spk_number ?? 'Unknown' }}</div>
                                            <div class="text-xs text-gray-600 mt-1">{{ $issue->category }}</div>
                                        </div>
                                        <div class="text-[10px] text-red-600 font-bold bg-white px-2 py-1 rounded">
                                            {{ $issue->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Common Problems --}}
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-xl font-black text-gray-800 mb-6 flex items-center gap-2">
                            <span class="text-2xl">💡</span> Common Problems
                        </h3>
                        <div class="space-y-4">
                            @foreach($commonProblems as $problem)
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="font-bold text-gray-700">{{ $problem->category }}</span>
                                    <span class="text-amber-600 font-bold">{{ $problem->count }}x</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="bg-amber-500 h-2 rounded-full" style="width: {{ min(($problem->count / 20) * 100, 100) }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
