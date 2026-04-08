<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 w-full">
            <h2 class="font-black text-xl text-white leading-tight flex items-center gap-4">
                <div class="p-2 bg-white/10 rounded-xl shadow-inner backdrop-blur-md border border-white/20">
                    <span class="text-xl">🏢</span>
                </div>
                {{ __('Pusat Kendali Gudang') }}
            </h2>
            <div x-data="{ now: new Date().toLocaleTimeString() }" x-init="setInterval(() => now = new Date().toLocaleTimeString(), 1000)" 
                 class="flex items-center gap-3 px-4 py-2 bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl shadow-lg shrink-0">
                <span class="h-1.5 w-1.5 rounded-full bg-[#FFC232] animate-pulse"></span>
                <span class="text-[9px] font-black text-white/90 uppercase tracking-[0.2em]">
                    TERKONEKSI: <span x-text="now" class="text-white"></span>
                </span>
            </div>
        </div>
    </x-slot>

    <style>
        :root { --brand-green: #22AF85; --brand-yellow: #FFC232; }
        [x-cloak] { display: none !important; }
        .stat-card { transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 15px 30px -10px rgba(0, 0, 0, 0.08); }
        .glass-panel { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.3); }
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        @keyframes pulse-ring { 0% { transform: scale(0.8); opacity: 0.5; } 100% { transform: scale(2); opacity: 0; } }
        .live-indicator { position: relative; }
        .live-indicator::after { content: ''; position: absolute; width: 100%; height: 100%; top: 0; left: 0; background: inherit; border-radius: inherit; animation: pulse-ring 1.5s cubic-bezier(0.455, 0.03, 0.515, 0.955) infinite; }
    </style>

    <div class="py-6 bg-[#F8FAFC] min-h-screen" wire:poll.30s x-data="{ activeTab: @entangle('activeTab').live }">
        <div class="max-w-[1600px] mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Toolbar: Compact Search --}}
            <div class="flex flex-col xl:flex-row justify-between items-center gap-4 bg-white/60 p-4 rounded-[1.5rem] shadow-lg border border-white glass-panel">
                <div class="flex items-center gap-3 w-full xl:w-auto">
                    <div class="relative group flex-1 xl:w-80" x-data>
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#22AF85] transition-colors">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search" x-ref="searchInput"
                               class="block w-full pl-10 pr-4 py-2.5 bg-white border border-gray-100 rounded-[1rem] text-xs font-bold placeholder-gray-400 focus:outline-none focus:ring-4 focus:ring-[#22AF85]/5 focus:border-[#22AF85] transition-all" 
                               placeholder="Cari SPK, Member, SKU...">
                    </div>
                </div>

                {{-- Hidden Tab Navigation placeholder --}}
                <div class="hidden">
                    <button @click="activeTab = 'summary'" class="px-8 py-2.5 rounded-[1rem] bg-white text-gray-900 shadow-lg text-xs font-black">
                            📊 RINGKASAN
                    </button>
                </div>
            </div>

            {{-- Summary Grid --}}
            <div x-show="activeTab === 'summary'" x-transition:enter="transition ease-out duration-300" class="space-y-6">
                <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
                    
                    {{-- Hero Compact --}}
                    <div class="xl:col-span-8 bg-white rounded-[2rem] p-8 shadow-lg border border-gray-100 relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-[#22AF85]/5 via-transparent to-[#FFC232]/5"></div>
                        <div class="relative z-10">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="px-3 py-1 bg-[#22AF85]/10 text-[#22AF85] rounded-full text-[9px] font-black uppercase tracking-widest border border-[#22AF85]/20">Sistem Aktif</span>
                                <span class="h-1.5 w-1.5 rounded-full bg-green-500 live-indicator"></span>
                            </div>
                            <h1 class="text-3xl font-black text-gray-900 leading-tight mb-2">Pusat Komando <span class="text-[#22AF85]">Operasional</span></h1>
                            <p class="text-gray-400 text-sm font-medium max-w-2xl mb-8">Pantau kesehatan inventaris dan optimalisasi gudang secara real-time.</p>
                            
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                                <div class="space-y-1">
                                    <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest">SPK PENDING</div>
                                    <div class="text-2xl font-black text-gray-900">{{ $stats['pending_reception'] ?? 0 }}</div>
                                </div>
                                <div class="space-y-1 border-l border-gray-50 pl-6">
                                    <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest">DI FINISH</div>
                                    <div class="text-2xl font-black text-[#FFC232]">{{ $stats['finished_not_stored'] ?? 0 }}</div>
                                </div>
                                <div class="space-y-1 border-l border-gray-50 pl-6">
                                    <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Antrean Kirim</div>
                                    <div class="text-2xl font-black text-[#FFC232]">{{ $stats['shipping_pending'] ?? 0 }}</div>
                                </div>
                                <div class="space-y-1 border-l border-gray-50 pl-6">
                                    <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Siap Diambil</div>
                                    <div class="text-2xl font-black text-[#22AF85]">{{ $stats['ready_for_pickup'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Pulse Cards Compact --}}
                    <div class="xl:col-span-4 grid grid-rows-2 gap-6">
                        <div class="bg-[#22AF85] rounded-[2rem] p-6 shadow-xl text-white relative overflow-hidden group">
                            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                            <h3 class="text-[9px] font-black uppercase tracking-widest text-white/70 mb-4">Skor Kesehatan Ops</h3>
                            <div class="flex items-end justify-between mb-4">
                                <div class="text-5xl font-black text-white">{{ $efficiencyStats['health_score'] ?? 0 }}%</div>
                                <div class="px-3 py-1 bg-white/20 rounded-lg text-[9px] font-black uppercase text-white">SEMPURNA</div>
                            </div>
                            <div class="h-1.5 bg-white/20 rounded-full overflow-hidden">
                                <div class="h-full bg-[#FFC232] transition-all duration-1000" style="width: {{ $efficiencyStats['health_score'] ?? 0 }}%"></div>
                            </div>
                        </div>

                        <div class="bg-white rounded-[2rem] p-6 shadow-lg border border-gray-100 relative group">
                            <h3 class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-4">Rata-rata Waktu Inap</h3>
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center text-2xl shadow-inner">⏳</div>
                                <div>
                                    <div class="text-3xl font-black text-gray-900">{{ $efficiencyStats['avg_dwell_hours'] ?? 0 }}<span class="text-base font-bold">jam</span></div>
                                    <div class="text-[8px] font-black text-[#22AF85] flex items-center gap-1 uppercase">
                                        🚀 Perputaran Cepat
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Storage Heatmap Compact --}}
                    <div class="xl:col-span-12 bg-white rounded-[2rem] p-8 shadow-lg border border-gray-100">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                            <div>
                                <h3 class="text-xl font-black text-gray-900">Peta Okupansi <span class="text-gray-400">Rak</span></h3>
                                <p class="text-gray-400 text-xs font-medium mt-0.5">Visualisasi langsung kepadatan penyimpanan.</p>
                            </div>
                            <div class="flex bg-gray-50 p-1.5 rounded-xl border border-gray-100 items-center gap-4">
                                <div class="flex items-center gap-1.5 px-2 border-r border-gray-200">
                                    <div class="w-2 h-2 rounded-full bg-[#22AF85]"></div>
                                    <span class="text-[9px] font-black text-gray-500 uppercase">Tersedia</span>
                                </div>
                                <div class="flex items-center gap-1.5 px-2 border-r border-gray-200">
                                    <div class="w-2 h-2 rounded-full bg-[#FFC232]"></div>
                                    <span class="text-[9px] font-black text-gray-500 uppercase">Optimal</span>
                                </div>
                                <div class="flex items-center gap-1.5 px-2">
                                    <div class="w-2 h-2 rounded-full bg-gray-900"></div>
                                    <span class="text-[9px] font-black text-gray-500 uppercase">Penuh</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-5 md:grid-cols-7 lg:grid-cols-10 xl:grid-cols-14 gap-3">
                            @foreach($heatmapData as $rack)
                                <div class="group relative">
                                    <div class="aspect-square rounded-xl border-2 transition-all duration-300 flex flex-col items-center justify-center cursor-help shadow-sm
                                        {{ $rack['color'] === 'black' ? 'bg-gray-900 border-gray-900' : ($rack['color'] === 'yellow' ? 'bg-[#FFC232]/10 border-[#FFC232]/30' : 'bg-[#22AF85]/5 border-[#22AF85]/20') }}">
                                        <div class="text-[10px] font-black {{ $rack['color'] === 'black' ? 'text-white' : ($rack['color'] === 'yellow' ? 'text-[#FFC232]' : 'text-[#22AF85]') }}">{{ $rack['code'] }}</div>
                                        <div class="text-[8px] font-black {{ $rack['color'] === 'black' ? 'text-white/40' : 'opacity-60' }}">{{ $rack['count'] }} Unit</div>
                                    </div>
                                    <div class="absolute bottom-[110%] left-1/2 -translate-x-1/2 mb-2 w-40 p-3 bg-gray-900 rounded-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-[100] text-white">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-[9px] font-black uppercase tracking-widest text-[#22AF85]">Rak {{ $rack['code'] }}</span>
                                            <span class="text-[7px] bg-white/10 px-1.5 py-0.5 rounded text-white/50">{{ ucfirst($rack['category']) }}</span>
                                        </div>
                                        <div class="h-1 bg-white/10 rounded-full overflow-hidden">
                                            <div class="h-full bg-[#22AF85]" style="width: {{ $rack['usage'] }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Metrics Grid Compact (2 Columns) --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white rounded-[2rem] p-6 shadow-lg border border-gray-100">
                        <h4 class="text-[10px] font-black text-gray-800 mb-4 flex items-center gap-2 uppercase tracking-widest">📈 TREN REJECT QC</h4>
                        <div style="height: 200px;" wire:ignore><canvas id="qcTrendsChart"></canvas></div>
                    </div>
                    <div class="bg-white rounded-[2rem] p-6 shadow-lg border border-gray-100">
                        <h4 class="text-[10px] font-black text-gray-800 mb-4 flex items-center gap-2 uppercase tracking-widest">🧩 ALASAN REJECT</h4>
                        <div style="height: 200px;" wire:ignore><canvas id="qcReasonsChart"></canvas></div>
                    </div>
                </div>

                {{-- Operational Activity Section --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- SPK Pending --}}
                    <div class="bg-white rounded-[2rem] overflow-hidden shadow-lg border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                            <h4 class="text-[10px] font-black text-gray-800 flex items-center gap-2 uppercase tracking-widest">📥 SPK PENDING</h4>
                            <span class="px-2 py-0.5 bg-[#22AF85] text-white text-[9px] font-black rounded-full">{{ $queues['reception']->count() }}</span>
                        </div>
                        <div class="divide-y divide-gray-50 max-h-[400px] overflow-y-auto sidebar-scroll">
                            @forelse($queues['reception'] as $order)
                                <div class="p-4 hover:bg-gray-50 transition-all flex items-center justify-between group">
                                    <div class="space-y-0.5">
                                        <div class="text-[10px] font-black text-[#22AF85]">{{ $order->spk_number }}</div>
                                        <div class="text-xs font-black text-gray-900">{{ $order->customer_name }}</div>
                                        <div class="text-[8px] text-gray-400 font-bold uppercase tracking-tighter">Antre {{ $order->updated_at->diffForHumans(null, true) }}</div>
                                    </div>
                                    <a href="{{ route('reception.show', $order->id) }}" class="opacity-0 group-hover:opacity-100 px-4 py-1.5 bg-[#FFC232] text-[9px] font-black rounded-lg transition-all uppercase shadow-lg shadow-[#FFC232]/20">
                                        Proses →
                                    </a>
                                </div>
                            @empty
                                <div class="p-12 text-center text-gray-400 text-[10px] font-black uppercase opacity-40">Antrean Bersih</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- SPK Received/Needs QC --}}
                    <div class="bg-white rounded-[2rem] overflow-hidden shadow-lg border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                            <h4 class="text-[10px] font-black text-gray-800 flex items-center gap-2 uppercase tracking-widest">🔍 DITERIMA</h4>
                            <span class="px-2 py-0.5 bg-[#FFC232] text-gray-900 text-[9px] font-black rounded-full">{{ $queues['needs_qc']->count() }}</span>
                        </div>
                        <div class="divide-y divide-gray-50 max-h-[400px] overflow-y-auto sidebar-scroll">
                            @forelse($queues['needs_qc'] as $order)
                                <div class="p-4 hover:bg-gray-50 transition-all flex items-center justify-between group">
                                    <div class="space-y-0.5">
                                        <div class="text-[10px] font-black text-[#22AF85]">{{ $order->spk_number }}</div>
                                        <div class="text-xs font-black text-gray-900">{{ $order->customer_name }}</div>
                                        <div class="text-[8px] text-gray-400 font-bold uppercase tracking-tighter">Tahap QC - {{ $order->updated_at->diffForHumans(null, true) }}</div>
                                    </div>
                                    <a href="{{ route('reception.show', $order->id) }}" class="opacity-0 group-hover:opacity-100 px-4 py-1.5 bg-[#22AF85] text-white text-[9px] font-black rounded-lg transition-all uppercase shadow-lg shadow-[#22AF85]/20">
                                        Inspeksi →
                                    </a>
                                </div>
                            @empty
                                <div class="p-12 text-center text-gray-400 text-[10px] font-black uppercase opacity-40">Belum Ada Aset Diterima</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Shipping Queue Section --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="h-px flex-1 bg-gray-100"></div>
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Pusat Pengiriman</h3>
                        <div class="h-px flex-1 bg-gray-100"></div>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Shipping Unverified --}}
                        <div class="bg-white rounded-[2rem] overflow-hidden shadow-lg border border-gray-100 border-t-4 border-t-red-500">
                            <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                                <h4 class="text-[10px] font-black text-gray-800 flex items-center gap-2 uppercase tracking-widest">🚩 BELUM VERIFIKASI</h4>
                                <span class="px-2 py-0.5 bg-red-500 text-white text-[9px] font-black rounded-full">{{ $queues['shipping_unverified']->count() }}</span>
                            </div>
                            <div class="divide-y divide-gray-50 max-h-[300px] overflow-y-auto sidebar-scroll">
                                @forelse($queues['shipping_unverified'] as $ship)
                                    <div class="p-4 hover:bg-gray-50 transition-all flex items-center justify-between group">
                                        <div class="space-y-0.5">
                                            <div class="text-[10px] font-black text-red-500">{{ $ship->spk_number }}</div>
                                            <div class="text-xs font-black text-gray-900">{{ $ship->customer_name }}</div>
                                            <div class="text-[8px] text-gray-400 font-bold uppercase tracking-tighter">{{ $ship->kategori_pengiriman }} • Menunggu Verifikasi</div>
                                        </div>
                                        <a href="/shipping" class="opacity-0 group-hover:opacity-100 px-4 py-1.5 bg-gray-900 text-white text-[9px] font-black rounded-lg transition-all uppercase shadow-lg">
                                            Verifikasi →
                                        </a>
                                    </div>
                                @empty
                                    <div class="p-8 text-center text-gray-300 text-[9px] font-black uppercase tracking-widest">Semua Data Terverifikasi</div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Shipping Verified --}}
                        <div class="bg-white rounded-[2rem] overflow-hidden shadow-lg border border-gray-100 border-t-4 border-t-[#22AF85]">
                            <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                                <h4 class="text-[10px] font-black text-gray-800 flex items-center gap-2 uppercase tracking-widest">✅ SUDAH VERIFIKASI</h4>
                                <span class="px-2 py-0.5 bg-[#22AF85] text-white text-[9px] font-black rounded-full">{{ $queues['shipping_verified']->count() }}</span>
                            </div>
                            <div class="divide-y divide-gray-50 max-h-[300px] overflow-y-auto sidebar-scroll">
                                @forelse($queues['shipping_verified'] as $ship)
                                    <div class="p-4 hover:bg-gray-50 transition-all flex items-center justify-between group">
                                        <div class="space-y-0.5">
                                            <div class="text-[10px] font-black text-[#22AF85]">{{ $ship->spk_number }}</div>
                                            <div class="text-xs font-black text-gray-900">{{ $ship->customer_name }}</div>
                                            <div class="text-[8px] text-gray-500 font-bold uppercase tracking-tighter">
                                                {{ $ship->resi_pengiriman ?? 'Resi Belum Input' }} • {{ $ship->tanggal_pengiriman ? $ship->tanggal_pengiriman->format('d/m/Y') : 'Siap Kirim' }}
                                            </div>
                                        </div>
                                        <div class="px-3 py-1 bg-[#22AF85]/10 text-[#22AF85] text-[8px] font-black rounded-full uppercase">Siap / Terkirim</div>
                                    </div>
                                @empty
                                    <div class="p-8 text-center text-gray-300 text-[9px] font-black uppercase tracking-widest">Belum Ada Data Terverifikasi</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script Engine --}}
    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            let charts = {};
            const standardOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [5, 5], color: '#f1f1f1' }, ticks: { font: { weight: 'bold', size: 9 } } },
                    x: { grid: { display: false }, ticks: { font: { weight: 'bold', size: 9 } } }
                }
            };

            const initCharts = () => {
                const colors = ['#22AF85', '#FFC232', '#1B8E6C', '#D49F28', '#A3E4D7'];
                
                const qcCtx = document.getElementById('qcTrendsChart');
                if (qcCtx) {
                    if (charts.qc) charts.qc.destroy();
                    charts.qc = new Chart(qcCtx, {
                        type: 'line',
                        data: {
                            labels: @json($qcRejectTrends['labels']),
                            datasets: [{
                                data: @json($qcRejectTrends['data']),
                                borderColor: '#FFC232',
                                backgroundColor: 'rgba(255, 194, 50, 0.05)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4,
                                pointRadius: 3,
                                pointBackgroundColor: '#fff'
                            }]
                        },
                        options: standardOptions
                    });
                }

                const reasonCtx = document.getElementById('qcReasonsChart');
                if (reasonCtx) {
                    if (charts.reason) charts.reason.destroy();
                    charts.reason = new Chart(reasonCtx, {
                        type: 'doughnut',
                        data: {
                            labels: @json($qcRejectReasons['labels']),
                            datasets: [{
                                data: @json($qcRejectReasons['data']),
                                backgroundColor: colors,
                                borderRadius: 4,
                                spacing: 8
                            }]
                        },
                        options: {
                            ...standardOptions,
                            plugins: { legend: { display: true, position: 'bottom', labels: { boxWidth: 6, font: { size: 8, weight: 'bold' } } } },
                            cutout: '80%'
                        }
                    });
                }
            };

            initCharts();
            Livewire.on('refreshCharts', () => initCharts());
        });
    </script>
    @endpush
</div>
