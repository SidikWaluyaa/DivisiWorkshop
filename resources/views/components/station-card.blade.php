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

<div id="spk-{{ $order->spk_number }}" 
     x-data="{ 
         showPhotos: false, 
         showFinishModal: false, 
         finishDate: '{{ now()->format('Y-m-d\TH:i') }}',
         isHighlighted: false,
         init() {
             const urlParams = new URLSearchParams(window.location.search);
             const hl = urlParams.get('highlight');
             if (hl === '{{ $order->spk_number }}') {
                 this.isHighlighted = true;
                 setTimeout(() => {
                     this.$el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                 }, 500);
                 setTimeout(() => {
                     this.isHighlighted = false;
                 }, 5000);
             }
         }
     }" 
     :class="{ 'ring-4 ring-yellow-400 bg-yellow-50/50 scale-[1.01] shadow-2xl z-10 transition-all duration-1000' : isHighlighted, 'border border-gray-200 hover:border-{{ $color }}-300 hover:shadow-xl' : !isHighlighted }"
     class="group relative bg-white rounded-2xl mb-4 overflow-hidden transition-all duration-500">
    
    {{-- Background Accent --}}
    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-{{ $color }}-500/5 to-transparent rounded-bl-full pointer-events-none"></div>

    <div class="flex flex-col md:flex-row divide-y md:divide-y-0 md:divide-x divide-gray-100">
        
        {{-- Section 1: Identity (25%) --}}
        <div class="p-5 w-full md:w-1/4 bg-gray-50/30">
            <div class="flex items-center gap-3 mb-4">
                @if($showCheckbox)
                    <input type="checkbox" value="{{ $order->id }}" wire:model.live="selectedItems"
                           class="w-5 h-5 text-{{ $color }}-600 rounded-md border-2 border-gray-300 focus:ring-2 focus:ring-{{ $color }}-500 focus:ring-offset-2 cursor-pointer transition-all hover:border-{{ $color }}-400">
                @endif
                <div class="font-mono font-black text-lg text-gray-900 bg-white px-3 py-1 rounded-lg border border-gray-200 shadow-sm">
                    {{ $order->spk_number }}
                </div>
            </div>

            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    @php
                        $avatarBg = match($color) {
                            'orange' => 'from-orange-400 to-orange-600',
                            'purple' => 'from-purple-400 to-purple-600',
                            'teal' => 'from-teal-400 to-teal-600',
                            default => 'from-blue-400 to-blue-600'
                        };
                    @endphp
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br {{ $avatarBg }} text-white flex items-center justify-center font-bold text-sm shadow-sm">
                        {{ substr($order->customer_name, 0, 1) }}
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 font-medium">Customer</div>
                        <div class="font-bold text-gray-900 truncate max-w-[150px]">{{ $order->customer_name }}</div>
                    </div>
                </div>

                <div class="p-3 bg-white rounded-xl border border-gray-100 shadow-sm">
                    <div class="text-[10px] text-gray-400 font-bold uppercase mb-1">Item Info</div>
                    <div class="text-xs font-bold text-gray-800">{{ $order->shoe_brand }}</div>
                    <div class="text-[10px] text-gray-500">{{ $order->shoe_type }}</div>
                </div>

                @if(in_array($order->priority, ['Prioritas', 'Urgent', 'Express']))
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black bg-red-100 text-red-600 border border-red-200 shadow-sm animate-pulse">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"/></svg>
                        {{ strtoupper($order->priority) }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Section 2: Technical Details (50%) --}}
        <div class="p-5 w-full md:w-2/4 space-y-4">
            {{-- Services --}}
            <div>
                <div class="text-[10px] text-gray-400 font-bold uppercase mb-2 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Layanan Aktif
                </div>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($order->workOrderServices as $detail)
                        @php
                            $category = $detail->category_name ?? ($detail->service ? $detail->service->category : 'Other');
                            $name = $detail->custom_service_name ?? ($detail->service ? $detail->service->name : 'Item');
                            $tagColor = match(true) {
                                stripos($category, 'Sol') !== false => 'orange',
                                stripos($category, 'Upper') !== false || stripos($category, 'Repaint') !== false => 'purple',
                                stripos($category, 'Cleaning') !== false || stripos($category, 'Treatment') !== false => 'teal',
                                default => 'gray'
                            };
                        @endphp
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border bg-{{ $tagColor }}-50 text-{{ $tagColor }}-700 border-{{ $tagColor }}-200">
                            {{ $name }}
                        </span>
                    @endforeach
                </div>
            </div>

            {{-- Instructions, Complaints & CX Results --}}
            <div class="grid grid-cols-1 gap-3">
                @if($order->technician_notes)
                    @php
                        // Filter out automated CX notes from technician_notes
                        $spkNotes = preg_replace('/\[CX - (Lanjut|Tambah Jasa)\].*$/s', '', $order->technician_notes);
                        $spkNotes = trim($spkNotes);
                    @endphp
                    @if($spkNotes)
                        <div class="p-3 bg-amber-50 rounded-xl border border-amber-100 relative group/note">
                            <div class="absolute -top-2 left-3 px-2 bg-amber-500 text-white text-[8px] font-black rounded-full uppercase tracking-tighter shadow-sm">Instruksi Khusus (SPK)</div>
                            <p class="text-sm text-amber-900 leading-relaxed font-black mt-1">
                                {{ $spkNotes }}
                            </p>
                        </div>
                    @endif
                @endif

                @php
                    $latestCx = $order->cxIssues->where('status', 'RESOLVED')->last();
                @endphp
                @if($latestCx)
                    <div class="p-3 bg-purple-50 rounded-xl border border-purple-100 relative group/cx">
                        <div class="absolute -top-2 left-3 px-2 bg-purple-600 text-white text-[8px] font-black rounded-full uppercase tracking-tighter shadow-sm">Keputusan Akhir Customer (CX)</div>
                        <div class="space-y-1 mt-1">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] text-purple-600 font-black uppercase tracking-tighter bg-purple-100 px-2 py-0.5 rounded-md">
                                    {{ str_replace('_', ' ', strtoupper($latestCx->resolution_type)) }}
                                </span>
                            </div>
                            <p class="text-[13px] text-purple-900 leading-tight font-black italic mt-1.5">
                                "{{ $latestCx->resolution_notes ?? $latestCx->resolution ?? 'Lanjut sesuai instruksi' }}"
                            </p>
                            <div class="flex items-center gap-2 mt-2 pt-1 border-t border-purple-100 text-[9px] text-purple-400 font-bold uppercase">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Verified by {{ $latestCx->resolver->name ?? 'System' }} • {{ $latestCx->resolved_at ? $latestCx->resolved_at->format('d/M H:i') : $latestCx->updated_at->format('d/M H:i') }}
                            </div>
                        </div>
                    </div>
                @endif

                @if($order->notes)
                    @php
                        // Filter out CX markers from original notes
                        $csNotes = preg_replace('/(\*10 HK Garansi)?\s?\[CX Issue Reported\].*$/s', '', $order->notes);
                        $csNotes = trim($csNotes);
                    @endphp
                    @if($csNotes)
                        <div class="p-3 bg-blue-50 rounded-xl border border-blue-100 relative group/complaint">
                            <div class="absolute -top-2 left-3 px-2 bg-blue-500 text-white text-[8px] font-black rounded-full uppercase tracking-tighter shadow-sm">Keluhan Awal (CS)</div>
                            <p class="text-xs text-blue-900 leading-relaxed italic mt-1">"{{ $csNotes }}"</p>
                        </div>
                    @endif
                @endif
            </div>

            @php
                $photoBase = match($color) {
                    'orange' => 'text-orange-600 border-orange-100 hover:border-orange-300',
                    'purple' => 'text-purple-600 border-purple-100 hover:border-purple-300',
                    'teal' => 'text-teal-600 border-teal-100 hover:border-teal-300',
                    default => 'text-blue-600 border-blue-100 hover:border-blue-300'
                };
                $photoActive = match($color) {
                    'orange' => 'bg-orange-600 text-white border-orange-600',
                    'purple' => 'bg-purple-600 text-white border-purple-600',
                    'teal' => 'bg-teal-600 text-white border-teal-600',
                    default => 'bg-blue-600 text-white border-blue-600'
                };
            @endphp
            {{-- Action Row --}}
            <div class="flex items-center gap-2 pt-2 border-t border-gray-50">
                <button @click="showPhotos = !showPhotos" 
                        class="px-4 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all border-2"
                        :class="showPhotos ? '{{ $photoActive }}' : 'bg-white {{ $photoBase }}'">
                    📸 Photos
                </button>
                <button @click="$dispatch('open-report-modal', {{ $order->id }})" class="px-4 py-1.5 rounded-lg text-[10px] font-black bg-white text-amber-600 border-2 border-amber-100 hover:border-amber-300 uppercase tracking-wider transition-all">
                    ⚠️ Report
                </button>
                <button @click="$dispatch('open-revision-modal', { id: {{ $order->id }}, number: '{{ $order->spk_number }}' })" class="px-4 py-1.5 rounded-lg text-[10px] font-black bg-white text-red-600 border-2 border-red-100 hover:border-red-300 uppercase tracking-wider transition-all">
                    🔄 Revisi
                </button>
            </div>
        </div>

        {{-- Section 3: Status & Assign (25%) --}}
        <div class="p-5 w-full md:w-1/4 bg-gray-50/50 flex flex-col justify-between">
            @php
                $techId = $order->{$byColumn};
                $techName = $order->{$techByRelation}->name ?? 'Belum Ada';
                $startedAt = $order->{$startedAtColumn};
            @endphp

            @php
                $colorClasses = match($color) {
                    'orange' => 'from-orange-600 to-orange-700 shadow-orange-500/20',
                    'purple' => 'from-purple-600 to-purple-700 shadow-purple-500/20',
                    'teal' => 'from-teal-600 to-teal-700 shadow-teal-500/20',
                    default => 'from-blue-600 to-blue-700 shadow-blue-500/20'
                };
            @endphp

            @if(!$techId)
                <div class="space-y-3">
                    <div class="text-[10px] text-gray-400 font-bold uppercase text-center mb-1">Penugasan Teknisi</div>
                    <select id="tech-{{ $type }}-{{ $order->id }}" class="block w-full text-xs font-bold border-gray-200 rounded-xl focus:ring-{{ $color }}-500 focus:border-{{ $color }}-500 shadow-sm">
                        <option value="">-- Pilih Teknisi --</option>
                        @foreach($technicians as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                    <button type="button" onclick="window.updateStation({{ $order->id }}, '{{ $type }}', 'start')" 
                            class="w-full py-3 bg-gradient-to-r {{ $colorClasses }} text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg hover:-translate-y-0.5 transition-all active:scale-95">
                        {{ $titleAction }} (Mulai)
                    </button>
                </div>
            @else
                <div class="space-y-4">
                    @php
                        $avatarClasses = match($color) {
                            'orange' => 'bg-orange-100 text-orange-700',
                            'purple' => 'bg-purple-100 text-purple-700',
                            'teal' => 'bg-teal-100 text-teal-700',
                            default => 'bg-blue-100 text-blue-700'
                        };
                        $timeClasses = match($color) {
                            'orange' => 'text-orange-600',
                            'purple' => 'text-purple-600',
                            'teal' => 'text-teal-600',
                            default => 'text-blue-600'
                        };
                    @endphp
                    <div class="bg-white p-3 rounded-2xl border border-gray-100 shadow-sm text-center">
                        <div class="text-[9px] text-gray-400 font-black uppercase mb-2">Petugas Saat Ini</div>
                        <div class="w-12 h-12 rounded-full {{ $avatarClasses }} flex items-center justify-center font-black text-lg mx-auto mb-2 border-2 border-white shadow-sm">
                            {{ substr($techName, 0, 1) }}
                        </div>
                        <div class="font-black text-sm text-gray-800">{{ $techName }}</div>
                        @if($startedAt)
                            <div class="text-[9px] {{ $timeClasses }} font-bold mt-1">
                                <span class="opacity-60">STARTED AT</span> {{ $startedAt->format('H:i') }}
                            </div>
                        @endif
                    </div>

                    <button type="button" @click="showFinishModal = true" 
                            class="w-full py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg hover:shadow-emerald-500/20 hover:-translate-y-0.5 transition-all active:scale-95">
                        ✅ SELESAI
                    </button>
                </div>
            @endif
        </div>
    </div>
    
    {{-- Photo Section (Expandable) --}}
    <div x-show="showPhotos" x-collapse style="display: none;">
        <div class="p-5 bg-gray-50 border-t border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                    <div class="text-[10px] font-black text-gray-400 uppercase mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-orange-400"></span> Kondisi Sebelum (Before)
                    </div>
                    <x-photo-uploader :order="$order" :step="'PROD_' . strtoupper($type) . '_BEFORE'" />
                </div>
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                    <div class="text-[10px] font-black text-gray-400 uppercase mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Hasil Akhir (After)
                    </div>
                    <x-photo-uploader :order="$order" :step="'PROD_' . strtoupper($type) . '_AFTER'" />
                </div>
            </div>
        </div>
    </div>

    {{-- Local Modals --}}
    <div x-show="showFinishModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm" style="display: none;" x-transition>
        <div class="bg-white rounded-3xl shadow-2xl p-6 w-full max-w-sm border border-gray-100" @click.away="showFinishModal = false">
            <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h3 class="font-black text-gray-800 text-xl text-center mb-1">Selesaikan Proses</h3>
            <p class="text-sm text-gray-500 text-center mb-6 px-4">Pastikan semua foto hasil akhir sudah diunggah dengan benar.</p>
            
            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 ml-1">Waktu Selesai Aktual</label>
                    <input type="datetime-local" x-model="finishDate" class="w-full text-sm border-gray-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 font-bold p-3">
                </div>
            </div>

            <div class="flex gap-3">
                <button @click="showFinishModal = false" class="flex-1 py-3 bg-gray-100 text-gray-500 rounded-xl text-xs font-black uppercase tracking-wider">Batal</button>
                <button @click="window.updateStation({{ $order->id }}, '{{ $type }}', 'finish', null, finishDate)" class="flex-1 py-3 bg-emerald-600 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-lg shadow-emerald-600/20">Simpan</button>
            </div>
        </div>
    </div>
</div>
