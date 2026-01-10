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
            <div class="flex space-x-1 mb-6 bg-white p-1 rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
                {{-- Sol Tab --}}
                <button @click="activeTab = 'sol'" 
                    :class="{ 'bg-orange-50 text-orange-700 shadow-sm border-orange-200': activeTab === 'sol', 'text-gray-500 hover:bg-gray-50': activeTab !== 'sol' }"
                    class="flex-1 py-2.5 px-4 rounded-lg text-sm font-bold transition-all flex items-center justify-center gap-2 border border-transparent">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    SOL REPARASI
                    <span class="ml-2 px-1.5 py-0.5 bg-orange-200 text-orange-800 rounded-full text-[10px]">
                        {{ $queues['sol']->whereNull('prod_sol_completed_at')->count() }}
                    </span>
                </button>

                {{-- Upper Tab (Standalone) --}}
                <button @click="activeTab = 'upper'" 
                    :class="{ 'bg-purple-50 text-purple-700 shadow-sm border-purple-200': activeTab === 'upper', 'text-gray-500 hover:bg-gray-50': activeTab !== 'upper' }"
                    class="flex-1 py-2.5 px-4 rounded-lg text-sm font-bold transition-all flex items-center justify-center gap-2 border border-transparent">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 15.536c-1.171 1.952-3.07 1.952-4.242 0-1.172-1.953-1.172-5.119 0-7.072 1.171-1.952 3.07-1.952 4.242 0M8 10.5h4m-4 3h4m9-1.5a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    UPPER REPARASI
                    <span class="ml-2 px-1.5 py-0.5 bg-purple-200 text-purple-800 rounded-full text-[10px]">
                        {{ $queues['upper']->whereNull('prod_upper_completed_at')->count() }}
                    </span>
                </button>

                {{-- Repaint & Treatment Tab --}}
                <button @click="activeTab = 'treatment'" 
                    :class="{ 'bg-teal-50 text-teal-700 shadow-sm border-teal-200': activeTab === 'treatment', 'text-gray-500 hover:bg-gray-50': activeTab !== 'treatment' }"
                    class="flex-1 py-2.5 px-4 rounded-lg text-sm font-bold transition-all flex items-center justify-center gap-2 border border-transparent">
                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                    REPAINT & TREATMENT
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
                            />
                             {{-- NOTE: We use item_prod_cleaning type to match ProductionController startedAtColumn map which maps 'cleaning' to prod_cleaning_* --}}
                             {{-- Wait, the ProductionController 'updateStation' receives 'type' from JS request. --}}
                             {{-- If we pass 'item_prod_cleaning', JS strips 'item_', sends 'prod_cleaning'. --}}
                             {{-- Controller receives 'prod_cleaning'. handleStationUpdate sees 'prod_cleaning'. --}}
                             {{-- It updates 'prod_cleaning_started_at'. This matches our reuse of logic. --}}
                        @endif
                    @empty
                         <div class="p-8 text-center text-gray-400">Tidak ada antrian repaint/treatment.</div>
                    @endforelse
                </div>
            </div>

            {{-- All Orders Table --}}
            <div x-show="activeTab === 'all'" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" style="display: none;">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
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
                                    <div class="font-bold">{{ $order->customer_name }}</div>
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
                                        <form action="{{ route('production.finish', $order->id) }}" method="POST" onsubmit="return confirm('Kirim ke QC? Order akan pindah ke tahap QC.');">
                                            @csrf
                                            <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-4 py-2 rounded-lg text-xs font-bold shadow-md transform hover:-translate-y-0.5 transition-all flex items-center gap-1 ml-auto">
                                                <span>KIRIM KE QC</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                            </button>
                                        </form>
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
    function updateStation(id, type, action = 'finish') {
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

        if (!confirm('Apakah anda yakin ingin ' + (action === 'start' ? 'memulai' : 'menyelesaikan') + ' proses ini?')) return;

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
                technician_id: techId
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
