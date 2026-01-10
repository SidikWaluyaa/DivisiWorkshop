@props([
    'order', 
    'type', // e.g. 'washing', 'prod_sol'
    'technicians',
    'titleAction' => 'Ambil',
    'techByRelation', // e.g., 'prepWashingBy'
    'startedAtColumn', // e.g., 'prep_washing_started_at'
    'byColumn', // e.g., 'prep_washing_by'
    'color' => 'blue' // Default color theme
])

<div class="p-4 hover:bg-gray-50 transition-colors flex items-center justify-between group border-b border-gray-100 last:border-0">
    <div class="flex gap-4 items-center">
        <div class="font-mono font-bold text-gray-600 bg-gray-100 px-2 py-1 rounded text-sm min-w-[80px] text-center">{{ $order->spk_number }}</div>
        <div>
            <div class="font-bold text-gray-800">{{ $order->shoe_brand }}</div>
            <div class="text-xs text-gray-500">{{ $order->shoe_color }}</div>
        </div>
    </div>
    <div class="flex items-center gap-4">
        {{-- Service Badges --}}
        <div class="text-right hidden sm:flex flex-wrap gap-1 justify-end max-w-xs">
            @foreach($order->services as $s)
                <span class="text-[10px] uppercase px-1.5 py-0.5 rounded border font-bold bg-gray-50 text-gray-500 border-gray-200">
                    {{ $s->name }}
                </span>
            @endforeach
        </div>

        @php
            $techId = $order->{$byColumn};
            // Access relation safely
            $techName = $order->{$techByRelation} ? $order->{$techByRelation}->name : '...';
            $startedAt = $order->{$startedAtColumn};
        @endphp

        @if(!$techId)
            <div class="flex items-center gap-2">
                <select id="tech-{{ $type }}-{{ $order->id }}" class="text-xs border-gray-300 rounded focus:ring-{{ $color }}-500 focus:border-{{ $color }}-500 w-32">
                    <option value="">-- Pilih Teknisi --</option>
                    @foreach($technicians as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>
                <button type="button" onclick="updateStation({{ $order->id }}, '{{ $type }}', 'start')" class="px-3 py-1.5 bg-{{ $color }}-600 hover:bg-{{ $color }}-700 text-white rounded text-xs font-bold uppercase tracking-wide transition-all shadow hover:shadow-md">
                    {{ $titleAction }}
                </button>
            </div>
        @elseif($techId && !$startedAt)
             {{-- Assigned but (Re)Start needed (e.g. Revision or Pre-Assigned) --}}
            <div class="flex items-center gap-2 justify-end">
                <div class="text-right mr-2">
                    <span class="text-[10px] text-red-500 font-bold block uppercase tracking-wider">Revisi / Pending</span>
                    <span class="font-bold text-xs text-{{ $color }}-600">{{ $techName }}</span>
                </div>
                
                {{-- Hidden select for JS compatibility --}}
                <select id="tech-{{ $type }}-{{ $order->id }}" class="hidden">
                    <option value="{{ $techId }}" selected>{{ $techName }}</option>
                </select>

                <button type="button" onclick="updateStation({{ $order->id }}, '{{ $type }}', 'start')" class="px-3 py-1.5 bg-{{ $color }}-600 hover:bg-{{ $color }}-700 text-white rounded text-xs font-bold uppercase tracking-wide transition-all shadow hover:shadow-md flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Mulai (Ulang)</span>
                </button>
            </div>
        @else
            <div class="flex flex-col items-end gap-1">
                <div class="text-right">
                    <span class="text-[10px] text-gray-400 block">Dikerjakan oleh:</span>
                    <span class="font-bold text-xs text-{{ $color }}-600 bg-{{ $color }}-50 px-2 py-0.5 rounded border border-{{ $color }}-100">
                        {{ $techName }}
                    </span>
                    @if($startedAt)
                        <span class="text-[10px] text-gray-500 block mt-0.5">Mulai: {{ $startedAt->format('H:i') }}</span>
                    @endif
                </div>
                <button type="button" onclick="updateStation({{ $order->id }}, '{{ $type }}', 'finish')" class="flex items-center gap-2 px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white rounded text-xs font-bold uppercase tracking-wide transition-all shadow hover:shadow-md">
                    <span>✔ Selesai</span>
                </button>
            </div>
        @endif
    </div>
</div>
