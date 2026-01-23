<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-100 to-teal-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            
            {{-- Premium Header Section --}}
            <section class="relative overflow-hidden bg-gradient-to-br from-gray-800 via-gray-900 to-teal-900 rounded-3xl shadow-2xl">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="absolute -right-20 -top-20 w-80 h-80 bg-white/5 rounded-full blur-3xl"></div>
                <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-teal-500/10 rounded-full blur-3xl"></div>
                
                <div class="relative px-8 py-10">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <div class="space-y-2">
                            <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 backdrop-blur-sm rounded-full text-white/90 text-xs font-bold mb-2">
                                <span class="w-2 h-2 rounded-full animate-pulse {{ $overallHealth === 'healthy' ? 'bg-green-400' : ($overallHealth === 'warning' ? 'bg-yellow-400' : 'bg-red-400') }}"></span>
                                System {{ ucfirst($overallHealth) }}
                            </div>
                            <h1 class="text-4xl lg:text-5xl font-black text-white tracking-tight">
                                Algorithm Control Center
                            </h1>
                            <p class="text-gray-300 text-lg font-medium">
                                Intelligent Automation & Performance Monitoring
                            </p>
                        </div>
                        
                        <div class="flex flex-col gap-3">
                            <div class="bg-white/10 backdrop-blur-md px-6 py-4 rounded-xl border border-white/20">
                                <div class="text-xs text-gray-300 font-medium mb-1">Automation Rate</div>
                                <div class="text-3xl font-black text-white">{{ number_format($automationRate, 1) }}%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Algorithm Status Cards --}}
            <section>
                <h2 class="text-xl font-black text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                    </svg>
                    Active Algorithms
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    
                    {{-- Auto Assignment --}}
                    @php $autoAssign = $algorithms['auto_assignment'] ?? null; @endphp
                    @if($autoAssign)
                    <div class="group bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-teal-200">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-teal-100 to-teal-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-black text-gray-800">Auto Assignment</h3>
                                    <p class="text-xs text-gray-500">Technician allocation</p>
                                </div>
                            </div>
                            <button onclick="toggleAlgorithm('auto_assignment')" 
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $autoAssign->is_active ? 'bg-teal-600' : 'bg-gray-300' }}">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $autoAssign->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                            </button>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Success Rate</span>
                                <span class="text-lg font-black bg-gradient-to-r from-teal-600 to-orange-600 bg-clip-text text-transparent">
                                    {{ number_format($metrics['auto_assignment']['success_rate'] ?? 0, 1) }}%
                                </span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="h-2 rounded-full bg-gradient-to-r from-teal-500 to-orange-500" 
                                     style="width: {{ min($metrics['auto_assignment']['success_rate'] ?? 0, 100) }}%"></div>
                            </div>
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span>Last run: {{ $autoAssign->last_run_at?->diffForHumans() ?? 'Never' }}</span>
                                <button onclick="runAlgorithm('auto_assignment')" class="text-teal-600 hover:text-teal-700 font-bold">Run Now →</button>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Load Balancing --}}
                    @php $loadBalance = $algorithms['load_balancing'] ?? null; @endphp
                    @if($loadBalance)
                    <div class="group bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-orange-200">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-orange-100 to-orange-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-black text-gray-800">Load Balancing</h3>
                                    <p class="text-xs text-gray-500">Workload distribution</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 rounded-lg text-xs font-bold {{ $loadAnalysis['health_status'] === 'healthy' ? 'bg-green-100 text-green-700' : ($loadAnalysis['health_status'] === 'warning' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                {{ ucfirst($loadAnalysis['health_status']) }}
                            </span>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Bottlenecks</span>
                                <span class="text-lg font-black {{ count($loadAnalysis['bottlenecks']) > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ count($loadAnalysis['bottlenecks']) }}
                                </span>
                            </div>
                            @if(count($loadAnalysis['bottlenecks']) > 0)
                                <div class="text-xs text-gray-600 bg-red-50 p-2 rounded-lg border border-red-100">
                                    <strong>{{ $loadAnalysis['bottlenecks'][0]['station'] }}</strong>: {{ $loadAnalysis['bottlenecks'][0]['count'] }} orders
                                </div>
                            @else
                                <div class="text-xs text-green-600 bg-green-50 p-2 rounded-lg border border-green-100">
                                    All stations operating normally
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Priority Calculation --}}
                    @php $priority = $algorithms['priority_calculation'] ?? null; @endphp
                    @if($priority)
                    <div class="group bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-gray-200">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-black text-gray-800">Priority Scoring</h3>
                                    <p class="text-xs text-gray-500">Dynamic prioritization</p>
                                </div>
                            </div>
                            <button onclick="toggleAlgorithm('priority_calculation')" 
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $priority->is_active ? 'bg-teal-600' : 'bg-gray-300' }}">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $priority->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                            </button>
                        </div>
                        
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-600">Critical</span>
                                <span class="font-bold text-red-600">{{ $priorityDistribution['critical'] ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-600">High</span>
                                <span class="font-bold text-orange-600">{{ $priorityDistribution['high'] ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-600">Medium</span>
                                <span class="font-bold text-yellow-600">{{ $priorityDistribution['medium'] ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-600">Low</span>
                                <span class="font-bold text-green-600">{{ $priorityDistribution['low'] ?? 0 }}</span>
                            </div>
                            <button onclick="runAlgorithm('priority_calculation')" class="w-full mt-2 text-xs text-teal-600 hover:text-teal-700 font-bold text-center">
                                Recalculate All →
                            </button>
                        </div>
                    </div>
                    @endif

                </div>
            </section>

            {{-- Activity & Recommendations --}}
            <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                {{-- Recent Activity --}}
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
                                <p class="text-xs text-gray-500 font-medium">Latest automated actions</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3 max-h-96 overflow-y-auto custom-scrollbar">
                            @forelse($recentLogs->take(10) as $log)
                            <div class="flex gap-3 p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-all border border-gray-100 hover:border-gray-200">
                                <div class="flex-shrink-0 mt-1">
                                    <div class="w-2 h-2 rounded-full {{ $log->result === 'success' ? 'bg-green-500 ring-4 ring-green-100' : 'bg-red-500 ring-4 ring-red-100' }}"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2 mb-1">
                                        <div class="font-bold text-gray-800 text-sm">
                                            {{ ucfirst(str_replace('_', ' ', $log->action_type)) }}
                                        </div>
                                        <span class="flex-shrink-0 px-2 py-0.5 rounded-md text-[10px] font-bold {{ $log->result === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $log->result }}
                                        </span>
                                    </div>
                                    @if($log->workOrder)
                                        <div class="text-xs text-gray-600 mb-1">SPK: {{ $log->workOrder->spk_number }}</div>
                                    @endif
                                    <div class="flex items-center gap-2 text-[10px] text-gray-400">
                                        <span>{{ $log->created_at->diffForHumans() }}</span>
                                        @if($log->execution_time_ms)
                                            <span>•</span>
                                            <span>{{ number_format($log->execution_time_ms, 2) }}ms</span>
                                        @endif
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
                                <p class="text-gray-500 text-sm font-medium">No activity yet</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Recommendations --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-orange-50 px-6 py-5 border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-gray-800">Smart Recommendations</h3>
                                <p class="text-xs text-gray-500 font-medium">AI-powered insights</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @forelse($loadAnalysis['recommendations'] as $recommendation)
                            <div class="p-4 bg-gradient-to-r from-orange-50 to-yellow-50 rounded-xl border-l-4 {{ $recommendation['priority'] === 'high' ? 'border-red-500' : 'border-yellow-500' }}">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0">
                                        <span class="inline-block px-2 py-1 rounded-md text-xs font-bold {{ $recommendation['priority'] === 'high' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">
                                            {{ strtoupper($recommendation['priority']) }}
                                        </span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-bold text-gray-800 text-sm mb-1">{{ ucfirst($recommendation['station']) }}</div>
                                        <div class="text-xs text-gray-600">{{ $recommendation['action'] }}</div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-12">
                                <div class="text-6xl mb-4">🎉</div>
                                <div class="text-gray-500 font-semibold">All systems optimal!</div>
                                <p class="text-xs text-gray-400 mt-1">No recommendations at this time</p>
                            </div>
                            @endforelse
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

    <script>
        function toggleAlgorithm(algorithmName) {
            fetch(`/algorithm/toggle/${algorithmName}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }

        function runAlgorithm(algorithmName) {
            if (!confirm(`Run ${algorithmName.replace('_', ' ')} algorithm now?`)) return;

            fetch(`/algorithm/run/${algorithmName}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    location.reload();
                }
            });
        }
    </script>
</x-app-layout>
