<x-app-layout>
    <div class="py-12 bg-gray-50/50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- Header with Filter & Actions --}}
            <div class="bg-gradient-to-r from-teal-600 to-teal-700 rounded-2xl p-6 md:p-8 text-white shadow-xl">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <h1 class="text-3xl font-black mb-2">🔧 Dashboard Workshop</h1>
                        <p class="text-teal-100">Metrik Performansi & Analitik Operasional</p>
                    </div>
                    
                    <div class="flex flex-col md:flex-row gap-4 items-end md:items-center">
                        {{-- Date Filter Form --}}
                        <form action="{{ route('workshop.dashboard') }}" method="GET" class="flex items-center gap-2 bg-white/10 p-1 rounded-lg backdrop-blur-sm">
                            <input type="date" name="start_date" value="{{ $filterStartDate }}" 
                                class="bg-transparent border-none text-white text-sm focus:ring-0 cursor-pointer placeholder-gray-300"
                                onchange="this.form.submit()">
                            <span class="text-teal-200">-</span>
                            <input type="date" name="end_date" value="{{ $filterEndDate }}" 
                                class="bg-transparent border-none text-white text-sm focus:ring-0 cursor-pointer placeholder-gray-300"
                                onchange="this.form.submit()">
                        </form>

                        {{-- Export Button --}}
                        <form action="{{ route('workshop.export') }}" method="POST">
                            @csrf
                            <input type="hidden" name="start_date" value="{{ $filterStartDate }}">
                            <input type="hidden" name="end_date" value="{{ $filterEndDate }}">
                            <button type="submit" class="flex items-center gap-2 px-4 py-2 bg-white text-teal-700 rounded-lg font-bold hover:bg-teal-50 transition-colors shadow-sm text-sm">
                                <span>📥</span> Expert Laporan
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Phase 1 & 3: KPI Metrics --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <x-kpi-card title="Diproses" :value="$inProgress" icon="⚙️" color="teal" />
                <x-kpi-card title="Selesai (Periode)" :value="$throughput" icon="🏁" color="blue" />
                <x-kpi-card title="Mendesak" :value="$urgentCount" icon="⚠️" color="red" />
                <x-kpi-card title="Lolos QC" :value="$qcPassRate . '%'" icon="✨" color="purple" />
                <x-kpi-card title="Kapasitas" :value="$capacityUtilization" icon="📊" color="orange" />
                <x-kpi-card title="Pendapatan" value="Rp {{ number_format($revenue/1000, 0) }}k" icon="💰" color="green" />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- Phase 1 & 2: Analytics Row --}}
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-xl font-black text-gray-800 mb-6 flex items-center justify-between">
                        <span class="flex items-center gap-2"><span class="text-2xl">📈</span> Tren Penyelesaian</span>
                        <span class="text-xs font-bold px-2 py-1 bg-gray-100 rounded text-gray-500">
                            {{ \Carbon\Carbon::parse($filterStartDate)->format('d M') }} - {{ \Carbon\Carbon::parse($filterEndDate)->format('d M') }}
                        </span>
                    </h3>
                    @php
                        $datasets = [[
                            'label' => 'Order Selesai',
                            'data' => $trendData,
                            'borderColor' => '#0d9488',
                            'backgroundColor' => '#ccfbf1',
                            'fill' => true
                        ]];
                    @endphp
                    <x-line-chart id="completionChart" :labels="$trendLabels" :datasets="$datasets" />
                </div>

                {{-- Phase 1: Deadline Distribution --}}
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-xl font-black text-gray-800 mb-6 flex items-center gap-2">
                        <span class="text-2xl">⏰</span> Tenggat Waktu
                    </h3>
                    <div class="relative h-64">
                        <x-donut-chart 
                            id="deadlineChart" 
                            :labels="['Aman', 'Perlu Perhatian', 'Terlambat']" 
                            :data="[$onTimeOrders, $atRiskOrders, $overdueOrders]" 
                            :colors="['#10b981', '#f59e0b', '#ef4444']" 
                            height="250" />
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none pb-8">
                            <div class="text-center">
                                <div class="text-3xl font-black text-gray-800">{{ $inProgress }}</div>
                                <div class="text-xs text-gray-400 font-bold uppercase tracking-wider">Aktif</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

             {{-- PHASE 4: Operational Monitoring --}}
             <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                {{-- Active Load per Technician --}}
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-xl font-black text-gray-800 mb-6 flex items-center gap-2">
                        <span class="text-2xl">⚡</span> Beban Kerja Teknisi (Live)
                        <span class="ml-auto text-xs text-green-600 bg-green-50 px-2 py-1 rounded-full animate-pulse">● Realtime</span>
                    </h3>
                    @if($technicianLoad->count() > 0)
                        <x-bar-chart 
                            id="techLoadChart" 
                            :labels="$technicianLoad->pluck('name')" 
                            :data="$technicianLoad->pluck('count')"
                            label="Order Sedang Dikerjakan"
                            color="#3b82f6"
                        />
                    @else
                        <div class="text-center py-12 text-gray-400">
                            Belum ada teknisi yang aktif mengerjakan order.
                        </div>
                    @endif
                </div>

                {{-- Recent Activity Log --}}
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-xl font-black text-gray-800 mb-6 flex items-center gap-2">
                        <span class="text-2xl">📡</span> Aktivitas Terbaru
                    </h3>
                    <div class="space-y-4 max-h-[300px] overflow-y-auto pr-2">
                        @forelse($recentLogs as $log)
                        <div class="flex gap-3">
                            <div class="flex-shrink-0 mt-1">
                                <div class="w-2 h-2 rounded-full bg-teal-400 ring-4 ring-teal-50"></div>
                            </div>
                            <div class="flex-1 pb-4 border-b border-gray-50 last:border-0">
                                <div class="text-sm font-bold text-gray-800">
                                    {{ $log->user->name ?? 'System' }} 
                                    <span class="font-normal text-gray-500">mengupdate</span> 
                                    <span class="text-teal-600">{{ $log->workOrder->spk_number ?? 'Unknown Order' }}</span>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">{{ $log->description }}</div>
                                <div class="text-[10px] text-gray-400 mt-1">{{ $log->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-6 text-gray-400 text-sm">Belum ada aktivitas tercatat.</div>
                        @endforelse
                    </div>
                </div>

             </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                {{-- Phase 1: Workload by Station --}}
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-xl font-black text-gray-800 mb-6 flex items-center gap-2">
                        <span class="text-2xl">📊</span> Antrian per Stasiun
                        @if($bottleneckCount > 10)
                            <span class="ml-auto text-xs font-bold px-2 py-1 bg-red-100 text-red-600 rounded-full animate-pulse">
                                Bottleneck: {{ ucfirst($bottleneckStation) }}
                            </span>
                        @endif
                    </h3>
                    
                    <x-workload-bar label="Asesmen" :count="$workloadByStation['assessment']" :max="30" href="{{ route('assessment.index') }}" />
                    <x-workload-bar label="Preparation" :count="$workloadByStation['preparation']" :max="30" href="{{ route('preparation.index') }}" />
                    <x-workload-bar label="Sortir & Material" :count="$workloadByStation['sortir']" :max="30" href="{{ route('sortir.index') }}" />
                    <x-workload-bar label="Produksi" :count="$workloadByStation['production']" :max="30" href="{{ route('production.index') }}" />
                    <x-workload-bar label="Quality Control" :count="$workloadByStation['qc']" :max="30" href="{{ route('qc.index') }}" />
                </div>

                {{-- Phase 2: Top Performers --}}
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-xl font-black text-gray-800 mb-6 flex items-center gap-2">
                        <span class="text-2xl">🏆</span> Teknisi Terbaik (Periode Ini)
                    </h3>
                    <x-leaderboard :performers="$topPerformers" />
                </div>

            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                {{-- Phase 1: Urgent Orders --}}
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-xl font-black text-gray-800 mb-6 flex items-center gap-2">
                        <span class="text-2xl">🚨</span> Order Mendesak
                    </h3>
                    @if($urgentOrders->count() > 0)
                        <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                            @foreach($urgentOrders as $order)
                                <div class="p-4 bg-gradient-to-r from-red-50 to-orange-50 rounded-xl border-l-4 border-red-500 hover:shadow-md transition-shadow">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex-1">
                                            <div class="font-bold text-gray-800 mb-1">{{ $order->spk_number }}</div>
                                            <div class="text-sm text-gray-600 mb-2">{{ $order->customer_name }}</div>
                                            <div class="flex items-center gap-2">
                                                <span class="px-2 py-1 bg-white rounded text-xs font-bold text-gray-700">
                                                    {{ $order->status->label() }}
                                                </span>
                                            </div>
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

                {{-- Phase 2 & 3: Alerts & Insights --}}
                <div class="space-y-8">
                    
                    {{-- Material Alerts --}}
                    @if($lowStockMaterials->count() > 0)
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-xl font-black text-gray-800 mb-6 flex items-center gap-2">
                            <span class="text-2xl">📦</span> Stok Menipis
                        </h3>
                        <div class="space-y-3">
                            @foreach($lowStockMaterials as $material)
                            <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-100">
                                <span class="font-bold text-gray-700">{{ $material->name }}</span>
                                <span class="px-2 py-1 bg-red-200 text-red-800 rounded font-bold text-xs">
                                    {{ $material->stock }} {{ $material->unit }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Service Mix --}}
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-xl font-black text-gray-800 mb-6 flex items-center gap-2">
                            <span class="text-2xl">💎</span> Layanan Terpopuler
                        </h3>
                        <div class="space-y-4">
                            @foreach($serviceMix as $mix)
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="font-bold text-gray-700">{{ $mix->service->name }}</span>
                                    <span class="text-teal-600 font-bold">Rp {{ number_format($mix->total_revenue/1000, 0) }}k</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="bg-teal-500 h-2 rounded-full" style="width: {{ min(($mix->order_count / 20) * 100, 100) }}%"></div>
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
