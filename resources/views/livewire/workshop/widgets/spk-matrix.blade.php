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
                            <h3 class="text-lg font-black text-gray-800 tracking-tight">SPK Matrix Intelligence</h3>
                            <div x-data="{ open: false }" class="relative inline-block text-left">
                                <button @click.stop="open = !open" class="text-teal-300 hover:text-teal-600 transition-colors cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                                <div x-show="open" x-cloak x-transition @click.away="open = false" class="absolute z-50 w-80 max-w-none p-5 bg-white rounded-2xl shadow-2xl border border-gray-100 left-0 mt-3 whitespace-normal text-left">
                                    <div class="absolute -top-1.5 left-4 w-3 h-3 bg-white border-t border-l border-gray-100 rotate-45"></div>
                                    <div class="relative">
                                        <div class="flex items-center gap-2 mb-2">
                                            <div class="w-1 h-4 bg-red-500 rounded-full"></div>
                                            <div class="text-[10px] font-black text-red-600 uppercase tracking-widest">Bottleneck Warning</div>
                                        </div>
                                        <div class="text-[13px] text-gray-700 leading-relaxed mb-4 pl-3 font-medium">Sistem secara otomatis mendeteksi tahap yang paling lama menahan SPK (Avg Hours) dan menandainya sebagai titik hambatan.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Alur kerja harian & deteksi hambatan</p>
                    </div>
                </div>
                <span class="px-3 py-1.5 bg-gradient-to-r from-teal-100 to-orange-100 text-gray-700 rounded-lg text-xs font-black">
                    Total: {{ $matrixData['total_spk'] ?? 0 }} SPK
                </span>
            </div>
        </div>
        <div class="p-6">
            @if(isset($matrixData['groups']))
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @php
                    $groupColors = [
                        'Persiapan' => ['bg' => 'from-violet-500 to-indigo-500', 'ring' => 'ring-violet-100', 'text' => 'text-violet-700', 'bgLight' => 'bg-violet-50', 'border' => 'border-violet-200'],
                        'Sortir' => ['bg' => 'from-rose-500 to-pink-500', 'ring' => 'ring-rose-100', 'text' => 'text-rose-700', 'bgLight' => 'bg-rose-50', 'border' => 'border-rose-200'],
                        'Produksi' => ['bg' => 'from-teal-500 to-emerald-500', 'ring' => 'ring-teal-100', 'text' => 'text-teal-700', 'bgLight' => 'bg-teal-50', 'border' => 'border-teal-200'],
                        'Post' => ['bg' => 'from-orange-500 to-amber-500', 'ring' => 'ring-orange-100', 'text' => 'text-orange-700', 'bgLight' => 'bg-orange-50', 'border' => 'border-orange-200'],
                    ];
                @endphp

                @foreach($matrixData['groups'] as $groupName => $group)
                @php $colors = $groupColors[$groupName] ?? $groupColors['Persiapan']; @endphp
                <div class="rounded-xl border {{ $colors['border'] }} {{ $colors['bgLight'] }} overflow-hidden flex flex-col h-full">
                    {{-- Group Header --}}
                    <div class="bg-gradient-to-r {{ $colors['bg'] }} px-4 py-3 flex items-center justify-between">
                        <span class="text-white font-black text-sm tracking-tight uppercase">{{ $groupName }}</span>
                        <div class="flex items-center gap-1.5">
                            <span class="bg-white/20 backdrop-blur-sm px-2.5 py-1 rounded-lg text-white text-[10px] font-black">
                                {{ $group['total'] }} SPK
                            </span>
                        </div>
                    </div>
                    {{-- Sub-items --}}
                    <div class="p-3 space-y-2 flex-grow">
                        @foreach($group as $subName => $data)
                            @if(!in_array($subName, ['total', 'bottleneck']))
                            @php 
                                $isBottleneck = ($subName === $group['bottleneck'] && $data['count'] > 0);
                            @endphp
                            <div class="flex flex-col px-3 py-2.5 rounded-xl transition-all duration-300 {{ $isBottleneck ? 'bg-red-50 border-2 border-red-200 shadow-sm animate-pulse' : ($data['count'] > 0 ? 'bg-white shadow-sm' : 'bg-transparent opacity-60') }}">
                                <div class="flex items-center justify-between mb-0.5">
                                    <span class="text-[11px] font-bold {{ $isBottleneck ? 'text-red-700' : ($data['count'] > 0 ? $colors['text'] : 'text-gray-400') }}">
                                        {{ $subName }}
                                        @if($isBottleneck)
                                            <span class="ml-1 text-[9px] bg-red-600 text-white px-1.5 py-0.5 rounded-full uppercase italic">Bottleneck</span>
                                        @endif
                                    </span>
                                    <span class="text-xs font-black {{ $isBottleneck ? 'text-red-800' : ($data['count'] > 0 ? 'text-gray-800' : 'text-gray-300') }}">
                                        {{ $data['count'] }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] text-gray-400 font-medium">Avg. Wait Time</span>
                                    <span class="text-[10px] {{ $isBottleneck ? 'text-red-600 font-bold' : 'text-gray-500' }}">
                                        {{ $data['avg_hours'] }}h
                                    </span>
                                </div>
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
