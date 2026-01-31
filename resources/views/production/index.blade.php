<x-app-layout>
    <x-slot name="header">
         <div class="flex flex-col md:flex-row justify-between items-center gap-4">
             <div class="flex items-center gap-4">
                <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm shadow-sm border border-white/30">
                     <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                    </svg>
                </div>
                
                <div class="flex flex-col">
                    <h2 class="font-bold text-xl leading-tight tracking-wide">
                        {{ __('Stasiun Produksi') }}
                    </h2>
                    <div class="text-xs font-medium opacity-90">
                       Proses & Pelacakan
                    </div>
                </div>
             </div>

             <div class="flex items-center gap-3">
                 {{-- Search Form --}}
                <form method="GET" action="{{ route('production.index') }}" class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Cari SPK / Customer..." 
                           style="color: #000000 !important; background-color: #ffffff !important;"
                           class="pl-9 pr-4 py-1.5 text-sm !text-gray-900 !bg-white border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 shadow-sm w-48 transition-all focus:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </form>

                 <div class="px-3 py-1 bg-white/10 text-white rounded-full text-xs font-bold border border-white/20">
                    {{ $orders->total() }} Order Aktif
                </div>
             </div>
        </div>
    </x-slot>

    <div class="py-6 bg-gray-50 min-h-screen" 
         x-data="{ activeTab: '{{ $activeTab ?? 'sol' }}', selectedItems: [] }"
         @open-report-modal.window="openReportModal($event.detail)">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            
            {{-- Premium Stats Overview with Glassmorphism --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                {{-- Sol Stat - Orange Gradient --}}
                <a href="{{ route('production.index', ['tab' => 'sol']) }}"
                     class="group relative overflow-hidden rounded-2xl p-6 cursor-pointer transition-all duration-300 hover:scale-105 hover:shadow-2xl"
                     :class="{ 'ring-4 ring-orange-400 ring-opacity-50': '{{ $activeTab }}' === 'sol' }">
                    {{-- Gradient Background --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-orange-400 via-orange-500 to-orange-600 opacity-90 group-hover:opacity-100 transition-opacity"></div>
                    
                    {{-- Glassmorphism Overlay --}}
                    <div class="absolute inset-0 backdrop-blur-sm bg-white/10"></div>
                    
                    {{-- Content --}}
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-3">
                            <div class="p-3 bg-white/20 rounded-xl backdrop-blur-md">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                            @if('{{ $activeTab }}' === 'sol')
                                <span class="px-3 py-1 bg-white/30 backdrop-blur-md rounded-full text-white text-xs font-bold">Active</span>
                            @endif
                        </div>
                        <h3 class="text-white font-black text-lg mb-1">Reparasi Sol</h3>
                        <p class="text-white/80 text-sm mb-3">Proses perbaikan sol sepatu</p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-black text-white">{{ $countSol }}</span>
                            <span class="text-white/70 text-sm font-medium">antrian</span>
                        </div>
                    </div>
                </a>

                {{-- Upper Stat - Purple Gradient --}}
                <a href="{{ route('production.index', ['tab' => 'upper']) }}"
                     class="group relative overflow-hidden rounded-2xl p-6 cursor-pointer transition-all duration-300 hover:scale-105 hover:shadow-2xl"
                     :class="{ 'ring-4 ring-purple-400 ring-opacity-50': '{{ $activeTab }}' === 'upper' }">
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-400 via-purple-500 to-purple-600 opacity-90 group-hover:opacity-100 transition-opacity"></div>
                    <div class="absolute inset-0 backdrop-blur-sm bg-white/10"></div>
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-3">
                            <div class="p-3 bg-white/20 rounded-xl backdrop-blur-md">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 15.536c-1.171 1.952-3.07 1.952-4.242 0-1.172-1.953-1.172-5.119 0-7.072 1.171-1.952 3.07-1.952 4.242 0M8 10.5h4m-4 3h4m9-1.5a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            @if('{{ $activeTab }}' === 'upper')
                                <span class="px-3 py-1 bg-white/30 backdrop-blur-md rounded-full text-white text-xs font-bold">Active</span>
                            @endif
                        </div>
                        <h3 class="text-white font-black text-lg mb-1">Reparasi Upper</h3>
                        <p class="text-white/80 text-sm mb-3">Perbaikan bagian atas sepatu</p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-black text-white">{{ $countUpper }}</span>
                            <span class="text-white/70 text-sm font-medium">antrian</span>
                        </div>
                    </div>
                </a>

                {{-- Treatment Stat - Teal Gradient --}}
                <a href="{{ route('production.index', ['tab' => 'treatment']) }}"
                     class="group relative overflow-hidden rounded-2xl p-6 cursor-pointer transition-all duration-300 hover:scale-105 hover:shadow-2xl"
                     :class="{ 'ring-4 ring-teal-400 ring-opacity-50': '{{ $activeTab }}' === 'treatment' }">
                    <div class="absolute inset-0 bg-gradient-to-br from-teal-400 via-teal-500 to-teal-600 opacity-90 group-hover:opacity-100 transition-opacity"></div>
                    <div class="absolute inset-0 backdrop-blur-sm bg-white/10"></div>
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-3">
                            <div class="p-3 bg-white/20 rounded-xl backdrop-blur-md">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                            </div>
                            @if('{{ $activeTab }}' === 'treatment')
                                <span class="px-3 py-1 bg-white/30 backdrop-blur-md rounded-full text-white text-xs font-bold">Active</span>
                            @endif
                        </div>
                        <h3 class="text-white font-black text-lg mb-1">Repaint & Treatment</h3>
                        <p class="text-white/80 text-sm mb-3">Pewarnaan & perawatan khusus</p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-black text-white">{{ $countTreatment }}</span>
                            <span class="text-white/70 text-sm font-medium">antrian</span>
                        </div>
                    </div>
                </a>

                {{-- All Orders Stat - Gray Gradient --}}
                <a href="{{ route('production.index', ['tab' => 'all']) }}"
                     class="group relative overflow-hidden rounded-2xl p-6 cursor-pointer transition-all duration-300 hover:scale-105 hover:shadow-2xl"
                     :class="{ 'ring-4 ring-gray-400 ring-opacity-50': '{{ $activeTab }}' === 'all' }">
                    <div class="absolute inset-0 bg-gradient-to-br from-gray-600 via-gray-700 to-gray-800 opacity-90 group-hover:opacity-100 transition-opacity"></div>
                    <div class="absolute inset-0 backdrop-blur-sm bg-white/10"></div>
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-3">
                            <div class="p-3 bg-white/20 rounded-xl backdrop-blur-md">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                            </div>
                            @if('{{ $activeTab }}' === 'all')
                                <span class="px-3 py-1 bg-white/30 backdrop-blur-md rounded-full text-white text-xs font-bold">Active</span>
                            @endif
                        </div>
                        <h3 class="text-white font-black text-lg mb-1">Semua Order</h3>
                        <p class="text-white/80 text-sm mb-3">Total seluruh antrian produksi</p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-black text-white">{{ $countAll }}</span>
                            <span class="text-white/70 text-sm font-medium">order</span>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Filter Bar --}}
            <x-workshop-filter-bar 
                :technicians="data_get($techs, $activeTab, collect([]))"
            />

            {{-- SOL Content --}}
            <div x-show="activeTab === 'sol'" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" style="display: none;">
                <div class="p-4 bg-gradient-to-r from-orange-50 to-orange-100 border-b border-orange-200 flex justify-between items-center">
                    <h3 class="font-bold text-orange-800 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-orange-500"></span> Antrian Reparasi Sol
                    </h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($queues['sol'] as $key => $order)
                        @if(!$order->prod_sol_completed_at)
                             <div class="flex items-start gap-4 p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex-1">
                                    <x-station-card 
                                        :order="$order" 
                                        type="item_prod_sol" 
                                        :technicians="$techs['sol']"
                                        techByRelation="prodSolBy"
                                        startedAtColumn="prod_sol_started_at"
                                        byColumn="prod_sol_by"
                                        color="orange"
                                        titleAction="Assign"
                                        showCheckbox="true"
                                        :loopIteration="($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration"
                                    />
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="p-8 text-center text-gray-400">Tidak ada antrian sol.</div>
                    @endforelse
                </div>
            </div>

            {{-- UPPER Content --}}
            <div x-show="activeTab === 'upper'" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" style="display: none;">
                <div class="p-4 bg-gradient-to-r from-purple-50 to-purple-100 border-b border-purple-200 flex justify-between items-center">
                    <h3 class="font-bold text-purple-800 flex items-center gap-2">
                         <span class="w-2 h-2 rounded-full bg-pink-500"></span> Antrian Reprasi Upper
                    </h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($queues['upper'] as $key => $order)
                        @if(!$order->prod_upper_completed_at)
                             <div class="flex items-start gap-4 p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex-1">
                                    <x-station-card 
                                        :order="$order" 
                                        type="item_prod_upper" 
                                        :technicians="$techs['upper']"
                                        techByRelation="prodUpperBy"
                                        startedAtColumn="prod_upper_started_at"
                                        byColumn="prod_upper_by"
                                        color="purple"
                                        titleAction="Assign"
                                        showCheckbox="true"
                                        :loopIteration="($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration"
                                    />
                                </div>
                            </div>
                        @endif
                    @empty
                         <div class="p-8 text-center text-gray-400">Tidak ada antrian upper.</div>
                    @endforelse
                </div>
            </div>

            {{-- REPAINT & TREATMENT Content --}}
             <div x-show="activeTab === 'treatment'" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" style="display: none;">
                <div class="p-4 bg-gradient-to-r from-teal-50 to-teal-100 border-b border-teal-200 flex justify-between items-center">
                    <h3 class="font-bold text-teal-800 flex items-center gap-2">
                         <span class="w-2 h-2 rounded-full bg-purple-500"></span> Antrian Repaint & Treatment
                    </h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($queues['treatment'] as $key => $order)
                        @if(!$order->prod_cleaning_completed_at)
                             <div class="flex items-start gap-4 p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex-1">
                                    <x-station-card 
                                        :order="$order" 
                                        type="item_prod_cleaning" 
                                        :technicians="$techs['treatment']"
                                        techByRelation="prodCleaningBy"
                                        startedAtColumn="prod_cleaning_started_at"
                                        byColumn="prod_cleaning_by"
                                        color="teal"
                                        titleAction="Assign"
                                        showCheckbox="true"
                                        :loopIteration="($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration"
                                    />
                                </div>
                            </div>
                        @endif
                    @empty
                         <div class="p-8 text-center text-gray-400">Tidak ada antrian repaint/treatment.</div>
                    @endforelse
                </div>
            </div>

            {{-- ADMIN REVIEW SECTION --}}
            @if($queueReview->isNotEmpty())
            <div class="mt-8 mb-8 bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden border-2 border-orange-400" x-show="activeTab === 'all' || activeTab.includes('review')">
                <div class="bg-gradient-to-r from-orange-500 to-red-500 p-4 text-white flex justify-between items-center">
                    <h3 class="text-lg font-bold flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Menunggu Pemeriksaan Admin (Produksi Selesai)
                    </h3>
                    <span class="bg-white/20 px-3 py-1 rounded-full text-xs font-bold">{{ $queueReview->count() }} Order</span>
                </div>
                
                <div class="overflow-x-auto -mx-4 sm:mx-0">
                    <table class="min-w-full w-full text-sm text-left">
                        <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-xs font-bold">
                            <tr>
                                <th class="px-6 py-3">
                                    <input type="checkbox" @click="toggleAll($event)" class="rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                </th>
                                <th class="px-6 py-3">No</th>
                                <th class="px-6 py-3">SPK</th>
                                <th class="px-6 py-3">Pelanggan</th>
                                <th class="px-6 py-3">Prioritas</th>
                                <th class="px-6 py-3">Item</th>
                                <th class="px-6 py-3">Status Pengerjaan</th>
                                <th class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($queueReview as $order)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4">
                                    <input type="checkbox" value="{{ $order->id }}" x-model="selectedItems" class="rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-500">{{ ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration }}</td>
                                <td class="px-6 py-4 font-bold font-mono text-gray-900">{{ $order->spk_number }}</td>
                                <td class="px-6 py-4 font-bold text-gray-800">{{ $order->customer_name }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if(in_array($order->priority, ['Prioritas', 'Urgent', 'Express']))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200 shadow-sm">
                                            PRIORITAS
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                            REGULER
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">{{ $order->shoe_brand }} - {{ $order->shoe_type }}</td>
                                <td class="px-6 py-4">
                                     <div class="flex flex-col gap-2">
                                        @if($order->prod_sol_completed_at) 
                                            <div class="flex items-start gap-2 text-xs">
                                                <span class="text-green-600 font-bold min-w-[50px]">✔ Sol:</span>
                                                <div>
                                                    <div class="font-medium text-gray-700">{{ $order->prodSolBy->name ?? 'System' }}</div>
                                                    @if($order->prod_sol_started_at)
                                                        <div class="text-[10px] text-gray-500">
                                                            {{ $order->prod_sol_started_at->format('H:i') }} - {{ $order->prod_sol_completed_at->format('H:i') }} 
                                                            <span class="font-bold text-teal-600">({{ $order->prod_sol_started_at->diffInMinutes($order->prod_sol_completed_at) }} mnt)</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                        @if($order->prod_upper_completed_at) 
                                            <div class="flex items-start gap-2 text-xs">
                                                <span class="text-green-600 font-bold min-w-[50px]">✔ Upper:</span>
                                                <div>
                                                    <div class="font-medium text-gray-700">{{ $order->prodUpperBy->name ?? 'System' }}</div>
                                                    @if($order->prod_upper_started_at)
                                                        <div class="text-[10px] text-gray-500">
                                                            {{ $order->prod_upper_started_at->format('H:i') }} - {{ $order->prod_upper_completed_at->format('H:i') }} 
                                                            <span class="font-bold text-teal-600">({{ $order->prod_upper_started_at->diffInMinutes($order->prod_upper_completed_at) }} mnt)</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                        @if($order->prod_cleaning_completed_at) 
                                            <div class="flex items-start gap-2 text-xs">
                                                <span class="text-green-600 font-bold min-w-[50px]">✔ Clean:</span>
                                                <div>
                                                    <div class="font-medium text-gray-700">{{ $order->prodCleaningBy->name ?? 'System' }}</div>
                                                    @if($order->prod_cleaning_started_at)
                                                        <div class="text-[10px] text-gray-500">
                                                            {{ $order->prod_cleaning_started_at->format('H:i') }} - {{ $order->prod_cleaning_completed_at->format('H:i') }} 
                                                            <span class="font-bold text-teal-600">({{ $order->prod_cleaning_started_at->diffInMinutes($order->prod_cleaning_completed_at) }} mnt)</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center items-center gap-2">
                                        <!-- Approve -->
                                        <form action="{{ route('production.approve', $order->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-bold text-xs flex items-center gap-1 shadow hover:shadow-lg transition-all" onclick="return confirm('Sudah dicek dan OK? Lanjut ke QC?')">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                Approve
                                            </button>
                                        </form>

                                        <!-- Follow Up Button -->
                                        <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-report-modal', { detail: {{ $order->id }} }))" class="bg-amber-100 hover:bg-amber-200 text-amber-700 px-3 py-2 rounded-lg font-bold text-xs flex items-center gap-1 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                            Lapor/Follow Up
                                        </button>
                                        
                                        <!-- Revision Modal Trigger -->
                                        <button @click="$dispatch('open-revision-modal', { id: {{ $order->id }}, number: '{{ $order->spk_number }}' })" 
                                                type="button" class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-2 rounded-lg font-bold text-xs flex items-center gap-1 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                            Revisi...
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Revision Modal Instance --}}
            <x-revision-modal currentStage="PRODUCTION" />



        </div>
        
        {{-- FLOATING BULK ACTION BAR (PREMIUM) --}}
        <div x-show="selectedItems.length > 0" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full opacity-0 scale-95"
             x-transition:enter-end="translate-y-0 opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-y-0 opacity-100 scale-100"
             x-transition:leave-end="translate-y-full opacity-0 scale-95"
             class="fixed bottom-6 inset-x-0 z-50 flex justify-center px-4"
             style="display: none;">
            
            <div class="bg-white/80 backdrop-blur-md border border-white/40 shadow-2xl rounded-2xl p-4 w-full max-w-4xl flex flex-col md:flex-row items-center justify-between gap-4 ring-1 ring-gray-900/5">

            
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 bg-gray-900/5 px-3 py-1.5 rounded-lg border border-gray-900/5">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Terpilih</span>
                    <span class="bg-gray-900 text-white px-2 py-0.5 rounded-md font-bold text-sm" x-text="selectedItems.length"></span>
                </div>
                <button @click="selectedItems = []" class="text-xs font-bold text-red-500 hover:text-red-700 hover:bg-red-50 px-3 py-1.5 rounded-lg transition-colors">
                    Batal
                </button>
            </div>

            <div class="h-8 w-px bg-gray-200 hidden md:block"></div>

            <div class="flex items-center gap-2 w-full md:w-auto overflow-x-auto pb-2 md:pb-0 scrollbar-hide justify-end">
                
                {{-- Assign Tech --}}
                <div class="flex items-center gap-2">
                    <div class="relative group">
                        <select id="bulk-tech-select" class="appearance-none bg-white border border-gray-200 text-gray-700 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-48 pl-3 pr-8 py-2.5 font-bold shadow-sm cursor-pointer hover:border-blue-300 transition-colors">
                            <option value="">-- PILIH TEKNISI --</option>
                            <optgroup label="Sol">
                                @foreach($techs['sol'] as $t) <option value="{{ $t->id }}">Sol: {{ $t->name }}</option> @endforeach
                            </optgroup>
                            <optgroup label="Upper">
                                @foreach($techs['upper'] as $t) <option value="{{ $t->id }}">Upper: {{ $t->name }}</option> @endforeach
                            </optgroup>
                            <optgroup label="Treatment/Repaint">
                                @foreach($techs['treatment'] as $t) <option value="{{ $t->id }}">Treat/Rep: {{ $t->name }}</option> @endforeach
                            </optgroup>
                        </select>
                         <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400 group-hover:text-blue-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>

                    <button type="button" onclick="bulkAction('assign')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-xs font-bold shadow hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center gap-2 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Assign
                    </button>
                </div>

                {{-- Finish --}}
                <button type="button" onclick="bulkAction('finish')" class="bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white px-5 py-2.5 rounded-lg text-xs font-bold shadow hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center gap-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Selesai
                </button>
                
                {{-- Approve (Review Tab & All Tab) --}}
                <button type="button" onclick="bulkAction('approve')" x-show="activeTab === 'all' || activeTab.includes('review')" class="bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white px-5 py-2.5 rounded-lg text-xs font-bold shadow hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center gap-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Approve & QC
                </button>
            </div>
            </div>
        </div>
        
    </div>

    {{-- REPORT ISSUE MODAL --}}
    <x-report-modal />

    <script>
    function openReportModal(id) {
        document.getElementById('report_work_order_id').value = id;
        document.getElementById('reportModal').style.display = 'flex';
        document.getElementById('reportModal').classList.remove('hidden');
    }

    function closeReportModal() {
        document.getElementById('reportModal').style.display = 'none';
        document.getElementById('reportModal').classList.add('hidden');
    }
    
    // Existing functions below...
    function bulkAction(action) {
        // Get selected items
        const alpineEl = document.getElementById('production-main-content');
        let selectedItems = [];
        if (alpineEl && Alpine) {
             selectedItems = Alpine.$data(alpineEl).selectedItems;
        } else {
             // Fallback
             selectedItems = Array.from(document.querySelectorAll('input[x-model="selectedItems"]:checked')).map(el => el.value);
        }
        
        if (selectedItems.length === 0) {
            Swal.fire('Peringatan', 'Tidak ada item yang dipilih.', 'warning');
            return;
        }

        if (!confirm(`Yakin ingin memproses ${selectedItems.length} item dengan aksi: ${action.toUpperCase()}?`)) {
            return;
        }

        let techId = null;
        if (action === 'assign' || action === 'start') {
            const selectEl = document.getElementById('bulk-tech-select');
            if (selectEl && selectEl.value) {
                techId = selectEl.value;
            } else if (action === 'assign') {
                Swal.fire('Info', 'Pilih teknisi untuk Assign!', 'info');
                return;
            }
        }

        // Get active tab from URL (Robust & Simple)
        const urlParams = new URLSearchParams(window.location.search);
        // Default to 'sol' if not present
        const activeTab = urlParams.get('tab') || 'sol';

        let type = 'prod_sol';
        if (activeTab === 'upper') type = 'prod_upper';
        if (activeTab === 'treatment') type = 'prod_cleaning';

        // Show loading
        Swal.fire({
            title: 'Memproses...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('{{ route('production.bulk-update') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                ids: selectedItems,
                action: action,
                type: type, 
                technician_id: techId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Check if there are errors despite allow-success
                if (data.errors && data.errors.length > 0) {
                    let errorHtml = '<ul class="text-left text-sm text-red-600 list-disc pl-4 mt-2">';
                    data.errors.forEach(err => {
                        errorHtml += `<li>${err}</li>`;
                    });
                    errorHtml += '</ul>';

                    Swal.fire({
                        icon: 'warning',
                        title: 'Proses Selesai dengan Catatan',
                        html: `${data.message}<br>${errorHtml}`,
                        confirmButtonText: 'OK',
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Error: ' + (data.message || JSON.stringify(data.errors))
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan server.'
            });
        });
    }

    function updateStation(id, type, action = 'finish', finishedAt = null) {
        const fullType = type.replace('item_', ''); // removes 'item_' 
        
        let techId = null;
        if (action === 'start') {
            const selectId = `tech-${type}-${id}`;
            const selectEl = document.getElementById(selectId);
            if (!selectEl) {
                console.error("Select Element not found:", selectId);
                alert("Error: Technician select not found for " + selectId);
                return;
            }
            techId = selectEl.value;
            if (!techId) {
                alert('Silakan pilih teknisi terlebih dahulu.');
                return;
            }
        }

        // if (!confirm('Apakah anda yakin ingin ' + (action === 'start' ? 'memulai' : 'menyelesaikan') + ' proses ini?')) return;
        // Confirm logic moved to Modal/Button for Finish. For Start, we might still want confirm.
        if (action === 'start' && !confirm('Mulai proses ini?')) return;

        // Note: For Repaint & Treatment, we passed type="item_prod_cleaning" in the Blade View.
        // So fullType becomes "prod_cleaning".
        // Use this directly so Controller knows to update prod_cleaning_* columns.

        fetch(`/production/${id}/update-station`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ 
                type: fullType, 
                action: action,
                technician_id: techId,
                finished_at: finishedAt
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Status berhasil diperbarui.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload(); 
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.message
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat update status.'
            });
        });
    }
    function toggleAll(e) {
        // Toggle selected items based on visibility
        const checkboxes = document.querySelectorAll('input[type="checkbox"][x-model="selectedItems"]');
        const alpineEl = document.querySelector('[x-data]');
        let selected = [];
        if (e.target.checked) {
            checkboxes.forEach(cb => {
                 // Check visibility if needed, but usually we just grab all in view
                 selected.push(cb.value);
            });
        }
        // Update Alpine data
        Alpine.$data(alpineEl).selectedItems = selected;
    }
    </script>
</x-app-layout>
