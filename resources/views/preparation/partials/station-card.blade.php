@props([
    'order', 
    'type', 
    'technicians',
    'titleAction' => 'Assign',
    'techByRelation', // e.g., 'prepWashingBy'
    'startedAtColumn', // e.g., 'prep_washing_started_at'
    'byColumn' // e.g., 'prep_washing_by'
])

<div x-data="{ showPhotos: false, showFinishModal: false, finishDate: '{{ now()->format('Y-m-d\TH:i') }}' }" class="border-b border-gray-100 last:border-0">
    <div class="p-4 hover:bg-gray-50 transition-colors flex items-center justify-between group">
        <div class="flex gap-4 items-center">
            <div class="font-mono font-bold text-gray-600 bg-gray-100 px-2 py-1 rounded text-sm min-w-[80px] text-center">{{ $order->spk_number }}</div>
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

            {{-- Photo Toggle Button --}}
            <button @click="showPhotos = !showPhotos" :class="showPhotos ? 'text-teal-600 bg-teal-50' : 'text-gray-400 hover:text-teal-600'" class="p-2 rounded-lg transition-colors" title="Dokumentasi Foto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </button>

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
                    <button type="button" @click="showFinishModal = true" class="flex items-center gap-2 px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white rounded text-xs font-bold uppercase tracking-wide transition-all shadow hover:shadow-md">
                        <span>✔ Selesai</span>
                    </button>
                    <!-- Finish Modal -->
                    <div x-show="showFinishModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display: none;" x-transition>
                        <div class="bg-white rounded-lg shadow-xl p-4 w-80" @click.away="showFinishModal = false">
                            <h3 class="font-bold text-gray-800 mb-2">Konfirmasi Selesai</h3>
                            <p class="text-xs text-gray-600 mb-3">Masukkan tanggal & jam selesai aktual:</p>
                            <input type="datetime-local" x-model="finishDate" class="w-full text-sm border-gray-300 rounded mb-4 focus:ring-green-500 focus:border-green-500">
                            <div class="flex justify-end gap-2">
                                <button @click="showFinishModal = false" class="px-3 py-1.5 bg-gray-200 text-gray-700 rounded text-xs font-bold">Batal</button>
                                <button @click="updateStation({{ $order->id }}, '{{ $type }}', 'finish', finishDate)" class="px-3 py-1.5 bg-green-600 text-white rounded text-xs font-bold">Simpan & Selesai</button>
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
                <x-photo-uploader :order="$order" :step="strtoupper('PREP_' . $type . '_BEFORE')" />
            </div>
            <div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-2">After (Akhir)</span>
                <x-photo-uploader :order="$order" :step="strtoupper('PREP_' . $type . '_AFTER')" />
            </div>
        </div>
    </div>
</div>
