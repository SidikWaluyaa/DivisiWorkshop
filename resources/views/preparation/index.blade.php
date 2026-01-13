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
                            <div class="text-xs font-bold text-gray-500 uppercase">Antrian Proses Cuci</div>
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
                            <div class="text-xs font-bold text-gray-500 uppercase">Antrian Bongkar Sol</div>
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
                            <div class="text-xs font-bold text-gray-500 uppercase">Antrian Bongkar Upper</div>
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
                                <div x-data="{ showPhotos: false, showFinishModal: false, finishDate: '{{ now()->format('Y-m-d\TH:i') }}' }" class="border-b border-gray-100 last:border-0 group">
                                    <div class="p-4 hover:bg-gray-50 transition-colors flex items-center justify-between">
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

                                            {{-- Photo Toggle Button --}}
                                            <button @click="showPhotos = !showPhotos" :class="showPhotos ? 'text-teal-600 bg-teal-50' : 'text-gray-400 hover:text-teal-600'" class="p-2 rounded-lg transition-colors" title="Dokumentasi Foto">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            </button>

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
                                                    <button type="button" @click="showFinishModal = true" class="flex items-center gap-2 px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white rounded text-xs font-bold uppercase tracking-wide transition-all shadow hover:shadow-md">
                                                        <span>✔ Selesai</span>
                                                    </button>
                                                    <div x-show="showFinishModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display: none;" x-transition>
                                                        <div class="bg-white rounded-lg shadow-xl p-4 w-80" @click.away="showFinishModal = false">
                                                            <h3 class="font-bold text-gray-800 mb-2">Konfirmasi Selesai</h3>
                                                            <p class="text-xs text-gray-600 mb-3">Masukkan tanggal & jam selesai aktual:</p>
                                                            <input type="datetime-local" x-model="finishDate" class="w-full text-sm border-gray-300 rounded mb-4 focus:ring-green-500 focus:border-green-500">
                                                            <div class="flex justify-end gap-2">
                                                                <button @click="showFinishModal = false" class="px-3 py-1.5 bg-gray-200 text-gray-700 rounded text-xs font-bold">Batal</button>
                                                                <button @click="updateStation({{ $order->id }}, 'washing', 'finish', finishDate)" class="px-3 py-1.5 bg-green-600 text-white rounded text-xs font-bold">Simpan & Selesai</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Photo Section -->
                                    <div x-show="showPhotos" class="px-4 pb-4 bg-gray-50/50" style="display: none;" x-transition>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-2">Before (Awal)</span>
                                                <x-photo-uploader :order="$order" step="PREP_WASHING_BEFORE" />
                                            </div>
                                            <div>
                                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-2">After (Akhir)</span>
                                                <x-photo-uploader :order="$order" step="PREP_WASHING_AFTER" />
                                            </div>
                                        </div>
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

                {{-- ADMIN REVIEW (Menunggu Persetujuan) --}}
                @if($queueReview->isNotEmpty())
                <div class="mb-6 bg-white rounded-xl shadow-lg border-2 border-orange-400 overflow-hidden" x-show="activeTab === 'all' || activeTab.includes('review')">
                     <div class="bg-gradient-to-r from-orange-500 to-red-500 p-4 text-white flex justify-between items-center">
                        <h3 class="font-bold flex items-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Menunggu Pemeriksaan Admin (Preparation Selesai)
                        </h3>
                        <span class="bg-white/20 px-3 py-1 rounded-full text-xs font-bold">{{ $queueReview->count() }} Order</span>
                    </div>
                    
                    <div class="overflow-x-auto -mx-4 sm:mx-0">
                        <table class="min-w-full w-full text-sm text-left">
                            <thead class="bg-gray-50 uppercase text-xs font-bold text-gray-600">
                                <tr>
                                    <th class="px-6 py-3">SPK</th>
                                    <th class="px-6 py-3">Item</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3 text-center">Aksi (Admin)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($queueReview as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 font-bold font-mono">{{ $order->spk_number }}</td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold">{{ $order->shoe_brand }}</div>
                                        <div class="text-xs text-gray-500">{{ $order->customer_name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-1 text-xs">
                                            <span class="text-green-600 flex items-center gap-1">✔ Washing: {{ $order->prepWashingBy->name ?? 'System' }}</span>
                                            @if($order->needs_sol)
                                                <span class="text-green-600 flex items-center gap-1">✔ Sol: {{ $order->prepSolBy->name ?? 'System' }}</span>
                                            @endif
                                            @if($order->needs_upper)
                                                <span class="text-green-600 flex items-center gap-1">✔ Upper: {{ $order->prepUpperBy->name ?? 'System' }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center items-center gap-2">
                                            <!-- Approve -->
                                            <form action="{{ route('preparation.approve', $order->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-bold text-xs flex items-center gap-1 shadow hover:shadow-lg transition-all" onclick="return confirm('Preparation OK? Lanjut Sortir?')">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    Approve & Sortir
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
                                                    <div class="bg-white rounded-xl shadow-2xl p-6 w-72 max-w-full text-left" @click.away="openRevisi = false">
                                                        <h3 class="font-bold text-lg text-gray-800 mb-4 border-b pb-2">Revisi Preparation</h3>
                                                        <p class="text-xs text-gray-500 mb-3">Pilih bagian yang perlu direvisi:</p>

                                                        <form action="{{ route('preparation.reject', $order->id) }}" method="POST" class="space-y-3">
                                                            @csrf
                                                            
                                                            <div>
                                                                <label class="flex items-center gap-2 mb-1 cursor-pointer">
                                                                    <input type="radio" name="target_station" value="washing" class="text-red-600" required>
                                                                    <span class="font-bold text-sm text-gray-700">Washing</span>
                                                                </label>
                                                            </div>

                                                            @if($order->needs_sol)
                                                            <div>
                                                                <label class="flex items-center gap-2 mb-1 cursor-pointer">
                                                                    <input type="radio" name="target_station" value="sol" class="text-red-600" required>
                                                                    <span class="font-bold text-sm text-gray-700">Bongkar Sol</span>
                                                                </label>
                                                            </div>
                                                            @endif

                                                            @if($order->needs_upper)
                                                            <div>
                                                                <label class="flex items-center gap-2 mb-1 cursor-pointer">
                                                                    <input type="radio" name="target_station" value="upper" class="text-red-600" required>
                                                                    <span class="font-bold text-sm text-gray-700">Bongkar Upper</span>
                                                                </label>
                                                            </div>
                                                            @endif
                                                            
                                                            <textarea name="reason" rows="2" class="w-full text-sm border-gray-300 rounded focus:border-red-500 focus:ring-red-500" placeholder="Alasan revisi..." required></textarea>

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

                {{-- All Orders / Progress View --}}
                <div x-show="activeTab === 'all'" x-transition>
                    <div class="p-4 border-b border-gray-100 bg-blue-50 flex justify-between items-center">
                        <h3 class="font-bold text-blue-800">📋 Semua Order di Preparation</h3>
                    </div>
                    <div class="overflow-x-auto -mx-4 sm:mx-0">
                        <table class="min-w-full w-full text-sm text-left">
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
                                            <div class="font-bold text-gray-800 flex items-center gap-2">
                                                {{ $order->shoe_brand }}
                                                @if($order->is_revising)
                                                    <span class="bg-red-100 text-red-600 text-[10px] font-bold px-2 py-0.5 rounded-full border border-red-200 animate-pulse">
                                                        REVISI
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $order->customer_name }}</div>
                                        </td>
                                        
                                        {{-- Washing Status --}}
                                        <td class="px-4 py-3 text-center">
                                            @if($order->prep_washing_completed_at)
                                                <div class="inline-flex flex-col items-center">
                                                    <span class="text-green-500 font-bold text-xs">✔ SELESAI</span>
                                                    <span class="text-[10px] text-gray-400 mb-1">{{ $order->prepWashingBy->name ?? 'System' }}</span>
                                                </div>
                                            @elseif($order->prep_washing_by)
                                                <div class="inline-flex flex-col items-center">
                                                    <span class="text-blue-500 font-bold text-xs">⚡ PROSES</span>
                                                    <span class="text-[10px] text-gray-500 mb-1">{{ $order->prepWashingBy->name ?? '...' }}</span>
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
                                                </div>
                                            @elseif($order->prep_sol_by)
                                                 <div class="inline-flex flex-col items-center">
                                                    <span class="text-blue-500 font-bold text-xs">⚡ PROSES</span>
                                                    <span class="text-[10px] text-gray-500 mb-1">{{ $order->prepSolBy->name ?? '...' }}</span>
                                                </div>
                                            @else
                                                <span class="text-gray-300 text-xs">Pending</span>
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
                                                </div>
                                            @elseif($order->prep_upper_by)
                                                 <div class="inline-flex flex-col items-center">
                                                    <span class="text-blue-500 font-bold text-xs">⚡ PROSES</span>
                                                    <span class="text-[10px] text-gray-500 mb-1">{{ $order->prepUpperBy->name ?? '...' }}</span>
                                                </div>
                                            @else
                                                <span class="text-gray-300 text-xs">Pending</span>
                                            @endif
                                        </td>

                                        {{-- Finish Button (Only shown if NOT in review queue loop, but basically redundant if using main review, optional here) --}}
                                        <td class="px-4 py-3 text-right">
                                            @if($order->is_ready)
                                                <div class="flex flex-col items-end gap-1">
                                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-bold border border-yellow-200 animate-pulse">
                                                        ⏳ Menunggu Approval
                                                    </span>
                                                    <span class="text-[10px] text-gray-400">Lihat bagian atas</span>
                                                </div>
                                            @else
                                                <span class="text-gray-400 text-xs italic">Proses...</span>
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
    function updateStation(id, type, action = 'finish', finishedAt = null) {
        
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
        if (action === 'start' && !confirm('Mulai proses ini?')) return;

        fetch(`/preparation/${id}/update-station`, {
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
