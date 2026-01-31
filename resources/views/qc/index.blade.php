<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm shadow-sm border border-white/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                
                <div class="flex flex-col">
                    <h2 class="font-bold text-xl leading-tight tracking-wide">
                        {{ __('Stasiun Quality Control') }}
                    </h2>
                    <div class="text-xs font-medium opacity-90">
                    Inspeksi & Verifikasi
                    </div>
                </div>
            </div>

        </div>
    </x-slot>

    <div class="py-6 bg-gray-50 min-h-screen" x-data="{ activeTab: '{{ $activeTab }}', selectedItems: [] }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            
            {{-- Premium Stats Overview with Glassmorphism --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                {{-- Jahit Stat - Blue Gradient --}}
                <a href="{{ route('qc.index', ['tab' => 'jahit']) }}"
                     class="group relative overflow-hidden rounded-2xl p-6 cursor-pointer transition-all duration-300 hover:scale-105 hover:shadow-2xl"
                     :class="{ 'ring-4 ring-blue-400 ring-opacity-50': '{{ $activeTab }}' === 'jahit' }">
                    {{-- Gradient Background --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-400 via-blue-500 to-blue-600 opacity-90 group-hover:opacity-100 transition-opacity"></div>
                    
                    {{-- Glassmorphism Overlay --}}
                    <div class="absolute inset-0 backdrop-blur-sm bg-white/10"></div>
                    
                    {{-- Content --}}
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-3">
                            <div class="p-3 bg-white/20 rounded-xl backdrop-blur-md">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                            </div>
                            @if('{{ $activeTab }}' === 'jahit')
                                <span class="px-3 py-1 bg-white/30 backdrop-blur-md rounded-full text-white text-xs font-bold">Active</span>
                            @endif
                        </div>
                        <h3 class="text-white font-black text-lg mb-1">QC Jahit</h3>
                        <p class="text-white/80 text-sm mb-3">Inspeksi jahitan & sol</p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-black text-white">{{ $counts['jahit'] }}</span>
                            <span class="text-white/70 text-sm font-medium">antrian</span>
                        </div>
                    </div>
                </a>

                {{-- Cleanup Stat - Teal Gradient --}}
                <a href="{{ route('qc.index', ['tab' => 'cleanup']) }}"
                     class="group relative overflow-hidden rounded-2xl p-6 cursor-pointer transition-all duration-300 hover:scale-105 hover:shadow-2xl"
                     :class="{ 'ring-4 ring-teal-400 ring-opacity-50': '{{ $activeTab }}' === 'cleanup' }">
                    <div class="absolute inset-0 bg-gradient-to-br from-teal-400 via-teal-500 to-teal-600 opacity-90 group-hover:opacity-100 transition-opacity"></div>
                    <div class="absolute inset-0 backdrop-blur-sm bg-white/10"></div>
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-3">
                            <div class="p-3 bg-white/20 rounded-xl backdrop-blur-md">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 15.536c-1.171 1.952-3.07 1.952-4.242 0-1.172-1.953-1.172-5.119 0-7.072 1.171-1.952 3.07-1.952 4.242 0M8 10.5h4m-4 3h4m9-1.5a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            @if('{{ $activeTab }}' === 'cleanup')
                                <span class="px-3 py-1 bg-white/30 backdrop-blur-md rounded-full text-white text-xs font-bold">Active</span>
                            @endif
                        </div>
                        <h3 class="text-white font-black text-lg mb-1">QC Cleanup</h3>
                        <p class="text-white/80 text-sm mb-3">Pemeriksaan kebersihan</p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-black text-white">{{ $counts['cleanup'] }}</span>
                            <span class="text-white/70 text-sm font-medium">antrian</span>
                        </div>
                    </div>
                </a>

                {{-- Final Stat - Emerald Gradient --}}
                <a href="{{ route('qc.index', ['tab' => 'final']) }}"
                     class="group relative overflow-hidden rounded-2xl p-6 cursor-pointer transition-all duration-300 hover:scale-105 hover:shadow-2xl"
                     :class="{ 'ring-4 ring-emerald-400 ring-opacity-50': '{{ $activeTab }}' === 'final' }">
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-400 via-emerald-500 to-emerald-600 opacity-90 group-hover:opacity-100 transition-opacity"></div>
                    <div class="absolute inset-0 backdrop-blur-sm bg-white/10"></div>
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-3">
                            <div class="p-3 bg-white/20 rounded-xl backdrop-blur-md">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            @if('{{ $activeTab }}' === 'final')
                                <span class="px-3 py-1 bg-white/30 backdrop-blur-md rounded-full text-white text-xs font-bold">Active</span>
                            @endif
                        </div>
                        <h3 class="text-white font-black text-lg mb-1">QC Final</h3>
                        <p class="text-white/80 text-sm mb-3">Verifikasi akhir sebelum selesai</p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-black text-white">{{ $counts['final'] }}</span>
                            <span class="text-white/70 text-sm font-medium">antrian</span>
                        </div>
                    </div>
                </a>

                {{-- All Orders Stat - Gray Gradient --}}
                <a href="{{ route('qc.index', ['tab' => 'all']) }}"
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
                        <p class="text-white/80 text-sm mb-3">Total seluruh antrian QC</p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-black text-white">{{ $counts['all'] }}</span>
                            <span class="text-white/70 text-sm font-medium">order</span>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Filter Bar --}}
            <x-workshop-filter-bar 
                :technicians="data_get($techs, $activeTab, collect([]))"
            />

            {{-- JAHIT Content --}}
            <div x-show="activeTab === 'jahit'" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" style="display: none;">
                <div class="p-4 bg-gradient-to-r from-blue-50 to-blue-100 border-b border-blue-200 flex justify-between items-center">
                    <h3 class="font-bold text-blue-800 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span> Antrian QC Jahit
                    </h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($queues['jahit'] as $key => $order)
                        <div class="flex items-start gap-4 p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex-1">
                                <x-station-card 
                                    :order="$order" 
                                    type="qc_jahit" 
                                    :technicians="$techs['jahit']"
                                    techByRelation="qcJahitBy"
                                    startedAtColumn="qc_jahit_started_at"
                                    byColumn="qc_jahit_by"
                                    color="blue"
                                    titleAction="Inspect"
                                    showCheckbox="true"
                                    :loopIteration="($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration"
                                />
                           </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-400">Tidak ada antrian QC Jahit.</div>
                    @endforelse
                </div>
            </div>

            {{-- CLEANUP Content --}}
            <div x-show="activeTab === 'cleanup'" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" style="display: none;">
                <div class="p-4 bg-gradient-to-r from-teal-50 to-teal-100 border-b border-teal-200 flex justify-between items-center">
                    <h3 class="font-bold text-teal-800 flex items-center gap-2">
                         <span class="w-2 h-2 rounded-full bg-teal-500"></span> Antrian QC Cleanup
                    </h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($queues['cleanup'] as $key => $order)
                        <div class="flex items-start gap-4 p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex-1">
                                <x-station-card 
                                    :order="$order" 
                                    type="qc_cleanup" 
                                    :technicians="$techs['cleanup']"
                                    techByRelation="qcCleanupBy"
                                    startedAtColumn="qc_cleanup_started_at"
                                    byColumn="qc_cleanup_by"
                                    color="teal"
                                    titleAction="Periksa"
                                    showCheckbox="true"
                                    :loopIteration="($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration"
                                />
                           </div>
                        </div>
                    @empty
                         <div class="p-8 text-center text-gray-400">Tidak ada antrian QC Cleanup.</div>
                    @endforelse
                </div>
            </div>

            {{-- FINAL Content --}}
            <div x-show="activeTab === 'final'" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" style="display: none;">
                <div class="p-4 bg-gradient-to-r from-emerald-50 to-emerald-100 border-b border-emerald-200 flex justify-between items-center">
                    <h3 class="font-bold text-emerald-800 flex items-center gap-2">
                         <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Antrian QC Final
                    </h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($queues['final'] as $key => $order)
                        <div class="flex items-start gap-4 p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex-1">
                                <x-station-card 
                                    :order="$order" 
                                    type="qc_final" 
                                    :technicians="$techs['final']"
                                    techByRelation="qcFinalBy"
                                    startedAtColumn="qc_final_started_at"
                                    byColumn="qc_final_by"
                                    color="emerald"
                                    titleAction="Verifikasi"
                                    showCheckbox="true"
                                    :loopIteration="($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration"
                                />
                           </div>
                        </div>
                    @empty
                         <div class="p-8 text-center text-gray-400">Tidak ada antrian QC Final.</div>
                    @endforelse
                </div>
            </div>

            {{-- ADMIN REVIEW SECTION --}}
            @if($queueReview->isNotEmpty())
            <div class="mt-8 mb-8 bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden border-2 border-emerald-400" x-show="activeTab === 'all'">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-500 p-4 text-white flex justify-between items-center">
                    <h3 class="text-lg font-bold flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Menunggu Pemeriksaan Admin (QC Selesai)
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
                                <th class="px-6 py-3">Item</th>
                                <th class="px-6 py-3">Status Pengerjaan (QC Tech)</th>
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
                                <td class="px-6 py-4">{{ $order->shoe_brand }} - {{ $order->shoe_type }}</td>
                                <td class="px-6 py-4">
                                     <div class="flex flex-col gap-2">
                                        @if($order->qc_jahit_completed_at) 
                                            <div class="flex items-start gap-2 text-xs">
                                                <span class="text-green-600 font-bold min-w-[50px]">✔ Jahit:</span>
                                                <div>
                                                    <div class="font-medium text-gray-700">{{ $order->qcJahitBy->name ?? 'System' }}</div>
                                                    @if($order->qc_jahit_started_at)
                                                        <div class="text-[10px] text-gray-500">
                                                            {{ $order->qc_jahit_started_at->format('H:i') }} - {{ $order->qc_jahit_completed_at->format('H:i') }} 
                                                            <span class="font-bold text-teal-600">({{ $order->qc_jahit_started_at->diffInMinutes($order->qc_jahit_completed_at) }} mnt)</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                        @if($order->qc_cleanup_completed_at) 
                                            <div class="flex items-start gap-2 text-xs">
                                                <span class="text-green-600 font-bold min-w-[50px]">✔ Clean:</span>
                                                <div>
                                                    <div class="font-medium text-gray-700">{{ $order->qcCleanupBy->name ?? 'System' }}</div>
                                                    @if($order->qc_cleanup_started_at)
                                                        <div class="text-[10px] text-gray-500">
                                                            {{ $order->qc_cleanup_started_at->format('H:i') }} - {{ $order->qc_cleanup_completed_at->format('H:i') }} 
                                                            <span class="font-bold text-teal-600">({{ $order->qc_cleanup_started_at->diffInMinutes($order->qc_cleanup_completed_at) }} mnt)</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                        @if($order->qc_final_completed_at) 
                                            <div class="flex items-start gap-2 text-xs">
                                                <span class="text-green-600 font-bold min-w-[50px]">✔ Final:</span>
                                                <div>
                                                    <div class="font-medium text-gray-700">{{ $order->qcFinalBy->name ?? 'System' }}</div>
                                                    @if($order->qc_final_started_at)
                                                        <div class="text-[10px] text-gray-500">
                                                            {{ $order->qc_final_started_at->format('H:i') }} - {{ $order->qc_final_completed_at->format('H:i') }} 
                                                            <span class="font-bold text-teal-600">({{ $order->qc_final_started_at->diffInMinutes($order->qc_final_completed_at) }} mnt)</span>
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
                                        <form action="{{ route('qc.approve', $order->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-bold text-xs flex items-center gap-1 shadow hover:shadow-lg transition-all" onclick="return confirm('QC sudah OK semua? Order akan Finish.')">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                Approve & Finish
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
            <x-revision-modal currentStage="QC" />



        </div>


    <div class="mt-8">
        {{ $orders->links() }}
    </div>

    <div x-show="selectedItems.length > 0" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full opacity-0 scale-95"
         x-transition:enter-end="translate-y-0 opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0 opacity-100 scale-100"
         x-transition:leave-end="translate-y-full opacity-0 scale-95"
         class="fixed bottom-6 inset-x-0 z-50 flex justify-center px-4"
         style="display: none;">
        
        <div class="bg-white/90 backdrop-blur-md border border-gray-200 shadow-2xl rounded-2xl p-4 w-full max-w-4xl flex flex-col md:flex-row items-center justify-between gap-4 ring-1 ring-black/5">
            
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 bg-gray-100 px-3 py-1.5 rounded-lg">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Terpilih</span>
                    <span class="bg-gray-800 text-white px-2 py-0.5 rounded-md font-bold text-sm" x-text="selectedItems.length"></span>
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
                        <select id="bulk-qc-tech-select" class="appearance-none bg-white border border-gray-200 text-gray-700 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-40 pl-3 pr-8 py-2.5 font-bold shadow-sm cursor-pointer hover:border-blue-300 transition-colors">
                            <option value="">-- PILIH ADMIN QC --</option>
                            <optgroup label="QC Jahit">
                                @foreach($techs['jahit'] as $t) <option value="{{ $t->id }}">Jahit: {{ $t->name }}</option> @endforeach
                            </optgroup>
                            <optgroup label="Clean Up">
                                @foreach($techs['cleanup'] as $t) <option value="{{ $t->id }}">Clean: {{ $t->name }}</option> @endforeach
                            </optgroup>
                            <optgroup label="Final">
                                @foreach($techs['final'] as $t) <option value="{{ $t->id }}">Final: {{ $t->name }}</option> @endforeach
                            </optgroup>
                        </select>
                         <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400 group-hover:text-blue-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>

                    <button type="button" onclick="bulkAction('start')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-xs font-bold shadow hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center gap-2 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Assign
                    </button>
                </div>

                {{-- Mark Pass --}}
                <button type="button" onclick="bulkAction('checked')" class="bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white px-5 py-2.5 rounded-lg text-xs font-bold shadow hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center gap-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Mark Checked
                </button>

                {{-- Approve (All Tab Only) --}}
                <button type="button" onclick="bulkAction('approve')" x-show="activeTab === 'all'" class="bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white px-5 py-2.5 rounded-lg text-xs font-bold shadow hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center gap-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Approve & Finish
                </button>

                {{-- Bulk Reject --}}
                <button type="button" onclick="bulkAction('reject')" class="bg-white border border-red-200 text-red-600 hover:bg-red-50 px-5 py-2.5 rounded-lg text-xs font-bold shadow-sm hover:shadow transition-all flex items-center gap-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    Reject ke Produksi
                </button>
            </div>
        </div>
    </div>
    </div>

    <script>
    // Toggle all checkboxes
    function toggleAll(event) {
        const isChecked = event.target.checked;
        const checkboxes = document.querySelectorAll('input[x-model="selectedItems"]');
        
        // Update Alpine data
        const mainEl = document.querySelector('[x-data*="selectedItems"]');
        if (mainEl && Alpine) {
            const alpineData = Alpine.$data(mainEl);
            if (isChecked) {
                alpineData.selectedItems = Array.from(checkboxes).map(cb => cb.value);
            } else {
                alpineData.selectedItems = [];
            }
        }
        
        // Also update DOM directly for reliability
        checkboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
        });
    }

    window.bulkAction = function(action) {
        // Direct DOM selection is often more reliable than trying to find the right Alpine scope
        const checkedInputs = document.querySelectorAll('input[x-model="selectedItems"]:checked');
        let selectedItems = Array.from(checkedInputs).map(el => el.value);

        // If DOM fails, try Alpine
        if (selectedItems.length === 0) {
            try {
                const mainEl = document.querySelector('[x-data*="selectedItems"]');
                if (mainEl && Alpine) {
                    selectedItems = Alpine.$data(mainEl).selectedItems;
                }
            } catch (e) { console.warn("Alpine selection failed", e); }
        }

        if (selectedItems.length === 0) {
            Swal.fire({ icon: 'warning', title: 'Pilih item', text: 'Tidak ada order yang dipilih.' });
            return;
        }

        let techId = null;
        if (action === 'start') {
             const selectEl = document.getElementById('bulk-qc-tech-select');
             if (selectEl && selectEl.value) {
                 techId = selectEl.value;
             } else {
                 Swal.fire({ icon: 'warning', title: 'Pilih Teknisi', text: 'Silakan pilih teknisi/admin untuk Assign.' });
                 return;
             }
        }

        Swal.fire({
            title: 'Konfirmasi Bulk Action',
            text: `Proses ${selectedItems.length} item dengan aksi: ${action.toUpperCase()}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Lanjutkan!'
        }).then((result) => {
            if (result.isConfirmed) {
                const urlParams = new URLSearchParams(window.location.search);
                const activeTab = urlParams.get('tab') || '{{ $activeTab }}';
                
                let type = 'qc_jahit'; 
                if (activeTab === 'cleanup') type = 'qc_cleanup';
                else if (activeTab === 'final') type = 'qc_final';

                fetch('{{ route('qc.bulk-update') }}', {
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
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
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
                        title: 'Server Error',
                        text: 'Terjadi kesalahan pada request.'
                    });
                });
            }
        });
    }

    // Single item update
    function updateStation(id, type, action = 'finish', finishedAt = null) {
        
        let techId = null;
        if (action === 'start') {
            const selectId = `tech-${type}-${id}`;
            const selectEl = document.getElementById(selectId);
            if (!selectEl) {
                 // Try generic ID fallback if type includes prefix
                 const cleanType = type.replace('qc_', '');
                 const selectId2 = `tech-${cleanType}-${id}`; // e.g. tech-jahit-123
                 const selectEl2 = document.getElementById(selectId2);
                 
                 if (selectEl2) techId = selectEl2.value;
                 else {
                    console.error("Select Element not found:", selectId);
                    alert("Error: Technician select not found for " + selectId);
                    return;
                }
            } else {
                techId = selectEl.value;
            }
            
            if (!techId) {
                alert('Silakan pilih teknisi terlebih dahulu.');
                return;
            }
        }

        if (action === 'start' && !confirm('Mulai proses ini?')) return;

        fetch(`/qc/${id}/update-station`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ 
                type: type, 
                action: action,
                technician_id: techId,
                finished_at: finishedAt
            })
        })
        .then(async response => {
            const data = await response.json().catch(() => ({})); 
            if (!response.ok) {
                throw new Error(data.message || response.statusText || 'Server Error ' + response.status);
            }
            return data;
        })
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
                text: 'Terjadi kesalahan: ' + error.message
            });
        });
    }
    </script>
    <x-report-modal />
</x-app-layout>
