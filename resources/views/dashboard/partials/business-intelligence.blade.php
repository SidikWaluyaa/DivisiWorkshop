<section class="section-gradient rounded-3xl p-8 animate-fade-in-up">
    <!-- Section Header -->
    <div class="flex items-center gap-4 mb-8">
        <div class="flex-shrink-0 w-12 h-12 rounded-2xl bg-gradient-to-br from-teal-500 to-teal-600 flex items-center justify-center shadow-lg section-icon-glow">
            <span class="text-2xl">📊</span>
        </div>
        <div class="flex-1">
            <h2 class="text-3xl font-black text-gray-900 tracking-tight">Business Intelligence</h2>
            <p class="text-sm text-gray-500 font-medium">Analisis pendapatan dan monitoring keluhan pelanggan</p>
        </div>
        <div class="hidden md:block flex-grow h-px section-divider"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left Column: Financials (2/3) --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Revenue Widget --}}
            <div class="dashboard-card">
                <div class="dashboard-card-header flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                    <h3 class="dashboard-card-title">💰 Trend Pendapatan</h3>
                    <div class="px-3 py-1 bg-teal-50 text-teal-600 rounded-lg text-xs font-black uppercase tracking-wider border border-teal-100 italic">
                        Real-time Data
                    </div>
                </div>
                <div class="dashboard-card-body">
                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-xl border border-blue-100 mb-6">
                        <div>
                            <div class="text-xs text-gray-500 font-bold uppercase tracking-widest">Total Terpilih</div>
                            <div class="text-3xl font-black text-blue-600">Rp {{ number_format($revenueData['total'] / 1000, 0, ',', '.') }}<span class="text-lg">rb</span></div>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold text-blue-200">{{ count($revenueData['daily']['data']) }}</div>
                            <div class="text-xs text-blue-400 font-bold uppercase">Points</div>
                        </div>
                    </div>
                    <div class="chart-container" style="height: 250px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Complaints (1/3) --}}
        <div class="space-y-6">
            <div class="dashboard-card border-l-4 border-rose-500">
                <div class="dashboard-card-header flex justify-between items-center">
                    <h3 class="dashboard-card-title text-rose-700">🚨 Keluhan</h3>
                    @if($complaintAnalytics['overdue_count'] > 0)
                        <span class="px-2 py-0.5 bg-red-600 text-white rounded text-[10px] font-black animate-pulse">
                            {{ $complaintAnalytics['overdue_count'] }} OVERDUE
                        </span>
                    @endif
                </div>
                <div class="dashboard-card-body">
                    <div class="chart-container mb-4" style="height: 120px;">
                        <canvas id="complaintCategoryChart"></canvas>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('admin.complaints.index', ['status' => 'PENDING']) }}" class="flex flex-col items-center p-2 bg-rose-50 rounded-lg hover:bg-rose-100 transition-colors">
                            <span class="text-xl font-black text-rose-600">{{ $complaintAnalytics['status_counts']['PENDING'] }}</span>
                            <span class="text-[10px] uppercase font-bold text-rose-400">Pending</span>
                        </a>
                        <a href="{{ route('admin.complaints.index', ['status' => 'PROCESS']) }}" class="flex flex-col items-center p-2 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                            <span class="text-xl font-black text-orange-600">{{ $complaintAnalytics['status_counts']['PROCESS'] }}</span>
                            <span class="text-[10px] uppercase font-bold text-orange-400">Proses</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Recent Complaints List --}}
            <div class="bg-white rounded-3xl p-6 shadow-lg shadow-gray-200/50 border border-gray-100">
                 <div class="flex justify-between items-center mb-4">
                    <h4 class="font-bold text-gray-800 text-sm">Terbaru</h4>
                    <a href="{{ route('admin.complaints.index') }}" class="text-xs text-teal-600 font-bold hover:underline">Lihat Semua</a>
                </div>
                <div class="space-y-3">
                    @forelse($complaintAnalytics['recent']->take(3) as $complaint)
                        <a href="{{ route('admin.complaints.show', $complaint->id) }}" class="block p-3 rounded-xl bg-gray-50 hover:bg-orange-50 transition-colors group border border-gray-100">
                            <div class="flex justify-between items-start mb-1">
                                <span class="text-xs font-black text-gray-700 group-hover:text-orange-600">{{ $complaint->workOrder->spk_number ?? '-' }}</span>
                                <span class="text-[10px] text-gray-400">{{ $complaint->created_at->diffForHumans(null, true) }}</span>
                            </div>
                            <p class="text-xs text-gray-500 line-clamp-1 mb-1">{{ $complaint->description }}</p>
                            <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-bold bg-white text-gray-500 border border-gray-200">
                                {{ $complaint->category }}
                            </span>
                        </a>
                    @empty
                        <div class="text-center py-4 text-gray-400 text-xs italic">Tidak ada keluhan</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
