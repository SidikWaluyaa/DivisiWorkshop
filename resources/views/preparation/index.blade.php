<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-2 bg-gradient-to-br from-teal-500 to-teal-600 rounded-lg shadow-lg text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
            </div>
            
            <div class="flex flex-col">
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    {{ __('Stasiun Persiapan') }}
                </h2>
                <div class="text-xs text-gray-500">
                    Proses Cuci, Bongkar Sol, dan Bongkar Upper
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-gray-50" x-data="{ activeTab: 'washing' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Stats Overview --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                {{-- Washing Stat --}}
                <div @click="activeTab = 'washing'" 
                     class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 cursor-pointer transition-all hover:shadow-md"
                     :class="{ 'ring-2 ring-teal-500 bg-teal-50': activeTab === 'washing' }">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="text-xs font-bold text-gray-500 uppercase">Antrian Cuci</div>
                            <div class="text-2xl font-black text-gray-800">{{ $queueWashing->count() }}</div>
                        </div>
                        <span class="text-2xl">🧼</span>
                    </div>
                </div>

                {{-- Sol Stat --}}
                <div @click="activeTab = 'sol'" 
                     class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 cursor-pointer transition-all hover:shadow-md"
                     :class="{ 'ring-2 ring-orange-500 bg-orange-50': activeTab === 'sol' }">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="text-xs font-bold text-gray-500 uppercase">Antrian Sol</div>
                            <div class="text-2xl font-black text-gray-800">{{ $queueSol->count() }}</div>
                        </div>
                        <span class="text-2xl">👟</span>
                    </div>
                </div>

                {{-- Upper Stat --}}
                <div @click="activeTab = 'upper'" 
                     class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 cursor-pointer transition-all hover:shadow-md"
                     :class="{ 'ring-2 ring-purple-500 bg-purple-50': activeTab === 'upper' }">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="text-xs font-bold text-gray-500 uppercase">Antrian Upper</div>
                            <div class="text-2xl font-black text-gray-800">{{ $queueUpper->count() }}</div>
                        </div>
                        <span class="text-2xl">🎨</span>
                    </div>
                </div>

                {{-- Final Check --}}
                <div @click="activeTab = 'all'" 
                     class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 cursor-pointer transition-all hover:shadow-md"
                     :class="{ 'ring-2 ring-blue-500 bg-blue-50': activeTab === 'all' }">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="text-xs font-bold text-gray-500 uppercase">Semua Order</div>
                            <div class="text-2xl font-black text-gray-800">{{ $allOrders->count() }}</div>
                        </div>
                        <span class="text-2xl">📋</span>
                    </div>
                </div>
            </div>

            {{-- Tab Content --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden min-h-[500px]">
                
                {{-- Washing Station --}}
                <div x-show="activeTab === 'washing'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="p-4 border-b border-gray-100 bg-teal-50 flex justify-between items-center">
                        <h3 class="font-bold text-teal-800 flex items-center gap-2">
                            <span>🧼 Station Washing & Cleaning</span>
                            <span class="px-2 py-0.5 bg-white rounded-full text-xs border border-teal-200">{{ $queueWashing->count() }} items</span>
                        </h3>
                    </div>
                    @if($queueWashing->count() > 0)
                        <div class="divide-y divide-gray-100">
                            @foreach($queueWashing as $order)
                                <div class="p-4 hover:bg-gray-50 transition-colors flex items-center justify-between group">
                                    <div class="flex gap-4 items-center">
                                        <div class="font-mono font-bold text-gray-600 bg-gray-100 px-2 py-1 rounded text-sm">{{ $order->spk_number }}</div>
                                        <div>
                                            <div class="font-bold text-gray-800">{{ $order->shoe_brand }} {{ $order->shoe_type }}</div>
                                            <div class="text-xs text-gray-500">{{ $order->shoe_color }} • {{ $order->customer_name }}</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        {{-- Services Tag --}}
                                        <div class="text-right hidden sm:block">
                                            @foreach($order->services as $s)
                                                <span class="text-[10px] uppercase bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded border border-gray-200">{{ $s->name }}</span>
                                            @endforeach
                                        </div>

                                        {{-- Quick Action Button --}}
                                        {{-- Quick Action Button --}}
                                        @if(!$order->prep_washing_by)
                                            <div class="flex items-center gap-2">
                                                <select id="tech-washing-{{ $order->id }}" class="text-xs border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                                                    <option value="">-- Pilih Teknisi --</option>
                                                    @foreach($techWashing as $t)
                                                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="button" onclick="updateStation({{ $order->id }}, 'washing', 'start')" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs font-bold uppercase tracking-wide transition-all shadow hover:shadow-md">
                                                    Assign
                                                </button>
                                            </div>
                                        @elseif($order->prep_washing_by)
                                            <div class="flex flex-col items-end gap-1">
                                                <div class="text-right">
                                                    <span class="text-[10px] text-gray-400 block">Dikerjakan oleh:</span>
                                                    <span class="font-bold text-xs text-teal-600 bg-teal-50 px-2 py-0.5 rounded border border-teal-100">{{ $order->prepWashingBy->name ?? '...' }}</span>
                                                    @if($order->prep_washing_started_at)
                                                        <span class="text-[10px] text-gray-500 block mt-0.5">Mulai: {{ $order->prep_washing_started_at->format('H:i') }}</span>
                                                    @endif
                                                </div>
                                                <button type="button" onclick="updateStation({{ $order->id }}, 'washing', 'finish')" class="flex items-center gap-2 px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white rounded text-xs font-bold uppercase tracking-wide transition-all shadow hover:shadow-md">
                                                    <span>✔ Selesai</span>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-12 text-center text-gray-400">
                            <span class="text-4xl block mb-2">✨</span>
                            <p>Tidak ada antrian cuci.</p>
                        </div>
                    @endif
                </div>

                {{-- Sol Station --}}
                <div x-show="activeTab === 'sol'" x-transition>
                    <div class="p-4 border-b border-gray-100 bg-orange-50 flex justify-between items-center">
                        <h3 class="font-bold text-orange-800 flex items-center gap-2">
                            <span>👟 Station Bongkar Sol</span>
                            <span class="px-2 py-0.5 bg-white rounded-full text-xs border border-orange-200">{{ $queueSol->count() }} item</span>
                        </h3>
                    </div>
                     @if($queueSol->count() > 0)
                        <div class="divide-y divide-gray-100">
                            @foreach($queueSol as $order)
                                @include('preparation.partials.station-card', [
                                    'order' => $order,
                                    'type' => 'sol',
                                    'technicians' => $techSol,
                                    'techByRelation' => 'prepSolBy',
                                    'startedAtColumn' => 'prep_sol_started_at',
                                    'byColumn' => 'prep_sol_by'
                                ])
                            @endforeach
                        </div>
                    @else
                        <div class="p-12 text-center text-gray-400">
                            <span class="text-4xl block mb-2">✅</span>
                            <p>Antrian Bongkar Sol kosong.</p>
                        </div>
                    @endif
                </div>

                {{-- Upper Station --}}
                <div x-show="activeTab === 'upper'" x-transition>
                    <div class="p-4 border-b border-gray-100 bg-purple-50 flex justify-between items-center">
                        <h3 class="font-bold text-purple-800 flex items-center gap-2">
                            <span>🎨 Station Bongkar Upper & Repaint</span>
                            <span class="px-2 py-0.5 bg-white rounded-full text-xs border border-purple-200">{{ $queueUpper->count() }} items</span>
                        </h3>
                    </div>
                    @if($queueUpper->count() > 0)
                        <div class="divide-y divide-gray-100">
                            @foreach($queueUpper as $order)
                                @include('preparation.partials.station-card', [
                                    'order' => $order,
                                    'type' => 'upper',
                                    'technicians' => $techUpper,
                                    'techByRelation' => 'prepUpperBy',
                                    'startedAtColumn' => 'prep_upper_started_at',
                                    'byColumn' => 'prep_upper_by'
                                ])
                            @endforeach
                        </div>
                    @else
                        <div class="p-12 text-center text-gray-400">
                            <span class="text-4xl block mb-2">✅</span>
                            <p>Antrian Bongkar Upper kosong.</p>
                        </div>
                    @endif
                </div>

                {{-- All Orders / Progress View --}}
                <div x-show="activeTab === 'all'" x-transition>
                    <div class="p-4 border-b border-gray-100 bg-blue-50 flex justify-between items-center">
                        <h3 class="font-bold text-blue-800">📋 Semua Order di Preparation</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-100 text-gray-600 text-xs uppercase font-bold">
                                <tr>
                                    <th class="px-4 py-3">SPK</th>
                                    <th class="px-4 py-3">Info Sepatu</th>
                                    <th class="px-4 py-3 text-center">Washing</th>
                                    <th class="px-4 py-3 text-center">Sol</th>
                                    <th class="px-4 py-3 text-center">Upper</th>
                                    <th class="px-4 py-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($allOrders as $order)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 font-mono font-bold text-gray-600">{{ $order->spk_number }}</td>
                                        <td class="px-4 py-3">
                                            <div class="font-bold text-gray-800">{{ $order->shoe_brand }}</div>
                                            <div class="text-xs text-gray-500">{{ $order->customer_name }}</div>
                                        </td>
                                        
                                        {{-- Washing Status --}}
                                        <td class="px-4 py-3 text-center">
                                            @if($order->prep_washing_completed_at)
                                                <div class="inline-flex flex-col items-center">
                                                    <span class="text-green-500 font-bold text-xs">✔ SELESAI</span>
                                                    <span class="text-[10px] text-gray-400 mb-1">{{ $order->prepWashingBy->name ?? 'System' }}</span>
                                                    
                                                    <div class="text-[10px] text-gray-500 bg-gray-50 rounded px-1.5 py-0.5 border border-gray-100 dark:border-gray-700">
                                                        @if($order->prep_washing_started_at)
                                                            <div class="flex justify-between gap-2"><span>Mulai:</span> <span>{{ $order->prep_washing_started_at->format('H:i') }}</span></div>
                                                            <div class="flex justify-between gap-2"><span>Selesai:</span> <span>{{ $order->prep_washing_completed_at->format('H:i') }}</span></div>
                                                            <div class="border-t border-gray-200 mt-0.5 pt-0.5 font-bold text-center text-teal-600">
                                                                ({{ $order->prep_washing_started_at->diffInMinutes($order->prep_washing_completed_at) }} mnt)
                                                            </div>
                                                        @else
                                                            <div>Selesai: {{ $order->prep_washing_completed_at->format('H:i') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @elseif($order->prep_washing_by)
                                                <div class="inline-flex flex-col items-center">
                                                    <span class="text-blue-500 font-bold text-xs">⚡ PROSES</span>
                                                    <span class="text-[10px] text-gray-500 mb-1">{{ $order->prepWashingBy->name ?? '...' }}</span>
                                                    @if($order->prep_washing_started_at)
                                                        <span class="text-[10px] text-gray-500 bg-blue-50 px-1 rounded">Mulai: {{ $order->prep_washing_started_at->format('H:i') }}</span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-gray-400 text-xs">Pending</span>
                                            @endif
                                        </td>

                                        {{-- Sol Status --}}
                                        <td class="px-4 py-3 text-center">
                                            @if(!$order->needs_sol)
                                                <span class="text-gray-300 text-xs">-</span>
                                            @elseif($order->prep_sol_completed_at)
                                                <div class="inline-flex flex-col items-center">
                                                    <span class="text-green-500 font-bold text-xs">✔ SELESAI</span>
                                                    <span class="text-[10px] text-gray-400 mb-1">{{ $order->prepSolBy->name ?? 'System' }}</span>
                                                    
                                                    <div class="text-[10px] text-gray-500 bg-gray-50 rounded px-1.5 py-0.5 border border-gray-100 dark:border-gray-700">
                                                        @if($order->prep_sol_started_at)
                                                            <div class="flex justify-between gap-2"><span>Mulai:</span> <span>{{ $order->prep_sol_started_at->format('H:i') }}</span></div>
                                                            <div class="flex justify-between gap-2"><span>Selesai:</span> <span>{{ $order->prep_sol_completed_at->format('H:i') }}</span></div>
                                                            <div class="border-t border-gray-200 mt-0.5 pt-0.5 font-bold text-center text-teal-600">
                                                                ({{ $order->prep_sol_started_at->diffInMinutes($order->prep_sol_completed_at) }} mnt)
                                                            </div>
                                                        @else
                                                            <div>Selesai: {{ $order->prep_sol_completed_at->format('H:i') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @elseif($order->prep_sol_by)
                                                 <div class="inline-flex flex-col items-center">
                                                    <span class="text-blue-500 font-bold text-xs">⚡ PROSES</span>
                                                    <span class="text-[10px] text-gray-500 mb-1">{{ $order->prepSolBy->name ?? '...' }}</span>
                                                    @if($order->prep_sol_started_at)
                                                        <span class="text-[10px] text-gray-500 bg-blue-50 px-1 rounded">Mulai: {{ $order->prep_sol_started_at->format('H:i') }}</span>
                                                    @endif
                                                </div>
                                            @elseif(!$order->prep_washing_completed_at)
                                                <span class="text-gray-400 font-bold text-[10px] italic">⏳ MENUNGGU CUCI</span>
                                            @else
                                                <span class="text-orange-500 font-bold text-xs">⚠️ MENUNGGU</span>
                                            @endif
                                        </td>

                                        {{-- Upper Status --}}
                                        <td class="px-4 py-3 text-center">
                                            @if(!$order->needs_upper)
                                                <span class="text-gray-300 text-xs">-</span>
                                            @elseif($order->prep_upper_completed_at)
                                                <div class="inline-flex flex-col items-center">
                                                    <span class="text-green-500 font-bold text-xs">✔ SELESAI</span>
                                                    <span class="text-[10px] text-gray-400 mb-1">{{ $order->prepUpperBy->name ?? 'System' }}</span>
                                                    
                                                    <div class="text-[10px] text-gray-500 bg-gray-50 rounded px-1.5 py-0.5 border border-gray-100 dark:border-gray-700">
                                                        @if($order->prep_upper_started_at)
                                                            <div class="flex justify-between gap-2"><span>Mulai:</span> <span>{{ $order->prep_upper_started_at->format('H:i') }}</span></div>
                                                            <div class="flex justify-between gap-2"><span>Selesai:</span> <span>{{ $order->prep_upper_completed_at->format('H:i') }}</span></div>
                                                            <div class="border-t border-gray-200 mt-0.5 pt-0.5 font-bold text-center text-teal-600">
                                                                ({{ $order->prep_upper_started_at->diffInMinutes($order->prep_upper_completed_at) }} mnt)
                                                            </div>
                                                        @else
                                                            <div>Selesai: {{ $order->prep_upper_completed_at->format('H:i') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @elseif($order->prep_upper_by)
                                                 <div class="inline-flex flex-col items-center">
                                                    <span class="text-blue-500 font-bold text-xs">⚡ PROSES</span>
                                                    <span class="text-[10px] text-gray-500 mb-1">{{ $order->prepUpperBy->name ?? '...' }}</span>
                                                    @if($order->prep_upper_started_at)
                                                        <span class="text-[10px] text-gray-500 bg-blue-50 px-1 rounded">Mulai: {{ $order->prep_upper_started_at->format('H:i') }}</span>
                                                    @endif
                                                </div>
                                            @elseif(!$order->prep_washing_completed_at)
                                                <span class="text-gray-400 font-bold text-[10px] italic">⏳ MENUNGGU CUCI</span>
                                            @else
                                                <span class="text-purple-500 font-bold text-xs">⚠️ MENUNGGU</span>
                                            @endif
                                        </td>

                                        {{-- Finish Button --}}
                                        <td class="px-4 py-3 text-right">
                                            @if($order->is_ready)
                                                <form action="{{ route('preparation.finish', $order->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs font-bold shadow transition-colors">
                                                        KIRIM KE SORTIR →
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-red-400 text-xs italic">Belum Lengkap</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Instructions --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex gap-4 items-start">
                <span class="text-2xl">💡</span>
                <div class="text-sm text-blue-800">
                    <strong>Panduan Stasiun:</strong>
                    <ul class="list-disc ml-4 mt-1 space-y-1">
                        <li>Gunakan tab <strong>Washing</strong>, <strong>Sol</strong>, dan <strong>Upper</strong> untuk melihat antrian spesifik.</li>
                        <li>Klik tombol <strong>"SELESAI"</strong> pada setiap baris untuk menandai bahwa tahapan tersebut sudah beres.</li>
                        <li>Sistem otomatis mencatat nama Anda sebagai teknisi yang mengerjakan.</li>
                        <li>Jika semua tahap selesai, tombol <strong>"Kirim ke Sortir"</strong> akan muncul di tab "Semua Order".</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

<script>
    function updateStation(id, type, action) {
        let techId = null;
        if (action === 'start') {
            const select = document.getElementById(`tech-${type}-${id}`);
            if (select) {
                techId = select.value;
                if (!techId) {
                    alert('Harap pilih teknisi terlebih dahulu!');
                    return;
                }
            }
        }

        const confirmMsg = action === 'start' 
            ? 'Tugaskan teknisi untuk cuci?' 
            : 'Tandai proses ini sebagai selesai?';

        if (!confirm(confirmMsg)) return;

        fetch(`/preparation/${id}/update-station`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ type: type, action: action, technician_id: techId }) // Sending Tech ID
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
                window.location.reload(); 
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan: ' + error.message);
        });
    }
</script>
