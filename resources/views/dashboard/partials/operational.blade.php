<section class="section-gradient rounded-3xl p-8 animate-fade-in-up">
    <!-- Section Header -->
    <div class="flex items-center gap-4 mb-8">
        <div class="flex-shrink-0 w-12 h-12 rounded-2xl bg-gradient-to-br from-teal-500 to-teal-600 flex items-center justify-center shadow-lg section-icon-glow">
            <span class="text-2xl">🏭</span>
        </div>
        <div class="flex-1">
            <h2 class="text-3xl font-black text-gray-900 tracking-tight">Operational Performance</h2>
            <p class="text-sm text-gray-500 font-medium">Performa teknisi, waktu proses, dan deadline mendatang</p>
        </div>
        <div class="hidden md:block flex-grow h-px section-divider"></div>
    </div>

    {{-- Row 1: Status & Trends --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        {{-- Status Distribution --}}
        <div class="dashboard-card shadow-xl shadow-gray-200/50">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">📊 Distribusi Status</h3>
            </div>
            <div class="dashboard-card-body">
                <div id="statusChart" class="min-h-[250px]"></div>
            </div>
        </div>

        {{-- Daily Trends --}}
        <div class="dashboard-card shadow-xl shadow-gray-200/50">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">📈 Trend Order (Periode Ini)</h3>
            </div>
            <div class="dashboard-card-body">
                <div id="trendsChart" class="min-h-[250px]"></div>
            </div>
        </div>
    </div>

    {{-- Row 2: Technician Performance (Featured) --}}
    <div class="bg-white border border-[#22AF85]/20 rounded-3xl p-8 mb-8 shadow-xl relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-[#22AF85] rounded-full mix-blend-multiply filter blur-3xl opacity-10 -mr-16 -mt-16 animate-pulse"></div>
        
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-black flex items-center gap-2 tracking-tight text-gray-900">
                    <span>🏆</span> Leaderboard Teknisi
                </h3>
            </div>

             @if($technicianPerformance->count() > 0)
                <div x-data="{ activeTab: '{{ $technicianPerformance->keys()->first() }}' }">
                    {{-- Tabs --}}
                    <div class="flex space-x-1 mb-6 bg-gray-100 p-1 rounded-xl inline-flex">
                        @foreach($technicianPerformance as $spec => $techs)
                            <button 
                                @click="activeTab = '{{ $spec }}'"
                                :class="{ 'bg-[#22AF85] text-white shadow-lg shadow-[#22AF85]/30': activeTab === '{{ $spec }}', 'text-gray-500 hover:bg-white hover:text-[#22AF85]': activeTab !== '{{ $spec }}' }"
                                class="px-4 py-2 rounded-lg text-[10px] font-black transition-all duration-200 uppercase tracking-widest">
                                {{ $spec }}
                            </button>
                        @endforeach
                    </div>

                    {{-- Leaderboard Grid --}}
                    @foreach($technicianPerformance as $spec => $techs)
                        <div x-show="activeTab === '{{ $spec }}'" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 translate-y-4"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            
                            @foreach($techs->take(3) as $index => $tech)
                                <div class="bg-gray-50 border border-gray-100 rounded-2xl p-4 flex items-center gap-4 relative overflow-hidden group hover:border-[#22AF85]/30 transition-colors">
                                    <div class="text-4xl font-black opacity-10 absolute right-2 bottom-0 group-hover:scale-110 transition-transform text-gray-900">#{{ $index + 1 }}</div>
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-xl font-bold
                                        {{ $index === 0 ? 'bg-yellow-100 text-yellow-700 border border-yellow-200' : 
                                          ($index === 1 ? 'bg-gray-100 text-gray-700 border border-gray-200' : 
                                          ($index === 2 ? 'bg-orange-100 text-orange-700 border border-orange-200' : 'bg-gray-50 text-gray-400')) }}">
                                        {{ substr($tech['name'], 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-lg leading-tight text-gray-900">{{ $tech['name'] }}</div>
                                        <div class="text-[#22AF85] font-mono text-xs font-bold">{{ $tech['count'] }} Order Selesai</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-400 text-sm italic">Belum ada data performa teknisi.</div>
            @endif
        </div>
    </div>

    {{-- Row 3: Processing Time & Deadlines --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
         {{-- Processing Time --}}
         {{-- Bottleneck Analysis --}}
        <div class="dashboard-card shadow-xl shadow-gray-200/50">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">⏳ Analisis Bottleneck (Rata-rata Durasi)</h3>
            </div>
            <div class="dashboard-card-body">
                <div id="bottleneckChart" class="min-h-[250px]"></div>
            </div>
        </div>

        {{-- Upcoming Deadlines --}}
        <div class="dashboard-card shadow-xl shadow-gray-200/50">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">📅 Deadline Mendatang</h3>
            </div>
            <div class="dashboard-card-body space-y-4">
                <div class="flex items-center justify-between p-4 bg-red-50 rounded-2xl border border-red-100 hover:bg-red-100/50 transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">🔥</span>
                        <div>
                            <div class="font-black text-gray-800 uppercase text-[10px] tracking-widest">Hari Ini</div>
                            <div class="text-xs text-red-600 font-bold">Harus Segera Selesai</div>
                        </div>
                    </div>
                    <div class="text-4xl font-black text-red-600">{{ $upcomingDeadlines['today'] }}</div>
                </div>
                
                <div class="flex items-center justify-between p-4 bg-orange-50 rounded-2xl border border-orange-100 hover:bg-orange-100/50 transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">⚡</span>
                        <div>
                            <div class="font-black text-gray-800 uppercase text-[10px] tracking-widest">Besok</div>
                            <div class="text-xs text-orange-600 font-bold">Antrian Prioritas</div>
                        </div>
                    </div>
                    <div class="text-4xl font-black text-orange-600">{{ $upcomingDeadlines['tomorrow'] }}</div>
                </div>

                <div class="flex items-center justify-between p-4 bg-blue-50 rounded-2xl border border-blue-100 hover:bg-blue-100/50 transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">📅</span>
                        <div>
                            <div class="font-black text-gray-800 uppercase text-[10px] tracking-widest">Minggu Ini</div>
                            <div class="text-xs text-blue-600 font-bold">Volume Produksi</div>
                        </div>
                    </div>
                    <div class="text-4xl font-black text-blue-600">{{ $upcomingDeadlines['thisWeek'] }}</div>
                </div>
            </div>
        </div>
    </div>
</section>
