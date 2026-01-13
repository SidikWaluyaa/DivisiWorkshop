<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-2 bg-gradient-to-r from-teal-600 to-emerald-600 rounded-lg shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            
            <div class="flex flex-col">
                <h2 class="font-bold text-xl leading-tight tracking-wide text-gray-800">
                    {{ __('Stasiun Quality Control') }}
                </h2>
                <div class="text-xs font-bold text-teal-600">
                   Inspeksi & Verifikasi
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50/50 min-h-screen" x-data="{ activeTab: 'jahit' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Tabs Navigation --}}
            <div class="flex space-x-1 mb-6 bg-white p-1 rounded-xl shadow-sm border border-gray-100 overflow-x-auto scrollbar-hide">
                {{-- Jahit Check Tab --}}
                <button @click="activeTab = 'jahit'" 
                    :class="{ 'bg-blue-50 text-blue-700 shadow-sm border-blue-200': activeTab === 'jahit', 'text-gray-500 hover:bg-gray-50': activeTab !== 'jahit' }"
                    class="flex-1 py-2.5 px-4 rounded-lg text-sm font-bold transition-all flex items-center justify-center gap-2 border border-transparent">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    ANTRIAN QC JAHIT
                    <span class="ml-2 px-1.5 py-0.5 bg-blue-200 text-blue-800 rounded-full text-[10px]">
                        {{ $queues['jahit']->count() }}
                    </span>
                </button>

                {{-- Cleanup Check Tab --}}
                <button @click="activeTab = 'cleanup'" 
                    :class="{ 'bg-teal-50 text-teal-700 shadow-sm border-teal-200': activeTab === 'cleanup', 'text-gray-500 hover:bg-gray-50': activeTab !== 'cleanup' }"
                    class="flex-1 py-2.5 px-4 rounded-lg text-sm font-bold transition-all flex items-center justify-center gap-2 border border-transparent">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 15.536c-1.171 1.952-3.07 1.952-4.242 0-1.172-1.953-1.172-5.119 0-7.072 1.171-1.952 3.07-1.952 4.242 0M8 10.5h4m-4 3h4m9-1.5a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    ANTRIAN QC CLEANUP
                    <span class="ml-2 px-1.5 py-0.5 bg-teal-200 text-teal-800 rounded-full text-[10px]">
                        {{ $queues['cleanup']->count() }}
                    </span>
                </button>

                {{-- Final Check Tab --}}
                <button @click="activeTab = 'final'" 
                    :class="{ 'bg-emerald-50 text-emerald-700 shadow-sm border-emerald-200': activeTab === 'final', 'text-gray-500 hover:bg-gray-50': activeTab !== 'final' }"
                    class="flex-1 py-2.5 px-4 rounded-lg text-sm font-bold transition-all flex items-center justify-center gap-2 border border-transparent">
                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    ANTRIAN QC FINAL
                    <span class="ml-2 px-1.5 py-0.5 bg-emerald-200 text-emerald-800 rounded-full text-[10px]">
                        {{ $queues['final']->count() }}
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

            {{-- JAHIT Content --}}
            <div x-show="activeTab === 'jahit'" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" style="display: none;">
                <div class="p-4 bg-blue-50 border-b border-blue-100 flex justify-between items-center">
                    <h3 class="font-bold text-blue-800 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span> Antrian QC Jahit
                    </h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($queues['jahit'] as $order)
                         <x-station-card 
                            :order="$order" 
                            type="qc_jahit" 
                            :technicians="$techs['jahit']"
                            techByRelation="qcJahitBy"
                            startedAtColumn="qc_jahit_started_at"
                            byColumn="qc_jahit_by"
                            color="blue"
                            titleAction="Inspect"
                        />
                    @empty
                        <div class="p-8 text-center text-gray-400">Tidak ada antrian QC Jahit.</div>
                    @endforelse
                </div>
            </div>

            {{-- CLEANUP Content --}}
            <div x-show="activeTab === 'cleanup'" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" style="display: none;">
                <div class="p-4 bg-teal-50 border-b border-teal-100 flex justify-between items-center">
                    <h3 class="font-bold text-teal-800 flex items-center gap-2">
                         <span class="w-2 h-2 rounded-full bg-teal-500"></span> Antrian QC Cleanup
                    </h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($queues['cleanup'] as $order)
                         <x-station-card 
                            :order="$order" 
                            type="qc_cleanup" 
                            :technicians="$techs['cleanup']"
                            techByRelation="qcCleanupBy"
                            startedAtColumn="qc_cleanup_started_at"
                            byColumn="qc_cleanup_by"
                            color="teal"
                            titleAction="Periksa"
                        />
                    @empty
                         <div class="p-8 text-center text-gray-400">Tidak ada antrian QC Cleanup.</div>
                    @endforelse
                </div>
            </div>

            {{-- FINAL Content --}}
            <div x-show="activeTab === 'final'" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" style="display: none;">
                <div class="p-4 bg-emerald-50 border-b border-emerald-100 flex justify-between items-center">
                    <h3 class="font-bold text-emerald-800 flex items-center gap-2">
                         <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Antrian QC Final
                    </h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($queues['final'] as $order)
                         <x-station-card 
                            :order="$order" 
                            type="qc_final" 
                            :technicians="$techs['final']"
                            techByRelation="qcFinalBy"
                            startedAtColumn="qc_final_started_at"
                            byColumn="qc_final_by"
                            color="emerald"
                            titleAction="Verifikasi"
                        />
                    @empty
                         <div class="p-8 text-center text-gray-400">Tidak ada antrian QC Final.</div>
                    @endforelse
                </div>
            </div>

            {{-- ADMIN REVIEW SECTION --}}
            @if($queueReview->isNotEmpty())
            <div class="mt-8 mb-8 bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden border-2 border-emerald-400" x-show="activeTab === 'all' || activeTab.includes('final')">
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
                                <th class="px-6 py-3">SPK</th>
                                <th class="px-6 py-3">Item</th>
                                <th class="px-6 py-3">Status Pengerjaan (QC Tech)</th>
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
                                        @if($order->qc_jahit_completed_at) 
                                            <span class="text-xs text-green-600 flex items-center gap-1">✔ Jahit: {{ $order->qcJahitBy->name ?? 'System' }}</span> 
                                        @endif
                                        @if($order->qc_cleanup_completed_at) 
                                            <span class="text-xs text-green-600 flex items-center gap-1">✔ Cleanup: {{ $order->qcCleanupBy->name ?? 'System' }}</span> 
                                        @endif
                                        @if($order->qc_final_completed_at) 
                                            <span class="text-xs text-green-600 flex items-center gap-1">✔ Final: {{ $order->qcFinalBy->name ?? 'System' }}</span> 
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
                                        
                                        <!-- Reject (To Production) -->
                                        <div x-data="{ openRevisi: false }">
                                            <button @click="openRevisi = true" type="button" class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-2 rounded-lg font-bold text-xs flex items-center gap-1 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                Tolak (Revisi QC)
                                            </button>
                                            
                                            <!-- Fixed Modal -->
                                            <div x-show="openRevisi" class="fixed inset-0 z-[999] flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display: none;" x-transition>
                                                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 w-80 max-w-full text-left" @click.away="openRevisi = false">
                                                    <h3 class="font-bold text-lg text-gray-800 dark:text-gray-200 mb-4 border-b dark:border-gray-700 pb-2">Kembalikan ke QC Tech</h3>
                                                    <p class="text-xs text-gray-500 mb-3">Pilih proses QC yang perlu diulang:</p>

                                                    <form action="{{ route('qc.reject', $order->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                                        @csrf
                                                        
                                                        <label class="flex items-center gap-2 p-2 rounded border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                                            <input type="checkbox" name="rejected_stations[]" value="qc_jahit" class="rounded text-red-600 focus:ring-red-500">
                                                            <div>
                                                                <span class="block text-sm font-bold text-gray-800 dark:text-gray-200">QC Jahit</span>
                                                            </div>
                                                        </label>

                                                        <label class="flex items-center gap-2 p-2 rounded border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                                            <input type="checkbox" name="rejected_stations[]" value="qc_cleanup" class="rounded text-red-600 focus:ring-red-500">
                                                            <div>
                                                                <span class="block text-sm font-bold text-gray-800 dark:text-gray-200">QC Cleanup</span>
                                                            </div>
                                                        </label>

                                                        <label class="flex items-center gap-2 p-2 rounded border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                                            <input type="checkbox" name="rejected_stations[]" value="qc_final" class="rounded text-red-600 focus:ring-red-500">
                                                            <div>
                                                                <span class="block text-sm font-bold text-gray-800 dark:text-gray-200">QC Final</span>
                                                            </div>
                                                        </label>
                                                        
                                                        <textarea name="notes" rows="2" class="w-full text-sm border-gray-300 dark:bg-gray-900 dark:border-gray-600 dark:text-white rounded focus:border-red-500 focus:ring-red-500" placeholder="Alasan revisi..." required></textarea>
                                                        
                                                        <div class="flex justify-end gap-2 mt-4">
                                                            <button type="button" @click="openRevisi = false" class="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition-colors">Batal</button>
                                                            <button type="submit" class="px-3 py-2 bg-red-600 text-white rounded-lg text-sm font-bold hover:bg-red-700 shadow transition-colors">Kirim Revisi</button>
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
                                <th class="px-6 py-3">Jahit Check</th>
                                <th class="px-6 py-3">Cleanup Check</th>
                                <th class="px-6 py-3">Final Check</th>
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
                                                SEDANG REVISI
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-400">{{ $order->shoe_brand }} - {{ $order->shoe_color }}</div>
                                </td>
                                
                                {{-- Jahit Status --}}
                                <td class="px-6 py-4">
                                    @php
                                        $needsJahit = $order->services->contains(fn($s) => 
                                            stripos($s->category, 'sol') !== false || 
                                            stripos($s->category, 'upper') !== false || 
                                            stripos($s->category, 'repaint') !== false
                                        );
                                    @endphp

                                    @if(!$needsJahit)
                                        <span class="text-gray-300 text-xs italic">Tidak Perlu</span>
                                    @elseif($order->qc_jahit_completed_at)
                                        <div class="inline-flex flex-col items-center">
                                            <span class="text-green-600 font-bold text-xs">✔ OK</span>
                                            <span class="text-[10px] text-gray-400 mb-1">{{ $order->qcJahitBy->name ?? 'System' }}</span>
                                            
                                            <div class="text-[10px] text-gray-500 bg-gray-50 rounded px-1.5 py-0.5 border border-gray-100 dark:border-gray-700">
                                                @if($order->qc_jahit_started_at)
                                                    <div class="flex justify-between gap-2"><span>Mulai:</span> <span>{{ $order->qc_jahit_started_at->format('H:i') }}</span></div>
                                                    <div class="flex justify-between gap-2"><span>Selesai:</span> <span>{{ $order->qc_jahit_completed_at->format('H:i') }}</span></div>
                                                    <div class="border-t border-gray-200 mt-0.5 pt-0.5 font-bold text-center text-teal-600">
                                                        ({{ $order->qc_jahit_started_at->diffInMinutes($order->qc_jahit_completed_at) }} mnt)
                                                    </div>
                                                @else
                                                    <div>Selesai: {{ $order->qc_jahit_completed_at->format('H:i') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    @elseif($order->qc_jahit_started_at)
                                        <div class="inline-flex flex-col items-center">
                                            <div class="text-blue-600 font-bold text-xs">Pemeriksaan</div>
                                            <div class="text-[10px] text-gray-400 mb-1">{{ $order->qcJahitBy->name ?? '-' }}</div>
                                            <span class="text-[10px] text-gray-500 bg-blue-50 px-1 rounded">Mulai: {{ $order->qc_jahit_started_at->format('H:i') }}</span>
                                        </div>
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-500 rounded text-[10px] text-center block w-fit">Antrian</span>
                                    @endif
                                </td>

                                {{-- Cleanup Status --}}
                                <td class="px-6 py-4">
                                    @if($order->qc_cleanup_completed_at)
                                        <div class="inline-flex flex-col items-center">
                                            <div class="text-green-600 font-bold text-xs">✔ OK</div>
                                            <div class="text-[10px] text-gray-400 mb-1">{{ $order->qcCleanupBy->name ?? 'System' }}</div>

                                            <div class="text-[10px] text-gray-500 bg-gray-50 rounded px-1.5 py-0.5 border border-gray-100 dark:border-gray-700">
                                                @if($order->qc_cleanup_started_at)
                                                    <div class="flex justify-between gap-2"><span>Mulai:</span> <span>{{ $order->qc_cleanup_started_at->format('H:i') }}</span></div>
                                                    <div class="flex justify-between gap-2"><span>Selesai:</span> <span>{{ $order->qc_cleanup_completed_at->format('H:i') }}</span></div>
                                                    <div class="border-t border-gray-200 mt-0.5 pt-0.5 font-bold text-center text-teal-600">
                                                        ({{ $order->qc_cleanup_started_at->diffInMinutes($order->qc_cleanup_completed_at) }} mnt)
                                                    </div>
                                                @else
                                                    <div>Selesai: {{ $order->qc_cleanup_completed_at->format('H:i') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    @elseif($order->qc_cleanup_started_at)
                                        <div class="inline-flex flex-col items-center">
                                            <div class="text-teal-600 font-bold text-xs">Pemeriksaan</div>
                                            <div class="text-[10px] text-gray-400 mb-1">{{ $order->qcCleanupBy->name ?? '-' }}</div>
                                            <span class="text-[10px] text-gray-500 bg-teal-50 px-1 rounded">Mulai: {{ $order->qc_cleanup_started_at->format('H:i') }}</span>
                                        </div>
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-500 rounded text-[10px] text-center block w-fit">Antrian</span>
                                    @endif
                                </td>

                                {{-- Final Status --}}
                                <td class="px-6 py-4">
                                    @if($order->qc_final_completed_at)
                                        <div class="inline-flex flex-col items-center">
                                            <div class="text-green-600 font-bold text-xs">✔ OK</div>
                                            <div class="text-[10px] text-gray-400 mb-1">{{ $order->qcFinalBy->name ?? 'System' }}</div>

                                            <div class="text-[10px] text-gray-500 bg-gray-50 rounded px-1.5 py-0.5 border border-gray-100 dark:border-gray-700">
                                                @if($order->qc_final_started_at)
                                                    <div class="flex justify-between gap-2"><span>Mulai:</span> <span>{{ $order->qc_final_started_at->format('H:i') }}</span></div>
                                                    <div class="flex justify-between gap-2"><span>Selesai:</span> <span>{{ $order->qc_final_completed_at->format('H:i') }}</span></div>
                                                    <div class="border-t border-gray-200 mt-0.5 pt-0.5 font-bold text-center text-teal-600">
                                                        ({{ $order->qc_final_started_at->diffInMinutes($order->qc_final_completed_at) }} mnt)
                                                    </div>
                                                @else
                                                    <div>Selesai: {{ $order->qc_final_completed_at->format('H:i') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    @elseif($order->qc_final_started_at)
                                        <div class="inline-flex flex-col items-center">
                                            <div class="text-emerald-600 font-bold text-xs">Pemeriksaan</div>
                                            <div class="text-[10px] text-gray-400 mb-1">{{ $order->qcFinalBy->name ?? '-' }}</div>
                                            <span class="text-[10px] text-gray-500 bg-emerald-50 px-1 rounded">Mulai: {{ $order->qc_final_started_at->format('H:i') }}</span>
                                        </div>
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-500 rounded text-[10px] text-center block w-fit">Antrian</span>
                                    @endif
                                </td>

                                {{-- Action --}}
                                <td class="px-6 py-4 text-right">
                                    @php
                                        // 1. Determine requirements
                                        // Assume 'Jahit' is required if the order has Sol/Upper/Repaint services (QCController logic).
                                        // But here we can simply check if timestamps are filled or if defaults allow.
                                        
                                        // Simplified check: Rely on Controller logic or check timestamps.
                                        // We know: 
                                        // - Markup for Jahit only shows if needed.
                                        // - Cleanup & Final are mandatory.

                                        // Let's check completion:
                                        // Need Jahit? (If 'Jahit Check' column shows 'Not Required', skip)
                                        // For now, robust check:
                                        $needsJahit = $order->services->contains(fn($s) => 
                                            stripos($s->category, 'sol') !== false || 
                                            stripos($s->category, 'upper') !== false ||
                                            stripos($s->category, 'repaint') !== false
                                        );
                                        
                                        $jahitOk = !$needsJahit || $order->qc_jahit_completed_at;
                                        $cleanupOk = $order->qc_cleanup_completed_at;
                                        $finalOk = $order->qc_final_completed_at;
                                        
                                        $allQcReady = $jahitOk && $cleanupOk && $finalOk;
                                    @endphp

                                    @if($allQcReady)
                                        <div class="flex flex-col items-end gap-1">
                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-bold border border-yellow-200 animate-pulse">
                                                ⏳ Menunggu Approval
                                            </span>
                                            <span class="text-[10px] text-gray-400">Lihat bagian atas</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-orange-400 italic">Proses QC Belum Lengkap</span>
                                    @endif

                                </td>
                            </tr>
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
        if (action === 'start' && !confirm('Mulai proses ini?')) return;

        fetch(`/qc/${id}/update-station`, {
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
