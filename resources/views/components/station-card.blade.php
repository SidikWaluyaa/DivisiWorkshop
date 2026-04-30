@props([
    'order', 
    'type', // e.g. 'prod_sol', 'qc_jahit', 'prep_washing'
    'technicians',
    'titleAction' => 'Assign',
    'techByRelation' => null, 
    'startedAtColumn' => null,
    'byColumn' => null, 
    'color' => 'blue', 
    'showCheckbox' => true,
    'loopIteration' => null
])

@php
    // Auto-detect columns if not provided
    $startedAtColumn = $startedAtColumn ?? "{$type}_started_at";
    $byColumn = $byColumn ?? "{$type}_by";

    if (!$techByRelation && $type !== 'prep_review') {
        $parts = explode('_', $type);
        $camel = '';
        foreach($parts as $p) $camel .= ucfirst($p);
        $techByRelation = lcfirst($camel) . 'By';
    }

    $isPrepReview = ($type === 'prep_review');

    // Dynamic color classes based on station type
    $stationColor = 'blue';
    if (strpos($type, 'washing') !== false) $stationColor = 'teal';
    if (strpos($type, 'sol') !== false) $stationColor = 'orange';
    if (strpos($type, 'upper') !== false) $stationColor = 'purple';
    if (strpos($type, 'qc') !== false) $stationColor = 'emerald';
@endphp

<div id="spk-{{ $order->spk_number }}" 
     x-data="{ 
         showPhotos: false, 
         showFinishModal: false, 
         finishDate: '{{ now()->format('Y-m-d\TH:i') }}',
         isHighlighted: false,
         init() {
             const urlParams = new URLSearchParams(window.location.search);
             if (urlParams.get('highlight') === '{{ $order->spk_number }}') {
                 this.isHighlighted = true;
                 setTimeout(() => {
                     this.$el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                 }, 500);
             }
         }
     }" 
     :class="{ 'bg-yellow-50 z-10 transition-all duration-1000 ring-2 ring-inset ring-yellow-400' : isHighlighted }"
     class="group relative bg-white overflow-hidden transition-all duration-500 border-b border-gray-100 last:border-b-0 hover:bg-gray-50/50">
    
    <div class="flex flex-col md:flex-row divide-y md:divide-y-0 md:divide-x divide-gray-100">
        
        {{-- Section 1: Identity (25%) --}}
        <div class="p-5 w-full md:w-1/4 bg-gray-50/10">
            <div class="flex items-center gap-3 mb-4">
                @if($showCheckbox)
                    <input type="checkbox" value="{{ $order->id }}" wire:model.live="selectedItems"
                           class="w-5 h-5 text-{{ $stationColor }}-600 rounded-md border-2 border-gray-300 focus:ring-2 focus:ring-{{ $stationColor }}-500 focus:ring-offset-2 cursor-pointer transition-all hover:border-{{ $stationColor }}-400">
                @endif
                <div class="font-mono font-black text-base text-gray-800 bg-white px-3 py-1.5 rounded-lg border-2 border-gray-200 shadow-sm">
                    {{ $order->spk_number }}
                </div>
                @if($loopIteration)
                    <div class="w-8 h-8 rounded-lg bg-gray-100 text-gray-500 flex items-center justify-center font-bold text-xs border border-gray-200">
                        {{ $loopIteration }}
                    </div>
                @endif
            </div>

            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-{{ $stationColor }}-400 to-{{ $stationColor }}-600 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                        {{ substr($order->customer_name, 0, 1) }}
                    </div>
                    <div>
                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Customer</div>
                        <div class="font-bold text-gray-900 truncate max-w-[140px] text-xs">{{ $order->customer_name }}</div>
                    </div>
                </div>

                <div class="p-2.5 bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div class="text-[9px] text-gray-400 font-black uppercase mb-1">Item Info</div>
                    <div class="text-xs font-bold text-gray-800 truncate">{{ $order->shoe_brand }}</div>
                    <div class="text-[10px] text-gray-500 truncate">{{ $order->shoe_type }} - {{ $order->shoe_color }}</div>
                </div>

                {{-- SLA Info --}}
                <div class="p-2.5 bg-gradient-to-br from-gray-50 to-teal-50 rounded-xl border-2 border-teal-100 shadow-sm">
                    <div class="text-[9px] text-teal-600 font-black uppercase mb-1 tracking-widest flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        SLA & Timeline
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="text-[10px] font-bold text-gray-700">Total HK:</div>
                        <div class="text-xs font-black text-teal-700">{{ $order->hk_days ?? 0 }} Hari</div>
                    </div>
                    @if($order->estimation_date)
                    <div class="flex items-center justify-between mt-1">
                        <div class="text-[10px] font-bold text-gray-700">Est. Selesai:</div>
                        <div class="text-xs font-black text-orange-600">{{ $order->estimation_date->format('d/m/Y') }}</div>
                    </div>
                    @endif
                </div>

                @if(in_array($order->priority, ['Prioritas', 'Urgent', 'Express']))
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black bg-red-500 text-white shadow-md animate-pulse">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"/></svg>
                        {{ strtoupper($order->priority) }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Section 2: Details & Services (55%) --}}
        <div class="p-5 w-full md:w-[55%] space-y-4">
            {{-- Keterangan Besar (From SPK) --}}
            @if($order->technician_notes || $order->notes)
                <div class="p-3 bg-teal-50 border-l-4 border-teal-500 rounded-r shadow-sm">
                    <span class="block font-black text-teal-600 uppercase text-[9px] tracking-widest mb-1">📝 KETERANGAN BESAR :</span>
                    <div class="text-xs text-teal-900 font-bold leading-relaxed space-y-2">
                        @if($order->technician_notes)
                            <div>{!! nl2br(e($order->technician_notes)) !!}</div>
                        @endif
                        @if($order->notes && $order->notes !== $order->technician_notes)
                            <div class="{{ $order->technician_notes ? 'pt-2 border-t border-teal-200/50' : '' }}">
                                {!! nl2br(e($order->notes)) !!}
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @php
                // Broad search for any resolution data in cxIssues
                $issues = $order->cxIssues;
                $resType = $issues->whereNotNull('resolution_type')->last()?->resolution_type;
                $resNotes = $issues->whereNotNull('resolution_notes')->last()?->resolution_notes 
                           ?? $issues->whereNotNull('description')->last()?->description;
            @endphp

            @if($resType || $resNotes)
                <div class="space-y-3">
                    {{-- Resolution Type --}}
                    @if($resType)
                    <div class="p-3 bg-indigo-50 border-l-4 border-indigo-500 rounded-r shadow-sm flex flex-col justify-center">
                        <span class="block font-black text-indigo-600 uppercase text-[9px] tracking-widest mb-1">🏷️ RESOLUTION TYPE :</span>
                        <p class="text-[11px] font-black text-indigo-900 uppercase">{{ $resType }}</p>
                    </div>
                    @endif

                    {{-- Resolution Notes --}}
                    @if($resNotes)
                    <div class="p-3 bg-purple-50 border-l-4 border-purple-500 rounded-r shadow-sm">
                        <span class="block font-black text-purple-600 uppercase text-[9px] tracking-widest mb-1">📝 RESOLUTION NOTES :</span>
                        <p class="text-xs text-purple-900 font-bold leading-relaxed italic">"{{ $resNotes }}"</p>
                    </div>
                    @endif
                </div>
            @endif

            {{-- Service Tags --}}
            <div>
                <div class="text-[10px] text-gray-400 font-black uppercase mb-2 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Layanan Aktif
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($order->workOrderServices as $detail)
                        @php
                            $cat = $detail->category_name ?? ($detail->service ? $detail->service->category : 'Unknown');
                            $svcName = $detail->custom_service_name ?? ($detail->service ? $detail->service->name : 'Layanan');
                            $tagColor = 'gray';
                            if (stripos($cat, 'Cleaning') !== false || stripos($svcName, 'Cleaning') !== false || stripos($cat, 'Treatment') !== false) $tagColor = 'teal';
                            elseif (stripos($cat, 'Sol') !== false || stripos($svcName, 'Sol') !== false) $tagColor = 'orange';
                            elseif (stripos($cat, 'Upper') !== false || stripos($cat, 'Repaint') !== false || stripos($cat, 'Jahit') !== false) $tagColor = 'purple';
                        @endphp
                        <div class="flex flex-col">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase border-2 bg-{{ $tagColor }}-50 text-{{ $tagColor }}-700 border-{{ $tagColor }}-200 shadow-sm flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-{{ $tagColor }}-500 animate-pulse"></span>
                                {{ $svcName }}
                            </span>
                            @php
                                $itemNotes = [];
                                if (is_array($detail->service_details)) {
                                    foreach ($detail->service_details as $k => $v) {
                                        if ($v && $v !== '-' && !in_array(strtolower($v), ['tidak ada', 'tidak', 'no', 'none'])) {
                                            $itemNotes[] = "$k: $v";
                                        }
                                    }
                                }
                            @endphp
                            @if(!empty($itemNotes) || $detail->notes)
                                <div class="mt-1 ml-1 text-[9px] text-gray-500 italic font-medium space-y-0.5">
                                    @if(!empty($itemNotes))
                                        <div class="leading-snug">
                                            <span class="text-{{ $tagColor }}-600 font-black">Detail:</span> {{ implode(', ', $itemNotes) }}
                                        </div>
                                    @endif
                                    @if($detail->notes)
                                        <div class="leading-snug">
                                            <span class="text-{{ $tagColor }}-600 font-black">Note:</span> {{ $detail->notes }}
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Card Actions --}}
            <div class="flex items-center gap-2 pt-2">
                <button @click="showPhotos = !showPhotos" 
                        class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest border-2 transition-all flex items-center gap-1.5 shadow-sm"
                        :class="showPhotos ? 'bg-{{ $stationColor }}-100 border-{{ $stationColor }}-300 text-{{ $stationColor }}-700' : 'bg-gray-100 border-gray-200 text-gray-500 hover:bg-gray-200'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Photos
                </button>
                <button @click="$dispatch('open-report-modal', {{ $order->id }})" 
                        class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest bg-amber-50 border-2 border-amber-200 text-amber-700 hover:bg-amber-100 transition-all flex items-center gap-1.5 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Lapor
                </button>
                <button @click="$dispatch('open-revision-modal', { id: {{ $order->id }}, number: '{{ $order->spk_number }}' })" 
                        class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest bg-red-50 border-2 border-red-200 text-red-700 hover:bg-red-100 transition-all flex items-center gap-1.5 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Revisi
                </button>
            </div>
        </div>

        {{-- Section 3: Status & Actions (20%) --}}
        <div class="p-5 w-full md:w-1/5 bg-gray-50/10 flex flex-col justify-center gap-3">
            @if($isPrepReview)
                <button type="button" onclick="confirmApprovePrep({{ $order->id }})" 
                        class="w-full px-4 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg hover:shadow-green-200 transition-all active:scale-95 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Approve
                </button>
                
                {{-- Steps Summary --}}
                <div class="mt-2 space-y-1">
                    @foreach(['washing', 'sol', 'upper'] as $step)
                        @php 
                            $isNeeded = true;
                            if($step === 'sol') $isNeeded = $order->needs_prep_sol;
                            if($step === 'upper') $isNeeded = $order->needs_prep_upper;
                            $comp = $order->{"prep_{$step}_completed_at"};
                        @endphp
                        @if($isNeeded)
                            <div class="flex items-center justify-between text-[9px] font-bold uppercase tracking-tighter">
                                <span class="text-gray-400">{{ $step }}</span>
                                <span class="{{ $comp ? 'text-green-600' : 'text-red-400' }}">{{ $comp ? 'Done' : 'Pending' }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                @php
                    $techId = $order->{$byColumn};
                    $techName = $order->{$techByRelation}->name ?? '...';
                    $startedAt = $order->{$startedAtColumn};
                @endphp

                @if(!$techId)
                    <div class="space-y-3">
                        <select id="tech-{{ $type }}-{{ $order->id }}" class="w-full text-[10px] font-black border-2 border-gray-200 rounded-lg focus:ring-{{ $stationColor }}-500 focus:border-{{ $stationColor }}-500 shadow-sm uppercase">
                            <option value="">-- TEKNISI --</option>
                            @foreach($technicians as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" onclick="window.updateStation({{ $order->id }}, '{{ $type }}', 'start')" 
                                class="w-full px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg hover:shadow-blue-200 transition-all active:scale-95 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Mulai
                        </button>
                    </div>
                @else
                    <div class="space-y-4">
                        {{-- Premium Technician Card --}}
                        <div class="bg-white border border-{{ $stationColor }}-200 rounded-2xl p-4 shadow-sm relative overflow-hidden group/tech">
                            <div class="absolute top-0 right-0 w-16 h-16 bg-{{ $stationColor }}-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover/tech:scale-110"></div>
                            
                            <div class="flex items-center gap-3 mb-3 relative z-10">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-{{ $stationColor }}-400 to-{{ $stationColor }}-600 text-white flex items-center justify-center text-xs font-black shadow-md border-2 border-white">
                                    {{ substr($techName, 0, 1) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-[9px] text-gray-400 font-black uppercase tracking-widest leading-none mb-1">Technician</div>
                                    <div class="truncate text-xs font-black text-gray-800">{{ $techName }}</div>
                                </div>
                            </div>
                            
                            {{-- Modern Timer Area --}}
                            <div class="pt-3 border-t border-gray-100 flex items-center justify-between relative z-10">
                                <div class="flex items-center gap-1.5">
                                    <div class="w-2 h-2 rounded-full bg-{{ $stationColor }}-500 animate-ping"></div>
                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">In Progress</span>
                                </div>
                                <div class="text-xs font-black font-mono text-{{ $stationColor }}-600 bg-{{ $stationColor }}-50 px-2 py-1 rounded-md border border-{{ $stationColor }}-100" data-started-at="{{ $startedAt?->toIso8601String() }}">
                                    00:00
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" @click="showFinishModal = true" 
                                class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] shadow-xl shadow-emerald-100 hover:shadow-emerald-200 transition-all active:scale-[0.98] flex items-center justify-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            Selesai
                        </button>
                    </div>

                    {{-- Finish Modal --}}
                    <div x-show="showFinishModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4" x-transition style="display: none;">
                        <div class="bg-white rounded-3xl shadow-2xl p-6 w-full max-w-sm" @click.away="showFinishModal = false">
                            <h3 class="font-black text-gray-900 mb-2 uppercase tracking-widest text-sm">Konfirmasi Selesai</h3>
                            <p class="text-xs text-gray-500 mb-4 font-bold">Masukkan waktu penyelesaian pengerjaan:</p>
                            <input type="datetime-local" x-model="finishDate" class="w-full text-xs font-bold border-2 border-gray-100 rounded-xl mb-6 focus:ring-teal-500 focus:border-teal-500 p-3">
                            <div class="flex gap-3">
                                <button @click="showFinishModal = false" class="flex-1 py-3 bg-gray-100 text-gray-500 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-gray-200 transition-all">Batal</button>
                                <button @click="window.updateStation({{ $order->id }}, '{{ $type }}', 'finish', null, finishDate)" class="flex-1 py-3 bg-teal-600 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-teal-100 hover:bg-teal-700 transition-all">Simpan</button>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- Photo Section --}}
    <div x-show="showPhotos" class="px-5 pb-5 bg-gray-50/50 border-t border-gray-100" style="display: none;" x-transition>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-5">
            <div>
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-3 ml-1">📸 Foto Sebelum (Before)</span>
                <x-photo-uploader :order="$order" :step="strtoupper($type . '_BEFORE')" />
            </div>
            <div>
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-3 ml-1">📸 Foto Sesudah (After)</span>
                <x-photo-uploader :order="$order" :step="strtoupper($type . '_AFTER')" />
            </div>
        </div>
    </div>
</div>
