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

<div x-data="{ showPhotos: false }" class="border-b border-gray-100 last:border-0">
    <div class="p-4 hover:bg-gray-50 transition-colors flex items-center justify-between group">
        <div class="flex gap-4 items-center">
            <div class="font-mono font-bold text-gray-600 bg-gray-100 px-2 py-1 rounded text-sm min-w-[80px] text-center">{{ $order->spk_number }}</div>
            <div>
                <div class="font-bold text-gray-800">{{ $order->shoe_brand }}</div>
                <div class="text-xs text-gray-500">{{ $order->shoe_color }}</div>
                
                {{-- Rejection Evidence --}}
                @php
                    $rejectPhoto = $order->photos->where('step', 'QC_REJECT_EVIDENCE')->last();
                @endphp
                @if($order->is_revising && $rejectPhoto)
                    <div x-data="{ openPreview: false }" class="mt-2 inline-block">
                        <span @click="openPreview = true" class="text-[10px] font-bold text-red-600 bg-red-100 px-1.5 py-0.5 rounded border border-red-200 cursor-pointer flex items-center gap-1 hover:bg-red-200 transition-colors">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            Lihat Bukti Revisi
                        </span>
                        
                        {{-- Lightbox Modal --}}
                        <div x-show="openPreview" 
                             style="display: none;"
                             class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4"
                             x-transition.opacity>
                            
                            <div class="relative max-w-4xl max-h-[90vh] bg-white rounded-lg shadow-2xl p-2" @click.away="openPreview = false">
                                <button @click="openPreview = false" class="absolute -top-4 -right-4 bg-red-500 text-white rounded-full p-2 shadow-lg hover:bg-red-600 transition-colors z-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                                
                                <div class="bg-red-50 border-b border-red-100 p-2 mb-2 rounded-t flex justify-between items-center">
                                    <span class="font-bold text-red-700 text-sm uppercase">Bukti Revisi QC</span>
                                    <span class="text-xs text-red-500">Klik luar untuk tutup</span>
                                </div>
                                
                                <img src="{{ Storage::url($rejectPhoto->file_path) }}" class="max-h-[80vh] w-auto mx-auto rounded border border-gray-200" alt="Bukti Revisi" />
                            </div>
                        </div>
                    </div>
                @elseif($order->is_revising)
                    <div class="mt-1">
                        <span class="text-[10px] font-bold text-red-600 bg-red-50 px-1.5 py-0.5 rounded border border-red-100">⚠ Revisi QC (Tanpa Foto)</span>
                    </div>
                @endif
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

            {{-- Photo Toggle Button --}}
            <button @click="showPhotos = !showPhotos" :class="showPhotos ? 'text-teal-600 bg-teal-50' : 'text-gray-400 hover:text-teal-600'" class="p-2 rounded-lg transition-colors" title="Dokumentasi Foto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </button>

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
                    <button type="button" onclick="updateStation({{ $order->id }}, '{{ $type }}', 'start')" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs font-bold uppercase tracking-wide transition-all shadow hover:shadow-md">
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

                    <button type="button" onclick="updateStation({{ $order->id }}, '{{ $type }}', 'start')" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs font-bold uppercase tracking-wide transition-all shadow hover:shadow-md flex items-center gap-1">
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
    
    <!-- Photo Section -->
    <div x-show="showPhotos" class="px-4 pb-4 bg-gray-50/50" style="display: none;" x-transition>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white p-3 rounded-lg border border-gray-100 shadow-sm">
                <span class="text-xs font-bold text-gray-500 uppercase block mb-2">📸 Kondisi Awal (Before)</span>
                <x-photo-uploader :order="$order" :step="'PROD_' . strtoupper($type) . '_BEFORE'" />
            </div>
            <div class="bg-white p-3 rounded-lg border border-gray-100 shadow-sm">
                <span class="text-xs font-bold text-gray-500 uppercase block mb-2">✨ Hasil Akhir (After)</span>
                <x-photo-uploader :order="$order" :step="'PROD_' . strtoupper($type) . '_AFTER'" />
            </div>
        </div>
    </div>
</div>
