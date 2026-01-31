<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Warehouse Dashboard') }}
        </h2>
        <style>
            :root {
                --brand-green: #22AF85;
                --brand-yellow: #FFC232;
            }
            .stat-card {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .stat-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            }
            .text-brand-green { color: var(--brand-green); }
            .bg-brand-green { background-color: var(--brand-green); }
            .bg-brand-yellow { background-color: var(--brand-yellow); }
            .border-brand-green { border-color: var(--brand-green); }
            .border-brand-yellow { border-color: var(--brand-yellow); }
            .ring-brand-green { --tw-ring-color: var(--brand-green); }
            
            @keyframes pulse-soft {
                0%, 100% { transform: scale(1); opacity: 1; }
                50% { transform: scale(1.05); opacity: 0.8; }
            }
            .animate-pulse-soft {
                animation: pulse-soft 3s infinite ease-in-out;
            }
            .sidebar-scroll::-webkit-scrollbar {
                width: 6px;
            }
            .sidebar-scroll::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 10px;
            }
            .sidebar-scroll::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 10px;
            }
            .sidebar-scroll::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }
        </style>
    </x-slot>

    <div class="py-12 bg-gray-50/50 min-h-screen" x-data="{ activeTab: 'summary' }">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- Toolbar: Search & Tab Toggle --}}
            <div class="flex flex-col md:flex-row justify-between items-center gap-6 bg-white p-6 rounded-3xl shadow-xl border border-gray-100">
                {{-- Search --}}
                <div class="w-full md:w-96 relative group">
                    <form action="{{ route('storage.dashboard') }}" method="GET">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none group-focus-within:text-brand-green transition-colors">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ $search }}" 
                               class="block w-full pl-10 pr-3 py-3 border-gray-200 rounded-2xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#22AF85]/20 focus:border-[#22AF85] transition-all bg-gray-50/50 group-hover:bg-white" 
                               placeholder="Cari SPK atau Nama Customer...">
                    </form>
                </div>

                {{-- Tab Toggle --}}
                <div class="flex bg-gray-100 p-1.5 rounded-2xl border border-gray-200 w-full md:w-auto shadow-inner">
                    <button @click="activeTab = 'summary'" 
                            :class="activeTab === 'summary' ? 'bg-white text-gray-900 shadow-lg' : 'text-gray-500 hover:text-gray-700'"
                            class="flex-1 md:flex-none px-6 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-2">
                            <span>📊</span> Ringkasan
                    </button>
                    <button @click="activeTab = 'board'" 
                            :class="activeTab === 'board' ? 'bg-white text-gray-900 shadow-lg' : 'text-gray-500 hover:text-gray-700'"
                            class="flex-1 md:flex-none px-6 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-2">
                            <span>📋</span> Board Operasional
                    </button>
                </div>
            </div>

            {{-- Overview Hero (Visible only in summary) --}}
            <div x-show="activeTab === 'summary'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                 class="relative bg-white rounded-3xl p-8 shadow-xl overflow-hidden border border-gray-100">
                <div class="absolute inset-0 bg-gradient-to-br from-[#22AF85]/5 via-transparent to-[#FFC232]/5 opacity-50"></div>
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-[#22AF85] rounded-full blur-3xl opacity-10"></div>
                
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="px-3 py-1 bg-brand-green/10 text-brand-green rounded-full text-xs font-black uppercase tracking-wider border border-brand-green/10">
                                Real-Time Operations
                            </span>
                        </div>
                        <h1 class="text-4xl md:text-5xl font-black text-gray-900 tracking-tight leading-tight">
                            Warehouse <span class="text-brand-green">Control Center</span>
                        </h1>
                        <p class="text-gray-500 text-lg font-medium max-w-xl mt-2">
                            Kelola penerimaan, penyimpanan, dan pengambilan barang dalam satu dashboard terintegrasi.
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 w-full md:w-auto">
                        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 text-center shadow-sm">
                            <div class="text-3xl font-black text-[#22AF85]">{{ $stats['pending_reception'] }}</div>
                            <div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-1">Pending Reception</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 text-center shadow-sm">
                            <div class="text-3xl font-black text-[#FFC232]">{{ $stats['stored_items'] }}</div>
                            <div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-1">Total Tersimpan</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Summary View: Workflow Queues --}}
            <div x-show="activeTab === 'summary'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                 class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-8">
                
                {{-- Queue: Reception --}}
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden stat-card">
                    <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-[#22AF85]/5 to-white">
                        <h3 class="font-black text-gray-800 flex items-center gap-2">
                            <span class="text-brand-green">📥</span> Penerimaan Baru
                        </h3>
                        <a href="{{ route('reception.index') }}" class="px-2 py-1 bg-[#22AF85]/10 text-brand-green rounded-lg text-xs font-black hover:bg-[#22AF85]/20 transition-colors">
                            {{ $queues['reception']->count() }} <span class="ml-1 opacity-50">View All →</span>
                        </a>
                    </div>
                    <div class="p-4 space-y-3">
                        @forelse($queues['reception']->take(5) as $order)
                            <div class="p-3 bg-gray-50 rounded-2xl border border-gray-100 hover:border-brand-green transition-all group">
                                <div class="flex justify-between items-start mb-1">
                                    <span class="font-mono text-sm font-black text-gray-900 group-hover:text-brand-green">{{ $order->spk_number }}</span>
                                    <span class="text-[10px] bg-white px-2 py-0.5 rounded-full border border-gray-200 text-gray-500 font-bold uppercase">{{ $order->priority }}</span>
                                </div>
                                <div class="text-xs text-gray-600 font-bold mb-2">{{ $order->customer_name }}</div>
                                <div class="flex justify-between items-center">
                                    <span class="text-[10px] text-gray-400">{{ $order->created_at->diffForHumans() }}</span>
                                    <a href="{{ route('reception.show', $order->id) }}" class="text-[10px] font-black text-brand-green hover:underline">Proses →</a>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center text-gray-400 text-sm italic">Tidak ada antrean penerimaan</div>
                        @endforelse
                    </div>
                </div>

                {{-- Queue: Needs QC --}}
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden stat-card">
                    <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-[#FFC232]/5 to-white">
                        <h3 class="font-black text-gray-800 flex items-center gap-2">
                            <span class="text-brand-yellow">🔍</span> Pengecekan Fisik
                        </h3>
                        <a href="{{ route('reception.index') }}#received" class="px-2 py-1 bg-[#FFC232]/10 text-[#B8860B] rounded-lg text-xs font-black hover:bg-[#FFC232]/20 transition-colors">
                            {{ $queues['needs_qc']->count() }} <span class="ml-1 opacity-50">View All →</span>
                        </a>
                    </div>
                    <div class="p-4 space-y-3">
                        @forelse($queues['needs_qc']->take(5) as $order)
                            <div class="p-3 bg-gray-50 rounded-2xl border border-gray-100 hover:border-brand-yellow transition-all group">
                                <div class="flex justify-between items-start mb-1">
                                    <span class="font-mono text-sm font-black text-gray-900 group-hover:text-brand-yellow">{{ $order->spk_number }}</span>
                                    <span class="text-[10px] bg-white px-2 py-0.5 rounded-full border border-gray-200 text-gray-500 font-bold uppercase">DITERIMA</span>
                                </div>
                                <div class="text-xs text-gray-600 font-bold mb-2">{{ $order->customer_name }}</div>
                                <div class="flex justify-between items-center">
                                    <span class="text-[10px] text-gray-400">{{ $order->updated_at->diffForHumans() }}</span>
                                    <a href="{{ route('reception.show', $order->id) }}" class="text-[10px] font-black text-brand-yellow hover:underline">Cek QC →</a>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center text-gray-400 text-sm italic">Tidak ada antrean QC</div>
                        @endforelse
                    </div>
                </div>

                {{-- Queue: Storage --}}
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden stat-card">
                    <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-[#22AF85]/5 to-white">
                        <h3 class="font-black text-gray-800 flex items-center gap-2">
                            <span class="text-brand-green">📦</span> Perlu Disimpan
                        </h3>
                        <a href="{{ route('storage.index') }}" class="px-2 py-1 bg-[#22AF85]/10 text-brand-green rounded-lg text-xs font-black hover:bg-[#22AF85]/20 transition-colors">
                            {{ $queues['storage']->count() }} <span class="ml-1 opacity-50">View All →</span>
                        </a>
                    </div>
                    <div class="p-4 space-y-3">
                        @forelse($queues['storage']->take(5) as $order)
                            <div class="p-3 bg-gray-50 rounded-2xl border border-gray-100 hover:border-brand-green transition-all group">
                                <div class="flex justify-between items-start mb-1">
                                    <span class="font-mono text-sm font-black text-gray-900 group-hover:text-brand-green">{{ $order->spk_number }}</span>
                                    <span class="text-[10px] bg-white px-2 py-0.5 rounded-full border border-gray-200 text-gray-500 font-bold uppercase">{{ $order->status->label() }}</span>
                                </div>
                                <div class="text-xs text-gray-600 font-bold mb-2">{{ $order->customer_name }}</div>
                                <div class="flex justify-between items-center">
                                    <span class="text-[10px] text-gray-400">{{ $order->updated_at->diffForHumans() }}</span>
                                    <a href="{{ route('storage.index') }}?search={{ $order->spk_number }}" class="text-[10px] font-black text-brand-green hover:underline">Simpan →</a>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center text-gray-400 text-sm italic">Semua barang sudah masuk rak</div>
                        @endforelse
                    </div>
                </div>

                {{-- Queue: Pickup --}}
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden stat-card">
                    <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-[#FFC232]/5 to-white">
                        <h3 class="font-black text-gray-800 flex items-center gap-2">
                            <span class="text-brand-yellow">🚀</span> Siap Diambil
                        </h3>
                        <a href="{{ route('storage.index', ['filter' => 'ready']) }}" class="px-2 py-1 bg-[#FFC232]/10 text-[#B8860B] rounded-lg text-xs font-black hover:bg-[#FFC232]/20 transition-colors">
                            {{ $queues['pickup']->count() }} <span class="ml-1 opacity-50">View All →</span>
                        </a>
                    </div>
                    <div class="p-4 space-y-3">
                        @forelse($queues['pickup']->take(5) as $order)
                            <div class="p-3 bg-gray-50 rounded-2xl border border-gray-100 hover:border-brand-yellow transition-all group">
                                <div class="flex justify-between items-start mb-1">
                                    <span class="font-mono text-sm font-black text-gray-900 group-hover:text-brand-yellow">{{ $order->spk_number }}</span>
                                    <span class="text-[10px] bg-brand-yellow text-gray-900 px-2 py-0.5 rounded-full font-bold uppercase animate-pulse-soft">Siap!</span>
                                </div>
                                <div class="text-xs text-gray-600 font-bold mb-1">{{ $order->customer_name }}</div>
                                <div class="text-[10px] text-brand-green font-black mb-2 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    @foreach($order->storageAssignments as $assignment)
                                        {{ $assignment->rack_code }}{{ !$loop->last ? ',' : '' }}
                                    @endforeach
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-[10px] text-gray-400">Selesai {{ $order->updated_at->format('d/m H:i') }}</span>
                                    <form action="{{ route('storage.retrieve', $order->storageAssignments->first()?->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Proses pengambilan barang?')" class="text-[10px] font-black text-brand-yellow hover:underline">Pick Up →</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center text-gray-400 text-sm italic">Belum ada barang siap ambil</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Board View: Large Volume Handling --}}
            <div x-show="activeTab === 'board'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                 class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-8 overflow-x-auto pb-6">
                
                @foreach(['reception' => 'Penerimaan Baru', 'needs_qc' => 'Pengecekan Fisik', 'storage' => 'Perlu Disimpan', 'pickup' => 'Siap Diambil'] as $key => $title)
                    <div class="flex flex-col h-[700px] bg-white rounded-3xl shadow-2xl border border-gray-100">
                        <div class="p-6 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white z-10 rounded-t-3xl">
                            <h3 class="font-black text-gray-900 text-lg flex items-center gap-3">
                                <span class="w-10 h-10 rounded-2xl bg-gray-50 text-brand-green flex items-center justify-center text-xl border border-gray-100">
                                    {{ $key === 'reception' ? '📥' : ($key === 'storage' ? '📦' : '🚀') }}
                                </span>
                                {{ $title }}
                            </h3>
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-black">{{ $queues[$key]->count() }}</span>
                        </div>
                        
                        <div class="flex-1 overflow-y-auto p-6 space-y-4 sidebar-scroll">
                            @forelse($queues[$key] as $order)
                                <div class="p-5 bg-white rounded-2xl border-2 border-gray-50 hover:border-indigo-100 hover:shadow-xl transition-all group relative">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex flex-col">
                                            <span class="font-mono text-base font-black text-gray-900 group-hover:text-indigo-600">{{ $order->spk_number }}</span>
                                            <span class="text-xs text-gray-400 font-bold tracking-tight">{{ $order->created_at->format('d M Y, H:i') }}</span>
                                        </div>
                                        <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-lg text-[10px] font-black uppercase tracking-widest">{{ $order->priority }}</span>
                                    </div>
                                    
                                    <div class="flex items-center gap-3 mb-4">
                                        <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-500">
                                            {{ strtoupper(substr($order->customer_name, 0, 1)) }}
                                        </div>
                                        <span class="text-sm font-black text-gray-700">{{ $order->customer_name }}</span>
                                    </div>

                                    @if($key === 'pickup')
                                        <div class="bg-gray-50 p-3 rounded-xl mb-4 border border-gray-100">
                                            <div class="text-[10px] text-brand-green font-black uppercase mb-1">Posisi Rak</div>
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($order->storageAssignments as $assignment)
                                                    <span class="bg-white px-2 py-0.5 rounded-lg border border-gray-200 text-brand-green font-black text-xs shadow-sm">{{ $assignment->rack_code }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <div class="flex gap-2">
                                        @if($key === 'reception' || $key === 'needs_qc')
                                            <a href="{{ route('reception.show', $order->id) }}" class="flex-1 py-2.5 bg-brand-yellow hover:bg-[#E5AF2D] text-gray-900 rounded-xl text-center text-xs font-black shadow-lg shadow-brand-yellow/20 transition-all">
                                                {{ $key === 'reception' ? 'Proses Sekarang →' : 'Cek QC Fisik →' }}
                                            </a>
                                        @elseif($key === 'storage')
                                            <a href="{{ route('storage.index') }}?search={{ $order->spk_number }}" class="flex-1 py-2.5 bg-brand-yellow hover:bg-[#E5AF2D] text-gray-900 rounded-xl text-center text-xs font-black shadow-lg shadow-brand-yellow/20 transition-all">Simpan ke Rak →</a>
                                        @elseif($key === 'pickup')
                                            <form action="{{ route('storage.retrieve', $order->storageAssignments->first()?->id) }}" method="POST" class="flex-1">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Proses pengambilan barang?')" class="w-full py-2.5 bg-brand-yellow hover:bg-[#E5AF2D] text-gray-900 rounded-xl text-xs font-black shadow-lg shadow-brand-yellow/20 transition-all text-center">Serahkan ke Customer →</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="h-full flex flex-col items-center justify-center text-gray-400 opacity-50 space-y-4">
                                    <div class="text-6xl">📭</div>
                                    <p class="text-sm font-bold uppercase tracking-widest">Kolam Kosong</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                {{-- Rack Utilization Graph --}}
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="p-8 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-xl font-bold text-gray-800">Visualisasi Utilitas Rak</h3>
                        <div class="flex gap-2">
                            <span class="flex items-center gap-1 text-[10px] font-bold text-gray-500">
                                <span class="w-2 h-2 rounded-full bg-green-500"></span> < 50%
                            </span>
                            <span class="flex items-center gap-1 text-[10px] font-bold text-gray-500">
                                <span class="w-2 h-2 rounded-full bg-orange-500"></span> 50-90%
                            </span>
                            <span class="flex items-center gap-1 text-[10px] font-bold text-gray-500">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span> Penuh
                            </span>
                        </div>
                    </div>
                    <div class="p-8 space-y-10">
                        @foreach(['shoes' => 'Rak Sepatu', 'accessories' => 'Rak Aksesoris'] as $key => $label)
                            @if(isset($racksByCategory[$key]))
                                <div>
                                    <h4 class="text-xs font-black text-gray-500 uppercase tracking-widest mb-4 flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full {{ $key === 'shoes' ? 'bg-brand-green' : 'bg-brand-yellow' }}"></span>
                                        {{ $label }}
                                    </h4>
                                    <div class="grid grid-cols-5 md:grid-cols-10 gap-3">
                                        @foreach($racksByCategory[$key] as $rack)
                                            @php
                                                $util = $rack->getUtilizationPercentage();
                                                $color = $util >= 100 ? 'bg-red-500' : ($util >= 50 ? 'bg-orange-500' : 'bg-green-500');
                                            @endphp
                                            <a href="{{ route('storage.index') }}?category={{ $rack->category }}&search={{ $rack->rack_code }}" class="group relative">
                                                <div class="aspect-square {{ $color }} rounded-xl flex items-center justify-center text-white font-black text-xs group-hover:scale-110 transition-transform shadow-lg border-2 border-white/50">
                                                    {{ $rack->rack_code }}
                                                </div>
                                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-20">
                                                    {{ $rack->current_count }}/{{ $rack->capacity }} Unit
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                        
                        <div class="mt-8 grid grid-cols-3 gap-4">
                            <div class="p-4 bg-gray-50 rounded-2xl text-center">
                                <div class="text-2xl font-black text-gray-800">{{ $rackStats['utilization_percentage'] }}%</div>
                                <div class="text-[10px] text-gray-500 font-bold uppercase">Avg. Utilitas</div>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-2xl text-center">
                                <div class="text-2xl font-black text-gray-800">{{ $rackStats['total_available'] }}</div>
                                <div class="text-[10px] text-gray-500 font-bold uppercase">Slot Kosong</div>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-2xl text-center">
                                <div class="text-2xl font-black text-gray-800">{{ $rackStats['full_racks'] }}</div>
                                <div class="text-[10px] text-gray-500 font-bold uppercase">Rak Penuh</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Side Info: Materials & Recent Logs --}}
                <div class="space-y-8">
                    
                    {{-- Material Stock Alert --}}
                    <div class="bg-white rounded-3xl p-6 shadow-xl relative overflow-hidden border border-[#FFC232]/30">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-[#FFC232]/5 rounded-full blur-3xl -mr-16 -mt-16"></div>
                        <h3 class="text-lg font-black text-gray-800 mb-4 flex items-center gap-2">
                             <span class="p-2 bg-[#FFC232]/10 rounded-lg">🚨</span> Stok Material Rendah
                        </h3>
                        <div class="space-y-3 relative z-10">
                            @forelse($lowStockMaterials as $material)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-2xl border border-gray-100 group hover:border-[#FFC232]/50 transition-all">
                                    <div>
                                        <div class="font-bold text-sm text-gray-800">{{ $material->name }}</div>
                                        <div class="text-[10px] text-gray-400 font-bold">Limit: {{ $material->min_stock }} {{ $material->unit }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-black text-[#FFC232]">{{ $material->stock }}</div>
                                        <div class="text-[10px] text-gray-400 uppercase font-black tracking-widest">Sisa</div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-gray-400 text-sm italic py-4">Semua stok material aman.</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Activity Feed --}}
                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-6">
                        <h3 class="text-lg font-black text-gray-800 mb-6 flex items-center gap-2">
                            <span>🕒</span> Aktivitas Terkini
                        </h3>
                        <div class="space-y-6">
                            @foreach($recentLogs as $log)
                                <div class="flex gap-4">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-xs">
                                        👤
                                    </div>
                                    <div class="flex-1 border-b border-gray-50 pb-4">
                                        <div class="flex justify-between items-start">
                                            <span class="text-xs font-black text-gray-900">{{ $log->user->name }}</span>
                                            <span class="text-[10px] text-gray-400">{{ $log->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-[11px] text-gray-600 mt-1">
                                            <span class="font-bold text-brand-green">{{ $log->workOrder->spk_number ?? 'N/A' }}</span>: {{ $log->description }}
                                        </p>
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
