<div>
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100">
        <div class="bg-gradient-to-r from-teal-50 to-orange-50 px-6 py-5 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-orange-500 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-black text-gray-800 tracking-tight">SPK Matrix</h3>
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
                                        <div class="text-[13px] text-gray-700 leading-relaxed mb-4 pl-3 font-medium">Pemetaan detail proses SPK berdasarkan grup kerja (Persiapan, Reparasi, Post) untuk melihat posisi barang secara akurat.</div>
                                        
                                        <div class="flex items-center gap-2 mb-2">
                                            <div class="w-1 h-4 bg-gray-400 rounded-full"></div>
                                            <div class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Sumber Data</div>
                                        </div>
                                        <div class="text-[12px] text-gray-500 leading-relaxed italic pl-3">Status teknis dan tahapan operasional dari setiap barang dalam sistem produksi.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Distribusi proses per grup kerja</p>
                    </div>
                </div>
                <span class="px-3 py-1.5 bg-gradient-to-r from-teal-100 to-orange-100 text-gray-700 rounded-lg text-xs font-black">
                    Total: {{ $matrixData['total_spk'] ?? 0 }} SPK
                </span>
            </div>
        </div>
        <div class="p-6">
            @if(isset($matrixData['groups']))
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @php
                    $groupColors = [
                        'Persiapan' => ['bg' => 'from-violet-500 to-indigo-500', 'ring' => 'ring-violet-100', 'text' => 'text-violet-700', 'bgLight' => 'bg-violet-50', 'border' => 'border-violet-200'],
                        'Reparasi' => ['bg' => 'from-teal-500 to-emerald-500', 'ring' => 'ring-teal-100', 'text' => 'text-teal-700', 'bgLight' => 'bg-teal-50', 'border' => 'border-teal-200'],
                        'Post' => ['bg' => 'from-orange-500 to-amber-500', 'ring' => 'ring-orange-100', 'text' => 'text-orange-700', 'bgLight' => 'bg-orange-50', 'border' => 'border-orange-200'],
                    ];
                @endphp

                @foreach($matrixData['groups'] as $groupName => $group)
                @php $colors = $groupColors[$groupName] ?? $groupColors['Persiapan']; @endphp
                <div class="rounded-xl border {{ $colors['border'] }} {{ $colors['bgLight'] }} overflow-hidden">
                    {{-- Group Header --}}
                    <div class="bg-gradient-to-r {{ $colors['bg'] }} px-4 py-3 flex items-center justify-between">
                        <span class="text-white font-black text-sm">{{ $groupName }}</span>
                        <span class="bg-white/20 backdrop-blur-sm px-2.5 py-1 rounded-lg text-white text-xs font-black">
                            {{ $group['total'] }}
                        </span>
                    </div>
                    {{-- Sub-items --}}
                    <div class="p-3 space-y-1.5">
                        @foreach($group as $subName => $count)
                            @if($subName !== 'total')
                            <div class="flex items-center justify-between px-3 py-2 rounded-lg {{ $count > 0 ? 'bg-white shadow-sm' : '' }}">
                                <span class="text-xs font-bold {{ $count > 0 ? $colors['text'] : 'text-gray-400' }}">{{ $subName }}</span>
                                <span class="text-sm font-black {{ $count > 0 ? 'text-gray-800' : 'text-gray-300' }}">{{ $count }}</span>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <p class="text-gray-500 text-sm font-medium">Tidak ada data matrix</p>
            </div>
            @endif
        </div>
    </div>
</div>
