<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-2 bg-gradient-to-r from-orange-500 to-red-600 rounded-lg shadow-sm">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                </svg>
            </div>
            
            <div class="flex flex-col">
                <h2 class="font-bold text-xl leading-tight tracking-wide text-gray-800">
                    {{ __('Stasiun Produksi') }}
                </h2>
                <div class="text-xs font-bold text-orange-600">
                   Proses & Pelacakan
                </div>
            </div>
            
             <div class="ml-auto">
                <div class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-bold border border-orange-200">
                    {{ $orders->count() }} Order Aktif
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50/50 min-h-screen" x-data="{ activeTab: 'sol' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Tabs Navigation --}}
            <div class="flex space-x-1 mb-6 bg-white p-1 rounded-xl shadow-sm border border-gray-100 overflow-x-auto scrollbar-hide">
                {{-- Sol Tab --}}
                <button @click="activeTab = 'sol'" 
                    :class="{ 'bg-orange-50 text-orange-700 shadow-sm border-orange-200': activeTab === 'sol', 'text-gray-500 hover:bg-gray-50': activeTab !== 'sol' }"
                    class="flex-1 py-2.5 px-4 rounded-lg text-sm font-bold transition-all flex items-center justify-center gap-2 border border-transparent">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    ANTRIAN REPARASI SOL
                    <span class="ml-2 px-1.5 py-0.5 bg-orange-200 text-orange-800 rounded-full text-[10px]">
                        {{ $queues['sol']->whereNull('prod_sol_completed_at')->count() }}
                    </span>
                </button>

                {{-- Upper Tab (Standalone) --}}
                <button @click="activeTab = 'upper'" 
                    :class="{ 'bg-purple-50 text-purple-700 shadow-sm border-purple-200': activeTab === 'upper', 'text-gray-500 hover:bg-gray-50': activeTab !== 'upper' }"
                    class="flex-1 py-2.5 px-4 rounded-lg text-sm font-bold transition-all flex items-center justify-center gap-2 border border-transparent">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 15.536c-1.171 1.952-3.07 1.952-4.242 0-1.172-1.953-1.172-5.119 0-7.072 1.171-1.952 3.07-1.952 4.242 0M8 10.5h4m-4 3h4m9-1.5a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    ANTRIAN REPARASI UPPER
                    <span class="ml-2 px-1.5 py-0.5 bg-purple-200 text-purple-800 rounded-full text-[10px]">
                        {{ $queues['upper']->whereNull('prod_upper_completed_at')->count() }}
                    </span>
                </button>

                {{-- Repaint & Treatment Tab --}}
                <button @click="activeTab = 'treatment'" 
                    :class="{ 'bg-teal-50 text-teal-700 shadow-sm border-teal-200': activeTab === 'treatment', 'text-gray-500 hover:bg-gray-50': activeTab !== 'treatment' }"
                    class="flex-1 py-2.5 px-4 rounded-lg text-sm font-bold transition-all flex items-center justify-center gap-2 border border-transparent">
                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                    ANTRIAN REPAINT & TREATMENT
                    <span class="ml-2 px-1.5 py-0.5 bg-teal-200 text-teal-800 rounded-full text-[10px]">
                        {{ $queues['treatment']->whereNull('prod_cleaning_completed_at')->count() }}
                    </span>
                </button>

                {{-- All Orders Tab --}}
                <button @click="activeTab = 'all'" 
                    :class="{ 'bg-gray-800 text-white shadow-md': activeTab === 'all', 'text-gray-600 hover:bg-gray-100': activeTab !== 'all' }"
                    class="flex-1 py-2.5 px-4 rounded-lg text-sm font-bold transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    SEMUA ORDER
                </button>
            </div>

            {{-- SOL Content --}}
            <div x-show="activeTab === 'sol'" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" style="display: none;">
                <div class="p-4 bg-orange-50 border-b border-orange-100 flex justify-between items-center">
                    <h3 class="font-bold text-orange-800 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-orange-500"></span> Antrian Reparasi Sol
                    </h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($queues['sol'] as $order)
                        @if(!$order->prod_sol_completed_at)
                             <x-station-card 
                                :order="$order" 
                                type="item_prod_sol" 
                                :technicians="$techs['sol']"
                                techByRelation="prodSolBy"
                                startedAtColumn="prod_sol_started_at"
                                byColumn="prod_sol_by"
                                color="orange"
                                titleAction="Assign"
                            />
                        @endif
                    @empty
                        <div class="p-8 text-center text-gray-400">Tidak ada antrian sol.</div>
                    @endforelse
                </div>
            </div>

            {{-- UPPER Content --}}
            <div x-show="activeTab === 'upper'" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" style="display: none;">
                <div class="p-4 bg-purple-50 border-b border-purple-100 flex justify-between items-center">
                    <h3 class="font-bold text-purple-800 flex items-center gap-2">
                         <span class="w-2 h-2 rounded-full bg-pink-500"></span> Antrian Reprasi Upper
                    </h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($queues['upper'] as $order)
                        @if(!$order->prod_upper_completed_at)
                             <x-station-card 
                                :order="$order" 
                                type="item_prod_upper" 
                                :technicians="$techs['upper']"
                                techByRelation="prodUpperBy"
                                startedAtColumn="prod_upper_started_at"
                                byColumn="prod_upper_by"
                                color="purple"
                                titleAction="Assign"
                            />
                        @endif
                    @empty
                         <div class="p-8 text-center text-gray-400">Tidak ada antrian upper.</div>
                    @endforelse
                </div>
            </div>

            {{-- REPAINT & TREATMENT Content --}}
             <div x-show="activeTab === 'treatment'" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" style="display: none;">
                <div class="p-4 bg-teal-50 border-b border-teal-100 flex justify-between items-center">
                    <h3 class="font-bold text-teal-800 flex items-center gap-2">
                         <span class="w-2 h-2 rounded-full bg-purple-500"></span> Antrian Repaint & Treatment
                    </h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($queues['treatment'] as $order)
                        @if(!$order->prod_cleaning_completed_at)
                             <x-station-card 
                                :order="$order" 
                                type="item_prod_cleaning" 
                                :technicians="$techs['treatment']"
                                techByRelation="prodCleaningBy"
                                startedAtColumn="prod_cleaning_started_at"
                                byColumn="prod_cleaning_by"
                                color="teal"
                                titleAction="Assign"
                            />
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
                                <th class="px-6 py-3">SPK</th>
                                <th class="px-6 py-3">Item</th>
                                <th class="px-6 py-3">Status Pengerjaan (Technician)</th>
                                <th class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($queueReview as $order)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 font-bold text-gray-800 dark:text-gray-200">{{ $order->spk_number }}</td>
                                <td class="px-6 py-4">{{ $order->shoe_brand }} - {{ $order->shoe_type }}</td>
                                <td class="px-6 py-4">
                                     <div class="flex flex-col gap-1">
                                        @if($order->prod_sol_completed_at) 
                                            <span class="text-xs text-green-600 flex items-center gap-1">✔ Sol: {{ $order->prodSolBy->name ?? 'System' }}</span> 
                                        @endif
                                        @if($order->prod_upper_completed_at) 
                                            <span class="text-xs text-green-600 flex items-center gap-1">✔ Upper: {{ $order->prodUpperBy->name ?? 'System' }}</span> 
                                        @endif
                                        @if($order->prod_cleaning_completed_at) 
                                            <span class="text-xs text-green-600 flex items-center gap-1">✔ Cleaning/Repaint: {{ $order->prodCleaningBy->name ?? 'System' }}</span> 
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
                                        
                                        <!-- Reject Modal Trigger -->
                                        <div x-data="{ openRevisi: false }">
                                            <button @click="openRevisi = true" type="button" class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-2 rounded-lg font-bold text-xs flex items-center gap-1 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                Revisi...
                                            </button>

                                            <!-- Modal -->
                                            <div x-show="openRevisi" class="fixed inset-0 z-[999] flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display: none;" x-transition>
                                                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 w-80 max-w-full text-left" @click.away="openRevisi = false">
                                                    <h3 class="font-bold text-lg text-gray-800 dark:text-gray-200 mb-4 border-b dark:border-gray-700 pb-2">Revisi Produksi</h3>
                                                    <p class="text-xs text-gray-500 mb-3">Pilih proses yang perlu dikerjakan ulang:</p>

                                                    <form action="{{ route('production.reject', $order->id) }}" method="POST" class="space-y-3">
                                                        @csrf
                                                        
                                                        @if($order->prod_sol_completed_at)
                                                        <div>
                                                            <label class="flex items-center gap-2 mb-1 cursor-pointer">
                                                                <input type="radio" name="target_station" value="prod_sol" class="text-red-600" required>
                                                                <span class="font-bold text-sm text-gray-700 dark:text-gray-300">Reparasi Sol</span>
                                                            </label>
                                                        </div>
                                                        @endif
                                                        
                                                        @if($order->prod_upper_completed_at)
                                                        <div>
                                                            <label class="flex items-center gap-2 mb-1 cursor-pointer">
                                                                <input type="radio" name="target_station" value="prod_upper" class="text-red-600" required>
                                                                <span class="font-bold text-sm text-gray-700 dark:text-gray-300">Reparasi Upper</span>
                                                            </label>
                                                        </div>
                                                        @endif

                                                        @if($order->prod_cleaning_completed_at)
                                                        <div>
                                                            <label class="flex items-center gap-2 mb-1 cursor-pointer">
                                                                <input type="radio" name="target_station" value="prod_cleaning" class="text-red-600" required>
                                                                <span class="font-bold text-sm text-gray-700 dark:text-gray-300">Cleaning / Repaint</span>
                                                            </label>
                                                        </div>
                                                        @endif
                                                        
                                                        <textarea name="reason" rows="2" class="w-full text-sm border-gray-300 dark:bg-gray-900 dark:border-gray-600 dark:text-white rounded focus:border-red-500 focus:ring-red-500" placeholder="Alasan revisi..." required></textarea>

                                                        <div class="flex justify-end gap-2 mt-2">
                                                            <button type="button" @click="openRevisi = false" class="px-3 py-1.5 bg-gray-100 text-gray-600 rounded text-xs font-bold hover:bg-gray-200">Batal</button>
                                                            <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded text-xs font-bold hover:bg-red-700 shadow">Kirim Revisi</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- All Orders Table --}}
            <div x-show="activeTab === 'all'" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" style="display: none;">
                <div class="overflow-x-auto -mx-4 sm:mx-0">
                    <table class="min-w-full w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3">SPK</th>
                                <th class="px-6 py-3">Pelanggan</th>
                                <th class="px-6 py-3">Sol</th>
                                <th class="px-6 py-3">Upper</th>
                                <th class="px-6 py-3">Repaint & Treatment</th>
                                <th class="px-6 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-bold font-mono text-gray-900">{{ $order->spk_number }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-bold flex items-center gap-2">
                                        {{ $order->customer_name }}
                                        @if($order->is_revising)
                                            <span class="bg-red-100 text-red-600 text-[10px] font-bold px-2 py-0.5 rounded-full border border-red-200 animate-pulse">
                                                REVISI
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-400">{{ $order->shoe_brand }} - {{ $order->shoe_color }}</div>
                                </td>
                                
                                {{-- Sol Status --}}
                                <td class="px-6 py-4">
                                    @php
                                        // TODO: Refactor 'needs_sol', 'needs_upper' to properly reflect new grouping if needed.
                                        // For now, assuming standard accessors work.
                                        $hasSol = $order->services->contains(fn($s) => stripos($s->category, 'sol') !== false);
                                    @endphp
                                    @if($hasSol)
                                        @if($order->prod_sol_completed_at)
                                            <div class="inline-flex flex-col items-center">
                                                <span class="text-green-500 font-bold text-xs">✔ SELESAI</span>
                                                <span class="text-[10px] text-gray-400 mb-1">{{ $order->prodSolBy->name ?? 'System' }}</span>
                                                
                                                <div class="text-[10px] text-gray-500 bg-gray-50 rounded px-1.5 py-0.5 border border-gray-100 dark:border-gray-700">
                                                    @if($order->prod_sol_started_at)
                                                        <div class="flex justify-between gap-2"><span>Mulai:</span> <span>{{ $order->prod_sol_started_at->format('H:i') }}</span></div>
                                                        <div class="flex justify-between gap-2"><span>Selesai:</span> <span>{{ $order->prod_sol_completed_at->format('H:i') }}</span></div>
                                                        <div class="border-t border-gray-200 mt-0.5 pt-0.5 font-bold text-center text-teal-600">
                                                            ({{ $order->prod_sol_started_at->diffInMinutes($order->prod_sol_completed_at) }} mnt)
                                                        </div>
                                                    @else
                                                        <div>Selesai: {{ $order->prod_sol_completed_at->format('H:i') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @elseif($order->prod_sol_started_at)
                                            <div class="inline-flex flex-col items-center">
                                                <span class="text-orange-600 font-bold text-xs">⚡ PROSES</span>
                                                <span class="text-[10px] text-gray-400 mb-1">{{ $order->prodSolBy->name ?? '-' }}</span>
                                                <span class="text-[10px] text-gray-500 bg-orange-50 px-1 rounded">Mulai: {{ $order->prod_sol_started_at->format('H:i') }}</span>
                                            </div>
                                        @else
                                            <span class="px-2 py-1 bg-gray-100 text-gray-500 rounded text-[10px] text-center block w-fit">Antrian</span>
                                        @endif
                                    @else
                                        <span class="text-gray-300">-</span>
                                    @endif
                                </td>

                                {{-- Upper Status --}}
                                <td class="px-6 py-4">
                                    @php
                                        $hasUpper = $order->services->contains(fn($s) => stripos($s->category, 'upper') !== false);
                                    @endphp
                                    @if($hasUpper)
                                        @if($order->prod_upper_completed_at)
                                            <div class="inline-flex flex-col items-center">
                                                <span class="text-green-500 font-bold text-xs">✔ SELESAI</span>
                                                <span class="text-[10px] text-gray-400 mb-1">{{ $order->prodUpperBy->name ?? 'System' }}</span>
                                                
                                                <div class="text-[10px] text-gray-500 bg-gray-50 rounded px-1.5 py-0.5 border border-gray-100 dark:border-gray-700">
                                                    @if($order->prod_upper_started_at)
                                                        <div class="flex justify-between gap-2"><span>Mulai:</span> <span>{{ $order->prod_upper_started_at->format('H:i') }}</span></div>
                                                        <div class="flex justify-between gap-2"><span>Selesai:</span> <span>{{ $order->prod_upper_completed_at->format('H:i') }}</span></div>
                                                        <div class="border-t border-gray-200 mt-0.5 pt-0.5 font-bold text-center text-teal-600">
                                                            ({{ $order->prod_upper_started_at->diffInMinutes($order->prod_upper_completed_at) }} mnt)
                                                        </div>
                                                    @else
                                                        <div>Selesai: {{ $order->prod_upper_completed_at->format('H:i') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @elseif($order->prod_upper_started_at)
                                            <div class="inline-flex flex-col items-center">
                                                <span class="text-purple-600 font-bold text-xs">⚡ PROSES</span>
                                                <span class="text-[10px] text-gray-400 mb-1">{{ $order->prodUpperBy->name ?? '-' }}</span>
                                                <span class="text-[10px] text-gray-500 bg-purple-50 px-1 rounded">Mulai: {{ $order->prod_upper_started_at->format('H:i') }}</span>
                                            </div>
                                        @else
                                            <span class="px-2 py-1 bg-gray-100 text-gray-500 rounded text-[10px] text-center block w-fit">Antrian</span>
                                        @endif
                                    @else
                                        <span class="text-gray-300">-</span>
                                    @endif
                                </td>

                                {{-- Repaint & Treatment Status --}}
                                <td class="px-6 py-4">
                                    @php
                                        $hasTreatment = $order->services->contains(fn($s) => 
                                            stripos($s->category, 'cleaning') !== false || 
                                            stripos($s->category, 'whitening') !== false || 
                                            stripos($s->category, 'repaint') !== false ||
                                            stripos($s->category, 'treatment') !== false
                                        );
                                    @endphp
                                    @if($hasTreatment)
                                        @if($order->prod_cleaning_completed_at)
                                            <div class="inline-flex flex-col items-center">
                                                <span class="text-green-500 font-bold text-xs">✔ SELESAI</span>
                                                <span class="text-[10px] text-gray-400 mb-1">{{ $order->prodCleaningBy->name ?? 'System' }}</span>
                                                
                                                <div class="text-[10px] text-gray-500 bg-gray-50 rounded px-1.5 py-0.5 border border-gray-100 dark:border-gray-700">
                                                    @if($order->prod_cleaning_started_at)
                                                        <div class="flex justify-between gap-2"><span>Mulai:</span> <span>{{ $order->prod_cleaning_started_at->format('H:i') }}</span></div>
                                                        <div class="flex justify-between gap-2"><span>Selesai:</span> <span>{{ $order->prod_cleaning_completed_at->format('H:i') }}</span></div>
                                                        <div class="border-t border-gray-200 mt-0.5 pt-0.5 font-bold text-center text-teal-600">
                                                            ({{ $order->prod_cleaning_started_at->diffInMinutes($order->prod_cleaning_completed_at) }} mnt)
                                                        </div>
                                                    @else
                                                        <div>Selesai: {{ $order->prod_cleaning_completed_at->format('H:i') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @elseif($order->prod_cleaning_started_at)
                                            <div class="inline-flex flex-col items-center">
                                                <span class="text-teal-600 font-bold text-xs">⚡ PROSES</span>
                                                <span class="text-[10px] text-gray-400 mb-1">{{ $order->prodCleaningBy->name ?? '-' }}</span>
                                                <span class="text-[10px] text-gray-500 bg-teal-50 px-1 rounded">Mulai: {{ $order->prod_cleaning_started_at->format('H:i') }}</span>
                                            </div>
                                        @else
                                            <span class="px-2 py-1 bg-gray-100 text-gray-500 rounded text-[10px] text-center block w-fit">Antrian</span>
                                        @endif
                                    @else
                                        <span class="text-gray-300">-</span>
                                    @endif
                                </td>

                                {{-- Action --}}
                                <td class="px-6 py-4 text-right">
                                    @php
                                        // Determine overall readiness manually
                                        $solReady = !$hasSol || $order->prod_sol_completed_at;
                                        $upperReady = !$hasUpper || $order->prod_upper_completed_at;
                                        // Treatment includes cleaning, whitening, repaint, treatment
                                        $treatmentReady = !$hasTreatment || $order->prod_cleaning_completed_at;

                                        $isReady = $solReady && $upperReady && $treatmentReady;
                                    @endphp

                                    @if($isReady)
                                        <div class="flex flex-col items-end gap-1">
                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-bold border border-yellow-200 animate-pulse">
                                                ⏳ Menunggu Approval
                                            </span>
                                            <span class="text-[10px] text-gray-400">Lihat bagian atas</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-orange-400 italic">Proses Belum Selesai</span>
                                    @endif
                                </td>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script>
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
                window.location.reload(); 
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat update status.');
        });
    }
    </script>
</x-app-layout>
