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
                            <span id="live-indicator" class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 border border-green-200 rounded-full">
                                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                                <span class="text-[10px] font-black text-green-600 uppercase">Live</span>
                                <span class="live-time text-[10px] font-bold text-green-400"></span>
                            </span>
                        </div>
                        <h1 class="text-4xl md:text-5xl font-black text-gray-900 tracking-tight leading-tight">
                            Warehouse <span class="text-brand-green">Control Center</span>
                        </h1>
                        <p class="text-gray-500 text-lg font-medium max-w-xl mt-2">
                            Kelola penerimaan, penyimpanan, dan pengambilan barang dalam satu dashboard terintegrasi.
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 w-full md:w-auto">
                        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 text-center shadow-sm hover:shadow-md transition-all group relative">
                            <div id="stat-pending-reception" class="text-3xl font-black text-[#22AF85]">{{ $stats['pending_reception'] }}</div>
                            <div class="flex items-center justify-center gap-1 mt-1">
                                <div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Pending Reception</div>
                                {{-- Info Tooltip --}}
                                <div x-data="{ isOpen: false }" class="relative inline-block">
                                    <button @click.stop="isOpen = !isOpen" class="text-gray-400 hover:text-[#22AF85] transition-colors cursor-pointer outline-none">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </button>
                                    <div x-show="isOpen" x-cloak x-transition @click.away="isOpen = false" class="absolute z-[100] w-64 max-w-none p-3 bg-white rounded-xl shadow-2xl border border-[#22AF85]/20 top-full left-1/2 -translate-x-1/2 mt-2 text-left font-medium whitespace-normal">
                                        <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-[#22AF85]/20 rotate-45"></div>
                                        <div class="relative">
                                            <div class="text-[9px] font-black text-[#22AF85] uppercase tracking-widest mb-1">Maksud</div>
                                            <div class="text-[11px] text-gray-700 leading-tight">SPK yang barangnya baru sampai dan mengantre untuk diperiksa.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 text-center shadow-sm hover:shadow-md transition-all group relative">
                            <div id="stat-inventory-value" class="text-3xl font-black text-indigo-600">Rp {{ number_format($inventoryValue['total'] / 1000000, 1, ',', '.') }}jt</div>
                            <div class="flex items-center justify-center gap-1 mt-1">
                                <div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Nilai Inventori</div>
                                {{-- Info Tooltip --}}
                                <div x-data="{ isOpen: false }" class="relative inline-block">
                                    <button @click.stop="isOpen = !isOpen" class="text-gray-400 hover:text-indigo-600 transition-colors cursor-pointer outline-none">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </button>
                                    <div x-show="isOpen" x-cloak x-transition @click.away="isOpen = false" class="absolute z-[100] w-64 max-w-none p-3 bg-white rounded-xl shadow-2xl border border-indigo-100 top-full left-1/2 -translate-x-1/2 mt-2 text-left font-medium whitespace-normal">
                                        <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-indigo-100 rotate-45"></div>
                                        <div class="relative">
                                            <div class="text-[9px] font-black text-indigo-600 uppercase tracking-widest mb-1">Maksud</div>
                                            <div class="text-[11px] text-gray-700 leading-tight">Estimasi total nilai uang dari seluruh stok barang di gudang saat ini.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 text-center shadow-sm hover:shadow-md transition-all group relative">
                            <div id="stat-stored-items" class="text-3xl font-black text-[#FFC232]">{{ $stats['stored_items'] }}</div>
                            <div class="flex items-center justify-center gap-1 mt-1">
                                <div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Total Tersimpan</div>
                                {{-- Info Tooltip --}}
                                <div x-data="{ isOpen: false }" class="relative inline-block">
                                    <button @click.stop="isOpen = !isOpen" class="text-gray-400 hover:text-[#FFC232] transition-colors cursor-pointer outline-none">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </button>
                                    <div x-show="isOpen" x-cloak x-transition @click.away="isOpen = false" class="absolute z-[100] w-64 max-w-none p-3 bg-white rounded-xl shadow-2xl border border-[#FFC232]/30 top-full left-1/2 -translate-x-1/2 mt-2 text-left font-medium whitespace-normal">
                                        <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-[#FFC232]/30 rotate-45"></div>
                                        <div class="relative">
                                            <div class="text-[9px] font-black text-[#FFC232] uppercase tracking-widest mb-1">Maksud</div>
                                            <div class="text-[11px] text-gray-700 leading-tight">Jumlah total unit barang yang sudah tertata di dalam rak gudang.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 text-center shadow-sm hover:shadow-md transition-all group relative">
                            <div id="stat-qc-reject" class="text-3xl font-black text-pink-600">{{ $qcRejectTrends['total'] }}</div>
                            <div class="flex items-center justify-center gap-1 mt-1">
                                <div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">QC Reject (30d)</div>
                                {{-- Info Tooltip --}}
                                <div x-data="{ isOpen: false }" class="relative inline-block">
                                    <button @click.stop="isOpen = !isOpen" class="text-gray-400 hover:text-pink-600 transition-colors cursor-pointer outline-none">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </button>
                                    <div x-show="isOpen" x-cloak x-transition @click.away="isOpen = false" class="absolute z-[100] w-64 max-w-none p-3 bg-white rounded-xl shadow-2xl border border-pink-100 top-full right-0 mt-2 text-left font-medium whitespace-normal">
                                        <div class="absolute -top-1.5 right-5 w-3 h-3 bg-white border-t border-l border-pink-100 rotate-45"></div>
                                        <div class="relative">
                                            <div class="text-[9px] font-black text-pink-600 uppercase tracking-widest mb-1">Maksud</div>
                                            <div class="text-[11px] text-gray-700 leading-tight">Total barang ditolak dalam 30 hari terakhir oleh tim QC Gudang.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                            <div x-data="{ isOpen: false }" class="relative inline-block">
                                <button @click.stop="isOpen = !isOpen" class="text-gray-400 hover:text-[#22AF85] transition-colors cursor-pointer outline-none">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </button>
                                <div x-show="isOpen" x-cloak x-transition @click.away="isOpen = false" class="absolute z-[100] w-56 max-w-none p-3 bg-white rounded-xl shadow-xl border border-[#22AF85]/20 left-1/2 -translate-x-1/2 top-full mt-2 text-[10px] font-bold text-gray-600 whitespace-normal">
                                    <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-[#22AF85]/20 rotate-45"></div>
                                    <div class="relative">Barang yang baru sampai dan menunggu unboxing/pemeriksaan awal.</div>
                                </div>
                            </div>
                        </h3>
                        <a href="{{ route('reception.index') }}" class="px-2 py-1 bg-[#22AF85]/10 text-brand-green rounded-lg text-xs font-black hover:bg-[#22AF85]/20 transition-colors">
                            <span id="queue-count-reception">{{ $queues['reception']->count() }}</span> <span class="ml-1 opacity-50">View All →</span>
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
                            <div x-data="{ isOpen: false }" class="relative inline-block">
                                <button @click.stop="isOpen = !isOpen" class="text-gray-400 hover:text-[#FFC232] transition-colors cursor-pointer outline-none">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </button>
                                <div x-show="isOpen" x-cloak x-transition @click.away="isOpen = false" class="absolute z-[100] w-56 max-w-none p-3 bg-white rounded-xl shadow-xl border border-[#FFC232]/30 left-1/2 -translate-x-1/2 top-full mt-2 text-[10px] font-bold text-gray-600 whitespace-normal">
                                    <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-[#FFC232]/30 rotate-45"></div>
                                    <div class="relative">Barang yang sedang/akan diperiksa kualitas dan kelengkapannya oleh tim QC.</div>
                                </div>
                            </div>
                        </h3>
                        <a href="{{ route('reception.index') }}#received" class="px-2 py-1 bg-[#FFC232]/10 text-[#B8860B] rounded-lg text-xs font-black hover:bg-[#FFC232]/20 transition-colors">
                            <span id="queue-count-needs_qc">{{ $queues['needs_qc']->count() }}</span> <span class="ml-1 opacity-50">View All →</span>
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
                            <div x-data="{ isOpen: false }" class="relative inline-block">
                                <button @click.stop="isOpen = !isOpen" class="text-gray-400 hover:text-brand-green transition-colors cursor-pointer outline-none">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </button>
                                <div x-show="isOpen" x-cloak x-transition @click.away="isOpen = false" class="absolute z-[100] w-56 max-w-none p-3 bg-white rounded-xl shadow-xl border border-[#22AF85]/20 left-1/2 -translate-x-1/2 top-full mt-2 text-[10px] font-bold text-gray-600 whitespace-normal">
                                    <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-[#22AF85]/20 rotate-45"></div>
                                    <div class="relative">Barang yang sudah lolos QC dan menunggu dimasukkan ke dalam rak penyimpanan permanen.</div>
                                </div>
                            </div>
                        </h3>
                        <a href="{{ route('storage.index') }}" class="px-2 py-1 bg-[#22AF85]/10 text-brand-green rounded-lg text-xs font-black hover:bg-[#22AF85]/20 transition-colors">
                            <span id="queue-count-storage">{{ $queues['storage']->count() }}</span> <span class="ml-1 opacity-50">View All →</span>
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
                    <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-pink-50/30 to-white">
                        <h3 class="font-black text-gray-800 flex items-center gap-2">
                            <span class="text-pink-600">🚀</span> Siap Diambil
                            <div x-data="{ isOpen: false }" class="relative inline-block">
                                <button @click.stop="isOpen = !isOpen" class="text-gray-400 hover:text-pink-600 transition-colors cursor-pointer outline-none">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </button>
                                <div x-show="isOpen" x-cloak x-transition @click.away="isOpen = false" class="absolute z-[100] w-56 max-w-none p-3 bg-white rounded-xl shadow-xl border border-pink-100 right-0 top-full mt-2 text-[10px] font-bold text-gray-600 whitespace-normal">
                                    <div class="absolute -top-1.5 right-5 w-3 h-3 bg-white border-t border-l border-pink-100 rotate-45"></div>
                                    <div class="relative">Barang yang sudah selesai diproses dan siap diserahkan ke pelanggan/produksi.</div>
                                </div>
                            </div>
                        </h3>
                        <a href="{{ route('storage.index', ['filter' => 'ready']) }}" class="px-2 py-1 bg-[#FFC232]/10 text-[#B8860B] rounded-lg text-xs font-black hover:bg-[#FFC232]/20 transition-colors">
                            <span id="queue-count-pickup">{{ $queues['pickup']->count() }}</span> <span class="ml-1 opacity-50">View All →</span>
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
            
            {{-- Advanced Analytics Section (Visible only in summary) --}}
            <div x-show="activeTab === 'summary'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                 class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8 mb-8">
                
                {{-- QC Reject Trends --}}
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 hover:shadow-2xl transition-all">
                    <h3 class="text-lg font-black text-gray-800 mb-6 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span>📉 QC Reject Trends</span>
                            <div x-data="{ isOpen: false }" class="relative inline-block">
                                <button @click.stop="isOpen = !isOpen" class="text-gray-400 hover:text-pink-500 transition-colors cursor-pointer outline-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </button>
                                <div x-show="isOpen" x-cloak x-transition @click.away="isOpen = false" class="absolute z-[100] w-80 max-w-none p-5 bg-white rounded-2xl shadow-2xl border border-pink-100 left-1/2 -translate-x-1/2 top-full mt-3 whitespace-normal text-left">
                                    <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-pink-100 rotate-45"></div>
                                    <div class="relative">
                                        <div class="flex items-center gap-2 mb-2">
                                            <div class="w-1 h-4 bg-pink-500 rounded-full"></div>
                                            <div class="text-[10px] font-black text-pink-600 uppercase tracking-widest">Maksud</div>
                                        </div>
                                        <div class="text-[13px] text-gray-700 leading-relaxed mb-4 pl-3 font-medium">Memantau tren jumlah barang yang tidak lolos pengecekan kualitas (QC) setiap harinya untuk melihat apakah ada masalah kualitas yang meningkat.</div>
                                        
                                        <div class="flex items-center gap-2 mb-2">
                                            <div class="w-1 h-4 bg-gray-400 rounded-full"></div>
                                            <div class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Sumber Data</div>
                                        </div>
                                        <div class="text-[12px] text-gray-500 leading-relaxed italic pl-3">Catatan isu/komplain tim QC Gudang dalam 30 hari terakhir.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <span class="text-[10px] bg-pink-50 text-pink-600 px-2 py-1 rounded-lg">Last 30 Days</span>
                    </h3>
                    <div style="height: 250px;">
                        <canvas id="qcTrendsChart"></canvas>
                    </div>
                </div>

                {{-- Supplier Performance --}}
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 hover:shadow-2xl transition-all">
                     <div x-data="{ supplierTab: 'spend' }">
                        <h3 class="text-lg font-black text-gray-800 mb-6 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span>🤝 Supplier Analytics</span>
                                {{-- Info Tooltip --}}
                                <div x-data="{ isOpen: false }" class="relative inline-block text-left">
                                    <button @click.stop="isOpen = !isOpen" class="text-gray-400 hover:text-orange-500 transition-colors cursor-pointer outline-none">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </button>
                                    <div x-show="isOpen" x-cloak x-transition @click.away="isOpen = false" class="absolute z-[100] w-80 max-w-none p-5 bg-white rounded-2xl shadow-2xl border border-orange-100 right-0 top-full mt-3 whitespace-normal text-left">
                                        <div class="absolute -top-1.5 right-5 w-3 h-3 bg-white border-t border-l border-orange-100 rotate-45"></div>
                                        <div class="relative">
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="w-1 h-4 bg-orange-500 rounded-full"></div>
                                                <div class="text-[10px] font-black text-orange-600 uppercase tracking-widest">Maksud</div>
                                            </div>
                                            <div class="text-[13px] text-gray-700 leading-relaxed mb-4 pl-3 font-medium">Melihat siapa supplier yang paling banyak kita belanjakan (Spend) dan bagaimana kualitas barang mereka (Rating).</div>
                                            
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="w-1 h-4 bg-gray-400 rounded-full"></div>
                                                <div class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Sumber Data</div>
                                            </div>
                                            <div class="text-[12px] text-gray-500 leading-relaxed italic pl-3">Data pembelian dan rekam jejak kualitas barang dari masing-masing vendor.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex bg-gray-100 p-1 rounded-xl">
                                <button @click="supplierTab = 'spend'" :class="supplierTab === 'spend' ? 'bg-white shadow-sm text-gray-800' : 'text-gray-400'" class="px-3 py-1 text-[10px] font-black uppercase rounded-lg transition-all">Spend</button>
                                <button @click="supplierTab = 'rating'" :class="supplierTab === 'rating' ? 'bg-white shadow-sm text-gray-800' : 'text-gray-400'" class="px-3 py-1 text-[10px] font-black uppercase rounded-lg transition-all">Rating</button>
                            </div>
                        </h3>
                        <div style="height: 250px;">
                            <canvas x-show="supplierTab === 'spend'" id="supplierSpendChart"></canvas>
                            <canvas x-show="supplierTab === 'rating'" id="supplierRatingChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- QC Rejection Reasons --}}
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 hover:shadow-2xl transition-all">
                    <h3 class="text-lg font-black text-gray-800 mb-6 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span>🧩 Rejection Reasons</span>
                            {{-- Info Tooltip --}}
                            <div x-data="{ isOpen: false }" class="relative inline-block">
                                <button @click.stop="isOpen = !isOpen" class="text-gray-400 hover:text-indigo-500 transition-colors cursor-pointer outline-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </button>
                                <div x-show="isOpen" x-cloak x-transition @click.away="isOpen = false" class="absolute z-[100] w-80 max-w-none p-5 bg-white rounded-2xl shadow-2xl border border-indigo-100 left-1/2 -translate-x-1/2 top-full mt-3 whitespace-normal text-left">
                                    <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-indigo-100 rotate-45"></div>
                                    <div class="relative">
                                        <div class="flex items-center gap-2 mb-2">
                                            <div class="w-1 h-4 bg-indigo-500 rounded-full"></div>
                                            <div class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">Maksud</div>
                                        </div>
                                        <div class="text-[13px] text-gray-700 leading-relaxed mb-4 pl-3 font-medium">Memahami alasan utama mengapa barang ditolak (misal: kondisi awal buruk atau rusak) agar bisa dievaluasi kedepannya.</div>
                                        
                                        <div class="flex items-center gap-2 mb-2">
                                            <div class="w-1 h-4 bg-gray-400 rounded-full"></div>
                                            <div class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Sumber Data</div>
                                        </div>
                                        <div class="text-[12px] text-gray-500 leading-relaxed italic pl-3">Kategori masalah yang dicatat tim QC saat melakukan penolakan barang masuk.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <span class="text-[10px] bg-indigo-50 text-indigo-600 px-2 py-1 rounded-lg">Category Distribution</span>
                    </h3>
                    <div style="height: 250px;">
                        <canvas id="qcReasonsChart"></canvas>
                    </div>
                </div>

                {{-- Material Turnover / Most Stored --}}
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 hover:shadow-2xl transition-all">
                    <h3 class="text-lg font-black text-gray-800 mb-6 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span>🔝 Fast-Moving Stock</span>
                            {{-- Info Tooltip --}}
                            <div x-data="{ isOpen: false }" class="relative inline-block">
                                <button @click.stop="isOpen = !isOpen" class="text-gray-400 hover:text-green-500 transition-colors cursor-pointer outline-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </button>
                                <div x-show="isOpen" x-cloak x-transition @click.away="isOpen = false" class="absolute z-[100] w-80 max-w-none p-5 bg-white rounded-2xl shadow-2xl border border-green-100 right-0 top-full mt-3 whitespace-normal text-left">
                                    <div class="absolute -top-1.5 right-5 w-3 h-3 bg-white border-t border-l border-green-100 rotate-45"></div>
                                    <div class="relative">
                                        <div class="flex items-center gap-2 mb-2">
                                            <div class="w-1 h-4 bg-green-500 rounded-full"></div>
                                            <div class="text-[10px] font-black text-green-600 uppercase tracking-widest">Maksud</div>
                                        </div>
                                        <div class="text-[13px] text-gray-700 leading-relaxed mb-4 pl-3 font-medium">Menampilkan barang yang paling banyak stoknya atau paling aktif digunakan, membantu prioritas penataan barang.</div>
                                        
                                        <div class="flex items-center gap-2 mb-2">
                                            <div class="w-1 h-4 bg-gray-400 rounded-full"></div>
                                            <div class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Sumber Data</div>
                                        </div>
                                        <div class="text-[12px] text-gray-500 leading-relaxed italic pl-3">Data stok material saat ini yang diurutkan berdasarkan jumlah terbanyak.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <span class="text-[10px] bg-green-50 text-green-600 px-2 py-1 rounded-lg">High Demand</span>
                    </h3>
                    <div style="height: 250px;">
                        <canvas id="materialTrendsChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                {{-- Rack Utilization Graph --}}
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="p-8 border-b border-gray-100 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <h3 class="text-xl font-bold text-gray-800">Visualisasi Utilitas Rak</h3>
                            {{-- Info Tooltip --}}
                            <div x-data="{ isOpen: false }" class="relative inline-block">
                                <button @click.stop="isOpen = !isOpen" class="text-gray-400 hover:text-blue-500 transition-colors cursor-pointer outline-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </button>
                                <div x-show="isOpen" x-cloak x-transition @click.away="isOpen = false" class="absolute z-[100] w-80 max-w-none p-5 bg-white rounded-2xl shadow-2xl border border-blue-100 right-0 top-full mt-3 whitespace-normal text-left">
                                    <div class="absolute -top-1.5 right-5 w-3 h-3 bg-white border-t border-l border-blue-100 rotate-45"></div>
                                    <div class="relative">
                                        <div class="flex items-center gap-2 mb-2">
                                            <div class="w-1 h-4 bg-blue-500 rounded-full"></div>
                                            <div class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Maksud</div>
                                        </div>
                                        <div class="text-[13px] text-gray-700 leading-relaxed mb-4 pl-3 font-medium">Peta visual kapasitas gudang untuk melihat area rak mana yang masih kosong (hijau) atau sudah penuh (merah).</div>
                                        
                                        <div class="flex items-center gap-2 mb-2">
                                            <div class="w-1 h-4 bg-gray-400 rounded-full"></div>
                                            <div class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Sumber Data</div>
                                        </div>
                                        <div class="text-[12px] text-gray-500 leading-relaxed italic pl-3">Data posisi stok di rak dibandingkan dengan kapasitas maksimal masing-masing rak.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                                <div id="rack-utilization" class="text-2xl font-black text-gray-800">{{ $rackStats['utilization_percentage'] }}%</div>
                                <div class="text-[10px] text-gray-500 font-bold uppercase">Avg. Utilitas</div>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-2xl text-center">
                                <div id="rack-available" class="text-2xl font-black text-gray-800">{{ $rackStats['total_available'] }}</div>
                                <div class="text-[10px] text-gray-500 font-bold uppercase">Slot Kosong</div>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-2xl text-center">
                                <div id="rack-full" class="text-2xl font-black text-gray-800">{{ $rackStats['full_racks'] }}</div>
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

    {{-- Realtime Polling Script & Charts --}}
    @push('scripts')
    <script>
        (function() {
            const POLL_INTERVAL = 30000; // 30 seconds
            const API_URL = '{{ route("storage.dashboard.api-stats") }}';
            let liveIndicator = document.getElementById('live-indicator');

            function updateDashboard() {
                fetch(API_URL, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    // Hero Stats
                    const statEls = {
                        'stat-pending-reception': data.stats.pending_reception,
                        'stat-stored-items': data.stats.stored_items,
                        'stat-qc-reject': data.qc_reject_count,
                        'stat-inventory-value': 'Rp ' + (data.inventory_value / 1000000).toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 }).replace('.', ',') + 'jt'
                    };
                    Object.entries(statEls).forEach(([id, val]) => {
                        const el = document.getElementById(id);
                        if (el && el.textContent.trim() != String(val).trim()) {
                            el.textContent = val;
                            el.classList.add('animate-pulse');
                            setTimeout(() => el.classList.remove('animate-pulse'), 1500);
                        }
                    });

                    // Queue badge counts
                    const queueEls = {
                        'queue-count-reception': data.queue_counts.reception,
                        'queue-count-needs_qc': data.queue_counts.needs_qc,
                        'queue-count-storage': data.queue_counts.storage,
                        'queue-count-pickup': data.queue_counts.pickup,
                    };
                    Object.entries(queueEls).forEach(([id, val]) => {
                        const el = document.getElementById(id);
                        if (el) el.textContent = val;
                    });

                    // Rack Stats
                    const rackEls = {
                        'rack-utilization': data.rack_stats.utilization_percentage + '%',
                        'rack-available': data.rack_stats.total_available,
                        'rack-full': data.rack_stats.full_racks,
                    };
                    Object.entries(rackEls).forEach(([id, val]) => {
                        const el = document.getElementById(id);
                        if (el) el.textContent = val;
                    });

                    // Update timestamp
                    if (liveIndicator) {
                        const liveTimeEl = liveIndicator.querySelector('.live-time');
                        if (liveTimeEl) liveTimeEl.textContent = data.timestamp;
                    }
                })
                .catch(err => console.warn('Dashboard poll error:', err));
            }

             // Start polling
            setInterval(updateDashboard, POLL_INTERVAL);

            // === Charts Initialization ===
            if (typeof Chart === 'undefined') {
                console.warn('Chart.js is not defined. Retrying in 500ms...');
                setTimeout(arguments.callee, 500);
                return;
            }

            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { display: false }, ticks: { font: { weight: 'bold', size: 10 } } },
                    x: { grid: { display: false }, ticks: { font: { weight: 'bold', size: 10 } } }
                }
            };

            const initChart = (id, config) => {
                const el = document.getElementById(id);
                if (el) return new Chart(el, config);
                return null;
            };

            // 1. QC Trends Chart
            initChart('qcTrendsChart', {
                type: 'line',
                data: {
                    labels: {!! json_encode($qcRejectTrends['labels']) !!},
                    datasets: [{
                        label: 'Reject Count',
                        data: {!! json_encode($qcRejectTrends['data']) !!},
                        borderColor: '#DB2777',
                        backgroundColor: 'rgba(219, 39, 119, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0
                    }]
                },
                options: commonOptions
            });

            // 2. Supplier Spend Chart
            initChart('supplierSpendChart', {
                type: 'bar',
                data: {
                    labels: {!! json_encode($supplierAnalytics['bySpend']['labels']) !!},
                    datasets: [{
                        data: {!! json_encode($supplierAnalytics['bySpend']['data']) !!},
                        backgroundColor: '#22AF85',
                        borderRadius: 8
                    }]
                },
                options: {
                    ...commonOptions,
                    plugins: { tooltip: { callbacks: { label: (c) => 'Rp ' + (c.raw/1000000).toFixed(1) + 'jt' } } }
                }
            });

            // 3. Supplier Rating Chart
            initChart('supplierRatingChart', {
                type: 'radar',
                data: {
                    labels: {!! json_encode($supplierAnalytics['byRating']['labels']) !!},
                    datasets: [{
                        data: {!! json_encode($supplierAnalytics['byRating']['data']) !!},
                        backgroundColor: 'rgba(255, 194, 50, 0.2)',
                        borderColor: '#FFC232',
                        pointBackgroundColor: '#FFC232'
                    }]
                },
                options: {
                    ...commonOptions,
                    scales: { r: { min: 0, max: 5, ticks: { display: false } } }
                }
            });

            // 4. Material Trends Chart
            initChart('materialTrendsChart', {
                type: 'bar',
                data: {
                    labels: {!! json_encode($materialTrends['labels']) !!},
                    datasets: [{
                        data: {!! json_encode($materialTrends['data']) !!},
                        backgroundColor: '#4F46E5',
                        borderRadius: 8
                    }]
                },
                options: {
                    ...commonOptions,
                    indexAxis: 'y'
                }
            });

            // 5. QC Reasons Chart
            initChart('qcReasonsChart', {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($qcRejectReasons['labels']) !!},
                    datasets: [{
                        data: {!! json_encode($qcRejectReasons['data']) !!},
                        backgroundColor: ['#22AF85', '#FFC232', '#4F46E5', '#DB2777', '#06B6D4'],
                        borderWidth: 0
                    }]
                },
                options: {
                    ...commonOptions,
                    plugins: { 
                        legend: { 
                            display: true, 
                            position: 'bottom',
                            labels: { usePointStyle: true, font: { size: 10, weight: 'bold' } }
                        } 
                    },
                    cutout: '70%'
                }
            });
        })();
    </script>
    @endpush

</x-app-layout>
