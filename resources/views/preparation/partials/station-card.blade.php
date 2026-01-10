@props([
    'order', 
    'type', 
    'technicians',
    'titleAction' => 'Assign',
    'techByRelation', // e.g., 'prepWashingBy'
    'startedAtColumn', // e.g., 'prep_washing_started_at'
    'byColumn' // e.g., 'prep_washing_by'
])

<div class="p-4 hover:bg-gray-50 transition-colors flex items-center justify-between group">
    <div class="flex gap-4 items-center">
        <div class="font-mono font-bold text-gray-600 bg-gray-100 px-2 py-1 rounded text-sm">{{ $order->spk_number }}</div>
        <div>
            <div class="font-bold text-gray-800">{{ $order->shoe_brand }}</div>
            <div class="text-xs text-gray-500">{{ $order->shoe_color }}</div>
        </div>
    </div>
    <div class="flex items-center gap-4">
        {{-- Service Badges (Optional / Contextual) --}}
        <div class="text-right hidden sm:block">
            @foreach($order->services as $s)
                @if(($type == 'washing' && (stripos($s->category, 'Cleaning') !== false || stripos($s->name, 'Cleaning') !== false || stripos($s->category, 'Treatment') !== false)) ||
                    ($type == 'sol' && (stripos($s->category, 'Sol') !== false || stripos($s->name, 'Sol') !== false)) ||
                    ($type == 'upper' && (stripos($s->category, 'Upper') !== false || stripos($s->category, 'Repaint') !== false)))
                    
                    <span class="text-[10px] uppercase px-1.5 py-0.5 rounded border font-bold
                        {{ $type == 'washing' ? 'bg-teal-100 text-teal-700 border-teal-200' : '' }}
                        {{ $type == 'sol' ? 'bg-orange-100 text-orange-700 border-orange-200' : '' }}
                        {{ $type == 'upper' ? 'bg-purple-100 text-purple-700 border-purple-200' : '' }}
                    ">
                        {{ $s->name }}
                    </span>
                @endif
            @endforeach
        </div>

        @php
            $techId = $order->{$byColumn};
            $techName = $order->{$techByRelation}->name ?? '...';
            $startedAt = $order->{$startedAtColumn};
            
            // Dynamic color classes based on type
            $colorClass = match($type) {
                'washing' => 'teal',
                'sol' => 'orange',
                'upper' => 'purple',
                default => 'gray'
            };
        @endphp

        @if(!$techId)
            <div class="flex items-center gap-2">
                <select id="tech-{{ $type }}-{{ $order->id }}" class="text-xs border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 w-32">
                    <option value="">-- Pilih Teknisi --</option>
                    @foreach($technicians as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>
                <button type="button" onclick="updateStation({{ $order->id }}, '{{ $type }}', 'start')" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs font-bold uppercase tracking-wide transition-all shadow hover:shadow-md">
                    {{ $titleAction }}
                </button>
            </div>
        @else
            <div class="flex flex-col items-end gap-1">
                <div class="text-right">
                    <span class="text-[10px] text-gray-400 block">Dikerjakan oleh:</span>
                    <span class="font-bold text-xs text-{{ $colorClass }}-600 bg-{{ $colorClass }}-50 px-2 py-0.5 rounded border border-{{ $colorClass }}-100">
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
