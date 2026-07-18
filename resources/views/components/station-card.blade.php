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
    'loopIteration' => null,
    'isReviewTab' => false
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

    // SLA Calculations for Fast Track
    $isSortirSlaViolated = $order->isSortirSlaViolated();
    $isProdSlaViolated = $order->isProductionSlaViolated();
@endphp

<tbody {{ $attributes }} x-data="{ 
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
          @click="expanded = !expanded"
          :class="{ 'bg-yellow-50/80 dark:bg-yellow-950/20 ring-2 ring-yellow-400' : isHighlighted }"
          class="hover:bg-teal-50/20 dark:hover:bg-gray-700/50 cursor-pointer transition-colors {{ ($isSortirSlaViolated || $isProdSlaViolated) ? 'bg-red-50/80 dark:bg-red-950/30 border-l-8 border-l-red-600 animate-pulse' : ($order->fast_track_status === 'yes' ? 'bg-orange-50/80 dark:bg-orange-950/40 border-l-8 border-l-orange-500 hover:bg-orange-100/40' : '') }}">
          
          {{-- Column 1: Checkbox & No --}}
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-505 dark:text-gray-400" @click.stop>
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
             <div class="flex items-center flex-wrap gap-2">
                 <span class="font-mono font-bold text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-750 px-2.5 py-1 rounded-md text-xs border border-gray-200 dark:border-gray-600">
                     {{ $order->spk_number }}
                 </span>
                 @if($isSortirSlaViolated)
                     <span class="px-2 py-0.5 rounded text-[8px] font-black bg-red-600 text-white tracking-widest shadow-sm animate-pulse">
                         ⚠️ SLA SORTIR OVERDUE (TERLAMBAT {{ $order->getDaysInSortir() - 3 }} HARI)
                     </span>
                 @endif
                 @if($isProdSlaViolated)
                     <span class="px-2 py-0.5 rounded text-[8px] font-black bg-red-600 text-white tracking-widest shadow-sm animate-pulse">
                         ⚠️ SLA PROD OVERDUE (TERLAMBAT {{ $order->getDaysInProduction() - 4 }} HARI)
                     </span>
                 @endif
                 @if($order->fast_track_status === 'yes')
                     <span class="px-2 py-0.5 rounded text-[8px] font-black bg-orange-600 text-white tracking-widest shadow-sm animate-pulse">
                         FAST TRACK
                     </span>
                 @endif
                 @if($order->has_active_oto)
                     <span class="px-2 py-0.5 rounded text-[8px] font-black bg-orange-500 text-white animate-pulse tracking-widest shadow-sm">
                         OTO
                     </span>
                 @endif
             </div>
         </td>

         {{-- Column 3: Customer & Shoe Info --}}
         <td class="px-6 py-4">
             <div class="text-xs font-bold text-gray-900 dark:text-white">{{ $order->customer_name }}</div>
             <div class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5">{{ $order->shoe_brand }} - {{ $order->shoe_type }}</div>
         </td>

         {{-- Column 4: Prioritas --}}
         <td class="px-6 py-4 whitespace-nowrap">
             @php
                 $priority = $order->priority ?? 'Regular';
                 $isUrgent = in_array($priority, ['Prioritas', 'Urgent', 'Express', 'OTO', 'Prioritas/Urgent']);
             @endphp
             <span class="inline-flex items-center text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider
                          {{ $isUrgent 
                             ? 'bg-red-50 text-red-700 dark:bg-red-950/40 dark:text-red-400 border border-red-200 dark:border-red-900/40 animate-pulse' 
                             : 'bg-gray-105 text-gray-700 dark:bg-gray-800 dark:text-gray-400 border border-gray-200 dark:border-gray-700' }}">
                 @if($isUrgent)
                     🔥 {{ $priority }}
                 @else
                     ⚡ {{ $priority }}
                 @endif
             </span>
         </td>

         @if($isReviewTab)
              {{-- Column 5: Status Badges for Review --}}
              <td class="px-6 py-4" @click.stop>
                  <div class="flex flex-wrap gap-2 justify-center">
                      @php
                          if (str_starts_with($type, 'prep')) {
                              $stations = [
                                  'washing' => ['label' => 'Washing', 'by' => 'prepWashingBy', 'col' => 'prep_washing_completed_at'],
                                  'sol' => ['label' => 'Sol', 'by' => 'prepSolBy', 'col' => 'prep_sol_completed_at'],
                                  'upper' => ['label' => 'Upper', 'by' => 'prepUpperBy', 'col' => 'prep_upper_completed_at'],
                              ];
                          } elseif (str_starts_with($type, 'prod')) {
                              $stations = [
                                  'sol' => ['label' => 'Sol', 'by' => 'prodSolBy', 'col' => 'prod_sol_completed_at'],
                                  'upper' => ['label' => 'Upper', 'by' => 'prodUpperBy', 'col' => 'prod_upper_completed_at'],
                                  'cleaning' => ['label' => 'Cleaning', 'by' => 'prodCleaningBy', 'col' => 'prod_cleaning_completed_at'],
                              ];
                          } else {
                              $stations = [
                                  'jahit' => ['label' => 'Jahit', 'by' => 'qcJahitBy', 'col' => 'qc_jahit_completed_at'],
                                  'cleanup' => ['label' => 'Cleanup', 'by' => 'qcCleanupBy', 'col' => 'qc_cleanup_completed_at'],
                                  'final' => ['label' => 'Final', 'by' => 'qcFinalBy', 'col' => 'qc_final_completed_at'],
                              ];
                          }
                      @endphp
                      @foreach($stations as $sKey => $sVal)
                          @php
                              $completed = $order->{$sVal['col']};
                              $techName = $order->{$sVal['by']}->name ?? '-';
                          @endphp
                          <div class="px-3 py-1.5 rounded-lg border {{ $completed ? 'bg-green-50/50 border-green-200 dark:bg-green-950/20 dark:border-green-900/30' : 'bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700' }} flex flex-col items-center min-w-[80px]">
                              <span class="text-[9px] font-black uppercase {{ $completed ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500' }}">{{ $sVal['label'] }}</span>
                              <span class="text-[10px] font-bold text-gray-700 dark:text-gray-300 truncate w-20 text-center">{{ $techName }}</span>
                          </div>
                      @endforeach
                  </div>
              </td>

              {{-- Column 6: Action Buttons for Review & Toggle --}}
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium" @click.stop>
                  <div class="flex items-center justify-end gap-2">
                      <button wire:click="performApprove({{ $order->id }})" 
                              wire:confirm="{{ str_starts_with($type, 'prep') ? 'Preparation sudah OK semua? Lanjut ke Sortir?' : (str_starts_with($type, 'prod') ? 'Sudah dicek dan OK? Lanjut ke QC?' : 'QC sudah OK semua? Order akan Finish.') }}"
                              class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-bold text-xs flex items-center gap-1 shadow transition-all active:scale-95">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                          Approve
                      </button>

                      <button @click="$dispatch('open-revision-modal', { id: {{ $order->id }}, number: '{{ $order->spk_number }}' })" 
                              class="bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-955/20 dark:text-red-400 px-3 py-2 rounded-lg font-bold text-xs transition-all active:scale-95">
                          Revisi
                      </button>

                      <button @click="expanded = !expanded" class="p-1.5 rounded-lg hover:bg-teal-50 dark:hover:bg-gray-750 text-teal-600 dark:text-teal-400 transition-colors">
                          <svg :class="{'rotate-180': expanded}" class="w-4 h-4 transform transition-transform duration-250" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                          </svg>
                      </button>
                  </div>
              </td>
          @else
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
                      <span class="text-xs text-gray-400 dark:text-gray-505 italic">Belum ditugaskan</span>
                  @endif
              </td>

              {{-- Column 6: Duration / SLA --}}
              <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                  @if($startedAt)
                      <div class="flex flex-col">
                          <span class="font-bold text-[9px] text-gray-400 dark:text-gray-505 uppercase tracking-wider">Durasi</span>
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
                  <button @click.stop="expanded = !expanded" class="p-1.5 rounded-lg hover:bg-teal-50 dark:hover:bg-gray-750 text-teal-600 dark:text-teal-400 transition-colors">
                      <svg :class="{'rotate-180': expanded}" class="w-5 h-5 transform transition-transform duration-250" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                      </svg>
                  </button>
              </td>
          @endif
     </tr>

     {{-- Collapsible detail row --}}
      <tr x-show="expanded" x-cloak x-transition>
           <td colspan="{{ ($isReviewTab ?? false) ? 6 : 7 }}" class="bg-gray-50 dark:bg-gray-850 p-5 border-t border-b border-gray-200 dark:border-gray-700">
               <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                   
                   {{-- Info & Notes Col --}}
                   <div class="md:col-span-2 space-y-4">
                       {{-- Services (Layanan) --}}
                       <div class="p-3.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
                           <span class="block font-bold text-gray-400 dark:text-gray-505 uppercase text-[9px] tracking-wider mb-2">Layanan / Treatment:</span>
                           <div class="flex flex-wrap gap-1.5">
                               @foreach($order->workOrderServices as $detail)
                                   @php
                                       $cat = $detail->category_name ?? ($detail->service ? $detail->service->category : 'Unknown');
                                       $svcName = $detail->custom_service_name ?? ($detail->service ? $detail->service->name : 'Layanan');
                                       $tagColor = 'gray';
                                       if (stripos($cat, 'Cleaning') !== false || stripos($svcName, 'Cleaning') !== false || stripos($cat, 'Treatment') !== false) $tagColor = 'teal';
                                       elseif (stripos($cat, 'Sol') !== false || stripos($svcName, 'Sol') !== false) $tagColor = 'orange';
                                       elseif (stripos($cat, 'Upper') !== false || stripos($cat, 'Upper') !== false || stripos($cat, 'Jahit') !== false) $tagColor = 'purple';
                                   @endphp
                                   <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider
                                                bg-{{ $tagColor }}-50 text-{{ $tagColor }}-700 border border-{{ $tagColor }}-200 dark:bg-{{ $tagColor }}-955/20 dark:text-{{ $tagColor }}-400 dark:border-{{ $tagColor }}-900/30">
                                       {{ $svcName }}
                                   </span>
                               @endforeach
                           </div>
                       </div>

                       {{-- HK (Hari Kerja) & Estimasi Selesai (Invoice) --}}
                       <div class="grid grid-cols-2 gap-4">
                           <div class="p-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
                               <span class="block text-[9px] font-bold text-gray-400 dark:text-gray-505 uppercase tracking-wider mb-1">Hari Kerja (HK)</span>
                               <span class="text-xs font-black text-gray-800 dark:text-white">
                                   {{ $order->hk ?? '-' }} Hari Kerja
                               </span>
                           </div>
                           <div class="p-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
                               <span class="block text-[9px] font-bold text-gray-400 dark:text-gray-505 uppercase tracking-wider mb-1">Estimasi Selesai (Invoice)</span>
                               <span class="text-xs font-black text-orange-600 dark:text-orange-400">
                                    @if($order->invoice && $order->invoice->estimasi_selesai)
                                        {{ \Carbon\Carbon::parse($order->invoice->estimasi_selesai)->format('d M Y') }}
                                    @else
                                        {{ $order->estimation_date ? $order->estimation_date->format('d M Y') : '-' }}
                                    @endif
                               </span>
                           </div>
                       </div>

                       {{-- Details (Keterangan & Instruksi) --}}
                       <div class="p-3.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
                           <div class="flex items-center gap-2 border-b border-gray-100 dark:border-gray-700 pb-2 mb-3">
                               <svg class="w-4 h-4 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                               </svg>
                               <span class="font-bold text-gray-400 dark:text-gray-505 uppercase text-[9px] tracking-wider">Detail Informasi &amp; Instruksi</span>
                           </div>
                           <div class="text-xs font-bold text-gray-700 dark:text-gray-300 whitespace-pre-line leading-relaxed">
                               {{ $order->description ?? 'Tidak ada instruksi khusus.' }}
                           </div>
                       </div>

                       {{-- Action Buttons --}}
                       <div class="flex flex-wrap items-center gap-2 pt-2">
                           <button type="button" @click="showPhotos = !showPhotos" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-teal-50 text-teal-700 border-2 border-teal-205 hover:bg-teal-100 dark:bg-teal-955/20 dark:text-teal-400 dark:border-teal-900/30 transition-all shadow-sm">
                               <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                               </svg>
                               Photos
                           </button>

                           <button type="button" @click="$dispatch('open-report-modal', { id: {{ $order->id }}, number: '{{ $order->spk_number }}' })" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border-2 border-amber-200 hover:bg-amber-100 dark:bg-amber-955/20 dark:text-amber-400 dark:border-amber-900/30 transition-all shadow-sm">
                               <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                               </svg>
                               Lapor
                           </button>

                           <button type="button" @click="$dispatch('open-revision-modal', { id: {{ $order->id }}, number: '{{ $order->spk_number }}' })" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-red-50 text-red-700 border-2 border-red-200 hover:bg-red-100 dark:bg-red-955/20 dark:text-red-400 dark:border-red-900/30 transition-all shadow-sm">
                               <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                               </svg>
                               Revisi
                           </button>

                           <a href="{{ route('admin.orders.show', $order->id) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-bold bg-slate-800 text-white hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-650 transition-all shadow-sm border border-slate-700">
                               <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                               </svg>
                               History
                           </a>
                       </div>
                       
                       {{-- Photos panel --}}
                       <div x-show="showPhotos" class="pt-4 border-t border-gray-200 dark:border-gray-700" style="display: none;" x-transition>
                           <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                               <div>
                                   <span class="text-[10px] font-bold text-gray-400 dark:text-gray-505 uppercase tracking-wider block mb-1.5">📸 Foto Sebelum (Before)</span>
                                   <x-photo-uploader :order="$order" :step="strtoupper($type . '_BEFORE')" />
                               </div>
                               <div>
                                   <span class="text-[10px] font-bold text-gray-400 dark:text-gray-505 uppercase tracking-wider block mb-1.5">📸 Foto Sesudah (After)</span>
                                   <x-photo-uploader :order="$order" :step="strtoupper($type . '_AFTER')" />
                               </div>
                           </div>
                       </div>
                   </div>

                   {{-- Controls / Cover SPK Col --}}
                   <div class="md:col-span-1">
                       @if(!($isReviewTab ?? false))
                           <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 flex flex-col justify-between shadow-sm h-fit">
                               <h4 class="text-xs font-bold text-gray-400 dark:text-gray-505 uppercase tracking-wider mb-3">Kontrol Pengerjaan</h4>
                               
                               @if($isPrepReview)
                                   <button type="button" @click="confirmApprovePrep({{ $order->id }}); expanded = false;" 
                                           class="w-full px-4 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-green-500/10 hover:shadow-green-500/20 transition-all flex items-center justify-center gap-2">
                                       <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                       Approve
                                   </button>
                               @else
                                   @if(!$techId)
                                       <div class="space-y-3">
                                           <div>
                                               <label class="block text-[10px] font-bold text-gray-455 dark:text-gray-505 uppercase mb-1">Pilih Teknisi</label>
                                               <select id="tech-{{ $type }}-{{ $order->id }}" class="w-full text-xs border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-teal-500 focus:border-teal-500 font-medium dark:bg-gray-700 dark:text-white">
                                                   <option value="">-- TEKNISI --</option>
                                                   @foreach($technicians as $t)
                                                       <option value="{{ $t->id }}">{{ $t->name }}</option>
                                                   @endforeach
                                               </select>
                                           </div>
                                           <button type="button" @click="window.updateStation({{ $order->id }}, '{{ $type }}', 'start'); expanded = false;" 
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
                                                   <div class="text-[9px] text-gray-400 dark:text-gray-550 mt-0.5">Mulai: {{ $startedAt->format('H:i') }} WIB</div>
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
                                                   <p class="text-xs text-gray-555 mb-3 font-bold">Masukkan waktu selesai aktual:</p>
                                                   <input type="datetime-local" x-model="finishDate" class="w-full text-xs border-gray-300 dark:border-gray-650 rounded-lg mb-4 focus:ring-green-500 focus:border-green-500 dark:bg-gray-750 dark:text-white">
                                                   <div class="flex justify-end gap-2">
                                                       <button @click="showFinishModal = false" class="px-3 py-1.5 bg-gray-100 text-gray-700 dark:text-gray-300 rounded-lg text-xs font-bold">Batal</button>
                                                       <button @click="window.updateStation({{ $order->id }}, '{{ $type }}', 'finish', null, finishDate); showFinishModal = false; expanded = false;" class="px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-xs font-bold">Simpan</button>
                                                   </div>
                                               </div>
                                           </div>
                                       </div>
                                   @endif
                               @endif
                               
                               {{-- Cover SPK --}}
                               @php
                                   $coverPhoto = $order->photos->firstWhere('is_spk_cover', true);
                               @endphp
                               @if($coverPhoto)
                                   <div class="mt-4 pt-4 border-t border-gray-150 dark:border-gray-750">
                                       <span class="block text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">📸 Cover SPK</span>
                                       <div class="relative rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 group">
                                           <template x-if="expanded">
                                               <img src="{{ $coverPhoto->photo_url }}" 
                                                    class="w-full h-32 object-cover hover:scale-105 transition-transform duration-300 cursor-pointer"
                                                    @click="window.open('{{ $coverPhoto->photo_url }}', '_blank')">
                                           </template>
                                       </div>
                                   </div>
                               @endif
                           </div>
                       @else
                           {{-- Cover SPK only for Review Tab --}}
                           @php
                               $coverPhoto = $order->photos->firstWhere('is_spk_cover', true);
                           @endphp
                           @if($coverPhoto)
                               <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm h-fit">
                                   <span class="block text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">📸 Cover SPK</span>
                                   <div class="relative rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 group">
                                       <template x-if="expanded">
                                           <img src="{{ $coverPhoto->photo_url }}" 
                                                class="w-full h-32 object-cover hover:scale-105 transition-transform duration-300 cursor-pointer"
                                                @click="window.open('{{ $coverPhoto->photo_url }}', '_blank')">
                                       </template>
                                   </div>
                               </div>
                           @endif
                       @endif
                   </div>
               </div>
           </td>
      </tr>
</tbody>
