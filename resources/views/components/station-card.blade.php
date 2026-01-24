@props([
    'order', 
    'type', // e.g. 'washing', 'prod_sol'
    'technicians',
    'titleAction' => 'Ambil',
    'techByRelation', // e.g., 'prepWashingBy'
    'startedAtColumn', // e.g., 'prep_washing_started_at'
    'byColumn', // e.g., 'prep_washing_by'
    'color' => 'blue', // Default color theme
    'showCheckbox' => false,
    'loopIteration' => null
])

<div x-data="{ showPhotos: false, showFinishModal: false, finishDate: '{{ now()->format('Y-m-d\TH:i') }}' }" class="border-b border-gray-100 last:border-0 group">
    <div class="p-5 flex items-start gap-4">
        {{-- Left Section: Checkbox & Number --}}
        <div class="flex flex-col items-center gap-3 pt-1">
            @if($showCheckbox)
                <input type="checkbox" value="{{ $order->id }}" x-model="selectedItems"
                       class="w-5 h-5 text-{{ $color }}-600 rounded-md border-2 border-gray-300 focus:ring-2 focus:ring-{{ $color }}-500 focus:ring-offset-2 cursor-pointer transition-all hover:border-{{ $color }}-400 shadow-sm">
            @endif
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 text-gray-700 flex items-center justify-center font-black text-sm border-2 border-gray-300 shadow-sm group-hover:scale-110 transition-transform">
                {{ $loopIteration ?? $order->id }}
            </div>
        </div>

        {{-- Main Content --}}
        <div class="flex-1 min-w-0">
            {{-- Header Row: SPK + Priority + Customer --}}
            <div class="flex items-start justify-between gap-4 mb-3">
                <div class="flex items-center gap-3 flex-wrap">
                    {{-- SPK Number --}}
                    <div class="font-mono font-black text-base text-gray-800 bg-gradient-to-r from-gray-100 to-gray-50 px-3 py-1.5 rounded-lg border-2 border-gray-200 shadow-sm">
                        {{ $order->spk_number }}
                    </div>
                    
                    {{-- Priority Badge --}}
                    @if($order->priority === 'Express')
                         <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-purple-500 to-purple-600 text-white shadow-md animate-pulse">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                            </svg>
                            EXPRESS
                        </span>
                    @elseif(in_array($order->priority, ['Prioritas', 'Urgent']))
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-red-500 to-red-600 text-white shadow-md animate-pulse">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            URGENT
                        </span>
                    @endif
                </div>
            </div>

            {{-- Customer Info --}}
            <div class="mb-3">
                <div class="flex items-center gap-2 mb-1">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="font-bold text-gray-900">{{ $order->customer_name }}</span>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                    <span>{{ $order->shoe_brand }} {{ $order->shoe_type }} <span class="font-bold text-teal-600">({{ $order->category ?? 'N/A' }})</span> - {{ $order->shoe_color }}</span>
                </div>

                {{-- TECHNICIAN INSTRUCTION / ALERT --}}
                @if($order->technician_notes)
                    <div class="mt-2 p-2 bg-amber-50 border-l-4 border-amber-500 rounded-r text-xs text-amber-900 font-medium">
                        <span class="block font-bold text-amber-600 uppercase text-[10px] tracking-wide mb-0.5">⚠️ Instruksi Khusus Teknisi:</span>
                        {{ $order->technician_notes }}
                    </div>
                @endif
                
                {{-- CS NOTES (Readonly) --}}
                @if($order->notes)
                    <div class="mt-1.5 p-2 bg-blue-50 border-l-4 border-blue-400 rounded-r text-[10px] text-blue-900 font-medium">
                        <span class="block font-bold text-blue-500 uppercase text-[9px] tracking-wide mb-0.5">💬 Request / Keluhan Customer (CS):</span>
                        {{ $order->notes }}
                    </div>
                @endif

                {{-- CX FOLLOW UP HISTORY --}}
                @php
                    $resolvedIssue = $order->cxIssues->where('status', 'RESOLVED')->last();
                @endphp
                @if($resolvedIssue)
                    <div class="mt-2 p-2 bg-purple-50 border-l-4 border-purple-500 rounded-r text-xs text-purple-900 font-medium">
                        <span class="block font-bold text-purple-600 uppercase text-[10px] tracking-wide mb-0.5">⚠️ Riwayat Follow Up CX:</span>
                        <div class="italic">"{{ $resolvedIssue->resolution_notes ?? $resolvedIssue->description ?? '-' }}"</div>
                        <div class="mt-1 text-[9px] text-purple-500 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Done by {{ $resolvedIssue->resolver->name ?? 'System' }} • {{ $resolvedIssue->updated_at->format('d/M H:i') }}
                        </div>
                    </div>
                @endif

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

            {{-- Service Tags - Display All Services --}}
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach($order->workOrderServices as $detail)
                    @php
                        // Determine color based on service category
                        $category = $detail->category_name ?? ($detail->service ? $detail->service->category : 'Unknown');
                        $name = $detail->custom_service_name ?? ($detail->service ? $detail->service->name : 'Layanan');

                        $serviceColor = 'gray';
                        if (stripos($category, 'Cleaning') !== false || stripos($name, 'Cleaning') !== false || stripos($category, 'Treatment') !== false) {
                            $serviceColor = 'teal';
                        } elseif (stripos($category, 'Sol') !== false || stripos($name, 'Sol') !== false) {
                            $serviceColor = 'orange';
                        } elseif (stripos($category, 'Upper') !== false || stripos($category, 'Repaint') !== false) {
                            $serviceColor = 'purple';
                        } elseif (stripos($category, 'Production') !== false) {
                            $serviceColor = 'blue';
                        }
                    @endphp
                    
                    <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-lg border-2 shadow-sm
                        bg-{{ $serviceColor }}-50 text-{{ $serviceColor }}-700 border-{{ $serviceColor }}-200
                    ">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        {{ $name }}
                    </span>
                @endforeach
            </div>

            {{-- Action Buttons Row --}}
            <div class="flex items-center gap-2 flex-wrap">
                {{-- Photo Toggle --}}
                <button @click="showPhotos = !showPhotos" 
                        :class="showPhotos ? 'bg-teal-100 text-teal-700 border-teal-300' : 'bg-gray-100 text-gray-600 border-gray-300 hover:bg-gray-200'" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold border-2 transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Photos
                </button>

                {{-- Report Issue --}}
                <button type="button" @click.stop="$dispatch('open-report-modal', {{ $order->id }})" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border-2 border-amber-200 hover:bg-amber-100 transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Report
                </button>
            </div>
        </div>

        {{-- Right Section: Status & Actions --}}
        <div class="flex flex-col items-end gap-2 min-w-[200px]">
            @php
                $techId = $order->{$byColumn};
                $techName = $order->{$techByRelation} ? $order->{$techByRelation}->name : '...';
                $startedAt = $order->{$startedAtColumn};
            @endphp

            @if(!$techId)
                {{-- Unassigned State --}}
                <div class="flex flex-col gap-2 w-full">
                    <select id="tech-{{ $type }}-{{ $order->id }}" class="text-sm border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full font-medium shadow-sm">
                        <option value="">-- Diserahkan Ke --</option>
                        @foreach($technicians as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                    <button type="button" onclick="updateStation({{ $order->id }}, '{{ $type }}', 'start')" 
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg text-sm font-bold uppercase tracking-wide transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                        </svg>
                        {{ $titleAction }} (Mulai)
                    </button>
                </div>
            @elseif($techId && !$startedAt)
                 {{-- Assigned but (Re)Start needed --}}
                <div class="flex items-center gap-2 justify-end">
                    <div class="text-right mr-2">
                        <span class="text-[10px] text-red-500 font-bold block uppercase tracking-wider">Pending / Revisi</span>
                        <span class="font-bold text-xs text-{{ $color }}-600">{{ $techName }}</span>
                    </div>
                    
                    <select id="tech-{{ $type }}-{{ $order->id }}" class="hidden">
                        <option value="{{ $techId }}" selected>{{ $techName }}</option>
                    </select>

                    <button type="button" onclick="updateStation({{ $order->id }}, '{{ $type }}', 'start')" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs font-bold uppercase tracking-wide transition-all shadow hover:shadow-md flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Mulai (Ulang)</span>
                    </button>
                </div>
            @else
                {{-- Assigned/In Progress State --}}
                <div class="flex flex-col gap-2 w-full">
                    {{-- Technician Info Card --}}
                    <div class="bg-{{ $color }}-50 border-2 border-{{ $color }}-200 rounded-lg p-3 shadow-sm">
                        <div class="text-[10px] text-{{ $color }}-600 font-bold uppercase tracking-wider mb-1">Technician</div>
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 rounded-full bg-{{ $color }}-200 flex items-center justify-center text-{{ $color }}-700 font-bold text-sm">
                                {{ substr($techName, 0, 1) }}
                            </div>
                            <div class="flex-1">
                                <div class="font-bold text-sm text-{{ $color }}-900">{{ $techName }}</div>
                                @if($startedAt)
                                    <div class="text-[10px] text-{{ $color }}-600 font-medium">
                                        Mulai: {{ $startedAt->format('H:i') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    {{-- Finish Button --}}
                    <button type="button" @click="showFinishModal = true" 
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-lg text-sm font-bold uppercase tracking-wide transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Selesai
                    </button>
                    
                    {{-- Finish Modal --}}
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
            <div class="bg-white p-3 rounded-lg border border-gray-100 shadow-sm">
                <span class="text-xs font-bold text-gray-500 uppercase block mb-2">📸 Kondisi Awal (Before)</span>
                <x-photo-uploader :order="$order" :step="'PROD_' . strtoupper(str_replace('item_', '', $type)) . '_BEFORE'" />
            </div>
            <div class="bg-white p-3 rounded-lg border border-gray-100 shadow-sm">
                <span class="text-xs font-bold text-gray-500 uppercase block mb-2">✨ Hasil Akhir (After)</span>
                <x-photo-uploader :order="$order" :step="'PROD_' . strtoupper(str_replace('item_', '', $type)) . '_AFTER'" />
            </div>
        </div>
    </div>
</div>
