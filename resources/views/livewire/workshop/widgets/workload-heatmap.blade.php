<div>
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100">
        <div class="bg-gradient-to-r from-gray-50 to-teal-50 px-6 py-5 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-black text-gray-800 tracking-tight">Beban Kerja</h3>
                            {{-- Info Tooltip --}}
                            <div x-data="{ open: false }" class="relative inline-block text-left">
                                <button @mouseenter="open = true" @mouseleave="open = false" class="text-teal-300 hover:text-teal-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                                <div x-show="open" x-cloak x-transition class="absolute z-50 w-80 max-w-none p-5 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-teal-100 left-0 mt-3 whitespace-normal text-left">
                                    <div class="absolute -top-1.5 left-4 w-3 h-3 bg-white border-t border-l border-teal-100 rotate-45"></div>
                                    <div class="relative">
                                        <div class="flex items-center gap-2 mb-2">
                                            <div class="w-1 h-4 bg-teal-500 rounded-full"></div>
                                            <div class="text-[10px] font-black text-teal-600 uppercase tracking-widest">Maksud</div>
                                        </div>
                                        <div class="text-[13px] text-gray-700 leading-relaxed mb-4 pl-3 font-medium">Memantau antrean di setiap stasiun dan beban kerja masing-masing teknisi untuk mendeteksi hambatan (bottleneck).</div>
                                        
                                        <div class="flex items-center gap-2 mb-2">
                                            <div class="w-1 h-4 bg-gray-400 rounded-full"></div>
                                            <div class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Sumber Data</div>
                                        </div>
                                        <div class="text-[12px] text-gray-500 leading-relaxed italic pl-3">Penempatan teknisi pada SPK aktif dan jumlah antrean di setiap stasiun kerja.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Antrian stasiun & distribusi teknisi</p>
                    </div>
                </div>
                @if($bottleneckCount > 10)
                <span class="px-3 py-1.5 bg-red-100 text-red-700 rounded-lg text-xs font-bold animate-pulse">
                    ⚠️ Bottleneck: {{ $bottleneck }}
                </span>
                @else
                <span class="px-3 py-1.5 bg-green-100 text-green-700 rounded-lg text-xs font-bold animate-pulse">● Live</span>
                @endif
            </div>
        </div>
        <div class="relative p-6 space-y-6">
            {{-- Loading Overlay --}}
            <div wire:loading class="absolute inset-0 bg-white/50 backdrop-blur-[1px] flex items-center justify-center z-10 transition-all duration-300">
                <div class="flex flex-col items-center gap-2">
                    <div class="w-8 h-8 border-4 border-teal-500 border-t-transparent rounded-full animate-spin"></div>
                    <span class="text-xs font-bold text-teal-700 uppercase tracking-widest">Updating</span>
                </div>
            </div>
            {{-- Station Queue --}}
            <div>
                <h4 class="text-sm font-bold text-gray-600 mb-3 uppercase tracking-wider">Antrian Stasiun</h4>
                <div class="space-y-3">
                    @php
                        $colors = [
                            'Assessment' => 'from-indigo-500 to-indigo-400',
                            'Preparation' => 'from-violet-500 to-violet-400',
                            'Sortir' => 'from-amber-500 to-amber-400',
                            'Production' => 'from-teal-500 to-teal-400',
                            'QC' => 'from-orange-500 to-orange-400',
                        ];
                        $routes = [
                            'Assessment' => route('assessment.index'),
                            'Preparation' => route('preparation.index'),
                            'Sortir' => route('sortir.index'),
                            'Production' => route('production.index'),
                            'QC' => route('qc.index'),
                        ];
                        $maxCount = max(max(array_values($stationData)), 1);
                    @endphp
                    @foreach($stationData as $station => $count)
                    <a href="{{ $routes[$station] }}" class="flex items-center gap-3 group hover:bg-gray-50 rounded-xl p-2 -m-2 transition-all">
                        <div class="w-24 text-sm font-bold text-gray-700 group-hover:text-teal-600 transition-colors">{{ $station }}</div>
                        <div class="flex-1 bg-gray-100 rounded-full h-6 overflow-hidden">
                            <div class="h-6 rounded-full bg-gradient-to-r {{ $colors[$station] }} flex items-center justify-end pr-2 transition-all duration-700"
                                 style="width: {{ min(($count / 30) * 100, 100) }}%">
                                @if($count > 0)
                                <span class="text-xs font-black text-white drop-shadow">{{ $count }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="w-10 text-right text-sm font-black {{ $count > 15 ? 'text-red-600' : ($count > 8 ? 'text-amber-600' : 'text-gray-500') }}">
                            {{ $count }}
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Technician Load --}}
            @if($technicianLoad->count() > 0)
            <div class="border-t border-gray-100 pt-5">
                <h4 class="text-sm font-bold text-gray-600 mb-3 uppercase tracking-wider">Beban Kerja Teknisi</h4>
                <div class="space-y-2">
                    @foreach($technicianLoad as $tech)
                    <div class="flex items-center gap-3">
                        <div class="w-24 text-xs font-bold text-gray-600 truncate">{{ $tech['name'] }}</div>
                        <div class="flex-1 bg-gray-100 rounded-full h-4 overflow-hidden">
                            <div class="h-4 rounded-full bg-gradient-to-r from-teal-500 to-orange-400 transition-all duration-500"
                                 style="width: {{ min(($tech['count'] / 10) * 100, 100) }}%"></div>
                        </div>
                        <div class="w-8 text-right text-xs font-black {{ $tech['count'] > 5 ? 'text-red-600' : 'text-gray-500' }}">{{ $tech['count'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
