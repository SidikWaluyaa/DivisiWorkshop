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

<tbody x-data="{ 
         expanded: false,
         showPhotos: false, 
         showFinishModal: false, 
         finishDate: '{{ now()->format('Y-m-d\TH:i') }}',
         isHighlighted: false,
         init() {
             const urlParams = new URLSearchParams(window.location.search);
             if (urlParams.get('highlight') === '{{ $order->spk_number }}') {
                 this.isHighlighted = true;
                 this.expanded = true;
                 setTimeout(() => {
                     this.$el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                 }, 500);
             }
         }
     }" 
     class="divide-y divide-gray-100 dark:divide-gray-800">
     
     <tr id="spk-{{ $order->spk_number }}" 
         :class="{ 'bg-yellow-50/80 dark:bg-yellow-950/20 ring-2 ring-yellow-400' : isHighlighted }"
         class="hover:bg-teal-50/20 dark:hover:bg-gray-700/50 transition-colors">
         
         {{-- Column 1: Checkbox & No --}}
         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
             <div class="flex items-center gap-3">
                 @if($showCheckbox)
                     <input type="checkbox" value="{{ $order->id }}" wire:model.live="selectedItems"
                            class="w-4 h-4 text-{{ $stationColor }}-600 rounded border-gray-300 focus:ring-{{ $stationColor }}-500 dark:bg-gray-750 dark:border-gray-600">
                 @endif
                 @if($loopIteration)
                     <span class="font-bold text-gray-400">{{ $loopIteration }}</span>
                 @endif
             </div>
         </td>

         {{-- Column 2: SPK Number --}}
         <td class="px-6 py-4 whitespace-nowrap">
             <div class="flex items-center gap-2">
                 <span class="font-mono font-bold text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-750 px-2.5 py-1 rounded-md text-xs border border-gray-200 dark:border-gray-600">
                     {{ $order->spk_number }}
                 </span>
                 @if($order->has_active_oto)
                     <span class="px-2 py-0.5 rounded text-[8px] font-black bg-orange-500 text-white animate-pulse tracking-widest shadow-sm">
                         OTO
                     </span>
                 @endif
                 @if(in_array($order->priority, ['Prioritas', 'Urgent', 'Express']))
                     <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-red-100 text-red-700 dark:bg-red-950/50 dark:text-red-400 animate-pulse border border-red-200 dark:border-red-900/30">
                         URGENT
                     </span>
                 @endif
             </div>
         </td>

         {{-- Column 3: Customer & Shoe Info --}}
         <td class="px-6 py-4">
             <div class="text-xs font-bold text-gray-900 dark:text-white">{{ $order->customer_name }}</div>
             <div class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5">{{ $order->shoe_brand }} - {{ $order->shoe_type }}</div>
         </td>

         {{-- Column 4: Services --}}
         <td class="px-6 py-4">
             <div class="flex flex-wrap gap-1">
                 @foreach($order->workOrderServices as $detail)
                     @php
                         $cat = $detail->category_name ?? ($detail->service ? $detail->service->category : 'Unknown');
                         $svcName = $detail->custom_service_name ?? ($detail->service ? $detail->service->name : 'Layanan');
                         $tagColor = 'gray';
                         if (stripos($cat, 'Cleaning') !== false || stripos($svcName, 'Cleaning') !== false || stripos($cat, 'Treatment') !== false) $tagColor = 'teal';
                         elseif (stripos($cat, 'Sol') !== false || stripos($svcName, 'Sol') !== false) $tagColor = 'orange';
                         elseif (stripos($cat, 'Upper') !== false || stripos($cat, 'Repaint') !== false || stripos($cat, 'Jahit') !== false) $tagColor = 'purple';
                     @endphp
                     <span class="inline-flex items-center text-[10px] font-semibold px-2 py-0.5 rounded bg-{{ $tagColor }}-50 text-{{ $tagColor }}-700 dark:bg-{{ $tagColor }}-950/20 dark:text-{{ $tagColor }}-400 border border-{{ $tagColor }}-200/50 dark:border-{{ $tagColor }}-900/30">
                         {{ Str::limit($svcName, 20) }}
                     </span>
                 @endforeach
             </div>
         </td>

         {{-- Column 5: Technician --}}
         <td class="px-6 py-4 whitespace-nowrap">
             @php
                 $techId = $isPrepReview ? null : $order->{$byColumn};
                 $techName = $isPrepReview ? null : ($order->{$techByRelation}->name ?? null);
                 $startedAt = $isPrepReview ? null : $order->{$startedAtColumn};
             @endphp
             @if($techName)
                 <div class="flex items-center gap-1.5">
                     <div class="w-5 h-5 rounded-full bg-{{ $stationColor }}-100 dark:bg-{{ $stationColor }}-955/40 flex items-center justify-center text-[10px] text-{{ $stationColor }}-700 dark:text-{{ $stationColor }}-400 font-bold">
                         {{ substr($techName, 0, 1) }}
                     </div>
                     <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ $techName }}</span>
                 </div>
             @else
                 <span class="text-xs text-gray-400 dark:text-gray-500 italic">Belum ditugaskan</span>
             @endif
         </td>

         {{-- Column 6: Duration / SLA --}}
         <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
             @if($startedAt)
                 <div class="flex flex-col">
                     <span class="font-bold text-[9px] text-gray-400 dark:text-gray-500 uppercase tracking-wider">Durasi</span>
                     <span class="font-mono font-black text-{{ $stationColor }}-600 dark:text-{{ $stationColor }}-400" data-started-at="{{ $startedAt->toIso8601String() }}">
                         Calculating...
                     </span>
                 </div>
             @else
                 <div class="text-[10px]">
                     @if($order->estimation_date)
                         <span class="text-orange-600 font-semibold" title="Estimasi Selesai">{{ $order->estimation_date->format('d M') }}</span>
                     @else
                         <span class="text-gray-400">-</span>
                     @endif
                 </div>
             @endif
         </td>

         {{-- Column 7: Toggle Expand --}}
         <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
             <button @click="expanded = !expanded" class="p-1.5 rounded-lg hover:bg-teal-50 dark:hover:bg-gray-750 text-teal-600 dark:text-teal-400 transition-colors">
                 <svg :class="{'rotate-180': expanded}" class="w-5 h-5 transform transition-transform duration-250" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                 </svg>
             </button>
         </td>
     </tr>

     {{-- Collapsible detail row --}}
     <tr x-show="expanded" x-cloak x-transition>
          <td colspan="7" class="bg-gray-50 dark:bg-gray-850 p-5 border-t border-b border-gray-200 dark:border-gray-700">
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                  
                  {{-- Info & Notes Col --}}
                  <div class="md:col-span-2 space-y-4">
                      <h4 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Detail Informasi & Instruksi</h4>
                      
                      {{-- Technician & CS Notes --}}
                      @if($order->technician_notes || $order->notes)
                          <div class="p-3.5 bg-teal-50 dark:bg-teal-950/20 border-l-4 border-teal-500 rounded-r text-xs text-teal-900 dark:text-teal-300 font-medium">
                              <span class="block font-bold text-teal-600 uppercase text-[10px] tracking-wide mb-1">📝 Keterangan & Instruksi:</span>
                              <div class="space-y-2">
                                  @if($order->technician_notes)
                                      <div>{!! nl2br(e($order->technician_notes)) !!}</div>
                                  @endif
                                  @if($order->notes && $order->notes !== $order->technician_notes)
                                      <div class="{{ $order->technician_notes ? 'pt-2 border-t border-teal-200/50 dark:border-teal-900/30' : '' }}">
                                          {!! nl2br(e($order->notes)) !!}
                                      </div>
                                  @endif
                              </div>
                          </div>
                      @endif

                      {{-- CX Follow Up --}}
                      @php
                          $issues = $order->cxIssues;
                          $resType = $issues->whereNotNull('resolution_type')->last()?->resolution_type;
                          $resNotes = $issues->whereNotNull('resolution_notes')->last()?->resolution_notes 
                                     ?? $issues->whereNotNull('description')->last()?->description;
                      @endphp
                      @if($resType || $resNotes)
                          <div class="p-3.5 bg-indigo-50 dark:bg-indigo-950/20 border-l-4 border-indigo-500 rounded-r text-xs text-indigo-900 dark:text-indigo-300 font-medium">
                              <span class="block font-bold text-indigo-600 uppercase text-[10px] tracking-wide mb-1"> Riwayat Follow Up CX:</span>
                              @if($resType)
                                  <div class="font-bold text-[10px] text-indigo-500 mb-1">TYPE: {{ strtoupper($resType) }}</div>
                              @endif
                              @if($resNotes)
                                  <div class="italic">"{{ $resNotes }}"</div>
                              @endif
                          </div>
                      @endif
                      
                      {{-- Action triggers --}}
                      <div class="pt-2 flex items-center gap-2">
                          <button @click="showPhotos = !showPhotos" 
                                  :class="showPhotos ? 'bg-teal-100 text-teal-700 border-teal-300 dark:bg-teal-955 dark:text-teal-400 dark:border-teal-900' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-700'" 
                                  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold border-2 transition-all shadow-sm">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                              </svg>
                              Photos
                          </button>

                          <button type="button" @click.stop="$dispatch('open-report-modal', {{ $order->id }})" 
                                  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border-2 border-amber-200 hover:bg-amber-100 dark:bg-amber-950/20 dark:text-amber-400 dark:border-amber-900/30 transition-all shadow-sm">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                              </svg>
                              Lapor
                          </button>

                          <button type="button" @click="$dispatch('open-revision-modal', { id: {{ $order->id }}, number: '{{ $order->spk_number }}' })" 
                                  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-red-50 text-red-700 border-2 border-red-200 hover:bg-red-100 dark:bg-red-950/20 dark:text-red-400 dark:border-red-900/30 transition-all shadow-sm">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                              </svg>
                              Revisi
                          </button>
                      </div>
                      
                      {{-- Photos panel --}}
                      <div x-show="showPhotos" class="pt-4 border-t border-gray-200 dark:border-gray-700" style="display: none;" x-transition>
                          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                              <div>
                                  <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider block mb-1.5">📸 Foto Sebelum (Before)</span>
                                  <x-photo-uploader :order="$order" :step="strtoupper($type . '_BEFORE')" />
                              </div>
                              <div>
                                  <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider block mb-1.5">📸 Foto Sesudah (After)</span>
                                  <x-photo-uploader :order="$order" :step="strtoupper($type . '_AFTER')" />
                              </div>
                          </div>
                      </div>
                  </div>

                  {{-- Controls Col --}}
                  <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 flex flex-col justify-between shadow-sm h-fit">
                      <h4 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">Kontrol Pengerjaan</h4>
                      
                      @if($isPrepReview)
                          <button type="button" onclick="confirmApprovePrep({{ $order->id }})" 
                                  class="w-full px-4 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-green-500/10 hover:shadow-green-500/20 transition-all flex items-center justify-center gap-2">
                              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                              Approve
                          </button>
                      @else
                          @if(!$techId)
                              <div class="space-y-3">
                                  <div>
                                      <label class="block text-[10px] font-bold text-gray-450 dark:text-gray-500 uppercase mb-1">Pilih Teknisi</label>
                                      <select id="tech-{{ $type }}-{{ $order->id }}" class="w-full text-xs border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-teal-500 focus:border-teal-500 font-medium dark:bg-gray-700 dark:text-white">
                                          <option value="">-- TEKNISI --</option>
                                          @foreach($technicians as $t)
                                              <option value="{{ $t->id }}">{{ $t->name }}</option>
                                          @endforeach
                                      </select>
                                  </div>
                                  <button type="button" onclick="window.updateStation({{ $order->id }}, '{{ $type }}', 'start')" 
                                          class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg text-xs font-bold uppercase transition-all shadow-md shadow-blue-500/10">
                                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                      Mulai
                                  </button>
                              </div>
                          @else
                              <div class="space-y-3">
                                  <div class="bg-{{ $stationColor }}-50/50 dark:bg-{{ $stationColor }}-955/20 border border-{{ $stationColor }}-200/50 dark:border-{{ $stationColor }}-900/30 rounded-lg p-2.5">
                                      <div class="text-[9px] text-{{ $stationColor }}-600 dark:text-{{ $stationColor }}-400 font-bold uppercase tracking-wider mb-1">Pekerja Aktif</div>
                                      <div class="font-bold text-xs text-gray-800 dark:text-white">{{ $techName }}</div>
                                      @if($startedAt)
                                          <div class="text-[9px] text-gray-400 dark:text-gray-500 mt-0.5">Mulai: {{ $startedAt->format('H:i') }} WIB</div>
                                      @endif
                                  </div>

                                  <button type="button" @click="showFinishModal = true" 
                                          class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white rounded-lg text-xs font-bold uppercase transition-all shadow-md shadow-green-500/10">
                                      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                      </svg>
                                      Selesaikan
                                  </button>

                                  {{-- Finish Modal --}}
                                  <div x-show="showFinishModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4" style="display: none;" x-transition>
                                      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-5 w-80 border border-gray-200 dark:border-gray-700" @click.away="showFinishModal = false">
                                          <h3 class="font-bold text-gray-800 dark:text-white text-sm mb-2 uppercase tracking-widest">Konfirmasi Selesai</h3>
                                          <p class="text-xs text-gray-500 mb-3 font-bold">Masukkan waktu selesai aktual:</p>
                                          <input type="datetime-local" x-model="finishDate" class="w-full text-xs border-gray-300 dark:border-gray-600 rounded-lg mb-4 focus:ring-green-500 focus:border-green-500 dark:bg-gray-750 dark:text-white">
                                          <div class="flex justify-end gap-2">
                                              <button @click="showFinishModal = false" class="px-3 py-1.5 bg-gray-100 text-gray-700 dark:text-gray-300 rounded-lg text-xs font-bold">Batal</button>
                                              <button @click="window.updateStation({{ $order->id }}, '{{ $type }}', 'finish', finishDate)" class="px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-xs font-bold">Simpan</button>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          @endif
                      @endif
                  </div>
              </div>
          </td>
     </tr>
</tbody>
